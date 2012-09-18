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

abstract class AbstractMetaDataParser
{
    
    protected $implementation ; // the DeployedMetaDataImplementation or UndeployedMetaDataImplementation object to handle the reading and writing of files and field data
    
    function getLayoutAsArray ()
    {
        $viewdefs = $this->_panels ;
    }

    function getLanguage ()
    {
        return $this->implementation->getLanguage () ;
    }

    function getHistory ()
    {
        return $this->implementation->getHistory () ;
    }
    
    /* 
     * Create a merged list of field definitions - the definition comes from the module, overridden by any values set in the viewdef for the field
     * @return array Merged field definitions
     */
    function mergeFieldDefinitions ( $viewdefs, $fielddefs )
    {
        foreach ( $viewdefs as $fieldname => $def )
        {
            if (isset ( $fielddefs [ $fieldname ] ))
            {
                $fielddef = $fielddefs [ $fieldname ] ;
                // set the vname if it doesn't already exist - set in def so the array merge will preserve it
                //jchi #24930, add fielddef['vname'] here for we introduce SearchField.php into our fileddefs array.
                if (! isset ( $def [ 'vname' ] ))
                    $def [ 'vname' ] = (isset ( $fielddef [ 'label' ] )) ? $fielddef [ 'label' ] : (isset($fielddef [ 'name' ])? $fielddef [ 'name' ]: (isset($fielddef [ 'vname' ])? $fielddef [ 'vname' ] : 'LBL_NO_LABEL_FOR_THIS_FIELD'));
                $fielddefs [ $fieldname ] = array_merge ( $fielddefs [ $fieldname ], $def ) ;
            } else
                $fielddefs [ $fieldname ] = $def ;
        }
        // finally tidy up the vname entries - do this here as so many downstream methods rely on vname being set correctly
        foreach ( $fielddefs as $fieldname => $def )
        {
            if (! isset ( $def [ 'vname' ] ) ) {
                $fielddefs [ $fieldname ] [ 'vname' ] = (!empty( $def [ 'name' ])) ? $def [ 'name' ] : $fielddefs [ $fieldname ] [ 'vname' ] = $fieldname ;
            } 
        }
        return $fielddefs ;
    }
}
?>
