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

require_once 'modules/ModuleBuilder/parsers/views/AbstractMetaDataParser.php' ;
require_once 'modules/ModuleBuilder/parsers/views/MetaDataParserInterface.php' ;


class ListLayoutMetaDataParser extends AbstractMetaDataParser implements MetaDataParserInterface
{

    // Columns is used by the view to construct the listview - each column is built by calling the named function
    public $columns = array ( 'LBL_DEFAULT' => 'getDefaultFields' , 'LBL_AVAILABLE' => 'getAdditionalFields' , 'LBL_HIDDEN' => 'getAvailableFields' ) ;
    protected $labelIdentifier = 'label' ; // labels in the listviewdefs.php are tagged 'label' =>
    protected $allowParent = false;

    /*
     * Simple function for array_udiff_assoc function call in getAvailableFields()
     */
    static function getArrayDiff ($one , $two)
    {
        $retArray = array();
        foreach($one as $key => $value)
        {
            if (!isset($two[$key]))
            {
                $retArray[$key] = $value;
            }
        }
        return $retArray;
    }

    /*
     * Constructor
     * @param string view          The view type, that is, editview, searchview etc
     * @param string moduleName     The name of the module to which this listview belongs
     * @param string packageName    If not empty, the name of the package to which this listview belongs
     */
    function __construct ($view , $moduleName , $packageName = '')
    {
        $GLOBALS [ 'log' ]->debug ( get_class ( $this ) . ": __construct()" ) ;

        // BEGIN ASSERTIONS
        if ($view != MB_LISTVIEW && $view != MB_SEARCHVIEW && $view != MB_DASHLET && $view != MB_DASHLETSEARCH)
        {
            sugar_die ( "ListLayoutMetaDataParser: View $view is not supported" ) ;
        }
        // END ASSERTIONS

        if (empty ( $packageName ))
        {
            require_once 'modules/ModuleBuilder/parsers/views/DeployedMetaDataImplementation.php' ;
            $this->implementation = new DeployedMetaDataImplementation ( $view, $moduleName ) ;
			$this->originalViewDef = array_change_key_case($this->implementation->getOriginalDefs ());
        } else
        {
            require_once 'modules/ModuleBuilder/parsers/views/UndeployedMetaDataImplementation.php' ;
            $this->implementation = new UndeployedMetaDataImplementation ( $view, $moduleName, $packageName ) ;
        }

        $this->_viewdefs = array_change_key_case ( $this->implementation->getViewdefs () ) ; // force to lower case so don't have problems with case mismatches later
        $this->_fielddefs = $this->implementation->getModuleFieldDefs () ;

        $this->loadFieldDefs();
    }

	/**
	 * Loads field defs first from the original defs, then from the module's field_defs
	 * @return
	 */
	function loadFieldDefs()
	{
		$fielddefs = array ( ) ;

		foreach ( $this->_fielddefs as $key => $def )
        {
            if ( AbstractMetaDataImplementation::validField ($def) && strcmp ( strtolower($key) , 'deleted' ) != 0)
			{
                $fielddefs [ strtolower ( $key ) ] = $def ;
            }
        }
        $this->_fielddefs = $this->mergeFieldDefinitions ( $this->_viewdefs, $fielddefs );
	}

    /*
     * Deploy the layout
     * @param boolean $populate If true (default), then update the layout first with new layout information from the $_REQUEST array
     */
    function handleSave ($populate = true)
    {
        if ($populate)
            $this->_populateFromRequest () ;
        $this->implementation->deploy ( array_change_key_case ( $this->_viewdefs, CASE_UPPER ) ) ; // force the field names back to upper case so the list view will work correctly
    }

    function getLayout ()
    {
        return $this->_viewdefs ;
    }

    /**
     * Return a list of the default fields for a listview
     * TODO: have this return just a list of fields, without definitions
     * @return array    List of default fields as an array, where key = value = <field name>
     */
    function getDefaultFields ()
    {
        $defaultFields = array ( ) ;
        foreach ( $this->_viewdefs as $key => $def )
        {
            // add in the default fields from the listviewdefs
            if (! empty ( $def [ 'default' ] ))
            {
                $defaultFields [ strtolower ( $key ) ] = $this->_viewdefs [ $key ] ;
            }
        }

        return $defaultFields ;
    }

    /**
     * Returns additional fields available for users to create fields
     * TODO: have this return just a list of fields, without definitions
      @return array    List of additional fields as an array, where key = value = <field name>
     */
    function getAdditionalFields ()
    {
        $additionalFields = array ( ) ;
        foreach ( $this->_viewdefs as $key => $def )
        {
            if (empty ( $def [ 'default' ] ))
            {
                $additionalFields [ strtolower ( $key ) ] = $this->_viewdefs [ $key ] ;
            }
        }
        return $additionalFields ;
    }

    /**
     * Returns unused fields that are available for use in either default or additional list views
     * @return array    List of available fields as an array, where key = value = <field name>
     */
    function getAvailableFields ()
    {
        $availableFields = array ( ) ;
        // Select available fields from the field definitions - don't need to worry about checking if ok to include as the Implementation has done that already in its constructor
        foreach ( $this->_fielddefs as $key => $def )
        {
            //Skip field types which are not allowed on list views
            if(!empty($def['type']) && ($def['type'] == 'html' || ($def['type'] == 'parent' && !$this->allowParent)))
                continue;
            if(strtolower ( $key ) == 'currency_id')
                continue;
            $label = isset ( $def [ 'vname' ] ) ? $def [ 'vname' ] : (isset($def['name'])?$def['name']:$key) ; // check for $def['name'] as the buttons in the field_defs don't have this set
            $availableFields [ strtolower ( $key ) ] = array ( 'width' => '10' , 'label' => $label ) ;
        }
        $GLOBALS['log']->debug(get_class($this).'->getAvailableFields(): '.print_r($availableFields,true));
        // now remove all fields that are already in the viewdef - they are not available; they are in use
        return ListLayoutMetaDataParser::getArrayDiff ( $availableFields, $this->_viewdefs) ;
    }

