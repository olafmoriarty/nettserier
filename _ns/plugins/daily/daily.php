<?php

$year = strtok('/');

$show_day = false;
if (!$year || !is_numeric($year)) {
	$show_day = time();
}
else {

	$month = strtok('/');
	if (!$month || !is_numeric($month)) {
		$pagetitle = str_replace('{time}', $year, __('Comics archive for {time}'));
		$ns_title = $pagetitle;
		$c .= '<h2>'.$pagetitle.'</h2>';
		$c .= '<p>'.__('Select a month:').'</p>';
		$c .= '<ul>';
		for ($i = 1; $i <= 12; $i++) {
			$c .= '<li>';
			$c .= '<a href="/n/daily/'.$year.'/'.$i.'/">';
			$c .= strftime('%B %Y', mktime(0, 0, 0, $i, 1, $year));
			$c .= '</a>';
			$c .= '</li>';
		}
		$c .= '</ul>'."\n";
	}
	else {
		$day = strtok('/');
		if (!$day || !is_numeric($day)) {
			$unixmonth = mktime(0, 0, 0, $month, 1, $year);
			$ns_title = strftime('%B %Y', $unixmonth);
			$c .= '<h2>'.$ns_title.'</h2>';
			$c .= '<p>'.__('Select a day:').'</p>';
			$c .= show_calendar($year, $month, '/n/daily/{year}/{month}/{day}/');
		}
		else {
			$show_day = mktime(0, 0, 0, $month, $day, $year);
		}

	}

	
}

if ($show_day) {
	$pagetitle = strftime(__('%B %e, %Y'), $show_day);
	$ns_title = $pagetitle;
	$c .= '<h2>'.$pagetitle.'</h2>';
	$year = date('Y', $show_day);
	$month = date('n', $show_day);
	$day = date('j', $show_day);

	$day_comics = new ShowComic;
	$day_comics->set_min_time(mktime(0, 0, 0, $month, $day, $year));
	$day_comics->set_max_time(mktime(23, 59, 59, $month, $day, $year));
	$day_comics->set_count(0);
	$day_comics->set_comic_title(true);
	$day_comics->set_linking(true);
	$comics = $day_comics->show();
	if ($comics) {
		$c .= $comics;
	}
	else {
		$c .= '<p>'.__('Sorry! There are no comics to show for this date.').'</p>';
	}
}