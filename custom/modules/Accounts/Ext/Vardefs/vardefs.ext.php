<?php 
 //WARNING: The contents of this file are auto-generated



$dictionary['Account']['fields']['SecurityGroups'] = array (
  	'name' => 'SecurityGroups',
    'type' => 'link',
	'relationship' => 'securitygroups_accounts',
	'module'=>'SecurityGroups',
	'bean_name'=>'SecurityGroup',
    'source'=>'non-db',
	'vname'=>'LBL_SECURITYGROUPS',
);






$dictionary['Account']['fields']['aarbal_c'] = array (
    'name' => 'aarbal_c',
    'vname'=>'LBL_AARBAL',
    'type' => 'currency'
);

$dictionary['Account']['fields']['addl_fleet_info_c'] = array (
    'name' => 'addl_fleet_info_c',
    'vname'=>'LBL_ADDL_FLEET_INFO',
    'type' => 'varchar',
    'len' => 50
);

$dictionary['Account']['fields']['rfp_c'] = array (
    'name' => 'rfp_c',
    'vname'=>'LBL_RFP',
    'type' => 'varchar',
    'len' => 255
);

$dictionary['Account']['fields']['other_banner_c'] = array (
    'name' => 'other_banner_c',
    'vname'=>'LBL_OTHER',
    'type' => 'varchar',
    'len' => 25 
);

$dictionary['Account']['fields']['ar60_90_c'] = array (
    'name' => 'ar60_90_c',
    'vname'=>'LBL_AR60_90',
    'type' => 'currency'
);

$dictionary['Account']['fields']['bays_c'] = array (
    'name' => 'bays_c',
    'vname'=>'LBL_BAYS',
    'type' => 'int',
    'len' => 11
);

$dictionary['Account']['fields']['creditlimit_c'] = array (
    'name' => 'creditlimit_c',
    'vname'=>'LBL_CREDITLIMIT',
    'type' => 'currency'
);

$dictionary['Account']['fields']['custno_c'] = array (
    'name' => 'custno_c',
    'vname'=>'LBL_CUSTNO',
    'type' => 'varchar',
    'len' => 25,
    'function' => array('name'=>'createCustNo', 'returns'=>'html', 'include'=>'custom/CustomCustNo.php')
);

$dictionary['Account']['fields']['dealer_prev_maint_c'] = array (
    'name' => 'dealer_prev_maint_c',
    'vname'=>'LBL_DEALER_PREV_MAINT',
    'type' => 'enum',
    'default' => '',
    'options' => 'dealer_prev_maint_list'
);

$dictionary['Account']['fields']['dealer_mgmt_sys_c'] = array (
    'name' => 'dealer_mgmt_sys_c',
    'vname'=>'LBL_DEALER_MGMT_SYS',
    'type' => 'enum',
    'default' => '',
    'options' => 'dealer_management_system_list'
);

$dictionary['Account']['fields']['shop_mgmt_sys_c'] = array (
    'name' => 'shop_mgmt_sys_c',
    'vname'=>'LBL_SHOP_MGMT_SYS',
    'type' => 'enum',
    'default' => '',
    'options' => 'shop_management_system_list'
);

$dictionary['Account']['fields']['fleet_model_name_c'] = array (
    'name' => 'fleet_model_name_c',
    'vname'=>'LBL_FLEET_MODEL_NAME',
    'type' => 'varchar',
    'len' => 25
);

$dictionary['Account']['fields']['fleet_num_vehicles_c'] = array (
    'name' => 'fleet_num_vehicles_c',
    'vname'=>'LBL_FLEET_NUM_VEHICLES',
    'type' => 'int',
    'len' => 11
);

$dictionary['Account']['fields']['fulldealertype_c'] = array (
    'name' => 'fulldealertype_c',
    'vname'=>'LBL_FULLDEALERTYPE',
    'type' => 'varchar',
    'len' => 10
);


$dictionary['Account']['fields']['narrative_c'] = array (
    'name' => 'narrative_c',
    'vname'=>'LBL_NARRATIVE',
    'type' => 'varchar',
    'len' => 50
);

$dictionary['Account']['fields']['last_payment_date_c'] = array (
    'name' => 'last_payment_date_c',
    'vname'=>'LBL_LAST_PAYMENT_DATE',
    'type' => 'date'
);

