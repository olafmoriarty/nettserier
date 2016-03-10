<?php

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

	function delete_user_comics($id) {
		global $conn;
		// Deletes all comics by user $id, unless that comic has more than one creator
		$query = 'SELECT comic FROM ns_user_comic_rel WHERE user = '.$id.' AND reltype IN (\'c\', \'e\')';
		$result = $conn->query($query);
		if ($result->num_rows) {
			while ($arr = $result->fetch_assoc()) {
				$comic = $arr['comic'];
				// Check number of listed creators
				$query = 'SELECT id FROM ns_user_comic_rel WHERE comic = \''.$comic.'\' AND reltype IN (\'c\', \'e\')';
				$result2 = $conn->query($query);
				if ($result2->num_rows == 1) {
					delete_comic($comic);
				}
			}
		}
	}