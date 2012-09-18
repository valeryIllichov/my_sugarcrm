<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

/***********************************************************************
2011-01-10  Lisa   Created: Overides Edit View Customer panel display

************************************************************************/


require_once('include/MVC/View/views/view.edit.php');
class AccountsViewEdit extends ViewEdit {
	function AccountsViewEdit(){
		parent::ViewEdit();
	}	
	function display() {
		parent::display();
		//Code to hide or display the panels when first editing the record
		$prePop = 'document.getElementById(\'dealertype_c\').onchange()';
		//$custno = 'custno_c';
		$fieldName = 'dealertype_c';
		$fieldSlsm = 'slsm_c';
		$fieldReg = 'region_c';
		$cust = 'custno_c';
		$fieldLoc = 'location_c';
		$json = getJSONobj();
		$dealerType =$json->encode($this->bean->$fieldName);
		$slsm = $json->encode($this->bean->$fieldSlsm);
		$region = $json->encode($this->bean->$fieldReg);
		$loc = $json->encode($this->bean->$fieldLoc);
		$custnoValue = $json->encode($this->bean->$cust);
		$e=$this->ev->defs['panels'];
		//Hides all panels
		foreach($e as $panel_label=>$panel_data){
			if($panel_label!=='lbl_account_information' && $panel_label !== 'lbl_panel6' && $dealerType!=='"9"' && strlen($dealerType) > 2){
				print'<script>document.getElementById(\''.$panel_label.'\'.toUpperCase()).style.display = \'none\';</script>';
				}
			}
print'<script>document.getElementById(\'lbl_panel8\'.toUpperCase()).style.display = \'block\';</script>';


		//generate custno if none exist
	        if (strlen($custnoValue) <=2) {
           	     $custnoValue = 'SG' . rand(1000,99999);
		}
		
//	displays panels based on dealertype_c.value
	$js=<<<EOQ
		<script>
		var val = $dealerType;
		var slsm = $slsm;
		var reg = $region;
		var loc = $loc;
		var custVal= $custnoValue;	
	
			if(val > 0 && val <='4'){
                                //DealerInfo Panel
                                //panel1 = dealer mgmt
                                document.getElementById('LBL_PANEL1').style.display='block';
                                }
                        if(val>'4' && val<'7' || val == 0){
                                //Retailers Jobbers
                                //panel8 = stocking items
                                document.getElementById('LBL_PANEL9').style.display='block';
                                document.getElementById('LBL_PANEL8').style.display='none';
                        }
                        if(val=='7'){
                                //Aftermarket ISC
                                //panel4 = Shop mgmt
                                document.getElementById('LBL_PANEL4').style.display='block';
                        }
                        if(val=='8'){
                                //Fleet
                                //panel5 = fleet info, panel7 = vehicle type,
                                document.getElementById('LBL_PANEL5').style.display='block';
                                document.getElementById('LBL_PANEL7').style.display='block';
//                                document.getElementById('LBL_PANEL8').style.display='none';
                        }
                </script>

EOQ;
echo $js;

?>
<?
}
        }
?>

