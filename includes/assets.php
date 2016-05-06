<?php

$assets_dir = '/wp-content/plugins/workhorse/assets';

wp_enqueue_style('workhorse-main', $assets_dir .'/css/main.css', array('wp-admin'));

if (is_admin()) {
	wp_enqueue_style('workhorse-tree', $assets_dir .'/css/tree.min.css');
	wp_enqueue_style('workhorse-fa', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');

	wp_enqueue_script('workhorse-main', $assets_dir .'/js/main.js', array('jquery'));
	wp_enqueue_script('workhorse-dialog', $assets_dir .'/js/dialog.js', array('jquery'));

	wp_enqueue_script('workhorse-posting', $assets_dir .'/js/posting.js', array('jquery'));
	wp_enqueue_script('workhorse-imagescraper', $assets_dir .'/js/imagescraper.js', array('jquery'));
	wp_enqueue_script('workhorse-videoscraper', $assets_dir .'/js/videoscraper.js', array('jquery'));
	wp_enqueue_script('workhorse-exif', $assets_dir .'/js/exif.js', array('jquery'));
	wp_enqueue_script('workhorse-wordai', $assets_dir .'/js/wordai.js', array('jquery'));
	
	wp_enqueue_script('workhorse-tree', $assets_dir .'/js/jstree.min.js', array('jquery'));
	wp_enqueue_script('workhorse-notify', $assets_dir .'/js/notify.js', array('jquery'));

	wp_enqueue_script('workhorse-underscore', 'https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js', array('underscore'));
	
}