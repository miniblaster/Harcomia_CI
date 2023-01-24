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
use Altum\Captcha;
use Altum\Date;
use Altum\Language;
use Altum\Meta;
use Altum\Models\Domain;
use Altum\Models\User;
use Altum\PaymentGateways\Paystack;
use Altum\Response;
use Altum\Title;
use MaxMind\Db\Reader;
use Razorpay\Api\Api;

class Link extends Controller {
    public $link = null;
    public $type;
    public $user;
    public $is_preview = false;

    public function index() {

        /* Detect if access to url comes from id linking or url alias */
        if(isset(\Altum\Router::$data['link'])) {
            $this->link = \Altum\Router::$data['link'];
            $this->type = 'link';
        } else {

            if(isset($_GET['link_id'])) {
                $link_id = (int) $_GET['link_id'];
                $this->link = db()->where('link_id', $link_id)->getOne('links');
                $this->type = 'link';
            }

            if(isset($_GET['biolink_block_id'])) {
                $biolink_block_id = (int) $_GET['biolink_block_id'];
                $this->link = db()->where('biolink_block_id', $biolink_block_id)->getOne('biolinks_blocks');
                $this->type = 'biolink_block';
            }

        }
        $domain_id = isset(\Altum\Router::$data['domain']) ? \Altum\Router::$data['domain']->domain_id : 0;

        if(!$this->link) {
            redirect();
        }

        /* If a preview is asked for, make sure it is correct */
        if(isset($_GET['preview']) && $_GET['preview'] == md5($this->link->user_id)) {
            $this->is_preview = true;

            /* Get available themes */
            $this->biolinks_themes = (new \Altum\Models\BiolinkTheme())->get_biolinks_themes();
            $this->biolink_theme = isset($_GET['biolink_theme_id']) && array_key_exists($_GET['biolink_theme_id'], $this->biolinks_themes) ? $this->biolinks_themes[$_GET['biolink_theme_id']] : null;
        }

        /* Make sure the link is enabled */
        if(!$this->link->is_enabled && !$this->is_preview) {
            redirect();
        }

        /* Get the owner details */
        $this->user = (new User())->get_user_by_user_id($this->link->user_id);

        /* Make sure to check if the user is active */
        if($this->user->status != 1) {
            redirect();
        }

        /* Process the plan of the user */
        (new User())->process_user_plan_expiration_by_user($this->user);

        /* Parse the settings */
        $this->link->settings = json_decode($this->link->settings);
        $this->link->pixels_ids = json_decode($this->link->pixels_ids ?? '[]');

        /* Check if its an expired link based on scheduling / total link clicks */
        if($this->user->plan_settings->temporary_url_is_enabled) {
            /* Check for temporary clicks */
            if(isset($this->link->settings->clicks_limit) && $this->link->settings->clicks_limit) {
                $current_clicks = db()->where('link_id', $this->link->link_id)->getValue('links', 'clicks');
            }

            if (
                (
                    !empty($this->link->start_date) && !empty($this->link->end_date) &&
                    (
                        \Altum\Date::get('', null) < \Altum\Date::get($this->link->start_date, null, \Altum\Date::$default_timezone) ||
                        \Altum\Date::get('', null) > \Altum\Date::get($this->link->end_date, null, \Altum\Date::$default_timezone)
                    )
                )
                || (isset($current_clicks) && $current_clicks >= $this->link->settings->clicks_limit)
            ) {
                if($this->link->settings->expiration_url) {
                    header('Location: ' . $this->link->settings->expiration_url, true, 301);
                    die();
                } else {
                    redirect();
                }
            }
        }

        /* Determine the actual full url */
        if(in_array($this->type, ['link', 'file', 'vcard', 'event'])) {
            $this->link->full_url = $domain_id && !isset($_GET['link_id']) ? \Altum\Router::$data['domain']->scheme . \Altum\Router::$data['domain']->host . '/' . $this->link->url : SITE_URL . $this->link->url;
        } else {
            $this->link->full_url = SITE_URL . 'l/link?biolink_block_id=' . $this->link->biolink_block_id;
        }

        /* Set the language */
        Language::set_by_name($this->user->language);

        /* Check if the user has access to the link */
        $has_access = !$this->link->settings->password || ($this->link->settings->password && isset($_COOKIE['link_password_' . $this->link->link_id]) && $_COOKIE['link_password_' . $this->link->link_id] == $this->link->settings->password);

        /* Do not let the user have password protection if the plan doesnt allow it */
        if(!$this->user->plan_settings->password) {
            $has_access = true;
        }

        /* Check if the password form is submitted */
        if(!$has_access && !empty($_POST) && isset($_POST['type']) && $_POST['type'] == 'password') {
            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!password_verify($_POST['password'], $this->link->settings->password)) {
                Alerts::add_field_error('password', l('link.password.error_message'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                /* Set a cookie */
                setcookie('link_password_' . $this->link->link_id, $this->link->settings->password, time()+60*60*24*30);

                header('Location: ' . $this->link->full_url);

                die();
            }
        }

        /* Check if the user has access to the link */
        $can_see_content = !$this->link->settings->sensitive_content || ($this->link->settings->sensitive_content && isset($_COOKIE['link_sensitive_content_' . $this->link->link_id]));

        /* Do not let the user have password protection if the plan doesnt allow it */
        if(!$this->user->plan_settings->sensitive_content) {
            $can_see_content = true;
        }

        /* Check if the password form is submitted */
        if(!$can_see_content && !empty($_POST) && isset($_POST['type']) && $_POST['type'] == 'sensitive_content') {
            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                /* Set a cookie */
                setcookie('link_sensitive_content_' . $this->link->link_id, 'true', time()+60*60*24*30);

                header('Location: ' . $this->link->full_url);

                die();
            }
        }

        /* Display the password form */
        if(!$has_access && !$this->is_preview) {

            /* Set a custom title */
            Title::set(l('link.password.title'));

            /* Main View */
            $view = new \Altum\View('l/partials/password', (array) $this);

            $this->add_view_content('content', $view->run());

        }

        else if(!$can_see_content && !$this->is_preview) {

            /* Set a custom title */
            Title::set(l('link.sensitive_content.title'));

            /* Main View */
            $view = new \Altum\View('l/partials/sensitive_content', (array) $this);

            $this->add_view_content('content', $view->run());

        }

        else {

            $this->create_statistics();

            /* Check what to do next */
            if($this->link->type == 'biolink') {
                $this->process_biolink();
            } else if($this->link->type == 'vcard') {

                foreach(['vcard_first_name', 'vcard_last_name', 'vcard_email', 'vcard_url', 'vcard_company', 'vcard_job_title', 'vcard_birthday', 'vcard_street', 'vcard_city', 'vcard_zip', 'vcard_region', 'vcard_country', 'vcard_note'] as $key) {
                    $this->link->settings->{$key} = htmlspecialchars_decode($this->link->settings->{$key}, ENT_QUOTES);
                }

                /* Check for vcard download link */
                $vcard = new \JeroenDesloovere\VCard\VCard();

                /* Check if we should try to add the image to the vcard */
                if($this->link->settings->vcard_avatar) {
                    $vcard->addPhoto(UPLOADS_FULL_URL . 'avatars/' . $this->link->settings->vcard_avatar);
                }

                $vcard->addName($this->link->settings->vcard_last_name, $this->link->settings->vcard_first_name);
                $vcard->addAddress(null, null, $this->link->settings->vcard_street, $this->link->settings->vcard_city, $this->link->settings->vcard_region, $this->link->settings->vcard_zip, $this->link->settings->vcard_country);
                $vcard->addEmail($this->link->settings->vcard_email);
                $vcard->addURL($this->link->settings->vcard_url);
                $vcard->addCompany($this->link->settings->vcard_company);
                $vcard->addJobtitle($this->link->settings->vcard_job_title);
                $vcard->addBirthday($this->link->settings->vcard_birthday);
                $vcard->addNote($this->link->settings->vcard_note);

                /* Phone numbers */
                foreach($this->link->settings->vcard_phone_numbers as $phone_number) {
                    $phone_number = htmlspecialchars_decode($phone_number, ENT_QUOTES);
                    $vcard->addPhoneNumber($phone_number);
                }

                /* Socials */
                foreach($this->link->settings->vcard_socials as $social) {
                    $social->value = htmlspecialchars_decode($social->value, ENT_QUOTES);
                    $social->label = htmlspecialchars_decode($social->label, ENT_QUOTES);

                    $vcard->addURL(
                        $social->value,
                        'TYPE=' . $social->label
                    );
                }

                $vcard->setFilename($this->link->settings->vcard_last_name . ' ' . $this->link->settings->vcard_first_name);
                $vcard->download();
                die();
            } else if($this->link->type == 'event') {

                foreach(['event_name', 'event_location', 'event_url', 'event_note', 'event_start_datetime', 'event_end_datetime', 'event_timezone'] as $key) {
                    $this->link->settings->{$key} = htmlspecialchars_decode($this->link->settings->{$key}, ENT_QUOTES);
                }

                /* Generate the event */
                $event = \Spatie\IcalendarGenerator\Components\Calendar::create()
                    ->name($this->link->settings->event_name)
                    ->event(
                        \Spatie\IcalendarGenerator\Components\Event::create()
                            ->name($this->link->settings->event_name)
                            ->address($this->link->settings->event_location)
                            ->url($this->link->settings->event_url)
                            ->description($this->link->settings->event_note)
                            ->startsAt(new \DateTime($this->link->settings->event_start_datetime, new \DateTimeZone($this->link->settings->event_timezone)))
                            ->endsAt(new \DateTime($this->link->settings->event_end_datetime, new \DateTimeZone($this->link->settings->event_timezone)))
                    )
                    ->get();

                /* Download the event file */
                header('Content-Type: text/calendar; charset=utf-8');
                header('Content-Disposition: attachment; filename=' . get_slug($this->link->settings->event_name) . '.ics');
                echo $event;
                die();
            } else {
                $this->process_redirect();
            }

        }

    }

