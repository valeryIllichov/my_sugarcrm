<?php
if (! defined ( 'sugarEntry' ) || ! sugarEntry)
    die ( 'Not A Valid Entry Point' ) ;
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
require_once 'modules/ModuleBuilder/parsers/constants.php' ;

class GridLayoutMetaDataParser extends AbstractMetaDataParser implements MetaDataParserInterface
{

    static $variableMap = array ( MB_EDITVIEW => 'EditView' , MB_DETAILVIEW => 'DetailView' , MB_QUICKCREATE => 'QuickCreate' ) ;
	private $FILLER , $EMPTY ;

    /*
     * Constructor
     * @param string view           The view type, that is, editview, searchview etc
     * @param string moduleName     The name of the module to which this view belongs
     * @param string packageName    If not empty, the name of the package to which this view belongs
     */
    function __construct ($view , $moduleName , $packageName = '')
    {
        $GLOBALS [ 'log' ]->debug ( get_class ( $this ) . "->__construct( {$view} , {$moduleName} , {$packageName} )" ) ;
        $view = strtolower ( $view ) ;
		$this->FILLER = array ( 'name' => MBConstants::$FILLER['name'] , 'label' => translate ( MBConstants::$FILLER['label'] ) ) ;
		$this->EMPTY = MBConstants::$EMPTY ;

        // BEGIN ASSERTIONS
        if ($view != MB_EDITVIEW && $view != MB_DETAILVIEW && $view != MB_QUICKCREATE)
        {
            sugar_die ( get_class ( $this ) . ": View $view is not supported" ) ;
        }
        // END ASSERTIONS


        $this->_moduleName = $moduleName ;
        $this->_view = $view ;

        if (empty ( $packageName ))
        {
            require_once 'modules/ModuleBuilder/parsers/views/DeployedMetaDataImplementation.php' ;
            $this->implementation = new DeployedMetaDataImplementation ( $view, $moduleName, self::$variableMap ) ;
			$this->originalViewDef = $this->implementation->getOriginalDefs ();
			if (!empty($this->originalViewDef))
			{
				$this->originalViewDef = $this->originalViewDef[self::$variableMap [ $view ]];
				if (!empty($this->originalViewDef [ 'panels' ]))
				{
					$this->originalViewDef [ 'panels' ] = $this->_convertFromCanonicalForm ( $this->originalViewDef [ 'panels' ] ) ;
				}
			}
			#jchi 17493
			$this->_beanName = get_valid_bean_name($moduleName);
			VardefManager::refreshVardefs($this->_moduleName,$this->_beanName);
			$this->_moduleVardefs = $GLOBALS['dictionary']["$this->_beanName"];
        } else
        {
            require_once 'modules/ModuleBuilder/parsers/views/UndeployedMetaDataImplementation.php' ;
            $this->implementation = new UndeployedMetaDataImplementation ( $view, $moduleName, $packageName ) ;
			//jchi 17493
			require_once ('modules/ModuleBuilder/MB/ModuleBuilder.php');
	        $mb = new ModuleBuilder();
	        $mbmodule = & $mb->getPackageModule($packageName, $moduleName);
			$this->_moduleVardefs = $mbmodule->getVardefs(true);
        }

        $viewdefs = $this->implementation->getViewdefs () ;

        if (! isset ( $viewdefs [ self::$variableMap [ $view ] ] ))
        {
            sugar_die ( get_class ( $this ) . ": missing variable " . self::$variableMap [ $view ] . " in layout definition" ) ;
        }
        $viewdefs = $viewdefs [ self::$variableMap [ $view ] ] ;
        if (! isset ( $viewdefs [ 'templateMeta' ] ))
        {
            sugar_die ( get_class ( $this ) . ": missing templateMeta section in layout definition (case sensitive)" ) ;
        }
        if (! isset ( $viewdefs [ 'panels' ] ))
        {
            sugar_die ( get_class ( $this ) . ": missing panels section in layout definition (case sensitive)" ) ;
        }

        $this->_viewdefs = $viewdefs ;
        if ($this->getMaxColumns () < 1)
        {
            sugar_die ( get_class ( $this ) . ": maxColumns=" . $this->getMaxColumns () . " - must be greater than 0!" ) ;
        }

        $this->_fielddefs = $this->implementation->getModuleFielddefs () ;
        $this->_viewdefs [ 'panels' ] = $this->_convertFromCanonicalForm ( $this->_viewdefs [ 'panels' ] ) ; // put into our internal format


		$this->loadFieldDefs();
    }


