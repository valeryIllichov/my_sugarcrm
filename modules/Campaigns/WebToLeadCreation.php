<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
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
 ********************************************************************************/
/*********************************************************************************

 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
require_once('XTemplate/xtpl.php');
require_once('modules/ProspectLists/ProspectList.php');

require_once('include/utils.php');
require_once('modules/Campaigns/utils.php');
require_once('modules/Leads/Lead.php');
require_once('modules/Campaigns/Campaign.php');
require_once('modules/Campaigns/utils.php');

global $mod_strings, $app_list_strings, $app_strings, $current_user, $import_bean_map;
global $import_file_name, $theme;$app_list_strings;
$lead = new Lead();
$fields = array();

$xtpl=new XTemplate ('modules/Campaigns/WebToLeadCreation.html');
$xtpl->assign("MOD", $mod_strings);
$xtpl->assign("APP", $app_strings);
if(isset($_REQUEST['module']))
{
    $xtpl->assign("MODULE", $_REQUEST['module']);
}
if(isset($_REQUEST['return_module']))
{
    $xtpl->assign("RETURN_MODULE", $_REQUEST['return_module']);
}
if(isset($_REQUEST['return_id']))
{
    $xtpl->assign("RETURN_ID", $_REQUEST['return_id']);
}
if(isset($_REQUEST['return_id']))
{
    $xtpl->assign("RETURN_ACTION", $_REQUEST['return_action']);
}
if(isset($_REQUEST['record']))
{
    $xtpl->assign("RECORD", $_REQUEST['record']);
}

global $theme;
global $currentModule;
$theme_path = "themes/".$theme."/";
$image_path = $theme_path."images/";

$xtpl->assign("TITLE1",get_webtolead_title($mod_strings['LBL_MODULE_NAME'],$mod_strings['LBL_CREATE_WEB_TO_LEAD_FORM'], $mod_strings['LBL_WEB_TO_LEAD_FORM_TITLE1'], true));

$xtpl->assign("TITLE2",get_webtolead_title($mod_strings['LBL_MODULE_NAME'], $mod_strings['LBL_CREATE_WEB_TO_LEAD_FORM'],$mod_strings['LBL_WEB_TO_LEAD_FORM_TITLE2'], true));

$site_url = $sugar_config['site_url'];
$web_post_url = $site_url.'/index.php?entryPoint=WebToLeadCapture';
$json = getJSONobj();
// Users Popup
$popup_request_data = array(
	'call_back_function' => 'set_return',
	'form_name' => 'WebToLeadCreation',
	'field_to_name_array' => array(
		'id' => 'assigned_user_id',
		'user_name' => 'assigned_user_name',
		),
	);
$xtpl->assign('encoded_users_popup_request_data', $json->encode($popup_request_data));

//Campaigns popup
$popup_request_data = array(
		'call_back_function' => 'set_return',
		'form_name' => 'WebToLeadCreation',
		'field_to_name_array' => array(
			'id' => 'campaign_id',
			'name' => 'campaign_name',
		),
);
$encoded_users_popup_request_data = $json->encode($popup_request_data);
$xtpl->assign('encoded_campaigns_popup_request_data' , $json->encode($popup_request_data));












//create the cancel button
$cancel_buttons_html = "<input class='button' onclick=\"this.form.action.value='".$_REQUEST['return_action']."'; this.form.module.value='".$_REQUEST['return_module']."';\" type='submit' name='cancel' value='".$app_strings['LBL_BACK']."'/>";
$xtpl->assign("CANCEL_BUTTON", $cancel_buttons_html );

$field_defs_js = "var field_defs = {'Contacts':[";

$count= 0;
foreach($lead->field_defs as $field_def)
{
	$email_fields = false;
    if($field_def['name']== 'webtolead_email1' || $field_def['name']== 'webtolead_email2')
    {
    	$email_fields = true;
    }
	  if($field_def['name']!= 'account_name'){
	    if( ( $field_def['type'] == 'relate' && empty($field_def['custom_type']) )
	    	|| $field_def['type'] == 'assigned_user_name' || $field_def['type'] =='link'
	    	|| (isset($field_def['source'])  && $field_def['source']=='non-db' && !$email_fields) || $field_def['type'] == 'id')
	    {
	        continue;
	    }
	   }
	    if($field_def['name']== 'deleted' || $field_def['name']=='converted' || $field_def['name']=='date_entered'
	        || $field_def['name']== 'date_modified' || $field_def['name']=='modified_user_id'
	        || $field_def['name']=='assigned_user_id' || $field_def['name']=='created_by'
	        || $field_def['name']=='team_id')
	    {
	    	continue;
	    }


    $field_def['vname'] = preg_replace('/:$/','',translate($field_def['vname'],'Leads'));

     //$cols_name = "{'".$field_def['vname']."'}";
     $col_arr = array();
     if((isset($field_def['required']) && $field_def['required'] != null && $field_def['required'] != 0)
     	|| $field_def['name']=='last_name'
     	){
        $cols_name=$field_def['vname'].' '.$app_strings['LBL_REQUIRED_SYMBOL'];
        $col_arr[0]=$cols_name;
        $col_arr[1]=$field_def['name'];
        $col_arr[2]=true;
     }
     else{
	     $cols_name=$field_def['vname'];
	     $col_arr[0]=$cols_name;
	     $col_arr[1]=$field_def['name'];
     }
     if (! in_array($cols_name, $fields))
     {
         array_push($fields,$col_arr);
     }
     $count++;
}

$xtpl->assign("WEB_POST_URL",$web_post_url);
//$xtpl->assign("LEAD_SELECT_FIELDS",'MOD.LBL_SELECT_LEAD_FIELDS');

require_once('include/QuickSearchDefaults.php');
$qsd = new QuickSearchDefaults();
$sqs_objects = array('account_name' => $qsd->getQSParent(),
					'assigned_user_name' => $qsd->getQSUser(),
					'campaign_name' => $qsd->getQSCampaigns(),




					);

$quicksearch_js = '<script type="text/javascript" language="javascript">sqs_objects = ' . $json->encode($sqs_objects) . '</script>';
$xtpl->assign("JAVASCRIPT", $quicksearch_js);

















if (empty($focus->assigned_user_id) && empty($focus->id))  $focus->assigned_user_id = $current_user->id;
if (empty($focus->assigned_name) && empty($focus->id))  $focus->assigned_user_name = $current_user->user_name;
$xtpl->assign("ASSIGNED_USER_OPTIONS", get_select_options_with_id(get_user_array(TRUE, "Active", $focus->assigned_user_id), $focus->assigned_user_id));
$xtpl->assign("ASSIGNED_USER_NAME", $focus->assigned_user_name);
$xtpl->assign("ASSIGNED_USER_ID", $focus->assigned_user_id );

$xtpl->assign("REDIRECT_URL_DEFAULT",'http://');

//required fields on Webtolead form
$campaign= new Campaign();
require_once('include/javascript/javascript.php');
$javascript = new javascript();
$javascript->setFormName('WebToLeadCreation');
$javascript->setSugarBean($lead);
$javascript->addAllFields('');
//$javascript->addFieldGeneric('redirect_url', '', 'LBL_REDIRECT_URL' ,'true');
$javascript->addFieldGeneric('campaign_name', '', 'LBL_RELATED_CAMPAIGN' ,'true');
$javascript->addFieldGeneric('assigned_user_name', '', 'LBL_ASSIGNED_TO' ,'true');
$javascript->addToValidateBinaryDependency('campaign_name', 'alpha', $app_strings['ERR_SQS_NO_MATCH_FIELD'] . $mod_strings['LBL_LEAD_NOTIFY_CAMPAIGN'], 'false', '', 'campaign_id');
$javascript->addToValidateBinaryDependency('assigned_user_name', 'alpha', $app_strings['ERR_SQS_NO_MATCH_FIELD'] . $app_strings['LBL_ASSIGNED_TO'], 'false', '', 'assigned_user_id');




echo $javascript->getScript();

$json = getJSONobj();
$lead_fields = $json->encode($fields);
$xtpl->assign("LEAD_FIELDS",$lead_fields);
$classname = "SUGAR_GRID";
$xtpl->assign("CLASSNAME",$classname);
$xtpl->assign("DRAG_DROP_CHOOSER_WEB_TO_LEAD",constructDDWebToLeadFields($fields,$classname));

$xtpl->parse("main");
$xtpl->out("main");
/*
$str = "<script>
WebToLeadForm.lead_fields = {$lead_fields};
</script>";
echo $str;
*/
/*
 *This function constructs Drag and Drop multiselect box of subscriptions for display in manage subscription form
*/
function constructDDWebToLeadFields($fields,$classname){
require_once("include/templates/TemplateDragDropChooser.php");
global $mod_strings;
$d2 = array();
    //now call function that creates javascript for invoking DDChooser object
    $dd_chooser = new TemplateDragDropChooser();
    $dd_chooser->args['classname']  = $classname;
    $dd_chooser->args['left_header']  = $mod_strings['LBL_AVALAIBLE_FIELDS_HEADER'];
    $dd_chooser->args['mid_header']   = $mod_strings['LBL_LEAD_FORM_FIRST_HEADER'];
    $dd_chooser->args['right_header'] = $mod_strings['LBL_LEAD_FORM_SECOND_HEADER'];
    $dd_chooser->args['left_data']    = $fields;
    $dd_chooser->args['mid_data']     = $d2;
    $dd_chooser->args['right_data']   = $d2;
    $dd_chooser->args['title']        =  ' ';
    $dd_chooser->args['left_div_name']      = 'ddgrid2';
    $dd_chooser->args['mid_div_name']       = 'ddgrid3';
    $dd_chooser->args['right_div_name']     = 'ddgrid4';
    $dd_chooser->args['gridcount'] = 'three';
    $str = $dd_chooser->displayScriptTags();
    $str .= $dd_chooser->displayDefinitionScript();
    $str .= $dd_chooser->display();
    $str .= "<script>
	           //function post rows
	           function postMoveRows(){
			    	//Call other function when this is called
	           }
	        </script>";
	$str .= "<script>
		       function displayAddRemoveDragButtons(Add_All_Fields,Remove_All_Fields){
				    var addRemove = document.getElementById('lead_add_remove_button');
				    if(" . $dd_chooser->args['classname'] . "_grid0.getDataModel().getTotalRowCount() ==0) {
				     addRemove.setAttribute('value',Remove_All_Fields);
				     addRemove.setAttribute('title',Remove_All_Fields);
				    }
				    else if(" . $dd_chooser->args['classname'] . "_grid1.getDataModel().getTotalRowCount() ==0 && " . $dd_chooser->args['classname'] . "_grid2.getDataModel().getTotalRowCount() ==0){
				     addRemove.setAttribute('value',Add_All_Fields);
				     addRemove.setAttribute('title',Add_All_Fields);
				    }
              }
            </script>";

    return $str;
}

