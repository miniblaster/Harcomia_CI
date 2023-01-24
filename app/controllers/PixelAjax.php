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

class PixelAjax extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Response::json('Please create an account on the demo to test out this function.', 'error');

        if(!empty($_POST) && (\Altum\Csrf::check('token') || \Altum\Csrf::check('global_token')) && isset($_POST['request_type'])) {

            switch($_POST['request_type']) {

                /* Create */
                case 'create': $this->create(); break;

                /* Update */
                case 'update': $this->update(); break;

                /* Delete */
                case 'delete': $this->delete(); break;

            }

        }

        die();
    }

    private function create() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $_POST['name'] = trim(query_clean($_POST['name']));
        $_POST['type'] = array_key_exists($_POST['type'], require APP_PATH . 'includes/pixels.php') ? $_POST['type'] : '';
        $_POST['pixel'] = trim(query_clean($_POST['pixel']));

        /* Check for possible errors */
        if(empty($_POST['name']) || empty($_POST['type']) || empty($_POST['pixel'])) {
            Response::json(l('global.error_message.empty_fields'), 'error');
        }

        /* Make sure that the user didn't exceed the limit */
        $user_total_pixels = database()->query("SELECT COUNT(*) AS `total` FROM `pixels` WHERE `user_id` = {$this->user->user_id}")->fetch_object()->total;
        if($this->user->plan_settings->pixels_limit != -1 && $user_total_pixels >= $this->user->plan_settings->pixels_limit) {
            Response::json(l('global.info_message.plan_feature_limit'), 'error');
        }

        /* Insert to database */
        db()->insert('pixels', [
            'user_id' => $this->user->user_id,
            'type' => $_POST['type'],
            'name' => $_POST['name'],
            'pixel' => $_POST['pixel'],
            'datetime' => Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('pixels?user_id=' . $this->user->user_id);

        /* Set a nice success message */
        Response::json(sprintf(l('global.success_message.create1'), '<strong>' . $_POST['name'] . '</strong>'));

    }

    private function update() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $_POST['name'] = trim(query_clean($_POST['name']));
        $_POST['type'] = array_key_exists($_POST['type'], require APP_PATH . 'includes/pixels.php') ? $_POST['type'] : '';
        $_POST['pixel'] = trim(query_clean($_POST['pixel']));

        /* Check for possible errors */
        if(empty($_POST['name']) || empty($_POST['type']) || empty($_POST['pixel'])) {
            Response::json(l('global.error_message.empty_fields'), 'error');
        }

        /* Insert to database */
        db()->where('pixel_id', $_POST['pixel_id'])->where('user_id', $this->user->user_id)->update('pixels', [
            'type' => $_POST['type'],
            'name' => $_POST['name'],
            'pixel' => $_POST['pixel'],
            'last_datetime' => Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('pixels?user_id=' . $this->user->user_id);

        /* Set a nice success message */
        Response::json(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['name'] . '</strong>'));

    }

}