$dictionary['Account']['fields']['last_payment_amt_c'] = array (
    'name' => 'last_payment_amt_c',
    'vname'=>'LBL_LAST_PAYMENT_AMT',
    'type' => 'currency'
);


$dictionary['Account']['fields']['avg_days_c'] = array (
    'name' => 'avg_days_c',
    'vname'=>'LBL_AVG_DAYS',
    'type' => 'varchar',
    'len' => 15
);

$dictionary['Account']['fields']['jobber_num_stores_c'] = array (
    'name' => 'jobber_num_stores_c',
    'vname'=>'LBL_JOBBER_NUM_STORES',
    'type' => 'int',
    'len' => 11
);

$dictionary['Account']['fields']['location_c'] = array (
    'name' => 'location_c',
    'vname'=>'LBL_LOCATION',
    'type' => 'enum',
    'default' => '',
    'options' => 'fmp_location_list',
	'required'=> true
);

$dictionary['Account']['fields']['ly_sales_c'] = array (
    'name' => 'ly_sales_c',
    'vname'=>'LBL_LY_SALES',
    'type' => 'currency' 
);

$dictionary['Account']['fields']['mtd_budget_gppct_c'] = array (
    'name' => 'mtd_budget_gppct_c',
    'vname'=>'LBL_MTD_BUDGET_GPPCT',
    'type' => 'varchar',
    'len' => 10
);

$dictionary['Account']['fields']['mtd_gppct_c'] = array (
    'name' => 'mtd_gppct_c',
    'vname'=>'LBL_MTD_GPPCT',
    'type' => 'varchar',
    'len' => 10
);

$dictionary['Account']['fields']['mtd_sales_c'] = array (
    'name' => 'mtd_sales_c',
    'vname'=>'LBL_MTD_SALES',
    'type' => 'currency'
);

$dictionary['Account']['fields']['ytd_sales_c'] = array (
    'name' => 'ytd_sales_c',
    'vname'=>'LBL_YTD_SALES',
    'type' => 'currency'
);

$dictionary['Account']['fields']['over_90_c'] = array (
    'name' => 'over_90_c',
    'vname'=>'LBL_OVER_90',
    'type' => 'currency'
);

$dictionary['Account']['fields']['stocklocation_c'] = array (
    'name' => 'stocklocation_c',
    'vname'=>'LBL_STOCKLOCATION',
    'type' => 'enum',
    'default' => '',
    'options' => 'fmp_location_list'
);

$dictionary['Account']['fields']['ytd_budget_gppct_c'] = array (
    'name' => 'ytd_budget_gppct_c',
    'vname'=>'LBL_YTD_BUDGET_GPPCT',
    'type' => 'varchar',
    'len' => 10
);

$dictionary['Account']['fields']['ytd_gppct_c'] = array (
    'name' => 'ytd_gppct_c',
    'vname'=>'LBL_YTD_GPPCT',
    'type' => 'varchar',
    'len' => 10
);

$dictionary['Account']['fields']['ytd_sales_c'] = array (
    'name' => 'ytd_sales_c',
    'vname'=>'LBL_YTD_SALES',
    'type' => 'currency'
);

$dictionary['Account']['fields']['addl_aftermarket_info_c'] = array (
    'name' => 'addl_aftermarket_info_c',
    'vname'=>'LBL_ADDL_AFTERMARKET_INFO',
    'type' => 'varchar',
    'len' => 50
);

$dictionary['Account']['fields']['addl_jobber_info_c'] = array (
    'name' => 'addl_jobber_info_c',
    'vname'=>'LBL_ADDL_JOBBER_INFO',
    'type' => 'varchar',
    'len' => 50
);

$dictionary['Account']['fields']['arcurrent_c'] = array (
    'name' => 'arcurrent_c',
    'vname'=>'LBL_ARCURRENT',
    'type' => 'currency'
);

$dictionary['Account']['fields']['chrysler_vehicle_count_c'] = array (
    'name' => 'chrysler_vehicle_count_c',
    'vname'=>'LBL_CHRYSLER_VEHICLE_COUNT',
    'type' => 'int',
    'len' => 11
);

$dictionary['Account']['fields']['currency_id'] = array (
    'name' => 'currency_id',
    'vname'=>'LBL_CURRENCY',
    'type' => 'id'
);

