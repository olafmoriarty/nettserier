<?php
  // Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	// Setup 
	$d_urls->add_line(['url' => 'new-comic', 'script' => $tpf.'newpage.php']);

	$d_menu->add_line(['text' => __('Create new comic'), 'link' => '/n/dashboard/new-comic/']);
