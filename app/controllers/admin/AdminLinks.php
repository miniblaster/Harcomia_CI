<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Alerts;

class AdminLinks extends Controller {

    public function index() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['is_enabled', 'user_id', 'project_id', 'domain_id', 'type'], ['url', 'location_url'], ['datetime', 'url', 'location_url', 'clicks']));
        $filters->set_default_order_by('link_id', settings()->main->default_order_type);
        $filters->set_default_results_per_page(settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/links?' . $filters->get_get() . '&page=%d')));

        /* Get the users */
        $links = [];
        $links_result = database()->query("
            SELECT
                `links`.*, `users`.`name` AS `user_name`, `users`.`email` AS `user_email`, `domains`.`scheme`, `domains`.`host`
            FROM
                `links`
            LEFT JOIN
                `users` ON `links`.`user_id` = `users`.`user_id`
            LEFT JOIN
                `domains` ON `links`.`domain_id` = `domains`.`domain_id`
            WHERE
                1 = 1
                {$filters->get_sql_where('links')}
                {$filters->get_sql_order_by('links')}
            {$paginator->get_sql_limit()}
        ");
        while($row = $links_result->fetch_object()) {
            $links[] = $row;
        }

        /* Export handler */
        process_export_csv($links, 'include', ['link_id', 'user_id', 'project_id', 'pixels_ids', 'type', 'url', 'location_url', 'start_date', 'end_date', 'clicks', 'is_verified', 'is_enabled', 'datetime'], sprintf(l('admin_links.title')));
        process_export_json($links, 'include', ['link_id', 'user_id', 'project_id', 'pixels_ids', 'type', 'url', 'location_url', 'settings', 'start_date', 'end_date', 'clicks', 'is_verified', 'is_enabled', 'datetime'], sprintf(l('admin_links.title')));

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'links' => $links,
            'filters' => $filters,
            'pagination' => $pagination,
            'links_types' => require APP_PATH . 'includes/links_types.php',
        ];

        $view = new \Altum\View('admin/links/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bulk() {

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/links');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/links');
        }

        if(!isset($_POST['type']) || (isset($_POST['type']) && !in_array($_POST['type'], ['delete']))) {
            redirect('admin/links');
        }

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            switch($_POST['type']) {
                case 'delete':

                    foreach($_POST['selected'] as $link_id) {
                        (new \Altum\Models\Link())->delete($link_id);
                    }
                    break;
            }

            /* Set a nice success message */
            Alerts::add_success(l('admin_bulk_delete_modal.success_message'));

        }

        redirect('admin/links');
    }

    public function delete() {

        $link_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!$link = db()->where('link_id', $link_id)->getOne('links', ['link_id', 'url'])) {
            redirect('admin/links');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            (new \Altum\Models\Link())->delete($link->link_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $link->url . '</strong>'));

        }

        redirect('admin/links');
    }

    public function is_verified() {

        $link_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!$link = db()->where('link_id', $link_id)->getOne('links', ['link_id', 'type', 'is_verified'])) {
            redirect('admin/links');
        }

        if($link->type != 'biolink') {
            redirect('admin/links');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            db()->where('link_id', $link->link_id)->update('links', [
                'is_verified' => (int) !$link->is_verified,
            ]);

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItem('link?link_id=' . $link->link_id);
            \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $link->link_id);

            /* Set a nice success message */
            Alerts::add_success(l('global.success_message.update2'));

        }

        redirect('admin/links');
    }

}
