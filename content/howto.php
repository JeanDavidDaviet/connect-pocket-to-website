<h2><?php _e('How to get your consumer key', 'connect-pocket-to-website'); ?></h2>
<p><?php _e(sprintf('In order to connect to Pocket from your WordPress website, you need to create an application on the %1$spocket API website%2$s.%3$sFollow the steps below to create you Pocket API application and get your %4$sconsumer key%5$s', '<a href="https://getpocket.com/developer/">', '</a>', '<br />', '<strong>', '</strong>'), 'connect-pocket-to-website'); ?>.</p>

<h2><?php _e('The steps', 'connect-pocket-to-website'); ?></h2>
<p><?php _e('First, you need to be connected to your Pocket account to create a new app.', 'connect-pocket-to-website'); ?><br />
    <?php _e('Once connected, you can click on the big red button "Create new app".', 'connect-pocket-to-website'); ?><br />
    <?php _e('Then, you arrive on a page asking you some details on your application.', 'connect-pocket-to-website'); ?></p>

<ul style="list-style: disc inside;">
    <li><?php _e(sprintf('The application Name : (eg: %1$s%2$s%3$s)', '<strong>', esc_html(get_option('blogname', site_url())),'</strong>'), 'connect-pocket-to-website'); ?></li>
    <li><?php _e(sprintf('The application Description : (eg: %1$s%2$s%3$s)', '<strong>', esc_html(get_option('blogdescription', __( 'Just another WordPress site' ))), '</strong>'), 'connect-pocket-to-website'); ?></li>
    <li><?php _e(sprintf('Permissions : You only need to check %1$sRetrieve%2$s', '<strong>', '</strong>'), 'connect-pocket-to-website'); ?></li>
    <li><?php _e(sprintf('Platforms : Choose %1$sWeb%2$s.', '<strong>', '</strong>'), 'connect-pocket-to-website'); ?></li>
    <li><?php _e(sprintf('Accept the %1$sTerms of Services%2$s.', '<strong>', '</strong>'), 'connect-pocket-to-website'); ?></li>
</ul>
<p><?php _e(sprintf('Click on the %1$sCreate Application%2$s button.', '<strong>', '</strong>'), 'connect-pocket-to-website'); ?><br />
    <?php _e('You are now redirected to the list of your applications.', 'connect-pocket-to-website'); ?><br />
    <?php _e(sprintf('All you have to do now is copy the consumer key and paste it it the %1$sConnection%2$s tab of this plugin.', '<a href="' . admin_url('options-general.php?page=connect-pocket-to-website') . '">', '</a>'), 'connect-pocket-to-website'); ?></p>

<h2><?php _e('Screenshots of an example application', 'connect-pocket-to-website'); ?></h2>
<p><img src="<?php echo plugin_dir_url(__FILE__) . '/img/howto.jpg'; ?>" alt="screenshot of how to create a pocket application" width="600"></p>
<p><img src="<?php echo plugin_dir_url(__FILE__) . '/img/howto2.jpg'; ?>" alt="screenshot of the list of pocket applications" width="600"></p>