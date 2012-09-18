{*

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



*}
{literal}

<script language="javascript">
  function enableQuickSearchOpp(noReload){
    Ext.onReady(function(){
        var qsFields=Ext.query('.sqsOpp');
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
                            module:'Opportunities',
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
                        typeAhead:false,
                        loadingText:SUGAR.language.get('app_strings','LBL_SEARCHING'),
                        valueNotFoundText:sqs.no_match_text,
                        hideTrigger:false,
                        confirmed:false,
                        pageSize:20,
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
                              setOppFields(type,el,index,/\S/);    
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
function setOppFields(type,el,index,filter){
    for(var field in type.json){
        for(var key in sqs_objects[el.el.id].field_list){
                if(field==sqs_objects[el.el.id].field_list[key]&&document.getElementById(sqs_objects[el.el.id].populate_list[key])&&sqs_objects[el.el.id].populate_list[key].match(filter)){
                     document.getElementById(sqs_objects[el.el.id].populate_list[key]).value=type.json[field];    
                }
        }
    }
    if(hasClass(el.innerList.dom.children[index],"selected-opp")){
        removeClass(el.innerList.dom.children[index], "selected-opp");
        remove_opp();
    }else{
        addClass(el.innerList.dom.children[index], "selected-opp");
        add_opp();    
    }
}

QSFieldsArray=new Array();
if(typeof Ext=='object'){
    enableQuickSearchOpp(true);
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

function add_opp(){
    var opp_id = document.getElementById('fmp-opp-id').value;
    var opp_name = document.getElementById('fmp-opp-name').value;    
    if(sessionStorage.getItem("opp_id") != null && sessionStorage.getItem("opp_name") != null && opp_id != '' && opp_name != ''){ 
            var opp_id_arr = eval('(' + sessionStorage.getItem("opp_id") + ')');
            var opp_name_arr = eval('(' + sessionStorage.getItem("opp_name") + ')');    
            opp_id_arr.jsonObject[opp_id] = opp_id;  
            opp_name_arr.jsonObject[opp_id] = opp_name;    
            sessionStorage.setItem("opp_id", JSON.stringify(opp_id_arr.jsonObject));
            sessionStorage.setItem("opp_name", JSON.stringify(opp_name_arr.jsonObject));
            var str_val = '';    
            for(var key in opp_name_arr.jsonObject) {
                str_val += opp_name_arr.jsonObject[key]+", ";
            } 
            str_val = str_val.slice(0,str_val.length-2);    
            $("#loppname").html("Opportunity "+str_val);    
    }else if(opp_id != '' && opp_name != ''){
            var opp_id_arr = {};
            var opp_name_arr = {};    
            opp_id_arr[opp_id] = opp_id;
            opp_name_arr[opp_id] = opp_name;    
            sessionStorage.setItem("opp_id", JSON.stringify(opp_id_arr));
            sessionStorage.setItem("opp_name", JSON.stringify(opp_name_arr));    
            var str_val = '';    
            for(var key in opp_name_arr) {
                str_val += opp_name_arr[key]+", ";
            }   
            str_val = str_val.slice(0,str_val.length-2);    
            $("#loppname").html("Opportunity "+str_val);     
    } 
}
function remove_opp(){
    var opp_id = document.getElementById('fmp-opp-id').value;
    if(sessionStorage.getItem("opp_id") != null && sessionStorage.getItem("opp_name") != null){ 
            var opp_id_arr = eval('(' + sessionStorage.getItem("opp_id") + ')');
            var opp_name_arr = eval('(' + sessionStorage.getItem("opp_name") + ')');    
            delete opp_id_arr.jsonObject[opp_id];  
            delete opp_name_arr.jsonObject[opp_id];    
            sessionStorage.setItem("opp_id", JSON.stringify(opp_id_arr.jsonObject));
            sessionStorage.setItem("opp_name", JSON.stringify(opp_name_arr.jsonObject));    
            var str_val = '';    
            for(var key in opp_name_arr.jsonObject) {
                str_val += opp_name_arr.jsonObject[key]+", ";
            }
            if(str_val != ''){
                str_val = str_val.slice(0,str_val.length-2);
                $("#loppname").html("Opportunity "+str_val);
            }else{
                $("#loppname").html("");
            }        
    }
}
                    function get_date_for_opp(){
                        $(".yui-skin-sam-fmp-sales").find("#panel").slideUp("fast");
               /*         $("#load_gif").show();*/
                        var url = "index.php?module=Opportunities&action=sendData";
                        var input_value = $("#opp_fmp_slsm_input").val();
                        if(input_value.length == 0){
                            var select_slsm = $("#opp_fmprep_slsm_tree option:selected").val();
                            }else{
                            var select_slsm = $("#opp_fmprep_slsm_tree_search option:selected").val();
                            }
                        var select_reg_loc = $("#opp_fmp_reg_loc option:selected").val();
                        var select_dealer = $("#opp_fmp_dealer_type option:selected").val();

                        if(select_slsm == null){
                            select_slsm = 'all';
                        } else if (select_slsm != 'all') {
			    var selected_slsm = new Array();
			    if (input_value.length == 0) {
				    $("#opp_fmprep_slsm_tree option:selected").each(function(k, v) {
					selected_slsm[k] = $(this).val();
				    });
			    }else{
				    $("#opp_fmprep_slsm_tree_search option:selected").each(function(k, v) {
					selected_slsm[k] = $(this).val();
				    });
			    
			    }
                            select_slsm = selected_slsm.join(" ");			
			}
                        if(select_reg_loc == null){
                            select_reg_loc = 'all';
                        }else if(select_reg_loc != 'all') {
			    var selected_reg_loc = new Array();
			    $("#opp_fmp_reg_loc option:selected").each(function(k, v) {
				selected_reg_loc[k] = $(this).val();
			    });
                            select_reg_loc = selected_reg_loc.join(" ");
			}
                        if(select_dealer == null) {
                            select_dealer = 'all';
                        }else if(select_dealer != 'all') {
			    var selected_dealer = new Array();
			    $("#opp_fmp_dealer_type option:selected").each(function(k, v) {
				selected_dealer[k] = $(this).val();
			    });
                            select_dealer = selected_dealer.join(" ");
			}


                        var opp_str = "";
                        var opp_sales_reps_names = "";

                        if($("#opp_sales_reps_list").val()){
                             $("#opp_sales_reps_list option:selected").each(function () {
                                opp_str += this.value + " ";
                                opp_sales_reps_names += this.text + " ";
                              });
                        }
                        else{
                            $("#opp_sales_reps_list option").each(function(){
                                this.selected=true;
                             });
                            opp_sales_reps_names = "All";
                          $("#opp_sales_reps_list option:selected").each(function () {
                                opp_str += this.value + " ";
                              });
                        }
                        if(sessionStorage.getItem("opp_name") != null && sessionStorage.getItem("opp_id") != null){
                                var opp_id_arr = eval('(' + sessionStorage.getItem("opp_id") + ')');
                                var opp_name_arr = eval('(' + sessionStorage.getItem("opp_name") + ')'); 
                                var ids = '';
                                for(var key in opp_id_arr.jsonObject) {
                                    ids += opp_id_arr.jsonObject[key]+",";
                                } 
                                ids = ids.slice(0,ids.length-1);    
                                var names = '';    
                                for(var key in opp_name_arr.jsonObject) {
                                    names += opp_name_arr.jsonObject[key]+",";
                                } 
                                names = names.slice(0,names.length-1);     
                        }else{
                           var names = document.getElementById('fmp-opp-name').value;      
                        }      
                            
                        $.post(url, {opp_slsm_num: select_slsm, opp_reg_loc: select_reg_loc, opp_dealer: select_dealer, opp_sales_reps: opp_str, opp_sr_names: opp_sales_reps_names, opp_name: names}, function(data){
                                 SUGAR.mySugar.retrieveDashlet("{/literal}{$dashletId}{literal}");
                            });
                        }

                    function reset_date_for_opp(){
                        var url = "index.php?module=Opportunities&action=sendData";
                        var select_slsm = 'all';
                        var select_reg_loc = 'all';
                        var select_dealer = 'all';
                        var opp_sales_reps_names = "all";
                        var opp_str = "";
                        var opportunity = "";
                        $("#opp_sales_reps_list option").each(function(){
                                this.selected=true;
                            });
                        $("#opp_sales_reps_list option:selected").each(function () {
                             opp_str += this.value + " ";
                        });
                        sessionStorage.removeItem("opp_name");
                        sessionStorage.removeItem("opp_id");     
                        $.post(url, {opp_slsm_num: select_slsm, opp_reg_loc: select_reg_loc, opp_dealer: select_dealer, opp_sales_reps: opp_str, opp_sr_names: opp_sales_reps_names,opp_name:opportunity}, function(data){
                                 SUGAR.mySugar.retrieveDashlet("{/literal}{$dashletId}{literal}");
                            });
                    }

                    function opp_fmp_slsm_list_quick_search(input_val){
                        if(input_val.length != 0){
                            $("#opp_box_for_slsm_first").hide();
                            var new_select = "";
                            new_select += '<select id="opp_fmprep_slsm_tree_search" size="15" multiple="multiple" style="width: 340px;">';
                            new_select += '<option value="all" style="border-bottom: 2px solid grey;">ALL</option>';
                            $.each($("#opp_fmprep_slsm_tree option"), function(){
                                var option_val = this.text;
                                if(option_val.indexOf(input_val.toUpperCase()) + 1) {
                                      new_select += '<option value="'+this.value+'">'+this.text+'</option>';
                                    }
                                });
                            new_select += '</select>';
                            $("#opp_box_for_slsm_second").show();
                            $("#opp_box_for_slsm_second").html(new_select);
                           }else{
                               $("#opp_box_for_slsm_second").hide();
                               $("#opp_box_for_slsm_first").show();
                           }
                        $("#opp_fmprep_slsm_tree_search").click(function(){
                            var fmprep_slsm_tree_search = $("#opp_fmprep_slsm_tree_search option:selected").val();
                            if (fmprep_slsm_tree_search != 'all'){
                                $("#lslsm").html("SLSM "+fmprep_slsm_tree_search);
                            }
                            else{
                                $("#lslsm").html("");
                            }
                        });
                    }

                    $(document).ready(function(){
                        $("#opp_slsm_list_show").hover(
                            function(){
                                $("#opp_lsm_list_show").find("#opp_slsm_panel").stop(true, true);
                                $("#opp_slsm_list_show").find("#opp_slsm_panel").slideDown("slow");
                            },
                            function() {
                                $("#opp_slsm_list_show").find("#opp_slsm_panel").slideUp("slow");
                            }
                            );
                        $("#opp_area_list_show").hover(
                            function(){
                                $("#opp_area_list_show").find("#opp_area_panel").stop(true, true);
                                $("#opp_area_list_show").find("#opp_area_panel").slideDown("slow");
                            },
                            function() {
                                $("#opp_area_list_show").find("#opp_area_panel").slideUp("slow");
                            }
                            );
                        $("#opp_dealer_list_show").hover(
                            function(){
                                $("#opp_dealer_list_show").find("#opp_dealer_panel").stop(true, true);
                                $("#opp_dealer_list_show").find("#opp_dealer_panel").slideDown("slow");
                            },
                            function() {
                                $("#opp_dealer_list_show").find("#opp_dealer_panel").slideUp("slow");
                            }
                            );
                        $("#opp_sales_reps_list_show").hover(
                            function(){
                                $("#opp_sales_reps_list_show").find("#opp_sales_reps_panel").stop(true, true);
                                $("#opp_sales_reps_list_show").find("#opp_sales_reps_panel").slideDown("slow");
                            },
                            function() {
                                $("#opp_sales_reps_list_show").find("#opp_sales_reps_panel").slideUp("slow");
                            }
                            );
                        $("#fmp-opp-list-show").hover(
                            function(){
                                    $("#fmp-opp-list-show").find("#fmp-opp-panel").stop(true, true);
                                    $("#fmp-opp-list-show").find("#fmp-opp-panel").slideDown("slow");
                            },
                            function() {
                                    if ( $(".x-shadow").css("display") == "none" || $(".x-shadow").length == 0) { 
                                            $("#fmp-opp-list-show").find("#fmp-opp-panel").slideUp("slow");
                                    } 
                            }
			); 
                        $("#opp_sales_reps_list").click(function(){
                            var select_sales_reps = $("#opp_sales_reps_list option:selected").html();
                            if (select_sales_reps != 'all'){
                                $("#lsalesreps").html("Sales Reps "+select_sales_reps);
                            }
                            else{
                                $("#lsalesreps").html("");
                            }
                        });
                        $("#opp_fmp_reg_loc").click(function(){
				
                            var select_reg_loc = $("#opp_fmp_reg_loc option:selected").val();
                            if (select_reg_loc != 'all'){
				var selected_reg_loc = new Array();
				$("#opp_fmp_reg_loc option:selected").each(function (k, v) {
					selected_reg_loc[k] = $(this).val();	
				});
                                $("#larea").html("Area "+selected_reg_loc.join(", "));
                            }
                            else{
                                $("#larea").html("");
                            }
                        });
                        $("#opp_fmprep_slsm_tree").click(function(){
                            var fmprep_slsm_tree = $("#opp_fmprep_slsm_tree option:selected").val();
                            if (fmprep_slsm_tree != 'all'){

				var selected_fmprep_slsm_tree = new Array();
				$("#opp_fmprep_slsm_tree option:selected").each(function (k, v) {
					selected_fmprep_slsm_tree[k] = $(this).val();	
				});

                                $("#lslsm").html("SLSM "+selected_fmprep_slsm_tree.join(", "));
                            }
                            else{
                                $("#lslsm").html("");
                            }
                        });

                        $("#opp_fmprep_slsm_tree_search").click(function(){
                            var fmprep_slsm_tree_search = $("#opp_fmprep_slsm_tree_search option:selected").val();
                            if (fmprep_slsm_tree_search != 'all'){

				var selected_fmprep_slsm_tree_search = new Array();
				$("#opp_fmprep_slsm_tree_search option:selected").each(function (k, v) {
					selected_fmprep_slsm_tree_search[k] = $(this).val();	
				});

                                $("#lslsm").html("SLSM "+selected_fmprep_slsm_tree_search.join(", "));
                            }
                            else{
                                $("#lslsm").html("");
                            }
                        });
                        $("#opp_fmp_dealer_type").click(function(){
                            var select_dealer = $("#opp_fmp_dealer_type option:selected").val();
                            if (select_dealer != 'all'){

				var selected_dealer = new Array();
				$("#opp_fmp_dealer_type option:selected").each(function (k, v) {
					selected_dealer[k] = $(this).val();	
				});

                                $("#lcustype").html("Customer Type "+selected_dealer.join(", "));
                                }
                                else{
                                    $("#lcustype").html("");
                                }
                        });
                  })
                function clear_opp(){
                    document.getElementById('fmp-opp-name').value = ''; 
                    document.getElementById('fmp-opp-id').value = '';       
                }       
                /*var word = document.getElementById("sales_reps_list").value;*/
                </script>
<style type="text/css">
	.selected-opp {
            background: none repeat scroll 0 0 #D0D0D0;
            border: 1px dotted #F6F6F6 !important;
	}   
        #fmp-opp-panel .x-form-field-wrap img.x-form-trigger{
            width: 18px !important;
            height: 19px;
            position: absolute;
            top: -4px;
            left: -17px;
        }
        #fmp-opp-panel .x-form-field-wrap img.x-form-arrow-trigger{
            background-position: -50px 0;
        }
        #fmp-opp-panel .x-form-field-wrap img.x-form-trigger-click{
            background-position: -67px 0 !important;
        }
        #fmp-opp-panel .x-form-field-wrap img.x-form-trigger-over{
            background-position: -33px 0;
        }
