<?php

// Find offset based on $_GET['page'] - this should probably be moved into a function later.

$rows_per_page = 50;
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
  $page = $_GET['page'];
}
else {
  $page = 1;
}

$offset = ($page - 1) * $rows_per_page;
$limitstring = 'LIMIT '.$offset.', '.$rows_per_page;

// How many pages are there?

// ----------

$ns_title = __('Browse comics');
$c .= '<h2>'.__('Browse comics').'</h2>';

$query = 'SELECT t1.url, t1.name, GROUP_CONCAT(IF(usr.realname = \'\', usr.username, usr.realname) SEPARATOR \', \') AS creator, t1.last_update FROM (SELECT c.id, c.url, c.name, MAX(u.pubtime) AS last_update FROM ns_updates AS u LEFT JOIN ns_comics AS c ON u.comic = c.id WHERE u.published = 1 AND u.pubtime < NOW() GROUP BY c.id) AS t1 LEFT JOIN ns_user_comic_rel AS r ON t1.id = r.comic LEFT JOIN ns_users AS usr ON r.user = usr.id WHERE r.reltype = \'c\' GROUP BY t1.id '.$limitstring;
$result = $conn->query($query);

$num = $result->num_rows;

if ($num) {
  $c .= '<table>';
  while ($arr = $result->fetch_assoc()) {
    $c .= '<tr><td><a href="/'.$arr['url'].'/">'.htmlspecialchars($arr['name']).'</a></td><td>'.htmlspecialchars($arr['creator']).'</td><td>'.$arr['last_update'].'</td></tr>';
  }
  $c .= '</table>';
}