<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

require_once('data/SugarBean.php');

class Pricing extends SugarBean {
    var $module_dir = "Pricing";
    var $object_name = "Pricing";
    // This is used to retrieve related fields from form posts.
    var $additional_column_fields = Array();

    function Audit() {
        parent::SugarBean();
    }

    var $new_schema = true;

    function get_summary_text() {
        return $this->name;
    }

    function create_list_query($order_by, $where, $show_deleted=0) {
        
    }

    function create_export_query(&$order_by, &$where) {
        
    }

    function fill_in_additional_list_fields() {
        
    }

    function fill_in_additional_detail_fields() {
        
    }

    function fill_in_additional_parent_fields() {
        
    }

    function get_list_view_data() {
        
    }

    function get_audit_link() {
        
    }

    function get_norm_line() {
        global $focus, $genericAssocFieldsArray, $moduleAssocFieldsArray, $current_user, $timedate, $app_strings;
        $norm_line_list = array();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }
        $query = "select a.custid_c, pec.profiletype, pec.line, pec.cat, pec.pc, pec.pf, pec.mult, 
			pec.expire_date, pec.effective_date, pec.review_date,pec.accel,pec.round, pec.strategic 
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'N' and Line is not null";
        $result = $focus->db->query($query);
        $temp_list = array();
        $n = 0;
        while (($row = $focus->db->fetchByAssoc($result)) != null) {

            $temp_list['profiletype'][$n] = $row['profiletype'];
            $temp_list['line'][$n] = $row['line'];
            $temp_list['cat'][$n] = $row['cat'];
            $temp_list['pc'][$n] = $row['pc'];
            $temp_list['pf'][$n] = $row['pf'];
            $temp_list['mult'][$n] = $row['mult'];
            $temp_list['expire_date'][$n] = $timedate->to_display_date($row['expire_date'], false);
            $temp_list['effective_date'][$n] = $timedate->to_display_date($row['effective_date'], false);
            $temp_list['review_date'][$n] = $timedate->to_display_date($row['review_date'], false);
            $temp_list['accel'][$n] = $row['accel'];
            $temp_list['round'][$n] = $row['round'];
            $temp_list['strategic'][$n] = $row['strategic'];
            $n = $n + 1;
        }
        $norm_line_list = $temp_list;
        return $norm_line_list;
    }

//  START
    /////////Normal Line////////////////////////Normal Line///////////////////Normal Line///////////////////////Normal Line////////
    function get_norm_line_default() {

        global $focus, $genericAssocFieldsArray, $moduleAssocFieldsArray, $current_user, $timedate, $app_strings;
        $norm_line_list = array();
	
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }
        if (!strstr($focus->fulldealertype_c, '*')) {
	//no *
            $dealertype_n_default = substr($focus->fulldealertype_c, 4, 2);
        } else {
            $splited_data = explode('*', $focus->fulldealertype_c);
	    $dealertype_n_default = $splited_data[1];
	    if(strlen($dealertype_n_default>2)){
		$dealertype_n_default = substr($dealertype_n_default,0,1);
		}
        }
        if ($dealertype_n_default == '') {
            return array();
        }
        $query = "select *
			from dsls_disc_ovr_line_default ddold
			where ddold.defaultid='" . $dealertype_n_default . "' and line is not null";
//                $query = "select * 
//			from dsls_disc_ovr_line_default ddold
//			where ddold.profiletype = 'N' and line is not null";
        $result = $focus->db->query($query);

        $temp_list = array();
        $n = 0;
        while (($row = $focus->db->fetchByAssoc($result)) != null) {

            $temp_list['profiletype'][$n] = $row['profiletype'];
            $temp_list['line'][$n] = $row['line'];
            $temp_list['cat'][$n] = $row['cat'];
            $temp_list['pc'][$n] = $row['pc'];
            $temp_list['pf'][$n] = $row['pf'];
            $temp_list['mult'][$n] = $row['mult'];
            $temp_list['expire_date'][$n] = $timedate->to_display_date($row['expire_date'], false);
            $temp_list['effective_date'][$n] = $timedate->to_display_date($row['effective_date'], false);
//            $temp_list['review_date'][$n] = $timedate->to_display_date($row['review_date'], false);
            $temp_list['accel'][$n] = $row['acceleration_pricing'];
            $temp_list['round'][$n] = $row['price_rounding'];
            $temp_list['strategic'][$n] = $row['strategic_code'];
            $n = $n + 1;
        }



        $norm_line_list = $temp_list;


        return $norm_line_list;
    }

    /////////Normal Line////////////////////////Normal Line///////////////////Normal Line///////////////////////Normal Line////////
    //END
