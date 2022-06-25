<?php

namespace JDD;

class Settings
{

    public function __construct()
    {
        add_action('admin_init', [$this, 'settings_init']);
        add_action('admin_footer', [$this, 'admin_footer']);
    }

    public function settings_init()
    {
        if(isset($_GET['login'])){
            ob_start();
        }

        add_settings_section(
            'ptw_section1',
            'Pocket To WordPress Settings',
            '__return_false',
            'pocket-to-wordpress'
        );

        add_settings_field(
            'ptw_consumer_key',
            'Consumer Key',
            [$this, 'ptw_consumer_key'],
            'pocket-to-wordpress',
            'ptw_section1'
        );
        register_setting('ptw_section1', 'ptw_consumer_key');
    }

    public function admin_footer() {
	    if ( isset( $_GET['login'] ) ) {
		    ob_end_flush();
	    }
    }

    public function ptw_consumer_key()
    {
        $setting = get_option('ptw_consumer_key');
        ?>
        <input name="ptw_consumer_key" type="text" value="<?php echo esc_attr($setting); ?>">
        <?php
    }

}