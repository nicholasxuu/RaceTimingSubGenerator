<?php
/**
 * Created by PhpStorm.
 * User: Nicholas
 * Date: 7/9/14
 * Time: 2:08 PM
 */

include_once "../index.php";

function get_upload() {
	if (
		!isset($_FILES['data_file']['error']) ||
		is_array($_FILES['data_file']['error'])
	) {
		throw new Exception('Invalid parameters.');
	}

	// Check $_FILES['data_file']['error'] value.
	switch ($_FILES['data_file']['error']) {
		case UPLOAD_ERR_OK:
			break;
		case UPLOAD_ERR_NO_FILE:
			throw new Exception('No file sent.');
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
			throw new Exception('Exceeded filesize limit.');
		default:
			throw new Exception('Unknown errors.');
	}

	if ((($_FILES['data_file']['type'] == "text/csv") ||
		 ($_FILES['data_file']['type'] == "text/txt") ||
		 ($_FILES['data_file']['type'] == "text/plain") ||
		 ($_FILES['data_file']['type'] == "application/octet-stream")) &&
		($_FILES['data_file']['size'] < 1000000))
	{
		if ($_FILES['data_file']['error'] > 0)
		{
			throw new Exception("Error: {$_FILES['data_file']['error']}");
		}
		else
		{
			$fileContent= file_get_contents($_FILES['data_file']['tmp_name']);
			return $fileContent;
		}
	}
	else
	{
		throw new Exception("Invalid File: Type={$_FILES['data_file']['type']}; Size={$_FILES['data_file']['size']}");
	}
}

if ($_POST['input_type'] == "file_upload") {
	try {
		$content = get_upload();
	} catch (Exception $e) {
		echo $e;
	}
} else if ($_POST['input_type'] == "text_area") {
	$content = $_POST['text_area'];
}

if (!isset($content) || empty($content)) {

	echo "empty content";
	exit;

}

$file = preg_split("/(\r|\n|\r\n)/", $content);
$parser = new Model\Parser\NCHDataParser($file);

$raceList = $parser->totalResult->getRaceNameList();

echo <<< HTML
<!DOCTYPE html>
<html>
<head>
	<title>Step1</title>
	<link rel="stylesheet" type="text/css" href="css/rtsg.css">
</head>
<body>
<form action="step3.php" id="step2_form" class="form_box" method="post" enctype="multipart/form-data">

	<select name="race_id" id="step2_drop" class="form_element">
HTML;

foreach ($raceList as $rid => $raceName) {
	echo "<option value=\"{$rid}\">{$raceName}</option>";
}

echo <<< HTML
	</select>

	<br/>

	<textarea name="text_area" id="step2_textarea" class="form_element" >{$content}</textarea>
	<br/>

	<input type="submit" id="step2_submit" class="form_element" value="Next" />
</form>

</body>
</html>
HTML;
