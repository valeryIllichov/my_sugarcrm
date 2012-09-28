<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');
/*
 *
 */

require_once('include/Dashlets/DashletGeneric.php');
require_once('modules/DSls_DailySales/DSls_DailySales.php');
require_once('include/Sugar_Smarty.php');
require_once('modules/ZuckerReportParameter/fmp.class.param.slsm.php');
require_once('modules/ZuckerReportParameter/fmp.class.param.regloc.php');
require_once('modules/ZuckerQueryTemplate/QueryTemplate.php');
require_once('include/database/PearDatabase.php');

//require_once('521/FMPSales.php');


class FMP_DSls_DailySalesDashlet extends DashletGeneric {

    protected $ss;
    var $configureTpl = 'include/Dashlets/DashletGenericConfigure.tpl';
    var $personal_filter =true;
    var $saved_option = false;
    function FMP_DSls_DailySalesDashlet($id, $def = null) {
        global $current_user, $app_strings;
        require('modules/DSls_DailySales/metadata/dashletviewdefs.php');

        parent::DashletGeneric($id, $def);
        $def['saved_option'] = ($_REQUEST['action'] == 'index') ? false:$def['saved_option'];
        if (empty($def['title']))
            $this->title = translate('LBL_FMP_HOMEPAGE_TITLE', 'DSls_DailySales');
        if(!empty($def['personal_filter']))
            $this->personal_filter = $def['personal_filter']; 
        
        if(!empty($def['saved_option']))
            $this->saved_option = $def['saved_option']; 
        $this->searchFields = $dashletData['DSls_DailySalesDashlet']['searchFields'];
        //print_r($dashletData);
        unset($dashletData['DSls_DailySalesDashlet']['columns']);

        $dashletData['DSls_DailySalesDashlet']['columns'] = array(
            'current_day_sales' => array(
                'label' => 'Current Day Sales',
                'default' => 1,
            ),
            'current_day_credits' => array(
                'label' => 'Current Day Credits',
                'default' => 1,
            ),
            'current_day_net_sales' => array(
                'label' => 'Current Day Net Sales',
                'default' => 1,
            ),
///////////////////////////////////////////////////
            'previous_day_sales' => array(
                'label' => 'Previous Day\'s Sales',
                'default' => 1,
            ),
            'previous_day_credits' => array(
                'label' => 'Previous Day\'s Credits',
                'default' => 1,
            ),
            'previous_day_net_sales' => array(
                'label' => 'Previous Day\'s Net Sales',
                'default' => 1,
            ),
///////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

            'daily_target' => array(
                'label' => 'Daily Sales Target',
                'default' => 1,
            ),
            'daily_average_target' => array(
                'label' => 'Daily Sales Average Target',
                'default' => 1,
            ),
///////////////////////////////////////////////////
            'daily_target_gp' => array(
                'label' => 'Daily GP$ Target',
                'default' => 1,
            ),
            'daily_average_target_gp' => array(
                'label' => 'Daily Average GP$ Target',
                'default' => 1,
            ),
 ////////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\	
            'current_month_proj' => array(
                'label' => 'Current Month Projected Sales',
                'default' => 1,
            ),
            'current_month_proj_gp' => array(
                'label' => 'Current Month Projected GP$',
                'default' => 1,
            ),       
 ///////////////////////////////////////////////////////           
             'cm_proj_gp_vs_budget_gp' => array(
                'label' => 'CM Proj GP$ vs. CM GP$ Budget',
                'default' => 1,
            ),
            'cm_proj_vs_budget' => array(
                'label' => 'Current Month Proj vs. Budget',
                'default' => 1,
            ),
///////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\	

            'mtd_sales_total' => array(
                'label' => 'MTD Sales Total',
                'default' => 1,
            ),
            'pending_orders' => array(
                'label' => 'Pending Orders',
                'default' => 1,
            ),
            'pending_credits' => array(
                'label' => 'Pending Credits',
                'default' => 1,
            ),
///////////////////////////////////////////////////
            'previous_day_invoiced_sales' => array(
                'label' => 'Previous Day\'s Invoiced Sales',
                'default' => 1,
            ),
            'mly_sales_invoiced' => array(
                'label' => 'CM Proj Sls vs. LM Sls',
                'default' => 1,
            ),
            'lytd_sales_invoiced' => array(
                'label' => 'CY Proj Sls vs. LY Sls Invoiced',
                'default' => 1,
            ),
///////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\	

           
            'current_month_sales_budget' => array(
                'label' => 'Current Month Sales Budget',
                'default' => 1,
            ),
            'cm_gp_budget' => array(
                'label' => 'CM GP$ Budget',
                'default' => 1,
            ),
            'cm_gpp_budget' => array(
                'label' => 'CM GP% Budget',
                'default' => 1,
            ),
///////////////////////////////////////////////////

            'att_sales_to_month_budget' => array(
                'label' => '% of Attainment MTD Sls to CM Sls Budget',
                'default' => 1,
            ),
            'att_gp_to_month_budget' => array(
                'label' => '% of Attainment MTD GP$ to CM GP$ Budget',
                'default' => 1,
            ),
            'att_gpp_to_month_budget_gpp' => array(
                'label' => 'MTD GP% vs. CM GP% Budget',
                'default' => 1,
            ),
///////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\	

            'current_month_sales' => array(
                'label' => 'MTD Sales Invoiced',
                'default' => 1,
            ),
            'current_month_gp' => array(
                'label' => 'MTD GP$',
                'default' => 1,
            ),
            'current_month_gpp' => array(
                'label' => 'MTD GP%',
                'default' => 1,
            ),
///////////////////////////////////////////////////
            'last_month_sales' => array(
                'label' => 'Last Month\'s Sales',
                'default' => 1,
            ),
            'last_month_gp' => array(
                'label' => 'Last Month\'s GP$',
                'default' => 1,
            ),
            'last_month_gpp' => array(
                'label' => 'Last Month\'s GP%',
                'default' => 1,
            ),
///////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
            'current_year_sales_proj' => array(
                'label' => 'Current Year Projected Sales',
                'default' => 1,
            ),    
             'current_year_gp_proj' => array(
                'label' => 'Current Year Projected GP$',
                'default' => 1,
            ),  
 ///////////////////////////////////////////////////
            'current_sales_proj_vs_ly_sales_invoiced' => array(
                'label' => 'CY Proj Sls vs. CY Sls Budget',
                'default' => 1,
            ),
            'current_gp_proj_vs_ly_gp_invoiced' => array(
                'label' => 'CY Proj GP$ vs. CY GP$ Budget',
                'default' => 1,
            ),
///////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\	



            'current_year_sales_budget' => array(
                'label' => 'Current Year Sales Budget',
                'default' => 1,
            ),
            'cy_gp_budget' => array(
                'label' => 'Current Year GP$ Budget',
                'default' => 1,
            ),
            'cy_gpp_budget' => array(
                'label' => 'Current Year GP% Budget',
                'default' => 1,
            ),
///////////////////////////////////////////////////

            'att_year_sls_proj_vs_cy_sales_budget' => array(
                'label' => '% of Attainment Year Sls Proj vs. CY Sales Budget',
                'default' => 1,
            ),
            'att_year_gp_proj_vs_cy_gp_budget' => array(
                'label' => '% of Attainment Year GP$ Proj vs. CY GP$ Budget',
                'default' => 1,
            ),
             'att_y_gpp_proj_vs_cy_gpp_budget' => array(
                'label' => 'YTD GP% vs. CY GP% Budget',
                'default' => 1,
            ),
///////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\	



            'ytd_sales_invoiced' => array(
                'label' => 'YTD Sales Invoiced',
                'default' => 1,
            ),
            'ytd_gp' => array(
                'label' => 'YTD GP$',
                'default' => 1,
            ),
            'ytd_gpp' => array(
                'label' => 'YTD GP%',
                'default' => 1,
            ),
///////////////////////////////////////////////////
            'ly_sales_invoiced' => array(
                'label' => 'LY Sales Invoiced',
                'default' => 1,
            ),
            'ly_gp' => array(
                'label' => 'LY GP$',
                'default' => 1,
            ),
            'ly_gpp' => array(
                'label' => 'LY GP%',
                'default' => 1,
            ),
///////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\	
//NOEM
            'mtd_sls_noem' => array(
                'label' => 'Mtd Non-OE Sales',
                'default' => 1,
            ),
            'ytd_sls_noem' => array(
                'label' => 'YTD Non-OE Sales',
                'default' => 1,
            ),
            'mtd_gp_noem' => array(
                'label' => 'Mtd Non-OE GP',
                'default' => 1,
            ),
            'ytd_gp_noem' => array(
                'label' => 'Ytd Non-OE GP',
                'default' => 1,
            ),
            'mtd_gpp_noem' => array(
                'label' => 'Mtd Non-OE GP %',
                'default' => 1,
            ),
            'ytd_gpp_noem' => array(
                'label' => 'Ytd Non-OE GP %',
                'default' => 1,
            ),
            'mtd_budget_noem_sales' => array(
                'label' => 'Mtd Non-OE Bgt Sls',
                'default' => 1,
            ),
            'ytd_budget_noem_sales' => array(
                'label' => 'Ytd Non-OE Bgt-Sls',
                'default' => 1,
            ),
           'mtd_projected_noem' => array(
                'label' => 'Mtd Non-OE Projected',
                'default' => 1,
            ),
            'ytd_projected_noem' => array(
                'label' => 'Ytd Non-OE Projected',
                'default' => 1,
            ),
            'ly_sls_noem' => array(
                'label' => 'Ly Non-OE Sales',
                'default' => 1,
            ),
           'ly_gpp_noem' => array(
                'label' => 'Ly Non-OE GP %',
                'default' => 1,
            ),
 ////////////////////////////////////////////////

            'mtd_sls_noem_divide_mtd_sales' => array(
                'label' => 'MTD % of NOE Sales vs. Total Sales',
                'default' => 1,
            ),   
                      'mtd_gp_noem_divide_mtd_gp' => array(
                'label' => 'MTD % of NOE GP vs. Total GP',
                'default' => 1,
            ),   
                    'mtd_gpp_noem_divide_mtd_gpp' => array(
                'label' => 'MTD NOE GP % vs. Total GP %',
                'default' => 1,
            ),
                    'mtd_budget_noem_sales_divide_mtd_budget_sales' => array(
                'label' => 'MTD % of NOE Bgt Sls vs. Total Bgt Sls',
                'default' => 1,
            ),
                     'mtd_projected_noem_divide_mtd_projected' => array(
                'label' => 'MTD % of NOE Projected vs. Total Projected',
                'default' => 1,
            ),   
                         'ly_sls_noem_divide_ly_sales' => array(
                'label' => 'LY % of NOE Sales vs. Total Sales',
                'default' => 1,
            ),
                   
                    'ytd_sls_noem_divide_ytd_sales' => array(
                'label' => 'YTD % of NOE Sales vs. Total Sales',
                'default' => 1,
            ),
                    'ytd_gp_noem_divide_ytd_gp' => array(
                'label' => 'YTD % of NOE GP vs. Total GP',
                'default' => 1,
            ),
                    'ytd_gpp_noem_divide_ytd_gpp' => array(
                'label' => 'YTD NOE GP % vs. Total GP %',
                'default' => 1,
            ),
                      'ytd_budget_noem_sales_divide_ytd_sales_budget' => array(
                'label' => 'YTD % of NOE Bgt Sls vs. Total Bgt Sls',
                'default' => 1,
            ),   
                    'ytd_projected_noem_divide_ytd_projected' => array(
                'label' => 'YTD % of NOE Projected vs. Total Projected',
                'default' => 1,
            ),
                    'ly_gpp_noem_divide_ly_gpp' => array(
                'label' => 'LY NOE GP % vs. Total GP %',
                'default' => 1,
            ),
///////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
//UNDERCAR
            'mtd_sls_undercar' => array(
                'label' => 'Mtd UnderCar Sales',
                'default' => 1,
            ),
            'ytd_sls_undercar' => array(
                'label' => 'Ytd UnderCar Sales',
                'default' => 1,
            ),
            'mtd_gp_undercar' => array(
                'label' => 'Mtd UnderCar GP',
                'default' => 1,
            ),
            'ytd_gp_undercar' => array(
                'label' => 'Ytd UnderCar GP',
                'default' => 1,
            ),
            'mtd_gpp_undercar' => array(
                'label' => 'Mtd UnderCar GP %',
                'default' => 1,
            ),
            'ytd_gpp_undercar' => array(
                'label' => 'Ytd UnderCar GP %',
                'default' => 1,
            ),
            'mtd_budget_undercar_sales' => array(
                'label' => 'Mtd UnderCar Bgt Sls',
                'default' => 1,
            ),
            'ytd_budget_undercar_sales' => array(
                'label' => 'Ytd UnderCar Bgt Sls',
                'default' => 1,
            ),
            'mtd_projected_undercar' => array(
                'label' => 'Mtd UnderCar Projected',
                'default' => 1,
            ),
            'ytd_projected_undercar' => array(
                'label' => 'Ytd UnderCar Projected',
                'default' => 1,
            ),
            'ly_sls_undercar' => array(
                'label' => 'Ly UnderCar Sales',
                'default' => 1,
            ),
            'ly_gpp_undercar' => array(
                'label' => 'Ly UnderCar GP %',
                'default' => 1,
            ),
 ////////////////////////////////////////////////
            'mtd_sls_undercar_divide_mtd_sales' => array(
                'label' => 'MTD % of Undercar Sales vs. Total Sales',
                'default' => 1,
            ),   
                    'ly_sls_undercar_divide_ly_sales' => array(
                'label' => 'MTD % of Undercar GP vs. Total GP',
                'default' => 1,
            ),
                    'ytd_sls_undercar_divide_ytd_sales' => array(
                'label' => 'MTD Undercar GP % vs. Total GP %',
                'default' => 1,
            ),
                    
                     'mtd_gp_undercar_divide_mtd_gp' => array(
                'label' => 'MTD % of Undercar Bgt Sls vs. Total Bgt Sls',
                'default' => 1,
            ),       
                    'mtd_gpp_undercar_divide_mtd_gpp' => array(
                'label' => 'MTD % of Undercar Projected vs. Total Projected',
                'default' => 1,
            ),
                    'mtd_budget_undercar_sales_divide_mtd_budget_sales' => array(
                'label' => 'LY % of Undercar Sales vs. Total Sales',
                'default' => 1,
            ),
                      'mtd_projected_undercar_divide_mtd_projected' => array(
                'label' => 'YTD % of Undercar Sales vs. Total Sales',
                'default' => 1,
            ),   
                    'ytd_gp_undercar_divide_ytd_gp' => array(
                'label' => 'YTD % of Undercar GP vs. Total GP',
                'default' => 1,
            ),
                    'ytd_gpp_undercar_divide_ytd_gpp' => array(
                'label' => 'YTD Undercar GP % vs. Total GP %',
                'default' => 1,
            ),
                     'ytd_budget_undercar_sales_divide_ytd_sales_budget' => array(
                'label' => 'YTD % of Undercar Bgt Sls vs. Total Bgt Sls',
                'default' => 1,
            ),   
                    'ytd_projected_undercar_divide_ytd_projected' => array(
                'label' => 'YTD % of Undercar Projected vs. Total Projected',
                'default' => 1,
            ),
                    'ly_gpp_undercar_divide_ly_gpp' => array(
                'label' => 'LY Undercar GP % vs. Total GP %',
                'default' => 1,
            ),
///////////////////////////////////////////////////\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\    

            'ytd_invoices' => array(
                'label' => 'YTD - # of Transactions',
                'default' => 1,
            ),
            'mtd_invoices' => array(
                'label' => 'CM - # of Transactions',
                'default' => 1,
            ),
            'wtd_invoices' => array(
                'label' => 'CW - # of Transactions',
                'default' => 1,
            ),
///////////////////////////////////////////////////////////////////////////////////////////////////
            'ytd_av_per_trans' => array(
                'label' => 'YTD Avg $ per transaction',
                'default' => 1,
            ),
            'mtd_av_per_trans' => array(
                'label' => 'MTD Avg $ per transaction',
                'default' => 1,
                'width' => 1,
            ),
        );

        $this->columns = $dashletData['DSls_DailySalesDashlet']['columns'];
        //print_r($dashletData['DSls_DailySalesDashlet']['columns']);



        $this->seedBean = new DSls_DailySales();
    }

