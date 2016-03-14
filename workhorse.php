<?php
/*
Plugin Name: Work Horse
Plugin URI: 
Description: Creates a large number of pages/posts and customize them to rank in Google.
Author: Denis Sirashev
Version: 0.4.1
*/

define('SOURCEFLOOD_ROOT', dirname(__FILE__));

include_once 'bootstrap.php';

register_activation_hook(__FILE__, 'sourceflood_install');
register_activation_hook(__FILE__, 'sourceflood_install_data');