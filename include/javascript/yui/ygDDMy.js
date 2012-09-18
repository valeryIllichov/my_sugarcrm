
function ygDDMy(id,sGroup){if(id){this.init(id,sGroup);this.initFrame();this.logger=new ygLogger("ygDDMy");}
this.setXConstraint(0,0);}
ygDDMy.prototype=new YAHOO.util.DDProxy();ygDDMy.prototype.onDragDrop=function(e,id){this.logger.debug(this.id+" onDragDrop");var el;if("string"==typeof id){el=YAHOO.util.DDM.getElement(id);}else{el=YAHOO.util.DDM.getBestMatch(id).getEl();}
YAHOO.util.DDM.swapNode(this.getEl(),el);};ygDDMy.prototype.endDrag=function(e){};
