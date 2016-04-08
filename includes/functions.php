<?php

use SourceFlood\Spintax;
use lsolesen\pel\PelIfd;
use lsolesen\pel\PelTag;
use lsolesen\pel\PelExif;
use lsolesen\pel\PelJpeg;
use lsolesen\pel\PelTiff;
use lsolesen\pel\PelEntryByte;
use lsolesen\pel\PelEntryAscii;
use lsolesen\pel\PelEntryRational;
use lsolesen\pel\PelEntryUserComment;

function sourceflood_configured() {
	return defined('DISABLE_WP_CRON');
}

function workhorse_permalink($previous = null) {
	$permalink = get_option('permalink_structure');

	$rewritecode = array(
		'%year%',
		'%monthnum%',
		'%day%',
		'%hour%',
		'%minute%',
		'%second%',
		'%postname%',
		'%post_id%',
		'%category%',
		'%author%',
		'%pagename%',
	);

	$date = explode(" ",date('Y m d H i s', time()));
	$rewritereplace =
	array(
		$date[0],
		$date[1],
		$date[2],
		$date[3],
		$date[4],
		$date[5],
		'@title',
		'@id',
		'@category',
		'@author',
		'@title',
	);

	$permalink = home_url( str_replace($rewritecode, $rewritereplace, $permalink) );

	// Add span tag
	$permalink = str_replace('@title', '<span id="permalink">@title</span>', $permalink);

	if ($previous) {
		$permalink = str_replace('@title', $previous, $permalink);
	}

	return $permalink;
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

function workhorse_search_geotags($fields) {
	$tags = array();

	foreach ($fields as $field) {
		if (sizeof($tags) == 4) break;

		preg_match_all("/(@zip|@city|@stateshort|@state)/", $field, $matches);

		if (isset($matches[1])) {
			if (!is_array($matches[1])) $matches[1] = array($matches[1]);
			foreach ($matches[1] as $match) {
				$tag = str_replace('@', '', $match);
				if (!in_array($tag, $tags)) $tags[] = $tag;
			}
		}
	}

	$tags = array_unique($tags);
	return $tags;
}

function workhorse_expand_geodata($country, $geodata, $tags) {
	global $wpdb;

	sort($geodata);
	$tweaked = [];

	foreach ($geodata as $key => $loc) {
		if ($country == 'us') {
			// Only state
			if (preg_match("/^[A-z]{2}$/", $loc)) {
				if (in_array('city', $tags) || in_array('zip', $tags)) {
					if ((isset($geodata[$key + 1]) && !preg_match("/^$loc/", $geodata[$key + 1])) || !isset($geodata[$key + 1])) {
						$cities = $wpdb->get_results("SELECT id, zip FROM {$wpdb->prefix}sourceflood_us_cities WHERE state_code = '$loc' AND 1=1". (!in_array('zip', $tags) ? ' GROUP BY city, county' : ''));

						foreach ($cities as $city) {
							if (in_array('zip', $tags))	$tweaked[] = "$loc/{$city->id}/{$city->zip}";
							elseif (in_array('city', $tags)) $tweaked[] = "$loc/{$city->id}";
						}
					}
				} else {
					$tweaked[] = $loc;
				}
			}
			// Only city
			elseif (preg_match("/^([A-z]{2})\/(\d+)$/", $loc, $loccy)) {
				if (in_array('zip', $tags)) {
					if ((isset($geodata[$key + 1]) && !preg_match("/^$loccy[1]\/$loccy[2]/", $geodata[$key + 1])) || !isset($geodata[$key + 1])) {
						$city = $wpdb->get_row("SELECT city FROM {$wpdb->prefix}sourceflood_us_cities WHERE id = {$loccy[2]}");
						$zippy = $wpdb->get_results("SELECT zip FROM {$wpdb->prefix}sourceflood_us_cities WHERE state_code = '$loccy[1]' AND city = '{$city->city}' AND 1=1");

						foreach ($zippy as $zip) {
							$tweaked[] = "$loc/{$zip->zip}";
						}
					}
				} else {
					$tweaked[] = $loc;
				}
			}
			// Everything else
			else {
				$parts = explode("/", $loc);

				if (!in_array('zip', $tags) && !in_array('city', $tags)) $tweaked[] = $parts[0];
				elseif (!in_array('zip', $tags) && in_array('city', $tags)) $tweaked[] = "$parts[0]/$parts[1]";
				else $tweaked[] = $loc;
			}
		}
		elseif ($country == 'uk') {
			// Only state
			if (preg_match("/^\d+$/", $loc)) {
				if (in_array('city', $tags) || in_array('zip', $tags)) {
					if ((isset($geodata[$key + 1]) && !preg_match("/^$loc/", $geodata[$key + 1])) || !isset($geodata[$key + 1])) {
						$cities = $wpdb->get_results("SELECT id, postcode FROM {$wpdb->prefix}sourceflood_uk_cities WHERE region_id = '$loc' AND 1=1". (!in_array('zip', $tags) ? ' GROUP BY name' : ''));

						foreach ($cities as $city) {
							if (in_array('zip', $tags))	$tweaked[] = "$loc/{$city->id}/{$city->postcode}";
							elseif (in_array('city', $tags)) $tweaked[] = "$loc/{$city->id}";
						}
					}
				} else {
					$tweaked[] = $loc;
				}
			}
			// Only city
			elseif (preg_match("/^(\d+)\/(\d+)$/", $loc, $loccy)) {
				if (in_array('zip', $tags)) {
					if ((isset($geodata[$key + 1]) && !preg_match("/^$loccy[1]\/$loccy[2]/", $geodata[$key + 1])) || !isset($geodata[$key + 1])) {
						$city = $wpdb->get_row("SELECT name FROM {$wpdb->prefix}sourceflood_uk_cities WHERE id = {$loccy[2]}");
						$zippy = $wpdb->get_results("SELECT postcode FROM {$wpdb->prefix}sourceflood_uk_cities WHERE region_id = '$loccy[1]' AND name = '{$city->name}' AND 1=1");

						foreach ($zippy as $zip) {
							$tweaked[] = "$loc/{$zip->postcode}";
						}
					}
				} else {
					$tweaked[] = $loc;
				}
			}
			// Everything else
			else {
				$parts = explode("/", $loc);

				if (!in_array('zip', $tags) && !in_array('city', $tags)) $tweaked[] = $parts[0];
				elseif (!in_array('zip', $tags) && in_array('city', $tags)) $tweaked[] = "$parts[0]/$parts[1]";
				else $tweaked[] = $loc;
			}
		}
	}

	// Remove non-used parts of locations
	$tweaked = array_unique($tweaked);

	return $tweaked;
}

/**
 * Get geo information from geopath.
 */
function sourceflood_get_geodata($country, $geopath) {
	global $wpdb;

	$path = explode('/', $geopath);
	$result = array('country' => '', 'countryshort' => '', 'state' => '', 'stateshort' => '', 'city' => '', 'zip' => '');

	if ($country == 'us') {
		$result['country'] = 'United States';
		$result['countryshort'] = 'US';

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
		$result['countryshort'] = 'UK';

		$state = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}sourceflood_uk_states WHERE id = ". $path[0]);
		$result['state'] = $state->name;
		$result['stateshort'] = $state->name;

		if (isset($path[1])) {
			$city = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}sourceflood_uk_cities WHERE id = ". $path[1]);
			$result['city'] = $city->name;
		}
		if (isset($path[2])) $result['zip'] = $path[2];
	}

	return $result;
}