//end function get_norm_line
    function get_norm_line_default_exeption() {

        global $focus, $genericAssocFieldsArray, $moduleAssocFieldsArray, $current_user, $timedate, $app_strings;
        $norm_line_list = array();
	
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }
        if (!strstr($focus->fulldealertype_c, '*')) {
	//no *
            $dealertype_n_default = substr($focus->fulldealertype_c, 4, 2);
        } else {
            $splited_data = explode('*', $focus->fulldealertype_c);
	    $dealertype_n_default = $splited_data[1];
	    if(strlen($dealertype_n_default>2)){
		$dealertype_n_default = substr($dealertype_n_default,0,1);
		}
        }
        $union = '';
        if ($dealertype_n_default != '') {
            $union = " union
select 'default' as typeRow,  ddold.profiletype,ddold.line,ddold.cat, ddold.pc, ddold.pf, ddold.mult, 
			ddold.expire_date, ddold.effective_date, '','','', ''
			from dsls_disc_ovr_line_default ddold
			where ddold.defaultid = '" . $dealertype_n_default . "' and line is not null";
        }
        $query = "select  'exeption' as typeRow, pec.profiletype, pec.line, pec.cat, pec.pc, pec.pf, pec.mult, 
			pec.expire_date, pec.effective_date, pec.review_date,pec.accel,pec.round, pec.strategic 
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'N' and Line is not null
                                    $union";

        $result = $focus->db->query($query);

        $temp_list = array();
        $n = 0;
        while (($row = $focus->db->fetchByAssoc($result)) != null) {
            $temp_list['typeRow'][$n] = $row['typeRow'];
            $temp_list['profiletype'][$n] = $row['profiletype'];
            $temp_list['line'][$n] = $row['line'];
            $temp_list['cat'][$n] = $row['cat'];
            $temp_list['pc'][$n] = $row['pc'];
            $temp_list['pf'][$n] = $row['pf'];
            $temp_list['mult'][$n] = $row['mult'];
            $temp_list['expire_date'][$n] = $timedate->to_display_date($row['expire_date'], false);
            $temp_list['effective_date'][$n] = $timedate->to_display_date($row['effective_date'], false);
            $temp_list['review_date'][$n] = $timedate->to_display_date($row['review_date'], false);
            $temp_list['accel'][$n] = $row['acceleration_pricing'];
            $temp_list['round'][$n] = $row['price_rounding'];
            $temp_list['strategic'][$n] = $row['strategic_code'];
            $n = $n + 1;
        }



        $norm_line_list = $temp_list;


        return $norm_line_list;
    }
    
    function get_stock_line() {
        global $focus, $genericAssocFieldsArray, $moduleAssocFieldsArray, $current_user, $timedate, $app_strings;
        $stock_line_list = array();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }
        $query = "select a.custid_c, pec.profiletype, pec.line, pec.cat, pec.pc,pec.pf, pec.mult, 
			pec.expire_date, pec.effective_date,pec.review_date,pec.accel,pec.round, pec.strategic 
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'S' and Line is not null";
        $result = $focus->db->query($query);
        $temp_list = array();
        $n = 0;
        while (($row = $focus->db->fetchByAssoc($result)) != null) {

            $temp_list['profiletype'][$n] = $row['profiletype'];
            $temp_list['line'][$n] = $row['line'];
            $temp_list['cat'][$n] = $row['cat'];
            $temp_list['pc'][$n] = $row['pc'];
            $temp_list['pf'][$n] = $row['pf'];
            $temp_list['mult'][$n] = $row['mult'];
            $temp_list['expire_date'][$n] = $timedate->to_display_date($row['expire_date'], false);
            $temp_list['effective_date'][$n] = $timedate->to_display_date($row['effective_date'], false);
            $temp_list['review_date'][$n] = $timedate->to_display_date($row['review_date'], false);
            $temp_list['accel'][$n] = $row['accel'];
            $temp_list['round'][$n] = $row['round'];
            $temp_list['strategic'][$n] = $row['strategic'];
            $n = $n + 1;
        }
        $stock_line_list = $temp_list;
        return $stock_line_list;
    }

