<?php

// ===================================================================

function __($str) {
	return $str;
} // __()


// ===================================================================

function mysql_install_table($tabname, $cols) {
	// Get MySQL connection
	global $conn;

	// Check if table exists.
	$result = $conn->query('SHOW TABLES LIKE \''.$tabname.'\'');
	$tab_exists = $result->num_rows > 0;
	
	// Create table
	if (!$tab_exists) {
		$query = 'CREATE TABLE '.$tabname.' (id INT(10) AUTO_INCREMENT PRIMARY KEY, '.implode(', ', $cols).') CHARSET utf8 COLLATE utf8_danish_ci';
		$result = $conn->query($query);
	}

	// Table exists, so make sure it's not missing any columns
	else {
		foreach ($cols as $col) {
			// First word of string is column name
			$colname = strtok($col, ' ');

			// Does column exist?
			$result = $conn->query('SHOW COLUMNS FROM '.$tabname.' LIKE \''.$colname.'\'');
			$col_exists = $result->num_rows > 0;

			// If not, add it
			if (!$col_exists) {
				$query = 'ALTER TABLE '.$tabname.' ADD COLUMN '.$col;
				$result = $conn->query($query);
			}

		}
	}
} // mysql_install_table()

// ===================================================================

function validate_input($arr) {
	global $conn, $_POST;
	if (isset($arr['error'])) {
		$error = $arr['error'];
	}
	else {
		$error = __('Something went wrong.');
	}
	if ($arr['check'] == 'unique') {
		// Check if value is already in use in the specified field
		$query = 'SELECT id FROM '.$arr['table'].' WHERE LOWER('.$arr['field'].') = \''.$conn->real_escape_string(strtolower($_POST[$arr['input']])).'\'';
		$result = $conn->query($query);
		$num = $result->num_rows;
		if ($num) {
			return $error;
		}
		else {
			return false;
		}
	}

	if ($arr['check'] == 'empty') {
		// returns error if field is empty
		if (!$_POST[$arr['input']]) {
			return $error;
		}
		else {
			return false;
		}
	}
	
	if ($arr['check'] == 'matching') {
		// Check if two input fields hold the same value. Neat for "Confirm password".
		if ($_POST[$arr['input']] == $_POST[$arr['input2']]) {
			return false;
		}
		else {
			return $error;
		}
	}
	
	if ($arr['check'] == 'email') {
		// Returns TRUE if field does not contain an e-mail address
		if (!filter_var($_POST[$arr['input']], FILTER_VALIDATE_EMAIL)) {
			return $error;
		}
		else {
			return false;
		}
	}
}

// ----------

function mysql_string($text) {
	global $conn;
	return '\''.($conn->escape_string($text)).'\'';
}

// ----------

function input_field($arr) {
  global $error_array;
  $c = '<div id="'.$arr['name'].'_box"';
  if (isset($error_array[$arr['name']]) && $error_array[$arr['name']]) {
		$c .= ' class="errorbox"';
	}
	$c .= '>';
    $c .= '<p>'.$arr['text'].'<br>'."\n";

	if (isset($arr['text_before_field'])) {
		$c .= $arr['text_before_field'];
	}

	$c .= '<input type="';
    if (isset($arr['type'])) {
      $c .= $arr['type'];
    }
  else {
    $c .= 'text';
  }
    $c .= '" name="'.$arr['name'].'" id="';
    if (isset($arr['id'])) {
      $c .= $arr['id'];
    }
  else {
    $c .= $arr['name'];
  }
    $c .= '"';
	if (isset($_POST[$arr['name']])) {
		$c .= ' value="'.htmlspecialchars($_POST[$arr['name']]).'"';
	}
	if (isset($arr['class'])) {
		$c .= ' class="'.htmlspecialchars($arr['class']).'"';
	}
	$c .= '>';
	if (isset($arr['text_after_field'])) {
		$c .= $arr['text_after_field'];
	}
	$c .= '</p>'."\n";
    if (isset($error_array[$arr['name']]) && $error_array[$arr['name']]) {
      $c .= '<p class="errormsg">'.$error_array[$arr['name']].'</p>';
    }
    $c .= '</div>'."\n";
  return $c;
}








