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

 * Description: This file is used to override the default Meta-data EditView behavior
 * to provide customization specific to the Tasks module.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('include/json_config.php');
require_once('include/MVC/View/views/view.edit.php');

class TasksViewEdit extends ViewEdit {

 	function TasksViewEdit(){
 		parent::ViewEdit();
 	}

 	/**
 	 * preDisplay
 	 * Override preDisplay to check for presence of 'status' in $_REQUEST
 	 * This is to support the "Close And Create New" operation.
 	 */
 	function preDisplay() {
 		if($_REQUEST['module'] != 'Tasks' && isset($_REQUEST['status']) && empty($_REQUEST['status'])) {
	       $this->bean->status = '';
 		} //if
 		if(!empty($_REQUEST['status']) && ($_REQUEST['status'] == 'Completed')) {
	       $this->bean->status = 'Completed';
 		}
 		parent::preDisplay();
 	}

 	function display() {
                
 		if($this->ev->isDuplicate){
	       $this->bean->status = $GLOBALS['mod_strings']['LBL_DEFAULT_STATUS'];
 		} //if
                $this->ev->process();

                if($this->ev->isDuplicate) {
                foreach($this->ev->fieldDefs as $name=>$defs) {
		   		if(!empty($defs['auto_increment'])) {
		   		   $this->ev->fieldDefs[$name]['value'] = '';
		   		}
		   }
		}


                if(isset($_REQUEST['record']) && $_REQUEST['record'] != '')
        {
            require_once("modules/Calls/Call.php");
            require_once("modules/Meetings/Meeting.php");
            $meeting_bean = new Meeting();
            $call_bean = new Call();
            if(!empty($_REQUEST['record'])) {
			$meeting_bean->retrieve($_REQUEST['record']);
                        $call_bean->retrieve($_REQUEST['record']);
		}
//                echo '<pre>';
//                print_r($this->ev->fieldDefs);
//                echo '<br/>';
//                print_r('BEFORE!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
//                echo '</pre>';

                if ($meeting_bean->id != ''){
                    foreach($this->ev->fieldDefs as $name=>$defs) {
                        if($name == 'name') $this->ev->fieldDefs[$name]['value'] = $meeting_bean->name;
                        if($name == 'parent_name') $this->ev->fieldDefs[$name]['value'] = $meeting_bean->parent_name;
                        if($name == 'parent_type') $this->ev->fieldDefs[$name]['value'] = $meeting_bean->parent_type;
                        if($name == 'parent_id') $this->ev->fieldDefs[$name]['value'] = $meeting_bean->parent_id;
                        if($name == 'description') $this->ev->fieldDefs[$name]['value'] = $meeting_bean->outcome_c;
                        if($name == 'date_start') $this->ev->fieldDefs[$name]['value'] = $meeting_bean->date_start;
                    }
                }
                if ($call_bean->id != ''){
                    foreach($this->ev->fieldDefs as $name=>$defs) {
                        if($name == 'name') $this->ev->fieldDefs[$name]['value'] = $call_bean->name;
                        if($name == 'parent_name') $this->ev->fieldDefs[$name]['value'] = $call_bean->parent_name;
                        if($name == 'parent_type') $this->ev->fieldDefs[$name]['value'] = $call_bean->parent_type;
                        if($name == 'patent_id') $this->ev->fieldDefs[$name]['value'] = $call_bean->parent_id;
                        if($name == 'description') $this->ev->fieldDefs[$name]['value'] = $call_bean->outcome_c;
                        if($name == 'date_start') $this->ev->fieldDefs[$name]['value'] = $meeting_bean->date_start;
                    }
                }

                $date_start = $this->ev->fieldDefs['date_start']['value'];
                $date_due =date("m/d/Y", strtotime($date_start)+12*24*60*60).' 05:00pm';
                $this->ev->fieldDefs['date_due']['value'] = $date_due;
//                echo '<pre>';
//                print_r($due_date);
//                echo '<br/>';
//                print_r($this->ev->fieldDefs);
//                echo '</pre>';
           }


 		echo $this->ev->display($this->showTitle);
 	}
}
?>
