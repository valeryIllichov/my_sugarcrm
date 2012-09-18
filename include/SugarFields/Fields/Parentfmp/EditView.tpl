<select name='parent_type' tabindex="{{$tabindex}}" id='parent_type' title='{{$vardef.help}}' onchange='document.{$form_name}.{{sugarvar key='name'}}.value="";document.{$form_name}.parent_id.value=""; changeQS(); checkParentType(document.{$form_name}.parent_type.value, document.{$form_name}.btn_{{sugarvar key='name'}});'>
{html_options options={{sugarvar key='options' string=true}} selected=$fields.parent_type.value}
</select> 
{{if $displayParams.split}}
<br>
{{/if}}
{if empty({{sugarvar key='options' string=true}}[$fields.parent_type.value])}
	{assign var="keepParent" value = 0}
{else}
	{assign var="keepParent value = 1}
{/if}
<input type="text" name="{{sugarvar key='name'}}_custno" id="{{sugarvar key='name'}}_custno" class="sqsEnabled" size="6" value="{fmp_activitycustno_value parent_type=$fields.parent_type.value parent_id=$fields.parent_id.value}" autocomplete="off" tabindex="{{$tabindex}}">
<input type="text" name="{{sugarvar key='name'}}" id="{{sugarvar key='name'}}" class="sqsEnabled" tabindex="{{$tabindex}}" size="{{$displayParams.size}}" {if $keepParent}value="{{sugarvar key='value'}}"{/if} autocomplete="off">
<input type="hidden" name="{{sugarvar memberName='vardef.id_name' key='name'}}" id="{{sugarvar memberName='vardef.id_name' key='name'}}"  {if $keepParent}value="{{sugarvar memberName='vardef.id_name' key='value'}}"{/if}>
<input type="button" name="btn_{{sugarvar key='name'}}" tabindex="{{$tabindex}}" title="{$APP.LBL_SELECT_BUTTON_TITLE}" accessKey="{$APP.LBL_SELECT_BUTTON_KEY}" class="button" value="{$APP.LBL_SELECT_BUTTON_LABEL}" onclick='open_popup(document.{$form_name}.parent_type.value, 600, 400, "", true, false, {{$displayParams.popupData}}, "single", true);'>
{{if empty($displayParams.selectOnly)}}
<input type="button" name="btn_clr_{{sugarvar key='name'}}" tabindex="{{$tabindex}}" title="{$APP.LBL_CLEAR_BUTTON_TITLE}" accessKey="{$APP.LBL_CLEAR_BUTTON_KEY}" class="button" onclick="this.form.{{sugarvar key='name'}}.value = ''; this.form.{{sugarvar memberName='vardef.id_name' key='name'}}.value = '';" value="{$APP.LBL_CLEAR_BUTTON_LABEL}">
{{/if}}
{literal}
<script type="text/javascript">
function changeQS() {
{/literal}
	new_module = document.{$form_name}.parent_type.value;
{literal}

	if(typeof(disabledModules[new_module]) != 'undefined') {
		sqs_objects['parent_name']['disable'] = true;
		document.getElementById('parent_name').readOnly = true;
	}
	else {
		sqs_objects['parent_name']['disable'] = false;
		document.getElementById('parent_name').readOnly = false;
	}	
	sqs_objects['parent_name']['modules'] = new Array(new_module);
    enableQS(false);
}
{/literal}
</script>
{literal}
{{$displayParams.disabled_parent_types}}
{/literal}
