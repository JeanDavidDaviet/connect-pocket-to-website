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
     * @var \JDD\Api
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

        add_shortcode('pocket-to-wordpress', [$this, 'pwt_shortcode']);
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

        if (((isset($_GET['logout']) && $_GET['logout'] === 'true') || !empty($this->api->get_auth_error())) || isset($_GET['reset'])) {
            $this->api->set_request_code(null);
            $this->api->set_access_token(null);
            $this->api->set_auth_error(null);
            // todo add notification of failure
            wp_redirect(admin_url('options-general.php?page=pocket-to-wordpress'));
            exit;
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
                if(!empty($this->api->get_consumer_key())):
                ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><?php _e('Request Code', 'pocket-to-wordpress'); ?></th>
                        <td><p><?php echo esc_html($this->api->get_request_code()); ?></p></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Access Token', 'pocket-to-wordpress'); ?></th>
                        <td><p><?php echo esc_html($this->api->get_access_token()); ?></p></td>
                    </tr>
                </table>
                <?php
                endif;
                submit_button(__('Save Settings', 'pocket-to-wordpress'));
                ?>
            </form>

            <?php if(!empty($this->api->get_consumer_key())): ?>

                <style>.pocket-btn{
                        padding: 12px 12px 12px 45px;
                        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='-16.04235 -23.82925 139.0337 142.9755'%3E%3Cpath d='M84.058 39.778L58.54 63.794c-1.313 1.501-3.377 2.065-4.878 2.065-1.876 0-3.752-.564-5.253-2.065L23.266 39.778c-2.627-2.814-3.002-7.505 0-10.507 2.814-2.627 7.505-3.002 10.32 0l20.076 19.325 20.452-19.325c2.627-3.002 7.317-2.627 9.944 0 2.627 3.002 2.627 7.693 0 10.507M97.005 0H10.32C4.691 0 0 4.316 0 9.945v32.084c0 29.083 24.016 53.288 53.662 53.288 29.458 0 53.287-24.205 53.287-53.288V9.945c0-5.629-4.503-9.945-9.944-9.945' fill='%23EF4056'/%3E%3C/svg%3E");
                        background-repeat: no-repeat;
                        background-position: 10px center;
                        background-color: white;
                        background-size: 30px;
                        border: 1px solid #cccccc;
                        border-radius: 3px;
                        cursor: pointer;
                        transition: color .15s;
                    }.pocket-btn:hover, .pocket-btn:focus {
                        color: #ee4055;
                     }</style>

                <?php if(empty($this->api->get_access_token())): ?>
                    <form>
                        <input type="submit" class="pocket-btn" value="<?php echo esc_attr(__('Login with Pocket', 'pocket-to-wordpress')); ?>">
                        <input type="hidden" name="login" value="true">
                        <input type="hidden" name="page" value="pocket-to-wordpress">
                    </form>
                <?php
                endif;

                if(!empty($this->api->get_access_token())): ?>
                <form>
                    <input type="submit" class="pocket-btn" value="<?php echo esc_attr(__('Disconnect from Pocket', 'pocket-to-wordpress')); ?>">
                    <input type="hidden" name="logout" value="true">
                    <input type="hidden" name="page" value="pocket-to-wordpress">
                </form>
                <?php
                endif;
            endif;
            ?>
        </div>
        <?php
    }

    public function pwt_shortcode($attributes)
    {
        $access_token = get_option($this->prefix . 'access_token');
        $list = $this->api->get_list($access_token, (array) $attributes);
        $list = (array) $list->list;

        if (!empty($list)){
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