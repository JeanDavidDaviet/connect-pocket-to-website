<h2><?php esc_html_e('How to get your consumer key', 'connect-pocket-to-website'); ?></h2>
<p><?php printf(__('In order to connect to Pocket from your WordPress website, you need to create an application on the %1$spocket API website%2$s.%3$sFollow the steps below to create you Pocket API application and get your %4$sconsumer key%5$s', 'connect-pocket-to-website'), '<a href="https://getpocket.com/developer/">', '</a>', '<br />', '<strong>', '</strong>'); ?>.</p>

<h2><?php esc_html_e('The steps', 'connect-pocket-to-website'); ?></h2>
<p><?php esc_html_e('First, you need to be connected to your Pocket account to create a new app.', 'connect-pocket-to-website'); ?><br />
    <?php esc_html_e('Once connected, you can click on the big red button "Create new app".', 'connect-pocket-to-website'); ?><br />
    <?php esc_html_e('Then, you arrive on a page asking you some details on your application.', 'connect-pocket-to-website'); ?></p>

<ul style="list-style: disc inside;">
    <li><?php printf(__('The application Name : (eg: %1$s%2$s%3$s)', 'connect-pocket-to-website'), '<strong>', esc_html(get_option('blogname', site_url())),'</strong>'); ?></li>
    <li><?php printf(__('The application Description : (eg: %1$s%2$s%3$s)', 'connect-pocket-to-website'), '<strong>', esc_html(get_option('blogdescription', __( 'Just another WordPress site' ))), '</strong>'); ?></li>
    <li><?php printf(__('Permissions : You only need to check %1$sRetrieve%2$s', 'connect-pocket-to-website'), '<strong>', '</strong>'); ?></li>
    <li><?php printf(__('Platforms : Choose %1$sWeb%2$s.', 'connect-pocket-to-website'), '<strong>', '</strong>'); ?></li>
    <li><?php printf(__('Accept the %1$sTerms of Services%2$s.', 'connect-pocket-to-website'), '<strong>', '</strong>'); ?></li>
</ul>
<p><?php printf(__('Click on the %1$sCreate Application%2$s button.', 'connect-pocket-to-website'), '<strong>', '</strong>'); ?><br />
    <?php esc_html_e('You are now redirected to the list of your applications.', 'connect-pocket-to-website'); ?><br />
    <?php printf(__('All you have to do now is copy the consumer key and paste it it the %1$sConnection%2$s tab of this plugin.', 'connect-pocket-to-website'), '<a href="' . admin_url($this->admin_url) . '">', '</a>'); ?></p>

<h2><?php esc_html_e('Screenshots of an example application', 'connect-pocket-to-website'); ?></h2>
<p><img src="<?php echo esc_attr(plugin_dir_url(__FILE__) . '/img/howto.jpg'); ?>" alt="screenshot of how to create a pocket application" width="600"></p>
<p><img src="<?php echo esc_attr(plugin_dir_url(__FILE__) . '/img/howto2.jpg'); ?>" alt="screenshot of the list of pocket applications" width="600"></p>