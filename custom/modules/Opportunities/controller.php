<?php
/*
 * custom controller for Opp dashket post request
 * Memet
 * ItCrimea 2011
 * memet@itcrimea.com
 */
class OpportunitiesController extends SugarController
{
    public function action_sendData()
    {
        $this->view = 'oppdatacreate';
    }
    public function action_processWhere() {
            $this->view = 'pbssparamsforquery';
    }

    public function action_getOpportunities()
    {
	$focus = new Opportunity();
	
	$opportunity = $focus->db->query("SELECT opp.name, opp.id FROM opportunities opp WHERE opp.name LIKE '".$_GET['q']."%'");
	while($row = $focus->db->fetchByAssoc($opportunity)) {
		$opport[] = array($row['name'],$row['id']);
	}    	
	
	$json = getJSONobj();
	echo $json->encode($opport);

	exit;
    }

    public function action_getOpportunitiesPL() {
	$opport = array();
    	$focus = new Opportunity();
	$opportunitypl = $focus->db->query("SELECT opl.pline_id AS pid, opl.pcat_id AS pcat, opl.pcode_id AS pcode FROM opportunities_product_line opl WHERE opl.opportunity_id='" . $_GET['record_id'] . "'");

	while($row = $focus->db->fetchByAssoc($opportunitypl)) {
		$opport[] = $row;
	}
	
	$json = getJSONobj();
	echo $json->encode($opport);

	exit; 
    }
    
    public function action_getAccount(){
        $accort = array();
    	$focus = new Opportunity();
	$accountpl = $focus->db->query("SELECT accopp.account_id, acc.custno_c FROM accounts_opportunities  accopp LEFT JOIN accounts acc ON (accopp.account_id=acc.id) WHERE opportunity_id='" . $_GET['opp_id'] . "'");

	while($row = $focus->db->fetchByAssoc($accountpl)) {
		$accort[] = $row;
	}
	
	$json = getJSONobj();
	echo $json->encode($accort);

	exit;
    }
}
