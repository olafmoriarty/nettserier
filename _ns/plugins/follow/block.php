<?php
if ($logged_in) {
	if (isset($_GET['do']) && $_GET['do'] == 'unblock') {
		unblock($user_info['id'], $comic_id);
	}
	else {
		block($user_info['id'], $comic_id);
	}
}
if (isset($_GET['returnurl'])) {
	$returnurl = $_GET['returnurl'];
}
else {
	$returnurl = '';
}
header('Location: '.NS_DOMAIN.$returnurl);
exit;
