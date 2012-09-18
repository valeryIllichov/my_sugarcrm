<?php
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
require_once ('modules/ModuleBuilder/MB/ModuleBuilder.php') ;
require_once ('modules/ModuleBuilder/parsers/ParserFactory.php') ;

class ModuleBuilderController extends SugarController
{
    var $action_remap = array ( ) ;

    function fromModuleBuilder ()
    {
        return (isset ( $_REQUEST [ 'MB' ] ) && ($_REQUEST [ 'MB' ] == '1')) ;
    }

    function process(){
        global $current_user;
        $access = get_admin_modules_for_user($current_user);
        if(is_admin($current_user) || (is_admin_for_any_module($current_user) && !isset($_REQUEST['view_module']) && (isset($_REQUEST['action']) && $_REQUEST['action'] != 'package'))||
          (isset($_REQUEST['view_module']) && (in_array($_REQUEST['view_module'], $access)|| empty($_REQUEST['view_module']))) ||
          (isset($_REQUEST['type']) && (($_REQUEST['type']=='dropdowns' && is_admin_for_any_module($current_user))||
          ($_REQUEST['type']=='studio' && displayStudioForCurrentUser() == true))))
        {
            $this->hasAccess = true;
        } 
        else 
        {
            $this->hasAccess = false;
        }
        parent::process();
    }

        
    function action_editLayout ()
    {
        switch ( strtolower ( $_REQUEST [ 'view' ] ))
        {
            case 'editview' :
            case 'detailview' :
            case 'quickcreate' :
                $this->view = 'layoutView' ;
                break ;
            case 'listview' :
                $this->view = 'listView' ;
                break ;           
            case 'basic_search' :
            case 'advanced_search' :
                $this->view = 'searchView' ;
                break ;
            case 'dashlet' :
            case 'dashletsearch' :
                $this->view = 'dashlet' ;
                break ;
            default :
                $GLOBALS [ 'log' ]->fatal ( 'Action = editLayout with unknown view=' . $_REQUEST [ 'view' ] ) ;
        }
    }
    

    function action_ViewTree ()
    {
        require_once ('modules/ModuleBuilder/MB/AjaxCompose.php') ;
        switch ( $_REQUEST [ 'tree' ])
        {
            case 'ModuleBuilder' :
                require_once ('modules/ModuleBuilder/MB/MBPackageTree.php') ;
                $mbt = new MBPackageTree ( ) ;
                break ;
            case 'Studio' :
                require_once ('modules/ModuleBuilder/Module/StudioTree.php') ;
                $mbt = new StudioTree ( ) ;
        }
        $ajax = new AjaxCompose ( ) ;
        $ajax->addSection ( 'west', $mbt->getName (), $mbt->fetchNodes () ) ;
        echo $ajax->getJavascript () ;
        
        sugar_cleanup ( true ) ;
    
    }

    function action_SavePackage ()
    {
        $mb = new ModuleBuilder ( ) ;
        $load = (! empty ( $_REQUEST [ 'original_name' ] )) ? $_REQUEST [ 'original_name' ] : $_REQUEST [ 'name' ] ;
        if (! empty ( $load ))
        {
            $mb->getPackage ( $load ) ;
            
            if (! empty ( $_REQUEST [ 'duplicate' ] ))
            {
                $result = $mb->packages [ $load ]->copy ( $_REQUEST [ 'name' ] ) ;
                $load = $mb->packages [ $load ]->name ;
                $mb->getPackage ( $load ) ;
            }
            $mb->packages [ $load ]->populateFromPost () ;
            $mb->packages [ $load ]->loadModules () ;
            $mb->save () ;
            if (! empty ( $_REQUEST [ 'original_name' ] ) && $_REQUEST [ 'original_name' ] != $_REQUEST [ 'name' ])
            {
                if (! $mb->packages [ $load ]->rename ( $_REQUEST [ 'name' ] ))
                {
                    $mb->packages [ $load ]->name = $_REQUEST [ 'original_name' ] ;
                    $_REQUEST [ 'name' ] = $_REQUEST [ 'original_name' ] ;
                }
            }
            
            $_REQUEST [ 'package' ] = $_REQUEST [ 'name' ] ;
            $this->view = 'package' ;
        }
    }

