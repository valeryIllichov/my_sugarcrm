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
    
    var selected_customers = new Array();
    
    $(document).ready(function () { 
   
    sessionStorage.removeItem("accounts_filter");
    sessionStorage.removeItem("custno_filter");
    var print_link = '<div id="multiopp-print" class="close" >'+
                    '<span class=\'printLink\'><img src=\'themes/Sugar/images/print.gif\' width=\'13\' height=\'13\' alt=\'Print\' border=\'0\' align=\'absmiddle\'></span>&nbsp;'+
                    '<span class=\'printLink\'>Print</span><span class=\'ui-icon list-icon\'></span>'+
                    '<ul class="print-list"><li><a  href="javascript:printDiv2(\'customers_list_wrapper\',1);" class=\'utilsLink\'>Table</a></li>'+
                    '<li><a  href="javascript:printDiv2(\'customers_list_wrapper\',2);" class=\'utilsLink\'>Filters and table</a></li>'+
                    '<li><a  href="javascript:printDiv2(\'customers_list_wrapper\',3);" class=\'utilsLink\'>Only checked</a></li></ul></div>';
   
$(".footer").html('<div id="OPPquickInput"><div id="top-nav-multiopp" class="hd" style="width:100%;position: fixed;z-index: 99;">Create Opportunities'+print_link+'<div id="fullscreen-multiopp" style="background-image: url(include/javascript/yui/assets/flscreen.gif); margin-right: 20px;" class=" close nonsecure fullscreen">&nbsp;</div><div id="close-btn" class="  close nonsecure">&nbsp;</div></div><div class="bd"></div></div>');    
    $("div.footer div#OPPquickInput").hide();
    $("div#OPPquickInput div.bd").load("index.php?module=Opportunities&action=EditView&return_module=Opportunities&return_action=DetailView&sugar_body_only=true&masscreate=true"); 
    
    SUGAR.util.evalScript($("div#OPPquickInput").html());
    
    $("#multiopp-print").click(function () {
        if($(".print-list").css("display") == "none"){
            $(".print-list").show();
        }else{
            $(".print-list").hide();
        }
    });
    $("div#OPPquickInput div#close-btn").click(function () {
        YAHOO.popup.container.OPPquickInput.cancel ( );
    });
    $("div#OPPquickInput div.fullscreen").click(function () {
        if(YAHOO.popup.container.OPPquickInput.cfg.getProperty("constraintoviewport") == true) {
            $("div#top-nav-multiopp").attr("style","width:100%;position: fixed;z-index: 99;");
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("constraintoviewport", false);
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("width", $(window).width());
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("height", $(window).height());
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("x", 0);
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("y", 0);
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("fixedcenter", false);
        }else {
            var w = $(window).width()-200-12;
            $("div#top-nav-multiopp").attr("style","width:"+w+";position: fixed;z-index: 99;");
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("constraintoviewport", true);
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("width", $(window).width()-200);
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("height", $(window).height()-200);
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("x", null);
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("y", null);
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("fixedcenter", true);
            YAHOO.popup.container.OPPquickInput.cfg.setProperty("draggable", true);
        }
    });


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
							{ 
                                                          fixedcenter : false,
                                                          modal: true,
                                                          close:false, 
							  visible : false, 
                                                          draggable: false,
                                                          autofillheight: "body",
                                                          monitorresize: true,
							  constraintoviewport : false,
                                                          x: 0,
                                                          y: 0,
                                                          width: ($(window).width()),
                                                          height: ($(window).height()),
							  buttons : [ { text:"Create Opportunities", handler:handleSubmit, isDefault:true }]
							 } );
                 YAHOO.popup.container.OPPquickInput.render();           
                   
            }, 3000);
	
     });
     
     function handleSubmit () {
	if(selected_customers.length == 0) {
            alert("You need select at least 1 customer from list to procced!");
            return false;
        }
        
        $("div#OPPquickInput form#EditView[name=action]").val('Save');
        $(".footer").append('<div id="result_oppcreate"><div class="hd">Created Opportunities</div><div class="bd"><table></table></div></div>');
        $.each(selected_customers, function (k, v) {
			if(typeof v != 'undefined'){
				$("div#OPPquickInput form#EditView input#account_name").val($(v).attr("parent_name"));
				$("div#OPPquickInput form#EditView input#account_id").val(v.id);
				if(typeof v.individual_assigned_name !='undefined' && typeof v.individual_assigned_id !='undefined') {
					$("div#OPPquickInput form#EditView input#assigned_user_name1").val(v.individual_assigned_name);
					$("div#OPPquickInput form#EditView input#assigned_user_id1").val(v.individual_assigned_id);
				}
				
				var createopp = new Object();
				$("div#OPPquickInput div#DEFAULT :input").each( function (k, v) {
					if (v.id != "") {
							createopp[v.id] = v.value;      
					} else {
							createopp[v.name] = v.value;
					}
				});
				sessionStorage.setItem("duplicate", JSON.stringify(createopp));
                                                                        var first = "";                                                                        
                                                                        var last = "";      
                                                                        if(k == 0){
                                                                            first = "&firstopp=1";
                                                                        }
                                                                        if((selected_customers.length-1) ==k){
                                                                            last = "&lastopp=1";
                                                                        }
                                                                        
				var resp = $.ajax({
						type: "POST",
						async: true,
						url: "index.php?multiopp=1"+first+last,
						data: $("div#OPPquickInput form#EditView").serialize(), 
						success: function (data) {
							//console.log(data);
							var record_object = new Object();
							var record_id = $(data).find("td#main form#form input[name=record]").val();
							record_object.record = record_id;
							record_object.parent_name = $(v).attr("parent_name");
							record_object.parent_id = v.id;
							$("div.footer div#result_oppcreate table").append('<tr><td><a href="index.php?action=DetailView&module=Opportunities&record=' + record_object.record + '&return_module=Opportunities&return_action=DetailView">' + record_object.parent_name + '</a></td><td></td></tr>');
							
						}
						});  
          }  
        });
   
        YAHOO.popup.container.OPPquickInput.hide();
        
        YAHOO.popup.container.result_oppcreate = new YAHOO.widget.Dialog("result_oppcreate", 
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
							  //buttons : [ { text:"Created Opportunities", handler:handleSubmit, isDefault:true } ]
							 } );
                                                         
        YAHOO.popup.container.result_oppcreate.render();
        YAHOO.popup.container.result_oppcreate.show();

    }
    
    //Load Prev settings
    
    function handleLoadPrev () {
		if(sessionStorage.length != 0) {
			var prev = JSON.parse(sessionStorage.getItem("duplicate"));
			$.each(prev, function (k, v) {
				if($("div#OPPquickInput div#DEFAULT [name=" + k + "]").length == 0) {
					$("div#OPPquickInput div#DEFAULT #" + k).val(v);
				} else {
					$("div#OPPquickInput div#DEFAULT [name=" + k + "]").val(v);
				}
			});
			refresh();
		}
    }
    //Clear Prev settings
    function handleClear () {
		$("#pline_list_display a").each(function(){
			this.onclick();
		});
    }
        function OpportunityQIPopup() { 
             $("div.footer div#OPPquickInput").show();
             enableQuickSearch(true);
             YAHOO.popup.container.OPPquickInput.show();
             setTimeout("document.getElementById('name1').focus();",1100);
             builTableList();
        }
