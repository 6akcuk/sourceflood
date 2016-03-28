<?php

use SourceFlood\View;
use SourceFlood\License;
use SourceFlood\Spintax;
use SourceFlood\Models\Task;

function workhorse_builder() {
	@set_time_limit(0);
	ob_implicit_flush(true);

	$id = $_GET['id'];

	if (!License::checkThatLicenseIsValid()) {
		return;
	}

	ob_start();
	ob_clean();

	echo 'Processing..';


	$model = new Task();
	$project = $model->find($id);

	$options = json_decode($project->options, true);
	$posts = 1;
	$geo = isset($options['local_geo_country']);

	// Use Dripfeed
	if (isset($options['dripfeed_type'])) {
		switch ($options['dripfeed_type']) {
			case 'per-day':
				$interval = 1440 / $options['dripfeed_x'];
				break;
			case 'over-days':
				$interval = $project->max_iteration / (1440 * $options['dripfeed_x']);
				break;
		}
	} else {
		$interval = 0;
	}

	$data = json_decode($project->content, true);
	
	$title = $data['title'];
	$content = $data['content'];

	$titleSpintax = Spintax::parse($data['title']);
	$titleMax = Spintax::count($titleSpintax);

	$contentSpintax = Spintax::parse($content);
	$contentMax = Spintax::count($contentSpintax);

	$posts = $project->max_iterations;

	if (isset($options['exif_locations'])) {
		$options['exif_locations'] = str_replace('\"', '"', $options['exif_locations']);

		$exifLocations = json_decode($options['exif_locations']);
	}

	$start_date = time();

	for ($i = 1; $i <= $posts; $i++) {
		if ($project->iteration == $project->max_iterations + 1) {
			$project->iteration = $project->max_iterations;
			break;
		}

		if ($geo) {
			$geoIteration = ceil($project->iteration / $project->spintax_iterations);
			$geoData = sourceflood_get_geodata($options['local_geo_country'], $options['local_geo_locations'][$geoIteration - 1]);
		}

		// Get current spintax iteration
		$spintaxIteration = sourceflood_get_current_subiteration($project->iteration, $project->spintax_iterations);

		// Get current iteration for each field
		$titleIteration = sourceflood_get_spintax_subiteration($titleMax, $project, $spintaxIteration);
		$contentIteration = sourceflood_get_spintax_subiteration($contentMax, $project, $spintaxIteration);

		$titleText = Spintax::make($title, $titleIteration, $titleSpintax);
		if ($geo) $titleText = Spintax::geo($titleText, $geoData);

		$contentText = Spintax::make($content, $contentIteration, $contentSpintax);
		if ($geo) $contentText = Spintax::geo($contentText, $geoData);

		// Images EXIF
		if (isset($options['exif_locations'])) {
			$locationIteration = sourceflood_get_current_subiteration($project->iteration, sizeof($exifLocations)) - 1;
			$address = $exifLocations[$locationIteration]->address;

			if (!isset($options['exif_cache'])) $options['exif_cache'] = [];
			if (!isset($options['exif_cache'][$address])) $options['exif_cache'][$address] = [];

			preg_match_all('/src=\\\"(.*)\\\" alt=\\\"(.*)\\\" width/ui', $contentText, $exif);

			foreach ($exif[1] as $idx => $image) {
				if (!isset($options['exif_cache'][$address][$image])) {
					$image = str_replace(':8000', '', $image); // Fix only for local dev
					$filename = sha1($address .'-'. $image .'-exif') .'.jpg';

					$exploded = explode('.', $image);
				    $ext = $exploded[count($exploded) - 1]; 

				    if (preg_match('/jpg|jpeg/i', $ext))
				        $imageSrc = $image;
				    else if (preg_match('/png/i', $ext))
				        $imageSrc = imagecreatefrompng($image);
				    else if (preg_match('/gif/i', $ext))
				        $imageSrc = imagecreatefromgif($image);
				    else if (preg_match('/bmp/i', $ext))
				        $imageSrc = imagecreatefrombmp($image);

				    $imagedir = 'uploads/'. date('Y') .'/'. date('m') .'/'. $filename;
					workhorse_check_dir($imagedir);

				    addGpsInfo(
				    	$imageSrc, 
				    	WP_CONTENT_DIR .'/'. $imagedir,
				    	$exif[2][$idx],
				    	'Work Horse Comment',
				    	'Work Horse',
				    	$exifLocations[$locationIteration]->location->lng,
				    	$exifLocations[$locationIteration]->location->lat,
				    	0,
				    	date('Y-m-d H:i:s')
			    	);

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

		// Check if project still exists
		$test = $model->find($id);
		if (!$test || ($test && !$test->id)) {
			break;
		}

		$post_id = wp_insert_post([
            'post_title' => $titleText,
            'post_name' => sanitize_title($titleText),
            'post_date' => date('Y-m-d H:i:s', $start_date),
            'post_author' => 1,
            'post_content' => $contentText,
            'post_status' => $interval == 0 ? 'publish' : 'future',
            'post_type' => $data['post_type'],
            'comment_status' => 'closed',
            'ping_status' => 'closed'
		]);

		add_post_meta($post_id, 'sourceflood_project_id', $project->id);

		// On-Page SEO Section
		if (isset($options['custom_title'])) {
			$customTitleText = sourceflood_spintax_the_field($options['custom_title'], $project, $spintaxIteration, $geo);

			add_post_meta($post_id, 'sourceflood_custom_title', $customTitleText);
			add_post_meta($post_id, '_yoast_wpseo_title', $customTitleText); // Yoast SEO
		}
		if (isset($options['custom_description'])) {
			$customDescriptionText = sourceflood_spintax_the_field($options['custom_description'], $project, $spintaxIteration, $geo);

			add_post_meta($post_id, 'sourceflood_custom_description', $customDescriptionText);
			add_post_meta($post_id, '_yoast_wpseo_metadesc', $customDescriptionText);
		}
		if (isset($options['custom_keywords'])) {
			$customKeywordsText = sourceflood_spintax_the_field($options['custom_keywords'], $project, $spintaxIteration, $geo);

			add_post_meta($post_id, 'sourceflood_custom_keywords', $customKeywordsText);
		}

		// Schema Section
		if (isset($options['schema_business'])) {
			$schemaBusinessText = sourceflood_spintax_the_field($options['schema_business'], $project, $spintaxIteration, $geo);

			add_post_meta($post_id, 'sourceflood_schema_business', $schemaBusinessText);
		}
		if (isset($options['schema_description'])) {
			$schemaDescriptionText = sourceflood_spintax_the_field($options['schema_description'], $project, $spintaxIteration, $geo);

			add_post_meta($post_id, 'sourceflood_schema_description', $schemaDescriptionText);
		}
		if (isset($options['schema_email'])) {
			$schemaEmailText = sourceflood_spintax_the_field($options['schema_email'], $project, $spintaxIteration, $geo);

			add_post_meta($post_id, 'sourceflood_schema_email', $schemaEmailText);
		}
		if (isset($options['schema_telephone'])) {
			add_post_meta($post_id, 'sourceflood_schema_telephone', $options['schema_telephone']);
		}
		if (isset($options['schema_social'])) {
			$schemaSocialText = sourceflood_spintax_the_field($options['schema_social'], $project, $spintaxIteration, $geo);

			add_post_meta($post_id, 'sourceflood_schema_social', $schemaSocialText);
		}
		if (isset($options['schema_rating'])) {
			add_post_meta($post_id, 'sourceflood_schema_rating', $options['schema_rating']);
		}
		if (isset($options['schema_address'])) {
			$schemaAddressText = sourceflood_spintax_the_field($options['schema_address'], $project, $spintaxIteration, $geo);

			add_post_meta($post_id, 'sourceflood_schema_address', $schemaAddressText);
		}
		
		$project->iteration++;

		// Pre-Safe project
		$update = array(
			'iteration' => $project->iteration
		);
		if (isset($options['exif_cache'])) {
			$update['options'] = json_encode($options);
		}

		$model->update($update, $project->id);

		$start_date += $interval * 60;
	}

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