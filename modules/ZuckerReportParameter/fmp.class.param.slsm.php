<?php
class fmp_Param_SLSM {
    protected $user_id = 0;
    protected $a_slsm = array();
    protected $a_reps = array();

    function __construct($user_id) 
    { 
        $this->user_id = $user_id; 
    }

    public function init() 
    {
//		$this->a_slsm = $this->getUserSlsmRollup($this->user_id);  // for dsls_slsm table
  	    $this->a_slsm = $this->getUserTwoSlsmRollup($this->user_id); // for dsls_slsm_combined table
        $GLOBALS['log']->test("get_user_array query: !!!!!!!we are here!!!!!!!");
    }

    public function html($desc) 
    {
        return $this->html__control($this->a_slsm, $desc);
    }

    public function html_for_daily_sales($onclick = '', $id = '')
    {
        $list = $this->html__slsm_tree($this->a_slsm);
        $select_creater = '<div><br/><label style="font-size: 12px; color: #000000;" for="fmp_slsm_input">Quick Search: </label>
                <input id="'.$id.'fmp_slsm_input" type="text" value="" name="fmpfilter_slsm_input" onkeyup="javaScript:'.$id.'fmp_slsm_list_quick_search(this.value);" style="width: 150px;"><br /><br /><div id="'.$id.'box_for_slsm_first">';
        $select_creater .= '<select id="'.$id.'fmprep_slsm_tree" '.$onclick.' size="15" multiple="multiple" style="width: 340px;">';
        $select_creater .= '<option value="all" style="border-bottom: 2px solid grey;">ALL</option>';
        foreach ($list as $key=>$value){
            $select_creater .= '<option value="'.$key.'">'.$value.'</option>';
        }
        $select_creater .= '</select></div><div id="'.$id.'box_for_slsm_second" style="display: none"></div></div>';
        return $select_creater;
    }
    
    public function get_sales_reps_array(){
        global $current_user;
        $current_user_item = Array("$current_user->id"=>"$current_user->user_name");
        $tempMass = $this->compile__sales_reps($this->a_slsm);
        asort($tempMass);
        return $current_user_item+$tempMass;
    }

    public function is_assigned_slsm() 
    {
        return (int) $this->a_slsm;
    }
    
    public function compile__available_slsm($r_slsm) 
    {
        $a_slsm__requested_by_user = array();
        foreach($r_slsm as $slsm_id) {
            $a_slsm__requested_by_user += $this->compile__available_slsm__tree2list__requested($this->a_slsm, $slsm_id);
        }
        
        if ($a_slsm__requested_by_user) {
            return array_keys($a_slsm__requested_by_user);
        }

        return array_keys($this->compile__available_slsm__tree2list__all_assigned($this->a_slsm));
    }
    
    public function compile__available_users($r_slsm) 
    {
        global $db;
        
        $a_slsm = $this->compile__available_slsm($r_slsm);

        if (!$a_slsm) {
            return array();
        }

        $groups = array('\'-101\'');
        foreach($a_slsm as $group_id) {
            if ($group_id < 10) {
                $group_id = '00' . $group_id;
            } 
            elseif ($group_id < 100) {
                $group_id = '0' . $group_id;
            }

            $groups[] = '\'SLSM_' . $group_id . '\'';
        }

        $q = ''
            . 'SELECT '
                . 'x_sgu.user_id '
            . 'FROM securitygroups AS x_sg '
                . 'INNER JOIN securitygroups_users AS x_sgu '
                    . 'ON x_sg.id=x_sgu.securitygroup_id '
            . 'WHERE x_sg.deleted=0 '
                . 'AND x_sgu.deleted=0 '
                . 'AND x_sg.name IN (' . implode(',', $groups) . ') '
            . 'GROUP BY x_sgu.user_id' 
            ;
        $rs = $db->query($q);

        $out=array('-101');
        while($row = $db->fetchByAssoc($rs)) {
            $out[] = $row['user_id'];
        }

        return $out;
    }

