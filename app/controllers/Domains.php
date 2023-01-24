<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Models\Domain;
use Altum\Response;

class Domains extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(!settings()->links->domains_is_enabled) {
            redirect('dashboard');
        }

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['user_id', 'is_enabled'], ['host'], ['host', 'datetime']));
        $filters->set_default_order_by('domain_id', settings()->main->default_order_type);
        $filters->set_default_results_per_page(settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `domains` WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('domains?' . $filters->get_get() . '&page=%d')));

        /* Get the domains list for the user */
        $domains = [];
        $domains_result = database()->query("SELECT * FROM `domains` WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where()} {$filters->get_sql_order_by()} {$paginator->get_sql_limit()}");
        while($row = $domains_result->fetch_object()) $domains[] = $row;

        /* Export handler */
        process_export_csv($domains, 'include', ['domain_id', 'user_id', 'scheme', 'host', 'custom_index_url', 'custom_not_found_url', 'is_enabled', 'last_datetime', 'datetime'], sprintf(l('domains.title')));
        process_export_json($domains, 'include', ['domain_id', 'user_id', 'scheme', 'host', 'custom_index_url', 'custom_not_found_url', 'is_enabled', 'last_datetime', 'datetime'], sprintf(l('domains.title')));

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Prepare the View */
        $data = [
            'domains' => $domains,
            'total_domains' => $total_rows,
            'pagination' => $pagination,
            'filters' => $filters,
        ];

        $view = new \Altum\View('domains/index', (array) $this);

        $this->add_view_content('content', $view->run($data));
    }

    /* Ajax method */
    public function create() {
        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        if(!settings()->links->domains_is_enabled) {
            die();
        }

        $_POST['scheme'] = isset($_POST['scheme']) && in_array($_POST['scheme'], ['http://', 'https://']) ? query_clean($_POST['scheme']) : 'https://';
        $_POST['host'] = mb_strtolower(input_clean($_POST['host']));
        $_POST['custom_index_url'] = trim(filter_var($_POST['custom_index_url'], FILTER_SANITIZE_URL));
        $_POST['custom_not_found_url'] = trim(filter_var($_POST['custom_not_found_url'], FILTER_SANITIZE_URL));

        /* Make sure that the user didn't exceed the limit */
        $user_total_domains = database()->query("SELECT COUNT(*) AS `total` FROM `domains` WHERE `user_id` = {$this->user->user_id} AND `type` = 0")->fetch_object()->total;
        if($this->user->plan_settings->domains_limit != -1 && $user_total_domains >= $this->user->plan_settings->domains_limit) {
            Response::json(l('global.info_message.plan_feature_limit'), 'error');
        }

        if(in_array($_POST['host'], explode(',', settings()->links->blacklisted_domains))) {
            Response::json(l('link.error_message.blacklisted_domain'), 'error');
        }

        if(!empty($_POST['custom_index_url']) && in_array(get_domain_from_url($_POST['custom_index_url']), explode(',', settings()->links->blacklisted_domains))) {
            Response::json(l('link.error_message.blacklisted_domain'), 'error');
        }

        if(!empty($_POST['custom_not_found_url']) && in_array(get_domain_from_url($_POST['custom_not_found_url']), explode(',', settings()->links->blacklisted_domains))) {
            Response::json(l('link.error_message.blacklisted_domain'), 'error');
        }

        if(db()->where('host', $_POST['host'])->where('is_enabled', 1)->has('domains')) {
            Response::json(l('domains.error_message.host_exists'), 'error');
        }

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Response::json('Please create an account on the demo to test out this function.', 'error');

        /* Define some needed variables */
        $type = 0;

        /* Add the row to the database */
        $domain_id = db()->insert('domains', [
            'user_id' => $this->user->user_id,
            'scheme' => $_POST['scheme'],
            'host' => $_POST['host'],
            'custom_index_url' => $_POST['custom_index_url'],
            'custom_not_found_url' => $_POST['custom_not_found_url'],
            'type' => $type,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Send notification to admin if needed */
        if(settings()->email_notifications->new_domain && !empty(settings()->email_notifications->emails)) {

            /* Prepare the email */
            $email_template = get_email_template(
                [],
                l('global.emails.admin_new_domain_notification.subject'),
                [
                    '{{ADMIN_DOMAIN_UPDATE_LINK}}' => url('admin/domain-update/' . $domain_id),
                    '{{DOMAIN_HOST}}' => $_POST['host'],
                    '{{NAME}}' => $this->user->name,
                    '{{EMAIL}}' => $this->user->email,
                ],
                l('global.emails.admin_new_domain_notification.body')
            );

            send_mail(explode(',', settings()->email_notifications->emails), $email_template->subject, $email_template->body);

        }

        Response::json(l('domain_create_modal.success_message'), 'success');
    }

    /* Ajax method */
    public function update() {
        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        if(!settings()->links->domains_is_enabled) {
            die();
        }

        $_POST['domain_id'] = (int) $_POST['domain_id'];
        $_POST['scheme'] = isset($_POST['scheme']) && in_array($_POST['scheme'], ['http://', 'https://']) ? query_clean($_POST['scheme']) : 'https://';
        $_POST['host'] = mb_strtolower(trim($_POST['host']));
        $_POST['custom_index_url'] = trim(filter_var($_POST['custom_index_url'], FILTER_SANITIZE_URL));
        $_POST['custom_not_found_url'] = trim(filter_var($_POST['custom_not_found_url'], FILTER_SANITIZE_URL));

        if(!$domain = db()->where('domain_id', $_POST['domain_id'])->where('user_id', $this->user->user_id)->getOne('domains')) {
            die();
        }

        if(in_array($_POST['host'], explode(',', settings()->links->blacklisted_domains))) {
            Response::json(l('link.error_message.blacklisted_domain'), 'error');
        }

        if(!empty($_POST['custom_index_url']) && in_array(get_domain_from_url($_POST['custom_index_url']), explode(',', settings()->links->blacklisted_domains))) {
            Response::json(l('link.error_message.blacklisted_domain'), 'error');
        }

        if(!empty($_POST['custom_not_found_url']) && in_array(get_domain_from_url($_POST['custom_not_found_url']), explode(',', settings()->links->blacklisted_domains))) {
            Response::json(l('link.error_message.blacklisted_domain'), 'error');
        }

        if($domain->host != $_POST['host'] && db()->where('host', $_POST['host'])->where('is_enabled', 1)->has('domains')) {
            Response::json(l('domains.error_message.host_exists'), 'error');
        }

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Response::json('Please create an account on the demo to test out this function.', 'error');

        $is_enabled = $domain->is_enabled;

        /* Add the domain on pending if the host has changed */
        if($_POST['host'] != $domain->host) {
            $is_enabled = 0;
        }

        /* Update the database */
        db()->where('domain_id', $domain->domain_id)->update('domains', [
            'scheme' => $_POST['scheme'],
            'host' => $_POST['host'],
            'custom_index_url' => $_POST['custom_index_url'],
            'custom_not_found_url' => $_POST['custom_not_found_url'],
            'is_enabled' => $is_enabled,
            'last_datetime' => \Altum\Date::$date,
        ]);

        /* Send notification to admin if needed */
        if(!$is_enabled && settings()->email_notifications->new_domain && !empty(settings()->email_notifications->emails)) {

            /* Prepare the email */
            $email_template = get_email_template(
                [],
                l('global.emails.admin_new_domain_notification.subject'),
                [
                    '{{ADMIN_DOMAIN_UPDATE_LINK}}' => url('admin/domain-update/' . $domain->domain_id),
                    '{{DOMAIN_HOST}}' => $_POST['host'],
                    '{{NAME}}' => $this->user->name,
                    '{{EMAIL}}' => $this->user->email,
                ],
                l('global.emails.admin_new_domain_notification.body')
            );

            send_mail(explode(',', settings()->email_notifications->emails), $email_template->subject, $email_template->body);

        }

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('domain_id=' . $domain->domain_id);

        Response::json(l('domain_update_modal.success_message'), 'success');
    }

    /* Ajax method */
    public function delete() {
        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('delete')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        if(!settings()->links->domains_is_enabled) {
            die();
        }

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Response::json('Please create an account on the demo to test out this function.', 'error');

        if(!empty($_POST) && (\Altum\Csrf::check('token') || \Altum\Csrf::check('global_token'))) {

            $_POST['domain_id'] = (int) $_POST['domain_id'];

            /* Check for possible errors */
            if(!$domain = db()->where('domain_id', $_POST['domain_id'])->where('user_id', $this->user->user_id)->getOne('domains', ['domain_id', 'host'])) {
                die();
            }

            (new Domain())->delete($_POST['domain_id']);

            /* Set a nice success message */
            Response::json(sprintf(l('global.success_message.delete1'), '<strong>' . $domain->host . '</strong>'));

        }

    }

}
