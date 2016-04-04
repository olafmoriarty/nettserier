<?php

// This is a comic! Hurray!

// We need to create a header.

// We also need to show the right content.

$startpage = 'comic';

$folder = strtok('/');

// Default content if all other checks fail is the 404 page
$content_file = NS_PATH.'pages/404.php';

if (!$folder) {
	// Which script to load is stated in $startpage
	
	$folderscript = $c_urls->find('url', $startpage);
	if ($folderscript) {
		$content_file = $folderscript['script'];
	}
}
elseif ($folderscript = $c_urls->find('url', $folder)) {
	// The URL is nettserier.no/comicname/something/, which means a script should be loaded (unless the URL is wrong).
	$content_file = $folderscript['script'];
}

if (file_exists($content_file)) {
	include($content_file);
}
else {
	include(NS_PATH.'pages/404.php');
}
