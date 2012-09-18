<?php
class fmp_Param_DateFromTo {
    protected $user_id = 0;
    
    public $date_from = '';
    public $date_from_mk = 0;
    public $date_to = '';
    public $date_to_mk = 0;
    protected $name = '';
    
    protected $timedate;

    function __construct($user_id, $from_to_name) 
    { 
        $this->user_id = $user_id;
        $this->name = $from_to_name;
    }

    public function init() 
    {
        $mk_secinweek = 24*60*60;
        $num_curr_dayofweek = date('w'); 
        $mk_base = mktime();
        $mk_week_end = $mk_base - ($num_curr_dayofweek + 2) * $mk_secinweek;
        $mk_week_start = $mk_week_end - 4 * $mk_secinweek;

        $this->date_from_mk = $mk_week_start;
        $this->date_from = date('m/d/Y', $mk_week_start);

        $this->date_to_mk = $mk_week_end;
        $this->date_to = date('m/d/Y', $mk_week_end);

        $this->timedate = new TimeDate();
    }

    public function r_date_from($use_datetime_shift = true) 
    {
        global $timedate;

        $date_mk = $this->date_from_mk; 
        if (isset($_REQUEST[$this->name . '_FROM'])) {
            if ($_REQUEST[$this->name . '_FROM']) {
                $date = explode('/', $_REQUEST[$this->name . '_FROM']);
                $date_mk = mktime(0, 0, 0, (int) $date[0], (int) $date[1], (int) $date[2]);
            }
        }
        
        if (!$use_datetime_shift) {
            return $date_mk;
        }
        
        $format = 'Y-m-d H:i:s';
        $date = date($format, $date_mk);
        $date_mk = $timedate->handle_offset($date, $format, false);
        
        return strtotime($date_mk);
    }

    public function r_date_to($use_datetime_shift = true)
    {
        global $timedate;

        $date_mk = $this->date_to_mk; 
        if (isset($_REQUEST[$this->name . '_TO'])) {
            if ($_REQUEST[$this->name . '_TO']) {
                $date = explode('/', $_REQUEST[$this->name . '_TO']);
                $date_mk = mktime(23, 59, 59, (int) $date[0], (int) $date[1], (int) $date[2]);
            }
        }

        if (!$use_datetime_shift) {
            return $date_mk;
        }

        $format = 'Y-m-d H:i:s';
        $date = date($format, $date_mk);
        $date_mk = $timedate->handle_offset($date, $format, false);

        return strtotime($date_mk);
    }
    
    public function html($desc) 
    {
        //return $this->html__control($this->a_regions, $desc);
    }
}