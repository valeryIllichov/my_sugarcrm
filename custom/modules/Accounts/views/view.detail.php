<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

/***********************************************************************
2011-01-10  Lisa   Created: Overides Edit View Customer panel display

************************************************************************/


require_once('include/MVC/View/views/view.detail.php');
class AccountsViewDetail extends ViewDetail {
	function AccountsViewDetail(){
		parent::ViewDetail();
	}	
	function display() {
		parent::display();
		
		//Code to hide or display the panels when first viewing the record
		$prePop = 'document.getElementById(\'dealertype_c\').onchange()';
		
		$fieldName = 'dealertype_c';
		$json = getJSONobj();
		$value =$json->encode($this->bean->$fieldName);
		$e=$this->dv->defs['panels'];
		//Hides all panels  
		foreach($e as $panel_label=>$panel_data){
			if($panel_label!=='default' && $panel_label !== 'lbl_panel2' && $panel_label !== 'lbl_panel3' && $value!=='"9"'){
				print'<script>document.getElementById(\''.$panel_label.'\'.toUpperCase()).style.display = \'none\';</script>';}
			}
		//stocking items		
                print'<script>document.getElementById(\'lbl_panel6\'.toUpperCase()).style.display = \'block\';</script>';
	
	print'<script>document.getElementById(\'lbl_panel8\'.toUpperCase()).style.display = \'block\';</script>';
	//displays panels based on dealertype_c.value
	$js=<<<EOQ
		<script>
		var val = $value;
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
		//		document.getElementById('LBL_PANEL8').style.display='none';
			}
		</script>
EOQ;
echo $js;
	}
}
?>



