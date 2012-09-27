<? 
if(!defined('sugarEntry'))define('sugarEntry', true);
require("FMPSales.php"); 
require_once('../custom/include/language/en_us.lang.php');
require_once('../include/QuickSearchDefaults.php');

$baseurl = FMPSales::crmurl;
FMPSales::initialize521();

if(!isset($_SESSION['authenticated_user_id'])) {
	die("Not logged in as a sugar user");
}
if(!isset($_COOKIE["current_user_keep_filters"]) || $_COOKIE["current_user_keep_filters"] == 0){
        $checked_keep_filters = '';
}else{
        $checked_keep_filters = 'checked';
}
$userid=$_SESSION['authenticated_user_id'];

$areaRollup=FMPSales::getUserAreaRollup($userid);
$slsmRollup=FMPSales::getUserSlsmRollup($userid);
$userName=FMPSales::getUserName($userid);
$slsm_tree_list = html_for_daily_sales('onchange="javaScript:get_date_for_i521(\'\')"', 'i521_');  // prepeare SLSM list for display
$area_list = html_for_regloc('onchange="javaScript:get_date_for_i521(\'\')"', 'i521_');
$dealer_list = get_dealer_type();
$url_arr = parse_url (getenv("HTTP_REFERER"));
parse_str($url_arr['query']);
if($fmpsalesdashlet){
    global $slsmRollup;
    $slsmlist = child__slsm_tree($slsmRollup);
    $slsm_title =getUserPreference('daily_sales_Slsm','global',$userName);
    $slsm_arr = explode(";",$slsm_title);
    $slsm_arr_new = array();
    foreach ($slsm_arr as $slsm_val){
        if (array_key_exists($slsm_val, $slsmlist)) {
            $slsm_arr_new[] = $slsmlist[$slsm_val];
        }else{
            $slsm_arr_new[] = $slsm_val;
        }
    }  
    $slsm = implode(";",$slsm_arr_new);
    $reg_loc = getUserPreference('daily_sales_Reg_loc','global',$userName);
    $reg_loc_arr = explode(";",$reg_loc);
    foreach($reg_loc_arr as $val){
        if($val{0} == 'r'){
            $reg_arr[] = substr($val, 1);
        }else{
            $loc_arr[] = $val;
        }
    }
    $reg = !empty($reg_arr) ? implode(";", $reg_arr) :'';
    $loc = !empty($loc_arr) ? implode(";", $loc_arr) :'';
    $dealer_post = getUserPreference('daily_sales_Dealer_post','global',$userName);
}
$qsd = new QuickSearchDefaults();

$o = $qsd->getQSParent();
$o['field_list'][] = 'custno_c';
$o['expanded_name'] = 1;
$o['populate_list'] = array('account_name_filter', "account_name_custno_filter", "account_id_filter");
$sqs_objects['account_name_filter'] = $o;

$o = $qsd->getQSParent();
$o['field_list'] = array('custno_c', 'name', 'id');
$o['expanded_name'] = 1;
$o['populate_list'] = array('account_name_custno_filter', "account_name_filter", "account_id_filter");
$o['conditions'][0]['name'] = 'custno_c';
$o['order'] = 'custno_c';
$sqs_objects['account_name_custno_filter'] = $o;


$quicksearch_js = array();
foreach ($sqs_objects as $sqsfield => $sqsfieldArray) {
        $json = json_encode($sqsfieldArray);
        $quicksearch_js[] = "sqs_objects['$sqsfield']={$json};";
}
$quicksearch_js = ''
        . '<script language="javascript">'
        . "if(typeof sqs_objects == 'undefined'){var sqs_objects = new Array;}"
        . implode("\n", $quicksearch_js)
        . '</script>';

function html_for_daily_sales($onclick = '', $id = '') {
    global $slsmRollup;
    $list = html__slsm_tree($slsmRollup);
    $select_creater = '<div><br/><label style="font-size: 14px; color: #000000;padding-left: 5px;" for="fmp_slsm_input">Quick Search: </label>
            <input id="'.$id.'fmp_slsm_input" type="text" value="" name="fmpfilter_slsm_input" onkeyup="javaScript:'.$id.'fmp_slsm_list_quick_search(this.value);" style="width: 200px;"><br /><br /><div id="'.$id.'box_for_slsm_first">';
    $select_creater .= '<select id="'.$id.'fmprep_slsm_tree" '.$onclick.' size="15" multiple="multiple" style="width: 340px;">';
    $select_creater .= '<option value="all" style="border-bottom: 2px solid grey;">ALL</option>';
    foreach ($list as $key=>$value){
        $select_creater .= '<option value="'.$key.'">'.$value.'</option>';
    }
    $select_creater .= '</select></div><div id="'.$id.'box_for_slsm_second" style="display: none"></div></div>';
    return $select_creater;
}
    
function html__slsm_tree($tree, $level=0)  {
    $space = '';
    for($i = 0; $i<$level; $i++) {
        $space .= '&nbsp;&nbsp;';
    }

    $sbox_out = array();
    foreach($tree as $k=>$v) {
//            $sbox_out[(string)$v['slsm']] = $space . $v['slsm'] . ' ' . $v['firstname'] . ' ' . $v['lastname'];
        if(!empty($v['children']) && is_array($v['children'])){
            $key = (string)$v['slsm'];
            foreach($v['children'] as $value) {
                $key .= ';'.(string)$value['slsm'];
            }
        }else{
            $key = (string)$v['slsm'];
        }
        $sbox_out[$key] = $space . $v['slsm'] . ' ' . $v['firstname'] .'&nbsp;'. $v['lastname'];
        if (!$v['children']) {
            continue;
        }
        $sbox_out += html__slsm_tree($v['children'], $level+1);
    }

    return $sbox_out;
}

function child__slsm_tree($tree, $level=0)  {
    $space = '';
    for($i = 0; $i<$level; $i++) {
        $space .= '&nbsp;&nbsp;';
    }

    $sbox_out = array();
    foreach($tree as $k=>$v) {
        if(!empty($v['children']) && is_array($v['children'])){
            $key = (string)$v['slsm'];
            $first_key = $key;
            foreach($v['children'] as $value) {
                $key .= ';'.(string)$value['slsm'];
            }
            $sbox_out[$first_key] = $key;
            if (!$v['children']) {
                continue;
            }
            $sbox_out += child__slsm_tree($v['children'], $level+1);
        }
    }

    return $sbox_out;
}

function html_for_regloc( $onclick = '', $id = '') {
        global $areaRollup;
        $mass_reg = $areaRollup;
        $select_creater = '<select id="'.$id.'fmp_reg_loc" '.$onclick.' size="15" multiple="multiple" style="width: 200px;">';
        $select_creater .= '<option value="all" style="border-bottom: 2px solid grey;">ALL</option>';
        foreach ($mass_reg as $value) {
            $select_creater .= '<option value="r'.(int)$value['number'].'" style="font-weight: bold;">'.(int)$value['number'].' '.$value['name'].'</option>';
                foreach ($value['locations'] as $val_loc){
                    $select_creater .= '<option value="'.(int)$val_loc['number'].'">&nbsp;&nbsp;&nbsp;&nbsp;'.(int)$val_loc['number'].' '.$val_loc['name'].'</option>';
                }
        }
        $select_creater .= '</select>';
        return $select_creater;
    }
    
function html__regloc_tree($tree, $level=0)  {
    $space = '';
    for($i = 0; $i<$level; $i++) {
        $space .= '&nbsp;&nbsp;';
    }

    $sbox_out = array();
    foreach($tree as $k=>$v) {
//            $sbox_out[(string)$v['slsm']] = $space . $v['slsm'] . ' ' . $v['firstname'] . ' ' . $v['lastname'];
        if(!empty($v['children']) && is_array($v['children'])){
            $key = (string)$v['slsm'];
            foreach($v['children'] as $value) {
                $key .= ';'.(string)$value['slsm'];
            }
        }else{
            $key = (string)$v['slsm'];
        }
        $sbox_out[$key] = $space . $v['slsm'] . ' ' . $v['firstname'] .'&nbsp;'. $v['lastname'];
        if (!$v['children']) {
            continue;
        }
        $sbox_out += html__slsm_tree($v['children'], $level+1);
    }

    return $sbox_out;
}

function get_dealer_type () {
        global $app_list_strings;
        $dealer_list = $app_list_strings['fmp_dealertype_list'];
        $select_creater = '<select id="i521_fmp_dealer_type" onchange="javaScript:get_date_for_i521(\'\')" size="10" multiple="multiple" style="width: 170px;">';
        $select_creater .= '<option value="all" style="border-bottom: 2px solid grey;">ALL</option>';
        foreach ($dealer_list as $key=>$value) {
            //if($key!='') {
                $select_creater .= '<option value="'.$key.'">'.$value.'</option>';
            //}
        }
        $select_creater .= '</select>';
        return $select_creater;
 }
 
function getUserPreference($name, $category = 'global', $userName = null) {
            if(isset($_SESSION[$userName.'_PREFERENCES'][$category][$name])) {
                    return $_SESSION[$userName.'_PREFERENCES'][$category][$name];
            }
            return null;
    }
    
function buildTermsCodeList(){
    global $app_list_strings;
    $termscode_list = $app_list_strings['termscode_list'];
    $js_obj = "{";
    foreach($termscode_list as $key=>$code){
        $js_obj .= "'$key':'$code',";
    }
    $js_obj .= "}";
    return $js_obj;
}    
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>5-21 Sales Summary</title>

<link rel="stylesheet" type="text/css" href="style/skin.css">
<link rel="stylesheet" type="text/css" href="style/style_521.css">
<link rel="stylesheet" type="text/css" href="../themes/default/ext/resources/css/ext-all.css">
<link rel="stylesheet" type="text/css" href="../themes/Sugar/colors.sugar.css">
<link rel="stylesheet" type="text/css" href="custom/modules/Accounts/datatables.css" />

<script type="text/javascript" src="javascript/yui/yahoo-dom-event.js"></script>
<script type="text/javascript" src="javascript/yui/connection-min.js"></script>
<script type="text/javascript" src="javascript/yui/json-min.js"></script>
<script type="text/javascript" src="javascript/yui/element-min.js"></script>
<script type="text/javascript" src="javascript/yui/paginator-min.js"></script>
<script type="text/javascript" src="javascript/yui/datasource.js"></script>
<script type="text/javascript" src="javascript/yui/datatable.js"></script>

<script type="text/javascript" src="javascript/yui/container.js"></script>
<script type="text/javascript" src="javascript/yui/menu-min.js"></script>
<script type="text/javascript" src="javascript/yui/element-min.js"></script>
<script type="text/javascript" src="javascript/yui/button-min.js"></script>
<script type="text/javascript" src="javascript/yui/tabview-min.js"></script>
<script type="text/javascript" src="javascript/yui/UserActionIphone.js"></script>
<script src="javascript/yui/animation-min.js" type="text/javascript"></script>

<script type="text/javascript" src="button.js"></script>
<script type="text/javascript" src="javascript/jquery.min.js"></script>
<script type="text/javascript" src="../include/javascript/ext-2.0/ext-all.js"></script>
<script type="text/javascript" src="../include/javascript/ext-2.0/ext-quicksearch.js"></script>
<script type="text/javascript" src="javascript/521.js"></script>
<script src="custom/modules/Accounts/jquery.datatables.min.js" type="text/javascript"></script>
<script type="text/javascript" src="modules/Calendar2/js/jquery-ui-1.7.2.custom.min.js">
<script type="text/javascript" src="javascript/drag_menu2.js"></script>
</head>

<body class="yui-skin-sam">
    <script type="text/javascript">
	var tabView = new YAHOO.widget.TabView('crm521');
	var tabViewSales = new YAHOO.widget.TabView('customersalestabs');        
</script>
<br />
<div id="selectionbuttons">
        <div class="keep-filters"> <span>Keep user-selected filters</span><input type="checkbox" id="current_user_filter" <? echo $checked_keep_filters?> name="current_user_filter" title="" tabindex=""> </div>
        <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="i521_area_list_show"><span class="first-child-fmp-sales"><button type="button" style="font-size: 15px;" >Area</button>
            <div id="i521_area_panel" style="display: none; position: absolute;background-color: #FFFFFF; border: 1px solid #94C1E8;z-index: 9">
                 <?  echo $area_list; ?>
            </div>
        </span></span>
       <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="i521_slsm_list_show"><span class="first-child-fmp-sales"><button type="button" style="font-size: 15px;" >Slsm</button>
            <div id="i521_slsm_panel" style="display: none; position: absolute; background-color: #FFFFFF; border: 1px solid #94C1E8;z-index: 9">
                <?  echo $slsm_tree_list; ?>
            </div>
        </span></span>
        <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="i521_dealer_list_show"><span class="first-child-fmp-sales"><button type="button" style="font-size: 15px;" >Customer Type</button>
            <div id="i521_dealer_panel" style="display: none; position: absolute; background-color: #FFFFFF; border: 1px solid #94C1E8;z-index: 9">
                 <?  echo $dealer_list; ?>
            </div>
        </span></span>
        <span class="yui-button-fmp-sales yui-split-button-fmp-sales" id="i521_customer_list_show"><span class="first-child-fmp-sales"><button type="button" style="font-size: 15px;" >Customer</button>    
        <div id="i521_customer_panel" style="display: none; padding: 15px 3px 15px 3px; position: absolute; background-color: #FFFFFF; border: 1px solid #94C1E8;z-index: 9">  
            <div id="filter_list_show" style="float: right;">
                <button type="button" class="default" class="show_filter" title="customer filter list"></button>
                <div id="filter_panel" class="filter_panel" style="display: none; ">
                    <p id="i521_customers">All</p>
                </div>    
            </div>
            <label for="account_name_custno_filter" style="display: inline-block; width: 90px; font-size: 14px; color: #000000;">Account Name: </label>
            <input type="text" name="account_name_custno_filter" id="account_name_custno_filter" tabindex='1' class="sqsCustom" size="6" value="" autocomplete="off">
            <input type="text" name="account_name_filter" class="sqsCustom" tabindex="1" id="account_name_filter" size="32" value="" title='' autocomplete="off">
            <input type="hidden" name="account_id_filter" id="account_id_filter" value="">
            <input type="button" style="font-size: 12px; width: 45px;" title="Clear" class="button" onclick="javaScript:clear_account()" value="Clear">
             <input type="button" style="font-size: 12px; width: 45px;" title="Filter" class="button" onclick="javaScript:get_date_for_i521('')" value="Filter">
            <input type="button" style="font-size: 12px; width: 45px;" title="Reset" onClick="javaScript:reset_account_filter();" class="button" value="Reset">
        </div></span></span>
                <?  echo $quicksearch_js; ?>              
</div>
<div id="title-list">
        <div class="divtitle active-tab"><label class="currtitle" id="currtitle-salessummary" for="#salessummary"></label></div>
        <div class="divtitle"><label class="currtitle" for="#customerar"></label></div>
        <div class="divtitle"><label class="currtitle" for="#customersales"></label></div>
        <div class="divtitle"><label class="currtitle" for="#customersalesnonoe"></label></div>
        <div class="divtitle"><label class="currtitle" for="#customersalesundercar"></label></div>
        <div class="divtitle"><label class="currtitle" for="#customertransactions"></label></div>
        <div class="divtitle"><label class="currtitle" for="#customerreturns"></label></div>
        <div class="divtitle"><label class="currtitle" for="#customerbudget"></label></div>
        <div class="divtitle"><label class="currtitle" for="#customersalescomparison"></label></div>
        <div class="divtitle"><label class="currtitle" for="#customerbudgetcomparison"></label></div>
</div>
<br />
<div id="customerar-dlg" class="dt-dlg" style="display:none;"> 
    <span class="corner_tr"></span> 
    <span class="corner_tl"></span> 
    <span class="corner_br"></span> 
    <span class="corner_bl"></span> 
    <div class="hd"> 
        Show / hide columns
    </div> 
    <div id="customerar-dlg-picker" class="bd"> 
    </div> 
</div> 
<div id="customersales-dlg" class="dt-dlg" style="display:none;"> 
    <span class="corner_tr"></span> 
    <span class="corner_tl"></span> 
    <span class="corner_br"></span> 
    <span class="corner_bl"></span> 
    <div class="hd"> 
        Show / hide columns
    </div> 
    <div id="customersales-dlg-picker" class="bd"> 
    </div> 
</div>
<div id="customersalesnonoe-dlg" class="dt-dlg" style="display:none;"> 
    <span class="corner_tr"></span> 
    <span class="corner_tl"></span> 
    <span class="corner_br"></span> 
    <span class="corner_bl"></span> 
    <div class="hd"> 
        Show / hide columns
    </div> 
    <div id="customersalesnonoe-dlg-picker" class="bd"> 
    </div> 
</div> 
<div id="customersalesundercar-dlg" class="dt-dlg" style="display:none;"> 
    <span class="corner_tr"></span> 
    <span class="corner_tl"></span> 
    <span class="corner_br"></span> 
    <span class="corner_bl"></span> 
    <div class="hd"> 
        Show / hide columns
    </div> 
    <div id="customersalesundercar-dlg-picker" class="bd"> 
    </div> 
</div>
<div id="customersalescomparison-dlg" class="dt-dlg" style="display:none;"> 
    <span class="corner_tr"></span> 
    <span class="corner_tl"></span> 
    <span class="corner_br"></span> 
    <span class="corner_bl"></span> 
    <div class="hd"> 
        Show / hide columns
    </div> 
    <div id="customersalescomparison-dlg-picker" class="bd"> 
    </div> 
</div>
<div id="customerbudgetcomparison-dlg" class="dt-dlg" style="display:none;"> 
    <span class="corner_tr"></span> 
    <span class="corner_tl"></span> 
    <span class="corner_br"></span> 
    <span class="corner_bl"></span> 
    <div class="hd"> 
        Show / hide columns
    </div> 
    <div id="customerbudgetcomparison-dlg-picker" class="bd"> 
    </div> 
</div>
<div id="customerreturns-dlg" class="dt-dlg" style="display:none;"> 
    <span class="corner_tr"></span> 
    <span class="corner_tl"></span> 
    <span class="corner_br"></span> 
    <span class="corner_bl"></span> 
    <div class="hd"> 
        Show / hide columns
    </div> 
    <div id="customerreturns-dlg-picker" class="bd"> 
    </div> 
</div>
<div id="customertransactions-dlg" class="dt-dlg" style="display:none;"> 
    <span class="corner_tr"></span> 
    <span class="corner_tl"></span> 
    <span class="corner_br"></span> 
    <span class="corner_bl"></span> 
    <div class="hd"> 
        Show / hide columns
    </div> 
    <div id="customertransactions-dlg-picker" class="bd"> 
    </div> 
</div>
<div id="customerbudget-dlg" class="dt-dlg" style="display:none;"> 
    <span class="corner_tr"></span> 
    <span class="corner_tl"></span> 
    <span class="corner_br"></span> 
    <span class="corner_bl"></span> 
    <div class="hd"> 
        Show / hide columns
    </div> 
    <div id="customerbudget-dlg-picker" class="bd"> 
    </div> 
</div>
<div id="crm521" class="yui-navset">
	<ul class="yui-nav">
		<li class="selected"><a href="#salessummary"><em>Sales Totals</em></a></li>
		<li><a href="#customerar"><em>A/R</em></a></li>
		<li><a href="#customersalestabs"><em>Customer Sales</em></a></li>
                                    <li><a href="#customertransactions"><em>Transactions</em></a></li>
                                    <li><a href="#customerreturns"><em>Returns</em></a></li>
                                    <li><a href="#customerbudget"><em>Budget</em></a></li>
		<li><a href="#customersalescomparison"><em>Sales Comparison</em></a></li>
		<li><a href="#customerbudgetcomparison"><em>Budget Comparison</em></a></li>
	</ul>
	<div class="yui-content">
		<center>
			<div id="salessummary"></div>
		</center>
		<center>
                                                    <div id="customerar-swhd" class="swhd-option"><a id="customerar-swhd-link" href="showhidecol">Show / hide columns</a></div>

                                                    <div id="customerar-pag-nav"></div>
                                                    <div id="withscript-customerar">
                                                    <div id="menu-scrolling-customerar" class="menu_scrolling">
                                                        <div class="scrolling_line" id="scroller-bar-customerar">
                                                            <img id="scroller-customerar" style=" left: 20px; " alt="" src="images/toddler_l.gif">
                                                        </div>
                                                    </div>
                                                    <div id="customerar" style="margin-left: 10px;"></div></div>
		</center>
		
		<div id="customersalestabs" class="yui-navset">
			<ul class="yui-nav">
				<li class="selected"><a href="#customersales"><em>All</em></a></li>
				<li><a href="#customersalesnonoe"><em>Non OE</em></a></li>
				<li><a href="#customersalesundercar"><em>Under Car</em></a></li>
			</ul>
			<div class="yui-content">
				<center>
                                                                         <div id="customersales-swhd" class="swhd-option"><a id="customersales-swhd-link" href="showhidecol">Show / hide columns</a></div>              
                                                                            
                                                                        <div id="customersales-pag-nav"></div>
                                                                        <div id="withscript-customersales">
                                                                        <div id="menu-scrolling-customersales" class="menu_scrolling">
                                                                            <div class="scrolling_line" id="scroller-bar-customersales">
                                                                                <img id="scroller-customersales" style=" left: 20px; " alt="" src="images/toddler_l.gif">
                                                                            </div>
                                                                        </div>
                                                                        <div id="customersales" style="margin-left: 10px;"></div></div>
				</center>
				<center>
                                                                         <div id="customersalesnonoe-swhd" class="swhd-option"><a id="customersalesnonoe-swhd-link" href="showhidecol">Show / hide columns</a></div>              
                                                                            
                                                                        <div id="customersalesnonoe-pag-nav"></div>
                                                                        <div id="withscript-customersalesnonoe">
                                                                        <div id="menu-scrolling-customersalesnonoe" class="menu_scrolling">
                                                                            <div class="scrolling_line" id="scroller-bar-customersalesnonoe">
                                                                                <img id="scroller-customersalesnonoe" style=" left: 20px; " alt="" src="images/toddler_l.gif">
                                                                            </div>
                                                                        </div>
                                                                        <div id="customersalesnonoe" style="margin-left: 10px;"></div></div>
				</center>
                                                                        <center><div id="customersalesundercar-swhd" class="swhd-option"><a id="customersalesundercar-swhd-link" href="showhidecol">Show / hide columns</a></div>              
                                                                            
                                                                        <div id="customersalesundercar-pag-nav"></div>
                                                                        <div id="withscript-customersalesundercar">
                                                                        <div id="menu-scrolling-customersalesundercar" class="menu_scrolling">
                                                                            <div class="scrolling_line" id="scroller-bar-customersalesundercar">
                                                                                <img id="scroller-customersalesundercar" style=" left: 20px; " alt="" src="images/toddler_l.gif">
                                                                            </div>
                                                                        </div>
                                                                        <div id="customersalesundercar" style="margin-left: 10px;"></div></div>
				</center>
			</div>
		</div>
                                    <center>
                                                <div id="customertransactions-swhd" class="swhd-option"><a id="customertransactions-swhd-link" href="showhidecol">Show / hide columns</a></div>              
                                                        
                                                    <div id="customertransactions-pag-nav"></div>
                                                    <div id="withscript-customertransactions">
                                                    <div id="menu-scrolling-customertransactions" class="menu_scrolling">
                                                        <div class="scrolling_line" id="scroller-bar-customertransactions">
                                                            <img id="scroller-customertransactions" style=" left: 20px; " alt="" src="images/toddler_l.gif">
                                                        </div>
                                                    </div>
                                                    <div id="customertransactions" style="margin-left: 10px;"></div></div>
		</center>
                                    <center>
                                                <div id="customerreturns-swhd" class="swhd-option"><a id="customerreturns-swhd-link" href="showhidecol">Show / hide columns</a></div>              
                                                        
                                                    <div id="customerreturns-pag-nav"></div>
                                                    <div id="withscript-customerreturns">
                                                    <div id="menu-scrolling-customerreturns" class="menu_scrolling">
                                                        <div class="scrolling_line" id="scroller-bar-customerreturns">
                                                            <img id="scroller-customerreturns" style=" left: 20px; " alt="" src="images/toddler_l.gif">
                                                        </div>
                                                    </div>
                                                    <div id="customerreturns" style="margin-left: 10px;"></div></div>
		</center>
		<center>
                                                <div id="customerbudget-swhd" class="swhd-option"><a id="customerbudget-swhd-link" href="showhidecol">Show / hide columns</a></div>              
                                                        
                                                    <div id="customerbudget-pag-nav"></div>
                                                    <div id="withscript-customerbudget">
                                                    <div id="menu-scrolling-customerbudget" class="menu_scrolling">
                                                        <div class="scrolling_line" id="scroller-bar-customerbudget">
                                                            <img id="scroller-customerbudget" style=" left: 20px; " alt="" src="images/toddler_l.gif">
                                                        </div>
                                                    </div>
                                                    <div id="customerbudget" style="margin-left: 10px;"></div></div>
		</center>
                                    <center>
                                                <div id="customersalescomparison-swhd" class="swhd-option"><a id="customersalescomparison-swhd-link" href="showhidecol">Show / hide columns</a></div>              
                                                        
                                                    <div id="customersalescomparison-pag-nav"></div>
                                                    <div id="withscript-customersalescomparison">
                                                    <div id="menu-scrolling-customersalescomparison" class="menu_scrolling">
                                                        <div class="scrolling_line" id="scroller-bar-customersalescomparison">
                                                            <img id="scroller-customersalescomparison" style=" left: 20px; " alt="" src="images/toddler_l.gif">
                                                        </div>
                                                    </div>
                                                    <div id="customersalescomparison" style="margin-left: 10px;"></div></div>
		</center>
		<center><div id="customerbudgetcomparison-swhd" class="swhd-option"><a id="customerbudgetcomparison-swhd-link" href="showhidecol">Show / hide columns</a></div>              
                                                        
                                                    <div id="customerbudgetcomparison-pag-nav"></div>
                                                    <div id="withscript-customerbudgetcomparison">
                                                    <div id="menu-scrolling-customerbudgetcomparison" class="menu_scrolling">
                                                        <div class="scrolling_line" id="scroller-bar-customerbudgetcomparison">
                                                            <img id="scroller-customerbudgetcomparison" style=" left: 20px; " alt="" src="images/toddler_l.gif">
                                                        </div>
                                                    </div>
                                                    <div id="customerbudgetcomparison" style="margin-left: 10px;"></div></div>
		</center>
     </div> 
</div> <!--end crm521-->	 

<!-- selection buttons -->

<script type="text/javascript">
	YAHOO.namespace("FMP");
        function removeClass(o, c){
	var re = new RegExp("(^|\\s)" + c + "(\\s|$)", "g");
	o.className = o.className.replace(re, "$1").replace(/\s+/g, " ").replace(/(^ | $)/g, "");
        }
</script>

