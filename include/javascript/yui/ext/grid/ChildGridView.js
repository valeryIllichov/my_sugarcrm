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
YAHOO.ext.grid.ChildGridView=function(){YAHOO.ext.grid.ChildGridView.superclass.constructor.call(this);};YAHOO.extendX(YAHOO.ext.grid.ChildGridView,YAHOO.ext.grid.GridView);YAHOO.ext.grid.ChildGridView.prototype.insertRows=function(dataModel,firstRow,lastRow,offset){var renderers=this.getColumnRenderers();var dindexes=this.getDataIndexes();var colCount=this.grid.colModel.getColumnCount();var beforeRow=null;var bt=this.getBodyTable();if(!offset){offset=0;}
if(firstRow+offset<bt.childNodes.length){beforeRow=bt.childNodes[firstRow];}
for(var rowIndex=firstRow;rowIndex<=lastRow;rowIndex++){var row=document.createElement('span');row.className='ygrid-row';row.style.top=(rowIndex*this.getRowHeight())+'px';this.renderRow(dataModel,row,rowIndex+offset,colCount,renderers,dindexes);if(beforeRow){bt.insertBefore(row,beforeRow);}else{bt.appendChild(row);}}
this.updateRowIndexes(firstRow);this.adjustForScroll();};YAHOO.ext.grid.ChildGridView.prototype.toggleClick=function(e){var selectedRows=this.grid.selModel.selectedRows;if(selectedRows.length>=1){var rowIndex=selectedRows[0].rowIndex;var html=selectedRows[0].innerHTML;var pattern='togglespan_(\\d)';var re=new RegExp(pattern);var match=re.exec(html);if(match){var rowId=match[1];var toggleSpan=document.getElementById('togglespan_'+rowId);if(toggleSpan.className=='collapsed'){toggleSpan.innerHTML="<img src='themes/Sugar/images/basic_search.gif' id='toggleImg_"+rowIndex+"'>";toggleSpan.className='expanded';this.insertRows(this.grid.dataModel,rowIndex+1,rowIndex+this.grid.dataModel.getRowById(rowId).children.length,this.grid.dataModel.getRowIndexById(rowId)-rowIndex);}else{toggleSpan.innerHTML="<img src='themes/Sugar/images/advanced_search.gif' id='toggleImg_"+rowIndex+"'>";toggleSpan.className='collapsed';this.deleteRows(this.grid.dataModel,rowIndex+1,rowIndex+this.grid.dataModel.getRowById(rowId).children.length);}}}};YAHOO.ext.grid.ChildGridView.prototype.renderRow=function(dataModel,row,rowIndex,colCount,renderers,dindexes){var toggletd=document.createElement('span');var togglespan=document.createElement('span');if(dataModel.data[rowIndex].isParent){togglespan.id='togglespan_'+dataModel.data[rowIndex].id;toggletd.appendChild(togglespan);togglespan.innerHTML="<img src='themes/Sugar/images/basic_search.gif' id='toggleImg_"+dataModel.data[rowIndex].id+"'>";togglespan.className='expanded';row.appendChild(toggletd);YAHOO.ext.EventManager.on('togglespan_'+dataModel.data[rowIndex].id,'click',this.toggleClick,this,true);}else{toggletd.appendChild(togglespan);row.appendChild(toggletd);}
for(var colIndex=0;colIndex<colCount;colIndex++){var td=document.createElement('span');td.className='ygrid-col ygrid-col-'+colIndex+(colIndex==colCount-1?' ygrid-col-last':'');td.columnIndex=colIndex;td.tabIndex=0;var span=document.createElement('span');span.className='ygrid-cell-text';td.appendChild(span);var val=renderers[colIndex](dataModel.getValueAt(rowIndex,dindexes[colIndex]),rowIndex,colIndex);if(val=='')val='&nbsp;';span.innerHTML=val;row.appendChild(td);}
YAHOO.ext.EventManager.on(row,'dblclick',this.showDialog,this,true);};YAHOO.ext.grid.ChildGridView.prototype.showDialog=function(e){dialog=new YAHOO.widget.SimpleDialog("dialog",{width:"300px",fixedcenter:true,visible:false,draggable:false,close:true,text:"Display Some documentation here",icon:YAHOO.widget.SimpleDialog.ICON_HELP,constraintoviewport:true});dialog.setHeader("Documentation");dialog.render('documentation-div');dialog.show();};YAHOO.ext.grid.ChildGridView.prototype.renderChildRows=function(data,rowIndex,bt){var childSpan=document.createElement('span');childSpan.id='childSpan_'+rowIndex;childSpan.style.display='block';for(var i=0;i<data.children.length;i++){var childRow=document.createElement('span');childRow.className='ygrid-row';childRow.style.top=((i*this.rowHeight))+'px';for(var j=0;j<2;j++){var td=document.createElement('span');td.className='ygrid-col ygrid-col-'+j+(j==2-1?' ygrid-col-last':'');td.columnIndex=j;td.tabIndex=0;var span=document.createElement('span');span.className='ygrid-cell-text';td.appendChild(span);var val=data.children[i][j];if(val=='')val='&nbsp;';span.innerHTML=val;childRow.appendChild(td);}
bt.appendChild(childRow);}};YAHOO.ext.grid.ChildGridView.prototype.deleteRows=function(dataModel,firstRow,lastRow){this.updateBodyHeight();this.grid.selModel.deselectRange(firstRow,lastRow);var bt=this.getBodyTable();var rows=[];for(var rowIndex=firstRow;rowIndex<=lastRow;rowIndex++){rows.push(bt.childNodes[rowIndex]);}
for(var i=0;i<rows.length;i++){if(rows[i])
bt.removeChild(rows[i]);rows[i]=null;}
rows=null;this.updateRowIndexes(firstRow);this.adjustForScroll();};
