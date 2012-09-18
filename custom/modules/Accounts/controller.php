<?php

require_once('modules/ZuckerReportParameter/fmp.class.param.slsm.php');
require_once('modules/ZuckerReportParameter/fmp.class.param.regloc.php');

class AccountsController extends SugarController {

    public function action_sendData(){
        $this->view = 'accdatacreate';
    }
    
    function red_color_text($value) {
        if ($value < 0) {
            return '<p style="color: red">(' . '$' . number_format(abs($value), 0, '.', ',') . ')</p>';
        } else {
            return '<p>' . '$' . number_format($value, 0, '.', ',') . '</p>';
        }
    }

    function build__slsm($compiled_slsm, $is_user_id) {

        //(strlen($_REQUEST['select']) > 0 && $_REQUEST['select'] != 'undefined') ? $selectMethod=$_REQUEST['select'] : $selectMethod=null;
        (strlen($_REQUEST['reg_loc']) > 0 && ($_REQUEST['reg_loc'] != 'undefined' && $_REQUEST['reg_loc'] != 'all')) ? $location = explode(';', $_REQUEST['reg_loc']) : $location = null;
        (strlen($_REQUEST['reg_loc']) > 0 && ($_REQUEST['reg_loc'] != 'undefined' && $_REQUEST['reg_loc'] != 'all')) ? $region = explode(';', $_REQUEST['reg_loc']) : $region = null;
        (strlen($_REQUEST['slsm_num']) > 0 && ($_REQUEST['slsm_num'] != 'undefined' && $_REQUEST['slsm_num'] != 'all')) ? $slsm = explode(';', $_REQUEST['slsm_num']) : $slsm = null;
        (strlen($_REQUEST['dealer']) > 0 && ($_REQUEST['dealer'] != 'undefined' && $_REQUEST['dealer'] != 'all')) ? $dealerType = explode(';', $_REQUEST['dealer']) : $dealerType = null;
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
            $bNeedSubOp = false;
            if ($bNeedOp) {
                $sqlWhere .= " OR ";
            }
            $sqlWhere .= "(";
            if (!is_null($acl['location'])) {
                $sqlWhere .= "a.location_c = " . $acl['location'];
                $bNeedSubOp = true;
            }
            if (!is_null($acl['region'])) {
                if ($bNeedSubOp) {
                    $sqlWhere .= " AND ";
                }
                $sqlWhere .= "a.region_c = " . $acl['region'];
                $bNeedSubOp = true;
            }
            if (!is_null($acl['slsm'])) {
                if ($bNeedSubOp) {
                    $sqlWhere .= " AND ";
                }
                $sqlWhere .= "a.slsm_c = '" . $acl['slsm'] . "'";  /* used to select a.slsm_c -- why? */
                $bNeedSubOp = true;
            }
            if (!is_null($acl['dealertype'])) {
                if ($bNeedSubOp) {
                    $sqlWhere .= " AND ";
                }
                $sqlWhere .= sprintf("a.dealertype_c = '%s'", $acl['dealertype']);
                $bNeedSubOp = true;
            }
            //if(is_null($location) and is_null($region) and is_null($slsm) and is_null($dealerType)) { /* no criteria specified, include odd custids */
            if (!is_null($acl['custid'])) {
                if ($bNeedSubOp) {
                    $sqlWhere .= " AND ";
                }
                $sqlWhere .= sprintf("a.custid_c = %d", $acl['custid']);
                $bNeedSubOp = true;
            }
            //}
            $sqlWhere .= ")";
            $bNeedOp = true;
        }
        if (!$bNeedOp) {
            $sqlWhere .= "0";
        } /* select nothing */

        $sqlWhere .= ") AND (";

