<?php
// Nettserier.no Webcomics Portal
// classes.php: "Global" classes 

// -----------------------------------------------------------------------------
// ArrayHandler
// Every instance of this class is basically an array of arrays. Class functions
// are used for enhanced array editing/reading.
// -----------------------------------------------------------------------------

class ArrayHandler {
	// Define $arr.
	public $arr = array();

	// add_line($subarr): Used to add information to the array.
	// In most cases this information will be an array itself, for example, to
	// add a new point to a menu, you would use
	// $a->add_line(['text' => 'Text', 'link' => '/some/url/here/']); .
	public function add_line($subarr) {
		$this->arr[] = $subarr;
	}

	// add_js($subarr): Same as add_line(), but the script automatically adds a
	// 'text' value to the subarray, which takes the 'js' value of the array and
	// wraps it in a <script> tag so that the function can be used to set up
	// a list of javascript files.
	public function add_js($subarr) {
		$subarr['text'] = '<script src="'.$subarr['js'].'"></script>';
		$this->arr[] = $subarr;
	}

	// add_css($subarr): Same as add_line(), but the script automatically adds a
	// 'text' value to the subarray, which takes the 'css' value of the array
	// and wraps it in a <link> tag so that the function can be used to set up
	// a list of stylesheets.
	public function add_css($subarr) {
		$subarr['text'] = '<link rel="stylesheet" href="'.$subarr['css'].'">';
		$this->arr[] = $subarr;
	}
	
	// return_arr(): Returns the array.
	public function return_arr() {
		return $this->arr;
	}

	// return_text(): Returns the text in the array, specifically the subarrays'
	// "text" column. return_text('array') returns an array, otherwise it is
	// returned as plain text separated by newlines.
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

	// find($key, $value): Returns the row of the array where a certain key has
	// a certain value. Add third argument to return only a specific column.
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
	
	// return_ul(): Sorts the array and then returns an unordered list. Uses the
	// subarray keys "text" and "link".
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

	// sort_arr(): Sorts the array based on the "order" value of the subarray.
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
} // class ArrayHandler

// -----------------------------------------------------------------------------
	// ActionHook (extends ArrayHandler)
	// Stores a list of functions to be run at a given point in time.
	// There are two types of ActionHook: "hooks" and "filters". A hook simply
	// runs all functions in the list and returns the eventual output. A filter
	// also runs all functions, but instead of outputting the result, that
	// result is used as an argument in the NEXT function, and so on.
// -----------------------------------------------------------------------------

class ActionHook extends ArrayHandler {
	public $type;

	// When creating the instance, use ActionHook('filter') for filter or
	// anything else for hooks.
	public function __construct($type = false) {
		if ($type) {
			$this->set_type($type);
		}
	}

	// set_type($type): Can be "filter" or "hook" (anything else will default to
	// hook)
	public function set_type($type) {
		$this->type = $type;
	}

	// run($arguments): Runs the functions in the list.
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
} // class ActionHook