//  START
/////////////Stock Line//////////////////////////////////////////Stock Line/////////////////////////////////////Stock Line
    function get_stock_line_default() {
        global $focus, $genericAssocFieldsArray, $moduleAssocFieldsArray, $current_user, $timedate, $app_strings;
        $stock_line_list = array();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }
        if (!strstr($focus->fulldealertype_c, '*')) {
            $dealertype_s_default = substr($focus->fulldealertype_c, 6, 2);
        } else {
            $splited_data = explode('*', $focus->fulldealertype_c);
		if(strlen($dealertype_n_default>2)){
                  $dealertype_n_default = substr($dealertype_n_default,1,2);
                }

            $dealertype_s_default = $splited_data[2];
        }

//        $query = "select a.custid_c, pec.profiletype, pec.line, pec.cat, pec.pc,pec.pf, pec.mult, 
//			pec.expire_date, pec.effective_date,pec.review_date,pec.accel,pec.round, pec.strategic 
//			from accounts a
//		join price_except_by_custid pec on pec.custid = a.custid_c 
//			where a.id = '" . $focus->id . "' and pec.profiletype = 'S' and Line is not null";
        if ($dealertype_s_default == '') {
            return array();
        }

        $query = "select *
			from dsls_disc_ovr_line_default ddold 
			where ddold.defaultid = '" . $dealertype_s_default . "' and line is not null";

        $result = $focus->db->query($query);
        $temp_list = array();
        $n = 0;
        while (($row = $focus->db->fetchByAssoc($result)) != null) {

            $temp_list['profiletype'][$n] = $row['profiletype'];
            $temp_list['line'][$n] = $row['line'];
            $temp_list['cat'][$n] = $row['cat'];
            $temp_list['pc'][$n] = $row['pc'];
            $temp_list['pf'][$n] = $row['pf'];
            $temp_list['mult'][$n] = $row['mult'];
            $temp_list['expire_date'][$n] = $timedate->to_display_date($row['expire_date'], false);
            $temp_list['effective_date'][$n] = $timedate->to_display_date($row['effective_date'], false);
            // $temp_list['review_date'][$n] = $timedate->to_display_date($row['review_date'], false);
            $temp_list['accel'][$n] = $row['acceleration_pricing'];
            $temp_list['round'][$n] = $row['price_rounding'];
            $temp_list['strategic'][$n] = $row['strategic_code'];
            $n = $n + 1;
        }
        $stock_line_list = $temp_list;
        return $stock_line_list;
    }

