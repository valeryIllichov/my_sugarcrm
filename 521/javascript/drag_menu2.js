/*
* Скрипт реализующий горизонтальный скроллер бар
* обрататывает события перемещения скроллера мышью,
* клик по линнии скролла
* прокрутка колесом мыши.
* Автоподгон ширины скроллера
*/

Scroll = function (scroller, scroller_bar, menu)
{
    this.canDrag = false;
    this.prepared = false;

    this.shift_x;
    this.delta;

    this.scroller = scroller;
    this.scrollerBar = scroller_bar;
    this.menu = menu;

    this.scrollerStartShift;
    this.menuStartShift;

    this.scrollerTrackWidth = document.getElementById("crm521").offsetWidth - 30;
    this.menuTrackWidth;
    this.slsmField = "yui-dt-col-slsm";
    this.nameField = "yui-dt-col-custname";
    this.custnoField = "yui-dt-col-custno";
    this.scrollerWidth;
    this.menuWidth =document.getElementById("crm521").offsetWidth - 20;

    this.step;

    this.dontmove = false;

    this.a = false;

    this.prepare = function()
    {
        if(get(this.scroller) && get(this.menu))
        {
            this.scroller = get(this.scroller);
            this.scrollerBar = get(this.scrollerBar);
            this.menu = get(this.menu);
            this.nameField = this.menu.getElementsByClassName(this.nameField);
            this.custnoField = this.menu.getElementsByClassName(this.custnoField);
            
            this.scrollerStartShift = 20;
            this.menuStartShift = 10;
            
            this.menuTrackWidth = this.menu.getElementsByTagName("table")['0'].offsetWidth + this.menuStartShift;
            
            this.scrollerWidth = Math.round( (this.menuWidth * this.scrollerTrackWidth) / this.menuTrackWidth );
               
            this.scrollerWidth = (this.scrollerWidth < 28) ?  28 : this.scrollerWidth;
            
            this.scrollerWidth = (this.scrollerWidth > this.scrollerTrackWidth) ?  this.scrollerTrackWidth : this.scrollerWidth;
            
            this.scroller.style.paddingRight = this.scrollerWidth - 28 + "px";
            
            this.scrollerTrackWidth -= this.scrollerWidth;
            this.menuTrackWidth -= this.menuWidth;

            this.delta = this.menuTrackWidth / this.scrollerTrackWidth;

            this.prepared = true;
        }
        return false;
    }

    this.fixForBrowsers = function(event)
    {
        if (!event)
        {
            // For IE.
            event = window.event;
        }
        if(event.stopPropagation) event.stopPropagation();
        else event.cancelBubble = true;
        if(event.preventDefault) event.preventDefault();
        else event.returnValue = false;
    }

    this.setStep = function()
    {
        this.step = Math.round(this.menu.getElementsByTagName("table")['0'].offsetWidth * this.scrollerTrackWidth / this.menuTrackWidth);    
    }

    this.setPosition = function(newPosition)
    {
        if(newPosition <= this.scrollerTrackWidth + this.scrollerStartShift && newPosition >= this.scrollerStartShift)
        {
            this.scroller.style.left = newPosition + "px";
            var  padding = this.menu.getElementsByClassName(this.slsmField)[0].offsetWidth + ($(".yui-content").outerWidth() - $(".yui-content").width()) + 10;
            var fieldPosition =Math.round( (parseInt(this.scroller.style.left) - this.scrollerStartShift) * this.delta * (1) ) + this.menuStartShift - padding;
            if(newPosition > padding){
                for(var j = 0; j < this.nameField.length; j++) {
                    //var heightTR = this.nameField[j].parentNode.offsetHeight;
                    //this.nameField[j].childNodes[0].style.height = heightTR + "px";
                    this.nameField[j].childNodes[0].style.left = fieldPosition  + "px";
                    this.nameField[j].childNodes[0].style.borderRight = "1px solid #CCCCCC";
                    //this.custnoField[j].childNodes[0].style.height = heightTR + "px";
                    this.custnoField[j].childNodes[0].style.left = fieldPosition  + "px"; 
                    this.custnoField[j].childNodes[0].style.borderRight = "1px solid #CCCCCC";
                }
            }else{
                for(var j = 0; j < this.nameField.length; j++) {
                    this.nameField[j].childNodes[0].style.left = "0";
                    this.nameField[j].childNodes[0].style.borderRight = "";
                    this.custnoField[j].childNodes[0].style.left = "0";
                    this.custnoField[j].childNodes[0].style.borderRight = "";
                }
            }
        }
        else
        {
            if(newPosition >= this.scrollerTrackWidth + this.scrollerStartShift)
            {        
                this.scroller.style.left = this.scrollerTrackWidth + this.scrollerStartShift + "px";
            }
            if(newPosition < this.scrollerStartShift)
            {
                this.scroller.style.left = this.scrollerStartShift + "px";
            }
        }
        this.menu.style.marginLeft = Math.round( (parseInt(this.scroller.style.left) - this.scrollerStartShift) * this.delta * (-1) ) + this.menuStartShift + "px";
        return false;
    }

    this.drag = function(event)
    {
        if (!event)
        {
            // For IE.
            event = window.event;
        }
        if (this.prepared)
        {
            this.canDrag = true;
            this.shift_x = event.clientX - parseInt(this.scroller.style.left);
            this.fixForBrowsers(event);
        }    
        return false;
    }

    this.movescroller = function(event)
    {
        if (!event)
        {
            // For IE.
            event = window.event;
        }
        if (this.prepared && !this.dontmove)
        {
            this.setStep();
            var clickX = event.layerX ? event.layerX : event.offsetX;
            var currentPosition = parseInt(this.scroller.style.left);               
            var i = (clickX > currentPosition) ? 1 : -1;
            var newPosition = 2*i*this.step + parseInt(this.scroller.style.left); 
            this.setPosition(newPosition);
            this.fixForBrowsers(event);
        }
        else
        {
            this.dontmove = false;
        }
        return false;
    }

    this.move = function(event)
    {
        if (!event)
        {
            // For IE.
            event = window.event;
        }
        if (this.prepared && this.canDrag)
        {
            this.setPosition(event.clientX-this.shift_x);
            this.fixForBrowsers(event);
        }
        return false;
    }

    this.drop = function()
    {
        this.canDrag=false; 
    }
    
    this.scrollerClickHandler = function()
    {
        this.dontmove=true;    
    }    

    this.handle = function(delta, event) 
    {
        if (!event)
        {
            // For IE.
            event = window.event;
        }
        var i = (delta < 0) ? 1 : -1;
        this.setStep()
        var currentPosition = parseInt(this.scroller.style.left);               
        var newPosition = i*this.step + currentPosition; 
        this.setPosition(newPosition);        
        this.fixForBrowsers(event);        
    }

    this.cancelWheelAction = function(event)
    {
        if (!event)
        {
            // For IE.
            event = window.event;
        }
        if (event.preventDefault)
        {
            event.preventDefault();
        }
        event.returnValue = false;
    }

    this.wheel = function(event)
    {
        var delta = 0;
        if (!event)
        {
            // For IE.
            event = window.event;
        }
        if (event.wheelDelta) 
        {
            // IE/Opera.
            delta = event.wheelDelta/120;
            if (window.opera)
            {
                delta = delta;
            }
        } 
        else if (event.detail) 
        {  
            delta = -event.detail/3;
        }
        if (delta)
        {
            this.handle(delta, event);
            this.cancelWheelAction(event);
            this.fixForBrowsers(event);
            return false;
        }
    }
}

