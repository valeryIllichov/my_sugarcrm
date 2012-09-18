
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

<table cellpadding="0" cellspacing="0" border="0" width="450px" class="tabForm">
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
    {if $showMyItemsOnly}
    <tr>
	    <td class='dataLabel'>
            Exceptions
        </td>
        <td class='dataField'>
            <input type='checkbox' {if $exceptions == 'true'}checked{/if} name='exceptions'>
        </td>
    </tr>
    {/if}
    <tr>
    {foreach name=searchIteration from=$searchFields key=name item=params}
        {if $params.label != 'Assigned to ID'}
        <td class='dataLabel' valign='top'>Assigned to ID
                {$params.label}
        </td>
        <td class='dataField' valign='top' style='padding-bottom: 5px'>
            {$params.input}
        </td>
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

</div>
