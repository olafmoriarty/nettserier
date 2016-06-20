<?php

if (isset($_POST['delete-comments'])) {
	// Which checkboxes are checked?
	$formfields = array_keys($_POST);
	$checked = array();
	$formnum = count($formfields);
	for ($i = 0; $i < $formnum; $i++) {
		if (substr($formfields[$i], 0, 8) == 'comment-' && is_numeric($this_id = substr($formfields[$i], 8))) {
			$checked[] = $this_id;
		}
	}

	// How many boxes are checked?
	$checked_num = count($checked);

	if ($checked_num) {
		for ($i = 0; $i < $checked_num; $i++) {
			$query = 'DELETE ns_comments FROM ns_comments LEFT JOIN ns_updates ON ns_comments.parent = ns_updates.id WHERE ns_comments.id = '.$checked[$i].' AND ns_updates.comic = '.$active_comic_id;
			$conn->query($query);
		}
	}
	header('Location: '.NS_DOMAIN.'/n/dashboard/my-comics/'.$active_comic.'/comments/');
	exit;
}

$rows = 50;


		$query = 'SELECT SQL_CALC_FOUND_ROWS c.id, c.user, u.username, c.text, c.regtime, c.oldauthor, upd.title, upd.slug FROM ns_comments AS c LEFT JOIN ns_users AS u on c.user = u.id LEFT JOIN ns_updates AS upd ON upd.id = c.parent WHERE upd.comic = '.$active_comic_id.' ORDER BY c.regtime DESC '.limitstring($rows);
		$result = $conn->query($query);

		$query = 'SELECT FOUND_ROWS()';
		$fr_result = $conn->query($query);
		$fr_arr = $fr_result->fetch_row();
		$total_rows = $fr_arr[0];
		$pagecount = ceil($total_rows / $rows);

		$num = $result->num_rows;

		$c .= '<h2>'.str_replace('{comic}', comic_name($active_comic), _('Comments for {comic}')).'</h2>';
		if ($num) {
			$navigation = limitstring_nav($pagecount);
			$c .= $navigation;

			$c .= '<section class="comment-section">';
			$c .= '<form method="post" action="'.NS_URL.'">';
			// Print comments
			while ($r_arr = $result->fetch_assoc()) {
				$c .= '<article class="comment">';
				$c .= '<div class="comment-header">';
				if ($r_arr['user']) {
					$c .= '<p class="comment_author"><input type="checkbox" name="comment-'.$r_arr['id'].'" id="comment-'.$r_arr['id'].'"> <a href="/n/users/'.$r_arr['user'].'/">'.htmlspecialchars($r_arr['username']).'</a></p>';
				}
				else {
					$c .= '<p class="comment_author"><input type="checkbox" name="comment-'.$r_arr['id'].'" id="comment-'.$r_arr['id'].'"> '.htmlspecialchars($r_arr['oldauthor']).'</a></p>';
				}

				$c .= '<p class="comment_time">'.$r_arr['regtime'].'</p>';
				if ($r_arr['user']) {
					$c .= '<p class="comment_avatar">'.avatar($r_arr['user'], 100).'</p>';
				}
				$c .= '</div>';
				$title = _('(no title)');
				if ($r_arr['title'])
					$title = $r_arr['title'];
				$c .= '<p><label for="comment-'.$r_arr['id'].'"> <strong>'.str_replace('{title}', '<a href="/'.$active_comic.'/comic/'.$r_arr['slug'].'/">'.$title.'</a>', _('Comment to {title}:')).'</strong></label></p>';
				$c .= $r_arr['text'];
				$c .= '<nav class="comment-meta">';
				$comment_menu = new ArrayHandler;
				if ($logged_in && can_edit_comment($user_info['id'], $r_arr['id'])) {
					$comment_menu->add_line(['text' => _('Edit'), 'link' => '/'.$active_comic.'/comments/edit/'.$r_arr['id'].'/']);
					$comment_menu->add_line(['text' => _('Delete'), 'link' => '/'.$active_comic.'/comments/delete/'.$r_arr['id'].'/']);
				}
				$c .= $comment_menu->return_ul();
				$c .= '</nav>';
				$c .= '</article>';
			}
			$c .= '<p><input name="delete-comments" type="submit" value="'._('Delete selected comments').'"></p>';
			$c .= '</form>';
			$c .= '</section>';
			$c .= $navigation;

		}
		else {
			$c .= '<p>'._('This comic hasn\'t received any comments yet.').'</p>';
		}
