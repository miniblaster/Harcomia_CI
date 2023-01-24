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
use Altum\Models\QrCode;
use Altum\Response;
use Altum\Traits\Apiable;
use Altum\Uploads;
use Unirest\Request;

class ApiQrCodes extends Controller {
    use Apiable;

    public function index() {

        $this->verify_request();

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
                    $this->patch();
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
        $filters = (new \Altum\Filters([], [], []));
        $filters->set_default_order_by('qr_code_id', settings()->main->default_order_type);
        $filters->set_default_results_per_page(settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `qr_codes` WHERE `user_id` = {$this->api_user->user_id}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('api/qr_codes?' . $filters->get_get() . '&page=%d')));

        /* Get the data */
        $data = [];
        $data_result = database()->query("
            SELECT
                *
            FROM
                `qr_codes`
            WHERE
                `user_id` = {$this->api_user->user_id}
                {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}
                  
            {$paginator->get_sql_limit()}
        ");
        while($row = $data_result->fetch_object()) {

            /* Prepare the data */
            $row = [
                'id' => (int) $row->qr_code_id,
                'type' => $row->type,
                'name' => $row->name,
                'qr_code' => UPLOADS_FULL_URL . 'qr_code/' . $row->qr_code,
                'qr_code_logo' => UPLOADS_FULL_URL . 'qr_code_logo/' . $row->qr_code,
                'settings' => json_decode($row->settings),
                'last_datetime' => $row->last_datetime,
                'datetime' => $row->datetime,
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

        $qr_code_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $qr_code = db()->where('qr_code_id', $qr_code_id)->where('user_id', $this->api_user->user_id)->getOne('qr_codes');

        /* We haven't found the resource */
        if(!$qr_code) {
            $this->return_404();
        }

        /* Prepare the data */
        $data = [
            'id' => (int) $qr_code->qr_code_id,
            'name' => $qr_code->name,
            'type' => $qr_code->type,
            'qr_code' => UPLOADS_FULL_URL . 'qr_code/' . $qr_code->qr_code,
            'qr_code_logo' => UPLOADS_FULL_URL . 'qr_code_logo/' . $qr_code->qr_code,
            'settings' => json_decode($qr_code->settings),
            'last_datetime' => $qr_code->last_datetime,
            'datetime' => $qr_code->datetime,
        ];

        Response::jsonapi_success($data);

    }

    private function post() {

        /* Check for the plan limit */
        $total_rows = db()->where('user_id', $this->api_user->user_id)->getValue('qr_codes', 'count(`qr_code_id`)');

        if($this->api_user->plan_settings->qr_codes_limit != -1 && $total_rows >= $this->api_user->plan_settings->qr_codes_limit) {
            $this->response_error(l('global.info_message.plan_feature_limit'), 401);
        }

        $qr_code_settings = require APP_PATH . 'includes/qr_code.php';

        /* Existing projects */
        $projects = (new \Altum\Models\Project())->get_projects_by_user_id($this->api_user->user_id);

        $_POST['name'] = trim($_POST['name'] ?? null);
        $_POST['project_id'] = !empty($_POST['project_id']) && array_key_exists($_POST['project_id'], $projects) ? (int) $_POST['project_id'] : null;
        $_POST['type'] = isset($_POST['type']) && array_key_exists($_POST['type'], $qr_code_settings['type']) ? $_POST['type'] : 'text';

        /* Settings & qr code */
        $settings = [];
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
                $settings['text'] = $_POST['text'] = mb_substr(trim(input_clean($_POST['text'] ?? null)), 0, $qr_code_settings['type']['text']['max_length']);
                break;

            case 'url':
                $required_fields[] = 'url';
                $settings['url'] = $_POST['url'] = mb_substr(trim(input_clean($_POST['url'] ?? null)), 0, $qr_code_settings['type']['url']['max_length']);
                break;

            case 'phone':
                $required_fields[] = 'phone';
                $settings['phone'] = $_POST['phone'] = mb_substr(trim(input_clean($_POST['phone'] ?? null)), 0, $qr_code_settings['type']['phone']['max_length']);
                break;

            case 'sms':
                $required_fields[] = 'sms';
                $settings['sms'] = $_POST['sms'] = mb_substr(trim(input_clean($_POST['sms'] ?? null)), 0, $qr_code_settings['type']['sms']['max_length']);
                $settings['sms_body'] = $_POST['sms_body'] = mb_substr(trim(input_clean($_POST['sms_body'] ?? null)), 0, $qr_code_settings['type']['sms']['body']['max_length']);
                break;

            case 'email':
                $required_fields[] = 'email';
                $settings['email'] = $_POST['email'] = mb_substr(trim(input_clean($_POST['email'] ?? null)), 0, $qr_code_settings['type']['email']['max_length']);
                $settings['email_subject'] = $_POST['email_subject'] = mb_substr(trim(input_clean($_POST['email_subject'] ?? null)), 0, $qr_code_settings['type']['email']['subject']['max_length']);
                $settings['email_body'] = $_POST['email_body'] = mb_substr(trim(input_clean($_POST['email_body'] ?? null)), 0, $qr_code_settings['type']['email']['body']['max_length']);
                break;

            case 'whatsapp':
                $required_fields[] = 'whatsapp';
                $settings['whatsapp'] = $_POST['whatsapp'] = mb_substr(trim(input_clean($_POST['whatsapp'] ?? null)), 0, $qr_code_settings['type']['whatsapp']['max_length']);
                $settings['whatsapp_body'] = $_POST['whatsapp_body'] = mb_substr(trim(input_clean($_POST['whatsapp_body'] ?? null)), 0, $qr_code_settings['type']['whatsapp']['body']['max_length']);
                break;

            case 'facetime':
                $required_fields[] = 'facetime';
                $settings['facetime'] = $_POST['facetime'] = mb_substr(trim(input_clean($_POST['facetime'] ?? null)), 0, $qr_code_settings['type']['facetime']['max_length']);
                break;

            case 'location':
                $required_fields[] = 'location_latitude';
                $required_fields[] = 'location_longitude';
                $settings['location_latitude'] = $_POST['location_latitude'] = (float) mb_substr(trim(input_clean($_POST['location_latitude'] ?? null)), 0, $qr_code_settings['type']['location']['latitude']['max_length']);
                $settings['location_longitude'] = $_POST['location_longitude'] = (float) mb_substr(trim(input_clean($_POST['location_longitude'] ?? null)), 0, $qr_code_settings['type']['location']['longitude']['max_length']);
                break;

            case 'wifi':
                $required_fields[] = 'wifi_ssid';
                $settings['wifi_ssid'] = $_POST['wifi_ssid'] = mb_substr(trim(input_clean($_POST['wifi_ssid'] ?? null)), 0, $qr_code_settings['type']['wifi']['ssid']['max_length']);
                $settings['wifi_encryption'] = $_POST['wifi_encryption'] = isset($_POST['wifi_encryption']) && in_array($_POST['wifi_encryption'], ['nopass', 'WEP', 'WPA/WPA2']) ? $_POST['wifi_encryption'] : 'nopass';
                $settings['wifi_password'] = $_POST['wifi_password'] = mb_substr(trim(input_clean($_POST['wifi_password'] ?? null)), 0, $qr_code_settings['type']['wifi']['password']['max_length']);
                $settings['wifi_is_hidden'] = $_POST['wifi_is_hidden'] = (int) ($_POST['wifi_is_hidden'] ?? 0);
                break;

            case 'event':
                $required_fields[] = 'event';
                $settings['event'] = $_POST['event'] = mb_substr(trim(input_clean($_POST['event'] ?? null)), 0, $qr_code_settings['type']['event']['max_length']);
                $settings['event_location'] = $_POST['event_location'] = mb_substr(trim(input_clean($_POST['event_location'] ?? null)), 0, $qr_code_settings['type']['event']['location']['max_length']);
                $settings['event_url'] = $_POST['event_url'] = mb_substr(trim(input_clean($_POST['event_url'] ?? null)), 0, $qr_code_settings['type']['event']['url']['max_length']);
                $settings['event_note'] = $_POST['event_note'] = mb_substr(trim(input_clean($_POST['event_note'] ?? null)), 0, $qr_code_settings['type']['event']['note']['max_length']);
                $settings['event_timezone'] = $_POST['event_timezone'] = in_array($_POST['event_timezone'], \DateTimeZone::listIdentifiers()) ? input_clean($_POST['event_timezone']) : Date::$default_timezone;
                $settings['event_start_datetime'] = $_POST['event_start_datetime'] = (new \DateTime($_POST['event_start_datetime']))->format('Y-m-d\TH:i:s');
                $settings['event_end_datetime'] = $_POST['event_end_datetime'] = (new \DateTime($_POST['event_end_datetime']))->format('Y-m-d\TH:i:s');
                break;

            case 'crypto':
                $required_fields[] = 'crypto_address';
                $settings['crypto_coin'] = $_POST['crypto_coin'] = isset($_POST['crypto_coin']) && array_key_exists($_POST['crypto_coin'], $qr_code_settings['type']['crypto']['coins']) ? $_POST['crypto_coin'] : array_key_first($qr_code_settings['type']['crypto']['coins']);
                $settings['crypto_address'] = $_POST['crypto_address'] = mb_substr(trim(input_clean($_POST['crypto_address'] ?? null)), 0, $qr_code_settings['type']['crypto']['address']['max_length']);
                $settings['crypto_amount'] = $_POST['crypto_amount'] = isset($_POST['crypto_amount']) ? (float) $_POST['crypto_amount'] : null;
                break;

            case 'vcard':
                $settings['vcard_first_name'] = $_POST['vcard_first_name'] = mb_substr(trim(input_clean($_POST['vcard_first_name'] ?? null)), 0, $qr_code_settings['type']['vcard']['first_name']['max_length']);
                $settings['vcard_last_name'] = $_POST['vcard_last_name'] = mb_substr(trim(input_clean($_POST['vcard_last_name'] ?? null)), 0, $qr_code_settings['type']['vcard']['last_name']['max_length']);
                $settings['vcard_phone'] = $_POST['vcard_phone'] = mb_substr(trim(input_clean($_POST['vcard_phone'] ?? null)), 0, $qr_code_settings['type']['vcard']['phone']['max_length']);
                $settings['vcard_email'] = $_POST['vcard_email'] = mb_substr(trim(input_clean($_POST['vcard_email'] ?? null)), 0, $qr_code_settings['type']['vcard']['email']['max_length']);
                $settings['vcard_url'] = $_POST['vcard_url'] = mb_substr(trim(input_clean($_POST['vcard_url'] ?? null)), 0, $qr_code_settings['type']['vcard']['url']['max_length']);
                $settings['vcard_company'] = $_POST['vcard_company'] = mb_substr(trim(input_clean($_POST['vcard_company'] ?? null)), 0, $qr_code_settings['type']['vcard']['company']['max_length']);
                $settings['vcard_job_title'] = $_POST['vcard_job_title'] = mb_substr(trim(input_clean($_POST['vcard_job_title'] ?? null)), 0, $qr_code_settings['type']['vcard']['job_title']['max_length']);
                $settings['vcard_birthday'] = $_POST['vcard_birthday'] = mb_substr(trim(input_clean($_POST['vcard_birthday'] ?? null)), 0, $qr_code_settings['type']['vcard']['birthday']['max_length']);
                $settings['vcard_street'] = $_POST['vcard_street'] = mb_substr(trim(input_clean($_POST['vcard_street'] ?? null)), 0, $qr_code_settings['type']['vcard']['street']['max_length']);
                $settings['vcard_city'] = $_POST['vcard_city'] = mb_substr(trim(input_clean($_POST['vcard_city'] ?? null)), 0, $qr_code_settings['type']['vcard']['city']['max_length']);
                $settings['vcard_zip'] = $_POST['vcard_zip'] = mb_substr(trim(input_clean($_POST['vcard_zip'] ?? null)), 0, $qr_code_settings['type']['vcard']['zip']['max_length']);
                $settings['vcard_region'] = $_POST['vcard_region'] = mb_substr(trim(input_clean($_POST['vcard_region'] ?? null)), 0, $qr_code_settings['type']['vcard']['region']['max_length']);
                $settings['vcard_country'] = $_POST['vcard_country'] = mb_substr(trim(input_clean($_POST['vcard_country'] ?? null)), 0, $qr_code_settings['type']['vcard']['country']['max_length']);
                $settings['vcard_note'] = $_POST['vcard_note'] = mb_substr(trim(input_clean($_POST['vcard_note'] ?? null)), 0, $qr_code_settings['type']['vcard']['note']['max_length']);

                if(!isset($_POST['vcard_social_label'])) {
                    $_POST['vcard_social_label'] = [];
                    $_POST['vcard_social_value'] = [];
                }

                $vcard_socials = [];
                foreach($_POST['vcard_social_label'] as $key => $value) {
                    if(empty(trim($value))) continue;
                    if($key >= 20) continue;

                    $vcard_socials[] = [
                        'label' => mb_substr(trim(input_clean($value)), 0, $qr_code_settings['type']['vcard']['social_value']['max_length']),
                        'value' => mb_substr(trim(input_clean($_POST['vcard_social_value'][$key])), 0, $qr_code_settings['type']['vcard']['social_value']['max_length'])
                    ];
                }
                $settings['vcard_socials'] = $vcard_socials;
                break;

            case 'paypal':
                $required_fields[] = 'paypal_email';
                $required_fields[] = 'paypal_title';
                $required_fields[] = 'paypal_currency';
                $required_fields[] = 'paypal_price';
                $settings['paypal_type'] = $_POST['paypal_type'] = isset($_POST['paypal_type']) && array_key_exists($_POST['paypal_type'] ?? 'buy_now', $qr_code_settings['type']['paypal']['type']) ? $_POST['paypal_type'] : array_key_first($qr_code_settings['type']['paypal']['type']);
                $settings['paypal_email'] = $_POST['paypal_email'] = mb_substr(trim(input_clean($_POST['paypal_email'] ?? null)), 0, $qr_code_settings['type']['paypal']['email']['max_length']);
                $settings['paypal_title'] = $_POST['paypal_title'] = mb_substr(trim(input_clean($_POST['paypal_title'] ?? null)), 0, $qr_code_settings['type']['paypal']['title']['max_length']);
                $settings['paypal_currency'] = $_POST['paypal_currency'] = mb_substr(trim(input_clean($_POST['paypal_currency'] ?? null)), 0, $qr_code_settings['type']['paypal']['currency']['max_length']);
                $settings['paypal_price'] = $_POST['paypal_price'] = (float) $_POST['paypal_price'] ?? 0;
                $settings['paypal_thank_you_url'] = $_POST['paypal_thank_you_url'] = mb_substr(trim(input_clean($_POST['paypal_thank_you_url'] ?? null)), 0, $qr_code_settings['type']['paypal']['thank_you_url']['max_length']);
                $settings['paypal_cancel_url'] = $_POST['paypal_cancel_url'] = mb_substr(trim(input_clean($_POST['paypal_cancel_url'] ?? null)), 0, $qr_code_settings['type']['paypal']['cancel_url']['max_length']);
                break;
        }

        /* Check for any errors */
        $required_fields = ['type', 'name'];
        foreach($required_fields as $field) {
            if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                $this->response_error(l('global.error_message.empty_fields'), 401);
                break 1;
            }
        }

        /* Generate the QR Code */
        $request_data = array_merge([
            'api_key' => $this->api_user->api_key,
            'type' => $_POST['type'],
        ], $settings);
        $request_files = [];
        if($_POST['qr_code_logo']) $request_files = ['qr_code_logo' => $_FILES['qr_code_logo']['tmp_name']];

        try {
            $response = Request::post(url('qr-code-generator'), [], Request\Body::multipart($request_data, $request_files));
        } catch (\Exception $exception) {
            $this->response_error($exception->getMessage(), 401);
        }

        if($response->body->status == 'error') {
            $this->response_error($response->body->message, 401);
        }

        $qr_code_logo = null;
        if($_POST['qr_code_logo']) {
            $file_name = $_FILES['qr_code_logo']['name'];
            $file_extension = explode('.', $file_name);
            $file_extension = mb_strtolower(end($file_extension));
            $file_temp = $_FILES['qr_code_logo']['tmp_name'];

            if($_FILES['qr_code_logo']['error'] == UPLOAD_ERR_INI_SIZE) {
                $this->response_error(sprintf(l('global.error_message.file_size_limit'), $qr_code_settings['qr_code_logo_size_limit']), 401);
            }

            if($_FILES['qr_code_logo']['error'] && $_FILES['qr_code_logo']['error'] != UPLOAD_ERR_INI_SIZE) {
                $this->response_error(l('global.error_message.file_upload'), 401);
            }

            if(!in_array($file_extension, Uploads::get_whitelisted_file_extensions('qr_code_logo'))) {
                $this->response_error(l('global.error_message.invalid_file_type'), 401);
            }

            if(!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {
                if(!is_writable(UPLOADS_PATH . 'qr_code_logo' . '/')) {
                    $this->response_error(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . 'qr_code_logo' . '/'), 401);
                }
            }

            if($_FILES['qr_code_logo']['size'] > $qr_code_settings['qr_code_logo_size_limit'] * 1000000) {
                $this->response_error(sprintf(l('global.error_message.file_size_limit'), $qr_code_settings['qr_code_logo_size_limit']), 401);
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Generate new name for image */
                $image_new_name = md5(time() . rand()) . '.' . $file_extension;

                /* Offload uploading */
                if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                    try {
                        $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                        /* Upload image */
                        $result = $s3->putObject([
                            'Bucket' => settings()->offload->storage_name,
                            'Key' => 'uploads/qr_code_logo/' . $image_new_name,
                            'ContentType' => mime_content_type($file_temp),
                            'SourceFile' => $file_temp,
                            'ACL' => 'public-read'
                        ]);
                    } catch (\Exception $exception) {
                        $this->response_error($exception->getMessage(), 401);
                    }
                }

                /* Local uploading */
                else {
                    /* Upload the original */
                    move_uploaded_file($file_temp, UPLOADS_PATH . 'qr_code_logo' . '/' . $image_new_name);
                }

                $qr_code_logo = $image_new_name;
            }
        }

