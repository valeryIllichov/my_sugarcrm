<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
require_once('include/MVC/View/SugarView.php');
require_once('modules/ZuckerReportParameter/fmp.class.param.slsm.php');
require_once('include/database/PearDatabase.php');

class AccountsViewAccDataCreate extends SugarView {
    function AccountsViewAccDataCreate() {
        parent::SugarView();
        $this->options['show_header'] = false;
        $this->options['show_footer'] = false;
        $this->options['show_search'] = false;
        $this->options['show_javascript'] = true;
    }

    function display() {
        global $current_user, $sugar_config;
        $customers = explode(",",$_POST['customers']);
        $custno =  explode(",",$_POST['custno']);
        $dealer_array = $_POST['meet_dealer'] == 'all' ? 'all' : explode(" ", trim($_POST['meet_dealer']));
        $dealer_array = $_POST['meet_dealer'] == 'allin' ? 'allin' : $dealer_array;
        $slsm_num_array = $_POST['meet_slsm_num'] == 'all' || $_POST['meet_slsm_num'] == '' ? 'all' : explode(" ", trim($_POST['meet_slsm_num']));
        $slsm_num_array = $_POST['meet_slsm_num'] == 'allin' || $_POST['meet_slsm_num'] == '' ? 'allin' : $slsm_num_array ;
        $reg_loc_array = $_POST['meet_reg_loc'] == 'all' ? 'all' : explode(" ", trim($_POST['meet_reg_loc']));
        $reg_loc_array = $_POST['meet_reg_loc'] == 'allin' ? 'allin' : $reg_loc_array;
        $date_range_start = $_POST['date_range_start'];
        $date_range_end = $_POST['date_range_end'];
        if(isset($_POST['update']) && $_POST['update']==1){
            $MEET_slsm_num = ($current_user->getPreference('MEET_slsm_num'))? $current_user->getPreference('MEET_slsm_num'): 'all';
            $MEET_reg_loc = ($current_user->getPreference('MEET_reg_loc'))? $current_user->getPreference('MEET_reg_loc'): 'all';
            $MEET_dealer = ($current_user->getPreference('MEET_dealer'))? $current_user->getPreference('MEET_dealer'): 'all';
            if($MEET_slsm_num != 'allin' && $MEET_slsm_num != 'all'){
                if($slsm_num_array != 'allin'){
                    $current_user->setPreference('MEET_slsm_num',array_unique(array_merge($MEET_slsm_num, $slsm_num_array)));
                }elseif($slsm_num_array != 'all'){
                    $current_user->setPreference('MEET_slsm_num',$MEET_slsm_num);
                }elseif($slsm_num_array == 'all'){
                    $current_user->setPreference('MEET_slsm_num',$slsm_num_array);
                }
            }else{
                $current_user->setPreference('MEET_slsm_num', $slsm_num_array);
            }
            if($MEET_reg_loc != 'allin' && $MEET_reg_loc != 'all'){
                if($reg_loc_array != 'allin'){
                    $current_user->setPreference('MEET_reg_loc',array_unique(array_merge($MEET_reg_loc, $reg_loc_array)));
                }elseif($reg_loc_array != 'all'){
                    $current_user->setPreference('MEET_reg_loc', $MEET_reg_loc);
                }elseif($reg_loc_array == 'all'){
                    $current_user->setPreference('MEET_reg_loc', $reg_loc_array);
                }
            }else{
                $current_user->setPreference('MEET_reg_loc', $reg_loc_array);
            }
            if($MEET_dealer != 'allin' && $MEET_dealer != 'all'){
                if($dealer_array != 'allin'){
                    $current_user->setPreference('MEET_dealer',array_unique(array_merge($MEET_dealer, $dealer_array)));
                }elseif($dealer_array != 'all'){
                    $current_user->setPreference('MEET_dealer', $MEET_dealer);
                }elseif($dealer_array == 'all'){
                    $current_user->setPreference('MEET_dealer', $dealer_array);
                }
            }else{
                $current_user->setPreference('MEET_dealer', $dealer_array);
            }
            $current_user->setPreference('MEET_customers', $customers);
            $current_user->setPreference('MEET_custno', $custno);
            $current_user->setPreference('MEET_date_range_start',$date_range_start);
            $current_user->setPreference('MEET_date_range_end', $date_range_end); 
        }else{
            $current_user->setPreference('MEET_slsm_num', $slsm_num_array);
            $current_user->setPreference('MEET_reg_loc', $reg_loc_array);
            $current_user->setPreference('MEET_dealer', $dealer_array);
            $current_user->setPreference('MEET_customers', $customers);
            $current_user->setPreference('MEET_custno', $custno);
            $current_user->setPreference('MEET_date_range_start',$date_range_start);
            $current_user->setPreference('MEET_date_range_end', $date_range_end); 
        }
    }
}
