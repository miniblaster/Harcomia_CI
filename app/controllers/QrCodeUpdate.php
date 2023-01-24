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
use Altum\Date;
use Altum\Uploads;

class QrCodeUpdate extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(!settings()->links->qr_codes_is_enabled) {
            redirect();
        }

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('qr-codes');
        }

        $qr_code_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$qr_code = db()->where('qr_code_id', $qr_code_id)->where('user_id', $this->user->user_id)->getOne('qr_codes')) {
            redirect('qr-codes');
        }
        $qr_code->settings = json_decode($qr_code->settings);

        $qr_code_settings = require APP_PATH . 'includes/qr_code.php';

        /* Existing projects */
        $projects = (new \Altum\Models\Project())->get_projects_by_user_id($this->user->user_id);

        if(!empty($_POST)) {
            $required_fields = ['name', 'type'];
            $settings = [];

            $_POST['name'] = trim(query_clean($_POST['name']));
            $_POST['project_id'] = !empty($_POST['project_id']) && array_key_exists($_POST['project_id'], $projects) ? (int) $_POST['project_id'] : null;
            $_POST['type'] = isset($_POST['type']) && array_key_exists($_POST['type'], $qr_code_settings['type']) ? $_POST['type'] : 'text';
            $settings['style'] = $_POST['style'] = isset($_POST['style']) && in_array($_POST['style'], ['square', 'dot', 'round']) ? $_POST['style'] : 'square';
            $settings['foreground_type'] = $_POST['foreground_type'] = isset($_POST['foreground_type']) && in_array($_POST['foreground_type'], ['color', 'gradient']) ? $_POST['foreground_type'] : 'color';
            switch($_POST['foreground_type']) {
                case 'color':
                    $settings['foreground_color'] = $_POST['foreground_color'] = isset($_POST['foreground_color']) && preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['foreground_color']) ? $_POST['foreground_color'] : '#000000';
                    break;

                case 'gradient':
                    $settings['foreground_gradient_style'] = $_POST['foreground_gradient_style'] = isset($_POST['foreground_gradient_style']) && in_array($_POST['foreground_gradient_style'], ['vertical', 'horizontal', 'diagonal', 'inverse_diagonal', 'radial']) ? $_POST['foreground_gradient_style'] : 'horizontal';
                    $settings['foreground_gradient_one'] = $_POST['foreground_gradient_one'] = isset($_POST['foreground_gradient_one']) && preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['foreground_gradient_one']) ? $_POST['foreground_gradient_one'] : '#000000';
                    $settings['foreground_gradient_two'] = $_POST['foreground_gradient_two'] = isset($_POST['foreground_gradient_two']) && preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['foreground_gradient_two']) ? $_POST['foreground_gradient_two'] : '#000000';
                    break;
            }
            $settings['background_color'] = $_POST['background_color'] = isset($_POST['background_color']) && preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? $_POST['background_color'] : '#ffffff';
            $settings['background_color_transparency'] = $_POST['background_color_transparency'] = isset($_POST['background_color_transparency']) && in_array($_POST['background_color_transparency'], range(0, 100)) ? (int) $_POST['background_color_transparency'] : 0;
            $settings['custom_eyes_color'] = $_POST['custom_eyes_color'] = (bool) (int) ($_POST['custom_eyes_color'] ?? 0);
            if($_POST['custom_eyes_color']) {
                $settings['eyes_inner_color'] = $_POST['eyes_inner_color'] = isset($_POST['eyes_inner_color']) && preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['eyes_inner_color']) ? $_POST['eyes_inner_color'] : '#000000';
                $settings['eyes_outer_color'] = $_POST['eyes_outer_color'] = isset($_POST['eyes_outer_color']) && preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['eyes_outer_color']) ? $_POST['eyes_outer_color'] : '#000000';
            }

            $_POST['qr_code_logo'] = !empty($_FILES['qr_code_logo']['name']) && !isset($_POST['qr_code_logo_remove']);
            $settings['qr_code_logo_size'] = $_POST['qr_code_logo_size'] = isset($_POST['qr_code_logo_size']) && in_array($_POST['qr_code_logo_size'], range(5, 35)) ? (int) $_POST['qr_code_logo_size'] : 25;

            $settings['size'] = $_POST['size'] = isset($_POST['size']) && in_array($_POST['size'], range(50, 2000)) ? (int) $_POST['size'] : 500;
            $settings['margin'] = $_POST['margin'] = isset($_POST['margin']) && in_array($_POST['margin'], range(0, 25)) ? (int) $_POST['margin'] : 1;
            $settings['ecc'] = $_POST['ecc'] = isset($_POST['ecc']) && in_array($_POST['ecc'], ['L', 'M', 'Q', 'H']) ? $_POST['ecc'] : 'M';

            /* Type dependant vars */
            switch($_POST['type']) {
                case 'text':
                    $required_fields[] = 'text';
                    $settings['text'] = $_POST['text'] = mb_substr(input_clean($_POST['text']), 0, $qr_code_settings['type']['text']['max_length']);
                    break;

                case 'url':
                    $required_fields[] = 'url';
                    $settings['url'] = $_POST['url'] = mb_substr(input_clean($_POST['url']), 0, $qr_code_settings['type']['url']['max_length']);
                    break;

                case 'phone':
                    $required_fields[] = 'phone';
                    $settings['phone'] = $_POST['phone'] = mb_substr(input_clean($_POST['phone']), 0, $qr_code_settings['type']['phone']['max_length']);
                    break;

                case 'sms':
                    $required_fields[] = 'sms';
                    $settings['sms'] = $_POST['sms'] = mb_substr(input_clean($_POST['sms']), 0, $qr_code_settings['type']['sms']['max_length']);
                    $settings['sms_body'] = $_POST['sms_body'] = mb_substr(input_clean($_POST['sms_body']), 0, $qr_code_settings['type']['sms']['body']['max_length']);
                    break;

                case 'email':
                    $required_fields[] = 'email';
                    $settings['email'] = $_POST['email'] = mb_substr(input_clean($_POST['email']), 0, $qr_code_settings['type']['email']['max_length']);
                    $settings['email_subject'] = $_POST['email_subject'] = mb_substr(input_clean($_POST['email_subject']), 0, $qr_code_settings['type']['email']['subject']['max_length']);
                    $settings['email_body'] = $_POST['email_body'] = mb_substr(input_clean($_POST['email_body']), 0, $qr_code_settings['type']['email']['body']['max_length']);
                    break;

                case 'whatsapp':
                    $required_fields[] = 'whatsapp';
                    $settings['whatsapp'] = $_POST['whatsapp'] = mb_substr(input_clean($_POST['whatsapp']), 0, $qr_code_settings['type']['whatsapp']['max_length']);
                    $settings['whatsapp_body'] = $_POST['whatsapp_body'] = mb_substr(input_clean($_POST['whatsapp_body']), 0, $qr_code_settings['type']['whatsapp']['body']['max_length']);
                    break;

                case 'facetime':
                    $required_fields[] = 'facetime';
                    $settings['facetime'] = $_POST['facetime'] = mb_substr(input_clean($_POST['facetime']), 0, $qr_code_settings['type']['facetime']['max_length']);
                    break;

                case 'location':
                    $required_fields[] = 'location_latitude';
                    $required_fields[] = 'location_longitude';
                    $settings['location_latitude'] = $_POST['location_latitude'] = (float) mb_substr(input_clean($_POST['location_latitude']), 0, $qr_code_settings['type']['location']['latitude']['max_length']);
                    $settings['location_longitude'] = $_POST['location_longitude'] = (float) mb_substr(input_clean($_POST['location_longitude']), 0, $qr_code_settings['type']['location']['longitude']['max_length']);
                    break;

                case 'wifi':
                    $required_fields[] = 'wifi_ssid';
                    $settings['wifi_ssid'] = $_POST['wifi_ssid'] = mb_substr(input_clean($_POST['wifi_ssid']), 0, $qr_code_settings['type']['wifi']['ssid']['max_length']);
                    $settings['wifi_encryption'] = $_POST['wifi_encryption'] = isset($_POST['wifi_encryption']) && in_array($_POST['wifi_encryption'], ['nopass', 'WEP', 'WPA/WPA2']) ? $_POST['wifi_encryption'] : 'nopass';
                    $settings['wifi_password'] = $_POST['wifi_password'] = mb_substr(input_clean($_POST['wifi_password']), 0, $qr_code_settings['type']['wifi']['password']['max_length']);
                    $settings['wifi_is_hidden'] = $_POST['wifi_is_hidden'] = (int) $_POST['wifi_is_hidden'];
                    break;

                case 'event':
                    $required_fields[] = 'event';
                    $settings['event'] = $_POST['event'] = mb_substr(input_clean($_POST['event']), 0, $qr_code_settings['type']['event']['max_length']);
                    $settings['event_location'] = $_POST['event_location'] = mb_substr(input_clean($_POST['event_location']), 0, $qr_code_settings['type']['event']['location']['max_length']);
                    $settings['event_url'] = $_POST['event_url'] = mb_substr(input_clean($_POST['event_url']), 0, $qr_code_settings['type']['event']['url']['max_length']);
                    $settings['event_note'] = $_POST['event_note'] = mb_substr(input_clean($_POST['event_note']), 0, $qr_code_settings['type']['event']['note']['max_length']);
                    $settings['event_timezone'] = $_POST['event_timezone'] = in_array($_POST['event_timezone'], \DateTimeZone::listIdentifiers()) ? input_clean($_POST['event_timezone']) : Date::$default_timezone;
                    $settings['event_start_datetime'] = $_POST['event_start_datetime'] = (new \DateTime($_POST['event_start_datetime']))->format('Y-m-d\TH:i:s');
                    $settings['event_end_datetime'] = $_POST['event_end_datetime'] = (new \DateTime($_POST['event_end_datetime']))->format('Y-m-d\TH:i:s');
                    break;

                case 'crypto':
                    $required_fields[] = 'crypto_address';
                    $settings['crypto_coin'] = $_POST['crypto_coin'] = isset($_POST['crypto_coin']) && array_key_exists($_POST['crypto_coin'], $qr_code_settings['type']['crypto']['coins']) ? $_POST['crypto_coin'] : array_key_first($qr_code_settings['type']['crypto']['coins']);
                    $settings['crypto_address'] = $_POST['crypto_address'] = mb_substr(input_clean($_POST['crypto_address']), 0, $qr_code_settings['type']['crypto']['address']['max_length']);
                    $settings['crypto_amount'] = $_POST['crypto_amount'] = isset($_POST['crypto_amount']) ? (float) $_POST['crypto_amount'] : null;
                    break;

                case 'vcard':
                    $settings['vcard_first_name'] = $_POST['vcard_first_name'] = mb_substr(input_clean($_POST['vcard_first_name']), 0, $qr_code_settings['type']['vcard']['first_name']['max_length']);
                    $settings['vcard_last_name'] = $_POST['vcard_last_name'] = mb_substr(input_clean($_POST['vcard_last_name']), 0, $qr_code_settings['type']['vcard']['last_name']['max_length']);
                    $settings['vcard_phone'] = $_POST['vcard_phone'] = mb_substr(input_clean($_POST['vcard_phone']), 0, $qr_code_settings['type']['vcard']['phone']['max_length']);
                    $settings['vcard_email'] = $_POST['vcard_email'] = mb_substr(input_clean($_POST['vcard_email']), 0, $qr_code_settings['type']['vcard']['email']['max_length']);
                    $settings['vcard_url'] = $_POST['vcard_url'] = mb_substr(input_clean($_POST['vcard_url']), 0, $qr_code_settings['type']['vcard']['url']['max_length']);
                    $settings['vcard_company'] = $_POST['vcard_company'] = mb_substr(input_clean($_POST['vcard_company']), 0, $qr_code_settings['type']['vcard']['company']['max_length']);
                    $settings['vcard_job_title'] = $_POST['vcard_job_title'] = mb_substr(input_clean($_POST['vcard_job_title']), 0, $qr_code_settings['type']['vcard']['job_title']['max_length']);
                    $settings['vcard_birthday'] = $_POST['vcard_birthday'] = mb_substr(input_clean($_POST['vcard_birthday']), 0, $qr_code_settings['type']['vcard']['birthday']['max_length']);
                    $settings['vcard_street'] = $_POST['vcard_street'] = mb_substr(input_clean($_POST['vcard_street']), 0, $qr_code_settings['type']['vcard']['street']['max_length']);
                    $settings['vcard_city'] = $_POST['vcard_city'] = mb_substr(input_clean($_POST['vcard_city']), 0, $qr_code_settings['type']['vcard']['city']['max_length']);
                    $settings['vcard_zip'] = $_POST['vcard_zip'] = mb_substr(input_clean($_POST['vcard_zip']), 0, $qr_code_settings['type']['vcard']['zip']['max_length']);
                    $settings['vcard_region'] = $_POST['vcard_region'] = mb_substr(input_clean($_POST['vcard_region']), 0, $qr_code_settings['type']['vcard']['region']['max_length']);
                    $settings['vcard_country'] = $_POST['vcard_country'] = mb_substr(input_clean($_POST['vcard_country']), 0, $qr_code_settings['type']['vcard']['country']['max_length']);
                    $settings['vcard_note'] = $_POST['vcard_note'] = mb_substr(input_clean($_POST['vcard_note']), 0, $qr_code_settings['type']['vcard']['note']['max_length']);

                    /* Phone numbers */
                    if(!isset($_POST['vcard_phone_number'])) {
                        $_POST['vcard_phone_number'] = [];
                    }
                    $vcard_phone_numbers = [];
                    foreach($_POST['vcard_phone_number'] as $key => $value) {
                        if(empty(trim($value))) continue;
                        if($key >= 20) continue;

                        $vcard_phone_numbers[] = mb_substr(input_clean($value), 0, $qr_code_settings['type']['vcard']['phone_number']['max_length']);
                    }
                    $settings['vcard_phone_numbers'] = $vcard_phone_numbers;

                    /* Socials */
                    if(!isset($_POST['vcard_social_label'])) {
                        $_POST['vcard_social_label'] = [];
                        $_POST['vcard_social_value'] = [];
                    }

                    $vcard_socials = [];
                    foreach($_POST['vcard_social_label'] as $key => $value) {
                        if(empty(trim($value))) continue;
                        if($key >= 20) continue;

                        $vcard_socials[] = [
                            'label' => mb_substr(input_clean($value), 0, $qr_code_settings['type']['vcard']['social_value']['max_length']),
                            'value' => mb_substr(input_clean($_POST['vcard_social_value'][$key]), 0, $qr_code_settings['type']['vcard']['social_value']['max_length'])
                        ];
                    }
                    $settings['vcard_socials'] = $vcard_socials;

                    break;

                case 'paypal':
                    $required_fields[] = 'paypal_email';
                    $required_fields[] = 'paypal_title';
                    $required_fields[] = 'paypal_currency';
                    $required_fields[] = 'paypal_price';
                    $settings['paypal_type'] = $_POST['paypal_type'] = isset($_POST['paypal_type']) && array_key_exists($_POST['paypal_type'], $qr_code_settings['type']['paypal']['type']) ? $_POST['paypal_type'] : array_key_first($qr_code_settings['type']['paypal']['type']);
                    $settings['paypal_email'] = $_POST['paypal_email'] = mb_substr(input_clean($_POST['paypal_email']), 0, $qr_code_settings['type']['paypal']['email']['max_length']);
                    $settings['paypal_title'] = $_POST['paypal_title'] = mb_substr(input_clean($_POST['paypal_title']), 0, $qr_code_settings['type']['paypal']['title']['max_length']);
                    $settings['paypal_currency'] = $_POST['paypal_currency'] = mb_substr(input_clean($_POST['paypal_currency']), 0, $qr_code_settings['type']['paypal']['currency']['max_length']);
                    $settings['paypal_price'] = $_POST['paypal_price'] = (float) $_POST['paypal_price'];
                    $settings['paypal_thank_you_url'] = $_POST['paypal_thank_you_url'] = mb_substr(input_clean($_POST['paypal_thank_you_url']), 0, $qr_code_settings['type']['paypal']['thank_you_url']['max_length']);
                    $settings['paypal_cancel_url'] = $_POST['paypal_cancel_url'] = mb_substr(input_clean($_POST['paypal_cancel_url']), 0, $qr_code_settings['type']['paypal']['cancel_url']['max_length']);
                    break;
            }

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if($_POST['qr_code_logo']) {
                $file_name = $_FILES['qr_code_logo']['name'];
                $file_extension = explode('.', $file_name);
                $file_extension = mb_strtolower(end($file_extension));
                $file_temp = $_FILES['qr_code_logo']['tmp_name'];

                if($_FILES['qr_code_logo']['error'] == UPLOAD_ERR_INI_SIZE) {
                    Alerts::add_error(sprintf(l('global.error_message.file_size_limit'), $qr_code_settings['qr_code_logo_size_limit']));
                }

                if($_FILES['qr_code_logo']['error'] && $_FILES['qr_code_logo']['error'] != UPLOAD_ERR_INI_SIZE) {
                    Alerts::add_error(l('global.error_message.file_upload'));
                }

                if(!in_array($file_extension, Uploads::get_whitelisted_file_extensions('qr_code_logo'))) {
                    Alerts::add_error(l('global.error_message.invalid_file_type'));
                }

                if(!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {
                    if(!is_writable(UPLOADS_PATH . 'qr_code_logo' . '/')) {
                        Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . 'qr_code_logo' . '/'));
                    }
                }

                if($_FILES['qr_code_logo']['size'] > $qr_code_settings['qr_code_logo_size_limit'] * 1000000) {
                    Alerts::add_error(sprintf(l('global.error_message.file_size_limit'), $qr_code_settings['qr_code_logo_size_limit']));
                }

                if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                    /* Generate new name for image */
                    $image_new_name = md5(time() . rand()) . '.' . $file_extension;

                    /* Offload uploading */
                    if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                        try {
                            $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                            /* Delete current image */
                            $s3->deleteObject([
                                'Bucket' => settings()->offload->storage_name,
                                'Key' => 'uploads/qr_code_logo/' . $qr_code->qr_code_logo,
                            ]);

                            /* Upload image */
                            $result = $s3->putObject([
                                'Bucket' => settings()->offload->storage_name,
                                'Key' => 'uploads/qr_code_logo/' . $image_new_name,
                                'ContentType' => mime_content_type($file_temp),
                                'SourceFile' => $file_temp,
                                'ACL' => 'public-read'
                            ]);
                        } catch (\Exception $exception) {
                            Alerts::add_error($exception->getMessage());
                        }
                    }

                    /* Local uploading */
                    else {
                        /* Delete current image */
                        if(!empty($qr_code->qr_code_logo) && file_exists(UPLOADS_PATH . 'qr_code_logo' . '/' . $qr_code->qr_code_logo)) {
                            unlink(UPLOADS_PATH . 'qr_code_logo' . '/' . $qr_code->qr_code_logo);
                        }

                        /* Upload the original */
                        move_uploaded_file($file_temp, UPLOADS_PATH . 'qr_code_logo' . '/' . $image_new_name);
                    }

                    $qr_code->qr_code_logo = $image_new_name;
                }
            }

            /* Check for the removal of the already uploaded file */
            if(isset($_POST['qr_code_logo_remove'])) {
                \Altum\Uploads::delete_uploaded_file($qr_code->qr_code_logo, 'qr_code_logo');
                $qr_code->qr_code_logo = '';
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                /* QR Code image */
                if($_POST['qr_code']) {
                    $_POST['qr_code'] = base64_decode(mb_substr($_POST['qr_code'], mb_strlen('data:image/svg+xml;base64,')));

                    /* Generate new name for image */
                    $image_new_name = md5(time() . rand()) . '.svg';

                    /* Offload uploading */
                    if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                        try {
                            $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                            /* Delete current image */
                            $s3->deleteObject([
                                'Bucket' => settings()->offload->storage_name,
                                'Key' => 'uploads/qr_code/' . $qr_code->qr_code,
                            ]);

                            /* Upload image */
                            $result = $s3->putObject([
                                'Bucket' => settings()->offload->storage_name,
                                'Key' => 'uploads/qr_code/' . $image_new_name,
                                'ContentType' => 'image/svg+xml',
                                'Body' => $_POST['qr_code'],
                                'ACL' => 'public-read'
                            ]);
                        } catch (\Exception $exception) {
                            Alerts::add_error($exception->getMessage());
                        }
                    }

                    /* Local uploading */
                    else {
                        /* Delete current image */
                        if(!empty($qr_code->qr_code) && file_exists(UPLOADS_PATH . 'qr_code' . '/' . $qr_code->qr_code)) {
                            unlink(UPLOADS_PATH . 'qr_code' . '/' . $qr_code->qr_code);
                        }

                        /* Upload the original */
                        file_put_contents(UPLOADS_PATH . 'qr_code' . '/' . $image_new_name, $_POST['qr_code']);
                    }

                    $qr_code->qr_code = $image_new_name;
                }

                $settings = json_encode($settings);

                /* Database query */
                db()->where('qr_code_id', $qr_code->qr_code_id)->update('qr_codes', [
                    'project_id' => $_POST['project_id'],
                    'name' => $_POST['name'],
                    'type' => $_POST['type'],
                    'settings' => $settings,
                    'qr_code' => $qr_code->qr_code,
                    'qr_code_logo' => $qr_code->qr_code_logo,
                    'last_datetime' => \Altum\Date::$date,
                ]);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['name'] . '</strong>'));

                redirect('qr-code-update/' . $qr_code_id);
            }
        }

        /* Prepare the View */
        $data = [
            'qr_code_settings' => $qr_code_settings,
            'qr_code' => $qr_code,
            'projects' => $projects,
        ];

        $view = new \Altum\View('qr-code-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
