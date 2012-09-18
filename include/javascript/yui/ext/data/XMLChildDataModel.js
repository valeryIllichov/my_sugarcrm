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
YAHOO.ext.grid.XMLChildDataModel=function(schema,childSchema,xml){YAHOO.ext.grid.XMLDataModel.superclass.constructor.call(this,YAHOO.ext.grid.LoadableDataModel.XML);this.schema=schema;this.childSchema=childSchema;this.xml=xml;if(xml){this.loadData(xml);}};YAHOO.extendX(YAHOO.ext.grid.XMLChildDataModel,YAHOO.ext.grid.LoadableDataModel);YAHOO.ext.grid.XMLChildDataModel.prototype.loadData=function(doc,callback,keepExisting,insertIndex){this.xml=doc;var idField=this.schema.id;var fields=this.schema.fields;var childIdField=this.childSchema.id;var childFields=this.childSchema.fields;if(this.schema.totalTag){this.totalCount=null;var totalNode=doc.getElementsByTagName(this.schema.totalTag);if(totalNode&&totalNode.item(0)&&totalNode.item(0).firstChild){var v=parseInt(totalNode.item(0).firstChild.nodeValue,10);if(!isNaN(v)){this.totalCount=v;}}}
var rowData=[];var nodes=doc.getElementsByTagName(this.schema.tagName);if(nodes&&nodes.length>0){var idCount=0;for(var i=0;i<nodes.length;i++){var node=nodes.item(i);var colData=[];colData.node=node;colData.id=String(idCount);colData.isParent=true;colData.parentId=colData.id;for(var j=0;j<fields.length;j++){var val=this.getNamedValue(node,fields[j],"");if(this.preprocessors[j]){val=this.preprocessors[j](val);}
colData.push(val);}
idCount++;var childNodes=node.getElementsByTagName(this.childSchema.tagName);var children=[];if(childNodes&&childNodes.length>0){for(var k=0;k<childNodes.length;k++){var childNode=childNodes.item(k);var childData=[];childData.node=childNode;childData.id=String(idCount);childData.isParent=false;childData.parentId=colData.id;for(var l=0;l<childFields.length;l++){var val=this.getNamedValue(childNode,childFields[l],"");if(this.preprocessors[l]){val=this.preprocessors[l](val);}
childData.push(val);}
idCount++;children.push(childData);}
colData.children=children;}
rowData.push(colData);if(colData.children){for(var p=0;p<colData.children.length;p++){rowData.push(colData.children[p]);}}}}
if(keepExisting!==true){YAHOO.ext.grid.XMLChildDataModel.superclass.removeAll.call(this);}
if(typeof insertIndex!='number'){insertIndex=this.getRowCount();}
YAHOO.ext.grid.XMLChildDataModel.superclass.insertRows.call(this,insertIndex,rowData);if(typeof callback=='function'){callback(this,true);}
this.fireLoadEvent();};YAHOO.ext.grid.XMLChildDataModel.prototype.getRows=function(indexes){var data=this.data;var r=[];for(var i=0;i<indexes.length;i++){r.push(data[indexes[i]]);if(data[indexes[i]].isParent){for(var j=0;j<data[indexes[i]].children.length;j++){r.push(data[indexes[i]+j+1]);}}}
return r;};YAHOO.ext.grid.XMLChildDataModel.prototype.addRow=function(id,cellValues){var newIndex=this.getRowCount();var node=this.createNode(this.xml,id,cellValues);cellValues.id=id||newIndex;cellValues.node=node;YAHOO.ext.grid.XMLChildDataModel.superclass.addRow.call(this,cellValues);return newIndex;};YAHOO.ext.grid.XMLChildDataModel.prototype.insertRow=function(index,id,cellValues){var node=this.createNode(this.xml,id,cellValues);cellValues.id=id||this.getRowCount();cellValues.node=node;YAHOO.ext.grid.XMLChildDataModel.superclass.insertRow.call(this,index,cellValues);return index;};YAHOO.ext.grid.XMLChildDataModel.prototype.getRowById=function(id){for(var i=0;i<this.data.length;i++){if(this.data[i].id==id){return this.data[i];}}
return null;};YAHOO.ext.grid.XMLChildDataModel.prototype.getRowIndexById=function(id){for(var i=0;i<this.data.length;i++){if(this.data[i].id==id)
return i;}
return null;};YAHOO.ext.grid.XMLChildDataModel.prototype.removeRow=function(index){var node=this.data[index].node;var rIndex=0;if(this.data[index].isParent){for(var i=0;i<this.data[index].children.length;i++){var cNode=this.data[index+1].node;if(cNode.parentNode!=null)
cNode.parentNode.removeChild(cNode);YAHOO.ext.grid.XMLChildDataModel.superclass.removeRow.call(this,index+1,index+1);rIndex++;}}else{var topNode=this.getRowById(this.data[index].parentId);if(topNode){var newChildren=[];for(var i=0;i<topNode.children.length;i++){if(this.data[index].id!=topNode.children[i].id){newChildren.push(topNode.children[i]);}}
topNode.children=newChildren;}}
if(node.parentNode!=null)
node.parentNode.removeChild(node);YAHOO.ext.grid.XMLChildDataModel.superclass.removeRow.call(this,index,index);};YAHOO.ext.grid.XMLChildDataModel.prototype.getNode=function(rowIndex){return this.data[rowIndex].node;};YAHOO.ext.grid.XMLChildDataModel.prototype.createNode=function(xmlDoc,id,colData){var template=this.data[0].node;var newNode=template.cloneNode(true);var fields=this.schema.fields;for(var i=0;i<fields.length;i++){var nodeValue=colData[i];if(this.postprocessors[i]){nodeValue=this.postprocessors[i](nodeValue);}
this.setNamedValue(newNode,fields[i],nodeValue);}
if(id){this.setNamedValue(newNode,this.schema.idField,id);}
template.parentNode.appendChild(newNode);return newNode;};YAHOO.ext.grid.XMLChildDataModel.prototype.getNamedValue=function(node,name,defaultValue){if(!node||!name){return defaultValue;}
var nodeValue=defaultValue;var attrNode=node.attributes.getNamedItem(name);if(attrNode){nodeValue=attrNode.value;}else{var childNode=node.getElementsByTagName(name);if(childNode&&childNode.item(0)&&childNode.item(0).firstChild){nodeValue=childNode.item(0).firstChild.nodeValue;}else{var index=name.indexOf(':');if(index>0){return this.getNamedValue(node,name.substr(index+1),defaultValue);}}}
return nodeValue;};YAHOO.ext.grid.XMLChildDataModel.prototype.setNamedValue=function(node,name,value){if(!node||!name){return;}
var attrNode=node.attributes.getNamedItem(name);if(attrNode){attrNode.value=value;return;}
var childNode=node.getElementsByTagName(name);if(childNode&&childNode.item(0)&&childNode.item(0).firstChild){childNode.item(0).firstChild.nodeValue=value;}else{var index=name.indexOf(':');if(index>0){this.setNamedValue(node,name.substr(index+1),value);}}};YAHOO.ext.grid.XMLChildDataModel.prototype.setValueAt=function(value,rowIndex,colIndex){var node=this.data[rowIndex].node;if(node){var nodeValue=value;if(this.postprocessors[colIndex]){nodeValue=this.postprocessors[colIndex](value);}
this.setNamedValue(node,this.schema.fields[colIndex],nodeValue);}
YAHOO.ext.grid.XMLDataModel.superclass.setValueAt.call(this,value,rowIndex,colIndex);};YAHOO.ext.grid.XMLChildDataModel.prototype.getRowId=function(rowIndex){return this.data[rowIndex].id;};YAHOO.ext.grid.XMLChildDataModel.prototype.getChildrenCount=function(){var count=0;for(var i=0;i<this.data.length;i++){if(!this.data[i].isParent)
count++;}
return count;};
