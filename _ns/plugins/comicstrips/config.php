<?php
  // Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	$comicadm_urls->add_line(['url' => 'add-strip', 'script' => $tpf.'add.php']);
	$comicadm_menu->add_line(['text' => __('Upload new comic strip or page'), 'link' => '/n/dashboard/my-comics/{comic}/add-strip/', 'order' => 10]);

	$comicadm_urls->add_line(['url' => 'edit-strip', 'script' => $tpf.'edit.php']);
	$comicadm_menu->add_line(['text' => __('Edit uploaded comic strips and pages'), 'link' => '/n/dashboard/my-comics/{comic}/edit-strip/', 'order' => 11]);

// Stuff for user feed

if ($logged_in) {

// Settings (will be moved to a database ...)
$feed_settings = array();

$feed_settings['comics_mine'] = true;
$feed_settings['comics_i_follow'] = true;
$feed_settings['comics_other'] = false;

$feed_settings['albums_mine'] = true;
$feed_settings['albums_i_follow'] = true;
$feed_settings['albums_other'] = false;

$feed_settings['blogs_mine'] = true;
$feed_settings['blogs_i_follow'] = true;
$feed_settings['blogs_other'] = false;

if ($feed_settings['comics_mine'] || $feed_settings['comics_i_follow'] || $feed_settings['comics_other']) {
  $select_updates = 'SELECT comupd.updtype AS type, comupd.id, comupd.comic, comupd.pubtime, comupd.title, comupd.text, comupd.slug, comupd.user, comupd.imgtype AS other FROM ns_updates AS comupd';
  if (!$feed_settings['comics_other']) {
    // Don't show all comics, only selection. So find the selection ...
    $select_updates .= ' LEFT JOIN ns_user_comic_rel AS comupdr ON comupd.comic = comupdr.comic';
  }
  $select_updates .= ' WHERE comupd.updtype = \'c\' AND comupd.published = 1 AND comupd.pubtime <= NOW()';
  if (!$feed_settings['comics_other']) {
    $select_updates .= ' AND comupdr.reltype ';
    if ($feed_settings['comics_mine'] && $feed_settings['comics_i_follow']) {
      $select_updates .= 'IN (\'c\', \'e\', \'f\')';
    }
    elseif ($feed_settings['comics_mine']) {
      $select_updates .= 'IN (\'c\', \'e\')';
    }
    elseif ($feed_settings['comics_i_follow']) {
      $select_updates .= '= \'f\'';
    }
    $select_updates .= ' AND comupdr.user = {user_id} GROUP BY comupd.id';
  }
	$feed_queries->add_line(['text' => $select_updates]);
}


	$feed_functions->add_line(['type' => 'c', 'func' => 'feed_comic_strip']);

function feed_comic_strip($arr) {
	$comic_linked = '<a href="/'.$arr['comic_url'].'/comic/'.$arr['slug'].'/">'.htmlspecialchars($arr['comic_name']).'</a>';
	
	$alt = str_replace('{comic}', htmlspecialchars($arr['comic_name']), __('{comic} comic strip (no title)'));
	if ($arr['title']) {
		$alt .= htmlspecialchars($arr['title']);
	}
	
	$c = '';
	
	$c .= '<h3>'.str_replace(array('{comic}', '{creator}'), array($comic_linked, htmlspecialchars($arr['comic_creator'])), __('{comic} by {creator}')).'</h3>';
	$c .= '<p class="comic-para"><img src="/_ns/files/'.md5($arr['id'] . $arr['other']).'.'.$arr['other'].'" alt="'.$alt.'"></p>';

	if ($arr['title']) {
		$c .= '<h4>'.htmlspecialchars($arr['title']).'</h4>'."\n";
	}
	if ($arr['text']) {
		$c .= $arr['text'];
	}
	
	return $c;
}
}