
{literal}

<script language="javascript">
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
                        maxHeight:300,
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
                       if(field=='name' && typeof type.json['expanded_name'] != "undefined"){ 
                        document.getElementById(sqs_objects[el.el.id].populate_list[key]).value=type.json['expanded_name'];
                       }else{
                        document.getElementById(sqs_objects[el.el.id].populate_list[key]).value=type.json[field];    
                       }     
                }
        }
    }
    if(hasClass(el.innerList.dom.children[index],"selected-accounts")){
        removeClass(el.innerList.dom.children[index], "selected-accounts");
        remove_account(el.innerList.dom.children[index].innerHTML);
    }else{
        addClass(el.innerList.dom.children[index], "selected-accounts");
        add_account(el.innerList.dom.children[index].innerHTML);    
    }
}

QSFieldsArray=new Array();
if(typeof Ext=='object'){
    enableQuickSearch(true);
}
function addClass(o, c){
	var re = new RegExp("(^|\\s)" + c + "(\\s|$)", "g");
	if (re.test(o.className)) return;
	o.className = (o.className + " " + c).replace(/\s+/g, " ").replace(/(^ | $)/g, "");
}
	  
function removeClass(o, c){
	var re = new RegExp("(^|\\s)" + c + "(\\s|$)", "g");
	o.className = o.className.replace(re, "$1").replace(/\s+/g, " ").replace(/(^ | $)/g, "");
}

