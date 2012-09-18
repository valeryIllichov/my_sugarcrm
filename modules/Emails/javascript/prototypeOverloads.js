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


YAHOO.util.Connect.uploadFile = function(id, callback, uri, postData){
	// Each iframe has an id prefix of "yuiIO" followed
	// by the unique transaction id.
	var frameId = 'yuiIO' + id;
	var io = document.getElementById(frameId);


	/*
	 * SUGAR - adding try/catch to this block as IE7 has some issue with this the first time it is called.
	 */
	// Initialize the HTML form properties in case they are
	// not defined in the HTML form.
	try {
		this._formNode.action = null;
		this._formNode.action = uri;
	} catch(e) {
		
	}
	this._formNode.method = 'POST';
	this._formNode.target = frameId;

	if(this._formNode.encoding){
		// IE does not respect property enctype for HTML forms.
		// Instead use property encoding.
		this._formNode.encoding = 'multipart/form-data';
	}
	else{
		this._formNode.enctype = 'multipart/form-data';
	}

	if(postData){
		var oElements = this.appendPostData(postData);
	}

	this._formNode.submit();

	if(oElements && oElements.length > 0){
		try
		{
			for(var i=0; i < oElements.length; i++){
				this._formNode.removeChild(oElements[i]);
			}
		}
		catch(e){}
	}

	// Reset HTML form status properties.
	this.resetFormState();

	// Create the upload callback handler that fires when the iframe
	// receives the load event.  Subsequently, the event handler is detached
	// and the iframe removed from the document.

	var uploadCallback = function()
	{
		var obj = {};
		obj.tId = id;
		obj.argument = callback.argument;

		try
		{
			obj.responseText = io.contentWindow.document.body?io.contentWindow.document.body.innerHTML:null;
			obj.responseXML = io.contentWindow.document.XMLDocument?io.contentWindow.document.XMLDocument:io.contentWindow.document;
		}
		catch(e){}

		if(callback.upload){
			if(!callback.scope){
				callback.upload(obj);
			}
			else{
				callback.upload.apply(callback.scope, [obj]);
			}
		}

		if(YAHOO.util.Event){
			YAHOO.util.Event.removeListener(io, "load", uploadCallback);
		}
		else if(window.detachEvent){
			io.detachEvent('onload', uploadCallback);
		}
		else{
			io.removeEventListener('load', uploadCallback, false);
		}
		setTimeout(function(){ document.body.removeChild(io); }, 100);
	};


	// Bind the onload handler to the iframe to detect the file upload response.
	if(YAHOO.util.Event){
		YAHOO.util.Event.addListener(io, "load", uploadCallback);
	}
	else if(window.attachEvent){
		io.attachEvent('onload', uploadCallback);
	}
	else{
		io.addEventListener('load', uploadCallback, false);
	}
}


/**
 * Need by autocomplete library? 
 */
YAHOO.register = function(name,mainClass,data) {
	var mods = YAHOO.env.modules;
	if(!mods[name]) { 
		mods[name] = {versions:[],builds:[]};
	}
	
	var m = mods[name], v = data.version, b = data.build, ls = YAHOO.env.listeners;
	m.name = name;
	m.version = v;
	m.build = b;
	m.versions.push(v);
	m.builds.push(b);
	m.mainClass = mainClass;

	for(var i = 0; i<ls.length; i = i+1) { 
		ls[i](m);
	}
	
	if(mainClass) { 
		mainClass.VERSION = v;
		mainClass.BUILD = b;
	} else {
		YAHOO.log("mainClass is undefined for module "+name,"warn");
	}
};

YAHOO.lang = {
	isArray  :  function(obj) {
		if(obj  &&  obj.constructor && obj.constructor.toString().indexOf('Array')>-1) {
			return true;
		} else {
			return YAHOO.lang.isObject(obj) && obj.constructor == Array;
		}
	},
	
	isBoolean  :  function(obj) {
		return typeof obj == 'boolean';
	},
	
	isFunction : function(obj) {
		return typeof obj == 'function';
	},
	
	isNull : function(obj) {
		return obj == null;
	},
	
	isNumber : function(obj) {
		return typeof obj == 'number' && isFinite(obj);
	},
	
	isObject : function(obj) {
		return obj && (typeof obj == 'object'||YAHOO.lang.isFunction(obj));
	},
	
	isString : function(obj) {
		return typeof obj == 'string';
	},
	
	isUndefined : function(obj) {
		return typeof obj == 'undefined';
	},
	
	hasOwnProperty : function(obj,prop) {
		if(Object.prototype.hasOwnProperty) {
			return obj.hasOwnProperty(prop);
		}
		return !YAHOO.lang.isUndefined(obj[prop]) && obj.constructor.prototype[prop] !== obj[prop];
	},
	
	extend : function(subc,superc,overrides) {
		if(!superc||!subc) {
			throw new Error("YAHOO.lang.extend failed, please check that "+"all dependencies are included.");
		}
	}
};