    protected function compile__available_slsm__tree2list__all_assigned($tree) 
    {
        $out = array();
        foreach($tree as $k=>$v) {
//            $out[$v['slsm']] = $v['slsm'] . ' ' . $v['firstname'] . ' ' . $v['lastname']; //value is for debugging
          	$out[$v['slsm']] = $v['slsm'] . ' ' . $v['name'] . ' (' . $v['company'].')'; //value is for debugging
            if (!$v['children']) {
                continue;
            }
            $out += $this->compile__available_slsm__tree2list__all_assigned($v['children']);
        }
        return $out;
    }

     protected function compile__sales_reps($tree)
    {
        foreach($tree as $k=>$v) {
            if($v[user_id] != ''){
            $this->a_reps[$v['user_id']] = $v['username'];
            }
            if (!$v['children']) {
                continue;
            }
            $this->a_reps += $this->compile__sales_reps($v['children']);
        }

        return $this->a_reps;
    }

    protected function compile__available_slsm__tree2list__requested($tree, $slsm_id, $include_into_result = false) 
    {
        $out = array();
        foreach($tree as $k=>$v) {
            if (!$include_into_result) {
                if ($slsm_id == $v['slsm']) {
//                    $out[$v['slsm']] = $v['slsm'] . ' ' . $v['firstname'] . ' ' . $v['lastname']; //value is for debugging
                    $out[$v['slsm']] = $v['slsm'] . ' ' . $v['name'] . ' (' . $v['company'].')'; //value is for debugging
                    if ($v['children']) {
                        $out += $this->compile__available_slsm__tree2list__requested($v['children'], $slsm_id, true);
                    }
                    return $out;
                }
            } else {
//                $out[$v['slsm']] = $v['slsm'] . ' ' . $v['firstname'] . ' ' . $v['lastname']; //value is for debugging
                $out[$v['slsm']] = $v['slsm'] . ' ' . $v['name'] . ' (' . $v['company'].')'; //value is for debugging
            }
             
            if (!$v['children']) {
                continue;
            }
            $out += $this->compile__available_slsm__tree2list__requested($v['children'], $slsm_id, $include_into_result);
        }
        return $out;
    }

    protected function getTwoSlsmRollup(){
        global $db;
                $q = ''
            . 'SELECT '
                . ' company, slsm, dsls_slsm_combined.name, manager_company, manager_slsm, x_u.id AS user_id, x_u.user_name AS username '
            . ' FROM dsls_slsm_combined '
            	. ' LEFT JOIN users AS x_u ON (dsls_slsm_combined.empid = x_u.empid '
                  . '  AND dsls_slsm_combined.empid <> \' \') '
            . ' ORDER BY slsm'
            ;

 
            $rs = $db->query($q);
        $FMP_slsmByMgr = array();
        $Splash_slsmByMgr = array();
        while($row = $db->fetchByAssoc($rs)) {
        if ($row['manager_slsm'] == '') $row['manager_slsm'] = '0';
        if($row['company'] == 'FMP') $FMP_slsmByMgr[$row['manager_slsm']][] = $row;
        if($row['company'] == 'Splash') $Splash_slsmByMgr[$row['manager_slsm']][] = $row;
        }

        $FMP_slsmTree = array();
        foreach($FMP_slsmByMgr['0'] as $parentSlsm) {
            $children=$this->getSlsmChildren($parentSlsm['slsm'], $FMP_slsmByMgr, 0);
            if(count($children) > 0) {
                $parentSlsm['children']=$children;
            }
            $FMP_slsmTree[]=$parentSlsm;
        }
        $Splash_slsmTree = array();
        foreach($Splash_slsmByMgr['0'] as $parentSlsm) {
            $children=$this->getSlsmChildren($parentSlsm['slsm'], $Splash_slsmByMgr, 0);
            if(count($children) > 0) {
                $parentSlsm['children']=$children;
            }
            $Splash_slsmTree[]=$parentSlsm;
        }

        return array(
            'FMP' => $FMP_slsmTree,
            'Splash' => $Splash_slsmTree
        );


    }


