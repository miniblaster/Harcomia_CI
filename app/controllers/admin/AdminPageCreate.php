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

class AdminPageCreate extends Controller {

    public function index() {

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['title'] = input_clean($_POST['title']);
            $_POST['description'] = input_clean($_POST['description']);
            $_POST['type'] = in_array($_POST['type'], ['internal', 'external']) ? input_clean($_POST['type']) : 'internal';
            $_POST['editor'] = in_array($_POST['editor'], ['wysiwyg', 'raw']) ? input_clean($_POST['editor']) : 'raw';
            $_POST['position'] = in_array($_POST['position'], ['hidden', 'top', 'bottom']) ? $_POST['position'] : 'top';
            $_POST['pages_category_id'] = empty($_POST['pages_category_id']) ? null : (int) $_POST['pages_category_id'];
            $_POST['language'] = !empty($_POST['language']) ? input_clean($_POST['language']) : null;
            $_POST['order'] = (int) $_POST['order'];
            $_POST['open_in_new_tab'] = (int) isset($_POST['open_in_new_tab']);

            switch($_POST['type']) {
                case 'internal':
                    $_POST['url'] = get_slug($_POST['url']);
                    break;


                case 'external':
                    $_POST['url'] = input_clean($_POST['url']);
                    break;
            }

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Check for any errors */
            $required_fields = ['title', 'url'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if($_POST['type'] == 'internal' && db()->where('url', $_POST['url'])->where('language', $_POST['language'])->has('pages')) {
                Alerts::add_field_error('url', l('admin_resources.error_message.url_exists'));
            }

            /* If there are no errors, continue */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Database query */
                db()->insert('pages', [
                    'pages_category_id' => $_POST['pages_category_id'],
                    'url' => $_POST['url'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'editor' => $_POST['editor'],
                    'content' => $_POST['content'],
                    'type' => $_POST['type'],
                    'position' => $_POST['position'],
                    'language' => $_POST['language'],
                    'open_in_new_tab' => $_POST['open_in_new_tab'],
                    'order' => $_POST['order'],
                    'datetime' => \Altum\Date::$date,
                    'last_datetime' => \Altum\Date::$date,
                ]);

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItem('pages_' . $_POST['position']);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . $_POST['title'] . '</strong>'));

                redirect('admin/pages');
            }

        }

        /* Get the pages categories available */
        $pages_categories = db()->get('pages_categories', null, ['pages_category_id', 'title']);

        /* Set default values */
        $values = [
            'pages_category_id' => $_POST['pages_category_id'] ?? '',
            'title' => $_POST['title'] ?? '',
            'url' => $_POST['url'] ?? '',
            'description' => $_POST['description'] ?? '',
            'editor' => $_POST['editor'] ?? 'wysiwyg',
            'content' => $_POST['content'] ?? '',
            'type' => $_POST['type'] ?? '',
            'position' => $_POST['position'] ?? '',
            'language' => $_POST['language'] ?? '',
            'icon' => $_POST['icon'] ?? '',
            'order' => $_POST['order'] ?? 0,
            'open_in_new_tab' => $_POST['open_in_new_tab'] ?? 1,
        ];

        $data = [
            'values' => $values,
            'pages_categories' => $pages_categories
        ];

        /* Main View */
        $view = new \Altum\View('admin/page-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));
    }

}