    function action_BuildPackage ()
    {
        $mb = new ModuleBuilder ( ) ;
        $load = $_REQUEST [ 'name' ] ;
        if (! empty ( $load ))
        {
            $mb->getPackage ( $load ) ;
            $mb->packages [ $load ]->build () ;
        }
    }

    function action_DeployPackage ()
    {
    	if(defined('TEMPLATE_URL'))SugarTemplateUtilities::disableCache();
        $mb = new ModuleBuilder ( ) ;
        $load = $_REQUEST [ 'package' ] ;
        $message = $GLOBALS [ 'mod_strings' ] [ 'LBL_MODULE_DEPLOYED' ] ;
        if (! empty ( $load ))
        {
            $zip = $mb->getPackage ( $load ) ;
            require_once ('ModuleInstall/PackageManager/PackageManager.php') ;
            $pm = new PackageManager ( ) ;
            $info = $mb->packages [ $load ]->build ( false ) ;
            mkdir_recursive ( $GLOBALS [ 'sugar_config' ] [ 'cache_dir' ] . '/upload/upgrades/module/') ;
            rename ( $info [ 'zip' ], $GLOBALS [ 'sugar_config' ] [ 'cache_dir' ] . '/' . 'upload/upgrades/module/' . $info [ 'name' ] . '.zip' ) ;
            copy ( $info [ 'manifest' ], $GLOBALS [ 'sugar_config' ] [ 'cache_dir' ] . '/' . 'upload/upgrades/module/' . $info [ 'name' ] . '-manifest.php' ) ;
            $_REQUEST [ 'install_file' ] = $GLOBALS [ 'sugar_config' ] [ 'cache_dir' ] . '/' . 'upload/upgrades/module/' . $info [ 'name' ] . '.zip' ;
            $GLOBALS [ 'mi_remove_tables' ] = false ;
            $pm->performUninstall ( $load ) ;
            $pm->performInstall ( $_REQUEST [ 'install_file' ] ) ;
        }
        echo 'complete' ;
    
    }

    function action_ExportPackage ()
    {
        $mb = new ModuleBuilder ( ) ;
        $load = $_REQUEST [ 'name' ] ;
        $author = $_REQUEST [ 'author' ] ;
        $description = $_REQUEST [ 'description' ] ;
        $readme = $_REQUEST [ 'readme' ] ;
        if (! empty ( $load ))
        {
            $mb->getPackage ( $load ) ;
            $mb->packages [ $load ]->author = $author ;
            $mb->packages [ $load ]->description = $description ;
            $mb->packages [ $load ]->exportProject () ;
            $mb->packages [ $load ]->readme = $readme ;
        }
    }

    function action_DeletePackage ()
    {
        $mb = new ModuleBuilder ( ) ;
        $mb->getPackage ( $_REQUEST [ 'package' ] ) ;
        $mb->packages [ $_REQUEST [ 'package' ] ]->delete () ;
        $this->view = 'deletepackage' ;
    }

    function action_SaveModule ()
    {
        $mb = new ModuleBuilder ( ) ;
        $load = (! empty ( $_REQUEST [ 'original_name' ] )) ? $_REQUEST [ 'original_name' ] : $_REQUEST [ 'name' ] ;
        if (! empty ( $load ))
        {
            $mb->getPackage ( $_REQUEST [ 'package' ] ) ;
            $mb->packages [ $_REQUEST [ 'package' ] ]->getModule ( $load ) ;
            $module = & $mb->packages [ $_REQUEST [ 'package' ] ]->modules [ $load ] ;
            $module->populateFromPost () ;
            $mb->save () ;
            if (! empty ( $_REQUEST [ 'duplicate' ] ))
            {
                $module->copy ( $_REQUEST [ 'name' ] ) ;
            } else if (! empty ( $_REQUEST [ 'original_name' ] ) && $_REQUEST [ 'original_name' ] != $_REQUEST [ 'name' ])
            {
                if (! $module->rename ( $_REQUEST [ 'name' ] ))
                {
                    $module->name = $_REQUEST [ 'original_name' ] ;
                    $_REQUEST [ 'name' ] = $_REQUEST [ 'original_name' ] ;
                }
            }
            
            $_REQUEST [ 'view_package' ] = $_REQUEST [ 'package' ] ;
            $_REQUEST [ 'view_module' ] = $module->name ;
            $this->view = 'module' ;
        }
    }