function builTableList(){
var url = "index.php?module=Accounts&action=getCustomersDefaultOpp";
                 
$("#customers_list").dataTable({
            "bJQueryUI": true,
            "bDestroy": true,
            //"bProcessing": true,
            "iDisplayLength": 100,
            "oLanguage": {
                    "sLengthMenu": 'Show <select>' +
                                                '<option value="10">10</option>' +
                                                '<option value="20">20</option>' +
                                                '<option value="30">30</option>' +
                                                '<option value="40">40</option>' +
                                                '<option value="50">50</option>' +
                                                '<option value="100">100</option>' +
                                                '<option value="200">200</option>' +
                                                '<option value="99999999">All</option>' +
                                                '</select> entries'
            },
            "bServerSide": true,
            "sAjaxSource": url,
            "sDom": 'C<"clear"><"H"lrf<"#creat_multiopps"> >t<"F"ip>',
            "fnDrawCallback": function(oSettings, json) {
                $.each(oSettings.aoData, function(k, v) {

                    SUGAR.util.evalScript(oSettings.aoData[k]._aData[12]);
                });
                enableQS(false);

                $("#creat_multiopps").html('<button type="button" onClick="handleSubmit();" class="default">Create Opportunities</button><button type="button" onClick="handleLoadPrev();" class="default">Duplicate Previous multi-create settings</button><input type="reset" class="button" onClick="handleClear();" value="Clear"/>');

                $("#customers_list_length").css("width", "20%");
                $("#customers_list_filter").css("width", "30%");

                if(selected_customers.length > 0) {
                    $.each(selected_customers, function (k, v) {
                        $("table#customers_list input#" + v.id).attr("checked", "checked");
                });
                }

                $("table#customers_list input:checkbox").click(function () {

                    var checkboxelem = $(this);
                    var row = checkboxelem.parent().parent();    
                     if (checkboxelem.is(":checked")) {
                         $(row).addClass("print-row");
                         if ($("#default_assigned").is(":checked")) {
                            var assigned_user_name = $("div#OPPquickInput form#EditView input#assigned_user_name1").val();
                            var assigned_user_id = $("div#OPPquickInput form#EditView input#assigned_user_id1").val();
                            row.find(".user_name").val(assigned_user_name);
                            row.find(".user_id").val(assigned_user_id);
                        }
                        if($("input[individual_assigned_name=" + checkboxelem.attr("id") + "]").val() && $("input[individual_assigned_id=" + checkboxelem.attr("id") + "]").val()) {
							this.individual_assigned_name = $("input[individual_assigned_name=" + checkboxelem.attr("id") + "]").val();
							this.individual_assigned_id = $("input[individual_assigned_id=" + checkboxelem.attr("id") + "]").val();
						}
                        selected_customers.push(this); 
                    }else {
                        $(row).removeClass("print-row");
                        var assigned_user_name = row.find(".user_name_hidden").val();
                        var assigned_user_id = row.find(".user_id_hidden").val(); 
                        row.find(".user_name").val(assigned_user_name);
                        row.find(".user_id").val(assigned_user_id);
                        if(selected_customers.length > 0) {
                            $.each(selected_customers, function (k, v) {
                            if(typeof v != "undefined"){
                                if(v.id == checkboxelem.attr("id")) {

                                    delete selected_customers[k];


                                }
                        }
                            });
                        }
                    }

                });

            },
            "aLengthMenu": [
            [10, 20, 30, 50, 99999999],
            [10, 20, 30, 50, "All"]
            ],
            "sPaginationType": "full_numbers"
});
}
function enableQuickSearch(noReload){
    Ext.onReady(function(){
        var qsFields=Ext.query('.sqsCustom');
        for(var qsField in qsFields){
            var loaded=false;
            if(isInteger(qsField)&&(qsFields[qsField].id&&!document.getElementById(qsFields[qsField].id).readOnly)&&typeof sqs_objects!='undefined'&&sqs_objects[qsFields[qsField].id]&&sqs_objects[qsFields[qsField].id]['disable']!=true){
                if(!loaded){
                    if(typeof QSFieldsArray[qsFields[qsField].id]=='undefined'){
                        var Arr=new Array(qsFields[qsField].parentNode,qsFields[qsField].nextSibling,qsFields[qsField]);
                        QSFieldsArray[qsFields[qsField].id]=Arr;
                    }
                    var sqs=sqs_objects[qsFields[qsField].id];
                    //console.log(sqs);
                    var display_field=sqs.field_list[0];
            
                    var ds=new Ext.data.Store({
                        storeId:"store_"+qsFields[qsField].id,
                        proxy:new Ext.data.HttpProxy({
                            url:'index.php'
                        }),
                        remoteSort:true,
                        reader:new Ext.data.JsonReader({
                            root:'fields',
                            totalProperty:'totalCount',
                            id:'id'
                        },[{
                            name:display_field
                        },]),
                        baseParams:{
                            to_pdf:'true',
                            module:'Home',
                            action:'quicksearchQuery',
                            data:Ext.util.JSON.encode(sqs)
                        }
                    });
                    
                    var search=new Ext.form.ComboBox({
                        id:"combobox_"+qsFields[qsField].id,
                        store:ds,
                        queryDelay:700,
                        maxHeight:150,
                        minListWidth: 'auto',
                        displayField:display_field,
                        fieldClass:'',
                        listClsClass:typeof(Ext.version)!='undefined'?'x-sqs-list':'x-combo-list',
                        focusClass:'',
                        disabledClass:'',
                        emptyClass:'',
                        invalidClass:'',
                        selectedClass:typeof(Ext.version)!='undefined'?'x-sqs-selected':'x-combo-list',
                        typeAhead:true,
                        loadingText:SUGAR.language.get('app_strings','LBL_SEARCHING'),
                        valueNotFoundText:sqs.no_match_text,
                        hideTrigger:true,
                        confirmed:false,
                        applyTo:typeof(Ext.version)!='undefined'?qsFields[qsField].id:Ext.form.ComboBox.prototype.applyTo,
                        minChars:1, 
                        onSelect: function(record, index){
                            if(this.fireEvent('beforeselect', this, record, index) !== false){
                                this.setValue(record.data[this.valueField || this.displayField]);
                                this.fireEvent('select', this, record, index);
                            }  							
                        },
                        listeners:{
                            select:function(el,type, index){
                              setCustomFields(type,el,index,/account/);      
                            }
                        }    
                    });
                    if(typeof search.applyTo!='undefined'){
                        search.applyTo(qsFields[qsField].id);
                    }
                    search.wrap.applyStyles('display:inline');
                    qsFields[qsField].className=qsFields[qsField].className.replace('x-form-text','');
                    if(Ext.isMac&&Ext.isGecko){
                        document.getElementById(qsFields[qsField].id).addEventListener('keypress',preventDef,false);
                    }
                    if((qsFields[qsField].form&&typeof(qsFields[qsField].form)=='object'&&qsFields[qsField].form.name=='search_form')||(qsFields[qsField].className.match('sqsNoAutofill')!=null)){
                        search.events.autofill.listeners[0].fireFn=function(){};

                    }
                }
            }
        }
    });
}
function setCustomFields(type,el,index,filter){
    for(var field in type.json){
        for(var key in sqs_objects[el.el.id].field_list){
                if(field==sqs_objects[el.el.id].field_list[key]&&document.getElementById(sqs_objects[el.el.id].populate_list[key])&&sqs_objects[el.el.id].populate_list[key].match(filter)){
                        document.getElementById(sqs_objects[el.el.id].populate_list[key]).value=type.json[field];
                }
        }
    }
    if($(el.innerList.dom.children[index]).hasClass("selected-accounts")){
        $(el.innerList.dom.children[index]).removeClass("selected-accounts");
        remove_account(el.innerList.dom.children[index].innerHTML);
    }else{
        $(el.innerList.dom.children[index]).addClass("selected-accounts");
        add_account(el.innerList.dom.children[index].innerHTML);    
    }
}

