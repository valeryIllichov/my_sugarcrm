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

require_once 'modules/ModuleBuilder/parsers/relationships/DeployedRelationships.php' ;

class StudioModule
{
    var $name ;
    var $subpanels = array ( ) ;
    private $views = array ( ) ;
    var $popups = array ( ) ;
    var $providedSubpanels = array ( ) ;
    var $search = array ( ) ;
    var $module ;
    var $seed ;
    var $fields ;





    function StudioModule ($module)
    {
        global $app_list_strings ;

        $this->viewSources = array ( 'editviewdefs.php' => array ( 'name' => translate ('LBL_EDITVIEW') , 'type' => 'editView' ) , 'detailviewdefs.php' => array ( 'name' => translate('LBL_DETAILVIEW') , 'type' => 'detailView' ) , 'listviewdefs.php' => array ( 'name' => translate('LBL_LISTVIEW') , 'type' => 'listView' ) ) ;
        $moduleNames = array_change_key_case ( $app_list_strings [ 'moduleList' ] ) ;
        $this->name = isset ( $moduleNames [ strtolower ( $module ) ] ) ? $moduleNames [ strtolower ( $module ) ] : strtolower ( $module ) ;
        $this->module = $module ;
        $this->class = $GLOBALS [ 'beanList' ] [ $this->module ] ;
        require_once $GLOBALS [ 'beanFiles' ] [ $this->class ] ;
        $this->seed = new $this->class ( ) ;
        $this->fields = $this->seed->field_defs ;

        $this->getViews () ;
        $this->getSubpanels () ;
        $this->getProvidedSubpanels () ;
        //$this->loadSubpanelRelationships () ;
        $this->getSearch () ;



    }

    /**
     * Gets the name of this module. Some modules have naming inconsistencies such as Bug Tracker and Bugs which causes warnings in Relationships
     * Added to resolve bug #20257
     */
    function getModuleName(){
    	$modules_with_odd_names = array('Bug Tracker'=>'Bugs');
    	foreach ($modules_with_odd_names as $odd_name=>$correct_name){
    		if ( $this->name == $odd_name )
    			return $correct_name;
    	}
    	return $this->name;
    }

    /**
     * Gets a list of views available
     * ListView/EditView/DetailView
     *
     */
    function getViews ()
    {
        foreach ( $this->viewSources as $file => $def )
        {
            if (file_exists ( "modules/{$this->module}/metadata/$file" ))
            {
                $this->views [ $file ] = $def ;
            }
        }

        // Now add in the QuickCreates - quickcreatedefs can be created by Studio from editviewdefs if they are absent, so just add them in regardless of whether the quickcreatedefs file exists
        $hideQuickCreateForModules = array ( 'kbdocuments' , 'projecttask' , 'campaigns' ) ;




        // Some modules should not have a QuickCreate form at all, so do not add them to the list
        if (! in_array ( strtolower ( $this->module ), $hideQuickCreateForModules ))
            $this->views [ 'quickcreatedefs.php' ] = array ( 'name' => translate('LBL_QUICKCREATE') , 'type' => 'quickCreate' ) ;

        return $this->views ;
    }

    function getSearch ()
    {
        if (file_exists ( "modules/{$this->module}/metadata/searchdefs.php" ))
        {
            $this->search [ translate('LBL_BASIC_SEARCH') ] = 'basic_search' ;
            $this->search [ translate('LBL_ADVANCED_SEARCH') ] = 'advanced_search' ;
        }
    }

    /*
     * Return an object containing all the relationships participated in by this module
     * @return AbstractRelationships Set of relationships
     */
    function getRelationships ()
    {
        return new DeployedRelationships ( $this->module ) ;
    }















