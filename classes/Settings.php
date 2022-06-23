<?php

namespace JDD;

class Settings
{

    public function __construct()
    {
        add_action('admin_init', [$this, 'settings_init']);
    }

    public function settings_init()
    {
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

    public function ptw_consumer_key()
    {
        $setting = get_option('ptw_consumer_key');
        ?>
        <input name="ptw_consumer_key" type="text" value="<?php echo esc_attr($setting); ?>">
        <?php
    }

}