    function action_DeleteModule ()
    {
        $mb = new ModuleBuilder ( ) ;
        $module = & $mb->getPackageModule ( $_REQUEST [ 'package' ], $_REQUEST [ 'view_module' ] ) ;
        $module->delete () ;
        $this->view = 'package' ;
    }

    function action_saveLabels ()
    {
        require_once 'modules/ModuleBuilder/parsers/parser.label.php' ;
        $parser = new ParserLabel ( $_REQUEST['view_module'] , isset ( $_REQUEST [ 'view_package' ] ) ? $_REQUEST [ 'view_package' ] : null ) ;
        $parser->handleSave ( $_REQUEST, $_REQUEST [ 'selected_lang' ] ) ;
        if (isset ( $_REQUEST [ 'view_package' ] )) //MODULE BUILDER
        {
            $this->view = 'modulelabels' ;
        } else //STUDIO
        {
            $this->view = isset ( $_REQUEST [ 'view' ] ) ? 'edit' : 'labels' ; // detect if we are being called by the LayoutEditor rather than the LabelEditor (set in view.layoutlabel.php)
        }
    }

    function action_SaveLabel ()
    {
        if (! empty ( $_REQUEST [ 'view_module' ] ))
        {
            $_REQUEST [ "label_" . $_REQUEST [ 'label' ] ] = $_REQUEST [ 'label_value' ] ;
            require_once 'modules/ModuleBuilder/parsers/parser.label.php' ;
            $parser = new ParserLabel ( $_REQUEST['view_module'] , isset ( $_REQUEST [ 'view_package' ] ) ? $_REQUEST [ 'view_package' ] : null ) ;
            $parser->handleSave ( $_REQUEST, $GLOBALS [ 'current_language' ] ) ;
        
        }
        $this->view = 'modulefields' ;
    }

    function action_ExportCustom ()
    {
        $modules = $_REQUEST [ 'modules' ] ;
        $name = $_REQUEST [ 'name' ] ;
        $author = $_REQUEST [ 'author' ] ;
        $description = $_REQUEST [ 'description' ] ;
        ob_clean () ;
        if (! empty ( $modules ) && ! empty ( $name ))
        {
            require_once ('modules/ModuleBuilder/MB/ModuleBuilder.php') ;
            $mb = new MBPackage ( $name ) ;
            $mb->author = $author ;
            $mb->description = $description ;
            $mb->exportCustom ( $modules, true, true ) ;
        }
    }

    function action_SaveField ()
    {
        require_once ('modules/DynamicFields/FieldCases.php') ;
        $field = get_widget ( $_REQUEST [ 'type' ] ) ;
        $_REQUEST [ 'name' ] = trim ( $_REQUEST [ 'name' ] ) ;
        $field->populateFromPost () ;
        
        if (!isset ( $_REQUEST [ 'view_package' ] ))
        {
            require_once ('modules/DynamicFields/DynamicField.php') ;
            if (! empty ( $_REQUEST [ 'view_module' ] ))
            {
                $module = $_REQUEST [ 'view_module' ] ;
                $df = new DynamicField ( $module ) ;
                $class_name = $GLOBALS [ 'beanList' ] [ $module ] ;
                require_once ($GLOBALS [ 'beanFiles' ] [ $class_name ]) ;
                $mod = new $class_name ( ) ;
                $df->setup ( $mod ) ;
                
                $field->save ( $df ) ;
                $this->action_SaveLabel () ;
                include_once ('modules/Administration/QuickRepairAndRebuild.php') ;
        		global $mod_strings;
                $mod_strings['LBL_ALL_MODULES'] = 'all_modules';
                $repair = new RepairAndClear();
        		$repair->show_output = false;
        		$repair->module_list = array($class_name);
        		$repair->clearVardefs();
        		//#28707 ,clear all the js files in cache
        		$repair->module_list = array();
        		$repair->clearJsFiles();
            }
        } else
        {
            $mb = new ModuleBuilder ( ) ;
            $module = & $mb->getPackageModule ( $_REQUEST [ 'view_package' ], $_REQUEST [ 'view_module' ] ) ;
            $field->save ( $module ) ;
            $module->mbvardefs->save () ;
            // get the module again to refresh the labels we might have saved with the $field->save (e.g., for address fields)
            $module = & $mb->getPackageModule ( $_REQUEST [ 'view_package' ], $_REQUEST [ 'view_module' ] ) ;            
            if (isset ( $_REQUEST [ 'label' ] ) && isset ( $_REQUEST [ 'label_value' ] ))
                $module->setLabel ( $GLOBALS [ 'current_language' ], $_REQUEST [ 'label' ], $_REQUEST [ 'label_value' ] ) ;
            $module->save();
        }
        $this->view = 'modulefields' ;
    }

