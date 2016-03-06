<?php

namespace SourceFlood\Validator;

class Required extends BaseValidator
{
	public static function validate($data, $field)
	{
		return isset($data[$field]) && !empty($data[$field]) ? true : _(self::fieldName($field) ." is required");
	}
}