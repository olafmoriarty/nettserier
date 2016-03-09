<?php

$submitted = false;
$errors = false;
$error_array = array();

if (isset($_POST) && isset($_POST['ed_button']) && $_POST['ed_button']) {
  
}

// Get old values
$query = 'SELECT username, realname, email FROM ns_users WHERE id = '.$user_info['id'].' LIMIT 1';
$result = $conn->query($query);
$old_values = $result->fetch_assoc();

$ns_title = __('User settings');
$c .= '<h2>'.__('User settings').'</h2>'."\n";

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
$c .= '<p><input type="submit" name="reg_button" id="reg_button" value="'.__('Deploy changes!').'"></p>';
$c .= '</form>'."\n";
