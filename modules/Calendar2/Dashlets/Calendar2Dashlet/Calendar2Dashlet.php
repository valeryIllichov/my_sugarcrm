<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/******************************************************************************
 OpensourceCRM End User License Agreement

 INSTALLING OR USING THE OpensourceCRM's SOFTWARE THAT YOU HAVE SELECTED TO
 PURCHASE IN THE ORDERING PROCESS (THE "SOFTWARE"), YOU ARE AGREEING ON BEHALF OF
 THE ENTITY LICENSING THE SOFTWARE ("COMPANY") THAT COMPANY WILL BE BOUND BY AND
 IS BECOMING A PARTY TO THIS END USER LICENSE AGREEMENT ("AGREEMENT") AND THAT
 YOU HAVE THE AUTHORITY TO BIND COMPANY.

 IF COMPANY DOES NOT AGREE TO ALL OF THE TERMS OF THIS AGREEMENT, DO NOT SELECT
 THE "ACCEPT" BOX AND DO NOT INSTALL THE SOFTWARE. THE SOFTWARE IS PROTECTED BY
 COPYRIGHT LAWS AND INTERNATIONAL COPYRIGHT TREATIES, AS WELL AS OTHER
 INTELLECTUAL PROPERTY LAWS AND TREATIES. THE SOFTWARE IS LICENSED, NOT SOLD.

 *The COMPANY may not copy, deliver, distribute the SOFTWARE without written
 permit from OpensourceCRM.
 *The COMPANY may not reverse engineer, decompile, or disassemble the
 SOFTWARE, except and only to the extent that such activity is expressly
 permitted by applicable law notwithstanding this limitation.
 *The COMPANY may not sell, rent, or lease resell, or otherwise transfer for
 value, the SOFTWARE.
 *Termination. Without prejudice to any other rights, OpensourceCRM may
 terminate this Agreement if the COMPANY fail to comply with the terms and
 conditions of this Agreement. In such event, the COMPANY must destroy all
 copies of the SOFTWARE and all of its component parts.
 *OpensourceCRM will give the COMPANY notice and 30 days to correct above
 before the contract will be terminated.

 The SOFTWARE is protected by copyright and other intellectual property laws and
 treaties. OpensourceCRM owns the title, copyright, and other intellectual
 property rights in the SOFTWARE.
 *****************************************************************************/
/*********************************************************************************
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights Reserved.
 ********************************************************************************/
require_once('include/Dashlets/Dashlet.php');
require_once('include/Sugar_Smarty.php');
require_once('modules/Calendar2/Calendar2.php');
require_once('modules/Calendar2/templates/templates_calendar.php');
require_once("modules/Calendar2/functions.php");
require_once("modules/Calls/Call.php");
require_once("modules/Meetings/Meeting.php");
require_once('include/utils.php');
require_once("include/utils/db_utils.php");

class Calendar2Dashlet extends Dashlet {
	var $id;

	function Calendar2Dashlet($id, $options) {
                //$GLOBALS['log']->test("calendar dashlet ---- investigation: ".$id);
//                        echo '<pre>';
//                        print_r($options);
//                        echo '</pre>';
		parent::Dashlet($id);
		if(ini_get('display_errors')) ini_set('display_errors', 0);
		$this->isConfigurable = false;
		$this->hasScript = true;  // dashlet has javascript attached to it
		$this->id = $id;
		if(empty($options['title'])) $this->title = translate('LBL_MODULE_NAME', 'Calendar2');
	}

	function displayScript() {
	}

	function display() {
		global $app_list_strings, $current_language, $current_user, $timedate, $mod_strings;

		$this->loadLanguage('Calendar2Dashlet', 'modules/Calendar2/Dashlets/');

		$returnStr = '<div id="calendar2_'.$this->id.'">'.$this->_display()."</div>";
		$mod_strings = return_module_language($current_language, 'Calendar2');

		//return parent::display();
                return parent::display() . $returnStr;
	}


