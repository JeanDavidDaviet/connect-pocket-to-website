<?php

namespace JDD\CPTW;

use function \wp_redirect;

class Callback
{

    use CPTWTrait;

    /**
     * The Api instance
     *
     * @var \JDD\CPTW\Api
     */
    private $api;

    public function __construct()
    {
        $this->admin_url = admin_url('options-general.php?page=' . $this->slug);
        $this->api = new Api();
        $this->handle_callback_get();
    }

    public function handle_callback_get()
    {
        if(isset($_GET['callback']) && sanitize_text_field($_GET['callback']) === 'pocket'){
            $this->handle_response();
        }
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
        wp_redirect($this->admin_url);
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
        $error_message = __('Something wrong happened', $this->slug);
        // user denied access
        if($status === 400){
            $error_message = __('Invalid request, please make sure you follow the documentation for proper syntax', $this->slug);
        }
        if($status === 401){
            $error_message = __('Problem authenticating the user', $this->slug);
        }
        if($status === 403){
            $error_message = __('User was authenticated, but access denied due to lack of permission or rate limiting', $this->slug);
        }
        if($status === 503){
            $error_message = __('Pocket\'s sync server is down for scheduled maintenance.', $this->slug);
        }
        $this->api->set_auth_error([
            'status' => $status,
            'message' => esc_html($error_message),
        ]);
    }
}