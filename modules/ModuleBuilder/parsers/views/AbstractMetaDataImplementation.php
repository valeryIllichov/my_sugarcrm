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

require_once 'modules/ModuleBuilder/parsers/constants.php' ;

/*
 * Abstract base clase for Parser Implementations (using a Bridge Pattern)
 * The Implementations hide the differences between :
 * - Deployed modules (such as OOB modules and deployed ModuleBuilder modules) that are located in the /modules directory and have metadata in modules/<name>/metadata and in the custom directory
 * - WIP modules which are being worked on in ModuleBuilder and that are located in custom
 */

require_once 'modules/ModuleBuilder/parsers/views/History.php' ;

abstract class AbstractMetaDataImplementation
{
    protected $_view ;
    protected $_moduleName ;
	protected $_viewdefs;
	protected $_originalDefs;
    protected $_sourceFilename = '' ; // the name of the file from which we loaded the definition we're working on - needed when we come to write out the historical record
    // would like this to be a constant, but alas, constants cannot contain arrays...
    protected $_fileVariables = array (
		MB_DASHLETSEARCH => 'dashletData', 
		MB_DASHLET 		 => 'dashletData', 
		MB_LISTVIEW 	 => 'listViewDefs', 
		MB_SEARCHVIEW 	 => 'searchdefs', 
		MB_EDITVIEW 	 => 'viewdefs', 
		MB_DETAILVIEW 	 => 'viewdefs', 
		MB_QUICKCREATE 	 => 'viewdefs' 
	) ;

    /*
     * Getters for the definitions loaded by the Constructor
     */
    function getViewdefs ()
    {
        return $this->_viewdefs ;
    }

    function getModuleFielddefs ()
    {
        return $this->_fielddefs ;
    }
	
	/**
	 *  This function will attempt to create a valid view def from the passed in field def.
	 *  If there exists a view def in the original (non-modified) defs, and not in the currently loaded one,
	 *  it should return that definition.
	 * @return 
	 * @param $fieldDef Object
	 */
	function getOriginalDefs() {
		return $this->_originalDefs;
	}

    /*
     * Obtain a new accessor for the history of this layout
     * Ideally the History object would be a singleton; however given the use case (modulebuilder/studio) it's unlikely to be an issue
     */
    function getHistory ()
    {
        return $this->_history ;
    }

    /*
     * Load a layout from a file, given a filename
     * Doesn't do any preprocessing on the viewdefs - just returns them as found for other classes to make sense of
     * @param string filename       The full path to the file containing the layout
     * @return array                The layout, null if the file does not exist
     */
    protected function _loadFromFile ($filename)
    {
        // BEGIN ASSERTIONS
        if (! file_exists ( $filename ))
        {
;            return null ;
        }
        // END ASSERTIONS        
        $GLOBALS['log']->debug(get_class($this)."->_loadFromFile(): reading from ".$filename );
        require $filename ; // loads the viewdef - must be a require not require_once to ensure can reload if called twice in succession

        // Check to see if we have the module name set as a variable rather than embedded in the $viewdef array
        // If we do, then we have to preserve the module variable when we write the file back out
        // This is a format used by ModuleBuilder templated modules to speed the renaming of modules
        // OOB Sugar modules don't use this format
        
        $moduleVariables = array ( 'module_name' , '_module_name' , 'OBJECT_NAME' , '_object_name' ) ;
        
        $variables = array ( ) ;
        foreach ( $moduleVariables as $name )
        {
            if (isset ( $$name ))
            {
                $variables [ $name ] = $$name ;
            }
        }
        
        // Extract the layout definition from the loaded file - the layout definition is held under a variable name that varies between the various layout types (e.g., listviews hold it in listViewDefs, editviews in viewdefs)
        $viewVariable = $this->_fileVariables [ $this->_view ] ;
        $defs = $$viewVariable ;
      
        // Now tidy up the module name in the viewdef array
        // MB created definitions store the defs under packagename_modulename and later methods that expect to find them under modulename will fail
        
        if (isset ( $variables [ 'module_name' ] ))
        {
            $mbName = $variables [ 'module_name' ] ;
            if ($mbName != $this->_moduleName)
            {
                $defs [ $this->_moduleName ] = $defs [ $mbName ] ;
                unset ( $defs [ $mbName ] ) ;
            }
        }
        $this->_variables = $variables ;
        // now remove the modulename preamble from the loaded defs
        reset($defs);
        $temp = each($defs);
//        $GLOBALS['log']->debug(print_r($temp['value'],true));
        return $temp['value']; // 'value' contains the value part of 'key'=>'value' part
    }