	/**
	 * Loads field defs first from the original defs, then from the module's field_defs
	 * @return
	 */
	function loadFieldDefs()
	{
		$fielddefs = array ( ) ;
		//load all the definitions first from the original view definition
		if (!empty ( $this->originalViewDef ) && !empty($this->originalViewDef [ 'panels' ]))
        {
            foreach ( $this->originalViewDef [ 'panels' ] as $panel )
            {
                foreach ( $panel as $row )
                {
                    foreach ( $row as $fieldArray )
                    { // fieldArray is an array('name'=>name,'label'=>label)
                        if (isset ( $fieldArray [ 'name' ] ) )
                        {
                            $fielddefs[strtolower($fieldArray [ 'name' ])] = $fieldArray;
                        }
                    }
                }
            }
        }
		// Run through the preliminary list, keeping only those fields that are valid to include in a layout
        // Do this after we've standardized the layout as some fields may not be 'valid' but are in the original layoutdefs;
		// in this case, if they lack a label we need the field defs to find the right one
        foreach ( $this->_fielddefs as $key => $def )
        {
            if (AbstractMetaDataImplementation::validField ( $def )
				&& strcmp ( strtolower ( $key ), 'deleted' ) != 0
				&& !isset($fielddefs[strtolower($key)]))
            {
                $fielddefs [ strtolower ( $key ) ] = $def;
            }
        }
		$this->_fielddefs = $fielddefs ;
	}

    /*
     * Save a draft layout
     */
    function writeWorkingFile ()
    {
        $this->_populateFromRequest () ;

        $this->_viewdefs [ 'panels' ] = $this->_convertToCanonicalForm ( $this->_viewdefs [ 'panels' ] ) ;

        $this->implementation->save ( array ( self::$variableMap [ $this->_view ] => $this->_viewdefs ) ) ;
    }

    /*
     * Deploy the layout
     * @param boolean $populate If true (default), then update the layout first with new layout information from the $_REQUEST array
     */
    function handleSave ($populate = true)
    {
    	$GLOBALS [ 'log' ]->info ( get_class ( $this ) . "->handleSave()" ) ;
        if ($populate)
            $this->_populateFromRequest () ;

        $this->_viewdefs [ 'panels' ] = $this->_convertToCanonicalForm ( $this->_viewdefs [ 'panels' ] ) ;

        $this->implementation->deploy ( array ( self::$variableMap [ $this->_view ] => $this->_viewdefs ) ) ;
    }

    /*
     * Return the layout, padded out with (empty) and (filler) fields ready for display
     */
    function getLayout ()
    {
        return $this->_viewdefs [ 'panels' ] ;
    }

    function getMaxColumns ()
    {
        if (!empty( $this->_viewdefs) && isset($this->_viewdefs [ 'templateMeta' ] [ 'maxColumns' ]))
		{
			return $this->_viewdefs [ 'templateMeta' ] [ 'maxColumns' ] ;
		}else
		{
			return 2;
		}
    }

    function getAvailableFields ()
    {

        $availableFields = array ( ) ;
        foreach ( $this->_fielddefs as $field => $def )
        {
            $label = $field;
            $name = $def['name'];
			if (!empty($def['label']))
			{
				$label = $def ['label'];
			}
			else if (!empty($def['vname']))
			{
				$label = $def['vname'];
			}else if(!empty($this->_moduleVardefs['fields']["$name"]['vname'])){
				$label = $this->_moduleVardefs['fields']["$name"]['vname'];
			}

            $availableFields [ $field ] = array ( 'name' => $field , 'label' => $label ) ;
        }

		// Available fields are those that are in the Model and the original layout definition, but not already shown in the View
        // So, because the formats of the two are different we brute force loop through View and unset the fields we find in a copy of Model
        if (! empty ( $this->_viewdefs ))
        {
            foreach ( $this->_viewdefs [ 'panels' ] as $panel )
            {
                foreach ( $panel as $row )
                {
                    foreach ( $row as $fieldArray )
                    { // fieldArray is an array('name'=>name,'label'=>label)
                        if (isset ( $fieldArray [ 'name' ] ))
                        {
                            unset ( $availableFields [ $fieldArray [ 'name' ] ] ) ;
                        }
                    }
                }
            }
        }

        return $availableFields ;
    }

