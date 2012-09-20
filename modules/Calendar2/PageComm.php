<?php
if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');
/* * ****************************************************************************
  OpensourceCRM End User License Agreement

  INSTALLING OR USING THE OpensourceCRM's SOFTWARE THAT YOU HAVE SELECTED TO
  PURCHASE IN THE ORDERING PROCESS (THE "SOFTWARE"), YOU ARE AGREEING ON BEHALF OF
  THE ENTITY LICENSING THE SOFTWARE ("COMPANY") THAT COMPANY WILL BE BOUND BY AND
  IS BECOMING A PARTY TO THIS END USER LICENSE AGREEMENT ("AGREEMENT") AND THAT
  YOU HAVE THE AUTHORITY TO BIND COMPANY.

  IF COMPANY DOES NOT AGREE TO ALL OF THE TERMS OF THIS AGREEMENT, DO NOT SELECT
  THE "ACCEPT" BOX AND DO NOT INSTALL THE SOFTWARE. THE SOFTWARE IS PROTECTED BY
  COPYRIGHT LAWS AND INTERNATIONAL COPYRIGHT TREATIES, AS WELL AS OTHER
  INTELLECTUAL PROPERTY LAWS AND TREATIES. THE SOFTWARE IS LICENSED, NOT SOLD.

 * The COMPANY may not copy, deliver, distribute the SOFTWARE without written
  permit from OpensourceCRM.
 * The COMPANY may not reverse engineer, decompile, or disassemble the 
  SOFTWARE, except and only to the extent that such activity is expressly
  permitted by applicable law notwithstanding this limitation.
 * The COMPANY may not sell, rent, or lease resell, or otherwise transfer for
  value, the SOFTWARE.
 * Termination. Without prejudice to any other rights, OpensourceCRM may 
  terminate this Agreement if the COMPANY fail to comply with the terms and
  conditions of this Agreement. In such event, the COMPANY must destroy all
  copies of the SOFTWARE and all of its component parts.
 * OpensourceCRM will give the COMPANY notice and 30 days to correct above 
  before the contract will be terminated.

  The SOFTWARE is protected by copyright and other intellectual property laws and
  treaties. OpensourceCRM owns the title, copyright, and other intellectual
  property rights in the SOFTWARE.
 * *************************************************************************** */
require_once("modules/Calendar2/functions.php");
$d_start_time = $current_user->getPreference('d_start_time');
$d_end_time = $current_user->getPreference('d_end_time');


if (empty($d_start_time))
    $d_start_time = "09:00";
if (empty($d_end_time))
    $d_end_time = "18:00";


$tarr = explode(":", $d_start_time);
$d_start_hour = $tarr[0];
$d_start_min = $tarr[1];
$tarr = explode(":", $d_end_time);
$d_end_hour = $tarr[0];
$d_end_min = $tarr[1];

$hour_start = $d_start_hour;
$minute_start = $d_start_min;
$hour_end = $d_end_hour;
$minute_end = $d_end_min;


$day_duration_hours = $hour_end - $hour_start;
if ($minute_end < $minute_start) {
    $day_duration_hours--;
    $day_duration_minutes = $minute_start - $minute_end;
}else
    $day_duration_minutes = $minute_end - $minute_start;

global $current_language, $currentModuele;
$current_module_strings = return_module_language($current_language, 'Calendar2');

if ($currentModule == 'Home') {
    //for dashlet
    $dom_name = 'dom_cal_day_short';
} else {
    $dom_name = 'dom_cal_day_long';
}

$weekday_names = array();
$of = 0;
$startday = $first_day_of_a_week;

if ($startday != "Monday") {
    $of = 1;
    $count = 0;
    foreach ($GLOBALS['app_list_strings'][$dom_name] as $k => $v) {
        if ($k < 2)
            continue;
        $weekday_names[$count] = $GLOBALS['app_list_strings'][$dom_name][$k - $of];
        $count++;
    }
    $weekday_names[6] = $GLOBALS['app_list_strings'][$dom_name][7];
} else {
    $of = 0;
    $count = 0;
    foreach ($GLOBALS['app_list_strings'][$dom_name] as $k => $v) {
        if ($k < 2)
            continue;
        $weekday_names[$count] = $GLOBALS['app_list_strings'][$dom_name][$k - $of];
        $count++;
    }
    $weekday_names[6] = $GLOBALS['app_list_strings'][$dom_name][1];
}

/*
  foreach($GLOBALS['app_list_strings'][$dom_name] as $k => $v)
  $weekday_names[$k-2] = $GLOBALS['app_list_strings'][$dom_name][$k - $of];

  if($startday == "Monday")
  $weekday_names[6] = $GLOBALS['app_list_strings'][$dom_name][1];
  else
  $weekday_names[6] = $GLOBALS['app_list_strings'][$dom_name][7];
 */


$today_unix = to_timestamp($gmt_today);

function to_hours($t) {
    if (intval($t) < 10)
        return "0" . $t;
    else
        return $t;
}

global $js_custom_version;
global $sugar_version;
?>

<script type="text/javascript">
<?php
if (isPro()) {
    require_once('modules/Teams/Team.php');
    $tm = new Team();
    $tm->retrieve($GLOBALS['current_user']->default_team);
    echo 'var default_team_name = "' . $tm->name . '";';
    echo 'var default_team_id = "' . $GLOBALS['current_user']->default_team . '";';
    if (is551()) {
        echo 'var is551 = true;';
    } else {
        echo 'var is551 = false;';
    }
} else {
    echo 'var default_team_name = "";';
    echo 'var default_team_id = "";';
    echo 'var is551 = false;';
}
?>
</script>

<link type="text/css" href="modules/Calendar2/css/themes/base/ui.all.css" rel="stylesheet" />
<script type="text/javascript" src="modules/Calendar2/js/jquery-1.3.2.min.js?s=<?php echo $sugar_version; ?>&c=<?php echo $js_custom_version; ?>"></script>
<script type="text/javascript" src="modules/Calendar2/js/jquery.form.js?s=<?php echo $sugar_version; ?>&c=<?php echo $js_custom_version; ?>"></script>
<script type="text/javascript" src="modules/Calendar2/js/jquery-ui-1.7.2.custom.min.js?s=<?php echo $sugar_version; ?>&c=<?php echo $js_custom_version; ?>"></script>
<script type="text/javascript" src="modules/Calendar2/js/ui.core.js?s=<?php echo $sugar_version; ?>&c=<?php echo $js_custom_version; ?>"></script>
<script type="text/javascript" src="modules/Calendar2/js/ui.datepicker.js?s=<?php echo $sugar_version; ?>&c=<?php echo $js_custom_version; ?>"></script>

<script type="text/javascript" src="include/javascript/sugar_grp_overlib.js?s=<?php echo $sugar_version; ?>&c=<?php echo $js_custom_version; ?>"></script>

<script type="text/javascript" src="modules/Calendar2/PageComm.js?s=<?php echo $sugar_version; ?>&c=<?php echo $js_custom_version; ?>"></script>

<link type="text/css" href="modules/Calendar2/PageStyle.css" rel="stylesheet" />
<style type="text/css">
                    div#multiopp-print {
                        margin-right:40px;
                        width:64px;
                        top:4px;
                        float: right;
                        cursor: pointer;
                    }

                    div#multiopp-print .list-icon {
                        display: inline-block;
                        height: 12px;
                        background-position: -64px -16px;
                    }

                    ul.print-list {
                        background: #DDD;
                        padding: 0 5px 5px 0;
                        text-align: left;
                        display: none;
                        border-bottom: 1px solid #999;
                        width: 101px;
                    }

                    ul.print-list li {
                        list-style: none;
                    }
                    ul.print-list li:hover {
                        border: 1px solid #999;
                        background-color: #F0F0F0;
                    }
                    
	.questions {
		width: 420px!important;
		height: 60px!important;
		padding: 10px 0 0 10px;
	}
	.questions img {
		float: left;
	}
	.questions span {
		float: left;
		margin: 20px 0 0 10px;
                                    font-size: 14px;
	}
	
            </style>
