<?php
require_once('include/utils.php');
require_once('data/SugarBean.php');
require_once('include/TimeDate.php');

class ReportParameter extends SugarBean {

    var $id;
    var $friendly_name;
    var $default_name;
    var $default_value;
    var $description;
    var $range_name; //SQL,LIST,SIMPLE,DATE,CURRENT_USER
    var $range_options; //module to select, sql-query, value-list

    var $created_by;
    var $date_entered;
    var $date_modified;
    var $modified_user_id;

    var $range_description;

    var $table_name = "zucker_reportparameters";
    var $object_name = "ReportParameter";
    var $module_dir = "ZuckerReportParameter";

    function ReportParameter() {
        parent::SugarBean();
        $this->new_schema = true;
        $this->disable_row_level_security = true;
    }

    function save($check_notify = false) {
        return parent::save($check_notify);
    }

    function retrieve($id = NULL, $encode=false) {
        $ret = parent::retrieve($id, $encode);
        return $ret;
    }

    function getByDefaultname($default_name) {
        $seed = new ReportParameter();
        $results = $seed->get_full_list("", "default_name='".$default_name."'");
        if (!empty($results)) {
            $result = $results[0];
            $result->retrieve();
            return $result;
        } else {
            return NULL;
        }
    }



    function mark_relationships_deleted($id) {
        $query = "UPDATE zucker_reportparameterlink set deleted=1 where parameter_id='$id'";
        $this->db->query($query, true, "Error marking record deleted: ");
    }

    function fill_in_additional_list_fields() {
        $this->fill_in_additional_detail_fields();
    }

    function fill_in_additional_detail_fields() {
        global $current_language;

        $mod_list_strings = return_mod_list_strings_language($current_language, "ZuckerReports");
        $this->range_description = $mod_list_strings['PARAM_RANGE_TYPES'][$this->range_name];
    }

    function get_summary_text() {
        return $this->friendly_name." (".$this->range_description.")";
    }

    function input_required() {
        if ($this->range_name == "CURRENT_USER") {
            return false;
        }
        if ($this->range_name == "DATE_NOW") {
            return false;
        }
        if ($this->range_name == "SCRIPT") {
            return false;
        }
        return true;
    }

