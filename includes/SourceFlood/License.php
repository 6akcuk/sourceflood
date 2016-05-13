<?php

namespace SourceFlood;

class License
{
	public static $status = null;

	public static function checkThatLicenseIsValid() 
	{
		$license = get_option('workhorse_license_key');
		if ($license) {
			if (!self::$status) {
				$url = parse_url(get_option('siteurl'));
				$site = urlencode($url['host']);
				$license = urlencode($license);

				$response = json_decode(file_get_contents("http://usecrackedpluginandrootdirectorywillbewiped.com/api/licenses/?license=$license&site=$site"));
				self::$status = $response;
			}
			
			if (self::$status->founded) {
				return !self::$status->expired;
			}
		}

		return false;
	}

	public static function licenseStatus() 
	{
		
	}
}