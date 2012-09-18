<?php
require_once 'QueryTemplate.php';

class QueryTemplate_FMP_Activity extends QueryTemplate {
    const FMPCO_PATH_HTML = 'modules/ZuckerQueryTemplate/html/';

    protected $fp = null;
    protected $log_counter = 0;
    
    public static $FMPCO_TPL = 'sales_summary/';
    public static $FMPCO_TOTAL_ANALYSIS = array();

    public function retrieve($id = NULL, $encode=false) 
    {
        $o = parent::retrieve($id, $encode);

        if (!isset($o->id)) {
            return ;
        }

        include 'fmp.config.php';
        if ($o->id != $FMPCO_REP_ID__SSAR) {
            return ;
        }

        return $o;
    }


    public function get_format_selection() 
    {
        global $current_language, $app_strings;
    
        $mod_strings = return_module_language($current_language, $this->module_dir);
        $mod_list_strings = return_mod_list_strings_language($current_language, $this->module_dir);
    
        $x = $mod_list_strings['QUERY_EXPORT_TYPES'];
        if (isset($x['SIMPLEHTML'])) {
            unset($x['SIMPLEHTML']);
        }
        
        if (isset($x['TABLE'])) {
            unset($x['TABLE']);
        }
        $mod_list_strings['QUERY_EXPORT_TYPES'] = $x;

        if (isset($_REQUEST["format"])) {
            if (!array_key_exists($_REQUEST["format"], $mod_list_strings["QUERY_EXPORT_TYPES"])) {
                $_REQUEST["format"] = "HTML";
            }
        }
        if (!isset($_REQUEST["format"])) {
            $_REQUEST["format"] = "HTML";
        }

        if ($_REQUEST["format"] == "CSV") {
            $this->report_result_type = "FILE";
        } else if ($_REQUEST["format"] == "TABLE") {
            $this->report_result_type = "INLINE";
        } else if ($_REQUEST["format"] == "HTML" || $_REQUEST["format"] == "SIMPLEHTML") {
            $this->report_result_type = "FILE";
        }

        asort($mod_list_strings["QUERY_EXPORT_TYPES"]);

        return get_select_options_with_id($mod_list_strings["QUERY_EXPORT_TYPES"], $_REQUEST["format"]);
    }
    
    public function get_format_parameters() 
    {
        global $current_language, $app_strings;

        $mod_strings = return_module_language($current_language, $this->module_dir);
        $mod_list_strings = return_mod_list_strings_language($current_language, $this->module_dir);

        $xtpl = new XTemplate('modules/ZuckerQueryTemplate/OnDemand.html');
        $xtpl->assign("MOD", $mod_strings);
        $xtpl->assign("APP", $app_strings);     

        if ($_REQUEST["format"] == "CSV") {
            asort($mod_list_strings["COL_DELIMS"]);
            asort($mod_list_strings["ROW_DELIMS"]);
            $xtpl->assign("COL_DELIM_SELECTION", get_select_options_with_id($mod_list_strings["COL_DELIMS"], $_REQUEST["col_delim"]));
            $xtpl->assign("ROW_DELIM_SELECTION", get_select_options_with_id($mod_list_strings["ROW_DELIMS"], $_REQUEST["row_delim"]));
            if ($this->get_format_parameters__include_header()) {
                $xtpl->assign("INCLUDE_HEADER_CHECKED", "checked");
            }
            $xtpl->parse("queryCSV");
            return $xtpl->text("queryCSV");
        } else if ($_REQUEST["format"] == "HTML"  || $_REQUEST["format"] == "SIMPLEHTML") {
            if ($this->get_format_parameters__include_header()) {
                $xtpl->assign("INCLUDE_HEADER_CHECKED", "checked");
            }
            $xtpl->parse("queryHTML");
            return $xtpl->text("queryHTML");
        } else if ($_REQUEST["format"] == "TABLE") {
            if ($this->get_format_parameters__include_header()) {
                $xtpl->assign("INCLUDE_HEADER_CHECKED", "checked");
            }
            $xtpl->parse("queryTABLE");
            return $xtpl->text("queryTABLE");
        }
    }
    
    protected function get_format_parameters__include_header() 
    {
        if (isset($_REQUEST['run'])) {
            if ($_REQUEST['run'] == 'true') {
                if (!isset($_REQUEST["include_header"])) {
                    return false;
                }
            }            
        }

        return true;
    }
    
    public function get_parameter_links() 
    {
        $links = parent::get_parameter_links();

        $order = array(
            'FMPREP_SSAR_ALLACTIVITYFIX' => 0,

            'FMPREP_SSAR_DATE' => 1,
            'FMPREP_SSAR_DEALERTYPE' => 2,
            'FMPREP_SSAR_LOC' => 3,
            'FMPREP_SSAR_SLSM' => 4,
            'FMPREP_SSAR_SALESSTAGE' => 5,
            'FMPREP_SSAR_CLOSING_DATE' => 6,
            'FMPREP_SSAR_SORT' => 7,
            'FMPREP_SSAR_FIELDLIST' => 8,
            'FMPREP_SSAR_TEXTFILTER' => 9
        );

        $out = array();
        foreach($links as $k=>$v) {
            if (!isset($order[$v->name])) {
                continue;
            }

            $out[$order[$v->name]] = $v;
        }

        ksort($out);
        

        return $out;
    }
    
    public function get_all() 
    {
        return ;
    }

    protected function path_html($fname, $def = false) 
    {
        if ($def) {
            return self::FMPCO_PATH_HTML . $fname;
        }
        return self::FMPCO_PATH_HTML . self::$FMPCO_TPL . $fname;
    }
    

    protected function html__datetime_mk($mktime) 
    {
        global $timedate;
        if (!$timedate) {
            $timedate = new TimeDate();
        }

        if (!$mktime) {
            return ;
        }
        
        $dbf = $timedate->get_db_date_time_format();
        $date = date($dbf, $mktime);
        return $timedate->to_display_date_time(  $date  );
    }

    protected function html__date_mk($mktime) 
    {
        global $timedate;
        if (!$timedate) {
            $timedate = new TimeDate();
        }

        if (!$mktime) {
            return ;
        }
        
        $dbf = $timedate->get_db_date_time_format();
        $date = date($dbf, $mktime);
        return $timedate->to_display_date( $date, $dst = false );
    }

    protected function html__row__with_html_spaces($row, $exclude=array()) 
    {
        foreach($row as $k=>$v) {
            if (!trim($row[$k])) {
                $row[$k] = '&nbsp;';
                continue;
            }
            
            if (in_array($k, $exclude)) {
                continue;
            }
            $row[$k] = $this->format_value_for_html($row[$k]);
        }
        return $row;
    }

    protected function html__sales_stage__val($key) 
    {
        global $app_list_strings; 
        if (isset($app_list_strings['sales_stage_dom'][$key])) {
            return $app_list_strings['sales_stage_dom'][$key];
        }
        return ;
    }

