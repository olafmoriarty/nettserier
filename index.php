<?php

// ---------------------------------------------------------------------------
// N E T T S E R I E R . N O
// developed by Olaf Moriarty Solstrand for Comicopia AS, © 2006-2016
// ---------------------------------------------------------------------------

// ---------------------------------------------------------------------------
// SET NAME OF SCRIPT FOLDER
// ---------------------------------------------------------------------------

// All other settings are done in config.php, so the only setting we have to
// do in this file is tell the script which folder config.php is located in.
// (All other files are located there, too, so if we need to change that we
// can just do the change in this file.

define('NS_PATH', '_ns/');

// ---------------------------------------------------------------------------
// LOAD SETTINGS
// ---------------------------------------------------------------------------

// Include the settings file.

include(NS_PATH.'config.php');

// ---------------------------------------------------------------------------
// MAKE AND LOAD CONTENT
// ---------------------------------------------------------------------------

// Include the content file.

include(NS_PATH.'content.php');

// ---------------------------------------------------------------------------
// PRODUCE OUTPUT
// ---------------------------------------------------------------------------

// Put everything together and show it to the user.

include(NS_PATH.'output.php');
?>