    function get_parameter_value($rp, $rpl) {

        //echo 'we are in function get_parametr_value';
        global $current_language, $current_user;

        if ($rp->range_name == "CURRENT_USER") {
            return $current_user->id;
        } else if ($rp->range_name == "SCRIPT") {
            return eval($rp->range_options);
        } else if ($rp->range_name == "DATE") {

            $timedate = new TimeDate();

            if ($rp->default_name == 'FMPREP_SSAR_DATE' || $rp->default_name == 'FMPREP_SSAR_CLOSING_DATE') {

                require_once 'fmp.class.param.datefromto.php';
                $o = new fmp_Param_DateFromTo($current_user->id, $rpl->name);
                $o->init();

                include 'modules/ZuckerQueryTemplate/fmp.config.php';

                if ($rpl->template_id == $FMPCO_REP_ID_SOR) {
                    $use_datetime_shift = false;
                    return ''
                            . ' UNIX_TIMESTAMP(x_o.date_closed) BETWEEN'
                            . ' ' . $o->r_date_from($use_datetime_shift) . ' AND ' . $o->r_date_to($use_datetime_shift)
                    ;
                }
                if($rp->default_name == 'FMPREP_SSAR_CLOSING_DATE') {

//                     return ''
//	                    . ' AND ((UNIX_TIMESTAMP(x_o.date_closed) BETWEEN'
//	                        . ' ' . $o->r_date_from() . ' AND ' . $o->r_date_to()
//                                . ') OR (x_o.date_closed IS NULL) '
//                             . ') '
//	                        ;
                    return array(
                            'closing_date_from' => $o->r_date_from(),
                            'closing_date_to' => $o->r_date_to()
                    );

                }

                return array(
                        'meet-call' => ''
                                . 'UNIX_TIMESTAMP(x_m.date_start) BETWEEN'
                                . ' ' . $o->r_date_from() . ' AND ' . $o->r_date_to()
                        ,
                        'task' => ''
                                . 'UNIX_TIMESTAMP(x_m.date_due) >= ' . $o->r_date_from()
                                . ' AND UNIX_TIMESTAMP(x_m.date_start) <= '  . $o->r_date_to()
                );
            }

            $result = $timedate->to_db_date($_REQUEST[$rpl->name], false);
            return $result;
        } else if ($rp->range_name == "DATE_NOW") {

            $timedate = new TimeDate();
            $result = $timedate->get_gmt_db_datetime();
            return $result;

        } else if ($rp->range_name == "DATE_ADD" || $rp->range_name == "DATE_SUB") {
            $timedate = new TimeDate();

            $arr = split("::", $_REQUEST[$rpl->name]);
            if (count($arr) == 2) {
                $count = $arr[0];
                $type = $arr[1];

                if ($type == "MINUTE") {
                    $count *= 60;
                } else if ($type == "HOUR") {
                    $count *= 60 * 60;
                } else if ($type == "DAY") {
                    $count *= 60 * 60 * 24;
                } else if ($type == "WEEK") {
                    $count *= 60 * 60 * 24 * 7;
                } else if ($type == "MONTH") {
                    $count *= 60 * 60 * 24 * 30;
                } else if ($type == "YEAR") {
                    $count *= 60 * 60 * 24 * 365;
                }
                if ($rp->range_name == "DATE_SUB") $count *= -1;
            } else {
                $count = 0;
            }

            $time = time();
            $time += $count;
            $result = date('Y-m-d H:i:s', $time);
            $result = $timedate->to_db($result);
            return $result;

        }
        elseif ($rp->default_name == 'FMPREP_SSAR_DEALERTYPE') {


            if (!isset($_REQUEST[$rpl->name])) {
                return ;
            }

            if (!is_array($_REQUEST[$rpl->name])) {
                return ;
            }

            $dat = array();
            foreach($_REQUEST[$rpl->name] as $k=>$v) {
                if ($v != 0) {
                    $dat[$k] = $v-1;
                    continue;
                }
                $dat = array();
                break;
            }


            if (!$dat) {
                return ;
            }

            if (count($dat) == 1) {
                return 'AND x_a.dealertype_c="' . ((int) $dat[0]) . '"';
            }

            foreach($dat as $k=>$v) {
                $dat[$k] = '\'' . $v . '\'';
            }

            return 'AND x_a.dealertype_c IN (' . implode(',', $dat) . ')';
        }
        elseif ($rp->default_name == 'FMPREP_SSAR_SALESSTAGE') {

            if (!isset($_REQUEST[$rpl->name])) {
                return ;
            }

            if (!is_array($_REQUEST[$rpl->name])) {
                return ;
            }

            $dat = array();
            foreach($_REQUEST[$rpl->name] as $k=>$v) {
                if ($v === '0') {
                    $dat = array();
                    break;
                }
                $dat[$k] = $v;
            }

            if (!$dat) {
                return ;
            }
            return $dat;

//            if (count($dat) == 1) {
//                return 'AND x_o.sales_stage="' . ($dat[0]) . '"';
//            }
//
//            foreach($dat as $k=>$v) {
//                $dat[$k] = '\'' . $v . '\'';
//            }
//
//            return 'AND x_o.sales_stage IN (' . implode(',', $dat) . ')';
        }
        elseif ($rp->default_name == 'FMPREP_OPP_SALESSTAGE') {

            if (!isset($_REQUEST[$rpl->name])) {
                return ;
            }

            if (!is_array($_REQUEST[$rpl->name])) {
                return ;
            }

            $dat = array();
            foreach($_REQUEST[$rpl->name] as $k=>$v) {
                if ($v === '0') {
                    $dat = array();
                    break;
                }
                $dat[$k] = $v;
            }

            if (!$dat) {
                return ;
            }
            //return $dat;

            if (count($dat) == 1) {
                return 'AND x_o.sales_stage="' . ($dat[0]) . '"';
            }

            foreach($dat as $k=>$v) {
                $dat[$k] = '\'' . $v . '\'';
            }

            return 'AND x_o.sales_stage IN (' . implode(',', $dat) . ')';
        }
        elseif ($rp->default_name == 'FMPREP_SSAR_LOC') {
            $r_regloc = array();

            if (isset($_REQUEST['fmpfilter_locs'])) {
                if ($_REQUEST['fmpfilter_locs']) {
                    $r_regloc = explode(',', $_REQUEST['fmpfilter_locs']);
                }
            }



            $r_slsm = array();
            if (isset($_REQUEST['fmpfilter_slsm'])) {
                if ($_REQUEST['fmpfilter_slsm']) {
                    $r_slsm = explode(',', $_REQUEST['fmpfilter_slsm']);
                }
            }

            $r_fmpfilter_slsm__include_user_id = 1;
            if (!isset($_REQUEST['fmpfilter_slsm__include_user_id'])) {
                $r_fmpfilter_slsm__include_user_id = 0;
            }

            require_once 'fmp.class.param.regloc-slsm-sql.php';
            $o = new fmp_Param_RegLoc_SLSM_SQL($current_user->id);
            $q = $o->build__query_addon($r_regloc, $r_slsm, $r_fmpfilter_slsm__include_user_id);
            include 'modules/ZuckerQueryTemplate/fmp.config.php';
            if ($rpl->template_id == $FMPCO_REP_ID_SOR) {
                $q = str_replace('x_m.assigned_user_id','x_o.assigned_user_id',$q);
            }

            return $q;
        }
        elseif ($rp->default_name == 'FMPREP_SSAR_SORT') {

            include 'modules/ZuckerQueryTemplate/fmp.config.php';
            require_once 'fmp.class.param.fieldlist.activity.php';
            $list = fmp_Param_FieldList_Activity::$F;
            $fields_grouped = fmp_Param_FieldList_Activity::$FF;
            $def = 'date_time_of_activity';
            if ($rpl->template_id == $FMPCO_REP_ID_SOR) {
                require_once 'fmp.class.param.fieldlist.opportunity.php';
                $list = fmp_Param_FieldList_Opportunity::$F;
                $fields_grouped = fmp_Param_FieldList_Opportunity::$FF;
                $def = 'date_closed';
            }

            require_once 'fmp.class.param.sort.php';
            $o = new fmp_Param_Sort($current_user->id, $list, $fields_grouped, $def);
            return $o->build_query_addon($rp->description);
        }
        elseif ($rp->default_name == 'FMPREP_SSAR_FIELDLIST') {
            require_once 'fmp.class.param.fieldlist.activity.php';
            require_once 'fmp.class.param.fieldlist.opportunity.php';
            include 'modules/ZuckerQueryTemplate/fmp.config.php';
        }
        elseif ($rp->default_name == 'FMPREP_SSAR_WAREHOUSE') {
            require_once 'fmp.class.param.warehouse.php';
            $o = new fmp_Param_Warehouse();
            return $o->build_query_addon();
        }
        elseif ($rp->default_name == 'FMPREP_SSAR_SHOWACTIVITIES') {
            require_once 'fmp.class.param.showactivities.php';
            return ;
        }
        elseif ($rp->default_name == 'FMPREP_SSAR_TEXTFILTER') {
            require_once 'fmp.class.param.textfilter.php';
            include 'modules/ZuckerQueryTemplate/fmp.config.php';
            require_once 'fmp.class.param.fieldlist.activity.php';
            require_once 'fmp.class.param.fieldlist.opportunity.php';

            $name = 'a';
            $fields = fmp_Param_FieldList_Activity::$F;
            $fields_grouped = fmp_Param_FieldList_Activity::$FF;
            if ($rpl->template_id == $FMPCO_REP_ID_SOR) {
                $fields = fmp_Param_FieldList_Opportunity::$F;
                $fields_grouped = fmp_Param_FieldList_Opportunity::$FF;
                $name = 'o';
            }

            $o = new fmp_Param_Textfilter($name, $fields, $fields_grouped);
            return $o->build_query_addon();
        } elseif ($rp->default_name == 'FMPREP_SSAR_ALLACTIVITYFIX') {
            $r_regloc = array();

            if (isset($_REQUEST['fmpfilter_locs'])) {
                if ($_REQUEST['fmpfilter_locs']) {
                    $r_regloc = explode(',', $_REQUEST['fmpfilter_locs']);
                }
            }

            $r_slsm = array();
            if (isset($_REQUEST['fmpfilter_slsm'])) {
                if ($_REQUEST['fmpfilter_slsm']) {
                    $r_slsm = explode(',', $_REQUEST['fmpfilter_slsm']);
                }
            }

            $r_fmpfilter_slsm__include_user_id = 1;
            if (!isset($_REQUEST['fmpfilter_slsm__include_user_id'])) {
                $r_fmpfilter_slsm__include_user_id = 0;
            }

            require_once 'fmp.class.param.regloc-slsm-sql.php';
            $o = new fmp_Param_RegLoc_SLSM_SQL($current_user->id);
            $q = $o->build__query_addon__all_activity_fix($r_regloc, $r_slsm, $r_fmpfilter_slsm__include_user_id);

//            pr($q);

            return $q;
        } else {
            return $_REQUEST[$rpl->name];
        }
    }

