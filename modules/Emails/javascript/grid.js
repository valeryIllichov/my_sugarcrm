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
function gridInit() {
	if(SUGAR.email2.grid) {
		SUGAR.email2.grid.destroy();
	}
	
	e2Grid = {
		init : function() {
			// changes "F" to an icon
			function flaggedIcon(value) {
				if(value != "") {
					return "<span style='color: #f00; font-weight:bold;'>!</a>";
				}
			}
			// changes "A" to replied icon
			function repliedIcon(value) {
				if(value != "") {
					return "<img src='themes/Sugar/images/export.gif' class='image' border='0' width='10' align='absmiddle'>";
				}
			}
			// example of custom renderer function
			function humanReadableDate(value) {
				/*var d = new Date();
				var eDate = getDateObject(value);
				var emailDate = new String(cal_date_format);
				var today = new String(cal_date_format);
				var yesterday = new String(cal_date_format);
	
				emailDate = emailDate.replace('%Y', eDate.getFullYear());
				emailDate = emailDate.replace('%m', strpad(eDate.getMonth() + 1));
				emailDate = emailDate.replace('%d', eDate.getDate());
	
				today = today.replace('%Y', d.getFullYear());
				today = today.replace('%m', strpad(d.getMonth() + 1));
				today = today.replace('%d', d.getDate());
	
				yesterday = yesterday.replace('%Y', d.getFullYear());
				yesterday = yesterday.replace('%m', strpad(d.getMonth() + 1));
				yesterday = yesterday.replace('%d', d.getDate() - 1);
				
				if(today == emailDate) {
					value = app_strings.LBL_EMAIL_DATE_TODAY;
				} else if(yesterday == emailDate) {
					value = app_strings.LBL_EMAIL_DATE_YESTERDAY;
				}*/
				
				return value;
			}
	
			var colModel = new Ext.grid.DefaultColumnModel(
				[
					{
						header: "&nbsp;", 
						width: 15, 
						sortable: true, 
						fixed: true,
						resizeable: false,
						renderer: flaggedIcon,
						dataIndex: 'flagged'
					}, 
					{
						header: "&nbsp;", 
						width: 15, 
						sortable: true, 
						fixed: true,
						resizeable: false,
						renderer: repliedIcon,
						dataIndex: 'status'
					},
					{
						header: app_strings.LBL_EMAIL_FROM, 
						width: 125,
						sortable: true,
						dataIndex: 'from'
					}, 
					{
						header: app_strings.LBL_EMAIL_SUBJECT,
						width: 250, 
						sortable: true,
						dataIndex: 'subject'
					}, 
					{
						header: app_strings.LBL_EMAIL_DATE_SENT_BY_SENDER,
						width: 100, 
						sortable: true,
						renderer: humanReadableDate,
                        dataIndex: 'date'
					}, 
					{
						header: app_strings.LBL_EMAIL_TO,
						width: 250, 
						sortable: false,
                        dataIndex: 'to_addrs'
					}, 
					{
						header: 'uid',
						hidden: true,
                        dataIndex: 'uid'
					}, 
					{
						header: 'mbox',
						hidden: true,
                        dataIndex: 'mbox'
					}, 
					{
						header: 'ieId',
						hidden: true,
                        dataIndex: 'ieId'
					}, 
					{	
						header: 'site_url',
						hidden: true,
                        dataIndex: 'site_url'
					},
					{	header: 'seen',
						hidden: true,
                        dataIndex: 'seen'
					},
					{	header: 'type',
						hidden: true,
                        dataIndex: 'type'
					}
				]
			);
			
			var dataModel = new Ext.data.Store({
		        // load using HTTP
		        proxy: new Ext.data.HttpProxy({url: urlBase}),
                
                //enable sorting on the server accross all data
                remoteSort: true,
		
		        // the return will be XML, so lets set up a reader
		        reader: new Ext.data.XmlReader({
		               record: 'Email',
		               id: 'uid',
		               totalRecords: 'TotalCount'
		           }, ['flagged', 'status', 'from', 'subject', 'date','to_addrs', 'uid', 'mbox', 'ieId', 'site_url', 'seen', 'type', 'AssignedTo'])
		    });
			
			dataModel.baseParams['to_pdf'] = 'true';
			dataModel.baseParams['module'] = 'Emails';
			dataModel.baseParams['action'] = 'EmailUIAjax';
			dataModel.baseParams['emailUIAction'] = 'getMessageListXML';
			dataModel.baseParams['mbox'] = 'INBOX';
			dataModel.baseParams['ieId'] = '';
			dataModel.baseParams['forceRefresh'] = 'false';
			dataModel.baseParams['to_pdf'] = 'true';
	
			if(lazyLoadFolder != null) {
				dataModel.baseParams['mbox'] = lazyLoadFolder.folder;
				dataModel.baseParams['ieId'] = lazyLoadFolder.ieId;
				
				var test = new String(lazyLoadFolder.folder);
				if(test.match(/SUGAR\./)) {
					dataModel.baseParams['emailUIAction'] = 'getMessageListSugarFoldersXML';
					dataModel.baseParams['mbox'] = test.substr(6);
				}
			}
			
			//dataModel.initPaging(urlBase, SUGAR.email2.userPrefs.emailSettings.showNumInList);
	
			// create the Grid
			SUGAR.email2.grid = new Ext.grid.Grid('frameList', {
					selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
                    enableDrag:true,
                    stripeRows: false,
                    trackMouseOver: false,
                    loadMask: true,
					ds: dataModel,
					cm: colModel,
					ddGroup: 'TreeDD',
					enableColumnHide: false
				}
			);
	
			SUGAR.email2.grid.render();
			
			var gridFoot = SUGAR.email2.grid.getView().getFooterPanel(true);
			// add a paging toolbar to the grid's footer
		    var paging = new Ext.PagingToolbar(gridFoot, dataModel, {
		        pageSize      : parseInt(SUGAR.email2.userPrefs.emailSettings.showNumInList),
		        displayInfo   : true,
		        displayMsg    : app_strings.LBL_EMAIL_DISPLAY_MSG,
		        emptyMsg      : app_strings.LBL_EMAIL_EMPTY_MSG,
		        beforePageText: app_strings.LBL_EMAIL_PAGE_BEFORE,
		        afterPageText : app_strings.LBL_EMAIL_PAGE_AFTER,
		        firstText     : app_strings.LBL_EMAIL_TEXT_FIRST,
		        prevText      : app_strings.LBL_EMAIL_TEXT_PREV,
		        nextText      : app_strings.LBL_EMAIL_TEXT_NEXT,
		        lastText      : app_strings.LBL_EMAIL_TEXT_LAST,
		        refreshText   : app_strings.LBL_EMAIL_TEXT_REFRESH
		    });
			
			if(lazyLoadFolder != null) {
				//forcePreview = true;
				SUGAR.email2.grid.getDataSource().load({params:{start:0, limit:parseInt(SUGAR.email2.userPrefs.emailSettings.showNumInList)}});
				//loadPage(1, SUGAR.email2.listView.setEmailListStyles);
				SUGAR.email2.grid.getSelectionModel().selectFirstRow();
			}
			// EVENT handlers
			SUGAR.email2.grid.on('dragdrop', SUGAR.email2.listView.handleDrop, SUGAR.email2.grid, false);
			SUGAR.email2.grid.on('headerclick', SUGAR.email2.listView.boldUnreadRows);
			SUGAR.email2.grid.on('columnmove', SUGAR.email2.listView.boldUnreadRows);
			SUGAR.email2.grid.on('rowcontextmenu', SUGAR.email2.contextMenus.showEmailsListMenu);
			SUGAR.email2.grid.on('rowdblclick', SUGAR.email2.listView.getEmail);

			
            dataModel.on('load', SUGAR.email2.listView.setEmailListStyles);
			SUGAR.email2.grid.getSelectionModel().on('rowselect', SUGAR.email2.listView.handleClick);
			
			SUGAR.email2.grid.getDataSource().on('loadexception', SUGAR.email2.listView.loadException);
		}
	};
	e2Grid.init();
};

