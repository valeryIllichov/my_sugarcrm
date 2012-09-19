<?php
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
 * *******************************************************************************/
/*
 * Created on Apr 13, 2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once('include/EditView/EditView2.php');
 class ViewEdit extends SugarView{
 	var $ev;
 	var $type ='edit';
 	var $useForSubpanel = false;  //boolean variable to determine whether view can be used for subpanel creates
 	var $useModuleQuickCreateTemplate = false; //boolean variable to determine whether or not SubpanelQuickCreate has a separate display function
 	var $showTitle = true;

 	function ViewEdit(){
 		parent::SugarView();
 	}

 	function preDisplay(){
 		$metadataFile = null;
 		$foundViewDefs = false;
 		/* BEGIN - SECURITY GROUPS */ 
 		//get group ids of current user and check to see if a layout exists for that group
 		global $current_user;
		require_once('modules/SecurityGroups/SecurityGroup.php');
		$groupFocus = new SecurityGroup();
		$groupList = $groupFocus->getUserSecurityGroups($current_user->id);
		//reorder by precedence....
		
		foreach($groupList as $groupItem) {
			$GLOBALS['log']->debug("Looking for: ".'custom/modules/' . $this->module . '/metadata/'.$groupItem['id'].'/editviewdefs.php');
			if(file_exists('custom/modules/' . $this->module . '/metadata/'.$groupItem['id'].'/editviewdefs.php')){
				$_SESSION['groupLayout'] = $groupItem['id'];
				$metadataFile = 'custom/modules/' . $this->module . '/metadata/'.$groupItem['id'].'/editviewdefs.php';
			}			
		}
		
 		if(isset($metadataFile)){
 			$foundViewDefs = true;
 		}
 		else
 		/* END - SECURITY GROUPS */ 
 		if(file_exists('custom/modules/' . $this->module . '/metadata/editviewdefs.php')){
 			$metadataFile = 'custom/modules/' . $this->module . '/metadata/editviewdefs.php';
 			$foundViewDefs = true;
 		}else{
	 		if(file_exists('custom/modules/'.$this->module.'/metadata/metafiles.php')){
				require_once('custom/modules/'.$this->module.'/metadata/metafiles.php');
				if(!empty($metafiles[$this->module]['editviewdefs'])){
					$metadataFile = $metafiles[$this->module]['editviewdefs'];
					$foundViewDefs = true;
				}
			}elseif(file_exists('modules/'.$this->module.'/metadata/metafiles.php')){
				require_once('modules/'.$this->module.'/metadata/metafiles.php');
				if(!empty($metafiles[$this->module]['editviewdefs'])){
					$metadataFile = $metafiles[$this->module]['editviewdefs'];
					$foundViewDefs = true;
				}
			}
 		}
 		$GLOBALS['log']->debug("metadatafile=". $metadataFile);
		if(!$foundViewDefs && file_exists('modules/'.$this->module.'/metadata/editviewdefs.php')){
				$metadataFile = 'modules/'.$this->module.'/metadata/editviewdefs.php';
 		}

 		$this->ev = new EditView();
 		$this->ev->ss =& $this->ss;
 		$this->ev->setup($this->module, $this->bean, $metadataFile, 'include/EditView/EditView.tpl');

 	}

 	function display(){
		$this->ev->process();
		if($this->ev->isDuplicate) {
		   foreach($this->ev->fieldDefs as $name=>$defs) {
		   		if(!empty($defs['auto_increment'])) {
		   		   $this->ev->fieldDefs[$name]['value'] = '';
		   		}
		   }
		}
		echo $this->ev->display($this->showTitle);
 	}


 }
?>