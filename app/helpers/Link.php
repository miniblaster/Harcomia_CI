<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum;

class Link {

    public static function get_biolink($tthis, $link, $user = null, $biolink_blocks = null) {

        /* Determine the background of the biolink */
        $link->design = new \StdClass();
        $link->design->background_class = '';
        $link->design->background_style = '';

        /* Check if the user has the access needed from the plan */
        if(!$user->plan_settings->custom_backgrounds && in_array($link->settings->background_type, ['image', 'gradient', 'color'])) {

            /* Revert to a default if no access */
            $link->settings->background_type = 'preset';
            $link->settings->background = 'one';

        }

        if(isset($tthis->biolink_theme) && $tthis->biolink_theme) {
            $link->settings = (object) array_merge((array) $link->settings, (array) $tthis->biolink_theme->settings->biolink);
        }

        switch($link->settings->background_type) {
            case 'image':

                $link->design->background_style = 'background: url(\'' . UPLOADS_FULL_URL . 'backgrounds/' . $link->settings->background . '\');';

                break;

            case 'gradient':

                $link->design->background_style = 'background-image: linear-gradient(135deg, ' . $link->settings->background_color_one . ' 10%, ' . $link->settings->background_color_two . ' 100%);';

                break;

            case 'color':

                $link->design->background_style = 'background: ' . $link->settings->background . ';';

                break;

            case 'preset':
            case 'preset_abstract':
                $biolink_backgrounds = require APP_PATH . 'includes/biolink_backgrounds.php';
                $link->design->background_style = $biolink_backgrounds[$link->settings->background_type][$link->settings->background];

                break;
        }

        /* Determine the color of the header text */
        $link->design->text_style = 'color: ' . $link->settings->text_color;

        /* Determine the notification branding settings */
        if($user && !$user->plan_settings->removable_branding && !$link->settings->display_branding) {
            $link->settings->display_branding = true;
        }

        if($user && $user->plan_settings->removable_branding && !$link->settings->display_branding) {
            $link->settings->display_branding = false;
        }

        /* Check if we can show the custom branding if available */
        if(isset($link->settings->branding, $link->settings->branding->name, $link->settings->branding->url) && !$user->plan_settings->custom_branding) {
            $link->settings->branding = false;
        }

        /* Prepare the View */
        $data = [
            'link'  => $link,
            'user'  => $user,
            'biolink_blocks' => $biolink_blocks
        ];

        $view = new \Altum\View('l/partials/biolink', (array) $tthis);

        return $view->run($data);

    }