    /* build sets of region, location, slsm and dealertype this user has access to */

    static function build521ACL($userid) {
        //$userGroupList = self::getUserSecurityGroupMembership ( $userid );

        $acl = array();
        $user_slsm = self::getUserSlsmRollup($userid);
        $user_area = self::getUserAreaRollup($userid, false); /* only include full Region and Location accesses */
        $user_dt = self::getUserDealerTypeAccess($userid);
        $user_cg = self::getUserCustomerGroups($userid);

        foreach ($user_slsm as $slsm) {
            $slsm_below_array = self::getSlsmBelow($_SESSION['fmp_slsm'], $slsm['slsm']); /* result includes original slsmno */
            foreach ($slsm_below_array as $subslsm) {
                $acl [] = array('region' => NULL, 'location' => NULL, 'slsm' => $subslsm, 'dealertype' => NULL, 'custid' => NULL);
            }
        }

        foreach ($user_area as $area) {
            if ($area['type'] == 'location') {
                $acl [] = array('region' => NULL, 'location' => $area['number'], 'slsm' => NULL, 'dealertype' => NULL, 'custid' => NULL);
            } elseif ($area['type'] == 'region') {
                $acl [] = array('region' => $area['number'], 'location' => NULL, 'slsm' => NULL, 'dealertype' => NULL, 'custid' => NULL);
                /* shouldn't need this as we can just select by region */
                /* foreach($area['locations'] as $locArray) {  
                  $acl [] = array('region' => NULL, 'location' => $locArray['number'], 'slsm' => NULL, 'dealertype' => NULL, 'custid' => NULL );
                  } */
            }
        }

        foreach ($user_dt as $dtarray) { /* 'dealertype'=>'dealertypeno','area'=>'location|region', 'number'=>'area/regionnumber' */
            $region = null;
            $location = null;
            if ($dtarray['area'] == 'location') {
                $location = $dtarray['number'];
            }
            if ($dtarray['area'] == 'region') {
                $region = $dtarray['number'];
            }
            if ($location !== null or $region !== null) {
                $acl[] = array('region' => $region, 'location' => $location, 'slsm' => NULL, 'dealertype' => $dtarray['dealertype'], 'custid' => NULL);
            }
        }

        foreach ($user_cg as $cgarray) { /* 'CustomerARGroup_xxx' => array(custid1, custid2, custid3...) */
            foreach ($cgarray as $custid) {
                $acl[] = array('region' => NULL, 'location' => NULL, 'slsm' => NULL, 'dealertype' => NULL, 'custid' => $custid);
            }
        }
        return $acl;
    }
    
