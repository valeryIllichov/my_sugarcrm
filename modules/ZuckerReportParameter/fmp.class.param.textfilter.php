<?php
class fmp_Param_Textfilter {
    protected $fields = array();
    protected $name = '';
    protected $settings_actual = false;
    protected $duration = 0;
    
    function __construct($name, $fields, $fields_grouped)
    {
       
        $this->name = $name;
        


        foreach($fields as $col_id=>$v) {
            if (!$v['sort']) {
                continue;
            }
            
            if (isset($fields_grouped[$col_id])) {
                $v['name'] = $fields_grouped[$col_id];
            }

            $this->fields[$col_id] = $v;
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
        if($settings_expired_time >= $current_date) $this->settings_actual = true;
        $this->duration =  $current_date+$settings_duration;
        // end personal settings

    }

    public function r_text($num)
    {
        global $current_user;
        $key = $this->get_name__text($num);
        $text = '';
        if($this->settings_actual) $text = $current_user->getPreference($report_id.$key);

        if (isset($_REQUEST['run']) && ($_REQUEST['run'] == 'true') && isset($_REQUEST[$key]) ) {
            $current_user->setPreference($report_id.$key, trim($_REQUEST[$key]));
            $current_user->setPreference($report_id.'modified', $this->duration);
            return trim($_REQUEST[$key]);
        }
        if($text != '') return $text;
        return ;
    }

    public function r_field($num)
    {
        global $current_user;
        $key = $this->get_name__field($num);
        $field = '';
        if($this->settings_actual) $field = $current_user->getPreference($report_id.$key);
        
        if (isset($_REQUEST['run']) && ($_REQUEST['run'] == 'true') && isset($_REQUEST[$key]) ) {
            $current_user->setPreference($report_id.$key, trim($_REQUEST[$key]));
            $current_user->setPreference($report_id.'modified', $this->duration);
            return trim($_REQUEST[$key]);
        }
        if($field != '') return $field;
        return ;
    }

    protected function get_name__text($num) 
    {
        
        return 'fmp_search_' . $this->name . '_' . ((int) $num) . '_text'; 
    }
    
    protected function get_name__field($num) 
    {
        
        return 'fmp_search_' . $this->name . '_' . ((int) $num) . '_field';
    }
    
    public function html($desc) 
    {
        $h = array();

       
        
        $fields = array('' => ' -- NOT SELECTED -- '); 
        foreach($this->fields as $k=>$v) {
            $a_parts = explode('<br>', $v['name']);
            if (count($a_parts) > 1) {
                $v['name'] = implode(' ', $a_parts);            
            }

            $fields[$k] = $v['name'];
        }

        for($k=0; $k<3; $k++) {
            $style = '';
            if ($k > 0) {
                $style = ' style="margin-top: 5px;"';
            }

            $val = $this->r_text($k);
            $field = $this->r_field($k);
            $name_text = $this->get_name__text($k);
            $name_field = $this->get_name__field($k);
            $h[] = ''
                . '<div ' . $style . '>'
                    . '<input type="text" name="' . $name_text . '" value="' . $val . '" />'
                    . '<select name="' . $name_field . '" style="margin-left: 3px;">'
                        . get_select_options_with_id($fields, $field)
                    . '</select>'
                . '</div>'
                ;
        }

        $h = implode('', $h);

        return <<<EOJS
<tr>
 <td class="tabDetailViewDL">$desc</td>
 <td class="tabDetailViewDF" colspan="3">
        {$h}
 </td>
</tr>
EOJS;
    }
    
    public function build_query_addon() 
    {
        /*
         * This is temporary fix for SASR. OR processes data using single SQL query. SASR uses TEMPORARY TABLES.
         * So the field names are not the same more. It may be best to create different class or use something else. 
         * Current solution is fixing such case.
         * Note*: 
         *  'a' - activity
         *  'o' - opportunity 
         */
        if ($this->name == 'a') {
            $out = array();
            for($k=0; $k<3; $k++) {
                $key = $this->r_field($k);
                if (!$key) {
                    continue;
                }
                
                $val = $this->r_text($k);
                if (!$val) {
                    continue;
                }
    
                if (!isset($this->fields[$key])) {
                    continue;
                }
                
                $out[] = $key . ' LIKE \'%' . $val . '%\'';
            }
            
            $out = implode(' AND ', $out);
            if ($out) {
                return ' AND ' . $out;
            }
            return ;
        }
        
        $out = array();
        for($k=0; $k<3; $k++) {
            $key = $this->r_field($k);
            if (!$key) {
                continue;
            }
            
            $val = $this->r_text($k);
            if (!$val) {
                continue;
            }

            if (!isset($this->fields[$key])) {
                continue;
            }

            $out[] = $this->fields[$key]['sort_q'] . ' LIKE \'%' . $val . '%\'';
        }
        $out = implode(' AND ', $out);
        if ($out) {
            return ' AND ' . $out;
        }
        return ;
    }

}
