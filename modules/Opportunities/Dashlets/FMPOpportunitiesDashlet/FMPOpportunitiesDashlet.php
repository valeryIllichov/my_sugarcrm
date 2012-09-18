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




require_once('include/Dashlets/DashletGeneric.php');
require_once('modules/Opportunities/Opportunity.php');
require_once('include/utils.php');
require_once('modules/ZuckerReportParameter/fmp.class.param.slsm.php');
require_once('modules/ZuckerReportParameter/fmp.class.param.regloc.php');
require_once('include/database/PearDatabase.php');

class FMPOpportunitiesDashlet extends DashletGeneric {
    var $fmpo_date_start =  '01/01/2011';
    var $fmpo_date_end = '12/31/2011';
    var $probabilities_array = array();
    var $company_array = array();
    var $buttons;
    var $where_query_for_total_row = '';

    function FMPOpportunitiesDashlet($id, $def = null) {
        global $current_user, $app_strings, $dashletData;
        require('modules/Opportunities/Dashlets/FMPOpportunitiesDashlet/FMPOpportunitiesDashlet.data.php');



        parent::DashletGeneric($id, $def);

        //pr($def);
        if(isset($def)) {
            if(!empty($def['displayRows'])) {$this->displayRows = $def['displayRows'];} else { $this->displayRows = 20;}
        }
        $this->displayTpl = "modules/Opportunities/Dashlets/FMPOpportunitiesDashlet/FMPDashletGenericDisplay.tpl";
        $this->fmpo_date_start =  date('01/01/Y');
        $this->fmpo_date_end =  date('12/31/Y');

        if(isset($def)) {
            if(!empty($def['fmpo_date_start'])) $this->fmpo_date_start = $def['fmpo_date_start'];
            if(!empty($def['fmpo_date_end'])) $this->fmpo_date_end = $def['fmpo_date_end'];
            if(!empty($def['probabilities_array'])) $this->probabilities_array = $def['probabilities_array'];
            if(!empty($def['company_array'])) $this->company_array = $def['company_array'];
        }

        if(empty($def['title'])) $this->title = translate('LBL_FMP_OPPORTUNITIES', 'Opportunities');
        $this->searchFields = $dashletData['FMPOpportunitiesDashlet']['searchFields'];
        $this->columns = $dashletData['FMPOpportunitiesDashlet']['columns'];
        $this->seedBean = new Opportunity();
    }

