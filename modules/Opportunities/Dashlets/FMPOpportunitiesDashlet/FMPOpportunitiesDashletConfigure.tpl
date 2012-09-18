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


<div>
<form action='index.php' id='configure_{$id}' method='post' onSubmit='SUGAR.mySugar.setChooser(); return SUGAR.dashlets.postForm("configure_{$id}", SUGAR.mySugar.uncoverPage);'>
<input type='hidden' name='id' value='{$id}'>
<input type='hidden' name='module' value='Home'>
<input type='hidden' name='action' value='ConfigureDashlet'>
<input type='hidden' name='configure' value='true'>
<input type='hidden' name='to_pdf' value='true'>
<input type='hidden' id='displayColumnsDef' name='displayColumnsDef' value=''>
<input type='hidden' id='hideTabsDef' name='hideTabsDef' value=''>
<input type='hidden' id='dashletType' name='dashletType' value='' />

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="tabForm">
	<tr>
        <td class='dataLabel' colspan='4' align='left'>
        	<h2>{$strings.general}</h2>
        </td>
    </tr>
    <tr>
	    <td class='dataLabel'>
		    {$strings.title}
        </td>
        <td class='dataField' colspan='3'>
            <input type='text' name='dashletTitle' value='{$dashletTitle}'>
        </td>
	</tr>
    <tr>
	    <td class='dataLabel'>
		    {$strings.displayRows}
        </td>
        <td class='dataField' colspan='3'>
            <select name='displayRows'>
				{html_options values=$displayRowOptions output=$displayRowOptions selected=$displayRowSelect}
           	</select>
        </td>
    </tr>
    <tr>
        <td colspan='4' align='center'>
        	<table border='0' cellpadding='0' cellspacing='0'>
        	<tr><td>
			    {$columnChooser}
		    </td>
		    </tr></table>
	    </td>    
	</tr>
	<tr>
        <td class='dataLabel' colspan='4' align='left'>
	        <br>
        	<h2>{$strings.filters}</h2>
        </td>
    </tr>
    <tr>
        <td valign='top' class='dataLabel'>Cls Date From</td>
        <td  valign='top' class='dataField'>
                <input onblur="parseDate(this, '{$cal_dateformat}');" class="text" name="fmpo_date_start" size='12' maxlength='10' id='fmpo_date_start' value='{$fmpo_date_start}'>
                <img src="themes/default/images/jscalendar.gif" alt="{$LBL_ENTER_DATE}" id="fmpo_date_start_trigger" align="absmiddle">
        </td>
        <td valign='top' class='dataLabel'>Cls Date To  </td>
        <td valign='top' class='dataField'>
            <input onblur="parseDate(this, '{$cal_dateformat}');" class="text" name="fmpo_date_end" size='12' maxlength='10' id='fmpo_date_end' value='{$fmpo_date_end}'>
            <img src="themes/default/images/jscalendar.gif" alt="{$LBL_ENTER_DATE}" id="fmpo_date_end_trigger" align="absmiddle">
        </td>
    </tr>
    {if $showMyItemsOnly}
    <tr>
	    <td class='dataLabel'>
            {$strings.myItems}
        </td>
        <td class='dataField'>
            <input type='checkbox' {if $myItemsOnly == 'true'}checked{/if} name='myItemsOnly' value='true'>
        </td>
    </tr>
    {/if}
    <tr>
    {foreach name=searchIteration from=$searchFields key=name item=params}
        {if $params.label != 'Assigned to ID' and $params.label != 'Company:' }
        <td class='dataLabel' valign='top'>Assigned to ID
                {$params.label}
        </td>
        <td class='dataField' valign='top' style='padding-bottom: 5px'>
            {$params.input}
        </td>
        {/if}
        {if $params.label == 'Company:' }
        <td class='dataLabel' valign='top'>{$params.label}</td>
        <td class='dataField' valign='top' style='padding-bottom: 5px'>{$params.input}</td>
        {/if}
        {if ($smarty.foreach.searchIteration.iteration is even) and $smarty.foreach.searchIteration.iteration != $smarty.foreach.searchIteration.last}     
        </tr><tr>   
        {/if}
        
    {/foreach}
    </tr>
    
    <tr>
	    <td colspan='4' align='right'>
	        <input type='submit' class='button' value='{$strings.save}'>
	    </td>    
	</tr>
</table>
</form>
{literal}
<script type="text/javascript">
Calendar.setup ({
    inputField : "fmpo_date_start", ifFormat : "{/literal}{$cal_dateformat}{literal}", showsTime : false, button : "fmpo_date_start_trigger", singleClick : true, step : 1
});
Calendar.setup ({
    inputField : "fmpo_date_end", ifFormat : "{/literal}{$cal_dateformat}{literal}", showsTime : false, button : "fmpo_date_end_trigger", singleClick : true, step : 1
});
{/literal}
</script>
</div>
