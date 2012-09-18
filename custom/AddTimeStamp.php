<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class AddTimeStamp{
	function stamp(& $focus, $event){
	global $current_user;
	if($focus->status == 'Closed'){
		$focus->resolution .= "\nUpdated " . date("Y-m-d g:i a") . " by " . $current_user->user_name;
}else{$focus->description .= "\nUpdated " . date("Y-m-d g:i a") . " by " . $current_user->user_name;
}
/*	echo $focus->id;
 $qry = "SELECT custno_c from ACCOUNTS where id = '" . $focus->account_id . "'";
        $row = $focus->db->fetchByAssoc($qry);
        

	if($focus->status == 'Closed'){
		$focus->resolution .= "\nUpdated " . date("Y-m-d g:i a") . " by " . $current_user->user_name;
}else{$focus->description .= "\nUpdated " . date("Y-m-d g:i a") . " by " . $current_user->user_name . $qry . " " . $row['custno_c'];
}*/
	$qry = "SELECT custno_c from ACCOUNTS where id = '" . $focus->account_id . "'";
	$row = $focus->db->fetchByAssoc($qry);
	$focus->account_name_custno = $row['custno_c'];
	echo $focus->account_name_custno;	
$focus->name = $focus->connection_c;
	$qry = "INSERT into CASES_CSTM(id,cases_id, assign_user_id, up_user_id, update_datetime) values( '" . create_guid() ."','". $focus->id . "','" . $focus->assigned_user_id . "','". $current_user->id . "','" . $focus->date_modified . "')";
	$focus->db->query($qry);

}
}