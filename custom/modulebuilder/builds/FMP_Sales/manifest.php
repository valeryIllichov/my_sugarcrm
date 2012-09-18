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

	$manifest = array (
		 'acceptable_sugar_versions' => 
		  array (
	     	
		  ),
		  'acceptable_sugar_flavors' =>
		  array(
		  	'CE', 'PRO','ENT'
		  ),
		  'readme'=>'',
		  'key'=>'DSls',
		  'author' => 'FMP',
		  'description' => '',
		  'icon' => '',
		  'is_uninstallable' => true,
		  'name' => 'FMP_Sales',
		  'published_date' => '2010-10-14 21:53:35',
		  'type' => 'module',
		  'version' => '1287093215',
		  'remove_tables' => 'prompt',
		  );
$installdefs = array (
  'id' => 'FMP_Sales',
  'beans' => 
  array (
    0 => 
    array (
      'module' => 'DSls_MgrGroups',
      'class' => 'DSls_MgrGroups',
      'path' => 'modules/DSls_MgrGroups/DSls_MgrGroups.php',
      'tab' => false,
    ),
    1 => 
    array (
      'module' => 'DSls_Slsm',
      'class' => 'DSls_Slsm',
      'path' => 'modules/DSls_Slsm/DSls_Slsm.php',
      'tab' => false,
    ),
    2 => 
    array (
      'module' => 'DSls_Calendar',
      'class' => 'DSls_Calendar',
      'path' => 'modules/DSls_Calendar/DSls_Calendar.php',
      'tab' => false,
    ),
    3 => 
    array (
      'module' => 'DSls_CustBudget',
      'class' => 'DSls_CustBudget',
      'path' => 'modules/DSls_CustBudget/DSls_CustBudget.php',
      'tab' => false,
    ),
    4 => 
    array (
      'module' => 'DSls_DailySales',
      'class' => 'DSls_DailySales',
      'path' => 'modules/DSls_DailySales/DSls_DailySales.php',
      'tab' => false,
    ),
    5 => 
    array (
      'module' => 'DSls_Journal',
      'class' => 'DSls_Journal',
      'path' => 'modules/DSls_Journal/DSls_Journal.php',
      'tab' => false,
    ),
    6 => 
    array (
      'module' => 'DSls_Regions',
      'class' => 'DSls_Regions',
      'path' => 'modules/DSls_Regions/DSls_Regions.php',
      'tab' => false,
    ),
    7 => 
    array (
      'module' => 'DSls_Locations',
      'class' => 'DSls_Locations',
      'path' => 'modules/DSls_Locations/DSls_Locations.php',
      'tab' => false,
    ),
    8 => 
    array (
      'module' => 'DSls_pline',
      'class' => 'DSls_pline',
      'path' => 'modules/DSls_pline/DSls_pline.php',
      'tab' => false,
    ),
    9 => 
    array (
      'module' => 'DSls_cat',
      'class' => 'DSls_cat',
      'path' => 'modules/DSls_cat/DSls_cat.php',
      'tab' => false,
    ),
    10 => 
    array (
      'module' => 'DSls_pricecode',
      'class' => 'DSls_pricecode',
      'path' => 'modules/DSls_pricecode/DSls_pricecode.php',
      'tab' => false,
    ),
    11 => 
    array (
      'module' => 'DSls_cat_simple',
      'class' => 'DSls_cat_simple',
      'path' => 'modules/DSls_cat_simple/DSls_cat_simple.php',
      'tab' => false,
    ),
    12 => 
    array (
      'module' => 'DSls_pline_simple',
      'class' => 'DSls_pline_simple',
      'path' => 'modules/DSls_pline_simple/DSls_pline_simple.php',
      'tab' => false,
    ),
  ),
  'layoutdefs' => 
  array (
  ),
  'relationships' => 
  array (
  ),
  'image_dir' => '<basepath>/icons',
  'copy' => 
  array (
    0 => 
    array (
      'from' => '<basepath>/SugarModules/modules/DSls_MgrGroups',
      'to' => 'modules/DSls_MgrGroups',
    ),
    1 => 
    array (
      'from' => '<basepath>/SugarModules/modules/DSls_Slsm',
      'to' => 'modules/DSls_Slsm',
    ),
    2 => 
    array (
      'from' => '<basepath>/SugarModules/modules/DSls_Calendar',
      'to' => 'modules/DSls_Calendar',
    ),
    3 => 
    array (
      'from' => '<basepath>/SugarModules/modules/DSls_CustBudget',
      'to' => 'modules/DSls_CustBudget',
    ),
    4 => 
    array (
      'from' => '<basepath>/SugarModules/modules/DSls_DailySales',
      'to' => 'modules/DSls_DailySales',
    ),
    5 => 
    array (
      'from' => '<basepath>/SugarModules/modules/DSls_Journal',
      'to' => 'modules/DSls_Journal',
    ),
    6 => 
    array (
      'from' => '<basepath>/SugarModules/modules/DSls_Regions',
      'to' => 'modules/DSls_Regions',
    ),
    7 => 
    array (
      'from' => '<basepath>/SugarModules/modules/DSls_Locations',
      'to' => 'modules/DSls_Locations',
    ),
    8 => 
    array (
      'from' => '<basepath>/SugarModules/modules/DSls_pline',
      'to' => 'modules/DSls_pline',
    ),
    9 => 
    array (
      'from' => '<basepath>/SugarModules/modules/DSls_cat',
      'to' => 'modules/DSls_cat',
    ),
    10 => 
    array (
      'from' => '<basepath>/SugarModules/modules/DSls_pricecode',
      'to' => 'modules/DSls_pricecode',
    ),
    11 => 
    array (
      'from' => '<basepath>/SugarModules/modules/DSls_cat_simple',
      'to' => 'modules/DSls_cat_simple',
    ),
    12 => 
    array (
      'from' => '<basepath>/SugarModules/modules/DSls_pline_simple',
      'to' => 'modules/DSls_pline_simple',
    ),
  ),
  'language' => 
  array (
    0 => 
    array (
      'from' => '<basepath>/SugarModules/language/application/en_us.lang.php',
      'to_module' => 'application',
      'language' => 'en_us',
    ),
  ),
);