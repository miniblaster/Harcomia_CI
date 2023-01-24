<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum;

use Altum\Models\Plan;
use Altum\Models\Settings;
use Altum\Models\User;


class App {

    protected $database;

    public function __construct() {

        /* Connect to the database */
        Database::initialize();

        /* Initialize caching system */
        Cache::initialize();

        /* Initiate the Language system */
        Language::initialize();

        /* Initiate the plugin system */
        Plugin::initialize();

        /* Parse the URL parameters */
        \Altum\Router::parse_url();

        /* Parse the potential language url */
        \Altum\Router::parse_language();

        /* Handle the controller */
        \Altum\Router::parse_controller();

        /* Create a new instance of the controller */
        $controller = \Altum\Router::get_controller(\Altum\Router::$controller, \Altum\Router::$path);

        /* Process the method and get it */
        $method = \Altum\Router::parse_method($controller);

        /* Get the remaining params */
        $params = \Altum\Router::get_params();

        /* Check for Preflight requests for the tracking of submissions from biolink pages */
        if(in_array(\Altum\Router::$controller, ['Link'])) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type');

            /* Check if preflight request */
            if($_SERVER['REQUEST_METHOD'] == 'OPTIONS') die();
        }

        /* Get the website settings */
        $settings = (new Settings())->get();
        \Altum\Settings::initialize($settings);

        /* Initiate the Language system with the default language */
        Language::set_default_by_name($settings->main->default_language);

        /* Set the default theme style */
        ThemeStyle::set_default($settings->main->default_theme_style);

        /* Initiate the Title system */
        Title::initialize($settings->main->title);
        Meta::initialize();

        /* Set the date timezone */
        date_default_timezone_set(Date::$default_timezone);
        Date::$timezone = date_default_timezone_get();

        /* Setting the datetime for backend usages ( insertions in database..etc ) */
        Date::$date = Date::get();

        /* Check if the team is set and do not allow access for certain routes */
        if(isset($_SESSION['team_id']) && \Altum\Plugin::is_active('teams') && !is_null(\Altum\Router::$controller_settings['allow_team_access'])) {
            if(!\Altum\Router::$controller_settings['allow_team_access']) {
                Alerts::add_info(l('global.info_message.team_limit'));
                redirect();
            }
        }

        /* Affiliate check */
        Affiliate::initiate();

        /* Check for a potential logged in account and do some extra checks */
        if(\Altum\Authentication::check()) {

            $user = \Altum\Authentication::$user;

            if(!$user) {
                \Altum\Authentication::logout();
            }

            /* Teams initialization */
            Teams::initialize();

            /* Delegate access if needed */
            if(Teams::delegate_access()) {
                $user = Teams::$team_user;
            }

            /* Determine if the current plan is expired or disabled */
            $user->plan_is_expired = false;

            /* Get current plan proper details */
            $user->plan = (new Plan())->get_plan_by_id($user->plan_id);

            if(!$user->plan || ($user->plan && ((new \DateTime()) > (new \DateTime($user->plan_expiration_date)) && $user->plan_id != 'free') || !$user->plan->status)) {
                $user->plan_is_expired = true;

                /* Switch the user to the default plan */
                db()->where('user_id', $user->user_id)->update('users', [
                    'plan_id' => 'free',
                    'plan_settings' => json_encode(settings()->plan_free->settings),
                    'payment_subscription_id' => ''
                ]);

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('user_id=' .  \Altum\Authentication::$user_id);

                /* Make sure to redirect the person to the payment page and only let the person access the following pages */
                if(!in_array(\Altum\Router::$controller_key, ['index', 'affiliate', 'contact', 'page', 'pages', 'plan', 'pay', 'pay-billing', 'pay-thank-you', 'account', 'account-plan', 'account-payments', 'invoice', 'account-logs', 'account-delete', 'referrals', 'account-api', 'account-redeem-code', 'logout', 'register', 'teams-system', 'teams-member', 'teams-members']) && \Altum\Router::$path != 'admin') {
                    redirect('plan/new');
                }
            }

            /* Update last activity */
            if(!$user->last_activity || (new \DateTime($user->last_activity))->modify('+5 minutes') < (new \DateTime())) {
                (new User())->update_last_activity(\Altum\Authentication::$user_id);
            }

            /* Update the language of the site for next page use if the current language (default) is different than the one the user has */
            if(!isset($_GET['set_language']) && Language::$name != $user->language) {
                Language::set_by_name($user->language);
            }

            /* Update the language of the user if needed */
            if(isset($_GET['set_language']) && array_key_exists($_GET['set_language'], Language::$languages) && Language::$name != $user->language) {
                db()->where('user_id', \Altum\Authentication::$user_id)->update('users', ['language' => $_GET['set_language']]);

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . \Altum\Authentication::$user_id);
            }

            /* Set the timezone to be used for displaying */
            Date::$timezone = $user->timezone;

            /* Store all the details of the user in the Authentication static class as well */
            \Altum\Authentication::$user = $user;
        }

        /* Set a CSRF Token */
        \Altum\Csrf::set('token');
        \Altum\Csrf::set('global_token');

        /* If the language code is the default one, redirect to index */
        if(\Altum\Router::$language_code == Language::$default_code) {
            redirect(\Altum\Router::$original_request);
        }

        /* Redirect based on browser language if needed */
        $browser_language_code = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
        if(settings()->main->auto_language_detection_is_enabled && \Altum\Router::$controller_settings['no_browser_language_detection'] == false && !\Altum\Router::$language_code && !\Altum\Authentication::check() && $browser_language_code && Language::$default_code != $browser_language_code && array_search($browser_language_code, Language::$active_languages)) {
            if(!isset($_SERVER['HTTP_REFERER']) || (isset($_SERVER['HTTP_REFERER']) && parse_url($_SERVER['HTTP_REFERER'])['host'] != parse_url(SITE_URL)['host'])) {
                header('Location: ' . SITE_URL . $browser_language_code . '/' . \Altum\Router::$original_request);
            }
        }

        /* Add main vars inside of the controller */
        $controller->add_params([
            /* Extra params available from the URL */
            'params' => $params,

            /* Potential logged in user */
            'user' => \Altum\Authentication::$user
        ]);

        /* Check for authentication checks */
        if(!is_null(\Altum\Router::$controller_settings['authentication'])) {
            \Altum\Authentication::guard(\Altum\Router::$controller_settings['authentication']);
        }

        /* Call the controller method */
        call_user_func_array([ $controller, $method ], []);

        /* Render and output everything */
        $controller->run();

        /* Close database */
        Database::close();
    }

}
