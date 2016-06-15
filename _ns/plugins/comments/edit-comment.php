<?php

$folder = strtok('/');

if ($folder == 'edit' || $folder == 'delete') {
	$comment_id = strtok('/');
	if (!$comment_id || !is_numeric($comment_id)) {
		header('Location: '.NS_DOMAIN.'/'.$comic_url.'/');
		exit;
	}
	elseif (!$logged_in || !can_edit_comment($user_info['id'], $comment_id)) {
		$c .= '<h2>'._('Error: Can\'t edit comment').'</h2>';
		$c .= '<p>'._('You don\'t have access to edit or delete this comment.').'</p>';
	}
	else {

		// EDIT COMMENT
		$submitted = false;
		$errors = false;
		$error_array = array();

		// Get old values
		$query = 'SELECT text FROM ns_comments WHERE id = '.$comment_id.' LIMIT 1';
		$result = $conn->query($query);
		$old_values = $result->fetch_assoc();
		
		if (isset($_POST['comment_edit_submit']) && $_POST['comment_edit_submit']) {
			// The form has been submitted
			$submitted = true;
			$fields = array();
			$values = array();

			// Get return URL
			$query = 'SELECT u.comic, u.slug, u.updtype FROM ns_comments AS c LEFT JOIN ns_updates AS u ON c.parent = u.id WHERE c.id = '.$comment_id.' LIMIT 1';
			$result = $conn->query($query);
			$arr = $result->fetch_assoc();

			if ($arr['updtype'] == 'c') {
				$returnurlfolder = 'comic';
			}
			elseif ($arr['updtype'] == 'b') {
				$returnurlfolder = 'blog';
			}
			$returnurl = '/'.comic_url($arr['comic']).'/'.$returnurlfolder.'/'.$arr['slug'].'/';

			// Delete comment
			if ($folder == 'delete') {
				$query = 'DELETE FROM ns_comments WHERE id = '.$comment_id;
				$conn->query($query);
				header('Location: '.NS_DOMAIN.$returnurl);
			}
			elseif ($folder == 'edit') {

				// Comment text
				if ($_POST['comment_text'] && ($_POST['comment_text'] != $old_values['comment_text'])) {
				
					if ($err = validate_input(['check' => 'empty', 'input' => 'comment_text', 'error' => _('Comment can\'t be blank!')])) {
					  $error_array['comment_text'] = $err;
					  $errors = true;
					}

					$fields[] = 'text';
					$values[] = mysql_string($filter['html']->run($_POST['comment_text']));

				}

				$fieldnum = count($fields);
				if (!$errors && $fieldnum > 0) {
					// Make the changes!
					$query = 'UPDATE ns_comments SET ';
					for ($i = 0; $i < $fieldnum; $i++) {
						if ($i) {
							$query .= ', ';
						}
						$query .= $fields[$i].'='.$values[$i];
					}

					$query .= ' WHERE id = '.$comment_id;
					$conn->query($query);

					header('Location: '.NS_DOMAIN.$returnurl);
					exit;
				}
				elseif (!$errors) {
					header('Location: '.NS_DOMAIN.$returnurl);
					exit;
				}
			
			}
		}

		if (!$submitted || $errors) {
			if ($folder == 'edit') {
				$c .= '<h2>'._('Edit comment').'</h2>';
				$buttontext = _('Deploy changes!');
			}
			elseif ($folder == 'delete') {
				$c .= '<h2>'._('Delete comment').'</h2>';
				$buttontext = _('Yes, delete comment');
			}

			$c .= '<form method="post" action="'.NS_URL.'">'."\n";

			if ($folder == 'edit') {
				$c .= input_field(['name' => 'comment_text', 'text' => _('Your comment'), 'type' => 'textarea', 'value' => $old_values['text']]);
			}
			elseif ($folder == 'delete') {
				$c .= '<p>'._('Are you sure you want to delete this comment?').'</p>';
			}
			$c .= '<p><input type="submit" name="comment_edit_submit" id="comment_edit_submit" value="'.$buttontext.'"></p>';
			$c .= '</form>'."\n";

		
		}

	}

}