<?php

require_once('modules/ZuckerReportParameter/fmp.class.param.slsm.php');
require_once('modules/ZuckerReportParameter/fmp.class.param.regloc.php');

class LeadsController extends SugarController {

    function get_reg_loc($reg_loc) {
        $query = '';
        if (is_numeric(substr($reg_loc, 1))) {
            if (substr($reg_loc, 0, 1) == 'r') {
                $query .= ' AND a.region_c = ' . substr($reg_loc, 1);
            } else {
                $query .= ' AND a.location_c = ' . $reg_loc;
            }

            return $query;
        }
        return $query;
    }

    function get_dealer_type_query($dealer_type) {
        $query = '';
        if (is_numeric($dealer_type)) {
            $query .= ' AND a.dealertype_c = "' . $dealer_type . '"';
        } else {
            $query .= '';
        }

        return $query;
    }

    protected function user_add_on($is_user_id) {

        if (!$is_user_id) {
            return;
        }

        return ''
                . ' AND x_m.assigned_user_id="' . $this->user_id . '" '
        ;
    }

    function build__slsm($r_users = null) {

        //(strlen($_REQUEST['select']) > 0 && $_REQUEST['select'] != 'undefined') ? $selectMethod=$_REQUEST['select'] : $selectMethod=null;
        (strlen($_REQUEST['reg_loc']) > 0 && ($_REQUEST['reg_loc'] != 'undefined' && $_REQUEST['reg_loc'] != 'all' && substr($_REQUEST['reg_loc'], 0, 1) != 'r')) ? $location = explode(';', $_REQUEST['reg_loc']) : $location = null;
        (strlen($_REQUEST['reg_loc']) > 0 && ($_REQUEST['reg_loc'] != 'undefined' && $_REQUEST['reg_loc'] != 'all' && substr($_REQUEST['reg_loc'], 0, 1) == 'r')) ? $region = explode(';', $_REQUEST['reg_loc']) : $region = null;
        //(strlen($_REQUEST['slsm_num']) > 0 && ($_REQUEST['slsm_num'] != 'undefined' && $_REQUEST['slsm_num'] != 'all')) ? $slsm=explode(';',$_REQUEST['slsm_num']) : $slsm=null;
        (strlen($_REQUEST['dealer']) > 0 && ($_REQUEST['dealer'] != 'undefined' && $_REQUEST['dealer'] != 'all')) ? $dealerType = explode(';', $_REQUEST['dealer']) : $dealerType = null;
        (strlen($_REQUEST['reps']) > 0 && ($_REQUEST['reps'] != 'undefined' && $_REQUEST['reps'] != 'all')) ? $reps = explode(';', $_REQUEST['reps']) : $reps = null;
        include_once("521/FMPSales.php");

        FMPSales::initialize521();

        /* where */
        $sqlWhere = "(";

        /* not used anymore */
        $primaryOp = "AND"; /* for $selectMethod = 'i', intersect */
        if ($selectMethod == 'u') { /* union */
            $primaryOp = "OR";
        }

        /* generate where criteria with everything in acl -- including custid */
        $acl_array = $_SESSION['fmp_acl'];

        $bNeedOp = false;
        foreach ($acl_array as $acl) {
            if (!is_null($acl['slsm']) && is_null($acl['region']) && is_null($acl['location']) && is_null($acl['dealertype']) && is_null($acl['custid'])) {
                continue;
            }
            $bNeedSubOp = false;
            if ($bNeedOp) {
                $sqlWhere .= " OR ";
            }
            $sqlWhere .= "(";
            if (!is_null($acl['location'])) {
                $sqlWhere .= "lc.location_c = " . $acl['location'];
                $bNeedSubOp = true;
            }
            if (!is_null($acl['region'])) {
                if ($bNeedSubOp) {
                    $sqlWhere .= " AND ";
                }
                $sqlWhere .= "lc.region_c = " . $acl['region'];
                $bNeedSubOp = true;
            }
            if (!is_null($acl['dealertype'])) {
                if ($bNeedSubOp) {
                    $sqlWhere .= " AND ";
                }
                $sqlWhere .= sprintf("lc.dealertype_c = '%s'", $acl['dealertype']);
                $bNeedSubOp = true;
            }
            //if(is_null($location) and is_null($region) and is_null($slsm) and is_null($dealerType)) { /* no criteria specified, include odd custids */
            if (!is_null($acl['custid'])) {
                if ($bNeedSubOp) {
                    $sqlWhere .= " AND ";
                }
                $sqlWhere .= sprintf("l.assigned_user_id = %d", $acl['custid']);
                $bNeedSubOp = true;
            }
            //}
            $sqlWhere .= ")";
            $bNeedOp = true;
        }
        if (!$bNeedOp) {
            //$sqlWhere .= "0";
	    $sqlWhere .= "1";
        } /* select nothing */

        $sqlWhere .= ") AND (";

        /* now add on criteria of selection */
        $bNeedOp = false;
        if (!is_null($location)) {
            $bNeedOp = true;
            if (!is_array($location)) {
                $sqlWhere .= " lc.location_c = '$location'";
            } else {
                $sqlWhere .= " lc.location_c IN(";
                $bFirstLoc = true;
                foreach ($location as $locno) {
                    if ($bFirstLoc) {
                        $bFirstLoc = false;
                    } else {
                        $sqlWhere .= ",";
                    }
                    $sqlWhere .= "'$locno'";
                }
                $sqlWhere .= ")";
            }
        }

        if (!is_null($region)) {
            if ($bNeedOp) {
                $sqlWhere .= " AND";
            } else {
                $bNeedOp = true;
            }
            if (!is_array($region)) {
                $sqlWhere .= " lc.region_c = '$region'";
            } else {
                $sqlWhere .= " lc.region_c IN (";
                $bFirstReg = true;
                foreach ($region as $regno) {
                    if ($bFirstReg) {
                        $bFirstReg = false;
                    } else {
                        $sqlWhere .= ",";
                    }
                    $sqlWhere .= "'$regno'";
                }
                $sqlWhere .= ")";
            }
        }

        /* if (! is_null ( $slsm )) {
          if ($bNeedOp) {
          $sqlWhere .= " AND";
          } else {
          $bNeedOp = true;
          }
          if (! is_array ( $slsm )) {
          $sqlWhere .= " a.slsm_c = '$slsm'";
          } else {
          $sqlWhere .= " a.slsm_c IN (";
          $bFirstSlsm = true;
          foreach ( $slsm as $slsmno ) {
          if ($bFirstSlsm) {
          $bFirstSlsm = false;
          } else {
          $sqlWhere .= ",";
          }
          $sqlWhere .= "'$slsmno'";
          }
          $sqlWhere .= ")";
          }
          } */

        if (!is_null($dealerType)) {
            if ($bNeedOp) {
                $sqlWhere .= " AND";
            } else {
                $bNeedOp = true;
            }
            if (!is_array($dealerType)) {
                $sqlWhere .= " lc.dealertype_c = '$dealerType'";
            } else {
                $sqlWhere .= " lc.dealertype_c IN (";
                $bFirstDT = true;
                foreach ($dealerType as $dt) {
                    if ($bFirstDT) {
                        $bFirstDT = false;
                    } else {
                        $sqlWhere .= ",";
                    }
                    $sqlWhere .= "$dt";
                }
                $sqlWhere .= ")";
            }
        }

	//print_r($r_users);
        if (!is_null($r_users)) {
            if ($bNeedOp) {
                $sqlWhere .= " AND";
            } else {
                $bNeedOp = true;
            }
            if (!is_array($r_users)) {
                $sqlWhere .= " l.assigned_user_id = '$r_users'";
            } else {
                $sqlWhere .= " l.assigned_user_id IN (";
                $bFirstDT = true;
                foreach ($r_users as $rep) {
                    if ($bFirstDT) {
                        $bFirstDT = false;
                    } else {
                        $sqlWhere .= ",";
                    }
                    $sqlWhere .= "'$rep'";
                }
                $sqlWhere .= ")";
            }
        }


        if (!is_null($reps)) {
            if ($bNeedOp) {
                $sqlWhere .= " AND";
            } else {
                $bNeedOp = true;
            }
            if (!is_array($reps)) {
                $sqlWhere .= " l.assigned_user_id = '$reps'";
            } else {
                $sqlWhere .= " l.assigned_user_id IN (";
                $bFirstDT = true;
                foreach ($reps as $rep) {
                    if ($bFirstDT) {
                        $bFirstDT = false;
                    } else {
                        $sqlWhere .= ",";
                    }
                    $sqlWhere .= "'$rep'";
                }
                $sqlWhere .= ")";
            }
        }

        if (!$bNeedOp) {
            $sqlWhere .= "1";
        } /* no criteria so provide everything already added in ACL */
        $sqlWhere .= ")";

	

        $h = ''
                //. $this->user_add_on($is_user_id)
                . ' WHERE l.deleted = 0 AND (' . $sqlWhere . ')'
        ;
        return $h;
    }

