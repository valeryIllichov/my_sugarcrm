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

 * Description:  Defines the Account SugarBean Account entity with the necessary
 * methods and variables.
 ********************************************************************************/

require_once('data/SugarBean.php');
require_once('include/SugarEmailAddress/SugarEmailAddress.php');
require_once('modules/Contacts/Contact.php');
require_once('modules/Opportunities/Opportunity.php');
require_once('modules/Cases/Case.php');
require_once('modules/Calls/Call.php');
require_once('modules/Notes/Note.php');
require_once('modules/Emails/Email.php');
require_once('modules/Bugs/Bug.php');

// Account is used to store account information.
class Account extends SugarBean {
	var $field_name_map = array();
	// Stored fields
	var $date_entered;
	var $date_modified;
	var $modified_user_id;
	var $assigned_user_id;
	var $annual_revenue;
	var $billing_address_street;
	var $billing_address_city;
	var $billing_address_state;
	var $billing_address_country;
	var $billing_address_postalcode;

    var $billing_address_street_2;
    var $billing_address_street_3;
    var $billing_address_street_4;
    
	var $description;
	var $email1;
	var $email2;
	var $email_opt_out;
	var $invalid_email;
	var $employees;
	var $id;
	var $industry;
	var $name;
	var $ownership;
	var $parent_id;
	var $phone_alternate;
	var $phone_fax;
	var $phone_office;
	var $rating;
	var $shipping_address_street;
	var $shipping_address_city;
	var $shipping_address_state;
	var $shipping_address_country;
	var $shipping_address_postalcode;
    
    var $shipping_address_street_2;    
    var $shipping_address_street_3;    
    var $shipping_address_street_4;    
    
	var $sic_code;
	var $ticker_symbol;
	var $account_type;
	var $website;
	var $custom_fields;

	var $created_by;
	var $created_by_name;
	var $modified_by_name;

	// These are for related fields
	var $opportunity_id;
	var $case_id;
	var $contact_id;
	var $task_id;
	var $note_id;
	var $meeting_id;
	var $call_id;
	var $email_id;
	var $member_id;
	var $parent_name;
	var $assigned_user_name;
	var $account_id = '';
	var $account_name = '';
	var $bug_id ='';
	var $module_dir = 'Accounts';
	var $emailAddress;	









