<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Date;
use Altum\Response;
use Altum\Traits\Apiable;

class ApiLinks extends Controller {
    use Apiable;

    public function index() {

        $this->verify_request();

        /* Decide what to continue with */
        switch($_SERVER['REQUEST_METHOD']) {
            case 'GET':

                /* Detect if we only need an object, or the whole list */
                if(isset($this->params[0])) {
                    $this->get();
                } else {
                    $this->get_all();
                }

            break;

            case 'POST':

                /* Detect what method to use */
                if(isset($this->params[0])) {
                    $this->patch();
                } else {
                    $this->post();
                }

                break;

            case 'DELETE':
                $this->delete();
                break;
        }

        $this->return_404();
    }

    private function get_all() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters([], [], []));
        $filters->set_default_order_by('link_id', settings()->main->default_order_type);
        $filters->set_default_results_per_page(settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$this->api_user->user_id}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('api/links?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $data = [];
        $data_result = database()->query("
            SELECT
                *
            FROM
                `links`
            WHERE
                `user_id` = {$this->api_user->user_id}
                {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}
                  
            {$paginator->get_sql_limit()}
        ");
        while($row = $data_result->fetch_object()) {

            /* Prepare the data */
            $row = [
                'id' => (int) $row->link_id,
                'project_id' => (int) $row->project_id,
                'domain_id' => (int) $row->domain_id,
                'biolink_id' => (int) $row->biolink_id,
                'pixels_ids' => json_decode($row->pixels_ids),
                'biolink_theme_id' => (int) $row->biolink_theme_id,
                'type' => $row->type,
                'url' => $row->url,
                'location_url' => $row->location_url,
                'settings' => json_decode($row->settings),
                'clicks' => $row->clicks,
                'start_date' => $row->start_date,
                'end_date' => $row->end_date,
                'is_verified' => $row->is_verified,
                'is_enabled' => $row->is_enabled,
                'last_datetime' => $row->last_datetime,
                'datetime' => $row->datetime,
            ];

            $data[] = $row;
        }

        /* Prepare the data */
        $meta = [
            'page' => $_GET['page'] ?? 1,
            'total_pages' => $paginator->getNumPages(),
            'results_per_page' => $filters->get_results_per_page(),
            'total_results' => (int) $total_rows,
        ];

        /* Prepare the pagination links */
        $others = ['links' => [
            'first' => $paginator->getPageUrl(1),
            'last' => $paginator->getNumPages() ? $paginator->getPageUrl($paginator->getNumPages()) : null,
            'next' => $paginator->getNextUrl(),
            'prev' => $paginator->getPrevUrl(),
            'self' => $paginator->getPageUrl($_GET['page'] ?? 1)
        ]];

        Response::jsonapi_success($data, $meta, 200, $others);
    }

    private function get() {

        $link_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $link = db()->where('link_id', $link_id)->where('user_id', $this->api_user->user_id)->getOne('links');

        /* We haven't found the resource */
        if(!$link) {
            $this->return_404();
        }

        /* Prepare the data */
        $data = [
            'id' => (int) $link->link_id,
            'project_id' => (int) $link->project_id,
            'domain_id' => (int) $link->domain_id,
            'pixels_ids' => json_decode($link->pixels_ids),
            'biolink_id' => (int) $link->biolink_id,
            'biolink_theme_id' => (int) $link->biolink_theme_id,
            'type' => $link->type,
            'url' => $link->url,
            'location_url' => $link->location_url,
            'settings' => json_decode($link->settings),
            'clicks' => $link->clicks,
            'start_date' => $link->start_date,
            'end_date' => $link->end_date,
            'is_verified' => $link->is_verified,
            'is_enabled' => $link->is_enabled,
            'last_datetime' => $link->last_datetime,
            'datetime' => $link->datetime
        ];

        Response::jsonapi_success($data);

    }

    private function post() {

        /* Check for the plan limit */
        $total_rows = db()->where('user_id', $this->api_user->user_id)->where('type', 'links')->getValue('links', 'count(`link_id`)');

        if($this->api_user->plan_settings->links_limit != -1 && $total_rows >= $this->api_user->plan_settings->links_limit) {
            $this->response_error(l('global.info_message.plan_feature_limit'), 401);
        }

        /* Type of link */
        $_POST['type'] = in_array($_POST['type'], ['link']) ? query_clean($_POST['type']) : null;

        /* Check for any errors */
        $required_fields = ['location_url'];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                $this->response_error(l('global.error_message.empty_fields'), 401);
                break 1;
            }
        }

        if(empty($_POST['domain_id']) && !settings()->links->main_domain_is_enabled && !\Altum\Authentication::is_admin()) {
            $this->response_error(l('create_link_modal.error_message.main_domain_is_disabled'), 401);
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id'] ?? false);

        /* Location & url */
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['url'] = !empty($_POST['url']) ? get_slug($_POST['url'], '-', false) : false;
        $this->check_url($_POST['url']);
        $this->check_location_url($_POST['location_url']);

        /* Process the rest of the data */
        $_POST['is_enabled'] = isset($_POST['is_enabled']) ? (int) (bool) $_POST['is_enabled'] : 1;
        if(isset($_POST['schedule']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && Date::validate($_POST['start_date'], 'Y-m-d H:i:s') && Date::validate($_POST['end_date'], 'Y-m-d H:i:s')) {
            $_POST['start_date'] = (new \DateTime($_POST['start_date'], new \DateTimeZone($this->api_user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
            $_POST['end_date'] = (new \DateTime($_POST['end_date'], new \DateTimeZone($this->api_user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        } else {
            $_POST['start_date'] = $_POST['end_date'] = null;
        }

        $_POST['expiration_url'] = get_url($_POST['expiration_url']);
        $_POST['clicks_limit'] = isset($_POST['clicks_limit']) ? (int) $_POST['clicks_limit'] : null;
        $this->check_location_url($_POST['expiration_url'], true);
        $_POST['sensitive_content'] = (bool) isset($_POST['sensitive_content']);

        /* Existing pixels */
        $pixels = (new \Altum\Models\Pixel())->get_pixels($this->api_user->user_id);
        $_POST['pixels_ids'] = isset($_POST['pixels_ids']) ? array_map(
            function($pixel_id) {
                return (int) $pixel_id;
            },
            array_filter($_POST['pixels_ids'], function($pixel_id) use($pixels) {
                return array_key_exists($pixel_id, $pixels);
            })
        ) : [];
        $_POST['pixels_ids'] = json_encode($_POST['pixels_ids']);

        /* Project */
        $_POST['project_id'] = isset($_POST['project_id']) ? (int) $_POST['project_id'] : null;
        if($_POST['project_id'] && !$project = db()->where('project_id', $_POST['project_id'])->where('user_id', $this->api_user->user_id)->getOne('projects', ['project_id'])) {
            $_POST['project_id'] = null;
        }

        /* Check for a password set */
        $_POST['password'] = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

        /* Check for duplicate url if needed */
        if($_POST['url']) {
            if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                $this->response_error(l('link.error_message.url_exists'), 401);
            }

            $url = $_POST['url'];
        } else {
            $url = mb_strtolower(string_generate(10));

            /* Generate random url if not specified */
            while(db()->where('url', $url)->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                $url = mb_strtolower(string_generate(10));
            }
        }

        /* Prepare the settings */
        $targeting_types = ['country_code', 'device_type', 'browser_language', 'rotation', 'os_name'];
        $_POST['targeting_type'] = isset($_POST['targeting_type']) && in_array($_POST['targeting_type'], array_merge(['false'], $targeting_types)) ? query_clean($_POST['targeting_type']) : 'false';

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

        /* Prepare the statement and execute query */
        $link_id = db()->insert('links', [
            'user_id' => $this->api_user->user_id,
            'project_id' => $_POST['project_id'],
            'domain_id' => $domain_id,
            'pixels_ids' => $_POST['pixels_ids'],
            'type' => 'link',
            'url' => $url,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'is_enabled' => $_POST['is_enabled'],
            'datetime' => \Altum\Date::$date,
        ]);

        /* Prepare the data */
        $data = [
            'id' => $link_id
        ];

        Response::jsonapi_success($data, null, 201);
    }

    private function patch() {

        $link_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $link = db()->where('link_id', $link_id)->where('user_id', $this->api_user->user_id)->getOne('links');

        /* We haven't found the resource */
        if(!$link) {
            $this->return_404();
        }
        $link->settings = json_decode($link->settings);
        $link->pixels_ids = json_decode($link->pixels_ids);

        if(isset($_POST['domain_id']) && $_POST['domain_id'] == 0 && !settings()->links->main_domain_is_enabled && !\Altum\Authentication::is_admin()) {
            $this->response_error(l('create_link_modal.error_message.main_domain_is_disabled'), 401);
        }

        /* Check if custom domain is set */
        $domain_id = $this->get_domain_id($_POST['domain_id'] ?? $link->domain_id);

        /* Location & url */
        $_POST['location_url'] = get_url($_POST['location_url'] ?? $link->location_url);
        $_POST['url'] = !empty($_POST['url']) ? get_slug(query_clean($_POST['url'] ?? $link->url), '-', false) : false;
        $this->check_url($_POST['url']);
        $this->check_location_url($_POST['location_url']);

        /* Process the rest of the data */
        $_POST['is_enabled'] = isset($_POST['is_enabled']) ? (int) (bool) $_POST['is_enabled'] : $link->is_enabled;
        if(isset($_POST['schedule']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && Date::validate($_POST['start_date'], 'Y-m-d H:i:s') && Date::validate($_POST['end_date'], 'Y-m-d H:i:s')) {
            $_POST['start_date'] = (new \DateTime($_POST['start_date'], new \DateTimeZone($this->api_user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
            $_POST['end_date'] = (new \DateTime($_POST['end_date'], new \DateTimeZone($this->api_user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        } else {
            $_POST['start_date'] = $link->start_date;
            $_POST['end_date'] = $link->end_date;
        }

        $_POST['expiration_url'] = get_url($_POST['expiration_url'] ?? $link->settings->expiration_url);
        $_POST['clicks_limit'] = isset($_POST['clicks_limit']) ? (int) $_POST['clicks_limit'] : $link->settings->clicks_limit;
        $this->check_location_url($_POST['expiration_url'], true);
        $_POST['sensitive_content'] = isset($_POST['sensitive_content']) ? (bool) isset($_POST['sensitive_content']) : $link->settings->sensitive_content;

        /* Existing pixels */
        $pixels = (new \Altum\Models\Pixel())->get_pixels($this->api_user->user_id);
        $_POST['pixels_ids'] = isset($_POST['pixels_ids']) ? array_map(
            function($pixel_id) {
                return (int) $pixel_id;
            },
            array_filter($_POST['pixels_ids'], function($pixel_id) use($pixels) {
                return array_key_exists($pixel_id, $pixels);
            })
        ) : $link->pixels_ids;
        $_POST['pixels_ids'] = json_encode($_POST['pixels_ids']);

        /* Project */
        $_POST['project_id'] = isset($_POST['project_id']) ? (int) $_POST['project_id'] : $link->project_id;
        if($_POST['project_id'] && !$project = db()->where('project_id', $_POST['project_id'])->where('user_id', $this->api_user->user_id)->getOne('projects', ['project_id'])) {
            $_POST['project_id'] = null;
        }

        /* Check for a password set */
        $_POST['password'] = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $link->settings->password;

        /* Check for duplicate url if needed */
        if($_POST['url'] && ($_POST['url'] != $link->url || $domain_id != $link->domain_id)) {
            if(db()->where('url', $_POST['url'])->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                $this->response_error(l('link.error_message.url_exists'), 401);
            }

            $url = $_POST['url'];
        } else {
            $url = $link->url;

            /* Generate random url if not specified */
            if(empty($_POST['url'])) {
                while (db()->where('url', $url)->where('domain_id', $domain_id)->getValue('links', 'link_id')) {
                    $url = mb_strtolower(string_generate(10));
                }
            }
        }

        /* Prepare the settings */
        $targeting_types = ['country_code', 'device_type', 'browser_language', 'rotation', 'os_name'];
        $_POST['targeting_type'] = isset($_POST['targeting_type']) && in_array($_POST['targeting_type'], array_merge(['false'], $targeting_types)) ? query_clean($_POST['targeting_type']) : $link->settings->targeting_type;

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

        /* Database query */
        db()->where('link_id', $link->link_id)->update('links', [
            'project_id' => $_POST['project_id'],
            'domain_id' => $domain_id,
            'pixels_ids' => $_POST['pixels_ids'],
            'url' => $url,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'is_enabled' => $_POST['is_enabled'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $link->link_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $link->link_id);

        /* Prepare the data */
        $data = [
            'id' => $link->link_id
        ];

        Response::jsonapi_success($data, null, 200);

    }

    private function delete() {

        $link_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $link = db()->where('link_id', $link_id)->where('user_id', $this->api_user->user_id)->getOne('links');

        /* We haven't found the resource */
        if(!$link) {
            $this->return_404();
        }

        /* Delete the resource */
        (new \Altum\Models\Link())->delete($link->link_id);

        http_response_code(200);
        die();

    }

    /* Function to bundle together all the checks of a custom url */
    private function check_url($url) {
        if($url) {
            /* Make sure the url alias is not blocked by a route of the product */
            if(array_key_exists($url, \Altum\Router::$routes[''])) {
                $this->response_error(l('link.error_message.blacklisted_url'), 401);
            }

            /* Make sure the custom url is not blacklisted */
            if(in_array(mb_strtolower($url), explode(',', settings()->links->blacklisted_keywords))) {
                $this->response_error(l('link.error_message.blacklisted_keyword'), 401);
            }
        }
    }

    /* Function to bundle together all the checks of an url */
    private function check_location_url($url, $can_be_empty = false) {

        if(empty(trim($url)) && $can_be_empty) {
            return;
        }

        if(empty(trim($url))) {
            $this->response_error(l('global.error_message.empty_fields'), 401);
        }

        $url_details = parse_url($url);

        if(!isset($url_details['scheme'])) {
            $this->response_error(l('link.error_message.invalid_location_url'), 401);
        }

        if(!$this->api_user->plan_settings->deep_links && !in_array($url_details['scheme'], ['http', 'https'])) {
            $this->response_error(l('link.error_message.invalid_location_url'), 401);
        }

        /* Make sure the domain is not blacklisted */
        $domain = get_domain_from_url($url);

        if($domain && in_array($domain, explode(',', settings()->links->blacklisted_domains))) {
            $this->response_error(l('link.error_message.blacklisted_domain'), 401);
        }

        /* Check the url with google safe browsing to make sure it is a safe website */
        if(settings()->links->google_safe_browsing_is_enabled) {
            if(google_safe_browsing_check($url, settings()->links->google_safe_browsing_api_key)) {
                $this->response_error(l('link.error_message.blacklisted_location_url'), 401);
            }
        }
    }

    /* Check if custom domain is set and return the proper value */
    private function get_domain_id($posted_domain_id) {
        $domain_id = 0;

        if(isset($posted_domain_id)) {
            $domain_id = (int) $posted_domain_id;

            /* Make sure the user has access to global additional domains */
            if($this->api_user->plan_settings->additional_global_domains) {
                $domain_id = database()->query("SELECT `domain_id` FROM `domains` WHERE `domain_id` = {$domain_id} AND (`user_id` = {$this->api_user->user_id} OR `type` = 1)")->fetch_object()->domain_id ?? 0;
            } else {
                $domain_id = database()->query("SELECT `domain_id` FROM `domains` WHERE `domain_id` = {$domain_id} AND `user_id` = {$this->api_user->user_id}")->fetch_object()->domain_id ?? 0;
            }

        }

        return $domain_id;
    }
}
