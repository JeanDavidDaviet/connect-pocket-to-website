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

        //Get the active tab from the $_GET param
        $default_tab = null;
        $tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;

        $tab_url = admin_url('options-general.php?page=pocket-to-wordpress');

        ?>
        <div class="wrap">

            <h1><?php _e('Pocket To WordPress Settings', 'pocket-to-wordpress'); ?></h1>

            <nav class="nav-tab-wrapper">
                <a href="<?php echo $tab_url; ?>" class="nav-tab <?php if($tab===null):?>nav-tab-active<?php endif; ?>"><?php _e('Connection', 'pocket-to-wordpress'); ?></a>
                <a href="<?php echo $tab_url; ?>&tab=how-to" class="nav-tab <?php if($tab==='how-to'):?>nav-tab-active<?php endif; ?>"><?php _e('How-to', 'pocket-to-wordpress'); ?></a>
                <a href="<?php echo $tab_url; ?>&tab=display" class="nav-tab <?php if($tab==='display'):?>nav-tab-active<?php endif; ?>"><?php _e('Display', 'pocket-to-wordpress'); ?></a>
            </nav>

            <div class="tab-content">
	            <?php if($tab === null): ?>
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
	            <?php endif; ?>
	            <?php if($tab === 'how-to'): ?>
                    <h2><?php _e('How to get your consumer key', 'pocket-to-wordpress'); ?></h2>
                    <p><?php _e(sprintf('In order to connect to Pocket from your WordPress website, you need to create an application on the %1$spocket API website%2$s.%3$sFollow the steps below to create you Pocket API application and get your %4$sconsumer key%5$s', '<a href="https://getpocket.com/developer/">', '</a>', '<br />', '<strong>', '</strong>'), 'pocket-to-wordpress'); ?>.</p>

                    <h2><?php _e('The steps', 'pocket-to-wordpress'); ?></h2>
                    <p><?php _e('First, you need to be connected to your Pocket account to create a new app.', 'pocket-to-wordpress'); ?><br />
			            <?php _e('Once connected, you can click on the big red button "Create new app".', 'pocket-to-wordpress'); ?><br />
			            <?php _e('Then, you arrive on a page asking you some details on your application.', 'pocket-to-wordpress'); ?></p>

                    <ul style="list-style: disc;list-style-position: inside;">
                        <li><?php _e(sprintf('The application Name : (eg: %1$s%2$s%3$s)', '<strong>', esc_html(get_option('blogname', site_url())),'</strong>'), 'pocket-to-wordpress'); ?></li>
                        <li><?php _e(sprintf('The application Description : (eg: %1$s%2$s%3$s)', '<strong>', esc_html(get_option('blogdescription', __( 'Just another WordPress site' ))), '</strong>'), 'pocket-to-wordpress'); ?></li>
                        <li><?php _e(sprintf('Permissions : You only need to check %1$sRetrieve%2$s', '<strong>', '</strong>'), 'pocket-to-wordpress'); ?></li>
                        <li><?php _e(sprintf('Platforms : Choose %1$sWeb%2$s.', '<strong>', '</strong>'), 'pocket-to-wordpress'); ?></li>
                        <li><?php _e(sprintf('Accept the %1$sTerms of Services%2$s.', '<strong>', '</strong>'), 'pocket-to-wordpress'); ?></li>
                    </ul>
                    <p><?php _e(sprintf('Click on the %1$sCreate Application%2$s button.', '<strong>', '</strong>'), 'pocket-to-wordpress'); ?><br />
			            <?php _e('You are now redirected to the list of your applications.', 'pocket-to-wordpress'); ?><br />
			            <?php _e(sprintf('All you have to do now is copy the consumer key and paste it it the %1$sConnection%2$s tab of this plugin.', '<a href="' . $tab_url . '">', '</a>'), 'pocket-to-wordpress'); ?></p>

                    <h2><?php _e('Screenshots of an example application', 'pocket-to-wordpress'); ?></h2>
                    <p><img src="<?php echo plugin_dir_url(__FILE__) . '/img/howto.jpg'; ?>" alt="screenshot of how to create a pocket application" width="600"></p>
                    <p><img src="<?php echo plugin_dir_url(__FILE__) . '/img/howto2.jpg'; ?>" alt="screenshot of the list of pocket applications" width="600"></p>
	            <?php endif; ?>
	            <?php if($tab === 'display'): ?>
                    <style>.shortcode_creator_group ul { padding-left: 20px; }</style>
                    <h2><?php _e('How to display a feed on your WordPress site', 'pocket-to-wordpress'); ?></h2>
                    <p><?php _e(sprintf('This plugin gives you the possibility to display a list of feed via the use of %1$sshortcodes%2$s.', '<a href="https://wordpress.com/support/shortcodes/" target="_blank">', '</a>'), 'pocket-to-wordpress'); ?><br />
                    <?php _e('Simply compose your shorcode with the use of the predefined filters below.', 'pocket-to-wordpress'); ?></p>
                    <p><input id="shortcode_display" type="text" value="[pocket-to-wordpress]" style="width: 100%;"></p>

                    <div class="shortcode_creator">
                        <div class="shortcode_creator_group">
                            <p><strong>State</strong></p>
                            <ul>
                                <li>
                                    <input type="radio" id="state_unread" name="state" value="unread" />
                                    <label for="state_unread">unread</label>
                                </li>
                                <li>
                                    <input type="radio" id="state_archive" name="state" value="archive" />
                                    <label for="state_archive">archive</label>
                                </li>
                                <li>
                                    <input type="radio" id="state_all" name="state" value="all" />
                                    <label for="state_all">all</label>
                                </li>
                            </ul>
                        </div>

                        <div class="shortcode_creator_group">
                            <p><strong>Favorite</strong></p>
                            <ul>
                                <li>
                                    <input type="radio" id="favorite_0" name="favorite" value="0" />
                                    <label for="favorite_0">only return un-favorited items</label>
                                </li>
                                <li>
                                    <input type="radio" id="favorite_1" name="favorite" value="1" />
                                    <label for="favorite_1">only return favorited items</label>
                                </li>
                            </ul>
                        </div>

                        <div class="shortcode_creator_group">
                            <p><strong>Tag</strong></p>
                            <ul>
                                <li>
                                    <input type="radio" id="tag_tagged" name="tag"/>
                                    <label for="tag_tagged">only return items tagged with <input type="text" name="tag"></label>
                                </li>
                                <li>
                                    <input type="radio" id="tag_1_untagged" name="tag" value="_untagged_" />
                                    <label for="tag_1_untagged">only return untagged items</label>
                                </li>
                            </ul>
                        </div>

                        <div class="shortcode_creator_group">
                            <p><strong>contentType</strong></p>
                            <ul>
                                <li>
                                    <input type="radio" id="contentType_article" name="contentType" value="article" />
                                    <label for="contentType_article">only return articles</label>
                                </li>
                                <li>
                                    <input type="radio" id="contentType_video" name="contentType" value="video" />
                                    <label for="contentType_video">only return videos or articles with embedded videos</label>
                                </li>
                                <li>
                                    <input type="radio" id="contentType_image" name="contentType" value="image" />
                                    <label for="contentType_image">only return images</label>
                                </li>
                            </ul>
                        </div>

                        <div class="shortcode_creator_group">
                        <p><strong>sort</strong></p>
                            <ul>
                                <li>
                                    <input type="radio" id="sort_newest" name="sort" value="newest" />
                                    <label for="sort_newest">return items in order of newest to oldest</label>
                                </li>
                                <li>
                                    <input type="radio" id="sort_oldest" name="sort" value="oldest" />
                                    <label for="sort_oldest">return items in order of oldest to newest</label>
                                </li>
                                <li>
                                    <input type="radio" id="sort_title" name="sort" value="title" />
                                    <label for="sort_title">return items in order of title alphabetically</label>
                                </li>
                                <li>
                                    <input type="radio" id="sort_site" name="sort" value="site" />
                                    <label for="sort_site">return items in order of url alphabetically</label>
                                </li>
                            </ul>
                        </div>

                        <div class="shortcode_creator_group">
                            <p><strong>search</strong><br />Only return items whose title or url contain the search string</p>
                            <input type="text" name="search">
                        </div>

                        <div class="shortcode_creator_group">
                            <p><strong>domain</strong><br />Only return items from a particular domain</p>
                            <input type="text" name="domain">
                        </div>

                        <div class="shortcode_creator_group">
                            <p><strong>count</strong><br />Only return count number of items</p>
                            <input type="number" name="count" step="1" min="0">
                        </div>

                        <div class="shortcode_creator_group">
                            <p><strong>offset</strong><br />Used only with count; start returning from offset position of results</p>
                            <input type="number" name="offset" step="1" min="0">
                        </div>
                    </div>

                    <script>
                        const shortcodeDisplay = document.getElementById('shortcode_display');
                        Array.from(document.querySelectorAll('.shortcode_creator input')).forEach(input => {
                            input.addEventListener('change', function(e){
                                calculateShortcode();
                            });
                        });
                        function calculateShortcode(){
                            let shortcode = '';

                            Array.from(document.querySelectorAll('.shortcode_creator input[type="radio"]')).forEach(input => {
                                if(input.checked && input.value !== 'on'){
                                    shortcode += ` ${input.name}="${input.value}"`;
                                }
                            });

                            Array.from(document.querySelectorAll('.shortcode_creator input[type="text"], .shortcode_creator input[type="number"]')).forEach(input => {
                                if(input.value.trim() !== ''){
                                    shortcode += ` ${input.name}="${input.value.trim()}"`;
                                }
                            });

                            shortcodeDisplay.value = `[pocket-to-wordpress${shortcode}]`;
                        }
                    </script>
	            <?php endif; ?>
            </div>
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