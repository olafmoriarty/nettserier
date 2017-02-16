<?php
// This is nettserier.no/n/admin/

$ns_title = _('Administration dashboard');

// Path to this folder
$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

if ($logged_in && is_admin($user_info['id'])) {

	$folder = strtok('/');

	if ($folder == 'plugins') {
		include($tpf.'plugin_manager.php');
	}
	elseif ($folder == 'languages') {
		include($tpf.'language_manager.php');
	}
	
// I NEED TO ADD AN ARRAYHANDLER FOR THIS, but for now ...

	elseif ($folder == 'users') {
		include(NS_PATH.'plugins/moderation/users.php');
	}

	elseif ($folder == 'comments') {
		include(NS_PATH.'plugins/moderation/comments.php');
	}

	elseif (!$folder) {
		$c .= '<h2>'._('Administration dashboard').'</h2>';

// I NEED TO ADD AN ARRAYHANDLER FOR THIS, but for now ...
		
		$c .= '<h3>'._('Moderation').'</h3>';
		$c .= '<ul>';
		$c .= '<li><a href="/n/admin/users/">'._('Users').'</a></li>';
		$c .= '<li><a href="/n/admin/comments/">'._('Comments').'</a></li>';
		$c .= '</ul>';
		
		$c .= '<h3>'._('Advanced configuration').'</h3>';
		$c .= '<ul>';
		$c .= '<li><a href="/n/admin/plugins/">'._('Plugins').'</a></li>';
		$c .= '<li><a href="/n/admin/languages/">'._('Languages').'</a></li>';
		$c .= '</ul>';
	}
	else {
		// *** 404!
	}
}
else {
	// *** Show an error message here! The user is not supposed to be here!
}
