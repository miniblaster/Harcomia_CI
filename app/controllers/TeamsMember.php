<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;


class TeamsMember extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(!\Altum\Plugin::is_active('teams')) {
            redirect('dashboard');
        }

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters([], ['name'], ['datetime']));
        $filters->set_default_order_by('team_member_id', settings()->main->default_order_type);
        $filters->set_default_results_per_page(settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `teams_members` WHERE (`user_id` = {$this->user->user_id} OR `user_email` = '{$this->user->email}') {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('teams-member?' . $filters->get_get() . 'page=%d')));

        /* Get the teams list for the user */
        $teams_member = [];
        $teams_member_result = database()->query("
            SELECT `teams`.`name`, `teams_members`.*
            FROM `teams_members` 
            LEFT JOIN `teams` ON `teams`.`team_id` = `teams_members`.`team_id` 
            WHERE 
                  (`teams_members`.`user_id` = {$this->user->user_id} 
                  OR `teams_members`.`user_email` = '{$this->user->email}')
                  {$filters->get_sql_where('teams_members')} 
            {$filters->get_sql_order_by('teams_members')} 
            {$paginator->get_sql_limit()}
        ");
        while($row = $teams_member_result->fetch_object()) {
            $row->access = json_decode($row->access);
            $teams_member[] = $row;
        }

        /* Export handler */
        process_export_json($teams_member, 'include', ['team_member_id', 'team_id', 'user_id', 'name', 'status', 'access', 'datetime', 'last_datetime']);
        process_export_csv($teams_member, 'include', ['team_member_id', 'team_id', 'user_id', 'name', 'status', 'datetime', 'last_datetime']);

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Prepare the View */
        $data = [
            'teams_member' => $teams_member,
            'total_teams' => $total_rows,
            'filters' => $filters,
            'pagination' => $pagination
        ];

        $view = new \Altum\View('teams-member/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