/////////////Stock Line//////////////////////////////////////////Stock Line/////////////////////////////////////Stock Line
//END
//end function get_stock_line
    function get_stock_line_default_exeption() {
        global $focus, $genericAssocFieldsArray, $moduleAssocFieldsArray, $current_user, $timedate, $app_strings;
              $stock_line_list = array();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }
        if (!strstr($focus->fulldealertype_c, '*')) {
            $dealertype_s_default = substr($focus->fulldealertype_c, 6, 2);
        } else {
            $splited_data = explode('*', $focus->fulldealertype_c);
		if(strlen($dealertype_n_default>2)){
                  $dealertype_n_default = substr($dealertype_n_default,1,2);
                }

            $dealertype_s_default = $splited_data[2];
        }
           $union = '';
        if ($dealertype_s_default != '') {
            $union = " union
select 'default' as typeRow,ddold.profiletype, ddold.line, ddold.cat, ddold.pc,ddold.pf, ddold.mult, 
			ddold.expire_date, ddold.effective_date,'','','',''
			from dsls_disc_ovr_line_default ddold 
			where ddold.defaultid = '" . $dealertype_s_default . "' and line is not null";
        }
        $query = "select 'exeption' as typeRow, pec.profiletype, pec.line, pec.cat, pec.pc,pec.pf, pec.mult, 
			pec.expire_date, pec.effective_date,pec.review_date,pec.accel,pec.round, pec.strategic 
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'S' and Line is not null
                                    $union";
        $result = $focus->db->query($query);
        $temp_list = array();
        $n = 0;
        while (($row = $focus->db->fetchByAssoc($result)) != null) {
            $temp_list['typeRow'][$n] = $row['typeRow'];
            $temp_list['profiletype'][$n] = $row['profiletype'];
            $temp_list['line'][$n] = $row['line'];
            $temp_list['cat'][$n] = $row['cat'];
            $temp_list['pc'][$n] = $row['pc'];
            $temp_list['pf'][$n] = $row['pf'];
            $temp_list['mult'][$n] = $row['mult'];
            $temp_list['expire_date'][$n] = $timedate->to_display_date($row['expire_date'], false);
            $temp_list['effective_date'][$n] = $timedate->to_display_date($row['effective_date'], false);
            $temp_list['review_date'][$n] = $timedate->to_display_date($row['review_date'], false);
            $temp_list['accel'][$n] = $row['accel'];
            $temp_list['round'][$n] = $row['round'];
            $temp_list['strategic'][$n] = $row['strategic'];
            $n = $n + 1;
        }
        $stock_line_list = $temp_list;
        return $stock_line_list;
    }
    
    function get_norm_product() {
        global $focus, $genericAssocFieldsArray, $moduleAssocFieldsArray, $current_user, $timedate, $app_strings;
        $norm_prod_list = array();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }
        $query = "select a.custid_c, pec.profiletype, pec.partno, pec.pf, pec.mult, 
			pec.expire_date, pec.effective_date, pec.review_date, pec.basetype, pec.price
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'N' and pec.partno is not null";
        $result = $focus->db->query($query);
        $temp_list = array();
        $n = 0;
        while (($row = $focus->db->fetchByAssoc($result)) != null) {

            $temp_list['profiletype'][$n] = $row['profiletype'];
            $temp_list['partno'][$n] = $row['partno'];
            $temp_list['pf'][$n] = $row['pf'];
            $temp_list['mult'][$n] = $row['mult'];
            $temp_list['expire_date'][$n] = $timedate->to_display_date($row['expire_date'], false);
            $temp_list['effective_date'][$n] = $timedate->to_display_date($row['effective_date'], false);
            $temp_list['review_date'][$n] = $timedate->to_display_date($row['review_date'], false);
            $temp_list['basetype'][$n] = $row['basetype'];
            $temp_list['price'][$n] = $row['price'];
            $n = $n + 1;
        }
        $norm_prod_list = $temp_list;
        return $norm_prod_list;
    }

//  START
    //////////////////////////////////////Normal Part///////////Normal Part////////////Normal Part///////////////Normal Part
    function get_norm_product_default() {
        global $focus, $genericAssocFieldsArray, $moduleAssocFieldsArray, $current_user, $timedate, $app_strings;
        $norm_prod_list = array();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }

        if (!strstr($focus->fulldealertype_c, '*')) {
            $dealertype_n_default = substr($focus->fulldealertype_c, 4, 2);
        } else {
            $splited_data = explode('*', $focus->fulldealertype_c);
            $dealertype_n_default = $splited_data[1];
        }



//        $query = "select a.custid_c, pec.profiletype, pec.partno, pec.pf, pec.mult, 
//			pec.expire_date, pec.effective_date, pec.review_date, pec.basetype, pec.price
//			from accounts a
//		join price_except_by_custid pec on pec.custid = a.custid_c 
//			where a.id = '" . $focus->id . "' and pec.profiletype = 'N' and pec.partno is not null";

        if ($dealertype_n_default == '') {
            return array();
        }

        $query = "select *
			from  dsls_disc_ovr_product_default ddop
			where ddop.defaultid = '" . $dealertype_n_default . "' and ddop.partno is not null";
        $result = $focus->db->query($query);
        $temp_list = array();
        $n = 0;
        while (($row = $focus->db->fetchByAssoc($result)) != null) {

            $temp_list['profiletype'][$n] = $row['profiletype'];
            $temp_list['partno'][$n] = $row['partno'];
            $temp_list['pf'][$n] = $row['pf'];
            $temp_list['mult'][$n] = $row['mult'];
            $temp_list['expire_date'][$n] = $timedate->to_display_date($row['expire_date'], false);
            $temp_list['effective_date'][$n] = $timedate->to_display_date($row['effective_date'], false);
            //$temp_list['review_date'][$n] = $timedate->to_display_date($row['review_date'], false);
            $temp_list['basetype'][$n] = $row['basetype'];
            $temp_list['price'][$n] = $row['price'];
            $n = $n + 1;
        }
        $norm_prod_list = $temp_list;
        return $norm_prod_list;
    }

    //////////////////////////////////////Normal Part///////////Normal Part////////////Normal Part///////////////Normal Part
    //END
