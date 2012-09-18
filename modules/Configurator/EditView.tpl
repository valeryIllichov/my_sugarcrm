{*

/**
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
 */



*}
<script type='text/javascript' src='include/javascript/overlibmws.js'></script>
<BR>
<form name="ConfigureSettings" enctype='multipart/form-data' method="POST" action="index.php" onSubmit="return (add_checks(document.ConfigureSettings) && check_form('ConfigureSettings'));">
<input type='hidden' name='action' value='EditView'/>
<input type='hidden' name='module' value='Configurator'/>
<span class='error'>{$error.main}</span>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>

	<td style="padding-bottom: 2px;">
		<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" accessKey="{$APP.LBL_SAVE_BUTTON_KEY}" class="button"  type="submit"  name="save" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " >
		&nbsp;<input title="{$MOD.LBL_SAVE_BUTTON_TITLE}"  class="button"  type="submit" name="restore" value="  {$MOD.LBL_RESTORE_BUTTON_LABEL}  " >
		&nbsp;<input title="{$MOD.LBL_CANCEL_BUTTON_TITLE}"  onclick="document.location.href='index.php?module=Administration&action=index'" class="button"  type="button" name="cancel" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " > </td>
	</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr><td>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tabForm">
	<tr><th align="left" class="dataLabel" colspan="4"><h4 class="dataLabel">{$MOD.DEFAULT_SYSTEM_SETTINGS}</h4></th>
	</tr><tr>
<td>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td  class="dataLabel">{$MOD.LIST_ENTRIES_PER_LISTVIEW}: </td>
		<td  class="dataField">
			<input type='text' size='4' name='list_max_entries_per_page' value='{$config.list_max_entries_per_page}'>
		</td>
		<td  class="dataLabel">{$MOD.LIST_ENTRIES_PER_SUBPANEL}: </td>
		<td  class="dataField">
			<input type='text' size='4' name='list_max_entries_per_subpanel' value='{$config.list_max_entries_per_subpanel}'>
		</td>
	</tr>












	<tr>
		<td  class="dataLabel">{$MOD.DISPLAY_RESPONSE_TIME}: </td>
		{if !empty($config.calculate_response_time )}
			{assign var='calculate_response_time_checked' value='CHECKED'}
		{else}
			{assign var='calculate_response_time_checked' value=''}
		{/if}
		<td class="dataField"><input type='hidden' name='calculate_response_time' value='false'><input name='calculate_response_time'  type="checkbox" value="true" {$calculate_response_time_checked}></td>

		<td  class="dataLabel">{$MOD.DISPLAY_LOGIN_NAV}: </td>
		{if !empty($config.login_nav)}
			{assign var='login_nav_checked' value='CHECKED'}
		{else}
			{assign var='login_nav_checked' value=''}
		{/if}
		<td class="dataField"><input type='hidden' name='login_nav' value='false'><input name='login_nav'  type="checkbox" value="true" {$login_nav_checked}></td>
	</tr>
	<tr>
		<td  class="dataLabel">{$MOD.LOCK_HOMEPAGE}:{sugar_help text=$MOD.LOCK_HOMEPAGE_HELP}</td>
		<td  class="dataField">
			{if !empty($config.lock_homepage)}
				{assign var='lock_homepage_checked' value='CHECKED'}
			{else}
				{assign var='lock_homepage_checked' value=''}
			{/if}
			<input type='hidden' name='lock_homepage' value='false'>
			<input type='checkbox' name='lock_homepage' value='true' {$lock_homepage_checked}>
		</td>
		<td  class="dataLabel">{$MOD.LOCK_SUBPANELS}: </td>
		<td  class="dataField">
			{if !empty($config.lock_subpanels)}
				{assign var='lock_subpanels_checked' value='CHECKED'}
			{else}
				{assign var='lock_subpanels_checked' value=''}
			{/if}
			<input type='hidden' name='lock_subpanels' value='false'>
			<input type='checkbox' name='lock_subpanels' value='true' {$lock_subpanels_checked}>
		</td>
	</tr>
	<tr>
		<td  class="dataLabel" nowrap>{$MOD.MAX_DASHLETS}: </td>
		<td  class="dataField" >
			<input type='text' size='4' name='max_dashlets_homepage' value='{$config.max_dashlets_homepage}'>
		</td>
		<td  class="dataLabel" nowrap>{$MOD.LBL_USE_REAL_NAMES}: </td>
		{if !empty($config.use_real_names)}
			{assign var='use_real_names' value='CHECKED'}
		{else}
			{assign var='use_real_names' value=''}
		{/if}
		<td class="dataField">
			<input type='hidden' name='use_real_names' value='false'>
			<input name='use_real_names'  type="checkbox" value="true" {$use_real_names}>
		</td>
	</tr>
	<tr>
		<td  class="dataLabel" width='15%' nowrap>{$MOD.SYSTEM_NAME}: </td>
		<td  class="dataField" width='35%'>
			<input type='text' name='system_name' value='{$settings.system_name}'>
		</td>
		<td  class="dataLabel" ></td>
		<td class="dataField" >
		</td>
	</tr>
