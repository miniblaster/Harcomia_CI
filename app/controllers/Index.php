<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Meta;
use Altum\Models\Page;

class Index extends Controller {

    public function index() {

        /* Custom index redirect if set */
        if(!empty(settings()->main->index_url)) {
            header('Location: ' . settings()->main->index_url); die();
        }

        /* Plans View */
        $view = new \Altum\View('partials/plans', (array) $this);
        $this->add_view_content('plans', $view->run());

        /* Opengraph image */
        if(settings()->main->opengraph) {
            Meta::set_social_url(SITE_URL);
            Meta::set_social_description(l('index.meta_description'));
            Meta::set_social_image(UPLOADS_FULL_URL . 'main/' .settings()->main->opengraph);
        }

        $total_links = database()->query("SELECT MAX(`link_id`) AS `total` FROM `links`")->fetch_object()->total ?? 0;
        $total_qr_codes = database()->query("SELECT MAX(`qr_code_id`) AS `total` FROM `qr_codes`")->fetch_object()->total ?? 0;
        $total_track_links = database()->query("SELECT MAX(`id`) AS `total` FROM `track_links`")->fetch_object()->total ?? 0;

        /* Establish the menu view */
        $menu = new \Altum\View('partials/index_menu', (array) $this);
        $this->add_view_content('index_menu', $menu->run([ 'pages' => (new Page())->get_pages('top') ]));

        /* Main View */
        $view = new \Altum\View('index/index', (array) $this);
        $this->add_view_content('content', $view->run([
            'total_links' => $total_links,
            'total_qr_codes' => $total_qr_codes,
            'total_track_links' => $total_track_links,
        ]));

    }

}