    function processDisplayOptions() {
        require_once('include/templates/TemplateGroupChooser.php');
        $this->configureSS = new Sugar_Smarty();
        // column chooser
        $chooser = new TemplateGroupChooser();

        $chooser->args['id'] = 'edit_tabs';
        $chooser->args['left_size'] = 5;
        $chooser->args['right_size'] = 5;
        $chooser->args['values_array'][0] = array();
        $chooser->args['values_array'][1] = array();

        //$this->addCustomFields();
        if($this->displayColumns) {
            // columns to display
            foreach($this->displayColumns as $num => $name) {
                // defensive code for array being returned
                $translated = translate($this->columns[$name]['label'], $this->seedBean->module_dir);
                if(is_array($translated)) $translated = $this->columns[$name]['label'];
                $chooser->args['values_array'][0][$name] = trim($translated, ':');
            }
            // columns not displayed
            foreach(array_diff(array_keys($this->columns), array_values($this->displayColumns)) as $num => $name) {
                // defensive code for array being returned
                $translated = translate($this->columns[$name]['label'], $this->seedBean->module_dir);
                if(is_array($translated)) $translated = $this->columns[$name]['label'];
                $chooser->args['values_array'][1][$name] = trim($translated, ':');
            }
        }
        else {
            foreach($this->columns as $name => $val) {
                // defensive code for array being returned
                $translated = translate($this->columns[$name]['label'], $this->seedBean->module_dir);
                if(is_array($translated)) $translated = $this->columns[$name]['label'];
                if(!empty($val['default']) && $val['default'])
                    $chooser->args['values_array'][0][$name] = trim($translated, ':');
                else
                    $chooser->args['values_array'][1][$name] = trim($translated, ':');
            }
        }

        $chooser->args['left_name'] = 'display_tabs';
        $chooser->args['right_name'] = 'hide_tabs';
        $chooser->args['max_left'] = '8';

        $chooser->args['left_label'] =  $GLOBALS['app_strings']['LBL_DISPLAY_COLUMNS'];
        $chooser->args['right_label'] =  $GLOBALS['app_strings']['LBL_HIDE_COLUMNS'];
        $chooser->args['title'] =  '';
        //$chooser->args['display'] =  'none';


        $this->configureSS->assign('columnChooser', $chooser->display());

        $query = false;
        $count = 0;


        if(!is_array($this->filters)) {
            // use default search params
            $this->filters = array();
            foreach($this->searchFields as $name => $params) {
                if(!empty($params['default']))
                    $this->filters[$name] = $params['default'];
            }
        }

        foreach($this->searchFields as $name=>$params) {
            if(!empty($name)) {
                $name = strtolower($name);
                $currentSearchFields[$name] = array();

                $widgetDef = $this->seedBean->field_defs[$name];


                if($widgetDef['type'] == 'enum') $widgetDef['remove_blank'] = true; // remove the blank option for the dropdown
                $widgetDef['input_name0'] = empty($this->filters[$name]) ? '' : $this->filters[$name];
                $currentSearchFields[$name]['label'] = translate($widgetDef['vname'], $this->seedBean->module_dir);
                //$currentSearchFields[$name]['input'] = $this->layoutManager->widgetDisplayInput($widgetDef, true, (empty($this->filters[$name]) ? '' : $this->filters[$name]));
                $currentSearchFields[$name]['input'] = $this->layoutManager->widgetDisplayInput($widgetDef, true, 'sales_reps');
            }
            else { // ability to create spacers in input fields
                $currentSearchFields['blank' + $count]['label'] = '';
                $currentSearchFields['blank' + $count]['input'] = '';
                $count++;
            }
        }

        $currentSearchFields['probability']['label'] = 'Probabilities(%):';
        $prob_srt = '<select multiple="true" size="3" name="probability[]">';
        if (in_array(0, $this->probabilities_array)) {
            $prob_srt .=  '<OPTION value="0" selected="selected">0%</OPTION>';
        } else {
            $prob_srt .=  '<OPTION value="0">0%</OPTION>';
        }
        if (in_array(25, $this->probabilities_array)) {
            $prob_srt .=  '<OPTION value="25" selected="selected">25%</OPTION>';
        } else {
            $prob_srt .=  '<OPTION value="25">25%</OPTION>';
        }
        if (in_array(50, $this->probabilities_array)) {
            $prob_srt .=  '<OPTION value="50" selected="selected">50%</OPTION>';
        } else {
            $prob_srt .=  '<OPTION value="50">50%</OPTION>';
        }
        if (in_array(75, $this->probabilities_array)) {
            $prob_srt .=  '<OPTION value="75" selected="selected">75%</OPTION>';
        } else {
            $prob_srt .=  '<OPTION value="75">75%</OPTION>';
        }
        if (in_array(100, $this->probabilities_array)) {
            $prob_srt .=  '<OPTION value="100" selected="selected">100%</OPTION>';
        } else {
            $prob_srt .=  '<OPTION value="100">100%</OPTION>';
        }
        $prob_srt .= '</select>';
        $currentSearchFields['probability']['input'] = $prob_srt;
        
        $currentSearchFields['company']['label'] = 'Company:';
       $prob_srt = '<select multiple="true" size="3" name="company[]">';
       	if (in_array(0, $this->company_array)) {
            $prob_srt .=  '<OPTION value="0" selected="selected">FMP</OPTION>';
        } else {
            $prob_srt .=  '<OPTION value="0">FMP</OPTION>';
        }
        if (in_array(1, $this->company_array)) {
            $prob_srt .=  '<OPTION value="1" selected="selected">Splash</OPTION>';
        } else {
            $prob_srt .=  '<OPTION value="1">Splash</OPTION>';
        }
        $prob_srt .= '</select>';
        $currentSearchFields['company']['input'] = $prob_srt;

//        echo "<pre>";
//        print_r($displayRowOptions);
//        echo "</pre>";

        $this->currentSearchFields = $currentSearchFields;

        $this->configureSS->assign('strings', array('general' => $GLOBALS['mod_strings']['LBL_DASHLET_CONFIGURE_GENERAL'],
                'filters' => $GLOBALS['mod_strings']['LBL_DASHLET_CONFIGURE_FILTERS'],
                'myItems' => $GLOBALS['mod_strings']['LBL_DASHLET_CONFIGURE_MY_ITEMS_ONLY'],
                'displayRows' => $GLOBALS['mod_strings']['LBL_DASHLET_CONFIGURE_DISPLAY_ROWS'],
                'title' => $GLOBALS['mod_strings']['LBL_DASHLET_CONFIGURE_TITLE'],
                'save' => $GLOBALS['app_strings']['LBL_SAVE_BUTTON_LABEL']));
        $this->configureSS->assign('id', $this->id);
        $this->configureSS->assign('showMyItemsOnly', $this->showMyItemsOnly);
        $this->configureSS->assign('myItemsOnly', $this->myItemsOnly);
        $this->configureSS->assign('searchFields', $this->currentSearchFields);
        // title
        $this->configureSS->assign('dashletTitle', $this->title);

        // display rows
        $displayRowOptions = $GLOBALS['sugar_config']['dashlet_display_row_options'];
        $displayRowOptions[4] = 20;

        
        //$this->displayRows = empty($this->displayRows) ? '20' : $this->displayRows;
        $this->configureSS->assign('displayRowOptions', $displayRowOptions);
        $this->configureSS->assign('displayRowSelect', $this->displayRows);
        global $timedate;
        $cal_dateformat = $timedate->get_cal_date_format();
        $this->configureSS->assign('cal_dateformat', $cal_dateformat);
        $this->configureSS->assign('fmpo_date_start', $this->fmpo_date_start);
        $this->configureSS->assign('fmpo_date_end', $this->fmpo_date_end);
        $this->configureSS->assign('probability', $this->probabilities_array);
        $this->configureSS->assign('company', $this->company_array);
    }


    function displayOptions() {
        $this->processDisplayOptions();
        return $this->configureSS->fetch('modules/Opportunities/Dashlets/FMPOpportunitiesDashlet/FMPOpportunitiesDashletConfigure.tpl');
    }