    private function create_statistics() {

        $cookie_name = 's_statistics_' . ($this->type == 'link' ? $this->link->link_id : $this->link->biolink_block_id);

        if(isset($_COOKIE[$cookie_name]) && (int) $_COOKIE[$cookie_name] >= 3) {
            return;
        }

        if($this->is_preview) {
            return;
        }

        /* Detect extra details about the user */
        $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);

        /* Do not track bots */
        if($whichbrowser->device->type == 'bot') {
            return;
        }

        /* Detect extra details about the user */
        $browser_name = $whichbrowser->browser->name ?? null;
        $os_name = $whichbrowser->os->name ?? null;
        $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
        $device_type = get_device_type($_SERVER['HTTP_USER_AGENT']);
        $is_unique = isset($_COOKIE[$cookie_name]) ? 0 : 1;

        /* Detect the location */
        try {
            $maxmind = (new Reader(APP_PATH . 'includes/GeoLite2-City.mmdb'))->get(get_ip());
        } catch(\Exception $exception) {
            /* :) */
        }
        $country_code = isset($maxmind) && isset($maxmind['country']) ? $maxmind['country']['iso_code'] : null;
        $city_name = isset($maxmind) && isset($maxmind['city']) ? $maxmind['city']['names']['en'] : null;

        /* Process referrer */
        $referrer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : null;