    protected function html__total_analysis($sum) 
    {
        $total_analysis = $sum['activity'];

        $label = 'TOTALS/ANALYSIS: ';
        $label = '<strong style="margin-left: 10px; color: #000000;">' . $label . '</strong>';

        $h1 = array();
        foreach($total_analysis['meetings'] as $stat_k=>$stat_dat) {

            $h1[] = '' 
                . '<tr>' 
                    . '<td width="80" style="border: 0;">'  . $stat_dat['desc'] . '</td>' 
                    . '<td width="30" style="border: 0; text-align: right;">'  . $stat_dat['total'] . '</td>' 
                . '</tr>'
                ;
        }
        $h1 = ''
            . '<table cellspacing="0" cellspadding="0" border="0" style="margin-top: 7px; margin-left: 10px;">'
                . '<tr><td colspan="2" style="font-weight: bold; border: 0;">Meetings: </td></tr>'
                . implode('', $h1)
            . '</table>'
            ;

        $h2 = array();
        foreach($total_analysis['calls'] as $stat_k=>$stat_dat) {
            $h2[] = '' 
                . '<tr>' 
                    . '<td width="80" style="border: 0;">'  . $stat_dat['desc'] . '</td>' 
                    . '<td width="30" style="border: 0; text-align: right;">'  . $stat_dat['total'] . '</td>' 
                . '</tr>'
                ;
        }
        $h2 = ''
            . '<table cellspacing="0" cellspadding="0" border="0" style="margin-top: 7px; margin-left: 10px;">'
                . '<tr><td colspan="2" style="font-weight: bold; border: 0;">Calls: </td></tr>'
                . implode('', $h2)
            . '</table>'
            ;

        $h3 = array();
        foreach($total_analysis['tasks'] as $stat_k=>$stat_dat) {
            $h3[] = '' 
                . '<tr>' 
                    . '<td width="80" style="border: 0;">'  . $stat_dat['desc'] . '</td>' 
                    . '<td width="30" style="border: 0; text-align: right;">'  . $stat_dat['total'] . '</td>' 
                . '</tr>'
                ;
        }
        $h3 = ''
            . '<table cellspacing="0" cellspadding="0" border="0" style="margin-top: 7px; margin-bottom: 7px; margin-left: 10px;">'
                . '<tr><td colspan="2" style="font-weight: bold; border: 0;">Tasks: </td></tr>'
                . implode('', $h3)
            . '</table>'
            ;

        $total_o = $sum['opportunity'];
        $h4 = ''
            . '<table cellspacing="0" cellspadding="0" border="0" style="margin-left: 10px; margin-top: 7px;">'
                . '<tr>'
                    . '<td width="250" style="border: 0; font-weight: bold;">Total Opportunities: </td>' 
                    . '<td width="230" style="border: 0;">' . $total_o['total_o'] . '</td>' 
                . '</tr>'
                . '<tr><td colspan="2" style="border: 0;">&nbsp;</td></tr>'
                . '<tr>'
                    . '<td style="border: 0; font-weight: bold;">Closed Won/Total Opportunities, (%)</td>' 
                    . '<td style="border: 0;">' . $total_o['win_p'] . '</td>' 
                . '</tr>'
                . '<tr><td colspan="2" style="border: 0;">&nbsp;</td></tr>'
                . '<tr>'
                    . '<td style="border: 0; font-weight: bold;">Closed Won/(Closed Lost+Closed Won), (%)</td>' 
                    . '<td style="border: 0;">' . $total_o['win2_p'] . '</td>' 
                . '</tr>'
            . '</table>'
            ;

        $h = ''
            . '<tr height="20">'
                . '<td style="background-color:#cccccc; border-top: 0; border-right:0 ;" colspan="20">'
                    . $label  
                . '</td>'
            . '</tr>'

            . '<tr height="20">'
                . '<td colspan="20" style="border-top: 1px solid #000000; border-right: 0;" valign="top">'

                    . '<table cellspacing="0" cellpadding="0" border="0">'
                        . '<tr>'
                            . '<td style="border-top: 0px;" width="150" valign="top">'
                                . $h1
                                . $h2
                                . $h3
                            . '</td>'
                            . '<td style="border-right: 0px;" valign="top">'
                                . $h4
                            . '</td>'
                        . '</tr>'
                    . '</table>'

                . '</td>'
            . '</tr>'
            ;
            
        return '' 
            . '<table cellspacing="0" cellpadding="0" border="0" style="margin-top: 20px;" class="listView">' 
                . $h 
            . '</table>';
    }

    protected function html__not_found() 
    {
        return '<tr><td colspan="20" align="left" height="20">&nbsp;&nbsp;Not found</td></tr>';
    }

    protected function html__sales($val) 
    {
        if (!$val) {
            return 0;
        }

        return number_format($val, 0, '.', ',');
    }

    protected function html__status($type, $key, &$app_list_strings) 
    {
        if (strtolower($type) == 'meeting') {
            if (isset($app_list_strings['meeting_status_dom'][$key])) {
                return $app_list_strings['meeting_status_dom'][$key];
            }
            return ;
        }

        if (strtolower($type) == 'call') {
            if (isset($app_list_strings['call_status_dom'][$key])) {
                return $app_list_strings['call_status_dom'][$key];
            }
            return ;
        }

        if (isset($app_list_strings['task_status_dom'][$key])) {
            return $app_list_strings['task_status_dom'][$key];
        }
        return ;
    }

    protected function html_row($row, $class, $a_field_list, $duplicated) 
    {
        if ($duplicated) {

            $row['customer_no'] = null;
            $row['account_name'] = null;
            $row['sales_rep_id'] = null;
//            $row['sales_rep_last_name'] = null;
            $row['sales_rep_name'] = null;
            $row['user_login'] = null;
            $row['type_of_activity'] = null;
            $row['held_not_held'] = null;
            $row['date_time_of_activity'] = null;
            $row['subject'] = null;
            $row['contact_name'] = null;
            $row['contact_address'] = null;
            $row['contact_phone'] = null;
            $row['points'] = null;
            $row['outcome'] = null;
        	/*
            $row['customer_no'] = '';
            $row['account_name'] = '';
            $row['sales_rep_id'] = '';
//            $row['sales_rep_last_name'] = null;
            $row['sales_rep_name'] = '';
            $row['user_login'] = '';
            $row['type_of_activity'] = '';
            $row['held_not_held'] = '';
            $row['date_time_of_activity'] = '';
            $row['subject'] = '';
            $row['contact_name'] = '';
            $row['contact_address'] = '';
            $row['contact_phone'] = '';
            $row['points'] = '';
            $row['outcome'] = '';
            */
        }
        
        $url_acc = 'index.php?module=Accounts&action=DetailView&record=' . $row['dev_account_id'];
        $url_acc = $this->url($url_acc);

        $module = 'Meetings';
        if ($row['type_of_activity'] == 'Call') {
            $module = 'Calls';
        } 
        elseif ($row['type_of_activity'] == 'Task') {
            $module = 'Tasks';
        }
        
        $url_act = 'index.php?module=' . $module . '&action=DetailView&record=' . $row['dev_activity_id'];
        $url_act = $this->url($url_act);

        $url_user = 'index.php?module=Users&action=DetailView&record=' . $row['dev_user_id'];
        $url_user = $this->url($url_user);

        $url_opp = 'index.php?module=Opportunities&action=DetailView&record=' . $row['dev_opportunity_id'];
        $url_opp = $this->url($url_opp);


        $sales_rep = '&nbsp;';
        if (!$duplicated) { 
            $sales_rep = array();
            $sales_rep[] = '<span class="fmpGrayed">Name: </span>' . $row['sales_rep_name'];
//            $sales_rep[] = '<span class="fmpGrayed">Name: </span>' . $row['account_name'];
            $sales_rep[] = '<span class="fmpGrayed">ID: </span>' . $row['sales_rep_id'];
            $sales_rep[] = '<span class="fmpGrayed">User: </span>'
                    . '<a href="' . $url_user . '" target="_blank">'
        		. $row['user_login']
                    . '</a>'
                    ;
            $sales_rep = implode('<br>', $sales_rep);
        }

        $account_name = '&nbsp;';
        if (!$duplicated) {
            $account_name = array();
            if ($row['account_name'] != '&nbsp;') {
                $account_name[] = ''
                    . '<a href="' . $url_acc . '" target="_blank">' . $row['account_name'] . '</a>'
                    ;
            }
            $p = strtolower($row['parent_type']); 
            if ( $p != 'accounts') {
                if ($row['parent_type'] == '&nbsp;') {
                    $row['parent_type'] = null;
                }
                if (!$row['parent_type']) {
                    $row['parent_type'] = 'none';
                }
                
                if ($row['parent_name'] == '&nbsp;') {
                    $row['parent_name'] = null;
                }
                if (!$row['parent_name']) {
                    $row['parent_name'] = 'none';
                }
                
                $account_name[] = ''
                    . '<span class="fmpGrayed">Related To: </span>'
                    . $row['parent_type']
                    ;
                $account_name[] = ''
                    . '<span class="fmpGrayed">Name: </span>'
                    . $row['parent_name']
                    ;
            }
            $account_name = implode('<br>', $account_name);
        }
        
        $contact = '&nbsp;';
        if (!$duplicated) {
            $contact = array();
            if ($row['contact_name'] != '&nbsp;') {
                $contact[] = ''
                    . trim($row['contact_name'])
                    ;
            }
            if ($row['contact_address'] != '&nbsp;') {
                $contact[] = ''
                    . trim($row['contact_address'])
                    ;
            }
            if ($row['contact_phone'] != '&nbsp;') {
                $contact[] = ''
                    . trim($row['contact_phone'])
                    ;
            }
            $contact = implode(',<br>', $contact);
        }
        $due_date = '';
        if ($row['type_of_activity'] == 'Task'){
            if($row['date_end_of_activity']){
                $due_date = ' - '. $row['date_end_of_activity'];
            } else {
                $due_date = ' - '. 'No due date set';
            }
        }

        $h_rows = array(
            'customer_no' => '<td class="' . $class . '">' . $row['customer_no'] . '</td>',

            'account_name' => '<td class="' . $class . '">' . $account_name . '</td>',
//            'sales_rep_last_name' => '<td class="' . $class . '">' . $sales_rep . '</td>',
            'sales_rep_name' => '<td class="' . $class . '">' . $sales_rep . '</td>',
            'type_of_activity' => '<td class="' . $class . '">' . $row['type_of_activity'] . '</td>',
            'held_not_held' => '<td class="' . $class . '">' . $row['held_not_held'] . '</td>',
            'date_time_of_activity' => '<td class="' . $class . '">' . $row['date_time_of_activity'] .$due_date. '</td>',
            'subject' => '' 
                . '<td class="' . $class . '">' 
                    . '<a href="' . $url_act . '" target="_blank">'
                        . $row['subject'] 
                    . '</a>' 
                . '</td>',
            'contact_name' => '<td class="' . $class . '">' . $contact . '</td>',
            'points' => '<td class="' . $class . ' discuss-points">' . $row['points'] . '</td>',
            'outcome' => '<td class="' . $class . ' outcome">' . $row['outcome'] . '</td>',
            'product' => ''
                . '<td class="' . $class . '">' 
                    . '<a href="' . $url_opp . '" target="_blank">'
                        . $row['product'] 
                    . '</a>' 
                . '</td>',
            'sales' => '<td class="' . $class . '">' . $row['sales'] . '</td>',
            //'estimated_monthly_sales' => '<td class="' . $class . '">' . $row['sales']/12 . '</td>',
            'estimated_monthly_sales' => '<td class="' . $class . '">' . $row['estimated_monthly_sales']. '</td>',
            'sales_stage' => '<td class="' . $class . '">' . $row['sales_stage'] . '</td>',
            'additional_opp_info' => '<td class="' . $class . '">' . $row['additional_opp_info'] . '</td>',
            'opupdate' => '<td class="' . $class . '">' . $row['opupdate'] . '</td>',
            'date_closed' => '<td class="' . $class . '">' . $row['date_closed'] . '</td>',
            'probability' => '<td class="' . $class . '">' . $row['probability'] . '</td>',
            'op_date_modified' => '<td class="' . $class . ' right">' . $row['op_date_modified'] . '</td>'
        );

        $h = array();
        $h_sep = array();
        foreach($h_rows as $col_id=>$v) {
            if (!isset($a_field_list[$col_id])) {
                continue;
            }

            $h[] = $v;
            $h_sep[] = '<td class="listViewHRS1 ' . $class . '"></td>';
        }

        $h = ''
            . '<tr height="20">'
                . implode('', $h)
            . '</tr>'
            . '<tr>'
                . implode('', $h_sep) 
            . '</tr>'
            ;
        return $h;
    }

