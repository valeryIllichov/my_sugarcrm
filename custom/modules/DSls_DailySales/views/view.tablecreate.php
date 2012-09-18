<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
require_once('include/MVC/View/SugarView.php');
require_once('modules/ZuckerReportParameter/fmp.class.param.slsm.php');
require_once('modules/ZuckerReportParameter/fmp.class.param.regloc.php');
require_once('include/database/PearDatabase.php');

class DSls_DailySalesViewTableCreate extends SugarView {
    function DSls_DailySalesViewTableCreate() {
        parent::SugarView();
        $this->options['show_header'] = false;
        $this->options['show_footer'] = false;
        $this->options['show_search'] = false;
        $this->options['show_javascript'] = false;


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


    function acl_where() {
                global $current_user;
	//(strlen($_REQUEST['select']) > 0 && $_REQUEST['select'] != 'undefined') ? $selectMethod=$_REQUEST['select'] : $selectMethod=null;
	//(strlen($_REQUEST['reg_loc']) > 0  && (substr($_REQUEST['reg_loc'], 0, 1) != 'r' && ['reg_loc'] != 'undefined' && $_REQUEST['reg_loc'] != 'all')) ? $location=explode(';',$_REQUEST['reg_loc']) : $location=null;
	//(strlen($_REQUEST['reg_loc']) > 0  && (substr($_REQUEST['reg_loc'], 0, 1) == 'r' && $_REQUEST['reg_loc'] != 'undefined' && $_REQUEST['reg_loc'] != 'all')) ? $region= substr($_REQUEST['reg_loc'], 1) : $region=null;
                $daily_sales_Slsm = ($current_user->getPreference('daily_sales_Slsm'))? $current_user->getPreference('daily_sales_Slsm'): '';
                $daily_sales_Reg_loc = ($current_user->getPreference('daily_sales_Reg_loc'))? $current_user->getPreference('daily_sales_Reg_loc'): '';
                $daily_sales_Dealer_post = ($current_user->getPreference('daily_sales_Dealer_post'))? $current_user->getPreference('daily_sales_Dealer_post'): '';
                (strlen($daily_sales_Slsm) > 0 && ($daily_sales_Slsm != 'undefined' && $daily_sales_Slsm != 'all')) ? $slsm=explode(';',$daily_sales_Slsm) : $slsm=null;
                (strlen($daily_sales_Dealer_post) > 0 && ($daily_sales_Dealer_post != 'undefined' && $daily_sales_Dealer_post != 'all')) ? $dealerType=explode(';',$daily_sales_Dealer_post) : $dealerType=null;
        include_once("521/FMPSales.php");

        FMPSales::initialize521();


	$record_reg_loc = !in_array($daily_sales_Reg_loc, array('undefined', 'all', '')) ? explode(";", $daily_sales_Reg_loc) : null;
	
	if(!is_null($record_reg_loc)) {
		foreach($record_reg_loc as $reg_loc_value) {
			if(substr($reg_loc_value, 0, 1) == "r") {
				$region[substr($reg_loc_value, 1)] = substr($reg_loc_value, 1);
			} else {
				$location[$reg_loc_value] = $reg_loc_value;
			}
		}
	}else{
		$region = null;
		$location = null;	
	}
	//print_r($region);
		if (count($slsm) == 1) {
			$slsm = self::getSlsmBelow($_SESSION['fmp_slsm'], $daily_sales_Slsm);
		}
		
		$sqlWhere = "(";
		
		/*not used anymore */
		$primaryOp = "AND"; /* for $selectMethod = 'i', intersect */
		if ($selectMethod == 'u') { /* union */
			$primaryOp = "OR";
		}

		/* generate where criteria with everything in acl -- including custid */
		$acl_array = $_SESSION['fmp_acl'];
		$bNeedOp = false;
		foreach($acl_array as $acl) {
			$bNeedSubOp = false;
			if($bNeedOp) {
				$sqlWhere .= " OR ";
			}
			$sqlWhere .= "(";
			if(!is_null($acl['location'])) {
				$sqlWhere .= "dsls_dailysales.loc = ".$acl['location'];
				$bNeedSubOp = true;
			}
			if(!is_null($acl['region'])) {
				if($bNeedSubOp) { $sqlWhere .= " AND "; }
				$sqlWhere .= "dsls_dailysales.region = ".$acl['region'];
				$bNeedSubOp = true;
			}
			if(!is_null($acl['slsm'])) {
				if($bNeedSubOp) { $sqlWhere .= " AND "; }
				$sqlWhere .= sprintf("x_a.slsm_c = '%s'",$acl['slsm']);  /* select slsm from accounts table to include those with no ds data */
				$bNeedSubOp = true;
			}
			if(!is_null($acl['dealertype'])) {
				if($bNeedSubOp) { $sqlWhere .= " AND "; }
				$sqlWhere .= sprintf("x_a.dealertype_c = '%s'",$acl['dealertype']);
				$bNeedSubOp = true;
			}
			//if(is_null($location) and is_null($region) and is_null($slsm) and is_null($dealerType)) { /* no criteria specified, include odd custids */
			if(!is_null($acl['custid'])) {
				if($bNeedSubOp) { $sqlWhere .= " AND "; }
				$sqlWhere .= sprintf("dsls_dailysales.custid = %d",$acl['custid']);
				$bNeedSubOp = true;
			}
			//}
			$sqlWhere .= ")";
			$bNeedOp = true;
		}
		if(!$bNeedOp) { $sqlWhere .= "0"; } /* select nothing */ 

		$sqlWhere .= ") AND (";
		
		/* now add on criteria of selection */
		$bNeedOp = false;
		if (! is_null ( $location )) {
			$bNeedOp = true;
			if (! is_array ( $location )) {
				$sqlWhere .= " dsls_dailysales.loc = '$location'";
			} else {
				$sqlWhere .= " dsls_dailysales.loc IN(";
				$bFirstLoc = true;
				foreach ( $location as $locno ) {
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
		
		if (! is_null ( $region )) {
			if ($bNeedOp) {
				$sqlWhere .= " AND";
			} else {
				$bNeedOp = true;
			}
			if (! is_array ( $region )) {
				$sqlWhere .= " dsls_dailysales.region = '$region'";
			} else {
				$sqlWhere .= " dsls_dailysales.region IN (";
				$bFirstReg = true;
				foreach ( $region as $regno ) {
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
		
		/* if 521.php ever requests just a single slsm instead of a list of slsm below the selected, use this
		 */
                if (!is_null($slsm)) {
			if ($bNeedOp) {
				$sqlWhere .= " AND";
			} else {
				$bNeedOp = true;
			}
			$slsm_array=array();
			if (! is_array ( $slsm )) {
				$slsm_array = self::getSlsmBelow($_SESSION['fmp_slsm'], $slsm);
			} else {
				foreach($slsm as $slsmno) {
					$slsm_array = array_merge($slsm_array, self::getSlsmBelow($_SESSION['fmp_slsm'], $slsmno));
				}
			}
			$sqlWhere .= " dsls_dailysales.slsm IN (";
			$bFirstSlsm = true;
			foreach ( $slsm_array as $slsmno ) {
				if ($bFirstSlsm) {
					$bFirstSlsm = false;
				} else {
					$sqlWhere .= ",";
				}
				$sqlWhere .= $slsmno;
			}
			$sqlWhere .= ")";
		}
		/*
		if (! is_null ( $slsm )) {
			if ($bNeedOp) {
				$sqlWhere .= " AND";
			} else {
				$bNeedOp = true;
			}
			if (! is_array ( $slsm )) {
				$sqlWhere .= " x_a.slsm_c = '$slsm'";
			} else {
				$sqlWhere .= " x_a.slsm_c IN (";
				$bFirstReg = true;
				foreach ( $slsm as $slsmno ) {
					if ($bFirstReg) {
						$bFirstReg = false;
					} else {
						$sqlWhere .= ",";
					}
					$sqlWhere .= "'$slsmno'";
				}
				$sqlWhere .= ")";
			}
		}*/
		
		if (! is_null ( $dealerType )) {
			if ($bNeedOp) {
				$sqlWhere .= " AND";
			} else {
				$bNeedOp = true;
			}
			if (! is_array ( $dealerType )) {
				$sqlWhere .= " x_a.dealertype_c = '$dealerType'";
			} else {
				$sqlWhere .= " x_a.dealertype_c IN (";
				$bFirstDT = true;
				foreach ( $dealerType as $dt ) {
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
		
		if(!$bNeedOp) { $sqlWhere .= "1"; } /* no criteria so provide everything already added in ACL */
		$sqlWhere .= ")";

        $h = ''
  //              . $this->user_add_on($is_user_id)
                . ' WHERE x_a.deleted = 0 AND ((x_a.slsm_c) Not In (20,232)) AND ((x_a.custtype_c) Not In (\'AFFL\',\'TRAV\')) AND (' . $sqlWhere . ')'
        ;
        return $h;
    }

    function acl_where_summary() {

        include_once("521/FMPSales.php");

        FMPSales::initialize521();


		/* generate where criteria with everything in acl -- including custid */
		$acl_array = $_SESSION['fmp_acl'];
		$acl_access_granted = array();
		foreach($acl_array as $acl) {

			if(!is_null($acl['location'])) {
				$acl_access_granted['location'][] = $acl['location'];
			}
			if(!is_null($acl['region'])) {
				$acl_access_granted['region'][] = $acl['region'];
			}
			if(!is_null($acl['slsm'])) {
				$acl_access_granted['slsm'][] = $acl['slsm'];
			}

		}
		
		return $acl_access_granted;
    }


    function display() {
        global $current_user, $sugar_config;
//yap. i know. that crazy. I need get settings for this dashlet
//print_r($_SESSION);
$dashlet_display_col = array();
foreach($_SESSION as $key=>$valye) {
	if(strstr($key, 'PREFERENCES')) {
		foreach($_SESSION[$key]['Home']['dashlets'] as $dashletid=>$dashletValue){
			if($dashletValue['module'] == 'DSls_DailySales') {
				$dashlet_display_col = $dashletValue['options']['displayColumns'];
			break;			
			}
				
		}
	break;
	}
}

//print_r($dashlet_display_col);
        $title = '';
        $total_query = '';
        //$personal_filter = ($current_user->getPreference('personal_filter_value')>0) ? $current_user->getPreference('personal_filter_time') : $current_user->getPreference('personal_filter_value') ;
        $daily_sales_Slsm = $_POST['slsm_num'] != 'all' ? $_POST['slsm_num'] : '';
        $daily_sales_Reg_loc = $_POST['reg_loc'] != 'all' ? $_POST['reg_loc'] : '';
        $daily_sales_Dealer_post = $_POST['dealer'] != 'all' ? $_POST['dealer'] : '';
//         if(isset($_POST['update']) && $_POST['update']==1){
//            $slsm = ($current_user->getPreference('daily_sales_Slsm'))? $current_user->getPreference('daily_sales_Slsm'): '';
//            $reg_loc = ($current_user->getPreference('daily_sales_Reg_loc'))? $current_user->getPreference('daily_sales_Reg_loc'): '';
//            $dealer_post = ($current_user->getPreference('daily_sales_Dealer_post'))? $current_user->getPreference('daily_sales_Dealer_post'): '';
//            if($slsm != '' && $daily_sales_Slsm != ''){
//                $slsm_array = explode(";", $slsm.';'.$daily_sales_Slsm);
//                $current_user->setPreference('daily_sales_Slsm', implode(";",array_unique($slsm_array)));
//            }else{
//                $current_user->setPreference('daily_sales_Slsm', $daily_sales_Slsm);
//            }
//            if($reg_loc != '' && $daily_sales_Reg_loc != ''){
//                $reg_loc_array = explode(";", $reg_loc.';'.$daily_sales_Reg_loc);
//                $current_user->setPreference('daily_sales_Reg_loc', implode(";",array_unique($reg_loc_array)));
//            }else{
//                $current_user->setPreference('daily_sales_Reg_loc', $daily_sales_Reg_loc);
//            }
//             if($dealer_post != '' && $daily_sales_Dealer_post != ''){
//                $dealer_post_array = explode(";", $dealer_post.';'.$daily_sales_Dealer_post);
//                $current_user->setPreference('daily_sales_Dealer_post', implode(";",array_unique($dealer_post_array)));
//            }else{
//                $current_user->setPreference('daily_sales_Dealer_post', $daily_sales_Dealer_post);
//            }
//         }else{
            $current_user->setPreference('daily_sales_Slsm', $daily_sales_Slsm);
            $current_user->setPreference('daily_sales_Reg_loc', $daily_sales_Reg_loc);
            $current_user->setPreference('daily_sales_Dealer_post', $daily_sales_Dealer_post);
//         }
        $slsm = ($current_user->getPreference('daily_sales_Slsm'))? $current_user->getPreference('daily_sales_Slsm'): '';
        $reg_loc = ($current_user->getPreference('daily_sales_Reg_loc'))? $current_user->getPreference('daily_sales_Reg_loc'): '';
        $dealer_post = ($current_user->getPreference('daily_sales_Dealer_post'))? $current_user->getPreference('daily_sales_Dealer_post'): '';

	$_SESSION['dashet_and_521_selections'] = array( 'slsm' => $slsm, 
							'loc' => $reg_loc, 
							'dealer' => $dealer_post);


            //$reg_loc_query = $this->get_reg_loc($reg_loc);

            $title .= $reg_loc != 'undefined' ? (substr($reg_loc,0,1) == 'r' ? 'Region '.substr($reg_loc,1) : $reg_loc != '' ? 'Location '.$reg_loc : ''): '';
        

        $is_user_id = 0;
        $slsm_obj = new fmp_Param_SLSM($current_user->id);
        $slsm_obj->init();

        $is_s = $slsm_obj->is_assigned_slsm();
        if ($is_s) {
            //$arr[] = $slsm;
            //$r_users = $slsm_obj->compile__available_slsm($arr);
            //$str_selection_button = $this->build__slsm($r_users, $is_user_id);
            $title .= $slsm != 'undefined' && $slsm != '' ? ($reg_loc != '' && $reg_loc != 'undefined' && $reg_loc != 'undefined' ? '/Slsm '.$slsm : 'Slsm '.$slsm) :'';
        }

        //$slsm_tree_list = $slsm_obj->html_for_daily_sales();  // prepeare SLSM list for display
        unset($slsm_obj);

        $db = &PearDatabase::getInstance();
        $query_all_data = "  SELECT
			    sum(previous_day_sales) AS previous_day_sales,
                            sum(mtd_projected) AS mtd_projected,
                            sum(mtd_projected_gp) AS mtd_projected_gp,
			    sum(ytd_projected) AS ytd_projected,
                            sum(ytd_projected_gp) AS ytd_projected_gp,
			    sum(ytd_sales) AS ytd_sales,
			    sum(ytd_gp) AS ytd_gp,
			    sum(ytd_budget_sales) AS ytd_budget_sales,
                            sum(mtd_budget_sales) AS mtd_budget_sales,
			    sum(mtd_budget_gp) AS mtd_budget_gp,
			    sum(ytd_budget_gp) AS ytd_budget_gp,
			    sum(mly_sls) AS mly_sls,
			    sum(lytd_sales) AS lytd_sales,
                            sum(mtd_sales) AS mtd_sales,
                            sum(lm_sales) AS lm_sales,
                            sum(mtd_gp) AS mtd_gp,
                            sum(lm_gp) AS lm_gp,
                            sum(mtd_budget_gp) AS mtd_budget_gp,
			    sum(pending_orders) AS pending_orders,
			    sum(pending_credits) AS pending_credits,
			    sum(todays_orders) AS todays_orders,
			    sum(todays_credits) AS todays_credits,
			    sum(ly_sales) AS ly_sales,
			    sum(ly_gp) AS ly_gp,			    
			    if(sum(ytd_invoices) = 0, null, round(sum(ytd_sales) / sum(ytd_invoices),2)) as ytd_av_per_trans, 
			    if(sum(mtd_invoices) = 0, null, round(sum(mtd_sales) / sum(mtd_invoices),2)) as mtd_av_per_trans, 




				sum(mtd_sls_noem) as mtd_sls_noem,
				sum(mtd_gp_noem) as mtd_gp_noem,
				sum(mtd_budget_noem_sales) as mtd_budget_noem_sales,
				sum(mtd_projected_noem) as mtd_projected_noem,
				sum(ly_sls_noem) as ly_sls_noem,
				sum(ly_gp_noem) as ly_gp_noem,
				    

				sum(ytd_sls_noem) as ytd_sls_noem,
				sum(ytd_gp_noem) as ytd_gp_noem,
				sum(ytd_budget_noem_sales) as ytd_budget_noem_sales,
				sum(ytd_projected_noem) as ytd_projected_noem,
				sum(mtd_projected_undercar) as mtd_projected_undercar,
				sum(mtd_budget_undercar_sales) as mtd_budget_undercar_sales,
				sum(mtd_budget_undercar_gp) as mtd_budget_undercar_gp,
				sum(ytd_projected_undercar) as ytd_projected_undercar,
				sum(ytd_budget_undercar_sales) as ytd_budget_undercar_sales,
				sum(mtd_sls_undercar) as mtd_sls_undercar,
				sum(mtd_gp_undercar) as mtd_gp_undercar,
				sum(ytd_sls_undercar) as ytd_sls_undercar,
				sum(ytd_gp_undercar) as ytd_gp_undercar,
				sum(ly_sls_undercar) as ly_sls_undercar,
				sum(ly_gp_undercar) as ly_gp_undercar,


			    sum(ytd_invoices) AS ytd_invoices,
			    sum(mtd_invoices) AS mtd_invoices,
			    sum(wtd_invoices) AS wtd_invoices
                    FROM dsls_dailysales
                    LEFT JOIN accounts AS x_a
                    ON x_a.custid_c = dsls_dailysales.custid ";

        //$dealer_type_query = $this->get_dealer_type_query($dealer_post);
        $title .= $dealer_post != 'undefined' && $dealer_post != '' ? (($slsm == 'undefined' || $slsm == '') && ($reg_loc == 'undefined' || $reg_loc == '') ? 'Customer Type '.$dealer_post : '/Customer Type '.$dealer_post) :'';

        //$total_query = $query_all_data.$str_selection_button.$reg_loc_query.$dealer_type_query;
	$total_query = $query_all_data . " " . $this->acl_where();

        //print_r($total_query);
        $res = $db->query($total_query);

        $previous_day_invoiced_sales = 0;
        $mtd_projected = 0;
        $mtd_projected_gp = 0;
        $mtd_budget_sales = 0;
        $mtd_sales = 0;
        $lm_sales = 0;
        $mtd_gp = 0;
        $lm_gp = 0;
        $mtd_budget_gp = 0;

        $result = $db->fetchByAssoc($res);
//        while($result = $db->fetchByAssoc($res)) {
	$ytd_projected += $result['ytd_projected'];
        $ytd_projected_gp += $result['ytd_projected_gp'];
	$ytd_sales_invoiced += $result['ytd_sales'];
	$ytd_gp += $result['ytd_gp'];
	$ytd_gpp += $result['ytd_sales']+0 != 0 ? round(($result['ytd_gp']/$result['ytd_sales'])*100,2)."%" : '0%';
	$ytd_sales_budget += $result['ytd_budget_sales'];
	$cm_gp_budget += $result['mtd_budget_gp'];
	$cm_gpp_budget += $result['mtd_budget_gp']+0 != 0 ? round(($result['mtd_budget_gp']/$result['mtd_budget_sales'])*100,2).'%' : '0%';
	$cy_gp_budget += $result['ytd_budget_gp'];
	$cy_gpp_budget += $result['ytd_budget_gp']+0 != 0 ? round(($result['ytd_budget_gp']/$result['ytd_budget_sales'])*100,2).'%' : '0%';
        $previous_day_invoiced_sales += $result['previous_day_sales'];
        $mtd_projected += $result['mtd_projected'];
        $mtd_projected_gp += $result['mtd_projected_gp'];
        $mtd_budget_sales += $result['mtd_budget_sales'];
        $mtd_sales += $result['mtd_sales'];
	$mly_sales_invoiced += $result['mly_sls'];
	$lytm_sales_invoiced += $result['lytd_sales'];
        $lm_sales += $result['lm_sales'];
	$ly_sales_invoiced += $result['ly_sales'];
        $mtd_gp += $result['mtd_gp'];
        $lm_gp += $result['lm_gp'];
        $mtd_budget_gp += $result['mtd_budget_gp'];
	$mtd_sales_total += $result['mtd_sales'] + $result['pending_orders'] + $result['pending_credits'] + $result['todays_orders'] + $result['todays_credits'];
	$pending_orders += $result['pending_orders'] + $result['todays_orders'];
	$pending_credits += $result['pending_credits'] + $result['todays_credits'];
	$ly_gp += $result['ly_gp'];
	$ly_gpp += $result['ly_sales'] +0 != 0 ? round(($result['ly_gp'] / $result['ly_sales']* 100),2).'%' : '0%';

//	$current_sls_proj_vs_ly_sls_invoiced = $ytd_projected - $ly_sales_invoiced;
//                  $current_gp_proj_vs_ly_gp_invoiced = $ytd_projected_gp - $ly_gp;
                   $current_sls_proj_vs_ly_sls_invoiced = $ytd_projected - $ytd_sales_budget;
                  $current_gp_proj_vs_ly_gp_invoiced = $ytd_projected_gp - $cy_gp_budget;
	//$cy_sales_proj_vs_cy_sales_budget = $ytd_projected - $ytd_sales_budget;
	$att_y_sls_proj_vs_cy_sls_budget = ($ytd_sales_budget != 0 ? round(($ytd_sales_invoiced/$ytd_sales_budget) * 100, 2) . '%' : '0%');
	$att_y_gp_proj_vs_cy_gp_budget = ($cy_gp_budget != 0 ? round(($ytd_gp/$cy_gp_budget)*100, 2) . '%' : '0%');
                  $att_y_gpp_proj_vs_cy_gpp_budget = ($cy_gpp_budget != 0 ? round(($ytd_gpp- $cy_gpp_budget) , 2) . '%' : '0%');
	$ytd_invoices = $result['ytd_invoices'];
	$mtd_invoices = $result['mtd_invoices'];
	$wtd_invoices = $result['wtd_invoices'];
	$ytd_av_per_trans = $result['ytd_av_per_trans'];
	$mtd_av_per_trans = $result['mtd_av_per_trans'];



//print_r($dashlet_display_col);
	($dashlet_display_col == array() || in_array('mtd_sls_noem', $dashlet_display_col)) ? $mtd_sls_noem =  number_format($result['mtd_sls_noem'], 0,'.',',' ) : '&nbsp;';

	($dashlet_display_col == array() || in_array('ytd_sls_noem', $dashlet_display_col)) ? $ytd_sls_noem =  number_format($result['ytd_sls_noem'], 0,'.',',' ) : '&nbsp;';
	
	
	($dashlet_display_col == array() || in_array('mtd_gp_noem', $dashlet_display_col)) ? $mtd_gp_noem =  number_format($result['mtd_gp_noem'], 0,'.',',' ) : '&nbsp;';
	
	($dashlet_display_col == array() || in_array('ytd_gp_noem', $dashlet_display_col)) ? $ytd_gp_noem =  number_format($result['ytd_gp_noem'], 0,'.',',' ) : '&nbsp;';

	
	($dashlet_display_col == array() || in_array('mtd_gpp_noem', $dashlet_display_col)) ? ($mtd_gpp_noem = $result['mtd_sls_noem']+0 != 0 ? round($result['mtd_gp_noem'] / $result['mtd_sls_noem'] * 100,2).'%' : '0%') : '&nbsp;';

	($dashlet_display_col == array() || in_array('ytd_gpp_noem', $dashlet_display_col)) ? ($ytd_gpp_noem = $result['ytd_sls_noem']+0 != 0 ? round($result['ytd_gp_noem'] / $result['ytd_sls_noem'] * 100,2).'%' : '0%') : '&nbsp;';
	



	($dashlet_display_col == array() || in_array('mtd_budget_noem_sales', $dashlet_display_col)) ? $mtd_budget_noem_sales =  number_format($result['mtd_budget_noem_sales'], 0,'.',',' ) : '&nbsp;';

	($dashlet_display_col == array() || in_array('ytd_budget_noem_sales', $dashlet_display_col)) ? $ytd_budget_noem_sales =  number_format($result['ytd_budget_noem_sales'], 0,'.',',' ) : '&nbsp;';

	($dashlet_display_col == array() || in_array('mtd_projected_noem', $dashlet_display_col)) ? $mtd_projected_noem =  number_format($result['mtd_projected_noem'], 0,'.',',' ) : '&nbsp;';

	($dashlet_display_col == array() || in_array('ytd_projected_noem', $dashlet_display_col)) ? $ytd_projected_noem =  number_format($result['ytd_projected_noem'], 0,'.',',' ) : '&nbsp;';

	($dashlet_display_col == array() || in_array('ly_sls_noem', $dashlet_display_col)) ? $ly_sls_noem =  number_format($result['ly_sls_noem'], 0,'.',',' ) : '&nbsp;';	

	($dashlet_display_col == array() || in_array('ly_gpp_noem', $dashlet_display_col)) ? ($ly_gpp_noem = $result['ly_sls_noem']+0 != 0 ? round($result['ly_gp_noem']/$result['ly_sls_noem']*100,2).'%' : '0%' ) : '&nbsp;';



/////////////////////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////////////////////
//UNDERCAR


	($dashlet_display_col == array() || in_array('mtd_sls_undercar', $dashlet_display_col)) ? $mtd_sls_undercar =  number_format($result['mtd_sls_undercar'], 0,'.',',' ) : '&nbsp;';

	($dashlet_display_col == array() || in_array('ytd_sls_undercar', $dashlet_display_col)) ? $ytd_sls_undercar =  number_format($result['ytd_sls_undercar'], 0,'.',',' ) : '&nbsp;';
	
	($dashlet_display_col == array() || in_array('mtd_gp_undercar', $dashlet_display_col)) ? $mtd_gp_undercar =  number_format($result['mtd_gp_undercar'], 0,'.',',' ) : '&nbsp;';

	($dashlet_display_col == array() || in_array('ytd_gp_undercar', $dashlet_display_col)) ? $ytd_gp_undercar =  number_format($result['ytd_gp_undercar'], 0,'.',',' ) : '&nbsp;';

	($dashlet_display_col == array() || in_array('mtd_gpp_undercar', $dashlet_display_col)) ? ($mtd_gpp_undercar = $result['mtd_sls_undercar']+0 > 0 ? round(($result['mtd_gp_undercar']/$result['mtd_sls_undercar'])*100,2)."%" : '0%' ) : '&nbsp;';

	($dashlet_display_col == array() || in_array('ytd_gpp_undercar', $dashlet_display_col)) ? ($ytd_gpp_undercar = $result['ytd_sls_undercar']+0 > 0 ? round(($result['ytd_gp_undercar']/$result['ytd_sls_undercar'])*100,2)."%" : '0%') : '&nbsp;';

	($dashlet_display_col == array() || in_array('mtd_budget_undercar_sales', $dashlet_display_col)) ? $mtd_budget_undercar_sales =  number_format($result['mtd_budget_undercar_sales'], 0,'.',',' ) : '&nbsp;';

	($dashlet_display_col == array() || in_array('ytd_budget_undercar_sales', $dashlet_display_col)) ? $ytd_budget_undercar_sales =  number_format($result['ytd_budget_undercar_sales'], 0,'.',',' ) : '&nbsp;';
	
	($dashlet_display_col == array() || in_array('mtd_projected_undercar', $dashlet_display_col)) ? $mtd_projected_undercar =  number_format($result['mtd_projected_undercar'], 0,'.',',' ) : '&nbsp;';

	($dashlet_display_col == array() || in_array('ytd_projected_undercar', $dashlet_display_col)) ? $ytd_projected_undercar =  number_format($result['ytd_projected_undercar'], 0,'.',',' ) : '&nbsp;';


	($dashlet_display_col == array() || in_array('ly_sls_undercar', $dashlet_display_col)) ? $ly_sls_undercar =  number_format($result['ly_sls_undercar'], 0,'.',',' ) : '&nbsp;';

	($dashlet_display_col == array() || in_array('ly_gpp_undercar', $dashlet_display_col)) ? ($ly_gpp_undercar = $result['ly_sls_undercar']+0 > 0 ? round(($result['ly_gp_undercar']/$result['ly_sls_undercar'])*100,2)."%" : '0%' ) : '&nbsp;';



	
//        }

        $today_mounth = $date_today = date("m");
        $today_year =  $date_today = date("Y");
        $query_bus_days = "  SELECT count(*) AS num FROM dsls_calendar where yr = '".$today_year."' and mo = '".$today_mounth."' and workday = '-1'";
        $res_month_business_days = $db->query($query_bus_days);
        $bus_days = $db->fetchByAssoc($res_month_business_days);

        $mounth_start = date("Y-m-1");
        $mounth_today =  date("Y-m-d", (time()-86400));
        $query_first_bus_days = "  SELECT count(*) AS num FROM dsls_calendar where cal_date between '".$mounth_start."' and '".$mounth_today."' and workday = '-1' ";
        $res_first_month_business_days = $db->query($query_first_bus_days);
        $first_bus_days = $db->fetchByAssoc($res_first_month_business_days);

        $cm_proj_vs_budget = round($mtd_projected - $mtd_budget_sales);
        $cm_proj_gp_vs_budget_gp = round($mtd_projected_gp - $mtd_budget_gp);
        if($mtd_sales == 0) {
            $current_month_gp = 0;
        }else {
            $current_month_gp = round(100*$mtd_gp/$mtd_sales, 2);
        }

        if($lm_sales == 0) {
            $last_month_gp = 0;
        }
        else {
            $last_month_gp = round(100*$lm_gp/$lm_sales, 2);
        }
        
        if($bus_days['num'] == 0) {
            $daily_average_target = 0;
        }else {
            $daily_average_target = round($mtd_budget_sales/$bus_days['num']);
	    $daily_average_target_gp = round($mtd_budget_gp/$bus_days['num']);
        }

        $remaining_bus_days = $bus_days['num'] - $first_bus_days['num'];
        if($remaining_bus_days == 0) {
            $daily_target = 0;
        }
        else {
            $daily_target = round(($mtd_budget_sales - $mtd_sales)/$remaining_bus_days);
	    $daily_target = $daily_target > $daily_average_target ? $daily_target : $daily_average_target;
	    $daily_target_gp = round(($cm_gp_budget - $mtd_gp)/$remaining_bus_days);
	    $daily_target_gp = $daily_target_gp > $daily_average_target_gp ? $daily_target_gp : $daily_average_target_gp; 
        }



        $att_sal_to_mon_sal = $mtd_budget_sales != 0 ? round($mtd_sales/$mtd_budget_sales*100, 2) . '%': '0%';
        $att_gp_to_mon_bud = $mtd_budget_gp != 0 ? round($mtd_gp/$mtd_budget_gp*100, 2) . '%': '0%';
        $att_gpp_to_month_budget_gpp =  $cm_gpp_budget != 0 ? round(($current_month_gp - $cm_gpp_budget), 2). '%' : '0%';
        
        $region_id = substr($reg_loc,0,1) == 'r' ? substr($reg_loc,1) : null;
        $location_id = substr($reg_loc,0,1) != 'r' ? $reg_loc : null;
//        pr($region_id);
//        pr($location_id);
       $current_month_gpp = $mtd_sales != 0 ? round(100 * $mtd_gp / $mtd_sales, 2) : '0%';
       $mtd_gp_noem_divide_mtd_gp = $mtd_gp != 0 ? round($result['mtd_gp_noem'] / $mtd_gp * 100, 2) . '%': '0%';
       $mtd_gpp_noem_divide_mtd_gpp = $current_month_gpp != 0 ? round($mtd_gpp_noem - $current_month_gpp, 2) . '%': '0%';
       $mtd_budget_noem_sales_divide_mtd_budget_sales = $mtd_budget_sales != 0 ? round($result['mtd_budget_noem_sales'] / $mtd_budget_sales * 100, 2) . '%': '0%';
       $mtd_projected_noem_divide_mtd_projected= $mtd_projected != 0 ? round($result['mtd_projected_noem'] / $mtd_projected * 100, 2) . '%': '0%';
       $ytd_gp_noem_divide_ytd_gp=$ytd_gp != 0 ? round($result['ytd_gp_noem'] / $ytd_gp * 100, 2) . '%': '0%';
       $ytd_gpp_noem_divide_ytd_gpp= $ytd_gpp != 0 ? round($ytd_gpp_noem - $ytd_gpp, 2) . '%': '0%';
       $ytd_budget_noem_sales_divide_ytd_sales_budget=$ytd_sales_budget != 0 ? round($result['ytd_budget_noem_sales'] / $ytd_sales_budget * 100, 2) . '%': '0%';
       $ytd_projected_noem_divide_ytd_projected= $ytd_projected != 0 ? round($result['ytd_projected_noem'] / $ytd_projected * 100, 2) . '%': '0%';
       $ly_gpp_noem_divide_ly_gpp= $ly_gpp != 0 ? round($ly_gpp_noem - $ly_gpp, 2) . '%': '0%';

        $mtd_sls_noem_divide_mtd_sales = $mtd_sales != 0 ? round($result['mtd_sls_noem'] / $mtd_sales * 100, 2). '%' : '0%';
        $ly_sls_noem_divide_ly_sales = $ly_sales_invoiced != 0 ? round($result['ly_sls_noem'] / $ly_sales_invoiced * 100, 2). '%' : '0%';
        $ytd_sls_noem_divide_ytd_sales = $ytd_sales_invoiced != 0 ? round($result['ytd_sls_noem'] / $ytd_sales_invoiced * 100, 2) . '%': '0%';
        
       $mtd_gp_undercar_divide_mtd_gp = $mtd_gp != 0 ? round($result['mtd_gp_undercar'] / $mtd_gp * 100, 2) . '%': '0%';
       $mtd_gpp_undercar_divide_mtd_gpp = $current_month_gpp != 0 ? round($mtd_gpp_undercar - $current_month_gpp , 2) . '%': '0%';
       $mtd_budget_undercar_sales_divide_mtd_budget_sales = $mtd_budget_sales != 0 ? round($result['mtd_budget_undercar_sales'] / $mtd_budget_sales * 100, 2) . '%': '0%';
       $mtd_projected_undercar_divide_mtd_projected= $mtd_projected != 0 ? round($result['mtd_projected_undercar'] / $mtd_projected * 100, 2) . '%': '0%';
       $ytd_gp_undercar_divide_ytd_gp=$ytd_gp != 0 ? round($result['ytd_gp_undercar'] / $ytd_gp * 100, 2) . '%': '0%';
       $ytd_gpp_undercar_divide_ytd_gpp= $ytd_gpp != 0 ? round($ytd_gpp_undercar - $ytd_gpp , 2) . '%': '0%';
       $ytd_budget_undercar_sales_divide_ytd_sales_budget=$ytd_sales_budget != 0 ? round($result['ytd_budget_undercar_sales'] / $ytd_sales_budget * 100, 2) . '%': '0%';
       $ytd_projected_undercar_divide_ytd_projected= $ytd_projected != 0 ? round($result['ytd_projected_undercar'] / $ytd_projected * 100, 2) . '%': '0%';
       $ly_gpp_undercar_divide_ly_gpp= $ly_gpp != 0 ? round($ly_gpp_undercar - $ly_gpp, 2) . '%': '0%';
        
        $mtd_sls_undercar_divide_mtd_sales = $mtd_sales != 0 ? round($result['mtd_sls_undercar'] / $mtd_sales * 100, 2). '%' : '0%';
        $ly_sls_undercar_divide_ly_sales = $ly_sales_invoiced != 0 ? round($result['ly_sls_undercar'] / $ly_sales_invoiced * 100, 2). '%' : '0%';
        $ytd_sls_undercar_divide_ytd_sales = $ytd_sales_invoiced != 0 ? round($result['ytd_sls_undercar'] / $ytd_sales_invoiced * 100, 2). '%' : '0%';
        
        $today_sales_summ =  $this->sales_today_previous(date("Y-m-d"), 'SLSM', $slsm, $region_id, $location_id);
        $previous_sales_summ = $this->sales_today_previous(date("Y-m-d", (time()-86400)), 'SLSM', $slsm, $region_id, $location_id);

        $title = $title == '' ? 'All' : $title;

        $strTable = "<table align='center' width='100%'>
            <caption><h1>".$title."</h1></caption>
            <tbody>";

	if($dashlet_display_col == array() ||
		in_array('current_day_sales', $dashlet_display_col) || 
		in_array('current_day_credits',  $dashlet_display_col) || 
		in_array('current_day_net_sales',  $dashlet_display_col) || 
		in_array('previous_day_sales',  $dashlet_display_col) || 
		in_array('previous_day_credits',  $dashlet_display_col) ||
		in_array('previous_day_net_sales',  $dashlet_display_col)		
		) {
	$strTable .= "
                <tr>
                    <td width='50%'>
                        <div>
                            <table width='100%'>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_day_sales', $dashlet_display_col) ? 'Current Day Sales' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_day_sales', $dashlet_display_col) ? ($today_sales_summ["sales"] != "N/A" ? $this->red_color_text($today_sales_summ["sales"]) : $today_sales_summ["sales"]) : '&nbsp;') . "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_day_credits',  $dashlet_display_col) ? 'Current Day Credits' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_day_credits',  $dashlet_display_col) ? ($today_sales_summ["credits"] != "N/A" ? $this->red_color_text($today_sales_summ["credits"]) : $today_sales_summ["credits"]) : '&nbsp;') . "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_day_net_sales',  $dashlet_display_col) ? 'Current Day Net Sales' : '&nbsp;')."</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_day_net_sales',  $dashlet_display_col) ? ($today_sales_summ["net_sales"] != "N/A" ? $this->red_color_text($today_sales_summ["net_sales"]) : $today_sales_summ["net_sales"]): '&nbsp;') . "</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td width='50%'>
                        <div>
                            <table width='100%'>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('previous_day_sales',  $dashlet_display_col) ? 'Previous Day\'s Sales' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('previous_day_sales',  $dashlet_display_col) ? ($previous_sales_summ["sales"] != "N/A" ? $this->red_color_text($previous_sales_summ["sales"]) : $previous_sales_summ["sales"]) : '&nbsp;') . "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('previous_day_credits',  $dashlet_display_col) ? 'Previous Day\'s Credits' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>".($dashlet_display_col == array() || in_array('previous_day_credits',  $dashlet_display_col) ? ($previous_sales_summ["credits"] != "N/A" ? $this->red_color_text($previous_sales_summ["credits"]) : $previous_sales_summ["credits"]) : '&nbsp;')."</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>".($dashlet_display_col == array() || in_array('previous_day_net_sales',  $dashlet_display_col) ? 'Previous Day\'s Net Sales' : '&nbsp;')."</td>
                                    <td style='font-size: 14px;'>".($dashlet_display_col == array() || in_array('previous_day_net_sales',  $dashlet_display_col) ? ($previous_sales_summ["net_sales"] != "N/A" ? $this->red_color_text($previous_sales_summ["net_sales"]) : $previous_sales_summ["net_sales"]) : '&nbsp;')."</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>";
	}

if($dashlet_display_col == array() ||
		in_array('daily_target', $dashlet_display_col) || 
		in_array('daily_average_target',  $dashlet_display_col) ||
		in_array('daily_target_gp', $dashlet_display_col) || 
		in_array('daily_average_target_gp',  $dashlet_display_col)
				
		) {
	$strTable .= "
                <tr>
                    <td width='50%'>
                        <div>
                            <table width='100%'>
                               <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('daily_target',  $dashlet_display_col) ? 'Daily Sales Target' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('daily_target',  $dashlet_display_col) ? $this->red_color_text($daily_target) : '&nbsp;') . "</td>
                                </tr>

                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('daily_average_target',  $dashlet_display_col) ? 'Daily Average Sales Target' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('daily_average_target',  $dashlet_display_col) ? $this->red_color_text($daily_average_target) : '&nbsp;') . "</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td width='50%'>
                        <div>
                            <table width='100%'>
                               <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('daily_target_gp',  $dashlet_display_col) ? 'Daily GP$ Target' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('daily_target_gp',  $dashlet_display_col) ? $this->red_color_text($daily_target_gp) : '&nbsp;') . "</td>
                                </tr>

                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('daily_average_target_gp',  $dashlet_display_col) ? 'Daily Average GP$ Target' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('daily_average_target_gp',  $dashlet_display_col) ? $this->red_color_text($daily_average_target_gp) : '&nbsp;') . "</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>";
	}
if($dashlet_display_col == array() ||
		in_array('current_month_proj',  $dashlet_display_col) ||
                                    in_array('current_month_proj_gp',  $dashlet_display_col) ||
		in_array('cm_proj_vs_budget',  $dashlet_display_col) ||
		in_array('cm_proj_gp_vs_budget_gp',  $dashlet_display_col)
		) {
$strTable .= "
                <tr>
                    <td width='50%'>
                        <div>
                            <table width='100%'>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_month_proj',  $dashlet_display_col) ? 'Current Month Projected Sales' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_month_proj',  $dashlet_display_col) ? $this->red_color_text($mtd_projected) : '&nbsp;') . "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_month_proj_gp',  $dashlet_display_col) ? 'Current Month Projected GP$' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_month_proj_gp',  $dashlet_display_col) ? $this->red_color_text($mtd_projected_gp) : '&nbsp;') . "</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td width='50%'>
                        <div>
                            <table width='100%'>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('cm_proj_vs_budget',  $dashlet_display_col) ? 'CM Proj Sls vs. CM Sls Budget'  : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('cm_proj_vs_budget',  $dashlet_display_col) ? $this->red_color_text($cm_proj_vs_budget)  : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('cm_proj_gp_vs_budget_gp',  $dashlet_display_col) ? 'CM Proj GP$ vs. CM GP$ Budget'  : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('cm_proj_gp_vs_budget_gp',  $dashlet_display_col) ? $this->red_color_text($cm_proj_gp_vs_budget_gp)  : '&nbsp;'). "</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>";
	}
	if($dashlet_display_col == array() ||
		in_array('current_month_sales_budget',  $dashlet_display_col) ||
		in_array('cm_gp_budget',  $dashlet_display_col) ||
		in_array('cm_gpp_budget',  $dashlet_display_col) ||
		in_array('att_sales_to_month_budget',  $dashlet_display_col) ||
		in_array('att_gp_to_month_budget',  $dashlet_display_col) ||
		in_array('att_gpp_to_month_budget_gpp',  $dashlet_display_col)
		) {
$strTable .= "
                <tr>
                    <td width='50%'>
                        <div>
                            <table width='100%'>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_month_sales_budget',  $dashlet_display_col) ? 'Current Month Sales Budget' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_month_sales_budget',  $dashlet_display_col) ? $this->red_color_text($mtd_budget_sales) : '&nbsp;') . "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('cm_gp_budget',  $dashlet_display_col) ? 'Current Month GP$ Budget' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('cm_gp_budget',  $dashlet_display_col) ? $this->red_color_text($cm_gp_budget) : '&nbsp;') . "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('cm_gpp_budget',  $dashlet_display_col) ? 'Current Month GP% Budget'  : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('cm_gpp_budget',  $dashlet_display_col) ? $this->red_color_pst($cm_gpp_budget)  : '&nbsp;'). "</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td width='50%'>
                        <div>
                            <table width='100%'>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('att_sales_to_month_budget',  $dashlet_display_col) ? '% of Attainment MTD Sls to CM Sls Budget'  : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('att_sales_to_month_budget',  $dashlet_display_col) ? $this->red_color_pst($att_sal_to_mon_sal) : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('att_gp_to_month_budget',  $dashlet_display_col) ? '% of Attainment MTD GP$ to CM GP$ Budget'  : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('att_gp_to_month_budget',  $dashlet_display_col) ? $this->red_color_pst($att_gp_to_mon_bud)  : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('att_gpp_to_month_budget_gpp',  $dashlet_display_col) ? 'MTD GP%  vs. CM GP% Budget'  : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('att_gpp_to_month_budget_gpp',  $dashlet_display_col) ? $this->red_color_pst($att_gpp_to_month_budget_gpp)  : '&nbsp;'). "</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>";
	}        
 
	if($dashlet_display_col == array() ||
		in_array('current_month_sales',  $dashlet_display_col) ||
		in_array('current_month_gp',  $dashlet_display_col) ||
		in_array('current_month_gpp',  $dashlet_display_col) ||
		in_array('last_month_sales',  $dashlet_display_col) ||
		in_array('last_month_gp',  $dashlet_display_col) ||
		in_array('last_month_gpp',  $dashlet_display_col)	

		) {
$strTable .= "
                <tr>
                    <td width='50%'>
                        <div>
                            <table width='100%'>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_month_sales',  $dashlet_display_col) ? 'MTD Sales Invoiced'  : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_month_sales',  $dashlet_display_col) ? $this->red_color_text($mtd_sales)  : '&nbsp;'). "</td>   
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_month_gp',  $dashlet_display_col) ? 'MTD GP$' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_month_gp',  $dashlet_display_col) ? $this->red_color_text($mtd_gp) : '&nbsp;') . "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_month_gpp',  $dashlet_display_col) ? 'MTD GP%' : '&nbsp;') ."</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_month_gpp',  $dashlet_display_col) ? $this->red_color_pst($current_month_gp) : '&nbsp;') ."</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td width='50%'>
                        <div>
                            <table width='100%'>

                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('last_month_sales',  $dashlet_display_col) ? 'Last Month\'s Sales' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('last_month_sales',  $dashlet_display_col) ? $this->red_color_text($lm_sales)  : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('last_month_gp',  $dashlet_display_col) ? 'Last Month\'s GP$' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('last_month_gp',  $dashlet_display_col) ? $this->red_color_text($lm_gp) : '&nbsp;') . "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('last_month_gpp',  $dashlet_display_col) ? 'Last Month\'s GP%' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('last_month_gpp',  $dashlet_display_col) ? $this->red_color_pst($last_month_gp)  : '&nbsp;'). "</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>";
	}
        
	if ($dashlet_display_col == array() ||
		in_array('mtd_sales_total',  $dashlet_display_col) ||
		in_array('pending_orders',  $dashlet_display_col) ||
		in_array('pending_credits',  $dashlet_display_col) ||
		in_array('previous_day_invoiced_sales',  $dashlet_display_col) || 
		in_array('mly_sales_invoiced',  $dashlet_display_col) ||
		in_array('lytd_sales_invoiced',  $dashlet_display_col) 
		) {
$strTable .= "
                <tr>
                    <td width='50%'>
                        <div>
                            <table width='100%'>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('pending_orders',  $dashlet_display_col) ? 'Pending Orders' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('pending_orders',  $dashlet_display_col) ? $this->red_color_text($pending_orders)  : '&nbsp;'). "</td>   
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('pending_credits',  $dashlet_display_col) ? 'Pending Credits' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('pending_credits',  $dashlet_display_col) ? $this->red_color_text($pending_credits) : '&nbsp;') . "</td>   
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_sales_total',  $dashlet_display_col) ? 'MTD Sales Total' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_sales_total',  $dashlet_display_col) ? $this->red_color_text($mtd_sales_total) : '&nbsp;') . "</td>   
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td width='50%'>
                        <div>
                            <table width='100%'>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mly_sales_invoiced',  $dashlet_display_col) ? 'CM Proj Sls vs. LM Sls' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mly_sales_invoiced',  $dashlet_display_col) ? $this->red_color_text($mly_sales_invoiced) : '&nbsp;') . "</td>   
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('lytd_sales_invoiced',  $dashlet_display_col) ? 'CY Proj Sls vs. LY Sls Invoiced' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('lytd_sales_invoiced',  $dashlet_display_col) ? $this->red_color_text($lytm_sales_invoiced) : '&nbsp;') . "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('previous_day_invoiced_sales',  $dashlet_display_col) ? 'Previous Day\'s Invoiced Sales' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('previous_day_invoiced_sales',  $dashlet_display_col) ? $this->red_color_text($previous_day_invoiced_sales) : '&nbsp;')."</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>";
	}
