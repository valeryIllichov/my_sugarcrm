<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/******************************************************************************
OpensourceCRM End User License Agreement

INSTALLING OR USING THE OpensourceCRM's SOFTWARE THAT YOU HAVE SELECTED TO 
PURCHASE IN THE ORDERING PROCESS (THE "SOFTWARE"), YOU ARE AGREEING ON BEHALF OF
THE ENTITY LICENSING THE SOFTWARE ("COMPANY") THAT COMPANY WILL BE BOUND BY AND 
IS BECOMING A PARTY TO THIS END USER LICENSE AGREEMENT ("AGREEMENT") AND THAT 
YOU HAVE THE AUTHORITY TO BIND COMPANY.

IF COMPANY DOES NOT AGREE TO ALL OF THE TERMS OF THIS AGREEMENT, DO NOT SELECT 
THE "ACCEPT" BOX AND DO NOT INSTALL THE SOFTWARE. THE SOFTWARE IS PROTECTED BY 
COPYRIGHT LAWS AND INTERNATIONAL COPYRIGHT TREATIES, AS WELL AS OTHER 
INTELLECTUAL PROPERTY LAWS AND TREATIES. THE SOFTWARE IS LICENSED, NOT SOLD.

    *The COMPANY may not copy, deliver, distribute the SOFTWARE without written
     permit from OpensourceCRM.
    *The COMPANY may not reverse engineer, decompile, or disassemble the 
    SOFTWARE, except and only to the extent that such activity is expressly 
    permitted by applicable law notwithstanding this limitation.
    *The COMPANY may not sell, rent, or lease resell, or otherwise transfer for
     value, the SOFTWARE.
    *Termination. Without prejudice to any other rights, OpensourceCRM may 
    terminate this Agreement if the COMPANY fail to comply with the terms and 
    conditions of this Agreement. In such event, the COMPANY must destroy all 
    copies of the SOFTWARE and all of its component parts.
    *OpensourceCRM will give the COMPANY notice and 30 days to correct above 
    before the contract will be terminated.

The SOFTWARE is protected by copyright and other intellectual property laws and 
treaties. OpensourceCRM owns the title, copyright, and other intellectual 
property rights in the SOFTWARE.
*****************************************************************************/
/*********************************************************************************
* Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights Reserved.
********************************************************************************/
require_once('include/ListView/ListViewSmarty.php');
if(file_exists('custom/modules/Calendar2/metadata/listviewdefs.php')){
	require_once('custom/modules/Calendar2/metadata/listviewdefs.php');
}else{
	require_once('modules/Calendar2/metadata/listviewdefs.php');
}


global $mod_strings;
global $app_strings;
global $app_list_strings;

global $urlPrefix;


global $currentModule;
global $current_language;
$current_module_strings = return_module_language($current_language, 'Tasks');


global $theme;

// clear the display columns back to default when clear query is called
if(!empty($_REQUEST['clear_query']) && $_REQUEST['clear_query'] == 'true')
    $current_user->setPreference('ListViewDisplayColumns', array(), 0, 'Calendar2');

$savedDisplayColumns = $current_user->getPreference('ListViewDisplayColumns', 'Calendar2'); // get user defined display columns

$json = getJSONobj();
$seedTask = new Task();

// setup listview smarty
$lv = new ListViewSmarty();

$displayColumns = array();
// check $_REQUEST if new display columns from post
if(!empty($_REQUEST['displayColumns'])) {
    foreach(explode('|', $_REQUEST['displayColumns']) as $num => $col) {
        if(!empty($listViewDefs['Tasks'][$col]))
            $displayColumns[$col] = $listViewDefs['Tasks'][$col];
    }
}
elseif(!empty($savedDisplayColumns)) { // use user defined display columns from preferences
    $displayColumns = $savedDisplayColumns;
}
else { // use columns defined in listviewdefs for default display columns
    foreach($listViewDefs['Calendar2'] as $col => $params) {
        if(!empty($params['default']) && $params['default'])
            $displayColumns[$col] = $params;
    }
}
$params = array('massupdate' => false); // setup ListViewSmarty params
if(!empty($_REQUEST['orderBy'])) { // order by coming from $_REQUEST
    $params['orderBy'] = $_REQUEST['orderBy'];
    $params['overrideOrder'] = true;
    if(!empty($_REQUEST['sortOrder'])) $params['sortOrder'] = $_REQUEST['sortOrder'];
}
$params['orderBy'] = '';
$lv->displayColumns = $displayColumns;

// use the stored query if there is one
if (!isset($where)) $where = "";
require_once('modules/MySettings/StoreQuery.php');
$storeQuery = new StoreQuery();
if(!isset($_REQUEST['query'])){
    $storeQuery->loadQuery('Calendar2');
    $storeQuery->populateRequest();
}else{
    $storeQuery->saveFromGet('Calendar2');
}
global $timedate;

//jc: bug 14616 - dates need to specificy the end of the current date in order to get tasks
// that are scheduled to start today
$today = $timedate->to_db_date(date($timedate->get_date_format() . " H:m:s"), false) . " 23:59:59";
$today = $timedate->handle_offset($today, $timedate->dbDayFormat, true) . " 23:59:59";
//end bug 14616

$where = "(tasks.assigned_user_id='$current_user->id' and tasks.status<>'Completed' and tasks.status<>'Deferred'";
$where .= "and (tasks.date_start is NULL or tasks.date_start <= '$today'))";

$lv->export = false;
$lv->delete = false;
$lv->select = false;
$lv->mailMerge = false;
$lv->multiSelect = false;
$lv->setup($seedTask, 'include/ListView/ListViewGeneric.tpl', $where, $params);
echo get_module_title($current_module_strings['LBL_MODULE_NAME'], $current_module_strings['LBL_LIST_FORM_TITLE'], false);


echo $lv->display();
//Fake Mass Update
$form = "<form action='index.php' id='MassUpdate' method='post' name='MassUpdate'><input type='hidden' id='uid' name='uid'><input name='action' type='hidden' value='index' /><input name='module' type='hidden' value='Project'></form>";
echo $form;
?>