    protected function html__row__preprocess($row) 
    {
        global $app_list_strings, $sugar_config;

        if (($row['probability'])) {
            $row['probability'] .= "%";
        }

        $row['sales_stage'] = $this->html__sales_stage__val($row['sales_stage']);

        $row['date_time_of_activity'] = $this->html__datetime_mk($row['date_time_of_activity']);
        $row['date_end_of_activity'] = $this->html__datetime_mk($row['date_end_of_activity']);
        $row['date_closed'] = $this->html__date_mk($row['date_closed']);
        $row['held_not_held']    = $this->html__status(
                                                $row['type_of_activity'], 
                                                $row['held_not_held'], 
                                                $app_list_strings
                                            );
                                            
        $row['sales'] = '$' . $this->html__sales($row['sales']);
        $row['estimated_monthly_sales'] = '$' . $this->html__sales($row['estimated_monthly_sales']);
        $row['op_date_modified'] = $this->html__datetime_mk($row['op_date_modified']);

        $row = $this->html__row__with_html_spaces($row, array('contact_name'));
        return $row;
    }

    protected function html_page_header() 
    {
        global $sugar_config, $theme;

        $c = file_get_contents($this->path_html('header.html'));
        $c = str_replace("{SITE_URL}", $sugar_config["site_url"], $c);
        $c = str_replace("{THEME_URL}", $sugar_config["site_url"]."/themes/".$theme, $c);
        $c = str_replace("{CHARSET}", $this->get_charset(), $c);
        return $c;
    }

    protected function html_table_header($oFList) 
    {
        $aHead = $oFList->html_header();
        $h = '<table width="' . $aHead['width'] . '" cellpadding="0" cellspacing="0" border="0" class="listView">';
        $this->write($h);
        if ($this->include_header) {
            $this->write($aHead['html']);
        }
        
        return true;
    }

    protected function csv_quote($str) 
    {
        $str = str_replace('&#039;', '\'', $str);
        return '"' . $str . '"';
    }
    

    protected function sql_activities($table, $type) 
    {
        return <<<EO_SQL_ACTIVITIES
SELECT
    x_m.id,
    "{$type}" AS type_of_activity,
    x_m.status AS held_not_held,
    x_m.date_start AS date_time_of_activity,
    NULL AS date_end_of_activity,
    x_m.name AS subject,
    x_m.description AS points, 
    x_m.outcome_c AS outcome,
    x_m.parent_type,
    x_m.parent_id,
    x_u.user_name AS user_login,
    x_m.assigned_user_id
FROM {$table} AS x_m
    LEFT JOIN users AS x_u
        ON x_m.assigned_user_id=x_u.id
WHERE 
    \$FMPREP_SSAR_DATE 
    AND x_m.deleted=0
EO_SQL_ACTIVITIES;
    }

    protected function sql_activities__tasks($table, $type) 
    {
        return <<<EO_SQL_ACTIVITIES
SELECT
    x_m.id,
    "{$type}" AS type_of_activity,
    x_m.status AS held_not_held,
    x_m.date_start AS date_time_of_activity,
    x_m.date_due AS date_end_of_activity,
    x_m.name AS subject,
    x_m.description AS points, 
    x_mc.outcome_c AS outcome,
    x_m.parent_type,
    x_m.parent_id,
    x_u.user_name AS user_login,
    x_m.assigned_user_id
FROM {$table} AS x_m
    LEFT JOIN {$table}_cstm AS x_mc
        ON x_m.id=x_mc.id_c
    LEFT JOIN users AS x_u
        ON x_m.assigned_user_id=x_u.id
WHERE 
    \$FMPREP_SSAR_TASK
    AND x_m.deleted=0
EO_SQL_ACTIVITIES;
    }
    
    
    protected function sql_activities__temporary_table() 
    {
        return <<<EO_Q
CREATE TEMPORARY TABLE x_m (
    id char(36) NOT NULL PRIMARY KEY,
    type_of_activity ENUM ('Meeting', 'Call', 'Task'),
    held_not_held VARCHAR(25),
    date_time_of_activity DATETIME,
    date_end_of_activity DATETIME,
    subject VARCHAR(50),
    points TEXT,
    outcome TEXT,
    parent_type VARCHAR(25),
    parent_id CHAR(36),
    user_login VARCHAR(60),
    assigned_user_id CHAR(36), 

    KEY i_rep_date (date_time_of_activity, type_of_activity, subject),
    KEY i_rep_type (type_of_activity, date_time_of_activity, subject),
    KEY i_rep_parent (parent_id, parent_type)
)
EO_Q;
    }
    
