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
use Altum\Response;
use Unirest\Request;

class BiolinkBlockAjax extends Controller {
    public $biolink_blocks = null;
    public $total_biolink_blocks = 0;

    public function index() {
        \Altum\Authentication::guard();

        if(!empty($_POST) && (\Altum\Csrf::check('token') || \Altum\Csrf::check('global_token')) && isset($_POST['request_type'])) {

            switch($_POST['request_type']) {

                /* Status toggle */
                case 'is_enabled_toggle': $this->is_enabled_toggle(); break;

                /* Duplicate link */
                case 'duplicate': $this->duplicate(); break;

                /* Order links */
                case 'order': $this->order(); break;

                /* Create */
                case 'create': $this->create(); break;

                /* Update */
                case 'update': $this->update(); break;

                /* Delete */
                case 'delete': $this->delete(); break;

            }

        }

        die($_POST['request_type']);
    }

    private function is_enabled_toggle() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];

        /* Get the current status */
        $biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks', ['biolink_block_id', 'link_id', 'is_enabled']);

        if($biolink_block) {
            $new_is_enabled = (int) !$biolink_block->is_enabled;

            db()->where('biolink_block_id', $biolink_block->biolink_block_id)->update('biolinks_blocks', ['is_enabled' => $new_is_enabled]);

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

            Response::json('', 'success');
        }
    }

    public function duplicate() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];

        //ALTUMCODE.DEMO: if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('links');
        }

        /* Get the link data */
        $biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks');

        if(!$biolink_block) {
            redirect('links');
        }

        /* Make sure that the user didn't exceed the limit */
        $this->total_biolink_blocks = database()->query("SELECT COUNT(*) AS `total` FROM `biolinks_blocks` WHERE `user_id` = {$this->user->user_id} AND `link_id` = {$biolink_block->link_id}")->fetch_object()->total;
        if($this->user->plan_settings->biolink_blocks_limit != -1 && $this->total_biolink_blocks >= $this->user->plan_settings->biolink_blocks_limit) {
            Alerts::add_error(l('global.info_message.plan_feature_limit'));
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
            $biolink_block->settings = json_decode($biolink_block->settings);

            /* Duplication of resources */
            switch($biolink_block->type) {
                case 'file':
                case 'audio':
                case 'video':
                    $biolink_block->settings->file = \Altum\Uploads::copy_uploaded_file($biolink_block->settings->file, \Altum\Uploads::get_path('files'), \Altum\Uploads::get_path('files'), 'json_error');
                    break;

                case 'review':
                    $biolink_block->settings->file = \Altum\Uploads::copy_uploaded_file($biolink_block->settings->image, \Altum\Uploads::get_path('block_images'), \Altum\Uploads::get_path('block_images'), 'json_error');
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

            $settings = json_encode($biolink_block->settings);

            /* Database query */
            db()->insert('biolinks_blocks', [
                'user_id' => $this->user->user_id,
                'link_id' => $biolink_block->link_id,
                'type' => $biolink_block->type,
                'location_url' => $biolink_block->location_url,
                'settings' => $settings,
                'order' => $this->total_biolink_blocks,
                'start_date' => $biolink_block->start_date,
                'end_date' => $biolink_block->end_date,
                'is_enabled' => $biolink_block->is_enabled,
                'datetime' => \Altum\Date::$date,
            ]);

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

            /* Set a nice success message */
            Alerts::add_success(l('global.success_message.create2'));

            /* Redirect */
            redirect('link/' . $biolink_block->link_id . '?tab=links');
        }

        redirect('links');
    }

    private function order() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        if(isset($_POST['biolink_blocks']) && is_array($_POST['biolink_blocks'])) {
            foreach($_POST['biolink_blocks'] as $link) {
                if(!isset($link['biolink_block_id']) || !isset($link['order'])) {
                    continue;
                }

                $biolink_block = db()->where('biolink_block_id', $link['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks', ['link_id']);

                if(!$biolink_block) {
                    continue;
                }

                $link['biolink_block_id'] = (int) $link['biolink_block_id'];
                $link['order'] = (int) $link['order'];

                /* Update the link order */
                db()->where('biolink_block_id', $link['biolink_block_id'])->where('user_id', $this->user->user_id)->update('biolinks_blocks', ['order' => $link['order']]);
            }

            if(isset($biolink_block)) {
                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);
            }
        }

        Response::json('', 'success');
    }

    private function create() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $this->biolink_blocks = require APP_PATH . 'includes/biolink_blocks.php';

        /* Check for available biolink blocks */
        if(isset($_POST['block_type']) && array_key_exists($_POST['block_type'], $this->biolink_blocks)) {
            $_POST['block_type'] = query_clean($_POST['block_type']);
            $_POST['link_id'] = (int) $_POST['link_id'];

            /* Make sure that the user didn't exceed the limit */
            $this->total_biolink_blocks = database()->query("SELECT COUNT(*) AS `total` FROM `biolinks_blocks` WHERE `user_id` = {$this->user->user_id} AND `link_id` = {$_POST['link_id']}")->fetch_object()->total;
            if($this->user->plan_settings->biolink_blocks_limit != -1 && $this->total_biolink_blocks >= $this->user->plan_settings->biolink_blocks_limit) {
                Response::json(l('global.info_message.plan_feature_limit'), 'error');
            }

            $individual_blocks = ['link', 'heading', 'paragraph', 'avatar', 'socials', 'mail', 'rss_feed', 'custom_html', 'vcard', 'image', 'image_grid', 'divider', 'list', 'alert', 'faq', 'timeline', 'review', 'image_slider', 'discord', 'countdown', 'cta', 'external_item', 'share', 'youtube_feed', 'paypal', 'phone_collector', 'donation', 'product', 'service', 'map', 'opensea'];
            $embeddable_blocks = ['anchor', 'applemusic', 'soundcloud', 'spotify', 'tidal', 'tiktok', 'twitch', 'twitter_tweet', 'vimeo', 'youtube', 'instagram_media', 'facebook', 'reddit'];
            $file_blocks = ['audio', 'video', 'file'];

            if(in_array($_POST['block_type'], $individual_blocks)) {
                $this->{'create_biolink_' . $_POST['block_type']}();
            }

            else if(in_array($_POST['block_type'], $file_blocks)) {
                $this->create_biolink_file($_POST['block_type']);
            }

            else if(in_array($_POST['block_type'], $embeddable_blocks)) {
                $this->create_biolink_embeddable($_POST['block_type']);
            }

        }

        Response::json('', 'success');
    }

    private function update() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $this->biolink_blocks = require APP_PATH . 'includes/biolink_blocks.php';

        if(!empty($_POST)) {
            /* Check for available biolink blocks */
            if(isset($_POST['block_type']) && array_key_exists($_POST['block_type'], $this->biolink_blocks)) {
                $_POST['block_type'] = query_clean($_POST['block_type']);

                $individual_blocks = ['link', 'heading', 'paragraph', 'avatar', 'socials', 'mail', 'rss_feed', 'custom_html', 'vcard', 'image', 'image_grid', 'divider', 'list', 'alert', 'faq', 'timeline', 'review', 'image_slider', 'discord', 'countdown', 'cta', 'external_item', 'share', 'youtube_feed', 'paypal', 'phone_collector', 'donation', 'product', 'service', 'map', 'opensea'];
                $embeddable_blocks = ['anchor', 'applemusic', 'soundcloud', 'spotify', 'tidal', 'tiktok', 'twitch', 'twitter_tweet', 'vimeo', 'youtube', 'instagram_media', 'facebook', 'reddit'];
                $file_blocks = ['audio', 'video', 'file'];

                if(in_array($_POST['block_type'], $individual_blocks)) {
                    $this->{'update_biolink_' . $_POST['block_type']}();
                }

                else if(in_array($_POST['block_type'], $file_blocks)) {
                    $this->update_biolink_file($_POST['block_type']);
                }

                else if(in_array($_POST['block_type'], $embeddable_blocks)) {
                    $this->update_biolink_embeddable($_POST['block_type']);
                }

            }
        }

        die();
    }

    private function create_biolink_link() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url']);

        $type = 'link';
        $settings = json_encode([
            'name' => $_POST['name'],
            'open_in_new_tab' => false,
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',
            'image' => '',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_link() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Check for any errors */
        $required_fields = ['location_url', 'name'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        $this->check_location_url($_POST['location_url']);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],
            'image' => $db_image,

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['image_prop' => true, 'image_url' => $image_url, 'location_url' => $_POST['location_url']]);
    }

    private function create_biolink_heading() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['text'] = mb_substr(query_clean($_POST['text']), 0, 256);
        $_POST['heading_type'] = in_array($_POST['heading_type'], ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']) ? query_clean($_POST['heading_type']) : 'h1';

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'heading';
        $settings = json_encode([
            'heading_type' => $_POST['heading_type'],
            'text' => $_POST['text'],
            'text_color' => 'white',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_heading() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['heading_type'] = in_array($_POST['heading_type'], ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']) ? query_clean($_POST['heading_type']) : 'h1';
        $_POST['text'] = mb_substr(query_clean($_POST['text']), 0, 256);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#fff' : $_POST['text_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'heading_type' => $_POST['heading_type'],
            'text' => $_POST['text'],
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_paragraph() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['text'] = mb_substr(input_clean($_POST['text']), 0, 2048);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'paragraph';
        $settings = json_encode([
            'text' => $_POST['text'],
            'text_color' => 'white',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_paragraph() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['text'] = mb_substr(input_clean($_POST['text']), 0, 2048);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#fff' : $_POST['text_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'text' => $_POST['text'],
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_avatar() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['size'] = in_array($_POST['size'], ['75', '100', '125', '150']) ? (int) $_POST['size'] : 125;
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        /* Image upload */
        $db_image = $this->handle_image_upload(null, 'avatars/', settings()->links->avatar_size_limit);

        $type = 'avatar';
        $settings = json_encode([
            'image' => $db_image,
            'size' => $_POST['size'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_avatar() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['size'] = in_array($_POST['size'], ['75', '100', '125', '150']) ? (int) $_POST['size'] : 125;
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'avatars/', settings()->links->image_size_limit);

        $image_url = $db_image ? UPLOADS_FULL_URL . 'avatars/' . $db_image : null;

        $settings = json_encode([
            'image' => $db_image,
            'size' => $_POST['size'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['image_prop' => true, 'image_url' => $image_url]);
    }

    private function create_biolink_socials() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['color']) ? '#ffffff' : $_POST['color'];
        $_POST['size'] = in_array($_POST['size'], ['s', 'm', 'l', 'xl']) ? $_POST['size'] : 'l';

        /* Make sure the socials sent are proper */
        $biolink_socials = require APP_PATH . 'includes/biolink_socials.php';

        foreach($_POST['socials'] as $key => $value) {
            if(!array_key_exists($key, $biolink_socials)) {
                unset($_POST['socials'][$key]);
            } else {
                $_POST['socials'][$key] = mb_substr(query_clean($_POST['socials'][$key]), 0, $biolink_socials[$key]['max_length']);
            }
        }

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'socials';
        $settings = json_encode([
            'color' => $_POST['color'],
            'socials' => $_POST['socials'],
            'size' => $_POST['size'],

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_socials() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['color']) ? '#ffffff' : $_POST['color'];
        $_POST['size'] = in_array($_POST['size'], ['s', 'm', 'l', 'xl']) ? $_POST['size'] : 'l';

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Make sure the socials sent are proper */
        $biolink_socials = require APP_PATH . 'includes/biolink_socials.php';

        foreach($_POST['socials'] as $key => $value) {
            if(!array_key_exists($key, $biolink_socials)) {
                unset($_POST['socials'][$key]);
            } else {
                $_POST['socials'][$key] = mb_substr(query_clean($_POST['socials'][$key]), 0, $biolink_socials[$key]['max_length']);
            }
        }

        $settings = json_encode([
            'color' => $_POST['color'],
            'socials' => $_POST['socials'],
            'size' => $_POST['size'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_mail() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'mail';
        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => '',
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',

            'email_placeholder' => l('create_biolink_mail_modal.email_placeholder_default'),
            'name_placeholder' => l('create_biolink_mail_modal.name_placeholder_default'),
            'button_text' => l('create_biolink_mail_modal.button_text_default'),
            'success_text' => l('create_biolink_mail_modal.success_text_default'),
            'thank_you_url' => '',
            'show_agreement' => false,
            'agreement_url' => '',
            'agreement_text' => '',
            'mailchimp_api' => '',
            'mailchimp_api_list' => '',
            'email_notification' => '',
            'webhook_url' => '',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_mail() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        $_POST['email_placeholder'] = mb_substr(query_clean($_POST['email_placeholder']), 0, 64);
        $_POST['name_placeholder'] = mb_substr(query_clean($_POST['name_placeholder']), 0, 64);
        $_POST['button_text'] = mb_substr(query_clean($_POST['button_text']), 0, 64);
        $_POST['success_text'] = mb_substr(query_clean($_POST['success_text']), 0, 256);
        $_POST['show_agreement'] = (bool) isset($_POST['show_agreement']);
        $_POST['agreement_url'] = get_url($_POST['agreement_url']);
        $_POST['agreement_text'] = mb_substr(query_clean($_POST['agreement_text']), 0, 256);
        $_POST['mailchimp_api'] = mb_substr(query_clean($_POST['mailchimp_api']), 0, 64);
        $_POST['mailchimp_api_list'] = mb_substr(query_clean($_POST['mailchimp_api_list']), 0, 64);
        $_POST['email_notification'] = mb_substr(query_clean($_POST['email_notification']), 0, 320);
        $_POST['webhook_url'] = get_url($_POST['webhook_url']);
        $_POST['thank_you_url'] = get_url($_POST['thank_you_url']);

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => $db_image,
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],
            'email_placeholder' => $_POST['email_placeholder'],
            'name_placeholder' => $_POST['name_placeholder'],
            'button_text' => $_POST['button_text'],
            'success_text' => $_POST['success_text'],
            'thank_you_url' => $_POST['thank_you_url'],
            'show_agreement' => $_POST['show_agreement'],
            'agreement_url' => $_POST['agreement_url'],
            'agreement_text' => $_POST['agreement_text'],
            'mailchimp_api' => $_POST['mailchimp_api'],
            'mailchimp_api_list' => $_POST['mailchimp_api_list'],
            'email_notification' => $_POST['email_notification'],
            'webhook_url' => $_POST['webhook_url'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['image_prop' => true, 'image_url' => $image_url]);
    }

    private function create_biolink_rss_feed() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url']);

        $type = 'rss_feed';
        $settings = json_encode([
            'amount' => 5,
            'open_in_new_tab' => false,
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_rss_feed() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['amount'] = (int) query_clean($_POST['amount']);
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $this->check_location_url($_POST['location_url']);

        $settings = json_encode([
            'amount' => $_POST['amount'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_custom_html() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['html'] = mb_substr(trim($_POST['html']), 0, $this->biolink_blocks['custom_html']['max_length']);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'custom_html';
        $settings = json_encode([
            'html' => $_POST['html'],

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_custom_html() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['html'] = mb_substr(trim($_POST['html']), 0, $this->biolink_blocks['custom_html']['max_length']);

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'html' => $_POST['html'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_vcard() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'vcard';
        $settings = [
            'name' => $_POST['name'],
            'image' => '',
            'first_name' => '',
            'last_name' => '',
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',
            'vcard_socials' => [],
            'vcard_phone_numbers' => [],

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ];
        $settings = json_encode($settings);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_vcard() {
        $settings = [];

        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        $_POST['icon'] = query_clean($_POST['icon']);

        $settings['vcard_first_name'] = $_POST['vcard_first_name'] = mb_substr(query_clean($_POST['vcard_first_name']), 0, $this->biolink_blocks['vcard']['fields']['first_name']['max_length']);
        $settings['vcard_last_name'] = $_POST['vcard_last_name'] = mb_substr(query_clean($_POST['vcard_last_name']), 0, $this->biolink_blocks['vcard']['fields']['last_name']['max_length']);
        $settings['vcard_email'] = $_POST['vcard_email'] = mb_substr(query_clean($_POST['vcard_email']), 0, $this->biolink_blocks['vcard']['fields']['email']['max_length']);
        $settings['vcard_url'] = $_POST['vcard_url'] = mb_substr(query_clean($_POST['vcard_url']), 0, $this->biolink_blocks['vcard']['fields']['url']['max_length']);
        $settings['vcard_company'] = $_POST['vcard_company'] = mb_substr(query_clean($_POST['vcard_company']), 0, $this->biolink_blocks['vcard']['fields']['company']['max_length']);
        $settings['vcard_job_title'] = $_POST['vcard_job_title'] = mb_substr(query_clean($_POST['vcard_job_title']), 0, $this->biolink_blocks['vcard']['fields']['job_title']['max_length']);
        $settings['vcard_birthday'] = $_POST['vcard_birthday'] = mb_substr(query_clean($_POST['vcard_birthday']), 0, $this->biolink_blocks['vcard']['fields']['birthday']['max_length']);
        $settings['vcard_street'] = $_POST['vcard_street'] = mb_substr(query_clean($_POST['vcard_street']), 0, $this->biolink_blocks['vcard']['fields']['street']['max_length']);
        $settings['vcard_city'] = $_POST['vcard_city'] = mb_substr(query_clean($_POST['vcard_city']), 0, $this->biolink_blocks['vcard']['fields']['city']['max_length']);
        $settings['vcard_zip'] = $_POST['vcard_zip'] = mb_substr(query_clean($_POST['vcard_zip']), 0, $this->biolink_blocks['vcard']['fields']['zip']['max_length']);
        $settings['vcard_region'] = $_POST['vcard_region'] = mb_substr(query_clean($_POST['vcard_region']), 0, $this->biolink_blocks['vcard']['fields']['region']['max_length']);
        $settings['vcard_country'] = $_POST['vcard_country'] = mb_substr(query_clean($_POST['vcard_country']), 0, $this->biolink_blocks['vcard']['fields']['country']['max_length']);
        $settings['vcard_note'] = $_POST['vcard_note'] = mb_substr(query_clean($_POST['vcard_note']), 0, $this->biolink_blocks['vcard']['fields']['note']['max_length']);

        /* Phone numbers */
        if(!isset($_POST['vcard_phone_number'])) {
            $_POST['vcard_phone_number'] = [];
        }
        $vcard_phone_numbers = [];
        foreach($_POST['vcard_phone_number'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 20) continue;

            $vcard_phone_numbers[] = mb_substr(input_clean($value), 0, $this->biolink_blocks['vcard']['fields']['phone_number']['max_length']);
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
                'label' => mb_substr(query_clean($value), 0, $this->biolink_blocks['vcard']['fields']['social_value']['max_length']),
                'value' => mb_substr(input_clean($_POST['vcard_social_value'][$key]), 0, $this->biolink_blocks['vcard']['fields']['social_value']['max_length'])
            ];
        }
        $settings['vcard_socials'] = $vcard_socials;

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $db_image : null;

        /* Vcard avatar */
        $settings['vcard_avatar'] = $this->handle_file_upload($biolink_block->settings->vcard_avatar, 'vcard_avatar', 'vcard_avatar_remove', \Altum\Uploads::get_whitelisted_file_extensions('vcards_avatars'), 'avatars/', 0.75);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['vcard_avatar_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/avatars/' . $biolink_block->settings->vcard_avatar,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->vcard_avatar) && file_exists(UPLOADS_PATH . 'avatars/' . $biolink_block->settings->vcard_avatar)) {
                    unlink(UPLOADS_PATH . 'avatars/' . $biolink_block->settings->vcard_avatar);
                }
            }
            $settings['vcard_avatar'] = null;
        }

        $settings = array_merge($settings, [
            'name' => $_POST['name'],
            'image' => $db_image,
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);
        $settings = json_encode($settings);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['image_prop' => true, 'image_url' => $image_url]);
    }

    private function create_biolink_image() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['image_alt'] = mb_substr(query_clean($_POST['image_alt']), 0, 100);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url'], true);

        /* Image upload */
        $db_image = $this->handle_image_upload(null, 'block_images/', settings()->links->image_size_limit);

        $type = 'image';
        $settings = json_encode([
            'image' => $db_image,
            'image_alt' => $_POST['image_alt'],
            'open_in_new_tab' => false,

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_image() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['image_alt'] = mb_substr(query_clean($_POST['image_alt']), 0, 100);
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        $this->check_location_url($_POST['location_url'], true);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_images/', settings()->links->image_size_limit);

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_images/' . $db_image : null;

        $settings = json_encode([
            'image' => $db_image,
            'image_alt' => $_POST['image_alt'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['image_prop' => true, 'image_url' => $image_url]);
    }

    private function create_biolink_image_grid() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['image_alt'] = mb_substr(query_clean($_POST['image_alt']), 0, 100);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url'], true);

        $db_image = $this->handle_image_upload(null, 'block_images/', settings()->links->image_size_limit);

        $type = 'image_grid';
        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => $db_image,
            'image_alt' => $_POST['image_alt'],
            'open_in_new_tab' => false,

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_image_grid() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['image_alt'] = mb_substr(query_clean($_POST['image_alt']), 0, 100);
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        $this->check_location_url($_POST['location_url'], true);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_images/', settings()->links->image_size_limit);

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_images/' . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => $db_image,
            'image_alt' => $_POST['image_alt'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['image_prop' => true, 'image_url' => $image_url]);
    }

    private function create_biolink_divider() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['margin_top'] = $_POST['margin_top'] > 7 || $_POST['margin_top'] < 0 ? 3 : (int) $_POST['margin_top'];
        $_POST['margin_bottom'] = $_POST['margin_bottom'] > 7 || $_POST['margin_bottom'] < 0 ? 3 : (int) $_POST['margin_bottom'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'divider';
        $settings = json_encode([
            'margin_top' => $_POST['margin_top'],
            'margin_bottom' => $_POST['margin_bottom'],
            'background_color' => 'white',
            'icon' => 'fa fa-infinity',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_divider() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['margin_top'] = $_POST['margin_top'] > 7 || $_POST['margin_top'] < 0 ? 3 : (int) $_POST['margin_top'];
        $_POST['margin_bottom'] = $_POST['margin_bottom'] > 7 || $_POST['margin_bottom'] < 0 ? 3 : (int) $_POST['margin_bottom'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        $_POST['icon'] = query_clean($_POST['icon']);

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'margin_top' => $_POST['margin_top'],
            'margin_bottom' => $_POST['margin_bottom'],
            'background_color' => $_POST['background_color'],
            'icon' => $_POST['icon'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_list() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['text'] = mb_substr(input_clean($_POST['text']), 0, 2048);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'list';
        $settings = json_encode([
            'text' => $_POST['text'],
            'list' => 'fa fa-check-circle',
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => '#FFFFFF00',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'margin_items_y' => '1',
            'margin_items_x' => '1',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_list() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['text'] = mb_substr(input_clean($_POST['text']), 0, 2048);
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        $_POST['margin_items_y'] = $_POST['margin_items_y'] > 5 || $_POST['margin_items_y'] < 0 ? 2 : (int) $_POST['margin_items_y'];
        $_POST['margin_items_x'] = $_POST['margin_items_x'] > 3 || $_POST['margin_items_x'] < 0 ? 1 : (int) $_POST['margin_items_x'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'text' => $_POST['text'],
            'icon' => $_POST['icon'],
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'margin_items_y' => $_POST['margin_items_y'],
            'margin_items_x' => $_POST['margin_items_x'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_alert() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['text'] = mb_substr(input_clean($_POST['text']), 0, 2048);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'alert';
        $settings = json_encode([
            'text' => $_POST['text'],
            'icon' => 'fa fa-check-circle',
            'open_in_new_tab' => false,
            'text_color' => '#ffffff',
            'text_alignment' => 'left',
            'background_color' => '#FFFFFF38',
            'border_width' => 1,
            'border_style' => 'solid',
            'border_color' => '#FFFFFF8C',
            'border_radius' => 'rounded',
            'display_close_button' => true,
            'alert_pause_after_closed' => 60,

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_alert() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['text'] = mb_substr(input_clean($_POST['text']), 0, 2048);
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);
        $_POST['display_close_button'] = isset($_POST['display_close_button']);
        $_POST['alert_pause_after_closed'] = (int) $_POST['alert_pause_after_closed'];

        $this->check_location_url($_POST['location_url'], true);

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'text' => $_POST['text'],
            'icon' => $_POST['icon'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'display_close_button' => $_POST['display_close_button'],
            'alert_pause_after_closed' => $_POST['alert_pause_after_closed'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'location_url' => $_POST['location_url'],
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_faq() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'faq';
        $settings = json_encode([
            'items' => [],
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_faq() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = [];
        }

        $items = [];
        foreach($_POST['item_title'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 100) continue;

            $items[] = [
                'title' => query_clean($value),
                'content' => input_clean($_POST['item_content'][$key]),
            ];
        }

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_timeline() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'timeline';
        $settings = json_encode([
            'items' => [],
            'title_color' => '#ffffff',
            'date_color' => '#ffffff',
            'description_color' => '#ffffff',
            'line_color' => '#FFFFFF38',
            'text_alignment' => 'left',
            'background_color' => '#FFFFFF00',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_timeline() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['title_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['title_color']) ? '#000' : $_POST['title_color'];
        $_POST['date_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['date_color']) ? '#000' : $_POST['date_color'];
        $_POST['line_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['line_color']) ? '#000' : $_POST['line_color'];
        $_POST['description_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['description_color']) ? '#000' : $_POST['description_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

        if(!isset($_POST['item_title'])) {
            $_POST['item_title'] = [];
            $_POST['item_content'] = [];
        }

        $items = [];
        foreach($_POST['item_title'] as $key => $value) {
            if(empty(trim($value))) continue;
            if($key >= 100) continue;

            $items[] = [
                'title' => query_clean($value),
                'date' => input_clean($_POST['item_date'][$key]),
                'description' => input_clean($_POST['item_description'][$key]),
            ];
        }

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'items' => $items,
            'title_color' => $_POST['title_color'],
            'date_color' => $_POST['date_color'],
            'description_color' => $_POST['description_color'],
            'line_color' => $_POST['line_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_review() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['title'] = mb_substr(input_clean($_POST['title']), 0, 128);
        $_POST['description'] = mb_substr(input_clean($_POST['description']), 0, 1024);
        $_POST['author_name'] = mb_substr(input_clean($_POST['author_name']), 0, 128);
        $_POST['author_description'] = mb_substr(input_clean($_POST['author_description']), 0, 128);
        $_POST['stars'] = $_POST['stars'] > 5 || $_POST['stars'] < 0 ? 5 : (int) $_POST['stars'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        /* Image upload */
        $db_image = $this->handle_image_upload(null, 'block_images/', settings()->links->image_size_limit);

        $type = 'review';
        $settings = json_encode([
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'author_name' => $_POST['author_name'],
            'author_description' => $_POST['author_description'],
            'image' => $db_image,
            'stars' => $_POST['stars'],

            'title_color' => '#ffffff',
            'description_color' => '#ffffff',
            'author_name_color' => '#ffffff',
            'author_description_color' => '#ffffff',
            'stars_color' => '#FFDF00',
            'text_alignment' => 'left',
            'background_color' => '#FFFFFF00',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_review() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['title'] = mb_substr(input_clean($_POST['title']), 0, 128);
        $_POST['description'] = mb_substr(input_clean($_POST['description']), 0, 1024);
        $_POST['author_name'] = mb_substr(input_clean($_POST['author_name']), 0, 128);
        $_POST['author_description'] = mb_substr(input_clean($_POST['author_description']), 0, 128);
        $_POST['stars'] = $_POST['stars'] > 5 || $_POST['stars'] < 0 ? 5 : (int) $_POST['stars'];
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['title_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['title_color']) ? '#000' : $_POST['title_color'];
        $_POST['description_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['description_color']) ? '#000' : $_POST['description_color'];
        $_POST['author_name_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['author_name_color']) ? '#000' : $_POST['author_name_color'];
        $_POST['author_description_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['author_description_color']) ? '#000' : $_POST['author_description_color'];
        $_POST['stars_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['stars_color']) ? '#000' : $_POST['stars_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_images/', settings()->links->image_size_limit);

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_images/' . $db_image : null;

        $settings = json_encode([
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'author_name' => $_POST['author_name'],
            'author_description' => $_POST['author_description'],
            'stars' => $_POST['stars'],
            'image' => $db_image,
            'title_color' => $_POST['title_color'],
            'description_color' => $_POST['description_color'],
            'author_name_color' => $_POST['author_name_color'],
            'author_description_color' => $_POST['author_description_color'],
            'stars_color' => $_POST['stars_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['image_prop' => true, 'image_url' => $image_url]);
    }

    private function create_biolink_image_slider() {
        $_POST['link_id'] = (int) $_POST['link_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'image_slider';
        $settings = json_encode([
            'items' => [],
            'width_height' => '20',
            'gap' => '2',
            'display_multiple' => true,
            'display_pagination' => true,
            'autoplay' => true,
            'display_arrows' => true,
            'open_in_new_tab' => false,

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_image_slider() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['width_height'] = $_POST['width_height'] > 25 || $_POST['width_height'] < 10 ? 15 : (int) $_POST['width_height'];
        $_POST['gap'] = $_POST['gap'] > 5 || $_POST['gap'] < 0 ? 2 : (int) $_POST['gap'];
        $_POST['display_arrows'] = isset($_POST['display_arrows']);
        $_POST['autoplay'] = isset($_POST['autoplay']);
        $_POST['display_multiple'] = isset($_POST['display_multiple']);
        $_POST['display_pagination'] = isset($_POST['display_pagination']);
        $_POST['open_in_new_tab'] = (int) isset($_POST['open_in_new_tab']);

        if(!isset($_POST['item_image_alt'])) {
            $_POST['item_image_alt'] = [];
            $_POST['item_location_url'] = [];
        }

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        $items = [];
        $count = 1;
        foreach($_POST['item_image_alt'] as $key => $value) {
            if($count++ >= 25) continue;

            $_POST['item_location_url'][$key] = get_url($_POST['item_location_url'][$key]);
            $this->check_location_url($_POST['item_location_url'][$key], true);

            $image = $this->handle_file_upload($biolink_block->settings->items->{$key}->image ?? null, 'item_image_' . $key, 'image_remove', ['jpg', 'jpeg', 'png', 'svg', 'ico', 'gif'], 'block_images/', settings()->links->image_size_limit);

            $items[md5($image)] = [
                'image_alt' => input_clean($value),
                'location_url' => $_POST['item_location_url'][$key],
                'image' => $image,
            ];
        }

        /* Make sure to delete old images if needed */
        foreach($biolink_block->settings->items as $key => $item) {
            if((isset($items[$key]) && $items[$key]['image'] != $item->image) || !isset($items[$key])) {
                \Altum\Uploads::delete_uploaded_file($item->image, 'block_images');
            }
        }

        $settings = json_encode([
            'items' => (array) $items,
            'width_height' => $_POST['width_height'],
            'gap' => $_POST['gap'],
            'display_multiple' => $_POST['display_multiple'],
            'autoplay' => $_POST['autoplay'],
            'display_arrows' => $_POST['display_arrows'],
            'display_pagination' => $_POST['display_pagination'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_discord() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['server_id'] = (int) $_POST['server_id'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'discord';
        $settings = json_encode([
            'server_id' => $_POST['server_id'],

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_discord() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['server_id'] = (int) $_POST['server_id'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'server_id' => $_POST['server_id'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_countdown() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['counter_end_date'] = (new \DateTime($_POST['counter_end_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        $_POST['theme'] = in_array($_POST['theme'], ['light', 'dark']) ? query_clean($_POST['theme']) : 'light';

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'countdown';
        $settings = json_encode([
            'counter_end_date' => $_POST['counter_end_date'],
            'theme' => $_POST['theme'],

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_countdown() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['counter_end_date'] = (new \DateTime($_POST['counter_end_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        $_POST['theme'] = in_array($_POST['theme'], ['light', 'dark']) ? query_clean($_POST['theme']) : 'light';

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'counter_end_date' => $_POST['counter_end_date'],
            'theme' => $_POST['theme'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_file($type) {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['poster_url'] = get_url($_POST['poster_url'] ?? null);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        /* File upload */
        $db_file = $this->handle_file_upload(null, 'file', 'file_remove', $this->biolink_blocks[$type]['whitelisted_file_extensions'], 'files/', settings()->links->{$type . '_size_limit'});

        $settings = [
            'file' => $db_file,
            'name' => $_POST['name'],
        ];

        if($type == 'video') {
            $settings['poster_url'] = $_POST['poster_url'];
        }

        if($type == 'file') {
            $settings = array_merge($settings, [
                'text_color' => 'black',
                'text_alignment' => 'center',
                'background_color' => 'white',
                'border_width' => 0,
                'border_style' => 'solid',
                'border_color' => 'white',
                'border_radius' => 'rounded',
                'animation' => false,
                'animation_runs' => 'repeat-1',
                'icon' => '',
                'image' => '',
            ]);
        }

        $settings = json_encode($settings);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_file($type) {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['poster_url'] = get_url($_POST['poster_url'] ?? null);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* File upload */
        $db_file = $this->handle_file_upload($biolink_block->settings->file, 'file', 'file_remove', $this->biolink_blocks[$type]['whitelisted_file_extensions'], 'files/', settings()->links->{$type . '_size_limit'});

        $settings = [
            'file' => $db_file,
            'name' => $_POST['name']
        ];

        if($type == 'file') {
            /* Image upload */
            $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

            /* Check for the removal of the already uploaded file */
            if(isset($_POST['image_remove'])) {
                /* Offload deleting */
                if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                    $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                    $s3->deleteObject([
                        'Bucket' => settings()->offload->storage_name,
                        'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                    ]);
                }

                /* Local deleting */
                else {
                    /* Delete current file */
                    if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                        unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                    }
                }
            }

            $settings = array_merge($settings, [
                'text_color' => $_POST['text_color'],
                'text_alignment' => $_POST['text_alignment'],
                'background_color' => $_POST['background_color'],
                'border_radius' => $_POST['border_radius'],
                'border_width' => $_POST['border_width'],
                'border_style' => $_POST['border_style'],
                'border_color' => $_POST['border_color'],
                'animation' => $_POST['animation'],
                'animation_runs' => $_POST['animation_runs'],
                'icon' => $_POST['icon'],
                'image' => $db_image,
            ]);
        }

        if($type == 'video') {
            $settings['poster_url'] = $_POST['poster_url'];
        }

        $settings = json_encode($settings);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_cta() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['type'] = in_array($_POST['type'], ['email', 'call', 'sms', 'facetime']) ? query_clean($_POST['type']) : 'email';
        $_POST['value'] = mb_substr(query_clean($_POST['value']), 0, 320);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'cta';
        $settings = json_encode([
            'type' => $_POST['type'],
            'value' => $_POST['value'],
            'name' => $_POST['name'],
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',
            'image' => '',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_cta() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['type'] = in_array($_POST['type'], ['email', 'call', 'sms', 'facetime']) ? query_clean($_POST['type']) : 'email';
        $_POST['value'] = mb_substr(query_clean($_POST['value']), 0, 320);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $db_image : null;

        $settings = json_encode([
            'type' => $_POST['type'],
            'value' => $_POST['value'],
            'name' => $_POST['name'],
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],
            'image' => $db_image,

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['image_prop' => true, 'image_url' => $image_url]);
    }

    private function create_biolink_external_item() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['description'] = mb_substr(query_clean($_POST['description']), 0, 256);
        $_POST['price'] = mb_substr(query_clean($_POST['price']), 0, 32);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url']);

        $type = 'external_item';
        $settings = json_encode([
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'price' => $_POST['price'],
            'name_text_color' => 'black',
            'description_text_color' => 'black',
            'price_text_color' => 'black',
            'open_in_new_tab' => false,
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'image' => '',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_external_item() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['description'] = mb_substr(query_clean($_POST['description']), 0, 256);
        $_POST['price'] = mb_substr(query_clean($_POST['price']), 0, 32);
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['name_text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['name_text_color']) ? '#000' : $_POST['name_text_color'];
        $_POST['description_text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['description_text_color']) ? '#000' : $_POST['description_text_color'];
        $_POST['price_text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['price_text_color']) ? '#000' : $_POST['price_text_color'];
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Check for any errors */
        $required_fields = ['location_url', 'name'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        $this->check_location_url($_POST['location_url']);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'price' => $_POST['price'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],
            'name_text_color' => $_POST['name_text_color'],
            'description_text_color' => $_POST['description_text_color'],
            'price_text_color' => $_POST['price_text_color'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'image' => $db_image,

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['image_prop' => true, 'image_url' => $image_url, 'location_url' => $_POST['location_url']]);
    }

    private function create_biolink_share() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url']);

        $type = 'share';
        $settings = json_encode([
            'name' => $_POST['name'],
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',
            'image' => '',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_share() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Check for any errors */
        $required_fields = ['location_url', 'name'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        $this->check_location_url($_POST['location_url']);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],
            'image' => $db_image,

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['image_prop' => true, 'image_url' => $image_url, 'location_url' => $_POST['location_url']]);
    }

    private function create_biolink_youtube_feed() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['channel_id'] = mb_substr(query_clean($_POST['channel_id']), 0, 128);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'youtube_feed';
        $settings = json_encode([
            'channel_id' => $_POST['channel_id'],
            'amount' => 5,
            'open_in_new_tab' => false,
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_youtube_feed() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['channel_id'] = mb_substr(query_clean($_POST['channel_id']), 0, 128);
        $_POST['amount'] = (int) query_clean($_POST['amount']);
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        $settings = json_encode([
            'channel_id' => $_POST['channel_id'],
            'amount' => $_POST['amount'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_paypal() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['type'] = in_array($_POST['type'], ['buy_now', 'add_to_cart', 'donation']) ? $_POST['type'] : 'buy_now';
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['email'] = mb_substr(query_clean($_POST['email']), 0, 320);
        $_POST['title'] = mb_substr(query_clean($_POST['title']), 0, 320);
        $_POST['currency'] = mb_substr(query_clean($_POST['currency']), 0, 8);
        $_POST['price'] = (float) $_POST['price'];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'paypal';
        $settings = json_encode([
            'type' => $_POST['type'],
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'title' => $_POST['title'],
            'currency' => $_POST['currency'],
            'price' => $_POST['price'],
            'thank_you_url' => '',
            'cancel_url' => '',
            'open_in_new_tab' => false,
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',
            'image' => '',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_paypal() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['type'] = in_array($_POST['type'], ['buy_now', 'add_to_cart', 'donation']) ? $_POST['type'] : 'buy_now';
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['email'] = mb_substr(query_clean($_POST['email']), 0, 320);
        $_POST['title'] = mb_substr(query_clean($_POST['title']), 0, 320);
        $_POST['currency'] = mb_substr(query_clean($_POST['currency']), 0, 8);
        $_POST['price'] = (float) $_POST['price'];
        $_POST['thank_you_url'] = get_url($_POST['thank_you_url']);
        $_POST['cancel_url'] = get_url($_POST['cancel_url']);
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Check for any errors */
        $required_fields = ['name'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $db_image : null;

        $settings = json_encode([
            'type' => $_POST['type'],
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'title' => $_POST['title'],
            'currency' => $_POST['currency'],
            'price' => $_POST['price'],
            'thank_you_url' => $_POST['thank_you_url'],
            'cancel_url' => $_POST['cancel_url'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],
            'image' => $db_image,

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['image_prop' => true, 'image_url' => $image_url]);
    }

    private function create_biolink_phone_collector() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'phone_collector';
        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => '',
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',
            'phone_placeholder' => l('create_biolink_phone_collector_modal.phone_placeholder_default'),
            'name_placeholder' => l('create_biolink_phone_collector_modal.name_placeholder_default'),
            'button_text' => l('create_biolink_phone_collector_modal.button_text_default'),
            'success_text' => l('create_biolink_phone_collector_modal.success_text_default'),
            'thank_you_url' => '',
            'show_agreement' => false,
            'agreement_url' => '',
            'agreement_text' => '',
            'email_notification' => '',
            'webhook_url' => '',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_phone_collector() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];
        $_POST['phone_placeholder'] = mb_substr(query_clean($_POST['phone_placeholder']), 0, 64);
        $_POST['name_placeholder'] = mb_substr(query_clean($_POST['name_placeholder']), 0, 64);
        $_POST['button_text'] = mb_substr(query_clean($_POST['button_text']), 0, 64);
        $_POST['success_text'] = mb_substr(query_clean($_POST['success_text']), 0, 256);
        $_POST['show_agreement'] = (bool) isset($_POST['show_agreement']);
        $_POST['agreement_url'] = get_url($_POST['agreement_url']);
        $_POST['agreement_text'] = mb_substr(query_clean($_POST['agreement_text']), 0, 256);
        $_POST['email_notification'] = mb_substr(query_clean($_POST['email_notification']), 0, 320);
        $_POST['webhook_url'] = get_url($_POST['webhook_url']);
        $_POST['thank_you_url'] = get_url($_POST['thank_you_url']);

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => $db_image,
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],
            'phone_placeholder' => $_POST['phone_placeholder'],
            'name_placeholder' => $_POST['name_placeholder'],
            'button_text' => $_POST['button_text'],
            'success_text' => $_POST['success_text'],
            'thank_you_url' => $_POST['thank_you_url'],
            'show_agreement' => $_POST['show_agreement'],
            'agreement_url' => $_POST['agreement_url'],
            'agreement_text' => $_POST['agreement_text'],
            'email_notification' => $_POST['email_notification'],
            'webhook_url' => $_POST['webhook_url'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['image_prop' => true, 'image_url' => $image_url]);
    }

    private function create_biolink_donation() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'donation';
        $settings = [
            'name' => $_POST['name'],
            'image' => '',
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',

            'title' => null,
            'description' => null,
            'prefilled_amount' => 5,
            'minimum_amount' => 1,
            'currency' => null,
            'allow_custom_amount' => true,
            'allow_message' => true,
            'thank_you_title' => null,
            'thank_you_description' => null,
            'payment_processors_ids' => [],
            'email_notification' => null,
            'webhook_url' => null,

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ];
        $settings = json_encode($settings);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_donation() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

        $_POST['title'] = mb_substr(query_clean($_POST['title']), 0, $this->biolink_blocks['donation']['fields']['title']['max_length']);
        $_POST['description'] = mb_substr(query_clean($_POST['description']), 0, $this->biolink_blocks['donation']['fields']['description']['max_length']);
        $_POST['prefilled_amount'] = (float) $_POST['prefilled_amount'];
        $_POST['minimum_amount'] = (float) $_POST['minimum_amount'];
        $_POST['currency'] = mb_substr(query_clean($_POST['currency']), 0, $this->biolink_blocks['donation']['fields']['currency']['max_length']);
        $_POST['allow_custom_amount'] = (bool) isset($_POST['allow_custom_amount']);
        $_POST['allow_message'] = (bool) isset($_POST['allow_message']);
        $_POST['thank_you_title'] = mb_substr(query_clean($_POST['thank_you_title']), 0, $this->biolink_blocks['donation']['fields']['thank_you_title']['max_length']);
        $_POST['thank_you_description'] = mb_substr(query_clean($_POST['thank_you_description']), 0, $this->biolink_blocks['donation']['fields']['thank_you_description']['max_length']);
        $_POST['email_notification'] = mb_substr(query_clean($_POST['email_notification']), 0, $this->biolink_blocks['donation']['fields']['email_notification']['max_length']);
        $_POST['webhook_url'] = mb_substr(query_clean($_POST['webhook_url']), 0, $this->biolink_blocks['donation']['fields']['webhook_url']['max_length']);

        $payment_processors = (new \Altum\Models\PaymentProcessor())->get_payment_processors_by_user_id($this->user->user_id);
        $_POST['payment_processors_ids'] = array_map(
            function($payment_processor_id) {
                return (int) $payment_processor_id;
            },
            array_filter($_POST['payment_processors_ids'] ?? [], function($payment_processor_id) use($payment_processors) {
                return array_key_exists($payment_processor_id, $payment_processors);
            })
        );

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => $db_image,
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],

            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'prefilled_amount' => $_POST['prefilled_amount'],
            'minimum_amount' => $_POST['minimum_amount'],
            'currency' => $_POST['currency'],
            'allow_custom_amount' => $_POST['allow_custom_amount'],
            'allow_message' => $_POST['allow_message'],
            'thank_you_title' => $_POST['thank_you_title'],
            'thank_you_description' => $_POST['thank_you_description'],
            'payment_processors_ids' => $_POST['payment_processors_ids'],
            'email_notification' => $_POST['email_notification'],
            'webhook_url' => $_POST['webhook_url'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['image_prop' => true, 'image_url' => $image_url]);
    }

    private function create_biolink_product() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'product';
        $settings = [
            'name' => $_POST['name'],
            'image' => '',
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',

            'file' => null,
            'title' => null,
            'description' => null,
            'price' => 5,
            'minimum_price' => 1,
            'currency' => null,
            'allow_custom_price' => true,
            'thank_you_title' => null,
            'thank_you_description' => null,
            'payment_processors_ids' => [],
            'email_notification' => null,
            'webhook_url' => null,

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ];
        $settings = json_encode($settings);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_product() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

        $_POST['title'] = mb_substr(query_clean($_POST['title']), 0, $this->biolink_blocks['product']['fields']['title']['max_length']);
        $_POST['description'] = mb_substr(query_clean($_POST['description']), 0, $this->biolink_blocks['product']['fields']['description']['max_length']);
        $_POST['price'] = (float) $_POST['price'];
        $_POST['minimum_price'] = (float) $_POST['minimum_price'];
        $_POST['currency'] = mb_substr(query_clean($_POST['currency']), 0, $this->biolink_blocks['product']['fields']['currency']['max_length']);
        $_POST['allow_custom_price'] = (bool) isset($_POST['allow_custom_price']);
        $_POST['thank_you_title'] = mb_substr(query_clean($_POST['thank_you_title']), 0, $this->biolink_blocks['product']['fields']['thank_you_title']['max_length']);
        $_POST['thank_you_description'] = mb_substr(query_clean($_POST['thank_you_description']), 0, $this->biolink_blocks['product']['fields']['thank_you_description']['max_length']);
        $_POST['email_notification'] = mb_substr(query_clean($_POST['email_notification']), 0, $this->biolink_blocks['product']['fields']['email_notification']['max_length']);
        $_POST['webhook_url'] = mb_substr(query_clean($_POST['webhook_url']), 0, $this->biolink_blocks['product']['fields']['webhook_url']['max_length']);

        $payment_processors = (new \Altum\Models\PaymentProcessor())->get_payment_processors_by_user_id($this->user->user_id);
        $_POST['payment_processors_ids'] = array_map(
            function($payment_processor_id) {
                return (int) $payment_processor_id;
            },
            array_filter($_POST['payment_processors_ids'] ?? [], function($payment_processor_id) use($payment_processors) {
                return array_key_exists($payment_processor_id, $payment_processors);
            })
        );

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* File upload */
        $db_file = $this->handle_file_upload($biolink_block->settings->file, 'file', 'file_remove', $this->biolink_blocks['product']['whitelisted_file_extensions'], 'products_files/', settings()->links->product_file_size_limit);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => $db_image,
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],

            'file' => $db_file,
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'price' => $_POST['price'],
            'minimum_price' => $_POST['minimum_price'],
            'currency' => $_POST['currency'],
            'allow_custom_price' => $_POST['allow_custom_price'],
            'thank_you_title' => $_POST['thank_you_title'],
            'thank_you_description' => $_POST['thank_you_description'],
            'payment_processors_ids' => $_POST['payment_processors_ids'],
            'email_notification' => $_POST['email_notification'],
            'webhook_url' => $_POST['webhook_url'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['image_prop' => true, 'image_url' => $image_url]);
    }

    private function create_biolink_service() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'service';
        $settings = [
            'name' => $_POST['name'],
            'image' => '',
            'text_color' => 'black',
            'text_alignment' => 'center',
            'background_color' => 'white',
            'border_width' => 0,
            'border_style' => 'solid',
            'border_color' => 'white',
            'border_radius' => 'rounded',
            'animation' => false,
            'animation_runs' => 'repeat-1',
            'icon' => '',

            'title' => null,
            'description' => null,
            'price' => null,
            'currency' => null,
            'thank_you_title' => null,
            'thank_you_description' => null,
            'payment_processors_ids' => [],
            'email_notification' => null,
            'webhook_url' => null,

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ];
        $settings = json_encode($settings);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => null,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_service() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['name'] = mb_substr(query_clean($_POST['name']), 0, 128);
        $_POST['border_radius'] = in_array($_POST['border_radius'], ['straight', 'round', 'rounded']) ? query_clean($_POST['border_radius']) : 'rounded';
        $_POST['border_width'] = in_array($_POST['border_width'], [0, 1, 2, 3, 4, 5]) ? (int) $_POST['border_width'] : 0;
        $_POST['border_style'] = in_array($_POST['border_style'], ['solid', 'dashed', 'double', 'inset', 'outset']) ? query_clean($_POST['border_style']) : 'solid';
        $_POST['border_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['border_color']) ? '#000' : $_POST['border_color'];
        $_POST['animation'] = in_array($_POST['animation'], require APP_PATH . 'includes/biolink_animations.php') || $_POST['animation'] == 'false' ? query_clean($_POST['animation']) : false;
        $_POST['animation_runs'] = in_array($_POST['animation_runs'], ['repeat-1', 'repeat-2', 'repeat-3', 'infinite']) ? query_clean($_POST['animation_runs']) : false;
        $_POST['icon'] = query_clean($_POST['icon']);
        $_POST['text_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['text_color']) ? '#000' : $_POST['text_color'];
        $_POST['text_alignment'] = in_array($_POST['text_alignment'], ['center', 'left', 'right', 'justify']) ? query_clean($_POST['text_alignment']) : 'center';
        $_POST['background_color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? '#fff' : $_POST['background_color'];

        $_POST['title'] = mb_substr(query_clean($_POST['title']), 0, $this->biolink_blocks['service']['fields']['title']['max_length']);
        $_POST['description'] = mb_substr(query_clean($_POST['description']), 0, $this->biolink_blocks['service']['fields']['description']['max_length']);
        $_POST['price'] = (float) $_POST['price'];
        $_POST['currency'] = mb_substr(query_clean($_POST['currency']), 0, $this->biolink_blocks['service']['fields']['currency']['max_length']);
        $_POST['thank_you_title'] = mb_substr(query_clean($_POST['thank_you_title']), 0, $this->biolink_blocks['service']['fields']['thank_you_title']['max_length']);
        $_POST['thank_you_description'] = mb_substr(query_clean($_POST['thank_you_description']), 0, $this->biolink_blocks['service']['fields']['thank_you_description']['max_length']);
        $_POST['email_notification'] = mb_substr(query_clean($_POST['email_notification']), 0, $this->biolink_blocks['service']['fields']['email_notification']['max_length']);
        $_POST['webhook_url'] = mb_substr(query_clean($_POST['webhook_url']), 0, $this->biolink_blocks['service']['fields']['webhook_url']['max_length']);

        $payment_processors = (new \Altum\Models\PaymentProcessor())->get_payment_processors_by_user_id($this->user->user_id);
        $_POST['payment_processors_ids'] = array_map(
            function($payment_processor_id) {
                return (int) $payment_processor_id;
            },
            array_filter($_POST['payment_processors_ids'] ?? [], function($payment_processor_id) use($payment_processors) {
                return array_key_exists($payment_processor_id, $payment_processors);
            })
        );

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Image upload */
        $db_image = $this->handle_image_upload($biolink_block->settings->image, 'block_thumbnail_images/', settings()->links->thumbnail_image_size_limit);

        /* Check for the removal of the already uploaded file */
        if(isset($_POST['image_remove'])) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_thumbnail_images/' . $biolink_block->settings->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($biolink_block->settings->image) && file_exists(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image)) {
                    unlink(UPLOADS_PATH . 'block_thumbnail_images/' . $biolink_block->settings->image);
                }
            }
            $db_image = null;
        }

        $image_url = $db_image ? UPLOADS_FULL_URL . 'block_thumbnail_images/' . $db_image : null;

        $settings = json_encode([
            'name' => $_POST['name'],
            'image' => $db_image,
            'text_color' => $_POST['text_color'],
            'text_alignment' => $_POST['text_alignment'],
            'background_color' => $_POST['background_color'],
            'border_radius' => $_POST['border_radius'],
            'border_width' => $_POST['border_width'],
            'border_style' => $_POST['border_style'],
            'border_color' => $_POST['border_color'],
            'animation' => $_POST['animation'],
            'animation_runs' => $_POST['animation_runs'],
            'icon' => $_POST['icon'],

            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'price' => $_POST['price'],
            'currency' => $_POST['currency'],
            'thank_you_title' => $_POST['thank_you_title'],
            'thank_you_description' => $_POST['thank_you_description'],
            'payment_processors_ids' => $_POST['payment_processors_ids'],
            'email_notification' => $_POST['email_notification'],
            'webhook_url' => $_POST['webhook_url'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success', ['image_prop' => true, 'image_url' => $image_url]);
    }

    private function create_biolink_map() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['address'] = mb_substr(query_clean($_POST['address']), 0, 64);
        $_POST['location_url'] = get_url($_POST['location_url']);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $this->check_location_url($_POST['location_url'], true);

        $type = 'map';
        $settings = json_encode([
            'address' => $_POST['address'],
            'open_in_new_tab' => false,
            'zoom' => 15,
            'type' => 'roadmap',

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_map() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['address'] = mb_substr(query_clean($_POST['address']), 0, 64);
        $_POST['open_in_new_tab'] = isset($_POST['open_in_new_tab']);
        $_POST['location_url'] = get_url($_POST['location_url']);
        $_POST['zoom'] = in_array($_POST['zoom'], range(1, 20)) ? (int) $_POST['zoom'] : 15;
        $_POST['type'] = in_array($_POST['type'], ['roadmap', 'satellite', 'hybrid', 'terrain']) ? $_POST['type'] : 'roadmap';

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Check for any errors */
        $required_fields = ['address'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        $this->check_location_url($_POST['location_url'], true);

        $settings = json_encode([
            'address' => $_POST['address'],
            'open_in_new_tab' => $_POST['open_in_new_tab'],
            'zoom' => $_POST['zoom'],
            'type' => $_POST['type'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_opensea() {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['token_address'] = mb_substr(query_clean($_POST['token_address']), 0, 128);
        $_POST['token_id'] = mb_substr(preg_replace('~\D~', '', $_POST['token_id']), 0, 256);

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        $type = 'opensea';
        $settings = json_encode([
            'token_address' => $_POST['token_address'],
            'token_id' => $_POST['token_id'],

            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ]);

        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'settings' => $settings,
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_opensea() {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['token_address'] = mb_substr(query_clean($_POST['token_address']), 0, 128);
        $_POST['token_id'] = mb_substr(preg_replace('~\D~', '', $_POST['token_id']), 0, 256);

        /* Display settings */
        $this->process_display_settings();

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }
        $biolink_block->settings = json_decode($biolink_block->settings);

        /* Check for any errors */
        $required_fields = ['token_address', 'token_id'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        $settings = json_encode([
            'token_address' => $_POST['token_address'],
            'token_id' => $_POST['token_id'],

            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ]);

        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'settings' => $settings,
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function create_biolink_embeddable($type) {
        $_POST['link_id'] = (int) $_POST['link_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);
        $settings = [
            /* Display settings */
            'display_countries' => [],
            'display_devices' => [],
            'display_languages' => [],
            'display_operating_systems' => [],
        ];

        if(!$link = db()->where('link_id', $_POST['link_id'])->where('user_id', $this->user->user_id)->getOne('links')) {
            die();
        }

        /* Check for any errors */
        $required_fields = ['location_url'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        $this->check_location_url($_POST['location_url']);

        /* Make sure the location url is valid & get needed details */
        $host = parse_url($_POST['location_url'], PHP_URL_HOST);

        if(isset($this->biolink_blocks[$type]['whitelisted_hosts']) && !in_array($host, $this->biolink_blocks[$type]['whitelisted_hosts'])) {
            Response::json(l('link.error_message.invalid_location_url_embed'), 'error');
        }

        switch($type) {
            case 'reddit':
                $response = Request::get('https://www.reddit.com/oembed?url=' . $_POST['location_url']);

                if($response->code >= 400) {
                    Response::json(l('link.error_message.invalid_location_url_embed'), 'error');
                }

                $settings['content'] = $response->body->html;
                break;
        }


        /* Database query */
        db()->insert('biolinks_blocks', [
            'user_id' => $this->user->user_id,
            'link_id' => $_POST['link_id'],
            'type' => $type,
            'location_url' => $_POST['location_url'],
            'settings' => json_encode($settings),
            'order' => $this->total_biolink_blocks,
            'datetime' => \Altum\Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $_POST['link_id']);

        Response::json('', 'success', ['url' => url('link/' . $_POST['link_id'] . '?tab=links')]);
    }

    private function update_biolink_embeddable($type) {
        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];
        $_POST['location_url'] = get_url($_POST['location_url']);

        /* Display settings */
        $this->process_display_settings();

        $settings = [
            /* Display settings */
            'display_countries' => $_POST['display_countries'],
            'display_devices' => $_POST['display_devices'],
            'display_languages' => $_POST['display_languages'],
            'display_operating_systems' => $_POST['display_operating_systems'],
        ];

        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        /* Check for any errors */
        $required_fields = ['location_url'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                Response::json(l('global.error_message.empty_fields'), 'error');
                break 1;
            }
        }

        $this->check_location_url($_POST['location_url']);

        /* Make sure the location url is valid & get needed details */
        $host = parse_url($_POST['location_url'], PHP_URL_HOST);

        if(isset($this->biolink_blocks[$type]['whitelisted_hosts']) && !in_array($host, $this->biolink_blocks[$type]['whitelisted_hosts'])) {
            Response::json(l('link.error_message.invalid_location_url_embed'), 'error');
        }

        switch($type) {
            case 'reddit':
                $response = Request::get('https://www.reddit.com/oembed?url=' . $_POST['location_url']);

                if($response->code >= 400) {
                    Response::json(l('link.error_message.invalid_location_url_embed'), 'error');
                }

                $setting['content'] = $response->body->html;
            break;
        }



        /* Database query */
        db()->where('biolink_block_id', $_POST['biolink_block_id'])->update('biolinks_blocks', [
            'location_url' => $_POST['location_url'],
            'settings' => json_encode($settings),
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('link?link_id=' . $biolink_block->link_id);

        Response::json(l('global.success_message.update2'), 'success');
    }

    private function delete() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('delete')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $_POST['biolink_block_id'] = (int) $_POST['biolink_block_id'];

        /* Check for possible errors */
        if(!$biolink_block = db()->where('biolink_block_id', $_POST['biolink_block_id'])->where('user_id', $this->user->user_id)->getOne('biolinks_blocks')) {
            die();
        }

        (new \Altum\Models\BiolinkBlock())->delete($biolink_block->biolink_block_id);

        Response::json(l('global.success_message.delete2'), 'success', ['url' => url('link/' . $biolink_block->link_id . '?tab=links')]);
    }

    public function handle_file_upload($already_existing_file, $file_name, $file_name_remove, $allowed_extensions, $upload_folder, $size_limit) {
        /* File upload */
        $file = (bool) !empty($_FILES[$file_name]['name']) && !isset($_POST[$file_name_remove]);
        $db_file = $already_existing_file;

        if($file) {
            $file_extension = explode('.', $_FILES[$file_name]['name']);
            $file_extension = mb_strtolower(end($file_extension));
            $file_temp = $_FILES[$file_name]['tmp_name'];

            if($_FILES[$file_name]['error'] == UPLOAD_ERR_INI_SIZE) {
                Response::json(sprintf(l('global.error_message.file_size_limit'), $size_limit), 'error');
            }

            if($_FILES[$file_name]['error'] && $_FILES[$file_name]['error'] != UPLOAD_ERR_INI_SIZE) {
                Response::json(l('global.error_message.file_upload'), 'error');
            }

            if(!is_writable(UPLOADS_PATH . $upload_folder)) {
                Response::json(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . $upload_folder), 'error');
            }

            if(!in_array($file_extension, $allowed_extensions)) {
                Response::json(l('global.error_message.invalid_file_type'), 'error');
            }

            if($_FILES[$file_name]['size'] > $size_limit * 1000000) {
                Response::json(sprintf(l('global.error_message.file_size_limit'), $size_limit), 'error');
            }

            /* Generate new name for the file */
            $file_new_name = md5(time() . rand()) . '.' . $file_extension;

            /* Try to compress the image */
            if(\Altum\Plugin::is_active('image-optimizer')) {
                \Altum\Plugin\ImageOptimizer::optimize($file_temp, $file_new_name);
            }

            /* Offload uploading */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                try {
                    $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                    /* Delete current image */
                    if(!empty($already_existing_file)) {
                        $s3->deleteObject([
                            'Bucket' => settings()->offload->storage_name,
                            'Key' => UPLOADS_URL_PATH . $upload_folder . $already_existing_file,
                        ]);
                    }

                    /* Upload image */
                    $result = $s3->putObject([
                        'Bucket' => settings()->offload->storage_name,
                        'Key' => UPLOADS_URL_PATH . $upload_folder . $file_new_name,
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
                /* Delete current file */
                if(!empty($already_existing_file) && file_exists(UPLOADS_PATH . $upload_folder . $already_existing_file)) {
                    unlink(UPLOADS_PATH . $upload_folder . $already_existing_file);
                }

                /* Upload the original */
                move_uploaded_file($file_temp, UPLOADS_PATH . $upload_folder . $file_new_name);
            }

            $db_file = $file_new_name;
        }

        return $db_file;
    }

    private function handle_image_upload($uploaded_image, $upload_folder, $size_limit) {
        return $this->handle_file_upload($uploaded_image, 'image', 'image_remove', ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'], $upload_folder, $size_limit);
    }

    /* Function to bundle together all the checks of an url */
    private function check_location_url($url, $can_be_empty = false) {

        if(empty(trim($url ?? '')) && $can_be_empty) {
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

    private function process_display_settings() {
        if(isset($_POST['schedule']) && !empty($_POST['start_date']) && !empty($_POST['end_date']) && Date::validate($_POST['start_date'], 'Y-m-d H:i:s') && Date::validate($_POST['end_date'], 'Y-m-d H:i:s')) {
            $_POST['start_date'] = (new \DateTime($_POST['start_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
            $_POST['end_date'] = (new \DateTime($_POST['end_date'], new \DateTimeZone($this->user->timezone)))->setTimezone(new \DateTimeZone(\Altum\Date::$default_timezone))->format('Y-m-d H:i:s');
        } else {
            $_POST['start_date'] = $_POST['end_date'] = null;
        }

        $_POST['display_countries'] = array_filter($_POST['display_countries'] ?? [], function($country) {
            return array_key_exists($country, get_countries_array());
        });

        $_POST['display_devices'] = array_filter($_POST['display_devices'] ?? [], function($device_type) {
            return in_array($device_type, ['desktop', 'tablet', 'mobile']);
        });

        $_POST['display_languages'] = array_filter($_POST['display_languages'] ?? [], function($locale) {
            return array_key_exists($locale, get_locale_languages_array());
        });

        $_POST['display_operating_systems'] = array_filter($_POST['display_operating_systems'] ?? [], function($os_name) {
            return in_array($os_name, ['iOS', 'Android', 'Windows', 'OS X', 'Linux', 'Ubuntu', 'Chrome OS']);
        });
    }
}
