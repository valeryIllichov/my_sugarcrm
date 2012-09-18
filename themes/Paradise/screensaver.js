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
SUGAR.themes.boat=function(){var timer;var animation;var delay;var targetId;var startXY;return{init:function(){SUGAR.themes.boat.delay=300000;SUGAR.themes.boat.timer=null;SUGAR.themes.boat.animation=null;SUGAR.themes.boat.startXY=[0,24];SUGAR.themes.boat.targetId='screensaver_boat';if(document.getElementById(SUGAR.themes.boat.targetId)){SUGAR.themes.boat.animation={isAnimated:function(){return false}};YAHOO.util.Event.addListener(window,"mousemove",SUGAR.themes.boat.interrupt);YAHOO.util.Event.addListener(window,"keypress",SUGAR.themes.boat.interrupt);SUGAR.themes.boat.resetTimer();}},interrupt:function(){if(SUGAR.themes.boat.animation.isAnimated()){SUGAR.themes.boat.animation.stop();document.getElementById(SUGAR.themes.boat.targetId).style.display='none';}
SUGAR.themes.boat.resetTimer();},resetTimer:function(){window.clearTimeout(SUGAR.themes.boat.timer);SUGAR.themes.boat.timer=window.setTimeout(SUGAR.themes.boat.animate,SUGAR.themes.boat.delay);},animate:function(){document.getElementById(SUGAR.themes.boat.targetId).style.display='';var attributes={points:{to:SUGAR.themes.boat.getEndXY(),from:SUGAR.themes.boat.startXY}};var duration=SUGAR.themes.boat.getDuration();SUGAR.themes.boat.animation=new YAHOO.util.Motion(SUGAR.themes.boat.targetId,attributes,duration);SUGAR.themes.boat.animation.onComplete.subscribe(SUGAR.themes.boat.reAnimate);SUGAR.themes.boat.animation.animate();},reAnimate:function(){document.getElementById(SUGAR.themes.boat.targetId).style.display='none';window.clearTimeout(SUGAR.themes.boat.timer);SUGAR.themes.boat.timer=window.setTimeout(SUGAR.themes.boat.animate,2000);},getEndXY:function(){return[YAHOO.util.Dom.getViewportWidth(),SUGAR.themes.boat.startXY[1]];},getDuration:function(){return 25*YAHOO.util.Dom.getViewportWidth()/1280;}};}();
