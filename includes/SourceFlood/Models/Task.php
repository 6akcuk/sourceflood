<?php

namespace SourceFlood\Models;

class Task extends AbstractModel
{
	public $fillable = array('name', 'content', 'options', 'iteration', 'spintax_iterations', 'max_iterations', 'finished_at');

	public function getActive() 
	{
		global $wpdb;

		return $wpdb->get_results("SELECT * FROM ". $this->getTable() ." WHERE iteration < max_iterations");
	}
}