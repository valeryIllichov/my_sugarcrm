<?php

class PricingController extends SugarController {

    public function action_getNormLineDefaultException() {
        global $timedate;
        require_once("modules/Accounts/Account.php");
        $focus = new Account();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }
        if (!strstr($focus->fulldealertype_c, '*')) {
            //no *
            $dealertype_n_default = substr($focus->fulldealertype_c, 4, 2);
        } else {
            $splited_data = explode('*', $focus->fulldealertype_c);
            $dealertype_n_default = $splited_data[1];
            if (strlen($dealertype_n_default > 2)) {
                $dealertype_n_default = substr($dealertype_n_default, 0, 1);
            }
        }
        $like_exep = "";
        $like_def = "";
        if (!empty($_REQUEST['sSearch'])) {
            $like_exep = " AND (pec.profiletype LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.line LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
                pec.cat LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.pc LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.pf LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            pec.mult LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.expire_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            pec.effective_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.review_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            pec.accel LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.round LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.strategic LIKE '%" . $_REQUEST['sSearch'] . "%') ";
        }
        $union = '';
        $union_count = '';
        if ($dealertype_n_default != '') {
            if (!empty($_REQUEST['sSearch'])) {
                $like_def = " AND (ddold.profiletype LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.line LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
                ddold.cat LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.pc LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.pf LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            ddold.mult LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.expire_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            ddold.effective_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.acceleration_pricing LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
                ddold.price_rounding LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.strategic_code LIKE '%" . $_REQUEST['sSearch'] . "%') ";
            }
            $union = " union
