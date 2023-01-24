<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Models\Page;
use Altum\Traits\Paramsable;

class Controller {
    use Paramsable;

    public $views = [];

    public function __construct(Array $params = []) {

        $this->add_params($params);

    }

    public function add_view_content($name, $data) {

        $this->views[$name] = $data;

    }

    public function run() {

        /* Do we need to show something? */
        if(!\Altum\Router::$controller_settings['has_view']) {
            return;
        }

        if(\Altum\Router::$path == 'l') {
            $wrapper = new \Altum\View('l/wrapper', (array) $this);
        }

        if(\Altum\Router::$path == '') {
            /* Get the top menu custom pages */
            $pages = (new Page())->get_pages('top');

            /* Establish the menu view */
            $menu = new \Altum\View('partials/menu', (array) $this);
            $this->add_view_content('menu', $menu->run([ 'pages' => $pages ]));

            /* Get the footer */
            $pages = (new Page())->get_pages('bottom');

            /* Establish the footer view */
            $footer = new \Altum\View('partials/footer', (array) $this);
            $this->add_view_content('footer', $footer->run([ 'pages' => $pages ]));

            $wrapper = new \Altum\View(\Altum\Router::$controller_settings['wrapper'], (array) $this);
        }


        if(\Altum\Router::$path == 'admin') {
            /* Establish the side menu view */
            $sidebar = new \Altum\View('admin/partials/admin_sidebar', (array) $this);
            $this->add_view_content('admin_sidebar', $sidebar->run());

            /* Establish the top menu view */
            $menu = new \Altum\View('admin/partials/admin_menu', (array) $this);
            $this->add_view_content('admin_menu', $menu->run());

            /* Establish the footer view */
            $footer = new \Altum\View('admin/partials/footer', (array) $this);
            $this->add_view_content('footer', $footer->run());

            $wrapper = new \Altum\View('admin/wrapper', (array) $this);
        }

        echo $wrapper->run();
    }


}
