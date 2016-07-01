<?php

	function user_comics($id) {
		global $conn;
		$c = '';
		$query = 'SELECT c.url, c.name FROM ns_comics AS c LEFT JOIN ns_user_comic_rel AS r ON c.id = r.comic WHERE r.user = '.$id.' AND r.reltype = \'c\' ORDER BY c.name';
		$result = $conn->query($query);
		$num = $result->num_rows;
		$c .= '<h3>'._('My comics').'</h3>';
		if ($num) {
			$c .= '<ul>';
			while ($arr = $result->fetch_assoc()) {
				$c .= '<li><a href="/'.$arr['url'].'/">'.htmlspecialchars($arr['name']).'</a></li>';
			}
			$c .= '</ul>';
		}
		else {
			$c .= '<p>'.str_replace('{name}', user_name($id), _('{name} hasn\'t created any comics yet.')).'</p>';
		}
		return $c;
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

	function comic_id($url) {
		global $conn;
		$query = 'SELECT id FROM ns_comics WHERE url = \''.($conn->escape_string($url)).'\'';
		$result = $conn->query($query);
		$num = $result->num_rows;
		if ($num) {
			$arr = $result->fetch_assoc();
			return $arr['id']; 
		}
		return 0;
	}

	function comic_url($id) {
		global $conn;
		if (!is_numeric($id)) {
			return false;
		}
		$query = 'SELECT url FROM ns_comics WHERE id = '.$id;
		$result = $conn->query($query);
		$num = $result->num_rows;
		if ($num) {
			$arr = $result->fetch_assoc();
			return $arr['url']; 
		}
		return 0;
	}

	function can_edit_comic($user, $url, $include_admin = true) {
		global $conn;

		if ($include_admin && is_admin($user)) {
			return true;
		}

		$query = 'SELECT id FROM ns_user_comic_rel WHERE user = '.$user.' AND comic = '.comic_id($url).' AND reltype IN (\'c\', \'e\')';
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
		global $conn, $action;

		$comic_id = comic_id($url);

		$action['delete_comic']->run($comic_id);

		$query = 'DELETE FROM ns_user_comic_rel WHERE comic = '.$comic_id;
		$conn->query($query);

		$query = 'DELETE FROM ns_comics WHERE url = \''.($conn->escape_string($url)).'\'';
		$conn->query($query);

	}

	function delete_user_comics($id) {
		global $conn;
		// Deletes all comics by user $id, unless that comic has more than one creator
		$query = 'SELECT comic FROM ns_user_comic_rel WHERE user = '.$id.' AND reltype IN (\'c\', \'e\')';
		$result = $conn->query($query);
		if ($result->num_rows) {
			while ($arr = $result->fetch_assoc()) {
				$comic = $arr['comic'];
				// Check number of listed creators
				$query = 'SELECT id FROM ns_user_comic_rel WHERE comic = '.$comic.' AND reltype IN (\'c\', \'e\')';
				$result2 = $conn->query($query);
				if ($result2->num_rows == 1) {
					delete_comic($comic);
				}
			}
		}
	}