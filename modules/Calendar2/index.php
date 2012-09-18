<?php
if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');
/* * ****************************************************************************
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

 * The COMPANY may not copy, deliver, distribute the SOFTWARE without written
  permit from OpensourceCRM.
 * The COMPANY may not reverse engineer, decompile, or disassemble the 
  SOFTWARE, except and only to the extent that such activity is expressly
  permitted by applicable law notwithstanding this limitation.
 * The COMPANY may not sell, rent, or lease resell, or otherwise transfer for
  value, the SOFTWARE.
 * Termination. Without prejudice to any other rights, OpensourceCRM may 
  terminate this Agreement if the COMPANY fail to comply with the terms and
  conditions of this Agreement. In such event, the COMPANY must destroy all
  copies of the SOFTWARE and all of its component parts.
 * OpensourceCRM will give the COMPANY notice and 30 days to correct above 
  before the contract will be terminated.

  The SOFTWARE is protected by copyright and other intellectual property laws and
  treaties. OpensourceCRM owns the title, copyright, and other intellectual
  property rights in the SOFTWARE.
 * *************************************************************************** */
/* * *******************************************************************************
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights Reserved.
 * ****************************************************************************** */
global $theme, $image_path;
global $current_user;
global $sugar_version;
global $first_day_of_a_week;

if (!isset($first_day_of_a_week))
    $first_day_of_a_week = $current_user->getPreference('week_start_day');
if (empty($first_day_of_a_week))
    $first_day_of_a_week = 'Sunday';

require_once('modules/Calendar2/templates/templates_calendar.php');
require_once('modules/Calendar2/Calendar2.php');

require_once("modules/Calendar2/functions.php");

require_once("modules/Calls/Call.php");
require_once("modules/Meetings/Meeting.php");
require_once("include/utils/db_utils.php");

setlocale(LC_TIME, $current_language);
if (!ACLController::checkAccess('Calendar', 'list', true)) {
    ACLController::displayNoAccess(true);
}

echo get_module_title($mod_strings['LBL_MODULE_NAME'], $mod_strings['LBL_MODULE_TITLE'], true);
echo "\n<BR>\n";
    echo '<input type="button" class="button" id="current-customer-call-list" value="Current Customer Call List" >&nbsp;&nbsp;&nbsp;';
    echo '<input type="button"  class="button" value="Leads Call List" id="leads-call-list">&nbsp;&nbsp;&nbsp;';
    echo '<input type="button"  class="button" value="Recurrence List" id="recurrence-list-button">&nbsp;&nbsp;&nbsp;';
    echo "<input type=\"button\"  class=\"button\" value=\"" . $mod_strings['LBL_SETTINGS'] . "\" onClick='$(\"#settings_dialog\").dialog(\"open\");'>";

//checking previous view
if (empty($_REQUEST['view'])) {
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

if (isset($_REQUEST['ts']))
    $date_arr['ts'] = $_REQUEST['ts'];

if (isset($_REQUEST['day']))
    $date_arr['day'] = $_REQUEST['day'];

if (isset($_REQUEST['month']))
    $date_arr['month'] = $_REQUEST['month'];

if (isset($_REQUEST['week']))
    $date_arr['week'] = $_REQUEST['week'];

if (isset($_REQUEST['year'])) {
    if ($_REQUEST['year'] > 2037 || $_REQUEST['year'] < 1970) {
        echo $mod_string['MSG_CANNOT_HANDLE_YEAR'] . "<br>";
        echo $mod_string['MSG_CANNOT_HANDLE_YEAR2'] . "<br>";
        exit;
    }
    $date_arr['year'] = $_REQUEST['year'];
}

/*
  function add_zero($t){
  if($t < 10)
  return "0" . $t;
  else
  return $t;
  }
 */
// today adjusted for user's timezone
global $timedate;
$gmt_today = $timedate->get_gmt_db_datetime();
$user_today = $timedate->handle_offset($gmt_today, $GLOBALS['timedate']->get_db_date_time_format());
preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $user_today, $matches);
$today_arr = array(
    'year' => $matches[1],
    'month' => $matches[2],
    'day' => $matches[3],
    'hour' => $matches[4],
    'min' => $matches[5]
);
$today_string = $matches[1] . '-' . $matches[2] . '-' . $matches[3];

