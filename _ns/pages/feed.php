<?php

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

// type. What kind of update is this? A comic strip? A blog post? An album? A comment?
// id. In its local database, what's the id of this content? Note that this may not necessarily be unique, as the same number can occur in different tables (but the combination of type and id should be).
// comic_url. What's the comic's URL?
// comic_name. What's the comic's name?
// comic_creator. Who's the comic's creator(s)?
// pubtime. What's the timestamp for this update?
// title. What's the title, if any?
// text. What's the text, if any?
// slug. What's the slug, if any?
// user. Who's the user who made the update (or other user that is relevant for this update)
// user_name. What's the username of user?
// other. Any other values it could be useful to store - something that values grossly from type to type (image type, parent id, price...)

$query = 'SELECT t1.type, t1.id, c.url AS comic_url, c.name AS comic_name, GROUP_CONCAT(IF(cr.realname = \'\', cr.username, cr.realname) SEPARATOR \', \') AS comic_creator, t1.pubtime, t1.title, t1.text, t1.user, usr.username, t1.other FROM (';


$tables = array();

// Comic strips:
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
  $tables[] = $select_updates;
}

$tables[] = 'SELECT \'comic_created\' AS type, NULL AS id, comcre.id AS comic, comcre.regtime AS pubtime, NULL as title, NULL as text, NULL as slug, NULL as user, NULL as other FROM ns_comics AS comcre LEFT JOIN ns_user_comic_rel AS comcrerel ON comcre.id = comcrerel.comic WHERE comcrerel.reltype IN (\'c\', \'e\') AND comcrerel.user = {user_id}';

$query .= str_replace('{user_id}', $user_info['id'], '('.implode(') UNION (', $tables).')');

$query .= ') AS t1 LEFT JOIN ns_comics AS c ON t1.comic = c.id LEFT JOIN ns_user_comic_rel AS r ON t1.comic = r.comic LEFT JOIN ns_users AS cr ON r.user = cr.id LEFT JOIN ns_users AS usr ON t1.user = usr.id WHERE r.reltype = \'c\' GROUP BY t1.id ORDER BY t1.pubtime DESC';
$result = $conn->query($query);

$c .= $conn->error.'<br>';

$num = $result->num_rows;
if ($num) {
  while ($arr = $result->fetch_assoc()) {
    $func = $feed_functions->find('type', $arr['type'], 'func');
    if (function_exists($func)) {
      $c .= '<section class="feed-box">';
      $c .= call_user_func($func, $arr);
      $c .= '<p class="feed-pubtime">'.$arr['pubtime'].'</p>';
      $c .= '</section>';
    }
  }
}
