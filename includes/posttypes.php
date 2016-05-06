<?php

use SourceFlood\Storage;

add_action('init', 'workhorse_create_posttypes');
add_action('pre_get_posts', 'add_workhorse_post_types_to_query');

function add_workhorse_post_types_to_query($query) {
	$storage = new Storage('workhouse');
	$types = $storage->permalink_prefixes;
	$post_types = array('post', 'page', 'channel');

	if (sizeof($types)) {
		foreach ($types as $type => $void) {
			$post_types[] = $type;
		}

		if (is_home() && $query->is_main_query()) {
			$query->set('post_type', $post_types);
		}

		if(is_category() || is_tag()) {
		    $post_type = get_query_var('post_type');

		    $query->set('post_type', $post_types);
		}
	}
	return $query;
}

function workhorse_create_posttypes() {
	global $wp_rewrite;

	$storage = new Storage('workhouse');
	$types = $storage->permalink_prefixes;

	register_post_type('channel',
		array(
			'labels' => array(
				'name' => __('Channels'),
				'singular_name' => __('Channel')
			),
			'public' => true,
			'publicly_queryable' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'channel', 'with_front' => true),
			'capability_type' => 'post',
			'show_ui' => true,
			'query_var' => true,
			'hierarchical' => false,
			'taxonomies' => array('post_tag', 'category'),
		)
	);

	if (sizeof($types)) {
		foreach ($types as $type => $config) {
			register_post_type($type,
				array(
					'labels' => array(
						'name' => __(ucfirst($type)),
						'singular_name' => __(ucfirst($type))
					),
					'public' => true,
					'publicly_queryable' => true,
					'has_archive' => true,
					'rewrite' => array('slug' => "$type/%category%", 'with_front' => false),
	    			'capability_type' => 'post',
	    			'show_ui' => true,
	    			'query_var' => true,
	    			'hierarchical' => false,
	    			'taxonomies' => array('post_tag', 'category'),
				)
			);

			// Rewrite rules for permalink structure with two channels
			if ($config['deep'] == 2) {
				add_rewrite_rule(
					"^{$type}/([^/]+)/([^/]+)/?$",
					'index.php?name=city-$matches[2]-'. $config['project_id'] .'&post_type=channel',
					'top'
				);

				add_rewrite_rule(
					"^{$type}/([^/]+)/?$",
					'index.php?name=state-$matches[1]-'. $config['project_id'] .'&post_type=channel',
					'top'
				);
			}
			elseif ($config['deep'] == 1) {
				add_rewrite_rule(
					"^{$type}/([^/]+)/?$",
					'index.php?name='. $config['type'] .'-$matches[1]-'. $config['project_id'] .'&post_type=channel',
					'top'
				);
			}

			add_rewrite_rule(
				"^{$type}/([^/]+)/(\d+)/?$",
				'index.php?name=$matches[2]&post_type='. $type,
				'top'
			);

			add_rewrite_rule(
				"^{$type}/([^/]+)/([^/]+)/(\d+)/?$",
				'index.php?name=$matches[3]&post_type='. $type,
				'top'
			);

			add_rewrite_rule(
				"^{$type}/([^/]+)/([^/]+)/([^/]+)/(\d+)/?$",
				'index.php?name=$matches[4]&post_type='. $type,
				'top'
			);
		}
	}

	add_rewrite_rule(
		"^([^/]+)/([^/]+)/(\d+)/?$",
		'index.php?name=$matches[3]',
		'top'
	);

	add_rewrite_rule(
		"^page/(\d+)/?$",
		'index.php?paged=$matches[1]',
		'top'
	);

	add_rewrite_rule(
		"^([^/]+)/(\d+)/?$",
		'index.php?name=$matches[2]',
		'top'
	);

	if ($storage->flush_rules) {
		$wp_rewrite->flush_rules(false);

		$storage->flush_rules = false;
	}
}
