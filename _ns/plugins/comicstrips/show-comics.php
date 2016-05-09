<?php

$cc = new ShowComic;
$cc->set_comic($comic_id);
$cc->is_page(true);

$folder = strtok('/');

if ($folder) {
$cc->set_slug($folder);
}
$c .= $cc->show();

