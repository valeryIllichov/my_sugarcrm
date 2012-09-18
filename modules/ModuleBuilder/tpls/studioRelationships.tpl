{*
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
*}
{if $view_module != 'KBDocuments'}
<input type='button' name='addrelbtn' value='{$mod_strings.LBL_BTN_ADD_RELATIONSHIP}'
	class='button' onclick='ModuleBuilder.moduleLoadRelationship2("");' style="margin-bottom:5px;">
{/if}
<div id='relGrid'></div>
{if $empty}{sugar_translate label='LBL_NO_RELS' module='ModuleBuilder'}</h3>{/if}
{if $studio}{sugar_translate label='LBL_CUSTOM_RELATIONSHIPS' module='ModuleBuilder'}</h3>{/if}
<script>
var relationships = {ldelim}"relationships":{$relationships}{rdelim};
{literal}
var relStore = new Ext.data.Store({
    data: relationships,
    reader: new Ext.data.JsonReader({
        root:"relationships",
        id: "id"
        }, Ext.data.Record.create([
            {name:'name', mapping:'name'},
            {name:'relationship_name', mapping:'relationship_name'},
            {name:'lhs_module'}, {name:'rhs_module'},
            {name:'relationship_type'}, {name:'rsub'}, {name:'lsub'}]))
});

var grid = new Ext.grid.GridPanel({
    store: relStore,
     columns: [
        {id:'id', header: SUGAR.language.get('ModuleBuilder','LBL_REL_NAME'), width: 200, sortable: true, dataIndex: 'name'},
        {header: SUGAR.language.get('ModuleBuilder','LBL_LHS_MODULE'), width: 120, sortable: true, dataIndex: 'lhs_module'},
        {header: SUGAR.language.get('ModuleBuilder','LBL_REL_TYPE'), width: 120, sortable: true, dataIndex: 'relationship_type'},
        {header: SUGAR.language.get('ModuleBuilder','LBL_RHS_MODULE'), width: 120, sortable: true, dataIndex: 'rhs_module'}
    ],
    renderTo:'relGrid',
    sm: new Ext.grid.RowSelectionModel({singleSelect:true}),
	width:Ext.isIE ? Ext.get(Ext.getDom('relGrid').parentNode).getWidth() - 25 : "100%",
    autoHeight: true
});
grid.on('rowclick', function(grid, i){
	var rel = grid.getStore().getAt(i);
	ModuleBuilder.tabPanel.remove("relEditor");
	ModuleBuilder.moduleLoadRelationship2(rel.data.relationship_name);
});

{/literal}
ModuleBuilder.module = '{$view_module}';
ModuleBuilder.MBpackage = '{$view_package}';
ModuleBuilder.helpRegisterByID('relGrid');
{if $fromModuleBuilder}
ModuleBuilder.helpSetup('relationshipsHelp','default');
{else}
ModuleBuilder.helpSetup('studioWizard','relationshipsHelp');
{/if}
</script>
