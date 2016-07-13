<?php

$folder = strtok('/');
$comicname = comic_name($active_comic);
$max_file_size = 10 * 1024 * 1024;
$accepted_extensions = ['jpg', 'jpeg', 'gif', 'png'];

// HANDLING BULK CHANGES

if (isset($_POST['bulk']) && $_POST['bulk'] && $_POST['bulk'] != 'edit' && $_POST['bulk'] != 'delete') {
	// Which checkboxes are checked?
	$formfields = array_keys($_POST);
	$checked = array();
	$formnum = count($formfields);
	for ($i = 0; $i < $formnum; $i++) {
		if (substr($formfields[$i], 0, 6) == 'check-' && is_numeric($this_id = substr($formfields[$i], 6))) {
			$checked[] = $this_id;
		}
	}

	// How many boxes are checked?
	$checked_num = count($checked);

	// Abort and go back if no boxes are checked
	if (!$checked_num) {
		header('Location: '.NS_DOMAIN.'/n/dashboard/my-comics/'.$active_comic.'/edit-strip/');
		exit;
	
	}
	elseif ($_POST['bulk'] == 'publish') {
		// Change 'published' for all selected IDs to 1
		$query = 'UPDATE ns_updates SET published = 1 WHERE id IN ('.implode(', ', $checked).') AND comic = '.$active_comic_id;
		$conn->query($query);
		// Also, if any of these lack a pubtime, set it to NOW()
		$query = 'UPDATE ns_updates SET pubtime = NOW() WHERE id IN ('.implode(', ', $checked).') AND comic = '.$active_comic_id.' AND pubtime IS NULL';
		$conn->query($query);
		header('Location: '.NS_DOMAIN.'/n/dashboard/my-comics/'.$active_comic.'/edit-strip/');
		exit;
	}
	elseif ($_POST['bulk'] == 'draft') {
		// Change 'published' for all selected IDs to 0
		$query = 'UPDATE ns_updates SET published = 0 WHERE id IN ('.implode(', ', $checked).') AND comic = '.$active_comic_id;
		$conn->query($query);
		header('Location: '.NS_DOMAIN.'/n/dashboard/my-comics/'.$active_comic.'/edit-strip/');
		exit;
	}
}

