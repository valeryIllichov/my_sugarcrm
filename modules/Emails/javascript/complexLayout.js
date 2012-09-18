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
/**
  Complex layout init
 */
function complexLayoutInit() {
    e2Layout = function(){
        //var mainPanel;

        return {
            getComposeLayout : function() {
                var idx = SUGAR.email2.composeLayout.currentInstanceId;
                SUGAR.email2.composeLayout[idx] = new Ext.BorderLayout('composeLayout' + idx, {
                    center:{
                        preservePanel : false,
                        animate: false,
                        autoScroll: false,
                        split:true,
                        tabPosition: SUGAR.email2.userPrefs.emailSettings.tabPosition,
                        titlebar: false
                    },
                    east:{
                        preservePanel : false,
                        animate: false,
                        autoScroll: true,
                        autoTab: true,
                        collapsible: true,
                        collapsed: true,
                        split:true,
                        tabPosition: SUGAR.email2.userPrefs.emailSettings.tabPosition,
                        initialSize: 300,
                        titlebar: true,
                        shim: true,
                        iframe: true
                    }
                });
                SUGAR.email2.composeLayout[idx].add('east', new Ext.ContentPanel('divAttachments' + idx, {title: app_strings.LBL_EMAIL_ATTACHMENT, closable: false}));
                SUGAR.email2.composeLayout[idx].add('east', new Ext.ContentPanel('divOptions' + idx, {title: app_strings.LBL_EMAIL_OPTIONS, closable: false}));
                SUGAR.email2.composeLayout[idx].add('center', new Ext.ContentPanel('composeOverFrame' + idx));
                //Convert selects to combo boxes for IE compatibility with panels

                
//                SUGAR.email2.composeLayout[idx].regions.east.expand();
//                SUGAR.email2.composeLayout[idx].regions.east.collapse();
            },
            
            getInnerLayout : function(rows) {
             SUGAR.email2.listViewLayout = new Ext.BorderLayout('listViewLayout', {
                    center: {
                        autoScroll:false, // grid should autoScroll itself
                        initialSize:'250',
                        titlebar:false,
                        split:true
                    },
                    south: {
                        autoScroll:true,
                        collapsible: true,
                        initialSize:'350',
                        titlebar: false,
                        split: true
                    },
                    east: {
                        autoScroll:true,
                        collapsible: false,
                        initialSize:'400',
                        titlebar: false,
                        split: true
                    }
                });
                SUGAR.email2.listViewLayout.beginUpdate();
                if (!SUGAR.email2.e2gridPanel){
                    SUGAR.email2.e2gridPanel = new Ext.GridPanel(SUGAR.email2.grid, {closable: false});
                }
                SUGAR.email2.listViewLayout.add('center', SUGAR.email2.e2gridPanel);
                SUGAR.email2.listViewLayout.add(rows ? 'south' : 'east', new Ext.ContentPanel('_blank', {closable: false}));
                SUGAR.email2.listViewLayout.restoreState();
                SUGAR.email2.listViewLayout.endUpdate(false);
                SUGAR.email2.listViewLayout.getRegion(rows ? 'east' : 'south').hide();
                
                SUGAR.email2.complexLayout.getRegion('center').remove('innerLayout');
                SUGAR.email2.innerLayout = new Ext.BorderLayout('innerLayout', {
                    center: {
                        autoScroll:true,// for popup msgs
                        titlebar:true,
                        autoTab: true,
                        closeOnTab: true,
                        alwaysShowTabs: true,
                        resizeTabs: true,
                        tabPosition: SUGAR.email2.userPrefs.emailSettings.tabPosition
                    }
                });

                SUGAR.email2.innerLayout.beginUpdate();
                var centerPanel = new Ext.NestedLayoutPanel(SUGAR.email2.listViewLayout);
                centerPanel.setTitle('&nbsp;');
                SUGAR.email2.innerLayout.add('center', centerPanel);
                SUGAR.email2.innerLayout.endUpdate();
                // set preview layout pointer
                SUGAR.email2.previewLayout = SUGAR.email2.listViewLayout;
                SUGAR.email2.complexLayout.add('center', new Ext.NestedLayoutPanel(SUGAR.email2.innerLayout));
            },
            
            getInnerLayout2Rows : function() {
                this.getInnerLayout(true);
            },
            getInnerLayout2Columns : function() {
                this.getInnerLayout(false);
            },
            
            init : function(){
                // initialize state manager, we will use cookies
                Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

                SUGAR.email2.complexLayout = new Ext.BorderLayout("container", {
                    hideOnLayout: true,
                    center: {
                        preservePanel : false,
                        animate: false,
                        autoScroll:false,
                        tabPosition: SUGAR.email2.userPrefs.emailSettings.tabPosition,
                        collapsible: false,
                        titlebar: false
                    },
                    west: {
                        animate: false,
                        autoScroll: true,
                        collapsible: true,
                        initialSize: 300,
                        minSize: 100,
                        split:true,
                        tabPosition: SUGAR.email2.userPrefs.emailSettings.tabPosition,
                        useShim:true,
                        titlebar: true
                    },
                    south: {
                        collapsible: false,
                        collapsed: false,
                        initialSize: 25,
                        split:false,
                        titlebar: true
                    }
                });
    
                SUGAR.email2.complexLayout.beginUpdate();
                    SUGAR.email2.userPrefs.emailSettings.layoutStyle == '2rows' ? this.getInnerLayout2Rows() : this.getInnerLayout2Columns();
                    
                    var innerLayout = new Ext.NestedLayoutPanel(SUGAR.email2.innerLayout);
                    var contacts = new Ext.ContentPanel('contactsTab', {
                       title: app_strings.LBL_EMAIL_ADDRESS_BOOK_TITLE_ICON, 
                       autoScroll: true
                    });
                    var searchContentPanel = new Ext.ContentPanel('searchTab', {title: app_strings.LBL_EMAIL_SEARCH});
                    SUGAR.email2.complexLayout.add('center', innerLayout);
                    SUGAR.email2.complexLayout.add('west', new Ext.ContentPanel('frameFolders', {title: app_strings.LBL_EMAIL_FOLDERS}));
                    SUGAR.email2.complexLayout.add('west', searchContentPanel);
                    SUGAR.email2.complexLayout.add('west', contacts);



                    SUGAR.email2.complexLayout.add('south', new Ext.ContentPanel('footerLinks', {title: document.getElementById('footerLinks').innerHTML}));
                    SUGAR.email2.complexLayout.showPanel('frameFolders');
                    SUGAR.email2.complexLayout.restoreState();
                SUGAR.email2.complexLayout.endUpdate();
                
                contacts.addListener('activate', SUGAR.email2.addressBook.getUserContacts);
                searchContentPanel.addListener('activate', SUGAR.email2.search.updateSearchTab);
                
                //IE Fails to redraw when switching panels, this forces its hand.
                if (SUGAR.email2.util.isIe()) {
                    SUGAR.email2.innerLayout.regions.center.on('panelactivated', function(panel) { 
                        SUGAR.email2.innerLayout.regions.center.resizeTo(100);
                    } );
                }
                
                // ear-mark the layout to target dblclick'd emails
                SUGAR.email2.innerLayout.regions.center.addListener("beforeremove", SUGAR.email2.composeLayout.confirmClose);
            }
        }
    }();
    e2Layout.init();


    SUGAR.email2.complexLayout.toggleArea = function(id){
        var area = SUGAR.email2.complexLayout.getRegion(id);
        if(area.isVisible()){
            area.hide();
        } else {
            area.show();
        }
    }

    SUGAR.email2.innerLayout.toggleArea = function(id){
        var area = SUGAR.email2.innerLayout.getRegion(id);
        if(area.isVisible()){
            area.hide();
        } else {
            area.show();
        }
    }
    
    SUGAR.email2.autoSetLayout();
}

var myBufferedListenerObject = new Object();
myBufferedListenerObject.refit = function() {
    if(SUGAR.email2.grid) {
        SUGAR.email2.grid.autoSize();
    }
}
