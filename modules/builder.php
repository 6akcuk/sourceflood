<?php

use SourceFlood\View;
use SourceFlood\License;
use SourceFlood\Spintax;
use SourceFlood\Storage;
use SourceFlood\LiteSpintax;
use SourceFlood\Models\Task;
use SourceFlood\ChannelManager;

function workhorse_builder() {
	global $wpdb;
	global $wp_rewrite;
	
	ignore_user_abort(true);
	@set_time_limit(0);
	ob_implicit_flush(true);

	ini_set("pcre.backtrack_limit", "23001337");
	ini_set("pcre.recursion_limit", "23001337");

	$id = $_GET['id'];

	if (!License::checkThatLicenseIsValid()) {
		return;
	}
	if (!$id) {
		echo '<h3>Please, build posts/pages from <a href="/wp-admin/admin.php?page=workhorse_projects">projects list.</a></h3>';
		return;
	}

	ob_start();
	ob_clean();
	session_write_close();

	flush();

	$model = new Task();
	$model->update(array('deleted_at' => '0000-00-00 00:00:00'), $id);

	$project = $model->find($id);

	$options = json_decode($project->options, true);
	$posts = 1;
	$geo = isset($options['local_geo_country']);

	$post_date = date('Y-m-d H:i:s');

	// Use Dripfeed
	if (isset($options['dripfeed_type'])) {
		switch ($options['dripfeed_type']) {
			case 'per-day':
				$per_day = $options['dripfeed_x'];
				break;
			case 'over-days':
				$per_day = ceil($project->max_iterations / $options['dripfeed_x']);
				break;
		}
	} else {
		$per_day = $project->max_iterations;
	}

	$data = json_decode($project->content, true);
	
	$title = $data['title'];
	$content = $data['content'];

	$titleSpintax = Spintax::parse($data['title']);
	$titleMax = Spintax::count($titleSpintax);

	//$contentSpintax = Spintax::parse($content);
	//$contentMax = Spintax::count($contentSpintax);

	$posts = $project->max_iterations;

	if (isset($options['exif_locations'])) {
		$options['exif_locations'] = str_replace('\"', '"', $options['exif_locations']);

		$exifLocations = json_decode($options['exif_locations']);
	}

	$start_date = new DateTime();
	$current_per_day = 0;

	$step = 100;
	$current_post = 0;

	$storage = new Storage('workhouse');

	// Authors
	if ($options['distribute']) {
		$_authors = $wpdb->get_results("SELECT user_id FROM {$wpdb->prefix}usermeta WHERE meta_key = 'workhorse_user' ORDER BY RAND() LIMIT 500");
		foreach ($_authors as $a) {
			$authors[] = $a->user_id;
		}
		shuffle($authors);
	}

	// Permalink prefix
	if (isset($options['permalink_prefix'])) {
		$prefixes = $storage->permalink_prefixes;

		if (!isset($prefixes[$options['permalink_prefix']])) $prefixes[$options['permalink_prefix']] = [];

		$storage->permalink_prefixes = $prefixes;
		
		register_post_type($options['permalink_prefix'],
			array(
				'labels' => array(
					'name' => __(ucfirst($options['permalink_prefix'])),
					'singular_name' => __(ucfirst($options['permalink_prefix']))
				),
				'public' => true,
				'publicly_queryable' => true,
				'has_archive' => true,
				'rewrite' => array('slug' => $options['permalink_prefix'] .'/%category%', 'with_front' => false),
    			'capability_type' => 'post',
    			'show_ui' => true,
    			'query_var' => true,
    			'hierarchical' => false,
    			'taxonomies' => array('post_tag', 'category'),
			)
		);

		$wp_rewrite->flush_rules(false);

		$storage->flush_rules = true;
	}

	$wpdb->query('SET autocommit = 0;');

	$lite = new LiteSpintax();
	$channel_cache = array('state' => array(null, null), 'city' => array(null, null));

	$google_api_key = get_option('workhorse_google_api_key');

	for ($i = 1; $i <= $posts; $i++) {	
		$project->iteration++;
		$current_per_day++;
		$current_post++;
		
		if ($project->iteration == $project->max_iterations + 1) {
			$project->iteration = $project->max_iterations;
			break;
		}
		
		if (isset($options['permalink_prefix'])) {
			$data['post_type'] = $options['permalink_prefix'];
		}

		if ($geo) {
			$geoIteration = $project->iteration;
			$geoData = sourceflood_get_geodata($options['local_geo_country'], $options['local_geo_locations'][$geoIteration - 1]);

			// Channel pages
			if ($geoData['city'] && isset($data['state_channel_page'])) {
				ChannelManager::create($project->id, $data, $geoData, 'state');
			}

			if ($geoData['zip'] && isset($data['city_channel_page'])) {
				ChannelManager::create($project->id, $data, $geoData, 'city');
			}

			// Save permalink structure for channels
			if (isset($options['permalink_prefix'])) {
				$storage = new Storage('workhouse');
				
				$prefixes = $storage->permalink_prefixes;
				if (!sizeof($prefixes[$options['permalink_prefix']])) {
					$prefixes[$options['permalink_prefix']] = ChannelManager::getPermalinkStructure($project->id);

					$storage->permalink_prefixes = $prefixes;
				}
			}
		}

		// Get current spintax iteration
		$spintaxIteration = sourceflood_get_current_subiteration($project->iteration, $project->spintax_iterations);

		// Get current iteration for each field
		$titleIteration = sourceflood_get_spintax_subiteration($titleMax, $project, $spintaxIteration);
		//$contentIteration = sourceflood_get_spintax_subiteration($contentMax, $project, $spintaxIteration);

		$titleText = Spintax::make($title, $titleIteration, $titleSpintax, false);
		if ($geo) $titleText = Spintax::geo($titleText, $geoData);

		$contentText = $lite->process($content);
		if ($geo) $contentText = Spintax::geo($contentText, $geoData);

		// Images EXIF
		if (isset($options['exif_locations'])) {
			if (isset($options['use_post_location'])) {
				$address = urlencode($geoData['country'] .', '. $geoData['state'] .', '. $geoData['city'] .', '. $geoData['zip']);
			} else {
				$locationIteration = sourceflood_get_current_subiteration($project->iteration, sizeof($exifLocations)) - 1;
				$address = $exifLocations[$locationIteration]->address;
			}

			if (!isset($options['exif_cache'])) $options['exif_cache'] = [];
			if (!isset($options['exif_cache'][$address])) $options['exif_cache'][$address] = [];

			preg_match_all('/src=\\\"([^"]*)\\\" alt=\\\"([^"]*)\\\" width/ui', $contentText, $exif);

			if (isset($exif[1])) {
				foreach ($exif[1] as $idx => $image) {
					if (!isset($options['exif_cache'][$address][$image])) {
						$image = str_replace(':8000', '', $image); // Fix only for local dev
						$filename = sha1($address .'-'. $image .'-exif') .'.jpg';

						$exploded = explode('.', $image);
					    $ext = $exploded[count($exploded) - 1]; 

					    if (preg_match('/jpg|jpeg/i', $ext))
					        $imageSrc = imagecreatefromjpeg($image);
					    else if (preg_match('/png/i', $ext))
					        $imageSrc = imagecreatefrompng($image);
					    else if (preg_match('/gif/i', $ext))
					        $imageSrc = imagecreatefromgif($image);
					    else if (preg_match('/bmp/i', $ext))
					        $imageSrc = imagecreatefrombmp($image);

					    $imagedir = 'uploads/'. date('Y') .'/'. date('m') .'/'. $filename;
						workhorse_check_dir($imagedir);

						// Location coordinates
						if (isset($options['use_post_location'])) {
							$response = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$address&key=$google_api_key"));

							if ($response->status == 'OK') {
								$location = $response->results[0]->geometry->location;
							}
						} else {
							$location = $exifLocations[$locationIteration]->location;
						}

						if ($location) {
						    addGpsInfo(
						    	$imageSrc, 
						    	WP_CONTENT_DIR .'/'. $imagedir,
						    	$exif[2][$idx],
						    	'Work Horse Comment',
						    	'Work Horse',
						    	$location->lng,
						    	$location->lat,
						    	0,
						    	date('Y-m-d H:i:s')
					    	);
						}

				    	$savedir = "/wp-content/$imagedir";

					    // local dev fix
					    $image = str_replace('.app', '.app:8000', $image);
					    
				    	$options['exif_cache'][$address][$image] = $savedir;
					} else {
						$savedir = $options['exif_cache'][$address][$image];
					}
					
			    	$contentText = str_replace($image, $savedir, $contentText);
				}
			}
		}

		// Check if project still exists
		$test = $model->find($id);
		if (!$test || ($test && !$test->id)) {
			echo '<h3>Project stopped by user.</h3>';
			return;
		}
		if ($test && $test->deleted_at == '1970-01-01 11:11:11') {
			echo '<h3>Project stopped by user.</h3>';
			break;
		}

		// Permalink
		$postName = $titleText;

		if (isset($options['permalink']) && $options['permalink']) {
			$postName = Spintax::geo($options['permalink'], $geoData);
			$postName = str_replace('@title', $titleText, $postName);
		}

		// Distribute
		$author_id = 1;

		if (isset($options['distribute'])) {
			$author_id = $authors[rand(0, sizeof($authors) - 1)];
		}

		if (isset($options['dripfeed_type'])) {
			$date_start = strtotime($start_date->format('Y-m-d') .' 00:00:00');
			$date_end = strtotime($start_date->format('Y-m-d') .' 23:59:59');

			$post_date = date('Y-m-d H:i:s', rand($date_start, $date_end));
		}

		if (isset($options['categorization'])) {
			$last = end($options['categorization']);

			$postName = $geoData[$last];
		}

		$post_id = wp_insert_post([
            'post_title' => $titleText,
            'post_name' => sanitize_title($postName),
            'post_date' => $post_date,
            'post_author' => $author_id,
            'post_content' => $contentText,
            'post_status' => $interval == 0 ? 'publish' : 'future',
            'post_type' => $data['post_type'],
            'comment_status' => 'closed',
            'ping_status' => 'closed'
		]);

		// Categorization
		if (isset($options['categorization'])) {
			$tags = $options['categorization'];
			$category = null;

			foreach ($tags as $tag) {
				if ($tag == $last) continue;

				$category = wp_create_category($geoData[$tag], $category);
			}

			wp_set_post_categories($post_id, array($category));
		}

		add_post_meta($post_id, 'sourceflood_project_id', $project->id);

		// On-Page SEO Section
		if (isset($options['custom_title'])) {
			$customTitleText = sourceflood_spintax_the_field($options['custom_title'], $project, $spintaxIteration, $geo, $geoData);

			add_post_meta($post_id, 'sourceflood_custom_title', $customTitleText);
			add_post_meta($post_id, '_yoast_wpseo_title', $customTitleText); // Yoast SEO
		}
		if (isset($options['custom_description'])) {
			$customDescriptionText = sourceflood_spintax_the_field($options['custom_description'], $project, $spintaxIteration, $geo, $geoData);

			add_post_meta($post_id, 'sourceflood_custom_description', $customDescriptionText);
			add_post_meta($post_id, '_yoast_wpseo_metadesc', $customDescriptionText);
		}
		if (isset($options['custom_keywords'])) {
			$customKeywordsText = sourceflood_spintax_the_field($options['custom_keywords'], $project, $spintaxIteration, $geo, $geoData);

			add_post_meta($post_id, 'sourceflood_custom_keywords', $customKeywordsText);
		}

		// Schema Section
		if (isset($options['schema_business'])) {
			$schemaBusinessText = sourceflood_spintax_the_field($options['schema_business'], $project, $spintaxIteration, $geo, $geoData);

			add_post_meta($post_id, 'sourceflood_schema_business', $schemaBusinessText);
		}
		if (isset($options['schema_description'])) {
			$schemaDescriptionText = sourceflood_spintax_the_field($options['schema_description'], $project, $spintaxIteration, $geo, $geoData);

			add_post_meta($post_id, 'sourceflood_schema_description', $schemaDescriptionText);
		}
		if (isset($options['schema_email'])) {
			$schemaEmailText = sourceflood_spintax_the_field($options['schema_email'], $project, $spintaxIteration, $geo, $geoData);

			add_post_meta($post_id, 'sourceflood_schema_email', $schemaEmailText);
		}
		if (isset($options['schema_telephone'])) {
			add_post_meta($post_id, 'sourceflood_schema_telephone', $options['schema_telephone']);
		}
		if (isset($options['schema_social'])) {
			$schemaSocialText = sourceflood_spintax_the_field($options['schema_social'], $project, $spintaxIteration, $geo, $geoData);

			add_post_meta($post_id, 'sourceflood_schema_social', $schemaSocialText);
		}
		if (isset($options['schema_rating_object'])) {
			$schemaRatingObjectText = sourceflood_spintax_the_field($options['schema_rating_object'], $project, $spintaxIteration, $geo, $geoData);

			add_post_meta($post_id, 'sourceflood_schema_rating_object', $schemaRatingObjectText);
		}
		if (isset($options['schema_rating'])) {
			add_post_meta($post_id, 'sourceflood_schema_rating', $options['schema_rating']);
		}
		if (isset($options['schema_rating_count'])) {
			add_post_meta($post_id, 'sourceflood_schema_rating_count', $options['schema_rating_count']);
		}
		if (isset($options['schema_address'])) {
			$schemaAddressText = sourceflood_spintax_the_field($options['schema_address'], $project, $spintaxIteration, $geo, $geoData);

			add_post_meta($post_id, 'sourceflood_schema_address', $schemaAddressText);
		}

		// Tags
		if (isset($options['tags']) && $options['tags']) {
			$tags = Spintax::geo($options['tags'], $geoData);

			wp_set_post_tags($post_id, $tags, true);

			// Add noindex meta tag to tag page
			if (isset($options['noindex_tags'])) {
				$tags = explode(',', $tags);

				foreach ($tags as $tag) {
					$term = get_term_by('name', $tag, 'post_tag');

					add_term_meta($term->term_id, 'workhorse_noindex_tag', 1, true);
				}
			}
		}
		
		// Channel page link
		ChannelManager::addLink($post_id, $geoData);
		
		// Pre-Safe project
		$update = array(
			'iteration' => $project->iteration
		);
		if (isset($options['exif_cache'])) {
			$update['options'] = json_encode($options);
		}

		$model->update($update, $project->id);

		// Commit
		if ($current_post == $step) {
			ChannelManager::save();

			$wpdb->query('COMMIT;');
			$current_post = 0;
		}

		if ($current_per_day == $per_day) {
			$start_date->add(new DateInterval('P1D'));
			$current_per_day = 0;
		} else {
			//$start_date = rand(time() - 43200, time() + 43200);
		}

		flush();
	}

	$wpdb->query('COMMIT;');

	$wpdb->query("SET autocommit = 1;");

	ChannelManager::save();

	// Save project changes
	$update = array(
		'iteration' => $project->iteration
	);

	if ($project->iteration == $project->max_iterations) {
		$update['finished_at'] = date('Y-m-d H:i:s');
	}

	if (isset($options['exif_cache'])) {
		$update['options'] = json_encode($options);
	}

	$model->update($update, $project->id);

	View::render('builder.index');
	return;
}