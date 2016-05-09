<?php

$action['after_footer']->add_line(['function' => 'ga_tracking_code']);

function ga_tracking_code() {
	$c = '<script>'."\n";
	$c .= '(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){'."\n";
	$c .= '(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),'."\n";
	$c .= 'm=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)'."\n";
	$c .= '})(window,document,\'script\',\'https://www.google-analytics.com/analytics.js\',\'ga\');'."\n";
	$c .= 'ga(\'create\', \'UA-4412960-1\', \'auto\');'."\n";
	$c .= 'ga(\'send\', \'pageview\');'."\n";
	$c .= '</script>'."\n";
	return $c;
}