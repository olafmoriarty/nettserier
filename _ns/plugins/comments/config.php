<?php

	// Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	include($tpf.'functions.php');

	$action['showcomic_on_page_after']->add_line(['function' => 'show_comments']);

	$c_urls->add_line(['url' => 'comments', 'script' => $tpf.'edit-comment.php']);