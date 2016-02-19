<?php

if ($logged_in) {
	$c .= '<p>There will be something interesting here soon. I hope.</p>';
}
else {
	$c .= '<p>There will be something interesting here soon. I hope.</p>';
	$c .= '<p><a href="/n/log-in/">Log in</a></p>';
}

?>