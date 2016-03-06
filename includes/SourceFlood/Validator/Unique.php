<?php

namespace SourceFlood\Validator;

class Unique extends BaseValidator
{
	public function validate($data, $field, $table, $column = null, $except = null) 
	{
		global $wpdb;

		if (!$column) $column = $field;

		$sql = $wpdb->prepare("SELECT id, $field FROM $table WHERE $field = %s", [$data[$field]]);
		$result = $wpdb->get_row($sql);

		return $result && $result->$field && (!$except || ($except && $result->id != $except)) ? _(self::fieldName($field) ." is already exists.") : true;
	}
}