    protected function getSlsmRollup() 
    {
        global $db;

        $q = ''
            . 'SELECT'
                . ' dsls_slsm.slsm, dsls_slsm.firstname, dsls_slsm.lastname, dsls_slsm.mgr_group AS mgr, x_u.id AS user_id, x_u.user_name AS username  '
            . 'FROM dsls_slsm '
            . 'LEFT JOIN users AS x_u ON dsls_slsm.empid = x_u.empid '
            . 'ORDER BY slsm'
            ;
//		echo "<pre>";
//		print_r($q);
//		echo "</pre>";die();
        
        $rs = $db->query($q);

        $slsmByMgr=array();
        while($row = $db->fetchByAssoc($rs)) {
            $slsmByMgr[$row['mgr']][] = $row;
        }
       
        $slsmTree = array();
        foreach($slsmByMgr['0'] as $parentSlsm) {
            $children=$this->getSlsmChildren($parentSlsm['slsm'], $slsmByMgr, 0);
            if(count($children) > 0) {
                $parentSlsm['children']=$children;
            }
            $slsmTree[]=$parentSlsm;
        }
        	
//		echo "<pre>";
//		print_r($slsmTree);
//		echo "</pre>";die();

        return $slsmTree;
    }

    protected function getSlsmChildren($slsmno, &$slsmByMgr) 
    {
        //pr($slsmno);
        $slsmTree=$slsmByMgr[(int)($slsmno)];
        if($slsmTree != '') {
            foreach($slsmTree as &$slsm) {
                $slsm['children']=$this->getSlsmChildren($slsm['slsm'], $slsmByMgr);
            }
        }
        return $slsmTree;
    }

     protected function getUserTwoSlsmRollup($user_id)
    {
        global $db;
         $two_slsmRollup = $this->getTwoSlsmRollup();
         $FMP_slsmRollup =  $two_slsmRollup['FMP'];
         $Splash_slsmRollup = $two_slsmRollup['Splash'];
         //pr($FMP_slsmRollup);

         $q_fmp = ''
            . 'SELECT '
                . 'x_sg.name '
            . 'FROM users AS x_u '
                . 'INNER JOIN securitygroups_users AS x_sgu ON x_u.id = x_sgu.user_id '
                . 'INNER JOIN securitygroups AS x_sg ON x_sgu.securitygroup_id = x_sg.id '
            . 'WHERE x_u.id = \'' . $user_id . '\' '
                . ' AND x_sgu.deleted = 0 '
                . 'AND x_sg.deleted = 0 '
                . 'AND x_sg.name LIKE \'SLSM_%\' '
            . 'ORDER BY x_sg.name';
            

        $rs_fmp = $db->query($q_fmp);
        
        while($row = $db->fetchByAssoc($rs_fmp)) {
            $FMP_slsmList[]=substr($row['name'],5);
        }        
        //pr($FMP_slsmList);

        $userSlsmRollup=array();
        foreach($FMP_slsmRollup as &$slsmRollupBranch) {
       	
            $slsmArray=$this->getUserSlsmRollupRecur($FMP_slsmList, $slsmRollupBranch);
            if($slsmArray != null) {
                foreach($slsmArray as $slsm) {
                    $userSlsmRollup[] = $slsm;
                }
            }
        }
		
       // pr($userSlsmRollup);

          $q_splash = ''
            . 'SELECT '
                . 'x_sg.name '
            . 'FROM users AS x_u '
                . 'INNER JOIN securitygroups_users AS x_sgu ON x_u.id = x_sgu.user_id '
                . 'INNER JOIN securitygroups AS x_sg ON x_sgu.securitygroup_id = x_sg.id '
            . 'WHERE x_u.id = \'' . $user_id . '\' '
                . ' AND x_sgu.deleted = 0 '
                . 'AND x_sg.deleted = 0 '
                . 'AND x_sg.name  LIKE \'Splash_Slsm_%\' '
            . 'ORDER BY x_sg.name';
        $rs_splash = $db->query($q_splash);

        while($row = $db->fetchByAssoc($rs_splash)) {
            $Splash_slsmList[]=substr($row['name'],12);
        }
        
        foreach($Splash_slsmRollup as &$slsmRollupBranch) {

            $slsmArray=$this->getUserSlsmRollupRecur($Splash_slsmList, $slsmRollupBranch);
            if($slsmArray != null) {
                foreach($slsmArray as $slsm) {
                  	$userSlsmRollup[] = $slsm;
                }
            }
        }
        
        //pr($userSlsmRollup);
        return $userSlsmRollup;

     }
    
