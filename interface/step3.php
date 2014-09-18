<?php
/**
 * Created by PhpStorm.
 * User: Nicholas
 * Date: 7/9/14
 * Time: 4:29 PM
 */


include_once "../index.php";

$content = $_POST['text_area'];
$rid = $_POST['race_id'];

if (!isset($content) || empty($content)) {

	echo "empty content";
	exit;
}

$file = preg_split("/(\r|\n|\r\n)/", $content);
if ($_POST['input_origin'] == "RCScoringPro") {
	$parser = new Model\Parser\NCHDataParser($file);
} else if ($_POST['input_origin'] == "MyLaps") {
	$parser = new Model\Parser\MylapsDataParser($file);
} else if ($_POST['input_origin'] == "GoKartRacer") {
	$parser = new Model\Parser\GKRDataParser($file);
} else {
	echo "empty input type.";
	exit;
}

$userList = $parser->totalResult->raceResultList[$rid]->getNameList();

echo <<< HTML
<!DOCTYPE html>
<html>
<head>
	<title>Step1</title>
	<link rel="stylesheet" type="text/css" href="css/rtsg.css">
</head>
<body>
<form action="step4.php" id="step3_form" class="form_box" method="post" enctype="multipart/form-data">
	<input type="text" class="form_element" name="input_origin" value="{$_POST['input_origin']}" />
	<br/>
	<select name="user_id" id="step3_drop" class="form_element">
HTML;

foreach ($userList as $uid => $username) {
	echo "<option value=\"{$uid}\">{$username}</option>";
}

echo <<< HTML
	</select>

	<br/>

	CrossLineTime:
	<input type="number" step="0.001" name="cross_time" min="0" max="300" id="step3_cross_time" class="form_element"/>

	<br/>

	<textarea name="text_area" id="step3_textarea" class="form_element" >{$content}</textarea>
	<br/>
	<input type="text" name="race_id" id="step3_race_id" class="form_element" value="{$rid}" />
	<br/>

	<input type="submit" id="step3_submit" class="form_element" value="Next" />
</form>

</body>
</html>
HTML;