if($dashlet_display_col == array() ||
		in_array('current_year_sales_proj',  $dashlet_display_col) ||
		in_array('current_year_gp_proj',  $dashlet_display_col) ||
                                    in_array('current_sales_proj_vs_ly_sales_invoiced',  $dashlet_display_col) ||
		in_array('current_gp_proj_vs_ly_gp_invoiced',  $dashlet_display_col) 	
	) {
$strTable .= "
                <tr>
                    <td width='50%'>
                        <div>
                            <table width='100%'>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_year_sales_proj',  $dashlet_display_col) ? 'Current Year Projected Sales' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_year_sales_proj',  $dashlet_display_col) ? $this->red_color_text($ytd_projected)  : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_year_gp_proj',  $dashlet_display_col) ? 'Current Year Projected GP$' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_year_gp_proj',  $dashlet_display_col) ? $this->red_color_text($ytd_projected_gp)  : '&nbsp;'). "</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td width='50%'>
                        <div>
                            <table width='100%'>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_sales_proj_vs_ly_sales_invoiced',  $dashlet_display_col) ? 'CY Proj Sls vs. CY Sls Budget' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_sales_proj_vs_ly_sales_invoiced',  $dashlet_display_col) ? $this->red_color_text($current_sls_proj_vs_ly_sls_invoiced)  : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_sales_proj_vs_ly_sales_invoiced',  $dashlet_display_col) ? 'CY Proj GP$ vs. CY GP$ Budget' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_sales_proj_vs_ly_sales_invoiced',  $dashlet_display_col) ? $this->red_color_text($current_gp_proj_vs_ly_gp_invoiced)  : '&nbsp;'). "</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>";
	}

	if($dashlet_display_col == array() ||
		in_array('current_year_sales_budget',  $dashlet_display_col) ||
		in_array('cy_gp_budget',  $dashlet_display_col) ||
		in_array('cy_gpp_budget',  $dashlet_display_col) ||
		in_array('att_y_gpp_proj_vs_cy_gpp_budget',  $dashlet_display_col) ||
		in_array('att_year_sls_proj_vs_cy_sales_budget',  $dashlet_display_col) ||
		in_array('att_year_gp_proj_vs_cy_gp_budget',  $dashlet_display_col) 		
	) {
$strTable .= "
                <tr>
                    <td width='50%'>
                        <div>
                            <table width='100%'>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_year_sales_budget',  $dashlet_display_col) ? 'Current Year Sales Budget'  : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('current_year_sales_budget',  $dashlet_display_col) ? $this->red_color_text($ytd_sales_budget)  : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('cy_gp_budget',  $dashlet_display_col) ? 'Current Year GP$ Budget' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('cy_gp_budget',  $dashlet_display_col) ? $this->red_color_text($cy_gp_budget)  : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('cy_gpp_budget',  $dashlet_display_col) ? 'Current Year GP% Budget' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('cy_gpp_budget',  $dashlet_display_col) ? $this->red_color_pst($cy_gpp_budget)  : '&nbsp;'). "</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td width='50%'>
                        <div>
                            <table width='100%'>
                               <!--  <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>Daily Target</td>
                                    <td>" . $this->red_color_text($daily_target) . "</td>
                                </tr>

                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>Daily Average Target</td>
                                    <td>" . $this->red_color_text($daily_average_target) . "</td>
                                </tr> -->
                                
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('att_year_sls_proj_vs_cy_sales_budget',  $dashlet_display_col) ? '% of Attainment YTD Sales to CY Sales Budget' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('att_year_sls_proj_vs_cy_sales_budget',  $dashlet_display_col) ? $this->red_color_pst($att_y_sls_proj_vs_cy_sls_budget)  : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('att_year_gp_proj_vs_cy_gp_budget',  $dashlet_display_col) ? '% of Attainment YTD GP$ to CY GP$ Budget' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('att_year_gp_proj_vs_cy_gp_budget',  $dashlet_display_col) ? $this->red_color_pst($att_y_gp_proj_vs_cy_gp_budget)  : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('att_y_gpp_proj_vs_cy_gpp_budget',  $dashlet_display_col) ? 'YTD GP% vs. CY GP% Budget' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('att_y_gpp_proj_vs_cy_gpp_budget',  $dashlet_display_col) ? $this->red_color_pst($att_y_gpp_proj_vs_cy_gpp_budget)  : '&nbsp;'). "</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>";
	}
	if($dashlet_display_col == array() ||
		in_array('ytd_sales_invoiced',  $dashlet_display_col) ||
		in_array('ytd_gp',  $dashlet_display_col) ||
		in_array('ytd_gpp',  $dashlet_display_col) ||
		in_array('ly_sales_invoiced',  $dashlet_display_col) ||
		in_array('ly_gp',  $dashlet_display_col) ||
		in_array('ly_gpp',  $dashlet_display_col)
		) {
$strTable .= "
                <tr><!-- ytd sales invoiced -->
                    <td width='50%'>
                        <div>
                            <table width='100%'>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_sales_invoiced',  $dashlet_display_col) ? 'YTD Sales Invoiced' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_sales_invoiced',  $dashlet_display_col) ? $this->red_color_text($ytd_sales_invoiced)  : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_gp',  $dashlet_display_col) ? 'YTD GP$' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_gp',  $dashlet_display_col) ? $this->red_color_text($ytd_gp)  : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_gpp',  $dashlet_display_col) ? 'YTD GP%' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_gpp',  $dashlet_display_col) ? $this->red_color_pst($ytd_gpp)  : '&nbsp;') . "</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td width='50%'>
                        <div>
                            <table width='100%'>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ly_sales_invoiced',  $dashlet_display_col) ? 'LY Sales Invoiced' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ly_sales_invoiced',  $dashlet_display_col) ? $this->red_color_text($ly_sales_invoiced)  : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ly_gp',  $dashlet_display_col) ? 'LY GP$': '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ly_gp',  $dashlet_display_col) ? $this->red_color_text($ly_gp)  : '&nbsp;'). "</td>   
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ly_gpp',  $dashlet_display_col) ? 'LY GP%' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ly_gpp',  $dashlet_display_col) ? $this->red_color_pst($ly_gpp)  : '&nbsp;'). "</td>   
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>";
	}

	if($dashlet_display_col == array() ||
		in_array('ytd_invoices',  $dashlet_display_col) ||
		in_array('mtd_invoices',  $dashlet_display_col) ||
		in_array('wtd_invoices',  $dashlet_display_col)
		) {
$strTable .= "
                <tr><!-- ytd sales invoiced -->
                    <td width='50%'>
                        <div>
                            <table width='100%'>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_invoices',  $dashlet_display_col) ? 'YTD - # of Transactions' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_invoices',  $dashlet_display_col) ? number_format($ytd_invoices,0,'.',',')  : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_invoices',  $dashlet_display_col) ? 'MTD - # of Transactions' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_invoices',  $dashlet_display_col) ? number_format($mtd_invoices,0,'.',',') : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('wtd_invoices',  $dashlet_display_col) ? 'WTD - # of Transactions' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('wtd_invoices',  $dashlet_display_col) ? number_format($wtd_invoices,0,'.',',') : '&nbsp;') . "</td>
                                </tr>
                                <!--<tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>LY - # of Transactions</td>
                                    <td style='font-size: 14px;'>???</td>
                                </tr>-->
                            </table>
                        </div>
                    </td>
                    <td width='50%'>
                        <div>
                            <table width='100%'>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_av_per_trans',  $dashlet_display_col) ? 'YTD Avg $ per transaction' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_av_per_trans',  $dashlet_display_col) ? number_format($ytd_av_per_trans,0,'.',',') : '&nbsp;') . "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_av_per_trans',  $dashlet_display_col) ? 'MTD Avg $ per transaction' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_av_per_trans',  $dashlet_display_col) ? number_format($mtd_av_per_trans,0,'.',',') : '&nbsp;') . "</td>   
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>&nbsp;</td>
                                    <td style='font-size: 14px;'>&nbsp;</td>
                                </tr>
                                <!--<tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>WTD Avg $ per transaction</td>
                                    <td style='font-size: 14px;'>???</td>   
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>LY - Avg $ per transaction</td>
                                    <td style='font-size: 14px;'>???</td>   
                                </tr>-->
                            </table>
                        </div>
                    </td>
                </tr>";
	}

