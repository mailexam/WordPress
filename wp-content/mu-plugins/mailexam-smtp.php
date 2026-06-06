<?php
/**
 * Plugin Name: Mailexam SMTP
 * Description: Send WordPress mail via Mailexam SMTP
 */

add_action('phpmailer_init', static function ($phpmailer): void {
    if (!defined('MAILEXAM_LOGIN') || !defined('MAILEXAM_PASSWORD')) {
        return;
    }

    $login = MAILEXAM_LOGIN;
    $port = defined('MAILEXAM_PORT') ? (int) MAILEXAM_PORT : 587;

    $phpmailer->isSMTP();
    $phpmailer->Host = $login . '.mailexam.io';
    $phpmailer->Port = $port;
    $phpmailer->SMTPAuth = true;
    $phpmailer->Username = $login;
    $phpmailer->Password = MAILEXAM_PASSWORD;

    if ($port === 465) {
        $phpmailer->SMTPSecure = 'ssl';
    } elseif (in_array($port, [587, 2525], true)) {
        $phpmailer->SMTPSecure = 'tls';
    } else {
        $phpmailer->SMTPSecure = '';
    }
});

add_filter('wp_mail_from', static function (): string {
    return defined('MAIL_FROM') ? MAIL_FROM : 'noreply@example.test';
});
