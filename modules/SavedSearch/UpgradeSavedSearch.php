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
class UpgradeSavedSearch {

	function UpgradeSavedSearch() {
		require_once('modules/SavedSearch/SavedSearch.php');
		$result = $GLOBALS['db']->query("SELECT id FROM saved_search");
		while($row = $GLOBALS['db']->fetchByAssoc($result)) {
		      $focus = new SavedSearch();
			  $focus->retrieve($row['id']);
			  $contents = unserialize(base64_decode($focus->contents));
			  if(isset($contents['searchFormTab']) && $contents['searchFormTab'] == 'saved_views') {
			  	 $new_contents = array();
			  	 $module = $contents['search_module'];
			  	 $advanced = !empty($contents['advanced']);
			  	 $field_map = array();
			  	 if(file_exists("modules/{$module}/metadata/SearchFields.php")) {
			  	 	require("modules/{$module}/metadata/SearchFields.php");
			  	 	$field_map = $searchFields[$module];
			  	 } else {
				  	require_once('include/utils.php');
				  	$bean = loadBean($module);
				  	$field_map = $bean->field_name_map;	 	
			  	 }
		
			  	 foreach($contents as $key=>$value) {
			  	 	 if(isset($field_map[$key])) {
			  	 	 	$new_key = $key . ($advanced ? '_advanced' : '_basic');
			  	 	 	$new_contents[$new_key] = $value;
			  	 	 } else {
			  	 	 	$new_contents[$key] = $value;
			  	 	 }
			  	 }
			  	 $new_contents['searchFormTab'] = $advanced ? 'advanced_search' : 'basic_search';
			  	 $content = base64_encode(serialize($new_contents));
			  	 $GLOBALS['db']->query("UPDATE saved_search SET contents = '{$content}' WHERE id = '{$row['id']}'");
			  }
		}
	}
}
?>
