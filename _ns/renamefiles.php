<?php

// Host
	$mysql_host = 'localhost';
// Database
	$mysql_db = 'nettserier';
// Username
	$mysql_user = 'username';
// Password
	$mysql_pass = 'password';

// Connect to database
$conn = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);

$query = 'SELECT u.id, u.imgtype, u.comic, c.url, UNIX_TIMESTAMP(u.pubtime) AS time FROM ns_updates AS u LEFT JOIN ns_comics AS c ON u.comic = c.id WHERE updtype = \'c\' ORDER BY u.pubtime';
$result = $conn->query($query);
$num = $result->num_rows;
while ($arr = $result->fetch_assoc()) {
  $imgname = 'files/_striper/'.$arr['url'].'-'.$arr['time'].'.'.$arr['imgtype'];
//  print_r($arr);
	$newname = 'files/'.md5($arr['id'] . $arr['imgtype']).'.'.$arr['imgtype'];
	
	if (file_exists($imgname)) {
		rename($imgname, $newname);
	}
	
  echo $imgname . ' moved to ' . $newname . ' (' . ++$i . ' av ' . $num . ')<br>';
}

