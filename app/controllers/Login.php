<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use Altum\Alerts;
use Altum\Captcha;
use Altum\Logger;
use Altum\Models\User;
use Google\Client;
use Google\Service\Oauth2;

class Login extends Controller {

    public function index() {

        \Altum\Authentication::guard('guest');

        $method	= (isset($this->params[0])) ? $this->params[0] : null;
        $redirect = isset($_GET['redirect']) ? query_clean($_GET['redirect']) : 'dashboard';

        /* Default values */
        $values = [
            'email' => isset($_GET['email']) ? query_clean($_GET['email']) : '',
            'password' => '',
            'rememberme' => isset($_POST['rememberme']),
        ];

        //ALTUMCODE:DEMO if(DEMO) {$values['email'] = 'admin'; $values['password'] = 'admin';$user=(object)['twofa_secret' => null];}

        /* Initiate captcha */
        $captcha = new Captcha();

        /* One time login */
        if($method == 'one-time-login-code') {
            $one_time_login_code = isset($this->params[1]) ? query_clean($this->params[1]) : null;

            if(empty($one_time_login_code)) {
                redirect('login');
            }

            /* Try to get the user from the database */
            $user = db()->where('one_time_login_code', $one_time_login_code)->getOne('users', ['user_id', 'password', 'name', 'status', 'language']);

            if(!$user) {
                redirect('login');
            }

            if($user->status != 1) {
                Alerts::add_error(l('login.error_message.user_not_active'));
                redirect('login');
            }

            /* Login the user */
            $_SESSION['user_id'] = $user->user_id;
            $_SESSION['user_password_hash'] = md5($user->password);

            (new User())->login_aftermath_update($user->user_id);

            /* Remove one time login */
            db()->where('user_id', $user->user_id)->update('users', ['one_time_login_code' => null]);

            /* Set a welcome message */
            Alerts::add_info(sprintf(l('login.info_message.logged_in'), $user->name));

            /* Check to see if the user has a custom language set */
            if(\Altum\Language::$name == $user->language) {
                redirect($redirect);
            } else {
                redirect((\Altum\Language::$active_languages[$user->language] ? \Altum\Language::$active_languages[$user->language] . '/' : null) . $redirect, true);
            }
        }

        /* Facebook Login / Register */
        if(settings()->facebook->is_enabled && in_array($method, ['facebook-initiate', 'facebook'])) {
            $facebook = new \Facebook\Facebook([
                'app_id' => settings()->facebook->app_id,
                'app_secret' => settings()->facebook->app_secret,
                'default_graph_version' => 'v3.2',
            ]);

            $facebook_helper = $facebook->getRedirectLoginHelper();

            if($method == 'facebook-initiate') {
                $facebook_login_url = $facebook->getRedirectLoginHelper()->getLoginUrl(SITE_URL . 'login/facebook', ['email', 'public_profile']);
                header('Location: ' . $facebook_login_url); die();
            }

            /* Check for the redirect after the oauth checkin */
            if($method == 'facebook') {
                try {
                    $facebook_access_token = $facebook_helper->getAccessToken(SITE_URL . 'login/facebook');
                } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                    Alerts::add_error('Graph returned an error: ' . $e->getMessage());
                } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                    Alerts::add_error('Facebook SDK returned an error: ' . $e->getMessage());
                }

                if(isset($facebook_access_token)) {

                    /* The OAuth 2.0 client handler helps us manage access tokens */
                    $facebook_oAuth2_client = $facebook->getOAuth2Client();

                    /* Get the access token metadata from /debug_token */
                    $facebook_token_metadata = $facebook_oAuth2_client->debugToken($facebook_access_token);

                    /* Validation */
                    $facebook_token_metadata->validateAppId(settings()->facebook->app_id);
                    $facebook_token_metadata->validateExpiration();

                    if(!$facebook_access_token->isLongLived()) {
                        /* Exchanges a short-lived access token for a long-lived one */
                        try {
                            $facebook_access_token = $facebook_oAuth2_client->getLongLivedAccessToken($facebook_access_token);
                        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                            Alerts::add_error('Error getting long-lived access token: ' . $facebook_helper->getMessage());
                        }
                    }

                    try {
                        $response = $facebook->get('/me?fields=id,name,email', $facebook_access_token);
                    } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                        Alerts::add_error('Graph returned an error: ' . $e->getMessage());
                    } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                        Alerts::add_error('Facebook SDK returned an error: ' . $e->getMessage());
                    }

