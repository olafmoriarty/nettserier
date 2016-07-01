<?php

$folder = strtok('/');

if ($folder == 'about') {
	$c .= '<h2>'._('About us').'</h2>';
	$c .= '<div class="about-us">';
	$c .= '<p>'.str_replace(['{page}', '{company}'], [PAGE_TITLE, '<a href="'.'http://comicopia.no'.'" target="_blank">'.'Comicopia AS'.'</a>'], _('{page} is a Norwegian webcomics portal run and owned by {company}.')).'</p>';

	$info['email'] = 'post@nettserier.no';
	$info['snailmail'] = 'Comicopia AS, Postboks 40, 1401 Ski, Norway';

	$c .= '<h3 class="expand">'._('Contact us').'</h3>';
	$c .= '<h4>'._('E-mail:').'</h4><p><a href="mailto:'.$info['email'].'">'.$info['email'].'</a></p>';
	$c .= '<h4>'._('Snail mail:').'</h4><p>'.$info['snailmail'].'</p>';

	$c .= '<h3 class="expand">'._('Who are we?').'</h3>';
	$c .= '<h4>'._('Managing director:').'</h4>'.'<p><a href="mailto:'.'olaf.moriarty.solstrand@comicopia.no'.'">'.'Olaf Moriarty Solstrand'.'</a></p>';
	$c .= '<h4>'._('Board members:').'</h4>'.'<ul><li>'.implode('</li><li>', ['Arne Bye', 'Rigmor Hanken', 'Bård Lilleøien', 'Olaf Moriarty Solstrand', 'Eirik Andreas Vik']).'</li></ul>';

	$c .= '<h3 class="expand">'.str_replace('{page}', PAGE_TITLE, _('Open source code used for {page}')).'</h3>';

	$os_arr = $open_source->return_arr();

	$osnum = count($os_arr);
	for ($i = 0; $i < $osnum; $i++) {
		$c .= '<h4><a href="'.$os_arr[$i]['link'].'">'.htmlspecialchars($os_arr[$i]['name']).'</a></h4>';
		if (isset($os_arr[$i]['license'])) {
			$c .= '<p>'.str_replace(['{name}', '{license}'], [htmlspecialchars($os_arr[$i]['developer']), htmlspecialchars($os_arr[$i]['license'])], _('Developed by {name} (released under {license})')).'</p>';
		}
		else {
			$c .= '<p>'.str_replace('{name}', htmlspecialchars($os_arr[$i]['developer']), _('Developed by {name}')).'</p>';
		}
	}

	// Having this hard-coded is STUPID. Must create a database over this. But hey, no rush, other things are more urgent ...
	$c .= '<h3 class="expand">'._('Our sponsors').'</h3>'."\n".'<p>'.str_replace(['{page}', '{service}'], [PAGE_TITLE, '<a href="http://patreon.com/nettserier">Patreon.com</a>'], _('This is a list of the amazing people who are currently supporting {page} on {service} with $10/month or more. They\'re really incredible, and it would have been a lot more difficult to run {page} without them. THANK YOU!')).'</p>'."\n".'<ul><li>';
	$c .= implode('</li><li>', ['Kristin Arnesen', '<a href="http://dadaph.no" target="_blank" rel="nofollow">Bård Lilleøien</a>', 'Øystein Skartsæterhagen', 'Nils Petter Smeby', 'Eirik Andreas Vik']);
	$c .= '</li></ul>';

	$c .= '</div>';
}
elseif ($folder == 'cookies') {
	$c .= '<h2>'._('Cookies').'</h2>';

	$c .= '<p>'._('This website uses cookies to improve your user experience. We stribe to keep cookie usage at a minimum and as non-intrusive as possible.').'</p>';
	$c .= '<p>'._('These are the cookies we use:').'</p>';
	$c .= '<table>';
	$cookie_arr = $cookie_info->return_arr();
	$cookie_num = count($cookie_arr);
	for ($i = 0; $i < $cookie_num; $i++) {
		$c .= '<tr>';
		$c .= '<td><strong>'.$cookie_arr[$i]['name'].'</strong></td>';
		$c .= '<td>'.$cookie_arr[$i]['desc'].'</td>';
		$c .= '</tr>';
	}
	$c .= '</table>';


}
else {
	$c .= '<h2>'._('Help').'</h2>';
	$c .= '<ul class="nav_menu"><li><a href="/n/help/about/">'._('About us').'</a></li><li><a href="/n/help/cookies/">'._('Cookies').'</a></li></ul>';
}