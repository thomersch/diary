<?php
	function image_resize($src, $dst, $type, $width, $height, $crop=0){
		if(!list($w, $h) = getimagesize($src)) return false;
	  	if($type == 'jpeg') $type = 'jpg';

	  	switch($type) {
	    	case 'jpg': $img = imagecreatefromjpeg($src); break;
	    	case 'png': $img = imagecreatefrompng($src); break;
	    	default : return false;
	  	}

	  	if($crop) {
	    	$ratio = max($width/$w, $height/$h);
	    	$h = $height / $ratio;
	    	$x = ($w - $width / $ratio) / 2;
	    	$w = $width / $ratio;
	  	}
	  	else {
	    	$ratio = min($width/$w, $height/$h);
	    	$width = $w * $ratio;
	    	$height = $h * $ratio;
	   		$x = 0;
	  	}

	  	$new = imagecreatetruecolor($width, $height);

	  	if($type == "png"){
	    	imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
	    	imagealphablending($new, false);
	    	imagesavealpha($new, true);
	  	}

	  	imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);
	  	switch($type) {
	    	case 'jpg': imagejpeg($new, $dst); break;
	    	case 'png': imagepng($new, $dst); break;
	  	}
	  	
	  	return true;
	}

	function prepend($string, $filename) {
 		$context = stream_context_create();
		$fp = fopen($filename, 'r', 1, $context);
		$tmpname = md5($string);
		file_put_contents($tmpname, $string);
  		file_put_contents($tmpname, $fp, FILE_APPEND);
		fclose($fp);
		unlink($filename);
		rename($tmpname, $filename);
	}

	function write_file($count, $date, $filename, $title, $description) {
		prepend(sprintf('"%s","%s","%s","%s","%s";%s', $count, $date, $filename, $title, $description, PHP_EOL), "contents.csv");
	}

	if ($_FILES['imagefile'] != "") {
		$counterfile = fopen('count.txt', 'r');
		$count = fgets($counterfile);
		fclose($counterfile);
		$count++;
		$counterfile = fopen('count.txt', 'c');
		fwrite($counterfile, $count);
		fclose($counterfile);

		if ($_FILES['imagefile']['type'] == "image/png")
			$target_fileextension = "png";
		elseif ($_FILES['imagefile']['type'] == "image/jpeg")
			$target_fileextension = "jpg";

		if ($target_fileextension != "") {
			$target_filename = sprintf("%s/images/%s-%s.%s", dirname(__FILE__), $count, $_POST['date'], $target_fileextension);
			$relative_filename = sprintf("/images/%s-%s.%s", $count, $_POST['date'], $target_fileextension);
			image_resize($_FILES['imagefile']['tmp_name'], $target_filename, $target_fileextension, 800, 800);
			write_file($count, $_POST['date'], $relative_filename, $_POST['title'], $_POST['description']);
		}
	}
?>
<html>
	<head>
		<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
		<link rel="stylesheet" href="upload_general.css" />
	</head>
	<body>
		<h1>Diary</h1>
		<form action="./upload.php" method="POST" enctype="multipart/form-data">
			<p>Date<br /><input name="date" type="date" /></p>
			<p>Title<br /><input name="title" /></p>
			<p>Image<br /><input type="file" name="imagefile" /></p>
			<p>Description<br /><textarea name="description"></textarea></p>
			<p><input type="submit" value="Submit" /></p>
		</form>
	</body>
</html>