    function saveOptions($req) {
        global $current_user;
        $options = parent::saveOptions($req);
        if(!empty($req['personal_filter'])) {
            $options['personal_filter'] = true;
        }
        $options['saved_option'] = true;
        return $options;
    }   
    // processDisplayOptions
    function processDisplayOptions() {
        require_once('include/templates/TemplateGroupChooser.php');

        $this->configureSS = new Sugar_Smarty();
        // column chooser
        $chooser = new TemplateGroupChooser();

        $chooser->args['id'] = 'edit_tabs';
        $chooser->args['left_size'] = 5;
        $chooser->args['right_size'] = 5;
        $chooser->args['values_array'][0] = array();
        $chooser->args['values_array'][1] = array();

        $this->addCustomFields();

        if ($this->displayColumns) {
            // columns to display
            foreach ($this->displayColumns as $num => $name) {
                // defensive code for array being returned
                $translated = translate($this->columns[$name]['label'], $this->seedBean->module_dir);
                if (is_array($translated))
                    $translated = $this->columns[$name]['label'];
                $chooser->args['values_array'][0][$name] = trim($translated, ':');
            }
            // columns not displayed
            foreach (array_diff(array_keys($this->columns), array_values($this->displayColumns)) as $num => $name) {
                // defensive code for array being returned
                $translated = translate($this->columns[$name]['label'], $this->seedBean->module_dir);
                if (is_array($translated))
                    $translated = $this->columns[$name]['label'];
                $chooser->args['values_array'][1][$name] = trim($translated, ':');
            }
        }
        else {
            $this->displayColumns = array();
            foreach ($this->columns as $name => $val) {
                // defensive code for array being returned
                $translated = translate($this->columns[$name]['label'], $this->seedBean->module_dir);
                if (is_array($translated))
                    $translated = $this->columns[$name]['label'];
                if (!empty($val['default']) && $val['default'])
                    $chooser->args['values_array'][0][$name] = trim($translated, ':');
                else
                    $chooser->args['values_array'][1][$name] = trim($translated, ':');
            }
        }

        $chooser->args['left_name'] = 'display_tabs';
        $chooser->args['right_name'] = 'hide_tabs';
        $chooser->args['max_left'] = '100';

        $chooser->args['left_label'] = 'Display Cells';
        $chooser->args['right_label'] = 'Hidden Cells';
        $chooser->args['title'] = '';
        $this->configureSS->assign('columnChooser', $chooser->display());

        $query = false;
        $count = 0;

        if (!is_array($this->filters)) {
            // use default search params
            $this->filters = array();
            foreach ($this->searchFields as $name => $params) {
                if (!empty($params['default']))
                    $this->filters[$name] = $params['default'];
            }
        }
        foreach ($this->searchFields as $name => $params) {
            if (!empty($name)) {
                    $name = strtolower($name);
                    $currentSearchFields[$name] = array();
                                $widgetDef = $this->seedBean->field_defs[$name];
                            if ($widgetDef['type'] == 'enum')
                                    $widgetDef['remove_blank'] = true; // remove the blank option for the dropdown

                            $widgetDef['input_name0'] = empty($this->filters[$name]) ? '' : $this->filters[$name];
                            $currentSearchFields[$name]['label'] = translate($widgetDef['vname'], $this->seedBean->module_dir);
                            $currentSearchFields[$name]['input'] = $this->layoutManager->widgetDisplayInput($widgetDef, true, (empty($this->filters[$name]) ? '' : $this->filters[$name]));
            }
            else { // ability to create spacers in input fields
                $currentSearchFields['blank' + $count]['label'] = '';
                $currentSearchFields['blank' + $count]['input'] = '';
                $count++;
            }
        }
        $cheked = $this->personal_filter ? 'checked' : '';
        $currentSearchFields['personal_filter']['label'] = 'Keep user-selected filters';
        $currentSearchFields['personal_filter']['input']  =  '<input type="checkbox" id="personal_filter" '.$cheked.' name="personal_filter" title="" tabindex="">';
        $this->currentSearchFields = $currentSearchFields;

        $this->configureSS->assign('strings', array('general' => $GLOBALS['mod_strings']['LBL_DASHLET_CONFIGURE_GENERAL'],
            'filters' => $GLOBALS['mod_strings']['LBL_DASHLET_CONFIGURE_FILTERS'],
            'myItems' => $GLOBALS['mod_strings']['LBL_DASHLET_CONFIGURE_MY_ITEMS_ONLY'],
            'displayRows' => $GLOBALS['mod_strings']['LBL_DASHLET_CONFIGURE_DISPLAY_ROWS'],
            'title' => $GLOBALS['mod_strings']['LBL_DASHLET_CONFIGURE_TITLE'],
            'save' => $GLOBALS['app_strings']['LBL_SAVE_BUTTON_LABEL']));
        $this->configureSS->assign('id', $this->id);
        $this->configureSS->assign('showMyItemsOnly', $this->showMyItemsOnly);
        $this->configureSS->assign('myItemsOnly', $this->myItemsOnly);
        $this->configureSS->assign('searchFields', $this->currentSearchFields);
        // title
        $this->configureSS->assign('dashletTitle', $this->title);

        // display rows
        $displayRowOptions = $GLOBALS['sugar_config']['dashlet_display_row_options'];
        $this->configureSS->assign('displayRowOptions', $displayRowOptions);
        $this->configureSS->assign('displayRowSelect', $this->displayRows);
    }

    function display() {
        echo '<link type="text/css" href="modules/Calendar2/css/themes/base/ui.all.css" rel="stylesheet" />';
        echo '<link rel="stylesheet" type="text/css" href="custom/modules/Accounts/datatables.css" />';
        echo '<script src="/modules/Calendar2/js/jquery-1.3.2.min.js"></script>';
        echo '<script type="text/javascript" src="modules/Calendar2/js/jquery-ui-1.7.2.custom.min.js"></script>';
        echo '<script src="custom/modules/Accounts/jquery.datatables.min.js" type="text/javascript"></script>';
        echo '<script type="text/javascript" src="modules/Calendar2/js/ui.core.js"></script>';
        echo '<script type="text/javascript" src="modules/Calendar2/js/ui.datepicker.js"></script>';

        echo $this->scripts_for_display();
        return $this->getTitle('') . $this->process_data() . '<br />';
    }