if (empty($date_arr)) {
    global $timedate;
    $gmt_today = $timedate->get_gmt_db_datetime();
    $user_today = $timedate->handle_offset($gmt_today, $GLOBALS['timedate']->get_db_date_time_format());
    preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $user_today, $matches);
    $date_arr = array(
        'year' => $matches[1],
        'month' => $matches[2],
        'day' => $matches[3],
        'hour' => $matches[4],
        'min' => $matches[5]
    );
} else {
    $gmt_today = $date_arr['year'] . "-" . add_zero($date_arr['month']) . "-" . add_zero($date_arr['day']);
    $user_today = $timedate->handle_offset($gmt_today, $GLOBALS['timedate']->get_db_date_time_format());
}
$args['calendar'] = new Calendar2($_REQUEST['view'], $date_arr);

$show_tasks = $current_user->getPreference('show_tasks');
$auto_accept = $current_user->getPreference('auto_accept');
$args['calendar']->show_tasks = $show_tasks;

if ($_REQUEST['view'] == 'day' || $_REQUEST['view'] == 'week' || $_REQUEST['view'] == 'month') {
    global $current_user;
    $args['calendar']->add_activities($current_user);
}


//if($_REQUEST['view'] == 'shared') {
if (true) {

    global $ids;
    global $current_user;
    global $mod_strings;
    global $app_list_strings, $current_language, $currentModule, $action, $app_strings;
    $current_module_strings = return_module_language($current_language, 'Calendar2');

    $ids = array();
    $user_ids = $current_user->getPreference('shared_ids');
    //get list of user ids for which to display data
    if (!empty($user_ids) && count($user_ids) != 0 && !isset($_REQUEST['shared_ids']))
        $ids = $user_ids;

    elseif (isset($_REQUEST['shared_ids']) && count($_REQUEST['shared_ids']) > 0) {
        $ids = $_REQUEST['shared_ids'];
        if (count($_REQUEST['shared_ids']) > 5)
            $ids = array_slice($ids, 0, 5);
        $current_user->setPreference('shared_ids', $_REQUEST['shared_ids']);
    } else {
        $ids = array($current_user->id);
    }
    if (isPro()) {
        //get team id for which to display user list
        $team = $current_user->getPreference('team_id');
        if (!empty($team) && !isset($_REQUEST['team_id']))
            $team_id = $team;
        elseif (isset($_REQUEST['team_id'])) {
            $team_id = $_REQUEST['team_id'];
            $current_user->setPreference('team_id', $_REQUEST['team_id']);
        } else
            $team_id = '';

        if (empty($_SESSION['team_id']))
            $_SESSION['team_id'] = "";
    }
    if (is551()) {
        $tools = '<div align="right"><a href="index.php?module=' . $currentModule . '&action=' . $action . '&view=shared" class="tabFormAdvLink">&nbsp;<a href="javascript: toggleDisplay(\'shared_cal_edit\');" class="tabFormAdvLink">' . SugarThemeRegistry::current()->getImage('edit', 'alt="' . $current_module_strings['LBL_EDIT'] . '"  border="0"  align="absmiddle"') . '&nbsp;' . $current_module_strings['LBL_EDIT'] . '</a></div>';
    } else {
        $tools = '<div align="right"><a href="index.php?module=' . $currentModule . '&action=' . $action . '&view=shared" class="tabFormAdvLink">&nbsp;<a href="javascript: toggleDisplay(\'shared_cal_edit\');" class="tabFormAdvLink">' . get_image($image_path . 'edit', 'alt="' . $current_module_strings['LBL_EDIT'] . '"  border="0"  align="absmiddle"') . '&nbsp;' . $current_module_strings['LBL_EDIT'] . '</a></div>';
    }

    echo get_form_header($mod_strings['LBL_SHARED_CAL_TITLE'], $tools, false);
    if (empty($_SESSION['shared_ids']))
        $_SESSION['shared_ids'] = "";

    echo "
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
			<input type=\"hidden\" name=\"module\" value=\"" . $currentModule . "\">
			<input type=\"hidden\" name=\"action\" value=\"" . $action . "\">
			<input type=\"hidden\" name=\"view\" value=\"shared\">
			<input type=\"hidden\" name=\"edit\" value=\"0\">
			<table cellpadding=\"0\" cellspacing=\"3\" border=\"0\" align=\"center\">
			<tr><th valign=\"top\"  align=\"center\" colspan=\"2\">
			";

    echo $current_module_strings['LBL_SELECT_USERS'];
    echo "
			</th>
			</tr>
			<tr><td valign=\"top\">";

    if (isPro()) {
        echo "
				<table cellpadding=\"1\" cellspacing=\"1\" border=\"0\" class=\"edit view\" align=\"center\">

				<tr>
					<td valign='top' nowrap><b>" . $current_module_strings['LBL_FILTER_BY_TEAM'] . "</b></td>
					<td valign='top' id=\"teams\"><select id=\"team_id\" onchange='this.form.edit.value=1; this.form.submit();' name=\"team_id\">";

        $teams = get_team_array(false);
        array_unshift($teams, '');
        echo get_select_options_with_id($teams, $team_id);

        echo "</select></td>
				</tr>
				</table>";
    } else {
        $team_id = "";
    }

    echo "
            </td><td valign=\"top\">

			<table cellpadding=\"1\" cellspacing=\"1\" border=\"0\" class=\"edit view\" align=\"center\">
			<tr>
				<td valign='top' nowrap><b>" . $current_module_strings['LBL_USERS'] . "</b></td>
				<td valign='top' id=\"shared_ids_td\"><select id=\"shared_ids\" name=\"shared_ids[]\" multiple size='3'>";


    if (!empty($team_id)) {

        $team = new Team();
        $team->retrieve($team_id);
        $users = $team->get_team_members();
        $user_ids = array();
        foreach ($users as $user) {
            $user_ids[$user->id] = $user->user_name;
        }
        echo get_select_options_with_id($user_ids, $ids);
    } else
        require_once("modules/ZuckerReportParameter/fmp.class.param.slsm.php");
    $o = new fmp_Param_SLSM($current_user->id);
    $o->init();
    //$str .= get_select_options_with_id($o->get_sales_reps_array(), $massForCalendar);
    echo get_select_options_with_id($o->get_sales_reps_array(), $ids);
    unset($o);
    //echo get_select_options_with_id(get_user_array(false), $ids);

    echo "	</select></td>";
    if (is551()) {
        echo "<td><a onclick=\"up('shared_ids');\">" . SugarThemeRegistry::current()->getImage('uparrow_big', 'border="0" style="margin-bottom: 1px;" alt="' . $app_strings['LBL_SORT'] . '"') . "</a><br>
				<a onclick=\"down('shared_ids');\">" . SugarThemeRegistry::current()->getImage('downarrow_big', 'border="0" style="margin-top: 1px;"  alt="' . $app_strings['LBL_SORT'] . '"') . "</a></td>";
    } else {
        echo "<td><a onclick=\"up('shared_ids');\">" . get_image($image_path . 'uparrow_big', 'border="0" style="margin-bottom: 1px;" alt="' . $app_strings['LBL_SORT'] . '"') . "</a><br>
				<a onclick=\"down('shared_ids');\">" . get_image($image_path . 'downarrow_big', 'border="0" style="margin-top: 1px;"  alt="' . $app_strings['LBL_SORT'] . '"') . "</a></td>";
    }
    echo "</tr>
			<tr>";
    echo "<td align=\"right\" colspan=\"2\"><input class=\"button\" type=\"submit\" title=\"" . $app_strings['LBL_SELECT_BUTTON_TITLE'] . "\" accessKey=\"" . $app_strings['LBL_SELECT_BUTTON_KEY'] . "\" value=\"" . $app_strings['LBL_SELECT_BUTTON_LABEL'] . "\" /><input class=\"button\" onClick=\"javascript: toggleDisplay('shared_cal_edit');\" type=\"button\" title=\"" . $app_strings['LBL_CANCEL_BUTTON_TITLE'] . "\" accessKey=\"" . $app_strings['LBL_CANCEL_BUTTON_KEY'] . "\" value=\"" . $app_strings['LBL_CANCEL_BUTTON_LABEL'] . "\"/></td>
			</tr>
			</table>
			</td></tr>
			</table>
			</form>
			
			</div></p>
			";

    global $current_user, $shared_user;
    $shared_user = new User();
    foreach ($ids as $member) {
        $shared_user->retrieve($member);
        $args['calendar']->acts_arr2[$member] = array();
        $args['calendar']->add_activities($shared_user);
    }
}


