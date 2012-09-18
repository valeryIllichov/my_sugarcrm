<?php
class fmp_Param_FieldList_Activity {
    const F_SECT_ACTIVITIES = 1;
    const F_SECT_DISCPTS = 2;
    const F_SECT_OUTCOME = 3;
    const F_SECT_OPP = 4;

    const F_TYPE_SINGLE = 1;
    const F_TYPE_MULTIPLE = 2;
    
    public static $F_SECT = array(
        self::F_SECT_ACTIVITIES => array(
            'name' => 'ACTIVITIES', 
            'class' => '',
            'type' => self::F_TYPE_MULTIPLE
        ),
        self::F_SECT_DISCPTS     => array(
            'name' => 'PRE-CALL PLAN',
            'class' => 'discuss-points',
            'type' => self::F_TYPE_SINGLE
        ),
        self::F_SECT_OUTCOME   => array(
            'name' => 'OUTCOME', 
            'class' => 'outcome',
            'type' => self::F_TYPE_SINGLE
        ),
        self::F_SECT_OPP           => array(
            'name' => 'OPPORTUNITIES', 
            'class' => 'opportunities',
            'type' => self::F_TYPE_MULTIPLE
        ),
    );

    public static $FF = array(
        'sales_rep_name' => 'SALES REP',
        'account_name' => 'CUSTOMER',
        'contact_name' => 'CONTACT'
    );

