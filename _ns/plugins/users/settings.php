<?php

$submitted = false;
$errors = false;
$error_array = array();

// Get old values
$query = 'SELECT username, realname, password, salt, email FROM ns_users WHERE id = '.$user_info['id'].' LIMIT 1';
$result = $conn->query($query);
$old_values = $result->fetch_assoc();


if (isset($_POST) && isset($_POST['ed_button']) && $_POST['ed_button']) {
	$submitted = true;

	// Username change
	if ($_POST['username'] && ($_POST['username'] != $old_values['username'])) {

		if ($err = validate_input(['check' => 'unique', 'input' => 'username', 'field' => 'username', 'table' => 'ns_users', 'error' => _('Sorry! The chosen username is already in use.'), 'this_id' => $user_info['id']])) {
			$error_array['username'] = $err;
			$errors = true;
		}

		$fields[] = 'username';
		$values[] = mysql_string($_POST['username']);
	}

	// Realname change (or deletion)
	if ($_POST['realname'] != $old_values['realname']) {
		$fields[] = 'realname';
		$values[] = mysql_string($_POST['realname']);
	}

	// E-mail change
	if ($_POST['email'] && ($_POST['email'] != $old_values['email'])) {

		if ($err = validate_input(['check' => 'unique', 'input' => 'email', 'field' => 'email', 'table' => 'ns_users', 'error' => _('This e-mail address is already registered.'), 'this_id' => $user_info['id']])) {
			$error_array['email'] = $err;
			$errors = true;
		}

		if ($err = validate_input(['check' => 'email', 'input' => 'email', 'error' => _('This is not a valid e-mail address.')])) {
			$error_array['email'] = $err;
			$errors = true;
		}
		$fields[] = 'email';
		$values[] = mysql_string($_POST['email']);
	}

	// Password change
	if ($_POST['oldpass'] || $_POST['newpass'] || $_POST['newpass2']) {
		// Check if old password matches
		if (hash('sha512', $_POST['oldpass'].$old_values['salt']) != $old_values['password']) {
			$error_array['oldpass'] = _('That is not the correct password.');
			$errors = true;
		}

		if ($err = validate_input(['check' => 'empty', 'input' => 'newpass', 'error' => _('When changing your password, the new password can\'t be blank.')])) {
		  $error_array['newpass'] = $err;
		  $errors = true;
		}

		if ($err = validate_input(['check' => 'matching', 'input2' => 'newpass', 'input' => 'newpass2', 'error' => _('The "Password" and "Confirm password" fields don\'t match.')])) {
		  $error_array['newpass2'] = $err;
		  $errors = true;
		}

		$fields[] = 'password';
		$values[] = mysql_string(hash('sha512', $_POST['newpass'].$old_values['salt']));

	}

	$fieldnum = count($fields);
	if (!$errors && $fieldnum > 0) {
		// Make the changes!
		$query = 'UPDATE ns_users SET ';
		for ($i = 0; $i < $fieldnum; $i++) {
			if ($i) {
				$query .= ', ';
			}
			$query .= $fields[$i].'='.$values[$i];
		}

		// TO DO:
		// - If e-mail address change, send new vaildation e-mail!
		// - If password change, re-login user!

		$query .= ' WHERE id = '.$user_info['id'];
		$conn->query($query);

		header('Location: '.NS_DOMAIN.'/n/dashboard/');
		exit;
	}
	elseif (!$errors) {
		header('Location: '.NS_DOMAIN.'/n/dashboard/');
		exit;
	}
}

if (!$submitted || $errors) {

	$ns_title = _('User settings');
	$c .= '<h2>'._('User settings').'</h2>'."\n";
	$c .= '<p>'._('To update your registered information, change the values in the fields below, and click "Deploy changes!" at the bottom of the page.').'</p>';

	$c .= '<form method="post" name="registration_form" action="/n/dashboard/settings/">'."\n";

	$c .= '<h3>'._('Update your profile').'</h3>'."\n";
	$c .= input_field(['name' => 'username', 'text' => _('Your preferred username'), 'value' => $old_values['username']]);
	$c .= input_field(['name' => 'realname', 'text' => _('Your real name or preferred pseudonym'), 'value' => $old_values['realname']]);
	$c .= input_field(['name' => 'email', 'text' => _('Your e-mail'), 'value' => $old_values['email']]);
	$c .= '<h3>'._('Change your password').'</h3>'."\n";
	$c .= '<p><em>'._('(Leave these fields blank if you don\'t need to change your password right now.)').'</em></p>'."\n";
	$c .= input_field(['name' => 'oldpass', 'text' => _('Old password'), 'type' => 'password']);
	$c .= input_field(['name' => 'newpass', 'text' => _('New password'), 'type' => 'password']);
	$c .= input_field(['name' => 'newpass2', 'text' => _('Repeat password'), 'type' => 'password']);
	$c .= '<p><input type="submit" name="ed_button" id="ed_button" value="'._('Deploy changes!').'"></p>';
	$c .= '</form>'."\n";

	$c .= '<p><a href="/n/dashboard/delete-user/">'._('Delete my account').'</a></p>';
}