        if(!isset($referrer)) {
            $referrer = [
                'host' => null,
                'path' => null
            ];
        }

        /* Check if the referrer comes from the same location */
        if($this->type == 'link' && isset($referrer) && isset($referrer['host']) && $referrer['host'] == parse_url($this->link->full_url)['host']) {
            $is_unique = 0;

            $referrer = [
                'host' => null,
                'path' => null
            ];
        }

        /* Check if referrer actually comes from the QR code */
        if(isset($_GET['referrer']) && $_GET['referrer'] == 'qr') {
            $referrer = [
                'host' => 'qr',
                'path' => null
            ];
        }

        $utm_source = $_GET['utm_source'] ?? null;
        $utm_medium = $_GET['utm_medium'] ?? null;
        $utm_campaign = $_GET['utm_campaign'] ?? null;

        /* Insert the log */
        db()->insert('track_links', [
            'user_id' => $this->user->user_id,
            'link_id' => $this->type == 'link' ? $this->link->link_id : null,
            'biolink_block_id' => $this->type == 'biolink_block' ? $this->link->biolink_block_id : null,
            'country_code' => $country_code,
            'city_name' => $city_name,
            'os_name' => $os_name,
            'browser_name' => $browser_name,
            'referrer_host' => $referrer['host'],
            'referrer_path' => $referrer['path'],
            'device_type' => $device_type,
            'browser_language' => $browser_language,
            'utm_source' => $utm_source,
            'utm_medium' => $utm_medium,
            'utm_campaign' => $utm_campaign,
            'is_unique' => $is_unique,
            'datetime' => Date::$date
        ]);

        /* Add the unique hit to the link table as well */
        if(in_array($this->type, ['link', 'file', 'vcard'])) {
            db()->where('link_id', $this->link->link_id)->update('links', ['clicks' => db()->inc()]);
        } else {
            db()->where('biolink_block_id', $this->link->biolink_block_id)->update('biolinks_blocks', ['clicks' => db()->inc()]);
        }

