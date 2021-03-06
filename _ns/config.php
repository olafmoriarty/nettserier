<?php

// Get functions and classes
include(NS_PATH.'functions.php');
include(NS_PATH.'classes.php');

// ---------------------------------------------------------------------------
// CONNECT TO DATABASE
// ---------------------------------------------------------------------------

// Get login information (stored in a separate folder for safety reasons)
include(NS_PATH.'mysql_conn.php');

// Connect to database
$conn = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);

// Check if this works
if ($conn->connect_error) {
	trigger_error('Could not connect to database: ' . $conn->connect_error, E_USER_ERROR);
}

// ---------------------------------------------------------------------------
// DATABASE SETUP
// ---------------------------------------------------------------------------

if (isset($_GET['special']) && $_GET['special'] == 'install')
	include(NS_PATH.'install.php');


// ---------------------------------------------------------------------------
// VARIOUS SETTINGS
// ---------------------------------------------------------------------------

$ns_style = 'default';
$c = '';
define('NS_URL', strtok($_SERVER['REQUEST_URI'], '?'));
define('NS_DOMAIN', 'http://'.$_SERVER['HTTP_HOST']);

$ns_title = '';
$is_comic_page = false;

// TO DO: Make a function run plugin setup files (pre-config) and move all ArrayHandlers THERE!

$n_urls = new ArrayHandler;
$c_urls = new ArrayHandler;
$d_urls = new ArrayHandler;

$n_menu = new ArrayHandler;
$c_menu = new ArrayHandler;
$d_menu = new ArrayHandler;

$delete_concequences = new ArrayHandler;

$feed_queries = new ArrayHandler;
$feed_functions = new ArrayHandler;

$feed_sidebar_menu = new ArrayHandler;

$open_source = new ArrayHandler;
$cookie_info = new ArrayHandler;

$head = new ArrayHandler;
$body_js = new ArrayHandler;

$edit_comic_single_menu = new ArrayHandler;

$filter = array();
$filter['html'] = new ActionHook('filter');

$action = array();
$action['edit_strips_submit'] = new ActionHook();
$action['frontpage'] = new ActionHook();
$action['footer'] = new ActionHook();
$action['delete_comic'] = new ActionHook();

$action['showcomic_text_after'] = new ActionHook();
$action['showcomic_on_page_after'] = new ActionHook();

$action['comic_header_buttons'] = new ActionHook();
$action['comic_below_header'] = new ActionHook();

$action['user_page'] = new ActionHook();

$action['after_footer'] = new ActionHook();

$action['delete_comic'] = new ActionHook();


// Add javascript for Adaptive Images
$head->add_line(['text' => '<script>document.cookie=\'resolution=\'+Math.max(screen.width,screen.height)+\'; path=/\';</script>']);
$open_source->add_line(['name' => 'Adaptive Images', 'developer' => 'Matt Wilcox', 'link' => 'http://adaptive-images.com/', 'license' => 'Creative Commons Attribution 3.0 Unported License']);
$cookie_info->add_line(['name' => 'resolution', 'desc' => _('This cookie is set by the <em>Adaptive Images</em> plugin and is used to preprocess image files based on your screen resolution before sending them to your device, so that we won\'t eat up all your cell phone data. (To be honest I have no idea why this operation requires cookies, but hey, it seems to work.)')]);

define('PAGE_TITLE', 'Nettserier.no');
$ns_tsep = ' :: ';

// ---------------------------------------------------------------------------
// LOCALIZATION
// ---------------------------------------------------------------------------

$locale = '';
$gt_languages = ['nn', 'nb', 'en'];
$gt_locales = ['nn' => 'nn_NO', 'nb' => 'nb_NO', 'en' => 'en_US'];