    protected function sql_activities__temporary_table__result() 
    {
        return <<<EO_Q
CREATE TEMPORARY TABLE x_out (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    customer_no VARCHAR(25) , 
    account_name VARCHAR(150), 
    sales_rep_id VARCHAR (6),
    sales_rep_name VARCHAR (60),
    user_login VARCHAR(60),
    type_of_activity ENUM ('Meeting', 'Call', 'Task'), 
    held_not_held VARCHAR(25),
    date_time_of_activity DATETIME,
    date_end_of_activity DATETIME,
    subject VARCHAR(50),
    points TEXT,
    outcome TEXT,
    parent_type VARCHAR(25),
    parent_id CHAR(36),
    parent_name VARCHAR(255),

    product VARCHAR(50),
    sales DOUBLE,
    sales_stage VARCHAR(25),
    additional_opp_info TEXT,
    
    opupdate TEXT,
    date_closed VARCHAR(50),
    probability DOUBLE,
    op_date_modified VARCHAR(50),    

    dev_account_id CHAR(36) , 
    dev_opportunity_id CHAR(36) , 
    dev_activity_id CHAR(36),
    dev_user_id CHAR(36) 
)
EO_Q;
    }

    protected function sql_activities__merge_any_type_to_single_table($parameter_values) 
    {
        $q = $this->sql_activities__temporary_table();
        $this->log();
        $this->log($q);
        $this->db->query($q);

        $q = array( 
            $this->sql_activities('meetings', 'Meeting'), 
            $this->sql_activities('calls', 'Call')            
        );
        
        $q[] = $this->sql_activities__tasks('tasks', 'Task');
        foreach ($parameter_values as $name => $value) {
            $q = str_replace('$'.$name, ''.$value, $q);
        }

        $q = '' 
            . 'INSERT INTO x_m (' 
                . 'id, type_of_activity, held_not_held, date_time_of_activity, date_end_of_activity, '
                . 'subject, points, outcome, parent_type, '
                . 'parent_id, user_login, assigned_user_id'
            . ') ' . implode(' UNION ', $q) ;

        $this->log();
        $this->log($q);
        $rs = $this->db->query($q);
        return $rs;
    }
    
    protected function sql_activities__fill_result_table($q) 
    {
        return <<<EO_Q
INSERT INTO x_out ( 
    customer_no,
    account_name,  
    sales_rep_id, 
    sales_rep_name,
    user_login,
    type_of_activity,  
    held_not_held, 
    date_time_of_activity,
    date_end_of_activity,
    subject, 
    points, 
    outcome, 
    parent_type, 
    parent_id, 
    parent_name, 
    
    product, 
    sales, 
    sales_stage, 
    additional_opp_info, 
    
    opupdate, 
    date_closed, 
    probability, 
    op_date_modified,     
    
    dev_account_id,  
    dev_opportunity_id,  
    dev_activity_id, 
    dev_user_id
) {$q}
EO_Q;
    }

    protected function sql_activities__fill_result_table__load_and_sort($parameter_values)
    {
        $q = <<<EO_Q
SELECT 
    *,
    (sales / 12 ) as estimated_monthly_sales,
    UNIX_TIMESTAMP(date_time_of_activity) AS date_time_of_activity,
    UNIX_TIMESTAMP(date_end_of_activity) AS date_end_of_activity,
    UNIX_TIMESTAMP(date_closed) AS date_closed,
    UNIX_TIMESTAMP(op_date_modified) AS op_date_modified
FROM x_out
WHERE 1=1
\$FMPREP_SSAR_TEXTFILTER
\$FMPREP_SSAR_SORT
EO_Q;
        foreach ($parameter_values as $name => $value) {
            $q = str_replace('$'.$name, ''.$value, $q);
        }
        return $q;
    }

    protected function sql_activities__activity_accout_opportunity() 
    {
        return <<<EO_Q
SELECT 
    x_a.custno_c AS customer_no, 
    x_a.name AS account_name, 
    x_slsm.slsm AS sales_rep_id,
    x_slsm.name AS sales_rep_name,
    x_u.user_name AS user_login,
    x_m.type_of_activity, 
    x_m.held_not_held, 
    x_m.date_time_of_activity,
    x_m.date_end_of_activity,
    x_m.subject,
    x_m.points, 
    x_m.outcome,
    x_m.parent_type,
    x_m.parent_id,
    '' AS parent_name,

    x_o.name AS product, 
    x_o.amount AS sales, 
    x_o.sales_stage, 
    x_o.description AS additional_opp_info,

    x_oc.opupdate_c AS opupdate,
    x_o.date_closed AS date_closed, 
    x_o.probability,
    x_o.date_modified AS op_date_modified,

    x_a.id AS dev_account_id, 
    x_o.id AS dev_opportunity_id, 
    x_m.id AS dev_activity_id,
    x_m.assigned_user_id AS dev_user_id
FROM x_m 
    LEFT JOIN accounts AS x_a 
        ON x_a.id=x_m.parent_id
/*    LEFT JOIN dsls_slsm AS x_slsm*/
    LEFT JOIN dsls_slsm_combined AS x_slsm
        ON x_slsm.slsm=x_a.slsm_c
    LEFT JOIN accounts_opportunities AS x_ao 
        ON (x_ao.account_id=x_a.id AND x_ao.deleted=0) 
    LEFT JOIN opportunities AS x_o 
        ON (x_o.id=x_ao.opportunity_id AND x_o.deleted=0)
    LEFT JOIN opportunities_cstm AS x_oc
        ON x_oc.id_c=x_o.id 
    LEFT JOIN users AS x_u
        ON x_u.id=x_m.assigned_user_id
WHERE x_m.parent_type='Accounts' 
    AND x_m.parent_id IS NOT NULL
    \$FMPREP_SSAR_DEALERTYPE
    \$FMPREP_SSAR_LOC
EO_Q;
    }

    protected function sql_activities__anytable_nullrecord() 
    {
        return <<<EO_Q
SELECT 
    '' AS customer_no, 
    x_m.subject AS account_name, 
    '' AS sales_rep_id,
    '' AS sales_rep_name,
    x_u.user_name AS user_login,
    x_m.type_of_activity, 
    x_m.held_not_held, 
    x_m.date_time_of_activity,
    x_m.date_end_of_activity,
    x_m.subject,
    x_m.points, 
    x_m.outcome,
    x_m.parent_type,
    x_m.parent_id,
    'none' AS parent_name,

    '' AS product, 
    '' AS sales, 
    '' AS sales_stage, 
    '' AS additional_opp_info,

    '' AS opupdate,
    '' AS date_closed, 
    '' AS probability,
    '' AS op_date_modified,

    '' AS dev_account_id, 
    '' AS dev_opportunity_id, 
    x_m.id AS dev_activity_id,
    x_m.assigned_user_id AS dev_user_id
FROM x_m 
    LEFT JOIN users AS x_u
        ON x_u.id=x_m.assigned_user_id
WHERE  
    x_m.parent_id IS NULL
    \$FMPREP_SSAR_ALLACTIVITYFIX
EO_Q;
    }
    