        /* now add on criteria of selection */
        $bNeedOp = false;
        if (!is_null($location)) {
            $bNeedOp = true;
            if (!is_array($location)) {
                $sqlWhere .= " a.location_c = '$location'";
            } else {
                $sqlWhere .= " a.location_c IN(";
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
                $sqlWhere .= " a.region_c = '$region'";
            } else {
                $sqlWhere .= " a.region_c IN (";
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

        if (!is_null($slsm)) {
            if ($bNeedOp) {
                $sqlWhere .= " AND";
            } else {
                $bNeedOp = true;
            }
            if (!is_array($slsm)) {
                $sqlWhere .= " a.slsm_c = '$slsm'";
            } else {
                $sqlWhere .= " a.slsm_c IN (";
                $bFirstSlsm = true;
                foreach ($slsm as $slsmno) {
                    if ($bFirstSlsm) {
                        $bFirstSlsm = false;
                    } else {
                        $sqlWhere .= ",";
                    }
                    $sqlWhere .= "'$slsmno'";
                }
                $sqlWhere .= ")";
            }
        }

        if (!is_null($dealerType)) {
            if ($bNeedOp) {
                $sqlWhere .= " AND";
            } else {
                $bNeedOp = true;
            }
            if (!is_array($dealerType)) {
                $sqlWhere .= " a.dealertype_c = '$dealerType'";
            } else {
                $sqlWhere .= " a.dealertype_c IN (";
                $bFirstDT = true;
                foreach ($dealerType as $dt) {
                    if ($bFirstDT) {
                        $bFirstDT = false;
                    } else {
                        $sqlWhere .= ",";
                    }
                    $sqlWhere .= "'$dt'";
                }
                $sqlWhere .= ")";
            }
        }

        if (!$bNeedOp) {
            $sqlWhere .= "1";
        } /* no criteria so provide everything already added in ACL */
        $sqlWhere .= ")";




        $h = ''
                . $this->user_add_on($is_user_id)
                . ' WHERE a.deleted = 0 AND ((a.slsm_c) Not In (20,232)) AND ((a.custtype_c) Not In (\'AFFL\',\'TRAV\') OR a.custtype_c is null) AND (' . $sqlWhere . ')'
        ;
        return $h;
    }

    function build__slsm_default($compiled_slsm, $is_user_id) {
        foreach ($compiled_slsm as $k => $v) {
            $compiled_slsm[$k] = "'$v'";
        }
        strlen($_REQUEST['select']) > 0 ? $selectMethod = $_REQUEST['select'] : $selectMethod = null;
        strlen($_REQUEST['location']) > 0 ? $location = explode(';', $_REQUEST['location']) : $location = null;
        strlen($_REQUEST['region']) > 0 ? $region = explode(';', $_REQUEST['region']) : $region = null;
        strlen($_REQUEST['slsm']) > 0 ? $slsm = explode(';', $_REQUEST['slsm']) : $slsm = null;
        strlen($_REQUEST['dealertype']) > 0 ? $dealertype = explode(';', $_REQUEST['dealertype']) : $dealertype = null;
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
            $bNeedSubOp = false;
            if ($bNeedOp) {
                $sqlWhere .= " OR ";
            }
            $sqlWhere .= "(";
            if (!is_null($acl['location'])) {
                $sqlWhere .= "a.location_c = " . $acl['location'];
                $bNeedSubOp = true;
            }
            if (!is_null($acl['region'])) {
                if ($bNeedSubOp) {
                    $sqlWhere .= " AND ";
                }
                $sqlWhere .= "a.region_c = " . $acl['region'];
                $bNeedSubOp = true;
            }
            if (!is_null($acl['slsm'])) {
                if ($bNeedSubOp) {
                    $sqlWhere .= " AND ";
                }
                $sqlWhere .= "a.slsm_c = '" . $acl['slsm'] ."'";  /* used to select a.slsm_c -- why? */
                $bNeedSubOp = true;
            }
            if (!is_null($acl['dealertype'])) {
                if ($bNeedSubOp) {
                    $sqlWhere .= " AND ";
                }
                $sqlWhere .= sprintf("a.dealertype_c = '%s'", $acl['dealertype']);
                $bNeedSubOp = true;
            }
            //if(is_null($location) and is_null($region) and is_null($slsm) and is_null($dealerType)) { /* no criteria specified, include odd custids */
            if (!is_null($acl['custid'])) {
                if ($bNeedSubOp) {
                    $sqlWhere .= " AND ";
                }
                $sqlWhere .= sprintf("a.custid_c = %d", $acl['custid']);
                $bNeedSubOp = true;
            }
            //}
            $sqlWhere .= ")";
            $bNeedOp = true;
        }
        if (!$bNeedOp) {
            $sqlWhere .= "0";
        } /* select nothing */

        $sqlWhere .= ") AND (";

        /* now add on criteria of selection */
        $bNeedOp = false;
        if (!is_null($location)) {
            $bNeedOp = true;
            if (!is_array($location)) {
                $sqlWhere .= " a.location_c = '$location'";
            } else {
                $sqlWhere .= " a.location_c IN(";
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
                $sqlWhere .= " a.region_c = '$region'";
            } else {
                $sqlWhere .= " a.region_c IN (";
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

        if (!is_null($slsm)) {
            if ($bNeedOp) {
                $sqlWhere .= " AND";
            } else {
                $bNeedOp = true;
            }
            if (!is_array($slsm)) {
                $sqlWhere .= " a.slsm_c = '$slsm'";
            } else {
                $sqlWhere .= " a.slsm_c IN (";
                $bFirstSlsm = true;
                foreach ($slsm as $slsmno) {
                    if ($bFirstSlsm) {
                        $bFirstSlsm = false;
                    } else {
                        $sqlWhere .= ",";
                    }
                    $sqlWhere .= "'$slsmno'";
                }
                $sqlWhere .= ")";
            }
        }

        if (!is_null($dealerType)) {
            if ($bNeedOp) {
                $sqlWhere .= " AND";
            } else {
                $bNeedOp = true;
            }
            if (!is_array($dealerType)) {
                $sqlWhere .= " a.dealertype_c = '$dealerType'";
            } else {
                $sqlWhere .= " a.dealertype_c IN (";
                $bFirstDT = true;
                foreach ($dealerType as $dt) {
                    if ($bFirstDT) {
                        $bFirstDT = false;
                    } else {
                        $sqlWhere .= ",";
                    }
                    $sqlWhere .= "'$dt'";
                }
                $sqlWhere .= ")";
            }
        }

        if (!$bNeedOp) {
            $sqlWhere .= "1";
        } /* no criteria so provide everything already added in ACL */
        $sqlWhere .= ")";

        $h = ''
                . $this->user_add_on($is_user_id)
                . ' WHERE a.deleted = 0 AND ((a.slsm_c) Not In (20,232)) AND ((a.custtype_c) Not In (\'AFFL\',\'TRAV\') OR a.custtype_c is null) AND (' . $sqlWhere . ')'
        ;
        return $h;
    }

    protected function user_add_on($is_user_id) {

        if (!$is_user_id) {
            return;
        }

        return ''
                . ' AND x_m.assigned_user_id="' . $this->user_id . '" '
        ;
    }

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

    public function action_getCustomers() {
        global $app_list_strings, $mod_strings, $current_user;

        //$statuses = '<select name="direction_qi" id="direction_qi" title="">';

        foreach ($app_list_strings['call_direction_dom'] as $k => $v)
            $statuses_direction .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';

        //$statuses = '</select><select name="status_qi" id="status_qi" title="">';

        foreach ($app_list_strings['call_status_dom'] as $k => $v)
            $statuses_status .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';
        //$statuses .= '</select>';




        require_once("modules/Calls/Call.php");
        $focus = new Call();
        if (!$focus) {
            //trigger_error($mod_strings['LBL_ERROR_IMPORTS_NOT_SET_UP'], E_USER_ERROR);
        }


        $slsm = $_REQUEST['slsm_num'] != 'all' ? $_REQUEST['slsm_num'] : '';
        $reg_loc = $_REQUEST['reg_loc'] != 'all' ? $_REQUEST['reg_loc'] : '';
        $dealer_post = $_REQUEST['dealer'] != 'all' ? $_REQUEST['dealer'] : '';
        $username_post = $_REQUEST['username'] != 'all' ? $_REQUEST['username'] : '';
        $city_post = $_REQUEST['city'] != '' ? $_REQUEST['city'] : '';
        $state_post = $_REQUEST['state'] != '' ? $_REQUEST['state'] : '';
        $postalcode_post = $_REQUEST['postalcode'] != '' ? $_REQUEST['postalcode'] : '';



        $username_query = $username_post != '' ? " AND a.name LIKE '" . $username_post . "%' " : "";
        $city_query = $city_post != '' ? " AND a.shipping_address_city LIKE '" . $city_post . "%' " : "";
        $state_query = $state_post != '' ? " AND a.shipping_address_state LIKE '" . $state_post . "%' " : "";
        $postalcode_query = $postalcode_post != '' ? " AND a.shipping_address_postalcode LIKE '" . $postalcode_post . "%' " : "";
        $is_user_id = 0;
        $slsm_obj = new fmp_Param_SLSM($current_user->id);
        $slsm_obj->init();

        $is_s = $slsm_obj->is_assigned_slsm();
        $str_selection_button = $this->build__slsm($r_users, $is_user_id);
        if ($is_s) {
            $arr[] = $slsm;
            $r_users = $slsm_obj->compile__available_slsm($arr);
            $str_selection_button = $this->build__slsm($r_users, $is_user_id);
            $title .= $slsm != 'undefined' && $slsm != '' ? ($reg_loc != '' && $reg_loc != 'undefined' && $reg_loc != 'undefined' ? '/Slsm ' . $slsm : 'Slsm ' . $slsm) : '';
        }


        $reg_loc_query = $this->get_reg_loc($reg_loc);
        $dealer_type_query = $this->get_dealer_type_query($dealer_post);

        $from_date = date('Y-m-d', strtotime('-6 month'));
        $to_date = date('Y-m-d');
        switch ($_REQUEST['iSortCol_0']) {

            case 1:
                $field = "CONCAT(IF(ASCII(LEFT(a.custno_c,1))>57,LEFT(a.custno_c,1),'0'),IF(ASCII(RIGHT(a.custno_c,1))>57,LPAD(a.custno_c,10,'0'),LPAD(CONCAT(a.custno_c,'-'), 10,'0')))";
                break;
            case 2:
                $field = "a.name";
                break;
            case 3:
                $field = "a.shipping_address_street";
                break;
            case 4:
                $field = "a.shipping_address_city";
                break;
            case 5:
                $field = "a.shipping_address_state";
                break;
            case 6:
                $field = "a.shipping_address_postalcode";
                break;
            case 7:
                $field = "a.employees";
                break;
            case 8:
                $field = "a.phone_office";
                break;
            case 9:
                $field = "a.mtd_sales_c";
                break;
            case 10:
                $field = "mtd_proj_vs_mtd_bud";
                break;
            case 11:
                $field = "a.ytd_sales_c";
                break;
            case 12:
                $field = "ytd_proj_vs_ytd_budg";
                break;
            default:
                $field = "a.name";
                break;
        }

        $customer_sql = "SELECT a.id AS account_id, 
				a.custno_c AS custno, 
				a.name AS name, 
				a.employees AS contact,
				a.shipping_address_street,
				a.shipping_address_city,
				a.shipping_address_state,
				a.shipping_address_postalcode, 
				a.phone_office AS phone, 
				a.mtd_sales_c AS mtd_sales, 
				(a.mtd_projected_c - a.mtd_budget_sales_c) AS mtd_proj_vs_mtd_bud, 
                                                                        a.ytd_sales_c AS ytd_sales, 
				(a.ytd_projected_c - a.ytd_budget_sales_c) AS ytd_proj_vs_ytd_budg 
			FROM accounts a
                                " . $str_selection_button . " " . $username_query . " " . $city_query . " " . $state_query . " " . $postalcode_query . " 
			ORDER BY " . $field . " " . $_REQUEST['sSortDir_0'] . " LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];

        $customer_sql_count = "SELECT COUNT(a.id) AS allrecords  
			FROM accounts a
                                " . $str_selection_button . " " . $username_query . "
			ORDER BY a.name";
//print_r($customer_sql);
//exit;
        /*        $customer_sql = "SELECT a.id AS account_id, a.custno_c AS custno, a.name AS name, a.employees AS contact, a.phone_office AS phone, a.mtd_sales_c AS mtd_sales, (a.mtd_projected_c - a.mtd_budget_sales_c) AS mtd_proj_vs_mtd_bud, (a.ytd_projected_c - a.ytd_budget_sales_c) AS ytd_proj_vs_ytd_budg FROM accounts a
          WHERE a.deleted=0 " . $reg_loc_query . " " . $str_selection_button . " " . $dealer_type_query . " " . $username_query . " GROUP BY a.id ORDER BY a.name ";
         */

        $customers = $focus->db->query($customer_sql);
        $customers_count = $focus->db->query($customer_sql_count);
        /*
         * AND o.date_modified BETWEEN STR_TO_DATE('" . $from_date . " 00:00:00', '%Y-%m-%d %H:%i:%s')
          AND STR_TO_DATE('" . $to_date . " 23:59:59', '%Y-%m-%d %H:%i:%s') AND o.sales_stage = 'Closed Won'
         */

        /* $selectbox = '<table id="customers_list">
          <thead>
          <th>Include</th>
          <th>CustNo</th>
          <th>CustName</th>
          <th>Contact</th>
          <th>Phone</th>
          <th>MTD Sales</th>
          <th>MTD Proj vs. Budget</th>
          <th>YTD Proj vs. Budget</th>
          <th>Product Line Specific MTD Sales</th>
          <th>Pre-Call Plan</th>
          <th>Outcome</th>
          </thead>
          <tbody>'; */


        $coustomer_count = $focus->db->fetchByAssoc($customers_count);
        $cstm_ids = array();
        while ($customer = $focus->db->fetchByAssoc($customers)) {
            $cstm_ids[] = array('<input type="checkbox" id="' . $customer['account_id'] . '" parent_name="' . $customer['name'] . '" cust_no="' . $customer['custno'] . '">',
                $customer['custno'],
                $customer['name'],
                $customer['shipping_address_street'],
                $customer['shipping_address_city'],
                $customer['shipping_address_state'],
                $customer['shipping_address_postalcode'],
                $customer['contact'],
                $customer['phone'],
                $this->red_color_text($customer['mtd_sales']),
                $this->red_color_text($customer['mtd_proj_vs_mtd_bud']),
                $this->red_color_text($customer['ytd_sales']),
                $this->red_color_text($customer['ytd_proj_vs_ytd_budg']),
                '<select name="direction_qi" class="statuses_direction" account_id_statuses_direction="' . $customer['account_id'] . '" title="">' . $statuses_direction . '</select><select name="status_qi"  class="statuses_status" account_id_statuses_status="' . $customer['account_id'] . '" title="">' . $statuses_status . '</select>',
                '<textarea name="pc-plan" class="pc-plan" cols="32" account_id_pcplan="' . $customer['account_id'] . '"></textarea>',
                '<textarea name="outcome" class="outcome" cols="32" account_id_outcome="' . $customer['account_id'] . '"></textarea>',
            );
        }

        print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => $coustomer_count['allrecords'], "iTotalDisplayRecords" => $coustomer_count['allrecords'], "aaData" => $cstm_ids));
        exit;
    }

    public function action_getCustomersOpp() {
        global $app_list_strings, $mod_strings, $current_user;

        //$statuses = '<select name="direction_qi" id="direction_qi" title="">';

        foreach ($app_list_strings['call_direction_dom'] as $k => $v)
            $statuses_direction .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';

        //$statuses = '</select><select name="status_qi" id="status_qi" title="">';

        foreach ($app_list_strings['call_status_dom'] as $k => $v)
            $statuses_status .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';
        //$statuses .= '</select>';




        require_once("modules/Calls/Call.php");
        $focus = new Call();
        if (!$focus) {
            //trigger_error($mod_strings['LBL_ERROR_IMPORTS_NOT_SET_UP'], E_USER_ERROR);
        }


        $slsm = $_REQUEST['slsm_num'] != 'all' ? $_REQUEST['slsm_num'] : '';
        $reg_loc = $_REQUEST['reg_loc'] != 'all' ? $_REQUEST['reg_loc'] : '';
        $dealer_post = $_REQUEST['dealer'] != 'all' ? $_REQUEST['dealer'] : '';
        $customers = explode(",",$_REQUEST['account_ids']);
        $ids_post = $customers[0] != '' ? $customers : '';
        $city_post = $_REQUEST['city'] != '' ? $_REQUEST['city'] : '';
        $state_post = $_REQUEST['state'] != '' ? $_REQUEST['state'] : '';
        $postalcode_post = $_REQUEST['postalcode'] != '' ? $_REQUEST['postalcode'] : '';



        $customer_query =  $ids_post != '' ? " AND a.id IN ('".implode("','", $ids_post)."') " : "";
        $city_query = $city_post != '' ? " AND a.shipping_address_city LIKE '" . $city_post . "%' " : "";
        $state_query = $state_post != '' ? " AND a.shipping_address_state LIKE '" . $state_post . "%' " : "";
        $postalcode_query = $postalcode_post != '' ? " AND a.shipping_address_postalcode LIKE '" . $postalcode_post . "%' " : "";
        $is_user_id = 0;
        $slsm_obj = new fmp_Param_SLSM($current_user->id);
        $slsm_obj->init();

        $is_s = $slsm_obj->is_assigned_slsm();
        $str_selection_button = $this->build__slsm($r_users, $is_user_id);
        if ($is_s) {
            $arr[] = $slsm;
            $r_users = $slsm_obj->compile__available_slsm($arr);
            $str_selection_button = $this->build__slsm($r_users, $is_user_id);
            $title .= $slsm != 'undefined' && $slsm != '' ? ($reg_loc != '' && $reg_loc != 'undefined' && $reg_loc != 'undefined' ? '/Slsm ' . $slsm : 'Slsm ' . $slsm) : '';
        }


        $reg_loc_query = $this->get_reg_loc($reg_loc);
        $dealer_type_query = $this->get_dealer_type_query($dealer_post);

        $from_date = date('Y-m-d', strtotime('-6 month'));
        $to_date = date('Y-m-d');

        switch ($_REQUEST['iSortCol_0']) {

            case 1:
                $field = "CONCAT(IF(ASCII(LEFT(a.custno_c,1))>57,LEFT(a.custno_c,1),'0'),IF(ASCII(RIGHT(a.custno_c,1))>57,LPAD(a.custno_c,10,'0'),LPAD(CONCAT(a.custno_c,'-'), 10,'0')))";
                break;
            case 2:
                $field = "a.name";
                break;
            case 3:
                $field = "a.shipping_address_street";
                break;
            case 4:
                $field = "a.shipping_address_city";
                break;
            case 5:
                $field = "a.shipping_address_state";
                break;
            case 6:
                $field = "a.shipping_address_postalcode";
                break;
            case 7:
                $field = "a.employees";
                break;
            case 8:
                $field = "a.phone_office";
                break;
            case 9:
                $field = "a.mtd_sales_c";
                break;
            case 10:
                $field = "mtd_proj_vs_mtd_bud";
                break;
            case 11:
                $field = "a.ytd_sales_c";
                break;
            case 12:
                $field = "ytd_proj_vs_ytd_budg";
                break;
            default:
                $field = "a.name";
                break;
        }
        //$count_of = $_GET['iDisplayLength'] != -1 ? ", " . $_GET['iDisplayLength'] : "";

        $customer_sql = "SELECT a.id AS account_id, 
				a.custno_c AS custno, 
				a.name AS name,
                                a.assigned_user_id as assigned,
				a.employees AS contact,
				a.shipping_address_street,
				a.shipping_address_city,
				a.shipping_address_state,
				a.shipping_address_postalcode, 
				a.phone_office AS phone, 
				a.mtd_sales_c AS mtd_sales, 
				(a.mtd_projected_c - a.mtd_budget_sales_c) AS mtd_proj_vs_mtd_bud, 
                                                                        a.ytd_sales_c AS ytd_sales, 
				(a.ytd_projected_c - a.ytd_budget_sales_c) AS ytd_proj_vs_ytd_budg 
			FROM accounts a
                                " . $str_selection_button . " " . $customer_query . " " . $city_query . " " . $state_query . " " . $postalcode_query . " 
			ORDER BY " . $field . " " . $_REQUEST['sSortDir_0'] . " LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];

        $customer_sql_count = "SELECT COUNT(a.id) AS allrecords  
			FROM accounts a
                                " . $str_selection_button . " " . $customer_query . "
			ORDER BY a.name";
