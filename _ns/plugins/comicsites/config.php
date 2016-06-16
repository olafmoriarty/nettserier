<?php
  // Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	// Functions

	include($tpf.'functions.php');
	
	// Setup 

	$comicadm_urls = new ArrayHandler;
	$comicadm_menu = new ArrayHandler;

	$n_urls->add_line(['url' => 'browse', 'script' => $tpf.'browse.php']);
	$n_menu->add_line(['text' => _('Comics'), 'link' => '/n/browse/', 'order' => 10]);

	$d_urls->add_line(['url' => 'new-comic', 'script' => $tpf.'newpage.php']);
	$d_urls->add_line(['url' => 'my-comics', 'script' => $tpf.'mycomics.php']);

	$d_menu->add_line(['text' => _('Create new comic'), 'link' => '/n/dashboard/new-comic/', 'order' => -10]);

	$feed_sidebar_menu->add_line(['text' => _('Browse all comics'), 'link' => '/n/browse/', 'order' => 10]);
	$feed_sidebar_menu->add_line(['text' => _('Create new comic'), 'link' => '/n/dashboard/new-comic/', 'order' => 100]);


	if ($logged_in) {
		if (owns_comics($user_info['id'])) {
			$d_menu->add_line(['text' => _('My comics'), 'link' => '/n/dashboard/my-comics/', 'order' => 0]);
		}
	}

	$comicadm_urls->add_line(['url' => 'delete', 'script' => $tpf.'delete-comic.php']);
	$comicadm_urls->add_line(['url' => 'edit', 'script' => $tpf.'edit-comic.php']);
	$comicadm_menu->add_line(['text' => _('Go to comic'), 'link' => '/{comic}/', 'order' => -100]);
	$comicadm_menu->add_line(['text' => _('Edit comic settings'), 'link' => '/n/dashboard/my-comics/{comic}/edit/', 'order' => 100]);
	$comicadm_menu->add_line(['text' => _('Delete comic'), 'link' => '/n/dashboard/my-comics/{comic}/delete/', 'order' => 999]);

	$delete_concequences->add_line(['text' => _('If you have created any comics: Any comic <strong>where you are the only listed creator or editor</strong> will be deleted. Deleting a comic also deletes all comic strips, blog posts, comments, statistics and everything else that is related to that comic.'), 'order' => 10]);
	$delete_concequences->add_line(['text' => _('Any comic you have created or contributed to <strong>with more than one creator/editor listed</strong> will not be deleted automatically, but you will be removed from its creator list and lose your access to edit this comic. If you want to delete such a comic, you have to do that manually before deleting your profile.'), 'order' => 10.5]);

/*
	$feed_queries->add_line(['text' => 'SELECT \'comic_created\' AS type, NULL AS id, comcre.id AS comic, comcre.regtime AS pubtime, NULL as title, NULL as text, NULL as slug, NULL as user, NULL as other FROM ns_comics AS comcre LEFT JOIN ns_user_comic_rel AS comcrerel ON comcre.id = comcrerel.comic WHERE comcrerel.reltype IN (\'c\', \'e\') AND comcrerel.user = {user_id}']);
	$feed_functions->add_line(['type' => 'comic_created', 'func' => 'feed_comic_created']);

function feed_comic_created($arr) {
	$comic_linked = '<a href="/'.$arr['comic_url'].'/">'.htmlspecialchars($arr['comic_name']).'</a>';
	$c = '<p>'.str_replace('{comic}', $comic_linked, _('{comic} was created.')).'</p>'."\n";
	return $c;
}
*/