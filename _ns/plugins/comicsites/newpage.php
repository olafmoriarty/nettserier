<?php

  $submitted = false;
  $errors = false;
  $error_array = array();

  if (isset($_POST) && isset($_POST['create_comic_submit']) && $_POST['create_comic_submit']) {
    // The registration form has been submitted
    $submitted = true;

    // VALIDATE DATA AND ADD TO DATABASE
    if ($err = validate_input(['check' => 'unique', 'input' => 'comic_url', 'field' => 'url', 'table' => 'ns_comics', 'error' => _('Sorry! The chosen URL is already in use.')])) {
      $error_array['comic_url'] = $err;
      $errors = true;
    }

    if ($err = validate_input(['check' => 'regex', 'input' => 'comic_url', 'regex' => '[A-Za-z0-9-]+', 'error' => _('The comic\'s URL part can only contain letters (a-z), digits (0-9) and hyphens.')])) {
      $error_array['comic_url'] = $err;
      $errors = true;
    }


    if ($_POST['comic_url'] == 'n') {
      $error_array['comic_url'] = _('Sorry! The chosen URL is reserved by the system and therefore can\'t be used.');
      $errors = true;
    }

    if ($err = validate_input(['check' => 'empty', 'input' => 'comic_name', 'error' => _('Comic name can\'t be blank.')])) {
      $error_array['comic_name'] = $err;
      $errors = true;
    }

    if ($err = validate_input(['check' => 'empty', 'input' => 'comic_url', 'error' => _('Comic URL can\'t be blank.')])) {
      $error_array['reg_email'] = $err;
      $errors = true;
    }


    if (!$errors) {
      $table = 'ns_comics';
      $fields = array();
      $values = array();
      
      $fields[] = 'name';
      $values[] = mysql_string($_POST['comic_name']);
      
      $fields[] = 'url';
      $values[] = mysql_string(strtolower($_POST['comic_url']));
      
      $fields[] = 'regtime';
      $values[] = 'NOW()';
      
      $query = 'INSERT INTO '.$table.' ('.implode(', ', $fields).') VALUES ('.implode(', ', $values).')';
      $conn->query($query);
			
			// Get id
			$comic_id = $conn->insert_id;

	// ------

      $table = 'ns_user_comic_rel';
      $fields = array();
      $values = array();
      
      $fields[] = 'user';
      $values[] = $user_info['id'];
      
      $fields[] = 'comic';
      $values[] = $comic_id;
      
      $fields[] = 'reltype';
      $values[] = mysql_string('c');
      
      $fields[] = 'time';
      $values[] = 'NOW()';
      
      $query = 'INSERT INTO '.$table.' ('.implode(', ', $fields).') VALUES ('.implode(', ', $values).')';
      $conn->query($query);


	  header('Location: '.NS_DOMAIN.'/n/dashboard/my-comics/');
      exit;
    
    }
  }

  if (!$submitted || $errors) {
    
		$ns_title = _('Create new comic');
		
    // Registration form
    $c .= '<h2>'._('Create new comic').'</h2>'."\n";
    $c .= '<form method="post" name="registration_form" action="/n/dashboard/new-comic/">'."\n";
    $c .= input_field(['name' => 'comic_name', 'text' => _('Name of the comic')]);
    $c .= input_field(['name' => 'comic_url', 'text' => _('Preferred URL to your comic'), 'text_before_field' => NS_DOMAIN.'/', 'text_after_field' => '/', 'class' => 'urlpart']);
	$c .= '<p><a href="">'._('More settings ...').'</a></p>';
	$c .= '<p><input type="submit" name="create_comic_submit" id="create_comic_submit" value="'._('Create comic!').'"></p>';
    $c .= '</form>'."\n";
  }
