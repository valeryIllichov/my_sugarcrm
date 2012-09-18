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
require_once ('modules/ModuleBuilder/MB/AjaxCompose.php') ;
require_once ('modules/ModuleBuilder/Module/StudioModule.php') ;



require_once ('include/MVC/View/SugarView.php') ;

class ModuleBuilderViewWizard extends SugarView
{
	var $actions = '' ;

	function ModuleBuilderViewWizard ()
	{

		$this->editModule = (! empty ( $_REQUEST [ 'view_module' ] )) ? $_REQUEST [ 'view_module' ] : null ;
		$this->layouts = (! empty ( $_REQUEST [ 'layouts' ] )) ? $_REQUEST [ 'layouts' ] : null ;
		$this->subpanels = (! empty ( $_REQUEST [ 'subpanels' ] )) ? $_REQUEST [ 'subpanels' ] : null ;
		$this->search = (! empty ( $_REQUEST [ 'search' ] )) ? $_REQUEST [ 'search' ] : null ;



		$this->dashlet = (!empty ($_REQUEST[ 'dashlet'])) ? $_REQUEST['dashlet'] : null;
		$this->assistant = array ( 'group' => 'main' , 'key' => 'studioWelcome' ) ;
		$this->buttons = array(); // initialize so that modules without subpanels for example don't result in this being unset and causing problems in the smarty->assign
	}

	function display ()
	{
		$this->ajax = new AjaxCompose ( ) ;
		$smarty = new Sugar_Smarty ( ) ;

		if (isset ( $_REQUEST [ 'MB' ] ))
		{
			$this->processMB ( $this->ajax ) ;
		} else
		{








				$this->processStudio ( $this->ajax ) ;




		}

		global $image_path ;
		$smarty->assign ( 'buttons', $this->buttons ) ;
		$smarty->assign ( 'image_path', $image_path ) ;
		$smarty->assign ( "title", $this->title ) ;
		$smarty->assign ( "question", $this->question ) ;
		$smarty->assign ( "defaultHelp", $this->help ) ;
		$smarty->assign ( "actions", $this->actions ) ;

		$this->ajax->addSection ( 'center', $this->title, $smarty->fetch ( 'modules/ModuleBuilder/tpls/wizard.tpl' ) ) ;
		echo $this->ajax->getJavascript () ;
	}

