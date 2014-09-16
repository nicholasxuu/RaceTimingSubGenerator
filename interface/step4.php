<?php
/**
 * Created by PhpStorm.
 * User: Nicholas
 * Date: 7/9/14
 * Time: 4:38 PM
 */


include_once "../index.php";

$content = $_POST['text_area'];
$rid = $_POST['race_id'];
$uid = $_POST['user_id'];
$crossTime = $_POST['cross_time'];

if (!isset($content) || empty($content)) {

	echo "empty content";
	exit;
}

$file = preg_split("/(\r|\n|\r\n)/", $content);
$parser = new Model\Parser\NCHDataParser($file);


$si = new Model\Subtitle\ScriptInfo();
$ts = new Model\Subtitle\TimingScoring($parser->totalResult->raceResultList[$rid]);
$driverId = $uid;
$ts->setStartTime($uid, $crossTime);
$ctd = new Model\Subtitle\CountDown($ts->getRaceTime(), $ts->getStartTime());

$outputFilename = preg_replace("/\s+/", ".", $parser->totalResult->raceResultList[$rid]->name);

header('Content-type: application/text');
header("Content-disposition: attachment; filename={$outputFilename}.ass");
echo $si . "\n" . $ts . "\n" . $ctd;

