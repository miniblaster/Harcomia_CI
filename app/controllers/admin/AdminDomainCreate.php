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

class AdminDomainCreate extends Controller {

    public function index() {

        /* Default variables */
        $values = [
            'scheme' => '',
            'host' => '',
        ];

        if(!empty($_POST)) {

            /* Clean some posted variables */
            $_POST['scheme'] = isset($_POST['scheme']) && in_array($_POST['scheme'], ['http://', 'https://']) ? input_clean($_POST['scheme']) : 'https://';
            $_POST['host'] = mb_strtolower(trim($_POST['host']));
            $_POST['custom_index_url'] = trim(filter_var($_POST['custom_index_url'], FILTER_SANITIZE_URL));
            $_POST['custom_not_found_url'] = trim(filter_var($_POST['custom_not_found_url'], FILTER_SANITIZE_URL));
            $_POST['is_enabled'] = (int) (bool) $_POST['is_enabled'];

            /* Default variables */
            $values['scheme'] = $_POST['scheme'];
            $values['host'] = $_POST['host'];
            $values['custom_index_url'] = $_POST['custom_index_url'];

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Check for any errors */
            $required_fields = ['host'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            /* If there are no errors continue the registering process */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                /* Define some needed variables */
                $type = 1;

                /* Add the row to the database */
                db()->insert('domains', [
                    'user_id' => $this->user->user_id,
                    'scheme' => $_POST['scheme'],
                    'host' => $_POST['host'],
                    'custom_index_url' => $_POST['custom_index_url'],
                    'custom_not_found_url' => $_POST['custom_not_found_url'],
                    'type' => $type,
                    'is_enabled' => $_POST['is_enabled'],
                    'datetime' => \Altum\Date::$date,
                ]);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . $_POST['host'] . '</strong>'));

                /* Redirect */
                redirect('admin/domains');
            }

        }

        /* Main View */
        $data = ['values' => $values];

        $view = new \Altum\View('admin/domain-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