    public function action_updateLead() {
        global $app_list_strings, $mod_strings, $current_user;
        require_once("modules/Calls/Call.php");
        $focus = new Call();
        if (!$focus) {
            //trigger_error($mod_strings['LBL_ERROR_IMPORTS_NOT_SET_UP'], E_USER_ERROR);
        }

        //print_r($_REQUEST['lead_id']);
        //print_r($_REQUEST['status_desc']);

        $lead_sql = "SELECT status_description FROM leads WHERE id='" . $_REQUEST['lead_id'] . "'";

        $leads = $focus->db->query($lead_sql);
        while ($lead = $focus->db->fetchByAssoc($leads)) {
            $lead_status_desc = $lead['status_description'];
        }
        $lead_update_sql = "UPDATE leads SET status_description='" . $_REQUEST['status_desc'] . "' WHERE id='" . $_REQUEST['lead_id'] . "'";

        $focus->db->query($lead_update_sql);
        exit;
    }

//////////////////////////////////////////get Leads
    public function action_getCustomersFilter() {
        global $app_list_strings, $mod_strings, $current_user;
        require_once("modules/Calls/Call.php");
        $focus = new Call();
        if (!$focus) {
            //trigger_error($mod_strings['LBL_ERROR_IMPORTS_NOT_SET_UP'], E_USER_ERROR);
        }



        foreach ($app_list_strings['call_direction_dom'] as $k => $v)
            $statuses_direction .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';

        //$statuses = '</select><select name="status_qi" id="status_qi" title="">';

        foreach ($app_list_strings['call_status_dom'] as $k => $v)
            $statuses_status .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';

//we dont know how this should work
        $str_selection_button = $this->build__slsm();

        $leads_statuses = ($_REQUEST["status"] != "" && $_REQUEST["status"] != "undefined" && $_REQUEST["status"] != "null") ? explode(";", $_REQUEST["status"]) : null;

        $leads_sources = ($_REQUEST["source"] != "" && $_REQUEST["source"] != "undefined" && $_REQUEST["source"] != "null") ? explode(";", $_REQUEST["source"]) : null;

        $statuses = (!is_null($leads_statuses)) ? " AND l.status IN('" . implode("', '", $leads_statuses) . "') " : "";
        $sources = (!is_null($leads_sources)) ? " AND l.lead_source IN('" . implode("', '", $leads_statuses) . "') " : "";

        switch ($_REQUEST['iSortCol_0']) {

            case 1:
                $field = "leadno";
                break;
            case 2:
                $field = "account_name";
                break;
            case 3:
                $field = "contact";
                break;
            case 4:
                $field = "phone";
                break;
            case 5:
                $field = "poten";
                break;
            case 6:
                $field = "status_desc";
                break;
            case 7:
                $field = "status";
                break;
            case 8:
                $field = "source_desc";
                break;
            /* case 9:
              $field = "source_desc";
              break;
              case 10:
              $field = "mtd_proj_vs_mtd_bud";
              break;
              case 11:
              $field = "ytd_proj_vs_ytd_budg";
              break;
             */
            default:
                $field = "l.id";
                break;
        }



        $customer_sql = "SELECT l.id AS lead_id, lc.leadid_c AS leadno, l.title AS name, l.salutation AS salutation, l.account_name AS account_name, l.first_name AS first_name, l.last_name AS last_name, concat_ws(' ', l.first_name, l.last_name) AS contact, l.phone_work AS phone, l.status_description AS status_desc, l.status AS status, l.lead_source_description AS source_desc, lc.leadopp_c AS poten
                            FROM leads l
                            INNER JOIN leads_cstm lc ON l.id=lc.id_c
                            " . $str_selection_button . " " . $statuses . " " . $sources . "
                            ORDER BY " . $field . " " . $_REQUEST['sSortDir_0'] . " LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];
//print_r($customer_sql);
//exit;