    protected function _populateFromRequest ()
    {
        $GLOBALS [ 'log' ]->debug ( get_class ( $this ) . "->populateFromRequest()" ) ;
        $GLOBALS [ 'log' ]->debug ( get_class ( $this ) . "->populateFromRequest() - fielddefs = ".print_r($this->_fielddefs, true));
        // Transfer across any reserved fields, that is, any where studio !== true, which are not editable but must be preserved
        $newViewdefs = array ( ) ;
        foreach ( $this->_viewdefs as $key => $def )
        {
            if (isset ( $def [ 'studio' ] ) && $def [ 'studio' ] !== true)
                $newViewdefs [ $key ] = $def ;
        }

        // only take items from group_0 for searchviews (basic_search or advanced_search) and subpanels (which both are missing the Available column) - take group_0, _1 and _2 for all other list views
        $lastGroup = (isset ( $this->columns [ 'LBL_AVAILABLE' ] )) ? 2 : 1 ;
        for ( $i = 0 ; isset ( $_POST [ 'group_' . $i ] ) && $i < $lastGroup ; $i ++ )
        {
            foreach ( $_POST [ 'group_' . $i ] as $fieldname )
            {
                $fieldname = strtolower ( $fieldname ) ;

                if (isset ( $this->_viewdefs [ $fieldname ] ))
                {
                    $newViewdefs [ $fieldname ] = $this->_viewdefs [ $fieldname ] ;
                } else if (isset($this->originalViewDef) && isset ( $this->originalViewDef [ $fieldname ] ))
                {
                	$newViewdefs [ $fieldname ] = $this->originalViewDef [ $fieldname ] ;
                }
                else
                {
                    //TODO: note that we check isset ( $this->_viewdefs...) above, but then reference $this->_fielddefs below - some fields (e.g, team_id) might be in the viewdefs but not in the fielddefs and might not have a label in either...
                    $newViewdefs [ $fieldname ] = array ( 'width' => 10 , $this->labelIdentifier => $this->_fielddefs [ $fieldname ] [ 'vname' ] ) ;
                   	// jchi #19792 , TODO
                     if($fieldname == 'name' || $fieldname == 'full_name'){
                     	$newViewdefs [ $fieldname ] = $this->_fielddefs [ $fieldname ]; 
                     	$newViewdefs [ $fieldname ]['width'] = 10;
                     	$newViewdefs [ $fieldname ]['link'] = true;
                     	$newViewdefs [ $fieldname ]["$this->labelIdentifier"] = $this->_fielddefs [ $fieldname ] [ 'vname' ];
                     	if(isset($this->_fielddefs [ $fieldname ]['fields']) && isset($this->_fielddefs [ $fieldname ]['group'])){
                     		$newViewdefs [ $fieldname ]['related_fields'] = $this->_fielddefs [ $fieldname ]['fields'];
                     		$newViewdefs [ $fieldname ]['orderBy'] = $this->_fielddefs [ $fieldname ]['group'];
                     	}
                    }
                }
                // sorting fields of certain types will cause a database engine problems
                if (isset ( $this->_fielddefs [ $fieldname ] ) && (isset($this->_fielddefs[$fieldname]['type'])
                	&& in_array ( $this->_fielddefs [ $fieldname ] [ 'type' ], array ( 'html' , 'text' ) )))
                {
                    $newViewdefs [ $fieldname ] [ 'sortable' ] = false ;
                }
                // Bug 23728 - Make adding a currency type field default to setting the 'currency_format' to true
                if (isset ( $this->_fielddefs [ $fieldname ] [ 'type' ]) && $this->_fielddefs [ $fieldname ] [ 'type' ] == 'currency')
                {
                    $newViewdefs [ $fieldname ] [ 'currency_format' ] = true;
                }

                if (isset ( $_REQUEST [ strtolower ( $fieldname ) . 'width' ] ))
                {
                    $width = substr ( $_REQUEST [ $fieldname . 'width' ], 6, 3 ) ;
                    if (strpos ( $width, "%" ) != false)
                    {
                        $width = substr ( $width, 0, 2 ) ;
                    }
                    if ($width < 101 && $width > 0)
                    {
                        $newViewdefs [ $fieldname ] [ 'width' ] = $width."%" ;
                    }
                } else if (isset ( $this->_viewdefs [ $fieldname ] [ 'width' ] ))
                {
                    $newViewdefs [ $fieldname ] [ 'width' ] = $this->_viewdefs [ $fieldname ] [ 'width' ] ;
                }

                $newViewdefs [ $fieldname ] [ 'default' ] = ($i == 0) ;
            }
        }
        $this->_viewdefs = $newViewdefs ;

    }

    /*
     * Remove all instances of a field from the layout
     * @param string $fieldName Name of the field to remove
     * @return boolean True if the field was removed; false otherwise
     */
    function removeField ($fieldName)
    {
        if (isset ( $this->_viewdefs [ $fieldName ] ))
        {
            unset( $this->_viewdefs [ $fieldName ] )  ;
            return true ;
        }
        return false ;
    }

}