    protected function getUserSlsmRollup($user_id) 
    {
        global $db;
        $slsmRollup=$this->getSlsmRollup();
        

        $q = '' 
            . 'SELECT ' 
                . 'x_sg.name ' 
            . 'FROM users AS x_u ' 
                . 'INNER JOIN securitygroups_users AS x_sgu ON x_u.id = x_sgu.user_id ' 
                . 'INNER JOIN securitygroups AS x_sg ON x_sgu.securitygroup_id = x_sg.id ' 
            . 'WHERE x_u.id = \'' . $user_id . '\' ' 
                . ' AND x_sgu.deleted = 0 ' 
                . 'AND x_sg.deleted = 0 ' 
                . 'AND x_sg.name LIKE \'SLSM_%\' '
            . 'ORDER BY x_sg.name';
        $rs = $db->query($q);


       
        while($row = $db->fetchByAssoc($rs)) {
            $slsmList[]=substr($row['name'],5);
        }

        

        /* Traverse the tree to find slsm they can see -- could be more than one */
        $userSlsmRollup=array();
        foreach($slsmRollup as &$slsmRollupBranch) {

            $slsmArray=$this->getUserSlsmRollupRecur($slsmList, $slsmRollupBranch);
            if($slsmArray != null) {
                foreach($slsmArray as $slsm) {
                    $userSlsmRollup[] = $slsm;
                }
            }
        }

//        echo '<pre>';
//        print_r($userSlsmRollup);
//        echo '</pre>';

        return $userSlsmRollup;
    }
    
    protected function getUserSlsmRollupRecur($slsmList, $slsmRollupBranch) 
    {
        if(count($slsmList) == 0) {
            return null;
        }
        
	    foreach($slsmList as &$slsmNo) {
            if($slsmRollupBranch['slsm'] == $slsmNo) {   /* match -- return it and all slsm under */
                return array($slsmRollupBranch);
            }
        }
            
        if(array_key_exists('children', $slsmRollupBranch) && $slsmRollupBranch['children'] != '') {   /* not found, so try to find in children */
            $userSlsmRollup=null;
            foreach($slsmRollupBranch['children'] as &$child) {
                $slsmArray=$this->getUserSlsmRollupRecur($slsmList, $child);
                if($slsmArray != null) {
                    foreach($slsmArray as $slsm) {
                        $userSlsmRollup[] = $slsm;
                    }
                }
            }
            
            return $userSlsmRollup;
        } else {
            return null;
        }
    }
    
