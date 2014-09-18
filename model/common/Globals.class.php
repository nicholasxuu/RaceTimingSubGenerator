<?php
/**
 * Created by PhpStorm.
 * User: Nicholas
 * Date: 7/8/14
 * Time: 4:37 PM
 */

namespace Model\Common;


class Globals {

	/**
	 * Check if string match query.
	 * return true if string contains every components in query, space delimited, case insensitive.
	 * @param string $string
	 * @param string $query
	 * @return bool
	 */
	public static function searchQuery($string, $query) {
		$string = strtolower($string);
		$query = strtolower($query);
		$queryArr = explode(" ", $query);
		foreach ($queryArr as $component) {
			if (strstr($string, $component) === false) {
				return false;
			}
		}
		return true;
	}

	public static function convertTimeToSeconds($string) {
		if (!preg_match('/[0-9]+/', $string)) {
			return false;
		}
		if (preg_match('/^[0-9]+\.[0-9]+$/', $string) || preg_match('/^[0-9]+$/', $string)) {
			return floatval($string);
		} else if (preg_match('/^[0-9]+\:[0-9]+\.[0-9]+$/', $string)) {
			$strArr = explode(":", $string);
			return floatval($strArr[0]) * 60 + floatval($strArr[1]);
		}
		return false;
	}

} 