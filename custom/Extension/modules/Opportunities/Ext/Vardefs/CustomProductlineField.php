<?php
$dictionary['Opportunity']['fields']['product_line'] = array (
    'name' => 'product_line',
    'vname' => 'LBL_PRODUCT_LINE',
    'type' => 'int',
    'function' => array('name'=>'getProductLine', 'returns'=>'html', 'include'=>'custom/CustomProductlineField.php'),
    'len' => 6,
    'comment' => 'Product',
    'source' => 'non-db'
);
