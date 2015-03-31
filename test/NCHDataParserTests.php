<?php
include_once("../index.php");

//$datafileLoc = "../data/2014norcalchamp.txt";
$datafileLoc = "../data/20150319norcal";

$fileContent = file($datafileLoc);


$parser = new Model\Parser\NCHDataParser($fileContent);

$raceName = "2wd Buggy Modified A";
//$raceName = "4wd Buggy Modified A";
//$raceName = "4x4 Short Course Open A";

$raceIdArr = $parser->totalResult->searchRaceByName($raceName);
if (count($raceIdArr) != 1) {
	if (empty($raceIdArr)) {
		echo "no race found.\n";
	} else {
		foreach ($raceIdArr as $rid) {
			echo "{$parser->totalResult->raceResultList[$rid]->name}\n";
		}
		echo "multiple races found.\n";
	}
	exit;
	echo "multiple race found";
	exit;
}
$raceId = $raceIdArr[0];


$si = new Model\Subtitle\ScriptInfo();

$ts = new Model\Subtitle\TimingScoring($parser->totalResult->raceResultList[$raceId]);
$driverIdArr = $ts->raceResult->searchIdByName("nicholas xu");
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
$fh = fopen("/Users/nicholasxu/Downloads/{$raceName}.ass", "w");
fwrite($fh, $si);
fwrite($fh, $ts);
fwrite($fh, $ctd);
fclose($fh);