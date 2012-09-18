<?php
/*
 * custom controller for Cases
 * Valery
 * sfdev 2012
 * valery@sfdev.com
 */
class CasesController extends SugarController
{
     public function action_getCasesCustomFields(){
        $accort = array();
    	$focus = new aCase();
        $query = "SELECT c.subject_c sms, c.connection_type_c psc FROM cases c WHERE c.case_number='" . trim($_POST['case_number']) . "'";
	$casefields = $focus->db->query($query);

	$row = $focus->db->fetchByAssoc($casefields);
	
	$json = getJSONobj();
	echo $json->encode($row);

	exit;
    }
    
}