<script type="text/javascript">
    var pview = "";

    var t_step = <?php echo $t_step; ?>;
    var dropped = 0;
    var records_openable = true;
    var moved_from_cell;
    var deleted_id = "";
    var deleted_module = "";
    var old_caption = "";
    var max_zindex = 50;
    var disable_creating = false;
    var current_user_id = "<?php echo $GLOBALS['current_user']->id; ?>";	
    var time_format = "<?php echo $GLOBALS['timedate']->get_user_time_format(); ?>";	
    var day_duration_hours = <?php echo $day_duration_hours ?>;
    var day_duration_minutes = <?php echo $day_duration_minutes ?>;
	
    var lbl_edit = "<?php echo $current_module_strings['LBL_EDIT_RECORD']; ?>";
    var lbl_loading = "<?php echo $current_module_strings['LBL_LOADING']; ?>";
    var lbl_error_saving = "<?php echo $current_module_strings['LBL_ERROR_SAVING']; ?>";
    var lbl_error_loading = "<?php echo $current_module_strings['LBL_ERROR_LOADING']; ?>";
    var lbl_another_browser = "<?php echo $current_module_strings['LBL_ANOTHER_BROWSER']; ?>";
    var lbl_first_team = "<?php echo $current_module_strings['LBL_FIRST_TEAM']; ?>";
    var lbl_remove_participants = "<?php echo $current_module_strings['LBL_REMOVE_PARTICIPANTS']; ?>";
    var lbl_cannot_remove_first = "<?php echo $current_module_strings['MSG_CANNOT_REMOVE_FIRST']; ?>";
    //this is used by avaxsave to check if it needs to remove am/pm to display a asved record
    var current_module = "<?php echo $currentModule; ?>";
    var today_string = "<?php echo $today_string; ?>";
    var lbl_wait_please = "<?php echo "Please wait while system is being updated ..............."; ?>";
 var selected_customers = new Array();      
    //	alert(<?php // pr($GLOBALS['current_user']->id);     ?>);
        
    $(function() {

        
<?php
if (isPro() && is551()) {
    echo "	collection['EditView_team_name'].add2 = 	function(){
                                                                if($.browser.opera){
                                                                        alert(lbl_another_browser);
                                                                        return 0;
                                                                }else
                                                                        collection['EditView_team_name'].add();			
                                                        }
	
        collection['EditView_team_name'].remove = function(num){
                                                                if(num == 0){
                                                                        alert(lbl_first_team);							
                                                                        return \"\";
                                                                }else{

                                                                   radio_els=this.get_radios();
                                                                   if(radio_els.length==1){
                                                                    div_el=document.getElementById(this.field_element_name+'_input_div_'+num);
                                                                    input_els=div_el.getElementsByTagName('input');
                                                                    input_els[0].value='';
                                                                    input_els[1].value='';
                                                                    if(this.primary_field){
                                                                     div_el=document.getElementById(this.field_element_name+'_radio_div_'+num);
                                                                     radio_els=div_el.getElementsByTagName('input');
                                                                     radio_els[0].checked=false;
                                                                    }
                                                                   }else{
                                                                    div_el=document.getElementById(this.field_element_name+'_input_div_'+num);
                                                                    tr_to_remove=document.getElementById('lineFields_'+this.field_element_name+'_'+num);
                                                                    div_el.parentNode.parentNode.parentNode.removeChild(tr_to_remove);
                                                                    var radios=this.get_radios();
                                                                    div_id='lineFields_'+this.field_element_name+'_'+num;
                                                                    if(typeof sqs_objects[div_id.replace(\"_field_\",\"_\")]!='undefined'){
                                                                     delete(sqs_objects[div_id.replace(\"_field_\",\"_\")]);
                                                                    }
                                                                    var checked=false;
                                                                    for(var k=0;k<radios.length;k++){
                                                                     if(radios[k].checked){
                                                                      checked=true;
                                                                     }
                                                                    }
                                                                    var primary_checked=document.forms[this.form].elements[this.field+\"_allowed_to_check\"];
                                                                    var allowed_to_check=true;
                                                                    if(primary_checked&&primary_checked.value=='false'){
                                                                     allowed_to_check=false;
                                                                    }
                                                                    if(/EditView/.test(this.form)&&!checked&&typeof radios[0]!='undefined'&&allowed_to_check){
                                                                     radios[0].checked=true;
                                                                     this.changePrimary(true);
                                                                     this.js_more();
                                                                     this.js_more();
                                                                    }
                                                                    if(radios.length==1){
                                                                     this.more_status=false;
                                                                     document.getElementById('more_'+this.field_element_name).style.display='none';
                                                                     this.show_arrow_label(false);
                                                                     this.js_more();
                                                                    }else{
                                                                     this.js_more();
                                                                     this.js_more();
                                                                    }
                                                                  }
                                                                }
                                                  }";
}
?>

        var droped_to_time;
        toggleDefaultDisplay('shared_cal_edit');
		
        $(".t_cell").droppable({
            hoverClass: 't_cell_active',
            tolerance: 'pointer',
            accept: '.record_item, .scDrag',
            drop: function(event, ui) {
				
                if(!ui.draggable.hasClass('scDrag')){
                    dropped = 1;
				
                    ui.draggable.css( { "position" : "relative", "top" : "0px", "float" : "none" } );
                    ui.draggable.appendTo($(this));	
                    align_divs($(this).attr("id"));	
                    align_divs(moved_from_cell);
				
                    cut_record(ui.draggable.attr('id'));	
				
                    var span = "<span class='rfloat' onmouseover='return show_i(" + '"' + ui.draggable.attr("record") + '"'  +  ", " + '"' + ui.draggable.attr("acttype") + '"' + ");' onmouseout='return nd(1000);' >&nbsp; i &nbsp;</span><br style='clear: both;'>";
                                

                    var widget_title = ui.draggable.attr('widget_title');
                    for (var ii in ActRecords) {
                        if (ActRecords[ii].record != ui.draggable.attr('id')) {
                            continue;
                        }
                        //widget_title = ActRecords[ii].widget_title;
                        break;
                    }
							
                    ui.draggable.attr("date_start",$(this).attr("datetime"));
                    ui.draggable.find('div.record_head').html(widget_title + " " + "" + span );
				
                    droped_to_time = $(this).attr("lang");

                    $.getJSON(
                    "index.php?module=Calendar2&action=AjaxAfterDrop&sugar_body_only=true",
                    {
                        "type" : ui.draggable.attr("acttype"),
                        "record" : ui.draggable.attr("id"),
                        "datetime" : $(this).attr("datetime")
                    },
                    function(res){
                        records_openable = true;
                        ui.draggable.attr("time_start", droped_to_time);

                        if(res.succuss == 'yes'){
                            //AddRecords(res);
                            $.each(
                            res.users,
                            function (i,v){
                                //updates the current user's scheduler row
                                urllib.getURL('./vcal_server_cal2.php?type=vfb&source=outlook&user_id='+v,[["Content-Type", "text/plain"]], function (result) { 
                                    if (typeof GLOBAL_REGISTRY.freebusy == 'undefined') {
                                        GLOBAL_REGISTRY.freebusy = new Object();
                                    }
                                    if (typeof GLOBAL_REGISTRY.freebusy_adjusted == 'undefined') {
                                        GLOBAL_REGISTRY.freebusy_adjusted = new Object();
                                    }
                                    // parse vCal and put it in the registry using the user_id as a key:
                                    GLOBAL_REGISTRY.freebusy[v] = SugarVCalClient.parseResults(result.responseText, false);                  
                                    // parse for current user adjusted vCal
                                    GLOBAL_REGISTRY.freebusy_adjusted[v] = SugarVCalClient.parseResults(result.responseText, true);
                                })
                            } //function
                        ); //each
                        } //endif
                    } //function(res)
                ); //getJSON
                }else{
								
                    $.getJSON(
                    "index.php?module=Calendar2&action=AjaxFlyCreate&sugar_body_only=true&currentmodule=" + current_module,
                    {
                        "type" : 'meeting',
                        "duration_hours" : '1',
                        "duration_minutes" : '0',
                        "contact_id" : ui.draggable.attr("contact_id"),
                        "account_id" : ui.draggable.attr("account_id"),
                        "title" : ui.draggable.attr("account_name") + " " + ui.draggable.html(),
                        "datetime" : $(this).attr("datetime")
                    },
                    function(res){
                        records_openable = true;
                        if(res.succuss == 'yes'){
                            AddRecords(res);
                            //AddRecordToPage(res);
                            $.each(
                            res.users,
                            function (i,v){
                                //updates the current user's scheduler row
                                urllib.getURL('./vcal_server_cal2.php?type=vfb&source=outlook&user_id='+v,[["Content-Type", "text/plain"]], function (result) { 
                                    if (typeof GLOBAL_REGISTRY.freebusy == 'undefined') {
                                        GLOBAL_REGISTRY.freebusy = new Object();
                                    }
                                    if (typeof GLOBAL_REGISTRY.freebusy_adjusted == 'undefined') {
                                        GLOBAL_REGISTRY.freebusy_adjusted = new Object();
                                    }
                                    // parse vCal and put it in the registry using the user_id as a key:
                                    GLOBAL_REGISTRY.freebusy[v] = SugarVCalClient.parseResults(result.responseText, false);                  
                                    // parse for current user adjusted vCal
                                    GLOBAL_REGISTRY.freebusy_adjusted[v] = SugarVCalClient.parseResults(result.responseText, true);
                                })
                            }
                        );
                        }
                    }
                );
				
                }
            },
			
            over: function(event, ui) { 
                ui.draggable.find('div.record_head').html($(this).attr('lang'));	
            },
		
				
            deactivate: function(event, ui) {
                if(dropped == 0){
                    ui.draggable.find('div.record_head').html(old_caption);
                }
				
            }
			
        });
		
        $("div.left_cell:nth-child(odd)").addClass("odd");
        $("div.t_cell:nth-child(odd)").addClass("odd");
        $("div.t_icell:nth-child(odd)").addClass("odd");
        $(".t_cell").click(	function() {
            if(!disable_creating){
                $('#ui-dialog-title-record_dialog').html("<?php echo $current_module_strings['LBL_CREATE_NEW_RECORD']; ?>");

                $('#record_dialog').dialog('open');
                $('#date_start_date').attr("value",$(".t_cell").attr("datetime"));
                $("form#EditView #return_module").val("Meetings");
                $("form#EditView #cur_module").val("Meetings");
                $("#form_record").attr("value","");
                $("#name").attr("value","");
                $("#parent_name_custno_c").css('display','inline');
                $("#lead_account_name").css('display','none');
                $("#repeat_type").removeAttr("disabled");
                $("#opp_table").empty();
                //$("#repeat_type option[value='Weekly']").attr('selected', 'selected');
                var lead_account_name = '';
                var account_custno = '';

                // add new record for shared functionality
                $("#cal2_assigned_user_name").val($(this).attr("shared_user_name"));
                $("#cal2_assigned_user_id").val($(this).attr("shared_user_id"));


                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(1)").removeAttr("disabled");
                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(6)").attr("disabled","disabled");
                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(5)").removeAttr("disabled");
                $("#previous_outcome_span").css('display','none');
                $("#previous_outcome_c").css('display','none');
                $("#next_date_span").css('display','none');
                $("#next_date").css('display','none');
                $("#next_description_span").css('display','none');
                $("#next_description").css('display','none');
                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(7)").attr("disabled","disabled");
                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(8)").attr("disabled","disabled");
                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(4)").attr("disabled","disabled");
                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(3)").attr("disabled","disabled");
                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(2)").attr("disabled","disabled");                                     
                //$(".record_dialog_class .ui-dialog-buttonpane button:nth-child(9)").css('display','none');   
                <?php
                    $default_end_date = strtotime(date("12/31/Y"));
                    $default_end_date = timestamp_to_user_formated($default_end_date,$GLOBALS['timedate']->get_date_format());?>
                   var end_default =    "<?php echo $default_end_date;?>";              
                   $("#repeat_end_date").val(end_default);                                     
														
                var combo_date_start = new Datetimecombo($(this).attr("datetime"), "date_start", "<?php echo $timedate->get_user_time_format(); ?>", 102, '', ''); 
                var weekDay = new Date(combo_date_start.datetime).getDay();
                $(".record_dialog_class .weeks_checks").removeAttr('checked');
                $(".record_dialog_class .weeks_checks[value='"+(weekDay+1)+"']").attr('checked', 'checked');
                text = combo_date_start.html('SugarWidgetScheduler.update_time();');
                document.getElementById('date_start_time_section').innerHTML = text;
                eval(combo_date_start.jsscript('SugarWidgetScheduler.update_time();'));
                combo_date_start.update(); 
							
                SugarWidgetScheduler.update_time();
            }
        }
    );
        // fullscreen quick input popup
        function fullscreen_popup (object) {
            $('div.ui-dialog-titlebar:visible').attr("style","width:99%;height: 18px;position: fixed;z-index: 9999;");
            $('div.record_dialog_class_qi:visible[aria-labelledby=ui-dialog-title-record_dialog_quick_input]').css('width', $(window).width());
            //$('div.ui-dialog:visible[aria-labelledby=ui-dialog-title-record_dialog_quick_input]').css('height', $(window).height());
            $('div.record_dialog_class_qi:visible[aria-labelledby=ui-dialog-title-record_dialog_quick_input]').css("top", 0);
            $('div.record_dialog_class_qi:visible[aria-labelledby=ui-dialog-title-record_dialog_quick_input]').css("left", 0);
            $('#customers_list').css("width", '100%');
            $(object).removeClass('ui-corner-fullscreen');
            $(object).addClass('ui-corner-fullscreen-minimize');
            positioning_autopopulate_fields();
            $('.ui-corner-fullscreen-minimize').bind('click',  function () { 
                minimize_popup(object); 
            });


        }
        
        // minimize quick input popup
        function minimize_popup (object) {
            $('div.record_dialog_class_qi:visible[aria-labelledby=ui-dialog-title-record_dialog_quick_input]').css('width', '1024px');
             $('div.ui-dialog-titlebar:visible').attr("style","width:1017px;height: 18px;position: fixed;z-index: 9999;");
            $(object).removeClass('ui-corner-fullscreen-minimize');
            $(object).addClass('ui-corner-fullscreen');
            positioning_autopopulate_fields(); 
            $('.ui-corner-fullscreen').bind('click',  function () { 
                fullscreen_popup(object); 
            });
        }

        function print_list_show (){
                if($(".print-list").css("display") == "none"){
                    $(".print-list").show();
                }else{
                    $(".print-list").hide();
                }
            }
            
        function loadSavedDate(typeDate,typeInput){
            var prev = eval('(' + sessionStorage.getItem(typeDate) + ')');
            var end_rec_meet=prev.jsonObject['Meeting'];
            var end_rec_call=prev.jsonObject['Call'];
            if(typeof end_rec_meet != 'undefined'){
                $.each(end_rec_meet, function (k, v) {
                    $("#"+k+"_"+typeInput).val(v);
                });
            }
            if(typeof end_rec_call != 'undefined'){
                $.each(end_rec_call, function (k, v) {
                    $("#"+k+"_"+typeInput).val(v);
                });
            }
        }    
        
        function saveEndRecurrence(elem,inputVal){
            var recID = $(elem).attr('rec_id');
            var startDateVal = $("#"+recID+"_start_date").html();
            var endDateVal = $("#"+recID+"_end_date").html();
            var typeSchedule = $("#"+recID+"_type_schedule").html();
            if(isDate(inputVal)&&isDate(startDateVal)&&isDate(endDateVal)){
                var startDate = new Date(startDateVal).valueOf();
                var endDate = new Date(endDateVal).valueOf();
                var endRec = new Date(inputVal).valueOf();
                if(endRec >= startDate && endRec <= endDate){
                    $(elem).val(inputVal);
                    $("#"+recID+"_extend_reccurence").datepicker('disable');
                    if(sessionStorage.getItem("endReccurence") != null){ 
                        var end_reccurence_obj= eval('(' + sessionStorage.getItem("endReccurence") + ')');
                        var end_reccurence_arr=end_reccurence_obj.jsonObject[typeSchedule];
                        if(typeof end_reccurence_arr == 'undefined')
                            end_reccurence_arr = {};
                        end_reccurence_arr[recID] = inputVal;
                        end_reccurence_obj.jsonObject[typeSchedule] = end_reccurence_arr; 
                        sessionStorage.setItem("endReccurence", JSON.stringify(end_reccurence_obj.jsonObject)); 
                    }else {
                        var end_reccurence_obj = {};
                        var end_reccurence_arr = {};
                        end_reccurence_arr[recID] = inputVal;
                        end_reccurence_obj[typeSchedule]  = end_reccurence_arr;
                        sessionStorage.setItem("endReccurence", JSON.stringify(end_reccurence_obj));   
                    }
                }else{
                    //alert("End Recurrence date must be within the start and end dates.");
                    $("#"+recID+"_extend_reccurence").datepicker('disable');
                    $(elem).val(endDateVal);
                }
            }else if(inputVal == ""){
                $("#"+recID+"_extend_reccurence").datepicker('enable');
                if(sessionStorage.getItem("endReccurence") != null){ 
					var end_reccurence_obj= eval('(' + sessionStorage.getItem("endReccurence") + ')');
					var end_reccurence_arr=end_reccurence_obj.jsonObject[typeSchedule];
					if(typeof end_reccurence_arr[recID] != 'undefined'){
						delete end_reccurence_arr[recID];
						end_reccurence_obj.jsonObject[typeSchedule] = end_reccurence_arr; 
						sessionStorage.setItem("endReccurence", JSON.stringify(end_reccurence_obj.jsonObject));
					} 
				} 
            }else{
                alert("Invalid Date");
            }
        }  
        function saveExtendRecurrence(elem,inputVal){
            var recID = $(elem).attr('rec_id');
            var endDateVal = $("#"+recID+"_end_date").html();
            var typeSchedule = $("#"+recID+"_type_schedule").html();
            if(isDate(inputVal)&&isDate(endDateVal)){
                var endDate = new Date(endDateVal).valueOf();
                var extRec = new Date(inputVal).valueOf();
                if(extRec > endDate){
                    $(elem).val(inputVal);
                    $("#"+recID+"_end_reccurence").datepicker('disable');
                    if(sessionStorage.getItem("extendReccurence") != null){ 
                        var extend_reccurence_obj= eval('(' + sessionStorage.getItem("extendReccurence") + ')');
                        var extend_reccurence_arr=extend_reccurence_obj.jsonObject[typeSchedule];
                        if(typeof extend_reccurence_arr == 'undefined')
                            extend_reccurence_arr = {};
                        extend_reccurence_arr[recID] = inputVal;
                        extend_reccurence_obj.jsonObject[typeSchedule] = extend_reccurence_arr; 
                        sessionStorage.setItem("extendReccurence", JSON.stringify(extend_reccurence_obj.jsonObject)); 
                    }else {
                        var extend_reccurence_obj = {};
                        var extend_reccurence_arr = {};
                        extend_reccurence_arr[recID] = inputVal;
                        extend_reccurence_obj[typeSchedule]  = extend_reccurence_arr;
                        sessionStorage.setItem("extendReccurence", JSON.stringify(extend_reccurence_obj));   
                    }
                }else{
                    //alert("Extend End Date of Recurrence may not be earlier than End Date of Recurrence.");
                    $("#"+recID+"_end_reccurence").datepicker('disable');
                    $(elem).val(endDateVal);
                }
            }else if(inputVal == ""){
                $("#"+recID+"_end_reccurence").datepicker('enable');
                if(sessionStorage.getItem("extendReccurence") != null){ 
					var extend_reccurence_obj= eval('(' + sessionStorage.getItem("extendReccurence") + ')');
					var extend_reccurence_arr=extend_reccurence_obj.jsonObject[typeSchedule];
					if(typeof extend_reccurence_arr[recID] != 'undefined'){
						delete extend_reccurence_arr[recID];
						extend_reccurence_obj.jsonObject[typeSchedule] = extend_reccurence_arr; 
						sessionStorage.setItem("extendReccurence", JSON.stringify(extend_reccurence_obj.jsonObject));
					} 
				} 
            }else{
                alert("Invalid Date");
            }
    }
    
    function loadRecurrenceList(loadOther,isChached){
		var url ="index.php?module=Calendar2&action=AjaxGetReccurence";
		 $("#recurrence_list").dataTable({
							"bJQueryUI": true,
							"bDestroy": true,
							"bProcessing": true,
							"bServerSide": true,
							"bAutoWidth": false,
							"aaSorting": [[ 4, "asc" ]],
						   "sDom": '<"H"l<"#loadOther">fr<"#autofill_date">>t<"F"ip>',
							"aoColumns": [ 
								{ "bSearchable": true },
								{ "bSearchable": true },
							   { "bSearchable": true },
								{ "bSearchable": false },
							   { "bSearchable": false },
							   { "bSortable": false , "sWidth": "115px","bSearchable": false },
							   { "bSortable": false ,"sWidth": "115px","bSearchable": false  }
								],
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
							"fnDrawCallback": function(oSettings, json) {
										$(".end_reccurence").datepicker({
												dateFormat: "mm/dd/yy",
												showOn: "button",
												buttonImage: "themes/default/images/jscalendar.gif",
												buttonImageOnly: true,
												showButtonPanel: true,
												closeText: 'Clear',
												 beforeShow: function( input, inst) {
														var recID = $(input).attr('rec_id');
														var startDateVal = $("#"+recID+"_start_date").html();
														var endDateVal = $("#"+recID+"_end_date").html();
														if(isDate(startDateVal)&&isDate(endDateVal)){
															inst.settings.minDate = new Date(new Date(startDateVal).getTime() + (24 * 60 * 60 * 1000));
															inst.settings.maxDate = new Date(new Date(endDateVal).getTime() - (24 * 60 * 60 * 1000));
														}
													   setTimeout(function() {
															$(".ui-datepicker-close").click(function(){
																DP_jQuery.datepicker._clearDate(input);
															});
														}, 10 );
												 },
												 onChangeMonthYear:function() {
														var input = this;
													   setTimeout(function() {
															$(".ui-datepicker-close").click(function(){
															   DP_jQuery.datepicker._clearDate(input);
															});
														}, 10 );
												 }
										});
										
										$(".extend_reccurence").datepicker({
												dateFormat: "mm/dd/yy",
											   // defaultDate: new Date(("#"+$(this).attr('rec_id')+"_end_date").html()),
												showOn: "button",
												buttonImage: "themes/default/images/jscalendar.gif",
												buttonImageOnly: true,
												showButtonPanel: true,
												closeText: 'Clear',
												 beforeShow: function( input, inst ) {
														var recID = $(input).attr('rec_id');
														var endDateVal = $("#"+recID+"_end_date").html();
														if(isDate(endDateVal)){
															inst.settings.defaultDate = new Date(endDateVal);
															inst.settings.minDate = new Date(new Date(endDateVal).getTime() + (24 * 60 * 60 * 1000));
														}
														setTimeout(function() {
																$(".ui-datepicker-close").click(function(){
																	DP_jQuery.datepicker._clearDate(input);
																});
															}, 10 );
												 },
												 onChangeMonthYear:function() {
														var input = this;
													   setTimeout(function() {
															$(".ui-datepicker-close").click(function(){
															   DP_jQuery.datepicker._clearDate(input);
															});
														}, 10 );
												 }
										});
										
										$("#ui-datepicker-div").css('z-index', '999999');
										$('img.ui-datepicker-trigger').css({'cursor':'pointer','padding-left': '2px'});
										
										$('.end_reccurence').change(function() {
										   saveEndRecurrence(this,this.value);
										});
										 $('.extend_reccurence').change(function() {
											saveExtendRecurrence(this,this.value);
										});
										
										if($('#autofill-end_reccurence').val() != '' || $('#autofill-extend_reccurence').val() != ''){
											if($('#autofill-end_reccurence').val() != ''){
												var inputVal = $('#autofill-end_reccurence').val();
												if(isDate(inputVal)){
													$("#autofill-extend_reccurence").datepicker('disable');
													$('.end_reccurence').each(function(){
														saveEndRecurrence(this,inputVal);
													});
												}
											}
											if($('#autofill-extend_reccurence').val() != ''){
												var inputVal = $('#autofill-extend_reccurence').val();
												if(isDate(inputVal)){
													$("#autofill-end_reccurence").datepicker('disable');
													$('.extend_reccurence').each(function(){
														saveExtendRecurrence(this,inputVal);
													});
												}
											}
										}else if(sessionStorage.getItem("endReccurence") != null || sessionStorage.getItem("extendReccurence") != null) {
											 if(sessionStorage.getItem("endReccurence") != null){
												loadSavedDate("endReccurence","end_reccurence");
											 } 
											  if(sessionStorage.removeItem("extendReccurence") != null){
												loadSavedDate("extendReccurence","extend_reccurence");
											 } 
										}
							},
							 "sAjaxSource": url,
							"sPaginationType": "full_numbers",
							"fnServerData": function ( sSource, aoData, fnCallback ) {
							aoData.push( { name: "otherLoad", value: loadOther } );
							$.getJSON( sSource, aoData, function (json) { 
								fnCallback(json)
							} );
						}
		  });
		   $("#autofill_date").css("float", "right");
			  $("#recurrence_list_filter").css({"width":"auto","float":"left"});
			  $("#recurrence_list_length").css("width","20%");
			  $("#loadOther").css({"padding-right":"10%","float":"left"});
			  $("#loadOther").append('All:<input type="checkbox" id="load-other-checkbox" name="load-other-checkbox" '+isChached+'>');
			  $("#autofill_date").append('<span style="padding-right: 6px;font-size: 0.9em;"><input type="text" name="autofill-end_reccurence" class="autofill-date" id="autofill-end_reccurence" size="11" value=""></span>');
			  $("#autofill_date").append('<span style="padding-right: 6px;font-size: 0.9em;"><input type="text" name="autofill-extend_reccurence" class="autofill-date" id="autofill-extend_reccurence" size="11" value=""></span>');           
			  $("#recurrence_list").css('width','100%');
			  
				$(".autofill-date").datepicker({
					dateFormat: "mm/dd/yy",
					showOn: "button",
					buttonImage: "themes/default/images/jscalendar.gif",
					buttonImageOnly: true,
					showButtonPanel: true,
					closeText: 'Clear',
					beforeShow: function( input, inst ) {
						setTimeout(function() {
							$(".ui-datepicker-close").click(function(){
								if(input.id == "autofill-end_reccurence"){
									$('.end_reccurence').val('');
									$(".extend_reccurence").datepicker('enable');
									sessionStorage.removeItem("endReccurence");
								}
								 if(input.id == "autofill-extend_reccurence"){
									$('.extend_reccurence').val('');
									$(".end_reccurence").datepicker('enable');
									sessionStorage.removeItem("extendReccurence");
								}
								DP_jQuery.datepicker._clearDate(input);
							});
						}, 10 );
					},
					onChangeMonthYear:function() {
						var input = this;
						setTimeout(function() {
							$(".ui-datepicker-close").click(function(){
								if(input.id == "autofill-end_reccurence"){
									$('.end_reccurence').val('');
									$(".extend_reccurence").datepicker('enable');
									sessionStorage.removeItem("endReccurence");
								}
								 if(input.id == "autofill-extend_reccurence"){
									$('.extend_reccurence').val('');
									$(".end_reccurence").datepicker('enable');
									sessionStorage.removeItem("extendReccurence");
								}
								DP_jQuery.datepicker._clearDate(input);
							});
						}, 10 );
					}
				});
               $('#autofill-end_reccurence').change(function() { 
                    var inputVal = this.value;
                    if(isDate(inputVal)){
                        $("#autofill-extend_reccurence").datepicker('disable');
                        $('.end_reccurence').each(function(){
                            saveEndRecurrence(this,inputVal);
                        });
                    }else{
                        $('.end_reccurence').val('');
                        $(".extend_reccurence").datepicker('enable');
                        sessionStorage.removeItem("endReccurence");
                        $("#autofill-extend_reccurence").datepicker('enable');
                    }
                }); 
                $('#autofill-extend_reccurence').change(function() { 
                    var inputVal = this.value;
                    if(isDate(inputVal)){
                        $("#autofill-end_reccurence").datepicker('disable');
                        $('.extend_reccurence').each(function(){
                            saveExtendRecurrence(this,inputVal);
                        });
                     }else{
                         $('.extend_reccurence').val('');
                         $(".end_reccurence").datepicker('enable');
                         sessionStorage.removeItem("extendReccurence");
                        $("#autofill-end_reccurence").datepicker('enable');
                    }
                });
                 
                 $("#load-other-checkbox").click(function () {
					var checkboxelem = $(this);
					if (checkboxelem.is(":checked")) {
						loadRecurrenceList("true","checked");
					}else {
						loadRecurrenceList("false","");
					 }
				}); 
	} 
        //quick customer input
        $("#recurrence-list-button").click(function() {
            $('#recurrence_dialog').dialog('open');
            $('div.ui-dialog-titlebar:visible').append('<a style="-moz-user-select: none; right: 23px;" unselectable="on" role="button" class="ui-dialog-titlebar-close ui-corner-all recurrence-fullscreen-minimize" href="#"><span style="-moz-user-select: none; background-position: -34px -128px;" unselectable="on" class="ui-icon">close</span></a>');
            $('.recurrence-fullscreen-minimize').toggle(
                function() {
                       $('#recurrence_dialog').parent().height(600);
                        $('#recurrence_dialog').parent().width(950);
                        $('#recurrence_dialog').height(500);
                        $('#recurrence_dialog').width(925);
                        $('#recurrence_dialog').parent().css('left',((window.innerWidth-950)/2)+'px');
                        $('#recurrence_dialog').parent().css('top',((window.innerHeight-500)/2)+'px');
                        return false;
                },
                function() {
                        $('#recurrence_dialog').parent().height(window.innerHeight - 10);
                        $('#recurrence_dialog').parent().width(window.innerWidth - 20);
                        $('#recurrence_dialog').height(window.innerHeight - 100);
                        $('#recurrence_dialog').width(window.innerWidth - 45);
                        $('#recurrence_dialog').parent().css({'top':'0','left':'0'});
                        return false;
                }
            );
            if(sessionStorage.getItem("endReccurence") != null || sessionStorage.getItem("extendReccurence") != null) {
                sessionStorage.removeItem("endReccurence");
                sessionStorage.removeItem("extendReccurence");
            }   
			loadRecurrenceList("false","");
			
        });
        
        $("#current-customer-call-list, #leads-call-list").click(function() {
            
            
            $("#radio_call_qi, #radio_meeting_qi").removeAttr("disabled");

            if(sessionStorage.getItem("previous_records") !== null && sessionStorage.getItem("previous_records").length > 0) {
                window.previmported = eval("(" + sessionStorage.getItem("previous_records") + ")").jsonObject;
                window.previmported.sort(function (a, b) {
                    var pattern_value = /^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2})(pm|am)$/;
                    var first_date =  a.date_start;

                    var first = first_date.match(pattern_value);
					
                    if(first[6] == "pm") {
                        first[4] = Number(first[4]) + 12;
                    }
					
                    var first_date_timestamp = Date.UTC(first[3], Number(first[1])-1, Number(first[2]), first[4], Number(first[5]));
					
                    var second_date = b.date_start;

                    var second = second_date.match(pattern_value);
					
                    if(second[6] == "pm") {
                        second[4] = Number(second[4]) + 12;
                    }
					
                    var second_date_timestamp = Date.UTC(second[3], Number(second[1])-1, Number(second[2]), second[4], Number(second[5]));
					
                    return first_date_timestamp - second_date_timestamp;
                });
            }

            var d = new Date();
            var str_date = new RegExp("^" + mpad(d.getMonth() + 1, 2) + "\/" + mpad(d.getDate(), 2) + "\/" + d.getFullYear());

            var strregexp = str_date;

            if(window.previmported && window.previmported[window.previmported.length-1] && strregexp.test(window.previmported[window.previmported.length-1].date_start)) {




                var time_splited = window.previmported[window.previmported.length-1].time_start.split(":");		
			    
                window.meridiem = "am";

                if(time_splited[1]) {
                    var meridium_is_am = time_splited[1].indexOf("am");
                    if(meridium_is_am == -1) {
                        window.meridiem = "pm";
                        $("#date_start_meridiem_qi").val(window.meridiem);
                        window.minute = Number(time_splited[1].substr(0, meridium_is_am))+15;
					    

                    } else {
                        window.meridiem = "am";
                        $("#date_start_meridiem_qi").val(window.meridiem);
                        window.minute = Number(time_splited[1].substr(0, meridium_is_am))+15;
					    

                    }
                }

                if(time_splited[0]) {
                    if (Number(time_splited[1].substr(0, meridium_is_am))+15 == 60) {
                        window.hour = Number(time_splited[0])+1;
                        window.minute = 0;
                        if (window.hour == 11 && window.meridiem == "am") window.meridiem = "pm";
                        if (window.hour == 11 && window.meridiem == "pm") window.meridiem = "am";
						
                    } else {
                        window.hour = Number(time_splited[0]);
                    }
					

                }else{
                    window.hour = 8;
                }
				 
            } else {
                window.minute = 0;
                window.hour = 8;		
            }
	
            $("#date_start_minutes_qi").val(mpad(window.minute, 2));
            $("#date_start_hours_qi").val(hpad(window.hour, 2));
            $("#date_start_meridiem_qi").val(window.meridiem);

            var popup_type = this.id;
	    
            $('#ui-dialog-title-record_dialog').html("<?php echo $current_module_strings['LBL_CREATE_NEW_RECORD']; ?>");

	    
            $('#record_dialog_quick_input').dialog('open');
	    
            $('#record_dialog_quick_input').attr("dialog-qi-type", popup_type);


            $('.ui-corner-fullscreen').remove();
            $('div.record_dialog_class_qi:visible[aria-labelledby=ui-dialog-title-record_dialog_quick_input]').css('width', $(window).width());
            //$('div.ui-dialog:visible[aria-labelledby=ui-dialog-title-record_dialog_quick_input]').css('height', $(window).height());
            $('div.record_dialog_class_qi:visible[aria-labelledby=ui-dialog-title-record_dialog_quick_input]').css("top", 0);
            $('div.record_dialog_class_qi:visible[aria-labelledby=ui-dialog-title-record_dialog_quick_input]').css("left", 0);
            $('#customers_list').css("width", '100%');
            
            var print_link = '<div id="multiopp-print" class="close" >'+
                    '<span class=\'printLink\'><img src=\'themes/Sugar/images/print.gif\' width=\'13\' height=\'13\' alt=\'Print\' border=\'0\' align=\'absmiddle\'></span>&nbsp;'+
                    '<span class=\'printLink\'>Print</span><span class=\'ui-icon list-icon\'></span>'+
                    '<ul class="print-list"><li><a  href="javascript:printDiv2(\'customers_list_wrapper\',1);" class=\'utilsLink\'>Table</a></li>'+
                    '<li><a  href="javascript:printDiv2(\'customers_list_wrapper\',2);" class=\'utilsLink\'>Filters and table</a></li>'+
                    '<li><a  href="javascript:printDiv2(\'customers_list_wrapper\',3);" class=\'utilsLink\'>Only checked</a></li></ul></div>';
            if($('#multiopp-print').length > 0){
                print_link = "";
            }
            $('div.ui-dialog-titlebar:visible').append(print_link+'<a style="-moz-user-select: none; right: 23px;" unselectable="on" role="button" class="ui-dialog-titlebar-close ui-corner-all ui-corner-fullscreen-minimize" href="#"><span style="-moz-user-select: none; background-position: -34px -128px;" unselectable="on" class="ui-icon">close</span></a>');
            $('div.ui-dialog-titlebar:visible').attr("style","width:99%;height: 18px;position: fixed;z-index: 9999;");
            $('#multiopp-print').bind('click',  function () { 
                print_list_show(); 
            });
            $('.ui-corner-fullscreen-minimize').bind('click',  function () { 
                minimize_popup(this); 
            });



	
            if (typeof(window.previmported) !== 'undefined' && window.previmported.length > 0) {//has in previous imported
                /*for (var key in window.previmported) {//loop prev imported
                        if ($(".previous_imported_record[record_for_open=" + window.previmported[key].record + "]").length == 0) {//not added in list
                                $('form#EditView-QI').append("<div record_for_open=\"" + window.previmported[key].record + "\" class=\"previous_imported_record\"><a href=\"#\">" + window.previmported[key].record_name + "</a></div>");
                        }
                }*/
                $(".record_dialog_class_qi .ui-dialog-buttonpane button:nth-child(1)").removeAttr("disabled","disabled");
                if(window.previmported[0].parent_type == "Accounts" && popup_type == 'current-customer-call-list') {
                    $(".record_dialog_class_qi .ui-dialog-buttonpane button:nth-child(1)").removeAttr("disabled","disabled");
                } else if (window.previmported[0].parent_type == "Leads" && popup_type == 'leads-call-list') {
                    $(".record_dialog_class_qi .ui-dialog-buttonpane button:nth-child(1)").removeAttr("disabled","disabled");
                }else{
                    $(".record_dialog_class_qi .ui-dialog-buttonpane button:nth-child(1)").attr("disabled","disabled");
                }
            } else {
                //$('form#EditView-QI .previous_imported_record').remove();
                $(".record_dialog_class_qi .ui-dialog-buttonpane button:nth-child(1)").attr("disabled","disabled");
            }

            $("div.previous_imported_record a").click(function () {
                $('#record_dialog_quick_input').dialog('close');
                $("#" + $(this).parent().attr("record_for_open")).trigger("click");
                return false;
            });

<?php
$table_customers = '<table id="customers_list" style="width: 100%"><thead><th style="width: 30px;">Include</th><th>CustNo</th><th style="width: 300px;">CustName</th><th style="width: 30px;">Address</th><th style="width: 30px;">City</th><th style="width: 30px;">State</th><th style="width: 30px;">PostalCode</th><th>Contact</th><th>Phone</th><th  style="width: 30px;">MTD Sales</th><th  style="width: 30px;">MTD Proj vs. Budget</th><th  style="width: 30px;">YTD Sales</th><th  style="width: 30px;">YTD Proj vs. Budget</th><th class="status-table-header" style="width: 30px;">Status</th><th class="pre-call-plan-table-header" style="width: 300px;">Pre-Call Plan</th><th class="outcome-table-header" style="width: 300px;">Outcome</th></thead><tbody><tr><td colspan="11" class="dataTables_empty">Loading data from server</td></tr></tbody></table>';
$table_leads = '<table id="customers_list" style="width: 100%"><thead><th style="width: 30px;">Include</th><th>LeadNo</th><th style="width: 300px;">Lead Account Name</th><th style="width: 30px;">Lead Contact</th><th style="width: 30px;">Phone</th><th style="width: 30px;">Lead Opportunity Potential</th><th style="width: 30px;">Status Description</th><th>Lead Status</th><th>Lead Source Description</th><th class="status-table-header" style="width: 30px;">Status</th><th class="pre-call-plan-table-header" style="width: 300px;">Pre-Call Plan</th><th class="outcome-table-header" style="width: 300px;">Outcome</th></thead><tbody><tr><td colspan="11" class="dataTables_empty">Loading data from server</td></tr></tbody></table>';
?>

                            if (popup_type == 'current-customer-call-list') {
                                $('div.record_dialog_class_qi').attr("popup_type", "accounts");
                                $('#customers-list-to-calendar').html('<?php print $table_customers; ?>');
                                $('#meetings_calls_calendar_quickinput .yui-skin-sam-fmp-sales span:nth-child(2), #meetings_calls_calendar_quickinput .yui-skin-sam-fmp-sales span:nth-child(5), #meetings_calls_calendar_quickinput .yui-skin-sam-fmp-sales span:nth-child(6), #meetings_calls_calendar_quickinput .yui-skin-sam-fmp-sales span:nth-child(7), #meetings_calls_calendar_quickinput .yui-skin-sam-fmp-sales span:nth-child(8)').show();
                                $('#meetings_calls_calendar_quickinput .yui-skin-sam-fmp-sales span:nth-child(4), #meetings_calls_calendar_quickinput .yui-skin-sam-fmp-sales span:nth-child(9), #meetings_calls_calendar_quickinput .yui-skin-sam-fmp-sales span:nth-child(10)').hide();
                                var url = "index.php?module=Accounts&action=getCustomersDefault";
                            } else if (popup_type == 'leads-call-list') {
                                //$('div.record_dialog_class_qi:visible[aria-labelledby=ui-dialog-title-record_dialog_quick_input]').css('width', '1200px');
                                $('div.record_dialog_class_qi').attr("popup_type", "leads");		
                                $('#customers-list-to-calendar').html('<?php print $table_leads; ?>');
                                $('#meetings_calls_calendar_quickinput .yui-skin-sam-fmp-sales span:nth-child(2), #meetings_calls_calendar_quickinput .yui-skin-sam-fmp-sales span:nth-child(5), #meetings_calls_calendar_quickinput .yui-skin-sam-fmp-sales span:nth-child(6), #meetings_calls_calendar_quickinput .yui-skin-sam-fmp-sales span:nth-child(7), #meetings_calls_calendar_quickinput .yui-skin-sam-fmp-sales span:nth-child(8)').hide();
                                $('#meetings_calls_calendar_quickinput .yui-skin-sam-fmp-sales span:nth-child(4), #meetings_calls_calendar_quickinput .yui-skin-sam-fmp-sales span:nth-child(9), #meetings_calls_calendar_quickinput .yui-skin-sam-fmp-sales span:nth-child(10)').show();
                                var url = "index.php?module=Leads&action=getCustomers";
 
                            }
                                
                            $("#customers_list").dataTable({
                                "bJQueryUI": true,
                                "bDestroy": true,
                                //"bProcessing": true,
                                "bServerSide": true,
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
                                "fnDrawCallback": function(oSettings, json) {
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
                                                selected_customers.push(this); 
                                            }else {
//                                                $.each(selected_customers, function (k, v) {
//                                                    if(v.id == checkboxelem.attr("id")) {
//                                                        delete selected_customers[k];
//                                                    }
//                                                });
                                                $(row).removeClass("print-row");
                                               if(selected_customers.length > 0) {
                                                    for(var i = 0; i < selected_customers.length; i++ ){
                                                         if(typeof selected_customers[i] != "undefined"){
                                                            if(selected_customers[i].id == checkboxelem.attr("id")) {
                                                                delete selected_customers[i];
                                                            }
                                                         }
                                                    }
                                                }
                                            }
                                        });
                                        positioning_autopopulate_fields();
                                    $("select.statuses_direction").not("select#direction_qi").val($("select#direction_qi").val());
                                    $("select.statuses_status").not("select#status_qi").val($("select#status_qi").val());
                                    $("textarea.pc-plan[textarea-custom!='1']").not("#autopopulate-pc-plan").each(function (k, v) {
                                        $(this).val($("#autopopulate-pc-plan").val() + '\n');	
                                    });
                                    $("textarea.pc-plan[textarea-custom!='1']").not("#autopopulate-pc-plan").attr('autopopulated', this.value);
                                    
                                      $("textarea.outcome[textarea-custom!='1']").not("#autopopulate-outcome").each(function (k, v) {
                                        $(this).val($("#autopopulate-outcome").val() + '\n');	
                                    });
                                    $("textarea.outcome[textarea-custom!='1']").not("#autopopulate-outcome").attr('autopopulated', this.value);
                                },
                                "sAjaxSource": url,
                                "sPaginationType": "full_numbers",
                                "sDom": 'C<"clear"><"H"lr<"#qi-form-inputs"><"#autofill_pcp_outcome"> >t<"F"ip>'
                            });
                            $("#customers_list_length").css("width", "13%");
                            $("#customers_list_filter").css("width", "23%");
                            $("#customers_list_filter").css("float", "left");
                            $("#autofill_pcp_outcome").css("float", "right");
<?php
                            $statuses .= '<select name="direction_qi" id="direction_qi" title="">';

                            foreach ($app_list_strings['call_direction_dom'] as $k => $v)
                                $statuses .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';

                            $statuses .= '</select><br / ><select name="status_qi" id="status_qi" title="">';

                            foreach ($app_list_strings['call_status_dom'] as $k => $v)
                                $statuses .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';
                            $statuses .= '</select>';
?>
                            $('#autofill_pcp_outcome').append('<div class="statuses-table-top-populate"><?php echo $statuses; ?></div>');
                            //$('.selecting-input-statuses').html('');
                            $("#autofill_pcp_outcome").append('<input type="text" name="pc-plan" class="pc-plan" id="autopopulate-pc-plan">');
                            $("#autofill_pcp_outcome").append('<input type="text" name="outcome" class="outcome" id="autopopulate-outcome">');


                            var d=new Date();
		
                            var hour = d.getHours();
                            if (hour > 12) {
                                hour = hour - 12;
                                var medium = "pm";
                            } else {
                                var medium = "am";
                            }

                            $("#autopopulate-pc-plan").keyup(function(event) {
                                //console.log(this.value);
                                var c= String.fromCharCode(event.keyCode);
                                var isWordcharacter = c.match(/\w/);	
                                if(!isWordcharacter) {
                                    c = '';
                                }

                                $("textarea.pc-plan[textarea-custom='1']").not("#autopopulate-pc-plan").each(function (k, v) {
                                    $(this).val(this.value + c);	
                                });
                                $("textarea.pc-plan[textarea-custom!='1']").not("#autopopulate-pc-plan").each(function (k, v) {
                                    $(this).val($("#autopopulate-pc-plan").val() + '\n');	
                                });
                                $("textarea.pc-plan[textarea-custom!='1']").not("#autopopulate-pc-plan").attr('autopopulated', this.value);
                            });

                            $("#autopopulate-pc-plan").blur(function() {
                                $("textarea.pc-plan").not("#autopopulate-pc-plan").each(function (k, v) {
                                    if($(this).val().length > 0) {						
                                        $(this).val($(this).val() + '\n');
                                    }	
                                });				
                            });



                            /*$("#autopopulate-pc-plan").blur(function() {
                                        //console.log(this.value);
                                        var timestamp = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear() + ", " + hour + ":" + d.getMinutes() + medium + " " + d.toString().replace(/^.*\(|\)$/g, "").replace(/[^A-Z]/g, "");
                                        //$("textarea.pc-plan").not("#autopopulate-pc-plan").val(timestamp + " : " + this.value);
                                        $("textarea.pc-plan").not("#autopopulate-pc-plan").val(this.value);
                                });*/

                            $("#autopopulate-outcome").keyup(function(event) {
                                //console.log(this.value);
                                var c= String.fromCharCode(event.keyCode);
                                var isWordcharacter = c.match(/\w/);	
                                if(!isWordcharacter) {
                                    c = '';
                                }

                                $("textarea.outcome[textarea-custom='1']").not("#autopopulate-outcome").each(function (k, v) {
                                    $(this).val(this.value + c);	
                                });
                                $("textarea.outcome[textarea-custom!='1']").not("#autopopulate-outcome").each(function (k, v) {
                                    $(this).val($("#autopopulate-outcome").val() + '\n');	
                                });
                                $("textarea.outcome[textarea-custom!='1']").not("#autopopulate-outcome").attr('autopopulated', this.value);
                            });

                            $("#autopopulate-outcome").blur(function() {
                                $("textarea.outcome").not("#autopopulate-outcome").each(function (k, v) {
                                    if($(this).val().length > 0) {						
                                        $(this).val($(this).val() + '\n');
                                    }	
                                });				
                            });

                            /*$("#autopopulate-outcome").blur(function() {
                                        var timestamp = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear() + ", " + hour + ":" + d.getMinutes() + medium + " " + d.toString().replace(/^.*\(|\)$/g, "").replace(/[^A-Z]/g, "");

                                        //$("textarea.outcome").not("#autopopulate-outcome").val(timestamp + " : " + this.value);
                                        $("textarea.outcome").not("#autopopulate-outcome").val(this.value);
                                });*/

                            $("select#direction_qi").change(function() {
                                $("select.statuses_direction").not("select#direction_qi").val(this.value);
                            });

                            $("select#status_qi").change(function() {
                                $("select.statuses_status").not("select#status_qi").val(this.value);
                            });
                            //console.log(popup_type);
                            cal2_hide_address_columns (popup_type);

                            $("textarea.pc-plan['textarea-custom'!='1']").not("#autopopulate-pc-plan").keydown(function(e) {
					
                                //$("textarea.pc-plan").not("#autopopulate-pc-plan").val(this.value);
                                //console.log(e);
                                var previous_text = $(e.currentTarget).attr('customized');
                                $(e.currentTarget).attr('customized', previous_text + String.fromCharCode(e.keyCode));
                            });
                                
                            $("#customers_list_wrapper #customers_list tbody").sortable();

                            $("#clicked_record_qi, #form_record_qi").val("");
                            $("input#save_tocalendar_button").unbind("click");
                            $("input#save_tocalendar_button").bind("click", function () {
                                CustomerListcreateRecords(popup_type); 
                                return false;		
                            });


                            //positioning_autopopulate_fields();

                            //});
            
                            //            $('#date_start_date').attr("value",$(".t_cell").attr("datetime"));
                            //            $("#form_record").attr("value","");
                            //            $("#name").attr("value","");
                            //            $("#parent_name_custno_c").css('display','inline');
                            //            $("#lead_account_name").css('display','none');
                            //            $("#repeat_type").removeAttr("disabled");
                            //            $("#opp_table").empty();
                            //
                            //            var lead_account_name = '';
                            //            var account_custno = '';
                            //
                            //            // add new record for shared functionality
                            //            $("#cal2_assigned_user_name").val($(this).attr("shared_user_name"));
                            //            $("#cal2_assigned_user_id").val($(this).attr("shared_user_id"));
                            //
                            //
                            //            $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(1)").removeAttr("disabled");
                            //            $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(6)").attr("disabled","disabled");
                            //            $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(5)").removeAttr("disabled");
                            //            $("#previous_outcome_span").css('display','none');
                            //            $("#previous_outcome_c").css('display','none');
                            //            $("#next_date_span").css('display','none');
                            //            $("#next_date").css('display','none');
                            //            $("#next_description_span").css('display','none');
                            //            $("#next_description").css('display','none');
                            //            $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(7)").attr("disabled","disabled");
                            //            $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(8)").attr("disabled","disabled");
                            //            $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(4)").attr("disabled","disabled");
                            //            $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(3)").attr("disabled","disabled");
                            //            $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(2)").attr("disabled","disabled");
                            //                                                        
                            //                                                        
                            //                                                        
                            //														
                            //           var combo_date_start = new Datetimecombo($(this).attr("datetime"), "date_start_qi", "<?php echo $timedate->get_user_time_format(); ?>", 102, '', ''); 
                            //           text = combo_date_start.html('SugarWidgetScheduler.update_time();');
                            //           document.getElementById('date_start_time_section_qi').innerHTML = text;
                            //eval(combo_date_start.jsscript('SugarWidgetScheduler.update_time();'));
                            //combo_date_start.update(); 
							
                            //SugarWidgetScheduler.update_time();
                            //}
                            return false;
                        }
                    );




                        //dialog quick customer input
                        $("#record_dialog_quick_input").dialog(
                        {
                            dialogClass: 'record_dialog_class_qi',
                            bgiframe: false,
                            autoOpen: false,
                            //height: 551,
                            //height: 500,
                            width: "auto",
                            modal: true,
                            buttons: {
                                'Load Previous Customer List' : function () {

                                    window.records_imported = new Array();
                                    if (typeof(window.previmported) !== 'undefined' && window.previmported.length > 0) {//has in previous imported

                                        for (var key in window.previmported) {//loop prev imported
	     			
                                            if (window.previmported[key].type == "call") {
                                                var record_module = "Calls";
                                            } else {
                                                var record_module = "Meetings";
                                            }
                                            //var url_loadajax = "index.php?module=Calendar2&action=AjaxLoad&sugar_body_only=true&cur_module=" + record_module + "&record=" + window.previmported[key].parent_id;


                                            jQuery("form#EditView-QI :input").not(".button").each(function() {

                                                var tag_name = "";

                                                var suffis_pos = "";

                                                var original_id = "";

                                                tag_name = jQuery(this).attr("name");

                                                suffis_pos = tag_name.indexOf("_qi");

                                                original_id = tag_name.substr(0, suffis_pos);
                                                //alert(original_id);

                                                switch(original_id) {
                                                    case "return_module":

                                                        jQuery(this).val(record_module);

                                                        break;

                                                    case "cur_module":
                                                        jQuery(this).val(record_module);
                                                        break;

                                                    case "form_record":
                                                        jQuery(this).val(window.previmported[key].record);
								
                                                        break;

                                                    case "clicked_record":
                                                        jQuery(this).val(window.previmported[key].record);
                                                        break;

                                                    case "appttype":
							 
                                                        jQuery("#radio_" + window.previmported[key].type + "_qi").attr("checked", "checked");
                                                        jQuery(this).attr("disabled", "disabled");
                                                        break;

                                                    case "cal2_assigned_user_name":
                                                        jQuery(this).val(window.previmported[key].user_name);
                                                        break;

                                                    case "cal2_assigned_user_id":
                                                        jQuery(this).val(window.previmported[key].assigned_user_id);
                                                        break;

                                                    case "cal2_category_c":
                                                        jQuery(this).val(window.previmported[key].assigned_user_id);
                                                        break;

                                                    case "return_module_qi":
                                                        break;

                                                    default:
				    
                                                }
					


                                                if(window.previmported[key].hasOwnProperty (original_id)) {
                                                    if (window.previmported[key].getPropertyValue) {
                                                        jQuery(this).val(window.previmported[key].getPropertyValue (original_id));
                                                    } else {
                                                        jQuery(this).val($(window.previmported[key]).attr(original_id));
                                                    }
                                                }

                                                var record_start_date = window.previmported[0].date_start.match(/^(\d{1,2}\/\d{1,2}\/\d{4})/);
                                                jQuery("#date_start_date_qi").val(record_start_date[0]);
	    

                                            });
				
                                            var popup_type = $('div.record_dialog_class_qi').attr("popup_type");

                                            if(popup_type == "accounts") {
                                                var row_data = new Array();

                                                var url_get_customer = "index.php?module=Accounts&action=getCustomerById&customer_id=" + window.previmported[key].parent_id + "&status=" + window.previmported[key].status + "&direction=" + window.previmported[key].direction+ "&type=" + window.previmported[key].type + "&record=" + window.previmported[key].record;
                                                var customer_data4table = '';	
						
                                                $.ajax({url : url_get_customer, 
                                                    keyRecordv: key,
                                                    //asinc: false,
                                                    success : function (data) {
                                                        //console.log(data);
                                                        var keyorder = this.keyRecordv;
                                                        data = data.dataresult;
                                                        row_data = ['<input type="checkbox" id="' + window.previmported[keyorder].parent_id + '" parent_name="' + window.previmported[keyorder].customer_name + '" record_id="' + window.previmported[keyorder].record + '" cust_no="' + window.previmported[keyorder].custno_c + '">',
                                                            data[0],
                                                            data[1],
                                                            data[2],
                                                            data[3],
                                                            data[4],
                                                            data[5],
                                                            data[6],
                                                            data[7],
                                                            data[8],
                                                            data[9],
                                                            data[10],
                                                            data[11],
                                                            data[12],
                                                            data[13]];
//                                                            '<textarea name="pc-plan" class="pc-plan" cols="32" account_id_pcplan="' + window.previmported[keyorder].parent_id + '">' + window.previmported[keyorder].description + '</textarea>',
//                                                            '<textarea name="outcome" class="outcome" cols="32" account_id_outcome="' + window.previmported[keyorder].parent_id + '">' + window.previmported[keyorder].outcome_c + '</textarea>'];	
                                                        //console.log(row_data);						
                                                        window.records_imported.push(row_data);

                                                    }, 
                                                    dataType : "json"
                                                });
				
                                                //console.log(row_data);
                                            } else if (popup_type == "leads") {


                                                var url_get_customer = "index.php?module=Leads&action=getCustomerById&customer_id=" + window.previmported[key].parent_id + "&status=" + window.previmported[key].status + "&direction=" + window.previmported[key].direction+ "&type=" + window.previmported[key].type + "&record=" + window.previmported[key].record;
                                                var customer_data4table = '';
                                                var row_data = new Array();
                                                $.ajax({url : url_get_customer, keyRecordv: key, success : function (data) {
	
                                                        var key = this.keyRecordv;
                                                        data = data.dataresult;

                                                        row_data = ['<input type="checkbox" id="' + window.previmported[key].parent_id + '" parent_name="' + window.previmported[key].customer_name + '" record_id="' + window.previmported[key].record + '" cust_no="' + window.previmported[key].custno_c + '">',
                                                            data[1],
                                                            data[2],
                                                            data[3],
                                                            data[4],
                                                            data[5],
                                                            data[6],
                                                            data[7],
                                                            data[8],
                                                            data[9],
                                                            //data[9],
                                                            data[10],
                                                            data[11]];
                                                            //'<textarea name="pc-plan" class="pc-plan" cols="32" account_id_pcplan="' + window.previmported[key].parent_id + '">' + window.previmported[key].description + '</textarea>',
                                                            //'<textarea name="outcome" class="outcome" cols="32" account_id_outcome="' + window.previmported[key].parent_id + '">' + window.previmported[key].outcome_c + '</textarea>'];	
                                                        window.records_imported.push(row_data);

                                                    }, 
                                                    dataType : "json"
                                                });
                                            }


                                            if($('div.record_dialog_class_qi').attr("popup_type") == "accounts") {
                                                $('#customers-list-to-calendar').html('<table id="customers_list" style="width: 100%"><thead><th style="width: 30px;">Include</th><th>CustNo</th><th style="width: 300px;">CustName</th><th style="width: 30px;">Address</th><th style="width: 30px;">City</th><th style="width: 30px;">State</th><th style="width: 30px;">PostalCode</th><th>Contact</th><th>Phone</th><th  style="width: 30px;">MTD Sales</th><th  style="width: 30px;">MTD Proj vs. Budget</th><th  style="width: 30px;">YTD Proj vs. Budget</th><th class="status-table-header" style="width: 30px;">Status</th><th class="pre-call-plan-table-header" style="width: 300px;">Pre-Call Plan</th><th class="outcome-table-header" style="width: 300px;">Outcome</th></thead><tbody><tr><td colspan="11" class="dataTables_empty">Loading data from server</td></tr></tbody></table>');	
                                                $("input#save_tocalendar_button").unbind("click");
                                                $("input#save_tocalendar_button").bind("click", function () {
                                                    CustomerListupdateRecords("accounts"); 
                                                    return false;		
                                                });
                                            } else if ($('div.record_dialog_class_qi').attr("popup_type") == "leads") {
                                                $('#customers-list-to-calendar').html('<?php print $table_leads; ?>');
                                                $("input#save_tocalendar_button").unbind("click");
                                                $("input#save_tocalendar_button").bind("click", function () {
                                                    CustomerListupdateRecords("leads"); 
                                                    return false;		
                                                });
                                            }
                                            //console.log(window.records_imported);
                                            window.setTimeout(function () {
                                                $("#customers_list").dataTable({
                                                    "bJQueryUI": true,
                                                    "bDestroy": true,
                                                    "aaData": window.records_imported,
                                                    "bProcessing": true,
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
                                                            "fnDrawCallback": function(oSettings, json) {
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
                                                selected_customers.push(this); 
                                            }else {
                                                $(row).removeClass("print-row");
                                                if(selected_customers.length > 0) {
                                                    $.each(selected_customers, function (k, v) {
                                                          if(typeof v.id != "undefined"){
                                                                if(v.id == checkboxelem.attr("id")) {
                                                                    delete selected_customers[k];
                                                                }
                                                          }
                                                    });
                                                }
                                            }
                                        });
		$("select.statuses_direction").not("select#direction_qi").val($("select#direction_qi").val());
                                    $("select.statuses_status").not("select#status_qi").val($("select#status_qi").val());
                                   
                                    $("textarea.pc-plan[textarea-custom!='1']").not("#autopopulate-pc-plan").each(function (k, v) {
                                        $(this).val($("#autopopulate-pc-plan").val() + '\n');	
                                    });
                                    $("textarea.pc-plan[textarea-custom!='1']").not("#autopopulate-pc-plan").attr('autopopulated', this.value);
                                    
                                     $("textarea.outcome[textarea-custom!='1']").not("#autopopulate-outcome").each(function (k, v) {
                                        $(this).val($("#autopopulate-outcome").val() + '\n');	
                                    });
                                    $("textarea.outcome[textarea-custom!='1']").not("#autopopulate-outcome").attr('autopopulated', this.value);
                                },
                                                    //"bServerSide": true,
                                                    //"sAjaxSource": url,
                                                    //"sPaginationType": "full_numbers",
                                                    "sDom": 'C<"clear"><"H"lr<"#qi-form-inputs"><"#autofill_pcp_outcome"> >t<"F"ip>'
                                                });
                                            }, 3000);


                                        }

                                    }


			
                                    return false;
				
                                }		
                            }              			
                        });	

                        $("#record_dialog").dialog(
                        {
                            dialogClass: 'record_dialog_class',
                            bgiframe: false,
                            autoOpen: false,
                            //height: 551,
                            height: 680,
                            width: 800,
                            modal: true,
                    			
                            buttons: {
                                '<?php echo $current_module_strings['LBL_SAVE_BUTTON']; ?>': function() { //start save button function
						
                                    //checks if all of recurred schedules are going to be removed
                                                       
                                    var edit_all_recurrence = false;
                                    edit_all_recurrence = $("#edit_all_recurrence").val();
                                    var lead_account_name = ($("#lead_account_name").val() =='undefined')? ' ': $("#lead_account_name").val()+ ' ';
                                    var account_custno = ($("#parent_name_custno_c").val()=='undefined')? ' ': ' ('+$("#parent_name_custno_c").val()+')';
                                    if ( $("#name").val() == '' && $("#parent_type").val() == 'Accounts' && $("#parent_id").val() != '') { $("#name").val($("#parent_name").val()+account_custno);}
                                    if ( $("#name").val() == '' &&  $("#parent_type").val() == 'Leads' && $("#parent_id").val() != '' ) { $("#name").val(lead_account_name+'('+$("#parent_name").val()+')');}
                                   $('#ui-dialog-title-record_dialog').html(lbl_wait_please);
                                        $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(1)").attr("disabled","disabled");
                                        $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(6)").attr("disabled","disabled");
                                        $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(5)").attr("disabled","disabled");
                                        $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(7)").attr("disabled","disabled");
                                        $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(8)").attr("disabled","disabled");
                                        $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(4)").attr("disabled","disabled");
                                        $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(3)").attr("disabled","disabled");
                                        $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(2)").attr("disabled","disabled");                     
                                    if (edit_all_recurrence) {                                         //start edit_all_recurrence = true
                                        //alert('we are in edit_all_recurrence = true');
                                        
                                        $("#next_description").val('');
                                        deleted_id = $("#form_record").val();
                                        deleted_module = $("#cur_module").val();
                                        var delete_recurring = false;
                                        var delete_first_recurring = false;
                                        $.post(
                                        "index.php?module=Calendar2&action=AjaxRemove&sugar_body_only=true",
                                        {        //start parametrs for request AjaxRemove
                                            "cur_module" : deleted_module ,
                                            "record" : deleted_id,
                                            "delete_recurring": delete_recurring,
                                            "delete_first_recurring": delete_first_recurring,
                                            "edit_all_recurrence": edit_all_recurrence
                                        }, //end parametrs for request AjaxRemove
                                        function(){  //start responce function for  request AjaxRemove
                                            //alert('Responce to delete');
                                            var cell_id = $("#" + deleted_id).parent().attr("id");
                                            if(pview == 'shared')	
                                                removeSharedById(deleted_id);
                                            $("#" + deleted_id).remove();
                                            align_divs(cell_id);
										
                                            ids = new Array();
                                            $.each(
                                            $("div[cal2_recur_id_c='" + deleted_id + "']"),
                                            function (i,v){
                                                ids[i] = $(v).parent().attr('id');
                                            }
                                        );				
                                            $("div[cal2_recur_id_c='" + deleted_id + "']").remove();
                                            $.each(
                                            ids,
                                            function (i,v){
                                                align_divs(ids[i]);
                                            }
                                        );
                                            if(!(check_form('EditView') && isValidDuration()))
                                                return false;
                                            fill_invitees2();
                                            fill_reccurence();
                                            $("#EditView").ajaxSubmit( //start reaction for button save pressed for edit_all_recurrence = true
                                            {
                                                url: "index.php?module=Calendar2&action=AjaxSave"
                                                    + "&sugar_body_only=true&currentmodule=" + current_module
                                                    + "&view=" + cal2_view,
                                                dataType: "json",
                                                success:	function(res){
                                                    if(res.succuss == 'yes'){
                                                        AddRecords(res);
                                                        $.each(
                                                        res.users,
                                                        function (i,v){
                                                            //updates the current user's scheduler row
                                                            urllib.getURL('./vcal_server_cal2.php?type=vfb&source=outlook&user_id='+v,[["Content-Type", "text/plain"]], function (result) {
                                                                if (typeof GLOBAL_REGISTRY.freebusy == 'undefined') {
                                                                    GLOBAL_REGISTRY.freebusy = new Object();
                                                                }
                                                                if (typeof GLOBAL_REGISTRY.freebusy_adjusted == 'undefined') {
                                                                    GLOBAL_REGISTRY.freebusy_adjusted = new Object();
                                                                }
                                                                // parse vCal and put it in the registry using the user_id as a key:
                                                                GLOBAL_REGISTRY.freebusy[v] = SugarVCalClient.parseResults(result.responseText, false);
                                                                // parse for current user adjusted vCal
                                                                GLOBAL_REGISTRY.freebusy_adjusted[v] = SugarVCalClient.parseResults(result.responseText, true);
                                                            })
                                                        }
                                                    );
                                                        $("#record_dialog").dialog('close');
                                                    }else
                                                        alert(lbl_error_saving);
                                                }
                                            }
                                        ); // end reaction for button save presed for case edit_all_recurrence = true
                                        }//end responce function for request AjaxRemove
                                    ); //end post for AjaxRemove
                                    }  else {//end edit_all_recurrence=true and start edit_all_recurrence=false
							
                                        //alert('we are in edit_all_recurrence = false');
                                        if(!(check_form('EditView') && isValidDuration()))
                                            return false;
                                        fill_invitees2();
                                        fill_reccurence();
                                        $("#EditView").ajaxSubmit( //start reaction for button save pressed for case edit_all_recurrence=false
                                        {
                                            url: "index.php?module=Calendar2&action=AjaxSave"  
                                                + "&sugar_body_only=true&currentmodule=" + current_module
                                                + "&view=" + cal2_view,
                                            dataType: "json",
                                            success:	function(res){
                                                if(res.succuss == 'yes'){
                                                    AddRecords(res);
                                                    $.each(
                                                    res.users,
                                                    function (i,v){
                                                        //updates the current user's scheduler row
                                                        urllib.getURL('./vcal_server_cal2.php?type=vfb&source=outlook&user_id='+v,[["Content-Type", "text/plain"]], function (result) { 
                                                            if (typeof GLOBAL_REGISTRY.freebusy == 'undefined') {
                                                                GLOBAL_REGISTRY.freebusy = new Object();
                                                            }
                                                            if (typeof GLOBAL_REGISTRY.freebusy_adjusted == 'undefined') {
                                                                GLOBAL_REGISTRY.freebusy_adjusted = new Object();
                                                            }
                                                            // parse vCal and put it in the registry using the user_id as a key:
                                                            GLOBAL_REGISTRY.freebusy[v] = SugarVCalClient.parseResults(result.responseText, false);                  
                                                            // parse for current user adjusted vCal
                                                            GLOBAL_REGISTRY.freebusy_adjusted[v] = SugarVCalClient.parseResults(result.responseText, true);
                                                        })
                                                    }
                                                );
                                                    $("#record_dialog").dialog('close');
                                                }else
                                                    alert(lbl_error_saving);
                                            }
                                        }
                                    ); // end reaction for button save presed for case edit_all_recurrence=false
                                    }; // end edit_all_recurrence=false
                                }, //end button save

                                '<?php echo 'Save and Create New Meeting or Call'; ?>': function(){
                                    var lead_account_name = ($("#lead_account_name").val() =='undefined')? ' ': $("#lead_account_name").val()+ ' ';
                                    var account_custno = ($("#parent_name_custno_c").val()=='undefined')? ' ': ' ('+$("#parent_name_custno_c").val()+')';
                                    if ( $("#name").val() == '' && $("#parent_type").val() == 'Accounts' && $("#parent_id").val() != '') { $("#name").val($("#parent_name").val()+account_custno);}
                                    if ( $("#name").val() == '' &&  $("#parent_type").val() == 'Leads' && $("#parent_id").val() != '' ) { $("#name").val(lead_account_name+'('+$("#parent_name").val()+')');}
                                    record_id = $("#form_record").val();
                                    cur_module = $("#cur_module").val();
                                    if(!(check_form('EditView') && isValidDuration()))
                                        return false;
                                    fill_invitees2();
                                    fill_reccurence();
                                    $("#EditView").ajaxSubmit( //start reaction for button save pressed for case edit_all_recurrence=false
                                    {
                                        url: "index.php?module=Calendar2&action=AjaxSave"
                                            + "&sugar_body_only=true&currentmodule=" + current_module
                                            + "&view=" + cal2_view,
                                        dataType: "json",
                                        success:	function(res){
                                            if(res.succuss == 'yes'){
                                                AddRecords(res);
                                                $.each(
                                                res.users,
                                                function (i,v){
                                                    //updates the current user's scheduler row
                                                    urllib.getURL('./vcal_server_cal2.php?type=vfb&source=outlook&user_id='+v,[["Content-Type", "text/plain"]], function (result) {
                                                        if (typeof GLOBAL_REGISTRY.freebusy == 'undefined') {
                                                            GLOBAL_REGISTRY.freebusy = new Object();
                                                        }
                                                        if (typeof GLOBAL_REGISTRY.freebusy_adjusted == 'undefined') {
                                                            GLOBAL_REGISTRY.freebusy_adjusted = new Object();
                                                        }
                                                        // parse vCal and put it in the registry using the user_id as a key:
                                                        GLOBAL_REGISTRY.freebusy[v] = SugarVCalClient.parseResults(result.responseText, false);
                                                        // parse for current user adjusted vCal
                                                        GLOBAL_REGISTRY.freebusy_adjusted[v] = SugarVCalClient.parseResults(result.responseText, true);
                                                    })
                                                }
                                            );
                                                $("#record_dialog").dialog('close');
                                                open_new_meeting_or_call(cur_module, record_id);
                                            }else
                                                alert(lbl_error_saving);
                                        }
                                    }
                                ); // end reaction for button save presed for case edit_all_recurrence=false
                                },

                                '<?php echo 'Save and Create an Opportunity'; ?>': function(){
                                    var lead_account_name = ($("#lead_account_name").val() =='undefined')? ' ': $("#lead_account_name").val()+ ' ';
                                    var account_custno = ($("#parent_name_custno_c").val()=='undefined')? ' ': ' ('+$("#parent_name_custno_c").val()+')';
                                    if ( $("#name").val() == '' && $("#parent_type").val() == 'Accounts' && $("#parent_id").val() != '') { $("#name").val($("#parent_name").val()+account_custno);}
                                    if ( $("#name").val() == '' &&  $("#parent_type").val() == 'Leads' && $("#parent_id").val() != '' ) { $("#name").val(lead_account_name+'('+$("#parent_name").val()+')');}
                                    record_id = $("#form_record").val();
                                    if(!(check_form('EditView') && isValidDuration()))
                                        return false;
                                    fill_invitees2();
                                    fill_reccurence();
                                    $("#EditView").ajaxSubmit( //start reaction for button save pressed for case edit_all_recurrence=false
                                    {
                                        url: "index.php?module=Calendar2&action=AjaxSave"
                                            + "&sugar_body_only=true&currentmodule=" + current_module
                                            + "&view=" + cal2_view,
                                        dataType: "json",
                                        success:	function(res){
                                            if(res.succuss == 'yes'){
                                                AddRecords(res);
                                                $.each(
                                                res.users,
                                                function (i,v){
                                                    //updates the current user's scheduler row
                                                    urllib.getURL('./vcal_server_cal2.php?type=vfb&source=outlook&user_id='+v,[["Content-Type", "text/plain"]], function (result) {
                                                        if (typeof GLOBAL_REGISTRY.freebusy == 'undefined') {
                                                            GLOBAL_REGISTRY.freebusy = new Object();
                                                        }
                                                        if (typeof GLOBAL_REGISTRY.freebusy_adjusted == 'undefined') {
                                                            GLOBAL_REGISTRY.freebusy_adjusted = new Object();
                                                        }
                                                        // parse vCal and put it in the registry using the user_id as a key:
                                                        GLOBAL_REGISTRY.freebusy[v] = SugarVCalClient.parseResults(result.responseText, false);
                                                        // parse for current user adjusted vCal
                                                        GLOBAL_REGISTRY.freebusy_adjusted[v] = SugarVCalClient.parseResults(result.responseText, true);
                                                    })
                                                }
                                            );
                                                $("#record_dialog").dialog('close');
                                                open_new_opportunity(600,400,true,record_id);
                                            }else
                                                alert(lbl_error_saving);
                                        }
                                    }
                                ); // end reaction for button save presed for case edit_all_recurrence=false
                                },

                                '<?php echo 'Save and Create a Task'; ?>': function(){
                                    var lead_account_name = ($("#lead_account_name").val() =='undefined')? ' ': $("#lead_account_name").val()+ ' ';
                                    var account_custno = ($("#parent_name_custno_c").val()=='undefined')? ' ': ' ('+$("#parent_name_custno_c").val()+')';
                                    if ( $("#name").val() == '' && $("#parent_type").val() == 'Accounts' && $("#parent_id").val() != '') { $("#name").val($("#parent_name").val()+account_custno);}
                                    if ( $("#name").val() == '' &&  $("#parent_type").val() == 'Leads' && $("#parent_id").val() != '' ) { $("#name").val(lead_account_name+'('+$("#parent_name").val()+')');}
                                    record_id = $("#form_record").val();
                                    if(!(check_form('EditView') && isValidDuration()))
                                        return false;
                                    fill_invitees2();
                                    fill_reccurence();
                                    $("#EditView").ajaxSubmit( //start reaction for button save pressed for case edit_all_recurrence=false
                                    {
                                        url: "index.php?module=Calendar2&action=AjaxSave"
                                            + "&sugar_body_only=true&currentmodule=" + current_module
                                            + "&view=" + cal2_view,
                                        dataType: "json",
                                        success:	function(res){
                                            if(res.succuss == 'yes'){
                                                AddRecords(res);
                                                $.each(
                                                res.users,
                                                function (i,v){
                                                    //updates the current user's scheduler row
                                                    urllib.getURL('./vcal_server_cal2.php?type=vfb&source=outlook&user_id='+v,[["Content-Type", "text/plain"]], function (result) {
                                                        if (typeof GLOBAL_REGISTRY.freebusy == 'undefined') {
                                                            GLOBAL_REGISTRY.freebusy = new Object();
                                                        }
                                                        if (typeof GLOBAL_REGISTRY.freebusy_adjusted == 'undefined') {
                                                            GLOBAL_REGISTRY.freebusy_adjusted = new Object();
                                                        }
                                                        // parse vCal and put it in the registry using the user_id as a key:
                                                        GLOBAL_REGISTRY.freebusy[v] = SugarVCalClient.parseResults(result.responseText, false);
                                                        // parse for current user adjusted vCal
                                                        GLOBAL_REGISTRY.freebusy_adjusted[v] = SugarVCalClient.parseResults(result.responseText, true);
                                                    })
                                                }
                                            );
                                                $("#record_dialog").dialog('close');
                                                open_new_task(600,400,true,record_id);

                                            }else
                                                alert(lbl_error_saving);
                                        }
                                    }
                                ); // end reaction for button save presed for case edit_all_recurrence=false
                                },

                                '<?php echo $current_module_strings['LBL_CANCEL_BUTTON']; ?>': function() {
                                    $(this).dialog('close');
                                },

                                '<?php echo $current_module_strings['LBL_DELETE_BUTTON']; ?>': function(){
                                    if($("#form_record").val() != "") {
                                        //checks if all of recurred schedules are going to be removed
                                        var edit_all_recurrence = false;
                                        edit_all_recurrence = $("#edit_all_recurrence").val();

                                        //checks if a recurred schedule is going to be removed
                                        var delete_recurring = false;
                                        if( $("#cal2_recur_id_c").val() != '')
                                            delete_recurring = true;

                                        //checks if the 1st recurred schedule is going to be removed
                                        var delete_first_recurring = false;
                                        if( $("#cal2_repeat_type_c").val() != '')
                                            delete_first_recurring = true;

                                        if (!edit_all_recurrence && delete_first_recurring) {
                                            alert(lbl_cannot_remove_first);
                                        } else if(confirm("<?php echo $current_module_strings['MSG_REMOVE_CONFIRM']; ?>")){
                                            $('#ui-dialog-title-record_dialog').html(lbl_wait_please);
                                            $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(1)").attr("disabled","disabled");
                                            $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(6)").attr("disabled","disabled");
                                            $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(5)").attr("disabled","disabled");
                                            $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(4)").attr("disabled","disabled");
                                            $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(3)").attr("disabled","disabled");
                                            $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(2)").attr("disabled","disabled");  
                                            deleted_id = $("#form_record").val();
                                            deleted_module = $("#cur_module").val();
                                            if(edit_all_recurrence){
                                                $.post(
                                                "index.php?module=Calendar2&action=AjaxRemove&sugar_body_only=true",
                                                {
                                                    "cur_module" : deleted_module ,
                                                    "record" : deleted_id,
                                                    "delete_recurring": delete_recurring,
                                                    "delete_first_recurring": delete_first_recurring,
                                                    "edit_all_recurrence": edit_all_recurrence
                                                },
                                                function(){
                                                    var cell_id = $("#" + deleted_id).parent().attr("id");
                                                    if(pview == 'shared')	
                                                        removeSharedById(deleted_id);												
                                                    $("#" + deleted_id).remove();
                                                    align_divs(cell_id);
												
                                                    ids = new Array();			
                                                    $.each(
                                                    $("div[cal2_recur_id_c='" + deleted_id + "']"),
                                                    function (i,v){
                                                        ids[i] = $(v).parent().attr('id'); 					
                                                    }
                                                );				
                                                    $("div[cal2_recur_id_c='" + deleted_id + "']").remove();				
                                                    $.each(
                                                    ids,
                                                    function (i,v){
                                                        align_divs(ids[i]);		
                                                    }				
                                                );
                                                    $("#record_dialog").dialog('close');
                                                }					
                                            );
                                            } else if (!delete_first_recurring) {
                                                $.post(
                                                "index.php?module=Calendar2&action=AjaxRemove&sugar_body_only=true",
                                                {
                                                    "cur_module" : deleted_module ,
                                                    "record" : deleted_id,
                                                    "delete_recurring": delete_recurring,
                                                    "delete_first_recurring": delete_first_recurring,
                                                    "edit_all_recurrence": edit_all_recurrence
                                                },
                                                function(){
                                                    var cell_id = $("#" + deleted_id).parent().attr("id");
                                                    if(pview == 'shared')	
                                                        removeSharedById(deleted_id);												
                                                    $("#" + deleted_id).remove();
                                                    align_divs(cell_id);
                                                    $("#record_dialog").dialog('close');
                                                }
                                            );
                                                
                                            }
                                        }
                                    }
                                },
				
                                '<?php echo 'End Recurrence'; ?>': function() {
                                    var lead_account_name = ($("#lead_account_name").val() =='undefined')? ' ': $("#lead_account_name").val()+ ' ';
                                    var account_custno = ($("#parent_name_custno_c").val()=='undefined')? ' ': ' ('+$("#parent_name_custno_c").val()+')';
                                    if ( $("#name").val() == '' && $("#parent_type").val() == 'Accounts' && $("#parent_id").val() != '') { $("#name").val($("#parent_name").val()+account_custno);}
                                    if ( $("#name").val() == '' &&  $("#parent_type").val() == 'Leads' && $("#parent_id").val() != '' ) { $("#name").val(lead_account_name+'('+$("#parent_name").val()+')');}
                                    $('#ui-dialog-title-record_dialog').html(lbl_wait_please);
                                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(1)").attr("disabled","disabled");
                                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(6)").attr("disabled","disabled");
                                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(5)").attr("disabled","disabled");
                                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(7)").attr("disabled","disabled");
                                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(8)").attr("disabled","disabled");
                                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(4)").attr("disabled","disabled");
                                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(3)").attr("disabled","disabled");
                                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(2)").attr("disabled","disabled");

                                    if(!(check_form('EditView') && isValidDuration()))
                                        return false;
                                    fill_invitees2();
                                    fill_reccurence();
                                    $("#EditView").ajaxSubmit( //start reaction for button split
                                    {
                                        url: "index.php?module=Calendar2&action=AjaxEndReccurence"
                                            + "&sugar_body_only=true&currentmodule=" + current_module
                                            + "&view=" + cal2_view,
                                        dataType: "json",
                                        success:	function(res){
                                            if(res.succuss == 'yes'){

//                                                var cell_id = $("#" + deleted_id).parent().attr("id");
//                                                if(pview == 'shared')
//                                                    removeSharedById(deleted_id);
//                                                $("#" + deleted_id).remove();
//                                                align_divs(cell_id);
//
//                                                ids = new Array();
//                                                $.each(
//                                                $("div[cal2_recur_id_c='" + deleted_id + "']"),
//                                                function (i,v){
//                                                    ids[i] = $(v).parent().attr('id');
//                                                }
//                                            );
//                                                $("div[cal2_recur_id_c='" + deleted_id + "']").remove();
                                                $.each(res.deleted_ids,function (i,v){
                                                    if (v != '')
                                                        $("div[record='" + v + "']").remove();
                                                });


                                                //alert("responce after split");
                                                //AddSplitRecords(res);
                                                $.each(
                                                res.users,
                                                function (i,v){
                                                    //updates the current user's scheduler row
                                                    if (v != '') { urllib.getURL('./vcal_server_cal2.php?type=vfb&source=outlook&user_id='+v,[["Content-Type", "text/plain"]], function (result) {
                                                            if (typeof GLOBAL_REGISTRY.freebusy == 'undefined') {
                                                                GLOBAL_REGISTRY.freebusy = new Object();
                                                            }
                                                            if (typeof GLOBAL_REGISTRY.freebusy_adjusted == 'undefined') {
                                                                GLOBAL_REGISTRY.freebusy_adjusted = new Object();
                                                            }
                                                            // parse vCal and put it in the registry using the user_id as a key:
                                                            GLOBAL_REGISTRY.freebusy[v] = SugarVCalClient.parseResults(result.responseText, false);
                                                            // parse for current user adjusted vCal
                                                            GLOBAL_REGISTRY.freebusy_adjusted[v] = SugarVCalClient.parseResults(result.responseText, true);
                                                        })
                                                    }
                                                }
                                            );
                                                $("#record_dialog").dialog('close');
                                            }else
                                                alert(lbl_error_saving);
                                        }
                                    }
                                ); // end reaction for button save presed for case edit_all_recurrence=false




                                },
                                   '<?php echo 'Extend Recurrence'; ?>': function() {
                                    var flag = isEditedForm();
                                    if(flag){
                                        alert("This button will not work since you accidentally changed more than the end date field.");
                                     
                                        if(sessionStorage.length != 0) {
                                                var prev = eval('(' + sessionStorage.getItem("formValues") + ')');
                                               $("div#record_tabs-1 :input,div#record_tabs-1 select option:selected, div#record_tabs-3 :input,div#record_tabs-3 select option:selected").each( function (k, v) {
                                                        if(typeof v.id != 'undefined' && v.id != ''){
                                                           if($(v).attr('type') == 'checkbox' && prev.jsonObject[v.id+"___"+k]){
                                                                $(v).attr("checked","checked")
                                                            }else if($(v).attr('type') == 'checkbox'){
                                                                $(v).removeAttr("checked");
                                                            }else{
                                                                v.value = prev.jsonObject[v.id+"___"+k];
                                                            }
                                                        }else if(typeof v.name != 'undefined' && v.name != ''){
                                                            if($(v).attr('type') == 'checkbox' && prev.jsonObject[v.name+"___"+k]){
                                                                $(v).attr("checked","checked")
                                                            }else if($(v).attr('type') == 'checkbox'){
                                                                $(v).removeAttr("checked");
                                                            }else{
                                                                v.value = prev.jsonObject[v.name+"___"+k];
                                                            }
                                                        }else if(typeof v.parentNode.id != 'undefined' && v.parentNode.id != '' ){
                                                            if(typeof prev.jsonObject[v.parentNode.id+"___"+k] != 'undefined'){
                                                                $(v).parent().find("option[value='"+prev.jsonObject[v.parentNode.id+"___"+k]+"']").attr("selected","selected");
                                                            }
                                                        }else if(typeof v.parentNode.name != 'undefined' && v.parentNode.name != ''){
                                                            if(typeof prev.jsonObject[v.parentNode.name+"___"+k] != 'undefined'){
                                                               $(v).parent().find("option[value='"+prev.jsonObject[v.parentNode.name+"___"+k]+"']").attr("selected","selected");
                                                            }
                                                        }
                                                });
                                        }
                                    }else{
                                            var isExtend = true; 
                                            if(sessionStorage.length != 0) {
                                                var prev = eval('(' + sessionStorage.getItem("formValues") + ')');
                                                $.each(prev.jsonObject, function (k, v) {
                                                    if((k.indexOf('repeat_end_date___') + 1)){
                                                        var currDate = new Date($("#repeat_end_date").val()).valueOf();
                                                        var oldDate = new Date(prev.jsonObject[k]).valueOf();
                                                        if(currDate <= oldDate){
                                                            $("#repeat_end_date").val(prev.jsonObject[k]);
                                                            isExtend = false;
                                                        }
                                                        return false;
                                                    }
                                                });
                                            }
                                            if(isExtend){
                                                var lead_account_name = ($("#lead_account_name").val() =='undefined')? ' ': $("#lead_account_name").val()+ ' ';
                                                var account_custno = ($("#parent_name_custno_c").val()=='undefined')? ' ': ' ('+$("#parent_name_custno_c").val()+')';
                                                if ( $("#name").val() == '' && $("#parent_type").val() == 'Accounts' && $("#parent_id").val() != '') { $("#name").val($("#parent_name").val()+account_custno);}
                                                if ( $("#name").val() == '' &&  $("#parent_type").val() == 'Leads' && $("#parent_id").val() != '' ) { $("#name").val(lead_account_name+'('+$("#parent_name").val()+')');}
                                                $('#ui-dialog-title-record_dialog').html(lbl_wait_please);
                                                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(1)").attr("disabled","disabled");
                                                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(6)").attr("disabled","disabled");
                                                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(5)").attr("disabled","disabled");
                                                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(7)").attr("disabled","disabled");
                                                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(8)").attr("disabled","disabled");
                                                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(4)").attr("disabled","disabled");
                                                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(3)").attr("disabled","disabled");
                                                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(2)").attr("disabled","disabled");

                                                if(!(check_form('EditView') && isValidDuration()))
                                                    return false;
                                                fill_invitees2();
                                                fill_reccurence();
                                                $("#EditView").ajaxSubmit( //start reaction for button save pressed for case edit_all_recurrence=false
                                                    {
                                                        url: "index.php?module=Calendar2&action=AjaxExtendReccurence"
                                                            + "&sugar_body_only=true&currentmodule=" + current_module
                                                            + "&view=" + cal2_view + "&split_date=",
                                                        dataType: "json",
                                                        success:	function(res){
                                                            if(res.succuss == 'yes'){
                                                              
                                                                AddRecords(res);
                                                                $.each(
                                                                res.users,
                                                                function (i,v){
                                                                    //updates the current user's scheduler row
                                                                    urllib.getURL('./vcal_server_cal2.php?type=vfb&source=outlook&user_id='+v,[["Content-Type", "text/plain"]], function (result) {
                                                                        if (typeof GLOBAL_REGISTRY.freebusy == 'undefined') {
                                                                            GLOBAL_REGISTRY.freebusy = new Object();
                                                                        }
                                                                        if (typeof GLOBAL_REGISTRY.freebusy_adjusted == 'undefined') {
                                                                            GLOBAL_REGISTRY.freebusy_adjusted = new Object();
                                                                        }
                                                                        // parse vCal and put it in the registry using the user_id as a key:
                                                                        GLOBAL_REGISTRY.freebusy[v] = SugarVCalClient.parseResults(result.responseText, false);
                                                                        // parse for current user adjusted vCal
                                                                        GLOBAL_REGISTRY.freebusy_adjusted[v] = SugarVCalClient.parseResults(result.responseText, true);
                                                                    })
                                                                }
                                                            );
                                                                $("#record_dialog").dialog('close');
                                                            }else
                                                                alert(lbl_error_saving);
                                                        }
                                                    }
                                                ); // end reaction for button save presed for case edit_all_recurrence=false
                                            }
                                    }
                                   
                                   }
//                                '<?php echo 'End Recurrence and Create New Recurrenc'; ?>': function() {
//                                    var lead_account_name = ($("#lead_account_name").val() =='undefined')? ' ': $("#lead_account_name").val()+ ' ';
//                                    var account_custno = ($("#parent_name_custno_c").val()=='undefined')? ' ': ' ('+$("#parent_name_custno_c").val()+')';
//                                    if ( $("#name").val() == '' && $("#parent_type").val() == 'Accounts' && $("#parent_id").val() != '') { $("#name").val($("#parent_name").val()+account_custno);}
//                                    if ( $("#name").val() == '' &&  $("#parent_type").val() == 'Leads' && $("#parent_id").val() != '' ) { $("#name").val(lead_account_name+'('+$("#parent_name").val()+')');}
//                                    $('#ui-dialog-title-record_dialog').html(lbl_wait_please);
//                                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(1)").attr("disabled","disabled");
//                                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(6)").attr("disabled","disabled");
//                                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(5)").attr("disabled","disabled");
//                                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(7)").attr("disabled","disabled");
//                                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(8)").attr("disabled","disabled");
//                                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(4)").attr("disabled","disabled");
//                                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(3)").attr("disabled","disabled");
//                                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(2)").attr("disabled","disabled");
//                                    deleted_id = $("#form_record").val();
//                                    //var clicked_record = '';
//                                    clicked_record = $('#clicked_record').val();
//                                    //alert('clicked_record: '+clicked_record);
//
//                                    if(!(check_form('EditView') && isValidDuration()))
//                                        return false;
//                                    fill_invitees2();
//                                    fill_reccurence();
//                                    $("#EditView").ajaxSubmit( //start reaction for button split
//                                    {
//                                        url: "index.php?module=Calendar2&action=AjaxSplit"
//                                            + "&sugar_body_only=true&currentmodule=" + current_module
//                                            + "&view=" + cal2_view,
//                                        dataType: "json",
//                                        success:	function(res){
//                                            if(res.succuss == 'yes'){
//
//                                                var cell_id = $("#" + deleted_id).parent().attr("id");
//                                                if(pview == 'shared')
//                                                    removeSharedById(deleted_id);
//                                                $("#" + deleted_id).remove();
//                                                align_divs(cell_id);
//
//                                                ids = new Array();
//                                                $.each(
//                                                $("div[cal2_recur_id_c='" + deleted_id + "']"),
//                                                function (i,v){
//                                                    ids[i] = $(v).parent().attr('id');
//                                                }
//                                            );
//                                                $("div[cal2_recur_id_c='" + deleted_id + "']").remove();
//                                                $.each(
//                                                ids,
//                                                function (i,v){
//                                                    align_divs(ids[i]);
//                                                }
//                                            );
//
//
//                                                //alert("responce after split");
//                                                AddSplitRecords(res);
//                                                $.each(
//                                                res.users,
//                                                function (i,v){
//                                                    //updates the current user's scheduler row
//                                                    if (v != '') {
//                                                        urllib.getURL('./vcal_server_cal2.php?type=vfb&source=outlook&user_id='+v,[["Content-Type", "text/plain"]], function (result) {
//                                                            if (typeof GLOBAL_REGISTRY.freebusy == 'undefined') {
//                                                                GLOBAL_REGISTRY.freebusy = new Object();
//                                                            }
//                                                            if (typeof GLOBAL_REGISTRY.freebusy_adjusted == 'undefined') {
//                                                                GLOBAL_REGISTRY.freebusy_adjusted = new Object();
//                                                            }
//                                                            // parse vCal and put it in the registry using the user_id as a key:
//                                                            GLOBAL_REGISTRY.freebusy[v] = SugarVCalClient.parseResults(result.responseText, false);
//                                                            // parse for current user adjusted vCal
//                                                            GLOBAL_REGISTRY.freebusy_adjusted[v] = SugarVCalClient.parseResults(result.responseText, true);
//                                                        })
//                                                    }
//                                                }
//                                            );
//
//
//
//                                                $("#EditView").ajaxSubmit( //start reaction for button save pressed for case edit_all_recurrence=false
//                                                {
//                                                    url: "index.php?module=Calendar2&action=AjaxSplitSave"
//                                                        + "&sugar_body_only=true&currentmodule=" + current_module
//                                                        + "&view=" + cal2_view + "&split_date=" + res.split_date,
//                                                    dataType: "json",
//                                                    success:	function(res){
//                                                        if(res.succuss == 'yes'){
//                                                            AddRecords(res);
//                                                            $.each(
//                                                            res.users,
//                                                            function (i,v){
//                                                                //updates the current user's scheduler row
//                                                                urllib.getURL('./vcal_server_cal2.php?type=vfb&source=outlook&user_id='+v,[["Content-Type", "text/plain"]], function (result) {
//                                                                    if (typeof GLOBAL_REGISTRY.freebusy == 'undefined') {
//                                                                        GLOBAL_REGISTRY.freebusy = new Object();
//                                                                    }
//                                                                    if (typeof GLOBAL_REGISTRY.freebusy_adjusted == 'undefined') {
//                                                                        GLOBAL_REGISTRY.freebusy_adjusted = new Object();
//                                                                    }
//                                                                    // parse vCal and put it in the registry using the user_id as a key:
//                                                                    GLOBAL_REGISTRY.freebusy[v] = SugarVCalClient.parseResults(result.responseText, false);
//                                                                    // parse for current user adjusted vCal
//                                                                    GLOBAL_REGISTRY.freebusy_adjusted[v] = SugarVCalClient.parseResults(result.responseText, true);
//                                                                })
//                                                            }
//                                                        );
//                                                            $("#record_dialog").dialog('close');
//                                                        }else
//                                                            alert(lbl_error_saving);
//                                                    }
//                                                }
//                                            ); // end reaction for button save presed for case edit_all_recurrence=false
//                                            }else
//                                                alert(lbl_error_saving);
//                                        }
//                                    }
//                                ); // end reaction for button save presed for case edit_all_recurrence=false
//
//                                }
                            },
                            close: function() {					
                                clearFields_cal2();						
                            }

                        }
                    );		
		
		
                        $("#record_tabs").tabs();
                        $("#record_quickinput_tabs").tabs();
		
                        var ActRecords = [
<?php
$ft = true;
foreach ($ActRecords as $act) {
    if (!$ft) {
        echo ",";
    }

    echo "{";
    echo '
                                        "type" : "' . $act["type"] . '", 
                                        "record" : "' . $act["id"] . '",
                                        "start" : "' . $act["start"] . '",
                                        "date_start" : "' . $act["date_start"] . '",
                                        "time_start" : "' . $act["time_start"] . '",
                                        "duration_hours" : ' . $act["duration_hours"] . ',
                                        "duration_minutes" : ' . $act["duration_minutes"] . ',
                                        "user_id" : "' . $act["user_id"] . '",
                                        "record_name": "' . $act["name"] . '",
                                        "location": "' . $act["location"] . '",
                                        "cal2_recur_id_c": "' . $act["cal2_recur_id_c"] . '",
                                        "cal2_category_c": "' . $act["cal2_category_c"] . '",
                                        "description" : "' . $act["description"] . '",
                                        "detailview" : "' . $act["detailview"] . '",
                                        "custno_c" : "' . $act["custno_c"] . '",
                                        "customer_name" : "' . $act["customer_name"] . '",
                                        "parent_type" : "' . $act["parent_type"] . '",
                                        "parent_id" : "' . $act["parent_id"] . '",
                                        "widget_title" : "' . $act["widget_title"] . '"
                                ';
    echo "}";
    $ft = false;
}
?>		
        ];

        $("#settings_dialog").dialog(
        {
            dialogClass: 'settings_dialog_class',
            bgiframe: false,
            autoOpen: false,
            //height: 200,
            height: 245,
            width: 380,
            //width: 600,
            modal: true,
					
            buttons: {
                'Apply': 	function(){
                    $("#form_settings").submit();
						
                },
                Cancel: function(){
                    $(this).dialog('close');
                }										
            },
            close: function(){
                $("#form_settings").resetForm();
            } 
        }
    );
		
        repeat_type_selected();
				
        for ( var i in ActRecords ){
            AddRecordToPage(ActRecords[i]);
        };
		
        $(".day_head[date='"+today_string+"']").addClass("today");

        $("#recurrence_dialog").dialog({
            dialogClass: 'recurrence_dialog_class',
            bgiframe: false,
            autoOpen: false,
            height: window.innerHeight- 10,
            width: window.innerWidth- 20,
            modal: true,
					
            buttons: {
                'Save': 	function(){
                    if(sessionStorage.length != 0) {
                        if(sessionStorage.getItem("endReccurence") != null ) {
                            $('#ui-dialog-title-recurrence_dialog').html(lbl_wait_please);
                            $(".recurrence_dialog .ui-dialog-buttonpane button").attr("disabled","disabled");
                            var end_reccurence_obj = eval('(' + sessionStorage.getItem("endReccurence") + ')');
                            $.post("index.php?module=Calendar2&action=AjaxEndReccurence&sugar_body_only=true",
                                        {"jsonObj" : jsObj2phpObj(end_reccurence_obj.jsonObject)}, 
                                        function(data){
                                           // $('#ui-dialog-title-recurrence_dialog').html("Recurrence List");
                                            if(sessionStorage.getItem("extendReccurence") == null) document.location.reload(true);
                                        });
                        }
                        if(sessionStorage.getItem("extendReccurence") != null) {
                            $('#ui-dialog-title-recurrence_dialog').html(lbl_wait_please);
                            $(".recurrence_dialog .ui-dialog-buttonpane button").attr("disabled","disabled");
                            var extend_reccurence_obj  = eval('(' + sessionStorage.getItem("extendReccurence") + ')');
                           $.post("index.php?module=Calendar2&action=AjaxExtendReccurence&sugar_body_only=true",
                                        {"jsonObj" : jsObj2phpObj(extend_reccurence_obj.jsonObject)}, 
                                        function(data){
                                           // $('#ui-dialog-title-recurrence_dialog').html("Recurrence List");
                                           document.location.reload(true);
                                        });
                        }
                    }					
                },
                Cancel: function(){
                    $(this).dialog('close');
                }										
            },
            close: function(){
                $("#form_recurrenceList").resetForm();
            } 
        });
    });
    
    function jsObj2phpObj(obj){
        var json = "{";
        for(property in obj){
            var value = obj[property];
            if(typeof(value) == "string" ) {
                json += '"'+property+'":"'+value+'",'; 
            }else{
                if(!value[0]){
                    json += '"'+property+'":'+jsObj2phpObj(value)+','; 
                }else{
                    json += '"'+property+'":[';
                    for(prop in value) json += '"'+value[prop]+'",';
                    json = json.substr(0,json.lenght-1) + '],';
                }
            }
        }
        return json.substr(0,json.length-1) + '}';
    }