        $customer_sql_count = "SELECT COUNT(l.id) AS allrecords  
			FROM leads l
                        INNER JOIN leads_cstm lc ON l.id=lc.id_c
                        " . $str_selection_button;


        //print_r($customer_sql);
        //exit;


        $customers = $focus->db->query($customer_sql);

        $customers_count = $focus->db->query($customer_sql_count);

        /*
         * AND o.date_modified BETWEEN STR_TO_DATE('" . $from_date . " 00:00:00', '%Y-%m-%d %H:%i:%s')
          AND STR_TO_DATE('" . $to_date . " 23:59:59', '%Y-%m-%d %H:%i:%s') AND o.sales_stage = 'Closed Won'
         */

        /* $selectbox = '<table id="customers_list">
          <thead>
          <th>Include</th>
          <th>LeadNo</th>
          <th>Lead Account Name</th>
          <th>Lead Contact</th>
          <th>Phone</th>
          <th>Lead Opportunity Potential</th>
          <th>Status Description</th>
          <th>Status</th>
          <th>Lead Source Description</th>
          <th>Pre-Call Plan</th>
          <th>Outcome</th>
          </thead>
          <tbody>';
         */

        $coustomer_count = $focus->db->fetchByAssoc($customers_count);

        $cstm_ids = array();
        while ($customer = $focus->db->fetchByAssoc($customers)) {

            $cstm_ids[] = array('<input type="checkbox"  id="' . $customer['lead_id'] . '" lead_account_name="' . $customer['account_name'] . '" parent_name="' . $customer['salutation'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'] . '">',
                $customer['leadno'],
                $customer['account_name'],
                $customer['contact'],
                $customer['phone'],
                $this->red_color_text($customer['poten']),
                '<textarea class="status_description" lead_id="' . $customer['lead_id'] . '" name="lead_id" >' . $customer['status_desc'] . '</textarea>',
                $customer['status'],
                $customer['source_desc'],
                '<select name="direction_qi" class="statuses_direction" account_id_statuses_direction="' . $customer['lead_id'] . '" title="">' . $statuses_direction . '</select><select name="status_qi"  class="statuses_status" account_id_statuses_status="' . $customer['lead_id'] . '" title="">' . $statuses_status . '</select>',
                '<textarea name="pc-plan" class="pc-plan" cols="32" account_id_pcplan="' . $customer['lead_id'] . '"></textarea>',
                '<textarea name="outcome" class="outcome" cols="32" account_id_outcome="' . $customer['lead_id'] . '"></textarea>',
            );

            //$cstm_ids[] = $customer;
        }

        //print_r($cstm_ids);

        print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => $coustomer_count['allrecords'], "iTotalDisplayRecords" => $coustomer_count['allrecords'], "aaData" => $cstm_ids));
        exit;
        //print $selectbox . implode(PHP_EOL, $cstm_ids) . $selectboxend;
        //exit;
    }

