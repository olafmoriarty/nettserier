<?php
// session_start()
session_start();

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
// LOG IN
// ---------------------------------------------------------------------------

$logged_in = false;
$user_info = array();
$salt['user_session'] = 'What\'s this, then? "Romanes eunt domus"? People called Romanes, they go, the house?';

// Has the user submitted a login form on the previous page?

if (isset($_POST['login_username']) && isset($_POST['login_password'])) {
	$username = stripslashes($_POST['login_username']);
	$password = stripslashes($_POST['login_password']);
	
	// Search table for users with that username (or e-mail), and get ID and hashed password
	$query = 'SELECT id, password FROM '.WCX_SQL_USERS.' WHERE username=\''.$conn->real_escape_string($username).'\'';
	$result = $conn->query($query);
	// Does mysql query work?
	if ($result !== false) {
		// Does mysql_query return rows?
		$num = $result->num_rows;
		if ($num) {
			// Go to beginning of result
			$result->data_seek(0);
			// Get row
			$row = $result->fetch_assoc();

			// The username is correct, but is the password?
			if (password_verify($password, $row['password'])) {
				// The password matches, too! Create user session!
				$_SESSION['user_id'] = $row['id'];
				$_SESSION['user_hash'] = password_hash($row['id'].$row['password'].$salt['user_session'], PASSWORD_DEFAULT);
			}
			else {
				// The password was incorrect. Show some kind of error message somewhere.
			}
		}
		else {
			// The username was incorrect. Show some kind of error message somewhere.
		}
	}
	else {
		// Something went wrong with the MySQL query. Show some kind of error message somewhere.
	}

}

// If sessions are not set, but cookies are, get session values from cookies:

// COMING SOON

// If sessions are set, attempt to log in the user:
if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id']) && isset($_SESSION['user_hash'])) {
	$id = $_SESSION['user_id'];

	// Search database for user with given ID, find their password hash
	$query = 'SELECT id, username, email, password, level FROM '.WCX_SQL_USERS.' WHERE id='.$id;
	$result = $conn->query($query);
	// Does mysql query work?
	if ($result !== false) {
		// Does mysql_query return rows?
		$num = $result->num_rows;
		if ($num) {
			// Go to beginning of result
			$result->data_seek(0);
			// Get row
			$row = $result->fetch_assoc();
			// Get password hash
			$hash1 = $row['password'];

			// Check if the hashed id.password.salt string matches the session
			if (password_verify($id.$hash1.$salt['user_session'], $_SESSION['user_hash'])) {
				// Session matches, let's log in the user!
				$logged_in = true;

				// Save user info to an array
				$user_info = $row;

				// Remove password from that array, we don't need it here...
				unset($user_info['password']);
			}
		}
	}
}

// ---------------------------------------------------------------------------
// VARIOUS SETTINGS
// ---------------------------------------------------------------------------


$ns_style = 'default';
$c = '';
define('NS_URL', strtok($_SERVER['REQUEST_URI'], '?'));

$n_urls = new ArrayHandler;
$c_urls = new ArrayHandler;
$n_menu = new ArrayHandler;

define('PAGE_TITLE', 'Nettserier.no');
$ns_tsep = ' :: ';

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