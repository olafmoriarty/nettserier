<?php

// ---------------------------------------------------------------------------
// GENERATE MAIN CONTENT
// ---------------------------------------------------------------------------

$folder = strtok(NS_URL, '/');

// Default content if all other checks fail is the 404 page
$content_file = NS_PATH.'pages/404.php';

if (!$folder) {
	// Load main page
	$content_file = NS_PATH.'pages/main.php';
}
elseif ($folder == 'n') {
	// The URL is nettserier.no/n/something/, which means a script should be loaded (unless the URL is wrong).
	// Check if the script is set in $n_urls
  $folder = strtok('/');
	$folderscript = $n_urls->find('url', $folder);
	if ($folderscript) {
		$content_file = $folderscript['script'];
	}
}
elseif ($comic_id = comic_id($folder)) {
	$comic_url = $folder;
	$content_file = NS_PATH.'pages/comic.php';
}

if (file_exists($content_file)) {
	include($content_file);
}
else {
	include(NS_PATH.'pages/404.php');
}

// ---------------------------------------------------------------------------
// GENERATE HEADER
// ---------------------------------------------------------------------------

// Define a variable for the header
$header = '';

// Open <header>
$header .= '<header>'."\n";

// Page title
$header .= '<h1 class="page-title"><a href="/">'.__(PAGE_TITLE).'</a></h1>'."\n";

// For small screens: Links to show/hide menu and search
$header .= '<nav class="invisible" id="show-menu">'."\n";
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
$header .= $n_menu->return_ul();
$header .= "\n";
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

// ---------------------------------------------------------------------------
// GENERATE FOOTER
// ---------------------------------------------------------------------------

// Define footer variable
$footer = '';

// Close main section
$footer .= '</section>'."\n";

$footer .= '<footer>'."\n";
$footer .= '<nav><h2 class="expand">Language</h2><ul><li><a href="">English</a></li><li><a href="">Norsk bokmål</a></li><li><a href="">Norsk nynorsk</a></li></ul></nav>';
$footer .= '<nav><h2 class="expand">Help</h2><ul><li><a href="">About</a></li><li><a href="">FAQ</a></li><li><a href="">Privacy policy</a></li><li><a href="">Cookies</a></li></ul></nav>';
$footer .= '<nav><h2 class="expand">Follow us!</h2><ul><li><a href="">Facebook</a></li><li><a href="">Twitter</a></li><li><a href="">YouTube</a></li><li><a href="">Patreon</a></li><li><a href="">Github</a></li></ul></nav>';
$footer .= '<div class="copyright">Nettserier.no &copy; Comicopia AS, 2006-2016<br>All comics are &copy; their respective creators</div>';
$footer .= '</footer>'."\n";

// ---------------------------------------------------------------------------
// ADD HEADER/FOOTER TO CONTENT
// ---------------------------------------------------------------------------

$c = $header.$c;

$c .= '
<script src="/_ns/plugins/jquery/jquery.js"></script>
<script src="/_ns/basic.js"></script>
';
$c .= implode("\n", $body_js->return_arr())."\n";

$c .= $footer;
?>