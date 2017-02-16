<?php

	$query = 'SELECT u.id, u.username FROM ns_users AS u WHERE u.verified = 0 ORDER BY u.id DESC';
	$query = 'SELECT cc.user, cc.lastcomm, u.username, u.regtime, c2.text FROM (SELECT DISTINCT c.user, MAX(c.id) AS lastcomm FROM ns_comments AS c GROUP BY c.user) AS cc LEFT JOIN ns_users AS u ON cc.user = u.id LEFT JOIN ns_comments AS c2 ON cc.lastcomm = c2.id ORDER BY c2.regtime DESC';
	$result = $conn->query($query);

	while ($r_arr = $result->fetch_assoc()) {
		$c .= '<h3>'.htmlentities($r_arr['username']).'</h3>'."\n";
		$c .= $r_arr['text'];
		$c .= '<p><a href="">'._('Approve user').'</a> | <a href="">'._('Delete user').'</a></p>';
	}