if (	$mtd_sls_noem != '' || 
        $ytd_sls_noem != ''  || 
        $mtd_gp_noem != ''  || 
        $ytd_gp_noem != ''  || 
        $mtd_gpp_noem != ''  || 
        $ytd_gpp_noem != ''  || 
        $mtd_budget_noem_sales != ''  || 
        $ytd_budget_noem_sales != ''  ||
        $mtd_projected_noem != ''  ||
        $ytd_projected_noem != ''  ||
        $ly_sls_noem != ''  ||
        $ly_gpp_noem != '' ) {
 $strTable .= '<tr>
                    <td width="50%">
                        <div>
                            <table width="100%">
                                <tr class="yui-dt-odd">
                                    <td width="70%"  style="font-size: 14px;">' . (($mtd_sls_noem !== '') ? "MTD Non-OE Sales" : "&nbsp" ) .'</td>
                                    <td style="font-size: 14px;">$' .$mtd_sls_noem . '</td>
                                </tr>
                                <tr class="yui-dt-even">
                                    <td width="70%"  style="font-size: 14px;">' . (($mtd_gp_noem !== '') ? "MTD Non-OE GP" : "&nbsp;" ) . '</td>
                                    <td style="font-size: 14px;">$' . $mtd_gp_noem .'</td>
                                </tr>
                                <tr class="yui-dt-odd">
                                    <td width="70%"  style="font-size: 14px;">' . (( $mtd_gpp_noem) ? "MTD Non-OE GP %" : "&nbsp;") . '</td>
                                    <td style="font-size: 14px;">' . $this->red_color_pst($mtd_gpp_noem) .'</td>
                                </tr>
                                <tr class="yui-dt-odd">
                                    <td width="70%"  style="font-size: 14px;">' . (( $mtd_budget_noem_sales !== '') ? "MTD Non-OE Bgt Sls" : "&nbsp;" ) .'</td>
                                    <td style="font-size: 14px;">$' . $mtd_budget_noem_sales . '</td>
                                </tr>
                                <tr class="yui-dt-even">
                                    <td width="70%"  style="font-size: 14px;">' . (( $mtd_projected_noem !== '')  ? "MTD Non-OE Projected" : "&nbsp;" ) . '</td>
                                    <td style="font-size: 14px;">$' . $mtd_projected_noem . '</td>
                                </tr>
                                <tr class="yui-dt-odd">
                                    <td width="70%"  style="font-size: 14px;">' . (( $ytd_sls_noem !== '') ? "YTD Non-OE Sales" : "&nbsp;") . '</td>
                                    <td style="font-size: 14px;">$' . $ytd_sls_noem .'</td>
                                </tr>
                                <tr class="yui-dt-even">
                                    <td width="70%"  style="font-size: 14px;">' . (( $ytd_gp_noem !== '') ? "YTD Non-OE GP" : "&nbsp;") .'</td>
                                    <td style="font-size: 14px;">$' . $ytd_gp_noem . '</td>   
                                </tr>
                                <tr class="yui-dt-odd">
                                    <td width="70%"  style="font-size: 14px;">' . (( $ytd_gpp_noem !== '') ? "YTD Non-OE GP %": "&nbsp;" ) .'</td>
                                    <td style="font-size: 14px;">' . $this->red_color_pst($ytd_gpp_noem) . '</td>   
                                </tr>
                                <tr class="yui-dt-odd">
                                    <td width="70%"  style="font-size: 14px;">' . (( $ytd_budget_noem_sales !== '') ? "YTD Non-OE Bgt Sls" : "&nbsp;") . '</td>
                                    <td style="font-size: 14px;">$' . $ytd_budget_noem_sales . '</td>
                                </tr>
                                <tr class="yui-dt-even">
                                    <td width="70%"  style="font-size: 14px;">' . (( $ytd_projected_noem !== '') ? "YTD Non-OE Projected" : "&nbsp;") . '</td>
                                    <td style="font-size: 14px;">$' . $ytd_projected_noem . '</td>
                                </tr>
                                <tr class="yui-dt-odd">
                                    <td width="70%"  style="font-size: 14px;">' . (( $ly_sls_noem !== '') ? "LY Non-OE Sales" : "&nbsp;") . '</td>
                                    <td style="font-size: 14px;">$' . $ly_sls_noem . '</td>
                                </tr>
                                <tr class="yui-dt-odd">
                                    <td width="70%"  style="font-size: 14px;">' . (( $ly_gpp_noem !== '') ? "LY Non-OE GP %" : "&nbsp;") .'</td>
                                    <td style="font-size: 14px;">' . $this->red_color_pst($ly_gpp_noem) . '</td>
                                </tr>
                            </table>
                        </div>
                    </td>';
}

