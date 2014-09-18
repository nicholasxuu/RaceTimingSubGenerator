<?php
/**
 * Created by PhpStorm.
 * User: nicholas
 * Date: 9/17/14
 * Time: 5:45 PM
 */

include_once("../index.php");

$datafileLoc1 = "../data/rcscoringpro_laps.txt";
$datafileLoc2 = "../data/rcscoringpro_general.txt";

$fileContent1 = file($datafileLoc1);
$fileContent2 = file($datafileLoc2);
$emptySpace = array("\n", "\n");

$fileContent = array_merge($fileContent2, $emptySpace, $fileContent1);

//var_dump($fileContent);
$parser = new Model\Parser\MylapsDataParser($fileContent);

