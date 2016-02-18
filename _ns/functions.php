<?php

// ===================================================================

function __($str) {
	return $str;
} // __()


// ===================================================================

function mysql_install_table($tabname, $cols) {
	// Get MySQL connection
	global $conn;

	// Check if table exists.
	$result = $conn->query('SHOW TABLES LIKE \''.$tabname.'\'');
	$tab_exists = $result->num_rows > 0;
	
	// Create table
	if (!$tab_exists) {
		$query = 'CREATE TABLE '.$tabname.' (id INT(10) AUTO_INCREMENT PRIMARY KEY, '.implode(', ', $cols).') CHARSET utf8 COLLATE utf8_danish_ci';
		$result = $conn->query($query);
	}

	// Table exists, so make sure it's not missing any columns
	else {
		foreach ($cols as $col) {
			// First word of string is column name
			$colname = strtok($col, ' ');

			// Does column exist?
			$result = $conn->query('SHOW COLUMNS FROM '.$tabname.' LIKE \''.$colname.'\'');
			$col_exists = $result->num_rows > 0;

			// If not, add it
			if (!$col_exists) {
				$query = 'ALTER TABLE '.$tabname.' ADD COLUMN '.$col;
				$result = $conn->query($query);
			}

		}
	}
} // mysql_install_table()

// ===================================================================

function validate_input($arr) {
	global $conn;
	if ($arr['check'] == 'unique') {
		// Check if value is already in use in the specified field
		$query = 'SELECT id FROM '.$arr['table'].' WHERE LOWER('.$arr['field'].') = \''.$conn->real_escape_string(strtolower($arr['field'])).'\'';
		$result = $conn->query($query);
		$num = $result->num_rows;
		if ($num) {
			return true;
		}
		else {
			return false;
		}
	}
}
?>