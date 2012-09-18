<?php
$dictionary['Opportunity']['fields']['month_sales'] = array (
    'name' => 'month_sales',
    'vname' => 'LBL_MONTH_SALES',
    'type' => 'int',
    'comment' => 'Month Sales',
    'function' => array('name'=>'getMonthSales', 'returns'=>'html', 'include'=>'custom/CustomMonthSales.php'),
);