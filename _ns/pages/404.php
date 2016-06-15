<?php
header('HTTP/1.0 404 Not Found');
$ns_title = _('404: Page not found');
$c .= '<h2>'._('404: Page not found').'</h2>';
$c .= '<p>'._('Well... This is awkward. We can\'t find the page you\'re looking for. No idea where it could be.').'</p>';

$c .= '<p>'._('Here are a few suggestions:').'</p>';
?>