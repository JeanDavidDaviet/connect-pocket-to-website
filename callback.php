<?php

require dirname(dirname(dirname(dirname((__FILE__))))) . '/wp-load.php';

defined('ABSPATH') || die();

$pwt_url = 'https://jeandaviddaviet.fr/pocket/';

$request_code = get_option('ptw_code');

$request = wp_remote_get($pwt_url . '?path=authorize&request_code=' . $request_code);
$response = wp_remote_retrieve_body($request);

parse_str($response, $output);

if(!isset($output["access_token"])){
    $error_code = substr(array_keys($output)[0], 0, 3);
    $error_message = 'Something wrong happened';
    // user denied access
    if($error_code === '400'){
        $error_message = 'Invalid request, please make sure you follow the documentation for proper syntax';
    }
    if($error_code === '401'){
        $error_message = 'Problem authenticating the user';
    }
    if($error_code === '403'){
        $error_message = 'User was authenticated, but access denied due to lack of permission or rate limiting';
    }
    if($error_code === '503'){
        $error_message = 'Pocket\'s sync server is down for scheduled maintenance.';
    }
    update_option('ptw_auth_error', [
        'code' => $error_code,
        'message' => $error_message,
    ]);
}else{
    update_option('ptw_access', $output["access_token"]);
    update_option('ptw_username', $output["username"]);
}

wp_redirect(admin_url('options-general.php?page=pocket-to-wordpress'));
die;