</table>
</td></tr>
</table>

<br>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tabForm">
	<tr><th align="left" class="dataLabel" colspan="4"><h4 class="dataLabel">{$MOD.IMAGES}</h4></th>
	</tr><tr>
<td>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td  class="dataLabel" nowrap>{$MOD.CURRENT_LOGO}: </td>
		<td  class="dataField">
			<img id="company_logo_image" src='{$company_logo}' height="40" width="212">
		</td>
	</tr>

	<tr>
		<td  class="dataLabel" nowrap>
			{$MOD.NEW_LOGO}:{sugar_help text=$MOD.NEW_LOGO_HELP}
        </td>
        <td  class="dataField">
            <div id="container_upload"></div>
            <input type='text' id='company_logo' name='company_logo' style="display:none">
        </td>
	</tr>

















</table>
</td>
</tr>
</table>
<br>
{if !empty($settings.system_ldap_enabled)}
		{assign var='system_ldap_enabled_checked' value='CHECKED'}
		{assign var='ldap_display' value='inline'}
	{else}
		{assign var='system_ldap_enabled_checked' value=''}
		{assign var='ldap_display' value='none'}
{/if}

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tabForm">
<tr><td>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr><th align="left" class="dataLabel" colspan='3'><h4 class="dataLabel">{$MOD.LBL_LDAP_TITLE}</h4></th>
	</tr>
	<tr><td width="15%">
	{$MOD.LBL_LDAP_ENABLE}
	</td><td width="35%"><input type='hidden' name="system_ldap_enabled" value="0" ><input name="system_ldap_enabled" value="1" class="checkbox" tabindex='1' type="checkbox" {$system_ldap_enabled_checked} onclick='toggleDisplay_2("ldap_display")'></td><td>&nbsp;</td><td>&nbsp;</td></tr>
	<tr><td colspan='4'>
	<table  cellspacing='0' cellpadding='0' id='ldap_display' style='display:{$ldap_display}' width='100%'>
	<tr>
		<td class="dataLabel" valign='top' nowrap>{$MOD.LBL_LDAP_SERVER_HOSTNAME}</td>{$settings.proxy_host}
		<td align="left" class="dataField" valign='top'><input name="ldap_hostname" size='25' type="text" value="{$settings.ldap_hostname}"></td>
		<td align="left" class="dataField" valign='top'>{$MOD.LBL_LDAP_SERVER_HOSTNAME_DESC}</td>
	</tr>
	<tr>
		<td class="dataLabel" valign='top' nowrap>{$MOD.LBL_LDAP_SERVER_PORT}</td>{$settings.proxy_port}
		<td align="left" class="dataField" valign='top'><input name="ldap_port" size='6' type="text" value="{$settings.ldap_port}"></td>
		<td align="left" class="dataField" valign='top'>{$MOD.LBL_LDAP_SERVER_PORT_DESC}</td>
	</tr>
	<tr>
		<td class="dataLabel" valign='middle' nowrap>{$MOD.LBL_LDAP_BASE_DN}</td>
		<td align="left" class="dataField" valign='middle'><input name="ldap_base_dn" size='35' type="text" value="{$settings.ldap_base_dn}"></td>
		 <td align="left" class="dataField" valign='middle'><em>{$MOD.LBL_LDAP_BASE_DN_DESC}</em></td>
	</tr>
	<tr>
		<td class="dataLabel" valign='top' nowrap>{$MOD.LBL_LDAP_BIND_ATTRIBUTE}</td>
		<td align="left" class="dataField" valign='top'><input name="ldap_bind_attr" size='25' type="text" value="{$settings.ldap_bind_attr}"> </td>
		<td align="left" class="dataField" valign='top'><em>{$MOD.LBL_LDAP_BIND_ATTRIBUTE_DESC}</em></td>
	</tr>

	<tr>
		<td class="dataLabel" valign='middle' nowrap>{$MOD.LBL_LDAP_LOGIN_ATTRIBUTE}</td>
		<td align="left" class="dataField" valign='middle'><input name="ldap_login_attr" size='25' type="text" value="{$settings.ldap_login_attr}"></td>
		 <td align="left" class="dataField" valign='middle'><em>{$MOD.LBL_LDAP_LOGIN_ATTRIBUTE_DESC}</em></td>
	</tr>

	<tr>
		<td class="dataLabel" valign='top'nowrap>{$MOD.LBL_LDAP_ADMIN_USER}</td>
		<td align="left" class="dataField" valign='top'><input name="ldap_admin_user" size='35' type="text" value="{$settings.ldap_admin_user}" autocomplete="off"></td>
		<td align="left" class="dataField" valign='top'><em>{$MOD.LBL_LDAP_ADMIN_USER_DESC}</em></td>
	</tr>
	<tr>
		<td class="dataLabel" valign='middle' nowrap>{$MOD.LBL_LDAP_ADMIN_PASSWORD}</td>
		<td align="left" class="dataField" valign='middle'><input name="ldap_admin_password" size='35' type="password" value="{$settings.ldap_admin_password}" autocomplete="off"> </td>
		<td align="left" class="dataField" valign='top'><em>{$MOD.LBL_LDAP_ADMIN_PASSWORD_DESC}</em></td>
	</tr>


	<tr>
		<td class="dataLabel" valign='top' nowrap>{$MOD.LBL_LDAP_AUTO_CREATE_USERS}</td>
		{if !empty($settings.ldap_auto_create_users)}
			{assign var='ldap_auto_create_users_checked' value='CHECKED'}
		{else}
			{assign var='ldap_auto_create_users_checked' value=''}
		{/if}
		<td align="left" class="dataField" valign='top'><input type='hidden' name='ldap_auto_create_users' value='0'><input name="ldap_auto_create_users" value="1" class="checkbox" type="checkbox" {$ldap_auto_create_users_checked}></td>
		<td align="left" class="dataField" valign='top'> <em>{$MOD.LBL_LDAP_AUTO_CREATE_USERS_DESC}</em></td>
	</tr>
	<tr>
		<td class="dataLabel" valign='middle' nowrap>{$MOD.LBL_LDAP_ENC_KEY}</td>
		<td align="left" class="dataField" valign='middle'><input name="ldap_enc_key" size='35' type="password" value="{$settings.ldap_enc_key}" {$LDAP_ENC_KEY_READONLY}> </td>
		<td align="left" class="dataField" valign='top'><em>{$LDAP_ENC_KEY_DESC}</em></td>
	</tr>

