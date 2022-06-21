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
class PocketToWordpress
{
    /**
     * This plugin's version number. Used for busting caches.
     *
     * @var string
     */
    public $version = '0.0.1';

    /**
     * This plugin's prefix
     *
     * @var string
     */
    private $prefix = 'ptw_';

    /**
     * The capability required to access this plugin's settings.
     *
     * @var string
     */
    public $capability_settings = 'manage_options';

    public function __construct()
    {
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);

        add_action('admin_menu', [$this, 'register_settings_page']);
        add_action('admin_init', [$this, 'admin_init']);
    }

    public function admin_init()
    {
        ob_start();
    }

    public function register_settings_page()
    {
        add_options_page(
            'Pocket To WordPress Settings Page',
            'Pocket To WordPress',
            $this->capability_settings,
            'pocket-to-wordpress',
            [$this, 'display_ptw_setting_page_callback']
        );
    }

    public function display_ptw_setting_page_callback()
    {
        if (! current_user_can($this->capability_settings)) {
            return;
        }

        $current_user = wp_get_current_user();
        $pwt_url = 'https://jeandaviddaviet.fr/pocket';

        $redirect_uri = urlencode(plugin_dir_url(__FILE__) . 'callback.php');

        $access_token = get_user_meta($current_user->ID, $this->prefix . 'access', true);

        if ($_GET['reset'] === 'true') {
            delete_user_meta($current_user->ID, $this->prefix . 'code');
        }

        if (empty($access_token)) {

            $pwt_code = get_user_meta($current_user->ID, $this->prefix . 'code', true);

            if (empty($pwt_code)) {

                $request = wp_remote_get($pwt_url . '?path=request&redirect_uri=' . $redirect_uri);
                $response = wp_remote_retrieve_body($request);

                $code = explode('=', $response);
                update_user_meta($current_user->ID, $this->prefix . 'code', $code[1]);

            } else {
                wp_redirect('https://getpocket.com/auth/authorize?request_token=' . $pwt_code . '&redirect_uri=' . $redirect_uri);
                die;
            }
        }

        if (! empty($access_token)) {

            $request = wp_remote_get($pwt_url . '?path=get&access_token=' . $access_token);
            $response = wp_remote_retrieve_body($request);
            $list = (array) json_decode($response);
            update_user_meta($current_user->ID, $this->prefix . 'list', $list);
        }

        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <?php

            if ($list) {
                var_dump($list);
            }

            ?>
        </div>
        <?php
    }

    public function activate()
    {
    }

    public function deactivate()
    {
    }
}

new PocketToWordpress();