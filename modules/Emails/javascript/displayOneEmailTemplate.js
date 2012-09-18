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
SUGAR.email2.templates['displayOneEmail'] = '<div>' +
'<table cellpadding="0" cellspacing="0" border="0" width="100%" style="height: 100%;"><tr><td>' +
'<div id="quickCreate{meta.panelId}"></div>' +
'<table cellpadding="0" cellspacing="0" border="0" width="100%">' +
'	<tr>' +
'		<td valign="top" colspan="2">' +
'			<table cellpadding="4" cellspacing="0" border="0" width="100%">' +
'				<td class="listViewThS1" width="1%" NOWRAP>' +
'					<button type="button" class="button" onclick="SUGAR.email2.composeLayout.c0_replyForwardEmail(\'{meta.ieId}\', \'{meta.uid}\', \'{meta.mbox}\', \'reply\');"><img src="themes/default/images/icon_email_reply.gif" align="absmiddle" border="0"> {app_strings.LBL_EMAIL_REPLY}</button>' +
'					<button type="button" class="button" onclick="SUGAR.email2.composeLayout.c0_replyForwardEmail(\'{meta.ieId}\', \'{meta.uid}\', \'{meta.mbox}\', \'replyAll\');"><img src="themes/default/images/icon_email_replyall.gif" align="absmiddle" border="0"> {app_strings.LBL_EMAIL_REPLY_ALL}</button>' +
'					<button type="button" class="button" onclick="SUGAR.email2.composeLayout.c0_replyForwardEmail(\'{meta.ieId}\', \'{meta.uid}\', \'{meta.mbox}\', \'forward\');"><img src="themes/default/images/icon_email_forward.gif" align="absmiddle" border="0"> {app_strings.LBL_EMAIL_FORWARD}</button>' +
'					<button type="button" class="button" onclick="SUGAR.email2.detailView.emailDeleteSingle(\'{meta.ieId}\', \'{meta.uid}\', \'{meta.mbox}\');"><img src="themes/default/images/icon_email_delete.gif" align="absmiddle" border="0"> {app_strings.LBL_EMAIL_DELETE}</button>' +
'					<button type="button" class="button" onclick="SUGAR.email2.detailView.viewPrintable(\'{meta.ieId}\', \'{meta.uid}\', \'{meta.mbox}\');"><img src="themes/default/images/Print_Email.gif" align="absmiddle" border="0"> {app_strings.LBL_EMAIL_PRINT}</button>' +
'					<span id="viewMenuSpan{meta.panelId}"><button type="button" class="button" onclick="SUGAR.email2.detailView.showViewMenu(\'{meta.ieId}\', \'{meta.uid}\', \'{meta.mbox}\');"><img src="themes/default/images/icon_email_view.gif" align="absmiddle" border="0"> {app_strings.LBL_EMAIL_VIEW} <img src="themes/default/images/more.gif" align="absmiddle" border="0"></button></span>' +
'					&nbsp;' +
'				<td class="listViewThS1" width="1%" NOWRAP>' +
'					<div id="archiveEmail{idx}">' +
'						<button type="button" class="button" onclick="SUGAR.email2.detailView.importEmail(\'{meta.ieId}\', \'{meta.uid}\', \'{meta.mbox}\');"><img src="themes/default/images/icon_email_archive.gif" align="absmiddle" border="0"> {app_strings.LBL_EMAIL_IMPORT_EMAIL}</button>' +
'					</div>' +
'				</td>' +
'				<td class="listViewThS1" width="1%" NOWRAP>' +
'					<span id="quickCreateSpan{meta.panelId}"><button type="button" class="button" onclick="SUGAR.email2.detailView.showQuickCreate(\'{meta.ieId}\', \'{meta.uid}\', \'{meta.mbox}\');"><img src="themes/default/images/icon_email_create.gif" align="absmiddle" border="0"> {app_strings.LBL_EMAIL_QUICK_CREATE} <img src="themes/default/images/more.gif" align="absmiddle" border="0"></button></span>' +
'				</td>' +
'				<td class="listViewThS1" width="1%" NOWRAP>' +
'					<div id="showDeialViewForEmail{meta.panelId}">' +
'						<button type="button" class="button" onclick="SUGAR.email2.contextMenus.showEmailDetailViewInPopup(\'{meta.ieId}\', \'{meta.uid}\', \'{meta.mbox}\');"><img src="themes/default/images/icon_email_relate.gif" align="absmiddle" border="0"> {app_strings.LBL_EMAIL_VIEW_RELATIONSHIPS}</button>' +
'					</div>' +
'				</td>' +
'				<td class="listViewThS1" width="97%" NOWRAP>' +
'				</td>' +
'			</table>' +
'		</td>' +
'	</tr>' +
'</table>' +
'</div></td></tr>' +
'<tr style="height:100%"><td>' +
'<table cellpadding="0" cellspacing="0" border="0" width="100%" style="height: 100%;">' +
'	<tr>' +
'		<td>' +
'			<table cellpadding="0" cellspacing="0" border="0" width="100%">' +
'				<tr>' +
'					<td NOWRAP valign="top" width="1%" class="displayEmailLabel">' +
'						{app_strings.LBL_EMAIL_FROM}:' +
'					</td>' +
'					<td width="99%" class="displayEmailValue">' +
'						{email.from_addr}' +
'					</td>' +
'				</tr>' +
'				<tr>' +
'					<td NOWRAP valign="top" class="displayEmailLabel">' +
'						{app_strings.LBL_EMAIL_SUBJECT}:' +
'					</td>' +
'					<td NOWRAP valign="top" class="displayEmailValue">' +
'						<b>{email.name}</b>' +
'					</td>' +
'				</tr>' +
'				<tr>' +
'					<td NOWRAP valign="top" class="displayEmailLabel">' +
'						{app_strings.LBL_EMAIL_DATE_SENT_BY_SENDER}:' +
'					</td>' +
'					<td class="displayEmailValue">' +
'						{email.date_start} {email.time_start}' +
'					</td>' +
'				</tr>' +
'				<tr>' +
'					<td NOWRAP valign="top" class="displayEmailLabel">' +
'						{app_strings.LBL_EMAIL_TO}:' +
'					</td>' +
'					<td class="displayEmailValue">' +
'						{email.toaddrs}' +
'					</td>' +
'				</tr>' +
'				{meta.cc}' +
'				{email.attachments}' +
'			</table>' +
'		</td>' +
'	</tr>' +
'	<tr>' +
'		<td style="height:100%">' +
'			<table cellpadding="0" cellspacing="0" border="0" style="width:100%;height:100%">' +
'				<tr>' +
'					<td style="border-top: 1px solid #333;">' +
'						<div style="height:100%">' +
'                           <iframe id="displayEmailFrame{idx}" src="modules/Emails/templates/_blank.html" width="100%" height="100%" frameborder="0"></iframe>' +
//'                           {email.description}' +
'						</div>' +
'					</td>' +
'				</tr>' +
'			</table>' +
'		</td>' +
'	</tr>' +
'</table>' +
'</td></tr></table>';
