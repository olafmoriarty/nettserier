<?php

	// Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	include($tpf.'functions.php');

	$c_urls->add_line(['url' => 'follow', 'script' => $tpf.'follow.php']);


	$action['comic_header_buttons']->add_line(['function' => 'follow_button', 'order' => 10]);