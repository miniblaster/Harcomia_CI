<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

class AdminIndex extends Controller {

    public function index() {

        $biolink_links = db()->where('type', 'biolink')->getValue('links', 'count(`link_id`)');
        $shortened_links = db()->where('type', 'link')->getValue('links', 'count(`link_id`)');
        $track_links = db()->getValue('track_links', 'MAX(`id`)');
        $qr_codes = db()->getValue('qr_codes', 'count(`qr_code_id`)');
        $domains = db()->getValue('domains', 'count(`domain_id`)');
        $users = db()->getValue('users', 'count(`user_id`)');

        if(in_array(settings()->license->type, ['Extended License', 'extended'])) {
            $payments = db()->getValue('payments', 'count(`id`)');
            $payments_total_amount = db()->getValue('payments', 'sum(`total_amount`)');
        } else {
            $payments = $payments_total_amount = 0;
        }

        /* Requested plan details */
        $plans = [];
        $plans_result = database()->query("SELECT `plan_id`, `name` FROM `plans`");
        while($row = $plans_result->fetch_object()) {
            $plans[$row->plan_id] = $row;
        }

        /* Main View */
        $data = [
            'biolink_links' => $biolink_links,
            'shortened_links' => $shortened_links,
            'track_links' => $track_links,
            'qr_codes' => $qr_codes,
            'domains' => $domains,
            'users' => $users,
            'payments' => $payments,
            'payments_total_amount' => $payments_total_amount,
            'plans' => $plans,
        ];

        $view = new \Altum\View('admin/index/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
