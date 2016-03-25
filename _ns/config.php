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

// TO DO: Make a function run plugin setup files (pre-config) and move all ArrayHandlers THERE!

$n_urls = new ArrayHandler;
$c_urls = new ArrayHandler;
$d_urls = new ArrayHandler;

$n_menu = new ArrayHandler;
$d_menu = new ArrayHandler;

$delete_concequences = new ArrayHandler;

$feed_queries = new ArrayHandler;
$feed_functions = new ArrayHandler;

$head = new ArrayHandler;
$body_js = new ArrayHandler;

// Add javascript for Adaptive Images
$head->add_line(['text' => '<script>document.cookie=\'resolution=\'+Math.max(screen.width,screen.height)+\'; path=/\';</script>']);

define('PAGE_TITLE', 'Nettserier.no');
$ns_tsep = ' :: ';

// ---------------------------------------------------------------------------
// LOG IN (stolen from https://github.com/peredurabefrog/phpSecureLogin/)
// ---------------------------------------------------------------------------

include(NS_PATH.'plugins/users/psl-config.php');

sec_session_start();

$logged_in = login_check();
if ($logged_in) {
	$query = 'SELECT id, username, email, level FROM ns_users WHERE id = '.$_SESSION['user_id'].' LIMIT 1';
	$result = $conn->query($query);
	$user_info = $result->fetch_assoc();
}

// ---------------------------------------------------------------------------
// LOAD PLUGINS
// ---------------------------------------------------------------------------

// FOR TESTING; REMOVE LATER!!!!!
$user_info['level'] = 100;

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
?>