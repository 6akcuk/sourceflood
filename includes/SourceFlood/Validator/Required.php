<?php

namespace SourceFlood\Validator;

class Required implements IValidator
{
	public static function validate($data, $field)
	{
		return isset($data[$field]) && !empty($data[$field]) ? true : _(ucfirst($field) ." is required");
	}
}