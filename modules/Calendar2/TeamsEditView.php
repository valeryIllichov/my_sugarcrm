<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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
/*********************************************************************************
* Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights Reserved.
********************************************************************************/
global $app_strings;
global $current_user;
?>



<script type="text/javascript" src="include/SugarFields/Fields/Collection/SugarFieldCollection.js"></script>
<script type="text/javascript" src="include/SugarFields/Fields/Teamset/Teamset.js"></script>
<script type="text/javascript" src="include/JSON.js"></script>
<script type="text/javascript">
    var collection = (typeof collection == 'undefined') ? new Array() : collection;
    if(typeof collection["EditView_team_name"] == 'undefined') {
       collection["EditView_team_name"] = new SUGAR.collection('EditView', 'team_name', 'Teams', '{literal}{"call_back_function":"set_return","form_name":"EditView","field_to_name_array":{"name":"team_name_collection_0","id":"id_team_name_collection_0"} }{/literal}');
	   	}

</script>
<input id="update_fields_team_name_collection" name="update_fields_team_name_collection" value="" type="hidden">
<input id="team_name_new_on_update" name="team_name_new_on_update" value="false" type="hidden">
<input id="team_name_allow_update" name="team_name_allow_update" value="" type="hidden">
<input id="team_name_allow_new" name="team_name_allow_new" value="true" type="hidden">
<input id="team_name" name="team_name" value="team_name" type="hidden">
<input id="team_name_field" name="team_name_field" value="team_name_table" type="hidden">


<table name="EditView_team_name_table" id="EditView_team_name_table" style="border-spacing: 0pt;">
<!-- BEGIN Labels Line -->
<tbody><tr id="lineLabel_team_name" name="lineLabel_team_name">
<td nowrap="nowrap">
<input style="margin-bottom: 4px;" class="button" value="<?php echo $app_strings['LBL_ADD_BUTTON'];?>" onclick="javascript:collection['EditView_team_name'].add2(); if(collection['EditView_team_name'].more_status)collection['EditView_team_name'].js_more();" type="button">   
<input style="margin-bottom: 4px;" class="button" value="<?php echo $app_strings['LBL_SELECT_BUTTON_LABEL'];?>" onclick='javascript:open_popup("Teams", 600, 400, "", true, false, {"call_back_function":"set_return_teams_for_editview","form_name":  "EditView","field_name":"team_name","field_to_name_array":{"id":"team_id","name":"team_name"}}, "MULTISELECT", true); if(collection["EditView_team_name"].more_status)collection["EditView_team_name"].js_more();' type="button">
</td>
<td>
&nbsp;
</td>

<td id="lineLabel_team_name_primary" rowspan="1" scope="row" style="white-space: nowrap; word-wrap: normal;" align="center">
<?php echo $app_strings['LBL_COLLECTION_PRIMARY'];?>
</td>
<!-- BEGIN Add and collapse -->
<td rowspan="1" scope="row" style="white-space: nowrap;" valign="top">
&nbsp;
<span onclick="collection['EditView_team_name'].js_more();" id="more_EditView_team_name" style="display: none; text-decoration: none;">
<input id="arrow_team_name" name="arrow_team_name" value="hide" type="hidden">
<img id="more_img_EditView_team_name" absmiddle="" alt="Hide/Show" src="index.php?entryPoint=getImage&amp;themeName=Sugar&amp;imageName=advanced_search.gif" height="8" width="8" border="0">
<span id="more_div_EditView_team_name" style="display: none;"><?php echo $app_strings['LBL_SHOW'];?></span>
</span>
</td>
<!-- END Add and collapse -->
<td width="100%">
&nbsp;
</td>

</tr>
<!-- END Labels Line -->
<tr id="lineFields_EditView_team_name_0">
<td valign="top">
<span id="EditView_team_name_input_div_0" name="teamset_div">          
<input name="team_name_collection_0" id="team_name_collection_0" class="sqsEnabled yui-ac-input" tabindex="" size="" value="" title="" autocomplete="off" type="text"><div class="yui-ac-container" id="EditView_team_name_collection_0_results"><div style="display: none;" class="yui-ac-content"><div style="display: none;" class="yui-ac-hd"></div><div class="yui-ac-bd"><ul><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li><li style="display: none;"></li></ul></div><div style="display: none;" class="yui-ac-ft"></div></div></div>
<input name="id_team_name_collection_0" id="id_team_name_collection_0" value="<?php echo $current_user->team_id; ?>" type="hidden">
</span>
</td>
<!-- BEGIN Remove and Radio -->
<td nowrap="nowrap" valign="top" align="left">
&nbsp;
<img id="remove_team_name_collection_0" name="remove_team_name_collection_0" src="themes/Sugar/images/delete_inline.gif" onclick="collection['EditView_team_name'].remove(0);">
</td>
<td valign="top" align="center">
<span id="EditView_team_name_radio_div_0">
&nbsp;

<input id="primary_team_name_collection_0" name="primary_team_name_collection" class="radio" checked="checked" value="0" onclick="collection['EditView_team_name'].changePrimary(true);" type="radio">
</span>
</td>
<td>
&nbsp;
</td>
<td>
&nbsp;
</td>
<!-- END Remove and Radio -->
</tr>
</tbody></table>
<!--
Put this button in here since we have moved the Add and Select buttons above the text fields, the accesskey will skip these. So create this button
and push it outside the screen.
-->
<input style="position: absolute; left: -9999px; width: 0px; height: 0px;" accesskey="T" halign="left" class="button" value="Select" onclick='javascript:open_popup("Teams", 600, 400, "", true, false, {"call_back_function":"set_return_teams_for_editview","form_name":  "EditView","field_name":"team_name","field_to_name_array":{"id":"team_id","name":"team_name"}}, "MULTISELECT", true); if(collection["EditView_team_name"].more_status)collection["EditView_team_name"].js_more();' type="button">




<script type="text/javascript">
	if(collection["EditView_team_name"] && collection["EditView_team_name"].secondaries_values.length == 0) {
			    collection_field = collection["EditView_team_name"];
		collection_field.add_secondaries(collection_field.secondaries_values);	
	}
</script>
<script type="text/javascript">
 	document.getElementById("id_team_name_collection_0").value = "<?php echo $current_user->team_id; ?>"; 	
 	document.getElementById("team_name_collection_0").value = "<?php echo $current_user->team_name; ?>";
        
    
	function call_js_more(c) {
	    c.js_more();
	} 
</script>
<script language="javascript">
	if(typeof sqs_objects == 'undefined'){
		var sqs_objects = new Array;
	}
	sqs_objects['EditView_team_name_collection_0'] = {
				"form":"EditView",
				"method":"query",
				"modules":["Teams"],
				"group":"or",
				"field_list":["name","id"],
				"populate_list":["team_name_collection_0","id_team_name_collection_0"],
				"required_list":["parent_id"],
				"conditions":[{"name":"name","op":"like_custom","end":"%","value":""}],
				"order":"name",
				"limit":"30",
				"no_match_text":
				"No Match",
				"primary_populate_list":[],
				"primary_field_list":[]};
	</script>



