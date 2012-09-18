<?php

if (!defined('sugarEntry') || !'sugarEntry') die('Not A Valid Entry Point');
$hook_version = 1;
$hook_array = Array();
$hook_array['before_save'][] = array(
	1,
	'custom',
	'custom/AddTimeStamp.php',
	'AddTimeStamp', 
	'stamp'
);
