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
require_once('XTemplate/xtpl.php');
require_once("data/Tracker.php");
require_once('modules/Resources/Resource.php');
require_once('themes/'.$theme.'/layout_utils.php');
require_once('modules/Calendar2/Calendar2.php');

require_once('modules/SavedSearch/SavedSearch.php');
require_once('include/SearchForm/SearchForm.php');


global $app_strings, $app_list_strings, $current_language, $current_user;
$current_module_strings = return_module_language($current_language, 'Resources');

global $urlPrefix;


global $currentModule;

global $theme;

require_once('include/QuickSearchDefaults.php');
$qsd = new QuickSearchDefaults();

$json = getJSONobj();

$seedResource = new Resource();
$searchForm = new SearchForm('Resources', $seedResource);

echo "\n<p>\n";
echo get_module_title($mod_strings['LBL_MODULE_NAME'], $mod_strings['LNK_RES_CAL'], true); 
echo "\n</p>\n";
if(!empty($_REQUEST['search_form_only']) && $_REQUEST['search_form_only']) {
    switch($_REQUEST['search_form_view']) {
        case 'basic_search':
            $searchForm->setup();
            $searchForm->displayBasic(false);
            break;
        case 'advanced_search':
            $searchForm->setup();
            $searchForm->displayAdvanced(false);
            break;
        default:
            break;
    }
    return;
}

if (!isset($where)) $where = "";

require_once('modules/MySettings/StoreQuery.php');
$storeQuery = new StoreQuery();
if(!isset($_REQUEST['query'])){
	$storeQuery->loadQuery($currentModule);
	$storeQuery->populateRequest();
}else{
	$storeQuery->saveFromGet($currentModule);	
}
if(isset($_REQUEST['query']))
{
	// we have a query
    $searchForm->populateFromRequest();

    $where_clauses = $searchForm->generateSearchWhere(true, "Resources");
    print_r($where_clauses);
    $where = "";
    if (count($where_clauses) > 0 )$where= implode(' and ', $where_clauses);
	$GLOBALS['log']->debug("Here is the where clause for the list view: $where");
}

if(!isset($_REQUEST['search_form']) || $_REQUEST['search_form'] != 'false') {
    $searchForm->setup();
    if(isset($_REQUEST['searchFormTab']) && $_REQUEST['searchFormTab'] == 'advanced_search') {
		$searchForm->displayAdvanced();
    }
    else {
        $searchForm->displayBasic();
    }
}

echo $qsd->GetQSScripts();
// display 

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
setlocale( LC_TIME ,$current_language);

$args['IMAGE_PATH'] = $image_path;
$args['view'] = 'shared';

$date_arr = array();

if ( isset($_REQUEST['ts'])) $date_arr['ts'] = $_REQUEST['ts'];
if ( isset($_REQUEST['day'])) $date_arr['day'] = $_REQUEST['day'];
if ( isset($_REQUEST['month'])) $date_arr['month'] = $_REQUEST['month'];
if ( isset($_REQUEST['week'])) $date_arr['week'] = $_REQUEST['week'];
if ( isset($_REQUEST['year']))
{
	if ($_REQUEST['year'] > 2037 || $_REQUEST['year'] < 1970)
	{
		print("Sorry, resource calendar cannot handle the year you requested");
		print("<br>Year must be between 1970 and 2037");
		exit;
	}
	$date_arr['year'] = $_REQUEST['year'];
}

$args['calendar'] = new Calendar2($args['view'], $date_arr);


require_once('include/database/PearDatabase.php');
$db = & PearDatabase::getInstance();
$focus = new Resource();
$query = $focus->create_list_query("name", $where);
$GLOBALS['log']->debug("get_resource_array query: $query");
$result = $db->query($query, true, "Error filling in resource array: ");
$rows = array();
$i=-1;
while(($row=$db->fetchByAssoc($result)) != null) {
	$i++;
	$rows[$i] = $row['id'];
}
if ($i==-1) return null;
global $ids;
$ids = $rows;
include_once('templates/templates_res_calendar.php');
$savedSearch = new SavedSearch();
$json = getJSONobj();
$savedSearchSelects = $json->encode(array($GLOBALS['app_strings']['LBL_SAVED_SEARCH_SHORTCUT'] . '<br>' . $savedSearch->getSelect('Resources')));
$str = "<script>
YAHOO.util.Event.addListener(window, 'load', SUGAR.util.fillShortcuts, $savedSearchSelects);
</script>";
echo $str;
?>
<script type="text/javascript" language="JavaScript">
<!-- Begin
function toggleDisplay(id){

	if(this.document.getElementById( id).style.display=='none'){
		this.document.getElementById( id).style.display='inline'
		if(this.document.getElementById(id+"link") != undefined){
			this.document.getElementById(id+"link").style.display='none';
		}
	}else{
		this.document.getElementById(  id).style.display='none'
		if(this.document.getElementById(id+"link") != undefined){
			this.document.getElementById(id+"link").style.display='inline';
		}
	}
}
		//  End -->
	</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td valign=top width="70%" style="padding-right: 10px; padding-top: 2px;">
<?php template_calendar($args); ?>
</td>
</tr>
</table>
