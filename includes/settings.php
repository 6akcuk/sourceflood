<?php

add_action('admin_init', 'workhorse_init_settings');

function workhorse_init_settings() {
	register_setting('workhorse_settings', 'workhorse_license_key');
	register_setting('workhorse_settings', 'workhorse_pixabay_key');
	register_setting('workhorse_settings', 'workhorse_google_api_key');
	register_setting('workhorse_settings', 'workhorse_youtube_key');
	register_setting('workhorse_settings', 'workhorse_word_ai_pass');
	register_setting('workhorse_settings', 'workhorse_word_ai_email');
}