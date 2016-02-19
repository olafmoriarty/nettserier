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

$n_urls = new ArrayHandler;
$c_urls = new ArrayHandler;
$n_menu = new ArrayHandler;

define('PAGE_TITLE', 'Nettserier.no');
$ns_tsep = ' :: ';

// ---------------------------------------------------------------------------
// LOG IN (stolen from https://github.com/peredurabefrog/phpSecureLogin/)
// ---------------------------------------------------------------------------

include(NS_PATH.'plugins/users/psl-config.php');

sec_session_start();

$logged_in = login_check();

// ---------------------------------------------------------------------------
// LOAD PLUGINS
// ---------------------------------------------------------------------------

// FOR TESTING; REMOVE LATER!!!!!
$user_info['level'] = 99;

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