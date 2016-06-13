<?php

	function show_comments($arr) {
		global $conn, $logged_in, $filter, $user_info;
		$submitted = false;
		$errors = false;
		$error_array = array();
		$active_comic = comic_url($arr['comic']);
		$target_url = '/'.$active_comic.'/comic/'.$arr['slug'].'/';
		if (isset($_POST['comment_text']) && $_POST['comment_text']) {
			$submitted = true;
			if ($logged_in) {
				// Comment has been submitted, all functions for sanitizing will be added here
			}
			else {
				$errors = true;	
			}

			if (!$errors) {

				// No errors, let's do this
				$table = 'ns_comments';
				$fields = array();
				$values = array();

				$fields[] = 'parent';
				$values[] = $arr['id'];

				$fields[] = 'user';
				$values[] = $user_info['id'];

				$fields[] = 'text';
				$values[] = mysql_string($filter['html']->run($_POST['comment_text']));

				$fields[] = 'ip';
				$values[] = mysql_string($_SERVER['REMOTE_ADDR']);

				$fields[] = 'regtime';
				$values[] = 'NOW()';

				$query = 'INSERT INTO '.$table.' ('.implode(', ', $fields).') VALUES ('.implode(', ', $values).')';
				$conn->query($query);
				
				header('Location: '.NS_DOMAIN.$target_url);
				exit;

			}
		}


		$c = '<section class="comment-section">';
		$query = 'SELECT c.id, c.user, u.username, c.text, c.regtime FROM ns_comments AS c LEFT JOIN ns_users AS u on c.user = u.id WHERE c.parent = '.$arr['id'];
		$result = $conn->query($query);
		$num = $result->num_rows;

		if ($num) {
			$c .= '<h4>'.str_replace('{n}', $num, __('{n} comments')).'</h4>';

			// Print comments
			while ($r_arr = $result->fetch_assoc()) {
				$c .= '<article class="comment">';
				$c .= '<div class="comment-header">';
				$c .= '<p class="comment_author"><a href="/n/users/'.$r_arr['user'].'/">'.htmlspecialchars($r_arr['username']).'</a></p>';
				$c .= '<p class="comment_time">'.$r_arr['regtime'].'</p>';
				$c .= '<p class="comment_avatar">'.avatar($r_arr['user'], 100).'</p>';
				$c .= '</div>';
				$c .= $r_arr['text'];
				$c .= '<nav class="comment-meta">';
				$comment_menu = new ArrayHandler;
				if ($logged_in && can_edit_comment($user_info['id'], $r_arr['id'])) {
					$comment_menu->add_line(['text' => __('Edit'), 'link' => '/'.$active_comic.'/comments/edit/'.$r_arr['id'].'/']);
					$comment_menu->add_line(['text' => __('Delete'), 'link' => '/'.$active_comic.'/comments/delete/'.$r_arr['id'].'/']);
				}
				$c .= $comment_menu->return_ul();
				$c .= '</nav>';
				$c .= '</article>';
			}

			
			$c .= '<h4>'.__('Join the discussion!').'</h4>'."\n";
		}
		else {
			$c .= '<h4>'.__('Write a comment!').'</h4>'."\n";
		}

		if ($logged_in) {

			if (!$submitted || $errors) {
				$c .= '<section class="comment-add">';
				$c .= '<form method="post" name="comment_form" action="'.$target_url.'">'."\n";
				$c .= input_field(['name' => 'comment_text', 'text' => __('Your comment'), 'type' => 'textarea']);
				$c .= '<p><input type="submit" name="add_comment_submit" id="add_comment_submit" value="'.__('Add your comment!').'"></p>';
				$c .= '</form>'."\n";
				$c .= '</section>';

			}
			
			
		}
		else {
			$c .= '<section class="comment-add">';
			$c .= '<p><a href="/n/log-in/?returnurl='.urlencode('/'.comic_url($arr['comic']).'/comic/'.$arr['slug'].'/').'">'.__('Log in to add your comment!').'</a></p>'."\n";
			$c .= '</section>';
		}
		$c .= '</section>'."\n";
		return $c;
	}

	function can_edit_comment($user, $id) {
		global $conn;
		if (!is_numeric($id) || !is_numeric($user)) {
			return false;
		}

		$query = 'SELECT c.user, u.comic FROM ns_comments AS c LEFT JOIN ns_updates AS u ON c.parent = u.id WHERE c.id = '.$id.' LIMIT 1';
		$result = $conn->query($query);
		$num = $result->num_rows;
		if ($num) {
			$arr = $result->fetch_assoc();
			if ($user == $arr['user']) {
				return true;
			}
			elseif (can_edit_comic($user, comic_url($arr['comic']))) {
				return true;
			}
		}
		return false;
	}