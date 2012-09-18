<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');
/**
 * MassUpdate for ListViews
 *
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
/**
 * MassUpdate class for updating multiple records at once
 */
require_once('modules/ZuckerReportParameter/fmp.class.param.slsm.php');
require_once('modules/ZuckerReportParameter/fmp.class.param.regloc.php');

//REQUIRED REFACTORING AND CLEANING
class QuickInputClass {
    /*
     * internal sugarbean reference
     */

    var $sugarbean = null;

    function get_dealer_type($dealer_list) {
        $select_creater = '<select id="fmp_dealer_type" onclick="javaScript:get_date_for_table()" size="10" multiple="multiple" style="width: 170px;">';
        $select_creater .= '<option value="all" style="border-bottom: 2px solid grey;">ALL</option>';
        foreach ($dealer_list as $key => $value) {
            $select_creater .= '<option value="' . $key . '">' . $value . '</option>';
        }
        $select_creater .= '</select>';
        return $select_creater;
    }

    function build__slsm($compiled_slsm, $is_user_id) {
        foreach ($compiled_slsm as $k => $v) {
            $compiled_slsm[$k] = "'$v'";
        }

        $h = ''
                . $this->user_add_on($is_user_id)
                . ' WHERE dsls_dailysales.deleted = 0 AND slsm IN (' . implode(', ', $compiled_slsm) . ') '
        ;
        return $h;
    }

    protected function user_add_on($is_user_id) {
        if (!$is_user_id) {
            return;
        }

        return ''
                . ' AND x_m.assigned_user_id="' . $this->user_id . '" '
        ;
    }

