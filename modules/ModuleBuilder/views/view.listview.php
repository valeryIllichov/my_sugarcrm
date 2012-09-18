<?php
if (! defined ( 'sugarEntry' ) || ! sugarEntry)
    die ( 'Not A Valid Entry Point' ) ;
/**
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
 */




require_once ('include/MVC/View/views/view.edit.php') ;
require_once ('include/Sugar_Smarty.php') ;

class ViewListView extends ViewEdit
{

    function ViewListView ()
    {
        $this->init () ;
    }

    /*
     * Pseudo-constructor to enable subclasses to call a parent's constructor without knowing the parent in PHP4
     */
    function init ()
    {
        $this->editModule = $_REQUEST [ 'view_module' ] ;
        $this->editLayout = $_REQUEST [ 'view' ] ;
        $this->subpanel = (! empty ( $_REQUEST [ 'subpanel' ] )) ? $_REQUEST [ 'subpanel' ] : false ;
        $this->subpanelLabel = (! empty ( $_REQUEST [ 'subpanelLabel' ] )) ? $_REQUEST [ 'subpanelLabel' ] : false ;

        $this->fromModuleBuilder = ! empty ( $_REQUEST [ 'view_package' ] ) ;
        if (! $this->fromModuleBuilder)
        {
            $moduleNames = array_change_key_case ( $GLOBALS['app_list_strings'] [ 'moduleList' ] ) ;
            $this->translatedEditModule = $moduleNames [ strtolower ( $this->editModule ) ] ;
        }
    }

    // DO NOT REMOVE - overrides parent ViewEdit preDisplay() which attempts to load a bean for a non-existent module
    function preDisplay ()
    {
    }

    function display ($preview = false)
    {

        $packageName = (! empty ( $_REQUEST [ 'view_package' ] )) ? $_REQUEST [ 'view_package' ] : null ;
        $subpanelName = (! empty ( $_REQUEST [ 'subpanel' ] )) ? $_REQUEST [ 'subpanel' ] : null ;
        require_once 'modules/ModuleBuilder/parsers/ParserFactory.php' ;
        $parser = ParserFactory::getParser ( 'listview', $_REQUEST [ 'view_module' ], $packageName, $subpanelName ) ;
		/* BEGIN - SECURITY GROUPS */ 
		$groupLayout = $_REQUEST['grpLayout'];
		global $groupName;
		$groupName = "Default";
		if(!isset($groupLayout) || empty($groupLayout)) {
			$groupLayout = "";
		} else {
			//Get group name for display
			require_once('modules/SecurityGroups/SecurityGroup.php');
			$groupFocus = new SecurityGroup();
			$groupFocus->retrieve($groupLayout);
			$groupName = $groupFocus->name;
		}
		/* END - SECURITY GROUPS */
        $smarty = $this->constructSmarty ( $parser ) ;
        $smarty->assign ( 'grpLayout', $groupLayout ) ; //SECURITY GROUPS

        if ($preview)
        {
            echo $smarty->fetch ( "modules/ModuleBuilder/tpls/Preview/listView.tpl" ) ;
        } else
        {
            $ajax = $this->constructAjax () ;
            $ajax->addSection ( 'center', 'no_change', $smarty->fetch ( "modules/ModuleBuilder/tpls/listView.tpl" ) ) ;

            echo $ajax->getJavascript () ;
        }
    }

