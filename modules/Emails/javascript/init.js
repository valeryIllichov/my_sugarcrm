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
/******************************************************************************
 * Initialize Email 2.0 Application
 */
//Override Sugar Languge so quick creates work properly

function email2init() {
    //Init Tiny MCE
   // var tinyConfig = "code,bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull," +
    //             "separator,bullist,numlist,outdent,indent,separator,forecolor,backcolor,fontselect,fontsizeselect";

     if(SUGAR.email2.util.isIe()) {
         if(typeof(tinyMCE) != 'undefined') {
             tinyMCE.init({
                 theme_advanced_toolbar_align : tinyConfig.theme_advanced_toolbar_align,
                 clean : false,
                 clean_on_startup : false,
                 width: tinyConfig.width,
                 theme: tinyConfig.theme,
                 theme_advanced_toolbar_location : tinyConfig.theme_advanced_toolbar_location,
                 theme_advanced_buttons1 : tinyConfig.theme_advanced_buttons1,
                 theme_advanced_buttons2 : tinyConfig.theme_advanced_buttons2,
                 theme_advanced_buttons3 : tinyConfig.theme_advanced_buttons3,
                 plugins : tinyConfig.plugins,
                 elements : tinyConfig.elements,
                 extended_valid_elements : tinyConfig.extended_valid_elements,
                 mode: tinyConfig.mode
             });
         }
     } else {
         tinyMCE.init({
             theme_advanced_toolbar_align : tinyConfig.theme_advanced_toolbar_align,
             width: tinyConfig.width,
             theme: tinyConfig.theme,
             theme_advanced_toolbar_location : tinyConfig.theme_advanced_toolbar_location,
             theme_advanced_buttons1 : tinyConfig.theme_advanced_buttons1,
             theme_advanced_buttons2 : tinyConfig.theme_advanced_buttons2,
             theme_advanced_buttons3 : tinyConfig.theme_advanced_buttons3,
             plugins : tinyConfig.plugins,
             elements : tinyConfig.elements,
             extended_valid_elements : tinyConfig.extended_valid_elements,
             mode: tinyConfig.mode,
             strict_loading_mode : true
         });
         //alert('loadedTiny');
     }
    // initialze message overlay
    SUGAR.email2.e2overlay = new Ext.BasicDialog("SUGAR.email2.e2overlay", {
            //iframe        : true,
            modal       : false,
            autoTabs    : true,
            width       : 300,
            height      : 120,
            shadow      : true
        }
    );
	// Hide Sugar menu
	document.getElementById('leftCol').style.display='none';

	// add key listener for kb shortcust - disable backspace nav in mozilla/ie
//	YAHOO.util.Event.addListener(window.document, 'keypress', SUGAR.email2.keys.overall);

	// set defaults for YAHOO.util.DragDropManager
	YAHOO.util.DDM.mode = 0; // point mode, default is point (0)

	// initialize treeview for folders
	onloadTreeinit();
	SUGAR.email2.folders.setSugarFolders();
	//SUGAR.email2.accounts.renderTree();



	// JSCalendar objects for Search by Date fields
	Calendar.setup ({inputField : "searchDateFrom", ifFormat : calFormat, showsTime : false, button : "jscal_trigger_from", singleClick : true, step : 1});
	Calendar.setup ({inputField : "searchDateTo", ifFormat : calFormat, showsTime : false, button : "jscal_trigger_to", singleClick : true, step : 1});

	// initialize and display grid (grid.js)
    gridInit();
    // initialize and display UI framework (complexLayout.js)
    complexLayoutInit();
    //Setup the Message Box overlay
    Ext.MessageBox.maxWidth = 350;
    Ext.MessageBox.minProgressWidth = 350;

	///////////////////////////////////////////////////////////////////////////
	////	CONTEXT MENUS
	// detailView array
	SUGAR.email2.contextMenus.detailViewContextMenus = new Object();

	//Grid menu
	SUGAR.email2.contextMenus.emailListContextMenu = new Ext.menu.Menu({items : [
                {
                    text: app_strings.LBL_EMAIL_VIEW_RELATIONSHIPS,
                    id: 'showDetailView',
                    icon: "themes/default/images/icon_email_relate.gif",
                    handler: SUGAR.email2.contextMenus.showDetailView
                },
	            {
                    text: app_strings.LBL_EMAIL_OPEN_ALL,
                    handler: SUGAR.email2.contextMenus.openMultiple
                },
                {
                    text: app_strings.LBL_EMAIL_ARCHIVE_TO_SUGAR,
                    icon: "themes/default/images/icon_email_archive.gif",
                    handler: SUGAR.email2.contextMenus.archiveToSugar
                },
                {
                    text: app_strings.LBL_EMAIL_REPLY,
                    id: 'reply',
                    icon: "themes/default/images/icon_email_reply.gif",
                    handler: SUGAR.email2.contextMenus.replyForwardEmailContext
                },
                {
                    text: app_strings.LBL_EMAIL_REPLY_ALL,
                    id: 'replyAll',
                    icon: "themes/default/images/icon_email_replyall.gif",
                    handler: SUGAR.email2.contextMenus.replyForwardEmailContext
                },
                {
                    text: app_strings.LBL_EMAIL_FORWARD,
                    id: 'forward',
                    icon: "themes/default/images/icon_email_forward.gif",
                    handler: SUGAR.email2.contextMenus.replyForwardEmailContext
                },
                {
                    text: app_strings.LBL_EMAIL_DELETE,
                    id: 'delete',
                    icon: "themes/default/images/icon_email_delete.gif",
                    handler: SUGAR.email2.contextMenus.markDeleted
                },
                {
                    text: app_strings.LBL_EMAIL_PRINT,
                    id: 'print',
                    icon: "themes/default/images/Print_Email.gif",
                    handler: SUGAR.email2.contextMenus.viewPrintable
                },                
                // Mark... submenu
                {
                    text : app_strings.LBL_EMAIL_MARK,
                    icon: "themes/default/images/icon_email_mark.gif",
                    menu: {
                        items : [
                            {
                                text: app_strings.LBL_EMAIL_MARK + " " + app_strings.LBL_EMAIL_MARK_UNREAD,
                                handler : SUGAR.email2.contextMenus.markUnread
                            },
                            {
                                text: app_strings.LBL_EMAIL_MARK + " " + app_strings.LBL_EMAIL_MARK_READ,
                                handler: SUGAR.email2.contextMenus.markRead
                            },
                            {
                                text: app_strings.LBL_EMAIL_MARK + " " + app_strings.LBL_EMAIL_MARK_FLAGGED,
                                handler: SUGAR.email2.contextMenus.markFlagged
                            },
                            {
                                text: app_strings.LBL_EMAIL_MARK + " " + app_strings.LBL_EMAIL_MARK_UNFLAGGED,
                                handler: SUGAR.email2.contextMenus.markUnflagged
                            }
                        ]
                    }
                 },
                {
                	text: app_strings.LBL_EMAIL_ASSIGN_TO,
                	id: 'assignTo',
                    icon: "themes/default/images/icon_email_assign.gif",
                	handler: SUGAR.email2.contextMenus.assignEmailsTo
                 },
                 {
                    text: app_strings.LBL_EMAIL_RELATE_TO,
                    id: 'relateTo',
                    icon: "themes/default/images/icon_email_relate.gif",
                    handler: SUGAR.email2.contextMenus.relateTo
                 }
             ]}
         );

    //Folder Menu
    SUGAR.email2.contextMenus.frameFoldersContextMenu = new Ext.menu.Menu({items: [
        {   text: app_strings.LBL_EMAIL_CHECK,
            //helptext: "<i>" + app_strings.LBL_EMAIL_MENU_HELP_ADD_FOLDER + "</i>",
            icon: "themes/default/images/icon_email_check.gif",
            handler: function() {
                var node = SUGAR.email2.clickedFolderNode;
                if (node.attributes.ieId) {
                    SUGAR.email2.folders.startEmailCheckOneAccount(node.attributes.ieId, false)};
            }
        },
        {   text: app_strings.LBL_EMAIL_MENU_SYNCHRONIZE,
            //helptext: "<i>" + app_strings.LBL_EMAIL_MENU_HELP_ADD_FOLDER + "</i>",
            handler: function() {
                var node = SUGAR.email2.clickedFolderNode;
                if (node.attributes.ieId) {
                    SUGAR.email2.folders.startEmailCheckOneAccount(node.attributes.ieId, true)};
            }
        },
        {
            text: app_strings.LBL_EMAIL_MENU_ADD_FOLDER,
            //helptext: "<i>" + app_strings.LBL_EMAIL_MENU_HELP_ADD_FOLDER + "</i>",
            handler: SUGAR.email2.folders.folderAdd
        },
        {
            text: app_strings.LBL_EMAIL_MENU_DELETE_FOLDER,
            //helptext: "<i>" + app_strings.LBL_EMAIL_MENU_HELP_DELETE_FOLDER + "</i>",
            handler: SUGAR.email2.folders.folderDelete
        },
        {
            text: app_strings.LBL_EMAIL_MENU_RENAME_FOLDER,
            //helptext: "<i>" + app_strings.LBL_EMAIL_MENU_HELP_RENAME_FOLDER + "</i>",
            handler: SUGAR.email2.folders.folderRename
         },
         {
            text: app_strings.LBL_EMAIL_MENU_EMPTY_TRASH,
            //helptext: "<i>" + app_strings.LBL_EMAIL_MENU_HELP_EMPTY_TRASH + "</i>",
            handler: SUGAR.email2.folders.emptyTrash
          },
         {
            text: app_strings.LBL_EMAIL_MENU_CLEAR_CACHE,
            handler: function() {
                var node = SUGAR.email2.clickedFolderNode;
                if (node.attributes.ieId) {
                    SUGAR.email2.folders.clearCacheFiles(node.attributes.ieId)};
            }
          }
          
    ]});


	// contacts
	SUGAR.email2.contextMenus.contactsContextMenu = new Ext.menu.Menu({items: [







				{
					text: app_strings.LBL_EMAIL_MENU_REMOVE,
					//helptext: "<i>" + app_strings.LBL_EMAIL_MENU_HELP_CONTACT_REMOVE + "</i>",
					handler: SUGAR.email2.addressBook.removeContact
				},
				{
					text: app_strings.LBL_EMAIL_MENU_COMPOSE,
					//helptext: "<i>" + app_strings.LBL_EMAIL_MENU_HELP_CONTACT_COMPOSE + "</i>",
					handler: function() {SUGAR.email2.addressBook.composeTo('contacts')}
				}
			]
		}
	);





























	// load contacts
	SUGAR.email2.addressBook.getUserContacts();
	// set auto-check timer
	SUGAR.email2.folders.startCheckTimer();
	// check if we're coming from an email-link click
	Ext.onReady(function() {setTimeout("SUGAR.email2.composeLayout.composePackage()", 2000)});


}