function hasClass(ele,cls) {
	return ele.className.match(new RegExp('(\\s|^)'+cls+'(\\s|$)'));
}   
// End of File include/javascript/quicksearch.js
                                

	function get_date_for_meet(){
		$(".yui-skin-sam-fmp-sales").find("#panel").slideUp("fast");
		var url = "index.php?module=Accounts&action=sendData";
		var input_value = $("#meet_fmp_slsm_input").val();
		if(input_value.length == 0){
                    var select_slsm = $("#meet_fmprep_slsm_tree option:selected").val();
                }else{
                    var select_slsm = $("#meet_fmprep_slsm_tree_search option:selected").val();
                }
		var select_reg_loc = $("#meet_fmp_reg_loc option:selected").val();
		var select_dealer = $("#meet_fmp_dealer_type option:selected").val();

		if(select_slsm == null){
			select_slsm = 'allin';
		} else if (select_slsm != 'all') {
			var selected_slsm = new Array();
			if (input_value.length == 0) {
					$("#meet_fmprep_slsm_tree option:selected").each(function(k, v) {
						selected_slsm[k] = $(this).val();
					});
			}else{
					$("#meet_fmprep_slsm_tree_search option:selected").each(function(k, v) {
						selected_slsm[k] = $(this).val();
					});

			}
			select_slsm = selected_slsm.join(" ");			
		}
		if(select_reg_loc == null){
			select_reg_loc = 'allin';
		}else if(select_reg_loc != 'all') {
			var selected_reg_loc = new Array();
			$("#meet_fmp_reg_loc option:selected").each(function(k, v) {
				selected_reg_loc[k] = $(this).val();
			});
			select_reg_loc = selected_reg_loc.join(" ");
		}
		if(select_dealer == null) {
			select_dealer = 'allin';
		}else if(select_dealer != 'all') {
			var selected_dealer = new Array();
			$("#meet_fmp_dealer_type option:selected").each(function(k, v) {
				selected_dealer[k] = $(this).val();
			});
			select_dealer = selected_dealer.join(" ");
		}
		if(sessionStorage.getItem("accounts") != null && sessionStorage.getItem("custno") != null){
			var accounts_arr = eval('(' + sessionStorage.getItem("accounts") + ')');
			var custno_arr = eval('(' + sessionStorage.getItem("custno") + ')'); 
                        var ids = '';
                        for(var key in accounts_arr.jsonObject) {
                            ids += accounts_arr.jsonObject[key]+",";
                        } 
                        ids = ids.slice(0,ids.length-1);    
			var custno_name = '';    
                        for(var key in custno_arr.jsonObject) {
                            custno_name += custno_arr.jsonObject[key]+",";
                        } 
                        custno_name = custno_name.slice(0,custno_name.length-1);     
		} 
		var date_start = $("#meet_date_start").val();
		var date_end = $("#meet_date_end").val();
                var upd = 1;    
		$.post(url, {update: upd,meet_slsm_num: select_slsm, meet_reg_loc: select_reg_loc, meet_dealer: select_dealer, customers: ids, custno: custno_name, date_range_start:date_start, date_range_end:date_end}, function(data){
					SUGAR.mySugar.retrieveDashlet("{/literal}{$dashletId}{literal}");
			});
		}

	function reset_date_for_meet(){
		var url = "index.php?module=Accounts&action=sendData";
		var select_slsm = 'all';
		var select_reg_loc = 'all';
		var select_dealer = 'all';
		var customers = "";
		var date_start = "";
		var date_end = "";    
		sessionStorage.removeItem("accounts");
		sessionStorage.removeItem("custno");    
		$.post(url, {meet_slsm_num: select_slsm, meet_reg_loc: select_reg_loc, meet_dealer: select_dealer, customers: customers, date_range_start:date_start, date_range_end:date_end}, function(data){
				SUGAR.mySugar.retrieveDashlet("{/literal}{$dashletId}{literal}");
			});       
		clear_account();        
	}
	function add_account(index){
            var account_id = document.getElementById('account_id_filter').value;
            var custno = document.getElementById('account_name_custno_filter').value;    
            if(sessionStorage.getItem("accounts") != null && sessionStorage.getItem("custno") != null && account_id != '' && custno != ''){ 
                    var accounts_arr = eval('(' + sessionStorage.getItem("accounts") + ')');
                    var custno_arr = eval('(' + sessionStorage.getItem("custno") + ')');    
                    accounts_arr.jsonObject[index] = account_id;  
                    custno_arr.jsonObject[index] = custno;    
                    sessionStorage.setItem("accounts", JSON.stringify(accounts_arr.jsonObject));
                    sessionStorage.setItem("custno", JSON.stringify(custno_arr.jsonObject));
                    var str_val = '';    
                    for(var key in custno_arr.jsonObject) {
                        str_val += custno_arr.jsonObject[key]+", ";
                    } 
                    str_val = str_val.slice(0,str_val.length-2);    
                    $("#meet_customers").html("Customers "+str_val);    
            }else if(account_id != '' && custno != ''){
                    var accounts_arr = {};
                    var custno_arr = {};    
                    accounts_arr[index] = account_id;
                    custno_arr[index] = custno;    
                    sessionStorage.setItem("accounts", JSON.stringify(accounts_arr));
                    sessionStorage.setItem("custno", JSON.stringify(custno_arr));    
                    var str_val = '';    
                    for(var key in custno_arr) {
                        str_val += custno_arr[key]+", ";
                    }   
                    str_val = str_val.slice(0,str_val.length-2);    
                    $("#meet_customers").html("Customers "+str_val);     
            } 
	}
	function remove_account(index){
            if(sessionStorage.getItem("accounts") != null && sessionStorage.getItem("custno") != null){ 
                    var accounts_arr = eval('(' + sessionStorage.getItem("accounts") + ')');
                    var custno_arr = eval('(' + sessionStorage.getItem("custno") + ')');    
                    delete accounts_arr.jsonObject[index];  
                    delete custno_arr.jsonObject[index];    
                    sessionStorage.setItem("accounts", JSON.stringify(accounts_arr.jsonObject));
                    sessionStorage.setItem("custno", JSON.stringify(custno_arr.jsonObject));    
                    var str_val = '';    
                    for(var key in custno_arr.jsonObject) {
                        str_val += custno_arr.jsonObject[key]+", ";
                    }
                    if(str_val != ''){
                        str_val = str_val.slice(0,str_val.length-2);
                        $("#meet_customers").html("Customers "+str_val);
                    }else{
                        $("#meet_customers").html("");
                    }        
            }
	}
	function clear_account(){
		document.getElementById('account_name_custno_filter').value = ''; 
		document.getElementById('account_name_filter').value = ''; 
		document.getElementById('account_id_filter').value = '';       
	}            
	function meet_fmp_slsm_list_quick_search(input_val){
		if(input_val.length != 0){
			$("#meet_box_for_slsm_first").hide();
			var new_select = "";
			new_select += '<select id="meet_fmprep_slsm_tree_search" size="15" multiple="multiple" style="width: 340px;">';
			new_select += '<option value="all" style="border-bottom: 2px solid grey;">ALL</option>';
			$.each($("#meet_fmprep_slsm_tree option"), function(){
				var option_val = this.text;
				if(option_val.indexOf(input_val.toUpperCase()) + 1) {
						new_select += '<option value="'+this.value+'">'+this.text+'</option>';
					}
				});
			new_select += '</select>';
			$("#meet_box_for_slsm_second").show();
			$("#meet_box_for_slsm_second").html(new_select);
			}else{
				$("#meet_box_for_slsm_second").hide();
				$("#meet_box_for_slsm_first").show();
			}
		$("#meet_fmprep_slsm_tree_search").click(function(){
			var fmprep_slsm_tree_search = $("#meet_fmprep_slsm_tree_search option:selected").val();
			if (fmprep_slsm_tree_search != 'all'){
				$("#meet_lslsm").html("SLSM "+fmprep_slsm_tree_search);
			}
			else{
				$("#meet_lslsm").html("");
			}
		});
	}

	$(document).ready(function(){ 
                $("#meet_slsm_list_show").hover(
			function(){
				$("#meet_slsm_list_show").find("#meet_slsm_panel").stop(true, true);
				$("#meet_slsm_list_show").find("#meet_slsm_panel").slideDown("slow");
			},
			function() {
				$("#meet_slsm_list_show").find("#meet_slsm_panel").slideUp("slow");
			}
			);
		$("#meet_area_list_show").hover(
			function(){
				$("#meet_area_list_show").find("#meet_area_panel").stop(true, true);
				$("#meet_area_list_show").find("#meet_area_panel").slideDown("slow");
			},
			function() {
				$("#meet_area_list_show").find("#meet_area_panel").slideUp("slow");
			}
			);
		$("#meet_dealer_list_show").hover(
			function(){
				$("#meet_dealer_list_show").find("#meet_dealer_panel").stop(true, true);
				$("#meet_dealer_list_show").find("#meet_dealer_panel").slideDown("slow");
			},
			function() {
				$("#meet_dealer_list_show").find("#meet_dealer_panel").slideUp("slow");
			}
			);

		$("#meet_customer_list_show").hover(
			function(){
				if ( $(".calendar:last").css("display") == "none" || $(".calendar").length == 0) { 
					$("#meet_customer_list_show").find("#meet_customer_panel").stop(true, true);
					$("#meet_customer_list_show").find("#meet_customer_panel").slideDown("slow");
				}
			},
			function() {
				if ( $(".x-shadow").css("display") == "none" || $(".x-shadow").length == 0) { 
					$("#meet_customer_list_show").find("#meet_customer_panel").slideUp("slow");
				} 
			}
			);   
		$("#meet_date_range_show").hover(
			function(){
				if ( $(".x-shadow").css("display") == "none" || $(".x-shadow").length == 0) {
					$("#meet_date_range_show").find("#meet_date_range").stop(true, true);
					$("#meet_date_range_show").find("#meet_date_range").slideDown("slow");
				}         
			},
			function() {
				if($(".calendar:last").css("display") == "none" || $(".calendar").length == 0)
					$("#meet_date_range_show").find("#meet_date_range").slideUp("slow");  
			}
			);
		$("#meet_fmp_reg_loc").click(function(){

			var select_reg_loc = $("#meet_fmp_reg_loc option:selected").val();
			if (select_reg_loc != 'all'){
				var selected_reg_loc = new Array();
				$("#meet_fmp_reg_loc option:selected").each(function (k, v) {
						selected_reg_loc[k] = $(this).val();	
				});
				$("#meet_larea").html("Area "+selected_reg_loc.join(", "));
			}
			else{
				$("#meet_larea").html("");
			}
		});
		$("#meet_fmprep_slsm_tree").click(function(){
			var fmprep_slsm_tree = $("#meet_fmprep_slsm_tree option:selected").val();
			if (fmprep_slsm_tree != 'all'){

				var selected_fmprep_slsm_tree = new Array();
				$("#meet_fmprep_slsm_tree option:selected").each(function (k, v) {
						selected_fmprep_slsm_tree[k] = $(this).val();	
				});

				$("#meet_lslsm").html("SLSM "+selected_fmprep_slsm_tree.join(", "));
			}
			else{
				$("#meet_lslsm").html("");
			}
		});
		$("#meet_fmprep_slsm_tree_search").click(function(){
			var fmprep_slsm_tree_search = $("#meet_fmprep_slsm_tree_search option:selected").val();
			if (fmprep_slsm_tree_search != 'all'){

				var selected_fmprep_slsm_tree_search = new Array();
				$("#meet_fmprep_slsm_tree_search option:selected").each(function (k, v) {
						selected_fmprep_slsm_tree_search[k] = $(this).val();	
				});

				$("#meet_lslsm").html("SLSM "+selected_fmprep_slsm_tree_search.join(", "));
			}
			else{
				$("#meet_lslsm").html("");
			}
		});
		$("#meet_fmp_dealer_type").click(function(){
			var select_dealer = $("#meet_fmp_dealer_type option:selected").val();
			if (select_dealer != 'all'){

				var selected_dealer = new Array();
				$("#meet_fmp_dealer_type option:selected").each(function (k, v) {
						selected_dealer[k] = $(this).val();	
				});

				$("#meet_lcustype").html("Customer Type "+selected_dealer.join(", "));
				}
				else{
					$("#meet_lcustype").html("");
				}
		});
	})

   
