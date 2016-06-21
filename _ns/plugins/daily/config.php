<?php

// Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	$n_urls->add_line(['url' => 'daily', 'script' => $tpf.'daily.php']);
	$n_menu->add_line(['text' => _('Today'), 'link' => '/n/daily/', 'order' => 20]);

	$feed_sidebar_menu->add_line(['text' => _('Today\'s updates'), 'link' => '/n/daily/', 'order' => 20]);


function show_calendar($year, $month, $url) {
	$c = '<table class="calendar">';
	$day1 = mktime(0, 0, 0, $month, 1, $year);
	$last_date = date('t', $day1);
	$weekday1 = date('N', $day1) - 1;
	$c .= '<tr>';
	// Blank cells before 1st
	if ($weekday1) {
		for ($i = 0; $i < $weekday1; $i++) {
			$c .= '<td></td>';
		}
	}
	for ($i = 1; $i <= $last_date; $i++) {
		$c .= '<td><a href="'.str_replace(['{year}', '{month}', '{day}'], [$year, $month, $i], $url).'">'.$i.'</a></td>';
		if (($i + $weekday1) % 7 == 0 && $i != $last_date) {
			$c .= '</tr><tr>';
		}
	}
	$days_left = 7 - (($i + $weekday1) % 7);
	if ($days_left != 7) {
		for ($i = 0; $i < $days_left; $i++) {
			$c .= '<td></td>';
		}
		
	}
	$c .= '</tr>';
	$c .= '</table>';
	return $c;
}

// Add daily updates to feed

	$feed_queries->add_line(['text' => 'SELECT \'daily\' AS type, CONCAT(MIN(id), \'-\', MAX(id)) AS id, MAX(daily.comic) AS comic, MIN(daily.pubtime) AS pubtime, NULL as title, NULL as text, NULL as slug, NULL as user, NULL as other FROM ns_updates AS daily LEFT JOIN (SELECT comic, time FROM ns_user_comic_rel WHERE user = '.$user_info['id'].' AND reltype = \'b\') AS dblocked ON daily.comic = dblocked.comic WHERE daily.updtype = \'c\' AND daily.pubtime >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND daily.published = 1 AND daily.pubtime <= NOW() AND dblocked.time IS NULL GROUP BY DATE(daily.pubtime)']);

	$feed_functions->add_line(['type' => 'daily', 'func' => 'feed_daily']);

function feed_daily($arr) {
	global $conn;
	$day = strtotime($arr['pubtime']);
	$c = '<h3><a href="/n/daily/'.date('Y', $day).'/'.date('m', $day).'/'.date('d', $day).'/">'.str_replace('{date}', strftime(_('%A %B %e'), $day), _('Updates {date}')).'</a></h3>';

	$query = 'SELECT COUNT(id) AS thenumber FROM ns_updates WHERE updtype = \'c\' AND pubtime <= NOW() AND published = 1 AND DATE(pubtime) = DATE(\''.$arr['pubtime'].'\')';
	$result = $conn->query($query);
	echo $conn->error;
	$arr = $result->fetch_assoc();
	$count = $arr['thenumber'];
	$c .= str_replace('{n}', $count, _('{n} new comic strips, including:'));

	$c .= '<section class="fp-comics-display">';
	$cc = new ShowComic;
	$cc->set_count(3);
	$cc->set_order('RAND()');
	$cc->set_comic_title(true);
	$cc->set_text(false);
	$cc->set_linking(true);
	$cc->set_min_time(mktime(0, 0, 0, date('n', $day), date('j', $day), date('Y', $day)));
	$cc->set_max_time(mktime(23, 59, 59, date('n', $day), date('j', $day), date('Y', $day)));
	$cc->set_result_type('comic');
	
	
	$c .= '<div class="comics">';
	$c .= $cc->show();
	$c .= '</div>';
	$c .= '</section>';

	return $c;
}
