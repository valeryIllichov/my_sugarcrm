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
require_once('include/formbase.php');
require_once('modules/Campaigns/Campaign.php');





$focus = new Campaign();

$focus->retrieve($_POST['record']);
if(!$focus->ACLAccess('Save')){
	ACLController::displayNoAccess(true);
	sugar_cleanup(true);
}
if (!empty($_POST['assigned_user_id']) && ($focus->assigned_user_id != $_POST['assigned_user_id']) && ($_POST['assigned_user_id'] != $current_user->id)) {
	$check_notify = TRUE;
}
else {
	$check_notify = FALSE;
}

foreach($focus->column_fields as $field)
{
	if(isset($_POST[$field]))
	{
		if(is_array($_POST[$field]) && !empty($focus->field_defs[$field]['isMultiSelect'])) {
			if(!isset($_POST[$field][0])) {
				unset($_POST[$field][0]);
			}
			if(isset($_POST[$field][0])) {
				$_POST[$field] = implode('^,^', $_POST[$field]);	
			} else {
			    continue;
			}
		}
		$value = $_POST[$field];
		$focus->$field = $value;

	}
}

foreach($focus->additional_column_fields as $field)
{
	if(isset($_POST[$field]))
	{
		$value = $_POST[$field];
		$focus->$field = $value;

	}
}


//store preformatted dates for 2nd save
$preformat_start_date = $focus->start_date;
$preformat_end_date = $focus->end_date;

$focus->save($check_notify);
$return_id = $focus->id;

$GLOBALS['log']->debug("Saved record with id of ".$return_id);


//if type is set to newsletter then make sure there are propsect lists attached
if($focus->campaign_type =='NewsLetter'){
        //add preformatted dates for 2nd save, to avoid formatting conversion errors
        $focus->start_date = $preformat_start_date ;
        $focus->end_date = $preformat_end_date ;

        $focus->load_relationship('prospectlists');
        $target_lists = $focus->prospectlists->get();
        if(count($target_lists)<1){
            global $current_user;
            global $mod_strings;
            //if no prospect lists are attached, then lets create a subscription and unsubscription
            //default prospect lists as these are required for newsletters.
             require_once('modules/ProspectLists/ProspectList.php');       
             //create subscription list     
             $subs = new ProspectList();
             $subs->name = $focus->name.' '.$mod_strings['LBL_SUBSCRIPTION_LIST'];
             $subs->assigned_user_id= $current_user->id;
             $subs->list_type = "default";
             $subs->save();
             $focus->prospectlists->add($subs->id);

             //create unsubscription list
             $unsubs = new ProspectList();
             $unsubs->name = $focus->name.' '.$mod_strings['LBL_UNSUBSCRIPTION_LIST'];
             $unsubs->assigned_user_id= $current_user->id;
             $unsubs->list_type = "exempt";
             $unsubs->save();
             $focus->prospectlists->add($unsubs->id);
             
             //create unsubscription list
             $test_subs = new ProspectList();
             $test_subs->name = $focus->name.' '.$mod_strings['LBL_TEST_LIST'];
             $test_subs->assigned_user_id= $current_user->id;
             $test_subs->list_type = "test";
             $test_subs->save();
             $focus->prospectlists->add($test_subs->id);             
        }
        //save new relationships
        $focus->save();

}//finish newsletter processing

handleRedirect($focus->id, 'Campaigns');


?>
