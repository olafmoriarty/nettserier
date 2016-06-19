<?php

	// Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	include($tpf.'functions.php');

	$c_urls->add_line(['url' => 'follow', 'script' => $tpf.'follow.php']);
	$c_urls->add_line(['url' => 'block', 'script' => $tpf.'block.php']);


	$action['comic_header_buttons']->add_line(['function' => 'follow_button', 'order' => 10]);
	$action['user_page']->add_line(['function' => 'user_follow', 'order' => 50]);
