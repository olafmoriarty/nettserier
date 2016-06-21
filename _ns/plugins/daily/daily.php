<?php

$year = strtok('/');

$show_day = false;
if (!$year || !is_numeric($year)) {
	$show_day = time();
}
else {

	$month = strtok('/');
	if (!$month || !is_numeric($month)) {
		$pagetitle = str_replace('{time}', $year, _('Comics archive for {time}'));
		$ns_title = $pagetitle;
		$c .= '<h2>'.$pagetitle.'</h2>';
		$c .= '<p>'._('Select a month:').'</p>';
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
			$c .= '<p>'._('Select a day:').'</p>';
			$c .= show_calendar($year, $month, '/n/daily/{year}/{month}/{day}/');
		}
		else {
			$show_day = mktime(0, 0, 0, $month, $day, $year);
		}
	}
}

if ($show_day) {
	$pagetitle = strftime(_('%B %e, %Y'), $show_day);
	$ns_title = $pagetitle;
	$c .= '<h2>'.$pagetitle.'</h2>';
	$year = date('Y', $show_day);
	$month = date('n', $show_day);
	$day = date('j', $show_day);

	$navigation = '<nav class="navigate-pages"><ul>';
	$one_month_ago = strtotime(date('Y-m-d', $show_day).' - 1 month');
	$navigation .= '<li><a href="/n/daily/'.date('Y/m/d', $one_month_ago).'/">'.str_replace('{date}', strftime(_('%B %e, %Y'), $one_month_ago), _('Previous month ({date})')).'</a></li>';

	$one_day_ago = strtotime(date('Y-m-d', $show_day).' - 1 day');
	$navigation .= '<li><a href="/n/daily/'.date('Y/m/d', $one_day_ago).'/" class="prev" rel="prev">'.str_replace('{date}', strftime(_('%B %e, %Y'), $one_day_ago), _('Previous day ({date})')).'</a></li>';

	$today = mktime(0, 0, 0, date('n'), date('j'), date('Y'));
	if ($show_day < $today) {
		$next_day = strtotime(date('Y-m-d', $show_day).' + 1 day');
		$navigation .= '<li><a href="/n/daily/'.date('Y/m/d', $next_day).'/" class="next" rel="next">'.str_replace('{date}', strftime(_('%B %e, %Y'), $next_day), _('Next day ({date})')).'</a></li>';
		$next_month = strtotime(date('Y-m-d', $show_day).' + 1 month');
		if ($next_month > $today) {
			$navigation .= '<li><a href="/n/daily/'.date('Y/m/d', $today).'/" class="next" rel="next">'._('Today').'</a></li>';
		}
		else {
			$navigation .= '<li><a href="/n/daily/'.date('Y/m/d', $next_month).'/" class="next" rel="next">'.str_replace('{date}', strftime(_('%B %e, %Y'), $next_month), _('Next month ({date})')).'</a></li>';
		}
	}

	$navigation .= '</ul></nav>';


	$c .= $navigation;

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
		$c .= '<p>'._('Sorry! There are no comics to show for this date.').'</p>';
	}

	$c .= $navigation;
}