<?php
/*
Plugin Name: Work Horse
Plugin URI: 
Description: Creates a large number of pages/posts and customize them to rank in Google.
Author: Work Horse Team
Version: 1.2.2
*/

define('SOURCEFLOOD_ROOT', dirname(__FILE__));

include_once 'bootstrap.php';

register_activation_hook(__FILE__, 'workhorse_install');
register_activation_hook(__FILE__, 'workhorse_install_data');

// Features
register_activation_hook(__FILE__, 'whs_Activation');
register_activation_hook(__FILE__,'whs_BuildPluginSQLTable');

register_deactivation_hook(__FILE__, 'workhorse_uninstall');