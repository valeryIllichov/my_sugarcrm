<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('include/Dashlets/DashletGeneric.php');
require_once('modules/Accounts/Account.php');
require_once('include/utils.php');
require_once('modules/ZuckerReportParameter/fmp.class.param.slsm.php');
require_once('modules/ZuckerReportParameter/fmp.class.param.regloc.php');
require_once('include/database/PearDatabase.php');

class MeetingsAndExceptionsDashlet extends DashletGeneric {
    var $exceptions = false;
    var $next_offset = 0;
    var $prev_offset = 0;
    var $custom_prev_offset = 0;
    var $custom_prev2_offset = 0;
    var $current_offset = 0;
    var $printing = false;
    function MeetingsAndExceptionsDashlet($id, $def = null) {
        global $current_user, $app_strings, $dashletData;
        require('modules/Accounts/Dashlets/MeetingsAndExceptionsDashlet/MeetingsAndExceptionsDashlet.data.php');

        parent::DashletGeneric($id, $def);
        if($_GET['print_dashlet'] == 'true'){
           $this->printing = true;
        }
        
        if(isset($def)) {
            if(!empty($def['displayRows'])) {$this->displayRows = $def['displayRows'];} else { $this->displayRows = 40;}
        }
        if(isset($def)) {
            if(!empty($def['exceptions'])) $this->exceptions = $def['exceptions'];
        }  
        if($current_user->getPreference('next_offset')){
            $this->next_offset = $current_user->getPreference('next_offset');
        }
        if($current_user->getPreference('prev_offset')){
            $this->prev_offset = $current_user->getPreference('prev_offset');
        }
        if($current_user->getPreference('current_offset')){
            $this->current_offset = $current_user->getPreference('current_offset');
        }
        if($current_user->getPreference('custom_prev_offset')){
            $this->custom_prev_offset = $current_user->getPreference('custom_prev_offset');
        }
        if($current_user->getPreference('custom_prev2_offset')){
            $this->custom_prev2_offset = $current_user->getPreference('custom_prev2_offset');
        }
        $this->displayTpl = "modules/Accounts/Dashlets/MeetingsAndExceptionsDashlet/MeetingsAndExceptionsGenericDisplay.tpl";
       

        if(empty($def['title'])) $this->title = 'Meetings and Exceptions';
        $this->searchFields = $dashletData['MeetingsAndExceptionsDashlet']['searchFields'];
        $this->columns = $dashletData['MeetingsAndExceptionsDashlet']['columns'];
        $this->seedBean = new Account();
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
        $chooser->args['max_left'] = '14';

        $chooser->args['left_label'] =  $GLOBALS['app_strings']['LBL_DISPLAY_COLUMNS'];
        $chooser->args['right_label'] =  $GLOBALS['app_strings']['LBL_HIDE_COLUMNS'];
        $chooser->args['title'] =  '';
        //$chooser->args['display'] =  'none';


        $this->configureSS->assign('columnChooser', $chooser->display());



        $this->configureSS->assign('strings', array('general' => $GLOBALS['mod_strings']['LBL_DASHLET_CONFIGURE_GENERAL'],
                'filters' => $GLOBALS['mod_strings']['LBL_DASHLET_CONFIGURE_FILTERS'],
                'myItems' => $GLOBALS['mod_strings']['LBL_DASHLET_CONFIGURE_MY_ITEMS_ONLY'],
                'displayRows' => $GLOBALS['mod_strings']['LBL_DASHLET_CONFIGURE_DISPLAY_ROWS'],
                'title' => $GLOBALS['mod_strings']['LBL_DASHLET_CONFIGURE_TITLE'],
                'save' => $GLOBALS['app_strings']['LBL_SAVE_BUTTON_LABEL']));
        $this->configureSS->assign('id', $this->id);
        $this->configureSS->assign('showMyItemsOnly', $this->showMyItemsOnly);
        $this->configureSS->assign('exceptions', $this->exceptions);
        // title
        $this->configureSS->assign('dashletTitle', $this->title);

        // display rows
        $displayRowOptions = array( 5, 10, 20, 40, 100);

        
        //$this->displayRows = empty($this->displayRows) ? '20' : $this->displayRows;
        $this->configureSS->assign('displayRowOptions', $displayRowOptions);
        $this->configureSS->assign('displayRowSelect', $this->displayRows);
    }
    
