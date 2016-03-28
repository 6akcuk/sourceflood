<?php
/*
Plugin Name: Work Horse
Plugin URI: 
Description: Creates a large number of pages/posts and customize them to rank in Google.
Author: Denis Sirashev
Version: 0.7
*/

define('SOURCEFLOOD_ROOT', dirname(__FILE__));

include_once 'bootstrap.php';

register_activation_hook(__FILE__, 'workhorse_install');
register_activation_hook(__FILE__, 'workhorse_install_data');

register_deactivation_hook(__FILE__, 'workhorse_uninstall');