function AddressSearchGridInit() {
    function moduleIcon(value) {
        if(value != "") {
            return "<img src='themes/default/images/" + value + ".gif' class='image' border='0' width='16' align='absmiddle'>";
        }
    };
    function selectionCheckBox(value) {
        if(value != "") {
            return '<input type="checkbox" onclick="SUGAR.email2.addressBook.grid.toggleSelect(\'' + value + '\', this.checked);">';
        }
    };
    var checkHeader = '<input type="checkbox" ';
    if (SUGAR.email2.util.isIe()) {
        checkHeader += 'style="top:-5px" ';
    }
    checkHeader += 'onclick="SUGAR.email2.addressBook.grid.toggleSelectAll(this.checked);">';
    var colModel = new Ext.grid.DefaultColumnModel(
	    [{
            header: checkHeader,
            width: 30,
            renderer: selectionCheckBox,
            dataIndex: 'bean_id'
        },
	    {
	        header: '',
	        width: 25,
	        renderer: moduleIcon,
            dataIndex: 'bean_module'
        },
	    {
	        header: app_strings.LBL_EMAIL_ADDRESS_BOOK_NAME, 
	        width: 200,
	        sortable: false,
	        dataIndex: 'name'
	    }, 
	    {
	        header: app_strings.LBL_EMAIL_ADDRESS_BOOK_EMAIL_ADDR,
	        width: 300, 
	        sortable: false,
	        dataIndex: 'email'
	    } 
	    ]
    );
    var dataModel = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({url: urlBase}),
        //enable sorting on the server accross all data
        remoteSort: true,

        // the return will be XML, so lets set up a reader
        reader: new Ext.data.XmlReader({
               record: 'Person',
               id: 'bean_id',
               totalRecords: 'TotalCount'
           }, ['name', 'email', 'bean_id', 'bean_module'])
    });
    
    SUGAR.email2.addressBook.addressBookDataModel = dataModel;
	var reportsDataModel = new Ext.data.Store({
        proxy: new Ext.data.HttpProxy({url: urlBase}),
        //enable sorting on the server accross all data
        remoteSort: true,

        // the return will be XML, so lets set up a reader
        reader: new Ext.data.XmlReader({
               record: 'Person',
               id: 'bean_id',
               totalRecords: 'TotalCount'
           }, ['name', 'email', 'bean_id', 'bean_module'])
    });
    reportsDataModel.baseParams['to_pdf'] = 'true';
    reportsDataModel.baseParams['reportId'] = '';
    reportsDataModel.baseParams['module'] = 'Emails';
    reportsDataModel.baseParams['action'] = 'EmailUIAjax';
    reportsDataModel.baseParams['emailUIAction'] = 'getReportUsers';
    
    SUGAR.email2.addressBook.reportsDataModel = reportsDataModel;   
    dataModel.baseParams['to_pdf'] = 'true';
    dataModel.baseParams['module'] = 'Emails';
    dataModel.baseParams['action'] = 'EmailUIAjax';
    dataModel.baseParams['emailUIAction'] = 'getAddressSearchResults';
    
    SUGAR.email2.addressBook.grid = new Ext.grid.Grid(Ext.DomHelper.append(document.body, {
        tag : 'div',id : 'addrSearchGrid'}), 
        {
            selModel: new Ext.grid.RowSelectionModel({singleSelect:false}),
            enableDrag: false,
            stripeRows: false,
            trackMouseOver: false,
            loadMask: true,
            ds: dataModel,
            cm: colModel,
            enableColumnHide: false
        }
    );
    var grid = SUGAR.email2.addressBook.grid;
    grid.render();

    grid.getSelectionModel().on('beforerowselect', function(selModel, rowIndex, keep) {
        var row = SUGAR.email2.addressBook.grid.getDataSource().getAt(rowIndex)
        if (!row.data.checked) {
            return false;
        }
        return true;
    });
    
    grid.getSelectionModel().on('rowdeselect', function(selModel, rowIndex) {
        var row = SUGAR.email2.addressBook.grid.getDataSource().getAt(rowIndex)
        if (row.data.checked) {
            selModel.selectRow(rowIndex, true);
        }
    });
    
    grid.toggleSelect = function(id, checked) {
        var row = SUGAR.email2.addressBook.grid.dataSource.getById(id);
        row.data.checked = checked;
        if (checked) {
            SUGAR.email2.addressBook.grid.getSelectionModel().selectRow(row.store.indexOf(row), true);
        } else {
            SUGAR.email2.addressBook.grid.getSelectionModel().deselectRow(row.store.indexOf(row));
        }
    };
    
    grid.toggleSelectAll = function(checked) {
        rows = SUGAR.email2.addressBook.grid.getDataSource().getRange(0, 25);
        for (var i = 0; i < rows.length; i++) {
	        var row = rows[i];
	        row.data.checked = checked;
        }
        var checkBoxes = SUGAR.email2.addressBook.grid.getGridEl().dom.getElementsByTagName('input');
        for (var i = 0; i < checkBoxes.length; i++) {
            checkBoxes[i].checked = checked;
        }
        if (checked) {
            SUGAR.email2.addressBook.grid.getSelectionModel().selectAll();
        } else {
            SUGAR.email2.addressBook.grid.getSelectionModel().clearSelections();
        }
    };
    
	/*var layout = Ext.BorderLayout.create({
	    center: {
	        margins:{left:3,top:3,right:3,bottom:3},
	        panels: [new Ext.GridPanel(SUGAR.email2.addressBook.grid)]
	    }
	}, 'adrsrchGridPanel');*/
	
	SUGAR.email2.addressBook.grid.autoSize();
	SUGAR.email2.addressBook.grid.render();
}




