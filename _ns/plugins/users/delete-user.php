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

				// Delete user
				delete_user($user_info['id']);
				header('Location: '.NS_DOMAIN);
				exit;
			
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

	$ns_title = __('Delete account');
	
	$c .= '<h2>'.__('Delete account').'</h2>';
	$c .= '<p><strong>'.__('This is a pretty big deal: Are you absolutely sure you want to delete your user account?').'</strong></p>';

	$c .= '<p>'.__('When you delete your user account, it is gone forever. This is permanent and <strong>cannot be undone</strong>. Seriously: We\'re not one of those shady businesses that never actually delete your data. When you hit delete, you\'re actually deleting it.').'</p>';

	$delete_concequences->add_line(['text' => __('Your profile page, and all information stored to it, will be deleted.'), 'order' => 0]);

	$c .= $delete_concequences->return_ul();
	

	$c .= '<p>'.__('If you\'re absolutely sure you want to delete your user account, please enter your password to confirm.').'</p>';

	$c .= '<form method="post" name="confirm_password" action="/n/dashboard/delete-user/">'."\n";
	$c .= input_field(['name' => 'password', 'text' => __('Password'), 'type' => 'password']);
	$c .= '<p><input type="submit" name="password_confirmed" id="password_confirmed" value="'.str_replace('{comic}', htmlspecialchars($comicname), __('Delete account')).'"></p>';
	$c .= '</form>'."\n";

}