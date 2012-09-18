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

require_once 'modules/ModuleBuilder/MB/ModuleBuilder.php' ;
require_once 'modules/ModuleBuilder/parsers/views/AbstractMetaDataImplementation.php' ;
require_once 'modules/ModuleBuilder/parsers/views/MetaDataImplementationInterface.php' ;
require_once 'modules/ModuleBuilder/parsers/views/ListLayoutMetaDataParser.php' ;
require_once 'modules/ModuleBuilder/parsers/views/GridLayoutMetaDataParser.php' ;
require_once 'modules/ModuleBuilder/parsers/constants.php' ;

class UndeployedMetaDataImplementation extends AbstractMetaDataImplementation implements MetaDataImplementationInterface
{
    
    private $_packageName ;

    function __construct ($view , $moduleName , $packageName)
    {
        $this->_view = strtolower ( $view ) ;
        $this->_moduleName = $moduleName ;
        $this->_packageName = $packageName ;
        // BEGIN ASSERTIONS
        if (! isset ( $this->_fileVariables [ $view ] ))
        {
            sugar_die ( get_class ( $this ) . ": View $view is not supported" ) ;
        }
        // END ASSERTIONS       
        

        $this->_history = new History ( $this->getFileName ( $view, $moduleName, $packageName, MB_HISTORYMETADATALOCATION ) ) ;
        
        //get the bean from ModuleBuilder
        $mb = new ModuleBuilder ( ) ;
        $this->module = & $mb->getPackageModule ( $packageName, $moduleName ) ;
        $this->module->mbvardefs->updateVardefs () ;
        
        // Set the global mod_strings directly as Sugar does not automatically load the language files for undeployed modules (how could it?)
        $GLOBALS [ 'mod_strings' ] = array_merge ( $GLOBALS [ 'mod_strings' ], $this->module->getModStrings () ) ;
        
        
        foreach ( array ( MB_HISTORYMETADATALOCATION , MB_WORKINGMETADATALOCATION ) as $type )
        {
            $this->_sourceFilename = $this->getFileName ( $view, $moduleName, $packageName, $type ) ;
            
            if (null !== ($loaded = $this->_loadFromFile ( $this->_sourceFilename )))
                break ;
        }
        
        if ($loaded === null)
        {
            sugar_die ( get_class ( $this ) . ": view definitions for View $this->_view and Module $this->_moduleName are missing" ) ;
        }
        
        $this->_viewdefs = $loaded ;
		$this->_originalDefs = $loaded;
        
        // Set the list of fields associated with this module
        $fielddefs = $this->module->mbvardefs->vardefs [ 'fields' ] ;
        $fielddefs = array_change_key_case ( $fielddefs ) ;
        $this->_fielddefs = array ( ) ;
        
        // Run through the preliminary list, keeping only those fields that are valid to include in a layout
        // Bug 24362, e should keep all of the defs as it is the specefic view's job of determining what is a valid field.
        foreach ( $fielddefs as $key => $def )
        {
            $this->_fielddefs [ strtolower ( $key ) ] = $def ;
        }
    }

    function getLanguage ()
    {
        return "" ;
    }

    /*
     * Deploy a layout
     * @param array defs    Layout definition in the same format as received by the constructor
     */
    function deploy ($defs)
    {
        //If we are pulling from the History Location, that means we did a restore, and we need to save the history for the previous file.
    	if ($this->_sourceFilename == $this->getFileName ( $this->_view, $this->_moduleName, $this->_packageName, MB_HISTORYMETADATALOCATION )
    	&& file_exists($this->getFileName ( $this->_view, $this->_moduleName, $this->_packageName, MB_WORKINGMETADATALOCATION ))) {
        	$this->_history->append ( $this->getFileName ( $this->_view, $this->_moduleName, $this->_packageName, MB_WORKINGMETADATALOCATION )) ;
        } else {
    		$this->_history->append ( $this->_sourceFilename ) ;
        }
        $filename = $this->getFileName ( $this->_view, $this->_moduleName, $this->_packageName, MB_WORKINGMETADATALOCATION ) ;
        $GLOBALS [ 'log' ]->debug ( get_class ( $this ) . "->deploy(): writing to " . $filename ) ;
        $this->_saveToFile ( $filename, $defs ) ;
    }

    /*
     * Construct a full pathname for the requested metadata
     * @param string view           The view type, that is, EditView, DetailView etc
     * @param string modulename     The name of the module that will use this layout
     * @param string type           
     */
    public static function getFileName ($view , $moduleName , $packageName , $type = MB_BASEMETADATALOCATION)
    {
        $_filenames = array (  MB_DASHLETSEARCH => 'dashletviewdefs', MB_DASHLET => 'dashletviewdefs', 
        					   MB_LISTVIEW => 'listviewdefs' , MB_SEARCHVIEW => 'searchdefs' , MB_EDITVIEW => 'editviewdefs' , 
        					   MB_DETAILVIEW => 'detailviewdefs' , MB_QUICKCREATE => 'quickcreatedefs' ) ;
        $type = strtolower ( $type ) ;
        
        // BEGIN ASSERTIONS
        if ($type != MB_BASEMETADATALOCATION && $type != MB_HISTORYMETADATALOCATION)
        {
            // just warn rather than die
            $GLOBALS [ 'log' ]->warning ( "UndeployedMetaDataImplementation->getFileName(): view type $type is not recognized" ) ;
        }
        // END ASSERTIONS
        

        switch ( $type)
        {
            case MB_HISTORYMETADATALOCATION :
                return 'custom/history/modulebuilder/packages/' . $packageName . '/modules/' . $moduleName . '/metadata/' . $_filenames [ $view ] . '.php' ;
            default :
                // get the module again, all so we can call this method statically without relying on the module stored in the class variables
                $mb = new ModuleBuilder ( ) ;
                $module = & $mb->getPackageModule ( $packageName, $moduleName ) ;
                return $module->getModuleDir () . '/metadata/' . $_filenames [ $view ] . '.php' ;
        }
    
    }
}
?>
