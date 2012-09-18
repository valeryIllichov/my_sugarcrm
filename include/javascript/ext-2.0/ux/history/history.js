/*
 Copyright (c) 2007, Yahoo! Inc. All rights reserved.
 Code licensed under the BSD License:
 http://developer.yahoo.net/yui/license.txt
 version: 2.4.1
 */
Ext.ux.History=function(){var _iframe=null;var _storageField=null;var _initialized=false;var _storageFieldReady=false;var _modules=[];var _fqstates=[];function _trim(str){return str.replace(/^\s*(\S*(\s+\S+)*)\s*$/,"$1");}
function _getHash(){var i,href;href=top.location.href;i=href.indexOf("#");return i>=0?href.substr(i+1):null;}
function _storeStates(){var moduleName;var moduleObj;var initialStates=[];var currentStates=[];for(moduleName in _modules){if(_modules.hasOwnProperty(moduleName)){moduleObj=_modules[moduleName];initialStates.push(moduleName+"="+moduleObj.initialState);currentStates.push(moduleName+"="+moduleObj.currentState);}}
_storageField.value=initialStates.join("&")+"|"+currentStates.join("&");if(Ext.isSafari){_storageField.value+="|"+_fqstates.join(",");}}
function _handleFQStateChange(fqstate){var i;var len;var moduleName;var moduleObj;var modules;var states;var tokens;var currentState;if(!fqstate){for(moduleName in _modules){if(_modules.hasOwnProperty(moduleName)){moduleObj=_modules[moduleName];moduleObj.currentState=moduleObj.initialState;moduleObj.onStateChange(unescape(moduleObj.currentState));}}
return;}
modules=[];states=fqstate.split("&");for(i=0,len=states.length;i<len;i++){tokens=states[i].split("=");if(tokens.length===2){moduleName=tokens[0];currentState=tokens[1];modules[moduleName]=currentState;}}
for(moduleName in _modules){if(_modules.hasOwnProperty(moduleName)){moduleObj=_modules[moduleName];currentState=modules[moduleName];if(!currentState||moduleObj.currentState!==currentState){moduleObj.currentState=currentState||moduleObj.initialState;moduleObj.onStateChange(unescape(moduleObj.currentState));}}}}
function _updateIFrame(fqstate){var html,doc;html='<html><body><div id="state">'+fqstate+'</div></body></html>';try{doc=_iframe.contentWindow.document;doc.open();doc.write(html);doc.close();return true;}catch(e){return false;}}
function _checkIframeLoaded(){var doc,elem,fqstate,hash;if(!_iframe.contentWindow||!_iframe.contentWindow.document){setTimeout(_checkIframeLoaded,10);return;}
doc=_iframe.contentWindow.document;elem=doc.getElementById("state");fqstate=elem?elem.innerText:null;hash=_getHash();setInterval(function(){var newfqstate,states,moduleName,moduleObj,newHash,historyLength;doc=_iframe.contentWindow.document;elem=doc.getElementById("state");newfqstate=elem?elem.innerText:null;newHash=_getHash();if(newfqstate!==fqstate){fqstate=newfqstate;_handleFQStateChange(fqstate);if(!fqstate){states=[];for(moduleName in _modules){if(_modules.hasOwnProperty(moduleName)){moduleObj=_modules[moduleName];states.push(moduleName+"="+moduleObj.initialState);}}
newHash=states.join("&");}else{newHash=fqstate;}
top.location.hash=newHash;hash=newHash;_storeStates();}else if(newHash!==hash){hash=newHash;_updateIFrame(newHash);}},50);_initialized=true;}
function _initialize(){var i;var len;var parts;var tokens;var moduleName;var moduleObj;var initialStates;var initialState;var currentStates;var currentState;var counter;var hash;_storageField=document.getElementById("yui_hist_field");parts=_storageField.value.split("|");if(parts.length>1){initialStates=parts[0].split("&");for(i=0,len=initialStates.length;i<len;i++){tokens=initialStates[i].split("=");if(tokens.length===2){moduleName=tokens[0];initialState=tokens[1];moduleObj=_modules[moduleName];if(moduleObj){moduleObj.initialState=initialState;}}}
currentStates=parts[1].split("&");for(i=0,len=currentStates.length;i<len;i++){tokens=currentStates[i].split("=");if(tokens.length>=2){moduleName=tokens[0];currentState=tokens[1];moduleObj=_modules[moduleName];if(moduleObj){moduleObj.currentState=currentState;}}}}
if(parts.length>2){_fqstates=parts[2].split(",");}
_storageFieldReady=true;if(Ext.isIE){_iframe=document.getElementById("yui_hist_iframe");_checkIframeLoaded();}else{counter=history.length;hash=_getHash();setInterval(function(){var state;var newHash;var newCounter;newHash=_getHash();newCounter=history.length;if(newHash!==hash){hash=newHash;counter=newCounter;_handleFQStateChange(hash);_storeStates();}else if(newCounter!==counter){hash=newHash;counter=newCounter;state=_fqstates[counter-1];_handleFQStateChange(state);_storeStates();}},50);_initialized=true;}}
return Ext.apply(new Ext.util.Observable(),{isReady:function(){return _initialized;},register:function(module,initialState,onStateChange,obj,override){var scope;var wrappedFn;if(typeof module!=="string"||_trim(module)===""||typeof initialState!=="string"||typeof onStateChange!=="function"){throw new Error("Missing or invalid argument passed to Ext.ux.History.register");}
if(_modules[module]){return;}
if(_initialized){throw new Error("All modules must be registered before calling Ext.ux.History.initialize");}
module=escape(module);initialState=escape(initialState);scope=null;if(override===true){scope=obj;}else{scope=override;}
wrappedFn=function(state){return onStateChange.call(scope,state,obj);};_modules[module]={name:module,initialState:initialState,currentState:initialState,onStateChange:wrappedFn};},initialize:function(stateField,histFrame){if(_initialized){return;}
if(typeof stateField==="string"){stateField=document.getElementById(stateField);}
if(!stateField||stateField.tagName!=="TEXTAREA"&&(stateField.tagName!=="INPUT"||stateField.type!=="hidden"&&stateField.type!=="text")){stateField=Ext.DomHelper.append(document.body,{tag:'input',id:'yui_hist_field',type:'hidden'},false);}
_storageField=stateField;if(Ext.isIE){if(typeof histFrame==="string"){histFrame=document.getElementById(histFrame);}
if(!histFrame||histFrame.tagName!=="IFRAME"){Ext.DomHelper.append(document.body,{tag:'iframe',id:'yui_hist_iframe',src:'javascript:document.open();document.write(&quot;'+new Date().getTime()+'&quot;);document.close();',style:'position:absolute;display:none;'});}
_iFrame=histFrame;}
Ext.onReady(_initialize);_initialized=true;},navigate:function(module,state){var states;if(typeof module!=="string"||typeof state!=="string"){throw new Error("Missing or invalid argument passed to Ext.ux.History.navigate");}
states={};states[module]=state;return Ext.ux.History.multiNavigate(states);},multiNavigate:function(states){var currentStates;var moduleName;var moduleObj;var currentState;var fqstate;var html;var doc;if(typeof states!=="object"){throw new Error("Missing or invalid argument passed to Ext.ux.History.multiNavigate");}
if(!_initialized){throw new Error("The Browser History Manager is not initialized");}
for(moduleName in states){if(!_modules[moduleName]){throw new Error("The following module has not been registered: "+moduleName);}}
currentStates=[];for(moduleName in _modules){if(_modules.hasOwnProperty(moduleName)){moduleObj=_modules[moduleName];if(_modules.hasOwnProperty(moduleName)){currentState=states[moduleName];}else{currentState=moduleObj.currentState;}
moduleName=escape(moduleName);currentState=escape(currentState);currentStates.push(moduleName+"="+currentState);}}
fqstate=currentStates.join("&");if(Ext.isIE){return _updateIFrame(fqstate);}else{top.location.hash=fqstate;if(Ext.isSafari){_fqstates[history.length]=fqstate;_storeStates();}}
return true;},getCurrentState:function(module){var moduleObj;if(typeof module!=="string"){throw new Error("Missing or invalid argument passed to Ext.ux.History.getCurrentState");}
if(!_storageFieldReady){throw new Error("The Browser History Manager is not initialized");}
moduleObj=_modules[module];if(!moduleObj){throw new Error("No such registered module: "+module);}
return unescape(moduleObj.currentState);},getBookmarkedState:function(module){var i;var len;var hash;var states;var tokens;var moduleName;if(typeof module!=="string"){throw new Error("Missing or invalid argument passed to Ext.ux.History.getBookmarkedState");}
idx=top.location.href.indexOf("#");hash=idx>=0?top.location.href.substr(idx+1):top.location.href;states=hash.split("&");for(i=0,len=states.length;i<len;i++){tokens=states[i].split("=");if(tokens.length===2){moduleName=tokens[0];if(moduleName===module){return unescape(tokens[1]);}}}
return null;},getQueryStringParameter:function(paramName,url){var i;var len;var idx;var queryString;var params;var tokens;url=url||top.location.href;idx=url.indexOf("?");queryString=idx>=0?url.substr(idx+1):url;idx=queryString.lastIndexOf("#");queryString=idx>=0?queryString.substr(0,idx):queryString;params=queryString.split("&");for(i=0,len=params.length;i<len;i++){tokens=params[i].split("=");if(tokens.length>=2){if(tokens[0]===paramName){return unescape(tokens[1]);}}}
return null;}});}();
