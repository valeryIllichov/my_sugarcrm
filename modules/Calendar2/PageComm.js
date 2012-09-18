/******************************************************************************
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

    *The COMPANY may not copy, deliver, distribute the SOFTWARE without written
     permit from OpensourceCRM.
    *The COMPANY may not reverse engineer, decompile, or disassemble the 
    SOFTWARE, except and only to the extent that such activity is expressly 
    permitted by applicable law notwithstanding this limitation.
    *The COMPANY may not sell, rent, or lease resell, or otherwise transfer for
     value, the SOFTWARE.
    *Termination. Without prejudice to any other rights, OpensourceCRM may 
    terminate this Agreement if the COMPANY fail to comply with the terms and 
    conditions of this Agreement. In such event, the COMPANY must destroy all 
    copies of the SOFTWARE and all of its component parts.
    *OpensourceCRM will give the COMPANY notice and 30 days to correct above 
    before the contract will be terminated.

The SOFTWARE is protected by copyright and other intellectual property laws and 
treaties. OpensourceCRM owns the title, copyright, and other intellectual 
property rights in the SOFTWARE.
*****************************************************************************/
function show_i(d_id,type){
    var obj = $("#" + d_id);	
    subj = $(obj).attr("subj");
    date_start = $(obj).attr("time_start");
    duration = $(obj).attr("lang");
    desc = $(obj).attr("desc");
    disc_point_name = (type == 'task')? 'Discussion Points: ' : 'Pre-Call Plan: ';

    desc = '<br>'
    + '<b>' + 'Customer Account Name:' + '</b> ' + $(obj).attr("customer_name") +'<br>'
    + '<b>' + 'Customer Number:' + '</b> ' + $(obj).attr("custno_c") +'<br>'
    + '<b>' + disc_point_name + '</b> ' + desc +'<br>'
    ;

    if(subj == '')
        return "";					

    //var date_string = "Start";
    var date_string = SUGAR.language.get('app_strings', 'DATA_TYPE_START');

    if(type == 'task')
        //date_string = "Due";
        date_string = SUGAR.language.get('app_strings', 'DATA_TYPE_DUE');

                        
    return overlib('<b>' + date_string + '</b> ' + date_start + ' <br><b>' + SUGAR.language.get('app_strings', 'LBL_CAL2_DURATION') + '</b> ' + duration + '<br><b>Subject: </b> ' + subj + desc, CAPTION, SUGAR.language.get('app_strings', 'LBL_CAL2_ADDITIONAL_DETAIL'), DELAY, 200, STICKY, MOUSEOFF, 1000, WIDTH, 300, CLOSETEXT, '<img border=0  style=\'margin-left:2px; margin-right: 2px;\' src=themes/Sugar/images/close.gif>', CLOSETITLE, 'Click to Close', CLOSECLICK, FGCLASS, 'olFgClass', CGCLASS, 'olCgClass', BGCLASS, 'olBgClass', TEXTFONTCLASS, 'olFontClass', CAPTIONFONTCLASS, 'olCapFontClass', CLOSEFONTCLASS, 'olCloseFontClass');
}

function check_whole_day(){
    if($("#cal2_whole_day_c").attr('checked')){
        $("#duration_hours").val(day_duration_hours);
        $("#duration_minutes option[value='"+day_duration_minutes+"']").attr("selected","selected");			
        $("#date_start_hours option[value='"+day_start_hours+"']").attr("selected","selected");
        $("#date_start_minutes option[value='"+day_start_minutes+"']").attr("selected","selected");
        $("#date_start_meridiem option[value='"+day_start_meridiem+"']").attr("selected","selected");
			
        $("#duration_hours_h").val($("#duration_hours").val());
        $("#duration_minutes_h").val($("#duration_minutes option:selected").val());
		
        $("#date_start_hours").attr("disabled","disabled");
        $("#date_start_minutes").attr("disabled","readonly");
        $("#date_start_meridiem").attr("disabled","disabled");
        $("#duration_hours").attr("disabled","disabled");
        $("#duration_minutes").attr("disabled","disabled");
			
        combo_date_start.update();			
    }else{
        $("#date_start_hours").removeAttr("disabled");
        $("#date_start_minutes").removeAttr("disabled");
        $("#date_start_meridiem").removeAttr("disabled");
        $("#duration_hours").removeAttr("disabled");
        $("#duration_minutes").removeAttr("disabled");	
			
        $("#duration_hours_h").val("");
        $("#duration_minutes_h").val("");	
    }
}
	
