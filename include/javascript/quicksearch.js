/**
 * Javascript file for Sugar
 *
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
function enableQS(noReload){Ext.onReady(function(){var qsFields=Ext.query('.sqsEnabled');for(var qsField in qsFields){var loaded=false;if(isInteger(qsField)&&(qsFields[qsField].id&&!document.getElementById(qsFields[qsField].id).readOnly)&&typeof sqs_objects!='undefined'&&sqs_objects[qsFields[qsField].id]&&sqs_objects[qsFields[qsField].id]['disable']!=true){if(typeof Ext.getCmp('combobox_'+qsFields[qsField].id)!='undefined'){if(noReload==true){loaded=true;}else if(typeof QSFieldsArray[qsFields[qsField].id]!='undefined'){Ext.getCmp('combobox_'+qsFields[qsField].id).destroy();var parent=QSFieldsArray[qsFields[qsField].id][0];if(typeof QSFieldsArray[qsFields[qsField].id][1]!='undefined'){var nextSib=QSFieldsArray[qsFields[qsField].id][1];parent.insertBefore(QSFieldsArray[qsFields[qsField].id][2],nextSib);}
else{parent.appendchild(QSFieldsArray[qsFields[qsField].id][2]);}}}
if(!loaded){if(typeof QSFieldsArray[qsFields[qsField].id]=='undefined'){var Arr=new Array(qsFields[qsField].parentNode,qsFields[qsField].nextSibling,qsFields[qsField]);QSFieldsArray[qsFields[qsField].id]=Arr;}
var sqs=sqs_objects[qsFields[qsField].id];var display_field=sqs.field_list[0];var ds=new Ext.data.Store({storeId:"store_"+qsFields[qsField].id,proxy:new Ext.data.HttpProxy({url:'index.php'}),remoteSort:true,reader:new Ext.data.JsonReader({root:'fields',totalProperty:'totalCount',id:'id'},[{name:display_field},]),baseParams:{to_pdf:'true',module:'Home',action:'quicksearchQuery',data:Ext.util.JSON.encode(sqs)}});var search=new Ext.form.ComboBox({id:"combobox_"+qsFields[qsField].id,store:ds,queryDelay:700,maxHeight:100,minListWidth:120,displayField:display_field,fieldClass:'',listClsClass:typeof(Ext.version)!='undefined'?'x-sqs-list':'x-combo-list',focusClass:'',disabledClass:'',emptyClass:'',invalidClass:'',selectedClass:typeof(Ext.version)!='undefined'?'x-sqs-selected':'x-combo-list',typeAhead:true,loadingText:SUGAR.language.get('app_strings','LBL_SEARCHING'),valueNotFoundText:sqs.no_match_text,hideTrigger:true,confirmed:false,applyTo:typeof(Ext.version)!='undefined'?qsFields[qsField].id:Ext.form.ComboBox.prototype.applyTo,minChars:1,listeners:{select:function(el,type){Ext.EventObject.preventDefault();Ext.EventObject.stopPropagation();if(sqs_objects[el.el.id].populate_list.indexOf('account_id')!=-1){var label_str='';var label_data_str='';var current_label_data_str='';for(var field in type.json){for(var key in sqs_objects[el.el.id].field_list){if(field==sqs_objects[el.el.id].field_list[key]&&document.getElementById(sqs_objects[el.el.id].populate_list[key])){if(!sqs_objects[el.el.id].populate_list[key].match(/account/)){var data_label=document.getElementById(sqs_objects[el.el.id].populate_list[key]+'_label').innerHTML.replace(/\n/gi,'');label_str+=data_label+' \n';label_data_str+=data_label+' '+type.json[field]+'\n';current_label_data_str+=data_label+' '+document.getElementById(sqs_objects[el.el.id].populate_list[key]).value+'\n'}}}}
if(label_data_str!=label_str&&current_label_data_str!=label_str){if(confirm(SUGAR.language.get('app_strings','NTC_OVERWRITE_ADDRESS_PHONE_CONFIRM')+'\n\n'+label_data_str))
{setFields(type,el,/\S/);}else{setFields(type,el,/account/);}}else if(label_data_str!=label_str&&current_label_data_str==label_str){setFields(type,el,/\S/);}else if(label_data_str==label_str){setFields(type,el,/account/);}
el.confirmed=true;}else{setFields(type,el,/\S/);}
if(typeof(sqs_objects[el.el.id]['post_onblur_function'])!='undefined'){collection_extended=new Array();for(var field in type.json){for(var key in sqs_objects[el.el.id].field_list){if(field==sqs_objects[el.el.id].field_list[key]){collection_extended[sqs_objects[el.el.id].field_list[key]]=type.json[field];}}}
eval(sqs_objects[el.el.id]['post_onblur_function']+'(collection_extended, el.el.id)');}},autofill:function(el,ev){el.lastQuery="";el.doQuery(el.getRawValue());el.store.on("load",function(){if(el.confirmed){el.confirmed=false;}
else if(el.store.data.items!='undefined'&&el.store.data.items[0]){el.setRawValue(el.store.data.items[0].json[this.displayField]);if(sqs_objects[el.el.id].populate_list.indexOf('account_id')!=-1){var label_str='';var label_data_str='';var current_label_data_str='';for(var i=0;i<el.store.data.length;i++){if(el.store.data.items[i].json[el.displayField]==el.getValue()){for(var field in el.store.data.items[i].json){for(var key in sqs_objects[el.el.id].field_list){if(field==sqs_objects[el.el.id].field_list[key]&&!sqs_objects[el.el.id].populate_list[key].match(/account/)&&document.getElementById(sqs_objects[el.el.id].populate_list[key])){var data_label=document.getElementById(sqs_objects[el.el.id].populate_list[key]+'_label').innerHTML.replace(/\n/gi,'');label_str+=data_label+' \n';label_data_str+=data_label+' '+el.store.data.items[i].json[field]+'\n';current_label_data_str+=data_label+' '+document.getElementById(sqs_objects[el.el.id].populate_list[key]).value+'\n';}}}
break;}}
if(label_data_str!=label_str&&current_label_data_str!=label_str){if(confirm(SUGAR.language.get('app_strings','NTC_OVERWRITE_ADDRESS_PHONE_CONFIRM')+'\n\n'+label_data_str))
{setAll(el,this,/\S/);}else{setAll(el,this,/account/);}
el.confirmed=true;}else if(label_data_str!=label_str&&current_label_data_str==label_str){setAll(el,this,/\S/);}else if(label_data_str==label_str){setAll(el,this,/account/);}}else{setAll(el,this,/\S/);}}
else{if(sqs_objects[el.el.id].populate_list.indexOf('account_id')!=-1){var selected=clearFields(sqs_objects[el.el.id],/account/);}else{var selected=clearFields(sqs_objects[el.el.id],/\S/);}}
el.confirmed=false;},this,{single:true});},blur:function(el){var selected=false;if(sqs_objects[el.el.id].populate_list.indexOf('account_id')!=-1){setAll(el,this,/account/);if(el.getRawValue()==""){selected=clearFields(sqs_objects[el.el.id],/account/);}}else{selected=setAll(el,this,/\S/);if(el.getRawValue()==""){selected=clearFields(sqs_objects[el.el.id],/\S/);}}
if(!selected){el.fireEvent("autofill",el);}}}});if(typeof search.applyTo!='undefined'){search.applyTo(qsFields[qsField].id);}
search.wrap.applyStyles('display:inline');qsFields[qsField].className=qsFields[qsField].className.replace('x-form-text','');if(Ext.isMac&&Ext.isGecko){document.getElementById(qsFields[qsField].id).addEventListener('keypress',preventDef,false);}
if((qsFields[qsField].form&&typeof(qsFields[qsField].form)=='object'&&qsFields[qsField].form.name=='search_form')||(qsFields[qsField].className.match('sqsNoAutofill')!=null)){search.events.autofill.listeners[0].fireFn=function(){};}}}}});}
function setAll(el,e,filter){var selected=false;for(var i=0;i<el.store.data.length;i++){if(el.store.data.items[i].json[e.displayField]==el.getValue()){for(var field in el.store.data.items[i].json){for(var key in sqs_objects[el.el.id].field_list){if(field==sqs_objects[el.el.id].field_list[key]&&document.getElementById(sqs_objects[el.el.id].populate_list[key])&&sqs_objects[el.el.id].populate_list[key].match(filter)){document.getElementById(sqs_objects[el.el.id].populate_list[key]).value=el.store.data.items[i].json[field];}}}
selected=true;break;}}
return selected;}
function setFields(type,el,filter){for(var field in type.json){for(var key in sqs_objects[el.el.id].field_list){if(field==sqs_objects[el.el.id].field_list[key]&&document.getElementById(sqs_objects[el.el.id].populate_list[key])&&sqs_objects[el.el.id].populate_list[key].match(filter)){document.getElementById(sqs_objects[el.el.id].populate_list[key]).value=type.json[field];}}}}
function clearFields(sqs_object,filter){for(var key in sqs_object.field_list){if(isInteger(key)&&sqs_object.populate_list[key]&&sqs_object.populate_list[key].match(filter))
document.getElementById(sqs_object.populate_list[key]).value='';}
return true;}
function preventDef(event){if(event.keyCode=='13'){event.preventDefault();}}
function registerSingleSmartInputListener(input){if((c=input.className)&&(c.indexOf("sqsEnabled")!=-1)){if(typeof Ext=='object'){enableQS(true);}}}
QSFieldsArray=new Array();if(typeof Ext=='object'){enableQS(true);}
