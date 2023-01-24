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

class Pixels extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['type'], ['name'], ['name', 'datetime']));
        $filters->set_default_order_by('pixel_id', settings()->main->default_order_type);
        $filters->set_default_results_per_page(settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `pixels` WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('pixels?' . $filters->get_get() . '&page=%d')));

        /* Get the pixels list for the user */
        $pixels = [];
        $pixels_result = database()->query("SELECT * FROM `pixels` WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where()} {$filters->get_sql_order_by()} {$paginator->get_sql_limit()}");
        while($row = $pixels_result->fetch_object()) $pixels[] = $row;

        /* Export handler */
        process_export_csv($pixels, 'include', ['pixel_id', 'user_id', 'type', 'name', 'pixel','last_datetime', 'datetime'], sprintf(l('pixels.title')));
        process_export_json($pixels, 'include', ['pixel_id', 'user_id', 'type', 'name', 'pixel','last_datetime', 'datetime'], sprintf(l('pixels.title')));

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Prepare the View */
        $data = [
            'pixels'              => $pixels,
            'total_pixels'        => $total_rows,
            'pagination'          => $pagination,
            'filters'             => $filters,
        ];

        $view = new \Altum\View('pixels/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function delete() {

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('delete')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('pixels');
        }

        if(empty($_POST)) {
            redirect('pixels');
        }

        $pixel_id = (int) $_POST['pixel_id'];

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!$pixel = db()->where('pixel_id', $pixel_id)->where('user_id', $this->user->user_id)->getOne('pixels', ['pixel_id', 'name'])) {
            redirect('pixels');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the project */
            db()->where('pixel_id', $pixel_id)->delete('pixels');

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItemsByTag('pixels?user_id=' . $this->user->user_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $pixel->name . '</strong>'));

            redirect('pixels');
        }

        redirect('pixels');
    }

}