    protected function html__control($a_slsm, $desc) 
    {

//            echo '<pre>';
//            print_r($_REQUEST['run']);
//            echo '</pre>';
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


        $disabled = '';
        if (!$a_slsm) {
            $disabled = ' disabled="disabled"';
        }

        $r_fmpfilter_slsm__mode = 1;

        
        $settings_expired_time = (int) $current_user->getPreference($report_id.'modified');
        if($settings_expired_time == 0) $settings_expired_time = $current_date;

        $r_fmpfilter_slsm__include_user_id = 1;
//        pr(date("m/d/y", $current_date));
//        pr(date("m/d/y", $settings_expired_time)); die();

        if($settings_expired_time >= $current_date) $r_fmpfilter_slsm__include_user_id  = (int) $current_user->getPreference($report_id.'fmpfilter_slsm__include_user_id');
        if (isset($_REQUEST['run']) && $_REQUEST['run'] == 'true') {
            if(!isset($_REQUEST['fmpfilter_slsm__include_user_id'])){
            $r_fmpfilter_slsm__include_user_id = 0;
            } else {$r_fmpfilter_slsm__include_user_id = 1;}
        }

        $current_user->setPreference($report_id.'fmpfilter_slsm__include_user_id', $r_fmpfilter_slsm__include_user_id);

        $r_fmpfilter_slsm__tree_item = -1;
        if($settings_expired_time >= $current_date && (int) $current_user->getPreference($report_id.'fmpfilter_slsm__tree_item') != 0) $r_fmpfilter_slsm__tree_item = (int) $current_user->getPreference('fmpfilter_slsm__tree_item');
        if (isset($_REQUEST['run']) && $_REQUEST['run'] == 'true' && isset($_REQUEST['fmpfilter_slsm__tree_item'])) {
//            echo '<pre>';
//            print_r($_REQUEST['fmpfilter_slsm__tree_item']);
//            echo '</pre>';
            $r_fmpfilter_slsm__tree_item = (int) $_REQUEST['fmpfilter_slsm__tree_item'];
            $current_user->setPreference($report_id.'fmpfilter_slsm__tree_item', $r_fmpfilter_slsm__tree_item);
            $current_user->setPreference($report_id.'modified', $current_date+$settings_duration);
        }
        
        $h_btn_slsm_add__rollup = '' 
            . '<input style="width: 110px;" type="button"' 
                . ' onclick="return h_click_slsm_add__rollup();" '
                . $disabled 
                . ' value="Add SLSM(s)" />'
                ;

        $r_slsm_tree__item_id = array();
        $reference_r_slsm_tree__item_id = array();
        $reference_r_slsm_tree__item_id = $current_user->getPreference($report_id.'fmprep_ssar__slsm_tree');
        if($settings_expired_time >= $current_date && is_array($reference_r_slsm_tree__item_id) && count($reference_r_slsm_tree__item_id)!=0)  $r_slsm_tree__item_id = $reference_r_slsm_tree__item_id;
        if (isset($_REQUEST['run']) && $_REQUEST['run'] == 'true' && isset($_REQUEST['fmprep_ssar__slsm_tree'])) {
            if (is_array($_REQUEST['fmprep_ssar__slsm_tree'])) {
                $r_slsm_tree__item_id = $_REQUEST['fmprep_ssar__slsm_tree'];
                $current_user->setPreference($report_id.'fmprep_ssar__slsm_tree', $r_slsm_tree__item_id);
                $current_user->setPreference($report_id.'modified', $current_date+$settings_duration);
            }
        }



        $h_slsm_tree = array();
        if ($r_fmpfilter_slsm__mode) {
            $h_slsm_tree = $this->html__slsm_tree($a_slsm);
        } else {
            $h_slsm_tree = $this->html__slsm_tree__single_level($a_slsm);
        }

        
        $h_slsm_tree = get_select_options_with_id($h_slsm_tree, $r_slsm_tree__item_id);

        $h_slsm_tree = ''
            . '<select style="display: none;" multiple="multiple"'
                    . ' id="fmprep_ssar__slsm_tree__source">' 
                . $h_slsm_tree
            . '</select>'
            . '<select style="width: 340px" name="fmprep_ssar__slsm_tree[]" multiple="multiple"'
                    . ' size="15"'
                    . $disabled
                    . ' id="fmprep_ssar__slsm_tree"' 
                    . ' ondblclick="return h_ondblclick_slsm_tree();">' 
                . $h_slsm_tree
            . '</select>';

        $h_filter_values = '<div style="text-align: center;"> -- all available SLSM ID(s)/rollup(s) -- </div>';
        $fmpfilter_slsm = '';
        $preference_fmpfilter_slsm = '';
        $preference_fmpfilter_slsm = $current_user->getPreference($report_id.'fmpfilter_slsm');
        if($settings_expired_time >= $current_date && $preference_fmpfilter_slsm !='') $fmpfilter_slsm = $preference_fmpfilter_slsm;
        if (isset($_REQUEST['run']) && $_REQUEST['run'] == 'true' && isset($_REQUEST['fmpfilter_slsm'])) {
            $fmpfilter_slsm = $_REQUEST['fmpfilter_slsm'];
            $current_user->setPreference($report_id.'fmpfilter_slsm', $fmpfilter_slsm);
        }

        $h_js = $this->html__slsm_tree__js($a_slsm);
        if ($h_js) {
            $h_js = 'var fmpfilter_slsm__tree = ' . $h_js . ';' . "\n";
        } else {
            $h_js = 'var fmpfilter_slsm__tree = new Array();' . "\n";
        }

        $checked = '';
        if ($r_fmpfilter_slsm__mode) {
            $checked = 'checked="checked" ';
        }

        $h_cbox = ''
            . '<input type="checkbox" name="fmpfilter_slsm__mode" value="1" ' 
                . 'id="fmpfilter_slsm__mode" style="display: none;" ' . $checked . '/>' 
            . ' <label for="fmpfilter_slsm__mode" ' 
                . 'style="font-size: 12px; color: #000000; padding-right: 30px; display: none;">Tree Mode</label>'
            ;

        $checked = '';
        if ($r_fmpfilter_slsm__include_user_id) {
            $checked = 'checked="checked" ';
        }
        $h_cbox2 = ''
            . '<input type="checkbox" name="fmpfilter_slsm__include_user_id" value="1" ' 
                . 'id="fmpfilter_slsm__include_user_id" ' . $checked . '/>' 
            . ' <label for="fmpfilter_slsm__include_user_id" ' 
                . 'style="font-size: 12px; color: #000000; padding-right: 30px;">Only my activity</label>'
            ;

        $r_fmpfilter_slsm__input_byid = ''; 
        if (isset($_REQUEST['fmpfilter_slsm__input_byid'])) {
            $r_fmpfilter_slsm__input_byid = htmlspecialchars($_REQUEST['fmpfilter_slsm__input_byid']);
        }
        $h_input_byid = '' 
            . '<label for="fmpfilter_slsm__input_byid" style="font-size: 12px; color: #000000;">' 
                . 'Quick Search: '
            . '</label> '

            . '<input type="text"'
            . ' style="width: 150px;"'
            . ' id="fmpfilter_slsm__input_byid" '
            . ' onkeyup="return fmp_slsm_list__quick_search_filtering(this.value);" ' 
            . ' name="fmpfilter_slsm__input_byid" value="' . $r_fmpfilter_slsm__input_byid . '" />'
            ;

        return <<<EOJS
<tr>
 <td width="15%" class="tabDetailViewDL">$desc</td>
 <td width="85%" class="tabDetailViewDF" colspan="3">

<script>
$h_js

var fmpfilter_slsm_tree__get_item = function(tree, slsm_id) {
    for (var i=0; i<tree.length; i++) {
        if (tree[i].id != slsm_id) {
            if (!tree[i].children) {
                continue;
            }
            
            var o = fmpfilter_slsm_tree__get_item(tree[i].children, slsm_id);
            if (o) {
                return o;
            }
            continue;
        }

        return tree[i];
    }
    
    return false;
}

var fmp_slsm_list__exclude = function(slsm_id) {
    var oInp = document.getElementById("fmpfilter_slsm");
    var aIDs = new Array();
    if (oInp.value) {
        var oStr = new String(oInp.value);
        aIDs = oStr.split(",");
    }

    var aIDs_new = new Array();
    for (var i=0; i<aIDs.length; i++) {
        if (parseInt(slsm_id) == parseInt(aIDs[i])) {
            continue;
        }
        aIDs_new[aIDs_new.length] = aIDs[i];
    }
    
    if (aIDs.length == aIDs_new.length) {
        return false;
    }
    oInp.value = aIDs_new.join(',');
    
    fmp_slsm_list__refresh();
    return false;
}

var fmp_slsm_list__refresh = function() {
    var oInp = document.getElementById("fmpfilter_slsm");
    var aIDs = new Array();
    if (!oInp.value) {
        var oDiv = document.getElementById("fmpfilter_slsm_preview");
        oDiv.innerHTML = '<div style="text-align: center;">-- all available SLSM ID(s)/rollup(s) --</div>';
        return ;
    }
    var oStr = new String(oInp.value);
    aIDs = oStr.split(",");
    
    var oView = document.getElementById("fmpfilter_slsm_preview");
    oView.innerHTML = "";
    
    var hLocs = document.createElement('ul');

    var aIDs_2 = new Array();
    for(var i=0; i<aIDs.length; i++) {
        aIDs_2[i] = fmpfilter_slsm_tree__get_item(fmpfilter_slsm__tree, aIDs[i]);
    }

    var msg = new Array();

    var aIDs_3 = new Array();
    for (var i=0; i<aIDs.length; i++) {

        //I am applying the j-childs on i index
        var i_is_child_of_j = false;
        for (var j=0; j<aIDs.length; j++) {
            if (i == j) {
                continue;
            }

            if (!aIDs_2[j].children) {
                continue;
            }

            var tmpObj = fmpfilter_slsm_tree__get_item(aIDs_2[j].children, aIDs[i]);
            if (tmpObj) {
                if (msg.length < 5) {

                    var kk_already_added = false;
                    for(var kk=0; kk<msg.length; kk++) {
                        if (msg[kk] == aIDs[i]) {
                            kk_already_added = true;
                            break;
                        }
                    } 
                    
                    if (!kk_already_added) {
                        msg[msg.length] = aIDs[i];
                    }
                }
                i_is_child_of_j = true;
            }
        }

        if (!i_is_child_of_j) {
            aIDs_3[aIDs_3.length] = aIDs[i];
        }

    }

    aIDs = aIDs_3;
    document.getElementById("fmpfilter_slsm").value = aIDs.join(',');

    for (var i=0; i<aIDs.length; i++) {
        var slsmGrp = fmpfilter_slsm_tree__get_item(fmpfilter_slsm__tree, aIDs[i]);

        var hItem = document.createElement('li');
        hItem.innerHTML = "" 
            + aIDs[i] + " " + slsmGrp.name
            + '<a href="javascript: void(0);" onclick="return fmp_slsm_list__exclude(\'' + aIDs[i] + '\');" style="float: right;">[del]</a>'
            ;
        hLocs.appendChild(hItem);
    }

    oView.appendChild(hLocs);
    
    if (msg.length) {
        msg = "One or more group(s) are child subgroups, so they are not visible in the filter dialogue: " + msg.join(', ') + '... ';
        alert(msg);
    }
}

//var fmp_slsm_list__is_in_filter = function(slsm_id) {
//    return false;    
//}

var fmp_slsm_list__add = function(slsm_id) {
    var oInp = document.getElementById("fmpfilter_slsm");
    var aIDs = new Array();
    if (oInp.value) {
        var oStr = new String(oInp.value);
        aIDs = oStr.split(",");
    }

    for (var i in aIDs) {
        if (slsm_id == parseInt(aIDs[i])) {
            return true;
        }
    }

    aIDs[aIDs.length] = slsm_id;
    oInp.value = aIDs.join(',');
    return true;
}

var h_click_slsm_add__rollup = function() {
    var sbox = document.getElementById("fmprep_ssar__slsm_tree");

    var a_slsm = new Array();
    for (var i=0; i<sbox.options.length; i++) {
        if (!sbox.options[i].selected) {
            continue;
        }
        
        a_slsm[a_slsm.length] = sbox.options[i].value;
    }

    for (var i=0; i<a_slsm.length; i++) {
//        var is_added = fmp_slsm_list__is_in_filter(a_slsm[i]);
//        if (!is_added) {
            fmp_slsm_list__add(a_slsm[i]);
//        }
    }

    fmp_slsm_list__refresh();
    return true;    
}

var h_ondblclick_slsm_tree = function() {
    var checked = document.getElementById("fmpfilter_slsm__mode").checked;
    if (checked) {
        return true;
    }
    
    var sbox = document.getElementById("fmprep_ssar__slsm_tree");
//    alert(sbox.options[sbox.options.selectedIndex].value);
    return true;
}

var fmp_slsm_list__quick_search_filtering = function(search_str) 
{
    var sbox_src = document.getElementById("fmprep_ssar__slsm_tree__source");
    var sbox_dst = document.getElementById("fmprep_ssar__slsm_tree");

    for(var i=sbox_dst.options.length-1; i>=0 ; i--) {
        sbox_dst.remove(i);
    }

    var sSrch = (new String(search_str)).toUpperCase();

//    var s = '';
    for (var i=0; i<sbox_src.options.length; i++) {
        if (search_str) {
            var sOrig = (new String(sbox_src.options[i].text)).toUpperCase();
            var rs = 1001;
            try {
                rs = sOrig.search("" + sSrch);
            } catch(e) {alert(e);};

            if (rs == -1) {
                continue;
            }
        }

        var oOpt = new Option();
        oOpt.label = sbox_src.options[i].innerText;
        oOpt.text = sbox_src.options[i].text;
        oOpt.value = sbox_src.options[i].value;
        sbox_dst.appendChild(oOpt);
//        s += sbox_src.options[i].text + ' [' + rs + ']' + ', ';
    }
//    alert(s);

    return true;
}
YAHOO.util.Event.addListener(window, "load", function() { fmp_slsm_list__refresh(); });
</script>

    <table cellspacing="2" cellpadding="0" border="0" style="float: left;">
        <tr>
          <td align="center" valign="top">$h_cbox $h_input_byid </td>
          <td align="center" valign="top">&nbsp;</td>
        </tr>
        <tr>
          <td align="center" valign="top">$h_cbox2 $h_btn_slsm_add__rollup</td>
          <td align="center" valign="top"><span style="font-size:12px; color: #000000;">Search by:</span></td>
        </tr>
        <tr>
          <td align="right" valign="top">$h_slsm_tree</td>
          <td align="left" valign="top">
            <style>
                #fmpfilter_slsm_preview ul {margin-bottom:2px;}
            </style>
            <div style="width: 350px; float: left; border: 1px dashed #94C1E8; font-size:12px;">
                <div id="fmpfilter_slsm_preview">$h_filter_values</div>
                <input type="hidden" name="fmpfilter_slsm" value="$fmpfilter_slsm" id="fmpfilter_slsm" />
                <input type="hidden" name="fmpfilter_slsm__tree_item" value="$r_fmpfilter_slsm__tree_item" id="fmpfilter_slsm__tree_item" />
            </div>
          </td>
        </tr>
    </table>

  </td>
</tr>
EOJS;
    }
    
