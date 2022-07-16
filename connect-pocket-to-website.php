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
     * This plugin's unique slug
     *
     * @var string
     */
    public $slug = 'connect-pocket-to-website';

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
     * The default admin page url.
     *
     * @var string
     */
    public $admin_url = '';

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

        $this->admin_url = 'options-general.php?page=' . $this->slug;
        $this->api = new Api();
        new Settings();

        add_action('admin_menu', [$this, 'register_settings_page']);
        add_action( 'admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);

        add_shortcode('connect-pocket-to-website', [$this, 'cptw_shortcode']);
    }

    public function admin_enqueue_scripts()
    {
        wp_enqueue_style( $this->prefix . 'admin_css', plugin_dir_url(__FILE__) . 'assets/' . $this->slug . '.css', [], $this->version );
        wp_enqueue_script( $this->prefix . 'admin_js', plugin_dir_url(__FILE__) . 'assets/' . $this->slug . '.js', [], $this->version, true );
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
            wp_redirect(admin_url($this->admin_url));
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

        $tab_url = admin_url($this->admin_url);

        ?>
        <div class="wrap">

            <h1><?php esc_html_e('Connect Pocket To Website Settings', 'connect-pocket-to-website'); ?></h1>

            <nav class="nav-tab-wrapper">
                <a href="<?php echo esc_attr($tab_url); ?>" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>"><?php esc_html_e('Connection', 'connect-pocket-to-website'); ?></a>
                <a href="<?php echo esc_attr($tab_url); ?>&tab=howto" class="nav-tab <?php if($tab==='howto'):?>nav-tab-active<?php endif; ?>"><?php esc_html_e('How-to', 'connect-pocket-to-website'); ?></a>
                <a href="<?php echo esc_attr($tab_url); ?>&tab=display" class="nav-tab <?php if($tab==='display'):?>nav-tab-active<?php endif; ?>"><?php esc_html_e('Display', 'connect-pocket-to-website'); ?></a>
            </nav>

            <div class="tab-content">
                <?php if($tab === null): ?>
                    <form action="<?php echo esc_attr(admin_url('options.php')); ?>" method="post">
                        <?php
                        do_settings_sections('connect-pocket-to-website');
                        settings_fields('cptw_section1');
                        if(!empty($this->api->get_consumer_key())):
                            ?>
                            <table class="form-table" role="presentation">
                                <tr>
                                    <th scope="row"><?php esc_html_e('Request Code', 'connect-pocket-to-website'); ?></th>
                                    <td><p><?php echo esc_html($this->api->get_request_code()); ?></p></td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php esc_html_e('Access Token', 'connect-pocket-to-website'); ?></th>
                                    <td><p><?php echo esc_html($this->api->get_access_token()); ?></p></td>
                                </tr>
                            </table>
                        <?php
                        endif;
                        submit_button(esc_html__('Save Settings', 'connect-pocket-to-website'));
                        ?>
                    </form>

                    <?php if(!empty($this->api->get_consumer_key())): ?>

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
                        <a href="<?php echo esc_attr($item->resolved_url); ?>"><?php echo esc_html($item->resolved_title); ?></a>
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