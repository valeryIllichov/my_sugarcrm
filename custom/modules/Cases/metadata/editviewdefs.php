<?php
$viewdefs ['Cases'] = 
array (
  'EditView' => 
  array (
    'templateMeta' => 
    array (
      'includes' => 
      array (
        0 => 
        array (
          'file' => 'custom/modules/Cases/javascript/cases_scr.js',
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
            'type' => 'readonly',
            'displayParams' => 
            array (
              'required' => true,
            ),
            'label' => 'LBL_NUMBER',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'priority',
            'label' => 'LBL_PRIORITY',
          ),
          1 => 
          array (
            'name' => 'assigned_user_name',
            'label' => 'LBL_ASSIGNED_TO_NAME',
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
            'name' => 'connection_c',
            'label' => 'LBL_CONNECTION',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'request_date_c',
            'label' => 'LBL_REQUEST_DATE',
          ),
          1 => 
          array (
            'name' => 'end_date_c',
            'label' => 'LBL_END_DATE',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'connection_type_c',
            'label' => 'LBL_CONNECTION_TYPE',
          ),
        ),
        7 => 
        array (
          0 => 
          array (
            'name' => 'subject_c',
            'label' => 'LBL_CASE_SUBJECT',
          ),
        ),
        8 => 
        array (
          0 => 
          array (
            'name' => 'connection_timing_c',
            'label' => 'LBL_CONNECTION_TIMING',
          ),
        ),
        9 => 
        array (
          0 => 
          array (
            'name' => 'description',
            'displayParams' => 
            array (
              'rows' => '8',
              'cols' => '80',
            ),
            'nl2br' => true,
            'label' => 'LBL_DESCRIPTION',
          ),
        ),
        10 => 
        array (
          0 => 
          array (
            'name' => 'resolution',
            'displayParams' => 
            array (
              'rows' => '5',
              'cols' => '80',
            ),
            'nl2br' => true,
            'label' => 'LBL_RESOLUTION',
          ),
        ),
      ),
    ),
  ),
);
?>
