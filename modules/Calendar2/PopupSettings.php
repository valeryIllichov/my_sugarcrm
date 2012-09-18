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
require_once("include/utils.php");


global $app_strings,$app_list_strings,$beanList,$mod_strings,$current_language,$currentModule;
global $timedate;
$gmt_default_date_start = $timedate->get_gmt_db_datetime();
$user_default_date_start  = $timedate->handle_offset($gmt_default_date_start, $GLOBALS['timedate']->get_date_time_format());

$current_module_strings = return_module_language($current_language, 'Calendar2');



        $date_format = $timedate->get_cal_date_format();
        $time_format = $timedate->get_user_time_format();
        $TIME_FORMAT = $time_format;      
        $t23 = strpos($time_format, '23') !== false ? '%H' : '%I';
        $time_separator = strpos($time_format, ':') !== false ? ':' : '.';
        if(!isset($match[2]) || $match[2] == '') {
          $CALENDAR_FORMAT = $date_format . ' ' . $t23 . $time_separator . "%M";
        } else {
          $pm = $match[2] == "pm" ? "%P" : "%p";
          $CALENDAR_FORMAT = $date_format . ' ' . $t23 . $time_separator . "%M" . $pm;
        }



		$hours_arr = array ();
		$num_of_hours = 24;
		$start_at = 0;

		$TIME_MERIDIEM = "";
		$TIME_MERIDIEM1 = "";
		$TIME_MERIDIEM2 = "";
		$time_pref = $timedate->get_time_format();
		
		$start_m = "";
		
		if(strpos($time_pref, 'a') || strpos($time_pref, 'A')){
			$num_of_hours = 13;
			$start_at = 1;
			
			$start_m = 'am';
		   	if($d_start_hour == 0){
		   		$d_start_hour = 12;
		   		$start_m = 'am';
		   	}else
		   		if($d_start_hour == 12)
		   			$start_m = 'pm';		   		
		   		
		   	if($d_start_hour > 12){		   		
		   		$d_start_hour = $d_start_hour - 12;
		   		$start_m = 'pm';
		   	}
		   	
			$end_m = 'am';
		   	if($d_end_hour == 0){
		   		$d_end_hour = 12;
		   		$end_m = 'am';
		   	}else
		   		if($d_end_hour == 12)
		   			$end_m = 'pm';		   		
		   		
		   	if($d_end_hour > 12){		   		
		   		$d_end_hour = $d_end_hour - 12;
		   		$end_m = 'pm';
		   	}
		   	
		   	if(strpos($time_pref, 'A')){
		   		$start_m = strtoupper($start_m);
		   		$end_m = strtoupper($end_m);
		   	}

			$options = strpos($time_pref, 'a') ? $app_list_strings['dom_meridiem_lowercase'] : $app_list_strings['dom_meridiem_uppercase'];
		   	$TIME_MERIDIEM1 = get_select_options_with_id($options, $start_m);  
		   	$TIME_MERIDIEM2 = get_select_options_with_id($options, $end_m);  
		   	
		   	$TIME_MERIDIEM1 = "<select id='d_start_meridiem' name='d_start_meridiem' tabindex='2'>".$TIME_MERIDIEM1."</select>";
		   	$TIME_MERIDIEM2 = "<select id='d_end_meridiem' name='d_end_meridiem' tabindex='2'>".$TIME_MERIDIEM2."</select>";
		   	

		} 
		
		for ($i = $start_at; $i < $num_of_hours; $i ++){
			$i = $i."";
			if (strlen($i) == 1)
				$i = "0".$i;
			
			$hours_arr[$i] = $i;
		}
		
		
        	$TIME_START_HOUR_OPTIONS1 = get_select_options_with_id($hours_arr, $d_start_hour);
		$TIME_START_MINUTES_OPTIONS1 = get_select_options_with_id(array('0'=>'00','15'=>'15','30'=>'30','45'=>'45'), $d_start_min);
        	$TIME_START_HOUR_OPTIONS2 = get_select_options_with_id($hours_arr, $d_end_hour);
		$TIME_START_MINUTES_OPTIONS2 = get_select_options_with_id(array('0'=>'00','15'=>'15','30'=>'30','45'=>'45'), $d_end_min);		
		
		$day_view = isset($_REQUEST['day'])? $_REQUEST['day'] : "";
		$month_view = isset($_REQUEST['month'])? $_REQUEST['month'] : "";
		$year_view = isset($_REQUEST['year'])? $_REQUEST['year'] : "";
	
