<?php

$admin_option_defs=array();
$admin_option_defs['Administration']['securitygroup_management']= array($image_path . 'SecurityGroups','LBL_MANAGE_SECURITYGROUPS_TITLE','LBL_MANAGE_SECURITYGROUPS','./index.php?module=SecurityGroups&action=index');
$admin_option_defs['Administration']['securitygroup_config']= array($image_path . 'SecurityGroups','LBL_CONFIG_SECURITYGROUPS_TITLE','LBL_CONFIG_SECURITYGROUPS','./index.php?module=SecurityGroups&action=config');
$admin_group_header[]= array('LBL_SECURITYGROUPS','',false,$admin_option_defs, '');


?>