    function action_RefreshField ()
    {
        require_once ('modules/DynamicFields/FieldCases.php') ;
        $field = get_widget ( $_POST [ 'type' ] ) ;
        $field->populateFromPost () ;
        $this->view = 'modulefield' ;
    }

    function action_SaveRelationship ()
    {
        if(!empty($GLOBALS['current_user']) && empty($GLOBALS['modListHeader']))
        {
            $GLOBALS['modListHeader'] = query_module_access_list($GLOBALS['current_user']);
        }

        if (empty($_REQUEST [ 'view_package' ]))
        {
            require_once 'modules/ModuleBuilder/parsers/relationships/DeployedRelationships.php' ;
            $relationships = new DeployedRelationships ( $_REQUEST [ 'view_module' ] ) ;
        } else
        {
            $mb = new ModuleBuilder ( ) ;
            $module = & $mb->getPackageModule ( $_REQUEST [ 'view_package' ], $_REQUEST [ 'view_module' ] ) ;
            require_once 'modules/ModuleBuilder/parsers/relationships/UndeployedRelationships.php' ;
            $relationships = new UndeployedRelationships ( $module->getModuleDir () ) ;
        }
        
        $relationships->addFromPost () ;
        $relationships->save () ;
        $GLOBALS['log']->debug("\n\nSTART BUILD");
        if (empty($_REQUEST [ 'view_package' ])) {
            $relationships->build () ;
            require_once('include/SugarObjects/LanguageManager.php');
            LanguageManager::clearLanguageCache($_REQUEST [ 'view_module' ]);
        }
        $GLOBALS['log']->debug("\n\nEND BUILD");
                
        $this->view = 'relationships' ;
    }

    function action_DeleteRelationship ()
    {
        if (isset ( $_REQUEST [ 'relationship_name' ] ))
        {
            if (empty($_REQUEST [ 'view_package' ] ))
            {
                require_once 'modules/ModuleBuilder/parsers/relationships/DeployedRelationships.php' ;
                $relationships = new DeployedRelationships ( $_REQUEST [ 'view_module' ] ) ;
            } else
            {
                $mb = new ModuleBuilder ( ) ;
                $module = & $mb->getPackageModule ( $_REQUEST [ 'view_package' ], $_REQUEST [ 'view_module' ] ) ;
                require_once 'modules/ModuleBuilder/parsers/relationships/UndeployedRelationships.php' ;
                $relationships = new UndeployedRelationships ( $module->getModuleDir () ) ;           
            }
            $relationships->delete ( $_REQUEST [ 'relationship_name' ] ) ;
            $relationships->save () ;
        }
        $this->view = 'relationships' ;
    }

    function action_SaveDropDown ()
    {
        require_once 'modules/ModuleBuilder/parsers/parser.dropdown.php' ;
        $parser = new ParserDropDown ( ) ;
        $parser->saveDropDown ( $_REQUEST ) ;
        $this->view = 'dropdowns' ;
    }