//end func get_norm_product
 function get_norm_product_default_exeption() {
        global $focus, $genericAssocFieldsArray, $moduleAssocFieldsArray, $current_user, $timedate, $app_strings;
        $norm_prod_list = array();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }
        if (!strstr($focus->fulldealertype_c, '*')) {
            $dealertype_n_default = substr($focus->fulldealertype_c, 4, 2);
        } else {
            $splited_data = explode('*', $focus->fulldealertype_c);
            $dealertype_n_default = $splited_data[1];
        }
        $union = '';
        if ($dealertype_n_default != '') {
            $union = " union 
                            select 'default' as typeRow,ddop.profiletype, ddop.partno, ddop.pf, ddop.mult, 
			ddop.expire_date, ddop.effective_date, '', ddop.basetype, ddop.price
			from  dsls_disc_ovr_product_default ddop
			where ddop.defaultid = '" . $dealertype_n_default . "' and ddop.partno is not null";
        }
        $query = "select 'exeption' as typeRow,pec.profiletype, pec.partno, pec.pf, pec.mult, 
			pec.expire_date, pec.effective_date, pec.review_date, pec.basetype, pec.price
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'N' and pec.partno is not null
                                    $union";
        $result = $focus->db->query($query);
        $temp_list = array();
        $n = 0;
        while (($row = $focus->db->fetchByAssoc($result)) != null) {
            $temp_list['typeRow'][$n] = $row['typeRow'];
            $temp_list['profiletype'][$n] = $row['profiletype'];
            $temp_list['partno'][$n] = $row['partno'];
            $temp_list['pf'][$n] = $row['pf'];
            $temp_list['mult'][$n] = $row['mult'];
            $temp_list['expire_date'][$n] = $timedate->to_display_date($row['expire_date'], false);
            $temp_list['effective_date'][$n] = $timedate->to_display_date($row['effective_date'], false);
            $temp_list['review_date'][$n] = $timedate->to_display_date($row['review_date'], false);
            $temp_list['basetype'][$n] = $row['basetype'];
            $temp_list['price'][$n] = $row['price'];
            $n = $n + 1;
        }
        $norm_prod_list = $temp_list;
        return $norm_prod_list;
    }
    
    function get_stock_product() {
        global $focus, $genericAssocFieldsArray, $moduleAssocFieldsArray, $current_user, $timedate, $app_strings;
        $stock_prod_list = array();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }
        $query = "select a.custid_c, pec.profiletype, pec.partno, pec.pf, pec.mult, 
			pec.expire_date, pec.effective_date, pec.review_date, pec.basetype, pec.price
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'S' and pec.partno is not null";
        $result = $focus->db->query($query);
        $temp_list = array();
        $n = 0;
        while (($row = $focus->db->fetchByAssoc($result)) != null) {

            $temp_list['profiletype'][$n] = $row['profiletype'];
            $temp_list['partno'][$n] = $row['partno'];
            $temp_list['pf'][$n] = $row['pf'];
            $temp_list['mult'][$n] = $row['mult'];
            $temp_list['expire_date'][$n] = $timedate->to_display_date($row['expire_date'], false);
            $temp_list['effective_date'][$n] = $timedate->to_display_date($row['effective_date'], false);
            $temp_list['review_date'][$n] = $timedate->to_display_date($row['review_date'], false);
            $temp_list['basetype'][$n] = $row['basetype'];
            $temp_list['price'][$n] = $row['price'];
            $n = $n + 1;
        }
        $stock_prod_list = $temp_list;
        return $stock_prod_list;
    }

    //START
//Stock Part/////////////////Stock Part///////////////////Stock Part/////////////////////////
    function get_stock_product_default() {
        global $focus, $genericAssocFieldsArray, $moduleAssocFieldsArray, $current_user, $timedate, $app_strings;
        $stock_prod_list = array();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }


        if (!strstr($focus->fulldealertype_c, '*')) {
            $dealertype_s_default = substr($focus->fulldealertype_c, 6, 2);
        } else {
            $splited_data = explode('*', $focus->fulldealertype_c);
            $dealertype_s_default = $splited_data[2];
        }


