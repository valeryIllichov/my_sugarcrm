<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/**
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004 - 2009 SugarCRM Inc.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 */




require_once('include/Dashlets/DashletGenericChart.php');
require_once('include/utils.php');
require_once('modules/ZuckerReportParameter/fmp.class.param.slsm.php');
require_once('modules/ZuckerReportParameter/fmp.class.param.regloc.php');
require_once('include/Sugar_Smarty.php');

class FMPPipelineBySalesStageDashlet extends DashletGenericChart {
    protected $ss;
    public $pbss_date_start;
    public $pbss_date_end;
    public $pbss_date_start_S;
    public $pbss_date_end_S;
    public $pbss_sales_stages = array();
    public $pbss_probability = array();
    public $pbss_company = array();
    public $pbss_chart_type = array();
    public $pbss_estimated_monthly_sales = array();
    public $pbss_estimated_annualized_sales = array();
    public $pbss_chart_view = array();
    public $pbss_opp_type = array();
    public $promo_opp = false; 
    protected $_seedName = 'Opportunities';

    /**
     * @see DashletGenericChart::__construct()
     */
    public function __construct($id, array $options = null) {
        global $timedate;
        $this->pbss_date_start_S =  date('Y-01-01');
        $this->pbss_date_end_S =  date('Y-12-31');
        if(empty($options['pbss_date_start']))
            $options['pbss_date_start'] = date('Y-01-01');
//            $options['pbss_date_start'] = date($timedate->get_db_date_time_format(), strtotime('2010-01-01'));

        if(empty($options['pbss_date_end']))
            $options['pbss_date_end'] = date('Y-12-31');
//            $options['pbss_date_end'] = date($timedate->get_db_date_time_format(), time());
        
        if(!empty($options['pbss_date_start_S']))
            $this->pbss_date_start_S = $options['pbss_date_start_S'];

        if(!empty($options['pbss_date_end_S']))
             $this->pbss_date_end_S = $options['pbss_date_end_S'];

        if(empty($options['pbss_chart_type']))
            $options['pbss_chart_type'] = 'sales_stage';

        if(empty($options['pbss_chart_view']))
            $options['pbss_chart_view'] = 'bar';

       if (!empty($options['pbss_sales_stages']) && is_array($options['pbss_sales_stages'])) {
            if (array_search('Closed Promotion Ended', $options['pbss_sales_stages'])) {
                $this->promo_opp = true;
            }
            if (array_search('Active Promotion', $options['pbss_sales_stages'])) {
                $this->promo_opp = true;
            }
        }
        if(empty($options['title']))
//            $options['title'] = translate('LBL_PIPELINE_FORM_TITLE', 'Home');
            $options['title'] = 'FMP Top Opportunities Chart';
//        pr($current_user->getPreference('reset_clicked'));
//        pr('______________________________________________');

        

//        if(empty($options['pbss_estimated_annualized_sales']))
//            $options['pbss_estimated_annualized_sales'] = array('$5,999 and under', '$6,000-$24,999', '$25,000-$59,999', '$60,000-$99,999', '$100,000-$249,999', '$250,000-$499,999', '$500,000-$999,999', '$1,000,000 and above');

        $this->_configureTpl = 'modules/Charts/Dashlets/FMPPipelineBySalesStageDashlet/CustomDashletGenericChartConfigure.html';

        parent::__construct($id,$options);
    }

    /**
     * @see DashletGenericChart::displayOptions()
     */
    public function displayOptions() {
        global $app_list_strings;


        /*====Chart Type==========*/
        $pbss_chart_type = array('sales_stage'=>'By Sales Stage',
                'monthly_sales'=>'By Monthly Sales',
                'annualized_sales'=>'By Annualized Sales',
                'probability'=>'By Probability %',
            'company'=>'By company',
                'acc_name' => 'By Account Name',
                'opp_name' => 'By Opportunity Name');
        $this->_searchFields['pbss_chart_type']['options'] = $pbss_chart_type;
        /*====end Chart Type==========*/

        /*======Sales Stages========*/
        if (!empty($this->pbss_sales_stages) && count($this->pbss_sales_stages) > 0) {
            foreach ($this->pbss_sales_stages as $key) {
                $selected_sales_stages_datax[] = $key;
            }
        }
        else {
            $selected_sales_stages_datax = array('Stage1', 'Stage 2', 'Stage 3', 'Stage 4 ', 'Stage 5', 'Closed Won', 'Closed Lost');
        }

        $this->_searchFields['pbss_sales_stages']['options'] = $app_list_strings['sales_stage_dom'];
        $this->_searchFields['pbss_sales_stages']['input_name0'] = $selected_sales_stages_datax;
        /*======end Sales Stages========*/
     /*======Opp Type========*/
        if (!empty($this->pbss_opp_type) && count($this->pbss_opp_type) > 0) {
            foreach ($this->pbss_opp_type as $key) {
                $selected_opp_type_datax[] = $key;
            }
        }
        else {
            $selected_opp_type = array_diff(array_keys($app_list_strings['opportunity_type_dom']), array(''));
            foreach($selected_opp_type as $value){
                $selected_opp_type_datax[] = $value;
            }
        }

        $this->_searchFields['pbss_opp_type']['options'] = array_diff($app_list_strings['opportunity_type_dom'],array(''));
        $this->_searchFields['pbss_opp_type']['input_name0'] = $selected_opp_type_datax;
        /*======end Opp Type========*/
        /*=====Estimated Monthly Sales====*/
        $pbss_estimated_monthly_sales = array('$499 and under', '$500-$1000', '$1001-$1999', '$2000 and above');
        if (!empty($this->pbss_estimated_monthly_sales) && count($this->pbss_estimated_monthly_sales) > 0) {
            foreach ($this->pbss_estimated_monthly_sales as $key => $value) {
                $selected_monthly_sales_datax[] = $value;
            }
        }
        else {
            //$selected_monthly_sales_datax[] = array('$499 and under', '$500-$1000', '$1001-$1999', '$2000 and above');
            $selected_monthly_sales_datax = array(0,1,2,3);
        }

        $this->_searchFields['pbss_estimated_monthly_sales']['options'] = $pbss_estimated_monthly_sales;
        $this->_searchFields['pbss_estimated_monthly_sales']['input_name0'] = $selected_monthly_sales_datax;
        /*=====end Estimated Monthly Sales====*/

        /*=====Estimated Annualized Sales====*/
        $pbss_estimated_annualized_sales = array('0' => '$5,999 and under', '1' =>'$6,000-$24,999', '2' =>'$25,000-$59,999', '3' =>'$60,000-$99,999', '4' =>'$100,000-$249,999', '5' =>'$250,000-$499,999', '6' =>'$500,000-$999,999', '7' =>'$1,000,000 and above');
        if (!empty($this->pbss_estimated_annualized_sales) && count($this->pbss_estimated_annualized_sales) > 0){
            foreach ($this->pbss_estimated_annualized_sales as $key => $value){
                $selected_annualized_sales_datax[] = $value;
            }
        }
        else{
            //$selected_annualized_sales_datax[] = array('0' => '$5,999 and under', '1' => '$6,000-$24,999', '2' =>'$25,000-$59,999', '3' =>'$60,000-$99,999', '4' =>'$100,000-$249,999', '5' =>'$250,000-$499,999', '6' =>'$500,000-$999,999', '7' =>'$1,000,000 and above');
            $selected_annualized_sales_datax = array(0,1,2,3,4,5,6,7);
        }
        $this->_searchFields['pbss_estimated_annualized_sales']['options'] = $pbss_estimated_annualized_sales;
        $this->_searchFields['pbss_estimated_annualized_sales']['input_name0'] = $selected_annualized_sales_datax;
        /*=====end Estimated Annualized Sales====*/

        /*=====Probability %====*/
        $pbss_pbss_probability = array('0'=>'0%', '25'=>'25%', '50'=>'50%', '75'=>'75%', '100'=>'100%');
        if (!empty($this->pbss_probability) && count($this->pbss_probability) > 0) {
            foreach ($this->pbss_probability as $key => $value) {
                $selected_probability_datax[] = $value;
            }
        }
        else {
            //$selected_probability_datax = array('0'=>'0%', '25'=>'25%', '50'=>'50%', '75'=>'75%', '100'=>'100%');
            $selected_probability_datax = array(0,25,50,75,100);
        }

        $this->_searchFields['pbss_probability']['options'] = $pbss_pbss_probability;
        $this->_searchFields['pbss_probability']['input_name0'] = $selected_probability_datax;
        /*=====end Probability %====*/

        /*=====Company====*/
        $pbss_pbss_company = array('0'=>'FMP', '1'=>'Splash');
        if (!empty($this->pbss_company) && count($this->pbss_company) > 0) {
            foreach ($this->pbss_company as $key => $value) {
                $selected_company_datax[] = $value;
            }
        }
        else {
            $selected_company_datax = array(0,1);
        }

        $this->_searchFields['pbss_company']['options'] = $pbss_pbss_company;
        $this->_searchFields['pbss_company']['input_name0'] = $selected_company_datax;
        /*=====end Company====*/
        
        /*=====Chart View====*/
        $pbss_chart_view = array('bar'=>'Bar', 'pie'=>'Pie');
        $this->_searchFields['pbss_chart_view']['options'] = $pbss_chart_view;
        /*=====end Chart View====*/
        $this->_searchFields['pbss_date_start_S']['input_name0'] = date('Y-m-d',strtotime($this->pbss_date_start_S));
        $this->_searchFields['pbss_date_end_S']['input_name0'] = date('Y-m-d',strtotime($this->pbss_date_end_S));
        if(!$this->promo_opp){
            unset($this->_searchFields['pbss_date_start_S']);
            unset($this->_searchFields['pbss_date_end_S']);
        }
      
        return parent::displayOptions();
    }