QSFieldsArray=new Array();
if(typeof Ext=='object'){
    enableQuickSearch(true);
}

function add_account(index){
    var account_id = document.getElementById('account_id_filter').value;
    var custno = document.getElementById('account_name_custno_filter').value;    
    if(sessionStorage.getItem("accounts_filter") != null && sessionStorage.getItem("custno_filter") != null && account_id != '' && custno != ''){ 
            var accounts_arr = eval('(' + sessionStorage.getItem("accounts_filter") + ')');
            var custno_arr = eval('(' + sessionStorage.getItem("custno_filter") + ')');    
            accounts_arr[index] = account_id;  
            custno_arr[index] = custno;    
            sessionStorage.setItem("accounts_filter", JSON.stringify(accounts_arr));
            sessionStorage.setItem("custno_filter", JSON.stringify(custno_arr));
            var str_val = '';    
            for(var key in custno_arr) {
                str_val += custno_arr[key]+", ";
            } 
            str_val = str_val.slice(0,str_val.length-2);    
            $("#meet_customers").html(str_val); 
            get_date_for_table();
    }else if(account_id != '' && custno != ''){
            var accounts_arr = {};
            var custno_arr = {};    
            accounts_arr[index] = account_id;
            custno_arr[index] = custno;    
            sessionStorage.setItem("accounts_filter", JSON.stringify(accounts_arr));
            sessionStorage.setItem("custno_filter", JSON.stringify(custno_arr));    
            var str_val = '';    
            for(var key in custno_arr) {
                str_val += custno_arr[key]+", ";
            }   
            str_val = str_val.slice(0,str_val.length-2);    
            $("#meet_customers").html(str_val);
            get_date_for_table();
    } 
}
function remove_account(index){
    if(sessionStorage.getItem("accounts_filter") != null && sessionStorage.getItem("custno_filter") != null){ 
            var accounts_arr = eval('(' + sessionStorage.getItem("accounts_filter") + ')');
            var custno_arr = eval('(' + sessionStorage.getItem("custno_filter") + ')');    
            delete accounts_arr[index];  
            delete custno_arr[index];    
            sessionStorage.setItem("accounts_filter", JSON.stringify(accounts_arr));
            sessionStorage.setItem("custno_filter", JSON.stringify(custno_arr));    
            var str_val = '';    
            for(var key in custno_arr) {
                str_val += custno_arr[key]+", ";
            }
            if(str_val != ''){
                str_val = str_val.slice(0,str_val.length-2);
                $("#meet_customers").html(str_val);
            }else{
                $("#meet_customers").html("All");
            }
            get_date_for_table();
    }
}
function clear_account(){
        document.getElementById('account_name_custno_filter').value = ''; 
        document.getElementById('account_name_filter').value = ''; 
        document.getElementById('account_id_filter').value = '';       
}            
function reset_account_filter(){    
        sessionStorage.removeItem("accounts_filter");
        sessionStorage.removeItem("custno_filter");
        $("#meet_customers").html("All");
        get_date_for_table();
}

