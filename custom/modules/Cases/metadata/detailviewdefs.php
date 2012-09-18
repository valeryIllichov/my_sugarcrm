<?php
$viewdefs ['Cases'] = 
array (
  'DetailView' => 
  array (
    'templateMeta' => 
    array (
      'form' => 
      array (
        'buttons' => 
        array (
          0 => 'EDIT',
          1 => 'DUPLICATE',
          2 => 'DELETE',
          3 => 'FIND_DUPLICATES',
        ),
      ),
      'maxColumns' => '2',
      'widths' => 
      array (
        0 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
        1 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
      ),
    ),
    'panels' => 
    array (
      'default' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'case_number',
            'label' => 'LBL_CASE_NUMBER',
          ),
          1 => 
          array (
            'name' => 'assigned_user_name',
            'label' => 'LBL_ASSIGNED_TO_NAME',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'priority',
            'label' => 'LBL_PRIORITY',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'status',
            'label' => 'LBL_STATUS',
          ),
          1 => 
          array (
            'name' => 'account_name',
            'label' => 'LBL_ACCOUNT_NAME',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'type',
            'label' => 'LBL_TYPE',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'request_date_c',
            'label' => 'LBL_REQUEST_DATE',
          ),
          1 => 
          array (
            'name' => 'modified_by_name',
            'group' => 'modified_by_name',
            'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}&nbsp;',
            'label' => 'LBL_DATE_MODIFIED',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'end_date_c',
            'label' => 'LBL_END_DATE',
          ),
          1 => 
          array (
            'name' => 'created_by_name',
            'group' => 'created_by_name',
            'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}&nbsp;',
            'label' => 'LBL_DATE_ENTERED',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'connection_c',
            'label' => 'LBL_CONNECTION',
          ),
          1 => 
          array (
            'name' => 'connection_timing_c',
            'label' => 'LBL_CONNECTION_TIMING',
          ),
        ),
        7 => 
        array (
          0 => 
          array (
            'name' => 'connection_type_c',
            'label' => 'LBL_CONNECTION_TYPE',
          ),
          1 => 
          array (
            'name' => 'subject_c',
            'label' => 'LBL_CASE_SUBJECT',
           'customCode' => '{if $fields.subject_c.value == "no"} <span>No</span>{else}<span>Yes ({$fields.subject_c.value})</span>{/if}',
          ),
        ),
        8 => 
        array (
          0 => 
          array (
            'name' => 'description',
            'label' => 'LBL_DESCRIPTION',
          ),
        ),
        9 => 
        array (
          0 => 
          array (
            'name' => 'resolution',
            'label' => 'LBL_RESOLUTION',
          ),
        ),
      ),
    ),
  ),
);
?>
