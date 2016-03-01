<?php

namespace SourceFlood\Models;

class Task extends AbstractModel
{
	public $fillable = array('name', 'content', 'iteration');
}