<?php

	$submitted = false;
	$errors = false;
	$error_array = array();
	$comicname = comic_name($active_comic);

	if (isset($_POST) && isset($_POST['upload']) && $_POST['upload']) {
		$submitted = true;
	
		// Let's assume there are uploaded images: How many are there?
		$imgnum = count($_FILES['file']['name']);

		for ($i = 0; $i < $imgnum; $i++) {
			echo $_FILES['file']['name'][$i].'<br>';		
		}
		exit;

	}

	if (!$submitted || $errors) {

  $ns_title = __('Upload new comic strip or page');
  $c .= '<h2>'.str_replace('{comic}', htmlspecialchars($comicname), __('Upload new comic strip or page for "{comic}"')).'</h2>'."\n";
  $c .= '<h3>'.__('Step one: Upload files').'</h3>'."\n";
  $c .= '<p>'.__('You can upload a single comic strip or page, or multiple at the same time.').'</p>';
  $c .= '<form method="post" action="/n/dashboard/my-comics/'.$active_comic.'/add-strip/" enctype="multipart/form-data">';
  $c .= '<p><input type="file" name="file[]" id="file" multiple></p>';
  $c .= '<p><input type="submit" name="upload" value="'.__('Upload and proceed').'"></p>';
  $c .= '</form>';

	}