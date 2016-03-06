<?php

namespace SourceFlood\Validator;

class Numeric extends BaseValidator
{
	public static function validate($data, $field)
	{
		return !isset($data[$field]) || (isset($data[$field]) && empty($data[$field])) || (isset($data[$field]) && !empty($data[$field]) && preg_match("/^[0-9\.\,+\-]+$/", $data[$field])) ? true : _(self::fieldName($field) ." must be numeric");
	}
}