    function saveOptions($req) {
        global $current_user;
        if(!empty($req['dashletTitle'])) {
            $options['title'] = $req['dashletTitle'];
        }

        if(!empty($req['exceptions'])) {
            $options['exceptions'] = true;
            $current_user->setPreference('next_offset',0);
            $current_user->setPreference('prev_offset',0);
            $current_user->setPreference('current_offset',0);
            $current_user->setPreference('custom_prev_offset',0);
            $current_user->setPreference('custom_prev2_offset',0);
        }
        else {
            $options['exceptions'] = false;
        }
        $options['displayRows'] = empty($req['displayRows']) ? '40' : $req['displayRows'];
        
        // displayColumns
        if(!empty($req['displayColumnsDef'])) {
            $options['displayColumns'] = explode('|', $req['displayColumnsDef']);
        }
        
        return $options;
    }   

    function displayOptions() {
        $this->processDisplayOptions();
        return $this->configureSS->fetch('modules/Accounts/Dashlets/MeetingsAndExceptionsDashlet/MeetingsAndExceptionsDashletConfigure.tpl');   
    }

    //4.5.0g fix for upgrade issue where user_preferences table still refer to column as 'amount'
    function process($lvsParams = array()) {
        global $current_user, $app_list_strings;
        $this->lvs->lvd->setVariableName($this->seedBean->object_name, array());
        $lvdOrderBy = $this->lvs->lvd->getOrderBy();
        $this->displayRows = $this->printing ? 1000 : $this->displayRows;
        foreach($this->columns as $field=>$params){
             if(!empty($params['custom_orderBy'])){
                 $orderByTotal = explode("|", $params['custom_orderBy']);
                 if($orderByTotal[0] == 'total' && $orderByTotal[1] == $lvdOrderBy['orderBy']){
                      $orderBy1 = explode(".", $orderByTotal[2]);
                      $lvsParams1 = $this->sortingByField($orderBy1[2],$orderBy1[0],$orderBy1[1]);
                      $orderBy2 = explode(".", $orderByTotal[3]);
                      $lvsParams2 = $this->sortingByField($orderBy2[2],$orderBy2[0],$orderBy2[1]);
                      $lvsParams = array(
                        'custom_select' => " ,(IFNULL(".$orderBy1[2].",0) + IFNULL(".$orderBy2[2].",0)) as $field ",
                        'custom_from' => $lvsParams1['custom_from'].$lvsParams2['custom_from']
                        );
                 }else{
                     $orderBy = explode(".", $params['custom_orderBy']);
                     if($orderBy[2] == $lvdOrderBy['orderBy'])
                        $lvsParams = $this->sortingByField($field,$orderBy[0],$orderBy[1]);
                 }
             }
        }
        $MEET_date_start =  $current_user->getPreference('MEET_date_range_start');
        $MEET_date_end =  $current_user->getPreference('MEET_date_range_end');
        if(!empty($MEET_date_start) && !empty($MEET_date_end)) {
            $date_start_unix = strtotime($MEET_date_start);
            $date_end_unix = strtotime($MEET_date_end);
            $date_start = $MEET_date_start;
            $date_end = $MEET_date_end;
        }else{
            $days = date("t");
            $date_start = date("m/01/Y");
            $date_end = date("m/$days/Y");
            $date_start_unix = strtotime($date_start);
            $date_end_unix = strtotime($date_end);
        }
        $meet_tot = $this->totalExceptionTable();
        if($this->exceptions) {
            $next = $this->lvs->lvd->getOffset() > $this->prev_offset ? 1:0;
            $prev = $this->lvs->lvd->getOffset() < $this->prev_offset ? 1:0;
            if($this->lvs->lvd->getOffset() == 0){
                $current_user->setPreference('next_offset',0);
                $current_user->setPreference('prev_offset',0);
                $current_user->setPreference('custom_prev_offset',0);
                $current_user->setPreference('custom_prev2_offset',0);
                $current_user->setPreference('current_offset',0);
                $this->next_offset = 0;
                $this->prev_offset = 0;
                $this->custom_prev_offset = 0;
                $this->custom_prev2_offset = 0;
                $this->current_offset = 0;
            }
            $current_user->setPreference('custom_prev2_offset',$this->custom_prev_offset);
            $lvsParams['exceptions'] = true;
            $lvsParams['next_offset'] = 0;
            if($next == 1 && $this->lvs->lvd->getOffset() != 0){
                $lvsParams['next_offset'] = $this->next_offset;  
            }elseif($prev == 1 && $this->lvs->lvd->getOffset() != 0){
                $lvsParams['next_offset'] = $this->custom_prev2_offset;
                $this->next_offset = $this->custom_prev2_offset;
            }
            if($meet_tot <= $this->displayRows){
                $this->displayRows = $this->totalCountQuery();
            }
            
        }
        parent::process($lvsParams);

        $is_user_id = 0;
        $slsm_obj = new fmp_Param_SLSM($current_user->id);
        $slsm_obj->init();

        $is_s = $slsm_obj->is_assigned_slsm();

        $slsm_tree_list = $slsm_obj->html_for_daily_sales('', 'meet_');  // prepeare SLSM list for display
        unset($slsm_obj);

        $slsm_area_obj = new fmp_Param_RegLoc($current_user->id);
        $slsm_area_obj->init($current_user->id);
        $area_list = $slsm_area_obj->html_for_daily_sales($current_user->id, '', 'meet_'); // prepeare AREA list for display
        unset($slsm_area_obj);

        $dealer_list = $this->get_dealer_type($app_list_strings['fmp_dealertype_list']); // prepeare Dealer Type list for display

        $quicksearch_js = $this->get_quicksearch_js();
        $MEET_slsm_num = ($current_user->getPreference('MEET_slsm_num'))? $current_user->getPreference('MEET_slsm_num'): 'all';
        $MEET_reg_loc = ($current_user->getPreference('MEET_reg_loc'))? $current_user->getPreference('MEET_reg_loc'): 'all';
        $MEET_dealer = ($current_user->getPreference('MEET_dealer'))? $current_user->getPreference('MEET_dealer'): 'all';
        $MEET_custno =  $current_user->getPreference('MEET_custno');
         
        $title = '';
	if (!in_array($MEET_reg_loc, array('all', 'undefined', '')) && is_array($MEET_reg_loc)) { 
		foreach($MEET_reg_loc as $reg_loc_value) {
			if(substr($reg_loc_value, 0, 1) == "r") {
				$MEET_reg[substr($reg_loc_value, 1)] = substr($reg_loc_value, 1);
			} else {
				$MEET_loc[$reg_loc_value] = $reg_loc_value;
			}
		}
	}

        $title .= $MEET_reg_loc != 'undefined' && $MEET_reg_loc != 'all' && $MEET_reg_loc != 'allin' && (isset($MEET_reg) || isset($MEET_loc)) ? (isset($MEET_reg) ? '<span style="font-style:italic; color: #000000;"> Region </span>'.implode(', ', $MEET_reg) : ''): '';
 	$title .= $MEET_reg_loc != 'undefined' && $MEET_reg_loc != 'all' && $MEET_reg_loc != 'allin' && (isset($MEET_reg) || isset($MEET_loc)) ? (isset($MEET_loc) ? '<span style="font-style:italic; color: #000000;"> Location </span>'.implode(', ', $MEET_loc) : ''): '';
        $title .= $MEET_slsm_num != 'undefined' && $MEET_slsm_num != 'all' && $MEET_slsm_num != 'allin' ? '<span style="font-style:italic; color: #000000;"> Slsm </span>'.implode(" ", $MEET_slsm_num):'';
        $title .= $MEET_dealer != 'undefined' && $MEET_dealer != 'all' && $MEET_dealer != 'allin' ? '<span style="font-style:italic; color: #000000;"> Customer Type </span>'.implode(" ", $MEET_dealer):'';
        $title .= isset($MEET_custno) && $MEET_custno[0] != '' && $MEET_custno[0] != 'undefined' ? '<span style="font-style:italic; color: #000000;"> Customers </span> '.implode(", ", $MEET_custno):'';
        $title .= isset($date_start) && isset($date_end) ? '<span style="font-style:italic; color: #000000;"> Start Date</span> from '.$date_start.' to '.$date_end:'';
        $title = $title == ''? 'All' :$title;
        global $timedate;
        
        $total_count = $this->addCustomerFields($this->lvs->data['data']);
        $total_offsets = $this->lvs->data['pageData']['offsets']['total'];
        $cal_dateformat = $timedate->get_cal_date_format();
        //$meet_tot = $meet >= $total_offsets ? $total_offsets : $meet; 
        $this->lvs->ss->assign('cal_dateformat', $cal_dateformat);
        $this->lvs->ss->assign('meet_date_start', $date_start);
        $this->lvs->ss->assign('meet_date_end', $date_end);
        $this->lvs->ss->assign('slsm_list', $slsm_tree_list);
        $this->lvs->ss->assign('area_list', $area_list);
        $this->lvs->ss->assign('dealer_list', $dealer_list);
        $this->lvs->ss->assign('quicksearch_js', $quicksearch_js);
        $this->lvs->ss->assign('meet_dashlet_title', $title);
        $this->lvs->ss->assign('exceptions', $this->exceptions);
       // print_r($this->next_offset);
        if($this->exceptions){
            if($this->displayRows > $total_count && $total_count != $meet_tot){
                $offset = $this->displayRows;    
                do {
                    $params['exceptions'] = true;
                    $params['next_offset'] = $offset + $this->next_offset;
                    $limit = $this->displayRows - $total_count;
                   // $this->lvs->lvd = new ListViewData();
                    $whereArray = $this->buildWhere();
                    $where = '';
                    if(!empty($whereArray)){
                        $where = '(' . implode(') AND (', $whereArray) . ')';
                    } 
                    $data = $this->lvs->lvd->getListViewData($this->seedBean, $where, $params['next_offset'], $limit, $filter_fields=array(), $params);
                    $c_test = count($data['data']);
                    //$total_count = $this->addCustomerFields($data,false);
                    if(count($data['data']) != 0){
                        $this->lvs->data['data'] = array_merge($this->lvs->data['data'],$data['data']);
                    }else{
                        break;
                    }
                    $total_count = $this->addCustomerFields($this->lvs->data['data']);
                    $offset = $offset + $limit;
                }while($total_count < $this->displayRows);
               // print_r($offset + $this->next_offset);
                //if($next && !$prev ){
                    $current_user->setPreference('current_offset',$total_count + $this->current_offset);
                    $current_user->setPreference('next_offset',$offset + $this->next_offset);
                    $current_user->setPreference('prev_offset',$this->lvs->lvd->getOffset());
                    $this->lvs->ss->assign('next_off', ($total_count + $this->current_offset) == $total_offsets ? true:false);
                    $current_user->setPreference('custom_prev_offset',$this->next_offset);
                //}
            }
        }
        //print_r(count($this->lvs->data['data']));
        foreach($this->lvs->data['data'] as $row => $data) {
            $mPlanned = $this->countQuery('meetings', 'Planned', $data['ID']);                      
            $mHeld = $this->countQuery('meetings', 'Held', $data['ID']);                 
            $mNotHeld = $this->countQuery('meetings', 'Not Held', $data['ID']);  
            $cPlanned = $this->countQuery('calls', 'Planned', $data['ID']);                        
            $cHeld = $this->countQuery('calls', 'Held', $data['ID']);           
            $cNotHeld = $this->countQuery('calls', 'Not Held', $data['ID']);
           // if( $mPlanned == 0 && (($mPlanned + $mHeld + $mNotHeld) == 0 ||  ($cPlanned + $cHeld + $cNotHeld) == 0)){
            if($mPlanned == 0 && $mHeld == 0 && $cPlanned == 0 && $cHeld == 0){
                $this->lvs->data['data'][$row]['EXCEPTION'] = "None scheduled";
            }
            $this->lvs->data['data'][$row]['MPLANNED'] = $mPlanned;
            $this->lvs->data['data'][$row]['MHELD'] = $mHeld;
            $this->lvs->data['data'][$row]['MNOTHELD'] = $mNotHeld;
            $this->lvs->data['data'][$row]['CPLANNED'] = $cPlanned;
            $this->lvs->data['data'][$row]['CHELD'] = $cHeld;
            $this->lvs->data['data'][$row]['CNOTHELD'] = $cNotHeld;
            $this->lvs->data['data'][$row]['TOTALPLANNED'] = $mPlanned + $cPlanned;
            $this->lvs->data['data'][$row]['TOTALHELD'] = $mHeld + $cHeld;
            $this->lvs->data['data'][$row]['TOTALNOTHELD'] = $mNotHeld + $cNotHeld;
            $this->lvs->data['data'][$row]['MTD_SALES_C'] = '$'.number_format(round($this->lvs->data['data'][$row]['MTD_SALES_C']),0,'.',',');
            $this->lvs->data['data'][$row]['YTD_SALES_C'] = '$'.number_format(round($this->lvs->data['data'][$row]['YTD_SALES_C']),0,'.',','); 
        }
        if(!$this->exceptions){
            $total_meetings['MPlanned'] = $this->totalCountException('meetings','Planned');
            $total_meetings['MHeld'] = $this->totalCountException('meetings','Held');
            $total_meetings['MNotHeld'] = $this->totalCountException('meetings','Not Held');
            $total_meetings['CPlanned'] = $this->totalCountException('calls','Planned');
            $total_meetings['CHeld'] = $this->totalCountException('calls','Held');
            $total_meetings['CNotHeld'] = $this->totalCountException('calls','Not Held');
            $total_sum = $this->totalSumQuery();
        }else{
            $excep_total = $this->totalExceptionCount();
            $total_meetings['MPlanned'] = 0;
            $total_meetings['MHeld'] = 0;
            $total_meetings['MNotHeld'] = $excep_total['MNotHeld'];
            $total_meetings['CPlanned'] = 0;
            $total_meetings['CHeld'] = 0;
            $total_meetings['CNotHeld'] = $excep_total['CNotHeld'];
            $total_sum =  array('MTDSales'=> $excep_total['MTDSales'],'YTDSales'=> $excep_total['YTDSales']);
        }
        
        $this->lvs->data['data'][$row+1]['MPLANNED'] = $total_meetings['MPlanned'];
        $this->lvs->data['data'][$row+1]['MHELD'] = $total_meetings['MHeld'];
        $this->lvs->data['data'][$row+1]['MNOTHELD'] = $total_meetings['MNotHeld'];
        $this->lvs->data['data'][$row+1]['CPLANNED'] = $total_meetings['CPlanned'];
        $this->lvs->data['data'][$row+1]['CHELD'] = $total_meetings['CHeld'];
        $this->lvs->data['data'][$row+1]['CNOTHELD'] = $total_meetings['CNotHeld'];
        $this->lvs->data['data'][$row+1]['TOTALPLANNED'] = $total_meetings['MPlanned'] + $total_meetings['CPlanned'];
        $this->lvs->data['data'][$row+1]['TOTALHELD'] = $total_meetings['MHeld'] + $total_meetings['CHeld'];
        $this->lvs->data['data'][$row+1]['TOTALNOTHELD'] = $total_meetings['MNotHeld'] + $total_meetings['CNotHeld'];
        $this->lvs->data['data'][$row+1]['MTD_SALES_C'] = '$'.number_format(round($total_sum['MTDSales']),0,'.',',');
        $this->lvs->data['data'][$row+1]['YTD_SALES_C'] = '$'.number_format(round($total_sum['YTDSales']),0,'.',','); 
        $this->lvs->data['data'][$row+1]['NAME'] = 'Total';
        $this->lvs->data['data'][$row+1]['CUSTNO_C'] = $total_offsets;
        $this->lvs->data['data'][$row+1]['EXCEPTION'] = $meet_tot;
        $this->lvs->data['data'] = array_merge(array($key => array_pop($this->lvs->data['data'])), $this->lvs->data['data']); // for moved "Total Row" on the top
    }
    
