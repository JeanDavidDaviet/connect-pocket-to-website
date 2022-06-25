<?php
/*
Plugin Name: Pocket To WordPress
Plugin URI: https://wordpress.org/plugins/pocket-to-wordpress/
Description: This application allow you to display your Pocket list into your WordPress site
Author: Jean-David Daviet
Version: 0.0.1
Author URI: https://jeandaviddaviet.fr
Text Domain: pocket-to-wordpress
*/

namespace JDD;

defined('ABSPATH') || die();

/**
 * Main plugin class.
 *
 * @since 1.0.0
 */
class Api
{
    /**
     * This plugin's prefix
     *
     * @var string
     */
    private $prefix = 'ptw_';

    /**
     * This plugin server's url
     *
     * @var string
     */
    private $api_url = 'https://getpocket.com/v3/';

    /**
     * This plugin redirect_uri url
     *
     * @var string
     */
    private $redirect_uri;

    /**
     * This user request code
     *
     * @var string
     */
    private $request_code;

    /**
     * This user access_token
     *
     * @var string
     */
    private $access_token;

    public function __construct()
    {
        $this->redirect_uri = urlencode(plugin_dir_url(dirname(__FILE__)) . 'callback.php');
        $this->consumer_key = get_option($this->prefix . 'consumer_key');
        $this->request_code = get_option($this->prefix . 'request_code');
        $this->access_token = get_option($this->prefix . 'access_token');
    }

    /**
     * @return string
     */
    public function get_request_code() {
        return $this->request_code;
    }

    /**
     * @return string
     */
    public function get_access_token() {
        return $this->access_token;
    }

    public function pocket($path, $params)
    {
        $params = array_merge([
            'consumer_key' => $this->consumer_key
        ], $params);

        $request = wp_remote_post( $this->api_url . $path, [
            'headers' => [
                'Content-Type' => 'application/json; charset=UTF-8',
                'X-Accept' => 'application/json'
            ],
            'body' => json_encode($params)
        ]);
        return json_decode(wp_remote_retrieve_body($request));
    }

    public function request_code(): void
    {
        if (empty($this->access_token) && empty($this->request_code)) {

            $response = $this->pocket('oauth/request', [
                'redirect_uri' => $this->redirect_uri
            ]);

            $this->request_code = $response->code;
            update_option($this->prefix . 'request_code', $this->request_code);

        }
    }

    public function authorize(): void
    {
        if (empty($this->access_token) && !empty($this->request_code)) {

            wp_redirect('https://getpocket.com/auth/authorize?request_token=' . $this->request_code . '&redirect_uri=' . $this->redirect_uri);
            die;

        }
    }

    public function get_list($access_token = '', $options = [])
    {
        if (empty($access_token)){
            $access_token = $this->access_token;
        }
        if (!empty($access_token)) {
	        return $this->pocket('get', array_merge([
                'access_token' => $access_token
            ], $options));
        }
        return null;
    }
}