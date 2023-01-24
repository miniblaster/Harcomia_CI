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
use Altum\Language;

class AdminLanguageUpdate extends Controller {

    public function index() {

        $language_name = isset($this->params[0]) ? $this->params[0] : null;

        /* Check if language exists */
        if(!isset(Language::$languages[$language_name])) {
            redirect('admin/languages');
        }

        /* Make sure to load up in memory the language that is being edited and the main language */
        Language::get(Language::$main_name);
        Language::get($language_name);

        $language = Language::$languages[$language_name];

        if(!empty($_POST)) {
            /* Clean some posted variables */
            $_POST['language_name'] = input_clean($_POST['language_name']);
            $_POST['language_code'] = mb_strtolower(input_clean($_POST['language_code']));
            $_POST['status'] = isset($_POST['status']) && in_array($_POST['status'], ['active', 'disabled']) ? $_POST['status'] : 'active';

            $language_strings = '';
            $admin_language_strings = '';
            foreach(\Altum\Language::$languages[\Altum\Language::$main_name]['content'] as $key => $value) {
                $form_key = str_replace('.', '##', $key);

                if(isset($_POST[$form_key]) && !empty($_POST[$form_key])) {
                    $values[$form_key] = $_POST[$form_key];
                    $_POST[$form_key] = addcslashes($_POST[$form_key], "'");

                    if(mb_substr($key, 0, strlen('admin_')) == 'admin_') {
                        $admin_language_strings .= "\t'{$key}' => '{$_POST[$form_key]}',\n";
                    } else {
                        $language_strings .= "\t'{$key}' => '{$_POST[$form_key]}',\n";
                    }
                }

                /* Check if the translation already exists in the file, if not submitted in the form */
                else {
                    $potential_already_existing_value = array_key_exists($key, $language['content']);

                    if($potential_already_existing_value) {
                        $potential_already_existing_value = addcslashes($language['content'][$key], "'");

                        if(mb_substr($key, 0, strlen('admin_')) == 'admin_') {
                            $admin_language_strings .= "\t'{$key}' => '{$potential_already_existing_value}',\n";
                        } else {
                            $language_strings .= "\t'{$key}' => '{$potential_already_existing_value}',\n";
                        }
                    }
                }
            }

            $language_content = function($language_strings) {
                return <<<ALTUM
<?php

return [
{$language_strings}
];
ALTUM;
            };

            //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

            /* Check for any errors */
            $required_fields = ['language_name', 'language_code'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!is_writable(Language::$path)) {
                Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), Language::$path));
            }

            if(!is_writable(Language::$path . 'admin/')) {
                Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), Language::$path . 'admin/'));
            }

            if(($_POST['language_name'] != $language_name && in_array($_POST['language_name'], Language::$languages))) {
                Alerts::add_error(sprintf(l('admin_languages.error_message.language_exists'), $_POST['language_name'], $_POST['language_code']));
            }

            if($_POST['language_code'] != $language['code']) {
                foreach(Language::$languages as $lang) {
                    if($lang['code'] == $_POST['language_code']) {
                        Alerts::add_error(sprintf(l('admin_languages.error_message.language_exists'), $_POST['language_name'], $_POST['language_code']));
                        break;
                    }
                }
            }

            /* If there are no errors, continue */
            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                file_put_contents(Language::$path . $_POST['language_name'] . '#' . $_POST['language_code'] . '#' . $_POST['status'] . '.php', $language_content($language_strings));
                file_put_contents(Language::$path . 'admin/' . $_POST['language_name'] . '#' . $_POST['language_code'] . '#' . $_POST['status'] . '.php', $language_content($admin_language_strings));

                chmod(Language::$path . $_POST['language_name'] . '#' . $_POST['language_code'] . '#' . $_POST['status'] . '.php', 0777);
                chmod(Language::$path . 'admin/' . $_POST['language_name'] . '#' . $_POST['language_code'] . '#' . $_POST['status'] . '.php', 0777);

                /* Change the name of the file if needed */
                if($_POST['language_code'] != $language['code'] || $_POST['language_name'] != $language['name'] || $_POST['status'] != $language['status']) {
                    if(file_exists(Language::$path . $language['name'] . '#' . $language['code'] . '.php')) {
                        unlink(Language::$path . $language['name'] . '#' . $language['code'] . '.php');
                        unlink(Language::$path . 'admin/' . $language['name'] . '#' . $language['code'] . '.php');
                    }

                    if(file_exists(Language::$path . $language['name'] . '#' . $language['code'] . '#' . $language['status'] . '.php')) {
                        unlink(Language::$path . $language['name'] . '#' . $language['code'] . '#' . $language['status'] . '.php');
                        unlink(Language::$path . 'admin/' . $language['name'] . '#' . $language['code'] . '#' . $language['status'] . '.php');
                    }
                }

                sleep(3);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['language_name'] . '</strong>'));

                /* Redirect */
                redirect('admin/language-update/' . $_POST['language_name']);
            }

        }

        /* Main View */
        $data = [
            'language' => $language,
        ];

        $view = new \Altum\View('admin/language-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