        /* QR Code image */
        $_POST['qr_code'] = base64_decode(mb_substr($response->body->details->data, mb_strlen('data:image/svg+xml;base64,')));

        /* Generate new name for image */
        $image_new_name = md5(time() . rand()) . '.svg';

        /* Offload uploading */
        if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
            try {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                /* Upload image */
                $result = $s3->putObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/qr_code/' . $image_new_name,
                    'ContentType' => 'image/svg+xml',
                    'Body' => $_POST['qr_code'],
                    'ACL' => 'public-read'
                ]);
            } catch (\Exception $exception) {
                $this->response_error($exception->getMessage(), 401);
            }
        }

        /* Local uploading */
        else {
            /* Upload the original */
            file_put_contents(UPLOADS_PATH . 'qr_code' . '/' . $image_new_name, $_POST['qr_code']);
        }
        $qr_code = $image_new_name;

        $settings = json_encode($settings);

        /* Prepare the statement and execute query */
        $qr_code_id = db()->insert('qr_codes', [
            'user_id' => $this->api_user->user_id,
            'project_id' => $_POST['project_id'],
            'name' => $_POST['name'],
            'type' => $_POST['type'],
            'settings' => $settings,
            'qr_code' => $qr_code,
            'qr_code_logo' => $qr_code_logo,
            'datetime' => \Altum\Date::$date,
        ]);


        /* Prepare the data */
        $data = [
            'id' => $qr_code_id
        ];

        Response::jsonapi_success($data, null, 201);

    }

    private function patch() {

        $qr_code_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $qr_code = db()->where('qr_code_id', $qr_code_id)->where('user_id', $this->api_user->user_id)->getOne('qr_codes');

        /* We haven't found the resource */
        if(!$qr_code) {
            $this->return_404();
        }
        $qr_code->settings = json_decode($qr_code->settings);

        $qr_code_settings = require APP_PATH . 'includes/qr_code.php';

        /* Existing projects */
        $projects = (new \Altum\Models\Project())->get_projects_by_user_id($this->api_user->user_id);

        $_POST['name'] = trim($_POST['name'] ?? $qr_code->name);
        $_POST['project_id'] = !empty($_POST['project_id']) && array_key_exists($_POST['project_id'], $projects) ? (int) $_POST['project_id'] : $qr_code->project_id;
        $_POST['type'] = isset($_POST['type']) && array_key_exists($_POST['type'], $qr_code_settings['type']) ? $_POST['type'] : $qr_code->type;

        /* Settings & qr code */
        $settings = [];
        $settings['style'] = $_POST['style'] = isset($_POST['style']) && in_array($_POST['style'], ['square', 'dot', 'round']) ? $_POST['style'] : $qr_code->settings->style;
        $settings['foreground_type'] = $_POST['foreground_type'] = isset($_POST['foreground_type']) && in_array($_POST['foreground_type'], ['color', 'gradient']) ? $_POST['foreground_type'] : $qr_code->settings->foreground_type;
        switch($_POST['foreground_type']) {
            case 'color':
                $settings['foreground_color'] = $_POST['foreground_color'] = isset($_POST['foreground_color']) && preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['foreground_color']) ? $_POST['foreground_color'] : $qr_code->settings->foreground_color;
                break;

            case 'gradient':
                $settings['foreground_gradient_style'] = $_POST['foreground_gradient_style'] = isset($_POST['foreground_gradient_style']) && in_array($_POST['foreground_gradient_style'], ['vertical', 'horizontal', 'diagonal', 'inverse_diagonal', 'radial']) ? $_POST['foreground_gradient_style'] : $qr_code->settings->foreground_gradient_style;
                $settings['foreground_gradient_one'] = $_POST['foreground_gradient_one'] = isset($_POST['foreground_gradient_one']) && preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['foreground_gradient_one']) ? $_POST['foreground_gradient_one'] : $qr_code->settings->foreground_gradient_one;
                $settings['foreground_gradient_two'] = $_POST['foreground_gradient_two'] = isset($_POST['foreground_gradient_two']) && preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['foreground_gradient_two']) ? $_POST['foreground_gradient_two'] : $qr_code->settings->foreground_gradient_two;
                break;
        }
        $settings['background_color'] = $_POST['background_color'] = isset($_POST['background_color']) && preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['background_color']) ? $_POST['background_color'] : $qr_code->settings->background_color;
        $settings['background_color_transparency'] = $_POST['background_color_transparency'] = isset($_POST['background_color_transparency']) && in_array($_POST['background_color_transparency'], range(0, 100)) ? (int) $_POST['background_color_transparency'] : 0;
        $settings['custom_eyes_color'] = $_POST['custom_eyes_color'] = (bool) (int) ($_POST['custom_eyes_color'] ?? 0);
        if($_POST['custom_eyes_color']) {
            $settings['eyes_inner_color'] = $_POST['eyes_inner_color'] = isset($_POST['eyes_inner_color']) && preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['eyes_inner_color']) ? $_POST['eyes_inner_color'] : $qr_code->settings->eyes_inner_color;
            $settings['eyes_outer_color'] = $_POST['eyes_outer_color'] = isset($_POST['eyes_outer_color']) && preg_match('/#([A-Fa-f0-9]{3,4}){1,2}\b/i', $_POST['eyes_outer_color']) ? $_POST['eyes_outer_color'] : $qr_code->settings->eyes_outer_color;
        }

        $_POST['qr_code_logo'] = !empty($_FILES['qr_code_logo']['name']) && !isset($_POST['qr_code_logo_remove']);
        $settings['qr_code_logo_size'] = $_POST['qr_code_logo_size'] = isset($_POST['qr_code_logo_size']) && in_array($_POST['qr_code_logo_size'], range(5, 35)) ? (int) $_POST['qr_code_logo_size'] : $qr_code->settings->qr_code_logo_size;

        $settings['size'] = $_POST['size'] = isset($_POST['size']) && in_array($_POST['size'], range(50, 2000)) ? (int) $_POST['size'] : $qr_code->settings->size;
        $settings['margin'] = $_POST['margin'] = isset($_POST['margin']) && in_array($_POST['margin'], range(0, 25)) ? (int) $_POST['margin'] : $qr_code->settings->margin;
        $settings['ecc'] = $_POST['ecc'] = isset($_POST['ecc']) && in_array($_POST['ecc'], ['L', 'M', 'Q', 'H']) ? $_POST['ecc'] : $qr_code->settings->ecc;

        /* Type dependant vars */
        switch($_POST['type']) {
            case 'text':
                $settings['text'] = $_POST['text'] = mb_substr(trim(input_clean($_POST['text'] ?? $qr_code->settings->text)), 0, $qr_code_settings['type']['text']['max_length']);
                break;

            case 'url':
                $settings['url'] = $_POST['url'] = mb_substr(trim(input_clean($_POST['url'] ?? $qr_code->settings->url)), 0, $qr_code_settings['type']['url']['max_length']);
                break;

            case 'phone':
                $settings['phone'] = $_POST['phone'] = mb_substr(trim(input_clean($_POST['phone'] ?? $qr_code->settings->phone)), 0, $qr_code_settings['type']['phone']['max_length']);
                break;

            case 'sms':
                $settings['sms'] = $_POST['sms'] = mb_substr(trim(input_clean($_POST['sms'] ?? $qr_code->settings->sms)), 0, $qr_code_settings['type']['sms']['max_length']);
                $settings['sms_body'] = $_POST['sms_body'] = mb_substr(trim(input_clean($_POST['sms_body'] ?? $qr_code->settings->sms_body)), 0, $qr_code_settings['type']['sms']['body']['max_length']);
                break;

            case 'email':
                $settings['email'] = $_POST['email'] = mb_substr(trim(input_clean($_POST['email'] ?? $qr_code->settings->email)), 0, $qr_code_settings['type']['email']['max_length']);
                $settings['email_subject'] = $_POST['email_subject'] = mb_substr(trim(input_clean($_POST['email_subject'] ?? $qr_code->settings->email_subject)), 0, $qr_code_settings['type']['email']['subject']['max_length']);
                $settings['email_body'] = $_POST['email_body'] = mb_substr(trim(input_clean($_POST['email_body'] ?? $qr_code->settings->email_body)), 0, $qr_code_settings['type']['email']['body']['max_length']);
                break;

            case 'whatsapp':
                $settings['whatsapp'] = $_POST['whatsapp'] = mb_substr(trim(input_clean($_POST['whatsapp'] ?? $qr_code->settings->whatsapp)), 0, $qr_code_settings['type']['whatsapp']['max_length']);
                $settings['whatsapp_body'] = $_POST['whatsapp_body'] = mb_substr(trim(input_clean($_POST['whatsapp_body'] ?? $qr_code->settings->whatsapp_body)), 0, $qr_code_settings['type']['whatsapp']['body']['max_length']);
                break;

            case 'facetime':
                $settings['facetime'] = $_POST['facetime'] = mb_substr(trim(input_clean($_POST['facetime'] ?? $qr_code->settings->facetime)), 0, $qr_code_settings['type']['facetime']['max_length']);
                break;

            case 'location':
                $settings['location_latitude'] = $_POST['location_latitude'] = (float) mb_substr(trim(input_clean($_POST['location_latitude'] ?? $qr_code->settings->location_latitude)), 0, $qr_code_settings['type']['location']['latitude']['max_length']);
                $settings['location_longitude'] = $_POST['location_longitude'] = (float) mb_substr(trim(input_clean($_POST['location_longitude'] ?? $qr_code->settings->location_longitude)), 0, $qr_code_settings['type']['location']['longitude']['max_length']);
                break;

            case 'wifi':
                $settings['wifi_ssid'] = $_POST['wifi_ssid'] = mb_substr(trim(input_clean($_POST['wifi_ssid'] ?? $qr_code->settings->wifi_ssid)), 0, $qr_code_settings['type']['wifi']['ssid']['max_length']);
                $settings['wifi_encryption'] = $_POST['wifi_encryption'] = isset($_POST['wifi_encryption']) && in_array($_POST['wifi_encryption'], ['nopass', 'WEP', 'WPA/WPA2']) ? $_POST['wifi_encryption'] : $qr_code->settings->wifi_encryption;
                $settings['wifi_password'] = $_POST['wifi_password'] = mb_substr(trim(input_clean($_POST['wifi_password'] ?? $qr_code->settings->wifi_password)), 0, $qr_code_settings['type']['wifi']['password']['max_length']);
                $settings['wifi_is_hidden'] = $_POST['wifi_is_hidden'] = (int) $_POST['wifi_is_hidden'] ?? $qr_code->settings->wifi_is_hidden;
                break;

            case 'event':
                $settings['event'] = $_POST['event'] = mb_substr(trim(input_clean($_POST['event'] ?? $qr_code->settings->event)), 0, $qr_code_settings['type']['event']['max_length']);
                $settings['event_location'] = $_POST['event_location'] = mb_substr(trim(input_clean($_POST['event_location'] ?? $qr_code->settings->event_location)), 0, $qr_code_settings['type']['event']['location']['max_length']);
                $settings['event_url'] = $_POST['event_url'] = mb_substr(trim(input_clean($_POST['event_url'] ?? $qr_code->settings->event_url)), 0, $qr_code_settings['type']['event']['url']['max_length']);
                $settings['event_note'] = $_POST['event_note'] = mb_substr(trim(input_clean($_POST['event_note'] ?? $qr_code->settings->event_note)), 0, $qr_code_settings['type']['event']['note']['max_length']);
                $settings['event_timezone'] = $_POST['event_timezone'] = in_array($_POST['event_timezone'], \DateTimeZone::listIdentifiers()) ? input_clean($_POST['event_timezone']) : $qr_code->settings->event_timezone;
                $settings['event_start_datetime'] = $_POST['event_start_datetime'] = (new \DateTime($_POST['event_start_datetime'] ?? $qr_code->settings->event_start_datetime))->format('Y-m-d\TH:i:s');
                $settings['event_end_datetime'] = $_POST['event_end_datetime'] = (new \DateTime($_POST['event_end_datetime'] ?? $qr_code->settings->event_end_datetime))->format('Y-m-d\TH:i:s');
                break;

            case 'crypto':
                $settings['crypto_coin'] = $_POST['crypto_coin'] = isset($_POST['crypto_coin']) && array_key_exists($_POST['crypto_coin'], $qr_code_settings['type']['crypto']['coins']) ? $_POST['crypto_coin'] : $qr_code->settings->crypto_coin;
                $settings['crypto_address'] = $_POST['crypto_address'] = mb_substr(trim(input_clean($_POST['crypto_address'] ?? $qr_code->settings->crypto_address)), 0, $qr_code_settings['type']['crypto']['address']['max_length']);
                $settings['crypto_amount'] = $_POST['crypto_amount'] = isset($_POST['crypto_amount']) ? (float) $_POST['crypto_amount'] : $qr_code->settings->crypto_address;
                break;

            case 'vcard':
                $settings['vcard_first_name'] = $_POST['vcard_first_name'] = mb_substr(trim(input_clean($_POST['vcard_first_name'] ?? $qr_code->settings->vcard_first_name)), 0, $qr_code_settings['type']['vcard']['first_name']['max_length']);
                $settings['vcard_last_name'] = $_POST['vcard_last_name'] = mb_substr(trim(input_clean($_POST['vcard_last_name'] ?? $qr_code->settings->vcard_last_name)), 0, $qr_code_settings['type']['vcard']['last_name']['max_length']);
                $settings['vcard_phone'] = $_POST['vcard_phone'] = mb_substr(trim(input_clean($_POST['vcard_phone'] ?? $qr_code->settings->vcard_phone)), 0, $qr_code_settings['type']['vcard']['phone']['max_length']);
                $settings['vcard_email'] = $_POST['vcard_email'] = mb_substr(trim(input_clean($_POST['vcard_email'] ?? $qr_code->settings->vcard_email)), 0, $qr_code_settings['type']['vcard']['email']['max_length']);
                $settings['vcard_url'] = $_POST['vcard_url'] = mb_substr(trim(input_clean($_POST['vcard_url'] ?? $qr_code->settings->vcard_url)), 0, $qr_code_settings['type']['vcard']['url']['max_length']);
                $settings['vcard_company'] = $_POST['vcard_company'] = mb_substr(trim(input_clean($_POST['vcard_company'] ?? $qr_code->settings->vcard_company)), 0, $qr_code_settings['type']['vcard']['company']['max_length']);
                $settings['vcard_job_title'] = $_POST['vcard_job_title'] = mb_substr(trim(input_clean($_POST['vcard_job_title'] ?? $qr_code->settings->vcard_job_title)), 0, $qr_code_settings['type']['vcard']['job_title']['max_length']);
                $settings['vcard_birthday'] = $_POST['vcard_birthday'] = mb_substr(trim(input_clean($_POST['vcard_birthday'] ?? $qr_code->settings->vcard_birthday)), 0, $qr_code_settings['type']['vcard']['birthday']['max_length']);
                $settings['vcard_street'] = $_POST['vcard_street'] = mb_substr(trim(input_clean($_POST['vcard_street'] ?? $qr_code->settings->vcard_street)), 0, $qr_code_settings['type']['vcard']['street']['max_length']);
                $settings['vcard_city'] = $_POST['vcard_city'] = mb_substr(trim(input_clean($_POST['vcard_city'] ?? $qr_code->settings->vcard_city)), 0, $qr_code_settings['type']['vcard']['city']['max_length']);
                $settings['vcard_zip'] = $_POST['vcard_zip'] = mb_substr(trim(input_clean($_POST['vcard_zip'] ?? $qr_code->settings->vcard_zip)), 0, $qr_code_settings['type']['vcard']['zip']['max_length']);
                $settings['vcard_region'] = $_POST['vcard_region'] = mb_substr(trim(input_clean($_POST['vcard_region'] ?? $qr_code->settings->vcard_region)), 0, $qr_code_settings['type']['vcard']['region']['max_length']);
                $settings['vcard_country'] = $_POST['vcard_country'] = mb_substr(trim(input_clean($_POST['vcard_country'] ?? $qr_code->settings->vcard_country)), 0, $qr_code_settings['type']['vcard']['country']['max_length']);
                $settings['vcard_note'] = $_POST['vcard_note'] = mb_substr(trim(input_clean($_POST['vcard_note'] ?? $qr_code->settings->vcard_note)), 0, $qr_code_settings['type']['vcard']['note']['max_length']);

                if(!isset($_POST['vcard_social_label'])) {
                    $_POST['vcard_social_label'] = [];
                    $_POST['vcard_social_value'] = [];
                }

                $vcard_socials = [];
                foreach($_POST['vcard_social_label'] as $key => $value) {
                    if(empty(trim($value))) continue;
                    if($key >= 20) continue;

                    $vcard_socials[] = [
                        'label' => mb_substr(trim(input_clean($value)), 0, $qr_code_settings['type']['vcard']['social_value']['max_length']),
                        'value' => mb_substr(trim(input_clean($_POST['vcard_social_value'][$key])), 0, $qr_code_settings['type']['vcard']['social_value']['max_length'])
                    ];
                }
                $settings['vcard_socials'] = $vcard_socials;
                break;

            case 'paypal':
                $settings['paypal_type'] = $_POST['paypal_type'] = isset($_POST['paypal_type']) && array_key_exists($_POST['paypal_type'] ?? $qr_code->settings->paypal_type, $qr_code_settings['type']['paypal']['type']) ? $_POST['paypal_type'] : array_key_first($qr_code_settings['type']['paypal']['type']);
                $settings['paypal_email'] = $_POST['paypal_email'] = mb_substr(trim(input_clean($_POST['paypal_email'] ?? $qr_code->settings->paypal_email)), 0, $qr_code_settings['type']['paypal']['email']['max_length']);
                $settings['paypal_title'] = $_POST['paypal_title'] = mb_substr(trim(input_clean($_POST['paypal_title'] ?? $qr_code->settings->paypal_title)), 0, $qr_code_settings['type']['paypal']['title']['max_length']);
                $settings['paypal_currency'] = $_POST['paypal_currency'] = mb_substr(trim(input_clean($_POST['paypal_currency'] ?? $qr_code->settings->paypal_currency)), 0, $qr_code_settings['type']['paypal']['currency']['max_length']);
                $settings['paypal_price'] = $_POST['paypal_price'] = (float) $_POST['paypal_price'] ?? $qr_code->settings->paypal_price;
                $settings['paypal_thank_you_url'] = $_POST['paypal_thank_you_url'] = mb_substr(trim(input_clean($_POST['paypal_thank_you_url'] ?? $qr_code->settings->paypal_thank_you_url)), 0, $qr_code_settings['type']['paypal']['thank_you_url']['max_length']);
                $settings['paypal_cancel_url'] = $_POST['paypal_cancel_url'] = mb_substr(trim(input_clean($_POST['paypal_cancel_url'] ?? $qr_code->settings->paypal_cancel_url)), 0, $qr_code_settings['type']['paypal']['cancel_url']['max_length']);
                break;
        }

        /* Generate the QR Code */
        $request_data = array_merge([
            'api_key' => $this->api_user->api_key,
            'type' => $_POST['type'],
        ], $settings);
        $request_files = [];
        if($_POST['qr_code_logo']) $request_files = ['qr_code_logo' => $_FILES['qr_code_logo']['tmp_name']];

        try {
            $response = Request::post(url('qr-code-generator'), [], Request\Body::multipart($request_data, $request_files));
        } catch (\Exception $exception) {
            $this->response_error($exception->getMessage(), 401);
        }

        if($response->body->status == 'error') {
            $this->response_error($response->body->message, 401);
        }

        if($_POST['qr_code_logo']) {
            $file_name = $_FILES['qr_code_logo']['name'];
            $file_extension = explode('.', $file_name);
            $file_extension = mb_strtolower(end($file_extension));
            $file_temp = $_FILES['qr_code_logo']['tmp_name'];

            if($_FILES['qr_code_logo']['error'] == UPLOAD_ERR_INI_SIZE) {
                $this->response_error(sprintf(l('global.error_message.file_size_limit'), $qr_code_settings['qr_code_logo_size_limit']), 401);
            }

            if($_FILES['qr_code_logo']['error'] && $_FILES['qr_code_logo']['error'] != UPLOAD_ERR_INI_SIZE) {
                $this->response_error(l('global.error_message.file_upload'), 401);
            }

            if(!in_array($file_extension, Uploads::get_whitelisted_file_extensions('qr_code_logo'))) {
                $this->response_error(l('global.error_message.invalid_file_type'), 401);
            }

            if(!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {
                if(!is_writable(UPLOADS_PATH . 'qr_code_logo' . '/')) {
                    $this->response_error(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . 'qr_code_logo' . '/'), 401);
                }
            }

            if($_FILES['qr_code_logo']['size'] > $qr_code_settings['qr_code_logo_size_limit'] * 1000000) {
                $this->response_error(sprintf(l('global.error_message.file_size_limit'), $qr_code_settings['qr_code_logo_size_limit']), 401);
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
                        $this->response_error($exception->getMessage(), 401);
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

        /* QR Code image */
        $_POST['qr_code'] = base64_decode(mb_substr($response->body->details->data, mb_strlen('data:image/svg+xml;base64,')));

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
                $this->response_error($exception->getMessage(), 401);
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

        /* Prepare the data */
        $data = [
            'id' => $qr_code->qr_code_id
        ];

        Response::jsonapi_success($data, null, 200);

    }

    private function delete() {

        $qr_code_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        /* Try to get details about the resource id */
        $qr_code = db()->where('qr_code_id', $qr_code_id)->where('user_id', $this->api_user->user_id)->getOne('qr_codes');

        /* We haven't found the resource */
        if(!$qr_code) {
            $this->return_404();
        }

        (new QrCode())->delete($qr_code->qr_code_id);

        http_response_code(200);
        die();

    }

}