    public static function get_biolink_link($link, $user = null, $biolink_theme = null) {

        $data = [];

        /* Require different files for different types of links available */
        switch($link->type) {
            case 'link':
            case 'mail':
            case 'rss_feed':
            case 'vcard':
            case 'file':
            case 'cta':
            case 'share':
            case 'youtube_feed':
            case 'paypal':
            case 'phone_collector':
            case 'donation':
            case 'product':
            case 'service':
            case 'faq':
            case 'list':
            case 'alert':

                /* Check if the user has the access needed from the plan */
                if(!$user->plan_settings->custom_colored_links) {
                    /* Revert to a default if no access */
                    $link->settings->background_color = 'white';
                    $link->settings->text_color = 'black';
                    $link->settings->border_color = 'black';
                }

                if(isset($link->settings->outline) && $link->settings->outline) {
                    $link->settings->border_color = $link->settings->background_color;
                    $link->settings->background_color = 'transparent';
                }

                if($biolink_theme) {
                    $link->settings = (object) array_merge((array)$link->settings, (array)$biolink_theme->settings->biolink_block);
                }

                /* Determine the css and styling of the button */
                $link->design = new \StdClass();
                $link->design->link_class = '';
                $link->design->link_style =
                    'background: ' . $link->settings->background_color . ';'
                    . 'color: ' . $link->settings->text_color . ';'
                    . 'border-width: ' . ($link->settings->border_width ?? '1') . 'px;'
                    . 'border-color: ' . ($link->settings->border_color ?? 'transparent') . ';'
                    . 'border-style: ' . ($link->settings->border_style ?? 'solid') . ';'
                    . 'text-align: ' . ($link->settings->text_alignment ?? 'center') . ';';

                /* Animation */
                if(isset($link->settings->animation)) {
                    $link->design->link_class .= ' animate__animated animate__' . $link->settings->animation_runs . ' animate__' . $link->settings->animation . ' animate__delay-2s';
                }

                /* UTM Parameters */
                $link->utm_query = null;
                if($user->plan_settings->utm && $link->utm->medium && $link->utm->source) {
                    $link->utm_query = '?utm_medium=' . $link->utm->medium . '&utm_source=' . $link->utm->source . '&utm_campaign=' . $link->settings->name;
                }

                /* Call to action custom link */
                if($link->type == 'cta') {
                    switch($link->settings->type) {
                        case 'email':
                            $link->location_url = 'mailto:' . $link->settings->value;
                            break;
                        case 'call':
                            $link->location_url = 'tel:' . $link->settings->value;
                            break;
                        case 'sms':
                            $link->location_url = 'sms:' . $link->settings->value;
                            break;
                        case 'facetime':
                            $link->location_url = 'facetime:' . $link->settings->value;
                            break;
                    }
                }

                /* Generate paypal payment link */
                if($link->type == 'paypal') {
                    $paypal_type = [
                        'buy_now' => '_xclick',
                        'add_to_cart' => '_cart',
                        'donation' => '_donations'
                    ];

                    if($link->settings->type == 'add_to_cart') {
                        $link->location_url = sprintf('https://www.paypal.com/cgi-bin/webscr?business=%s&cmd=%s&currency_code=%s&amount=%s&item_name=%s&button_subtype=products&add=1&return=%s&cancel_return=%s', $link->settings->email, $paypal_type[$link->settings->type], $link->settings->currency, $link->settings->price, $link->settings->title, $link->settings->thank_you_url, $link->settings->cancel_url);
                    } else {
                        $link->location_url = sprintf('https://www.paypal.com/cgi-bin/webscr?business=%s&cmd=%s&currency_code=%s&amount=%s&item_name=%s&return=%s&cancel_return=%s', $link->settings->email, $paypal_type[$link->settings->type], $link->settings->currency, $link->settings->price, $link->settings->title, $link->settings->thank_you_url, $link->settings->cancel_url);
                    }
                }

                /* Get payment processors */
                if(in_array($link->type, ['donation', 'product', 'service'])) {
                    $data['payment_processors'] = (new \Altum\Models\PaymentProcessor())->get_payment_processors_by_user_id($user->user_id);
                }

                $biolink_blocks = require APP_PATH . 'includes/biolink_blocks.php';

                if($biolink_blocks[$link->type]['type'] == 'default') {
                    $view_path = THEME_PATH . 'views/l/biolink_blocks/' . $link->type . '.php';
                } else {
                    $view_path = \Altum\Plugin::get($biolink_blocks[$link->type]['type'] . '-blocks')->path . 'views/l/biolink_blocks/' . $link->type . '.php';
                }

                break;

            case 'heading':
            case 'paragraph':

                /* Check if the user has the access needed from the plan */
                if(!$user->plan_settings->custom_colored_links) {
                    /* Revert to a default if no access */
                    $link->settings->text_color = 'white';
                }

                $view_path = THEME_PATH . 'views/l/biolink_blocks/' . $link->type . '.php';

                break;

            case 'socials':
                $view_path = THEME_PATH . 'views/l/biolink_blocks/' . $link->type . '.php';
                break;

            case 'avatar':
                $view_path = THEME_PATH . 'views/l/biolink_blocks/' . $link->type . '.php';
                break;

            case 'image':
            case 'image_grid':
            case 'map':
            case 'image_slider':

                /* UTM Parameters */
                $link->utm_query = null;
                if($user->plan_settings->utm && $link->utm->medium && $link->utm->source) {
                    $link->utm_query = '?utm_medium=' . $link->utm->medium . '&utm_source=' . $link->utm->source . '&utm_campaign=' . $link->settings->name;
                }

                $biolink_blocks = require APP_PATH . 'includes/biolink_blocks.php';

                if($biolink_blocks[$link->type]['type'] == 'default') {
                    $view_path = THEME_PATH . 'views/l/biolink_blocks/' . $link->type . '.php';
                } elseif($biolink_blocks[$link->type]['type'] == 'pro') {
                    $view_path = \Altum\Plugin::get('pro-blocks')->path . 'views/l/biolink_blocks/' . $link->type . '.php';
                } elseif($biolink_blocks[$link->type]['type'] == 'ultimate') {
                    $view_path = \Altum\Plugin::get('ultimate-blocks')->path . 'views/l/biolink_blocks/' . $link->type . '.php';
                }

                break;

            case 'youtube':

                if(preg_match('/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((?:\w|-){11})(?:&list=(\S+))?$/', $link->location_url, $match)) {
                    $data['embed'] = $match[1];

                    $view_path = THEME_PATH . 'views/l/biolink_blocks/' . $link->type . '.php';
                }

                break;

            case 'soundcloud':

                if(preg_match('/(soundcloud\.com)/', $link->location_url)) {
                    $data['embed'] = $link->location_url;

                    $view_path = THEME_PATH . 'views/l/biolink_blocks/' . $link->type . '.php';
                }

                break;

            case 'vimeo':

                if(preg_match('/https:\/\/(player\.)?vimeo\.com(\/video)?\/(\d+)/', $link->location_url, $match)) {
                    $data['embed'] = $match[3];

                    $view_path = THEME_PATH . 'views/l/biolink_blocks/' . $link->type . '.php';
                }

                break;

            case 'twitch':

                if(preg_match('/^(?:https?:\/\/)?(?:www\.)?(?:twitch\.tv\/)(.+)$/', $link->location_url, $match)) {
                    $data['embed'] = $match[1];

                    $view_path = THEME_PATH . 'views/l/biolink_blocks/' . $link->type . '.php';
                }

                break;

            case 'spotify':

                if(preg_match('/^(?:https?:\/\/)?(?:www\.)?(?:open\.)?(?:spotify\.com\/)(album|track|show|episode)+\/(.+)$/', $link->location_url, $match)) {
                    $data['embed_type'] = $match[1];
                    $data['embed_value'] = $match[2];

                    $view_path = THEME_PATH . 'views/l/biolink_blocks/' . $link->type . '.php';
                }

                break;

            case 'tiktok':

                if(preg_match('/^(?:https?:\/\/)?(?:www\.)?(?:tiktok\.com\/.+\/)(.+)$/', $link->location_url, $match)) {
                    $data['embed'] = $match[1];

                    $view_path = THEME_PATH . 'views/l/biolink_blocks/' . $link->type . '.php';
                }

                break;

            case 'applemusic':

                if(preg_match('/(https:\/\/music\.apple\.com)/', $link->location_url)) {

                    $position = mb_strpos($link->location_url, 'music.apple.com');

                    if($position !== false) {
                        $link->location_url = str_replace('music.apple.com', 'embed.music.apple.com', $link->location_url);

                        $view_path = \Altum\Plugin::get('pro-blocks')->path . 'views/l/biolink_blocks/' . $link->type . '.php';
                    }

                }

                break;

            case 'tidal':

                if(preg_match('/(https:\/\/tidal\.com)/', $link->location_url)) {

                    $position = mb_strpos($link->location_url, 'tidal.com');

                    if($position !== false) {
                        $link->location_url = str_replace('tidal.com', 'embed.tidal.com', $link->location_url) . '?disableAnalytics=true';
                        $link->location_url = str_replace('browse/', '', $link->location_url);
                        $link->location_url = str_replace('track/', 'tracks/', $link->location_url);
                        $link->location_url = str_replace('album/', 'albums/', $link->location_url);

                        $view_path = \Altum\Plugin::get('pro-blocks')->path . 'views/l/biolink_blocks/' . $link->type . '.php';
                    }

                }

                break;

            case 'anchor':

                if(preg_match('/(https:\/\/anchor\.fm)/', $link->location_url)) {

                    $position = mb_strpos($link->location_url, '/', 18);

                    if($position !== false) {

                        $link->location_url = substr_replace($link->location_url, '/embed', $position, 0);

                        $view_path = \Altum\Plugin::get('pro-blocks')->path . 'views/l/biolink_blocks/' . $link->type . '.php';
                    }

                }

                break;

            case 'twitter_tweet':

                if(preg_match('/(https:\/\/twitter\.com)/', $link->location_url)) {
                    $view_path = \Altum\Plugin::get('pro-blocks')->path . 'views/l/biolink_blocks/' . $link->type . '.php';
                }

                break;

            case 'instagram_media':

                if(preg_match('/(https:\/\/www.instagram\.com)/', $link->location_url)) {
                    $view_path = \Altum\Plugin::get('pro-blocks')->path . 'views/l/biolink_blocks/' . $link->type . '.php';
                }

                break;

            case 'custom_html':

                $view_path = \Altum\Plugin::get('pro-blocks')->path . 'views/l/biolink_blocks/' . $link->type . '.php';

                break;

            case 'divider':

                $view_path = \Altum\Plugin::get('pro-blocks')->path . 'views/l/biolink_blocks/' . $link->type . '.php';

                break;

            case 'discord':

                $view_path = \Altum\Plugin::get('ultimate-blocks')->path . 'views/l/biolink_blocks/' . $link->type . '.php';

                break;

            case 'facebook':
                $view_path = \Altum\Plugin::get('ultimate-blocks')->path . 'views/l/biolink_blocks/' . $link->type . '.php';
                break;

            case 'reddit':

                $view_path = \Altum\Plugin::get('ultimate-blocks')->path . 'views/l/biolink_blocks/' . $link->type . '.php';

                break;

            case 'audio':

                $view_path = \Altum\Plugin::get('ultimate-blocks')->path . 'views/l/biolink_blocks/' . $link->type . '.php';

                break;

            case 'video':

                $view_path = \Altum\Plugin::get('ultimate-blocks')->path . 'views/l/biolink_blocks/' . $link->type . '.php';

                break;

            case 'countdown':

                $view_path = \Altum\Plugin::get('ultimate-blocks')->path . 'views/l/biolink_blocks/' . $link->type . '.php';

                break;

            case 'external_item':

                /* Check if the user has the access needed from the plan */
                if(!$user->plan_settings->custom_colored_links) {
                    /* Revert to a default if no access */
                    $link->settings->background_color = 'white';
                    $link->settings->text_color = 'black';
                    $link->settings->border_color = 'black';
                }

                /* Determine the css and styling of the button */
                $link->design = new \StdClass();
                $link->design->card_class = '';
                $link->design->card_style = 'background: ' . $link->settings->background_color . ';border-width: ' . $link->settings->border_width . 'px; border-color: ' . $link->settings->border_color . ';border-style: ' . $link->settings->border_style . ';';

                /* Animation */
                if($link->settings->animation) {
                    $link->design->card_class .= ' animate__animated animate__' . $link->settings->animation_runs . ' animate__' . $link->settings->animation . ' animate__delay-2s';
                }

                /* UTM Parameters */
                $link->utm_query = null;
                if($user->plan_settings->utm && $link->utm->medium && $link->utm->source) {
                    $link->utm_query = '?utm_medium=' . $link->utm->medium . '&utm_source=' . $link->utm->source . '&utm_campaign=' . $link->settings->name;
                }

                $view_path = \Altum\Plugin::get('ultimate-blocks')->path . 'views/l/biolink_blocks/' . $link->type . '.php';

                break;

            case 'opensea':

                $view_path = THEME_PATH . 'views/l/biolink_blocks/' . $link->type . '.php';

                break;

            case 'timeline':

                /* Check if the user has the access needed from the plan */
                if(!$user->plan_settings->custom_colored_links) {
                    /* Revert to a default if no access */
                    $link->settings->background_color = '#FFFFFF00';
                    $link->settings->title_color = '#000000';
                    $link->settings->date_color = '#000000';
                    $link->settings->description_color = '#000000';
                    $link->settings->border_color = '#FFFFFF00';
                }

                $view_path = \Altum\Plugin::get('ultimate-blocks')->path . 'views/l/biolink_blocks/' . $link->type . '.php';

                break;

            case 'review':

                /* Check if the user has the access needed from the plan */
                if(!$user->plan_settings->custom_colored_links) {
                    /* Revert to a default if no access */
                    $link->settings->background_color = '#FFFFFF00';
                    $link->settings->title_color = '#000000';
                    $link->settings->description_color = '#000000';
                    $link->settings->author_name_color = '#000000';
                    $link->settings->author_description_color = '#000000';
                    $link->settings->stars_color = '#FFDF00';
                    $link->settings->border_color = '#FFFFFF00';
                }

                $view_path = \Altum\Plugin::get('ultimate-blocks')->path . 'views/l/biolink_blocks/' . $link->type . '.php';

                break;
        }

        if(!isset($view_path)) return null;

        /* Prepare the View */
        $data = array_merge($data, [
            'link'  => $link,
            'user'  => $user
        ]);

        return include_view($view_path, $data);

    }
}
