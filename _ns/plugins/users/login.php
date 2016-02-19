<?php
if ($logged_in) {
  $c .= 'Du er allereie innlogga';
}
else {
  if (isset($_POST['email'], $_POST['password'])) {
      $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
      $password = $_POST['password']; // The unhashed password.

      if (login($email, $password) == true) {
          header("Location: ".NS_DOMAIN);
          exit();
      } else {
        $error = true;
        $c .= 'Ein feil oppstod under innlogging';
      }
  }
  
    $c .= '<form action="/n/login/" method="post" name="login_form">'."\n";                      
    $c .= '<p>Email: <input type="text" name="email"></p>'."\n";
    $c .= 'Password: <input type="password" name="password" id="password">'."\n";
    $c .= '<input type="submit" value="Login">'."\n";
    $c .= '</form>';
}