$dictionary['Account']['fields']['custtype_c'] = array (
    'name' => 'custtype_c',
    'vname'=>'LBL_CUSTTYPE',
    'type' => 'varchar',
    'len' => 10
);

$dictionary['Account']['fields']['fleetmaintenanceoptions_c'] = array (
    'name' => 'fleetmaintenanceoptions_c',
    'vname'=>'LBL_FLEET_MAINT_OPT',
    'type' => 'multienum',
    'isMultiSelect' => true,
    'default' => '',
    'options' => 'fmpfleetmainttype_list'
);

$dictionary['Account']['fields']['fleet_model_number2_c'] = array (
    'name' => 'fleet_model_number2_c',
    'vname'=>'LBL_FLEET_MODEL_NUMBER2',
    'type' => 'int',
    'len' => 11
);

$dictionary['Account']['fields']['fmpconnect_c'] = array (
    'name' => 'fmpconnect_c',
    'vname'=>'LBL_FMPCONNECT',
    'type' => 'enum',
    'default' => '',
    'options' => 'fmpconnect_list'
);

$dictionary['Account']['fields']['gm_vehicle_count_c'] = array (
    'name' => 'gm_vehicle_count_c',
    'vname'=>'LBL_GM_VEHICLE_COUNT',
    'type' => 'int',
    'len' => 11
);

$dictionary['Account']['fields']['jobber_num_trucks_c'] = array (
    'name' => 'jobber_num_trucks_c',
    'vname'=>'LBL_JOBBER_NUM_TRUCKS',
    'type' => 'int',
    'len' => 11
);

$dictionary['Account']['fields']['ly_gppct_c'] = array (
    'name' => 'ly_gppct_c',
    'vname'=>'LBL_LY_GPPCT',
    'type' => 'varchar',
    'len' => 25
);

$dictionary['Account']['fields']['masterar_c'] = array (
    'name' => 'masterar_c',
    'vname'=>'LBL_MASTERAR',
    'type' => 'int',
    'len' => 11
);

$dictionary['Account']['fields']['mtd_budget_gp_c'] = array (
    'name' => 'mtd_budget_gp_c',
    'vname'=>'LBL_MTD_BUDGET_GP',
    'type' => 'currency'
);

$dictionary['Account']['fields']['mtd_gp_c'] = array (
    'name' => 'mtd_gp_c',
    'vname'=>'LBL_MTD_GP',
    'type' => 'currency'
);

$dictionary['Account']['fields']['ytd_gp_c'] = array (
    'name' => 'ytd_gp_c',
    'vname'=>'LBL_YTD_GP',
    'type' => 'currency'
);

$dictionary['Account']['fields']['nexpart_c'] = array (
    'name' => 'nexpart_c',
    'vname'=>'LBL_NEXPART',
    'type' => 'enum',
    'default' => '',
    'options' => 'nexpart_list'
);

$dictionary['Account']['fields']['region_c'] = array (
    'name' => 'region_c',
    'vname'=>'LBL_REGION',
    'type' => 'enum',
    'default' => '',
    'options' => 'fmp_region_list',
	'required' => true
);

$dictionary['Account']['fields']['technicians_c'] = array (
    'name' => 'technicians_c',
    'vname'=>'LBL_TECHNICIANS',
    'type' => 'int',
    'len' => 11
);

$dictionary['Account']['fields']['ytd_budget_gp_c'] = array (
    'name' => 'ytd_budget_gp_c',
    'vname'=>'LBL_YTD_BUDGET_GP',
    'type' => 'currency'
);

$dictionary['Account']['fields']['ytd_gp_c'] = array (
    'name' => 'ytd_gp_c',
    'vname'=>'LBL_YTD_GP',
    'type' => 'currency'
);

$dictionary['Account']['fields']['addl_dealer_information_c'] = array (
    'name' => 'addl_dealer_information_c',
    'vname'=>'LBL_ADDL_DEALER_INFORMATION',
    'type' => 'varchar',
    'len' => 50
);

$dictionary['Account']['fields']['ar30_60_c'] = array (
    'name' => 'ar30_60_c',
    'vname'=>'LBL_AR30_60',
    'type' => 'currency'
);

$dictionary['Account']['fields']['arfuture_c'] = array (
    'name' => 'arfuture_c',
    'vname'=>'LBL_ARFUTURE',
    'type' => 'currency'
);

