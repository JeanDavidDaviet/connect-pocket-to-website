<?php

require dirname(dirname(dirname(dirname((__FILE__))))) . '/wp-load.php';

defined('ABSPATH') || die();

$current_user = wp_get_current_user();
$pwt_url = 'https://jeandaviddaviet.fr/pocket/';

$request_code = get_user_meta($current_user->ID, 'ptw_code', true);

$request = wp_remote_get($pwt_url . '?path=authorize&request_code=' . $request_code);

$response = wp_remote_retrieve_body($request);

parse_str($response, $output);

update_user_meta($current_user->ID, 'ptw_access', $output["access_token"]);
update_user_meta($current_user->ID, 'ptw_username', $output["username"]);

wp_redirect(admin_url('options-general.php?page=pocket-to-wordpress'));
die;