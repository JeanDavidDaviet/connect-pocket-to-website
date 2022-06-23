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
    private $pwt_code;

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
        $this->pwt_code = get_option($this->prefix . 'code');
        $this->access_token = get_option($this->prefix . 'access_token');
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
        if ($_GET['logout'] === 'true' || !empty($auth_error)) {
            delete_option($this->prefix . 'code');
            delete_option($this->prefix . 'access_token');
            delete_option($this->prefix . 'auth_error');
            // todo add notification of failure
            wp_redirect(admin_url('options-general.php?page=pocket-to-wordpress'));
            die;
        }

        if(isset($_GET['login'])){
            $this->request_pocket();
            $this->auth_pocket();
        }

        $list = $this->fetch_pocket();

        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <?php if(empty($this->access_token)): ?>
                <form>
                    <input type="submit" value="Login with Pocket">
                    <input type="hidden" name="login" value="true">
                    <input type="hidden" name="page" value="pocket-to-wordpress">
                </form>
            <?php
            endif;

            if(!empty($this->access_token)): ?>
            <form>
                <input type="submit" value="Deconnect from Pocket">
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

    private function request_pocket(): void
    {
        if (empty($this->access_token) && empty($this->pwt_code)) {

            $response = $this->get_from_pocket('request', [
                'redirect_uri' => $this->redirect_uri
            ]);

            $code = explode('=', $response);
            $this->pwt_code = $code[1];
            update_option($this->prefix . 'code', $this->pwt_code);

        }
    }

    private function auth_pocket(): void
    {
        if (empty($this->access_token) && !empty($this->pwt_code)) {

            wp_redirect('https://getpocket.com/auth/authorize?request_token=' . $this->pwt_code . '&redirect_uri=' . $this->redirect_uri);
            die;

        }
    }

    private function get_from_pocket($path, $params)
    {
        $params = array_merge([
            'path' => $path,
        ], $params);
        $request = wp_remote_get($this->pwt_url . '?' . http_build_query($params));
        return wp_remote_retrieve_body($request);
    }

    public function fetch_pocket($access_token = '', $options = [])
    {
        if (empty($access_token)){
            $access_token = $this->access_token;
        }
        if (!empty($access_token)) {
            $response = $this->get_from_pocket('get', array_merge([
                'access_token' => $access_token
            ], $options));
            $list = (array) json_decode($response);
            update_option($this->prefix . 'list', $list);
            return $list;
        }
        return [];
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