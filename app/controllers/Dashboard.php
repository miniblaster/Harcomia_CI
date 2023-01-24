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

class Dashboard extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['is_enabled', 'type'], ['url'], ['datetime', 'clicks', 'url']));
        $filters->set_default_order_by('link_id', settings()->main->default_order_type);
        $filters->set_default_results_per_page(settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('links?' . $filters->get_get() . '&page=%d')));

        /* Get the links list for the project */
        $links_result = database()->query("
            SELECT 
                `links`.*, `domains`.`scheme`, `domains`.`host`
            FROM 
                `links`
            LEFT JOIN 
                `domains` ON `links`.`domain_id` = `domains`.`domain_id`
            WHERE 
                `links`.`user_id` = {$this->user->user_id}
            {$filters->get_sql_order_by()}
            {$paginator->get_sql_limit()}
        ");

        /* Iterate over the links */
        $links = [];

        while($row = $links_result->fetch_object()) {
            $row->full_url = $row->domain_id ? $row->scheme . $row->host . '/' . $row->url : SITE_URL . $row->url;

            $links[] = $row;
        }

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Get statistics */
        if(count($links)) {
            $links_chart = [];
            $start_date_query = (new \DateTime())->modify('-30 day')->format('Y-m-d H:i:s');
            $end_date_query = (new \DateTime())->modify('+1 day')->format('Y-m-d H:i:s');

            $track_links_result = database()->query("
                SELECT
                    COUNT(`id`) AS `pageviews`,
                    SUM(`is_unique`) AS `visitors`,
                    DATE_FORMAT(`datetime`, '%Y-%m-%d') AS `formatted_date`
                FROM
                    `track_links`
                WHERE   
                    `user_id` = {$this->user->user_id} 
                    AND (`datetime` BETWEEN '{$start_date_query}' AND '{$end_date_query}')
                GROUP BY
                    `formatted_date`
                ORDER BY
                    `formatted_date`
            ");

            /* Generate the raw chart data and save logs for later usage */
            while($row = $track_links_result->fetch_object()) {
                $label = \Altum\Date::get($row->formatted_date, 4);

                $links_chart[$label] = [
                    'pageviews' => $row->pageviews,
                    'visitors' => $row->visitors
                ];
            }

            $links_chart = get_chart_data($links_chart);
        }

        /* Some statistics for the widgets */
        if(settings()->links->domains_is_enabled) {
            $domains_total = database()->query("SELECT COUNT(*) AS `total` FROM `domains` WHERE `user_id` = {$this->user->user_id}")->fetch_object()->total;
        }

        if(settings()->links->shortener_is_enabled) {
            $links_total = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'link'")->fetch_object()->total;
            $links_clicks_total = database()->query("SELECT SUM(`clicks`) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'link'")->fetch_object()->total;
        }

        if(settings()->links->files_is_enabled) {
            $file_links_total = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'file'")->fetch_object()->total;
        }

        if(settings()->links->vcards_is_enabled) {
            $vcard_links_total = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'vcard'")->fetch_object()->total;
        }

        if(settings()->links->biolinks_is_enabled) {
            $biolinks_total = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'biolink'")->fetch_object()->total;
        }

        if(settings()->links->qr_codes_is_enabled) {
            $qr_codes_total = database()->query("SELECT COUNT(*) AS `total` FROM `qr_codes` WHERE `user_id` = {$this->user->user_id}")->fetch_object()->total;
        }

        /* Delete Modal */
        $view = new \Altum\View('links/link_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Create Link Modal */
        $domains = (new Domain())->get_domains($this->user);
        $data = [
            'domains' => $domains
        ];

        $view = new \Altum\View('links/create_link_modals', (array) $this);
        \Altum\Event::add_content($view->run($data), 'modals');

        /* Existing projects */
        $projects = (new \Altum\Models\Project())->get_projects_by_user_id($this->user->user_id);

        /* Prepare the Links View */
        $data = [
            'links'             => $links,
            'pagination'        => $pagination,
            'filters'           => $filters,
            'projects'          => $projects,
            'links_types'       => require APP_PATH . 'includes/links_types.php',
        ];
        $view = new \Altum\View('links/links_content', (array) $this);
        $this->add_view_content('links_content', $view->run($data));

        /* Prepare the View */
        $data = [
            'links_chart'       => $links_chart ?? false,

            /* Widgets stats */
            'domains_total'             => $domains_total ?? null,
            'qr_codes_total'            => $qr_codes_total ?? null,
            'vcard_links_total'        => $vcard_links_total ?? null,
            'links_total'               => $links_total ?? null,
            'file_links_total'          => $file_links_total ?? null,
            'biolinks_total'            => $biolinks_total ?? null,
        ];

        $view = new \Altum\View('dashboard/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
