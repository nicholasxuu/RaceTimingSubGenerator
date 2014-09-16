<!DOCTYPE html>
<html>
<head>
	<title>Step1</title>
	<link rel="stylesheet" type="text/css" href="css/rtsg.css">
</head>
<body>
<form action="step2.php" id="step1_form" class="form_box" method="post" enctype="multipart/form-data">

	<select name="input_type" id="step1_drop" class="form_element">
		<option value="file_upload">File Upload</option>
		<option value="text_area">Text</option>
	</select>

	<br/>

	<input name="data_file" id="step1_fileupload"  class="form_element" type="file" />
	<br/>
	<textarea name="text_area" id="step1_textarea" class="form_element" ></textarea>
	<br/>

	<input type="submit" id="step1_submit" class="form_element" value="Next" />
</form>

</body>
</html>