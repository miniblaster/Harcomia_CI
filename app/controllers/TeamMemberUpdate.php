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
use Altum\Title;

class TeamMemberUpdate extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(!\Altum\Plugin::is_active('teams')) {
            redirect('dashboard');
        }

        $team_member_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$team_member = db()->where('team_member_id', $team_member_id)->getOne('teams_members')) {
            redirect('teams');
        }
        $team_member->access = json_decode($team_member->access);

        if(!$team = db()->where('team_id', $team_member->team_id)->where('user_id', $this->user->user_id)->getOne('teams')) {
            redirect('teams');
        }

        if(!empty($_POST)) {
            /* Make sure the read access is always on */
            $_POST['access'][] = 'read';

            /* Generate the access variable for the database */
            $access = ['read' => true];
            foreach(['create', 'update', 'delete'] as $access_key) {
                $access[$access_key] = in_array($access_key, $_POST['access']);
            }

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            $required_fields = [];
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
                db()->where('team_member_id', $team_member->team_member_id)->update('teams_members', [
                    'access' => json_encode($access),
                    'last_datetime' => \Altum\Date::$date,
                ]);

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItem('team_member?team_id=' . $team_member->team_id . '&user_id=' . $team_member->user_id);

                /* Set a nice success message */
                Alerts::add_success(l('global.success_message.update2'));

                redirect('team-member-update/' . $team_member_id);
            }
        }

        /* Set a custom title */
        Title::set(sprintf(l('team_member_update.title'), $team->name));

        /* Prepare the View */
        $data = [
            'team' => $team,
            'team_member' => $team_member,
        ];

        $view = new \Altum\View('team-member-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
