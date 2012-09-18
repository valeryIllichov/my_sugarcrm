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
require_once('include/Sugar_Smarty.php');
require_once('modules/ACL/ACLController.php');

class UnifiedSearchAdvanced {
	
	function getDropDownDiv($tpl = 'modules/Home/UnifiedSearchAdvanced.tpl') {
        global $app_list_strings;
        
		if(!file_exists('cache/modules/unified_search_modules.php')) 
			$this->buildCache();
		include('cache/modules/unified_search_modules.php');

		global $mod_strings, $modListHeader, $app_list_strings, $current_user, $app_strings, $image_path, $beanList;
		$users_modules = $current_user->getPreference('globalSearch', 'search');
		
		if(!isset($users_modules)) { // preferences are empty, select all
			$users_modules = array();
			foreach($unified_search_modules as $module=>$data) {
				$users_modules[$module] = $beanList[$module];
			}
			$current_user->setPreference('globalSearch', $users_modules, 0, 'search');
		}
		$sugar_smarty = new Sugar_Smarty();
		
		$modules_to_search = array();
		foreach($unified_search_modules as $module => $data) {
			if(array_key_exists($module, $modListHeader)) {
				if(ACLController :: checkAccess($module, 'list', true)) {
					$modules_to_search[$module] = array('translated' => $app_list_strings['moduleList'][$module]);
					if(array_key_exists($module, $users_modules)) $modules_to_search[$module]['checked'] = true;
					else $modules_to_search[$module]['checked'] = false;
				}
			}
		}
		
		if(!empty($_REQUEST['query_string'])) $sugar_smarty->assign('query_string', securexss($_REQUEST['query_string']));
        else $sugar_smarty->assign('query_string', '');
		$sugar_smarty->assign('IMAGE_PATH', $image_path);
            $sugar_smarty->assign('USE_SEARCH_GIF', 0);
            $sugar_smarty->assign('LBL_SEARCH_BUTTON_LABEL', $app_strings['LBL_SEARCH_BUTTON_LABEL']);
		$sugar_smarty->assign('MODULES_TO_SEARCH', $modules_to_search);
		$sugar_smarty->debugging = true;

		return $sugar_smarty->fetch($tpl);
	}
	
