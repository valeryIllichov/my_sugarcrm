<?php
$searchdefs ['Cases'] = 
array (
  'layout' => 
  array (
    'basic_search' => 
    array (
      'case_number' => 
      array (
        'name' => 'case_number',
        'label' => 'LBL_NUMBER',
        'default' => true,
      ),
      'account_name' => 
      array (
        'name' => 'account_name',
        'label' => 'LBL_ACCOUNT_NAME',
        'default' => true,
      ),
      'current_user_only' => 
      array (
        'name' => 'current_user_only',
        'label' => 'LBL_CURRENT_USER_FILTER',
        'type' => 'bool',
        'default' => true,
      ),
      'connection_c' => 
      array (
        'width' => '10%',
        'label' => 'LBL_CONNECTION',
        'default' => true,
        'name' => 'connection_c',
      ),
      'type' => 
      array (
        'width' => '10%',
        'label' => 'LBL_TYPE',
        'default' => true,
        'name' => 'type',
      ),
    ),
    'advanced_search' => 
    array (
      'case_number' => 
      array (
        'name' => 'case_number',
        'label' => 'LBL_NUMBER',
        'default' => true,
      ),
      'account_name' => 
      array (
        'name' => 'account_name',
        'label' => 'LBL_ACCOUNT_NAME',
        'default' => true,
      ),
      'connection_c' => 
      array (
        'width' => '10%',
        'label' => 'LBL_CONNECTION',
        'default' => true,
        'name' => 'connection_c',
      ),
      'type' => 
      array (
        'width' => '10%',
        'label' => 'LBL_TYPE',
        'default' => true,
        'name' => 'type',
      ),
      'status' => 
      array (
        'name' => 'status',
        'label' => 'LBL_STATUS',
        'default' => true,
      ),
      'assigned_user_id' => 
      array (
        'name' => 'assigned_user_id',
        'type' => 'enum',
        'label' => 'LBL_ASSIGNED_TO',
        'function' => 
        array (
          'name' => 'get_user_array',
          'params' => 
          array (
            0 => false,
          ),
        ),
        'default' => true,
      ),
      'priority' => 
      array (
        'name' => 'priority',
        'label' => 'LBL_PRIORITY',
        'default' => true,
      ),
    ),
  ),
  'templateMeta' => 
  array (
    'maxColumns' => '3',
    'widths' => 
    array (
      'label' => '10',
      'field' => '30',
    ),
  ),
);
?>