//print_r($customer_sql);
//exit;
        /*        $customer_sql = "SELECT a.id AS account_id, a.custno_c AS custno, a.name AS name, a.employees AS contact, a.phone_office AS phone, a.mtd_sales_c AS mtd_sales, (a.mtd_projected_c - a.mtd_budget_sales_c) AS mtd_proj_vs_mtd_bud, (a.ytd_projected_c - a.ytd_budget_sales_c) AS ytd_proj_vs_ytd_budg FROM accounts a
          WHERE a.deleted=0 " . $reg_loc_query . " " . $str_selection_button . " " . $dealer_type_query . " " . $username_query . " GROUP BY a.id ORDER BY a.name ";
         */

        $customers = $focus->db->query($customer_sql);
        $customers_count = $focus->db->query($customer_sql_count);
        /*
         * AND o.date_modified BETWEEN STR_TO_DATE('" . $from_date . " 00:00:00', '%Y-%m-%d %H:%i:%s')
          AND STR_TO_DATE('" . $to_date . " 23:59:59', '%Y-%m-%d %H:%i:%s') AND o.sales_stage = 'Closed Won'
         */

        /* $selectbox = '<table id="customers_list">
          <thead>
          <th>Include</th>
          <th>CustNo</th>
          <th>CustName</th>
          <th>Contact</th>
          <th>Phone</th>
          <th>MTD Sales</th>
          <th>MTD Proj vs. Budget</th>
          <th>YTD Proj vs. Budget</th>
          <th>Product Line Specific MTD Sales</th>
          <th>Pre-Call Plan</th>
          <th>Outcome</th>
          </thead>
          <tbody>'; */


        $coustomer_count = $focus->db->fetchByAssoc($customers_count);
        $cstm_ids = array();
        while ($customer = $focus->db->fetchByAssoc($customers)) {
            $cstm_ids[] = array('<input type="checkbox" id="' . $customer['account_id'] . '" parent_name="' . $customer['name'] . '" cust_no="' . $customer['custno'] . '">',
                $customer['custno'],
                $customer['name'],
                $customer['shipping_address_street'],
                $customer['shipping_address_city'],
                $customer['shipping_address_state'],
                $customer['shipping_address_postalcode'],
                $customer['contact'],
                $customer['phone'],
                $this->red_color_text($customer['mtd_sales']),
                $this->red_color_text($customer['mtd_proj_vs_mtd_bud']),
                $this->red_color_text($customer['ytd_sales']),
                $this->red_color_text($customer['ytd_proj_vs_ytd_budg']),
                $this->action_getAssignedWidget($customer['account_id'],$customer['assigned']),
                    //'<select name="direction_qi" class="statuses_direction" account_id_statuses_direction="' . $customer['account_id'] . '" title="">' . $statuses_direction . '</select><select name="status_qi"  class="statuses_status" account_id_statuses_status="' . $customer['account_id'] . '" title="">' . $statuses_status . '</select>',
                    //'<textarea name="pc-plan" class="pc-plan" cols="32" account_id_pcplan="' . $customer['account_id'] . '"></textarea>',
                    //'<textarea name="outcome" class="outcome" cols="32" account_id_outcome="' . $customer['account_id'] . '"></textarea>',
            );
        }

        print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => $coustomer_count['allrecords'], "iTotalDisplayRecords" => $coustomer_count['allrecords'], "aaData" => $cstm_ids));
        exit;
    }

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



