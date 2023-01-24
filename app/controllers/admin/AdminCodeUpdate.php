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

class AdminCodeUpdate extends Controller {

    public function index() {

        $code_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$code = db()->where('code_id', $code_id)->getOne('codes')) {
            redirect('admin/codes');
        }

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['name'] = input_clean($_POST['name']);
            $_POST['type'] = in_array($_POST['type'], ['discount', 'redeemable']) ? input_clean($_POST['type']) : 'discount';
            $_POST['days'] = $_POST['type'] == 'redeemable' ? (int) $_POST['days'] : null;
            $_POST['plan_id'] = empty($_POST['plan_id']) ? null : (int) $_POST['plan_id'];
            $_POST['discount'] = $_POST['type'] == 'redeemable' ? 100 : (int) $_POST['discount'];
            $_POST['quantity'] = (int) $_POST['quantity'];
            $_POST['code'] = trim(get_slug($_POST['code'], '-', false));

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Database query */
                db()->where('code_id', $code_id)->update('codes', [
                    'name' => $_POST['name'],
                    'type' => $_POST['type'],
                    'days' => $_POST['days'],
                    'code' => $_POST['code'],
                    'discount' => $_POST['discount'],
                    'quantity' => $_POST['quantity'],
                ]);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['code'] . '</strong>'));

                /* Refresh the page */
                redirect('admin/code-update/' . $code_id);

            }

        }

        /* Main View */
        $data = [
            'code_id' => $code_id,
            'code' => $code,
        ];

        $view = new \Altum\View('admin/code-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