</script>






<div id="record_dialog" title="Record dialog" style='display: none;'>
    <div id="record_tabs">
        <ul>
            <li style="list-style:none;"><a href="#record_tabs-1"><?php echo $current_module_strings['LBL_GENERAL']; ?></a></li>
            <li style="list-style:none;"><a href="#record_tabs-2"><?php echo $current_module_strings['LBL_PARTICIPANTS']; ?></a></li>
            <li style="list-style:none;"><a href="#record_tabs-3"><?php echo $current_module_strings['LBL_RECURENCE']; ?></a></li>
        </ul>
        <div id="record_tabs-1"> 

            <form id="EditView" name="EditView" method="POST">	

                <input name='return_module' id='return_module' type='hidden' value="Meetings">
                <input name='cur_module' id='cur_module' type='hidden' value="Meetings">
                <input name='record' id='form_record' type='hidden' value="">

                <?php include("modules/Calendar2/PopupEditView.php"); ?>

            </form>	

        </div>
        <div id="record_tabs-2">
            <?php include("modules/Calendar2/PopupParticipants.php"); ?>
        </div>
        <div id="record_tabs-3">
            <?php include("modules/Calendar2/PopupReccurence.php"); ?>			
        </div>
    </div>		
</div>

<div id="record_dialog_quick_input" title="Record dialog" style='display: none;margin-top: 22px;'>
    <div id="record_quickinput_tabs">
        <ul>
            <li style="list-style:none;"><a href="#record_quickinput_tabs-1"><?php echo $current_module_strings['LBL_GENERAL']; ?></a></li>

        </ul>
        <div id="record_quickinput_tabs-1"> 


            <?php
            //require_once('include/MassUpdate.php');
            include_once("modules/Calendar2/QuickInputClass.php");
            include_once("modules/Calendar2/PopupCustomersQuickInput.php");
            print Call_Meeting_output();
            ?>

            </form>

        </div>
    </div>		