$dictionary['Account']['fields']['creditcode_c'] = array (
    'name' => 'creditcode_c',
    'vname'=>'LBL_CREDITCODE',
    'type' => 'varchar',
    'len' => 5
);

$dictionary['Account']['fields']['custid_c'] = array (
    'name' => 'custid_c',
    'vname'=>'LBL_CUSTID',
    'type' => 'int',
    'len' => 11
);

$dictionary['Account']['fields']['dealertype_c'] = array (
    'name' => 'dealertype_c',
    'vname'=>'LBL_DEALERTYPE',
    'type' => 'enum',
    'default' => '',
    'options' => 'dealertype_list',
	'required' => true

);

$dictionary['Account']['fields']['fleet_model_name2_c'] = array (
    'name' => 'fleet_model_name2_c',
    'vname'=>'LBL_FLEET_MODEL_NAME2',
    'type' => 'varchar',
    'len' => 25
);

$dictionary['Account']['fields']['additional_notes_c'] = array (
    'name' => 'additional_notes_c',
    'vname'=>'LBL_ADDITIONAL_NOTES',
    'type' => 'varchar',
    'len' => 200
);

$dictionary['Account']['fields']['fleet_model_number_c'] = array (
    'name' => 'fleet_model_number_c',
    'vname'=>'LBL_FLEET_MODEL_NUMBER',
    'type' => 'int',
    'len' => 11
);

$dictionary['Account']['fields']['ford_vehicle_count_c'] = array (
    'name' => 'ford_vehicle_count_c',
    'vname'=>'LBL_FORD_VEHICLE_COUNT',
    'type' => 'int',
    'len' => 11
);

$dictionary['Account']['fields']['jobber_affilliation_c'] = array (
    'name' => 'jobber_affilliation_c',
    'vname'=>'LBL_JOBBER_AFFILLIATION',
    'type' => 'varchar',
    'len' => 25
);

$dictionary['Account']['fields']['hotbuttons_c'] = array (
    'name' => 'hotbuttons_c',
    'vname'=>'LBL_HOTBUTTONS',
    'type' => 'varchar',
    'len' => 25
);
$dictionary['Account']['fields']['jobber_retailorwholesale_c'] = array (
    'name' => 'jobber_retailorwholesale_c',
    'vname'=>'LBL_JOBBER_RETAILORWHOLESALE',
    'type' => 'enum',
    'default' => '',
    'options' => 'jobber_retailorwholesale_list'
);

$dictionary['Account']['fields']['ly_gp_c'] = array (
    'name' => 'ly_gp_c',
    'vname'=>'LBL_LY_GP',
    'type' => 'currency'
);

$dictionary['Account']['fields']['mly_sales_c'] = array (
    'name' => 'mly_sales_c',
    'vname'=>'LBL_MLY_SALES',
    'type' => 'currency'
);

$dictionary['Account']['fields']['mtd_budget_sales_c'] = array (
    'name' => 'mtd_budget_sales_c',
    'vname'=>'LBL_MTD_BUDGET_SALES',
    'type' => 'currency'
);

$dictionary['Account']['fields']['mtd_projected_c'] = array (
    'name' => 'mtd_projected_c',
    'vname'=>'LBL_MTD_PROJECTED',
    'type' => 'currency'
);

$dictionary['Account']['fields']['ytd_projected_c'] = array (
    'name' => 'ytd_projected_c',
    'vname'=>'LBL_YTD_PROJECTED',
    'type' => 'currency'
);

$dictionary['Account']['fields']['other_vehicle_count_c'] = array (
    'name' => 'other_vehicle_count_c',
    'vname'=>'LBL_OTHER_VEHICLE_COUNT',
    'type' => 'int',
    'len' => 11
);

$dictionary['Account']['fields']['slsm_c'] = array (
    'name' => 'slsm_c',
    'vname'=>'LBL_SLSM',
    'type' => 'enum',
    'default' => '',
    'options' => 'fmp_slsm_list',
	'required' => true
);

$dictionary['Account']['fields']['termscode_c'] = array (
    'name' => 'termscode_c',
    'vname'=>'LBL_TERMSCODE',
    'type' => 'enum',
    'default' => '',
    'options' => 'termscode_list'
);