</script>
<style type="text/css">
	.selected-accounts {
            background: none repeat scroll 0 0 #D0D0D0;
            border: 1px dotted #F6F6F6 !important;
	}
	.yui-skin-sam-fmp-sales tr td {
            padding: 4px;
	}
        
        
</style>
{/literal}
<div class="dashlet_print" >
<a href="javascript:void window.open('index.php?module=Home&action=index&print_dashlet=true&dashlet_id=dashlet_{$dashletId}','printwin','menubar=1,status=0,resizable=1,scrollbars=1,toolbar=0,location=1')" class='utilsLink'>
    <img src='themes/Sugar/images/print.gif' width='13' height='13' alt='Print' border='0' align='absmiddle'></a>&nbsp;
<a  href="javascript:void window.open('index.php?module=Home&action=index&print_dashlet=true&dashlet_id=dashlet_{$dashletId}','printwin','menubar=1,status=0,resizable=1,scrollbars=1,toolbar=0,location=1')" class='utilsLink'>
    Print</a>
</div>
<div id="meetings-and-exceptions-content">
<div class="yui-skin-sam-fmp-sales" >
<table style="border:none" cellspacing="0" cellpadding="0" border="0">
<tr>
<td style="border:none">
    <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="meet_area_list_show"><span class="first-child-fmp-sales"><button type="button" id="yui-gen0-button" >Area</button>
            <div id="meet_area_panel" style="display: none; position: absolute;">
                {$area_list}
            </div>
        </span></span>
