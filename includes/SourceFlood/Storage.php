<?php

namespace SourceFlood;

class Storage
{
	protected $name;

	protected $content = array();

	public function __construct($name) 
	{
		$this->name = $name;
		$this->content = file_exists($this->filename($name)) ? json_decode(file_get_contents($this->filename($name)), true) : array();
	}

	public function store($key, $value) 
	{
		$this->content[$key] = $value;

		file_put_contents($this->filename($this->name), json_encode($this->content));
	}

	public function get($key) 
	{
		return $this->content[$key];
	}

	public function __get($key) 
	{
		if (isset($this->content[$key])) return $this->content[$key];
	}

	public function __set($key, $value) 
	{
		$this->store($key, $value);
	}

	protected function filename($name)
	{
		return SOURCEFLOOD_ROOT . "/storage/$name.dat";
	}
}