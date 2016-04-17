<?php

$cc = new ShowComic;
$cc->set_comic($comic_id);

$folder = strtok('/');

if ($folder) {
$cc->set_slug($folder);
}
$c .= $cc->show();

class ShowComic {

  function set_comic($id) {
    if (is_numeric($id)) {
      $this->comic = $id;
    }
  }
  
  function set_slug($slug) {
    $this->slug = $slug;
  }
  
  function show() {
    global $conn;
    if (isset($this->count) && is_numeric($this->count)) {
      $count = $this->count;
    }
    else {
      $count = 1;
    }

    if (isset($this->order)) {
      $order = $this->order;
    }
    else {
      $order = 'u.pubtime DESC, u.id DESC';
    }

    $c = '';
    $query = 'SELECT u.id, u.comic, u.imgtype, u.pubtime, u.title, u.text, u.slug ';
    $query .= 'FROM ns_updates AS u ';

    $query .= 'WHERE ';
    if ($this->comic && is_numeric($this->comic)) {
      $query .= 'u.comic = '.$this->comic.' AND ';
			if ($this->slug) {
				$query .= 'u.slug = \''.$conn->escape_string($this->slug).'\' AND ';
			}
    }
    $query .= 'u.pubtime <= NOW() AND u.published = 1 AND u.updtype IN (\'c\', \'i\') ORDER BY '.$order.' LIMIT '.$count;
    $result = $conn->query($query);
    $num = $result->num_rows;

    if ($num) {

      if ($this->comic) {
        $comic_url = comic_url($this->comic);
      }
    

      
      $nav = '';
      while ($r_arr = $result->fetch_assoc()) {
        if ($num == 1 && $this->comic) {

			$nav .= $this->nav_element(__('First comic'), $comic_url, $this->comic, $r_arr['slug'], false, 'first');
			$nav .= $this->nav_element(__('Previous comic'), $comic_url, $this->comic, $r_arr, true, 'prev');
			$nav .= $this->nav_element(__('Next comic'), $comic_url, $this->comic, $r_arr, false, 'next');
			$nav .= $this->nav_element(__('Latest comic'), $comic_url, $this->comic, $r_arr['slug'], true, 'last');
			if ($nav) {
				$nav = '<nav class="navigate-strips"><ul>'.$nav.'</ul></nav>';
			}
        }
        $c .= '<section class="comicbox">';

        if ($nav) {
          $c .= $nav;
        }
        
        //      $c .= '<h3>'.str_replace(array('{comic}', '{creator}'), array($comic_linked, htmlspecialchars($arr['comic_creator'])), __('{comic} by {creator}')).'</h3>';
        $c .= '<p class="comic-para"><img src="/_ns/files/'.md5($r_arr['id'] . $r_arr['imgtype']).'.'.$r_arr['imgtype'].'" alt=""></p>';
        if ($nav) {
          $c .= $nav;
        }

        if ($r_arr['title']) {
          $c .= '<h4>'.htmlspecialchars($r_arr['title']).'</h4>'."\n";
        }
        if ($r_arr['text']) {
          $c .= $r_arr['text'];
        }
        
				$c .= '</section>';
      }
    }
    return $c;
  }

	private function nav_element($label, $comic_url, $comic_id, $r, $desc, $rel) {
		global $conn;
		if ($desc) {
			$order = 'pubtime DESC, id DESC';
			$op = '<';
		}
		else {
			$order = 'pubtime, id';
			$op = '>';
		}

		$query = 'SELECT slug FROM ns_updates WHERE comic = '.$comic_id.' AND published = 1 AND updtype IN (\'c\', \'i\') AND pubtime <= NOW()';
		if (is_array($r)) {
			$query .= ' AND (pubtime '.$op.' \''.$r['pubtime'].'\' OR (pubtime = \''.$r['pubtime'].'\' AND id '.$op.' '.$r['id'].'))';
		}
		$query .= ' ORDER BY '.$order.' LIMIT 1';
		$result = $conn->query($query);
		if ($result->num_rows) {
			$arr = $result->fetch_assoc();
			if (is_array($r)) {
				$current_slug = $r['slug'];
			}
			else {
				$current_slug = $r;
			}
			if ($arr['slug'] != $current_slug) {
				$reltag = '';
				if (in_array($rel, ['prev', 'next']))
					$reltag = ' rel="'.$rel.'"';
				return '<li class="'.$rel.'"'.$reltag.'><a href="/'.$comic_url.'/comic/'.$arr['slug'].'/">'.$label.'</a></li>';
			}
		}
	}

}