    /*
     * Add a new field to the layout
     * If $panelID is passed in, attempt to add to that panel, otherwise add to the first panel
     * The field is added in place of the first empty (not filler) slot after the last field in the panel; if that row is full, then a new row will be added to the end of the panel
     * and the field added to the start of it.
     * @param array $properties Set of properties for the field, in same format as in the viewdefs
     * @param string $panelID Identifier of the panel to add the field to; empty or false if we should use the first panel
     */
    function addField ($properties , $panelID = FALSE)
    {

        if (count ( $this->_viewdefs [ 'panels' ] ) == 0)
        {
            $GLOBALS [ 'log' ]->error ( get_class ( $this ) . "->addField(): _viewdefs empty for module {$this->_moduleName} and view {$this->_view}" ) ;
        }
        // if a panelID was not provided, use the first available panel in the list
        if (! $panelID)
        {
            $panels = array_keys ( $this->_viewdefs [ 'panels' ] ) ;
            list ( $dummy, $panelID ) = each ( $panels ) ;
        }

        if (isset ( $this->_viewdefs [ 'panels' ] [ $panelID ] ))
        {

            $panel = $this->_viewdefs [ 'panels' ] [ $panelID ] ;
            $lastrow = count ( $panel ) - 1 ; // index starts at 0
            $maxColumns = $this->getMaxColumns () ;
            for ( $column = 0 ; $column < $maxColumns ; $column ++ )
            {
                if (! isset ( $this->_viewdefs [ 'panels' ] [ $panelID ] [ $lastrow ] [ $column ] ) || ($this->_viewdefs [ 'panels' ] [ $panelID ] [ $lastrow ] [ $column ] [ 'name' ] == '(empty)'))
                    break ;
            }

            // if we're on the last column of the last row, start a new row
            if ($column >= $maxColumns)
            {
                $lastrow ++ ;
                $this->_viewdefs [ 'panels' ] [ $panelID ] [ $lastrow ] = array ( ) ;
                $column = 0 ;
            }

            $this->_viewdefs [ 'panels' ] [ $panelID ] [ $lastrow ] [ $column ] = $properties ;
        }
        return true ;
    }

    /*
     * Remove all instances of a field from the layout, and replace by (filler)
     * Filler because we attempt to preserve the customized layout as much as possible - replacing by (empty) would mean that the positions or sizes of adjacent fields may change
     * If the last row of a panel only consists of (filler) after removing the fields, then remove the row also. This undoes the standard addField() scenario;
     * If the fields had been moved around in the layout however then this will not completely undo any addField()
     * @param string $fieldName Name of the field to remove
     * @return boolean True if the field was removed; false otherwise
     */
    function removeField ($fieldName)
    {
        $GLOBALS [ 'log' ]->info ( get_class ( $this ) . "->removeField($fieldName)" ) ;

        $result = false ;
        reset ( $this->_viewdefs ) ;
        $firstPanel = each ( $this->_viewdefs [ 'panels' ] ) ;
        $firstPanelID = $firstPanel [ 'key' ] ;

        foreach ( $this->_viewdefs [ 'panels' ] as $panelID => $panel )
        {
            $lastRowTouched = false ;
            $lastRowID = count ( $this->_viewdefs [ 'panels' ] [ $panelID ] ) - 1 ; // zero offset

            foreach ( $panel as $rowID => $row )
            {

                foreach ( $row as $colID => $field )
                    if ($field [ 'name' ] == $fieldName)
                    {
                        $lastRowTouched = $rowID ;
                        $this->_viewdefs [ 'panels' ] [ $panelID ] [ $rowID ] [ $colID ] = $this->FILLER ;
                    }

            }

            // if we removed a field from the last row of this panel, tidy up if the last row now consists only of (empty) or (filler)

            if ( $lastRowTouched ==  $lastRowID )
            {
                $lastRow = end ( $this->_viewdefs [ 'panels' ] [ $panelID ] ) ;

                $empty = true ;
                foreach ( $lastRow as $colID => $field )
                    $empty &=  $field ['name' ] == $this->EMPTY ['name' ] || $field [ 'name' ] == $this->FILLER [ 'name' ]  ;

                if ($empty)
                {
                    unset ( $this->_viewdefs [ 'panels' ] [ $panelID ] [ $lastRowID ] ) ;
                    // if the row was the only one in the panel, and the panel is not the first (default) panel, then remove the panel also
					if ( count ( $this->_viewdefs [ 'panels' ] [ $panelID ] ) == 0 && $panelID != $firstPanelID )
						unset ( $this->_viewdefs [ 'panels' ] [ $panelID ] ) ;
                }

            }

            $result |= ($lastRowTouched !== false ); // explicitly compare to false as row 0 will otherwise evaluate as false
        }

        return $result ;

    }