    function action_DeleteField ()
    {
        require_once ('modules/DynamicFields/FieldCases.php') ;
        $field = get_widget ( $_POST [ 'type' ] ) ;
        $field->name = $_REQUEST [ 'name' ] ;
        if (!isset ( $_REQUEST [ 'view_package' ] ))
        {
            if (! empty ( $_REQUEST [ 'name' ] ) && ! empty ( $_REQUEST [ 'view_module' ] ))
            {
                require_once ('modules/DynamicFields/DynamicField.php') ;
                $moduleName = $_REQUEST [ 'view_module' ] ;
                $class_name = $GLOBALS [ 'beanList' ] [ $moduleName ] ;
                require_once ($GLOBALS [ 'beanFiles' ] [ $class_name ]) ;
                $seed = new $class_name ( ) ;
                $df = new DynamicField ( $moduleName ) ;
                $df->setup ( $seed ) ;
                //Need to load the entire field_meta_data for some field types
                $field = $df->getFieldWidget($moduleName, $field->name);
                $field->delete ( $df ) ;
                require_once 'modules/ModuleBuilder/Module/StudioModule.php' ;
                $module = new StudioModule ( $moduleName ) ;
            }
        } 
        else
        {
            $mb = new ModuleBuilder ( ) ;
            $module = & $mb->getPackageModule ( $_REQUEST [ 'view_package' ], $_REQUEST [ 'view_module' ] ) ;
            $field->delete ( $module ) ;
            $mb->save () ;
        }
        $module->removeFieldFromLayouts( $field->name );
        $this->view = 'modulefields' ;
    }

    function action_CloneField ()
    {
        $this->view_object_map [ 'field_name' ] = $_REQUEST [ 'name' ] ;
        $this->view_object_map [ 'is_clone' ] = true ;
        $this->view = 'modulefield' ;
    }

    function action_SaveAssistantPref ()
    {
        global $current_user ;
        if (isset ( $_REQUEST [ 'pref_value' ] ))
        {
            if ($_REQUEST [ 'pref_value' ] == 'ignore')
            {
                $current_user->setPreference ( 'mb_assist', 'DISABLED', 0, 'Assistant' ) ;
            } else
            {
                $current_user->setPreference ( 'mb_assist', 'ENABLED', 0, 'Assistant' ) ;
            }
            $current_pref = $current_user->getPreference ( 'mb_assist', 'Assistant' ) ;
            echo "Assistant.processUserPref('$current_pref')" ;
            sugar_cleanup ( true ) ; //push preferences to DB.
        }
    }

    // Studio2 Actions
    

    function action_EditProperty ()
    {
        $this->view = 'property' ;
    }

    function action_saveProperty ()
    {
        require_once 'modules/ModuleBuilder/parsers/parser.label.php' ;
        $parser = new ParserLabel ( $_REQUEST['view_module'] , isset ( $_REQUEST [ 'view_package' ] ) ? $_REQUEST [ 'view_package' ] : null ) ;
        // if no language provided, then use the user's current language which is most likely what they intended
        $language = (isset($_REQUEST [ 'selected_lang' ])) ? $_REQUEST [ 'selected_lang' ] : $GLOBALS['current_language'] ;
        $parser->handleSave ( $_REQUEST, $language ) ;
        $json = getJSONobj();
        echo $json->encode(array("east" => array("action" => "deactivate")));
    }

    function action_editModule ()
    {
        $this->view = 'module' ;
    }

    function action_wizard ()
    {
        $this->view = 'wizard' ;
    }
    
    /* BEGIN - SECURITY GROUPS */ 
    function action_addLayout ()
    {
        $this->view = 'addlayout' ;
    }
    