</td>
<td style="border:none">
    <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="meet_slsm_list_show"><span class="first-child-fmp-sales"><button type="button" id="yui-gen2-button" >Slsm</button>
            <div id="meet_slsm_panel" style="display: none; position: absolute; background-color: #FFFFFF; border: 1px solid #94C1E8;">
                {$slsm_list}
            </div>
        </span></span>
</td>
<td style="border:none">  <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="meet_dealer_list_show"><span class="first-child-fmp-sales"><button type="button" id="yui-gen4-button" >Customer Type</button>
            <div id="meet_dealer_panel" style="display: none; position: absolute;">
                {$dealer_list}
            </div>
        </span></span>
</td>

<td style="border:none">
    <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="meet_customer_list_show"><span class="first-child-fmp-sales"><button type="button" id="yui-gen5-button" >Customer</button>    
      <div id="meet_customer_panel" style="display: none; padding: 3px; position: absolute; background-color: #FFFFFF; border: 1px solid #94C1E8;">  
        <br><label for="account_name_custno_filter" style="display: inline-block; width: 90px; font-size: 12px; color: #000000;">Account Name: </label>
        <input type="text" name="account_name_custno_filter" id="account_name_custno_filter" tabindex='1' class="sqsCustom" size="6" value="" autocomplete="off">
        <input type="text" name="account_name_filter" class="sqsCustom" tabindex="1" id="account_name_filter" size="32" value="" title='' autocomplete="off">
        <input type="hidden" name="account_id_filter" id="account_id_filter" value="">
        <input type="button" style="font-size: 12px; width: 40px;" title="Clear" class="button" onclick="javaScript:clear_account()" value="Clear">
        </br>
        <br>
      </div>  
        {$quicksearch_js}          
