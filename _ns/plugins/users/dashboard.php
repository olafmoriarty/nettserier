<?php

if ($logged_in) {
	$folder = strtok('/');

	if (!$folder) {
		$c .= '<h2>'.__('User dashboard').'</h2>';

		$c .= $d_menu->return_ul('nav_menu');
	}
	else {
		// Default content if all other checks fail is the 404 page
		$content_file = NS_PATH.'pages/404.php';

		// The URL is nettserier.no/n//dashboard/something/, which means a script should be loaded (unless the URL is wrong).
		// Check if the script is set in $d_urls
		$folderscript = $d_urls->find('url', $folder);
		if ($folderscript) {
			$content_file = $folderscript['script'];
		}
		include($content_file);
		$c .= '<p><a href="/n/dashboard/">'.__('Return to dashboard').'</a></p>';
	}
}
else {
	header('Location: '.NS_DOMAIN.'/n/log-in/?returnurl=/n/dashboard/new-comic/');
	exit;
}