	function search() {
        if(!file_exists('cache/modules/unified_search_modules.php')) 
            $this->buildCache();
		include('cache/modules/unified_search_modules.php');
		require_once('include/ListView/ListViewSmarty.php');
		require_once('include/utils.php');

		global $modListHeader, $beanList, $beanFiles, $current_language, $app_strings, $current_user, $mod_strings;
        $home_mod_strings = return_module_language($current_language, 'Home');
        
		$overlib = true;
		$_REQUEST['query_string'] = $GLOBALS['db']->quote(securexss(from_html(clean_string($_REQUEST['query_string'], 'UNIFIED_SEARCH'))));

        if(!empty($_REQUEST['advanced']) && $_REQUEST['advanced'] != 'false') {
            $modules_to_search = array(); 
    		foreach($_REQUEST as $param => $value) {
    			if(preg_match('/^search_mod_(.*)$/', $param, $match)) {
    					$modules_to_search[$match[1]] = $beanList[$match[1]];
    			}
    		}
           $current_user->setPreference('globalSearch', $modules_to_search, 0, 'search'); // save selections to user preference
        }
        else {
            $users_modules = $current_user->getPreference('globalSearch', 'search');
    		if(isset($users_modules)) { // use user's previous selections 
    			$modules_to_search = $users_modules;
    		}
            else { // select all the modules (ie first time user has used global search)
                foreach($unified_search_modules as $module=>$data) {
                    $modules_to_search[$module] = $beanList[$module];
                }

                $current_user->setPreference('globalSearch', $modules_to_search, 'search');
            }
        }
		echo $this->getDropDownDiv('modules/Home/UnifiedSearchAdvancedForm.tpl');
		
		$module_results = array();
		$module_counts = array();
		$has_results = false;

        if(!empty($_REQUEST['query_string'])) {
        	// MFH BUG 15404: Added support to trim off whitespace at the beginning and end of a search string
        	$_REQUEST['query_string'] = trim($_REQUEST['query_string']);
    		foreach($modules_to_search as $name => $beanName) {
    			if(array_key_exists($name, $modListHeader)) {
    				$where_clauses_array = array();
    				foreach($unified_search_modules[$name]['fields'] as $field=>$def) {
                        $clause = '';
                        if(isset($def['table']))  {// if field is from joining table
                            $clause = "{$def['table']}.{$def['rname']} ";
                        }
                        else {
                            $clause = "{$unified_search_modules[$name]['table']}.$field ";
                        }

                        switch($def['type']) {
                            case 'int':
                                if(is_numeric($_REQUEST['query_string']))  
                                    $clause .=  "in ('{$_REQUEST['query_string']}')";
                                else
                                    $clause .=  "in ('-1')";
                                break;
                            default:
                            	//MFH BUG 15405 - added support for seaching full names in global search
                            	if ($field == 'last_name'){
                            		if(strpos($_REQUEST['query_string'], ' ')){
                            			$string = explode(' ', $_REQUEST['query_string']);
                            			$clause = "CONCAT({$unified_search_modules[$name]['table']}.first_name, ' ', {$unified_search_modules[$name]['table']}.last_name) in ('{$string[0]} {$string[1]}', '{$string[1]} {$string[0]}')";//when search with space, we think it to be full name.
                            		} else {
                            			$clause .=  "LIKE '{$_REQUEST['query_string']}%'";
                            		}
                            	} else {
                            		$clause .=  "LIKE '{$_REQUEST['query_string']}%'";
                            	}
                                break;
                        }

                        array_push($where_clauses_array, $clause);
    				}
    
    				$where = '('.implode(' or ', $where_clauses_array).')';
    				
    				require_once($beanFiles[$beanName]);
    				$seed = new $beanName();
    				
    				$lv = new ListViewSmarty();
    				$lv->lvd->additionalDetails = false;
    				$mod_strings = return_module_language($current_language, $seed->module_dir);
    				if(file_exists('custom/modules/'.$seed->module_dir.'/metadata/listviewdefs.php')){
    					require_once('custom/modules/'.$seed->module_dir.'/metadata/listviewdefs.php');	
    				}else{
    					require_once('modules/'.$seed->module_dir.'/metadata/listviewdefs.php');
    				}
                    $displayColumns = array();
    				foreach($listViewDefs[$seed->module_dir] as $colName => $param) {
                        if(!empty($param['default']) && $param['default'] == true) {
                            $param['url_sort'] = true;//bug 27933
                            $displayColumns[$colName] = $param;
                        }
                    }
                    
                    if(count($displayColumns) > 0) $lv->displayColumns = $displayColumns; 
                    else $lv->displayColumns = $listViewDefs[$seed->module_dir];
                    
    				$lv->export = false;
                    $lv->mergeduplicates = false;
    				$lv->multiSelect = false;
    				$lv->delete = false;
    				$lv->select = false;
    				if($overlib) {
    					$lv->overlib = true;
    					$overlib = false;
    				}
    				else $lv->overlib = false;
    			
    				$lv->setup($seed, 'include/ListView/ListViewGeneric.tpl', $where, 0, 10);
    				
    				$module_results[$name] = '<br /><br />' . get_form_header($GLOBALS['app_list_strings']['moduleList'][$seed->module_dir] . ' (' . $lv->data['pageData']['offsets']['total'] . ')', '', false);
    				$module_counts[$name] = $lv->data['pageData']['offsets']['total'];
    				 
    				if($lv->data['pageData']['offsets']['total'] == 0) {
    					$module_results[$name] .= '<h2>' . $home_mod_strings['LBL_NO_RESULTS_IN_MODULE'] . '</h2>';
    				}
    				else { 
    					$has_results = true;
    					$module_results[$name] .= $lv->display(false, false);
    				}
    			}
    		}
        }
		
		if($has_results) {
			arsort($module_counts);
			foreach($module_counts as $name=>$value) {
				echo $module_results[$name];
			}
		}
		else {
			echo '<br>';
            echo $home_mod_strings['LBL_NO_RESULTS'];
            echo $home_mod_strings['LBL_NO_RESULTS_TIPS'];
		}
		
	}
	
	function buildCache() {
//		echo 'build cache';
		require_once('include/utils.php');
		require_once('include/database/DBHelper.php');
		
		$dbh = $GLOBALS['db']->getHelper();
		global $beanList, $dictionary;
		
		$supported_modules = array();
		$supported_types = array('varchar', 'char', 'int'); // support data types 
		
		foreach($beanList as $module_name=>$bean_name) {
			if($bean_name == 'aCase') $bean_name = 'Case';
			if(file_exists("modules/$module_name/vardefs.php")) {
				require_once("modules/$module_name/vardefs.php");
				if(isset($dictionary[$bean_name]['unified_search']) && $dictionary[$bean_name]['unified_search']) { // if bean participates in uf
					$fields = array();
					foreach($dictionary[$bean_name]['fields'] as $field=>$def) {
						if(isset($def['unified_search']) && $def['unified_search'] &&
							in_array($dbh->getFieldType($def), $supported_types)) { // if field participates
								$fields[$def['name']] = array('vname' => $def['vname'], 
															  'type' => $def['type']);
								if($def['type'] == 'relate') {
									$fields[$def['name']]['table'] = $def['table'];
									$fields[$def['name']]['rname'] = $def['rname'];
								} 
						} 
					}
					if(count($fields) > 0) {
						$supported_modules[$module_name]['table'] = $dictionary[$bean_name]['table'];
						$supported_modules[$module_name]['fields'] = $fields;
					}
				}
			}	
		}
		asort($supported_modules);
		write_array_to_file('unified_search_modules', $supported_modules, 'cache/modules/unified_search_modules.php');
	}
}

?>
