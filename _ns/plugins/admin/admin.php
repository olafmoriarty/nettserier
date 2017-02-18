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
		
		$c .= '<h3>'._('Administrator options').'</h3>';
		$c .= '<ul>';
		$c .= '<li><a href="/n/admin/users/">'._('Approve/delete users').'</a></li>';
		$c .= '<li><a href="/n/admin/plugins/">'._('Plugins').'</a></li>';
		$c .= '<li><a href="/n/admin/languages/">'._('Languages').'</a></li>';
		$c .= '</ul>';
	}
	else {
		include(NS_PATH.'pages/404.php');
	}
}
else {
	if (!$logged_in) {
		header('Location: '.NS_DOMAIN.'/n/log-in/?returnurl='.urlencode(NS_URL));
		exit;
	}
	else {
		$c .= '<p>'._('You don\'t have access to view this page.').'</p>';
	}
}
