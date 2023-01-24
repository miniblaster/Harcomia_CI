<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;


class Refer extends Controller {

    public function index() {

        \Altum\Authentication::guard('guest');

        if(!\Altum\Plugin::is_active('affiliate') || (\Altum\Plugin::is_active('affiliate') && !settings()->affiliate->is_enabled)) {
            redirect();
        }

        $referral_key = isset($this->params[0]) ? query_clean($this->params[0]) : null;

        /* Get the owner user of the referral key */
        if(!$user = db()->where('referral_key', $referral_key)->getOne('users', ['user_id', 'plan_settings', 'status', 'referral_key'])) {
            redirect();
        }

        /* Make sure the user is still active */
        if($user->status != 1) {
            redirect();
        }

        /* Make sure the user has access to the affiliate program */
        $user->plan_settings = json_decode($user->plan_settings);
        if(!$user->plan_settings->affiliate_commission_percentage) {
            redirect();
        }

        /* Set the cookie for 90 days */
        setcookie('referred_by', $user->referral_key, time()+60*60*24*90, COOKIE_PATH);

        /* Redirect to the landing page */
        $redirect = isset($_GET['redirect']) ? query_clean($_GET['redirect']) : 'dashboard';
        redirect($redirect);

    }

}
