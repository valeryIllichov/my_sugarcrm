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
 *Calendar2ViewsearchContacts
 * 
 * */
 
require_once('include/MVC/View/SugarView.php');
require_once('modules/Calendar2/Calendar2.php');

class Calendar2ViewAjaxSearchContacts extends SugarView {

 	function Calendar2ViewAjaxSearchContacts(){
 		parent::SugarView();

 	}
 	
 	function process(){
		$this->display();
 	}

 	function display(){
		require_once("modules/Contacts/Contact.php");
		$bean = new Contact();
		
		global $current_user;

		$where = "";
		if(!empty($_REQUEST['first_name']))
			$where .= " AND contacts.first_name LIKE '".addslashes($_REQUEST['first_name'])."' ";
		if(!empty($_REQUEST['last_name']))
			$where .= " AND contacts.last_name LIKE '".addslashes($_REQUEST['last_name'])."' ";
		if(!empty($_REQUEST['account_name']))
			$where .= " AND a.name LIKE '".addslashes($_REQUEST['account_name'])."' ";
		if(!empty($_REQUEST['current_user_only']) && $_REQUEST['current_user_only'] == 'true' && !is_admin($current_user))
			$where .= " AND contacts.assigned_user_id = '".$current_user->id."' ";

		$qu = "
			SELECT contacts.id c_id, IFNULL(contacts.first_name,'') c_first_name, IFNULL(contacts.last_name,'') c_last_name, IFNULL(contacts.salutation,'') c_salutation, IFNULL(contacts.primary_address_state,'') c_primary_address_state, IFNULL(contacts.primary_address_city,'') c_primary_address_city, IFNULL(a.name,'') a_name, IFNULL(a.id,'') a_id
			FROM contacts ";

    	if(isset($bean->disable_row_level_security) && !$bean->disable_row_level_security) $bean->add_team_security_where_clause($qu);

		$qu .= "
			LEFT JOIN accounts_contacts ac ON ac.contact_id = contacts.id AND ac.deleted = 0
			LEFT JOIN accounts a ON ac.account_id = a.id AND a.deleted = 0
			WHERE contacts.deleted = 0 
		";

		$qu .= $where;
		
		$total_count = $bean->_get_num_rows_in_query($qu);
		
		$qu .= " ORDER BY contacts.last_name";
		$offset = intval($_REQUEST['offset']);
		$qu .= " LIMIT ".$offset.",5 ";
		
		$re = $bean->db->query($qu);
		
		$con_arr = array();		
	
		$count = 0;
		global $locale;
		while($ro = $bean->db->fetchByAssoc($re)){
			$ro['c_full_name'] = $locale->getLocaleFormattedName($ro['c_first_name'], $ro['c_last_name'], $ro['c_salutation'], "");
			$con_arr[] = $ro;
			$count++;		
		}
				
		$res = array(
			'contacts' => $con_arr,
			'total_count' => $total_count,
			'count' => $count,
			'offset' => $offset,			
		);
		
		echo json_encode($res);
			
	}
	
}
?>
