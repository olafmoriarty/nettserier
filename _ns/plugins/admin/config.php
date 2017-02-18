<?php
	// Plugin information
	// NAME: Website administration

	// Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	// Setup 
	$n_urls->add_line(['url' => 'admin', 'script' => $tpf.'admin.php']);

	// User level labels
	$user_levels = ['0' => _('Everybody'), '1' => _('Registered users'), '10' => _('Verified users'), '40' => _('Beta testers'), '50' => _('Moderators'), '70' => _('Administrators'), '99' => _('Super administrators'), '100' => _('Nobody')];
?>