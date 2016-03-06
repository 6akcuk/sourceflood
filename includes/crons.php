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

				if (isset($options['custom_title'])) {
					$customtitleSpintax = Spintax::parse($options['custom_title']);
					$customtitleMax = Spintax::count($customtitleSpintax);

					$customtitleIteration = sourceflood_get_spintax_subiteration($customtitleMax, $project, $spintaxIteration);

					$customtitleText = Spintax::make($options['custom_title'], $customtitleIteration, $customtitleSpintax);
					if ($geo) $customtitleText = Spintax::geo($customtitleText, $geoData);

					add_post_meta($post_id, 'sourceflood_custom_title', $customtitleText);
				}
				if (isset($options['custom_description'])) {
					$descriptionSpintax = Spintax::parse($options['custom_description']);
					$descriptionMax = Spintax::count($descriptionSpintax);

					$descriptionIteration = sourceflood_get_spintax_subiteration($descriptionMax, $project, $spintaxIteration);

					$descriptionText = Spintax::make($options['custom_description'], $descriptionIteration, $descriptionSpintax);
					if ($geo) $descriptionText = Spintax::geo($descriptionText, $geoData);

					add_post_meta($post_id, 'sourceflood_custom_description', $descriptionText);
				}
				if (isset($options['custom_keywords'])) {
					$keywordsSpintax = Spintax::parse($options['custom_keywords']);
					$keywordsMax = Spintax::count($keywordsSpintax);

					$keywordsIteration = sourceflood_get_spintax_subiteration($keywordsMax, $project, $spintaxIteration);

					$keywordsText = Spintax::make($options['custom_keywords'], $keywordsIteration, $keywordsSpintax);
					if ($geo) $keywordsText = Spintax::geo($keywordsText, $geoData);

					add_post_meta($post_id, 'sourceflood_custom_keywords', $keywordsText);
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