$dictionary['Account']['fields']['primary_suppliers_c'] = array (
    'name' => 'primary_suppliers_c',
    'vname'=>'LBL_PRIMARY_SUPPLIER',
    'type' => 'enum',
    'default' => '',
    'options' => 'primarysuppliers_list'
);

$dictionary['Account']['fields']['primary_suppliers1_c'] = array (
    'name' => 'primary_suppliers1_c',
    'vname'=>'LBL_PRIMARY_SUPPLIER1',
    'type' => 'enum',
    'default' => '',
    'options' => 'primarysuppliers_list'
);

$dictionary['Account']['fields']['primary_suppliers2_c'] = array (
    'name' => 'primary_suppliers2_c',
    'vname'=>'LBL_PRIMARY_SUPPLIER2',
    'type' => 'enum',
    'default' => '',
    'options' => 'primarysuppliers_list'
);

$dictionary['Account']['fields']['primary_suppliers3_c'] = array (
    'name' => 'primary_suppliers3_c',
    'vname'=>'LBL_PRIMARY_SUPPLIER3',
    'type' => 'varchar',
    'len' => 25	   
);

$dictionary['Account']['fields']['ytd_budget_sales_c'] = array (
    'name' => 'ytd_budget_sales_c',
    'vname'=>'LBL_YTD_BUDGET_SALES',
    'type' => 'currency'
);

$dictionary['Account']['fields']['fmp_visa_c'] = array (
    'name' => 'fmp_visa_c',
    'vname'=>'LBL_FMP_VISA',
    'type' => 'enum',
    'default' => '',
    'options' => 'blank_yes_no_list'
);

$dictionary['Account']['fields']['motorcraft_rewards_c'] = array (
    'name' => 'motorcraft_rewards_c',
    'vname'=>'LBL_MOTOR_REWARDS',
    'type' => 'enum',
    'default' => '',
    'options' => 'blank_yes_no_list'
);

$dictionary['Account']['fields']['acdelco_rewards_c'] = array (
    'name' => 'acdelco_rewards_c',
    'vname'=>'LBL_ACDELCO_REWARDS',
    'type' => 'enum',
    'default' => '',
    'options' => 'blank_yes_no_list'
);

$dictionary['Account']['fields']['ytd_projected_c'] = array (
    'name' => 'ytd_projected_c',
    'vname'=>'LBL_YTD_PROJECTED',
    'type' => 'currency'
);

  $dictionary['Account']['fields']['company_c'] = array (
  'name' => 'company_c',
  'vname'=>'LBL_COMPANY',
  'type' => 'enum',
  'default' => '',
  'options' => 'fmp_company_list'
  );
 
  $dictionary['Account']['fields']['internet_c'] = array (
    'name' => 'internet_c',
    'vname'=>'LBL_INTERNET',
    'type' => 'enum',
    'default' => '',
    'options' => 'yes_no_list'
);

$dictionary['Account']['fields']['larger_group_c'] = array (
    'name' => 'larger_group_c',
    'vname'=>'LBL_LARGER_GROUP',
    'type' => 'enum',
    'default' => '',
    'options' => 'no_yes_list'
);

$dictionary['Account']['fields']['affiliated_banner_c'] = array (
    'name' => 'affiliated_banner_c',
    'vname'=>'LBL_AFFILIATED_BANNER',
    'type' => 'enum',
    'default' => '',
    'options' => 'no_yes_list'
);

$dictionary['Account']['fields']['banner_c'] = array (
    'name' => 'banner_c',
    'vname'=>'LBL_BANNER',
    'type' => 'enum',
    'default' => '',
    'options' => 'banner_program_list'
);

 $dictionary['Account']['fields']['larger_group_name_c'] = array (
    'name' => 'larger_group_name_c',
    'vname'=>'LBL_LARGER_GROUP_NAME',
    'type' => 'varchar',
    'len' => 25
);
 
$dictionary['Account']['fields']['own_lease_c'] = array (
    'name' => 'own_lease_c',
    'vname'=>'LBL_OWN_LEASE',
    'type' => 'enum',
    'default' => '',
    'options' => 'own_lease_list'
);

$dictionary['Account']['fields']['cat_purch_batt_c'] = array (
    'name' => 'cat_purch_batt_c',
    'vname'=>'LBL_CAT_PURCH_BATT',
    'type' => 'bool',
    'default' => '',
);

