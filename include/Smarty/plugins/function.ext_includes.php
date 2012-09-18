<?php

function smarty_function_ext_includes($params, &$smarty)
{
	$ret = '<link rel="stylesheet" type="text/css" href="' . getJSPath("themes/default/ext/resources/css/ext-all.css") . '" />'
		 . '<link rel="stylesheet" type="text/css" href="' . getJSPath("themes/default/ext/resources/css/xtheme-gray.css") . '" />';
		 
	global $theme;
	if (is_dir("themes/$theme/ext/resources/css")) {
			$cssDir = opendir("themes/$theme/ext/resources/css");
			while (($file = readdir($cssDir)) !== false) {
				if (strcasecmp(substr($file, -4), '.css' == 0)) {
            		$ret .= "<link rel='stylesheet' type='text/css' href='" . getJSPath("themes/$theme/ext/resources/css/$file") . "' />";
				}
        	}
	}	 
		 
	$ret .= '<script type="text/javascript" language="Javascript" src="' . getJSPath('include/javascript/ext-2.0/adapter/ext/ext-base.js') .'"></script>'



		. '<script type="text/javascript" language="Javascript" src=' . getJSPath('include/javascript/ext-2.0/ext-all.js') . '"></script>';



	
	
	return $ret;
	
}
