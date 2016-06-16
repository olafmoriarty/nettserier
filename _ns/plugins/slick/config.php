<?php
	// Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';
	$open_source->add_line(['name' => 'Slick', 'developer' => 'Ken Wheeler', 'link' => 'http://kenwheeler.github.io/slick/', 'license' => 'The MIT License']);

$body_js->add_js(['js' => '/'.$tpf.'slick/slick.min.js']);
$head->add_css(['css' => '/'.$tpf.'slick/slick.css']);
$head->add_css(['css' => '/'.$tpf.'slick/slick-theme.css']);
$body_js->add_js(['js' => '/'.$tpf.'slick-config.js']);
