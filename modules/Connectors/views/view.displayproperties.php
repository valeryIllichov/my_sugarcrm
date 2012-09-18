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

require_once('include/json_config.php');
require_once('include/MVC/View/views/view.list.php');
require_once('include/Sugar_Smarty.php');
require_once('include/connectors/sources/SourceFactory.php');
class ViewDisplayProperties extends ViewList {
   
 	function ViewDisplayProperties(){
 		parent::ViewList();
 	}
    
 	function process() {
 		$this->options['show_all'] = false;
 		$this->options['show_javascript'] = true;
 		$this->options['show_footer'] = false;
 		$this->options['show_header'] = false;
 	    parent::process();
 	}
 	
    function display() {
    	require_once('include/connectors/utils/ConnectorUtils.php');
    	$source = $_REQUEST['source_id'];
        $sources = ConnectorUtils::getConnectors();
        $modules_sources = ConnectorUtils::getDisplayConfig(); 	
    	//$json = getJSONobj();
    	
    	$enabled_modules = array();
    	$disabled_modules = array();
    	
    	//Find all modules this source has been enabled for
    	foreach($modules_sources as $module=>$mapping) {
    		foreach($modules_sources[$module] as $entry) {
    			if($entry == $source) {
    			   $enabled_modules[$module] = $module;
    			}
    		}  			
   		}

    	
    	global $moduleList, $beanList;
    	//Do filtering here?
    	$count = 0;
   		global $current_user;
		$access = get_admin_modules_for_user($current_user);
	    $d = dir('modules');
		while($e = $d->read()){
			if(substr($e, 0, 1) == '.' || !is_dir('modules/' . $e))continue;
			if(empty($enabled_modules[$e]) && file_exists('modules/' . $e . '/metadata/studio.php') && file_exists('modules/' . $e . '/metadata/detailviewdefs.php') && isset($GLOBALS [ 'beanList' ][$e]) && (in_array($e, $access) || is_admin($current_user))) // installed modules must also exist in the beanList
			{
				$disabled_modules[$e] = $e;
			}
		}
		
		asort($enabled_modules);
    	asort($disabled_modules);

    	//$enabled = $json->encode($enabled_modules);
    	//$disabled = $json->encode($disabled_modules);
    	//$script = "addTable('{$module}', '{$enabled}', '{$disabled}', '{$source}', '{$GLOBALS['theme']}');\n";    		
    	//$this->ss->assign('new_modules_sources', $modules_sources);
    	//$this->ss->assign('dynamic_script', $script);
    	
    	$this->ss->assign('enabled_modules', $enabled_modules);
    	$this->ss->assign('disabled_modules', $disabled_modules);
    	$this->ss->assign('source_id', $source);
    	$this->ss->assign('mod', $GLOBALS['mod_strings']);
    	$this->ss->assign('APP', $GLOBALS['app_strings']);
    	$this->ss->assign('theme', $GLOBALS['theme']);
    	echo $this->ss->fetch('modules/Connectors/tpls/display_properties.tpl');
    }
}

?>
