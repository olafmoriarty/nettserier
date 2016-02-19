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
else {
}


include($content_file);

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
$header .= '<li><a href="" id="show-menu-link">'.__('Menu').'</a></li>';
$header .= '<li><a href="">'.__('Search').'</a></li>';
if ($logged_in) {
	$header .= '<li><img src="http://www.gravatar.com/avatar/'.md5(strtolower(trim($user_info['email']))).'?s=24&amp;d=mm" alt="'.htmlspecialchars($user_info['username']).'"></li>';
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
	$header .= '<nav class="user-menu">'."\n";
	$header .= '<ul>';
	$header .= '<li><img src="http://www.gravatar.com/avatar/'.md5(strtolower(trim($user_info['email']))).'?s=24&amp;d=mm" alt="'.htmlspecialchars($user_info['username']).'"></li>';
	$header .= '<li>'.str_replace('{n}', htmlspecialchars($user_info['username']), __('You are logged in as {n}.')).'</li>';
	$header .= '<li><a href="/n/dashboard/">'.__('Dashboard').'</a></li>';
	$header .= '<li><a href="/n/user-settings/">'.__('Settings').'</a></li>';
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
$footer = '</section>'."\n";

// ---------------------------------------------------------------------------
// ADD HEADER/FOOTER TO CONTENT
// ---------------------------------------------------------------------------

$c = $header.$c;

$c .= '
<script src="/_ns/plugins/jquery/jquery.js"></script>
<script src="/_ns/basic.js"></script>
';
$c .= $footer;
?>