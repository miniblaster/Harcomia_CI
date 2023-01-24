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
use Altum\Date;
use Altum\Models\BiolinkTheme;
use Altum\Response;


class LinkAjax extends Controller {
    public $links_types = null;

    public function index() {
        \Altum\Authentication::guard();

        if(!empty($_POST) && (\Altum\Csrf::check('token') || \Altum\Csrf::check('global_token')) && isset($_POST['request_type'])) {

            $this->links_types = require APP_PATH . 'includes/links_types.php';

            switch($_POST['request_type']) {

                /* Status toggle */
                case 'is_enabled_toggle': $this->is_enabled_toggle(); break;

                /* Create */
                case 'create': $this->create(); break;

                /* Update */
                case 'update': $this->update(); break;

                /* Delete */
                case 'delete': $this->delete(); break;

                /* Duplicate */
                case 'duplicate': $this->duplicate(); break;

            }

        }

        die($_POST['request_type']);
    }

    private function is_enabled_toggle() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $_POST['link_id'] = (int) $_POST['link_id'];

        /* Get the current status */
        $link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links', ['link_id', 'is_enabled']);

        if($link) {
            $new_is_enabled = (int) !$link->is_enabled;

            db()->where('link_id', $link->link_id)->update('links', ['is_enabled' => $new_is_enabled]);

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);
            \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $_POST['link_id']);

            Response::json(l('global.success_message.create2'), 'success');
        }
    }

    private function create() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $_POST['type'] = trim(query_clean($_POST['type']));

        /* Check for possible errors */
        if(!array_key_exists($_POST['type'], $this->links_types)) {
            die();
        }

        $this->{'create_' . $_POST['type']}();

    }

    private function create_link() {
        if(!settings()->links->shortener_is_enabled) {
            Response::json(l('global.error_message.basic'), 'error');
        }

        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['url'] = !empty($_POST['url']) ? get_slug($_POST['url'], '-', false) : false;
        $_POST['sensitive_content'] = (bool) isset($_POST['sensitive_content']);

        if(empty($_POST['domain_id']) && !settings()->links->main_domain_is_enabled && !\Altum\Authentication::is_admin()) {
            Response::json(l('create_link_modal.error_message.main_domain_is_disabled'), 'error');
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id'] ?? false);

        if(empty($_POST['location_url'])) {
            Response::json(l('global.error_message.empty_fields'), 'error');
        }

        $this->check_url($_POST['url']);

        $this->check_location_url($_POST['location_url']);

        /* Make sure that the user didn't exceed the limit */
        $user_total_links = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'link'")->fetch_object()->total;
        if($this->user->plan_settings->links_limit != -1 && $user_total_links >= $this->user->plan_settings->links_limit) {
            Response::json(l('global.info_message.plan_feature_limit'), 'error');
        }

        /* Check for duplicate url if needed */
        if($_POST['url']) {
            if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                Response::json(l('link.error_message.url_exists'), 'error');
            }

            $url = $_POST['url'];
        } else {
            $url = mb_strtolower(string_generate(10));

            /* Generate random url if not specified */
            while(db()->where('url', $url)->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                $url = mb_strtolower(string_generate(10));
            }
        }

        $settings = json_encode([
            'clicks_limit' => null,
            'expiration_url' => null,
            'password' => null,
            'sensitive_content' => false,
            'targeting_type' => null,
        ]);

        /* Insert to database */
        $link_id = db()->insert('links', [
            'user_id' => $this->user->user_id,
            'domain_id' => $domain_id,
            'type' => 'link',
            'url' => $url,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        Response::json(l('global.success_message.create2'), 'success', ['url' => url('link/' . $link_id)]);
    }

    private function create_biolink() {
        if(!settings()->links->biolinks_is_enabled) {
            Response::json(l('global.error_message.basic'), 'error');
        }

        $_POST['url'] = !empty($_POST['url']) ? get_slug($_POST['url'], '-', false) : false;

        if(empty($_POST['domain_id']) && !settings()->links->main_domain_is_enabled && !\Altum\Authentication::is_admin()) {
            Response::json(l('create_link_modal.error_message.main_domain_is_disabled'), 'error');
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id'] ?? false);

        /* Make sure that the user didn't exceed the limit */
        $user_total_biolinks = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'biolink'")->fetch_object()->total;
        if($this->user->plan_settings->biolinks_limit != -1 && $user_total_biolinks >= $this->user->plan_settings->biolinks_limit) {
            Response::json(l('global.info_message.plan_feature_limit'), 'error');
        }

        /* Check for duplicate url if needed */
        if($_POST['url']) {
            if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                Response::json(l('link.error_message.url_exists'), 'error');
            }
        }

        /* Start the creation process */
        $url = $_POST['url'] ? $_POST['url'] : mb_strtolower(string_generate(10));
        $type = 'biolink';
        $settings = json_encode([
            'verified_location' => 'top',
            'favicon' => null,
            'background_type' => 'preset',
            'background' => 'one',
            'text_color' => 'white',
            'display_branding' => true,
            'branding' => [
                'url' => '',
                'name' => ''
            ],
            'seo' => [
                'block' => false,
                'title' => '',
                'meta_description' => '',
                'image' => '',
            ],
            'utm' => [
                'medium' => '',
                'source' => '',
            ],
            'font' => null,
            'font_size' => 16,
            'password' => null,
            'sensitive_content' => false,
            'leap_link' => null,
            'custom_css' => null,
            'custom_js' => null,
        ]);

        /* Generate random url if not specified */
        while(db()->where('url', $url)->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
            $url = mb_strtolower(string_generate(10));
        }

        $this->check_url($_POST['url']);

        /* Insert to database */
        $link_id = db()->insert('links', [
            'user_id' => $this->user->user_id,
            'domain_id' => $domain_id,
            'type' => $type,
            'url' => $url,
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        Response::json(l('global.success_message.create2'), 'success', ['url' => url('link/' . $link_id)]);
    }

    private function create_file() {
        if(!settings()->links->files_is_enabled) {
            Response::json(l('global.error_message.basic'), 'error');
        }

        $_POST['url'] = !empty($_POST['url']) ? get_slug($_POST['url'], '-', false) : null;

        if(empty($_POST['domain_id']) && !settings()->links->main_domain_is_enabled && !\Altum\Authentication::is_admin()) {
            Response::json(l('create_link_modal.error_message.main_domain_is_disabled'), 'error');
        }

        /* Make sure that the user didn't exceed the limit */
        $user_total_files = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'file'")->fetch_object()->total;
        if($this->user->plan_settings->files_limit != -1 && $user_total_files >= $this->user->plan_settings->files_limit) {
            Response::json(l('global.info_message.plan_feature_limit'), 'error');
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id'] ?? false);

        /* File upload */
        $db_file = \Altum\Uploads::process_upload(null, 'files', 'file', 'file_remove', settings()->links->file_size_limit, 'json_error');

        /* Check for duplicate url if needed */
        if($_POST['url']) {
            if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                Response::json(l('link.error_message.url_exists'), 'error');
            }
        }

        /* Start the creation process */
        $url = $_POST['url'] ?? mb_strtolower(string_generate(10));
        $type = 'file';
        $settings = json_encode([
            'file' => $db_file,
            'password' => null,
            'sensitive_content' => false,
            'clicks_limit' => null,
            'expiration_url' => null,
        ]);

        /* Generate random url if not specified */
        while(db()->where('url', $url)->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
            $url = mb_strtolower(string_generate(10));
        }

        $this->check_url($_POST['url']);

        /* Insert to database */
        $link_id = db()->insert('links', [
            'user_id' => $this->user->user_id,
            'domain_id' => $domain_id,
            'type' => $type,
            'url' => $url,
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        Response::json(l('global.success_message.create2'), 'success', ['url' => url('link/' . $link_id)]);
    }

    private function create_vcard() {
        if(!settings()->links->vcards_is_enabled) {
            Response::json(l('global.error_message.basic'), 'error');
        }

        $_POST['url'] = !empty($_POST['url']) ? get_slug($_POST['url'], '-', false) : null;

        if(empty($_POST['domain_id']) && !settings()->links->main_domain_is_enabled && !\Altum\Authentication::is_admin()) {
            Response::json(l('create_link_modal.error_message.main_domain_is_disabled'), 'error');
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id'] ?? false);

        /* Make sure that the user didn't exceed the limit */
        $user_total_vcards = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'vcard'")->fetch_object()->total;
        if($this->user->plan_settings->vcards_limit != -1 && $user_total_vcards >= $this->user->plan_settings->vcards_limit) {
            Response::json(l('global.info_message.plan_feature_limit'), 'error');
        }

        /* Check for duplicate url if needed */
        if($_POST['url']) {
            if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                Response::json(l('link.error_message.url_exists'), 'error');
            }
        }

        /* Start the creation process */
        $url = $_POST['url'] ?? mb_strtolower(string_generate(10));
        $type = 'vcard';
        $settings = json_encode([
            'password' => null,
            'sensitive_content' => false,
            'clicks_limit' => null,
            'expiration_url' => null,
            'vcard_avatar' => null,
            'vcard_first_name' => null,
            'vcard_last_name' => null,
            'vcard_email' => null,
            'vcard_url' => null,
            'vcard_company' => null,
            'vcard_job_title' => null,
            'vcard_birthday' => null,
            'vcard_street' => null,
            'vcard_city' => null,
            'vcard_zip' => null,
            'vcard_region' => null,
            'vcard_country' => null,
            'vcard_note' => null,
            'vcard_socials' => [],
            'vcard_phone_numbers' => [],
        ]);

        /* Generate random url if not specified */
        while(db()->where('url', $url)->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
            $url = mb_strtolower(string_generate(10));
        }

        $this->check_url($_POST['url']);

        /* Insert to database */
        $link_id = db()->insert('links', [
            'user_id' => $this->user->user_id,
            'domain_id' => $domain_id,
            'type' => $type,
            'url' => $url,
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        Response::json(l('global.success_message.create2'), 'success', ['url' => url('link/' . $link_id)]);
    }

    private function create_event() {
        if(!settings()->links->events_is_enabled) {
            Response::json(l('global.error_message.basic'), 'error');
        }

        $_POST['url'] = !empty($_POST['url']) ? get_slug($_POST['url'], '-', false) : null;

        if(empty($_POST['domain_id']) && !settings()->links->main_domain_is_enabled && !\Altum\Authentication::is_admin()) {
            Response::json(l('create_link_modal.error_message.main_domain_is_disabled'), 'error');
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id'] ?? false);

        /* Make sure that the user didn't exceed the limit */
        $user_total_events = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'event'")->fetch_object()->total;
        if($this->user->plan_settings->events_limit != -1 && $user_total_events >= $this->user->plan_settings->events_limit) {
            Response::json(l('global.info_message.plan_feature_limit'), 'error');
        }

        /* Check for duplicate url if needed */
        if($_POST['url']) {
            if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                Response::json(l('link.error_message.url_exists'), 'error');
            }
        }

        /* Start the creation process */
        $url = $_POST['url'] ?? mb_strtolower(string_generate(10));
        $type = 'event';
        $settings = json_encode([
            'password' => null,
            'sensitive_content' => false,
            'clicks_limit' => null,
            'expiration_url' => null,
            'event_name' => null,
            'event_note' => null,
            'event_url' => null,
            'event_location' => null,
            'event_start_datetime' => null,
            'event_end_datetime' => null,
            'event_timezone' => $this->user->timezone,
        ]);

        /* Generate random url if not specified */
        while(db()->where('url', $url)->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
            $url = mb_strtolower(string_generate(10));
        }

        $this->check_url($_POST['url']);

        /* Insert to database */
        $link_id = db()->insert('links', [
            'user_id' => $this->user->user_id,
            'domain_id' => $domain_id,
            'type' => $type,
            'url' => $url,
            'settings' => $settings,
            'datetime' => \Altum\Date::$date,
        ]);

        Response::json(l('global.success_message.create2'), 'success', ['url' => url('link/' . $link_id)]);
    }

    private function update() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        if(empty($_POST)) {
            die();
        }

        /* Check for possible errors */
        if(!array_key_exists($_POST['type'], $this->links_types)) {
            die();
        }

        $this->{'update_' . $_POST['type']}();

    }

    private function update_link() {
        if(!settings()->links->shortener_is_enabled) {
            Response::json(l('global.error_message.basic'), 'error');
        }

        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['project_id'] = empty($_POST['project_id']) ? null : (int) $_POST['project_id'];
        $_POST['url'] = !empty($_POST['url']) ? get_slug($_POST['url'], '-', false) : false;
        $_POST['location_url'] = get_url($_POST['location_url']);
        if(isset($_POST['schedule']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && Date::validate($_POST['start_date'], 'Y-m-d H:i:s') && Date::validate($_POST['end_date'], 'Y-m-d H:i:s')) {
            $_POST['start_date'] = (new \DateTime($_POST['start_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
            $_POST['end_date'] = (new \DateTime($_POST['end_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        } else {
            $_POST['start_date'] = $_POST['end_date'] = null;
        }
        $_POST['expiration_url'] = get_url($_POST['expiration_url']);
        $_POST['clicks_limit'] = empty($_POST['clicks_limit']) ? null : (int) $_POST['clicks_limit'];
        $this->check_location_url($_POST['expiration_url'], true);
        $_POST['sensitive_content'] = (bool) isset($_POST['sensitive_content']);

        if(empty($_POST['domain_id']) && !settings()->links->main_domain_is_enabled && !\Altum\Authentication::is_admin()) {
            Response::json(l('create_link_modal.error_message.main_domain_is_disabled'), 'error');
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id'] ?? false);

        /* Existing pixels */
        $pixels = (new \Altum\Models\Pixel())->get_pixels($this->user->user_id);
        $_POST['pixels_ids'] = isset($_POST['pixels_ids']) ? array_map(
            function($pixel_id) {
                return (int) $pixel_id;
            },
            array_filter($_POST['pixels_ids'], function($pixel_id) use($pixels) {
                return array_key_exists($pixel_id, $pixels);
            })
        ) : [];
        $_POST['pixels_ids'] = json_encode($_POST['pixels_ids']);

        /* Check for any errors */
        $required_fields = ['location_url'];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        $this->check_url($_POST['url']);

        $this->check_location_url($_POST['location_url']);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        if($_POST['project_id'] && !$project = db()->where('project_id', $_POST['project_id'])->where('user_id', $this->user->user_id)->getOne('projects', ['project_id'])) {
            die();
        }

        $link->settings = json_decode($link->settings);

        /* Check for a password set */
        $_POST['password'] = !empty($_POST['qweasdzxc']) ?
            ($_POST['qweasdzxc'] != $link->settings->password ? password_hash($_POST['qweasdzxc'], PASSWORD_DEFAULT) : $link->settings->password)
            : null;


        /* Check for duplicate url if needed */
        if($_POST['url'] && ($_POST['url'] != $link->url || $domain_id != $link->domain_id)) {

            if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                Response::json(l('link.error_message.url_exists'), 'error');
            }

        }

        $url = $_POST['url'];

        if(empty($_POST['url'])) {
            /* Generate random url if not specified */
            $url = mb_strtolower(string_generate(10));

            while(db()->where('url', $url)->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                $url = mb_strtolower(string_generate(10));
            }

        }

        /* Prepare the settings */
        $targeting_types = ['country_code', 'device_type', 'browser_language', 'rotation', 'os_name'];
        $_POST['targeting_type'] = in_array($_POST['targeting_type'], array_merge(['false'], $targeting_types)) ? query_clean($_POST['targeting_type']) : 'false';

        $settings = [
            'clicks_limit' => $_POST['clicks_limit'],
            'expiration_url' => $_POST['expiration_url'],
            'password' => $_POST['password'],
            'sensitive_content' => $_POST['sensitive_content'],
            'targeting_type' => $_POST['targeting_type'],
        ];

        /* Process the targeting */
        foreach($targeting_types as $targeting_type) {
            ${'targeting_' . $targeting_type} = [];

            if(isset($_POST['targeting_' . $targeting_type . '_key'])) {
                foreach ($_POST['targeting_' . $targeting_type . '_key'] as $key => $value) {
                    if (empty(trim($value))) continue;

                    ${'targeting_' . $targeting_type}[] = [
                        'key' => trim(query_clean($value)),
                        'value' => get_url($_POST['targeting_' . $targeting_type . '_value'][$key]),
                    ];
                }

                $settings['targeting_' . $targeting_type] = ${'targeting_' . $targeting_type};
            }
        }

        $settings = json_encode($settings);

        db()->where('link_id', $_POST['link_id'])->update('links', [
            'project_id' => $_POST['project_id'],
            'domain_id' => $domain_id,
            'pixels_ids' => $_POST['pixels_ids'],
            'url' => $url,
            'location_url' => $_POST['location_url'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'settings' => $settings,
            'last_datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $link->link_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $link->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['url' => $url]);
    }

    private function update_biolink() {
        if(!settings()->links->biolinks_is_enabled) {
            Response::json(l('global.error_message.basic'), 'error');
        }

        $_POST['project_id'] = empty($_POST['project_id']) ? null : (int) $_POST['project_id'];
        $_POST['url'] = !empty($_POST['url']) ? get_slug($_POST['url'], '-', false) : false;

        if(empty($_POST['domain_id']) && !settings()->links->main_domain_is_enabled && !\Altum\Authentication::is_admin()) {
            Response::json(l('create_biolink_modal.error_message.main_domain_is_disabled'), 'error');
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id'] ?? false);

        /* Check for any errors */
        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        if($_POST['project_id'] && !$project = db()->where('project_id', $_POST['project_id'])->where('user_id', $this->user->user_id)->getOne('projects', ['project_id'])) {
            die();
        }

        $link->settings = json_decode($link->settings);

        /* Get available themes */
        $biolinks_themes = (new BiolinkTheme())->get_biolinks_themes();
        $_POST['biolink_theme_id'] = isset($_POST['biolink_theme_id']) && array_key_exists($_POST['biolink_theme_id'], $biolinks_themes) ? $_POST['biolink_theme_id'] : null;

        /* Existing pixels */
        $pixels = (new \Altum\Models\Pixel())->get_pixels($this->user->user_id);
        $_POST['pixels_ids'] = isset($_POST['pixels_ids']) ? array_map(
            function($pixel_id) {
                return (int) $pixel_id;
            },
            array_filter($_POST['pixels_ids'], function($pixel_id) use($pixels) {
                return array_key_exists($pixel_id, $pixels);
            })
        ) : [];
        $_POST['pixels_ids'] = json_encode($_POST['pixels_ids']);

        if($_POST['url'] == $link->url) {
            $url = $link->url;

            if($link->domain_id != $domain_id) {
                if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                    Response::json(l('link.error_message.url_exists'), 'error');
                }
            }

        } else {
            $url = $_POST['url'] ? $_POST['url'] : mb_strtolower(string_generate(10));

            if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                Response::json(l('link.error_message.url_exists'), 'error');
            }

            /* Generate random url if not specified */
            while(db()->where('url', $url)->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                $url = mb_strtolower(string_generate(10));
            }

            $this->check_url($_POST['url']);
        }

        /* Image uploads */
        $image_allowed_extensions = [
            'seo_image' => \Altum\Uploads::get_whitelisted_file_extensions('biolink_seo_image'),
            'favicon' => \Altum\Uploads::get_whitelisted_file_extensions('biolink_favicon'),
            'background' => \Altum\Uploads::get_whitelisted_file_extensions('biolink_background'),
        ];
        $image = [
            'seo_image' => !empty($_FILES['seo_image']['name']) && !isset($_POST['seo_image_remove']),
            'favicon' => !empty($_FILES['favicon']['name']) && !isset($_POST['favicon_remove']),
            'background' => !empty($_FILES['background']['name']) && !isset($_POST['background_remove']),
        ];
        $image_upload_path = [
            'seo_image' => \Altum\Uploads::get_path('biolink_seo_image'),
            'favicon' => \Altum\Uploads::get_path('biolink_favicon'),
            'background' => \Altum\Uploads::get_path('biolink_background'),
        ];
        $image_uploaded_file = [
            'seo_image' => $link->settings->seo->image,
            'favicon' => $link->settings->favicon,
        ];
        $image_url = [
            'seo_image' => null,
            'favicon' => null,
            'background' => null,
        ];

        foreach(['favicon', 'seo_image'] as $image_key) {
            if($image[$image_key]) {
                $file_name = $_FILES[$image_key]['name'];
                $file_extension = explode('.', $file_name);
                $file_extension = mb_strtolower(end($file_extension));
                $file_temp = $_FILES[$image_key]['tmp_name'];

                if($_FILES[$image_key]['error'] == UPLOAD_ERR_INI_SIZE) {
                    Response::json(sprintf(l('global.error_message.file_size_limit'), settings()->links->{$image_key . '_size_limit'}), 'error');
                }

                if($_FILES[$file_name]['error'] && $_FILES[$file_name]['error'] != UPLOAD_ERR_INI_SIZE) {
                    Response::json(l('global.error_message.file_upload'), 'error');
                }

                if(!in_array($file_extension, $image_allowed_extensions[$image_key])) {
                    Response::json(l('global.error_message.invalid_file_type'), 'error');
                }

                if(!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {
                    if(!is_writable(UPLOADS_PATH . $image_upload_path[$image_key])) {
                        Response::json(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . $image_upload_path[$image_key]), 'error');
                    }
                }

                if($_FILES[$image_key]['size'] > settings()->links->{$image_key . '_size_limit'} * 1000000) {
                    Response::json(sprintf(l('global.error_message.file_size_limit'), settings()->links->{$image_key . '_size_limit'}), 'error');
                }

                /* Generate new name for image */
                $image_new_name = md5(time() . rand()) . '.' . $file_extension;

                /* Try to compress the image */
                if(\Altum\Plugin::is_active('image-optimizer')) {
                    \Altum\Plugin\ImageOptimizer::optimize($file_temp, $image_new_name);
                }

                /* Offload uploading */
                if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                    try {
                        $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                        /* Delete current image */
                        $s3->deleteObject([
                            'Bucket' => settings()->offload->storage_name,
                            'Key' => 'uploads/' . $image_upload_path[$image_key] . $image_uploaded_file[$image_key],
                        ]);

                        /* Upload image */
                        $result = $s3->putObject([
                            'Bucket' => settings()->offload->storage_name,
                            'Key' => 'uploads/' . $image_upload_path[$image_key] . $image_new_name,
                            'ContentType' => mime_content_type($file_temp),
                            'SourceFile' => $file_temp,
                            'ACL' => 'public-read'
                        ]);
                    } catch (\Exception $exception) {
                        Response::json($exception->getMessage(), 'error');
                    }
                }

                /* Local uploading */
                else {
                    /* Delete current image */
                    if(!empty($image_uploaded_file[$image_key]) && file_exists(UPLOADS_PATH . $image_upload_path[$image_key] . $image_uploaded_file[$image_key])) {
                        unlink(UPLOADS_PATH . $image_upload_path[$image_key] . $image_uploaded_file[$image_key]);
                    }

                    /* Upload the original */
                    move_uploaded_file($file_temp, UPLOADS_PATH . $image_upload_path[$image_key] . $image_new_name);
                }

                $image_uploaded_file[$image_key] = $image_new_name;
            }

            /* Check for the removal of the already uploaded file */
            if(isset($_POST[$image_key . '_remove'])) {

                /* Offload deleting */
                if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                    $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                    $s3->deleteObject([
                        'Bucket' => settings()->offload->storage_name,
                        'Key' => 'uploads/' . $image_upload_path[$image_key] . $image_uploaded_file[$image_key],
                    ]);
                }

                /* Local deleting */
                else {
                    /* Delete current file */
                    if(!empty($image_uploaded_file[$image_key]) && file_exists(UPLOADS_PATH . $image_upload_path[$image_key] . $image_uploaded_file[$image_key])) {
                        unlink(UPLOADS_PATH . $image_upload_path[$image_key] . $image_uploaded_file[$image_key]);
                    }
                }

                $image_uploaded_file[$image_key] = null;
            }

            $image_url[$image_key] = $image_uploaded_file[$image_key] ? UPLOADS_FULL_URL . $image_upload_path[$image_key] . $image_uploaded_file[$image_key] : null;
        }

        $biolink_backgrounds = require APP_PATH . 'includes/biolink_backgrounds.php';
        $_POST['background_type'] = array_key_exists($_POST['background_type'], $biolink_backgrounds) ? $_POST['background_type'] : 'preset';

        switch($_POST['background_type']) {
            case 'preset':
            case 'preset_abstract':
                $background = array_key_exists($_POST['background'], $biolink_backgrounds[$_POST['background_type']]) ? $_POST['background'] : 'one';
                break;

            case 'color':

                $background = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background']) ? '#000' : $_POST['background'];

                break;

            case 'gradient':

                $background_color_one = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color_one']) ? '#000' : $_POST['background_color_one'];
                $background_color_two = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color_two']) ? '#000' : $_POST['background_color_two'];

                break;

            case 'image':

                /* Background processing */
                if($image['background']) {
                    $background_file_extension = explode('.', $_FILES['background']['name']);
                    $background_file_extension = mb_strtolower(end($background_file_extension));
                    $background_file_temp = $_FILES['background']['tmp_name'];

                    if($_FILES['background']['error'] == UPLOAD_ERR_INI_SIZE) {
                        Response::json(sprintf(l('global.error_message.file_size_limit'), settings()->links->background_size_limit), 'error');
                    }

                    if($_FILES['background']['error'] && $_FILES['background']['error'] != UPLOAD_ERR_INI_SIZE) {
                        Response::json(l('global.error_message.file_upload'), 'error');
                    }

                    if(!is_writable(UPLOADS_PATH . $image_upload_path['background'])) {
                        Response::json(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . $image_upload_path['background']), 'error');
                    }

                    if(!in_array($background_file_extension, $image_allowed_extensions['background'])) {
                        Response::json(l('global.error_message.invalid_file_type'), 'error');
                    }

                    if($_FILES['background']['size'] > settings()->links->background_size_limit * 1000000) {
                        Response::json(sprintf(l('global.error_message.file_size_limit'), settings()->links->background_size_limit), 'error');
                    }

                    /* Generate new name */
                    $background_new_name = md5(time() . rand()) . '.' . $background_file_extension;

                    /* Try to compress the image */
                    if(\Altum\Plugin::is_active('image-optimizer')) {
                        \Altum\Plugin\ImageOptimizer::optimize($background_file_temp, $background_new_name);
                    }

                    /* Offload uploading */
                    if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                        try {
                            $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                            /* Delete current image */
                            if(!$link->biolink_theme_id && is_string($link->settings->background)) {
                                $s3->deleteObject([
                                    'Bucket' => settings()->offload->storage_name,
                                    'Key' => 'uploads/backgrounds/' . $link->settings->background,
                                ]);
                            }

                            /* Upload image */
                            $result = $s3->putObject([
                                'Bucket' => settings()->offload->storage_name,
                                'Key' => 'uploads/backgrounds/' . $background_new_name,
                                'ContentType' => mime_content_type($background_file_temp),
                                'SourceFile' => $background_file_temp,
                                'ACL' => 'public-read'
                            ]);
                        } catch (\Exception $exception) {
                            Response::json($exception->getMessage(), 'error');
                        }
                    }

                    /* Local uploading */
                    else {
                        /* Delete current file */
                        if(!$link->biolink_theme_id && is_string($link->settings->background) && !empty($link->settings->background) && file_exists(UPLOADS_PATH . $image_upload_path['background'] . $link->settings->background)) {
                            unlink(UPLOADS_PATH . $image_upload_path['background'] . $link->settings->background);
                        }

                        /* Upload the original */
                        move_uploaded_file($background_file_temp, UPLOADS_PATH . $image_upload_path['background'] . $background_new_name);
                    }

                    $background = $background_new_name;
                }

                break;
        }

        /* Delete existing background file if needed */
        if(
            $link->settings->background_type == 'image'
            && ($image['background'] || ($_POST['biolink_theme_id'] && $link->biolink_theme_id != $_POST['biolink_theme_id']) || $_POST['background_type'] != $link->settings->background_type)
            && is_string($link->settings->background)
            && (!$link->biolink_theme_id)
        )
        {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/backgrounds/' . $link->settings->background,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($link->settings->background) && file_exists(UPLOADS_PATH . $image_upload_path['background'] . $link->settings->background)) {
                    unlink(UPLOADS_PATH . $image_upload_path['background'] . $link->settings->background);
                }
            }
        }

        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#fff' : $_POST['text_color'];
        $_POST['display_branding'] = (bool) isset($_POST['display_branding']);
        $_POST['verified_location'] = in_array($_POST['verified_location'], ['top', 'bottom']) ? query_clean($_POST['verified_location']) : 'top';
        $_POST['branding_name'] = mb_substr(trim(query_clean($_POST['branding_name'])), 0, 128);
        $_POST['branding_url'] = get_url($_POST['branding_url']);
        $_POST['seo_block'] = (bool) isset($_POST['seo_block']);
        $_POST['seo_title'] = trim(query_clean(mb_substr($_POST['seo_title'], 0, 70)));
        $_POST['seo_meta_description'] = trim(query_clean(mb_substr($_POST['seo_meta_description'], 0, 160)));
        $_POST['utm_medium'] = mb_substr(trim(query_clean($_POST['utm_medium'])), 0, 128);
        $_POST['utm_source'] = mb_substr(trim(query_clean($_POST['utm_source'])), 0, 128);
        $_POST['password'] = !empty($_POST['qweasdzxc']) ?
            ($_POST['qweasdzxc'] != $link->settings->password ? password_hash($_POST['qweasdzxc'], PASSWORD_DEFAULT) : $link->settings->password)
            : null;
        $_POST['sensitive_content'] = (bool) isset($_POST['sensitive_content']);
        $_POST['custom_css'] = mb_substr(trim(input_clean($_POST['custom_css'])), 0, 8192);
        $_POST['custom_js'] = mb_substr(trim($_POST['custom_js']), 0, 8192);
        $_POST['leap_link'] = get_url($_POST['leap_link'] ?? null);
        $this->check_location_url($_POST['leap_link'], true);

        /* Make sure the font is ok */
        $biolink_fonts = require APP_PATH . 'includes/biolink_fonts.php';
        $_POST['font'] = !array_key_exists($_POST['font'], $biolink_fonts) ? false : query_clean($_POST['font']);
        $_POST['font_size'] = (int) $_POST['font_size'] < 14 || (int) $_POST['font_size'] > 22 ? 16 : (int) $_POST['font_size'];

        /* Set the new settings variable */
        $settings = [
            'verified_location' => $_POST['verified_location'],
            'background_type' => $_POST['background_type'],
            'background' => $background ?? $link->settings->background,
            'background_color_one' => $background_color_one ?? null,
            'background_color_two' => $background_color_two ?? null,
            'favicon' => $image_uploaded_file['favicon'],
            'text_color' => $_POST['text_color'],
            'display_branding' => $_POST['display_branding'],
            'branding' => [
                'name' => $_POST['branding_name'],
                'url' => $_POST['branding_url'],
            ],
            'seo' => [
                'block' => $_POST['seo_block'],
                'title' => $_POST['seo_title'],
                'meta_description' => $_POST['seo_meta_description'],
                'image' => $image_uploaded_file['seo_image'],
            ],
            'utm' => [
                'medium' => $_POST['utm_medium'],
                'source' => $_POST['utm_source'],
            ],
            'font' => $_POST['font'],
            'font_size' => $_POST['font_size'],
            'password' => $_POST['password'],
            'sensitive_content' => $_POST['sensitive_content'],
            'leap_link' => $_POST['leap_link'],
            'custom_css' => $_POST['custom_css'],
            'custom_js' => $_POST['custom_js'],
        ];

        /* Check if we need to override defaults for a new theme */
        if($_POST['biolink_theme_id'] && $link->biolink_theme_id != $_POST['biolink_theme_id']) {
            $biolink_theme = $biolinks_themes[$_POST['biolink_theme_id']];

            /* Save settings for biolink page */
            $settings = array_merge($settings, (array) $biolink_theme->settings->biolink);

            /* Save settings for all existing blocks */
            $themable_blocks = ['link', 'mail', 'paypal', 'phone_collector', 'rss_feed', 'vcard', 'cta', 'youtube_feed', 'share', 'file', 'product', 'donation', 'service'];
            $themable_blocks_sql = "'" . implode('\', \'', $themable_blocks) . "'";

            $biolink_blocks_result = database()->query("SELECT `biolink_block_id`, `settings` FROM `biolinks_blocks` WHERE `link_id` = {$link->link_id} AND `type` IN ({$themable_blocks_sql})");
            while($biolink_block = $biolink_blocks_result->fetch_object()) {
                $biolink_block->settings = json_decode($biolink_block->settings);
                $new_biolink_block_settings = json_encode(array_merge((array) $biolink_block->settings, (array) $biolink_theme->settings->biolink_block));

                db()->where('biolink_block_id', $biolink_block->biolink_block_id)->update('biolinks_blocks', [
                    'settings' => $new_biolink_block_settings,
                ]);
            }

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItem('link?link_id=' . $link->link_id);
            \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $link->link_id);
        }

        /* Prepare background url if needed */
        $image_url['background'] = $settings['background_type'] == 'image' && $settings['background'] ?  UPLOADS_FULL_URL . $image_upload_path['background'] . $settings['background'] : null;

        /* Prepare settings for JSON insertion */
        $settings = json_encode($settings);

        /* Update the record */
        db()->where('link_id', $link->link_id)->update('links', [
            'project_id' => $_POST['project_id'],
            'domain_id' => $domain_id,
            'biolink_theme_id' => $_POST['biolink_theme_id'],
            'pixels_ids' => $_POST['pixels_ids'],
            'url' => $url,
            'settings' => $settings,
            'last_datetime' => \Altum\Date::$date,
        ]);

        /* Update the biolink page blocks if needed */
        if($link->project_id != $_POST['project_id']) {
            db()->where('biolink_id', $link->link_id)->update('links', ['project_id' => $_POST['project_id']]);
        }

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $link->link_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $link->link_id);

        Response::json(l('global.success_message.update2'), 'success', [
            'url' => $url,
            'images' => [
                'seo_image' => $image_url['seo_image'],
                'favicon' => $image_url['favicon'],
                'background' => $image_url['background'],
            ],
        ]);

    }

    private function update_file() {
        if(!settings()->links->files_is_enabled) {
            Response::json(l('global.error_message.basic'), 'error');
        }

        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['project_id'] = empty($_POST['project_id']) ? null : (int) $_POST['project_id'];
        $_POST['url'] = !empty($_POST['url']) ? get_slug($_POST['url'], '-', false) : false;
        if(isset($_POST['schedule']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && Date::validate($_POST['start_date'], 'Y-m-d H:i:s') && Date::validate($_POST['end_date'], 'Y-m-d H:i:s')) {
            $_POST['start_date'] = (new \DateTime($_POST['start_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
            $_POST['end_date'] = (new \DateTime($_POST['end_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        } else {
            $_POST['start_date'] = $_POST['end_date'] = null;
        }
        $_POST['expiration_url'] = get_url($_POST['expiration_url']);
        $_POST['clicks_limit'] = empty($_POST['clicks_limit']) ? null : (int) $_POST['clicks_limit'];
        $this->check_location_url($_POST['expiration_url'], true);
        $_POST['sensitive_content'] = (bool) isset($_POST['sensitive_content']);

        if(empty($_POST['domain_id']) && !settings()->links->main_domain_is_enabled && !\Altum\Authentication::is_admin()) {
            Response::json(l('create_link_modal.error_message.main_domain_is_disabled'), 'error');
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id'] ?? false);

        /* Existing pixels */
        $pixels = (new \Altum\Models\Pixel())->get_pixels($this->user->user_id);
        $_POST['pixels_ids'] = isset($_POST['pixels_ids']) ? array_map(
            function($pixel_id) {
                return (int) $pixel_id;
            },
            array_filter($_POST['pixels_ids'], function($pixel_id) use($pixels) {
                return array_key_exists($pixel_id, $pixels);
            })
        ) : [];
        $_POST['pixels_ids'] = json_encode($_POST['pixels_ids']);

        /* Check for any errors */
        $required_fields = [];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        $this->check_url($_POST['url']);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        if($_POST['project_id'] && !$project = db()->where('project_id', $_POST['project_id'])->where('user_id', $this->user->user_id)->getOne('projects', ['project_id'])) {
            die();
        }

        $link->settings = json_decode($link->settings);

        /* Check for a password set */
        $_POST['password'] = !empty($_POST['qweasdzxc']) ?
            ($_POST['qweasdzxc'] != $link->settings->password ? password_hash($_POST['qweasdzxc'], PASSWORD_DEFAULT) : $link->settings->password)
            : null;

        /* Check for duplicate url if needed */
        if($_POST['url'] && ($_POST['url'] != $link->url || $domain_id != $link->domain_id)) {

            if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                Response::json(l('link.error_message.url_exists'), 'error');
            }

        }

        $url = $_POST['url'];

        if(empty($_POST['url'])) {
            /* Generate random url if not specified */
            $url = mb_strtolower(string_generate(10));

            while(db()->where('url', $url)->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                $url = mb_strtolower(string_generate(10));
            }
        }

        /* File upload */
        $db_file = \Altum\Uploads::process_upload($link->settings->file, 'files', 'file', 'file_remove', settings()->links->file_size_limit, 'json_error');

        $settings = [
            'file' => $db_file,
            'clicks_limit' => $_POST['clicks_limit'],
            'expiration_url' => $_POST['expiration_url'],
            'password' => $_POST['password'],
            'sensitive_content' => $_POST['sensitive_content'],
        ];

        $settings = json_encode($settings);

        db()->where('link_id', $_POST['link_id'])->update('links', [
            'project_id' => $_POST['project_id'],
            'domain_id' => $domain_id,
            'pixels_ids' => $_POST['pixels_ids'],
            'url' => $url,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'settings' => $settings,
            'last_datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $link->link_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $link->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['url' => $url, 'file' => $db_file]);
    }

    private function update_vcard() {
        if(!settings()->links->vcards_is_enabled) {
            Response::json(l('global.error_message.basic'), 'error');
        }

        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['project_id'] = empty($_POST['project_id']) ? null : (int) $_POST['project_id'];
        $_POST['url'] = !empty($_POST['url']) ? get_slug($_POST['url'], '-', false) : false;
        if(isset($_POST['schedule']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && Date::validate($_POST['start_date'], 'Y-m-d H:i:s') && Date::validate($_POST['end_date'], 'Y-m-d H:i:s')) {
            $_POST['start_date'] = (new \DateTime($_POST['start_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
            $_POST['end_date'] = (new \DateTime($_POST['end_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        } else {
            $_POST['start_date'] = $_POST['end_date'] = null;
        }
        $_POST['expiration_url'] = get_url($_POST['expiration_url']);
        $_POST['clicks_limit'] = empty($_POST['clicks_limit']) ? null : (int) $_POST['clicks_limit'];
        $this->check_location_url($_POST['expiration_url'], true);
        $_POST['sensitive_content'] = (bool) isset($_POST['sensitive_content']);

        if(empty($_POST['domain_id']) && !settings()->links->main_domain_is_enabled && !\Altum\Authentication::is_admin()) {
            Response::json(l('create_link_modal.error_message.main_domain_is_disabled'), 'error');
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id'] ?? false);

        /* Existing pixels */
        $pixels = (new \Altum\Models\Pixel())->get_pixels($this->user->user_id);
        $_POST['pixels_ids'] = isset($_POST['pixels_ids']) ? array_map(
            function($pixel_id) {
                return (int) $pixel_id;
            },
            array_filter($_POST['pixels_ids'], function($pixel_id) use($pixels) {
                return array_key_exists($pixel_id, $pixels);
            })
        ) : [];
        $_POST['pixels_ids'] = json_encode($_POST['pixels_ids']);

        /* Check for any errors */
        $required_fields = [];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        $this->check_url($_POST['url']);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        if($_POST['project_id'] && !$project = db()->where('project_id', $_POST['project_id'])->where('user_id', $this->user->user_id)->getOne('projects', ['project_id'])) {
            die();
        }

        $link->settings = json_decode($link->settings);

        /* Check for a password set */
        $_POST['password'] = !empty($_POST['qweasdzxc']) ?
            ($_POST['qweasdzxc'] != $link->settings->password ? password_hash($_POST['qweasdzxc'], PASSWORD_DEFAULT) : $link->settings->password)
            : null;


        /* Check for duplicate url if needed */
        if($_POST['url'] && ($_POST['url'] != $link->url || $domain_id != $link->domain_id)) {

            if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                Response::json(l('link.error_message.url_exists'), 'error');
            }

        }

        $url = $_POST['url'];

        if(empty($_POST['url'])) {
            /* Generate random url if not specified */
            $url = mb_strtolower(string_generate(10));

            while(db()->where('url', $url)->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                $url = mb_strtolower(string_generate(10));
            }
        }

        /* File upload */
        $db_vcard_avatar = \Altum\Uploads::process_upload($link->settings->vcard_avatar, 'vcards_avatars', 'vcard_avatar', 'vcard_avatar_remove', 0.75, 'json_error');

        $settings = [
            'vcard_avatar' => $db_vcard_avatar,
            'clicks_limit' => $_POST['clicks_limit'],
            'expiration_url' => $_POST['expiration_url'],
            'password' => $_POST['password'],
            'sensitive_content' => $_POST['sensitive_content'],
        ];

        /* Process vcard */
        $settings['vcard_first_name'] = $_POST['vcard_first_name'] = mb_substr(input_clean($_POST['vcard_first_name']), 0, $this->links_types['vcard']['fields']['first_name']['max_length']);
        $settings['vcard_last_name'] = $_POST['vcard_last_name'] = mb_substr(input_clean($_POST['vcard_last_name']), 0, $this->links_types['vcard']['fields']['last_name']['max_length']);
        $settings['vcard_email'] = $_POST['vcard_email'] = mb_substr(input_clean($_POST['vcard_email']), 0, $this->links_types['vcard']['fields']['email']['max_length']);
        $settings['vcard_url'] = $_POST['vcard_url'] = mb_substr(input_clean($_POST['vcard_url']), 0, $this->links_types['vcard']['fields']['url']['max_length']);
        $settings['vcard_company'] = $_POST['vcard_company'] = mb_substr(input_clean($_POST['vcard_company']), 0, $this->links_types['vcard']['fields']['company']['max_length']);
        $settings['vcard_job_title'] = $_POST['vcard_job_title'] = mb_substr(input_clean($_POST['vcard_job_title']), 0, $this->links_types['vcard']['fields']['job_title']['max_length']);
        $settings['vcard_birthday'] = $_POST['vcard_birthday'] = mb_substr(input_clean($_POST['vcard_birthday']), 0, $this->links_types['vcard']['fields']['birthday']['max_length']);
        $settings['vcard_street'] = $_POST['vcard_street'] = mb_substr(input_clean($_POST['vcard_street']), 0, $this->links_types['vcard']['fields']['street']['max_length']);
        $settings['vcard_city'] = $_POST['vcard_city'] = mb_substr(input_clean($_POST['vcard_city']), 0, $this->links_types['vcard']['fields']['city']['max_length']);
        $settings['vcard_zip'] = $_POST['vcard_zip'] = mb_substr(input_clean($_POST['vcard_zip']), 0, $this->links_types['vcard']['fields']['zip']['max_length']);
        $settings['vcard_region'] = $_POST['vcard_region'] = mb_substr(input_clean($_POST['vcard_region']), 0, $this->links_types['vcard']['fields']['region']['max_length']);
        $settings['vcard_country'] = $_POST['vcard_country'] = mb_substr(input_clean($_POST['vcard_country']), 0, $this->links_types['vcard']['fields']['country']['max_length']);
        $settings['vcard_note'] = $_POST['vcard_note'] = mb_substr(input_clean($_POST['vcard_note']), 0, $this->links_types['vcard']['fields']['note']['max_length']);

        /* Phone numbers */
        if(!isset($_POST['vcard_phone_number'])) {
            $_POST['vcard_phone_number'] = [];
        }
        $vcard_phone_numbers = [];
        foreach($_POST['vcard_phone_number'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 20) continue;

            $vcard_phone_numbers[] = mb_substr(input_clean($value), 0, $this->links_types['vcard']['fields']['phone_number']['max_length']);
        }
        $settings['vcard_phone_numbers'] = $vcard_phone_numbers;

        /* Socials */
        if(!isset($_POST['vcard_social_label'])) {
            $_POST['vcard_social_label'] = [];
            $_POST['vcard_social_value'] = [];
        }

        $vcard_socials = [];
        foreach($_POST['vcard_social_label'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 20) continue;

            $vcard_socials[] = [
                'label' => mb_substr(input_clean($value), 0, $this->links_types['vcard']['fields']['social_value']['max_length']),
                'value' => mb_substr(input_clean($_POST['vcard_social_value'][$key]), 0, $this->links_types['vcard']['fields']['social_value']['max_length'])
            ];
        }
        $settings['vcard_socials'] = $vcard_socials;

        $settings = json_encode($settings);

        db()->where('link_id', $_POST['link_id'])->update('links', [
            'project_id' => $_POST['project_id'],
            'domain_id' => $domain_id,
            'pixels_ids' => $_POST['pixels_ids'],
            'url' => $url,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'settings' => $settings,
            'last_datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $link->link_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $link->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['url' => $url]);
    }

    private function update_event() {
        if(!settings()->links->events_is_enabled) {
            Response::json(l('global.error_message.basic'), 'error');
        }

        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['project_id'] = empty($_POST['project_id']) ? null : (int) $_POST['project_id'];
        $_POST['url'] = !empty($_POST['url']) ? get_slug($_POST['url'], '-', false) : false;
        if(isset($_POST['schedule']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && Date::validate($_POST['start_date'], 'Y-m-d H:i:s') && Date::validate($_POST['end_date'], 'Y-m-d H:i:s')) {
            $_POST['start_date'] = (new \DateTime($_POST['start_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
            $_POST['end_date'] = (new \DateTime($_POST['end_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        } else {
            $_POST['start_date'] = $_POST['end_date'] = null;
        }
        $_POST['expiration_url'] = get_url($_POST['expiration_url']);
        $_POST['clicks_limit'] = empty($_POST['clicks_limit']) ? null : (int) $_POST['clicks_limit'];
        $this->check_location_url($_POST['expiration_url'], true);
        $_POST['sensitive_content'] = (bool) isset($_POST['sensitive_content']);

        if(empty($_POST['domain_id']) && !settings()->links->main_domain_is_enabled && !\Altum\Authentication::is_admin()) {
            Response::json(l('create_link_modal.error_message.main_domain_is_disabled'), 'error');
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id'] ?? false);

        /* Existing pixels */
        $pixels = (new \Altum\Models\Pixel())->get_pixels($this->user->user_id);
        $_POST['pixels_ids'] = isset($_POST['pixels_ids']) ? array_map(
            function($pixel_id) {
                return (int) $pixel_id;
            },
            array_filter($_POST['pixels_ids'], function($pixel_id) use($pixels) {
                return array_key_exists($pixel_id, $pixels);
            })
        ) : [];
        $_POST['pixels_ids'] = json_encode($_POST['pixels_ids']);

        /* Check for any errors */
        $required_fields = [];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        $this->check_url($_POST['url']);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        if($_POST['project_id'] && !$project = db()->where('project_id', $_POST['project_id'])->where('user_id', $this->user->user_id)->getOne('projects', ['project_id'])) {
            die();
        }

        $link->settings = json_decode($link->settings);

        /* Check for a password set */
        $_POST['password'] = !empty($_POST['qweasdzxc']) ?
            ($_POST['qweasdzxc'] != $link->settings->password ? password_hash($_POST['qweasdzxc'], PASSWORD_DEFAULT) : $link->settings->password)
            : null;


        /* Check for duplicate url if needed */
        if($_POST['url'] && ($_POST['url'] != $link->url || $domain_id != $link->domain_id)) {

            if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                Response::json(l('link.error_message.url_exists'), 'error');
            }

        }

        $url = $_POST['url'];

        if(empty($_POST['url'])) {
            /* Generate random url if not specified */
            $url = mb_strtolower(string_generate(10));

            while(db()->where('url', $url)->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                $url = mb_strtolower(string_generate(10));
            }
        }

        $settings = [
            'clicks_limit' => $_POST['clicks_limit'],
            'expiration_url' => $_POST['expiration_url'],
            'password' => $_POST['password'],
            'sensitive_content' => $_POST['sensitive_content'],
        ];

        /* Process event */
        $settings['event_name'] = $_POST['event_name'] = mb_substr(input_clean($_POST['event_name']), 0, $this->links_types['event']['fields']['name']['max_length']);
        $settings['event_location'] = $_POST['event_location'] = mb_substr(input_clean($_POST['event_location']), 0, $this->links_types['event']['fields']['location']['max_length']);
        $settings['event_url'] = $_POST['event_url'] = mb_substr(input_clean($_POST['event_url']), 0, $this->links_types['event']['fields']['url']['max_length']);
        $settings['event_note'] = $_POST['event_note'] = mb_substr(input_clean($_POST['event_note']), 0, $this->links_types['event']['fields']['note']['max_length']);
        $settings['event_timezone'] = $_POST['event_timezone'] = in_array($_POST['event_timezone'], \DateTimeZone::listIdentifiers()) ? input_clean($_POST['event_timezone']) : Date::$default_timezone;
        $settings['event_start_datetime'] = $_POST['event_start_datetime'] = (new \DateTime($_POST['event_start_datetime']))->format('Y-m-d\TH:i:s');
        $settings['event_end_datetime'] = $_POST['event_end_datetime'] = (new \DateTime($_POST['event_end_datetime']))->format('Y-m-d\TH:i:s');

        $settings = json_encode($settings);

        db()->where('link_id', $_POST['link_id'])->update('links', [
            'project_id' => $_POST['project_id'],
            'domain_id' => $domain_id,
            'pixels_ids' => $_POST['pixels_ids'],
            'url' => $url,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'settings' => $settings,
            'last_datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $link->link_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $link->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['url' => $url]);
    }

    private function delete() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('delete')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $_POST['link_id'] = (int) $_POST['link_id'];

        /* Check for possible errors */
        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links', ['link_id'])) {
            die();
        }

        (new \Altum\Models\Link())->delete($link->link_id);

        Response::json(l('global.success_message.delete2'), 'success', ['url' => url('links')]);

    }

    public function duplicate() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('links');
        }

        $_POST['link_id'] = (int) $_POST['link_id'];

        //ALTUMCODE.DEMO: if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('links');
        }

        /* Get the link data */
        $link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links');

        if(!$link) {
            redirect('links');
        }

        /* Make sure that the user didn't exceed the limit */
        if($link->type == 'link') {
            if(!settings()->links->shortener_is_enabled) {
                Response::json(l('global.error_message.basic'), 'error');
            }

            $user_total_links = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'link'")->fetch_object()->total;
            if($this->user->plan_settings->links_limit != -1 && $user_total_links >= $this->user->plan_settings->links_limit) {
                Alerts::add_error(l('global.info_message.plan_feature_limit'));
            }
        }

        elseif($link->type == 'biolink') {
            if(!settings()->links->biolinks_is_enabled) {
                Response::json(l('global.error_message.basic'), 'error');
            }

            $user_total_biolinks = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'biolink'")->fetch_object()->total;
            if($this->user->plan_settings->biolinks_limit != -1 && $user_total_biolinks >= $this->user->plan_settings->biolinks_limit) {
                Alerts::add_error(l('global.info_message.plan_feature_limit'));
            }
        }

        elseif($link->type == 'file') {
            if(!settings()->links->files_is_enabled) {
                Response::json(l('global.error_message.basic'), 'error');
            }

            $user_total_files = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'file'")->fetch_object()->total;
            if($this->user->plan_settings->files_limit != -1 && $user_total_files >= $this->user->plan_settings->files_limit) {
                Alerts::add_error(l('global.info_message.plan_feature_limit'));
            }
        }

        elseif($link->type == 'vcard') {
            if(!settings()->links->vcards_is_enabled) {
                Response::json(l('global.error_message.basic'), 'error');
            }

            $user_total_vcards = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->user->user_id} AND `type` = 'vcard'")->fetch_object()->total;
            if($this->user->plan_settings->vcards_limit != -1 && $user_total_vcards >= $this->user->plan_settings->vcards_limit) {
                Alerts::add_error(l('global.info_message.plan_feature_limit'));
            }
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Duplicate the link */
            $link->settings = json_decode($link->settings);

            if($link->type == 'biolink') {
                $link->settings->seo_image = \Altum\Uploads::copy_uploaded_file($link->settings->seo_image, 'block_images/', 'block_images/', 'json_error');
                $link->settings->favicon = \Altum\Uploads::copy_uploaded_file($link->settings->favicon, 'favicons/', 'favicons/', 'json_error');
                if ($link->settings->background_type == 'image') $link->settings->background = \Altum\Uploads::copy_uploaded_file($link->settings->background, 'backgrounds/', 'backgrounds/', 'json_error');
            }

            if($link->type == 'vcard') {
                $link->settings->vcard_avatar = \Altum\Uploads::copy_uploaded_file($link->settings->vcard_avatar, \Altum\Uploads::get_path('vcards_avatars'), \Altum\Uploads::get_path('vcards_avatars'), 'json_error');
            }

            if($link->type == 'file') {
                $link->settings->file = \Altum\Uploads::copy_uploaded_file($link->settings->file, \Altum\Uploads::get_path('files'), \Altum\Uploads::get_path('files'), 'json_error');
            }

            /* Generate random url if not specified */
            $url = mb_strtolower(string_generate(10));
            while (db()->where('url', $url)->where('domain_id', $link->domain_id)->getValue('links', 'link_id')) {
                $url = mb_strtolower(string_generate(10));
            }

            /* Database query */
            $link_id = db()->insert('links', [
                'user_id' => $this->user->user_id,
                'project_id' => $link->project_id,
                'biolink_theme_id' => $link->biolink_theme_id,
                'biolink_id' => $link->biolink_id,
                'domain_id' => $link->domain_id,
                'pixels_ids' => $link->pixels_ids,
                'type' => $link->type,
                'url' => $url,
                'location_url' => $link->location_url,
                'settings' => json_encode($link->settings),
                'start_date' => $link->start_date,
                'end_date' => $link->end_date,
                'is_verified' => $link->is_verified,
                'is_enabled' => $link->is_enabled,
                'datetime' => \Altum\Date::$date,
            ]);

            /* Duplicate the biolink blocks */
            if ($link->type == 'biolink') {
                /* Get all biolink blocks if needed */
                $biolink_blocks = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->get('biolinks_blocks');

                foreach($biolink_blocks as $biolink_block) {
                    $biolink_block->settings = json_decode($biolink_block->settings);

                    if(is_array($biolink_block->settings)) {
                        $biolink_block->settings = (object) $biolink_block->settings;
                    }

                    /* Duplication of resources */
                    switch($biolink_block->type) {
                        case 'file':
                            $biolink_block->settings->file = \Altum\Uploads::copy_uploaded_file($biolink_block->settings->file, \Altum\Uploads::get_path('files'), \Altum\Uploads::get_path('files'), 'json_error');
                            break;

                        case 'avatar':
                            $biolink_block->settings->image = \Altum\Uploads::copy_uploaded_file($biolink_block->settings->image, 'avatars/', 'avatars/', 'json_error');
                            break;

                        case 'vcard':
                            $biolink_block->settings->vcard_avatar = \Altum\Uploads::copy_uploaded_file($biolink_block->settings->vcard_avatar, 'avatars/', 'avatars/', 'json_error');
                            break;

                        case 'image':
                        case 'image_grid':
                            $biolink_block->settings->image = \Altum\Uploads::copy_uploaded_file($biolink_block->settings->image, 'block_images/', 'block_images/', 'json_error');
                            break;

                        default:
                            $biolink_block->settings->image = \Altum\Uploads::copy_uploaded_file($biolink_block->settings->image, 'block_thumbnail_images/', 'block_thumbnail_images/', 'json_error');
                            break;
                    }

                    /* Database query */
                    db()->insert('biolinks_blocks', [
                        'user_id' => $this->user->user_id,
                        'link_id' => $link_id,
                        'type' => $biolink_block->type,
                        'location_url' => $biolink_block->location_url,
                        'settings' => json_encode($biolink_block->settings),
                        'order' => $biolink_block->order,
                        'start_date' => $biolink_block->start_date,
                        'end_date' => $biolink_block->end_date,
                        'is_enabled' => $biolink_block->is_enabled,
                        'datetime' => \Altum\Date::$date,
                    ]);
                }
            }

            /* Set a nice success message */
            Alerts::add_success(l('global.success_message.create2'));

            /* Redirect */
            redirect('link/' . $link_id);
        }

        redirect('links');
    }


    /* Function to bundle together all the checks of a custom url */
    private function check_url($url) {
        if($url) {
            /* Make sure the url alias is not blocked by a route of the product */
            if(array_key_exists($url, \Altum\Router::$routes[''])) {
                Response::json(l('link.error_message.blacklisted_url'), 'error');
            }

            /* Make sure the custom url is not blacklisted */
            if(in_array(mb_strtolower($url), explode(',', settings()->links->blacklisted_keywords))) {
                Response::json(l('link.error_message.blacklisted_keyword'), 'error');
            }
        }
    }

    /* Function to bundle together all the checks of an url */
    private function check_location_url($url, $can_be_empty = false) {

        if(empty(trim($url)) && $can_be_empty) {
            return;
        }

        if(empty(trim($url))) {
            Response::json(l('global.error_message.empty_fields'), 'error');
        }

        $url_details = parse_url($url);

        if(!isset($url_details['scheme'])) {
            Response::json(l('link.error_message.invalid_location_url'), 'error');
        }

        if(!$this->user->plan_settings->deep_links && !in_array($url_details['scheme'], ['http', 'https'])) {
            Response::json(l('link.error_message.invalid_location_url'), 'error');
        }

        /* Make sure the domain is not blacklisted */
        $domain = get_domain_from_url($url);

        if($domain && in_array($domain, explode(',', settings()->links->blacklisted_domains))) {
            Response::json(l('link.error_message.blacklisted_domain'), 'error');
        }

        /* Check the url with google safe browsing to make sure it is a safe website */
        if(settings()->links->google_safe_browsing_is_enabled) {
            if(google_safe_browsing_check($url, settings()->links->google_safe_browsing_api_key)) {
                Response::json(l('link.error_message.blacklisted_location_url'), 'error');
            }
        }
    }

    /* Check if custom domain is set and return the proper value */
    private function get_domain_id($posted_domain_id) {
        $domain_id = 0;

        if(isset($posted_domain_id)) {
            $domain_id = (int) $posted_domain_id;

            /* Make sure the user has access to global additional domains */
            if($this->user->plan_settings->additional_global_domains) {
                $domain_id = database()->query("SELECT `domain_id` FROM `domains` WHERE `domain_id` = {$domain_id} AND (`user_id` = {$this->user->user_id} OR `type` = 1)")->fetch_object()->domain_id ?? 0;
            } else {
                $domain_id = database()->query("SELECT `domain_id` FROM `domains` WHERE `domain_id` = {$domain_id} AND `user_id` = {$this->user->user_id}")->fetch_object()->domain_id ?? 0;
            }

        }

        return $domain_id;
    }
}
