<?php
// Unset all session values 
$_SESSION = array();
 
// get session parameters 
$params = session_get_cookie_params();
 
// Delete the actual cookie. 
setcookie(session_name(),
        '', time() - 42000, 
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]);

 // Delete "Remember me"-stuff
$expiration_time = -3600; // One hour ago
setcookie('user_id', $user_id, time() + $expiration_time, '/', '', SECURE);
setcookie('login_string', $_SESSION['login_string'], time() + $expiration_time, '/', '', SECURE);

// Destroy session 
session_destroy();
header('Location: '.NS_DOMAIN);
exit;