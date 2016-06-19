<?php

$folder = strtok('/');

if ($folder && is_numeric($folder)) {

	$user = false;

	$query = 'SELECT username FROM ns_users WHERE id = '.$folder;
	$result = $conn->query($query);
	$num = $result->num_rows;
	if ($num) {
		$arr = $result->fetch_assoc();
		$user = $folder;
	}

	if ($user) {
		$ns_title = htmlspecialchars($arr['username']);
		$c .= '<h2>'.htmlspecialchars($arr['username']).'</h2>';
		$c .= '<p class="profile_pic">'.avatar($user, 400).'</p>';

		$c .= $action['user_page']->run($user);

	}

}
else {

}