    protected function sql_activities__task_notnullrecord() 
    {
        return <<<EO_Q
SELECT 
    x_a.custno_c AS customer_no, 
    x_a.name AS account_name, 
    x_slsm.slsm AS sales_rep_id,
    x_slsm.name AS sales_rep_name,
    x_u.user_name AS user_login,
    x_m.type_of_activity, 
    x_m.held_not_held, 
    x_m.date_time_of_activity,
    x_m.date_end_of_activity,
    x_m.subject,
    x_m.points, 
    x_m.outcome,
    x_m.parent_type,
    x_m.parent_id,
    x_t.name AS parent_name,

    '' AS product, 
    '' AS sales, 
    '' AS sales_stage, 
    '' AS additional_opp_info,

    '' AS opupdate,
    '' AS date_closed, 
    '' AS probability,
    '' AS op_date_modified,

    x_a.id AS dev_account_id, 
    '' AS dev_opportunity_id, 
    x_m.id AS dev_activity_id,
    x_m.assigned_user_id AS dev_user_id
FROM x_m 
    LEFT JOIN users AS x_u
        ON x_u.id=x_m.assigned_user_id
    LEFT JOIN tasks AS x_t
        ON (x_m.parent_id=x_t.id AND x_m.parent_type='Tasks')
    LEFT JOIN accounts AS x_a
        ON (x_t.parent_id=x_a.id AND x_t.parent_type='Accounts')
    /*LEFT JOIN dsls_slsm AS x_slsm*/
    LEFT JOIN dsls_slsm_combined AS x_slsm
        ON x_slsm.slsm=x_a.slsm_c
WHERE x_m.parent_id IS NOT NULL
    AND x_t.deleted=0
    AND x_a.deleted=0
    \$FMPREP_SSAR_ALLACTIVITYFIX
EO_Q;
    }
    
    protected function sql_activities__case_notnullrecord() 
    { 
        return <<<EO_Q
SELECT 
    x_a.custno_c AS customer_no, 
    x_a.name AS account_name, 
    x_slsm.slsm AS sales_rep_id,
    x_slsm.name AS sales_rep_name,
    x_u.user_name AS user_login,
    x_m.type_of_activity, 
    x_m.held_not_held, 
    x_m.date_time_of_activity,
    x_m.date_end_of_activity,
    x_m.subject,
    x_m.points, 
    x_m.outcome,
    x_m.parent_type,
    x_m.parent_id,
    x_cs.name AS parent_name,

    '' AS product, 
    '' AS sales, 
    '' AS sales_stage, 
    '' AS additional_opp_info,

    '' AS opupdate,
    '' AS date_closed, 
    '' AS probability,
    '' AS op_date_modified,

    x_a.id AS dev_account_id, 
    '' AS dev_opportunity_id, 
    x_m.id AS dev_activity_id,
    x_m.assigned_user_id AS dev_user_id
FROM x_m 
    LEFT JOIN users AS x_u
        ON x_u.id=x_m.assigned_user_id
    LEFT JOIN cases AS x_cs
        ON (x_m.parent_id=x_cs.id AND x_m.parent_type='Cases')
    LEFT JOIN accounts AS x_a
        ON x_cs.account_id=x_a.id
/*    LEFT JOIN dsls_slsm AS x_slsm*/
    LEFT JOIN dsls_slsm_combined AS x_slsm
        ON x_slsm.slsm=x_a.slsm_c
WHERE x_m.parent_id IS NOT NULL
    AND x_cs.deleted=0
    AND x_a.deleted=0
    \$FMPREP_SSAR_ALLACTIVITYFIX
EO_Q;
    }
    
    protected function sql_activities__contact_notnullrecord() 
    { 
        return <<<EO_Q
SELECT 
    x_a.custno_c AS customer_no, 
    x_a.name AS account_name, 
    x_slsm.slsm AS sales_rep_id,
    x_slsm.name AS sales_rep_name,
    x_u.user_name AS user_login,
    x_m.type_of_activity, 
    x_m.held_not_held, 
    x_m.date_time_of_activity,
    x_m.date_end_of_activity,
    x_m.subject,
    x_m.points, 
    x_m.outcome,
    x_m.parent_type,
    x_m.parent_id,
    CONCAT(x_co.first_name, ' ', x_co.last_name) AS parent_name,

    '' AS product, 
    '' AS sales, 
    '' AS sales_stage, 
    '' AS additional_opp_info,

    '' AS opupdate,
    '' AS date_closed, 
    '' AS probability,
    '' AS op_date_modified,

    x_a.id AS dev_account_id, 
    '' AS dev_opportunity_id, 
    x_m.id AS dev_activity_id,
    x_m.assigned_user_id AS dev_user_id
FROM x_m 
    LEFT JOIN users AS x_u
        ON x_u.id=x_m.assigned_user_id
    LEFT JOIN contacts AS x_co
        ON (x_m.parent_id=x_co.id AND x_m.parent_type='Contacts')
    LEFT JOIN accounts_contacts AS x_a_co
        ON (x_a_co.contact_id=x_co.id AND x_a_co.deleted=0)

    LEFT JOIN accounts AS x_a
        ON x_a_co.account_id=x_a.id

/*    LEFT JOIN dsls_slsm AS x_slsm*/
    LEFT JOIN dsls_slsm_combined AS x_slsm
        ON x_slsm.slsm=x_a.slsm_c
WHERE x_m.parent_id IS NOT NULL
    AND x_co.deleted=0
    AND x_a.deleted=0
    \$FMPREP_SSAR_ALLACTIVITYFIX
EO_Q;
    }
    
    protected function sql_activities__opportunity_notnullrecord() 
    { 
        return <<<EO_Q
SELECT 
    x_a.custno_c AS customer_no, 
    x_a.name AS account_name, 
    x_slsm.slsm AS sales_rep_id,
    x_slsm.name AS sales_rep_name,
    x_u.user_name AS user_login,
    x_m.type_of_activity, 
    x_m.held_not_held, 
    x_m.date_time_of_activity,
    x_m.date_end_of_activity,
    x_m.subject,
    x_m.points, 
    x_m.outcome,
    x_m.parent_type,
    x_m.parent_id,
    x_o.name AS parent_name,

    x_o.name AS product, 
    x_o.amount AS sales, 
    x_o.sales_stage, 
    x_o.description AS additional_opp_info,

    x_oc.opupdate_c AS opupdate,
    x_o.date_closed AS date_closed, 
    x_o.probability,
    x_o.date_modified AS op_date_modified,

    x_a.id AS dev_account_id, 
    x_o.id AS dev_opportunity_id,
    x_m.id AS dev_activity_id,
    x_m.assigned_user_id AS dev_user_id
FROM x_m 
    LEFT JOIN users AS x_u
        ON x_u.id=x_m.assigned_user_id
    LEFT JOIN opportunities AS x_o
        ON (x_m.parent_id=x_o.id AND x_m.parent_type='Opportunities')
    LEFT JOIN opportunities_cstm AS x_oc
        ON x_oc.id_c=x_o.id
    LEFT JOIN accounts_opportunities AS x_a_o
        ON (x_a_o.opportunity_id=x_o.id AND x_a_o.deleted=0)

    LEFT JOIN accounts AS x_a
        ON x_a_o.account_id=x_a.id

/*    LEFT JOIN dsls_slsm AS x_slsm*/
    LEFT JOIN dsls_slsm_combined AS x_slsm
        ON x_slsm.slsm=x_a.slsm_c
WHERE x_m.parent_id IS NOT NULL
    AND x_o.deleted=0
    AND x_a.deleted=0
    \$FMPREP_SSAR_ALLACTIVITYFIX
EO_Q;
    }
    
    protected function sql_activities__project_notnullrecord() 
    { 
        return <<<EO_Q
SELECT 
    '' AS customer_no, 
    x_m.subject AS account_name, 
    '' AS sales_rep_id,
    '' AS sales_rep_name,
    x_u.user_name AS user_login,
    x_m.type_of_activity, 
    x_m.held_not_held, 
    x_m.date_time_of_activity,
    x_m.date_end_of_activity,
    x_m.subject,
    x_m.points, 
    x_m.outcome,
    x_m.parent_type,
    x_m.parent_id,
    x_p.name AS parent_name,

    '' AS product, 
    '' AS sales, 
    '' AS sales_stage, 
    '' AS additional_opp_info,

    '' AS opupdate,
    '' AS date_closed, 
    '' AS probability,
    '' AS op_date_modified,

    '' AS dev_account_id, 
    '' AS dev_opportunity_id,
    x_m.id AS dev_activity_id,
    x_m.assigned_user_id AS dev_user_id
FROM x_m 
    LEFT JOIN users AS x_u
        ON x_u.id=x_m.assigned_user_id
    LEFT JOIN project AS x_p
        ON (x_m.parent_id=x_p.id AND x_m.parent_type='Project')
WHERE x_m.parent_id IS NOT NULL
    AND x_p.deleted=0
    \$FMPREP_SSAR_ALLACTIVITYFIX
EO_Q;
    }

