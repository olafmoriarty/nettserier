<?php

$ns_title = _('Plugin manager');
$c .= '<h2>'._('Plugin manager').'</h2>'."\n";

// Get all plugins from database
$query = 'SELECT name, folder, level, pos FROM ns_plugins ORDER BY IF(pos = 0, 99999, pos), name';
$result = $conn->query($query);
if ($result !== false) {
	$num = $result->num_rows;
	if ($num) {
		$result->data_seek(0);
		
    // Open <table>
    $c .= '<table>'."\n";
    $c .= '<tr>'."\n";
    $c .= '<th>'._('Plugin name').'</th>';
    $c .= '<th>'._('Plugin folder').'</th>';
    $c .= '<th>'._('User level').'</th>';
    $c .= '<th>'._('Order').'</th>';
    $c .= '</tr>'."\n";
    
    // Array which holds all folders in database
    $folderarr = array();
    while ($arr = $result->fetch_assoc()) {
      $c .= '<tr>'."\n";
      
      // Plugin name
      $c .= '<td id="plugin-'.$arr['folder'].'-name">';
      if (isset($_GET['mode']) && $_GET['mode'] == 'edit') {
        $c .= '<form method="post">'."\n";
        $c .= '<input type="text" name="newvalue" value="'.htmlspecialchars($arr['name'], ENT_COMPAT, 'UTF-8').'" />'."\n";
        $c .= '<input type="hidden" name="folder" value="'.$arr['folder'].'" />'."\n";
        $c .= '<input type="hidden" name="field" value="name" />'."\n";
        $c .= '<input type="submit" value="'._('Update').'" />'."\n";
        $c .= '</form>';
      }
      else {
        $c .= '<a href="/n/admin/plugins/?mode=edit&amp;plugin='.$arr['folder'].'&amp;field=name">'.$arr['name'].'</a>';
      }
      $c .= '</td>';
      
      // Plugin folder
      $c .= '<td id="plugin-'.$arr['folder'].'-folder">'.NS_PATH.'plugins/'.$arr['folder'].'</td>';
      
      // Plugin level
      $c .= '<td id="plugin-'.$arr['folder'].'-level">'.$arr['level'].'</td>';
      
      // Plugin position
      $c .= '<td id="plugin-'.$arr['folder'].'-pos">'.$arr['pos'].'</td>';
      
      $c .= '</tr>'."\n";
      
      // Add folder to array
      $folderarr[] = $arr['folder'];
    }

    // Add plugins not listed in database yet
    $files = array_slice(scandir(NS_PATH.'plugins'), 2);
    foreach($files AS $file) {
      if (!in_array($file, $folderarr) && is_dir(NS_PATH.'plugins/'.$file) && file_exists(NS_PATH.'plugins/'.$file.'/config.php')) {
        // This is a plugin that exists and has a config file, but that's not listed in the database. So add a row to the table ...
        
        $c .= '<tr>'."\n";

        // Plugin name (let's use a modified version of the folder name as default)
        $plugin_name = ucwords(str_replace(['_', '-'], ' ', $file));
        $c .= '<td id="plugin-'.$file.'-name">'.$plugin_name.'</td>';

        // Plugin folder
        $c .= '<td id="plugin-'.$file.'-folder">'.$file.'</td>';

        // Plugin level
        $c .= '<td id="plugin-'.$file.'-level">'.'100'.'</td>';

        // Plugin position
        $c .= '<td id="plugin-'.$file.'-pos"></td>';

        $c .= '</tr>'."\n";
        
        // ... and also add the row to the database.
        $query = 'INSERT INTO ns_plugins (name, folder, level, pos) VALUES (\''.($conn->escape_string($plugin_name)).'\', \''.($conn->escape_string($file)).'\', 100, 0)';
        $conn->query($query);
        
      }
    }    
    
    // Close <table>
    $c .= '</table>'."\n";
  }
}

?>