<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

require_once('XTemplate/xtpl.php');
require_once("include/upload_file.php");
require_once('include/modules.php');
require_once('include/utils/db_utils.php');
require_once('modules/Pricing/Pricing.php');

global $beanList, $beanFiles, $currentModule, $focus, $action, $app_strings, $app_list_strings, $current_language, $timedate, $mod_strings;

$bean = $beanList[$_REQUEST['module_name']];
require_once($beanFiles[$bean]);
$focus = new $bean;

class Popup_Picker {

    function Popup_Picker() {
        
    }
    
    function process_page_defExep($pageType = 'Default') {
        global $theme;
        global $focus;
        global $app_strings;
        global $image_path;

        $image_path = 'themes/' . $theme . '/images/';
        $pricing_th = $pageType == 'DefaultException' ? '<th>Pricing</th>':'';
        $sort_num = $pageType == 'DefaultException' ? 2:1;
        insert_popup_header($theme);
        //output header

        echo "<table width='100%' cellpadding='0' cellspacing='0'><tr><td>";
        echo get_module_title($focus->module_dir, translate('LBL_MODULE_NAME', $focus->module_dir) . ": " . $focus->name, false);
        echo "</td><td align='right' class='moduleTitle'>";
        echo "<A href='javascript:print();' class='utilsLink'><img src='" . $image_path . "print.gif' width='13' height='13' alt='" . $app_strings['LNK_PRINT'] . "' border='0' align='absmiddle'></a>&nbsp;<A href='javascript:print();' class='utilsLink'>" . $app_strings['LNK_PRINT'] . "</A>\n";
        echo "</td></tr></table>";
        echo "<div class='table-content' style='width:100%;'><h3 style='cursor: pointer;font-size: 18px;'>Normal Line</h3>";
        echo '<table id="norm-line-list" style="width: 99%">
            <thead>
                '.$pricing_th.'
                <th>Profile Type</th>
                <th>Line</th>
                <th>Cat</th>
                <th>PC</th>
                <th>Eff-date</th>
                <th>Rev-date</th>
                <th>Exp-date</th>
                <th>PF</th>
                <th>Mult</th>
                <th>Accel</th>
                <th>Rnd</th>
                <th>Strategic</th>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" class="dataTables_empty">Loading data from server</td>
                </tr>
            </tbody>
        </table></div>';
        insert_popup_footer();
        echo '<script language="javascript">
            $(document).ready(function(){
              var url ="index.php?module=Pricing&action=getNormLine'.$pageType.'&record='.$_REQUEST['record'].'";
             $("#norm-line-list").dataTable({
                                "bJQueryUI": true,
                                "bDestroy": true,
                                "bProcessing": true,
                                "bServerSide": true,
                                "bAutoWidth": false, 
                                "sAjaxSource": url,
                                "aaSorting": [[ '.$sort_num.', "asc" ]],
                                "iDisplayLength": 99999999,
                                              "oLanguage": {
                    "sLengthMenu": \'Show <select>\' +
                                                \'<option value="10">10</option>\' +
                                                \'<option value="20">20</option>\' +
                                                \'<option value="30">30</option>\' +
                                                \'<option value="40">40</option>\' +
                                                \'<option value="50">50</option>\' +
                                                \'<option value="100">100</option>\' +
                                                \'<option value="200">200</option>\' +
                                                \'<option value="99999999">All</option>\' +
                                                \'</select> entries\'
            },
                                    "sPaginationType": "full_numbers"
                  });
            });
            </script>';
        
        echo "<div class='table-content' style='width:100%'><h3 style='cursor: pointer;font-size: 18px;'>Normal Part</h3>";
        echo '<table id="norm-prod-list" style="width: 99%">
            <thead>
                '.$pricing_th.'
                <th>Profile Type</th>
                <th>Partno</th>
                <th>Eff-date</th>
                <th>Rev-date</th>
                <th>Exp-date</th>
                <th>Base</th>
                <th>PF</th>
                <th>Mult</th>
                <th>Price</th>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" class="dataTables_empty">Loading data from server</td>
                </tr>
            </tbody>
        </table></div>';
        insert_popup_footer();
        echo '<script language="javascript">
            $(document).ready(function(){
              var url ="index.php?module=Pricing&action=getNormProd'.$pageType.'&record='.$_REQUEST['record'].'";
             $("#norm-prod-list").dataTable({
                                "bJQueryUI": true,
                                "bDestroy": true,
                                "bProcessing": true,
                                "bServerSide": true,
                                "bAutoWidth": false, 
                                "sAjaxSource": url,
                                "aaSorting": [[ '.$sort_num.', "asc" ]],
                                "iDisplayLength": 99999999,
                                              "oLanguage": {
                    "sLengthMenu": \'Show <select>\' +
                                                \'<option value="10">10</option>\' +
                                                \'<option value="20">20</option>\' +
                                                \'<option value="30">30</option>\' +
                                                \'<option value="40">40</option>\' +
                                                \'<option value="50">50</option>\' +
                                                \'<option value="100">100</option>\' +
                                                \'<option value="200">200</option>\' +
                                                \'<option value="99999999">All</option>\' +
                                                \'</select> entries\'
            },
                                    "sPaginationType": "full_numbers"
                  });
            });
            </script>';
        echo "<div class='table-content' style='width:100%;'><h3 style='cursor:pointer;font-size: 18px;'>Stock Line</h3>";
        echo '<table id="stock-line-list" style="width: 99%">
            <thead>
                '.$pricing_th.'
                <th>Profile Type</th>
                <th>Line</th>
                <th>Cat</th>
                <th>PC</th>
                <th>Eff-date</th>
                <th>Rev-date</th>
                <th>Exp-date</th>
                <th>PF</th>
                <th>Mult</th>
                <th>Accel</th>
                <th>Rnd</th>
                <th>Strategic</th>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" class="dataTables_empty">Loading data from server</td>
                </tr>
            </tbody>
        </table></div>';
        insert_popup_footer();
        echo '<script language="javascript">
            $(document).ready(function(){
              var url ="index.php?module=Pricing&action=getStockLine'.$pageType.'&record='.$_REQUEST['record'].'";
             $("#stock-line-list").dataTable({
                                "bJQueryUI": true,
                                "bDestroy": true,
                                "bProcessing": true,
                                "bServerSide": true,
                                "bAutoWidth": false, 
                                "sAjaxSource": url,
                                "aaSorting": [[ '.$sort_num.', "asc" ]],
                                "iDisplayLength": 99999999,
                                              "oLanguage": {
                    "sLengthMenu": \'Show <select>\' +
                                                \'<option value="10">10</option>\' +
                                                \'<option value="20">20</option>\' +
                                                \'<option value="30">30</option>\' +
                                                \'<option value="40">40</option>\' +
                                                \'<option value="50">50</option>\' +
                                                \'<option value="100">100</option>\' +
                                                \'<option value="200">200</option>\' +
                                                \'<option value="99999999">All</option>\' +
                                                \'</select> entries\'
            },
                                    "sPaginationType": "full_numbers"
                  });
            });
            </script>';
        echo "<div class='table-content' style='width:100%'><h3 style='cursor:pointer;font-size: 18px;'>Stock Part</h3>";
        echo '<table id="stock-prod-list" style="width: 99%">
            <thead>
                '.$pricing_th.'
                <th>Profile Type</th>
                <th>Partno</th>
                <th>Eff-date</th>
                <th>Rev-date</th>
                <th>Exp-date</th>
                <th>Base</th>
                <th>PF</th>
                <th>Mult</th>
                <th>Price</th>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" class="dataTables_empty">Loading data from server</td>
                </tr>
            </tbody>
        </table></div>';
        insert_popup_footer();
        echo '<script language="javascript">
            $(document).ready(function(){
              var url ="index.php?module=Pricing&action=getStockProd'.$pageType.'&record='.$_REQUEST['record'].'";
             $("#stock-prod-list").dataTable({
                                "bJQueryUI": true,
                                "bDestroy": true,
                                "bProcessing": true,
                                "bServerSide": true,
                                "bAutoWidth": false, 
                                "sAjaxSource": url,
                                "aaSorting": [[ '.$sort_num.', "desc" ]],
                                "iDisplayLength": 99999999,
                                              "oLanguage": {
                    "sLengthMenu": \'Show <select>\' +
                                                \'<option value="10">10</option>\' +
                                                \'<option value="20">20</option>\' +
                                                \'<option value="30">30</option>\' +
                                                \'<option value="40">40</option>\' +
                                                \'<option value="50">50</option>\' +
                                                \'<option value="100">100</option>\' +
                                                \'<option value="200">200</option>\' +
                                                \'<option value="99999999">All</option>\' +
                                                \'</select> entries\'
            },
                                    "sPaginationType": "full_numbers"
                  });
            });
            </script>';
    }
    