</td>
<td style="border:none">
    <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="meet_date_range_show"><span class="first-child-fmp-sales"><button type="button" id="yui-gen6-button" >Start Date</button>    
      <div id="meet_date_range" style="display: none; padding: 3px; position: absolute; background-color: #FFFFFF; border: 1px solid #94C1E8;"> 
        <label for="meet_date_start">From</label>
        <div id="date_from">
            <input onblur="parseDate(this, '{$cal_dateformat}');" class="text" name="meet_date_start" size='12' maxlength='10' id='meet_date_start' value='{$meet_date_start}'>
            <img src="themes/default/images/jscalendar.gif" alt="{$LBL_ENTER_DATE}" id="meet_date_start_trigger" align="absmiddle">
        </div>
        <label for="meet_date_end">To</label>
        <div id="date_end">
            <input onblur="parseDate(this, '{$cal_dateformat}');" class="text" name="meet_date_end" size='12' maxlength='10' id='meet_date_end' value='{$meet_date_end}'>
            <img src="themes/default/images/jscalendar.gif" alt="{$LBL_ENTER_DATE}" id="meet_date_end_trigger" align="absmiddle">
        </div>
    </div>      
</td>
<td style="border:none">    <span>
        <button class='button' onclick='javaScript:get_date_for_meet()'> Filter </button>
    </span>
</td>
<td style="border:none">    <span>
                    <button class='button' onclick='javaScript:reset_date_for_meet()'> Reset </button>
                </span>
</td>
<td style="border:none">
    <p id="meet_larea"></p>
    <p id="meet_lslsm"></p>
    <p id="meet_lcustype"></p>
    <p id="meet_customers"></p>
</td>
<td style="border:none">
    <h4>{$meet_dashlet_title}</h4>
</td>