    public static $F = array(
        'customer_no'               => array(
            'width' => 50, 
            'name' => 'CUST<br>ACCT #', 
            'type' => self::F_SECT_ACTIVITIES,
            'class' => '',
            'sort' => 1
        ),
        'account_name'             => array(
            'width' => 200, 
            'name' => 'CUSTOMER NAME', 
            'type' => self::F_SECT_ACTIVITIES,
            'class' => '',
            'sort' => 1
        ),
        'parent_type'                => array(
            'width' => 140, 
            'name' => 'RELATED TO / TYPE', 
            'type' => self::F_SECT_ACTIVITIES,
            'class' => '',
            'sort' => 0
        ),
        'parent_name'                => array(
            'width' => 260, 
            'name' => 'RELATED TO / NAME', 
            'type' => self::F_SECT_ACTIVITIES,
            'class' => '',
            'sort' => 0
        ),
        'sales_rep_id'                => array(
            'width' => 60, 
            'name' => 'SALES<br>REP ID', 
            'type' => self::F_SECT_ACTIVITIES,
            'class' => '',
            'sort' => 0
        ),
        'sales_rep_name'     => array(
            'width' => 220, 
            'name' => 'SALES REP<br>LAST NAME', 
            'type' => self::F_SECT_ACTIVITIES,
            'class' => '',
            'sort' => 1
        ),
        'user_login'                   => array(
            'width' => 130, 
            'name' => 'USER LOGIN', 
            'type' => self::F_SECT_ACTIVITIES,
            'class' => '',
            'sort' => 0
        ),
        'type_of_activity'          => array(
            'width' => 76, 
            'name' => 'TYPE OF<br>ACTIVITY', 
            'type' => self::F_SECT_ACTIVITIES,
            'class' => '',
            'sort' => 1
        ),
        'held_not_held'              => array(
            'width' => 55, 
            'name' => 'STATUS', 
            'type' => self::F_SECT_ACTIVITIES,
            'class' => '',
            'sort' => 1
        ),
        'date_time_of_activity'   => array(
            'width' => 85, 
            'name' => 'DATE/TIME<br>OF ACTIVITY', 
            'type' => self::F_SECT_ACTIVITIES,
            'class' => 'date-time-cell',
            'sort' => 1
        ),
        'subject'   => array(
            'width' => 120, 
            'name' => 'SUBJECT', 
            'type' => self::F_SECT_ACTIVITIES,
            'class' => '',
            'sort' => 1
        ),

        'contact_name'   => array(
            'width' => 180, 
            'name' => 'CONTACT', 
            'type' => self::F_SECT_ACTIVITIES,
            'class' => '',
            'sort' => 1
        ),
        'contact_address'   => array(
            'width' => 180, 
            'name' => 'ADDRESS', 
            'type' => self::F_SECT_ACTIVITIES,
            'class' => '',
            'sort' => 0
        ),
        'contact_phone'   => array(
            'width' => 100, 
            'name' => 'PHONE', 
            'type' => self::F_SECT_ACTIVITIES,
            'class' => '',
            'sort' => 0
        ),

        'points'                        => array(
            'width' => 300, 
            'name' => 'PRE-CALL PLAN',
            'type' => self::F_SECT_DISCPTS,
            'class' => '',
            'sort' => 1
        ),

        'outcome'                     => array(
            'width' => 300, 
            'name' => 'OUTCOME', 
            'type' => self::F_SECT_OUTCOME,
            'class' => '',
            'sort' => 1
        ),

        'product'                      => array(
            'width' => 160,
            //'name' => 'PRODUCT',
            'name' => 'OPPORTUNITY NAME',
            'type' => self::F_SECT_OPP,
            'class' => '',
            'sort' => 1
        ),
        'sales'                         => array(
            'width' => 110,
            //'name' => 'SALES',
            'name' => 'ESTIMATED ANNUALIZED SALES',
            'type' => self::F_SECT_OPP,
            'class' => '',
            'sort' => 1
        ),
        'estimated_monthly_sales'        => array(
            'width' => 72,
            'name' => 'ESTIMATED MONTHLY SALES',
            'type' => self::F_SECT_OPP,
            'class' => '',
            'sort' => 1
        ),
        'sales_stage'                => array(
            'width' => 100, 
            'name' => 'SALES STAGE', 
            'type' => self::F_SECT_OPP,
            'class' => '',
            'sort' => 1
        ),
        'additional_opp_info'      => array(
            'width' => 250, 
            'name' => 'ADDITIONAL OPP<br>INFORMATION', 
            'type' => self::F_SECT_OPP,
            'class' => '',
            'sort' => 1
        ), 
        'opupdate'                   => array(
            'width' => 200, 
            'name' => 'UPDATE', 
            'type' => self::F_SECT_OPP,
            'class' => '',
            'sort' => 1
        ),
        'date_closed'               => array(
            'width' => 74, 
            'name' => 'CLOSING DATE', 
            'type' => self::F_SECT_OPP,
            'class' => '',
            'sort' => 1
        ), 
        'probability'                 => array(
            'width' => 37, 
            'name' => 'PR %', 
            'type' => self::F_SECT_OPP,
            'class' => '',
            'sort' => 1
        ),
        'op_date_modified'        => array(
            'width' => 72, 
            'name' => 'MODIFIED', 
            'type' => self::F_SECT_OPP,
            'class' => 'right',
            'sort' => 1
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
                if($_REQUEST['format'] == 'CSV'){
                $preference_sel = $current_user->getPreference($report_id.'fmp_csv_report_fields_activity');
                if(is_array($preference_sel) && count($preference_sel) !=0) $sel = $preference_sel;
                } else {
                $preference_sel = $current_user->getPreference($report_id.'fmp_html_report_fields_activity');
                if(is_array($preference_sel) && count($preference_sel) !=0) $sel = $preference_sel;
                }
                
            }


        if ( !isset($_REQUEST['run']) || ($_REQUEST['run'] != 'true') ) {
            return $sel;
        }

       
         

        $sel = $this->init();

        foreach($sel as $opt_id => $v) {
            if (isset($_REQUEST['fmp_report_fields_activity'][$opt_id])) {
                continue;
            }

            unset($sel[$opt_id]);
        }

        if ( isset($_REQUEST['run']) && ($_REQUEST['run'] == 'true')) {
            if($_REQUEST['format'] == 'CSV'){
            $current_user->setPreference($report_id.'fmp_csv_report_fields_activity', $sel);
            } else {
            $current_user->setPreference($report_id.'fmp_html_report_fields_activity', $sel);
            }
            $current_user->setPreference($report_id.'modified', $current_date+$settings_duration);
        }
        return $sel;
    }