// ----------
// Secure login: Functions stolen from / heavily inspired by https://github.com/peredurabefrog/phpSecureLogin/

function sec_session_start() {
    $session_name = 'sec_session_id';   // Set a custom session name 
    $secure = SECURE;
    // This stops JavaScript being able to access the session id.
    $httponly = true;
    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        exit();
    }
    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
    // Sets the session name to the one set above.
    session_name($session_name);
    session_start();            // Start the PHP session 
    session_regenerate_id();    // regenerated the session, delete the old one. 
}

function login($email, $password) {
	global $conn;
    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $conn->prepare('SELECT id, username, password, salt FROM ns_users WHERE email = ? LIMIT 1')) {
        $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();
        // get variables from result.
        $stmt->bind_result($user_id, $username, $db_password, $salt);
        $stmt->fetch();
        // hash the password with the unique salt.
        $password = hash('sha512', $password . $salt);
        if ($stmt->num_rows == 1) {
            // If the user exists we check if the account is locked
            // from too many login attempts 
            if (checkbrute($user_id) == true) {
                // Account is locked 
                // Send an email to user saying their account is locked 
                return false;
            } else {
                // Check if the password in the database matches 
                // the password the user submitted.
                if ($db_password == $password) {
                    // Password is correct!
                    // Get the user-agent string of the user.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS protection as we might print this value
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;
                    // XSS protection as we might print this value
                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username);
                    $_SESSION['username'] = $username;
                    $_SESSION['login_string'] = hash('sha512', $password . $user_browser);
									// Login successful. 
                    return true;
                } else {
                    // Password is not correct 
                    // We record this attempt in the database 
                    $now = time();
                    if (!$conn->query("INSERT INTO ns_login_attempts(user_id, time) VALUES ('$user_id', '$now')")) {
                        exit();
                    }
                    return false;
                }
            }
        } else {
            // No user exists. 
            return false;
        }
    } else {
        // Could not create a prepared statement
        exit();
    }
}

function checkbrute($user_id) {
	global $conn;
	// Get timestamp of current time 
    $now = time();
    // All login attempts are counted from the past 2 hours. 
    $valid_attempts = $now - (2 * 60 * 60);
    if ($stmt = $conn->prepare("SELECT time FROM ns_login_attempts WHERE user_id = ? AND time > '$valid_attempts'")) {
        $stmt->bind_param('i', $user_id);
        // Execute the prepared query. 
        $stmt->execute();
        $stmt->store_result();
        // If there have been more than 5 failed logins 
        if ($stmt->num_rows > 5) {
            return true;
        } else {
            return false;
        }
    } else {
        // Could not create a prepared statement
        exit();
    }
}

function login_check() {
	global $conn;
	// Check if all session variables are set 
    if (isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])) {
        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];
        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
        if ($stmt = $conn->prepare("SELECT password FROM ns_users WHERE id = ? LIMIT 1")) {
            // Bind "$user_id" to parameter. 
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   // Execute the prepared query.
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                // If the user exists get variables from result.
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);
                if ($login_check == $login_string) {
                    // Logged In!!!! 
                    return true;
                } else {
                    // Not logged in 
                    return false;
                }
            } else {
                // Not logged in 
                return false;
            }
        } else {
            // Could not prepare statement
            exit();
        }
    } else {
        // Not logged in 
        return false;
    }
}


function esc_url($url) {
    if ('' == $url) {
        return $url;
    }
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
    
    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;
    
    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }
    
    $url = str_replace(';//', '://', $url);
    $url = htmlentities($url);
    
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);
    if ($url[0] !== '/') {
        // We're only interested in relative links from $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}