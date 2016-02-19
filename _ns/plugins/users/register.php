<?php
  $submitted = false;
  $errors = false;

  if (isset($_POST) && isset($_POST['reg_button']) && $_POST['reg_button']) {
    // The registration form has been submitted
    $submitted = true;

    // VALIDATE DATA AND ADD TO DATABASE
    $validate_input_table = 'ns_users';
    if (validate_input(['check' => 'unique', 'input' => 'reg_username', 'field' => 'username', error => __('Sorry! The chosen username is already in use.')])) {
      $errors = true;
    }
    
    if (!$errors) {
      $table = 'ns_users';
      $fields = array();
      $values = array();
      
      // Username
      
      $fields[] = 'username';
      $values[] = $_POST['reg_username'];
      
      // E-mail
      
      $fields[] = 'email';
      $values[] = $_POST['reg_email'];
      
      // Password and salt
      $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));

      $fields[] = 'password';
      $values[] = hash('sha512', $_POST['reg_pass'] . $random_salt);

      $fields[] = 'salt';
      $values[] = $random_salt;
      
      $query = 'INSERT INTO '.$table.' ('.implode(', ', $fields).') VALUES (\''.implode('\', \'', $values).'\')';
      $conn->query($query);

      $c .= 'Success';
    
    }
  }

  if (!$submitted || $errors) {
    // Registration form
    $c .= '<h2>'.__('Register new user').'</h2>'."\n";
    $c .= '<form method="post" name="registration_form" action="/n/register/">'."\n";
    $c .= '<p>'.__('Your preferred username:').'<br>'."\n";
    $c .= '<input type="text" name="reg_username" id="reg_username"></p>'."\n";
    $c .= '<p>'.__('Your e-mail:').'<br>'."\n";
    $c .= '<input type="text" name="reg_email" id="reg_email"></p>'."\n";
    $c .= '<p>'.__('Your password:').'<br>'."\n";
    $c .= '<input type="password" name="reg_pass" id="reg_pass"></p>'."\n";
    $c .= '<p>'.__('Repeat password:').'<br>'."\n";
    $c .= '<input type="password" name="reg_pass2" id="reg_pass2"></p>'."\n";
    $c .= '<p><input type="submit" name="reg_button" value="'.__('Register!').'"></p>';
    $c .= '</form>'."\n";
  }