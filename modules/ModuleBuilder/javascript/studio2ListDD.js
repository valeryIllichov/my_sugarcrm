/**
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

 Studio2.ListDD = function(id, sGroup, fromOnly) {
 	if (typeof id == 'number') {
		id = id + "";
	}
	if (Ext.get(id)) {
		Studio2.ListDD.superclass.constructor.call(this, id, sGroup);
		Ext.get(this.getDragEl()).applyStyles({
			borderColor: "#FF0000",
			backgroundColor: "#e5e5e5",
			opacity: 0.76,
			filter: "alpha(opacity=76)"
		});
		this.fromOnly = fromOnly;
	}
};

Ext.extend(Studio2.ListDD, Ext.dd.DDProxy, {
	
    startDrag: function(x, y){
		var dragEl = Ext.get(this.getDragEl());
		var clickEl = Ext.get(this.getEl());
		
		this.parentID = clickEl.parent().id;
		dragEl.update(clickEl.dom.innerHTML);
		
		dragEl.addClass(clickEl.dom.className);
		dragEl.applyStyles(clickEl.getStyles('color', 'height'));
		dragEl.setStyle({border:"1px solid #aaa"});
		
		// save the style of the object 
		this.clickStyle = clickEl.getStyles('opacity', 'border', 'height', 'filter', 'zoom');
		if (typeof(this.clickStyle['border']) == 'undefined')
			this.clickStyle['border'] = "1px solid";
		this.clickContent = clickEl.dom.innerHTML;
		clickElRegion = clickEl.getRegion();
		//clickEl.style.height = (clickElRegion.bottom - clickElRegion.top) + 'px';
		clickEl.applyStyles({
			opacity : 0.5,
			filter : "alpha(opacity=10)",
			border : '2px dashed #cccccc'
		});
	},
	
	updateTabs: function(){
		studiotabs.moduleTabs = [];
		for (j = 0; j < studiotabs.slotCount; j++) {
		
			var ul = document.getElementById('ul' + j);
			studiotabs.moduleTabs[j] = [];
			items = ul.getElementsByTagName("li");
			for (i = 0; i < items.length; i++) {
				if (items.length == 1) {
					items[i].innerHTML = SUGAR.language.get('ModuleBuilder', 'LBL_DROP_HERE');
				}
				else if (items[i].innerHTML == SUGAR.language.get('ModuleBuilder', 'LBL_DROP_HERE')) {
					items[i].innerHTML = '';
				}
				studiotabs.moduleTabs[ul.id.substr(2, ul.id.length)][studiotabs.subtabModules[items[i].id]] = true;
			}	
		}	
	},
	
	endDrag: function(e){
		ModuleBuilder.state.isDirty=true;
		var clickEl = this.getEl();
		var clickExEl = Ext.get(clickEl);
		clickEl.innerHTML = this.clickContent;
		var p = clickEl.parentNode;
		if (p.id == 'trash') {
			p.removeChild(clickEl);
			this.lastNode = false;
			this.updateTabs();
			return;
		}
		for(var style in this.clickStyle) {
			if (typeof(this.clickStyle[style]) != 'undefined')
				clickExEl.setStyle(style, this.clickStyle[style]);
			else
				clickExEl.setStyle(style, '');
		}
		
		if (this.lastNode) {
			this.lastNode.id = 'addLS' + addListStudioCount;
			studiotabs.subtabModules[this.lastNode.id] = this.lastNode.module;
			yahooSlots[this.lastNode.id] = new Studio2.ListDD(this.lastNode.id, 'subTabs', false);
			addListStudioCount++;
			this.lastNode.style.opacity = 1;
			this.lastNode.style.filter = "alpha(opacity=100)";
		}
		this.lastNode = false;
		this.updateTabs();
	},
	onDragOver: function(e, id){
		// this.logger.debug(this.id.toString() + " onDragOver " + id);
		var el;
		if (this.lastNode) {
			this.lastNode.parentNode.removeChild(this.lastNode);
			this.lastNode = false;
		}
		if (id.substr(0, 7) == 'modSlot') {
			return;
		}
		el = Ext.get(id);
		dragEl = Ext.get(this.getDragEl());
		elRegion = el.getRegion();
		
		var mid = el.getCenterXY()[1];
		var el2 = this.getEl();
		var p = el.dom.parentNode;
		if ((this.fromOnly || (el.id != 'trashcan' && el2.parentNode.id != p.id && el2.parentNode.id == this.parentID))) {
			if (typeof(studiotabs.moduleTabs[p.id.substr(2, p.id.length)][studiotabs.subtabModules[el2.id]]) != 'undefined') 
				return;
		}
		
		if (this.fromOnly && el.id != 'trashcan') {
			el2 = el2.cloneNode(true);
			el2.module = studiotabs.subtabModules[el2.id];
			el2.id = 'addListStudio' + addListStudioCount;
			this.lastNode = el2;
			this.lastNode.clickContent = el2.clickContent;
			this.lastNode.clickBorder = el2.clickBorder;
			this.lastNode.clickHeight = el2.clickHeight
		}
		if (dragEl.getY() < mid) { // insert on top triggering item
			p.insertBefore(el2, el.dom);
		}
		else { // insert below triggered item
			p.insertBefore(el2, el.dom.nextSibling);
		}
	}
});
