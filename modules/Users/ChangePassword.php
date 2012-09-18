<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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
/*********************************************************************************

 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
// This file is used for all popups on this module
// The popup_picker.html file is used for generating a list from which to find and choose one instance.

global $theme;
require_once('modules/Users/User.php');



global $app_strings;
global $mod_strings;

$image_path = "themes/{$theme}/images/";
?>









































































































<script type='text/javascript' language='JavaScript'>
function set_password(form) {
	if (form.is_admin.value == 1 && form.old_password.value == "") {
		alert("<?php echo $mod_strings['ERR_ENTER_OLD_PASSWORD']; ?>");
		return false;
	}
	if (form.new_password.value == "") {
		alert("<?php echo $mod_strings['ERR_ENTER_NEW_PASSWORD']; ?>");
		return false;
	}
	if (form.confirm_new_password.value == "") {
		alert("<?php echo $mod_strings['ERR_ENTER_CONFIRMATION_PASSWORD']; ?>");
		return false;
	}

	if(typeof window.opener.document.DetailView != 'undefined') {
		var openerForm = window.opener.document.DetailView;
		openerForm.return_action.value = 'DetailView';
	} else if(typeof window.opener.document.EditView != 'undefined') {
		var openerForm = window.opener.document.EditView;
		openerForm.return_action.value = 'EditView';
	}

	if (form.new_password.value == form.confirm_new_password.value) {
		if (form.is_admin.value == 1) openerForm.old_password.value = form.old_password.value;
		openerForm.new_password.value = form.new_password.value;
		openerForm.return_module.value = 'Users';
		openerForm.password_change.value = 'true';
		openerForm.return_id.value = openerForm.record.value;
		openerForm.action.value = 'Save';
		openerForm.submit();
		return true;
	}
	else {
		alert("<?php echo $mod_strings['ERR_REENTER_PASSWORDS']; ?>");
		return false;
	}
}


























































</script>

<?php insert_popup_header($theme); ?>

<form>
<?php echo get_form_header($mod_strings['LBL_CHANGE_PASSWORD'], "", false); ?>
<br>
<table width='100%' cellspacing='0' cellpadding='1' border='0'>
<tr>
<?php if (!is_admin($current_user)) {
	echo "<td width='40%' class='dataLabel'>".$mod_strings['LBL_OLD_PASSWORD']."</td>\n";
	echo "<td width='60%' class='dataField'><input name='old_password' type='password' tabindex='1' ></td>\n";
	echo "<input name='is_admin' type='hidden' value='1'>";
	echo "</tr><tr>\n";
}
else echo "<input name='old_password' type='hidden'><input name='is_admin' type='hidden' value='0'>";
?>
<td width='100px' class='dataLabel'nowrap><?php echo $mod_strings['LBL_NEW_PASSWORD']; ?></td>




<td width='100px' class='dataField'><input name='new_password' type='password' tabindex='1' /></td>



















</tr><tr>
<td width='40%' class='dataLabel' nowrap><?php echo $mod_strings['LBL_CONFIRM_PASSWORD']; ?></td>
<td width='60%' class='dataField'><input name='confirm_new_password' type='password' tabindex='1'  ></td>
</tr><tr>
<td width='40%' class='dataLabel'></td>
<td width='60%' class='dataField'></td>
</td></tr>
</table>
<br>
<table width='100%' cellspacing='0' cellpadding='1' border='0'>
<tr>
<td align='right'><input title='<?php echo $app_strings['LBL_SAVE_BUTTON_TITLE']; ?>' accessKey='<?php echo $app_strings['LBL_SAVE_BUTTON_KEY']; ?>' class='button' LANGUAGE=javascript onclick='if (set_password(this.form)) window.close(); else return false;' type='submit' name='button' value='  <?php echo $app_strings['LBL_SAVE_BUTTON_LABEL']; ?>  '></td>
<td align='left'><input title='<?php echo $app_strings['LBL_CANCEL_BUTTON_TITLE']; ?>' accessKey='<?php echo $app_strings['LBL_CANCEL_BUTTON_KEY']; ?>' class='button' LANGUAGE=javascript onclick='window.close()' type='submit' name='button' value='  <?php echo $app_strings['LBL_CANCEL_BUTTON_LABEL']; ?>  '></td>
</tr>
</form>
</table>
<br>
<script type='text/javascript' language='JavaScript'>
function set_password_form_focus() {
	if (document.forms.length > 0) {
		for (i = 0; i < document.forms.length; i++) {
			for (j = 0; j < document.forms[i].elements.length; j++) {
				var field = document.forms[i].elements[j];
				if ((field.type == "password"))
				{
					field.focus();
					break;
				}
    		}
		}
   	}
}

set_password_form_focus();

</script>
<?php insert_popup_footer(); ?>
