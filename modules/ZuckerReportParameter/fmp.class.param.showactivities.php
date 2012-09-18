<?php
class fmp_Param_Showactivities {

    function __construct() {}

    public function r_showactivities() 
    {
        if (isset($_REQUEST['fmp_showactivities'])) {
            if ($_REQUEST['fmp_showactivities']) {
                return true;
            }
        }

        return false;
    }

    public static function q_main($table, $type, $opportunity_id) 
    {
        return <<<EOSQL
SELECT
    "{$type}" AS type_of_activity, 
    UNIX_TIMESTAMP(x_m.date_start) AS date_time_of_activity,
    x_m.description AS points, 
    x_m.outcome_c AS outcome, 
    UNIX_TIMESTAMP(x_m.date_modified) AS date_modified,
    x_m.id AS dev_activity_id

FROM {$table} AS x_m 
  
    INNER JOIN accounts AS x_a
        ON  x_a.id=x_m.parent_id AND x_m.parent_type = 'Accounts'

    INNER JOIN accounts_opportunities AS x_ao 
        ON x_ao.account_id=x_a.id

WHERE 
    x_ao.opportunity_id='{$opportunity_id}'
    AND x_a.deleted=0 
    AND x_ao.deleted=0
    AND x_m.deleted=0
EOSQL;
    }

    public static function q_main__tasks($table, $type, $opportunity_id) 
    {
        return <<<EOSQL
SELECT
    "{$type}" AS type_of_activity, 
    UNIX_TIMESTAMP(x_m.date_start) AS date_time_of_activity,
    x_m.description AS points, 
    x_mc.outcome_c AS outcome, 
    UNIX_TIMESTAMP(x_m.date_modified) AS date_modified,
    x_m.id AS dev_activity_id

FROM {$table} AS x_m 
    LEFT JOIN {$table}_cstm AS x_mc 
        ON x_m.id=x_mc.id_c
   
    INNER JOIN accounts AS x_a
        ON  x_a.id=x_m.parent_id AND x_m.parent_type = 'Accounts'

    INNER JOIN accounts_opportunities AS x_ao 
        ON x_ao.account_id=x_a.id

WHERE 
    x_ao.opportunity_id='{$opportunity_id}'
    AND x_a.deleted=0 
    AND x_ao.deleted=0
    AND x_m.deleted=0
EOSQL;
    }
    
    public static function q($opportunity_id) 
    {
        $q = '' 
            . fmp_Param_Showactivities::q_main('meetings', 'Meetings', $opportunity_id) 
            . ' UNION '
            . fmp_Param_Showactivities::q_main('calls', 'Calls', $opportunity_id)
            . ' UNION '
            . fmp_Param_Showactivities::q_main__tasks('tasks', 'Tasks', $opportunity_id)
            . ' ORDER BY date_time_of_activity DESC'
            ;
            
        return $q;
    }

    public function html($desc) 
    {
        $r = $this->r_showactivities();
        $checked = '';
        if ($r) {
            $checked = ' checked="checked"';
        }

        $h = ''
            . '<input type="checkbox" name="fmp_showactivities"' . $checked . ' value="1">'
            . ' Check this option to show the related activities'
            ;

        return <<<EOJS
<tr>
 <td class="tabDetailViewDL">{$desc}</td>
 <td class="tabDetailViewDF" colspan="3">
    {$h}
 </td>
</tr>
EOJS;
    }

}