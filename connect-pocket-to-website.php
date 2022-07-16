<?php
/*
Plugin Name: Connect Pocket To Website
Plugin URI: https://wordpress.org/plugins/connect-pocket-to-website/
Description: This plugin allows you to display your Pocket list into your WordPress site
Author: Jean-David Daviet
Version: 1.0.0
Author URI: https://jeandaviddaviet.fr
Text Domain: connect-pocket-to-website
*/

namespace JDD\CPTW;

defined('ABSPATH') || die();

require_once dirname( __FILE__ ) . '/classes/Settings.php';
require_once dirname( __FILE__ ) . '/classes/Api.php';

class ConnectPocketToWordpress
{
    /**
     * This plugin's version number. Used for busting caches.
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * This plugin's prefix
     *
     * @var string
     */
    private $prefix = 'cptw_';

    /**
     * The Api instance
     *
     * @var \JDD\CPTW\Api
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

        add_shortcode('connect-pocket-to-website', [$this, 'cptw_shortcode']);
    }

    public function register_settings_page()
    {
        add_options_page(
            'Connect Pocket To Website Settings Page',
            'Connect Pocket To Website',
            $this->capability_settings,
            'connect-pocket-to-website',
            [$this, 'display_cptw_setting_page_callback']
        );
    }

    public function display_cptw_setting_page_callback()
    {
        if (! current_user_can($this->capability_settings)) {
            return;
        }

        if (((isset($_GET['logout']) && $_GET['logout'] === 'true') || !empty($this->api->get_auth_error())) || isset($_GET['reset'])) {
            $this->api->set_request_code(null);
            $this->api->set_access_token(null);
            $this->api->set_auth_error(null);
            // todo add notification of failure
            wp_redirect(admin_url('options-general.php?page=connect-pocket-to-website'));
            exit;
        }

        if(isset($_GET['login'])){
            $this->api->request_code();
            $this->api->authorize();
        }

        $list = (array) $this->api->get_list();
        update_option($this->prefix . 'list', $list);

        //Get the active tab from the $_GET param
        $default_tab = null;
        $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : $default_tab;

        $tab_url = admin_url('options-general.php?page=connect-pocket-to-website');

        ?>
        <div class="wrap">

            <h1><?php _e('Connect Pocket To Website Settings', 'connect-pocket-to-website'); ?></h1>

            <nav class="nav-tab-wrapper">
                <a href="<?php echo esc_attr($tab_url); ?>" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>"><?php _e('Connection', 'connect-pocket-to-website'); ?></a>
                <a href="<?php echo esc_attr($tab_url); ?>&tab=howto" class="nav-tab <?php if($tab==='howto'):?>nav-tab-active<?php endif; ?>"><?php _e('How-to', 'connect-pocket-to-website'); ?></a>
                <a href="<?php echo esc_attr($tab_url); ?>&tab=display" class="nav-tab <?php if($tab==='display'):?>nav-tab-active<?php endif; ?>"><?php _e('Display', 'connect-pocket-to-website'); ?></a>
            </nav>

            <div class="tab-content">
                <?php if($tab === null): ?>
                    <form action="<?php echo admin_url('options.php'); ?>" method="post">
                        <?php
                        do_settings_sections('connect-pocket-to-website');
                        settings_fields('cptw_section1');
                        if(!empty($this->api->get_consumer_key())):
                            ?>
                            <table class="form-table" role="presentation">
                                <tr>
                                    <th scope="row"><?php _e('Request Code', 'connect-pocket-to-website'); ?></th>
                                    <td><p><?php echo esc_html($this->api->get_request_code()); ?></p></td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php _e('Access Token', 'connect-pocket-to-website'); ?></th>
                                    <td><p><?php echo esc_html($this->api->get_access_token()); ?></p></td>
                                </tr>
                            </table>
                        <?php
                        endif;
                        submit_button(__('Save Settings', 'connect-pocket-to-website'));
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
                                <input type="submit" class="pocket-btn" value="<?php echo esc_attr(__('Login with Pocket', 'connect-pocket-to-website')); ?>">
                                <input type="hidden" name="login" value="true">
                                <input type="hidden" name="page" value="connect-pocket-to-website">
                            </form>
                        <?php
                        endif;

                        if(!empty($this->api->get_access_token())): ?>
                            <form>
                                <input type="submit" class="pocket-btn" value="<?php echo esc_attr(__('Disconnect from Pocket', 'connect-pocket-to-website')); ?>">
                                <input type="hidden" name="logout" value="true">
                                <input type="hidden" name="page" value="connect-pocket-to-website">
                            </form>
                        <?php
                        endif;
                    endif;
                    ?>
                <?php endif; ?>
                <?php if($tab === 'howto'): require_once plugin_dir_path(__FILE__) . 'content/howto.php'; endif; ?>
                <?php if($tab === 'display'): require_once plugin_dir_path(__FILE__) . 'content/shortcode.php'; endif; ?>
            </div>
        </div>
        <?php
    }

    public function cptw_shortcode($attributes)
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

new ConnectPocketToWordpress();