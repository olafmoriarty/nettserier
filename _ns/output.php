<!DOCTYPE html>
<html lang="no">
	<head>
		<title><?php
		if ($ns_title) {
			echo $ns_title;
			echo $ns_tsep;
		}
		echo _(PAGE_TITLE); ?></title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width; initial-scale=1.0">
<?php
	echo '<link rel="stylesheet" href="/_ns/styles/'.$ns_style.'/style.css">'."\n";
	echo $head->return_text()."\n";
?>	</head>
	<body>
<?php
	echo $c;
?>	</body>
</html>