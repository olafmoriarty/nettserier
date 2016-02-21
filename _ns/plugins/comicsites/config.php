<?php
  // Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	// Setup 
	$d_urls->add_line(['url' => 'new-comic', 'script' => $tpf.'newpage.php']);

//	$n_menu->add_line(['text' => __('New user?'), 'link' => '/n/register/']);