$dictionary['Account']['fields']['cat_purch_filters_c'] = array (
    'name' => 'cat_purch_filters_c',
    'vname'=>'LBL_CAT_PURCH_FILTERS',
    'type' => 'bool',
    'default' => '',
);

$dictionary['Account']['fields']['cat_purch_brake_c'] = array (
    'name' => 'cat_purch_brake_c',
    'vname'=>'LBL_CAT_PURCH_BRAKE',
    'type' => 'bool',
    'default' => '',
);

$dictionary['Account']['fields']['cat_purch_chassis_c'] = array (
    'name' => 'cat_purch_chassis_c',
    'vname'=>'LBL_CAT_PURCH_CHASSIS',
    'type' => 'bool',
    'default' => '',
);

$dictionary['Account']['fields']['cat_purch_elec_c'] = array (
    'name' => 'cat_purch_elec_c',
    'vname'=>'LBL_CAT_PURCH_ELEC',
    'type' => 'bool',
    'default' => '',
);

$dictionary['Account']['fields']['cat_purch_emis_c'] = array (
    'name' => 'cat_purch_emis_c',
    'vname'=>'LBL_CAT_PURCH_EMIS',
    'type' => 'bool',
    'default' => '',
);

$dictionary['Account']['fields']['cat_purch_fuel_c'] = array (
    'name' => 'cat_purch_fuel_c',
    'vname'=>'LBL_CAT_PURCH_FUEL',
    'type' => 'bool',
    'default' => '',
);

$dictionary['Account']['fields']['cat_purch_heat_c'] = array (
    'name' => 'cat_purch_heat_c',
    'vname'=>'LBL_CAT_PURCH_HEAT',
    'type' => 'bool',
    'default' => '',
);

$dictionary['Account']['fields']['cat_purch_ride_c'] = array (
    'name' => 'cat_purch_ride_c',
    'vname'=>'LBL_CAT_PURCH_RIDE',
    'type' => 'bool',
    'default' => '',
);

$dictionary['Account']['fields']['cat_purch_steer_c'] = array (
    'name' => 'cat_purch_steer_c',
    'vname'=>'LBL_CAT_PURCH_STEER',
    'type' => 'bool',
    'default' => '',
);

$dictionary['Account']['fields']['cat_purch_supp_c'] = array (
    'name' => 'cat_purch_supp_c',
    'vname'=>'LBL_CAT_PURCH_SUPP',
    'type' => 'bool',
    'default' => '',
);

$dictionary['Account']['fields']['tech_total_opp_c'] = array (
    'name' => 'tech_total_opp_c',
    'vname'=>'LBL_TECH_TOTAL_OPP',
    'type' => 'varchar',
    'len' => 5,
    'function' => array('name'=>'getTechTotOpp', 'returns'=>'html', 'include'=>'custom/CustomTechTotOpp.php')
);

$dictionary['Account']['fields']['fleet_tot_opp_c'] = array (
    'name' => 'fleet_tot_opp_c',
    'vname'=>'LBL_FLEET_TOT_OPP',
    'type' => 'int',
    'function' => array('name'=>'getFleetTotOpp', 'returns'=>'html', 'include'=>'custom/CustomFleetTotOpp.php')
);

$dictionary['Account']['fields']['ford_pct_fleet_c'] = array (
    'name' => 'ford_pct_fleet_c',
    'vname'=>'LBL_FORD_PCT_FLEET',
    'type' => 'int',
    'len' => 3,
);

$dictionary['Account']['fields']['gm_pct_fleet_c'] = array (
    'name' => 'gm_pct_fleet_c',
    'vname'=>'LBL_GM_PCT_FLEET',
    'type' => 'int',
    'len' => 11,
);

$dictionary['Account']['fields']['mopar_pct_fleet_c'] = array (
    'name' => 'mopar_pct_fleet_c',
    'vname'=>'LBL_MOPAR_PCT_FLEET',
    'type' => 'int',
    'len' => 10,
);

$dictionary['Account']['fields']['other_fleet_c'] = array (
    'name' => 'other_fleet_c',
    'vname'=>'LBL_OTHER_PCT_FLEET',
    'type' => 'int',
    'len' => 11,
);
$dictionary['Account']['fields']['vehicle_age_c'] = array (
    'name' => 'vehicle_age_c',
    'vname'=>'LBL_VEHICLE_AGE',
    'type' => 'varchar',
    'len' => 25,
);

