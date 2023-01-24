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

class Links extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['is_enabled', 'type', 'project_id'], ['url'], ['datetime', 'clicks', 'url']));
        $filters->set_default_order_by('link_id', settings()->main->default_order_type);
        $filters->set_default_results_per_page(settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id}  {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
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
                {$filters->get_sql_where('links')}
                {$filters->get_sql_order_by('links')}
            {$paginator->get_sql_limit()}
        ");

        /* Iterate over the links */
        $links = [];

        while($row = $links_result->fetch_object()) {
            $row->full_url = $row->domain_id ? $row->scheme . $row->host . '/' . $row->url : SITE_URL . $row->url;

            $links[] = $row;
        }

        /* Export handler */
        process_export_csv($links, 'include', ['link_id', 'user_id', 'project_id', 'pixels_ids', 'type', 'url', 'location_url', 'start_date', 'end_date', 'clicks', 'is_verified', 'is_enabled', 'datetime'], sprintf(l('links.title')));
        process_export_json($links, 'include', ['link_id', 'user_id', 'project_id', 'pixels_ids', 'type', 'url', 'location_url', 'settings', 'start_date', 'end_date', 'clicks', 'is_verified', 'is_enabled', 'datetime'], sprintf(l('links.title')));

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Create Link Modal */
        $domains = (new Domain())->get_domains($this->user);
        $data = [
            'domains' => $domains
        ];
        $view = new \Altum\View('links/create_link_modals', (array) $this);
        \Altum\Event::add_content($view->run($data), 'modals');

        /* Delete Modal */
        $view = new \Altum\View('links/link_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Existing projects */
        $projects = (new \Altum\Models\Project())->get_projects_by_user_id($this->user->user_id);

        /* Prepare the Links Content View */
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
        $view = new \Altum\View('links/index', (array) $this);

        $this->add_view_content('content', $view->run());

    }

}
