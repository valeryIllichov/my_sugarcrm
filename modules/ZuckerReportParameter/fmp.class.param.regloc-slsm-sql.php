<?php
class fmp_Param_RegLoc_SLSM_SQL {
    protected $user_id = 0;

    function __construct($user_id) 
    { 
        $this->user_id = $user_id;
    }

    public function build__query_addon( $r_regloc, $r_slsm, $is_user_id ) 
    {
        require_once 'fmp.class.param.regloc.php';
        $oRegLoc = new fmp_Param_RegLoc($this->user_id);
        $oRegLoc->init();

        require_once 'fmp.class.param.slsm.php';
        $oSlsm = new fmp_Param_SLSM($this->user_id);
        $oSlsm->init();

        $is_r = $oRegLoc->is_assigned_regons();
        $is_s = $oSlsm->is_assigned_slsm();

        if ($is_r) {
            if ($is_s) {
                $r_regloc = $oRegLoc->compile__available_regions($r_regloc);
                $r_slsm = $oSlsm->compile__available_slsm($r_slsm);

                return $this->build__reqloc_slsm($r_regloc, $r_slsm, $is_user_id);
            }
            $r_regloc = $oRegLoc->compile__available_regions($r_regloc);
            return $this->build__regloc($r_regloc, $is_user_id);
        } 

        if ($is_s) {
            $r_slsm = $oSlsm->compile__available_slsm($r_slsm);
            return $this->build__slsm($r_slsm, $is_user_id);
        }
        return ;
    }
    
    public function build__query_addon__all_activity_fix( $r_regloc, $r_slsm, $is_user_id ) 
    {
        require_once 'fmp.class.param.regloc.php';
        $oRegLoc = new fmp_Param_RegLoc($this->user_id);
        $oRegLoc->init();

        require_once 'fmp.class.param.slsm.php';
        $oSlsm = new fmp_Param_SLSM($this->user_id);
        $oSlsm->init();

        $is_r = $oRegLoc->is_assigned_regons();
        $is_s = $oSlsm->is_assigned_slsm();

        if ($is_r) {
            if ($is_s) {
                $r_regloc = $oRegLoc->compile__available_regions($r_regloc);
                $r_users = $oSlsm->compile__available_users($r_slsm);

                return $this->build__reqloc_users__all_activity_fix($r_regloc, $r_users, $is_user_id);
            }
            $r_regloc = $oRegLoc->compile__available_regions($r_regloc);
            return $this->build__regloc__all_activity_fix($r_regloc, $is_user_id);
        } 

        if ($is_s) {
            $r_users = $oSlsm->compile__available_users($r_slsm);
            return $this->build__users__all_activity_fix($r_users, $is_user_id);
        }
        return ;
    }

    protected function build__reqloc_slsm($compiled_regloc, $compiled_slsm, $is_user_id ) 
    {
        foreach($compiled_regloc as $k=>$v) {
            $compiled_regloc[$k] = "'$v'";
        }

        foreach ($compiled_slsm as $k=>$v) {
        	$compiled_slsm[$k] = "'$v'";
        }

        $h = ''
            . $this->user_add_on($is_user_id)
            . ' AND (x_a.company_c="Splash"  OR  x_a.location_c IN (' . implode(', ', $compiled_regloc) . '))'
            . ' AND x_a.slsm_c IN (' . implode(', ', $compiled_slsm) . ')'
            ;
        return $h;
    }

    protected function build__regloc($compiled_regloc, $is_user_id)  
    {
        foreach($compiled_regloc as $k=>$v) {
            $compiled_regloc[$k] = "'$v'";
        }

        $h = ''
            . $this->user_add_on($is_user_id)
            . ' AND ( x_a.company_c="Splash" OR x_a.location_c IN (' . implode(', ', $compiled_regloc) . '))'
            ;
        return $h;        
    }

    protected function build__slsm($compiled_slsm, $is_user_id) 
    {
        foreach ($compiled_slsm as $k=>$v) {
            $compiled_slsm[$k] = "'$v'";
        }

        $h = ''
            . $this->user_add_on($is_user_id)
            . ' AND x_a.slsm_c IN (' . implode(', ', $compiled_slsm) . ')'
            ;
        return $h;        
    }
    
    protected function build__reqloc_users__all_activity_fix($compiled_regloc, $compiled_users, $is_user_id ) 
    {
        foreach($compiled_regloc as $k=>$v) {
            $compiled_regloc[$k] = "'$v'";
        }

        foreach ($compiled_users as $k=>$v) {
            $compiled_users[$k] = "'$v'";
        }

        $h = ''
            . $this->user_add_on($is_user_id)
            . ' AND x_u.location IN (' . implode(', ', $compiled_regloc) . ')'
            . ' AND x_u.id IN (' . implode(', ', $compiled_users) . ')'
            ;
        return $h;
    }

    protected function build__regloc__all_activity_fix($compiled_regloc, $is_user_id)  
    {
        foreach($compiled_regloc as $k=>$v) {
            $compiled_regloc[$k] = "'$v'";
        }

        $h = ''
            . $this->user_add_on($is_user_id)
            . ' AND x_u.location IN (' . implode(', ', $compiled_regloc) . ')'
            ;
        return $h;        
    }

    protected function build__users__all_activity_fix($compiled_users, $is_user_id) 
    {
        foreach ($compiled_users as $k=>$v) {
            $compiled_users[$k] = "'$v'";
        }

        $h = ''
            . $this->user_add_on($is_user_id)
            . ' AND x_u.id IN (' . implode(', ', $compiled_users) . ')'
            ;
        return $h;        
    }
    
    
    protected function user_add_on($is_user_id) 
    {
        if (!$is_user_id) {
            return ;
        }
        
        return ''
            . ' AND x_m.assigned_user_id="' . $this->user_id . '" ' 
            ;
    }
}