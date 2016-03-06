<?php

add_filter('wp_title', 'sourceflood_seo_title');

function sourceflood_seo_title($title) {
	return $title;
}