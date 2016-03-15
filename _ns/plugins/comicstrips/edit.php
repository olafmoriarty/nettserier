<?php

$folder = strtok('/');

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

elseif (is_numeric($folder)) {
	if ($_POST['edit']) {
		
	}
	// A single comic to edit is selected

	$c .= '<h2>'.__('Edit comic strip or page').'</h2>';
	$c .= '<form method="post" action="/n/dashboard/my-comics/'.$active_comic.'/edit/">';
	$c .= '<p>Description:<br>'."\n".'<textarea name="test" class="wysiwyg"></textarea></p>';
	$c .= '<p><input type="submit" name="save-draft" value="'.__('Save as draft').'"></p>';
	$c .= '<p><input type="submit" name="save-publish" value="'.__('Save and publish').'"></p>';
	$c .= '</form>';
}
else {
	// No comic to edit is selected

	$ns_title = __('Edit comic strips and pages');
	$c .= '<h2>'.__('Edit comic strips and pages').'</h2>';

	$c .= '<p>'.__('To edit a single strip/page, click it below. To edit multiple strips/pages, use the checkboxes to select them and then choose "Edit" from "Bulk actions" below.').'</p>';
	$query = 'SELECT id, imgtype, title, pubtime, published FROM ns_updates WHERE comic = '.$active_comic_id.' AND updtype = \'c\' ORDER BY id DESC';
	$result = $conn->query($query);
	$num = $result->num_rows;

	if ($num){
		$c .= '<form method="post" action="/n/dashboard/my-comics/'.$active_comic.'/edit-strip/">'."\n";
		$c .= '<table>'."\n";
		while ($arr = $result->fetch_assoc()) {
			$c .= '<tr>'."\n";

			// First, a checkbox
			$c .= '<td>';
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
			$c .= '<td>';
			$c .= '<a href="/n/dashboard/my-comics/'.$active_comic.'/edit-strip/'.$arr['id'].'/">';
			$c .= $comic_title;
			$c .= '</a>';
			$c .= '</td>'."\n";


			// Published status
			$c .= '<td>';
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