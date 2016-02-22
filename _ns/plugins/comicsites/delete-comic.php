<?php
$comicname = comic_name($active_comic);

$c .= '<h2>'.str_replace('{comic}', htmlspecialchars($comicname), __('Delete "{comic}"')).'</h2>';
$c .= '<p><strong>'.__('This is a pretty big deal: Are you absolutely sure you want to delete this comic?').'</strong></p>';
$c .= '<p>'.__('Deleting a comic is permanent and <strong>cannot be undone</strong>. When deleting a comic you\'re also deleting all comic strips, blog posts, comments, statistics and everything else that is related to that comic').'</p>';
$c .= '<p>'.__('If you\'re absolutely sure you want to delete this comic, please enter your password to confirm.').'</p>';