    function saveOptions($req) {
        global $timedate;

        $options = array();

        foreach($req as $name => $value)
            if(!is_array($value)) $req[$name] = trim($value);


        foreach($this->_searchFields as $name => $params) {
            $widgetDef = $params;
            if ( isset($this->getSeedBean()->field_defs[$name]) )
                $widgetDef = $this->getSeedBean()->field_defs[$name];
            if ( $widgetDef['type'] == 'date')           // special case date types
                $options[$widgetDef['name']] = $timedate->swap_formats($req['type_'.$widgetDef['name']], $timedate->get_date_format(), $timedate->dbDayFormat);
            elseif ( $widgetDef['type'] == 'time')       // special case time types
                $options[$widgetDef['name']] = $timedate->swap_formats($req['type_'.$widgetDef['name']], $timedate->get_time_format(), $timedate->dbTimeFormat);
            elseif ( $widgetDef['type'] == 'datepicker') // special case datepicker types
                $options[$widgetDef['name']] = $timedate->swap_formats($req[$widgetDef['name']], $timedate->get_date_format(), $timedate->dbDayFormat);
            elseif (!empty($req[$widgetDef['name']])){
                $options[$widgetDef['name']] = $req[$widgetDef['name']];
            }
        }

        if (!empty($req['dashletTitle']))
            $options['title'] = $req['dashletTitle'];

        if (!empty($req['probability']))
            $options['pbss_probability'] = $req['probability'];

        if (!empty($req['company']))
            $options['pbss_company'] = $req['company'];
        
        if (!empty($req['estimated_annualized_sales']))
            $options['pbss_estimated_annualized_sales'] = $req['estimated_annualized_sales'];

        if (!empty($req['chart_view']))
            $options['pbss_chart_view'] = $req['chart_view'];

        if (!empty($req['estimated_monthly_sales']))
            $options['pbss_estimated_monthly_sales'] = $req['estimated_monthly_sales'];

        if (!empty($req['chart_type']))
            $options['pbss_chart_type'] = $req['chart_type'];
        
        if (!empty($req['opp_type']))
             $options['pbss_opp_type'] = $req['opp_type'];

        if (!empty($req['pbss_date_start_S']))
             $options['pbss_date_start_S'] = $req['pbss_date_start_S'];
        
        if (!empty($req['pbss_date_end_S']))
             $options['pbss_date_end_S'] = $req['pbss_date_end_S'];
        
        return $options;
    }

    /**
     * @see DashletGenericChart::display()
     */

