<?php

if (!is_admin()) {
	add_action('init', 'sourceflood_seo_buffer_start');
	add_action('wp_head', 'sourceflood_seo_buffer_end');

	add_filter('the_content', 'sourceflood_seo_filter_content');

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

	function sourceflood_seo_filter_content($content) {
		global $wp_query;

		if (is_single()) {
			$business = esc_attr(get_post_meta($wp_query->post->ID, 'sourceflood_schema_business', true));
			$description = esc_attr(get_post_meta($wp_query->post->ID, 'sourceflood_schema_description', true));
			$email = esc_attr(get_post_meta($wp_query->post->ID, 'sourceflood_schema_email', true));
			$telephone = esc_attr(get_post_meta($wp_query->post->ID, 'sourceflood_schema_telephone', true));
			$social = esc_attr(get_post_meta($wp_query->post->ID, 'sourceflood_schema_social', true));
			$rating = esc_attr(get_post_meta($wp_query->post->ID, 'sourceflood_schema_rating', true));
			$address = esc_attr(get_post_meta($wp_query->post->ID, 'sourceflood_schema_address', true));
			
			if ($business || $description || $email || $telephone || $social || $rating || $address) {
				$schema = '<div itemscope>';
				if ($business) $schema .= '<span itemprop="name">'. $business .'</span>';
				
				if ($email) $schema .= 'E-mail: <span itemprop="email">'. $email .'</span>';
				if ($telephone) $schema .= 'Tel:<span itemprop="telephone">'. $telephone .'</span>';
				if ($social) $schema .= 'Social:<span itemprop="social">'. $social .'</span>';
				if ($rating) {
					$schema .= '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
						<span itemprop="ratingValue">'. $rating .'</span>
					</div>';
				}

				if ($address) {
					$schema .= '<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
						Main address:
							<span itemprop="streetAddress">'. $address .'</span>
					</div>';
				}

				if ($description) $schema .= '<span itemprop="description">'. $description .'</span>';

				$schema .= '</div>';

				// Rendering schema
				$content = $content . $schema;
			}
		}

		return $content;
	}
}