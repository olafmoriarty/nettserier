<?php

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


$tables = $feed_queries->return_text('array');
$query .= str_replace('{user_id}', $user_info['id'], '('.implode(') UNION (', $tables).')');

$query .= ') AS t1 LEFT JOIN ns_comics AS c ON t1.comic = c.id LEFT JOIN ns_user_comic_rel AS r ON t1.comic = r.comic LEFT JOIN ns_users AS cr ON r.user = cr.id LEFT JOIN ns_users AS usr ON t1.user = usr.id WHERE r.reltype = \'c\' GROUP BY t1.id ORDER BY t1.pubtime DESC';
$result = $conn->query($query);

$num = $result->num_rows;
$c .= '<section class="feed">'."\n";
$c .= '<h2>'.__('User feed').'</h2>';
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
else {
  $c .= '<p>'.__('This is your user feed. This is where updates that are relevant for you will be shown. But currently, it\'s completely empty. That\'s a little sad...').'</p>';
  $c .= '<p>'.__('To start getting interesting content here, you could:').'</p>';
  $c .= '<ul>';
  $c .= '<li><a href="/n/browse/">'.__('Follow more comics').'</a></li>';
  $c .= '<li><a href="#feed-settings">'.__('Change your feed settings to allow more updates through').'</a></li>';
  $c .= '</ul>';
}
$c .= '</section>'."\n";

$c .= '<section class="feed-sidebar">'."\n";

$c .= '<h3 class="expand">'.__('Recommended comics').'</h3>';
$c .= '<p>Where a not-yet-made plugin will give you a list of recommended comics based on your taste</p>';
$c .= '<p>This is lorem ipsum text</p>';

$c .= '<h3 class="expand" id="feed-settings">'.__('Feed settings').'</h3>';
$c .= '<p>'.__('Use the checkboxes below to select what kinds of updates you want to see in your feed.').'</p>';
$c .= '<h4>'.__('Comic strips').'</h4>';
$c .= '<p><input name="comics-mine" type="checkbox" checked> Comics I\'ve created</p>';
$c .= '<p><input name="comics-i-follow" type="checkbox" checked> Comics I follow</p>';
$c .= '<p><input name="comics-other" type="checkbox"> All other comics</p>';
$c .= '<h4>'.__('Blog posts').'</h4>';
$c .= '<p><input name="blogs-mine" type="checkbox" checked> Comics I\'ve created</p>';
$c .= '<p><input name="blogs-i-follow" type="checkbox" checked> Comics I follow</p>';
$c .= '<p><input name="blogs-other" type="checkbox"> All other comics</p>';
$c .= '<h4>'.__('Albums').'</h4>';
$c .= '<p><input type="checkbox" checked> Comics I\'ve created</p>';
$c .= '<p><input type="checkbox" checked> Comics I follow</p>';
$c .= '<p><input type="checkbox"> All other comics</p>';
$c .= '<h4>'.__('Comments').'</h4>';
$c .= '<p><input type="checkbox" checked> Comics I\'ve created</p>';
$c .= '<p><input type="checkbox" checked> Comics I follow</p>';
$c .= '<p><input type="checkbox"> All other comics</p>';
$c .= '<h4>'.__('Other updates').'</h4>';
$c .= '<p><input type="checkbox" checked> Daily compilations</p>';

$c .= '</section>';