	/**
	 * Layouts   --->    click for page with "Add Layout For...newline....SecurityGroup [Select ^]   Copy From [Default ^]
	 * 	Default
	 * 		Edit
	 * 		Detail
	 * 		List
	 * 		Quick Create
	 * 		Search
	 * 			Basic
	 * 			Advanced
	 * 	(these are all saved in custom/modules/.../metadata)
	 * 	Support
	 * 		Edit
	 * 		Detail
	 * 		List
	 * 		Quick Create
	 * 		Search
	 * 			Basic
	 * 			Advanced
	 * 	(save these in custom/modules/.../metadata/_GUID_/)	
	 */
    function action_saveGroupLayout ()
    {
    	$create_for = $_REQUEST['securitygroup']; //the guid of the security group
    	$copy_from = $_REQUEST['copy_layout'];
    	$module = $_REQUEST['view_module'];
    	
    	if(empty($create_for)) return; 
   
		require_once('include/dir_inc.php'); 	
        //$GLOBALS['log']->fatal("saving group layout for mod: $module for $create_for and from: $copy_from");
        //create destination dir
        $destDirectory = "custom/modules/".$module."/metadata/".$create_for."/";
        $GLOBALS['log']->debug("make dir: $destDirectory" );
        mkdir_recursive( $destDirectory ) ;
        
        //copy from source to new 
		$baseDirectory = "modules/{$module}/metadata/";
		if($copy_from != "Default") {
			$baseDirectory .= $copy_from."/";
		}
		$viewArray = array("detailView","editView","listView","quickCreate","search");
        foreach ( $viewArray as $view)
        {

			//$view = "detailView";
			$file = $baseDirectory . strtolower($view) . "defs.php";
			$destFile = $destDirectory. strtolower($view). "defs.php";
			$customFile = "custom/" . $file;
			$sourceFile = $file; //the default layout in the modules directory

			if(is_file($customFile)) {
				$sourceFile = $customFile; //use custom dir file if exists
			} else if (! is_file($sourceFile)) {
				// if we don't have ANY defined metadata then improvise as best we can
				if (strtolower($view) == 'quickcreate')
				{
					// special handling for quickcreates - base the quickcreate on the editview if no quickcreatedef exists
					$sourceFile = $baseDirectory."editviewdefs.php";
					if (is_file("custom/" . $sourceFile))
					{
						$sourceFile = "custom/" . $sourceFile;
					}
				}
				else //else no metadata for this module and view so ignore...
				{
					//continue;
					$sourceFile = "";
				}
			}
			if(!empty($sourceFile)) {
				$GLOBALS['log']->debug("Copying Layouts....\n	From: $sourceFile\n	To: $destFile");
				//copy_recursive( $sourceFile, $destFile );
				if(copy( $sourceFile, $destFile )) {
					$GLOBALS['log']->debug("Copy Successful");
				} else {
					$GLOBALS['log']->debug("Copy Failed");
				}
			}
		} //end for each view
		

        $this->view = 'addlayoutdone';	

    }    
    
    function action_removeGroupLayoutPrompt()
    {
		$this->view = 'removegrouplayoutprompt';	  
    } 
    
    function action_removeGroupLayoutConfirm()
    {
		//do the deal...make sure to clear the cache as well
		$securitygroup_id = $_REQUEST['grpLayout'];
		$view_module = $_REQUEST['view_module'];
		if(!isset($securitygroup_id) || !isset($view_module)) return;
		
		//delete custom/modules/MOD/grouplayout/***, cache/modules/MOD/grouplayout/***
		require_once('include/dir_inc.php');
		rmdir_recursive('custom/modules/'.$view_module .'/metadata/' . $securitygroup_id);
		
		$remove_dir = 'cache/modules/'.$view_module .'/' . $securitygroup_id;
		if(file_exists($remove_dir)) rmdir_recursive($remove_dir);

        $this->view = 'removelayoutdone';			
    }
    /* END - SECURITY GROUPS */ 

    /**
     * Receive a layout through $_REQUEST and save it out to the working files directory
     * Expects a series of $_REQUEST parameters all in the format $_REQUEST['slot-panel#-slot#-property']=value
     */
    
    function action_saveLayout ()
    {










            $parser = ParserFactory::getParser ( $_REQUEST [ 'view' ], $_REQUEST [ 'view_module' ], isset ( $_REQUEST [ 'view_package' ] ) ? $_REQUEST [ 'view_package' ] : null ) ;
            $this->view = 'layoutview' ;



        $parser->writeWorkingFile () ;
    }

