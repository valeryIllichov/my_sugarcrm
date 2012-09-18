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
global $app_strings, $json;
$json = getJSONobj();

require_once("include/json_config.php");
class json_config_cal2 extends json_config {

	function meeting_retrieve($module, $record) {
		global $json, $response;
		global $beanFiles, $beanList;
		require_once($beanFiles[$beanList[$module]]);
		$focus = new $beanList[$module];
		
		if(empty($module) || empty($record)) {
			return '';
		}
		
		$focus->retrieve($record);
		$module_arr = $this->populateBean($focus);
		
		if($module == 'Meetings') {
			$users = $focus->get_meeting_users();
		} 
		else if ( $module == 'Calls') {
			$users = $focus->get_call_users();
			
		}

		$focus->load_relationships('resources');
		$resources = $focus->get_linked_beans('resources','Resource');
		
		$module_arr['users_arr'] = array();
		
		foreach($users as $user) {
			array_push($module_arr['users_arr'],  $this->populateBean($user));
		}
		
		$module_arr['orig_users_arr_hash'] = array();
		
		foreach($users as $user) {
			$module_arr['orig_users_arr_hash'][$user->id] = '1';
		}
		
		$module_arr['contacts_arr'] = array();
		
		$focus->load_relationships('contacts');
		$contacts=$focus->get_linked_beans('contacts','Contact');
		foreach($contacts as $contact) {
			array_push($module_arr['users_arr'], $this->populateBean($contact));
	  	}

        $module_arr['leads_arr'] = array();
		
		$focus->load_relationships('leads');
		$leads=$focus->get_linked_beans('leads','Lead');
		foreach($leads as $lead) {
			array_push($module_arr['users_arr'], $this->populateBean($lead));
	  	}
	
		$module_arr['resources_arr'] = array();
		
		foreach($resources as $resource) {
			array_push($module_arr['users_arr'],  $this->populateBean($resource));
		}
		return $module_arr;
	}
}
?>
