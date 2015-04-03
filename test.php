<?php
/**
 * Created by PhpStorm.
 * User: Nicholas
 * Date: 3/31/2015
 * Time: 1:58 PM
 */

function endsWith($haystack, $needle) {
	// search forward starting from end minus needle length characters
	return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

$f = file("data/livetimerc_sample.txt");
$quoteCount = 0;
$l = "";
foreach ($f as $line) {
	$quoteCount += substr_count($line, "\"");

	$l .= $line;
	if ($quoteCount % 2 == 0) {
		var_dump($l);
		$l = "";
	}

}