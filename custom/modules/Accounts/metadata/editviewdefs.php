<?php
$viewdefs ['Accounts'] = 
array (
  'EditView' => 
  array (
    'templateMeta' => 
    array (
      'form' => 
      array (
        'buttons' => 
        array (
          0 => 'SAVE',
          1 => 'CANCEL',
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
      'includes' => 
      array (
        0 => 
        array (
          'file' => 'modules/Accounts/Account.js',
        ),
      ),
    ),
    'panels' => 
    array (
      'lbl_panel10' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'custno_c',
            'label' => 'LBL_CUSTNO',
          ),
          1 => 
          array (
            'name' => 'dealertype_c',
            'label' => 'LBL_DEALERTYPE',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'name',
            'label' => 'LBL_NAME',
            'displayParams' => 
            array (
              'required' => true,
            ),
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'billing_address_street',
            'label' => 'LBL_BILLING_ADDRESS_STREET',
          ),
          1 => 
          array (
            'name' => 'shipping_address_street',
            'label' => 'LBL_SHIPPING_ADDRESS_STREET',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'billing_address_city',
            'label' => 'LBL_BILLING_ADDRESS_CITY',
          ),
          1 => 
          array (
            'name' => 'shipping_address_city',
            'label' => 'LBL_SHIPPING_ADDRESS_CITY',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'billing_address_state',
            'label' => 'LBL_BILLING_ADDRESS_STATE',
          ),
          1 => 
          array (
            'name' => 'shipping_address_state',
            'label' => 'LBL_SHIPPING_ADDRESS_STATE',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'billing_address_postalcode',
            'label' => 'LBL_BILLING_ADDRESS_POSTALCODE',
          ),
          1 => 
          array (
            'name' => 'shipping_address_postalcode',
            'label' => 'LBL_SHIPPING_ADDRESS_POSTALCODE',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'copy_ship_c',
            'label' => 'LBL_COPY_SHIP',
          ),
          1 => NULL,
        ),
        7 => 
        array (
          0 => 
          array (
            'name' => 'phone_office',
            'label' => 'LBL_PHONE_OFFICE',
          ),
          1 => 
          array (
            'name' => 'phone_alternate',
            'label' => 'LBL_PHONE_ALT',
          ),
        ),
        8 => 
        array (
          0 => 
          array (
            'name' => 'credit_app_c',
            'label' => 'LBL_CREDIT_APP',
          ),
          1 => NULL,
        ),
        9 => 
        array (
          0 => 
          array (
            'name' => 'credit_app_date_c',
            'label' => 'LBL_CREDIT_DATE',
          ),
          1 => NULL,
        ),
      ),
      'lbl_account_information' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'narrative_c',
            'label' => 'LBL_NARRATIVE',
          ),
          1 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'slsm_c',
            'label' => 'LBL_SLSM',
          ),
          1 => 
          array (
            'name' => 'assigned_user_name',
            'label' => 'LBL_ASSIGNED_TO',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'region_c',
            'label' => 'LBL_REGION',
          ),
          1 => 
          array (
            'name' => 'location_c',
            'label' => 'LBL_LOCATION',
          ),
        ),
      ),
      'lbl_panel6' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'hotbuttons_c',
            'label' => 'LBL_HOTBUTTONS',
          ),
          1 => 
          array (
            'name' => 'internet_c',
            'label' => 'LBL_INTERNET',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'facility_condition_c',
            'label' => 'LBL_FAC_COND',
          ),
          1 => 
          array (
            'name' => 'fmpconnect_c',
            'label' => 'LBL_FMPCONNECT',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'yearsinbusiness_c',
            'label' => 'LBL_YEARSINBUSINESS',
          ),
          1 => 
          array (
            'name' => 'nexpart_c',
            'label' => 'LBL_NEXPART',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'employees',
            'label' => 'LBL_EMPLOYEES',
          ),
          1 => 
          array (
            'name' => 'bays_c',
            'label' => 'LBL_BAYS',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'larger_group_c',
            'label' => 'LBL_LARGER_GROUP',
          ),
          1 => 
          array (
            'name' => 'larger_group_name_c',
            'label' => 'LBL_LARGER_GROUP_NAME',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'noe_suppliers_c',
            'label' => 'LBL_NOE_SUPPLIER',
          ),
          1 => 
          array (
            'name' => 'oe_suppliers1_c',
            'label' => 'LBL_OE_SUPPLIER1',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'noe_suppliers1_c',
            'label' => 'LBL_NOE_SUPPLIER1',
          ),
          1 => 
          array (
            'name' => 'oe_suppliers2_c',
            'label' => 'LBL_OE_SUPPLIER2',
          ),
        ),
        7 => 
        array (
          0 => 
          array (
            'name' => 'noe_suppliers2_c',
            'label' => 'LBL_NOE_SUPPLIER2',
          ),
          1 => 
          array (
            'name' => 'oe_suppliers3_c',
            'label' => 'LBL_OE_SUPPLIER3',
          ),
        ),
        8 => 
        array (
          0 => 
          array (
            'name' => 'other_suppliers_c',
            'label' => 'LBL_OTHER_SUPPLIER',
          ),
          1 => 
          array (
            'name' => 'other_oe_suppliers_c',
            'label' => 'LBL_OTHER_OE_SUPPLIER',
          ),
        ),
        9 => 
        array (
          0 => 
          array (
            'name' => 'delivery_expect_c',
            'label' => 'LBL_DELEXPECTS',
          ),
          1 => 
          array (
            'name' => 'motorcraft_rewards_c',
            'label' => 'LBL_MOTOR_REWARDS',
          ),
        ),
        10 => 
        array (
          0 => 
          array (
            'name' => 'supp_meet_c',
            'label' => 'LBL_SUPP_MEET',
          ),
          1 => 
          array (
            'name' => 'acdelco_rewards_c',
            'label' => 'LBL_ACDELCO_REWARDS',
          ),
        ),
        11 => 
        array (
          0 => 
          array (
            'name' => 'supp_changes_c',
            'label' => 'LBL_SUPP_CHANGES',
          ),
          1 => 
          array (
            'name' => 'fmp_visa_c',
            'label' => 'LBL_FMP_VISA',
          ),
        ),
        12 => 
        array (
          0 => 
          array (
            'name' => 'additional_notes_c',
            'label' => 'LBL_ADDITIONAL_NOTES',
          ),
        ),
      ),
      'lbl_panel1' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'dealer_mgmt_sys_c',
            'label' => 'LBL_DEALER_MGMT_SYS',
          ),
          1 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'ford_vehicle_count_c',
            'label' => 'LBL_FORD_VEHICLE_COUNT',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'chrysler_vehicle_count_c',
            'label' => 'LBL_CHRYSLER_VEHICLE_COUNT',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'gm_vehicle_count_c',
            'label' => 'LBL_GM_VEHICLE_COUNT',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'other_vehicle_count_c',
            'label' => 'LBL_OTHER_VEHICLE_COUNT',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'dealer_prev_maint_c',
            'label' => 'LBL_DEALER_PREV_MAINT',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'dealer_make_c',
            'label' => 'LBL_DEALER_MAKE',
          ),
          1 => NULL,
        ),
      ),
      'lbl_panel5' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'fleet_num_vehicles_c',
            'label' => 'LBL_FLEET_NUM_VEHICLES',
          ),
          1 => 
          array (
            'name' => 'fleet_parts_spent_c',
            'label' => 'LBL_FLEET_PARTS_SPENT',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'percent_okv_c',
            'label' => 'LBL_PERCENT_OKV',
          ),
          1 => 
          array (
            'name' => 'fleet_tot_opp_c',
            'label' => 'LBL_FLEET_TOT_OPP',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'own_lease_c',
            'label' => 'LBL_OWN_LEASE',
          ),
          1 => 
          array (
            'name' => 'vehicle_age_c',
            'label' => 'LBL_VEHICLE_AGE',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'num_okv_owned_c',
            'label' => 'LBL_NUM_OKV_OWN',
          ),
          1 => 
          array (
            'name' => 'per_okv_owned_c',
            'label' => 'LBL_PER_OKV_OWN',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'num_okv_leased_c',
            'label' => 'LBL_NUM_OKV_LEASE',
          ),
          1 => 
          array (
            'name' => 'per_okv_leased_c',
            'label' => 'LBL_PER_OKV_LEASE',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'fleetmaintenanceoptions_c',
            'label' => 'LBL_FLEET_MAINT_OPT',
          ),
          1 => 
          array (
            'name' => 'rfp_c',
            'label' => 'LBL_RFP',
          ),
        ),
      ),
      'lbl_panel4' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'shop_mgmt_sys_c',
            'label' => 'LBL_SHOP_MGMT_SYS',
          ),
          1 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'technicians_c',
            'label' => 'LBL_TECHNICIANS',
          ),
          1 => 
          array (
            'name' => 'tech_total_opp_c',
            'label' => 'LBL_TECH_TOTAL_OPP',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'affiliated_banner_c',
            'label' => 'LBL_AFFILIATED_BANNER',
          ),
          1 => NULL,
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'banner_c',
            'label' => 'LBL_BANNER',
          ),
          1 => 
          array (
            'name' => 'other_banner_c',
            'label' => 'LBL_OTHER',
          ),
        ),
      ),
      'lbl_panel7' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'model_ford_c',
            'label' => 'LBL_MODEL_FORD',
          ),
          1 => 
          array (
            'name' => 'ford_pct_fleet_c',
            'label' => 'LBL_FORD_PCT_FLEET',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'model_gm_c',
            'label' => 'LBL_MODEL_GM',
          ),
          1 => 
          array (
            'name' => 'gm_pct_fleet_c',
            'label' => 'LBL_GM_PCT_FLEET',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'model_mopar_c',
            'label' => 'LBL_MODEL_MOPAR',
          ),
          1 => 
          array (
            'name' => 'mopar_pct_fleet_c',
            'label' => 'LBL_MOPAR_PCT_FLEET',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'model_other_c',
            'label' => 'LBL_MODEL_OTHER',
          ),
          1 => 
          array (
            'name' => 'other_fleet_c',
            'label' => 'LBL_OTHER_PCT_FLEET',
          ),
        ),
      ),
      'lbl_panel8' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'cat_purch_filters_c',
            'label' => 'LBL_CAT_PURCH_FILTERS',
          ),
          1 => 
          array (
            'name' => 'cat_purch_batt_c',
            'label' => 'LBL_CAT_PURCH_BATT',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'cat_purch_brake_c',
            'label' => 'LBL_CAT_PURCH_BRAKE',
          ),
          1 => 
          array (
            'name' => 'cat_purch_elec_c',
            'label' => 'LBL_CAT_PURCH_ELEC',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'cat_purch_fuel_c',
            'label' => 'LBL_CAT_PURCH_FUEL',
          ),
          1 => 
          array (
            'name' => 'cat_purch_heat_c',
            'label' => 'LBL_CAT_PURCH_HEAT',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'cat_purch_chassis_c',
            'label' => 'LBL_CAT_PURCH_CHASSIS',
          ),
          1 => 
          array (
            'name' => 'cat_purch_steer_c',
            'label' => 'LBL_CAT_PURCH_STEER',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'cat_purch_ride_c',
            'label' => 'LBL_CAT_PURCH_RIDE',
          ),
          1 => 
          array (
            'name' => 'cat_purch_emis_c',
            'label' => 'LBL_CAT_PURCH_EMIS',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'cat_purch_supp_c',
            'label' => 'LBL_CAT_PURCH_SUPP',
          ),
          1 => NULL,
        ),
      ),
      'lbl_panel9' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'jobber_num_stores_c',
            'label' => 'LBL_JOBBER_NUM_STORES',
          ),
          1 => 
          array (
            'name' => 'jobber_num_trucks_c',
            'label' => 'LBL_JOBBER_NUM_TRUCKS',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'jobber_retailorwholesale_c',
            'label' => 'LBL_JOBBER_RETAILORWHOLESALE',
          ),
          1 => NULL,
        ),
      ),
    ),
  ),
);
?>