/**
 * Adding some error checking since we're immediately transitioning to a modal dialogue in QuickCreate
 * and much of this class is unavailable
 
YAHOO.widget.Menu.prototype._getOffsetWidth = function() {
	if(this.element) {
	    var oClone = this.element.cloneNode(true);
	    
	    if(typeof(Dom) != 'undefined') {
		    Dom.setStyle(oClone, "width", "");
		    document.body.appendChild(oClone);
		    var sWidth = oClone.offsetWidth;
		    document.body.removeChild(oClone);
		    return sWidth;
	    }
	}
};*/


Ext.Template.prototype.applyTemplate = function(values){
        var myRE = /\{(\w+\.*\w*)\}/g;
        if(this.compiled){
            return this.compiled(values);
        }
        var useF = this.disableFormats !== true;
        var fm = Ext.util.Format, tpl = this;
        var fn = function(m, name, format, args){
            if(name.match(/\./g)) {
                var temp = name.split(".");
                values[name] = eval(temp[0] + "." + temp[1]);
                return values[name];
            }
            else if(format && useF && typeof(format) == "string") {
                if(format.substr(0, 5) == "this."){
                    return tpl.call(format.substr(5), values[name], values);
                }else{
                    if(args){
                        var re = /^\s*['"](.*)["']\s*$/;
                        args = args.split(',');
                        for(var i = 0, len = args.length; i < len; i++){
                            args[i] = args[i].replace(re, "$1");
                        }
                        args = [values[name]].concat(args);
                    }else{
                        args = [values[name]];
                    }
                    return fm[format].apply(fm, args);
                }
            }else{
                return values[name] !== undefined ? values[name] : "";
            }
        };
        return this.html.replace(myRE, fn);
    };


/*
 * Fixes an IE7 issue where this.dom.parentNode is null
*/
Ext.Element.prototype.remove = function() {
	if(this.dom.parentNode) {
        this.dom.parentNode.removeChild(this.dom);
        delete Ext.Element.cache[this.dom.id];
	}
}; 

//Adds translated strings to message boxes
Ext.onReady(function(){Ext.MessageBox.buttonText = {
    ok:     app_strings.LBL_EMAIL_OK, 
    cancel: app_strings.LBL_EMAIL_CANCEL, 
    yes:    app_strings.LBL_EMAIL_YES, 
    no:     app_strings.LBL_EMAIL_NO
}});

ContactsDragZone = function(view, config){
    this.view = view;
    this.view.dragz = this;
    this.contacts = [ ];
    ContactsDragZone.superclass.constructor.call(this, view.el, config);
};
    //This Class recognizes what elements in 
    //the contacts view are drag dropable and when