    /**
     * Gets a list of subpanels used by the current module
     */
    function getSubpanels ()
    {
        if(!empty($GLOBALS['current_user']) && empty($GLOBALS['modListHeader']))
            $GLOBALS['modListHeader'] = query_module_access_list($GLOBALS['current_user']);

        $this->subpanels = array ( ) ; // initialize as some modules do not have subpanels
        require_once ('include/SubPanel/SubPanel.php') ;
        if (file_exists ( "modules/$this->module/metadata/subpaneldefs.php" ))
        {
            $GLOBALS [ 'log' ]->debug ( "StudioModule->getSubpanels(): getting subpanels for " . $this->module ) ;

            $layout_def = SubPanel::getModuleSubpanels ( $this->module ) ;

            foreach ( $layout_def as $sub => $label )
            {
                if ($sub == 'users')
                    continue ;
                $name = (! empty ( $label )) ? translate ( $label, $this->module ) : $sub ;
                $this->subpanels [ $sub ] = ucfirst ( $name ) ;
            }

        }

    }

    /**
     * gets a list of subpanels provided to other modules
     *
     *
     */
    function getProvidedSubpanels ()
    {
        $this->providedSubpanels = array () ;
        $subpanelDir = 'modules/' . $this->module . '/metadata/subpanels/' ;
        if (file_exists ( $subpanelDir ))
        {
            $f = dir ( $subpanelDir ) ;
            while ( $g = $f->read () )
            {
                // sanity check to confirm that this is a usable subpanel...
                require_once 'modules/ModuleBuilder/parsers/relationships/AbstractRelationships.php' ;
                if (substr ( $g, 0, 1 ) != '.' && AbstractRelationships::validSubpanel ( $subpanelDir . $g ))
                {
                    $subname = str_replace ( '.php', '', $g ) ;
                    $this->providedSubpanels [ $subname ] = $subname ;
                }
            }
        }

		return $this->providedSubpanels;
    }

