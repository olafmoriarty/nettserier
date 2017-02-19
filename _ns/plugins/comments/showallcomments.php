<?php

if (isset($_POST['delete-comments']) && $logged_in && is_admin($user_info['id'])) {
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
			$query = 'DELETE ns_comments FROM ns_comments LEFT JOIN ns_updates ON ns_comments.parent = ns_updates.id WHERE ns_comments.id = '.$checked[$i];
			$conn->query($query);
		}
	}
	header('Location: '.NS_DOMAIN.'/n/comments/');
	exit;
}

$rows = 50;


		$query = 'SELECT SQL_CALC_FOUND_ROWS c.id, c.user, u.username, c.text, c.regtime, c.oldauthor, upd.title, upd.slug, upd.comic, comic.url, comic.name AS cname FROM ns_comments AS c LEFT JOIN ns_users AS u on c.user = u.id LEFT JOIN ns_updates AS upd ON upd.id = c.parent LEFT JOIN ns_comics AS comic ON upd.comic = comic.id ORDER BY c.regtime DESC '.limitstring($rows);
		$result = $conn->query($query);

		$query = 'SELECT FOUND_ROWS()';
		$fr_result = $conn->query($query);
		$fr_arr = $fr_result->fetch_row();
		$total_rows = $fr_arr[0];
		$pagecount = ceil($total_rows / $rows);

		$num = $result->num_rows;

		$c .= '<h2>'._('All comments').'</h2>';
		$ns_title = _('All comments');
		if ($num) {
			$navigation = limitstring_nav($pagecount);
			$c .= $navigation;

			$c .= '<section class="comment-section">';
			$c .= '<form method="post" action="'.NS_URL.'">';
			// Print comments

			$checkbox = '';
			while ($r_arr = $result->fetch_assoc()) {
				$c .= '<article class="comment">';
				$c .= '<div class="comment-header">';
				if (is_admin($user_info['id'])) {
					$checkbox = '<input type="checkbox" name="comment-'.$r_arr['id'].'" id="comment-'.$r_arr['id'].'">';
				}
				if ($r_arr['user']) {
					$c .= '<p class="comment_author">'.$checkbox.' <a href="/n/users/'.$r_arr['user'].'/">'.htmlspecialchars($r_arr['username']).'</a></p>';
				}
				else {
					$c .= '<p class="comment_author">'.$checkbox.' '.htmlspecialchars($r_arr['oldauthor']).'</a></p>';
				}

				$c .= '<p class="comment_time">'.$r_arr['regtime'].'</p>';
				if ($r_arr['user']) {
					$c .= '<p class="comment_avatar">'.avatar($r_arr['user'], 100).'</p>';
				}
				$c .= '</div>';
				$title = _('(no title)');
				if ($r_arr['title'])
					$title = $r_arr['title'];
				$c .= '<p><label for="comment-'.$r_arr['id'].'"> <strong>'.str_replace('{title}', '<a href="/'.$r_arr['url'].'/comic/'.$r_arr['slug'].'/"><em>'.htmlspecialchars($r_arr['cname']).'</em> : '.$title.'</a>', _('Comment to {title}:')).'</strong></label></p>';
				$c .= $r_arr['text'];
				$c .= '<nav class="comment-meta">';
				$comment_menu = new ArrayHandler;
				if ($logged_in && can_edit_comment($user_info['id'], $r_arr['id'])) {
					$comment_menu->add_line(['text' => _('Edit'), 'link' => '/'.$r_arr['url'].'/comments/edit/'.$r_arr['id'].'/']);
					$comment_menu->add_line(['text' => _('Delete'), 'link' => '/'.$r_arr['url'].'/comments/delete/'.$r_arr['id'].'/']);
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