function align_divs(cell_id){		
	
    cellElm = document.getElementById(cell_id);

    if(cellElm){	
        max_zindex = 50;
				
        var total_height = 0;
        var prev_i = 0;
        var first = 1;
        var top = 0;
        var height = 0;
        var cnt = 0;

        for(var i = 0; i < cellElm.childNodes.length; i++){	
            if(cellElm.childNodes[i].tagName == "DIV"){

                if(first == 1){
                    first = 0;	
                    cnt++;	
                    $(cellElm.childNodes[i]).css({
                        "top" : "0px", 
                        "left" : "-1px", 
                        "z-index" : "50"
                    });
                }else{					
                    top = 0;
                    height = $(cellElm.childNodes[prev_i]).css("height");
                    height = height.split("px").join("");
                    ;	
                    total_height += parseInt(height);					
                    var new_top = parseInt(top) - total_height - cnt;
                    var left = "12px";
                    if(cnt > 1)
                        var left = "18px";
                    $(cellElm.childNodes[i]).css({
                        "top" : new_top + "px", 
                        "left" : left, 
                        "z-index" : "50"
                    });
                    cnt++;
                }
                prev_i = i;					
            }
        }
    }
}
	
	
	
var nwn = new Array();
	
function AddRecordToPage(ActRecord){
    if ( typeof(ActRecord) != 'object' ) {
        return ;
    }
    var duration_text = ActRecord.duration_hours + "h";
    if(ActRecord.duration_minutes > 0)
        duration_text += ActRecord.duration_minutes + "m";
    var startD = new Date((ActRecord.start)*1000);
			
    var suffix = "";
    var id_suffix = "";
    if(ActRecord.user_id != current_user_id && ActRecord.user_id != "" && pview == 'shared'){
        suffix = "_" + shared_users[ActRecord.user_id];
        id_suffix = '____' + shared_users[ActRecord.user_id];
    }

    $("#" + ActRecord.record + id_suffix).remove();
    var start_text = ActRecord.time_start;
    var time_cell = ActRecord.start - ActRecord.start % (t_step * 60);			
			
    var duration_coef; 
    if(ActRecord.type == 'task'){
        duration_coef = 2;
        duration_text = " ";
    }
    else{	
        if((ActRecord.duration_minutes < t_step) && (ActRecord.duration_hours == 0))
            duration_coef = 1;
        else					
            duration_coef = (parseInt(ActRecord.duration_hours) * 60 + parseInt(ActRecord.duration_minutes)) / t_step;
    }
			
    var subj = "";
    if(duration_coef >= 1.75)
        subj = ActRecord.record_name;

    //			alert(ActRecord.record + id_suffix);
    //			alert(ActRecord.cal2_recur_id_c);


    $("<div><div class='record_head'>" + ActRecord.widget_title + " " + "" + " <div class='rfloat' onmouseover='return show_i(" + '"' + ActRecord.record  + id_suffix + '"'  +  "," + '"' + ActRecord.type + '"' + ");' onmouseout='return nd(1000);' >&nbsp; i &nbsp;</div><br style='clear: both;'></div><div class='record_contain'>" + subj + "</div></div>")
    .addClass("record_item")
    .addClass(ActRecord.parent_type+"_"+ActRecord.type)
    .addClass(ActRecord.type+"_item")
    .attr("id",ActRecord.record + id_suffix)
    .attr("cal2_recur_id_c",ActRecord.cal2_recur_id_c)
    .attr("record",ActRecord.record)
    .attr("lang",duration_text)
    .attr("subj",ActRecord.record_name)
    .attr("location",ActRecord.location)
    .attr("date_start",ActRecord.date_start)
    .attr("time_start",ActRecord.time_start)
    .attr("desc",ActRecord.description)
    .attr("acttype",ActRecord.type)
    .attr("detailview",ActRecord.detailview)
    .attr("duration_coef",duration_coef)
    .attr("parent_type",ActRecord.parent_type)
    .attr("parent_id",ActRecord.parent_id)
    .attr("custno_c", ActRecord.custno_c)
    .attr("customer_name", ActRecord.customer_name)
    .attr("widget_title", ActRecord.widget_title)
    .css({
        "height" : parseInt(14 * duration_coef) + "px"
        } )
    .click(
        function(){
            max_zindex += 1;
            $(this).css({
                "z-index" : max_zindex
            });
            if($(this).attr('detailview') == "1"){
                $("#clicked_record").val($(this).attr('record'));
                $("#clicked_cal2_recur_id_c").val($(this).attr('cal2_recur_id_c'));
                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(7)").attr("disabled","disabled");
                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(8)").attr("disabled","disabled");
                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(4)").removeAttr("disabled");
                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(3)").removeAttr("disabled");
                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(2)").removeAttr("disabled");
                $("#opp_table").empty();
                FormLoad($(this).attr('acttype'),$(this).attr('record'),false);
            }
        }
        )
					
    .mouseover(	function(){ 	
        disable_creating = true;		
    }
    )
    .mouseout(	function(){ 
							
        disable_creating = false;		
    }
    )	
    .appendTo("#t_" + time_cell + suffix);	
				
				
				
    //if(ActRecord.type != 'task' && pview != 'shared' && ActRecord.detailview)
    if(ActRecord.type != 'task' && ActRecord.detailview){
        $(("#" + ActRecord.record))
        .draggable(
        { 
            revert: 'invalid', 
            containment: '#week_div', 
            handle: 'div', 
            stack: {
                group: 'products', 
                min: 50
            }, 
            zIndex: 500,
            start: 	function(event, ui) { 
                dropped = 0;
                records_openable = false;
                old_caption = ui.helper.find('div.record_head').html();
                moved_from_cell = ui.helper.parent().attr("id");
            }
        }
        );
        $(("#" + ActRecord.record + id_suffix))
        .draggable(
        { 
            revert: 'invalid', 
            containment: '#week_div', 
            handle: 'div', 
            stack: {
                group: 'products', 
                min: 50
            }, 
            zIndex: 500,
            start: 	function(event, ui) { 
                dropped = 0;
                records_openable = false;
                old_caption = ui.helper.find('div.record_head').html();
                moved_from_cell = ui.helper.parent().attr("id");
            }
        }
        );
    }
				
				
    cut_record(ActRecord.record + id_suffix);				
    align_divs("t_" + time_cell + suffix);	
				
}
	
