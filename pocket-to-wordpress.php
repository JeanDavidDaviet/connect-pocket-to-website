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

require_once dirname( __FILE__ ) . '/classes/Settings.php';
require_once dirname( __FILE__ ) . '/classes/Api.php';

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
     * The Api instance
     *
     * @var string
     */
    private $api;

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

        $this->api = new Api();
        new Settings();

        add_action('admin_menu', [$this, 'register_settings_page']);
        add_action('admin_init', [$this, 'admin_init']);

        add_shortcode('pocket-to-wordpress', [$this, 'pwt_shortcode']);
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

        $auth_error = get_option('ptw_auth_error');
        if (($_GET['logout'] === 'true' || !empty($auth_error)) || isset($_GET['reset'])) {
            delete_option($this->prefix . 'request_code');
            delete_option($this->prefix . 'access_token');
            delete_option($this->prefix . 'auth_error');
            // todo add notification of failure
            wp_redirect(admin_url('options-general.php?page=pocket-to-wordpress'));
            die;
        }

        if(isset($_GET['login'])){
	        $this->api->request_code();
            $this->api->authorize();
        }

        $list = (array) $this->api->get_list();
        update_option($this->prefix . 'list', $list);

        ?>
        <div class="wrap">

            <form action="<?php echo admin_url('options.php'); ?>" method="post">
                <?php
                do_settings_sections('pocket-to-wordpress');
                settings_fields('ptw_section1');
                ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">Request Code</th>
                        <td><p><?php echo esc_html($this->api->get_request_code()); ?></p></td>
                    </tr>
                    <tr>
                        <th scope="row">Access Token</th>
                        <td><p><?php echo esc_html($this->api->get_access_token()); ?></p></td>
                    </tr>
                </table>
                <?php
                submit_button('Save Settings');
                ?>
            </form>

            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <?php if(empty($this->api->get_access_token())): ?>
                <form>
                    <input type="submit" value="Login with Pocket">
                    <input type="hidden" name="login" value="true">
                    <input type="hidden" name="page" value="pocket-to-wordpress">
                </form>
            <?php
            endif;

            if(!empty($this->api->get_access_token())): ?>
            <form>
                <input type="submit" value="Disconnect from Pocket">
                <input type="hidden" name="logout" value="true">
                <input type="hidden" name="page" value="pocket-to-wordpress">
            </form>
            <?php
            endif;

            if (is_array($list) && isset($list['list'])) {
                echo '<h2>Reading List</h2>';
                echo $this->display_pocket_list_items((array) $list['list']);
            }

            ?>
        </div>
        <?php
    }

    public function pwt_shortcode($attributes)
    {
        $access_token = get_option($this->prefix . 'access_token');
        $list = $this->fetch_pocket($access_token, $attributes);

        if (isset($list['list'])) {
            $list = $list['list'];
            return $this->display_pocket_list_items($list);
        }

        return '';
    }

    public function display_pocket_list_items(array $list)
    {
        if (!empty($list)) {
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
            return ob_get_clean();
        }

        return '';
    }

    public function activate()
    {
    }

    public function deactivate()
    {
    }
}

new PocketToWordpress();