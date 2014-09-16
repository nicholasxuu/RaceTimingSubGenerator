<?php
/**
 * Created by PhpStorm.
 * User: Nicholas
 * Date: 7/8/14
 * Time: 8:22 PM
 */

include_once("../index.php");

$datafileLoc = "../data/20130917gokartracer.txt";

$fileContent = file($datafileLoc);


$parser = new Model\Parser\GKRDataParser($fileContent);

