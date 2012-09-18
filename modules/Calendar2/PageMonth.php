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
$t_step = 60;

include("modules/Calendar2/PageComm.php");

?>
<style type="text/css">
		.day_col, .left_time_col{
			border-bottom-width: 2px;	
		}
</style>
<?php


$Tw = date("w",$today_unix - date('Z',$today_unix));
$Ti = date("i",$today_unix - date('Z',$today_unix));
$Ts = date("s",$today_unix - date('Z',$today_unix));
$Th = date("H",$today_unix - date('Z',$today_unix));
$Td = date("d",$today_unix - date('Z',$today_unix));
$Tm = date("m",$today_unix - date('Z',$today_unix));
$Ty = date("Y",$today_unix - date('Z',$today_unix));
$Tt = date("t",$today_unix - date('Z',$today_unix));
$timezone = $GLOBALS['timedate']->getUserTimeZone();


$month_start_unix = $today_unix - $Ts - 60*$Ti - 60*60*$Th - 60*60*24*($Td - 1);// - $timezone['gmtOffset']*60;
$month_end_unix = $month_start_unix + 60*60*24*($Tt);

//$Tw = date("w",$month_start_unix + $timezone['gmtOffset']*60 - date('Z',$month_start_unix));
$Tw = date("w",$month_start_unix - date('Z',$month_start_unix));
$week_start_unix = $month_start_unix - 60*60*24*($Tw);

if($startday == "Monday"){
	$week_start_unix = $week_start_unix + 60*60*24;	
	
	if(date("j",$week_start_unix - date('Z',$week_start_unix)) == 1)
		$week_start_unix = $week_start_unix - 7*60*60*24;
}
$week_end_unix = $week_start_unix + 60*60*24*7;
if($startday == "Monday"){
	$week_end_unix = $week_end_unix + 60*60*24;	
}

global $currentModule;

echo "<div id='week_div'>";

	$curr_time_g = $week_start_unix;
	$w = 0;
	while($curr_time_g < $month_end_unix){
		
		echo "<div class='left_time_col'>";
			echo "<div class='day_head'><a href='index.php?module=Calendar2&action=index&view=week&hour=0&day=".timestamp_to_user_formated2($curr_time_g,'j')."&month=".timestamp_to_user_formated2($curr_time_g,'n')."&year=".timestamp_to_user_formated2($curr_time_g,'Y')."'>Wk ".timestamp_to_user_formated2($curr_time_g,'W')."</a></div>";		
			for($i = $hour_start; $i <= $hour_end; $i++){
				if($i == $hour_end && $minute_end == 0)
					break;
				if(!($i == $hour_start && $minute_start >= 0 + $t_step)){
					echo "<div class='left_cell'>";
					if ($currentModule == "Home") {
						echo timestamp_to_user_formated2($curr_time_g + $i * 3600 ,$GLOBALS['timedate']->get_time_format(false));
					} else {
						echo timestamp_to_user_formated2($curr_time_g + $i * 3600 ,$GLOBALS['timedate']->get_time_format());
					}
					echo "</div>";
				}
				for($j = $t_step; $j < 60; $j += $t_step){
					if($i == $hour_end && $j >= $minute_end)
						break;					
					if($i == $hour_start && $minute_start > $j)
						continue;
					echo "<div class='left_cell'>&nbsp;</div>";
				}
			}	
		echo "</div>";

		global $currentModule;
		$meridian = true;
		if ($currentModule == 'Home') $meridian = false;

		for($d = 0; $d < 7; $d++){
			$curr_time = $week_start_unix + $d*86400 + $hour_start*3600 + $w*60*60*24*7;

			echo "<div class='day_col'>";
				//echo "<div class='day_head'><a href='index.php?module=Calendar2&action=index&view=day&hour=0&day=".timestamp_to_user_formated($curr_time,'j')."&month=".timestamp_to_user_formated($curr_time,'n')."&year=".timestamp_to_user_formated($curr_time,'Y')."'>".$weekday_names[$d]." ".date("d",$curr_time)."</a></div>";		
				echo "<div class='day_head' date='".t_to_date($curr_time)."'><a href='index.php?module=Calendar2&action=index&view=day&hour=0&day=".timestamp_to_user_formated2($curr_time,'j')."&month=".timestamp_to_user_formated2($curr_time,'n')."&year=".timestamp_to_user_formated2($curr_time,'Y')."'>".$weekday_names[$d]." ".timestamp_to_user_formated2($curr_time, "d")."</a></div>";		
				for($i = $hour_start; $i <= $hour_end; $i++){
					if($i == $hour_end && $minute_end == 0)
						break;
					if(!($i == $hour_start && $minute_start >= 0 + $t_step)){
						echo "<div id='t_".$curr_time."' class='t_cell' lang='".timestamp_to_user_formated2($curr_time,$GLOBALS['timedate']->get_time_format($meridian))."' datetime='".timestamp_to_user_formated2($curr_time)."'></div>";
					}
					$curr_time += $t_step*60;
					for($j = $t_step; $j < 60; $j += $t_step){
						if($i == $hour_end && $j >= $minute_end)
							break;
						if($i == $hour_start && $minute_start > $j){
							$curr_time += $t_step*60;
							continue;	
						}					
						echo "<div id='t_".$curr_time."' class='t_cell' lang='".timestamp_to_user_formated2($curr_time,$GLOBALS['timedate']->get_time_format($meridian))."' datetime='".timestamp_to_user_formated2($curr_time)."'></div>";
						$curr_time += $t_step*60;
					}
				}
			echo "</div>";			
		}

		echo "<div style='clear: left;'></div>";
		$curr_time_g += 60*60*24*7;
		$w++;
	}

echo "</div>";




?>