                    if(isset($response)) {
                        $facebook_user = $response->getGraphUser();
                        $email = $facebook_user->getEmail();
                        $name = $facebook_user->getName();

                        /* Check if email is actually not null */
                        if(is_null($email)) {
                            Alerts::add_error(l('login.error_message.email_is_null'));
                            redirect('login');
                        }

                        $this->process_social_login($email, $name, $redirect, $method);
                    }
                }
            }
        }

        /* Google Login / Register */
        if(settings()->google->is_enabled && in_array($method, ['google-initiate', 'google'])) {
            $client = new Client();
            $client->setClientId(settings()->google->client_id);
            $client->setClientSecret(settings()->google->client_secret);
            $client->setRedirectUri(SITE_URL . 'login/google');
            $client->addScope('email');
            $client->addScope('profile');

            if($method == 'google-initiate') {
                $google_login_url = $client->createAuthUrl();
                header('Location: ' . $google_login_url); die();
            }

            if($method == 'google' && isset($_GET['code'])) {
                $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
                $client->setAccessToken($token['access_token']);

                /* Get profile info */
                $google_oauth = new Oauth2($client);
                $google_account_info = $google_oauth->userinfo->get();
                $email = $google_account_info->email;
                $name = $google_account_info->name;

                if(is_null($email)) {
                    Alerts::add_error(l('login.error_message.email_is_null'));
                    redirect('login');
                }

                $this->process_social_login($email, $name, $redirect, $method);
            }
        }

        /* Twitter Login / Register */
        if(settings()->twitter->is_enabled && in_array($method, ['twitter-initiate', 'twitter'])) {

            if($method == 'twitter-initiate') {
                try {
                    $twitter = new TwitterOAuth(settings()->twitter->consumer_api_key, settings()->twitter->consumer_api_secret);
                    $twitter_request_token = $twitter->oauth('oauth/request_token', ['oauth_callback' => SITE_URL . 'login/twitter']);
                    $_SESSION['twitter_request_token'] = $twitter_request_token;
                    $twitter_login_url = $twitter->url('oauth/authorize', ['oauth_token' => $twitter_request_token['oauth_token']]);
                    header('Location: ' . $twitter_login_url); die();

                } catch (\Exception $exception) {
                    Alerts::add_error($exception->getMessage());
                    redirect('login');
                }
            }

            if($method == 'twitter' && isset($_GET['oauth_token'], $_GET['oauth_verifier'])) {
                try {
                    $twitter_logged_in = new TwitterOAuth(settings()->twitter->consumer_api_key, settings()->twitter->consumer_api_secret, $_SESSION['twitter_request_token']['oauth_token'], $_SESSION['twitter_request_token']['oauth_token_secret']);
                    $twitter_access_token = $twitter_logged_in->oauth('oauth/access_token', ['oauth_verifier' => $_GET['oauth_verifier']]);

                    /* Get profile info */
                    $twitter_logged_in = new TwitterOAuth(settings()->twitter->consumer_api_key, settings()->twitter->consumer_api_secret, $twitter_access_token['oauth_token'], $twitter_access_token['oauth_token_secret']);
                    $twitter_account_info = $twitter_logged_in->get('account/verify_credentials', ['include_email' => true]);
                    $email = $twitter_account_info->email;
                    $name = $twitter_account_info->name;

                    if(is_null($email)) {
                        Alerts::add_error(l('login.error_message.email_is_null'));
                        redirect('login');
                    }

                    $this->process_social_login($email, $name, $redirect, $method);
                } catch (\Exception $exception) {
                    Alerts::add_error($exception->getMessage());
                    redirect('login');
                }
            }

        }

        /* Discord Login / Register */
        if(settings()->discord->is_enabled && in_array($method, ['discord-initiate', 'discord'])) {
            $discord = new \Wohali\OAuth2\Client\Provider\Discord([
                'clientId' => settings()->discord->client_id,
                'clientSecret' => settings()->discord->client_secret,
                'redirectUri' => SITE_URL . 'login/discord'
            ]);

            if($method == 'discord-initiate') {
                try {
                    $discord_login_url = $discord->getAuthorizationUrl([
                        'scope' => ['identify', 'email']
                    ]);
                    $_SESSION['oauth2state'] = $discord->getState();
                    header('Location: ' . $discord_login_url); die();
                } catch (\Exception $exception) {
                    Alerts::add_error($exception->getMessage());
                    redirect('login');
                }
            }

            if($method == 'discord' && isset($_GET['code'])) {
                try {
                    $token = $discord->getAccessToken('authorization_code', [
                        'code' => $_GET['code']
                    ]);

                    $discord_account_info = $discord->getResourceOwner($token);
                    $email = $discord_account_info->toArray()['email'];
                    $name = $discord_account_info->toArray()['username'];

                    if(is_null($email)) {
                        Alerts::add_error(l('login.error_message.email_is_null'));
                        redirect('login');
                    }

                    $this->process_social_login($email, $name, $redirect, $method);
                } catch (\Exception $exception) {
                    Alerts::add_error($exception->getMessage());
                    redirect('login');
                }
            }

        }

        if(!empty($_POST)) {
            /* Clean email and encrypt the password */
            $_POST['email'] = query_clean($_POST['email']);
            $_POST['twofa_token'] = isset($_POST['twofa_token']) ? query_clean($_POST['twofa_token']) : null;
            $values['email'] = $_POST['email'];
            $values['password'] = $_POST['password'];

            /* Check for any errors */
            $required_fields = ['email', 'password'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(settings()->captcha->login_is_enabled && !isset($_SESSION['twofa_required']) && !$captcha->is_valid()) {
                Alerts::add_field_error('captcha', l('global.error_message.invalid_captcha'));
            }

            /* Try to get the user from the database */
            $user = db()->where('email', $_POST['email'])->getOne('users', ['user_id', 'email', 'name', 'status', 'password', 'token_code', 'twofa_secret', 'language']);

            if(!$user) {
                Alerts::add_error(l('login.error_message.wrong_login_credentials'));
            } else {

                if($user->status != 1) {
                    Alerts::add_error(l('login.error_message.user_not_active'));
                } else

                    if(!password_verify($_POST['password'], $user->password)) {
                        Logger::users($user->user_id, 'login.wrong_password');

                        Alerts::add_error(l('login.error_message.wrong_login_credentials'));
                    }

            }

            /* Check if the user has Two-factor Authentication enabled */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                if($user && $user->twofa_secret) {
                    $_SESSION['twofa_required'] = 1;

                    if($_POST['twofa_token']) {
                        $twofa = new \RobThree\Auth\TwoFactorAuth(settings()->main->title, 6, 30);
                        $twofa_check = $twofa->verifyCode($user->twofa_secret, $_POST['twofa_token']);

                        if (!$twofa_check) {
                            Alerts::add_field_error('twofa_token', l('login.error_message.twofa_token'));
                        }
                    } else {
                        Alerts::add_info(l('login.info_message.twofa_token'));
                    }
                }
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors() && !Alerts::has_infos()) {

                /* If remember me is checked, log the user with cookies for 30 days else, remember just with a session */
                if(isset($_POST['rememberme'])) {
                    $token_code = $user->token_code;

                    /* Generate a new token */
                    if(empty($user->token_code)) {
                        $token_code = md5($user->email . microtime());

                        db()->where('user_id', $user->user_id)->update('users', ['token_code' => $token_code]);
                    }

                    setcookie('user_id', $user->user_id, time()+60*60*24*30, COOKIE_PATH);
                    setcookie('token_code', $token_code, time()+60*60*24*30, COOKIE_PATH);
                    setcookie('user_password_hash', md5($user->password), time()+60*60*24*30, COOKIE_PATH);

                } else {
                    $_SESSION['user_id'] = $user->user_id;
                    $_SESSION['user_password_hash'] = md5($user->password);
                }

                unset($_SESSION['twofa_required']);

                (new User())->login_aftermath_update($user->user_id);

                Alerts::add_info(sprintf(l('login.info_message.logged_in'), $user->name));

                /* Check to see if the user has a custom language set */
                if(\Altum\Language::$name == $user->language) {
                    redirect($redirect);
                } else {
                    redirect((\Altum\Language::$active_languages[$user->language] ? \Altum\Language::$active_languages[$user->language] . '/' : null) . $redirect, true);
                }
            }
        }

        if(empty($_POST)) {
            unset($_SESSION['twofa_required']);
        }

        /* Prepare the View */
        $data = [
            'captcha' => $captcha,
            'values' => $values,
            'user' => $user ?? null
        ];

        $view = new \Altum\View('login/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    /* After a successful social login auth, register or login the user */
    private function process_social_login($email, $name, $redirect, $method) {

        /* If the user is already in the system, log him in */
        if($user = db()->where('email', $email)->getOne('users', ['user_id', 'email', 'password', 'lost_password_code', 'language'])) {

            /* Make sure the user has a password set before letting the user login */
            (new User())->verify_null_password($user->user_id, $user->email, $user->password);

            $_SESSION['user_id'] = $user->user_id;
            $_SESSION['user_password_hash'] = md5($user->password);

            (new User())->login_aftermath_update($user->user_id, $method);

            /* Check to see if the user has a custom language set */
            if(\Altum\Language::$name == $user->language) {
                redirect($redirect);
            } else {
                redirect((\Altum\Language::$active_languages[$user->language] ? \Altum\Language::$active_languages[$user->language] . '/' : null) . $redirect, true);
            }
        }

        /* Create a new account */
        else {

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Determine what plan is set by default */
                $plan_id                    = 'free';
                $plan_settings              = json_encode(settings()->plan_free->settings);
                $plan_expiration_date       = \Altum\Date::$date;
                $lost_password_code         = md5($email . microtime());

                $registered_user = (new User())->create(
                    $email,
                    null,
                    $name,
                    1,
                    $method,
                    null,
                    $lost_password_code,
                    $plan_id,
                    $plan_settings,
                    $plan_expiration_date,
                    settings()->main->default_timezone
                );

                /* Log the action */
                Logger::users($registered_user['user_id'], 'register.' . $method . '.success');

                /* Send notification to admin if needed */
                if(settings()->email_notifications->new_user && !empty(settings()->email_notifications->emails)) {

                    $email_template = get_email_template(
                        [],
                        l('global.emails.admin_new_user_notification.subject'),
                        [
                            '{{NAME}}' => $name,
                            '{{EMAIL}}' => $email,
                        ],
                        l('global.emails.admin_new_user_notification.body')
                    );

                    send_mail(explode(',', settings()->email_notifications->emails), $email_template->subject, $email_template->body);

                }

                /* Send webhook notification if needed */
                if(settings()->webhooks->user_new) {

                    \Unirest\Request::post(settings()->webhooks->user_new, [], [
                        'user_id' => $registered_user['user_id'],
                        'email' => $email,
                        'name' => $name,
                        'source' => $method,
                    ]);

                }

                /* Redirect the newly created user to set a new password */
                redirect('reset-password/' . md5($email) . '/' . $lost_password_code);
            }
        }
    }

}