Ext.extend(ContactsDragZone, Ext.dd.DragZone, {
    setContacts : function(contactArray) {
        this.contacts = contactArray;
    },
    addContact : function(contactObject) {
        this.contacts.push(contactObject);
    },
    getContact : function(index) {
        return this.contacts[index];
    },
    getContactFromEmail : function(emailId) {
        for(i in this.contacts) {
            var contact = this.contacts[i];
            for(j in contact.email) {
                if (contact.email[j].id && contact.email[j].id == emailId) {
                    return contact;
                }
            }
        }
        return null;
    },
    getPrimaryEmail : function(contactId) {
        var contact = this.contacts[contactId];
        for(j in contact.email) {
            if (contact.email[j].id && contact.email[j].primary_address == "1") {
                return contact.email[j];
            }
        }
        return null;
    },
    toggleContact : function(view, index, node, e) {
        e = Ext.EventObject.setEvent(e);
        e.preventDefault();
        if (node.className.indexOf('address-contact') > -1) {
            var expNode = view.getNode('ex' + node.id);
            expNode.style.display = "";
            var contact = view.dragz.getContact(node.id);
            for(i in contact.email) {
                if (contact.email[i].id) {
                    view.getNode(contact.email[i].id).style.display = "";
                }
            }
            var i = 0;
            node.style.display="none";
            view.clearSelections(true);
            view.select(expNode);
        }
        //Collapse Expanded Contact
        else if (node.className.indexOf('address-exp-contact') > -1) {
            var origId = node.id.substring(2);
            var colNode = view.getNode(origId);
            colNode.style.display = "";
            var contact = view.dragz.getContact(origId);
            for(i in contact.email) {
                if (contact.email[i].id) {
                    view.getNode(contact.email[i].id).style.display = "none";
                }
            }
            node.style.display="none";
            view.clearSelections(true);
            view.select(colNode);
        }
    },
    toggleList : function(view, index, node, e) {
        e = Ext.EventObject.setEvent(e);
        e.preventDefault();
        if (node.className.indexOf('address-list') > -1) {
            var length = node.getAttribute('size');
            if (length > 0) {
                var emails = view.getNodes(index + 1, index + parseInt(length));
                for(var i=0; i < emails.length; i++) {
                    if (emails[i]) {
                        emails[i].style.display = (emails[i].style.display == 'none') ? '' : 'none';
                    }
                }
            }
        }
        view.clearSelections(true);
        view.select(node);
    },
    //initizlized Drag-Drop objects and proxies. 
    getDragData : function(e){
        e = Ext.EventObject.setEvent(e);
        e.preventDefault();
        var target = e.getTarget('.address-contact') || e.getTarget('.address-email');
        if(target){
            var view = this.view;
            if(!view.isSelected(target)){
                view.select(target, e.ctrlKey);
            }
            var selNodes = view.getSelectedNodes();
            var dragData = { }
            if(selNodes.length == 1){
                dragData.nodes = target;
                var proxy = document.createElement('div');
                if (target.className.indexOf('address-contact') != -1) {
                    var email = view.getNode(view.dragz.getPrimaryEmail(target.id).id);
                    proxy.innerHTML =  target.innerHTML + email.innerHTML;
                } else {
                    var id = view.dragz.getContactFromEmail(target.id).id;
                    proxy.innerHTML = view.getNode(id).innerHTML + target.innerHTML;
                }
                dragData.ddel = proxy;
                dragData.single = true;
                
            }else{
                dragData.nodes = [];
                var div = document.createElement('div'); // create the multi element drag "ghost"
                div.className = 'contacts-multi-proxy';
                for(var i = 0, len = selNodes.length; i < len; i++){
                    if (selNodes[i].className.indexOf('address-exp-contact') < 0 && selNodes[i].style.display != "none") {
                        //Append the default email to a contact drag proxy
                        var proxy = document.createElement('div');
                        if (selNodes[i].className.indexOf('address-contact') != -1) {
                            var email = view.getNode(view.dragz.getPrimaryEmail(selNodes[i].id).id);
                            proxy.innerHTML =  selNodes[i].innerHTML + email.innerHTML;
                        } else {
                            var id = view.dragz.getContactFromEmail(selNodes[i].id).id;
                            proxy.innerHTML = view.getNode(id).innerHTML + selNodes[i].innerHTML;
                        }
                        div.appendChild(proxy);
                        dragData.nodes.push(selNodes[i]);
                    }
                }
                dragData.ddel = div;
                dragData.multi = true;
            }
            return dragData;
        }
        var target = e.getTarget('.address-list');
        if (target) {
            var view = this.view;
            if(!view.isSelected(target)){
                view.select(target, e.ctrlKey);
            }
            var selNodes = view.getSelectedNodes();
            var dragData = { }
            if(selNodes.length == 1){
                dragData.nodes = target;
                dragData.ddel = target;
                dragData.single = true;
            }
            else {
                dragData.nodes = [];
                var div = document.createElement('div'); // create the multi element drag "ghost"
                div.className = 'contacts-multi-proxy';
                for(var i = 0, len = selNodes.length; i < len; i++){
                    if (selNodes[i].className.indexOf('address-list') > -1) {
                        //Append the default email to a contact drag proxy
                        var proxy = document.createElement('div');
                        proxy.innerHTML = selNodes[i].innerHTML;
                        div.appendChild(proxy);
                        dragData.nodes.push(selNodes[i]);
                    }
                }
                dragData.ddel = div;
                dragData.multi = true;
            }
            return dragData;
        }
        return false;
    }
});