    function process_data() {
        global $current_user, $sugar_config, $app_list_strings;
        $this->ss = new Sugar_Smarty();

        //$data521Array=FMPSales::getSalesTotals();
        //print_r($data521Array);
        $title = '';
        //$personal_filter = ($current_user->getPreference('personal_filter_value')>0) ? $current_user->getPreference('personal_filter_time') : $current_user->getPreference('personal_filter_value') ;
        $date_now = time();
        if(($_REQUEST['action'] == 'DynamicAction'  && !$this->saved_option) || (!$this->personal_filter && !$this->saved_option)){
            $slsm = '';
            $reg_loc = '';
            $dealer_post = '';
            $current_user->setPreference('daily_sales_Slsm', $slsm);
            $current_user->setPreference('daily_sales_Reg_loc', $reg_loc);
            $current_user->setPreference('daily_sales_Dealer_post', $dealer_post);
        }else{
            $slsm = ($current_user->getPreference('daily_sales_Slsm'))? $current_user->getPreference('daily_sales_Slsm'): '';
            $reg_loc = ($current_user->getPreference('daily_sales_Reg_loc'))? $current_user->getPreference('daily_sales_Reg_loc'): '';
            $dealer_post = ($current_user->getPreference('daily_sales_Dealer_post'))? $current_user->getPreference('daily_sales_Dealer_post'): '';
        }
	$_SESSION['dashet_and_521_selections'] = array( 'slsm' => $slsm, 
							'loc' => $reg_loc, 
							'dealer' => $dealer_post);

        $region_id = substr($reg_loc,0,1) == 'r' ? substr($reg_loc,1) : null;
        $location_id = substr($reg_loc,0,1) != 'r' ? $reg_loc : null;
            //$reg_loc_query = $this->get_reg_loc($reg_loc);

            $title .= $reg_loc != 'undefined' ? (substr($reg_loc,0,1) == 'r' ? 'Region '.substr($reg_loc,1) : $reg_loc != '' ? 'Location '.$reg_loc : ''): '';
            
        $is_user_id = 0;
        $slsm_obj = new fmp_Param_SLSM($current_user->id);
        $slsm_obj->init();

        $is_s = $slsm_obj->is_assigned_slsm();

        if ($is_s) {

            $title .= $slsm != 'undefined' && $slsm != '' ? ($reg_loc != '' && $reg_loc != 'undefined' && $reg_loc != 'undefined' ? '/Slsm '.$slsm : 'Slsm '.$slsm) :'';
            //$r_users = $slsm_obj->compile__available_slsm($arr);

            //$str_selection_button = $this->build__slsm($r_users, $is_user_id);
        }
        $title .= $dealer_post != 'undefined' && $dealer_post != '' ? (($slsm == 'undefined' || $slsm == '') && ($reg_loc == 'undefined' || $reg_loc == '') ? 'Customer Type '.$dealer_post : '/Customer Type '.$dealer_post) :'';

        $slsm_tree_list = $slsm_obj->html_for_daily_sales('onchange="javaScript:get_date_for_table()"', '');  // prepeare SLSM list for display
        unset($slsm_obj);

        $slsm_area_obj = new fmp_Param_RegLoc($current_user->id);
        $slsm_area_obj->init($current_user->id);
        $area_list = $slsm_area_obj->html_for_daily_sales($current_user->id, 'onchange="javaScript:get_date_for_table()"');
        unset($slsm_area_obj);


        $db = &PearDatabase::getInstance();
        $query = $this->construct_query_for_daily_sales();
        
        $res = $db->query($query . " " . $this->acl_where());
        //print_r($str_selection_button);


        $result = $db->fetchByAssoc($res);
//print_r($result);
//        while($result = $db->fetchByAssoc($res)) {
//print_r($result['ytd_gp']);
        $ytd_projected = isset($result['ytd_projected']) ? $result['ytd_projected'] : 0;
        $ytd_projected_gp = isset($result['ytd_projected_gp']) ? $result['ytd_projected_gp'] : 0;
        $ytd_sales_invoiced = isset($result['ytd_sales']) ? $result['ytd_sales'] : 0;
        $ytd_gp = isset($result['ytd_gp']) ? $result['ytd_gp'] : 0;
        $ytd_gpp = (isset($result['ytd_sales']) && $result['ytd_sales'] + 0 != 0) ? round(($result['ytd_gp'] / $result['ytd_sales']) * 100, 2) . "%" : '0%';
        $ytd_sales_budget = isset($result['ytd_budget_sales']) ? $result['ytd_budget_sales'] : 0;
        $cm_gp_budget = isset($result['mtd_budget_gp']) ? $result['mtd_budget_gp'] : 0;
        $cm_gpp_budget = (isset($result['mtd_budget_gp']) && $result['mtd_budget_gp'] + 0 != 0) ? round(($result['mtd_budget_gp'] / $result['mtd_budget_sales']) * 100, 2) . '%' : '0%';
        $cy_gp_budget = isset($result['ytd_budget_gp']) ? $result['ytd_budget_gp'] : 0;
        $cy_gpp_budget = (isset($result['ytd_budget_gp']) && $result['ytd_budget_gp'] + 0 != 0) ? round(($result['ytd_budget_gp'] / $result['ytd_budget_sales']) * 100, 2) . '%' : '0%';
        $previous_day_invoiced_sales = isset($result['previous_day_sales']) ? $result['previous_day_sales'] : 0;
        $mtd_projected = isset($result['mtd_projected']) ? $result['mtd_projected'] : 0;
        $mtd_projected_gp = isset($result['mtd_projected_gp']) ? $result['mtd_projected_gp'] : 0;
        $mtd_budget_sales = isset($result['mtd_budget_sales']) ? $result['mtd_budget_sales'] : 0;
        $mtd_sales = isset($result['mtd_sales']) ? $result['mtd_sales'] : 0;
        $mly_sales_invoiced = isset($result['mly_sls']) ? $result['mly_sls'] : 0;
        $lytm_sales_invoiced = isset($result['lytd_sales']) ? $result['lytd_sales'] : 0;
        $lm_sales = isset($result['lm_sales']) ? $result['lm_sales'] : 0;
        $ly_sales_invoiced = isset($result['ly_sales']) ? $result['ly_sales'] : 0;
        $mtd_gp = isset($result['mtd_gp']) ? $result['mtd_gp'] : 0;
        $lm_gp = isset($result['lm_gp']) ? $result['lm_gp'] : 0;
        $mtd_budget_gp = isset($result['mtd_budget_gp']) ? $result['mtd_budget_gp'] : 0;
        $mtd_sales_total = $result['mtd_sales'] + $result['pending_orders'] + $result['pending_credits'] + $result['todays_orders'] + $result['todays_credits'];
        $pending_orders = $result['pending_orders'] + $result['todays_orders'];
        $pending_credits = $result['pending_credits'] + $result['todays_credits'];
        $ly_gp = $result['ly_gp'];
        $ly_gpp = $result['ly_sales'] + 0 != 0 ? round(($result['ly_gp'] / $result['ly_sales'] * 100), 2) . '%' : '0%';
        $ytd_invoices = $result['ytd_invoices'];
        $mtd_invoices = $result['mtd_invoices'];
        $wtd_invoices = $result['wtd_invoices'];

        $ytd_av_per_trans = $result['ytd_av_per_trans'];
        $mtd_av_per_trans = $result['mtd_av_per_trans'];

//        }

        $today_mounth = $date_today = date("m");
        $today_year = $date_today = date("Y");
        $res_month_business_days = $db->query($this->month_business_days($today_year, $today_mounth, -1));
        $bus_days = $db->fetchByAssoc($res_month_business_days);
        //print_r($bus_days);

        $mounth_start = date("Y-m-1");
        $mounth_today = date("Y-m-d", (time() - 86400));
        $res_first_month_business_days = $db->query($this->first_month_business_days_through_yesterday($mounth_start, $mounth_today));
        $first_bus_days = $db->fetchByAssoc($res_first_month_business_days);
        //print_r($first_bus_days);
        $dealer_list = $this->get_dealer_type($app_list_strings['fmp_dealertype_list']);

        $today_sales_summ = $this->sales_today_previous(date("Y-m-d"), 'SLSM',$slsm, $region_id, $location_id);
        $previous_sales_summ = $this->sales_today_previous(date("Y-m-d", (time() - 86400)), 'SLSM', $slsm, $region_id, $location_id);

        //($this->displayColumns == array() || in_array('cy_sales_proj_vs_cy_sales_budget', $this->displayColumns)) ? $this->ss->assign("cy_sales_proj_vs_cy_sales_budget", $this->red_color_text(($ytd_projected - $ytd_sales_budget))) : '&nbsp;';
        ($this->displayColumns == array() || in_array('current_year_sales_proj', $this->displayColumns)) ? $this->ss->assign("ytd_sales_projected", $this->red_color_text($ytd_projected)) : '&nbsp;';
         ($this->displayColumns == array() || in_array('current_year_gp_proj', $this->displayColumns)) ? $this->ss->assign("ytd_sales_projected_gp", $this->red_color_text($ytd_projected_gp)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('ytd_sales_invoiced', $this->displayColumns)) ? $this->ss->assign("ytd_sales_invoiced", $this->red_color_text($ytd_sales_invoiced)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('ytd_gp', $this->displayColumns)) ? $this->ss->assign("ytd_gp", $this->red_color_text($ytd_gp)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('ytd_gpp', $this->displayColumns)) ? $this->ss->assign("ytd_gpp", $ytd_gpp) : '&nbsp;';
        ($this->displayColumns == array() || in_array('current_year_sales_budget', $this->displayColumns)) ? $this->ss->assign("cy_sales_budget", $this->red_color_text($ytd_sales_budget)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('cm_gp_budget', $this->displayColumns)) ? $this->ss->assign("cm_gp_budget", $this->red_color_text($cm_gp_budget)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('cm_gpp_budget', $this->displayColumns)) ? $this->ss->assign("cm_gpp_budget", $this->red_color_pst($cm_gpp_budget)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('cy_gp_budget', $this->displayColumns)) ? $this->ss->assign("cy_gp_budget", $this->red_color_text($cy_gp_budget)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('cy_gpp_budget', $this->displayColumns)) ? $this->ss->assign("cy_gpp_budget", $this->red_color_pst($cy_gpp_budget)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('mly_sales_invoiced', $this->displayColumns)) ? $this->ss->assign("mly_sales_invoiced", $this->red_color_text($mly_sales_invoiced)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('lytd_sales_invoiced', $this->displayColumns)) ? $this->ss->assign("lytm_sales_invoiced", $this->red_color_text($lytm_sales_invoiced)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('mtd_sales_total', $this->displayColumns)) ? $this->ss->assign("mtd_sales_total", $this->red_color_text($mtd_sales_total)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('pending_orders', $this->displayColumns)) ? $this->ss->assign("pending_orders", $this->red_color_text($pending_orders)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('pending_credits', $this->displayColumns)) ? $this->ss->assign("pending_credits", $this->red_color_text($pending_credits)) : '&nbsp;';

        ($this->displayColumns == array() || in_array('ly_sales_invoiced', $this->displayColumns)) ? $this->ss->assign("ly_sales_invoiced", $this->red_color_text($ly_sales_invoiced)) : '&nbsp;';

        ($this->displayColumns == array() || in_array('ly_gp', $this->displayColumns)) ? $this->ss->assign("ly_gp", $this->red_color_text($ly_gp)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('ly_gpp', $this->displayColumns)) ? $this->ss->assign("ly_gpp", $this->red_color_pst($ly_gpp)) : '&nbsp;';


        ($this->displayColumns == array() || in_array('current_day_sales', $this->displayColumns)) ? $this->ss->assign("current_day_sales", $this->red_color_text($today_sales_summ['sales'])) : '&nbsp;';
        ($this->displayColumns == array() || in_array('current_day_credits', $this->displayColumns)) ? $this->ss->assign("current_day_credits", $this->red_color_text($today_sales_summ['credits'])) : '&nbsp;';
        ($this->displayColumns == array() || in_array('current_day_net_sales', $this->displayColumns)) ? $this->ss->assign("current_day_net_sales", $this->red_color_text($today_sales_summ['net_sales'])) : '&nbsp;';
        ($this->displayColumns == array() || in_array('previous_day_sales', $this->displayColumns)) ? $this->ss->assign("previous_day_sales", $this->red_color_text($previous_sales_summ['sales'])) : '&nbsp;';
        ($this->displayColumns == array() || in_array('previous_day_credits', $this->displayColumns)) ? $this->ss->assign("previous_day_credits", $this->red_color_text($previous_sales_summ['credits'])) : '&nbsp;';
        ($this->displayColumns == array() || in_array('previous_day_net_sales', $this->displayColumns)) ? $this->ss->assign("previous_day_net_sales", $this->red_color_text($previous_sales_summ['net_sales'])) : '&nbsp;';
        ($this->displayColumns == array() || in_array('previous_day_invoiced_sales', $this->displayColumns)) ? $this->ss->assign("previous_day_invoiced_sales", $this->red_color_text($previous_day_invoiced_sales)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('current_month_proj', $this->displayColumns)) ? $this->ss->assign("mtd_projected", $this->red_color_text($mtd_projected)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('current_month_proj_gp', $this->displayColumns)) ? $this->ss->assign("mtd_projected_gp", $this->red_color_text($mtd_projected_gp)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('current_month_sales', $this->displayColumns)) ? $this->ss->assign("mtd_sales", $this->red_color_text($mtd_sales)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('current_month_sales_budget', $this->displayColumns)) ? $this->ss->assign("mtd_budget_sales", $this->red_color_text($mtd_budget_sales)) : '&nbsp;';

//        $daily_average_target = $first_bus_days['num'] != 0 ? $mtd_budget_sales / $first_bus_days['num'] : 0;
        $daily_average_target = $bus_days['num'] != 0 ? $mtd_budget_sales / $bus_days['num'] : 0;
        
        $remaining_working_days = $bus_days['num'] - $first_bus_days['num'];
        
        ($this->displayColumns == array() || in_array('daily_target', $this->displayColumns)) ? $this->ss->assign("daily_target", ($remaining_working_days != 0) ? ((($mtd_budget_sales - $mtd_sales) / $remaining_working_days > $daily_average_target) ? $this->red_color_text(($mtd_budget_sales - $mtd_sales) / $remaining_working_days) : $this->red_color_text($daily_average_target)) : 0) : '&nbsp;';

        ($this->displayColumns == array() || in_array('daily_average_target', $this->displayColumns)) ? $this->ss->assign("daily_average_target", $bus_days['num'] != 0 ? $this->red_color_text($mtd_budget_sales / $bus_days['num']) : 0) : '&nbsp;';

        $daily_average_target_gp = ($bus_days['num'] != 0) ? $mtd_budget_gp / $bus_days['num'] : 0;

        ($this->displayColumns == array() || in_array('daily_target_gp', $this->displayColumns)) ? $this->ss->assign("daily_target_gp", ($remaining_working_days != 0) ? ((($mtd_budget_gp - $mtd_gp) / $remaining_working_days > $daily_average_target_gp) ? $this->red_color_text(($mtd_budget_gp - $mtd_gp) / $remaining_working_days) : $this->red_color_text($daily_average_target_gp)) : 0) : '&nbsp;';

        ($this->displayColumns == array() || in_array('daily_average_target_gp', $this->displayColumns)) ? $this->ss->assign("daily_average_target_gp", ($bus_days['num'] != 0) ? $this->red_color_text($mtd_budget_gp / $bus_days['num']) : 0) : '&nbsp;';



        ($this->displayColumns == array() || in_array('last_month_sales', $this->displayColumns)) ? $this->ss->assign("lm_sales", $this->red_color_text($lm_sales)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('current_month_gp', $this->displayColumns)) ? $this->ss->assign("mtd_gp", $this->red_color_text($mtd_gp)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('last_month_gp', $this->displayColumns)) ? $this->ss->assign("lm_gp", $this->red_color_text($lm_gp)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('cm_proj_vs_budget', $this->displayColumns)) ? $this->ss->assign("cm_proj_vs_budget", $this->red_color_text($mtd_projected - $mtd_budget_sales)) : '&nbsp;';
       // ($this->displayColumns == array() || in_array('current_month_proj_sales_vs_last_month_sales', $this->displayColumns)) ? $this->ss->assign("cm_proj_sales_vs_lm_month_sales", $this->red_color_text($mtd_projected - $lm_sales)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('cm_proj_gp_vs_budget_gp', $this->displayColumns)) ? $this->ss->assign("cm_proj_gp_vs_budget_gp", $this->red_color_text($mtd_projected_gp - $mtd_budget_gp)) : '&nbsp;';

//        ($this->displayColumns == array() || in_array('current_sales_proj_vs_ly_sales_invoiced', $this->displayColumns)) ? $this->ss->assign("current_sls_proj_vs_ly_sls_invoiced", $this->red_color_text($ytd_projected - $ly_sales_invoiced)) : '&nbsp;';
//        ($this->displayColumns == array() || in_array('current_gp_proj_vs_ly_gp_invoiced', $this->displayColumns)) ? $this->ss->assign("current_gp_proj_vs_ly_gp_invoiced", $this->red_color_text($ytd_projected_gp - $ly_gp)) : '&nbsp;';
         ($this->displayColumns == array() || in_array('current_sales_proj_vs_ly_sales_invoiced', $this->displayColumns)) ? $this->ss->assign("current_sls_proj_vs_ly_sls_invoiced", $this->red_color_text($ytd_projected - $ytd_sales_budget)) : '&nbsp;';
        ($this->displayColumns == array() || in_array('current_gp_proj_vs_ly_gp_invoiced', $this->displayColumns)) ? $this->ss->assign("current_gp_proj_vs_ly_gp_invoiced", $this->red_color_text($ytd_projected_gp - $cy_gp_budget)) : '&nbsp;';
        
        ($this->displayColumns == array() || in_array('att_year_sls_proj_vs_cy_sales_budget', $this->displayColumns)) ? $this->ss->assign("att_y_sls_proj_vs_cy_sls_budget", $this->red_color_pst($ytd_sales_budget != 0 ? round(($ytd_sales_invoiced / $ytd_sales_budget) * 100, 2) . '%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('att_year_gp_proj_vs_cy_gp_budget', $this->displayColumns)) ? $this->ss->assign("att_y_gp_proj_vs_cy_gp_budget", $this->red_color_pst($cy_gp_budget != 0 ? round(($ytd_gp / $cy_gp_budget)*100, 2) . '%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('att_y_gpp_proj_vs_cy_gpp_budget', $this->displayColumns)) ? $this->ss->assign("att_y_gpp_proj_vs_cy_gpp_budget", $this->red_color_pst($cy_gpp_budget != 0 ? round(($ytd_gpp-$cy_gpp_budget) , 2) . '%' : '0%')) : '&nbsp;';
      
        $current_month_gpp = $mtd_sales != 0 ? round(100 * $mtd_gp / $mtd_sales, 2) : 0;
        
        ($this->displayColumns == array() || in_array('current_month_gpp', $this->displayColumns)) ? $this->ss->assign("current_month_gp", $this->red_color_pst($current_month_gpp != 0 ?$current_month_gpp.'%' :'0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('last_month_gpp', $this->displayColumns)) ? $this->ss->assign("last_month_gp", $this->red_color_pst($lm_sales != 0 ? round(100 * $lm_gp / $lm_sales, 2) .'%' :'0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('att_sales_to_month_budget', $this->displayColumns)) ? $this->ss->assign("att_sal_to_mon_sal", $this->red_color_pst($mtd_budget_sales != 0 ? round($mtd_sales / $mtd_budget_sales * 100, 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('att_gp_to_month_budget', $this->displayColumns)) ? $this->ss->assign("att_gp_to_mon_bud", $this->red_color_pst($mtd_budget_gp != 0 ? round($mtd_gp / $mtd_budget_gp * 100, 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('att_gpp_to_month_budget_gpp', $this->displayColumns)) ? $this->ss->assign("att_gpp_to_month_budget_gpp", $this->red_color_pst($cm_gpp_budget != 0 ? round(($current_month_gpp - $cm_gpp_budget), 2).'%' : '0%')) : '&nbsp;';
         
        ($this->displayColumns == array() || in_array('ytd_invoices', $this->displayColumns)) ? $this->ss->assign("ytd_invoices", number_format($ytd_invoices, 0, '.', ',')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('mtd_invoices', $this->displayColumns)) ? $this->ss->assign("mtd_invoices", number_format($mtd_invoices, 0, '.', ',')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('wtd_invoices', $this->displayColumns)) ? $this->ss->assign("wtd_invoices", number_format($wtd_invoices, 0, '.', ',')) : '&nbsp;';

        ($this->displayColumns == array() || in_array('ytd_av_per_trans', $this->displayColumns)) ? $this->ss->assign("ytd_av_per_trans", number_format($ytd_av_per_trans, 0, '.', ',')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('mtd_av_per_trans', $this->displayColumns)) ? $this->ss->assign("mtd_av_per_trans", number_format($mtd_av_per_trans, 0, '.', ',')) : '&nbsp;';



        ($this->displayColumns == array() || in_array('mtd_sls_noem', $this->displayColumns)) ? $this->ss->assign('mtd_sls_noem', $this->red_color_text($result['mtd_sls_noem'])) : '&nbsp;';

        ($this->displayColumns == array() || in_array('ytd_sls_noem', $this->displayColumns)) ? $this->ss->assign('ytd_sls_noem', $this->red_color_text($result['ytd_sls_noem'])) : '&nbsp;';


        ($this->displayColumns == array() || in_array('mtd_gp_noem', $this->displayColumns)) ? $this->ss->assign('mtd_gp_noem', $this->red_color_text($result['mtd_gp_noem'])) : '&nbsp;';

        ($this->displayColumns == array() || in_array('ytd_gp_noem', $this->displayColumns)) ? $this->ss->assign('ytd_gp_noem', $this->red_color_text($result['ytd_gp_noem'])) : '&nbsp;';

        $mtd_gpp_noem = $result['mtd_sls_noem'] + 0 != 0 ? round($result['mtd_gp_noem'] / $result['mtd_sls_noem'] * 100, 2)  : 0;
        $ytd_gpp_noem = $result['ytd_sls_noem'] + 0 != 0 ? round($result['ytd_gp_noem'] / $result['ytd_sls_noem'] * 100, 2) : 0;
        $ly_gpp_noem = $result['ly_sls_noem'] + 0 != 0 ? round($result['ly_gp_noem'] / $result['ly_sls_noem'] * 100, 2)  : 0;
        ($this->displayColumns == array() || in_array('mtd_gpp_noem', $this->displayColumns)) ? $this->ss->assign('mtd_gpp_noem', $this->red_color_pst($mtd_gpp_noem != 0 ? $mtd_gpp_noem.'%':'0%')) : '&nbsp;';

        ($this->displayColumns == array() || in_array('ytd_gpp_noem', $this->displayColumns)) ? $this->ss->assign('ytd_gpp_noem', $this->red_color_pst($ytd_gpp_noem!= 0 ? $ytd_gpp_noem.'%':'0%')) : '&nbsp;';

        ($this->displayColumns == array() || in_array('mtd_sls_noem_divide_mtd_sales', $this->displayColumns)) ? $this->ss->assign("mtd_sls_noem_divide_mtd_sales", $this->red_color_pst($mtd_sales != 0 ? round($result['mtd_sls_noem'] / $mtd_sales * 100, 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('ly_sls_noem_divide_ly_sales', $this->displayColumns)) ? $this->ss->assign("ly_sls_noem_divide_ly_sales", $this->red_color_pst($ly_sales_invoiced != 0 ? round($result['ly_sls_noem'] / $ly_sales_invoiced * 100, 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('ytd_sls_noem_divide_ytd_sales', $this->displayColumns)) ? $this->ss->assign("ytd_sls_noem_divide_ytd_sales", $this->red_color_pst($ytd_sales_invoiced != 0 ? round($result['ytd_sls_noem'] / $ytd_sales_invoiced * 100, 2).'%' : '0%')) : '&nbsp;';
        
        ($this->displayColumns == array() || in_array('mtd_gp_noem_divide_mtd_gp', $this->displayColumns)) ? $this->ss->assign("mtd_gp_noem_divide_mtd_gp", $this->red_color_pst($mtd_gp != 0 ? round($result['mtd_gp_noem'] / $mtd_gp * 100, 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('mtd_gpp_noem_divide_mtd_gpp', $this->displayColumns)) ? $this->ss->assign("mtd_gpp_noem_divide_mtd_gpp", $this->red_color_pst($current_month_gpp != 0 ? round($mtd_gpp_noem - $current_month_gpp , 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('mtd_budget_noem_sales_divide_mtd_budget_sales', $this->displayColumns)) ? $this->ss->assign("mtd_budget_noem_sales_divide_mtd_budget_sales", $this->red_color_pst($mtd_budget_sales != 0 ? round($result['mtd_budget_noem_sales'] / $mtd_budget_sales * 100, 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('mtd_projected_noem_divide_mtd_projected', $this->displayColumns)) ? $this->ss->assign("mtd_projected_noem_divide_mtd_projected", $this->red_color_pst($mtd_projected != 0 ? round($result['mtd_projected_noem'] / $mtd_projected * 100, 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('ytd_gp_noem_divide_ytd_gp', $this->displayColumns)) ? $this->ss->assign("ytd_gp_noem_divide_ytd_gp", $this->red_color_pst($ytd_gp != 0 ? round($result['ytd_gp_noem'] / $ytd_gp * 100, 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('ytd_gpp_noem_divide_ytd_gpp', $this->displayColumns)) ? $this->ss->assign("ytd_gpp_noem_divide_ytd_gpp", $this->red_color_pst($ytd_gpp != 0 ? round($ytd_gpp_noem - $ytd_gpp , 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('ytd_budget_noem_sales_divide_ytd_sales_budget', $this->displayColumns)) ? $this->ss->assign("ytd_budget_noem_sales_divide_ytd_sales_budget", $this->red_color_pst($ytd_sales_budget != 0 ? round($result['ytd_budget_noem_sales'] / $ytd_sales_budget * 100, 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('ytd_projected_noem_divide_ytd_projected', $this->displayColumns)) ? $this->ss->assign("ytd_projected_noem_divide_ytd_projected", $this->red_color_pst($ytd_projected != 0 ? round($result['ytd_projected_noem'] / $ytd_projected * 100, 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('ly_gpp_noem_divide_ly_gpp', $this->displayColumns)) ? $this->ss->assign("ly_gpp_noem_divide_ly_gpp", $this->red_color_pst($ly_gpp != 0 ? round($ly_gpp_noem - $ly_gpp , 2).'%' : '0%')) : '&nbsp;';

 
        ($this->displayColumns == array() || in_array('mtd_budget_noem_sales', $this->displayColumns)) ? $this->ss->assign('mtd_budget_noem_sales', $this->red_color_text($result['mtd_budget_noem_sales'])) : '&nbsp;';

        ($this->displayColumns == array() || in_array('ytd_budget_noem_sales', $this->displayColumns)) ? $this->ss->assign('ytd_budget_noem_sales', $this->red_color_text($result['ytd_budget_noem_sales'])) : '&nbsp;';

        ($this->displayColumns == array() || in_array('mtd_projected_noem', $this->displayColumns)) ? $this->ss->assign('mtd_projected_noem', $this->red_color_text($result['mtd_projected_noem'])) : '&nbsp;';

        ($this->displayColumns == array() || in_array('ytd_projected_noem', $this->displayColumns)) ? $this->ss->assign('ytd_projected_noem', $this->red_color_text($result['ytd_projected_noem'])) : '&nbsp;';

        ($this->displayColumns == array() || in_array('ly_sls_noem', $this->displayColumns)) ? $this->ss->assign('ly_sls_noem', $this->red_color_text($result['ly_sls_noem'])) : '&nbsp;';

        ($this->displayColumns == array() || in_array('ly_gpp_noem', $this->displayColumns)) ? $this->ss->assign('ly_gpp_noem',$this->red_color_pst($ly_gpp_noem != 0 ? $ly_gpp_noem.'%' : '0%')) : '&nbsp;';


/////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////
//UNDERCAR

        $mtd_gpp_undercar = $result['mtd_sls_undercar'] + 0 != 0 ? round($result['mtd_gp_undercar'] / $result['mtd_sls_undercar'] * 100, 2) : 0;
        $ytd_gpp_undercar = $result['ytd_sls_undercar'] + 0 != 0 ? round($result['ytd_gp_undercar'] / $result['ytd_sls_undercar'] * 100, 2)  : 0;
        $ly_gpp_undercar = $result['ly_sls_undercar'] + 0 != 0 ? round($result['ly_gp_undercar'] / $result['ly_sls_undercar'] * 100, 2) : 0;
        ($this->displayColumns == array() || in_array('mtd_sls_undercar', $this->displayColumns)) ? $this->ss->assign('mtd_sls_undercar', $this->red_color_text($result['mtd_sls_undercar'])) : '&nbsp;';

        ($this->displayColumns == array() || in_array('ytd_sls_undercar', $this->displayColumns)) ? $this->ss->assign('ytd_sls_undercar', $this->red_color_text($result['ytd_sls_undercar'])) : '&nbsp;';

        ($this->displayColumns == array() || in_array('mtd_gp_undercar', $this->displayColumns)) ? $this->ss->assign('mtd_gp_undercar', $this->red_color_text($result['mtd_gp_undercar'])) : '&nbsp;';

        ($this->displayColumns == array() || in_array('ytd_gp_undercar', $this->displayColumns)) ? $this->ss->assign('ytd_gp_undercar', $this->red_color_text($result['ytd_gp_undercar'])) : '&nbsp;';

        ($this->displayColumns == array() || in_array('mtd_gpp_undercar', $this->displayColumns)) ? $this->ss->assign('mtd_gpp_undercar', $this->red_color_pst($result['mtd_sls_undercar'] + 0 > 0 ? round(($result['mtd_gp_undercar'] / $result['mtd_sls_undercar']) * 100, 2) . "%" : '0%' )) : '&nbsp;';

        ($this->displayColumns == array() || in_array('ytd_gpp_undercar', $this->displayColumns)) ? $this->ss->assign('ytd_gpp_undercar', $this->red_color_pst($result['ytd_sls_undercar'] + 0 > 0 ? round(($result['ytd_gp_undercar'] / $result['ytd_sls_undercar']) * 100, 2) . "%" : '0%' )) : '&nbsp;';

        ($this->displayColumns == array() || in_array('mtd_budget_undercar_sales', $this->displayColumns)) ? $this->ss->assign('mtd_budget_undercar_sales', $this->red_color_text($result['mtd_budget_undercar_sales'])) : '&nbsp;';

        ($this->displayColumns == array() || in_array('ytd_budget_undercar_sales', $this->displayColumns)) ? $this->ss->assign('ytd_budget_undercar_sales', $this->red_color_text($result['ytd_budget_undercar_sales'])) : '&nbsp;';

        ($this->displayColumns == array() || in_array('mtd_projected_undercar', $this->displayColumns)) ? $this->ss->assign('mtd_projected_undercar', $this->red_color_text($result['mtd_projected_undercar'])) : '&nbsp;';

        ($this->displayColumns == array() || in_array('ytd_projected_undercar', $this->displayColumns)) ? $this->ss->assign('ytd_projected_undercar', $this->red_color_text($result['ytd_projected_undercar'])) : '&nbsp;';


        ($this->displayColumns == array() || in_array('ly_sls_undercar', $this->displayColumns)) ? $this->ss->assign('ly_sls_undercar', $this->red_color_text($result['ly_sls_undercar'])) : '&nbsp;';

        ($this->displayColumns == array() || in_array('ly_gpp_undercar', $this->displayColumns)) ? $this->ss->assign('ly_gpp_undercar', $this->red_color_pst($result['ly_sls_undercar'] + 0 > 0 ? round(($result['ly_gp_undercar'] / $result['ly_sls_undercar']) * 100, 2) . "%" : '0%' )) : '&nbsp;';
        
        ($this->displayColumns == array() || in_array('mtd_sls_undercar_divide_mtd_sales', $this->displayColumns)) ? $this->ss->assign("mtd_sls_undercar_divide_mtd_sales", $this->red_color_pst($mtd_sales != 0 ? round($result['mtd_sls_undercar'] / $mtd_sales * 100, 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('ly_sls_undercar_divide_ly_sales', $this->displayColumns)) ? $this->ss->assign("ly_sls_undercar_divide_ly_sales", $this->red_color_pst($ly_sales_invoiced != 0 ? round($result['ly_sls_undercar'] / $ly_sales_invoiced * 100, 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('ytd_sls_undercar_divide_ytd_sales', $this->displayColumns)) ? $this->ss->assign("ytd_sls_undercar_divide_ytd_sales", $this->red_color_pst($ytd_sales_invoiced != 0 ? round($result['ytd_sls_undercar'] / $ytd_sales_invoiced * 100, 2).'%' : '0%')) : '&nbsp;';

        ($this->displayColumns == array() || in_array('mtd_gp_undercar_divide_mtd_gp', $this->displayColumns)) ? $this->ss->assign("mtd_gp_undercar_divide_mtd_gp", $this->red_color_pst($mtd_gp != 0 ? round($result['mtd_gp_undercar'] / $mtd_gp * 100, 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('mtd_gpp_undercar_divide_mtd_gpp', $this->displayColumns)) ? $this->ss->assign("mtd_gpp_undercar_divide_mtd_gpp", $this->red_color_pst($current_month_gpp != 0 ? round($mtd_gpp_undercar - $current_month_gpp , 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('mtd_budget_undercar_sales_divide_mtd_budget_sales', $this->displayColumns)) ? $this->ss->assign("mtd_budget_undercar_sales_divide_mtd_budget_sales", $this->red_color_pst($mtd_budget_sales != 0 ? round($result['mtd_budget_undercar_sales'] / $mtd_budget_sales * 100, 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('mtd_projected_undercar_divide_mtd_projected', $this->displayColumns)) ? $this->ss->assign("mtd_projected_undercar_divide_mtd_projected", $this->red_color_pst($mtd_projected != 0 ? round($result['mtd_projected_undercar'] / $mtd_projected * 100, 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('ytd_gp_undercar_divide_ytd_gp', $this->displayColumns)) ? $this->ss->assign("ytd_gp_undercar_divide_ytd_gp", $this->red_color_pst($ytd_gp != 0 ? round($result['ytd_gp_undercar'] / $ytd_gp * 100, 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('ytd_gpp_undercar_divide_ytd_gpp', $this->displayColumns)) ? $this->ss->assign("ytd_gpp_undercar_divide_ytd_gpp", $this->red_color_pst($ytd_gpp != 0 ? round($ytd_gpp_undercar - $ytd_gpp , 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('ytd_budget_undercar_sales_divide_ytd_sales_budget', $this->displayColumns)) ? $this->ss->assign("ytd_budget_undercar_sales_divide_ytd_sales_budget", $this->red_color_pst($ytd_sales_budget != 0 ? round($result['ytd_budget_undercar_sales'] / $ytd_sales_budget * 100, 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('ytd_projected_undercar_divide_ytd_projected', $this->displayColumns)) ? $this->ss->assign("ytd_projected_undercar_divide_ytd_projected", $this->red_color_pst($ytd_projected != 0 ? round($result['ytd_projected_undercar'] / $ytd_projected * 100, 2).'%' : '0%')) : '&nbsp;';
        ($this->displayColumns == array() || in_array('ly_gpp_undercar_divide_ly_gpp', $this->displayColumns)) ? $this->ss->assign("ly_gpp_undercar_divide_ly_gpp", $this->red_color_pst($ly_gpp != 0 ? round($ly_gpp_undercar - $ly_gpp , 2).'%' : '0%')) : '&nbsp;';

        
        $this->ss->assign("title_filter", empty($title)? 'All':$title);
        $this->ss->assign("areaList", $area_list);
        $this->ss->assign("slsmList", $slsm_tree_list);
        $this->ss->assign("dealerList", $dealer_list);
        $this->ss->assign('dashletId', $this->id);
//print_r($_POST);
//print_r($_GET);
//print_r($_SESSION);
        return $this->ss->fetch('modules/DSls_DailySales/Dashlets/FMP_DSls_DailySalesDashlet/FMP_Dsls_DailySales_templete.html');
    }

    function acl_where_summary() {

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

        return $acl_access_granted;
    }

    protected function build__slsm($compiled_slsm, $is_user_id) {
        foreach ($compiled_slsm as $k => $v) {
            $compiled_slsm[$k] = "'$v'";
        }
        include_once("521/FMPSales.php");

        FMPSales::initialize521();

        $sql = $qryCorporate;
        $sqlWhere = "(";

        /* not used anymore */
        $primaryOp = "AND"; /* for $selectMethod = 'i', intersect */
        if ($selectMethod == 'u') { /* union */
            $primaryOp = "OR";
        }

        /* generate where criteria with everything in acl -- including custid */
        $acl_array = $_SESSION['fmp_acl'];
        $bNeedOp = false;
        $slsmInSqlWhere = array();
        //return $acl_array;
        foreach ($acl_array as $acl) {
            $bNeedSubOp = false;
            if ($bNeedOp) {
                if (!is_null($acl['slsm'])) {
                    $slsmInSqlWhere[] = $acl['slsm'];
                    $bNeedOp = true;
                    continue;
                }
                $sqlWhere .= " OR ";
            }
            $sqlWhere .= "(";
            if (!is_null($acl['location'])) {
                $sqlWhere .= "dsls_dailysales.loc = " . $acl['location'];
                $bNeedSubOp = true;
            }
            if (!is_null($acl['region'])) {
                if ($bNeedSubOp) {
                    $sqlWhere .= " AND ";
                }
                $sqlWhere .= "dsls_dailysales.region = " . $acl['region'];
                $bNeedSubOp = true;
            }
            if (!is_null($acl['slsm'])) {
                if ($bNeedSubOp) {
                    $sqlWhere .= " AND ";
                }
                $sqlWhere .= sprintf("x_a.slsm_c = '%s'", $acl['slsm']);  /* select slsm from accounts table to include those with no ds data */

                $bNeedSubOp = true;
            }
            if (!is_null($acl['dealertype'])) {
                if ($bNeedSubOp) {
                    $sqlWhere .= " AND ";
                }
                $sqlWhere .= sprintf("x_a.dealertype_c = '%s'", $acl['dealertype']);
                $bNeedSubOp = true;
            }
            //if(is_null($location) and is_null($region) and is_null($slsm) and is_null($dealerType)) { /* no criteria specified, include odd custids */
            if (!is_null($acl['custid'])) {
                if ($bNeedSubOp) {
                    $sqlWhere .= " AND ";
                }
                $sqlWhere .= sprintf("dsls_dailysales.custid = %d", $acl['custid']);
                $bNeedSubOp = true;
            }
            //}
            $sqlWhere .= ")";
            $bNeedOp = true;
        }
        if (count($slsmInSqlWhere) > 0) {
            $sqlWhere .= " OR x_a.slsm_c IN (" . implode(',', $slsmInSqlWhere) . ")";
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
                $sqlWhere .= " dsls_dailysales.loc = '$location'";
            } else {
                $sqlWhere .= " dsls_dailysales.loc IN(";
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
                $sqlWhere .= " dsls_dailysales.region = '$region'";
            } else {
                $sqlWhere .= " dsls_dailysales.region IN (";
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

        /* if 521.php ever requests just a single slsm instead of a list of slsm below the selected, use this
         * if (!is_null($slsm)) {
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
          $sqlWhere .= " ds.slsm IN (";
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
          } */

        if (!is_null($slsm)) {
            if ($bNeedOp) {
                $sqlWhere .= " AND";
            } else {
                $bNeedOp = true;
            }
            if (!is_array($slsm)) {
                $sqlWhere .= " x_a.slsm_c = '$slsm'";
            } else {
                $sqlWhere .= " x_a.slsm_c IN (";
                $bFirstReg = true;
                foreach ($slsm as $slsmno) {
                    if ($bFirstReg) {
                        $bFirstReg = false;
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
                $sqlWhere .= " x_a.dealertype_c = '$dealerType'";
            } else {
                $sqlWhere .= " x_a.dealertype_c IN (";
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
                . ' WHERE x_a.deleted = 0 AND ((x_a.slsm_c) Not In (20,232)) AND ((x_a.custtype_c) Not In (\'AFFL\',\'TRAV\')) AND (' . $sqlWhere . ')'
        ;
        return $h;
    }
    
    function acl_where() {
                global $current_user;
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
    
    protected function user_add_on($is_user_id) {

        if (!$is_user_id) {
            return;
        }

        return ''
                . ' AND x_m.assigned_user_id="' . $this->user_id . '" '
        ;
    }

    protected function scripts_for_display() {
        return '<script language="javascript">
                    function get_date_for_table(){
                        $(".yui-skin-sam-fmp-sales").find("#panel").slideUp("fast");
                        $("#load_gif").show();
                        var url = "index.php?module=DSls_DailySales&action=ProcessTable";
                        var input_value = $("#fmp_slsm_input").val();
                        if(input_value.length == 0){
                            var select_slsm = $("#fmprep_slsm_tree option:selected").val();
			    var selected_slsm_array = Array();
			    $("#fmprep_slsm_tree option:selected").each(function (i, k) {
				selected_slsm_array[i] = $(this).val();
			    });
			    select_slsm = selected_slsm_array.join(";");
                            }else{
                            var select_slsm = $("#fmprep_slsm_tree_search option:selected").val();
			    
			    var selected_slsm_array = Array();
			    $("#fmprep_slsm_tree_search option:selected").each(function (i, k) {
				selected_slsm_array[i] = $(this).val();
			    });
			    select_slsm = selected_slsm_array.join(";");
                            }
                        var select_reg_loc = $("#fmp_reg_loc option:selected").val();
			var selected_reg_loc_array = Array();
			$("#fmp_reg_loc option:selected").each(function (i, k) {
				selected_reg_loc_array[i] = $(this).val();
			});
			select_reg_loc = selected_reg_loc_array.join(";");
			
			
                        var select_dealer = $("#fmp_dealer_type option:selected").val();
			var selected_dealertype_array = Array();
			$("#fmp_dealer_type option:selected").each(function (i, k) {
				selected_dealertype_array[i] = $(this).val();
			});

			select_dealer = selected_dealertype_array.join(";");
                            var upd = 1;
                            $.post(url, {update: upd, slsm_num: select_slsm, reg_loc: select_reg_loc, dealer: select_dealer}, function(data){
                                $("#load_gif").hide();
                                $("#getResponce").html(data);
                            });
                        }
                    function fmp_slsm_list_quick_search(input_val){
                        if(input_val.length != 0){
                            $("#box_for_slsm_first").hide();
                            var new_select = "";
                            new_select += \'<select id="fmprep_slsm_tree_search" onclick="javaScript:get_date_for_table()" size="15" multiple="multiple" style="width: 340px;">\';
                            new_select += \'<option value="all" style="border-bottom: 2px solid grey;">ALL</option>\';
                            $.each($("#fmprep_slsm_tree option"), function(){
                                var option_val = this.text;
                                if(option_val.indexOf(input_val.toUpperCase()) + 1) {
                                      new_select += \'<option value="\'+this.value+\'">\'+this.text+\'</option>\';
                                    }
                                });
                            new_select += \'</select>\';
                            $("#box_for_slsm_second").show();
                            $("#box_for_slsm_second").html(new_select);
                           }else{
                               $("#box_for_slsm_second").hide();
                               $("#box_for_slsm_first").show();
                           }
                    }
                    function loadCreditsListTable(day,startDateF,endDateF){
                          var url = "index.php?module=DSls_DailySales&action=getSalesSummaryCredits";
                                 var dayCredits = "";
                                 if(typeof(day) != "undefined"){
                                    if(day == "current-day-credits"){
                                        dayCredits = "current";
                                        var currDateR = new Date();
                                        $("#ui-dialog-title-current_day_credits_dialog").html("Current Day Credits");
                                    }
                                     if(day == "previous-day-credits"){
                                        dayCredits = "previous";
                                        var currDateR = new Date(new Date().getTime() - 1000*60*60*24);
                                        $("#ui-dialog-title-current_day_credits_dialog").html("Previous Day\'s Sales");
                                    }  
                                 }
                                var input_value = $("#fmp_slsm_input").val();
                                if(input_value.length == 0){
                                    var select_slsm = $("#fmprep_slsm_tree option:selected").val();
                                    var selected_slsm_array = Array();
                                    $("#fmprep_slsm_tree option:selected").each(function (i, k) {
                                        selected_slsm_array[i] = $(this).val();
                                    });
                                    select_slsm = selected_slsm_array.join(";");
                                    }else{
                                    var select_slsm = $("#fmprep_slsm_tree_search option:selected").val();

                                    var selected_slsm_array = Array();
                                    $("#fmprep_slsm_tree_search option:selected").each(function (i, k) {
                                        selected_slsm_array[i] = $(this).val();
                                    });
                                    select_slsm = selected_slsm_array.join(";");
                                    }
                                 $("#current_day_credits_list").dataTable({
                                                "bJQueryUI": true,
                                                "bDestroy": true,
                                                "bProcessing": true,
                                                "bServerSide": true,
                                                "bAutoWidth": false, 
                                                "sAjaxSource": url,
                                                "sDom": \'<"H"l<"#range_date">fr>t<"F"ip>\',
                                                "iDisplayLength": 99999999,
                                                              "oLanguage": {
                                                "sLengthMenu": \'Show <select>\' +
                                                                            \'<option value="10">10</option>\' +
                                                                            \'<option value="20">20</option>\' +
                                                                            \'<option value="30">30</option>\' +
                                                                            \'<option value="40">40</option>\' +
                                                                            \'<option value="50">50</option>\' +
                                                                            \'<option value="100">100</option>\' +
                                                                            \'<option value="200">200</option>\' +
                                                                            \'<option value="99999999">All</option>\' +
                                                                            \'</select> entries\'
                                                },
                                                "sPaginationType": "full_numbers",
                                                "fnServerData": function ( sSource, aoData, fnCallback ) {  
                                                                                    var stD = startDateF != "" ? $.datepicker.formatDate("mm/dd/yy", startDateF) : "";
                                                                                    var enD = endDateF != "" ? $.datepicker.formatDate("mm/dd/yy", endDateF) : "";
                                                                                    aoData.push( { name: "startDate", value: stD } );
                                                                                    aoData.push( { name: "endDate", value: enD } );
                                                                                    aoData.push( { name: "dayCredits", value: dayCredits } );
                                                                                     aoData.push( { name: "slsm_num", value: select_slsm } );
                                                                                    $.getJSON( sSource, aoData, function (json) { 
                                                                                            fnCallback(json)
                                                                                    } );
                                                                            }
                                  });
                                    var stD = startDateF != "" ? $.datepicker.formatDate("mm/dd/yy", startDateF) : $.datepicker.formatDate("mm/dd/yy", currDateR);
                                    var enD = endDateF != "" ? $.datepicker.formatDate("mm/dd/yy",  endDateF) :  $.datepicker.formatDate("mm/dd/yy", currDateR);
                                  $("#range_date").css({"padding-right":"5%","float":"left"});
                        $("#range_date").addClass("yui-skin-custom");
                      $("#range_date").append("<span class=\'yui-button-fmp-sales yui-split-button-fmp-sales\' id=\'cred_date_range_show\' style=\'border-width: 1px 1px;\'><span class=\'first-child-fmp-sales\'>"+
	  "<button type=\'button\'>Date Range</button>"+   
		"<div id=\'cred_date_range\' style=\'display: none; padding: 3px; position: absolute; background-color: #FFFFFF; border: 1px solid #94C1E8;\'>"+
		  "<label for=\'cred_date_start\'>From</label>"+
		  "<div id=\'date_from\'>"+
			  "<input class=\'text range-date-inp\' name=\'cred_date_start\' size=\'12\' maxlength=\'10\' id=\'cred_date_start\' value=\'"+stD+"\'>"+
		  "</div>"+
		  "<label for=\'cred_date_end\'>To</label>"+
		  "<div id=\'date_end\'>"+
			  "<input class=\'text range-date-inp\' name=\'cred_date_end\' size=\'12\' maxlength=\'10\' id=\'cred_date_end\' value=\'"+enD+"\'>"+
		  "</div>"+
	  "</div>");
                        $("#cred_date_range_show").hover(
                          function(){
                                  $("#cred_date_range_show").find("#cred_date_range").stop(true, true);
                                  $("#cred_date_range_show").find("#cred_date_range").slideDown("slow");      
                          },
                          function() {
                                if ( $("#ui-datepicker-div").css("display") == "none" || $("#ui-datepicker-div").html() == "")
                                  $("#cred_date_range_show").find("#cred_date_range").slideUp("slow");  
                        });
                     startDateF = startDateF != "" ? startDateF : currDateR;
                     endDateF = endDateF != "" ? endDateF :  currDateR;
                        $( "#cred_date_start" ).datepicker({
                          dateFormat: "mm/dd/yy",
                          defaultDate: startDateF,
                          maxDate: endDateF,
                          numberOfMonths: 3,
                          showOn: "button",
                          buttonImage: "themes/default/images/jscalendar.gif",
                          buttonImageOnly: true,
                          showButtonPanel: true,
                          closeText: "Clear",
                          beforeShow: function( input, inst ) {
                                setTimeout(function() {
                                  $(".ui-datepicker-close").click(function(){
                                        DP_jQuery.datepicker._clearDate(input);
                                  });
                                }, 10 );
                          },
                          onChangeMonthYear:function() {
                                var input = this;
                                setTimeout(function() {
                                  $(".ui-datepicker-close").click(function(){
                                        DP_jQuery.datepicker._clearDate(input);
                                  });
                                }, 10 );
                          },
                          onSelect: function( selectedDate ) {
                               loadCreditsListTable(day,new Date(selectedDate),endDateF);
                          }
                        });
                        $( "#cred_date_end" ).datepicker({
                          dateFormat: "mm/dd/yy",
                          defaultDate: endDateF,
                          minDate: startDateF,
                          numberOfMonths: 3,
                          showOn: "button",
                          buttonImage: "themes/default/images/jscalendar.gif",
                          buttonImageOnly: true,
                          showButtonPanel: true,
                          closeText: "Clear",
                          beforeShow: function( input, inst ) {
                                setTimeout(function() {
                                  $(".ui-datepicker-close").click(function(){
                                        DP_jQuery.datepicker._clearDate(input);
                                  });
                                }, 10 );
                          },
                          onChangeMonthYear:function() {
                                var input = this;
                                setTimeout(function() {
                                  $(".ui-datepicker-close").click(function(){
                                        DP_jQuery.datepicker._clearDate(input);
                                  });
                                }, 10 );
                          },
                          onSelect: function( selectedDate ) {
                               loadCreditsListTable(day,startDateF,new Date(selectedDate));
                          }
                        });
                        $("#ui-datepicker-div").css("z-index", "999999");
                    }
                    $(document).ready(function(){

                        $("#slsm_list_show").hover(
                                function(){
                                    $("#slsm_list_show").find("#slsm_panel").stop(true, true);
                                    $("#slsm_list_show").find("#slsm_panel").slideDown("slow");
                                },
                                function() {
                                    $("#slsm_list_show").find("#slsm_panel").slideUp("slow");
                               } 
                            );
                        $("#area_list_show").hover(
                                function(){
                                    $("#area_list_show").find("#area_panel").stop(true, true);
                                    $("#area_list_show").find("#area_panel").slideDown("slow");
                                },
                                function() {
                                    $("#area_list_show").find("#area_panel").slideUp("slow");
                                }
                            );
                        $("#dealer_list_show").hover(
                                function(){
                                    $("#dealer_list_show").find("#dealer_panel").stop(true, true);
                                    $("#dealer_list_show").find("#dealer_panel").slideDown("slow");
                                },
                                function() {
                                    $("#dealer_list_show").find("#dealer_panel").slideUp("slow");
                                }
                            );

                             $("#current-day-credits, #previous-day-credits").live("dblclick",function(){
                                $("#current_day_credits_dialog").dialog("open");
                               loadCreditsListTable(this.id,"","");
                            });
                            $("#current_day_credits_dialog").dialog({
                                dialogClass: "current_day_credits_dialog_class",
                                bgiframe: false,
                                autoOpen: false,
                                height: 600,
                                width: 950,
                                modal: true,
                            });
                    })
                </script>';
    }

    protected function construct_query_for_daily_sales() {
        $query = "  SELECT  sum(previous_day_sales) AS previous_day_sales,
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
                    INNER JOIN accounts AS x_a
                    ON dsls_dailysales.custid=x_a.custid_c ";
        return $query;
    }

    protected function month_business_days($yr, $mo, $workday) {
        $query = "  SELECT count(*) AS num FROM dsls_calendar where yr = '" . $yr . "' and mo = '" . $mo . "' and workday = '" . $workday . "'";
        return $query;
    }

    protected function first_month_business_days_through_yesterday($from, $to) {
        $query = "  SELECT count(*) AS num FROM dsls_calendar where cal_date between '" . $from . "' and '" . $to . "' and workday = '-1' ";
        return $query;
    }

    function dsls_sales_summary($record_day, $record_type_slsm = "SLSM", $record_id_slsm, $record_id_region, $record_id_location) {
        global $current_user;
        $acl_granted = $this->acl_where_summary();

        $record_id_slsm = !in_array($record_id_slsm, array('undefined', 'all', '')) ? explode(";", $record_id_slsm) : null;
        $record_id_region = !in_array($record_id_region, array('undefined', 'all', '')) ? explode(";", "r" . $record_id_region) : null;
        $slsm_area_obj = new fmp_Param_RegLoc($current_user->id);
        $reg_loc_object = $slsm_area_obj->init($current_user->id);
        //print_r();
        if (!is_null($record_id_region)) {
            foreach ($record_id_region as $reg_loc_value) {
                if (substr($reg_loc_value, 0, 1) == "r") {
                    $region_array[substr($reg_loc_value, 1)] = substr($reg_loc_value, 1);
                    $locations_available = array();
                    $locations_available = $slsm_area_obj->compile__available_regions_below(array(0 => null), substr($reg_loc_value, 1));

                    foreach ($locations_available as $region_value => $locations_value) {


                        $location_array[$locations_value] = $locations_value;
                    }
                } else {
                    $location_array[$reg_loc_value] = $reg_loc_value;
                }
            }
        }
        $record_id_location = !in_array($record_id_location, array('undefined', 'all', '')) ? explode(";", $record_id_location) : null;

        if (!is_null($record_id_location)) {
            foreach ($record_id_location as $reg_loc_value) {
                if (substr($reg_loc_value, 0, 1) == "r") {
                    $region_array[substr($reg_loc_value, 1)] = substr($reg_loc_value, 1);
                    $locations_available = array();
                    $locations_available = $slsm_area_obj->compile__available_regions_below(array(0 => null), substr($reg_loc_value, 1));

                    foreach ($locations_available as $region_value => $locations_value) {


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
        if (!is_null($record_id_slsm) xor !is_null($record_id_region) xor !is_null($record_id_location)) {

            $query = "  SELECT
                        sum(sales) as sales,
                        sum(credits) as credits
                    FROM dsls_sales_summary
                    WHERE record_date = '" . $record_day . "'
                    ";

            if (!is_null($record_id_slsm)) {

                if (count($record_id_slsm) == 1) {
                    $record_id_slsm = $this->getSlsmBelow($_SESSION['fmp_slsm'], $record_id_slsm[0]);
                }

                if (count($acl_granted) != 0 && count($acl_granted['slsm']) > 0) {
                    $query .= "AND (record_type = '" . $record_type_slsm . "' AND record_id IN (" . implode(', ', $acl_granted['slsm']) . ") AND record_id IN (" . implode(', ', $record_id_slsm) . "))";
                } else {
                    $query .= "AND (record_type = '" . $record_type_slsm . "' AND (0) )";
                }
            } else {
                /* if(count($acl_granted) != 0 && count($acl_granted['slsm']) > 0) {
                  $query .= "AND (record_type = '".$record_type_slsm."' AND record_id IN (" . implode(', ', $acl_granted['slsm']) . ") )";
                  } else {
                  $query .= "AND (record_type = '".$record_type_slsm."' AND (0) )";
                  } */
            }

            /* if(!is_null($record_id_region) && isset($region_array)) {
              if(count($acl_granted) != 0 && count($acl_granted['region']) > 0) {
              $query .= " AND (record_type='REGION' AND record_id IN (" . implode(", ", $acl_granted['region']) . ") AND record_id IN (".implode(", ", $region_array). "))";
              //print_r($query);
              } else {
              $query .= " AND (record_type='REGION' AND (0) )";
              }
              } */

            if (isset($location_array)) {
                if (count($acl_granted) != 0 && count($acl_granted['location']) > 0) {
                    $query .= " AND (record_type='LOCATION' AND record_id IN (" . implode(", ", $acl_granted['location']) . ") AND record_id IN (" . implode(", ", $location_array) . "))";
                    //print_r()
                } else {
                    $query .= " AND (record_type='LOCATION' AND record_id IN (" . implode(", ", $location_array) . "))";
                }
            }
            if (is_null($record_id_slsm) && !isset($location_array) && !isset($region_array)) {
                //print_r("regloc not selected");
                if (count($acl_granted) != 0 && count($acl_granted['region']) > 0) {
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
                    WHERE record_date = '" . $record_day . "'
                    AND (record_type = 'SLSM' ";

            if (count($acl_granted) != 0 && count($acl_granted['slsm']) > 0) {
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

    protected function sales_today_previous($date_sales_summ, $type, $record_id_slsm, $record_id_region, $record_id_location) {
        $db = &PearDatabase::getInstance();
        $sales = 0;
        $credits = 0;
        $query_sales_summ = $db->query($this->dsls_sales_summary($date_sales_summ, $type, $record_id_slsm, $record_id_region, $record_id_location));
        $res_sales_summ = $db->fetchByAssoc($query_sales_summ);
//        while($res_sales_summ = $db->fetchByAssoc($query_sales_summ)) {
        $sales += $res_sales_summ['sales'];
        $credits += $res_sales_summ['credits'];
//        }

        $net_sales = $sales - $credits;

        $result = array();
        $result['sales'] = $sales;
        $result['credits'] = $credits;
        //$result['net_sales'] = $net_sales;
        $result['net_sales'] = $sales + $credits;
        return $result;
    }

    function red_color_text($value = 0) {
        if ($value < 0) {
            return '<p style="color: red">(' . '$' . number_format(abs($value), 0, '.', ',') . ')</p>';
        } else {
            return '<p>' . '$' . number_format($value, 0, '.', ',') . '</p>';
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
    function get_dealer_type($dealer_list) {
        $select_creater = '<select id="fmp_dealer_type" onchange="javaScript:get_date_for_table()" size="10" multiple="multiple" style="width: 170px;">';
        $select_creater .= '<option value="all" style="border-bottom: 2px solid grey;">ALL</option>';
        foreach ($dealer_list as $key => $value) {
            $select_creater .= '<option value="' . $key . '">' . $value . '</option>';
        }
        $select_creater .= '</select>';
        return $select_creater;
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
