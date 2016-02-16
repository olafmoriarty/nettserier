<!DOCTYPE html>
<html lang="no">
	<head>
		<title><?php
		if ($ns_title) {
			echo $ns_title;
			echo $ns_tsep;
		}
		echo __(PAGE_TITLE); ?></title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width; initial-scale=1.0">
<?php
	echo '<link rel="stylesheet" href="/_ns/styles/'.$ns_style.'/style.css">'."\n";
?>	</head>
	<body>
<?php
	echo $c;
?>	</body>
</html>