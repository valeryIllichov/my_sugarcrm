<?php
/*********************************************************************************
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004 - 2009 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/
$vardefs = array (
  'fields' => 
  array (
    'custid' => 
    array (
      'required' => '1',
      'name' => 'custid',
      'vname' => 'LBL_CUSTID',
      'type' => 'int',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => '11',
      'disable_num_format' => '',
    ),
    'custtype' => 
    array (
      'required' => false,
      'name' => 'custtype',
      'vname' => 'LBL_CUSTTYPE',
      'type' => 'varchar',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => '10',
    ),
    'region' => 
    array (
      'required' => false,
      'name' => 'region',
      'vname' => 'LBL_REGION',
      'type' => 'int',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => '11',
      'disable_num_format' => '',
    ),
    'loc' => 
    array (
      'required' => false,
      'name' => 'loc',
      'vname' => 'LBL_LOC',
      'type' => 'int',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => '11',
      'disable_num_format' => '',
    ),
    'slsm' => 
    array (
      'required' => false,
      'name' => 'slsm',
      'vname' => 'LBL_SLSM',
      'type' => 'int',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => '11',
      'disable_num_format' => '',
    ),
    'todays_orders' => 
    array (
      'required' => false,
      'name' => 'todays_orders',
      'vname' => 'LBL_TODAYS_ORDERS',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'currency_id' => 
    array (
      'required' => false,
      'name' => 'currency_id',
      'vname' => 'LBL_CURRENCY',
      'type' => 'id',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => 0,
      'audited' => 0,
      'reportable' => 0,
      'len' => 36,
      'studio' => 'visible',
      'function' => 
      array (
        'name' => 'getCurrencyDropDown',
        'returns' => 'html',
      ),
    ),
    'pending_orders' => 
    array (
      'required' => false,
      'name' => 'pending_orders',
      'vname' => 'LBL_PENDING_ORDERS',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'todays_credits' => 
    array (
      'required' => false,
      'name' => 'todays_credits',
      'vname' => 'LBL_TODAYS_CREDITS',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'pending_credits' => 
    array (
      'required' => false,
      'name' => 'pending_credits',
      'vname' => 'LBL_PENDING_CREDITS',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'mtd_projected' => 
    array (
      'required' => false,
      'name' => 'mtd_projected',
      'vname' => 'LBL_MTD_PROJECTED',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ytd_projected' => 
    array (
      'required' => false,
      'name' => 'ytd_projected',
      'vname' => 'LBL_YTD_PROJECTED',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'mtd_sales' => 
    array (
      'required' => false,
      'name' => 'mtd_sales',
      'vname' => 'LBL_MTD_SALES',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'mtd_gp' => 
    array (
      'required' => false,
      'name' => 'mtd_gp',
      'vname' => 'LBL_MTD_GP',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'mtd_frt' => 
    array (
      'required' => false,
      'name' => 'mtd_frt',
      'vname' => 'LBL_MTD_FRT',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'mtd_cores' => 
    array (
      'required' => false,
      'name' => 'mtd_cores',
      'vname' => 'LBL_MTD_CORES',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ytd_sales' => 
    array (
      'required' => false,
      'name' => 'ytd_sales',
      'vname' => 'LBL_YTD_SALES',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ytd_gp' => 
    array (
      'required' => false,
      'name' => 'ytd_gp',
      'vname' => 'LBL_YTD_GP',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ly_sales' => 
    array (
      'required' => false,
      'name' => 'ly_sales',
      'vname' => 'LBL_LY_SALES',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ly_gp' => 
    array (
      'required' => false,
      'name' => 'ly_gp',
      'vname' => 'LBL_LY_GP',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'mtd_projected_noem' => 
    array (
      'required' => false,
      'name' => 'mtd_projected_noem',
      'vname' => 'LBL_MTD_PROJECTED_NOEM',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ytd_projected_noem' => 
    array (
      'required' => false,
      'name' => 'ytd_projected_noem',
      'vname' => 'LBL_YTD_PROJECTED_NOEM',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'mtd_budget_sales' => 
    array (
      'required' => false,
      'name' => 'mtd_budget_sales',
      'vname' => 'LBL_MTD_BUDGET_SALES',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'mtd_budget_gp' => 
    array (
      'required' => false,
      'name' => 'mtd_budget_gp',
      'vname' => 'LBL_MTD_BUDGET_GP',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'mtd_budget_noem_sales' => 
    array (
      'required' => false,
      'name' => 'mtd_budget_noem_sales',
      'vname' => 'LBL_MTD_BUDGET_NOEM_SALES',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'mtd_budget_noem_gp' => 
    array (
      'required' => false,
      'name' => 'mtd_budget_noem_gp',
      'vname' => 'LBL_MTD_BUDGET_NOEM_GP',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ytd_budget_sales' => 
    array (
      'required' => false,
      'name' => 'ytd_budget_sales',
      'vname' => 'LBL_YTD_BUDGET_SALES',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ytd_budget_gp' => 
    array (
      'required' => false,
      'name' => 'ytd_budget_gp',
      'vname' => 'LBL_YTD_BUDGET_GP',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ytd_budget_noem_sales' => 
    array (
      'required' => false,
      'name' => 'ytd_budget_noem_sales',
      'vname' => 'LBL_YTD_BUDGET_NOEM_SALES',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ytd_budget_noem_gp' => 
    array (
      'required' => false,
      'name' => 'ytd_budget_noem_gp',
      'vname' => 'LBL_YTD_BUDGET_NOEM_GP',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'lytd_sales' => 
    array (
      'required' => false,
      'name' => 'lytd_sales',
      'vname' => 'LBL_LYTD_SALES',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'mtd_projected_undercar' => 
    array (
      'required' => false,
      'name' => 'mtd_projected_undercar',
      'vname' => 'LBL_MTD_PROJECTED_UNDERCAR',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'mtd_budget_undercar_sales' => 
    array (
      'required' => false,
      'name' => 'mtd_budget_undercar_sales',
      'vname' => 'LBL_MTD_BUDGET_UNDERCAR_SALES',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'mtd_budget_undercar_gp' => 
    array (
      'required' => false,
      'name' => 'mtd_budget_undercar_gp',
      'vname' => 'LBL_MTD_BUDGET_UNDERCAR_GP',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ytd_projected_undercar' => 
    array (
      'required' => false,
      'name' => 'ytd_projected_undercar',
      'vname' => 'LBL_YTD_PROJECTED_UNDERCAR',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ytd_budget_undercar_sales' => 
    array (
      'required' => false,
      'name' => 'ytd_budget_undercar_sales',
      'vname' => 'LBL_YTD_BUDGET_UNDERCAR_SALES',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ytd_budget_undercar_gp' => 
    array (
      'required' => false,
      'name' => 'ytd_budget_undercar_gp',
      'vname' => 'LBL_YTD_BUDGET_UNDERCAR_GP',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'mly_sls' => 
    array (
      'required' => false,
      'name' => 'mly_sls',
      'vname' => 'LBL_MLY_SLS',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'mtd_sls_noem' => 
    array (
      'required' => false,
      'name' => 'mtd_sls_noem',
      'vname' => 'LBL_MTD_SLS_NOEM',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'mtd_gp_noem' => 
    array (
      'required' => false,
      'name' => 'mtd_gp_noem',
      'vname' => 'LBL_MTD_GP_NOEM',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ytd_sls_noem' => 
    array (
      'required' => false,
      'name' => 'ytd_sls_noem',
      'vname' => 'LBL_YTD_SLS_NOEM',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ytd_gp_noem' => 
    array (
      'required' => false,
      'name' => 'ytd_gp_noem',
      'vname' => 'LBL_YTD_GP_NOEM',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ly_sls_noem' => 
    array (
      'required' => false,
      'name' => 'ly_sls_noem',
      'vname' => 'LBL_LY_SLS_NOEM',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ly_gp_noem' => 
    array (
      'required' => false,
      'name' => 'ly_gp_noem',
      'vname' => 'LBL_LY_GP_NOEM',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'mtd_sls_undercar' => 
    array (
      'required' => false,
      'name' => 'mtd_sls_undercar',
      'vname' => 'LBL_MTD_SLS_UNDERCAR',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'mtd_gp_undercar' => 
    array (
      'required' => false,
      'name' => 'mtd_gp_undercar',
      'vname' => 'LBL_MTD_GP_UNDERCAR',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ytd_sls_undercar' => 
    array (
      'required' => false,
      'name' => 'ytd_sls_undercar',
      'vname' => 'LBL_YTD_SLS_UNDERCAR',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ytd_gp_undercar' => 
    array (
      'required' => false,
      'name' => 'ytd_gp_undercar',
      'vname' => 'LBL_YTD_GP_UNDERCAR',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ly_sls_undercar' => 
    array (
      'required' => false,
      'name' => 'ly_sls_undercar',
      'vname' => 'LBL_LY_SLS_UNDERCAR',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ly_gp_undercar' => 
    array (
      'required' => false,
      'name' => 'ly_gp_undercar',
      'vname' => 'LBL_LY_GP_UNDERCAR',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'lm_sales' => 
    array (
      'required' => false,
      'name' => 'lm_sales',
      'vname' => 'LBL_LM_SALES',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'false',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'lm_gp' => 
    array (
      'required' => false,
      'name' => 'lm_gp',
      'vname' => 'LBL_LM_GP',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'false',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'lytm_sales' => 
    array (
      'required' => false,
      'name' => 'lytm_sales',
      'vname' => 'LBL_LYTM_SALES',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'false',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'lytm_gp' => 
    array (
      'required' => false,
      'name' => 'lytm_gp',
      'vname' => 'LBL_LYTM_GP',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'false',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'ytd_projected_gp' => 
    array (
      'required' => false,
      'name' => 'ytd_projected_gp',
      'vname' => 'LBL_YTD_PROJECTED_GP',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'false',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
    'lytd_gp' => 
    array (
      'required' => false,
      'name' => 'lytd_gp',
      'vname' => 'LBL_LYTD_GP',
      'type' => 'currency',
      'massupdate' => 0,
      'comments' => '',
      'help' => '',
      'importable' => 'true',
      'duplicate_merge' => 'disabled',
      'duplicate_merge_dom_value' => '0',
      'audited' => 0,
      'reportable' => 0,
      'len' => 26,
    ),
  ),
  'relationships' => 
  array (
  ),
);
?>