    protected function html__slsm_tree($tree, $level=0) 
    {
       

        $space = '';
        for($i = 0; $i<$level; $i++) {
            $space .= '&nbsp;&nbsp;';
        }
        
        $sbox_out = array();
        foreach($tree as $k=>$v) {
//            $sbox_out[(string)$v['slsm']] = $space . $v['slsm'] . ' ' . $v['firstname'] . ' ' . $v['lastname'];
            $sbox_out[(string)$v['slsm']] = $space . $v['slsm'] . ' ' . $v['name'] . ' (' . $v['company'].')';
            if (!$v['children']) {
                continue;
            }
            $sbox_out += $this->html__slsm_tree($v['children'], $level+1);
        }

        return $sbox_out;
    }

    protected function html__slsm_tree__sigle_elvel__non_recursive($subtree) 
    {

        
        if (!is_array($subtree)) {
            return array();
        }
        
        $sbox_out = array();
        foreach($subtree as $k=>$v) {
//            $sbox_out[$v['slsm']] = $v['slsm'] . ' ' . $v['firstname'] . ' ' . $v['lastname'];
            $sbox_out[$v['slsm']] = $v['slsm'] . ' ' . $v['name'] . ' (' . $v['company'].')';
            if ($v['children']) {
                $sbox_out[$v['slsm']] .= ' --&gt;';
            }
        }
        
        return $sbox_out;
    }