</div>	


<script type="text/javascript" src="include/JSON.js?s=<?php echo $sugar_version; ?>&c=<?php echo $js_custom_version; ?>"></script>
<script type="text/javascript" src="include/jsolait/init.js?s=<?php echo $sugar_version; ?>&c=<?php echo $js_custom_version; ?>"></script>
<script type="text/javascript" src="include/jsolait/lib/urllib.js?s=<?php echo $sugar_version; ?>&c=<?php echo $js_custom_version; ?>"></script>

<script type="text/javascript">	
<?php
require_once('include/json_config_cal2.php');
global $json;
$json = getJSONobj();
$json_config_cal2 = new json_config_cal2();
$GRjavascript = $json_config_cal2->get_static_json_server(false, true, 'Meetings');

echo $GRjavascript;
?>
</script>

<script type="text/javascript" src="include/javascript/jsclass_base.js?s=<?php echo $sugar_version; ?>&c=<?php echo $js_custom_version; ?>"></script>
<script type="text/javascript" src="modules/Calendar2/jsclass_async_cal2.js?s=<?php echo $sugar_version; ?>&c=<?php echo $js_custom_version; ?>"></script>
<script type="text/javascript" src="modules/Meetings/jsclass_scheduler.js?s=<?php echo $sugar_version; ?>&c=<?php echo $js_custom_version; ?>"></script>
<script>toggle_portal_flag();function toggle_portal_flag()  {  } </script>



