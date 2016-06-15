<?php

$ns_title = _('Language manager');
$c .= '<h2>'._('Language manager').'</h2>'."\n";

// Get all plugins from database
$query = 'SELECT fullcode, name FROM ns_languages ORDER BY name';
$result = $conn->query($query);
if ($result !== false) {
	$num = $result->num_rows;
	// Open <table>
	$c .= '<table>'."\n";
	$c .= '<tr>'."\n";
	$c .= '<th>'._('Language').'</th>';
	$c .= '<th>'._('Language code').'</th>';
	$c .= '</tr>'."\n";

	// Array which holds all folders in database
	$folderarr = array();
	if ($num) {
		$result->data_seek(0);

		while ($arr = $result->fetch_assoc()) {
			$c .= '<tr>'."\n";

			// Language name
			$c .= '<td id="language-'.$arr['fullcode'].'-name">';
			/*
			if (isset($_GET['mode']) && $_GET['mode'] == 'edit') {
			$c .= '<form method="post">'."\n";
			$c .= '<input type="text" name="newvalue" value="'.htmlspecialchars($arr['name'], ENT_COMPAT, 'UTF-8').'" />'."\n";
			$c .= '<input type="hidden" name="folder" value="'.$arr['folder'].'" />'."\n";
			$c .= '<input type="hidden" name="field" value="name" />'."\n";
			$c .= '<input type="submit" value="'._('Update').'" />'."\n";
			$c .= '</form>';
			}
			else {
			*/
			$c .= '<a href="/n/admin/plugins/?mode=edit&amp;plugin='.$arr['fullcode'].'&amp;field=name">'.$arr['name'].'</a>';
			/*
			}
			*/
			$c .= '</td>';

			// Plugin folder
			$c .= '<td id="language-'.$arr['fullcode'].'-code">'.$arr['fullcode'].'</td>';

			$c .= '</tr>'."\n";

			// Add language to array
			$folderarr[] = $arr['fullcode'];
		}
	}

	// Add languages not listed in database yet
	$files = array_slice(scandir(NS_PATH.'translation'), 2);
	foreach($files AS $file) {
		if (!in_array($file, $folderarr) && is_dir(NS_PATH.'translation/'.$file) && $file != 'nocache') {
			// This is a language that exists, but that's not listed in the database. So add a row to the table ...

			$c .= '<tr>'."\n";

			// Plugin name (let's use language code as default)
			$c .= '<td id="language-'.$file.'-name">'.$file.'</td>';

			// Plugin folder
			$c .= '<td id="language-'.$file.'-code">'.$file.'</td>';

			$c .= '</tr>'."\n";

			// ... and also add the row to the database.
			$query = 'INSERT INTO ns_languages (root, name, fullcode) VALUES (\''.($conn->escape_string(substr($file, 0, 2))).'\', \''.($conn->escape_string($file)).'\', \''.($conn->escape_string($file)).'\')';
			$conn->query($query);

		}
	}    

	// Close <table>
	$c .= '</table>'."\n";
}