    protected function sql_activities__lead_notnullrecord() 
    { 
        return <<<EO_Q
SELECT 
    '' AS customer_no, 
    x_m.subject AS account_name, 
    '' AS sales_rep_id,
    '' AS sales_rep_name,
    x_u.user_name AS user_login,
    x_m.type_of_activity, 
    x_m.held_not_held, 
    x_m.date_time_of_activity,
    x_m.date_end_of_activity,
    x_m.subject,
    x_m.points, 
    x_m.outcome,
    x_m.parent_type,
    x_m.parent_id,
    CONCAT(IF (x_le.account_name, CONCAT(x_le.account_name, ', '), ''), x_le.last_name) AS parent_name,

    '' AS product, 
    '' AS sales, 
    '' AS sales_stage, 
    '' AS additional_opp_info,

    '' AS opupdate,
    '' AS date_closed, 
    '' AS probability,
    '' AS op_date_modified,

    '' AS dev_account_id, 
    '' AS dev_opportunity_id,
    x_m.id AS dev_activity_id,
    x_m.assigned_user_id AS dev_user_id
FROM x_m 
    LEFT JOIN users AS x_u
        ON x_u.id=x_m.assigned_user_id
    LEFT JOIN leads AS x_le
        ON (x_m.parent_id=x_le.id AND x_m.parent_type='Leads')
    LEFT JOIN leads_cstm AS x_lec
        ON x_lec.id_c=x_le.id
WHERE x_m.parent_id IS NOT NULL
    AND x_le.deleted=0
    \$FMPREP_SSAR_ALLACTIVITYFIX
EO_Q;
    }

    protected function sql_contacts($accounts) 
    {
        $accounts = array_merge(array(-1), $accounts);

        foreach($accounts as $k=>$v) {
            $accounts[$k] = '\'' . $v . '\'';
        }
        $accounts = implode(', ', $accounts);

        $q = <<<EO_SQL_CONTACTS
SELECT 
    x_a.id AS account_id, 
    x_a.name AS dev_a_name, 
    x_c.id AS contact_id, 
    x_c.*
FROM accounts AS x_a 
    INNER JOIN accounts_contacts AS x_a_c 
        ON x_a.id=x_a_c.account_id 
    INNER JOIN contacts AS x_c 
        ON x_c.id = x_a_c.contact_id
WHERE x_a.id IN ({$accounts})
EO_SQL_CONTACTS;
        return $q;
    }


    
    protected function summary_analysys__activity_by_status__init($activity_type) 
    {
        global $app_list_strings;
        $out_a  = array(
                        'meetings' => 'meeting_status_dom',
                        'calls' => 'call_status_dom',
                        'tasks' => 'task_status_dom'
                    );

        $dom = $out_a[$activity_type];
        
        $out = array();
        foreach($app_list_strings[$dom] as $stat_k=>$stat_desc) {
            $out[$stat_k]   = array(
                                        'desc' => $stat_desc, 
                                        'total' => 0,
                                        'uniq_a_a' => array()
                                    );
        }                    

        return $out;
    }
    
    protected function summary_analysys($rows,$closing_date_from, $closing_date_to, $sales_stage)
    {
         if ($sales_stage == '') {
            $sales_stage[] = 'Stage1';
            $sales_stage[] = 'Stage 2';
            $sales_stage[] = 'Stage 3';
            $sales_stage[] = 'Stage 4 ';
            $sales_stage[] = 'Stage 5';
            $sales_stage[] = 'Closed Won';
            $sales_stage[] = 'Closed Lost';
       }

        $out_a  = array(
                        'meetings' => array(),
                        'calls' => array(),
                        'tasks' => array()
                    );
        foreach($out_a as $type=>$v) {
            $out_a[$type] = $this->summary_analysys__activity_by_status__init($type);
        }

        $out_o = array(
            'total_o'=>0, 
            'win_p' => 'N/A', 
            'win2_p' => 'N/A', 
            'store' => array('Closed Won' => array(), 'Closed Lost' => array(), 'other' => array())
        );
        $out = array(
            'activity' => $out_a, 
            'opportunity' => $out_o
        );

        $types = array('meeting' => 'meetings', 'call' => 'calls', 'task' => 'tasks');
        foreach($rows as $v) 
        {
            $type_of_activity = strtolower($v['type_of_activity']);
            $type_of_activity = $types[$type_of_activity];

            $held_not_held = $v['held_not_held'];
            $activity_id = $v['dev_activity_id'];

            $out['activity'][$type_of_activity][$held_not_held]['uniq_a_a'][$activity_id][] = true;
            
            $opp_id = $v['dev_opportunity_id'];
            $opp_date_closed = $v['date_closed'];
            $opp_sales_stage = $v['sales_stage'];

            if ($opp_id && $opp_date_closed <= $closing_date_to && $opp_date_closed >= $closing_date_from && in_array($opp_sales_stage,$sales_stage)) {
            //if ($opp_id) {
                switch($v['sales_stage']) {
                    case 'Closed Won':
                    case 'Closed Lost':
                        $stage = $v['sales_stage'];
                        $out['opportunity']['store'][$stage][$opp_id][] = true;
                        break;

                    default:
                        $out['opportunity']['store']['other'][$opp_id][] = true;
                }
            }
        }

        foreach($out['activity'] as $type=>$v) {
            foreach($v as $status=>$x) {
                $out['activity'][$type][$status]['total'] = count($x['uniq_a_a']);
                unset($out['activity'][$type][$status]['uniq_a_a']);
            }
        }

        $closed_won = count($out['opportunity']['store']['Closed Won']);
        $closed_lost = count($out['opportunity']['store']['Closed Lost']);
        $total_opp = $closed_won + $closed_lost + count($out['opportunity']['store']['other']);
        
        $out['opportunity']['total_o'] = $total_opp;
        if ($total_opp > 0) {
            $win_p = ($closed_won / $total_opp) * 100 ;
            $win_p = sprintf('%0.2f', $win_p);
            $win_p .= '%, (' . $closed_won . ' / ' . $total_opp . ') * 100';
            $out['opportunity']['win_p'] = $win_p;

            if ($closed_won > 0) {
                $win2_p = $closed_won / ($closed_won + $closed_lost) * 100;
                $win2_p = sprintf('%0.2f', $win2_p);
                $win2_p .= '%, (' . $closed_won  . ' / (' . $closed_won . ' + ' . $closed_lost . ') ) * 100';
                $out['opportunity']['win2_p'] = $win2_p;
            }
        }

        unset($out['opportunity']['store']);
        return $out;
    }

    protected function rows_extract($rs) 
    {
        $rows = array();
        while($row = $this->db->fetchByAssoc($rs)) {
            $row['sales_rep_name'] = $row['sales_rep_name'];
//            $row['sales_rep_last_name'] = $row['sales_rep_first_name'] . ' ' . $row['sales_rep_last_name'];
//            unset($row['sales_rep_first_name']);
            $rows[] = $row;
        }
        return $rows;        
    }
    
    protected function rows_group($rows)
    {
        $out = array();
        foreach($rows as $row) {
            $key = $row['dev_activity_id'];
            $out[$key][] = $row;        
        }

        return $out;
    }

