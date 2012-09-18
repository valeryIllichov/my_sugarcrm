<?php

//ob_start();
//
require_once("FMPSales.php");
//session_save_path('/var/SugarCRM/session');

FMPSales::initialize521();
//print_r($_SESSION);
//exit;
require_once("JSON.php");






if ($_GET['tab'] == 'salessummary') {

//print_r(session_id());
//print_r($_SESSION);
//exit;

    $formstring = "<form id='salessummary'  onSubmit='SUGAR.mySugar.save521config(this);'>
        <input value='salessummary' type='hidden' name='tab'>
        
      <table>
            <tr>
                <td>
                    <input type='checkbox' name='fields[mtd_sales]' " . (!isset($_SESSION['display_table_settings']['salessummary']) || in_array('mtd_sales', $_SESSION['display_table_settings']['salessummary']) ? 'checked' : '') . ">MTD Sales<br />
                    <input type='checkbox' name='fields[mtd_gp]' " . (!isset($_SESSION['display_table_settings']['salessummary']) || in_array('mtd_gp', $_SESSION['display_table_settings']['salessummary']) ? 'checked' : '') . ">MTD GP$<br />
                    <input type='checkbox' name='fields[mtd_gpp]' " . (!isset($_SESSION['display_table_settings']['salessummary']) || in_array('mtd_gpp', $_SESSION['display_table_settings']['salessummary']) ? 'checked' : '') . ">MTD GP%<br />
                    <input type='checkbox' name='fields[mtd_sales_budget]' " . (!isset($_SESSION['display_table_settings']['salessummary']) || in_array('mtd_sales_budget', $_SESSION['display_table_settings']['salessummary']) ? 'checked' : '') . ">MTD Sales Budget<br />
                    <input type='checkbox' name='fields[mtd_proj]' " . (!isset($_SESSION['display_table_settings']['salessummary']) || in_array('mtd_proj', $_SESSION['display_table_settings']['salessummary']) ? 'checked' : '') . ">MTD Projected<br />
                    <input type='checkbox' name='fields[ly_sales]' " . (!isset($_SESSION['display_table_settings']['salessummary']) || in_array('ly_sales', $_SESSION['display_table_settings']['salessummary']) ? 'checked' : '') . ">LY Sales<br />
                </td>
                <td>
                    <input type='checkbox' name='fields[ytd_sales]' " . (!isset($_SESSION['display_table_settings']['salessummary']) || in_array('ytd_sales', $_SESSION['display_table_settings']['salessummary']) ? 'checked' : '') . ">YTD Sales<br />
                    <input type='checkbox' name='fields[ytd_gp]' " . (!isset($_SESSION['display_table_settings']['salessummary']) || in_array('ytd_gp', $_SESSION['display_table_settings']['salessummary']) ? 'checked' : '') . ">YTD GP$<br />
                    <input type='checkbox' name='fields[ytd_gpp]' " . (!isset($_SESSION['display_table_settings']['salessummary']) || in_array('ytd_gpp', $_SESSION['display_table_settings']['salessummary']) ? 'checked' : '') . ">YTD GP%<br />
                    <input type='checkbox' name='fields[ytd_sales_budget]' " . (!isset($_SESSION['display_table_settings']['salessummary']) || in_array('ytd_sales_budget', $_SESSION['display_table_settings']['salessummary']) ? 'checked' : '') . ">YTD Sales Budget<br />
                    <input type='checkbox' name='fields[ytd_proj]' " . (!isset($_SESSION['display_table_settings']['salessummary']) || in_array('ytd_proj', $_SESSION['display_table_settings']['salessummary']) ? 'checked' : '') . ">YTD Projected<br />
                    <input type='checkbox' name='fields[ly_gpp]' " . (!isset($_SESSION['display_table_settings']['salessummary']) || in_array('ly_gpp', $_SESSION['display_table_settings']['salessummary']) ? 'checked' : '') . ">LY GP%<br />
                </td>
            </tr>
            
      </table>
      <input type='submit' value='Save' class='button'>
      </form>";

	header('Content-type: application/json');
	$json = new Services_JSON();
    echo $json->encode(array('header' => 'Display/Hide fields for summery sales', 'body' => $formstring));
}else if($_GET['tab'] == 'customerar') {
//print_r(session_id());
//print_r($_SESSION);
//exit;

	$fields[] = array("key" => "slsm", "label" => "Slsm");
	$fields[] = array("key" => "custno", "label" => "CustNo");
	$fields[] = array("key" => "custname", "label" => "Name");
	$fields[] = array("key" => "credit_code", "label" => "Credit Code");
	$fields[] = array("key" => "future", "label" => "Future");
	$fields[] = array("key" => "current", "label" => "Current");
	$fields[] = array("key" => "ar30_60", "label" => "30 - 60");
	$fields[] = array("key" => "ar60_90", "label" => "60 - 90");
	$fields[] = array("key" => "over_90", "label" => "90+");
	$fields[] = array("key" => "aarbal", "label" => "Balance");
	$fields[] = array("key" => "credit_limit", "label" => "Credit Limit");
	$fields[] = array("key" => "perc_balance2credit_limit", "label" => "% of Balance to Credit Limit");




    $formstring = "<form id='customerar'  onSubmit='SUGAR.mySugar.save521config(this);'>
        <input value='customerar' type='hidden' name='tab'>
        
      <table>
            <tr>
                <td>";
	foreach($fields as $field) {
		$formstring .= "<input type='checkbox' name='fields[" . $field['key'] . "]' " . (!isset($_SESSION['display_table_settings']['customerar']) || in_array($field['key'], $_SESSION['display_table_settings']['customerar']) ? 'checked' : '') . ">" . $field['label'] . "<br />";
	}
		
    $formstring .= "<select name='result' value='".$_SESSION['display_table_settings']['customerar_result']."'>";
   
    for($i=20; $i<=120;($i+=20)) {
	$formstring .= "<option value='" . $i . "' ".(($_SESSION['display_table_settings']['customerar_result'] == $i) ? 'selected': '').">" . $i . "</option>";
    }

    $formstring .= "</select>
		</td>
            </tr>
            
      </table>
      <input type='submit' value='Save' class='button'>
      </form>";

	header('Content-type: application/json');
	$json = new Services_JSON();
    echo $json->encode(array('header' => 'Display/Hide fields for customerar', 'body' => $formstring));

}else if($_GET['tab'] == 'customersales') {
//print_r(session_id());
//print_r($_SESSION);
//exit;

					$fields[] = array("key" => "slsm", "label" => "Slsm");
					$fields[] = array("key" => "custno", "label" => "CustNo");
					$fields[] = array("key" => "custname", "label" => "Name");

					$fields[] = array("key" => "ytd_invoices", "label" => "YTD # of transactions");
					$fields[] = array("key" => "mtd_invoices", "label" => "MTD # of transactions");
					$fields[] = array("key" => "wtd_invoices", "label" => "WTD # of transactions");

					$fields[] = array("key" => "ytd_av_per_trans", "label" => "YTD Dollar average per transaction");
					$fields[] = array("key" => "mtd_av_per_trans", "label" => "MTD Dollar average per transaction");

					$fields[] = array("key" => "num_mtdreturns_new", "label" => "Number of MTD Returns (New)");
					$fields[] = array("key" => "mtdreturns_perc_new", "label" => "MTD Returns % (New)");
					$fields[] = array("key" => "num_mtdreturns_def", "label" => "Number of MTD Returns (Defective)");
					$fields[] = array("key" => "mtdreturns_perc_def", "label" => "MTD Returns % (Defective)");
					$fields[] = array("key" => "num_mtdreturns_ovr", "label" => "Number of MTD Returns (Overall)");
					$fields[] = array("key" => "mtdreturns_perc_ovr", "label" => "MTD Returns % (Overall)");
					$fields[] = array("key" => "mtdproj_sls", "label" => "MTD Projected Sales");
					$fields[] = array("key" => "mtdproj_gp", "label" => "MTD Projected GP$");
					$fields[] = array("key" => "mtdproj_gpp", "label" => "MTD Projected GP%");
					$fields[] = array("key" => "mtd_sales", "label" => "MTD Sales");
					$fields[] = array("key" => "mtd_gp", "label" => "MTD GP");
					$fields[] = array("key" => "mtd_gpp", "label" => "MTD GP%");

					$fields[] = array("key" => "lm_sales", "label" => "LM Sales");
					$fields[] = array("key" => "lm_gp", "label" => "LM GP$");
					$fields[] = array("key" => "lm_gpp", "label" => "LM GP%");
					$fields[] = array("key" => "pm_sales", "label" => "PM Sales");
					$fields[] = array("key" => "pm_gp", "label" => "PM GP$");
					$fields[] = array("key" => "pm_gpp", "label" => "PM GP%");
					$fields[] = array("key" => "ytd_sales", "label" => "YTD Sales");
					$fields[] = array("key" => "ytd_gp", "label" => "YTD GP");
					$fields[] = array("key" => "ytd_gpp", "label" => "YTD GP%");

					$fields[] = array("key" => "ly_sales", "label" => "LY Sales");
					$fields[] = array("key" => "ly_gp", "label" => "LY GP");
					$fields[] = array("key" => "ly_gpp", "label" => "LY GP%");




    $formstring = "<form id='customersales'  onSubmit='SUGAR.mySugar.save521config(this);'>
        <input value='customersales' type='hidden' name='tab'>
        
      <table>
            <tr>
                <td>";
	foreach($fields as $field) {
		$formstring .= "<input type='checkbox' name='fields[" . $field['key'] . "]' " . (!isset($_SESSION['display_table_settings']['customersales']) || in_array($field['key'], $_SESSION['display_table_settings']['customersales']) ? 'checked' : '') . ">" . $field['label'] . "<br />";
	}
		
    $formstring .= "<select name='result'>";
   
    for($i=20; $i<=120;($i+=20)) {
	$formstring .= "<option value='" . $i . "' ".(($_SESSION['display_table_settings']['customersales_result'] == $i) ? 'selected': '').">" . $i . "</option>";
    }

    $formstring .= "</select>
		</td>
            </tr>
            
      </table>
      <input type='submit' value='Save' class='button'>
      </form>";

	header('Content-type: application/json');
	$json = new Services_JSON();
    echo $json->encode(array('header' => 'Display/Hide fields for customersales', 'body' => $formstring));

}else if($_GET['tab'] == 'customersalescomparison') {
//print_r(session_id());
//print_r($_SESSION);
//exit;

	$fields[] = array("key" => "slsm", "label" => "Slsm");
	$fields[] = array("key" => "custno", "label" => "CustNo");
	$fields[] = array("key" => "custname", "label" => "Name");

	$fields[] = array("key" => "mtd_vs_mtd_proj_sales", "label" => "MTD vs MTD Proj Sales");
	$fields[] = array("key" => "mtd_vs_mtd_proj_gp", "label" => "MTD vs MTD Proj GP");
	$fields[] = array("key" => "mtd_vs_mtd_proj_gp_percent", "label" => "MTD vs MTD Proj GP%");

	$fields[] = array("key" => "mtd_proj_sales_vs_lm_sales", "label" => "MTD Proj Sales vs LM Sales");
	$fields[] = array("key" => "mtd_proj_gp_vs_lm_gp", "label" => "MTD Proj GP vs LM GP");
	$fields[] = array("key" => "mtd_proj_gp_percent_vs_lm_gp_percent", "label" => "MTD Proj GP% vs LM GP%");

	$fields[] = array("key" => "mtd_vs_lm_sales", "label" => "MTD vs LM Sales");
	$fields[] = array("key" => "mtd_vs_lm_gp", "label" => "MTD vs LM GP");
	$fields[] = array("key" => "mtd_vs_lm_gp_percent", "label" => "MTD vs LM GP%");

	$fields[] = array("key" => "mtd_vs_lytm_sales", "label" => "MTD vs LYTM Sales");
	$fields[] = array("key" => "mtd_vs_lytm_gp", "label" => "MTD vs LYTM GP");
	$fields[] = array("key" => "mtd_vs_lytm_gp_percent", "label" => "MTD vs LYTM GP%");

	$fields[] = array("key" => "ytd_vs_lytd_sales", "label" => "YTD vs LYTD Sales");
	$fields[] = array("key" => "ytd_vs_lytd_gp", "label" => "YTD vs LYTD GP");
	$fields[] = array("key" => "ytd_vs_lytd_gp_percent", "label" => "YTD vs LYTD GP%");
	
	$fields[] = array("key" => "projected_vs_ly_sales", "label" => "Proj vs LY Sales");
	$fields[] = array("key" => "projected_vs_ly_gp", "label" => "Proj vs LY GP");
	$fields[] = array("key" => "projected_vs_ly_gp_percent", "label" => "Proj vs LY GP%");
				




    $formstring = "<form id='customersalescomparison'  onSubmit='SUGAR.mySugar.save521config(this);'>
        <input value='customersalescomparison' type='hidden' name='tab'>
        
      <table>
            <tr>
                <td>";
	foreach($fields as $field) {
		$formstring .= "<input type='checkbox' name='fields[" . $field['key'] . "]' " . (!isset($_SESSION['display_table_settings']['customersalescomparison']) || in_array($field['key'], $_SESSION['display_table_settings']['customersalescomparison']) ? 'checked' : '') . ">" . $field['label'] . "<br />";
	}
		
    $formstring .= "<select name='result' value='".$_SESSION['display_table_settings']['customerar_result']."'>";
   
    for($i=20; $i<=120;($i+=20)) {
	$formstring .= "<option value='" . $i . "' ".(($_SESSION['display_table_settings']['customersalescomparison_result'] == $i) ? 'selected': '').">" . $i . "</option>";
    }

    $formstring .= "</select>
		</td>
            </tr>
            
      </table>
      <input type='submit' value='Save' class='button'>
      </form>";

	header('Content-type: application/json');
	$json = new Services_JSON();
    echo $json->encode(array('header' => 'Display/Hide fields for customersalescomparison', 'body' => $formstring));

}else if($_GET['tab'] == 'customerbudgetcomparison') {
//print_r(session_id());
//print_r($_SESSION);
//exit;

	$fields[] = array("key" => "slsm", "label" => "Slsm");
	$fields[] = array("key" => "custno", "label" => "CustNo");
	$fields[] = array("key" => "custname", "label" => "Name");

	$fields[] = array("key" => "mtd_vs_budget_sales", "label" => "MTD vs Budget Sales");
	$fields[] = array("key" => "mtd_vs_budget_gp", "label" => "MTD vs Budget GP");
	$fields[] = array("key" => "mtd_vs_budget_gp_percent", "label" => "MTD vs Budget GP%");

	$fields[] = array("key" => "ytd_vs_budget_sales", "label" => "YTD vs Budget Sales");
	$fields[] = array("key" => "ytd_vs_budget_gp", "label" => "YTD vs Budget GP");
	$fields[] = array("key" => "ytd_vs_budget_gp_percent", "label" => "YTD vs Budget GP%");
	
	$fields[] = array("key" => "projected_vs_budget_sales", "label" => "Proj vs Budget Sales");
	$fields[] = array("key" => "projected_vs_budget_gp", "label" => "Proj vs Budget GP");
	$fields[] = array("key" => "projected_vs_budget_gp_percent", "label" => "Proj vs Budget GP%");
				
				




    $formstring = "<form id='customerbudgetcomparison'  onSubmit='SUGAR.mySugar.save521config(this);'>
        <input value='customerbudgetcomparison' type='hidden' name='tab'>
        
      <table>
            <tr>
                <td>";
	foreach($fields as $field) {
		$formstring .= "<input type='checkbox' name='fields[" . $field['key'] . "]' " . (!isset($_SESSION['display_table_settings']['customerbudgetcomparison']) || in_array($field['key'], $_SESSION['display_table_settings']['customerbudgetcomparison']) ? 'checked' : '') . ">" . $field['label'] . "<br />";
	}
		
    $formstring .= "<select name='result' value='".$_SESSION['display_table_settings']['customerar_result']."'>";
   
    for($i=20; $i<=120;($i+=20)) {
	$formstring .= "<option value='" . $i . "' ".(($_SESSION['display_table_settings']['customerbudgetcomparison_result'] == $i) ? 'selected': '').">" . $i . "</option>";
    }

    $formstring .= "</select>
		</td>
            </tr>
            
      </table>
      <input type='submit' value='Save' class='button'>
      </form>";

	header('Content-type: application/json');
	$json = new Services_JSON();
    echo $json->encode(array('header' => 'Display/Hide fields for customerbudgetcomparison', 'body' => $formstring));

}


