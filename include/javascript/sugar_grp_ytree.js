/*
 Copyright (c) 2006, Yahoo! Inc. All rights reserved.
 Code licensed under the BSD License:
 http://developer.yahoo.net/yui/license.txt
 version: 0.12.0
 
 THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED
 WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A
 PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR
 TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
 ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
YAHOO.widget.TreeView=function(id){if(id){this.init(id);}};YAHOO.widget.TreeView.prototype={id:null,_el:null,_nodes:null,locked:false,_expandAnim:null,_collapseAnim:null,_animCount:0,maxAnim:2,setExpandAnim:function(type){if(YAHOO.widget.TVAnim.isValid(type)){this._expandAnim=type;}},setCollapseAnim:function(type){if(YAHOO.widget.TVAnim.isValid(type)){this._collapseAnim=type;}},animateExpand:function(el,node){if(this._expandAnim&&this._animCount<this.maxAnim){var tree=this;var a=YAHOO.widget.TVAnim.getAnim(this._expandAnim,el,function(){tree.expandComplete(node);});if(a){++this._animCount;this.fireEvent("animStart",{"node":node,"type":"expand"});a.animate();}
return true;}
return false;},animateCollapse:function(el,node){if(this._collapseAnim&&this._animCount<this.maxAnim){var tree=this;var a=YAHOO.widget.TVAnim.getAnim(this._collapseAnim,el,function(){tree.collapseComplete(node);});if(a){++this._animCount;this.fireEvent("animStart",{"node":node,"type":"collapse"});a.animate();}
return true;}
return false;},expandComplete:function(node){--this._animCount;this.fireEvent("animComplete",{"node":node,"type":"expand"});},collapseComplete:function(node){--this._animCount;this.fireEvent("animComplete",{"node":node,"type":"collapse"});},init:function(id){this.id=id;if("string"!==typeof id){this._el=id;this.id=this.generateId(id);}
this.createEvent("animStart",this);this.createEvent("animComplete",this);this.createEvent("collapse",this);this.createEvent("expand",this);this._nodes=[];YAHOO.widget.TreeView.trees[this.id]=this;this.root=new YAHOO.widget.RootNode(this);},getEl:function(){if(!this._el){this._el=document.getElementById(this.id);}
return this._el;},draw:function(){var html=this.root.getHtml();this.getEl().innerHTML=html;this.firstDraw=false;},regNode:function(node){this._nodes[node.index]=node;},getRoot:function(){return this.root;},setDynamicLoad:function(fnDataLoader,iconMode){this.root.setDynamicLoad(fnDataLoader,iconMode);},expandAll:function(){if(!this.locked){this.root.expandAll();}},collapseAll:function(){if(!this.locked){this.root.collapseAll();}},getNodeByIndex:function(nodeIndex){var n=this._nodes[nodeIndex];return(n)?n:null;},getNodeByProperty:function(property,value){for(var i in this._nodes){var n=this._nodes[i];if(n.data&&value==n.data[property]){return n;}}
return null;},getNodesByProperty:function(property,value){var values=[];for(var i in this._nodes){var n=this._nodes[i];if(n.data&&value==n.data[property]){values.push(n);}}
return(values.length)?values:null;},removeNode:function(node,autoRefresh){if(node.isRoot()){return false;}
var p=node.parent;if(p.parent){p=p.parent;}
this._deleteNode(node);if(autoRefresh&&p&&p.childrenRendered){p.refresh();}
return true;},removeChildren:function(node){while(node.children.length){this._deleteNode(node.children[0]);}
node.childrenRendered=false;node.dynamicLoadComplete=false;if(node.expanded){node.collapse();}else{node.updateIcon();}},_deleteNode:function(node){this.removeChildren(node);this.popNode(node);},popNode:function(node){var p=node.parent;var a=[];for(var i=0,len=p.children.length;i<len;++i){if(p.children[i]!=node){a[a.length]=p.children[i];}}
p.children=a;p.childrenRendered=false;if(node.previousSibling){node.previousSibling.nextSibling=node.nextSibling;}
if(node.nextSibling){node.nextSibling.previousSibling=node.previousSibling;}
node.parent=null;node.previousSibling=null;node.nextSibling=null;node.tree=null;delete this._nodes[node.index];},toString:function(){return"TreeView "+this.id;},generateId:function(el){var id=el.id;if(!id){id="yui-tv-auto-id-"+YAHOO.widget.TreeView.counter;++YAHOO.widget.TreeView.counter;}
return id;},onExpand:function(node){},onCollapse:function(node){}};YAHOO.augment(YAHOO.widget.TreeView,YAHOO.util.EventProvider);YAHOO.widget.TreeView.nodeCount=0;YAHOO.widget.TreeView.trees=[];YAHOO.widget.TreeView.counter=0;YAHOO.widget.TreeView.getTree=function(treeId){var t=YAHOO.widget.TreeView.trees[treeId];return(t)?t:null;};YAHOO.widget.TreeView.getNode=function(treeId,nodeIndex){var t=YAHOO.widget.TreeView.getTree(treeId);return(t)?t.getNodeByIndex(nodeIndex):null;};YAHOO.widget.TreeView.addHandler=function(el,sType,fn){if(el.addEventListener){el.addEventListener(sType,fn,false);}else if(el.attachEvent){el.attachEvent("on"+sType,fn);}};YAHOO.widget.TreeView.removeHandler=function(el,sType,fn){if(el.removeEventListener){el.removeEventListener(sType,fn,false);}else if(el.detachEvent){el.detachEvent("on"+sType,fn);}};YAHOO.widget.TreeView.preload=function(prefix){prefix=prefix||"ygtv";var styles=["tn","tm","tmh","tp","tph","ln","lm","lmh","lp","lph","loading"];var sb=[];for(var i=0;i<styles.length;++i){sb[sb.length]='<span class="'+prefix+styles[i]+'">&#160;</span>';}
var f=document.createElement("div");var s=f.style;s.position="absolute";s.top="-1000px";s.left="-1000px";f.innerHTML=sb.join("");document.body.appendChild(f);YAHOO.widget.TreeView.removeHandler(window,"load",YAHOO.widget.TreeView.preload);};YAHOO.widget.TreeView.addHandler(window,"load",YAHOO.widget.TreeView.preload);YAHOO.widget.Node=function(oData,oParent,expanded){if(oData){this.init(oData,oParent,expanded);}};YAHOO.widget.Node.prototype={index:0,children:null,tree:null,data:null,parent:null,depth:-1,href:null,target:"_self",expanded:false,multiExpand:true,renderHidden:false,childrenRendered:false,dynamicLoadComplete:false,previousSibling:null,nextSibling:null,_dynLoad:false,dataLoader:null,isLoading:false,hasIcon:true,iconMode:0,_type:"Node",init:function(oData,oParent,expanded){this.data=oData;this.children=[];this.index=YAHOO.widget.TreeView.nodeCount;++YAHOO.widget.TreeView.nodeCount;this.expanded=expanded;this.createEvent("parentChange",this);if(oParent){oParent.appendChild(this);}},applyParent:function(parentNode){if(!parentNode){return false;}
this.tree=parentNode.tree;this.parent=parentNode;this.depth=parentNode.depth+1;if(!this.href){this.href="javascript:"+this.getToggleLink();}
if(!this.multiExpand){this.multiExpand=parentNode.multiExpand;}
this.tree.regNode(this);parentNode.childrenRendered=false;for(var i=0,len=this.children.length;i<len;++i){this.children[i].applyParent(this);}
this.fireEvent("parentChange");return true;},appendChild:function(childNode){if(this.hasChildren()){var sib=this.children[this.children.length-1];sib.nextSibling=childNode;childNode.previousSibling=sib;}
this.children[this.children.length]=childNode;childNode.applyParent(this);return childNode;},appendTo:function(parentNode){return parentNode.appendChild(this);},insertBefore:function(node){var p=node.parent;if(p){if(this.tree){this.tree.popNode(this);}
var refIndex=node.isChildOf(p);p.children.splice(refIndex,0,this);if(node.previousSibling){node.previousSibling.nextSibling=this;}
this.previousSibling=node.previousSibling;this.nextSibling=node;node.previousSibling=this;this.applyParent(p);}
return this;},insertAfter:function(node){var p=node.parent;if(p){if(this.tree){this.tree.popNode(this);}
var refIndex=node.isChildOf(p);if(!node.nextSibling){return this.appendTo(p);}
p.children.splice(refIndex+1,0,this);node.nextSibling.previousSibling=this;this.previousSibling=node;this.nextSibling=node.nextSibling;node.nextSibling=this;this.applyParent(p);}
return this;},isChildOf:function(parentNode){if(parentNode&&parentNode.children){for(var i=0,len=parentNode.children.length;i<len;++i){if(parentNode.children[i]===this){return i;}}}
return-1;},getSiblings:function(){return this.parent.children;},showChildren:function(){if(!this.tree.animateExpand(this.getChildrenEl(),this)){if(this.hasChildren()){this.getChildrenEl().style.display="";}}},hideChildren:function(){if(!this.tree.animateCollapse(this.getChildrenEl(),this)){this.getChildrenEl().style.display="none";}},getElId:function(){return"ygtv"+this.index;},getChildrenElId:function(){return"ygtvc"+this.index;},getToggleElId:function(){return"ygtvt"+this.index;},getEl:function(){return document.getElementById(this.getElId());},getChildrenEl:function(){return document.getElementById(this.getChildrenElId());},getToggleEl:function(){return document.getElementById(this.getToggleElId());},getToggleLink:function(){return"YAHOO.widget.TreeView.getNode(\'"+this.tree.id+"\',"+
this.index+").toggle()";},collapse:function(){if(!this.expanded){return;}
var ret=this.tree.onCollapse(this);if(false===ret){return;}
ret=this.tree.fireEvent("collapse",this);if(false===ret){return;}
if(!this.getEl()){this.expanded=false;return;}
this.hideChildren();this.expanded=false;this.updateIcon();},expand:function(){if(this.expanded){return;}
var ret=this.tree.onExpand(this);if(false===ret){return;}
ret=this.tree.fireEvent("expand",this);if(false===ret){return;}
if(!this.getEl()){this.expanded=true;return;}
if(!this.childrenRendered){this.getChildrenEl().innerHTML=this.renderChildren();}else{}
this.expanded=true;this.updateIcon();if(this.isLoading){this.expanded=false;return;}
if(!this.multiExpand){var sibs=this.getSiblings();for(var i=0;i<sibs.length;++i){if(sibs[i]!=this&&sibs[i].expanded){sibs[i].collapse();}}}
this.showChildren();},updateIcon:function(){if(this.hasIcon){var el=this.getToggleEl();if(el){el.className=this.getStyle();}}},getStyle:function(){if(this.isLoading){return"ygtvloading";}else{var loc=(this.nextSibling)?"t":"l";var type="n";if(this.hasChildren(true)||(this.isDynamic()&&!this.getIconMode())){type=(this.expanded)?"m":"p";}
return"ygtv"+loc+type;}},getHoverStyle:function(){var s=this.getStyle();if(this.hasChildren(true)&&!this.isLoading){s+="h";}
return s;},expandAll:function(){for(var i=0;i<this.children.length;++i){var c=this.children[i];if(c.isDynamic()){alert("Not supported (lazy load + expand all)");break;}else if(!c.multiExpand){alert("Not supported (no multi-expand + expand all)");break;}else{c.expand();c.expandAll();}}},collapseAll:function(){for(var i=0;i<this.children.length;++i){this.children[i].collapse();this.children[i].collapseAll();}},setDynamicLoad:function(fnDataLoader,iconMode){if(fnDataLoader){this.dataLoader=fnDataLoader;this._dynLoad=true;}else{this.dataLoader=null;this._dynLoad=false;}
if(iconMode){this.iconMode=iconMode;}},isRoot:function(){return(this==this.tree.root);},isDynamic:function(){var lazy=(!this.isRoot()&&(this._dynLoad||this.tree.root._dynLoad));return lazy;},getIconMode:function(){return(this.iconMode||this.tree.root.iconMode);},hasChildren:function(checkForLazyLoad){return(this.children.length>0||(checkForLazyLoad&&this.isDynamic()&&!this.dynamicLoadComplete));},toggle:function(){if(!this.tree.locked&&(this.hasChildren(true)||this.isDynamic())){if(this.expanded){this.collapse();}else{this.expand();}}},getHtml:function(){this.childrenRendered=false;var sb=[];sb[sb.length]='<div class="ygtvitem" id="'+this.getElId()+'">';sb[sb.length]=this.getNodeHtml();sb[sb.length]=this.getChildrenHtml();sb[sb.length]='</div>';return sb.join("");},getChildrenHtml:function(){var sb=[];sb[sb.length]='<div class="ygtvchildren"';sb[sb.length]=' id="'+this.getChildrenElId()+'"';if(!this.expanded){sb[sb.length]=' style="display:none;"';}
sb[sb.length]='>';if((this.hasChildren(true)&&this.expanded)||(this.renderHidden&&!this.isDynamic())){sb[sb.length]=this.renderChildren();}
sb[sb.length]='</div>';return sb.join("");},renderChildren:function(){var node=this;if(this.isDynamic()&&!this.dynamicLoadComplete){this.isLoading=true;this.tree.locked=true;if(this.dataLoader){setTimeout(function(){node.dataLoader(node,function(){node.loadComplete();});},10);}else if(this.tree.root.dataLoader){setTimeout(function(){node.tree.root.dataLoader(node,function(){node.loadComplete();});},10);}else{return"Error: data loader not found or not specified.";}
return"";}else{return this.completeRender();}},completeRender:function(){var sb=[];for(var i=0;i<this.children.length;++i){sb[sb.length]=this.children[i].getHtml();}
this.childrenRendered=true;return sb.join("");},loadComplete:function(){this.getChildrenEl().innerHTML=this.completeRender();this.dynamicLoadComplete=true;this.isLoading=false;this.expand();this.tree.locked=false;},getAncestor:function(depth){if(depth>=this.depth||depth<0){return null;}
var p=this.parent;while(p.depth>depth){p=p.parent;}
return p;},getDepthStyle:function(depth){return(this.getAncestor(depth).nextSibling)?"ygtvdepthcell":"ygtvblankdepthcell";},getNodeHtml:function(){return"";},refresh:function(){this.getChildrenEl().innerHTML=this.completeRender();if(this.hasIcon){var el=this.getToggleEl();if(el){el.className=this.getStyle();}}},toString:function(){return"Node ("+this.index+")";}};YAHOO.augment(YAHOO.widget.Node,YAHOO.util.EventProvider);YAHOO.widget.RootNode=function(oTree){this.init(null,null,true);this.tree=oTree;};YAHOO.extend(YAHOO.widget.RootNode,YAHOO.widget.Node,{getNodeHtml:function(){return"";},toString:function(){return"RootNode";},loadComplete:function(){this.tree.draw();}});YAHOO.widget.TextNode=function(oData,oParent,expanded){if(oData){this.init(oData,oParent,expanded);this.setUpLabel(oData);}};YAHOO.extend(YAHOO.widget.TextNode,YAHOO.widget.Node,{labelStyle:"ygtvlabel",labelElId:null,label:null,textNodeParentChange:function(){if(this.tree&&!this.tree.hasEvent("labelClick")){this.tree.createEvent("labelClick",this.tree);}},setUpLabel:function(oData){this.textNodeParentChange();this.subscribe("parentChange",this.textNodeParentChange);if(typeof oData=="string"){oData={label:oData};}
this.label=oData.label;if(oData.href){this.href=oData.href;}
if(oData.target){this.target=oData.target;}
if(oData.style){this.labelStyle=oData.style;}
this.labelElId="ygtvlabelel"+this.index;},getLabelEl:function(){return document.getElementById(this.labelElId);},getNodeHtml:function(){var sb=[];sb[sb.length]='<table border="0" cellpadding="0" cellspacing="0">';sb[sb.length]='<tr>';for(var i=0;i<this.depth;++i){sb[sb.length]='<td class="'+this.getDepthStyle(i)+'">&#160;</td>';}
var getNode='YAHOO.widget.TreeView.getNode(\''+
this.tree.id+'\','+this.index+')';sb[sb.length]='<td';sb[sb.length]=' id="'+this.getToggleElId()+'"';sb[sb.length]=' class="'+this.getStyle()+'"';if(this.hasChildren(true)){sb[sb.length]=' onmouseover="this.className=';sb[sb.length]=getNode+'.getHoverStyle()"';sb[sb.length]=' onmouseout="this.className=';sb[sb.length]=getNode+'.getStyle()"';}
sb[sb.length]=' onclick="javascript:'+this.getToggleLink()+'">';sb[sb.length]='&#160;';sb[sb.length]='</td>';sb[sb.length]='<td>';sb[sb.length]='<a';sb[sb.length]=' id="'+this.labelElId+'"';sb[sb.length]=' class="'+this.labelStyle+'"';sb[sb.length]=' href="javascript:set_selected_node(\''+this.tree.id+'\',\''+this.index+'\');'+this.href+'"';sb[sb.length]=' target="'+this.target+'"';sb[sb.length]=' onclick="return '+getNode+'.onLabelClick('+getNode+')"';if(this.hasChildren(true)){sb[sb.length]=' onmouseover="document.getElementById(\'';sb[sb.length]=this.getToggleElId()+'\').className=';sb[sb.length]=getNode+'.getHoverStyle()"';sb[sb.length]=' onmouseout="document.getElementById(\'';sb[sb.length]=this.getToggleElId()+'\').className=';sb[sb.length]=getNode+'.getStyle()"';}
sb[sb.length]=' >';sb[sb.length]=this.label;sb[sb.length]='</a>';sb[sb.length]='</td>';sb[sb.length]='</tr>';sb[sb.length]='</table>';return sb.join("");},onLabelClick:function(me){return me.tree.fireEvent("labelClick",me);},toString:function(){return"TextNode ("+this.index+") "+this.label;}});YAHOO.widget.MenuNode=function(oData,oParent,expanded){if(oData){this.init(oData,oParent,expanded);this.setUpLabel(oData);}
this.multiExpand=false;};YAHOO.extend(YAHOO.widget.MenuNode,YAHOO.widget.TextNode,{toString:function(){return"MenuNode ("+this.index+") "+this.label;}});YAHOO.widget.HTMLNode=function(oData,oParent,expanded,hasIcon){if(oData){this.init(oData,oParent,expanded);this.initContent(oData,hasIcon);}};YAHOO.extend(YAHOO.widget.HTMLNode,YAHOO.widget.Node,{contentStyle:"ygtvhtml",contentElId:null,content:null,initContent:function(oData,hasIcon){if(typeof oData=="string"){oData={html:oData};}
this.html=oData.html;this.contentElId="ygtvcontentel"+this.index;this.hasIcon=hasIcon;},getContentEl:function(){return document.getElementById(this.contentElId);},getNodeHtml:function(){var sb=[];sb[sb.length]='<table border="0" cellpadding="0" cellspacing="0">';sb[sb.length]='<tr>';for(var i=0;i<this.depth;++i){sb[sb.length]='<td class="'+this.getDepthStyle(i)+'">&#160;</td>';}
if(this.hasIcon){sb[sb.length]='<td';sb[sb.length]=' id="'+this.getToggleElId()+'"';sb[sb.length]=' class="'+this.getStyle()+'"';sb[sb.length]=' onclick="javascript:'+this.getToggleLink()+'"';if(this.hasChildren(true)){sb[sb.length]=' onmouseover="this.className=';sb[sb.length]='YAHOO.widget.TreeView.getNode(\'';sb[sb.length]=this.tree.id+'\','+this.index+').getHoverStyle()"';sb[sb.length]=' onmouseout="this.className=';sb[sb.length]='YAHOO.widget.TreeView.getNode(\'';sb[sb.length]=this.tree.id+'\','+this.index+').getStyle()"';}
sb[sb.length]='>&#160;</td>';}
sb[sb.length]='<td';sb[sb.length]=' id="'+this.contentElId+'"';sb[sb.length]=' class="'+this.contentStyle+'"';sb[sb.length]=' >';sb[sb.length]=this.html;sb[sb.length]='</td>';sb[sb.length]='</tr>';sb[sb.length]='</table>';return sb.join("");},toString:function(){return"HTMLNode ("+this.index+")";}});YAHOO.widget.TVAnim=function(){return{FADE_IN:"TVFadeIn",FADE_OUT:"TVFadeOut",getAnim:function(type,el,callback){if(YAHOO.widget[type]){return new YAHOO.widget[type](el,callback);}else{return null;}},isValid:function(type){return(YAHOO.widget[type]);}};}();YAHOO.widget.TVFadeIn=function(el,callback){this.el=el;this.callback=callback;};YAHOO.widget.TVFadeIn.prototype={animate:function(){var tvanim=this;var s=this.el.style;s.opacity=0.1;s.filter="alpha(opacity=10)";s.display="";var dur=0.4;var a=new YAHOO.util.Anim(this.el,{opacity:{from:0.1,to:1,unit:""}},dur);a.onComplete.subscribe(function(){tvanim.onComplete();});a.animate();},onComplete:function(){this.callback();},toString:function(){return"TVFadeIn";}};YAHOO.widget.TVFadeOut=function(el,callback){this.el=el;this.callback=callback;};YAHOO.widget.TVFadeOut.prototype={animate:function(){var tvanim=this;var dur=0.4;var a=new YAHOO.util.Anim(this.el,{opacity:{from:1,to:0.1,unit:""}},dur);a.onComplete.subscribe(function(){tvanim.onComplete();});a.animate();},onComplete:function(){var s=this.el.style;s.display="none";s.filter="alpha(opacity=100)";this.callback();},toString:function(){return"TVFadeOut";}};// End of File include/ytree/TreeView/TreeView.js
                                
/**
 * The check box marks a task complete.  It is a simulated form field
 * with three states ...
 * 0=unchecked, 1=some children checked, 2=all children checked
 * When a task is clicked, the state of the nodes and parent and children
 * are updated, and this behavior cascades.
 *
 * @extends YAHOO.widget.TextNode
 * @constructor
 * @param oData    {object}  A string or object containing the data that will
 *                           be used to render this node.
 * @param oParent  {Node}    This node's parent node
 * @param expanded {boolean} The initial expanded/collapsed state
 * @param checked  {boolean} The initial checked/unchecked state
 */