$dictionary['Account']['fields']['model_ford_c'] = array (
    'name' => 'model_ford_c',
    'vname'=>'LBL_MODEL_FORD',
    'type' => 'bool',
);

$dictionary['Account']['fields']['model_gm_c'] = array (
    'name' => 'model_gm_c',
    'vname'=>'LBL_MODEL_GM',
    'type' => 'bool',
);

$dictionary['Account']['fields']['model_mopar_c'] = array (
    'name' => 'model_mopar_c',
    'vname'=>'LBL_MODEL_MOPAR',
    'type' => 'bool',
);

$dictionary['Account']['fields']['model_other_c'] = array (
    'name' => 'model_other_c',
    'vname'=>'LBL_MODEL_OTHER',
    'type' => 'bool',
);

$dictionary['Account']['fields']['percent_okv_c'] = array (
    'name' => 'percent_okv_c',
    'vname'=>'LBL_PERCENT_OKV',
    'type' => 'varchar',
    'len' => 3,
);

$dictionary['Account']['fields']['fleet_parts_spent_c'] = array (
    'name' => 'fleet_parts_spent_c',
    'vname'=>'LBL_FLEET_PARTS_SPENT',
    'type' => 'int',
    'len' => 11,
);
$dictionary['Account']['fields']['credit_app_c'] = array (
    'name' => 'credit_app_c',
    'vname'=>'LBL_CREDIT_APP',
    'type' => 'bool',
    'default' => '',
);

$dictionary['Account']['fields']['copy_ship_c'] = array (
    'name' => 'copy_ship_c',
    'vname'=>'LBL_COPY_SHIP',
    'type' => 'bool',
    'default' => '',
//'function' => array('name'=>'copyBilling', 'returns'=>'html', 'include'=>'custom/CustomShipping.php')
);

$dictionary['Account']['fields']['credit_app_date_c'] = array (
    'name' => 'credit_app_date_c',
    'vname'=>'LBL_CREDIT_DATE',
    'type' => 'date',
);
$dictionary['Account']['fields']['num_okv_owned_c'] = array (
    'name' => 'num_okv_owned_c',
    'vname'=>'LBL_NUM_OKV_OWN',
    'type' => 'varchar',
    'len' => 25
//    'function' => array('name'=>'getFleetOwnOKVPerc', 'returns'=>'html', 'include'=>'custom/CustomFleetOKV.php')
);

$dictionary['Account']['fields']['per_okv_owned_c'] = array (
    'name' => 'per_okv_owned_c',
    'vname'=>'LBL_PER_OKV_OWN',
    'type' => 'varchar',
    'len' => 25,
  'function' => array('name'=>'getFleetOwnOKVPerc', 'returns'=>'html', 'include'=>'custom/CustomFleetOKV.php')
);

$dictionary['Account']['fields']['num_okv_leased_c'] = array (
    'name' => 'num_okv_leased_c',
    'vname'=>'LBL_NUM_OKV_LEASE',
    'type' => 'varchar',
    'len' => 25
//  'function' => array('name'=>'getFleetLeaseOKVPerc', 'returns'=>'html', 'include'=>'custom/CustomFleetLeaseOKV.php')
);

$dictionary['Account']['fields']['per_okv_leased_c'] = array (
    'name' => 'per_okv_leased_c',
    'vname'=>'LBL_PER_OKV_LEASE',
    'type' => 'varchar',
    'len' => 25,
    'function' => array('name'=>'getFleetLeaseOKVPerc', 'returns'=>'html', 'include'=>'custom/CustomFleetLeaseOKV.php')
);

$dictionary['Account']['fields']['dealer_make_c'] = array (
    'name' => 'dealer_make_c',
    'vname'=>'LBL_DEALER_MAKE',
    'type' => 'multienum',
    'isMultiSelect' => true,
    'options' => 'dealer_make_list'
);

$dictionary['Account']['fields']['noe_suppliers_c'] = array (
    'name' => 'noe_suppliers_c',
    'vname'=>'LBL_NOE_SUPPLIER',
    'type' => 'enum',
    'default' => '',
    'options' => 'primarysuppliers_list'
);

$dictionary['Account']['fields']['oe_suppliers_c'] = array (
    'name' => 'oe_suppliers_c',
    'vname'=>'LBL_OE_SUPPLIER',
    'type' => 'enum',
    'default' => '',
    'options' => 'primarysuppliers_list'
);