<script type="text/javascript">
	
    function fill_invitees() { 
        if (typeof(GLOBAL_REGISTRY) != 'undefined')  {    
            SugarWidgetScheduler.fill_invitees(document.EditView);
        } 
    }
	

	
    var root_div = document.getElementById('scheduler');
    var sugarContainer_instance = new SugarContainer(document.getElementById('scheduler'));
    sugarContainer_instance.start(SugarWidgetScheduler);
		
		
    /*if ( document.getElementById('save_and_continue') ) {
            var oldclick = document.getElementById('save_and_continue').attributes['onclick'].nodeValue;
            document.getElementById('save_and_continue').onclick = function(){
                fill_invitees();
                eval(oldclick);
            }
        }*/
		
    $(".schedulerInvitees").remove();
	
</script>

<script type="text/javascript">
<?php
if (isPro()) {
    echo 'var teams_or_users = "teams";';
} else {
    echo 'var teams_or_users = "users";';
}
?></script>

<script type="text/javascript" src="modules/Calendar2/particapants.js?s=<?php echo $sugar_version; ?>&c=<?php echo $js_custom_version; ?>"></script>

<script type="text/javascript">
    var GLOBAL_PARTICIPANTS = new Object();

    GLOBAL_PARTICIPANTS['Teams'] = [ 
<?php
require_once('include/database/PearDatabase.php');
$db = &PearDatabase::getInstance();
$db2 = &PearDatabase::getInstance();
if (isPro()) {
    $res = $db->query("SELECT id,name FROM teams WHERE private = 0 AND deleted <> 1 ORDER BY name");
    $ft = true;
    while ($row = $db->fetchByAssoc($res)) {
        if (!$ft)
            echo ",";
        else
            $ft = false;

        $dbU = &PearDatabase::getInstance();
        $qU = "
                                                SELECT u.id u_id,u.user_name u_name 
                                                        FROM users AS u
                                                        JOIN team_memberships AS tm ON tm.user_id = u.id
                                                        WHERE tm.team_id = '" . $row['id'] . "' AND u.deleted <> 1 AND tm.deleted <> 1
                                                        ORDER BY u.user_name 
                                                ";
        $resU = $dbU->query($qU);
        $users_str = "";
        $ftU = true;
        while ($rowU = $dbU->fetchByAssoc($resU)) {
            if (!$ftU)
                $users_str .= ",";
            else
                $ftU = false;
            $users_str .= "{" .
                    "'id': '" . $rowU['u_id'] . "'," .
                    "'name': '" . $rowU['u_name'] . "'" .
                    "}";
        }

        echo "{" .
        "'id': '" . $row['id'] . "'," .
        "'name': '" . $row['name'] . "'," .
        "'users': [ " . $users_str . " ]" .
        "}";
    }
} else {
    $qU = "
                                        SELECT u.id u_id,u.user_name u_name 
                                                FROM users AS u
                                                WHERE u.deleted <> 1
                                                ORDER BY u.user_name 
                                                ";
    $resU = $db->query($qU);
    $users_str = "";
    $ftU = true;
    while ($rowU = $db->fetchByAssoc($resU)) {
        if (!$ftU)
            $users_str .= ",";
        else
            $ftU = false;
        $users_str .= "{" .
                "'id': '" . $rowU['u_id'] . "'," .
                "'name': '" . $rowU['u_name'] . "'" .
                "}";
    }

    echo "{" .
    "'id': '1'," .
    "'name': 'Global'," .
    "'users': [ " . $users_str . " ]" .
    "}";
}
?>
				
    ];
				
    GLOBAL_PARTICIPANTS['Resources'] = [ 
<?php
$query = "SELECT id,name FROM resources ";
global $current_user;
if (is_admin($current_user)) {
    $query .= " WHERE deleted <> 1 ORDER BY name";
} else {
    require_once('modules/Resources/Resource.php');
    $temp_res = new Resource();
    if (isPro()) {
        $temp_res->add_team_security_where_clause($query);
        $query .= " AND deleted <> 1 ORDER BY name";
    } else {
        $query .= " WHERE deleted <> 1 ORDER BY name";
    }
}
$res = $db->query($query);
$ft = true;
while ($row = $db->fetchByAssoc($res)) {
    if (!$ft)
        echo ",";
    else
        $ft = false;

    echo "{" .
    "'id': '" . $row['id'] . "'," .
    "'name': '" . $row['name'] . "'" .
    "}";
}
?>		
		
    ];
		