YAHOO.widget.TaskNode=function(oData,oParent,expanded,checked){if(oData){this.init(oData,oParent,expanded);this.setUpLabel(oData);this.setUpCheck(checked);}};YAHOO.widget.TaskNode.prototype=new YAHOO.widget.TextNode();YAHOO.widget.TaskNode.prototype.checked=false;YAHOO.widget.TaskNode.prototype.checkState=0;YAHOO.widget.TaskNode.prototype.cascadeUp=false;YAHOO.widget.TaskNode.prototype.taskNodeParentChange=function(){if(this.tree&&!this.tree.hasEvent("checkClick")){this.tree.createEvent("checkClick",this.tree);}};YAHOO.widget.TaskNode.prototype.setUpCheck=function(checked){if(checked&&checked===true){this.check();}
this.taskNodeParentChange();this.subscribe("parentChange",this.taskNodeParentChange);};YAHOO.widget.TaskNode.prototype.getCheckElId=function(){return"ygtvcheck"+this.index;};YAHOO.widget.TaskNode.prototype.getCheckEl=function(){return document.getElementById(this.getCheckElId());};YAHOO.widget.TaskNode.prototype.getCheckStyle=function(){return"ygtvcheck"+this.checkState;};YAHOO.widget.TaskNode.prototype.getCheckLink=function(){return"YAHOO.widget.TreeView.getNode(\'"+this.tree.id+"\',"+
this.index+").checkClick()";};YAHOO.widget.TaskNode.prototype.checkClick=function(){if(this.checkState===0){this.check();}else{this.uncheck();}
this.onCheckClick();this.tree.fireEvent("checkClick",this);};YAHOO.widget.TaskNode.prototype.onCheckClick=function(){};YAHOO.widget.TaskNode.prototype.updateParent=function(){var p=this.parent;if(!p||!p.updateParent){return;}
var somethingChecked=false;var somethingNotChecked=false;for(var i=0;i<p.children.length;++i){if(p.children[i].checked){somethingChecked=true;if(p.children[i].checkState==1){somethingNotChecked=true;}}else{somethingNotChecked=true;}}
if(somethingChecked){p.setCheckState((somethingNotChecked)?1:2);}else{p.setCheckState(0);}
p.updateCheckHtml();p.updateParent();};YAHOO.widget.TaskNode.prototype.updateCheckHtml=function(){if(this.parent&&this.parent.childrenRendered){this.getCheckEl().className=this.getCheckStyle();}};YAHOO.widget.TaskNode.prototype.setCheckState=function(state){this.checkState=state;this.checked=(state>0);};YAHOO.widget.TaskNode.prototype.check=function(){this.setCheckState(2);for(var i=0;i<this.children.length;++i){this.children[i].check();}
this.updateCheckHtml();if(this.cascadeUp)
this.updateParent();};YAHOO.widget.TaskNode.prototype.uncheck=function(){this.setCheckState(0);for(var i=0;i<this.children.length;++i){this.children[i].uncheck();}
this.updateCheckHtml();this.updateParent();};YAHOO.widget.TaskNode.prototype.getNodeHtml=function(){var sb=new Array();sb[sb.length]='<table border="0" cellpadding="0" cellspacing="0">';sb[sb.length]='<tr>';for(var i=0;i<this.depth;++i){sb[sb.length]='<td class="'+this.getDepthStyle(i)+'">&#160;</td>';}
sb[sb.length]='<td';sb[sb.length]=' id="'+this.getToggleElId()+'"';sb[sb.length]=' class="'+this.getStyle()+'"';if(this.hasChildren(true)){sb[sb.length]=' onmouseover="this.className=';sb[sb.length]='YAHOO.widget.TreeView.getNode(\'';sb[sb.length]=this.tree.id+'\','+this.index+').getHoverStyle()"';sb[sb.length]=' onmouseout="this.className=';sb[sb.length]='YAHOO.widget.TreeView.getNode(\'';sb[sb.length]=this.tree.id+'\','+this.index+').getStyle()"';}
sb[sb.length]=' onclick="javascript:'+this.getToggleLink()+'">&#160;';sb[sb.length]='</td>';sb[sb.length]='<td';sb[sb.length]=' id="'+this.getCheckElId()+'"';sb[sb.length]=' class="'+this.getCheckStyle()+'"';sb[sb.length]=' onclick="javascript:'+this.getCheckLink()+'">';sb[sb.length]='&#160;</td>';sb[sb.length]='<td>';sb[sb.length]='<a';sb[sb.length]=' id="'+this.labelElId+'"';sb[sb.length]=' class="'+this.labelStyle+'"';sb[sb.length]=' href="'+this.href+'"';sb[sb.length]=' target="'+this.target+'"';if(this.hasChildren(true)){sb[sb.length]=' onmouseover="document.getElementById(\'';sb[sb.length]=this.getToggleElId()+'\').className=';sb[sb.length]='YAHOO.widget.TreeView.getNode(\'';sb[sb.length]=this.tree.id+'\','+this.index+').getHoverStyle()"';sb[sb.length]=' onmouseout="document.getElementById(\'';sb[sb.length]=this.getToggleElId()+'\').className=';sb[sb.length]='YAHOO.widget.TreeView.getNode(\'';sb[sb.length]=this.tree.id+'\','+this.index+').getStyle()"';}
sb[sb.length]=' >';sb[sb.length]=this.label;sb[sb.length]='</a>';sb[sb.length]='</td>';sb[sb.length]='</tr>';sb[sb.length]='</table>';return sb.join("");};YAHOO.widget.TaskNode.prototype.toString=function(){return"TaskNode ("+this.index+") "+this.label;};// End of File include/ytree/TreeView/TaskNode.js
                                
