<?php

// ----------

$rows = 50;

$ns_title = __('Browse comics');
$c .= '<h2>'.__('Browse comics').'</h2>';


$order_fields = ['updated' => 't1.last_update', 'name' => 't1.name', 'creator' => 'creator'];
if (isset($_GET['order']) && isset($order_fields[$_GET['order']])) {
	$orderby = $_GET['order'];
}
else {
	$orderby = 'updated';
}

// Reverse direction some fields (so that the default setting for time updated can be descending)
$reversed = ['updated'];

if ((isset($_GET['dir']) && $_GET['dir'] == 'desc') xor in_array($orderby, $reversed)) {
	$orderdir = 'DESC';
}
else {
	$orderdir = 'ASC';
}

$query = 'SELECT SQL_CALC_FOUND_ROWS t1.url, t1.name, GROUP_CONCAT(IF(usr.realname = \'\', usr.username, usr.realname) SEPARATOR \', \') AS creator, t1.last_update FROM (SELECT c.id, c.url, c.name, MAX(u.pubtime) AS last_update FROM ns_updates AS u LEFT JOIN ns_comics AS c ON u.comic = c.id WHERE u.published = 1 AND u.pubtime < NOW() GROUP BY c.id) AS t1 LEFT JOIN ns_user_comic_rel AS r ON t1.id = r.comic LEFT JOIN ns_users AS usr ON r.user = usr.id WHERE r.reltype = \'c\' GROUP BY t1.id ORDER BY '.$order_fields[$orderby].' '.$orderdir.' '.limitstring($rows);
$result = $conn->query($query);

$query = 'SELECT FOUND_ROWS()';
$fr_result = $conn->query($query);
$fr_arr = $fr_result->fetch_row();
$total_rows = $fr_arr[0];

$pagecount = ceil($total_rows / $rows);

$num = $result->num_rows;

if ($num) {

$navigation = limitstring_nav($pagecount);
$c .= $navigation;

	$c .= '<table class="browse-table">'."\n";
		$c .= '<tr>'."\n";
		$c .= '<th><a href="'.NS_URL.'?order=name'.maybedesc('name').'">'.__('Name').'</a></th>'."\n";
		$c .= '<th><a href="'.NS_URL.'?order=creator'.maybedesc('creator').'">'.__('Creator').'</a></th>'."\n";
		$c .= '<th><a href="'.NS_URL.'?order=updated'.maybedesc('updated', true).'">'.__('Last update').'</a></th>'."\n";
		$c .= '</tr>'."\n";
	while ($arr = $result->fetch_assoc()) {
		$c .= '<tr>'."\n";
		$c .= '<td class="main-cell"><a href="/'.$arr['url'].'/">'.htmlspecialchars($arr['name']).'</a></td>'."\n";
		$c .= '<td>'.htmlspecialchars($arr['creator']).'</td>'."\n";
		$c .= '<td>'.$arr['last_update'].'</td>'."\n";
		$c .= '</tr>'."\n";
	}
	$c .= '</table>'."\n";
}

$c .= $navigation;

// Function which figures out what should happen if we click one of the "order" links in the table - ascending or descending results?
function maybedesc($field, $is_default = false) {
	$order = false;
	$dir = false;
	if (isset($_GET['order'])) {
		$order = $_GET['order'];
	}
	if (isset($_GET['dir'])) {
		$dir = $_GET['dir'];
	}

	// String to return if we should be ascending
	$asc = '';
	
	// String to return if we should be descending
	$desc = '&amp;dir=desc';

	// If no sorting has occured yet:
	if (!$order) {
		if ($is_default) {
			return $desc;
		}
		else {
			return $asc;
		}
	}

	// Else if sorting has occured, but on a completely different column
	elseif ($order != $field) {
		return $asc;
	}

	// Else if sorted on THIS column
	else {
		if ($dir == 'desc') {
			return $asc;
		}
		else {
			return $desc;
		}
	}
	
}