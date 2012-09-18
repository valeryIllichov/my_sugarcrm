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
require_once('include/SugarObjects/templates/basic/Basic.php');


class Resource extends Basic {

// Stored fields
	var $id;
	var $name = '';
	var $date_entered;
	var $date_modified;
	var $assigned_user_id;
	var $modified_user_id;
	var $created_by;
	var $res_type;
	var $department;
	var $location;
	var $description;
	var $status;
	var $phone;
	
//Pro
	var $team_id;
	var $assigned_name; //team name
	var $team_name; //temporary team_name

//Scheme
	var $table_name = "resources";
	var $module_dir = 'Resources';
	var $object_name = "Resource";
	var $default_order_by = "name";
	var $new_schema = true;
	var $rel_meetings_table = "meetings_resources";
	var $rel_calls_table = "calls_resources";

//Additional fields
	var $assigned_user_name;
	var $created_by_name;
	var $modified_by_name;

//Dummy for vCal table select
	var $first_name = "";
	var $last_name = "";
	var $email1 = "";


	function Resource() {
		parent::Basic();
		global $current_user;

		global $sugar_flavor;
		if (!empty($current_user) && ($sugar_flavor == 'ENT' || $sugar_flavor == 'PRO')) {
			$this->team_id = $current_user->default_team;
		} else {
			$this->team_id = 1;
		}

	}

	function create_list_query($order_by, $where, $show_deleted=0) {

		$custom_join = $this->custom_fields->getJOIN();
		$query = "SELECT ";

		$query .= "$this->table_name.*, users.user_name assigned_user_name";
		
		global $sugar_flavor;
		if ($sugar_flavor == "ENT" || $sugar_flavor == "PRO") {
			$query .= ", teams.name team_name";
		}

		if($custom_join) {
  				$query .= $custom_join['select'];
		}
		$query .= " FROM resources ";

		if ($sugar_flavor == "ENT" || $sugar_flavor == "PRO") {
			$this->add_team_security_where_clause($query);
		}

		$query .= "		LEFT JOIN users
                                ON resources.assigned_user_id=users.id ";
		if ($sugar_flavor == "ENT" || $sugar_flavor == "PRO") {
			$query .=		"LEFT JOIN teams ON resources.team_id=teams.id ";
		}

		if($custom_join) {
			$query .= $custom_join['join'];
		}
		$where_auto = '1=1';
		if($show_deleted == 0) {
			$where_auto = " resources.deleted=0 ";

		} else if($show_deleted == 1) {
			$where_auto = " resources.deleted=1 ";
		}


		if($where != "")
			$query .= "where ($where) AND ".$where_auto;
		else
			$query .= "where ".$where_auto;

		if(!empty($order_by))
			$query .= " ORDER BY $order_by";
		return $query;
	}



/*
	function create_export_query(&$order_by, &$where) {
		$custom_join = $this->custom_fields->getJOIN();
		$query = "SELECT resources.*,
                                users.user_name assigned_user_name";
		$query .= ", teams.name team_name";

		if($custom_join) {
			$query .= $custom_join['select'];
		}
        $query .= " FROM resources ";

		// We need to confirm that the user is a member of the team of the item.
		$this->add_team_security_where_clause($query);

		$query .= "			LEFT JOIN users
                                ON resources.assigned_user_id=users.id ";
		$query .=		"LEFT JOIN teams ON resources.team_id=teams.id ";

		if($custom_join) {
			$query .= $custom_join['join'];
		}

		$where_auto = " resources.deleted=0 ";


		if($where != "")
			$query .= "where ($where) AND ".$where_auto;
		else
			$query .= "where ".$where_auto;

		if(!empty($order_by))
			$query .= " ORDER BY $order_by";

		return $query;
	}
*/

//Returns all of meetings that are related to this resoruce object
	function get_meetings()
	{
		// First, get the list of IDs.
		$query = "SELECT meeting_id as id from meetings_resources where resource_id='$this->resource_id' AND deleted=0";
		return $this->build_related_list($query, new Meeting());
	}

//Make this module available in the ACL list
	function bean_implements($interface){
		switch($interface){
			case 'ACL':return true;
		}
		return false;
	}

}
?>