    public function display() {
        global $current_user, $sugar_config;

        /* ========== QuickSearch functionality ==============*/
//        require_once 'include/QuickSearchDefaults.php';
//        $qsd = new QuickSearchDefaults();
//
//        $o = $qsd->getQSParent();
//        $o['field_list'][] = 'custno_c';
//        $o['populate_list'][] = 'pipe_parent_name_custno_c';
//        $o['field_list'][] = 'name';
//        $sqs_objects['pipe_parent_name'] = $o;
//
//        $o = $qsd->getQSParent();
//        $o['field_list'] = array('custno_c', 'name', 'id');
//        $o['populate_list'] = array('pipe_parent_name_custno_c', "pipe_parent_name", "parent_id");
//        $o['conditions'][0]['name'] = 'custno_c';
//        $o['order'] = 'custno_c';
//
//        $sqs_objects['pipe_parent_name_custno_c'] = $o;
//
//        $quicksearch_js = '<script language="javascript">';
//        $quicksearch_js.= "if(typeof sqs_objects == 'undefined'){var sqs_objects = new Array;}";
//        $json = getJSONobj();
//        foreach($sqs_objects as $sqsfield=>$sqsfieldArray) {
//            $quicksearch_js .= "sqs_objects['$sqsfield']={$json->encode($sqsfieldArray)};";
//        }
//        echo $quicksearch_js . '</script>';
        /* ========== End QuickSearch functionality ==============*/
        
        if($current_user->getPreference('reset_clicked')){
            if($this->pbss_chart_type[0] == 'acc_name') $this->pbss_chart_type[0] = 'sales_stage';
            $current_user->setPreference('reset_clicked', false);
        }

        if($this->pbss_chart_view[0] != 'pie') {

            require_once('include/SugarCharts/SugarChart.php');
            $sugarChart = new SugarChart();
            $sugarChart->base_url = array(
                    'module' => 'Opportunities',
                    'action' => 'index',
                    'query' => 'true',
                    'searchFormTab' => 'advanced_search',
            );
            //pr($this->pbss_chart_type);
            $sugarChart->url_params = array(  );
            $sugarChart->group_by = $this->constructGroupBy($this->pbss_chart_type);
            $sugarChart->setData($this->getChartData($this->constructQuery($this->pbss_chart_type, true), $this->pbss_chart_type));
            $sugarChart->is_currency = true;
//            $sugarChart->thousands_symbol = translate('LBL_OPP_THOUSANDS', 'Charts');

            $currency_symbol = $sugar_config['default_currency_symbol'];
            if ($current_user->getPreference('currency')) {
                require_once('modules/Currencies/Currency.php');
                $currency = new Currency();
                $currency->retrieve($current_user->getPreference('currency'));
                $currency_symbol = $currency->symbol;
            }
//            $subtitle = translate('LBL_OPP_SIZE', 'Charts') . " " . $currency_symbol . "1" . translate('LBL_OPP_THOUSANDS', 'Charts');
//            $pipeline_total_string = translate('LBL_TOTAL_PIPELINE', 'Charts') . $sugarChart->currency_symbol . format_number($sugarChart->getTotal(), 0, 0, array('convert'=>true)) . $sugarChart->thousands_symbol;
            $pipeline_total_string = translate('LBL_TOTAL_PIPELINE', 'Charts') . $sugarChart->currency_symbol . number_format(round($sugarChart->getTotal()),0,'.',',') . $sugarChart->thousands_symbol;

            $sugarChart->setProperties($pipeline_total_string, $subtitle, 'horizontal group by chart');

            $xmlFile = $sugarChart->getXMLFileName($this->id);
            $sugarChart->saveXMLFile($xmlFile, $sugarChart->generateXML());
        }
        else {
            require("modules/Charts/chartdefs.php");
            $chartDef = $chartDefs['pipeline_by_lead_source'];

            $chartDef['groupBy'] = $this->pbss_chart_type;
            $chartDef['url_params'] = $this->pbss_chart_type;

//            pr($chartDef);

            require_once('include/SugarCharts/SugarChart.php');
            $sugarChart = new SugarChart();
            $sugarChart->is_currency = true;
            $currency_symbol = $sugar_config['default_currency_symbol'];
            if ($current_user->getPreference('currency')) {
                require_once('modules/Currencies/Currency.php');
                $currency = new Currency();
                $currency->retrieve($current_user->getPreference('currency'));
                $currency_symbol = $currency->symbol;
            }
            $subtitle = translate('LBL_OPP_SIZE', 'Charts') . " " . $currency_symbol . "1" . translate('LBL_OPP_THOUSANDS', 'Charts');
            $sugarChart->base_url = $chartDef['base_url'];
            $sugarChart->group_by = $chartDef['groupBy'];
            $sugarChart->url_params = array();
            if ( count($this->pbls_ids) > 0 )
                $sugarChart->url_params['assigned_user_id'] = array_values($this->pbls_ids);
            $sugarChart->getData($this->constructQuery($this->pbss_chart_type, false));

            if ($this->pbss_chart_type[0] == 'sales_stage')
                $sugarChart->data_set = $sugarChart->sortData($sugarChart->data_set, $this->pbss_chart_type[0], true);

            if($this->pbss_chart_type[0] == 'probability') {// display % for 'probability' on the Bar chart
                $mass_count = count($sugarChart->data_set);
                for ($i=0; $i<$mass_count; $i++) {
                    $sugarChart->data_set[$i]['probability'] = $sugarChart->data_set[$i]['probability'].'%';
                }
            }

            if($this->pbss_chart_type[0] == 'monthly_sales') { // display Bar dashlet for 'monthly_sales'
                $mass_count = count($sugarChart->data_set);
                for ($i=0; $i<$mass_count; $i++) {

                    if($sugarChart->data_set[$i]['monthly_sales'] <= 499 && isset($sugarChart->data_set[$i])) {
                        $data[0]['annualized_sales'] += $sugarChart->data_set[$i]['annualized_sales'];
                        $data[0]['monthly_sales'] = '$499 and under';
                        $data[0]['opp_count'] += $sugarChart->data_set[$i]['opp_count'];
                        $data[0]['total'] += $sugarChart->data_set[$i]['total'];
                        unset($sugarChart->data_set[$i]);
                    }

                    if($sugarChart->data_set[$i]['monthly_sales'] >= 500 && $sugarChart->data_set[$i]['monthly_sales'] <= 1000 && isset($sugarChart->data_set[$i])) {
                        $data[1]['annualized_sales'] += $sugarChart->data_set[$i]['annualized_sales'];
                        $data[1]['monthly_sales'] = '$500-$1000';
                        $data[1]['opp_count'] += $sugarChart->data_set[$i]['opp_count'];
                        $data[1]['total'] += $sugarChart->data_set[$i]['total'];
                        unset($sugarChart->data_set[$i]);
                    }
                    if($sugarChart->data_set[$i]['monthly_sales'] >= 1001 && $sugarChart->data_set[$i]['monthly_sales'] <= 1999 && isset($sugarChart->data_set[$i])) {
                        $data[2]['annualized_sales'] += $sugarChart->data_set[$i]['annualized_sales'];
                        $data[2]['monthly_sales'] = '$1001-$1999';
                        $data[2]['opp_count'] += $sugarChart->data_set[$i]['opp_count'];
                        $data[2]['total'] += $sugarChart->data_set[$i]['total'];
                        unset($sugarChart->data_set[$i]);
                    }
                    if($sugarChart->data_set[$i]['monthly_sales'] >= 2000 && isset($sugarChart->data_set[$i])) {
                        $data[3]['annualized_sales'] += $sugarChart->data_set[$i]['annualized_sales'];
                        $data[3]['monthly_sales'] = '$2000 and above';
                        $data[3]['opp_count'] += $sugarChart->data_set[$i]['opp_count'];
                        $data[3]['total'] += $sugarChart->data_set[$i]['total'];
                        unset($sugarChart->data_set[$i]);
                    }
                }
                unset($sugarChart->data_set);
                foreach($data as $key=>$value) { // update the keys in the array after the merger
                    $sugarChart->data_set[] = $value;
                }
            }

            if($this->pbss_chart_type[0] == 'annualized_sales') {// display Bar dashlet for 'annualized_sales'
                $mass_count = count($sugarChart->data_set);
                for ($i=0; $i<$mass_count; $i++) {
                    if($sugarChart->data_set[$i]['annualized_sales'] <= 5999 && isset($sugarChart->data_set[$i])) {
                        $data[0]['annualized_sales'] = '$5,999 and under';
                        $data[0]['monthly_sales'] += $sugarChart->data_set[$i]['monthly_sales'];
                        $data[0]['opp_count'] += $sugarChart->data_set[$i]['opp_count'];
                        $data[0]['total'] += $sugarChart->data_set[$i]['total'];
                        unset($sugarChart->data_set[$i]);
                    }

                    if($sugarChart->data_set[$i]['annualized_sales'] >= 6000 && $sugarChart->data_set[$i]['annualized_sales'] <= 24999 && isset($sugarChart->data_set[$i])) {
                        $data[1]['annualized_sales'] = '$6,000-$24,999';
                        $data[1]['monthly_sales'] += $sugarChart->data_set[$i]['monthly_sales'];
                        $data[1]['opp_count'] += $sugarChart->data_set[$i]['opp_count'];
                        $data[1]['total'] += $sugarChart->data_set[$i]['total'];
                        unset($sugarChart->data_set[$i]);
                    }
                    if($sugarChart->data_set[$i]['annualized_sales'] >= 25000 && $sugarChart->data_set[$i]['annualized_sales'] <= 59999 && isset($sugarChart->data_set[$i])) {
                        $data[2]['annualized_sales'] = '$25,000-$59,999';
                        $data[2]['monthly_sales'] += $sugarChart->data_set[$i]['monthly_sales'];
                        $data[2]['opp_count'] += $sugarChart->data_set[$i]['opp_count'];
                        $data[2]['total'] += $sugarChart->data_set[$i]['total'];
                        unset($sugarChart->data_set[$i]);
                    }
                    if($sugarChart->data_set[$i]['annualized_sales'] >= 60000 && $sugarChart->data_set[$i]['annualized_sales'] <= 99999 && isset($sugarChart->data_set[$i])) {
                        $data[3]['annualized_sales'] = '$60,000-$99,999';
                        $data[3]['monthly_sales'] += $sugarChart->data_set[$i]['monthly_sales'];
                        $data[3]['opp_count'] += $sugarChart->data_set[$i]['opp_count'];
                        $data[3]['total'] += $sugarChart->data_set[$i]['total'];
                        unset($sugarChart->data_set[$i]);
                    }
                    if($sugarChart->data_set[$i]['annualized_sales'] >= 100000 && $sugarChart->data_set[$i]['annualized_sales'] <= 249999 && isset($sugarChart->data_set[$i])) {
                        $data[4]['annualized_sales'] = '$100,000-$249,999';
                        $data[4]['monthly_sales'] += $sugarChart->data_set[$i]['monthly_sales'];
                        $data[4]['opp_count'] += $sugarChart->data_set[$i]['opp_count'];
                        $data[4]['total'] += $sugarChart->data_set[$i]['total'];
                        unset($sugarChart->data_set[$i]);
                    }
                    if($sugarChart->data_set[$i]['annualized_sales'] >= 250000 && $sugarChart->data_set[$i]['annualized_sales'] <= 499999 && isset($sugarChart->data_set[$i])) {
                        $data[5]['annualized_sales'] = '$250,000-$499,999';
                        $data[5]['monthly_sales'] += $sugarChart->data_set[$i]['monthly_sales'];
                        $data[5]['opp_count'] += $sugarChart->data_set[$i]['opp_count'];
                        $data[5]['total'] += $sugarChart->data_set[$i]['total'];
                        unset($sugarChart->data_set[$i]);
                    }
                    if($sugarChart->data_set[$i]['annualized_sales'] >= 500000 && $sugarChart->data_set[$i]['annualized_sales'] <= 999999 && isset($sugarChart->data_set[$i])) {
                        $data[6]['annualized_sales'] = '$500,000-$999,999';
                        $data[6]['monthly_sales'] += $sugarChart->data_set[$i]['monthly_sales'];
                        $data[6]['opp_count'] += $sugarChart->data_set[$i]['opp_count'];
                        $data[6]['total'] += $sugarChart->data_set[$i]['total'];
                        unset($sugarChart->data_set[$i]);
                    }
                    if($sugarChart->data_set[$i]['annualized_sales'] >= 1000000 && isset($sugarChart->data_set[$i])) {
                        $data[7]['annualized_sales'] = '$1,000,000 and above';
                        $data[7]['monthly_sales'] += $sugarChart->data_set[$i]['monthly_sales'];
                        $data[7]['opp_count'] += $sugarChart->data_set[$i]['opp_count'];
                        $data[7]['total'] += $sugarChart->data_set[$i]['total'];
                        unset($sugarChart->data_set[$i]);
                    }
                }
                unset($sugarChart->data_set);
                foreach($data as $key=>$value) { // update the keys in the array after the merger
                    $sugarChart->data_set[] = $value;
                }
            }
            $pipeline_total_string = translate('LBL_TOTAL_PIPELINE', 'Charts') . $sugarChart->currency_symbol . number_format(round($sugarChart->getTotal()),0,'.',',') . $sugarChart->thousands_symbol;
            $sugarChart->setProperties($pipeline_total_string, $subtitle, $chartDef['chartType']);
            $xmlFile = $sugarChart->getXMLFileName($this->id);
            $sugarChart->saveXMLFile($xmlFile, $sugarChart->generateXML());
        }
        $return_data = $this->getTitle('');
        $return_data .= '<div>'.$this->getButtonsData().'</div><br />';
        $return_data .= '<div align="center">' . $sugarChart->display($this->id, $xmlFile, '100%', '480', false) . '</div><br />';
        return $return_data;
    }

