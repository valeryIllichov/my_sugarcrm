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
/////////////////////////////////
// template
/////////////////////////////////
global $timedate;

function template_echo_slice_activities_shared(& $args) {
	global $app_list_strings;
	global $image_path;
	global $shared_resource, $timedate;
	$count = 0;
	//$GLOBALS['log']->debug("Now in template_echo_slice_activities_shared count of acts_arr=".count($args['slice']->acts_arr[$shared_resource->id]));

	if (empty ($args['slice']->acts_arr[$shared_resource->id])) {
		return;
	}

	foreach ($args['slice']->acts_arr[$shared_resource->id] as $act) {
		$count ++;
		echo "<div style=\"margin-top: 1px;\">
		<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\" class=\"monthCalBodyDayItem\">";
		
		if($act->sugar_bean->object_name == 'Call') { 
			echo "<tr><td class=\"monthCalBodyDayIconTd\">";
			get_image($image_path.'Calls','alt=\"'.$app_list_strings['call_status_dom'][$act->sugar_bean->status].': '.$act->sugar_bean->name.'\"'); 
			echo "</td>";

			if(empty($act->sugar_bean->name)) {
				echo "<td class=\"monthCalBodyDayItemTd\" width=\"100%\">";
				echo $timedate->to_display_time($act->sugar_bean->time_start, true, false); 
				echo "</td></tr>";
			} else {
				echo "<td class=\"monthCalBodyDayItemTd\" width=\"100%\">
					<a href=\"index.php?module=Calls&action=DetailView&record=".
					$act->sugar_bean->id."\" class=\"monthCalBodyDayItemLink\">".
					$app_list_strings['call_status_dom'][$act->sugar_bean->status].":".
					$act->sugar_bean->name."(".
					$timedate->to_display_time($act->sugar_bean->time_start, true, false)."
					)</a></td></tr>";
			}
		} else if ($act->sugar_bean->object_name == 'Meeting') { 
			echo "<td class=\"monthCalBodyDayIconTd\">".
				get_image($image_path.'Meetings','alt=\"'.$app_list_strings['meeting_status_dom'][$act->sugar_bean->status].': '.$act->sugar_bean->name.'\"'); 
			echo "</td>";
		
			if (empty($act->sugar_bean->name)) {
				echo "<td class=\"monthCalBodyDayItemTd\" width=\"100%\">".
					$timedate->to_display_time($act->sugar_bean->time_start, true, false); 
				echo "</td></tr>";
			} else {
				echo "<td class=\"monthCalBodyDayItemTd\" width=\"100%\">
					<a href=\"index.php?module=Meetings&action=DetailView&record=".
					$act->sugar_bean->id."\" class=\"monthCalBodyDayItemLink\">".
					$app_list_strings['meeting_status_dom'][$act->sugar_bean->status].":".
					$act->sugar_bean->name."<br>(".
					$timedate->to_display_time($act->sugar_bean->time_start, true, false).")
					</a></td></tr>";
			}
		}
		echo "</table><div>";
	}
}

/////////////////////////////////
// template
/////////////////////////////////
function template_calendar(& $args) {
	global $timedate;
	$newargs = array ();
	$newargs['view'] = $args['view'];
	$newargs['calendar'] = $args['calendar'];
	echo "</div></p>
	<script language=\"javascript\">";
	echo "
	function set_dates(date,time)
	{
	document.CallSave.date_start.value = date;
	document.CallSave.time_start.value = time;
	
	}
	</script>
	<table id=\"daily_cal_table_outside\" width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"monthBox\">
	<tr>
	<td>
	  <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"monthHeader\">
	  <tr>
	  <td width=\"1%\" class=\"monthHeaderPrevTd\" nowrap>";
	template_get_previous_calendar($args);

		echo "
	  </td>
	  <td width=\" 98 % \" align=center scope='row'>";
?>
<span class="monthHeaderH3">
<?php template_echo_date_info($args['view'],$args['calendar']->date_time); ?>
</span>
  </td>
  <td align="right" class="monthHeaderNextTd" width="1%" nowrap><?php
  template_get_next_calendar($args);
?> </td>
  </tr>
  </table>
</td>
</tr>
<tr>
<td class="monthCalBody">
<?php
	require_once ('modules/Resources/Resource.php');
	$shared_args = array ();
	foreach ($args as $key => $val) {
		$shared_args[$key] = $val;
	}
	$shared_args['calendar'] = $args['calendar'];
	global $ids;
	foreach ($ids as $member) {
		global $shared_resource;
		$shared_resource = new Resource();
		$shared_resource->retrieve($member);
		$shared_args['calendar']->show_tasks = false;
		$shared_args['calendar']->add_activities($shared_resource);
		$shared_args['show_link'] = 'on';
		echo '<h5 class="calSharedUser">'.$shared_resource->name.'</h5>';
		template_calendar_horizontal($shared_args);
	}
?>
</td>
</tr>
<tr>
<td>
  <table width="100%" cellspacing="0" cellpadding="0" class="monthFooter">
  <tr>
  <td width="50%" class="monthFooterPrev"><?php template_get_previous_calendar($args); ?></td>
  <td align="right" width="50%" class="monthFooterNext"><?php template_get_next_calendar($args); ?></td>
  </tr>
  </table>

</td>
</tr>
</table>
<?php
}

