<?php

namespace SourceFlood;

class License
{
	public static $status = null;

	public static function checkThatLicenseIsValid() 
	{
		return true;
		
		$license = get_option('workhorse_license_key');
		if ($license) {
			if (!self::$status) {
				$response = json_decode(file_get_contents("http://workhorselicense.app/api/licenses/$license"));
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