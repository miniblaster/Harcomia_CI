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
use Altum\Models\User;

class AccountDelete extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(!empty($_POST)) {

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!password_verify($_POST['current_password'], $this->user->password)) {
                Alerts::add_field_error('current_password', l('account.error_message.invalid_current_password'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Delete the user */
                (new User())->delete($this->user->user_id);

                /* Send notification to admin if needed */
                if(settings()->email_notifications->delete_user && !empty(settings()->email_notifications->emails)) {
                    $email_template = get_email_template(
                        [],
                        l('global.emails.admin_delete_user_notification.subject'),
                        [
                            '{{NAME}}' => $this->user->name,
                            '{{EMAIL}}' => $this->user->email,
                        ],
                        l('global.emails.admin_delete_user_notification.body')
                    );

                    send_mail(explode(',', settings()->email_notifications->emails), $email_template->subject, $email_template->body);
                }

                /* Logout of the user */
                \Altum\Authentication::logout(false);

                /* Start a new session to set a deletion message */
                session_start();

                /* Set a nice success message */
                Alerts::add_success(l('account_delete.success_message'));

                redirect();

            }

        }

        /* Get the account header menu */
        $menu = new \Altum\View('partials/account_header_menu', (array) $this);
        $this->add_view_content('account_header_menu', $menu->run());

        /* Prepare the View */
        $view = new \Altum\View('account-delete/index', (array) $this);

        $this->add_view_content('content', $view->run([]));

    }

}
