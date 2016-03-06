<?php

if (isset($_GET['api']) && $_GET['api'] == 'sourceflood') {
    $act = $_GET['action'];
    $results = array();

    // Generate JSON data for GEO Tree
    if ($act == 'geo-tree') {
    	$country = $_GET['country'];

    	// Show US Data
    	if ($country == 'us') {
    		if (isset($_GET['id']) && $_GET['id'] != '#') {
    			$id = urldecode($_GET['id']);

    			// Show zip codes
    			if (substr_count($id, '/') == 1) {
    				list($state, $city) = explode('/', $id);
    				$city_obj = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}sourceflood_us_cities WHERE id = ". $city);

    				$codes = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sourceflood_us_cities WHERE county = '{$city_obj->county}' AND state_code = '{$city_obj->state_code}' AND city = '{$city_obj->city}' ORDER BY zip");
	    			
	    			foreach ($codes as $code) {
	    				$results[] = array(
	    					'id' => $state .'/'. $city .'/'. $code->zip,
	    					'text' => $code->zip,
	    					'children' => false
						);
	    			}
    			}
    			// Show cities
    			else {
    				$cities = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sourceflood_us_cities WHERE state_code = '$id' GROUP BY county, state_code, city ORDER BY city");

	    			foreach ($cities as $city) {
	    				$results[] = array(
	    					'id' => $id . '/' . $city->id,
	    					'text' => $city->city,
	    					'children' => true
						);
	    			}
	    		}
    		} 
    		// Show states
    		else {
	    		$states = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sourceflood_us_states");
	    		foreach ($states as $state) {
	    			$results[] = array(
						'id' => $state->state_code,
						'text' => $state->state,
						'children' => true
					);
	    		}
	    	}
    	}
    	// Show UK Data
    	elseif ($country == 'uk') {
    		if (isset($_GET['id']) && $_GET['id'] != '#') {
    			$id = urldecode($_GET['id']);

    			// Show zip codes
    			if (substr_count($id, '/')) {
    				list($state, $city) = explode('/', $id);
    				$city_obj = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}sourceflood_uk_cities WHERE id = $city");

    				$codes = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sourceflood_uk_cities WHERE region_id = '{$city_obj->region_id}' AND name = '{$city_obj->name}' ORDER BY postcode");
	    			
	    			foreach ($codes as $code) {
	    				$results[] = array(
	    					'id' => $state .'/'. $city .'/'. $code->postcode,
	    					'text' => $code->postcode,
	    					'children' => false
						);
	    			}
    			}
    			// Show cities
    			else {
	    			$cities = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sourceflood_uk_cities WHERE region_id = '". $id ."' GROUP BY region_id, name ORDER BY name");

	    			foreach ($cities as $city) {
	    				$results[] = array(
	    					'id' => $id .'/'. $city->id,
	    					'text' => $city->name,
	    					'children' => true
						);
	    			}
	    		}
    		} 
    		// Show states
    		else {
	    		$states = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}sourceflood_uk_states ORDER BY name");
	    		foreach ($states as $state) {
	    			$results[] = array(
						'id' => $state->id,
						'text' => $state->name .' ['. $state->country_short .']',
						'children' => true
					);
	    		}
	    	}
    	}
    }

    header('Content-Type: application/json');

    echo json_encode($results);
    exit;
}