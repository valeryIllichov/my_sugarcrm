
function ygDDOnTop(id,sGroup){if(id){this.init(id,sGroup);this.logger=new ygLogger("ygDDOnTop");}}
ygDDOnTop.prototype=new YAHOO.util.DD();ygDDOnTop.prototype.origZ=0;ygDDOnTop.prototype.startDrag=function(x,y){this.logger.debug(this.id+" startDrag");var style=this.getEl().style;this.origZ=style.zIndex;style.zIndex=999;}
ygDDOnTop.prototype.endDrag=function(e){this.logger.debug(this.id+" endDrag");this.getEl().style.zIndex=this.origZ;};
