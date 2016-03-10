<?php
  // Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	// Setup 

	$comicadm_urls = new ArrayHandler;
	$comicadm_menu = new ArrayHandler;

	$d_urls->add_line(['url' => 'new-comic', 'script' => $tpf.'newpage.php']);
	$d_urls->add_line(['url' => 'my-comics', 'script' => $tpf.'mycomics.php']);

	$d_menu->add_line(['text' => __('Create new comic'), 'link' => '/n/dashboard/new-comic/', 'order' => -10]);

	if ($logged_in) {
		if (owns_comics($user_info['id'])) {
			$d_menu->add_line(['text' => __('My comics'), 'link' => '/n/dashboard/my-comics/', 'order' => 0]);
		}
	}

	$comicadm_urls->add_line(['url' => 'delete', 'script' => $tpf.'delete-comic.php']);
	$comicadm_menu->add_line(['text' => __('Go to comic'), 'link' => '/{comic}/', 'order' => -100]);
	$comicadm_menu->add_line(['text' => __('Delete comic'), 'link' => '/n/dashboard/my-comics/{comic}/delete/', 'order' => 999]);

	$delete_concequences->add_line(['text' => __('If you have created any comics: Any comic <strong>where you are the only listed creator or editor</strong> will be deleted. Deleting a comic also deletes all comic strips, blog posts, comments, statistics and everything else that is related to that comic.'), 'order' => 10]);
	$delete_concequences->add_line(['text' => __('Any comic you have created or contributed to <strong>with more than one creator/editor listed</strong> will not be deleted automatically, but you will be removed from its creator list and lose your access to edit this comic. If you want to delete such a comic, you have to do that manually before deleting your profile.'), 'order' => 10.5]);



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

	function can_edit_comic($user, $url) {
		global $conn;
		$query = 'SELECT id FROM ns_user_comic_rel WHERE user = '.$user.' AND comic = \''.($conn->escape_string($url)).'\' AND reltype IN (\'c\', \'e\')';
		$result = $conn->query($query);
		$num = $result->num_rows;
		if ($num) {
			return true;
		}
		return false;
	}

	function comic_name($url) {
		global $conn;
		$query = 'SELECT name FROM ns_comics WHERE url = \''.($conn->escape_string($url)).'\'';
		$result = $conn->query($query);
		while ($arr = $result->fetch_assoc()) {
			return $arr['name'];
		}
		return false;
	}

	function delete_comic($url) {
		global $conn;

		$query = 'DELETE FROM ns_comics WHERE url = \''.($conn->escape_string($url)).'\'';
		$conn->query($query);

		$query = 'DELETE FROM ns_user_comic_rel WHERE comic = \''.($conn->escape_string($url)).'\'';
		$conn->query($query);

		// TODO: Add ArrayHandler functionality to this function

	}