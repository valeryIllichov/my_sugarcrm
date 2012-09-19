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
 *********************************************************************************/
/*
 * Changes to AbstractSubpanelImplementation for DeployedSubpanels
 * The main differences are in the load and save of the definitions
 * For subpanels we must make use of the SubPanelDefinitions class to do this; this also means that the history mechanism,
 * which tracks files, not objects, needs us to create an intermediate file representation of the definition that it can manage and restore
 */

require_once 'modules/ModuleBuilder/parsers/views/MetaDataImplementationInterface.php' ;
require_once 'modules/ModuleBuilder/parsers/views/AbstractMetaDataImplementation.php' ;
require_once 'modules/ModuleBuilder/parsers/constants.php' ;

class UndeployedSubpanelImplementation extends AbstractMetaDataImplementation implements MetaDataImplementationInterface
{

    const HISTORYFILENAME = 'restored.php' ;
    const HISTORYVARIABLENAME = 'layout_defs' ;

    /*
     * Constructor
     * @param string subpanelName   The name of this subpanel
     * @param string moduleName     The name of the module to which this subpanel belongs
     * @param string packageName    If not empty, the name of the package to which this subpanel belongs
     */
    function __construct ($subpanelName , $moduleName , $packageName)
    {
        $this->_subpanelName = $subpanelName ;
        $this->_moduleName = $moduleName ;
        
        // TODO: history
        $this->historyPathname = 'custom/history/modulebuilder/packages/' . $packageName . '/modules/' . $moduleName . '/metadata/' . self::HISTORYFILENAME ;
        $this->_history = new History ( $this->historyPathname ) ;
        
        //get the bean from ModuleBuilder
        $mb = new ModuleBuilder ( ) ;
        $this->module = & $mb->getPackageModule ( $packageName, $moduleName ) ;
        $this->module->mbvardefs->updateVardefs () ;
        $this->_fielddefs = & $this->module->mbvardefs->vardefs [ 'fields' ] ;
        
        $subpanel_layout = $this->module->getAvailibleSubpanelDef ( $this->_subpanelName ) ;
        $this->_viewdefs = & $subpanel_layout [ 'list_fields' ] ;
		$this->_originalDefs = $this->_viewdefs;
       
        // Set the global mod_strings directly as Sugar does not automatically load the language files for undeployed modules (how could it?)
        $GLOBALS [ 'mod_strings' ] = array_merge ( $GLOBALS [ 'mod_strings' ], $this->module->getModStrings () ) ;
    }

    function getLanguage ()
    {
        return "" ; // '' is the signal to translate() to use the global mod_strings
    }

    /*
     * Save a subpanel
     * @param array defs    Layout definition in the same format as received by the constructor
     * @param string type   The location for the file - for example, MB_BASEMETADATALOCATION for a location in the OOB metadata directory
     */
    function deploy ($defs)
    {
        $outputDefs = $this->module->getAvailibleSubpanelDef ( $this->_subpanelName ) ;
        // first sort out the historical record...
        // copy the definition to a temporary file then let the history object add it
        write_array_to_file ( self::HISTORYVARIABLENAME, $outputDefs, $this->historyPathname, 'w', '' ) ;
        $this->_history->append ( $this->historyPathname ) ;
        // no need to unlink the temporary file as being handled by in history->append()
        //unlink ( $this->historyPathname ) ;
        
        $outputDefs [ 'list_fields' ] = $defs ;
        $this->_viewdefs = $defs ;
        $this->module->saveAvailibleSubpanelDef ( $this->_subpanelName, $outputDefs ) ;
    
    }

}