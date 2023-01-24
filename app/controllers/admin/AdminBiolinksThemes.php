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

class AdminBiolinksThemes extends Controller {

    public function index() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters([], [], []));
        $filters->set_default_order_by('biolink_theme_id', settings()->main->default_order_type);
        $filters->set_default_results_per_page(settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `biolinks_themes` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/biolinks-themes?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $biolinks_themes = [];
        $biolinks_themes_result = database()->query("
            SELECT
                `biolinks_themes`.*
            FROM
                `biolinks_themes`
            WHERE
                1 = 1
                {$filters->get_sql_where('biolinks_themes')}
                {$filters->get_sql_order_by('biolinks_themes')}

            {$paginator->get_sql_limit()}
        ");
        while($row = $biolinks_themes_result->fetch_object()) {
            $biolinks_themes[] = $row;
        }

        /* Export handler */
        process_export_csv($biolinks_themes, 'include', ['biolink_theme_id', 'name', 'is_enabled', 'last_datetime', 'datetime'], sprintf(l('admin_biolinks_themes.title')));
        process_export_json($biolinks_themes, 'include', ['biolink_theme_id', 'name', 'settings', 'is_enabled', 'last_datetime', 'datetime'], sprintf(l('admin_biolinks_themes.title')));

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'biolinks_themes' => $biolinks_themes,
            'filters' => $filters,
            'pagination' => $pagination
        ];

        $view = new \Altum\View('admin/biolinks-themes/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bulk() {

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/biolinks-themes');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/biolinks-themes');
        }

        if(!isset($_POST['type']) || (isset($_POST['type']) && !in_array($_POST['type'], ['delete']))) {
            redirect('admin/biolinks-themes');
        }

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            switch($_POST['type']) {
                case 'delete':

                    foreach($_POST['selected'] as $biolink_theme_id) {
                        $biolink_theme = db()->where('biolink_theme_id', $biolink_theme_id)->getOne('biolinks_themes');

                        if(!$biolink_theme) {
                            continue;
                        }

                        $biolink_theme->settings = json_decode($biolink_theme->settings);

                        /* Offload deleting */
                        if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                            $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                            if(!empty($biolink_theme->image)) {
                                $s3->deleteObject([
                                    'Bucket' => settings()->offload->storage_name,
                                    'Key' => 'uploads/biolinks_themes/' . $biolink_theme->image,
                                ]);
                            }

                            if(!empty($biolink_theme->settings->biolink->background) && file_exists(UPLOADS_PATH . 'backgrounds' . '/' . $biolink_theme->settings->biolink->background)) {
                                $s3->deleteObject([
                                    'Bucket' => settings()->offload->storage_name,
                                    'Key' => 'uploads/backgrounds/' . $biolink_theme->settings->biolink->background,
                                ]);
                            }
                        }

                        /* Local deleting */
                        else {
                            if(!empty($biolink_theme->image) && file_exists(UPLOADS_PATH . 'biolinks_themes/' . $biolink_theme->image)) {
                                unlink(UPLOADS_PATH . 'biolinks_themes/' . $biolink_theme->image);
                            }
                            if(!empty($biolink_theme->settings->biolink->background) && file_exists(UPLOADS_PATH . 'backgrounds/' . $biolink_theme->settings->biolink->background)) {
                                unlink(UPLOADS_PATH . 'backgrounds/' . $biolink_theme->settings->biolink->background);
                            }
                        }

                        /* Delete the project */
                        db()->where('biolink_theme_id', $biolink_theme_id)->delete('biolinks_themes');
                    }

                    /* Clear the cache */
                    \Altum\Cache::$adapter->deleteItem('biolinks_themes');

                    break;
            }

            /* Set a nice success message */
            Alerts::add_success(l('admin_bulk_delete_modal.success_message'));

        }

        redirect('admin/biolinks-themes');
    }

    public function delete() {

        $biolink_theme_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!$biolink_theme = db()->where('biolink_theme_id', $biolink_theme_id)->getOne('biolinks_themes')) {
            redirect('admin/biolinks-themes');
        }

        $biolink_theme->settings = json_decode($biolink_theme->settings);

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                if(!empty($biolink_theme->image)) {
                    $s3->deleteObject([
                        'Bucket' => settings()->offload->storage_name,
                        'Key' => 'uploads/biolinks_themes/' . $biolink_theme->image,
                    ]);
                }

                if(!empty($biolink_theme->settings->biolink->background) && file_exists(UPLOADS_PATH . 'backgrounds' . '/' . $biolink_theme->settings->biolink->background)) {
                    $s3->deleteObject([
                        'Bucket' => settings()->offload->storage_name,
                        'Key' => 'uploads/backgrounds/' . $biolink_theme->settings->biolink->background,
                    ]);
                }
            }

            /* Local deleting */
            else {
                if(!empty($biolink_theme->image) && file_exists(UPLOADS_PATH . 'biolinks_themes/' . $biolink_theme->image)) {
                    unlink(UPLOADS_PATH . 'biolinks_themes/' . $biolink_theme->image);
                }
                if(!empty($biolink_theme->settings->biolink->background) && file_exists(UPLOADS_PATH . 'backgrounds/' . $biolink_theme->settings->biolink->background)) {
                    unlink(UPLOADS_PATH . 'backgrounds/' . $biolink_theme->settings->biolink->background);
                }
            }

            /* Delete the project */
            db()->where('biolink_theme_id', $biolink_theme->biolink_theme_id)->delete('biolinks_themes');

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItem('biolinks_themes');

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $biolink_theme->name . '</strong>'));

        }

        redirect('admin/biolinks-themes');
    }

}
