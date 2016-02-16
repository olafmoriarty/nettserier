<?php

// Install all MySQL tables (if they exist)

// ns_users
mysql_install_table('ns_users', ['username VARCHAR(100) NOT NULL', 'realname VARCHAR(100) NOT NULL', 'email VARCHAR(255) NOT NULL', 'regtime TIMESTAMP NULL', 'updtime TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'password VARCHAR(255)', 'level INT(3)']);

// ns_user_comic_rel
mysql_install_table('ns_user_comic_rel', ['user INT(10) NOT NULL', 'comic VARCHAR(100) NOT NULL', 'reltype VARCHAR(1) NOT NULL', 'time TIMESTAMP']);

// ns_plugins
mysql_install_table('ns_plugins', ['name VARCHAR(100) NOT NULL', 'folder VARCHAR(100) NOT NULL', 'level INT(3)', 'pos INT(5)']);

// ns_comics
mysql_install_table('ns_comics', ['url VARCHAR(100) NOT NULL', 'name VARCHAR(100) NOT NULL', 'regtime TIMESTAMP NULL', 'updtime TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP']);
?>