
<table width="100%" cellspacing="0" cellpadding="0" border="0" style="border-top: 0px none; margin-bottom: 4px" class="tabForm">
<tr>
{{foreach name=colIteration from=$formData key=col item=colData}}
	






	{counter assign=index}
	{math equation="left % right"
   		  left=$index
          right=$templateMeta.maxColumns
          assign=modVal
    }
	{if ($index % $templateMeta.maxColumns == 1 && $index != 1)}
		</tr><tr>
	{/if}
	
	<td class="dataLabel" nowrap="nowrap" width='{{$templateMeta.widths.label}}%' >
	{{if isset($colData.field.label)}}	
		{sugar_translate label='{{$colData.field.label}}' module='{{$module}}'}
    {{elseif isset($fields[$colData.field.name])}}
		{sugar_translate label='{{$fields[$colData.field.name].vname}}' module='{{$module}}'}
	{{/if}}
	</td>
	<td class="dataField" nowrap="nowrap" width='{{$templateMeta.widths.field}}%'>
	{{if $fields[$colData.field.name]}}
		{{sugar_field parentFieldArray='fields' vardef=$fields[$colData.field.name] displayType='searchView' displayParams=$colData.field.displayParams typeOverride=$colData.field.type formName=$form_name}}
   	{{/if}}
   	</td>





{{/foreach}}
	</tr>
<tr>
	<td colspan='20'>
		&nbsp;
	</td>
</tr>	
{if $DISPLAY_SAVED_SEARCH}
<tr>
	<td colspan='6'>
		<a class='listViewTdLinkS1' onhover href='javascript:toggleInlineSearch()'><img src='{$IMG_PATH}advanced_search.gif' id='up_down_img' border=0>{$APP.LNK_SAVED_VIEWS}</a><br>
		<input type='hidden' id='showSSDIV' name='showSSDIV' value='{$SHOWSSDIV}'><p>
		<div style='{$DISPLAYSS}' id='inlineSavedSearch' >
			{$SAVED_SEARCH}
		</div>
	</td>
</tr>
{/if}
{if $DISPLAY_SEARCH_HELP}
<tr>
	<td colspan='5'>&nbsp;</td>
	<td align=right><img  border='0' src='{$IMG_PATH}help.gif' onmouseover="return overlib(SUGAR.language.get('app_strings', 'LBL_SEARCH_HELP_TEXT'), STICKY, MOUSEOFF,1000,WIDTH, 700, LEFT,CAPTION,'<div style=\'float:left\'>'+SUGAR.language.get('app_strings', 'LBL_SEARCH_HELP_TITLE')+'</div>', CLOSETEXT, '<div style=\'float: right\'><img border=0 style=\'margin-left:2px; margin-right: 2px;\' src={$IMG_PATH}close.gif></div>',CLOSETITLE, SUGAR.language.get('app_strings', 'LBL_SEARCH_HELP_CLOSE_TOOLTIP'), CLOSECLICK,FGCLASS, 'olFgClass', CGCLASS, 'olCgClass', BGCLASS, 'olBgClass', TEXTFONTCLASS, 'olFontClass', CAPTIONFONTCLASS, 'olCapFontClass');" ></td>
</tr>
{/if}

</table>

<script>
{literal}
	if(typeof(loadSSL_Scripts)=='function'){
		loadSSL_Scripts();
	}
{/literal}	
</script>