    /*
     * Return an integer value for the next unused panel identifier, such that it and any larger numbers are guaranteed to be unused already in the layout
     * Necessary when adding new panels to a layout
     * @return integer First unique panel ID suffix
     */
    function getFirstNewPanelId ()
    {
        $firstNewPanelId = 0 ;
        foreach ( $this->_viewdefs [ 'panels' ] as $panelID => $panel )
        {
            // strip out all but the numerics from the panelID - can't just use a cast as numbers may not be first in the string
            for ( $i = 0, $result = '' ; $i < strlen ( $panelID ) ; $i ++ )
            {
                if (is_numeric ( $panelID [ $i ] ))
                {
                    $result .= $panelID [ $i ] ;
                }
            }

            $firstNewPanelId = max ( ( int ) $result, $firstNewPanelId ) ;
        }
        return $firstNewPanelId + 1 ;
    }

    /*
     * Load the panel layout from the submitted form and update the _viewdefs
     */
    private function _populateFromRequest ()
    {
        $GLOBALS [ 'log' ]->debug ( get_class ( $this ) . "->populateFromRequest()" ) ;
        $i = 1 ;

        // set up the map of panel# (as provided in the _REQUEST) to panel ID (as used in $this->_viewdefs['panels'])
        $i = 1 ;
        foreach ( $this->_viewdefs [ 'panels' ] as $panelID => $panel )
        {
            $panelMap [ $i ++ ] = $panelID ;
        }

        foreach ( $_REQUEST as $key => $displayLabel )
        {
            $components = explode ( '-', $key ) ;
            if ($components [ 0 ] == 'panel')
            {
                $panelMap [ $components [ '1' ] ] = $displayLabel ;
            }
        }

        $origFieldDefs = $this->_getFieldDefs ( $this->_viewdefs [ 'panels' ] ) ;
        $GLOBALS [ 'log' ]->debug ( print_r ( $origFieldDefs, true ) ) ;

        $this->_viewdefs [ 'panels' ] = array ( ) ; // because the new field properties should replace the old fields, not be merged
        foreach ( $_REQUEST as $slot => $value )
        {
            $slotComponents = explode ( '-', $slot ) ; // [0] = 'slot', [1] = panel #, [2] = slot #, [3] = property name


            if ($slotComponents [ 0 ] == 'slot')
            {
                $slotNumber = $slotComponents [ '2' ] ;
                $panelID = $panelMap [ $slotComponents [ '1' ] ] ;
                $rowID = floor ( $slotNumber / $this->getMaxColumns () ) ;
                $colID = $slotNumber - ($rowID * $this->getMaxColumns ()) ;
                $property = $slotComponents [ '3' ] ;
                //If the original editview defined this field, copy that over, ignoring any (filler) values that might be in there
                if ($property == 'name' && $value != $this->FILLER['name'] && isset ( $origFieldDefs [ $value ] )
                	&& is_array ( $origFieldDefs [ $value ] ))
                {
                    $this->_viewdefs [ 'panels' ] [ $panelID ] [ $rowID ] [ $colID ] = $origFieldDefs [ $value ] ;
                } else
                {
//                    if ($value == $this->FILLER['name'])
//                    {
//                        $this->_viewdefs [ 'panels' ] [ $panelID ] [ $rowID ] [ $colID ] = NULL ;
//                    } else
//                    {
                        $this->_viewdefs [ 'panels' ] [ $panelID ] [ $rowID ] [ $colID ] [ $property ] = $value ;
//                    }
                }
            }

        }

        $GLOBALS [ 'log' ]->debug ( print_r ( $this->_viewdefs [ 'panels' ], true ) ) ;

    }

    /*  Convert our internal format back to the standard Canonical MetaData layout
     *  First non-(empty) field goes in at column 0; all other (empty)'s removed
     *  Do this AFTER reading in all the $_REQUEST parameters as can't guarantee the order of those, and we need to operate on complete rows
     */
    private function _convertToCanonicalForm ($panels)
    {

        foreach ( $panels as $panelID => $panel )
        {
            // remove all (empty)s
            foreach ( $panel as $rowID => $row )
            {
                $startOfRow = true ;
                $offset = 0 ;
                foreach ( $row as $colID => $col )
                {
                    if ($col [ 'name' ] == $this->EMPTY[ 'name' ])
                    {
                        // if a leading (empty) then remove (by noting that remaining fields need to be shuffled along)
                        if ($startOfRow)
                        {
                            $offset ++ ;
                        }
                        unset ( $row [ $colID ] ) ;
                    } else
                    {
                        $startOfRow = false ;
                        // if a (filler) element then replace by null
                        if ($col [ 'name' ] == $this->FILLER[ 'name' ])
                            $row [ $colID ] = null ;
                    }
                }

                // reindex to remove leading (empty)s
                $newRow = array ( ) ;
                foreach ( $row as $colID => $col )
                {
                    $newRow [ $colID - $offset ] = $col ;
                }
                $panels [ $panelID ] [ $rowID ] = $newRow ;
            }
        }

        return $panels ;
    }

