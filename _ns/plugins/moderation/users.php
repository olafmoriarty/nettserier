<?php

	// The purpose of this page is for moderators to be able to approve or delete unregistered users.

	// The URL is either /n/admin/users/, /n/admin/users/approve/[n]/ or /n/admin/users/delete/[n]/ . Find out which one it is!
	$folder = strtok('/');

// --------------------

	// Folder "approve": Approve user

	if ($folder == 'approve') {
		$user = strtok('/');
		if (is_numeric($user)) {
			$query = 'UPDATE ns_users SET level = 10 WHERE id = '.$user;
			$conn->query($query);
		}
		header('Location: '.NS_DOMAIN.'/n/admin/users/');
	}

// --------------------

	// Folder "delete": Delete user

	elseif ($folder == 'delete') {
		$user = strtok('/');

		if ($user && is_numeric($user)) {
			if (isset($_POST['confirm']) && $_POST['confirm'] && $user && is_numeric($user)) {
				delete_user($user);
				header('Location: '.NS_DOMAIN.'/n/admin/users/');
			}
			else {

				$c .= '<p>'._('Are you sure you want to delete this user?').'</p>'."\n";
				
				// TO ADD: User information so we know who we're deleting!
				$c .= '<p>'.$user.'</p>';
				
				$c .= '<form method="post" action="/n/admin/users/delete/'.$user.'/">'."\n";
				$c .= '<p><input type="submit" name="confirm" value="'._('Yes!').'"> | <a href="/n/admin/users/">'._('No!').'</a></p>';
				$c .= '</form>'."\n";
			}
		}
		else {
			header('Location: '.NS_DOMAIN.'/n/admin/users/');
		}
	}

// --------------------

	// No folder (or invalid folder): Show list of unapproved users

	else {

		// Select active unverified users from database
		$query = 'SELECT cc.user, cc.lastcomm, IFNULL(u.username, '._('[User not found]').') AS username, u.regtime, c2.text AS lastcomment FROM (SELECT DISTINCT c.user, MAX(c.id) AS lastcomm FROM ns_comments AS c GROUP BY c.user) AS cc LEFT JOIN ns_users AS u ON cc.user = u.id LEFT JOIN ns_comments AS c2 ON cc.lastcomm = c2.id WHERE u.level < 10 ORDER BY c2.regtime DESC';
		$result = $conn->query($query);

		// For each row ...
		while ($r_arr = $result->fetch_assoc()) {
			// Username
			$c .= '<h3><a href="/n/users/'.$r_arr['user'].'/">'.htmlentities($r_arr['username']).'</a></h3>'."\n";

			// Registered:
			$c .= '<p><strong>'._('Registered:').'</strong> '.$r_arr['regtime'].'</p>';

			// Last comment
			$c .= '<h4>'._('Last comment:').'</h4>'."\n";
			$c .= $r_arr['lastcomment'];
			$c .= '<p><a href="/n/admin/users/approve/'.$r_arr['user'].'/">'._('Approve user').'</a> | <a href="/n/admin/users/delete/'.$r_arr['user'].'/">'._('Delete user').'</a></p>';
		}
	}
