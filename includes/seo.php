<?php

if (!is_admin()) {
	add_action('init', 'sourceflood_seo_buffer_start');
	add_action('wp_head', 'sourceflood_seo_buffer_end');

	function sourceflood_seo_buffer_start() { 
		ob_start("sourceflood_seo_callback");
	}

	function sourceflood_seo_buffer_end() { 
		ob_end_flush();
	}

	function sourceflood_seo_callback($buffer) {
		global $wp_query;

		if (!is_single()) {
			return $buffer;
		}

		$title = esc_attr(get_post_meta($wp_query->post->ID, 'sourceflood_custom_title', true));
		$description = esc_attr(get_post_meta($wp_query->post->ID, 'sourceflood_custom_description', true));
		$keywords = esc_attr(get_post_meta($wp_query->post->ID, 'sourceflood_custom_keywords', true));

		$meta_seo = array();

		// Meta description
		if (!empty($description)) {
			$meta_seo[] = '<meta name="description" content="'. $description .'">';
		} 
		// TODO: Add automatic generation
		else {
			$description = strlen($wp_query->post->post_content) > 156 ? substr($wp_query->post->post_content, 0, 152) .'...' : $wp_query->post->post_content;
			$meta_seo[] = '<meta name="description" content="'. esc_attr($description) .'">';
		}

		// Meta keywords
		if (!empty($keywords)) {
			$meta_seo[] = '<meta name="keywords" content="'. $keywords .'">';
		}

		$buffer = str_replace('</title>', "</title>\n". implode("\n", $meta_seo), $buffer);

		// Custom Title
		if (!empty($title)) {
			$buffer = preg_replace("/<title>[^<]*<\/title>/u", "<title>$title</title>", $buffer);
		}

		return $buffer;
	}
}