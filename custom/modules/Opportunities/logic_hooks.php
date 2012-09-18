<?php
$hook_array['after_save'][] = array(
    1, 
    'logichook_product_line',
    'custom/modules/Opportunities/fmpProductLine.php',
    'fmpProductLine',
    'after_save'
);

$hook_array['after_retrieve'][] = array(
    1, 
    'logichook_product_line',
    'custom/modules/Opportunities/fmpProductLine.php',
    'fmpProductLine',
    'after_retrieve'
);
