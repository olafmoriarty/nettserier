<?php

if ($logged_in) {
	include(NS_PATH.'pages/feed.php');
}
else {
	$c .= $action['frontpage']->run();
	$c .= '<p><a href="/n/log-in/">Log in</a></p>';
}

?>