function template_calendar_horizontal(& $args) {
	echo "<table id=\"daily_cal_table\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"100%\"><tr>";

	// need to change these values after we find out what activities
	// occur outside of these values
	$start_slice_idx = $args['calendar']->get_start_slice_idx();
	$end_slice_idx = $args['calendar']->get_end_slice_idx();
	$cur_slice_idx = 1;
	for ($cur_slice_idx = $start_slice_idx; $cur_slice_idx <= $end_slice_idx; $cur_slice_idx ++) {
		$calendar = $args['calendar'];
		$args['slice'] = $calendar->slice_hash[$calendar->slices_arr[$cur_slice_idx]];

		template_cal_horizontal_slice($args);
	}

	echo "</tr></table>";
}

function template_cal_horizontal_slice(& $args) {
	echo "<td width=\"14%\" class=\"dailyCalBodyItems\" id=\"bodyItem\" scope='row' valign=\"top\">";

	if($args['show_link'] == 'on') {
		template_echo_slice_date($args);
	} else {
		template_echo_slice_date_nolink($args);
	}

	template_echo_slice_activities_shared($args);

	echo "</td>";
}

function get_current_day(& $args) {
	global $timedate;
	static $user_today_timestamp = null;
	
	// adjust for user's TZ
	if(!isset($user_today_timestamp)) { 
	    $gmt_today = $timedate->get_gmt_db_datetime();
	    $user_today = $timedate->handle_offset($gmt_today, $GLOBALS['timedate']->get_db_date_time_format());
		preg_match_all('/\d*/', $user_today, $matches);
		$matches = $matches[0];
		$user_today_timestamp = mktime($matches[6], $matches[8], '0', $matches[2], $matches[4], $matches[0]);
	}
    
	$slice = $args['slice'];
	if($slice->start_time->get_mysql_date() == date($GLOBALS['timedate']->get_db_date_time_format(), $user_today_timestamp)) {
		return true;
	}
}

function template_echo_daily_view_hour(& $args) {

	$slice = $args['slice'];
	$hour = $slice->start_time->get_hour();
	return $hour;

}

function template_echo_daily_view_24_hour(& $args) {

	$slice = $args['slice'];
	$hour = $slice->start_time->get_24_hour();
	return $hour;

}

function template_echo_slice_date(& $args) {
	global $mod_strings;
    global $timedate;
	$slice = $args['slice'];

	if ($slice->view != 'hour') {
		if ($slice->start_time->get_day_of_week_short() == 'Sun' || $slice->start_time->get_day_of_week_short() == 'Sat') {
			echo "<a href=\"index.php?module=Calendar2&action=index&view=".$slice->get_view()."&".$slice->start_time->get_date_str()."\" ";
		} else {
			echo "<a href=\"index.php?module=Calendar2&action=index&view=".$slice->get_view()."&".$slice->start_time->get_date_str()."\" ";
		}
	}

	echo "class='monthCalBodyWeekDayDateLink'>";
	echo $slice->start_time->get_day_of_week_short();
	echo "&nbsp;";
	echo $slice->start_time->get_day();
//	echo $slice->start_time->get_day();

	echo "</a>";
}

function template_echo_slice_date_nolink(& $args) {
	global $mod_strings;
	$slice = $args['slice'];
	echo $slice->start_time->get_day_of_week_short();
	echo "&nbsp;";
	echo $slice->start_time->get_day();
}

function template_echo_date_info($view, $date_time) {
	global $current_user;
	$dateFormat = $current_user->getUserDateTimePreferences();
	$first_day = $date_time->get_day_by_index_this_week(0);
	$last_day = $date_time->get_day_by_index_this_week(6);

			for($i=0; $i<strlen($dateFormat['date']); $i++) {
				switch($dateFormat['date']{$i}) {
					case "Y":
						echo " ".$first_day->year;
						break;
					case "m":
						echo " ".$first_day->get_month_name();
						break;
					case "d":
						echo " ".$first_day->get_day();
						break;
				}
			}
			echo " - ";
			for($i=0; $i<strlen($dateFormat['date']); $i++) {
				switch($dateFormat['date']{$i}) {
					case "Y":
						echo " ".$last_day->year;
						break;
					case "m":
						echo " ".$last_day->get_month_name();
						break;
					case "d":
						echo " ".$last_day->get_day();
						break;
				}
			}

}

function template_get_next_calendar(& $args) {
	global $image_path;
	global $mod_strings;
?>
<a href="index.php?action=index&module=Resources&action=WeeklyListView&view=<?php echo $args['calendar']->view; ?>&<?php echo $args['calendar']->get_next_date_str(); ?>" class="NextPrevLink"><?php echo $mod_strings["LBL_NEXT_".$args['calendar']->get_view_name($args['calendar']->view)]; ?>&nbsp;<?php echo get_image($image_path.'calendar_next','alt="'. $mod_strings["LBL_NEXT_".$args['calendar']->get_view_name($args['calendar']->view)].'" align="absmiddle" border="0"'); ?></a>
<?php

}

function template_get_previous_calendar(& $args) {
	global $mod_strings;
	global $image_path;
?>
<a href="index.php?action=index&module=Resources&action=WeeklyListView&view=<?php echo $args['calendar']->view; ?>&<?php echo $args['calendar']->get_previous_date_str(); ?>" class="NextPrevLink"><?php echo get_image($image_path.'calendar_previous','alt="'. $mod_strings["LBL_PREVIOUS_".$args['calendar']->get_view_name($args['calendar']->view)].'" align="absmiddle" border="0"'); ?>&nbsp;&nbsp;<?php echo $mod_strings["LBL_PREVIOUS_".$args['calendar']->get_view_name($args['calendar']->view)]; ?></a>
<?php

}
?>
