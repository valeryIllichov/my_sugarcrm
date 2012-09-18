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
?>

<table class="edit view" cellspacing="1" cellpadding="0" border="0" width="100%">

	<tr>
		<td>
			<?php echo $calls_lang['LBL_REPEAT_TYPE'].":"; ?>
		</td>
		<td>
			<select id='repeat_type' name='repeat_type' onChange='repeat_type_selected();'>
			<?php
					$repeat_types = array(
						''			=>	'None',
						'Daily'			=>	'Daily',
						'Weekly'		=>	'Weekly',
						'Monthly (date)'	=>	'Monthly (date)',
						'Monthly (day)'		=>	'Monthly (day)',
						'Yearly'		=>	'Yearly',
					);
					foreach($repeat_types as $k => $v){
                                                                                            if($k == ''){
                                                                                                 echo "<option value='".$k."' selected>".$v."</option>";
                                                                                            }else{
                                                                                                echo "<option value='".$k."' >".$v."</option>";
                                                                                            }
                                                                                          }
			?>				
			</select>
		</td>
		
		<td>
			<?php echo $calls_lang['LBL_REPEAT_INTERVAL'].":"; ?>		
		</td>
		<td>
			<select id='repeat_interval' name='repeat_interval'>
				<option value=''>1</option>
			<?php
				for($i = 2; $i <= 31; $i++)
					echo "<option value='".$i."'>".$i."</option>";			
				
			?>
			</select>
		</td>			
	</tr>
	<tr>
		<td>
			<?php echo $calls_lang['LBL_REPEAT_END_DATE'].":"; ?>	
		</td>
		<td>
			<?php
			
			//$default_end_date = $today_unix + 24*60*60*date("t",$today_unix - date('Z'));
                                                    $default_end_date = strtotime(date("12/31/Y"));
                                                    $default_end_date = timestamp_to_user_formated($default_end_date,$GLOBALS['timedate']->get_date_format());				
			
			?>
			<input autocomplete="off" name="repeat_end_date" id="repeat_end_date" value="<?php echo $default_end_date;?>" title="" tabindex="105" size="11" maxlength="10" type="text">
			<img src="themes/default/images/jscalendar.gif" alt="Enter Date" id="repeat_end_date_trigger" align="absmiddle" border="0">
			<script type="text/javascript">
			Calendar.setup ({
			inputField : "repeat_end_date",
			daFormat : "<?php echo $CALENDAR_FORMAT;?>",
			button : "repeat_end_date_trigger",
			singleClick : true,
			dateStr : "<?php echo $default_end_date;?>",
			step : 1,
			weekNumbers:false
			}
			);
			</script>
		</td>	
	</tr>
	<tr>
		<td>
			<?php echo $calls_lang['LBL_REPEAT_DAYS'].":"; ?>	
		</td>
		<td>
			<?php
				foreach($GLOBALS['app_list_strings']['dom_cal_day_long'] as $k => $v)
					if($k != '0')
						echo "<input type='checkbox' class='weeks_checks' name='repeat_days[]' value='".$k."'> ".$v . "<br>";	
			?>			
		</td>
	</tr>
	
</table>

<?php

?>
