<?php

// The comment field is wrapped in this filter so that it can be removed if a function requires it
$filter['comment_field'] = new ActionHook('filter');

$action['check_comment'] = new ActionHook();