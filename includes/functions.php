<?php

use SourceFlood\Spintax;

function sourceflood_configured() {
	return defined('DISABLE_WP_CRON');
}

/**
 * Get current iteration for field.
 */
function sourceflood_get_current_subiteration($current, $submax) {
	$double = $current / $submax;
	if (strstr($double, '.')) $double -= floor($double);
	else $double = 1;

	return $submax * $double;
}

/**
 * Get current spintax iteration for field.
 */
function sourceflood_get_spintax_subiteration($max, $project, $iteration) {
	return $max < $project->spintax_iterations ? sourceflood_get_current_subiteration($iteration, $max) : $iteration;
}

/**
 * Get geo information from geopath.
 */
function sourceflood_get_geodata($country, $geopath) {
	global $wpdb;

	$path = explode('/', $geopath);
	$result = array('country' => '', 'state' => '', 'stateshort' => '', 'city' => '', 'zip' => '');

	if ($country == 'us') {
		$result['country'] = 'United States';

		$state = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}sourceflood_us_states WHERE state_code = '". $path[0] ."'");
		$result['state'] = $state->state;
		$result['stateshort'] = $state->state_code;

		if (isset($path[1])) {
			$city = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}sourceflood_us_cities WHERE id = ". $path[1]);
			$result['city'] = $city->city;
		}
		if (isset($path[2])) $result['zip'] = $path[2];
	}
	elseif ($country == 'uk') {
		$result['country'] = 'United Kingdom';

		$state = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}sourceflood_uk_states WHERE id = ". $path[0]);
		$result['state'] = $state->name;
		//$result['stateshort'] = $state['state_code'];

		if (isset($path[1])) {
			$city = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}sourceflood_uk_cities WHERE id = ". $path[1]);
			$result['city'] = $city->name;
		}
		if (isset($path[2])) $result['zip'] = $path[2];
	}

	return $result;
}

function sourceflood_spintax_the_field($value, $project, $spintaxIteration, $geo = false) {
	$spintax = Spintax::parse($value);
	$max = Spintax::count($spintax);

	$iteration = sourceflood_get_spintax_subiteration($max, $project, $spintaxIteration);

	$text = Spintax::make($value, $iteration, $spintax);
	if ($geo) $text = Spintax::geo($text, $geoData);

	return $text;
}

if (!function_exists('isJSON')) {
	function isJSON($string){
		return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
	}
}