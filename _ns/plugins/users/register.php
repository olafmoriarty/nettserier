<?php
  $submitted = false;
  $errors = false;

  if (isset($_POST) && isset($_POST['reg_button']) && $_POST['reg_button']) {
    // The registration form has been submitted
    $submitted = true;

    // VALIDATE DATA AND ADD TO DATABASE
  }

  if (!$submitted || $errors) {
    // Registration form
    $c .= '<h2>'.__('Register new user').'</h2>'."\n";
    $c .= '<form method="post" action="/n/register/">'."\n";
    $c .= '<p>'.__('Your preferred username:').'<br>'."\n";
    $c .= '<input type="text" name="reg_username"></p>'."\n";
    $c .= '<p>'.__('Your e-mail:').'<br>'."\n";
    $c .= '<input type="text" name="reg_username"></p>'."\n";
    $c .= '<p>'.__('Your password:').'<br>'."\n";
    $c .= '<input type="password" name="reg_username"></p>'."\n";
    $c .= '<p>'.__('Repeat password:').'<br>'."\n";
    $c .= '<input type="password" name="reg_username"></p>'."\n";
    $c .= '</form>'."\n";
  }
  
?>