    /*
     * Save a layout to a file
     * Must be the exact inverse of _loadFromFile
     * Obtains the additional variables, such as module_name, to include in beginning of the file (as required by ModuleBuilder) from the internal variable _variables, set in the Constructor
     * @param string filename       The full path to the file to contain the layout
     * @param array defs        Array containing the layout definition; the top level should be the definition itself; not the modulename or viewdef= preambles found in the file definitions
     */
    protected function _saveToFile ($filename , $defs, $viewVariable = "")
    {
        
        mkdir_recursive ( dirname ( $filename ) ) ;
        
        $useVariables = (count ( $this->_variables ) > 0) ;

        // create the new metadata file contents, and write it out
        // once we move to PHP 5 replace this fopen/fputs/fclose with file_put_contents()
        if ($fh = sugar_fopen ( $filename, 'w' ))
        {
            $out = "<?php\n" ;
            if ($useVariables)
            {
                // write out the $<variable>=<modulename> lines
                foreach ( $this->_variables as $key => $value )
                {
                    $out .= "\$$key = '" . $value . "';\n" ;
                }
            }
            
            $viewVariable = empty($viewVariable) ? $this->_fileVariables [ $this->_view ] : $viewVariable;
            $out .= "\$$viewVariable [".(($useVariables) ? '$module_name' : "'$this->_moduleName'")."] = \n" . var_export_helper ( $defs ) ;
            $out .= ";\n?>\n" ;

            fputs ( $fh, $out) ;
            fclose ( $fh ) ;
        } else
        {
            $GLOBALS [ 'log' ]->fatal ( get_class($this).": could not write new viewdef file " . $filename ) ;
        }
    }
    
    /*
     * Is this field something we wish to show in Studio/ModuleBuilder layout editors?
     * @param array $def    Field definition in the standard SugarBean field definition format - name, vname, type and so on
     * @return boolean      True if ok to show, false otherwise
     */
    static function validField ( $def )
    {
        // first, studio overrides - if visible then always show, if anything else, never show
        if (! empty ($def[ 'studio' ] ) )
            return ($def [ 'studio' ] == 'visible' ) ;
        // bug 19656: this test changed after 5.0.0b - we now remove all ID type fields - whether set as type, or dbtype, from the fielddefs
        //jchi #24930 , add  isset($def [ 'query_type' ]) for we introduce SearchField.php into our fileddefs array.
        return (((empty ( $def [ 'source' ] ) || $def [ 'source' ] == 'db' || $def [ 'source' ] == 'custom_fields') &&(( isset($def [ 'type' ] ) && $def [ 'type' ] != 'id' ) || isset($def [ 'query_type' ]))&& (empty ( $def [ 'dbType' ] ) || $def [ 'dbType' ] != 'id') ) // db and custom fields that aren't ID fields
               || 
               (isset ( $def [ 'name' ] ) && substr ( $def [ 'name' ], - 5 ) === '_name' ) ) ; // fields named *_name regardless of their type...just convention
//               (isset ( $def [ 'name' ] ) && strpos ( $def [ 'name' ], "_name" ) !== false) ) ; // fields not named *_name
    }

}

?>
