<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');
/**
 * MassUpdate for ListViews
 *
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
 */
/**
 * MassUpdate class for updating multiple records at once
 */
require_once('include/EditView/EditView2.php');
require_once('modules/ZuckerReportParameter/fmp.class.param.slsm.php');
require_once('modules/ZuckerReportParameter/fmp.class.param.regloc.php');
//REQUIRED REFACTORING AND CLEANING
class QuickInputClass {
    /*
     * internal sugarbean reference
     */

    var $sugarbean = null;

    /**
     * set the sugar bean to its internal member
     * @param sugar bean reference
     */
    function setSugarBean($sugar) {
        $this->sugarbean = $sugar;
    }

    /**
     * get the massupdate form
     * @param bool boolean need to execute the massupdate form or not
     * @param multi_select_popup booleanif it is a multi-select value
     */
    function getDisplayMassUpdateForm($bool, $multi_select_popup = false) {

        require_once('include/formbase.php');

        if (!$multi_select_popup)
            $form = '<form action="index.php" method="post" name="displayMassUpdate" id="displayMassUpdate">' . "\n";
        else
            $form = '<form action="index.php" method="post" name="MassUpdate">' . "\n";

        if ($bool) {
            $form .= '<input type="hidden" name="mu" value="false" />' . "\n";
        } else {
            $form .= '<input type="hidden" name="mu" value="true" />' . "\n";
        }

        $form .= getAnyToForm('mu');
        if (!$multi_select_popup)
            $form .= "</form>\n";

        return $form;
    }

    /**
     * returns the mass update's html form header
     * @param multi_select_popup boolean if it is a mult-select or not
     */
    function getMassUpdateFormHeader($multi_select_popup = false) {
        global $sugar_version;
        global $sugar_config;
        global $current_user;

        $query = base64_encode(serialize($_REQUEST));

        $bean = loadBean($_REQUEST['module']);
        $order_by_name = $bean->module_dir . '2_' . strtoupper($bean->object_name) . '_ORDER_BY';
        $lvso = isset($_REQUEST['lvso']) ? $_REQUEST['lvso'] : "";
        $request_order_by_name = isset($_REQUEST[$order_by_name]) ? $_REQUEST[$order_by_name] : "";
        if ($multi_select_popup)
            $tempString = '';
        else
            $tempString = "<form action='index.php' method='post' name='MassUpdate'  id='MassUpdate' onsubmit=\"return check_form('MassUpdate');\">\n"
                    . "<input type='hidden' name='return_action' value='{$_REQUEST['action']}' />\n"
                    . "<input type='hidden' name='return_module' value='{$_REQUEST['module']}' />\n"
                    . "<input type='hidden' name='massupdate' value='true' />\n"
                    . "<input type='hidden' name='delete' value='false' />\n"
                    . "<input type='hidden' name='merge' value='false' />\n"
                    . "<input type='hidden' name='current_query_by_page' value='{$query}' />\n"
                    . "<input type='hidden' name='module' value='{$_REQUEST['module']}' />\n"
                    . "<input type='hidden' name='action' value='MassUpdate' />\n"
                    . "<input type='hidden' name='lvso' value='{$lvso}' />\n"
                    . "<input type='hidden' name='{$order_by_name}' value='{$request_order_by_name}' />\n";

        // cn: bug 9103 - MU navigation in emails is broken
        if ($_REQUEST['module'] == 'Emails') {
            $type = "";
            // determine "type" - inbound, archive, etc.
            if (isset($_REQUEST['type'])) {
                $type = $_REQUEST['type'];
            }
            // determine owner
            $tempString .=<<<eoq
				<input type='hidden' name='type' value="{$type}" />
				<input type='hidden' name='ie_assigned_user_id' value="{$current_user->id}" />
eoq;
        }

        return $tempString;
    }

    /**
     * Executes the massupdate form
     * @param displayname Name to display in the popup window
     * @param varname name of the variable
     */
    function handleMassUpdate() {

        require_once('include/formbase.php');
        global $current_user, $db;

        /*
          C.L. - Commented this out... not sure why it's here
          if(!is_array($this->sugarbean) && $this->sugarbean->bean_implements('ACL') && !ACLController::checkAccess($this->sugarbean->module_dir, 'edit', true))
          {

          }
         */

        foreach ($_POST as $post => $value) {
            if (empty($value)) {
                unset($_POST[$post]);
            }
            if (is_string($value)
                    && isset($this->sugarbean->field_defs[$post]) &&
                    ($this->sugarbean->field_defs[$post]['type'] == 'bool'
                    || (!empty($this->sugarbean->field_defs[$post]['custom_type']) && $this->sugarbean->field_defs[$post]['custom_type'] == 'bool'
                    ))) {


                if (strcmp($value, '2') == 0)
                    $_POST[$post] = 0;
                if (!empty($this->sugarbean->field_defs[$post]['dbType']) && strcmp($this->sugarbean->field_defs[$post]['dbType'], 'varchar') == 0) {
                    if (strcmp($value, '1') == 0)
                        $_POST[$post] = 'on';
                    if (strcmp($value, '2') == 0)
                        $_POST[$post] = 'off';
                }
            }
        }

        if (!empty($_REQUEST['uid']))
            $_POST['mass'] = explode(',', $_REQUEST['uid']); // coming from listview
        elseif (isset($_REQUEST['entire']) && empty($_POST['mass'])) {
            if (empty($order_by))
                $order_by = '';
            $ret_array = create_export_query_relate_link_patch($_REQUEST['module'], $this->searchFields, $this->where_clauses);
            $query = $this->sugarbean->create_export_query($order_by, $ret_array['where'], $ret_array['join']);
            $result = $db->query($query, true);
            $new_arr = array();
            while ($val = $db->fetchByAssoc($result, -1, false)) {
                array_push($new_arr, $val['id']);
            }
            $_POST['mass'] = $new_arr;
        }

        if (isset($_POST['mass']) && is_array($_POST['mass']) && $_REQUEST['massupdate'] == 'true') {
            $count = 0;
            foreach ($_POST['mass'] as $id) {
                if (empty($id)) {
                    continue;
                }
                if (isset($_POST['Delete'])) {
                    $this->sugarbean->retrieve($id);
                    if ($this->sugarbean->ACLAccess('Delete')) {
                        //Martin Hu Bug #20872
                        if ($this->sugarbean->object_name == 'EmailMan') {
                            $query = "DELETE FROM emailman WHERE id = '" . $this->sugarbean->id . "'";
                            $db->query($query);
                        } else {
                            if ($this->sugarbean->object_name == 'Team') {
                                if ($this->sugarbean->private == 1) {
                                    die($GLOBALS['app_strings']['LBL_MASSUPDATE_DELETE_PRIVATE_TEAMS']);
                                }
                                if ($this->sugarbean->id == $this->sugarbean->global_team) {
                                    die($GLOBALS['app_strings']['LBL_MASSUPDATE_DELETE_GLOBAL_TEAM']);
                                }
                            }
                            $this->sugarbean->mark_deleted($id);
                        }
                    }
                } else {
                    if ($this->sugarbean->object_name == 'Contact' && isset($_POST['Sync'])) { // special for contacts module
                        if ($_POST['Sync'] == 'true') {
                            $this->sugarbean->retrieve($id);
                            if ($this->sugarbean->ACLAccess('Save')) {
                                if ($this->sugarbean->object_name == 'Contact') {

                                    $this->sugarbean->contacts_users_id = $current_user->id;
                                    $this->sugarbean->save(false);
                                }
                            }
                        } elseif ($_POST['Sync'] == 'false') {
                            $this->sugarbean->retrieve($id);
                            if ($this->sugarbean->ACLAccess('Save')) {
                                if ($this->sugarbean->object_name == 'Contact') {
                                    if (!isset($this->sugarbean->users)) {
                                        $this->sugarbean->load_relationship('user_sync');
                                    }
                                    $this->sugarbean->contacts_users_id = null;
                                    $this->sugarbean->user_sync->delete($this->sugarbean->id, $current_user->id);
                                }
                            }
                        }
                    } //end if for special Contact handling

                    if ($count++ != 0) {
                        //Create a new instance to clear values and handle additional updates to bean's 2,3,4...
                        $className = get_class($this->sugarbean);
                        $this->sugarbean = new $className();
                    }

                    $this->sugarbean->retrieve($id);

                    foreach ($_POST as $field => $value) {
                        if (isset($this->sugarbean->field_defs[$field])) {
                            if ($this->sugarbean->field_defs[$field]['type'] == 'datetime') {
                                $_POST[$field] = $this->date_to_dateTime($field, $value);
                            }
                            if ($this->sugarbean->field_defs[$field]['type'] == 'bool') {
                                $this->checkClearField($field, $value);
                            }
                        }
                    }











                    if ($this->sugarbean->ACLAccess('Save')) {
                        $_POST['record'] = $id;
                        $_GET['record'] = $id;
                        $_REQUEST['record'] = $id;
                        $newbean = $this->sugarbean;
                        //Call include/formbase.php, but do not call retrieve again
                        populateFromPost('', $newbean, true);
                        $newbean->save_from_post = false;
                        if (!isset($_POST['parent_id']))
                            $newbean->parent_type = null;

                        $check_notify = FALSE;

                        if (isset($this->sugarbean->assigned_user_id)) {
                            $old_assigned_user_id = $this->sugarbean->assigned_user_id;
                            if (!empty($_POST['assigned_user_id'])
                                    && ($old_assigned_user_id != $_POST['assigned_user_id'])
                                    && ($_POST['assigned_user_id'] != $current_user->id)) {
                                $check_notify = TRUE;
                            }
                        }
                        $email_address_id = '';
                        if (!empty($_POST['optout_primary'])) {
                            $optout_flag_value = 0;
                            if ($_POST['optout_primary'] == 'true') {
                                $optout_flag_value = 1;
                            } // if
                            if (isset($this->sugarbean->emailAddress)) {
                                if (!empty($this->sugarbean->emailAddress->addresses)) {
                                    foreach ($this->sugarbean->emailAddress->addresses as $key => $emailAddressRow) {
                                        if ($emailAddressRow['primary_address'] == '1') {
                                            $email_address_id = $emailAddressRow['email_address_id'];
                                            break;
                                        } // if
                                    } // foreach
                                } // if
                            } // if
                        } // if
                        $newbean->save($check_notify);
                        if (!empty($email_address_id)) {
                            $query = "UPDATE email_addresses SET opt_out = {$optout_flag_value} where id = '{$emailAddressRow['email_address_id']}'";
                            $GLOBALS['db']->query($query);
                        } // if
                    }
                }
            }
        }
    }