    /*
     * Convert from the standard MetaData format to our internal format
     * Replace NULL with (filler) and missing entries with (empty)
     */

    private function _convertFromCanonicalForm ($panels)
    {
        if (empty ( $panels ))
            return ;

        // Fix for a flexibility in the format of the panel sections - if only one panel, then we don't have a panel level defined,
		// it goes straight into rows
        // See EditView2 for similar treatment
        if (! empty ( $panels ) && count ( $panels ) > 0)
        {
            $keys = array_keys ( $panels ) ;
            if (is_numeric ( $keys [ 0 ] ))
            {
                $defaultPanel = $panels ;
                unset ( $panels ) ; //blow away current value
                $panels [ 'default' ] = $defaultPanel ;
            }
        }

        $newPanels = array ( ) ;

        // replace NULL with (filler), standardize the field properties
        foreach ( $panels as $panelID => $panel )
        {
            foreach ( $panel as $rowID => $row )
            {
                foreach ( $row as $colID => $col )
                {
                    $properties = array ( ) ;

                    if (! empty ( $col ))
                    {
                        if (is_string ( $col ))
                        {
                            $properties [ 'name' ] = $col ;
                        } else if (! empty ( $col [ 'name' ] ))
                        {
                            $properties = $col ;
                        }
                    } else
                    {
                        $properties [ 'name' ] = $this->FILLER['name'] ; // was translate ('LBL_FILLER') but studio2.js relies on (filler) regardless of language...
                        $properties [ 'label' ] = translate ( $this->FILLER['label'] ) ;
                    }

                    if (! empty ( $properties [ 'name' ] ))
                    {

                        // get this field's label - if it has not been explicity provided, see if the fieldDefs has a label for this field, and if not fallback to the field name
                        if (! isset ( $properties [ 'label' ] ))
                        {
                            if (! empty ( $this->_fielddefs [ $properties [ 'name' ] ] [ 'vname' ] ))
                            {
                                $properties [ 'label' ] = $this->_fielddefs [ $properties [ 'name' ] ] [ 'vname' ] ;
                            }
                        }
                        $newPanels [ $panelID ] [ $rowID ] [ $colID ] = $properties ;

                    }
                }
            }
        }

        // replace missing fields with (empty)
        foreach ( $newPanels as $panelID => $panel )
        {
            $column = 0 ;
            foreach ( $panel as $rowID => $row )
            {
                // pad between fields on a row
                foreach ( $row as $colID => $col )
                {
                    for ( $i = $column + 1 ; $i < $colID ; $i ++ )
                    {
                        $row [ $i ] = $this->EMPTY ;
                    }
                    $column = $colID ;
                }
                // now pad out to the end of the row
                if (($column + 1) < $this->getMaxColumns ())
                { // last column is maxColumns-1
                    for ( $i = $column + 1 ; $i < $this->getMaxColumns () ; $i ++ )
                    {
                        $row [ $i ] = $this->EMPTY ;
                    }
                }
                ksort ( $row ) ;
                $newPanels [ $panelID ] [ $rowID ] = $row ;
            }
        }

        return $newPanels ;
    }

    /*
     * Generate a list of field names with associated field definitions from the viewdefs
     * Used to ensure that we don't lose any field definition information supplied in the viewdefs that may not be in either the module field definitions, or in the _POST response
     * @param array $panelDefs Panel section of the viewdefs
     * @return array Field name => Field definition
     */
    private function _getFieldDefs ($panelDefs)
    {
        $fieldDefs = array ( ) ;

        foreach ( $panelDefs as $pname => $paneldef )
        {
            foreach ( $paneldef as $row )
            {
                foreach ( $row as $fieldDef )
                {
                    $fieldDefs [ $fieldDef [ 'name' ] ] = $fieldDef ;
                }
            }
        }
        return $fieldDefs ;
    }

}

?>
