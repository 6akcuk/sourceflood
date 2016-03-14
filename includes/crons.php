<?php

use Carbon\Carbon;
use SourceFlood\Spintax;
use SourceFlood\Models\Task;

// Add every minute schedule
function sourceflood_add_every_minute($schedules) {
     $schedules['every_minute'] = array(
        'interval'  => 60,
        'display'   => __( 'Every Minute', 'textdomain' )
    );
     
    return $schedules;
}
add_filter('cron_schedules', 'sourceflood_add_every_minute');


// Schedules
wp_schedule_event(time(), 'every_minute', 'sourceflood_parse_tasks');

function sourceflood_parse_tasks() {
	global $wpdb;

	$model = new Task();

	$projects = $model->getActive();
	foreach ($projects as $project) {
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
			$interval = 2 / 30;
		}

		if ($interval < 2) {
			$posts = floor(2 / $interval);
		}

		//$spintax = Spintax::parse($content->content);

		$last_update = Carbon::parse($project->updated_at);
		$now = Carbon::now();

		// Time to post
		if ($now->diffInMinutes($last_update) >= $interval) {
			$data = json_decode($project->content, true);

			$title = $data['title'];
			$content = $data['content'];

			$titleSpintax = Spintax::parse($data['title']);
			$titleMax = Spintax::count($titleSpintax);

			$contentSpintax = Spintax::parse($content);
			$contentMax = Spintax::count($contentSpintax);

			$posts = min($posts, $project->max_iterations);

			for ($i = 1; $i <= $posts; $i++) {
				if ($project->iteration == $project->max_iterations + 1) break;

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
				
				$post_id = wp_insert_post([
		            'post_title' => $titleText,
		            'post_name' => sanitize_title($titleText),
		            'post_author' => 1,
		            'post_content' => $contentText,
		            'post_status' => 'publish',
		            'post_type' => $data['post_type'],
		            'comment_status' => 'closed',
		            'ping_status' => 'closed'
				]);

				add_post_meta($post_id, 'sourceflood_project_id', $project->id);

				// On-Page SEO Section
				if (isset($options['custom_title'])) {
					$customTitleText = sourceflood_spintax_the_field($options['custom_title'], $project, $spintaxIteration, $geo);

					add_post_meta($post_id, 'sourceflood_custom_title', $customTitleText);
				}
				if (isset($options['custom_description'])) {
					$customDescriptionText = sourceflood_spintax_the_field($options['custom_description'], $project, $spintaxIteration, $geo);

					add_post_meta($post_id, 'sourceflood_custom_description', $customDescriptionText);
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
			}

			// Save project changes
			$update = array(
				'iteration' => $project->iteration
			);

			if ($project->iteration == $project->max_iterations) {
				$update['finished_at'] = date('Y-m-d H:i:s');
			}

			$model->update($update, $project->id);
		}
	}
}
add_action('sourceflood_parse_tasks', 'sourceflood_parse_tasks');