<?php

	// Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	$n_urls->add_line(['url' => 'help', 'script' => $tpf.'help.php']);