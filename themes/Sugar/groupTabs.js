/**
 * groupTabs javascript file
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
SUGAR.themes.updateDelay=250;SUGAR.themes.updateSubTabHtml=function(mainTab){var subTabs=document.getElementById("subtabs");subTabs.innerHTML=SUGAR.themes.subTabs[mainTab];};SUGAR.themes.updateSubTabs=function(mainTab,htmlTab){if(typeof htmlTab!='undefined'){SUGAR.themes.updateSubTabHtml(htmlTab);}else{SUGAR.themes.updateSubTabHtml(mainTab);}
if(SUGAR.themes.activeTab){var oldActive=document.getElementById(SUGAR.themes.activeTab+"_tab");oldActive.getElementsByTagName('tr')[0].getElementsByTagName('td')[0].className="otherTabLeft";oldActive.getElementsByTagName('tr')[0].getElementsByTagName('td')[1].className="otherTab";oldActive.getElementsByTagName('tr')[0].getElementsByTagName('a')[0].className="otherTabLink";oldActive.getElementsByTagName('tr')[0].getElementsByTagName('td')[2].className="otherTabRight";}
var newActive=document.getElementById(mainTab+"_tab");newActive.getElementsByTagName('tr')[0].getElementsByTagName('td')[0].className="currentTabLeft";newActive.getElementsByTagName('tr')[0].getElementsByTagName('td')[1].className="currentTab";newActive.getElementsByTagName('tr')[0].getElementsByTagName('a')[0].className="currentTabLink";newActive.getElementsByTagName('tr')[0].getElementsByTagName('td')[2].className="currentTabRight";SUGAR.themes.activeTab=mainTab;};SUGAR.themes.updateMoreTab=function(tabName){var moreTab=document.getElementById(SUGAR.themes.moreTab+"_tab");if(SUGAR.themes.moreTabUrl==null){SUGAR.themes.moreTabUrl=moreTab.getElementsByTagName('tr')[0].getElementsByTagName('td')[1].getElementsByTagName('a')[0].href;}
var href=(tabName!=SUGAR.themes.moreTab)?document.getElementById(tabName+"Handle").href:SUGAR.themes.moreTabUrl;moreTab.onmouseover=function(){SUGAR.themes.updateSubTabsDelay(SUGAR.themes.moreTab,tabName);};moreTab.getElementsByTagName('tr')[0].getElementsByTagName('td')[0].getElementsByTagName('img')[0].alt=tabName;moreTab.getElementsByTagName('tr')[0].getElementsByTagName('td')[1].getElementsByTagName('a')[0].innerHTML=tabName;moreTab.getElementsByTagName('tr')[0].getElementsByTagName('td')[1].getElementsByTagName('a')[0].href=href;moreTab.getElementsByTagName('tr')[0].getElementsByTagName('td')[1].getElementsByTagName('a')[0].onclick=function(){SUGAR.themes.chooseTab(tabName);return true;};moreTab.getElementsByTagName('tr')[0].getElementsByTagName('td')[2].getElementsByTagName('img')[0].alt=tabName;};SUGAR.themes.resetTabs=function(){SUGAR.themes.updateSubTabs(SUGAR.themes.startTab);SUGAR.themes.updateMoreTab(SUGAR.themes.moreTab);}
SUGAR.themes.firstReset=function(){SUGAR.themes.resetTabs();document.getElementById(SUGAR.themes.startTab+"_tab").getElementsByTagName('tr')[0].getElementsByTagName('td')[1].getElementsByTagName('a')[0].className='currentTablink';}
SUGAR.themes.setResetTimer=function(){window.clearTimeout(SUGAR.themes.updateSubTabsTimer);window.clearTimeout(SUGAR.themes.resetSubTabsTimer);SUGAR.themes.resetSubTabsTimer=window.setTimeout('SUGAR.themes.resetTabs();',1000);}
SUGAR.themes.updateSubTabsDelay=function(mainTab,htmlTab,moreTab){var htmlTabArg='';if(typeof htmlTab!='undefined'){htmlTabArg=', "'+htmlTab+'"';}
var moreTabCode='';if(typeof moreTab!='undefined'){moreTabCode='SUGAR.themes.updateMoreTab("'+moreTab+'");';}
window.clearTimeout(SUGAR.themes.updateSubTabsTimer);SUGAR.themes.updateSubTabsTimer=window.setTimeout('SUGAR.themes.updateSubTabs("'+mainTab+'"'+htmlTabArg+');'+moreTabCode,SUGAR.themes.updateDelay);}
SUGAR.themes.chooseTab=function(tab){Set_Cookie('parentTab',tab,30,'/','','');}
