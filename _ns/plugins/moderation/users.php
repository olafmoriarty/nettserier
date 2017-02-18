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
				$query = 'SELECT username, realname, regtime, email FROM ns_users WHERE id = '.$user;
				$result = $conn->query($query);
				$num = $result->num_rows;

				if ($num) {
					$r_arr = $result->fetch_assoc();
					$c .= '<ul>';
					$c .= '<li><strong>'._('Username:').'</strong> '.htmlentities($r_arr['username']).'</li>';
					$c .= '<li><strong>'._('Real name:').'</strong> '.htmlentities($r_arr['realname']).'</li>';
					$c .= '<li><strong>'._('E-mail').'</strong> '.htmlentities($r_arr['email']).'</li>';
					$c .= '<li><strong>'._('User registered:').'</strong> '.htmlentities($r_arr['regtime']).'</li>';
					$c .= '<li><strong>'._('User ID:').'</strong> '.$user.'</li>';
					$c .= '</ul>'."\n";

				}
				else {
					$c .= '<p><em>'._('The user was not found in the database.').'</em></p>';
				}
				$c .= '<form method="post" action="/n/admin/users/delete/'.$user.'/">'."\n";
				$c .= '<p><input type="submit" name="confirm" value="'._('Yes!').'"></p>'."\n".'<p><a href="/n/admin/users/">'._('No!').'</a></p>'."\n";
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

		$c .= '<h2>'._('Approve/delete users').'</h2>';

		// How many users to show per page?
		$rows = 20;

		// Select active unverified users from database
		$query = 'SELECT SQL_CALC_FOUND_ROWS cc.user, cc.lastcomm, IFNULL(u.username, \''._('[User not found]').'\') AS username, u.regtime, c2.text AS lastcomment, c2.regtime AS lastcommtime, c2.ip FROM (SELECT DISTINCT c.user, MAX(c.id) AS lastcomm FROM ns_comments AS c GROUP BY c.user) AS cc LEFT JOIN ns_users AS u ON cc.user = u.id LEFT JOIN ns_comments AS c2 ON cc.lastcomm = c2.id WHERE u.level < 10 ORDER BY c2.regtime DESC '.limitstring($rows);
		$result = $conn->query($query);

		$query = 'SELECT FOUND_ROWS()';
		$fr_result = $conn->query($query);
		$fr_arr = $fr_result->fetch_row();
		$total_rows = $fr_arr[0];
		$pagecount = ceil($total_rows / $rows);


		// If no rows ...
		
		$num = $result->num_rows;

		if (!$num) {
			$c .= '<p>'._('No new users to approve!').'</p>';
		}

		else {

			$c .= '<p>'.str_replace('{n}', $total_rows, _('{n} new users to approve/delete')).'</p>';

			$c .= '<section class="comment-section">';

			// For each row ...
			while ($r_arr = $result->fetch_assoc()) {
				$c .= '<article class="comment">';
				// Username
				$c .= '<h3><a href="/n/users/'.$r_arr['user'].'/">'.htmlentities($r_arr['username']).'</a></h3>'."\n";

				// Registered:
				$c .= '<p><strong>'._('Registered:').'</strong> '.$r_arr['regtime'].'</p>';
				$c .= '<p><strong>'._('Last IP:').'</strong> '.$r_arr['ip'].'</p>';

				// Last comment
				$c .= '<div class="last-comment">';
				$c .= '<h4>'._('Last comment:').'</h4>'."\n";
				$c .= '<p><strong>'._('Published:').'</strong> '.$r_arr['lastcommtime'].'</p>';
				$c .= $r_arr['lastcomment'];
				$c .= '</div>';
				$c .= '<ul class="nav_menu"><li class="good"><a href="/n/admin/users/approve/'.$r_arr['user'].'/">'._('Approve user').'</li><li class="bad"><a href="/n/admin/users/delete/'.$r_arr['user'].'/">'._('Delete user').'</a></li></ul>';
				$c .= '</article>';
			}
			$c .= limitstring_nav($pagecount);
			$c .= '</section>';
		}
	}
