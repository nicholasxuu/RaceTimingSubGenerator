<?php
/**
 * Created by PhpStorm.
 * User: Nicholas
 * Date: 7/9/14
 * Time: 2:08 PM
 */

include_once "../index.php";

function get_upload($dataFileElement) {
	if (
		!isset($_FILES[$dataFileElement]['error']) ||
		is_array($_FILES[$dataFileElement]['error'])
	) {
		throw new Exception('Invalid parameters.');
	}

	// Check $_FILES[$dataFileElement]['error'] value.
	switch ($_FILES[$dataFileElement]['error']) {
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

	if ((($_FILES[$dataFileElement]['type'] == "text/csv") ||
		 ($_FILES[$dataFileElement]['type'] == "text/txt") ||
		 ($_FILES[$dataFileElement]['type'] == "text/plain") ||
		 ($_FILES[$dataFileElement]['type'] == "application/octet-stream")) &&
		($_FILES[$dataFileElement]['size'] < 1000000))
	{
		if ($_FILES[$dataFileElement]['error'] > 0)
		{
			throw new Exception("Error: {$_FILES[$dataFileElement]['error']}");
		}
		else
		{
			$fileContent= file_get_contents($_FILES[$dataFileElement]['tmp_name']);
			return $fileContent;
		}
	}
	else
	{
		throw new Exception("Invalid File: Type={$_FILES[$dataFileElement]['type']}; Size={$_FILES[$dataFileElement]['size']}");
	}
}

if ($_POST['input_type'] == "file_upload") {
	try {
		$content = get_upload('data_file');
		try {
			$content2 = get_upload('data_file2');
		} catch (Exception $e) {
			// ignore
		}
		if (!empty($content2)) {
			$content .= "\n\n" . $content2;

		}
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

$file = preg_split("/(\r\n|\r|\n)/u", $content);

if ($_POST['input_origin'] == "RCScoringPro") {
	$parser = new Model\Parser\NCHDataParser($file);
} else if ($_POST['input_origin'] == "MyLaps") {
	$parser = new Model\Parser\MylapsDataParser($file);
} else if ($_POST['input_origin'] == "GoKartRacer") {
	$parser = new Model\Parser\GKRDataParser($file);
} else if ($_POST['input_origin'] == "LiveTiming") {
	$parser = new Model\Parser\LivetimeRCDataParser($file);
} else {
	echo "empty input type.";
	exit;
}

$raceList = $parser->totalResult->getRaceNameList();

echo <<< HTML
<!DOCTYPE html>
<html>
<head>
	<title>Step1</title>
	<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
	<script src="http://code.jquery.com/jquery.js"></script>
	<script src="js/bootstrap.min.js"></script>
</head>
<body>
<form action="step3.php" id="step2_form" class="form_box" method="post" enctype="multipart/form-data">
	<input type="text" class="form_element" name="input_origin" value="{$_POST['input_origin']}"  readonly="readonly"/>
	<br/>
	<br/>
	Select Race: <br/>
	<select name="race_id" id="step2_drop" class="form_element">
HTML;

foreach ($raceList as $rid => $raceName) {
	echo "<option value=\"{$rid}\">{$raceName}</option>";
}

echo <<< HTML
	</select>

	<br/>
	<br/>
	<textarea name="text_area" id="step2_textarea" class="form_element" readonly="readonly">{$content}</textarea>
	<br/>
	<br/>
	<input type="submit" id="step2_submit" class="btn" value="Next" />
</form>

</body>
</html>
HTML;