</table>
</td></tr></table></td></tr></table>
<BR>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tabForm">
<tr><td>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr><th align="left" class="dataLabel" colspan="4"><h4 class="dataLabel">{$MOD.LBL_PROXY_TITLE}</h4></th>
	</tr><tr>
	<td width="15%" class="dataLabel" valign='middle'>{$MOD.LBL_PROXY_ON}</td>
		{if !empty($settings.proxy_on)}
		{assign var='proxy_on_checked' value='CHECKED'}
	{else}
		{assign var='proxy_on_checked' value=''}
	{/if}
	<td width="85%" align="left" class="dataField" valign='middle' colspan='3'><input type='hidden' name='proxy_on' value='0'><input name="proxy_on" value="1" class="checkbox" tabindex='1' type="checkbox" {$proxy_on_checked} onclick='toggleDisplay_2("proxy_config_display")'> <em>{$MOD.LBL_PROXY_ON_DESC}</em></td>
	</tr><tr>
	<td colspan="4">
	<div id="proxy_config_display" style='display:{$PROXY_CONFIG_DISPLAY}'>
		<table width="100%" cellpadding="0" cellspacing="0"><tr>
		<td width="15%" class="dataLabel">{$MOD.LBL_PROXY_HOST}<span class="required">{$APP.LBL_REQUIRED_SYMBOL}</span></td>
		<td width="35%" class="dataField"><input type="text" name="proxy_host" size="25"  value="{$settings.proxy_host}" tabindex='1' ></td>
		<td width="15%" class="dataLabel">{$MOD.LBL_PROXY_PORT}<span class="required">{$APP.LBL_REQUIRED_SYMBOL}</span></td>
		<td width="35%" class="dataField"><input type="text" name="proxy_port" size="6"  value="{$settings.proxy_port}" tabindex='1' ></td>
		</tr><tr>
		<td width="15%" class="dataLabel" valign='middle'>{$MOD.LBL_PROXY_AUTH}</td>
	{if !empty($settings.proxy_auth)}
		{assign var='proxy_auth_checked' value='CHECKED'}
	{else}
		{assign var='proxy_auth_checked' value=''}
	{/if}
		<td width="35%" align="left" class="dataField" valign='middle' ><input type='hidden' name='proxy_auth' value='0'><input name="proxy_auth" value="1" class="checkbox" tabindex='1' type="checkbox" {$proxy_auth_checked} onclick='toggleDisplay_2("proxy_auth_display")'> </td>
		</tr></table>

		<div id="proxy_auth_display" style='display:{$PROXY_AUTH_DISPLAY}'>

		<table width="100%" cellpadding="0" cellspacing="0"><tr>
		<td width="15%" class="dataLabel">{$MOD.LBL_PROXY_USERNAME}<span class="required">{$APP.LBL_REQUIRED_SYMBOL}</span></td>

		<td width="35%" class="dataField"><input type="text" name="proxy_username" size="25"  value="{$settings.proxy_username}" tabindex='1' ></td>
		<td width="15%" class="dataLabel">{$MOD.LBL_PROXY_PASSWORD}<span class="required">{$APP.LBL_REQUIRED_SYMBOL}</span></td>
		<td width="35%" class="dataField"><input type="password" name="proxy_password" size="25"  value="{$settings.proxy_password}" tabindex='1' ></td>
		</tr></table>
		</div>
	</div>
  </td></tr></table>
