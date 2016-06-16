<?php
	// Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	$open_source->add_line(['name' => 'phpSecureLogin', 'developer' => 'Peter Bradley', 'link' => 'https://github.com/peredurabefrog/phpSecureLogin/', 'license' => 'GNU General Public License 3']);
	$cookie_info->add_line(['name' => 'sec_session_id', 'desc' => _('Used in login handling for security reasons.')]);
	$cookie_info->add_line(['name' => 'user_id', 'desc' => _('When you log in to the website, and you check the box that says "Remember me", this is one of the cookies that make that box work.')]);
	$cookie_info->add_line(['name' => 'login_string', 'desc' => _('When you log in to the website, and you check the box that says "Remember me", this is one of the cookies that make that box work.')]);

	// Setup 
	$n_urls->add_line(['url' => 'log-in', 'script' => $tpf.'login.php']);
	$n_urls->add_line(['url' => 'register', 'script' => $tpf.'register.php']);
	$n_urls->add_line(['url' => 'welcome', 'script' => $tpf.'welcome.php']);
	$n_urls->add_line(['url' => 'log-out', 'script' => $tpf.'logout.php']);
	$n_urls->add_line(['url' => 'dashboard', 'script' => $tpf.'dashboard.php']);
	$n_urls->add_line(['url' => 'users', 'script' => $tpf.'profiles.php']);

	if (!$logged_in) {
		$n_menu->add_line(['text' => _('Sign up'), 'link' => '/n/register/', 'order' => 90]);
		$n_menu->add_line(['text' => _('Log in'), 'link' => '/n/log-in/', 'order' => 91]);
	}

	$d_urls->add_line(['url' => 'settings', 'script' => $tpf.'settings.php']);
	$d_urls->add_line(['url' => 'delete-user', 'script' => $tpf.'delete-user.php']);

	$d_menu->add_line(['text' => _('Settings'), 'link' => '/n/dashboard/settings/', 'order' => 99]);
	$d_menu->add_line(['text' => _('Log out'), 'link' => '/n/log-out/', 'order' => 100]);

	function delete_user($id) {
		global $conn;
		if (is_numeric($id)) {
			// Delete user
			$query = 'DELETE FROM ns_users WHERE id = '.$id;
			$conn->query($query);

			// Delete user comics (MOVE TO ARRAYHANDLER!)
			delete_user_comics($id);
		}
	}

	function avatar($id, $size = 100) {
		global $conn;
		if (!is_numeric($id))
			return false;
		$query = 'SELECT username, email FROM ns_users WHERE id = '.$id;
		$result = $conn->query($query);
		$num = $result->num_rows;

		if (!$num)
			return false;

		$arr = $result->fetch_assoc();

		return '<img src="http://www.gravatar.com/avatar/'.md5(strtolower(trim($arr['email']))).'?s='.$size.'&amp;d=mm" alt="'.htmlspecialchars($arr['username']).'">';
	}