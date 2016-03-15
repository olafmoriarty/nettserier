<?php
	// Path to this folder
	$tpf = NS_PATH.'plugins/'.basename(dirname(__FILE__)).'/';

$head->add_line('<script src="/'.$tpf.'tinymce.min.js"></script>');
$tinymce_init = '<script>'."\n".'tinymce.init({'."\n";
$tinymce_init .= '    selector: \'.wysiwyg\','."\n";
$tinymce_init .= '    toolbar: \'undo redo | bold italic | image | code fullscreen\','."\n";
$tinymce_init .= '    menubar: false,'."\n";

$tinymce_init .= '    content_css: \'/'.NS_PATH.'styles/default/style.css\','."\n";
$tinymce_init .= '    body_class: \'main\','."\n";

$tinymce_init .= '    image_list: [{title: \'Pacham Monster\', value: \'http://beta-testing.nettserier.no/_ns/files/d17416eb1c4945215260372ea3653707.png\'}],'."\n";
$tinymce_init .= '    relative_urls: false,'."\n";

$tinymce_init .= '    plugins: \'code image imagetools fullscreen\','."\n";
$tinymce_init .= '  });'."\n".'</script>';
$head->add_line($tinymce_init);