    function get_parameter_html($rp, $rpl) {

//                echo '<pre>';
//                print_r($rpl);
//                echo '</pre>';

        global $app_strings;
        global $current_language, $current_user, $theme;

        $focus =& new QueryTemplate();

        if(isset($_REQUEST['record'])) {
            $focus->retrieve($_REQUEST['record']);
        }



        $report_id = $_REQUEST['record'];
        $report_name =strtolower($focus->name);
        $settings_duration = 0;
        switch ($report_name) {
            case 'opportunities':
                if($current_user->getPreference('ORPersonalSettings') != null) {
                    $settings_duration = $current_user->getPreference('ORPersonalSettings');
                }
                else {
                    $settings_duration = 30*24*60*60;
                }
                break;
            default:
                if($current_user->getPreference('SASRPersonalSettings') != null ) {
                    $settings_duration = $current_user->getPreference('SASRPersonalSettings');
                }
                else {
                    $settings_duration = 7*24*60*60;
                }
                break;
        }


        $current_date = mktime();

//                echo '<pre>';
//                print_r($current_date);
//                echo '</pre>';


        $mod_strings = return_module_language($current_language, "ZuckerReportParameter");
        $mod_list_strings = return_mod_list_strings_language($current_language, "ZuckerReportParameter");

        $xtpl = new XTemplate('modules/ZuckerReportParameter/ParameterFill.html');
        $xtpl->assign("MOD", $mod_strings);
        $xtpl->assign("APP", $app_strings);
        $xtpl->assign("THEME", $theme);

        $selected_val = $rpl->default_value;
        if($rpl->name == FMPREP_SSAR_DEALERTYPE || $rpl->name == FMPREP_SSAR_SALESSTAGE || $rpl->name == FMPREP_OPP_SALESSTAGE) {
            $settings_expired_time = (int) $current_user->getPreference($report_id.'modified');
            if($settings_expired_time == 0) $settings_expired_time = $current_date;
            $preference_selected_val = $current_user->getPreference($report_id.$rpl->name);
            if(is_array($preference_selected_val) && count($preference_selected_val)!=0 && $settings_expired_time >= $current_date ) $selected_val = $preference_selected_val;
        }

        if(isset ($_REQUEST['run']) && $_REQUEST['run'] == 'true' && ($rpl->name == FMPREP_SSAR_DEALERTYPE  || $rpl->name == FMPREP_OPP_SALESSTAGE)) {
            //echo "we are here";
            $selected_val = $_REQUEST[$rpl->name];
            $current_user->setPreference($report_id.$rpl->name, $_REQUEST[$rpl->name]);
            $current_user->setPreference($report_id.'modified', $current_date+$settings_duration);
        }
        if(isset ($_REQUEST['run']) && $_REQUEST['run'] == 'true' && $rpl->name == FMPREP_SSAR_SALESSTAGE && $_REQUEST['format'] != 'CSV') {
            //echo "we are here";
            $selected_val = $_REQUEST[$rpl->name];
            $current_user->setPreference($report_id.$rpl->name, $_REQUEST[$rpl->name]);
            $current_user->setPreference($report_id.'modified', $current_date+$settings_duration);
        }

//		if (!empty($_REQUEST[$rpl->name])) {
//
//                                $selected_val = $_REQUEST[$rpl->name];
//                                $current_user->setPreference($report_id.$rpl->name, $selected_val);
//                                //$current_user->setPreference($report_id, $report_id);
//
////                        echo '<pre>';
////                        print_r($selected_val);
////                        echo '<br/>';
////                        print_r($rpl->name);
////                        echo '<br/>';
////                        echo '</pre>';
//		}



        if ($rp->range_name == 'SQL') {
            if ($rp->default_name == 'FMPREP_SSAR_SLSM') {

                require_once 'fmp.class.param.slsm.php';
                $o = new fmp_Param_SLSM($current_user->id);
                $o->init();
                $parameter_html = $o->html($rp->description);
                unset($o);

            }
            elseif ($rp->default_name == 'FMPREP_SSAR_LOC') {

                require_once 'fmp.class.param.regloc.php';
                $o = new fmp_Param_RegLoc($current_user->id);
                $o->init();
                $parameter_html = $o->html($rp->description);
                unset($o);
            }
            elseif ($rp->default_name == 'FMPREP_SSAR_SORT') {

                include 'modules/ZuckerQueryTemplate/fmp.config.php';
                require_once 'fmp.class.param.fieldlist.activity.php';
                $list = fmp_Param_FieldList_Activity::$F;
                $fields_grouped = fmp_Param_FieldList_Activity::$FF;
                $def = 'date_time_of_activity';
                if ($rpl->template_id == $FMPCO_REP_ID_SOR) {
                    require_once 'fmp.class.param.fieldlist.opportunity.php';
                    $list = fmp_Param_FieldList_Opportunity::$F;
                    $fields_grouped = fmp_Param_FieldList_Opportunity::$FF;
                    $def = 'date_closed';
                }

                require_once 'fmp.class.param.sort.php';
                $o = new fmp_Param_Sort($current_user->id, $list, $fields_grouped, $def);
                $parameter_html = $o->html($rp->description);
                unset($o);
            } elseif ($rp->default_name == 'FMPREP_SSAR_FIELDLIST') {
                include 'modules/ZuckerQueryTemplate/fmp.config.php';
                if ($rpl->template_id == $FMPCO_REP_ID_SOR) {
                    require_once 'fmp.class.param.fieldlist.opportunity.php';
                    $o = new fmp_Param_FieldList_Opportunity();
                } else {
                    require_once 'fmp.class.param.fieldlist.activity.php';
                    $o = new fmp_Param_FieldList_Activity();
                }

                $parameter_html = $o->html($rp->description);

                unset($o);

            } elseif ($rp->default_name == 'FMPREP_SSAR_WAREHOUSE') {
                require_once 'fmp.class.param.warehouse.php';
                $o = new fmp_Param_Warehouse();
                $parameter_html = $o->html($rp->description);
            } elseif ($rp->default_name == 'FMPREP_SSAR_SHOWACTIVITIES') {
                require_once 'fmp.class.param.showactivities.php';
                $o = new fmp_Param_Showactivities();
                $parameter_html = $o->html($rp->description);
            } elseif ($rp->default_name == 'FMPREP_SSAR_TEXTFILTER') {
                require_once 'fmp.class.param.textfilter.php';
                include 'modules/ZuckerQueryTemplate/fmp.config.php';
                require_once 'fmp.class.param.fieldlist.activity.php';
                require_once 'fmp.class.param.fieldlist.opportunity.php';

                $name = 'a';
                $fields = fmp_Param_FieldList_Activity::$F;
                $fields_grouped = fmp_Param_FieldList_Activity::$FF;
                if ($rpl->template_id == $FMPCO_REP_ID_SOR) {
                    $fields = fmp_Param_FieldList_Opportunity::$F;
                    $fields_grouped = fmp_Param_FieldList_Opportunity::$FF;
                    $name = 'o';
                }

                $o = new fmp_Param_Textfilter($name, $fields, $fields_grouped);
                $parameter_html = $o->html($rp->description);
            } elseif ($rp->default_name == 'FMPREP_SSAR_ALLACTIVITYFIX') {
                $parameter_html = '';
            } else {
                $param_table = $rp->get_sql_table();
                asort($param_table);
                if (is_array($param_table)) {
                    $xtpl->assign("PARAM_FRIENDLY_NAME", $rpl->friendly_name);
                    $xtpl->assign("PARAM_NAME", $rpl->name);
                    $xtpl->assign("PARAM_SELECTION", get_select_options_with_id($param_table, $selected_val));
                    $xtpl->parse("SQL");
                    $parameter_html = $xtpl->text("SQL");
                } else {
                    $parameter_html = $param_table."<br/>";
                }
            }
        } else if ($rp->range_name == 'LIST') {
            $list = $rp->get_list_table();
            asort($list);
            $xtpl->assign("PARAM_FRIENDLY_NAME", $rpl->friendly_name);
            $xtpl->assign("PARAM_NAME", $rpl->name);
            $xtpl->assign("PARAM_SELECTION", get_select_options_with_id($list, $selected_val));
            $xtpl->parse("LIST");
            $parameter_html = $xtpl->text("LIST");

        } else if ($rp->range_name == 'DROPDOWN') {
            $app_list_strings = return_app_list_strings_language($current_language);

            $app_list_strings[$rp->range_options] = array_merge(array('-1' => ' -- ALL -- '), $app_list_strings[$rp->range_options]);

            if ($rp->default_name == 'FMPREP_SSAR_DEALERTYPE') {
                if (!$selected_val) {
                    $selected_val[0] = 0;
                } else {
                    foreach($selected_val as $k=>$v) {
                        if ($v != 0) {
                            continue;
                        }

                        $selected_val = array();
                        $selected_val[0] = 0;
                        break;
                    }
                }
                $list = $rp->get_list_table();
                $xtpl->assign("PARAM_FRIENDLY_NAME", $rp->description);
                $xtpl->assign("PARAM_NAME", $rpl->name . '[]');
                asort($app_list_strings[$rp->range_options]);
                $xtpl->assign("PARAM_SELECTION", get_select_options_with_id($app_list_strings[$rp->range_options], $selected_val));
                $xtpl->parse("LIST_2");
                $parameter_html = $xtpl->text("LIST_2");
            }
            else if ($rp->default_name == 'FMPREP_SSAR_SALESSTAGE' || $rp->default_name == 'FMPREP_OPP_SALESSTAGE') {


                if (!$selected_val) {
                    $selected_val[0] = '0';
                } else {
                    foreach($selected_val as $k=>$v) {
                        if ($v === '0') {
                            $selected_val = array();
                            $selected_val[0] = '0';
                            break;
                        }
                    }
                }

                $list = $rp->get_list_table();
                $xtpl->assign("PARAM_FRIENDLY_NAME", $rp->description);
                $xtpl->assign("PARAM_NAME", $rpl->name . '[]');
                asort($app_list_strings[$rp->range_options]);
                //$xtpl->assign("PARAM_SELECTION", get_select_options_with_id_separate_key_sales_stage($app_list_strings[$rp->range_options],$app_list_strings[$rp->range_options], $selected_val));
                if($rp->default_name == 'FMPREP_SSAR_SALESSTAGE' && $_REQUEST['format'] == 'CSV') {
                    $selected_val = array();
                    $xtpl->assign("PARAM_SELECTION", get_select_options_with_id_separate_key_sales_stage($app_list_strings[$rp->range_options],$app_list_strings[$rp->range_options], $selected_val));
                    $xtpl->parse("LIST_3");
                    $parameter_html = $xtpl->text("LIST_3");
                } else {
                    $xtpl->assign("PARAM_SELECTION", get_select_options_with_id_separate_key_sales_stage($app_list_strings[$rp->range_options],$app_list_strings[$rp->range_options], $selected_val));
                    $xtpl->parse("LIST_2");
                    $parameter_html = $xtpl->text("LIST_2");
                }
            } else {
                $list = $rp->get_list_table();
                $xtpl->assign("PARAM_FRIENDLY_NAME", $rpl->friendly_name);
                $xtpl->assign("PARAM_NAME", $rpl->name);
                asort($app_list_strings[$rp->range_options]);
                $xtpl->assign("PARAM_SELECTION", get_select_options_with_id($app_list_strings[$rp->range_options], $selected_val));
                $xtpl->parse("LIST");
                $parameter_html = $xtpl->text("LIST");
            }

        } else if ($rp->range_name == 'SIMPLE') {
            $xtpl->assign("PARAM_FRIENDLY_NAME", $rpl->friendly_name);
            $xtpl->assign("PARAM_NAME", $rpl->name);
            $xtpl->assign("PARAM_VALUE", $selected_val);
            $xtpl->parse("SIMPLE");
            $parameter_html = $xtpl->text("SIMPLE");

        } else if ($rp->range_name == 'DATE') {
            $timedate = new TimeDate();

            if ($rp->default_name == 'FMPREP_SSAR_DATE' || $rp->default_name == 'FMPREP_SSAR_CLOSING_DATE') {
                if($rp->default_name == 'FMPREP_SSAR_DATE') {
                    $name = explode('|', $rp->description);

                    $name_from = '';
                    $name_to = '';
                    if (isset($name[0])) {
                        $name_from = $name[0];
                    }
                    if (isset($name[1])) {
                        $name_to = $name[1];
                    }

                    include 'modules/ZuckerQueryTemplate/fmp.config.php';
                    require_once 'fmp.class.param.datefromto.php';
                    $o = new fmp_Param_DateFromTo($current_user->id, $rpl->name);
                    $o->init();

                    $value_from = $o->date_from;
                    $value_to = $o->date_to;

                    if ($rpl->template_id == $FMPCO_REP_ID_SOR || $rp->default_name == 'FMPREP_SSAR_CLOSING_DATE') {
                        $value_from = date('01/01/Y');
                        $value_to = date('12/31/Y');
                    }

                    if (isset($_REQUEST['run']) && ($_REQUEST['run'] == 'true')) {
                        if (isset($_REQUEST[$rpl->name . '_FROM']) && $rpl->name == FMPREP_SSAR_DATE) {
                            $value_from = $_REQUEST[$rpl->name . '_FROM'];
                            $current_user->setPreference($report_id.'time_from', $_REQUEST[$rpl->name . '_FROM']);
                        }

                        if (isset($_REQUEST[$rpl->name . '_TO']) && $rpl->name == FMPREP_SSAR_DATE) {
                            $value_to = $_REQUEST[$rpl->name . '_TO'];
                            $current_user->setPreference($report_id.'time_to', $_REQUEST[$rpl->name . '_TO']);
                        }
                    }
                    
                    $current_date = mktime();
                    $settings_expired_time = (int) $current_user->getPreference($report_id.'modified');
                    if($settings_expired_time == 0) $settings_expired_time = $current_date;
                    
                    if($rpl->name == FMPREP_SSAR_DATE || $rpl->name == FMPREP_SSAR_DATE_FROM) {
                        if ($settings_expired_time >= $current_date) {
                            $value_from = $current_user->getPreference($report_id.'time_from') === null ? $value_from: $current_user->getPreference($report_id.'time_from');
                            $value_to = $current_user->getPreference($report_id.'time_to') === null ? $value_to: $current_user->getPreference($report_id.'time_to');
                        }
                    }

                    $xtpl->assign("PARAM_FRIENDLY_NAME_FROM", $name_from);
                    $xtpl->assign("PARAM_NAME_FROM", $rpl->name . '_FROM');
                    $xtpl->assign("PARAM_VALUE_FROM", $value_from);
                    $xtpl->assign("CALENDAR_LANG_FROM", "en");
                    $xtpl->assign("USER_DATEFORMAT_FROM", '('. $timedate->get_user_date_format().')');
                    $xtpl->assign("CALENDAR_DATEFORMAT_FROM", $timedate->get_cal_date_format());

                    $xtpl->assign("PARAM_FRIENDLY_NAME_TO", $name_to);
                    $xtpl->assign("PARAM_NAME_TO", $rpl->name . '_TO');
                    $xtpl->assign("PARAM_VALUE_TO", $value_to);
                    $xtpl->assign("CALENDAR_LANG_TO", "en");
                    $xtpl->assign("USER_DATEFORMAT_TO", '('. $timedate->get_user_date_format().')');
                    $xtpl->assign("CALENDAR_DATEFORMAT_TO", $timedate->get_cal_date_format());

                    $xtpl->parse("DATE_2");
                    $parameter_html = $xtpl->text("DATE_2");
                }
                if($rp->default_name == 'FMPREP_SSAR_CLOSING_DATE') {
                    $name = explode('|', $rp->description);
                    $name_from = '';
                    $name_to = '';
                    if (isset($name[0])) {
                        $name_from = $name[0];
                    }
                    if (isset($name[1])) {
                        $name_to = $name[1];
                    }

                    include 'modules/ZuckerQueryTemplate/fmp.config.php';
                    require_once 'fmp.class.param.datefromto.php';
                    $o = new fmp_Param_DateFromTo($current_user->id, $rpl->name);
                    $o->init();

                    $value_from = date('01/01/Y');
                    $value_to = date('12/31/Y');


                    if (isset($_REQUEST['run']) && ($_REQUEST['run'] == 'true') && ($_REQUEST['format'] != 'CSV')) {
                        if (isset($_REQUEST[$rpl->name . '_FROM']) && $rpl->name == 'FMPREP_SSAR_CLOSING_DATE') {
                            $value_from = $_REQUEST[$rpl->name . '_FROM'];
                            $current_user->setPreference($report_id.'closing_time_from', $_REQUEST[$rpl->name . '_FROM']);
                        }

                        if (isset($_REQUEST[$rpl->name . '_TO']) && $rpl->name == 'FMPREP_SSAR_CLOSING_DATE') {
                            $value_to = $_REQUEST[$rpl->name . '_TO'];
                            $current_user->setPreference($report_id.'closing_time_to', $_REQUEST[$rpl->name . '_TO']);
                        }
                    }

                    if ($settings_expired_time >= $current_date) {
                        $value_from = $current_user->getPreference($report_id.'closing_time_from') === null ? $value_from: $current_user->getPreference($report_id.'closing_time_from');
                        $value_to = $current_user->getPreference($report_id.'closing_time_to') === null ? $value_to: $current_user->getPreference($report_id.'closing_time_to');
                    }


                    $xtpl->assign("PARAM_FRIENDLY_NAME_FROM", $name_from);
                    $xtpl->assign("PARAM_NAME_FROM", $rpl->name . '_FROM');
                    $xtpl->assign("PARAM_VALUE_FROM", $value_from);
                    $xtpl->assign("CALENDAR_LANG_FROM", "en");
                    $xtpl->assign("USER_DATEFORMAT_FROM", '('. $timedate->get_user_date_format().')');
                    $xtpl->assign("CALENDAR_DATEFORMAT_FROM", $timedate->get_cal_date_format());

                    $xtpl->assign("PARAM_FRIENDLY_NAME_TO", $name_to);
                    $xtpl->assign("PARAM_NAME_TO", $rpl->name . '_TO');
                    $xtpl->assign("PARAM_VALUE_TO", $value_to);
                    $xtpl->assign("CALENDAR_LANG_TO", "en");
                    $xtpl->assign("USER_DATEFORMAT_TO", '('. $timedate->get_user_date_format().')');
                    $xtpl->assign("CALENDAR_DATEFORMAT_TO", $timedate->get_cal_date_format());
                    if($_REQUEST['format'] == 'CSV') {
                        $xtpl->parse("DATE_3");
                        $parameter_html = $xtpl->text("DATE_3");
                    } else {
                        $xtpl->parse("DATE_2");
                        $parameter_html = $xtpl->text("DATE_2");
                    }
                }

            } else {
                $xtpl->assign("PARAM_FRIENDLY_NAME", $rpl->friendly_name);
                $xtpl->assign("PARAM_NAME", $rpl->name);
                $xtpl->assign("PARAM_VALUE", $selected_val);
                $xtpl->assign("CALENDAR_LANG", "en");
                $xtpl->assign("USER_DATEFORMAT", '('. $timedate->get_user_date_format().')');
                $xtpl->assign("CALENDAR_DATEFORMAT", $timedate->get_cal_date_format());
                $xtpl->parse("DATE");
                $parameter_html = $xtpl->text("DATE");
            }

        } else if ($rp->range_name == 'DATE_ADD' || $rp->range_name == 'DATE_SUB') {

            $xtpl->assign("PARAM_FRIENDLY_NAME", $rpl->friendly_name);
            $xtpl->assign("PARAM_NAME", $rpl->name);
            $xtpl->assign("PARAM_VALUE", $selected_val);

            $arr = split("::", $selected_val);
            if (count($arr) == 2) {
                $count = $arr[0];
                $type = $arr[1];
            } else {
                $count = 0;
                $type = NULL;
            }
            $xtpl->assign("PARAM_VALUE_COUNT", $count);
            asort($mod_list_strings['PARAM_DATE_TYPES']);
            $xtpl->assign("PARAM_SELECTION", get_select_options_with_id($mod_list_strings['PARAM_DATE_TYPES'], $type));

            $xtpl->parse("DATE_CALC");
            $parameter_html = $xtpl->text("DATE_CALC");
        }

        return $parameter_html;
    }

    function get_sql_table($query = "", $limit = -1) {
        if (empty($query)) {
            $query = $this->range_options;
        }
        if ($limit > 0) {
            $rs = $this->db->limitQuery($query,0,$limit,false);
        } else {
            $rs = $this->db->query($query, false);
        }
        if(!empty($rs)) {
            $result = array();
            while(($row = $this->db->fetchByAssoc($rs)) != null) {
                $keys = array_keys($row);
                $key = $row[$keys[0]];
                if (count($row) == 1) {
                    $value = $key;
                } else {
                    $value = $row[$keys[1]];
                }
                $result[$key] = $value;
            }
        } else {
            $result = $this->db->last_error;
        }
        return $result;
    }

    function get_list_table($list = "") {
        if (empty($list)) {
            $list = $this->range_options;
        }
        $list = split(",", $list);
        $result = array();
        foreach ($list as $l) {
            $result[$l] = $l;
        }
        return $result;
    }
}