    function constructAjax ()
    {
        require_once ('modules/ModuleBuilder/MB/AjaxCompose.php') ;
        $ajax = new AjaxCompose ( ) ;

        if ($this->fromModuleBuilder)
        {
            $ajax->addCrumb ( translate ( 'LBL_MODULEBUILDER', 'ModuleBuilder' ), 'ModuleBuilder.main("mb")' ) ;
            $ajax->addCrumb ( $_REQUEST [ 'view_package' ], 'ModuleBuilder.getContent("module=ModuleBuilder&action=package&view_package=' . $_REQUEST [ 'view_package' ] . '")' ) ;
            $ajax->addCrumb ( $this->editModule, 'ModuleBuilder.getContent("module=ModuleBuilder&action=module&view_package=' . $_REQUEST [ 'view_package' ] . '&view_module=' . $_REQUEST [ 'view_module' ] . '")' ) ;
            $ajax->addCrumb ( translate ( 'LBL_LAYOUTS', 'ModuleBuilder' ), 'ModuleBuilder.getContent("module=ModuleBuilder&MB=true&action=wizard&view_module=' . $_REQUEST [ 'view_module' ] . '&view_package=' . $_REQUEST [ 'view_package' ] . '")' ) ;
            if ($this->subpanel != "")
            {
                $ajax->addCrumb ( translate ( 'LBL_SUBPANELS', 'ModuleBuilder' ), '' ) ;
                if ($this->subpanelLabel)
                {
                    $ajax->addCrumb ( $this->subpanelLabel, '' ) ;
                } else
                {
                    $ajax->addCrumb ( $this->subpanel, '' ) ;
                }
            } else
            {
                $ajax->addCrumb ( 'ListView', 'ModuleBuilder.getContent("module=ModuleBuilder&MB=true&action=editLayout&view=ListView&view_module=' . $this->editModule . '&view_package=' . $_REQUEST [ 'view_package' ] . '")' ) ;
            }
        } else
        {
            $ajax->addCrumb ( translate ( 'LBL_STUDIO', 'ModuleBuilder' ), 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard")' ) ;
            $ajax->addCrumb ( $this->translatedEditModule, 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view_module=' . $this->editModule . '")' ) ;

            if ($this->subpanel)
            {
                $ajax->addCrumb ( translate ( 'LBL_SUBPANELS', 'ModuleBuilder' ), 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&subpanels=1&view_module=' . $this->editModule . '")' ) ;
                if ($this->subpanelLabel)
                {
                    $ajax->addCrumb ( $this->subpanelLabel, 'ModuleBuilder.getContent("module=ModuleBuilder&action=editLayout&view=ListView&view_module=' . $this->editModule . '&subpanel=' . $this->subpanel . '&subpanelLabel=' . $this->subpanelLabel . '")' ) ;
                } else
                {
                    $ajax->addCrumb ( $this->subpanel, 'ModuleBuilder.getContent("module=ModuleBuilder&action=editLayout&view=ListView&view_module=' . $this->editModule . '&subpanel=' . $this->subpanel . '")' ) ;
                }
            } else
            {
                $ajax->addCrumb ( translate ( 'LBL_LAYOUTS', 'ModuleBuilder' ), 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&layouts=1&view_module=' . $this->editModule . '")' ) ;
				/* BEGIN - SECURITY GROUPS */ 
				global $groupName;
				$ajax->addCrumb ( translate ( $groupName ), '' ) ;
				/* END - SECURITY GROUPS */ 
                $ajax->addCrumb ( translate ( 'LBL_LISTVIEW', 'ModuleBuilder' ), 'ModuleBuilder.getContent("module=ModuleBuilder&action=editLayout&view=ListView&view_module=' . $this->editModule . '")' ) ;
            }
        }
        return $ajax ;
    }

    function constructSmarty ($parser)
    {
        $smarty = new Sugar_Smarty ( ) ;
        $smarty->assign ( 'translate', true ) ;
        $smarty->assign ( 'language', $parser->getLanguage () ) ;

        $smarty->assign ( 'view', 'listView' ) ;
        $smarty->assign ( 'action', 'listViewSave' ) ;
        $smarty->assign ( 'view_module', $this->editModule ) ;
        if (isset ( $this->subpanel ))
        {
            $smarty->assign ( 'submodule', $this->subpanel ) ;
        }
        $helpName = $this->subpanel ? 'subPanelEditor' : 'listViewEditor';
        $smarty->assign ( 'helpName', $helpName ) ;
        $smarty->assign ( 'helpDefault', 'modify' ) ;

        $smarty->assign ( 'title', $this->_constructTitle () ) ;
        $groups = array ( ) ;
        foreach ( $parser->columns as $column => $function )
        {
            // update this so that each field has a properties set
            // properties are name, value, title (optional)
            $groups [ $GLOBALS [ 'mod_strings' ] [ $column ] ] = $parser->$function () ; // call the parser functions to populate the list view columns, by default 'default', 'available' and 'hidden'
        }
        foreach ( $groups as $groupKey => $group )
        {
            foreach ( $group as $fieldKey => $field )
            {
                if (isset ( $field [ 'width' ] ))
                {
                    if (substr ( $field [ 'width' ], - 1, 1 ) == '%')
                    {

                        $groups [ $groupKey ] [ $fieldKey ] [ 'width' ] = substr ( $field [ 'width' ], 0, strlen ( $field [ 'width' ] ) - 1 ) ;
                    }
                }
            }
        }

        $smarty->assign ( 'groups', $groups ) ;
        $smarty->assign('from_mb', $this->fromModuleBuilder);

        global $image_path ;
        $imageSave = get_image ( $image_path . 'studio_save', '' ) ;
//        $imageHelp = get_image ( $image_path . 'help', '' ) ;

        $histaction = "ModuleBuilder.history.browse(\"{$this->editModule}\", \"{$this->editLayout}\")" ;

        if ($this->subpanel)
            $histaction = "ModuleBuilder.history.browse(\"{$this->editModule}\", \"{$this->editLayout}\", \"{$this->subpanel}\")" ;

        $buttons = array ( ) ;
        if (! $this->fromModuleBuilder)
        {
            $buttons [] = array ( 'id' =>'savebtn', 'name' => 'savebtn' , 'image' => $imageSave , 'text' => $GLOBALS [ 'mod_strings' ] [ 'LBL_BTN_SAVEPUBLISH' ] , 'actionScript' => "onclick='studiotabs.generateGroupForm(\"edittabs\");ModuleBuilder.state.isDirty=false;ModuleBuilder.submitForm(\"edittabs\" )'" ) ;
        } else
        {
            $buttons [] = array ( 'id'=>'savebtn','name' => 'savebtn' , 'image' => $imageSave , 'text' => $GLOBALS [ 'mod_strings' ] [ 'LBL_BTN_SAVE' ] , 'actionScript' => "onclick='studiotabs.generateGroupForm(\"edittabs\");ModuleBuilder.state.isDirty=false;ModuleBuilder.submitForm(\"edittabs\" )'" ) ;
        }
        if(!isset($_REQUEST['grpLayout'])) //SECURITY GROUPS - don't support history for group layouts...too much work
        $buttons [] = array ( 'id'=>'historyBtn', 'name' => 'historyBtn' , 'text' => translate ( 'LBL_HISTORY' ) , 'actionScript' => "onclick='$histaction'" ) ;
        $smarty->assign ( 'buttons', $this->_buildImageButtons ( $buttons ) ) ;
        $editImage = get_image ( $image_path . 'edit_inline', '' ) ;
        $smarty->assign ( 'editImage', $editImage ) ;
        $deleteImage = get_image ( $image_path . 'delete_inline', '' ) ;
        $smarty->assign ( 'deleteImage', $deleteImage ) ;
        $smarty->assign ( 'MOD', $GLOBALS [ 'mod_strings' ] ) ;

        if ($this->fromModuleBuilder)
        {
            $smarty->assign ( 'MB', true ) ;
            $smarty->assign ( 'view_package', $_REQUEST [ 'view_package' ] ) ;

            if ($this->subpanel)
            {
                if (isset ( $_REQUEST [ 'local' ] ))
                {
                    $smarty->assign ( 'local', '1' ) ;
                }
                $smarty->assign ( "subpanel", "&subpanel={$this->subpanel}" ) ;
            } else
            {
                $smarty->assign ( 'description', $GLOBALS [ 'mod_strings' ] [ 'LBL_LISTVIEW_DESCRIPTION' ] ) ;

            }

        } else
        {
            if ($this->subpanel)
            {
                $smarty->assign ( "module", $this->subpanel ) ;
                $smarty->assign ( "subpanel", "&subpanel={$this->subpanel}" ) ;
            } else
            {
                $smarty->assign ( 'description', $GLOBALS [ 'mod_strings' ] [ 'LBL_LISTVIEW_DESCRIPTION' ] ) ;
            }
        }

        return $smarty ;
    }

    function _constructTitle ()

    {

        global $app_list_strings ;

        if ($this->fromModuleBuilder)
        {
            $title = $this->editModule ;
            if ($this->subpanel != "")
            {
                $title .= ":$this->subpanel" ;
            }
        } else
        {
            $title = ($this->subpanel) ? ':' . $this->subpanel : $app_list_strings [ 'moduleList' ] [ $this->editModule ] ;
        }
        return $GLOBALS [ 'mod_strings' ] [ 'LBL_LISTVIEW_EDIT' ] . ':&nbsp;' . $title ;

    }

    function _buildImageButtons ($buttons , $horizontal = true)
    {
    	$text = '<table cellspacing=2><tr>' ;
        foreach ( $buttons as $button )
        {
            if (empty($button['id'])) {
            	$button['id'] = $button['name'];
            }
        	if (! $horizontal)
            {
                $text .= '</tr><tr>' ;
            }
            if (! empty ( $button [ 'plain' ] ))
            {
                $text .= <<<EOQ
	             <td><input name={$button['name']} id={$button['id']} class="button" type="button" valign='center' {$button['actionScript']}
EOQ;

            } else
            {
                $text .= <<<EOQ
	          <td><input name={$button['name']} id={$button['id']} class="button" type="button" valign='center' style='cursor:default'  {$button['actionScript']}
EOQ;
            }
            $text .= "value=\"{$button['text']}\"/></td>" ;
        }
        $text .= '</tr></table>' ;
        return $text ;
    }

}

?>