    //4.5.0g fix for upgrade issue where user_preferences table still refer to column as 'amount'
    function process($lvsParams = array()) {

        global $current_user, $sugar_config, $app_list_strings;

        if(!empty($this->displayColumns)) {
            if(array_search('amount', $this->displayColumns)) {
                $this->displayColumns[array_search('amount', $this->displayColumns)] = 'amount_usdollar';
            }
        }
        $lvsParams['custom_from'] = " LEFT JOIN users ON opportunities.assigned_user_id = users.id 
            LEFT JOIN dsls_slsm_combined AS d_c ON (d_c.slsm = accounts.slsm_c) ";
        parent::process($lvsParams);

        $seed_bean  = new Opportunity();

        /* Get Lists for buttons in the Opp Dashlet*/
        $is_user_id = 0;
        $slsm_obj = new fmp_Param_SLSM($current_user->id);
        $slsm_obj->init();

        $is_s = $slsm_obj->is_assigned_slsm();

        $slsm_tree_list = $slsm_obj->html_for_daily_sales('', 'opp_');  // prepeare SLSM list for display
        unset($slsm_obj);

        $slsm_area_obj = new fmp_Param_RegLoc($current_user->id);
        $slsm_area_obj->init($current_user->id);
        $area_list = $slsm_area_obj->html_for_daily_sales($current_user->id, '', 'opp_'); // prepeare AREA list for display
        unset($slsm_area_obj);

        $dealer_list = $this->get_dealer_type($app_list_strings['fmp_dealertype_list']); // prepeare Dealer Type list for display
        $sales_reps_list = $this->get_sales_reps($current_user); // prepeare Sales Reps list for display

        $PREVIOUSAVG_SALES_C_Total = 0;
        $ROLLING_SALES_C_Total = 0;
        $MTD_SALES_C_Total = 0;
        $YTD_SALES_C_Total = 0;
        $AMOUNT_USDOLLAR_Total = 0;
        $AMOUNT_Total = 0;
        $VAR_MONTH_SLS_Total = 0;
        $VAR_ANNUAL_SLS_Total = 0;
        $VAR_MONTH_GP_Total = 0;
        $VAR_ANNUAL_GP_Total = 0;

        $param_query = $current_user->getPreference('opp_dasl_query_param');
        $total_query = "SELECT
                        COUNT(accounts.name) AS coun_sales,
                        SUM(opportunities_cstm.previousavg_sales_c) AS previousavg_sales_c,
                        SUM(opportunities_cstm.rolling_sales_c) AS rolling_sales_c,
                        SUM(opportunities_cstm.mtd_sales_c) AS mtd_sales_c,
                        SUM(opportunities_cstm.ytd_sales_c) AS ytd_sales_c,
                        SUM(opportunities.amount_usdollar/12) AS amount_usdollar,
                        SUM(opportunities.amount) AS mount_usdollar,
                        SUM(opportunities_cstm.mtd_sales_c-opportunities.amount_usdollar/12) AS VAR_MONTH_SLS,
                        SUM(opportunities_cstm.ytd_sales_c-opportunities.amount_usdollar) AS VAR_ANNUAL_SLS,
                        SUM(opportunities_cstm.mtd_gp_c-(opportunities_cstm.mtd_sales_c-opportunities.amount_usdollar)*opportunities.gp_perc/100) AS VAR_MONTH_GP,
                        SUM(opportunities_cstm.ytd_gp_c-(opportunities_cstm.ytd_sales_c-opportunities.amount_usdollar)*opportunities.gp_perc/100) AS VAR_ANNUAL_GP ".
                $param_query;

//        echo'<pre>'; print_r($param_query);echo'</pre>';

        $OPP_slsm_num = ($current_user->getPreference('OPP_slsm_num'))? $current_user->getPreference('OPP_slsm_num'): 'all';
        $OPP_reg_loc = ($current_user->getPreference('OPP_reg_loc'))? $current_user->getPreference('OPP_reg_loc'): 'all';
        $OPP_dealer = ($current_user->getPreference('OPP_dealer'))? $current_user->getPreference('OPP_dealer'): 'all';
        $OPP_sales_reps_names = $current_user->getPreference('OPP_sales_reps_names');
        $OPP_sales_reps_names = str_replace(" ", ", ", $OPP_sales_reps_names);
        $OPP_names =  $current_user->getPreference('OPP_opp_name');
        
        $title = '';
	//print_r($OPP_reg_loc);
	if (!in_array($OPP_reg_loc, array('all', 'undefined', '')) && is_array($OPP_reg_loc)) { 
		foreach($OPP_reg_loc as $reg_loc_value) {
			if(substr($reg_loc_value, 0, 1) == "r") {
				$OPP_reg[substr($reg_loc_value, 1)] = substr($reg_loc_value, 1);
			} else {
				$OPP_loc[$reg_loc_value] = $reg_loc_value;
			}
		}
	}

        $title .= $OPP_reg_loc != 'undefined' && $OPP_reg_loc != 'all' && (isset($OPP_reg) || isset($OPP_loc)) ? (isset($OPP_reg) ? 'Region '.implode(', ', $OPP_reg) : ''): '';
 	$title .= $OPP_reg_loc != 'undefined' && $OPP_reg_loc != 'all' && (isset($OPP_reg) || isset($OPP_loc)) ? (isset($OPP_loc) ? 'Location '.implode(', ', $OPP_loc) : ''): '';
        $title .= $OPP_slsm_num != 'undefined' && $OPP_slsm_num != 'all' ? ($OPP_reg_loc != 'all' && $OPP_reg_loc != 'undefined' ? '/Slsm '.implode(" ", $OPP_slsm_num) : 'Slsm '.implode(" ", $OPP_slsm_num)):'';
        $title .= $OPP_dealer != 'undefined' && $OPP_dealer != 'all' ? (($OPP_slsm_num == 'undefined' || $OPP_slsm_num == 'all') && ($OPP_reg_loc == 'undefined' || $OPP_reg_loc == 'all') ? 'Customer Type '.implode(" ", $OPP_dealer) : '/Customer Type '.implode(" ", $OPP_dealer)):'';
        $title .= isset($OPP_names) && $OPP_names[0] != '' && $OPP_names[0] != 'undefined' ? ' Opportunities '.implode(", ", $OPP_names) : '';
        
        $title = $title == ''? 'All' :$title;

        $total_sum_query = $seed_bean->db->query($total_query);
        $total_sum = $seed_bean->db->fetchByAssoc($total_sum_query);

        $PREVIOUSAVG_SALES_C_Total = $total_sum['previousavg_sales_c'];
        $ROLLING_SALES_C_Total = $total_sum['rolling_sales_c'];
        $MTD_SALES_C_Total = $total_sum['mtd_sales_c'];
        $YTD_SALES_C_Total = $total_sum['ytd_sales_c'];
        $AMOUNT_USDOLLAR_Total = $total_sum['amount_usdollar'];
        $AMOUNT_Total = $total_sum['mount_usdollar'];
        $VAR_MONTH_SLS_Total = $total_sum['VAR_MONTH_SLS'];
        $VAR_ANNUAL_SLS_Total = $total_sum['VAR_ANNUAL_SLS'];
        $VAR_MONTH_GP_Total = $total_sum['VAR_MONTH_GP'];
        $VAR_ANNUAL_GP_Total = $total_sum['VAR_ANNUAL_GP'];

        foreach($this->lvs->data['data'] as $row => $data) {

            $active_id = $data['ACCOUNT_ID'];
            $qry = "SELECT custno_c  from accounts WHERE id = '" . $active_id . "' AND deleted=0";
            $result = $seed_bean->db->query($qry);
            $result_row = $seed_bean->db->fetchByAssoc($result);
            $opp_qry = "SELECT gp_perc, sales_stage, amount_usdollar, opportunities_cstm.mtd_gp_c-(opportunities_cstm.mtd_sales_c-opportunities.amount_usdollar/12)*gp_perc/100 AS var_month_gp,
                        opportunities_cstm.ytd_gp_c-(opportunities_cstm.ytd_sales_c-opportunities.amount_usdollar)*gp_perc/100 AS var_annual_gp,
                        opportunities_cstm.mtd_sales_c-opportunities.amount_usdollar/12 AS var_month_sls,
                        opportunities_cstm.ytd_sales_c-opportunities.amount_usdollar AS var_annual_sls
                        FROM opportunities LEFT JOIN opportunities_cstm ON opportunities.id = opportunities_cstm.id_c
                        WHERE id = '" . $data['ID'] . "' AND deleted=0";
            $opp_result = $seed_bean->db->query($opp_qry);
            $opp_result_row = $seed_bean->db->fetchByAssoc($opp_result);
            $gp_perc = $opp_result_row['gp_perc'];
            $sales_stage =  $opp_result_row['sales_stage'];
            $amount_usdollar = $opp_result_row['amount_usdollar'];
            $this->lvs->data['data'][$row]['ACCOUNT_CUSTNO_C'] =  $result_row['custno_c'];
            $this->lvs->data['data'][$row]['PREV_12_MO'] = '$'.number_format(round($this->lvs->data['data'][$row]['PREVIOUSAVG_SALES_C']),0,'.',',');
            $this->lvs->data['data'][$row]['ROLLING_SALES'] = '$'.number_format(round($this->lvs->data['data'][$row]['ROLLING_SALES_C']),0,'.',',');
            $this->lvs->data['data'][$row]['MTD_SALES'] = '$'.number_format(round($this->lvs->data['data'][$row]['MTD_SALES_C']),0,'.',',');
            $this->lvs->data['data'][$row]['YTD_SALES'] = '$'.number_format(round($this->lvs->data['data'][$row]['YTD_SALES_C']),0,'.',',');
            $this->lvs->data['data'][$row]['AMOUNT_USDOLLAR'] = $this->lvs->data['data'][$row]['AMOUNT'];
            $this->lvs->data['data'][$row]['MONTH_SALES'] = '$'.number_format(round($data['AMOUNT_USDOLLAR']/12),0,'.',',');
            if(empty($data['AMOUNT_USDOLLAR']))
                $this->lvs->data['data'][$row]['MONTH_SALES'] = '$'.number_format(round($amount_usdollar/12),0,'.',',');
            $this->lvs->data['data'][$row]['VAR_MONTH_SLS'] = '$'.number_format(round($opp_result_row['var_month_sls']),0,'.',',');
            $this->lvs->data['data'][$row]['VAR_ANNUAL_SLS'] = '$'.number_format(round($opp_result_row['var_annual_sls']),0,'.',',');
            $this->lvs->data['data'][$row]['VAR_MONTH_PRC'] = ($gp_perc-$data['MTD_GP_PERCENT_C']).'%';
            $this->lvs->data['data'][$row]['VAR_ANNUAL_PRC'] = ($gp_perc-$data['YTD_GP_PERCENT_C']).'%';
            $this->lvs->data['data'][$row]['VAR_MONTH_GP'] = '$'.number_format(round($opp_result_row['var_month_gp']),0,'.',',');
            $this->lvs->data['data'][$row]['VAR_ANNUAL_GP'] = '$'.number_format(round($opp_result_row['var_annual_gp']),0,'.',',');
            $this->lvs->data['data'][$row]['USER'] = $data['ASSIGNED_USER_NAME'];

            if($data['SALES_STAGE'] == 'Closed Won' || $sales_stage == 'Closed Won') {
                $this->lvs->data['data'][$row]['SALES_STAGE'] = "<font style='color: green;'>".$data['SALES_STAGE']."</font>";
                $this->lvs->data['data'][$row]['NAME'] = "<font style='color: green;'>".$data['NAME']."</font>";
                $this->lvs->data['data'][$row]['ACCOUNT_NAME'] = "<font style='color: green;'>".$data['ACCOUNT_NAME']."</font>";
                $this->lvs->data['data'][$row]['ACCOUNT_CUSTNO_C'] = "<font style='color: green;'>".$this->lvs->data['data'][$row]['ACCOUNT_CUSTNO_C']."</font>";
                $this->lvs->data['data'][$row]['AMOUNT_USDOLLAR'] = "<font style='color: green;'>".$this->lvs->data['data'][$row]['AMOUNT']."</font>";
                $this->lvs->data['data'][$row]['MONTH_SALES'] = "<font style='color: green;'>".$this->lvs->data['data'][$row]['MONTH_SALES']."</font>";
                $this->lvs->data['data'][$row]['PREV_12_MO'] = "<font style='color: green;'>".$this->lvs->data['data'][$row]['PREV_12_MO']."</font>";
                $this->lvs->data['data'][$row]['ROLLING_SALES'] = "<font style='color: green;'>".$this->lvs->data['data'][$row]['ROLLING_SALES']."</font>";
                $this->lvs->data['data'][$row]['MTD_SALES'] = "<font style='color: green;'>".$this->lvs->data['data'][$row]['MTD_SALES']."</font>";
                $this->lvs->data['data'][$row]['YTD_SALES'] = "<font style='color: green;'>".$this->lvs->data['data'][$row]['YTD_SALES']."</font>";
                $this->lvs->data['data'][$row]['DATE_CLOSED'] = "<font style='color: green;'>".$data['DATE_CLOSED']."</font>";
                $this->lvs->data['data'][$row]['USER'] = "<font style='color: green;'>".$data['ASSIGNED_USER_NAME']."</font>";
                $this->lvs->data['data'][$row]['PROBABILITY'] = "<font style='color: green;'>".$data['PROBABILITY']."</font>";
                $this->lvs->data['data'][$row]['VAR_MONTH_SLS'] = "<font style='color: green;'>".$this->lvs->data['data'][$row]['VAR_MONTH_SLS']."</font>";
                $this->lvs->data['data'][$row]['VAR_ANNUAL_SLS'] = "<font style='color: green;'>".$this->lvs->data['data'][$row]['VAR_ANNUAL_SLS']."</font>";
                $this->lvs->data['data'][$row]['VAR_MONTH_PRC'] = "<font style='color: green;'>".$this->lvs->data['data'][$row]['VAR_MONTH_PRC']."</font>";
                $this->lvs->data['data'][$row]['VAR_ANNUAL_PRC'] = "<font style='color: green;'>".$this->lvs->data['data'][$row]['VAR_ANNUAL_PRC']."</font>";
                $this->lvs->data['data'][$row]['VAR_MONTH_GP'] = "<font style='color: green;'>".$this->lvs->data['data'][$row]['VAR_MONTH_GP']."</font>";
                $this->lvs->data['data'][$row]['VAR_ANNUAL_GP'] = "<font style='color: green;'>".$this->lvs->data['data'][$row]['VAR_ANNUAL_GP']."</font>";
            }
            if($data['SALES_STAGE'] == 'Closed Lost' || $sales_stage == 'Closed Lost' ) {
                $this->lvs->data['data'][$row]['SALES_STAGE'] = "<font style='color: red;'>".$data['SALES_STAGE']."</font>";
                $this->lvs->data['data'][$row]['NAME'] = "<font style='color: red;'>".$data['NAME']."</font>";
                $this->lvs->data['data'][$row]['ACCOUNT_NAME'] = "<font style='color: red;'>".$data['ACCOUNT_NAME']."</font>";
                $this->lvs->data['data'][$row]['ACCOUNT_CUSTNO_C'] = "<font style='color: red;'>".$this->lvs->data['data'][$row]['ACCOUNT_CUSTNO_C']."</font>";
                $this->lvs->data['data'][$row]['AMOUNT_USDOLLAR'] = "<font style='color: red;'>".$this->lvs->data['data'][$row]['AMOUNT']."</font>";
                $this->lvs->data['data'][$row]['MONTH_SALES'] = "<font style='color: red;'>".$this->lvs->data['data'][$row]['MONTH_SALES']."</font>";
                $this->lvs->data['data'][$row]['PREV_12_MO'] = "<font style='color: red;'>".$this->lvs->data['data'][$row]['PREV_12_MO']."</font>";
                $this->lvs->data['data'][$row]['ROLLING_SALES'] = "<font style='color: red;'>".$this->lvs->data['data'][$row]['ROLLING_SALES']."</font>";
                $this->lvs->data['data'][$row]['MTD_SALES'] = "<font style='color: red;'>".$this->lvs->data['data'][$row]['MTD_SALES']."</font>";
                $this->lvs->data['data'][$row]['YTD_SALES'] = "<font style='color: red;'>".$this->lvs->data['data'][$row]['YTD_SALES']."</font>";
                $this->lvs->data['data'][$row]['DATE_CLOSED'] = "<font style='color: red;'>".$data['DATE_CLOSED']."</font>";
                $this->lvs->data['data'][$row]['USER'] = "<font style='color: red;'>".$data['ASSIGNED_USER_NAME']."</font>";
                $this->lvs->data['data'][$row]['PROBABILITY'] = "<font style='color: red;'>".$data['PROBABILITY']."</font>";
                $this->lvs->data['data'][$row]['VAR_MONTH_SLS'] = "<font style='color: red;'>".$this->lvs->data['data'][$row]['VAR_MONTH_SLS']."</font>";
                $this->lvs->data['data'][$row]['VAR_ANNUAL_SLS'] = "<font style='color: red;'>".$this->lvs->data['data'][$row]['VAR_ANNUAL_SLS']."</font>";
                $this->lvs->data['data'][$row]['VAR_MONTH_PRC'] = "<font style='color: red;'>".$this->lvs->data['data'][$row]['VAR_MONTH_PRC']."</font>";
                $this->lvs->data['data'][$row]['VAR_ANNUAL_PRC'] = "<font style='color: red;'>".$this->lvs->data['data'][$row]['VAR_ANNUAL_PRC']."</font>";
                $this->lvs->data['data'][$row]['VAR_MONTH_GP'] = "<font style='color: red;'>".$this->lvs->data['data'][$row]['VAR_MONTH_GP']."</font>";
                $this->lvs->data['data'][$row]['VAR_ANNUAL_GP'] = "<font style='color: red;'>".$this->lvs->data['data'][$row]['VAR_ANNUAL_GP']."</font>";
            }

        }

        $this->lvs->data['data'][$row+1]['ACCOUNT_NAME'] = $total_sum['coun_sales'];
        $this->lvs->data['data'][$row+1]['PREV_12_MO'] = '$'.number_format(round($PREVIOUSAVG_SALES_C_Total),0,'.',',');
        $this->lvs->data['data'][$row+1]['ROLLING_SALES'] = '$'.number_format(round($ROLLING_SALES_C_Total),0,'.',',');
        $this->lvs->data['data'][$row+1]['MTD_SALES'] = '$'.number_format(round($MTD_SALES_C_Total),0,'.',',');
        $this->lvs->data['data'][$row+1]['YTD_SALES'] = '$'.number_format(round($YTD_SALES_C_Total),0,'.',',');
        $this->lvs->data['data'][$row+1]['AMOUNT_USDOLLAR'] = '$'.number_format(round($AMOUNT_Total),0,'.',',');
        $this->lvs->data['data'][$row+1]['MONTH_SALES'] = '$'.number_format(round($AMOUNT_USDOLLAR_Total),0,'.',',');
        $this->lvs->data['data'][$row+1]['VAR_MONTH_SLS'] = '$'.number_format(round($VAR_MONTH_SLS_Total),0,'.',',');
        $this->lvs->data['data'][$row+1]['VAR_ANNUAL_SLS'] = '$'.number_format(round($VAR_ANNUAL_SLS_Total),0,'.',',');
        $this->lvs->data['data'][$row+1]['VAR_MONTH_GP'] = '$'.number_format(round($VAR_MONTH_GP_Total),0,'.',',');
        $this->lvs->data['data'][$row+1]['VAR_ANNUAL_GP'] = '$'.number_format(round($VAR_ANNUAL_GP_Total),0,'.',',');
        $this->lvs->data['data'][$row+1]['NAME'] = 'Total';

        $this->lvs->data['data'] = array_merge(array($key => array_pop($this->lvs->data['data'])), $this->lvs->data['data']); // for moved "Total Row" on the top
        $quicksearch_js = $this->get_quicksearch_js();
        $opp_name = isset($OPP_names) && $OPP_names[0] != '' && $OPP_names[0] != 'undefined' && count($OPP_names) == 1 ? implode(", ", $OPP_names):'';
        //pr($this->lvs->data['pageData']['offsets']['totalCounted']);
        $this->lvs->ss->assign('slsm_list', $slsm_tree_list);
        $this->lvs->ss->assign('area_list', $area_list);
        $this->lvs->ss->assign('dealer_list', $dealer_list);
        $this->lvs->ss->assign('sales_reps_list', $sales_reps_list);
        $this->lvs->ss->assign('opp_dashlet_title', $title);
        $this->lvs->ss->assign('sales_reps_name', 'Sales Reps: '.$OPP_sales_reps_names);
        $this->lvs->ss->assign('quicksearch_js', $quicksearch_js);
        $this->lvs->ss->assign('opp_name', $opp_name);
    }

