
function ygDDMy2(id,sGroup){if(id){this.init(id,sGroup);this.initFrame();this.logger=new ygLogger("ygDDMy2");}
var s=this.getDragEl().style;s.background="url(img/channel.png) 0 0 no-repeat";s.height="92px";s.width="100px";this.resizeFrame=false;this.centerFrame=true;}
ygDDMy2.prototype=new YAHOO.util.DDProxy();ygDDMy2.prototype.onDragDrop=function(e,id){this.logger.debug(this.id+" onDragDrop");var el;if("string"==typeof id){el=YAHOO.util.DDM.getElement(id);}else{el=YAHOO.util.DDM.getBestMatch(id).getEl();}
YAHOO.util.DDM.swapNode(this.getEl(),el);};ygDDMy2.prototype.endDrag=function(e){};