    /**
     * Displays the massupdate form
     */
    function getMassUpdateForm() {
        global $app_strings;
        global $current_user;

        if ($this->sugarbean->bean_implements('ACL') && !ACLController::checkAccess($this->sugarbean->module_dir, 'edit', true)) {
            return '';
        }
        $lang_delete = translate('LBL_DELETE');
        $lang_update = translate('LBL_UPDATE');
        $lang_confirm = translate('NTC_DELETE_CONFIRMATION_MULTIPLE');
        $lang_sync = translate('LBL_SYNC_CONTACT');
        $lang_oc_status = translate('LBL_OC_STATUS');
        $lang_unsync = translate('LBL_UNSYNC');
        $lang_archive = translate('LBL_ARCHIVE');
        $lang_optout_primaryemail = $app_strings['LBL_OPT_OUT_FLAG_PRIMARY'];



//		if(!isset($this->sugarbean->field_defs) || count($this->sugarbean->field_defs) == 0) {
//			$html = "<table cellpadding='0' cellspacing='0' border='0' width='100%'><tr><td>";
//
//			if($this->sugarbean->ACLAccess('Delete', true) ){
//				$html .= "<input type='submit' name='Delete' value='{$lang_delete}' onclick=\"return confirm('{$lang_confirm}')\" class='button'>";
//			}
//			$html .= "</td></tr></table>";
//			return $html;
//		}

        $should_use = false;

        $html = "<div id='massupdate_form'>" . get_form_header($app_strings['LBL_MASS_UPDATE'], '', false);
        $html .= "<table cellpadding='0' cellspacing='0' border='0' width='100%'><tr><td style='padding-bottom: 2px;' class='listViewButtons'><input onclick='return sListView.send_mass_update(\"selected\", \"{$app_strings['LBL_LISTVIEW_NO_SELECTED']}\")' type='submit' id='update_button' name='Update' value='{$lang_update}' class='button'>";
        // TODO: allow ACL access for Delete to be set false always for users
//		if($this->sugarbean->ACLAccess('Delete', true) && $this->sugarbean->object_name != 'User') {
//			global $app_list_strings;
//			$html .=" <input id='delete_button' type='submit' name='Delete' value='{$lang_delete}' onclick='return confirm(\"{$lang_confirm}\") && sListView.send_mass_update(\"selected\", \"{$app_strings['LBL_LISTVIEW_NO_SELECTED']}\", 1)' class='button'>";
//		}
        // only for My Inbox views - to allow CSRs to have an "Archive" emails feature to get the email "out" of their inbox.
        if ($this->sugarbean->object_name == 'Email'
                && (isset($_REQUEST['assigned_user_id']) && !empty($_REQUEST['assigned_user_id']))
                && (isset($_REQUEST['type']) && !empty($_REQUEST['type']) && $_REQUEST['type'] == 'inbound')) {
            $html .=<<<eoq
			<input type='button' name='archive' value="{$lang_archive}" class='button' onClick='setArchived();'>
			<input type='hidden' name='ie_assigned_user_id' value="{$current_user->id}">
			<input type='hidden' name='ie_type' value="inbound">
eoq;
        }

        $html .= "</td></tr></table><table cellpadding='0' cellspacing='0' border='0' width='100%' class='tabForm' id='mass_update_table'><tr><td><table width='100%' border='0' cellspacing='0' cellpadding='0'>";

        $even = true;
        if ($this->sugarbean->object_name == 'Contact') {
            $html .= "<tr><td width='15%' class='dataLabel'>$lang_sync</td><td width='35%' class='dataField'><select name='Sync'><option value=''>{$GLOBALS['app_strings']['LBL_NONE']}</option><option value='false'>{$GLOBALS['app_list_strings']['checkbox_dom']['2']}</option><option value='true'>{$GLOBALS['app_list_strings']['checkbox_dom']['1']}</option></select></td>";
            $even = false;
        }

        if ($this->sugarbean->object_name == 'User' && (!isset($this->sugarbean->module_dir) || $this->sugarbean->module_dir == 'Employees')) {
            $this->sugarbean->field_defs['status']['massupdate'] = false;

            $this->sugarbean->field_defs['employee_status']['type'] = 'enum';
            $this->sugarbean->field_defs['employee_status']['massupdate'] = true;
            $this->sugarbean->field_defs['employee_status']['options'] = 'employee_status_dom';
        }
        if ($this->sugarbean->object_name == 'InboundEmail') {
            $this->sugarbean->field_defs['status']['type'] = 'enum';
            $this->sugarbean->field_defs['status']['options'] = 'user_status_dom';
        }








        static $banned = array('date_modified' => 1, 'date_entered' => 1, 'created_by' => 1, 'modified_user_id' => 1, 'deleted' => 1,);
        foreach ($this->sugarbean->field_defs as $field) {




            if (!isset($banned[$field['name']]) && (!isset($field['massupdate']) || !empty($field['massupdate']))) {
                $newhtml = '';
                if ($even) {
                    $newhtml .= "<tr>";
                }
                if (isset($field['vname'])) {
                    $displayname = translate($field['vname']);
                } else {
                    $displayname = '';
                }
                if (isset($field['type']) && $field['type'] == 'relate' && isset($field['id_name']) && $field['id_name'] == 'assigned_user_id')
                    $field['type'] = 'assigned_user_name';
                if (isset($field['custom_type']))
                    $field['type'] = $field['custom_type'];
                if (isset($field['type'])) {
                    switch ($field["type"]) {
                        case "relate":
                            // bug 14691: avoid laying out an empty cell in the <table>
                            $handleRelationship = $this->handleRelationship($displayname, $field);
                            if ($handleRelationship != '') {
                                $even = !$even;
                                $newhtml .= $handleRelationship;
                            }
                            break;
                        case "parent":$even = !$even;
                            $newhtml .=$this->addParent($displayname, $field);
                            break;
                        case "contact_id":$even = !$even;
                            $newhtml .=$this->addContactID($displayname, $field["name"]);
                            break;
                        case "assigned_user_name":$even = !$even;
                            $newhtml .= $this->addAssignedUserID($displayname, $field["name"]);
                            break;
                        case "account_id":$even = !$even;
                            $newhtml .= $this->addAccountID($displayname, $field["name"]);
                            break;
                        case "account_name":$even = !$even;
                            $newhtml .= $this->addAccountID($displayname, $field["id_name"]);
                            break;
                        case "bool": $even = !$even;
                            $newhtml .= $this->addBool($displayname, $field["name"]);
                            break;
                        case "enum":
                        case "multienum":
                            if (!empty($field['isMultiSelect'])) {
                                $even = !$even;
                                $newhtml .= $this->addStatusMulti($displayname, $field["name"], translate($field["options"]));
                                break;
                            } else if (!empty($field['options'])) {
                                $even = !$even;
                                $newhtml .= $this->addStatus($displayname, $field["name"], translate($field["options"]));
                                break;
                            } else if (!empty($field['function'])) {
                                $functionValue = $this->getFunctionValue($this->sugarbean, $field);
                                $even = !$even;
                                $newhtml .= $this->addStatus($displayname, $field["name"], $functionValue);
                                break;
                            }
                            break;
                        case "datetime":
                        case "date":$even = !$even;
                            $newhtml .= $this->addDate($displayname, $field["name"]);
                            break;
                    }
                }
                if ($even) {
                    $newhtml .="</tr>";
                } else {
                    $should_use = true;
                }
                if (!in_array($newhtml, array('<tr>', '</tr>', '<tr></tr>', '<tr><td></td></tr>'))) {
                    $html.=$newhtml;
                }
            }
        }

        if ($this->sugarbean->object_name == 'Contact' ||
                $this->sugarbean->object_name == 'Account' ||
                $this->sugarbean->object_name == 'Lead' ||
                $this->sugarbean->object_name == 'Prospect') {

            $html .= "<tr><td width='15%' class='dataLabel'>$lang_optout_primaryemail</td><td width='35%' class='dataField'><select name='optout_primary'><option value=''>{$GLOBALS['app_strings']['LBL_NONE']}</option><option value='false'>{$GLOBALS['app_list_strings']['checkbox_dom']['2']}</option><option value='true'>{$GLOBALS['app_list_strings']['checkbox_dom']['1']}</option></select></td></tr>";
        }
        $html .="</table></td></tr></table></div>";


        if ($should_use) {
            return $html;
        } else {
            if ($this->sugarbean->ACLAccess('Delete', true)) {
                return "<table cellpadding='0' cellspacing='0' border='0' width='100%'><tr><td><input type='submit' name='Delete' value='$lang_delete' onclick=\"return confirm('{$lang_confirm}')\" class='button'></td></tr></table>";
            } else {
                return '';
            }
        }
    }