    /**
     * awu: Bug 16794 - this function is a hack to get the correct sales stage order until
     * i can clean it up later
     *
     * @param  $query string
     * @return array
     */
    function getButtonsData() {
        global $current_user, $app_list_strings;

        $this->ss = new Sugar_Smarty();

        $slsm_area_obj = new fmp_Param_RegLoc($current_user->id);
        $slsm_area_obj->init($current_user->id);
        $area_list_for_query = $slsm_area_obj->get_id_for_area($current_user->id);
        $area_list = $slsm_area_obj->html_for_daily_sales($current_user->id, '', 'pipeline_');
        unset($slsm_area_obj);

        $is_user_id = 0;
        $slsm_obj = new fmp_Param_SLSM($current_user->id);
        $slsm_obj->init();

        $is_s = $slsm_obj->is_assigned_slsm();
        $str_selection_button = '';
        if ($is_s) {
            if(isset($_POST['slsm_num']));
            $arr =  Array(0 => null);
            $r_users = $slsm_obj->compile__available_slsm($arr);
            $str_selection_button = $this->build__slsm($r_users, $is_user_id);
        }
        $slsm_tree_list = $slsm_obj->html_for_daily_sales('', 'pipeline_');  // prepeare SLSM list for display
        unset($slsm_obj);

        $dealer_list = $this->get_dealer_type($app_list_strings['fmp_dealertype_list']);
        $sales_reps_list = $this->get_sales_reps($current_user);

        $sales_reps_names = $current_user->getPreference('pipeline_sales_reps_names');
        $pipeline_slsm_num = $current_user->getPreference('pipeline_slsm_num');
        $pipeline_reg_loc = $current_user->getPreference('pipeline_reg_loc');
        $pipeline_dealer = $current_user->getPreference('pipeline_dealer');
        $cust_no = $current_user->getPreference('pipeline_parent_name_custno_c');
        $OPP_names =  $current_user->getPreference('OPP_opp_name');
        $title = '';

	if (!in_array($pipeline_reg_loc, array('all', 'undefined', '')) && is_array($pipeline_reg_loc)) { 
		foreach($pipeline_reg_loc as $reg_loc_value) {
			if(substr($reg_loc_value, 0, 1) == "r") {
				$pipeline_reg[substr($reg_loc_value, 1)] = substr($reg_loc_value, 1);
			} else {
				$pipeline_loc[$reg_loc_value] = $reg_loc_value;
			}
		}
	}

        $title .= $pipeline_reg_loc != 'undefined' && $pipeline_reg_loc != 'all' && (isset($pipeline_reg) || isset($pipeline_loc)) ? (isset($pipeline_reg) ? 'Region '.implode(', ', $pipeline_reg) : ''): '';
 	$title .= $pipeline_reg_loc != 'undefined' && $pipeline_reg_loc != 'all' && (isset($pipeline_reg) || isset($pipeline_loc)) ? (isset($pipeline_loc) ? 'Location '.implode(', ', $pipeline_loc) : ''): '';
        $title .= $pipeline_slsm_num != 'undefined' && $pipeline_slsm_num != 'all' ? (isset($pipeline_slsm_num) ? '/Slsm '.implode(" ", $pipeline_slsm_num) : 'Slsm '. $pipeline_slsm_num ):'';
        $title .= $pipeline_dealer != 'undefined' && $pipeline_dealer != 'all' && isset($pipeline_dealer) ? (($pipeline_slsm_num == 'undefined' || $pipeline_slsm_num == 'all') && ($pipeline_reg_loc == 'undefined' || $pipeline_reg_loc == 'all') ? 'Customer Type '.implode(" ", $pipeline_dealer) : '/Customer Type '.implode(" ", $pipeline_dealer)):'';
        $title .= isset($OPP_names) && $OPP_names[0] != '' && $OPP_names[0] != 'undefined' ? ' Opportunities '.implode(", ", $OPP_names) : '';
        /*$title .= $pipeline_reg_loc != 'undefined' && $pipeline_reg_loc != 'all' ? (substr($pipeline_reg_loc,0,1) == 'r' ? 'Region '.substr($pipeline_reg_loc,1) : 'Location '.$pipeline_reg_loc): '';
        $title .= $pipeline_slsm_num != 'undefined' && $pipeline_slsm_num != 'all' ? ($pipeline_reg_loc != 'all' && $pipeline_reg_loc != 'undefined' ? '/Slsm '.$pipeline_slsm_num : 'Slsm '.$pipeline_slsm_num):'';
        $title .= $pipeline_dealer != 'undefined' && $pipeline_dealer != 'all' ? (($pipeline_slsm_num == 'undefined' || $pipeline_slsm_num == 'all') && ($pipeline_reg_loc == 'undefined' || $pipeline_reg_loc == 'all') ? 'Customer Type '.$pipeline_dealer : '/Customer Type '.$pipeline_dealer):'';*/

        $title = $title == ''? 'All' :$title;

        if($title == 'Location /Slsm /Customer Type ') {
            $title = 'please, press "Reset" button';
        }
        $quicksearch_js = $this->get_quicksearch_js();
        $opp_name = isset($OPP_names) && $OPP_names[0] != '' && $OPP_names[0] != 'undefined' && count($OPP_names) == 1 ? implode(", ", $OPP_names):'';
        $this->ss->assign("areaList", $area_list);
        $this->ss->assign("slsmList", $slsm_tree_list);
        $this->ss->assign("dealerList", $dealer_list);
        $this->ss->assign("salesRepsList", $sales_reps_list);
        $this->ss->assign("pipeline_dashlet_title", $title);
        $this->ss->assign("sales_reps_name", 'Sales Reps: '.$sales_reps_names);
        $this->ss->assign("dashletId", $this->id);
        $this->ss->assign('quicksearch_js', $quicksearch_js);
        $this->ss->assign('opp_name', $opp_name);

        return $this->ss->fetch('modules/Charts/Dashlets/FMPPipelineBySalesStageDashlet/FMP_PipelineBySalesStageDashlet_templete.html');
    }

