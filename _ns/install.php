<?php

// Install all MySQL tables (if they exist)

// ns_users
mysql_install_table('ns_users', ['username VARCHAR(100) NOT NULL', 'realname VARCHAR(100) NOT NULL', 'email VARCHAR(255) NOT NULL', 'regtime TIMESTAMP NULL', 'updtime TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'password VARCHAR(255)', 'level INT(3)', 'salt CHAR(128) NOT NULL', 'emailtoken CHAR(128) NOT NULL']);

// ns_login_attempts
mysql_install_table('ns_login_attempts', ['user_id INT(11) NOT NULL', 'time VARCHAR(30) NOT NULL']);

// ns_user_comic_rel
mysql_install_table('ns_user_comic_rel', ['user INT(10) NOT NULL', 'comic INT(10) NOT NULL', 'reltype VARCHAR(1) NOT NULL', 'time TIMESTAMP']);

// ns_plugins
mysql_install_table('ns_plugins', ['name VARCHAR(100) NOT NULL', 'folder VARCHAR(100) NOT NULL', 'level INT(3)', 'pos INT(5)']);

// ns_comics
mysql_install_table('ns_comics', ['url VARCHAR(100) NOT NULL', 'name VARCHAR(100) NOT NULL', 'regtime TIMESTAMP NULL', 'updtime TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP']);

// ns_updates
mysql_install_table('ns_updates', ['updtype VARCHAR(3) NOT NULL', 'comic INT(10) NOT NULL', 'user INT(10) NOT NULL', 'title VARCHAR(100) NOT NULL', 'text TEXT NOT NULL', 'imgtype VARCHAR(5) NOT NULL', 'ip VARCHAR(45) NOT NULL', 'published INT(1) NOT NULL', 'pubtime TIMESTAMP NULL', 'regtime TIMESTAMP NULL', 'updtime TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'visible INT(1) NOT NULL']);
?>