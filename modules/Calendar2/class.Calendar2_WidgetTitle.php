<?php
class Calendar2_WidgetTitle 
{
    public static $VIEW_FULL = array('day');
    public static $VIEW_BRIEF = array('week', 'shared', 'month');
    
    protected $r_view = '';
    
    function __construct($view) 
    {
        $this->r_view = $view;
    }
    
    protected function html_widget_col2($title, $status) 
    {
        $status_color = '#cccccc';
        $title_color = '#ffffff';
        switch(strtolower($status)){
            case 'held':
                $status_color = '#92fc8b';
                $title_color = '#0fff00';
                break;
            case 'not held':
                $status_color = '#fdf793';
                $title_color = '#ffe50e';
                break;
        }
        return '' 
            . '<div style=\'float: left; width: 70%; height: 14px; overflow: hidden; color: '.$title_color.';\'>'
                . '<nobr>' 
                    . $title
                . '</nobr>' 
            . '</div>' 
            . '<div style=\'float: left; width: 18%; height: 14px; overflow: hidden;  color: '.$status_color.';\'>'
                . '<nobr>' 
                    . $status
                . '</nobr>'
            . '</div>'
            ;
    }
    
    protected function html_widget_col3($title, $subject, $status) 
    {
        $out = array();

        $a = array($title, $subject, $status);
        foreach($a as $val) {
            $val = trim($val);
            if (!$val) {
                continue;
            }
            $out[] = $val;
        }
        return implode(', ', $out);
    }

    public function process($w_parent_type, $w_parent_id, $w_customer_name, $w_lead_name, $w_subject, $w_status) 
    {
        $w_parent_type = strtolower($w_parent_type);

        if ( in_array( $this->r_view, self::$VIEW_BRIEF) || in_array( $this->r_view, self::$VIEW_FULL) ) {
            if ($w_parent_type == 'accounts') {
                if ($w_customer_name) {
                    return $this->html_widget_col2($w_customer_name, $w_status); 
                }
                return $this->html_widget_col2($w_subject, $w_status);
            }

            if ($w_parent_type == 'leads') {
                if ($w_lead_name) {
                    return $this->html_widget_col2($w_lead_name, $w_status);
                }
                return $this->html_widget_col2($w_subject, $w_status);
            }
            
            return $this->html_widget_col2($w_subject, $w_status);
        }


        if ($w_parent_type == 'accounts') {
            if ($w_customer_name) {
                return $this->html_widget_col3($w_customer_name, $w_subject, $w_status); 
            }
            return $this->html_widget_col3('', $w_subject, $w_status);
        }

        if ($w_parent_type == 'leads') {
            if ($w_lead_name) {
                return $this->html_widget_col3($w_lead_name, $w_subject, $w_status);
            }
            return $this->html_widget_col3('', $w_subject, $w_status);
        }

        return $this->html_widget_col3('', $w_subject, $w_status);
    }
    
    public static function instance() 
    {
        global $cal2_widget_title_obj;
        
        if (!$cal2_widget_title_obj) {
            $view = '';
            if ( isset($_REQUEST['view']) ) {
                $view = $_REQUEST['view'];
            }
            $cal2_widget_title_obj = new self($view);
        }

        return $cal2_widget_title_obj;
    }
    
    public static function create_from_activity_array($a_act) 
    {
        $o = self::instance();
        
        $widget_parent_type = $a_act['parent_type']; 
        $widget_parent_id = $a_act['parent_id'];
        $widget_customer_name = $a_act['customer_name'];
        $widget_subject = $a_act['name'];
        $widget_lead_name = $a_act['lead_name'];
        $widget_status = $a_act['status'];
        $widget_lead_account_name = $a_act['lead_account_name'];
        $widget_custno_c = $a_act['custno_c'];
        if($widget_parent_id){
           if (strtolower($widget_parent_type) == 'accounts') $widget_customer_name = $widget_customer_name.' ('.$widget_custno_c.')';
           if (strtolower($widget_parent_type) == 'leads') $widget_lead_name = $widget_lead_account_name.' ('.$widget_lead_name.')';
        }
        
        $title = $o->process(
            $widget_parent_type, 
            $widget_parent_id, 
            $widget_customer_name,
            $widget_lead_name,
            $widget_subject,
            $widget_status
        );
        return $title;
    }

    public static function create($w_parent_type, $w_parent_id, $w_customer_name, $w_lead_name, $w_subject, $w_status, $w_account_name ='', $w_custno_c='')
    {
        if($w_parent_id){
           if (strtolower($w_parent_type) == 'accounts') $w_customer_name = $w_customer_name.' ('.$w_custno_c.')';
           if (strtolower($w_parent_type) == 'leads') $w_lead_name = $w_account_name.' ('.$w_lead_name.')';
        }
        $o = self::instance();
        $title = $o->process(
            $w_parent_type, 
            $w_parent_id, 
            $w_customer_name,
            $w_lead_name,
            $w_subject,
            $w_status
        );
        return $title;
    }
}
