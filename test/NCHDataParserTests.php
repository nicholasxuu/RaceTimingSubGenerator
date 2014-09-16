<?php
include_once("../index.php");

$datafileLoc = "../data/2014norcalchamp.txt";

$fileContent = file($datafileLoc);


$parser = new Model\Parser\NCHDataParser($fileContent);


$raceIdArr = $parser->totalResult->searchRaceByName("4wd Buggy A1");
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
$driverIdArr = $ts->raceResult->searchIdByName("nathan bernal");
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
$ts->setStartTime($driverId, 1.801);
$ctd = new Model\Subtitle\CountDown($ts->getRaceTime(), $ts->getStartTime());


// write to file
$fh = fopen("D:\\test.ass", "w");
fwrite($fh, $si);
fwrite($fh, $ts);
fwrite($fh, $ctd);
fclose($fh);