if($dashlet_display_col == array() ||
        in_array('mtd_sls_noem_divide_mtd_sales',  $dashlet_display_col) ||
        in_array('ly_sls_noem_divide_ly_sales',  $dashlet_display_col) ||
        in_array('ytd_sls_noem_divide_ytd_sales',  $dashlet_display_col) ||
        in_array('mtd_gp_noem_divide_mtd_gp',  $dashlet_display_col) ||
        in_array('mtd_gpp_noem_divide_mtd_gpp',  $dashlet_display_col) ||
        in_array('mtd_budget_noem_sales_divide_mtd_budget_sales',  $dashlet_display_col) ||
        in_array('mtd_projected_noem_divide_mtd_projected',  $dashlet_display_col) ||
        in_array('ytd_gp_noem_divide_ytd_gp',  $dashlet_display_col) ||
        in_array('ytd_gpp_noem_divide_ytd_gpp',  $dashlet_display_col) ||
        in_array('ytd_budget_noem_sales_divide_ytd_sales_budget',  $dashlet_display_col) ||
        in_array('ytd_projected_noem_divide_ytd_projected',  $dashlet_display_col) ||
        in_array('ly_gpp_noem_divide_ly_gpp',  $dashlet_display_col) ) {
$strTable .= "
            <td width='50%'>
                        <div>
                            <table width='100%'>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_sls_noem_divide_mtd_sales',  $dashlet_display_col) ? 'MTD % of NOE Sales vs. Total Sales' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_sls_noem_divide_mtd_sales',  $dashlet_display_col) ? $this->red_color_pst($mtd_sls_noem_divide_mtd_sales)  : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_gp_noem_divide_mtd_gp',  $dashlet_display_col) ? 'MTD % of NOE GP vs. Total GP' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_gp_noem_divide_mtd_gp',  $dashlet_display_col) ? $this->red_color_pst($mtd_gp_noem_divide_mtd_gp)  : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_gpp_noem_divide_mtd_gpp',  $dashlet_display_col) ? 'MTD NOE GP % vs. Total GP %' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_gpp_noem_divide_mtd_gpp',  $dashlet_display_col) ? $this->red_color_pst($mtd_gpp_noem_divide_mtd_gpp) : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_budget_noem_sales_divide_mtd_budget_sales',  $dashlet_display_col) ? 'MTD % of NOE Bgt Sls vs. Total Bgt Sls' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_budget_noem_sales_divide_mtd_budget_sales',  $dashlet_display_col) ? $this->red_color_pst($mtd_budget_noem_sales_divide_mtd_budget_sales) : '&nbsp;') . "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_projected_noem_divide_mtd_projected',  $dashlet_display_col) ? 'MTD % of NOE Projected vs. Total Projected' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_projected_noem_divide_mtd_projected',  $dashlet_display_col) ? $this->red_color_pst($mtd_projected_noem_divide_mtd_projected)  : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_sls_noem_divide_ytd_sales',  $dashlet_display_col) ? 'YTD % of NOE Sales vs. Total Sales' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_sls_noem_divide_ytd_sales',  $dashlet_display_col) ? $this->red_color_pst($ytd_sls_noem_divide_ytd_sales) : '&nbsp;') . "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_gp_noem_divide_ytd_gp',  $dashlet_display_col) ? 'YTD % of NOE GP vs. Total GP' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_gp_noem_divide_ytd_gp',  $dashlet_display_col) ? $this->red_color_pst($ytd_gp_noem_divide_ytd_gp) : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_gpp_noem_divide_ytd_gpp',  $dashlet_display_col) ? 'YTD NOE GP % vs. Total GP %' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_gpp_noem_divide_ytd_gpp',  $dashlet_display_col) ? $ytd_gpp_noem_divide_ytd_gpp : '&nbsp;') . "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_budget_noem_sales_divide_ytd_sales_budget',  $dashlet_display_col) ? 'YTD % of NOE Bgt Sls vs. Total Bgt Sls' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_budget_noem_sales_divide_ytd_sales_budget',  $dashlet_display_col) ? $this->red_color_pst($ytd_budget_noem_sales_divide_ytd_sales_budget)  : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_projected_noem_divide_ytd_projected',  $dashlet_display_col) ? 'YTD % of NOE Projected vs. Total Projected' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_projected_noem_divide_ytd_projected',  $dashlet_display_col) ? $this->red_color_pst($ytd_projected_noem_divide_ytd_projected) : '&nbsp;'). "</td>
                                </tr>
                                 <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ly_sls_noem_divide_ly_sales',  $dashlet_display_col) ? 'LY % of NOE Sales vs. Total Sales' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ly_sls_noem_divide_ly_sales',  $dashlet_display_col) ? $this->red_color_pst($ly_sls_noem_divide_ly_sales) : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ly_gpp_noem_divide_ly_gpp',  $dashlet_display_col) ? 'LY  NOE GP % vs. Total GP %' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ly_gpp_noem_divide_ly_gpp',  $dashlet_display_col) ? $this->red_color_pst($ly_gpp_noem_divide_ly_gpp) : '&nbsp;') . "</td>
                                </tr>
                                   </table>
                        </div>
                    </td>
                </tr>";
	}