    function saveOptions($req) {

        $GLOBALS['log']->test("fmp dashlet save testing ".$req['probability']);
        $options = array();

        foreach($req as $name => $value) {
            if(!is_array($value)) $req[$name] = trim($value);
        }
        $options['filters'] = array();
        foreach($this->searchFields as $name=>$params) {
            $widgetDef = $this->seedBean->field_defs[$name];
            if($widgetDef['type'] == 'datetime' || $widgetDef['type'] == 'date') { // special case datetime types
                $options['filters'][$widgetDef['name']] = array();
                if(!empty($req['type_' . $widgetDef['name']])) { // save the type of date filter
                    $options['filters'][$widgetDef['name']]['type'] = $req['type_' . $widgetDef['name']];
                }
                if(!empty($req['date_' . $widgetDef['name']])) { // save the date
                    $options['filters'][$widgetDef['name']]['date'] = $req['date_' . $widgetDef['name']];
                }
            }
            elseif(!empty($req[$widgetDef['name']])) {
                $options['filters'][$widgetDef['name']] = $req[$widgetDef['name']];
            }
        }
        if(!empty($req['dashletTitle'])) {
            $options['title'] = $req['dashletTitle'];
        }

        if(!empty($req['myItemsOnly'])) {
            $options['myItemsOnly'] = $req['myItemsOnly'];
        }
        else {
            $options['myItemsOnly'] = false;
        }
        $options['displayRows'] = empty($req['displayRows']) ? '20' : $req['displayRows'];
        // displayColumns
        if(!empty($req['displayColumnsDef'])) {
            $options['displayColumns'] = explode('|', $req['displayColumnsDef']);
        }

        if(!empty($req['probability'])) {
            $options['probabilities_array'] = $req['probability'];
        }
        if(!empty($req['company'])) {
            $options['company_array'] = $req['company'];
        }

        if(!empty($req['fmpo_date_start'])) {
            $options['fmpo_date_start'] = $req['fmpo_date_start'];
        }

        if(!empty($req['fmpo_date_end'])) {
            $options['fmpo_date_end'] = $req['fmpo_date_end'];
        }

        return $options;
    }

