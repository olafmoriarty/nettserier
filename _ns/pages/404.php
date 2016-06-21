<?php
header('HTTP/1.0 404 Not Found');
$ns_title = _('404: Page not found');
$c .= '<h2>'._('404: Page not found').'</h2>';
$c .= '<p>'._('Well... This is awkward. We can\'t find the page you\'re looking for. No idea where it could be.').'</p>';

$c .= '<p>'._('Sorry!').'</p>';

if (isset($comic_url)) {
	if (preg_match('/\/([0-9]+)\/([0-9]+)\/([0-9]+)\/?/', NS_URL, $matches)) {
		$query = 'SELECT slug FROM ns_updates WHERE updtype = \'c\' AND comic = '.comic_id($comic_url).' AND pubtime LIKE \''.$matches[1].'-'.$matches[2].'-'.$matches[3].'%\' LIMIT 1';
		$result = $conn->query($query);
		$num = $result->num_rows;
		$arr = $result->fetch_assoc();
		if ($num) {
			header('Location: '.NS_DOMAIN.'/'.$comic_url.'/comic/'.$arr['slug'].'/');
			exit;
		}
	}
	$c .= '<p><a href="/'.$comic_url.'/">'._('Go back to the comic').'</a></p>';

}
	$c .= '<p><a href="/">'.str_replace('{page}', PAGE_TITLE, _('Go back to {page}')).'</a></p>';

if (preg_match('/^\/dagfordag\/?(.*)/', NS_URL, $matches)) {
	header('Location: '.NS_DOMAIN.'/n/daily/'.$matches[1]);
	exit;
}

if (preg_match('/^\/_striper\/jellyvampire-1304892000\.jpg(.*)/', NS_URL, $matches)) {
	header('Location: '.NS_DOMAIN.'/jellyvampire/comic/27310/');
	exit;
}

?>