    protected function rows_group_with_opportunity_options($rows, $closing_date_from, $closing_date_to, $sales_stage)
    {
        if ($sales_stage == '') {
            $sales_stage[] = 'Stage1';
            $sales_stage[] = 'Stage 2';
            $sales_stage[] = 'Stage 3';
            $sales_stage[] = 'Stage 4 ';
            $sales_stage[] = 'Stage 5';
            $sales_stage[] = 'Closed Won';
            $sales_stage[] = 'Closed Lost';
       }
        $out = array();
        foreach($rows as $row) {
            $key = $row['dev_activity_id'];
            $out[$key][] = $row;
        }
        $filter_out = array();
        foreach($out as $activity_id => $op_array){
            foreach($op_array as $num => $item){
                if( $item['date_closed'] <= $closing_date_to && $item['date_closed'] >= $closing_date_from && in_array($item['sales_stage'],$sales_stage)){
                 $filter_out[$activity_id][] = $item;   
                }
            }
                   if(count($filter_out[$activity_id]) == 0){
                        $no_opp_item = array();
                  foreach($op_array[0] as $no_opp_key => $no_opp_val){
                       $no_opp_item[$no_opp_key] = $no_opp_val;
                   if($no_opp_key == 'product' || $no_opp_key == 'estimated_monthly_sales' || $no_opp_key == 'sales' || $no_opp_key == 'sales_stage'  || $no_opp_key == 'additional_opp_info' || $no_opp_key == 'opupdate' || $no_opp_key == 'date_closed' || $no_opp_key == 'probability' || $no_opp_key == 'op_date_modified' || $no_opp_key =='dev_opportunity_id'){
                       $no_opp_item[$no_opp_key] = '';
                   }
                  }

         $filter_out[$activity_id][] =$no_opp_item;
           }

        }

        
        

        return $filter_out;
    }

    protected function rows__upate_with_contacts($rows) 
    {
        $accounts_keys = array(); 
        foreach($rows as $v) {
            $account_id = $v['dev_account_id'];
            $accounts_keys[$account_id] = true;
        }

        $keys = array_keys($accounts_keys); 
        $q = $this->sql_contacts($keys);

        $out = array();
        
        $rs = $this->db->query($q);
        while($row = $this->db->fetchByAssoc($rs)) {
            $account_id = $row['account_id'];
            $out[$account_id] = $row;
        }
        
        foreach($rows as $k=>$v) {
            $rows[$k]['contact_id'] = 0;
            $rows[$k]['contact_name'] = '';
            $rows[$k]['contact_address'] = '';
            $rows[$k]['contact_phone'] = '';
            
            $account_id = $v['dev_account_id'];
            if (isset($out[$account_id])) {
                $cont = $out[$account_id];
                $rows[$k]['contact_id'] = $cont['contact_id'];

                $cont['first_name'] = trim($cont['first_name']);
                $cont['last_name'] = trim($cont['last_name']);
                
                $name = array();
                if ($cont['first_name']) {
                    $name[] = $cont['first_name'];
                }
                if ($cont['last_name']) {
                    $name[] = $cont['last_name'];
                }                    
                

                $rows[$k]['contact_name'] =  implode(' ', $name);
                
                $addr = array(
                    trim($cont['primary_address_street']),
                    trim($cont['primary_address_city']),
                    trim($cont['primary_address_state']),
                    trim($cont['primary_address_postalcode']),
                    trim($cont['primary_address_country'])
                );
                $rows[$k]['contact_address'] = implode(' ', $addr);
                
                $rows[$k]['contact_phone'] = trim($cont['phone_work']);
            }
        }

        return $rows;
    }
    

    protected function execute__handle_db_error() 
    {
        if ($this->db->dbType == 'mysql') {
            $this->report_output .= 'MySQL error ' . $this->db->database->errno . ': ' . $this->db->database->error .'<br />'
                . $this->db->lastsql . '<br />';
        } else {
            $this->report_output .= $this->database->getMessage()."<br/>";
        }
        
        return false;
    }

    protected function write($msg) 
    {
        if (!$this->fp) {
            $this->fp = fopen($this->report_result, 'w');
        }
        
        return fwrite($this->fp, $msg);
    }
    
    protected function log($msg=null, $color='blue') 
    {
        
        if (!$msg) {
            echo '<p>&nbsp;</p>';
            return ;
        }
        
        $this->log_counter++;
        echo '<p id="log_' . $this->log_counter . '" style="display: none; color: ' . $color . '">' . $msg . '</p>';
	//echo '<p id="log_' . $this->log_counter . '" style="color: ' . $color . '">' . $msg . '</p>';
    }

    protected function url($url) 
    {
        global $sugar_config;
        return $sugar_config['site_url'] . '/'. $url;
    }


    protected function execute__init_result_varialbes($format) 
    {
        $date = date("ymd_His");

        if ($format == 'CSV') {
            $this->report_result_type = "FILE";
            $this->report_result_name = $date."_".$this->name.".csv";
            $this->report_result_name = strtolower(join("_", explode(" ", $this->report_result_name)));
            $this->report_result = $this->archive_dir."/".$this->report_result_name;
        } else if ($format == 'HTML' || $format == 'SIMPLEHTML') {
            $this->report_result_type = "FILE";
            $this->report_result_name = $date."_".$this->name.".html";
            $this->report_result_name = strtolower(join("_", explode(" ", $this->report_result_name)));
            $this->report_result = $this->archive_dir."/".$this->report_result_name;
        } else {
            $this->report_result_type = "INLINE";
        }    
    }
    
    protected function execute_csv(&$rs, &$parameter_values) 
    {
        global $app_list_strings;

        $oFList = new fmp_Param_FieldList_Activity();
        if (!$a_field_list = $oFList->r_fields()) {
            $h = 'You need to select one or more fields';
            $this->write($h);
            return $result = true;
        }
        

        $h = array();
        foreach (fmp_Param_FieldList_Activity::$F as $k=>$col) {
            if (!isset($a_field_list[$k])) {
                continue;
            }
            
            if ($k == 'sales_rep_name') {
                $namecol = 'sales_rep_id';
                $col2 = fmp_Param_FieldList_Activity::$F[$namecol]; 
                $h[] = str_replace('<br>', ' ', $col2['name']);
                
                $h[] = str_replace('<br>', ' ', $col['name']);
                
                $namecol = 'user_login';
                $col2 = fmp_Param_FieldList_Activity::$F[$namecol]; 
                $h[] = str_replace('<br>', ' ', $col2['name']);

                continue;                
            }

            if ($k == 'outcome') {
                $h[] = str_replace('<br>', ' ', $col['name']);
                
                $namecol = 'parent_type';
                $col2 = fmp_Param_FieldList_Activity::$F[$namecol]; 
                $h[] = str_replace('<br>', ' ', $col2['name']);
                
                $namecol = 'parent_name';
                $col2 = fmp_Param_FieldList_Activity::$F[$namecol]; 
                $h[] = str_replace('<br>', ' ', $col2['name']);

                continue;
            }
            
            if ($k == 'contact_name') {
                $h[] = str_replace('<br>', ' ', $col['name']);

                $namecol = 'contact_address';
                $col2 = fmp_Param_FieldList_Activity::$F[$namecol]; 
                $h[] = str_replace('<br>', ' ', $col2['name']);
                
                $namecol = 'contact_phone';
                $col2 = fmp_Param_FieldList_Activity::$F[$namecol]; 
                $h[] = str_replace('<br>', ' ', $col2['name']);

                continue; 
            }

            $h[] = str_replace('<br>', ' ', $col['name']);
        }
        $h = implode($this->col_delim, $h) . $this->row_delim;
        $this->write($h);

        $q = $this->sql_activities__fill_result_table__load_and_sort($parameter_values);
        $this->log();
        $this->log($q);
        $rs = $this->db->query($q);
        if (!$rs) {
            $this->execute__handle_db_error(); 
            return false;
        }
        $rows = $this->rows_extract($rs);
       
        $rows = $this->rows__upate_with_contacts($rows);

        $rows = $this->rows_group_with_opportunity_options($rows,$parameter_values['FMPREP_SSAR_CLOSING_DATE']['closing_date_from'],$parameter_values['FMPREP_SSAR_CLOSING_DATE']['closing_date_to'],$parameter_values['FMPREP_SSAR_SALESSTAGE']);
       
        if (!count($rows)) {
            return true;
        }

        //foreach($rows as $row) {
        foreach($rows as $key => $value) {
            $row = $value[0];
            $row['sales_stage'] = $this->html__sales_stage__val($row['sales_stage']);
            $row['date_time_of_activity'] = $this->html__datetime_mk($row['date_time_of_activity']);
            $row['date_end_of_activity'] = $this->html__datetime_mk($row['date_end_of_activity']);
            $row['date_closed'] = $this->html__date_mk($row['date_closed']);
            $row['held_not_held']    = $this->html__status(
                                                    $row['type_of_activity'], 
                                                    $row['held_not_held'], 
                                                    $app_list_strings
                                                );
                                                
            $row['op_date_modified'] = $this->html__datetime_mk($row['op_date_modified']);

            if (empty ($row['sales'])) {
                $row['sales'] = 0;
                $row['estimated_monthly_sales'] = 0;
            }
//            pr($row);die();
            $h_rows = array(
                'customer_no' => $row['customer_no'],
                'account_name' => $this->csv_quote($row['account_name']),
                'sales_rep_id' => $row['sales_rep_id'],
//                'sales_rep_last_name' => $this->csv_quote($row['sales_rep_last_name']),
                'sales_rep_name' => $this->csv_quote($row['sales_rep_name']),
                'user_login' => $row['user_login'],
                'type_of_activity' => $row['type_of_activity'],
                'held_not_held' => $row['held_not_held'],
                'date_time_of_activity' => $row['date_time_of_activity'],
                'subject' => $this->csv_quote($row['subject']),
                'contact_name' => $this->csv_quote($row['contact_name']),
                'contact_address' => $this->csv_quote($row['contact_address']),
                'contact_phone' => $row['contact_phone'],
                'points' => $this->csv_quote($row['points']),
                'outcome' => $this->csv_quote($row['outcome']),
                'parent_type' => $this->csv_quote($row['parent_type']),
                'parent_name' => $this->csv_quote($row['parent_name']),
                'product' => $this->csv_quote($row['product']),
                'sales' => $row['sales'],
                'estimated_monthly_sales' => $row['estimated_monthly_sales'],
                'sales_stage' => $row['sales_stage'],
                'additional_opp_info' => $this->csv_quote($row['additional_opp_info']),
                'opupdate' => $this->csv_quote($row['opupdate']),
                'date_closed' => $row['date_closed'],
                'probability' => $row['probability'],
                'op_date_modified' => $row['op_date_modified'] 
            );
            
            $h = array();
            
            foreach($h_rows as $col_id=>$v) {
                if (!isset($a_field_list[$col_id])) {
                    continue;
                }

                if ($col_id == 'sales_rep_name') {
                    $h[] = $h_rows['sales_rep_id']; 
//                    $h[] = $h_rows['sales_rep_last_name'];
                    $h[] = $h_rows['sales_rep_name'];
                    $h[] = $h_rows['user_login'];
                    continue;                
                }

                if ($col_id == 'outcome') {
                    $h[] = $h_rows['outcome'];
                    $h[] = $h_rows['parent_type'];
                    $h[] = $h_rows['parent_name'];
                    continue;
                }

                if ($col_id == 'contact_name') {
                    $h[] = $h_rows['contact_name'];
                    $h[] = $h_rows['contact_address'];
                    $h[] = $h_rows['contact_phone'];
                    continue;
                }

                $h[] = $v;
            }
            
            $h = implode($this->col_delim, $h) . $this->row_delim;
            $this->write($h);
        }

        return true;
    }