$dictionary['Account']['fields']['other_suppliers_c'] = array (
    'name' => 'other_suppliers_c',
    'vname'=>'LBL_OTHER_SUPPLIER',
    'type' => 'enum',
    'default' => '',
    'options' => 'primarysuppliers_list'
);
$dictionary['Account']['fields']['noe_suppliers1_c'] = array (
    'name' => 'noe_suppliers1_c',
    'vname'=>'LBL_NOE_SUPPLIER1',
    'type' => 'enum',
    'default' => '',
    'options' => 'primarysuppliers_list'
);

$dictionary['Account']['fields']['noe_suppliers2_c'] = array (
    'name' => 'noe_suppliers2_c',
    'vname'=>'LBL_NOE_SUPPLIER2',
    'type' => 'enum',
    'default' => '',
    'options' => 'primarysuppliers_list'
);

$dictionary['Account']['fields']['other_suppliers_c'] = array (
    'name' => 'other_suppliers_c',
    'vname'=>'LBL_OTHER_SUPPLIER',
    'type' => 'varchar',
    'len' => 25	   
);
$dictionary['Account']['fields']['other_oe_suppliers_c'] = array (
    'name' => 'other_oe_suppliers_c',
    'vname'=>'LBL_OTHER_OE_SUPPLIER',
    'type' => 'varchar',
    'len' => 25
);
$dictionary['Account']['fields']['oe_suppliers1_c'] = array (
    'name' => 'oe_suppliers1_c',
    'vname'=>'LBL_OE_SUPPLIER1',
    'type' => 'enum',
    'default' => '',
    'options' => 'primarysuppliers_list'
);

$dictionary['Account']['fields']['oe_suppliers2_c'] = array (
    'name' => 'oe_suppliers2_c',
    'vname'=>'LBL_OE_SUPPLIER2',
    'type' => 'enum',
    'default' => '',
    'options' => 'primarysuppliers_list'
);
$dictionary['Account']['fields']['oe_suppliers3_c'] = array (
    'name' => 'oe_suppliers3_c',
    'vname'=>'LBL_OE_SUPPLIER3',
    'type' => 'enum',
    'default' => '',
    'options' => 'primarysuppliers_list'
);
$dictionary['Account']['fields']['method_of_catalog_c'] = array (
    'name' =>'method_of_catalog_c',
    'vname'=>'LBL_METHOD_CATALOG',
    'type' => 'enum',
    'default' => '',
    'options' => 'method_catalog_list'
);
$dictionary['Account']['fields']['system_integration_c'] = array (
    'name' =>'system_integration_c',
    'vname'=>'LBL_SYSTEM_INTEGRATION',
    'type' => 'enum',
    'default' => '',
    'options' => 'blank_yes_no_list'
);
$dictionary['Account']['fields']['sys_int_timeframe_c'] = array (
    'name' =>'sys_int_timeframe_c',
    'vname'=>'LBL_TIMEFRAME',
    'type' => 'enum',
    'default' => '',
    'options' => 'timeframe_list'
);

$dictionary['Account']['fields']['facility_condition_c'] = array (
    'name' =>'facility_condition_c',
    'vname'=>'LBL_FAC_COND',
    'type' => 'enum',
    'default' => '',
    'options' => 'abc_list'
);
$dictionary['Account']['fields']['yearsinbusiness_c'] = array (
    'name' =>'yearsinbusiness_c',
    'vname'=>'LBL_YEARSINBUSINESS',
    'type' => 'enum',
    'default' => '',
    'options' => 'year_list'
);
$dictionary['Account']['fields']['delivery_expect_c'] = array (
    'name' =>'delivery_expect_c',
    'vname'=>'LBL_DELEXPECTS',
    'type' => 'enum',
    'default' => '',
    'options' => 'delopt_list'
);

$dictionary['Account']['fields']['supp_changes_c'] = array (
    'name' => 'supp_changes_c',
    'vname'=>'LBL_SUPP_CHANGES',
    'type' => 'varchar',
    'len' => 200
);
$dictionary['Account']['fields']['supp_meet_c'] = array (
    'name' => 'supp_meet_c',
    'vname'=>'LBL_SUPP_MEET',
    'type' => 'enum',
    'default' => '',
    'options' => 'yes_no_list'
);



?>