<?php
if (isPro()) {
    echo "fill_teams();";
} else {
    echo "fill_users_ce();";
}
?>
    fill_resources();
		
    prior_filling_select_boxes();
	
</script>

<div id="recurrence_dialog" title="Recurrence List" style='display: none;'>
        <table id="recurrence_list" style="width: 100%">
            <thead>
                <th>Customer Number</th>
                <th>Customer Name</th>
                <th>Type</th>
                <th>Start Date of Recurrence</th>
                <th>End Date of Recurrence</th>
                <th>End Recurrence</th>
                <th>Extend End Date of Recurrence</th>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" class="dataTables_empty">Loading data from server</td>
                </tr>
            </tbody>
        </table>
</div>

<div id="settings_dialog" title="<?php echo $current_module_strings['LBL_SETTINGS']; ?>" style='display: none;'>
    <?php include("modules/Calendar2/PopupSettings.php"); ?>	
</div>

<script type="text/javascript">
    var day_start_hours = "<?php echo $d_start_hour; ?>";
    var day_start_minutes = "<?php echo $d_start_min; ?>";
    var day_start_meridiem = "<?php echo $start_m; ?>"; // don't remove this line!
</script>



<script type="text/javascript">
    addToValidate('EditView', 'name', 'name', true,'Subject' );
