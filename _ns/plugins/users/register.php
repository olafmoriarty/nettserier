<?php
$ns_title = _('Register new user');
if ($logged_in) {
    $c .= '<h2>'._('Register new user').'</h2>'."\n";
	$c .= '<p>'._('You can\'t register - you are already logged in.').'</p>';
	$c .= '<p><a href="/n/log-out/">'._('Log out').'</a></p>'."\n";
}
else {

  $submitted = false;
  $errors = false;
  $error_array = array();

  if (isset($_POST) && isset($_POST['reg_button']) && $_POST['reg_button']) {
    // The registration form has been submitted
    $submitted = true;

    // VALIDATE DATA AND ADD TO DATABASE
    if ($err = validate_input(['check' => 'unique', 'input' => 'reg_username', 'field' => 'username', 'table' => 'ns_users', 'error' => _('Sorry! The chosen username is already in use.')])) {
      $error_array['reg_username'] = $err;
      $errors = true;
    }

    if ($err = validate_input(['check' => 'empty', 'input' => 'reg_username', 'error' => _('Username can\'t be blank.')])) {
      $error_array['reg_username'] = $err;
      $errors = true;
    }

    if ($err = validate_input(['check' => 'email', 'input' => 'reg_email', 'error' => _('This is not a valid e-mail address.')])) {
      $error_array['reg_email'] = $err;
      $errors = true;
    }

    if ($err = validate_input(['check' => 'empty', 'input' => 'reg_email', 'error' => _('E-mail address can\'t be blank.')])) {
      $error_array['reg_email'] = $err;
      $errors = true;
    }

    if ($err = validate_input(['check' => 'unique', 'input' => 'reg_email', 'table' => 'ns_users', 'field' => 'email', 'error' => _('This e-mail address is already registered.')])) {
      $error_array['reg_email'] = $err;
      $errors = true;
    }
    
    if ($err = validate_input(['check' => 'empty', 'input' => 'reg_pass', 'error' => _('Password can\'t be blank.')])) {
      $error_array['reg_pass'] = $err;
      $errors = true;
    }

    if ($err = validate_input(['check' => 'matching', 'input2' => 'reg_pass', 'input' => 'reg_pass2', 'error' => _('The "Password" and "Confirm password" fields don\'t match.')])) {
      $error_array['reg_pass2'] = $err;
      $errors = true;
    }
    

    if (!$errors) {
      $table = 'ns_users';
      $fields = array();
      $values = array();
      
      // Username
      
      $fields[] = 'username';
      $values[] = mysql_string($_POST['reg_username']);
      
      // E-mail
      
      $fields[] = 'email';
      $values[] = mysql_string($_POST['reg_email']);
      
      // Password and salt
      $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));

      $fields[] = 'password';
      $values[] = mysql_string(hash('sha512', $_POST['reg_pass'] . $random_salt));

      $fields[] = 'salt';
      $values[] = mysql_string($random_salt);

      // Current timestamp
      $fields[] = 'regtime';
      $values[] = 'NOW()';
      
      // User level (1 by default)
      $fields[] = 'level';
      $values[] = '1';

	  // Unique token to be sent to user on e-mail
	  $emailtoken = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
      $fields[] = 'emailtoken';
      $values[] = mysql_string($emailtoken);

      $query = 'INSERT INTO '.$table.' ('.implode(', ', $fields).') VALUES ('.implode(', ', $values).')';
      $conn->query($query);

	  // Send user welcome e-mail with e-mail verification token

		$email_sender = 'post@nettserier.no';
	  $email_text = str_replace(array('{username}', '{pagetitle}', '{url}'), array($_POST['reg_username'], PAGE_TITLE, NS_DOMAIN.'/n/email-verify/'.$emailtoken), _('Dear {username},'."\n\n".'Welcome to {pagetitle}! We hope you will enjoy our wide selection of user-generated comics - or perhaps even add one (or more!) of your own?'."\n\n".'Your registration is almost complete. To finish it, we just need you to click this link to confirm that this is actually your e-mail address:'."\n".'{url}'."\n\n".'Have a wonderful day!'."\n\n".'Best wishes,'."\n".'{pagetitle}'))."\n";

	  mail($_POST['reg_email'], str_replace('{pagetitle}', PAGE_TITLE, _('{pagetitle} Registration Confirmation')), $email_text, 'From: "'.PAGE_TITLE.'" <'.$email_sender.'>');

      header('Location: '.NS_DOMAIN.'/n/welcome/');
      exit;
    
    }
  }

  if (!$submitted || $errors) {
    
    // Registration form
    $c .= '<h2>'._('Register new user').'</h2>'."\n";
    $c .= '<form method="post" name="registration_form" action="/n/register/">'."\n";
    $c .= input_field(['name' => 'reg_username', 'text' => _('Your preferred username')]);
    $c .= input_field(['name' => 'reg_email', 'text' => _('Your e-mail')]);
    $c .= input_field(['name' => 'reg_pass', 'text' => _('Your password'), 'type' => 'password']);
    $c .= input_field(['name' => 'reg_pass2', 'text' => _('Repeat password'), 'type' => 'password']);
    $c .= '<p><input type="submit" name="reg_button" id="reg_button" value="'._('Register!').'"></p>';
    $c .= '</form>'."\n";
  }
}