<?php

namespace SourceFlood;

class Spintax
{
	/**
	 * Creates spintax array from text.
	 */
	public static function parse($content, $parent_key = null) 
	{
		preg_match_all("/\{(((?>[^\{\}]+)|(?R))*)\}/x", $content, $spintaxes);
		
		foreach ($spintaxes[0] as $key => $value) {
			$content = str_replace($value, '$$'. $key, $content);
		}

		$template = $content;
		$vars = array();

		// 
		if (strstr($content, '|')) {
			$strokes = explode('|', $content);
			$values = array();

			foreach ($strokes as $value) {
				if (strstr($value, '$$')) {
					preg_match("/\\$\\$(\d+)/i", $value, $varKeys);
					$string = $value;

					foreach ($varKeys as $vark) {
						$string = str_replace("$$". $vark, $spintaxes[0][$vark], $string);
					}

					$values[] = self::parse($string);
				} else {
					$values[] = $value;
				}
			}

			return $values;
		} else {
			foreach ($spintaxes[1] as $key => $value) {
				if (strstr($value, '{')) {
					$parsed = self::parse($value, $key);
					$vars[$key] = $parsed;
				} else {
					$vars[$key] = explode('|', $value);
				}
			}

			return array(
				'template' => $template,
				'vars' => $vars
			);
		}
	}

	/**
	 * Counts available unique texts from spintax.
	 */
	public static function count($spintax, $level = 0) 
	{
		$vars = 0;
		$values = 0;

		$variables = isset($spintax['vars']) ? $spintax['vars'] : $spintax;
		foreach ($variables as $key => $var) {
			if (is_array($var)) {
				if ($level > 0) {
					$vars += self::count($var, $level + 1);
				} else {
					$vars = ($vars == 0 ? 1 : $vars) * self::count($var, $level + 1);
				}
			} else {
				$vars++;
			}
		}

		return $vars;
	}

	/**
	 * Build spintax variables table.
	 */
	public static function build($spintax) 
	{
		$table = array();

		$variables = isset($spintax['vars']) ? $spintax['vars'] : $spintax;
		foreach ($variables as $key => $var) {
			$subitems = array();

			foreach ($var as $key_vr => $vr) {
				if (isset($vr['template'])) {
					$subitems[$key_vr] = self::build($vr);
				}
			}

			$table[] = array(
				'item' => 1,
				'max' => sizeof($var),
				'subitems' => $subitems
			);
		}

		return $table;
	}

	public static function __drop($table) 
	{
		if (!sizeof($table)) return array();

		foreach ($table as &$item) {
			foreach ($item as &$itm) {
				$itm['item'] = 1;
				$itm['subitems'] = self::__drop($itm['subitems']);
			}
		}

		return $table;
	}

	public static function __inc($table, $next) 
	{
		for ($i = $next; $i <= sizeof($table); $i++) {
			if (isset($table[$i]['subitems'][$table[$i]['item'] - 1])) {
				$result = self::__subinc($table[$i]['subitems'][$table[$i]['item'] - 1]);
				if ($result !== false) {
					$table[$i]['subitems'][$table[$i]['item'] - 1] = $result;

					return $table;
				}
			}

			$table[$i]['item']++;

			if ($table[$i]['item'] <= $table[$i]['max']) return $table;
			else {
				$table[$i]['item'] = 1;
				$table[$i]['subitems'] = self::__drop($table[$i]['subitems']);
			}
		}

		return $table;
	}

	public static function __subinc($table) 
	{
		$result = false;

		foreach ($table as $key => &$item) {
			// Digging through subitems
			if (isset($item['subitems'][$item['item'] - 1])) {
				$result = self::__subinc($item['subitems'][$item['item'] - 1]);
				if ($result !== false) {
					$item['subitems'][$item['item'] - 1] = $result;
					return $table;
				}
			}

			if ($item['item'] < $item['max']) {
				$item['item']++;

				return $table;
			}
			else {
				continue;
			}
		}

		return $result;
	}

