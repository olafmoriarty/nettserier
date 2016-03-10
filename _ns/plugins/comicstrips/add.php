<?php

$page = strtok('/');

if (!$page || $page == 1) {
  $ns_title = __('Upload new comic strip or page');
  $c .= '<h2>'.__('Upload new comic strip or page').'</h2>'."\n";
  $c .= '<h3>'.__('Step one: Upload files').'</h3>'."\n";
  $c .= '<p>'.__('You can upload a single comic strip or page, or multiple at the same time.').'</p>';
  $c .= '<form method="post" action="/n/dashboard/my-comics/'.$active_comic.'/add/" enctype="multipart/form-data">';
  
  $c .= '<p><input type="file" name="file" id="file"></p>';
  $c .= '<p><input type="submit" value="'.__('Upload and proceed').'"></p>';
  $c .= '</form>';
}