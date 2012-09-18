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
SUGAR.email2 = {
    cache : new Object(),
    o : null, // holder for reference to AjaxObject's return object (used in composeDraft())
    reGUID : new RegExp(/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/i),
    templates : {},
    tinyInstances : {
        currentHtmleditor : ''
    },

    /**
     * preserves hits from email server
     */
    _setDetailCache : function(ret) {
        if(ret.meta) {
            var compKey = ret.meta.mbox + ret.meta.uid;

            if(!SUGAR.email2.cache[compKey]) {
                SUGAR.email2.cache[compKey] = ret;
            }
        }
    },

    autoSetLayout : function() {
        var c = document.getElementById('container');
        var nav = new String(navigator.appVersion);
        var cH = c.offsetTop;
        var h = (nav.match(/MSIE/i)) ? document.body.clientHeight - 10 : document.body.scrollHeight - 10;
        h -= 13;
        c.style.height = h - cH;

        SUGAR.email2.complexLayout.layout();

        if(SUGAR.email2.grid) {
            SUGAR.email2.grid.autoSize();
        }
    }
};

///////////////////////////////////////////////////////////////////////////////
////    EMAIL ACCOUNTS
SUGAR.email2.accounts = {
    outboundDialog : null,
    errorStyle : 'input-error',
    normalStyle : '',

    /**
     * makes async call to retrieve an outbound instance for editting
     */
     //EXT111
    editOutbound : function() {
        var sel = document.getElementById("outbound_email");
        var obi = sel.options[sel.selectedIndex].value;
        var obt = sel.options[sel.selectedIndex].text;

        if(obt.match(/^(add|line|sendmail|system - sendmail)+/)) {
            alert('Invalid Operation');
        } else {
            AjaxObject.startRequest(AjaxObject.accounts.callbackEditOutbound, urlStandard + "&emailUIAction=editOutbound&outbound_email=" + obi);
        }
    },
    deleteOutbound : function() {
        var sel = document.getElementById("outbound_email");
        var obi = sel.options[sel.selectedIndex].value;

        if(obi.match(/^(add|line|sendmail)+/)) {
            alert('Invalid Operation');
        } else {
        	overlay(app_strings.LBL_EMAIL_DELETING_OUTBOUND, app_strings.LBL_EMAIL_ONE_MOMENT);
            AjaxObject.startRequest(AjaxObject.accounts.callbackDeleteOutbound, urlStandard + "&emailUIAction=deleteOutbound&outbound_email=" + obi);
        }
    },
    //EXT111
    getReplyAddress : function() {
        var primary = '';

        for(var i=0; i<SUGAR.email2.userPrefs.current_user.emailAddresses.length; i++) {
            var addy = SUGAR.email2.userPrefs.current_user.emailAddresses[i];

            if(addy.primary_address == "1") {
                primary = addy.email_address;
            }

            if(addy.reply_to == "1") {
                return addy.email_address;
            }
        }

        return primary;
    },

    /**
     * Handles change to 'add'
     *///EXT111
    handleOutboundSelectChange : function() {
        var select = document.getElementById("outbound_email");
        document.getElementById("outbound_email_edit_button").style.display = 'none';
        document.getElementById("outbound_email_delete_button").style.display = 'none';

        if(select.value == 'add') {
            this.showAddSmtp();
        } else if(select.value != 'sendmail' && select.value != 'none' && select.value != 'line' && 
        	select.options[select.selectedIndex].text.search(/system - /) == -1) {
            document.getElementById("outbound_email_edit_button").style.display = '';
            document.getElementById("outbound_email_delete_button").style.display = '';
        }
    },

    /**
     * Called on "Accounts" tab activation event
     */
    lazyLoad : function() {
        // below called with FQ names, wrapped by event handler
        SUGAR.email2.accounts.rebuildAccountList();
        //SUGAR.email2.accounts.rebuildShowAccountList();
        SUGAR.email2.accounts.rebuildMailerOptions();
        SUGAR.email2.accounts.addNewAccount();
    },

    /**
     * Displays a modal diaglogue to add a SMTP server
     */
    showAddSmtp : function() {
        // lazy load dialogue
        if(!this.outboundDialog) {
            this.outboundDialog = new Ext.LayoutDialog("outboundDialog", {
                title: app_strings.LBL_EMAIL_ACCOUNTS_OUTBOUND,
                modal:true,
                width:600,
                height:350,
                shadow:true,
                minWidth:300,
                minHeight:300,

                center: {
                    autoScroll:true,
                    alwaysShowTabs: false
                }
            });

            var layout = this.outboundDialog.getLayout();

            this.outboundDialog.beginUpdate();
            layout.add('center', new Ext.ContentPanel('outboundServers'));
            this.outboundDialog.endUpdate();
            layout.showPanel('outboundServers');
        } // end lazy load
        
        // clear out form
        var form = document.getElementById('outboundEmailForm');
        for(i=0; i<form.elements.length; i++) {
            if(form.elements[i].name == 'mail_smtpport') {
                form.elements[i].value = 25;
            } else if(form.elements[i].type != 'button') {
                form.elements[i].value = '';
            } else if(form.elements[i].type == 'checkbox') {
                form.elements[i].checked = false;
            }
        }

        this.outboundDialog.show();
    },

    /**
     * Accounts' Advanced Settings view toggle
     */
    toggleAdv : function() {
        var adv = document.getElementById("ie_adv");
        if(adv.style.display == 'none') {
            adv.style.display = "";
        } else {
            adv.style.display = 'none';
        }
    },

    /**
     * Presets default values for Gmail
     */
    fillGmailDefaults : function() {
        document.getElementById("mail_smtpserver").value = 'smtp.gmail.com';
        document.getElementById("mail_smtpport").value = '465';
        document.getElementById("mail_smtpauth_req").checked = true;
        document.getElementById("mail_smtpssl").checked = true;
    },

    /**
     * Sets Port field to selected protocol and SSL settings defaults
     */
    setPortDefault : function() {
        var prot    = document.getElementById('protocol');
        var ssl        = document.getElementById('ssl');
        var port    = document.getElementById('port');
        var stdPorts= new Array("110", "143", "993", "995");
        var stdBool    = new Boolean(false);
        var mailboxdiv = document.getElementById("mailboxdiv");
        var trashFolderdiv = document.getElementById("trashFolderdiv");
        var sentFolderdiv = document.getElementById("sentFolderdiv");
		var monitoredFolder = document.getElementById("subscribeFolderButton");
        if(port.value == '') {
            stdBool.value = true;
        } else {
            for(i=0; i<stdPorts.length; i++) {
                if(stdPorts[i] == port.value) {
                    stdBool.value = true;
                }
            }
        }

        if(stdBool.value == true) {
            if(prot.value == 'imap' && ssl.checked == false) { // IMAP
                port.value = "143";
            } else if(prot.value == 'imap' && ssl.checked == true) { // IMAP-SSL
                port.value = '993';
            } else if(prot.value == 'pop3' && ssl.checked == false) { // POP3
                port.value = '110';
            } else if(prot.value == 'pop3' && ssl.checked == true) { // POP3-SSL
                port.value = '995';
            }
        }
        
        if (prot.value == 'imap') {
        	mailboxdiv.style.display = "";
        	trashFolderdiv.style.display = "";
        	sentFolderdiv.style.display = "";
        	monitoredFolder.style.display = "";
        	if (document.getElementById('mailbox').value == "") {
        		document.getElementById('mailbox').value = "INBOX";
        	}
        } else {
        	mailboxdiv.style.display = "none";
        	trashFolderdiv.style.display = "none";
        	sentFolderdiv.style.display = "none";
			monitoredFolder.style.display = "none";
        	document.getElementById('mailbox').value = "";
        } // else
    },

    /**
     * Draws/removes red boxes around required fields.
     */
    ieAccountError : function(style) {
        document.getElementById('server_url').className = style;
        document.getElementById('email_user').className = style;
        document.getElementById('email_password').className = style;
        document.getElementById('protocol').className = style;
        document.getElementById('port').className = style;
    },
    /**
     * Empties all the fields in the accounts edit view
     */
    addNewAccount:function() {
        document.getElementById('ie_id').value = '';
        document.getElementById('ie_name').value = '';
        document.getElementById('from_name').value = SUGAR.email2.userPrefs.current_user.full_name;
        document.getElementById('from_addr').value = this.getReplyAddress();
        document.getElementById('server_url').value = '';
        document.getElementById('email_user').value = '';
        document.getElementById('email_password').value = '';
        document.getElementById('port').value = '';
        document.getElementById('deleteButton').style.display = 'none';

        document.getElementById('protocol').options[0].selected = true;
        // handle SSL
        document.getElementById('ssl').checked = false;
    },

    /**
     * Populates an account's fields in Settings->Accounts
     */
    fillIeAccount:function(jsonstr) {
        var o = JSON.parse(jsonstr);

        document.getElementById('ie_id').value = o.id;
        document.getElementById('ie_name').value = o.name;
        if (o.stored_options != null) {
        	document.getElementById('from_name').value = o.stored_options.from_name;
        	document.getElementById('from_addr').value = o.stored_options.from_addr;
        	if (o.stored_options.trashFolder != null) {
        		document.getElementById('trashFolder').value = o.stored_options.trashFolder;
        	}
        	if (o.stored_options.sentFolder != null) {
        		document.getElementById('sentFolder').value = o.stored_options.sentFolder;
        	}
        }
        document.getElementById('server_url').value = o.server_url;
        document.getElementById('email_user').value = o.email_user;
        document.getElementById('email_password').value = o.email_password;
        document.getElementById('port').value = o.port;
        document.getElementById('group_id').value = o.group_id;
        document.getElementById('mailbox').value = o.mailbox;

        document.getElementById('deleteButton').style.display = 'inline';

        var i = 0;

        // handle SSL
        if(typeof(o.service[2]) != 'undefined') {
            document.getElementById('ssl').checked = true;
        }

        // handle protocol
        if(document.getElementById('protocol').value != o.protocol) {
            var prot = document.getElementById('protocol');
            for(i=0; i<prot.options.length; i++) {
                if(prot.options[i].value == o.service[3]) {
                    prot.options[i].selected = true;
                    this.setPortDefault();
                }
            }
        }

        // handle SMTP selection
        if(o.stored_options != null && typeof(o.stored_options.outbound_email) != 'undefined') {
            var opts = document.getElementById('outbound_email').options;
            for(i=0; i<opts.length; i++) {
                if(opts[i].value == o.stored_options.outbound_email) {
                    opts[i].selected = true;
                }
            }
        }

        SUGAR.email2.accounts.handleOutboundSelectChange();
    },

    deleteIeAccount : function() {
        if(confirm(app_strings.LBL_EMAIL_IE_DELETE_CONFIRM)) {
            overlay(app_strings.LBL_EMAIL_IE_DELETE, app_strings.LBL_EMAIL_ONE_MOMENT);

            var formObject = document.getElementById('ieAccount');
            YAHOO.util.Connect.setForm(formObject);

            AjaxObject.target = 'frameFlex';
            AjaxObject.startRequest(callbackAccountDelete, urlStandard + '&emailUIAction=deleteIeAccount');
        }
    },

    /**
     * Saves Outbound email settings
     */
    saveOutboundSettings : function() {
        YAHOO.util.Connect.setForm(document.getElementById("outboundEmailForm"));
        AjaxObject.startRequest(callbackOutboundSave, urlStandard + "&emailUIAction=saveOutbound");
    },

    saveIeAccount : function() {
        if(SUGAR.email2.accounts.checkIeCreds(true)) {
            document.getElementById('saveButton').disabled = true;

            overlay(app_strings.LBL_EMAIL_IE_SAVE, app_strings.LBL_EMAIL_ONE_MOMENT);

            var formObject = document.getElementById('ieAccount');
            YAHOO.util.Connect.setForm(formObject);

            AjaxObject._reset();
            AjaxObject.target = 'frameFlex';
            AjaxObject.startRequest(callbackAccount, urlStandard + '&emailUIAction=saveIeAccount');
        }
    },

    testSettings : function() {
        form = document.getElementById('ieAccount');

        if(SUGAR.email2.accounts.checkIeCreds()) {
            ie_test_open_popup_with_submit("InboundEmail", "Popup", "Popup", 400, 300, form.server_url.value, form.protocol.value, form.port.value, form.email_user.value, Rot13.write(form.email_password.value), form.mailbox.value, form.ssl.checked, true);
        }
    },

    getFoldersListForInboundAccountForEmail2 : function() {
        form = document.getElementById('ieAccount');
        if(SUGAR.email2.accounts.checkIeCreds()) {
        	var mailBoxValue = form.mailbox.value;
        	if (form.searchField.value.length > 0) {
        		mailBoxValue = "";
        	} // if
            getFoldersListForInboundAccount("InboundEmail", "ShowInboundFoldersList", "Popup", 400, 300, form.server_url.value, form.protocol.value, form.port.value, form.email_user.value, Rot13.write(form.email_password.value), mailBoxValue, form.ssl.checked, true, form.searchField.value);
        } // if
    	
    },
    
    checkIeCreds : function(valiateTrash) {
        var errors = new Array();
        var out = new String();

        var ie_name = document.getElementById('ie_name').value;
        var fromAddress = document.getElementById('from_addr').value;
        var server_url = document.getElementById('server_url').value;
        var email_user = document.getElementById('email_user').value;
        var email_password = document.getElementById('email_password').value;
        var protocol = document.getElementById('protocol').value;
        var port = document.getElementById('port').value;

        if(trim(ie_name) == "") {
            errors.push(app_strings.LBL_EMAIL_ERROR_NAME);
        }
        if(trim(fromAddress) == "") {
            errors.push(app_strings.LBL_EMAIL_ERROR_FROM_ADDRESS);
        }
        if(trim(server_url) == "") {
            errors.push(app_strings.LBL_EMAIL_ERROR_SERVER);
        }
        if(trim(email_user) == "") {
            errors.push(app_strings.LBL_EMAIL_ERROR_USER);
        }
        if(trim(email_password) == "") {
            errors.push(app_strings.LBL_EMAIL_ERROR_PASSWORD);
        }
        if(protocol == "") {
            errors.push(app_strings.LBL_EMAIL_ERROR_PROTOCOL);
        }
        if (protocol == 'imap') {
        	var mailbox = document.getElementById('mailbox').value;
        	if (trim(mailbox) == "") {
        		errors.push(app_strings.LBL_EMAIL_ERROR_MONITORED_FOLDER);
        	} // if
        	if (valiateTrash != null && valiateTrash) {
	        	var trashFolder = document.getElementById('trashFolder').value;
	        	if (trim(trashFolder) == "") {
	        		errors.push(app_strings.LBL_EMAIL_ERROR_TRASH_FOLDER);
	        	} // if
			} // if
        }
        if(port == "") {
            errors.push(app_strings.LBL_EMAIL_ERROR_PORT);
        }

        if(errors.length > 0) {
            out = app_strings.LBL_EMAIL_ERROR_DESC;
            for(i=0; i<errors.length; i++) {
                if(out != "") {
                    out += "\n";
                }
                out += errors[i];
            }

            alert(out);
            return false;
        } else {

            return true;
        }
    },

    getIeAccount : function(ieId) {
        if(ieId == '')
            return;

        overlay(app_strings.LBL_EMAIL_SETTINGS_RETRIEVING_ACCOUNT, app_strings.LBL_EMAIL_ONE_MOMENT);

        var formObject = document.getElementById('ieSelect');
        formObject.emailUIAction.value = 'getIeAccount';

        YAHOO.util.Connect.setForm(formObject);

        AjaxObject.startRequest(callbackIeAccountRetrieve, null);
    },

    /**
     * Iterates through TreeView nodes to apply styles dependent nature of node
     */
    renderTree:function() {
        var tree = SUGAR.email2.tree;
        tree.root.cascade(SUGAR.email2.accounts.setNodeStyle);
    },
    
    //Sets the style for any nodes that need it.
    setNodeStyle : function(node) {
       //Set unread
       if (typeof(node.attributes.unseen) != 'undefined') {
           if (!node.attributes.origText) {
                  node.attributes.origText = node.attributes.text;
           }
           if (node.attributes.unseen > 0) {
               node.setText('<b>' + node.attributes.origText + '(' + node.attributes.unseen + ')<b>');
           }
           else {
               node.setText(node.attributes.origText);
           }
       }
    },

    /**
     * selects or creates the IE element in the multi-select
     */
    focusOrCreateIeEl : function(jsonstr) {
        var o = JSON.parse(jsonstr);

        var ms = document.getElementById('ieAccountList');
        var found = false;

        for(i=0; i<ms.options.length; i++) {
            if(ms.options[i].value == o.id) {
                found = true;
                var newOpt = new Option(o.name, o.id);
                document.ieSelect.ieId.options[i] = newOpt;
                newOpt.selected = true;
            } else {
                ms.options[i].selected = false;
            }
        }


        if(found == false) {
            var newO = new Option(o.name, o.id);
            document.ieSelect.ieId.options[i] = newO;
        }

        // rebuild
        this.rebuildAccountList();
    },

    /**
     * Rebuilds the drop-down selector for available email accounts
     */
    rebuildAccountList:function() {
        var ms = document.getElementById('ieAccountList');

        for(j=0; j<ms.options.length; j++) {
            var newOpt = new Option(ms.options[j].text, ms.options[j].value);
            if(ms.options[j].disabled == true)
                newOpt.disabled = true;
            document.ieSelect.ieId.options[j] = newOpt;
        }
        this.rebuildShowAccountList();
    },

    /**
     * rebuilds the select options for mailer options
     */
    rebuildMailerOptions : function() {
        var select = document.forms['ieAccount'].elements['outbound_email'];

        SUGAR.email2.util.emptySelectOptions(select);

        for(var key in SUGAR.mailers) {
            var display = SUGAR.mailers[key].name;
            var opt = new Option(display, key);
            select.options.add(opt);
        }
    },

    /**
     * rebuilds the multiselect list of "active" or viewed I-E accounts in the Options->Accounts screen
     */
    rebuildShowAccountList:function() {
        var formObject = document.getElementById('ieSelect');
        YAHOO.util.Connect.setForm(formObject);
        SUGAR.email2.accounts.setPortDefault();

        AjaxObject.startRequest(callbackRebuildShowAccountList, urlStandard + '&emailUIAction=rebuildShowAccount');
    },

    /**
     * Async call to rebuild the folder list.  After a folder delete or account delete
     */
    rebuildFolderList : function() {
        overlay(app_strings.LBL_EMAIL_REBUILDING_FOLDERS, app_strings.LBL_EMAIL_ONE_MOMENT);
        AjaxObject.startRequest(callbackFolders, urlStandard + '&emailUIAction=rebuildFolders');
    },
    
    /**
     * Returns the number of remote accounts the user has active.
     */
    getAccountCount : function() {
        var tree = SUGAR.email2.tree;
        var count = 0;
        for(i=0; i<tree._nodes.length; i++) {
            var node = tree._nodes[i];

            if(typeof(node) != 'undefined' && node.data.ieId) {
                count++;
            }
        }
        return count;
    }
};
////    END ACCOUNTS
///////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
////    ADDRESS BOOK
SUGAR.email2.addressBook = {
    _contactCache : new Array(), // cache of contacts
    _dd : new Array(), // filtered list, same format as _contactCache
    _ddLists : new Array(), // list of Lists
    _dd_mlUsed : new Array(), // contacts in mailing list edit view column1
    _dd_mlAvailable : new Array(), // contacts in mailing list edit view column2
    clickBubble : true, // hack to get around onclick event bubbling






    itemSpacing : 'white-space:nowrap; padding:2px;',
    reGUID : SUGAR.email2.reGUID,

    /**
     * sets up the async call to add a Person to the address book
     * @param String elId
     */
    addContact : function(elId) {
        var form = document.getElementById(elId);
        var get = "&bean_module=" + form.bean_module.value + "&bean_id=" + form.bean_id.value;
        AjaxObject.startRequest(callbackGetUserContacts, urlStandard + "&emailUIAction=addContact" + get);
    },












































































    /**
     * takes an array of data (usually from an async call) and builds the SugarContacts list
     */
    buildContactList : function(contactCache) {
         //Initalize the view if we haven't yet
        if (!SUGAR.email2.contactView) {
             var contactTemplate = new Ext.Template(
                "<div id='{id}' class='{cls}' nowrap style='cursor:pointer; white-space: nowrap;display:{disp}' unselectable='on'>" +
                "{innerHTML}</div>"
            );
            
            SUGAR.email2.contactView = new Ext.View('contacts', contactTemplate, {multiSelect: true});
            SUGAR.email2.contactView.on('contextmenu', function (view, index, target, e) {
                e.stopEvent();
                if(!view.isSelected(target)){
                    view.select(target, e.ctrlKey);
                }
                var isContact = (target.className.indexOf('-contact') > -1);
                var selections = view.getSelectedNodes();
                var matchClass = (selections[0].className.indexOf("address-contact") > -1) ? "address-contact" : "address-email";
                for (var i=0; i < selections.length; i++) {
                    if (selections[i].className.indexOf(matchClass) == -1 && selections[i].style.display != "none") {
                        view.clearSelections(true);
                        view.select(target);
                        break;
                    }
                }
                SUGAR.email2.contextMenus.contactsContextMenu.items.items[0][isContact ? 'show' : 'hide']();
                SUGAR.email2.contextMenus.contactsContextMenu.show(target);
            });
        }
    
        var target = document.getElementById("contacts");
        target.innerHTML = "";
        var contactsData = [];
        for (var i in contactCache) {
            var person = contactCache[i];
            var innerHTML = "<img src='themes/default/images/" + person.module + ".gif' class='img' align='absmiddle'>"
                          + "&nbsp;" + person.name;
            //Create the collapsed node
            contactsData.push([i, 'address-contact', "", innerHTML]);
            //Create the expanded node
            contactsData.push(['ex' + i, 'address-exp-contact', "none", "<i>" + innerHTML + "</i>"]);
            for (var j in person.email) {
                if (person.email[j].email_address) {
                    var emailHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&lt;" + person.email[j].email_address + "&gt;";
                    var emailPrimary = "";
                    if (person.email[j].primary_address == "1") {emailPrimary = " address-primary";}
                    contactsData.push([person.email[j].id, 'address-email' + emailPrimary, "none",  emailHTML]);
                }
            }
        }
        
        var ds = new Ext.data.Store({
                proxy: new Ext.data.MemoryProxy(contactsData),
                reader: new Ext.data.ArrayReader({}, [
                    {name: 'id'},
                    {name: 'cls'},
                    {name: 'disp'},
                    {name: 'innerHTML'}
                ])
            });
        SUGAR.email2.contactView.setStore(ds);
        ds.load()
        this._dd = new ContactsDragZone(SUGAR.email2.contactView, {containerScroll: true, ddGroup: 'addressBookDD'});
        SUGAR.email2.contactView.on('dblclick', this._dd.toggleContact);
        this._dd.setContacts(contactCache);
    },

    cancelEdit : function() {
        if(this.editContactDialog)
            this.editContactDialog.hide();
        if(this.editMailingListDialog)
            this.editMailingListDialog.hide();
    },

    /**
     * Clears filter form
     */
    clear : function() {
        var t = document.getElementById('contactsFilter');
        t.value = '';
        this.filter(t);
    },

    /**
     * handle context-menu Compose-to call
     * @param string type 'contacts' or 'lists'
     */
    composeTo : function(type, waited) {
        var activePanel = SUGAR.email2.innerLayout.getRegion('center').activePanel.getId();
        if (activePanel.substring(0, 13) != "composeLayout") {
            SUGAR.email2.composeLayout.c0_composeNewEmail();
            setTimeout("SUGAR.email2.addressBook.composeTo('" + type + "', true);");
	        SUGAR.email2.contextMenus.contactsContextMenu.hide();



            return;
        }
        var idx = activePanel.substring(13);
        var nodes = [ ];
        var id = '';
        // determine if we have a selection to work with
        if(type == 'contacts') {
            nodes = SUGAR.email2.contactView.getSelectedNodes();
        } 





		else { return; }
        
        //Remove hidden nodes
        removeHiddenNodes(nodes);

        if(nodes.length > 0) {
            //var idx = SUGAR.email2.composeLayout.currentInstanceId;
            SUGAR.email2.composeLayout.handleDrop(
                (type == 'contacts') ? SUGAR.email2.contactView.el : SUGAR.email2.emailListsView.el, 
                null, nodes, 'addressTo' + idx );
        } else {
            alert(app_strings.LBL_EMAIL_MENU_MAKE_SELECTION);
        }
    },

    /**
     * builds one row in the listView
     * @param int index
     * @param Object AssocArray
     */
    displaySearchResultRow : function(idx, obj) {
        var imgPath = "themes/default/images/" + obj.module + ".gif";

        Ext.DomHelper.append("peopleTable", {
            tag : 'tr',
            id : 'resultRow' + idx
        }, true);

        tmp = Ext.DomHelper.append('resultRow' + idx, {
            tag : 'td',
            cls : ''
        }, true);
        var tdId = tmp.dom.id;
        Ext.DomHelper.append(tdId, {
            tag : 'img',
            cls : 'img',
            src : imgPath
        });
        Ext.DomHelper.append('resultRow' + idx, {
            tag : 'td',
            cls : '',
            width : '1%',
            nowrap : "NOWRAP",
            html : obj.full_name
        });
        Ext.DomHelper.append('resultRow' + idx, {
            tag : 'td',
            cls : '',
            width : '1%',
            nowrap : "NOWRAP",
            html : obj.email_address
        });
        tmp = Ext.DomHelper.append('resultRow' + idx, {
            tag : 'td',
            width : '1%',
            nowrap : "NOWRAP",
            style : "text-align:right;",
            cls : ''
        }, true);
        var buttonId = tmp.dom.id;

        // form
        tmp = Ext.DomHelper.append(buttonId, {
            tag : 'form',
            id : 'addForm' + idx,
            method : 'get',
            action : '#'
        }, true);
        var addFormId = tmp.dom.id;
        Ext.DomHelper.append(addFormId, {
            tag : 'input',
            type : 'hidden',
            name : 'bean_id',
            value : obj.id
        });
        Ext.DomHelper.append(addFormId, {
            tag : 'input',
            type : 'hidden',
            name : 'bean_module',
            value : obj.module
        });
        Ext.DomHelper.append(addFormId, {
            tag : 'input',
            type : 'button',
            cls : 'button',
            value : app_strings.LBL_EMAIL_ADDRESS_BOOK_ADD,
            onclick : "SUGAR.email2.addressBook.addContact('addForm" + idx + "');"
        });
    },

    editContact : function() {
        SUGAR.email2.contextMenus.contactsContextMenu.hide();
        var element = SUGAR.email2.contactView.getSelectedNodes()[0];
        var elementId = "";
        if (element.className.indexOf('address-contact') > -1) {
            elementId = element.id;
        } else if (element.className.indexOf('address-exp-contact') > -1) {
            elementId = element.id.substring(2);
        }

        if(elementId != "") {
            // verify that it is a Sugar Contact
            var contact = SUGAR.email2.addressBook._contactCache[elementId];

            if(contact.module == 'Contacts') {
                // lazy load settings
                if(!SUGAR.email2.addressBook.editContactDialog) {
                    SUGAR.email2.addressBook.editContactDialog = new Ext.LayoutDialog("editContact", {
                        modal:true,
                        width:600,
                        height:400,
                        shadow:true,
                        minWidth:300,
                        minHeight:300,

                        center: {
                            autoScroll:true,
                            tabPosition: SUGAR.email2.userPrefs.emailSettings.tabPosition,
                            closeOnTab: false,
                            alwaysShowTabs: false
                        }
                    });
                } // end lazy load

                AjaxObject.startRequest(AjaxObject.addressBook.callback.editContact, urlStandard + "&emailUIAction=editContact&id=" + elementId);
            } else {
                overlay(app_strings.LBL_EMAIL_ERROR_DESC, app_strings.LBL_EMAIL_ADDRESS_BOOK_ERR_NOT_CONTACT, 'alert');
            }
        } else {



            alert(app_strings.LBL_EMAIL_MENU_MAKE_SELECTION);
        }
    },
    

    /**
     * Filters contact entries based on user input
     */
    filter : function(inputEl) {
        var ret = new Object();
        var re = new RegExp(inputEl.value, "gi");

        for(var i in this._contactCache) {
            if(this._contactCache[i].name.match(re)) {
                ret[i] = this._contactCache[i];
            }
        }

        this.buildContactList(ret);
    },

    fullForm : function(id, module) {
        document.location = "index.php?return_module=Emails&return_action=index&module=" + module + "&action=EditView&record=" + id;
    },

    /**
     * returns a formatted email address from the addressBook cache
     */
    getFormattedAddress : function(id) {
        var o = this._contactCache[id];
        var primaryEmail = '';

        for(var i=0; i<o.email.length; i++) {
            var currentEmail = o.email[i].email_address;

            if(o.email[i].primary_address == 1) {
                primaryEmail = o.email[i].email_address;
            }
        }

        var finalEmail = (primaryEmail == "") ? currentEmail : primaryEmail;
        var name = new String(o.name);
        var finalName = name.replace(/(<([^>]+)>)/ig, "");
        var ret = finalName + " <" + finalEmail + ">";

        return ret;
    },


    /**
     * Generates the listView form that unites users, contacts, leads, and prospects
     * @return string HTML
     */
    getPeopleListView : function() {
        var sf = document.getElementById('searchForm');

        // lazy load searchform
        if(sf == null || typeof(sf) == 'undefined') {
            var tmp = null;

            // search form
            Ext.DomHelper.append("contactsDialogueHTML", {
                tag : 'form',
                id : 'searchForm',
                method : 'GET',
                action : "#"
            });
            Ext.DomHelper.append("searchForm", {
                tag : 'table',
                id : 'searchTable',
                cellpadding : 0,
                cellspacing : 0,
                border : 0,
                width : "100%",
                cls : 'tabForm'
            });
            Ext.DomHelper.append("searchTable", {
                tag : 'tr',
                id : 'peopleTableSearchRow'
            });
            // first name
            Ext.DomHelper.append('peopleTableSearchRow', {
                tag : 'td',
                nowrap : "NOWRAP",
                id : "searchNameFirst",
                cls : 'dataLabel',
                html : app_strings.LBL_EMAIL_ADDRESS_BOOK_FIRST_NAME + ": "
            });
            Ext.DomHelper.append("searchNameFirst", {
                tag : "input",
                type : 'text',
                name : 'first_name',
                id : 'input_searchNameFirst'
            });
            // last name
            Ext.DomHelper.append('peopleTableSearchRow', {
                tag : 'td',
                nowrap : "NOWRAP",
                id : "searchNameLast",
                cls : 'dataLabel',
                html : app_strings.LBL_EMAIL_ADDRESS_BOOK_LAST_NAME + ": "
            });
            Ext.DomHelper.append("searchNameLast", {
                tag : "input",
                type : 'text',
                name : 'last_name',
                id : 'input_searchNameLast'
            });
            // email
            Ext.DomHelper.append('peopleTableSearchRow', {
                tag : 'td',
                nowrap : "NOWRAP",
                id : "searchEmail",
                cls : 'dataLabel',
                html : app_strings.LBL_EMAIL_ADDRESS_BOOK_EMAIL_ADDR + ": "
            });
            Ext.DomHelper.append("searchEmail", {
                tag : "input",
                type : 'text',
                name : 'email_address',
                id : 'input_searchEmail'
            });
            // search button
            Ext.DomHelper.append('peopleTableSearchRow', {
                tag : 'td',
                nowrap : "NOWRAP",
                id : "searchSubmit",
                cls : 'dataLabel'
            });
            Ext.DomHelper.append("searchSubmit", {
                tag : "input",
                cls : 'button',
                type : 'button',
                onclick : "SUGAR.email2.addressBook.searchContacts();",
                value : "   " + app_strings.LBL_EMAIL_ADDRESS_BOOK_SEARCH + "   ",
                id : 'input_searchSubmit'
            });

            Ext.DomHelper.append("contactsDialogueHTML", {
                tag : "br"
            });
        }

        return document.getElementById('contactsDialogueHTML').innerHTML;
    },

    /**
     * Parses through Contact object and returns the primary email address
     * @param object
     * @return string
     */
    getPrimaryEmailFromContact : function(contact) {
        var emails = contact.email;

        for(var i=0; i<emails.length; i++) {
            if(emails[i].primary_address == '1') {
                return emails[i].email_address;
            }
        }
        return '';
    },

    /**
     * Async call to get user's contacts & groups
     */
    getUserContacts : function() {
        if(SUGAR.email2.addressBook._contactCache.length < 1) {
            AjaxObject.startRequest(callbackGetUserContacts, urlStandard + "&emailUIAction=getUserContacts");
        }
    },

    removeContact : function() {
        SUGAR.email2.contextMenus.contactsContextMenu.hide();

        if(confirm(app_strings.LBL_EMAIL_CONFIRM_DELETE)) {
            var str = '';
            var selectedItems = SUGAR.email2.contactView.getSelectedNodes();
            removeHiddenNodes(selectedItems);
            for(var i=0; i < selectedItems.length; i++) {
                var node = selectedItems[i];
                if (node.className.indexOf('address-contact') > -1) {
                    if(str != '') {
                        str += "::";
                    }
                    str += node.id;
                } else if (node.className.indexOf('address-exp-contact') > -1) {
                    if(str != '') {
                        str += "::";
                    }
                    str += node.id.substring(2);
                }
            }

            if(str != "") {
                AjaxObject.startRequest(callbackGetUserContacts, urlStandard + '&emailUIAction=removeContact&ids=' + str);
            }
        }
    },































































    /**
     * commits changes to a contact record from Email 2.0
     * @return bool false on failure
     */
    saveContact : function() {
        var form = document.getElementById('editContactForm');
        var errors = new Array();
        var emailElements = new Array();

        if(form.contact_last_name.value == "") {
            errors.push(app_strings.LBL_EMAIL_ERROR_CONTACT_NAME)
        }

        if(errors.length > 0) {
            var out = new String();
            out = app_strings.LBL_EMAIL_ERROR_DESC;
            for(i=0; i<errors.length; i++) {
                if(out != "") {
                    out += "\n";
                }
                out += errors[i];
            }

            alert(out);
            return false;
        } else {
            var send = new Object();
            send.invalid = new Array();
            send.optOut = new Array();
            send.primary = '';

            // get values and save
            for(var i=0; i<form.elements.length; i++) {
                var el = form.elements[i];

                if(el.type == 'text' || el.type == 'hidden') {
                    send[el.name] = el.value;

                    // get id if is address field
                    if(el.name.match(/emailAddress[0-9]/) && el.value != "") {
                        emailElements.push(el.name);
                    }
                }
            }

            // handle multi-value (PHP array) values
            for(var j=0; j<emailElements.length; j++) {
                var indexNumber = emailElements[j].substr(12);
                var optOut = document.getElementById("emailAddressOptOutFlag" + indexNumber);
                var invalid = document.getElementById("emailAddressInvalidFlag" + indexNumber);
                var primary = document.getElementById("emailAddressPrimaryFlag" + indexNumber);

                if(optOut && optOut.checked) {
                    send.optOut.push(optOut.value);
                }

                if(invalid && invalid.checked) {
                    send.invalid.push(invalid.value);
                }

                if(primary && primary.checked) {
                    send.primary = primary.value;
                }
            }

            var args = JSON.stringifyNoSecurity(send);
            AjaxObject.startRequest(callbackGetUserContacts, urlStandard + "&emailUIAction=saveContactEdit&args=" + args);
        }
    },

    /**
     * Sets up async call to query for matching contacts, users, etc.
     */
    searchContacts : function() {
        var fn = document.getElementById('input_searchNameFirst').value;
        var ln = document.getElementById('input_searchNameLast').value;
        var em = document.getElementById('input_searchEmail').value;
        var pe = document.getElementById('input_searchPerson').value;
        this.grid.getDataSource().baseParams['first_name'] = fn;
        this.grid.getDataSource().baseParams['last_name'] = ln;
        this.grid.getDataSource().baseParams['email_address'] = em;
        this.grid.getDataSource().baseParams['person'] = pe;
        this.grid.getDataSource().baseParams['emailUIAction'] = 'getAddressSearchResults';
        SUGAR.email2.addressBook.grid.toggleSelectAll(false);
        this.grid.getDataSource().load({params: {start:0, limit: 25}});
        //SUGAR.email2.addressBook.addressBookDataModel = this.grid.getDataSource();
    },
    
    getAddressBookPanel : function() {
        grid = SUGAR.email2.addressBook.grid;
        grid.reconfigure(SUGAR.email2.addressBook.addressBookDataModel, grid.getColumnModel());
        document.getElementById('addressBookGridFooterDiv').style.display = "";



    },

























































    /**
     * Opens modal select window to add contacts to addressbook
     */
    selectContactsDialogue : function(destId) {
        if (!SUGAR.email2.addressBook.grid) {
            AddressSearchGridInit();
        }
        if(!this.contactsDialogue) {
            this.contactsDialogue = new Ext.LayoutDialog("contactsDialogue", {
                iframe : true,
                modal : true,
                width : 800,
                height : 450,
                shadow : true,
                minWidth : 300,
                minHeight : 300,

                north: {
                	alwaysShowTabs : false,
                    autoScroll : false,
                    closeOnTab : false,
                    tabPosition: "top",
                    initialSize: 80
                },
                center: {
                    autoScroll : true,
                    closeOnTab : false,
                    alwaysShowTabs : false
                }
            });
            //this.contactsDialogue.getLayout().addRegion('north', {alwaysShowTabs : true, minTabWidth : 200});
            contactsDialogueHTMLContentpanel = new Ext.ContentPanel('contactsDialogueHTML', {title : app_strings.LBL_EMAIL_ADDRESS_BOOK_TITLE});
            this.contactsDialogue.getLayout().add('north', contactsDialogueHTMLContentpanel);






            this.contactsDialogue.getLayout().add('center', new Ext.GridPanel(SUGAR.email2.addressBook.grid));

            //this.contactsDialogue.getLayout().add('center', new Ext.GridPanel(SUGAR.email2.addressBook.grid))  
            this.contactsDialogue.setTitle(app_strings.LBL_EMAIL_ADDRESS_BOOK_SELECT_TITLE);
            this.contactsDialogue.addButton(app_strings.LBL_EMAIL_ADDRESS_BOOK_ADD, this.addMultipleContacts);
            this.contactsDialogue.addButton(app_strings.LBL_EMAIL_CLOSE, function(){
                SUGAR.email2.addressBook.contactsDialogue.hide();
            });
            
            
            var gridFoot = this.grid.getView().getFooterPanel(true);
		    // add a paging toolbar to the grid's footer
		    var paging = new Ext.PagingToolbar('addressBookGridFooterDiv', SUGAR.email2.addressBook.addressBookDataModel, {
		        pageSize      : 25,
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
		    gridFoot.dom.appendChild(document.getElementById('addressBookGridFooterDiv'));
		    document.getElementById('addressBookGridFooterDiv').style.display = "";
		    SUGAR.email2.addressBook.addressBookPaging = paging;
			this.contactsDialogue.getLayout().showPanel('contactsDialogueHTML');
		    this.contactsDialogue.resizeTo(800, 450);
        }
        if (destId) {
            this.contactsDialogue.buttons[0].handler = this.insertContactToField;
            this.contactsDialogue.target = destId;
        } else {
            this.contactsDialogue.buttons[0].handler = this.addMultipleContacts;
        }
        //this.contactsDialogue.layout.regions.center.activePanel.setContent(this.getPeopleListView());
        this.contactsDialogue.show('selectContacts');
    },
    
    addMultipleContacts : function() {
        var rows = SUGAR.email2.addressBook.grid.getSelections();
        var contacts = [];
        for (var i = 0; i < rows.length; i++) {
            var data = rows[i].data;
            contacts.push({id : data.bean_id, module : data.bean_module});
        }
        var contactData = JSON.stringifyNoSecurity(contacts);
        
        SUGAR.email2.addressBook.grid.toggleSelectAll(false);
        
        AjaxObject.startRequest(callbackGetUserContacts, urlStandard + "&emailUIAction=addContactsMultiple&contactData=" + contactData);
    },
    
    insertContactToField : function() {
        var contactsDialogue = SUGAR.email2.addressBook.contactsDialogue;
        var target = document.getElementById(contactsDialogue.target);
        var contacts = SUGAR.email2.addressBook.grid.getSelections();
        for (var i=0; i < contacts.length; i++) {
            var data = contacts[i].data;
            target.value = SUGAR.email2.addressBook.smartAddEmailAddressToComposeField(target.value, data.name + ' <' + data.email + '>');
        }
        SUGAR.email2.addressBook.grid.toggleSelectAll(false);
    },





























































































    /**
     * adds an email address to a string, but first checks if it exists
     * @param string concat The string we are appending email addresses to
     * @param string addr Email address to add
     * @return string
     */
    smartAddEmailAddressToComposeField : function(concat, addr) {
        var re = new RegExp(addr);

        if(!concat.match(re)) {
            if(concat != "") {
                concat += "; " + addr;
            } else {
                concat = addr;
            }
        }

        return concat;
    }
};
////    END ADDRESS BOOK
///////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////
////    AUTOCOMPLETE
/**
 * Auto-complete object
 */
SUGAR.email2.autoComplete = {
    config : {
        delimChar : [";", ","],
        useShadow :    false,
        useIFrame : false,
        typeAhead : true,
        prehighlightClassName : "yui-ac-prehighlight",
        queryDelay : 0
    },
    instances : new Array(),

    /**
     * Parses an addressBook entry looking for primary address.  If not found, it will return the last found address.
     * @param object Contact from AddressBook
     * @return string
     */
    getPrimaryAddress : function(contact) {
        var address = app_strings.LBL_EMAIL_ADDRESS_BOOK_NOT_FOUND;

        for(var eIndex in contact.email) {
            address = contact.email[eIndex].email_address;
            if(contact.email[eIndex].primary_address == 1) {
                return contact.email[eIndex].email_address;
            }
        }
        return address;
    },
    

    /**
     * initializes autocomplete widgets for a given compose view
     * @param int idx
     */
    init : function(idx) {
        var ds = new YAHOO.widget.DS_JSArray(this.returnDataSource(SUGAR.email2.addressBook._contactCache), {
            "queryMatchContains" : false,
            "queryMatchSubset" : true
        });

        this.instances[idx] = {
            to : null,
            cc : null,
            bcc : null
        };

   
        // instantiate the autoComplete widgets
        this.instances[idx]['to'] = new YAHOO.widget.AutoComplete('addressTo'+idx, "addressToAC"+idx, ds, this.config);
        this.instances[idx]['cc'] = new YAHOO.widget.AutoComplete('addressCC'+idx, "addressCcAC"+idx, ds, this.config);
        this.instances[idx]['bcc'] = new YAHOO.widget.AutoComplete('addressBCC'+idx, "addressBccAC"+idx, ds, this.config);

        // enable hiding of interfering textareas
        this.instances[idx]['to'].containerExpandEvent.subscribe(SUGAR.email2.autoComplete.toggleTextareaHide);
        this.instances[idx]['cc'].containerExpandEvent.subscribe(SUGAR.email2.autoComplete.toggleTextareaHide);
        this.instances[idx]['bcc'].containerExpandEvent.subscribe(SUGAR.email2.autoComplete.toggleTextareaHide);

        // enable reshowing of hidden textareas
        this.instances[idx]['to'].containerCollapseEvent.subscribe(SUGAR.email2.autoComplete.toggleTextareaShow);
        this.instances[idx]['cc'].containerCollapseEvent.subscribe(SUGAR.email2.autoComplete.toggleTextareaShow);
        this.instances[idx]['bcc'].containerCollapseEvent.subscribe(SUGAR.email2.autoComplete.toggleTextareaShow);

        // enable refreshes of contact lists
        this.instances[idx]['to'].textboxFocusEvent.subscribe(SUGAR.email2.autoComplete.refreshDataSource);
        this.instances[idx]['cc'].textboxFocusEvent.subscribe(SUGAR.email2.autoComplete.refreshDataSource);
        this.instances[idx]['bcc'].textboxFocusEvent.subscribe(SUGAR.email2.autoComplete.refreshDataSource);

    },

    refreshDataSource : function(sType, aArgs) {
        var textBoxId = aArgs[0]._oTextbox.id; // "addressTo0"
        var idx;
        var refresh = SUGAR.email2.autoComplete.returnDataSource(SUGAR.email2.addressBook._contactCache);

        if(textBoxId.indexOf("addressTo") > -1 || textBoxId.indexOf("addressCC") > -1) {
            idx = textBoxId.substr(9);
        } else {
            idx = textBoxId.substr(10);
        }

        SUGAR.email2.autoComplete.instances[idx]['to'].dataSource.data = refresh;
        SUGAR.email2.autoComplete.instances[idx]['cc'].dataSource.data = refresh;
        SUGAR.email2.autoComplete.instances[idx]['bcc'].dataSource.data = refresh;
    },

    /**
     * Parses AddressBook entries to return an appropriate DataSource array for YUI.autoComplete
     */
    returnDataSource : function(contacts) {
        var ret = new Array();
            for(var id in contacts) {
                if (contacts[id].name) {
                var primary = this.getPrimaryAddress(contacts[id]);
    
                ret[ret.length] = contacts[id].name.replace(/<[\/]*b>/gi, '') + " <" + primary + ">";
                //ret[ret.length] = contacts[id].name + " <" + primary + ">";
    
                for(var emailIndex in contacts[id].email) {
                    ret[ret.length] = contacts[id].email[emailIndex].email_address;
                }
                }
            }

        return ret;
    },

    /**
     * Hides address textareas to prevent autocomplete dropdown from being obscured
     */
    toggleTextareaHide : function(sType, aArgs) {
        var textBoxId = aArgs[0]._oTextbox.id; // "addressTo0"
        var type = "";
        var idx = -1;

        if(textBoxId.indexOf("addressTo") > -1) {
            type = "to";
        } else if(textBoxId.indexOf("addressCC") > -1) {
            type = "cc";
        }
        idx = textBoxId.substr(9);

        // follow through if not BCC
        if(type != "") {
            var cc = document.getElementById("addressCC" + idx);
            var bcc = document.getElementById("addressBCC" + idx);

            switch(type) {
                case "to":
                    cc.style.visibility = 'hidden';
                case "cc":
                    bcc.style.visibility = 'hidden';
                break;
            }
        }
    },

    /**
     * Redisplays the textareas after an address is commited
     */
    toggleTextareaShow : function(sType, aArgs) {
        var textBoxId = aArgs[0]._oTextbox.id; // "addressTo0"
        var type = "";
        var idx = -1;

        if(textBoxId.indexOf("addressTo") > -1) {
            type = "to";
        } else if(textBoxId.indexOf("addressCC") > -1) {
            type = "cc";
        }
        idx = textBoxId.substr(9);

        // follow through if not BCC
        if(type != "") {
            document.getElementById("addressCC" + idx).style.visibility = 'visible';
            document.getElementById("addressBCC" + idx).style.visibility = 'visible';
        }
    }
};

////    END AUTOCOMPLETE
///////////////////////////////////////////////////////////////////////////////































///////////////////////////////////////////////////////////////////////////////
////    COMPOSE & SEND
/**
 * expands the options sidebar
 */
SUGAR.email2.composeLayout = {
    currentInstanceId : 0,
    tinyConfig : "code,bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull," +
                 "separator,bullist,numlist,outdent,indent,separator,forecolor,backcolor,fontselect,fontsizeselect",

    ///////////////////////////////////////////////////////////////////////////
    ////    COMPOSE FLOW
    /**
     * Prepare bucket DIV and yui-ext tab panels
     */
    _0_yui_ext : function() {
        var idx = this.currentInstanceId;

        // render div and tab
        // create bucket DIV
        Ext.DomHelper.append(document.body, {
            tag:'div',
            name:'htmleditordiv' + idx,
            id:'htmleditordiv' + idx,
            cls:'x-layout-inactive-content'
        });

        // get template engine with template
        var editorTarget = new Ext.DomHelper.Template(SUGAR.email2.templates['compose']);

        // apply template variables
        editorTarget.append('htmleditordiv' + idx, {
            'app_strings':app_strings,
            'mod_strings':mod_strings,
            'theme': theme,
            'linkbeans_options' : linkBeans,
            'idx' : SUGAR.email2.composeLayout.currentInstanceId
        });

        e2Layout.getComposeLayout();

        // create Tab panel
        var composePanel = new Ext.NestedLayoutPanel(SUGAR.email2.composeLayout[idx], {isCompose : true});
        composePanel.title = app_strings.LBL_EMAIL_COMPOSE;
        composePanel.closable = true;
		composePanel.on('activate', function () {SUGAR.email2.tinyInstances.currentHtmleditor = "htmleditor" + idx});

        SUGAR.email2.innerLayout.add('center', composePanel);

        // work-around to hide sliding panels in IE
        //SUGAR.email2.composeLayout[idx].regions.east.expand();
        //SUGAR.email2.composeLayout[idx].regions.east.collapse();
    },

    isParentTypeValid : function(idx) {
		var parentTypeValue = document.getElementById('data_parent_type' + idx).value;
		var parentNameValue = document.getElementById('data_parent_name' + idx).value;
		if (trim(parentTypeValue) == ""){
			alert(mod_strings.LBL_ERROR_SELECT_MODULE);
			return false;
		} // if
		return true;
    },
    
    isParentTypeAndNameValid : function(idx) {
		var parentTypeValue = document.getElementById('data_parent_type' + idx).value;
		var parentNameValue = document.getElementById('data_parent_name' + idx).value;
		var parentIdValue = document.getElementById('data_parent_id' + idx).value;
		if ((trim(parentTypeValue) != "" && trim(parentNameValue) == "") || 
			(trim(parentTypeValue) != "" && trim(parentNameValue) != "" && parentIdValue == "")){
				alert(mod_strings.LBL_ERROR_SELECT_MODULE_SELECT);
			return false;
		} // if
		return true;
    },

    callopenpopupForEmail2 : function(idx) {
		var parentTypeValue = document.getElementById('data_parent_type' + idx).value;
		var parentNameValue = document.getElementById('data_parent_name' + idx).value;
		if (!SUGAR.email2.composeLayout.isParentTypeValid(idx)) {
			return;
		} // if
		open_popup(document.getElementById('data_parent_type' + idx).value,600,400,'&tree=ProductsProd',true,false,{"call_back_function":"set_return","form_name":'emailCompose' + idx,"field_to_name_array":{"id":'data_parent_id' + idx,"name":'data_parent_name' + idx}}); 	
	},    
    /**
     * Prepare TinyMCE
     */
    _1_tiny : function() {
        var idx = SUGAR.email2.composeLayout.currentInstanceId;
        SUGAR.email2.tinyInstances.currentHtmleditor = 'htmleditor' + idx;
        SUGAR.email2.tinyInstances[SUGAR.email2.tinyInstances.currentHtmleditor] = new Object();
        SUGAR.email2.tinyInstances[SUGAR.email2.tinyInstances.currentHtmleditor].ready = false;

        var t = tinyMCE.getInstanceById('htmleditor' + idx);

        if(typeof(t) == 'undefined')  {
            
            var nav = new String(navigator.appVersion);

            var cof = document.getElementById('composeOverFrame' + idx);
            var head = document.getElementById('composeHeaderTable' + idx);
            var targetHeight = cof.clientHeight - head.clientHeight;
            tinyMCE.execCommand('mceAddControl', false, SUGAR.email2.tinyInstances.currentHtmleditor);
            var instance =  tinyMCE.getInstanceById(SUGAR.email2.tinyInstances.currentHtmleditor);
            var tableEl = document.getElementById(instance.editorId + '_parent').firstChild;
            var toolbar = document.getElementById(instance.editorId + '_toolbar');
            if (SUGAR.email2.util.isIe()) {
                instance.iframeElement.style.height = "100%";
                instance.iframeElement.height = "100%";
            } else {
                instance.iframeElement.style.height = targetHeight - toolbar.clientHeight;
            }
            tableEl.style.height = targetHeight;
            setTimeout("SUGAR.email2.composeLayout.setSignature('" + idx + "');", 1000);
        }
    },

    /**
     * Initializes d&d, auto-complete, email templates
     */
    _2_final : function() {
        var idx = SUGAR.email2.composeLayout.currentInstanceId;

        if(this.emailTemplates) {
            this.setComposeOptions(idx);
        } else {
            //populate email template cache
            AjaxObject.target = '';
            AjaxObject.startRequest(callbackComposeCache, urlStandard + "&emailUIAction=fillComposeCache");
        }

        // handle drop targets for addressBook
        var to = new Ext.dd.DropTarget('addressTo'+idx, {ddGroup: 'addressBookDD', notifyDrop:this.handleDrop});
        var cc = new Ext.dd.DropTarget('addressCC' + idx, {ddGroup: 'addressBookDD', notifyDrop:this.handleDrop});
        var bcc = new Ext.dd.DropTarget('addressBCC' + idx, {ddGroup: 'addressBookDD', notifyDrop:this.handleDrop});

        // auto-complete setup
        SUGAR.email2.autoComplete.init(idx);

        // set focus on to:
        document.getElementById("addressTo" + idx).focus();
    },



    c1_composeEmail : function(isReplyForward, retry) {
        if (Ext.isEmpty(retry)) {
            this._0_yui_ext();
        }
        if (typeof(tinyMCE) == 'undefined' || typeof(tinyMCE.settings) == 'undefined' 
        || typeof(tinyMCE.themes.advanced) == 'undefined'){
            setTimeout("SUGAR.email2.composeLayout.c1_composeEmail(" + isReplyForward + ", true);", 500);
        } else {
	        this._1_tiny();
	        this._2_final();
	
	        if(isReplyForward) {
	            this.replyForwardEmailStage2();
	        }
        }
    },

    /**
     * takes draft info and prepopulates
     */
    c0_composeDraft : function() {
        this.getNewInstanceId();
        inCompose = true;
        document.getElementById('_blank').innerHTML = '';

        var idx = SUGAR.email2.composeLayout.currentInstanceId;
        SUGAR.email2.composeLayout.currentInstanceId = idx;
        SUGAR.email2.tinyInstances.currentHtmleditor = 'htmleditor' + SUGAR.email2.composeLayout.currentInstanceId;
        SUGAR.email2.tinyInstances[SUGAR.email2.tinyInstances.currentHtmleditor] = new Object();
        SUGAR.email2.tinyInstances[SUGAR.email2.tinyInstances.currentHtmleditor].ready = false;

        SUGAR.email2.composeLayout._0_yui_ext();
        SUGAR.email2.composeLayout._1_tiny();
        // hack to hide "hidden" option/attach panel content in both IE and FF
        SUGAR.email2.composeLayout[idx].regions.east.expand();
        SUGAR.email2.composeLayout[idx].regions.east.collapse();

        // final touches
        SUGAR.email2.composeLayout._2_final();

        /* Draft-specific final processing. Need a delay to allow Tiny to render before calling setText() */
        setTimeout("AjaxObject.handleReplyForwardForDraft(SUGAR.email2.o);", 1000);
    },

    /**
     * Strip & Prep editor hidden fields
     */
    c0_composeNewEmail : function() {
        this.getNewInstanceId();
        this.c1_composeEmail(false);
    },

    /**
     * Sends async request to get the compose view.
     * Requests come from "reply" or "forwards"
     */
    c0_replyForwardEmail : function(ieId, uid, mbox, type) {
        SUGAR.email2.composeLayout.replyForwardObj = new Object();
        SUGAR.email2.composeLayout.replyForwardObj.ieId = ieId;
        SUGAR.email2.composeLayout.replyForwardObj.uid = uid;
        SUGAR.email2.composeLayout.replyForwardObj.mbox = mbox;
        SUGAR.email2.composeLayout.replyForwardObj.type = type;

        if(mbox == 'sugar::Emails') {
            SUGAR.email2.composeLayout.replyForwardObj.sugarEmail = true;
        }

        SUGAR.email2.composeLayout.getNewInstanceId();
        SUGAR.email2.composeLayout.c1_composeEmail(true);
    },
    ////    END COMPOSE FLOW
    ///////////////////////////////////////////////////////////////////////////

    /**
     * Called when a contact, email, or mailinglist is dropped
     * into one of the compose fields.
     */
    handleDrop : function (source, event, data, target) {
        var nodes;
        if (!target) {
            target = event.getTarget();
            if (data.single) {
                data.nodes = [data.nodes];
            }
            nodes = data.nodes;
        } else {
            target = document.getElementById(target);
            nodes = data;
        }
        
        //If a mailing list was dropped
        if (source.id == 'lists') {
































        }
        else if (target.id.indexOf('address') > -1) {
            // dropped onto email to/cc/bcc field
            for(var i in nodes) {
                if (nodes[i].id) {
                    var node = nodes[i];
                    var email = "";
                    if (node.className.indexOf('contact') > -1) {
                        email = SUGAR.email2.addressBook.getFormattedAddress(node.id);
                    } else if (node.className.indexOf('address-email') > -1){
                        email = node.innerHTML.replace(/&nbsp;/gi, '');
                        email = email.replace('&lt;', '<').replace('&gt;', '>');
                        var o = SUGAR.email2.addressBook._contactCache[node.parentNode.firstChild['id']];
                        var name = new String(o.name);
                        var finalName = name.replace(/(<([^>]+)>)/ig, "");
                        email = finalName + email;
                    }
                    target.value = SUGAR.email2.addressBook.smartAddEmailAddressToComposeField(target.value, email);
                }                
            }
        }
    },


    /*/////////////////////////////////////////////////////////////////////////////
    ///    EMAIL TEMPLATE CODE
     */
    applyEmailTemplate : function (idx, id) {
        // id is selected index of email template drop-down

        if(id == '' || id == "0") {
            return;
        }
        
        //bug #20680
        var box_title = SUGAR.language.get('Emails', 'LBL_EMAILTEMPLATE_MESSAGE_SHOW_TITLE');
		var box_msg = SUGAR.language.get('Emails', 'LBL_EMAILTEMPLATE_MESSAGE_SHOW_MSG');
	
		Ext.MessageBox.show({
           title:box_title,
           msg: box_msg,
           buttons: Ext.MessageBox.YESNO,
           fn: function(btn){
           		if(btn=='no'){return;};
           		this.processResult(idx, id);},
           modal:true,
           scope:this
       });
    },
	
	processResult : function(idx , id){
        call_json_method('EmailTemplates','retrieve','record='+id,'email_template_object', this.appendEmailTemplateJSON);

        // get attachments if any
        AjaxObject.target = '';
        AjaxObject.startRequest(callbackLoadAttachments, urlStandard + "&emailUIAction=getTemplateAttachments&parent_id=" + id);
    },

    appendEmailTemplateJSON : function() {
        var idx = SUGAR.email2.composeLayout.currentInstanceId; // post increment

        // query based on template, contact_id0,related_to
        //jchi 09/10/2008 refix #7743
        if(json_objects['email_template_object']['fields']['subject'] != '' ) { // cn: bug 7743, don't stomp populated Subject Line
            document.getElementById('emailSubject' + idx).value = decodeURI(encodeURI(json_objects['email_template_object']['fields']['subject']));
        }

        var text = decodeURI(encodeURI(json_objects['email_template_object']['fields']['body_html'])).replace(/<BR>/ig, '\n').replace(/<br>/gi, "\n").replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"');

        // cn: bug 14361 - text-only templates don't fill compose screen
        if(text == '') {
            text = decodeURI(encodeURI(json_objects['email_template_object']['fields']['body'])).replace(/<BR>/ig, '\n').replace(/<br>/gi, "\n").replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"').replace(/\r\n/gi,"<br/>");
        }

        var tiny = SUGAR.email2.util.getTiny('htmleditor' + idx);
        var tinyHTML = tiny.getHTML('htmleditor' + idx);
        var openTag = '<div><span><span>';
        var closeTag = '</span></span></div>';
        var htmllow = tinyHTML.toLowerCase();
        var start = htmllow.indexOf(openTag);
		if (start > -1) {
	        var htmlPart2 = tinyHTML.substr(start);
	        tinyHTML = text + htmlPart2;
	        tiny.setHTML(tinyHTML);
		} else {
        	tiny.setHTML(text);
		}
    },

    /**
     * Writes out the signature in the email editor
     */
    setSignature : function(idx) {
        if (!tinyMCE)
            return false;
        //wait for signatures to load before trying to set them
        if (!SUGAR.email2.composeLayout.signatures) {
            setTimeout("SUGAR.email2.composeLayout.setSignature(" + idx + ");", 1000);
        }
            
        if(idx) {
            var sel = document.getElementById('signatures' + idx);
        } else {
            var sel = document.getElementById('signature_id');
            idx = SUGAR.email2.tinyInstances.currentHtmleditor;
        }

        var signature = '';

        try {
            signature = sel.options[sel.selectedIndex].value;
        } catch(e) {

        }

        var openTag = '<div><span><span>';
        var closeTag = '</span></span></div>';
        var t = SUGAR.email2.util.getTiny('htmleditor' + idx);
        //IE 6 Hack
        t.contentDocument = t.contentWindow.document;
        var html = t.getHTML('htmleditor' + idx);
        var htmllow = html.toLowerCase();
        var start = htmllow.indexOf(openTag);
        var end = htmllow.indexOf(closeTag) + closeTag.length;

        // selected "none" - remove signature from email
        if(signature == '') {
            if (start > -1) {
                var htmlPart1 = html.substr(0, start);
                var htmlPart2 = html.substr(end, html.length);
    
                html = htmlPart1 + htmlPart2;
                t.setHTML(html);
            }
            SUGAR.email2.signatures.lastAttemptedLoad = '';
            return false;
        }

        if(!SUGAR.email2.signatures.lastAttemptedLoad) // lazy load place holder
            SUGAR.email2.signatures.lastAttemptedLoad = '';

        SUGAR.email2.signatures.lastAttemptedLoad = signature;

        if(typeof(SUGAR.email2.signatures[signature]) == 'undefined') {
            //lazy load
            SUGAR.email2.signatures.lastAttemptedLoad = ''; // reset this flag for recursion
            SUGAR.email2.signatures.targetInstance = (idx) ? idx : "";
            AjaxObject.target = '';
            AjaxObject.startRequest(callbackLoadSignature, urlStandard + "&emailUIAction=getSignature&id="+signature);
        } else {
            var newSignature = this.prepareSignature(SUGAR.email2.signatures[signature]);

            // clear out old signature
            if(SUGAR.email2.signatures.lastAttemptedLoad && start > -1) {
                var htmlPart1 = html.substr(0, start);
                var htmlPart2 = html.substr(end, html.length);

                html = htmlPart1 + htmlPart2;
            }

            // [pre|ap]pend
			start = html.indexOf('<div><hr></div>');
            if(SUGAR.email2.userPrefs.signatures.signature_prepend == 'true' && start > -1) {
				var htmlPart1 = html.substr(0, start);
				var htmlPart2 = html.substr(start, html.length);
                var newHtml = htmlPart1 + openTag + newSignature + closeTag + htmlPart2;
            } else {
                var newHtml = html + openTag + newSignature + closeTag;
            }
            //tinyMCE.setContent(newHtml);
            t.setHTML(newHtml);
        }
    },

    prepareSignature : function(str) {
        var signature = new String(str);

        signature = signature.replace(/&lt;/gi, '<');
        signature = signature.replace(/&gt;/gi, '>');

        /*
        // the following is specific to tinyMCE's idiosyncracies
        signature = signature.replace(/&quot;/gi, '"');
        signature = signature.replace(/;/gi, ''); // removes semi-colons?!?!
        */

        return signature;
    },


    showAttachmentPanel : function(idx) {
        var east = SUGAR.email2.composeLayout[idx].regions.east;

        if(east.collapsed == true && (typeof(east.isSlid) == 'undefined' || east.isSlid == false)) {
            east.slideOut();
        }
        east.showPanel('divAttachments'+idx);
    },

    /**
     * expands sidebar and displays options panel
     */
    showOptionsPanel : function(idx) {
        var east = SUGAR.email2.composeLayout[idx].regions.east;

        if(east.collapsed == true && (typeof(east.isSlid) == 'undefined' || east.isSlid == false)) {
            east.slideOut();
        }
        east.showPanel('divOptions'+idx);
    },

    /**
     * Selects the Contacts tab
     */
    showContactsPanel : function() {
        SUGAR.email2.complexLayout.regions.west.showPanel("contactsTab");
    },

    /**
     * Generates fields for Select Document
     */
    addDocumentField : function(idx) {
        var basket = document.getElementById('addedDocuments' + idx);
        if(basket) {
            var index = (basket.childNodes.length / 7) - 1;
            if(index < 0)
                index = 0;
        } else {
            index = 0;
        }

        var test = document.getElementById('documentId' + idx + index);

        while(test != null) {
            index++;
            test = document.getElementById('documentId' + idx + index);
        }

        // holder div - to allow quick removal
        Ext.DomHelper.append(basket, {
            tag:'div',
            id:'documentCup' + idx + index
        });

        var d = document.getElementById('documentCup' + idx + index);

        // document field
        Ext.DomHelper.append(d, {
            tag:'input',
            type:'hidden',
            name:'document' + idx + index,
            id:'document' + idx + index
        });
        // docId field
        Ext.DomHelper.append(d, {
            tag:'input',
            type:'hidden',
            name:'documentId' + idx + index,
            id:'documentId' + idx + index
        });
        // docName field
        Ext.DomHelper.append(d, {
            tag:'input',
            type:'text',
            name:'documentName' + idx + index,
            id:'documentName' + idx + index,
            disabled: true,
            size: 20
        });
        // select button
        Ext.DomHelper.append(d, {
            tag:'input',
            type: 'button',
            cls: 'button',
            onclick: "SUGAR.email2.composeLayout.selectDocument('" + index + "');",
            value: app_strings.LBL_EMAIL_SELECT,
            name:'documentSelect' + idx + index,
            id:'documentSelect' + idx + index,
            size: 30
        });
        // remove button
        Ext.DomHelper.append(d, {
            tag:'input',
            type: 'button',
            cls: 'button',
            onclick: "SUGAR.email2.composeLayout.deleteDocumentField('documentCup" + idx + index +"');",
            value: app_strings.LBL_EMAIL_REMOVE,
            name:'documentRemove' + idx + index,
            id:'documentRemove' + idx + index,
            size: 30
        });
        // br tag
        Ext.DomHelper.append(d, {tag:'br', id:'brdoc' + index});

        return index;
    },

    /**
     * Makes async call to save a draft of the email
     * @param int Instance index
     */
    saveDraft : function(tinyInstance) {
        this.sendEmail(tinyInstance, true);
    },

    selectDocument : function(target) {
        URL="index.php?module=Emails&action=PopupDocuments&to_pdf=true&target=" + target;
        windowName = 'selectDocument';
        windowFeatures = 'width=800' + ',height=600' + ',resizable=1,scrollbars=1';

        win = window.open(URL, windowName, windowFeatures);
        if(window.focus) {
            // put the focus on the popup if the browser supports the focus() method
            win.focus();
        }
    },

    /**
     * Modal popup for file attachment dialogue
     */
    addFileField : function() {
        if(!SUGAR.email2.addFileDialog){ // lazy initialize the dialog and only create it once
            SUGAR.email2.addFileDialog = new Ext.LayoutDialog("addFileDialog", {
                modal:true,
                title:app_strings.LBL_EMAIL_ATTACHMENTS,
                width:400,
                height:200,
                shadow:false,
                minWidth:300,
                minHeight:300,
                center: {
                    autoScroll:true,
                    tabPosition: SUGAR.email2.userPrefs.emailSettings.tabPosition,
                    closeOnTab: true
                }
            });
            SUGAR.email2.addFileDialog.addKeyListener(27, SUGAR.email2.addFileDialog.hide, SUGAR.email2.addFileDialog);

            var layout = SUGAR.email2.addFileDialog.getLayout();
            SUGAR.email2.addFileDialog.beginUpdate();
                layout.add('center', new Ext.ContentPanel('addFileDialogContent', {title: 'Inner Tab'}));
            SUGAR.email2.addFileDialog.endUpdate();
        }
        SUGAR.email2.addFileDialog.show();
    },

    /**
     * Async upload of file to temp dir
     */
    uploadAttachment : function() {
        if(document.getElementById('email_attachment').value != "") {
            var formObject = document.getElementById('uploadAttachment');
            YAHOO.util.Connect.setForm(formObject, true, true);
            AjaxObject.target = '';
            AjaxObject.startRequest(callbackUploadAttachment, null);
        } else {
            alert(app_strings.LBL_EMAIL_ERROR_NO_FILE);
        }
    },

    /**
     * Adds a SugarDocument to an outbound email.  Action occurs in a popup window displaying a ListView from the Documents module
     * @param string target in focus compose layout
     */
    setDocument : function(idx, target, documentId, documentName, docRevId) {
        // fields are named/id'd [fieldName][instanceId][index]
        var addedDocs = document.getElementById("addedDocuments" + idx);
        var docId = document.getElementById('documentId' + idx + target);
        var docName = document.getElementById('documentName' + idx + target);
        var docRevisionId = document.getElementById('document' + idx + target);
        docId.value = documentId;
        docName.value = documentName;
        docRevisionId.value = docRevId;
    },

    /**
     * Removes the bucket div containing the document input fields
     */
    deleteDocumentField : function(documentCup) {
        var f0 = document.getElementById(documentCup);
        f0.parentNode.removeChild(f0);
    },

    /**
     * Removes a Template Attachment field
     * @param int
     * @param int
     */
    deleteTemplateAttachmentField : function(idx, index) {
        // create not-in-array values for removal filtering
        var r = document.getElementById("templateAttachmentsRemove" + idx).value;

        if(r != "") {
            r += "::";
        }

        r += document.getElementById('templateAttachmentId' + idx + index).value;
        document.getElementById("templateAttachmentsRemove" + idx).value = r;

        var target = 'templateAttachmentCup' + idx + index;
        d =  document.getElementById(target);
        d.parentNode.removeChild(d);
    },

    /**
     * Async removal of uploaded temp file
     * @param string index Should be a concatenation of idx and index
     * @param string
     */
    deleteUploadAttachment : function(index, file) {
        var d = document.getElementById('email_attachment_bucket' + index);
        d.parentNode.removeChild(d);

        // make async call to delete cached file
        AjaxObject.target = '';
        AjaxObject.startRequest(null, urlStandard + "&emailUIAction=removeUploadedAttachment&file="+file);
    },

    /**
     * Attaches files coming from Email Templates
     */
    addTemplateAttachmentField : function(idx) {
        // expose title
        document.getElementById('templateAttachmentsTitle' + idx).style.display = 'block';

        var basket = document.getElementById('addedTemplateAttachments' + idx);

        if(basket) {
            var index = basket.childNodes.length;
            if(index < 0)
                index = 0;
        } else {
            index = 0;
        }

        // holder div - to allow quick removal
        Ext.DomHelper.append(basket, {
            tag:'div',
            id:'templateAttachmentCup' + idx + index
        });

        var d = document.getElementById('templateAttachmentCup' + idx + index);

        // remove button
        Ext.DomHelper.append(d, {
            tag:'img',
            src: 'themes/Sugar/images/minus.gif',
            cls: 'image',
            style: 'cursor:pointer',
            align: 'absmiddle',
            onclick: "SUGAR.email2.composeLayout.deleteTemplateAttachmentField('" + idx + "', '" +index +"');"
        });
        // file icon
        Ext.DomHelper.append(d, {
            tag:'img',
            src: 'themes/default/images/attachment.gif',
            align: 'absmiddle',
            cls: 'image'
        });
        // templateAttachment field
        Ext.DomHelper.append(d, {
            tag:'input',
            type:'hidden',
            name:'templateAttachment' + idx + index,
            id:'templateAttachment' + idx + index
        });
        // docId field
        Ext.DomHelper.append(d, {
            tag:'input',
            type:'hidden',
            name:'templateAttachmentId' + idx + index,
            id:'templateAttachmentId' + idx + index
        });
        // file name
        Ext.DomHelper.append(d, {
            tag    : 'span',
            id    : 'templateAttachmentName' + idx + index,
            html: "&nbsp;"
        });
        // br
        Ext.DomHelper.append(d, {
            tag:'br',
            id:'br' + index
        });
        // br tag
        Ext.DomHelper.append(d, {tag:'br', id:'brdoc' + index});

        return index;
    },

    /**
     * Sends one email via async call
     * @param int idx Editor instance ID
     * @param bool isDraft
     */
    sendEmail : function(idx, isDraft) {
        var form = document.getElementById('emailCompose' + idx);
        var t = SUGAR.email2.util.getTiny(SUGAR.email2.tinyInstances.currentHtmleditor);
        var html = t.getHTML();
        var subj = document.getElementById('emailSubject' + idx).value;
        var to = trim(document.getElementById('addressTo' + idx).value);
        var cc = trim(document.getElementById('addressCC' + idx).value);
        var bcc = trim(document.getElementById('addressBCC' + idx).value);
        var email_id = document.getElementById('email_id' + idx).value;
        var composeType = document.getElementById('composeType').value;
        var parent_type = document.getElementById("parent_type").value;
        var parent_id = document.getElementById("parent_id").value;
        if (!isValidEmail(to) || !isValidEmail(cc) || !isValidEmail(bcc)) {
			alert(app_strings.LBL_EMAIL_COMPOSE_INVALID_ADDRESS);
        	return false;
        }

        if (!SUGAR.email2.composeLayout.isParentTypeAndNameValid(idx)) {
        	return;
        } // if
		var parentTypeValue = document.getElementById('data_parent_type' + idx).value;
		var parentIdValue = document.getElementById('data_parent_id' + idx).value;
        parent_id = parentIdValue;
        parent_type = parentTypeValue;

        var in_draft = (document.getElementById('type' + idx).value == 'draft') ? true : false;
        // baseline viability check

        if(to == "" && cc == '' && bcc == '' && !isDraft) {
            alert(app_strings.LBL_EMAIL_COMPOSE_ERR_NO_RECIPIENTS);
            return false;
        } else if(subj == '' && !isDraft) {
            if(!confirm(app_strings.LBL_EMAIL_COMPOSE_NO_SUBJECT)) {
                return false;
            } else {
                subj = app_strings.LBL_EMAIL_COMPOSE_NO_SUBJECT_LITERAL;
            }
        } else if(html == '' && !isDraft) {
            if(!confirm(app_strings.LBL_EMAIL_COMPOSE_NO_BODY)) {
                return false;
            }
        }

        SUGAR.email2.util.clearHiddenFieldValues('emailCompose' + idx);

        var title = (isDraft) ? app_strings.LBL_EMAIL_SAVE_DRAFT : app_strings.LBL_EMAIL_SENDING_EMAIL;
        overlay(title, app_strings.LBL_EMAIL_ONE_MOMENT);
        html = html.replace(/&lt;/ig, "sugarLessThan");       
        html = html.replace(/&gt;/ig, "sugarGreaterThan");
        form.sendDescription.value = html;
        form.sendSubject.value = subj;
        form.sendTo.value = to;
        form.sendCc.value = cc;
        form.sendBcc.value = bcc;
        form.email_id.value = email_id;
        form.composeType.value = composeType;
        form.composeLayoutId.value = 'composeLayout' + idx;
        form.setEditor.value = (document.getElementById('setEditor' + idx).checked == true) ? 1 : 0;
        form.sendCharset.value = document.getElementById('charsetOptions' + idx).value;
        form.saveToSugar.value = (document.getElementById('saveOutbound' + idx).checked == true) ? 1 : 0;
        form.fromAccount.value = document.getElementById('addressFrom' + idx).value;
        form.parent_type.value = parent_type;
        form.parent_id.value = parent_id;




        // email attachments
        var addedFiles = document.getElementById('addedFiles' + idx);
        if(addedFiles) {
            for(i=0; i<addedFiles.childNodes.length; i++) {
                var bucket = addedFiles.childNodes[i];

                for(j=0; j<bucket.childNodes.length; j++) {
                    var node = bucket.childNodes[j];
                    var nName = new String(node.name);

                    if(node.type == 'hidden' && nName.match(/email_attachment/)) {
                        if(form.attachments.value != '') {
                            form.attachments.value += "::";
                        }
                        form.attachments.value += node.value;
                    }
                }
            }
        }

        // sugar documents
        var addedDocs = document.getElementById('addedDocuments' + idx);
        if(addedDocs) {
            for(i=0; i<addedDocs.childNodes.length; i++) {
                var cNode = addedDocs.childNodes[i];
                for(j=0; j<cNode.childNodes.length; j++) {
                    var node = cNode.childNodes[j];
                    var nName = new String(node.name);
                    if(node.type == 'hidden' && nName.match(/documentId/)) {
                        if(form.documents.value != '') {
                            form.documents.value += "::";
                        }
                        form.documents.value += node.value;
                    }
                }
            }
        }

        // template attachments
        var addedTemplateAttachments = document.getElementById('addedTemplateAttachments' + idx);
        if(addedTemplateAttachments) {
            for(i=0; i<addedTemplateAttachments.childNodes.length; i++) {
                var cNode = addedTemplateAttachments.childNodes[i];
                for(j=0; j<cNode.childNodes.length; j++) {
                    var node = cNode.childNodes[j];
                    var nName = new String(node.name);
                    if(node.type == 'hidden' && nName.match(/templateAttachmentId/)) {
                        if(form.templateAttachments.value != "") {
                            form.templateAttachments.value += "::";
                        }
                        form.templateAttachments.value += node.value;
                    }
                }
            }
        }

        // remove attachments
        form.templateAttachmentsRemove.value = document.getElementById("templateAttachmentsRemove" + idx).value;

        YAHOO.util.Connect.setForm(form);

        AjaxObject.target = 'frameFlex';

        // sending a draft email
        if(!isDraft && in_draft) {
            // remove row
            SUGAR.email2.listView.removeRowByUid(email_id);
        }

        var sendCallback = (isDraft) ? AjaxObject.composeLayout.callback.saveDraft : callbackSendEmail;
        var emailUiAction = (isDraft) ? "&emailUIAction=sendEmail&saveDraft=true" : "&emailUIAction=sendEmail";

        AjaxObject.startRequest(sendCallback, urlStandard + emailUiAction);
    },

    /**
     * Handles clicking the email address link from a given view
     */
    composePackage : function() {
        if(composePackage != null) {
            SUGAR.email2.composeLayout.c0_composeNewEmail();

            if(composePackage.to_email_addrs) {
                document.getElementById("addressTo" + SUGAR.email2.composeLayout.currentInstanceId).value = composePackage.to_email_addrs;
            } // if
            if (composePackage.subject != null && composePackage.subject.length > 0) {
            	document.getElementById("emailSubject" + SUGAR.email2.composeLayout.currentInstanceId).value = composePackage.subject;
            }
            
            if(composePackage.parent_type) {
                document.getElementById("parent_type").value = composePackage.parent_type;
                document.getElementById('data_parent_type' + SUGAR.email2.composeLayout.currentInstanceId).value = composePackage.parent_type;
            } // if
            if(composePackage.parent_id) {
                document.getElementById("parent_id").value = composePackage.parent_id;
                document.getElementById('data_parent_id' + SUGAR.email2.composeLayout.currentInstanceId).value = composePackage.parent_id;
            } // if
            if(composePackage.parent_name) {
                document.getElementById('data_parent_name' + SUGAR.email2.composeLayout.currentInstanceId).value = composePackage.parent_name;
            } // if
            if(composePackage.email_id != null && composePackage.email_id.length > 0) {
                document.getElementById("email_id" + SUGAR.email2.composeLayout.currentInstanceId).value = composePackage.email_id;
            } // if
            if (composePackage.body != null && composePackage.body.length > 0) {
		        var tiny = SUGAR.email2.util.getTiny('htmleditor' + SUGAR.email2.composeLayout.currentInstanceId);
		        var tinyHTML = tiny.getHTML('htmleditor' + SUGAR.email2.composeLayout.currentInstanceId);
		        composePackage.body = decodeURI(encodeURI(composePackage.body));
		
		        // cn: bug 14361 - text-only templates don't fill compose screen
		        if(composePackage.body == '') {
		            composePackage.body = decodeURI(encodeURI(composePackage.body)).replace(/<BR>/ig, '\n').replace(/<br>/gi, "\n").replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"');
		        }

		        SUGAR.email2.composeLayout.tinyHTML = tinyHTML + composePackage.body;		        
		        setTimeout("SUGAR.email2.composeLayout.setHTMLOnThisTiny();", 1000);
            	
            } // if
            if (composePackage.attachments != null) {
				SUGAR.email2.composeLayout.loadAttachments(composePackage.attachments);            	
            } // if
            
            if (composePackage.fromAccounts != null && composePackage.fromAccounts.status) {
				var addressFrom = document.getElementById('addressFrom' + SUGAR.email2.composeLayout.currentInstanceId);
		        SUGAR.email2.util.emptySelectOptions(addressFrom);
		        var fromAccountOpts = composePackage.fromAccounts.data;
		        for(i=0; i<fromAccountOpts.length; i++) {
		              var key = fromAccountOpts[i].value;
		              var display = fromAccountOpts[i].text;
		              var opt = new Option(display, key);
		              if (fromAccountOpts[i].selected) {
		              	opt.selected = true;
		              }
		              addressFrom.options.add(opt);
		        }			
            	
            } // if
        } // if
    },

    setHTMLOnThisTiny : function() {
    	var tiny = SUGAR.email2.util.getTiny('htmleditor' + SUGAR.email2.composeLayout.currentInstanceId);
    	tiny.setHTML(SUGAR.email2.composeLayout.tinyHTML);
    },
    /**
     * Confirms closure of a compose screen if "x" is clicked
     */
    confirmClose : function(region, panel, e) {
        if(panel.isCompose) {
            if(confirm(app_strings.LBL_EMAIL_CONFIRM_CLOSE)) {
                SUGAR.email2.composeLayout.closeCompose(panel.layout.id);
            } else {
                e.cancel = true;
            }
        }
    },

    /**
     * forces close of a compose screen
     */
    forceCloseCompose : function(id) {
        SUGAR.email2.innerLayout.regions.center.purgeListeners();
        SUGAR.email2.innerLayout.regions.center.remove(id, false);
        SUGAR.email2.innerLayout.regions.center.addListener("beforeremove", SUGAR.email2.composeLayout.confirmClose);

        SUGAR.email2.composeLayout.closeCompose(id);

        // handle flow back to originating view
        if(composePackage) {
            // check if it's a module we need to return to
            if(composePackage.return_module && composePackage.return_action && composePackage.return_id) {
                if(confirm(app_strings.LBL_EMAIL_RETURN_TO_VIEW)) {
                    var url = "index.php?module=" + composePackage.return_module + "&action=" + composePackage.return_action + "&record=" + composePackage.return_id;
                    window.location = url;
                }
            }
        }
    },

    /**
     * closes the editor that just sent email
     * @param string id ID of composeLayout tab
     */
    closeCompose : function(id) {
        // destroy tinyMCE instance
        var idx = id.substr(13, id.length);
        var instanceId = "htmleditor" + idx;
        tinyMCE.removeInstance(SUGAR.email2.util.getTiny(instanceId));

        // nullify DOM and namespace values.
        inCompose = false;
        SUGAR.email2.composeLayout[idx] = null;
        SUGAR.email2.tinyInstances[instanceId] = null;
    },

    /**
     * Returns a new instance ID, 0-index
     */
    getNewInstanceId : function() {
        this.currentInstanceId = this.currentInstanceId + 1;
        return this.currentInstanceId;
    },

    /**
     * Takes an array of objects that contain the filename and GUID of a Note (attachment or Sugar Document) and applies the values to the compose screen.  Valid use-cases are applying an EmailTemplate or resuming a Draft Email.
     */
    loadAttachments : function(result) {
        var idx = SUGAR.email2.composeLayout.currentInstanceId;

        if(typeof(result) == 'object') {
        	//jchi #20680. Clean the former template attachments;
        	var basket = document.getElementById('addedTemplateAttachments' + idx);
        	if(basket.innerHTML != ''){
        		Ext.Msg.confirm(mod_strings.LBL_CHECK_ATTACHMENTS, mod_strings.LBL_HAS_ATTACHMENTS, function(btn){
				    if (btn != 'yes'){
				        basket.innerHTML = '';
				    }
				});
        	}
            for(i in result) {
                if(typeof result[i] == 'object') {
                    var index = SUGAR.email2.composeLayout.addTemplateAttachmentField(idx);
                    var bean = result[i];

                    document.getElementById('templateAttachmentId' + idx + index).value = bean['id'];
                    document.getElementById('templateAttachmentName' + idx + index).innerHTML += bean['filename'];
                }
            }
        }
    },

    /**
     * fills drop-down values for email templates and signatures
     */
    setComposeOptions : function(idx) {
        // send from accounts
        var addressFrom = document.getElementById('addressFrom' + idx);
        
        if (addressFrom.options.length <= 0) {
        	SUGAR.email2.util.emptySelectOptions(addressFrom);
	        var fromAccountOpts = SUGAR.email2.composeLayout.fromAccounts;
	        for (id = 0 ; id < fromAccountOpts.length ; id++) {
	              var key = fromAccountOpts[id].value;
	              var display = fromAccountOpts[id].text;
	              var opt = new Option(display, key);
	              addressFrom.options.add(opt);
	        }
        }

        // email templates
        var et = document.getElementById('email_template' + idx);
        SUGAR.email2.util.emptySelectOptions(et);

        for(var key in this.emailTemplates) { // iterate through assoc array
            var display = this.emailTemplates[key];
            var opt = new Option(display, key);
            et.options.add(opt);
        }














        // signatures
        var sigs = document.getElementById('signatures' + idx);
        SUGAR.email2.util.emptySelectOptions(sigs);

        for(var key in this.signatures) { // iterate through assoc array
            var display = this.signatures[key];
            var opt = new Option(display, key);

            if(key == SUGAR.email2.userPrefs.signatures.signature_default) {
                opt.selected = true;
            }

            sigs.options.add(opt);
        }

        // character set
        var charset = document.getElementById('charsetOptions' + idx);
        for(var key in this.charsets) { // iterate through assoc array
            var display = this.charsets[key];
            var opt = new Option(display, key);

            if(key == SUGAR.email2.userPrefs.emailSettings.defaultOutboundCharset) {
                opt.selected = true;
            }

            charset.options.add(opt);
        }

        // html/plain email?
        var htmlEmail = document.getElementById('setEditor' + idx);
        if(SUGAR.email2.userPrefs.emailSettings.sendPlainText != 1) {
            htmlEmail.checked = true;
        }

        // save sent email on Sugar?
        var saveOnSugar = document.getElementById('saveOutbound' + idx);
        if(SUGAR.email2.userPrefs.emailSettings.alwaysSaveOutbound == "1") {
            saveOnSugar.checked = true;
        }

        SUGAR.email2.tinyInstances[SUGAR.email2.tinyInstances.currentHtmleditor].ready = true;
    },

    /**
     * After compose screen is rendered, async call to get email body from Sugar
     */
    replyForwardEmailStage2 : function() {
        SUGAR.email2.util.clearHiddenFieldValues('emailUIForm');
        overlay(app_strings.LBL_EMAIL_RETRIEVING_MESSAGE, app_strings.LBL_EMAIL_ONE_MOMENT);

        var ieId = SUGAR.email2.composeLayout.replyForwardObj.ieId;
        var uid = SUGAR.email2.composeLayout.replyForwardObj.uid;
        var mbox = SUGAR.email2.composeLayout.replyForwardObj.mbox;
        var type = SUGAR.email2.composeLayout.replyForwardObj.type;
        var idx = SUGAR.email2.composeLayout.currentInstanceId;

        var sugarEmail = (SUGAR.email2.composeLayout.replyForwardObj.sugarEmail) ? '&sugarEmail=true' : "";

        document.getElementById('emailSubject' + idx).value = type;
        document.getElementById('emailUIAction').value = 'composeEmail';
        document.getElementById('composeType').value = type;
        document.getElementById('ieId').value = ieId;
        document.getElementById('uid').value = uid;
        document.getElementById('mbox').value = mbox;

        var formObject = document.getElementById('emailUIForm');
        YAHOO.util.Connect.setForm(formObject);

        var sendType = type;
        AjaxObject.startRequest(callbackReplyForward, urlStandard + "&composeType=" + type + sugarEmail);
    }
};

////    END SUGAR.email2.composeLayout
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
////    CONTEXT MENU CALLS
SUGAR.email2.contextMenus = {
    /**
     * Archives from context menu
     * @param Array uids
     * @param string ieId
     * @param string folder
     */
    _archiveToSugar : function(uids, ieId, folder) {
        var ser = '';

        for(var i=0; i<uids.length; i++) { // using 1 index b/c getSelectedRowIds doubles the first row's id
            if(ser != "") ser += app_strings.LBL_EMAIL_DELIMITER;
            ser += uids[i];
        }
        AjaxObject.startRequest(callbackImportOneEmail, urlStandard + '&emailUIAction=getImportForm&uid=' + ser + "&ieId=" + ieId + "&mbox=" + folder);
    },

    /**
     * Archives from context menu
     */
    archiveToSugar : function(menuItem) {
        SUGAR.email2.contextMenus.emailListContextMenu.hide();
        
        var rows = SUGAR.email2.grid.getSelections();
        var uids = [];
        /* iterate through available rows JIC a row is deleted - use first available */
        for(var i=0; i<rows.length; i++) {
            uids[i] = rows[i].data.uid;
        }
        SUGAR.email2.contextMenus._archiveToSugar(uids, rows[0].data.ieId, rows[0].data.mbox);
    },
    
    /**
     * Popup the printable version and start system's print function.
     */
    viewPrintable : function(menuItem) {
    	var rows = SUGAR.email2.grid.getSelections();
    	SUGAR.email2.detailView.viewPrintable(rows[0].data.ieId, rows[0].data.uid, rows[0].data.mbox);
    },

    /**
     * Marks email flagged on mail server
     */
    markRead : function(type, contextMenuId) {
        SUGAR.email2.contextMenus.markEmail('read');
    },

    /**
     * Assign this emails to people based on assignment rules
     */
    assignEmailsTo : function(type, contextMenuId) {
        SUGAR.email2.contextMenus.showAssignmentDialog();
    },
    
    /**
     * Marks email flagged on mail server
     */
    markFlagged : function(contextMenuId) {
        SUGAR.email2.contextMenus.markEmail('flagged');
    },

    /**
     * Marks email unflagged on mail server
     */
    markUnflagged : function(contextMenuId) {
        SUGAR.email2.contextMenus.markEmail('unflagged');
    },

    /**
     * Marks email unread on mail server
     */
    markUnread : function() {
        SUGAR.email2.contextMenus.markEmail('unread');
    },

    /**
     * Deletes an email from context menu
     */
    markDeleted : function() {
    	if(confirm(app_strings.LBL_EMAIL_DELETE_CONFIRM)) {
        	document.getElementById('_blank').innerHTML = "";
        	SUGAR.email2.contextMenus.markEmail('deleted');
    	}
    },

    /**
     * generic call API to apply a flag to emails on the server and on sugar
     * @param string type "read" | "unread" | "flagged" | "deleted"
     */
    markEmail : function(type) {
        SUGAR.email2.contextMenus.emailListContextMenu.hide();
        overlay(app_strings.LBL_EMAIL_PERFORMING_TASK, app_strings.LBL_EMAIL_ONE_MOMENT);

        //var dm = SUGAR.email2.grid.getDataSource();
        //var uids = SUGAR.email2.grid.getSelectedRowIds();
        //var indexes = SUGAR.email2.grid.getSelectedRowIndexes();
        var rows = SUGAR.email2.grid.getSelections();
        var ser = [ ];

        for(var i=0; i<rows.length; i++) {
            ser.push(rows[i].data.uid);
        }

        ser = JSON.stringifyNoSecurity(ser);
            
        var ieId = rows[0].data.ieId;
        var folder = rows[0].data.mbox;


        var count = 0;
        if(type == 'read' || type == 'deleted') {
            // mark read
            for(var j=0; j<rows.length; j++) {
                if(rows[j].data.seen == '0') { 
                    count = count + 1;
                    rows[j].data.seen = 1;
                }
            }

            var node = SUGAR.email2.folders.getNodeFromIeIdAndMailbox(ieId, folder);
            var unseenCount = node.attributes.unseen;
            var finalCount = parseInt(unseenCount) - count;
            node.attributes.unseen = finalCount;

            SUGAR.email2.accounts.renderTree();
        } else if(type == 'unread') {
            // mark unread
            for(var j=0; j<rows.length; j++) {
                if(rows[j].data.seen == '1') { // index [9] is the seen flag
                    count = count + 1;
                }
            }

            var node = SUGAR.email2.folders.getNodeFromIeIdAndMailbox(ieId, folder);
            var unseenCount = node.attributes.unseen;
            var finalCount = parseInt(unseenCount) + count;
            node.attributes.unseen = finalCount;
            SUGAR.email2.accounts.renderTree();
        }

        if (type == 'unread') {
	        for(var i=0; i<rows.length; i++) {
	            SUGAR.email2.cache[folder + rows[i].data.uid] = null;
	        } // for
        }
        AjaxObject.startRequest(callbackContextmenus.markUnread, urlStandard + '&emailUIAction=markEmail&type=' + type + '&uids=' + ser + "&ieId=" + ieId + "&folder=" + folder);
    },

    /**
     * refreshes the ListView to show changes to cache
     */
    markEmailCleanup : function() {
        SUGAR.email2.accounts.renderTree();
        hideOverlay();
        SUGAR.email2.grid.getDataSource().reload();
    },

	showAssignmentDialog : function() {
		if (SUGAR.email2.contextMenus.assignmentDialog == null) {
			AjaxObject.startRequest(callbackAssignmentDialog, urlStandard + '&emailUIAction=getAssignmentDialogContent');
		} else {
			SUGAR.email2.contextMenus.assignmentDialog.show();
		} // else
	},
	
	/**
     * shows the import dialog with only relate visible.
     */
    relateTo : function() {
        SUGAR.email2.contextMenus.emailListContextMenu.hide();
        
        var rows = SUGAR.email2.grid.getSelections();
        var ieId = rows[0].data.ieId;
        var folder = rows[0].data.mbox;
        var uids = [];
        /* iterate through available rows JIC a row is deleted - use first available */
        for(var i=0; i<rows.length; i++) {
            uids[i] = rows[i].data.uid;
        }
        var ser = JSON.stringifyNoSecurity(uids);
        
        AjaxObject.startRequest(callbackRelateEmail, urlStandard + '&emailUIAction=getRelateForm&uid=' + ser + "&ieId=" + ieId + "&mbox=" + folder);
    },

	/**
     * shows the import dialog with only relate visible.
     */
    showDetailView : function() {
        SUGAR.email2.contextMenus.emailListContextMenu.hide();
        var rows = SUGAR.email2.grid.getSelections();
        if (rows.length > 1) {
        	alert(app_strings.LBL_EMAIL_SELECT_ONE_RECORD);
        	return;
        }
        var ieId = rows[0].data.ieId;
        var folder = rows[0].data.mbox;
        /* iterate through available rows JIC a row is deleted - use first available */
        var uid = rows[0].data.uid;
        SUGAR.email2.contextMenus.showEmailDetailViewInPopup(ieId, uid, folder);
    },
    
    /**
     *
     */
    showEmailDetailViewInPopup : function(ieId,uid, folder) {
        overlay(app_strings.LBL_EMAIL_RETRIEVING_RECORD, app_strings.LBL_EMAIL_ONE_MOMENT);
        AjaxObject.startRequest(callbackEmailDetailView, urlStandard + '&emailUIAction=getEmail2DetailView&uid=' + uid + "&ieId=" + ieId + "&mbox=" + folder + "&record=" + uid);
    },
    
    /**
     * Opens multiple messages from ListView context click
     */
    openMultiple : function() {
        SUGAR.email2.contextMenus.emailListContextMenu.hide();

        var rows = SUGAR.email2.grid.getSelections();
        var uids = SUGAR.email2.listView.getUidsFromSelection();

        if(uids.length > 0) {
            var mbox = rows[0].data.mbox;
            var ieId = rows[0].data.ieId;
            SUGAR.email2.detailView.populateDetailViewMultiple(uids, mbox, ieId, true);
        }
    },

    /**
     * Replies/forwards email
     */
    replyForwardEmailContext : function(menuItem) {
        SUGAR.email2.contextMenus.emailListContextMenu.hide();

        var indexes = SUGAR.email2.grid.getSelections();
        //var dm = SUGAR.email2.grid.getDataModel();
        var type = menuItem.id;

        for(var i=0; i<indexes.length; i++) {
            var row = indexes[i].data;
            SUGAR.email2.composeLayout.c0_replyForwardEmail(row.ieId, row.uid, row.mbox, type);
        }
    },
    
    //show menu functions
    showEmailsListMenu : function(grid, rowIndex, event) {
       event.stopEvent();
       var row = grid.getDataSource().getAt(rowIndex);
       var draft = (row.data.type == "draft");
       var menu = SUGAR.email2.contextMenus.emailListContextMenu;
       var folderNode;
       if (row.data.mbox == 'sugar::Emails') {
       	folderNode = SUGAR.email2.folders.getNodeFromIeIdAndMailbox('folder', row.data.ieId);
       } else {
       	folderNode = SUGAR.email2.folders.getNodeFromIeIdAndMailbox(row.data.ieId, row.data.mbox);
       }
       if (folderNode != null && 
          ((folderNode.attributes.is_group != null) && 
          (folderNode.attributes.is_group == 'true')) ||
          (folderNode.attributes.isGroup != null && folderNode.attributes.isGroup == "true")){
           menu.items.items[9]['show']();
       } else {
           menu.items.items[9]['hide']();
       }
       
       menu.items.items[2][draft ? 'hide' : 'show']();
       menu.items.items[3][draft ? 'hide' : 'show']();
       menu.items.items[4][draft ? 'hide' : 'show']();
       menu.items.items[5][draft ? 'hide' : 'show']();
       menu.items.items[8][draft ? 'hide' : 'show']();
       
       if (row.data.mbox == "sugar::Emails") {
           menu.items.items[2].hide();
           menu.items.items[0].show();
           menu.items.items[10].show();
       } else {
           menu.items.items[0].hide();
           menu.items.items[10].hide();
       }
       var coords = event.getXY();
       SUGAR.email2.contextMenus.emailListContextMenu.showAt([coords[0], coords[1]]);
    },
    
    showFolderMenu : function(grid, rowIndex, event) {
       event.stopEvent();
       var coords = event.getXY();
       SUGAR.email2.contextMenus.emailListContextMenu.showAt([coords[0], coords[1]]);
    }
};

SUGAR.email2.contextMenus.dv = {
    archiveToSugar : function(contextMenuId) {

        SUGAR.email2.contextMenus._archiveToSugar(uids, ieId, folder);
    },

    replyForwardEmailContext : function(all) {
        SUGAR.email2.contextMenus.detailViewContextMenu.hide();
        debugger;
    }

};





////    END SUGAR.email2.contextMenus
///////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////
////    DETAIL VIEW
SUGAR.email2.detailView = {
    consumeMetaDetail : function(ret) {
        // handling if the Email drafts
        if(ret.type == 'draft') {
            SUGAR.email2.composeLayout.c0_composeDraft(); 
            return;
        }
        

        // cache contents browser-side
        SUGAR.email2._setDetailCache(ret);

        var content = SUGAR.email2.innerLayout.regions.center.getPanel(targetDiv.id);
        var displayTemplate = new Ext.DomHelper.Template(SUGAR.email2.templates['displayOneEmail']);
        // 2 below must be in global context
        meta = ret.meta;
        meta['panelId'] = SUGAR.email2.util.getPanelId();

        email = ret.meta.email;
        var out = displayTemplate.applyTemplate({
            'app_strings' : app_strings,
            'theme' : theme,
            'idx' : targetDiv.id,
            'meta' : meta,
            'email' : meta.email,
            'linkBeans' : linkBeans
        });
        content.setTitle(meta.email.name);
        content.setContent(out);
        
        var displayFrame = document.getElementById('displayEmailFrame' + targetDiv.id);
        displayFrame.contentWindow.document.write(email.description);
        displayFrame.contentWindow.document.close();
        
        content.el.dom.style.height = "100%";

        // hide archive links
        if(ret.meta.is_sugarEmail) {
			document.getElementById("archiveEmail" + targetDiv.id).innerHTML = "&nbsp;";
			if (viewRawEmail == 'false') {
				document.getElementById("viewMenuSpan" + meta['panelId']).innerHTML = "&nbsp";
			}
        } else {
            document.getElementById("showDeialViewForEmail" + targetDiv.id).innerHTML = "&nbsp;";
        } // else
        
    },

    consumeMetaPreview : function(ret) {
        // cache contents browser-side
        SUGAR.email2._setDetailCache(ret);
        
        
        
        var currrow = SUGAR.email2.grid.getSelectionModel().getSelected();
        if (!currrow) {
            document.getElementById('_blank').innerHTML = '';
            return;
        }
        // handling if the Email drafts
        if(ret.type == 'draft'){
            if (currrow.id == ret.uid) {
                SUGAR.email2.composeLayout.c0_composeDraft();
            }
            return;
        }
        
        if (currrow.data.uid != ret.meta.uid) {
           return;
        }
                
        // remove loading sprite
        document.getElementById('_blank').innerHTML = '';
        
        var displayTemplate = new Ext.DomHelper.Template(SUGAR.email2.templates['displayOneEmail']);
        meta = ret.meta;
        meta['panelId'] = SUGAR.email2.util.getPanelId();
        email = ret.meta.email;

        displayTemplate.append('_blank', {
            'app_strings' : app_strings,
            'theme' : theme,
            'idx' : 'Preview',
            'meta' : meta,
            'email' :meta.email,
            'linkBeans' : linkBeans
        });
        
        var displayFrame = document.getElementById('displayEmailFramePreview');
        displayFrame.contentWindow.document.write(email.description);
        displayFrame.contentWindow.document.close();

        // hide archive links
        if(ret.meta.is_sugarEmail) {
            document.getElementById("archiveEmailPreview").innerHTML = "&nbsp;";
			if (viewRawEmail == 'false') {
				document.getElementById("viewMenuSpan" + meta['panelId']).innerHTML = "&nbsp";
			}
        } else {
          //mark email as read
            document.getElementById("showDeialViewForEmail" + meta['panelId']).innerHTML = "&nbsp;";
        }

        // put focus back on Grid
        
        if(!SUGAR.email2.grid.getSelectionModel().isLocked()){
            //SUGAR.email2.grid.getSelectionModel().selectRow(row);
            //SUGAR.email2.grid.getSelectionModel().setRowState(row, true, true);
        }
    },

    /**
     * wraps emailDelete() for single messages, comes from preview or tab
     */
    emailDeleteSingle : function(ieId, uid, mbox) {
        if(confirm(app_strings.LBL_EMAIL_DELETE_CONFIRM)) {
            // find active panel
            var activePanel = SUGAR.email2.innerLayout.regions.center.getActivePanel();

            if(activePanel.el.id != 'listViewLayout') {
                SUGAR.email2.innerLayout.regions.center.remove(activePanel);
            }
            document.getElementById('_blank').innerHTML = "";
	        var ser = [ ];
			ser.push(uid);
	        uid = JSON.stringifyNoSecurity(ser);
            this.emailDelete(ieId, uid, mbox);
        }
    },

    /**
     * Sends async call to delete a given message
     * @param
     */
    emailDelete : function(ieId, uid, mbox) {
       overlay(app_strings.LBL_EMAIL_DELETING_MESSAGE, app_strings.LBL_EMAIL_ONE_MOMENT);
       AjaxObject.startRequest(callbackContextmenus.markUnread, urlStandard + '&emailUIAction=markEmail&type=deleted&uids=' + 
           uid + "&ieId=" + ieId + "&folder=" + mbox);
    },

    /**
     * retrieves one email to display in the preview pane.
     */
    getEmailPreview : function() {
	   var row = SUGAR.email2.grid.getSelectionModel().getSelected();
	   if (row.data && !(!SUGAR.email2.contextMenus.emailListContextMenu.hidden && row.data.type =='draft')) {
	       var setRead = (row.data['seen'] == 0) ? true : false;
	       SUGAR.email2.listView.markRead(SUGAR.email2.listView.currentRowIndex, row);
	       SUGAR.email2.detailView.populateDetailView(row.data['uid'], row.data['mbox'], row.data['ieId'], setRead, SUGAR.email2.previewLayout);
	    }
    },

    /**
     * Imports one email into Sugar
     */
    importEmail : function(ieId, uid, mbox) {
        SUGAR.email2.util.clearHiddenFieldValues('emailUIForm');

        overlay(app_strings.LBL_EMAIL_IMPORTING_EMAIL, app_strings.LBL_EMAIL_ONE_MOMENT);

        var vars = "&ieId=" + ieId + "&uid=" + uid + "&mbox=" + mbox;
        AjaxObject.target = '';
        AjaxObject.startRequest(callbackImportOneEmail, urlStandard + '&emailUIAction=getImportForm' + vars);
    },

    /**
     * Populates the frameFlex div with the contents of an email
     */
    populateDetailView : function(uid, mbox, ieId, setRead, destination) {
        SUGAR.email2.util.clearHiddenFieldValues('emailUIForm');

        var mboxStr = new String(mbox);
        var compKey = mbox + uid;

        if(mboxStr.substring(0,7) == 'sugar::') {
            // display an email from Sugar
            document.getElementById('emailUIAction').value = 'getSingleMessageFromSugar';
        } else {
            // display an email from an email server
            document.getElementById('emailUIAction').value = 'getSingleMessage';
        }
        document.getElementById('mbox').value = mbox;
        document.getElementById('ieId').value = ieId;
        document.getElementById('uid').value = uid;

        YAHOO.util.Connect.setForm(document.getElementById('emailUIForm'));

        AjaxObject.forceAbort = true;
        AjaxObject.target = '_blank';

        if(setRead == true) {
            SUGAR.email2.folders.decrementUnreadCount(ieId, mbox, 1);
        }

        if(destination.id == SUGAR.email2.innerLayout.id) {
            /*
             * loading email into a tab, peer with ListView
             * targetDiv must remain in the global namespace as it is used by AjaxObject
             */
            targetDiv = Ext.DomHelper.append(document.body, {
                tag : 'div',
                name : 'detailViewTarget',
                style : 'overflow:hidden',
                cls : 'ylayout-inactive-content'
            });

            var content = new Ext.ContentPanel(targetDiv, {title:loadingSprite, closable:true, autoScroll:true});

            SUGAR.email2.innerLayout.add('center', content);

            // use cache if available
            if(SUGAR.email2.cache[compKey]) {
                SUGAR.email2.detailView.consumeMetaDetail(SUGAR.email2.cache[compKey]);
            } else {
                AjaxObject.startRequest(AjaxObject.detailView.callback.emailDetail, null); // open email as peer-tab to listView
            }
        } else {
            // loading email into preview pane
            document.getElementById('_blank').innerHTML = loadingSprite;

            // use cache if available
            if(SUGAR.email2.cache[compKey]) {
                SUGAR.email2.detailView.consumeMetaPreview(SUGAR.email2.cache[compKey]);
            } else {
                AjaxObject.forceAbort = true;
                AjaxObject.startRequest(AjaxObject.detailView.callback.emailPreview, null); // open in preview window
            }
        }
    },

    /**
     * Retrieves multiple emails for DetailView
     */
    populateDetailViewMultiple : function(uids, mbox, ieId, setRead) {
        overlay(app_strings.LBL_EMAIL_RETRIEVING_MESSAGE, app_strings.LBL_EMAIL_ONE_MOMENT);
        SUGAR.email2.util.clearHiddenFieldValues('emailUIForm');

        var mboxStr = new String(mbox);

        uids = SUGAR.email2.util.cleanUids(uids);

        if(mboxStr.substring(0,7) == 'sugar::') {
            // display an email from Sugar
            document.getElementById('emailUIAction').value = 'getMultipleMessagesFromSugar';
            document.getElementById('uid').value = uids;
        } else {
            // display an email from an email server
            document.getElementById('emailUIAction').value = 'getMultipleMessages';
            document.getElementById('mbox').value = mbox;
            document.getElementById('ieId').value = ieId;
            document.getElementById('uid').value = uids;
        }

        var formObject = document.getElementById('emailUIForm');
        YAHOO.util.Connect.setForm(formObject);

        AjaxObject.target = 'frameFlex';
        AjaxObject.startRequest(callbackEmailDetailMultiple, null);

        if(setRead == true) {
            var c = uids.split(",");
            SUGAR.email2.folders.decrementUnreadCount(ieId, mbox, c.length);
        }
    },

    /**
     * Makes async call to get QuickCreate form
     * Renders a modal edit view for a given module
     */
    quickCreate : function(module, ieId, uid, mailbox) {
        var get = "&qc_module=" + module + "&ieId=" + ieId + "&uid=" + uid + "&mailbox=" + mailbox;

        if(ieId == null || ieId == "null" || mailbox == 'sugar::Emails') {
            get += "&sugarEmail=true";
        }
        
        AjaxObject.startRequest(callbackQuickCreate, urlStandard + '&emailUIAction=getQuickCreateForm' + get);
    },

    /**
     * Makes async call to save a quick create
     * @param bool
     */
    saveQuickCreate : function(action) {
        var qcd = SUGAR.email2.detailView.quickCreateDialog;
        if (check_form('form_EmailQCView_' + qcd.qcmodule)) {
	        var formObject = document.getElementById('form_EmailQCView_' + qcd.qcmodule);
	        var theCallback = callbackQuickCreateSave;
	        var accountType = '&sugarEmail=true';
	        if (qcd.ieId != 'null' && qcd.mbox != 'sugar::Emails') {
	           accountType = '&ieId=' + qcd.ieId;
	        }
	        
            if (action == 'reply') {
	           theCallback = callbackQuickCreateSaveAndReply;
	        } else if (action == true) {
	            theCallback = callbackQuickCreateSaveAndAddToAddressBook;
	        }
	        formObject.action.value = 'EmailUIAjax';
	        YAHOO.util.Connect.setForm(formObject);
	        overlay('Saving', app_strings.LBL_EMAIL_ONE_MOMENT);
	        AjaxObject.startRequest(theCallback, "to_pdf=true&emailUIAction=saveQuickCreate&qcmodule=" + qcd.qcmodule + '&uid=' + qcd.uid +
	                               accountType + '&mbox=' + qcd.mbox);
        }
    },

    /**
     * Code to show/hide long list of email address in DetailView
     */
    showCroppedEmailList : function(el) {
        el.style.display = 'none';
        el.previousSibling.style.display = 'inline'
    },
    showFullEmailList : function(el) {
        el.style.display = 'none';
        el.nextSibling.style.display = 'inline';
    },

    /**
     * Shows the QuickCreate overlay
     * @param string ieId
     * @param string uid
     * @param string mailbox
     */
    showQuickCreate : function(ieId, uid, mailbox) {
        var panelId = SUGAR.email2.util.getPanelId();
        var context = document.getElementById("quickCreateSpan" + panelId);
        var qcitemdata = [];
        for (var i=0; i < this.qcmodules.length; i++) {
            var module = this.qcmodules[i];
            qcitemdata.push({
                text:   app_strings['LBL_EMAIL_QC_' + module.toUpperCase()],
                modulename: module,
                handler: function() {SUGAR.email2.detailView.quickCreate(this.modulename, ieId, uid, mailbox);}
            });
        }
		var menu = new Ext.menu.Menu({items : qcitemdata});
        //#23108 jchi@07/17/2008
        menu.render('quickCreateSpan'+ panelId);
        this.quickCreateMenu = menu;
        this.quickCreateMenu.show(context);
    },

    /**
     * Displays the "View" submenu in the detailView
     * @param string ieId
     * @param string uid
     * @param string mailbox
     */
    showViewMenu : function(ieId, uid, mailbox) {
        var panelId = SUGAR.email2.util.getPanelId();
        var context = document.getElementById("viewMenuSpan" + panelId);
		var menu = new Ext.menu.Menu({items : [
	            {
                    text: app_strings.LBL_EMAIL_VIEW_HEADERS,
                    handler: function() {SUGAR.email2.detailView.viewHeaders(ieId, uid, mailbox);}
                },
                {
                    text: app_strings.LBL_EMAIL_VIEW_RAW,
                    handler: function() {SUGAR.email2.detailView.viewRaw(ieId, uid, mailbox);}
                }]}
        );

        if (ieId == 'null' || ieId == null) {
			menu = new Ext.menu.Menu({items : [
                {
                    text: app_strings.LBL_EMAIL_VIEW_RAW,
                    handler: function() {SUGAR.email2.detailView.viewRaw(ieId, uid, mailbox);}
                }]});
        }
        //#23108 jchi@07/17/2008
        menu.render('quickCreateSpan'+ panelId);
        this.viewMenu = menu;
        this.viewMenu.show(context);
    },
    /**
     * Makes async call to get an email's headers
     */
    viewHeaders : function(ieId, uid, mailbox) {
        this.viewMenu.hide();
        var get = "&type=headers&ieId=" + ieId + "&uid=" + uid + "&mailbox=" + mailbox;
        AjaxObject.startRequest(AjaxObject.detailView.callback.viewRaw, urlStandard + "&emailUIAction=displayView" + get);
    },

    /**
     * Makes async call to get a printable version
     */
    viewPrintable : function(ieId, uid, mailbox) {
        if(mailbox == 'sugar::Emails') {
            // display an email from Sugar
            var emailUIAction = '&emailUIAction=getSingleMessageFromSugar';
        } else {
            // display an email from an email server
            var emailUIAction = '&emailUIAction=getSingleMessage';
        }

        var get = "&type=printable&ieId=" + ieId + "&uid=" + uid + "&mbox=" + mailbox;
        AjaxObject.startRequest(AjaxObject.detailView.callback.viewPrint, urlStandard + emailUIAction + get);
    },

    /**
     * Makes async call to get an email's raw source
     */
    viewRaw : function(ieId, uid, mailbox) {
        SUGAR.email2.detailView.viewMenu.hide();
        var get = "&type=raw&ieId=" + ieId + "&uid=" + uid + "&mailbox=" + mailbox;
        AjaxObject.startRequest(AjaxObject.detailView.callback.viewRaw, urlStandard + "&emailUIAction=displayView" + get);
    }
};
////    END SUGAR.email2.detailView
///////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////
////    SUGAR.email2.folders
SUGAR.email2.folders = {
    contextMenuFocus : new Object(),

    /**
     * Generates a standardized identifier that allows reconstruction of I-E ID-folder strings or
     * SugarFolder ID - folder strings
     */
    _createFolderId : function(node) {
        var ret = '';

        if(!node.data.id)
            return ret;

        if(node.data.ieId) {
            /* we have a local Sugar folder */
            if(node.data.ieId == 'folder') {
                ret = "sugar::" + node.data.id; // FYI: folder_id is also stored in mbox field
            } else if(node.data.ieId.match(SUGAR.email2.reGUID)) {
                ret = "remote::" + node.data.ieId + "::" + node.data.mbox.substr(node.data.mbox.indexOf("INBOX"), node.data.mbox.length);
            }
        } else {
            ret = node.data.id;
        }

        return ret;
    },

    addChildNode : function(parentNode, childNode) {
        var is_group = (childNode.properties.is_group == 'true') ? 1 : 0;
        var is_dynamic = (childNode.properties.is_dynamic == 'true') ? 1 : 0;
        var node = this.buildTreeViewNode(childNode.label, childNode.properties.id, is_group, is_dynamic, childNode.properties.unseen, parentNode, childNode.expanded);

        if(childNode.nodes) {
            if(childNode.nodes.length > 0) {
                for(j=0; j<childNode.nodes.length; j++) {
                    var newChildNode = childNode.nodes[j];
                    this.addChildNode(node, newChildNode);
                }
            }
        }
    },

    /**
     * Handles Group Folder adds via Settings->Folders (only admins)
     */
    addNewGroupFolder : function() {
        if(document.getElementById('groupFolderAddName').value == "") {
            alert(app_strings.LBL_EMAIL_ERROR_ADD_GROUP_FOLDER);
            return false;
        }

        if(this.isUniqueFolderName(document.getElementById('groupFolderAddName').value)) {
            overlay(app_strings.LBL_EMAIL_SETTINGS_GROUP_FOLDERS_CREATE, app_strings.LBL_EMAIL_ONE_MOMENT);
            var get = '';
            var groupFolderAddName = document.getElementById('groupFolderAddName').value;
            var groupFoldersAdd = document.getElementById('groupFoldersAdd').value;
            var get = "&name=" + groupFolderAddName + "&parent_folder=" + groupFoldersAdd + "&group_id=" + '';




            AjaxObject.startRequest(callbackAddGroupFolderFrom, urlStandard + '&emailUIAction=addGroupFolder' + get);
        } else {
            alert(app_strings.LBL_EMAIL_ERROR_DUPE_FOLDER_NAME);
            document.getElementById('groupFolderAddName').focus();
            return;
        }
    },

    saveGroupFolder : function() {
        if(document.getElementById('groupFolderAddName').value == "") {
            alert(app_strings.LBL_EMAIL_ERROR_ADD_GROUP_FOLDER);
            return false;
        }
        overlay(app_strings.LBL_EMAIL_SETTINGS_GROUP_FOLDERS_Save, app_strings.LBL_EMAIL_ONE_MOMENT);
        var get = '';
        var groupFolderAddName = document.getElementById('groupFolderAddName').value;
        var groupFoldersAdd = document.getElementById('groupFoldersAdd').value;
        var get = "&name=" + groupFolderAddName + "&parent_folder=" + groupFoldersAdd + "&group_id=" + '';



        var editGroupFolderList = document.getElementById('editGroupFolderList');
        var groupFolderIndex = editGroupFolderList.selectedIndex;
		get += "&record=" + editGroupFolderList.options[groupFolderIndex].value;
        AjaxObject.startRequest(callbackSaveGroupFolderFrom, urlStandard + '&emailUIAction=saveGroupFolder' + get);
    	
    },
    /**
     * Handles Editing of a Group Folder
     */
    editGroupFolder : function(folderId) {
        if(folderId == '') {
            document.getElementById('groupFolderAddName').value = '';
            document.getElementById('editGroupFolderList').options[0].selected = true;
            var groupFoldersAdd = document.getElementById('groupFoldersAdd');
			SUGAR.email2.util.emptySelectOptions(groupFoldersAdd);
			var grp = document.getElementById('groupFolders');
			for(i=0; i<grp.options.length; i++) {
				groupFoldersAdd.options.add(new Option(grp.options[i].text, grp.options[i].value));
			}
            groupFoldersAdd.options[0].selected = true;
			document.getElementById('addNewFolders').style.display = '';
			document.getElementById('saveGroupFolder').style.display = 'none';
			document.getElementById('cancelEditGroupFolder').style.display = 'none';
            
        } else {
			document.getElementById('addNewFolders').style.display = 'none';
			document.getElementById('saveGroupFolder').style.display = '';
			document.getElementById('cancelEditGroupFolder').style.display = '';
	        overlay(app_strings.LBL_EMAIL_SETTINGS_RETRIEVING_GROUP, app_strings.LBL_EMAIL_ONE_MOMENT);
	        get = "&folderId=" + folderId;
            AjaxObject.startRequest(callbackEditGroupFolder, urlStandard + '&emailUIAction=getGroupFolder' + get);
	
	        //var formObject = document.getElementById('ieSelect');
	        //formObject.emailUIAction.value = 'getIeAccount';
	
	        //YAHOO.util.Connect.setForm(formObject);
	
	        //AjaxObject.startRequest(callbackIeAccountRetrieve, null);
        } // else
    },
    /**
     * Builds and returns a new TreeView Node
     * @param string name
     * @param string id
     * @param int is_group
     * @return object
     */
    buildTreeViewNode : function(name, id, is_group, is_dynamic, unseen, parentNode, expanded) {
        var node = new YAHOO.widget.TextNode(name, parentNode, true);

        //node.href = " SUGAR.email2.listView.populateListFrameSugarFolder(YAHOO.namespace('frameFolders').selectednode, '" + id + "', 'false');";
        node.expanded = expanded;
        node.data = new Object;
        node.data['id'] = id;
        node.data['mbox'] = id; // to support DD imports into BRAND NEW folders
        node.data['label'] = name;
        node.data['ieId'] = 'folder';
        node.data['isGroup'] = (is_group == 1) ? 'true' : 'false';
        node.data['isDynamic'] = (is_dynamic == 1) ? 'true' : 'false';
        node.data['unseen'] = unseen;
        return node;
    },

    /**
     * ensures that a new folder has a valid name
     */
    checkFolderName : function(name) {
        if(name == "")
            return false;

        this.folderAdd(name);
    },

    /**
     * Pings email servers for new email - forces refresh of folder pane
     */
    checkEmailAccounts : function() {
        this.checkEmailAccountsSilent(true);
    },

    checkEmailAccountsSilent : function(showOverlay) {
        if(typeof(SUGAR.email2.folders.checkingMail)) {
            clearTimeout(SUGAR.email2.folders.checkingMail);
        }

        // don't stomp an on-going request
        if(AjaxObject.currentRequestObject.conn == null) {
            if(showOverlay) {
                overlay(app_strings.LBL_EMAIL_CHECKING_NEW,
                      app_strings.LBL_EMAIL_ONE_MOMENT + "<br>&nbsp;<br><i>" + app_strings.LBL_EMAIL_CHECKING_DESC + "</i>");
            }
            AjaxObject.startRequest(AjaxObject.folders.callback.checkMail, urlStandard + '&emailUIAction=checkEmail&all=true');
        } else {
            // wait 5 secs before trying again.
            SUGAR.email2.folders.checkingMail = setTimeout("SUGAR.email2.folders.checkEmailAccountsSilent(false);", 5000);
        }
    },
    
    /**
     * Starts check of all email Accounts using a loading bar for large POP accounts
     */
    startEmailAccountCheck : function() {
        // don't do two checks at the same time
        if(AjaxObject.currentRequestObject.conn == null) {
            overlay(app_strings.LBL_EMAIL_ONE_MOMENT, app_strings.LBL_EMAIL_CHECKING_NEW, 'progress');
            SUGAR.email2.accounts.ieIds = SUGAR.email2.folders.getIeIds();
            if (SUGAR.email2.accounts.ieIds.length > 0) {
            AjaxObject.startRequest(AjaxObject.accounts.callbackCheckMailProgress, urlStandard + 
                                '&emailUIAction=checkEmailProgress&ieId=' + SUGAR.email2.accounts.ieIds[0] + "&currentCount=0");
            } else {
               hideOverlay();
            }
        } else {
            // wait 5 secs before trying again.
            SUGAR.email2.folders.checkingMail = setTimeout("SUGAR.email2.folders.startEmailAccountCheck();", 5000);
        }
    },
    
    /**
     * Checks a single Account check based on passed ieId
     */
     startEmailCheckOneAccount : function(ieId, synch) {
            if (synch) {
                synch = true;
            } else {
                synch = false;
            }
            var mbox = "";
            var node = SUGAR.email2.clickedFolderNode;
            if (node && !synch) {
            	mbox = node.attributes.mbox;
            } // if
            overlay(app_strings.LBL_EMAIL_ONE_MOMENT, app_strings.LBL_EMAIL_CHECKING_DESC, 'progress');
            SUGAR.email2.accounts.ieIds = [ieId];
            AjaxObject.startRequest(AjaxObject.accounts.callbackCheckMailProgress, urlStandard + 
                                '&emailUIAction=checkEmailProgress&mbox=' + mbox + '&ieId=' + ieId + "&currentCount=0&synch=" + synch);
      },


    /**
     * Empties trash for subscribed accounts
     */
    emptyTrash : function() {
        SUGAR.email2.contextMenus.frameFoldersContextMenu.hide();
        overlay(app_strings.LBL_EMAIL_EMPTYING_TRASH, app_strings.LBL_EMAIL_ONE_MOMENT);
        AjaxObject.startRequest(callbackEmptyTrash, urlStandard + '&emailUIAction=emptyTrash');
    },
    
    /**
     * Clears Cache files of the inboundemail account
     */
    clearCacheFiles : function(ieId) {
        SUGAR.email2.contextMenus.frameFoldersContextMenu.hide();
        overlay(app_strings.LBL_EMAIL_CLEARING_CACHE_FILES, app_strings.LBL_EMAIL_ONE_MOMENT);
        AjaxObject.startRequest(callbackClearCacheFiles, urlStandard + '&ieId=' + ieId + '&emailUIAction=clearInboundAccountCache');
    },
    
    
    /**
     * Returns an array of all the active accounts in the folder view
     */
    getIeIds : function() {
         var ieIds = [];
         var root = SUGAR.email2.tree.getRootNode();
         for(i=0; i < root.childNodes.length; i++) {
           if ((root.childNodes[i].attributes.cls == "ieFolder" && root.childNodes[i].childNodes.length > 0) ||
           		(root.childNodes[i].attributes.isGroup != null && root.childNodes[i].attributes.isGroup == "true" && root.childNodes[i].childNodes.length > 0)) {
               ieIds.push(root.childNodes[i].childNodes[0].attributes.ieId);
           }
         }
         return ieIds;
     },

    /**
     * loads folder lists in Settings->Folders
     */
    lazyLoadSettings : function() {
        AjaxObject.timeout = 300000; // 5 min timeout for long checks
        AjaxObject.startRequest(callbackSettingsFolderRefresh, urlStandard + '&emailUIAction=getFoldersForSettings');
    },

    /**
     * After the add new folder is done via folders tab on seetings, this function should get called
     * It will refresh the folder list after inserting an entry on the UI to update the new folder list
     */
    loadSettingFolder : function() {
        AjaxObject.timeout = 300000; // 5 min timeout for long checks
        AjaxObject.startRequest(callbackLoadSettingFolder, urlStandard + '&emailUIAction=getFoldersForSettings');
    },
    
    /**
     * Recursively removes nodes from the TreeView of type Sugar (data.ieId = 'folder')
     */
    removeSugarFolders : function() {
        var tree = SUGAR.email2.tree;
        var root = tree.getRootNode();
        var folder = root.findChild("ieId", "folder");
        while(folder) {
            root.removeChild(folder);
            folder = root.findChild("ieId", "folder");
        }
        if (!root.childrenRendered) {
        	root.childrenRendered = true;
        }
    },
    
    rebuildFolders : function() {
       overlay(app_strings.LBL_EMAIL_REBUILDING_FOLDERS, app_strings.LBL_EMAIL_ONE_MOMENT);
       AjaxObject.startRequest(callbackFolders, urlStandard + '&emailUIAction=getAllFoldersTree');
    },

    /**
     * Updates TreeView with Sugar Folders
     */
    setSugarFolders : function(delay) {
        if (delay) {
			if (typeof(SUGAR.email2.folders.setSugarFoldersTask) == 'undefined') {
				SUGAR.email2.folders.setSugarFoldersTask = new Ext.util.DelayedTask(SUGAR.email2.folders.setSugarFolders, this); 
			}
			SUGAR.email2.folders.setSugarFoldersTask.delay(delay);
		} else {
			this.removeSugarFolders();
			AjaxObject.forceAbort = true;
			AjaxObject.startRequest(callbackRefreshSugarFolders, urlStandard + "&emailUIAction=refreshSugarFolders");
		}
    },

    /**
     * Takes async data object and creates the sugar folders in TreeView
     */
    setSugarFoldersEnd : function(o) {
        var root = SUGAR.email2.tree.getRootNode();
        for(i in o) {
            var node = o[i];
            if (node.text) {
                newNode = new Ext.tree.AsyncTreeNode(node);
                root.appendChild(newNode);
                //Epand the nodes to remove the + signs
                newNode.expand(true);
            }
        }
        SUGAR.email2.accounts.renderTree();
    },

    startCheckTimer : function() {
        if(SUGAR.email2.userPrefs.emailSettings.emailCheckInterval && SUGAR.email2.userPrefs.emailSettings.emailCheckInterval != -1) {
            var ms = SUGAR.email2.userPrefs.emailSettings.emailCheckInterval * 60 * 1000;

            if(typeof(SUGAR.email2.folders.checkTimer) != 'undefined') {
                clearTimeout(SUGAR.email2.folders.checkTimer);
            }

            SUGAR.email2.folders.checkTimer = setTimeout("SUGAR.email2.folders.checkEmailAccountsSilent(false);", ms);
        }
    },

    /**
     * makes an async call to save user preference and refresh folder view
     * @param object SELECT list object
     */
    setFolderSelection : function() {
        overlay(app_strings.LBL_EMAIL_REBUILDING_FOLDERS, app_strings.LBL_EMAIL_ONE_MOMENT);
		SUGAR.email2.search.markSearchAccountListDirty();

        document.getElementById('emailUIAction2').value = 'setFolderViewSelection';

        var formObject = document.getElementById('ieSubscribe');
        YAHOO.util.Connect.setForm(formObject);

        AjaxObject.startRequest(callbackFolders, null);
    },

    /**
     * makes async call to save user preference for a given node's open state
     * @param object node YUI TextNode object
     */
    setOpenState : function(node) {
        SUGAR.email2.util.clearHiddenFieldValues('emailUIForm');
        var nodePath = node.data.id;
        var nodeParent = node.parent;

        while(nodeParent != null) {
            // root node has no ID param
            if(nodeParent.data != null) {
                nodePath = nodeParent.data.id + "::" + nodePath;
            }

            var nodeParent = nodeParent.parent;
        }

        document.getElementById('emailUIAction').value = 'setFolderOpenState';
        document.getElementById('focusFolder').value = nodePath;

        if(node.expanded == true) {
            document.getElementById('focusFolderOpen').value = 'open';
        } else {
            document.getElementById('focusFolderOpen').value = 'closed';
        }

        var formObject = document.getElementById('emailUIForm');
        YAHOO.util.Connect.setForm(formObject);

        AjaxObject.startRequest(null, null);
    },

    getNodeFromMboxPath : function(path) {
        var tree = YAHOO.widget.TreeView.getTree('frameFolders');
        var a = JSON.parse(path);

        var node = tree.getRoot();

        var i = 0;
        while(i < a.length) {
            node = this.getChildNodeFromLabel(node, a[i]);
            i++;
        }

        return node;
    },

    getChildNodeFromLabel : function(node, nodeLabel) {
        for(i=0; i<node.children.length; i++) {
            if(node.children[i].data.id == nodeLabel) {
                return node.children[i];
            }
        }
    },

    /**
     * returns the node that presumably under the user's right-click
     */
    getNodeFromContextMenuFocus : function() {
        //// get the target(parent) node
        var tree = YAHOO.widget.TreeView.trees.frameFolders;
        var index = -1;
        var target = SUGAR.email2.contextMenus.frameFoldersContextMenu.contextEventTarget;

        // filter local folders
        if(target.className == 'localFolder' || target.className == 'groupInbox') {
            while(target && (target.className == 'localFolder' || target.className == 'groupInbox')) {
                if(target.id == '') {
                    target = target.parentNode;
                } else {
                    break;
                }
            }
        }

        var targetNode = document.getElementById(target.id);
        re = new RegExp(/ygtv[a-z]*(\d+)/i);

        try {
            var matches = re.exec(targetNode.id);
        } catch(ex) {
            return document.getElementById(ygtvlabelel1);
        }

        if(matches) {
            index = matches[1];
        } else {
            // usually parent node
            matches = re.exec(targetNode.parentNode.id);

            if(matches) {
                index = matches[1];
            }
        }

        var parentNode = (index == -1) ? tree.getNodeByProperty('id', 'Home') : tree.getNodeByIndex(index);
        parentNode.expand();

        return parentNode;
    },

    /**
     * Decrements the Unread Email count in folder text
     * @param string ieId ID to look for
     * @param string mailbox name
     * @param count how many to decrement
     */
    decrementUnreadCount : function(ieId, mbox, count) {
        if(mbox.indexOf("sugar::") === 0) {
            var node = this.getNodeFromId(ieId);
        } else {
            var node = this.getNodeFromIeIdAndMailbox(ieId, mbox);
        }
        if(node) {
            var unseen = node.attributes.unseen;
            if(unseen > 0) {
                var check = unseen - count;
                var finalCount = (check >= 0) ? check : 0;
                node.attributes.unseen = finalCount;
            }
            SUGAR.email2.accounts.renderTree();
        }
    },

    /**
     * gets the TreeView node with a given ID/ieId
     * @param string id ID to look for
     * @return object Node
     */
    getNodeFromId : function(id) {
        SUGAR.email2.folders.focusNode = null;
        SUGAR.email2.tree.root.cascade(function(ieId) {
            if (this.id == ieId || this.attributes.ieId == ieId) {
                SUGAR.email2.folders.focusNode = this;
                return false;
            }
        }, null, id);
        return SUGAR.email2.folders.focusNode;
    },

    /**
     * Uses ieId and mailbox to try to find a node in the tree
     */
    getNodeFromIeIdAndMailbox : function(id, mbox) {
		SUGAR.email2.folders.focusNode = SUGAR.email2.tree.getNodeById(id);
		if (SUGAR.email2.folders.focusNode) {
			return SUGAR.email2.folders.focusNode;
		}
        if (mbox == "sugar::Emails") {        
        	mbox = id;
        	id = "folder";
        } // if
    	var root = SUGAR.email2.tree.getRootNode();
        root.cascade(function(varsarray) {
                if (this.attributes.ieId && this.attributes.ieId == varsarray[0] 
                        && this.attributes.mbox == varsarray[1]) {
                    SUGAR.email2.folders.focusNode = this;
                    return false;
                }
            }, null, [id, mbox]);
        return SUGAR.email2.folders.focusNode;
    },

    /**
     * Displays a short form
     */
    folderAdd : function() {
        SUGAR.email2.contextMenus.frameFoldersContextMenu.hide();

        var node = SUGAR.email2.clickedFolderNode;

        if(node != null && node.attributes) {
            overlay(app_strings.LBL_EMAIL_FOLDERS_ADD_DIALOG_TITLE, 
                    app_strings.LBL_EMAIL_SETTINGS_NAME, 
                    'prompt', {fn:SUGAR.email2.folders.folderAddXmlCall});
        } else {
            alert(app_strings.LBL_EMAIL_FOLDERS_NO_VALID_NODE);
        }
    },

    folderAddXmlCall : function(button, name) {
        if (button != "ok") {
          return;
        }
        
        var post = '';
        var type = 'sugar';

        parentNode = SUGAR.email2.clickedFolderNode;
        this.contextMenuFocus = parentNode;

        if(parentNode.attributes.ieId) {
            if(parentNode.attributes.ieId != 'folder' && parentNode.attributes.ieId.match(SUGAR.email2.reGUID)) {
                type = 'imap';
            }
        }
        if(type == 'imap') {

            // make an IMAP folder
            
            post = "&newFolderName=" + name + "&mbox=" + parentNode.attributes.mbox + "&ieId=" + parentNode.attributes.ieId;
            AjaxObject.startRequest(callbackFolderUpdate, urlStandard + '&emailUIAction=saveNewFolder&folderType=imap' + post);
        } else if(type == 'sugar') {
            // make a Sugar folder
            if(SUGAR.email2.folders.isUniqueFolderName(name)) {
                post = "&parentId=" + parentNode.attributes.id + "&nodeLabel=" + name;
                AjaxObject.startRequest(callbackFolderSave, urlStandard + '&emailUIAction=saveNewFolder&folderType=sugar&' + post);
            } else {
                alert(app_strings.LBL_EMAIL_ERROR_DUPE_FOLDER_NAME);
                SUGAR.email2.folders.folderAdd();
                return;
            }
        } else {
            alert(app_strings.LBL_EMAIL_ERROR_CANNOT_FIND_NODE);
        }

        // hide add-folder diaglogue
        SUGAR.email2.e2overlay.hide();
    },

    /**
     * Removes either an IMAP folder or a Sugar Folder
     */
    folderDelete : function() {
        SUGAR.email2.contextMenus.frameFoldersContextMenu.hide();
        
        if(confirm(app_strings.LBL_EMAIL_FOLDERS_DELETE_CONFIRM)) {
            var post = '';
            var parentNode = SUGAR.email2.clickedFolderNode;

            if(parentNode != null && parentNode.attributes) {
                if(parentNode.attributes.mbox == 'INBOX' || parentNode.attributes.id == 'Home') {
                    overlay(app_strings.LBL_EMAIL_ERROR_GENERAL_TITLE, app_strings.LBL_EMAIL_FOLDERS_CHANGE_HOME, 'alert');
                    return;
                }

                AjaxObject.target = 'frameFlex';

                if(parentNode.attributes.ieId != 'folder') {
                    // delete an IMAP folder
                    post = "&folderType=imap&mbox=" + parentNode.attributes.mbox + "&ieId=" + parentNode.attributes.ieId;
                } else {
                    // delete a sugar folder
                    post = "&folderType=sugar&folder_id=" + parentNode.attributes.id;
                }
                overlay("Deleting folder", app_strings.LBL_EMAIL_ONE_MOMENT);
                AjaxObject.startRequest(callbackFolderDelete, urlStandard + '&emailUIAction=deleteFolder' + post);
            } else {
                alert(app_strings.LBL_EMAIL_ERROR_CANNOT_FIND_NODE);
            }
        }
    },

    /**
     * Rename folder form
     */
     //EXT111
    folderRename : function() {
        SUGAR.email2.contextMenus.frameFoldersContextMenu.hide();
        var node = SUGAR.email2.clickedFolderNode;

        if(node != null) {
            if(node.id == 'Home' || !node.attributes || node.attributes.mbox == 'INBOX') {
                overlay(app_strings.LBL_EMAIL_ERROR_GENERAL_TITLE, app_strings.LBL_EMAIL_FOLDERS_CHANGE_HOME, 'alert');
                return;
            }
            SUGAR.email2.tree.folderEditor.editNode = node;
            SUGAR.email2.tree.folderEditor.enable();
            SUGAR.email2.tree.folderEditor.startEdit(node.ui.textNode);
        } else {
            alert(app_strings.LBL_EMAIL_FOLDERS_NO_VALID_NODE);
        }
    },

    /**
     * fills an Object with key-value pairs of available folders
     */
    getAvailableFoldersObject : function() {
        var ret = new Object();
        var tree = SUGAR.email2.tree.root;

        if(tree.children) {
            for(var i=0; i<tree.children.length; i++) {
                ret = this.getFolderFromChild(ret, tree.children[i], '', app_strings.LBL_EMAIL_SPACER_MAIL_SERVER);
            }
        } else {
            ret['none'] = app_strings.LBL_NONE;
        }

        return ret;
    },

    /**
     * Fills in key-value pairs for dependent dropdowns
     * @param object ret Associative array
     * @param object node TreeView node in focus
     * @param string currentPath Built up path thus far
     * @param string spacer Defined in app_strings, visual separator b/t Sugar and Remote folders
     */
    getFolderFromChild : function(ret, node, currentPath, spacer) {
        if(node.data != null && node.depth > 0) {
            /* handle visual separtors differentiating b/t mailserver and local */
            if(node.data.ieId && node.data.ieId == 'folder') {
                spacer = app_strings.LBL_EMAIL_SPACER_LOCAL_FOLDER;
            }

            if(!ret.spacer0) {
                ret['spacer0'] = spacer;
            } else if(ret.spacer0 != spacer) {
                ret['spacer1'] = spacer
            }

            var theLabel = node.data.label.replace(/<[^>]+[\w\/]+[^=>]*>/gi, '');
            var depthMarker = currentPath;
            var retIndex = SUGAR.email2.folders._createFolderId(node);
            ret[retIndex] = depthMarker + theLabel;
        }

        if(node.children != null) {
            if(theLabel) {
                currentPath += theLabel + "/";
            }

            for(var i=0; i<node.children.length; i++) {
                ret = this.getFolderFromChild(ret, node.children[i], currentPath, spacer);
            }
        }

        return ret;
    },

    /**
     * Wrapper to refresh folders tree
     */
    getFolders : function() {
        SUGAR.email2.accounts.rebuildFolderList();
    },

    /**
     * handles events around folder-rename input field changes
     * @param object YUI event object
     */
    handleEnter : function(e) {
        switch(e.browserEvent.type) {
            case 'click':
                e.preventDefault(); // click in text field
            break;

            case 'blur':
                SUGAR.email2.folders.submitFolderRename(e);
            break;

            case 'keypress':
                var kc = e.browserEvent.keyCode;
                switch(kc) {
                    case 13: // enter
                        e.preventDefault();
                        SUGAR.email2.folders.submitFolderRename(e);
                    break;

                    case 27: // esc
                        e.preventDefault(e);
                        SUGAR.email2.folders.cancelFolderRename(e);
                    break;
                }
            break;
        }
    },
    /**
    * Called when a node is clicked on in the folder tree
    * @param node, The node clicked on
    * @param e, The click event
    */
    handleClick : function(node, e) {
        //If the click was on a sugar folder
        if (node.attributes.ieId == "folder") {
            SUGAR.email2.listView.populateListFrameSugarFolder(node, node.id, false)
        }
        else {
            SUGAR.email2.listView.populateListFrame(node, node.attributes.ieId, false);
        }
       //eval(node.attributes.click);
       //debugger;
    },
    
    /**
    * Called when a node is right-clicked on in the folder tree
    */
    handleRightClick : function(node, e) {
        //If the click was on a sugar folder
        
        SUGAR.email2.clickedFolderNode = node;
        var inbound = (node.attributes.ieId && node.attributes.ieId != 'folder');
		var disableNew = (inbound && (typeof(node.attributes.mbox) == 'undefined'));
        var menu = SUGAR.email2.contextMenus.frameFoldersContextMenu;
        menu.items.items[0][inbound ? 'enable' : 'disable']();
        menu.items.items[1][inbound ? 'enable' : 'disable']();
		menu.items.items[2][disableNew ? 'disable' : 'enable']();
        menu.items.items[3]['enable']();
        menu.items.items[4]['enable']();
        menu.items.items[5]['enable']();
        menu.items.items[6]['disable']();
		if (inbound && node.attributes.isGroup != null && node.attributes.isGroup == "true") {
	        menu.items.items[0]['disable']();
	        menu.items.items[1]['disable']();
	        menu.items.items[3]['disable']();
            menu.items.items[4]['disable']();
		}
        if (node.attributes.protocol != null) {
        	menu.items.items[6]['enable']();
        }
		if (node.attributes.folder_type != null && (node.attributes.folder_type == "inbound" ||
													node.attributes.folder_type == "sent" ||
													node.attributes.folder_type == "draft")) {

	        menu.items.items[3]['disable']();
	        menu.items.items[4]['disable']();
	        menu.items.items[5]['disable']();
															
		}
        menu.show(node.ui.getAnchor());
    },
    
    /**
    * Called when a row is dropped on a node
    */
    handleDrop : function(dropEvent) {
        //console.log(dropEvent);
        var rows = dropEvent.data.selections;
        var targetFolder = dropEvent.target;
        //Check that the drop was from the grid and not onto its own folder
        if (dropEvent.data.grid && rows[0].data.mbox != targetFolder.attributes.mbox) {
            var srcIeId = rows[0].data.ieId;
            var srcFolder = rows[0].data.mbox;
            var destIeId = targetFolder.attributes.ieId;
            var destFolder = targetFolder.attributes.mbox;
            var uids = [];
            for(var i=0; i<rows.length; i++) {
                uids[i] = rows[i].data.uid;
            }
            SUGAR.email2.listView.moveEmails(srcIeId, srcFolder, destIeId, destFolder, uids, rows);
        }
    },
    
    /**
    * Called when something is dragged over a Folder Node
    */
    dragOver : function(dragObject) {
       //console.log("dragged Over");
       return true;
    },
    
    /**
     * Determines if a folder name is unique to the folder tree
     * @param string name
     */
    isUniqueFolderName : function(name) {
        uniqueFolder = true;
        var root = SUGAR.email2.tree.getRootNode();
        root.cascade(function(name) {
            if (this.attributes && this.attributes.ieId == "folder") {
                if (this.attributes.text == name) {
                    uniqueFolder = false;
                    return false;
                }
            }
        }, null, name);
        return uniqueFolder;
    },

    /**
     * Makes async call to rename folder in focus
     * @param object e Event Object
     */
    submitFolderRename : function(editor, newName, origName) {
        if (SUGAR.email2.tree.folderEditor.disabled) {
        	return;
        }
    	SUGAR.email2.tree.folderEditor.disable();
        //Ignore no change
        if (newName == origName) {
            return true;
        }
        if(SUGAR.email2.folders.isUniqueFolderName(newName, editor.editNode)) {
            overlay(app_strings.LBL_EMAIL_MENU_RENAMING_FOLDER, app_strings.LBL_EMAIL_ONE_MOMENT);
        	node = editor.editNode;
            if (node.attributes.ieId == "folder") {
                //Sugar Folder
                AjaxObject.startRequest(callbackFolderRename, urlStandard + "&emailUIAction=renameFolder&folderId=" + node.id + "&newFolderName=" + newName);
            }
            else {
                //IMAP folder or POP mailbox
                var nodePath = node.attributes.mbox.substring(0, node.attributes.mbox.lastIndexOf(".") + 1);
                AjaxObject.startRequest(callbackFolderRename, urlStandard + "&emailUIAction=renameFolder&ieId=" 
                    + node.attributes.ieId + "&oldFolderName=" + node.attributes.mbox + "&newFolderName=" + nodePath + newName);
            }
            return true;
        } else {
            alert(app_strings.LBL_EMAIL_ERROR_DUPE_FOLDER_NAME);
            return false;
        }
    },

    /**
     * makes async call to do a full synchronization of all accounts
     */
    synchronizeAccounts : function() {
        if(confirm(app_strings.LBL_EMAIL_SETTINGS_FULL_SYNC_WARN)) {
            overlayModal(app_strings.LBL_EMAIL_SETTINGS_FULL_SYNC, app_strings.LBL_EMAIL_ONE_MOMENT + "<br>&nbsp;<br>" + app_strings.LBL_EMAIL_COFFEE_BREAK);
            AjaxObject.startRequest(callbackFullSync, urlStandard + '&emailUIAction=synchronizeEmail');
        }
    },

    /**
     * Updates user's folder subscriptsion (Sugar only)
     * @param object SELECT DOM object in focus
     * @param string type of Folder selection
     */
    updateSubscriptions : function() {
        var active = "";

        process = new Array();
        process[0] = document.getElementById('userFolders');
        process[1] = document.getElementById('groupFolders');

        for(p=0; p<2; p++) {
            var select = process[p];

            for(i=0; i<select.options.length; i++) {
                var opt = select.options[i];
                if(opt.selected && opt.value != "") {
                    if(active != "") {
                        active += "::";
                    }
                    active += opt.value;
                }
            }
        }

        AjaxObject.startRequest(callbackFolderSubscriptions, urlStandard + '&emailUIAction=updateSubscriptions&subscriptions=' + active);
    }

};

SUGAR.email2.folders.checkEmail2 = function() {
    AjaxObject.startRequest(callbackCheckEmail2, urlStandard + "&emailUIAction=checkEmail2");
}
////    END FOLDERS OBJECT
///////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
////    SUGAR.email2.keys
/**
 * Keypress Event capture and processing
 */
SUGAR.email2.keys = {
    overall : function(e) {





        switch(e.charCode) {
            case 119: // "w"
                if(e.ctrlKey || e.altKey) {
                    var focusRegion = SUGAR.email2.innerLayout.regions.center;
                    if(focusRegion.activePanel.closable == true) {
                        focusRegion.remove(focusRegion.activePanel);
                    }
                }
            break;
        }
    }
};
////    END SUGAR.email2.keys
///////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
////    SUGAR.email2.listView
/**
 * ListView object methods and attributes
 */
SUGAR.email2.listView = {
    currentRowId : -1,

    /**
     * Fills the ListView pane with detected messages.
     */
    populateListFrame : function(node, ieId, forceRefresh) {
        YAHOO.util.Connect.abort(AjaxObject.currentRequestObject, null, false);

        SUGAR.email2.innerLayout.regions.center.showPanel('listViewLayout');
        //SUGAR.email2.innerLayout.regions.center.activePanel.setTitle(loadingSprite);
        SUGAR.email2.previewLayout.findPanel('_blank').setContent('');
        /*var mbox = '';

        // save expanded/closed state
        if(id.depth < 2) {
            return;
        } else {
            mbox = id.data.id;

            tempId = id;
            for(i=0; i<id.depth-2; i++) {
                mbox = tempId.parent.data.id + '.' + mbox;
                tempId = tempId.parent;
            }
        }
        // preserve lazyLoadFolder
        lazyLoadFolder.folder = mbox;
        lazyLoadFolder.ieId = ieId;*/

        SUGAR.email2.grid.getDataSource().baseParams['emailUIAction'] = 'getMessageListXML';
        SUGAR.email2.grid.getDataSource().baseParams['mbox'] = node.attributes.mbox;
        //SUGAR.email2.grid.getDataSource().baseParams['acct'] = (tempId) ? tempId.parent.data.label : "";
        SUGAR.email2.grid.getDataSource().baseParams['ieId'] = ieId;
        forcePreview = true; // loads the preview pane with first item in grid
        SUGAR.email2.grid.colModel.setHidden(5, true);
        SUGAR.email2.grid.getDataSource().load({params:{start:0, limit:SUGAR.email2.userPrefs.emailSettings.showNumInList}});
    },

    /**
     * Like populateListFrame(), but specifically for SugarFolders since the API is radically different
     */
    populateListFrameSugarFolder : function(node, folderId, forceRefresh) {
        SUGAR.email2.innerLayout.regions.center.showPanel('listViewLayout');
        SUGAR.email2.previewLayout.findPanel('_blank').setContent('');

        SUGAR.email2.grid.getDataSource().baseParams['emailUIAction'] = 'getMessageListSugarFoldersXML';
        SUGAR.email2.grid.getDataSource().baseParams['ieId'] = node.id;
        SUGAR.email2.grid.getDataSource().baseParams['mbox'] = node.attributes.origText ? node.attributes.origText : node.attributes.text;
        if (node.attributes.folder_type != null && node.attributes.folder_type == 'sent') {
        	SUGAR.email2.grid.colModel.setHidden(5, false);
        } else {
        	SUGAR.email2.grid.colModel.setHidden(5, true);
        }
        SUGAR.email2.grid.getDataSource().load({params:{start:0, limit:SUGAR.email2.userPrefs.emailSettings.showNumInList}});
    },

    /**
     * Sets sort order as user preference
     * @param
     */
    saveListViewSortOrder : function(sortBy, focusFolderPassed, ieIdPassed, ieNamePassed) {
        ieId = ieIdPassed;
        ieName = ieNamePassed;
        focusFolder = focusFolderPassed;

        SUGAR.email2.util.clearHiddenFieldValues('emailUIForm');
        var previousSort = document.getElementById('sortBy').value;

        document.getElementById('sortBy').value = sortBy;
        document.getElementById('emailUIAction').value = 'saveListViewSortOrder';
        document.getElementById('focusFolder').value = focusFolder;
        document.getElementById('ieId').value = ieId;

        if(sortBy == previousSort) {
            document.getElementById('reverse').value = '1';
        }

        var formObject = document.getElementById('emailUIForm');
        YAHOO.util.Connect.setForm(formObject);

        AjaxObject.startRequest(callbackListViewSortOrderChange, null);
    },


    /**
     * Enables click/arrow select of grid items which then populate the preview pane.
     */
    selectFirstRow : function() {
        SUGAR.email2.grid.selModel.selectFirstRow();
    },

    selectLastRow : function() {
        SUGAR.email2.grid.selModel.selectRow(SUGAR.email2.grid.dataSource.data.getCount() - 1);
    },

    setEmailListStyles : function() {
        SUGAR.email2.listView.boldUnreadRows();
        var ds = SUGAR.email2.grid.getDataSource();
        if (SUGAR.email2.grid.getSelections().length == 0) {
            document.getElementById('_blank').innerHTML = '';
        }

        var acctMbox = '';
        if(typeof(ds.baseParams.mbox) != 'undefined') {
            acctMbox = (ds.baseParams.acct) ? ds.baseParams.acct + " " + ds.baseParams.mbox : ds.baseParams.mbox;
            var cm = SUGAR.email2.grid.getColumnModel();
            if (ds.baseParams.mbox == mod_strings.LBL_LIST_FORM_SENT_TITLE) {
                cm.setColumnHeader(4, mod_strings.LBL_LIST_DATE_SENT);
                //SUGAR.email2.grid.render();
            } else if (cm.config[4].header != app_strings.LBL_EMAIL_DATE_SENT_BY_SENDER){
                cm.setColumnHeader(4, app_strings.LBL_EMAIL_DATE_SENT_BY_SENDER);
                //SUGAR.email2.grid.render();
            }
        }
        var total = (typeof(ds.totalLength) != "undefined") ? " (" + ds.totalLength +" " + app_strings.LBL_EMAIL_MESSAGES +") " : "";
        SUGAR.email2.innerLayout.regions.center.getPanel('listViewLayout').setTitle(acctMbox + total);// + toggleRead + manualFit);


        // 4/20/2007 added to hide overlay after search
        //hideOverlay();
        if (ds.reader.xmlData.getElementsByTagName('UnreadCount').length > 0){
            var unread = ds.reader.xmlData.getElementsByTagName('UnreadCount')[0].childNodes[0].data;
            var node = SUGAR.email2.folders.getNodeFromIeIdAndMailbox(ds.baseParams.ieId, ds.baseParams.mbox);
            if (node) node.attributes.unseen = unread;
        }
        SUGAR.email2.accounts.renderTree();

        
        // bug 15035 perhaps a heavy handed solution to stopping the loading spinner.
        if(forcePreview && ds.totalCount > 0) {
            SUGAR.email2.detailView.getEmailPreview();
            forcePreview = false;
        }
    },

    /**
     * Removes a row if found via its UID
     */
    removeRowByUid : function(uid) {
        uid = new String(uid);
        uids = uid.split(',');
        var ds = SUGAR.email2.grid.getDataSource();

        for(j=0; j<uids.length; j++) {
            var theUid = uids[j];
            var r = ds.getById(uids[j]);
            ds.remove(r);
        }
    },

    displaySelectedEmails : function(rows) {
        var dm = SUGAR.email2.grid.getDataModel();
        var uids = '';

        for(i=0; i<rows.length; i++) {
            var rowIndex = rows[i].rowIndex;
            var metadata = dm.data[rowIndex];

            if(uids != "") {
                uids += ",";
            }
            uids += metadata[5];

            // unbold unseen email
            this.unboldRow(rowIndex);
        }

        SUGAR.email2.detailView.populateDetailViewMultiple(uids, metadata[6], metadata[7], metadata[8], false);
    },

    /**
     * exception handler for data load failures
     */
    loadException : function(dataModel, ex, response) {
        //debugger;
    },

    /**
     * Moves email(s) from a folder to another, from IMAP/POP3 to Sugar and vice-versa
     * @param string sourceIeId Email's source I-E id
     * @param string sourceFolder Email's current folder
     * @param destinationIeId Destination I-E id
     * @param destinationFolder Destination folder in format [root::IE::INBOX::etc]
     *
     * @param array emailUids Array of email's UIDs
     */
    moveEmails : function(sourceIeId, sourceFolder, destinationIeId, destinationFolder, emailUids, selectedRows) {
        if(destinationIeId != 'folder' && sourceIeId != destinationIeId) {
            overlay(app_strings.LBL_EMAIL_ERROR_MOVE_TITLE, app_strings.LBL_EMAIL_ERROR_MOVE);
        } else {
            overlay("Moving Email(s)", app_strings.LBL_EMAIL_ONE_MOMENT);
            // remove rows from visibility
            for(row in selectedRows) {
                //SUGAR.email2.grid.getDataSource().remove(row);
            }

            var baseUrl =    '&sourceIeId=' + sourceIeId +
                            '&sourceFolder=' + sourceFolder +
                            '&destinationIeId=' + destinationIeId +
                            '&destinationFolder=' + destinationFolder;
            var uids = '';

            for(i=0; i<emailUids.length; i++) {
                if(uids != '') {
                    uids += app_strings.LBL_EMAIL_DELIMITER;
                }
                uids += emailUids[i];
            }
            if (destinationIeId == 'folder' && sourceFolder != 'sugar::Emails') {
            	AjaxObject.startRequest(callbackImportOneEmail, urlStandard + '&emailUIAction=moveEmails&emailUids=' + uids + baseUrl);
            } else {
            	AjaxObject.startRequest(callbackMoveEmails, urlStandard + '&emailUIAction=moveEmails&emailUids=' + uids + baseUrl);
            }
        }
    },
    
    /**
     * Unbolds text in the grid view to denote read status
     */
    markRead : function(index, record) {
        // unbold unseen email
        if (typeof (index) == 'number') {
            var row = SUGAR.email2.grid.view.getRow(index);
            row.style.fontWeight = "normal";
            record.data['seen'] = 1;
        }
    },

    /**
     * grid row output, bolding unread emails
     */
    boldUnreadRows : function() {
        // bold unread emails
        var rows = SUGAR.email2.grid.view.getBodyTable().getElementsByTagName('tr');
        for(i=0; i < rows.length; i++) {
            var row = rows[i];
            var seen = row.getElementsByTagName('td')[10];
            if(seen.firstChild.firstChild.innerHTML == "0") {
                row.style.fontWeight = "bold"
            }
        }
    },

    /**
     * Show preview for an email if 1 and only 1 is selected
     * ---- all references must be fully qual'd since this gets wrapped by the YUI event handler
     */
    handleRowSelect : function(e) {
        if(e.selectedRows.length == 1) {
            SUGAR.email2.detailView.getEmailPreview();
        }
    },

    handleDrop : function(e, dd, targetId, e2) {
        switch(targetId) {
            case 'htmleditordiv':
                var rows = SUGAR.email2.grid.getSelectedRows();
                if(rows.length > 0) {
                    SUGAR.email2.listView.displaySelectedEmails(rows);
                }
            break;

            default:
                var targetElId = new String(targetId);
                var targetIndex = targetElId.replace('ygtvlabelel',"");
                var targetNode = SUGAR.email2.tree.getNodeByIndex(targetIndex);
                var dm = SUGAR.email2.grid.getDataModel();
                var emailUids = new Array();
                var destinationIeId = targetNode.data.ieId;
                var destinationFolder = SUGAR.email2.util.generateMboxPath(targetNode.data.mbox);


                var rows = SUGAR.email2.grid.getSelectedRows();
                // iterate through dragged rows
                for(i=0; i<rows.length; i++) {
                    //var rowIndex = e.selModel.selectedRows[i].rowIndex;
                    var rowIndex = rows[i].rowIndex;
                    var dataModelRow = dm.data[rowIndex];
                    var sourceIeId = dataModelRow[7];
                    var sourceFolder = dataModelRow[6];
                    emailUids[i] = dataModelRow[5];
                }

                // event wrapped call - need FQ
                overlay(app_strings.LBL_EMAIL_PERFORMING_TASK, app_strings.LBL_EMAIL_ONE_MOMENT);
                SUGAR.email2.listView.moveEmails(sourceIeId, sourceFolder, destinationIeId, destinationFolder, emailUids, e.selModel.selectedRows);
            break;
        }
    },

    /**
     * Hack-around to get double-click and single clicks to work on the grid
     * ---- all references must be fully qual'd since this gets wrapped by the YUI event handler
     */
    handleClick : function(selModel, index, record) {
        SUGAR.email2.listView.currentRowIndex = index;
        clearTimeout(SUGAR.email2.detailView.previewTimer);
        SUGAR.email2.detailView.previewTimer = setTimeout("SUGAR.email2.detailView.getEmailPreview();", 500);
    },

    /**
     * Custom handler for double-click/enter
     * ---- all references must be fully qual'd since this gets wrapped by the YUI event handler
     */
    getEmail : function() {
        //var idx = SUGAR.email2.grid.getSelectionModel().getSelected().id;
        //var metadata = SUGAR.email2.grid.getDataSource().data[idx];
        var rows = SUGAR.email2.grid.getSelections();
        //only handle one selection for now.
        var row = rows[0].data;

//        SUGAR.email2.listView.unboldRow(idx);


        clearTimeout(SUGAR.email2.detailView.previewTimer);
        document.getElementById("_blank").innerHTML = "";

        if(row.type != "draft") {
            SUGAR.email2.detailView.populateDetailView(row.uid, row.mbox, row.ieId, 'true', SUGAR.email2.innerLayout);
        } else {
            // circumventing yui-ext tab generation, let callback handler build new view
            SUGAR.email2.util.clearHiddenFieldValues('emailUIForm');
            //function(uid, mbox, ieId, setRead, destination) {
            document.getElementById('emailUIAction').value = 'getSingleMessageFromSugar';
            document.getElementById('uid').value = row.uid; // uid;
            document.getElementById('mbox').value = row.mbox; // mbox;
            document.getElementById('ieId').value = row.ieId; // ieId;

            YAHOO.util.Connect.setForm(document.getElementById('emailUIForm'));
            AjaxObject.target = '_blank';
            AjaxObject.startRequest(AjaxObject.detailView.callback.emailDetail, null);
        }
    },

    /**
     * Retrieves a row if found via its UID
     * @param string
     * @return int
     */
    getRowIndexByUid : function(uid) {
        uid = new String(uid);
        uids = uid.split(',');

        for(j=0; j<uids.length; j++) {
            var theUid = uids[j];

            for(i=0; i<SUGAR.email2.grid.getDataSource().data.length; i++) {
                if(SUGAR.email2.grid.getDataSource().data[i].id == theUid) {
                    return i;
                }
            }
        }
    },
    
    /**
     * Returns the UID's of the seleted rows
     *
     */
     getUidsFromSelection : function() {
         var rows = SUGAR.email2.grid.getSelections();
         var uids = [];
         /* iterate through available rows JIC a row is deleted - use first available */
         for(var i=0; i<rows.length; i++) {
             uids[i] = rows[i].data.uid;
         }
         return uids;
     }
    
};
////    END SUGAR.email2.listView
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
////    SEARCH
SUGAR.email2.search = {
    /**
     * sends search criteria
     * @param reference element search field
     */
    search : function(el) {
        var searchCriteria = new String(el.value);

        if(searchCriteria == '') {
            alert(app_strings.LBL_EMAIL_ERROR_EMPTY);
            return false;
        }

        var safeCriteria = escape(searchCriteria);

        var accountListSearch = document.getElementById('accountListSearch');
        //overlay(app_strings.LBL_EMAIL_SEARCHING,app_strings.LBL_EMAIL_ONE_MOMENT);

        SUGAR.email2.grid.getDataSource().baseParams['emailUIAction'] = 'search';
        SUGAR.email2.grid.getDataSource().baseParams['mbox'] = app_strings.LBL_EMAIL_SEARCH_RESULTS_TITLE;
        SUGAR.email2.grid.getDataSource().baseParams['subject'] = safeCriteria;
        SUGAR.email2.grid.getDataSource().baseParams['ieId'] = accountListSearch.options[accountListSearch.selectedIndex].value;
        SUGAR.email2.grid.getDataSource().load({params:{start:0, limit:SUGAR.email2.userPrefs.emailSettings.showNumInList}});
        
    },

    /**
     * sends advanced search criteria
     */
    searchAdvanced : function() {
        var formObject = document.getElementById('advancedSearchForm');
        var search = false;

        for(i=0; i<formObject.elements.length; i++) {
            if(formObject.elements[i].type != 'button' && formObject.elements[i].value != "") {
                search = true;
            }
            if(formObject.elements[i].type == 'text') {
                SUGAR.email2.grid.getDataSource().baseParams[formObject.elements[i].name] = formObject.elements[i].value;
            }
        }

        if(search) {
            SUGAR.email2.grid.getDataSource().baseParams['emailUIAction'] = 'searchAdvanced';
            SUGAR.email2.grid.getDataSource().baseParams['mbox'] = app_strings.LBL_EMAIL_SEARCH_RESULTS_TITLE;
        	var accountListSearch = document.getElementById('accountListSearch');
        SUGAR.email2.grid.getDataSource().baseParams['ieId'] = accountListSearch.options[accountListSearch.selectedIndex].value;
            SUGAR.email2.grid.getDataSource().load({params:{start:0, limit:SUGAR.email2.userPrefs.emailSettings.showNumInList}});
        } else {
            alert(app_strings.LBL_EMAIL_ERROR_EMPTY);
        }
    },

    /**
     * clears adv search form fields
     */
    searchClearAdvanced : function() {
        var form = document.getElementById('advancedSearchForm');

        for(i=0; i<form.elements.length; i++) {
            if(form.elements[i].type != 'button') {
                form.elements[i].value = '';
            }
        }
    },
    
    updateSearchTab : function() {
    	var accountListSearch = document.getElementById("accountListSearch");
    	if (accountListSearch.isLoaded == null || accountListSearch.isLoaded == false) {
    		// load data
        	AjaxObject.startRequest(callbackRebuildShowAccountListForSearch, urlStandard + '&emailUIAction=rebuildShowAccountForSearch');
    		accountListSearch.isLoaded = true;
    	}
    },
    
    accountListSearchChange : function(accountListSearch) {
    	var searchIndex = accountListSearch.selectedIndex;
    	if (accountListSearch.selectedIndex == 0) {
    		document.getElementById("advancedSearchButton").disabled = true;
    	} else {
    		
    		if (accountListSearch.options[searchIndex].protocol == 'pop3') {
    			document.getElementById('searchBodyDiv').style.display = "none";
    		} else {
    			document.getElementById('searchBodyDiv').style.display = "";	
    		} //else
    		document.getElementById("advancedSearchButton").disabled = false;
    	}
    },
    
    markSearchAccountListDirty : function() {
    	var accountListSearch = document.getElementById("accountListSearch");
    	accountListSearch.isLoaded = false;
    }
};
////    END SUGAR.email2.search
//////////////////////////////////////////////////////////////////////////////


//////////////////////////////////////////////////////////////////////////////
////    SUGAR.email2.settings
SUGAR.email2.settings = {
    /******************************************************************************
     * USER SIGNATURES calls stolen from Users module
     *****************************************************************************/
    createSignature : function(record, the_user_id) {
        var URL = "index.php?module=Users&action=PopupSignature&sugar_body_only=true";
        if(record != "") {
            URL += "&record="+record;
        }
        if(the_user_id != "") {
            URL += "&the_user_id="+the_user_id;
        }
        var windowName = 'email_signature';
        var windowFeatures = 'width=800,height=600,resizable=1,scrollbars=1';

        var win = window.open(URL, windowName, windowFeatures);
        if(win && win.focus) {
            // put the focus on the popup if the browser supports the focus() method
            win.focus();
        }
    },

    deleteSignature : function() {
        if(confirm(app_strings.LBL_EMAIL_CONFIRM_DELETE_SIGNATURE)) {
            overlay(app_strings.LBL_EMAIL_IE_DELETE_SIGNATURE, app_strings.LBL_EMAIL_ONE_MOMENT);
    		var singature_id = document.getElementById('signature_id').value;
        	AjaxObject.startRequest(callbackDeleteSignature, urlStandard + '&emailUIAction=deleteSignature&id=' + singature_id);
        } // if
    },
    
    saveOptionsGeneral :  function(displayMessage) {
        var formObject = document.getElementById('formEmailSettingsGeneral');
        YAHOO.util.Connect.setForm(formObject);
        SUGAR.email2.composeLayout.emailTemplates = null;

        AjaxObject.target = 'frameFlex';
        AjaxObject.startRequest(callbackSettings, urlStandard + '&emailUIAction=saveSettingsGeneral');

        if(displayMessage)
            alert(app_strings.LBL_EMAIL_SETTINGS_SAVED);
    },

    /**
     * switches UI to selected view
     */
    changeView : function(view) {
        SUGAR.email2.listViewLayout.remove('south', '_blank');
        SUGAR.email2.listViewLayout.remove('east', '_blank');
        SUGAR.email2.listViewLayout.beginUpdate();
        if (view == 'rows') {
            SUGAR.email2.listViewLayout.add('south', new Ext.ContentPanel(Ext.DomHelper.append(document.body, {tag:'div', id:'_blank'}), {closable: false}));
            SUGAR.email2.listViewLayout.getRegion('east').hide();
            SUGAR.email2.listViewLayout.getRegion('south').show();
        } else {
            SUGAR.email2.listViewLayout.add('east', new Ext.ContentPanel(Ext.DomHelper.append(document.body, {tag:'div', id:'_blank'}), {closable: false}));
            SUGAR.email2.listViewLayout.getRegion('south').hide();
            SUGAR.email2.listViewLayout.getRegion('east').show();
        }
        SUGAR.email2.listViewLayout.endUpdate(false);
        SUGAR.email2.grid.getDataSource().reload();
    },

    /**
     * Shows settings container screen
     */
    showSettings : function() {
        if(!this.settingsDialog) {
            this.settingsDialog = new Ext.LayoutDialog("settings", {
                modal:true,
                title: app_strings.LBL_EMAIL_SETTINGS,
                width:800,
                height:560,
                shadow:true,
                minWidth:300,
                minHeight:300,
                animateTarget:"settingsButton",
                autoCreate: true,
                collapsible : false,
                center: {
                    autoScroll:true,
                    tabPosition: SUGAR.email2.userPrefs.emailSettings.tabPosition,
                    closeOnTab: true,
                    alwaysShowTabs: true
                }
            });

            this.settingsDialog.addButton(app_strings.LBL_EMAIL_CLOSE, function(o) {SUGAR.email2.settings.settingsDialog.hide()});
            var layout = this.settingsDialog.getLayout();
            var accounts = new Ext.ContentPanel('tab_accounts', {title: app_strings.LBL_EMAIL_SETTINGS_ACCOUNTS});
            var folders = new Ext.ContentPanel('tab_folders', {title: app_strings.LBL_EMAIL_SETTINGS_FOLDERS});

            layout.beginUpdate();
            layout.add('center', new Ext.ContentPanel('tab_general', {title: app_strings.LBL_EMAIL_SETTINGS_GENERAL}));
            layout.add('center', accounts);
            layout.add('center', folders);





            layout.endUpdate(true);
            layout.showPanel('tab_general');

            accounts.addListener('activate', SUGAR.email2.accounts.lazyLoad);
            folders.addListener('activate', SUGAR.email2.folders.lazyLoadSettings);

        } // end lazy load
        this.settingsDialog.show();
    },

    lazyLoadRules : function() {
        if(false/*!SUGAR.email2.settings.rules*/) {
            AjaxObject.startRequest(callbackLoadRules, urlStandard + "&emailUIAction=loadRulesForSettings");
        }

    },

    toggleFullScreen : function(el) {
        var h = document.getElementById('header');

        if(h != null) {
            if(el.checked == false) {
                h.style.display = '';
            } else {
                h.style.display = 'none';
            }
        } else {
            alert(SUGAR.language.get("app_strings", "ERR_NO_HEADER_ID"));
        }
        SUGAR.email2.autoSetLayout();
    },

    toggleFullScreenQuick : function() {
        var h = document.getElementById('header');

        if(h != null) {
            if(h.style.display == 'none') {
                h.style.display = '';
            } else {
                h.style.display = 'none';
            }
        } else {
            alert(SUGAR.language.get("app_strings", "ERR_NO_HEADER_ID"));
        }
        SUGAR.email2.autoSetLayout();
    }
};
////    END SUGAR.email2.settings
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
////    SUGAR.email2.util
SUGAR.email2.util = {
    /**
     * Cleans serialized UID lists of duplicates
     * @param string
     * @return string
     */
    cleanUids : function(str) {
        var seen = new Object();
        var clean = "";
        var arr = new String(str).split(",");

        for(var i=0; i<arr.length; i++) {
            if(seen[arr[i]]) {
                continue;
            }

            clean += (clean != "") ? "," : "";
            clean += arr[i];
            seen[arr[i]] = true;
        }

        return clean;
    },

    /**
     * Clears hidden field values
     * @param string id ID of form element to clear
     */
    clearHiddenFieldValues : function(id) {
        form = document.getElementById(id);

        for(i=0; i<form.elements.length; i++) {
            if(form.elements[i].type == 'hidden') {
                var e = form.elements[i];
                if(e.name != 'action' && e.name != 'module' && e.name != 'to_pdf') {
                    e.value = '';
                }
            }
        }
    },

    /**
     * Reduces a SELECT drop-down to 0 items to prepare for new ones
     */
    emptySelectOptions : function(el) {
        if(el) {
            for(i=el.childNodes.length - 1; i >= 0; i--) {
                if(el.childNodes[i]) {
                    el.removeChild(el.childNodes[i]);
                }
            }
        }
    },

    /**
     * Returns the MBOX path in the manner php_imap expects:
     * ie: INBOX.DEBUG.test
     * @param string str Current serialized value, Home.personal.test.INBOX.DEBUG.test
     */
    generateMboxPath : function(str) {
        var ex = str.split("::");

        /* we have a serialized MBOX path */
        if(ex.length > 1) {
            var start = false;
            var ret = '';
            for(var i=0; i<ex.length; i++) {
                if(ex[i] == 'INBOX') {
                    start = true;
                }

                if(start == true) {
                    if(ret != "") {
                        ret += ".";
                    }
                    ret += ex[i];
                }
            }
        } else {
            /* we have a Sugar folder GUID - do nothing */
            return str;
        }

        return ret;
    },

    /**
     * returns a SUGAR GUID by navigating the DOM tree a few moves backwards
     * @param HTMLElement el
     * @return string GUID of found element or empty on failure
     */
    getGuidFromElement : function(el) {
        var GUID = '';
        var iterations = 4;
        var passedEl = el;

        // upwards
        for(var i=0; i<iterations; i++) {
            if(el) {
                if(el.id.match(SUGAR.email2.reGUID)) {
                    return el.id;
                } else {
                    el = el.parentNode;
                }
            }
        }

        return GUID;
    },

    /**
     * Returns the ID value for the current in-focus, active panel (in the innerLayout, not complexLayout)
     * @return string
     */
    getPanelId : function() {
        return SUGAR.email2.innerLayout.getRegion('center').getActivePanel().getId();
    },
    
    /**
     * wrapper to handle weirdness with IE
     * @param string instanceId
     * @return tinyMCE Controller object
     */
    getTiny : function(instanceId) {
        if(instanceId == '') {



            return null;
        }

        var t = tinyMCE.getInstanceById(instanceId);

        if(this.isIe()) {
            this.sleep(200);
            YAHOO.util.Event.onContentReady(instanceId, function(t) { return t; });
        }
        return t;
    },

    /**
     * Simple check for MSIE browser
     * @return bool
     */
    isIe : function() {
        var nav = new String(navigator.appVersion);
        if(nav.match(/MSIE/)) {
            return true;
        }
        return false;
    },

    /**
     * Recursively removes an element from the DOM
     * @param HTMLElement
     */
    removeElementRecursive : function(el) {
        this.emptySelectOptions(el);
    },
    
    /**
     * Fakes a sleep
     * @param int
     */
    sleep : function(secs) {
        setTimeout("void(0);", secs);
    },
    
    /**
     * Converts a <select> element to an Ext.form.combobox
     */
     convertSelect : function(select) {
       alert('in convertSelect');
       if (typeof(select) == "string") {
           select = document.getElementById(select);
       }
       debugger;
     }
};
////    END UTIL
///////////////////////////////////////////////////////////////////////////////


/******************************************************************************
 * UTILITIES
 *****************************************************************************/
/**
 * Shows overlay progress message
 */
function overlayModal(title, body) {
    overlay(title, body);
}
function overlay(reqtitle, body, type, additconfig) {
    var config = { };
    if (typeof(additconfig) == "object") {
        var config = additconfig;
    }
    config.title = reqtitle;
    config.msg = body;
    
    if (typeof(type) == "string") {
        if (type == 'prompt') {
            Ext.MessageBox.prompt(reqtitle, body, additconfig.fn);
        }
        if (type == 'alert') {
            Ext.MessageBox.alert(reqtitle, body);
        }
        if (type == 'progress') {
            Ext.MessageBox.progress(reqtitle, body);
        }
    } else {
       Ext.MessageBox.show(config);
    }
};

function hideOverlay() {
    Ext.MessageBox.hide();
};

function removeHiddenNodes(nodes) {
    for(var i = nodes.length - 1; i > -1; i--) {
       if (nodes[i].style.display == 'none') {
          nodes.splice(i,1);
       }
    }
}

function strpad(val) {
    return (!isNaN(val) && val.toString().length==1)?"0"+val:val;
};

function refreshTodos() {
    SUGAR.email2.util.clearHiddenFieldValues('emailUIForm');
    AjaxObject.target = 'todo';
    AjaxObject.startRequest(callback, urlStandard + '&emailUIAction=refreshTodos');
};

//Recursive function for adding nodes to an EXT tree
function addExtNodeRecursive(parentNode, childNodeData) {
    if (childNodeData.data) {
    childNode = new Ext.tree.TreeNode({id:childNodeData.data.id, text:childNodeData.data.label} + childNodeData.custom);
    childNode.data = childNodeData.data;
    parentNode.appendChild(childNode);
    if (childNodeData.nodes) {
        for(var i=0; i < childNodeData.nodes.length; i++) {
            addExtNodeRecursive(childNode, childNodeData.nodes[i]);
        }
    }
    }
}

/******************************************************************************
 * MUST STAY IN GLOBAL NAMESPACE
 *****************************************************************************/
function refresh_signature_list(signature_id, signature_name) {
    var field=document.getElementById('signature_id');
    var bfound=0;
    for (var i=0; i < field.options.length; i++) {
            if (field.options[i].value == signature_id) {
                if (field.options[i].selected==false) {
                    field.options[i].selected=true;
                }
                bfound=1;
            }
    }
    //add item to selection list.
    if (bfound == 0) {
        var newElement=document.createElement('option');
        newElement.text=signature_name;
        newElement.value=signature_id;
        field.options.add(newElement);
        newElement.selected=true;
    }

    //enable the edit button.
    var field1=document.getElementById('edit_sig');
    field1.style.visibility="visible";
    var deleteButt = document.getElementById('delete_sig');
    deleteButt.style.visibility="visible";
};

function setDefaultSigId(id) {
    var checkbox = document.getElementById("signature_default");
    var default_sig = document.getElementById("signatureDefault");

    if(checkbox.checked) {
        default_sig.value = id;
    } else {
        default_sig.value = "";
    }
};

function setSigEditButtonVisibility() {
    var field = document.getElementById('signature_id');
    var editButt = document.getElementById('edit_sig');
    var deleteButt = document.getElementById('delete_sig');
    if(field.value != '') {
        editButt.style.visibility = "visible";
        deleteButt.style.visibility = "visible";
    } else {
        editButt.style.visibility = "hidden";
        deleteButt.style.visibility = "hidden";
    }
}

// The reason to add this function because the original function in the gugar_3.js doesn't work in IE -7
// document.forms[form_name].elements is always null - Don't know why 
function set_return(popup_reply_data)
{
	from_popup_return = true;
	var form_name = popup_reply_data.form_name;
	var name_to_value_array = popup_reply_data.name_to_value_array;
	for (var the_key in name_to_value_array)
	{
		if(the_key == 'toJSON')
		{
			/* just ignore */
		}
		else
		{
			var displayValue=name_to_value_array[the_key].replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"');;
			// begin andopes change: support for enum fields (SELECT)
			if(document.getElementById(the_key).tagName == 'SELECT') {
				var selectField = document.getElementById(the_key);
				for(var i = 0; i < selectField.options.length; i++) {
					if(selectField.options[i].text == displayValue) {
						selectField.options[i].selected = true;
						break;
					}
				}
			} else {
				document.getElementById(the_key).value = displayValue;
			}
			// end andopes change: support for enum fields (SELECT)
		}
	}
}