	/**
	 * Math spintax variables index on table
	 */
	public static function math($spintax, $table, $iteration = 1, $maxlength = null) 
	{
		if ($maxlength && $iteration > $maxlength) {
			throw new \Exception('Maximum number of unique text reached.');
		}

		for ($i = 1; $i < $iteration; $i++) {
			$iterate = true;

			if (isset($table[0]['subitems'][$table[0]['item'] - 1])) {
				$result = self::__subinc($table[0]['subitems'][$table[0]['item'] - 1]);
				if ($result !== false) {
					$iterate = false;
					$table[0]['subitems'][$table[0]['item'] - 1] = $result;
				}
			}

			if ($iterate) {
				$table[0]['item']++;

				if ($table[0]['item'] > $table[0]['max'] && isset($table[1])) {
					$table[0]['item'] = 1;
					$table[0]['subitems'] = self::__drop($table[0]['subitems']);

					$table = self::__inc($table, 1);
				}
			}
		}

		return $table;
	}

	public function make($content, $iteration, $spintax = null) 
	{
		$spintax = $spintax ? $spintax : self::parse($content);
		$table = self::build($spintax);
		$max = self::count($spintax);
		$math = self::math($spintax, $table, $iteration, $max);

		return self::renderTemplate($spintax['template'], $spintax['vars'], $math);
	}

	/**
	 * Renders text from template.
	 */
	public static function renderTemplate($template, $vars, $math) 
	{
		preg_match_all("/\\$\\$(\d+)/i", $template, $keys);

		if (sizeof($keys[1]) > 0) {
			foreach ($keys[1] as $key) {
				$renderValue = $vars[$key][$math[$key]['item'] - 1];

				if (is_array($renderValue) && isset($renderValue['template'])) {
					$renderValue = self::renderTemplate($renderValue['template'], $renderValue['vars'], $math[$key]['subitems'][ $math[$key]['item'] - 1]);
				}

				$template = str_replace("$$$key", $renderValue, $template);
			}
		}
		
		return $template;
	}



	/*public static function build($spintax) 
	{
		$table = array();

		if (isset($spintax['vars'])) {
			foreach ($spintax['vars'] as $key => $var) {
				if (is_array($var)) {
					$table = array_merge($table, self::build($var));
				} else {
					$table[] = array('item' => 1, 'max' => sizeof($spintax));
				}
			}
		} else {
			foreach ($spintax as $key => $var) {
				if (is_array($var)) {
					$table = array_merge($table, self::build($var));
				} else {
					$table[] = array('item' => 1, 'max' => sizeof($spintax));
				}
			}
		}

		return $table;
	}*/

	/*public static function math($spintax, $table, $iteration = 1) 
	{
		function __inc($table, $next) {
			for ($i = $next; $i <= sizeof($table); $i++) {
				$table[$i]['item']++;

				if ($table[$i]['item'] <= $table[$i]['max']) return $table;
				else $table[$i]['item'] = 1;
			}

			return $table;
		}

		for ($i = 1; $i < $iteration; $i++) {
			$table[0]['item']++;

			if ($table[0]['item'] > $table[0]['max'] && isset($table[1])) {
				$table[0]['item'] = 1;

				$table = __inc($table, 1);
			}
		}

		return $table;
	}

	public static function make($spintax, $phraseTable, $vars = 0) 
	{
		$phrase = $spintax['template'];

		foreach ($spintax['vars'] as $key => $var) {
			if (isset($var['template'])) {
				$phrase = str_replace("$$". $key, self::make($var, $phraseTable, $key), $phrase);
				//$table = array_merge($table, self::build($var));
			} else {
				$phrase = str_replace("$$". $key, $var[$phraseTable[$key]['item'] - 1], $phrase);
			}
		}

		return $phrase;
	}*/
}