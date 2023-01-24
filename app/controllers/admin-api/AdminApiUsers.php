<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Models\User;
use Altum\Response;
use Altum\Traits\Apiable;

class AdminApiUsers extends Controller {
    use Apiable;

    public function index() {

        $this->verify_request(true);

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

                    if(isset($this->params[1]) && $this->params[1] == 'one-time-login-code') {
                        $this->one_time_login_code();
                    }elseif ($this->params[0] == 'search') {
                        return $this->search_user();
                    } else {
                        $this->patch();
                    }


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
        $filters = (new \Altum\Filters([], ['name', 'email'], ['email', 'datetime', 'last_activity', 'name', 'total_logins']));
        $filters->set_default_order_by('user_id', settings()->main->default_order_type);
        $filters->set_default_results_per_page(settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `users` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin-api/users?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $data = [];
        $data_result = database()->query("
            SELECT
                *
            FROM
                `users`
            WHERE
                1 = 1
                {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}
                  
            {$paginator->get_sql_limit()}
        ");
        while($row = $data_result->fetch_object()) {

            /* Prepare the data */
            $row = [
                'id' => (int) $row->user_id,
                'name' => $row->name,
                'email' => $row->email,
                'api_key' => $row->api_key,
                'billing' => json_decode($row->billing),
                'status' => (bool) $row->status,
                'plan_id' => $row->plan_id,
                'plan_expiration_date' => $row->plan_expiration_date,
                'plan_settings' => json_decode($row->plan_settings),
                'plan_trial_done' => (bool) $row->plan_trial_done,
                'language' => $row->language,
                'timezone' => $row->timezone,
                'ip' => $row->ip,
                'country' => $row->country,
                'datetime' => $row->datetime,
                'last_activity' => $row->last_activity,
                'total_logins' => (int) $row->total_logins,
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

        $user_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $user = db()->where('user_id', $user_id)->getOne('users');

        /* We haven't found the resource */
        if(!$user) {
            $this->return_404();
        }

        /* Prepare the data */
        $data = [
            'id' => (int) $user->user_id,
            'name' => $user->name,
            'email' => $user->email,
            'api_key' => $user->api_key,
            'billing' => json_decode($user->billing),
            'status' => (bool) $user->status,
            'plan_id' => $user->plan_id,
            'plan_expiration_date' => $user->plan_expiration_date,
            'plan_settings' => json_decode($user->plan_settings),
            'plan_trial_done' => (bool) $user->plan_trial_done,
            'language' => $user->language,
            'timezone' => $user->timezone,
            'ip' => $user->ip,
            'country' => $user->country,
            'datetime' => $user->datetime,
            'last_activity' => $user->last_activity,
            'total_logins' => (int) $user->total_logins,
        ];

        Response::jsonapi_success($data);

    }

    private function post() {

        $required_fields = ['name', 'email' ,'password'];

        /* Check for any errors */
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                $this->response_error(l('global.error_message.empty_fields'), 401);
                break 1;
            }
        }

        if(mb_strlen($_POST['name']) < 1 || mb_strlen($_POST['name']) > 64) {
            $this->response_error(l('admin_user_create.error_message.name_length'), 401);
        }
        if(db()->where('email', $_POST['email'])->has('users')) {
            $this->response_error(l('admin_user_create.error_message.email_exists'), 401);
        }
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $this->response_error(l('global.error_message.invalid_email'), 401);
        }
        if(mb_strlen($_POST['password']) < 6 || mb_strlen($_POST['password']) > 64) {
            $this->response_error(l('global.error_message.password_length'), 401);
        }

        /* Define some needed variables */
        $_POST['name'] = mb_substr(trim(input_clean($_POST['name'])), 0, 64);
        $_POST['email'] = mb_substr(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL), 0, 320);

        $registered_user = (new User())->create(
            $_POST['email'],
            $_POST['password'],
            $_POST['name'],
            1,
            'admin_api_create',
            null,
            null,
            'free',
            json_encode(settings()->plan_free->settings),
            null,
            settings()->main->default_timezone,
            true
        );

        /* Send webhook notification if needed */
        if(settings()->webhooks->user_new) {

            \Unirest\Request::post(settings()->webhooks->user_new, [], [
                'user_id' => $registered_user['user_id'],
                'email' => $_POST['email'],
                'name' => $_POST['name'],
                'source' => 'admin_api_create',
            ]);

        }

        /* Prepare the data */
        $data = [
            'id' => $registered_user['user_id']
        ];

        Response::jsonapi_success($data, null, 201);

    }

