<?php

class ShowComic {

	protected $comic = 0;
	protected $show_comic_title = false;
	protected $count = 1;
	protected $link_to_strip = false;
	protected $order = 'u.pubtime DESC, u.id DESC';
	protected $result_type = 'update';
	protected $slug = '';
	protected $updtype = array('c', 'i');
	protected $show_text = true;
	protected $mintime = 0;
	protected $maxtime = 0;
	protected $is_the_page = false;

	function __construct() {
		$this->maxtime = time();
	}
	
	function is_page($bin) {
		$this->is_the_page = $bin;
	}
  function set_comic($id) {
    if (is_numeric($id)) {
      $this->comic = $id;
    }
  }
  
  function set_comic_title($bin) {
    $this->show_comic_title = $bin;
  }
  
  function set_count($n) {
    $this->count = $n;
  }
  
	function set_linking($bin) {
		$this->link_to_strip = $bin;
	}
  function set_order($order) {
    $this->order = $order;
  }

	function set_result_type($type) {
		$this->result_type = $type;
	}
	
  function set_slug($slug) {
    $this->slug = $slug;
  }
	
	function set_text($bin) {
		$this->show_text = $bin;
	}

	function set_min_time($time) {
		$this->mintime = $time;
	}

  	function set_max_time($time) {
		$this->maxtime = $time;
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

		// If we want to show only one update for each comic
		if ($this->result_type == 'comic') {
			$query = 'SELECT u.id, u.updtype, u.comic, u.imgtype, u.pubtime, u.title, u.text, u.slug, c.name AS comic_name FROM (SELECT MAX(mi.id) AS id FROM (SELECT comic, MAX(pubtime) AS pubtime FROM ns_updates WHERE published = 1 AND updtype IN (\'c\', \'i\') AND ';

			if ($this->mintime) {
				$query .= ' pubtime >= FROM_UNIXTIME('.$this->mintime.') AND ';
			}
			if ($this->maxtime != time()) {
				$query .= ' pubtime <= FROM_UNIXTIME('.$this->maxtime.') AND ';
			}

			$query .= 'pubtime <= NOW() GROUP BY comic) AS mp LEFT JOIN ns_updates AS mi ON mp.comic = mi.comic AND mp.pubtime = mi.pubtime GROUP BY mp.comic, mp.pubtime) AS mpi LEFT JOIN ns_updates AS u ON mpi.id = u.id ';
		}
		else {
			// If we want to show all updates
			$query = 'SELECT u.id, u.updtype, u.comic, u.imgtype, u.pubtime, u.title, u.text, u.slug, c.name AS comic_name FROM ns_updates AS u ';
		}
		
		$query .= 'LEFT JOIN ns_comics AS c ON u.comic = c.id ';

    $query .= 'WHERE ';
    if ($this->comic && is_numeric($this->comic)) {
      $query .= 'u.comic = '.$this->comic.' AND ';
			if ($this->slug) {
				$query .= 'u.slug = \''.$conn->escape_string($this->slug).'\' AND ';
			}
    }
	if ($this->mintime) {
		$query .= 'u.pubtime >= FROM_UNIXTIME('.$this->mintime.') AND ';
	}
	if ($this->maxtime != time()) {
		$query .= 'u.pubtime <= FROM_UNIXTIME('.$this->maxtime.') AND ';
	}
	$query .= 'u.pubtime <= NOW() AND u.published = 1 AND u.updtype IN (\'c\', \'i\') ORDER BY '.$order.' LIMIT '.$count;

//	echo $query;
//	exit;

		$result = $conn->query($query);
    $num = $result->num_rows;

    if ($num) {

			while ($r_arr = $result->fetch_assoc()) {
				$c .= $this->show_comic($r_arr, $num);
			}
		}
		return $c;
	}

	public function show_comic($arr, $num = 1) {
		global $action;
		$comic_url = comic_url($arr['comic']);
		$nav = '';
		if ($num == 1 && $this->comic) {

			$nav .= $this->nav_element(__('First comic'), $comic_url, $this->comic, $arr['slug'], false, 'first');
			$nav .= $this->nav_element(__('Previous comic'), $comic_url, $this->comic, $arr, true, 'prev');
			$nav .= $this->nav_element(__('Next comic'), $comic_url, $this->comic, $arr, false, 'next');
			$nav .= $this->nav_element(__('Latest comic'), $comic_url, $this->comic, $arr['slug'], true, 'last');
			if ($nav) {
				$nav = '<nav class="navigate-strips"><ul>'.$nav.'</ul></nav>';
			}
		}
		$c = '<section class="comicbox">';

		if ($nav) {
			$c .= $nav;
		}
			
		if ($this->show_comic_title) {
			$c .= '<h3>';
			if ($this->link_to_strip) {
				$c .= '<a href="/'.$comic_url.'/comic/'.$arr['slug'].'/">';
			}
			$c .= htmlspecialchars($arr['comic_name']);
			if ($this->link_to_strip) {
				$c .= '</a>';
			}
			$c .= '</h3>';
		}
		$c .= '<p class="comic-para">';
		if ($this->link_to_strip) {
			$c .= '<a href="/'.$comic_url.'/comic/'.$arr['slug'].'/">';
		}
		$c .= '<img src="/_ns/files/'.md5($arr['id'] . $arr['imgtype']).'.'.$arr['imgtype'].'" alt="">';
		if ($this->link_to_strip) {
			$c .= '</a>';
		}
		$c .= '</p>';
		if ($nav) {
			$c .= $nav;
		}

		if ($this->show_text) {
			$c .= '<div class="comic-text">'."\n";
			if ($arr['title']) {
				$c .= '<h4>'.htmlspecialchars($arr['title']).'</h4>'."\n";
			}
			if ($arr['text']) {
				$c .= $arr['text'];
			}
			
			$c .= '<p class="comic-pubtime">'.str_replace('{time}', $arr['pubtime'], __('Published {time}')).'</p>';
			$c .= $action['showcomic_text_after']->run($arr);
			$c .= '</div>'."\n";
		}
		$c .= '</section>'."\n";

		if ($this->is_the_page) {
			$c .= $action['showcomic_on_page_after']->run($arr);
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