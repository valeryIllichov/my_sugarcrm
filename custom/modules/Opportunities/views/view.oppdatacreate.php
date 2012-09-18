<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
require_once('include/MVC/View/SugarView.php');
require_once('modules/ZuckerReportParameter/fmp.class.param.slsm.php');
require_once('include/database/PearDatabase.php');

class OpportunitiesViewOppDataCreate extends SugarView {
    function OpportunitiesViewOppDataCreate() {
        parent::SugarView();
        $this->options['show_header'] = false;
        $this->options['show_footer'] = false;
        $this->options['show_search'] = false;
        $this->options['show_javascript'] = true;
    }

    function display() {
        global $current_user, $sugar_config;
        $sales_reps_array = $_POST['opp_sales_reps'] == 'all ' ? 'all' : explode(" ", trim($_POST['opp_sales_reps']));
	$dealer_array = $_POST['opp_dealer'] == 'all' ? 'all' : explode(" ", trim($_POST['opp_dealer']));
	$slsm_num_array = $_POST['opp_slsm_num'] == 'all' || $_POST['opp_slsm_num'] == '' ? 'all' : explode(" ", trim($_POST['opp_slsm_num']));
	$reg_loc_array = $_POST['opp_reg_loc'] == 'all' ? 'all' : explode(" ", trim($_POST['opp_reg_loc']));
        $opp_name =  explode(",",$_POST['opp_name']);
        $current_user->setPreference('OPP_slsm_num', $slsm_num_array);
        $current_user->setPreference('OPP_reg_loc', $reg_loc_array);
        $current_user->setPreference('OPP_dealer', $dealer_array);
        $current_user->setPreference('OPP_sales_reps', $sales_reps_array);
        $current_user->setPreference('OPP_sales_reps_names', trim($_POST['opp_sr_names']));
        $current_user->setPreference('OPP_opp_name', $opp_name);
    }
}