/**
 * Custom TreeView initialization sequence to setup DragDrop targets for every tree node
 */
function email2treeinit(tree, treedata, treediv, params) {
	//ensure the tree data is not corrupt
	if (!treedata) {
	   return;
	}
	//This lazy load data source should never be called!
	var treeDataSource = new Ext.tree.TreeLoader({dataUrl: 'index.php', baseParams :
	   {
   	        action: 'EmailUIAjax',
   	        emailUIAction: 'refreshSugarFolders',
            module:  'Emails',
            sugar_body_only: true,
            to_pdf:  true
       }
    });
    //Remove any old data
    if (SUGAR.email2.tree) {
        SUGAR.email2.tree.getTreeEl().dom.innerHTML = "";
    }

    SUGAR.email2.tree = new Ext.tree.TreePanel('frameFolders', {
        enableDrop:true,
        containerScroll: true,
        loader: treeDataSource,
        rootVisible: false,
        ddGroup: 'TreeDD',
        ddAppendOnly: true
    });
    //new Ext.tree.TreeSorter(SUGAR.email2.tree, {folderSort:true});
    var root = new Ext.tree.AsyncTreeNode(treedata.nodes[0]);
    SUGAR.email2.tree.setRootNode(root);
   	SUGAR.email2.tree.render();
   	root.expand(true, false);
    root.collapse(true, false);


   	//Add an editor to rename folders
   	SUGAR.email2.tree.folderEditor = new Ext.tree.TreeEditor(SUGAR.email2.tree, {
   	        allowBlank: false,
   	        blankText: 'A Folder must have a Title',
   	        completeOnEnter: true,
   	        cancelOnEsc: true,
   	        ignoreNoChange: true
   	});

   	SUGAR.email2.tree.folderEditor.on('beforecomplete', SUGAR.email2.folders.submitFolderRename);
    SUGAR.email2.tree.folderEditor.on('beforestartedit', function(editor){return !editor.disabled});
    SUGAR.email2.tree.folderEditor.disable();
   	SUGAR.email2.tree.on('click', SUGAR.email2.folders.handleClick);
   	SUGAR.email2.tree.on('contextMenu', SUGAR.email2.folders.handleRightClick);
   	//Show what folders are valid drop targets
   	//SUGAR.email2.tree.on('nodedragover', SUGAR.email2.folders.dragOver);
    SUGAR.email2.tree.on('beforenodedrop', SUGAR.email2.folders.handleDrop);
    /*SUGAR.email2.tree.on('nodedragover', function(tree, target, data, point, source, event, dropNodes, cancel) {
        debugger;
    });
    SUGAR.email2.tree.dropZone.notifyOver = function(source, e, data) {
        debugger;
    }
    SUGAR.email2.tree.dropZone.onContainerOver = function(source, e, data) {
        debugger;
    }*/
    SUGAR.email2.accounts.renderTree();

   	//SUGAR.email2.folders.setContextMenus();
   	//SUGAR.email2.folders.setDragDropTargets();
}
