<?php
$searchdefs ['Accounts'] = 
array (
  'layout' => 
  array (
    'basic_search' => 
    array (
      'custno_c' => 
      array (
        'width' => '10%',
        'label' => 'LBL_CUSTNO',
        'default' => true,
        'name' => 'custno_c',
	'type'=> 'name',
      ),
      'name' => 
      array (
        'name' => 'name',
        'label' => 'LBL_NAME',
        'default' => true,
      ),
    ),
    'advanced_search' => 
    array (
      'custno_c' => 
      array (
        'width' => '10%',
        'label' => 'LBL_CUSTNO',
        'default' => true,
        'name' => 'custno_c',
        'type'=> 'name',
	),
      'name' => 
      array (
        'name' => 'name',
        'label' => 'LBL_NAME',
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
      'address_city' => 
      array (
        'name' => 'address_city',
        'label' => 'LBL_CITY',
        'type' => 'name',
        'default' => true,
      ),
      'address_state' => 
      array (
        'name' => 'address_state',
        'label' => 'LBL_STATE',
        'type' => 'name',
        'default' => true,
      ),
      'slsm_c' => 
      array (
        'width' => '10%',
        'label' => 'LBL_SLSM',
        'default' => true,
        'name' => 'slsm_c',
      ),
      'location_c' => 
      array (
        'width' => '10%',
        'label' => 'LBL_LOCATION',
        'default' => true,
        'name' => 'location_c',
      ),
      'dealertype_c' => 
      array (
        'width' => '10%',
        'label' => 'LBL_DEALERTYPE',
        'default' => true,
        'name' => 'dealertype_c',
      ),
      'assigned_user_name' => 
      array (
        'width' => '10%',
        'label' => 'LBL_ASSIGNED_TO_NAME',
        'default' => true,
        'name' => 'assigned_user_name',
      ),
      'company_c' => 
      array (
        'width' => '10%',
        'label' => 'LBL_COMPANY',
        'default' => true,
        'name' => 'company_c',
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