<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
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



require_once('include/Dashlets/DashletGeneric.php');
require_once('modules/Cases/Case.php');

class MyCasesDashlet extends DashletGeneric { 
    function MyCasesDashlet($id, $def = null) {
        global $current_user, $app_strings;
		require('modules/Cases/Dashlets/MyCasesDashlet/MyCasesDashlet.data.php');
		
        parent::DashletGeneric($id, $def);
        
        if(empty($def['title'])) $this->title = translate('LBL_LIST_MY_CASES', 'Cases');
        $this->searchFields = $dashletData['MyCasesDashlet']['searchFields'];
        $this->columns = $dashletData['MyCasesDashlet']['columns'];
        $this->seedBean = new aCase();        
    }
   function process($lvsParams = array()){
	$lvsParams['custom_select'] = ' , cases_cstm.assign_user_id ';
//	$lvsParams['custom_select'] = 'select c.id , c.case_number , c.subject_c , c.priority , c.status , c.created_by, jt0.user_name assigned_user_name , jt0.created_by assigned_user_name_owner  , \'Users\' assigned_user_name_mod, c.assigned_user_id, cstm.assign_user_id' ;       
//	$lvsParams['custom_from'] = ' from cases c LEFT JOIN  users jt0 ON jt0.id= c.assigned_user_id AND jt0.deleted=0 left join cases_cstm cstm on cstm.assign_user_id = c.assigned_user_id';
	 $lvsParams['custom_from'] = ' LEFT JOIN cases_cstm on cases_cstm.assign_user_id = cases.assigned_user_id';
       $this->seedBean->field_defs['task_count'] =
          array(
             'name' => 'task_count',
             'vname' => '#Tasks',
             'len' => '8',
             'source' => 'non-db',
          );
       $this->seedBean->field_defs['meeting_count'] =
          array(
             'name' => 'meeting_count',
             'vname' => '#Meetingss',
             'len' => '8',
             'source' => 'non-db',
          );
       $this->seedBean->field_defs['call_count'] =
          array(
             'name' => 'call_count',
             'vname' => '#Calls',
             'len' => '8',
             'source' => 'non-db',
          );

        $lvsParams['overrideOrder'] = true;
        $lvsParams['orderBy'] = 'user_name';
        $lvsParams['sortOrder'] = 'ASC';

        parent::process($lvsParams);
	




}
}

?>