function sourceflood_spintax_the_field($value, $project, $spintaxIteration, $geo = false, $geoData = null) {
	$spintax = Spintax::parse($value);
	$max = Spintax::count($spintax);

	$iteration = sourceflood_get_spintax_subiteration($max, $project, $spintaxIteration);

	$text = Spintax::make($value, $iteration, $spintax);
	if ($geo) $text = Spintax::geo($text, $geoData);

	return $text;
}

function workhorse_check_dir($dir) {
	$dirs = explode("/", $dir);
	$check = WP_CONTENT_DIR;

	foreach ($dirs as $dir) {
		if (strstr($dir, '.')) continue;

		if (!$check) $check = $dir;
		else $check .= "/". $dir;

		if (!is_dir($check)) mkdir($check);
	}
}

if (!function_exists('isJSON')) {
	function isJSON($string){
		return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
	}
}

if (!function_exists('convertDecimalToDMS')) {
	/**
	 * Convert a decimal degree into degrees, minutes, and seconds.
	 *
	 * @param
	 *            int the degree in the form 123.456. Must be in the interval
	 *            [-180, 180].
	 *
	 * @return array a triple with the degrees, minutes, and seconds. Each
	 *         value is an array itself, suitable for passing to a
	 *         PelEntryRational. If the degree is outside the allowed interval,
	 *         null is returned instead.
	 */
	function convertDecimalToDMS($degree)
	{
	    if ($degree > 180 || $degree < - 180) {
	        return null;
	    }

	    $degree = abs($degree); // make sure number is positive
	                            // (no distinction here for N/S
	                            // or W/E).

	    $seconds = $degree * 3600; // Total number of seconds.

	    $degrees = floor($degree); // Number of whole degrees.
	    $seconds -= $degrees * 3600; // Subtract the number of seconds
	                                 // taken by the degrees.

	    $minutes = floor($seconds / 60); // Number of whole minutes.
	    $seconds -= $minutes * 60; // Subtract the number of seconds
	                               // taken by the minutes.

	    $seconds = round($seconds * 100, 0); // Round seconds with a 1/100th
	                                         // second precision.

	    return array(
	        array(
	            $degrees,
	            1
	        ),
	        array(
	            $minutes,
	            1
	        ),
	        array(
	            $seconds,
	            100
	        )
	    );
	}
}

