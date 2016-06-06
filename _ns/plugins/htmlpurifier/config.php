<?php

	// Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	// Include library
    require_once $tpf.'library/HTMLPurifier.auto.php';

	$config = HTMLPurifier_Config::createDefault();
	$config->set('HTML.Doctype', 'XHTML 1.0 Strict');
	$config->set('AutoFormat.AutoParagraph', true);
	$config->set('AutoFormat.Linkify', true);

	// Instantiate object
    $purifier = new HTMLPurifier($config);

	// Purify all textareas!
	$filter['html']->add_line(['function' => array($purifier, 'purify')]);