	var $table_name = "accounts";
	var $object_name = "Account";
	var $importable = true;
	var $new_schema = true;
	// This is used to retrieve related fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'assigned_user_id', 'opportunity_id', 'bug_id', 'case_id', 'contact_id', 'task_id', 'note_id', 'meeting_id', 'call_id', 'email_id', 'parent_name', 'member_id'



	);
	var $relationship_fields = Array('opportunity_id'=>'opportunities', 'bug_id' => 'bugs', 'case_id'=>'cases', 
									'contact_id'=>'contacts', 'task_id'=>'tasks', 'note_id'=>'notes',
									'meeting_id'=>'meetings', 'call_id'=>'calls', 'email_id'=>'emails','member_id'=>'members',



									'project_id'=>'project',
									);

    //Meta-Data Framework fields
    var $push_billing;
    var $push_shipping;
    
	function Account() {
        parent::SugarBean();
        
        $this->setupCustomFields('Accounts');
        
		foreach ($this->field_defs as $field) {
			$this->field_name_map[$field['name']] = $field;
		}









		$this->emailAddress = new SugarEmailAddress();
		//Combine the email logic original here with bug #26450.
		if( (!empty($_REQUEST['parent_id']) && !empty($_REQUEST['parent_type']) && $_REQUEST['parent_type'] == 'Emails'
        	&& !empty($_REQUEST['return_module']) && $_REQUEST['return_module'] == 'Emails' ) 
        	||
        	(!empty($_REQUEST['parent_type']) && $_REQUEST['parent_type'] != 'Accounts' &&
        	!empty($_REQUEST['return_module']) && $_REQUEST['return_module'] != 'Accounts') ){
			$_REQUEST['parent_name'] = '';
			$_REQUEST['parent_id'] = '';	
		}
	}
	
	/**
	 * Does a bit of overloading on save()
	 */
	function save($check_notify=false) {
        $this->add_address_streets('billing_address_street');
        $this->add_address_streets('shipping_address_street');
        $ori_in_workflow = empty($this->in_workflow) ? false : true;
        $this->emailAddress->handleLegacySave($this, $this->module_dir);
        parent::save($check_notify);
        $override_email = array();
        if(!empty($this->email1_set_in_workflow)) {
            $override_email['emailAddress0'] = $this->email1_set_in_workflow;
        }
        if(!empty($this->email2_set_in_workflow)) {
            $override_email['emailAddress1'] = $this->email2_set_in_workflow;
        }
        if(!isset($this->in_workflow)) {
            $this->in_workflow = false;
        }
        if($ori_in_workflow === false || !empty($override_email)){
            $this->emailAddress->save($this->id, $this->module_dir, $override_email,'','','','',$this->in_workflow);
        }
		return $this;
	}

	function get_summary_text()
	{
		return $this->name;
	}










































	function get_contacts() {
		return $this->get_linked_beans('contacts','Contact');
	}
	


	function clear_account_case_relationship($account_id='', $case_id='')
	{
		if (empty($case_id)) $where = '';
		else $where = " and id = '$case_id'";
		$query = "UPDATE cases SET account_name = '', account_id = '' WHERE account_id = '$account_id' AND deleted = 0 " . $where;
		$this->db->query($query,true,"Error clearing account to case relationship: ");
	}

	// This method is used to provide backward compatibility with old data that was prefixed with http://
	// We now automatically prefix http://
	function remove_redundant_http()
	{
		if(eregi("http://", $this->website))
		{
			$this->website = substr($this->website, 7);
		}
	}

	function fill_in_additional_list_fields()
	{
		parent::fill_in_additional_list_fields();
	// Fill in the assigned_user_name
	//	$this->assigned_user_name = get_assigned_user_name($this->assigned_user_id);







		$this->remove_redundant_http();
	}

	function fill_in_additional_detail_fields()
	{
        parent::fill_in_additional_detail_fields();
		$query = "SELECT a1.name from accounts a1, accounts a2 where a1.id = a2.parent_id and a2.id = '$this->id' and a1.deleted=0";
		$result = $this->db->query($query,true," Error filling in additional detail fields: ");

		// Get the id and the name.
		$row = $this->db->fetchByAssoc($result);

		if($row != null)
		{
			$this->parent_name = $row['name'];
		}
		else
		{
			$this->parent_name = '';
		}
		$this->remove_redundant_http();		
	}
	
	function get_list_view_data(){
		global $system_config;
		$temp_array = $this->get_list_view_array();
		$temp_array["ENCODED_NAME"]=$this->name;
//		$temp_array["ENCODED_NAME"]=htmlspecialchars($this->name, ENT_QUOTES);
		if(!empty($this->billing_address_state))
		{
			$temp_array["CITY"] = $this->billing_address_city . ', '. $this->billing_address_state;
		}
		else
		{
			$temp_array["CITY"] = $this->billing_address_city;
		}
		$temp_array["BILLING_ADDRESS_STREET"]  = preg_replace("/[\r]/",'',$this->billing_address_street);
		$temp_array["SHIPPING_ADDRESS_STREET"] = preg_replace("/[\r]/",'',$this->shipping_address_street);
		$temp_array["BILLING_ADDRESS_STREET"]  = preg_replace("/[\n]/",'\n',$temp_array["BILLING_ADDRESS_STREET"] );
		$temp_array["SHIPPING_ADDRESS_STREET"] = preg_replace("/[\n]/",'\n',$temp_array["SHIPPING_ADDRESS_STREET"] );
    	if(isset($system_config->settings['system_skypeout_on']) && $system_config->settings['system_skypeout_on'] == 1){
    	if(!empty($temp_array['PHONE_OFFICE']) && skype_formatted($temp_array['PHONE_OFFICE'])){
    		$temp_array['PHONE_OFFICE'] = '<a href="callto://' . $temp_array['PHONE_OFFICE']. '">'.$temp_array['PHONE_OFFICE']. '</a>' ;
    	}}
		return $temp_array;
	}
	/**
		builds a generic search based on the query string using or
		do not include any $this-> because this is called on without having the class instantiated
	*/
	function build_generic_where_clause ($the_query_string) {
	$where_clauses = Array();
	$the_query_string = $this->db->quote($the_query_string);
	array_push($where_clauses, "accounts.name like '$the_query_string%'");
	if (is_numeric($the_query_string)) {
		array_push($where_clauses, "accounts.phone_alternate like '%$the_query_string%'");
		array_push($where_clauses, "accounts.phone_fax like '%$the_query_string%'");
		array_push($where_clauses, "accounts.phone_office like '%$the_query_string%'");
	}

	$the_where = "";
	foreach($where_clauses as $clause)
	{
		if(!empty($the_where)) $the_where .= " or ";
		$the_where .= $clause;
	}

	return $the_where;
}


        function create_export_query(&$order_by, &$where, $relate_link_join='')
        {
        	$custom_join = $this->custom_fields->getJOIN(true, true,$where);
			if($custom_join)
				$custom_join['join'] .= $relate_link_join;
                         $query = "SELECT
                                accounts.*,email_addresses.email_address email1,
                                accounts.name as account_name,
                                users.user_name as assigned_user_name ";



						if($custom_join){
   							$query .= $custom_join['select'];
 						}
						 $query .= " FROM accounts ";




                         $query .= "LEFT JOIN users
	                                ON accounts.assigned_user_id=users.id ";



				
						//join email address table too.
						$query .=  ' LEFT JOIN  email_addr_bean_rel on accounts.id = email_addr_bean_rel.bean_id and email_addr_bean_rel.bean_module=\'Accounts\' and email_addr_bean_rel.deleted=0 and email_addr_bean_rel.primary_address=1 ';
						$query .=  ' LEFT JOIN email_addresses on email_addresses.id = email_addr_bean_rel.email_address_id ' ;
						
						if($custom_join){
  							$query .= $custom_join['join'];
						}

		        $where_auto = "( accounts.deleted IS NULL OR accounts.deleted=0 )";
 
                if($where != "")
                        $query .= "where ($where) AND ".$where_auto;
                else
                        $query .= "where ".$where_auto;

                if(!empty($order_by))
                        $query .=  " ORDER BY ". $this->process_order_by($order_by, null);

                return $query;
        }


        function create_list_query($order_by, $where, $show_deleted= 0)
        {

			$custom_join = $this->custom_fields->getJOIN();

                $query = "SELECT ";

                $query .= "
                    users.user_name assigned_user_name,
                    accounts.*";
                 if($custom_join){
					$query .=  $custom_join['select'];
				}



             $query .= " FROM  accounts ";
			




			 $query .= "LEFT JOIN users
                    	ON accounts.assigned_user_id=users.id ";
             if($custom_join){
					$query .=  $custom_join['join'];
				}



     		$where_auto = '1=1';
			if($show_deleted == 0){
            	$where_auto = " accounts.deleted=0 ";
			}else if($show_deleted == 1){
				$where_auto = " accounts.deleted=1 ";	
			}

            if($where != "")
                    $query .= "where ($where) AND ".$where_auto;
            else
                    $query .= "where ".$where_auto;

        if($order_by != "")
			$query .= " ORDER BY $order_by";
		else
			$query .= " ORDER BY $this->table_name.name";
                    return $query;
        }

	function set_notification_body($xtpl, $account)
	{
		$xtpl->assign("ACCOUNT_NAME", $account->name);
		$xtpl->assign("ACCOUNT_TYPE", $account->account_type);
		$xtpl->assign("ACCOUNT_DESCRIPTION", $account->description);

		return $xtpl;
	}
	
	function bean_implements($interface){
		switch($interface){
			case 'ACL':return true;
		}
		return false;
	}

	function retrieve($id = -1, $encode=true) {
		$ret_val = parent::retrieve($id, $encode);
		$this->emailAddress->handleLegacyRetrieve($this);
		return $ret_val;
	}
	function get_unlinked_email_query($type=array()) {
		require_once('include/utils.php');
		return get_unlinked_email_query($type, $this);
	}

}