function printDiv2(divName,opt) {
        if(opt == 1) {
                var printContents = document.getElementById(divName).innerHTML;
                Popup(printContents,''); 
                }
         if(opt == 2) {
                var filter = "<div id='filters-show'>";
                var select_reg_loc = $("#fmp_reg_loc option:selected").val();
                if (select_reg_loc != 'all'){
                        var selected_reg_loc = new Array();
                        $("#fmp_reg_loc option:selected").each(function (k, v) {
                                        selected_reg_loc[k] = $(this).text();	
                        });
                        if(selected_reg_loc.length != 0){
                                filter += "<b>Area:</b>"+selected_reg_loc.join(", ")+"; ";
                        }else{
                                filter += "<b>Area:</b> all;";
                        }
                }
                var input_value = $("#fmp_slsm_input").val();
                if(input_value == "" && input_value.length == 0){
                    var fmprep_slsm_tree = $("#fmprep_slsm_tree option:selected").val();
                    if (fmprep_slsm_tree != 'all'){

                            var selected_fmprep_slsm_tree = new Array();
                            $("#fmprep_slsm_tree option:selected").each(function (k, v) {
                                            selected_fmprep_slsm_tree[k] = $(this).text();	
                            });
                            if(selected_fmprep_slsm_tree.length != 0){
                                    filter += "<b>SLSM:</b>"+selected_fmprep_slsm_tree.join(",")+";";
                            }else{
                                    filter += "<b>SLSM:</b> all;";
                            }
                    }
                }else{
                    var fmprep_slsm_tree_search = $("#fmprep_slsm_tree_search option:selected").val();
                    if (fmprep_slsm_tree_search != 'all'){
                        var selected_fmprep_slsm_tree = new Array();
                            $("#fmprep_slsm_tree_search option:selected").each(function (k, v) {
                                            selected_fmprep_slsm_tree[k] = $(this).text();	
                            });
                            if(selected_fmprep_slsm_tree.length != 0){
                                    filter += "<b>SLSM:</b>"+selected_fmprep_slsm_tree.join(",")+";";
                            }else{
                                    filter += "<b>SLSM:</b> all;";
                            }
                    }
                }
                var select_dealer = $("#fmp_dealer_type option:selected").val();
                        if (select_dealer != 'all'){
                                var selected_dealer = new Array();
                                $("#fmp_dealer_type option:selected").each(function (k, v) {
                                                selected_dealer[k] = $(this).text();	
                                });
                                if(selected_dealer.length != 0){
                                        filter += "<b>Customer Type:</b>"+selected_dealer.join(", ")+";";
                                }else{
                                        filter += "<b>Customer Type:</b> all;"
                                }
                        }
                filter += "<b>Customers:</b> "+$("#meet_customers").text();	
                filter += "</div>";
                $("#customers_list_wrapper").prepend(filter);
                var printContents = document.getElementById(divName).innerHTML;
                Popup(printContents,'');
        }
         if(opt == 3) {
                var printContents = document.getElementById(divName).innerHTML;
                var style = " tr.odd, tr.even {display: none;} tr.print-row {display: table-row;}";
                Popup(printContents,style); 
    }
}

function Popup(data,style) 
{
	var mywindow = window.open('', 'Print', 'menubar=1,status=0,resizable=1,scrollbars=1,toolbar=0,location=1');
	mywindow.document.write('<html><head><title>Customers list</title>');
	/*optional stylesheet*/ //
	mywindow.document.write('<link rel="stylesheet" type="text/css" href="custom/modules/Accounts/datatables.css" />');
	mywindow.document.write('<style type="text/css">.ui-toolbar, .ColVis{display:none;}'+style+'</style>');
	mywindow.document.write('</head><body >');
	mywindow.document.write(data);
	mywindow.document.write('</body></html>');
	mywindow.document.close();
	mywindow.print();
	$("#filters-show").html("");
	return true;
}
    </script>
     

EOQ;
print $js;