    private function getChartData($query, $selectParam) {
        global $app_list_strings, $db;

        $data = array();
        $temp_data = array();
        $selected_datax = array();
        $group_param = 'sales_stage';

        $result = $db->query($query);
        while($row = $db->fetchByAssoc($result, -1, false))
            $temp_data[] = $row;

       //pr($query);
       //pr('____________________________');
        switch ($selectParam[0]) {
            case 'sales_stage':
                $group_param = $selectParam[0];
                $user_sales_stage = $this->pbss_sales_stages;
                $tempx = $user_sales_stage;

                //set $datax using selected sales stage keys

                if (count($tempx) > 0) {
                    foreach ($tempx as $key) {
                        $datax[$key] = $app_list_strings['sales_stage_dom'][$key];
                        $selected_datax[] = $key;
                    }
                }
                else {
                    $datax = $app_list_strings['sales_stage_dom'];
                    $selected_datax = array_keys($app_list_strings['sales_stage_dom']);
                }
                break;
            case 'monthly_sales':
                $group_param = $selectParam[0];
                $selected_datax = array('$499 and under', '$500-$1000', '$1001-$1999', '$2000 and above');
                break;
            case 'annualized_sales':
                $group_param = $selectParam[0];
                $selected_datax = array('$5,999 and under', '$6,000-$24,999', '$25,000-$59,999', '$60,000-$99,999', '$100,000-$249,999', '$250,000-$499,999', '$500,000-$999,999', '$1,000,000 and above');
                break;
            case 'probability':
                $group_param = $selectParam[0];
                $selected_datax = array('0', '25', '50', '75', '100');
                break;
            case 'acc_name':
                $group_param = $selectParam[0];
                $qwe = count($temp_data);
                for($i=0; $i<$qwe; $i++) {
                    $selected_datax[] = $temp_data[$i]['acc_name'];
                }
                break;
            case 'opp_name':
                $group_param = $selectParam[0];
                $qwe = count($temp_data);
                for($i=0; $i<$qwe; $i++) {
                    $selected_datax[] = $temp_data[$i]['opp_name'];
                }
                break;
            default:
                $selected_datax = array_keys($app_list_strings['sales_stage_dom']);
                break;
        }

//         reorder and set the array based on the order of selected_datax
        foreach($selected_datax as $chart_type ) {
            foreach($temp_data as $key => $value) {
                if($group_param == 'monthly_sales') { // this condition is to group monthly_sales in the chartDashlet
                    if (($value[$group_param] <= 499 || $value[$group_param] == null) && $chart_type == '$499 and under') {
                        $value['value'] = $value[$group_param];
                        $value['key'] = $chart_type;
                        $value['monthly_sales'] = $chart_type;
                        $data[] = $value;
                        unset($temp_data[$key]);
                    }

                    if (($value[$group_param] >= 500) && ($value[$group_param] <= 1000) && $chart_type == '$500-$1000') {
                        $value['value'] = $value[$group_param];
                        $value['key'] = $chart_type;
                        $value['monthly_sales'] = $chart_type;
                        $data[] = $value;
                        unset($temp_data[$key]);
                    }

                    if (($value[$group_param] >= 1001) && ($value[$group_param] <= 1999) && $chart_type == '$1001-$1999') {
                        $value['value'] = $value[$group_param];
                        $value['key'] = $chart_type;
                        $value['monthly_sales'] = $chart_type;
                        $data[] = $value;
                        unset($temp_data[$key]);
                    }

                    if ($value[$group_param] >= 2000 && $chart_type == '$2000 and above') {
                        $value['value'] = $value[$group_param];
                        $value['key'] = $chart_type;
                        $value['monthly_sales'] = $chart_type;
                        $data[] = $value;
                        unset($temp_data[$key]);
                    }
                }elseif($group_param == 'probability') { // this condition is to group probability in the chartDashlet
                        if (($value[$group_param] == 0 || $value[$group_param] == null) && $chart_type == '0') {
                            $value[$group_param] = $chart_type.'%';
                            $value['value'] = $value[$group_param];
                            $value['key'] = $chart_type;
                            $data[] = $value;
                            unset($temp_data[$key]);
                        }

                        if (($value[$group_param] > 0) && ($value[$group_param] < 50) && $chart_type == '25') {
                            $value[$group_param] = $chart_type.'%';
                            $value['value'] = $value[$group_param];
                            $value['key'] = $chart_type;
                            $data[] = $value;
                            unset($temp_data[$key]);
                        }

                        if (($value[$group_param] >= 50) && ($value[$group_param] < 75) && $chart_type == '50') {
                            $value[$group_param] = $chart_type.'%';
                            $value['value'] = $value[$group_param];
                            $value['key'] = $chart_type;
                            $data[] = $value;
                            unset($temp_data[$key]);
                        }

                        if (($value[$group_param] >= 75) && ($value[$group_param] < 100) && $chart_type == '75') {
                            $value[$group_param] = $chart_type.'%';
                            $value['value'] = $value[$group_param];
                            $value['key'] = $chart_type;
                            $data[] = $value;
                            unset($temp_data[$key]);
                        }

                        if ($value[$group_param] == 100 && $chart_type == '100') {
                            $value[$group_param] = $chart_type.'%';
                            $value['value'] = $value[$group_param];
                            $value['key'] = $chart_type;
                            $data[] = $value;
                            unset($temp_data[$key]);
                        }
                }elseif($group_param == 'annualized_sales') {// this condition is to group annualized_sales in the chartDashlet
                    if (($value[$group_param] <= 5999 || $value[$group_param] == null) && $chart_type == '$5,999 and under') {
                        $value['value'] = $value[$group_param];
                        $value['key'] = $chart_type;
                        $value['annualized_sales'] = $chart_type;
                        $data[] = $value;
                        unset($temp_data[$key]);
                    }
                    if (($value[$group_param] >= 6000) && ($value[$group_param] <= 24999) && $chart_type == '$6,000-$24,999') {
                        $value['value'] = $value[$group_param];
                        $value['key'] = $chart_type;
                        $value['annualized_sales'] = $chart_type;
                        $data[] = $value;
                        unset($temp_data[$key]);
                    }
                    if (($value[$group_param] >= 25000) && ($value[$group_param] <= 59999) && $chart_type == '$25,000-$59,999') {
                        $value['value'] = $value[$group_param];
                        $value['key'] = $chart_type;
                        $value['annualized_sales'] = $chart_type;
                        $data[] = $value;
                        unset($temp_data[$key]);
                    }
                    if (($value[$group_param] >= 60000) && ($value[$group_param] <= 99999) && $chart_type == '$60,000-$99,999') {
                        $value['value'] = $value[$group_param];
                        $value['key'] = $chart_type;
                        $value['annualized_sales'] = $chart_type;
                        $data[] = $value;
                        unset($temp_data[$key]);
                    }
                    if (($value[$group_param] >= 100000) && ($value[$group_param] <= 249999) && $chart_type == '$100,000-$249,999') {
                        $value['value'] = $value[$group_param];
                        $value['key'] = $chart_type;
                        $value['annualized_sales'] = $chart_type;
                        $data[] = $value;
                        unset($temp_data[$key]);
                    }
                    if (($value[$group_param] >= 250000) && ($value[$group_param] <= 499999) && $chart_type == '$250,000-$499,999') {
                        $value['value'] = $value[$group_param];
                        $value['key'] = $chart_type;
                        $value['annualized_sales'] = $chart_type;
                        $data[] = $value;
                        unset($temp_data[$key]);
                    }
                    if (($value[$group_param] >= 500000) && ($value[$group_param] <= 999999) && $chart_type == '$500,000-$999,999') {
                        $value['value'] = $value[$group_param];
                        $value['key'] = $chart_type;
                        $value['annualized_sales'] = $chart_type;
                        $data[] = $value;
                        unset($temp_data[$key]);
                    }
                    if ($value[$group_param] >= 1000000 && $chart_type == '$1,000,000 and above') {
                        $value['value'] = $value[$group_param];
                        $value['key'] = $chart_type;
                        $value['annualized_sales'] = $chart_type;
                        $data[] = $value;
                        unset($temp_data[$key]);
                    }
                }
                else { // this condition for "sales_stage" and "probability" and "acc_name" and "opp_name"
                    if ($value[$group_param] == $chart_type) {
                        if($group_param == 'sales_stage') {
                            $value[$group_param] = $app_list_strings['sales_stage_dom'][$value['sales_stage']];
                        }
//                        elseif($group_param == 'probability') {
//                            $value[$group_param] = $chart_type.'%';
//                        }
                        else {
                            $value[$group_param] = $chart_type;
                        }
                        $value['key'] = $chart_type;
                        $value['value'] = $value[$group_param];
                        $data[] = $value;
                        unset($temp_data[$key]);
                    }
                }

//                if ($value['sales_stage'] == $sales_stage) {
//                    $value['sales_stage'] = $app_list_strings['sales_stage_dom'][$value['sales_stage']];
//                    $value['key'] = $sales_stage;
//                    $value['value'] = $value['sales_stage'];
//                    $data[] = $value;
//                    unset($temp_data[$key]);
//                }
            }
        }

        //pr($data);

        if($group_param == 'monthly_sales' || $group_param == 'annualized_sales') { //this condition is to group
            $temp_sales_stage_mas = array();
            $mass_count = count($data);
            for ($i=0; $i<$mass_count; $i++) {
                for($j=$i+1; $j<$mass_count; $j++) { // This condition for the unification of arrays with different conditions for group
                    if(isset($data[$i]) && isset($data[$j]) && $data[$i]['user_name'] == $data[$j]['user_name'] && $j!=$i  && $data[$i][$group_param] == $data[$j][$group_param]) {
                        $data[$i]['sales_stage'] = $data[$j]['sales_stage'];
                        $data[$i]['probability'] = $data[$j]['probability'];
                        $data[$i]['annualized_sales'] = $data[$j]['annualized_sales'];
                        $data[$i]['monthly_sales'] = $data[$j]['monthly_sales'];
                        $data[$i]['user_name'] = $data[$j]['user_name'];
                        $data[$i]['assigned_user_id'] = $data[$j]['assigned_user_id'];
                        $data[$i]['opp_count'] += $data[$j]['opp_count'];
                        $data[$i]['total'] += $data[$j]['total'];
                        $data[$i]['value'] += $data[$j]['value'];
                        $data[$i]['key'] = $data[$j]['key'];
                        unset($data[$j]);
                    }
                }
            }
            foreach($data as $value) { // update the keys in the array after the merger
                $temp_sales_stage_mas[] = $value;
            }
            

            return $temp_sales_stage_mas;
        }

        if($group_param == 'probability') { //this condition is to group
            $ready_probability_array= array();
            $mass_count = count($data);
            for ($i=0; $i<$mass_count; $i++) {
                for($j=$i+1; $j<$mass_count; $j++) { // This condition for the unification of arrays with different conditions for group
                    if(isset($data[$i]) && isset($data[$j]) && $data[$i]['user_name'] == $data[$j]['user_name'] && $j!=$i  && $data[$i][$group_param] == $data[$j][$group_param]) {
                        $data[$i]['opp_count'] += $data[$j]['opp_count'];
                        $data[$i]['total'] += $data[$j]['total'];
                        unset($data[$j]);
                    }
                }
            }
            foreach($data as $value) { // update the keys in the array after the merger
                $ready_probability_array[] = $value;
            }
            

            return $ready_probability_array;
        }
        return $data;
    }