//function to retrieve webtolead image and title. path to help file
function get_webtolead_title ($module,$image_name, $module_title, $show_help)
{
	global $sugar_version, $sugar_flavor, $server_unique_key, $current_language, $action;
	global $image_path;
	global $app_strings;

	$the_title = "<table width='100%' cellpadding='0' cellspacing='0' border='0' class='moduleTitle'><tr><td valign='top'>\n";
	$module = preg_replace("/ /","",$module);
	if (is_file($image_path.$image_name.".gif")) {
		$the_title .= "<IMG src='".$image_path.$image_name.".gif' width='16' height='16' border='0' style='margin-top: 3px;' alt='".$image_name."'>&nbsp;</td><td width='100%'>";
	}
	$the_title .= "<h2>".$module_title."</h2></td>\n";



	if ($show_help) {

			$the_title .= "<td valign='top' align='right' nowrap style='padding-top:3px; padding-left: 5px;'>";
			if ($_REQUEST['action'] != "EditView") {
				$the_title .= "<A href='index.php?".$GLOBALS['request_string']."' class='utilsLink'><img src='".$image_path."print.gif' width='13' height='13' alt='".$app_strings['LNK_PRINT']."' border='0' align='absmiddle'></a>&nbsp;<A href='index.php?".$GLOBALS['request_string']."' class='utilsLink'>".$app_strings['LNK_PRINT']."</A>\n";
			}
		    $the_title .= "&nbsp;<A href=\"javascript:void window.open('index.php?module=Administration&action=SupportPortal&view=documentation&version=".$sugar_version."&edition=".$sugar_flavor."&lang=".$current_language."&help_module=".$module."&help_action=".$action."&key=".$server_unique_key."','helpwin','width=600,height=600,status=0,resizable=1,scrollbars=1,toolbar=0,location=1')\"  class='utilsLink'>" .
		    			  "<img src='".$image_path."help.gif' width='13' height='13' alt='".$app_strings['LNK_HELP']."' border='0' align='absmiddle'></a>";
	    	$the_title .= "&nbsp;<A href=\"javascript:void window.open('index.php?module=Administration&action=SupportPortal&view=documentation&version=".$sugar_version."&edition=".$sugar_flavor."&lang=".$current_language."&help_module=".$module."&help_action=".$action."&key=".$server_unique_key."','helpwin','width=600,height=600,status=0,resizable=1,scrollbars=1,toolbar=0,location=1');\" class='utilsLink'>"
	    				  .$app_strings['LNK_HELP'].
						  "</A></td>\n";
		}



	$the_title .= "</tr></table>\n";

	return $the_title;

}
?>
