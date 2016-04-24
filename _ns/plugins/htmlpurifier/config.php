<?php

	// Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	// Include library
    require_once $tpf.'library/HTMLPurifier.auto.php';

	// Instantiate object
    $purifier = new HTMLPurifier();

	// Purify all textareas!
	$filter['html']->add_line(['function' => array($purifier, 'purify')]);
