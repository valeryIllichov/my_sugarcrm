<?php
$dictionary['Opportunity']['fields']['gp_dollar'] = array (
    'name' => 'gp_dollar',
    'vname' => 'LBL_GP_DOLLAR',
    'type' => 'int',
    'comment' => 'GP$',
    'function' => array('name'=>'getGPDollar', 'returns'=>'html', 'include'=>'custom/CustomGPDollar.php'),
);