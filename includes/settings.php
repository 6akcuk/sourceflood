<?php

add_action('admin_init', 'sourceflood_init_settings');

function sourceflood_init_settings() {
	register_setting('sourceflood_settings', 'sourceflood_pixabay_key');
}