if( $mtd_sls_undercar != ''  || 
        $ytd_sls_undercar != ''  || 
        $mtd_gp_undercar != ''  || 
        $ytd_gp_undercar != ''  || 
        $mtd_gpp_undercar != ''  || 
        $ytd_gpp_undercar != ''  || 
        $mtd_budget_undercar_sales != ''  || 
        $ytd_budget_undercar_sales != ''  ||
        $mtd_projected_undercar != ''  ||
        $ytd_projected_undercar != ''  ||
        $ly_sls_undercar != ''  ||
        $ly_gpp_undercar != '' 
        ) {
$strTable .= '<tr>
                    <td width="50%">
                        <div>
                            <table width="100%">
                                <tr class="yui-dt-odd">
                                    <td width="70%"  style="font-size: 14px;">' . (($mtd_sls_undercar !== '') ? "MTD UnderCar Sales" : "&nbsp;") . '</td>
                                    <td style="font-size: 14px;">$' . $mtd_sls_undercar .'</td>
                                </tr>
                                <tr class="yui-dt-even">
                                    <td width="70%"  style="font-size: 14px;">' . (( $mtd_gp_undercar !== '') ? "MTD UnderCar GP" : "&nbsp;") . '</td>
                                    <td style="font-size: 14px;">$' . $mtd_gp_undercar . '</td>
                                </tr>
                                <tr class="yui-dt-odd">
                                    <td width="70%"  style="font-size: 14px;">' . (( $mtd_gpp_undercar !== '') ? "MTD UnderCar GP %" : "&nbsp;") .'</td>
                                    <td style="font-size: 14px;">' . $this->red_color_pst($mtd_gpp_undercar) . '</td>
                                </tr>
                                <tr class="yui-dt-odd">
                                    <td width="70%"  style="font-size: 14px;">' . (( $mtd_budget_undercar_sales !== '') ? "MTD UnderCar Bgt Sls" : "&nbsp;") . '</td>
                                    <td style="font-size: 14px;">$' . $mtd_budget_undercar_sales . '</td>
                                </tr>
                                <tr class="yui-dt-even">
                                    <td width="70%"  style="font-size: 14px;">' . (( $mtd_projected_undercar !== '') ? "MTD UnderCar Projected" : "&nbsp;") . '</td>
                                    <td style="font-size: 14px;">$' . $mtd_projected_undercar . '</td>
                                </tr>
                                <tr class="yui-dt-odd">
                                    <td width="70%"  style="font-size: 14px;">' . (( $ytd_sls_undercar !== '') ? "YTD UnderCar Sales" : "&nbsp;") . '</td>
                                    <td style="font-size: 14px;">$' . $ytd_sls_undercar . '</td>
                                </tr>
                                <tr class="yui-dt-even">
                                    <td width="70%"  style="font-size: 14px;">' . (( $ytd_gp_undercar !== '') ? "YTD UnderCar GP" : "&nbsp;") . '</td>
                                    <td style="font-size: 14px;">$'.$ytd_gp_undercar . '</td>   
                                </tr>
                                <tr class="yui-dt-odd">
                                    <td width="70%"  style="font-size: 14px;">' . (( $ytd_gpp_undercar !== '') ? "YTD UnderCar GP %" : "&nbsp;") . '</td>
                                    <td style="font-size: 14px;">' . $this->red_color_pst($ytd_gpp_undercar) . '</td>   
                                </tr>
                                <tr class="yui-dt-odd">
                                    <td width="70%"  style="font-size: 14px;">' . (( $ytd_budget_undercar_sales !== '') ? "YTD UnderCar Bgt Sls"  : "&nbsp;") . '</td>
                                    <td style="font-size: 14px;">$' . $ytd_budget_undercar_sales . '</td>
                                </tr>
                                <tr class="yui-dt-even">
                                    <td width="70%"  style="font-size: 14px;">' . (($ytd_projected_undercar !== '') ? "YTD UnderCar Projected" : "&nbsp;") . '</td>
                                    <td style="font-size: 14px;">$' . $ytd_projected_undercar . '</td>
                                </tr>
                                <tr class="yui-dt-odd">
                                    <td width="70%"  style="font-size: 14px;">' . (( $ly_sls_undercar !== '') ? "LY UnderCar Sales" : "&nbsp;") . '</td>
                                    <td style="font-size: 14px;">$' . $ly_sls_undercar . '</td>
                                </tr>
                                <tr class="yui-dt-odd">
                                    <td width="70%"  style="font-size: 14px;">' . (( $ly_gpp_undercar !== '') ? "LY UnderCar GP %" : "&nbsp;") . '</td>
                                    <td style="font-size: 14px;">' . $this->red_color_pst($ly_gpp_undercar) . '</td>
                                </tr>
                            </table>
                        </div>
                    </td>';
}
if($dashlet_display_col == array() ||
        in_array('mtd_sls_undercar_divide_mtd_sales',  $dashlet_display_col) ||
        in_array('ly_sls_undercar_divide_ly_sales',  $dashlet_display_col) ||
        in_array('ytd_sls_undercar_divide_ytd_sales',  $dashlet_display_col) ||
        in_array('mtd_gp_undercar_divide_mtd_gp',  $dashlet_display_col) ||
        in_array('mtd_gpp_undercar_divide_mtd_gpp',  $dashlet_display_col) ||
        in_array('mtd_budget_undercar_sales_divide_mtd_budget_sales',  $dashlet_display_col) ||
        in_array('mtd_projected_undercar_divide_mtd_projected',  $dashlet_display_col) ||
        in_array('ytd_gp_undercar_divide_ytd_gp',  $dashlet_display_col) ||
        in_array('ytd_gpp_undercar_divide_ytd_gpp',  $dashlet_display_col) ||
        in_array('ytd_budget_undercar_sales_divide_ytd_sales_budget',  $dashlet_display_col) ||
        in_array('ytd_projected_undercar_divide_ytd_projected',  $dashlet_display_col) ||
        in_array('ly_gpp_undercar_divide_ly_gpp',  $dashlet_display_col) ) {
$strTable .= "
            <td width='50%'>
                        <div>
                            <table width='100%'>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_sls_undercar_divide_mtd_sales',  $dashlet_display_col) ? 'MTD % of Undercar Sales vs. Total Sales' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_sls_undercar_divide_mtd_sales',  $dashlet_display_col) ? $this->red_color_pst($mtd_sls_undercar_divide_mtd_sales)  : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_gp_undercar_divide_mtd_gp',  $dashlet_display_col) ? 'MTD % of Undercar GP vs. Total GP' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_gp_undercar_divide_mtd_gp',  $dashlet_display_col) ? $this->red_color_pst($mtd_gp_undercar_divide_mtd_gp)  : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_gpp_undercar_divide_mtd_gpp',  $dashlet_display_col) ? 'MTD Undercar GP % vs. Total GP %' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_gpp_undercar_divide_mtd_gpp',  $dashlet_display_col) ? $this->red_color_pst($mtd_gpp_undercar_divide_mtd_gpp) : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_budget_undercar_sales_divide_mtd_budget_sales',  $dashlet_display_col) ? 'MTD % of Undercar Bgt Sls vs. Total Bgt Sls' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_budget_undercar_sales_divide_mtd_budget_sales',  $dashlet_display_col) ? $this->red_color_pst($mtd_budget_undercar_sales_divide_mtd_budget_sales) : '&nbsp;') . "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_projected_undercar_divide_mtd_projected',  $dashlet_display_col) ? 'MTD % of Undercar Projected vs. Total Projected' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('mtd_projected_undercar_divide_mtd_projected',  $dashlet_display_col) ? $this->red_color_pst($mtd_projected_undercar_divide_mtd_projected) : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_sls_undercar_divide_ytd_sales',  $dashlet_display_col) ? 'YTD % of Undercar Sales vs. Total Sales' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_sls_undercar_divide_ytd_sales',  $dashlet_display_col) ? $this->red_color_pst($ytd_sls_undercar_divide_ytd_sales) : '&nbsp;') . "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_gp_undercar_divide_ytd_gp',  $dashlet_display_col) ? 'YTD % of Undercar GP vs. Total GP' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_gp_undercar_divide_ytd_gp',  $dashlet_display_col) ? $this->red_color_pst($ytd_gp_undercar_divide_ytd_gp) : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_gpp_undercar_divide_ytd_gpp',  $dashlet_display_col) ? 'YTD Undercar GP % vs. Total GP %' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_gpp_undercar_divide_ytd_gpp',  $dashlet_display_col) ? $this->red_color_pst($ytd_gpp_undercar_divide_ytd_gpp) : '&nbsp;') . "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_budget_undercar_sales_divide_ytd_sales_budget',  $dashlet_display_col) ? 'YTD % of Undercar Bgt Sls vs. Total Bgt Sls' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_budget_undercar_sales_divide_ytd_sales_budget',  $dashlet_display_col) ? $this->red_color_pst($ytd_budget_undercar_sales_divide_ytd_sales_budget)  : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_projected_undercar_divide_ytd_projected',  $dashlet_display_col) ? 'YTD % of Undercar Projected vs. Total Projected' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ytd_projected_undercar_divide_ytd_projected',  $dashlet_display_col) ? $this->red_color_pst($ytd_projected_undercar_divide_ytd_projected) : '&nbsp;'). "</td>
                                </tr>
                                 <tr class='yui-dt-even'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ly_sls_undercar_divide_ly_sales',  $dashlet_display_col) ? 'LY % of Undercar Sales vs. Total Sales' : '&nbsp;'). "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ly_sls_undercar_divide_ly_sales',  $dashlet_display_col) ? $this->red_color_pst($ly_sls_undercar_divide_ly_sales) : '&nbsp;'). "</td>
                                </tr>
                                <tr class='yui-dt-odd'>
                                    <td width='70%'  style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ly_gpp_undercar_divide_ly_gpp',  $dashlet_display_col) ? 'LY Undercar GP % vs. Total GP %' : '&nbsp;') . "</td>
                                    <td style='font-size: 14px;'>" . ($dashlet_display_col == array() || in_array('ly_gpp_undercar_divide_ly_gpp',  $dashlet_display_col) ? $this->red_color_pst($ly_gpp_undercar_divide_ly_gpp) : '&nbsp;') . "</td>
                                </tr>
                                   </table>
                        </div>
                    </td>
                </tr>";
	}