ListDropZone = function(view, config){
    this.view = view;
    ListDropZone.superclass.constructor.call(this, view.el, config);
};
    //This Class handels contacts dropped onto Mailing Lists
Ext.extend(ListDropZone, Ext.dd.DropZone, {
    getTargetFromEvent : function(e){
        e = Ext.EventObject.setEvent(e);
        //e.preventDefault();
        var target = e.getTarget('.address-list');
        if(target){
            var view = this.view;
            if(!view.isSelected(target)){
                view.select(target, e.ctrlKey);
            }
            return target;
        }
        return null;
    },
    onNodeOver : function(node, source, e, data ) {
        if (source.id == 'contacts') {
            return "x-dd-drop-ok-add";
        } else {
            return this.dropNotAllowed;
        }
    },
    onNodeDrop : function(node, source, e, data) {
        if (source.id == 'contacts') {
            SUGAR.email2.addressBook.addContactsToMailinglist(node, data);
        }
    }
});


/**
 * Workaround for sliding panel noted here: http://extjs.com/forum/showthread.php?t=11899
 */
Ext.override(Ext.SplitLayoutRegion, {
    initAutoHide: function(){
        if (this.autoHide !== false) {
            if (!this.autoHideHd) {
                var st = new Ext.util.DelayedTask(this.slideIn, this);
                this.autoHideHd = {
                    "mouseout": function(e){
                        if (!e.within(this.el, true)) {
                            var box = this.el.getBox();
                            if (e.getPageX() > -1 && e.getPageY() > -1 &&
							   (e.getPageX() < box.x || e.getPageX() > box.right ||
                                e.getPageY() < box.y || e.getPageY() > box.bottom)) {
                                st.delay(500);
                            }
                        }
                    },
                    "mouseover": function(e){
                        st.cancel();
                    },
                    scope: this
                };
            }
            this.el.on(this.autoHideHd);
        }
    }
});

 
/*
 * override tinyMCE methods for IE7 (specific to SugarCRM, since the normal code works fine in their examples)
 */ 
tinyMCE.loadScript = function(url) {
	var i;

	for (i=0; i<this.loadedFiles.length; i++) {
		if (this.loadedFiles[i] == url)
			return;
	}

	if (tinyMCE.settings.strict_loading_mode) {
		this.pendingFiles[this.pendingFiles.length] = url;
	} else if(this.isIE) {
		/*
		 * SUGARCRM - added this else if() clause
		 */
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = url;
		document.getElementsByTagName('head')[0].appendChild(script); 
	} else {
		document.write('<sc'+'ript language="javascript" type="text/javascript" src="' + url + '"></script>');
	}

	this.loadedFiles[this.loadedFiles.length] = url;
}	

tinyMCE.loadCSS = function(url) {
	var ar = url.replace(/\s+/, '').split(',');
	var lflen = 0, csslen = 0;
	var skip = false;
	var x = 0, i = 0, nl, le;

	for (x = 0,csslen = ar.length; x<csslen; x++) {
		if (ar[x] != null && ar[x] != 'null' && ar[x].length > 0) {
			/* Make sure it doesn't exist. */
			for (i=0, lflen=this.loadedFiles.length; i<lflen; i++) {
				if (this.loadedFiles[i] == ar[x]) {
					skip = true;
					break;
				}
			}

			if (!skip) {
				/*
				 * SUGARCRM - added "this.isIE || ..."
				 */
				if (this.isIE || tinyMCE.settings.strict_loading_mode) {
					nl = document.getElementsByTagName("head");

					le = document.createElement('link');
					le.setAttribute('href', ar[x]);
					le.setAttribute('rel', 'stylesheet');
					le.setAttribute('type', 'text/css');

					nl[0].appendChild(le);			
				} else
					document.write('<link href="' + ar[x] + '" rel="stylesheet" type="text/css" />');

				this.loadedFiles[this.loadedFiles.length] = ar[x];
			}
		}
	}		
}