//        print_r($bean);
//        exit;

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

        require_once("modules/Calls/Call.php");
        $focus = new Call();

        $customer_sql = "SELECT a.id AS account_id, 
                                a.custno_c AS custno, 
                                a.name AS name, 
                                a.employees AS contact, 
				a.shipping_address_street,
				a.shipping_address_city,
				a.shipping_address_state,
				a.shipping_address_postalcode,
                                a.phone_office AS phone, 
                                a.mtd_sales_c AS mtd_sales, 
                                (a.mtd_projected_c - a.mtd_budget_sales_c) AS mtd_proj_vs_mtd_bud, 
                                (a.ytd_projected_c - a.ytd_budget_sales_c) AS ytd_proj_vs_ytd_budg 
			FROM accounts a
                        WHERE a.id='" . $_GET['customer_id'] . "'";

        $customers = $focus->db->query($customer_sql);
        while ($customer = $focus->db->fetchByAssoc($customers)) {
            $cstm_ids = array($customer['custno'],
                $customer['name'],
                $customer['shipping_address_street'],
                $customer['shipping_address_city'],
                $customer['shipping_address_state'],
                $customer['shipping_address_postalcode'],
                $customer['contact'],
                $customer['phone'],
                $this->red_color_text($customer['mtd_sales']),
                $this->red_color_text($customer['mtd_proj_vs_mtd_bud']),
                $this->red_color_text($customer['ytd_proj_vs_ytd_budg']),
                '<select name="direction_qi" class="statuses_direction" account_id_statuses_direction="' . $customer['account_id'] . '" title="">' . $statuses_direction . '</select><select name="status_qi"  class="statuses_status" account_id_statuses_status="' . $customer['account_id'] . '" title="">' . $statuses_status . '</select>',
                '<textarea name="pc-plan" class="pc-plan" cols="32" account_id_pcplan="' . $customer['account_id'] . '">' . $bean->description . '</textarea>',
                '<textarea name="outcome" class="outcome" cols="32" account_id_outcome="' . $customer['account_id'] . '">' . $bean->outcome_c . '</textarea>',
            );
        }

        print json_encode(array("dataresult" => $cstm_ids));
        exit;
    }

    public function action_getCustomersDefault() {
        global $app_list_strings, $mod_strings, $current_user;

        //$statuses = '<select name="direction_qi" id="direction_qi" title="">';


        foreach ($app_list_strings['call_direction_dom'] as $k => $v)
            $statuses_direction .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';

        //$statuses = '</select><select name="status_qi" id="status_qi" title="">';

        foreach ($app_list_strings['call_status_dom'] as $k => $v)
            $statuses_status .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';
        //$statuses .= '</select>';


        $focus = new Call();
        if (!$focus) {
            //trigger_error($mod_strings['LBL_ERROR_IMPORTS_NOT_SET_UP'], E_USER_ERROR);
        }





        $is_user_id = 0;
        $slsm_obj = new fmp_Param_SLSM($current_user->id);
        $slsm_obj->init();

        $is_s = $slsm_obj->is_assigned_slsm();

        if ($is_s) {
//            if(isset($_POST['slsm_num'])) {
            if (isset($_POST['slsm_num']))
                ;
            $arr = Array(0 => null);
//            }


            $r_users = $slsm_obj->compile__available_slsm($arr);

            $str_selection_button = $this->build__slsm_default($r_users, $is_user_id);
        }

        switch ($_REQUEST['iSortCol_0']) {

            case 1:
                $field = "CONCAT(IF(ASCII(LEFT(a.custno_c,1))>57,LEFT(a.custno_c,1),'0'),IF(ASCII(RIGHT(a.custno_c,1))>57,LPAD(a.custno_c,10,'0'),LPAD(CONCAT(a.custno_c,'-'), 10,'0')))";
                break;
            case 2:
                $field = "a.name";
                break;
            case 3:
                $field = "a.shipping_address_street";
                break;
            case 4:
                $field = "a.shipping_address_city";
                break;
            case 5:
                $field = "a.shipping_address_state";
                break;
            case 6:
                $field = "a.shipping_address_postalcode";
                break;
            case 7:
                $field = "a.employees";
                break;
            case 8:
                $field = "a.phone_office";
                break;
            case 9:
                $field = "a.mtd_sales_c";
                break;
            case 10:
                $field = "mtd_proj_vs_mtd_bud";
                break;
            case 11:
                $field = "a.ytd_sales_c";
                break;
             case 12:
                $field = "ytd_proj_vs_ytd_budg";
                break;
            default:
                $field = "a.name";
                break;
        }

        $customer_sql = "SELECT a.id AS account_id, 
                                a.custno_c AS custno, 
                                a.name AS name, 
                                a.employees AS contact, 
				a.shipping_address_street,
				a.shipping_address_city,
				a.shipping_address_state,
				a.shipping_address_postalcode,
                                a.phone_office AS phone, 
                                a.mtd_sales_c AS mtd_sales, 
                                (a.mtd_projected_c - a.mtd_budget_sales_c) AS mtd_proj_vs_mtd_bud, 
                                a.ytd_sales_c AS ytd_sales, 
                                (a.ytd_projected_c - a.ytd_budget_sales_c) AS ytd_proj_vs_ytd_budg 
			FROM accounts a
                                " . $str_selection_button . " 
			ORDER BY " . $field . " " . $_REQUEST['sSortDir_0'] . " LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];


        $customer_sql_count = "SELECT COUNT(a.id) AS allrecords  
			FROM accounts a
                                " . $str_selection_button . " 
			ORDER BY a.name";

        $customers = $focus->db->query($customer_sql);
        $customers_count = $focus->db->query($customer_sql_count);
        /*
         * AND o.date_modified BETWEEN STR_TO_DATE('" . $from_date . " 00:00:00', '%Y-%m-%d %H:%i:%s')
          AND STR_TO_DATE('" . $to_date . " 23:59:59', '%Y-%m-%d %H:%i:%s') AND o.sales_stage = 'Closed Won'
         */

        /* $selectbox = '<table id="customers_list">
          <thead>
          <th>Include</th>
          <th>CustNo</th>
          <th>CustName</th>
          <th>Contact</th>
          <th>Phone</th>
          <th>MTD Sales</th>
          <th>MTD Proj vs. Budget</th>
          <th>YTD Proj vs. Budget</th>
          <th>Product Line Specific MTD Sales</th>
          <th>Pre-Call Plan</th>
          <th>Outcome</th>
          </thead>
          <tbody>'; */
        $coustomer_count = $focus->db->fetchByAssoc($customers_count);
        $cstm_ids = array();
        while ($customer = $focus->db->fetchByAssoc($customers)) {





            $cstm_ids[] = array('<input type="checkbox" id="' . $customer['account_id'] . '" parent_name="' . $customer['name'] . '"  cust_no="' . $customer['custno'] . '">',
                $customer['custno'],
                $customer['name'],
                $customer['shipping_address_street'],
                $customer['shipping_address_city'],
                $customer['shipping_address_state'],
                $customer['shipping_address_postalcode'],
                $customer['contact'],
                $customer['phone'],
                $this->red_color_text($customer['mtd_sales']),
                $this->red_color_text($customer['mtd_proj_vs_mtd_bud']),
                $this->red_color_text($customer['ytd_sales']),
                $this->red_color_text($customer['ytd_proj_vs_ytd_budg']),
                '<select name="direction_qi" class="statuses_direction" account_id_statuses_direction="' . $customer['account_id'] . '" title="">' . $statuses_direction . '</select><select name="status_qi"  class="statuses_status" account_id_statuses_status="' . $customer['account_id'] . '" title="">' . $statuses_status . '</select>',
                '<textarea name="pc-plan" class="pc-plan" cols="32" account_id_pcplan="' . $customer['account_id'] . '"></textarea>',
                '<textarea name="outcome" class="outcome" cols="32" account_id_outcome="' . $customer['account_id'] . '"></textarea>',
            );
        }

        print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => $coustomer_count['allrecords'], "iTotalDisplayRecords" => $coustomer_count['allrecords'], "aaData" => $cstm_ids));
        exit;
    }

    public function action_getAssignedWidget($id,$assigned_id) {
        global $current_user;
        if(!empty($assigned_id)){
            $focus  = new Account();
            $query = "SELECT user_name FROM users WHERE id='".$assigned_id."'";
            $result = $focus->db->query($query);
            $user_name = $focus->db->fetchByAssoc($result);
             if(empty($user_name['user_name'])){
            $user_name = $current_user->user_name;
            }else{
                $user_name = $user_name['user_name'];
            }
        }else{
            $assigned_id = $current_user->id;
            $user_name = $current_user->user_name;
        }
        $output = '<script language="javascript">
                if(typeof sqs_objects == \'undefined\')
                    {
                    var sqs_objects = new Array;
                    }
                    sqs_objects[\'assigned_user_name_' . $id . '\']={
                                                "method":"get_user_array",
                                                "field_list":["user_name","id","custno_c","shipping_address_street","shipping_address_city","shipping_address_state","shipping_address_postalcode"],
                                                "populate_list":["assigned_user_name_' . $id . '","assigned_user_id_' . $id . '"],
                                                "required_list":["assigned_user_id_' . $id . '"],
                                                "conditions":[{"name":"user_name","op":"like_custom","end":"%","value":""}],
                                                "limit":"30","no_match_text":"No Match"};
        </script>
        <input type="text" individual_assigned_name="' . $id . '" name="assigned_user_name_' . $id . '" class="sqsEnabled user_name" tabindex="1" id="assigned_user_name_' . $id . '" size="" value="' . $user_name . '" title=\'\' autocomplete="off"  >
        <input type="hidden" class="user_id" individual_assigned_id="' . $id . '" name="assigned_user_id_' . $id . '" id="assigned_user_id_' . $id . '" value="' . $assigned_id . '">
        <input type="hidden" class="user_name_hidden" value="' . $user_name . '">
        <input type="hidden" class="user_id_hidden"  value="' . $assigned_id . '">
        <input type="button" name="btn_assigned_user_name" tabindex="1" title="Select [Alt+T]" accessKey="T" class="button" value="Search" onclick=\'open_popup("Users", 600, 400, "", true, false, {"call_back_function":"set_return","form_name":"EditView","field_to_name_array":{"id":"assigned_user_id_' . $id . '","user_name":"assigned_user_name_' . $id . '"}}, "single", true);\'>
        <input type="button" name="btn_clr_assigned_user_name" tabindex="1" title="Clear [Alt+C]" accessKey="C" class="button" onclick="$(\'form#EditView input[individual_assigned_id=' . $id . ']\').val(\'\'); $(\'form#EditView input[individual_assigned_name=' . $id . ']\').val(\'\');" value="Clear">';

        return $output;
    }

    public function action_getCustomersDefaultOpp() {
        global $app_list_strings, $mod_strings, $current_user;

        //$statuses = '<select name="direction_qi" id="direction_qi" title="">';


        foreach ($app_list_strings['call_direction_dom'] as $k => $v)
            $statuses_direction .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';

        //$statuses = '</select><select name="status_qi" id="status_qi" title="">';

        foreach ($app_list_strings['call_status_dom'] as $k => $v)
            $statuses_status .= '<option label = "' . $v . '" value = "' . $k . '">' . $v . '</option>';
        //$statuses .= '</select>';


        $focus = new Call();
        if (!$focus) {
            //trigger_error($mod_strings['LBL_ERROR_IMPORTS_NOT_SET_UP'], E_USER_ERROR);
        }





        $is_user_id = 0;
        $slsm_obj = new fmp_Param_SLSM($current_user->id);
        $slsm_obj->init();

        $is_s = $slsm_obj->is_assigned_slsm();

        if ($is_s) {
//            if(isset($_POST['slsm_num'])) {
            if (isset($_POST['slsm_num']))
                ;
            $arr = Array(0 => null);
//            }


            $r_users = $slsm_obj->compile__available_slsm($arr);

            $str_selection_button = $this->build__slsm_default($r_users, $is_user_id);
        }

        switch ($_REQUEST['iSortCol_0']) {

            case 1:
                $field = "CONCAT(IF(ASCII(LEFT(a.custno_c,1))>57,LEFT(a.custno_c,1),'0'),IF(ASCII(RIGHT(a.custno_c,1))>57,LPAD(a.custno_c,10,'0'),LPAD(CONCAT(a.custno_c,'-'), 10,'0')))";
                break;
            case 2:
                $field = "a.name";
                break;
            case 3:
                $field = "a.shipping_address_street";
                break;
            case 4:
                $field = "a.shipping_address_city";
                break;
            case 5:
                $field = "a.shipping_address_state";
                break;
            case 6:
                $field = "a.shipping_address_postalcode";
                break;
            case 7:
                $field = "a.employees";
                break;
            case 8:
                $field = "a.phone_office";
                break;
            case 9:
                $field = "a.mtd_sales_c";
                break;
            case 10:
                $field = "mtd_proj_vs_mtd_bud";
                break;
            case 11:
                $field = "a.ytd_sales_c";
                break;
            case 12:
                $field = "ytd_proj_vs_ytd_budg";
                break;
            default:
                $field = "a.name";
                break;
        }


        $count_of = $_GET['iDisplayLength'] != -1 ? ", " . $_GET['iDisplayLength'] : "";

        $customer_sql = "SELECT a.id AS account_id, 
                                a.custno_c AS custno, 
                                a.name AS name, 
                                a.assigned_user_id as assigned,
                                a.employees AS contact, 
				a.shipping_address_street,
				a.shipping_address_city,
				a.shipping_address_state,
				a.shipping_address_postalcode,
                                a.phone_office AS phone, 
                                a.mtd_sales_c AS mtd_sales, 
                                (a.mtd_projected_c - a.mtd_budget_sales_c) AS mtd_proj_vs_mtd_bud, 
                                a.ytd_sales_c AS ytd_sales, 
                                (a.ytd_projected_c - a.ytd_budget_sales_c) AS ytd_proj_vs_ytd_budg 
			FROM accounts a
                                " . $str_selection_button . " 
			ORDER BY " . $field . " " . $_REQUEST['sSortDir_0'] . " LIMIT " . $_GET['iDisplayStart'] . $count_of;


        $customer_sql_count = "SELECT COUNT(a.id) AS allrecords  
			FROM accounts a
                                " . $str_selection_button . " 
			ORDER BY a.name";

        $customers = $focus->db->query($customer_sql);
        $customers_count = $focus->db->query($customer_sql_count);
        /*
         * AND o.date_modified BETWEEN STR_TO_DATE('" . $from_date . " 00:00:00', '%Y-%m-%d %H:%i:%s')
          AND STR_TO_DATE('" . $to_date . " 23:59:59', '%Y-%m-%d %H:%i:%s') AND o.sales_stage = 'Closed Won'
         */

        /* $selectbox = '<table id="customers_list">
          <thead>
          <th>Include</th>
          <th>CustNo</th>
          <th>CustName</th>
          <th>Contact</th>
          <th>Phone</th>
          <th>MTD Sales</th>
          <th>MTD Proj vs. Budget</th>
          <th>YTD Proj vs. Budget</th>
          <th>Product Line Specific MTD Sales</th>
          <th>Pre-Call Plan</th>
          <th>Outcome</th>
          </thead>
          <tbody>'; */
        $coustomer_count = $focus->db->fetchByAssoc($customers_count);
        $cstm_ids = array();
        while ($customer = $focus->db->fetchByAssoc($customers)) {





            $cstm_ids[] = array('<input type="checkbox" id="' . $customer['account_id'] . '" parent_name="' . $customer['name'] . '"  cust_no="' . $customer['custno'] . '">',
                $customer['custno'],
                $customer['name'],
                $customer['shipping_address_street'],
                $customer['shipping_address_city'],
                $customer['shipping_address_state'],
                $customer['shipping_address_postalcode'],
                $customer['contact'],
                $customer['phone'],
                $this->red_color_text($customer['mtd_sales']),
                $this->red_color_text($customer['mtd_proj_vs_mtd_bud']),
                $this->red_color_text($customer['ytd_sales']),
                $this->red_color_text($customer['ytd_proj_vs_ytd_budg']),
                $this->action_getAssignedWidget($customer['account_id'],$customer['assigned']),
//                '<select name="direction_qi" class="statuses_direction" account_id_statuses_direction="' . $customer['account_id'] . '" title="">' . $statuses_direction . '</select><select name="status_qi"  class="statuses_status" account_id_statuses_status="' . $customer['account_id'] . '" title="">' . $statuses_status . '</select>',
//                '<textarea name="pc-plan" class="pc-plan" cols="32" account_id_pcplan="' . $customer['account_id'] . '"></textarea>',
//                '<textarea name="outcome" class="outcome" cols="32" account_id_outcome="' . $customer['account_id'] . '"></textarea>',
            );
        }

        print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => $coustomer_count['allrecords'], "iTotalDisplayRecords" => $coustomer_count['allrecords'], "aaData" => $cstm_ids));
        exit;
    }

}

?>
