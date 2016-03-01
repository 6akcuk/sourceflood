<?php

global $sourceflood_db_version;
$sourceflood_db_version = '0.1';

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
		`iteration` INT UNSIGNED NOT NULL,
		`created_at` TIMESTAMP NOT NULL,
		`updated_at` TIMESTAMP NOT NULL,
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

	add_option('sourceflood_db_version', $sourceflood_db_version);
}

function sourceflood_install_data() {
	global $wpdb;
	
	
}