    /**
     * @see DashletGenericChart::constructQuery()
     */
    protected function constructQuery($selectParam, $group_by_pie = false) {
        global $current_user, $app_list_strings;
        $group_by = '';
        $additional_params = '';
        switch ($selectParam[0]) {
            case 'sales_stage':
                if($group_by_pie) {
                    $group_by = ' opportunities.sales_stage, users.user_name, opportunities.assigned_user_id ';
                }
                else {
                    $group_by = ' opportunities.sales_stage ORDER BY total DESC ';
                }
                break;
            case 'monthly_sales':
                if($group_by_pie) {
                    $group_by = ' monthly_sales, users.user_name, opportunities.assigned_user_id ';
                }else {
                    $group_by = ' monthly_sales ORDER BY total DESC ';
                }
                break;
            case 'annualized_sales':
                if($group_by_pie) {
                    $group_by = ' annualized_sales, users.user_name, opportunities.assigned_user_id ';
                }else {
                    $group_by = ' annualized_sales ORDER BY total DESC ';
                }
                break;
            case 'probability':
                if($group_by_pie) {
                    $group_by = ' opportunities.probability, users.user_name, opportunities.assigned_user_id ';
                }else {
                    $group_by = ' opportunities.probability ORDER BY total DESC ';
                }
                break;
            case 'acc_name':
                if($group_by_pie) {
                    $group_by = ' acc_name, users.user_name, opportunities.assigned_user_id ';
                }else {
                    $group_by = ' acc_name ORDER BY total DESC ';
                }
                break;
            case 'opp_name':
                if($group_by_pie) {
                    $group_by = ' opp_name, users.user_name, opportunities.assigned_user_id ';
                }else {
                    $group_by = ' opp_name ORDER BY total DESC ';
                }
                break;
            default:
                if($group_by_pie) {
                    $group_by = ' opportunities.sales_stage, users.user_name, opportunities.assigned_user_id ';
                }else {
                    $group_by = ' opportunities.sales_stage ORDER BY total DESC ';
                }
                break;
        }

//        $query = "  SELECT
//                        accounts.name AS acc_name,
//                        opportunities.probability,
//                        opportunities.name AS opp_name,
//                        opportunities.sales_stage,
//                        opportunities.amount_usdollar AS annualized_sales,
//                        opportunities.amount_usdollar/12 AS monthly_sales,
//                        users.user_name,
//                        opportunities.assigned_user_id,
//                        count( * ) AS opp_count,
//                        sum(amount_usdollar) AS total
//                    FROM opportunities, accounts_opportunities, accounts, users ";
//
//        $query .= " WHERE opportunities.date_closed >= ". db_convert("'".$this->pbss_date_start."'",'datetime').
//                " AND opportunities.date_closed <= ".db_convert("'".$this->pbss_date_end."'",'datetime') .
//                " AND opportunities.assigned_user_id = users.id AND opportunities.deleted=0 ".
//                " AND opportunities.id=accounts_opportunities.opportunity_id ".
//                " AND accounts_opportunities.account_id=accounts.id ".
//                " AND accounts.deleted=0 ";

 $query = "  SELECT
                        accounts.name AS acc_name,
                        opportunities.probability,
                        opportunities.name AS opp_name,
                        opportunities.sales_stage,
                        opportunities.amount_usdollar AS annualized_sales,
                        opportunities.amount_usdollar/12 AS monthly_sales,
                        users.user_name,
                        opportunities.assigned_user_id,
                        count( * ) AS opp_count,
                        sum(amount_usdollar) AS total
              FROM      opportunities
              LEFT JOIN opportunities_cstm ON opportunities.id = opportunities_cstm.id_c
              LEFT JOIN  accounts_opportunities  ON opportunities.id=accounts_opportunities.opportunity_id  AND accounts_opportunities.deleted=0
              LEFT JOIN  accounts ON accounts.id=accounts_opportunities.account_id AND accounts.deleted=0
              LEFT JOIN users ON opportunities.assigned_user_id = users.id 
              LEFT JOIN dsls_slsm_combined AS d_c ON (d_c.slsm = accounts.slsm_c) ";
$date_start_unix = strtotime($this->pbss_date_start);
$date_end_unix = strtotime($this->pbss_date_end);

  $query .= " WHERE UNIX_TIMESTAMP(opportunities.date_closed) >=  '".$date_start_unix."' ".
                " AND UNIX_TIMESTAMP(opportunities.date_closed) <=  '".$date_end_unix."' ";
    if($this->promo_opp){
      $date_start_S_unix = strtotime($this->pbss_date_start_S);
       $date_end_S_unix = strtotime($this->pbss_date_end_S);

      $query .= " AND UNIX_TIMESTAMP(opportunities_cstm.date_start) >=  '".$date_start_S_unix."' ".
                    " AND UNIX_TIMESTAMP(opportunities_cstm.date_start) <=  '".$date_end_S_unix."' ";
    }

        if ( count($this->pbss_sales_stages) > 0 ) {
            $query .= " AND opportunities.sales_stage IN ('" . implode("','",$this->pbss_sales_stages) . "') ";
        }
//        if ( count($this->pbss_probability) > 0) {
//            $query .= ' AND opportunities.probability IN ("' . implode('", "',$this->pbss_probability) . '")';
//        }
        if ( count($this->pbss_probability) > 0 ) {
            $probability_query = '';
            $probability_query = ' AND(';
            $probability_array = array();
            foreach($this->pbss_probability as $probability_val) {
                switch ($probability_val) {
                    case 0:
                        $probability_array[] = ' (opportunities.probability = 0 OR opportunities.probability IS NULL OR opportunities.probability="") ';
                        break;
                    case 25:
                        $probability_array[] = ' (opportunities.probability >= 1 AND opportunities.probability <= 49) ';
                        break;
                    case 50:
                        $probability_array[] = ' (opportunities.probability >= 50 AND opportunities.probability <= 74) ';
                        break;
                    case 75:
                        $probability_array[] = ' (opportunities.probability >= 75 AND opportunities.probability <= 99) ';
                        break;
                    case 100:
                        $probability_array[] = ' (opportunities.probability = 100) ';
                        break;
                }
            }
            $probability_query .= implode("OR",$probability_array);
            $probability_query .=') ';
            $query .= $probability_query;
        }
        
        if(is_array($this->pbss_company) && !empty($this->pbss_company)) {
            if (in_array(0, $this->pbss_company) && !in_array(1, $this->pbss_company)) {
                $rqs_company = " AND d_c.company = 'FMP' ";
                $query .= $rqs_company;
            } 
            if (in_array(1, $this->pbss_company) && !in_array(0, $this->pbss_company)) {
                $rqs_company = " AND d_c.company = 'Splash' ";
                 $query .= $rqs_company;
            }
        }
        
         if(is_array($this->pbss_opp_type) && !empty($this->pbss_opp_type)&& count($this->pbss_opp_type) > 0) {
                $query .= " AND opportunities.opportunity_type IN(\"".implode('", "', $this->pbss_opp_type)."\") ";
        }
        
        if ( count($this->pbss_estimated_monthly_sales) > 0 ) {
            $monthly_sales_query = '';
            $monthly_sales_query = ' AND (';
            $monthly_sales_array = array();
            foreach($this->pbss_estimated_monthly_sales as $monthly_sales_val) {
                switch ($monthly_sales_val) {
                    case 0:
                        $monthly_sales_array[] = ' (opportunities.amount_usdollar/12 < 500 OR opportunities.amount_usdollar IS NULL OR opportunities.amount_usdollar="") ';
                        break;
                    case 1:
                        $monthly_sales_array[] = ' (opportunities.amount_usdollar/12 >= 500 AND opportunities.amount_usdollar/12 < 1001) ';
                        break;
                    case 2:
                        $monthly_sales_array[] = ' (opportunities.amount_usdollar/12 >= 1001 AND opportunities.amount_usdollar/12 < 2000) ';
                        break;
                    case 3:
                        $monthly_sales_array[] = ' (opportunities.amount_usdollar/12 >= 2000) ';
                        break;
                }
            }
            $monthly_sales_query .= implode("OR",$monthly_sales_array);
            $monthly_sales_query .=') ';
            $query .= $monthly_sales_query;
        }
        if ( count($this->pbss_estimated_annualized_sales) > 0 ) {
            $estimated_annualized_sales_query = '';
            $estimated_annualized_sales_query = ' AND (';
            $estimated_annualized_sales_array = array();
            foreach($this->pbss_estimated_annualized_sales as $monthly_sales_val) {
                switch ($monthly_sales_val) {
                    case 0:
                        $estimated_annualized_sales_array[] = ' (opportunities.amount_usdollar < 6000 OR opportunities.amount_usdollar IS NULL OR opportunities.amount_usdollar="") ';
                        break;
                    case 1:
                        $estimated_annualized_sales_array[] = ' (opportunities.amount_usdollar >= 6000 AND opportunities.amount_usdollar < 25000) ';
                        break;
                    case 2:
                        $estimated_annualized_sales_array[] = ' (opportunities.amount_usdollar >= 25000 AND opportunities.amount_usdollar < 60000) ';
                        break;
                    case 3:
                        $estimated_annualized_sales_array[] = ' (opportunities.amount_usdollar >= 60000 AND opportunities.amount_usdollar < 100000) ';
                        break;
                    case 4:
                        $estimated_annualized_sales_array[] = ' (opportunities.amount_usdollar >= 100000 AND opportunities.amount_usdollar < 250000) ';
                        break;
                    case 5:
                        $estimated_annualized_sales_array[] = ' (opportunities.amount_usdollar >= 250000 AND opportunities.amount_usdollar < 500000) ';
                        break;
                    case 6:
                        $estimated_annualized_sales_array[] = ' (opportunities.amount_usdollar >= 500000 AND opportunities.amount_usdollar < 1000000) ';
                        break;
                    case 7:
                        $estimated_annualized_sales_array[] = ' (opportunities.amount_usdollar >= 1000000) ';
                        break;
                }
            }
            $estimated_annualized_sales_query .= implode("OR",$estimated_annualized_sales_array);
            $estimated_annualized_sales_query .=') ';
            $query .= $estimated_annualized_sales_query;
        }

        /*Conditions from Buttons*/
        $returnArray = array();
        $pipeline_reg = '';
        $pipeline_loc = '';
        $slsmqry = '';
        $regqry = '';
        $locqry = '';
        $allarea = '';
        $dealerqry = '';
        $pipe_parent_name_custno_c = '';

        

        $pipeline_slsm_num = ($current_user->getPreference('pipeline_slsm_num'))? $current_user->getPreference('pipeline_slsm_num'): 'all';
        $pipeline_reg_loc = ($current_user->getPreference('pipeline_reg_loc'))? $current_user->getPreference('pipeline_reg_loc'): 'all';
        $pipeline_dealer = ($current_user->getPreference('pipeline_dealer'))? $current_user->getPreference('pipeline_dealer'): 'all';
        $pipeline_sales_reps = ($current_user->getPreference('pipeline_sales_reps'))?$current_user->getPreference('pipeline_sales_reps'): 'all';
        //$pipeline_sales_reps = $current_user->getPreference('pipeline_sales_reps');
        $pipe_parent_name_custno_c = $current_user->getPreference('pipeline_parent_name_custno_c');
        $pipe_parent_name = $current_user->getPreference('pipeline_parent_name');
        $OPP_names =  $current_user->getPreference('OPP_opp_name');
        


        if(isset($pipeline_sales_reps) && $pipeline_sales_reps != '' && $pipeline_sales_reps != null && $pipeline_sales_reps!= 'all') {
            foreach ($pipeline_sales_reps as $v) {
                $compiled_sale_reps[] = "'$v'";
            }
            $sales_reps = ' opportunities.assigned_user_id IN (' . implode(', ', $compiled_sale_reps) . ') ';
            array_push($returnArray, $sales_reps);
        }

//        if($pipeline_sales_reps == 'all'){
//                   $o = new fmp_Param_SLSM($current_user->id);
//                   $o->init();
//                   $pipeline_sales_reps = $o->get_sales_reps_array();
//                   unset($o);
//                   foreach ($pipeline_sales_reps as $k=>$v) {
//                        $compiled_sale_reps[] = "'$k'";
//                   }
//                   $sales_reps = ' opportunities.assigned_user_id IN (' . implode(', ', $compiled_sale_reps) . ') ';
//                   array_push($returnArray, $sales_reps);
//         }
//
//        if ($pipeline_reg_loc == 'all') {
//            $slsm_area_obj = new fmp_Param_RegLoc($current_user->id);
//            $slsm_area_obj->init($current_user->id);
//            $area_list = $slsm_area_obj->get_id_for_area($current_user->id);
//            unset($slsm_area_obj);
//            if (count($area_list)>0) {
//                $regIn = array();
//                $locIn = array();
//                foreach ($area_list as $reg => $loc) {
//                    $regIn[] = $reg;
//                    foreach ($loc['locs'] as $key => $value) {
//                        $locIn[] = $key;
//                    }
//
//                }
//
//                $allarea .= ' (accounts.region_c IN (' . implode(', ', $regIn) . ') AND accounts.location_c IN (' . implode(', ', $locIn) . ')) ';
//                array_push($returnArray, $allarea);
//            }
//        }
//
//	if (!in_array($pipeline_reg_loc, array('all', 'undefined', '')) && is_array($pipeline_reg_loc)) { 
//		foreach($pipeline_reg_loc as $reg_loc_value) {
//			if(substr($reg_loc_value, 0, 1) == "r") {
//				$pipeline_reg[substr($reg_loc_value, 1)] = substr($reg_loc_value, 1);
//			} else {
//				$pipeline_loc[$reg_loc_value] = $reg_loc_value;
//			}
//		}
//	}

        /*if (substr($pipeline_reg_loc,0,1) == 'r') {
            $pipeline_reg = substr($pipeline_reg_loc,1) ;
        }
        if(is_numeric($pipeline_reg_loc)) {
            $pipeline_loc = $pipeline_reg_loc;
        }*/


        if (isset($pipeline_reg) && $pipeline_reg != '' && $pipeline_reg != 'undefined' && $pipeline_reg != 'all' && count($pipeline_reg) > 0) {
            $regqry .= " (accounts.region_c IN (".implode(', ', $pipeline_reg).")) ";
            array_push($returnArray, $regqry);
        }

        if (isset($pipeline_loc) && $pipeline_loc != '' && $pipeline_loc != 'undefined' && $pipeline_loc != 'all' && count($pipeline_loc) > 0) {
            $locqry .= " (accounts.location_c IN (".implode(', ', $pipeline_loc).")) ";
            array_push($returnArray, $locqry);
        }
        if (isset($pipeline_dealer) && $pipeline_dealer != '' && $pipeline_dealer != 'undefined' && $pipeline_dealer != 'all' && count($pipeline_dealer) > 0) {
            $dealer_list = array();
            if($pipeline_dealer != 'all') {
                $dealerqry .= " (accounts.dealertype_c IN (".implode(', ', $pipeline_dealer).")) ";
                array_push($returnArray, $dealerqry);
            }else {
                if (count($app_list_strings['fmp_dealertype_list'])>0) {
                    foreach($app_list_strings['fmp_dealertype_list'] as $key=>$value) {
                        if($key != '' && $key != null) {
                            $dealer_list[] = $key;
                        }
                    }
                    $dealerqry = " (accounts.dealertype_c IN (" . implode(", ", $dealer_list) . ")) ";
                }
                array_push($returnArray, $dealerqry);
            }
        }

        if (isset($pipeline_slsm_num) && $pipeline_slsm_num != '' && $pipeline_slsm_num != 'undefined' ) {

            $is_user_id = 0;
            $slsm_obj = new fmp_Param_SLSM($current_user->id);
            $slsm_obj->init();

            $is_s = $slsm_obj->is_assigned_slsm();
            if ($is_s) {
                if(isset($pipeline_slsm_num) && $pipeline_slsm_num != '' && $pipeline_slsm_num != 'undefined' && $pipeline_slsm_num != 'all') {
                    $arr =  $pipeline_slsm_num;
                }else {
                    $arr =  Array(0 => null);
                }
                $r_users = $slsm_obj->compile__available_slsm($arr);
                $str_selection_button = $this->build__slsm($r_users, $is_user_id);
                $slsmqry .= " (accounts.slsm_c ".$str_selection_button.") ";
                array_push($returnArray, $slsmqry);
            }
            unset($slsm_obj);
        }
        
        if(isset($OPP_names) && $OPP_names[0] != '' && $OPP_names[0] != 'undefined' && count($OPP_names) == 1){
            $nameqry = " opportunities.name like '%".$OPP_names[0]."%' ";
            array_push($returnArray, $nameqry);
        }elseif(isset($OPP_names) && $OPP_names[0] != '' && $OPP_names[0] != 'undefined' && count($OPP_names) > 1){
            $nameqry = " (";
            foreach($OPP_names as $name){
                if (!next($OPP_names)) {
                    $nameqry .= " opportunities.name like '%".$name."%') ";
                }else{
                    $nameqry .= " opportunities.name like '%".$name."%' OR ";
                }
            }
            array_push($returnArray, $nameqry);
        }
        
        if(count($returnArray) > 0) {
            $query .= ' AND '. implode("AND",$returnArray);
        }

        /*end Conditions from Buttons*/
//echo'<pre>';print_r($query);echo'</pre>';
        $query .= " GROUP BY ". $group_by;
        return $query;
    }

