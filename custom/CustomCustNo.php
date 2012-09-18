<?php
function createCustNo(&$focus, $field, $value, $view) 
{
	$val = $focus->custno_c;
//	echo $view;
	
	if ($view == 'DetailView' or $view == 'SearchForm_advanced_search'){
			return $val;
	}
     if ($view == 'EditView' && empty($val)) {
		$val = 'S' . rand(1000,99999);
		return '' 
            .  '<input id="custno_c" type="enum" name = "custno_c" multiple = "false" value="' . $val . '" />'
            ;
    }
}
