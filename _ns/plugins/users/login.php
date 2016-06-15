<?php

$ns_title = _('Log in');

if ($logged_in) {
  $c .= '<h2>'._('Log in').'</h2>'."\n";
	$c .= '<p>'._('You can\'t log in - you are already logged in.').'</p>';
	$c .= '<p><a href="/n/log-out/">'._('Log out').'</a></p>'."\n";
}
else {
	$error = false;
	if (isset($_POST['email'], $_POST['password'])) {
	$email = $_POST['email'];
	$password = $_POST['password']; // The unhashed password.
	$remember = false;
	if (isset($_POST['remember']) && $_POST['remember']) {
		$remember = true;
	}

	if (login($email, $password, $remember) == true) {
		header("Location: ".NS_DOMAIN.$_POST['returnurl']);
		exit();
	} else {
		$error = true;
	}
}

$c .= '<h2>'._('Log in').'</h2>'."\n";
$c .= '<form action="/n/log-in/" method="post" name="login_form">'."\n";
if ($error) {
	$c .= '<p class="errorbox">'._('Wrong e-mail or password.').'</p>';
}
$c .= '<p>'._('Username or e-mail:').'<br><input type="text" name="email"></p>'."\n";
$c .= '<p>'._('Password:').'<input type="password" name="password" id="password"></p>'."\n";
$c .= '<p><input type="checkbox" name="remember"> '._('Remember me in this browser').'</p>'."\n";

	// TO DO: Check ns_login_attempts and add some kind of captcha if more than two attempts in the last two hours. Also, disable form if user is blocked ...

	$c .= '<p><input type="submit" value="'._('Log in').'"></p>'."\n";
	if (isset($_GET['returnurl'])) {
		$c .= '<input name="returnurl" type="hidden" value="'.htmlspecialchars($_GET['returnurl']).'">';
	}
	$c .= '</form>';

	$c .= '<p><a href="/n/register/">'._('New user?').'</a> | <a href="/n/password/">'._('Forgot password?').'</a></p>';
}