<?php
  // Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	include($tpf.'functions.php');
	include($tpf.'classes.php');

	$c_urls->add_line(['url' => 'comic', 'script' => $tpf.'show-comics.php']);

	$c_menu->add_line(['text' => _('Comic'), 'link' => '/{comic}/comic/', 'order' => 10]);

	$comicadm_urls->add_line(['url' => 'add-strip', 'script' => $tpf.'add.php']);
	$comicadm_menu->add_line(['text' => _('Upload new comic strip or page'), 'link' => '/n/dashboard/my-comics/{comic}/add-strip/', 'order' => 10]);

	$comicadm_urls->add_line(['url' => 'edit-strip', 'script' => $tpf.'edit.php']);
	$comicadm_menu->add_line(['text' => _('Edit uploaded comic strips and pages'), 'link' => '/n/dashboard/my-comics/{comic}/edit-strip/', 'order' => 11]);

$action['frontpage']->add_line(['function' => 'fp_splash', 'order' => 0]);
$action['frontpage']->add_line(['function' => 'fp_recent', 'order' => 10]);

$action['delete_comic']->add_line(['function' => 'delete_all_strips']);

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
  $select_updates = 'SELECT comupd.updtype AS type, comupd.id, comupd.comic, comupd.pubtime, comupd.title, comupd.text, comupd.slug, comupd.user, CONCAT(\'imgtype\', 0x1F, comupd.imgtype) AS other FROM ns_updates AS comupd';
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

}

// Related to editing comic strips
$edit_comic_single_menu->add_line(['text' => _('Edit strip'), 'link' => '/n/dashboard/my-comics/{comic}/edit-strip/{id}/', 'order' => 10]);


// Scheduler - MOVE to separate plugin :-)
$action['edit_strips_submit']->add_line(['function' => 'strip_scheduler']);

// Related to deleting comic strips
$edit_comic_single_menu->add_line(['text' => _('Delete strip'), 'link' => '/n/dashboard/my-comics/{comic}/edit-strip/delete/{id}/', 'order' => 90]);

