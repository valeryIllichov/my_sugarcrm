<?php
$viewdefs ['Accounts'] = 
array (
  'DetailView' => 
  array (
    'templateMeta' => 
    array (
      'form' => 
      array (
        'buttons' => 
        array (
          0 => 
          array (
            'customCode' => '{literal}<style>#back_to_bottom:hover{color: #ffffff !important;}</style>{/literal}<a href="#All_sp_tab" id="back_to_bottom" class="button" style="color:#000000; display:block; height:18px; line-height:21px; padding-left:7px; padding-right:7px; text-decoration:none;">Jump to bottom</a>',
          ),
          1 => 'EDIT',
          2 => 'DUPLICATE',
          3 => 'DELETE',
          4 => 'FIND_DUPLICATES',
          5 => 
          array (
            'customCode' => '<td style="padding-bottom: 2px;" align="left" NOWRAP>
{if !empty($fields.id.value)}<input title="Default Pricing" class="button" onclick=\'open_popup("Pricing", "800", "400", "&record={$fields.id.value}&module_name=Accounts&default_pricing=1", true, false,  {ldelim} "call_back_function":"set_return","form_name":"DetailView","field_to_name_array":[] {rdelim} ); return false;\' type="submit" value="Default Pricing">{/if}</td>',
          ),
          6 => 
          array (
            'customCode' => '<td style="padding-bottom: 2px;" align="left" NOWRAP>
{if !empty($fields.id.value)}<input title="Pricing Exceptions" class="button" onclick=\'open_popup("Pricing", "800", "400", "&record={$fields.id.value}&module_name=Accounts", true, false,  {ldelim} "call_back_function":"set_return","form_name":"DetailView","field_to_name_array":[] {rdelim} ); return false;\' type="submit" value="Pricing Exceptions">{/if}</td>',
          ),
            7 => 
          array (
            'customCode' => '<td style="padding-bottom: 2px;" align="left" NOWRAP>
{if !empty($fields.id.value)}<input title="Default and Exceptions Pricing" class="button" onclick=\'open_popup("Pricing", "800", "400", "&record={$fields.id.value}&module_name=Accounts&default_exception_pricing=1", true, false,  {ldelim} "call_back_function":"set_return","form_name":"DetailView","field_to_name_array":[] {rdelim} ); return false;\' type="submit" value="Default and Exceptions Pricing">{/if}</td>',
          ),
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
      'default' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'custno_c',
            'label' => 'LBL_CUSTNO',
          ),
          1 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'name',
            'label' => 'LBL_NAME',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'narrative_c',
            'label' => 'LBL_NARRATIVE',
          ),
          1 => NULL,
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'billing_address_street',
            'label' => 'LBL_BILLING_ADDRESS',
            'type' => 'address',
            'displayParams' => 
            array (
              'key' => 'billing',
            ),
          ),
          1 => 
          array (
            'name' => 'shipping_address_street',
            'label' => 'LBL_SHIPPING_ADDRESS',
            'type' => 'address',
            'displayParams' => 
            array (
              'key' => 'shipping',
            ),
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'phone_office',
            'label' => 'LBL_PHONE_OFFICE',
          ),
          1 => 
          array (
            'name' => 'fulldealertype_c',
            'label' => 'LBL_FULLDEALERTYPE',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'phone_fax',
            'label' => 'LBL_FAX',
          ),
          1 => 
          array (
            'name' => 'dealertype_c',
            'label' => 'LBL_DEALERTYPE',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'region_c',
            'label' => 'LBL_REGION',
          ),
          1 => 
          array (
            'name' => 'date_modified',
            'label' => 'LBL_DATE_MODIFIED',
          ),
        ),
        7 => 
        array (
          0 => 
          array (
            'name' => 'location_c',
            'label' => 'LBL_LOCATION',
          ),
          1 => 
          array (
            'name' => 'stocklocation_c',
            'label' => 'LBL_STOCKLOCATION',
          ),
        ),
        8 => 
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
      ),
      'lbl_panel3' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'mtd_projected_c',
            'label' => 'LBL_MTD_PROJECTED',
          ),
          1 => 
          array (
            'name' => 'ytd_projected_c',
            'label' => 'LBL_YTD_PROJECTED',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'mtd_sales_c',
            'label' => 'LBL_MTD_SALES',
          ),
          1 => 
          array (
            'name' => 'ytd_sales_c',
            'label' => 'LBL_YTD_SALES',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'mtd_gppct_c',
            'label' => 'LBL_MTD_GPPCT',
          ),
          1 => 
          array (
            'name' => 'ytd_gppct_c',
            'label' => 'LBL_YTD_GPPCT',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'mtd_gp_c',
            'label' => 'LBL_MTD_GP',
          ),
          1 => 
          array (
            'name' => 'ytd_gp_c',
            'label' => 'LBL_YTD_GP',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'mtd_budget_sales_c',
            'label' => 'LBL_MTD_BUDGET_SALES',
          ),
          1 => 
          array (
            'name' => 'ytd_budget_sales_c',
            'label' => 'LBL_YTD_BUDGET_SALES',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'mtd_budget_gp_c',
            'label' => 'LBL_MTD_BUDGET_GP',
          ),
          1 => 
          array (
            'name' => 'ytd_budget_gp_c',
            'label' => 'LBL_YTD_BUDGET_GP',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'mtd_budget_gppct_c',
            'label' => 'LBL_MTD_BUDGET_GPPCT',
          ),
          1 => 
          array (
            'name' => 'ytd_budget_gppct_c',
            'label' => 'LBL_YTD_BUDGET_GPPCT',
          ),
        ),
        7 => 
        array (
          0 => 
          array (
            'name' => 'mly_sales_c',
            'label' => 'LBL_MLY_SALES',
          ),
          1 => 
          array (
            'name' => 'ly_sales_c',
            'label' => 'LBL_LY_SALES',
          ),
        ),
        8 => 
        array (
          0 => 
          array (
            'name' => 'ly_gppct_c',
            'label' => 'LBL_LY_GPPCT',
          ),
          1 => 
          array (
            'name' => 'ly_gp_c',
            'label' => 'LBL_LY_GP',
          ),
        ),
      ),
      'lbl_panel2' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'termscode_c',
            'label' => 'LBL_TERMSCODE',
          ),
          1 => 
          array (
            'name' => 'creditcode_c',
            'label' => 'LBL_CREDITCODE',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'last_payment_date_c',
            'label' => 'LBL_LAST_PAYMENT_DATE',
          ),
          1 => 
          array (
            'name' => 'last_payment_amt_c',
            'label' => 'LBL_LAST_PAYMENT_AMT',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'avg_days_c',
            'label' => 'LBL_AVG_DAYS',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'aarbal_c',
            'label' => 'LBL_AARBAL',
          ),
          1 => 
          array (
            'name' => 'arcurrent_c',
            'label' => 'LBL_ARCURRENT',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'arfuture_c',
            'label' => 'LBL_ARFUTURE',
          ),
          1 => 
          array (
            'name' => 'ar30_60_c',
            'label' => 'LBL_AR30_60',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'ar60_90_c',
            'label' => 'LBL_AR60_90',
          ),
          1 => 
          array (
            'name' => 'over_90_c',
            'label' => 'LBL_OVER_90',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'creditlimit_c',
            'label' => 'LBL_CREDITLIMIT',
          ),
        ),
      ),
      'lbl_panel9' => 
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
      'lbl_panel13' => 
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
          1 => 
          array (
            'name' => 'gm_vehicle_count_c',
            'label' => 'LBL_GM_VEHICLE_COUNT',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'chrysler_vehicle_count_c',
            'label' => 'LBL_CHRYSLER_VEHICLE_COUNT',
          ),
          1 => 
          array (
            'name' => 'other_vehicle_count_c',
            'label' => 'LBL_OTHER_VEHICLE_COUNT',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'dealer_prev_maint_c',
            'label' => 'LBL_DEALER_PREV_MAINT',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'dealer_make_c',
            'label' => 'LBL_DEALER_MAKE',
          ),
          1 => NULL,
        ),
      ),
      'lbl_panel11' => 
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
      'lbl_panel12' => 
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