    function addCustomerFields($lvsData) {
        $total_count = 0;
        foreach($lvsData as $row => $data) {
            $mPlanned = $this->countQuery('meetings', 'Planned', $data['ID']);                      
            $mHeld = $this->countQuery('meetings', 'Held', $data['ID']);                 
           // $mNotHeld = $this->countQuery('meetings', 'Not Held', $data['ID']);  
            $cPlanned = $this->countQuery('calls', 'Planned', $data['ID']);                        
            $cHeld = $this->countQuery('calls', 'Held', $data['ID']);           
          //  $cNotHeld = $this->countQuery('calls', 'Not Held', $data['ID']);
          //  if( $mPlanned == 0 && (($mPlanned + $mHeld + $mNotHeld) == 0 ||  ($cPlanned + $cHeld + $cNotHeld) == 0)){
            if($mPlanned == 0 && $mHeld == 0 && $cPlanned == 0 && $cHeld == 0){
                $total_count++;
            }elseif($this->exceptions){
                unset($this->lvs->data['data'][$row]);
            }
        }
        return $total_count;
    }
    
    function buildWhere() {
        global $current_user;
        $returnArray = array();
        $MEET_reg = '';
        $MEET_loc = '';
        $slsmqry = '';
        $regqry = '';
        $locqry = '';
        $allarea = '';
        $dealerqry = '';

        $MEET_slsm_num = ($current_user->getPreference('MEET_slsm_num'))? $current_user->getPreference('MEET_slsm_num'): 'all';
        $MEET_reg_loc = ($current_user->getPreference('MEET_reg_loc'))? $current_user->getPreference('MEET_reg_loc'): 'all';
        $MEET_dealer = ($current_user->getPreference('MEET_dealer'))? $current_user->getPreference('MEET_dealer'): 'all';
        $MEET_customers =  $current_user->getPreference('MEET_customers');
        
	if (!in_array($MEET_reg_loc, array('all', 'undefined', '')) && is_array($MEET_reg_loc)) { 
		foreach($MEET_reg_loc as $reg_loc_value) {
			if(substr($reg_loc_value, 0, 1) == "r") {
				$MEET_reg[substr($reg_loc_value, 1)] = substr($reg_loc_value, 1);
			} else {
				$MEET_loc[$reg_loc_value] = $reg_loc_value;
			}
		}
	}
        
         if (isset($MEET_reg) && $MEET_reg != '' && $MEET_reg != 'undefined' && $MEET_reg != 'all' && $MEET_reg != 'allin' && count($MEET_reg) > 0) {
            $regqry .= " accounts.region_c IN(".implode(', ', $MEET_reg).") ";
            array_push($returnArray, $regqry);
        }

        if (isset($MEET_loc) && $MEET_loc != '' && $MEET_loc != 'undefined' && $MEET_loc != 'all' && $MEET_loc != 'allin' && count($MEET_loc) > 0) {
            $locqry .= " accounts.location_c IN(".implode(', ', $MEET_loc).") ";
            array_push($returnArray, $locqry);
        }
        if (isset($MEET_dealer) && $MEET_dealer != '' && $MEET_dealer != 'undefined' && $MEET_dealer != 'all' && $MEET_dealer != 'allin') {
            $dealer_list = array();
            $dealerqry .= " accounts.dealertype_c IN (".implode(", ", $MEET_dealer).") ";
            array_push($returnArray, $dealerqry);
        }
        
        if (isset($MEET_slsm_num) && $MEET_slsm_num != '' && $MEET_slsm_num != 'undefined' && $MEET_slsm_num != 'all' && $MEET_slsm_num != 'allin') {
            $MEET_slsm_num = array_unique(array_merge(array(421,422,423,424),$MEET_slsm_num));
        }else{
            $MEET_slsm_num = array(421,422,423,424);
        }    
                                   
        $is_user_id = 0;
        $slsm_obj = new fmp_Param_SLSM($current_user->id);
        $slsm_obj->init();

        $is_s = $slsm_obj->is_assigned_slsm();
        if ($is_s) {
            $arr = $MEET_slsm_num;
            $r_users = $slsm_obj->compile__available_slsm($arr);
            $str_selection_button = $this->build__slsm($r_users, $is_user_id);
            $slsmqry .= " accounts.slsm_c ".$str_selection_button." ";
            array_push($returnArray, $slsmqry);
        }
        unset($slsm_obj);
         
        if (isset($MEET_customers) && $MEET_customers[0] != '' && $MEET_customers[0] != 'undefined' ) {
            $customerqry = " accounts.id IN ('".implode("','", $MEET_customers)."') ";
           
            array_push($returnArray, $customerqry);
        }
        $filter = " accounts.name NOT LIKE '%New Business Budget%' AND accounts.name NOT LIKE '%New Fleet%' AND accounts.name NOT LIKE '%Incentive%' AND accounts.name NOT LIKE '%Cash Sales%' ";
        array_push($returnArray, $filter);
        return $returnArray;
    }
    
