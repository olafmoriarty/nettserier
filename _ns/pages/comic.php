<?php

// This is a comic! Hurray!

$is_comic_page = true;

// Get some basic information about the comic ...

$query = 'SELECT t1.name, GROUP_CONCAT(IF(usr.realname = \'\', usr.username, usr.realname) SEPARATOR \', \') AS creator FROM ns_comics AS t1 LEFT JOIN ns_user_comic_rel AS r ON t1.id = r.comic LEFT JOIN ns_users AS usr ON r.user = usr.id WHERE r.reltype = \'c\' AND t1.id = '.$comic_id.' GROUP BY t1.id';
$result = $conn->query($query);
$comic_info = $result->fetch_assoc();

$ns_title = htmlspecialchars($comic_info['name']);

// We need to create a header.

$header = '';

// Open <header>
$header .= '<header class="comicheader">'."\n";

// Title and creator(s)
$header .= '<div class="titleblock">';
$header .= '<h1 class="comic-title"><a href="/'.$comic_url.'/">'.htmlspecialchars($comic_info['name']).'</a></h1>'."\n";
$header .= '<p>'.str_replace('{creator}', htmlspecialchars($comic_info['creator']), __('by {creator}')).'</p>';
$header .= '</div>';

// For small screens: Links to show/hide menu and search
$header .= '<nav class="show-menu" id="show-menu">'."\n";
$header .= '<ul>';
$header .= '<li><a href="" class="icons show-menu-link" id="show-menu-link">'.__('Menu').'</a></li>';
$header .= '<li><a href="" class="icons show-search-link" id="show-search-link">'.__('Search').'</a></li>';
if ($logged_in) {
	$header .= '<li><a href="" id="show-user-menu-link"><img src="http://www.gravatar.com/avatar/'.md5(strtolower(trim($user_info['email']))).'?s=24&amp;d=mm" alt="'.htmlspecialchars($user_info['username']).'"></a></li>';
}
$header .= '</ul>'."\n";
$header .= '</nav>';

// Meny
$header .= '<nav class="main-menu" id="main-menu">'."\n";
$header .= str_replace('{comic}', $comic_url, $c_menu->return_ul());
$header .= "\n";
$header .= '</nav>'."\n";

// Nettserier.no menu
$header .= '<nav class="portal-menu" id="portal-menu">'."\n";
$header .= '<h2><a href="/">'.__(PAGE_TITLE).'</a></h2>';
$header .= $n_menu->return_ul();
$header .= "\n";
$header .= '</nav>'."\n";

// Follow button etc.
// ArrayHandler Action:
$header .= '<nav class="header-buttons" id="header-buttons">'."\n";
$header .= $action['comic_header_buttons']->run($comic_id);
$header .= '</nav>'."\n";




// Close <header>
$header .= '</header>'."\n";

if ($logged_in) {
	$header .= '<nav class="user-menu" id="user-menu">'."\n";
	$header .= '<ul>';
	$header .= '<li><a href="/n/dashboard/">'.str_replace('{n}', htmlspecialchars($user_info['username']), __('{n}\'s dashboard')).'</a></li>';
	$header .= '<li><a href="/n/dashboard/settings/">'.__('Settings').'</a></li>';
	$header .= '<li><a href="/n/log-out/">'.__('Log out').'</a></li>';
	$header .= '</ul>';
	$header .= '</nav>'."\n";
}

// Open main section
$header .= '<section class="main">'."\n";

// ArrayHandler Action:
$header .= $action['comic_below_header']->run($comic_id);

// We also need to show the right content.

$startpage = 'comic';

$folder = strtok('/');

// Default content if all other checks fail is the 404 page
$content_file = NS_PATH.'pages/404.php';

if (!$folder) {
	// Which script to load is stated in $startpage
	
	$folderscript = $c_urls->find('url', $startpage);
	if ($folderscript) {
		$content_file = $folderscript['script'];
	}
}
elseif ($folderscript = $c_urls->find('url', $folder)) {
	// The URL is nettserier.no/comicname/something/, which means a script should be loaded (unless the URL is wrong).
	$content_file = $folderscript['script'];
}

if (file_exists($content_file)) {
	include($content_file);
}
else {
	include(NS_PATH.'pages/404.php');
}

