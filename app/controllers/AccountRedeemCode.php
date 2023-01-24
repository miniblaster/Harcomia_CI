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

class AccountRedeemCode extends Controller {

    public function index() {
        \Altum\Authentication::guard();

        if(!settings()->payment->is_enabled || !settings()->payment->codes_is_enabled) {
            redirect();
        }

        /* Get all plans */
        $plans = db()->where('status', 1)->get('plans');

        if(!empty($_POST)) {
            $_POST['plan_id'] = (int) $_POST['plan_id'];
            $code = $_POST['code'] = trim(query_clean($_POST['code']));

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!$code = database()->query("SELECT * FROM `codes` WHERE `code` = '{$code}' AND `type` = 'redeemable' AND `redeemed` < `quantity`")->fetch_object()) {
                Alerts::add_field_error('code', l('account_redeem_code.error_message.code_invalid')); redirect('account-redeem-code');
            }

            if(!$plan = db()->where('plan_id', $_POST['plan_id'])->getOne('plans')) {
                Alerts::add_field_error('code', l('account_redeem_code.error_message.code_invalid')); redirect('account-redeem-code');
            }

            $plan->settings = json_decode($plan->settings);
            $plan->codes_ids = json_decode($plan->codes_ids);

            if(!in_array($code->code_id, $plan->codes_ids)) {
                Alerts::add_field_error('code', l('account_redeem_code.error_message.code_invalid'));
            }

            if(db()->where('user_id', $this->user->user_id)->where('code_id', $code->code_id)->has('redeemed_codes')) {
                Alerts::add_field_error('code', l('account_redeem_code.error_message.code_used'));
            }

            /* Cancel current subscription if needed */
            if($this->user->plan_id != $plan->plan_id) {
                try {
                    (new User())->cancel_subscription($this->user->user_id);
                } catch (\Exception $exception) {
                    Alerts::add_error($exception->getCode() . ':' . $exception->getMessage());
                }
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                $datetime = $this->user->plan_id == $plan->plan_id ? $this->user->plan_expiration_date : '';
                $plan_expiration_date = (new \DateTime($datetime))->modify('+' . $code->days . ' days')->format('Y-m-d H:i:s');
                $plan_settings = json_encode($plan->settings);

                /* Database query */
                db()->where('user_id', $this->user->user_id)->update('users', [
                    'plan_id' => $plan->plan_id,
                    'plan_expiration_date' => $plan_expiration_date,
                    'plan_settings' => $plan_settings,
                    'plan_expiry_reminder' => 0,
                ]);

                /* Update the code usage */
                db()->where('code_id', $code->code_id)->update('codes', ['redeemed' => db()->inc()]);

                /* Add log for the redeemed code */
                db()->insert('redeemed_codes', [
                    'code_id'   => $code->code_id,
                    'user_id'   => $this->user->user_id,
                    'datetime'  => \Altum\Date::$date
                ]);

                /* Send webhook notification if needed */
                if(settings()->webhooks->code_redeemed) {
                    \Unirest\Request::post(settings()->webhooks->code_redeemed, [], [
                        'user_id' => $this->user->user_id,
                        'email' => $this->user->email,
                        'name' => $this->user->name,
                        'plan_id' => $plan->plan_id,
                        'plan_expiration_date' => $plan_expiration_date,
                        'code_id' => $code->code_id,
                        'code' => $code->code,
                        'code_name' => $code->name,
                        'redeemed_days' => $code->days,
                    ]);
                }

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $this->user->user_id);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('account_redeem_code.success_message'), $code->days, $plan->name));

                redirect('account-redeem-code');
            }

        }

        /* Default values */
        $values = [
            'plan_id' => $_POST['plan_id'] ?? null,
            'code' => $_POST['code'] ?? null,
        ];

        /* Get the account header menu */
        $menu = new \Altum\View('partials/account_header_menu', (array) $this);
        $this->add_view_content('account_header_menu', $menu->run());

        /* Prepare the View */
        $data = [
            'plans' => $plans,
            'values' => $values,
        ];

        $view = new \Altum\View('account-redeem-code/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
