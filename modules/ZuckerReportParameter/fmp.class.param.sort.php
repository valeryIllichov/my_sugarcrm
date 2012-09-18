<?php
class fmp_Param_Sort {
    protected $user_id = 0;
    protected $SORT_FIELDS = array();
    protected $SORT_ORDERS = array();
    protected $def_id = '';

    function __construct($user_id, $fields, $fields_grouped, $def_id)
    {
        $this->user_id = $user_id;

        $this->SORT_FIELDS = array();
        foreach($fields as $col_id => $v) {
            if (!$v['sort']) {
                continue;
            }

            if (isset($fields_grouped[$col_id])) {
//                if($col_id = 'estimated_monthly_sales'){
//                    $col_id = 'sales';
//                }
                $v['name'] = $fields_grouped[$col_id];
            }
            
            $name = str_replace('<br>', ' ', $v['name']);
            $this->SORT_FIELDS[$col_id] = $name;
        }
        $this->SORT_ORDERS = array('asc' => 'ASC', 'desc' => 'DESC');

        $this->def_id = $def_id;
    }

    protected function r_sort_field($num = 0) 
    {
        $list = $this->SORT_FIELDS;
        $key = 'FMPREP_SSAR_SORT__FIELD';
        if ($num > 0) {
            $key .= '_' . $num;
            $list = array('' => ' -- NOT SELECTED -- ') + $this->SORT_FIELDS;
        }

        $r_sort_field = $this->def_id;
        if ($num > 0) {
            reset($list);
            $r_sort_field = key($list);
        }

        //personal settings
         global $current_user;

        $report_id = $_REQUEST['record'];
        $focus =& new QueryTemplate();

        if(isset($_REQUEST['record'])) {
        $focus->retrieve($_REQUEST['record']);
        }

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
        $reference_r_sort_field = '';
        if($settings_expired_time >= $current_date) $reference_r_sort_field = $current_user->getPreference($report_id.$num.'r_sort_field');
        if($reference_r_sort_field != '') $r_sort_field = $reference_r_sort_field;
        // end personal settings

        if (isset($_REQUEST['run']) && ($_REQUEST['run'] == 'true') ) {
            if (isset($_REQUEST[$key])) {
                $r_tmp_key = $_REQUEST[$key];
                if (isset($list[$r_tmp_key])) {
                    $r_sort_field = $r_tmp_key;
                    $current_user->setPreference($report_id.$num.'r_sort_field', $r_sort_field);
                    $current_user->setPreference($report_id.'modified', $current_date+$settings_duration);
                  }
            }
        }

        return $r_sort_field;
    }

    protected function r_sort_order($num = 0) 
    {
        $key = 'FMPREP_SSAR_SORT__ORDER';
        if ($num > 0) {
            $key .= '_' . $num;
        }

        reset($this->SORT_ORDERS);
        $r_sort_order = key($this->SORT_ORDERS);

        //personal settings
         global $current_user;

        $report_id = $_REQUEST['record'];
        $focus =& new QueryTemplate();

        if(isset($_REQUEST['record'])) {
        $focus->retrieve($_REQUEST['record']);
        }

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
                if($current_user->getPreference('SASRPersonalSettings') != null){
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
        $reference_r_sort_order = '';
        if($settings_expired_time >= $current_date) $reference_r_sort_order = $current_user->getPreference($report_id.$num.'r_sort_order');
        if ($reference_r_sort_order != '') $r_sort_order = $reference_r_sort_order;
        // end personal settings




        if (isset($_REQUEST['run']) && ($_REQUEST['run'] == 'true') ) {
            if (isset($_REQUEST[$key])) {
                $r_tmp_key = $_REQUEST[$key];
                if (isset($this->SORT_ORDERS[$r_tmp_key])) {
                    $r_sort_order = $r_tmp_key;
                    $current_user->setPreference($report_id.$num.'r_sort_order', $r_sort_order);
                    $current_user->setPreference($report_id.'modified', $current_date+$settings_duration);
               }
            }
        }

        return $r_sort_order;
    }

    public function html($desc) 
    {
//        pr($this->SORT_FIELDS);die();
        $r_sort_field = $this->r_sort_field();
        $r_sort_order = $this->r_sort_order();

        $sbox_fields = ''
            . '<select name="FMPREP_SSAR_SORT__FIELD">' 
                . get_select_options_with_id($this->SORT_FIELDS, $r_sort_field)
            . '</select>';

        $sbox_asc_desc = ''
            . '<select name="FMPREP_SSAR_SORT__ORDER">'
                . get_select_options_with_id($this->SORT_ORDERS, $r_sort_order)
            . '</select>'
            ;


        $r_sort_field = $this->r_sort_field(2);
        $r_sort_order = $this->r_sort_order(2);

        $list = array('' => ' -- NOT SELECTED -- ') + $this->SORT_FIELDS;
        $sbox_fields2 = ''
            . '<select name="FMPREP_SSAR_SORT__FIELD_2">'
                . get_select_options_with_id($list, $r_sort_field)
            . '</select>';

        $sbox_asc_desc2 = ''
            . '<select name="FMPREP_SSAR_SORT__ORDER_2">'
                . get_select_options_with_id($this->SORT_ORDERS, $r_sort_order)
            . '</select>'
            ;


        $r_sort_field = $this->r_sort_field(3);
        $r_sort_order = $this->r_sort_order(3);

        $sbox_fields3 = ''
            . '<select name="FMPREP_SSAR_SORT__FIELD_3">' 
                . get_select_options_with_id($list, $r_sort_field)
            . '</select>';

        $sbox_asc_desc3 = ''
            . '<select name="FMPREP_SSAR_SORT__ORDER_3">'
                . get_select_options_with_id($this->SORT_ORDERS, $r_sort_order)
            . '</select>'
            ;

        return <<<EOJS
<tr>
 <td class="tabDetailViewDL">$desc</td>
 <td class="tabDetailViewDF" colspan="3">
    <div>$sbox_fields $sbox_asc_desc</div>
    <div style="margin-top: 5px;">$sbox_fields2 $sbox_asc_desc2</div>
    <div style="margin-top: 5px;">$sbox_fields3 $sbox_asc_desc3</div>
 </td>
</tr>
EOJS;
    }

    public function build_query_addon() 
    {
        $r_sort_field = $this->r_sort_field();
        $r_sort_order = $this->r_sort_order();

        $q = 'ORDER BY ' . $r_sort_field . ' ' . strtoupper($r_sort_order);

        $r_sort_field = $this->r_sort_field(2);
        if ($r_sort_field) {
            $q .= ', ' . $r_sort_field . ' ' . strtoupper($this->r_sort_order(2));
        }

        $r_sort_field = $this->r_sort_field(3);
        if ($r_sort_field) {
            $q .= ', ' . $r_sort_field . ' ' . strtoupper($this->r_sort_order(3));
        }

        return $q;
    }
}