elseif (isset($_POST['edit-ids'])) {
	// An edit form is submitted

	// Which strips are being edited?
	$ids = unserialize($_POST['edit-ids']);

	$query = 'SELECT id FROM ns_updates WHERE comic = '.$active_comic_id.' AND id IN ('.$conn->real_escape_string(implode(', ', $ids)).') AND updtype = \'c\' ORDER BY filename';
	$result = $conn->query($query);
	$num = $result->num_rows;

	if ($num) {

		$values = array();

		$ids_sorted = array();
		$order_arr = array();

		while ($arr = $result->fetch_assoc()) {
			
			// For each row to be edited ...

			$id = $arr['id'];
			$values[$id] = array();

			$ids_sorted[] = $id;
			$order_arr[] = $_POST['comic-order-'.$id];

			// Title
			$title = $_POST['comic-title-'.$id];
			$values[$id]['title'] = mysql_string($title);

			// Slug
			if ($title) {
				$values[$id]['slug'] = mysql_string(slugify($title).'-'.$id);
			}
			else {
				$values[$id]['slug'] = mysql_string($id);
			}

			// Description
			$desc = $_POST['comic-desc-'.$id];
			$desc = $filter['html']->run($desc);
			$values[$id]['text'] = mysql_string($desc);

			// Published status
			$values[$id]['published'] = 0;
			if ($_POST['save-publish']) {
				$values[$id]['published'] = 1;
			}


			// Time
			$unixtime = time();
			if ($_POST['comic-pubradio-'.$id] == 'time' && $_POST['comic-datetime-'.$id.'-date'] && $_POST['comic-datetime-'.$id.'-time']) {
				$pubtime = $_POST['comic-datetime-'.$id.'-date'].' '.$_POST['comic-datetime-'.$id.'-time'];
				$unixtime = strtotime($pubtime);
				if ($unixtime === false) {
					$unixtime = time();
				} 
			}
			elseif ($_POST['comic-pubradio-'.$id] == 'time' && $_POST['comic-datetime-'.$id.'-date']) {
				$pubtime = $_POST['comic-datetime-'.$id.'-date'].' 00:00:00';
				$unixtime = strtotime($pubtime);
				if ($unixtime === false) {
					$unixtime = time();
				} 
			}
			$pubtime = date('Y-m-d H:i:s', $unixtime);

			// Save pubtime only if radio button is set to "choose time" or if status is set to "publish" (i.e. do NOT save time if radiobutton is left at "now" and we're saving as draft)
			if ($_POST['save-publish'] || $_POST['comic-pubradio-'.$id] == 'time') {
				$values[$id]['pubtime'] = mysql_string($pubtime);
			}

			// Replace image file if new submitted
			if ($_FILES['comic-file-'.$id]['tmp_name'] && getimagesize($_FILES['comic-file-'.$id]['tmp_name']) !== false && $_FILES['comic-file-'.$id]['size'] <= $max_file_size && in_array($extension = strtolower(pathinfo($_FILES['comic-file-'.$id]['name'], PATHINFO_EXTENSION)), $accepted_extensions)) {
			
				move_uploaded_file($_FILES['comic-file-'.$id]['tmp_name'], NS_PATH.'files/'.md5($id . $extension).'.'.$extension);

				// What's the image type?
				$values[$id]['imgtype'] = mysql_string($extension);

				// What's the original filename?
				$values[$id]['filename'] = mysql_string($_FILES['comic-file-'.$id]['name']);
			}
		}



		if ($_POST['schedule'] == 'sametime' && $_POST['save-publish']) {
			// Set ALL pubtimes to the same time! Breathe. We can do this.

			$unixtime = time();
			if ($_POST['schedule-sametime'] == 'time' && ($_POST['sametime-datetime-date'] || $_POST['sametime-datetime-time'])) {
				$pubtime = $_POST['sametime-datetime-date'].' '.$_POST['sametime-datetime-time'];
				$unixtime = strtotime($pubtime);
				if ($unixtime === false) {
					$unixtime = time();
				} 
			}
			$pubtime = date('Y-m-d H:i:s', $unixtime);

			foreach ($ids as $id) {
				$values[$id]['pubtime'] = mysql_string($pubtime);
			}
		}

		// Sort ids by order (in case user pressed "move up" or "move down"
		array_multisort($order_arr, SORT_NUMERIC, $ids_sorted);

		$action['edit_strips_submit']->run();

		// Phew! We should be ready to put stuff into the database now.
		foreach ($ids_sorted as $id) {
			if (count($values[$id])) {
				$updatestring = '';
				foreach($values[$id] as $key => $value) {
					if ($updatestring) {
						$updatestring .= ', ';
					}
					$updatestring .= $key.'='.$value;
				}
				$query = 'UPDATE ns_updates SET '.$updatestring.' WHERE id = '.$id;
				$conn->query($query);
			}
		}


	
	}

	// Return to edit page
	header('Location: '.NS_DOMAIN.'/n/dashboard/my-comics/'.$active_comic.'/edit-strip/');
	exit;

}

