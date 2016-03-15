<?php

if ($logged_in) {
	include(NS_PATH.'pages/feed.php');
}
else {
	$c .= '<p>There will be something interesting here soon. I hope.</p>';
	$c .= '<p><a href="/n/log-in/">Log in</a></p>';
}

?>