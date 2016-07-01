<?php

	$submitted = false;
	$errors = false;
	$error_array = array();
	$comicname = comic_name($active_comic);
	$max_file_size = 2 * 1024 * 1024;
	$accepted_extensions = ['jpg', 'jpeg', 'gif', 'png'];

	if (isset($_POST) && isset($_POST['upload']) && $_POST['upload']) {
		$submitted = true;

		// Let's assume there are uploaded images: How many are there?
		$imgnum = count($_FILES['file']['name']);
		$realimgnum = 0;
		
		for ($i = 0; $i < $imgnum; $i++) {
			// Check that file actually exists ...
			if ($_FILES['file']['tmp_name'][$i]) {
				$realimgnum++;
				
				// Is it an image?
				if (getimagesize($_FILES['file']['tmp_name'][$i]) === false) {
					$errors = true;
					if (isset($error_array['file[]'])) {
						$error_array['file[]'] .= '<br>'."\n".str_replace('{file}', htmlspecialchars($_FILES['file']['name'][$i]), _('{file} is not a valid image file.'));
					}
					else {
						$error_array['file[]'] = _('Could not upload file(s):').'<br>'."\n".str_replace('{file}', htmlspecialchars($_FILES['file']['name'][$i]), _('{file} is not a valid image file.'));
					}
				}

				// Is its size okay?
				if ($_FILES['file']['size'][$i] > $max_file_size) {
					$errors = true;
					if (isset($error_array['file[]'])) {
						$error_array['file[]'] .= '<br>'."\n".str_replace(['{file}', '{size}'], [htmlspecialchars($_FILES['file']['name'][$i]), floor($max_file_size / 1024)], _('{file} exceeds the maximum allowed filesize of {size} kB.'));
					}
					else {
						$error_array['file[]'] = _('Could not upload file(s):').'<br>'."\n".str_replace(['{file}', '{size}'], [htmlspecialchars($_FILES['file']['name'][$i]), floor($max_file_size / 1024)], _('{file} exceeds the maximum allowed filesize of {size} kB.'));
					}
				}

				// Extension okay?
				$extension = strtolower(pathinfo($_FILES['file']['name'][$i], PATHINFO_EXTENSION));
				if (!in_array($extension, $accepted_extensions)) {
					$errors = true;
					if (isset($error_array['file[]'])) {
						$error_array['file[]'] .= '<br>'."\n".str_replace(['{file}', '{extensions}'], [htmlspecialchars($_FILES['file']['name'][$i]), implode(', ', $accepted_extensions)], _('{file} does not have a valid file type. Accepted file types are: {extensions}.'));
					}
					else {
						$error_array['file[]'] = _('Could not upload file(s):').'<br>'."\n".str_replace(['{file}', '{extensions}'], [htmlspecialchars($_FILES['file']['name'][$i]), implode(', ', $accepted_extensions)], _('{file} does not have a valid file type. Accepted file types are: {extensions}.'));
					}
				}

			}
			elseif ($_FILES['file']['name'][$i]) {
				// File is not uploaded, but DOES have a name, so something definitely went wrong here. Let's call the whole thing off.
					$errors = true;
					if (isset($error_array['file[]'])) {
						$error_array['file[]'] .= '<br>'."\n".str_replace('{file}', htmlspecialchars($_FILES['file']['name'][$i]), _('{file} could not be uploaded because something went horribly wrong.'));
					}
					else {
						$error_array['file[]'] = _('Could not upload file(s):').'<br>'."\n".str_replace('{file}', htmlspecialchars($_FILES['file']['name'][$i]), _('{file} could not be uploaded because something went horribly wrong (one reason this may happen is if the file size is obscenely large, in which case you should definitely reduce it).'));
					}

				$realimgnum++;
			}

		}
		
		if (!$realimgnum) {
			// No images seem to exist ...?
			$errors = true;
			$error_array['file[]'] = _('You haven\'t chosen any files to upload!');
		}
		
		if (!$errors) {

			$table = 'ns_updates';

			// Comic ID
			$comic_id = comic_id($active_comic);

			// For each image
			for ($i = 0; $i < $imgnum; $i++) {
				// Check that file actually exists ...
				if ($_FILES['file']['tmp_name'][$i]) {
					$fields = array();
					$values = array();

					// This is a comic strip or page; updtype should be 'c'
					$fields[] = 'updtype';
					$values[] = mysql_string('c');

					// Which comic is this strip a part of?
					$fields[] = 'comic';
					$values[] = $comic_id;

					// Who's uploading this?
					$fields[] = 'user';
					$values[] = $user_info['id'];

					// And what's their IP address?
					$fields[] = 'ip';
					$values[] = mysql_string($_SERVER['REMOTE_ADDR']);

					// Not published - save as draft
					$fields[] = 'published';
					$values[] = 0;

					// What's the image type?
					$extension = pathinfo($_FILES['file']['name'][$i], PATHINFO_EXTENSION);
					$fields[] = 'imgtype';
					$values[] = mysql_string($extension);

					// What's the original filename?
					$fields[] = 'filename';
					$values[] = mysql_string($_FILES['file']['name'][$i]);

					// Upload time
					$fields[] = 'regtime';
					$values[] = 'NOW()';

					// Create database row
					$query = 'INSERT INTO '.$table.' ('.implode(', ', $fields).') VALUES ('.implode(', ', $values).')';
					$conn->query($query);

					// Get ID
					$strip_id = $conn->insert_id;


					// Save image to uploads folder
					move_uploaded_file($_FILES['file']['tmp_name'][$i], NS_PATH.'files/'.md5($strip_id . $extension).'.'.$extension);
				}
			}

		  header('Location: '.NS_DOMAIN.'/n/dashboard/my-comics/'.$active_comic.'/edit-strip/drafts/?uploaded=yep');
		  exit;
		}

	}

	if (!$submitted || $errors) {

  $ns_title = _('Upload new comic strip or page');
  $c .= '<h2>'.str_replace('{comic}', htmlspecialchars($comicname), _('Upload new comic strip or page for "{comic}"')).'</h2>'."\n";
  $c .= '<h3>'._('Step one: Upload files').'</h3>'."\n";
  $c .= '<p>'._('You can upload a single comic strip or page, or multiple at the same time.').'</p>';
  $c .= '<form method="post" action="/n/dashboard/my-comics/'.$active_comic.'/add-strip/" enctype="multipart/form-data">';
	$c .= input_field(['name' => 'file[]', 'id' => 'file-upload', 'type' => 'file', 'extra-attributes' => 'multiple']);
  $c .= '<p><input type="submit" name="upload" value="'._('Upload and proceed').'"></p>';
  $c .= '</form>';

	}