    function getFunctionValue($focus, $vardef) {
        $function = $vardef['function'];
        if (is_array($function) && isset($function['name'])) {
            $function = $vardef['function']['name'];
        } else {
            $function = $vardef['function'];
        }
        if (!empty($vardef['function']['returns']) && $vardef['function']['returns'] == 'html') {
            if (!empty($vardef['function']['include'])) {
                require_once($vardef['function']['include']);
            }
            return $function($focus, $vardef['name'], '', 'MassUpdate');
        } else {
            return $function($focus, $vardef['name'], '', 'MassUpdate');
        }
    }

    function get_dealer_type($dealer_list) {
        $select_creater = '<select id="fmp_dealer_type" onclick="javaScript:get_date_for_table()" size="10" multiple="multiple" style="width: 170px;">';
        $select_creater .= '<option value="all" style="border-bottom: 2px solid grey;">ALL</option>';
        foreach ($dealer_list as $key => $value) {
            $select_creater .= '<option value="' . $key . '">' . $value . '</option>';
        }
        $select_creater .= '</select>';
        return $select_creater;
    }

    function build__slsm($compiled_slsm, $is_user_id) {
        foreach ($compiled_slsm as $k => $v) {
            $compiled_slsm[$k] = "'$v'";
        }

        $h = ''
                . $this->user_add_on($is_user_id)
                . ' WHERE dsls_dailysales.deleted = 0 AND slsm IN (' . implode(', ', $compiled_slsm) . ') '
        ;
        return $h;
    }

    protected function user_add_on($is_user_id) {
        if (!$is_user_id) {
            return;
        }

        return ''
                . ' AND x_m.assigned_user_id="' . $this->user_id . '" '
        ;
    }

