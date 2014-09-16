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
$parser = new Model\Parser\NCHDataParser($file);

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
