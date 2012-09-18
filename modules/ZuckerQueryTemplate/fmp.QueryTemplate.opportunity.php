<?php
require_once 'QueryTemplate.php';

class QueryTemplate_FMP_Opportunity extends QueryTemplate {
    const FMPCO_PATH_HTML = 'modules/ZuckerQueryTemplate/html/';

    public static $FMPCO_TPL = 'sales_summary/';

    public function retrieve($id = NULL, $encode=false) 
    {
        $o = parent::retrieve($id, $encode);

        if (!isset($o->id)) {
            return ;
        }

        include 'fmp.config.php';
        if ($o->id != $FMPCO_REP_ID_SOR) {
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
            'FMPREP_SSAR_DATE' => 0,
            'FMPREP_SSAR_DEALERTYPE' => 1,
            'FMPREP_SSAR_LOC' => 2,
            'FMPREP_SSAR_SLSM' => 3,
            'FMPREP_OPP_SALESSTAGE' => 4,
            'FMPREP_SSAR_SORT' => 5,
            'FMPREP_SSAR_FIELDLIST' => 6,
            'FMPREP_SSAR_WAREHOUSE' => 7,
            'FMPREP_SSAR_TEXTFILTER'  => 8,
            'FMPREP_SSAR_SHOWACTIVITIES' => 9
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
    
    protected function html__date($format, $date) 
    {
        if ($date) {
            return date($format, $date);
        }
        return ;
    }

    protected function html__row__with_html_spaces($row) 
    {
        foreach($row as $k=>$v) {
            if (is_array($row[$k])) {
                continue;
            }
            if (!trim($row[$k])) {
                $row[$k] = '&nbsp;';
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

    protected function &html__total_analysis(&$summary_a) 
    {
        global $app_list_strings;
    	$Active = array(
                        'Stage1'=>array(),
                        'Stage 2'=>array(),
                        'Stage 3'=>array(),
                        'Stage 4 '=>array(),
                        'Stage 5'=>array()
                      );
        $NonActive = array('Closed Won'=>array(),'Closed Lost'=>array());
        
        $res = "
        <table  cellpadding='0' cellspacing='0' border='0' class='listView' style='margin-top:20px'>
      <tr>
      <th class='listViewThS1 bottom' width='150'>Active Sales Stages </th>
      <th class='listViewThS1 bottom' width='100'>Total</th>
      <th class='listViewThS1 bottom' width='100'>Total Dollars</th>
      <th class='listViewThS1 bottom' width='150'>% of Active Total Opp</th>
      <th class='listViewThS1 bottom' width='150'>% of Active Total Opp Dollars</th>
      </tr>";
        
        foreach($Active as $Key => $Item)
        {
            $res .= ''
                . '<tr>'
                    . '<td>&nbsp;' . $app_list_strings['sales_stage_dom'][$Key] . ':</td>'
		            .'<td>&nbsp;' . self::$FMPCO_ANALISYS_ARRAY['Active'][$Key]['Num'] . '</td>
		      <td>&nbsp;$' . number_format(self::$FMPCO_ANALISYS_ARRAY['Active'][$Key]['Amount'], 0, '.', ',') . '</td>
		      <td>&nbsp;' . self::$FMPCO_ANALISYS_ARRAY['Active'][$Key]['pNum'] . '</td>
		      <td>&nbsp;' . self::$FMPCO_ANALISYS_ARRAY['Active'][$Key]['pAmount'] . '</td>
		      </tr>';        	
        }

        $res .= '<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
                    $res .= ''
                . '<tr>'
                    . '<td>&nbsp;Active Total Opp:</td>'
                    .'<td>&nbsp;' . self::$FMPCO_ANALISYS_ARRAY['TotalCountActive'] . '</td>
              <td>&nbsp;$' . number_format(self::$FMPCO_ANALISYS_ARRAY['TotalSumActive'], 0, '.', ',') . '</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              </tr>'; 
                    
                    
      $res .= "</table>";
      
      $res .=  "<table cellpadding='0' cellspacing='0' border='0' class='listView' style='margin-top:20px'>
      <tr>
      <th class='listViewThS1 bottom' width='150'>Closed Sales Stages </th>   
      <th class='listViewThS1 bottom' width='100'>Total</th>
      <th class='listViewThS1 bottom' width='100'>Total Dollars</th>
      <th class='listViewThS1 bottom' width='150'>% Closed/Total Opp</th>
      <th class='listViewThS1 bottom' width='150'>%Closed/Total</th>
      </tr>";
        
        foreach($NonActive as $Key => $Item)
        {
            $Num  = self::$FMPCO_ANALISYS_ARRAY['NonActive'][$Key]['Num'];
            $TCA  = self::$FMPCO_ANALISYS_ARRAY['TotalCountActive'];
            $TCC  = self::$FMPCO_ANALISYS_ARRAY['TotalCountFinished'];
            $res .= ''
                . '<tr>'
                    . '<td>&nbsp;' . $app_list_strings['sales_stage_dom'][$Key] . '</td>'
                    .'<td>&nbsp;' . self::$FMPCO_ANALISYS_ARRAY['NonActive'][$Key]['Num'] . '</td>
              <td>&nbsp;' . number_format(self::$FMPCO_ANALISYS_ARRAY['NonActive'][$Key]['Amount'], 0, '.', ',') . '</td>
              <td>&nbsp;' . round( 100*$Num / ($TCA + $TCC), 2 ) . '%</td>     
              <td>&nbsp;' . round( (100*$Num / $TCC), 2 ) . '%</td>
              </tr>';
        } 
        
        $res .= '<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';        
        $res .= ''
                . '<tr>'
                    . '<td>Closed Total Opp: </td>'
                    .'<td>&nbsp;' . self::$FMPCO_ANALISYS_ARRAY['TotalCountFinished'] . '</td>
              <td>&nbsp;' . number_format(self::$FMPCO_ANALISYS_ARRAY['TotalSumFinished'], 0, '.', ',') . '</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              </tr>';
      $res .= "</table>";
      
            $res .=  "<table style='margin-top:20px' cellpadding='0' cellspacing='0' border='0' class='listView'>
      <tr>
      <th class='listViewThS1 bottom' width='150'>&nbsp;</th>   
      <th class='listViewThS1 bottom' width='100'>Total</th>
      <th class='listViewThS1 bottom' width='100'>Total Dollars</th>
      </tr>";
            
            $res .= ''
                . '
                <tr>'
                    . '<td>Total Opportunities: </td>'
                    .'<td>&nbsp;' . (self::$FMPCO_ANALISYS_ARRAY['TotalCountActive'] + self::$FMPCO_ANALISYS_ARRAY['TotalCountFinished']) . '</td>
              <td>&nbsp;$' . number_format(self::$FMPCO_ANALISYS_ARRAY['TotalSumActive'] + self::$FMPCO_ANALISYS_ARRAY['TotalSumFinished'], 0, '.', ',') . '</td>
              
              
              </tr>';
      
      $res .= "</table>";
      
      return $res;
    }

    protected function html__not_found() 
    {
        return '<tr><td colspan="20" align="left" height="20">&nbsp;&nbsp;Not found</td></tr>';
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

    protected function html__sales($val) 
    {
        if (!$val) {
            return 0;
        }

        return number_format($val, 0, '.', ',');
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

        return $timedate->to_display_date($date, $dst = false );
    } 

    protected function csv_quote($str) 
    {
//        echo '<p style="color: blue;">[' . $str . ']</p>';
        $str = str_replace('&#039;', '\'', $str);
        $str = str_replace("\r", 'x', $str);
        $str = str_replace("\n", 'x', $str);
//        echo '<p style="color: red;">[' . $str . ']</p>';

        return '"' . $str . '"';
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

    protected function &rows_extract($rs) 
    {
        $o = new fmp_Param_Showactivities();
        $showactivities = $o->r_showactivities();
        $rows = array();
        while($row = $this->db->fetchByAssoc($rs)) {
            $opportunity_id = $row['dev_opportunity_id'];
            if (!isset($rows[$opportunity_id])) {
                if ($showactivities) {
                    $q = fmp_Param_Showactivities::q($opportunity_id);
                    $rs2 = $this->db->query($q);

                    while($a = $this->db->fetchByAssoc($rs2)) {
                        $activity_id = $a['dev_activity_id'];
                        $row['activities'][$activity_id] = $a;
                    }
                }

                $row['product_line'] = '';
                $rows[$opportunity_id] = $row;
                $rows[$opportunity_id]['product_line'] .= pline_desc($row['pline_id'], $row['pcat_id'], $row['pcode_id']);
                continue;
            }
            $rows[$opportunity_id]['product_line'] .= "\n" . pline_desc($row['pline_id'], $row['pcat_id'], $row['pcode_id']);
        }

        

        return $rows;        
    }

    protected function &rows__upate_with_contacts(&$rows) 
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

    function execute($format = 'CSV', $parameter_values = array()) {
        global $sugar_config, $current_user, $theme, $app_list_strings;

//        echo '<pre>';
//        print_r($parameter_values);
//        echo '</pre>';
//        die();
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

        $seed = new QueryTemplate();
        $bean = $seed->retrieve($this->id, false);

        $sql = $bean->sql1;

        $sql = <<<EOSQL
SELECT
    x_a.custno_c AS customer_no, 
    x_a.name AS account_name, 
    x_slsm.slsm AS sales_rep_id,
    x_u.user_name AS user_login,
    x_slsm.name AS sales_rep_last_name,
    x_o.name AS product, 
    x_o.amount AS sales,
    (x_o.amount/12) AS estimated_monthly_sales,
    x_o.sales_stage, 
    x_o.description AS 'additional_opp_info', 
    x_oc.opupdate_c AS opupdate,
    x_o.gp_perc * x_o.amount AS gp_dollar,
    x_o.gp_perc,

    x_oc.mtd_gp_c AS mtd_gp,
    x_oc.mtd_gp_percent_c AS mtd_gp_percent,
    x_oc.mtd_sales_c AS mtd_sales,

    x_oc.ytd_gp_c AS ytd_gp,
    x_oc.ytd_gp_percent_c AS ytd_gp_percent,
    x_oc.ytd_sales_c AS ytd_sales,

    x_oc.previousavg_gp_c AS prev12mo_gp, 
    x_oc.previousavg_gp_percent_c AS prev12mo_gp_percent, 
    x_oc.previousavg_sales_c AS prev12mo_sales,

    x_oc.rolling_gp_c AS rolling_gp,
    x_oc.rolling_gp_percent_c AS rolling_gp_percent,
    x_oc.rolling_sales_c AS rolling_sales,

    x_o_pline.pline_id,
    x_o_pline.pcat_id,
    x_o_pline.pcode_id,
    '' AS product_line,
    UNIX_TIMESTAMP(x_o.date_closed) AS date_closed, 
    x_o.probability,
    UNIX_TIMESTAMP(x_o.date_modified) AS op_date_modified,
    x_a.id AS dev_account_id, 
    x_o.id AS dev_opportunity_id
FROM accounts AS x_a 
    LEFT JOIN dsls_slsm_combined AS x_slsm
        ON x_slsm.slsm=x_a.slsm_c

    INNER JOIN accounts_opportunities AS x_ao 
        ON x_ao.account_id=x_a.id 
    INNER JOIN opportunities AS x_o 
        ON x_o.id=x_ao.opportunity_id
    LEFT JOIN opportunities_cstm AS x_oc
        ON x_oc.id_c=x_o.id

    LEFT JOIN users AS x_u
        ON x_u.id=x_o.assigned_user_id
        
    LEFT JOIN opportunities_product_line AS x_o_pline
        ON x_o_pline.opportunity_id = x_o.id

WHERE 
    \$FMPREP_SSAR_DATE
    AND x_a.deleted=0 
    AND x_o.deleted=0
    AND x_ao.deleted=0
    \$FMPREP_SSAR_DEALERTYPE
    \$FMPREP_OPP_SALESSTAGE
    \$FMPREP_SSAR_LOC
    \$FMPREP_SSAR_TEXTFILTER
    \$FMPREP_SSAR_WAREHOUSE

\$FMPREP_SSAR_SORT
EOSQL;

        foreach ($parameter_values as $name => $value) {
            $sql = str_replace('$'.$name, ''.$value, $sql);
        }

        $sql = str_replace('$SUGAR_USER_ID', $current_user->id, $sql);
        $sql = str_replace('$SUGAR_USER_NAME', $current_user->user_name, $sql);
        $sql = str_replace('$SUGAR_SESSION_ID', $_REQUEST['PHPSESSID'], $sql);

        echo '<p style="display: none;" class="fmp-sql--report">' . $sql . '</p>';

        $this->report_output .= "Query: ".$sql."<br/>";

        $rs =& $this->db->query($sql, false, "Error executing query: ");
        if (!$rs) {
            if ($this->db->dbType == "mysql") {
                $this->report_output .= "MySQL error ".mysql_errno().": ".mysql_error()."<br/>";
            } else {
                $this->report_output .= $this->database->getMessage()."<br/>";
            }
            return $result = false;
        }

        $rows_found =  $this->db->getRowCount($rs);
        $this->report_output .= "Found ".$rows_found."<br/>";
        $this->report_output .= "Writing to ".$this->report_result."<br/>";

        $fields = $this->db->getFieldsArray($rs);

        if ($format == "CSV") {
            $f = fopen($this->report_result, "w");
            
            $oFList = new fmp_Param_FieldList_Opportunity();
            if (!$a_field_list = $oFList->r_fields()) {
                fwrite($f, 'You need to select one or more fields');
                return $result = true;
            }

            $h = array();
            foreach (fmp_Param_FieldList_Opportunity::$F as $k=>$col) {
                if (!isset($a_field_list[$k])) {
                    continue;
                }
                if ($k == 'sales_rep_last_name') {
                    $namecol = 'sales_rep_id';
                    $col2 = fmp_Param_FieldList_Opportunity::$F[$namecol];
                    $h[] = str_replace('<br>', ' ', $col2['name']);

                    $h[] = str_replace('<br>', ' ', $col['name']);

                    $namecol = 'user_login';
                    $col2 = fmp_Param_FieldList_Opportunity::$F[$namecol];
                    $h[] = str_replace('<br>', ' ', $col2['name']);

                    continue;                
                }

                if ($k == 'contact_name') {
                    $h[] = str_replace('<br>', ' ', $col['name']);

                    $namecol = 'contact_address';
                    $col2 = fmp_Param_FieldList_Opportunity::$F[$namecol];
                    $h[] = str_replace('<br>', ' ', $col2['name']);

                    $namecol = 'contact_phone';
                    $col2 = fmp_Param_FieldList_Opportunity::$F[$namecol];
                    $h[] = str_replace('<br>', ' ', $col2['name']);

                    continue; 
                }

                if ($k == 'sales') {
                    $h[] = strtoupper('estimated_annualized_sales');
                    $h[] = strtoupper('estimated_annualized_gp');
                    $h[] = strtoupper('estimated_annualized_gp_percent');
                    continue;
                }

                if ($k == 'monthly_sales') {
                    $h[] = strtoupper('estimated_monthly_sales');
                    $h[] = strtoupper('estimated_monthly_gp');
                    $h[] = strtoupper('estimated_monthly_gp_percent');
                    continue;
                }

                if ($k == 'prev12mo') {
                    $h[] = strtoupper('prev12mo_sales');
                    $h[] = strtoupper('prev12mo_gp');
                    $h[] = strtoupper('prev12mo_gp_percent');
                    continue;
                }

                if ($k == 'rolling') {
                    $h[] = strtoupper('rolling_sales'); 
                    $h[] = strtoupper('rolling_gp');
                    $h[] = strtoupper('rolling_gp_percent');
                    continue;
                }

                if ($k == 'mtd') {
                    $h[] = strtoupper('mtd_sales'); 
                    $h[] = strtoupper('mtd_gp');
                    $h[] = strtoupper('mtd_gp_percent');
                    continue;
                }

                if ($k == 'ytd') {
                    $h[] = strtoupper('ytd_sales');
                    $h[] = strtoupper('ytd_gp');
                    $h[] = strtoupper('ytd_gp_percent');
                    continue;
                }

                $h[] = str_replace('<br>', ' ', $col['name']);
            }
            $h = implode($this->col_delim, $h) . $this->row_delim;
            fwrite($f, $h);

            $rows = $this->rows_extract($rs);
            $rows = $this->rows__upate_with_contacts($rows);

            foreach($rows as $row) {
                $row['sales_stage'] = $this->html__sales_stage__val($row['sales_stage']);
                $row['date_closed'] = $this->html__date_mk($row['date_closed']);
                $row['product_line'] = str_replace("\n", '; ', $row['product_line']);
                $row['held_not_held'] = $this->html__status(
                                                        $row['type_of_activity'], 
                                                        $row['held_not_held'], 
                                                        $app_list_strings
                                                    );
                $row['op_date_modified'] = $this->html__datetime_mk($row['op_date_modified']);

                if (!$row['sales']) {
                    $row['sales'] = 0;
                }

                $row['sales'] = $row['sales'];
                $row['sales_gp'] = ($row['sales'] * $row['gp_perc'])/100;
                $row['sales_gp_percent'] = $row['gp_perc'];
                if ($row['sales_gp_percent'])  $row['sales_gp_percent'] = $row['sales_gp_percent'].'%';

                $row['monthly_sales'] = round($row['sales']/12);
                $row['monthly_sales_gp'] = ($row['sales'] * $row['gp_perc'])/1200;
                $row['monthly_sales_gp_percent'] = $row['gp_perc'];
                if($row['monthly_sales_gp_percent']) $row['monthly_sales_gp_percent'] = $row['monthly_sales_gp_percent'].'%';



                $h_rows = array(
                    'customer_no' => $this->csv_quote($row['customer_no']),
                    'account_name' => $this->csv_quote($row['account_name']),
                    'sales_rep_id' => $this->csv_quote($row['sales_rep_id']),
                    'sales_rep_last_name' => $this->csv_quote($row['sales_rep_last_name']),
                    'user_login' => $this->csv_quote($row['user_login']),

                    'contact_name' => $this->csv_quote($row['contact_name']),
                    'contact_address' => $this->csv_quote($row['contact_address']),
                    'contact_phone' => $this->csv_quote($row['contact_phone']),

                    'product' => $this->csv_quote($row['product']),
                    'product_line' => $this->csv_quote($row['product_line']),

                    'sales' => $row['sales'],
                    'sales_gp' => ($row['sales'] * $row['gp_perc'])/100,
                    'sales_gp_percent' => $row['gp_perc'],
                    
                    'monthly_sales' => round($row['sales']/12),
                    'monthly_sales_gp' => ($row['sales'] * $row['gp_perc'])/1200,
                    'monthly_sales_gp_percent' => $row['gp_perc'],
//                    'monthly_sales' => round($row['estimated_monthly_sales']),


                    'gp_dollar' => ($row['sales'] * $row['gp_perc'])/100,
                    'gp_perc' => $row['gp_perc'],


                    'prev12mo' => '--',
                    'rolling' => '--',
                    'mtd' => '--',
                    'ytd' => '--',

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

                    if ($col_id == 'sales_rep_last_name') {
                        $h[] = $this->csv_quote($row['sales_rep_id']); 
                        $h[] = $this->csv_quote($row['sales_rep_last_name']); 
                        $h[] = $this->csv_quote($row['user_login']);
                        continue;                
                    }
    
                    if ($col_id == 'contact_name') {
                        $h[] = $this->csv_quote($row['contact_name']);
                        $h[] = $this->csv_quote($row['contact_address']);
                        $h[] = $this->csv_quote($row['contact_phone']);
                        continue;
                    }

                    if ($col_id == 'sales') {
                        $h[] = $row['sales'];
                        $h[] = $row['sales_gp'];
                        $h[] = $row['sales_gp_percent'];
                        continue;
                    }

                    if ($col_id == 'monthly_sales') {
                        $h[] = $row['monthly_sales'];
                        $h[] = $row['monthly_sales_gp'];
                        $h[] = $row['monthly_sales_gp_percent'];
                        continue;
                    }
                    
                    if ($col_id == 'prev12mo') {
                        $h[] = $row['prev12mo_sales'];
                        $h[] = $row['prev12mo_gp'];
                        $h[] = $row['prev12mo_gp_percent']; 
                        continue;
                    }

                    if ($col_id == 'rolling') {
                        $h[] = $row['rolling_sales'];
                        $h[] = $row['rolling_gp'];
                        $h[] = $row['rolling_gp_percent'];
                        continue;
                    }

                    if ($col_id == 'mtd') {
                        $h[] = $row['mtd_sales'];
                        $h[] = $row['mtd_gp'];
                        $h[] = $row['mtd_gp_percent'];
                        continue;
                    }

                    if ($col_id == 'ytd') {
                        $h[] = $row['ytd_sales'];
                        $h[] = $row['ytd_gp'];
                        $h[] = $row['ytd_gp_percent'];
                        continue;
                    }
                    
                    $h[] = $v;
                }
                $h = implode($this->col_delim, $h) . $this->row_delim;
                fwrite($f, $h);
            }
            fclose($f);
        } 
        else /*($format == "HTML")*/ {
            $f = fopen($this->report_result, "w");

            $c = file_get_contents($this->path_html('header.html'));
            $c = str_replace("{SITE_URL}", $sugar_config["site_url"], $c);
            $c = str_replace("{THEME_URL}", $sugar_config["site_url"]."/themes/".$theme, $c);
            $c = str_replace("{CHARSET}", $this->get_charset(), $c);
            fwrite($f, $c);

           

            fwrite($f, file_get_contents($this->path_html('style.html')));
            
            $oFList = new fmp_Param_FieldList_Opportunity();
            if (!$a_field_list = $oFList->r_fields()) {
                fwrite($f, '<p style="text-align: left;">You need to select one or more fields</p>');
                return $result = true;
            }

            $aHead = $oFList->html_header();
            
            fwrite($f, '<table width="' . $aHead['width'] . '" cellpadding="0" cellspacing="0" border="0" class="listView">');
            if ($this->include_header) {
                fwrite($f, $aHead['html']);
            }
            $ext = "1";

            $sum_o = new QueryTemplate_FMP_Opportunity__Summary();

            if ($rows_found) {
               
                $rows = $this->rows_extract($rs);
                $rows = $this->rows__upate_with_contacts($rows);

                $sum_o->evaluate($rows);
                
                foreach ($rows as $row) {
                    $class = 'oddListRowS1';
                    if ($ext == '2') {
                        $class = 'evenListRowS1';
                    }
                    $row = $this->html__row__preprocess($row);
                    $h = $this->html_row($row, $class, $a_field_list);
                    
                    if ($ext == '1') {
                        $ext = '2';
                    } else { 
                        $ext = '1';
                    }
                    
                    fwrite($f, $h);                        
                }

            } else {
                $h = $this->html__not_found();
                fwrite($f, $h);
            }
            
            
//            $h = $this->html__total_analysis($summary_a);
            $h = $sum_o->html();
            fwrite($f, $h);

            fwrite($f, '</table>');
            fclose($f);

        }

        $result = true;
        
        return $result;
    }

    protected function html__row__preprocess($row) 
    {
        global $app_list_strings;

        if (($row['probability'])) {
            $row['probability'] .= "%";
        }

        $row['sales_stage'] = $this->html__sales_stage__val($row['sales_stage']);

        $row['date_closed'] = $this->html__date_mk($row['date_closed']);
        $row['held_not_held']    = $this->html__status(
                                                $row['type_of_activity'], 
                                                $row['held_not_held'], 
                                                $app_list_strings
                                            );
        

       
        $row['annualized_gp'] = '$' . $this->html__sales(round($row['sales'] * $row['gp_perc']/100));
        $row['annualized_gp_percent'] = $this->html__sales($row['gp_perc']).'%';

        $row['monthly_sales'] = '$' . $this->html__sales(round($row['sales']/12));
        $row['monthly_gp'] = '$' . $this->html__sales(round($row['sales'] * $row['gp_perc']/1200));
        $row['monthly_gp_percent'] = $this->html__sales($row['gp_perc']).'%';

        $row['gp_dollar'] = '$' . $this->html__sales($row['sales'] * $row['gp_perc']/100);
        $row['sales'] = '$' . $this->html__sales($row['sales']);




        $row['prev12mo_gp'] = '$' . $this->html__sales($row['prev12mo_gp']);
        $row['prev12mo_gp_percent'] = $this->html__sales($row['prev12mo_gp_percent']).'%';
        $row['prev12mo_sales'] = '$' . $this->html__sales($row['prev12mo_sales']);

        $row['mtd_gp'] = '$' . $this->html__sales($row['mtd_gp']);
        $row['mtd_gp_percent'] = $this->html__sales($row['mtd_gp_percent']).'%';
        $row['mtd_sales'] = '$' . $this->html__sales($row['mtd_sales']);

        $row['ytd_gp'] = '$' . $this->html__sales($row['ytd_gp']);
        $row['ytd_gp_percent'] =$this->html__sales($row['ytd_gp_percent']).'%';
        $row['ytd_sales'] = '$' . $this->html__sales($row['ytd_sales']);

        $row['rolling_gp'] = '$' . $this->html__sales($row['rolling_gp']);
        $row['rolling_gp_percent'] =$this->html__sales($row['rolling_gp_percent']).'%';
        $row['rolling_sales'] = '$' . $this->html__sales($row['rolling_sales']);

        $row['op_date_modified'] = $this->html__datetime_mk($row['op_date_modified']);

        $product_line = $row['product_line'];
        $row = $this->html__row__with_html_spaces($row);
        $row['product_line'] = str_replace("\n", '<br />', $product_line);

        return $row;
    }

    protected function html_prev12mo_mtd_rolling($sales, $gp, $gp_perc) 
    {
        $h = '' 
            . '<table cellspacing="0" cellpadding="0" width="100%" border="0">'
                . '<tr>' 
                    . '<td width="50%" style="border: 0;">Sales:</td>'
                    . '<td width="50%"  style="border: 0;" align="left">&nbsp;' . $sales . '</td>'
                . '</tr>'

                . '<tr>' 
                    . '<td width="50%" style="border: 0;">GP$:</td>'
                    . '<td width="50%"  style="border: 0;" align="left">&nbsp;' . $gp . '</td>' 
                . '</tr>'

                . '<tr>' 
                    . '<td width="50%" style="border: 0;">GP%:</td>'
                    . '<td width="50%"  style="border: 0;" align="left">&nbsp;' . $gp_perc . '</td>' 
                . '</tr>'
            . '</table>'
            ;
        return $h;
    }


    protected function url($url)
    {
        global $sugar_config;
        return $sugar_config['site_url'] . '/'. $url;
    }
    
    protected function html_row($row, $class, $a_field_list)
    {
        $prev12mo   = $this->html_prev12mo_mtd_rolling(
                                $row['prev12mo_sales'], 
                                $row['prev12mo_gp'], 
                                $row['prev12mo_gp_percent']
                            );

        $rolling         = $this->html_prev12mo_mtd_rolling(
                                $row['rolling_sales'], 
                                $row['rolling_gp'], 
                                $row['rolling_gp_percent']
                            );

        $mtd            = $this->html_prev12mo_mtd_rolling(
                                $row['mtd_sales'], 
                                $row['mtd_gp'], 
                                $row['mtd_gp_percent']
                            );

        $annualized_sales = $this->html_prev12mo_mtd_rolling(
                                $row['sales'],
                                $row['annualized_gp'],
                                $row['annualized_gp_percent']
                            );

        $monthly_sales = $this->html_prev12mo_mtd_rolling(
                                $row['monthly_sales'],
                                $row['monthly_gp'],
                                $row['monthly_gp_percent']
                            );
        $ytd            = $this->html_prev12mo_mtd_rolling(
                                $row['ytd_sales'],
                                $row['ytd_gp'],
                                $row['ytd_gp_percent']
                            );

        $url_acc = 'index.php?module=Accounts&action=DetailView&record=' . $row['dev_account_id'];
        $url_acc = $this->url($url_acc);
        
        $url_opp = 'index.php?module=Opportunities&action=DetailView&record=' . $row['dev_opportunity_id'];
        $url_opp = $this->url($url_opp);

        $account_name = '&nbsp;';
        if (!$duplicated) {
            if ($row['account_name'] != '&nbsp;') {
                $account_name = ''
                    . '<a href="' . $url_acc . '" target="_blank">' . $row['account_name'] . '</a>'
                    ;
            }
        }

        $sales_rep = '&nbsp;';
        if (!$duplicated) { 
            $sales_rep = array();
            $sales_rep[] = '<span class="fmpGrayed">Name: </span>' . $row['sales_rep_last_name'];
            $sales_rep[] = '<span class="fmpGrayed">ID: </span>' . $row['sales_rep_id'];
            $sales_rep[] = '<span class="fmpGrayed">User: </span>'
                        . $row['user_login'];
            $sales_rep = implode('<br>', $sales_rep);
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

        $h_rows = array(
            'customer_no' => '<td class="' . $class . '">' . $row['customer_no'] . '</td>',
            'account_name' => '<td class="' . $class . '">' . $account_name . '</td>',
            'sales_rep_last_name' => '<td class="' . $class . '">' . $sales_rep . '</td>',
            'contact_name' => '<td class="' . $class . '">' . $contact . '</td>',
            'product' => '<td class="' . $class . '">' . '<a href="' . $url_opp . '" target="_blank">'. $row['product']. '</a>' . '</td>',
            'product_line' => '<td class="' . $class . '">' . $row['product_line'] . '</td>',
            'sales' => '<td class="' . $class . '">' . $annualized_sales . '</td>',
            'monthly_sales' => '<td class="' . $class . '">' . $monthly_sales . '</td>',
            //'sales' => '<td class="' . $class . '">' . $row['sales'] . '</td>',
            //'gp_dollar' => '<td class="' . $class . '">' . $row['gp_dollar'] . '</td>',
            //'gp_perc' => '<td class="' . $class . '">' . $row['gp_perc'] . '</td>',

            'prev12mo' => '<td class="' . $class . '">' . $prev12mo . '</td>',
            'rolling' => '<td class="' . $class . '">' . $rolling . '</td>',
            'mtd' => '<td class="' . $class . '">' . $mtd . '</td>',
            'ytd' => '<td class="' . $class . '">' . $ytd . '</td>',

            'sales_stage' => '<td class="' . $class . '">' . $row['sales_stage'] . '</td>',

            'additional_opp_info' => '<td class="' . $class . '">' . $row['additional_opp_info'] . '</td>',
            'opupdate' => '<td class="' . $class . '">' . $row['opupdate'] . '</td>',
            'date_closed' => '<td class="' . $class . '">' . $row['date_closed'] . '</td>',
            'probability' => '<td class="' . $class . '">' . $row['probability'] . '</td>',
            'op_date_modified' => '<td class="' . $class . ' right">' . $row['op_date_modified'] . '</td>'
        );

        $o = new fmp_Param_Showactivities();
        $showactivities = $o->r_showactivities();

        $h = array();
        foreach($h_rows as $col_id=>$v) {
            if (!isset($a_field_list[$col_id])) {
                continue;
            }

            $h[] = $v;
            
            if ($showactivities) {
                if ($col_id == 'mtd') {
                    $h[] = '<td class="' . $class . '">&nbsp;</td>';
                    $h[] = '<td class="' . $class . '">&nbsp;</td>';
                }
            }
        }

        $h_sep = array();
        foreach($h as $v) {
            $h_sep[] = '<td class="listViewHRS1 ' . $class . '"></td>';
        }

        $h = ''
            . '<tr height="20">'
                . implode('', $h)
            . '</tr>'
            ;

        if ($showactivities) {

            if (is_array($row['activities'])) {
                $h1 = array();
                foreach($row['activities'] as $aa) {
                    $h1_row = array();

                    foreach($h_rows as $col_id=>$v) {
                        if (!isset($a_field_list[$col_id])) {
                            continue;
                        }

                        if ($col_id == 'additional_opp_info') {
                            $h1_row[] = '<td class="' . $class . '">' . $aa['points'] . '&nbsp;</td>';
                            continue;
                        }

                        if ($col_id == 'opupdate') {
                            $h1_row[] = '<td class="' . $class . '">' . $aa['outcome'] . '&nbsp;</td>';
                            continue;
                        }

                        if ($col_id == 'op_date_modified') {
                            $h1_row[] = '<td class="' . $class . '">' . $this->html__datetime_mk($aa['date_modified']) . '&nbsp;</td>';
                            continue;
                        }

                        $h1_row[] = '<td class="' . $class . '">---</td>';
                        if ($col_id == 'mtd') {
                            $h1_row[] = '<td class="' . $class . '">' . $aa['type_of_activity'] . '&nbsp;</td>';
                            $h1_row[] = '<td class="' . $class . '">' . $this->html__datetime_mk($aa['date_time_of_activity']) . '&nbsp;</td>';                    
                        }

                    }

                    $h1[] = ''
                        . '<tr height="20">'
                            . implode('', $h1_row)
                        . '</tr>'
                        ;
                }

                $h .= implode('', $h1);
            }
        }

        return $h 
            . '<tr>'
                . implode('', $h_sep)
                //. '<td colspan="100" class="listViewHRS1"></td>'
            . '</tr>'
            ;
    }
}


class QueryTemplate_FMP_Opportunity__Summary 
{
    const SS_TYPE_ACTIVE = 1;
    const SS_TYPE_NON_ACTIVE = 2;
    const S_NOT_AVAIL = 'N/A';

    protected static $SS_TYPES = array(
        'Stage1' => self::SS_TYPE_ACTIVE, 
        'Stage 2' => self::SS_TYPE_ACTIVE, 
        'Stage 3' => self::SS_TYPE_ACTIVE, 
        'Stage 4 ' => self::SS_TYPE_ACTIVE, 
        'Stage 5' => self::SS_TYPE_ACTIVE, 
        'Closed Won' => self::SS_TYPE_NON_ACTIVE, 
        'Closed Lost' => self::SS_TYPE_NON_ACTIVE
    );

    protected $total_count_active = 0; //TotalCountActive
    protected $total_sum_active = 0; //TotalSumActive
    protected $total_count_closed = 0; //TotalSumFinished
    protected $total_sum_closed = 0; //TotalCountFinished
    protected $total_count = 0;
    protected $total_sum = 0;

    protected $data = array();

    function __construct() 
    {
        foreach(self::$SS_TYPES as $stage_id=>$type) {
            $this->data[$type][$stage_id] = array(
                'num' => 0, 
                'amount' => 0,
                'num_perc' => self::S_NOT_AVAIL,
                'amount_perc' => self::S_NOT_AVAIL,
                'mtd_sales' => 0,
                'mtd_gp'=> 0,
                'mtd_gp_persent' => self::S_NOT_AVAIL,
                'ytd_sales' => 0,
                'ytd_gp'=> 0,
                'ytd_gp_persent' => self::S_NOT_AVAIL,
                'prev12mo_sales' => 0,
                'prev12mo_gp'=> 0,
                'prev12mo_gp_persent' => self::S_NOT_AVAIL,
                'rolling_sales' => 0,
                'rolling_gp'=> 0,
                'rolling_gp_persent' => self::S_NOT_AVAIL
            );
        }
    }
    
    protected function sales_stage__list() 
    {
        global $app_list_strings;
        return $app_list_strings['sales_stage_dom'];
    }

    public function getData()
    {
        return $this->data;
    }

    public function &html() 
    {
        $ss = $this->sales_stage__list();

        $h1 = array();
        foreach($this->data[self::SS_TYPE_ACTIVE] as $stage_id=>$a) {
            $h1[] = ''
                . '<tr>'
                    . '<td>&nbsp;' . $ss[$stage_id] . ':</td>'
                    . '<td>&nbsp;' . $a['num'] . '</td>'
                    . '<td>&nbsp;' . $this->html_amount($a['amount']) . '</td>'
                    . '<td>&nbsp;' . $this->html_percent($a['num_perc']) . '</td>'
                    . '<td>&nbsp;' . $this->html_percent($a['amount_perc']) . '</td>'
                . '</tr>'
                ;
        }
        
        $h1 = implode('', $h1);
        $h1 = $this->html_table_ss(
                    $h1,
                    $this->total_count_active,
                    $this->html_amount($this->total_sum_active),
                    'Active',
                    '% of Active Total Opp',
                    '% of Active Total Opp Dollars'
                 );


        $h2 = array();
        foreach($this->data[self::SS_TYPE_NON_ACTIVE] as $stage_id=>$a) {

//            $Num  = $a['num'];
//            $TCA  = $summary_a['TotalCountActive'];
//            $TCC  = $summary_a['TotalCountFinished'];

            $h2[] = ''
                . '<tr>'
                    . '<td>&nbsp;' . $ss[$stage_id] . '</td>'
                    . '<td>&nbsp;' . $a['num'] . '</td>'
                    . '<td>&nbsp;' . $this->html_amount($a['amount']) . '</td>'
                    . '<td>&nbsp;' . $this->html_percent($a['num_perc'])  /*round( 100*$Num / ($TCA + $TCC), 2 )*/ . '</td>'     
                    . '<td>&nbsp;' . $this->html_percent($a['amount_perc']) /*round( (100*$Num / $TCC), 2 )*/ . '</td>'
                . '</tr>';
        }

        $h2 = implode('', $h2);
        $h2 = $this->html_table_ss(
                    $h2, 
                    $this->total_count_closed, 
                    $this->html_amount($this->total_sum_closed), 
                    'Closed',
                    '% Closed/Total Opp',
                    '% Closed/Total'
                 );

        $h3 = '' 
            . '<table style="margin-top:20px" cellpadding="0" cellspacing="0" border="0" class="listView">'
                . '<tr>'
                    . '<th class="listViewThS1 bottom" width="150">&nbsp;</th>'   
                    . '<th class="listViewThS1 bottom" width="100">Total</th>'
                    . '<th class="listViewThS1 bottom" width="100">Total Dollars</th>'
                . '</tr>'
                . '<tr>'
                    . '<td>Total Opportunities: </td>'
                    . '<td>&nbsp;' . $this->total_count . '</td>'
                    . '<td>&nbsp;' . $this->html_amount($this->total_sum) . '</td>'
                . '</tr>'
            . '</table>'
            ;

        $h4 = ''
            . '<table style="margin-top:20px" cellpadding="0" cellspacing="0" border="0" class="listView">'
                . '<tr>'
                    . '<th class="listViewThS1 bottom" width="150">Closed Won Stage</th>'
                    . '<th class="listViewThS1 bottom" width="100">Prev 12-mo avg</th>'
                    . '<th class="listViewThS1 bottom" width="100">Rolling</th>'
                    . '<th class="listViewThS1 bottom" width="100">MTD</th>'
                    . '<th class="listViewThS1 bottom" width="100">YTD</th>'
                . '</tr>'
                . '<tr>'
                    . '<td>&nbsp;</td>'
                    . '<td>&nbsp;' . $this->total_html_prev12mo_mtd_rolling($this->html_amount($this->data[self::SS_TYPE_NON_ACTIVE]['Closed Won']['prev12mo_sales']),$this->html_amount($this->data[self::SS_TYPE_NON_ACTIVE]['Closed Won']['prev12mo_gp']),$this->html_percent($this->data[self::SS_TYPE_NON_ACTIVE]['Closed Won']['prev12mo_gp_persent'])) . '</td>'
                    . '<td>&nbsp;' . $this->total_html_prev12mo_mtd_rolling($this->html_amount($this->data[self::SS_TYPE_NON_ACTIVE]['Closed Won']['rolling_sales']),$this->html_amount($this->data[self::SS_TYPE_NON_ACTIVE]['Closed Won']['rolling_gp']),$this->html_percent($this->data[self::SS_TYPE_NON_ACTIVE]['Closed Won']['rolling_gp_persent'])) . '</td>'
                    . '<td>&nbsp;' . $this->total_html_prev12mo_mtd_rolling($this->html_amount($this->data[self::SS_TYPE_NON_ACTIVE]['Closed Won']['mtd_sales']),$this->html_amount($this->data[self::SS_TYPE_NON_ACTIVE]['Closed Won']['mtd_gp']),$this->html_percent($this->data[self::SS_TYPE_NON_ACTIVE]['Closed Won']['mtd_gp_persent'])) . '</td>'
                    . '<td>&nbsp;' . $this->total_html_prev12mo_mtd_rolling($this->html_amount($this->data[self::SS_TYPE_NON_ACTIVE]['Closed Won']['ytd_sales']),$this->html_amount($this->data[self::SS_TYPE_NON_ACTIVE]['Closed Won']['ytd_gp']),$this->html_percent($this->data[self::SS_TYPE_NON_ACTIVE]['Closed Won']['ytd_gp_persent'])) . '</td>'
                . '</tr>'
            . '</table>'
            ;
            
        return $h1 . $h2 . $h3 . $h4;
    }

    protected function html_amount($val) 
    {
        return '$' . number_format($val, 0, '.', ',');
    }
    
    protected function html_percent($val) 
    {
        if (strtoupper($val) == self::S_NOT_AVAIL) {
            return self::S_NOT_AVAIL;
        }
        return round($val, 2) . '%';
    }

    protected function &html_table_ss(&$h_rows, $v1, $v2, $desc, $title1, $title2) 
    {
        $h1 = ''
            . '<table cellpadding="0" cellspacing="0" border="0" class="listView" style="margin-top:20px">'
                . '<tr>'
                    . '<th class="listViewThS1 bottom" width="150">' . $desc . ' Sales Stages </th>'
                    . '<th class="listViewThS1 bottom" width="100">Total</th>'
                    . '<th class="listViewThS1 bottom" width="100">Total Dollars</th>'
                    . '<th class="listViewThS1 bottom" width="150">' . $title1 . '</th>'
                    . '<th class="listViewThS1 bottom" width="150">' . $title2 . '</th>'
                . '</tr>'
                . $h_rows
                . '<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>'
                . '<tr>'
                    . '<td>&nbsp;' . $desc . ' Total Opp:</td>'
                    . '<td>&nbsp;' . $v1 . '</td>'
                    . '<td>&nbsp;' . $v2 . '</td>'
                    . '<td>&nbsp;</td>'
                    . '<td>&nbsp;</td>'
                . '</tr>'
            . '</table>'
            ;
        return $h1;
    }


    protected function total_html_prev12mo_mtd_rolling($sales, $gp, $gp_perc)
    {
        $h = ''
            . '<table cellspacing="0" cellpadding="0" width="100%" border="0">'
                . '<tr>'
                    . '<td width="50%" style="border: 0;">&nbsp;Sales:</td>'
                    . '<td width="50%"  style="border: 0;" align="left">&nbsp;' . $sales . '</td>'
                . '</tr>'

                . '<tr>'
                    . '<td width="50%" style="border: 0;">&nbsp;GP$:</td>'
                    . '<td width="50%"  style="border: 0;" align="left">&nbsp;' . $gp . '</td>'
                . '</tr>'

                . '<tr>'
                    . '<td width="50%" style="border: 0;">&nbsp;GP%:</td>'
                    . '<td width="50%"  style="border: 0;" align="left">&nbsp;' . $gp_perc . '</td>'
                . '</tr>'
            . '</table>'
            ;
        return $h;
    }

    
    public function evaluate(&$rows) 
    {
        foreach($rows as $opportunity_id=>$o) {
            $stage_id = $o['sales_stage'];
            if (!isset(self::$SS_TYPES[$stage_id])) {
                continue;
            }
            $type = self::$SS_TYPES[$stage_id];

            $this->data[$type][$stage_id]['num']++;
            $this->data[$type][$stage_id]['amount'] += $o['sales'];
            $this->data[$type][$stage_id]['mtd_sales'] += $o['mtd_sales'];
            $this->data[$type][$stage_id]['mtd_gp'] += $o['mtd_gp'];
            $this->data[$type][$stage_id]['ytd_sales'] += $o['ytd_sales'];
            $this->data[$type][$stage_id]['ytd_gp'] += $o['ytd_gp'];
            $this->data[$type][$stage_id]['prev12mo_sales'] += $o['prev12mo_sales'];
            $this->data[$type][$stage_id]['prev12mo_gp'] += $o['prev12mo_gp'];
            $this->data[$type][$stage_id]['rolling_sales'] += $o['rolling_sales'];
            $this->data[$type][$stage_id]['rolling_gp'] += $o['rolling_gp'];

        }


        foreach($this->data[self::SS_TYPE_ACTIVE] as $v) {
            $this->total_count_active += $v['num'];
            $this->total_sum_active += $v['amount'];
        }

        foreach($this->data[self::SS_TYPE_NON_ACTIVE] as $v) {
            $this->total_count_closed += $v['num'];
            $this->total_sum_closed += $v['amount'];
        }

        
        foreach($this->data[self::SS_TYPE_ACTIVE] as $stage_id=>$v) {
            if ($this->total_count_active) {
                $this->data[self::SS_TYPE_ACTIVE][$stage_id]['num_perc'] = ($v['num'] / $this->total_count_active) * 100;
            }
            if ($this->total_sum_active) {
                $this->data[self::SS_TYPE_ACTIVE][$stage_id]['amount_perc'] = ($v['amount'] / $this->total_sum_active) * 100;
            }
        }

        foreach($this->data[self::SS_TYPE_NON_ACTIVE] as $stage_id=>$v) {
            if($v['prev12mo_sales']) $this->data[self::SS_TYPE_NON_ACTIVE][$stage_id]['prev12mo_gp_persent'] = ($v['prev12mo_gp'] / $v['prev12mo_sales']) * 100;
            if($v['rolling_sales']) $this->data[self::SS_TYPE_NON_ACTIVE][$stage_id]['rolling_gp_persent'] = ($v['rolling_gp'] / $v['rolling_sales']) * 100;
            if($v['mtd_sales']) $this->data[self::SS_TYPE_NON_ACTIVE][$stage_id]['mtd_gp_persent'] = ($v['mtd_gp'] / $v['mtd_sales']) * 100;
            if($v['ytd_sales']) $this->data[self::SS_TYPE_NON_ACTIVE][$stage_id]['ytd_gp_persent'] = ($v['ytd_gp'] / $v['ytd_sales']) * 100;
         }

//        foreach($this->data[self::SS_TYPE_NON_ACTIVE] as $stage_id=>$v) {
//            if ($this->total_count_closed) {
//                $this->data[self::SS_TYPE_NON_ACTIVE][$stage_id]['num_perc'] = ($v['num'] / $this->total_count_closed) * 100;
//            }
//
//            if ($this->total_sum_closed) {
//                $this->data[self::SS_TYPE_NON_ACTIVE][$stage_id]['amount_perc'] = ($v['amount'] / $this->total_sum_closed) * 100;
//            }
//        }

        $this->total_count = $this->total_count_active + $this->total_count_closed;
        $this->total_sum = $this->total_sum_active + $this->total_sum_closed;


        foreach($this->data[self::SS_TYPE_NON_ACTIVE] as $stage_id=>$v) {
            if ($this->total_count) {
                $this->data[self::SS_TYPE_NON_ACTIVE][$stage_id]['num_perc'] = ($v['num'] / $this->total_count) * 100;
            }

            if ($this->total_sum_closed) {
                $this->data[self::SS_TYPE_NON_ACTIVE][$stage_id]['amount_perc'] = ($v['amount'] / $this->total_sum) * 100;
            }
        }
        return ;
    }
    
}