    function scripts_for_display() {
        global $app_list_strings;
        //<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
        //<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/jquery-ui.min.js" type="text/javascript"></script>
        //<link rel="stylesheet" type="text/css" href="custom/modules/Accounts/jquery-ui-1.8.11.custom.css" />

        $statuses .= '<select name="direction_qi" id="direction_qi" title="">';

        foreach ($app_list_strings['call_direction_dom'] as $k => $v)
            $statuses .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';

        $statuses .= '</select><br /><select name="status_qi" id="status_qi" title="">';

        foreach ($app_list_strings['call_status_dom'] as $k => $v)
            $statuses .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';
        $statuses .= '</select>';
        return '
            
            <script src="custom/modules/Accounts/jquery.datatables.min.js" type="text/javascript"></script>
	    <script src="custom/modules/Accounts/ColVis.js" type="text/javascript"></script>
	    <script src="custom/modules/Accounts/jquery.autoresize.js" type="text/javascript"></script>
	    <script src="custom/modules/Accounts/jquery-fieldselection.js" type="text/javascript"></script>
            
	    <link rel="stylesheet" type="text/css" href="custom/modules/Accounts/datatables.css" />
	    <link rel="stylesheet" type="text/css" href="custom/modules/Accounts/ColVis.css" />
            
            <script language="javascript">

                    function get_date_for_table(){
			//type of popup-box (accounts/leads)
			var type_popup = $("div.record_dialog_class_qi").attr("popup_type");


                        $(".yui-skin-sam-fmp-sales").find("#panel").slideUp("fast");
			if(type_popup == "accounts") {
	                        var url = "index.php?module=Accounts&action=getCustomers";
			} else {	
				var url = "index.php?module=Leads&action=getCustomersFilter";
			}
                        var input_value = $("#fmp_slsm_input").val();
                        var username_value = jQuery("#call_list_username").val();
			var city_value = jQuery("#call_list_city").val();
			var state_value = jQuery("#call_list_state").val();
			var postalcode_value = jQuery("#call_list_postalcode").val();
			
                        if(input_value.length == 0){
                            var select_slsm = $("#fmprep_slsm_tree option:selected").val();
                            }else{
                            var select_slsm = $("#fmprep_slsm_tree_search option:selected").val();
                            }
                        var select_reg_loc = $("#fmp_reg_loc option:selected").val();
			//$("#customers-list-to-calendar").html("Loading ...");
                        var select_dealer = $("#fmp_dealer_type option:selected").val();
			var select_reps = $("#opp_sales_reps_list option:selected").val();
			var select_status = $("#leads_status").val();
			var select_source = $("#leads_source").val();

			if(select_status != null) {
				select_status = select_status.join(";");
			}

			if(select_source != null) {
				select_source = select_source.join(";");
			}

                        //$.post(url, {slsm_num: select_slsm, reg_loc: select_reg_loc, dealer: select_dealer, username: username_value}, function(data){
                            
                               //$("#customers-list-to-calendar").html(data);
                                
			       $("#customers_list").dataTable({
					"bJQueryUI": true,
					"bDestroy": true,
					"bProcessing": true,
					"bServerSide": true,
					"iDisplayLength": 100,
  					"oLanguage": {
                                                    "sLengthMenu": \'Show <select>\' +
                                                                                \'<option value="10">10</option>\' +
                                                                                \'<option value="20">20</option>\' +
                                                                                \'<option value="30">30</option>\' +
                                                                                \'<option value="40">40</option>\' +
                                                                                \'<option value="50">50</option>\' +
                                                                                \'<option value="100">100</option>\' +
                                                                                \'<option value="200">200</option>\' +
                                                                                \'<option value="99999999">All</option>\' +
                                                                                \'</select> entries\'
                                            },
					"sAjaxSource": url,
					"fnDrawCallback": function(oSettings, json) {
						//console.log($("div.record_dialog_class_qi").attr("popup_type"));
      						//cal2_hide_address_columns ($("div.record_dialog_class_qi").attr("popup_type"));
                                                                                                            if(selected_customers.length > 0) {
                                                                                                                $.each(selected_customers, function (k, v) {
                                                                                                                    $("table#customers_list input#" + v.id).attr("checked", "checked");
                                                                                                                });
                                                                                                            }

                                                                                                            $("table#customers_list input:checkbox").click(function () {
                                                                                                                var checkboxelem = $(this);
                                                                                                                var row = checkboxelem.parent().parent();
                                                                                                                if (checkboxelem.is(":checked")) {
                                                                                                                    $(row).addClass("print-row");
                                                                                                                    selected_customers.push(this); 
                                                                                                                }else {
                                                                                                                    $(row).removeClass("print-row");
                                                                                                                    if(selected_customers.length > 0) {
                                                                                                                        for(var i = 0; i < selected_customers.length; i++ ){
                                                                                                                        if(typeof selected_customers[i] != "undefined"){
                                                                                                                                if(selected_customers[i].id == checkboxelem.attr("id")) {
                                                                                                                                    delete selected_customers[i];
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                 }
                                                                                                            });
						positioning_autopopulate_fields();
                                                                                                            $("select.statuses_direction").not("select#direction_qi").val($("select#direction_qi").val());
                                                                                                $("select.statuses_status").not("select#status_qi").val($("select#status_qi").val());
                                                                                                $("textarea.pc-plan[textarea-custom!=\'1\']").not("#autopopulate-pc-plan").each(function (k, v) {
                                                                                                    $(this).val($("#autopopulate-pc-plan").val() + \'\n\');	
                                                                                                });
                                                                                                $("textarea.pc-plan[textarea-custom!=\'1\']").not("#autopopulate-pc-plan").attr(\'autopopulated\', this.value);

                                                                                                $("textarea.outcome[textarea-custom!=\'1\']").not("#autopopulate-outcome").each(function (k, v) {
                                                                                                    $(this).val($("#autopopulate-outcome").val() + \'\n\');	
                                                                                                });
                                                                                                $("textarea.outcome[textarea-custom!=\'1\']").not("#autopopulate-outcome").attr(\'autopopulated\', this.value);
    					},
					"sPaginationType": "full_numbers",
					"sDom": \'C<"clear"><"H"lr<"#autofill_pcp_outcome"> >t<"F"ip>\',
					"fnServerData": function ( sSource, aoData, fnCallback ) {
						/* Add some extra data to the sender */
						aoData.push({name: \'slsm_num\', value: select_slsm });
						aoData.push({name: \'reg_loc\', value: select_reg_loc});
						aoData.push({name: \'dealer\', value: select_dealer });
						aoData.push({name: \'reps\', value: select_reps });
						aoData.push({name: \'username\', value: username_value});
						aoData.push({name: \'city\', value: city_value});
						aoData.push({name: \'state\', value: state_value});
						aoData.push({name: \'postalcode\', value: postalcode_value});
						aoData.push({name: \'status\', value: select_status});
						aoData.push({name: \'source\', value: select_source});
						$.getJSON( sSource, aoData, function (json) {

						    fnCallback(json);
						    
					    } );
				    	}

				});
				$("#customers_list_length").css("width", "13%");
				$("#customers_list_filter").css("width", "23%");
				$("#customers_list_filter").css("float", "left");
				$("#autofill_pcp_outcome").css("float", "right");
				$("#autofill_pcp_outcome").append(\'<div class="statuses-table-top-populate">' . $statuses . '</div>\');
				$("#autofill_pcp_outcome").append(\'<input type="text" name="pc-plan" class="pc-plan" id="autopopulate-pc-plan">\');
				$("#autofill_pcp_outcome").append(\'<input type="text" name="outcome" class="outcome" id="autopopulate-outcome">\');
				                
				$("#customers_list_wrapper #customers_list tbody").sortable();
				

				cal2_hide_address_columns ($("div.record_dialog_class_qi").attr("popup_type"));

				var d=new Date();
		
				var hour = d.getHours();
				if(hour > 12) {
				    hour = hour - 12;
				    var medium = "pm";
				}else{
				    var medium = "am";
				}
				var timestamp = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear() + ", " + hour + ":" + d.getMinutes() + medium + " " + d.toString().replace(/^.*\(|\)$/g, "");
				$("#autopopulate-pc-plan").keyup(function() {
					//console.log(this.value);
					$("textarea.pc-plan[\'textarea-custom\'!=\'1\']").not("#autopopulate-pc-plan").val(this.value);
				});

				$("#autopopulate-pc-plan").blur(function() {
					//console.log(this.value);
					var timestamp = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear() + ", " + hour + ":" + d.getMinutes() + medium + " " + d.toString().replace(/^.*\(|\)$/g, "").replace(/[^A-Z]/g, "");
					//$("textarea.pc-plan").not("#autopopulate-pc-plan").val(timestamp + " : " + this.value);
					$("textarea.pc-plan[textarea-custom!=1]").not("#autopopulate-pc-plan").val(this.value);
				});



				$("#autopopulate-outcome").keyup(function() {
					//console.log(this.value);
					$("textarea.outcome[textarea-custom!=\'1\']").not("#autopopulate-outcome").val(this.value);
				});

				$("#autopopulate-outcome").blur(function() {
					var timestamp = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear() + ", " + hour + ":" + d.getMinutes() + medium + " " + d.toString().replace(/^.*\(|\)$/g, "").replace(/[^A-Z]/g, "");
					//console.log(this.value);
					//$("textarea.outcome").not("#autopopulate-outcome").val(timestamp + " : " + this.value);
					$("textarea.outcome[textarea-custom!=1]").not("#autopopulate-outcome").val(this.value);
				});
                                //positioning autopopulate fields for make this inputs in top of columns
				positioning_autopopulate_fields();

                                
                           // });
			
                        }
                    function fmp_slsm_list_quick_search(input_val){
                        if(input_val.length != 0){
                            $("#box_for_slsm_first").hide();
                            var new_select = "";
                            new_select += \'<select id="fmprep_slsm_tree_search" onclick="javaScript:get_date_for_table()" size="15" multiple="multiple" style="width: 340px;">\';
                            new_select += \'<option value="all" style="border-bottom: 2px solid grey;">ALL</option>\';
                            $.each($("#fmprep_slsm_tree option"), function(){
                                var option_val = this.text;
                                if(option_val.indexOf(input_val.toUpperCase()) + 1) {
                                      new_select += \'<option value="\'+this.value+\'">\'+this.text+\'</option>\';
                                    }
                                });
                            new_select += \'</select>\';
                            $("#box_for_slsm_second").show();
                            $("#box_for_slsm_second").html(new_select);
                           }else{
                               $("#box_for_slsm_second").hide();
                               $("#box_for_slsm_first").show();
                           }
                    }

                    $(document).ready(function(){
                      	//jQuery("#call_list_username").blur(function() {
                        //	get_date_for_table();
                        //});


(function(){
	var old = $.ui.dialog.prototype._create;
	$.ui.dialog.prototype._create = function(d){
		old.call(this, d);
		var self = this,
			options = self.options,
			oldHeight = options.height,
			oldWidth = options.width,
			uiDialogTitlebarFull = $(\'<a href="#"></a>\')
				.addClass(
					\'ui-dialog-titlebar-full \' +
					\'ui-corner-all\'
				)
				.attr(\'role\', \'button\')
				.hover(
					function() {
						uiDialogTitlebarFull.addClass(\'ui-state-hover\');
					},
					function() {
						uiDialogTitlebarFull.removeClass(\'ui-state-hover\');
					}
				)
				.toggle(
					function() {
						self._setOptions({
							height : window.innerHeight - 10,
							width : window.innerWidth - 30
						});
						self._position(\'center\');
						return false;
					},
					function() {
						self._setOptions({
							height : oldHeight,
							width : oldWidth
						});
						self._position(\'center\');
						return false;
					}
				)
				.focus(function() {
					uiDialogTitlebarFull.addClass(\'ui-state-focus\');
				})
				.blur(function() {
					uiDialogTitlebarFull.removeClass(\'ui-state-focus\');
				})
				.appendTo(self.uiDialogTitlebar),

			uiDialogTitlebarFullText = $(\'<span></span>\')
				.addClass(
					\'ui-icon \' +
					\'ui-icon-newwin\'
				)
				.text(options.fullText)
				.appendTo(uiDialogTitlebarFull)

	};
})();

			
			
                        $("#slsm_list_show").hover(
                            function(){
                                $("#slsm_list_show").find("#slsm_panel").stop(true, true);
                                $("#slsm_list_show").find("#slsm_panel").slideDown();
                                $("#customers_list_wrapper").css("z-index", -1000);
                            },
                            function() {
                                $("#slsm_list_show").find("#slsm_panel").slideUp("fast");
                                $("#customers_list_wrapper").css("z-index", 1000);
                            }
                            );
                        $("#area_list_show").hover(
                            function(){
                                $("#area_list_show").find("#area_panel").stop(true, true);
                                $("#area_list_show").find("#area_panel").slideDown();
                                $("#customers_list_wrapper").css("z-index", -1000);
                            },
                            function() {
                                $("#area_list_show").find("#area_panel").slideUp("fast");
                                $("#customers_list_wrapper").css("z-index", 1000);
                            }
                            );
                        $("#dealer_list_show").hover(
                            function(){
                                $("#dealer_list_show").find("#dealer_panel").stop(true, true);
                                $("#dealer_list_show").find("#dealer_panel").slideDown();
                                $("#customers_list_wrapper").css("z-index", -1000);
                            },
                            function() {
                                $("#dealer_list_show").find("#dealer_panel").slideUp("fast");
                                $("#customers_list_wrapper").css("z-index", 1000);
                            }
                        );

                        $("#reps_list_show").hover(
                            function(){
                                $("#reps_list_show").find("#reps_panel").stop(true, true);
                                $("#reps_list_show").find("#reps_panel").slideDown();
                                $("#customers_list_wrapper").css("z-index", -1000);
                            },
                            function() {
                                $("#reps_list_show").find("#reps_panel").slideUp("fast");
                                $("#customers_list_wrapper").css("z-index", 1000);
                            }
                        );
                        $("#leads_status_show").hover(
                            function(){
                                $("#leads_status_show").find("#status_panel").stop(true, true);
                                $("#leads_status_show").find("#status_panel").slideDown();
                                $("#customers_list_wrapper").css("z-index", -1000);
                            },
                            function() {
                                $("#leads_status_show").find("#status_panel").slideUp("fast");
                                $("#customers_list_wrapper").css("z-index", 1000);
                            }
                        );
                        $("#leads_source_show").hover(
                            function(){
                                $("#leads_source_show").find("#source_panel").stop(true, true);
                                $("#leads_source_show").find("#source_panel").slideDown();
                                $("#customers_list_wrapper").css("z-index", -1000);
                            },
                            function() {
                                $("#leads_source_show").find("#source_panel").slideUp("fast");
                                $("#customers_list_wrapper").css("z-index", 1000);
                            }
                        );

                    })

		function cal2_hide_address_columns (type) {

		    //hide adrress columns
		    
		    /* Get the DataTables object again - this is not a recreation, just a get of the object */
		    if (type == "current-customer-call-list" || type == "accounts") {

			    var oTable = $(\'#customers_list\').dataTable();
			     
			    var bVis = oTable.fnSettings().aoColumns[3].bVisible;
			    oTable.fnSetColumnVis( 3, bVis ? false : true );

			    bVis = oTable.fnSettings().aoColumns[4].bVisible;
			    oTable.fnSetColumnVis( 4, bVis ? false : true );

			    bVis = oTable.fnSettings().aoColumns[5].bVisible;
			    oTable.fnSetColumnVis( 5, bVis ? false : true );

			    bVis = oTable.fnSettings().aoColumns[6].bVisible;
			    oTable.fnSetColumnVis( 6, bVis ? false : true );

		    }

		    //positioning_autopopulate_fields();

		}




		//make autopopulate inputs in top of column
		function positioning_autopopulate_fields() {

		window.setTimeout(function () { $("#customers_list_wrapper #customers_list tbody textarea.pc-plan, #customers_list_wrapper #customers_list tbody textarea.outcome").autoGrow(); }, 1);

            	window.setTimeout(function () {
			$(\'.ui-toolbar:first\').css(\'height\', \'50px\');

			
			$(\'input#autopopulate-pc-plan, textarea.pc-plan\').width($(\'th.pre-call-plan-table-header\').width());
			$(\'input#autopopulate-outcome,  textarea.outcome\').width($(\'th.outcome-table-header\').width());


			var header_statuses_position = $(\'.status-table-header\').position();
			$(\'.statuses-table-top-populate\').css({\'left\': header_statuses_position.left, \'position\': \'absolute\'});


			var header_pc_plan_position = $(\'.pre-call-plan-table-header\').position();
			$(\'#autopopulate-pc-plan\').css({\'left\': header_pc_plan_position.left, \'position\': \'absolute\'});

			$("select#direction_qi").change(function() {
				$("select.statuses_direction").not("select#direction_qi").val(this.value);
			});

			$("select#status_qi").change(function() {
				$("select.statuses_status").not("select#status_qi").val(this.value);
			});

			var header_outcome_position = $(\'.outcome-table-header\').position();
			$(\'#autopopulate-outcome\').css({\'left\': header_outcome_position.left, \'position\': \'absolute\'}); 
			$("textarea.pc-plan").not("#autopopulate-pc-plan").unbind("blur");
			$("textarea.pc-plan, textarea.status_description").not("#autopopulate-pc-plan").blur(function() {
				var cursor_position = $(this).getSelection();
				var textarea_element = this;
				var length_value = 0;
				var d=new Date();
				var hour = d.getHours() ;
                                var minutes = d.getMinutes() < 10 ? "0" + d.getMinutes() : d.getMinutes() ;
				var start_length_value = 0;
				var message_start = -1;
				if(hour > 12) {
				    hour = hour - 12;
				    var medium = "pm";
				}else{
				    var medium = "am";
				}
                                
                                hour = hour < 10 ? "0" + hour : hour ;

				var timestamp = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear() + ", " + hour + ":" + minutes + medium + " " + d.toString().replace(/^.*\(|\)$/g, "").replace(/[^A-Z]/g, "");
				var value_rows = $(this).val().split("\n");
                                var row_key = 0;
				//console.log(value_rows);				
				$.each(value_rows, function (k,v) {
					
					start_length_value = length_value;					
					length_value += v.length;
					
					if(v.length == 0) {
						//console.log("go");
						return true;					
					}
                                        row_key = k;
					//console.log(cursor_position.start);
					//console.log(length_value);
					if(cursor_position.start <= length_value+value_rows.length) {
						

						var note_of_order = k;
						if (start_length_value == 0) {
							message_start = -1;
							message_end = 0;
						}else{

							message_start = start_length_value-1;
							message_end = start_length_value+ k;						
						}
						
						$(textarea_element).attr("textarea-custom", 1);

						return false;
					}

				});
                                var ts = Math.round((new Date()).getTime() / 1000);
 
                                if($(this).attr("timestamp") == undefined || (ts - $(this).attr("timestamp")) > 60) {
                                    $(this).val(this.value.substr(0, message_start+(row_key+1)) + timestamp + " : " + this.value.substr(message_end));
                                    $(this).attr("timestamp", ts);
                                }
                                
				
                                
			});


			$("textarea.outcome").not("#autopopulate-outcome").unbind("blur");
			$("textarea.outcome").not("#autopopulate-outcome").blur(function() {
                                var cursor_position = $(this).getSelection();
				var textarea_element = this;
				var length_value = 0;
				var d=new Date();
				var hour = d.getHours() ;
                                var minutes = d.getMinutes() < 10 ? "0" + d.getMinutes() : d.getMinutes() ;
				var start_length_value = 0;
				var message_start = -1;
				if(hour > 12) {
				    hour = hour - 12;
				    var medium = "pm";
				}else{
				    var medium = "am";
				}
                                
                                hour = hour < 10 ? "0" + hour : hour ;

				var timestamp = (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear() + ", " + hour + ":" + minutes + medium + " " + d.toString().replace(/^.*\(|\)$/g, "").replace(/[^A-Z]/g, "");
				var value_rows = $(this).val().split("\n");
                                var row_key = 0;
				//console.log(value_rows);				
				$.each(value_rows, function (k,v) {
					
					start_length_value = length_value;					
					length_value += v.length;
					
					if(v.length == 0) {
						//console.log("go");
						return true;					
					}
                                        row_key = k;
					//console.log(cursor_position.start);
					//console.log(length_value);
					if(cursor_position.start <= length_value+value_rows.length) {
						

						var note_of_order = k;
						if (start_length_value == 0) {
							message_start = -1;
							message_end = 0;
						}else{

							message_start = start_length_value-1;
							message_end = start_length_value+ k;						
						}
						
						$(textarea_element).attr("textarea-custom", 1);

						return false;
					}

				});
                                var ts = Math.round((new Date()).getTime() / 1000);

                                if($(this).attr("timestamp") == undefined || (ts - $(this).attr("timestamp")) > 60) {

                                    $(this).val(this.value.substr(0, message_start+(row_key+1)) + timestamp + " : " + this.value.substr(message_end));
                                    $(this).attr("timestamp", ts);
                                }
			});


		}, 1);

}
                </script>';
    }

    /**
     * Returns end of the massupdate form
     */
    function endMassUpdateForm() {
        global $app_list_strings, $current_user;

        $dealer_list = $this->get_dealer_type($app_list_strings['fmp_dealertype_list']);
        $is_user_id = 0;
        $slsm_obj = new fmp_Param_SLSM($current_user->id);

        $slsm_obj->init();

        $is_s = $slsm_obj->is_assigned_slsm();
        if ($is_s) {
//            if(isset($_POST['slsm_num'])) {
            if (isset($_POST['slsm_num']))
                ;
            $arr = Array(0 => null);
//            }
            $r_users = $slsm_obj->compile__available_slsm($arr);
            $str_selection_button = $this->build__slsm($r_users, $is_user_id);
        }
        $slsm_tree_list = $slsm_obj->html_for_daily_sales('onclick="javaScript:get_date_for_table()"', '');  // prepeare SLSM list for display

        unset($slsm_obj);
        $slsm_area_obj = new fmp_Param_RegLoc($current_user->id);
        $slsm_area_obj->init($current_user->id);
        $area_list = $slsm_area_obj->html_for_daily_sales($current_user->id, 'onclick="javaScript:get_date_for_table()"');

        unset($slsm_area_obj);

        return $form = '</form>';

        if ($_REQUEST['module'] == 'Accounts' || $_REQUEST['module'] == 'Leads') {
            $call_list = $this->scripts_for_display();
            $call_list .= '<div id="meetings_calls_calendar_quickinput">';
            if ($_REQUEST['module'] == 'Accounts') {
                $call_list .= '<div class="yui-skin-sam-fmp-sales">
                                <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="area_list_show">
                                    <span class="first-child-fmp-sales"><button type="button" id="yui-gen0-button" >Area</button>
                                        <div id="area_panel" style="display: none; position: absolute;">
                                            ' . $area_list . '
                                        </div>
                                    </span>
                                </span>
                                <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="slsm_list_show">
                                        <span class="first-child-fmp-sales"><button type="button" id="yui-gen2-button" >Slsm</button>
                                            <div id="slsm_panel" style="display: none; position: absolute; background-color: #FFFFFF; border: 1px solid #94C1E8;">
                                                ' . $slsm_tree_list . '
                                            </div>
                                        </span>
                                </span>

                                <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="dealer_list_show">
                                        <span class="first-child-fmp-sales"><button type="button" id="yui-gen4-button" >Customer Type</button>
                                            <div id="dealer_panel" style="display: none; position: absolute;">
                                                ' . $dealer_list . '
                                            </div>
                                        </span>
                                </span>
                                
                                <span>
                                    Username <input id="call_list_username" type="text" value="">
                                </span>
                           </div>
                           ';
            } else if ($_REQUEST['module'] == 'Leads') {
                $call_list .= '<script language="javascript">
                                    $(document).ready(function(){
                                        var url = "index.php?module=' . $_REQUEST['module'] . '&action=getCustomers";
                                        $.post(url, function(data){
                                            $("#customers-list-to-calendar").html(data); 
                                            $("#customers_list").dataTable({"bJQueryUI": true, "sPaginationType": "full_numbers"});
                                            $("#customers_list_wrapper #customers_list tbody").sortable();
                                        });


                                    });
                                </script>';
            }

//            $call_list .= $dealer_list;
//            $call_list .= $slsm_tree_list;
//            $call_list .= $area_list;
            include("custom/modules/Accounts/PopupEditView.php");
            $call_list .= '</div><div id="customers-list-to-calendar"></div>' . Call_Meeting_output() . '</form>';
        }

        $form .= $call_list;


        return $form;
    }

    /**
     * Decides which popup HTML code is needed for mass updating
     * @param displayname Name to display in the popup window
     * @param field name of the field to update
     */
    function handleRelationship($displayname, $field) {
        $ret_val = '';

        if (isset($field['module'])) {
            switch ($field['module']) {
                case 'Accounts':
                    $ret_val = $this->addAccountID($displayname, $field['name'], $field['id_name']);
                    break;
                case 'Contacts':
                    $ret_val = $this->addGenericModuleID($displayname, $field['name'], $field['id_name'], "Contacts");
                    break;
                case 'Releases':
                    $ret_val = $this->addGenericModuleID($displayname, $field['name'], $field['id_name'], "Releases");
                    break;
                default:
                    break;
            }
        }

        return $ret_val;
    }

    /**
     * Add a parent selection popup window
     * @param displayname Name to display in the popup window
     * @param field_name name of the field
     */
    function addParent($displayname, $field) {
        global $app_strings, $app_list_strings;

        ///////////////////////////////////////
        ///
        /// SETUP POPUP

        $popup_request_data = array(
            'call_back_function' => 'set_return',
            'form_name' => 'MassUpdate',
            'field_to_name_array' => array(
                'id' => "parent_id",
                'name' => "parent_name",
            ),
        );

        $json = getJSONobj();
        $encoded_popup_request_data = $json->encode($popup_request_data);

        //
        ///////////////////////////////////////

        $change_parent_button = " <input title='" . $app_strings['LBL_SELECT_BUTTON_TITLE'] . "' accessKey='" . $app_strings['LBL_SELECT_BUTTON_KEY'] . "'  type='button' class='button' value='" . $app_strings['LBL_SELECT_BUTTON_LABEL']
                . "' name='button' onclick='open_popup(document.MassUpdate.{$field['type_name']}.value, 600, 400, \"\", true, false, {$encoded_popup_request_data});'  />";
        $parent_type = $field['parent_type'];
        $types = get_select_options_with_id($app_list_strings[$parent_type], '');
        //BS Fix Bug 17110
        $pattern = "/\n<OPTION.*" . $app_strings['LBL_NONE'] . "<\/OPTION>/";
        $types = preg_replace($pattern, "", $types);
        // End Fix
        return '<td width="15%" class="dataLabel">' . $displayname . " </td>
			<td><table width='100%' border='0' cellspacing='0' cellpadding='0'><tr>
			<td class='dataField' valign='top'><select name='{$field['type_name']}' id='mass_{$field['type_name']}'>$types</select></td>
			<td class='dataField'>
			<input name='{$field['id_name']}' id='mass_{$field['id_name']}' type='hidden' value=''>
			<input name='parent_name' id='mass_parent_name' readonly type='text' value=''>$change_parent_button</td>
			</tr></table></td>";
    }

    /**
     * Add a generic module popup selection popup window HTML code.
     * Currently supports Contact and Releases
     * @param displayname Name to display in the popup window
     * @param varname name of the variable
     * @param id_name name of the id in vardef
     * @param mod_type name of the module, either "Contact" or "Releases" currently
     */
    function addGenericModuleID($displayname, $varname, $id_name = '', $mod_type) {
        global $app_strings;

        if (empty($id_name))
            $id_name = strtolower($mod_type) . "_id";

        ///////////////////////////////////////
        ///
        /// SETUP POPUP

        $popup_request_data = array(
            'call_back_function' => 'set_return',
            'form_name' => 'MassUpdate',
            'field_to_name_array' => array(
                'id' => "{$id_name}",
                'name' => "{$varname}",
            ),
        );

        $json = getJSONobj();
        $encoded_popup_request_data = $json->encode($popup_request_data);

        //
        ///////////////////////////////////////

        return "<td width='15%' class='dataLabel'>$displayname</td><td width='35%' class='dataField'><input name='{$varname}' id='mass_{$varname}' readonly type='text' value=''><input name='{$id_name}' id='mass_{$id_name}' type='hidden' value=''>&nbsp;<input title=\"{$app_strings['LBL_SELECT_BUTTON_TITLE']}\" accessKey='{$app_strings['LBL_SELECT_BUTTON_KEY']}'  type='button' class='button' value='{$app_strings['LBL_SELECT_BUTTON_LABEL']}' name='button'"
                . " onclick='open_popup(\"$mod_type\", 600, 400, \"\", true, false, {$encoded_popup_request_data});' /></td>";
    }

    /**
     * Add Account selection popup window HTML code
     * @param displayname Name to display in the popup window
     * @param varname name of the variable
     * @param id_name name of the id in vardef
     */
    function addAccountID($displayname, $varname, $id_name = '') {
        global $app_strings;

        $json = getJSONobj();

        if (empty($id_name))
            $id_name = "account_id";

        ///////////////////////////////////////
        ///
        /// SETUP POPUP

        $popup_request_data = array(
            'call_back_function' => 'set_return',
            'form_name' => 'MassUpdate',
            'field_to_name_array' => array(
                'id' => "{$id_name}",
                'name' => "{$varname}",
            ),
        );

        $encoded_popup_request_data = $json->encode($popup_request_data);

        //
        ///////////////////////////////////////

        $qsParent = array('method' => 'query',
            'modules' => array('Accounts'),
            'group' => 'or',
            'field_list' => array('name', 'id'),
            'populate_list' => array('parent_name', 'parent_id'),
            'conditions' => array(array('name' => 'name', 'op' => 'like_custom', 'end' => '%', 'value' => '')),
            'order' => 'name',
            'limit' => '30',
            'no_match_text' => $app_strings['ERR_SQS_NO_MATCH']
        );
        $qsParent['populate_list'] = array('mass_' . $varname, 'mass_' . $id_name);

        $html = '<td class="dataLabel">' . $displayname . " </td>\n"
                . '<td><input class="sqsEnabled" type="text" autocomplete="off" id="mass_' . $varname . '" name="' . $varname . '" value="" /><input id="mass_' . $id_name . '" type="hidden" name="'
                . $id_name . '" value="" />&nbsp;<input type="button" name="btn1" class="button" title="'
                . $app_strings['LBL_SELECT_BUTTON_LABEL'] . '" accesskey="'
                . $app_strings['LBL_SELECT_BUTTON_KEY'] . '" value="' . $app_strings['LBL_SELECT_BUTTON_LABEL'] . '" onclick='
                . "'open_popup(\"Accounts\",600,400,\"\",true,false,{$encoded_popup_request_data});' /></td>\n";
        $html .= '<script type="text/javascript" language="javascript">if(typeof sqs_objects == \'undefined\'){var sqs_objects = new Array;}sqs_objects[\'mass_' . $varname . '\'] = ' .
                $json->encode($qsParent) . '; registerSingleSmartInputListener(document.getElementById(\'mass_' . $varname . '\'));
					addToValidateBinaryDependency(\'MassUpdate\', \'' . $varname . '\', \'alpha\', false, \'' . $app_strings['ERR_SQS_NO_MATCH_FIELD'] . $app_strings['LBL_ACCOUNT'] . '\',\'' . $id_name . '\');
					</script>';

        return $html;
    }

    /**
     * Add AssignedUser popup window HTML code
     * @param displayname Name to display in the popup window
     * @param varname name of the variable
     */
    function addAssignedUserID($displayname, $varname) {
        global $app_strings;

        $json = getJSONobj();

        $popup_request_data = array(
            'call_back_function' => 'set_return',
            'form_name' => 'MassUpdate',
            'field_to_name_array' => array(
                'id' => 'assigned_user_id',
                'user_name' => 'assigned_user_name',
            ),
        );
        $encoded_popup_request_data = $json->encode($popup_request_data);
        $qsUser = array('method' => 'get_user_array', // special method
            'field_list' => array('user_name', 'id'),
            'populate_list' => array('assigned_user_name', 'assigned_user_id'),
            'conditions' => array(array('name' => 'user_name', 'op' => 'like_custom', 'end' => '%', 'value' => '')),
            'limit' => '30', 'no_match_text' => $app_strings['ERR_SQS_NO_MATCH']);

        $qsUser['populate_list'] = array('mass_assigned_user_name', 'mass_assigned_user_id');
        $html = <<<EOQ
		<td width="15%" class="dataLabel">$displayname</td>
		<td class="dataField"><input class="sqsEnabled" autocomplete="off" id="mass_assigned_user_name" name='assigned_user_name' type="text" value=""><input id='mass_assigned_user_id' name='assigned_user_id' type="hidden" value="" />
		<input title="{$app_strings['LBL_SELECT_BUTTON_TITLE']}" accessKey="{$app_strings['LBL_SELECT_BUTTON_KEY']}" type="button" class="button" value='{$app_strings['LBL_SELECT_BUTTON_LABEL']}' name=btn1
				onclick='open_popup("Users", 600, 400, "", true, false, $encoded_popup_request_data);' />
		</td>
EOQ;
        $html .= '<script type="text/javascript" language="javascript">if(typeof sqs_objects == \'undefined\'){var sqs_objects = new Array;}sqs_objects[\'mass_assigned_user_name\'] = ' .
                $json->encode($qsUser) . '; registerSingleSmartInputListener(document.getElementById(\'mass_assigned_user_name\'));
				addToValidateBinaryDependency(\'MassUpdate\', \'assigned_user_name\', \'alpha\', false, \'' . $app_strings['ERR_SQS_NO_MATCH_FIELD'] . $app_strings['LBL_ASSIGNED_TO'] . '\',\'assigned_user_id\');
				</script>';

        return $html;
    }

    /**
     * Add Status selection popup window HTML code
     * @param displayname Name to display in the popup window
     * @param varname name of the variable
     * @param options array of options for status
     */
    function addStatus($displayname, $varname, $options) {
        global $app_strings, $app_list_strings;

        // cn: added "mass_" to the id tag to diffentieate from the status id in StoreQuery
        $html = '<td class="dataLabel" width="15%">' . $displayname . '</td><td>';
        if (is_array($options)) {
            if (!isset($options['']) && !isset($options['0'])) {
                $new_options = array();
                $new_options[''] = '';
                foreach ($options as $key => $value) {
                    $new_options[$key] = $value;
                }
                $options = $new_options;
            }
            $options = get_select_options_with_id($options, '');
            $html .= '<select id="mass_' . $varname . '" name="' . $varname . '">' . $options . '</select>';
        } else {
            $html .= $options;
        }
        $html .= '</td>';
        return $html;
    }

    /**
     * Add Status selection popup window HTML code
     * @param displayname Name to display in the popup window
     * @param varname name of the variable
     * @param options array of options for status
     */
    function addBool($displayname, $varname) {
        global $app_strings, $app_list_strings;
        return $this->addStatus($displayname, $varname, $app_list_strings['checkbox_dom']);
    }

    function addStatusMulti($displayname, $varname, $options) {
        global $app_strings, $app_list_strings;

        if (!isset($options['']) && !isset($options['0'])) {
            $new_options = array();
            $new_options[''] = '';
            foreach ($options as $key => $value) {
                $new_options[$key] = $value;
            }
            $options = $new_options;
        }
        $options = get_select_options_with_id($options, '');

        // cn: added "mass_" to the id tag to diffentieate from the status id in StoreQuery
        $html = '<td class="dataLabel" width="15%">' . $displayname . '</td>
			 <td><select id="mass_' . $varname . '" name="' . $varname . '[]" size="5" MULTIPLE>' . $options . '</select></td>';
        return $html;
    }

    /**
     * Add Date selection popup window HTML code
     * @param displayname Name to display in the popup window
     * @param varname name of the variable
     */
    function addDate($displayname, $varname) {
        global $timedate;
        $userformat = '(' . $timedate->get_user_date_format() . ')';
        $cal_dateformat = $timedate->get_cal_date_format();
        global $app_strings, $app_list_strings, $theme;

        $javascriptend = <<<EOQ
		 <script type="text/javascript">
		Calendar.setup ({
			inputField : "${varname}jscal_field", daFormat : "$cal_dateformat", ifFormat : "$cal_dateformat", showsTime : false, button : "${varname}jscal_trigger", singleClick : true, step : 1
		});
		</script>
EOQ;

        $html = <<<EOQ
	<td class="dataLabel" width="20%">$displayname</td>
	<td class='dataField' width="30%"><input onblur="parseDate(this, '$cal_dateformat')" type="text" name='$varname' size="12" id='{$varname}jscal_field' maxlength='10' value="">
    <img src="themes/default/images/jscalendar.gif" id="{$varname}jscal_trigger" align="absmiddle" title="{$app_strings['LBL_MASSUPDATE_DATE']}" alt='{$app_strings['LBL_MASSUPDATE_DATE']}'>&nbsp;<span class="dateFormat">$userformat</span>
	$javascriptend</td>
	<script> addToValidate('MassUpdate','$varname','date',false,'$displayname');</script>
EOQ;
        return $html;
    }

    function date_to_dateTime($field, $value) {
        global $timedate;
        //Check if none was set
        if (isset($this->sugarbean->field_defs[$field]['group'])) {
            $group = $this->sugarbean->field_defs[$field]['group'];
            if (isset($this->sugarbean->field_defs[$group . "_flag"]) && isset($_POST[$group . "_flag"])
                    && $_POST[$group . "_flag"] == 1) {
                return "";
            }
        }

        $oldDateTime = $this->sugarbean->$field;
        $oldTime = split(" ", $oldDateTime);
        if (isset($oldTime[1])) {
            $oldTime = $oldTime[1];
        } else {
            $oldTime = $timedate->to_display_time($timedate->get_gmt_db_datetime());
        }
        $value = split(" ", $value);
        $value = $value[0];
        return $value . " " . $oldTime;
    }

    function checkClearField($field, $value) {
        if ($value == 1 && strpos($field, '_flag')) {
            $fName = substr($field, -5);
            if (isset($this->sugarbean->field_defs[$field]['group'])) {
                $group = $this->sugarbean->field_defs[$field]['group'];
                if (isset($this->sugarbean->field_defs[$group])) {
                    $_POST[$group] = "";
                }
            }
        }
    }

    function generateSearchWhere($module, $query) {//this function is similar with function prepareSearchForm() in view.list.php
        $seed = loadBean($module);
        $this->use_old_search = true;
        if (file_exists('modules/' . $module . '/SearchForm.html')) {
            if (file_exists('modules/' . $module . '/metadata/SearchFields.php')) {
                require_once('include/SearchForm/SearchForm.php');
                $searchForm = new SearchForm($module, $seed);
            } elseif (!empty($_SESSION['export_where'])) { //bug 26026, sometimes some module doesn't have a metadata/SearchFields.php, the searchfrom is generated in the ListView.php. 
                //So currently massupdate will not gernerate the where sql. It will use the sql stored in the SESSION. But this will cause bug 24722, and it cannot be avoided now.
                $where = $_SESSION['export_where'];
                $whereArr = explode(" ", trim($where));
                if ($whereArr[0] == trim('where')) {
                    $whereClean = array_shift($whereArr);
                }
                $this->where_clauses = implode(" ", $whereArr);
                return;
            } else {
                $this->where_clauses = '';
                return;
            }
        } else {
            $this->use_old_search = false;
            require_once('include/SearchForm/SearchForm2.php');
            if (file_exists('custom/modules/' . $module . '/metadata/searchdefs.php')) {
                require_once('custom/modules/' . $module . '/metadata/searchdefs.php');
            } elseif (!empty($metafiles[$module]['searchdefs'])) {
                require_once($metafiles[$module]['searchdefs']);
            } elseif (file_exists('modules/' . $module . '/metadata/searchdefs.php')) {
                require_once('modules/' . $module . '/metadata/searchdefs.php');
            }


            if (!empty($metafiles[$module]['searchfields']))
                require_once($metafiles[$module]['searchfields']);
            elseif (file_exists('modules/' . $module . '/metadata/SearchFields.php'))
                require_once('modules/' . $module . '/metadata/SearchFields.php');
            if (empty($searchdefs) || empty($searchFields)) {
                $this->where_clauses = ''; //for some modules, such as iframe, it has massupdate, but it doesn't have search function, the where sql should be empty.
                return;
            }
            $searchForm = new SearchForm($seed, $module);
            $searchForm->setup($searchdefs, $searchFields, 'include/SearchForm/tpls/SearchFormGeneric.tpl');
        }
        $searchForm->populateFromArray(unserialize(base64_decode($query)));
        $this->searchFields = $searchForm->searchFields;
        $where_clauses = $searchForm->generateSearchWhere(true, $module);
        if (count($where_clauses) > 0) {
            $this->where_clauses = '(' . implode(' ) AND ( ', $where_clauses) . ')';
            $GLOBALS['log']->info("MassUpdate Where Clause: {$this->where_clauses}");
        }
    }

}

?>