if (!function_exists('addGpsInfo')) {
	/**
	 * Add GPS information to an image basic metadata.
	 * Any old Exif data
	 * is discarded.
	 *
	 * @param
	 *            string the input filename.
	 *
	 * @param
	 *            string the output filename. An updated copy of the input
	 *            image is saved here.
	 *
	 * @param
	 *            string image description.
	 *
	 * @param
	 *            string user comment.
	 *
	 * @param
	 *            string camera model.
	 *
	 * @param
	 *            float longitude expressed as a fractional number of degrees,
	 *            e.g. 12.345пїЅ. Negative values denotes degrees west of Greenwich.
	 *
	 * @param
	 *            float latitude expressed as for longitude. Negative values
	 *            denote degrees south of equator.
	 *
	 * @param
	 *            float the altitude, negative values express an altitude
	 *            below sea level.
	 *
	 * @param
	 *            string the date and time.
	 */
	function addGpsInfo($input, $output, $description, $comment, $model, $longitude, $latitude, $altitude, $date_time) {
	    /* Load the given image into a PelJpeg object */
	    $jpeg = new PelJpeg($input);

	    /*
	     * Create and add empty Exif data to the image (this throws away any
	     * old Exif data in the image).
	     */
	    $exif = new PelExif();
	    $jpeg->setExif($exif);

	    /*
	     * Create and add TIFF data to the Exif data (Exif data is actually
	     * stored in a TIFF format).
	     */
	    $tiff = new PelTiff();
	    $exif->setTiff($tiff);

	    /*
	     * Create first Image File Directory and associate it with the TIFF
	     * data.
	     */
	    $ifd0 = new PelIfd(PelIfd::IFD0);
	    $tiff->setIfd($ifd0);

	    /*
	     * Create a sub-IFD for holding GPS information. GPS data must be
	     * below the first IFD.
	     */
	    $gps_ifd = new PelIfd(PelIfd::GPS);
	    $ifd0->addSubIfd($gps_ifd);

	    /*
	     * The USER_COMMENT tag must be put in a Exif sub-IFD under the
	     * first IFD.
	     */
	    $exif_ifd = new PelIfd(PelIfd::EXIF);
	    $exif_ifd->addEntry(new PelEntryUserComment($comment));
	    $ifd0->addSubIfd($exif_ifd);

	    $inter_ifd = new PelIfd(PelIfd::INTEROPERABILITY);
	    $ifd0->addSubIfd($inter_ifd);

	    $ifd0->addEntry(new PelEntryAscii(PelTag::MODEL, $model));
	    $ifd0->addEntry(new PelEntryAscii(PelTag::DATE_TIME, $date_time));
	    $ifd0->addEntry(new PelEntryAscii(PelTag::IMAGE_DESCRIPTION, $description));

	    $gps_ifd->addEntry(new PelEntryByte(PelTag::GPS_VERSION_ID, 2, 2, 0, 0));

	    /*
	     * Use the convertDecimalToDMS function to convert the latitude from
	     * something like 12.34пїЅ to 12пїЅ 20' 42"
	     */
	    list ($hours, $minutes, $seconds) = convertDecimalToDMS($latitude);

	    /* We interpret a negative latitude as being south. */
	    $latitude_ref = ($latitude < 0) ? 'S' : 'N';

	    $gps_ifd->addEntry(new PelEntryAscii(PelTag::GPS_LATITUDE_REF, $latitude_ref));
	    $gps_ifd->addEntry(new PelEntryRational(PelTag::GPS_LATITUDE, $hours, $minutes, $seconds));

	    /* The longitude works like the latitude. */
	    list ($hours, $minutes, $seconds) = convertDecimalToDMS($longitude);
	    $longitude_ref = ($longitude < 0) ? 'W' : 'E';

	    $gps_ifd->addEntry(new PelEntryAscii(PelTag::GPS_LONGITUDE_REF, $longitude_ref));
	    $gps_ifd->addEntry(new PelEntryRational(PelTag::GPS_LONGITUDE, $hours, $minutes, $seconds));

	    /*
	     * Add the altitude. The absolute value is stored here, the sign is
	     * stored in the GPS_ALTITUDE_REF tag below.
	     */
	    $gps_ifd->addEntry(new PelEntryRational(PelTag::GPS_ALTITUDE, array(
	        abs($altitude),
	        1
	    )));
	    /*
	     * The reference is set to 1 (true) if the altitude is below sea
	     * level, or 0 (false) otherwise.
	     */
	    $gps_ifd->addEntry(new PelEntryByte(PelTag::GPS_ALTITUDE_REF, (int) ($altitude < 0)));

	    /* Finally we store the data in the output file. */
	    file_put_contents($output, $jpeg->getBytes());
	}
}