<?php if (isPro()) echo "addToValidate('EditView', 'team_count', 'relate', true,'Teams' );"; ?>
<?php if (isPro()) echo "addToValidate('EditView', 'team_name', 'teamset', true,'Teams' );"; ?>
    addToValidate('EditView', 'duration_hours', 'int', true,'Duration Hours' );
    addToValidate('EditView', 'date_start_date', 'date', true,'Start Date' );
    addToValidate('EditView', 'status', 'enum', true,'Status' );
    addToValidateBinaryDependency('EditView', 'assigned_user_name', 'alpha', false,'No match for field: Assigned to', 'assigned_user_id' );
    //date validation
    function isDate(txtDate){
        var currVal = txtDate;
        if(currVal == '')
            return false;

        //Declare Regex  
        var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/; 
        var dtArray = currVal.match(rxDatePattern); // is format OK?

        if (dtArray == null)
            return false;

        //Checks for mm/dd/yyyy format.
        dtMonth = dtArray[1];
        dtDay= dtArray[3];
        dtYear = dtArray[5];

        if (dtMonth < 1 || dtMonth > 12)
            return false;
        else if (dtDay < 1 || dtDay> 31)
            return false;
        else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31)
            return false;
        else if (dtMonth == 2)
        {
            var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
            if (dtDay> 29 || (dtDay ==29 && !isleap))
                return false;
        }
        return true;
    }
