<?php
// This is nettserier.no/n/admin/

$ns_title = __('Administration dashboard');

// Path to this folder
$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';


$folder = strtok('/');

if ($folder == 'plugins') {
	include($tpf.'plugin_manager.php');
}
elseif (!$folder) {
	$c .= '<h2>'.__('Administration dashboard').'</h2>';

	$c .= '<ul>';
	$c .= '<li><a href="/n/admin/plugins/">'.__('Plugins').'</a></li>';
	$c .= '</ul>';
}

?>