    function getNodes ()
    {
        $nodes = array ( 'name' => $this->name , 'module' => $this->module , 'type' => 'StudioModule' , 'action' => 'module=ModuleBuilder&action=wizard&view_module=' . $this->module , 'children' => array ( array ( 'name' => 'labels' , 'label' => translate('LBL_LABELS') , 'action' => 'module=ModuleBuilder&action=editLabels&view_module=' . $this->module ) , array ( 'name' => 'fields' , 'label' => translate('LBL_FIELDS') , 'action' => "module=ModuleBuilder&action=modulefields&view_package=studio&view_module={$this->module}" ) , array ( 'name' => 'relatonships' , 'label' => translate('LBL_RELATIONSHIPS') , 'action' => "get_tpl=true&module=ModuleBuilder&action=relationships&view_module={$this->module}" ) ) ) ;
        $layouts = array ( ) ;
        foreach ( $this->views as $file => $def )
        {
            $file = str_replace ( $file, '.php', '' ) ;
            $layouts [] = array ( 'name' => $def [ 'name' ] , 'action' => "module=ModuleBuilder&action=editLayout&view=".ucfirst ( $def [ 'type' ] )."&view_module={$this->module}" ) ;
        }

        $search = array ( ) ;
        foreach ( $this->search as $searchName => $searchType )
        {
            $search [] = array ( 'name' => $searchName , 'action' => "module=ModuleBuilder&action=editLayout&view={$searchType}&view_module={$this->module}" ) ;
        }
        if (! empty ( $search ))
        {
            $layouts [] = array ( 'name' => translate('LBL_SEARCH') , 'type' => 'Folder' , 'children' => $search , 'action' => 'module=ModuleBuilder&action=wizard&search=1&view_module=' . $this->module ) ;
        }

        if (! empty ( $layouts ))
        {
            //$nodes [ 'children' ] [] = array ( 'name' => translate('LBL_LAYOUTS') , 'type' => 'Folder' , 'children' => $layouts , 'action' => 'module=ModuleBuilder&action=wizard&layouts=1&view_module=' . $this->module ) ;

			/* BEGIN - SECURITY GROUPS */ 
            
			$defaultLayout [] = array ( 'name' => translate('LBL_DEFAULT') , 'type' => 'Folder' , 'children' => $layouts , 'action' => 'module=ModuleBuilder&action=wizard&layouts=1&view_module=' . $this->module ) ;
			$layout_nodes = array();
			$layout_nodes = $defaultLayout;
    		//get security group nodes...in metadata directory in a folder named with the guid of the security group
        	$custDirectory = "custom/modules/".$this->module."/metadata/";
    		if(is_dir($custDirectory)) {		
    			require_once('modules/SecurityGroups/SecurityGroup.php');
				$groupFocus = new SecurityGroup();
				$d = dir($custDirectory);
				while( $f = $d->read() ) {
					if( $f == "." || $f == ".." ){
						continue;
					}

					if( is_dir( "$custDirectory/$f" ) ) {
						//see if $f is a guid for a security group
						$groupFocus = new SecurityGroup();
						$groupFocus->retrieve($f);
						if(!empty($groupFocus->id) && isset($groupFocus->id)) {
							//found existing group layout...create node
							$GLOBALS['log']->debug("Found Security Group Layout for: ".$groupFocus->name);
							
							$group_layouts = array ( ) ;
							//remove group layout option
							$group_layouts[] = array('name'=> translate('LBL_REMOVE_LAYOUT'), 'action' => "module=ModuleBuilder&action=removeGroupLayoutPrompt&view_module={$this->module}". "&grpLayout=".$groupFocus->id);
							foreach ( $this->views as $file => $def )
							{
								$file = str_replace ( $file, '.php', '' ) ;
								$group_layouts [] = array ( 'name' => $def [ 'name' ] , 'action' => "module=ModuleBuilder&action=editLayout&view=".ucfirst ( $def [ 'type' ] )."&view_module={$this->module}" . "&grpLayout=".$groupFocus->id ) ;
							}

							$search = array ( ) ;
							foreach ( $this->search as $searchName => $searchType )
							{
								$search [] = array ( 'name' => $searchName , 'action' => "module=ModuleBuilder&action=editLayout&view={$searchType}&view_module={$this->module}" . "&grpLayout=".$groupFocus->id ) ;
							}
							if (! empty ( $search ))
							{
								$group_layouts [] = array ( 'name' => translate('LBL_SEARCH') , 'type' => 'Folder' , 'children' => $search , 'action' => 'module=ModuleBuilder&action=wizard&search=1&view_module=' . $this->module . "&grpLayout=".$groupFocus->id ) ;
							}
							$layout_nodes[] = array ( 'name' => translate($groupFocus->name) , 'type' => 'Folder' , 'children' => $group_layouts , 'action' => 'module=ModuleBuilder&action=wizard&layouts=1&view_module=' . $this->module . "&grpLayout=".$groupFocus->id ) ;
						}
					}


				} //end while files/dirs
    		} //end if directory exists

    		$nodes [ 'children' ] [] = array ( 'name' => translate('LBL_LAYOUTS') , 'type' => 'Folder' , 'children' => $layout_nodes , 'action' => 'module=ModuleBuilder&action=addlayout&layouts=1&view_module=' . $this->module ) ;
        
			/* END - SECURITY GROUPS */ 
        }

        $subpanels = array ( ) ;
        foreach ( $this->subpanels as $sub => $subname )
        {
            $subpanels [] = array ( 'label' => $subname , 'name' => $sub , 'action' => 'module=ModuleBuilder&action=editLayout&view=ListView&view_module=' . $this->module . '&subpanel=' . $sub . '&subpanelLabel=' . $subname ) ;
        }
        if (! empty ( $subpanels ))
        {
            $nodes [ 'children' ] [] = array ( 'name' => translate('LBL_SUBPANELS') , 'type' => 'Folder' , 'children' => $subpanels , 'action' => 'module=ModuleBuilder&action=wizard&subpanels=1&view_module=' . $this->module ) ;
        }















        return $nodes ;
    }

    function removeFieldFromLayouts ( $fieldName )
    {
    	require_once("modules/ModuleBuilder/parsers/ParserFactory.php");
    	$GLOBALS [ 'log' ]->info ( get_class ( $this ) . "->removeFieldFromLayouts($fieldName)" ) ;
        $sources = $this->views ;
        foreach ( $this->search as $key => $value )
            $sources [] = array ( 'type' => $value ) ;
        $GLOBALS [ 'log' ]->debug ( print_r( $sources,true) ) ;
        foreach ( $sources as $name => $defs )
        {
            $parser = ParserFactory::getParser( $defs [ 'type' ] , $this->module ) ;
            if ($parser->removeField ( $fieldName ) )
                $parser->handleSave(false) ; // don't populate from $_REQUEST, just save as is...
        }
    }

}
?>
