<?php
$searchdefs ['Leads'] = 
array (
  'layout' => 
  array (
    'basic_search' => 
    array (
      'first_name' => 
      array (
        'name' => 'first_name',
        'label' => 'LBL_FIRST_NAME',
        'default' => true,
      ),
      'last_name' => 
      array (
        'name' => 'last_name',
        'label' => 'LBL_LAST_NAME',
        'default' => true,
      ),
      'current_user_only' => 
      array (
        'name' => 'current_user_only',
        'label' => 'LBL_CURRENT_USER_FILTER',
        'type' => 'bool',
        'default' => true,
      ),
      'account_name' => 
      array (
        'width' => '10%',
        'label' => 'LBL_ACCOUNT_NAME',
        'default' => true,
        'name' => 'account_name',
      ),
      'lead_source' => 
      array (
        'name' => 'lead_source',
        'label' => 'LBL_LEAD_SOURCE',
        'default' => true,
      ),
      'status' => 
      array (
        'width' => '10%',
        'label' => 'LBL_STATUS',
        'default' => true,
        'name' => 'status',
      ),
    ),
    'advanced_search' => 
    array (
      'first_name' => 
      array (
        'name' => 'first_name',
        'label' => 'LBL_FIRST_NAME',
        'default' => true,
      ),
      'address_street' => 
      array (
        'name' => 'address_street',
        'label' => 'LBL_ANY_ADDRESS',
        'type' => 'name',
        'default' => true,
      ),
      'phone' => 
      array (
        'name' => 'phone',
        'label' => 'LBL_ANY_PHONE',
        'type' => 'name',
        'default' => true,
      ),
      'last_name' => 
      array (
        'name' => 'last_name',
        'label' => 'LBL_LAST_NAME',
        'default' => true,
      ),
      'address_city' => 
      array (
        'name' => 'address_city',
        'label' => 'LBL_CITY',
        'type' => 'name',
        'default' => true,
      ),
      'email' => 
      array (
        'name' => 'email',
        'label' => 'LBL_ANY_EMAIL',
        'type' => 'name',
        'default' => true,
      ),
      'account_name' => 
      array (
        'name' => 'account_name',
        'label' => 'LBL_ACCOUNT_NAME',
        'default' => true,
      ),
      'department' => 
      array (
        'width' => '10%',
        'label' => 'LBL_DEPARTMENT',
        'default' => true,
        'name' => 'department',
      ),
      'title' => 
      array (
        'width' => '10%',
        'label' => 'LBL_TITLE',
        'default' => true,
        'name' => 'title',
      ),
      'address_state' => 
      array (
        'name' => 'address_state',
        'label' => 'LBL_STATE',
        'type' => 'name',
        'default' => true,
      ),
      'address_postalcode' => 
      array (
        'name' => 'address_postalcode',
        'label' => 'LBL_POSTAL_CODE',
        'type' => 'name',
        'default' => true,
      ),
      '' => 
      array (
        'name' => '',
        'default' => true,
        'label' => '',
      ),
      'lead_source' => 
      array (
        'name' => 'lead_source',
        'label' => 'LBL_LEAD_SOURCE',
        'default' => true,
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