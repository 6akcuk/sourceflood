<?php

$assets_dir = '/wp-content/plugins/sourceflood/assets';

wp_enqueue_style('sourceflood-main', $assets_dir .'/css/main.css', array('wp-admin'));

if (is_admin()) {
	wp_enqueue_style('sourceflood-tree', $assets_dir .'/css/tree.min.css');

	wp_enqueue_script('sourceflood-main', $assets_dir .'/js/main.js', array('jquery'));
	wp_enqueue_script('sourceflood-posting', $assets_dir .'/js/posting.js', array('jquery'));
	
	wp_enqueue_script('sourceflood-tree', $assets_dir .'/js/jstree.min.js', array('jquery'));
	
}