require_once("include/TimeDate.php");
global $timedate;
$ActRecords = array();
echo "<pre>";
//pr($args['calendar']);
if ($_REQUEST['view'] == "week" || $_REQUEST['view'] == "day" || $_REQUEST['view'] == "month" || $_REQUEST['view'] == "shared") {
    foreach ($args['calendar']->acts_arr2 as $user_id => $acts) {
        foreach ($acts as $act) {
            $newAct = array();
            $newAct['type'] = strtolower($act->sugar_bean->object_name);
            //$newAct['name'] = $act->sugar_bean->name;
            $newAct['name'] = str_replace(array("\rn", "\r", "\n"), array('', '', '\n'), to_html($act->sugar_bean->name));
            $newAct['user_id'] = $user_id;
            $newAct['assigned_user_id'] = $act->sugar_bean->assigned_user_id;

            $newAct['parent_type'] = $act->sugar_bean->parent_type;
            $parent_id = $act->sugar_bean->parent_id;
            $newAct['parent_id'] = $parent_id;
            $newAct['custno_c'] = '';
            $newAct['customer_name'] = '';

            $parent_type = strtolower($newAct['parent_type']);
            if ($parent_type == 'accounts') {
                global $moduleList, $beanList, $beanFiles;
                $module_object = 'Accounts';
                $class_name = $beanList[$module_object];
                $class_file_path = $beanFiles[$class_name];
                require_once $class_file_path;
                $o = new $class_name();
                $o->retrieve($parent_id);

                if (isset($o->custno_c))
                    $newAct['custno_c'] = trim($o->custno_c);
                if (isset($o->name))
                    $newAct['customer_name'] = trim($o->name);
            }

            if ($parent_type == 'leads') {
                global $moduleList, $beanList, $beanFiles;
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
            if ($newAct['type'] == 'call')
                $beanA->cal2_call_id_c = "";
            if ($newAct['type'] == 'meeting')
                $beanA->cal2_meeting_id_c = "";
            $beanA->retrieve($newAct['id']);

            $newAct['cal2_recur_id_c'] = "";
            if ($newAct['type'] == 'call' && !is_null($beanA->cal2_call_id_c))
                $newAct['cal2_recur_id_c'] = $beanA->cal2_call_id_c;

            if ($newAct['type'] == 'meeting' && !is_null($beanA->cal2_meeting_id_c))
                $newAct['cal2_recur_id_c'] = $beanA->cal2_meeting_id_c;

            if ($act->sugar_bean->ACLAccess('DetailView')) {
                $newAct['detailview'] = 1;
            }else
                $newAct['detailview'] = 0;
            if (empty($beanA->id))
                $newAct['detailview'] = 0;

            if ($_REQUEST['view'] == "shared" && $newAct['detailview'] == 1 && isset($beanA->cal2_options_c)) {
                $i_list = get_invitees_list($beanA, $newAct['type']);
                if (!in_array($current_user->id, $i_list))
                    $newAct['detailview'] = 0;
            }

            //addded for edit possibility
            $newAct['detailview'] = 1;







            if ($newAct['type'] == 'task') {
                $newAct['date_start'] = $beanA->date_due;
            }


            $timezone = $GLOBALS['timedate']->getUserTimeZone();



            $newAct['date_start'] = $act->sugar_bean->date_start;
            if ($newAct['type'] == 'task') {
                $newAct['date_start'] = $beanA->date_due;
            }
            $date_unix = to_timestamp_from_uf($newAct['date_start']);


            $newAct['start'] = $date_unix;
            $newAct['time_start'] = timestamp_to_user_formated2($newAct['start'], $GLOBALS['timedate']->get_time_format());



            if ($newAct['type'] == 'task') {
                $newAct['duration_hours'] = 0;
                $newAct['duration_minutes'] = 0;
                $newAct['cal2_category_c'] = "";
                $newAct['location'] = "";
            } else {
                $newAct['duration_hours'] = $act->sugar_bean->duration_hours;
                $newAct['duration_minutes'] = $act->sugar_bean->duration_minutes;
                $newAct['cal2_category_c'] = $act->sugar_bean->cal2_category_c;
                if ($newAct['type'] == 'call' || is_null($act->sugar_bean->location)) {
                    $newAct['location'] = "";
                } else {
                    $newAct['location'] = $act->sugar_bean->location;
                }
            }

            if (empty($newAct['duration_hours']))
                $newAct['duration_hours'] = 0;
            if (empty($newAct['duration_minutes']))
                $newAct['duration_minutes'] = 0;

            if ($newAct['detailview'] == 1) {
                $newAct['status'] = $act->sugar_bean->status;
                if (isPro())
                    $newAct['team_id'] = $act->sugar_bean->team_id;
            }
            //$newAct['description'] = $act->sugar_bean->description;
            $newAct['description'] = str_replace(array("\rn", "\r", "\n"), array('', '', '\n'), to_html($act->sugar_bean->description));
            //if(isset($beanA->cal2_options_c) && !$newAct['detailview']){
            if (isset($beanA->cal2_options_c) || $newAct['detailview'] == 0) {
                $i_list = get_invitees_list($beanA, $newAct['type']);
                if (!in_array($current_user->id, $i_list)) {
                    //$newAct['description'] = "";
                    //$newAct['name'] = "";
                }
            }

            require_once 'modules/Calendar2/class.Calendar2_WidgetTitle.php';
            $newAct['widget_title'] = Calendar2_WidgetTitle::create_from_activity_array($newAct);
            $ActRecords[] = $newAct;
        }
    }
    $cal2_view = $_REQUEST['view'];
    echo ''
    . '<script>'
    . 'var cal2_view = "' . $cal2_view . '";'
    . '</script>'
    ;
}
echo "</pre>";
$args['view'] = $_REQUEST['view'];
?>

<script type="text/javascript" language="JavaScript">
    <!-- Begin
    function toggleDisplay(id){

        if(this.document.getElementById( id).style.display=='none'){
            this.document.getElementById( id).style.display='inline'
            if(this.document.getElementById(id+"link") != undefined){
                this.document.getElementById(id+"link").style.display='none';
            }
            localStorage.sharedCalEditDisplay = 1;
        }else{
            this.document.getElementById(  id).style.display='none'
            if(this.document.getElementById(id+"link") != undefined){
                this.document.getElementById(id+"link").style.display='inline';
            }
            localStorage.sharedCalEditDisplay = 0;
        }
    }
     function toggleDefaultDisplay(id){

        if(typeof localStorage.sharedCalEditDisplay != "undefited" && localStorage.sharedCalEditDisplay == 0){
            this.document.getElementById(  id).style.display='none'
            if(this.document.getElementById(id+"link") != undefined){
                this.document.getElementById(id+"link").style.display='inline';
            }
        }
    }
    //  End -->
</script>

<?php
if ($_REQUEST['view'] == "week" || $_REQUEST['view'] == "day" || $_REQUEST['view'] == "month" || $_REQUEST['view'] == "year" || $_REQUEST['view'] == "shared") {
    echo "<div style='width: 100%;'>";

    echo "<div style='float:left; width: 50%;'>";
    //$tabs = array('day', 'week', 'month', 'year', 'shared');
    $tabs = array('day', 'week', 'month', 'year');
    foreach ($tabs as $tab) {
        ?>
        <input type="button" <?php if ($args['view'] == $tab) { ?>selected="selected" <?php } ?> value=" <?php echo $mod_strings["LBL_" . $args['calendar']->get_view_name($tab)]; ?> " title="<?php echo $mod_strings["LBL_" . $args['calendar']->get_view_name($tab)]; ?>" onclick="window.location.href='index.php?module=<?php echo $currentModule ?>&action=index&view=<?php echo $tab; ?><?php echo $args['calendar']->date_time->get_date_str(); ?>'">&nbsp;
        <?php
    }
    ?>
    &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value=" <?php echo $mod_strings['LNK_VIEW_CALENDAR'] ?> " title="<?php echo $mod_strings['LBL_TODAY']; ?>" onclick="window.location.href='index.php?module=<?php echo $currentModule ?>&action=index&view=<?php echo $args['calendar']->view; ?>&day=<?php echo intval($today_arr['day']); ?>&month=<?php echo intval($today_arr['month']); ?>&year=<?php echo $today_arr['year']; ?>'">&nbsp;
    <?php
    echo "</div>";
    echo "<div style='float:left; text-align: right; width: 50%; font-size: 12px;'>";
    if ($args['view'] != 'shared' && $args['view'] != 'year') {
        ?>
        <input type="button" id="toggle_search" value=" <?php echo $mod_strings['LBL_SHOW_SEARCH']; ?> " title="<?php echo $GLOBALS['app_strings']['LBL_SEARCH']; ?>" onclick="switchSearch();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <?php
    }

    echo "&nbsp;&nbsp;&nbsp;&nbsp;</div>";
    echo "<div style='clear: both;'></div>";

    $con_lang = return_module_language($GLOBALS['current_language'], 'Contacts');
    echo "
	<div id='scDiv' style='margin-top:10px; display: none;'>
	<form action='index.php' method='POST' name='contact_search' id='contact_search' >
	<div class='edit view search' style='width: 99%; border-top: 2px solid;'>
	<table cellpadding=0 celspacing=0 border=0>
";
    echo "<tr>";
    echo "<td scope='row'> " . $con_lang['LBL_LIST_FIRST_NAME'] . " </td>";
    echo "<td><input type='text' size='30' id='first_name_search' name='first_name_search'></td>";

    echo "<td scope='row'> " . $con_lang['LBL_LIST_LAST_NAME'] . " </td>";
    echo "<td><input type='text' size='30' id='last_name_search' name='last_name_search'></td>";

    echo "<td scope='row'> " . $app_strings['LBL_CURRENT_USER_FILTER'] . " </td>";
    echo "<td><input type='checkbox' id='current_user_only_search' name='current_user_only_search' ></td>";

    echo "</tr>";

    echo "<tr>";
    echo "<td scope='row'> " . $con_lang['LBL_LIST_ACCOUNT_NAME'] . " </td>";
    //echo "<td><input type='text' size='30' id='account_name_search' name='account_name_search'></td>";
    ?>
    <td><input type="text" name="account_name_search" class="sqsEnabled" id="account_name_search" size="" value="" title='' autocomplete="off"  >
        <input type="hidden" name="account_id_search" id="account_id_search" value="">
        <input type="button" name="btn_account_name" id="btn_account_name" tabindex="102" title="Select [Alt+T]" accessKey="T" class="button" value="Select" onclick='open_popup("Accounts", 600, 400, "", true, false, {"call_back_function":"set_return","form_name":"contact_search","field_to_name_array":{"id":"account_id_search","name":"account_name_search"}}, "single", true);' >
        <input type="button" name="btn_clr_account_name" id="btn_clr_account_name" tabindex="102" title="Clear [Alt+C]" accessKey="C" class="button" onclick="this.form.account_name_search.value = ''; this.form.account_id_search.value = '';" value="Clear" ></td>
    <script type="text/javascript">
        <!--
        enableQS(false);
        -->
    </script>

    <?php
    echo "<td scope='row'></td>";
    echo "<td></td>";

    echo "</tr>";
    ?>
    </table>
    </form>
    </div>	
    <?php
    echo "
	<button type='button' onclick='searchContacts(0);'>" . $GLOBALS['app_strings']['LBL_SEARCH'] . "</button>
	<button type='button' onclick='clearSearchFields(0); clearContacts(0);'>" . $GLOBALS['app_strings']['LBL_CLEAR_BUTTON_LABEL'] . "</button>
";
    ?>
    <table style='width: 99%' cellspacing="0" cellpadding="0" border="0" class="list view contactsTable">
        <tr class='pagination'>
            <td></td>
            <td></td>
            <td></td>
            <td align='right' colspan=2>	
                <div style='padding: 2px;'>	
                    <button id='scFirst' type="button" title="<?php echo $app_strings['LNK_LIST_START']; ?>" class="button" disabled="disabled">
                        <img src="themes/default/images/start_off.gif" alt="<?php echo $app_strings['LNK_LIST_START']; ?>" height="11" width="13" align="absmiddle" border="0">
                    </button>

                    <button id='scPrev' type="button" class="button" disabled="disabled" title="<?php echo $app_strings['LNK_LIST_PREVIOUS']; ?>">
                        <img src="themes/default/images/previous_off.gif" alt="<?php echo $app_strings['LNK_LIST_PREVIOUS']; ?>" height="11" width="8" align="absmiddle" border="0">
                    </button>

                    <span class="pageNumbers" id="pageNumbers">(0 - 0 of 0)</span>

                    <button id='scNext' type="button" class="button" title="<?php echo $app_strings['LNK_LIST_NEXT']; ?>" disabled="disabled">
                        <img src="themes/default/images/next_off.gif" alt="<?php echo $app_strings['LNK_LIST_NEXT']; ?>" height="11" width="8" align="absmiddle" border="0">
                    </button>

                    <button id='scLast' type="button" class="button" disabled="disabled" title="<?php echo $app_strings['LNK_LIST_END']; ?>">
                        <img src="themes/default/images/end_off.gif" alt="<?php echo $app_strings['LNK_LIST_END']; ?>" height="11" width="13" align="absmiddle" border="0">
                    </button>
                </div>
            </td>
        </tr>

        <tr height="20">
            <th width="25%" nowrap="nowrap" scope="col">
    <?php echo $con_lang['LBL_LIST_NAME']; ?>
            </th>
            <th width="35%" nowrap="nowrap" scope="col">
    <?php echo $con_lang['LBL_LIST_ACCOUNT_NAME']; ?>
            </th>
            <th width="15%" nowrap="nowrap" scope="col">
    <?php echo $con_lang['LBL_LIST_STATE']; ?>
            </th>
            <th width="25%" nowrap="nowrap" scope="col">
    <?php echo $con_lang['LBL_LIST_CITY']; ?>
            </th>
        </tr>

    </table>

    </div>

    <?php
    global $mod_strings;
    echo "<div class='monthHeader'>";
    echo "<div style='float: left; width: 20%;'>";
    template_get_previous_calendar($args);
    echo "</div>";

    echo "<div style='float: left; width: 60%; text-align: center;'><h3>";
    template_echo_date_info($args['view'], $args['calendar']->date_time);
    echo "</h3></div>";

    echo "<div style='float: right;'>";
    template_get_next_calendar($args);
    echo "</div>";
    echo "</div>";
}


if ($_REQUEST['view'] == "week")
//include("modules/Calendar2/PageWeek.php");
    include("modules/Calendar2/PageShared.php");
else
if ($_REQUEST['view'] == "day")
//include("modules/Calendar2/PageDay.php");
    include("modules/Calendar2/PageDayShared.php");
else
if ($_REQUEST['view'] == "month")
//include("modules/Calendar2/PageMonth.php");
    include("modules/Calendar2/PageMonthShared.php");
else
if ($_REQUEST['view'] == "year")
    include("modules/Calendar2/PageYear.php");
else
    include("modules/Calendar2/PageShared.php");

if ($_REQUEST['view'] == "week" || $_REQUEST['view'] == "day" || $_REQUEST['view'] == "month" || $_REQUEST['view'] == "year" || $_REQUEST['view'] == "shared") {
    echo "<div class='monthFooter'>";
    echo "<div style='float: left;'>";
    template_get_previous_calendar($args);
    echo "</div>";
    echo "<div style='float: right;'>";
    template_get_next_calendar($args);
    echo "</div>";
    echo "<br style='clear: both;'>";
    echo "</div>";
}