elseif ($folder == 'delete' || (isset($_POST['bulk']) && $_POST['bulk'] == 'delete')) {
	if ($_POST['delete_ids']) {
		// Which strips are being deleted?
		$ids = unserialize($_POST['delete_ids']);

		// Check if they exist

		$query = 'SELECT id FROM ns_updates WHERE comic = '.$active_comic_id.' AND id IN ('.$conn->real_escape_string(implode(', ', $ids)).') AND updtype = \'c\'';
		$result = $conn->query($query);
		$num = $result->num_rows;

		if ($num) {

			// Run delete strip function for all selected comics ...

			while ($arr = $result->fetch_assoc()) {
				delete_strip($arr['id']);
			}

		}
		// Return to edit page
		header('Location: '.NS_DOMAIN.'/n/dashboard/my-comics/'.$active_comic.'/edit-strip/');
		exit;

	}
	else {
		// The user has clicked "Delete", but not confirmed yet

		$whereclause = false;
		if ($folder == 'delete') {
			$strip_id = strtok('/');
			if ($strip_id && is_numeric($strip_id)) {
				// A single comic to edit is selected.
				$whereclause = 'id = '.$strip_id;
			}
		}
		elseif (isset($_POST['bulk']) && $_POST['bulk'] == 'delete') {

			$formfields = array_keys($_POST);
			$checked = array();
			$formnum = count($formfields);
			for ($i = 0; $i < $formnum; $i++) {
				if (substr($formfields[$i], 0, 6) == 'check-' && is_numeric($this_id = substr($formfields[$i], 6))) {
					$checked[] = $this_id;
				}
			}

			// How many boxes are checked?
			$checked_num = count($checked);

			if ($checked_num) {
				$whereclause = 'id IN ('.implode(', ', $checked).')';
			}
			
		}
		
		$query = 'SELECT id, imgtype, title, filename FROM ns_updates WHERE comic = '.$active_comic_id.' AND '.$whereclause.' AND updtype = \'c\' ORDER BY filename';
		$result = $conn->query($query);
		$num = $result->num_rows;

		if ($num) {

			$ns_title = _('Delete comic strips or pages');
			$c .= '<h2>'._('Delete comic strips or pages').'</h2>'."\n";

			$c .= '<form method="post" action="/n/dashboard/my-comics/'.$active_comic.'/edit-strip/delete">';
			$c .= '<p>'._('Are you sure you want to delete these comic strips?').'</p>';
			$c .= '<ul>';
			$ids = array();
			while ($arr = $result->fetch_assoc()) {
				$c .= '<li>';
				if ($arr['title']) {
					$c .= htmlspecialchars($arr['title']);
				}
				else {
					$c .= _('No title');
				}
				$c .= ' - <a href="/_ns/files/'.md5($arr['id'] . $arr['imgtype']).'.'.$arr['imgtype'].'" target="_blank">'.htmlspecialchars($arr['filename']).'</a>';
				$c .= '</li>';
				$ids[] = $arr['id'];
			}
			$c .= '</ul>';
			$c .= '<input type="hidden" name="delete_ids" value="'.htmlspecialchars(serialize($ids)).'">';
			$c .= '<p><input type="submit" value="'._('Yes, delete them!').'"></p>';
			$c .= '</form>';
		}
		else {
			header('Location: '.NS_DOMAIN.'/n/dashboard/my-comics/'.$active_comic.'/edit-strip/');
			exit;
		}
	}
}

