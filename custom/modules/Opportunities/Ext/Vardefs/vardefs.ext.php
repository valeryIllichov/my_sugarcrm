<?php 
 //WARNING: The contents of this file are auto-generated


$dictionary['Opportunity']['fields']['month_sales'] = array (
    'name' => 'month_sales',
    'vname' => 'LBL_MONTH_SALES',
    'type' => 'int',
    'comment' => 'Month Sales',
    'function' => array('name'=>'getMonthSales', 'returns'=>'html', 'include'=>'custom/CustomMonthSales.php'),
);

$dictionary['Opportunity']['fields']['gp_perc'] = array (
    'name' => 'gp_perc',
    'vname' => 'LBL_GP_PERC',
    'type' => 'int',
    'len' => 6,
    'comment' => 'GP%'
);

$dictionary['Opportunity']['fields']['product_line'] = array (
    'name' => 'product_line',
    'vname' => 'LBL_PRODUCT_LINE',
    'type' => 'int',
    'function' => array('name'=>'getProductLine', 'returns'=>'html', 'include'=>'custom/CustomProductlineField.php'),
    'len' => 6,
    'comment' => 'Product',
    'source' => 'non-db'
);



$dictionary['Opportunity']['fields']['SecurityGroups'] = array (
  	'name' => 'SecurityGroups',
    'type' => 'link',
	'relationship' => 'securitygroups_opportunities',
	'module'=>'SecurityGroups',
	'bean_name'=>'SecurityGroup',
    'source'=>'non-db',
	'vname'=>'LBL_SECURITYGROUPS',
);






$dictionary['Opportunity']['fields']['gp_dollar'] = array (
    'name' => 'gp_dollar',
    'vname' => 'LBL_GP_DOLLAR',
    'type' => 'int',
    'comment' => 'GP$',
    'function' => array('name'=>'getGPDollar', 'returns'=>'html', 'include'=>'custom/CustomGPDollar.php'),
);

$dictionary['Opportunity']['fields']['month_gp_perc'] = array (
    'name' => 'month_gp_perc',
    'vname' => 'LBL_MONTH_GP_PERC',
    'type' => 'int',
    'len' => 6,
    'comment' => 'Month GP%'
);

$dictionary['Opportunity']['fields']['month_gp_dollar'] = array (
    'name' => 'month_gp_dollar',
    'vname' => 'LBL_MONTH_GP_DOLLAR',
    'type' => 'int',
    'comment' => 'GP$',
    'function' => array('name'=>'getMonthGPDollar', 'returns'=>'html', 'include'=>'custom/CustomMonthGPDollar.php'),
);
?>