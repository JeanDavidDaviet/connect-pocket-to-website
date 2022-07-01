<?php

namespace JDD;

require dirname(dirname(dirname(dirname((__FILE__))))) . '/wp-load.php';

defined('ABSPATH') || die();

require_once dirname( __FILE__ ) . '/classes/Api.php';

/**
 * Main plugin class.
 *
 * @since 1.0.0
 */
class Callback
{
    /**
     * The Api instance
     *
     * @var \JDD\Api
     */
    private $api;

    public function __construct()
    {
        $this->api = new Api();
        $this->handle_response();
    }

    private function handle_response()
    {
        $response = $this->api->pocket('oauth/authorize', [
            'code' => $this->api->get_request_code()
        ]);
        $headers = wp_remote_retrieve_headers($response);
        $status = (int) substr($headers['status'], 0, 3);

        if($status !== 200){
            $this->handle_error($status);
        }else{
            $this->handle_success($response);
        }

        wp_redirect(admin_url('options-general.php?page=pocket-to-wordpress'));
        exit;
    }

    private function handle_success($response)
    {
        $body = json_decode(wp_remote_retrieve_body($response));
        $this->api->set_access_token($body->access_token);
        $this->api->set_username($body->username);
    }

    private function handle_error($status)
    {
        $error_message = __('Something wrong happened', 'pocket-to-wordpress');
        // user denied access
        if($status === 400){
            $error_message = __('Invalid request, please make sure you follow the documentation for proper syntax', 'pocket-to-wordpress');
        }
        if($status === 401){
            $error_message = __('Problem authenticating the user', 'pocket-to-wordpress');
        }
        if($status === 403){
            $error_message = __('User was authenticated, but access denied due to lack of permission or rate limiting', 'pocket-to-wordpress');
        }
        if($status === 503){
            $error_message = __('Pocket\'s sync server is down for scheduled maintenance.', 'pocket-to-wordpress');
        }
        $this->api->set_auth_error([
            'status' => $status,
            'message' => $error_message,
        ]);
    }
}

new Callback();