//////////////////////////////////////////get Leads
    public function action_getCustomers() {
        global $app_list_strings, $mod_strings, $current_user;
        require_once("modules/Calls/Call.php");
        $focus = new Call();
        if (!$focus) {
            //trigger_error($mod_strings['LBL_ERROR_IMPORTS_NOT_SET_UP'], E_USER_ERROR);
        }



        foreach ($app_list_strings['call_direction_dom'] as $k => $v)
            $statuses_direction .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';

        //$statuses = '</select><select name="status_qi" id="status_qi" title="">';

        foreach ($app_list_strings['call_status_dom'] as $k => $v)
            $statuses_status .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';

        $is_user_id = 0;
        $slsm_obj = new fmp_Param_SLSM($current_user->id);
        $slsm_obj->init();

        $is_s = $slsm_obj->is_assigned_slsm();
        $str_selection_button = '';
        if ($is_s) {
            if(isset($_POST['slsm_num']));
            $arr =  Array(0 => null);
            $r_users = $slsm_obj->compile__available_users($arr);
            //$str_selection_button = $this->build__slsm($r_users, $is_user_id);
        }



//we dont know how this should work
        $str_selection_button = $this->build__slsm($r_users);



        switch ($_REQUEST['iSortCol_0']) {

            case 1:
                $field = "leadno";
                break;
            case 2:
                $field = "account_name";
                break;
            case 3:
                $field = "contact";
                break;
            case 4:
                $field = "phone";
                break;
            case 5:
                $field = "poten";
                break;
            case 6:
                $field = "status_desc";
                break;
            case 7:
                $field = "status";
                break;
            case 8:
                $field = "source_desc";
                break;
            /* case 9:
              $field = "source_desc";
              break;
              case 10:
              $field = "mtd_proj_vs_mtd_bud";
              break;
              case 11:
              $field = "ytd_proj_vs_ytd_budg";
              break;
             */
            default:
                $field = "l.id";
                break;
        }



        $customer_sql = "SELECT l.id AS lead_id, lc.leadid_c AS leadno, l.title AS name, l.salutation AS salutation, l.account_name AS account_name, l.first_name AS first_name, l.last_name AS last_name, concat_ws(' ', l.first_name, l.last_name) AS contact, l.phone_work AS phone, l.status_description AS status_desc, l.status AS status, l.lead_source_description AS source_desc, lc.leadopp_c AS poten
                            FROM leads l
                            INNER JOIN leads_cstm lc ON l.id=lc.id_c
                            " . $str_selection_button . "
                            ORDER BY " . $field . " " . $_REQUEST['sSortDir_0'] . " LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];





        $customer_sql_count = "SELECT COUNT(l.id) AS allrecords  
			FROM leads l
                        INNER JOIN leads_cstm lc ON l.id=lc.id_c
                        " . $str_selection_button;


        //print_r($customer_sql);
        //exit;


        $customers = $focus->db->query($customer_sql);

        $customers_count = $focus->db->query($customer_sql_count);

        /*
         * AND o.date_modified BETWEEN STR_TO_DATE('" . $from_date . " 00:00:00', '%Y-%m-%d %H:%i:%s')
          AND STR_TO_DATE('" . $to_date . " 23:59:59', '%Y-%m-%d %H:%i:%s') AND o.sales_stage = 'Closed Won'
         */

        /* $selectbox = '<table id="customers_list">
          <thead>
          <th>Include</th>
          <th>LeadNo</th>
          <th>Lead Account Name</th>
          <th>Lead Contact</th>
          <th>Phone</th>
          <th>Lead Opportunity Potential</th>
          <th>Status Description</th>
          <th>Status</th>
          <th>Lead Source Description</th>
          <th>Pre-Call Plan</th>
          <th>Outcome</th>
          </thead>
          <tbody>';
         */

        $coustomer_count = $focus->db->fetchByAssoc($customers_count);

        $cstm_ids = array();
        while ($customer = $focus->db->fetchByAssoc($customers)) {

            $cstm_ids[] = array('<input type="checkbox"  id="' . $customer['lead_id'] . '" lead_account_name="' . $customer['account_name'] . '" parent_name="' . $customer['salutation'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'] . '">',
                $customer['leadno'],
                $customer['account_name'],
                $customer['contact'],
                $customer['phone'],
                $this->red_color_text($customer['poten']),
                '<textarea class="status_description" lead_id="' . $customer['lead_id'] . '" name="lead_id" >' . $customer['status_desc'] . '</textarea>',
                $customer['status'],
                $customer['source_desc'],
                '<select name="direction_qi" class="statuses_direction" account_id_statuses_direction="' . $customer['lead_id'] . '" title="">' . $statuses_direction . '</select><select name="status_qi"  class="statuses_status" account_id_statuses_status="' . $customer['lead_id'] . '" title="">' . $statuses_status . '</select>',
                '<textarea name="pc-plan" class="pc-plan" cols="32" account_id_pcplan="' . $customer['lead_id'] . '"></textarea>',
                '<textarea name="outcome" class="outcome" cols="32" account_id_outcome="' . $customer['lead_id'] . '"></textarea>',
            );

            //$cstm_ids[] = $customer;
        }

        //print_r($cstm_ids);

        print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => $coustomer_count['allrecords'], "iTotalDisplayRecords" => $coustomer_count['allrecords'], "aaData" => $cstm_ids));
        exit;
        //print $selectbox . implode(PHP_EOL, $cstm_ids) . $selectboxend;
        //exit;
    }