</tr>
</table>
</div>
<div id="getResponceOpp">
<table cellpadding='0' cellspacing='0' width='100%' border='0' class='listView'>
    <tr>
        <td colspan='{$colCount+1}' align='right'>
            <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                <tr>
                    <td align='left' class='listViewPaginationTdS1'>&nbsp;</td>
                    <td class='listViewPaginationTdS1' align='right' nowrap='nowrap' id='listViewPaginationButtons'>
                        {if $pageData.urls.startPage}
                            <!--<a href='#' onclick='return SUGAR.mySugar.retrieveDashlet("{$dashletId}", "{$pageData.urls.startPage}")' class='listViewPaginationLinkS1'><img src='{$imagePath}start.gif' alt='{$navStrings.start}' align='absmiddle' border='0' width='13' height='11'>&nbsp;{$navStrings.start}</a>&nbsp;-->
							<button title='{$navStrings.start}' class='button' onclick='return SUGAR.mySugar.retrieveDashlet("{$dashletId}", "{$pageData.urls.startPage}")'>
								<img src='{sugar_getimagepath file="start.gif"}' alt='{$navStrings.start}' align='absmiddle' border='0' width='13' height='11'>
							</button>

                        {else}
                            <!--<img src='{$imagePath}start_off.gif' alt='{$navStrings.start}' align='absmiddle' border='0' width='13' height='11'>&nbsp;{$navStrings.start}&nbsp;&nbsp;-->
							<button title='{$navStrings.start}' class='button' disabled>
								<img src='{sugar_getimagepath file="start_off.gif"}' alt='{$navStrings.start}' align='absmiddle' border='0' width='13' height='11'>
							</button>

                        {/if}
                        {if $pageData.urls.prevPage}
                            <!--<a href='#' onclick='return SUGAR.mySugar.retrieveDashlet("{$dashletId}", "{$pageData.urls.prevPage}")' class='listViewPaginationLinkS1'><img src='{$imagePath}previous.gif' alt='{$navStrings.previous}' align='absmiddle' border='0' width='8' height='11'>&nbsp;{$navStrings.previous}</a>&nbsp;-->
							<button title='{$navStrings.previous}' class='button' onclick='return SUGAR.mySugar.retrieveDashlet("{$dashletId}", "{$pageData.urls.prevPage}")'>
								<img src='{sugar_getimagepath file="previous.gif"}' alt='{$navStrings.previous}' align='absmiddle' border='0' width='8' height='11'>
							</button>

                        {else}
                            <!--<img src='{$imagePath}previous_off.gif' alt='{$navStrings.previous}' align='absmiddle' border='0' width='8' height='11'>&nbsp;{$navStrings.previous}&nbsp;-->
							<button class='button' disabled title='{$navStrings.previous}'>
								<img src='{sugar_getimagepath file="previous_off.gif"}' alt='{$navStrings.previous}' align='absmiddle' border='0' width='8' height='11'>
							</button>
                        {/if}
                            <span class='pageNumbers'>({if $pageData.offsets.lastOffsetOnPage-1 == 0}0{else}{$pageData.offsets.current+1}{/if} - {$pageData.offsets.lastOffsetOnPage-1} {$navStrings.of} {if $pageData.offsets.totalCounted}{$pageData.offsets.total}{else}{$pageData.offsets.total}{if $pageData.offsets.lastOffsetOnPage-1 != $pageData.offsets.total}+{/if}{/if})</span>
                        {if $pageData.urls.nextPage && !$next_off}
							<button title='{$navStrings.next}' class='button' onclick='return SUGAR.mySugar.retrieveDashlet("{$dashletId}", "{$pageData.urls.nextPage}")'>
								<img src='{sugar_getimagepath file="next.gif"}' alt='{$navStrings.next}' align='absmiddle' border='0' width='8' height='11'>
							</button>

                        {else}
							<button class='button' title='{$navStrings.next}' disabled>
								<img src='{sugar_getimagepath file="next_off.gif"}' alt='{$navStrings.next}' align='absmiddle' border='0' width='8' height='11'>
							</button>

                        {/if}
			{if $pageData.urls.endPage  && $pageData.offsets.total != $pageData.offsets.lastOffsetOnPage-1}
                           
							<button title='{$navStrings.end}' {if $exceptions} disabled {/if} class='button' onclick='return SUGAR.mySugar.retrieveDashlet("{$dashletId}", "{$pageData.urls.endPage}")'>
								{if $exceptions}<img src='{sugar_getimagepath file="end_off.gif"}'{else}<img src='{sugar_getimagepath file="end.gif"}' {/if} alt='{$navStrings.end}' align='absmiddle' border='0' width='13' height='11'>
							</button>

			{elseif !$pageData.offsets.totalCounted || $pageData.offsets.total == $pageData.offsets.lastOffsetOnPage-1}
                           
							<button class='button' disabled title='{$navStrings.end}'>
							 	<img src='{sugar_getimagepath file="end_off.gif"}' alt='{$navStrings.end}' align='absmiddle' border='0' width='13' height='11'>
							</button>

                        {/if}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr height='20'>
        {foreach from=$displayColumns key=colHeader item=params}
	        <td scope='col' width='{$params.width}%' class='listViewThS1' nowrap>
				<div style='white-space: nowrap;'width='100%' align='{$params.align|default:'left'}'>
                {if $params.sortable|default:true}
	                <a href='#' onclick='return SUGAR.mySugar.retrieveDashlet("{$dashletId}", "{$pageData.urls.orderBy}{$params.orderBy|default:$colHeader|lower}&sugar_body_only=1&id={$dashletId}")' class='listViewThLinkS1'>{sugar_translate label=$params.label module=$pageData.bean.moduleDir}&nbsp;&nbsp;
	                {if $params.orderBy|default:$colHeader|lower == $pageData.ordering.orderBy}
	                    {if $pageData.ordering.sortOrder == 'ASC'}
	                        <img border='0' src='{$imagePath}arrow_down.{$arrowExt}' width='{$arrowWidth}' height='{$arrowHeight}' align='absmiddle' alt='{$arrowAlt}'>
	                    {else}
	                        <img border='0' src='{$imagePath}arrow_up.{$arrowExt}' width='{$arrowWidth}' height='{$arrowHeight}' align='absmiddle' alt='{$arrowAlt}'>
	                    {/if}
	                {else}
	                    <img border='0' src='{$imagePath}arrow.{$arrowExt}' width='{$arrowWidth}' height='{$arrowHeight}' align='absmiddle' alt='{$arrowAlt}'>
	                {/if}
	                </a>
	           {else}
	           		{sugar_translate label=$params.label module=$pageData.bean.moduleDir}
	           {/if}
			   </div>
            </td>
        {/foreach}
        <td scope='col' class='listViewThS1' nowrap></td>
    </tr>

	{foreach name=rowIteration from=$data key=id item=rowData}
		{if $smarty.foreach.rowIteration.iteration is odd}
			{assign var='_bgColor' value=$bgColor[0]}
			{assign var='_rowColor' value=$rowColor[0]}
		{else}
			{assign var='_bgColor' value=$bgColor[1]}
			{assign var='_rowColor' value=$rowColor[1]}
		{/if}
		<tr height='20' {if $rowData.NAME == 'Total'} style="background: none repeat scroll 0 0 #EBEBED;"{/if} onmouseover="setPointer(this, '{$id}', 'over', '{$_bgColor}', '{$bgHilite}', '');" onmouseout="setPointer(this, '{$rowData[$params.id]|default:$rowData.ID}', 'out', '{$_bgColor}', '{$bgHilite}', '');" onmousedown="setPointer(this, '{$id}', 'click', '{$_bgColor}', '{$bgHilite}', '');" >
			{if $prerow}
			<td width='1%'  class='{$_rowColor}S1' {if $rowData.NAME != 'Total'} bgcolor='{$_bgColor}' {/if} nowrap>
					<input onclick='sListView.check_item(this, document.MassUpdate)' type='checkbox' class='checkbox' name='mass[]' value='{$rowData[$params.id]|default:$rowData.ID}'>
			</td>
			{/if}
			{counter start=0 name="colCounter" print=false assign="colCounter"}
			{foreach from=$displayColumns key=col item=params}
				<td scope='row' align='{$params.align|default:'left'}' valign=top class='{$_rowColor}S1' {if $rowData.NAME != 'Total'} bgcolor='{$_bgColor}' {/if} ><span sugar="sugar{$colCounter}b">
					{if $params.link && !$params.customCode}
                                            {if $rowData.$col != 'Total'}
						<{$pageData.tag.$id[$params.ACLTag]|default:$pageData.tag.$id.MAIN} href='index.php?action={$params.action|default:'DetailView'}&module={if $params.dynamic_module}{$rowData[$params.dynamic_module]}{else}{$params.module|default:$pageData.bean.moduleDir}{/if}&record={$rowData[$params.id]|default:$rowData.ID}&offset={$pageData.offsets.current+$smarty.foreach.rowIteration.iteration}&stamp={$pageData.stamp}' class='listViewTdLinkS1'>{$rowData.$col}</{$pageData.tag.$id[$params.ACLTag]|default:$pageData.tag.$id.MAIN}>
                                            {else}
                                                    {$rowData.$col}
                                            {/if}
					{elseif $params.customCode}
						{sugar_evalcolumn_old var=$params.customCode rowData=$rowData}
					{elseif $params.currency_format}
						{sugar_currency_format
							var=$rowData.$col
							round=$params.currency_format.round
							decimals=$params.currency_format.decimals
							symbol=$params.currency_format.symbol
							convert=$params.currency_format.convert
							currency_symbol=$params.currency_format.currency_symbol
						}
					{elseif $params.type == 'bool'}
							<input type='checkbox' disabled=disabled class='checkbox'
							{if !empty($rowData[$col])}
								checked=checked
							{/if}
							/>
					{elseif $params.type == 'multienum'}
						{if !empty($rowData.$col)}
							{counter name="oCount" assign="oCount" start=0}
							{assign var="vals" value='^,^'|explode:$rowData.$col}
							{foreach from=$vals item=item}
								{counter name="oCount"}
								{sugar_translate label=$params.options select=$item}{if $oCount !=  count($vals)},{/if}
							{/foreach}
						{/if}
					{else}
						{$rowData.$col}
					{/if}
				</span sugar='sugar{$colCounter}b'></td>
				{counter name="colCounter"}
			{/foreach}
                        {if !empty($quickViewLinks)}
                                <td width='1%' class='{$_rowColor}S1' {if $rowData.NAME != 'Total'} bgcolor='{$_bgColor}' {/if} nowrap>
                                    {if $rowData.NAME != 'Total'}
                                        {if $pageData.access.view}
                                            <a title='Details' target="_blank" href='index.php?action=DetailView&module={$params.module|default:$pageData.bean.moduleDir}&record={$rowData[$params.parent_id]|default:$rowData.ID}&offset={$pageData.offsets.current+$smarty.foreach.rowIteration.iteration}&stamp={$pageData.stamp}&return_module=Home&return_action=index#groupTabs'><img border=0 src='themes/default/images/Meetings.gif'></a>
                                        {/if}
                                    {/if}     
                                </td>
			{/if}
	    	</tr>
	 	<tr><td colspan='20' class='listViewHRS1'></td></tr>
	{/foreach}
</table>
<br/>
{literal}
<script type="text/javascript">
  Calendar.setup ({
    inputField : "meet_date_start", ifFormat : "{/literal}{$cal_dateformat}{literal}", showsTime : false, button : "meet_date_start_trigger", singleClick : true, step : 1
    });
    Calendar.setup ({
    inputField : "meet_date_end", ifFormat : "{/literal}{$cal_dateformat}{literal}", showsTime : false, button : "meet_date_end_trigger", singleClick : true, step : 1
    }); 
</script>        
{/literal}
</div>
</div>
