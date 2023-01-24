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

class AdminPlanCreate extends Controller {

    public function index() {

        if(in_array(settings()->license->type, ['Extended License', 'extended'])) {
            /* Get the available taxes from the system */
            $taxes = db()->get('taxes');

            /* Get the available codes from the system */
            $codes = db()->get('codes');
        }

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['name'] = input_clean($_POST['name']);
            $_POST['description'] = input_clean($_POST['description']);
            $_POST['monthly_price'] = (float) $_POST['monthly_price'];
            $_POST['annual_price'] = (float) $_POST['annual_price'];
            $_POST['lifetime_price'] = (float) $_POST['lifetime_price'];

            /* Determine the enabled biolink blocks */
            $enabled_biolink_blocks = [];

            foreach(require APP_PATH . 'includes/biolink_blocks.php' as $key => $value) {
                $enabled_biolink_blocks[$key] = (bool) isset($_POST['enabled_biolink_blocks']) && in_array($key, $_POST['enabled_biolink_blocks']);
            }

            $_POST['settings'] = json_encode([
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
            ]);

            $_POST['color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['color']) ? null : $_POST['color'];
            $_POST['status'] = (int) $_POST['status'];
            $_POST['order'] = (int) $_POST['order'];
            $_POST['trial_days'] = (int) $_POST['trial_days'];
            $_POST['taxes_ids'] = json_encode($_POST['taxes_ids'] ?? []);
            $_POST['codes_ids'] = json_encode($_POST['codes_ids'] ?? []);

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Check for any errors */
            $required_fields = ['name'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Database query */
                db()->insert('plans', [
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'monthly_price' => $_POST['monthly_price'],
                    'annual_price' => $_POST['annual_price'],
                    'lifetime_price' => $_POST['lifetime_price'],
                    'settings' => $_POST['settings'],
                    'taxes_ids' => $_POST['taxes_ids'],
                    'codes_ids' => $_POST['codes_ids'],
                    'color' => $_POST['color'],
                    'status' => $_POST['status'],
                    'order' => $_POST['order'],
                    'datetime' => \Altum\Date::$date,
                ]);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . $_POST['name'] . '</strong>'));

                redirect('admin/plans');
            }
        }


        /* Main View */
        $data = [
            'taxes' => $taxes ?? null,
            'codes' => $codes ?? null,
        ];

        $view = new \Altum\View('admin/plan-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