</td></tr></table>
<BR>



























<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tabForm">
<tr><td>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr><th align="left" class="dataLabel" colspan="4"><h4 class="dataLabel">{$MOD.LBL_SKYPEOUT_TITLE}</h4></th>
	</tr><tr>
	<td width="25%" class="dataLabel" valign='middle'>{$MOD.LBL_SKYPEOUT_ON}</td>
	{if !empty($settings.system_skypeout_on)}
		{assign var='system_skypeout_on_checked' value='CHECKED'}
	{else}
		{assign var='system_skypeout_on_checked' value=''}
	{/if}
	<td width="75%" align="left" class="dataField" valign='middle'><input type='hidden' name='system_skypeout_on' value='0'><input name="system_skypeout_on" value="1" class="checkbox" tabindex='1' type="checkbox" {$system_skypeout_on_checked} > <em>{$MOD.LBL_SKYPEOUT_ON_DESC}</em></td>
	</tr><tr>
	<td colspan="4">
	<div id="portal_config">
		<table width="100%" cellpadding="0" cellspacing="0"><tr>
		<td width="15%" class="dataLabel">&nbsp;</td>
		<td width="35%" class="dataField">&nbsp;</td>
		</tr></table>
	</div>
  </td></tr></table>
</td></tr></table>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tabForm">
<tr><td>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr><th align="left" class="dataLabel" colspan="4"><h4 class="dataLabel">{$MOD.LBL_MAILMERGE}</h4></th>
	</tr><tr>
	<td width="25%" class="dataLabel" valign='middle'>{$MOD.LBL_ENABLE_MAILMERGE}</td>
	{if !empty($settings.system_mailmerge_on)}
		{assign var='system_mailmerge_on_checked' value='CHECKED'}
	{else}
		{assign var='system_mailmerge_on_checked' value=''}
	{/if}
	<td width="75%" align="left" class="dataField" valign='middle'><input type='hidden' name='system_mailmerge_on' value='0'><input name="system_mailmerge_on" value="1" class="checkbox" type="checkbox" {$system_mailmerge_on_checked}> <em>{$MOD.LBL_MAILMERGE_DESC}</em></td>
	</tr>
</table>
</td></tr></table>




















<br>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tabForm">
	<tr><th align="left" class="dataLabel" colspan="4"><h4 class="dataLabel">{$MOD.ADVANCED}</h4></th>
	</tr><tr>