//////////////////////////////////////////get Lead by ID
    public function action_getCustomerById() {
        global $app_list_strings, $mod_strings, $current_user;
        require_once("modules/Calls/Call.php");
        require_once("modules/Meetings/Meeting.php");

        if ($_REQUEST['type'] == 'call') {
            $bean = new Call();
            $type = 'call';
            $table_name = $bean->table_name;
            $jn = "cal2_call_id_c";
        }
        if ($_REQUEST['type'] == 'meeting') {
            $bean = new Meeting();
            $type = 'meeting';
            $table_name = $bean->table_name;
            $jn = "cal2_meeting_id_c";
        }
        $bean->retrieve($_REQUEST['record']);


        require_once("modules/Calls/Call.php");
        $focus = new Call();
        if (!$focus) {
            //trigger_error($mod_strings['LBL_ERROR_IMPORTS_NOT_SET_UP'], E_USER_ERROR);
        }



        foreach ($app_list_strings['call_direction_dom'] as $k => $v) {
            if ($k == $bean->direction) {
                $statuses_direction .= '<option selected label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';
            } else {
                $statuses_direction .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';
            }
        }
        //$statuses = '</select><select name="status_qi" id="status_qi" title="">';

        foreach ($app_list_strings['call_status_dom'] as $k => $v) {
            if ($k == $bean->status) {
                $statuses_status .= '<option selected label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';
            } else {
                $statuses_status .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';
            }
        }

//we dont know how this should work
//	$str_selection_button = $this->build__slsm();







        $customer_sql = "SELECT l.id AS lead_id, lc.leadid_c AS leadno, l.title AS name, l.salutation AS salutation, l.account_name AS account_name, l.first_name AS first_name, l.last_name AS last_name, concat_ws(' ', l.first_name, l.last_name) AS contact, l.phone_work AS phone, l.status_description AS status_desc, l.status AS status, l.lead_source_description AS source_desc, lc.leadopp_c AS poten
                            FROM leads l
                            INNER JOIN leads_cstm lc ON l.id=lc.id_c
                            WHERE l.id='" . $_REQUEST['customer_id'] . "'";





        $customer_sql_count = "SELECT COUNT(l.id) AS allrecords  
			FROM leads l
                        INNER JOIN leads_cstm lc ON l.id=lc.id_c
                        WHERE l.deleted=0";


        //print_r($customer_sql);
        //exit;


        $customers = $focus->db->query($customer_sql);

        $customers_count = $focus->db->query($customer_sql_count);

        /*
         * AND o.date_modified BETWEEN STR_TO_DATE('" . $from_date . " 00:00:00', '%Y-%m-%d %H:%i:%s')
          AND STR_TO_DATE('" . $to_date . " 23:59:59', '%Y-%m-%d %H:%i:%s') AND o.sales_stage = 'Closed Won'
         */

        /* $selectbox = '<table id="customers_list">
          <thead>
          <th>Include</th>
          <th>LeadNo</th>
          <th>Lead Account Name</th>
          <th>Lead Contact</th>
          <th>Phone</th>
          <th>Lead Opportunity Potential</th>
          <th>Status Description</th>
          <th>Status</th>
          <th>Lead Source Description</th>
          <th>Pre-Call Plan</th>
          <th>Outcome</th>
          </thead>
          <tbody>';
         */

        $coustomer_count = $focus->db->fetchByAssoc($customers_count);

        //$cstm_ids = array();
        while ($customer = $focus->db->fetchByAssoc($customers)) {

            $cstm_ids = array('<input type="checkbox"  id="' . $customer['lead_id'] . '" lead_account_name="' . $customer['account_name'] . '" parent_name="' . $customer['salutation'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'] . '">',
                $customer['leadno'],
                $customer['account_name'],
                $customer['contact'],
                $customer['phone'],
                $this->red_color_text($customer['poten']),
                '<textarea class="status_description" lead_id="' . $customer['lead_id'] . '" name="lead_id" >' . $customer['status_desc'] . '</textarea>',
                $customer['status'],
                $customer['source_desc'],
                '<select name="direction_qi" class="statuses_direction" account_id_statuses_direction="' . $customer['lead_id'] . '" title="">' . $statuses_direction . '</select><select name="status_qi"  class="statuses_status" account_id_statuses_status="' . $customer['lead_id'] . '" title="">' . $statuses_status . '</select>',
                '<textarea name="pc-plan" class="pc-plan" cols="32" account_id_pcplan="' . $customer['lead_id'] . '">'. $bean->description .'</textarea>',
                '<textarea name="outcome" class="outcome" cols="32" account_id_outcome="' . $customer['lead_id'] . '">'. $bean->outcome_c .'</textarea>',
            );

            //$cstm_ids[] = $customer;
        }

        //print_r($cstm_ids);

        print json_encode(array("dataresult" => $cstm_ids));
        exit;
        //print $selectbox . implode(PHP_EOL, $cstm_ids) . $selectboxend;
        //exit;
    }

    function red_color_text($value) {
        if ($value < 0) {
            return '<p style="color: red">(' . '$' . number_format(abs($value), 0, '.', ',') . ')</p>';
        } else {
            return '<p>' . '$' . number_format($value, 0, '.', ',') . '</p>';
        }
    }

}

?>
