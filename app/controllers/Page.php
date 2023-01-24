<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Language;
use Altum\Meta;
use Altum\Title;

class Page extends Controller {

    public function index() {

        $url = isset($this->params[0]) ? query_clean($this->params[0]) : null;
        $language = Language::$name;

        /* If the custom page url is set then try to get data from the database */
        $page = $url ? database()->query("
            SELECT *
            FROM `pages`
            WHERE
                (`url` = '{$url}' AND `language` = '{$language}') OR (`url` = '{$url}' AND `language` IS NULL)
            ORDER BY `language` DESC
        ")->fetch_object() ?? null : null;

        /* Redirect if the page does not exist */
        if(!$page) {
            redirect('pages');
        }

        /* Get the page category */
        $pages_category = db()->where('pages_category_id', $page->pages_category_id)->getOne('pages_categories');

        /* Add a new view to the page */
        db()->where('page_id', $page->page_id)->update('pages', ['total_views' => db()->inc()]);

        /* Prepare the View */
        $data = [
            'page'  => $page,
            'pages_category' => $pages_category,
        ];

        $view = new \Altum\View('page/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

        /* Set a custom title */
        Title::set($page->title);

        /* Meta */
        Meta::set_description($page->description);
    }

}


