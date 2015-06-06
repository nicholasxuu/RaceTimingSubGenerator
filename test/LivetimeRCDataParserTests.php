<?php
include_once("../index.php");

//$datafileLoc = "../data/2014norcalchamp.txt";
$datafileLoc = "../data/livetimerc_sample2.txt";

$fileContent = file($datafileLoc);


$parser = new Model\Parser\LivetimeRCDataParser($fileContent);
//var_dump($parser);

$raceList = $parser->totalResult->getRaceNameList();

// $raceName = "油越";
//$raceName = "4wd Buggy Modified A";
//$raceName = "4x4 Short Course Open A";

var_dump($parser->totalResult);

$si = new Model\Subtitle\ScriptInfo();
$ts = new Model\Subtitle\TimingScoring($parser->totalResult->raceResultList[0]);
$driverIdArr = $ts->raceResult->searchIdByName("张 晓琪");
if (count($driverIdArr) != 1) {
	if (empty($driverIdArr)) {
		echo "no driver found.\n";
	} else {
		foreach ($driverIdArr as $did) {
			echo "{$ts->raceResult->driverList[$did]->name}\n";
		}
		echo "multiple driver found.\n";
	}
	exit;
}
$driverId = $driverIdArr[0];
$ts->setStartTime($driverId, 7.057);
$ctd = new Model\Subtitle\CountDown($ts->getRaceTime(), $ts->getStartTime());


// write to file
//$fh = fopen("F:\\Download\\2015_The_Dirt_Nitro_Challenge_1_8_Pro_Buggy_A-Main_and_JConcepts_Pit_Report_with_top_five_finishers.ass", "w");
//fwrite($fh, $si);
//fwrite($fh, $ts);
//fwrite($fh, $ctd);
//fclose($fh);