<!DOCTYPE html>
<html>
<head>
	<title>Step1</title>
	<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
	<script src="http://code.jquery.com/jquery.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script>
		$(document).ready(function() {

		});
	</script>
</head>
<body>
<form action="step2.php" id="step1_form" class="form_box" method="post" enctype="multipart/form-data">

	Input Data Type:<br/>
	<select name="input_origin" id="step1_drop2" class="btn">
		<option value="RCScoringPro">RCScoringPro</option>
		<option value="MyLaps">MyLaps</option>
		<option value="GoKartRacer">GoKartRacer</option>
	</select>
	<br/>
	<br/>
	Input Method:<br/>
	<select name="input_type" id="step1_drop" class="btn">
		<option value="file_upload">File Upload</option>
		<option value="text_area">Text</option>
	</select>
	<br/>
	<br/>
	<div id="input_file_area1">
	Input File 1:<br/>
	<input name="data_file" id="step1_fileupload"  class="form_element" type="file" />
	<br/>
	</div>
	<br/>
	<div id="input_file_area2" >
	Input File 2 (optional):<br/>
	<input name="data_file2" id="step1_fileupload2"  class="form_element" type="file" />
	<br/>
	</div>
	<br/>
	<div id="input_text_area" >
	Input Text:<br/>
	<textarea name="text_area" id="step1_textarea" class="form_element" ></textarea>
	<br/>
	</div>
	<br/>
	<input type="submit" id="step1_submit" class="btn" value="Next" />
</form>

</body>
</html>