<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

function get_email_template($email_template_subject_array, $email_template_subject, $email_template_body_array, $email_template_body) {

    $email_template_subject = str_replace(
        array_keys($email_template_subject_array),
        array_values($email_template_subject_array),
        $email_template_subject
    );

    $email_template_body = str_replace(
        array_keys($email_template_body_array),
        array_values($email_template_body_array),
        $email_template_body
    );

    return (object) [
        'subject' => $email_template_subject,
        'body' => $email_template_body
    ];
}

function send_server_mail($to, $from, $title, $content, $reply_to = null) {

    $headers = "From: " . strip_tags($from) . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "Reply-To: " . ($reply_to ?? $from) . "\r\n";

    /* Sent to multiple addresses if $to variable is array of emails */
    if(is_array($to)) {
        $to = implode(',', $to);
    }

    return mail($to, $title, $content, $headers);
}

function send_mail($to, $title, $content, $data = [], $reply_to = null, $debug = false) {

    /* Templating for the title */
    $replacers = [
        '{{WEBSITE_TITLE}}' => settings()->main->title,
    ];

    $title = str_replace(
        array_keys($replacers),
        array_values($replacers),
        $title
    );

    /* Prepare the content */
    $replacers = [
        '{{WEBSITE_TITLE}}' => settings()->main->title,
    ];

    $content = str_replace(
        array_keys($replacers),
        array_values($replacers),
        $content
    );

    /* Get the email template */
    $email_template = include_view(THEME_PATH . 'views/partials/email_wrapper.php', [
        'anti_phishing_code' => $data['anti_phishing_code'] ?? null,
        'language' => $data['language'] ?? settings()->main->default_language,
        'content' => $content,
    ]);

    if(!empty(settings()->smtp->host)) {
        try {
            /* Initiate phpMailer */
            $mail = new \PHPMailer\PHPMailer\PHPMailer();
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->isHTML(true);

            /* Set the debugging for phpMailer */
            $mail->SMTPDebug = $debug ? 2 : 0;

            /* SMTP settings */
            if(settings()->smtp->encryption != '0') {
                $mail->SMTPSecure = settings()->smtp->encryption;
            }
            $mail->SMTPAuth = settings()->smtp->auth;
            $mail->Host = settings()->smtp->host;
            $mail->Port = settings()->smtp->port;
            $mail->Username = settings()->smtp->username;
            $mail->Password = settings()->smtp->password;

            /* Email sent from */
            $mail->setFrom(settings()->smtp->from, settings()->smtp->from_name);

            /* Reply to */
            if($reply_to) {
                $mail->addReplyTo($reply_to);
            } else {
                $mail->addReplyTo(settings()->smtp->from, settings()->smtp->from_name);
            }

            /* Sent to multiple addresses if $to variable is array of emails */
            if(is_array($to)) {
                foreach($to as $address) {
                    $mail->addAddress($address);
                }
            } else {
                $mail->addAddress($to);
            }

            /* Email title & content */
            $mail->Subject = $title;
            $mail->Body = $email_template;
            $mail->AltBody = strip_tags($email_template);

            /* Save errors in array for debugging */
            $errors = [];

            if($debug) {
                $mail->Debugoutput = function($string, $level) use(&$errors) {
                    $errors[] = $string;
                };
            }

            /* Send the mail */
            $send = $mail->send();

            /* Save the errors in the returned object for output purposes */
            if($debug) {
                $mail->errors = $errors;
            }

            return $debug ? $mail : $send;
        } catch (Exception $e) {
            return $debug ? $mail : false;
        }

    } else {
        return send_server_mail($to, settings()->smtp->from, $title, $email_template, $reply_to);
    }

}

