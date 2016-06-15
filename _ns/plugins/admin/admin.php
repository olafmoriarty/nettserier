<?php
// This is nettserier.no/n/admin/

$ns_title = _('Administration dashboard');

// Path to this folder
$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';


$folder = strtok('/');

if ($folder == 'plugins') {
	include($tpf.'plugin_manager.php');
}
elseif ($folder == 'languages') {
	include($tpf.'language_manager.php');
}
elseif (!$folder) {
	$c .= '<h2>'._('Administration dashboard').'</h2>';

	$c .= '<ul>';
	$c .= '<li><a href="/n/admin/plugins/">'._('Plugins').'</a></li>';
	$c .= '<li><a href="/n/admin/languages/">'._('Languages').'</a></li>';
	$c .= '</ul>';
}

?>