    /**
     * @see DashletGenericChart::constructGroupBy()
     */
    protected function constructGroupBy($selectParam) {
        switch ($selectParam[0]) {
            case 'sales_stage':
                return array(
                        'sales_stage',
                        'user_name'
                );
                break;
            case 'monthly_sales':
                return array(
                        'monthly_sales',
                        'user_name'
                );
                break;
            case 'annualized_sales':
                return array(
                        'annualized_sales',
                        'user_name'
                );
                break;
            case 'probability':
                return array(
                        'probability',
                        'user_name',
                );
                break;
            case 'acc_name':
                return array(
                        'acc_name',
                        'user_name',
                );
                break;
            case 'opp_name':
                return array(
                        'opp_name',
                        'user_name',
                );
                break;
            default:
                return array(
                        'sales_stage',
                        'user_name',
                );
                break;
        }
    }

    function build__slsm($compiled_slsm, $is_user_id) {
        foreach ($compiled_slsm as $k=>$v) {
            $compiled_slsm[$k] = "'$v'";
        }

        $h = ''
                . ' IN (' . implode(', ', $compiled_slsm) . ') '
        ;
        return $h;
    }

    function get_dealer_type ($dealer_list) {
        $select_creater = '<select id="get_date_for_pipeline" size="10" multiple="multiple" style="width: 170px;">';
        $select_creater .= '<option value="all" style="border-bottom: 2px solid grey;">ALL</option>';
        foreach ($dealer_list as $key=>$value) {
            //if($key!='') {
                $select_creater .= '<option value="'.$key.'">'.$value.'</option>';
            //}
        }
        $select_creater .= '</select>';
        return $select_creater;
    }