</script>

<script type="text/javascript">
    function open_new_meeting_or_call(module,record){
        URL = 'index.php?'
            + 'module='+module+'&action=EditView' + '&old_record='+record+'&return_module='+module+'&return_action=DetailView';
        win = window.open(URL,'_blank');
        //win = window.open(URL);
        //        URL = 'index.php?'
        //		+ 'module=Tasks' + '&action=EditView' + '&record='+record+'&isDuplicate=true&return_module=Tasks&return_action=EditView&status=';
        //        win = window.open(URL,'_blank');
        if(window.focus)
        {
            // put the focus on the popup if the browser supports the focus() method
            win.focus();
        }

        return win;
    }
    function open_new_task(width,height,close_button,record)
    {
        // window.document.popup_request_data = request_data;
        // window.document.close_popup = close_button;
        // launch the popup
        URL = 'index.php?'
            + 'module=Tasks' + '&action=EditView' + '&record='+record+'&isDuplicate=true&return_module=Tasks&return_action=EditView&status=';
        win = window.open(URL,'_blank');
        
        //       windowName = 'new_task_window';
        //       windowFeatures = 'fullscreen=1,resizable=1,scrollbars=1';
        ////	windowFeatures = 'width=' + width
        ////		+ ',height=' + height
        ////		+ ',resizable=1,scrollbars=1';
        //   win = window.open(URL, windowName, windowFeatures);

        if(window.focus)
        {
            // put the focus on the popup if the browser supports the focus() method
            win.focus();
        }

        return win;

    }

    function open_new_opportunity(width,height,close_button,record)
    {
   
        //window.document.close_popup = close_button;
        // launch the popup
        URL = 'index.php?'
            + 'module=Opportunities' + '&action=EditView' + '&record='+record+'&isDuplicate=true&return_module=Opportunities&return_action=EditView&status=';
        win = window.open(URL,'_blank');

        //       windowName = 'new_task_window';
        //       windowFeatures = 'fullscreen=1,resizable=1,scrollbars=1';
        ////	windowFeatures = 'width=' + width
        ////		+ ',height=' + height
        ////		+ ',resizable=1,scrollbars=1';
        //   win = window.open(URL, windowName, windowFeatures);
        //
        if(window.focus)
        {
            // put the focus on the popup if the browser supports the focus() method
            win.focus();
        }

        return win;

    }
</script>

<?php
require_once 'include/QuickSearchDefaults.php';
$qsd = new QuickSearchDefaults();

//pr($qsd->getQSUser());

$sqs_objects['cal2_assigned_user_name'] = $qsd->getQSUser();

$o = $qsd->getQSParent();
$o['field_list'] = array('name', 'custno_c', 'id');
$o['populate_list'] = array("parent_name", 'parent_name_custno_c', "parent_id");
$o['field_list'][] = 'account_name';
$o['populate_list'][] = 'lead_account_name';
$sqs_objects['parent_name'] = $o;

$o = $qsd->getQSParent();
$o['field_list'] = array('custno_c', 'name', 'id');
$o['populate_list'] = array('parent_name_custno_c', "parent_name", "parent_id");
$o['conditions'][0]['name'] = 'custno_c';
$o['order'] = 'custno_c';

$sqs_objects['parent_name_custno_c'] = $o;
$sqs_objects['cal2_assigned_user_name'] = $qsd->getQSUser();

$o = $qsd->getQSParent();
$o['field_list'] = array('account_name', 'name', 'id');
$o['populate_list'] = array('lead_account_name', "parent_name", "parent_id");
$o['conditions'][0]['name'] = 'account_name';
$o['order'] = 'account_name';
$o['modules'][0] = 'Leads';

$sqs_objects['lead_account_name'] = $o;
$sqs_objects['cal2_assigned_user_name'] = $qsd->getQSUser();

$quicksearch_js = '<script language="javascript">';
$quicksearch_js.= "if(typeof sqs_objects == 'undefined'){var sqs_objects = new Array;}";
$json = getJSONobj();
foreach ($sqs_objects as $sqsfield => $sqsfieldArray) {
    $quicksearch_js .= "sqs_objects['$sqsfield']={$json->encode($sqsfieldArray)};";
}
echo $quicksearch_js . '</script>';
?>
<script type="text/javascript">
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
                if($("#slsm_list_show").css("display") != "none"){
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
                if($("#reps_list_show").css("display") != "none"){
                    var select_reps = $("#opp_sales_reps_list option:selected").val();
                      if (select_reps != null){
                        var selected_reps = new Array();
                        $("#opp_sales_reps_list option:selected").each(function (k, v) {
                                        selected_reps[k] = $(this).text();	
                        });
                        if(selected_reps.length != 0){
                                filter += "<b>Sales Reps:</b>"+selected_reps.join(", ")+"; ";
                        }else{
                                filter += "<b>Sales Reps:</b> all;";
                        }
                    }else{
                        filter += "<b>Sales Reps:</b> all;";
                    }
                }         
                if($("#leads_status_show").css("display") != "none"){
                    var select_status = $("#leads_status").val();
                     if (select_status != null){
                        var selected_status = new Array();
                        $("#leads_status option:selected").each(function (k, v) {
                                        selected_status[k] = $(this).text();	
                        });
                        if(selected_status.length != 0){
                                filter += "<b>Status:</b>"+selected_status.join(", ")+"; ";
                        }else{
                                filter += "<b>Status:</b> all;";
                        }
                    }else{
                        filter += "<b>Status:</b> all;";
                    }
                }    
                 if($("#leads_source_show").css("display") != "none"){
                     var select_source = $("#leads_source").val();
                     if (select_source != null){
                        var selected_source = new Array();
                        $("#leads_source option:selected").each(function (k, v) {
                                        selected_source[k] = $(this).text();	
                        });
                        if(selected_source.length != 0){
                                filter += "<b>Leads Sources:</b>"+selected_source.join(", ")+"; ";
                        }else{
                                filter += "<b>Leads Sources:</b> all;";
                        }
                    }else{
                        filter += "<b>Leads Sources:</b> all;";
                    }
                 }  
                        
			
			
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

    function isEditedForm () {
            var formValues = new Object();
            var result = false;
            $("div#record_tabs-1 :input,div#record_tabs-1 select option:selected, div#record_tabs-3 :input,div#record_tabs-3 select option:selected").each( function (k, v) {
                    if(typeof v.id != 'undefined' && v.id != ''){
                        formValues[v.id+"___"+k] = $(v).attr('type') == 'checkbox' ?$(v).is(":checked") : v.value;
                    }else if(typeof v.name != 'undefined' && v.name != ''){
                        formValues[v.name+"___"+k] = $(v).attr('type') == 'checkbox' ?$(v).is(":checked") : v.value;
                    }else if(typeof v.parentNode.id != 'undefined' && v.parentNode.id != ''){
                        formValues[v.parentNode.id+"___"+k] = v.value;
                    }else if(typeof v.parentNode.name != 'undefined' && v.parentNode.name != ''){
                        formValues[v.parentNode.name+"___"+k] = v.value;
                    }
            });
            if(sessionStorage.length != 0) {
                    var prev = eval('(' + sessionStorage.getItem("formValues") + ')');
                    $.each(formValues, function (k, v) {
                        if(formValues[k] != prev.jsonObject[k] && !(k.indexOf('repeat_end_date___') + 1)){
                            result = true;
                            return false;
                        }
                    });
            }
            return result;
    }
</script>
