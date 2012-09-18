<?php
//Custom Widget Sales Reps.
//ItCrimea company
//memet@itcrimea.com
//08.08.2011

require_once('include/generic/SugarWidgets/SugarWidgetFielduser_name.php');
require_once("modules/ZuckerReportParameter/fmp.class.param.slsm.php");

class SugarWidgetFieldSalesReps extends SugarWidgetFielduser_name
{
	function __construct(&$layout_def){
		parent::__construct(&$layout_def);
	}

	function displayInput(&$layout_def)
	{
		$selected_users = empty($layout_def['input_name0']) ? '' : $layout_def['input_name0'];
		$str = '<select multiple="true" size="3" name="' . $layout_def['name'] . '[]">' . $this->distlayUsersSelectByName(get_user_array(false)) . '</select>';
		return $str;
	}
	function distlayUsersSelectByName($userList){
		global $current_user;
		$dude_id = $current_user->id;
		$userName = $userList[$dude_id];
		$getUserFromArray = array();
		$getUserFromArray = array_flip($userList);
		$o = new fmp_Param_SLSM($getUserFromArray[$userName]);
		$o->init();
		$usersList = get_select_options_with_id($o->get_sales_reps_array(), $userName);
		unset($o);
		return $usersList;
	}
}