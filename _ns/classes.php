<?php
// Nettserier.no Webcomics Portal
// classes.php: "Global" classes 

class ArrayHandler {
	public $arr = array();

	public function add_line($subarr) {
		$this->arr[] = $subarr;
	}

	public function add_js($subarr) {
		$subarr['text'] = '<script src="'.$subarr['js'].'"></script>';
		$this->arr[] = $subarr;
	}

	public function add_css($subarr) {
		$subarr['text'] = '<link rel="stylesheet" href="'.$subarr['css'].'">';
		$this->arr[] = $subarr;
	}
	
	public function return_arr() {
		return $this->arr;
	}

	public function return_text($mode = 'text') {
		$n = count($this->arr);
		$text = '';
		$arr = array();

		for ($i = 0; $i < $n; $i++) {
			if ($i) {
				$text .= "\n";
			}
			$text .= $this->arr[$i]['text'];
			$arr[] = $this->arr[$i]['text'];
		}
		if ($mode == 'array') {
			return $arr;
		}
		else {
			return $text;
		}
	}

	public function find($key, $value, $r = null) {
		foreach ($this->arr as $subarr) {
			if ($subarr[$key] == $value) {
				if ($r) {
					return $subarr[$r];
				}
				else {
					return $subarr;
				}
			}
		}
		return null;
	}
	
	public function return_ul($class = '') {
		$ret = '<ul';
		if ($class) {
			$ret .= ' class="'.$class.'"';
		}
		$ret .= '>';
		$this->sort_arr();
		foreach ($this->arr as $subarr) {
			if (isset($subarr['text']) && isset($subarr['link'])) {
				$ret .= '<li><a href="'.$subarr['link'].'">'.$subarr['text'].'</a></li>';
			}
			elseif (isset($subarr['text'])) {
				$ret .= '<li>'.$subarr['text'].'</li>';
			}
		}
		$ret .= '</ul>';
		return $ret;
	}

	protected function sort_arr() {
		$sorting_arr = $this->arr;
		if (is_array($sorting_arr) && count($sorting_arr)) {
		foreach ($sorting_arr as $key => $row) {
			if (isset($row['order'])) {
				$order[$key] = $row['order'];
			}
			else {
				$order[$key] = 0;
			}
		}
		array_multisort($order, $sorting_arr);
		$this->arr = $sorting_arr;
		}
	}
}

class ActionHook extends ArrayHandler {
	public $type;

	public function __construct($type = false) {
		if ($type) {
			$this->set_type($type);
		}
	}

	public function set_type($type) {
		$this->type = $type;
	}

	public function run($arguments = false) {
		$this->sort_arr();

		$num = count($this->arr);

		if ($num) {
			$input = $arguments;
			$returnstring = '';
			foreach($this->arr as $subarr) {
				$func = $subarr['function'];
				if ($arguments === false) {
					// No arguments, so just run call_user_func
					$output = call_user_func($func);
				}
				else {
					// Only one argument, use call_user_func
					$output = call_user_func($func, $input);
				}

				// Figure out what to do in the next step:
				if ($this->type == 'filter') {
					$input = $output;
					$returnstring = $output;
				}
				else {
					if ($returnstring !== false) {
						$returnstring .= $output;
					}
				}
			}

			return $returnstring;
		}
		else {
			// No functions. If type = filter, return arguments. Otherwise, return nothing.
			if ($this->type == 'filter') {
				return $arguments;
			}
			else {
				return false;
			}
		}
		
	}
}

?>