	function _display() {
            //FB::memory();
            
           // $dop = new Opportunity();//PAHA
            //$dop->db->query("set profiling=1;");



               
		global $app_strings, $app_list_strings, $current_language, $sugar_config, $currentModule, $action, $current_user, $theme, $timedate, $image_path, $mod_strings;
		global $first_day_of_a_week;
		$current_module_strings = return_module_language($current_language, 'Calendar2');

		if (!isset($first_day_of_a_week)) $first_day_of_a_week = $current_user->getPreference('week_start_day');
		if(empty($first_day_of_a_week))
		$first_day_of_a_week = 'Sunday';

		setlocale( LC_TIME ,$current_language);
                //pr("We are here 1");
		if(!ACLController::checkAccess('Calendar', 'list', true)){
			ACLController::displayNoAccess(true);
		}
                //pr("We are here 2");

 $GLOBALS['log']->test("calendar dashlet ---- investigation: ".$_REQUEST['view']);

		//checking previous view
		if ( !isset($_REQUEST['view']) || empty($_REQUEST['view'])) {
			$vw = $current_user->getPreference('calendar_view');
			if ($vw == '') {
				$_REQUEST['view'] = 'day';
				$current_user->setPreference('calendar_view', 'day');
			} else {
				$_REQUEST['view'] = $vw;
			}
		} else {
			$current_user->setPreference('calendar_view', $_REQUEST['view']);
		}

		$date_arr = array();

		if ( isset($_REQUEST['ts']))
		$date_arr['ts'] = $_REQUEST['ts'];

		if ( isset($_REQUEST['day']))
		$date_arr['day'] = $_REQUEST['day'];

		if ( isset($_REQUEST['month']))
		$date_arr['month'] = $_REQUEST['month'];

		if ( isset($_REQUEST['week']))
		$date_arr['week'] = $_REQUEST['week'];

		if ( isset($_REQUEST['year'])){
			if ($_REQUEST['year'] > 2037 || $_REQUEST['year'] < 1970){
				echo $mod_string['MSG_CANNOT_HANDLE_YEAR']."<br>";
				echo $mod_string['MSG_CANNOT_HANDLE_YEAR2']."<br>";
				exit;
			}
			$date_arr['year'] = $_REQUEST['year'];
		}

		// today adjusted for user's timezone
		global $timedate;
               	$gmt_today = $timedate->get_gmt_db_datetime();
                //pr($gmt_today);
		$user_today = $timedate->handle_offset($gmt_today, $GLOBALS['timedate']->get_db_date_time_format());
		preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/',$user_today,$matches);
		$today_arr = array(
		      'year'=>$matches[1],
		      'month'=>$matches[2],
		      'day'=>$matches[3],
		      'hour'=>$matches[4],
		      'min'=>$matches[5]
		);
		$today_string = $matches[1].'-'.$matches[2].'-'.$matches[3];
               // pr($user_today);
                //pr(date('Z'));

		if(empty($date_arr)) {
			$gmt_today = $timedate->get_gmt_db_datetime();
			$user_today = $timedate->handle_offset($gmt_today, $GLOBALS['timedate']->get_db_date_time_format());
			preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/',$user_today,$matches);
			$date_arr = array(
	      		'year'=>$matches[1],
	      		'month'=>$matches[2],
	      		'day'=>$matches[3],
	      		'hour'=>$matches[4],
	      		'min'=>$matches[5]
			);
		} else {
			$gmt_today = $date_arr['year'] . "-" . $this->add_zero($date_arr['month']) . "-" . $this->add_zero($date_arr['day']);
			$user_today = $timedate->handle_offset($gmt_today, $GLOBALS['timedate']->get_db_date_time_format());
		}

		$args['calendar'] = new Calendar2($_REQUEST['view'], $date_arr);

		$show_tasks = $current_user->getPreference('show_tasks');
		$auto_accept = $current_user->getPreference('auto_accept');
		$args['calendar']->show_tasks = $show_tasks;

		if ($_REQUEST['view'] == 'day' || $_REQUEST['view'] == 'week' || $_REQUEST['view'] == 'month' ){
			global $current_user;
			$args['calendar']->add_activities($current_user);
		}

		$str = "";

		//if($_REQUEST['view'] == 'shared') {
                if(true) {

			global $ids;
			$ids = array();
                        $ids = array($current_user->id);
			$massForCalendar = array();
			$user_ids = $current_user->getPreference('shared_ids');
			//get list of user ids for which to display data
			if(!empty($user_ids) && count($user_ids) != 0 && !isset($_REQUEST['shared_ids']))
			$ids = $user_ids;

			if(isset($_REQUEST['shared_ids']) && count($_REQUEST['shared_ids']) > 0) {
				$ids = $_REQUEST['shared_ids'];
                                if(count($_REQUEST['shared_ids'])>5) $ids = array_slice( $ids, 0,5);
                                $current_user->setPreference('shared_ids', $_REQUEST['shared_ids']);
			} else {
				//$ids = array($current_user->id);
			}

			if (isPro()) {
				//get team id for which to display user list
				$team = $current_user->getPreference('team_id');
				if(!empty($team) && !isset($_REQUEST['team_id']))
				$team_id = $team;
				if(isset($_REQUEST['team_id'])) {
					$team_id = $_REQUEST['team_id'];
					$current_user->setPreference('team_id', $_REQUEST['team_id']);
				} else
				$team_id = '';

				if(empty($_SESSION['team_id']))
				$_SESSION['team_id'] = "";
			}


			if (is551()) {
				$tools = '<div align="right"><a href="index.php?module='.$currentModule.'&action='.$action.'&view=shared" class="tabFormAdvLink">&nbsp;<a href="javascript: toggleDisplay(\'shared_cal_edit\');" class="tabFormAdvLink">'. SugarThemeRegistry::current()->getImage('edit', 'alt="'.$current_module_strings['LBL_EDIT'].'"  border="0"  align="absmiddle"') .'&nbsp;'.$current_module_strings['LBL_EDIT'].'</a></div>';
			} else {
				$tools = '<div align="right"><a href="index.php?module='.$currentModule.'&action='.$action.'&view=shared" class="tabFormAdvLink">&nbsp;<a href="javascript: toggleDisplay(\'shared_cal_edit\');" class="tabFormAdvLink">'. get_image($image_path.'edit', 'alt="'.$current_module_strings['LBL_EDIT'].'"  border="0"  align="absmiddle"').'&nbsp;'.$current_module_strings['LBL_EDIT'].'</a></div>';
			}

			$str .= get_form_header($current_module_strings['LBL_SHARED_CAL_TITLE'], $tools, false);
			if(empty($_SESSION['shared_ids']))
			$_SESSION['shared_ids'] = "";

			$str .= "
			<script language=\"javascript\">
			function up(name) {
				var td = document.getElementById(name+'_td');
				var obj = td.getElementsByTagName('select')[0];
				obj =(typeof obj == \"string\") ? document.getElementById(obj) : obj;
				if(obj.tagName.toLowerCase() != \"select\" && obj.length < 2)
					return false;
				var sel = new Array();
			
				for(i=0; i<obj.length; i++) {
					if(obj[i].selected == true) {
						sel[sel.length] = i;
					}
				}
				for(i in sel) {
					if(sel[i] != 0 && !obj[sel[i]-1].selected) {
						var tmp = new Array(obj[sel[i]-1].text, obj[sel[i]-1].value);
						obj[sel[i]-1].text = obj[sel[i]].text;
						obj[sel[i]-1].value = obj[sel[i]].value;
						obj[sel[i]].text = tmp[0];
						obj[sel[i]].value = tmp[1];
						obj[sel[i]-1].selected = true;
						obj[sel[i]].selected = false;
					}
				}
			}
			
			function down(name) {
				var td = document.getElementById(name+'_td');
				var obj = td.getElementsByTagName('select')[0];
				if(obj.tagName.toLowerCase() != \"select\" && obj.length < 2)
					return false;
				var sel = new Array();
				for(i=obj.length-1; i>-1; i--) {
					if(obj[i].selected == true) {
						sel[sel.length] = i;
					}
				}
				for(i in sel) {
					if(sel[i] != obj.length-1 && !obj[sel[i]+1].selected) {
						var tmp = new Array(obj[sel[i]+1].text, obj[sel[i]+1].value);
						obj[sel[i]+1].text = obj[sel[i]].text;
						obj[sel[i]+1].value = obj[sel[i]].value;
						obj[sel[i]].text = tmp[0];
						obj[sel[i]].value = tmp[1];
						obj[sel[i]+1].selected = true;
						obj[sel[i]].selected = false;
					}
				}
			}
			</script>
			
			
			<div id='shared_cal_edit' style=''>
			<form name='shared_cal' action=\"index.php\" method=\"post\" >
			<input type=\"hidden\" name=\"module\" value=\"".$currentModule."\">
			<input type=\"hidden\" name=\"action\" value=\"".$action."\">
			<input type=\"hidden\" name=\"view\" value=\"shared\">
			<input type=\"hidden\" name=\"edit\" value=\"0\">
			<table cellpadding=\"0\" cellspacing=\"3\" border=\"0\" align=\"center\">
			<tr><th valign=\"top\"  align=\"center\" colspan=\"2\">
			";

			$str .= $current_module_strings['LBL_SELECT_USERS'];
			$str .= "
			</th>
			</tr>
			<tr><td valign=\"top\">";

			if (isPro()) {
				$str .= "
				<table cellpadding=\"1\" cellspacing=\"1\" border=\"0\" class=\"edit view\" align=\"center\">

				<tr>
					<td valign='top' nowrap><b>".$current_module_strings['LBL_FILTER_BY_TEAM']."</b></td>
					<td valign='top' id=\"teams\"><select id=\"team_id\" onchange='this.form.edit.value=1; this.form.submit();' name=\"team_id\">";

				$teams = get_team_array(false);
				array_unshift($teams, '');
				$str .= get_select_options_with_id($teams, $team_id);

				$str .= "</select></td>
				</tr>
				</table>";

				$str .= "
   				</td><td valign=\"top\">";
			} else {
				$team_id = "";
			}

			$str .= "<table cellpadding=\"1\" cellspacing=\"1\" border=\"0\" class=\"edit view\" align=\"center\">
			<tr>
				<td valign='top' nowrap><b>".$current_module_strings['LBL_USERS']."</b></td>
				<td valign='top' id=\"shared_ids_td\"><select id=\"shared_ids\" name=\"shared_ids[]\" multiple size='3'>";

			if(!empty($team_id)) {

				$team = new Team();
				$team->retrieve($team_id);
				$users = $team->get_team_members();
				$user_ids = array();
				foreach($users as $user) {
					$user_ids[$user->id] = $user->user_name;
				}
				$str .= get_select_options_with_id($user_ids, $ids);
			} else {
				require_once("modules/ZuckerReportParameter/fmp.class.param.slsm.php");
				$o = new fmp_Param_SLSM($current_user->id);
				$o->init();
				//$str .= get_select_options_with_id($o->get_sales_reps_array(), $massForCalendar);
                                $str .= get_select_options_with_id($o->get_sales_reps_array(), $ids);
				unset($o);
//				$str .= get_select_options_with_id(get_user_array(false), $ids);
			}

			$str .= "	</select></td>";
			if (is551()) {
				$str .= "<td><a onclick=\"up('shared_ids');\">". SugarThemeRegistry::current()->getImage('uparrow_big', 'border="0" style="margin-bottom: 1px;" alt="'.$app_strings['LBL_SORT'].'"') ."</a><br>
				<a onclick=\"down('shared_ids');\">". SugarThemeRegistry::current()->getImage('downarrow_big', 'border="0" style="margin-top: 1px;"  alt="'.$app_strings['LBL_SORT'].'"') ."</a></td>
				</tr>
				<tr>";
			} else {
				$str .= "<td><a onclick=\"up('shared_ids');\">". get_image($image_path.'uparrow_big', 'border="0" style="margin-bottom: 1px;" alt="'.$app_strings['LBL_SORT'].'"') ."</a><br>
				<a onclick=\"down('shared_ids');\">". get_image($image_path.'downarrow_big', 'border="0" style="margin-top: 1px;"  alt="'.$app_strings['LBL_SORT'].'"') ."</a></td>
				</tr>
				<tr>";
			}
			$str .= "<td align=\"right\" colspan=\"2\"><input class=\"button\" type=\"submit\" title=\"".$app_strings['LBL_SELECT_BUTTON_TITLE']."\" accessKey=\"".$app_strings['LBL_SELECT_BUTTON_KEY']."\" value=\"".$app_strings['LBL_SELECT_BUTTON_LABEL']."\" /><input class=\"button\" onClick=\"javascript: toggleDisplay('shared_cal_edit');\" type=\"button\" title=\"".$app_strings['LBL_CANCEL_BUTTON_TITLE']."\" accessKey=\"".$app_strings['LBL_CANCEL_BUTTON_KEY']."\" value=\"".$app_strings['LBL_CANCEL_BUTTON_LABEL']."\"/></td>
			</tr>
			</table>
			</td></tr>
			</table>
			</form>
			
			</div></p>
			";


			global $shared_user;
                        //pr($ids);
                        //FB::memory();
			$shared_user = new User();
                        $timedateForDeltaCalculation = new TimeDate();
                        $userTimezone = $timedateForDeltaCalculation->getUserTimeZone($current_user);
                        $deltaServerUser = $timedateForDeltaCalculation->get_hour_offset(true, $userTimezone);
                        
                       
			foreach($ids as $member){
                                                         
				$shared_user->retrieve($member);
                                //FB::memory();
                                $sharedUserTimezone = $timedateForDeltaCalculation->getUserTimeZone($shared_user);
                                $deltaServerSharedUser = $timedateForDeltaCalculation->get_hour_offset(true, $sharedUserTimezone);
                                $deltaWithShared = $deltaServerUser - $deltaServerSharedUser;
                                //pr($deltaWithShared);
                                //pr("_______________");
                                $args['calendar']->add_activities($shared_user);
                                //onset($shared_user);

			}
		}


                //FB::memory();

                //pr($args['calendar']->acts_arr2);

		require_once("include/TimeDate.php");
		global $timedate;
		$ActRecords = array();
		$str .= "<pre>";
                //FB::memory();
		if($_REQUEST['view'] == "week" ||  $_REQUEST['view'] == "day" || $_REQUEST['view'] == "month" || $_REQUEST['view'] == "shared") {
			foreach($args['calendar']->acts_arr2 as $user_id => $acts) {
				foreach($acts as $act){
					$newAct = array();
					$newAct['type'] = strtolower($act->sugar_bean->object_name);
					//$newAct['name'] = $act->sugar_bean->name;
					$newAct['name'] = str_replace(array("\rn", "\r", "\n"), array('','','\n'), to_html($act->sugar_bean->name));
					$newAct['user_id'] = $user_id;
					$newAct['assigned_user_id'] = $act->sugar_bean->assigned_user_id;

					$newAct['parent_type'] = $act->sugar_bean->parent_type;
					$parent_id = $act->sugar_bean->parent_id;
					$newAct['parent_id'] = $parent_id;
					$newAct['custno_c'] = '';
					$newAct['customer_name'] = '';

					$parent_type = strtolower($newAct['parent_type']);
					if ($parent_type == 'accounts') {
						global $moduleList,$beanList,$beanFiles;
						$module_object = 'Accounts';
						$class_name = $beanList[$module_object];
						$class_file_path = $beanFiles[$class_name];
						require_once $class_file_path;
						$o = new $class_name();
						$o->retrieve($parent_id);

						$newAct['custno_c'] = $o->custno_c;
						$newAct['customer_name'] = $o->name;
					}

					if ($parent_type == 'leads') {
						global $moduleList,$beanList,$beanFiles;
						$module_object = 'Leads';
						$class_name = $beanList[$module_object];
						$class_file_path = $beanFiles[$class_name];
						require_once $class_file_path;
						$o = new $class_name();
						$o->retrieve($parent_id);

						$newAct['lead_name'] = trim($o->name);
						$newAct['lead_account_name'] = trim($o->account_name);
					}

					$newAct['id'] = $act->sugar_bean->id;

					$beanA = new $act->sugar_bean->object_name();
					if($newAct['type'] == 'call') $beanA->cal2_call_id_c = "";
					if($newAct['type'] == 'meeting') $beanA->cal2_meeting_id_c = "";
					$beanA->retrieve($newAct['id']);

					$newAct['cal2_recur_id_c'] = "";
					if($newAct['type'] == 'call' && !is_null($beanA->cal2_call_id_c))
					$newAct['cal2_recur_id_c'] = $beanA->cal2_call_id_c;

					if($newAct['type'] == 'meeting' && !is_null($beanA->cal2_meeting_id_c))
					$newAct['cal2_recur_id_c'] = $beanA->cal2_meeting_id_c;

					if($act->sugar_bean->ACLAccess('DetailView')){
						$newAct['detailview'] = 1;
					}else
					//$newAct['detailview'] = 0;
                                            $newAct['detailview'] = 1;
					if(empty($beanA->id))
					$newAct['detailview'] = 0;

					if($_REQUEST['view'] == "shared" && $newAct['detailview'] == 1 && isset($beanA->cal2_options_c)){
						$i_list = get_invitees_list($beanA,$newAct['type']);
						if(!in_array($current_user->id, $i_list))
						{//$newAct['detailview'] = 0;
                                                    
                                                }
					}

					if($newAct['type'] == 'task'){
						$newAct['date_start'] = $beanA->date_due;
						//$date_unix = to_timestamp($newAct['date_start']);
					}

					//$newAct['date_start'] = $act->sugar_bean->date_start;

					$timezone = $GLOBALS['timedate']->getUserTimeZone();
					$newAct['date_start'] = $act->sugar_bean->date_start;
					if($newAct['type'] == 'task'){
						$newAct['date_start'] = $beanA->date_due;
					}
					$date_unix = to_timestamp_from_uf($newAct['date_start']);


					$newAct['start'] = $date_unix;
					$timezone = $GLOBALS['timedate']->getUserTimeZone();

					//remove meridian for dashlet week/month view
					if($_REQUEST['view'] == "week" ||  $_REQUEST['view'] == "month" || $_REQUEST['view'] == "shared") {
						//$newAct['time_start'] = date($GLOBALS['timedate']->get_time_format(false),$date_unix + $GLOBALS['timedate']->get_hour_offset() * 3600);
						$newAct['time_start'] = timestamp_to_user_formated2($newAct['start'],$GLOBALS['timedate']->get_time_format());
					} else {
						//$newAct['time_start'] = date($GLOBALS['timedate']->get_time_format(),$date_unix + $GLOBALS['timedate']->get_hour_offset() * 3600);
						//$newAct['time_start'] = date($GLOBALS['timedate']->get_time_format(),$date_unix);
						$newAct['time_start'] = timestamp_to_user_formated2($newAct['start'],$GLOBALS['timedate']->get_time_format(false));
					}
					if($newAct['type'] == 'task'){
						$newAct['duration_hours'] = 0;
						$newAct['duration_minutes'] = 0;
						$newAct['cal2_category_c'] = "";
						$newAct['location'] = "";
					}else{
						$newAct['duration_hours'] = $act->sugar_bean->duration_hours;
						$newAct['duration_minutes'] = $act->sugar_bean->duration_minutes;
						$newAct['cal2_category_c'] = $act->sugar_bean->cal2_category_c;
						if($newAct['type'] == 'call' || is_null($act->sugar_bean->location)) {
							$newAct['location'] = "";
						} else {
							$newAct['location'] = $act->sugar_bean->location;
						}
					}

					if(empty($newAct['duration_hours']))
					$newAct['duration_hours'] = 0;
					if(empty($newAct['duration_minutes']))
					$newAct['duration_minutes'] = 0;

					if($newAct['detailview'] == 1){
						$newAct['status'] = $act->sugar_bean->status;
						if (isPro()) $newAct['team_id'] = $act->sugar_bean->team_id;
					}
					//$newAct['description'] = $act->sugar_bean->description;
					$newAct['description'] = str_replace(array("\rn", "\r", "\n"), array('','','\n'), to_html($act->sugar_bean->description));
					//if(isset($beanA->cal2_options_c) && !$newAct['detailview']){
					if(isset($beanA->cal2_options_c) || $newAct['detailview'] == 0){
						$i_list = get_invitees_list($beanA,$newAct['type']);
						if(!in_array($current_user->id, $i_list)) {
							//$newAct['description'] = "";
							//$newAct['name'] = "";
						}
					}

					require_once 'modules/Calendar2/class.Calendar2_WidgetTitle.php';
					$newAct['widget_title'] = Calendar2_WidgetTitle::create_from_activity_array($newAct);

					$ActRecords[] = $newAct;
				} //foreach
			} //foreach
			echo ''
			. '<script>'
			. 'var cal2_view = "' . $_REQUEST['view'] . '";'
			. '</script>'
			;
		} //endif


                //FB::memory();

		$str .= "</pre>";

		$args['view'] = $_REQUEST['view'];

		$str .= "<script type='text/javascript' language='JavaScript'>
<!-- Begin
function toggleDisplay(id){

	if(this.document.getElementById( id).style.display=='none'){
		this.document.getElementById( id).style.display='inline'
		if(this.document.getElementById(id+'link') != undefined){
			this.document.getElementById(id+'link').style.display='none';
		}
	}else{
		this.document.getElementById(  id).style.display='none'
		if(this.document.getElementById(id+'link') != undefined){
			this.document.getElementById(id+'link').style.display='inline';
		}
	}
}
		//  End -->
	</script>";

//				echo '<pre>';
//				print_r($args);
//				echo '</pre>';

		if($_REQUEST['view'] == "week" ||  $_REQUEST['view'] == "day" || $_REQUEST['view'] == "month" || $_REQUEST['view'] == "year" || $_REQUEST['view'] == "shared"){
			$str .= "<div style='width: 100%;'>";

			$str .= "<div style='float:left; width: 50%;'>";
			//$tabs = array('day', 'week', 'month', 'year', 'shared');
                        $tabs = array('day', 'week', 'month', 'year');
			foreach($tabs as $tab) {
				$str .= "<input type='button' ";
				if($args['view'] == $tab) $str .= "selected=\"selected\" ";
				$str .= "value='".$current_module_strings["LBL_".$args['calendar']->get_view_name($tab)]."' title='".$current_module_strings["LBL_".$args['calendar']->get_view_name($tab)]."' onclick=\"window.location.href='index.php?module=".$currentModule."&action=index&view=".$tab.$args['calendar']->date_time->get_date_str()."'\">&nbsp";
			}

			$str .= "&nbsp;&nbsp;<input type='button' value='".$current_module_strings['LNK_VIEW_CALENDAR']."' title='".$current_module_strings['LNK_VIEW_CALENDAR']."' onclick='window.location.href=\"index.php?module=".$currentModule."&action=index&view=".$args['calendar']->view."&day=".intval($today_arr['day'])."&month=".intval($today_arr['month'])."&year=".$today_arr['year']."\"'>&nbsp";

			$str .= "</div>";
			$str .= "<div style='float:left; text-align: right; width: 50%; font-size: 12px;'>";
			$str .= "<a href='#' onClick='$(\"#settings_dialog\").dialog(\"open\");'>".$current_module_strings['LBL_SETTINGS']."</a>";
			$str .= "&nbsp;&nbsp;&nbsp;&nbsp;</div>";
			$str .= "<div style='clear: both;'></div>";
			$str .= "</div>";
		}

		$str .= "<div class='monthHeader'>";

		$str .= "<div style='float: left; width: 20%;'>";
		ob_start();
		template_get_previous_calendar($args);
		$str .= ob_get_contents();
		ob_end_clean();
		$str .= "</div>";
			
		$str .= "<div style='float: left; width: 60%; text-align: center;'><h3>";
		ob_start();
		template_echo_date_info($args['view'],$args['calendar']->date_time);
		$str .= ob_get_contents();
		ob_end_clean();
		$str .= "</h3></div>";

		//			echo '<pre>';
		//			print_r($args['calendar']->get_next_date_str());
		//			print_r($args['calendar']->view);
		//			echo '</pre>';
			
		$str .= "<div style='float: right;'>";
		ob_start();
		template_get_next_calendar($args);
		$str .= ob_get_contents();
		ob_end_clean();
		$str .= "</div>";
		$str .= "</div>";

		ob_start();
		if($_REQUEST['view'] == "week")
		//include("modules/Calendar2/PageWeek.php");
                include("modules/Calendar2/PageShared.php");
		else
		if($_REQUEST['view'] == "day")
		//include("modules/Calendar2/PageDay.php");
                include("modules/Calendar2/PageDayShared.php");
		else
		if($_REQUEST['view'] == "month")
		//include("modules/Calendar2/PageMonth.php");
                include("modules/Calendar2/PageMonthShared.php");
		else
		if($_REQUEST['view'] == "year")
		include("modules/Calendar2/PageYear.php");
		else
		include("modules/Calendar2/PageShared.php");
		$str .= ob_get_contents();
		ob_end_clean();


//                $result = $dop->db->query("show profiles;") ;
//
//
//
//                echo "<pre>";
//
//                $row_num = 0;
//
//                $row = $dop->db->fetchByAssoc($result) ;
//		while($row)
//		{
//                        FB::log($row);
//
//			$row_num++;
//			$row = $dop->db->fetchByAssoc($result, $row_num);
//		}
//
//
//
//
//                echo "</pre>";
//FB::memory();
		return $str;
	}

	function add_zero($t){
		if($t < 10)
		return "0" . $t;
		else
		return $t;
	}
}
?>