	function processStudio ( $ajax )
	{
		//      $GLOBALS['log']->debug("in processStudio");


		global $mod_strings ;
		if (isset ( $this->editModule ))
		{
			global $app_list_strings ;
			$moduleNames = array_change_key_case ( $app_list_strings [ 'moduleList' ] ) ;
			$this->translatedEditModule = $moduleNames [ strtolower ( $this->editModule ) ] ;
		}

		$this->ajax->addCrumb ( $mod_strings [ 'LBL_STUDIO' ], 'ModuleBuilder.main("studio")' ) ;
		if (! isset ( $this->editModule ))
		{
			//Studio Select Module Page
			$this->generateStudioModuleButtons () ;
			$this->question = $mod_strings [ 'LBL_QUESTION_EDIT' ] ;
			$this->title = $mod_strings [ 'LBL_STUDIO' ] ;
			global $current_user;
			if (is_admin($current_user))
				$this->actions = "<input class=\"button\" type=\"button\" id=\"exportBtn\" name=\"exportBtn\" onclick=\"ModuleBuilder.getContent('module=ModuleBuilder&action=exportcustomizations');\" value=\"" . $mod_strings [ 'LBL_BTN_EXPORT' ] . '">' ;

			$this->help = 'studioHelp' ;
		} else if (isset ( $this->layouts ))
		{
			//Studio Select Layout page
			$this->generateViewButtons () ;
			$this->title = "{$this->translatedEditModule} {$mod_strings['LBL_LAYOUTS']}" ;
			$this->question = $mod_strings [ 'LBL_QUESTION_LAYOUT' ] ;
			$this->help = 'layoutsHelp' ;
			$this->ajax->addCrumb ( $this->translatedEditModule, 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view_module=' . $this->editModule . '")' ) ;
			/* BEGIN - SECURITY GROUPS */ 
			$groupLayout = $_REQUEST['grpLayout'];
			$groupName = "Default";
			if(!isset($groupLayout) || empty($groupLayout)) {
				$groupLayout = "";
			} else {
				//Get group name for display
				require_once('modules/SecurityGroups/SecurityGroup.php');
				$groupFocus = new SecurityGroup();
				$groupFocus->retrieve($groupLayout);
				$groupName = $groupFocus->name;
				$this->ajax->addCrumb($groupName,"");
			}
			/* END - SECURITY GROUPS */
			$this->ajax->addCrumb ( $mod_strings [ 'LBL_LAYOUTS' ], 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&layouts=1&view_module=' . $this->editModule . '")' ) ;
			$this->assistant [ 'key' ] = 'viewlayouts' ;
			$this->assistant [ 'group' ] = 'module' ;
		} else if (isset ( $this->subpanels ))
		{
			//Studio Select Subpanel page.
			$this->generateSubpanelButtons () ;
			$this->title = $this->translatedEditModule . " " . $mod_strings [ 'LBL_SUBPANELS' ] ;
			$this->question = $mod_strings [ 'LBL_QUESTION_SUBPANEL' ] ;
			$this->ajax->addCrumb ( $this->translatedEditModule, 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view_module=' . $this->editModule . '")' ) ;
			$this->ajax->addCrumb ( $mod_strings [ 'LBL_SUBPANELS' ], 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&subpanels=1&view_module=' . $this->editModule . '")' ) ;
			$this->help = 'subpanelHelp' ;
		} else if (isset ( $this->search ))
		{
			//Studio Select Search Layout page.
			$this->generateSearchButtons () ;
			$this->title = $this->translatedEditModule . " " . $mod_strings['LBL_SEARCH'];
			$this->question = $mod_strings [ 'LBL_QUESTION_SEARCH' ] ;
			$this->ajax->addCrumb ( $this->translatedEditModule, 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view_module=' . $this->editModule . '")' ) ;
			/* BEGIN - SECURITY GROUPS */ 
			$groupLayout = $_REQUEST['grpLayout'];
			$groupName = "Default";
			if(!isset($groupLayout) || empty($groupLayout)) {
				$groupLayout = "";
			} else {
				//Get group name for display
				require_once('modules/SecurityGroups/SecurityGroup.php');
				$groupFocus = new SecurityGroup();
				$groupFocus->retrieve($groupLayout);
				$groupName = $groupFocus->name;
				$this->ajax->addCrumb($groupName,"");
			}
			/* END - SECURITY GROUPS */ 
			$this->ajax->addCrumb ( $mod_strings [ 'LBL_LAYOUTS' ], 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&layouts=1&view_module=' . $this->editModule . '")' ) ;
			$this->ajax->addCrumb ( $mod_strings [ 'LBL_SEARCH' ], 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&search=1&view_module=' . $this->editModule . '")' ) ;
			$this->help = 'searchHelp' ;
		} else












		{
			//Studio Edit Module Page
			$module = new StudioModule ( $this->editModule ) ;
			$this->generateModuleWizardButtons () ;
			$this->question = $mod_strings [ 'LBL_QUESTION_MODULE' ] ;
			$this->title = $mod_strings [ 'LBL_EDIT' ] . " " . $this->translatedEditModule ;
			$this->help = 'moduleHelp' ;
			$this->ajax->addCrumb ( $this->translatedEditModule, 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view_module=' . $this->editModule . '")' ) ;
		}
	}








































	function processMB ( $ajax )
	{
		global $mod_strings ;
		$this->editModule = $_REQUEST [ 'view_module' ] ;
		$ajax->addCrumb ( translate ( 'LBL_MODULEBUILDER', 'ModuleBuilder' ), 'ModuleBuilder.main("mb")' ) ;
		if (! isset ( $_REQUEST [ 'view_package' ] ))
		{
			sugar_die ( "no ModuleBuilder package set" ) ;
		}
		$this->package = $_REQUEST [ 'view_package' ] ;

		$ajax->addCrumb ( $this->package, 'ModuleBuilder.getContent("module=ModuleBuilder&action=package&view_package=' . $this->package . '")' ) ;
		$ajax->addCrumb ( $this->editModule, 'ModuleBuilder.getContent("module=ModuleBuilder&action=module&view_module=' . $this->editModule . '&view_package=' . $this->package . '")' ) ;

		//ModuleBuilder Select Subpanel
		if (isset ( $_REQUEST [ 'subpanel' ] ))
		{
			$ajax->addCrumb ( $this->editModule, 'ModuleBuilder.getContent("module=ModuleBuilder&action=module&view_module=' . $this->editModule . '&view_package=' . $this->package . '")' ) ;
			$ajax->addCrumb ( $mod_strings [ 'LBL_SUBPANELS' ], 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&MB=1&subpanels=1&view_module=' . $this->editModule . '&view_package=' . $this->package . '")' ) ;
		} else if (isset ( $this->search ))
		{
			//MB Select Search Layout page.
			$this->generateMBSearchButtons () ;
			$this->title = $this->editModule . " " . $mod_strings [ 'LBL_SEARCH' ] ;
			$this->question = $mod_strings [ 'LBL_QUESTION_SEARCH' ] ;
			$ajax->addCrumb ( $mod_strings [ 'LBL_LAYOUTS' ], 'ModuleBuilder.getContent("module=ModuleBuilder&MB=true&action=wizard&view_module=' . $this->editModule . '&view_package=' . $this->package . '")' ) ;
			$ajax->addCrumb ( $mod_strings [ 'LBL_SEARCH_FORMS' ], 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&MB=1&search=1&view_module=' . $this->editModule . '&view_package=' . $this->package . '")' ) ;
			$this->help = "searchHelp" ;
		}
		else if (isset( $this->dashlet )){
			$this->generateMBDashletButtons ();
			$this->title = $this->editModule ." " .$mod_strings['LBL_DASHLET'];
			$this->question = $mod_strings [ 'LBL_QUESTION_DASHLET' ] ;
			//$this->ajax->addCrumb ( $mod_strings['LBL_QUESTION_DASHLET'], 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&view_module=' . $this->editModule . '")' ) ;
			$this->ajax->addCrumb ( $mod_strings [ 'LBL_LAYOUTS' ], 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&layouts=1&MB=1&view_package='.$this->package.'&view_module=' . $this->editModule . '")' ) ;
			$this->ajax->addCrumb ( $mod_strings [ 'LBL_DASHLET' ], 'ModuleBuilder.getContent("module=ModuleBuilder&action=wizard&dashlet=1&MB=1&view_package='.$this->package.'&view_module=' . $this->editModule . '")' ) ;
			$this->help = 'dashletHelp' ;
		} else
		{
			$ajax->addCrumb ( $mod_strings [ 'LBL_LAYOUTS' ], 'ModuleBuilder.getContent("module=ModuleBuilder&MB=true&action=wizard&view_module=' . $this->editModule . '&view_package=' . $this->package . '")' ) ;
			$this->generateMBViewButtons () ;
			$this->title = $this->editModule . " " . $mod_strings [ 'LBL_LAYOUTS' ] ;
			$this->question = $mod_strings [ 'LBL_QUESTION_LAYOUT' ] ;
			$this->help = "layoutsHelp" ;
		}
	}

	function generateStudioModuleButtons ()
	{
		require_once ('modules/ModuleBuilder/Module/StudioBrowser.php') ;
		$sb = new StudioBrowser ( ) ;
		$sb->loadModules () ;
		$nodes = $sb->getNodes () ;
		$this->buttons = array ( ) ;
		//$GLOBALS['log']->debug(print_r($nodes,true));
		foreach ( $nodes as $module )
		{
			$this->buttons [ $module [ 'name' ] ] = array ( 'action' => "ModuleBuilder.getContent('" . $module [ 'action' ] . "')" , 'imageTitle' => ucfirst ( $module [ 'module' ] ) , 'size' => '48' ) ;
		}
	}

	function generateModuleWizardButtons ()
	{

		$this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_LABELS' ] ] = array ( 'action' => "ModuleBuilder.getContent('module=ModuleBuilder&action=editLabels&view_module=" . $this->editModule . "')" , 'imageTitle' => 'Labels' , 'size' => '48' , 'help' => 'labelsBtn' ) ;
		$this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_FIELDS' ] ] = array ( 'action' => "ModuleBuilder.getContent('module=ModuleBuilder&action=modulefields&view_package=studio&view_module=" . $this->editModule . "')" , 'imageTitle' => 'Fields' , 'size' => '48' , 'help' => 'fieldsBtn' ) ;
		$this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_LAYOUTS' ] ] = array ( 'action' => "ModuleBuilder.getContent('module=ModuleBuilder&action=wizard&layouts=1&view_module=" . $this->editModule . "')" , 'imageTitle' => 'Layouts' , 'size' => '48' , 'help' => 'layoutsBtn' ) ;
        $this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_RELATIONSHIPS' ] ] = array ( 'action' => "ModuleBuilder.getContent('module=ModuleBuilder&action=relationships&view_module=" . $this->editModule . "')" , 'imageTitle' => 'Relationships' , 'size' => '48' , 'help' => 'relationshipsBtn' ) ;
		$module = new StudioModule ( $this->editModule ) ;
		if (count( $module->subpanels ) > 0)
		{
			$this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_SUBPANELS' ] ] = array ( 'action' => "ModuleBuilder.getContent('module=ModuleBuilder&action=wizard&subpanels=1&view_module=" . $this->editModule . "')" , 'imageTitle' => 'Subpanels' , 'size' => '48' , 'help' => 'subpanelBtn' ) ;
		}








	}

	function generateViewButtons ()
	{
		$module = new StudioModule ( $this->editModule ) ;
		
		foreach ( $module->getViews() as $file => $view )
		{
			$viewType = ($view [ 'type' ] == 'list') ? "ListView" : ucfirst ( $view [ 'type' ] ) ;
			// SECURITY GROUPS : (isset($_REQUEST['grpLayout'])?"&grpLayout=".$_REQUEST['grpLayout']:"") 
			$this->buttons [ $view [ 'name' ] ] = array ( 'action' => "ModuleBuilder.getContent('module=ModuleBuilder&action=editLayout&view_module={$this->editModule}&view=$viewType".(isset($_REQUEST['grpLayout'])?"&grpLayout=".$_REQUEST['grpLayout']:"")."')" , 'imageTitle' => $viewType , 'help' => "viewBtn{$viewType}" , 'size' => '48' ) ;
			//$this->buttons [ $view [ 'name' ] ] = array ( 'action' => "ModuleBuilder.getContent('module=ModuleBuilder&action=editLayout&view_module={$this->editModule}&view=$viewType')" , 'imageTitle' => $viewType , 'help' => "viewBtn{$viewType}" , 'size' => '48' ) ;
		}
		if (isset ( $module->search ))
		{
			// SECURITY GROUPS : (isset($_REQUEST['grpLayout'])?"&grpLayout=".$_REQUEST['grpLayout']:"") 
			$this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_SEARCH' ] ] = array ( 'action' => "ModuleBuilder.getContent('module=ModuleBuilder&action=wizard&search=1&view_module={$this->editModule}".(isset($_REQUEST['grpLayout'])?"&grpLayout=".$_REQUEST['grpLayout']:"") ."')" , 'imageTitle' => 'SearchForm' , 'help' => "searchBtn" , 'size' => '48' ) ;
			//$this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_SEARCH' ] ] = array ( 'action' => "ModuleBuilder.getContent('module=ModuleBuilder&action=wizard&search=1&view_module={$this->editModule}')" , 'imageTitle' => 'SearchForm' , 'help' => "searchBtn" , 'size' => '48' ) ;
		}
	}
























































	function generateMBViewButtons ()
	{
		$this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_EDITVIEW' ] ] = array ( 'action' => "ModuleBuilder.getContent('module=ModuleBuilder&MB=true&action=editLayout&view=editView&view_module={$this->editModule}&view_package={$this->package}')" , 'imageTitle' => 'EditView', 'help'=>'viewBtnEditView' ) ;
		$this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_DETAILVIEW' ] ] = array ( 'action' => "ModuleBuilder.getContent('module=ModuleBuilder&MB=true&action=editLayout&view=detailView&view_module={$this->editModule}&view_package={$this->package}')" , 'imageTitle' => 'DetailView', 'help'=>'viewBtnListView'  ) ;
		$this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_LISTVIEW' ] ] = array ( 'action' => "ModuleBuilder.getContent('module=ModuleBuilder&MB=true&action=editLayout&view=ListView&view_module={$this->editModule}&view_package={$this->package}')" , 'imageTitle' => 'ListView', 'help'=>'viewBtnListView' ) ;
		$this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_SEARCH' ] ] = array ( 'action' => "ModuleBuilder.getContent('module=ModuleBuilder&MB=true&action=wizard&search=1&view_module={$this->editModule}&view_package={$this->package}')" , 'imageTitle' => 'SearchForm' , 'help'=> 'searchBtn' ) ;
		$this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_DASHLET' ] ] = array ( 'action' => "ModuleBuilder.getContent('module=ModuleBuilder&MB=true&action=wizard&dashlet=1&view_module={$this->editModule}&view_package={$this->package}')" , 'imageTitle' => 'Dashlet', 'help'=>'viewBtnDashlet' ) ;

	}

	function generateSubpanelButtons ()
	{
		$this->module = new StudioModule ( $this->editModule ) ;
		foreach ( $this->module->subpanels as $panel => $pname )
		{
			$this->buttons [ $pname ] = array ( 'action' => "ModuleBuilder.getContent('module=ModuleBuilder&action=editLayout&view_module={$this->editModule}&view=listView&subpanel={$panel}&subpanelLabel={$pname}')" , 'imageTitle' => $pname , 'imageName' => $pname, 'altImageName' => 'Subpanels', 'size' => '48' ) ;

		}
	}
	function generateSearchButtons ()
	{
		$this->module = new StudioModule ( $this->editModule ) ;
		foreach ( $this->module->search as $title => $searchLayout )
		{
			if($searchLayout == 'basic_search'){
				$name = 'BasicSearch';
			}elseif($searchLayout == 'advanced_search'){
				$name = 'AdvancedSearch';
			}else{
				$name = str_replace ( ' ', '', $title ) ;
			}
			// SECURITY GROUPS : (isset($_REQUEST['grpLayout'])?"&grpLayout=".$_REQUEST['grpLayout']:"") 
			$this->buttons [ $title ] = array ( 'action' => "ModuleBuilder.getContent('module=ModuleBuilder&action=editLayout&view_module={$this->editModule}&view={$searchLayout}".(isset($_REQUEST['grpLayout'])?"&grpLayout=".$_REQUEST['grpLayout']:"") ."')" , 'imageTitle' => $title , 'imageName' => $name , 'help' => "{$name}Btn" , 'size' => '48' ) ;
			//$this->buttons [ $title ] = array ( 'action' => "ModuleBuilder.getContent('module=ModuleBuilder&action=editLayout&view_module={$this->editModule}&view={$searchLayout}')" , 'imageTitle' => $title , 'imageName' => $name , 'help' => "{$name}Btn" , 'size' => '48' ) ;

		}
	}
	function generateMBDashletButtons() {
		$this->buttons [ $GLOBALS ['mod_strings' ]['LBL_DASHLETLISTVIEW']] = array('action'=> "ModuleBuilder.getContent('module=ModuleBuilder&MB=true&action=editLayout&view=dashlet&view_module={$this->editModule}&view_package={$this->package}')", 'imageTitle'=> $GLOBALS ['mod_strings']['LBL_DASHLETLISTVIEW'], 'imageName'=>'ListView', 'help'=>'DashletListViewBtn');
		$this->buttons [ $GLOBALS ['mod_strings' ]['LBL_DASHLETSEARCHVIEW']] = array('action'=> "ModuleBuilder.getContent('module=ModuleBuilder&MB=true&action=editLayout&view=dashletsearch&view_module={$this->editModule}&view_package={$this->package}')", 'imageTitle'=> $GLOBALS ['mod_strings']['LBL_DASHLETSEARCHVIEW'], 'imageName'=>'BasicSearch','help'=> 'DashletSearchViewBtn');
	}
	function generateMBSearchButtons ()
	{
		$this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_BASIC' ] ] = array ( 'action' => "ModuleBuilder.getContent('module=ModuleBuilder&MB=true&action=editLayout&view_module={$this->editModule}&view_package={$this->package}&view=SearchView&searchlayout=basic_search')" , 'imageTitle' => $GLOBALS [ 'mod_strings' ] [ 'LBL_BASIC_SEARCH' ] , 'imageName' => 'BasicSearch','help' => "BasicSearchBtn" ) ;
		$this->buttons [ $GLOBALS [ 'mod_strings' ] [ 'LBL_ADVANCED' ] ] = array ( 'action' => "ModuleBuilder.getContent('module=ModuleBuilder&MB=true&action=editLayout&view_module={$this->editModule}&view_package={$this->package}&view=SearchView&searchlayout=advanced_search')" , 'imageTitle' => $GLOBALS [ 'mod_strings' ] [ 'LBL_ADVANCED_SEARCH' ] , 'imageName' => 'AdvancedSearch','help' => "AdvancedSearchBtn" ) ;

	}
}
?>
