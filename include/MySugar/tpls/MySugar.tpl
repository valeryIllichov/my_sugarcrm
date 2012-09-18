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
{literal}
<style>
.menu{
	z-index:100;
}

.subDmenu{
	z-index:100;
}
</style>
{/literal}

<!-- begin includes for overlib -->
<script type="text/javascript" src="{sugar_getjspath file='include/javascript/sugar_grp_overlib.js'}"></script>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000"></div>
<!-- end includes for overlib -->

<script type="text/javascript">





var activePage = {$activePage};
var theme = '{$theme}';
current_user_id = '{$current_user}';
jsChartsArray = new Array();
var moduleName = '{$module}';
</script>

<script type="text/javascript" src="{sugar_getjspath file='include/javascript/dashlets.js'}"></script>
<script type="text/javascript" src="{sugar_getjspath file='include/javascript/yui/container.js'}"></script>
<script type="text/javascript" src="{sugar_getjspath file='include/javascript/yui/PanelEffect.js'}"></script>
<script type="text/javascript" src='{sugar_getjspath file='include/javascript/sugar_grp_ytree.js'}'></script>
<script type="text/javascript" src='{sugar_getjspath file='include/JSON.js'}'></script>
<script type='text/javascript' src='{sugar_getjspath file='include/MySugar/javascript/MySugar.js'}'></script>
<link rel='stylesheet' href='{sugar_getjspath file='include/ytree/TreeView/css/folders/tree.css'}'>
<link rel='stylesheet' href='{sugar_getjspath file='include/javascript/yui/assets/container.css'}'>

<script type="text/javascript" src="{sugar_getjspath file='include/javascript/swfobject.js'}"></script>

<link rel="stylesheet" type="text/css" href="{sugar_getjspath file='themes/default/ext/resources/css/ext-all.css'}" />
<script type="text/javascript" language="Javascript" src="{sugar_getjspath file='include/javascript/ext-2.0/adapter/ext/ext-base.js'}"></script>
<script type="text/javascript" language="Javascript" src="{sugar_getjspath file='include/javascript/ext-2.0/ext-all.js'}"></script>





























<div id="pageContainer">
	<div id="pageNum_{$activePage}_div">
<table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top: 5px;">
 	<tr>



	 	<td>
		&nbsp;
		</td>
	
		<td rowspan="3">
				<img src='{sugar_getwebpath file='include/images/blank.gif'}' width='15' height='1' border='0'>
		</td>











		<td align='right'>



			{if !$lock_homepage}<input id="add_dashlets" class="button" type="button" value="{$lblAddDashlets}" onclick="return SUGAR.mySugar.showDashletsDialog();"/>{/if}



	 		<a href='#' onclick="window.open('index.php?module=Administration&action=SupportPortal&view=documentation&version={$sugarVersion}&edition={$sugarFlavor}&lang={$currentLanguage}&help_module={$module}&help_action=index&key={$serverUniqueKey}','helpwin','width=600,height=600,status=0,resizable=1,scrollbars=1,toolbar=0,location=1'); return false" class='utilsLink'>
				<img src='{sugar_getimagepath file='help.gif'}' width='13' height='13' alt='{$lblLnkHelp}' border='0' align='absmiddle'>
				{$lblLnkHelp}
			</a>
		</td>
	</tr>
	<tr>
		{counter assign=hiddenCounter start=0 print=false}
		{foreach from=$columns key=colNum item=data}
		<td valign='top' width={$data.width}>
			<ul class='noBullet' id='col_{$activePage}_{$colNum}'>
				<li id='page_{$activePage}_hidden{$hiddenCounter}b' style='height: 5px' class='noBullet'>&nbsp;&nbsp;&nbsp;</li>
		        {foreach from=$data.dashlets key=id item=dashlet}		
				<li class='noBullet' id='dashlet_{$id}'>
					<div id='dashlet_entire_{$id}'>
						{$dashlet.script}
						{$dashlet.display}
					</div>
				</li>
				{/foreach}
				<li id='page_{$activePage}_hidden{$hiddenCounter}' style='height: 5px' class='noBullet'>&nbsp;&nbsp;&nbsp;</li>
			</ul>
		</td>
		{counter}
		{/foreach}
	</tr>
</table>
	</div>
	
	{foreach from=$divPages key=divPageIndex item=divPageNum}
	<div id="pageNum_{$divPageNum}_div" style="display:none;">
	</div>
	{/foreach}


















	
















	
	<div id="dashletsDialog" style="display:none;">
		<div class="hd" id="dashletsDialogHeader">{$lblAddDashlets}</div>	
		<div class="bd" id="dashletsList">
			<form></form>
		</div>
		<div class="close nonsecure"><a href="#" onClick="javascript:SUGAR.mySugar.closeDashletsDialog();"><img src="{sugar_getwebpath file='include/javascript/yui/assets/close12_1.gif'}" border="0" /></a></div>
	</div>
				
	
</div>

{literal}
<script type="text/javascript">
SUGAR.mySugar.maxCount = 	{/literal}{$maxCount}{literal};
SUGAR.mySugar.homepage_dd = new Array();
SUGAR.mySugar.init = function () {
	j = 0;
	
	{/literal}
	dashletIds = {$dashletIds};
	
	{if !$lock_homepage}
	{literal}
	for(i in dashletIds) {
		SUGAR.mySugar.homepage_dd[j] = new ygDDList('dashlet_' + dashletIds[i]);
		SUGAR.mySugar.homepage_dd[j].setHandleElId('dashlet_header_' + dashletIds[i]);
		SUGAR.mySugar.homepage_dd[j].onMouseDown = SUGAR.mySugar.onDrag;  
		SUGAR.mySugar.homepage_dd[j].afterEndDrag = SUGAR.mySugar.onDrop;
		j++;
	}
	for(var wp = 0; wp <= {/literal}{$hiddenCounter}{literal}; wp++) {
	    SUGAR.mySugar.homepage_dd[j++] = new ygDDListBoundary('page_'+activePage+'_hidden' + wp);
	}

	YAHOO.util.DDM.mode = 1;
	{/literal}
	{/if}
	{literal}
	SUGAR.mySugar.renderDashletsDialog();











	SUGAR.mySugar.loadSugarCharts();
}

</script>
{/literal}

<script type="text/javascript">
	YAHOO.util.Event.addListener(window, 'load', SUGAR.mySugar.init); 
</script>
