<?php
/**
 * Plugin Name: Mailexam Mail Test
 * Description: REST endpoint POST /wp-json/mailexam/v1/mail/test
 */

add_action('rest_api_init', static function (): void {
    register_rest_route('mailexam/v1', '/mail/test', [
        'methods' => 'POST',
        'callback' => static function (WP_REST_Request $request): WP_REST_Response {
            $payload = $request->get_json_params() ?: [];

            $to = $payload['to'] ?? 'user@example.test';
            $subject = $payload['subject'] ?? 'WordPress + Mailexam';
            $body = $payload['body'] ?? $payload['text'] ?? 'Mailexam test from WordPress';

            $sent = wp_mail($to, $subject, $body);

            if (!$sent) {
                return new WP_REST_Response(['error' => 'send failed'], 500);
            }

            return new WP_REST_Response(['status' => 'ok'], 200);
        },
        'permission_callback' => '__return_true',
    ]);
});
