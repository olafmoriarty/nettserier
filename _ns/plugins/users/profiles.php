<?php

$folder = strtok('/');

if ($folder && is_numeric($folder)) {

	$user = false;

	$query = 'SELECT username, email FROM ns_users WHERE id = '.$folder;
	$result = $conn->query($query);
	$num = $result->num_rows;
	if ($num) {
		$arr = $result->fetch_assoc();
		$user = $folder;
	}

	if ($user) {
		$ns_title = htmlspecialchars($arr['username']);
		$c .= '<h2>'.htmlspecialchars($arr['username']).'</h2>';
		$c .= '<p class="profile_pic"><img src="http://www.gravatar.com/avatar/'.md5(strtolower(trim($arr['email']))).'?s=400&amp;d=mm" alt="'.htmlspecialchars($arr['username']).'"></p>';

		$c .= '<h3>'.__('My comics').'</h3>';
		$c .= '<p>'.__('{name} hasn\'t created any comics yet.').'</p>';

		$c .= '<h3>'.__('Comics I follow').'</h3>';
		$c .= '<p>'.__('{name} isn\'t following any comics yet.').'</p>';
	}

}
else {
	$c .= 'Something wicked this way comes';
}