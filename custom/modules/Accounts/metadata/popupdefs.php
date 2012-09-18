<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

global $mod_strings;

$popupMeta = array('moduleMain' => 'Account',
						'varName' => 'ACCOUNT',
						'orderBy' => 'name',
						'whereClauses' => 
							array('name' => 'accounts.name', 
									'billing_address_city' => 'accounts.billing_address_city',
									'phone_office' => 'accounts.phone_office'),
						'searchInputs' =>
							array('name', 'billing_address_city', 'phone_office'),
						'create' =>
							array('formBase' => 'AccountFormBase.php',
									'formBaseClass' => 'AccountFormBase',
									'getFormBodyParams' => array('','','AccountSave'),
									'createButton' => $mod_strings['LNK_NEW_ACCOUNT']
								  ),
						'listviewdefs' => array(
											'NAME' => array(
												'width' => '30',
												'label' => 'LBL_LIST_ACCOUNT_NAME', 
												'link' => true,	
												'default' => true,								        
										        ),
                                                                                        'CUSTNO_C' =>
                                                                                              array (
                                                                                                'width' => '10',
                                                                                                'label' => 'LBL_CUSTNO',
                                                                                                'default' => true,
                                                                                              ),
										    'BILLING_ADDRESS_STREET' => array(
												'width' => '10', 
												'label' => 'LBL_BILLING_ADDRESS_STREET',
												'default' => false,
												),
											'BILLING_ADDRESS_CITY' => array(
												'width' => '10', 
												'label' => 'LBL_LIST_CITY',
												'default' => false,
												),
											'BILLING_ADDRESS_STATE' => array(
									        	'width' => '7', 
									        	'label' => 'LBL_STATE',
									        	'default' => true,									        	
									        	),
									        'BILLING_ADDRESS_POSTALCODE' => array(
												'width' => '10', 
												'label' => 'LBL_BILLING_ADDRESS_POSTALCODE',
												'default' => false,										        
												),
									        'BILLING_ADDRESS_COUNTRY' => array(
										        'width' => '10', 
										        'label' => 'LBL_COUNTRY',
										        'default' => false,
										        ),
										     'PHONE_OFFICE' => array(
												'width' => '10', 
												'label' => 'LBL_LIST_PHONE',
										        'default' => false),	
										    'SHIPPING_ADDRESS_STREET' => array(
										        'width' => '15', 
										        'label' => 'LBL_SHIPPING_ADDRESS_STREET',
                                                                                        'default' => true,
                                                                                        ),
										    'SHIPPING_ADDRESS_CITY' => array(
										        'width' => '10', 
										        'label' => 'LBL_SHIPPING_ADDRESS_CITY',
                                                                                        'default' => true,
                                                                                        ),
										    'SHIPPING_ADDRESS_STATE' => array(
										        'width' => '7', 
										        'label' => 'LBL_SHIPPING_ADDRESS_STATE'),
										    'SHIPPING_ADDRESS_POSTALCODE' => array(
										        'width' => '10', 
										        'label' => 'LBL_SHIPPING_ADDRESS_POSTALCODE'),
										    'SHIPPING_ADDRESS_COUNTRY' => array(
										        'width' => '10', 
										        'label' => 'LBL_SHIPPING_ADDRESS_COUNTRY'),										        								     
										    'ASSIGNED_USER_NAME' => array(
										        'width' => '2', 
										        'label' => 'LBL_LIST_ASSIGNED_USER',
										        'default' => true,
										       ),
											),
						'searchdefs'   => array(
											'custno_c',
											'name',
											array('name' => 'billing_address_city', 'label' => 'LBL_CITY'),
											'billing_address_state',
                                            'slsm_c',
                                            'location_c',
											'dealertype_c',
											array('name' => 'assigned_user_id', 'label' => 'LBL_ASSIGNED_TO')
//											array('name' => 'assigned_user_id', 'label'=>'LBL_ASSIGNED_TO', 'type' => 'enum', 'function' => array('name' => 'get_user_array', 'params' => array(false))),
										  )
						);
