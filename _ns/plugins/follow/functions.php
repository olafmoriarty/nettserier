<?php

function follow_button($comic) {
	global $logged_in, $user_info;
	$url = comic_url($comic);
	if (!$logged_in || can_edit_comic($user_info['id'], $url)) {
		return;
	}

	if (is_following($user_info['id'], $comic)) {
		return '<a href="/'.$url.'/follow/?do=unfollow&amp;returnurl='.urlencode(NS_URL).'" class="following" title="'._('Unfollow this comic').'">'._('Following').'</a>';
	}
	else {
		return '<a href="/'.$url.'/follow/?returnurl='.urlencode(NS_URL).'">'._('Follow').'</a>';
	}
}

function is_following($user, $comic) {
	global $conn;
	if (!is_numeric($user) || !is_numeric($comic)) {
		return false;
	}
	$query = 'SELECT COUNT(id) FROM ns_user_comic_rel WHERE reltype = \'f\' AND user = '.$user.' AND comic = '.$comic;
	$result = $conn->query($query);
	$num = $result->num_rows;
	if ($num) {
		$arr = $result->fetch_row();
		if ($arr[0])
			return true;
	}
	return false;
}

function is_blocking($user, $comic) {
	global $conn;
	if (!is_numeric($user) || !is_numeric($comic)) {
		return false;
	}
	$query = 'SELECT COUNT(id) FROM ns_user_comic_rel WHERE reltype = \'b\' AND user = '.$user.' AND comic = '.$comic;
	$result = $conn->query($query);
	$num = $result->num_rows;
	if ($num) {
		$arr = $result->fetch_row();
		if ($arr[0])
			return true;
	}
	return false;
}

function follow($user, $comic) {
	global $conn;
	if (is_numeric($user) && is_numeric($comic) && !is_following($user, $comic) && !can_edit_comic($user, $comic)) {
		$query = 'INSERT INTO ns_user_comic_rel (user, comic, reltype, time) VALUES ('.$user.', '.$comic.', \'f\', NOW())';
		$conn->query($query);
		return true;
	}
	return false;
}

function unfollow($user, $comic) {
	global $conn;
	if (is_numeric($user) && is_numeric($comic)) {
		$query = 'DELETE FROM ns_user_comic_rel WHERE user = '.$user.' AND comic = '.$comic.' AND reltype = \'f\'';
		$conn->query($query);
		return true;
	}
	return false;
}

function block($user, $comic) {
	global $conn;
	if (is_numeric($user) && is_numeric($comic) && !is_blocking($user, $comic) && !can_edit_comic($user, $comic)) {
		$query = 'INSERT INTO ns_user_comic_rel (user, comic, reltype, time) VALUES ('.$user.', '.$comic.', \'b\', NOW())';
		$conn->query($query);
		return true;
	}
	return false;
}

function unblock($user, $comic) {
	global $conn;
	if (is_numeric($user) && is_numeric($comic)) {
		$query = 'DELETE FROM ns_user_comic_rel WHERE user = '.$user.' AND comic = '.$comic.' AND reltype = \'b\'';
		$conn->query($query);
		return true;
	}
	return false;
}

