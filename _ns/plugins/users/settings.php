<?php

$submitted = false;
$errors = false;
$error_array = array();

// Get old values
$query = 'SELECT username, realname, email FROM ns_users WHERE id = '.$user_info['id'].' LIMIT 1';
$result = $conn->query($query);
$old_values = $result->fetch_assoc();


if (isset($_POST) && isset($_POST['ed_button']) && $_POST['ed_button']) {
	$submitted = true;

	// Username change
	if ($_POST['username'] && ($_POST['username'] != $old_values['username'])) {

		if ($err = validate_input(['check' => 'unique', 'input' => 'username', 'field' => 'username', 'table' => 'ns_users', 'error' => __('Sorry! The chosen username is already in use.'), 'this_id' => $user_info['id']])) {
		  $error_array['username'] = $err;
		  $errors = true;
		}

	}
}

if (!$submitted || $errors) {

	$ns_title = __('User settings');
	$c .= '<h2>'.__('User settings').'</h2>'."\n";
	$c .= '<p>'.__('To update your registered information, change the values in the fields below, and click "Deploy changes!" at the bottom of the page.').'</p>';

	$c .= '<form method="post" name="registration_form" action="/n/dashboard/settings/">'."\n";

	$c .= '<h3>'.__('Update your profile').'</h3>'."\n";
	$c .= input_field(['name' => 'username', 'text' => __('Your preferred username'), 'value' => $old_values['username']]);
	$c .= input_field(['name' => 'realname', 'text' => __('Your real name or preferred pseudonym'), 'value' => $old_values['realname']]);
	$c .= input_field(['name' => 'email', 'text' => __('Your e-mail'), 'value' => $old_values['email']]);
	$c .= '<h3>'.__('Change your password').'</h3>'."\n";
	$c .= '<p><em>'.__('(Leave these fields blank if you don\'t need to change your password right now.)').'</em></p>'."\n";
	$c .= input_field(['name' => 'oldpass', 'text' => __('Old password'), 'type' => 'password']);
	$c .= input_field(['name' => 'newpass', 'text' => __('New password'), 'type' => 'password']);
	$c .= input_field(['name' => 'newpass2', 'text' => __('Repeat password'), 'type' => 'password']);
	$c .= '<p><input type="submit" name="ed_button" id="ed_button" value="'.__('Deploy changes!').'"></p>';
	$c .= '</form>'."\n";

}