if (isset($_GET['language']) && in_array($_GET['language'], $gt_locales)) {
	$locale = $_GET['language'];
	setcookie('language', $_GET['language'], time() + (60 * 60 * 24 * 365), '/');
}
elseif (isset($_COOKIE['language']) && in_array($_COOKIE['language'], $gt_locales)) {
	$locale = $_COOKIE['language'];
}
else {
	$httplangs_arr = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
	$httplangs_count = count($httplangs_arr);
	$http_languages = array();
	$http_priorities = array();
	for ($i = 0; $i < $httplangs_count; $i++) {
		$tmp_arr = explode(';q=', $httplangs_arr[$i]);
		$shortcode = substr($tmp_arr[0], 0, 2);
		if (isset($tmp_arr[1]) && $tmp_arr[1]) {
			$priority = $tmp_arr[1];
		}
		else {
			$priority = 1;
		}
		if (in_array($shortcode, $http_languages)) {
			reset($http_languages);
			while ($lang_code = current($http_languages)) {
				if ($lang_code == $shortcode) {
					$key = key($http_languages);
					break;
				}
				next($http_languages);
			}
			if ($priority > $http_priorities[$key]) {
				$http_priorities[$key] = $priority;
			}
		}
		else {
			$http_languages[] = $shortcode;
			$http_priorities[] = $priority;
		}
	}
	array_multisort($http_priorities, SORT_DESC, $http_languages);
	$httplangs_count = count($http_languages);
	for ($i = 0; $i < $httplangs_count; $i++) {
		if (in_array($http_languages[$i], $gt_languages)) {
			$locale = $gt_locales[$http_languages[$i]];
			break;
		}
	}
}
if (!$locale) {
	$locale = 'en_US';
}

putenv('LANG='.$locale.'.UTF8'); 
setlocale(LC_ALL, $locale.'.UTF8');

$gtdomain = 'nettserier';

if (isset($_GET['special']) && $_GET['special'] == 'language') {
	bindtextdomain($gtdomain, NS_PATH.'translation/nocache'); 
	bind_textdomain_codeset($gtdomain, 'UTF-8');
	textdomain($gtdomain);
}
bindtextdomain($gtdomain, NS_PATH.'translation'); 
bind_textdomain_codeset($gtdomain, 'UTF-8');
textdomain($gtdomain);

$cookie_info->add_line(['name' => 'language', 'desc' => str_replace('{page}', PAGE_TITLE, _('This cookie is set when you select a language and stores information about which language you prefer reading {page} in. This cookie won\'t be set until you\'re choosing a language, and deleting it is completely safe - without it, {page} will be shown in your browser\'s default language.'))]);


// ---------------------------------------------------------------------------
// LOG IN (stolen from https://github.com/peredurabefrog/phpSecureLogin/)
// ---------------------------------------------------------------------------

include(NS_PATH.'plugins/users/psl-config.php');

sec_session_start();

$logged_in = login_check();
$user_info = array();
if ($logged_in) {
	$query = 'SELECT id, username, email, level, sponsor FROM ns_users WHERE id = '.$_SESSION['user_id'].' LIMIT 1';
	$result = $conn->query($query);
	$user_info = $result->fetch_assoc();
}
else {
	$user_info['level'] = 0;
}

// ---------------------------------------------------------------------------
// LOAD PLUGINS
// ---------------------------------------------------------------------------

// FOR TESTING; REMOVE LATER!!!!!
// $user_info['level'] = 100;

$query = 'SELECT folder FROM ns_plugins WHERE level <= '.$user_info['level'];
$result = $conn->query($query);
if ($result !== false) {
	$num = $result->num_rows;
	if ($num) {
		$result->data_seek(0);
		while ($arr = $result->fetch_assoc()) {
			$folder = $arr['folder'];
			if (is_dir(NS_PATH.'plugins/'.$folder) && file_exists(NS_PATH.'plugins/'.$folder.'/config.php')) {
				include(NS_PATH.'plugins/'.$folder.'/config.php');
			}
		}
	}
}
