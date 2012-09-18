<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
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
 */



global $current_user;

$dashletData['FMPOpportunitiesDashlet']['searchFields'] = array('date_entered'     => array('default' => ''),
        'date_modified'    => array('default' => ''),
        'opportunity_type' => array('default' => ''),
        'sales_stage'      => array('default' =>
                array('Stage1', 'Stage 2', 'Stage 3', 'Stage 4 ', 'Stage 5', 'Closed Won', 'Closed Lost')),

        'assigned_user_id' => array('type'    => 'assigned_user_name',
                'default' => $current_user->name));
                

$dashletData['FMPOpportunitiesDashlet']['columns'] = array('name' => array('width'   => '40',
                'label'   => 'LBL_OPPORTUNITY_NAME',
                'link'    => true,
                'default' => true
        ),
    'account_custno_c' => array('width'  => '29',
                'label'   => 'CustNo',
                'default' => true,
                'link' => false,
                'id' => 'account_id',
                'ACLTag' => 'ACCOUNT',
                'orderBy' => 'accounts.custno_c',
        ),
        'account_name' => array('width'  => '60',
                'label'   => 'LBL_ACCOUNT_NAME',
                'default' => true,
                'link' => false,
                'id' => 'account_id',
                'ACLTag' => 'ACCOUNT'),
        
        'amount_usdollar' => array('width'   => '15',
                'label'   => ' Est Annual Sls',
                'default' => false,
        //'currency_format' => true
        ),
        'month_sales' => array('width'   => '15',
                'label'   => 'Est Monthly Sls',
                'default' => true,
                'orderBy' => 'opportunities.amount_usdollariiii12'
        ),

        'prev_12_mo' => array('width'   => '15',
                'label'   => 'Prev 12-mo avg',
                'default' => false,
                'orderBy' => 'opportunities_cstm.previousavg_sales_c'
        ),

        'rolling_sales' => array('width'   => '15',
                'label'   => 'Rolling Sls',
                'default' => false,
                'orderBy' => 'opportunities_cstm.rolling_sales_c'
        ),
        'mtd_sales' => array('width'   => '15',
                'label'   => 'MTD Sls',
                'default' => true,
                'orderBy' => 'opportunities_cstm.mtd_sales_c',
        ),
        'ytd_sales' => array('width'   => '15',
                'label'   => 'YTD Sls',
                'default' => false,
                'orderBy' => 'opportunities_cstm.ytd_sales_c',
        ),
        'probability' => array('width'   => '15',
                'label'   => 'LBL_PROBABILITY',
                //'label'   => 'Pct.',
                'default'        => false,
        ),
        'var_month_sls' => array('width'   => '15',
                'label'   => 'MTD Sls - Est Monthly Sls',
                'default' => true,
                'orderBy' => 'opportunities_cstm.mtd_sales_c____opportunities.amount_usdollariiii12'
        ),
        'sales_stage' => array('width'   => '15',
                'label'   => 'LBL_SALES_STAGE',
                'default' => true),
        'date_closed' => array('width'   => '15',
                //'label'   => 'LBL_DATE_CLOSED',
                'label'   => 'Exp Close Date',
                'default'        => false,
//                                                                                 'defaultOrderColumn' => array('sortOrder' => 'ASC')
        ),
        'user' => array('width'   => '15',
                'label'   => 'User',
                'default' => true,
//                                                                          'orderBy' => 'opportunities.assigned_user_id'
                'orderBy' => 'users.user_name'
        ),
        
        'var_annual_sls' => array('width'   => '15',
                'label'   => 'YTD Sls - Est Annual Sls',
                'default' => false,
                'orderBy' => 'opportunities_cstm.ytd_sales_c____opportunities.amount_usdollar'
        ),
        'var_month_gp' => array('width'   => '15',
                'label'   => 'MTD GP$ - Est Monthly GP$',
                'default' => false,
                'orderBy' => 'opportunities_cstm.mtd_gp_c____y___opportunities_cstm.mtd_sales_c____opportunities.amount_usdollariiii12___yhhhhopportunities.gp_perciiii100'
        ),
        'var_annual_gp' => array('width'   => '15',
                'label'   => 'YTD GP$ - Est Annual GP$',
                'default' => false,
                'orderBy' => 'opportunities_cstm.ytd_gp_c____y___opportunities_cstm.ytd_sales_c____opportunities.amount_usdollar___yhhhhopportunities.gp_perciiii100'
        ),
        'var_month_prc' => array('width'   => '15',
                'label'   => 'MTD GP% - Est Monthly GP%',
                'default' => false,
                'orderBy' => 'opportunities_cstm.mtd_gp_percent_c'
        ),
        'var_annual_prc' => array('width'   => '15',
                'label'   => 'YTD GP% - Est Annual GP%',
                'default' => false,
                'orderBy' => 'opportunities_cstm.ytd_gp_percent_c'
        ),
//                                                          'opportunity_type' => array('width'   => '15',
//                                                                                      'label'   => 'LBL_TYPE'),
//                                                          'lead_source' => array('width'   => '15',
//                                                                                 'label'   => 'LBL_LEAD_SOURCE'),
//
//                                                          'date_entered' => array('width'   => '15',
//                                                                                  'label'   => 'LBL_DATE_ENTERED'),
//                                                          'date_modified' => array('width'   => '15',
//                                                                                   'label'   => 'LBL_DATE_MODIFIED'),
//                                                          'created_by' => array('width'   => '8',
//                                                                                'label'   => 'LBL_CREATED'),
//                                                          'assigned_user_name' => array('width'   => '8',
//                                                                                        'label'   => 'LBL_LIST_ASSIGNED_USER'),
//														  'next_step' => array('width' => '10',
//														        'label' => 'LBL_NEXT_STEP'),                                                                         




);
?>