    protected function html__slsm_tree__single_level($tree, $node_id=0) 
    {
        if ($node_id==0) {
            return $this->html__slsm_tree__sigle_elvel__non_recursive($tree);
        }

        $sbox_out = array();
        foreach($tree as $k=>$v) {
            if ($v['slsm'] == $node_id) {
                return $this->html__slsm_tree__sigle_elvel__non_recursive($v['children']);
            }

            if (!$v['children']) {
                continue;
            }
            $sbox_out += $this->html__slsm_tree($v['children'], $level+1) ;
        }

        return $sbox_out;
    }

    protected function html__slsm_tree__js($tree) 
    {
        $sbox_out = array();
        foreach($tree as $k=>$v) {
//            $s_row = 'id: ' . ((int) $v['slsm']) . ', name: \'' . $v['firstname'] . ' ' . $v['lastname'] . '\', children: ';
            $s_row = 'id: ' . ((int) $v['slsm']) . ', name: \'' . $v['name'] . ' (' . $v['company'].')' . '\', children: ';

            if (!$v['children']) {
                $s_row .= 'null';
            } else {
                $s_row .= $this->html__slsm_tree__js($v['children']);
            }

            $s_row = '{' . $s_row . '}';
            $sbox_out[] = $s_row;
        }

        return '[' .  implode(',', $sbox_out) . ']';
    }
}
