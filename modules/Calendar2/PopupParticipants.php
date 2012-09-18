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
$res_lang = return_module_language($current_language, "Resources");
?>

<div class='participants'>
	
	<div style='float: left; width: 250px; text-align: center;'>
		<?php 
		if (isPro()) {
			echo "<div class='sel_captions'>".$GLOBALS['app_strings']['LBL_TEAMS']."</div>";
			echo "<div class='padded'>";
			echo "<select id='sel_team_list' multiple size=4 onClick='teams_or_users = \"teams\";'>";
			echo "</select>";
			echo "</div>";
			echo "<div class='padded'>";
			echo "<button onClick='fill_users();'>".$res_lang['LBL_DISPLAY_BUTTON']."</button>";
			echo "</div>";
		}
		?>
		<div class='sel_captions'><?php echo $GLOBALS['app_strings']['LBL_USERS']; ?></div>		
		<div class='padded'>
			<select id='sel_user_list' multiple size=4 onClick='teams_or_users = "users";'>
			</select>
		</div>
	</div>
	
	<div style='float: left; width: 150px; text-align: center; vertical-align: middle;'>
		<br><br><br>
		<div class='padded'>
			<button onClick='add_users();'><?php echo $res_lang['LBL_ADD_BUTTON']; ?></button>
		</div>
		<div class='padded'>
			<button id='btn_remove_users' onClick='remove_users();'><?php echo $res_lang['LBL_REMOVE_BUTTON']; ?></button>
		</div>
	</div>
	
	<div style='float: left; width: 200px; text-align: center;'>
		<br>
		<div class='padded'>
			<select id='sel_user_list_selected' multiple size=9>
	
			</select>
		</div>
	</div>
	
	<div style='clear:left;'></div>
	
	
	
	<div style='float: left; width: 250px; text-align: center;'>
		<div class='sel_captions'><?php echo $res_lang['LBL_LIST_FORM_TITLE']; ?></div>
		<div class='padded'>
			<select id='sel_resource_list' multiple size=4>
	
			</select>
		</div>

	</div>
	<div style='float: left; width: 150px; text-align: center; vertical-align: middle;'>
		<br>
		<div class='padded'>
			<button onClick='add_resources();'><?php echo $res_lang['LBL_ADD_BUTTON']; ?></button>
		</div>
		<div class='padded'>
			<button id='btn_remove_resources' onClick='remove_resources();'><?php echo $res_lang['LBL_REMOVE_BUTTON']; ?></button>
		</div>
	</div>
	<div style='float: left; width: 200px; text-align: center;'>
		<br>
		<div class='padded'>
			<select id='sel_resource_list_selected' multiple size=4>
	
			</select>
		</div>
	</div>
	<div style='clear:left;'></div>
	
</div>


<div class="h3Row" id="scheduler"></div>