    function process_page() {
   echo '<link rel="stylesheet" type="text/css" href="custom/modules/Accounts/datatables.css" />
            <link type="text/css" href="modules/Calendar2/css/themes/base/ui.all.css" rel="stylesheet" />
            <script src="custom/modules/Accounts/jquery.datatables.min.js" type="text/javascript"></script>
<script type="text/javascript" src="modules/Calendar2/js/jquery-ui-1.7.2.custom.min.js"></script>';

        if (!empty($_GET['default_pricing']) && $_GET['default_pricing'] == 1) {
            $this->process_page_defExep('Default');
        }elseif (!empty($_GET['default_exception_pricing']) && $_GET['default_exception_pricing'] == 1) {
            $this->process_page_defExep('DefaultException');      
        }else{
            $this->process_page_defExep('Exception');
        }
         echo '<script language="javascript">
            $(document).ready(function(){
                    $(".dataTables_wrapper").css("min-height","0");
                    $( ".table-content h3" ).click(function() {
			$(this).parent().find(".dataTables_wrapper").toggle();
                                                if($(this).parent().find(".dataTables_wrapper").css("display") == "none"){
                                                    $(this).css("background", "#CCC");
                                                }else{
                                                    $(this).css("background", "#FFF");
                                                }
			return false;
		});
            });
            </script>
            <style type="text/css">
                .datatables_wrapper table td, .datatables_wrapper table th, .datatables_wrapper{font-size:16px;}
            </style>';
        return;
        /*global $theme;
        global $focus;
        global $mod_strings;
        global $app_strings;
        global $app_list_strings;
        global $currentModule;
        global $odd_bg;
        global $even_bg;
        global $image_path;
        global $audit;
        global $current_language;

        $theme_path = "themes/" . $theme . "/";
        $image_path = 'themes/' . $theme . '/images/';


        $norm_line_list = Pricing::get_norm_line();
        $stock_line_list = Pricing::get_stock_line();
        $norm_prod_list = Pricing::get_norm_product();
        $stock_prod_list = Pricing::get_stock_product();
        $xtpl = new XTemplate('modules/Pricing/Popup_picker.html');

        $xtpl->assign('THEME', $theme);
        $xtpl->assign('MOD', $mod_strings);
        $xtpl->assign('APP', $app_strings);
        insert_popup_header($theme);
        //output header

        echo "<table width='100%' cellpadding='0' cellspacing='0'><tr><td>";
        $focus_mod_strings = return_module_language($current_language, $focus->module_dir);
        echo get_module_title($focus->module_dir, translate('LBL_MODULE_NAME', $focus->module_dir) . ": " . $focus->name, false);
        echo "</td><td align='right' class='moduleTitle'>";
        echo "<A href='javascript:print();' class='utilsLink'><img src='" . $image_path . "print.gif' width='13' height='13' alt='" . $app_strings['LNK_PRINT'] . "' border='0' align='absmiddle'></a>&nbsp;<A href='javascript:print();' class='utilsLink'>" . $app_strings['LNK_PRINT'] . "</A>\n";
        echo "</td></tr></table>";
        $oddRow = true;
        $fields = '';
        $start_tag = "<table><tr><td class=\"listViewPaginationLinkS1\">";
        $end_tag = "</td></tr></table>";


        $n = 0;
        echo "<table width='100%' cellpadding='0' cellspacing='0' ><tr><td><h3>Normal Line</h3></td>";
        foreach ($norm_line_list as $v) {
            foreach ($v as $t) {

                $activity_fields = array(
                    'PROFILE' => $norm_line_list['profiletype'][$n],
                    'LINE' => $norm_line_list['line'][$n],
                    'CATEGORY' => $norm_line_list['cat'][$n],
                    'PRICECODE' => $norm_line_list['pc'][$n],
                    'PF' => $norm_line_list['pf'][$n],
                    'MULT' => $norm_line_list['mult'][$n],
                    'EXPIREDATE' => $norm_line_list['expire_date'][$n],
                    'REVIEWDATE' => $norm_line_list['review_date'][$n],
                    'EFFECTIVEDATE' => $norm_line_list['effective_date'][$n],
                    'ACCEL' => $norm_line_list['accel'][$n],
                    'ROUND' => $norm_line_list['round'][$n],
                    'STRATEGIC' => $norm_line_list['strategic'][$n]
                );

                if ($norm_line_list['profiletype'][$n] != null) {
                    $n = $n + 1;

                    $xtpl->assign("ACTIVITY", $activity_fields);

                    if ($oddRow) {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'oddListRow');
                        $xtpl->assign("BG_COLOR", '#CCCCCC');
                    } else {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'evenListRow');
                        $xtpl->assign("BG_COLOR", $even_bg);
                    }
                    $oddRow = !$oddRow;
                    $xtpl->parse("normalline.row");
                    // Put the rows in.
                }//end if
            }//end foreach
        }//end for each

        $xtpl->parse("normalline");
        $xtpl->out("normalline");
        insert_popup_footer();


        $n = 0;
        echo "<table width='100%' cellpadding='0' cellspacing='0' ><tr><td><h3>Normal Part</h3></td>";
        foreach ($norm_prod_list as $v) {
            foreach ($v as $t) {

                $activity_fields = array(
                    'PROFILE' => $norm_prod_list['profiletype'][$n],
                    'PARTNO' => $norm_prod_list['partno'][$n],
                    'PF' => $norm_prod_list['pf'][$n],
                    'MULT' => $norm_prod_list['mult'][$n],
                    'EXPIREDATE' => $norm_prod_list['expire_date'][$n],
                    'EFFECTIVEDATE' => $norm_prod_list['effective_date'][$n],
                    'REVIEWDATE' => $norm_prod_list['review_date'][$n],
                    'PRICE' => $norm_prod_list['price'][$n],
                    'BASE' => $norm_prod_list['basetype'][$n],
                );

                if ($norm_prod_list['profiletype'][$n] != null) {
                    $n = $n + 1;

                    $xtpl->assign("ACTIVITY", $activity_fields);

                    if ($oddRow) {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'oddListRow');
                        $xtpl->assign("BG_COLOR", '#CCCCCC');
                    } else {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'evenListRow');
                        $xtpl->assign("BG_COLOR", $even_bg);
                    }
                    $oddRow = !$oddRow;
                    $xtpl->parse("normalprod.row");
                    // Put the rows in.
                }//end if
            }//end foreach
        }//end for each

        $xtpl->parse("normalprod");
        $xtpl->out("normalprod");
        insert_popup_footer();

        $n = 0;

        echo "<table width='100%' cellpadding='0' cellspacing='0' ><tr><td><h3>Stock Line</h3></td>";
        foreach ($stock_line_list as $v) {
            foreach ($v as $t) {
                $activity_fields = array(
                    'PROFILE' => $stock_line_list['profiletype'][$n],
                    'LINE' => $stock_line_list['line'][$n],
                    'CATEGORY' => $stock_line_list['cat'][$n],
                    'PRICECODE' => $stock_line_list['pc'][$n],
                    'PF' => $stock_line_list['pf'][$n],
                    'MULT' => $stock_line_list['mult'][$n],
                    'EXPIREDATE' => $stock_line_list['expire_date'][$n],
                    'REVIEWDATE' => $stock_line_list['review_date'][$n],
                    'EFFECTIVEDATE' => $stock_line_list['effective_date'][$n],
                    'ACCEL' => $stock_line_list['accel'][$n],
                    'ROUND' => $stock_line_list['round'][$n],
                    'STRATEGIC' => $stock_line_list['strategic'][$n]
                );

                if ($stock_line_list['profiletype'][$n] != null) {
                    $n = $n + 1;

                    $xtpl->assign("ACTIVITY", $activity_fields);

                    if ($oddRow) {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'oddListRow');
                        $xtpl->assign("BG_COLOR", '#CCCCCC');
                    } else {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'evenListRow');
                        $xtpl->assign("BG_COLOR", $even_bg);
                    }
                    $oddRow = !$oddRow;
                    $xtpl->parse("stockline.row");
                    // Put the rows in.
                }//end if
            }//end foreach
        }//end for each

        $xtpl->parse("stockline");
        $xtpl->out("stockline");
        insert_popup_footer();

        $n = 0;
        echo "<table width='100%' cellpadding='0' cellspacing='0' ><tr><td><h3>Stock Part</h3></td>";
        foreach ($stock_prod_list as $v) {
            foreach ($v as $t) {

                $activity_fields = array(
                    'PROFILE' => $stock_line_list['profiletype'][$n],
                    'PARTNO' => $stock_line_list['partno'][$n],
                    'PF' => $stock_line_list['pf'][$n],
                    'MULT' => $stock_line_list['mult'][$n],
                    'EXPIREDATE' => $stock_line_list['expire_date'][$n],
                    'EFFECTIVEDATE' => $stock_line_list['effective_date'][$n],
                    'REVIEWDATE' => $stock_line_list['review_date'][$n],
                    'PRICE' => $stock_line_list['price'][$n],
                    'BASE' => $stock_line_list['basetype'][$n],
                );

                if ($stock_line_list['profiletype'][$n] != null) {
                    $n = $n + 1;

                    $xtpl->assign("ACTIVITY", $activity_fields);

                    if ($oddRow) {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'oddListRow');
                        $xtpl->assign("BG_COLOR", '#CCCCCC');
                    } else {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'evenListRow');
                        $xtpl->assign("BG_COLOR", $even_bg);
                    }
                    $oddRow = !$oddRow;
                    $xtpl->parse("stockprod.row");
                    // Put the rows in.
                }//end if
            }//end foreach
        }//end for each

        $xtpl->parse("stockprod");
        $xtpl->out("stockprod");
        insert_popup_footer();*/
    }

//end function

