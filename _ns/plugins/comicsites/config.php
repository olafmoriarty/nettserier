<?php
  // Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	// Setup 
	$d_urls->add_line(['url' => 'new-comic', 'script' => $tpf.'newpage.php']);

	$d_menu->add_line(['text' => __('Create new comic'), 'link' => '/n/dashboard/new-comic/', 'order' => -10]);

	if ($logged_in) {
		if (owns_comics($user_info['id'])) {
			$d_menu->add_line(['text' => __('My comics'), 'link' => '/n/dashboard/my-comics/', 'order' => 0]);
		}
	}

	function owns_comics($id) {
		global $conn;
		$query = 'SELECT id FROM ns_user_comic_rel WHERE user = '.$id.' AND reltype IN (\'c\', \'e\')';
		$result = $conn->query($query);
		$num = $result->num_rows;
		if ($num) {
			return true;
		}
		return false;
	}