    protected function execute_html(&$rs, &$parameter_values) 
    {

        
       
        $h = $this->html_page_header();
        $this->write($h);

        $oFList = new fmp_Param_FieldList_Activity();
        $a_field_list = $oFList->r_fields();
       
        
       
        if (!$a_field_list) {
            $h = '<p style="text-align: left;">You need to select one or more fields</p>';
            $this->write($h);
            return ;
        }
        
        $rh = $this->html_table_header($oFList);
       
        if (!$rh) {
            return true;
        } 

        $q = $this->sql_activities__fill_result_table__load_and_sort($parameter_values);
        
        $this->log();
        $this->log($q);
        $rs = $this->db->query($q);
        if (!$rs) {
            $this->execute__handle_db_error(); 
            return false;
        }

        
        $rows = $this->rows_extract($rs);
        
        $sum = $this->summary_analysys($rows,$parameter_values['FMPREP_SSAR_CLOSING_DATE']['closing_date_from'],$parameter_values['FMPREP_SSAR_CLOSING_DATE']['closing_date_to'],$parameter_values['FMPREP_SSAR_SALESSTAGE']);

        $rows = $this->rows__upate_with_contacts($rows);
       
       
        //$rows = $this->rows_group($rows);
        $rows = $this->rows_group_with_opportunity_options($rows,$parameter_values['FMPREP_SSAR_CLOSING_DATE']['closing_date_from'],$parameter_values['FMPREP_SSAR_CLOSING_DATE']['closing_date_to'],$parameter_values['FMPREP_SSAR_SALESSTAGE']);
         
        
        
        if (!count($rows)) {
            $h = $this->html__not_found();
            $this->write($h);

            $this->write('</table></html>');
            $h = $this->html__total_analysis($sum);
            $this->write($h);
            return true;
        }

        $ext = '1';
        foreach ($rows as $vv) {
            $h = '';

            $class = 'oddListRowS1';
            if ($ext == '2') {
                $class = 'evenListRowS1';
            }

            foreach($vv as $k=>$v) {
                $v = $this->html__row__preprocess($v);
                $h .= $this->html_row($v, $class, $a_field_list, $duplicse_indicator = $k);
            }
            

            if ($ext == '1') {
                $ext = '2';
            } else {
                $ext = '1';
            }

            $this->write($h);
        }

        $this->write('</table></html>');
        
        $h = $this->html__total_analysis($sum);
        $this->write($h);

        return true;
    }
    
    function execute($format = 'CSV', $parameter_values = array()) 
    {
        global $current_user, $app_list_strings;
        $this->execute__init_result_varialbes($format);

        $parameter_values['FMPREP_SSAR_TASK'] = $parameter_values['FMPREP_SSAR_DATE']['task'];
        $parameter_values['FMPREP_SSAR_DATE'] = $parameter_values['FMPREP_SSAR_DATE']['meet-call'];

        //pr($parameter_values);

        $rs = $this->sql_activities__merge_any_type_to_single_table($parameter_values);

        if (!$rs) {
            $this->execute__handle_db_error();
            return false;
        }

        $q = $this->sql_activities__temporary_table__result();
        
        $this->log();
        $this->log($q);
        $rs = $this->db->query($q);
        if (!$rs) {
            $this->execute__handle_db_error();
            return false;
        }

        $q = array();
        $q['q1_act_acc_o'] = $this->sql_activities__activity_accout_opportunity();
        $q['q2_any_nullrec'] = $this->sql_activities__anytable_nullrecord();
        $q['q3_task_notnullrec'] = $this->sql_activities__task_notnullrecord();
        $q['q4_case_notnullrec'] = $this->sql_activities__case_notnullrecord();
        $q['q5_contact_notnullrec'] = $this->sql_activities__contact_notnullrecord();
        $q['q6_opportunity_notnullrec'] = $this->sql_activities__opportunity_notnullrecord();
        $q['q6_project_notnullrec'] = $this->sql_activities__project_notnullrecord();
        $q['q7_lead_notnullrec'] = $this->sql_activities__lead_notnullrecord();
        
        foreach($q as $k=>$sql) {
            foreach ($parameter_values as $name => $value) {
                $sql = str_replace('$'.$name, ''.$value, $sql);
            }

            $sql = str_replace('$SUGAR_USER_ID', $current_user->id, $sql);
            $sql = str_replace('$SUGAR_USER_NAME', $current_user->user_name, $sql);
            $sql = str_replace('$SUGAR_SESSION_ID', $_REQUEST['PHPSESSID'], $sql);

            $this->log();
            $this->log($k, 'green');
            $this->log($sql);
            
            $sql = $this->sql_activities__fill_result_table($sql);
            $this->log($k . ' (final query)', 'red');
            $this->log($sql);

            $rs = $this->db->query($sql);
            if (!$rs) {
                $this->execute__handle_db_error();
                return false;
            }
        }

        if ($format == 'CSV') {
            return $this->execute_csv($rs, $parameter_values);
        }
        return $this->execute_html($rs, $parameter_values);
    }
}
