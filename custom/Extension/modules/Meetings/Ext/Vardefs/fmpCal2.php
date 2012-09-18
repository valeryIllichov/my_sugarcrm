<?php
$dictionary['Meeting']['fields']['outcome_c'] = array (
    'name' => 'outcome_c',
    'vname'=>'LBL_OUTCOME',
    'type' => 'text'
);

$dictionary['Meeting']['fields']['parent_name'] = array (
    'name'=> 'parent_name',
    'parent_type'=>'record_type_display' ,
    'type_name'=>'parent_type',
    'id_name'=>'parent_id',
    'vname'=>'LBL_LIST_RELATED_TO',
    'type'=>'parentfmp',
    'group'=>'parent_name',
    'source'=>'non-db',
    'options'=> 'parent_type_display'
);

$dictionary['Meeting']['fields']['date_previuos_c'] = array (
    'name' => 'date_previuos_c',
    'vname'=>'LBL_DATE_PREVIOUS',
    'type' => 'int',
    'comment' => 'Previous Meeting',
    'function' => array('name'=>'getPreviousMeeting', 'returns'=>'html', 'include'=>'custom/CustomPreviousMeetingField.php'),
);

