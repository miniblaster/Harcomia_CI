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

class AdminPlanUpdate extends Controller {

    public function index() {

        $plan_id = isset($this->params[0]) ? $this->params[0] : null;

        /* Make sure it is either the trial / free plan or normal plans */
        switch($plan_id) {

            case 'free':
            case 'custom':

                /* Get the current settings for the free plan */
                $plan = settings()->{'plan_' . $plan_id};

                break;

            default:

                $plan_id = (int) $plan_id;

                /* Check if plan exists */
                if(!$plan = db()->where('plan_id', $plan_id)->getOne('plans')) {
                    redirect('admin/plans');
                }

                /* Parse the settings of the plan */
                $plan->settings = json_decode($plan->settings);

                /* Parse codes & taxes */
                $plan->taxes_ids = json_decode($plan->taxes_ids);
                $plan->codes_ids = json_decode($plan->codes_ids);

                if(in_array(settings()->license->type, ['Extended License', 'extended'])) {
                    /* Get the available taxes from the system */
                    $taxes = db()->get('taxes');

                    /* Get the available codes from the system */
                    $codes = db()->get('codes');
                }

                break;

        }

        if(!empty($_POST)) {

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            /* Determine the enabled biolink blocks */
            $enabled_biolink_blocks = [];

            foreach(require APP_PATH . 'includes/biolink_blocks.php' as $key => $value) {
                $enabled_biolink_blocks[$key] = (bool) isset($_POST['enabled_biolink_blocks']) && in_array($key, $_POST['enabled_biolink_blocks']);
            }

            /* Filter variables */
            $_POST['color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['color']) ? null : $_POST['color'];

            $_POST['settings'] = [
                'additional_global_domains' => (bool) isset($_POST['additional_global_domains']),
                'custom_url' => (bool) isset($_POST['custom_url']),
                'deep_links' => (bool) isset($_POST['deep_links']),
                'no_ads' => (bool) isset($_POST['no_ads']),
                'removable_branding' => (bool) isset($_POST['removable_branding']),
                'custom_branding' => (bool) isset($_POST['custom_branding']),
                'custom_colored_links' => (bool) isset($_POST['custom_colored_links']),
                'statistics' => (bool) isset($_POST['statistics']),
                'custom_backgrounds' => (bool) isset($_POST['custom_backgrounds']),
                'verified' => (bool) isset($_POST['verified']),
                'temporary_url_is_enabled' => (bool) isset($_POST['temporary_url_is_enabled']),
                'seo' => (bool) isset($_POST['seo']),
                'utm' => (bool) isset($_POST['utm']),
                'fonts' => (bool) isset($_POST['fonts']),
                'password' => (bool) isset($_POST['password']),
                'sensitive_content' => (bool) isset($_POST['sensitive_content']),
                'leap_link' => (bool) isset($_POST['leap_link']),
                'api_is_enabled' => (bool) isset($_POST['api_is_enabled']),
                'dofollow_is_enabled' => (bool) isset($_POST['dofollow_is_enabled']),
                'biolink_blocks_limit' => (int) $_POST['biolink_blocks_limit'],
                'projects_limit' => (int) $_POST['projects_limit'],
                'pixels_limit' => (int) $_POST['pixels_limit'],
                'qr_codes_limit' => (int) $_POST['qr_codes_limit'],
                'biolinks_limit' => (int) $_POST['biolinks_limit'],
                'links_limit' => (int) $_POST['links_limit'],
                'files_limit' => (int) $_POST['files_limit'],
                'vcards_limit' => (int) $_POST['vcards_limit'],
                'events_limit' => (int) $_POST['events_limit'],
                'domains_limit' => (int) $_POST['domains_limit'],
                'payment_processors_limit' => (int) $_POST['payment_processors_limit'],
                'teams_limit' => (int) $_POST['teams_limit'],
                'team_members_limit' => (int) $_POST['team_members_limit'],
                'affiliate_commission_percentage' => (int) $_POST['affiliate_commission_percentage'],
                'track_links_retention' => (int) $_POST['track_links_retention'],
                'custom_css_is_enabled' => (bool) isset($_POST['custom_css_is_enabled']),
                'custom_js_is_enabled' => (bool) isset($_POST['custom_js_is_enabled']),
                'enabled_biolink_blocks' => $enabled_biolink_blocks,
            ];

            switch($plan_id) {

                case 'free':

                    $_POST['name'] = input_clean($_POST['name']);
                    $_POST['description'] = input_clean($_POST['description']);
                    $_POST['price'] = input_clean($_POST['price']);
                    $_POST['status'] = (int) $_POST['status'];

                    /* Check for any errors */
                    $required_fields = ['name', 'price'];
                    foreach($required_fields as $field) {
                        if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                            Alerts::add_field_error($field, l('global.error_message.empty_field'));
                        }
                    }

                    /* Make sure to not let the admin disable ALL the plans */
                    if(!$_POST['status']) {

                        $enabled_plans = (int) settings()->payment->is_enabled ? database()->query("SELECT COUNT(*) AS `total` FROM `plans` WHERE `status` <> 0")->fetch_object()->total ?? 0 : 0;

                        if(!$enabled_plans) {
                            Alerts::add_error(l('admin_plan_update.error_message.disabled_plans'));
                        }
                    }

                    $setting_key = 'plan_free';
                    $setting_value = json_encode([
                        'plan_id' => 'free',
                        'name' => $_POST['name'],
                        'description' => $_POST['description'],
                        'price' => $_POST['price'],
                        'color' => $_POST['color'],
                        'status' => $_POST['status'],
                        'settings' => $_POST['settings']
                    ]);

                    break;

                case 'custom':

                    $_POST['name'] = input_clean($_POST['name']);
                    $_POST['description'] = input_clean($_POST['description']);
                    $_POST['price'] = input_clean($_POST['price']);
                    $_POST['custom_button_url'] = input_clean($_POST['custom_button_url']);
                    $_POST['status'] = (int) $_POST['status'];

                    /* Check for any errors */
                    $required_fields = ['name', 'price', 'custom_button_url'];
                    foreach($required_fields as $field) {
                        if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                            Alerts::add_field_error($field, l('global.error_message.empty_field'));
                        }
                    }

                    $setting_key = 'plan_custom';
                    $setting_value = json_encode([
                        'plan_id' => 'custom',
                        'name' => $_POST['name'],
                        'description' => $_POST['description'],
                        'price' => $_POST['price'],
                        'custom_button_url' => $_POST['custom_button_url'],
                        'color' => $_POST['color'],
                        'status' => $_POST['status'],
                        'settings' => $_POST['settings']
                    ]);

                    break;

                default:

                    $_POST['name'] = input_clean($_POST['name']);
                    $_POST['description'] = input_clean($_POST['description']);
                    $_POST['monthly_price'] = (float) $_POST['monthly_price'];
                    $_POST['annual_price'] = (float) $_POST['annual_price'];
                    $_POST['lifetime_price'] = (float) $_POST['lifetime_price'];
                    $_POST['trial_days'] = (int) $_POST['trial_days'];
                    $_POST['status'] = (int) $_POST['status'];
                    $_POST['order'] = (int) $_POST['order'];
                    $_POST['taxes_ids'] = json_encode($_POST['taxes_ids'] ?? []);
                    $_POST['codes_ids'] = json_encode($_POST['codes_ids'] ?? []);

                    /* Check for any errors */
                    $required_fields = ['name'];
                    foreach($required_fields as $field) {
                        if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                            Alerts::add_field_error($field, l('global.error_message.empty_field'));
                        }
                    }

                    /* Make sure to not let the admin disable ALL the plans */
                    if(!$_POST['status']) {

                        $enabled_plans = (int) database()->query("SELECT COUNT(*) AS `total` FROM `plans` WHERE `status` <> 0")->fetch_object()->total ?? 0;

                        if(
                            (
                                !$enabled_plans ||
                                ($enabled_plans == 1 && $plan->status))
                            && !settings()->plan_free->status
                        ) {
                            Alerts::add_error(l('admin_plan_update.error_message.disabled_plans'));
                        }
                    }

                    break;

            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Update the plan in database */
                switch ($plan_id) {

                    case 'free':
                    case 'custom':

                        db()->where('`key`', $setting_key)->update('settings', ['value' => $setting_value]);

                        /* Clear the cache */
                        \Altum\Cache::$adapter->deleteItem('settings');

                        break;

                    default:

                        $settings = json_encode($_POST['settings']);

                        db()->where('plan_id', $plan_id)->update('plans', [
                            'name' => $_POST['name'],
                            'description' => $_POST['description'],
                            'monthly_price' => $_POST['monthly_price'],
                            'annual_price' => $_POST['annual_price'],
                            'lifetime_price' => $_POST['lifetime_price'],
                            'trial_days' => $_POST['trial_days'],
                            'settings' => $settings,
                            'taxes_ids' => $_POST['taxes_ids'],
                            'codes_ids' => $_POST['codes_ids'],
                            'color' => $_POST['color'],
                            'status' => $_POST['status'],
                            'order' => $_POST['order'],
                        ]);

                        break;

                }

                /* Update all users plan settings with these ones */
                if(isset($_POST['submit_update_users_plan_settings'])) {

                    $plan_settings = json_encode($_POST['settings']);

                    db()->where('plan_id', $plan_id)->update('users', ['plan_settings' => $plan_settings]);

                    /* Clear the cache */
                    \Altum\Cache::$adapter->deleteItemsByTag('users');

                }

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $plan->name . '</strong>'));

                /* Refresh the page */
                redirect('admin/plan-update/' . $plan_id);

            }

        }

        /* Main View */
        $data = [
            'plan_id' => $plan_id,
            'plan' => $plan,
            'taxes' => $taxes ?? null,
            'codes' => $codes ?? null,
        ];

        $view = new \Altum\View('admin/plan-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
