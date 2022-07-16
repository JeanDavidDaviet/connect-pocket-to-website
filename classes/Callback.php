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
        add_action('admin_menu', [$this, 'register_callback_page']);
        $this->admin_url = 'options-general.php?page=' . $this->slug;
        $this->api = new Api();
    }

    public function register_callback_page()
    {
        add_options_page(
            'Connect Pocket To Website Callback Page',
            'Connect Pocket To Website2',
            $this->capability_settings,
            $this->slug . '2',
            [$this, 'display_cptw_setting_page_callback2']
        );
    }

    public function display_cptw_setting_page_callback2()
    {
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
        var_dump($status);
        die;
        header( "Location: " . admin_url($this->admin_url), true, 302 );
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