    function scripts_for_display() {
        global $app_list_strings;
        //<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
        //<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/jquery-ui.min.js" type="text/javascript"></script>
        //<link rel="stylesheet" type="text/css" href="custom/modules/Accounts/jquery-ui-1.8.11.custom.css" />

        $statuses .= '<select name="direction_qi" id="direction_qi" title="">';

        foreach ($app_list_strings['call_direction_dom'] as $k => $v)
            $statuses .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';

        $statuses .= '</select><br /><select name="status_qi" id="status_qi" title="">';

        foreach ($app_list_strings['call_status_dom'] as $k => $v)
            $statuses .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';
        $statuses .= '</select>';
        return '
            
            <script src="custom/modules/Accounts/jquery.datatables.min.js" type="text/javascript"></script>
	    <script src="custom/modules/Accounts/ColVis.js" type="text/javascript"></script>
	    <script src="custom/modules/Accounts/jquery.autoresize.js" type="text/javascript"></script>
	    <script src="custom/modules/Accounts/jquery-fieldselection.js" type="text/javascript"></script>
            
	    <link rel="stylesheet" type="text/css" href="custom/modules/Accounts/datatables.css" />
	    <link rel="stylesheet" type="text/css" href="custom/modules/Accounts/ColVis.css" />
            
            <script language="javascript">

                    function get_date_for_table(){
			//type of popup-box (accounts/leads)
			var type_popup = $("div.record_dialog_class_qi").attr("popup_type");


                        $(".yui-skin-sam-fmp-sales").find("#panel").slideUp("fast");
			if(type_popup == "accounts") {
	                        var url = "index.php?module=Accounts&action=getCustomers";
			} else {	
				var url = "index.php?module=Accounts&action=getCustomersOpp";
			}
                        var input_value = $("#fmp_slsm_input").val();
                        var username_value = jQuery("#call_list_username").val();
			var city_value = jQuery("#call_list_city").val();
			var state_value = jQuery("#call_list_state").val();
			var postalcode_value = jQuery("#call_list_postalcode").val();
			
                        if(input_value && input_value.length == 0){
                            var select_slsm = $("#fmprep_slsm_tree option:selected").val();
                            }else{
                            var select_slsm = $("#fmprep_slsm_tree_search option:selected").val();
                            }
                        var select_reg_loc = $("#fmp_reg_loc option:selected").val();
			//$("#customers-list-to-calendar").html("Loading ...");
                        var select_dealer = $("#fmp_dealer_type option:selected").val();
			var select_reps = $("#opp_sales_reps_list option:selected").val();
			var select_status = $("#leads_status").val();
			var select_source = $("#leads_source").val();

			if(select_status != null) {
				select_status = select_status.join(";");
			}

			if(select_source != null) {
				select_source = select_source.join(";");
			}

                        //$.post(url, {slsm_num: select_slsm, reg_loc: select_reg_loc, dealer: select_dealer, username: username_value}, function(data){
                            
                               //$("#customers-list-to-calendar").html(data);
                                
			       $("#customers_list").dataTable({
					"bJQueryUI": true,
					"bDestroy": true,
					"bProcessing": true,
					"bServerSide": true,
					"sAjaxSource": url,
					"fnDrawCallback": function(oSettings, json) {
						//console.log($("div.record_dialog_class_qi").attr("popup_type"));
      						//cal2_hide_address_columns ($("div.record_dialog_class_qi").attr("popup_type"));
						positioning_autopopulate_fields();
    					},
					"sPaginationType": "full_numbers",
					"sDom": \'C<"clear"><"H"lr<"#autofill_pcp_outcome"> >t<"F"ip>\',
					"fnServerData": function ( sSource, aoData, fnCallback ) {
						/* Add some extra data to the sender */
						aoData.push({name: \'slsm_num\', value: select_slsm });
						aoData.push({name: \'reg_loc\', value: select_reg_loc});
						aoData.push({name: \'dealer\', value: select_dealer });
						aoData.push({name: \'reps\', value: select_reps });
						aoData.push({name: \'username\', value: username_value});
						aoData.push({name: \'city\', value: city_value});
						aoData.push({name: \'state\', value: state_value});
						aoData.push({name: \'postalcode\', value: postalcode_value});
						aoData.push({name: \'status\', value: select_status});
						aoData.push({name: \'source\', value: select_source});
						$.getJSON( sSource, aoData, function (json) {

						    fnCallback(json);
						    
					    } );
				    	}

				});

                                
                           // });
			
                        }
                    function fmp_slsm_list_quick_search(input_val){
                        if(input_val.length != 0){
                            $("#box_for_slsm_first").hide();
                            var new_select = "";
                            new_select += \'<select id="fmprep_slsm_tree_search" onclick="javaScript:get_date_for_table()" size="15" multiple="multiple" style="width: 340px;">\';
                            new_select += \'<option value="all" style="border-bottom: 2px solid grey;">ALL</option>\';
                            $.each($("#fmprep_slsm_tree option"), function(){
                                var option_val = this.text;
                                if(option_val.indexOf(input_val.toUpperCase()) + 1) {
                                      new_select += \'<option value="\'+this.value+\'">\'+this.text+\'</option>\';
                                    }
                                });
                            new_select += \'</select>\';
                            $("#box_for_slsm_second").show();
                            $("#box_for_slsm_second").html(new_select);
                           }else{
                               $("#box_for_slsm_second").hide();
                               $("#box_for_slsm_first").show();
                           }
                    }

                    $(document).ready(function(){
                      	//jQuery("#call_list_username").blur(function() {
                        //	get_date_for_table();
                        //});


/*(function(){
	var old = $.ui.dialog.prototype._create;
	$.ui.dialog.prototype._create = function(d){
		old.call(this, d);
		var self = this,
			options = self.options,
			oldHeight = options.height,
			oldWidth = options.width,
			uiDialogTitlebarFull = $(\'<a href="#"></a>\')
				.addClass(
					\'ui-dialog-titlebar-full \' +
					\'ui-corner-all\'
				)
				.attr(\'role\', \'button\')
				.hover(
					function() {
						uiDialogTitlebarFull.addClass(\'ui-state-hover\');
					},
					function() {
						uiDialogTitlebarFull.removeClass(\'ui-state-hover\');
					}
				)
				.toggle(
					function() {
						self._setOptions({
							height : window.innerHeight - 10,
							width : window.innerWidth - 30
						});
						self._position(\'center\');
						return false;
					},
					function() {
						self._setOptions({
							height : oldHeight,
							width : oldWidth
						});
						self._position(\'center\');
						return false;
					}
				)
				.focus(function() {
					uiDialogTitlebarFull.addClass(\'ui-state-focus\');
				})
				.blur(function() {
					uiDialogTitlebarFull.removeClass(\'ui-state-focus\');
				})
				.appendTo(self.uiDialogTitlebar),

			uiDialogTitlebarFullText = $(\'<span></span>\')
				.addClass(
					\'ui-icon \' +
					\'ui-icon-newwin\'
				)
				.text(options.fullText)
				.appendTo(uiDialogTitlebarFull)

	};
})();*/

			
			
                        $("#slsm_list_show").hover(
                            function(){
                                $("#slsm_list_show").find("#slsm_panel").stop(true, true);
                                $("#slsm_list_show").find("#slsm_panel").slideDown();
                                $("#customers_list_wrapper").css("z-index", -1000);
                            },
                            function() {
                                $("#slsm_list_show").find("#slsm_panel").slideUp("fast");
                                $("#customers_list_wrapper").css("z-index", 1000);
                            }
                            );
                        $("#area_list_show").hover(
                            function(){
                                $("#area_list_show").find("#area_panel").stop(true, true);
                                $("#area_list_show").find("#area_panel").slideDown();
                                $("#customers_list_wrapper").css("z-index", -1000);
                            },
                            function() {
                                $("#area_list_show").find("#area_panel").slideUp("fast");
                                $("#customers_list_wrapper").css("z-index", 1000);
                            }
                            );
                        $("#dealer_list_show").hover(
                            function(){
                                $("#dealer_list_show").find("#dealer_panel").stop(true, true);
                                $("#dealer_list_show").find("#dealer_panel").slideDown();
                                $("#customers_list_wrapper").css("z-index", -1000);
                            },
                            function() {
                                $("#dealer_list_show").find("#dealer_panel").slideUp("fast");
                                $("#customers_list_wrapper").css("z-index", 1000);
                            }
                        );

                        $("#reps_list_show").hover(
                            function(){
                                $("#reps_list_show").find("#reps_panel").stop(true, true);
                                $("#reps_list_show").find("#reps_panel").slideDown();
                                $("#customers_list_wrapper").css("z-index", -1000);
                            },
                            function() {
                                $("#reps_list_show").find("#reps_panel").slideUp("fast");
                                $("#customers_list_wrapper").css("z-index", 1000);
                            }
                        );
                        $("#leads_status_show").hover(
                            function(){
                                $("#leads_status_show").find("#status_panel").stop(true, true);
                                $("#leads_status_show").find("#status_panel").slideDown();
                                $("#customers_list_wrapper").css("z-index", -1000);
                            },
                            function() {
                                $("#leads_status_show").find("#status_panel").slideUp("fast");
                                $("#customers_list_wrapper").css("z-index", 1000);
                            }
                        );
                        $("#leads_source_show").hover(
                            function(){
                                $("#leads_source_show").find("#source_panel").stop(true, true);
                                $("#leads_source_show").find("#source_panel").slideDown();
                                $("#customers_list_wrapper").css("z-index", -1000);
                            },
                            function() {
                                $("#leads_source_show").find("#source_panel").slideUp("fast");
                                $("#customers_list_wrapper").css("z-index", 1000);
                            }
                        );

                    })

		function cal2_hide_address_columns (type) {

		    //hide adrress columns
		    
		    /* Get the DataTables object again - this is not a recreation, just a get of the object */
		    if (type == "current-customer-call-list" || type == "accounts") {

			    var oTable = $(\'#customers_list\').dataTable();
			     
			    var bVis = oTable.fnSettings().aoColumns[3].bVisible;
			    oTable.fnSetColumnVis( 3, bVis ? false : true );

			    bVis = oTable.fnSettings().aoColumns[4].bVisible;
			    oTable.fnSetColumnVis( 4, bVis ? false : true );

			    bVis = oTable.fnSettings().aoColumns[5].bVisible;
			    oTable.fnSetColumnVis( 5, bVis ? false : true );

			    bVis = oTable.fnSettings().aoColumns[6].bVisible;
			    oTable.fnSetColumnVis( 6, bVis ? false : true );

		    }

		    //positioning_autopopulate_fields();

		}




		//make autopopulate inputs in top of column
		function positioning_autopopulate_fields() {

		window.setTimeout(function () { $("#customers_list_wrapper #customers_list tbody textarea.pc-plan, #customers_list_wrapper #customers_list tbody textarea.outcome").autoGrow(); }, 1);

            	window.setTimeout(function () {
			$(\'.ui-toolbar:first\').css(\'height\', \'50px\');

			
			$(\'input#autopopulate-pc-plan, textarea.pc-plan\').width($(\'th.pre-call-plan-table-header\').width());
			$(\'input#autopopulate-outcome,  textarea.outcome\').width($(\'th.outcome-table-header\').width());


			var header_statuses_position = $(\'.status-table-header\').position();
			$(\'.statuses-table-top-populate\').css({\'left\': header_statuses_position.left, \'position\': \'absolute\'});


			var header_pc_plan_position = $(\'.pre-call-plan-table-header\').position();
			$(\'#autopopulate-pc-plan\').css({\'left\': header_pc_plan_position.left, \'position\': \'absolute\'});

			$("select#direction_qi").change(function() {
				$("select.statuses_direction").not("select#direction_qi").val(this.value);
			});

			$("select#status_qi").change(function() {
				$("select.statuses_status").not("select#status_qi").val(this.value);
			});

			var header_outcome_position = $(\'.outcome-table-header\').position();
			$(\'#autopopulate-outcome\').css({\'left\': header_outcome_position.left, \'position\': \'absolute\'}); 
			$("textarea.pc-plan").not("#autopopulate-pc-plan").unbind("blur");
			$("textarea.pc-plan, textarea.status_description").not("#autopopulate-pc-plan").blur(function() {
				var cursor_position = $(this).getSelection();
				var textarea_element = this;
				var length_value = 0;
				var d=new Date();
				var hour = d.getHours() ;
                                var minutes = d.getMinutes() < 10 ? "0" + d.getMinutes() : d.getMinutes() ;
				var start_length_value = 0;
				var message_start = -1;
				if(hour > 12) {
				    hour = hour - 12;
				    var medium = "pm";
				}else{
				    var medium = "am";
				}
                                
                                hour = hour < 10 ? "0" + hour : hour ;

				var timestamp = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear() + ", " + hour + ":" + minutes + medium + " " + d.toString().replace(/^.*\(|\)$/g, "").replace(/[^A-Z]/g, "");
				var value_rows = $(this).val().split("\n");
                                var row_key = 0;
				//console.log(value_rows);				
				$.each(value_rows, function (k,v) {
					
					start_length_value = length_value;					
					length_value += v.length;
					
					if(v.length == 0) {
						//console.log("go");
						return true;					
					}
                                        row_key = k;
					//console.log(cursor_position.start);
					//console.log(length_value);
					if(cursor_position.start <= length_value+value_rows.length) {
						

						var note_of_order = k;
						if (start_length_value == 0) {
							message_start = -1;
							message_end = 0;
						}else{

							message_start = start_length_value-1;
							message_end = start_length_value+ k;						
						}
						
						$(textarea_element).attr("textarea-custom", 1);

						return false;
					}

				});
                                var ts = Math.round((new Date()).getTime() / 1000);
 
                                if($(this).attr("timestamp") == undefined || (ts - $(this).attr("timestamp")) > 60) {
                                    $(this).val(this.value.substr(0, message_start+(row_key+1)) + timestamp + " : " + this.value.substr(message_end));
                                    $(this).attr("timestamp", ts);
                                }
                                
				
                                
			});


			$("textarea.outcome").not("#autopopulate-outcome").unbind("blur");
			$("textarea.outcome").not("#autopopulate-outcome").blur(function() {
                                var cursor_position = $(this).getSelection();
				var textarea_element = this;
				var length_value = 0;
				var d=new Date();
				var hour = d.getHours() ;
                                var minutes = d.getMinutes() < 10 ? "0" + d.getMinutes() : d.getMinutes() ;
				var start_length_value = 0;
				var message_start = -1;
				if(hour > 12) {
				    hour = hour - 12;
				    var medium = "pm";
				}else{
				    var medium = "am";
				}
                                
                                hour = hour < 10 ? "0" + hour : hour ;

				var timestamp = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear() + ", " + hour + ":" + minutes + medium + " " + d.toString().replace(/^.*\(|\)$/g, "").replace(/[^A-Z]/g, "");
				var value_rows = $(this).val().split("\n");
                                var row_key = 0;
				//console.log(value_rows);				
				$.each(value_rows, function (k,v) {
					
					start_length_value = length_value;					
					length_value += v.length;
					
					if(v.length == 0) {
						//console.log("go");
						return true;					
					}
                                        row_key = k;
					//console.log(cursor_position.start);
					//console.log(length_value);
					if(cursor_position.start <= length_value+value_rows.length) {
						

						var note_of_order = k;
						if (start_length_value == 0) {
							message_start = -1;
							message_end = 0;
						}else{

							message_start = start_length_value-1;
							message_end = start_length_value+ k;						
						}
						
						$(textarea_element).attr("textarea-custom", 1);

						return false;
					}

				});
                                var ts = Math.round((new Date()).getTime() / 1000);

                                if($(this).attr("timestamp") == undefined || (ts - $(this).attr("timestamp")) > 60) {

                                    $(this).val(this.value.substr(0, message_start+(row_key+1)) + timestamp + " : " + this.value.substr(message_end));
                                    $(this).attr("timestamp", ts);
                                }
			});


		}, 1);

}
                </script>';
    }

}

?>