//        $query = "select a.custid_c, pec.profiletype, pec.partno, pec.pf, pec.mult, 
//			pec.expire_date, pec.effective_date, pec.review_date, pec.basetype, pec.price
//			from accounts a
//		join price_except_by_custid pec on pec.custid = a.custid_c 
//			where a.id = '" . $focus->id . "' and pec.profiletype = 'S' and pec.partno is not null";
        if ($dealertype_s_default == '') {
            return array();
        }

        $query = "select *
			from dsls_disc_ovr_product_default ddop
			where ddop.defaultid = '" . $dealertype_s_default . "' and ddop.partno is not null";
        $result = $focus->db->query($query);
        $temp_list = array();
        $n = 0;
        while (($row = $focus->db->fetchByAssoc($result)) != null) {

            $temp_list['profiletype'][$n] = $row['profiletype'];
            $temp_list['partno'][$n] = $row['partno'];
            $temp_list['pf'][$n] = $row['pf'];
            $temp_list['mult'][$n] = $row['mult'];
            $temp_list['expire_date'][$n] = $timedate->to_display_date($row['expire_date'], false);
            $temp_list['effective_date'][$n] = $timedate->to_display_date($row['effective_date'], false);
            //$temp_list['review_date'][$n] = $timedate->to_display_date($row['review_date'], false);
            $temp_list['basetype'][$n] = $row['basetype'];
            $temp_list['price'][$n] = $row['price'];
            $n = $n + 1;
        }
        $stock_prod_list = $temp_list;
        return $stock_prod_list;
    }

//Stock Part/////////////////Stock Part///////////////////Stock Part/////////////////////////
    //END
//end func get_stock_prod
    
    function get_stock_product_default_exeption() {
        global $focus, $genericAssocFieldsArray, $moduleAssocFieldsArray, $current_user, $timedate, $app_strings;
        $stock_prod_list = array();
        if (!empty($_REQUEST['record'])) {
            $result = $focus->retrieve($_REQUEST['record']);
        }

        if (!strstr($focus->fulldealertype_c, '*')) {
            $dealertype_s_default = substr($focus->fulldealertype_c, 6, 2);
        } else {
            $splited_data = explode('*', $focus->fulldealertype_c);
            $dealertype_s_default = $splited_data[2];
        }
         $union = '';
        if ($dealertype_s_default != '') {
            $union = " union 
                            select 'default' as typeRow,ddop.profiletype, ddop.partno, ddop.pf, ddop.mult, 
			ddop.expire_date, ddop.effective_date, '', ddop.basetype, ddop.price
			from dsls_disc_ovr_product_default ddop
			where ddop.defaultid = '" . $dealertype_s_default . "' and ddop.partno is not null";
        }
        $query = "select 'exeption' as typeRow, pec.profiletype, pec.partno, pec.pf, pec.mult, 
			pec.expire_date, pec.effective_date, pec.review_date, pec.basetype, pec.price
			from accounts a
		join price_except_by_custid pec on pec.custid = a.custid_c 
			where a.id = '" . $focus->id . "' and pec.profiletype = 'S' and pec.partno is not null
                            $union";
        $result = $focus->db->query($query);
        $temp_list = array();
        $n = 0;
        while (($row = $focus->db->fetchByAssoc($result)) != null) {
            $temp_list['typeRow'][$n] = $row['typeRow'];
            $temp_list['profiletype'][$n] = $row['profiletype'];
            $temp_list['partno'][$n] = $row['partno'];
            $temp_list['pf'][$n] = $row['pf'];
            $temp_list['mult'][$n] = $row['mult'];
            $temp_list['expire_date'][$n] = $timedate->to_display_date($row['expire_date'], false);
            $temp_list['effective_date'][$n] = $timedate->to_display_date($row['effective_date'], false);
            $temp_list['review_date'][$n] = $timedate->to_display_date($row['review_date'], false);
            $temp_list['basetype'][$n] = $row['basetype'];
            $temp_list['price'][$n] = $row['price'];
            $n = $n + 1;
        }
        $stock_prod_list = $temp_list;
        return $stock_prod_list;
    }
}

?>