<script type="text/javascript">
	var tabView = new YAHOO.widget.TabView('crm521');
	var tabViewSales = new YAHOO.widget.TabView('customersalestabs');
            function TopScrollTable(table,div) {
                var frameWidth = document.getElementById("crm521").offsetWidth;
                var tableWidth = table._elTable.offsetWidth;
                if(tableWidth > frameWidth){
                    $("th.yui-dt-col-custname").css('padding-right','0');
                    YAHOO.util.Dom.setStyle("menu-scrolling-"+div,"display","block");
                    initScroll(div);
                }else{
                    YAHOO.util.Dom.setStyle("menu-scrolling-"+div,"display","none");
                    destroyScroll(div);
                }
            }
            
            function browserDetectNav(chrAfterPoint){

                    var  UA=window.navigator.userAgent,     

                        FirefoxB = /Firefox\/\w+\.\w+/i,   

                        ChromeB = /Chrome\/\w+\.\w+/i,     

                        browser = new Array(),  

                        browserSplit = /[ \/\.]/i,   

                        Firefox = UA.match(FirefoxB),

                        Chrome = UA.match(ChromeB);
                        //----- Firefox ----

                        if (!Firefox=="") browser[0]=Firefox[0]

                            else

                                //----- Chrom ----

                                if (!Chrome=="") browser[0] = Chrome[0]

                        var outputData;                   

                        if (browser[0] != null) outputData = browser[0].split(browserSplit);

                        if ((chrAfterPoint==null)&&(outputData != null)) 

                            {

                                return(outputData[0]);

                            }

                                else return(false);

            }

</script>

<script type="text/javascript">
YAHOO.util.Event.onContentReady("selectionbuttons", function () {
    <?if($checked_keep_filters != '' && !$fmpsalesdashlet) {?>
		var currentLocation =  localStorage.currentLocation!= null ? localStorage.currentLocation: "";
		var currentRegion = localStorage.currentRegion != null ? localStorage.currentRegion: "";
		var currentDealerType =  localStorage.currentDealerType != null ? localStorage.currentDealerType: "";
		var currentSlsm = localStorage.currentSlsm != null ? localStorage.currentSlsm: "";
                                    var currentSlsmTitle = localStorage.currentSlsmTitle != null ? localStorage.currentSlsmTitle: "";
                                    var currentCustomer = localStorage.currentCustomerCustno != null ? localStorage.currentCustomerCustno: "";
                                    var currentCustomerId =  localStorage.currentCustomerId != null ? localStorage.currentCustomerId: "";
    <?}elseif($fmpsalesdashlet){?>
                                    var currentLocation =  <? echo "'$loc'";?>;
                                    localStorage.currentLocation = currentLocation;
		var currentRegion =  <? echo "'$reg'";?>;
                                    localStorage.currentRegion = currentRegion
		var currentDealerType =   <? echo "'$dealer_post'";?>;
                                    localStorage.currentDealerType = currentDealerType;
		var currentSlsm =  <? echo "'$slsm'";?>;
                                    localStorage.currentSlsm =currentSlsm;
                                    var currentSlsmTitle = <? echo "'$slsm_title'";?>;
                                    localStorage.currentSlsmTitle = currentSlsmTitle;
                                    var currentCustomer = "";
                                    localStorage.currentCustomerCustno = currentCustomer;
                                    var currentCustomerId =  "";
                                    localStorage.currentCustomerId = currentCustomerId;
    <?}else{?>
                                    var currentLocation =  "";
                                    localStorage.currentLocation = currentLocation;
		var currentRegion =  "";
                                    localStorage.currentRegion = currentRegion
		var currentDealerType =   "";
                                    localStorage.currentDealerType = currentDealerType;
		var currentSlsm =  "";
                                    localStorage.currentSlsm =currentSlsm;
                                    var currentSlsmTitle = "";
                                    localStorage.currentSlsmTitle = currentSlsmTitle;
                                    var currentCustomer = "";
                                    localStorage.currentCustomerCustno = currentCustomer;
                                    var currentCustomerId =  "";
                                    localStorage.currentCustomerId = currentCustomerId;
    <?}?>
        <?if($checked_keep_filters == '' ) {?>
                        localStorage.tablePaginator = 100;
                        localStorage.tablePaginatorBefore = 100;
                <?}?>                                          
		updateDivTitle();
		refreshDataTables();
		
		
		function updateDivTitle() {
                                            var s = "";
                                            var slash = false;

                                            if(currentRegion != "") {
                                                s += " Region " + currentRegion;
                                                slash = true;
                                            }

                                            if(currentLocation != "") {
                                                if(slash) s += " / ";
                                                s += " Location " + currentLocation;
                                                slash = true;
                                            }

                                            if(currentSlsm != "") {
                                                if(slash) s += " / ";
                                                s += " Slsm " + currentSlsmTitle;
                                                slash = true;
                                            }

                                            if(currentDealerType != "") {
                                                if(slash) s += " / ";
                                                s += " Customer Type " + currentDealerType;
                                                slash = true;
                                            }

                                            if(currentCustomer != "") {
                                                if(slash) s += " / ";
                                                s += " Customers " + currentCustomer;
                                            }

                                            if(s == "") {
                                                s = "All Sales";
                                            }
                                                    $("#currtitle-salessummary").html(s);
		}
		
                
		function refreshDataTables() {
			var slsmList="";
			var regionList="";
			var locationList="";
			var selectMethod=""; /*"i";  /* intersection or union -- not used anymore */ 
			var account_id="";
			slsmList=currentSlsm;
			regionList=currentRegion;
			locationList=currentLocation;
                                                      account_id=currentCustomerId;
			YAHOO.FMP.SalesSummary = createSalesSummaryDataTable('salessummary', selectMethod, slsmList,  regionList, locationList,currentDealerType,account_id)();
			
                                                       /*YAHOO.FMP.CustomerAR = createCustomerARDataTable('customerar', selectMethod, slsmList, regionList, locationList, currentDealerType,account_id)();
			YAHOO.FMP.CustomerSales = createCustomerSalesDataTable('customersales', selectMethod, slsmList, regionList, locationList,currentDealerType,'',account_id)();
			YAHOO.FMP.CustomerSalesNonOE = createCustomerSalesNonoeDataTable('customersalesnonoe', selectMethod, slsmList, regionList, locationList,currentDealerType,'nonoe',account_id)();
			YAHOO.FMP.CustomerSalesUnderCar = createCustomerSalesUndercarDataTable('customersalesundercar', selectMethod, slsmList, regionList, locationList,currentDealerType,'undercar',account_id)();
            YAHOO.FMP.CustomerSalesComparison = createCustomerSalesComparisonDataTable('customersalescomparison', selectMethod, slsmList, regionList, locationList, currentDealerType,account_id)();
			YAHOO.FMP.CustomerBudgetComparison = createCustomerBudgetComparisonDataTable('customerbudgetcomparison', selectMethod, slsmList, regionList, locationList, currentDealerType,account_id)();
            YAHOO.FMP.CustomerReturns = createCustomerReturnsDataTable('customerreturns', selectMethod, slsmList, regionList, locationList, currentDealerType,account_id)();
            YAHOO.FMP.CustomerTransactions = createCustomerTransactionsDataTable('customertransactions', selectMethod, slsmList, regionList, locationList, currentDealerType,account_id)();
            YAHOO.FMP.CustomerBudget = createCustomerBudgetDataTable('customerbudget', selectMethod, slsmList, regionList, locationList, currentDealerType,account_id)();*/

		};
   	
});
</script>

<!-- sales summary  -->
<script type="text/javascript">
function createSalesSummaryDataTable (divNameParm, selectMethodParm, slsmParm, regionParm, locationParm, dealerTypeParm,account_id) {
    if(divNameParm == 'salessummary'){
	return function() {

		var title="";
		var i;
		
		if(selectMethodParm != 'u') {
			selectMethodParm = 'i'; /* intersection */
		}
		
		if(slsmParm.length > 0) {
		   i=slsmParm.indexOf(';');
		   if(i > -1) {
			title = "Slsm " + slsmParm.substring(0,i);
		   } else {
		   	title="Slsm " + slsmParm;
		   }
		}
		if(regionParm.length > 0) {
		   if(title.length > 0) {
				title += ", ";
			}
			title += "Region " + regionParm;
		}
		
		if(locationParm.length > 0) {
			if(title.length > 0) {
				title += ", ";
			}
			title += "Location " + locationParm;
		}
			
		if(title.length == 0) {
			title = "Corporate";
		}
		
		title += " Totals";
		
		if(dealerTypeParm.length > 0) {
			if(title.length > 0) {
				title += ", ";
			}
			title += dealerTypeParm + " Dealer Type";
		}
		title = "&nbsp;";
						
		var myFormatter = function(elCell, oRecord, oColumn, oData) {
			if(oData != null) {
			if(oData.toString().indexOf('%') >= 0) {
                                                            if(parseInt(oData) < 0){
                                                                elCell.innerHTML = '<span style="color: red">('+oData+')</span>';
                                                            }else{
                                                                elCell.innerHTML = '<span>'+oData+'</span>';
                                                            }
			} else {
				var oFormatConfig = {
					prefix: "$",
					decimalPlaces: 0,
					decimalSeparator: ".",
					thousandsSeparator: ",",
					suffix: ""
				};
					
				if (oData < 0) {
                                                                            elCell.innerHTML= '<span style="color: red">('+YAHOO.util.Number.format(oData*(-1), oFormatConfig)+')</span>';
                                                                        } else {
                                                                            elCell.innerHTML = YAHOO.util.Number.format(oData, oFormatConfig);
                                                                        }
			}
			}
		};
		
		var summaryColumnDefs = [ // sortable:true enables sorting
			{label:title, children:[
				{key:"label1", label:""},
				{key:"amount1", label:"", formatter: myFormatter}, //YAHOO.widget.DataTable.formatCurrency},
				{key:"label2", label:""},
				{key:"amount2", label:"", formatter: myFormatter}
			]}
		];

		
		// DataSource instance
		var summaryDataSource = new YAHOO.util.DataSource("json_proxy_sales.php?");
		summaryDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
		summaryDataSource.responseSchema = {
			resultsList: "records",
			fields: [
				{key:"label1"},
				{key:"amount1"}, //, parser:"number"},
				{key:"label2"},
				{key:"amount2"} //,parser:"number"}
			],
			metaFields: {
				totalRecords: "totalRecords" // Access to value in the server response
			}
		};
		
		// DataTable configuration
		var summaryConfigs = {
			initialRequest: "", //"sort=id&dir=asc&startIndex=0&results=25", // Initial request for first page of data
			dynamicData: true, // Enables dynamic server-driven data
			initialRequest: "select=" + selectMethodParm +
                                                                                    "&slsm=" + slsmParm +
                                                                                    "&region=" + regionParm + 
                                                                                    "&location=" + locationParm + 
                                                                                    "&dealertype=" + dealerTypeParm +
                                                                                    "&slsm=" + slsmParm +
                                                                                    "&account=" + account_id
			/*sortedBy : {key:"id", dir:YAHOO.widget.DataTable.CLASS_ASC}, // Sets UI initial sort arrow
			paginator: new YAHOO.widget.Paginator({ rowsPerPage:25 }) // Enables pagination  */
		};
		
		// DataTable instance
		var summaryDataTable = new YAHOO.widget.DataTable(divNameParm, summaryColumnDefs, summaryDataSource, summaryConfigs);
		// Update totalRecords on the fly with value from server
		summaryDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
			oPayload.totalRecords = oResponse.meta.totalRecords;
			return oPayload;
		}
		
		return {
			ds: summaryDataSource,
			dt: summaryDataTable
		};
			
	};
    }else{
        return function(){};
    }
}
</script>

<!-- customer AR -->
<script type="text/javascript">
function createCustomerARDataTable(divNameParm, selectMethodParm, slsmParm, regionParm, locationParm, dealerTypeParm,account_id) {
    if(divNameParm == 'customerar'){
	return function() {
	
		if(selectMethodParm != 'u') {
			selectMethodParm = 'i'; /* intersection */
		}

		this.custlink = function(elCell, oRecord, oColumn, oData) {
			elCell.innerHTML = "<a href=\"" + "<? echo $baseurl."/index.php?module=Accounts&action=DetailView&record="; ?>" + oRecord.getData('id') + "\" target=\"_blank\">" + oData + "</a>";
		};
                                    var termsCodeList = <? echo buildTermsCodeList();?>;
                                    this.termscode = function(elCell, oRecord, oColumn, oData) {
                                                elCell.innerHTML = termsCodeList[oData];
		};
                                    this.redcolor= function(elCell, oRecord, oColumn, oData) {
                                            if (oData >= 80) {
                                               elCell.innerHTML = '<span style="color: green">'+oData.toFixed(2)+'%</span>';
                                            }else if (oData < 0) {
                                                 elCell.innerHTML = '<span style="color: red">('+oData.toFixed(2)*(-1)+'%)</span>';
                                            }else if(oData != null){
                                                elCell.innerHTML = '<span>'+oData.toFixed(2)+'%</span>';
                                            }else if(oData == null){
                                                oData = 0;
                                                elCell.innerHTML = '<span>'+oData.toFixed(2)+'%</span>';
                                            }
		};
                                    this.currencyRed= function(elCell, oRecord, oColumn, oData) {
                                                if(oData != null) {
                                                    var oFormatConfig = {
					prefix: "$",
					decimalPlaces: 2,
					decimalSeparator: ".",
					thousandsSeparator: ",",
					suffix: ""
				};
			if (oData < 0) {
                                                             elCell.innerHTML= '<span style="color: red">('+YAHOO.util.Number.format(oData*(-1), oFormatConfig)+')</span>';
			} else {
                                                             elCell.innerHTML = YAHOO.util.Number.format(oData, oFormatConfig);
			}
                                            }
		};
		var myColumnDefs = [ // sortable:true enables sorting
			{key:"slsm", label:"Slsm", sortable:true,resizeable:true},
			{key:"custno", label:"CustNo", sortable:true},
			{key:"custname", label:"Name", sortable:true, formatter:this.custlink},

                                                      {key:"avg_days", label:"Avg Days", sortable:true},
                                                      {key:"termscode", label:"Terms Code", sortable:true, formatter:this.termscode},
                                                      {key:"creditcode", label:"Credit Code", sortable:true},
                                                      
                                                      {key:"shipping_address_street", label:"Address", sortable:true, hidden:true},
			{key:"shipping_address_city", label:"City", sortable:true, hidden:true},
                                                      {key:"shipping_address_state", label:"State", sortable:true, hidden:true},
			{key:"shipping_address_postalcode", label:"Zip", sortable:true, hidden:true},
                                                      {key:"contact", label:"Contact", sortable:true, hidden:true},
                                                      {key:"phone", label:"Phone", sortable:true, hidden:true},
                                                        
			{key:"future", label:"Future", sortable:true, formatter: this.currencyRed},
			{key:"current", label:"Current", sortable:true, formatter: this.currencyRed},
			{key:"ar30_60", label:"30 - 60", sortable:true, formatter: this.currencyRed},
			{key:"ar60_90", label:"60 - 90", sortable:true, formatter: this.currencyRed},
			{key:"over_90", label:"90+", sortable:true, formatter: this.currencyRed},
			{key:"aarbal", label:"Balance", sortable:true, formatter: this.currencyRed},
                        
                                                      {key:"creditlimit", label:"Credit Limit", sortable:true, formatter: this.currencyRed},
                                                      {key:"aarbal_to_creditlimit", label:"% of Balance to Credit Limit", sortable:true,className:'white-sp', formatter:this.redcolor}
		];


	
		// DataSource instance
		var myDataSource = new YAHOO.util.DataSource("json_proxy_customer_ar.php?");
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
		myDataSource.responseSchema = {
			resultsList: "records",
			fields: [
				{key:"id"},
				{key:"slsm",width:"10px"},
				{key:"slsmname"},
				{key:"custno"},
				{key:"custname"},
                                                                        {key:"avg_days"},
                                                                        {key:"termscode"},
                                                                        {key:"creditcode"},
                                                                        {key:"shipping_address_street"},
                                                                        {key:"shipping_address_city"},
                                                                        {key:"shipping_address_state"},
                                                                        {key:"shipping_address_postalcode"},
                                                                        {key:"contact"},
                                                                        {key:"phone"},
				{key:"future",parser:"number"},
				{key:"current",parser:"number"},
				{key:"ar30_60",parser:"number"},
				{key:"ar60_90",parser:"number"},
				{key:"over_90",parser:"number"},
				{key:"aarbal",parser:"number"},
                                                                        {key:"creditlimit",parser:"number"},
                                                                        {key:"aarbal_to_creditlimit",parser:"number"}
			],
			metaFields: {
				totalRecords: "totalRecords"// Access to value in the server response
			}
		};
		 // Create the Paginator 
                                    var myPaginator = new YAHOO.widget.Paginator({ 
                                        rowsPerPage:typeof localStorage.tablePaginator != "undefited" && localStorage.tablePaginator != null ? localStorage.tablePaginator : 100,
                                        containers : ["customerar-pag-nav"], 
                                        template : "<div class='counter-nav'>{CurrentPageReport} {RowsPerPageDropdown}</div><center style='clear: both;'>{FirstPageLink}{PreviousPageLink}{PageLinks} {NextPageLink}{LastPageLink} </center>", 
                                        pageReportTemplate : "Showing items {startRecord} - {endRecord} of {totalRecords}", 
                                        rowsPerPageOptions : [10,20,30,40,50,100,200,{ value : 5000, text : "All" } ]  
                                    }); 
		//future requets of data
		var myRequestBuilder = function(oState, oSelf) { 
			// Standard stuff:
			oState = oState || {pagination:null, sortedBy:null}; 
                                                      if(oState.pagination && oState.pagination.before){
                                                          localStorage.tablePaginator = oState.pagination.rowsPerPage;
                                                          localStorage.tablePaginatorBefore = oState.pagination.before.rowsPerPage;
                                                      }
			var sort = (oState.sortedBy) ? oState.sortedBy.key : "myDefaultColumnKey"; 
			var dir = (oState.sortedBy && oState.sortedBy.dir === YAHOO.widget.DataTable.CLASS_DESC) ? "desc" : "asc"; 
			var startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0; 
			var results = (oState.pagination) ? oState.pagination.rowsPerPage : 100; 
			
			 
			// Build custom request 
			return  "select=" + selectMethodParm +
					"&slsm=" + slsmParm +
			        "&region=" + regionParm +
			        "&location=" + locationParm +
					"&dealertype=" + dealerTypeParm +
					"&sort=" + sort + 
					"&dir=" + dir + 
					"&startIndex=" + startIndex + 
					"&results=" + results+
                                                                                    "&account=" + account_id; 
		}; 
		
		// DataTable configuration
		var myConfigs = {
			initialRequest: "select=" + selectMethodParm +
							"&slsm=" + slsmParm +
			                "&region=" + regionParm +
			                "&location=" + locationParm + 
							"&dealertype=" + dealerTypeParm +
							"&sort=custname" + 
							"&dir=asc" + 
							"&startIndex=0" + 
							"&results=100"+
                                                                                    "&account=" + account_id, // Initial request for first page of data
			generateRequest: myRequestBuilder,
			dynamicData: true, // Enables dynamic server-driven data
			sortedBy : {key:"custname", dir:YAHOO.widget.DataTable.CLASS_ASC}, // Sets UI initial sort arrow
			paginator:myPaginator,
                                                    scrollable: "y",
                                                height: "490px",
                                                width:  "100%"
		};
		
		// DataTable instance
		var myDataTable = new YAHOO.widget.DataTable(divNameParm, myColumnDefs, myDataSource, myConfigs);
		
		// Update totalRecords on the fly with value from server
		myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
			oPayload.totalRecords = oResponse.meta.totalRecords;
			return oPayload;
		}
		
		
		/* slsm tooltip */
		var showTimer = 0, hideTimer = 0;
		var tt = new YAHOO.widget.Tooltip("myTooltip");
		myDataTable.on('cellMouseoverEvent', function (oArgs) {
			if (showTimer) {
				window.clearTimeout(showTimer);
				showTimer = 0;
			}

			var target = oArgs.target;
			var column = this.getColumn(target);
			if (column != null && column.key == 'slsm') {
				var record = this.getRecord(target);
				var description = record.getData('slsm') || '??';
				description += ' ';
				description += record.getData('slsmname') || '??? ????';
				/* var xy = [parseInt(oArgs.event.clientX,10) + 10 ,parseInt(oArgs.event.clientY,10) + 10 ]; */
				var xy = YAHOO.util.Event.getXY(oArgs.event);
				showTimer = window.setTimeout(function() {
					tt.setBody(description);
					tt.cfg.setProperty('xy',xy);
					tt.show();
					hideTimer = window.setTimeout(function() {
						tt.hide();
					},5000);
				},500);
			}
		});
		myDataTable.on('cellMouseoutEvent', function (oArgs) {
			if (showTimer) {
				window.clearTimeout(showTimer);
				showTimer = 0;
			}
			if (hideTimer) {
				window.clearTimeout(hideTimer);
				hideTimer = 0;
			}
			tt.hide();
		});
                                    // Shows dialog, creating one when necessary
                                    var newCols = true;
                                    var showDlg = function(e) {
                                        YAHOO.util.Event.stopEvent(e);
                                        
                                        if(newCols) {
                                            var cell = document.getElementById("customerar-dlg-picker");

                                            if ( cell.hasChildNodes() )
                                            {
                                                while ( cell.childNodes.length >= 1 )
                                                {
                                                    cell.removeChild( cell.firstChild );       
                                                } 
                                            }
                                            // Populate Dialog
                                            // Using a template to create elements for the SimpleDialog
                                            var allColumns = myDataTable.getColumnSet().keys;
                                            var elPicker = YAHOO.util.Dom.get("customerar-dlg-picker");
                                            var elTemplateCol = document.createElement("div");
                                            YAHOO.util.Dom.addClass(elTemplateCol, "dt-dlg-pickercol");
                                            var elTemplateKey = elTemplateCol.appendChild(document.createElement("span"));
                                            YAHOO.util.Dom.addClass(elTemplateKey, "dt-dlg-pickerkey");
                                            var elTemplateBtns = elTemplateCol.appendChild(document.createElement("span"));
                                            YAHOO.util.Dom.addClass(elTemplateBtns, "dt-dlg-pickerbtns");
                                            var onclickObj = {fn:handleButtonClick, obj:this, scope:false };

                                            // Create one section in the SimpleDialog for each Column
                                            var elColumn, elKey, oButtonGrp;
                                            for(var i=0,l=allColumns.length;i<l;i++) {
                                                var oColumn = allColumns[i];

                                                // Use the template
                                                elColumn = elTemplateCol.cloneNode(true);

                                                // Write the Column key
                                                elKey = elColumn.firstChild;
                                                elKey.innerHTML = oColumn.label;

                                                // Create a ButtonGroup
                                                oButtonGrp = new YAHOO.widget.ButtonGroup({ 
                                                    id: "customerar-buttongrp-"+oColumn.getKey(), 
                                                    name: oColumn.getKey(), 
                                                    container: elKey.nextSibling
                                                });
                                                oButtonGrp.addButtons([
                                                    { label: "Show", value: "Show", checked: (!oColumn.hidden), onclick: onclickObj},
                                                    { label: "Hide", value: "Hide", checked: (oColumn.hidden), onclick: onclickObj}
                                                ]);

                                                elPicker.appendChild(elColumn);
                                            }
                                            newCols = false;
                                        }
                                        myDlg.show();
                                    };
                                    var hideDlg = function(e) {
                                        this.hide();
                                    };
                                    var handleButtonClick = function(e, oSelf) {
                                        var sKey = this.get("name");
                                        if(this.get("value") === "Hide"){
                                            myDataTable.hideColumn(sKey);
                                        }else{
                                            myDataTable.showColumn(sKey);
                                        }
                                        TopScrollTable(myDataTable,divNameParm);
                                        var classTd = myDataTable.getColumn(sKey)._elTh.className.split(/\s/);  
                                        var cells =  YAHOO.util.Dom.getElementsByClassName(classTd);
                                        var mode =this.get("value") === "Hide" ?  'none': 'table-cell';
                                        
                                        for(j = 0; j < cells.length; j++) cells[j].style.display = mode;  
                                        
                                    };

                                    // Create the SimpleDialog
                                    YAHOO.util.Dom.removeClass("customerar-dlg", "inprogress");
                                    YAHOO.util.Dom.setStyle("customerar-dlg","display","block");
                                    var myDlg = new YAHOO.widget.SimpleDialog("customerar-dlg", {
                                        width: "30em",
                                        visible: false,
                                        modal: false,
                                        buttons: [ 
                                            { text:"Close",  handler:hideDlg }
                                        ],
                                        fixedcenter: true,
                                        constrainToViewport: true
                                    });
                                    myDlg.render();

                                    // Nulls out myDlg to force a new one to be created
                                    myDataTable.subscribe("columnReorderEvent", function(){
                                        newCols = true;
                                        YAHOO.util.Event.purgeElement("customerar-dlg-picker", true);
                                        YAHOO.util.Dom.get("customerar-dlg-picker").innerHTML = "";
                                    }, this, true);
                                    
                                    myDataTable.subscribe('postRenderEvent', function()    {
                                        var divWidth = $("#customerar").width();
                                        var tableWidth = $("#customerar .yui-dt-bd table").width();
                                        var borderRight = divWidth - tableWidth;
                                        var paddR_name = 0;
                                        var paddR_last = 0;
                                        var currBrowser = browserDetectNav();
                                        var winWidth =window.outerWidth;
                                        if(winWidth >= 1152 && winWidth < 1280){
                                            paddR_name = currBrowser == "Firefox" ? 0:0;
                                            paddR_last = currBrowser == "Firefox" ? 0:0;
                                        }else if(winWidth >= 1280 && winWidth < 1360){
                                            paddR_name = currBrowser == "Firefox" ? 3:3;
                                            paddR_last = currBrowser == "Firefox" ? 2:2;
                                        }else if(winWidth >= 1360 && winWidth < 1440){
                                            paddR_name = currBrowser == "Firefox" ? 4:2;
                                            paddR_last = currBrowser == "Firefox" ? 2:2;
                                        }else if(winWidth >= 1440 && winWidth < 1600){
                                            paddR_name = currBrowser == "Firefox" ? 3:4;
                                            paddR_last = currBrowser == "Firefox" ? 2:2;
                                        }else if(winWidth >= 1600){
                                            paddR_name = currBrowser == "Firefox" ? 3:3;
                                            paddR_last = currBrowser == "Firefox" ? 11:10;
                                        }
                                        var theadEle = this.getTheadEl(),
                                        thEle = theadEle.getElementsByClassName("yui-dt-last")[0].getElementsByTagName('th');
                                        $("th.yui-dt-col-custname").css('padding-right',paddR_name+'px');
                                        $("th.yui-dt-col-custname div").height(theadEle.offsetHeight);
                                        $("th.yui-dt-col-custno div").height(theadEle.offsetHeight);
                                        $("th.yui-dt-col-custname  div").css('line-height',theadEle.offsetHeight+'px');
                                        $("th.yui-dt-col-custno  div").css('line-height',theadEle.offsetHeight+'px');
                                        var lastCol = thEle.length - 1;
                                        for(var i=0; i < thEle.length; i++) {
                                            if(!YAHOO.util.Dom.hasClass(thEle[i], "hiden-col")) {
                                                lastCol = i;
                                            }
                                        }
                                        thEle[lastCol].style.borderRight = borderRight+"px solid #F2F2F2";
                                         thEle[lastCol].style.paddingRight =paddR_last+'px';
                                        TopScrollTable(this,divNameParm);
                                    });
                                    YAHOO.util.Event.addListener("customerar-swhd-link", "click", showDlg, this, true);
		return {
			ds: myDataSource,
			dt: myDataTable
		};

	};
        }else{
           return function(){};
        }
}
</script>