    function get_sales_reps ($current_user) {
        $str = '<select id="pipeline_sales_reps_list" size="10" multiple="multiple" style="width: 170px;">';
        $ids = array($current_user->id);
        $o = new fmp_Param_SLSM($current_user->id);
        $o->init();
        $str .= get_select_options_with_id($o->get_sales_reps_array(), '');
        unset($o);
        $str .= '</select>';
        return $str;
    }
    
    function get_quicksearch_js () {
        require_once 'include/QuickSearchDefaults.php';
        $qsd = new QuickSearchDefaults();

        $o = $qsd->getQSParent('Opportunities');
        $o['field_list'] = array('name','id');
        $o['populate_list'] = array('fmp-oppchart-name','fmp-oppchart-id');
        $o['conditions'] = array(array('name'=>'name','op'=>'like_custom','begin'=>'%','end'=>'%','value'=>''));
        $sqs_objects['fmp-oppchart-name'] = $o;
        
        $json = getJSONobj();
        $quicksearch_js = array();
        foreach ($sqs_objects as $sqsfield => $sqsfieldArray) {
                $quicksearch_js[] = "sqs_objects['$sqsfield']={$json->encode($sqsfieldArray)};";
        }
        $quicksearch_js = ''
                . '<script language="javascript">'
                . "if(typeof sqs_objects == 'undefined'){var sqs_objects = new Array;}"
                . implode("\n", $quicksearch_js)
                . '</script>';
        return $quicksearch_js;
    }
}
