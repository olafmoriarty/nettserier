<?php

$folder = strtok('/');
$comicname = comic_name($active_comic);

// HANDLING BULK CHANGES

if (isset($_POST['bulk']) && $_POST['bulk'] && $_POST['bulk'] != 'edit') {
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

elseif ($_POST['edit-ids']) {
	// An edit form is submitted

	// Which strips are being edited?
	$ids = unserialize($_POST['edit-ids']);

	print_r($ids);
	exit;
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
	
	$query = 'SELECT id, imgtype, title, pubtime, published, filename FROM ns_updates WHERE comic = '.$active_comic_id.' AND '.$whereclause.' AND updtype = \'c\'';
	$result = $conn->query($query);
	$num = $result->num_rows;

	if ($num){
		// Comic exists

		if ($folder == 'drafts') {
			$ns_title = __('Edit drafts');
			if ($_GET['uploaded']) {
				$c .= '<h2>'.str_replace('{comic}', htmlspecialchars($comicname), __('Upload new comic strip or page for "{comic}"')).'</h2>'."\n";
				$c .= '<h3>'.__('Step two: Edit metadata').'</h3>'."\n";
			}
			else {
				$c .= '<h2>'.__('Edit drafts').'</h2>'."\n";
			}
		}
		elseif ($num > 1) {
			$ns_title = __('Edit comic strips or pages');
			$c .= '<h2>'.__('Edit comic strips or pages').'</h2>'."\n";
		}
		else {
			$ns_title = __('Edit comic strip or page');
			$c .= '<h2>'.__('Edit comic strip or page').'</h2>'."\n";
		}
		$c .= '<form method="post" action="/n/dashboard/my-comics/'.$active_comic.'/edit-strip/">'."\n";

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
				$has_dates = true;
			}
			else {
				$comic_title = __('No title');
			}

			$c .= '<p class="edit-image-preview"><img src="/_ns/files/'.md5($old_values['id'] . $old_values['imgtype']).'.'.$old_values['imgtype'].'" alt="'.$comic_title.'"></p>'."\n";

			$c .= '<fieldset class="edit-comic-info">'."\n";
			$c .= '<legend>'.htmlspecialchars($old_values['filename']).'</legend>'."\n";


			$c .= input_field(['name' => 'comic-title-'.$id, 'text' => __('Title (optional)'), 'value' => $old_values['title']]);
			$c .= '<fieldset class="pubtime-single">'."\n";
			$c .= '<legend>'.__('Publication time:').'</legend>'."\n";
			$c .= '<ul><li><input name="comic-pubradio-'.$id.'" type="radio"';
			if (!$old_values['pubtime']) {
				$c .= ' checked="checked"';
			}
			$c .= '> '.__('The moment I press the "Publish" button"').'</li>'."\n";
			$c .= '<li><input name="comic-pubradio-'.$id.'" type="radio"';
			if ($old_values['pubtime']) {
				$c .= ' checked="checked"';
			}
			$c .= '> At the following time:<br>'."\n".input_field(['name' => 'comic-datetime-'.$id, 'type' => 'datetime', 'class' => 'dateandtime', 'value' => $old_values['pubtime']]).'</li></ul></fieldset>'."\n";

			$c .= input_field(['name' => 'comic-desc-'.$id, 'text' => __('Description (optional)'), 'type' => 'textarea', 'class' => 'wysiwyg', 'value' => $old_values['text']]);
			$c .= '<input type="hidden" name="order" class="comic-order-'.$i.'" value="'.(++$i).'">'."\n";
			$c .= '</fieldset>'."\n";
			$c .= '</div>'."\n";
		}
		// Which comics are we editing?
		$c .= '<input type="hidden" name="edit-ids" value="'.htmlspecialchars(serialize($ids)).'">'."\n";

		if ($num > 1) {
			$body_js->add_js('/_ns/plugins/comicstrips/comic-strips-mass-edit.js');
			$c .= '<fieldset class="bulk-schedule-change">'."\n";
			$c .= '<legend>'.__('Bulk schedule change').'</legend>'."\n";
			$c .= '<p>'.str_replace('{num}', $num, __('You are editing {num} comic strips or pages. You can select a publication time for each strip/page separately, or you can use one of these bulk options:')).'</p>'."\n";
			$c .= '<ul>'."\n";
			$c .= '<li><input type="radio" name="schedule" value="nobulk"';
			if ($hasdates) {
				$c .= ' checked="checked"';
			}
			$c .= '> '.__('Choose publication time individually for each strip').'</li>';
			$c .= '<li><input type="radio" name="schedule" value="sametime"';
			if (!$hasdates) {
				$c .= ' checked="checked"';
			}
			$c .= '> '.__('Publish all comic strips at the same time');
			$c .= '<ul>'."\n";
			$c .= '<li><input type="radio" name="schedule-sametime" value="now" checked="checked"> '.__('The moment I press the "Publish" button').'</li>'."\n";
			$c .= '<li><input type="radio" name="schedule-sametime" value="time"> '.__('At the following time:').'<br>'."\n";
			$c .= input_field(['name' => 'sametime-datetime', 'type' => 'datetime', 'class' => 'dateandtime']);
			$c .= '</li>';
			$c .= '</ul>';
			$c .= '</li>';

			$schedulefirst = '<ul>'."\n".
				'<li><input type="radio" name="schedule-schedule-first" value="now" checked="checked"> '.__('The moment I press the "Publish" button').'</li>'."\n".
				'<li><input type="radio" name="schedule-schedule-first" value="time"> '.__('At the following time:').'<br>'."\n".
				input_field(['name' => 'schedule-first-datetime', 'type' => 'datetime', 'class' => 'dateandtime']).
				'</li>'.
				'</ul>';

			$weekdays_array = [__('Monday'), __('Tuesday'), __('Wednesday'), __('Thursday'), __('Friday'), __('Saturday'), __('Sunday')];
			$weekdays_checked = [1, 2, 3, 4, 5];			

			$weekdays = '<ul class="weekdays">';
			for ($i = 0; $i < 7; $i++) {
				$weekdays .= '<li><input type="checkbox" name="schedule-weekday-'.($i + 1).'"';
				if (in_array(($i + 1), $weekdays_checked)) {
					$weekdays .= ' checked="checked"';
				}
				$weekdays .= '> '.__($weekdays_array[$i]).'</li>';
			}
			$weekdays .= '</ul>';

			$c .= '<li><input type="radio" name="schedule" value="schedule"> '.__('Set a publication schedule');
			$c .= '<ul>';
			$c .= '<li>'.str_replace('{time}', $schedulefirst, __('The first strip should be published {time}')).'</li>';
			$c .= '<li>'.str_replace('{weekdays}', $weekdays, __('After that, publish a new strip every {weekdays}')).'</li>';
			$c .= '</ul>';
			$c .= '</li>';
			$c .= '<li><input type="radio" name="schedule" value="album"> '.__('Create an album');
			$c .= '<ul>';
			$c .= '<li>'.input_field(['name' => 'album_title', 'text' => __('Album title')]).'</li>';
			$c .= '<li>'.input_field(['name' => 'album_desc', 'text' => __('Album description'), 'type' => 'textarea', 'class' => 'wysiwyg']).'</li>';
			$c .= '<li>'.__('Album publication time');
			$c .= '<ul>'."\n";
			$c .= '<li><input type="radio" name="schedule-album" value="now" checked="checked"> '.__('The moment I press the "Publish" button').'</li>'."\n";
			$c .= '<li><input type="radio" name="schedule-album" value="time"> '.__('At the following time:').'<br>'."\n";
			$c .= input_field(['name' => 'album-datetime', 'type' => 'datetime', 'class' => 'dateandtime']);
			$c .= '</li>';
			$c .= '</ul>';
			$c .= '</li>';
			$c .= '</ul>';
			$c .= '</li>';
			$c .= '</ul>';
			$c .= '</fieldset>'."\n";
		}


		$c .= '<p><input type="submit" name="save-draft" value="'.__('Save as draft').'"></p>'."\n";
		$c .= '<p><input type="submit" name="save-publish" value="'.__('Save and publish').'"></p>'."\n";
		$c .= '</form>';
	}
}
else {
	// No comic to edit is selected

	$ns_title = __('Edit comic strips and pages');
	$c .= '<h2>'.__('Edit comic strips and pages').'</h2>';

	$c .= '<p>'.__('To edit a single strip/page, click it below. To edit multiple strips/pages, use the checkboxes to select them and then choose "Edit" from "Bulk actions" below.').'</p>';

	$query = 'SELECT id, imgtype, title, pubtime, published, filename FROM ns_updates WHERE comic = '.$active_comic_id.' AND updtype = \'c\' ORDER BY published ASC, pubtime DESC, id DESC';
	
	$result = $conn->query($query);
	$num = $result->num_rows;

	if ($num){
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
				$comic_title = __('No title');
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
			$c .= '<a href="/n/dashboard/my-comics/'.$active_comic.'/edit-strip/'.$arr['id'].'/">';
			$c .= $comic_title;
			$c .= '</a>';

			// Filename
			$c .= '<br>'."\n".htmlspecialchars($arr['filename']);
			$c .= '</td>'."\n";


			// Published status
			$c .= '<td class="date-cell">';
			if (!$arr['published']) {
				$c .= __('Draft');
			}
			elseif (time() < strtotime($arr['pubtime'])) {
				$c .= str_replace('{time}', $arr['pubtime'], __('To be published {time}'));
			}
			else {
				$c .= str_replace('{time}', $arr['pubtime'], __('Published {time}'));
			}
			$c .= '</td>'."\n";

			$c .= '</tr>'."\n";
		}
		$c .= '</table>'."\n";

		$c .= '<p>'.__('Bulk actions:').'<br>'."\n";
		$c .= '<select name="bulk">'."\n";
		$c .= '<option value="">'.__('Select an action ...').'</option>';
		$c .= '<option value="edit">'.__('Edit selected strips/pages').'</option>';
		$c .= '<option value="publish">'.__('Change status of selected strips/pages to Published').'</option>';
		$c .= '<option value="draft">'.__('Change status of selected strips/pages to Draft').'</option>';
		$c .= '<option value="delete">'.__('Delete selected strips/pages').'</option>';
		// Arrayhandler?
		$c .= '</select></p>'."\n";
		$c .= '<p><input type="submit" value="'.__('Apply').'"></p>';
		$c .= '</form>'."\n";
			
	}
	else {
		// There are no comics
		$c .= '<p>'.__('You haven\'t uploaded any comics yet!');
	}
}