<?php

if (1 == 0) {
}
else {
	// No comic to edit is selected

	$ns_title = __('Edit comic strips and pages');
	$c .= '<h2>'.__('Edit comic strips and pages').'</h2>';

	$query = 'SELECT id, imgtype, title, pubtime, published FROM ns_updates WHERE comic = '.$active_comic_id.' AND updtype = \'c\' ORDER BY id DESC';
	$result = $conn->query($query);
	$num = $result->num_rows;

	if ($num){
		$c .= '<form>'."\n";
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