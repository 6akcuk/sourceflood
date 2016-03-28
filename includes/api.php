<?php

use SourceFlood\View;
use SourceFlood\Validator;

if (isset($_GET['api']) && $_GET['api'] == 'workhorse') {
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
    elseif ($act == 'shortcode') {
    	if (!Validator::validate($_POST, array(
			'shortcode' => 'required',
			'images' => 'required'
		))) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    		exit;
		}

    	$shortcode = $_POST['shortcode'];
    	$images = json_encode($_POST['images']);

    	$sql = $wpdb->prepare("INSERT INTO {$wpdb->prefix}sourceflood_shortcodes (shortcode, type, content) VALUES (%s, %s, %s)", array($shortcode, 'static', $images));

    	$wpdb->query($sql);

    	$results['success'] = 1;
    }
    elseif ($act == 'exif') {
    	View::render('exif.index');

    	exit;
    }
    elseif ($act == 'word-ai') {
    	$text = urlencode($_POST['text']);
    	$quality = $_POST['quality'];
    	$email = urlencode($_POST['email']);
    	$hash = $_POST['hash'];
    	
		$ch = curl_init('http://wordai.com/users/turing-api.php');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, "s=$text&quality=$quality&email=$email&hash=$hash&output=json");

		$results = json_decode(curl_exec($ch), true);
		curl_close ($ch);
    }

    header('Content-Type: application/json');

    echo json_encode($results);
    exit;
}