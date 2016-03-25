<?php
// Nettserier.no Webcomics Portal
// classes.php: "Global" classes 

// ActionHooks: Class for storing of all actions to be run at certain hooks, allowing functions in plugins to be run at specific times in the script
class ActionHooks {
	
	// Define a two-dimensional array to hold all the hooks, so that $hooks['hook_name'] is an array holding all actions to be run at that hook
	public $hooks = array();
	
	// add_action(): Used by plugins to register new functions to be run at certain hooks. First parameter is the hook name, second parameter is the function to be run.
	public function add_action($hook, $action) {
		
		// If the hook isn't registered in $hooks yet...
		if (!isset($this->hooks[$hook])) {
			// ... add it!
			$this->hooks[$hook] = array();
		}
		
		// Add the action to the correct hook array.
		$this->hooks[$hook][] = $action;
	}
	
	// get_actions(): Used on hooks to run all actions for that hook. First parameter is the hook name.
	public function get_actions($hook) {
		
		// Run this only if the given hook has no actions set to it.
		if (isset($this->hooks[$hook])) {
			
			// How many actions are there for the current hook?
			$actioncount = count($this->hooks[$hook]);
			
			// For each action:
			for ($i = 0; $i < $actioncount; $i++) {
				// Get the action name
				$fn = $this->hooks[$hook][$i];
				// Check if the function exists...
				if (function_exists($fn)) {
					// ... and if it does, call it!
					call_user_func($fn);
				}
			}
		}
	}
}

class ArrayHandler {
	public $arr = array();

	public function add_line($subarr) {
		$this->arr[] = $subarr;
	}

	public function add_js($subarr) {
		$subarr['text'] = '<script src="'.$subarr['js'].'"></script>';
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

	private function sort_arr() {
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

?>