?>
			<form name='settings' id='form_settings' method='POST' action='index.php?module=Calendar2&action=SaveSettings'>
			<input type="hidden" name="view" value="<?php echo $_REQUEST['view'];?>">
			<input type="hidden" name="day" value="<?php echo $day_view;?>">
			<input type="hidden" name="month" value="<?php echo $month_view;?>">
			<input type="hidden" name="year" value="<?php echo $year_view;?>">
			<input type="hidden" name="current_module" value="<?php echo $currentModule;?>">
			<table class='edit view'>
				<tr>
					<td>
						<?php echo $current_module_strings['LBL_START_DAY']; ?>
					</td>
					<td>
						<input type='radio' name='start_day' value='Sunday' <?php if ($startday != 'Monday') echo "checked"; ?> > <?php echo $GLOBALS['app_list_strings']['dom_cal_day_long'][1]; ?>
						<br>
						<input type='radio' name='start_day' value='Monday' <?php if ($startday == 'Monday') echo "checked"; ?> > <?php echo $GLOBALS['app_list_strings']['dom_cal_day_long'][2] ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $current_module_strings['LBL_START_TIME']; ?>
					</td>
					<td>
						<div id="d_start_time_section">
							<select size="1" id="d_start_hours" name="d_start_hours" tabindex="102">
								<?php echo $TIME_START_HOUR_OPTIONS1; ?>
							</select>&nbsp;:
							
							<select size="1" id="d_start_minutes" name="d_start_minutes"  tabindex="102">
								<?php echo $TIME_START_MINUTES_OPTIONS1; ?>
							</select>
								&nbsp;
							<?php echo $TIME_MERIDIEM1;?>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $current_module_strings['LBL_END_TIME']; ?>
					</td>
					<td>
						<div id="d_end_time_section">
							<select size="1" id="d_end_hours" name="d_end_hours" tabindex="102">
								<?php echo $TIME_START_HOUR_OPTIONS2; ?>
							</select>&nbsp;:
							
							<select size="1" id="d_end_minutes" name="d_end_minutes"  tabindex="102">
								<?php echo $TIME_START_MINUTES_OPTIONS2; ?>
							</select>
								&nbsp;
							<?php echo $TIME_MERIDIEM2;?>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $current_module_strings['LBL_SHOW_TASKS']; ?>
					</td>
					<td>
	
							<select size="1" name="show_tasks" tabindex="102">
								<option value='' <?php if(!$show_tasks) echo "selected"; ?> ><?php echo $current_module_strings['LBL_NO'];?></option>
								<option value='true' <?php if($show_tasks) echo "selected"; ?>  ><?php echo $current_module_strings['LBL_YES'];?></option>								
							</select>


					</td>
				</tr>
				<tr>
					<td>
						<?php echo $current_module_strings['LBL_AUTO_ACCEPT']; ?>
					</td>
					<td>
	
							<select size="1" name="auto_accept" tabindex="102">
								<option value='' <?php if(!$auto_accept) echo "selected"; ?> ><?php echo $current_module_strings['LBL_NO'];?></option>
								<option value='true' <?php if($auto_accept) echo "selected"; ?>  ><?php echo $current_module_strings['LBL_YES'];?></option>								
							</select>


					</td>
				</tr>
					
			</table>
			</form>
				
<?php

	

?>
