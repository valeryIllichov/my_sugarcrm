<?php
ob_start();
require_once("FMPSales.php");

$the_tab = $_REQUEST['tab'];
$filters_str = $_REQUEST['filters'];
$fields_arr = strlen($_REQUEST['fields']) > 0 ? explode("|", $_REQUEST['fields']) : $fields_arr = null;
$fields = array();
foreach($fields_arr as $field_str){
    $label = explode("&", $field_str);
    $key = explode("-", $label[1]);
    $fields[$key[2]] = $label[0];
}

parse_str($filters_str);
$startIndex=0;
$results=null;
$sort='custno';
$sort_dir='asc';
$selectMethod=null;

strlen($location) > 0 ? $location=explode(';',$region) : $location=null;
strlen($region) > 0 ? $region=explode(';',$region) : $region=null;
strlen($slsm) > 0 ? $slsm=explode(";",$slsm) : $slsm=null;
strlen($dealertype) > 0 ? $dealertype=explode(';',$dealertype) : $dealertype=null;
strlen($account) > 0 ? $account=explode(';',$account) : $account=null;

switch ($the_tab) {
    case 'customerar':
        if(empty($fields)){
           $fields = array("slsm" =>"Slsm", 
                "custno" =>"CustNo" , 
                "custname" =>"Name", 
                "future" =>"Future", 
                "ar30_60" =>"30 - 60", 
                "ar60_90" =>"60 - 90", 
                "over_90" =>"90+",
                "aarbal" =>"Balance");      
        }
        $content=FMPSales::getCustomerAR($startIndex, $results, $sort, $sort_dir, $selectMethod, $location, $region, $slsm, $dealertype,$account,true,$fields);
        break;
    case 'customersales':
        if(empty($fields)){
           $fields = array("slsm" =>"Slsm", 
               "custno" =>"CustNo" , 
               "custname" =>"Name", 
               "mtd_sales" =>"MTD Sales", 
               "mtd_gp" =>"MTD GP", 
               "mtd_gpp" =>"MTD GP%", 
               "ytd_sales" =>"YTD Sales", 
               "ytd_gp" =>"YTD GP", 
               "ytd_gpp" =>"YTD GP%", 
               "ly_sales" =>"LY Sales", 
               "ly_gp" =>"LY GP", 
               "ly_gpp" =>"LY GP%");      
        }
        $content=FMPSales::getCustomerSales($startIndex, $results, $sort, $sort_dir, $selectMethod, $location, $region, $slsm, $dealertype, '',$account,true,$fields);
        break;
    case 'customersalesnonoe':
        if(empty($fields)){
           $fields = array("slsm" =>"Slsm", 
               "custno" =>"CustNo" , 
               "custname" =>"Name", 
               "mtd_sales" =>"MTD Sales", 
               "mtd_gp" =>"MTD GP", 
               "mtd_gpp" =>"MTD GP%", 
               "ytd_sales" =>"YTD Sales", 
               "ytd_gp" =>"YTD GP", 
               "ytd_gpp" =>"YTD GP%", 
               "ly_sales" =>"LY Sales", 
               "ly_gp" =>"LY GP", 
               "ly_gpp" =>"LY GP%");      
        }
        $content=FMPSales::getCustomerSales($startIndex, $results, $sort, $sort_dir, $selectMethod, $location, $region, $slsm, $dealertype, 'nonoe',$account,true,$fields);
        break;
    case 'customersalesundercar':
        if(empty($fields)){
           $fields = array("slsm" =>"Slsm", 
               "custno" =>"CustNo" , 
               "custname" =>"Name", 
               "mtd_sales" =>"MTD Sales", 
               "mtd_gp" =>"MTD GP", 
               "mtd_gpp" =>"MTD GP%", 
               "ytd_sales" =>"YTD Sales", 
               "ytd_gp" =>"YTD GP", 
               "ytd_gpp" =>"YTD GP%", 
               "ly_sales" =>"LY Sales", 
               "ly_gp" =>"LY GP", 
               "ly_gpp" =>"LY GP%");      
        }
        $content=FMPSales::getCustomerSales($startIndex, $results, $sort, $sort_dir, $selectMethod, $location, $region, $slsm, $dealertype, 'undercar',$account,true,$fields);
        break;
    case 'customersalescomparison':
        if(empty($fields)){
           $fields = array("slsm" =>"Slsm", 
               "custno" =>"CustNo" , 
               "custname" =>"Name", 
               "mtd_vs_lm_sales" =>"MTD vs. LM Sales", 
               "mtd_vs_lm_gp" =>"MTD vs. LM GP", 
               "mtd_vs_lm_gp_percent" =>"MTD vs. LM GP%", 
               
               "mtd_vs_lytm_sales" =>"MTD vs. LYTM Sales", 
               "mtd_vs_lytm_gp" =>"MTD vs. LYTM GP", 
               "mtd_vs_lytm_gp_percent" =>"MTD vs. LYTM GP%", 
               
               "ytd_vs_lytd_sales" =>"YTD vs. LYTD Sales", 
               "ytd_vs_lytd_gp" =>"YTD vs. LYTD GP", 
               "ytd_vs_lytd_gp_percent" =>"YTD vs. LYTD GP%", 
               
               "projected_vs_ly_sales" =>"Projection vs. LY Sales", 
               "projected_vs_ly_gp" =>"Projection vs. LY GP", 
               "projected_vs_ly_gp_percent" =>"Projection vs. LY GP%");      
        }
        $content=FMPSales::getCustomerSalesComparison($startIndex, $results, $sort, $sort_dir, $selectMethod, $location, $region, $slsm, $dealertype,$account,true,$fields);
        break;
    case 'customerbudgetcomparison':
       if(empty($fields)){
           $fields = array("slsm" =>"Slsm", 
               "custno" =>"CustNo" , 
               "custname" =>"Name", 
               
               "mtd_vs_budget_sales" =>"MTD vs. Budget Sales", 
               "mtd_vs_budget_gp" =>"MTD vs. Budget GP", 
               "mtd_vs_budget_gp_percent" =>"MTD vs. Budget GP%", 
               
               "ytd_vs_budget_sales" =>"YTD vs. Budget Sales", 
               "ytd_vs_budget_gp" =>"YTD vs. Budget GP", 
               "ytd_vs_budget_gp_percent" =>"YTD vs. Budget GP%", 
               
               "projected_vs_budget_sales" =>"Projection vs. Budget Sales", 
               "projected_vs_budget_gp" =>"Projection vs. Budget GP", 
               "projected_vs_budget_gp_percent" =>"Projection vs. Budget GP%");      
        }                      
         $content=FMPSales::getCustomerBudgetComparison($startIndex, $results, $sort, $sort_dir, $selectMethod, $location, $region, $slsm, $dealertype,$account,true,$fields);
        break;
    case 'customerreturns':
       if(empty($fields)){
           $fields = array("slsm" =>"Slsm", 
               "custno" =>"CustNo" , 
               "custname" =>"Name", 
               
               "rtn_new_mtd" =>"New Returns MTD", 
               "rtn_new_mtd_precent" =>"New Returns MTD %", 
               "rtn_new_ytd" =>"New Returns YTD", 
               "rtn_new_ytd_precent" =>"New Returns YTD %", 
               
               "rtn_def_mtd" =>"Defective Returns MTD", 
               "rtn_def_mtd_precent" =>"Defective Returns MTD %", 
               "rtn_def_ytd" =>"Defective Returns YTD", 
               "rtn_def_ytd_precent" =>"Defective Returns YTD %", 
               
               "rtn_cor_mtd" =>"Core Returns MTD", 
               "rtn_cor_mtd_precent" =>"Core Returns MTD %", 
               "rtn_cor_ytd" =>"Core Returns YTD", 
               "rtn_cor_ytd_precent" =>"Core Returns YTD %", 
               
               "rtn_mtd_overall" =>"Overall Returns MTD", 
               "rtn_ytd_overall" =>"Overall Returns YTD");      
        }                      
         $content=FMPSales::getCustomerReturns($startIndex, $results, $sort, $sort_dir, $selectMethod, $location, $region, $slsm, $dealertype,$account,true,$fields);
        break;
   case 'customertransactions':
       if(empty($fields)){
           $fields = array("slsm" =>"Slsm", 
               "custno" =>"CustNo" , 
               "custname" =>"Name", 
               
               "wtd_invoices" =>"# of Transactions - WTD", 
               "mtd_invoices" =>"# of Transactions - MTD", 
               "ytd_invoices" =>"# of Transactions - YTD", 
               "mtd_av_per_trans" =>"New Returns YTD %", 
               
               "ytd_av_per_trans" =>"MTD - Avg $ per Transaction", 
               "rtn_def_mtd_precent" =>"YTD - Avg $ per Transaction");      
        }                      
         $content=FMPSales::getCustomerTransactions($startIndex, $results, $sort, $sort_dir, $selectMethod, $location, $region, $slsm, $dealertype,$account,true,$fields);
        break;
   case 'customerbudget':
       if(empty($fields)){
           $fields = array("slsm" =>"Slsm", 
               "custno" =>"CustNo" , 
               "custname" =>"Name", 
               
               "mtd_budget_sales" =>"CM Budget Sales", 
               "mtd_budget_gp" =>"CM Budget GP", 
               "mtd_budget_gpp" =>"CM Budget GP%", 
               "ytd_budget_sales" =>"CY Budget Sales", 
               "ytd_budget_gp" =>"CY Budget GP", 
               "ytd_budget_gpp" =>"CY Budget GP%");      
        }                      
         $content=FMPSales::getCustomerBudget($startIndex, $results, $sort, $sort_dir, $selectMethod, $location, $region, $slsm, $dealertype,$account,true,$fields);
        break;
    default:
        exit();
        break;
}

$filename = $the_tab;

ob_clean();
header("Pragma: cache");
header("Content-type: application/octet-stream; charset=UTF-8");
header("Content-Disposition: attachment; filename={$filename}.csv");
header("Content-transfer-encoding: binary");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
header("Cache-Control: post-check=0, pre-check=0", false );
header("Content-Length: ".strlen($content));

print $content;