<!-- customer sales -->
<script type="text/javascript">
function createCustomerSalesDataTable(divNameParm, selectMethodParm, slsmParm, regionParm, locationParm, dealerTypeParm, specialTypeParm,account_id) {
    if((divNameParm == 'customersales' && specialTypeParm == '') || (divNameParm == 'customersalesnonoe' && specialTypeParm == 'nonoe') || (divNameParm == 'customersalesundercar' && specialTypeParm == 'undercar')) {	
            return function() {
		if(selectMethodParm != 'u') {
			selectMethodParm = 'i'; /* intersection */
		}

		this.custlink = function(elCell, oRecord, oColumn, oData) {
			elCell.innerHTML = "<a href=\"" + "<? echo $baseurl."/index.php?module=Accounts&action=DetailView&record="; ?>" + oRecord.getData('id') + "\" target=\"_blank\">" + oData + "</a>";
		};
		this.precent= function(elCell, oRecord, oColumn, oData) {
                                            if (oData < 0) {
                                               elCell.innerHTML = '<span style="color: red">('+oData.toFixed(2)*(-1)+'%)</span>';
                                            }else if(oData != null){
                                                elCell.innerHTML = '<span>'+oData.toFixed(2)+'%</span>';
                                            }else if(oData == null){
                                                oData = 0;
                                                elCell.innerHTML = '<span>'+oData.toFixed(2)+'%</span>';
                                            }
		};
                                    this.currencyRed= function(elCell, oRecord, oColumn, oData) {
                                                if(oData != null) {
                                                    var oFormatConfig = {
					prefix: "$",
					decimalPlaces: 0,
					decimalSeparator: ".",
					thousandsSeparator: ",",
					suffix: ""
				};
			if (oData < 0) {
                                                             elCell.innerHTML= '<span style="color: red">('+YAHOO.util.Number.format(oData*(-1), oFormatConfig)+')</span>';
			} else {
                                                             elCell.innerHTML = YAHOO.util.Number.format(oData, oFormatConfig);
			}
                                            }
		};
		// Column definitions
		var myColumnDefs = [ // sortable:true enables sorting
		       {key:"slsm_acc",label:"", children: [
					{key:"slsm", label:"Slsm", sortable:true},
					{key:"custno", label:"CustNo", sortable:true},
					{key:"custname", label:"Name", sortable:true, formatter:this.custlink},
                                                      {key:"shipping_address_street", label:"Address", sortable:true,  className: "hiden-col"},
			{key:"shipping_address_city", label:"City", sortable:true, className: "hiden-col"},
                                                      {key:"shipping_address_state", label:"State", sortable:true, className: "hiden-col"},
			{key:"shipping_address_postalcode", label:"Zip", sortable:true, className: "hiden-col"},
                                                      {key:"contact", label:"Contact", sortable:true, className: "hiden-col"},
                                                      {key:"phone", label:"Phone", sortable:true, className: "hiden-col"},
				]
		    },
                      
		    {key:"mtd",label:"MTD", children: [
					{key:"mtd_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "sales-width"},
					{key:"mtd_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "gp-width"},
					{key:"mtd_gpp", label:"GP%", sortable:true, formatter:this.precent, className: "gpp-width"},
				]
		    },
		    {key:"ytd",label:"YTD", children: [
					{key:"ytd_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "sales-width"},
					{key:"ytd_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "gp-width"},
					{key:"ytd_gpp", label:"GP%", sortable:true, formatter:this.precent, className: "gpp-width"},
				]
		    },
		    {key:"ly",label:"LY", children: [
					{key:"ly_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "sales-width"},
					{key:"ly_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "gp-width"},
					{key:"ly_gpp", label:"GP%", sortable:true, formatter:this.precent, className: "gpp-width"}
				]
		    },
                                        {key:"lm",label:"LM",  className: "hiden-col", children: [
					{key:"lm_sales", label:"Sales", sortable:true, formatter:this.currencyRed,  className: "hiden-col"},
					{key:"lm_gp", label:"GP", sortable:true, formatter:this.currencyRed,  className: "hiden-col"},
					{key:"lm_gpp", label:"GP%", sortable:true, formatter:this.precent,  className: "hiden-col"}
				]
		    },
                                        {key:"lytm",label:"LYTM",  className: "hiden-col", children: [
					{key:"lytm_sales", label:"Sales", sortable:true, formatter:this.currencyRed,  className: "hiden-col"},
					{key:"lytm_gp", label:"GP", sortable:true, formatter:this.currencyRed,  className: "hiden-col"},
					{key:"lytm_gpp", label:"GP%", sortable:true, formatter:this.precent,  className: "hiden-col"}
				]
		    },
                                        {key:"lytd",label:"LYTD",  className: "hiden-col", children: [
					{key:"lytd_sales", label:"Sales", sortable:true, formatter:this.currencyRed,  className: "hiden-col"},
					{key:"lytd_gp", label:"GP", sortable:true, formatter:this.currencyRed,  className: "hiden-col"},
					{key:"lytd_gpp", label:"GP%", sortable:true, formatter:this.precent,  className: "hiden-col"}
				]
		    }
		];


		// Custom parser
		var stringToDate = function(sData) {
			var array = sData.split("-");
			return new Date(array[1] + " " + array[0] + ", " + array[2]);
		};
		
		// DataSource instance
		var myDataSource = new YAHOO.util.DataSource("json_proxy_customer.php?");
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
		myDataSource.responseSchema = {
			resultsList: "records",
			fields: [
				{key:"id"},
				{key:"slsm"},
				{key:"slsmname"},
				{key:"custno"},
				{key:"custname"},
                                 {key:"shipping_address_street"},
                                                                        {key:"shipping_address_city"},
                                                                        {key:"shipping_address_state"},
                                                                        {key:"shipping_address_postalcode"},
                                                                        {key:"contact"},
                                                                        {key:"phone"},
				{key:"mtd_sales",parser:"number"},
				{key:"mtd_gp",parser:"number"},
				{key:"mtd_gpp",parser:"number"},
				{key:"ytd_sales",parser:"number"},
				{key:"ytd_gp",parser:"number"},
				{key:"ytd_gpp", parser:"number"},
				{key:"ly_sales",parser:"number"},
				{key:"ly_gp",parser:"number"},
				{key:"ly_gpp",parser:"number"},
                                                                        {key:"lm_sales",parser:"number"},
				{key:"lm_gp",parser:"number"},
				{key:"lm_gpp",parser:"number"},
                                                                        {key:"lytm_sales",parser:"number"},
				{key:"lytm_gp",parser:"number"},
				{key:"lytm_gpp",parser:"number"},
                                                                        {key:"lytd_sales",parser:"number"},
				{key:"lytd_gp",parser:"number"},
				{key:"lytd_gpp",parser:"number"}
			],
			metaFields: {
				totalRecords: "totalRecords" // Access to value in the server response
			}
		};
		 // Create the Paginator 
                                    var myPaginator = new YAHOO.widget.Paginator({ 
                                        rowsPerPage: typeof localStorage.tablePaginator != "undefited" && localStorage.tablePaginator != null ? localStorage.tablePaginator : 100,
                                        containers : ["customersales-pag-nav"], 
                                        template : "<div class='counter-nav'>{CurrentPageReport} {RowsPerPageDropdown}</div><center style='clear: both;'>{FirstPageLink}{PreviousPageLink}{PageLinks} {NextPageLink}{LastPageLink} </center>", 
                                        pageReportTemplate : "Showing items {startRecord} - {endRecord} of {totalRecords}", 
                                        rowsPerPageOptions : [10,20,30,40,50,100,200,{ value : 5000, text : "All" } ] 
                                    }); 
		//future requets of data
		var myRequestBuilder = function(oState, oSelf) { 
			// Standard stuff:
			oState = oState || {pagination:null, sortedBy:null}; 
                                                      if(oState.pagination && oState.pagination.before){
                                                          localStorage.tablePaginator = oState.pagination.rowsPerPage;
                                                          localStorage.tablePaginatorBefore = oState.pagination.before.rowsPerPage;
                                                      }
			var sort = (oState.sortedBy) ? oState.sortedBy.key : "myDefaultColumnKey"; 
			var dir = (oState.sortedBy && oState.sortedBy.dir === YAHOO.widget.DataTable.CLASS_DESC) ? "desc" : "asc"; 
			var startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0; 
			var results = (oState.pagination) ? oState.pagination.rowsPerPage : 100; 
			
			 
			// Build custom request 
			return  "select=" + selectMethodParm +
					"&slsm=" + slsmParm +
			        "&region=" + regionParm +
			        "&location=" + locationParm +
					"&dealertype=" + dealerTypeParm +
					"&specialtype=" + specialTypeParm +
					"&sort=" + sort + 
					"&dir=" + dir + 
					"&startIndex=" + startIndex + 
					"&results=" + results+
                                                                                    "&account=" + account_id; 
		}; 
		
		// DataTable configuration
		var myConfigs = {
			initialRequest: "select=" + selectMethodParm +
							"&slsm=" + slsmParm +
			                "&region=" + regionParm +
			                "&location=" + locationParm + 
							"&dealertype=" + dealerTypeParm +
							"&specialtype=" + specialTypeParm +
							"&sort=mtd_sales" + 
							"&dir=desc" + 
							"&startIndex=0" + 
							"&results=100"+
                                                                                    "&account=" + account_id, // Initial request for first page of data
			generateRequest: myRequestBuilder,
			dynamicData: true, // Enables dynamic server-driven data
			sortedBy : {key:"mtd_sales", dir:YAHOO.widget.DataTable.CLASS_DESC}, // Sets UI initial sort arrow
			paginator: myPaginator ,
                                                    scrollable: "y",
                                                height: "473px",
                                                width:  "100%"
		};
		
		// DataTable instance
		var myDataTable = new YAHOO.widget.DataTable(divNameParm, myColumnDefs, myDataSource, myConfigs);
		//myDataTable.hideColumn("slsm");
                    myDataTable.getColumn("slsm_acc")._elTh.setAttribute("colSpan", 3);
		// Update totalRecords on the fly with value from server
		myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
			oPayload.totalRecords = oResponse.meta.totalRecords;
			return oPayload;
		}
		
		
			
		/* slsm tooltip */
		var showTimer = 0, hideTimer = 0;
		var tt = new YAHOO.widget.Tooltip("myTooltip");
		myDataTable.on('cellMouseoverEvent', function (oArgs) {
			if (showTimer) {
				window.clearTimeout(showTimer);
				showTimer = 0;
			}

			var target = oArgs.target;
			var column = this.getColumn(target);
			if (column != null && column.key == 'slsm') {
				var record = this.getRecord(target);
				var description = record.getData('slsm') || '??';
				description += ' ';
				description += record.getData('slsmname') || '??? ????';
				/* var xy = [parseInt(oArgs.event.clientX,10) + 10 ,parseInt(oArgs.event.clientY,10) + 10 ]; */
				var xy = YAHOO.util.Event.getXY(oArgs.event);
				showTimer = window.setTimeout(function() {
					tt.setBody(description);
					tt.cfg.setProperty('xy',xy);
					tt.show();
					hideTimer = window.setTimeout(function() {
						tt.hide();
					},5000);
				},500);
			}
		});
		myDataTable.on('cellMouseoutEvent', function (oArgs) {
			if (showTimer) {
				window.clearTimeout(showTimer);
				showTimer = 0;
			}
			if (hideTimer) {
				window.clearTimeout(hideTimer);
				hideTimer = 0;
			}
			tt.hide();
		});

  // Shows dialog, creating one when necessary
                                    var newCols = true;
                                    var showDlg = function(e) {
                                        YAHOO.util.Event.stopEvent(e);

                                        if(newCols) {
                                            var cell = document.getElementById("customersales-dlg-picker");

                                            if ( cell.hasChildNodes() )
                                            {
                                                while ( cell.childNodes.length >= 1 )
                                                {
                                                    cell.removeChild( cell.firstChild );       
                                                } 
                                            }
                                            // Populate Dialog
                                            // Using a template to create elements for the SimpleDialog
                                            var allColumns = myDataTable.getColumnSet().keys;
                                            var headers = myDataTable.getColumnSet().headers;
                                            var elPicker = YAHOO.util.Dom.get("customersales-dlg-picker");
                                            var elTemplateCol = document.createElement("div");
                                            YAHOO.util.Dom.addClass(elTemplateCol, "dt-dlg-pickercol");
                                            var elTemplateKey = elTemplateCol.appendChild(document.createElement("span"));
                                            YAHOO.util.Dom.addClass(elTemplateKey, "dt-dlg-pickerkey");
                                            var elTemplateBtns = elTemplateCol.appendChild(document.createElement("span"));
                                            YAHOO.util.Dom.addClass(elTemplateBtns, "dt-dlg-pickerbtns");
                                            var onclickObj = {fn:handleButtonClick, obj:this, scope:false };

                                            // Create one section in the SimpleDialog for each Column
                                            var elColumn, elKey, oButtonGrp;
                                            for(var i=0,l=allColumns.length;i<l;i++) {
                                                var oColumn = allColumns[i];
                                                var header  = headers[i][0]; 
                                                var groupname = myDataTable.getColumn(header).label;
                                                // Use the template
                                                elColumn = elTemplateCol.cloneNode(true);

                                                // Write the Column key
                                                elKey = elColumn.firstChild;
                                                elKey.innerHTML = groupname+" "+oColumn.label;

                                                // Create a ButtonGroup
                                                oButtonGrp = new YAHOO.widget.ButtonGroup({ 
                                                    id: "customersales-buttongrp-"+oColumn.getKey(), 
                                                    name: oColumn.getKey(), 
                                                    container: elKey.nextSibling
                                                });
                                              var chek = true;
                                                if(oColumn.className == "hiden-col"){
                                                    chek = false;
                                                }
                                                oButtonGrp.addButtons([
                                                    { label: "Show", value: "Show", checked: (chek), onclick: onclickObj},
                                                    { label: "Hide", value: "Hide", checked: (!chek), onclick: onclickObj}
                                                ]);

                                                elPicker.appendChild(elColumn);
                                            }
                                            newCols = false;
                                        }
                                        myDlg.show();
                                    };
                                    var hideDlg = function(e) {
                                        this.hide();
                                    };
                                    var handleButtonClick = function(e, oSelf) {
                                        var sKey = this.get("name");
                                        
                                        var classTd = myDataTable.getColumn(sKey)._elTh.className.split(/\s/);
                                        if(classTd[0] == "hiden-col"){
                                            var cellClass = classTd[1];
                                        }else{
                                            var cellClass = classTd[0];
                                        }
                                        if(this.get("value") === "Hide"){
                                            YAHOO.util.Dom.removeClass(myDataTable.getColumn(sKey)._elTh, "show-col");
                                        }else{
                                             YAHOO.util.Dom.addClass(myDataTable.getColumn(sKey)._elTh, "show-col");
                                        }
                                        var cells =  YAHOO.util.Dom.getElementsByClassName(cellClass);
                                        var mode = this.get("value") === "Hide" ?  'none': 'table-cell';
                                        var showCol = 0;
                                        var isHide = false;
                                        for(k = 0; k < myDataTable.getColumn(sKey)._oParent.children.length; k++){
                                            var hidenCol = YAHOO.util.Dom.hasClass(myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh, 'hiden-col');
                                            showCol = myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh.style.display != 'none' && !hidenCol ? showCol+1 : showCol;
                                            if(myDataTable.getColumn(sKey)._oParent.children[k].key == sKey && this.get("value") === "Hide")
                                                isHide = myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh.style.display == 'none' ? true:false;
                                        }
                                        if(classTd[0] == "hiden-col"){
                                            removeClass(myDataTable.getColumn(sKey)._elTh,"hiden-col");
                                        }
                                        if(!isHide){
                                            var minusplusColspan =  this.get("value") === "Hide" ?  -1: +1;
                                            var nColspan =   showCol + minusplusColspan;
                                            nColspan = nColspan > myDataTable.getColumn(sKey)._oParent.children.length ? myDataTable.getColumn(sKey)._oParent.children.length: nColspan;

                                            if(nColspan == 0){
                                                myDataTable.getColumn(sKey)._oParent._elTh.style.display = mode;   
                                            }else if(nColspan == 1 && showCol == 0){
                                                myDataTable.getColumn(sKey)._oParent._elTh.style.display = mode; 
                                                myDataTable.getColumn(sKey)._oParent._elTh.setAttribute("colSpan", nColspan);
                                            } else {
                                                myDataTable.getColumn(sKey)._oParent._elTh.setAttribute("colSpan", nColspan);
                                            }

                                            //var widthCell = $(cells[0]).width();
                                            for(j = 0; j < cells.length; j++) {
                                                cells[j].style.display = mode;
                                                //if(widthCell != $(cells[j]).children(0).width())
                                                    //$(cells[j]).children(0).width(widthCell);
                                            }  
                                        }
                                        var theadEle = myDataTable.getTheadEl(),
                                        thEle = theadEle.getElementsByClassName("yui-dt-last")[0].getElementsByTagName('th');
                                        var lastCol = thEle.length - 1;
                                        var countCol = 1;
                                        for(var i=0; i < thEle.length; i++) {
                                            if(!YAHOO.util.Dom.hasClass(thEle[i], "hiden-col") && thEle[i].style.display != 'none') {
                                                thEle[i].style.borderRight = "";
                                                lastCol = i;
                                                countCol++;
                                            }
                                        }
                                        thEle[lastCol].style.borderRight = countCol+"px solid #F2F2F2";
                                        TopScrollTable(myDataTable,divNameParm);
                                    };

                                    // Create the SimpleDialog
                                    YAHOO.util.Dom.removeClass("customersales-dlg", "inprogress");
                                     YAHOO.util.Dom.setStyle("customersales-dlg","display","block");
                                    var myDlg = new YAHOO.widget.SimpleDialog("customersales-dlg", {
                                        width: "30em",
                                        visible: false,
                                        modal: false,
                                        buttons: [ 
                                            { text:"Close",  handler:hideDlg }
                                        ],
                                        fixedcenter: true,
                                        constrainToViewport: true
                                    });
                                    myDlg.render();

                                    // Nulls out myDlg to force a new one to be created
                                    myDataTable.subscribe("columnReorderEvent", function(){
                                        newCols = true;
                                        YAHOO.util.Event.purgeElement("customersales-dlg-picker", true);
                                        YAHOO.util.Dom.get("customersales-dlg-picker").innerHTML = "";
                                    }, this, true);
                                    
		myDataTable.subscribe('renderEvent', function()    {
                                        var theadEle = this.getTheadEl(),
                                        thEle = theadEle.getElementsByTagName('th'),
                                        len = thEle.length;
                                        for(var i=0; i < len; i++) {
                                            var classTd =  this.getColumn(thEle[i].id)._elTh.className.split(/\s/);
                                                var cells =  YAHOO.util.Dom.getElementsByClassName(classTd[0]);
                                            if(YAHOO.util.Dom.hasClass(thEle[i], "show-col")) {
                                                
                                                for(j = 0; j < cells.length; j++) cells[j].style.display = 'table-cell';
                                            }
                                        }
                                    });
                                    myDataTable.subscribe('postRenderEvent', function()    {
                                      var divWidth = $("#customersales").width();
                                       var tableWidth = $("#customersales .yui-dt-bd table").width();
                                       var borderRight = divWidth - tableWidth;
                                       var paddR_name = 0;
                                        var paddR_last = 0;
                                        var currBrowser = browserDetectNav();
                                        var winWidth =window.outerWidth;
                                        if(winWidth >= 1152 && winWidth < 1280){
                                            paddR_name = currBrowser == "Firefox" ? 0:0;
                                            paddR_last = currBrowser == "Firefox" ? 1:1;
                                        }else if(winWidth >= 1280 && winWidth < 1360){
                                            paddR_name = currBrowser == "Firefox" ? 3:3;
                                            paddR_last = currBrowser == "Firefox" ? 2:2;
                                        }else if(winWidth >= 1360 && winWidth < 1440){
                                            paddR_name = currBrowser == "Firefox" ? 3:4;
                                            paddR_last = currBrowser == "Firefox" ? 9:7;
                                        }else if(winWidth >= 1440 && winWidth < 1600){
                                            paddR_name = currBrowser == "Firefox" ? 1:3;
                                            paddR_last = currBrowser == "Firefox" ? 13:13;
                                        }else if(winWidth >= 1600){
                                            paddR_name = currBrowser == "Firefox" ? 2:3;
                                            paddR_last = currBrowser == "Firefox" ? 26:24;
                                        }
                                        var theadEle = this.getTheadEl(),
                                        thEle = theadEle.getElementsByClassName("yui-dt-last")[0].getElementsByTagName('th');
                                        $("th.yui-dt-col-custname").css('padding-right',paddR_name+'px');
                                        var lastCol = thEle.length - 1;
                                        var countCol = 1;
                                        for(var i=0; i < thEle.length; i++) {
                                            if(!YAHOO.util.Dom.hasClass(thEle[i], "hiden-col")) {
                                                lastCol = i;
                                                countCol++;
                                            }
                                        }
                                          thEle[lastCol].style.borderRight = borderRight+"px solid #F2F2F2";
                                         thEle[lastCol].style.paddingRight =paddR_last+'px';
                                        TopScrollTable(this,divNameParm);
                                    });
                                    YAHOO.util.Event.addListener("customersales-swhd-link", "click", showDlg, this, true);
                                    
		return {
			ds: myDataSource,
			dt: myDataTable
		};
	};
        }else{
            return function(){};
        }
}

</script>
<!-- customer Nonoe -->
<script type="text/javascript">
function createCustomerSalesNonoeDataTable(divNameParm, selectMethodParm, slsmParm, regionParm, locationParm, dealerTypeParm, specialTypeParm,account_id) {
    if((divNameParm == 'customersales' && specialTypeParm == '') || (divNameParm == 'customersalesnonoe' && specialTypeParm == 'nonoe') || (divNameParm == 'customersalesundercar' && specialTypeParm == 'undercar')) {	
            return function() {
		if(selectMethodParm != 'u') {
			selectMethodParm = 'i'; /* intersection */
		}

		this.custlink = function(elCell, oRecord, oColumn, oData) {
			elCell.innerHTML = "<a href=\"" + "<? echo $baseurl."/index.php?module=Accounts&action=DetailView&record="; ?>" + oRecord.getData('id') + "\" target=\"_blank\">" + oData + "</a>";
		};
		this.precent= function(elCell, oRecord, oColumn, oData) {
                                            if (oData < 0) {
                                               elCell.innerHTML = '<span style="color: red">('+oData.toFixed(2)*(-1)+'%)</span>';
                                            }else if(oData != null){
                                                elCell.innerHTML = '<span>'+oData.toFixed(2)+'%</span>';
                                            }else if(oData == null){
                                                oData = 0;
                                                elCell.innerHTML = '<span>'+oData.toFixed(2)+'%</span>';
                                            }
		};
                                     this.currencyRed= function(elCell, oRecord, oColumn, oData) {
                                                if(oData != null) {
                                                    var oFormatConfig = {
					prefix: "$",
					decimalPlaces: 0,
					decimalSeparator: ".",
					thousandsSeparator: ",",
					suffix: ""
				};
			if (oData < 0) {
                                                             elCell.innerHTML= '<span style="color: red">('+YAHOO.util.Number.format(oData*(-1), oFormatConfig)+')</span>';
			} else {
                                                             elCell.innerHTML = YAHOO.util.Number.format(oData, oFormatConfig);
			}
                                            }
		};
// Column definitions
		var myColumnDefs = [ // sortable:true enables sorting
		       {key:"slsm_acc",label:"", children: [
					{key:"slsm", label:"Slsm", sortable:true},
					{key:"custno", label:"CustNo", sortable:true},
					{key:"custname", label:"Name", sortable:true, formatter:this.custlink},
                                        {key:"shipping_address_street", label:"Address", sortable:true, className: "hiden-col"},
			{key:"shipping_address_city", label:"City", sortable:true, className: "hiden-col"},
                                                      {key:"shipping_address_state", label:"State", sortable:true, className: "hiden-col"},
			{key:"shipping_address_postalcode", label:"Zip", sortable:true, className: "hiden-col"},
                                                      {key:"contact", label:"Contact", sortable:true, className: "hiden-col"},
                                                      {key:"phone", label:"Phone", sortable:true, className: "hiden-col"},
				]
		    },
		    {key:"mtd",label:"MTD", children: [
					{key:"mtd_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "sales-width"},
					{key:"mtd_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "gp-width"},
					{key:"mtd_gpp", label:"GP%", sortable:true, formatter:this.precent, className: "gpp-width"},
				]
		    },
		    {key:"ytd",label:"YTD", children: [
					{key:"ytd_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "sales-width"},
					{key:"ytd_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "gp-width"},
					{key:"ytd_gpp", label:"GP%", sortable:true, formatter:this.precent, className: "gpp-width"},
				]
		    },
		    {key:"ly",label:"LY", children: [
					{key:"ly_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "sales-width"},
					{key:"ly_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "gp-width"},
					{key:"ly_gpp", label:"GP%", sortable:true, formatter:this.precent, className: "gpp-width"}
				]
		    },
                                        {key:"lm",label:"LM", className: "hiden-col",children: [
					{key:"lm_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "hiden-col"},
					{key:"lm_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "hiden-col"},
					{key:"lm_gpp", label:"GP%", sortable:true, formatter:this.precent, className: "hiden-col"}
				]
		    },
                                        {key:"lytm",label:"LYTM", className: "hiden-col",  children: [
					{key:"lytm_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "hiden-col"},
					{key:"lytm_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "hiden-col"},
					{key:"lytm_gpp", label:"GP%", sortable:true, formatter:this.precent, className: "hiden-col"}
				]
		    },
                                        {key:"lytd",label:"LYTD", className: "hiden-col", children: [
					{key:"lytd_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "hiden-col"},
					{key:"lytd_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "hiden-col"},
					{key:"lytd_gpp", label:"GP%", sortable:true, formatter:this.precent, className: "hiden-col"}
				]
		    }
		];

		// Custom parser
		var stringToDate = function(sData) {
			var array = sData.split("-");
			return new Date(array[1] + " " + array[0] + ", " + array[2]);
		};
		
		// DataSource instance
		var myDataSource = new YAHOO.util.DataSource("json_proxy_customer.php?");
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
		myDataSource.responseSchema = {
			resultsList: "records",
			fields: [
				{key:"id"},
				{key:"slsm"},
				{key:"slsmname"},
				{key:"custno"},
				{key:"custname"},
                                                {key:"shipping_address_street"},
                                                                        {key:"shipping_address_city"},
                                                                        {key:"shipping_address_state"},
                                                                        {key:"shipping_address_postalcode"},
                                                                        {key:"contact"},
                                                                        {key:"phone"},
				{key:"mtd_sales",parser:"number"},
				{key:"mtd_gp",parser:"number"},
				{key:"mtd_gpp",parser:"number"},
				{key:"ytd_sales",parser:"number"},
				{key:"ytd_gp",parser:"number"},
				{key:"ytd_gpp", parser:"number"},
				{key:"ly_sales",parser:"number"},
				{key:"ly_gp",parser:"number"},
				{key:"ly_gpp",parser:"number"},
                                                                        {key:"lm_sales",parser:"number"},
				{key:"lm_gp",parser:"number"},
				{key:"lm_gpp",parser:"number"},
                                                                        {key:"lytm_sales",parser:"number"},
				{key:"lytm_gp",parser:"number"},
				{key:"lytm_gpp",parser:"number"},
                                                                        {key:"lytd_sales",parser:"number"},
				{key:"lytd_gp",parser:"number"},
				{key:"lytd_gpp",parser:"number"}
			],
			metaFields: {
				totalRecords: "totalRecords" // Access to value in the server response
			}
		};
		 // Create the Paginator 
                                    var myPaginator = new YAHOO.widget.Paginator({ 
                                        rowsPerPage: typeof localStorage.tablePaginator != "undefited" && localStorage.tablePaginator != null ? localStorage.tablePaginator : 100,
                                        containers : ["customersalesnonoe-pag-nav"], 
                                        template : "<div class='counter-nav'>{CurrentPageReport} {RowsPerPageDropdown}</div><center style='clear: both;'>{FirstPageLink}{PreviousPageLink}{PageLinks} {NextPageLink}{LastPageLink} </center>", 
                                        pageReportTemplate : "Showing items {startRecord} - {endRecord} of {totalRecords}", 
                                        rowsPerPageOptions : [10,20,30,40,50,100,200,{ value : 5000, text : "All" } ]  
                                    }); 
		//future requets of data
		var myRequestBuilder = function(oState, oSelf) { 
			// Standard stuff:
			oState = oState || {pagination:null, sortedBy:null}; 
                                                      if(oState.pagination && oState.pagination.before){
                                                          localStorage.tablePaginator = oState.pagination.rowsPerPage;
                                                          localStorage.tablePaginatorBefore = oState.pagination.before.rowsPerPage;
                                                      }
			var sort = (oState.sortedBy) ? oState.sortedBy.key : "myDefaultColumnKey"; 
			var dir = (oState.sortedBy && oState.sortedBy.dir === YAHOO.widget.DataTable.CLASS_DESC) ? "desc" : "asc"; 
			var startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0; 
			var results = (oState.pagination) ? oState.pagination.rowsPerPage : 100; 
			
			 
			// Build custom request 
			return  "select=" + selectMethodParm +
					"&slsm=" + slsmParm +
			        "&region=" + regionParm +
			        "&location=" + locationParm +
					"&dealertype=" + dealerTypeParm +
					"&specialtype=" + specialTypeParm +
					"&sort=" + sort + 
					"&dir=" + dir + 
					"&startIndex=" + startIndex + 
					"&results=" + results+
                                                                                    "&account=" + account_id; 
		}; 
		
		// DataTable configuration
		var myConfigs = {
			initialRequest: "select=" + selectMethodParm +
							"&slsm=" + slsmParm +
			                "&region=" + regionParm +
			                "&location=" + locationParm + 
							"&dealertype=" + dealerTypeParm +
							"&specialtype=" + specialTypeParm +
							"&sort=mtd_sales" + 
							"&dir=desc" + 
							"&startIndex=0" + 
							"&results=100"+
                                                                                    "&account=" + account_id, // Initial request for first page of data
			generateRequest: myRequestBuilder,
			dynamicData: true, // Enables dynamic server-driven data
			sortedBy : {key:"mtd_sales", dir:YAHOO.widget.DataTable.CLASS_DESC}, // Sets UI initial sort arrow
			paginator: myPaginator,
                                                    scrollable: "y",
                                                height: "473px",
                                                width:  "100%"
		};
		
		// DataTable instance
		var myDataTable = new YAHOO.widget.DataTable(divNameParm, myColumnDefs, myDataSource, myConfigs);
		myDataTable.getColumn("slsm_acc")._elTh.setAttribute("colSpan", 3);
		// Update totalRecords on the fly with value from server
		myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
			oPayload.totalRecords = oResponse.meta.totalRecords;
			return oPayload;
		}
		
		
			
		/* slsm tooltip */
		var showTimer = 0, hideTimer = 0;
		var tt = new YAHOO.widget.Tooltip("myTooltip");
		myDataTable.on('cellMouseoverEvent', function (oArgs) {
			if (showTimer) {
				window.clearTimeout(showTimer);
				showTimer = 0;
			}

			var target = oArgs.target;
			var column = this.getColumn(target);
			if (column != null && column.key == 'slsm') {
				var record = this.getRecord(target);
				var description = record.getData('slsm') || '??';
				description += ' ';
				description += record.getData('slsmname') || '??? ????';
				/* var xy = [parseInt(oArgs.event.clientX,10) + 10 ,parseInt(oArgs.event.clientY,10) + 10 ]; */
				var xy = YAHOO.util.Event.getXY(oArgs.event);
				showTimer = window.setTimeout(function() {
					tt.setBody(description);
					tt.cfg.setProperty('xy',xy);
					tt.show();
					hideTimer = window.setTimeout(function() {
						tt.hide();
					},5000);
				},500);
			}
		});
		myDataTable.on('cellMouseoutEvent', function (oArgs) {
			if (showTimer) {
				window.clearTimeout(showTimer);
				showTimer = 0;
			}
			if (hideTimer) {
				window.clearTimeout(hideTimer);
				hideTimer = 0;
			}
			tt.hide();
		});
 // Shows dialog, creating one when necessary
                                    var newCols = true;
                                    var showDlg = function(e) {
                                        YAHOO.util.Event.stopEvent(e);

                                        if(newCols) {
                                            var cell = document.getElementById("customersalesnonoe-dlg-picker");

                                            if ( cell.hasChildNodes() )
                                            {
                                                while ( cell.childNodes.length >= 1 )
                                                {
                                                    cell.removeChild( cell.firstChild );       
                                                } 
                                            }
                                            // Populate Dialog
                                            // Using a template to create elements for the SimpleDialog
                                            var allColumns = myDataTable.getColumnSet().keys;
                                            var headers = myDataTable.getColumnSet().headers;
                                            var elPicker = YAHOO.util.Dom.get("customersalesnonoe-dlg-picker");
                                            var elTemplateCol = document.createElement("div");
                                            YAHOO.util.Dom.addClass(elTemplateCol, "dt-dlg-pickercol");
                                            var elTemplateKey = elTemplateCol.appendChild(document.createElement("span"));
                                            YAHOO.util.Dom.addClass(elTemplateKey, "dt-dlg-pickerkey");
                                            var elTemplateBtns = elTemplateCol.appendChild(document.createElement("span"));
                                            YAHOO.util.Dom.addClass(elTemplateBtns, "dt-dlg-pickerbtns");
                                            var onclickObj = {fn:handleButtonClick, obj:this, scope:false };

                                            // Create one section in the SimpleDialog for each Column
                                            var elColumn, elKey, oButtonGrp;
                                            for(var i=0,l=allColumns.length;i<l;i++) {
                                                var oColumn = allColumns[i];
                                                var header  = headers[i][0]; 
                                                var groupname = myDataTable.getColumn(header).label;
                                                // Use the template
                                                elColumn = elTemplateCol.cloneNode(true);

                                                // Write the Column key
                                                elKey = elColumn.firstChild;
                                                elKey.innerHTML = groupname+" "+oColumn.label;

                                                // Create a ButtonGroup
                                                oButtonGrp = new YAHOO.widget.ButtonGroup({ 
                                                    id: "customersalesnonoe-buttongrp-"+oColumn.getKey(), 
                                                    name: oColumn.getKey(), 
                                                    container: elKey.nextSibling
                                                });
                                                 var chek = true;
                                                if(oColumn.className == "hiden-col"){
                                                    chek = false;
                                                }
                                                oButtonGrp.addButtons([
                                                    { label: "Show", value: "Show", checked: (chek), onclick: onclickObj},
                                                    { label: "Hide", value: "Hide", checked: (!chek), onclick: onclickObj}
                                                ]);

                                                elPicker.appendChild(elColumn);
                                            }
                                            newCols = false;
                                        }
                                        myDlg.show();
                                    };
                                    var hideDlg = function(e) {
                                        this.hide();
                                    };
                                    var handleButtonClick = function(e, oSelf) {
                                        var sKey = this.get("name");
                                        
                                        var classTd = myDataTable.getColumn(sKey)._elTh.className.split(/\s/);
                                        if(classTd[0] == "hiden-col"){
                                            var cellClass = classTd[1];
                                        }else{
                                            var cellClass = classTd[0];
                                        }
                                        if(this.get("value") === "Hide"){
                                            YAHOO.util.Dom.removeClass(myDataTable.getColumn(sKey)._elTh, "show-col");
                                        }else{
                                             YAHOO.util.Dom.addClass(myDataTable.getColumn(sKey)._elTh, "show-col");
                                        }
                                        var cells =  YAHOO.util.Dom.getElementsByClassName(cellClass);
                                        var mode = this.get("value") === "Hide" ?  'none': 'table-cell';
                                        var showCol = 0;
                                        var isHide = false;
                                        for(k = 0; k < myDataTable.getColumn(sKey)._oParent.children.length; k++){
                                            var hidenCol = YAHOO.util.Dom.hasClass(myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh, 'hiden-col');
                                            showCol = myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh.style.display != 'none' && !hidenCol ? showCol+1 : showCol;
                                            if(myDataTable.getColumn(sKey)._oParent.children[k].key == sKey && this.get("value") === "Hide")
                                                isHide = myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh.style.display == 'none' ? true:false;
                                        }
                                        if(classTd[0] == "hiden-col"){
                                            removeClass(myDataTable.getColumn(sKey)._elTh,"hiden-col");
                                        }
                                        if(!isHide){
                                            var minusplusColspan =  this.get("value") === "Hide" ?  -1: +1;
                                            var nColspan =   showCol + minusplusColspan;
                                            nColspan = nColspan > myDataTable.getColumn(sKey)._oParent.children.length ? myDataTable.getColumn(sKey)._oParent.children.length: nColspan;

                                            if(nColspan == 0){
                                                myDataTable.getColumn(sKey)._oParent._elTh.style.display = mode;   
                                            }else if(nColspan == 1 && showCol == 0){
                                                myDataTable.getColumn(sKey)._oParent._elTh.style.display = mode; 
                                                myDataTable.getColumn(sKey)._oParent._elTh.setAttribute("colSpan", nColspan);
                                            } else {
                                                myDataTable.getColumn(sKey)._oParent._elTh.setAttribute("colSpan", nColspan);
                                            }

                                            //var widthCell = $(cells[0]).width();
                                            for(j = 0; j < cells.length; j++) {
                                                cells[j].style.display = mode;
                                                //if(widthCell != $(cells[j]).children(0).width())
                                                   // $(cells[j]).children(0).width(widthCell);
                                            }
                                        }
                                           var theadEle = myDataTable.getTheadEl(),
                                            thEle = theadEle.getElementsByClassName("yui-dt-last")[0].getElementsByTagName('th');
                                            var lastCol = thEle.length - 1;
                                            var countCol = 1;
                                            for(var i=0; i < thEle.length; i++) {
                                                if(!YAHOO.util.Dom.hasClass(thEle[i], "hiden-col") && thEle[i].style.display != 'none') {
                                                    thEle[i].style.borderRight = "";
                                                    lastCol = i;
                                                    countCol++;
                                                }
                                            }
                                            thEle[lastCol].style.borderRight = countCol+"px solid #F2F2F2";
                                            TopScrollTable(myDataTable,divNameParm);
                                    };

                                    // Create the SimpleDialog
                                    YAHOO.util.Dom.removeClass("customersalesnonoe-dlg", "inprogress");
                                     YAHOO.util.Dom.setStyle("customersalesnonoe-dlg","display","block");
                                    var myDlg = new YAHOO.widget.SimpleDialog("customersalesnonoe-dlg", {
                                        width: "30em",
                                        visible: false,
                                        modal: false,
                                        buttons: [ 
                                            { text:"Close",  handler:hideDlg }
                                        ],
                                        fixedcenter: true,
                                        constrainToViewport: true
                                    });
                                    myDlg.render();

                                    // Nulls out myDlg to force a new one to be created
                                    myDataTable.subscribe("columnReorderEvent", function(){
                                        newCols = true;
                                        YAHOO.util.Event.purgeElement("customersalesnonoe-dlg-picker", true);
                                        YAHOO.util.Dom.get("customersalesnonoe-dlg-picker").innerHTML = "";
                                    }, this, true);
		myDataTable.subscribe('renderEvent', function()    {
                                        var theadEle = this.getTheadEl(),
                                        thEle = theadEle.getElementsByTagName('th'),
                                        len = thEle.length;
                                        for(var i=0; i < len; i++) {
                                            var classTd =  this.getColumn(thEle[i].id)._elTh.className.split(/\s/);
                                                var cells =  YAHOO.util.Dom.getElementsByClassName(classTd[0]);
                                            if(YAHOO.util.Dom.hasClass(thEle[i], "show-col")) {
                                                
                                                for(j = 0; j < cells.length; j++) cells[j].style.display = 'table-cell';
                                            }
                                        }
                                    });
                                   myDataTable.subscribe('postRenderEvent', function()    {
                                           var divWidth = $("#customersalesnonoe").width();
                                       var tableWidth = $("#customersalesnonoe .yui-dt-bd table").width();
                                       var borderRight = divWidth - tableWidth;
                                        var paddR_name = 0;
                                        var paddR_last = 0;
                                        var currBrowser = browserDetectNav();
                                        var winWidth =window.outerWidth;
                                        if(winWidth >= 1152 && winWidth < 1280){
                                            paddR_name = currBrowser == "Firefox" ? 0:0;
                                            paddR_last = currBrowser == "Firefox" ? 1:1;
                                        }else if(winWidth >= 1280 && winWidth < 1360){
                                            paddR_name = currBrowser == "Firefox" ? 3:3;
                                            paddR_last = currBrowser == "Firefox" ? 2:2;
                                        }else if(winWidth >= 1360 && winWidth < 1440){
                                            paddR_name = currBrowser == "Firefox" ? 3:4;
                                            paddR_last = currBrowser == "Firefox" ? 9:7;
                                        }else if(winWidth >= 1440 && winWidth < 1600){
                                            paddR_name = currBrowser == "Firefox" ? 1:3;
                                            paddR_last = currBrowser == "Firefox" ? 13:13;
                                        }else if(winWidth >= 1600){
                                            paddR_name = currBrowser == "Firefox" ? 2:3;
                                            paddR_last = currBrowser == "Firefox" ? 26:24;
                                        }
                                        var theadEle = this.getTheadEl(),
                                        thEle = theadEle.getElementsByClassName("yui-dt-last")[0].getElementsByTagName('th');
                                        $("th.yui-dt-col-custname").css('padding-right',paddR_name+'px');
                                        var lastCol = thEle.length - 1;
                                        var countCol = 1;
                                        for(var i=0; i < thEle.length; i++) {
                                            if(!YAHOO.util.Dom.hasClass(thEle[i], "hiden-col")) {
                                                lastCol = i;
                                                countCol++;
                                            }
                                        }
                                          thEle[lastCol].style.borderRight = borderRight+"px solid #F2F2F2";
                                         thEle[lastCol].style.paddingRight =paddR_last+'px';
                                        TopScrollTable(this,divNameParm);
                                    });
                                    YAHOO.util.Event.addListener("customersalesnonoe-swhd-link", "click", showDlg, this, true);

		return {
			ds: myDataSource,
			dt: myDataTable
		};

	};
        }else{
            return function(){};
        }
}

</script>
<!-- customer undercar -->
<script type="text/javascript">
function createCustomerSalesUndercarDataTable(divNameParm, selectMethodParm, slsmParm, regionParm, locationParm, dealerTypeParm, specialTypeParm,account_id) {
    if((divNameParm == 'customersales' && specialTypeParm == '') || (divNameParm == 'customersalesnonoe' && specialTypeParm == 'nonoe') || (divNameParm == 'customersalesundercar' && specialTypeParm == 'undercar')) {	
            return function() {
		if(selectMethodParm != 'u') {
			selectMethodParm = 'i'; /* intersection */
		}

		this.custlink = function(elCell, oRecord, oColumn, oData) {
			elCell.innerHTML = "<a href=\"" + "<? echo $baseurl."/index.php?module=Accounts&action=DetailView&record="; ?>" + oRecord.getData('id') + "\" target=\"_blank\">" + oData + "</a>";
		};
		this.precent= function(elCell, oRecord, oColumn, oData) {
                                            if (oData < 0) {
                                               elCell.innerHTML = '<span style="color: red">('+oData.toFixed(2)*(-1)+'%)</span>';
                                            }else if(oData != null){
                                                elCell.innerHTML = '<span>'+oData.toFixed(2)+'%</span>';
                                            }else if(oData == null){
                                                oData = 0;
                                                elCell.innerHTML = '<span>'+oData.toFixed(2)+'%</span>';
                                            }
		};
                                    this.currencyRed= function(elCell, oRecord, oColumn, oData) {
                                                if(oData != null) {
                                                    var oFormatConfig = {
					prefix: "$",
					decimalPlaces: 0,
					decimalSeparator: ".",
					thousandsSeparator: ",",
					suffix: ""
				};
			if (oData < 0) {
                                                             elCell.innerHTML= '<span style="color: red">('+YAHOO.util.Number.format(oData*(-1), oFormatConfig)+')</span>';
			} else {
                                                             elCell.innerHTML = YAHOO.util.Number.format(oData, oFormatConfig);
			}
                                            }
		};
                
		// Column definitions
		var myColumnDefs = [ // sortable:true enables sorting
		       {key:"slsm_acc",label:"", children: [
					{key:"slsm", label:"Slsm", sortable:true},
					{key:"custno", label:"CustNo", sortable:true},
					{key:"custname", label:"Name", sortable:true, formatter:this.custlink},
                                        {key:"shipping_address_street", label:"Address", sortable:true, className: "hiden-col"},
			{key:"shipping_address_city", label:"City", sortable:true, className: "hiden-col"},
                                                      {key:"shipping_address_state", label:"State", sortable:true, className: "hiden-col"},
			{key:"shipping_address_postalcode", label:"Zip", sortable:true, className: "hiden-col"},
                                                      {key:"contact", label:"Contact", sortable:true, className: "hiden-col"},
                                                      {key:"phone", label:"Phone", sortable:true, className: "hiden-col"},
				]
		    },
		    {key:"mtd",label:"MTD", children: [
					{key:"mtd_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "sales-width"},
					{key:"mtd_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "gp-width"},
					{key:"mtd_gpp", label:"GP%", sortable:true, formatter:this.precent, className: "gpp-width"},
				]
		    },
		    {key:"ytd",label:"YTD", children: [
					{key:"ytd_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "sales-width"},
					{key:"ytd_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "gp-width"},
					{key:"ytd_gpp", label:"GP%", sortable:true, formatter:this.precent, className: "gpp-width"},
				]
		    },
		    {key:"ly",label:"LY", children: [
					{key:"ly_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "sales-width"},
					{key:"ly_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "gp-width"},
					{key:"ly_gpp", label:"GP%", sortable:true, formatter:this.precent, className: "gpp-width"}
				]
		    },
                                        {key:"lm",label:"LM",className: "hiden-col",  children: [
					{key:"lm_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "hiden-col"},
					{key:"lm_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "hiden-col"},
					{key:"lm_gpp", label:"GP%", sortable:true, formatter:this.precent, className: "hiden-col"}
				]
		    },
                                        {key:"lytm",label:"LYTM", className: "hiden-col", children: [
					{key:"lytm_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "hiden-col"},
					{key:"lytm_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "hiden-col"},
					{key:"lytm_gpp", label:"GP%", sortable:true, formatter:this.precent, className: "hiden-col"}
				]
		    },
                                        {key:"lytd",label:"LYTD", className: "hiden-col", children: [
					{key:"lytd_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "hiden-col"},
					{key:"lytd_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "hiden-col"},
					{key:"lytd_gpp", label:"GP%", sortable:true, formatter:this.precent, className: "hiden-col"}
				]
		    }
		];


		// Custom parser
		var stringToDate = function(sData) {
			var array = sData.split("-");
			return new Date(array[1] + " " + array[0] + ", " + array[2]);
		};
		
		// DataSource instance
		var myDataSource = new YAHOO.util.DataSource("json_proxy_customer.php?");
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
		myDataSource.responseSchema = {
			resultsList: "records",
			fields: [
				{key:"id"},
				{key:"slsm"},
				{key:"slsmname"},
				{key:"custno"},
				{key:"custname"},
                                 {key:"shipping_address_street"},
                                                                        {key:"shipping_address_city"},
                                                                        {key:"shipping_address_state"},
                                                                        {key:"shipping_address_postalcode"},
                                                                        {key:"contact"},
                                                                        {key:"phone"},
				{key:"mtd_sales",parser:"number"},
				{key:"mtd_gp",parser:"number"},
				{key:"mtd_gpp",parser:"number"},
				{key:"ytd_sales",parser:"number"},
				{key:"ytd_gp",parser:"number"},
				{key:"ytd_gpp", parser:"number"},
				{key:"ly_sales",parser:"number"},
				{key:"ly_gp",parser:"number"},
				{key:"ly_gpp",parser:"number"},
                                                                        {key:"lm_sales",parser:"number"},
				{key:"lm_gp",parser:"number"},
				{key:"lm_gpp",parser:"number"},
                                                                        {key:"lytm_sales",parser:"number"},
				{key:"lytm_gp",parser:"number"},
				{key:"lytm_gpp",parser:"number"},
                                                                        {key:"lytd_sales",parser:"number"},
				{key:"lytd_gp",parser:"number"},
				{key:"lytd_gpp",parser:"number"}
			],
			metaFields: {
				totalRecords: "totalRecords" // Access to value in the server response
			}
		};
		 // Create the Paginator 
                                    var myPaginator = new YAHOO.widget.Paginator({ 
                                        rowsPerPage: typeof localStorage.tablePaginator != "undefited" && localStorage.tablePaginator != null ? localStorage.tablePaginator : 100,
                                        containers : ["customersalesundercar-pag-nav"], 
                                        template : "<div class='counter-nav'>{CurrentPageReport} {RowsPerPageDropdown}</div><center style='clear: both;'>{FirstPageLink}{PreviousPageLink}{PageLinks} {NextPageLink}{LastPageLink} </center>", 
                                        pageReportTemplate : "Showing items {startRecord} - {endRecord} of {totalRecords}", 
                                        rowsPerPageOptions : [10,20,30,40,50,100,200,{ value : 5000, text : "All" } ]  
                                    }); 
		//future requets of data
		var myRequestBuilder = function(oState, oSelf) { 
			// Standard stuff:
			oState = oState || {pagination:null, sortedBy:null}; 
                                                      if(oState.pagination && oState.pagination.before){
                                                          localStorage.tablePaginator = oState.pagination.rowsPerPage;
                                                          localStorage.tablePaginatorBefore = oState.pagination.before.rowsPerPage;
                                                      }
			var sort = (oState.sortedBy) ? oState.sortedBy.key : "myDefaultColumnKey"; 
			var dir = (oState.sortedBy && oState.sortedBy.dir === YAHOO.widget.DataTable.CLASS_DESC) ? "desc" : "asc"; 
			var startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0; 
			var results = (oState.pagination) ? oState.pagination.rowsPerPage : 100; 
			
			 
			// Build custom request 
			return  "select=" + selectMethodParm +
					"&slsm=" + slsmParm +
			        "&region=" + regionParm +
			        "&location=" + locationParm +
					"&dealertype=" + dealerTypeParm +
					"&specialtype=" + specialTypeParm +
					"&sort=" + sort + 
					"&dir=" + dir + 
					"&startIndex=" + startIndex + 
					"&results=" + results+
                                                                                    "&account=" + account_id; 
		}; 
		
		// DataTable configuration
		var myConfigs = {
			initialRequest: "select=" + selectMethodParm +
							"&slsm=" + slsmParm +
			                "&region=" + regionParm +
			                "&location=" + locationParm + 
							"&dealertype=" + dealerTypeParm +
							"&specialtype=" + specialTypeParm +
							"&sort=mtd_sales" + 
							"&dir=desc" + 
							"&startIndex=0" + 
							"&results=100"+
                                                                                    "&account=" + account_id, // Initial request for first page of data
			generateRequest: myRequestBuilder,
			dynamicData: true, // Enables dynamic server-driven data
			sortedBy : {key:"mtd_sales", dir:YAHOO.widget.DataTable.CLASS_DESC}, // Sets UI initial sort arrow
			paginator: myPaginator ,
                                                    scrollable: "y",
                                                height: "473px",
                                                width:  "100%"
		};
		
		// DataTable instance
		var myDataTable = new YAHOO.widget.DataTable(divNameParm, myColumnDefs, myDataSource, myConfigs);
		myDataTable.getColumn("slsm_acc")._elTh.setAttribute("colSpan", 3);
		// Update totalRecords on the fly with value from server
		myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
			oPayload.totalRecords = oResponse.meta.totalRecords;
			return oPayload;
		}
		
		
			
		/* slsm tooltip */
		var showTimer = 0, hideTimer = 0;
		var tt = new YAHOO.widget.Tooltip("myTooltip");
		myDataTable.on('cellMouseoverEvent', function (oArgs) {
			if (showTimer) {
				window.clearTimeout(showTimer);
				showTimer = 0;
			}

			var target = oArgs.target;
			var column = this.getColumn(target);
			if (column != null && column.key == 'slsm') {
				var record = this.getRecord(target);
				var description = record.getData('slsm') || '??';
				description += ' ';
				description += record.getData('slsmname') || '??? ????';
				/* var xy = [parseInt(oArgs.event.clientX,10) + 10 ,parseInt(oArgs.event.clientY,10) + 10 ]; */
				var xy = YAHOO.util.Event.getXY(oArgs.event);
				showTimer = window.setTimeout(function() {
					tt.setBody(description);
					tt.cfg.setProperty('xy',xy);
					tt.show();
					hideTimer = window.setTimeout(function() {
						tt.hide();
					},5000);
				},500);
			}
		});
		myDataTable.on('cellMouseoutEvent', function (oArgs) {
			if (showTimer) {
				window.clearTimeout(showTimer);
				showTimer = 0;
			}
			if (hideTimer) {
				window.clearTimeout(hideTimer);
				hideTimer = 0;
			}
			tt.hide();
		});
// Shows dialog, creating one when necessary
                                    var newCols = true;
                                    var showDlg = function(e) {
                                        YAHOO.util.Event.stopEvent(e);

                                        if(newCols) {
                                            var cell = document.getElementById("customersalesundercar-dlg-picker");

                                            if ( cell.hasChildNodes() )
                                            {
                                                while ( cell.childNodes.length >= 1 )
                                                {
                                                    cell.removeChild( cell.firstChild );       
                                                } 
                                            }
                                            // Populate Dialog
                                            // Using a template to create elements for the SimpleDialog
                                            var allColumns = myDataTable.getColumnSet().keys;
                                            var headers = myDataTable.getColumnSet().headers;
                                            var elPicker = YAHOO.util.Dom.get("customersalesundercar-dlg-picker");
                                            var elTemplateCol = document.createElement("div");
                                            YAHOO.util.Dom.addClass(elTemplateCol, "dt-dlg-pickercol");
                                            var elTemplateKey = elTemplateCol.appendChild(document.createElement("span"));
                                            YAHOO.util.Dom.addClass(elTemplateKey, "dt-dlg-pickerkey");
                                            var elTemplateBtns = elTemplateCol.appendChild(document.createElement("span"));
                                            YAHOO.util.Dom.addClass(elTemplateBtns, "dt-dlg-pickerbtns");
                                            var onclickObj = {fn:handleButtonClick, obj:this, scope:false };

                                            // Create one section in the SimpleDialog for each Column
                                            var elColumn, elKey, oButtonGrp;
                                            for(var i=0,l=allColumns.length;i<l;i++) {
                                                var oColumn = allColumns[i];
                                                var header  = headers[i][0]; 
                                                var groupname = myDataTable.getColumn(header).label;
                                                // Use the template
                                                elColumn = elTemplateCol.cloneNode(true);

                                                // Write the Column key
                                                elKey = elColumn.firstChild;
                                                elKey.innerHTML = groupname+" "+oColumn.label;

                                                // Create a ButtonGroup
                                                oButtonGrp = new YAHOO.widget.ButtonGroup({ 
                                                    id: "customersalesundercar-buttongrp-"+oColumn.getKey(), 
                                                    name: oColumn.getKey(), 
                                                    container: elKey.nextSibling
                                                });
                                                var chek = true;
                                                if(oColumn.className == "hiden-col"){
                                                    chek = false;
                                                }
                                                oButtonGrp.addButtons([
                                                    { label: "Show", value: "Show", checked: (chek), onclick: onclickObj},
                                                    { label: "Hide", value: "Hide", checked: (!chek), onclick: onclickObj}
                                                ]);

                                                elPicker.appendChild(elColumn);
                                            }
                                            newCols = false;
                                        }
                                        myDlg.show();
                                    };
                                    var hideDlg = function(e) {
                                        this.hide();
                                    };
                                    var handleButtonClick = function(e, oSelf) {
                                        var sKey = this.get("name");
                                        
                                        var classTd = myDataTable.getColumn(sKey)._elTh.className.split(/\s/);
                                        if(classTd[0] == "hiden-col"){
                                            var cellClass = classTd[1];
                                        }else{
                                            var cellClass = classTd[0];
                                        }
                                        if(this.get("value") === "Hide"){
                                            YAHOO.util.Dom.removeClass(myDataTable.getColumn(sKey)._elTh, "show-col");
                                        }else{
                                             YAHOO.util.Dom.addClass(myDataTable.getColumn(sKey)._elTh, "show-col");
                                        }
                                        var cells =  YAHOO.util.Dom.getElementsByClassName(cellClass);
                                        var mode = this.get("value") === "Hide" ?  'none': 'table-cell';
                                        var showCol = 0;
                                        var isHide = false;
                                        for(k = 0; k < myDataTable.getColumn(sKey)._oParent.children.length; k++){
                                            var hidenCol = YAHOO.util.Dom.hasClass(myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh, 'hiden-col');
                                            showCol = myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh.style.display != 'none' && !hidenCol ? showCol+1 : showCol;
                                            if(myDataTable.getColumn(sKey)._oParent.children[k].key == sKey && this.get("value") === "Hide")
                                                isHide = myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh.style.display == 'none' ? true:false;
                                        }
                                        if(classTd[0] == "hiden-col"){
                                            removeClass(myDataTable.getColumn(sKey)._elTh,"hiden-col");
                                        }
                                        if(!isHide){
                                            var minusplusColspan =  this.get("value") === "Hide" ?  -1: +1;
                                            var nColspan =   showCol + minusplusColspan;
                                            nColspan = nColspan > myDataTable.getColumn(sKey)._oParent.children.length ? myDataTable.getColumn(sKey)._oParent.children.length: nColspan;

                                            if(nColspan == 0){
                                                myDataTable.getColumn(sKey)._oParent._elTh.style.display = mode;   
                                            }else if(nColspan == 1 && showCol == 0){
                                                myDataTable.getColumn(sKey)._oParent._elTh.style.display = mode; 
                                                myDataTable.getColumn(sKey)._oParent._elTh.setAttribute("colSpan", nColspan);
                                            } else {
                                                myDataTable.getColumn(sKey)._oParent._elTh.setAttribute("colSpan", nColspan);
                                            }

                                            //var widthCell = $(cells[0]).width();
                                            for(j = 0; j < cells.length; j++) {
                                                cells[j].style.display = mode;
                                                //if(widthCell != $(cells[j]).children(0).width())
                                                   // $(cells[j]).children(0).width(widthCell);
                                            }
                                        }
                                            var theadEle = myDataTable.getTheadEl(),
                                            thEle = theadEle.getElementsByClassName("yui-dt-last")[0].getElementsByTagName('th');
                                            var lastCol = thEle.length - 1;
                                            var countCol = 1;
                                            for(var i=0; i < thEle.length; i++) {
                                                if(!YAHOO.util.Dom.hasClass(thEle[i], "hiden-col") && thEle[i].style.display != 'none') {
                                                    thEle[i].style.borderRight = "";
                                                    lastCol = i;
                                                    countCol++;
                                                }
                                            }
                                            thEle[lastCol].style.borderRight = countCol+"px solid #F2F2F2";
                                            TopScrollTable(myDataTable,divNameParm);
                                    };

                                    // Create the SimpleDialog
                                    YAHOO.util.Dom.removeClass("customersalesundercar-dlg", "inprogress");
                                     YAHOO.util.Dom.setStyle("customersalesundercar-dlg","display","block");
                                    var myDlg = new YAHOO.widget.SimpleDialog("customersalesundercar-dlg", {
                                        width: "30em",
                                        visible: false,
                                        modal: false,
                                        buttons: [ 
                                            { text:"Close",  handler:hideDlg }
                                        ],
                                        fixedcenter: true,
                                        constrainToViewport: true
                                    });
                                    myDlg.render();

                                    // Nulls out myDlg to force a new one to be created
                                    myDataTable.subscribe("columnReorderEvent", function(){
                                        newCols = true;
                                        YAHOO.util.Event.purgeElement("customersalesundercar-dlg-picker", true);
                                        YAHOO.util.Dom.get("customersalesundercar-dlg-picker").innerHTML = "";
                                    }, this, true);
		myDataTable.subscribe('renderEvent', function()    {
                                        var theadEle = this.getTheadEl(),
                                        thEle = theadEle.getElementsByTagName('th'),
                                        len = thEle.length;
                                        for(var i=0; i < len; i++) {
                                            var classTd =  this.getColumn(thEle[i].id)._elTh.className.split(/\s/);
                                                var cells =  YAHOO.util.Dom.getElementsByClassName(classTd[0]);
                                            if(YAHOO.util.Dom.hasClass(thEle[i], "show-col")) {
                                                
                                                for(j = 0; j < cells.length; j++) cells[j].style.display = 'table-cell';
                                            }
                                        }
                                    });
                                    myDataTable.subscribe('postRenderEvent', function()    {
                                           var divWidth = $("#customersalesundercar").width();
                                       var tableWidth = $("#customersalesundercar .yui-dt-bd table").width();
                                       var borderRight = divWidth - tableWidth;
                                       var paddR_name = 0;
                                        var paddR_last = 0;
                                        var currBrowser = browserDetectNav();
                                        var winWidth =window.outerWidth;
                                        if(winWidth >= 1152 && winWidth < 1280){
                                            paddR_name = currBrowser == "Firefox" ? 0:0;
                                            paddR_last = currBrowser == "Firefox" ? 1:1;
                                        }else if(winWidth >= 1280 && winWidth < 1360){
                                            paddR_name = currBrowser == "Firefox" ? 3:3;
                                            paddR_last = currBrowser == "Firefox" ? 2:2;
                                        }else if(winWidth >= 1360 && winWidth < 1440){
                                            paddR_name = currBrowser == "Firefox" ? 3:4;
                                            paddR_last = currBrowser == "Firefox" ? 9:7;
                                        }else if(winWidth >= 1440 && winWidth < 1600){
                                            paddR_name = currBrowser == "Firefox" ? 1:3;
                                            paddR_last = currBrowser == "Firefox" ? 13:13;
                                        }else if(winWidth >= 1600){
                                            paddR_name = currBrowser == "Firefox" ? 2:3;
                                            paddR_last = currBrowser == "Firefox" ? 26:24;
                                        }
                                        var theadEle = this.getTheadEl(),
                                        thEle = theadEle.getElementsByClassName("yui-dt-last")[0].getElementsByTagName('th');
                                        $("th.yui-dt-col-custname").css('padding-right',paddR_name+'px');
                                        var lastCol = thEle.length - 1;
                                        var countCol = 1;
                                        for(var i=0; i < thEle.length; i++) {
                                            if(!YAHOO.util.Dom.hasClass(thEle[i], "hiden-col")) {
                                                lastCol = i;
                                                countCol++;
                                            }
                                        }
                                          thEle[lastCol].style.borderRight = borderRight+"px solid #F2F2F2";
                                         thEle[lastCol].style.paddingRight =paddR_last+'px';
                                        TopScrollTable(this,divNameParm);
                                    });
                                    YAHOO.util.Event.addListener("customersalesundercar-swhd-link", "click", showDlg, this, true);


		return {
			ds: myDataSource,
			dt: myDataTable
		};

	};
        }else{
            return function(){};
        }
}

</script>
<!-- customer sales comparison -->
<script type="text/javascript">
function createCustomerBudgetComparisonDataTable(divNameParm, selectMethodParm, slsmParm, regionParm, locationParm, dealerTypeParm, account_id) {
        if(divNameParm == 'customerbudgetcomparison') {
                return function() {
		if(selectMethodParm != 'u') {
			selectMethodParm = 'i'; /* intersection */
		}

		this.custlink = function(elCell, oRecord, oColumn, oData) {
			elCell.innerHTML = "<a href=\"" + "<? echo $baseurl."/index.php?module=Accounts&action=DetailView&record="; ?>" + oRecord.getData('id') + "\" target=\"_blank\">" + oData + "</a>";
		};
		this.precent= function(elCell, oRecord, oColumn, oData) {
                                           if (oData < 0) {
                                               elCell.innerHTML = '<span style="color: red">('+oData.toFixed(2)*(-1)+'%)</span>';
                                            }else if(oData != null){
                                                elCell.innerHTML = '<span>'+oData.toFixed(2)+'%</span>';
                                            }else if(oData == null){
                                                oData = 0;
                                                elCell.innerHTML = '<span>'+oData.toFixed(2)+'%</span>';
                                            }
		};
                                   this.currencyRed= function(elCell, oRecord, oColumn, oData) {
                                                if(oData != null) {
                                                    var oFormatConfig = {
					prefix: "$",
					decimalPlaces: 0,
					decimalSeparator: ".",
					thousandsSeparator: ",",
					suffix: ""
				};
			if (oData < 0) {
                                                             elCell.innerHTML= '<span style="color: red">('+YAHOO.util.Number.format(oData*(-1), oFormatConfig)+')</span>';
			} else {
                                                             elCell.innerHTML = YAHOO.util.Number.format(oData, oFormatConfig);
			}
                                            }
		};
		// Column definitions
		var myColumnDefs = [ // sortable:true enables sorting
		    {key:"slsm_acc",label:"", children: [
					{key:"slsm", label:"Slsm", sortable:true},
					{key:"custno", label:"CustNo", sortable:true},
					{key:"custname", label:"Name", sortable:true, formatter:this.custlink},
                                        {key:"shipping_address_street", label:"Address", sortable:true, className: "hiden-col"},
			{key:"shipping_address_city", label:"City", sortable:true, className: "hiden-col"},
                                                      {key:"shipping_address_state", label:"State", sortable:true, className: "hiden-col"},
			{key:"shipping_address_postalcode", label:"Zip", sortable:true, className: "hiden-col"},
                                                      {key:"contact", label:"Contact", sortable:true, className: "hiden-col"},
                                                      {key:"phone", label:"Phone", sortable:true, className: "hiden-col"},
				]
		    },
			{key:"mtd_budget",label:"MTD vs. CM Budget", children: [
					{key:"mtd_vs_budget_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "sales-width"},
					{key:"mtd_vs_budget_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "gp-width"},
					{key:"mtd_vs_budget_gp_percent", label:"GP%", sortable:true, formatter:this.precent, className: "gpp-width"},
				]
			},  
                        
                                                      {key:"cm_proj_budget",label:"CM Projection vs. CM Budget", children: [
					{key:"cm_proj_vs_budget_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "sales-width"},
					{key:"cm_proj_vs_budget_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "gp-width"},
					{key:"cm_proj_vs_budget_gp_percent", label:"GP%", sortable:true, formatter:this.precent, className: "gpp-width"},
				]
			},
			{key:"ytd_budget",label:"YTD vs. CY Budget", children: [
					{key:"ytd_vs_budget_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "sales-width"},
					{key:"ytd_vs_budget_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "gp-width"},
					{key:"ytd_vs_budget_gp_percent", label:"GP%", sortable:true, formatter:this.precent, className: "gpp-width"},
				]
			},

			{key:"proj_budget",label:"CY Projection vs. CY Budget", children: [	
					{key:"projected_vs_budget_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "sales-width"},
					{key:"projected_vs_budget_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "gp-width"},
					{key:"projected_vs_budget_gp_percent", label:"GP%", sortable:true, formatter:this.precent, className: "gpp-width"},
				]
			},

		];


		// Custom parser
		var stringToDate = function(sData) {
			var array = sData.split("-");
			return new Date(array[1] + " " + array[0] + ", " + array[2]);
		};
		
		// DataSource instance
		var myDataSource = new YAHOO.util.DataSource("json_proxy_customer_budget_comparison.php?");
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
		myDataSource.responseSchema = {
			resultsList: "records",
			fields: [
				{key:"id"},
				{key:"slsm"},
				{key:"slsmname"},
				{key:"custno"},
				{key:"custname"},
                                 {key:"shipping_address_street"},
                                                                        {key:"shipping_address_city"},
                                                                        {key:"shipping_address_state"},
                                                                        {key:"shipping_address_postalcode"},
                                                                        {key:"contact"},
                                                                        {key:"phone"},
							
				{key:"mtd_vs_budget_sales",parser:"number"},
				{key:"mtd_vs_budget_gp",parser:"number"},
				{key:"mtd_vs_budget_gp_percent",parser:"number"},
                                
                                                                        {key:"cm_proj_vs_budget_sales",parser:"number"},
				{key:"cm_proj_vs_budget_gp",parser:"number"},
				{key:"cm_proj_vs_budget_gp_percent",parser:"number"},		
                                
				{key:"ytd_vs_budget_sales",parser:"number"},
				{key:"ytd_vs_budget_gp",parser:"number"},
				{key:"ytd_vs_budget_gp_percent",parser:"number"},
				
				{key:"projected_vs_budget_sales",parser:"number"},
				{key:"projected_vs_budget_gp",parser:"number"},
				{key:"projected_vs_budget_gp_percent",parser:"number"},
				
				{key:"projected_vs_ly_sales",parser:"number"},
				{key:"projected_vs_ly_gp",parser:"number"},
				{key:"projected_vs_ly_gp_percent",parser:"number"}
			],
			metaFields: {
				totalRecords: "totalRecords" // Access to value in the server response
			}
		};
		 // Create the Paginator 
                                    var myPaginator = new YAHOO.widget.Paginator({ 
                                        rowsPerPage: typeof localStorage.tablePaginator != "undefited" && localStorage.tablePaginator != null ? localStorage.tablePaginator : 100,
                                        containers : ["customerbudgetcomparison-pag-nav"], 
                                        template : "<div class='counter-nav'>{CurrentPageReport} {RowsPerPageDropdown}</div><center style='clear: both;'>{FirstPageLink}{PreviousPageLink}{PageLinks} {NextPageLink}{LastPageLink} </center>", 
                                        pageReportTemplate : "Showing items {startRecord} - {endRecord} of {totalRecords}", 
                                        rowsPerPageOptions : [10,20,30,40,50,100,200,{ value : 5000, text : "All" } ]  
                                    }); 
		//future requets of data
		var myRequestBuilder = function(oState, oSelf) { 
			// Standard stuff:
			oState = oState || {pagination:null, sortedBy:null}; 
                                                      if(oState.pagination && oState.pagination.before){
                                                          localStorage.tablePaginator = oState.pagination.rowsPerPage;
                                                          localStorage.tablePaginatorBefore = oState.pagination.before.rowsPerPage;
                                                      }
			var sort = (oState.sortedBy) ? oState.sortedBy.key : "myDefaultColumnKey"; 
			var dir = (oState.sortedBy && oState.sortedBy.dir === YAHOO.widget.DataTable.CLASS_DESC) ? "desc" : "asc"; 
			var startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0; 
			var results = (oState.pagination) ? oState.pagination.rowsPerPage : 100; 
			
			 
			// Build custom request 
			return  "select=" + selectMethodParm +
					"&slsm=" + slsmParm +
			        "&region=" + regionParm +
			        "&location=" + locationParm +
					"&dealertype=" + dealerTypeParm +
					"&sort=" + sort + 
					"&dir=" + dir + 
					"&startIndex=" + startIndex + 
					"&results=" + results+
                                                                                    "&account=" + account_id; 
		}; 
		
		// DataTable configuration
		var myConfigs = {
			initialRequest: "select=" + selectMethodParm +
							"&slsm=" + slsmParm +
			                "&region=" + regionParm +
			                "&location=" + locationParm + 
							"&dealertype=" + dealerTypeParm +
							"&sort=custname" + 
							"&dir=asc" + 
							"&startIndex=0" + 
							"&results=100"+
                                                                                    "&account=" + account_id, // Initial request for first page of data
			generateRequest: myRequestBuilder,
			dynamicData: true, // Enables dynamic server-driven data
			sortedBy : {key:"custname", dir:YAHOO.widget.DataTable.CLASS_ASC}, // Sets UI initial sort arrow
			paginator: myPaginator ,
                                                    scrollable: "y",
                                                height: "490px",
                                                width:  "100%"
		};
		
		// DataTable instance
		var myDataTable = new YAHOO.widget.DataTable(divNameParm, myColumnDefs, myDataSource, myConfigs);
		myDataTable.getColumn("slsm_acc")._elTh.setAttribute("colSpan", 3);
		// Update totalRecords on the fly with value from server
		myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
			oPayload.totalRecords = oResponse.meta.totalRecords;
			return oPayload;
		}
		
		
			
		/* slsm tooltip */
		var showTimer = 0, hideTimer = 0;
		var tt = new YAHOO.widget.Tooltip("myTooltip");
		myDataTable.on('cellMouseoverEvent', function (oArgs) {
			if (showTimer) {
				window.clearTimeout(showTimer);
				showTimer = 0;
			}

			var target = oArgs.target;
			var column = this.getColumn(target);
			if (column != null && column.key == 'slsm') {
				var record = this.getRecord(target);
				var description = record.getData('slsm') || '??';
				description += ' ';
				description += record.getData('slsmname') || '??? ????';
				/* var xy = [parseInt(oArgs.event.clientX,10) + 10 ,parseInt(oArgs.event.clientY,10) + 10 ]; */
				var xy = YAHOO.util.Event.getXY(oArgs.event);
				showTimer = window.setTimeout(function() {
					tt.setBody(description);
					tt.cfg.setProperty('xy',xy);
					tt.show();
					hideTimer = window.setTimeout(function() {
						tt.hide();
					},5000);
				},500);
			}
		});
		myDataTable.on('cellMouseoutEvent', function (oArgs) {
			if (showTimer) {
				window.clearTimeout(showTimer);
				showTimer = 0;
			}
			if (hideTimer) {
				window.clearTimeout(hideTimer);
				hideTimer = 0;
			}
			tt.hide();
		});

// Shows dialog, creating one when necessary
                                    var newCols = true;
                                    var showDlg = function(e) {
                                        YAHOO.util.Event.stopEvent(e);

                                        if(newCols) {
                                            var cell = document.getElementById("customerbudgetcomparison-dlg-picker");

                                            if ( cell.hasChildNodes() )
                                            {
                                                while ( cell.childNodes.length >= 1 )
                                                {
                                                    cell.removeChild( cell.firstChild );       
                                                } 
                                            }
                                            // Populate Dialog
                                            // Using a template to create elements for the SimpleDialog
                                            var allColumns = myDataTable.getColumnSet().keys;
                                            var headers = myDataTable.getColumnSet().headers;
                                            var elPicker = YAHOO.util.Dom.get("customerbudgetcomparison-dlg-picker");
                                            var elTemplateCol = document.createElement("div");
                                            YAHOO.util.Dom.addClass(elTemplateCol, "dt-dlg-pickercol");
                                            var elTemplateKey = elTemplateCol.appendChild(document.createElement("span"));
                                            YAHOO.util.Dom.addClass(elTemplateKey, "dt-dlg-pickerkey");
                                            var elTemplateBtns = elTemplateCol.appendChild(document.createElement("span"));
                                            YAHOO.util.Dom.addClass(elTemplateBtns, "dt-dlg-pickerbtns");
                                            var onclickObj = {fn:handleButtonClick, obj:this, scope:false };

                                            // Create one section in the SimpleDialog for each Column
                                            var elColumn, elKey, oButtonGrp;
                                            for(var i=0,l=allColumns.length;i<l;i++) {
                                                var oColumn = allColumns[i];
                                                var header  = headers[i][0]; 
                                                var groupname = myDataTable.getColumn(header).label;
                                                // Use the template
                                                elColumn = elTemplateCol.cloneNode(true);

                                                // Write the Column key
                                                elKey = elColumn.firstChild;
                                                elKey.innerHTML = groupname+" "+oColumn.label;

                                                // Create a ButtonGroup
                                                oButtonGrp = new YAHOO.widget.ButtonGroup({ 
                                                    id: "customerbudgetcomparison-buttongrp-"+oColumn.getKey(), 
                                                    name: oColumn.getKey(), 
                                                    container: elKey.nextSibling
                                                });
                                                 var chek = true;
                                                if(oColumn.className == "hiden-col"){
                                                    chek = false;
                                                }
                                                oButtonGrp.addButtons([
                                                    { label: "Show", value: "Show", checked: (chek), onclick: onclickObj},
                                                    { label: "Hide", value: "Hide", checked: (!chek), onclick: onclickObj}
                                                ]);

                                                elPicker.appendChild(elColumn);
                                            }
                                            newCols = false;
                                        }
                                        myDlg.show();
                                    };
                                    var hideDlg = function(e) {
                                        this.hide();
                                    };
                                    var handleButtonClick = function(e, oSelf) {
                                      var sKey = this.get("name");
                                        
                                        var classTd = myDataTable.getColumn(sKey)._elTh.className.split(/\s/);
                                        if(classTd[0] == "hiden-col"){
                                            var cellClass = classTd[1];
                                        }else{
                                            var cellClass = classTd[0];
                                        }
                                        if(this.get("value") === "Hide"){
                                            YAHOO.util.Dom.removeClass(myDataTable.getColumn(sKey)._elTh, "show-col");
                                        }else{
                                             YAHOO.util.Dom.addClass(myDataTable.getColumn(sKey)._elTh, "show-col");
                                        }
                                        var cells =  YAHOO.util.Dom.getElementsByClassName(cellClass);
                                        var mode = this.get("value") === "Hide" ?  'none': 'table-cell';
                                        var showCol = 0;
                                        var isHide = false;
                                        for(k = 0; k < myDataTable.getColumn(sKey)._oParent.children.length; k++){
                                            var hidenCol = YAHOO.util.Dom.hasClass(myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh, 'hiden-col');
                                            showCol = myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh.style.display != 'none' && !hidenCol ? showCol+1 : showCol;
                                            if(myDataTable.getColumn(sKey)._oParent.children[k].key == sKey && this.get("value") === "Hide")
                                                isHide = myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh.style.display == 'none' ? true:false;
                                        }
                                        if(classTd[0] == "hiden-col"){
                                            removeClass(myDataTable.getColumn(sKey)._elTh,"hiden-col");
                                        }
                                        if(!isHide){
                                            var minusplusColspan =  this.get("value") === "Hide" ?  -1: +1;
                                            var nColspan =   showCol + minusplusColspan;
                                            nColspan = nColspan > myDataTable.getColumn(sKey)._oParent.children.length ? myDataTable.getColumn(sKey)._oParent.children.length: nColspan;

                                            if(nColspan == 0){
                                                myDataTable.getColumn(sKey)._oParent._elTh.style.display = mode;   
                                            }else if(nColspan == 1 && showCol == 0){
                                                myDataTable.getColumn(sKey)._oParent._elTh.style.display = mode; 
                                                myDataTable.getColumn(sKey)._oParent._elTh.setAttribute("colSpan", nColspan);
                                            } else {
                                                myDataTable.getColumn(sKey)._oParent._elTh.setAttribute("colSpan", nColspan);
                                            }

                                            //var widthCell = $(cells[0]).width();
                                            for(j = 0; j < cells.length; j++) {
                                                cells[j].style.display = mode;
                                                //if(widthCell != $(cells[j]).children(0).width())
                                                    //$(cells[j]).children(0).width(widthCell);
                                            }
                                        }
                                            var theadEle = myDataTable.getTheadEl(),
                                            thEle = theadEle.getElementsByClassName("yui-dt-last")[0].getElementsByTagName('th');
                                            var lastCol = thEle.length - 1;
                                            var countCol = 1;
                                            for(var i=0; i < thEle.length; i++) {
                                                if(!YAHOO.util.Dom.hasClass(thEle[i], "hiden-col") && thEle[i].style.display != 'none') {
                                                    thEle[i].style.borderRight = "";
                                                    lastCol = i;
                                                    countCol++;
                                                }
                                            }
                                            thEle[lastCol].style.borderRight = countCol+"px solid #F2F2F2";
                                            TopScrollTable(myDataTable,divNameParm);
                                    };

                                    // Create the SimpleDialog
                                    YAHOO.util.Dom.removeClass("customerbudgetcomparison-dlg", "inprogress");
                                     YAHOO.util.Dom.setStyle("customerbudgetcomparison-dlg","display","block");
                                    var myDlg = new YAHOO.widget.SimpleDialog("customerbudgetcomparison-dlg", {
                                        width: "30em",
                                        visible: false,
                                        modal: false,
                                        buttons: [ 
                                            { text:"Close",  handler:hideDlg }
                                        ],
                                        fixedcenter: true,
                                        constrainToViewport: true
                                    });
                                    myDlg.render();

                                    // Nulls out myDlg to force a new one to be created
                                    myDataTable.subscribe("columnReorderEvent", function(){
                                        newCols = true;
                                        YAHOO.util.Event.purgeElement("customerbudgetcomparison-dlg-picker", true);
                                        YAHOO.util.Dom.get("customerbudgetcomparison-dlg-picker").innerHTML = "";
                                    }, this, true);
		myDataTable.subscribe('renderEvent', function()    {
                                        var theadEle = this.getTheadEl(),
                                        thEle = theadEle.getElementsByTagName('th'),
                                        len = thEle.length;
                                        for(var i=0; i < len; i++) {
                                             var classTd =  this.getColumn(thEle[i].id)._elTh.className.split(/\s/);
                                                var cells =  YAHOO.util.Dom.getElementsByClassName(classTd[0]);
                                            if(YAHOO.util.Dom.hasClass(thEle[i], "show-col")) {
                                               
                                                for(j = 0; j < cells.length; j++) cells[j].style.display = 'table-cell';
                                            }
                                        }
                                    });
                                      myDataTable.subscribe('postRenderEvent', function()    {
                                           var divWidth = $("#customerbudgetcomparison").width();
                                       var tableWidth = $("#customerbudgetcomparison .yui-dt-bd table").width();
                                       var borderRight = divWidth - tableWidth;
                                       var paddR_name = 0;
                                        var paddR_last = 0;
                                        var currBrowser = browserDetectNav();
                                        var winWidth =window.outerWidth;
                                        if(winWidth >= 1152 && winWidth < 1280){
                                            paddR_name = currBrowser == "Firefox" ? 0:0;
                                            paddR_last = currBrowser == "Firefox" ? 0:0;
                                        }else if(winWidth >= 1280 && winWidth < 1360){
                                            paddR_name = currBrowser == "Firefox" ? 0:0;
                                            paddR_last = currBrowser == "Firefox" ? 1:1;
                                        }else if(winWidth >= 1360 && winWidth < 1440){
                                            paddR_name = currBrowser == "Firefox" ? 0:1;
                                            paddR_last = currBrowser == "Firefox" ? 1:1;
                                        }else if(winWidth >= 1440 && winWidth < 1600){
                                            paddR_name = currBrowser == "Firefox" ? 0:39;
                                            paddR_last = currBrowser == "Firefox" ? 1:1;
                                        }else if(winWidth >= 1600){
                                            paddR_name = currBrowser == "Firefox" ? 0:0;
                                            paddR_last = currBrowser == "Firefox" ? 3:3;
                                        }
                                        var theadEle = this.getTheadEl(),
                                        thEle = theadEle.getElementsByClassName("yui-dt-last")[0].getElementsByTagName('th');
                                        $("th.yui-dt-col-custname").css('padding-right',paddR_name+'px');
                                        var lastCol = thEle.length - 1;
                                        var countCol = 1;
                                        for(var i=0; i < thEle.length; i++) {
                                            if(!YAHOO.util.Dom.hasClass(thEle[i], "hiden-col")) {
                                                lastCol = i;
                                                countCol++;
                                            }
                                        }
                                          thEle[lastCol].style.borderRight = borderRight+"px solid #F2F2F2";
                                         thEle[lastCol].style.paddingRight =paddR_last+'px';
                                        TopScrollTable(this,divNameParm);
                                    });
                                    YAHOO.util.Event.addListener("customerbudgetcomparison-swhd-link", "click", showDlg, this, true);


		return {
			ds: myDataSource,
			dt: myDataTable
		};

	};
        }else {
            return function(){};
        }
}

</script>


<!-- customer sales comparison -->
<script type="text/javascript">
function createCustomerSalesComparisonDataTable(divNameParm, selectMethodParm, slsmParm, regionParm, locationParm, dealerTypeParm,account_id) {
    if(divNameParm == 'customersalescomparison'){
                return function() {
		if(selectMethodParm != 'u') {
			selectMethodParm = 'i'; /* intersection */
		}

		this.custlink = function(elCell, oRecord, oColumn, oData) {
			elCell.innerHTML = "<a href=\"" + "<? echo $baseurl."/index.php?module=Accounts&action=DetailView&record="; ?>" + oRecord.getData('id') + "\" target=\"_blank\">" + oData + "</a>";
		};
		this.precent= function(elCell, oRecord, oColumn, oData) {
                                            if (oData < 0) {
                                               elCell.innerHTML = '<span style="color: red">('+oData.toFixed(2)*(-1)+'%)</span>';
                                            }else if(oData != null){
                                                elCell.innerHTML = '<span>'+oData.toFixed(2)+'%</span>';
                                            }else if(oData == null){
                                                oData = 0;
                                                elCell.innerHTML = '<span>'+oData.toFixed(2)+'%</span>';
                                            }
		};
                                     this.currencyRed= function(elCell, oRecord, oColumn, oData) {
                                                if(oData != null) {
                                                    var oFormatConfig = {
					prefix: "$",
					decimalPlaces: 0,
					decimalSeparator: ".",
					thousandsSeparator: ",",
					suffix: ""
				};
			if (oData < 0) {
                                                             elCell.innerHTML= '<span style="color: red">('+YAHOO.util.Number.format(oData*(-1), oFormatConfig)+')</span>';
			} else {
                                                             elCell.innerHTML = YAHOO.util.Number.format(oData, oFormatConfig);
			}
                                            }
		};
		// Column definitions
		var myColumnDefs = [ // sortable:true enables sorting
		    {key:"slsm_acc",label:"", children: [
					{key:"slsm", label:"Slsm", sortable:true},
					{key:"custno", label:"CustNo", sortable:true},
					{key:"custname", label:"Name", sortable:true, formatter:this.custlink},
                                        {key:"shipping_address_street", label:"Address", sortable:true, className: "hiden-col"},
			{key:"shipping_address_city", label:"City", sortable:true, className: "hiden-col"},
                                                      {key:"shipping_address_state", label:"State", sortable:true, className: "hiden-col"},
			{key:"shipping_address_postalcode", label:"Zip", sortable:true, className: "hiden-col"},
                                                      {key:"contact", label:"Contact", sortable:true, className: "hiden-col"},
                                                      {key:"phone", label:"Phone", sortable:true, className: "hiden-col"},
				]
		    },

                                                    {key:"mtd_projected_vs_lm",label:"CM Proj vs. LM",  children: [
					{key:"mtd_projected_vs_lm_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "sales-width"},
					{key:"mtd_projected_vs_lm_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "gp-width"},
					{key:"mtd_projected_vs_lm_gp_percent", label:"GP%", sortable:true, formatter:this.precent, className: "gpp-width"},
				]
			},
                                                    {key:"mtd_projected_vs_lytm",label:"CM Proj  vs. LYTM",  children: [
					{key:"mtd_projected_vs_lytm_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "sales-width"},
					{key:"mtd_projected_vs_lytm_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "gp-width"},
					{key:"mtd_projected_vs_lytm_gp_percent", label:"GP%", sortable:true, formatter:this.precent, className: "gpp-width"},
				]
			},
			{key:"mtd_lm",label:"MTD vs. LM", className: "hiden-col", children: [
					{key:"mtd_vs_lm_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "hiden-col"},
					{key:"mtd_vs_lm_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "hiden-col"},
					{key:"mtd_vs_lm_gp_percent", label:"GP%", sortable:true, formatter:this.precent, className: "hiden-col"},
				]
			},

			{key:"mtd_lmtm",label:"MTD vs. LYTM", className: "hiden-col", children: [
					{key:"mtd_vs_lytm_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "hiden-col"},
					{key:"mtd_vs_lytm_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "hiden-col"},
					{key:"mtd_vs_lytm_gp_percent", label:"GP%", sortable:true, formatter:this.precent, className: "hiden-col"},
				]
			},

			{key:"ytd_lytd",label:"YTD vs. LYTD", children: [
					{key:"ytd_vs_lytd_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "sales-width"},
					{key:"ytd_vs_lytd_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "gp-width"},
					{key:"ytd_vs_lytd_gp_percent", label:"GP%", sortable:true, formatter:this.precent, className: "gpp-width"},
				]
			},

			{key:"mproj_ly",label:"CY Projection vs. LY", children: [	
					{key:"projected_vs_ly_sales", label:"Sales", sortable:true, formatter:this.currencyRed, className: "sales-width"},
					{key:"projected_vs_ly_gp", label:"GP", sortable:true, formatter:this.currencyRed, className: "gp-width"},
					{key:"projected_vs_ly_gp_percent", label:"GP%", sortable:true, formatter:this.precent, className: "gpp-width"}
				]
			}
		];
                

		// Custom parser
		var stringToDate = function(sData) {
			var array = sData.split("-");
			return new Date(array[1] + " " + array[0] + ", " + array[2]);
		};
		
		// DataSource instance
		var myDataSource = new YAHOO.util.DataSource("json_proxy_customer_sales_comparison.php?");
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
		myDataSource.responseSchema = {
			resultsList: "records",
			fields: [
				{key:"id"},
				{key:"slsm"},
				{key:"slsmname"},
				{key:"custno"},
				{key:"custname"},
                                 {key:"shipping_address_street"},
                                                                        {key:"shipping_address_city"},
                                                                        {key:"shipping_address_state"},
                                                                        {key:"shipping_address_postalcode"},
                                                                        {key:"contact"},
                                                                        {key:"phone"},
                                                                        
				{key:"mtd_projected_vs_lm_sales",parser:"number"},
                                                                        {key:"mtd_projected_vs_lm_gp",parser:"number"},
                                                                        {key:"mtd_projected_vs_lm_gp_percent",parser:"number"},
                                                                        
                                                                        {key:"mtd_projected_vs_lytm_sales",parser:"number"},
                                                                        {key:"mtd_projected_vs_lytm_gp",parser:"number"},
                                                                        {key:"mtd_projected_vs_lytm_gp_percent",parser:"number"},
                                
				{key:"mtd_vs_lm_sales",parser:"number"},
				{key:"mtd_vs_lm_gp",parser:"number"},
				{key:"mtd_vs_lm_gp_percent",parser:"text"},
				
				{key:"mtd_vs_lm_sales",parser:"number"},
				{key:"mtd_vs_lm_gp",parser:"number"},
				{key:"mtd_vs_lm_gp_percent",parser:"number"},
				
				{key:"mtd_vs_lytm_sales",parser:"number"},
				{key:"mtd_vs_lytm_gp",parser:"number"},
				{key:"mtd_vs_lytm_gp_percent",parser:"number"},
	
				{key:"ytd_vs_lytd_sales",parser:"number"},
				{key:"ytd_vs_lytd_gp",parser:"number"},
				{key:"ytd_vs_lytd_gp_percent",parser:"number"},
	
				{key:"projected_vs_ly_sales",parser:"number"},
				{key:"projected_vs_ly_gp",parser:"number"},
				{key:"projected_vs_ly_gp_percent",parser:"number"}
			],
			metaFields: {
				totalRecords: "totalRecords" // Access to value in the server response
			}
		};
		 // Create the Paginator 
                                    var myPaginator = new YAHOO.widget.Paginator({ 
                                        rowsPerPage: typeof localStorage.tablePaginator != "undefited" && localStorage.tablePaginator != null ? localStorage.tablePaginator : 100,
                                        containers : ["customersalescomparison-pag-nav"], 
                                        template : "<div class='counter-nav'>{CurrentPageReport} {RowsPerPageDropdown}</div><center style='clear: both;'>{FirstPageLink}{PreviousPageLink}{PageLinks} {NextPageLink}{LastPageLink} </center>", 
                                        pageReportTemplate : "Showing items {startRecord} - {endRecord} of {totalRecords}", 
                                        rowsPerPageOptions : [10,20,30,40,50,100,200,{ value : 5000, text : "All" } ]  
                                    }); 
		//future requets of data
		var myRequestBuilder = function(oState, oSelf) { 
			// Standard stuff:
			oState = oState || {pagination:null, sortedBy:null}; 
                                                      if(oState.pagination && oState.pagination.before){
                                                          localStorage.tablePaginator = oState.pagination.rowsPerPage;
                                                          localStorage.tablePaginatorBefore = oState.pagination.before.rowsPerPage;
                                                      }
			var sort = (oState.sortedBy) ? oState.sortedBy.key : "myDefaultColumnKey"; 
			var dir = (oState.sortedBy && oState.sortedBy.dir === YAHOO.widget.DataTable.CLASS_DESC) ? "desc" : "asc"; 
			var startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0; 
			var results = (oState.pagination) ? oState.pagination.rowsPerPage : 100; 
			
			 
			// Build custom request 
			return  "select=" + selectMethodParm +
					"&slsm=" + slsmParm +
			        "&region=" + regionParm +
			        "&location=" + locationParm +
					"&dealertype=" + dealerTypeParm +
					"&sort=" + sort + 
					"&dir=" + dir + 
					"&startIndex=" + startIndex + 
					"&results=" + results+
                                                                                    "&account=" + account_id; 
		}; 
		
		// DataTable configuration
		var myConfigs = {
			initialRequest: "select=" + selectMethodParm +
							"&slsm=" + slsmParm +
			                "&region=" + regionParm +
			                "&location=" + locationParm + 
							"&dealertype=" + dealerTypeParm +
							"&sort=custname" + 
							"&dir=asc" + 
							"&startIndex=0" + 
							"&results=100"+
                                                                                    "&account=" + account_id, // Initial request for first page of data
			generateRequest: myRequestBuilder,
			dynamicData: true, // Enables dynamic server-driven data
			sortedBy : {key:"custname", dir:YAHOO.widget.DataTable.CLASS_ASC}, // Sets UI initial sort arrow
			paginator: myPaginator,
                                                    scrollable: "y",
                                                height: "490px",
                                                width:  "100%"
		};
		
		// DataTable instance
		var myDataTable = new YAHOO.widget.DataTable(divNameParm, myColumnDefs, myDataSource, myConfigs);
		myDataTable.getColumn("slsm_acc")._elTh.setAttribute("colSpan", 3);
		myDataTable.getColumn("mtd_lm")._elTh.setAttribute("colSpan", 0);
                                    myDataTable.getColumn("mtd_lmtm")._elTh.setAttribute("colSpan", 0);
                                    // Update totalRecords on the fly with value from server
		myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
			oPayload.totalRecords = oResponse.meta.totalRecords;
			return oPayload;
		}
		
		
			
		/* slsm tooltip */
		var showTimer = 0, hideTimer = 0;
		var tt = new YAHOO.widget.Tooltip("myTooltip");
		myDataTable.on('cellMouseoverEvent', function (oArgs) {
			if (showTimer) {
				window.clearTimeout(showTimer);
				showTimer = 0;
			}

			var target = oArgs.target;
			var column = this.getColumn(target);
			if (column != null && column.key == 'slsm') {
				var record = this.getRecord(target);
				var description = record.getData('slsm') || '??';
				description += ' ';
				description += record.getData('slsmname') || '??? ????';
				/* var xy = [parseInt(oArgs.event.clientX,10) + 10 ,parseInt(oArgs.event.clientY,10) + 10 ]; */
				var xy = YAHOO.util.Event.getXY(oArgs.event);
				showTimer = window.setTimeout(function() {
					tt.setBody(description);
					tt.cfg.setProperty('xy',xy);
					tt.show();
					hideTimer = window.setTimeout(function() {
						tt.hide();
					},5000);
				},500);
			}
		});
		myDataTable.on('cellMouseoutEvent', function (oArgs) {
			if (showTimer) {
				window.clearTimeout(showTimer);
				showTimer = 0;
			}
			if (hideTimer) {
				window.clearTimeout(hideTimer);
				hideTimer = 0;
			}
			tt.hide();
		});

// Shows dialog, creating one when necessary
                                    var newCols = true;
                                    var showDlg = function(e) {
                                        YAHOO.util.Event.stopEvent(e);

                                        if(newCols) {
                                            var cell = document.getElementById("customersalescomparison-dlg-picker");

                                            if ( cell.hasChildNodes() )
                                            {
                                                while ( cell.childNodes.length >= 1 )
                                                {
                                                    cell.removeChild( cell.firstChild );       
                                                } 
                                            }
                                            // Populate Dialog
                                            // Using a template to create elements for the SimpleDialog
                                            var allColumns = myDataTable.getColumnSet().keys;
                                            var headers = myDataTable.getColumnSet().headers;
                                            var elPicker = YAHOO.util.Dom.get("customersalescomparison-dlg-picker");
                                            var elTemplateCol = document.createElement("div");
                                            YAHOO.util.Dom.addClass(elTemplateCol, "dt-dlg-pickercol");
                                            var elTemplateKey = elTemplateCol.appendChild(document.createElement("span"));
                                            YAHOO.util.Dom.addClass(elTemplateKey, "dt-dlg-pickerkey");
                                            var elTemplateBtns = elTemplateCol.appendChild(document.createElement("span"));
                                            YAHOO.util.Dom.addClass(elTemplateBtns, "dt-dlg-pickerbtns");
                                            var onclickObj = {fn:handleButtonClick, obj:this, scope:false };

                                            // Create one section in the SimpleDialog for each Column
                                            var elColumn, elKey, oButtonGrp;
                                            for(var i=0,l=allColumns.length;i<l;i++) {
                                                var oColumn = allColumns[i];
                                                var header  = headers[i][0]; 
                                                var groupname = myDataTable.getColumn(header).label;
                                                // Use the template
                                                elColumn = elTemplateCol.cloneNode(true);

                                                // Write the Column key
                                                elKey = elColumn.firstChild;
                                                elKey.innerHTML = groupname+" "+oColumn.label;

                                                // Create a ButtonGroup
                                                oButtonGrp = new YAHOO.widget.ButtonGroup({ 
                                                    id: "customersalescomparison-buttongrp-"+oColumn.getKey(), 
                                                    name: oColumn.getKey(), 
                                                    container: elKey.nextSibling
                                                });
                                                var chek = true;
                                                if(oColumn.className == "hiden-col"){
                                                    chek = false;
                                                }
                                                oButtonGrp.addButtons([
                                                    { label: "Show", value: "Show", checked: (chek), onclick: onclickObj},
                                                    { label: "Hide", value: "Hide", checked: (!chek), onclick: onclickObj}
                                                ]);

                                                elPicker.appendChild(elColumn);
                                            }
                                            newCols = false;
                                        }
                                        myDlg.show();
                                    };
                                    var hideDlg = function(e) {
                                        this.hide();
                                    };
                                    var handleButtonClick = function(e, oSelf) {
                                       var sKey = this.get("name");
                                        
                                        var classTd = myDataTable.getColumn(sKey)._elTh.className.split(/\s/);
                                        if(classTd[0] == "hiden-col"){
                                            var cellClass = classTd[1];
                                        }else{
                                            var cellClass = classTd[0];
                                        }
                                        if(this.get("value") === "Hide"){
                                            YAHOO.util.Dom.removeClass(myDataTable.getColumn(sKey)._elTh, "show-col");
                                        }else{
                                             YAHOO.util.Dom.addClass(myDataTable.getColumn(sKey)._elTh, "show-col");
                                        }
                                        var cells =  YAHOO.util.Dom.getElementsByClassName(cellClass);
                                        var mode = this.get("value") === "Hide" ?  'none': 'table-cell';
                                        var showCol = 0;
                                        var isHide = false;
                                        for(k = 0; k < myDataTable.getColumn(sKey)._oParent.children.length; k++){
                                            var hidenCol = YAHOO.util.Dom.hasClass(myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh, 'hiden-col');
                                            showCol = myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh.style.display != 'none' && !hidenCol ? showCol+1 : showCol;
                                            if(myDataTable.getColumn(sKey)._oParent.children[k].key == sKey && this.get("value") === "Hide")
                                                isHide = myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh.style.display == 'none' ? true:false;
                                        }
                                        if(classTd[0] == "hiden-col"){
                                            removeClass(myDataTable.getColumn(sKey)._elTh,"hiden-col");
                                        }
                                        if(!isHide){
                                            var minusplusColspan =  this.get("value") === "Hide" ?  -1: +1;
                                            var nColspan =   showCol + minusplusColspan;
                                            nColspan = nColspan > myDataTable.getColumn(sKey)._oParent.children.length ? myDataTable.getColumn(sKey)._oParent.children.length: nColspan;

                                            if(nColspan == 0){
                                                myDataTable.getColumn(sKey)._oParent._elTh.style.display = mode;   
                                            }else if(nColspan == 1 && showCol == 0){
                                                myDataTable.getColumn(sKey)._oParent._elTh.style.display = mode; 
                                                myDataTable.getColumn(sKey)._oParent._elTh.setAttribute("colSpan", nColspan);
                                            } else {
                                                myDataTable.getColumn(sKey)._oParent._elTh.setAttribute("colSpan", nColspan);
                                            }

                                            //var widthCell = $(cells[0]).width();
                                            for(j = 0; j < cells.length; j++) {
                                                cells[j].style.display = mode;
                                                //if(widthCell != $(cells[j]).children(0).width())
                                                   // $(cells[j]).children(0).width(widthCell);
                                            }
                                        }  
                                            var theadEle = myDataTable.getTheadEl(),
                                            thEle = theadEle.getElementsByClassName("yui-dt-last")[0].getElementsByTagName('th');
                                            var lastCol = thEle.length - 1;
                                            var countCol = 1;
                                            for(var i=0; i < thEle.length; i++) {
                                                if(!YAHOO.util.Dom.hasClass(thEle[i], "hiden-col") && thEle[i].style.display != 'none') {
                                                    thEle[i].style.borderRight = "";
                                                    lastCol = i;
                                                    countCol++;
                                                }
                                            }
                                            thEle[lastCol].style.borderRight = countCol+"px solid #F2F2F2";
                                            TopScrollTable(myDataTable,divNameParm);
                                    };

                                    // Create the SimpleDialog
                                    YAHOO.util.Dom.removeClass("customersalescomparison-dlg", "inprogress");
                                     YAHOO.util.Dom.setStyle("customersalescomparison-dlg","display","block");
                                    var myDlg = new YAHOO.widget.SimpleDialog("customersalescomparison-dlg", {
                                        width: "30em",
                                        visible: false,
                                        modal: false,
                                        buttons: [ 
                                            { text:"Close",  handler:hideDlg }
                                        ],
                                        fixedcenter: true,
                                        constrainToViewport: true
                                    });
                                    myDlg.render();

                                    // Nulls out myDlg to force a new one to be created
                                    myDataTable.subscribe("columnReorderEvent", function(){
                                        newCols = true;
                                        YAHOO.util.Event.purgeElement("customersalescomparison-dlg-picker", true);
                                        YAHOO.util.Dom.get("customersalescomparison-dlg-picker").innerHTML = "";
                                    }, this, true);
		myDataTable.subscribe('renderEvent', function()    {
                                        var theadEle = this.getTheadEl(),
                                        thEle = theadEle.getElementsByTagName('th'),
                                        len = thEle.length;
                                        for(var i=0; i < len; i++) {
                                             var classTd =  this.getColumn(thEle[i].id)._elTh.className.split(/\s/);
                                                var cells =  YAHOO.util.Dom.getElementsByClassName(classTd[0]);
                                            if(YAHOO.util.Dom.hasClass(thEle[i], "show-col")) {
                                               
                                                for(j = 0; j < cells.length; j++) cells[j].style.display = 'table-cell';
                                            }
                                        }
                                    });
                                      myDataTable.subscribe('postRenderEvent', function()    {
                                           var divWidth = $("#customersalescomparison").width();
                                       var tableWidth = $("#customersalescomparison .yui-dt-bd table").width();
                                       var borderRight = divWidth - tableWidth;
                                       var paddR_name = 0;
                                        var paddR_last = 0;
                                        var currBrowser = browserDetectNav();
                                        var winWidth =window.outerWidth;
                                        if(winWidth >= 1152 && winWidth < 1280){
                                            paddR_name = currBrowser == "Firefox" ? 0:0;
                                            paddR_last = currBrowser == "Firefox" ? 0:0;
                                        }else if(winWidth >= 1280 && winWidth < 1360){
                                            paddR_name = currBrowser == "Firefox" ? 0:0;
                                            paddR_last = currBrowser == "Firefox" ? 1:1;
                                        }else if(winWidth >= 1360 && winWidth < 1440){
                                            paddR_name = currBrowser == "Firefox" ? 0:1;
                                            paddR_last = currBrowser == "Firefox" ? 1:1;
                                        }else if(winWidth >= 1440 && winWidth < 1600){
                                            paddR_name = currBrowser == "Firefox" ? 0:39;
                                            paddR_last = currBrowser == "Firefox" ? 1:1;
                                        }else if(winWidth >= 1600){
                                            paddR_name = currBrowser == "Firefox" ? 0:0;
                                            paddR_last = currBrowser == "Firefox" ? 3:3;
                                        }
                                        var theadEle = this.getTheadEl(),
                                        thEle = theadEle.getElementsByClassName("yui-dt-last")[0].getElementsByTagName('th');
                                        $("th.yui-dt-col-custname").css('padding-right',paddR_name+'px');
                                        var lastCol = thEle.length - 1;
                                        var countCol = 1;
                                        for(var i=0; i < thEle.length; i++) {
                                            if(!YAHOO.util.Dom.hasClass(thEle[i], "hiden-col")) {
                                                lastCol = i;
                                                countCol++;
                                            }
                                        }
                                          thEle[lastCol].style.borderRight = borderRight+"px solid #F2F2F2";
                                         thEle[lastCol].style.paddingRight =paddR_last+'px';
                                        TopScrollTable(this,divNameParm);
                                    });
                                    YAHOO.util.Event.addListener("customersalescomparison-swhd-link", "click", showDlg, this, true);

		return {
			ds: myDataSource,
			dt: myDataTable
		};

	};
        }else {
            return function(){};
        }
}
</script>

<!-- customer sales comparison -->
<script type="text/javascript">
function createCustomerReturnsDataTable(divNameParm, selectMethodParm, slsmParm, regionParm, locationParm, dealerTypeParm,account_id) {
    if(divNameParm == 'customerreturns'){
                return function() {
		if(selectMethodParm != 'u') {
			selectMethodParm = 'i'; /* intersection */
		}

		this.custlink = function(elCell, oRecord, oColumn, oData) {
			elCell.innerHTML = "<a href=\"" + "<? echo $baseurl."/index.php?module=Accounts&action=DetailView&record="; ?>" + oRecord.getData('id') + "\" target=\"_blank\">" + oData + "</a>";
		};
		
		// Column definitions
		var myColumnDefs = [ // sortable:true enables sorting
		    {key:"slsm_acc",label:"", children: [
					{key:"slsm", label:"Slsm", sortable:true},
					{key:"custno", label:"CustNo", sortable:true},
					{key:"custname", label:"Name", sortable:true, formatter:this.custlink},
                                        {key:"shipping_address_street", label:"Address", sortable:true, className: "hiden-col"},
			{key:"shipping_address_city", label:"City", sortable:true, className: "hiden-col"},
                                                      {key:"shipping_address_state", label:"State", sortable:true, className: "hiden-col"},
			{key:"shipping_address_postalcode", label:"Zip", sortable:true, className: "hiden-col"},
                                                      {key:"contact", label:"Contact", sortable:true, className: "hiden-col"},
                                                      {key:"phone", label:"Phone", sortable:true, className: "hiden-col"},
				]
		    },

                                                    {key:"rtn_new",label:"New Returns",  children: [
                                                                        {key:"rtn_new_mtd",label:"MTD", sortable:true, formatter:"number", className: "number-width"},
                                                                        {key:"rtn_new_mtd_precent",label:"MTD %", sortable:true, formatter:"number", className: "number-width"},
                                                                        {key:"rtn_new_ytd",label:"YTD", sortable:true, formatter:"number", className: "number-width"},
                                                                        {key:"rtn_new_ytd_precent",label:"YTD %", sortable:true, formatter:"number", className: "number-width"},
                                                                        {key:"rtn_new_lymtd",label:"LYMTD", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_new_lymtd_precent",label:"LYMTD %", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_new_lyytd",label:"LYYTD", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_new_lyytd_precent",label:"LYYTD %", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_new_lm",label:"LM", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_new_lm_precent",label:"LM %", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_new_lylm",label:"LYLM", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_new_lylm_precent",label:"LYLM %", sortable:true, formatter:"number",  className: "hiden-col"}
                                                            ]
			},
                                                    {key:"rtn_def",label:"Defective Returns",  children: [
                                                                        {key:"rtn_def_mtd",label:"MTD", sortable:true, formatter:"number", className: "number-width"},
                                                                        {key:"rtn_def_mtd_precent",label:"MTD %", sortable:true, formatter:"number", className: "number-width"},
                                                                        {key:"rtn_def_ytd",label:"YTD", sortable:true, formatter:"number", className: "number-width"},
                                                                        {key:"rtn_def_ytd_precent",label:"YTD %", sortable:true, formatter:"number", className: "number-width"},
                                                                        {key:"rtn_def_lymtd",label:"LYMTD", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_def_lymtd_precent",label:"LYMTD %", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_def_lyytd",label:"LYYTD", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_def_lyytd_precent",label:"LYYTD %", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_def_lm",label:"LM", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_def_lm_precent",label:"LM %", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_def_lylm",label:"LYLM", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_def_lylm_precent",label:"LYLM %", sortable:true, formatter:"number",  className: "hiden-col"}
                                                            ]
			},
                                                    {key:"rtn_cor",label:"Core Returns",  children: [
                                                                        {key:"rtn_cor_mtd",label:"MTD", sortable:true, formatter:"number", className: "number-width"},
                                                                        {key:"rtn_cor_mtd_precent",label:"MTD %", sortable:true, formatter:"number", className: "number-width"},
                                                                        {key:"rtn_cor_ytd",label:"YTD", sortable:true, formatter:"number", className: "number-width"},
                                                                        {key:"rtn_cor_ytd_precent",label:"YTD %", sortable:true, formatter:"number", className: "number-width"},
                                                                        {key:"rtn_cor_lymtd",label:"LYMTD", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_cor_lymtd_precent",label:"LYMTD %", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_cor_lyytd",label:"LYYTD", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_cor_lyytd_precent",label:"LYYTD %", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_cor_lm",label:"LM", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_cor_lm_precent",label:"LM %", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_cor_lylm",label:"LYLM", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_cor_lylm_precent",label:"LYLM %", sortable:true, formatter:"number",  className: "hiden-col"}
                                                            ]
			},
                                                    {key:"rtn_overall",label:"Overall Returns",  children: [
                                                                        {key:"rtn_mtd_overall",label:"MTD", sortable:true, formatter:"number", className: "number-width"},
                                                                        {key:"rtn_ytd_overall",label:"YTD", sortable:true, formatter:"number", className: "number-width"},
                                                                        {key:"rtn_lymtd_overall",label:"LYMTD", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_lyytd_overall",label:"LYYTD", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_lm_overall",label:"LM", sortable:true, formatter:"number",  className: "hiden-col"},
                                                                        {key:"rtn_lylm_overall",label:"LYLM", sortable:true, formatter:"number",  className: "hiden-col"}
                                                            ]
			}
		];
                

		// Custom parser
		var stringToDate = function(sData) {
			var array = sData.split("-");
			return new Date(array[1] + " " + array[0] + ", " + array[2]);
		};
		
		// DataSource instance
		var myDataSource = new YAHOO.util.DataSource("json_proxy_customer_returns.php?");
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
		myDataSource.responseSchema = {
			resultsList: "records",
			fields: [
				{key:"id"},
				{key:"slsm"},
				{key:"slsmname"},
				{key:"custno"},
				{key:"custname"},
                                                                        {key:"shipping_address_street"},
                                                                        {key:"shipping_address_city"},
                                                                        {key:"shipping_address_state"},
                                                                        {key:"shipping_address_postalcode"},
                                                                        {key:"contact"},
                                                                        {key:"phone"},
                                                                             
				{key:"rtn_new_mtd",parser:"number"},
                                                                        {key:"rtn_new_mtd_precent",parser:"number"},
                                                                        {key:"rtn_new_ytd",parser:"number"},
                                                                        {key:"rtn_new_ytd_precent",parser:"number"},
                                                                        {key:"rtn_new_lymtd",parser:"number"},
                                                                        {key:"rtn_new_lymtd_precent",parser:"number"},
                                                                        {key:"rtn_new_lyytd",parser:"number"},
                                                                        {key:"rtn_new_lyytd_precent",parser:"number"},
                                                                        {key:"rtn_new_lm",parser:"number"},
                                                                        {key:"rtn_new_lm_precent",parser:"number"},
                                                                        {key:"rtn_new_lylm",parser:"number"},
                                                                        {key:"rtn_new_lylm_precent",parser:"number"},
                                                                        
                                                                        {key:"rtn_def_mtd",parser:"number"},
                                                                        {key:"rtn_def_mtd_precent",parser:"number"},
                                                                        {key:"rtn_def_ytd",parser:"number"},
                                                                        {key:"rtn_def_ytd_precent",parser:"number"},
                                                                        {key:"rtn_def_lymtd",parser:"number"},
                                                                        {key:"rtn_def_lymtd_precent",parser:"number"},
                                                                        {key:"rtn_def_lyytd",parser:"number"},
                                                                        {key:"rtn_def_lyytd_precent",parser:"number"},
                                                                        {key:"rtn_def_lm",parser:"number"},
                                                                        {key:"rtn_def_lm_precent",parser:"number"},
                                                                        {key:"rtn_def_lylm",parser:"number"},
                                                                        {key:"rtn_def_lylm_precent",parser:"number"},
                                                                        
                                                                        {key:"rtn_cor_mtd",parser:"number"},
                                                                        {key:"rtn_cor_mtd_precent",parser:"number"},
                                                                        {key:"rtn_cor_ytd",parser:"number"},
                                                                        {key:"rtn_cor_ytd_precent",parser:"number"},
                                                                        {key:"rtn_cor_lymtd",parser:"number"},
                                                                        {key:"rtn_cor_lymtd_precent",parser:"number"},
                                                                        {key:"rtn_cor_lyytd",parser:"number"},
                                                                        {key:"rtn_cor_lyytd_precent",parser:"number"},
                                                                        {key:"rtn_cor_lm",parser:"number"},
                                                                        {key:"rtn_cor_lm_precent",parser:"number"},
                                                                        {key:"rtn_cor_lylm",parser:"number"},
                                                                        {key:"rtn_cor_lylm_precent",parser:"number"},
                                                                        
                                                                        {key:"rtn_mtd_overall",parser:"number"},
                                                                        {key:"rtn_ytd_overall",parser:"number"},
                                                                        {key:"rtn_lymtd_overall",parser:"number"},
                                                                        {key:"rtn_lyytd_overall",parser:"number"},
                                                                        {key:"rtn_lm_overall",parser:"number"},
                                                                        {key:"rtn_lylm_overall",parser:"number"}
                                
			],
			metaFields: {
				totalRecords: "totalRecords" // Access to value in the server response
			}
		};
		 // Create the Paginator 
                                    var myPaginator = new YAHOO.widget.Paginator({ 
                                        rowsPerPage: typeof localStorage.tablePaginator != "undefited" && localStorage.tablePaginator != null ? localStorage.tablePaginator : 100,
                                        containers : ["customerreturns-pag-nav"], 
                                        template : "<div class='counter-nav'>{CurrentPageReport} {RowsPerPageDropdown}</div><center style='clear: both;'>{FirstPageLink}{PreviousPageLink}{PageLinks} {NextPageLink}{LastPageLink} </center>", 
                                        pageReportTemplate : "Showing items {startRecord} - {endRecord} of {totalRecords}", 
                                        rowsPerPageOptions : [10,20,30,40,50,100,200,{ value : 5000, text : "All" } ]  
                                    }); 
		//future requets of data
		var myRequestBuilder = function(oState, oSelf) { 
			// Standard stuff:
			oState = oState || {pagination:null, sortedBy:null}; 
                                                      if(oState.pagination && oState.pagination.before){
                                                          localStorage.tablePaginator = oState.pagination.rowsPerPage;
                                                          localStorage.tablePaginatorBefore = oState.pagination.before.rowsPerPage;
                                                      }
			var sort = (oState.sortedBy) ? oState.sortedBy.key : "myDefaultColumnKey"; 
			var dir = (oState.sortedBy && oState.sortedBy.dir === YAHOO.widget.DataTable.CLASS_DESC) ? "desc" : "asc"; 
			var startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0; 
			var results = (oState.pagination) ? oState.pagination.rowsPerPage : 100; 
			
			 
			// Build custom request 
			return  "select=" + selectMethodParm +
					"&slsm=" + slsmParm +
			        "&region=" + regionParm +
			        "&location=" + locationParm +
					"&dealertype=" + dealerTypeParm +
					"&sort=" + sort + 
					"&dir=" + dir + 
					"&startIndex=" + startIndex + 
					"&results=" + results+
                                                                                    "&account=" + account_id; 
		}; 
		
		// DataTable configuration
		var myConfigs = {
			initialRequest: "select=" + selectMethodParm +
							"&slsm=" + slsmParm +
			                "&region=" + regionParm +
			                "&location=" + locationParm + 
							"&dealertype=" + dealerTypeParm +
							"&sort=custname" + 
							"&dir=asc" + 
							"&startIndex=0" + 
							"&results=100"+
                                                                                    "&account=" + account_id, // Initial request for first page of data
			generateRequest: myRequestBuilder,
			dynamicData: true, // Enables dynamic server-driven data
			sortedBy : {key:"custname", dir:YAHOO.widget.DataTable.CLASS_ASC}, // Sets UI initial sort arrow
			paginator: myPaginator,
                                                    scrollable: "y",
                                                height: "490px",
                                                width:  "100%"
		};
		
		// DataTable instance
		var myDataTable = new YAHOO.widget.DataTable(divNameParm, myColumnDefs, myDataSource, myConfigs);
		myDataTable.getColumn("slsm_acc")._elTh.setAttribute("colSpan", 3);
                                    myDataTable.getColumn("rtn_new")._elTh.setAttribute("colSpan", 4);
		myDataTable.getColumn("rtn_def")._elTh.setAttribute("colSpan", 4);
                                    myDataTable.getColumn("rtn_cor")._elTh.setAttribute("colSpan", 4);
                                    myDataTable.getColumn("rtn_overall")._elTh.setAttribute("colSpan", 2);
                                    // Update totalRecords on the fly with value from server
		myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
			oPayload.totalRecords = oResponse.meta.totalRecords;
			return oPayload;
		}
		
		
			
		/* slsm tooltip */
		var showTimer = 0, hideTimer = 0;
		var tt = new YAHOO.widget.Tooltip("myTooltip");
		myDataTable.on('cellMouseoverEvent', function (oArgs) {
			if (showTimer) {
				window.clearTimeout(showTimer);
				showTimer = 0;
			}

			var target = oArgs.target;
			var column = this.getColumn(target);
			if (column != null && column.key == 'slsm') {
				var record = this.getRecord(target);
				var description = record.getData('slsm') || '??';
				description += ' ';
				description += record.getData('slsmname') || '??? ????';
				/* var xy = [parseInt(oArgs.event.clientX,10) + 10 ,parseInt(oArgs.event.clientY,10) + 10 ]; */
				var xy = YAHOO.util.Event.getXY(oArgs.event);
				showTimer = window.setTimeout(function() {
					tt.setBody(description);
					tt.cfg.setProperty('xy',xy);
					tt.show();
					hideTimer = window.setTimeout(function() {
						tt.hide();
					},5000);
				},500);
			}
		});
		myDataTable.on('cellMouseoutEvent', function (oArgs) {
			if (showTimer) {
				window.clearTimeout(showTimer);
				showTimer = 0;
			}
			if (hideTimer) {
				window.clearTimeout(hideTimer);
				hideTimer = 0;
			}
			tt.hide();
		});

// Shows dialog, creating one when necessary
                                    var newCols = true;
                                    var showDlg = function(e) {
                                        YAHOO.util.Event.stopEvent(e);

                                        if(newCols) {
                                            var cell = document.getElementById("customerreturns-dlg-picker");

                                            if ( cell.hasChildNodes() )
                                            {
                                                while ( cell.childNodes.length >= 1 )
                                                {
                                                    cell.removeChild( cell.firstChild );       
                                                } 
                                            }
                                            // Populate Dialog
                                            // Using a template to create elements for the SimpleDialog
                                            var allColumns = myDataTable.getColumnSet().keys;
                                            var headers = myDataTable.getColumnSet().headers;
                                            var elPicker = YAHOO.util.Dom.get("customerreturns-dlg-picker");
                                            var elTemplateCol = document.createElement("div");
                                            YAHOO.util.Dom.addClass(elTemplateCol, "dt-dlg-pickercol");
                                            var elTemplateKey = elTemplateCol.appendChild(document.createElement("span"));
                                            YAHOO.util.Dom.addClass(elTemplateKey, "dt-dlg-pickerkey");
                                            var elTemplateBtns = elTemplateCol.appendChild(document.createElement("span"));
                                            YAHOO.util.Dom.addClass(elTemplateBtns, "dt-dlg-pickerbtns");
                                            var onclickObj = {fn:handleButtonClick, obj:this, scope:false };

                                            // Create one section in the SimpleDialog for each Column
                                            var elColumn, elKey, oButtonGrp;
                                            for(var i=0,l=allColumns.length;i<l;i++) {
                                                var oColumn = allColumns[i];
                                                var header  = headers[i][0]; 
                                                var groupname = myDataTable.getColumn(header).label;
                                                // Use the template
                                                elColumn = elTemplateCol.cloneNode(true);

                                                // Write the Column key
                                                elKey = elColumn.firstChild;
                                                elKey.innerHTML = groupname+" "+oColumn.label;

                                                // Create a ButtonGroup
                                                oButtonGrp = new YAHOO.widget.ButtonGroup({ 
                                                    id: "customerreturns-buttongrp-"+oColumn.getKey(), 
                                                    name: oColumn.getKey(), 
                                                    container: elKey.nextSibling
                                                });
                                                var chek = true;
                                                if(oColumn.className == "hiden-col"){
                                                    chek = false;
                                                }
                                                oButtonGrp.addButtons([
                                                    { label: "Show", value: "Show", checked: (chek), onclick: onclickObj},
                                                    { label: "Hide", value: "Hide", checked: (!chek), onclick: onclickObj}
                                                ]);

                                                elPicker.appendChild(elColumn);
                                            }
                                            newCols = false;
                                        }
                                        myDlg.show();
                                    };
                                    var hideDlg = function(e) {
                                        this.hide();
                                    };
                                    var handleButtonClick = function(e, oSelf) {
                                       var sKey = this.get("name");
                                        
                                        var classTd = myDataTable.getColumn(sKey)._elTh.className.split(/\s/);
                                        if(classTd[0] == "hiden-col"){
                                            var cellClass = classTd[1];
                                        }else{
                                            var cellClass = classTd[0];
                                        }
                                        if(this.get("value") === "Hide"){
                                            YAHOO.util.Dom.removeClass(myDataTable.getColumn(sKey)._elTh, "show-col");
                                        }else{
                                             YAHOO.util.Dom.addClass(myDataTable.getColumn(sKey)._elTh, "show-col");
                                        }
                                        var cells =  YAHOO.util.Dom.getElementsByClassName(cellClass);
                                        var mode = this.get("value") === "Hide" ?  'none': 'table-cell';
                                        var showCol = 0;
                                        var isHide = false;
                                        for(k = 0; k < myDataTable.getColumn(sKey)._oParent.children.length; k++){
                                            var hidenCol = YAHOO.util.Dom.hasClass(myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh, 'hiden-col');
                                            showCol = myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh.style.display != 'none' && !hidenCol ? showCol+1 : showCol;
                                            if(myDataTable.getColumn(sKey)._oParent.children[k].key == sKey && this.get("value") === "Hide")
                                                isHide = myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh.style.display == 'none' ? true:false;
                                        }
                                        if(classTd[0] == "hiden-col"){
                                            removeClass(myDataTable.getColumn(sKey)._elTh,"hiden-col");
                                        }
                                        if(!isHide){
                                            var minusplusColspan =  this.get("value") === "Hide" ?  -1: +1;
                                            var nColspan =   showCol + minusplusColspan;
                                            nColspan = nColspan > myDataTable.getColumn(sKey)._oParent.children.length ? myDataTable.getColumn(sKey)._oParent.children.length: nColspan;

                                            if(nColspan == 0){
                                                myDataTable.getColumn(sKey)._oParent._elTh.style.display = mode;   
                                            }else if(nColspan == 1 && showCol == 0){
                                                myDataTable.getColumn(sKey)._oParent._elTh.style.display = mode; 
                                                myDataTable.getColumn(sKey)._oParent._elTh.setAttribute("colSpan", nColspan);
                                            } else {
                                                myDataTable.getColumn(sKey)._oParent._elTh.setAttribute("colSpan", nColspan);
                                            }

                                            //var widthCell = $(cells[0]).width();
                                            for(j = 0; j < cells.length; j++) {
                                                cells[j].style.display = mode;
                                                //if(widthCell != $(cells[j]).children(0).width())
                                                   // $(cells[j]).children(0).width(widthCell);
                                            }
                                        }
                                            var theadEle = myDataTable.getTheadEl(),
                                            thEle = theadEle.getElementsByClassName("yui-dt-last")[0].getElementsByTagName('th');
                                            var lastCol = thEle.length - 1;
                                            var countCol = 1;
                                            for(var i=0; i < thEle.length; i++) {
                                                if(!YAHOO.util.Dom.hasClass(thEle[i], "hiden-col") && thEle[i].style.display != 'none') {
                                                    thEle[i].style.borderRight = "";
                                                    lastCol = i;
                                                    countCol++;
                                                }
                                            }
                                            thEle[lastCol].style.borderRight = countCol+"px solid #F2F2F2";
                                            TopScrollTable(myDataTable,divNameParm);
                                    };

                                    // Create the SimpleDialog
                                    YAHOO.util.Dom.removeClass("customerreturns-dlg", "inprogress");
                                     YAHOO.util.Dom.setStyle("customerreturns-dlg","display","block");
                                    var myDlg = new YAHOO.widget.SimpleDialog("customerreturns-dlg", {
                                        width: "30em",
                                        visible: false,
                                        modal: false,
                                        buttons: [ 
                                            { text:"Close",  handler:hideDlg }
                                        ],
                                        fixedcenter: true,
                                        constrainToViewport: true
                                    });
                                    myDlg.render();

                                    // Nulls out myDlg to force a new one to be created
                                    myDataTable.subscribe("columnReorderEvent", function(){
                                        newCols = true;
                                        YAHOO.util.Event.purgeElement("customerreturns-dlg-picker", true);
                                        YAHOO.util.Dom.get("customerreturns-dlg-picker").innerHTML = "";
                                    }, this, true);
		myDataTable.subscribe('renderEvent', function()    {
                                        var theadEle = this.getTheadEl(),
                                        thEle = theadEle.getElementsByTagName('th'),
                                        len = thEle.length;
                                        for(var i=0; i < len; i++) {
                                             var classTd =  this.getColumn(thEle[i].id)._elTh.className.split(/\s/);
                                                var cells =  YAHOO.util.Dom.getElementsByClassName(classTd[0]);
                                            if(YAHOO.util.Dom.hasClass(thEle[i], "show-col")) {
                                               
                                                for(j = 0; j < cells.length; j++) cells[j].style.display = 'table-cell';
                                            }
                                        }
                                    });
                                      myDataTable.subscribe('postRenderEvent', function()    {
                                         var divWidth = $("#customerreturns").width();
                                       var tableWidth = $("#customerreturns .yui-dt-bd table").width();
                                       var borderRight = divWidth - tableWidth;
                                       var paddR_name = 0;
                                        var paddR_last = 0;
                                        var currBrowser = browserDetectNav();
                                        var winWidth =window.outerWidth;
                                        if(winWidth >= 1152 && winWidth < 1280){
                                            paddR_name = currBrowser == "Firefox" ? 2:2;
                                            paddR_last = currBrowser == "Firefox" ? 2:2;
                                        }else if(winWidth >= 1280 && winWidth < 1360){
                                            paddR_name = currBrowser == "Firefox" ? 5:5;
                                            paddR_last = currBrowser == "Firefox" ? 6:5;
                                        }else if(winWidth >= 1360 && winWidth < 1440){
                                            paddR_name = currBrowser == "Firefox" ? 3:3;
                                            paddR_last = currBrowser == "Firefox" ? 9:9;
                                        }else if(winWidth >= 1440 && winWidth < 1600){
                                            paddR_name = currBrowser == "Firefox" ? 2:3;
                                            paddR_last = currBrowser == "Firefox" ? 14:13;
                                        }else if(winWidth >= 1600){
                                            paddR_name = currBrowser == "Firefox" ? 2:3;
                                            paddR_last = currBrowser == "Firefox" ? 22:21;
                                        }
                                        var theadEle = this.getTheadEl(),
                                        thEle = theadEle.getElementsByClassName("yui-dt-last")[0].getElementsByTagName('th');
                                        $("th.yui-dt-col-custname").css('padding-right',paddR_name+'px');
                                        var lastCol = thEle.length - 1;
                                        var countCol = 1;
                                        for(var i=0; i < thEle.length; i++) {
                                            if(!YAHOO.util.Dom.hasClass(thEle[i], "hiden-col")) {
                                                lastCol = i;
                                                countCol++;
                                            }
                                        }
                                          thEle[lastCol].style.borderRight = borderRight+"px solid #F2F2F2";
                                         thEle[lastCol].style.paddingRight =paddR_last+'px';
                                        TopScrollTable(this,divNameParm);
                                    });
                                    YAHOO.util.Event.addListener("customerreturns-swhd-link", "click", showDlg, this, true);

		return {
			ds: myDataSource,
			dt: myDataTable
		};

	};
        }else {
            return function(){};
        }
}
</script>
<!-- customer Transactions -->
<script type="text/javascript">
function createCustomerTransactionsDataTable(divNameParm, selectMethodParm, slsmParm, regionParm, locationParm, dealerTypeParm,account_id) {
    if(divNameParm == 'customertransactions'){
	return function() {
	
		if(selectMethodParm != 'u') {
			selectMethodParm = 'i'; /* intersection */
		}

		this.custlink = function(elCell, oRecord, oColumn, oData) {
			elCell.innerHTML = "<a href=\"" + "<? echo $baseurl."/index.php?module=Accounts&action=DetailView&record="; ?>" + oRecord.getData('id') + "\" target=\"_blank\">" + oData + "</a>";
		};
                     this.currencyRed= function(elCell, oRecord, oColumn, oData) {
                                                if(oData != null) {
                                                    var oFormatConfig = {
					prefix: "$",
					decimalPlaces: 0,
					decimalSeparator: ".",
					thousandsSeparator: ",",
					suffix: ""
				};
			if (oData < 0) {
                                                             elCell.innerHTML= '<span style="color: red">('+YAHOO.util.Number.format(oData*(-1), oFormatConfig)+')</span>';
			} else {
                                                             elCell.innerHTML = YAHOO.util.Number.format(oData, oFormatConfig);
			}
                                            }
		};
		var myColumnDefs = [ // sortable:true enables sorting
			{key:"slsm", label:"Slsm", sortable:true},
			{key:"custno", label:"CustNo", sortable:true},
			{key:"custname", label:"Name", sortable:true, formatter:this.custlink},
                                                      {key:"shipping_address_street", label:"Address", sortable:true, hidden:true},
			{key:"shipping_address_city", label:"City", sortable:true, hidden:true},
                                                      {key:"shipping_address_state", label:"State", sortable:true, hidden:true},
			{key:"shipping_address_postalcode", label:"Zip", sortable:true, hidden:true},
                                                      {key:"contact", label:"Contact", sortable:true, hidden:true},
                                                      {key:"phone", label:"Phone", sortable:true, hidden:true},

                                                      {key:"wtd_invoices", label:"# of Transactions - WTD", sortable:true, formatter:"number", className: "numberof-width"},
                                                      {key:"mtd_invoices", label:"# of Transactions - MTD", sortable:true, formatter:"number", className: "numberof-width"},
                                                      {key:"ytd_invoices", label:"# of Transactions - YTD", sortable:true, formatter:"number", className: "numberof-width"},
                                                      
                                                      {key:"mtd_av_per_trans", label:"MTD - Avg $ per Transaction", sortable:true, formatter:this.currencyRed, className: "numberof-width"},
			{key:"ytd_av_per_trans", label:"YTD - Avg $ per Transaction", sortable:true, formatter:this.currencyRed, className: "numberof-width"}
		];


	
		// DataSource instance
		var myDataSource = new YAHOO.util.DataSource("json_proxy_customer_transactions.php?");
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
		myDataSource.responseSchema = {
			resultsList: "records",
			fields: [
				{key:"id"},
				{key:"slsm"},
				{key:"slsmname"},
				{key:"custno"},
				{key:"custname"},
                                                                        {key:"shipping_address_street"},
                                                                        {key:"shipping_address_city"},
                                                                        {key:"shipping_address_state"},
                                                                        {key:"shipping_address_postalcode"},
                                                                        {key:"contact"},
                                                                        {key:"phone"},
                                                                        {key:"wtd_invoices", parser:"number"},
                                                                        {key:"mtd_invoices", parser:"number"},
                                                                        {key:"ytd_invoices", parser:"number"},
                                                                        {key:"mtd_av_per_trans", parser:"number"},
                                                                        {key:"ytd_av_per_trans",parser:"number"}
			],
			metaFields: {
				totalRecords: "totalRecords"// Access to value in the server response
			}
		};
		 // Create the Paginator 
                                    var myPaginator = new YAHOO.widget.Paginator({ 
                                        rowsPerPage:typeof localStorage.tablePaginator != "undefited" && localStorage.tablePaginator != null ? localStorage.tablePaginator : 100,
                                        containers : ["customertransactions-pag-nav"], 
                                        template : "<div class='counter-nav'>{CurrentPageReport} {RowsPerPageDropdown}</div><center style='clear: both;'>{FirstPageLink}{PreviousPageLink}{PageLinks} {NextPageLink}{LastPageLink} </center>", 
                                        pageReportTemplate : "Showing items {startRecord} - {endRecord} of {totalRecords}", 
                                        rowsPerPageOptions : [10,20,30,40,50,100,200,{ value : 5000, text : "All" } ]  
                                    }); 
		//future requets of data
		var myRequestBuilder = function(oState, oSelf) { 
			// Standard stuff:
			oState = oState || {pagination:null, sortedBy:null}; 
                                                      if(oState.pagination && oState.pagination.before){
                                                          localStorage.tablePaginator = oState.pagination.rowsPerPage;
                                                          localStorage.tablePaginatorBefore = oState.pagination.before.rowsPerPage;
                                                      }
			var sort = (oState.sortedBy) ? oState.sortedBy.key : "myDefaultColumnKey"; 
			var dir = (oState.sortedBy && oState.sortedBy.dir === YAHOO.widget.DataTable.CLASS_DESC) ? "desc" : "asc"; 
			var startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0; 
			var results = (oState.pagination) ? oState.pagination.rowsPerPage : 100; 
			
			 
			// Build custom request 
			return  "select=" + selectMethodParm +
					"&slsm=" + slsmParm +
			        "&region=" + regionParm +
			        "&location=" + locationParm +
					"&dealertype=" + dealerTypeParm +
					"&sort=" + sort + 
					"&dir=" + dir + 
					"&startIndex=" + startIndex + 
					"&results=" + results+
                                                                                    "&account=" + account_id; 
		}; 
		
		// DataTable configuration
		var myConfigs = {
			initialRequest: "select=" + selectMethodParm +
							"&slsm=" + slsmParm +
			                "&region=" + regionParm +
			                "&location=" + locationParm + 
							"&dealertype=" + dealerTypeParm +
							"&sort=custname" + 
							"&dir=asc" + 
							"&startIndex=0" + 
							"&results=100"+
                                                                                    "&account=" + account_id, // Initial request for first page of data
			generateRequest: myRequestBuilder,
			dynamicData: true, // Enables dynamic server-driven data
			sortedBy : {key:"custname", dir:YAHOO.widget.DataTable.CLASS_ASC}, // Sets UI initial sort arrow
			paginator:myPaginator ,
                                                    scrollable: "y",
                                                height: "490px",
                                                width:  "100%"
		};
		
		// DataTable instance
		var myDataTable = new YAHOO.widget.DataTable(divNameParm, myColumnDefs, myDataSource, myConfigs);
		
		// Update totalRecords on the fly with value from server
		myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
			oPayload.totalRecords = oResponse.meta.totalRecords;
			return oPayload;
		}
		
		
		/* slsm tooltip */
		var showTimer = 0, hideTimer = 0;
		var tt = new YAHOO.widget.Tooltip("myTooltip");
		myDataTable.on('cellMouseoverEvent', function (oArgs) {
			if (showTimer) {
				window.clearTimeout(showTimer);
				showTimer = 0;
			}

			var target = oArgs.target;
			var column = this.getColumn(target);
			if (column != null && column.key == 'slsm') {
				var record = this.getRecord(target);
				var description = record.getData('slsm') || '??';
				description += ' ';
				description += record.getData('slsmname') || '??? ????';
				/* var xy = [parseInt(oArgs.event.clientX,10) + 10 ,parseInt(oArgs.event.clientY,10) + 10 ]; */
				var xy = YAHOO.util.Event.getXY(oArgs.event);
				showTimer = window.setTimeout(function() {
					tt.setBody(description);
					tt.cfg.setProperty('xy',xy);
					tt.show();
					hideTimer = window.setTimeout(function() {
						tt.hide();
					},5000);
				},500);
			}
		});
		myDataTable.on('cellMouseoutEvent', function (oArgs) {
			if (showTimer) {
				window.clearTimeout(showTimer);
				showTimer = 0;
			}
			if (hideTimer) {
				window.clearTimeout(hideTimer);
				hideTimer = 0;
			}
			tt.hide();
		});
                                    // Shows dialog, creating one when necessary
                                    var newCols = true;
                                    var showDlg = function(e) {
                                        YAHOO.util.Event.stopEvent(e);
                                        
                                        if(newCols) {
                                            var cell = document.getElementById("customertransactions-dlg-picker");

                                            if ( cell.hasChildNodes() )
                                            {
                                                while ( cell.childNodes.length >= 1 )
                                                {
                                                    cell.removeChild( cell.firstChild );       
                                                } 
                                            }
                                            // Populate Dialog
                                            // Using a template to create elements for the SimpleDialog
                                            var allColumns = myDataTable.getColumnSet().keys;
                                            var elPicker = YAHOO.util.Dom.get("customertransactions-dlg-picker");
                                            var elTemplateCol = document.createElement("div");
                                            YAHOO.util.Dom.addClass(elTemplateCol, "dt-dlg-pickercol");
                                            var elTemplateKey = elTemplateCol.appendChild(document.createElement("span"));
                                            YAHOO.util.Dom.addClass(elTemplateKey, "dt-dlg-pickerkey");
                                            var elTemplateBtns = elTemplateCol.appendChild(document.createElement("span"));
                                            YAHOO.util.Dom.addClass(elTemplateBtns, "dt-dlg-pickerbtns");
                                            var onclickObj = {fn:handleButtonClick, obj:this, scope:false };

                                            // Create one section in the SimpleDialog for each Column
                                            var elColumn, elKey, oButtonGrp;
                                            for(var i=0,l=allColumns.length;i<l;i++) {
                                                var oColumn = allColumns[i];

                                                // Use the template
                                                elColumn = elTemplateCol.cloneNode(true);

                                                // Write the Column key
                                                elKey = elColumn.firstChild;
                                                elKey.innerHTML = oColumn.label;

                                                // Create a ButtonGroup
                                                oButtonGrp = new YAHOO.widget.ButtonGroup({ 
                                                    id: "customertransactions-buttongrp-"+oColumn.getKey(), 
                                                    name: oColumn.getKey(), 
                                                    container: elKey.nextSibling
                                                });
                                                oButtonGrp.addButtons([
                                                    { label: "Show", value: "Show", checked: (!oColumn.hidden), onclick: onclickObj},
                                                    { label: "Hide", value: "Hide", checked: (oColumn.hidden), onclick: onclickObj}
                                                ]);

                                                elPicker.appendChild(elColumn);
                                            }
                                            newCols = false;
                                        }
                                        myDlg.show();
                                    };
                                    var hideDlg = function(e) {
                                        this.hide();
                                    };
                                    var handleButtonClick = function(e, oSelf) {
                                        var sKey = this.get("name");
                                        if(this.get("value") === "Hide"){
                                            myDataTable.hideColumn(sKey);
                                        }else{
                                            myDataTable.showColumn(sKey);
                                        }
                                        TopScrollTable(myDataTable,divNameParm);
                                        var classTd = myDataTable.getColumn(sKey)._elTh.className.split(/\s/);   
                                        var cells =  YAHOO.util.Dom.getElementsByClassName(classTd);
                                        var mode =this.get("value") === "Hide" ?  'none': 'table-cell';
                                        
                                        for(j = 0; j < cells.length; j++) cells[j].style.display = mode;  
                                    };

                                    // Create the SimpleDialog
                                    YAHOO.util.Dom.removeClass("customertransactions-dlg", "inprogress");
                                     YAHOO.util.Dom.setStyle("customertransactions-dlg","display","block");
                                    var myDlg = new YAHOO.widget.SimpleDialog("customertransactions-dlg", {
                                        width: "30em",
                                        visible: false,
                                        modal: false,
                                        buttons: [ 
                                            { text:"Close",  handler:hideDlg }
                                        ],
                                        fixedcenter: true,
                                        constrainToViewport: true
                                    });
                                    myDlg.render();

                                    // Nulls out myDlg to force a new one to be created
                                    myDataTable.subscribe("columnReorderEvent", function(){
                                        newCols = true;
                                        YAHOO.util.Event.purgeElement("customertransactions-dlg-picker", true);
                                        YAHOO.util.Dom.get("customertransactions-dlg-picker").innerHTML = "";
                                    }, this, true);
                                     myDataTable.subscribe('postRenderEvent', function()    {
                                          var divWidth = $("#customertransactions").width();
                                       var tableWidth = $("#customertransactions .yui-dt-bd table").width();
                                       var borderRight = divWidth - tableWidth;
                                        var paddR_name = 0;
                                        var paddR_last = 0;
                                        var currBrowser = browserDetectNav();
                                        var winWidth =window.outerWidth;
                                        if(winWidth >= 1152 && winWidth < 1280){
                                            paddR_name = currBrowser == "Firefox" ? 3:3;
                                            paddR_last = currBrowser == "Firefox" ? 51:51;
                                        }else if(winWidth >= 1280 && winWidth < 1360){
                                            paddR_name = currBrowser == "Firefox" ? 4:4;
                                            paddR_last = currBrowser == "Firefox" ? 70:69;
                                        }else if(winWidth >= 1360 && winWidth < 1440){
                                            paddR_name = currBrowser == "Firefox" ? 3:5;
                                            paddR_last = currBrowser == "Firefox" ? 81:81;
                                        }else if(winWidth >= 1440 && winWidth < 1600){
                                            paddR_name = currBrowser == "Firefox" ? 3:2;
                                            paddR_last = currBrowser == "Firefox" ? 92:92;
                                        }else if(winWidth >= 1600){
                                            paddR_name = currBrowser == "Firefox" ? 2:2;
                                            paddR_last = currBrowser == "Firefox" ? 114:114;
                                        }
                                        var theadEle = this.getTheadEl(),
                                        thEle = theadEle.getElementsByClassName("yui-dt-last")[0].getElementsByTagName('th');
                                        $("th.yui-dt-col-custname").css('padding-right',paddR_name+'px');
                                        var lastCol = thEle.length - 1;
                                        var countCol = 1;
                                        for(var i=0; i < thEle.length; i++) {
                                            if(!YAHOO.util.Dom.hasClass(thEle[i], "hiden-col")) {
                                                lastCol = i;
                                                countCol++;
                                            }
                                        }
                                          thEle[lastCol].style.borderRight = borderRight+"px solid #F2F2F2";
                                         thEle[lastCol].style.paddingRight =paddR_last+'px';
                                        TopScrollTable(this,divNameParm);
                                    });
                                    YAHOO.util.Event.addListener("customertransactions-swhd-link", "click", showDlg, this, true);
		return {
			ds: myDataSource,
			dt: myDataTable
		};

	};
        }else{
           return function(){};
        }
}
</script>
<!-- customer sales comparison -->
<script type="text/javascript">
function createCustomerBudgetDataTable(divNameParm, selectMethodParm, slsmParm, regionParm, locationParm, dealerTypeParm,account_id) {
    if(divNameParm == 'customerbudget'){
                return function() {
		if(selectMethodParm != 'u') {
			selectMethodParm = 'i'; /* intersection */
		}

		this.custlink = function(elCell, oRecord, oColumn, oData) {
			elCell.innerHTML = "<a href=\"" + "<? echo $baseurl."/index.php?module=Accounts&action=DetailView&record="; ?>" + oRecord.getData('id') + "\" target=\"_blank\">" + oData + "</a>";
		};
		this.precent= function(elCell, oRecord, oColumn, oData) {
                                            if (oData < 0) {
                                               elCell.innerHTML = '<span style="color: red">('+oData.toFixed(2)*(-1)+'%)</span>';
                                            }else if(oData != null){
                                                elCell.innerHTML = '<span>'+oData.toFixed(2)+'%</span>';
                                            }else if(oData == null){
                                                oData = 0;
                                                elCell.innerHTML = '<span>'+oData.toFixed(2)+'%</span>';
                                            }
		};
                                      this.currencyRed= function(elCell, oRecord, oColumn, oData) {
                                                if(oData != null) {
                                                    var oFormatConfig = {
					prefix: "$",
					decimalPlaces: 0,
					decimalSeparator: ".",
					thousandsSeparator: ",",
					suffix: ""
				};
			if (oData < 0) {
                                                             elCell.innerHTML= '<span style="color: red">('+YAHOO.util.Number.format(oData*(-1), oFormatConfig)+')</span>';
			} else {
                                                             elCell.innerHTML = YAHOO.util.Number.format(oData, oFormatConfig);
			}
                                            }
		};
		// Column definitions
		var myColumnDefs = [ // sortable:true enables sorting
		    {key:"slsm_acc",label:"", children: [
					{key:"slsm", label:"Slsm", sortable:true},
					{key:"custno", label:"CustNo", sortable:true},
					{key:"custname", label:"Name", sortable:true, formatter:this.custlink},
                                        {key:"shipping_address_street", label:"Address", sortable:true, className: "hiden-col"},
			{key:"shipping_address_city", label:"City", sortable:true, className: "hiden-col"},
                                                      {key:"shipping_address_state", label:"State", sortable:true, className: "hiden-col"},
			{key:"shipping_address_postalcode", label:"Zip", sortable:true, className: "hiden-col"},
                                                      {key:"contact", label:"Contact", sortable:true, className: "hiden-col"},
                                                      {key:"phone", label:"Phone", sortable:true, className: "hiden-col"}
				]
		    },

                                                    {key:"cm_budget",label:"CM Budget",  children: [
                                                                        {key:"mtd_budget_sales",label:"Sales", sortable:true, formatter:this.currencyRed, className: "sales-width"},
                                                                        {key:"mtd_budget_gp",label:"GP", sortable:true, formatter:this.currencyRed, className: "gp-width"},
                                                                        {key:"mtd_budget_gpp",label:"GP%", sortable:true, formatter:this.precent, className: "gpp-width"}
                                                            ]
			},
                                                    {key:"cy_budget",label:"CY Budget",  children: [
                                                                        {key:"ytd_budget_sales",label:"Sales", sortable:true, formatter:this.currencyRed, className: "sales-width"},
                                                                        {key:"ytd_budget_gp",label:"GP", sortable:true, formatter:this.currencyRed, className: "gp-width"},
                                                                        {key:"ytd_budget_gpp",label:"GP%", sortable:true, formatter:this.precent, className: "gpp-width"}
                                                            ]
			}
		];
                

		// Custom parser
		var stringToDate = function(sData) {
			var array = sData.split("-");
			return new Date(array[1] + " " + array[0] + ", " + array[2]);
		};
		
		// DataSource instance
		var myDataSource = new YAHOO.util.DataSource("json_proxy_customer_budget.php?");
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
		myDataSource.responseSchema = {
			resultsList: "records",
			fields: [
				{key:"id"},
				{key:"slsm"},
				{key:"slsmname"},
				{key:"custno"},
				{key:"custname"},
                                                                        {key:"shipping_address_street"},
                                                                        {key:"shipping_address_city"},
                                                                        {key:"shipping_address_state"},
                                                                        {key:"shipping_address_postalcode"},
                                                                        {key:"contact"},
                                                                        {key:"phone"},
                                                                             
				{key:"mtd_budget_sales",parser:"number"},
                                                                        {key:"mtd_budget_gp",parser:"number"},
                                                                        {key:"mtd_budget_gpp",parser:"number"},
                                                                        {key:"ytd_budget_sales",parser:"number"},
                                                                        {key:"ytd_budget_gp",parser:"number"},
                                                                        {key:"ytd_budget_gpp",parser:"number"}
                                
			],
			metaFields: {
				totalRecords: "totalRecords" // Access to value in the server response
			}
		};
		 // Create the Paginator 
                                    var myPaginator = new YAHOO.widget.Paginator({ 
                                        rowsPerPage: typeof localStorage.tablePaginator != "undefited" && localStorage.tablePaginator != null ? localStorage.tablePaginator : 100,
                                        containers : ["customerbudget-pag-nav"], 
                                        template : "<div class='counter-nav'>{CurrentPageReport} {RowsPerPageDropdown}</div><center style='clear: both;'>{FirstPageLink}{PreviousPageLink}{PageLinks} {NextPageLink}{LastPageLink} </center>", 
                                        pageReportTemplate : "Showing items {startRecord} - {endRecord} of {totalRecords}", 
                                        rowsPerPageOptions : [10,20,30,40,50,100,200,{ value : 5000, text : "All" } ]  
                                    }); 
		//future requets of data
		var myRequestBuilder = function(oState, oSelf) { 
			// Standard stuff:
			oState = oState || {pagination:null, sortedBy:null}; 
                                                      if(oState.pagination && oState.pagination.before){
                                                          localStorage.tablePaginator = oState.pagination.rowsPerPage;
                                                          localStorage.tablePaginatorBefore = oState.pagination.before.rowsPerPage;
                                                      }
			var sort = (oState.sortedBy) ? oState.sortedBy.key : "myDefaultColumnKey"; 
			var dir = (oState.sortedBy && oState.sortedBy.dir === YAHOO.widget.DataTable.CLASS_DESC) ? "desc" : "asc"; 
			var startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0; 
			var results = (oState.pagination) ? oState.pagination.rowsPerPage : 100; 
			
			 
			// Build custom request 
			return  "select=" + selectMethodParm +
					"&slsm=" + slsmParm +
			        "&region=" + regionParm +
			        "&location=" + locationParm +
					"&dealertype=" + dealerTypeParm +
					"&sort=" + sort + 
					"&dir=" + dir + 
					"&startIndex=" + startIndex + 
					"&results=" + results+
                                                                                    "&account=" + account_id; 
		}; 
		
		// DataTable configuration
		var myConfigs = {
			initialRequest: "select=" + selectMethodParm +
							"&slsm=" + slsmParm +
			                "&region=" + regionParm +
			                "&location=" + locationParm + 
							"&dealertype=" + dealerTypeParm +
							"&sort=custname" + 
							"&dir=asc" + 
							"&startIndex=0" + 
							"&results=100"+
                                                                                    "&account=" + account_id, // Initial request for first page of data
			generateRequest: myRequestBuilder,
			dynamicData: true, // Enables dynamic server-driven data
			sortedBy : {key:"custname", dir:YAHOO.widget.DataTable.CLASS_ASC}, // Sets UI initial sort arrow
			paginator: myPaginator,
                                                    scrollable: "y",
                                                height: "490px",
                                                width:  "100%"
		};
		
		// DataTable instance
		var myDataTable = new YAHOO.widget.DataTable(divNameParm, myColumnDefs, myDataSource, myConfigs);
		myDataTable.getColumn("slsm_acc")._elTh.setAttribute("colSpan", 3);
                                    // Update totalRecords on the fly with value from server
		myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
			oPayload.totalRecords = oResponse.meta.totalRecords;
			return oPayload;
		}
		
		
			
		/* slsm tooltip */
		var showTimer = 0, hideTimer = 0;
		var tt = new YAHOO.widget.Tooltip("myTooltip");
		myDataTable.on('cellMouseoverEvent', function (oArgs) {
			if (showTimer) {
				window.clearTimeout(showTimer);
				showTimer = 0;
			}

			var target = oArgs.target;
			var column = this.getColumn(target);
			if (column != null && column.key == 'slsm') {
				var record = this.getRecord(target);
				var description = record.getData('slsm') || '??';
				description += ' ';
				description += record.getData('slsmname') || '??? ????';
				/* var xy = [parseInt(oArgs.event.clientX,10) + 10 ,parseInt(oArgs.event.clientY,10) + 10 ]; */
				var xy = YAHOO.util.Event.getXY(oArgs.event);
				showTimer = window.setTimeout(function() {
					tt.setBody(description);
					tt.cfg.setProperty('xy',xy);
					tt.show();
					hideTimer = window.setTimeout(function() {
						tt.hide();
					},5000);
				},500);
			}
		});
		myDataTable.on('cellMouseoutEvent', function (oArgs) {
			if (showTimer) {
				window.clearTimeout(showTimer);
				showTimer = 0;
			}
			if (hideTimer) {
				window.clearTimeout(hideTimer);
				hideTimer = 0;
			}
			tt.hide();
		});

// Shows dialog, creating one when necessary
                                    var newCols = true;
                                    var showDlg = function(e) {
                                        YAHOO.util.Event.stopEvent(e);

                                        if(newCols) {
                                            var cell = document.getElementById("customerbudget-dlg-picker");

                                            if ( cell.hasChildNodes() )
                                            {
                                                while ( cell.childNodes.length >= 1 )
                                                {
                                                    cell.removeChild( cell.firstChild );       
                                                } 
                                            }
                                            // Populate Dialog
                                            // Using a template to create elements for the SimpleDialog
                                            var allColumns = myDataTable.getColumnSet().keys;
                                            var headers = myDataTable.getColumnSet().headers;
                                            var elPicker = YAHOO.util.Dom.get("customerbudget-dlg-picker");
                                            var elTemplateCol = document.createElement("div");
                                            YAHOO.util.Dom.addClass(elTemplateCol, "dt-dlg-pickercol");
                                            var elTemplateKey = elTemplateCol.appendChild(document.createElement("span"));
                                            YAHOO.util.Dom.addClass(elTemplateKey, "dt-dlg-pickerkey");
                                            var elTemplateBtns = elTemplateCol.appendChild(document.createElement("span"));
                                            YAHOO.util.Dom.addClass(elTemplateBtns, "dt-dlg-pickerbtns");
                                            var onclickObj = {fn:handleButtonClick, obj:this, scope:false };

                                            // Create one section in the SimpleDialog for each Column
                                            var elColumn, elKey, oButtonGrp;
                                            for(var i=0,l=allColumns.length;i<l;i++) {
                                                var oColumn = allColumns[i];
                                                var header  = headers[i][0]; 
                                                var groupname = myDataTable.getColumn(header).label;
                                                // Use the template
                                                elColumn = elTemplateCol.cloneNode(true);

                                                // Write the Column key
                                                elKey = elColumn.firstChild;
                                                elKey.innerHTML = groupname+" "+oColumn.label;

                                                // Create a ButtonGroup
                                                oButtonGrp = new YAHOO.widget.ButtonGroup({ 
                                                    id: "customerbudget-buttongrp-"+oColumn.getKey(), 
                                                    name: oColumn.getKey(), 
                                                    container: elKey.nextSibling
                                                });
                                                var chek = true;
                                                if(oColumn.className == "hiden-col"){
                                                    chek = false;
                                                }
                                                oButtonGrp.addButtons([
                                                    { label: "Show", value: "Show", checked: (chek), onclick: onclickObj},
                                                    { label: "Hide", value: "Hide", checked: (!chek), onclick: onclickObj}
                                                ]);

                                                elPicker.appendChild(elColumn);
                                            }
                                            newCols = false;
                                        }
                                        myDlg.show();
                                    };
                                    var hideDlg = function(e) {
                                        this.hide();
                                    };
                                    var handleButtonClick = function(e, oSelf) {
                                       var sKey = this.get("name");
                                        
                                        var classTd = myDataTable.getColumn(sKey)._elTh.className.split(/\s/);
                                        if(classTd[0] == "hiden-col"){
                                            var cellClass = classTd[1];
                                        }else{
                                            var cellClass = classTd[0];
                                        }
                                        if(this.get("value") === "Hide"){
                                            YAHOO.util.Dom.removeClass(myDataTable.getColumn(sKey)._elTh, "show-col");
                                        }else{
                                             YAHOO.util.Dom.addClass(myDataTable.getColumn(sKey)._elTh, "show-col");
                                        }
                                        var cells =  YAHOO.util.Dom.getElementsByClassName(cellClass);
                                        var mode = this.get("value") === "Hide" ?  'none': 'table-cell';
                                        var showCol = 0;
                                        var isHide = false;
                                        for(k = 0; k < myDataTable.getColumn(sKey)._oParent.children.length; k++){
                                            var hidenCol = YAHOO.util.Dom.hasClass(myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh, 'hiden-col');
                                            showCol = myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh.style.display != 'none' && !hidenCol ? showCol+1 : showCol;
                                            if(myDataTable.getColumn(sKey)._oParent.children[k].key == sKey && this.get("value") === "Hide")
                                                isHide = myDataTable.getColumn(myDataTable.getColumn(sKey)._oParent.children[k].key)._elTh.style.display == 'none' ? true:false;
                                        }
                                        if(classTd[0] == "hiden-col"){
                                            removeClass(myDataTable.getColumn(sKey)._elTh,"hiden-col");
                                        }
                                        if(!isHide){
                                            var minusplusColspan =  this.get("value") === "Hide" ?  -1: +1;
                                            var nColspan =   showCol + minusplusColspan;
                                            nColspan = nColspan > myDataTable.getColumn(sKey)._oParent.children.length ? myDataTable.getColumn(sKey)._oParent.children.length: nColspan;

                                            if(nColspan == 0){
                                                myDataTable.getColumn(sKey)._oParent._elTh.style.display = mode;   
                                            }else if(nColspan == 1 && showCol == 0){
                                                myDataTable.getColumn(sKey)._oParent._elTh.style.display = mode; 
                                                myDataTable.getColumn(sKey)._oParent._elTh.setAttribute("colSpan", nColspan);
                                            } else {
                                                myDataTable.getColumn(sKey)._oParent._elTh.setAttribute("colSpan", nColspan);
                                            }
                                            //var widthCell = $(cells[0]).width();
                                            for(j = 0; j < cells.length; j++) {
                                                cells[j].style.display = mode;
                                                //if(widthCell != $(cells[j]).children(0).width())
                                                   // $(cells[j]).children(0).width(widthCell);
                                            }
                                        }
                                            var theadEle = myDataTable.getTheadEl(),
                                            thEle = theadEle.getElementsByClassName("yui-dt-last")[0].getElementsByTagName('th');
                                            var lastCol = thEle.length - 1;
                                            var countCol = 1;
                                            for(var i=0; i < thEle.length; i++) {
                                                if(!YAHOO.util.Dom.hasClass(thEle[i], "hiden-col") && thEle[i].style.display != 'none') {
                                                    thEle[i].style.borderRight = "";
                                                    lastCol = i;
                                                    countCol++;
                                                }
                                            }
                                            thEle[lastCol].style.borderRight = countCol+"px solid #F2F2F2";
                                            TopScrollTable(myDataTable,divNameParm);
                                    };

                                    // Create the SimpleDialog
                                    YAHOO.util.Dom.removeClass("customerbudget-dlg", "inprogress");
                                     YAHOO.util.Dom.setStyle("customerbudget-dlg","display","block");
                                    var myDlg = new YAHOO.widget.SimpleDialog("customerbudget-dlg", {
                                        width: "30em",
                                        visible: false,
                                        modal: false,
                                        buttons: [ 
                                            { text:"Close",  handler:hideDlg }
                                        ],
                                        fixedcenter: true,
                                        constrainToViewport: true
                                    });
                                    myDlg.render();

                                    // Nulls out myDlg to force a new one to be created
                                    myDataTable.subscribe("columnReorderEvent", function(){
                                        newCols = true;
                                        YAHOO.util.Event.purgeElement("customerbudget-dlg-picker", true);
                                        YAHOO.util.Dom.get("customerbudget-dlg-picker").innerHTML = "";
                                    }, this, true);
		                            myDataTable.subscribe('renderEvent', function()    {
                                        var theadEle = this.getTheadEl(),
                                        thEle = theadEle.getElementsByClassName("yui-dt-last")[0].getElementsByTagName('th'),
                                        len = thEle.length;
                                        for(var i=0; i < len; i++) {
                                             var classTd =  this.getColumn(thEle[i].id)._elTh.className.split(/\s/);
                                                var cells =  YAHOO.util.Dom.getElementsByClassName(classTd[0]);
                                            if(YAHOO.util.Dom.hasClass(thEle[i], "show-col")) {
                                                
                                                for(var j = 0; j < cells.length; j++){
                                                 cells[j].style.display = 'table-cell';
                                                }
                                            }                                     
                                        }
                                    });
                                    myDataTable.subscribe('postRenderEvent', function()    {
                                           var divWidth = $("#customerbudget").width();
                                       var tableWidth = $("#customerbudget .yui-dt-bd table").width();
                                       var borderRight = divWidth - tableWidth;
                                       var paddR_name = 0;
                                        var paddR_last = 0;
                                        var currBrowser = browserDetectNav();
                                        var winWidth =window.outerWidth;
                                        if(winWidth >= 1152 && winWidth < 1280){
                                            paddR_name = currBrowser == "Firefox" ? 0:0;
                                            paddR_last = currBrowser == "Firefox" ? 1:1;
                                        }else if(winWidth >= 1280 && winWidth < 1360){
                                            paddR_name = currBrowser == "Firefox" ? 3:3;
                                            paddR_last = currBrowser == "Firefox" ? 4:4;
                                        }else if(winWidth >= 1360 && winWidth < 1440){
                                            paddR_name = currBrowser == "Firefox" ? 2:2;
                                            paddR_last = currBrowser == "Firefox" ? 10:10;
                                        }else if(winWidth >= 1440 && winWidth < 1600){
                                            paddR_name = currBrowser == "Firefox" ? 3:3;
                                            paddR_last = currBrowser == "Firefox" ? 16:15;
                                        }else if(winWidth >= 1600){
                                            paddR_name = currBrowser == "Firefox" ? 2:2;
                                            paddR_last = currBrowser == "Firefox" ? 27:27;
                                        }
                                        var theadEle = this.getTheadEl(),
                                        thEle = theadEle.getElementsByClassName("yui-dt-last")[0].getElementsByTagName('th');
                                        $("th.yui-dt-col-custname").css('padding-right',paddR_name+'px');
                                        var lastCol = thEle.length - 1;
                                        var countCol = 1;
                                        for(var i=0; i < thEle.length; i++) {
                                            if(!YAHOO.util.Dom.hasClass(thEle[i], "hiden-col")) {
                                                lastCol = i;
                                                countCol++;
                                            }
                                        }
                                          thEle[lastCol].style.borderRight = borderRight+"px solid #F2F2F2";
                                         thEle[lastCol].style.paddingRight =paddR_last+'px';
                                        TopScrollTable(this,divNameParm);
                                    });
                                    YAHOO.util.Event.addListener("customerbudget-swhd-link", "click", showDlg, this, true);

		return {
			ds: myDataSource,
			dt: myDataTable
		};

	};
        }else {
            return function(){};
        }
}
</script>
<script>
</script>>        
</body>
</html>