</style>
{/literal}


<div class="yui-skin-sam-fmp-sales">
<table style="border:none" cellspacing="0" cellpadding="0" border="0">
<tr>
<td style="border:none">
    <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="opp_area_list_show"><span class="first-child-fmp-sales"><button type="button" id="yui-gen0-button" >Area</button>
            <div id="opp_area_panel" style="display: none; position: absolute;">
                {$area_list}
            </div>
        </span></span>
</td>
<td style="border:none">
    <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="opp_slsm_list_show"><span class="first-child-fmp-sales"><button type="button" id="yui-gen2-button" >Slsm</button>
            <div id="opp_slsm_panel" style="display: none; position: absolute; background-color: #FFFFFF; border: 1px solid #94C1E8;">
                {$slsm_list}
            </div>
        </span></span>
</td>
<td style="border:none">  <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="opp_dealer_list_show"><span class="first-child-fmp-sales"><button type="button" id="yui-gen4-button" >Customer Type</button>
            <div id="opp_dealer_panel" style="display: none; position: absolute;">
                {$dealer_list}
            </div>
        </span></span>
</td>
<td style="border:none">  <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="opp_sales_reps_list_show"><span class="first-child-fmp-sales"><button type="button" id="yui-gen4-button" >Sales Reps</button>
            <div id="opp_sales_reps_panel" style="display: none; position: absolute;">
                {$sales_reps_list}
            </div>
        </span></span>
