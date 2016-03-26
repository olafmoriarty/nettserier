<?php
  // Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	$comicadm_urls->add_line(['url' => 'add-strip', 'script' => $tpf.'add.php']);
	$comicadm_menu->add_line(['text' => __('Upload new comic strip or page'), 'link' => '/n/dashboard/my-comics/{comic}/add-strip/', 'order' => 10]);

	$comicadm_urls->add_line(['url' => 'edit-strip', 'script' => $tpf.'edit.php']);
	$comicadm_menu->add_line(['text' => __('Edit uploaded comic strips and pages'), 'link' => '/n/dashboard/my-comics/{comic}/edit-strip/', 'order' => 11]);

// Stuff for user feed

if ($logged_in) {

// Settings (will be moved to a database ...)
$feed_settings = array();

$feed_settings['comics_mine'] = true;
$feed_settings['comics_i_follow'] = true;
$feed_settings['comics_other'] = false;

$feed_settings['albums_mine'] = true;
$feed_settings['albums_i_follow'] = true;
$feed_settings['albums_other'] = false;

$feed_settings['blogs_mine'] = true;
$feed_settings['blogs_i_follow'] = true;
$feed_settings['blogs_other'] = false;

if ($feed_settings['comics_mine'] || $feed_settings['comics_i_follow'] || $feed_settings['comics_other']) {
  $select_updates = 'SELECT comupd.updtype AS type, comupd.id, comupd.comic, comupd.pubtime, comupd.title, comupd.text, comupd.slug, comupd.user, comupd.imgtype AS other FROM ns_updates AS comupd';
  if (!$feed_settings['comics_other']) {
    // Don't show all comics, only selection. So find the selection ...
    $select_updates .= ' LEFT JOIN ns_user_comic_rel AS comupdr ON comupd.comic = comupdr.comic';
  }
  $select_updates .= ' WHERE comupd.updtype = \'c\' AND comupd.published = 1 AND comupd.pubtime <= NOW()';
  if (!$feed_settings['comics_other']) {
    $select_updates .= ' AND comupdr.reltype ';
    if ($feed_settings['comics_mine'] && $feed_settings['comics_i_follow']) {
      $select_updates .= 'IN (\'c\', \'e\', \'f\')';
    }
    elseif ($feed_settings['comics_mine']) {
      $select_updates .= 'IN (\'c\', \'e\')';
    }
    elseif ($feed_settings['comics_i_follow']) {
      $select_updates .= '= \'f\'';
    }
    $select_updates .= ' AND comupdr.user = {user_id} GROUP BY comupd.id';
  }
	$feed_queries->add_line(['text' => $select_updates]);
}


	$feed_functions->add_line(['type' => 'c', 'func' => 'feed_comic_strip']);

function feed_comic_strip($arr) {
	$comic_linked = '<a href="/'.$arr['comic_url'].'/comic/'.$arr['slug'].'/">'.htmlspecialchars($arr['comic_name']).'</a>';
	
	$alt = str_replace('{comic}', htmlspecialchars($arr['comic_name']), __('{comic} comic strip (no title)'));
	if ($arr['title']) {
		$alt .= htmlspecialchars($arr['title']);
	}
	
	$c = '';
	
	$c .= '<h3>'.str_replace(array('{comic}', '{creator}'), array($comic_linked, htmlspecialchars($arr['comic_creator'])), __('{comic} by {creator}')).'</h3>';
	$c .= '<p class="comic-para"><img src="/_ns/files/'.md5($arr['id'] . $arr['other']).'.'.$arr['other'].'" alt="'.$alt.'"></p>';

	if ($arr['title']) {
		$c .= '<h4>'.htmlspecialchars($arr['title']).'</h4>'."\n";
	}
	if ($arr['text']) {
		$c .= $arr['text'];
	}
	
	return $c;
}
}

// Related to editing comic strips
$edit_comic_single_menu->add_line(['text' => __('Edit strip'), 'link' => '/n/dashboard/my-comics/{comic}/edit-strip/{id}/', 'order' => 10]);


// Scheduler - MOVE to separate plugin :-)
$action['edit_strips_submit']->add_line(['function' => 'strip_scheduler']);

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

// Related to deleting comic strips
$edit_comic_single_menu->add_line(['text' => __('Delete strip'), 'link' => '/n/dashboard/my-comics/{comic}/edit-strip/delete/{id}/', 'order' => 90]);

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