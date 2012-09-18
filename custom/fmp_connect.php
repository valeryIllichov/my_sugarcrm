<?php
if (!defined('sugarEntry') || !'sugarEntry') die('Not A Valid Entry Point');

class fmp_connect{

	public function func_fmp_connect(&$bean, $event, $arguments){
	require_once('custom/Mailer.php');
	$json = getJSONobj();
	$fieldName = "status";
	$status = $json->encode($bean->$fieldName);
	$fieldName = "assigned_user_name";
	$au = $json->encode($bean->$fieldName);
 	//check assigned_user = fmp_connect then send mail
	if ($au == '"fmpconnect"' && $status == '"Assigned"'){
		$subject = "type = " . $bean->type;
		//$subject = "New Customer Connection:";
		$body = $bean->description;
		$to['group'] = 'l.hertweck@fmpco.com';
		sendSugarPHPMail($to,$subject,$body);
			
	}

}
    }


?>
