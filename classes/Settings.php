<?php

namespace JDD\CPTW;

class Settings
{
    use CPTWTrait;

    public function __construct()
    {
        add_action('admin_init', [$this, 'settings_init']);
        add_action('admin_footer', [$this, 'admin_footer']);
    }

    public function settings_init()
    {
        ob_start();

        add_settings_section(
            'cptw_section1',
            '',
            '__return_false',
            $this->slug
        );

        add_settings_field(
            'cptw_consumer_key',
            esc_html__('Consumer Key', $this->slug),
            [$this, 'cptw_consumer_key'],
            $this->slug,
            'cptw_section1'
        );
        register_setting('cptw_section1', 'cptw_consumer_key');
    }

    public function admin_footer() {
        ob_end_flush();
    }

    public function cptw_consumer_key()
    {
        $setting = get_option('cptw_consumer_key');
        ?>
        <input name="cptw_consumer_key" type="text" value="<?php echo esc_attr($setting); ?>">
        <?php
    }

}