select 'default' as typeRow,  ddold.profiletype,ddold.line,ddold.cat, ddold.pc, ddold.pf, ddold.mult, 
			ddold.expire_date, ddold.effective_date,'', ddold.acceleration_pricing,ddold.price_rounding,ddold.strategic_code
			from dsls_disc_ovr_line_default ddold
			where ddold.defaultid = '" . $dealertype_n_default . "' and line is not null 
                                                        $like_def";
            $union_count = "select count(*) as total
			from dsls_disc_ovr_line_default ddold
			where ddold.defaultid = '" . $dealertype_n_default . "' and line is not null 
                                                        $like_def";
        }
        switch ($_REQUEST['iSortCol_0']) {
            case 0:
                $field = "typeRow";
                break;
            case 1:
                $field = "profiletype";
                break;
            case 2:
                $field = "line";
                break;
            case 3:
                $field = "cat+0";
                break;
            case 4:
                $field = "pc";
                break;
            case 5:
                $field = "expire_date";
                break;
            case 6:
                $field = "effective_date";
                break;
            case 7:
                $field = "review_date";
                break;
            case 8:
                $field = "pf";
                break;
            case 9:
                $field = "mult";
                break;
            case 10:
                $field = "accel";
                break;
            case 11:
                $field = "round";
                break;
            case 12:
                $field = "strategic";
                break;

            default:
                $field = "line,cat,pc";
                break;
        }

        $query = "select  'exception' as typeRow, pec.profiletype, pec.line, pec.cat, pec.pc, pec.pf, pec.mult, 
			pec.expire_date, pec.effective_date, pec.review_date,pec.accel,pec.round, pec.strategic 
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'N' and Line is not null
                                    $like_exep
                                    $union 
               ORDER BY " . $field . " " . $_REQUEST['sSortDir_0'] . " LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];
        $query_count = "select count(*) as total
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'N' and Line is not null $like_exep";
        if ($union_count != '') {
            $union_total_res = $focus->db->query($union_count);
            $union_total = $focus->db->fetchByAssoc($union_total_res);
        } else {
            $union_total['total'] = 0;
        }
        $query_total_res = $focus->db->query($query_count);
        $query_total = $focus->db->fetchByAssoc($query_total_res);
        $totalRecords = $query_total['total'] + $union_total['total'];

        $result = $focus->db->query($query);
        $norm_line_list = array();
        while (($row = $focus->db->fetchByAssoc($result)) != null) {
            if ($row['typeRow'] == 'exception') {
                $norm_line_list[] = array('<span style="color:#00B400">exception</span>',
                    '<span style="color:#00B400">' . $row['profiletype'] . '</span>',
                    '<span style="color:#00B400">' . $row['line'] . '</span>',
                    '<span style="color:#00B400">' . $row['cat'] . '</span>',
                    '<span style="color:#00B400">' . $row['pc'] . '</span>',
                    '<span style="color:#00B400">' . $timedate->to_display_date($row['effective_date'], false) . '</span>',
                    '<span style="color:#00B400">' . $timedate->to_display_date($row['review_date'], false) . '</span>',
                    '<span style="color:#00B400">' . $timedate->to_display_date($row['expire_date'], false) . '</span>',
                    '<span style="color:#00B400">' . $row['pf'] . '</span>',
                    '<span style="color:#00B400">' . $row['mult'] . '</span>',
                    '<span style="color:#00B400">' . $row['accel'] . '</span>',
                    '<span style="color:#00B400">' . $row['round'] . '</span>',
                    '<span style="color:#00B400">' . $row['strategic'] . '</span>'
                );
            } else {
                $norm_line_list[] = array('default',
                    $row['profiletype'],
                    $row['line'],
                    $row['cat'],
                    $row['pc'],
                    $timedate->to_display_date($row['effective_date'], false),
                    $timedate->to_display_date($row['review_date'], false),
                    $timedate->to_display_date($row['expire_date'], false),
                    $row['pf'],
                    $row['mult'],
                    $row['accel'],
                    $row['round'],
                    $row['strategic']
                );
            }
        }

        print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => $totalRecords, "iTotalDisplayRecords" => $totalRecords, "aaData" => $norm_line_list));
        exit;
    }

    public function action_getNormProdDefaultException() {
        global $timedate;
        require_once("modules/Accounts/Account.php");
        $focus = new Account();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }
        if (!strstr($focus->fulldealertype_c, '*')) {
            $dealertype_n_default = substr($focus->fulldealertype_c, 4, 2);
        } else {
            $splited_data = explode('*', $focus->fulldealertype_c);
            $dealertype_n_default = $splited_data[1];
        }
        $like_exep = "";
        $like_def = "";
        if (!empty($_REQUEST['sSearch'])) {
            $like_exep = " AND (pec.profiletype LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.partno LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
                pec.pf LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.mult LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.expire_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            pec.effective_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.review_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            pec.basetype LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.price LIKE '%" . $_REQUEST['sSearch'] . "%') ";
        }
        $union = '';
        $union_count = '';
        if ($dealertype_n_default != '') {
            if (!empty($_REQUEST['sSearch'])) {
                $like_def = " AND (ddop.profiletype LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddop.partno LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
                ddop.pf LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddop.mult LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddop.expire_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            ddop.effective_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddop.basetype LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            ddop.price LIKE '%" . $_REQUEST['sSearch'] . "%') ";
            }
            $union = " union 
                            select 'default' as typeRow,ddop.profiletype, ddop.partno, ddop.pf, ddop.mult, 
			ddop.expire_date, ddop.effective_date, '', ddop.basetype, ddop.price
			from  dsls_disc_ovr_product_default ddop
			where ddop.defaultid = '" . $dealertype_n_default . "' and ddop.partno is not null
                                                        $like_def";
            $union_count = "select count(*) as total
			from dsls_disc_ovr_product_default ddop
			where ddop.defaultid = '" . $dealertype_n_default . "' and ddop.partno is not null
                                                        $like_def";
        }
        switch ($_REQUEST['iSortCol_0']) {
            case 0:
                $field = "typeRow";
                break;
             case 1:
                $field = "profiletype";
                break;
            case 2:
                $field = "partno";
                break;
            case 3:
                $field = "pf";
                break;
            case 4:
                $field = "mult";
                break;
            case 5:
                $field = "expire_date";
                break;
            case 6:
                $field = "effective_date";
                break;
            case 7:
                $field = "review_date";
                break;
            case 8:
                $field = "basetype";
                break;
            case 9:
                $field = "price";

            default:
                $field = "partno";
                break;
        }

        $query = "select  'exception' as typeRow, pec.profiletype, pec.partno, pec.pf, pec.mult, 
			pec.expire_date, pec.effective_date, pec.review_date, pec.basetype, pec.price
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'N' and pec.partno is not null
                                    $like_exep
                                    $union 
               ORDER BY " . $field . " " . $_REQUEST['sSortDir_0'] . " LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];
        $query_count = "select count(*) as total
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'N' and Line is not null $like_exep";
        if ($union_count != '') {
            $union_total_res = $focus->db->query($union_count);
            $union_total = $focus->db->fetchByAssoc($union_total_res);
        } else {
            $union_total['total'] = 0;
        }
        $query_total_res = $focus->db->query($query_count);
        $query_total = $focus->db->fetchByAssoc($query_total_res);
        $totalRecords = $query_total['total'] + $union_total['total'];

        $result = $focus->db->query($query);
        $norm_line_list = array();
        while (($row = $focus->db->fetchByAssoc($result)) != null) {
            if ($row['typeRow'] == 'exception') {
                $norm_line_list[] = array('<span style="color:#00B400">exception</span>',
                    '<span style="color:#00B400">' . $row['profiletype'] . '</span>',
                    '<span style="color:#00B400">' . $row['partno'] . '</span>',
                    '<span style="color:#00B400">' . $timedate->to_display_date($row['effective_date'], false) . '</span>',
                    '<span style="color:#00B400">' . $timedate->to_display_date($row['review_date'], false) . '</span>',
                    '<span style="color:#00B400">' . $timedate->to_display_date($row['expire_date'], false) . '</span>',
                    '<span style="color:#00B400">' . $row['basetype'] . '</span>',
                    '<span style="color:#00B400">' . $row['pf'] . '</span>',
                    '<span style="color:#00B400">' . $row['mult'] . '</span>',
                    '<span style="color:#00B400">' . $row['price'] . '</span>'
                );
            } else {
                $norm_line_list[] = array('default',
                    $row['profiletype'],
                    $row['partno'],
                    $timedate->to_display_date($row['effective_date'], false),
                    $timedate->to_display_date($row['review_date'], false),
                    $timedate->to_display_date($row['expire_date'], false),
                    $row['basetype'],
                    $row['pf'],
                    $row['mult'],
                    $row['price']
                );
            }
        }

        print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => $totalRecords, "iTotalDisplayRecords" => $totalRecords, "aaData" => $norm_line_list));
        exit;
    }

    public function action_getStockLineDefaultException() {
        global $timedate;
        require_once("modules/Accounts/Account.php");
        $focus = new Account();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }
        if (!strstr($focus->fulldealertype_c, '*')) {
            $dealertype_s_default = substr($focus->fulldealertype_c, 6, 2);
        } else {
            $splited_data = explode('*', $focus->fulldealertype_c);
            if (strlen($dealertype_n_default > 2)) {
                $dealertype_n_default = substr($dealertype_n_default, 1, 2);
            }

            $dealertype_s_default = $splited_data[2];
        }
        $like_exep = "";
        $like_def = "";
        if (!empty($_REQUEST['sSearch'])) {
            $like_exep = " AND (pec.profiletype LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.line LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
                pec.cat LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.pc LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.pf LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            pec.mult LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.expire_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            pec.effective_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.review_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            pec.accel LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.round LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.strategic LIKE '%" . $_REQUEST['sSearch'] . "%') ";
        }
        $union = '';
        $union_count = '';
        if ($dealertype_s_default != '') {
            if (!empty($_REQUEST['sSearch'])) {
                $like_def = " AND (ddold.profiletype LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.line LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
                ddold.cat LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.pc LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.pf LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            ddold.mult LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.expire_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            ddold.effective_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.acceleration_pricing LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
                ddold.price_rounding LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.strategic_code LIKE '%" . $_REQUEST['sSearch'] . "%') ";
            }
            $union = " union
