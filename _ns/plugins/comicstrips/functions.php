<?php

// FRONT PAGE BOXES

function fp_splash() {
	$c = '<section class="fp-splash">';
	$cc = new ShowComic;
	$cc->set_result_type('comic');
	$cc->set_comic_title(true);
	$cc->set_text(false);
	$cc->set_order('RAND()');
	$cc->set_count(5);
	$c .= $cc->show();
	$c .= '</section>';
	return $c;
}

function fp_popular() {
	$c = '<section class="fp-comics-display">';
	$c .= '<h2>'.__('Trending comics').'</h2>';
	$cc = new ShowComic;
	$cc->set_count(6);
	$cc->set_comic_title(true);
	$cc->set_text(false);
	$cc->set_result_type('comic');
	
	
	$c .= '<div class="comics">';
	$c .= $cc->show();
	$c .= '</div>';
	$c .= '</section>';
	return $c;
}

// FEED BOXES

function feed_comic_strip($arr) {
	$cc = new ShowComic;
	$cc->set_comic_title(true);
	$c = $cc->show_comic($arr);
	return $c;
}

// DELETE COMICS

function delete_all_strips($comic) {
	global $conn;
	if (is_numeric($comic)) {
		$query = 'SELECT id FROM ns_updates WHERE comic = '.$comic;
		$result = $conn->query($query);
		$num = $result->num_rows;
		if ($num) {
			while ($arr = $result->fetch_assoc()) {
				delete_strip($arr['id']);
			}
		}
	}
}

function delete_strip($id, $arr = false) {
	global $conn;
	if (!$arr || !$arr['imgtype']) {
		$query = 'SELECT imgtype FROM ns_updates WHERE id = '.$id;
		$result = $conn->query($query);
		$arr = $result->fetch_assoc();
	}
	$filename = NS_PATH.'files/'.md5($id . $arr['imgtype']).'.'.$arr['imgtype'];

	// Unlink image file
	unlink($filename);

	// Delete row from database
	$query = 'DELETE FROM ns_updates WHERE id = '.$id;
	$conn->query($query);

	// Add ActionHook later
}

// SCHEDULER

function strip_scheduler() {
	global $values, $ids_sorted;

	if ($_POST['schedule'] == 'schedule') {

		// First day:
		$day_one = time();
		if ($_POST['schedule-schedule-first'] == 'time' && $_POST['schedule-first-datetime-date'] && $_POST['schedule-first-datetime-time']) {
			$pubtime = $_POST['schedule-first-datetime-date'].' '.$_POST['schedule-first-datetime-time'];
			$day_one = strtotime($pubtime);
			if ($day_one === false) {
				$day_one = time();
			} 
		}
		$values[$ids_sorted[0]]['pubtime'] = mysql_string(date('Y-m-d H:i:s', $day_one));

		// Which weekdays are selected?
		$active_days = array();
		for ($i = 1; $i <= 7; $i++) {
			if ($_POST['schedule-weekday-'.$i]) {
				$active_days[] = $i;
			}
		}

		// If none are selected, ALL are selected.
		if (!count($active_days)) {
			$active_days = [1, 2, 3, 4, 5, 6, 7];
		}

		// Weekday of first day
		$wd_one = date('N', $day_one);
		$days = 0;

		$num = count($ids_sorted);

		for ($i = 1; $i < $num; $i++) {
			// Increase days by one
			$days++;

			while (!in_array((($days + $wd_one - 1) % 7) + 1, $active_days)) {
				$days++;
			}
			$values[$ids_sorted[$i]]['pubtime'] = mysql_string(date('Y-m-d H:i:s', mktime(date('H', $day_one), date('i', $day_one), date('s', $day_one), date('n', $day_one), date('j', $day_one) + $days, date('Y', $day_one))));
		}
	}
}

