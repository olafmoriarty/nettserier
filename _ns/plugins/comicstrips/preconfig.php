<?php

// Edit comic menu
$edit_comic_single_menu = new ArrayHandler;

// HOOKS

// Runs when a comic strip is submitted
$action['edit_strips_submit'] = new ActionHook();

// Runs after printing a comic
$action['showcomic_text_after'] = new ActionHook();

// Runs below comic box, but only on the comic page, not on e.g. the daily page
$action['showcomic_on_page_after'] = new ActionHook();

