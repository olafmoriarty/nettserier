<?php
$submitted = false;
$errors = false;
$error_array = array();

if (isset($_POST) && isset($_POST['password_confirmed']) && $_POST['password_confirmed']) {
	// The form has been submitted
	$submitted = true;

	if (is_numeric($user_info['id'])) {
		$query = 'SELECT password, salt FROM ns_users WHERE id = '.$user_info['id'].' LIMIT 1';
		$result = $conn->query($query);
		$num = $result->num_rows;
		if ($num) {
			$arr = $result->fetch_assoc();
			if (hash('sha512', $_POST['password'].$arr['salt']) == $arr['password']) {

				// Delete comic
				if (can_edit_comic($user_info['id'], $active_comic)) {
					delete_comic($active_comic);
					header('Location: '.NS_DOMAIN.'/n/dashboard/my-comics/');
					exit;
				}
			
			}
			else {
				$errors = true;
				$error_array['password'] = __('That\'s not the correct password.');
			}
		}
		else {
			$errors = true;
			$error_array['password'] = __('Impossible error. Please try again.');
		}
	}
	else {
		$errors = true;
		$error_array['password'] = __('Impossible error. Please try again.');
	}
}

if (!$submitted || $errors) {

	$comicname = comic_name($active_comic);

	$ns_title = str_replace('{comic}', htmlspecialchars($comicname), __('Delete "{comic}"'));
	
	$c .= '<h2>'.str_replace('{comic}', htmlspecialchars($comicname), __('Delete "{comic}"')).'</h2>';
	$c .= '<p><strong>'.__('This is a pretty big deal: Are you absolutely sure you want to delete this comic?').'</strong></p>';
	$c .= '<p>'.__('Deleting a comic is permanent and <strong>cannot be undone</strong>. When deleting a comic you\'re also deleting all comic strips, blog posts, comments, statistics and everything else that is related to that comic.').'</p>';
	$c .= '<p>'.__('If you\'re absolutely sure you want to delete this comic, please enter your password to confirm.').'</p>';

	$c .= '<form method="post" name="confirm_password" action="/n/dashboard/my-comics/'.$active_comic.'/delete/">'."\n";
	$c .= input_field(['name' => 'password', 'text' => __('Password'), 'type' => 'password']);
	$c .= '<p><input type="submit" name="password_confirmed" id="password_confirmed" value="'.str_replace('{comic}', htmlspecialchars($comicname), __('Delete "{comic}"')).'"></p>';
	$c .= '</form>'."\n";

}