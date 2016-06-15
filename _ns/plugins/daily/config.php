<?php

// Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	$n_urls->add_line(['url' => 'daily', 'script' => $tpf.'daily.php']);
	$n_menu->add_line(['text' => _('Today'), 'link' => '/n/daily/', 'order' => 20]);

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