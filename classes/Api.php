<?php

namespace JDD\CPTW;

defined( 'ABSPATH' ) || die();

class Api {

    use CPTWTrait;

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

    /**
     * This user username
     *
     * @var string
     */
    private $username;

    /**
     * If there is authentification error
     *
     * @var array|null
     */
    private $auth_error;

    public function __construct() {
        $this->admin_url = admin_url('options-general.php?page=' . $this->slug);
        $this->redirect_uri = urlencode( $this->admin_url . '&callback=pocket' );
        $this->consumer_key = get_option( $this->prefix . 'consumer_key' );
        $this->request_code = get_option( $this->prefix . 'request_code' );
        $this->access_token = get_option( $this->prefix . 'access_token' );
        $this->username     = get_option( $this->prefix . 'username' );
        $this->auth_error   = get_option( $this->prefix . 'auth_error' );
    }

    /**
     * @return string
     */
    public function get_request_code() {
        return $this->request_code;
    }

    /**
     * @param string|null $request_code
     */
    public function set_request_code( $request_code ): void {
        $this->request_code = $request_code;
        if($this->request_code === null){
            delete_option($this->prefix . 'request_code');
        }else{
            update_option($this->prefix . 'request_code', $this->request_code);
        }
    }

    /**
     * @return string
     */
    public function get_access_token() {
        return $this->access_token;
    }

    /**
     * @param string|null $access_token
     */
    public function set_access_token( $access_token ): void {
        $this->access_token = $access_token;
        if ( $this->access_token === null ) {
            delete_option( $this->prefix . 'access_token' );
        } else {
            update_option( $this->prefix . 'access_token', $this->access_token );
        }
    }

    /**
     * @return string
     */
    public function get_consumer_key() {
        return $this->consumer_key;
    }

    /**
     * @return string
     */
    public function get_username() {
        return $this->username;
    }

    /**
     * @param string|null $username
     */
    public function set_username( $username ): void {
        $this->username = $username;
        if ( $this->username === null ) {
            delete_option( $this->prefix . 'username' );
        } else {
            update_option( $this->prefix . 'username', $this->username );
        }
    }

    /**
     * @return array
     */
    public function get_auth_error() {
        return $this->auth_error;
    }

    /**
     * @param array|null $auth_error
     */
    public function set_auth_error( $auth_error ): void {
        $this->auth_error = $auth_error;
        if($this->auth_error === null){
            delete_option($this->prefix . 'auth_error');
        }else{
            update_option($this->prefix . 'auth_error', $this->auth_error);
        }
    }

    public function pocket( $path, $params ) {
        $params = array_merge( [
            'consumer_key' => $this->consumer_key,
        ], $params );

        return wp_remote_post( $this->api_url . $path, [
            'headers' => [
                'Content-Type' => 'application/json; charset=UTF-8',
                'X-Accept'     => 'application/json',
            ],
            'body'    => json_encode( $params ),
        ] );
    }

    public function request_code(): void {
        if ( empty( $this->access_token ) && empty( $this->request_code ) ) {

            $response = $this->pocket( 'oauth/request', [
                'redirect_uri' => $this->redirect_uri,
            ] );

            $body = json_decode( wp_remote_retrieve_body( $response ) );

            if($body && isset($body->code)){
                $this->request_code = $body->code;
                update_option( $this->prefix . 'request_code', $this->request_code );
            }

        }
    }

    public function authorize(): void {
        if ( empty( $this->access_token ) && ! empty( $this->request_code ) ) {

            wp_redirect( 'https://getpocket.com/auth/authorize?request_token=' . $this->request_code . '&redirect_uri=' . $this->redirect_uri );
            exit;

        }
    }

    public function get_list( $access_token = '', $options = [] ) {
        if ( empty( $access_token ) ) {
            $access_token = $this->access_token;
        }
        if ( ! empty( $access_token ) ) {
            $response = $this->pocket( 'get', array_merge( [
                'access_token' => $access_token,
            ], array_filter($options) ) );

            return json_decode( wp_remote_retrieve_body( $response ) );
        }

        return null;
    }
}