        /* Set cookie to try and avoid multiple entrances */
        $cookie_new_value = isset($_COOKIE[$cookie_name]) ? (int) $_COOKIE[$cookie_name] + 1 : 0;
        setcookie($cookie_name, (int) $cookie_new_value, time()+60*60*24*1);
    }

    public function process_biolink() {

        /* Check for a leap link */
        if($this->link->settings->leap_link && $this->user->plan_settings->leap_link && !$this->is_preview) {
            $this->redirect_to($this->link->settings->leap_link);
            return;
        }

        /* Get all the links inside of the biolink */
        $cache_instance = \Altum\Cache::$adapter->getItem('link?link_id=' . $this->link->link_id);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            $result = database()->query("SELECT * FROM `biolinks_blocks` WHERE `link_id` = {$this->link->link_id} AND `is_enabled` = 1 ORDER BY `order` ASC");
            $biolink_blocks = [];

            while($row = $result->fetch_object()) {
                $biolink_blocks[] = $row;
            }

            \Altum\Cache::$adapter->save($cache_instance->set($biolink_blocks)->expiresAfter(CACHE_DEFAULT_SECONDS));

        } else {

            /* Get cache */
            $biolink_blocks = $cache_instance->get();

        }

        /* Default basic title */
        Title::set($this->link->url);

        /* Set the meta tags */
        if($this->user->plan_settings->seo) {
            if($this->link->settings->seo->title) Title::set($this->link->settings->seo->title, true);
            Meta::set_description(string_truncate($this->link->settings->seo->meta_description, 200));
            Meta::set_social_url($this->link->full_url);
            Meta::set_social_title($this->link->settings->seo->title);
            Meta::set_social_description(string_truncate($this->link->settings->seo->meta_description, 200));
            Meta::set_social_image(!empty($this->link->settings->seo->image) ? UPLOADS_FULL_URL . 'block_images/' . $this->link->settings->seo->image : null);
        }

        if(count($this->link->pixels_ids)) {
            /* Get the needed pixels */
            $pixels = (new \Altum\Models\Pixel())->get_pixels_by_pixels_ids_and_user_id($this->link->pixels_ids, $this->link->user_id);

            /* Prepare the pixels view */
            $pixels_view = new \Altum\View('l/partials/pixels');
            $this->add_view_content('pixels', $pixels_view->run(['pixels' => $pixels, 'type' => 'biolink']));
        }

        /* Prepare the View */
        $view_content = \Altum\Link::get_biolink($this, $this->link, $this->user, $biolink_blocks);

        $this->add_view_content('content', $view_content);
    }

    public function process_redirect() {

        /* Check if we should redirect the user or kill the script */
        if(isset($_GET['no_redirect'])) {
            die();
        }

        /* Check for targeting */
        if(isset($this->link->settings->targeting_type)) {
            if ($this->link->settings->targeting_type == 'country_code') {
                /* Detect the location */
                try {
                    $maxmind = (new Reader(APP_PATH . 'includes/GeoLite2-Country.mmdb'))->get(get_ip());
                } catch (\Exception $exception) {
                    /* :) */
                }
                $country_code = isset($maxmind) && isset($maxmind['country']) ? $maxmind['country']['iso_code'] : null;

                foreach ($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                    if ($country_code == $value->key) {
                        $this->redirect_to($value->value);
                    }
                }
            }

            if ($this->link->settings->targeting_type == 'device_type') {
                $device_type = get_device_type($_SERVER['HTTP_USER_AGENT']);

                foreach ($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                    if ($device_type == $value->key) {
                        $this->redirect_to($value->value);
                    }
                }
            }

            if ($this->link->settings->targeting_type == 'browser_language') {
                $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;

                foreach ($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                    if ($browser_language == $value->key) {
                        $this->redirect_to($value->value);
                    }
                }
            }

            if ($this->link->settings->targeting_type == 'rotation') {
                $total_chances = 0;

                foreach ($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                    $total_chances += $value->key;
                }

                $chosen_winner = rand(0, $total_chances);

                $start = 0;
                $end = 0;

                foreach ($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                    $end += $value->key;

                    if ($chosen_winner >= $start && $chosen_winner <= $end) {
                        $this->redirect_to($value->value);
                    }

                    $start += $value->key;
                }
            }

            if ($this->link->settings->targeting_type == 'os_name') {
                /* Detect extra details about the user */
                $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
                $os_name = $whichbrowser->os->name ?? null;

                foreach ($this->link->settings->{'targeting_' . $this->link->settings->targeting_type} as $value) {
                    if ($os_name == $value->key) {
                        $this->redirect_to($value->value);
                    }
                }
            }
        }

        /* :) */
        switch ($this->link->type) {
            case 'link':
                $this->redirect_to($this->link->location_url );
                break;

            case 'file':
                $this->redirect_to(UPLOADS_FULL_URL . 'files/' . $this->link->settings->file);
                break;
        }
    }

    private function redirect_to($location_url) {
        if(count($this->link->pixels_ids)) {

            /* Get the needed pixels */
            $pixels = (new \Altum\Models\Pixel())->get_pixels_by_pixels_ids_and_user_id($this->link->pixels_ids, $this->link->user_id);

            /* Prepare the pixels view */
            $pixels_view = new \Altum\View('l/partials/pixels');
            $this->add_view_content('pixels', $pixels_view->run(['pixels' => $pixels, 'type' => 'link']));

            /* Prepare the view */
            $pixels_redirect_wrapper = new \Altum\View('l/pixels_redirect_wrapper', (array) $this);
            echo $pixels_redirect_wrapper->run(['location_url' => $location_url]);
            die();

        } else {
            header('Location: ' . $location_url, true, 301); die();
        }
    }

    public function mail() {
        if(empty($_POST)) {
            die();
        }

        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['email'] = mb_substr(trim(query_clean($_POST['email'])), 0, 320);
        $_POST['name'] = mb_substr(trim(query_clean($_POST['name'])), 0, 32);

        if(settings()->captcha->biolink_is_enabled && settings()->captcha->type != 'basic' && !(new Captcha())->is_valid()) {
            Response::json(l('global.error_message.invalid_captcha'), 'error');
        }

        /* Get the link data */
        $biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('type', 'mail')->getOne('biolinks_blocks', ['biolink_block_id', 'link_id', 'type', 'settings']);

        if(!$biolink_block) {
            die();
        }

        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Get biolink data */
        $link = db()->where('link_id', $biolink_block->link_id)->getOne('links');

        /* Get the user data */
        $user = db()->where('user_id', $link->user_id)->getOne('users');

        $data = [
            'email' => $_POST['email'],
            'name' => $_POST['name'],
        ];

        /* Store the data */
        db()->insert('data', [
            'biolink_block_id' => $biolink_block->biolink_block_id,
            'link_id' => $link->link_id,
            'project_id' => $link->project_id,
            'user_id' => $link->user_id,
            'type' => $biolink_block->type,
            'data' => json_encode($data),
            'datetime' => Date::$date,
        ]);

        /* Send email notifications if needed to the owner */
        if($biolink_block->settings->email_notification) {
            $email_template = get_email_template(
                [
                    '{{BLOCK_TITLE}}' => $biolink_block->settings->name,
                ],
                l('global.emails.user_data_collected.subject', $user->language),
                [
                    '{{NAME}}' => $user->name,
                    '{{DATA_EMAIL}}' => $_POST['email'],
                    '{{DATA_NAME}}' => $_POST['name'],
                    '{{DATA_LINK}}' => url('data'),
                ],
                l('global.emails.user_data_collected_mail.body', $user->language)
            );

            send_mail($biolink_block->settings->email_notification, $email_template->subject, $email_template->body, ['anti_phishing_code' => $user->anti_phishing_code, 'language' => $user->language]);
        }

        /* Send the webhook */
        if($biolink_block->settings->webhook_url) {
            $body = \Unirest\Request\Body::form([
                'email' => $_POST['email'],
                'name' => $_POST['name'],
            ]);

            $response = \Unirest\Request::post($biolink_block->settings->webhook_url, [], $body);
        }

        /* Send the email to mailchimp */
        if($biolink_block->settings->mailchimp_api && $biolink_block->settings->mailchimp_api_list) {

            /* Check the mailchimp api list and get data */
            $explode = explode('-', $biolink_block->settings->mailchimp_api);

            if(count($explode) < 2) {
                die();
            }

            $dc = $explode[1];
            $url = 'https://' . $dc . '.api.mailchimp.com/3.0/lists/' . $biolink_block->settings->mailchimp_api_list . '/members';

            /* Try to subscribe the user to mailchimp list */
            \Unirest\Request::auth('altum', $biolink_block->settings->mailchimp_api);

            $body = \Unirest\Request\Body::json([
                'email_address' => $_POST['email'],
                'status' => 'subscribed',
                'merge_fields' => [
                    'FNAME' => $_POST['name']
                ],
            ]);

            \Unirest\Request::post(
                $url,
                [],
                $body
            );

        }

        Response::json($biolink_block->settings->success_text, 'success', ['thank_you_url' => $biolink_block->settings->thank_you_url]);
    }

    public function phone_collector() {
        if(empty($_POST)) {
            die();
        }

        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['phone'] = mb_substr(trim(query_clean($_POST['phone'])), 0, 32);
        $_POST['name'] = mb_substr(trim(query_clean($_POST['name'])), 0, 32);

        if(settings()->captcha->biolink_is_enabled && settings()->captcha->type != 'basic' && !(new Captcha())->is_valid()) {
            Response::json(l('global.error_message.invalid_captcha'), 'error');
        }

        /* Get the link data */
        $biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('type', 'phone_collector')->getOne('biolinks_blocks', ['biolink_block_id', 'link_id', 'type', 'settings']);

        if(!$biolink_block) {
            die();
        }

        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Get biolink data */
        $link = db()->where('link_id', $biolink_block->link_id)->getOne('links');

        /* Get the user data */
        $user = db()->where('user_id', $link->user_id)->getOne('users');

        $data = [
            'phone' => $_POST['phone'],
            'name' => $_POST['name'],
        ];

        /* Store the data */
        db()->insert('data', [
            'biolink_block_id' => $biolink_block->biolink_block_id,
            'link_id' => $link->link_id,
            'project_id' => $link->project_id,
            'user_id' => $link->user_id,
            'type' => $biolink_block->type,
            'data' => json_encode($data),
            'datetime' => Date::$date,
        ]);

        /* Send email notifications if needed to the owner */
        if($biolink_block->settings->email_notification) {
            $email_template = get_email_template(
                [
                    '{{BLOCK_TITLE}}' => $biolink_block->settings->name,
                ],
                l('global.emails.user_data_collected.subject', $user->language),
                [
                    '{{NAME}}' => $user->name,
                    '{{DATA_PHONE}}' => $_POST['phone'],
                    '{{DATA_NAME}}' => $_POST['name'],
                    '{{DATA_LINK}}' => url('data'),
                ],
                l('global.emails.user_data_collected_phone_collector.body', $user->language)
            );

            send_mail($biolink_block->settings->email_notification, $email_template->subject, $email_template->body, ['anti_phishing_code' => $user->anti_phishing_code, 'language' => $user->language]);
        }

        /* Send the webhook */
        if($biolink_block->settings->webhook_url) {
            $body = \Unirest\Request\Body::form([
                'phone' => $_POST['phone'],
                'name' => $_POST['name'],
            ]);

            $response = \Unirest\Request::post($biolink_block->settings->webhook_url, [], $body);
        }

        Response::json($biolink_block->settings->success_text, 'success', ['thank_you_url' => $biolink_block->settings->thank_you_url]);
    }

    public function payment_generator() {
        if(empty($_POST)) {
            die();
        }

        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['payment_processor_id'] = (int) $_POST['payment_processor_id'];

        /* Get the link data */
        $biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->getOne('biolinks_blocks');

        if(!$biolink_block) {
            die();
        }

        if(!in_array($biolink_block->type, ['donation', 'product', 'service'])) {
            die();
        }

        $biolink_block->settings = json_decode($biolink_block->settings);

        if(!in_array($_POST['payment_processor_id'], $biolink_block->settings->payment_processors_ids)) {
            die();
        }

        /* Get biolink data */
        $link = db()->where('link_id', $biolink_block->link_id)->getOne('links');

        /* Determine the full url of the biolink page */
        if($link->domain_id) {
            $domain = (new Domain())->get_domain($link->domain_id);
            $link->full_url = $domain->scheme . $domain->host . '/' . $link->url;
        } else {
            $link->full_url = SITE_URL . $link->url;
        }

        /* Get the payment processor */
        $payment_processors = (new \Altum\Models\PaymentProcessor())->get_payment_processors_by_user_id($biolink_block->user_id);
        $payment_processor = $payment_processors[$_POST['payment_processor_id']];

        /* Prepare the data */
        $data = [];
        $price = null;
        $email = null;
        $name = null;

        switch($biolink_block->type) {
            case 'donation':
                $price = $_POST['amount'] = (float) $_POST['amount'];
                $data['message'] = $_POST['message'] = mb_substr(trim(query_clean($_POST['message'] ?? null)), 0, 256);
                break;

            case 'product':
                $price = $_POST['price'] = (float) $_POST['price'];
                $email = $_POST['email'] = mb_substr(trim(query_clean($_POST['email'] ?? null)), 0, 320);
                break;

            case 'service':
                $price = $_POST['price'] = (float) $_POST['price'];
                $email = $_POST['email'] = mb_substr(trim(query_clean($_POST['email'] ?? null)), 0, 320);
                $data['message'] = $_POST['message'] = mb_substr(trim(query_clean($_POST['message'] ?? null)), 0, 256);
                break;
        }

        /* Insert the guest payment in a pending state */
        $guest_payment_id = db()->insert('guests_payments', [
            'biolink_block_id' => $biolink_block->biolink_block_id,
            'link_id' => $biolink_block->link_id,
            'payment_processor_id' => $payment_processor->payment_processor_id,
            'project_id' => $link->project_id,
            'user_id' => $biolink_block->user_id,
            'type' => $biolink_block->type,
            'processor' => $payment_processor->processor,
            'name' => $name,
            'email' => $email,
            'data' => json_encode($data),
            'datetime' => \Altum\Date::$date
        ]);

        /* Start generating the payment */
        switch($payment_processor->processor) {
            case 'paypal':

                /* Initiate PayPal */
                \Unirest\Request::auth($payment_processor->settings->client_id, $payment_processor->settings->secret);

                /* Get API URL */
                $paypal_api_url = $payment_processor->settings->mode == 'live' ? 'https://api-m.paypal.com/' : 'https://api-m.sandbox.paypal.com/';

                /* Try to get access token */
                $response = \Unirest\Request::post($paypal_api_url . 'v1/oauth2/token', [], \Unirest\Request\Body::form(['grant_type' => 'client_credentials']));

                /* Check against errors */
                if($response->code >= 400) {
                    /* Delete inserted pending payment on error */
                    db()->where('guest_payment_id', $guest_payment_id)->delete('guests_payments');
                    Response::json($response->body->error . ':' . $response->body->error_description, 'error');
                }

                $paypal_access_token = $response->body->access_token;

                /* Set future request headers */
                $paypal_headers = [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $paypal_access_token
                ];

                $price = in_array($biolink_block->settings->currency, ['JPY', 'TWD', 'HUF']) ? number_format($price, 0, '.', '') : number_format($price, 2, '.', '');

                /* Metadata */
                $custom_id = $guest_payment_id;

                /* Create an order */
                $response = \Unirest\Request::post($paypal_api_url . 'v2/checkout/orders', $paypal_headers, \Unirest\Request\Body::json([
                    'intent' => 'CAPTURE',
                    'purchase_units' => [[
                        'amount' => [
                            'currency_code' => $biolink_block->settings->currency,
                            'value' => $price,
                            'breakdown' => [
                                'item_total' => [
                                    'currency_code' => $biolink_block->settings->currency,
                                    'value' => $price
                                ]
                            ]
                        ],
                        'description' => $biolink_block->settings->description,
                        'custom_id' => $custom_id,
                        'items' => [[
                            'name' => $biolink_block->settings->title,
                            'description' => $biolink_block->settings->description,
                            'quantity' => 1,
                            'unit_amount' => [
                                'currency_code' => $biolink_block->settings->currency,
                                'value' => $price
                            ]
                        ]]
                    ]],
                    'application_context' => [
                        'brand_name' => $biolink_block->settings->title,
                        'landing_page' => 'NO_PREFERENCE',
                        'shipping_preference' => 'NO_SHIPPING',
                        'user_action' => 'PAY_NOW',
                        'return_url' => $link->full_url . '?payment_thank_you=' . $biolink_block->type . '&biolink_block_id=' . $biolink_block->biolink_block_id,
                        'cancel_url' => $link->full_url . '?payment_cancelled=' . $biolink_block->type . '&biolink_block_id=' . $biolink_block->biolink_block_id,
                    ]
                ]));

                /* Check against errors */
                if($response->code >= 400) {
                    /* Delete inserted pending payment on error */
                    db()->where('guest_payment_id', $guest_payment_id)->delete('guests_payments');
                    Response::json($response->body->error . ':' . $response->body->error_description, 'error');
                }

                $checkout_url = $response->body->links[1]->href;

                break;

            case 'stripe':

                /* Initiate Stripe */
                \Stripe\Stripe::setApiKey($payment_processor->settings->secret_key);
                \Stripe\Stripe::setApiVersion('2020-08-27');

                /* Final price */
                $stripe_formatted_price = in_array($biolink_block->settings->currency, ['MGA', 'BIF', 'CLP', 'PYG', 'DJF', 'RWF', 'GNF', 'UGX', 'JPY', 'VND', 'VUV', 'XAF', 'KMF', 'KRW', 'XOF', 'XPF']) ? number_format($price, 0, '.', '') : number_format($price, 2, '.', '') * 100;

                /* Generate the stripe session */
                try {
                    $stripe_session = \Stripe\Checkout\Session::create([
                        'payment_method_types' => ['card'],
                        'line_items' => [[
                            'name' => $biolink_block->settings->title,
                            'description' => $biolink_block->settings->description,
                            'amount' => $stripe_formatted_price,
                            'currency' => $biolink_block->settings->currency,
                            'quantity' => 1,
                        ]],
                        'metadata' => [
                            'guest_payment_id' => $guest_payment_id,
                        ],
                        'success_url' => $link->full_url . '?payment_thank_you=' . $biolink_block->type . '&biolink_block_id=' . $biolink_block->biolink_block_id,
                        'cancel_url' => $link->full_url . '?payment_cancelled=' . $biolink_block->type . '&biolink_block_id=' . $biolink_block->biolink_block_id,
                    ]);
                } catch (\Exception $exception) {
                    /* Delete inserted pending payment on error */
                    db()->where('guest_payment_id', $guest_payment_id)->delete('guests_payments');
                    Response::json($exception->getCode() . ':' . $exception->getMessage(), 'error');
                }

                $checkout_url = $stripe_session->url;

                break;

            case 'crypto_com':

                \Unirest\Request::auth($payment_processor->settings->secret_key, '');

                /* Final price */
                $price = number_format($price, 2, '.', '') * 100;

                $response = \Unirest\Request::post(
                    'https://pay.crypto.com/api/payments',
                    [],
                    \Unirest\Request\Body::Form([
                        'description' => $biolink_block->settings->title,
                        'amount' => $price,
                        'currency' => $biolink_block->settings->currency,
                        'metadata' => [
                            'guest_payment_id' => $guest_payment_id,
                        ],
                        'return_url' => $link->full_url . '?payment_thank_you=' . $biolink_block->type . '&biolink_block_id=' . $biolink_block->biolink_block_id,
                        'cancel_url' => $link->full_url . '?payment_cancelled=' . $biolink_block->type . '&biolink_block_id=' . $biolink_block->biolink_block_id,
                    ])
                );

                /* Check against errors */
                if($response->code >= 400) {
                    /* Delete inserted pending payment on error */
                    db()->where('guest_payment_id', $guest_payment_id)->delete('guests_payments');
                    Response::json($response->body->error->type . ':' . $response->body->error->error_message, 'error');
                }

                $checkout_url = $response->body->payment_url;

                break;

            case 'razorpay':

                $razorpay = new Api($payment_processor->settings->key_id, $payment_processor->settings->key_secret);

                /* Final price */
                $price = number_format($price, 2, '.', '') * 100;

                try {
                    $response = $razorpay->paymentLink->create([
                        'amount' => $price,
                        'currency' => $biolink_block->settings->currency,
                        'accept_partial' => false,
                        'description' => $biolink_block->settings->description,
                        'customer' => [
                            'name' => '',
                            'email' => '',
                        ],
                        'notify' => [
                            'sms' => false,
                            'email' => false,
                        ],
                        'reminder_enable' => false,
                        'notes' => [
                            'guest_payment_id' => $guest_payment_id,
                        ],
                        'callback_url' => $link->full_url . '?payment_thank_you=' . $biolink_block->type . '&biolink_block_id=' . $biolink_block->biolink_block_id,
                        'callback_method' => 'get'
                    ]);
                } catch (\Exception $exception) {
                    /* Delete inserted pending payment on error */
                    db()->where('guest_payment_id', $guest_payment_id)->delete('guests_payments');
                    Response::json($exception->getCode() . ':' . $exception->getMessage(), 'error');
                }

                $checkout_url = $response['short_url'];

                break;

            case 'paystack':

                Paystack::$secret_key = $payment_processor->settings->secret_key;

                /* Final price */
                $price = (int) number_format($price, 2, '.', '') * 100;

                $response = \Unirest\Request::post(Paystack::$api_url . 'transaction/initialize', Paystack::get_headers(), \Unirest\Request\Body::json([
                    'key' => $payment_processor->settings->public_key,
                    'amount' => $price,
                    'currency' => $biolink_block->settings->currency,
                    'metadata' => [
                        'guest_payment_id' => $guest_payment_id,
                    ],
                    'email' => 'hey@example.com',
                    'callback_url' => $link->full_url . '?payment_thank_you=' . $biolink_block->type . '&biolink_block_id=' . $biolink_block->biolink_block_id,
                ]));

                if(!$response->body->status) {
                    /* Delete inserted pending payment on error */
                    db()->where('guest_payment_id', $guest_payment_id)->delete('guests_payments');
                    Response::json($response->body->message, 'error');
                }

                $checkout_url = $response->body->data->authorization_url;

                break;
        }

        /* Return the checkout URL */
        Response::json('', 'success', ['checkout_url' => $checkout_url]);

        die();
    }

}
