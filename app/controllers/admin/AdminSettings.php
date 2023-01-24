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


class AdminSettings extends Controller {

    public function index() {
        redirect('admin/settings/main');
    }

    private function process() {
        $method	= (isset(\Altum\Router::$method) && file_exists(THEME_PATH . 'views/admin/settings/partials/' . \Altum\Router::$method . '.php')) ? \Altum\Router::$method : 'main';
        $payment_processors = require APP_PATH . 'includes/payment_processors.php';

        /* Method View */
        $view = new \Altum\View('admin/settings/partials/' . $method, (array) $this);
        $this->add_view_content('method', $view->run());

        /* Main View */
        $view = new \Altum\View('admin/settings/index', (array) $this);
        $this->add_view_content('content', $view->run([
            'method' => $method,
            'payment_processors' => $payment_processors,
        ]));
    }

    private function update_settings($key, $value) {
        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Update the database */
            db()->where('`key`', $key)->update('settings', ['value' => $value]);

            $this->after_update_settings($key);
        }

        redirect('admin/settings/' . $key);
    }

    private function after_update_settings($key) {

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('settings');

        /* Set a nice success message */
        Alerts::add_success(l('global.success_message.update2'));

        /* Refresh the page */
        redirect('admin/settings/' . $key);

    }

    public function main() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Uploads processing */
            foreach(['logo_light', 'logo_dark', 'logo_email', 'favicon', 'opengraph'] as $image_key) {
                settings()->main->{$image_key} = \Altum\Uploads::process_upload(settings()->main->{$image_key}, $image_key, $image_key, $image_key . '_remove', null);
            }

            /* :) */
            $value = json_encode([
                'title' => $_POST['title'],
                'default_language' => $_POST['default_language'],
                'default_theme_style' => $_POST['default_theme_style'],
                'default_timezone' => $_POST['default_timezone'],
                'index_url' => $_POST['index_url'],
                'terms_and_conditions_url' => $_POST['terms_and_conditions_url'],
                'privacy_policy_url' => $_POST['privacy_policy_url'],
                'not_found_url' => $_POST['not_found_url'],
                'se_indexing' => (bool) $_POST['se_indexing'],
                'default_results_per_page' => (int) $_POST['default_results_per_page'],
                'default_order_type' => $_POST['default_order_type'],
                'auto_language_detection_is_enabled' => (bool) $_POST['auto_language_detection_is_enabled'],
                'blog_is_enabled' => (bool) $_POST['blog_is_enabled'],
                'logo_light' => settings()->main->logo_light ?? '',
                'logo_dark' => settings()->main->logo_dark ?? '',
                'logo_email' => settings()->main->logo_email ?? '',
                'opengraph' => settings()->main->opengraph ?? '',
                'favicon' => settings()->main->favicon ?? '',
            ]);

            $this->update_settings('main', $value);
        }
    }

    public function users() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['blacklisted_domains'] = implode(',', array_map('trim', explode(',', $_POST['blacklisted_domains'])));
            $_POST['blacklisted_countries'] = $_POST['blacklisted_countries'] ?? [];

            $value = json_encode([
                'email_confirmation' => (bool) $_POST['email_confirmation'],
                'register_is_enabled' => (bool) $_POST['register_is_enabled'],
                'auto_delete_inactive_users' => (int) $_POST['auto_delete_inactive_users'],
                'user_deletion_reminder' => (int) $_POST['user_deletion_reminder'],
                'blacklisted_domains' => $_POST['blacklisted_domains'],
                'blacklisted_countries' => $_POST['blacklisted_countries'],
            ]);

            $this->update_settings('users', $value);
        }
    }

    public function payment() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];
            $_POST['type'] = in_array($_POST['type'], ['one_time', 'recurring', 'both']) ? input_clean($_POST['type']) : 'both';
            $_POST['codes_is_enabled'] = (bool)$_POST['codes_is_enabled'];
            $_POST['taxes_and_billing_is_enabled'] = (bool)$_POST['taxes_and_billing_is_enabled'];
            $_POST['invoice_is_enabled'] = (bool) $_POST['invoice_is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'type' => $_POST['type'],
                'currency' => $_POST['currency'],
                'codes_is_enabled' => $_POST['codes_is_enabled'],
                'taxes_and_billing_is_enabled' => $_POST['taxes_and_billing_is_enabled'],
                'invoice_is_enabled' => $_POST['invoice_is_enabled'],
            ]);

            $this->update_settings('payment', $value);
        }
    }

    public function paypal() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];
            $_POST['mode'] = in_array($_POST['mode'], ['live', 'sandbox']) ? input_clean($_POST['mode']) : 'live';

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'mode' => $_POST['mode'],
                'client_id' => $_POST['client_id'],
                'secret' => $_POST['secret'],
            ]);

            $this->update_settings('paypal', $value);
        }
    }

    public function stripe() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'publishable_key' => $_POST['publishable_key'],
                'secret_key' => $_POST['secret_key'],
                'webhook_secret' => $_POST['webhook_secret'],
            ]);

            $this->update_settings('stripe', $value);
        }
    }

    public function offline_payment() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'instructions' => $_POST['instructions'],
            ]);

            $this->update_settings('offline_payment', $value);
        }
    }

    public function coinbase() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'api_key' => $_POST['api_key'],
                'webhook_secret' => $_POST['webhook_secret'],
            ]);

            $this->update_settings('coinbase', $value);
        }
    }

    public function payu() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];
            $_POST['mode'] = in_array($_POST['mode'], ['secure', 'sandbox']) ? input_clean($_POST['mode']) : 'secure';

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'mode' => $_POST['mode'],
                'merchant_pos_id' => $_POST['merchant_pos_id'],
                'signature_key' => $_POST['signature_key'],
                'oauth_client_id' => $_POST['oauth_client_id'],
                'oauth_client_secret' => $_POST['oauth_client_secret'],
            ]);

            $this->update_settings('payu', $value);
        }
    }

    public function paystack() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'public_key' => $_POST['public_key'],
                'secret_key' => $_POST['secret_key'],
            ]);

            $this->update_settings('paystack', $value);
        }
    }

    public function razorpay() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'key_id' => $_POST['key_id'],
                'key_secret' => $_POST['key_secret'],
                'webhook_secret' => $_POST['webhook_secret'],
            ]);

            $this->update_settings('razorpay', $value);
        }
    }

    public function mollie() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'api_key' => $_POST['api_key'],
            ]);

            $this->update_settings('mollie', $value);
        }
    }

    public function yookassa() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'shop_id' => $_POST['shop_id'],
                'secret_key' => $_POST['secret_key'],
            ]);

            $this->update_settings('yookassa', $value);
        }
    }

    public function crypto_com() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'publishable_key' => $_POST['publishable_key'],
                'secret_key' => $_POST['secret_key'],
                'webhook_secret' => $_POST['webhook_secret'],
            ]);

            $this->update_settings('crypto_com', $value);
        }
    }

    public function paddle() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];
            $_POST['mode'] = in_array($_POST['mode'], ['live', 'sandbox']) ? input_clean($_POST['mode']) : 'live';

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'mode' => $_POST['mode'],
                'vendor_id' => $_POST['vendor_id'],
                'api_key' => $_POST['api_key'],
                'public_key' => $_POST['public_key'],
            ]);

            $this->update_settings('paddle', $value);
        }
    }

    public function affiliate() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Plugin::is_active('affiliate')) {
                redirect('admin/settings/affiliate');
            }

            /* :) */
            $_POST['is_enabled'] = (bool)$_POST['is_enabled'];
            $_POST['commission_type'] = in_array($_POST['commission_type'], ['once', 'forever']) ? input_clean($_POST['commission_type']) : 'once';
            $_POST['minimum_withdrawal_amount'] = (float)$_POST['minimum_withdrawal_amount'];
            $_POST['withdrawal_notes'] = trim(input_clean($_POST['withdrawal_notes']));

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'commission_type' => $_POST['commission_type'],
                'minimum_withdrawal_amount' => $_POST['minimum_withdrawal_amount'],
                'withdrawal_notes' => $_POST['withdrawal_notes'],
            ]);

            $this->update_settings('affiliate', $value);
        }
    }

    public function business() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['brand_name'] = input_clean($_POST['brand_name']);

            $value = json_encode([
                'brand_name' => $_POST['brand_name'],
                'invoice_nr_prefix' => $_POST['invoice_nr_prefix'],
                'name' => $_POST['name'],
                'address' => $_POST['address'],
                'city' => $_POST['city'],
                'county' => $_POST['county'],
                'zip' => $_POST['zip'],
                'country' => $_POST['country'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'tax_type' => $_POST['tax_type'],
                'tax_id' => $_POST['tax_id'],
                'custom_key_one' => $_POST['custom_key_one'],
                'custom_value_one' => $_POST['custom_value_one'],
                'custom_key_two' => $_POST['custom_key_two'],
                'custom_value_two' => $_POST['custom_value_two'],
            ]);

            $this->update_settings('business', $value);
        }
    }

    public function captcha() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['type'] = in_array($_POST['type'], ['basic', 'recaptcha', 'hcaptcha', 'turnstile']) ? $_POST['type'] : 'basic';
            foreach(['login', 'register', 'lost_password', 'resend_activation', 'contact', 'biolink'] as $key) {
                $_POST[$key . '_is_enabled'] = (bool) $_POST[$key . '_is_enabled'];
            }

            $value = json_encode([
                'type' => $_POST['type'],
                'recaptcha_public_key' => $_POST['recaptcha_public_key'],
                'recaptcha_private_key' => $_POST['recaptcha_private_key'],
                'hcaptcha_site_key' => $_POST['hcaptcha_site_key'],
                'hcaptcha_secret_key' => $_POST['hcaptcha_secret_key'],
                'turnstile_site_key' => $_POST['turnstile_site_key'],
                'turnstile_secret_key' => $_POST['turnstile_secret_key'],
                'login_is_enabled' => $_POST['login_is_enabled'],
                'register_is_enabled' => $_POST['register_is_enabled'],
                'lost_password_is_enabled' => $_POST['lost_password_is_enabled'],
                'resend_activation_is_enabled' => $_POST['resend_activation_is_enabled'],
                'contact_is_enabled' => $_POST['contact_is_enabled'],
                'biolink_is_enabled' => $_POST['biolink_is_enabled'],
            ]);

            $this->update_settings('captcha', $value);
        }
    }

    public function facebook() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'app_id' => $_POST['app_id'],
                'app_secret' => $_POST['app_secret'],
            ]);

            $this->update_settings('facebook', $value);
        }
    }

    public function google() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'client_id' => $_POST['client_id'],
                'client_secret' => $_POST['client_secret'],
            ]);

            $this->update_settings('google', $value);
        }
    }

    public function twitter() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'consumer_api_key' => $_POST['consumer_api_key'],
                'consumer_api_secret' => $_POST['consumer_api_secret'],
            ]);

            $this->update_settings('twitter', $value);
        }
    }

    public function discord() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'client_id' => $_POST['client_id'],
                'client_secret' => $_POST['client_secret'],
            ]);

            $this->update_settings('discord', $value);
        }
    }

    public function ads() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $value = json_encode([
                'header' => $_POST['header'],
                'footer' => $_POST['footer'],
                'header_biolink' => $_POST['header_biolink'],
                'footer_biolink' => $_POST['footer_biolink'],
            ]);

            $this->update_settings('ads', $value);
        }
    }

    public function cookie_consent() {
        $this->process();

        /* CSV Export */
        if(isset($_GET['export']) && $_GET['export'] == 'csv') {
            //ALTUMCODE:DEMO if(DEMO) exit('This command is blocked on the demo.');

            header('Content-Disposition: attachment; filename="data.csv";');
            header('Content-Type: application/csv; charset=UTF-8');

            die(file_get_contents(UPLOADS_PATH . 'cookie_consent/data.csv'));
        }

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];
            $_POST['logging_is_enabled'] = (bool) $_POST['logging_is_enabled'];
            $_POST['necessary_is_enabled'] = true;
            $_POST['analytics_is_enabled'] = (bool) $_POST['analytics_is_enabled'];
            $_POST['targeting_is_enabled'] = (bool) $_POST['targeting_is_enabled'];
            $_POST['layout'] = in_array($_POST['layout'], ['cloud', 'box', 'bar']) ? $_POST['layout'] : 'cloud';
            $_POST['position_y'] = in_array($_POST['position_y'], ['top', 'middle', 'bottom']) ? $_POST['position_y'] : 'bottom';
            $_POST['position_x'] = in_array($_POST['position_x'], ['left', 'center', 'right']) ? $_POST['position_x'] : 'center';

            if($_POST['logging_is_enabled']) {
                if(!is_writable(UPLOADS_PATH . 'cookie_consent/')) {
                    Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . 'cookie_consent/'));
                }
            }

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'logging_is_enabled' => $_POST['logging_is_enabled'],
                'necessary_is_enabled' => $_POST['necessary_is_enabled'],
                'analytics_is_enabled' => $_POST['analytics_is_enabled'],
                'targeting_is_enabled' => $_POST['targeting_is_enabled'],
                'layout' => $_POST['layout'],
                'position_y' => $_POST['position_y'],
                'position_x' => $_POST['position_x'],
            ]);

            $this->update_settings('cookie_consent', $value);
        }
    }

    public function socials() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $value = [];
            foreach(require APP_PATH . 'includes/admin_socials.php' as $key => $social) {
                $value[$key] = $_POST[$key];
            }
            $value = json_encode($value);

            $this->update_settings('socials', $value);
        }
    }

    public function smtp() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['auth'] = (bool) isset($_POST['auth']);
            $_POST['username'] = input_clean($_POST['username'] ?? '');
            $_POST['password'] = $_POST['password'] ?? '';

            $value = json_encode([
                'from_name' => $_POST['from_name'],
                'from' => $_POST['from'],
                'host' => $_POST['host'],
                'encryption' => $_POST['encryption'],
                'port' => $_POST['port'],
                'auth' => $_POST['auth'],
                'username' => $_POST['username'],
                'password' => $_POST['password'],
            ]);

            $this->update_settings('smtp', $value);
        }
    }

    public function custom() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $value = json_encode([
                'head_js' => $_POST['head_js'],
                'head_css' => $_POST['head_css'],
                'head_js_biolink' => $_POST['head_js_biolink'],
                'head_css_biolink' => $_POST['head_css_biolink'],
            ]);

            $this->update_settings('custom', $value);
        }
    }

    public function announcements() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['guests_id'] = md5($_POST['content'] . time());
            $_POST['guests_text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['guests_text_color']) ? '#000' : $_POST['guests_text_color'];
            $_POST['guests_background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['guests_background_color']) ? '#fff' : $_POST['guests_background_color'];
            $_POST['users_id'] = md5($_POST['content'] . time());
            $_POST['users_text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['users_text_color']) ? '#000' : $_POST['users_text_color'];
            $_POST['users_background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['users_background_color']) ? '#fff' : $_POST['users_background_color'];

            $value = json_encode([
                'guests_id' => $_POST['guests_id'],
                'guests_content' => $_POST['guests_content'],
                'guests_text_color' => $_POST['guests_text_color'],
                'guests_background_color' => $_POST['guests_background_color'],
                'users_id' => $_POST['users_id'],
                'users_content' => $_POST['users_content'],
                'users_text_color' => $_POST['users_text_color'],
                'users_background_color' => $_POST['users_background_color'],
            ]);

            $this->update_settings('announcements', $value);
        }
    }

    public function email_notifications() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['emails'] = str_replace(' ', '', $_POST['emails']);
            $_POST['new_user'] = (bool) isset($_POST['new_user']);
            $_POST['delete_user'] = (bool) isset($_POST['delete_user']);
            $_POST['new_payment'] = (bool) isset($_POST['new_payment']);
            $_POST['new_domain'] = (bool) isset($_POST['new_domain']);
            $_POST['new_affiliate_withdrawal'] = (bool) isset($_POST['new_affiliate_withdrawal']);
            $_POST['contact'] = (bool) isset($_POST['contact']);

            $value = json_encode([
                'emails' => $_POST['emails'],
                'new_user' => $_POST['new_user'],
                'delete_user' => $_POST['delete_user'],
                'new_payment' => $_POST['new_payment'],
                'new_domain' => $_POST['new_domain'],
                'new_affiliate_withdrawal' => $_POST['new_affiliate_withdrawal'],
                'contact' => $_POST['contact'],
            ]);

            $this->update_settings('email_notifications', $value);
        }
    }

    public function webhooks() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['user_new'] = input_clean($_POST['user_new']);
            $_POST['user_delete'] = input_clean($_POST['user_delete']);
            $_POST['payment_new'] = input_clean($_POST['payment_new']);
            $_POST['code_redeemed'] = input_clean($_POST['code_redeemed']);
            $_POST['contact'] = input_clean($_POST['contact']);

            $value = json_encode([
                'user_new' => $_POST['user_new'],
                'user_delete' => $_POST['user_delete'],
                'payment_new' => $_POST['payment_new'],
                'code_redeemed' => $_POST['code_redeemed'],
                'contact' => $_POST['contact'],
            ]);

            $this->update_settings('webhooks', $value);
        }
    }

    public function offload() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Plugin::is_active('offload')) {
                redirect('admin/settings/offload');
            }

            /* :) */
            $_POST['assets_url'] = trim(input_clean($_POST['assets_url']));

            $value = json_encode([
                'assets_url' => $_POST['assets_url'],
                'provider' => $_POST['provider'],
                'endpoint_url' => $_POST['endpoint_url'],
                'uploads_url' => $_POST['uploads_url'],
                'access_key' => $_POST['access_key'],
                'secret_access_key' => $_POST['secret_access_key'],
                'storage_name' => $_POST['storage_name'],
                'region' => $_POST['region'],
            ]);

            $this->update_settings('offload', $value);
        }
    }

    public function cron() {
        /* Get the latest cronjob details */
        settings()->cron = json_decode(db()->where('`key`', 'cron')->getValue('settings', '`value`'));

        $this->process();
    }

    public function cache() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            \Altum\Cache::$adapter->clear();

            /* Set a nice success message */
            Alerts::add_success(l('global.success_message.update2'));

            /* Refresh the page */
            redirect('admin/settings/cache');
        }
    }

    public function license() {
        $this->process();

        if(!empty($_POST) && !empty($_POST['new_license'])) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            $altumcode_api = 'https://api.altumcode.com/validate';

            /* Make sure the license is correct */
            $response = \Unirest\Request::post($altumcode_api, [], [
                'type'              => 'update',
                'license'           => $_POST['new_license'],
                'url'               => url(),
                'product_key'       => PRODUCT_KEY,
                'product_name'      => PRODUCT_NAME,
                'product_version'   => PRODUCT_VERSION,
            ]);

            if($response->body->status == 'error') {
                Alerts::add_error($response->body->message);
            }

            /* Success check */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                if($response->body->status == 'success') {
                    /* Run external SQL if needed */
                    if(!empty($response->body->sql)) {
                        $dump = explode('-- SEPARATOR --', $response->body->sql);

                        foreach ($dump as $query) {
                            database()->query($query);
                        }
                    }

                    Alerts::add_success($response->body->message);

                    $this->after_update_settings('license');
                }
            }

            redirect('admin/settings/license');
        }
    }

    public function support() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            //$altumcode_api = 'http://127.0.0.1/altumcode-api/get-support-status';
            $altumcode_api = 'https://api.altumcode.com/get-support-status';

            /* Make sure the license is correct */
            $response = \Unirest\Request::post($altumcode_api, [], [
                'license'           => settings()->license->license,
                'url'               => url(),
                'product_key'       => PRODUCT_KEY,
                'product_name'      => PRODUCT_NAME,
                'product_version'   => PRODUCT_VERSION,
            ]);

            if($response->body->status == 'error') {
                Alerts::add_error($response->body->message);
            }

            /* Success check */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                if($response->body->status == 'success') {
                    /* Run external SQL if needed */
                    if(!empty($response->body->sql)) {
                        database()->query($response->body->sql);
                    }

                    Alerts::add_success($response->body->message);

                    $this->after_update_settings('support');
                }
            }

            redirect('admin/settings/support');
        }
    }

    public function links() {
        $this->process();

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['branding'] = trim($_POST['branding']);
            $_POST['biolinks_is_enabled'] = (bool) $_POST['biolinks_is_enabled'];
            $_POST['qr_codes_is_enabled'] = (bool) $_POST['qr_codes_is_enabled'];
            $_POST['shortener_is_enabled'] = (bool) $_POST['shortener_is_enabled'];
            $_POST['files_is_enabled'] = (bool) $_POST['files_is_enabled'];
            $_POST['vcards_is_enabled'] = (bool) $_POST['vcards_is_enabled'];
            $_POST['events_is_enabled'] = (bool) $_POST['events_is_enabled'];
            $_POST['directory_is_enabled'] = (bool) $_POST['directory_is_enabled'];
            $_POST['directory_display'] = in_array($_POST['directory_display'], ['all', 'verified']) ? $_POST['directory_display'] : 'all';
            $_POST['domains_is_enabled'] = (bool) $_POST['domains_is_enabled'];
            $_POST['main_domain_is_enabled'] = (bool) $_POST['main_domain_is_enabled'];
            $_POST['blacklisted_domains'] = implode(',', array_map('trim', explode(',', $_POST['blacklisted_domains'])));
            $_POST['blacklisted_keywords'] = implode(',', array_map('trim', explode(',', $_POST['blacklisted_keywords'])));
            $_POST['google_safe_browsing_is_enabled'] = (bool) $_POST['google_safe_browsing_is_enabled'];
            $_POST['google_static_maps_is_enabled'] = (bool) $_POST['google_static_maps_is_enabled'];
            $_POST['avatar_size_limit'] = $_POST['avatar_size_limit'] > get_max_upload() || $_POST['avatar_size_limit'] < 0 ? get_max_upload() : (float) $_POST['avatar_size_limit'];
            $_POST['background_size_limit'] = $_POST['background_size_limit'] > get_max_upload() || $_POST['background_size_limit'] < 0 ? get_max_upload() : (float) $_POST['background_size_limit'];
            $_POST['favicon_size_limit'] = $_POST['favicon_size_limit'] > get_max_upload() || $_POST['favicon_size_limit'] < 0 ? get_max_upload() : (float) $_POST['favicon_size_limit'];
            $_POST['seo_image_size_limit'] = $_POST['seo_image_size_limit'] > get_max_upload() || $_POST['seo_image_size_limit'] < 0 ? get_max_upload() : (float) $_POST['seo_image_size_limit'];
            $_POST['thumbnail_image_size_limit'] = $_POST['thumbnail_image_size_limit'] > get_max_upload() || $_POST['thumbnail_image_size_limit'] < 0 ? get_max_upload() : (float) $_POST['thumbnail_image_size_limit'];
            $_POST['image_size_limit'] = $_POST['image_size_limit'] > get_max_upload() || $_POST['image_size_limit'] < 0 ? get_max_upload() : (float) $_POST['image_size_limit'];
            $_POST['audio_size_limit'] = $_POST['audio_size_limit'] > get_max_upload() || $_POST['audio_size_limit'] < 0 ? get_max_upload() : (float) $_POST['audio_size_limit'];
            $_POST['video_size_limit'] = $_POST['video_size_limit'] > get_max_upload() || $_POST['video_size_limit'] < 0 ? get_max_upload() : (float) $_POST['video_size_limit'];
            $_POST['file_size_limit'] = $_POST['file_size_limit'] > get_max_upload() || $_POST['file_size_limit'] < 0 ? get_max_upload() : (float) $_POST['file_size_limit'];
            $_POST['product_file_size_limit'] = $_POST['product_file_size_limit'] > get_max_upload() || $_POST['product_file_size_limit'] < 0 ? get_max_upload() : (float) $_POST['product_file_size_limit'];

            $value = json_encode([
                'branding' => $_POST['branding'],
                'shortener_is_enabled' => $_POST['shortener_is_enabled'],
                'qr_codes_is_enabled' => $_POST['qr_codes_is_enabled'],
                'biolinks_is_enabled' => $_POST['biolinks_is_enabled'],
                'files_is_enabled' => $_POST['files_is_enabled'],
                'vcards_is_enabled' => $_POST['vcards_is_enabled'],
                'events_is_enabled' => $_POST['events_is_enabled'],
                'directory_is_enabled' => $_POST['directory_is_enabled'],
                'directory_display' => $_POST['directory_display'],
                'domains_is_enabled' => $_POST['domains_is_enabled'],
                'main_domain_is_enabled' => $_POST['main_domain_is_enabled'],
                'blacklisted_domains' => $_POST['blacklisted_domains'],
                'blacklisted_keywords' => $_POST['blacklisted_keywords'],
                'google_safe_browsing_is_enabled' => $_POST['google_safe_browsing_is_enabled'],
                'google_safe_browsing_api_key' => $_POST['google_safe_browsing_api_key'],
                'google_static_maps_is_enabled' => $_POST['google_static_maps_is_enabled'],
                'google_static_maps_api_key' => $_POST['google_static_maps_api_key'],
                'avatar_size_limit' => $_POST['avatar_size_limit'],
                'background_size_limit' => $_POST['background_size_limit'],
                'favicon_size_limit' => $_POST['favicon_size_limit'],
                'seo_image_size_limit' => $_POST['seo_image_size_limit'],
                'thumbnail_image_size_limit' => $_POST['thumbnail_image_size_limit'],
                'image_size_limit' => $_POST['image_size_limit'],
                'audio_size_limit' => $_POST['audio_size_limit'],
                'video_size_limit' => $_POST['video_size_limit'],
                'file_size_limit' => $_POST['file_size_limit'],
                'product_file_size_limit' => $_POST['product_file_size_limit'],
            ]);

            $this->update_settings('links', $value);
        }
    }

    public function tools() {
        $this->process();

        $tools = require APP_PATH . 'includes/tools.php';

        if(!empty($_POST)) {
            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* :) */
            $_POST['is_enabled'] = (bool) $_POST['is_enabled'];
            $_POST['access'] = in_array($_POST['access'], ['everyone', 'users']) ? $_POST['access'] : 'everyone';
            $available_tools = [];
            foreach($tools as $key => $value) {
                $available_tools[$key] = in_array($key, $_POST['available_tools']);
            }

            $value = json_encode([
                'is_enabled' => $_POST['is_enabled'],
                'access' => $_POST['access'],
                'available_tools' => $available_tools,
            ]);

            $this->update_settings('tools', $value);
        }
    }

    public function send_test_email() {

        if(empty($_POST)) {
            redirect('admin/settings/smtp');
        }

        /* Check for any errors */
        $required_fields = ['email'];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Alerts::add_field_error($field, l('global.error_message.empty_field'));
            }
        }

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        /* If there are no errors, continue */
        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            $result = send_mail($_POST['email'], settings()->main->title . ' - Test Email', 'This is just a test email to confirm that the smtp email settings are properly working!', null, [], true);

            if($result->ErrorInfo == '') {
                Alerts::add_success(l('admin_settings_send_test_email_modal.success_message'));
            } else {
                Alerts::add_error(sprintf(l('admin_settings_send_test_email_modal.error_message'), $result->ErrorInfo));
                Alerts::add_info(implode('<br />', $result->errors));
            }

        }

        redirect('admin/settings/smtp');
    }

}
