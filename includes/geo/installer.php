<?php

// US States Table
$table_name = $wpdb->prefix . 'sourceflood_us_states';

$charset_collate = $wpdb->get_charset_collate();

$sql = "CREATE TABLE $table_name (
  `state` varchar(22) NOT NULL,
  `state_code` char(2) NOT NULL,
  PRIMARY KEY (`state_code`)
) $charset_collate;";

dbDelta($sql);

// US Cities Table
$table_name = $wpdb->prefix . 'sourceflood_us_cities';

$sql = "CREATE TABLE $table_name (
  `id` INT(10) UNSIGNED AUTO_INCREMENT,
  `city` varchar(50) NOT NULL,
  `state_code` char(2) NOT NULL,
  `zip` int(5) unsigned zerofill NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `county` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) $charset_collate;";

dbDelta($sql);

// UK States Table
$table_name = $wpdb->prefix .'sourceflood_uk_states';

$sql = "CREATE TABLE $table_name (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `country` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `country_short` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) $charset_collate;";

dbDelta($sql);

// UK Cities Table
$table_name = $wpdb->prefix .'sourceflood_uk_cities';

$sql = "CREATE TABLE $table_name (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `region_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `postcode` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `latitude` double NOT NULL DEFAULT '0',
  `longitude` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) $charset_collate;";

dbDelta($sql);


// Install Geo Data
if (!get_option('sourceflood_db_version')) {
	include_once 'us.states.php';
	include_once 'us.cities.php';
  
  include_once 'uk.states.php';
  include_once 'uk.cities.php';
}