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
 //var teams_or_users = "teams";

function fill_teams(){
	$.each(
			GLOBAL_PARTICIPANTS['Teams'], 
			function(i,v){
				$("<option>")
				.val(v.id)
				.html(v.name)
				.appendTo("#sel_team_list");
			}	
	);
}

function fill_resources(){
	$.each(
			GLOBAL_PARTICIPANTS['Resources'], 
			function(i,v){
				$("<option>")
				.val(v.id)
				.html(v.name)
				.appendTo("#sel_resource_list");
			}	
	);
}

function fill_users(){
	$("#sel_user_list option").remove();
	$.each(
		$("#sel_team_list option:selected"),
		function(i,t){	
			var users = find_users_by_team($(t).val());
			$.each(
				users,
				function(j,v){
					if(!($("#sel_user_list option[value='" + v.id + "']").length))
						$("<option>")
						.val(v.id)
						.html(v.name)
						.appendTo("#sel_user_list");					
				}
			);			
		}	
	);
}

function fill_users_ce(){
	$("#sel_user_list option").remove();
	var users = find_users_by_team('1');
		$.each(
			users,
			function(j,v){
				if(!($("#sel_user_list option[value='" + v.id + "']").length))
					$("<option>")
					.val(v.id)
					.html(v.name)
					.appendTo("#sel_user_list");					
			}
		);
}

function find_users_by_team(team_id){
	var users = Object();
	$.each(
			GLOBAL_PARTICIPANTS['Teams'], 
			function(i,v){
				if(v.id == team_id)
					users = v.users; 
			}	
	);
	return users;
}

function add_users(){
	if(teams_or_users == "teams"){
		var type = 'Team';
		var select_id = 'sel_team_list';
	}else{
		var type = 'User';
		var select_id = 'sel_user_list';	
	}
	
	$.each(
		$("#" + select_id + " option:selected"),
		function(i,v){
					if(!($("#sel_user_list_selected option[value='" + $(v).val() + "'][type='" + type + "']").length))
						$("<option>")
						.val($(v).val())
						.attr("type",type)
						.html($(v).html())
						.appendTo("#sel_user_list_selected");						
		}	
	);
	
	handle_btn_remove();
	GR_arr_update();	
}

function remove_users(){
	if($("#sel_user_list_selected option:selected").length == $("#sel_user_list_selected option").length){
		alert(lbl_remove_participants);
		return;
	}

	$("#sel_user_list_selected option:selected").remove();
	
	handle_btn_remove();	
	GR_arr_update();
}

function add_resources(){	
	$.each(
		$("#sel_resource_list option:selected"),
		function(i,v){
					if(!($("#sel_resource_list_selected option[value='" + $(v).val() + "']").length))
						$("<option>")
						.val($(v).val())
						.attr("type",'Resource')
						.html($(v).html())
						.appendTo("#sel_resource_list_selected");						
		}	
	);
		
	handle_btn_remove();
	GR_arr_update();	
}

function remove_resources(){
	$("#sel_resource_list_selected option:selected").remove();
	
	handle_btn_remove();
	GR_arr_update();
}

function prior_filling_select_boxes(){
		$("#sel_user_list_selected option").remove();
		$("#sel_resource_list_selected option").remove();
				$.each( 
								GLOBAL_REGISTRY['focus'].users_arr,
								function(i,v){
									var select_id = "";
									var dn;
									if(v.module == "User"){
										select_id = 'sel_user_list_selected';
										type = "User";
										dn = v.fields.user_name;
									}
									if(v.module == "Resource"){
										select_id = 'sel_resource_list_selected';
										type = "Resource";
										dn = v.fields.name;
									}
									if(!($("#"+select_id+" option[value='" + v.fields.id + "'][type='" + type + "']").length))
										$("<option>")
										.val(v.fields.id)
										.attr("type",type)
										.html(dn)
										.appendTo("#"+select_id);
								}
				);
		handle_btn_remove();
}

function handle_btn_remove(){
	if($("#sel_user_list_selected option").length > 1)
		$("#btn_remove_users").removeAttr("disabled");
	else
		$("#btn_remove_users").attr("disabled","disabled");
		
	if($("#sel_resource_list_selected option").length > 0)
		$("#btn_remove_resources").removeAttr("disabled");
	else
		$("#btn_remove_resources").attr("disabled","disabled");		
		
}

SugarWidgetScheduleRow.deleteRow = function(bean_id) {
	if(GLOBAL_REGISTRY.focus.users_arr.length == 1 || GLOBAL_REGISTRY.focus.fields.assigned_user_id == bean_id) {
        	return;
      	}
	
	for(var i=0;i<GLOBAL_REGISTRY.focus.users_arr.length;i++) {
		if(GLOBAL_REGISTRY.focus.users_arr[i]['fields']['id']==bean_id) {
			  delete GLOBAL_REGISTRY.focus.users_arr_hash[GLOBAL_REGISTRY.focus.users_arr[i]['fields']['id']];
			  GLOBAL_REGISTRY.focus.users_arr.splice(i,1);
			  sugarContainer_instance.root_widget.display();
			  sugarContainer_instance.root_widget.display();
		}
      }     
     
      prior_filling_select_boxes();
}

