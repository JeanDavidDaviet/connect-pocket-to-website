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
     * This plugin server's url
     *
     * @var string
     */
    private $pwt_url = 'https://jeandaviddaviet.fr/pocket';

    /**
     * This plugin redirect_uri url
     *
     * @var string
     */
    private $redirect_uri;

    /**
     * This user access_token
     *
     * @var string
     */
    private $access_token;

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

        add_shortcode('pocket-to-wordpress', [$this, 'pwt_shortcode']);

        $this->redirect_uri = urlencode(plugin_dir_url(__FILE__) . 'callback.php');
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

        $this->access_token = get_option($this->prefix . 'access', true);

        if ($_GET['reset'] === 'true') {
            delete_option($this->prefix . 'code');
        }

        $this->request_pocket();

        $list = $this->fetch_pocket();

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

    private function request_pocket(): void
    {
        if (empty($this->access_token)) {

            $pwt_code = get_option($this->prefix . 'code', true);

            if (empty($pwt_code)) {

                $request = wp_remote_get($this->pwt_url . '?path=request&redirect_uri=' . $this->redirect_uri);
                $response = wp_remote_retrieve_body($request);

                $code = explode('=', $response);
                update_option($this->prefix . 'code', $code[1]);

            } else {

                wp_redirect('https://getpocket.com/auth/authorize?request_token=' . $pwt_code . '&redirect_uri=' . $this->redirect_uri);
                die;

            }
        }
    }

    public function fetch_pocket()
    {
        if (! empty($this->access_token)) {
            $request = wp_remote_get($this->pwt_url . '?path=get&access_token=' . $this->access_token);
            $response = wp_remote_retrieve_body($request);
            $list = (array) json_decode($response);
            update_option($this->prefix . 'list', $list);
            return $list;
        }
        return [];
    }

    public function pwt_shortcode()
    {
        $html = '';
        $list = get_option($this->prefix . 'list');
        if (isset($list['list'])) {
            $list = $list['list'];
            ob_start();
            ?>
            <ul>
                <?php
                foreach ($list as $item) {
                    ?>
                    <li>
                        <a href="<?php echo $item->resolved_url; ?>"><?php echo $item->resolved_title; ?></a>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <?php
            $html = ob_get_clean();
        }

        return $html;
    }

    public function activate()
    {
    }

    public function deactivate()
    {
    }
}

new PocketToWordpress();