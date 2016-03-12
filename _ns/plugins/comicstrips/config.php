<?php
  // Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	$comicadm_urls->add_line(['url' => 'add-strip', 'script' => $tpf.'add.php']);
	$comicadm_menu->add_line(['text' => __('Upload new comic strip or page'), 'link' => '/n/dashboard/my-comics/{comic}/add-strip/', 'order' => 10]);

	$comicadm_urls->add_line(['url' => 'edit-strip', 'script' => $tpf.'edit.php']);
	$comicadm_menu->add_line(['text' => __('Edit uploaded comic strips and pages'), 'link' => '/n/dashboard/my-comics/{comic}/edit-strip/', 'order' => 11]);
