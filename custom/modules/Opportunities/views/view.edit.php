<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

require_once('include/MVC/View/views/view.edit.php');

class OpportunitiesViewEdit extends ViewEdit {

    function OpportunitiesViewEdit() {
        parent::ViewEdit();
        $this->useForSubpanel = true;
    }

    function shortcut_get_sales_reps($current_user) {
        $str = '<select id="opp_sales_reps_list" size="10" multiple="multiple" style="width: 170px;" onclick="javaScript:get_date_for_table()">';
        $ids = array($current_user->id);
        $o = new fmp_Param_SLSM($current_user->id);
        $o->init();
        $str .= get_select_options_with_id($o->get_sales_reps_array(), '');
        unset($o);
        $str .= '</select>';
        return $str;
    }

    function display() {
        global $app_list_strings, $current_user;
        $json = getJSONobj();
        $prob_array = $json->encode($app_list_strings['sales_probability_dom']);
        $prePopProb = '';
        if (empty($this->bean->id))
            $prePopProb = 'document.getElementsByName(\'sales_stage\')[0].onchange();';

        $probability_script = <<<EOQ
    <script>
    prob_array = $prob_array;
    document.getElementsByName('sales_stage')[0].onchange = function() {
            if(typeof(document.getElementsByName('sales_stage')[0].value) != "undefined" && prob_array[document.getElementsByName('sales_stage')[0].value]) {
                document.getElementsByName('probability')[0].value = prob_array[document.getElementsByName('sales_stage')[0].value];
            } 
        };
    $prePopProb
    </script>
EOQ;

        $this->ss->assign('PROBABILITY_SCRIPT', $probability_script);
//        parent::display();

        require_once 'include/QuickSearchDefaults.php';
        $qsd = new QuickSearchDefaults();

        $o = $qsd->getQSParent();
        $o['field_list'][] = 'custno_c';
        $o['populate_list'][2] = 'account_id';
        $o['populate_list'][1] = 'account_name_custno';
        $sqs_objects['account_name'] = $o;

        $o = $qsd->getQSParent();
        $o['field_list'] = array('custno_c', 'name', 'id');
        $o['populate_list'] = array('account_name_custno', "account_name", "account_id");
        $o['conditions'][0]['name'] = 'custno_c';
        $o['order'] = 'custno_c';
        $sqs_objects['account_name_custno'] = $o;
        
        $o = $qsd->getQSParent();
        $o['field_list'][] = 'custno_c';
        $o['populate_list'][2] = 'account_id_filter';
        $o['populate_list'][1] = 'account_name_custno_filter';
        $sqs_objects['account_name_filter'] = $o;

        $o = $qsd->getQSParent();
        $o['field_list'] = array('custno_c', 'name', 'id');
        $o['populate_list'] = array('account_name_custno_filter', "account_name_filter", "account_id_filter");
        $o['conditions'][0]['name'] = 'custno_c';
        $o['order'] = 'custno_c';
        $sqs_objects['account_name_custno_filter'] = $o;
        
        $o = $qsd->getQSUser();
        $o['populate_list'] = array('assigned_user_name1', "assigned_user_id1");
        $sqs_objects['assigned_user_name1'] = $o;
        
//print_r($qsd->getQSParent('Opportunities'));
        $o = $qsd->getQSParent('Opportunities');
        $opp_fields = array('month_sales','gp_dollar',
            'gp_perc','month_gp_perc','opportunity_type','lead_source');
        $o['field_list'] = array_merge(array('name','amount'),$opp_fields,array('id'));
        $o['populate_list'] = array_merge(array('name1','amount1'),$opp_fields,array('opp_id'));
        $sqs_objects['name1'] = $o;

        $json = getJSONobj();
        $quicksearch_js = array();
        foreach ($sqs_objects as $sqsfield => $sqsfieldArray) {
                $quicksearch_js[] = "sqs_objects['$sqsfield']={$json->encode($sqsfieldArray)};";
        }
        $quicksearch_js = ''
                . '<script language="javascript">'
                . "if(typeof sqs_objects == 'undefined'){var sqs_objects = new Array;}"
                . implode("\n", $quicksearch_js)
                . '</script>'
        ;
        $quicksearch_js .= '' . '<script src="modules/Calendar2/js/jquery-ui-1.7.2.custom.min.js?s=5.2.0f&amp;c=" type="text/javascript"></script><link rel="stylesheet" href="modules/Calendar2/css/themes/base/ui.all.css" type="text/css">
                                <script src="custom/modules/Accounts/jquery.datatables.min.js" type="text/javascript"></script>
                                <script src="custom/modules/Accounts/ColVis.js" type="text/javascript"></script>
                                <link rel="stylesheet" type="text/css" href="custom/modules/Accounts/datatables.css" />';
        $quicksearch_js .= '' . '<script src="modules/Opportunities/tpls/jquery.autocomplete.js" type="text/javascript"></script>';
        $quicksearch_js .= '' . '<script src="include/javascript/sugar_grp_overlib.js" type="text/javascript"></script>';
        $quicksearch_js .= '' . '<link rel="stylesheet" type="text/css" href="modules/Opportunities/tpls/jquery.autocomplete.css" />';
        $quicksearch_js .= '' . '<link rel="stylesheet" type="text/css" href="include/javascript/yui/assets/container.css" />';
        //$quicksearch_js .= '' . '<link rel="stylesheet" type="text/css" href="/modules/Opportunities/tpls/sexy.css" />';
        $quicksearch_js .= '' . '<script>
$(document).ready(function() {

/*$(function() {
        				$("form#EditView #name1").autocomplete({url: "index.php?module=Opportunities&action=getOpportunities&return_module=Opportunities&return_action=DetailView",
								remoteDataType: "json",
								onItemSelect: function(item) {
								$.get("index.php?module=Opportunities&action=getOpportunitiesPL&return_module=Opportunities&return_action=DetailView&record_id="+item.data[0], function(data) {
                                                                var pl_value = [];
                                                                var pid = "";
                                                                //console.log(data);
                                                                for(var key in data) {
                                                                pl_value[pl_value.length] = data[key].pid + "," + data[key].pcat  + "," + data[key].pcode;
                                                                }
                                                                var pl_value_input = ( ( pl_value instanceof Array ) ? pl_value.join ( "|" ) : pl_value );

                                                                $("#product_line_hidden").val(pl_value_input);
                                                                YAHOO.util.Event.onContentReady("pline_c", pline_init);
                                                                }, "json");
								}
								});
      				});*/

	$("form#EditView #sales_stage").change(function() {
		var stage = $(this).val();
		if(stage == "Closed Won" ) {
			$("form#EditView #probability").val(100);
			var dateObj = new Date();
			var curr_date = dateObj.getDate();
			var curr_month = dateObj.getMonth();
			curr_month++;
			var curr_year = dateObj.getFullYear();
			$("form#EditView #date_closed").val(curr_month + "/" + curr_date + "/" + curr_year);		
		}else if(stage == "Closed Lost") {
			var dateObj = new Date();
			var curr_date = dateObj.getDate();
			var curr_month = dateObj.getMonth();
			curr_month++;
			var curr_year = dateObj.getFullYear();
			$("form#EditView #date_closed").val(curr_month + "/" + curr_date + "/" + curr_year);
			$("form#EditView #probability").val(0);
		}
                                    if(stage == "Closed Promotion Ended" || stage == "Active Promotion") {
                                         $("form#EditView #start_date_row").show();
                                        if($("form#EditView #date_start_c").val() == ""){
                                            if($("form#EditView #date_start_c").attr("oldValue") == ""){
                                                var dateObj = new Date();
                                                var curr_date = dateObj.getDate();
                                                var curr_month = dateObj.getMonth();
                                                curr_month++;
                                                var curr_year = dateObj.getFullYear();
                                                $("form#EditView #date_start_c").val(curr_month + "/" + curr_date + "/" + curr_year);
                                                $("form#EditView #date_start_c").attr("oldValue",curr_month + "/" + curr_date + "/" + curr_year);
                                            }else{
                                                $("form#EditView #date_start_c").val($("form#EditView #date_start_c").attr("oldValue"));
                                            }
                                        }
                                        if($("form#EditView #date_start_c_popup").val() == ""){
                                            if($("form#EditView #date_start_c_popup").attr("oldValue") == ""){
                                                var dateObj = new Date();
                                                var curr_date = dateObj.getDate();
                                                var curr_month = dateObj.getMonth();
                                                curr_month++;
                                                var curr_year = dateObj.getFullYear();
                                                $("form#EditView #date_start_c_popup").val(curr_month + "/" + curr_date + "/" + curr_year);
                                                $("form#EditView #date_start_c_popup").attr("oldValue",curr_month + "/" + curr_date + "/" + curr_year);
                                            }else{
                                                $("form#EditView #date_start_c_popup").val($("form#EditView #date_start_c_popup").attr("oldValue"));
                                            }
                                        }
                                    }else{
                                        if($("form#EditView #date_start_c").val() != ""){
                                            $("form#EditView #date_start_c").attr("oldValue",$("form#EditView #date_start_c").val());
                                        }
                                         if($("form#EditView #date_start_c_popup").val() != ""){
                                            $("form#EditView #date_start_c_popup").attr("oldValue",$("form#EditView #date_start_c_popup").val());
                                        }
                                        $("form#EditView #start_date_row").hide();
                                        $("form#EditView #date_start_c").val("");
                                        $("form#EditView #date_start_c_popup").val("");
                                    }
	});

});                        
</script>';

        
        
        ///////////////////////////////////////////////Customer List/////////////////START

        require_once 'custom/modules/Opportunities/QuickInputClass.php';
        require_once('modules/ZuckerReportParameter/fmp.class.param.slsm.php');
        require_once('modules/ZuckerReportParameter/fmp.class.param.regloc.php');
        $qi_obj = new QuickInputClass();
        $script = $qi_obj->scripts_for_display();
        



        $dealer_list = $qi_obj->get_dealer_type($app_list_strings['fmp_dealertype_list']);
        $is_user_id = 0;
        $slsm_obj = new fmp_Param_SLSM($current_user->id);

        $slsm_obj->init();

        $is_s = $slsm_obj->is_assigned_slsm();
        if ($is_s) {
//            if(isset($_POST['slsm_num'])) {
            if (isset($_POST['slsm_num']))
                ;
            $arr = Array(0 => null);
//            }
            $r_users = $slsm_obj->compile__available_slsm($arr);
            $str_selection_button = $qi_obj->build__slsm($r_users, $is_user_id);
        }
        $slsm_tree_list = $slsm_obj->html_for_daily_sales('onclick="javaScript:get_date_for_table()"', '');  // prepeare SLSM list for display
        $reps_list = $this->shortcut_get_sales_reps($current_user);
        unset($slsm_obj);
        $slsm_area_obj = new fmp_Param_RegLoc($current_user->id);
        $slsm_area_obj->init($current_user->id);
        $area_list = $slsm_area_obj->html_for_daily_sales($current_user->id, 'onclick="javaScript:get_date_for_table()"');


        $call_list = '<div class="yui-skin-sam-fmp-sales">
                                <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="area_list_show" style="float: left;">
                                    <span class="first-child-fmp-sales"><button type="button" id="yui-gen0-button" >Area</button>
                                        <div id="area_panel" class="filter_panel">
                                            ' . $area_list . '
                                        </div>
                                    </span>
                                </span>
                                <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="slsm_list_show" style="float: left;">
                                        <span class="first-child-fmp-sales"><button type="button" id="yui-gen2-button" >Slsm</button>
                                            <div id="slsm_panel" class="filter_panel">
                                                ' . $slsm_tree_list . '
                                            </div>
                                        </span>
                                </span>

                                <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="dealer_list_show" style="float: left;">
                                        <span class="first-child-fmp-sales"><button type="button" id="yui-gen4-button" >Customer Type</button>
                                            <div id="dealer_panel" class="filter_panel">
                                                ' . $dealer_list . '
                                            </div>
                                        </span>
                                </span>

                                <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="meet_customer_list_show" style="float: left;">
                                <span class="first-child-fmp-sales"><button type="button" id="yui-gen5-button" >Customer</button>    
                                <div id="meet_customer_panel" class="filter_panel"> 
                                    <div id="filter_list_show" style="float: right;">
                                        <button type="button" class="default" class="show_filter" title="customer filter list"></button>
                                        <div id="filter_panel" class="filter_panel">
                                            <p id="meet_customers">All</p>
                                        </div>    
                                    </div><label for="account_name_custno_filter" style="display: inline-block; width: 90px; font-size: 12px; color: #000000;">Account Name: </label>
                                    <input type="text" name="account_name_custno_filter" id="account_name_custno_filter" tabindex="1" class="sqsCustom" size="6" value="" autocomplete="off">
                                    <input type="text" name="account_name_filter" class="sqsCustom" tabindex="2" id="account_name_filter" size="32" value="" autocomplete="off">
                                    <input type="hidden" name="account_id_filter" id="account_id_filter" value="">
                                    <input type="button" style="font-size: 12px; width: 40px;" title="Clear" class="button" onclick="javaScript:clear_account()" value="Clear">
                                    <input type="button" style="font-size: 12px; width: 40px;" title="Reset" onClick="javaScript:reset_account_filter();" class="button" value="Reset">
                                    </br>
                                </div>
                                
                                </span></span>
                                <!--<span  style="float: left;">
                                    Username <input id="call_list_username" type="text" value="" onblur="get_date_for_table();">
                                </span>-->
                                <span>
                                    City <input id="call_list_city" type="text" value="" onblur="get_date_for_table();">
                                </span>
                                <span>
                                    State <input id="call_list_state" type="text" value="" onblur="get_date_for_table();">
                                </span>
                                <span>
                                    Zip/Postal Code <input id="call_list_postalcode" type="text" value="" onblur="get_date_for_table();">
                                </span>
                           </div></div>
                           ';

        //$call_list = str_replace("'", '"', $call_list);
        //$call_list = str_replace(PHP_EOL, '', $call_list);

        $this->ev->th->ss->assign('popup_massopp_js', $script);
        $this->ev->th->ss->assign('popup_massopp_filter', $call_list);
        
        ///////////////////////////////////////////////Customer List/////////////////END
        

        $this->ev->th->ss->assign('quicksearch_js', $quicksearch_js);

        $this->ev->th->ss->assign('probability_options', array(0 => '0%', 25 => '25%', 50 => '50%', 75 => '75%', 100 => '100%',));
        //$this->ev->th->ss->assign('close_date_default', date("m/d/Y", strtotime('last day of next month')));
        $this->ev->th->ss->assign('close_date_default', date("m/d/Y", strtotime('-1 second',strtotime('+3 month',strtotime(date('m').'/01/'.date('Y').' 00:00:00')))));

        /* 						ajaxData: {module:"Opportunities",
          action: "getOpportunities",
          return_module :"Opportunities",
          return_action:"DetailView",
          maxItemsToShow:10},
          } */


        $this->ev->process();
        if ($this->ev->isDuplicate) {

            foreach ($this->ev->fieldDefs as $name => $defs) {
                if (!empty($defs['auto_increment'])) {
                    $this->ev->fieldDefs[$name]['value'] = '';
                }
            }
        }

        if (isset($_REQUEST['record']) && $_REQUEST['record'] != '') {
            require_once("modules/Calls/Call.php");
            require_once("modules/Meetings/Meeting.php");
            $meeting_bean = new Meeting();
            $call_bean = new Call();
            if (!empty($_REQUEST['record'])) {
                $meeting_bean->retrieve($_REQUEST['record']);
                $call_bean->retrieve($_REQUEST['record']);
            }
//                echo '<pre>';
//                print_r($this->ev->fieldDefs);
//                echo '<br/>';
//                print_r('call'.$call_bean->id);
//                echo '</pre>';

            if ($meeting_bean->id != '') {
                foreach ($this->ev->fieldDefs as $name => $defs) {
                    //if($name == 'name') $this->ev->fieldDefs[$name]['value'] = $meeting_bean->name;
                    if ($name == 'account_name')
                        $this->ev->fieldDefs[$name]['value'] = $meeting_bean->parent_name;
                    if ($name == 'account_id')
                        $this->ev->fieldDefs[$name]['value'] = $meeting_bean->parent_id;
                    if ($name == 'description')
                        $this->ev->fieldDefs[$name]['value'] = $meeting_bean->outcome_c;
                }
            }
            if ($call_bean->id != '') {
                foreach ($this->ev->fieldDefs as $name => $defs) {
                    //if($name == 'name') $this->ev->fieldDefs[$name]['value'] = $call_bean->name;
                    if ($name == 'account_name')
                        $this->ev->fieldDefs[$name]['value'] = $call_bean->parent_name;
                    if ($name == 'account_id')
                        $this->ev->fieldDefs[$name]['value'] = $call_bean->parent_id;
                    if ($name == 'description')
                        $this->ev->fieldDefs[$name]['value'] = $call_bean->outcome_c;
                }
            }
        }





        $this->ev->display($this->showTitle);

        $tpl = 'custom/modules/Opportunities/EditView.html';
        echo $this->ev->th->ss->fetch($tpl);
    }

}
