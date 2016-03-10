<?php

  $submitted = false;
  $errors = false;
  $error_array = array();

  // Get old values
  $query = 'SELECT url, name FROM ns_comics WHERE url = \''.$active_comic.'\' LIMIT 1';
  $result = $conn->query($query);
  $old_values = $result->fetch_assoc();

  if (isset($_POST) && isset($_POST['edit_comic_submit']) && $_POST['edit_comic_submit']) {
    // The form has been submitted
    $submitted = true;
      $fields = array();
      $values = array();
      

	// Comic name change
	if ($_POST['comic_name'] && ($_POST['comic_name'] != $old_values['name'])) {
      $fields[] = 'name';
      $values[] = mysql_string($_POST['comic_name']);
  }

  // URL change
	if ($_POST['comic_url'] && ($_POST['comic_url'] != $old_values['url'])) {

    if ($err = validate_input(['check' => 'unique', 'input' => 'comic_url', 'field' => 'url', 'table' => 'ns_comics', 'error' => __('Sorry! The chosen URL is already in use.')])) {
      $error_array['comic_url'] = $err;
      $errors = true;
    }
    
    if ($err = validate_input(['check' => 'regex', 'input' => 'comic_url', 'regex' => '[A-Za-z0-9-]+', 'error' => __('The comic\'s URL part can only contain letters (a-z), digits (0-9) and hyphens.')])) {
      $error_array['comic_url'] = $err;
      $errors = true;
    }


    if ($_POST['comic_url'] == 'n') {
      $error_array['comic_url'] = __('Sorry! The chosen URL is reserved by the system and therefore can\'t be used.');
      $errors = true;
    }

      $fields[] = 'url';
      $values[] = mysql_string(strtolower($_POST['comic_url']));
  }
    
	$fieldnum = count($fields);
	if (!$errors && $fieldnum > 0) {
		// Make the changes!
		$query = 'UPDATE ns_comics SET ';
		for ($i = 0; $i < $fieldnum; $i++) {
			if ($i) {
				$query .= ', ';
			}
			$query .= $fields[$i].'='.$values[$i];
		}

		$query .= ' WHERE url = \''.$active_comic.'\'';
		$conn->query($query);

		header('Location: '.NS_DOMAIN.'/n/dashboard/my-comics/');
		exit;
	}
	elseif (!$errors) {
		header('Location: '.NS_DOMAIN.'/n/dashboard/my-comics/');
		exit;
	}



  }

  if (!$submitted || $errors) {
    
		$ns_title = __('Edit comic settings');
		
    // Registration form
    $c .= '<h2>'.__('Edit comic settings').'</h2>'."\n";
    $c .= '<form method="post" action="/n/dashboard/my-comics/'.$active_comic.'/edit/">'."\n";
    $c .= input_field(['name' => 'comic_name', 'text' => __('Name of the comic'), 'value' => $old_values['name']]);
    $c .= input_field(['name' => 'comic_url', 'text' => __('Preferred URL to your comic'), 'text_before_field' => NS_DOMAIN.'/', 'text_after_field' => '/', 'class' => 'urlpart', 'value' => $old_values['url']]);

    $c .= '<p><input type="submit" name="edit_comic_submit" id="edit_comic_submit" value="'.__('Deploy changes!').'"></p>';
    $c .= '</form>'."\n";
  }
