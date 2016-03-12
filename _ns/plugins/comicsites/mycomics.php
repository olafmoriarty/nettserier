<?php

$folder = strtok('/');

if (!$folder) {

	$ns_title = __('My comics');
$c .= '<h2>'.__('My comics').'</h2>';
	if (owns_comics($user_info['id'])) {
		$c .= '<p><a href="/n/dashboard/new-comic/">'.__('Create a new comic').'</a></p>';
		$query = 'SELECT c.url, c.name FROM ns_comics AS c LEFT JOIN ns_user_comic_rel AS r ON c.id = r.comic WHERE r.user = '.$user_info['id'].' AND r.reltype IN (\'c\', \'e\') ORDER BY c.name';
		$result = $conn->query($query);
		while ($arr = $result->fetch_assoc()) {
			$c .= '<h3>'.htmlspecialchars($arr['name']).'</h3>';
			$c .= str_replace('{comic}', $arr['url'], $comicadm_menu->return_ul('nav_menu'));
		}
	}
	else {
		$c .= '<p>'.__('You haven\'t created any comics yet.').'</p>';
		$c .= '<p><a href="/n/dashboard/new-comic/">'.__('Create your first comic now!').'</a></p>';
	}
}
else {

	// Default content if all other checks fail is the 404 page
	$content_file = NS_PATH.'pages/404.php';

	// $folder should be a comic URL that the user has permissions to edit, check if it is!

	if (can_edit_comic($user_info['id'], $folder)) {
		$active_comic = $folder;
		$active_comic_id = comic_id($folder);
		$folder = strtok('/');

		$folderscript = $comicadm_urls->find('url', $folder);
		if ($folderscript) {
			$content_file = $folderscript['script'];
		}
	}
	include($content_file);
	$c .= '<p><a href="/n/dashboard/my-comics/">'.__('Return to "My comics"').'</a></p>';
	
}