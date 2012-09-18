<?php
$dictionary['Call']['fields']['date_previuos_c'] = array (
    'name' => 'date_previuos_c',
    'vname'=>'LBL_DATE_PREVIOUS',
    'type' => 'int',
    'comment' => 'Previous Call',
    'function' => array('name'=>'getPreviousCall', 'returns'=>'html', 'include'=>'custom/CustomPreviousCallField.php'),
);