elseif ($folder || (isset($_POST['bulk']) && $_POST['bulk'] == 'edit')) {

	$whereclause = false;
	if ($folder && is_numeric($folder)) {
		// A single comic to edit is selected.
		$whereclause = 'id = '.$folder;
	}
	elseif ($folder == 'drafts') {
		$whereclause = 'published = 0';
	}
	elseif (isset($_POST['bulk']) && $_POST['bulk'] == 'edit') {

		$formfields = array_keys($_POST);
		$checked = array();
		$formnum = count($formfields);
		for ($i = 0; $i < $formnum; $i++) {
			if (substr($formfields[$i], 0, 6) == 'check-' && is_numeric($this_id = substr($formfields[$i], 6))) {
				$checked[] = $this_id;
			}
		}

		// How many boxes are checked?
		$checked_num = count($checked);

		if ($checked_num) {
			$whereclause = 'id IN ('.implode(', ', $checked).')';
		}
		
	}
	
	$query = 'SELECT id, imgtype, title, text, pubtime, published, filename FROM ns_updates WHERE comic = '.$active_comic_id.' AND '.$whereclause.' AND updtype = \'c\' ORDER BY filename';
	$result = $conn->query($query);
	$num = $result->num_rows;

	if ($num){
		// Comic exists

		if ($folder == 'drafts') {
			$ns_title = _('Edit drafts');
			if ($_GET['uploaded']) {
				$c .= '<h2>'.str_replace('{comic}', htmlspecialchars($comicname), _('Upload new comic strip or page for "{comic}"')).'</h2>'."\n";
				$c .= '<h3>'._('Step two: Edit metadata').'</h3>'."\n";
				$c .= '<p>'._('(Psst! Your comic strips are now uploaded and saved as drafts. If for some reason you abort the operation now, you can always come back to this page by clicking "Edit drafts" under "Edit comic strips and pages" in your dashboard.)').'</p>';
			}
			else {
				$c .= '<h2>'._('Edit drafts').'</h2>'."\n";
			}
		}
		elseif ($num > 1) {
			$ns_title = _('Edit comic strips or pages');
			$c .= '<h2>'._('Edit comic strips or pages').'</h2>'."\n";
		}
		else {
			$ns_title = _('Edit comic strip or page');
			$c .= '<h2>'._('Edit comic strip or page').'</h2>'."\n";
		}
		$c .= '<form method="post" action="/n/dashboard/my-comics/'.$active_comic.'/edit-strip/" enctype="multipart/form-data">'."\n";

		$i = 0;
		$ids = array();
		$has_dates = false;

		while ($old_values = $result->fetch_assoc()) {

			$id = $old_values['id'];
			$ids[] = $id;

			$c .= '<div class="edit-comic">';
			// What's the title
			if ($old_values['title']) {
				$comic_title = htmlspecialchars($old_values['title']);
			}
			else {
				$comic_title = _('No title');
			}

			$c .= '<p class="edit-image-preview"><img src="/_ns/files/'.md5($old_values['id'] . $old_values['imgtype']).'.'.$old_values['imgtype'].'" alt="'.$comic_title.'"></p>'."\n";

			$c .= '<fieldset class="edit-comic-info">'."\n";
			$c .= '<legend>'.htmlspecialchars($old_values['filename']).'</legend>'."\n";

			$c .= input_field(['name' => 'comic-title-'.$id, 'text' => _('Title (optional)'), 'value' => $old_values['title']]);
			$c .= '<fieldset class="pubtime-single">'."\n";
			$c .= '<legend>'._('Publication time:').'</legend>'."\n";
			$c .= '<ul><li><input name="comic-pubradio-'.$id.'" type="radio" value="now"';
			if (!$old_values['pubtime']) {
				$c .= ' checked="checked"';
			}
			$c .= '> '._('The moment I press the "Publish" button"').'</li>'."\n";
			$c .= '<li><input name="comic-pubradio-'.$id.'" type="radio" value="time"';
			if ($old_values['pubtime']) {
				$c .= ' checked="checked"';
				$has_dates = true;
			}
			$c .= '> At the following time:<br>'."\n".input_field(['name' => 'comic-datetime-'.$id, 'type' => 'datetime', 'class' => 'dateandtime', 'value' => $old_values['pubtime']]).'</li></ul></fieldset>'."\n";

			$c .= input_field(['name' => 'comic-desc-'.$id, 'text' => _('Description (optional)'), 'type' => 'textarea', 'class' => 'wysiwyg', 'value' => $old_values['text']]);
			$c .= input_field(['name' => 'comic-file-'.$id, 'type' => 'file', 'text' => _('Image file (leave blank to keep the current file)')]);

			$c .= '<input type="hidden" name="comic-order-'.$id.'" class="order" value="'.(++$i).'">'."\n";
			$c .= '</fieldset>'."\n";
			$c .= '</div>'."\n";
		}
		// Which comics are we editing?
		$c .= '<input type="hidden" name="edit-ids" value="'.htmlspecialchars(serialize($ids)).'">'."\n";

		if ($num > 1) {
			$body_js->add_js(['js' => '/_ns/plugins/comicstrips/comic-strips-mass-edit.js']);
			$c .= '<fieldset class="bulk-schedule-change">'."\n";
			$c .= '<legend>'._('Bulk schedule change').'</legend>'."\n";
			$c .= '<p>'.str_replace('{num}', $num, _('You are editing {num} comic strips or pages. You can select a publication time for each strip/page separately, or you can use one of these bulk options:')).'</p>'."\n";
			$c .= '<ul>'."\n";
			$c .= '<li><input type="radio" name="schedule" value="nobulk"';
			if ($has_dates) {
				$c .= ' checked="checked"';
			}
			$c .= '> '._('Choose publication time individually for each strip').'</li>';
			$c .= '<li><input type="radio" name="schedule" value="sametime"';
			if (!$has_dates) {
				$c .= ' checked="checked"';
			}
			$c .= '> '._('Publish all comic strips at the same time');
			$c .= '<ul>'."\n";
			$c .= '<li><input type="radio" name="schedule-sametime" value="now" checked="checked"> '._('The moment I press the "Publish" button').'</li>'."\n";
			$c .= '<li><input type="radio" name="schedule-sametime" value="time"> '._('At the following time:').'<br>'."\n";
			$c .= input_field(['name' => 'sametime-datetime', 'type' => 'datetime', 'class' => 'dateandtime']);
			$c .= '</li>';
			$c .= '</ul>';
			$c .= '</li>';

			$schedulefirst = '<ul>'."\n".
				'<li><input type="radio" name="schedule-schedule-first" value="now" checked="checked"> '._('The moment I press the "Publish" button').'</li>'."\n".
				'<li><input type="radio" name="schedule-schedule-first" value="time"> '._('At the following time:').'<br>'."\n".
				input_field(['name' => 'schedule-first-datetime', 'type' => 'datetime', 'class' => 'dateandtime']).
				'</li>'.
				'</ul>';

			$weekdays_array = [_('Monday'), _('Tuesday'), _('Wednesday'), _('Thursday'), _('Friday'), _('Saturday'), _('Sunday')];
			$weekdays_checked = [1, 2, 3, 4, 5];			

			$weekdays = '<ul class="weekdays">';
			for ($i = 0; $i < 7; $i++) {
				$weekdays .= '<li><input type="checkbox" name="schedule-weekday-'.($i + 1).'"';
				if (in_array(($i + 1), $weekdays_checked)) {
					$weekdays .= ' checked="checked"';
				}
				$weekdays .= '> '.$weekdays_array[$i].'</li>';
			}
			$weekdays .= '</ul>';

			$c .= '<li><input type="radio" name="schedule" value="schedule"> '._('Set a publication schedule');
			$c .= '<ul>';
			$c .= '<li>'.str_replace('{time}', $schedulefirst, _('The first strip should be published {time}')).'</li>';
			$c .= '<li>'.str_replace('{weekdays}', $weekdays, _('After that, publish a new strip every {weekdays}')).'</li>';
			$c .= '</ul>';
			$c .= '</li>';
/*			$c .= '<li><input type="radio" name="schedule" value="album"> '._('Create an album');
			$c .= '<ul>';
			$c .= '<li>'.input_field(['name' => 'album_title', 'text' => _('Album title')]).'</li>';
			$c .= '<li>'.input_field(['name' => 'album_desc', 'text' => _('Album description'), 'type' => 'textarea', 'class' => 'wysiwyg']).'</li>';
			$c .= '<li>'._('Album publication time');
			$c .= '<ul>'."\n";
			$c .= '<li><input type="radio" name="schedule-album" value="now" checked="checked"> '._('The moment I press the "Publish" button').'</li>'."\n";
			$c .= '<li><input type="radio" name="schedule-album" value="time"> '._('At the following time:').'<br>'."\n";
			$c .= input_field(['name' => 'album-datetime', 'type' => 'datetime', 'class' => 'dateandtime']);
			$c .= '</li>';
			$c .= '</ul>';
			$c .= '</li>';
			$c .= '</ul>';
			$c .= '</li>';
			*/
			$c .= '</ul>';
			$c .= '</fieldset>'."\n";
		}


		$c .= '<p><input type="submit" name="save-draft" value="'._('Save as draft').'"></p>'."\n";
		$c .= '<p><input type="submit" name="save-publish" value="'._('Save and publish').'"></p>'."\n";
		$c .= '</form>';
	}
	else {
		header('Location: '.NS_DOMAIN.'/n/dashboard/my-comics/'.$active_comic.'/edit-strip/');
		exit;
	}
}
else {
	// No comic to edit is selected

	$ns_title = _('Edit comic strips and pages');
	$c .= '<h2>'._('Edit comic strips and pages').'</h2>';

	$query = 'SELECT id, imgtype, title, pubtime, published, filename FROM ns_updates WHERE comic = '.$active_comic_id.' AND updtype = \'c\' ORDER BY published ASC, pubtime DESC, id DESC';
	
	$result = $conn->query($query);
	$num = $result->num_rows;

	if ($num){
		$c .= '<p>'._('To edit a single strip/page, click it below. To edit multiple strips/pages, use the checkboxes to select them and then choose "Edit" from "Bulk actions" below.').'</p>';

		$query = 'SELECT id FROM ns_updates WHERE comic = '.$active_comic_id.' AND updtype = \'c\' AND published = 0';
		$drafts_result = $conn->query($query);
		if ($drafts_result->num_rows) {
			$c .= '<p><a href="/n/dashboard/my-comics/'.$active_comic.'/edit-strip/drafts/">'._('Edit all drafts').'</a></p>';
		}

		$c .= '<form method="post" action="/n/dashboard/my-comics/'.$active_comic.'/edit-strip/">'."\n";
		$c .= '<table class="strips-to-edit">'."\n";
		while ($arr = $result->fetch_assoc()) {
			$c .= '<tr>'."\n";

			// First, a checkbox
			$c .= '<td class="checkbox-cell">';
			$c .= '<input type="checkbox" name="check-'.$arr['id'].'" id="check-'.$arr['id'].'">';
			$c .= '</td>'."\n";

			// What's the title
			if ($arr['title']) {
				$comic_title = htmlspecialchars($arr['title']);
			}
			else {
				$comic_title = _('No title');
			}

			// Image
			$c .= '<td class="thumbnail-cell">';
			$c .= '<div class="thumbnail-box">';
			$c .= '<a href="/n/dashboard/my-comics/'.$active_comic.'/edit-strip/'.$arr['id'].'/">';
			$c .= '<img src="/_ns/files/'.md5($arr['id'] . $arr['imgtype']).'.'.$arr['imgtype'].'" alt="'.$comic_title.'" class="thumbnail">';
			$c .= '</a>';
			$c .= '</div>';
			$c .= '</td>'."\n";

			// Title
			$c .= '<td class="title-cell">';
			$c .= '<h3><a href="/n/dashboard/my-comics/'.$active_comic.'/edit-strip/'.$arr['id'].'/">';
			$c .= $comic_title;
			$c .= '</a></h3>';

			// Filename
			$c .= '<p>'."\n".htmlspecialchars($arr['filename']).'</p>';
			$c .= '<nav>';
			$c .= str_replace(['{id}', '{comic}'], [$arr['id'], $active_comic], $edit_comic_single_menu->return_ul());
			$c .= '</nav>';
			$c .= '</td>'."\n";


			// Published status
			$c .= '<td class="date-cell">';
			if (!$arr['published']) {
				$c .= _('Draft');
			}
			elseif (time() < strtotime($arr['pubtime'])) {
				$c .= str_replace('{time}', $arr['pubtime'], _('To be published {time}'));
			}
			else {
				$c .= str_replace('{time}', $arr['pubtime'], _('Published {time}'));
			}
			$c .= '</td>'."\n";

			$c .= '</tr>'."\n";
		}
		$c .= '</table>'."\n";

		$c .= '<p>'._('Bulk actions:').'<br>'."\n";
		$c .= '<select name="bulk">'."\n";
		$c .= '<option value="">'._('Select an action ...').'</option>';
		$c .= '<option value="edit">'._('Edit selected strips/pages').'</option>';
		$c .= '<option value="publish">'._('Change status of selected strips/pages to Published').'</option>';
		$c .= '<option value="draft">'._('Change status of selected strips/pages to Draft').'</option>';
		$c .= '<option value="delete">'._('Delete selected strips/pages').'</option>';
		// Arrayhandler?
		$c .= '</select></p>'."\n";
		$c .= '<p><input type="submit" value="'._('Apply').'"></p>';
		$c .= '</form>'."\n";
			
	}
	else {
		// There are no comics
		$c .= '<p>'._('You haven\'t uploaded any comics yet!');
	}
}