<td>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td  class="dataLabel">{$MOD.VERIFY_CLIENT_IP}: </td>
		{if !empty($config.verify_client_ip)}
			{assign var='verify_client_ip_checked' value='CHECKED'}
		{else}
			{assign var='verify_client_ip_checked' value=''}
		{/if}
		<td  class="dataField"><input type='hidden' name='verify_client_ip' value='false'><input name='verify_client_ip'  type="checkbox" value="1" {$verify_client_ip_checked}></td>

		<td  class="dataLabel">{$MOD.LOG_MEMORY_USAGE}: </td>
		{if !empty($config.log_memory_usage)}
			{assign var='log_memory_usage_checked' value='CHECKED'}
		{else}
			{assign var='log_memory_usage_checked' value=''}
		{/if}
		<td  class="dataField"><input type='hidden' name='log_memory_usage' value='false'><input name='log_memory_usage'  type="checkbox" value='true' {$log_memory_usage_checked}></td>

	</tr>
	<tr>
		<td  class="dataLabel">{$MOD.LOG_SLOW_QUERIES}: </td>
		{if !empty($config.dump_slow_queries)}
			{assign var='dump_slow_queries_checked' value='CHECKED'}
		{else}
			{assign var='dump_slow_queries_checked' value=''}
		{/if}
		<td class="dataField"><input type='hidden' name='dump_slow_queries' value='false'><input name='dump_slow_queries'  type="checkbox" value='true' {$dump_slow_queries_checked}></td>

		<td  class="dataLabel">{$MOD.SLOW_QUERY_TIME_MSEC}: </td>
		<td  class="dataField">
			<input type='text' size='5' name='slow_query_time_msec' value='{$config.slow_query_time_msec}'>
		</td>

	</tr>
	<tr>
		<td  class="dataLabel">{$MOD.UPLOAD_MAX_SIZE}: </td>
		<td  class="dataField">
			<input type='text' size='8' name='upload_maxsize' value='{$config.upload_maxsize}'>
		</td>
		<td  class="dataLabel">{$MOD.STACK_TRACE_ERRORS}: </td>
		{if !empty($config.stack_trace_errors)}
			{assign var='stack_trace_errors_checked' value='CHECKED'}
		{else}
			{assign var='stack_trace_errors_checked' value=''}
		{/if}
		<td class="dataField"><input type='hidden' name='stack_trace_errors' value='false'><input name='stack_trace_errors'  type="checkbox" value='true' {$stack_trace_errors_checked}></td>



	</tr>

	<tr>






		<td  class="dataLabel">{$MOD.DEVELOPER_MODE}: </td>
		{if !empty($config.developerMode)}
			{assign var='developerModeChecked' value='CHECKED'}
		{else}
			{assign var='developerModeChecked' value=''}
		{/if}
		<td class="dataField"><input type='hidden' name='developerMode' value='false'><input name='developerMode'  type="checkbox" value='true' {$developerModeChecked}></td>
	</tr>
	<tr>
		<td  class="dataLabel" >{$MOD.LBL_VCAL_PERIOD} {sugar_help text=$MOD.vCAL_HELP}</td>
		<td class="dataField" >
			<input type='text' size='4' name='vcal_time' value='{$config.vcal_time}'>
		</td>
	</tr>
</table>
</td></tr>
</table>
<br />

































<table  width="100%" border="0" cellspacing="0" cellpadding="0" class="tabForm">
<tr><th align="left" class="dataLabel" colspan="4"><h4 class="dataLabel">{$MOD.LBL_LOGGER}</h4></th></tr>
	<tr>
		<td  class="dataLabel" valign='middle'>{$MOD.LBL_LOGGER_FILENAME}</td>
		<td  class="dataField" valign='middle' ><input type='text' name = 'logger_file_name'  value="{$config.logger.file.name}"></td>
		<td  class="dataLabel">{$MOD.LBL_LOGGER_FILE_EXTENSION}</td>
		<td class="dataField"><input name ="logger_file_ext" type="text" size="5" value="{$config.logger.file.ext}"></td>
		<td class="dataLabel">{$MOD.LBL_LOGGER_FILENAME_SUFFIX}</td>
		<td class="dataField"><select name = "logger_file_suffix" selected='{$config.logger.file.suffix}'>{$filename_suffix}</select></td>
	</tr>
	<tr>
		<td class="dataLabel">{$MOD.LBL_LOGGER_MAX_LOG_SIZE} </td>
		<td class="dataField"> <input name="logger_file_maxSize" size="4" value="{$config.logger.file.maxSize}"></td>
		<td class="dataLabel">{$MOD.LBL_LOGGER_DEFAULT_DATE_FORMAT}</td>
		<td  class="dataField"><input name ="logger_file_dateFormat" type="text" value={$config.logger.file.dateFormat}></td>
	</tr>
	<tr>
		<td class="dataLabel">{$MOD.LBL_LOGGER_LOG_LEVEL} </td>
		<td class="dataField"> <select name="logger_level">{$log_levels}</select></td>
		<td class="dataLabel">{$MOD.LBL_LOGGER_MAX_LOGS} </td>
		<td class="dataField"> <input name="logger_file_maxLogs" value="{$config.logger.file.maxLogs}"></td>
	</tr>

