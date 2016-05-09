<?php

	function show_comments($arr) {
		global $conn, $logged_in;
		$c = '<section class="comment_section">';
		$query = 'SELECT text FROM ns_comments WHERE parent = '.$arr['id'];
		$result = $conn->query($query);
		$num = $result->num_rows;
		$c .= '<h4>'.str_replace('{n}', $num, __('{n} comments')).'</h4>';
		
		if ($num) {
			
		}
			$c .= '<h4>'.__('Join the discussion!').'</h4>'."\n";
		if ($logged_in) {

			if (!$submitted || $errors) {
				$c .= '<form method="post" name="comment_form" action="/'.comic_url($arr['comic']).'/comic/'.$arr['slug'].'/">'."\n";
				$c .= input_field(['name' => 'comment_text', 'text' => __('Your comment:'), 'type' => 'textarea']);
				$c .= '<p><input type="submit" name="add_comment_submit" id="add_comment_submit" value="'.__('Add your comment!').'"></p>';
				$c .= '</form>'."\n";

			}
			
			
		}
		else {
			$c .= '<p><a href="/n/log-in/?returnurl='.urlencode('/'.comic_url($arr['comic']).'/comic/'.$arr['slug'].'/').'">'.__('Log in to add your comment!').'</a></p>'."\n";
		}
		$c .= '</section>'."\n";
		return $c;
	}