    function buildWhere() {
        global $current_user, $app_list_strings;

        $returnArray = array();

        if(!is_array($this->filters)) {
            // use defaults
            $this->filters = array();
            foreach($this->searchFields as $name => $params) {
                if(!empty($params['default']))
                    $this->filters[$name] = $params['default'];
            }
        }

        if($current_user->getPreference('OPP_sales_reps') != null && $current_user->getPreference('OPP_sales_reps') != '') {
            $this->filters['assigned_user_id'] = $current_user->getPreference('OPP_sales_reps');
        }

        foreach($this->filters as $name=>$params) {

            if(!empty($params)) {
                if($name == 'assigned_user_id' && $this->myItemsOnly) continue; // don't handle assigned user filter if filtering my items only
                $widgetDef = $this->seedBean->field_defs[$name];

                $widgetClass = $this->layoutManager->getClassFromWidgetDef($widgetDef, true);
                $widgetDef['table'] = $this->seedBean->table_name;
                $widgetDef['table_alias'] = $this->seedBean->table_name;

                switch($widgetDef['type']) {// handle different types
                    case 'date':
                    case 'datetime':
                        if(is_array($params) && !empty($params)) {
                            if(!empty($params['date']))
                                $widgetDef['input_name0'] = $params['date'];
                            $filter = 'queryFilter' . $params['type'];
                        }
                        else {
                            $filter = 'queryFilter' . $params;
                        }
                        array_push($returnArray, $widgetClass->$filter($widgetDef, true));
                        break;
                    default:
                        $widgetDef['input_name0'] = $params;
                        if(is_array($params) && !empty($params)) { // handle array query
                            array_push($returnArray, $widgetClass->queryFilterone_of($widgetDef, false));
                        }
                        else {
                            array_push($returnArray, $widgetClass->queryFilterStarts_With($widgetDef, true));
                        }
                        $widgetDef['input_name0'] = $params;
                        break;
                }
            }
        }

        if($this->myItemsOnly){
            array_push($returnArray, $this->seedBean->table_name . '.' . "assigned_user_id = '" . $current_user->id . "'");
            array_push($returnArray, "users.id = '" . $current_user->id . "'");
        }
        if($this->fmpo_date_start && $this->fmpo_date_start) {
            $date_start_unix = strtotime($this->fmpo_date_start);
            $date_end_unix = strtotime($this->fmpo_date_end);
            $rqs_closed_date = ' UNIX_TIMESTAMP(opportunities.date_closed) >=  \''.$date_start_unix.'\'';
            $rqs_closed_date .=  ' AND UNIX_TIMESTAMP(opportunities.date_closed) <=  \''.$date_end_unix.'\' ';
            array_push($returnArray, $rqs_closed_date);
        }

        $OPP_reg = '';
        $OPP_loc = '';
        $slsmqry = '';
        $regqry = '';
        $locqry = '';
        $allarea = '';
        $dealerqry = '';

        $OPP_slsm_num = ($current_user->getPreference('OPP_slsm_num'))? $current_user->getPreference('OPP_slsm_num'): 'all';
        $OPP_reg_loc = ($current_user->getPreference('OPP_reg_loc'))? $current_user->getPreference('OPP_reg_loc'): 'all';
        $OPP_dealer = ($current_user->getPreference('OPP_dealer'))? $current_user->getPreference('OPP_dealer'): 'all';
        $OPP_names =  $current_user->getPreference('OPP_opp_name');
        
//        if ($OPP_reg_loc == 'all') {
//            $slsm_area_obj = new fmp_Param_RegLoc($current_user->id);
//            $slsm_area_obj->init($current_user->id);
//            $area_list = $slsm_area_obj->get_id_for_area($current_user->id);
//            unset($slsm_area_obj);
//            if (count($area_list)>0) {
//                $regIn = array();
//                $locIn = array();
//                foreach ($area_list as $reg => $loc) {
//                    $regIn[] = $reg;
//                    foreach ($loc['locs'] as $key => $value) {
//                        $locIn[] = $key;
//                    }
//
//                }
//
//                $allarea .= ' accounts.region_c IN (' . implode(', ', $regIn) . ') AND accounts.location_c IN (' . implode(', ', $locIn) . ') ';
//                array_push($returnArray, $allarea);
//            }
//        }
//	if (!in_array($OPP_reg_loc, array('all', 'undefined', '')) && is_array($OPP_reg_loc)) { 
//		foreach($OPP_reg_loc as $reg_loc_value) {
//			if(substr($reg_loc_value, 0, 1) == "r") {
//				$OPP_reg[substr($reg_loc_value, 1)] = substr($reg_loc_value, 1);
//			} else {
//				$OPP_loc[$reg_loc_value] = $reg_loc_value;
//			}
//		}
//	}

        /*if (substr($OPP_reg_loc,0,1) == 'r') {
            $OPP_reg = substr($OPP_reg_loc,1) ;
        }
        if(is_numeric($OPP_reg_loc)) {
            $OPP_loc = $OPP_reg_loc;
        }*/

        if(is_array($this->probabilities_array) && !empty($this->probabilities_array)) {
            $rqs_probability = ' opportunities.probability IN (' . implode(',', $this->probabilities_array) . ') OR opportunities.probability is NULL ';
            array_push($returnArray, $rqs_probability);
        }
        
        if(is_array($this->company_array) && !empty($this->company_array)) {
            if (in_array(0, $this->company_array) && !in_array(1, $this->company_array)) {
                $rqs_company = " d_c.company = 'FMP' ";
                array_push($returnArray, $rqs_company);
            } 
            if (in_array(1, $this->company_array) && !in_array(0, $this->company_array)) {
                $rqs_company = " d_c.company = 'Splash' ";
                array_push($returnArray, $rqs_company);
            }
        }
        
        if (isset($OPP_reg) && $OPP_reg != '' && $OPP_reg != 'undefined' && $OPP_reg != 'all' && count($OPP_reg) > 0) {
            $regqry .= " accounts.region_c IN(".implode(', ', $OPP_reg).") ";
            array_push($returnArray, $regqry);
        }

        if (isset($OPP_loc) && $OPP_loc != '' && $OPP_loc != 'undefined' && $OPP_loc != 'all' && count($OPP_loc) > 0) {
            $locqry .= " accounts.location_c IN(".implode(', ', $OPP_loc).") ";
            array_push($returnArray, $locqry);
        }
        if (isset($OPP_dealer) && $OPP_dealer != '' && $OPP_dealer != 'undefined' && $OPP_dealer != 'all' && count($OPP_dealer) > 0) {
            $dealer_list = array();
            if($OPP_dealer != 'all') {
                $dealerqry .= " accounts.dealertype_c IN (".implode(", ", $OPP_dealer).") ";
                array_push($returnArray, $dealerqry);
            }else {
                if (count($app_list_strings['fmp_dealertype_list'])>0) {
                    foreach($app_list_strings['fmp_dealertype_list'] as $key=>$value) {
                        if($key != '' && $key != null) {
                            $dealer_list[] = $key;
                        }
                    }
                    $dealerqry = " accounts.dealertype_c IN (" . implode(", ", $dealer_list) . ") ";
                }
                array_push($returnArray, $dealerqry);
            }
        }
        
        if (isset($OPP_slsm_num) && $OPP_slsm_num != '' && $OPP_slsm_num != 'undefined' ) {

            $is_user_id = 0;
            $slsm_obj = new fmp_Param_SLSM($current_user->id);
            $slsm_obj->init();

            $is_s = $slsm_obj->is_assigned_slsm();
            if ($is_s) {
                if(isset($OPP_slsm_num) && $OPP_slsm_num != '' && $OPP_slsm_num != 'undefined' && $OPP_slsm_num != 'all') {
                    $arr =  $OPP_slsm_num;
                }else {
                    $arr =  Array(0 => null);
                }
                $r_users = $slsm_obj->compile__available_slsm($arr);
                $str_selection_button = $this->build__slsm($r_users, $is_user_id);
                $slsmqry .= " accounts.slsm_c ".$str_selection_button." ";
                array_push($returnArray, $slsmqry);
            }
            unset($slsm_obj);                        
        }
        
        if(isset($OPP_names) && $OPP_names[0] != '' && $OPP_names[0] != 'undefined' && count($OPP_names) == 1){
            $nameqry = " opportunities.name like '%".$OPP_names[0]."%' ";
            array_push($returnArray, $nameqry);
        }elseif(isset($OPP_names) && $OPP_names[0] != '' && $OPP_names[0] != 'undefined' && count($OPP_names) > 1){
            $nameqry = " (";
            foreach($OPP_names as $name){
                if (!next($OPP_names)) {
                    $nameqry .= " opportunities.name like '%".$name."%') ";
                }else{
                    $nameqry .= " opportunities.name like '%".$name."%' OR ";
                }
            }
            array_push($returnArray, $nameqry);
        }
       
/*
 *      foreach ($returnArray as $where_value) {
            $this->where_query_for_total_row .= " AND (". $where_value .")";
        }
 */
//        echo '<pre>'; print_r($returnArray); echo '</pre>';
        return $returnArray;
    }

