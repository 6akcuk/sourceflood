<?php

namespace SourceFlood\Validator;

class PostType implements IValidator
{
	public static function validate($data, $field) 
	{
		return in_array($data[$field], ['post', 'page']) ? true : _('Not allowed post type');
	}
}