function get(id)
{
    return document.getElementById(id);
}
function handleOnMouseUp(event)
{
    first.drop(event);
}
function handleOnMouseMove(event)
{
    first.move(event);
}

function handleOnClickBarFirst(event) 
{
    first.movescroller(event);
}
function handleOnMouseDownFirst(event)
{
    first.drag(event);
}
function handleOnClickFirst(event) 
{
    first.scrollerClickHandler(event);
}
function handleOnMouseWheelFirst(event)
{
    first.wheel(event);
}

function handleOnMouseDownThird(event)
{
    third.drag(event);
}

function handleOnMouseDownThirdFixed(event)
{

    thirdFixed.drag(event);
}
var first;


function initScroll(tab)
{
    first = new Scroll('scroller-'+tab, 'scroller-bar-'+tab, tab);

    first.prepare();
    document.onmousemove = handleOnMouseMove;
    window.onmouseup = handleOnMouseUp;
    get('scroller-bar-'+tab).onclick = handleOnClickBarFirst;    
    get('scroller-'+tab).onmousedown = handleOnMouseDownFirst;
    get('scroller-'+tab).onmouseup = handleOnMouseUp;
    get('scroller-'+tab).onclick = handleOnClickFirst;    

//    if (get('withscript-'+tab).addEventListener)
//        get('withscript-'+tab).addEventListener('DOMMouseScroll', handleOnMouseWheelFirst, false);
//   get('withscript-'+tab).onmousewheel = handleOnMouseWheelFirst;
}

function destroyScroll(tab)
{
    first = new Scroll('scroller-'+tab, 'scroller-bar-'+tab, tab);

    first.wheel  = null;
}
