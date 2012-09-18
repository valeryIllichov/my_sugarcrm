<?php
class fmp_Param_FieldList_Opportunity {

    public static $FF = array(
        'sales_rep_last_name' => 'SALES REP',
        'contact_name' => 'CONTACT'
    );

    public static $F = array(
        'customer_no'               => array(
            'width' => 50,
            'name' => 'CUST<br>ACCT #',
            'class' => '',
            'sort' => 0,
            'sort_q' => ''
        ),
        'account_name'             => array(
            'width' => 200,
            'name' => 'CUSTOMER NAME',
            'class' => '',
            'sort' => 1,
            'sort_q' => 'x_a.name'
        ),
        'sales_rep_id'                => array(
            'width' => 60,
            'name' => 'SALES<br>REP ID',
            'class' => '',
            'sort' => 0,
            'sort_q' => ''
        ),
        'user_login'                   => array(
            'width' => 180,
            'name' => 'USER LOGIN',
            'class' => '',
            'sort' => 0,
            'sort_q' => 'x_u.user_name'
        ),
        'sales_rep_last_name'     => array(
            'width' => 220,
            'name' => 'SALES REP<br>LAST NAME',
            'class' => '',
            'sort' => 1,
            'sort_q' => 'x_slsm.lastname'
        ),

        'contact_name'   => array(
            'width' => 180,
            'name' => 'CONTACT',
            'class' => '',
            'sort' => 0,
            'sort_q' => ''
        ),
        'contact_address'   => array(
            'width' => 180,
            'name' => 'ADDRESS',
            'class' => '',
            'sort' => 0,
            'sort_q' => ''
        ),
        'contact_phone'   => array(
            'width' => 100,
            'name' => 'PHONE',
            'class' => '',
            'sort' => 0,
            'sort_q' => ''
        ),


        'product'                      => array(
            'width' => 200,
            //'name' => 'PRODUCT',
            'name' => 'OPPORTUNITY NAME',
            'class' => '',
            'sort' => 1,
            'sort_q' => 'x_o.name'
        ),

        'product_line' => array(
            'width' => 400,
            'name' => 'LINE / CATEGORY/ PRICE CODE*',
            'class' => '',
            'sort' => 0,
            'sort_q' => ''
        ),

//        'sales'                         => array(
//            'width' => 90,
//            'name' => 'ESTIMATED ANNUALIZED SALES',
//            'class' => '',
//            'sort' => 0,
//            'sort_q' => ''
//        ),

        'sales'                         => array(
            'width' => 100,
            'name' => 'ESTIMATED ANNUALIZED SALES',
            'class' => '',
            'sort' => 1,
            'sort_q' => ''
        ),

        'monthly_sales'                         => array(
            'width' => 100,
            'name' => 'ESTIMATED MONTHLY SALES',
            'class' => '',
            'sort' => 0,
            'sort_q' => ''
        ),
        'gp_dollar' => array(
            'width' => 80,
            'name' => 'Expected GP$',
            'class' => '',
            'sort' => 0,
            'sort_q' => ''
        ),
        'gp_perc' => array(
            'width' => 80,
            'name' => 'Expected GP%',
            'class' => '',
            'sort' => 0,
            'sort_q' => ''
        ),

        'prev12mo' => array(
            'width' => 100,
            'name' => 'Prev 12-mo avg',
            'class' => '',
            'sort' => 0,
            'sort_q' => ''
        ),
        'rolling' => array(
            'width' => 100,
            'name' => 'Rolling',
            'class' => '',
            'sort' => 0,
            'sort_q' => ''
        ),
        'mtd' => array(
            'width' => 100,
            'name' => 'MTD',
            'class' => '',
            'sort' => 0,
            'sort_q' => ''
        ),
        'ytd' => array(
            'width' => 100,
            'name' => 'YTD',
            'class' => '',
            'sort' => 0,
            'sort_q' => ''
        ),

        'sales_stage'                => array(
            'width' => 85,
            'name' => 'SALES STAGE',
            'class' => '',
            'sort' => 1,
            'sort_q' => 'x_o.sales_stage'
        ),
        'additional_opp_info'      => array(
            'width' => 250,
            'name' => 'ADDITIONAL OPP<br>INFORMATION',
            'class' => '',
            'sort' => 1,
            'sort_q' => 'x_o.description'
        ),
        'opupdate'                   => array(
            'width' => 200,
            'name' => 'UPDATE',
            'class' => '',
            'sort' => 1,
            'sort_q' => 'x_oc.opupdate_c'
        ),
        'date_closed'               => array(
            'width' => 74,
            'name' => 'CLOSING DATE',
            'class' => '',
            'sort' => 1,
            'sort_q' => 'x_o.date_closed'
        ),
        'probability'                 => array(
            'width' => 37,
            'name' => 'PR %',
            'class' => '',
            'sort' => 1,
            'sort_q' => 'x_o.probability'
        ),
        'op_date_modified'        => array(
            'width' => 72,
            'name' => 'MODIFIED',
            'class' => 'right',
            'sort' => 1,
            'sort_q' => 'x_o.date_modified'
        )
    );

    function __construct() {}

    public function init()
    {
        $sel = array();
        foreach(self::$F as $opt_id => $v) {
            $sel[$opt_id] = true;
        }

        return $sel;
    }

