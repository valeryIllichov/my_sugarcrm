<?php
$dictionary['Opportunity']['fields']['month_gp_dollar'] = array (
    'name' => 'month_gp_dollar',
    'vname' => 'LBL_MONTH_GP_DOLLAR',
    'type' => 'int',
    'comment' => 'GP$',
    'function' => array('name'=>'getMonthGPDollar', 'returns'=>'html', 'include'=>'custom/CustomMonthGPDollar.php'),
);