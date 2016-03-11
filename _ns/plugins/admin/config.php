<?php
	// Plugin information
	// NAME: Website administration

	// Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	// Setup 
	$n_urls->add_line(['url' => 'admin', 'script' => $tpf.'admin.php']);
?>