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



$dashletData['FMPPipelineBySalesStageDashlet']['searchFields'] = array(

        'pbss_date_start' => array(
                'name'  => 'pbss_date_start',
                'vname' => 'LBL_DATE_START',
                'type'  => 'datepicker',
        ),
        'pbss_date_end' => array(
                'name'  => 'pbss_date_end',
                'vname' => 'LBL_DATE_END',
                'type'  => 'datepicker',
        ),
        'pbss_chart_view' => array(
                'name'  => 'chart_view',
                'vname' => 'Chart View',
                'type'  => 'enum',
        ),
        'pbss_chart_type' => array(
                'name'  => 'chart_type',
                'vname' => 'Chart Type',
                'type'  => 'enum',
        ),
        'pbss_sales_stages' => array(
                'name'  => 'pbss_sales_stages',
                'vname' => 'LBL_SALES_STAGES',
                'type'  => 'enum',
        ),
        'pbss_estimated_annualized_sales' => array(
                'name'  => 'estimated_annualized_sales',
                'vname' => 'Estimated Annualized Sales',
                'type'  => 'enum',
        ),
        'pbss_estimated_monthly_sales' => array(
                'name'  => 'estimated_monthly_sales',
                'vname' => 'Estimated Monthly Sales',
                'type'  => 'enum',
        ),
        'pbss_probability' => array(
                'name'  => 'probability',
                'vname' => 'Probability %',
                'type'  => 'enum',
        ),
        'pbss_company' => array(
                'name'  => 'company',
                'vname' => 'Company',
                'type'  => 'enum',
        ),
        'pbss_opp_type' => array(
            'name'   => 'opp_type',
            'vname'   => 'LBL_TYPE',
            'type'  => 'enum',
        )
//        'pbss_account' => array(
//                'name'  => 'parent_name_custno_c',
//                'vname' => 'Account',
//                'type'  => 'text',
//                'quicksearch' => 'enabled',
//        ),
);
?>
