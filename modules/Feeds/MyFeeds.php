<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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
/*********************************************************************************

 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright(C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('XTemplate/xtpl.php');

require_once('modules/Feeds/Feed.php');

require_once('include/ListView/ListView.php');

global $app_strings;
global $app_list_strings;
global $current_language;
global $urlPrefix;
global $currentModule;
global $theme;
global $current_user;
$current_module_strings = return_module_language($current_language, 'Feeds');

if(!isset($where)) $where = "";

$seedFeed = new Feed();
$where = " users_feeds.user_id='{$current_user->id}' ";
$orderby = 'rank asc';
$query = $seedFeed->create_list_query($orderby,$where);
$result = $seedFeed->db->query($query, -1);

while($row =  $seedFeed->db->fetchByAssoc($result, -1)) {
	echo "<p>";
	template_display_my_feed($row);
	echo "</p>";
}



// My feeds headlines template
function template_display_my_feed(&$feed_row) {
	global $image_path, $even_bg, $sugar_config ,$mod_strings , $current_user;
	$out = "";

	if(!defined('DOMIT_RSS_INCLUDE_PATH')) {
		define('DOMIT_RSS_INCLUDE_PATH', "include/domit_rss/");
	}
	require_once(DOMIT_RSS_INCLUDE_PATH . 'xml_domit_rss.php');

	$rssdoc = new xml_domit_rss_document($feed_row['url'],'cache/feeds/',$sugar_config['rss_cache_time']);
	$content = '';
	$currChannel = $rssdoc->getChannel(0);
		
	if(!method_exists($currChannel, 'getTitle')) {
		$out .= $mod_strings['LBL_FEED_NOT_AVAILABLE'];

		// This section of the code fetches the filename of the cache required to delete and refresh
		$cache = new php_text_cache('cache/feeds/',$sugar_config['rss_cache_time']);
		
		$deletecache = $cache->getCacheFileName($feed_row['url']);
		if(file_exists($deletecache)){
			unlink($deletecache);
		}
		
		echo "<br><a href='index.php?module=Feeds&action=index&return_module=Feeds&delete_cache='>".$mod_strings['LBL_REFRESH_CACHE']."</a>";
		return;
	}
	
	if(method_exists($currChannel,'getLastBuildDate')) {
		$last_build_date = $currChannel->getLastBuildDate();
		if(!empty($last_build_date)){
			$user_preference_format = $current_user->getUserDateTimePreferences();
			$last_build_date = Date($user_preference_format['date'].' '.$user_preference_format['time'] , strtotime($last_build_date));
		}
	}

	$img_html = '';
	$baseURL = "index.php?return_action={$_REQUEST['action']}&return_module=Feeds&module=Feeds&record={$feed_row['id']}&action=";
	$up = get_image($image_path."uparrow",'border="0" align="absmiddle" alt="'.$mod_strings['LBL_MOVE_UP'].'"');
	$down = get_image($image_path."downarrow",'border="0" align="absmiddle" alt="'.$mod_strings['LBL_MOVE_DOWN'].'"');
	$del = get_image($image_path."delete",'border="0" align="absmiddle" alt="'.$mod_strings['LBL_DELETE_FAV_BUTTON_LABEL'].'"');
	$title = $currChannel->getTitle();
	$link = $currChannel->getLink();
	$buildDate = empty($last_build_date) ? "" : $mod_strings['LBL_LAST_UPDATED'].": ".$last_build_date; 
	
	$out .=<<<eoq
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td align="right">
					<nobr>
					<a href="{$baseURL}MoveUp">{$up}</a>
					<a href="{$baseURL}MoveDown">{$down}</a>
					<a href="{$baseURL}DeleteFavorite" class="listViewTdToolsS1">{$del}</a>
					</nobr>
				</td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0" width="100%" border="0" class="listView">
			<tr height="20">
				<td scope="col" width="100%" class="listViewThS1">
					<a href="index.php?action=DetailView&module=Feeds&record={$feed_row['id']}" class="listViewThLinkS1">{$title}</a> - 
					<a target="_new" href="{$link}" class="listViewThLinkS1">( {$mod_strings['LBL_VISIT_WEBSITE']} )</a>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$buildDate}
				</td>
			</tr>
			<tr>
				<td scope="col" bgcolor="{$even_bg}" class="evenListRowS1" colspan="10">
eoq;
	$totalItems = 5;
	$topitem = $currChannel->getItem(0);

	if(!isset($topitem)) {
		$out .= "No content";
	} else {
		//loop through each item
		for($j = 0; $j < $totalItems; $j++) {
			//get reference to current item
			$currItem = $currChannel->getItem($j);
			if(!isset($currItem)) {
				continue;
			}

			$item_link = $currItem->getLink();
			$item_title = strip_tags($currItem->getTitle());
			$item_date = $currItem->getPubDate();
			if(!empty($item_date)){
				$user_preference_format = $current_user->getUserDateTimePreferences();
				$item_date = Date($user_preference_format['date'].' '.$user_preference_format['time'] , strtotime($item_date));
			}
			$out .= <<<eoq
				<li><a target="_new" href="{$item_link}" class="listViewTdLinkS1">{$item_title}</a>&nbsp;&nbsp;<span class="rssItemDate">{$item_date}</span>
eoq;
		}
	}
	
	$out .= "</td></tr></table>";
	echo $out;
}
