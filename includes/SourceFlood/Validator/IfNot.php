<?php

namespace SourceFlood\Validator;

class IfNot
{
	public function validate($data, $field, $value, $check = null) 
	{
		$checker = $check ? $check : $data[$field];

		return !isset($data[$field]) || (isset($data[$field]) && $checker == $value) ? true : _('Value not allowed.');
	}
}