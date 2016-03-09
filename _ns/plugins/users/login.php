<?php

$ns_title = __('Log in');

if ($logged_in) {
  $c .= '<h2>'.__('Log in').'</h2>'."\n";
	$c .= '<p>'.__('You can\'t log in - you are already logged in.').'</p>';
	$c .= '<p><a href="/n/log-out/">'.__('Log out').'</a></p>'."\n";
}
else {
	$error = false;
	if (isset($_POST['email'], $_POST['password'])) {
	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
	$password = $_POST['password']; // The unhashed password.

	if (login($email, $password) == true) {
		header("Location: ".NS_DOMAIN);
		exit();
	} else {
		$error = true;
	}
}

$c .= '<h2>'.__('Log in').'</h2>'."\n";
$c .= '<form action="/n/log-in/" method="post" name="login_form">'."\n";
if ($error) {
	$c .= '<p class="errorbox">'.__('Wrong e-mail or password.').'</p>';
}
$c .= '<p>'.__('E-mail:').'<br><input type="text" name="email"></p>'."\n";
$c .= '<p>'.__('Password:').'<input type="password" name="password" id="password"></p>'."\n";

	// TO DO: Check ns_login_attempts and add some kind of captcha if more than two attempts in the last two hours. Also, disable form if user is blocked ...

	$c .= '<p><input type="submit" value="'.__('Log in').'"></p>'."\n";
	$c .= '</form>';

	$c .= '<p><a href="/n/register/">'.__('New user?').'</a> | <a href="/n/password/">'.__('Forgot password?').'</a></p>';
}