var hs = "";
function cut_record(id){
	
    var rec = $("#" + id);
    var duration_coef = rec.attr("duration_coef");			
			
    var celcount = $("#week_div .day_col:nth-child(2) .t_cell").size();
    var celpos = rec.parent().parent().children().index(rec.parent());
    if (celcount - celpos - duration_coef < 0)
        duration_coef = celcount - celpos  + 1;
			
    rec.css({
        "height" : parseInt(14 * duration_coef) + "px"
        } );
			
    if(duration_coef < 1.75)
        $("#" + id + "record_contain").html("");
}
	
function clearFields_cal2(){
    $("#EditView").resetForm();
    $("#direction").css("display","none");
    $("#should_remind_list").css("display","none");
    $("#parent_id").val("");
    $("#form_record").val("");
    $("#cur_module").val("Meetings");
    $("#return_module").val("Meetings"); 		
    $("#radio_call").removeAttr("checked");
    $("#radio_meeting").attr("checked",true);
    $("#radio_call").removeAttr("disabled");
    $("#radio_meeting").removeAttr("disabled");
		
    //for 5.5.1
    if (is551) {
        $("#id_team_name_collection_0").val(default_team_id);
        $("#team_name_collection_0").val(default_team_name);
        $("#primary_team_name_collection_0").attr("checked", true);
    } else {
        //for 5.5 or 5.2
        $("#team_id").val(default_team_id); 	
        $("#team_name").val(default_team_name);
    }
 		
    $("#send_invites").removeAttr("disabled");
 		
    $("#edit_all_recurrence").val("");
	
    if (default_team_id != "" && is551) {
        collection['EditView_team_name'].secondaries_values = new Array(); 
        if(!($.browser.opera))					
            collection['EditView_team_name'].clean_up();		
        collection["EditView_team_name"].js_more();
    }
		
    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(6)").removeAttr("disabled");
														
    $("#EditView table.edit td:nth-child(2n) span.required").remove();
 		
 		
    $("#repeat_type option[value='']").attr("selected","selected");
    repeat_type_selected(); 		
	
    GR_update_focus("Meetings","");
 		
    check_whole_day();
 		
    $("#record_tabs").tabs('select',0);
 		
 						
}
	
function GR_update_focus(module,record){
    if(record == ""){
        GLOBAL_REGISTRY["focus"] = {
            "module":module, 
            users_arr:[],
            fields:{
                "id":"-1"
            }
        };
    SugarWidgetScheduler.update_time();
    prior_filling_select_boxes();
}
else{
    $.ajax(
    {
        url: 'index.php?module=Calendar2&action=AjaxGetGR&sugar_body_only=true&type=' + module + '&record=' + record,
        dataType: "script", 
        success: 	function(){
            SugarWidgetScheduler.update_time();
            prior_filling_select_boxes();														
        }						
    }	
    );
}		
}
	