</table>
<br/>
</table>


<div style="padding-top: 2px;">
<input title="{$APP.LBL_SAVE_BUTTON_TITLE}" class="button"  type="submit" name="save" value="  {$APP.LBL_SAVE_BUTTON_LABEL}  " />
		&nbsp;<input title="{$MOD.LBL_SAVE_BUTTON_TITLE}"  class="button"  type="submit" name="restore" value="  {$MOD.LBL_RESTORE_BUTTON_LABEL}  " />
		&nbsp;<input title="{$MOD.LBL_CANCEL_BUTTON_TITLE}"  onclick="document.location.href='index.php?module=Administration&action=index'" class="button"  type="button" name="cancel" value="  {$APP.LBL_CANCEL_BUTTON_LABEL}  " />
</div>
{$JAVASCRIPT}
{literal}
<script>
addToValidate('ConfigureSettings', 'system_name', 'varchar', true,'System Name' );
</script>
</form>
<div id='upload_panel' style="display:none">
    <form id="upload_form" name="upload_form" method="POST" action='index.php' enctype="multipart/form-data">
        <input type="file" id="my_file_company" name="file_1" size="20" onchange="uploadCheck(false)"/>
        <img id="loading_img_company" alt="loading..." src="themes/default/images/sqsWait.gif" style="display:none">
    </form>
</div>








<script type='text/javascript' src='include/javascript/ext-2.0/adapter/ext/ext-base.js'></script>
<script type='text/javascript' src='include/javascript/ext-2.0/ext-all.js'></script>
<script type='text/javascript'>
function init_logo(){
    document.getElementById('upload_panel').style.display="inline";
    document.getElementById('upload_panel').style.position="absolute";
    Ext.get('upload_panel').anchorTo(Ext.get('container_upload'));





}
Ext.onReady(function(){
    init_logo();
});
function toggleDisplay_2(div_string){
    toggleDisplay(div_string);
    init_logo();
}
 function uploadCheck(quotes){
    //AJAX call for checking the file size and comparing with php.ini settings.
    var callback = {
        upload:function(r) {
            eval("var file_type = " + r.responseText);
            var forQuotes = file_type['forQuotes'];
            document.getElementById('loading_img_'+forQuotes).style.display="none";
            switch(file_type['data']){
                case 'other':
                    alert(SUGAR.language.get('Configurator','LBL_ALERT_JPG_IMAGE'));
                    document.getElementById('my_file_' + forQuotes).value='';
                    break;
                case 'size':
                    if(forQuotes == 'quotes'){
                        alert(SUGAR.language.get('Configurator','LBL_ALERT_SIZE_RATIO_QUOTES'));
                        document.getElementById('my_file_quotes').value='';
                    }else{
                        alert(SUGAR.language.get('Configurator','LBL_ALERT_SIZE_RATIO'));
                        document.getElementById(forQuotes + "_logo").value=file_type['path'];
                        document.getElementById(forQuotes + "_logo_image").src=file_type['path'];
                    }
                    break;
                case 'file_error':
                    alert(SUGAR.language.get('Configurator','ERR_ALERT_FILE_UPLOAD'));
                    document.getElementById('my_file_' + forQuotes).value='';
                    break;
                //File good
                case 'ok':
                    document.getElementById(forQuotes + "_logo").value=file_type['path'];
                    document.getElementById(forQuotes + "_logo_image").src=file_type['path'];
                    break;
                //error in getimagesize because unsupported type
                default:
                   alert(SUGAR.language.get('Configurator','LBL_ALERT_TYPE_IMAGE'));
                   document.getElementById('my_file_' + forQuotes).value='';
            }
        },
        failure:function(r){
            alert("Ajax failure");
        }
    }
    if(quotes){







    }
    else{
        document.getElementById("company_logo").value='';
        document.getElementById('loading_img_company').style.display="inline";
        var file_name = document.getElementById('my_file_company').value;
        postData = '&module=Configurator&action=UploadFileCheck&to_pdf=1&forQuotes=false';
        YAHOO.util.Connect.setForm(document.getElementById('upload_form'), true,true);
    }
    if(file_name){
        YAHOO.util.Connect.asyncRequest('POST', 'index.php', callback, postData);
    }
}
</script>
{/literal}