    function action_saveAndPublishLayout ()
    {











            $parser = ParserFactory::getParser ( $_REQUEST [ 'view' ], $_REQUEST [ 'view_module' ], isset ( $_REQUEST [ 'view_package' ] ) ? $_REQUEST [ 'view_package' ] : null ) ;
            $this->view = 'layoutview' ;



        $parser->handleSave () ;
    }

    function action_manageBackups ()
    {
    
    }

    function action_listViewSave ()
    {









                     
        $this->view = 'listview' ;
        $packageName = (isset ( $_REQUEST [ 'view_package' ] ) && (strtolower($_REQUEST['view_package']) != 'studio')) ? $_REQUEST [ 'view_package' ] : null ;
//        $parserName = (! empty ( $_REQUEST [ 'subpanel' ] )) ? 'subpanel' : 'listview' ;
        $subpanelName = (! empty ( $_REQUEST [ 'subpanel' ] )) ? $_REQUEST [ 'subpanel' ] : null ;
        require_once 'modules/ModuleBuilder/parsers/ParserFactory.php' ;
        $parser = ParserFactory::getParser ( 'listview', $_REQUEST [ 'view_module' ], $packageName, $subpanelName ) ;          



        $parser->handleSave () ;
    }
    
    function action_dashletSave () {
        $this->view = 'dashlet' ;
        $packageName = (isset ( $_REQUEST [ 'view_package' ] ) && (strtolower($_REQUEST['view_package']) != 'studio')) ? $_REQUEST [ 'view_package' ] : null ;
        require_once 'modules/ModuleBuilder/parsers/ParserFactory.php' ;
        $parser = ParserFactory::getParser ( $_REQUEST [ 'view' ], $_REQUEST [ 'view_module' ], $packageName ) ;          
        $parser->handleSave () ;
    }

    function action_searchViewSave ()
    {
        $packageName = (isset ( $_REQUEST [ 'view_package' ] )) ? $_REQUEST [ 'view_package' ] : null ;
        require_once 'modules/ModuleBuilder/parsers/views/SearchViewMetaDataParser.php' ;
        $parser = new SearchViewMetaDataParser ( $_REQUEST [ 'view' ], $_REQUEST [ 'view_module' ], $packageName ) ;
        $parser->handleSave () ;
        $this->view = 'searchView' ;
    }

    function action_editLabels ()
    {
        if (isset ( $_REQUEST [ 'view_package' ] )) //MODULE BUILDER
        {
            $this->view = 'modulelabels';
        }else{ //STUDIO
            $this->view = 'labels';
        }
    }

    function action_get_app_list_string ()
    {
        require_once ('include/JSON.php') ;
        $json = new JSON ( ) ;
        if (isset ( $_REQUEST [ 'key' ] ) && ! empty ( $_REQUEST [ 'key' ] ))
        {
            $key = $_REQUEST [ 'key' ] ;
            $value = array ( ) ;
            if (! empty ( $GLOBALS [ 'app_list_strings' ] [ $key ] ))
            {
                $value = $GLOBALS [ 'app_list_strings' ] [ $key ] ;
            } else
            {
                $package_strings = array ( ) ;
                if (! empty ( $_REQUEST [ 'view_package' ] ) && $_REQUEST [ 'view_package' ] != 'studio' && ! empty ( $_REQUEST [ 'view_module' ] ))
                {
                    require_once ('modules/ModuleBuilder/MB/ModuleBuilder.php') ;
                    $mb = new ModuleBuilder ( ) ;
                    $module = & $mb->getPackageModule ( $_REQUEST [ 'view_package' ], $_REQUEST [ 'view_module' ] ) ;
                    $lang = $GLOBALS [ 'current_language' ] ;
                    $module->mblanguage->generateAppStrings ( false ) ;
                    $package_strings = $module->mblanguage->appListStrings [ $lang . '.lang.php' ] ;
                    if (isset ( $package_strings [ $key ] ) && is_array ( $package_strings [ $key ] ))
                    {
                        $value = $package_strings [ $key ] ;
                    }
                }
            }
            echo $json->encode ( $value ) ;
        }
    }

    function action_history ()
    {
        $this->view = 'history' ;
    }

















































}
?>
