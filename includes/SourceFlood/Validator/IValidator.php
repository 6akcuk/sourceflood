<?php

namespace SourceFlood\Validator;

interface IValidator
{
	public static function validate($data, $field);
}