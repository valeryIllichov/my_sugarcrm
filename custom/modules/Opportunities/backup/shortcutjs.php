<?php

/* require_once 'modules/Calendar2/QuickInputClass.php';
  require_once('modules/ZuckerReportParameter/fmp.class.param.slsm.php');
  require_once('modules/ZuckerReportParameter/fmp.class.param.regloc.php');
  $qi_obj = new QuickInputClass();
  $script = $qi_obj->scripts_for_display();

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
  $reps_list = shortcut_get_sales_reps($current_user);
  unset($slsm_obj);
  $slsm_area_obj = new fmp_Param_RegLoc($current_user->id);
  $slsm_area_obj->init($current_user->id);
  $area_list = $slsm_area_obj->html_for_daily_sales($current_user->id, 'onclick="javaScript:get_date_for_table()"'); */

/*
  $call_list .= '<div class="yui-skin-sam-fmp-sales">
  <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="area_list_show">
  <span class="first-child-fmp-sales"><button type="button" id="yui-gen0-button" >Area</button>
  <div id="area_panel" style="display: none; position: absolute;">
  ' . $area_list . '
  </div>
  </span>
  </span>
  <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="slsm_list_show">
  <span class="first-child-fmp-sales"><button type="button" id="yui-gen2-button" >Slsm</button>
  <div id="slsm_panel" style="display: none; position: absolute; background-color: #FFFFFF; border: 1px solid #94C1E8;">
  ' . $slsm_tree_list . '
  </div>
  </span>
  </span>

  <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="dealer_list_show">
  <span class="first-child-fmp-sales"><button type="button" id="yui-gen4-button" >Customer Type</button>
  <div id="dealer_panel" style="display: none; position: absolute;">
  ' . $dealer_list . '
  </div>
  </span>
  </span>

  <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="reps_list_show">
  <span class="first-child-fmp-sales"><button type="button" id="yui-gen4-button" >Sales Reps</button>
  <div id="reps_panel" style="display: none; position: absolute;">
  ' . $reps_list . '
  </div>
  </span>
  </span>

  <span>
  Username <input id="call_list_username" type="text" value="" onblur="get_date_for_table();">
  </span>
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

  $call_list = str_replace("'", '"', $call_list);
  $call_list = str_replace(PHP_EOL, '', $call_list);
 */
//$('.form-bottom-div-filter').html('$call_list');
//$('form#EditView div.form-bottom-div-table').html('<table id="customers_list" style="width: 100%"><thead><th style="width: 30px;">Include</th><th>CustNo</th><th style="width: 300px;">CustName</th><th style="width: 30px;">Address</th><th style="width: 30px;">City</th><th style="width: 30px;">State</th><th style="width: 30px;">PostalCode</th><th>Contact</th><th>Phone</th><th  style="width: 30px;">MTD Sales</th><th  style="width: 30px;">MTD Proj vs. Budget</th><th  style="width: 30px;">YTD Proj vs. Budget</th><th class="status-table-header" style="width: 30px;">Status</th><th class="pre-call-plan-table-header" style="width: 300px;">Pre-Call Plan</th><th class="outcome-table-header" style="width: 300px;">Outcome</th></thead><tbody><tr><td colspan="11" class="dataTables_empty">Loading data from server</td></tr></tbody></table>');
//<link rel="stylesheet" type="text/css" href="custom/modules/Opportunities/javascript/container.css" />
$js = <<<EOQ

   <script>
    YAHOO.namespace("popup.container");
    $(document).ready(function () { 
    
    $(".footer").html('<div id="OPPquickInput"><div class="hd">Create Opportunities</div><div class="bd"></div><div style="background-image: url(include/javascript/yui/assets/flscreen.gif); margin-right: 20px;" class=" close nonsecure fullscreen">&nbsp;</div></div>');    
    $("div.footer div#OPPquickInput").hide();
    $("div#OPPquickInput div.bd").load("index.php?module=Opportunities&action=EditView&return_module=Opportunities&return_action=DetailView&sugar_body_only=true&masscreate=true"); 
    
    SUGAR.util.evalScript($("div#OPPquickInput").html());
    

    $("div#OPPquickInput div.fullscreen").click(function () {
        //alert(YAHOO.popup.container.OPPquickInput.cfg.getProperty("constraintoviewport"));
        if(YAHOO.popup.container.OPPquickInput.cfg.getProperty("constraintoviewport") == true) {
            
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("constraintoviewport", false);
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("width", $(window).width());
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("height", $(window).height());
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("x", 0);
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("y", 0);
        }else {
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("constraintoviewport", true);
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("width", $(window).width()-200);
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("height", $(window).height()-200);
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("x", null);
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("y", null);
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("fixedcenter", true);
        }
    });

    // Define various event handlers for Dialog
    var handleSubmit = function() {
	if($("#customers_list :checkbox:checked").length == 0) {
            alert("You need select at least 1 customer from list to procced!");
            return false;
        }
        
        $("div#OPPquickInput form#EditView[name=action]").val('Save');
        $("#customers_list :checkbox:checked").each(function(k, v) {
            $("div#OPPquickInput form#EditView input#account_name").val($(v).attr("parent_name"));
            $("div#OPPquickInput form#EditView input#account_id").val(v.id);
           
            //return check_form('EditView');
            $.post("index.php", $("div#OPPquickInput form#EditView").serialize());

        });
        
        YAHOO.popup.container.OPPquickInput.hide();
        
        //location.reload();
    };


             window.setTimeout(function () {
               /*$(".OPPquickInput").dialog({
                       title: "MassOpportunities",
                       modal: false,
                       autoOpen: false,
                       position: "left",
                       dialogClass: "opp_dialog",
                       width: "auto",
                       height: "auto"
               });*/
               
                YAHOO.popup.container.OPPquickInput = new YAHOO.widget.Dialog("OPPquickInput", 
							{ height: 600,
                                                          fixedcenter : true,
                                                          modal: true,
                                                          close:true, 
							  visible : false, 
                                                          draggable: true,
                                                          autofillheight: "body",
                                                          monitorresize: true,
							  constraintoviewport : true,
                                                          x: 0,
                                                          y: 0,
                                                          width: ($(window).width()-200),
                                                          height: ($(window).height()-200),
							  buttons : [ { text:"Create Opportunities", handler:handleSubmit, isDefault:true } ]
							 } );
                 YAHOO.popup.container.OPPquickInput.render();           
                   
            }, 3000);
            



     });
    
        function OpportunityQIPopup() {  
             $("div.footer div#OPPquickInput").show();
             //$(".OPPquickInput").dialog("show");
             //popup_opport.render();
             YAHOO.popup.container.OPPquickInput.show();
             var url = "index.php?module=Accounts&action=getCustomersDefaultOpp";
                 
                 $("#customers_list").dataTable({
                                "bJQueryUI": true,
                                "bDestroy": true,
                                //"bProcessing": true,
                                "bServerSide": true,
                                "sAjaxSource": url,
                                "aLengthMenu": [
                                [10, 20, 30, 50, 99999999],
                                [10, 20, 30, 50, "All"]
                                ],
                                "sPaginationType": "full_numbers"
                 });
        }

        


      

    </script>
     

EOQ;
print $js;