$strTable .= "
            </tbody>
        </table>";
        echo $strTable;
    }

    function build__slsm($compiled_slsm, $is_user_id) {
        foreach ($compiled_slsm as $k=>$v) {
            $compiled_slsm[$k] = "'$v'";
        }

        $h = ''
                . $this->user_add_on($is_user_id)
                . ' WHERE dsls_dailysales.deleted = 0 AND slsm IN (' . implode(', ', $compiled_slsm) . ') '
        ;
        return $h;
    }

    function user_add_on($is_user_id) {
        if (!$is_user_id) {
            return ;
        }

        return ''
                . ' AND x_m.assigned_user_id="' . $this->user_id . '" '
        ;
    }

    function dsls_sales_summary($record_day, $record_type_slsm = "SLSM", $record_id_slsm, $record_id_region, $record_id_location) {
	global $current_user;
        $acl_granted = $this->acl_where_summary();

	$record_id_slsm = !in_array($record_id_slsm, array('undefined', 'all', '')) ? explode(";", $record_id_slsm) : null;
	$record_id_region = !in_array($record_id_region, array('undefined', 'all', '')) ? explode(";", "r".$record_id_region) : null;
	$slsm_area_obj = new fmp_Param_RegLoc($current_user->id);
        $reg_loc_object = $slsm_area_obj->init($current_user->id);
	//print_r();
	if(!is_null($record_id_region)) {
		foreach($record_id_region as $reg_loc_value) {
			if(substr($reg_loc_value, 0, 1) == "r") {
				$region_array[substr($reg_loc_value, 1)] = substr($reg_loc_value, 1);
				$locations_available = array();
				$locations_available = $slsm_area_obj->compile__available_regions_below(array(0 => null), substr($reg_loc_value, 1));

				foreach($locations_available as $region_value=>$locations_value) {

						
							$location_array[$locations_value] = $locations_value;
						
					
				}
			} else {
				$location_array[$reg_loc_value] = $reg_loc_value;
			}
		}
	}
	$record_id_location = !in_array($record_id_location, array('undefined', 'all', '')) ? explode(";", $record_id_location) : null;
	
	if(!is_null($record_id_location)) {
		foreach($record_id_location as $reg_loc_value) {
			if(substr($reg_loc_value, 0, 1) == "r") {
				$region_array[substr($reg_loc_value, 1)] = substr($reg_loc_value, 1);
				$locations_available = array();
				$locations_available = $slsm_area_obj->compile__available_regions_below(array(0 => null), substr($reg_loc_value, 1));
			
				foreach($locations_available as $region_value=>$locations_value) {

						
							$location_array[$locations_value] = $locations_value;
						
					
				}
			} else {
				$location_array[$reg_loc_value] = $reg_loc_value;
			}
		}
	}
	//print_r($location_array);
	//print_r($region_array);
	//if(!is_null($record_id_slsm) || !is_null($record_id_region) || !is_null($record_id_location)) {
	if(!is_null($record_id_slsm) xor !is_null($record_id_region) xor !is_null($record_id_location)) {
		
            $query = "  SELECT
                        sum(sales) as sales,
                        sum(credits) as credits
                    FROM dsls_sales_summary
                    WHERE record_date = '".$record_day."'
                    ";
	    
            if(!is_null($record_id_slsm)) {
		
		if (count($record_id_slsm) != 0) {
			//$record_id_slsm = $this->getSlsmBelow($_SESSION['fmp_slsm'], $record_id_slsm[0]);
                        $ids_slsm = array();
                        foreach($record_id_slsm as $id_slsm){
                            $result = $this->getSlsmBelow($_SESSION['fmp_slsm'], $id_slsm);
                            $ids_slsm = array_merge($ids_slsm, $result);
                        }
                        if(!empty($ids_slsm)){
                            $record_id_slsm = $ids_slsm;
                        }
		}
		
		if(count($acl_granted) != 0 && count($acl_granted['slsm']) > 0) {
	                $query .= "AND (record_type = '".$record_type_slsm."' AND record_id IN (" . implode(', ', $acl_granted['slsm']) . ") AND record_id IN (".implode(', ' , $record_id_slsm)."))";
			
		} else {
			$query .= "AND (record_type = '".$record_type_slsm."' AND (0) )";		
		}
            }else {
		/*if(count($acl_granted) != 0 && count($acl_granted['slsm']) > 0) {
			$query .= "AND (record_type = '".$record_type_slsm."' AND record_id IN (" . implode(', ', $acl_granted['slsm']) . ") )";
		} else {
			$query .= "AND (record_type = '".$record_type_slsm."' AND (0) )";
		}*/

            }

            /*if(!is_null($record_id_region) && isset($region_array)) {
		if(count($acl_granted) != 0 && count($acl_granted['region']) > 0) {
	                $query .= " AND (record_type='REGION' AND record_id IN (" . implode(", ", $acl_granted['region']) . ") AND record_id IN (".implode(", ", $region_array). "))";
			//print_r($query);
		} else {
			$query .= " AND (record_type='REGION' AND (0) )";
		}
            }*/

            if(isset($location_array)) {
                if(count($acl_granted) != 0 && count($acl_granted['location']) > 0) {
	                $query .= " AND (record_type='LOCATION' AND record_id IN (" . implode(", ", $acl_granted['location']) . ") AND record_id IN (".implode(", ", $location_array). "))";
		//print_r()
		} else {
			$query .= " AND (record_type='LOCATION' AND record_id IN (".implode(", ", $location_array). "))";
		}
            }
            if(is_null($record_id_slsm) && !isset($location_array) && !isset($region_array)) {
		//print_r("regloc not selected");
                if(count($acl_granted) != 0 && count($acl_granted['region']) > 0) {
	                $query .= " OR (record_type='REGION' AND record_id IN (" . implode(", ", $acl_granted['region']) . "))";
		} else {
			$query .= " OR (record_type='REGION' AND (0) )";
		}
            }
            //$query .= ")";
	    //print_r($query);
        } else if (is_null($record_id_slsm) AND is_null($record_id_region) AND is_null($record_id_location)) {
            $query = "  SELECT
                        sum(sales) as sales,
                        sum(credits) as credits
                    FROM dsls_sales_summary
                    WHERE record_date = '".$record_day."'
                    AND (record_type = 'SLSM' ";
	
		if(count($acl_granted) != 0 && count($acl_granted['slsm']) > 0) {
			$query .= " AND record_id IN (" . implode(', ', $acl_granted['slsm']) . ") )";
		} else {
			$query .= " AND (0) )";
		}
		//print_r($query);
/*
                if(count($acl_granted) != 0 && count($acl_granted['region']) > 0) {
	                $query .= " OR (record_type='REGION' AND record_id IN (" . implode(", ", $acl_granted['region']) . "))";
		} else {
			$query .= " OR (record_type='REGION' AND (0) )";
		}
*/

        } else {
		return false;	
	}
        //print_r($query);
        return $query;
    }

    function sales_today_previous($date_sales_summ, $type, $record_id_slsm, $record_id_region, $record_id_location) {
        global $current_user;
        $db = &PearDatabase::getInstance();
        $sales = 0;
        $credits = 0;
	$query = $this->dsls_sales_summary($date_sales_summ, $type, $record_id_slsm, $record_id_region, $record_id_location);
	if($query !== false) {        
		$query_sales_summ = $db->query($query);
		$res_sales_summ = $db->fetchByAssoc($query_sales_summ);
	//        while($res_sales_summ = $db->fetchByAssoc($query_sales_summ)) {
		$sales += $res_sales_summ['sales'];
		$credits += $res_sales_summ['credits'];
	//        }

		$net_sales = $sales-$credits;
	
		$result = array ();
	
		$result['sales'] = in_array($current_user->getPreference('daily_sales_Dealer_post'), array("all", "undefined", "")) ? $sales : "N/A";
		$result['credits'] = in_array($current_user->getPreference('daily_sales_Dealer_post'), array("all", "undefined", "")) ? $credits  : "N/A";
		//$result['net_sales'] = $net_sales;
		$result['net_sales'] = in_array($current_user->getPreference('daily_sales_Dealer_post'), array("all", "undefined", "")) ? $sales + $credits : "N/A";
	} else {
		$result['sales'] = "N/A";
		$result['credits'] = "N/A";
		$result['net_sales'] = "N/A";
	}
        return $result;
    }

    function get_reg_loc($reg_loc) {
        $query = '';
        if(is_numeric(substr($reg_loc,1))) {
            if (substr($reg_loc,0,1) == 'r') {
                $query .= ' AND region = '.substr($reg_loc,1) ;
            }
            else {
                $query .= ' AND loc = '.$reg_loc;
            }

            return $query;
        }
        return $query;
    }

    function get_dealer_type_query($dealer_type) {
        $query = '';
        if (is_numeric($dealer_type)) {
            $query .= ' AND x_a.dealertype_c = "'.$dealer_type.'"' ;
        }
        else {
            $query .= '';
        }

        return $query;
    }

    function red_color_text ($value = 0) {
        if ($value < 0) {
            return '<p style="color: red">('.'$'.number_format(abs($value+0),0,'.',',').')</p>';
        }
        else {
            return '<p>'.'$'.number_format($value+0,0,'.',',').'</p>';
        }
    }
    
    function red_color_pst ($value = 0) {
        if ((float)$value < 0) {
            return '<p style="color: red">('.number_format($value,2).'%)</p>';
        }
        else {
            return '<p>'.number_format($value,2).'%</p>';
        }
    }
}
