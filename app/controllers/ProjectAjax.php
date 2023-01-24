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

class ProjectAjax extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Response::json('Please create an account on the demo to test out this function.', 'error');

        if(!empty($_POST) && (\Altum\Csrf::check('token') || \Altum\Csrf::check('global_token')) && isset($_POST['request_type'])) {

            switch($_POST['request_type']) {

                /* Create */
                case 'create': $this->create(); break;

                /* Update */
                case 'update': $this->update(); break;

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
        $_POST['color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['color']) ? '#000' : $_POST['color'];

        /* Check for possible errors */
        if(empty($_POST['name'])) {
            Response::json(l('global.error_message.empty_fields'), 'error');
        }

        /* Make sure that the user didn't exceed the limit */
        $user_total_projects = database()->query("SELECT COUNT(*) AS `total` FROM `projects` WHERE `user_id` = {$this->user->user_id}")->fetch_object()->total;
        if($this->user->plan_settings->projects_limit != -1 && $user_total_projects >= $this->user->plan_settings->projects_limit) {
            Response::json(l('global.info_message.plan_feature_limit'), 'error');
        }

        /* Insert to database */
        db()->insert('projects', [
            'user_id' => $this->user->user_id,
            'name' => $_POST['name'],
            'color' => $_POST['color'],
            'datetime' => Date::$date,
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItem('projects?user_id=' . $this->user->user_id);

        /* Set a nice success message */
        Response::json(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['name'] . '</strong>'));

    }

    private function update() {
        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Response::json(l('global.info_message.team_no_access'), 'error');
        }

        $_POST['project_id'] = (int) $_POST['project_id'];
        $_POST['name'] = trim(query_clean($_POST['name']));
        $_POST['color'] = !preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['color']) ? '#000' : $_POST['color'];

        /* Check for possible errors */
        if(empty($_POST['name'])) {
            Response::json(l('global.error_message.empty_fields'), 'error');
        }

            /* Insert to database */
            db()->where('project_id', $_POST['project_id'])->where('user_id', $this->user->user_id)->update('projects', [
                'name' => $_POST['name'],
                'color' => $_POST['color'],
                'last_datetime' => Date::$date,
            ]);

            /* Clear the cache */
            \Altum\Cache::$adapter->deleteItem('projects?user_id=' . $this->user->user_id);

            /* Set a nice success message */
            Response::json(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['name'] . '</strong>'));

    }

}