    function sortingByField($field, $table, $status){
        return $lvsParams = array(
            'custom_select' => ",$field ",
            'custom_from' => " LEFT JOIN (
                        SELECT $table.parent_id, COUNT( $table.id ) $field
                        FROM $table
                        WHERE $table.status =  '$status'
                        AND $table.deleted=0 
                        GROUP BY $table.parent_id
                        )tabl$table ON accounts.id = tabl$table.parent_id 
                        ",
            );
    }
    function buildDateRange($table){
        global $current_user;
        $MEET_date_start =  $current_user->getPreference('MEET_date_range_start');
        $MEET_date_end =  $current_user->getPreference('MEET_date_range_end');
        $rqs_start_date = '';
        if(!empty($MEET_date_start) && !empty($MEET_date_end)) {
            $date_start_unix = strtotime($MEET_date_start);
            $date_end_unix = strtotime($MEET_date_end);
           
        }else{
            $days = date("t");
            $date_start_unix = strtotime(date("m/01/Y"));
            $date_end_unix = strtotime(date("m/$days/Y"));
        }
        if($date_start_unix && $date_end_unix){
            $rqs_start_date .= ' AND UNIX_TIMESTAMP('.$table.'.date_start) >=  \''.$date_start_unix.'\'';
            $rqs_start_date .=  ' AND UNIX_TIMESTAMP('.$table.'.date_start) <=  \''.$date_end_unix.'\' ';
        }
        return $rqs_start_date;
    }
    function countQuery($table, $status, $account_id) {
        $rqs_start_date = $this->buildDateRange($table);
        $seed_bean  = new Account();
        $query = "SELECT COUNT( $table.id ) as '$table$status'
                         FROM $table
                         WHERE $table.status =  '$status'
                         AND $table.parent_id = '$account_id' 
                         AND $table.deleted=0 $rqs_start_date";
        $result = $seed_bean->db->query($query);
        $count = $seed_bean->db->fetchByAssoc($result);
        return $count[$table.$status];
        
    }
    
    function totalSumQuery() {
        $total_info = array();
        $seed_bean  = new Account();
        
        $whereArray = $this->buildWhere();
        $where = '';
        if(!empty($whereArray)){
            $where = '(' . implode(') AND (', $whereArray) . ')';
        }
        $ret_array = $seed_bean->create_new_list_query('ASC', $where, array(), array(), 0, '', true, $seed_bean, true);
        $query = "SELECT  SUM(mtd_sales_c) as MTDSales,  SUM(ytd_sales_c) as YTDSales FROM accounts ".$ret_array['where'];
        $result = $seed_bean->db->query($query);
        $total_info = $seed_bean->db->fetchByAssoc($result);
        return $total_info;
    }
    
    function totalCountQuery() {
        $total_info = array();
        $seed_bean  = new Account();
        
        $whereArray = $this->buildWhere();
        $where = '';
        if(!empty($whereArray)){
            $where = '(' . implode(') AND (', $whereArray) . ')';
        }
        $ret_array = $seed_bean->create_new_list_query('ASC', $where, array(), array(), 0, '', true, $seed_bean, true);
        $query = "SELECT  COUNT(accounts.id) as Total FROM accounts ".$ret_array['where'];
        $result = $seed_bean->db->query($query);
        $total_info = $seed_bean->db->fetchByAssoc($result);
        return $total_info['Total'];
    }
    
    function totalCountException($table, $status){
        $total_info = array();
        $seed_bean  = new Account();
        $whereArray = $this->buildWhere();
        $where = '';
        if(!empty($whereArray)){
            $where = '(' . implode(') AND (', $whereArray) . ')';
        }
        $ret_array = $seed_bean->create_new_list_query('ASC', $where, array(), array(), 0, '', true, $seed_bean, true);
        $rqs_start_date = $this->buildDateRange($table);
        $query = "SELECT  count($table.parent_id) as total FROM accounts  
                    LEFT JOIN $table on (accounts.id = $table.parent_id )
                    ".$ret_array['where']."
                    and $table.status = '$status' $rqs_start_date and $table.deleted=0 ";
        
        $result = $seed_bean->db->query($query);
        $total_info = $seed_bean->db->fetchByAssoc($result);
        return $total_info['total'];
    }
    
    function totalExceptionCount(){
         $total_info = array();
        $seed_bean  = new Account();
        $whereArray = $this->buildWhere();
        $where = '';
        if(!empty($whereArray)){
            $where = '(' . implode(') AND (', $whereArray) . ')';
        }
        $ret_array = $seed_bean->create_new_list_query('ASC', $where, array(), array(), 0, '', true, $seed_bean, true);
        $rqs_start_date_meet = $this->buildDateRange('meetings');
        $rqs_start_date_call = $this->buildDateRange('calls');
        
        $query =  "SELECT
                SUM(IFNULL(mNotHeld,0)) as MNotHeld, 
                SUM(IFNULL(cNotHeld,0)) as CNotHeld,
                SUM(accounts.mtd_sales_c) as MTDSales,  
                SUM(accounts.ytd_sales_c) as YTDSales
                FROM accounts  LEFT JOIN (
                        SELECT meetings.parent_id, COUNT( meetings.id ) mPlanned
                        FROM meetings
                        WHERE meetings.status =  'Planned'
                        AND meetings.deleted=0
                        $rqs_start_date_meet
                        GROUP BY meetings.parent_id
                        )meet1 ON accounts.id = meet1.parent_id 
                        
                        LEFT JOIN (
                        SELECT meetings.parent_id, COUNT( meetings.id ) mHeld
                        FROM meetings
                        WHERE meetings.status =  'Held'
                        AND meetings.deleted=0
                        $rqs_start_date_meet
                        GROUP BY meetings.parent_id
                        )meet2 ON accounts.id = meet2.parent_id 
                        
                        LEFT JOIN (
                        SELECT meetings.parent_id, COUNT( meetings.id ) mNotHeld
                        FROM meetings
                        WHERE meetings.status =  'Not Held'
                        AND meetings.deleted=0
                        $rqs_start_date_meet
                        GROUP BY meetings.parent_id
                        )meet3 ON accounts.id = meet3.parent_id
                        
                        LEFT JOIN (
                        SELECT calls.parent_id, COUNT( calls.id ) cPlanned
                        FROM calls
                        WHERE calls.status =  'Planned'
                        AND calls.deleted=0
                        $rqs_start_date_call
                        GROUP BY calls.parent_id
                        )call1 ON accounts.id = call1.parent_id 
                        
                        LEFT JOIN (
                        SELECT calls.parent_id, COUNT( calls.id ) cHeld
                        FROM calls
                        WHERE calls.status =  'Held'
                        AND calls.deleted=0
                        $rqs_start_date_call
                        GROUP BY calls.parent_id
                        )call2 ON accounts.id = call2.parent_id
                        
                        LEFT JOIN (
                        SELECT calls.parent_id, COUNT( calls.id ) cNotHeld
                        FROM calls
                        WHERE calls.status =  'Not Held'
                        AND calls.deleted=0
                        $rqs_start_date_call
                        GROUP BY calls.parent_id
                        )call3 ON accounts.id = call3.parent_id 
                        ".$ret_array['where']."
                        AND (IFNULL(mPlanned,0) = 0 and IFNULL(mHeld,0) = 0) AND (IFNULL(cPlanned,0) = 0 and IFNULL(cHeld,0) = 0) ";

        $result = $seed_bean->db->query($query);
        $total_info = $seed_bean->db->fetchByAssoc($result);
        
        return $total_info;
    }
    
    function totalExceptionTable(){
         $total_info = array();
        $seed_bean  = new Account();
        $whereArray = $this->buildWhere();
        $where = '';
        if(!empty($whereArray)){
            $where = '(' . implode(') AND (', $whereArray) . ')';
        }
        $ret_array = $seed_bean->create_new_list_query('ASC', $where, array(), array(), 0, '', true, $seed_bean, true);
        $rqs_start_date_meet = $this->buildDateRange('meetings');
        $rqs_start_date_call = $this->buildDateRange('calls');
        
        $query =  "SELECT  SUM(IF((IFNULL(mPlanned,0) = 0 and IFNULL(mHeld,0) = 0) AND (IFNULL(cPlanned,0) = 0 and IFNULL(cHeld,0) = 0),1,0)) as ExceptionTotal
                FROM accounts  LEFT JOIN (
                        SELECT meetings.parent_id, COUNT( meetings.id ) mPlanned
                        FROM meetings
                        WHERE meetings.status =  'Planned'
                        AND meetings.deleted=0
                        $rqs_start_date_meet
                        GROUP BY meetings.parent_id
                        )meet1 ON accounts.id = meet1.parent_id
                        
                        LEFT JOIN (
                        SELECT meetings.parent_id, COUNT( meetings.id ) mHeld
                        FROM meetings
                        WHERE meetings.status =  'Held'
                        AND meetings.deleted=0
                        $rqs_start_date_meet
                        GROUP BY meetings.parent_id
                        )meet2 ON accounts.id = meet2.parent_id
                        
                        LEFT JOIN (
                        SELECT calls.parent_id, COUNT( calls.id ) cPlanned
                        FROM calls
                        WHERE calls.status =  'Planned'
                        AND calls.deleted=0
                        $rqs_start_date_call
                        GROUP BY calls.parent_id
                        )call1 ON accounts.id = call1.parent_id
                        
                        LEFT JOIN (
                        SELECT calls.parent_id, COUNT( calls.id ) cHeld
                        FROM calls
                        WHERE calls.status =  'Held'
                        AND calls.deleted=0
                        $rqs_start_date_call
                        GROUP BY calls.parent_id
                        )call2 ON accounts.id = call2.parent_id
                        
                        ".$ret_array['where'];
        $result = $seed_bean->db->query($query);
        $total_info = $seed_bean->db->fetchByAssoc($result);
        
        return $total_info['ExceptionTotal'];
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
        $select_creater = '<select id="meet_fmp_dealer_type" size="10" multiple="multiple" style="width: 170px;">';
        $select_creater .= '<option value="all" style="border-bottom: 2px solid grey;">ALL</option>';
        foreach ($dealer_list as $key=>$value) {
            //if($key!='') {
                $select_creater .= '<option value="'.$key.'">'.$value.'</option>';
            //}
        }
        $select_creater .= '</select>';
        return $select_creater;
    }

    function get_quicksearch_js () {
        require_once 'include/QuickSearchDefaults.php';
        $qsd = new QuickSearchDefaults();

        $o = $qsd->getQSParent();
        $o['field_list'][] = 'custno_c';
        $o['expanded_name'] = 1;
        $o['populate_list'] = array('account_name_filter', "account_name_custno_filter", "account_id_filter");
        $sqs_objects['account_name_filter'] = $o;

        $o = $qsd->getQSParent();
        $o['field_list'] = array('custno_c', 'name', 'id');
        $o['expanded_name'] = 1;
        $o['populate_list'] = array('account_name_custno_filter', "account_name_filter", "account_id_filter");
        $o['conditions'][0]['name'] = 'custno_c';
        $o['order'] = 'custno_c';
        $sqs_objects['account_name_custno_filter'] = $o;
        
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
