<?php

$folder = strtok('/');

if ($folder && is_numeric($folder)) {

	$user = false;

	$query = 'SELECT username, realname, sponsor FROM ns_users WHERE id = '.$folder;
	$result = $conn->query($query);
	$num = $result->num_rows;
	if ($num) {
		$arr = $result->fetch_assoc();
		$user = $folder;
	}

	if ($user) {
		$ns_title = htmlspecialchars($arr['username']);
		if ($arr['realname']) {
			$c .= '<h2>'.htmlspecialchars($arr['realname']).'</h2>';
			$c .= '<p><em>'.str_replace('{name}', $arr['username'], _('({name})')).'</em></p>';
		}
		else {
			$c .= '<h2>'.htmlspecialchars($arr['username']).'</h2>';
		}
		$c .= '<div class="profile_pic">'.avatar($user, 400);
		if ($arr['sponsor']) {
			$c .= '<img src="/_ns/stuff/sponsor_icon.png" alt="'.str_replace('{page}', PAGE_TITLE, _('This user supports {page} on Patreon!')).'" class="sponsor-icon">';
		}
		$c .= '</div>';

		$c .= $action['user_page']->run($user);

	}

}
else {

}