<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
require_once('include/MVC/View/SugarView.php');
require_once('modules/ZuckerReportParameter/fmp.class.param.slsm.php');
require_once('include/database/PearDatabase.php');

class OpportunitiesViewPBSSParamsForQuery extends SugarView {
    function OpportunitiesViewPBSSParamsForQuery() {
        parent::SugarView();
        $this->options['show_header'] = false;
        $this->options['show_footer'] = false;
        $this->options['show_search'] = false;
        $this->options['show_javascript'] = true;
    }

    function display() {
        global $current_user, $sugar_config;
        $sales_reps_array = $_POST['pipeline_sales_reps'] == 'all' ? 'all' : explode(" ", trim($_POST['pipeline_sales_reps']));
	$dealer_array = $_POST['pipeline_dealer'] == 'all' ? 'all' : explode(" ", trim($_POST['pipeline_dealer']));
	$slsm_num_array = $_POST['pipeline_slsm_num'] == 'all' ? 'all' : explode(" ", trim($_POST['pipeline_slsm_num']));
	$reg_loc_array = $_POST['pipeline_reg_loc'] == 'all' ? 'all' : explode(" ", trim($_POST['pipeline_reg_loc']));
        $opp_name =  explode(",",$_POST['oppchart_name']);
        $current_user->setPreference('pipeline_slsm_num', $slsm_num_array);
        $current_user->setPreference('pipeline_reg_loc', $reg_loc_array);
        $current_user->setPreference('pipeline_dealer', $dealer_array);
        $current_user->setPreference('pipeline_sales_reps', $sales_reps_array);
        $current_user->setPreference('pipeline_sales_reps_names', trim($_POST['pipeline_sr_names']));
        $current_user->setPreference('reset_clicked', $_POST['reset_clicked']);
        $current_user->setPreference('OPP_opp_name', $opp_name);
    }
}
