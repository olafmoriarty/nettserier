<?php
  // Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	// Setup 
	$n_urls->add_line(['url' => 'register', 'script' => $tpf.'register.php']);
	$n_menu->add_line(['text' => __('New user?'), 'link' => '/n/register/']);
?>