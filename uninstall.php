<?php

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
 
$option_code = 'wz_code';
$option_multi = 'wz_multi';
 
delete_option($option_name);
delete_option($option_multi);

?>