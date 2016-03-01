<?php

namespace SourceFlood;

class View
{
	public static $sections = [];

	public static function render($template, $args = array())
	{
		extract($args);

		$template = str_replace('.', '/', $template);
		if (!preg_match("/\.php$/", $template)) {
			$template .= '.php';
		}

		include SOURCEFLOOD_ROOT .'/views/'. $template;
	}

	public static function startSection($section)
	{
		ob_start();
	}

	public static function endSection($section) 
	{
		self::$sections[$section] = ob_get_clean();
	}

	public static function section($section)
	{
		return self::$sections[$section];
	}

	public static function make($layout) 
	{
		return View::render($layout);
	}
}