    public function html($desc) 
    {
        $sel = $this->r_fields();
       
        $skip = array(
            'sales_rep_id', 'user_login', 
            'parent_type', 'parent_name', 
            'contact_address', 'contact_phone'
        );

        $h = array();
        foreach(self::$F as $opt_id=>$o) {
                if (in_array($opt_id, $skip)) {
                continue;
            }

            $disabled = '';
            $checked = '';
            if (isset($sel[$opt_id])) {
                $checked = ' checked="checked"';
            }
            $type = $o['type'];

            if($_REQUEST['format'] == 'CSV' && $type == self::F_SECT_OPP){
                $checked = '';
                 $disabled = ' disabled="disabled"';
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

            $h[$type][] = ''
                . '<div style="float: left; width: 220px;">'
                    . '<input type="checkbox" name="fmp_report_fields_activity[' . $opt_id . ']" value="true"' . $checked . $disabled . ' />' . $o['name']
                . '</div>'
                ;
        }

        foreach($h as $type=>$opts) {
            $h[$type] = ''
                . '<fieldset>'
                    . '<legend>' . self::$F_SECT[$type]['name'] . '</legend>'
                    . implode('', $opts)
                    . '<div style="clear: both;"></div>' 
                . '</fieldset>'
                ;
        }

        $sbox = implode('', $h) ;

        return <<<EOJS
<tr>
 <td class="tabDetailViewDL">Columns</td>
 <td class="tabDetailViewDF" colspan="3">
    {$sbox}
 </td>
</tr>
EOJS;
    }

    public function html_header() 
    {
        $sel = $this->r_fields();

        $col_id = 'sales_rep_id';
        if ($sel[$col_id]) {
            unset($sel[$col_id]);
        }

        $col_id = 'user_login';
        if ($sel[$col_id]) {
            unset($sel[$col_id]);
        }
        
        $out_width = 0;

        $h = array();
        foreach(self::$F as $opt_id=>$o) {
            if (!isset($sel[$opt_id])) {
                continue;
            }

            $type = $o['type'];
            $h[$type][$opt_id] = $o;
        }

        $out_r1 = array();
        $out_r2 = array();

        foreach($h as $sect_id=>$opts) {
            $sect = self::$F_SECT[$sect_id];
            
            if ($sect['class']) {
                $sect['class'] = ' ' . $sect['class'];
            }

            if ($sect['type'] == self::F_TYPE_SINGLE) {
                $opt = current($opts);
                $out_width += $opt['width'];

                $out_r1[] = ''
                    . '<th width="' . $opt['width'] . '" class="listViewThS1 top' . $sect['class'] . '" rowspan="2">'  
                        . $sect['name']
                    . '</th>'
                    ;
                continue;
            }

            $colspan = '';
            if (count($opts) > 1) {
                $colspan = ' colspan="' . count($opts) . '"';
            }

            $out_width_sect = 0;
            foreach($opts as $opt_id => $opt) {
                $out_width += $opt['width'];
                $out_width_sect += $opt['width'];

                if ($opt['class']) {
                    $opt['class'] = ' ' . $opt['class'];
                }

                if (isset(self::$FF[$opt_id])) {
                    $opt['name'] = self::$FF[$opt_id];
                }

                $out_r2[] = '' 
                    . '<th class="listViewThS1 bottom' . $opt['class'] . '" width="' . $opt['width'] . '">' 
                        . $opt['name'] 
                    . '</th>'
                    ;
            }
            $out_r1[] = ''
                    . '<th width="' . $out_width_sect . '" class="listViewThS1 top' . $class . '"' . $colspan . '>'
                        . $sect['name']
                    . '</th>'
                    ;

        }

        $out_html = ''
            . '<tr height="20">' . implode('', $out_r1) . '</tr>' 
            . '<tr height="20">' . implode('', $out_r2) . '</tr>'
            ;
        return array('width' => $out_width, 'html' => $out_html); 
    }
}