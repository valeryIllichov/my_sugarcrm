<?php
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
/**
 *Calendar2ViewAjaxGetGrArr
 * 
 * */
 
require_once('include/MVC/View/SugarView.php');

class Calendar2ViewAjaxGetGRArr extends SugarView {
	
 	function Calendar2ViewAjaxGetGRArr(){
 		parent::SugarView();
 	}
 	
 	function process() {
		$this->display();
 	}

 	function display() {
		$users_arr = array();
		require_once("modules/Users/User.php");
		require_once("modules/Resources/Resource.php");
				
		$user_ids = explode(",", trim($_REQUEST['users'],','));
		$resource_ids = explode(",",trim($_REQUEST['resources'],','));
		
		$user_ids = array_unique($user_ids);
		$resource_ids = array_unique($resource_ids);
		
		require_once('include/json_config_cal2.php');
		global $json;
        $json = getJSONobj();
        $json_config_cal2 = new json_config_cal2();        
       
        foreach($user_ids as $u_id){
        	if(empty($u_id))
        		continue;
        	$bean = new User();
        	$bean->retrieve($u_id);
        	array_push($users_arr, $json_config_cal2->populateBean($bean));        	
        }
        foreach($resource_ids as $r_id){
        	if(empty($r_id))
        		continue;
        	$bean = new Resource();
        	$bean->retrieve($r_id);
        	array_push($users_arr, $json_config_cal2->populateBean($bean));        	
        }
        
        $GRjavascript = "\n" . $json_config_cal2->global_registry_var_name."['focus'].users_arr = " . $json->encode($users_arr) . ";\n";       	
        	
        echo $GRjavascript;

	}
}
?>