/**
 * Javascript for Tree
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
function treeinit(tree,treedata,treediv,params){tree=new YAHOO.widget.TreeView(treediv);YAHOO.namespace(treediv).param=params;var root=tree.getRoot();var tmpNode;var data=treedata;for(nodedata in data){for(node in data[nodedata]){addNode(root,data[nodedata][node]);}}
tree.draw();}
function set_selected_node(treeid,nodeid){node=YAHOO.widget.TreeView.getNode(treeid,nodeid);if(typeof(node)!='undefined'){YAHOO.namespace(treeid).selectednode=node;}else{YAHOO.namespace(treeid).selectednode=null;}}
function addNode(parentnode,nodedef){var dynamicload=false;var dynamicloadfunction;var childnodes;var expanded=false;if(nodedef['data']!='undefined'){if(typeof(nodedef['custom'])!='undefined'){dynamicload=nodedef['custom']['dynamicload'];dynamicloadfunction=nodedef['custom']['dynamicloadfunction'];expanded=nodedef['custom']['expanded'];}
var tmpNode=new YAHOO.widget.TextNode(nodedef['data'],parentnode,expanded);if(dynamicload){tmpNode.setDynamicLoad(eval(dynamicloadfunction));}
if(typeof(nodedef['nodes'])!='undefined'){for(childnodes in nodedef['nodes']){addNode(tmpNode,nodedef['nodes'][childnodes]);}}}}
function node_click(treeid,target,targettype,functioname){node=YAHOO.namespace(treeid).selectednode;var url=site_url.site_url+"/index.php?entryPoint=TreeData&function="+functioname+construct_url_from_tree_param(node);var callback={success:function(o){var targetdiv=document.getElementById(o.argument[0]);targetdiv.innerHTML=o.responseText;SUGAR.util.evalScript(o.responseText);},failure:function(o){},argument:[target,targettype]}
var trobj=YAHOO.util.Connect.asyncRequest('GET',url,callback,null);}
function construct_url_from_tree_param(node){var treespace=YAHOO.namespace(node.tree.id);url="&PARAMT_depth="+node.depth;if(treespace!='undefined'){for(tparam in treespace.param){url=url+"&PARAMT_"+tparam+'='+treespace.param[tparam];}}
for(i=node.depth;i>=0;i--){var currentnode;if(i==node.depth){currentnode=node;}else{currentnode=node.getAncestor(i);}
url=url+"&PARAMN_id"+'_'+currentnode.depth+'='+currentnode.data.id;if(currentnode.data.param!='undefined'){for(nparam in currentnode.data.param){url=url+"&PARAMN_"+nparam+'_'+currentnode.depth+'='+currentnode.data.param[nparam];}}}
return url;}
function loadDataForNode(node,onCompleteCallback){var id=node.data.id;var url=site_url.site_url+"/index.php?entryPoint=TreeData&function=get_node_data"+construct_url_from_tree_param(node);var callback={success:function(o){node=o.argument[0];var nodes=JSON.parse(o.responseText);var tmpNode;for(nodedata in nodes){for(node1 in nodes[nodedata]){addNode(node,nodes[nodedata][node1]);}}
o.argument[1]();},failure:function(o){},argument:[node,onCompleteCallback]}
var trobj=YAHOO.util.Connect.asyncRequest('POST',url,callback,null);}// End of File include/ytree/treeutil.js
                                
