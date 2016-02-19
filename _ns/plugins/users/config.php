<?php
  // Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	// Setup 
	$n_urls->add_line(['url' => 'log-in', 'script' => $tpf.'login.php']);
	$n_urls->add_line(['url' => 'register', 'script' => $tpf.'register.php']);
	$n_urls->add_line(['url' => 'welcome', 'script' => $tpf.'welcome.php']);
	$n_urls->add_line(['url' => 'log-out', 'script' => $tpf.'logout.php']);

	$n_menu->add_line(['text' => __('New user?'), 'link' => '/n/register/']);