    function process_page_default() {
        global $theme;
        global $focus;
        global $mod_strings;
        global $app_strings;
        global $app_list_strings;
        global $currentModule;
        global $odd_bg;
        global $even_bg;
        global $image_path;
        global $audit;
        global $current_language;

        $theme_path = "themes/" . $theme . "/";
        $image_path = 'themes/' . $theme . '/images/';



        $norm_line_list = Pricing::get_norm_line_default();
        $stock_line_list = Pricing::get_stock_line_default();
        $norm_prod_list = Pricing::get_norm_product_default();
        $stock_prod_list = Pricing::get_stock_product_default();
        $xtpl = new XTemplate('modules/Pricing/Popup_picker_default.html');

        $xtpl->assign('THEME', $theme);
        $xtpl->assign('MOD', $mod_strings);
        $xtpl->assign('APP', $app_strings);
        insert_popup_header($theme);
        //output header

        echo "<table width='100%' cellpadding='0' cellspacing='0'><tr><td>";
        $focus_mod_strings = return_module_language($current_language, $focus->module_dir);
        echo get_module_title($focus->module_dir, translate('LBL_MODULE_NAME', $focus->module_dir) . ": " . $focus->name, false);
        echo "</td><td align='right' class='moduleTitle'>";
        echo "<A href='javascript:print();' class='utilsLink'><img src='" . $image_path . "print.gif' width='13' height='13' alt='" . $app_strings['LNK_PRINT'] . "' border='0' align='absmiddle'></a>&nbsp;<A href='javascript:print();' class='utilsLink'>" . $app_strings['LNK_PRINT'] . "</A>\n";
        echo "</td></tr></table>";
        $oddRow = true;
        $fields = '';
        $start_tag = "<table><tr><td class=\"listViewPaginationLinkS1\">";
        $end_tag = "</td></tr></table>";


        $n = 0;
        echo "<table width='100%' cellpadding='0' cellspacing='0' ><tr><td><h3>Normal Line</h3></td>";
        foreach ($norm_line_list as $v) {
            foreach ($v as $t) {

                $activity_fields = array(
                    'LINE' => $norm_line_list['line'][$n],
                    'CATEGORY' => $norm_line_list['cat'][$n],
                    'PRICECODE' => $norm_line_list['pc'][$n],
                    'PF' => $norm_line_list['pf'][$n],
                    'MULT' => $norm_line_list['mult'][$n],
                    'EXPIREDATE' => $norm_line_list['expire_date'][$n],
                    //'REVIEWDATE' => $norm_line_list['review_date'][$n],
                    'EFFECTIVEDATE' => $norm_line_list['effective_date'][$n],
                    'ACCEL' => $norm_line_list['accel'][$n],
                    'ROUND' => $norm_line_list['round'][$n],
                    'STRATEGIC' => $norm_line_list['strategic'][$n]
                );

                if ($norm_line_list['profiletype'][$n] != null) {
                    $n = $n + 1;

                    $xtpl->assign("ACTIVITY", $activity_fields);

                    if ($oddRow) {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'oddListRow');
                        $xtpl->assign("BG_COLOR", '#CCCCCC');
                    } else {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'evenListRow');
                        $xtpl->assign("BG_COLOR", $even_bg);
                    }
                    $oddRow = !$oddRow;
                    $xtpl->parse("normalline.row");
                    // Put the rows in.
                }//end if
            }//end foreach
        }//end for each

        $xtpl->parse("normalline");
        $xtpl->out("normalline");
        insert_popup_footer();


        $n = 0;
        echo "<table width='100%' cellpadding='0' cellspacing='0' ><tr><td><h3>Normal Part</h3></td>";
        foreach ($norm_prod_list as $v) {
            foreach ($v as $t) {

                $activity_fields = array(
                    'PARTNO' => $norm_prod_list['partno'][$n],
                    'PF' => $norm_prod_list['pf'][$n],
                    'MULT' => $norm_prod_list['mult'][$n],
                    'EXPIREDATE' => $norm_prod_list['expire_date'][$n],
                    'EFFECTIVEDATE' => $norm_prod_list['effective_date'][$n],
                    //'REVIEWDATE' => $norm_prod_list['review_date'][$n],
                    'PRICE' => $norm_prod_list['price'][$n],
                    'BASE' => $norm_prod_list['basetype'][$n],
                );

                if ($norm_prod_list['profiletype'][$n] != null) {
                    $n = $n + 1;

                    $xtpl->assign("ACTIVITY", $activity_fields);

                    if ($oddRow) {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'oddListRow');
                        $xtpl->assign("BG_COLOR", '#CCCCCC');
                    } else {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'evenListRow');
                        $xtpl->assign("BG_COLOR", $even_bg);
                    }
                    $oddRow = !$oddRow;
                    $xtpl->parse("normalprod.row");
                    // Put the rows in.
                }//end if
            }//end foreach
        }//end for each

        $xtpl->parse("normalprod");
        $xtpl->out("normalprod");
        insert_popup_footer();

        $n = 0;

        echo "<table width='100%' cellpadding='0' cellspacing='0' ><tr><td><h3>Stock Line</h3></td>";
        foreach ($stock_line_list as $v) {
            foreach ($v as $t) {
                $activity_fields = array(
                    'LINE' => $stock_line_list['line'][$n],
                    'CATEGORY' => $stock_line_list['cat'][$n],
                    'PRICECODE' => $stock_line_list['pc'][$n],
                    'PF' => $stock_line_list['pf'][$n],
                    'MULT' => $stock_line_list['mult'][$n],
                    'EXPIREDATE' => $stock_line_list['expire_date'][$n],
                    //'REVIEWDATE' => $stock_line_list['review_date'][$n],
                    'EFFECTIVEDATE' => $stock_line_list['effective_date'][$n],
                    'ACCEL' => $stock_line_list['accel'][$n],
                    'ROUND' => $stock_line_list['round'][$n],
                    'STRATEGIC' => $stock_line_list['strategic'][$n]
                );

                if ($stock_line_list['profiletype'][$n] != null) {
                    $n = $n + 1;

                    $xtpl->assign("ACTIVITY", $activity_fields);

                    if ($oddRow) {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'oddListRow');
                        $xtpl->assign("BG_COLOR", '#CCCCCC');
                    } else {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'evenListRow');
                        $xtpl->assign("BG_COLOR", $even_bg);
                    }
                    $oddRow = !$oddRow;
                    $xtpl->parse("stockline.row");
                    // Put the rows in.
                }//end if
            }//end foreach
        }//end for each

        $xtpl->parse("stockline");
        $xtpl->out("stockline");
        insert_popup_footer();

        $n = 0;
        echo "<table width='100%' cellpadding='0' cellspacing='0' ><tr><td><h3>Stock Part</h3></td>";
        foreach ($stock_prod_list as $v) {
            foreach ($v as $t) {

                $activity_fields = array(
                    'PARTNO' => $stock_prod_list['partno'][$n],
                    'PF' => $stock_prod_list['pf'][$n],
                    'MULT' => $stock_prod_list['mult'][$n],
                    'EXPIREDATE' => $stock_prod_list['expire_date'][$n],
                    'EFFECTIVEDATE' => $stock_prod_list['effective_date'][$n],
                   // 'REVIEWDATE' => $stock_line_list['review_date'][$n],
                    'PRICE' => $stock_prod_list['price'][$n],
                    'BASE' => $stock_prod_list['basetype'][$n],
                );

                if ($stock_prod_list['profiletype'][$n] != null) {
                    $n = $n + 1;

                    $xtpl->assign("ACTIVITY", $activity_fields);

                    if ($oddRow) {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'oddListRow');
                        $xtpl->assign("BG_COLOR", '#CCCCCC');
                    } else {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'evenListRow');
                        $xtpl->assign("BG_COLOR", $even_bg);
                    }
                    $oddRow = !$oddRow;
                    $xtpl->parse("stockprod.row");
                    // Put the rows in.
                }//end if
            }//end foreach
        }//end for each

        $xtpl->parse("stockprod");
        $xtpl->out("stockprod");
        insert_popup_footer();
    }
    
    function process_page_default_exception(){
        global $theme;
        global $focus;
        global $mod_strings;
        global $app_strings;
        global $app_list_strings;
        global $currentModule;
        global $odd_bg;
        global $even_bg;
        global $image_path;
        global $audit;
        global $current_language;

        $theme_path = "themes/" . $theme . "/";
        $image_path = 'themes/' . $theme . '/images/';



        $norm_line_list = Pricing::get_norm_line_default_exception();
        $stock_line_list = Pricing::get_stock_line_default_exception();
        $norm_prod_list = Pricing::get_norm_product_default_exception();
        $stock_prod_list = Pricing::get_stock_product_default_exception();
        $xtpl = new XTemplate('modules/Pricing/Popup_picker.html');

        $xtpl->assign('THEME', $theme);
        $xtpl->assign('MOD', $mod_strings);
        $xtpl->assign('APP', $app_strings);
        insert_popup_header($theme);
        //output header

        echo "<table width='100%' cellpadding='0' cellspacing='0'><tr><td>";
        $focus_mod_strings = return_module_language($current_language, $focus->module_dir);
        echo get_module_title($focus->module_dir, translate('LBL_MODULE_NAME', $focus->module_dir) . ": " . $focus->name, false);
        echo "</td><td align='right' class='moduleTitle'>";
        echo "<A href='javascript:print();' class='utilsLink'><img src='" . $image_path . "print.gif' width='13' height='13' alt='" . $app_strings['LNK_PRINT'] . "' border='0' align='absmiddle'></a>&nbsp;<A href='javascript:print();' class='utilsLink'>" . $app_strings['LNK_PRINT'] . "</A>\n";
        echo "</td></tr></table>";
        $oddRow = true;
        $fields = '';
        $start_tag = "<table><tr><td class=\"listViewPaginationLinkS1\">";
        $end_tag = "</td></tr></table>";


        $n = 0;
        echo "<table width='100%' cellpadding='0' cellspacing='0' ><tr><td><h3>Normal Line</h3></td>";
        foreach ($norm_line_list as $v) {
            foreach ($v as $t) {

                $activity_fields = array(
                    'PROFILE' => $norm_line_list['profiletype'][$n],
                    'LINE' => $norm_line_list['line'][$n],
                    'CATEGORY' => $norm_line_list['cat'][$n],
                    'PRICECODE' => $norm_line_list['pc'][$n],
                    'PF' => $norm_line_list['pf'][$n],
                    'MULT' => $norm_line_list['mult'][$n],
                    'EXPIREDATE' => $norm_line_list['expire_date'][$n],
                    'REVIEWDATE' => $norm_line_list['review_date'][$n],
                    'EFFECTIVEDATE' => $norm_line_list['effective_date'][$n],
                    'ACCEL' => $norm_line_list['accel'][$n],
                    'ROUND' => $norm_line_list['round'][$n],
                    'STRATEGIC' => $norm_line_list['strategic'][$n]
                );

                if ($norm_line_list['profiletype'][$n] != null) {
                    $n = $n + 1;

                    $xtpl->assign("ACTIVITY", $activity_fields);
                    if($norm_line_list['typeRow'][$n] == 'exception'){
                        $xtpl->assign("FONT_COLOR", 'color:green;');
                    }else{
                         $xtpl->assign("FONT_COLOR", '');
                    }    
                    if ($oddRow) {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'oddListRow');
                        $xtpl->assign("BG_COLOR", '#CCCCCC');
                    } else {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'evenListRow');
                        $xtpl->assign("BG_COLOR", $even_bg);
                    }
                    $oddRow = !$oddRow;
                    $xtpl->parse("normalline.row");
                    // Put the rows in.
                }//end if
            }//end foreach
        }//end for each

        $xtpl->parse("normalline");
        $xtpl->out("normalline");
        
        insert_popup_footer();
        $n = 0;

        echo "<table width='100%' cellpadding='0' cellspacing='0' ><tr><td><h3>Stock Line</h3></td>";
        foreach ($stock_line_list as $v) {
            foreach ($v as $t) {
                $activity_fields = array(
                    'PROFILE' => $stock_line_list['profiletype'][$n],
                    'LINE' => $stock_line_list['line'][$n],
                    'CATEGORY' => $stock_line_list['cat'][$n],
                    'PRICECODE' => $stock_line_list['pc'][$n],
                    'PF' => $stock_line_list['pf'][$n],
                    'MULT' => $stock_line_list['mult'][$n],
                    'EXPIREDATE' => $stock_line_list['expire_date'][$n],
                    'REVIEWDATE' => $stock_line_list['review_date'][$n],
                    'EFFECTIVEDATE' => $stock_line_list['effective_date'][$n],
                    'ACCEL' => $stock_line_list['accel'][$n],
                    'ROUND' => $stock_line_list['round'][$n],
                    'STRATEGIC' => $stock_line_list['strategic'][$n]
                );

                if ($stock_line_list['profiletype'][$n] != null) {
                    $n = $n + 1;

                    $xtpl->assign("ACTIVITY", $activity_fields);
                    if($stock_line_list['typeRow'][$n] == 'exception'){
                        $xtpl->assign("FONT_COLOR", 'color:green;');
                    }else{
                         $xtpl->assign("FONT_COLOR", '');
                    }    
                    if ($oddRow) {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'oddListRow');
                        $xtpl->assign("BG_COLOR", '#CCCCCC');
                    } else {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'evenListRow');
                        $xtpl->assign("BG_COLOR", $even_bg);
                    }
                    $oddRow = !$oddRow;
                    $xtpl->parse("stockline.row");
                    // Put the rows in.
                }//end if
            }//end foreach
        }//end for each

        $xtpl->parse("stockline");
        $xtpl->out("stockline");
        insert_popup_footer();
        
        $n = 0;
        echo "<table width='100%' cellpadding='0' cellspacing='0' ><tr><td><h3>Normal Part</h3></td>";
        foreach ($norm_prod_list as $v) {
            foreach ($v as $t) {

                $activity_fields = array(
                    'PROFILE' => $norm_prod_list['profiletype'][$n],
                    'PARTNO' => $norm_prod_list['partno'][$n],
                    'PF' => $norm_prod_list['pf'][$n],
                    'MULT' => $norm_prod_list['mult'][$n],
                    'EXPIREDATE' => $norm_prod_list['expire_date'][$n],
                    'EFFECTIVEDATE' => $norm_prod_list['effective_date'][$n],
                    'REVIEWDATE' => $norm_prod_list['review_date'][$n],
                    'PRICE' => $norm_prod_list['price'][$n],
                    'BASE' => $norm_prod_list['basetype'][$n],
                );

                if ($norm_prod_list['profiletype'][$n] != null) {
                    $n = $n + 1;

                    $xtpl->assign("ACTIVITY", $activity_fields);
                    if($norm_prod_list['typeRow'][$n] == 'exception'){
                        $xtpl->assign("FONT_COLOR", 'color:green;');
                    }else{
                         $xtpl->assign("FONT_COLOR", '');
                    } 
                    if ($oddRow) {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'oddListRow');
                        $xtpl->assign("BG_COLOR", '#CCCCCC');
                    } else {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'evenListRow');
                        $xtpl->assign("BG_COLOR", $even_bg);
                    }
                    $oddRow = !$oddRow;
                    $xtpl->parse("normalprod.row");
                    // Put the rows in.
                }//end if
            }//end foreach
        }//end for each

        $xtpl->parse("normalprod");
        $xtpl->out("normalprod");
        insert_popup_footer();
        
        $n = 0;
        echo "<table width='100%' cellpadding='0' cellspacing='0' ><tr><td><h3>Stock Part</h3></td>";
        foreach ($stock_prod_list as $v) {
            foreach ($v as $t) {

                $activity_fields = array(
                    'PROFILE' => $stock_line_list['profiletype'][$n],
                    'PARTNO' => $stock_line_list['partno'][$n],
                    'PF' => $stock_line_list['pf'][$n],
                    'MULT' => $stock_line_list['mult'][$n],
                    'EXPIREDATE' => $stock_line_list['expire_date'][$n],
                    'EFFECTIVEDATE' => $stock_line_list['effective_date'][$n],
                    'REVIEWDATE' => $stock_line_list['review_date'][$n],
                    'PRICE' => $stock_line_list['price'][$n],
                    'BASE' => $stock_line_list['basetype'][$n],
                );

                if ($stock_line_list['profiletype'][$n] != null) {
                    $n = $n + 1;

                    $xtpl->assign("ACTIVITY", $activity_fields);
                    if($stock_line_list['typeRow'][$n] == 'exception'){
                        $xtpl->assign("FONT_COLOR", 'color:green;');
                    }else{
                         $xtpl->assign("FONT_COLOR", '');
                    } 
                    if ($oddRow) {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'oddListRow');
                        $xtpl->assign("BG_COLOR", '#CCCCCC');
                    } else {
                        //todo move to themes
                        $xtpl->assign("ROW_COLOR", 'evenListRow');
                        $xtpl->assign("BG_COLOR", $even_bg);
                    }
                    $oddRow = !$oddRow;
                    $xtpl->parse("stockprod.row");
                    // Put the rows in.
                }//end if
            }//end foreach
        }//end for each

        $xtpl->parse("stockprod");
        $xtpl->out("stockprod");
        insert_popup_footer();
    }

}

// end of class Popup_Picker
?>