    protected function build__slsm($compiled_slsm, $is_user_id) {
        foreach ($compiled_slsm as $k=>$v) {
            $compiled_slsm[$k] = "'$v'";
        }
        $h = ''
                . ' IN (' . implode(', ', $compiled_slsm) . ') '
        ;
        return $h;
    }

    function get_dealer_type ($dealer_list) {
        $select_creater = '<select id="opp_fmp_dealer_type" size="10" multiple="multiple" style="width: 170px;">';
        $select_creater .= '<option value="all" style="border-bottom: 2px solid grey;">ALL</option>';
        foreach ($dealer_list as $key=>$value) {
            //if($key!='') {
                $select_creater .= '<option value="'.$key.'">'.$value.'</option>';
            //}
        }
        $select_creater .= '</select>';
        return $select_creater;
    }

    function get_sales_reps ($current_user) {
        $str = '<select id="opp_sales_reps_list" size="10" multiple="multiple" style="width: 170px;">';
        $ids = array($current_user->id);
        $o = new fmp_Param_SLSM($current_user->id);
        $o->init();
        $str .= get_select_options_with_id($o->get_sales_reps_array(), '');
        unset($o);
        $str .= '</select>';
        return $str;
    }
    
    function get_quicksearch_js () {
        require_once 'include/QuickSearchDefaults.php';
        $qsd = new QuickSearchDefaults();

        $o = $qsd->getQSParent('Opportunities');
        $o['field_list'] = array('name','id');
        $o['populate_list'] = array('fmp-opp-name','fmp-opp-id');
        $o['conditions'] = array(array('name'=>'name','op'=>'like_custom','begin'=>'%','end'=>'%','value'=>''));
        $sqs_objects['fmp-opp-name'] = $o;
        
        $json = getJSONobj();
        $quicksearch_js = array();
        foreach ($sqs_objects as $sqsfield => $sqsfieldArray) {
                $quicksearch_js[] = "sqs_objects['$sqsfield']={$json->encode($sqsfieldArray)};";
        }
        $quicksearch_js = ''
                . '<script language="javascript">'
                . "if(typeof sqs_objects == 'undefined'){var sqs_objects = new Array;}"
                . implode("\n", $quicksearch_js)
                . '</script>';
        return $quicksearch_js;
    }
    
}

?>