if ($_POST) {
    switch ($_POST['tab']) {
        case 'salessummary':

            foreach ($_POST['fields'] as $key => $value) {
                $display_fields[] = $key;
            }

            $_SESSION['display_table_settings']['salessummary'] = $display_fields;
	    
		//sleep(5);
		//print_r(session_id());
		session_write_close();

		//exit;
            break;

        case 'customerar':

            foreach ($_POST['fields'] as $key => $value) {
                $display_fields[] = $key;
            }

            $_SESSION['display_table_settings']['customerar'] = $display_fields;
	    $_SESSION['display_table_settings']['customerar_result'] = $_POST['result'];
		//sleep(5);
		//print_r(session_id());
		session_write_close();

		//exit;
            break;

        case 'customersales':

            foreach ($_POST['fields'] as $key => $value) {
                $display_fields[] = $key;
            }

            $_SESSION['display_table_settings']['customersales'] = $display_fields;
	    $_SESSION['display_table_settings']['customersales_result'] = $_POST['result'];
		//sleep(5);
		//print_r(session_id());
		session_write_close();

		//exit;
            break;

        case 'customersalescomparison':

            foreach ($_POST['fields'] as $key => $value) {
                $display_fields[] = $key;
            }

            $_SESSION['display_table_settings']['customersalescomparison'] = $display_fields;
	    $_SESSION['display_table_settings']['customersalescomparison_result'] = $_POST['result'];
		//sleep(5);
		//print_r(session_id());
		session_write_close();

		//exit;
            break;

        case 'customerbudgetcomparison':

            foreach ($_POST['fields'] as $key => $value) {
                $display_fields[] = $key;
            }

            $_SESSION['display_table_settings']['customerbudgetcomparison'] = $display_fields;
	    $_SESSION['display_table_settings']['customerbudgetcomparison_result'] = $_POST['result'];
		//sleep(5);
		//print_r(session_id());
		session_write_close();

		//exit;
            break;

        default:
            break;
    }
}
