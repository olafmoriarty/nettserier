<?php
	// Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	// Setup 
	$n_urls->add_line(['url' => 'log-in', 'script' => $tpf.'login.php']);
	$n_urls->add_line(['url' => 'register', 'script' => $tpf.'register.php']);
	$n_urls->add_line(['url' => 'welcome', 'script' => $tpf.'welcome.php']);
	$n_urls->add_line(['url' => 'log-out', 'script' => $tpf.'logout.php']);
	$n_urls->add_line(['url' => 'dashboard', 'script' => $tpf.'dashboard.php']);
	$n_urls->add_line(['url' => 'users', 'script' => $tpf.'profiles.php']);

	if (!$logged_in) {
		$n_menu->add_line(['text' => __('New user?'), 'link' => '/n/register/']);
	}

	$d_urls->add_line(['url' => 'settings', 'script' => $tpf.'settings.php']);
	$d_urls->add_line(['url' => 'delete-user', 'script' => $tpf.'delete-user.php']);

	$d_menu->add_line(['text' => __('Settings'), 'link' => '/n/dashboard/settings/', 'order' => 99]);
	$d_menu->add_line(['text' => __('Log out'), 'link' => '/n/log-out/', 'order' => 100]);

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