function GR_arr_update(){
	
    var user_str = "";
    var resource_str = "";
    $.each(
        $("#sel_user_list_selected option[type='User']"),
        function (i,v){
            user_str += $(v).val() + ",";				
        }	
        );
    $.each(
        $("#sel_user_list_selected option[type='Team']"),
        function(i,t){
            var users = find_users_by_team($(t).val());
            $.each(
                users,
                function(j,u){
                    user_str += u.id + ",";														
                }				
                );				
        }	
        );		
    $.each(
        $("#sel_resource_list_selected option"),
        function (i,v){
            resource_str += $(v).val() + ",";				
        }	
        );				
		
    $.ajax(
    {
        url: 'index.php?module=Calendar2&action=AjaxGetGRArr&sugar_body_only=true',
        dataType: "script", 
        type: "POST",
        data: 	{
            "users": user_str,
            "resources": resource_str
        },
        success: 	function(){								
            SugarWidgetScheduler.update_time();
        //prior_filling_select_boxes();														
        }						
    }	
    );
			
}
	
function fill_invitees2(){
	
    $("#EditView input[name='user_invitees']").val("");
    $("#EditView input[name='resources_assigned']").val("");
		
    $.each( GLOBAL_REGISTRY['focus'].users_arr, 	function(i,v){
        var field_name = "";
        if(v.module == "User")
            field_name = "user_invitees";
        if(v.module == "Resource")
            field_name = "resources_assigned";
        var str = $("#EditView input[name='" + field_name + "']").val();
        $("#EditView input[name='" + field_name + "']").val(str + v.fields.id + ",");	
    }
    );	
}	
		
function fill_reccurence(){
    $("#cal2_repeat_type_c").val($("#repeat_type").val());
    $("#cal2_repeat_interval_c").val($("#repeat_interval").val());
    $("#cal2_repeat_end_date_c").val($("#repeat_end_date").val());
    $("#cal2_repeat_days_c").val("");

    if( $("#repeat_type").val() == 'Weekly' || $("#repeat_type").val() == 'Monthly (day)'){
			
        $.each(
            $(".weeks_checks:checked"),
            function (i,v){
                $("#cal2_repeat_days_c").val($("#cal2_repeat_days_c").val() + $(v).val());				
            }
            );
    }	
}
	
function repeat_type_selected(){
    if( $("#repeat_type").val() == 'Weekly' || $("#repeat_type").val() == 'Monthly (day)')
        $(".weeks_checks").removeAttr("disabled");
    else
        $(".weeks_checks").attr("disabled","disabled");
		
    if( $("#repeat_type").val() == '' ){
        $("#repeat_interval").attr("disabled","disabled");
        $("#repeat_end_date").attr("disabled","disabled");
    }else{
        $("#repeat_interval").removeAttr("disabled");
        $("#repeat_end_date").removeAttr("disabled");		
    }
}
	
