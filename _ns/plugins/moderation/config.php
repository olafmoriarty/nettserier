<?php

	// Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

	// Deactivate comment field for 30 seconds after last comment

	function comment_cooldown_field($text) {
		if ($cc = comment_cooldown()) {
			return $cc;
		}
		else {
			return $text;
		}
	}
	$filter['comment_field']->add_line(['function' => 'too_many_comments_field']);
	$action['check_comment']->add_line(['function' => 'too_many_comments']);

	function comment_cooldown() {
		global $conn, $logged_in, $user_info;
		
		if ($logged_in && isset($user_info['id'])) {
			// Has the user written any comments the past 30 seconds?
			$sec = 30;
			$query = 'SELECT COUNT(id) AS num FROM ns_comments WHERE regtime >= DATE_ADD(CURDATE(), INTERVAL -'.$sec.' SECOND)';
			$result = $conn->query($query);
			$r_arr = $result->fetch_assoc();
			$num = $r_arr['num'];
			if ($num > 0) {
				return str_replace('{n}', $sec, _('You can\'t write new comments as long as it\'s less than {n} seconds since your last comment. Refresh the page in a few seconds to try again. Sorry for the inconvenience if you\'re a human, we just really hate spambots.')).' ';
			}
		}
		return false;
	}

	// Deactivate comment field for unverified users 

	function too_many_comments_field($text) {
		if ($tmc = too_many_comments()) {
			return $tmc;
		}
		else {
			return $text;
		}
	}
	$filter['comment_field']->add_line(['function' => 'too_many_comments_field']);
	$action['check_comment']->add_line(['function' => 'too_many_comments']);

	function too_many_comments() {
		global $conn, $logged_in, $user_info;
		
		if ($logged_in && isset($user_info['id']) && $user_info['level'] < 10) {
			// How many comments have the user written in the past day?
			$query = 'SELECT COUNT(id) AS num FROM ns_comments WHERE regtime >= DATE_ADD(CURDATE(), INTERVAL -1 DAY)';
			$result = $conn->query($query);
			$r_arr = $result->fetch_assoc();
			$num = $r_arr['num'];
			$limit = 10;
			if ($num >= $limit) {
				return str_replace('{n}', $limit, _('You have reached your daily comment quota. New and unverified users are limited to posting {n} comments per day. Sorry for the inconvenience, but this limitation will be lifted as soon as a moderator has looked over a few of your comments to verify that you\'re not a spambot.')).' ';
			}
		}
		return false;
	}
