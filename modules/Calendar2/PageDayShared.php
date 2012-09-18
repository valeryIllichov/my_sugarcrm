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
$t_step = 15;

include("modules/Calendar2/PageComm.php");

?>

<?php

$Tw = date("w",$today_unix - date('Z',$today_unix));
$Ti = date("i",$today_unix - date('Z',$today_unix));
$Ts = date("s",$today_unix - date('Z',$today_unix));
$Th = date("H",$today_unix - date('Z',$today_unix));
$Td = date("d",$today_unix - date('Z',$today_unix));
$Tm = date("m",$today_unix - date('Z',$today_unix));
$Ty = date("Y",$today_unix - date('Z',$today_unix));
$timezone = $GLOBALS['timedate']->getUserTimeZone();

$day_start_unix = $today_unix - $Ts - 60*$Ti - 60*60*$Th; // - $timezone['gmtOffset']*60;

$day_start = date("m/d/Y H:i:s",$day_start_unix);

echo "
<script type='text/javascript'>
pview = 'shared';
var shared_users = new Object();
";
$un = 0;

foreach($ids as $member_id){
	echo "
		shared_users['".$member_id."'] = '".$un."';

	";
        $GLOBALS['log']->test("calendar dashlet ---- investigation: ".$member_id);
        $GLOBALS['log']->test("calendar dashlet ---- investigation: ".$un);
        $GLOBALS['log']->test("calendar dashlet ---- investigation: ______________________________________________________________________");
	$un++;
}
echo "</script>";


echo "<div id='week_div'>";
$un = 0;


$vw = $current_user->getPreference('shared_ids');

	if ($vw == '') {
		$current_user->setPreference('shared_ids', $ids);
	} else {
		$ids = $vw;
	}
foreach($ids as $member_id){
	if($current_user->id == $member_id){
		$un_str = "";
		$un_class = "t_cell";
	}else{
		$un_str = "_".$un;
		//$un_class = "t_icell";
                $un_class = "t_cell";
	}

        $ST_curr = 0;
        $ST_start = 0;
	$shared_user->retrieve($member_id);



	echo "<div style='clear: both;'></div>";
	echo "<div class='monthCalBody'><h5 class='calSharedUser'>".$shared_user->full_name."</h5></div>";

	echo "<div>";

	echo "<div class='left_time_col' style='width: 15%'>";
		echo "<div class='day_head'>&nbsp;</div>";
		global $currentModuele;
		$meridian = true;
		if ($currentModule == 'Home') $meridian = false;
		for($i = $hour_start; $i <= $hour_end; $i++){
			if($i == $hour_end && $minute_end == 0)
				break;
			if(!($i == $hour_start && $minute_start >= 0 + $t_step)){
				echo "<div class='left_cell'>";
				echo timestamp_to_user_formated2($day_start_unix + $i * 3600,$GLOBALS['timedate']->get_time_format($meridian));
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

		$d = 0;
		$curr_time = $day_start_unix + $d*86400 + $hour_start*3600;
		echo "<div class='day_col' style='width: 85%'>";
			echo "<div class='day_head'>&nbsp;</div>";
			for($i = $hour_start; $i <= $hour_end; $i++){
				if($i == $hour_end && $minute_end == 0)
					break;
				if(!($i == $hour_start && $minute_start >= 0 + $t_step)){
					echo "<div id='t_".$curr_time.$un_str."' class='".$un_class."' lang='".timestamp_to_user_formated2($curr_time,$GLOBALS['timedate']->get_time_format())."' datetime='".timestamp_to_user_formated2($curr_time)."' shared_user_id='".$member_id."' shared_user_name='".$shared_user->user_name."'></div>";
				}
				$curr_time += $t_step*60;
				for($j = $t_step; $j < 60; $j += $t_step){
					if($i == $hour_end && $j >= $minute_end)
						break;
					if($i == $hour_start && $minute_start > $j){
						$curr_time += $t_step*60;
						continue;
					}
					echo "<div id='t_".$curr_time.$un_str."' class='".$un_class."' lang='".timestamp_to_user_formated2($curr_time,$GLOBALS['timedate']->get_time_format())."' datetime='".timestamp_to_user_formated2($curr_time)."' shared_user_id='".$member_id."' shared_user_name='".$shared_user->user_name."'></div>";
					$curr_time += $t_step*60;
				}
			}
		echo "</div>";
                	$un++;
}


echo "</div>";



?>