</td>
<td style="border:none">
    <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="fmp-opp-list-show"><span class="first-child-fmp-sales"><button type="button" id="yui-gen5-button" >Opportunity</button>    
      <div id="fmp-opp-panel" style="display: none; padding: 3px; position: absolute; background-color: #FFFFFF; border: 1px solid #94C1E8;">  
        <br><label for="fmp-opp-name" style="display: inline-block; width: 90px; font-size: 12px; color: #000000;padding-left: 10px;">Name: </label>
        <input type="text" name="fmp-opp-name" class="sqsOpp" tabindex="1" id="fmp-opp-name" size="32" value="{$opp_name}" title='' autocomplete="off">
        <input type="hidden" name="fmp-opp-id" id="fmp-opp-id" value="">
        <input type="button" style="font-size: 12px; width: 40px;" title="Clear" class="button" onclick="javaScript:clear_opp()" value="Clear">
        </br>
        <br>
      </div>  
        {$quicksearch_js} 
        </span></span> 
</td>
<td style="border:none">    <span>
        <button class='button' onclick='javaScript:get_date_for_opp()'> Filter </button>
    </span>
</td>
<td style="border:none">    <span>
                    <button class='button' onclick='javaScript:reset_date_for_opp()'> Reset </button>
                </span>
</td>
<td style="border:none">
    <p id="larea"></p>
    <p id="lslsm"></p>
    <p id="lcustype"></p>
    <p id="loppname"></p>
    <p id="lsalesreps" style="display: none"></p>
