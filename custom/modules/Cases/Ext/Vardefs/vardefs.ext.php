<?php 
 //WARNING: The contents of this file are auto-generated



$dictionary['Case']['fields']['SecurityGroups'] = array (
  	'name' => 'SecurityGroups',
    'type' => 'link',
	'relationship' => 'securitygroups_cases',
	'module'=>'SecurityGroups',
	'bean_name'=>'SecurityGroup',
    'source'=>'non-db',
	'vname'=>'LBL_SECURITYGROUPS',
);






if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

$dictionary['Case']['fields']['request_date_c'] = array (
    'name' => 'request_date_c',
    'vname'=>'LBL_REQUEST_DATE',
    'type' => 'date',
	'default' => date('m/d/Y'),
  'customCode' => '{literal}<script type="text/javascript">function isValidDuration() { form = document.getElementById(\'EditView\'); onkeyup="SugarWidgetScheduler.update_time();"/>{$fields.duration_minutes.value} {$MOD.LBL_HOURS_MINS}'
);


$dictionary['Case']['fields']['end_date_c'] = array (
    'name' => 'end_date_c',
    'vname'=>'LBL_END_DATE',
    'type' => 'date'
);


$dictionary['Case']['fields']['connection_c'] = array (
    'name' => 'connection_c',
    'vname'=>'LBL_CONNECTION',
    'type' => 'enum',
	'default' => 'New Connection',
	'options' => 'connection_list'
);

$dictionary['Case']['fields']['connection_type_c'] = array (
    'name' => 'connection_type_c',
    'vname'=>'LBL_CONNECTION_TYPE',
    'type' => 'enum',
    'default' => 'no',
    'options' => 'custom_flag'
    //'default' => 'FMPConnect',
    //'options' => 'connection_type_list',
//	'required' => true
//    'function' => array('name'=>'Case_FMPConnect', 'returns'=>'html', 'include'=>'custom/CustomCase.php')
);
$dictionary['Case']['fields']['connection_timing_c'] = array (
    'name' => 'connection_timing_c',
    'vname'=>'LBL_CONNECTION_TIMING',
    'type' => 'enum',
        'default' => 'WHI',
       // 'options' => 'connection_timing_list'
    'options' => 'customer_catalog_list'
);

$dictionary['Case']['fields']['name'] = array (
    'name' => 'name',
    'type' => 'enum',
    'options' => 'case_subject_list',
    'default' => '',
    'required' => true
);

$dictionary['Case']['fields']['subject_c'] = array (
    'name' => 'subject_c',
    'type' => 'enum',
    'options' => 'custom_flag',
    'default' => 'no',
   // 'options' => 'case_subject_list',
   // 'default' => '',
    //'required' => true,
    'vname' => 'LBL_CASE_SUBJECT'
);



?>