    public function r_fields()
    {
        $sel = $this->init();

        global  $current_user;
        $focus =& new QueryTemplate();

            if(isset($_REQUEST['record'])) {
            $focus->retrieve($_REQUEST['record']);
            }



            $report_id = $_REQUEST['record'];
            $report_name =strtolower($focus->name);
            $settings_duration = 0;
            switch ($report_name){
                case 'opportunities':
                    if($current_user->getPreference('ORPersonalSettings') != null){
                        $settings_duration = $current_user->getPreference('ORPersonalSettings');
                        }
                    else{
                        $settings_duration = 30*24*60*60;
                        }
                    break;
                default:
                    if($current_user->getPreference('SASRPersonalSettings') != null ){
                        $settings_duration = $current_user->getPreference('SASRPersonalSettings');
                        }
                    else{
                        $settings_duration = 7*24*60*60;
                        }
                    break;
            }


            $current_date = mktime();
            $settings_expired_time = (int) $current_user->getPreference($report_id.'modified');
            if($settings_expired_time == 0) $settings_expired_time = $current_date;

            if ($settings_expired_time >= $current_date){
                $preference_sel = $current_user->getPreference($report_id.'fmp_report_fields_opportunity');
                if(is_array($preference_sel) && count($preference_sel) !=0) $sel = $preference_sel;

            }


        if ( !isset($_REQUEST['run']) || ($_REQUEST['run'] != 'true') ) {
            return $sel;
        }

        $sel = $this->init();

        foreach($sel as $opt_id => $v) {
            if (isset($_REQUEST['fmp_report_fields_opportunity'][$opt_id])) {
                continue;
            }

            unset($sel[$opt_id]);
        }

        $current_user->setPreference($report_id.'fmp_report_fields_opportunity', $sel);
        $current_user->setPreference($report_id.'modified', $current_date+$settings_duration);
        return $sel;
    }

    public function html($desc)
    {
        $sel = $this->r_fields();

        $skip = array(
            'sales_rep_id', 'user_login',
            'contact_address', 'contact_phone',
            'gp_dollar', 'gp_perc'
        );

        $h = array();
        foreach(self::$F as $opt_id=>$o) {
            if (in_array($opt_id, $skip)) {
                continue;
            }

            $checked = '';
            if (isset($sel[$opt_id])) {
                $checked = ' checked="checked"';
            }

            if (isset(self::$FF[$opt_id])) {
                $o['name'] = self::$FF[$opt_id];
            } else {
                $o['name'] = explode('<br>', $o['name']);
                foreach($o['name'] as $k => $v) {
                    $o['name'][$k] = trim($v);
                }
                $o['name'] = implode(' ', $o['name']);
            }

            $h[] = ''
                . '<div style="float: left; width: 220px;">'
                    . '<input type="checkbox" name="fmp_report_fields_opportunity[' . $opt_id . ']" value="true"' . $checked . ' />' . $o['name']
                . '</div>'
                ;
        }

        $sbox = ''
                . '<fieldset>'
                    . '<legend>Opportunity</legend>'
                    . implode('', $h)
                    . '<div style="clear: both;"></div>'
                . '</fieldset>'
                ;
        return <<<EOJS
<tr>
 <td class="tabDetailViewDL">{$desc}</td>
 <td class="tabDetailViewDF" colspan="3">
    {$sbox}
 </td>
</tr>
EOJS;

    }

    public function html_header()
    {
        /*
         * It may be best to use fmp_Param_FieldList_Activity class
         * with one section (named 'OPPORTUNITIES') in future
         * */
        $sel = $this->r_fields();
        $out_width = 0;

        $colspan = 0;

        $o = new fmp_Param_Showactivities();
        $showactivities = $o->r_showactivities();

        $h = array();
        foreach(self::$F as $opt_id=>$opt) {
            if (!isset($sel[$opt_id])) {
                continue;
            }

            if ($showactivities) {
                if ($opt_id == 'additional_opp_info') {
                    self::$F[$opt_id]['name'] = 'ADDITIONAL OPP INFORMATION / "Activity" Discussion Points';
                    $opt = self::$F[$opt_id];
                }
                elseif ($opt_id == 'opupdate') {
                    self::$F[$opt_id]['name'] = 'UPDATE / "Activity" Outcome';
                    $opt = self::$F[$opt_id];
                }
            }

            $out_width += $opt['width'];
            $colspan++;

            if ($opt['class']) {
                $opt['class'] = ' ' . $opt['class'];
            }

            $h[] = ''
                . '<th class="listViewThS1 bottom' . $opt['class'] . '" width="' . $opt['width'] . '">'
                    . $opt['name']
                . '</th>'
                ;
            if ($showactivities) {
                if ($opt_id == 'mtd') {
                    $out_width += 400;
                    $h[] = ''
                        . '<th class="listViewThS1 bottom' . $opt['class'] . '" width="200">'
                            . 'Activity \'Related to\' Specific Opportunity'
                        . '</th>'
                        ;

                    $h[] = ''
                        . '<th class="listViewThS1 bottom' . $opt['class'] . '" width="200">'
                            . 'Activity Date'
                        . '</th>'
                        ;
                }
            }
        }



        $out_html = ''
            . '<tr height="20">'
                . '<th width="' . $out_width . '" class="listViewThS1 top opportunities" colspan="100">OPPORTUNITIES</th>'
            . '</tr>'
            . '<tr height="20">' . implode('', $h) . '</tr>'
            ;
        return array('width' => $out_width, 'html' => $out_html);
    }
}