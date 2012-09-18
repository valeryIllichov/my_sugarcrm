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

if(typeof('console') == 'undefined'){
console = {
	log: function(message){

	}}
}

if (typeof(ModuleBuilder) == 'undefined') {
	ModuleBuilder = {
		init: function(){
			//Setup the basic ajax request settings
			Ext.Ajax.extraParams = {
				to_pdf: true
			};
			Ext.Ajax.url = 'index.php';
			Ext.Ajax.method = 'POST';
			Ext.Ajax.timeout = 300000;
			//Resize content on window resize()
			Ext.EventManager.onWindowResize(ModuleBuilder.autoSetLayout);
			//Setup the main layout
			ModuleBuilder.tree = createTreePanel(TREE_DATA, {
				header: true,
				region: 'west',
				id: 'west',
				width: 230,
				minSize: 100,
				autoScroll: true,
				collapsible: true,
				split: true,
				rootVisible: false
			});
			ModuleBuilder.tree.getSelectionModel().on('beforeselect', function(){
				return false;
			});
			ModuleBuilder.tree.on('click', ModuleBuilder.handleTreeClick);

			ModuleBuilder.tabPanel = new Ext.TabPanel({
				activeTab: 0,
				title: 'Properties',
				region:'center',
				items: [{
					title: SUGAR.language.get('ModuleBuilder', 'LBL_SECTION_MAIN'),
					autoScroll: true,
					id: 'center',
					contentEl: 'mbcenter'
				}]
			});

			ModuleBuilder.mainPanel = new Ext.Panel({
				layout: 'border',
				applyTo: 'mblayout',
				border: false,
				height: Ext.lib.Dom.getViewHeight() - (Ext.get('header').getHeight() * 2), // - Ext.get('main').getPadding('tb'),
				//autoHeight: true
				//frame: true,
				items: [ModuleBuilder.tree, ModuleBuilder.tabPanel,
				{
					id: 'help',
					title: SUGAR.language.get('ModuleBuilder', 'LBL_SECTION_HELP'),
					region:'east',
					contentEl: 'mbhelp',
					autoScroll: true,
					width: 250,
					minSize: 200,
					split: true,
					collapsible: true
				},
			 	{
					title: Ext.getDom('footerHTML').innerHTML,
					region: 'south',
					id: 'mbfooter',
					height: 0,
					border: false
				}]
			});
			Ext.dd.ScrollManager.register(Ext.getCmp('center').body);
			Ext.dd.ScrollManager.register(Ext.getCmp('help').body);
			Ext.dd.ScrollManager.register(ModuleBuilder.tree.body);

			//Setup Browser History
			var mbContent = Ext.ux.History.getBookmarkedState('mbContent');

			if (ModuleBuilder.mode == 'mb') {
				Ext.getCmp('west').setTitle(SUGAR.language.get('ModuleBuilder', 'LBL_SECTION_PACKAGES'));
				mbContent = mbContent ? mbContent : 'module=ModuleBuilder&action=package&package=';
			}
			else if (ModuleBuilder.mode == 'studio') {
				ModuleBuilder.MBpackage = ''; // set to empty so other views can recognize that dealing with an deployed, rather than undeployed, module
				Ext.getCmp('west').setTitle(SUGAR.language.get('ModuleBuilder', 'LBL_SECTION_MODULES'));
				mbContent = mbContent ? mbContent :'module=ModuleBuilder&action=wizard';
			}
			else if (ModuleBuilder.mode == 'sugarportal') {
				Ext.getCmp('west').setTitle(SUGAR.language.get('ModuleBuilder', 'LBL_SECTION_PORTAL'));
				mbContent = mbContent ? mbContent : 'module=ModuleBuilder&action=wizard&portal=1';
			}
			else if (ModuleBuilder.mode == 'dropdowns') {
				Ext.getCmp('west').setTitle(SUGAR.language.get('ModuleBuilder', 'LBL_SECTION_DROPDOWNS'));
				mbContent = mbContent ? mbContent : 'module=ModuleBuilder&action=dropdowns';
			}
			else {
				Ext.getCmp('west').collapse(false);
				mbContent = mbContent ? mbContent : 'module=ModuleBuilder&action=home';
			}

			Ext.ux.History.register('mbContent', mbContent, ModuleBuilder.navigate);
			Ext.ux.History.initialize();
			ModuleBuilder.getContent(mbContent);

			if (Ext.get('left')) {Ext.get('left').setStyle({display:'none'})}
			if (Ext.get('leftCol')) {Ext.get('leftCol').setStyle({display:'none'})}
			Ext.getCmp('mbfooter').body.setStyle({display:'none'});
			ModuleBuilder.autoSetLayout();
			//Assistant.mbAssistant = Assistant.creatembAssistant();
			ModuleBuilder.tabPanel.on('tabchange', function(panel, tab) {
				if (tab.isVisible())
					ModuleBuilder.helpSetup(null, null, tab.id);
			});

		},
		//Layout history manager
		history: {
			popup_window: false,
			reverted: false,
			params: { },
			browse: function(module, layout, subpanel){
				if (!module && ModuleBuilder.module != "undefined") {
					module = ModuleBuilder.module;
				}
				if (!ModuleBuilder.history.popup_window || !ModuleBuilder.history.popup_window.isVisible()) {
					ModuleBuilder.history.popup_window = new Ext.Window({
						title: module + ' : ' + SUGAR.language.get('ModuleBuilder', 'LBL_' + layout.toUpperCase()) + " history",
						width: 350,
						autoHeight: true,
						resizeable: true,
						renderTo: document.body
					});
				}
				ModuleBuilder.history.params = {
					module: 'ModuleBuilder',
					histAction: 'browse',
					action: 'history',
					view_package: ModuleBuilder.MBpackage,
					view_module: module,
					view: layout,
					subpanel: subpanel
				};
				ModuleBuilder.history.popup_window.load({url: 'index.php', params: ModuleBuilder.history.params});
				ModuleBuilder.history.popup_window.show();
			},
			preview: function(module, layout, id, subpanel) {
				var prevPanel = Ext.getCmp('preview:' + id);
				if (!prevPanel) {
					ModuleBuilder.history.params = {
						module: 'ModuleBuilder',
						histAction: 'preview',
						action: 'history',
						view_package: ModuleBuilder.MBpackage,
						view_module: module,
						view: layout,
						sid: id,
						subpanel: subpanel
					};
					prevPanel = new Ext.Panel({
						title: "Preview",
						id: 'preview:' + id,
						closable: true,
						autoScroll: true,
						autoLoad: {url:'index.php',params: ModuleBuilder.history.params}
					});
					ModuleBuilder.tabPanel.add(prevPanel);
				}
				prevPanel.show();
			},
			revert: function(module, layout, id, subpanel){
				ModuleBuilder.tabPanel.remove("preview:" + id);
				ModuleBuilder.history.params = {
					module: 'ModuleBuilder',
					histAction: 'restore',
					action: 'history',
					view_package: ModuleBuilder.MBpackage,
					view_module: module,
					view: layout,
					sid: id,
					subpanel: subpanel
				}
				Ext.Ajax.request({
					params: ModuleBuilder.history.params,
					success: function(){
						ModuleBuilder.history.reverted = true;
						ModuleBuilder.getContent(ModuleBuilder.contentURL);
						ModuleBuilder.state.isDirty = true;
					}
				});
			},
			cleanup: function() {
				if (ModuleBuilder.history.reverted && ModuleBuilder.history.params.histAction) {
					ModuleBuilder.history.params.histAction = 'unrestore';
					Ext.Ajax.request({params: ModuleBuilder.history.params});
				}
				ModuleBuilder.history.params = { };
				ModuleBuilder.history.reverted = false;
			},
			update: function() {
				if (ModuleBuilder.history.popup_window && ModuleBuilder.history.popup_window.isVisible()) {
					var historyButton = Ext.getDom('historyBtn');
					if (historyButton) {
						historyButton.onclick();
					} else {
						ModuleBuilder.history.popup_window.close();
					}
				}
			}
		},
		state: {
			isDirty: false,
			intended_view: {
				url: null,
				successCall: null
			},
			current_view: {
				url: null,
				successCall: null
			},
			save_url_for_current_view: null,
			popup_window: null,
			setupState: function(){
				//ModuleBuilder.state.popup();
				document.body.setAttribute("onclose", "ModuleBuilder.state.popup(); ModuleBuilder.state.popup_window.show()");
				return;
			},
			onSaveClick: function(){
				//set dirty = false
				//call the save method of the current view.
				//call the intended action.
				ModuleBuilder.state.isDirty = false;
				var saveBtn = document.getElementById("saveBtn");
				if (!saveBtn) {
					var mbForm = document.forms[1];
					if (mbForm)
						var mbButtons = mbForm.getElementsByTagName("input");
					if (mbButtons) {
						for (var button = 0; button < mbButtons.length; button++) {
							var name = mbButtons[button].getAttribute("name");
							if (name && (name.toUpperCase() == "SAVEBTN" || name.toUpperCase() == "LSAVEBTN")) {
								saveBtn = mbButtons[button];
								break;
							}
						}
					}
					else {
						alert("Could not find the save action for this view.");
					}
				}
				if (saveBtn) {
					//After the save call completes, load the next page
					Ext.Ajax.on('requestcomplete', ModuleBuilder.state.loadOnSaveComplete);
					eval(saveBtn.getAttributeNode('onclick').value);
				}
				ModuleBuilder.state.popup_window.hide();
			},
			onDontSaveClick: function(){
				//set dirty to false
				//call the intended action.
				ModuleBuilder.state.isDirty = false;
				ModuleBuilder.history.cleanup();
				ModuleBuilder.getContent(ModuleBuilder.state.intended_view.url, ModuleBuilder.state.intended_view.successCall);
				ModuleBuilder.state.popup_window.hide();
			},
			loadOnSaveComplete: function() {
				Ext.Ajax.un('requestcomplete', ModuleBuilder.state.loadOnSaveComplete);
				ModuleBuilder.getContent(ModuleBuilder.state.intended_view.url, ModuleBuilder.state.intended_view.successCall);
			},
			popup: function(){
				ModuleBuilder.state.popup_window = new Ext.Window({
					width: "400px",
					draggable: true,
					constrainHeader: true,
					renderTo: document.body,
					modal: true,
					x: 500,
					y: 400,
					title:SUGAR.language.get('ModuleBuilder', 'LBL_CONFIRM_DONT_SAVE_TITLE'),
					html: SUGAR.language.get('ModuleBuilder', 'LBL_CONFIRM_DONT_SAVE'),
					bodyStyle: "padding:5px",
					bbar: [{
						text: SUGAR.language.get('ModuleBuilder', 'LBL_BTN_DONT_SAVE'),
						handler: ModuleBuilder.state.onDontSaveClick
					}, '->', {
						text: SUGAR.language.get('ModuleBuilder', 'LBL_BTN_CANCEL'),
						handler: function(){
							ModuleBuilder.state.popup_window.hide()
						}
					},{
						text: SUGAR.language.get('ModuleBuilder', 'LBL_BTN_SAVE_CHANGES'),
						handler: ModuleBuilder.state.onSaveClick
					}]
				});
			}

		},
		//AJAX Navigation Functions
		navigate : function(url) {
			//Check if we are just registering the url
			if (url != ModuleBuilder.contentURL) {
				ModuleBuilder.getContent(url);
			}
		},
		getContent: function(url, successCall){
			//save a pointer to intended action
			ModuleBuilder.state.intended_view.url = url;
			ModuleBuilder.state.intended_view.successCall = successCall;
			if(ModuleBuilder.state.isDirty){ //prompt to save current data.
				//check if we are editing a property of the current view (such views open up in new tabs)
				//if so we leave the state dirty and return
				temp_url = url.toLowerCase();
				if(null == temp_url.match(/&action=editproperty/)){
					ModuleBuilder.state.popup();
					ModuleBuilder.state.popup_window.show();
					return;
				}
				//use current_view
				//open a yui panel popup.
				//set the save method to call the this.state.save_action_for_current_view.
				//collect user response.
				//set state.isDirty = false.
				//call this.getContent for intended_action.

			}else{
				ModuleBuilder.state.current_view.url = url;
				ModuleBuilder.state.current_view.successCall = successCall;
			}

			if (Ext.Ajax.isLoading()) {
				//ajaxStatus.showStatus(SUGAR.language.get('ModuleBuilder', 'LBL_AJAX_TIME_DEPENDENT'));
				return;
			}
			ModuleBuilder.contentURL =  url;
			ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_LOADING_PAGE'));
			if (typeof(successCall) != 'function') {
				successCall = ModuleBuilder.updateContent;
			}
			Ext.Ajax.request({
				params: url,
				success: successCall,
				failure: ModuleBuilder.failed
			});
		},
		updateContent: function(o){
			ajaxStatus.flashStatus(SUGAR.language.get('app_strings', 'LBL_REQUEST_PROCESSED'), 2000);
			var ajaxResponse = Ext.util.JSON.decode((o.responseText));
			//Randomplaying around code
			if (ajaxResponse.tpl){
				var t = new Ext.XTemplate(ajaxResponse.tpl);
				ModuleBuilder.ajaxData = ajaxResponse.data;
				t.compile();
				t.overwrite(Ext.getCmp('center').body.dom, ajaxResponse.data, false);
				SUGAR.util.evalScript(t.applyTemplate(ajaxResponse.data));
				Ext.ux.History.navigate('mbContent', ModuleBuilder.contentURL);
				return true;
			}
			for (var maj in ajaxResponse) {
				var name = 'mb' + maj;
				var comp = Ext.getCmp(maj);
				if (name == 'mbwest') { //refresh package_tree!
					var newPanel = createTreePanel(ajaxResponse.west.content.tree_data);
					var newNodes = newPanel.getRootNode().childNodes;
					while (ModuleBuilder.tree.root.firstChild) {
						ModuleBuilder.tree.root.removeChild(ModuleBuilder.tree.root.firstChild);
					}
					while (newNodes.length > 0) {
						ModuleBuilder.tree.root.appendChild(newNodes[0]);
					}
					newPanel.destroy();
				}
				else {
					if (!comp) {
						if(ajaxResponse[maj].action == 'deactivate') continue;
						comp = new Ext.Panel({
							html: "<div class='bodywrapper'>" + ((maj == 'center') ? "<div>" + ajaxResponse[maj].crumb + "</div>" :"")
								 + ajaxResponse[maj].content + "</div>",
							title: ajaxResponse[maj].title,
							id: maj,
							autoScroll: true,
							closable: true
						});
						//if we don't unregister, IE will complain.
						comp.on('beforedestroy', function(panel){
							Ext.dd.ScrollManager.unregister(panel.body);
							if(tinyMCE && tinyMCE.selectedInstance) {
								tinyMCE.removeMCEControl(tinyMCE.selectedInstance.editorId);
							}
						});
						ModuleBuilder.tabPanel.add(comp);
						comp.show();
					} else {
						//Store Center pane changes in browser history
						if (name == 'mbcenter') {
							Ext.ux.History.navigate('mbContent', ModuleBuilder.contentURL);
							ModuleBuilder.history.cleanup();
							ModuleBuilder.closeAllTabs();
						}
						var el
						if (!comp.body) {
							comp.show();
						}
						el = comp.body.dom
						el.innerHTML = "<div class='bodywrapper'><div>" + ajaxResponse[maj].crumb + "</div>" + ajaxResponse[maj].content + "</div>";
					}
					SUGAR.util.evalScript(ajaxResponse[maj].content);
					Ext.dd.ScrollManager.register(comp.body);
				}
				switch (ajaxResponse[maj].action) {
					case 'activate':
						comp.show();
						break;
					case 'deactivate':
						ModuleBuilder.tabPanel.remove(comp);
						comp.destroy();
						Ext.getCmp('center').show();
				}
				if (ajaxResponse[maj].title && ajaxResponse[maj].title != 'no_change') {
					comp.setTitle(ajaxResponse[maj].title);
				}
				ModuleBuilder.history.update();
			}
		},
		submitForm: function(formname, successCall){
			if (Ext.Ajax.isLoading()) {
				ajaxStatus.showStatus(SUGAR.language.get('ModuleBuilder', 'LBL_AJAX_TIME_DEPENDENT'));
				return;
			}
			ajaxStatus.showStatus(SUGAR.language.get('ModuleBuilder', 'LBL_AJAX_LOADING'));
			if (typeof(successCall) == 'undefined') {
				successCall = ModuleBuilder.updateContent;
			}
			else {
				ModuleBuilder.callLock = true;
			}
			Ext.Ajax.request({
				params: Ext.Ajax.serializeForm(formname),
				success: successCall,
				failure: ModuleBuilder.failed
			});
		},
		setMode: function(reqMode){
			ModuleBuilder.mode = reqMode;
		},
		main: function(type){
			document.location.href = 'index.php?module=ModuleBuilder&action=index&type=' + type;
		},
		failed: function(o){
			ajaxStatus.flashStatus(SUGAR.language.get('ModuleBuilder', 'LBL_AJAX_FAILED_DATA'), 2000);
		},
		//Wizard Functions
		buttonDown: function(button, name, list){
			if (typeof(name) != 'undefined') {
				for (i in ModuleBuilder.buttons[list]) {
					ModuleBuilder.buttons[list][i].className = 'wizardButton';
				}
				ModuleBuilder.buttonSelect(name, list);
			}
			button.className = 'wizardButtonDown';

		},
		buttonOver: function(button){
			button.className = 'buttonOn';
		},
		buttonOut: function(button, name, list){
			if (typeof(name) != 'undefined') {
				if (ModuleBuilder.buttonGetSelected(list) != name) {
					button.className = 'wizardButton'
				}
			}
			else {
				button.className = 'wizardButton'
			}

		},
		buttonAdd: function(id, name, list){
			if (typeof(ModuleBuilder.buttons[list]) == 'undefined') {
				ModuleBuilder.buttons[list] = {};

			}
			ModuleBuilder.buttons[list][name] = document.getElementById(id);

		},
		buttonGetSelected: function(list){
			if (typeof(ModuleBuilder.selected[list]) == 'undefined') {
				return false;
			}
			return ModuleBuilder.selected[list];
		},
		buttonSelect: function(name, list){
			ModuleBuilder.selected[list] = name;
		},
		buttonToForm: function(form, field, list){
			var theField = eval('document.' + form + '.' + field);
			theField.value = ModuleBuilder.buttonGetSelected(list);
		},

		getTitle: function(title, breadCrumb){
			return "<h2>" + title + "</h2><br>" + breadCrumb;
		},
		closeAllTabs: function() {
			for (var i = ModuleBuilder.tabPanel.items.getCount() - 1; i > -1; i--) {
				var tab = ModuleBuilder.tabPanel.items.get(i);
				if (tab.closable) {
					ModuleBuilder.tabPanel.remove(tab);
				}
			}
		},
		//Help Functions
		helpRegister: function(name){
			var formname = 'document.' + name;
			var form = eval(formname);
			var i = 0;
			for (i = 0; i < form.elements.length; i++) {
				if (typeof(form.elements[i].type) != 'undefined' && typeof(form.elements[i].name) != 'undefined' && form.elements[i].type != 'hidden') {
					form.elements[i].onmouseover = function(){
						ModuleBuilder.helpToggle(this.name)
					};
					form.elements[i].onmouseout = function(){
						ModuleBuilder.helpToggle('default')
					};

				}
			}
		},
		helpUnregisterByID: function (id){
			var elm = document.getElementById(id);
			elm.onmouseover = function() {};
			elm.onmouseout = function() {};
			return;
		},
		helpRegisterByID: function(name, tag){
			var parent = document.getElementById(name);
			var children = parent.getElementsByTagName(tag);
			for (var i = 0; i < children.length; i++) {
				if (children[i].id != 'undefined') {
					children[i].onmouseover = function(){
						ModuleBuilder.helpToggle(this.id)
					};
					//children[i].onmouseover = function(){alert(this.id)};
					children[i].onmouseout = function(){
						ModuleBuilder.helpToggle('default')
					};
				}
			}
		},
		helpSetup: function(group, def, panel){
			if (!ModuleBuilder.panelHelp) ModuleBuilder.panelHelp = [];
			//console.log("helpsetup Called with " + group + " : " + def + " : " + panel);
			if (!panel) {
				panel = 'center';
			}
			var helpLang = SUGAR.language.get('ModuleBuilder', 'help');
			if (group && def) {
				ModuleBuilder.panelHelp[panel] = {lang: helpLang[group],def: def};
				ModuleBuilder.helpLang = helpLang[group];
				ModuleBuilder.helpDefault = def;
			} else if (ModuleBuilder.panelHelp[panel]){
				ModuleBuilder.helpLang = ModuleBuilder.panelHelp[panel].lang;
				ModuleBuilder.helpDefault = ModuleBuilder.panelHelp[panel].def;
			}
			ModuleBuilder.helpToggle('default');
		},
		helpSetupAssistant: function(group, subgroup){
			helpLang = SUGAR.language.get('ModuleBuilder', 'assistantHelp');

		},
		helpToggle: function(name){
			if (name == 'default')
				name = ModuleBuilder.helpDefault;
			if (ModuleBuilder.helpLang != null && typeof(ModuleBuilder.helpLang[name]) != 'undefined') {
				document.getElementById('mbhelp').innerHTML = ModuleBuilder.helpLang[name];
			}
		},
		handleSave: function(form, callBack){
			if (check_form(form)) {
				ModuleBuilder.state.isDirty=false;
				ModuleBuilder.submitForm(form, callBack);
			}
		},
		//Tree Functions
		handleTreeClick: function(node, event) {
			ModuleBuilder.getContent(node.attributes.action);
		},
		treeSubscribe:function(tree){
			debugger;
			tree.subscribe("labelClick", ModuleBuilder.treeLabelClick);
		},
		treeRefresh:function(type){
			ModuleBuilder.getContent('module=ModuleBuilder&action=ViewTree&tree=' + type);
		},
		//MB Specific
		addModule: function(MBpackage){
			ModuleBuilder.getContent('module=ModuleBuilder&action=module&view_package=' + MBpackage);
		},
		viewModule: function(MBpackage, module){
			ModuleBuilder.getContent('module=ModuleBuilder&action=module&view_package=' + MBpackage + '&view_module=' + module);
		},
		packageDelete: function(MBpackage){
			ajaxStatus.showStatus(SUGAR.language.get('ModuleBuilder', 'LBL_AJAX_DELETING'));
			if (confirm(SUGAR.language.get('ModuleBuilder', 'LBL_JS_REMOVE_PACKAGE'))) {
				ModuleBuilder.getContent('module=ModuleBuilder&action=DeletePackage&package=' + MBpackage);
				ModuleBuilder.tree.root.removeChild(ModuleBuilder.tree.getNodeById('package_tree/' + MBpackage));
			}
		},
		packagePublish: function(form){
			if (check_form(form)) {
				ajaxStatus.showStatus(SUGAR.language.get('ModuleBuilder', 'LBL_AJAX_BUILDPROGRESS'));
				ModuleBuilder.submitForm(form, ModuleBuilder.packageBuild);
			}
		},
		packageBuild: function(o){
			//make sure no user changes were made
			document.CreatePackage.action.value = 'BuildPackage';
			document.CreatePackage.submit();
			ModuleBuilder.callLock = false;
		},
		packageDeploy: function(form){
			if (check_form(form)) {
				ajaxStatus.showStatus(SUGAR.language.get('ModuleBuilder', 'LBL_AJAX_DEPLOYPROGRESS'));
				ModuleBuilder.submitForm(form, ModuleBuilder.packageInstall);
			}
		},
		packageInstall: function(o){
			//make sure no user changes were made
			document.CreatePackage.action.value = 'displaydeploy';
			ModuleBuilder.callLock = false;
			ModuleBuilder.submitForm('CreatePackage');
		},
		beginDeploy: function(p){
			Ext.Ajax.request({
				params: 'module=ModuleBuilder&action=DeployPackage&package=' + p,
				method: 'GET',
				success: ModuleBuilder.deployComplete,
				failure: ModuleBuilder.failed
			});
		},
		deployComplete: function(data){
			ModuleBuilder.getContent('module=ModuleBuilder&action=package&package=');
			Ext.Ajax.request({
				params: 'module=Administration&action=RebuildRelationship&silent=true',
				method: 'GET',
				failure: ModuleBuilder.failed
			});
			Ext.Ajax.request({
				params: 'module=Administration&action=RebuildDashlets&silent=true',
				method: 'GET',
				failure: ModuleBuilder.failed				
			});
		},
		packageExport: function(form){
			if (check_form(form)) {
				ajaxStatus.showStatus(SUGAR.language.get('ModuleBuilder', 'LBL_AJAX_BUILDPROGRESS'));
				ModuleBuilder.submitForm(form, ModuleBuilder.packageExportProject);
			}
		},
		packageExportProject: function(o){
			//make sure no user changes were made
			document.CreatePackage.action.value = 'ExportPackage';
			document.CreatePackage.submit();
			ModuleBuilder.callLock = false;
		},
		moduleDelete: function(MBpackage, module){
			ajaxStatus.showStatus(SUGAR.language.get('ModuleBuilder', 'LBL_AJAX_DELETING'));
			if (confirm(SUGAR.language.get('ModuleBuilder', 'LBL_JS_REMOVE_PACKAGE'))) {
				ModuleBuilder.getContent('module=ModuleBuilder&action=DeleteModule&package=' + MBpackage + '&view_module=' + module);
				ModuleBuilder.tree.root.removeChild(ModuleBuilder.tree.getNodeById('package_tree/' + MBpackage + '/' + module));
			}
		},
		moduleViewFields: function(o){

			ModuleBuilder.callLock = false;

			ModuleBuilder.getContent('module=ModuleBuilder&action=modulefields&view_package=' + ModuleBuilder.MBpackage + '&view_module=' + ModuleBuilder.module);
		},
		moduleLoadField: function(name, type){
			if (typeof(type) == 'undefined')
				type = 0;
			if (typeof(formsWithFieldLogic) != 'undefined')
				formsWithFieldLogic = 'undefined';
			ModuleBuilder.getContent('module=ModuleBuilder&action=modulefield&view_package=' + ModuleBuilder.MBpackage + '&view_module=' + ModuleBuilder.module + '&field=' + name + '&type=' + type);
		},
		moduleLoadLabels: function(type){
			if (typeof(type) == 'undefined')
				type = 0;
			else
				if (type == "studio") {
					ModuleBuilder.getContent('module=ModuleBuilder&action=editLabels&view_module=' + ModuleBuilder.module);
				}
				else
					if (type == "mb") {
						ModuleBuilder.getContent('module=ModuleBuilder&action=modulelabels&view_package=' + ModuleBuilder.MBpackage + '&view_module=' + ModuleBuilder.module + '&type=' + type);
					}
		},
		moduleViewRelationships: function(o){
			ModuleBuilder.callLock = false;
			ModuleBuilder.getContent('module=ModuleBuilder&action=relationships&view_package=' + ModuleBuilder.MBpackage + '&view_module=' + ModuleBuilder.module);
		},
		moduleLoadRelationship2: function(name, resetLabel) {
			if (resetLabel && Ext.getDom('rhs_label')) {
				Ext.getDom('rhs_label').value = "";
			}
			var panel = Ext.getCmp('relEditor');
			if (!panel) {
				panel = new Ext.Panel({
					id:'relEditor',
					title: SUGAR.language.get('ModuleBuilder', 'LBL_RELATIONSHIP_EDIT'),
					autoScroll: true,
					autoHeight: true,
					closable: true
				});
				ModuleBuilder.tabPanel.add(panel);
			}
			if (name == "") {
				name = Ext.getDom('rel_name_id') ? Ext.getDom('rel_name_id').value : "";
			}

			panel.show();
			var rtField = Ext.getDom('relationship_type_field');
			var relType = rtField ? rtField.options[rtField.selectedIndex].value: "";
			panel.load({
				url: 'index.php',
				params: {
					module: 'ModuleBuilder',
					action: 'relationship',
					view_package: ModuleBuilder.MBpackage,
					view_module: ModuleBuilder.module,
					relationship_name: name,
					relationship_type: relType,
					lhs_module: Ext.getDom('lhs_mod_field') ? Ext.getDom('lhs_mod_field').value : "",
					rhs_module: Ext.getDom('rhs_mod_field') ? Ext.getDom('rhs_mod_field').value : "",
					lhs_label: Ext.getDom('lhs_label') ? Ext.getDom('lhs_label').value : "",
					rhs_label: Ext.getDom('rhs_label') ? Ext.getDom('rhs_label').value : "",
					json: false
				},
				scripts: true
			});
		},
		moduleDropDown: function(name, field){
			ModuleBuilder.getContent('module=ModuleBuilder&action=dropdown&view_package=' + ModuleBuilder.MBpackage + '&view_module=' + ModuleBuilder.module + '&dropdown_name=' + name + '&field=' + field);
		},
		moduleViewLayouts: function(o){
			ModuleBuilder.callLock = false;
			ModuleBuilder.getContent('module=ModuleBuilder&MB=1&action=wizard&view_package=' + ModuleBuilder.MBpackage + '&view_module=' + ModuleBuilder.module);
		},
		autoSetLayout: function(){
			var mbEl = ModuleBuilder.mainPanel.getEl();
			var newHeight = Ext.lib.Dom.getViewHeight() - mbEl.getY() - mbEl.parent().getPadding('tb') - 10;
			ModuleBuilder.mainPanel.setHeight(newHeight);
		},
		refreshGlobalDropDown: function(o){
			// just clear the callLock; the convention is that this is done in a handler rather than in updateContent
			ModuleBuilder.callLock = false;
			ModuleBuilder.updateContent(o);
		},
		refreshDropDown: function(){
			ModuleBuilder.callLock = false;
			document.popup_form.action.value = 'RefreshField';
			document.popup_form.new_dropdown.value = ModuleBuilder.refreshDD_name;
			SimpleList.refreshDD_name = '';
			ModuleBuilder.submitForm("popup_form");
		},
		dropdownChanged: function(value){
			var select = document.getElementById('default[]').options;
			while(select.length > 0) {
				select[0] = null;
			}
			Ext.Ajax.request({
				params: 'module=ModuleBuilder&action=get_app_list_string&key=' + value +
				'&view_package=' + ModuleBuilder.MBpackage + '&view_module=' + ModuleBuilder.module,
				method: 'GET',
				success: ModuleBuilder.dropdownChangedCallback,
				failure: ModuleBuilder.failed
			});
		},
		dropdownChangedCallback : function(o) {
			var ajaxResponse = Ext.util.JSON.decode((o.responseText));
			var select = document.getElementById('default[]').options;
			var count = 0;
			for (var key in ajaxResponse) {
				select[count] = new Option(ajaxResponse[key], key);
				count++;
			}
		},
		showPopup : function(title, params) {

		}
	};
	ModuleBuilder.buttons = {};
	ModuleBuilder.selected = {};
	ModuleBuilder.callLock = false;
}
