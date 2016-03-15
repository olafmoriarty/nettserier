<?php
  // Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	$comicadm_urls->add_line(['url' => 'add-strip', 'script' => $tpf.'add.php']);
	$comicadm_menu->add_line(['text' => __('Upload new comic strip or page'), 'link' => '/n/dashboard/my-comics/{comic}/add-strip/', 'order' => 10]);

	$comicadm_urls->add_line(['url' => 'edit-strip', 'script' => $tpf.'edit.php']);
	$comicadm_menu->add_line(['text' => __('Edit uploaded comic strips and pages'), 'link' => '/n/dashboard/my-comics/{comic}/edit-strip/', 'order' => 11]);

	$feed_functions->add_line(['type' => 'c', 'func' => 'feed_comic_strip']);

function feed_comic_strip($arr) {
	$comic_linked = '<a href="/'.$arr['comic_url'].'/comic/'.$arr['slug'].'/">'.htmlspecialchars($arr['comic_name']).'</a>';
	
	$alt = str_replace('{comic}', htmlspecialchars($arr['comic_name']), __('{comic} comic strip (no title)'));
	if ($arr['title']) {
		$alt .= htmlspecialchars($arr['title']);
	}
	
	$c = '';
	
	$c .= '<h3>'.$comic_linked.'</h3>';
	$c .= '<p><img src="/_ns/files/'.md5($arr['id'] . $arr['other']).'.'.$arr['other'].'" alt="'.$alt.'"></p>';

	if ($arr['title']) {
		$c .= '<h4>'.htmlspecialchars($arr['title']).'</h4>'."\n";
	}
	if ($arr['text']) {
		$c .= $arr['text'];
	}
	
	return $c;
}