</td>
<td style="border:none">
    <h1>{$opp_dashlet_title}</h1>
</td>
<td style="border:none">
    <p>{$sales_reps_name}</p>
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
                        {if $pageData.urls.nextPage}
                            <!--&nbsp;<a href='#' onclick='return SUGAR.mySugar.retrieveDashlet("{$dashletId}", "{$pageData.urls.nextPage}")' class='listViewPaginationLinkS1'>{$navStrings.next}&nbsp;<img src='{$imagePath}next.gif' alt='{$navStrings.next}' align='absmiddle' border='0' width='8' height='11'></a>&nbsp;-->
							<button title='{$navStrings.next}' class='button' onclick='return SUGAR.mySugar.retrieveDashlet("{$dashletId}", "{$pageData.urls.nextPage}")'>
								<img src='{sugar_getimagepath file="next.gif"}' alt='{$navStrings.next}' align='absmiddle' border='0' width='8' height='11'>
							</button>

                        {else}
                           <!-- &nbsp;{$navStrings.next}&nbsp;<img src='{$imagePath}next_off.gif' alt='{$navStrings.next}' align='absmiddle' border='0' width='8' height='11'>-->
							<button class='button' title='{$navStrings.next}' disabled>
								<img src='{sugar_getimagepath file="next_off.gif"}' alt='{$navStrings.next}' align='absmiddle' border='0' width='8' height='11'>
							</button>

                        {/if}
			{if $pageData.urls.endPage  && $pageData.offsets.total != $pageData.offsets.lastOffsetOnPage-1}
                            <!--<a href='#' onclick='return SUGAR.mySugar.retrieveDashlet("{$dashletId}", "{$pageData.urls.endPage}")' class='listViewPaginationLinkS1'>{$navStrings.end}&nbsp;<img src='{$imagePath}end.gif' alt='{$navStrings.end}' align='absmiddle' border='0' width='13' height='11'></a></td>-->
							<button title='{$navStrings.end}' class='button' onclick='return SUGAR.mySugar.retrieveDashlet("{$dashletId}", "{$pageData.urls.endPage}")'>
								<img src='{sugar_getimagepath file="end.gif"}' alt='{$navStrings.end}' align='absmiddle' border='0' width='13' height='11'>
							</button>

			{elseif !$pageData.offsets.totalCounted || $pageData.offsets.total == $pageData.offsets.lastOffsetOnPage-1}
                            <!--&nbsp;{$navStrings.end}&nbsp;<img src='{$imagePath}end_off.gif' alt='{$navStrings.end}' align='absmiddle' border='0' width='13' height='11'>-->
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
		{if !empty($quickViewLinks)}
		<td scope='col' class='listViewThS1' nowrap width='1%'>&nbsp;</td>
		{/if}
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
                                        {if $pageData.access.edit}
                                                <a title='{$editLinkString}' href='index.php?action=EditView&module={$params.module|default:$pageData.bean.moduleDir}&record={$rowData[$params.parent_id]|default:$rowData.ID}&offset={$pageData.offsets.current+$smarty.foreach.rowIteration.iteration}&stamp={$pageData.stamp}&return_module=Home&return_action=index'><img border=0 src={$imagePath}edit_inline.gif></a>
                                        {/if}
                                        {if $pageData.access.view}
                                                <a title='{$viewLinkString}' href='index.php?action=DetailView&module={$params.module|default:$pageData.bean.moduleDir}&record={$rowData[$params.parent_id]|default:$rowData.ID}&offset={$pageData.offsets.current+$smarty.foreach.rowIteration.iteration}&stamp={$pageData.stamp}&return_module=Home&return_action=index'><img border=0 src={$imagePath}view_inline.gif></a>
                                        {/if}
                                        {else}
                                    {/if}
                                </td>
			{/if}
	    	</tr>
	 	<tr><td colspan='20' class='listViewHRS1'></td></tr>
	{/foreach}
</table>
<br/>
</div>
