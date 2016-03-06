<?php

namespace SourceFlood;

use SourceFlood\Validator\ValidatorException;

class Validator
{
	public static $errorsKey = 'sourceflood.validator.errors';
	public static $oldKey = 'sourceflood.validator.old';
	
	public static function old($field, $default = null) 
	{
		if (isset($_SESSION[self::$oldKey]) && isset($_SESSION[self::$oldKey][$field])) {
			$value = $_SESSION[self::$oldKey][$field];
			unset($_SESSION[self::$oldKey][$field]);
			
			return $value;
		} 
		else {
			return $default;
		}
	}

	public function saveOld($field, $value) 
	{
		$_SESSION[self::$oldKey][$field] = $value;
	}

	public static function error($field, $message) 
	{
		if (!isset($_SESSION[self::$errorsKey][$field])) $_SESSION[self::$errorsKey][$field] = [];

		$_SESSION[self::$errorsKey][$field][] = $message;
	}

	public static function hasError($field)
	{
		return isset($_SESSION[self::$errorsKey]) && isset($_SESSION[self::$errorsKey][$field]);
	}

	public static function getErrors()
	{
		return $_SESSION[self::$errorsKey];
	}

	public static function get($field) 
	{
		$error = $_SESSION[self::$errorsKey][$field][0];
		unset($_SESSION[self::$errorsKey][$field]);

		return $error;
	}

	public static function validate($data, $rulesList) 
	{
		$_SESSION[self::$errorsKey] = [];
		$success = true;

		foreach ($rulesList as $field => $list) {
			$rules = !is_array($list) ? explode('|', $list) : $list;

			foreach ($rules as $rule) {
				$arguments = array();

				if (stristr($rule, ':')) {
					list($rule, $arguments) = explode(':', $rule);
					$arguments = explode(',', $arguments);
					if (!is_array($arguments)) $arguments = array($arguments);
				}

				$rule = ucwords(str_replace("_", " ", $rule));
				$rule = str_replace(" ", "", $rule);

				$validatorClass = "SourceFlood\Validator\\$rule";

				if (class_exists($validatorClass)) {
					$arguments = array_merge(array($data, $field), $arguments);

					$result = call_user_func_array(array($validatorClass, 'validate'), $arguments);
					//$result = $validatorClass::validate($data, $field);
					if ($result !== true) {
						$success = false;
						
						self::error($field, $result);
					}
				} else {
					throw new ValidatorException("Validator $rule: class $validatorClass doesn't exist");
				}
			}
		}

		// Save old form values
		if (!$success) {
			foreach ($data as $field => $value) {
				self::saveOld($field, $value);
			}
		}

		return $success;
	}
}