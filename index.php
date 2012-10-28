<?php
	function entry($id, $date, $image, $title, $description) {
		if ($image != "")
			return sprintf("<h2>%s</h2><h3>%s</h3><img src='.%s' alt='%s' /><div class='desc'>%s</div>", $date, $title, $image, $title, $description);
	}
?>
<html>
	<head>
		<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />	
		<link rel="stylesheet" href="index_general.css" />
	</head>
	<body>
		<h1>Diary</h1>
		<?php
			$displaylimit = 30;

			$contentfile = fopen(dirname(__FILE__)."/contents.csv", "r");
			$c = 0;
			while(!feof($contentfile) and $c < $displaylimit) {
				$line = fgets($contentfile);
				$line = explode(",", $line);
				foreach ($line as &$l) {
					$l = str_replace('"', '', $l);
					$l = str_replace(';', '', $l);
				}
				print entry($line[0], $line[1], $line[2], $line[3], $line[4]);
				$c++;
			}
		?>
	</body>
</html>