select 'default' as typeRow,  ddold.profiletype,ddold.line,ddold.cat, ddold.pc, ddold.pf, ddold.mult, 
			ddold.expire_date, ddold.effective_date,'', ddold.acceleration_pricing,ddold.price_rounding,ddold.strategic_code
			from dsls_disc_ovr_line_default ddold
			where ddold.defaultid = '" . $dealertype_s_default . "' and line is not null 
                                                        $like_def";
            $union_count = "select count(*) as total
			from dsls_disc_ovr_line_default ddold
			where ddold.defaultid = '" . $dealertype_s_default . "' and line is not null 
                                                        $like_def";
        }
        switch ($_REQUEST['iSortCol_0']) {
            case 0:
                $field = "typeRow";
                break;
            case 1:
                $field = "profiletype";
                break;
            case 2:
                $field = "line";
                break;
            case 3:
                $field = "cat+0";
                break;
            case 4:
                $field = "pc";
                break;
            case 5:
                $field = "expire_date";
                break;
            case 6:
                $field = "effective_date";
                break;
            case 7:
                $field = "review_date";
                break;
            case 8:
                $field = "pf";
                break;
            case 9:
                $field = "mult";
                break;
            case 10:
                $field = "accel";
                break;
            case 11:
                $field = "round";
                break;
            case 12:
                $field = "strategic";
                break;

            default:
                $field = "line,cat,pc";
                break;
        }

        $query = "select  'exception' as typeRow, pec.profiletype, pec.line, pec.cat, pec.pc, pec.pf, pec.mult, 
			pec.expire_date, pec.effective_date, pec.review_date,pec.accel,pec.round, pec.strategic 
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'S' and Line is not null
                                    $like_exep
                                    $union 
               ORDER BY " . $field . " " . $_REQUEST['sSortDir_0'] . " LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];
        $query_count = "select count(*) as total
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'S' and Line is not null $like_exep";
        if ($union_count != '') {
            $union_total_res = $focus->db->query($union_count);
            $union_total = $focus->db->fetchByAssoc($union_total_res);
        } else {
            $union_total['total'] = 0;
        }
        $query_total_res = $focus->db->query($query_count);
        $query_total = $focus->db->fetchByAssoc($query_total_res);
        $totalRecords = $query_total['total'] + $union_total['total'];

        $result = $focus->db->query($query);
        $norm_line_list = array();
        while (($row = $focus->db->fetchByAssoc($result)) != null) {
            if ($row['typeRow'] == 'exception') {
                $norm_line_list[] = array('<span style="color:#00B400">exception</span>',
                    '<span style="color:#00B400">' . $row['profiletype'] . '</span>',
                    '<span style="color:#00B400">' . $row['line'] . '</span>',
                    '<span style="color:#00B400">' . $row['cat'] . '</span>',
                    '<span style="color:#00B400">' . $row['pc'] . '</span>',
                    '<span style="color:#00B400">' . $timedate->to_display_date($row['effective_date'], false) . '</span>',
                    '<span style="color:#00B400">' . $timedate->to_display_date($row['review_date'], false) . '</span>',
                    '<span style="color:#00B400">' . $timedate->to_display_date($row['expire_date'], false) . '</span>',
                    '<span style="color:#00B400">' . $row['pf'] . '</span>',
                    '<span style="color:#00B400">' . $row['mult'] . '</span>',
                    '<span style="color:#00B400">' . $row['accel'] . '</span>',
                    '<span style="color:#00B400">' . $row['round'] . '</span>',
                    '<span style="color:#00B400">' . $row['strategic'] . '</span>'
                );
            } else {
                $norm_line_list[] = array('default',
                    $row['profiletype'],
                    $row['line'],
                    $row['cat'],
                    $row['pc'],
                    $timedate->to_display_date($row['effective_date'], false),
                    $timedate->to_display_date($row['review_date'], false),
                    $timedate->to_display_date($row['expire_date'], false),
                    $row['pf'],
                    $row['mult'],
                    $row['accel'],
                    $row['round'],
                    $row['strategic']
                );
            }
        }

        print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => $totalRecords, "iTotalDisplayRecords" => $totalRecords, "aaData" => $norm_line_list));
        exit;
    }

    public function action_getStockProdDefaultException() {
        global $timedate;
        require_once("modules/Accounts/Account.php");
        $focus = new Account();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }
        if (!strstr($focus->fulldealertype_c, '*')) {
            $dealertype_s_default = substr($focus->fulldealertype_c, 6, 2);
        } else {
            $splited_data = explode('*', $focus->fulldealertype_c);
            if (strlen($dealertype_n_default > 2)) {
                $dealertype_n_default = substr($dealertype_n_default, 1, 2);
            }

            $dealertype_s_default = $splited_data[2];
        }
        $like_exep = "";
        $like_def = "";
        if (!empty($_REQUEST['sSearch'])) {
            $like_exep = " AND (pec.profiletype LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.partno LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
                pec.pf LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.mult LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.expire_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            pec.effective_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.review_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            pec.basetype LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.price LIKE '%" . $_REQUEST['sSearch'] . "%') ";
        }
        $union = '';
        $union_count = '';
        if ($dealertype_s_default != '') {
            if (!empty($_REQUEST['sSearch'])) {
                $like_def = " AND (ddop.profiletype LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddop.partno LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
                ddop.pf LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddop.mult LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddop.expire_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            ddop.effective_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddop.basetype LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            ddop.price LIKE '%" . $_REQUEST['sSearch'] . "%') ";
            }
            $union = " union 
                            select 'default' as typeRow,ddop.profiletype, ddop.partno, ddop.pf, ddop.mult, 
			ddop.expire_date, ddop.effective_date, '', ddop.basetype, ddop.price
			from  dsls_disc_ovr_product_default ddop
			where ddop.defaultid = '" . $dealertype_s_default . "' and ddop.partno is not null
                                                        $like_def";
            $union_count = "select count(*) as total
			from dsls_disc_ovr_product_default ddop
			where ddop.defaultid = '" . $dealertype_s_default . "' and ddop.partno is not null
                                                        $like_def";
        }
        switch ($_REQUEST['iSortCol_0']) {
            case 0:
                $field = "typeRow";
                break;
             case 1:
                $field = "profiletype";
                break;
            case 2:
                $field = "partno";
                break;
            case 3:
                $field = "pf";
                break;
            case 4:
                $field = "mult";
                break;
            case 5:
                $field = "expire_date";
                break;
            case 6:
                $field = "effective_date";
                break;
            case 7:
                $field = "review_date";
                break;
            case 8:
                $field = "basetype";
                break;
            case 9:
                $field = "price";

            default:
                $field = "partno";
                break;
        }

        $query = "select  'exception' as typeRow, pec.profiletype, pec.partno, pec.pf, pec.mult, 
			pec.expire_date, pec.effective_date, pec.review_date, pec.basetype, pec.price
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'S' and pec.partno is not null
                                    $like_exep
                                    $union 
               ORDER BY " . $field . " " . $_REQUEST['sSortDir_0'] . " LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];
        $query_count = "select count(*) as total
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'S' and Line is not null $like_exep";
        if ($union_count != '') {
            $union_total_res = $focus->db->query($union_count);
            $union_total = $focus->db->fetchByAssoc($union_total_res);
        } else {
            $union_total['total'] = 0;
        }
        $query_total_res = $focus->db->query($query_count);
        $query_total = $focus->db->fetchByAssoc($query_total_res);
        $totalRecords = $query_total['total'] + $union_total['total'];

        $result = $focus->db->query($query);
        $norm_line_list = array();
        while (($row = $focus->db->fetchByAssoc($result)) != null) {
            if ($row['typeRow'] == 'exception') {
                $norm_line_list[] = array('<span style="color:#00B400">exception</span>',
                    '<span style="color:#00B400">' . $row['profiletype'] . '</span>',
                    '<span style="color:#00B400">' . $row['partno'] . '</span>',
                    '<span style="color:#00B400">' . $timedate->to_display_date($row['effective_date'], false) . '</span>',
                    '<span style="color:#00B400">' . $timedate->to_display_date($row['review_date'], false) . '</span>',
                    '<span style="color:#00B400">' . $timedate->to_display_date($row['expire_date'], false) . '</span>',
                    '<span style="color:#00B400">' . $row['basetype'] . '</span>',
                    '<span style="color:#00B400">' . $row['pf'] . '</span>',
                    '<span style="color:#00B400">' . $row['mult'] . '</span>',
                    '<span style="color:#00B400">' . $row['price'] . '</span>'
                );
            } else {
                $norm_line_list[] = array('default',
                    $row['profiletype'],
                    $row['partno'],
                    $timedate->to_display_date($row['effective_date'], false),
                    $timedate->to_display_date($row['review_date'], false),
                    $timedate->to_display_date($row['expire_date'], false),
                    $row['basetype'],
                    $row['pf'],
                    $row['mult'],
                    $row['price']
                );
            }
        }

        print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => $totalRecords, "iTotalDisplayRecords" => $totalRecords, "aaData" => $norm_line_list));
        exit;
    }

    public function action_getNormLineDefault() {
        global $timedate;
        require_once("modules/Accounts/Account.php");
        $focus = new Account();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }
        if (!strstr($focus->fulldealertype_c, '*')) {
            //no *
            $dealertype_n_default = substr($focus->fulldealertype_c, 4, 2);
        } else {
            $splited_data = explode('*', $focus->fulldealertype_c);
            $dealertype_n_default = $splited_data[1];
            if (strlen($dealertype_n_default > 2)) {
                $dealertype_n_default = substr($dealertype_n_default, 0, 1);
            }
        }

        if ($dealertype_n_default == '') {
            print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => 0, "iTotalDisplayRecords" => 0, "aaData" => array()));
            exit;
        }
        switch ($_REQUEST['iSortCol_0']) {
            case 0:
                $field = "profiletype";
                break;
            case 1:
                $field = "line";
                break;
            case 2:
                $field = "cat+0";
                break;
            case 3:
                $field = "pc";
                break;
            case 4:
                $field = "expire_date";
                break;
            case 5:
                $field = "effective_date";
                break;
            case 6:
                $field = "review_date";
                break;
            case 7:
                $field = "pf";
                break;
            case 8:
                $field = "mult";
                break;
            case 9:
                $field = "accel";
                break;
            case 10:
                $field = "round";
                break;
            case 11:
                $field = "strategic";
                break;

            default:
                $field = "line,cat,pc";
                break;
        }
        $like_def = "";
        if (!empty($_REQUEST['sSearch'])) {
            $like_def = " AND (ddold.profiletype LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.line LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
                ddold.cat LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.pc LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.pf LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            ddold.mult LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.expire_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            ddold.effective_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.acceleration_pricing LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
                ddold.price_rounding LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.strategic_code LIKE '%" . $_REQUEST['sSearch'] . "%') ";
        }
        $query = "select 'default' as typeRow,  ddold.profiletype,ddold.line,ddold.cat, ddold.pc, ddold.pf, ddold.mult, 
			ddold.expire_date, ddold.effective_date,'', ddold.acceleration_pricing,ddold.price_rounding,ddold.strategic_code
			from dsls_disc_ovr_line_default ddold
			where ddold.defaultid = '" . $dealertype_n_default . "' and line is not null 
                                    $like_def
               ORDER BY " . $field . " " . $_REQUEST['sSortDir_0'] . " LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];
        $query_count = "select count(*) as total
			from dsls_disc_ovr_line_default ddold
			where ddold.defaultid = '" . $dealertype_n_default . "' and line is not null 
                                                        $like_def";

        $query_total_res = $focus->db->query($query_count);
        $query_total = $focus->db->fetchByAssoc($query_total_res);
        $totalRecords = $query_total['total'];

        $result = $focus->db->query($query);
        $norm_line_list = array();
        while (($row = $focus->db->fetchByAssoc($result)) != null) {
                $norm_line_list[] = array($row['profiletype'],
                    $row['line'],
                    $row['cat'],
                    $row['pc'],
                    $timedate->to_display_date($row['effective_date'], false),
                    $timedate->to_display_date($row['review_date'], false),
                    $timedate->to_display_date($row['expire_date'], false),
                    $row['pf'],
                    $row['mult'],
                    $row['accel'],
                    $row['round'],
                    $row['strategic']
                );
        }

        print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => $totalRecords, "iTotalDisplayRecords" => $totalRecords, "aaData" => $norm_line_list));
        exit;
    }

    public function action_getNormProdDefault() {
        global $timedate;
        require_once("modules/Accounts/Account.php");
        $focus = new Account();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }
        if (!strstr($focus->fulldealertype_c, '*')) {
            $dealertype_n_default = substr($focus->fulldealertype_c, 4, 2);
        } else {
            $splited_data = explode('*', $focus->fulldealertype_c);
            $dealertype_n_default = $splited_data[1];
        }

        if ($dealertype_n_default == '') {
            print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => 0, "iTotalDisplayRecords" => 0, "aaData" => array()));
            exit;
        }
        switch ($_REQUEST['iSortCol_0']) {
            case 0:
                $field = "profiletype";
                break;
            case 1:
                $field = "partno";
                break;
            case 2:
                $field = "pf";
                break;
            case 3:
                $field = "mult";
                break;
            case 4:
                $field = "expire_date";
                break;
            case 5:
                $field = "effective_date";
                break;
            case 6:
                $field = "review_date";
                break;
            case 7:
                $field = "basetype";
                break;
            case 8:
                $field = "price";

            default:
                $field = "partno";
                break;
        }
        $like_def = "";
        if (!empty($_REQUEST['sSearch'])) {
            $like_def = " AND (ddop.profiletype LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddop.partno LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
                ddop.pf LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddop.mult LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddop.expire_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            ddop.effective_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddop.basetype LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            ddop.price LIKE '%" . $_REQUEST['sSearch'] . "%') ";
        }
        $query = "select 'default' as typeRow,ddop.profiletype, ddop.partno, ddop.pf, ddop.mult, 
			ddop.expire_date, ddop.effective_date, '', ddop.basetype, ddop.price
			from  dsls_disc_ovr_product_default ddop
			where ddop.defaultid = '" . $dealertype_n_default . "' and ddop.partno is not null
                                                        $like_def
               ORDER BY " . $field . " " . $_REQUEST['sSortDir_0'] . " LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];
        $query_count = "select count(*) as total
			from dsls_disc_ovr_product_default ddop
			where ddop.defaultid = '" . $dealertype_n_default . "' and ddop.partno is not null
                                                        $like_def";

        $query_total_res = $focus->db->query($query_count);
        $query_total = $focus->db->fetchByAssoc($query_total_res);
        $totalRecords = $query_total['total'];

        $result = $focus->db->query($query);
        $norm_line_list = array();
        while (($row = $focus->db->fetchByAssoc($result)) != null) {
                $norm_line_list[] = array($row['profiletype'],
                    $row['partno'],
                    $timedate->to_display_date($row['effective_date'], false),
                    $timedate->to_display_date($row['review_date'], false),
                    $timedate->to_display_date($row['expire_date'], false),
                    $row['basetype'],
                    $row['pf'],
                    $row['mult'],
                    $row['price']
                );
        }

        print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => $totalRecords, "iTotalDisplayRecords" => $totalRecords, "aaData" => $norm_line_list));
        exit;
    }

    public function action_getStockLineDefault() {
        global $timedate;
        require_once("modules/Accounts/Account.php");
        $focus = new Account();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }
        if (!strstr($focus->fulldealertype_c, '*')) {
            $dealertype_s_default = substr($focus->fulldealertype_c, 6, 2);
        } else {
            $splited_data = explode('*', $focus->fulldealertype_c);
            if (strlen($dealertype_n_default > 2)) {
                $dealertype_n_default = substr($dealertype_n_default, 1, 2);
            }

            $dealertype_s_default = $splited_data[2];
        }

        if ($dealertype_s_default == '') {
            print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => 0, "iTotalDisplayRecords" => 0, "aaData" => array()));
            exit;
        }
        switch ($_REQUEST['iSortCol_0']) {
            case 0:
                $field = "profiletype";
                break;
            case 1:
                $field = "line";
                break;
            case 2:
                $field = "cat+0";
                break;
            case 3:
                $field = "pc";
                break;
            case 4:
                $field = "expire_date";
                break;
            case 5:
                $field = "effective_date";
                break;
            case 6:
                $field = "review_date";
                break;
            case 7:
                $field = "pf";
                break;
            case 8:
                $field = "mult";
                break;
            case 9:
                $field = "accel";
                break;
            case 10:
                $field = "round";
                break;
            case 11:
                $field = "strategic";
                break;

            default:
                $field = "line,cat,pc";
                break;
        }
        $like_def = "";
        if (!empty($_REQUEST['sSearch'])) {
            $like_def = " AND (ddold.profiletype LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.line LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
                ddold.cat LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.pc LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.pf LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            ddold.mult LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.expire_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            ddold.effective_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.acceleration_pricing LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
                ddold.price_rounding LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddold.strategic_code LIKE '%" . $_REQUEST['sSearch'] . "%') ";
        }
        $query = "select 'default' as typeRow,  ddold.profiletype,ddold.line,ddold.cat, ddold.pc, ddold.pf, ddold.mult, 
			ddold.expire_date, ddold.effective_date,'', ddold.acceleration_pricing,ddold.price_rounding,ddold.strategic_code
			from dsls_disc_ovr_line_default ddold
			where ddold.defaultid = '" . $dealertype_s_default . "' and line is not null 
                                                        $like_def
               ORDER BY " . $field . " " . $_REQUEST['sSortDir_0'] . " LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];
        $query_count = "select count(*) as total
			from dsls_disc_ovr_line_default ddold
			where ddold.defaultid = '" . $dealertype_s_default . "' and line is not null 
                                                        $like_def";
        if ($union_count != '') {
            $union_total_res = $focus->db->query($union_count);
            $union_total = $focus->db->fetchByAssoc($union_total_res);
        } else {
            $union_total['total'] = 0;
        }
        $query_total_res = $focus->db->query($query_count);
        $query_total = $focus->db->fetchByAssoc($query_total_res);
        $totalRecords = $query_total['total'] + $union_total['total'];

        $result = $focus->db->query($query);
        $norm_line_list = array();
        while (($row = $focus->db->fetchByAssoc($result)) != null) {
                $norm_line_list[] = array($row['profiletype'],
                    $row['line'],
                    $row['cat'],
                    $row['pc'],
                    $timedate->to_display_date($row['effective_date'], false),
                    $timedate->to_display_date($row['review_date'], false),
                    $timedate->to_display_date($row['expire_date'], false),
                    $row['pf'],
                    $row['mult'],
                    $row['accel'],
                    $row['round'],
                    $row['strategic']
                );
        }

        print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => $totalRecords, "iTotalDisplayRecords" => $totalRecords, "aaData" => $norm_line_list));
        exit;
    }

    public function action_getStockProdDefault() {
        global $timedate;
        require_once("modules/Accounts/Account.php");
        $focus = new Account();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }
        if (!strstr($focus->fulldealertype_c, '*')) {
            $dealertype_s_default = substr($focus->fulldealertype_c, 6, 2);
        } else {
            $splited_data = explode('*', $focus->fulldealertype_c);
            if (strlen($dealertype_n_default > 2)) {
                $dealertype_n_default = substr($dealertype_n_default, 1, 2);
            }

            $dealertype_s_default = $splited_data[2];
        }

        if ($dealertype_s_default == '') {
            print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => 0, "iTotalDisplayRecords" => 0, "aaData" => array()));
            exit;
        }
        switch ($_REQUEST['iSortCol_0']) {
            case 0:
                $field = "profiletype";
                break;
            case 1:
                $field = "partno";
                break;
            case 2:
                $field = "pf";
                break;
            case 3:
                $field = "mult";
                break;
            case 4:
                $field = "expire_date";
                break;
            case 5:
                $field = "effective_date";
                break;
            case 6:
                $field = "review_date";
                break;
            case 7:
                $field = "basetype";
                break;
            case 8:
                $field = "price";

            default:
                $field = "partno";
                break;
        }
        $like_def = "";
        if (!empty($_REQUEST['sSearch'])) {
            $like_def = " AND (ddop.profiletype LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddop.partno LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
                ddop.pf LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddop.mult LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddop.expire_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            ddop.effective_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR ddop.basetype LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            ddop.price LIKE '%" . $_REQUEST['sSearch'] . "%') ";
        }
        $query = "select 'default' as typeRow,ddop.profiletype, ddop.partno, ddop.pf, ddop.mult, 
			ddop.expire_date, ddop.effective_date, '', ddop.basetype, ddop.price
			from  dsls_disc_ovr_product_default ddop
			where ddop.defaultid = '" . $dealertype_s_default . "' and ddop.partno is not null
                                                        $like_def
               ORDER BY " . $field . " " . $_REQUEST['sSortDir_0'] . " LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];
        $query_count = "select count(*) as total
			from dsls_disc_ovr_product_default ddop
			where ddop.defaultid = '" . $dealertype_s_default . "' and ddop.partno is not null
                                                        $like_def";

        $query_total_res = $focus->db->query($query_count);
        $query_total = $focus->db->fetchByAssoc($query_total_res);
        $totalRecords = $query_total['total'];

        $result = $focus->db->query($query);
        $norm_line_list = array();
        while (($row = $focus->db->fetchByAssoc($result)) != null) {
                $norm_line_list[] = array($row['profiletype'],
                    $row['partno'],
                    $timedate->to_display_date($row['effective_date'], false),
                    $timedate->to_display_date($row['review_date'], false),
                    $timedate->to_display_date($row['expire_date'], false),
                    $row['basetype'],
                    $row['pf'],
                    $row['mult'],
                    $row['price']
                );
        }

        print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => $totalRecords, "iTotalDisplayRecords" => $totalRecords, "aaData" => $norm_line_list));
        exit;
    }

    public function action_getNormLineException() {
        global $timedate;
        require_once("modules/Accounts/Account.php");
        $focus = new Account();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }

        $like_exep = "";

        if (!empty($_REQUEST['sSearch'])) {
            $like_exep = " AND (pec.profiletype LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.line LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
                pec.cat LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.pc LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.pf LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            pec.mult LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.expire_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            pec.effective_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.review_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            pec.accel LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.round LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.strategic LIKE '%" . $_REQUEST['sSearch'] . "%') ";
        }

        switch ($_REQUEST['iSortCol_0']) {
            case 0:
                $field = "profiletype";
                break;
            case 1:
                $field = "line";
                break;
            case 2:
                $field = "cat+0";
                break;
            case 3:
                $field = "pc";
                break;
            case 4:
                $field = "expire_date";
                break;
            case 5:
                $field = "effective_date";
                break;
            case 6:
                $field = "review_date";
                break;
            case 7:
                $field = "pf";
                break;
            case 8:
                $field = "mult";
                break;
            case 9:
                $field = "accel";
                break;
            case 10:
                $field = "round";
                break;
            case 11:
                $field = "strategic";
                break;

            default:
                $field = "line,cat,pc";
                break;
        }

        $query = "select  'exception' as typeRow, pec.profiletype, pec.line, pec.cat, pec.pc, pec.pf, pec.mult, 
			pec.expire_date, pec.effective_date, pec.review_date,pec.accel,pec.round, pec.strategic 
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'N' and Line is not null
                                    $like_exep
                                    $union 
               ORDER BY " . $field . " " . $_REQUEST['sSortDir_0'] . " LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];
        $query_count = "select count(*) as total
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'N' and Line is not null $like_exep";

        $query_total_res = $focus->db->query($query_count);
        $query_total = $focus->db->fetchByAssoc($query_total_res);
        $totalRecords = $query_total['total'];

        $result = $focus->db->query($query);
        $norm_line_list = array();
        while (($row = $focus->db->fetchByAssoc($result)) != null) {
                $norm_line_list[] = array($row['profiletype'],
                    $row['line'],
                    $row['cat'],
                    $row['pc'],
                    $timedate->to_display_date($row['effective_date'], false),
                    $timedate->to_display_date($row['review_date'], false),
                    $timedate->to_display_date($row['expire_date'], false),
                    $row['pf'],
                    $row['mult'],
                    $row['accel'],
                    $row['round'],
                    $row['strategic']
                );
        }

        print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => $totalRecords, "iTotalDisplayRecords" => $totalRecords, "aaData" => $norm_line_list));
        exit;
    }

    public function action_getNormProdException() {
        global $timedate;
        require_once("modules/Accounts/Account.php");
        $focus = new Account();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }
 
        $like_exep = "";

        if (!empty($_REQUEST['sSearch'])) {
            $like_exep = " AND (pec.profiletype LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.partno LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
                pec.pf LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.mult LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.expire_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            pec.effective_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.review_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            pec.basetype LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.price LIKE '%" . $_REQUEST['sSearch'] . "%') ";
        }

        switch ($_REQUEST['iSortCol_0']) {
            case 0:
                $field = "profiletype";
                break;
            case 1:
                $field = "partno";
                break;
            case 2:
                $field = "pf";
                break;
            case 3:
                $field = "mult";
                break;
            case 4:
                $field = "expire_date";
                break;
            case 5:
                $field = "effective_date";
                break;
            case 6:
                $field = "review_date";
                break;
            case 7:
                $field = "basetype";
                break;
            case 8:
                $field = "price";

            default:
                $field = "partno";
                break;
        }

        $query = "select  'exception' as typeRow, pec.profiletype, pec.partno, pec.pf, pec.mult, 
			pec.expire_date, pec.effective_date, pec.review_date, pec.basetype, pec.price
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'N' and pec.partno is not null
                                    $like_exep
                                    $union 
               ORDER BY " . $field . " " . $_REQUEST['sSortDir_0'] . " LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];
        $query_count = "select count(*) as total
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'N' and Line is not null $like_exep";
 
        $query_total_res = $focus->db->query($query_count);
        $query_total = $focus->db->fetchByAssoc($query_total_res);
        $totalRecords = $query_total['total'];

        $result = $focus->db->query($query);
        $norm_line_list = array();
        while (($row = $focus->db->fetchByAssoc($result)) != null) {
                $norm_line_list[] = array($row['profiletype'],
                    $row['partno'],
                    $timedate->to_display_date($row['effective_date'], false),
                    $timedate->to_display_date($row['review_date'], false),
                    $timedate->to_display_date($row['expire_date'], false),
                    $row['basetype'],
                    $row['pf'],
                    $row['mult'],
                    $row['price']
                );
        }

        print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => $totalRecords, "iTotalDisplayRecords" => $totalRecords, "aaData" => $norm_line_list));
        exit;
    }

    public function action_getStockLineException() {
        global $timedate;
        require_once("modules/Accounts/Account.php");
        $focus = new Account();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }

        $like_exep = "";

        if (!empty($_REQUEST['sSearch'])) {
            $like_exep = " AND (pec.profiletype LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.line LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
                pec.cat LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.pc LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.pf LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            pec.mult LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.expire_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            pec.effective_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.review_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            pec.accel LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.round LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.strategic LIKE '%" . $_REQUEST['sSearch'] . "%') ";
        }

        switch ($_REQUEST['iSortCol_0']) {
            case 0:
                $field = "profiletype";
                break;
            case 1:
                $field = "line";
                break;
            case 2:
                $field = "cat+0";
                break;
            case 3:
                $field = "pc";
                break;
            case 4:
                $field = "expire_date";
                break;
            case 5:
                $field = "effective_date";
                break;
            case 6:
                $field = "review_date";
                break;
            case 7:
                $field = "pf";
                break;
            case 8:
                $field = "mult";
                break;
            case 9:
                $field = "accel";
                break;
            case 10:
                $field = "round";
                break;
            case 11:
                $field = "strategic";
                break;

            default:
                $field = "line,cat,pc";
                break;
        }

        $query = "select  'exception' as typeRow, pec.profiletype, pec.line, pec.cat, pec.pc, pec.pf, pec.mult, 
			pec.expire_date, pec.effective_date, pec.review_date,pec.accel,pec.round, pec.strategic 
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'S' and Line is not null
                                    $like_exep
                                    $union 
               ORDER BY " . $field . " " . $_REQUEST['sSortDir_0'] . " LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];
        $query_count = "select count(*) as total
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'S' and Line is not null $like_exep";

        $query_total_res = $focus->db->query($query_count);
        $query_total = $focus->db->fetchByAssoc($query_total_res);
        $totalRecords = $query_total['total'];

        $result = $focus->db->query($query);
        $norm_line_list = array();
        while (($row = $focus->db->fetchByAssoc($result)) != null) {
                $norm_line_list[] = array($row['profiletype'],
                    $row['line'],
                    $row['cat'],
                    $row['pc'],
                    $timedate->to_display_date($row['effective_date'], false),
                    $timedate->to_display_date($row['review_date'], false),
                    $timedate->to_display_date($row['expire_date'], false),
                    $row['pf'],
                    $row['mult'],
                    $row['accel'],
                    $row['round'],
                    $row['strategic']
                );
        }

        print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => $totalRecords, "iTotalDisplayRecords" => $totalRecords, "aaData" => $norm_line_list));
        exit;
    }

    public function action_getStockProdException() {
        global $timedate;
        require_once("modules/Accounts/Account.php");
        $focus = new Account();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }

        $like_exep = "";

        if (!empty($_REQUEST['sSearch'])) {
            $like_exep = " AND (pec.profiletype LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.partno LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
                pec.pf LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.mult LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.expire_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            pec.effective_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.review_date LIKE '%" . $_REQUEST['sSearch'] . "%' OR 
            pec.basetype LIKE '%" . $_REQUEST['sSearch'] . "%' OR pec.price LIKE '%" . $_REQUEST['sSearch'] . "%') ";
        }

        switch ($_REQUEST['iSortCol_0']) {
            case 0:
                $field = "profiletype";
                break;
            case 1:
                $field = "partno";
                break;
            case 2:
                $field = "pf";
                break;
            case 3:
                $field = "mult";
                break;
            case 4:
                $field = "expire_date";
                break;
            case 5:
                $field = "effective_date";
                break;
            case 6:
                $field = "review_date";
                break;
            case 7:
                $field = "basetype";
                break;
            case 8:
                $field = "price";

            default:
                $field = "partno";
                break;
        }

        $query = "select  'exception' as typeRow, pec.profiletype, pec.partno, pec.pf, pec.mult, 
			pec.expire_date, pec.effective_date, pec.review_date, pec.basetype, pec.price
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'S' and pec.partno is not null
                                    $like_exep
                                    $union 
               ORDER BY " . $field . " " . $_REQUEST['sSortDir_0'] . " LIMIT " . $_GET['iDisplayStart'] . ", " . $_GET['iDisplayLength'];
        $query_count = "select count(*) as total
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'S' and Line is not null $like_exep";
  
        $query_total_res = $focus->db->query($query_count);
        $query_total = $focus->db->fetchByAssoc($query_total_res);
        $totalRecords = $query_total['total'];

        $result = $focus->db->query($query);
        $norm_line_list = array();
        while (($row = $focus->db->fetchByAssoc($result)) != null) {
                $norm_line_list[] = array($row['profiletype'],
                    $row['partno'],
                    $timedate->to_display_date($row['effective_date'], false),
                    $timedate->to_display_date($row['review_date'], false),
                    $timedate->to_display_date($row['expire_date'], false),
                    $row['basetype'],
                    $row['pf'],
                    $row['mult'],
                    $row['price']
                );
        }

        print json_encode(array("sEcho" => $_GET["sEcho"], "iTotalRecords" => $totalRecords, "iTotalDisplayRecords" => $totalRecords, "aaData" => $norm_line_list));
        exit;
    }

}
