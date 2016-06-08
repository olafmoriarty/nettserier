<?php
if ($logged_in) {
	if (isset($_GET['do']) && $_GET['do'] == 'unfollow') {
		unfollow($user_info['id'], $comic_id);
	}
	else {
		follow($user_info['id'], $comic_id);
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