function FormLoad(type,record,run_one_time){
    var to_open = false;
    if(type == 'call'){
        type = "Calls";
        to_open = true;			
    }
    if(type == 'meeting'){
        type = "Meetings";
        to_open = true;
    }
		
    if(to_open && records_openable){
		
        $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(1)").attr("disabled","disabled");
        $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(6)").attr("disabled","disabled");
			
        if(!run_one_time){
            $("#previous_outcome_span").css('display','block');
            $("#previous_outcome_c").css('display','block');
            $("#next_date_span").css('display','block');
            $("#next_date").css('display','block');
            $("#next_description_span").css('display','block');
            $("#next_description").css('display','block');
                      
        }
                        
                        
                        
			
                        
        $('#ui-dialog-title-record_dialog').html(lbl_loading);
			
        $('#EditView input').attr("disabled", true);
        $('#EditView select').attr("disabled", true);	
        $('#EditView textarea').attr("disabled", true);
			
        $('#record_dialog').dialog('open');	
	
        $("#form_record").attr("value","");
			
						
        var pos = $('#ui-dialog-title-record_dialog').offset();    
        var eWidth = $('#ui-dialog-title-record_dialog').outerWidth()
	
        $("<img src='themes/default/images/img_loading.gif'>")
        .appendTo('.loader_div');
			
        changeQS();
        $.getJSON(							
            "index.php?module=Calendar2&action=AjaxLoad&sugar_body_only=true",
            {
                "cur_module" : type,
                "record" : record
            },								
            function(res){									
                if(res.succuss == 'yes'){													
                    $("#form_record").val(res.record);
													
                    $("#cal2_recur_id_c").val(res.cal2_recur_id_c);
													
                    if(res.type == 'call'){
                        $("#return_module").val("Calls");
                        $("#cur_module").val("Calls");
                        $("#direction").css("display","inline");
                        $("#radio_call").attr("checked",true);
                        $("#radio_meeting").removeAttr("checked");
                    }
                    if(res.type == 'meeting'){
                        $("#return_module").val("Meetings");
                        $("#cur_module").val("Meetings");
                        $("#direction").css("display","none");
                        $("#radio_meeting").attr("checked",true);
                        $("#radio_call").removeAttr("checked");
                    }

                    //$("#name").val(res.record_name);
                    $("#opp_table").empty();
                    $("#name").val(res.record_name.replace(/<br>/gi, "\n").replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"'));

                    $("#location").val(res.location.replace(/<br>/gi, "\n").replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"'));
													
                    $("select[name='status']>option[value='"+res.status+"']").attr("selected",true);
                    $("select[name='direction']>option[value='"+res.direction+"']").attr("selected",true);
													
                    $("input[name='duration_hours']").val(res.duration_hours);
                    $("select[name='duration_minutes']>option[value='"+res.duration_minutes+"']").attr("selected",true);
                    if(parseInt(res.reminder_time) != parseInt(-1)){
                        $("input[name='reminder_checked']").attr("checked",true);
                        $("#should_remind_list").css("display","inline");
                        $("select[name='reminder_time']>option[value='"+res.reminder_time+"']").attr("selected",true);													
                    }
                    if (is551) {
                        $("#team_name_collection_0").val(res.team_name.replace(/<br>/gi, "\n").replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"'));
                        $("#id_team_name_collection_0").val(res.team_id);
                        $("#primary_team_name_collection_0").attr("checked",true);
                    } else {
                        $("#cal2_team_name").val(res.team_name.replace(/<br>/gi, "\n").replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"'));
                        $("#cal2_team_id").val(res.team_id);
                    }
                    $("#cal2_assigned_user_name").val(res.user_name);
                    $("#cal2_assigned_user_id").val(res.assigned_user_id);
                    $("#parent_type>option[value='"+res.parent_type+"']").attr("selected",true);
                    $("#parent_name").val(res.parent_name.replace(/<br>/gi, "\n").replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"'));
                    $("#parent_id").val(res.parent_id);
                    //$("#description").val(res.description);
                    $("#description").val(res.description.replace(/<br>/gi, "\n").replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"'));
                    $("#outcome_c").val(res.outcome_c.replace(/<br>/gi, "\n").replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"'));
                    $("#lead_account_name").val(res.lead_account_name.replace(/<br>/gi, "\n").replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"'));
                    var previous_outcome = '';
                    var previous_date = '';
                    var previous_status = '';
                    var previous_all = '';
                    if(res.previous_outcome) previous_outcome = res.previous_outcome.replace(/<br>/gi, "\n").replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"');
                    if(res.previous_date) previous_date = ' ('+res.previous_date+')';
                    if(res.previous_status) previous_status = ' Status: '+res.previous_status;
                    previous_all = previous_outcome + previous_date + previous_status;
                    if((res.parent_type == 'Accounts' || res.parent_type == 'Leads') && res.previous_date == "") previous_all = "No previous meeting or call held for this customer or lead";
                    $("#previous_outcome_c").val(previous_all);
                    if(res.next_date){
                        $("#next_date").val(res.next_date);
                        if(res.next_description)  $("#next_description").val(res.next_description.replace(/<br>/gi, "\n").replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"'));
                    } else {
                        $("#next_date").val("No next meeting or call held for this customer or lead");
                        $("#next_description_span").css('display','none');
                        $("#next_description").css('display','none');
                    }
                    $("#parent_name_custno_c").val(res.custno_c);
                    if(parseInt(res.cal2_whole_day_c))
                        $("#cal2_whole_day_c").attr("checked",true);
                    if(parseInt(res.cal2_options_c))
                        $("#cal2_options_c").attr("checked",true);
                    $("#cal2_category_c > option[value='"+res.cal2_category_c+"']").attr("selected",true);
													
                    var combo_date_start = new Datetimecombo(res.date_start, "date_start", time_format, 102, '', ''); 
                    text = combo_date_start.html('SugarWidgetScheduler.update_time();');
                    document.getElementById('date_start_time_section').innerHTML = text;
                    eval(combo_date_start.jsscript('SugarWidgetScheduler.update_time();'));
                    combo_date_start.update();
													
													
                    $.each(res.opportunities_arr,
                        function(i, t){
                            $("#opp_table").append('<tr><td class="opp"><a style="color: #0B578F;" target="_blank" href="index.php?module=Opportunities&action=EditView&record='+t.opportunity_id+'">'+t.product_name+'</a></td><td class="opp">'+t.date_closed+'</td><td class="opp">'+t.sales_stage+'</td></tr>');
                        }
                        );
                                                                                                        
													
                    $('#EditView input').removeAttr("disabled");
                    $('#EditView select').removeAttr("disabled");
                    $('#EditView textarea').removeAttr("disabled");
													
                    $("#radio_call").attr("disabled",true);
                    $("#radio_meeting").attr("disabled",true);
													
													
                    $.each(res.teams,
                        function(i, t){
                            if(t.id == res.team_id){
                                $("#id_team_name_collection_0").val(t.id);
                                $("#team_name_collection_0").val(t.name);
                                $("#primary_team_name_collection_0").attr("checked", true);
                            }else																						
                                collection["EditView_team_name"].secondaries_values.push(t);								
                        }
                        );
																										
													
                    if(!($.browser.opera) && res.teams != "")
                        collection["EditView_team_name"].add_secondaries();													
                    if (res.teams != "") collection["EditView_team_name"].js_more();
																									
                    $('#ui-dialog-title-record_dialog').html(lbl_edit);
																										
                    SugarWidgetScheduler.update_time();
													
													
                    var mod_name = '';
                    if(res.type == 'call')
                        mod_name = 'Calls';
                    if(res.type == 'meeting')
                        mod_name = 'Meetings';																										
														
                    GR_update_focus(mod_name,res.record);	
													
                    $("#repeat_type option[value='" + res.cal2_repeat_type_c + "']").attr("selected","selected");
                    $("#repeat_interval option[value='" + res.cal2_repeat_interval_c + "']").attr("selected","selected");																									
                    $("#cal2_repeat_type_c").val(res.cal2_repeat_type_c);

                    $("#repeat_end_date").val(res.cal2_repeat_end_date_c);
													
													
                    if(res.cal2_recur_id_c != '')
                        $("#repeat_type").attr("disabled","disabled");
                    else
                        $("#repeat_type").removeAttr("disabled");
													
                    repeat_type_selected();
													
													
                    var d_str = res.cal2_repeat_days_c;
                    var d_arr = d_str.split("");
                    $.each(
                        d_arr,
                        function (i,v){
                            $(".weeks_checks[value='" + v + "']").attr("checked","checked");	
                        }
                        );
													
                    if(!run_one_time)
                        if(res.cal2_recur_id_c != '' || res.cal2_repeat_type_c != '' ){  
                            $("#question1").remove();
                            $('body').append('<div class="dialog questions" id="question1"><img src="themes/Sugar/images/question.png" alt="" /><span>Do you want to edit all recurring records at once?<span></div>');
                            $( "#question1" ).dialog({
                                resizable: false,
                                height:140,
                                width:400,
                                modal: true,
                                open: function(event) {
                                    $(this).next().find('button').css('float','right');
                                },
                                buttons: {
                                    "No": function() {
                                        $("#repeat_type option[value='']").attr("selected","selected");
                                        repeat_type_selected();
                                        $("#repeat_type").attr("disabled","disabled");
                                        //$(".record_dialog_class .ui-dialog-buttonpane button:nth-child(7)").removeAttr("disabled");
                                        //$(".record_dialog_class .ui-dialog-buttonpane button:nth-child(8)").removeAttr("disabled");
                                        $( this ).dialog( "close" );
                                    },
                                    "Yes": function() {
                                          $("#previous_outcome_span").css('display','none');
                                        $("#previous_outcome_c").css('display','none');
                                        $("#next_date_span").css('display','none');
                                        $("#next_date").css('display','none');
                                        $("#next_description_span").css('display','none');
                                        $("#next_description").css('display','none');
                                        if(res.cal2_recur_id_c != ''){
                                            clearFields_cal2();
                                            FormLoad(res.type,res.cal2_recur_id_c,true);
                                        }
                                        $("#edit_all_recurrence").val(true);
                                        $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(4)").attr("disabled","disabled");
                                        $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(3)").attr("disabled","disabled");
                                        $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(2)").attr("disabled","disabled");
                                        if (res.cal2_recur_id_c != ''){
                                        $("#question2").remove();
                                        $('body').append('<div class="dialog questions" id="question2"><img src="themes/Sugar/images/question.png" alt="" /><span>Would you like to extend the recurrence?<span></div>');
                                        $( "#question2" ).dialog({
                                            resizable: false,
                                            height:140,
                                            width:400,
                                            modal: true,
                                            open: function(event) {
                                                $(this).next().find('button').css('float','right');
                                            },
                                            buttons: {
                                                "No": function() {
                                                    $("#question3").remove();
                                                    $('body').append('<div class="dialog questions" id="question3"><img src="themes/Sugar/images/question.png" alt="" /><span>Would you like to end the recurrence early?<span></div>');
                                                    $( "#question3" ).dialog({
                                                        resizable: false,
                                                        height:140,
                                                        width:400,
                                                        modal: true,
                                                        open: function(event) {
                                                            $(this).next().find('button').css('float','right');
                                                        },
                                                        buttons: {
                                                            "No": function() {
                                                                $( this ).dialog( "close" );
                                                            },
                                                            "Yes": function() {
                                                                $("#record_tabs").tabs('select',2);
                                                                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(1)").attr("disabled","disabled");
                                                                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(6)").attr("disabled","disabled");
                                                                $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(7)").removeAttr("disabled");
                                                                $( this ).dialog( "close" );
                                                            }
                                                        }
                                                    }); 
                                                    $( this ).dialog( "close" );
                                                },
                                                "Yes": function() {
                                                    $("#record_tabs").tabs('select',2);
                                                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(1)").attr("disabled","disabled");
                                                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(6)").attr("disabled","disabled");
                                                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(8)").removeAttr("disabled");
                                                    $( this ).dialog( "close" );
                                                }
                                            }
                                        });   
                                    }                         
                                        $( this ).dialog( "close" );
                                    }
                                }
                            });
//                            if(confirm("Do you want to edit all recurring records at once?")){
//                     
//                            }else{
//                                
//                            }
 
                        }
													
                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(1)").removeAttr("disabled");
                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(6)").removeAttr("disabled");
                    $(".record_dialog_class .ui-dialog-buttonpane button:nth-child(5)").removeAttr("disabled");
													
                    check_whole_day();
	  var formValues = new Object();
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
                    sessionStorage.setItem("formValues", JSON.stringify(formValues));																									
                }else
                    alert(lbl_error_loading);	
												
												
                changeQS();
            }

            );
    }
    records_openable = true;								
}
	
	
function removeSharedById(rec_id){
    var cell_id = $("#" + rec_id).parent().attr("id");											
    $.each(
        shared_users,
        function(i,v){	
            if(i != current_user_id){																		
                $("#" + rec_id + '____' + v).remove();
                align_divs(cell_id + '_' + v);	
            }
        }
        );
    $("#" + rec_id).remove();
    align_divs(cell_id);				
}
	
	
function AddRecords(res){
		
    if($("#edit_all_recurrence").val() == true){
        ids = new Array();			
        $.each(
            $("div[cal2_recur_id_c='" + res.record + "']"),
            function (i,v){
                ids[i] = $(v).parent().attr('id'); 					
            }
            );				
        $("div[cal2_recur_id_c='" + res.record + "']").remove();				
        $.each(
            ids,
            function (i,v){
                align_divs(ids[i]);		
            }				
            );
    }
		
    if(pview != 'shared'){
        AddRecordToPage(res);
								
        var record_id = res.record;
        $.each(
            res.arr_rec,
            function (j,r){
                res.record = r.record;
                res.start = r.start;
                res.cal2_recur_id_c = record_id;
                AddRecordToPage(res);
            }				
            );
    }else{
        removeSharedById(res.record);
        record_id = res.record;
        var cal2_recur_id_c = res.cal2_recur_id_c;
        var start = res.start;
        $.each(
            res.users,
            function (i,v){
                var rec = res;
                rec.cal2_recur_id_c = cal2_recur_id_c;
                rec.start = start;						
                rec.user_id = v;
                rec.record = record_id;
						
                AddRecordToPage(rec);
						
                $.each(
                    rec.arr_rec,
                    function (j,r){
                        rec.record = r.record;
                        rec.start = r.start;
                        rec.cal2_recur_id_c = record_id;
                        AddRecordToPage(rec);
                    }
                    );
            }
            );
    }
}


function AddSplitRecords(res){

    if($("#edit_all_recurrence").val() == true){
        ids = new Array();
        $.each(
            $("div[cal2_recur_id_c='" + res.record + "']"),
            function (i,v){
                ids[i] = $(v).parent().attr('id');
            }
            );
        $("div[cal2_recur_id_c='" + res.record + "']").remove();
        $.each(
            ids,
            function (i,v){
                align_divs(ids[i]);
            }
            );
    }

    if(pview != 'shared'){
        AddRecordToPage(res);

        var record_id = res.record;
        $.each(
            res.arr_rec,
            function (j,r){
                res.record = r.record;
                res.start = r.start;
                res.status = r.status;
                res.description = r.desc;
                res.widget_title = r.widget_title;
                res.cal2_recur_id_c = record_id;
                AddRecordToPage(res);
            }
            );
    }else{
        removeSharedById(res.record);
        record_id = res.record;
        var cal2_recur_id_c = res.cal2_recur_id_c;
        var start = res.start;
        $.each(
            res.users,
            function (i,v){
                var rec = res;
                rec.cal2_recur_id_c = cal2_recur_id_c;
                rec.start = start;
                rec.user_id = v;
                rec.record = record_id;

                AddRecordToPage(rec);

                $.each(
                    rec.arr_rec,
                    function (j,r){
                        rec.record = r.record;
                        rec.start = r.start;
                        rec.status = r.status;
                        rec.description = r.desc;
                        rec.widget_title = r.widget_title;
                        rec.cal2_recur_id_c = record_id;
                        AddRecordToPage(rec);
                    }
                    );
            }
            );
    }
}



function clearSearchFields(){
    $("input:text").val("");
    $("input:checked").attr("checked", false);
}
	
function clearContacts(){
    $(".contactRow").remove();										
    $(".contactsTable .pagination button").attr("disabled","disabled");										
    $("#scFirst img").attr("src","themes/default/images/start_off.gif");
    $("#scPrev img").attr("src","themes/default/images/previous_off.gif");
    $("#scNext img").attr("src","themes/default/images/next_off.gif");
    $("#scLast img").attr("src","themes/default/images/end_off.gif");
    $("#pageNumbers").html("(0 - 0 of 0)");
}
	
function switchSearch(){
    var hide_text = SUGAR.language.get('Calendar2', 'LBL_HIDE_SEARCH')
    var show_text = SUGAR.language.get('Calendar2', 'LBL_SHOW_SEARCH')

    if($("#scDiv").css("display") == "none") {
        $("#scDiv").css("display","block");
        $("#toggle_search").val(hide_text);
    } else {
        $("#scDiv").css("display","none");
        $("#toggle_search").val(show_text);
    }
}
	
function searchContacts(offset){
    clearContacts();
    $.getJSON(							
        "index.php?module=Calendar2&action=AjaxSearchContacts&sugar_body_only=true",
        {
            "first_name" : $("#first_name_search").val(),
            "last_name" : $("#last_name_search").val(),
            "account_name" : $("#account_name_search").val(),
            "current_user_only" : $('#current_user_only_search').is(':checked'),
            "offset" : offset
        },
        function(res){	
		
            clearContacts();
										
            $.each(
                res.contacts,
                function (i,c){
                    $("<tr></tr>")
                    .addClass("contactRow")
                    .addClass("oddListRowS1")
                    .append(
                        $("<td></td>")
                        .addClass("oddListRowS1")
                        .html(
                            "<div class='scDrag' contact_id='"+c.c_id+"' account_id='"+c.a_id+"' account_name='"+c.a_name+"'>" + c.c_full_name + "</div>"
                            )				
                        )
                    .append(
                        $("<td></td>")
                        .addClass("oddListRowS1")
                        .html(c.a_name)				
                        )
                    .append(
                        $("<td></td>")
                        .addClass("oddListRowS1")
                        .html(c.c_primary_address_state)				
                        )
                    .append(
                        $("<td></td>")
                        .addClass("oddListRowS1")
                        .html(c.c_primary_address_city)				
                        )
                    .appendTo(".contactsTable");																																	
                }
                );
																													
										
            if(res.offset > 0){
                $("#scFirst").removeAttr("disabled");
                $("#scPrev").removeAttr("disabled");
											
											
                $("#scFirst img").attr("src","themes/default/images/start.gif");
                $("#scPrev img").attr("src","themes/default/images/previous.gif");
											
                $("#scFirst").click(
                    function(){
                        searchContacts(0);																			
                    }
                    );
                $("#scPrev").click(
                    function(){
                        searchContacts(res.offset - 10);																			
                    }
                    );
            }
										
            if((res.offset + res.count) < res.total_count){
                $("#scNext").removeAttr("disabled");
                $("#scLast").removeAttr("disabled");
                $("#scNext img").attr("src","themes/default/images/next.gif");
                $("#scLast img").attr("src","themes/default/images/end.gif");
                $("#scLast").click(
                    function(){
                        searchContacts((parseInt(res.total_count/10)) * 10);																			
                    }
                    );
                $("#scNext").click(
                    function(){
                        searchContacts(res.offset + 10);																			
                    }
                    );
            }
										
										
										
            $("#pageNumbers").html("(" + (res.offset + 1) + " - " + (res.offset + res.count) + " of " + res.total_count + ")");
            $(".scDrag")
            .css("cursor","move")
            .draggable(
            { 
                revert: true, 
                handle: 'div', 													
                zIndex: 500,
                start: 	function(event, ui) { 
                }
            }
            );
									
        }
        );
}
