<?php
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
	$c .= '<p>Email: <input type="text" name="email"></p>'."\n";
    $c .= 'Password: <input type="password" name="password" id="password">'."\n";

	// TO DO: Check ns_login_attempts and add some kind of captcha if more than two attempts in the last two hours. Also, disable form if user is blocked ...

    $c .= '<input type="submit" value="'.__('Log in').'">'."\n";
    $c .= '</form>';
}