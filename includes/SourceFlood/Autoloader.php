<?php

namespace SourceFlood;

class Autoloader
{
	public function __construct() 
	{
		spl_autoload_register(array($this, 'loader'));
	}

	public function loader($className) 
	{
		// Corrections
		$className = str_replace('\\', '/', $className);
		
		$file = SOURCEFLOOD_ROOT .'/includes/'. $className . '.php';

		if (file_exists($file)) include $file;
	}
}