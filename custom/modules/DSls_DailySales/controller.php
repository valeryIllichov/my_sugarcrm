<?php
/* 
 * custom controller for post request
 * Memet
 * ItCrimea 2011
 * memet@itcrimea.com
 */

class DSls_DailySalesController extends SugarController {
    public function action_ProcessTable()
    {
        $this->view = 'tableCreate';
    }
    public function action_getSalesSummaryCredits()
    {
        global $current_user;
        require_once("modules/Calls/Call.php");
        $focus = new Call();
          switch ($_REQUEST['iSortCol_0']) {

            case 0:
                $field = "ref";
                break;
            case 1:
                $field = "credit";
                break;
            case 2:
                $field = "slsm";
                break;
            case 3:
                $field = "rdate";
                break;

            default:
                $field = "rdate";
                break;
        }
        $like_q = "";
        if(!empty($_REQUEST['sSearch'])){
            $like_q = " AND (dssc.record_reference LIKE '%".$_REQUEST['sSearch']."%' OR dssc.slsm LIKE '%".$_REQUEST['sSearch']."%') ";
        }
        $day_Credits = "CURRENT_DATE() ";
        if(!empty($_REQUEST['dayCredits'])){
            if($_REQUEST['dayCredits'] == "current")
                $day_Credits = "CURRENT_DATE() ";
            if($_REQUEST['dayCredits'] == "previous")
                $day_Credits = "DATE_SUB(NOW(), INTERVAL 2 DAY) and dssc.record_date < CURRENT_DATE() ";
        }
        if(!empty($_REQUEST['slsm_num'])&&$_REQUEST['slsm_num'] != ""){
            $slsm = $_REQUEST['slsm_num'] != 'all' ? $_REQUEST['slsm_num'] : '';
        }else{
            $slsm = ($current_user->getPreference('daily_sales_Slsm'))? $current_user->getPreference('daily_sales_Slsm'): '';
        }
         include_once("521/FMPSales.php");

        FMPSales::initialize521();

        /* generate where criteria with everything in acl -- including custid */
        $acl_array = $_SESSION['fmp_acl'];

        $acl_access_granted = array();
        foreach ($acl_array as $acl) {

            if (!is_null($acl['location'])) {
                $acl_access_granted['location'][] = $acl['location'];
            }
            if (!is_null($acl['region'])) {
                $acl_access_granted['region'][] = $acl['region'];
            }
            if (!is_null($acl['slsm'])) {
                $acl_access_granted['slsm'][] = $acl['slsm'];
            }
        }

        $acl_granted = $acl_access_granted;
        
        $slsm = !in_array($slsm, array('undefined', 'all', '')) ? explode(";", $slsm) : null;
        
        $where = "";
        if (!is_null($slsm)) {

            if (count($slsm) == 1) {
                $slsm = $this->getSlsmBelow($_SESSION['fmp_slsm'], $slsm[0]);
            }

            if (count($acl_granted) != 0 && count($acl_granted['slsm']) > 0) {
                $where .= " AND dssc.slsm IN (" . implode(', ', $acl_granted['slsm']) . ") AND dssc.slsm IN (" . implode(', ', $slsm) . ")";
            }
        }
        $current_day_credits_sql = "select dssc.record_reference as ref,dssc.credits as credit,dssc.slsm as slsm,dssc.record_date as rdate
                                                                from dsls_sales_summary_credits dssc
                                                                where dssc.record_date >= ".$day_Credits.$where."
                                                                ".$like_q."
                                                                ORDER BY " . $field . " " . $_REQUEST['sSortDir_0'] . " LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];
        $current_day_credits_sql_count = "select count(*) as totalRecords
                                                                from dsls_sales_summary_credits dssc
                                                                where dssc.record_date >=  ".$day_Credits.$where."
                                                                ".$like_q;
        $current_day_credits_result_count = $focus->db->query($current_day_credits_sql_count);  
        $current_day_credits_count = $focus->db->fetchByAssoc($current_day_credits_result_count);
        $current_day_credits_result = $focus->db->query($current_day_credits_sql);  
        $result_records = array();
        while ($res = $focus->db->fetchByAssoc($current_day_credits_result)) {
            $result_records[] = array(
                    '<a href="http://fmpco.info/python/invLookup.py/main" target="_blank">'.$res['ref'].'</a>',
                    $res['credit'],
                    $res['slsm'],
                    $res['rdate'],
                );
        }

        print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => $current_day_credits_count['totalRecords'], "iTotalDisplayRecords" => $current_day_credits_count['totalRecords'], "aaData" => $result_records));
        exit;
    }
    
      function getSlsmBelow($slsmtree, $slsmno, $matchfoundinparent=false) {
		$result = array();
		foreach($slsmtree as $slsm) {
			if($slsm['slsm'] == $slsmno or $matchfoundinparent) { /* found above me or we have a match, so return all below */
				$result[] = $slsm['slsm'];
				if($slsm['children']!=='') {
					$result = array_merge($result, self::getSlsmBelow($slsm['children'], $slsmno, true));
				}
			} else {
				if($slsm['children']!=='') {
					$result = array_merge($result, self::getSlsmBelow($slsm['children'], $slsmno, false));
				}
			}			
		}
		return $result;
    }
}
?>