    private function search_user(){
        $post_data = file_get_contents('php://input');
        $post_data = json_decode($post_data);
        
        $email = $post_data->email;
        $user = db()->where('email', $email)->getOne('users');

        if(!$user) {
            Response::jsonapi_error([[
                'title' => l('api.error_message.not_found'),
                'status' => '404'
            ]], null, 404);
        }

        $data = [
            'id' => (int) $user->user_id,
        ];

        Response::jsonapi_success($data);

    }


    private function patch() {

        $user_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $user = db()->where('user_id', $user_id)->getOne('users');

        /* We haven't found the resource */
        if(!$user) {
            $this->return_404();
        }

        if(isset($_POST['name']) && (mb_strlen($_POST['name']) < 1 || mb_strlen($_POST['name']) > 64)) {
            $this->response_error(l('admin_user_create.error_message.name_length'), 401);
        }
        if(isset($_POST['email']) && $user->email != $_POST['email'] && db()->where('email', $_POST['email'])->has('users')) {
            $this->response_error(l('admin_user_create.error_message.email_exists'), 401);
        }
        if(isset($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $this->response_error(l('global.error_message.invalid_email'), 401);
        }
        if(isset($_POST['password']) && (mb_strlen($_POST['password']) < 6 || mb_strlen($_POST['password']) > 64)) {
            $this->response_error(l('global.error_message.password_length'), 401);
        }

        /* Define some needed variables */
        $name = isset($_POST['name']) ? mb_substr(trim(input_clean($_POST['name'])), 0, 64) : $user->name;
        $email = isset($_POST['email']) ? mb_substr(trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)), 0, 128) : $user->email;
        $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user->password;
        $status = isset($_POST['status']) ? (int) $_POST['status'] : $user->status;
        $type = isset($_POST['type']) ? (int) $_POST['type'] : $user->type;

        $plan_id = $user->plan_id;
        $plan_settings = $user->plan_settings;

        if(isset($_POST['plan_id'])) {
            switch($_POST['plan_id']) {
                case 'free':

                    $plan_id = 'free';
                    $plan_settings = json_encode(settings()->plan_free->settings);

                    break;

                default:

                    $_POST['plan_id'] = (int) $_POST['plan_id'];

                    /* Make sure this plan exists */
                    if(!$plan_settings = db()->where('plan_id', $_POST['plan_id'])->getValue('plans', 'settings')) {
                        $this->response_error();
                    }

                    $plan_id = $_POST['plan_id'];

                    break;
            }
        }

        $plan_expiration_date = isset($_POST['plan_expiration_date']) ? (new \DateTime($_POST['plan_expiration_date']))->format('Y-m-d H:i:s') : $user->plan_expiration_date;
        $plan_trial_done = isset($_POST['plan_trial_done']) ? (int) $_POST['plan_trial_done'] : $user->plan_trial_done;

        /* Update the basic user settings */
        db()->where('user_id', $user->user_id)->update('users', [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'status' => $status,
            'type' => $type,
            'plan_id' => $plan_id,
            'plan_expiration_date' => $plan_expiration_date,
            'plan_expiry_reminder' => $user->plan_expiration_date != $plan_expiration_date ? 0 : 1,
            'plan_settings' => $plan_settings,
            'plan_trial_done' => $plan_trial_done
        ]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $user->user_id);

        /* Prepare the data */
        $data = [
            'id' => $user->user_id
        ];

        Response::jsonapi_success($data, null, 200);

    }

    private function one_time_login_code() {

        $user_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $user = db()->where('user_id', $user_id)->getOne('users');

        /* We haven't found the resource */
        if(!$user) {
            $this->return_404();
        }

        /* Define some needed variables */
        $one_time_login_code = md5($user->email . $user->datetime . time());

        /* Update the basic user settings */
        db()->where('user_id', $user->user_id)->update('users', ['one_time_login_code' => $one_time_login_code]);

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $user->user_id);

        /* Prepare the data */
        $data = [
            'one_time_login_code' => $one_time_login_code,
            'url' => url('login/one-time-login-code/' . $one_time_login_code),
            'id' => $user->user_id
        ];

        Response::jsonapi_success($data, null, 200);

    }

    private function delete() {

        $user_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $user = db()->where('user_id', $user_id)->getOne('users');

        /* We haven't found the resource */
        if(!$user) {
            $this->return_404();
        }

        if($user->user_id == $this->api_user->user_id) {
            $this->response_error(l('admin_users.error_message.self_delete'), 401);
        }

        /* Delete the user */
        (new User())->delete($user->user_id);

        http_response_code(200);
        die();

    }

}
