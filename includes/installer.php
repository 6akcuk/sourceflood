<?php

global $sourceflood_db_version;
$sourceflood_db_version = '0.3.2';

function sourceflood_update_db_check() {
    global $sourceflood_db_version;
    if ( get_site_option( 'sourceflood_db_version' ) != $sourceflood_db_version ) {
        sourceflood_install();
    }
}
add_action('plugins_loaded', 'sourceflood_update_db_check');

function sourceflood_install() {
	global $wpdb;
	global $sourceflood_db_version;

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	// Tasks table
	$table_name = $wpdb->prefix . 'sourceflood_tasks';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		`name` VARCHAR(255) NOT NULL,
		`content` MEDIUMTEXT NOT NULL,
		`options` TEXT NOT NULL,
		`iteration` INT UNSIGNED NOT NULL,
		`spintax_iterations` INT UNSIGNED NOT NULL,
		`max_iterations` INT UNSIGNED NOT NULL,
		`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		`finished_at` TIMESTAMP NOT NULL,
		`deleted_at` TIMESTAMP NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	dbDelta($sql);

	// Shortcodes table
	$table_name = $wpdb->prefix . 'sourceflood_shortcodes';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		shortcode VARCHAR(255) NOT NULL,
		type VARCHAR(40) NOT NULL,
		content TEXT NOT NULL,
		created_at TIMESTAMP NOT NULL,
		updated_at TIMESTAMP NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	dbDelta($sql);

	// Geo codes
	include_once 'geo/installer.php';

	// Update SourceFlood DB Schema
	$installed_ver = get_option("sourceflood_db_version");
	if ($installed_ver && $installed_ver != $sourceflood_db_version) {
		update_option( "sourceflood_db_version", $sourceflood_db_version );
	}
	else add_option('sourceflood_db_version', $sourceflood_db_version);
}

function sourceflood_install_data() {
	global $wpdb;
	
	
}