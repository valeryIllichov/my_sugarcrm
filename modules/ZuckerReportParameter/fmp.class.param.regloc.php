<?php
class fmp_Param_RegLoc {
    protected $user_id = 0;
    protected $a_regions = array();

    function __construct($user_id) {
        $this->user_id = $user_id;
    }

    public function init() {
        $this->a_regions = $this->init__a_regions($this->user_id);
    }

    public function html($desc) {
        return $this->html__control($this->a_regions, $desc);
    }

    public function html_for_daily_sales($user_id, $onclick = '', $id = '') {
        $mass_reg = $this->init__a_regions($user_id);
        $select_creater = '<select id="'.$id.'fmp_reg_loc" '.$onclick.' size="15" multiple="multiple" style="width: 170px;">';
        $select_creater .= '<option value="all" style="border-bottom: 2px solid grey;">ALL</option>';
        foreach ($mass_reg as $key=>$value) {
            $select_creater .= '<option value="r'.$key.'" style="font-weight: bold;">'.$key.' '.$value['name'].'</option>';
                foreach ($value['locs'] as $key_loc => $val_loc){
                    $select_creater .= '<option value="'.$key_loc.'">&nbsp;&nbsp;&nbsp;&nbsp;'.$val_loc.'</option>';
                }
        }
        $select_creater .= '</select>';
        return $select_creater;
    }
    public function get_id_for_area($user_id){
        return $this->init__a_regions($user_id);
    }

    public function is_assigned_regons() {
        return (int) $this->a_regions;
    }

    public function compile__available_regions($r_regloc) {
        $r_ids = array();
        foreach($r_regloc as $r_loc_id) {
            foreach($this->a_regions as $reg_id=>$vv) {
                foreach($vv['locs'] as $loc_id => $v) {
                    if ($loc_id != $r_loc_id) {
                        continue;
                    }
                    $r_ids[] = $loc_id;
                }
            }
        }

        if ($r_ids) {
            return $r_ids;
        }

        $r_ids = array();
        foreach($this->a_regions as $reg_id=>$vv) {
            foreach($vv['locs'] as $loc_id => $v) {
                $r_ids[] = $loc_id;
            }
        }
        return $r_ids;
    }
//get below locations for transforming region to locations
    public function compile__available_regions_below($r_regloc, $region_parent_id) {
        $r_ids = array();
        foreach($r_regloc as $r_loc_id) {
            foreach($this->a_regions as $reg_id=>$vv) {
                foreach($vv['locs'] as $loc_id => $v) {
                    if ($loc_id != $r_loc_id) {
                        continue;
                    }
                    $r_ids[] = $loc_id;
                }
            }
        }

        if ($r_ids) {
            return $r_ids;
        }

        $r_ids = array();
        foreach($this->a_regions as $reg_id=>$vv) {
	    if($region_parent_id == $reg_id) {
            foreach($vv['locs'] as $loc_id => $v) {
		
                	$r_ids[] = $loc_id;
		}
            }
        }
        return $r_ids;
    }

    protected function init__a_regions($user_id) {
        global $db;

        $d_locations = array();
        $q = 'SELECT * FROM dsls_locations';
        $rs = $db->query($q, false);
        while(($row = $db->fetchByAssoc($rs)) != null) {
            $d_locations[$row['loc']] = $row;
        }



        $d_regions = array();
        $q = 'SELECT * FROM dsls_regions';
        $rs = $db->query($q, false);
        while(($row = $db->fetchByAssoc($rs)) != null) {
            $sub = array();
            foreach($d_locations as $k=>$v) {
                if ($v['region'] == $row['region']) {
                    $sub[$k] = $v['city'];
                }
            }
            $d_regions[$row['region']]  = array(
                    'name' => $row['rgnname'],
                    'locs' => $sub
            );
        }

        $q = ''
                . 'SELECT '
                . 'x_s.id,'
                . 'x_s.name '
                . 'FROM securitygroups_users AS x_su '
                . 'INNER JOIN securitygroups AS x_s '
                . 'ON x_su.securitygroup_id = x_s.id '
                . 'WHERE '
                . 'x_su.user_id = \'' . $db->quote($user_id) . '\' '
                . 'AND x_su.deleted = 0  AND x_s.deleted = 0 '
                . 'AND ('
                . ' x_s.name LIKE \'' . $db->quote('Region%') . '\''
                . ' OR x_s.name LIKE \'' . $db->quote('L%')  . '\''
                . ')'
        ;
        $rs = $db->query($q, false);

        $o = array();
        while(($row = $db->fetchByAssoc($rs)) != null) {
            $o[$row['id']] = $row['name'];
        }

        $a_regions = array();
        $a_locations = array();

        foreach($o as $group_id => $group_name) {
            if (strtoupper($group_name[0]) == 'L') {
                $group_name = explode('_', $group_name);
                $group_name = $group_name[0];
                $group_name = (int) str_ireplace('L', '', $group_name);
                $a_locations[$group_name] = $d_locations[$group_name]['region'];
                continue;
            }

            $group_name = explode('_', $group_name);
            $group_name = $group_name[0];
            $group_name = (int) str_ireplace('region', '', $group_name);
            $a_regions[$group_name] = $d_regions[$group_name];
        }

        foreach($a_locations as $loc_id => $reg_id) {
            if (isset($a_regions[$reg_id]['locs'][$loc_id])) {
                continue;
            }

            if (!isset($a_regions[$reg_id])) {
                $a_regions[$reg_id] = $d_regions[$reg_id];
                $a_regions[$reg_id]['locs'] = array();
            }

            $a_regions[$reg_id]['locs'][$loc_id] = $d_regions[$reg_id]['locs'][$loc_id];
        }

        ksort($a_regions);
        foreach($a_regions as $reg_id => $v) {
            ksort($a_regions[$reg_id]['locs']);
        }

        return $a_regions;
    }

    protected function html__control($a_regions, $desc) {
//        echo '<pre>';
//        print_r($_REQUEST);
//        echo '</pre>';

        global $current_user;

        $report_id = $_REQUEST['record'];
        $focus =& new QueryTemplate();

        if(isset($_REQUEST['record'])) {
            $focus->retrieve($_REQUEST['record']);
        }

        $report_name =strtolower($focus->name);
        $settings_duration = 0;
        switch ($report_name) {
            case 'opportunities':
                if($current_user->getPreference('ORPersonalSettings') != null) {
                    $settings_duration = $current_user->getPreference('ORPersonalSettings');
                }
                else {
                    $settings_duration = 30*24*60*60;
                }
                break;
            default:
                if($current_user->getPreference('SASRPersonalSettings') != null ) {
                    $settings_duration = $current_user->getPreference('SASRPersonalSettings');
                }
                else {
                    $settings_duration = 7*24*60*60;
                }
                break;
        }
        $current_date = mktime();
        $disabled = '';
        if (!$a_regions) {
            $disabled = ' disabled="disabled"';
        }

        $h_regs = array('-1' => ' -- ALL -- ');
        foreach($a_regions as $reg_id=>$v) {
            $h_regs[$reg_id] = $reg_id . ' ' . $v['name'];
        }
        $r_reg_id = -1;
        $settings_expired_time = (int) $current_user->getPreference($report_id.'modified');
        if($settings_expired_time == 0) $settings_expired_time = $current_date;
        if($settings_expired_time >= $current_date) $preference_r_reg_id = $current_user->getPreference($report_id.'fmprep_ssar__reg');
        if ($preference_r_reg_id != '') {
            $r_reg_id = $preference_r_reg_id;
        }
        if (isset($_REQUEST['run']) && $_REQUEST['run'] == 'true' && isset($_REQUEST['fmprep_ssar__reg'])) {
            //echo 'we are in run';
            $r_reg_id = $_REQUEST['fmprep_ssar__reg'];
            $current_user->setPreference($report_id.'fmprep_ssar__reg', $r_reg_id);
            $current_user->setPreference($report_id.'modified', $current_date+$settings_duration);
        }
        $h_regs = get_select_options_with_id($h_regs, $r_reg_id);
        $h_regs = ''
                . '<select style="width: 170px" size="15" name="fmprep_ssar__reg"'
                . $disabled
                . ' id="fmprep_ssar__reg" onchange="return h_onchange_reg();">'
                . $h_regs
                . '</select>'
        ;

        $r_loc_ids = array();
        $preference_r_loc_ids = array();
        if($settings_expired_time >= $current_date) $preference_r_loc_ids = $current_user->getPreference($report_id.'fmprep_ssar__loc');
        if ( is_array($preference_r_loc_ids) && count($preference_r_loc_ids) != 0) {
            $r_loc_ids = $preference_r_loc_ids;
        }

        if (isset($_REQUEST['run']) && $_REQUEST['run'] == 'true' && isset($_REQUEST['fmprep_ssar__loc'])) {
            if (is_array($_REQUEST['fmprep_ssar__loc'])) {
                $r_loc_ids = $_REQUEST['fmprep_ssar__loc'];
                $current_user->setPreference($report_id.'fmprep_ssar__loc', $r_loc_ids);
            }
        }


        $r_loc_ids = array_flip($r_loc_ids);

        $h_locs = array();
        foreach($a_regions as $reg_id=>$r) {

            if ($r_reg_id != -1) {
                if ($r_reg_id != $reg_id) {
                    continue;
                }
            }

            $h_opt_grp = array();
            foreach($r['locs'] as $loc_id => $v) {
                $selected = '';
                if (isset($r_loc_ids[$loc_id])) {
                    $selected = ' selected="selected"';
                }
                $h_opt_grp[$loc_id] = '<option' . $selected . ' value="' . $loc_id .'">' . $loc_id . ' ' . $v . '</option>';
            }
            $h_locs[] = '<optgroup label="' . $reg_id . ' ' . $r['name'] . '">' . implode('', $h_opt_grp) . '</optgroup>';
        }
        $h_locs = implode('', $h_locs);


        $h_js = array();
        $i = 0;
        foreach($a_regions as $reg_id=>$vv) {
            $h_js[] = 'a_regions[' . $i . '] = {name:"' . $vv['name'] . '", id: "' . $reg_id . '", locs: []};' . "\n";
            $j = 0;
            foreach($vv['locs'] as $loc_id => $v) {
                $h_js[] = 'a_regions[' . $i . '].locs[' . $j . '] = {name: "' . $v . '", id: "' . $loc_id . '"};' . "\n";
                $j++;
            }
            $i++;
        }
        $h_js = implode('', $h_js) . "\n";

        $h_btn_reg_add = ''
                . '<input style="width: 110px;" type="button"'
                . ' onclick="return h_click_region_add();" id="fmpbtn_reg_add"'
                . $disabled
                . ' value="Add Region" />'
        ;

        $h_btn_loc_add = ''
                . '<input style="width: 110px;" type="button"'
                . ' onclick="return h_click_location_add();" id="fmpbtn_loc_add"'
                . $disabled
                . ' value="Add Location(s)" />'
        ;

        unset($a_regions);
        $h_locs = ''
                . '<select style="width: 170px" size="15"'
                . $disabled
                . ' name="fmprep_ssar__loc[]" id="fmprep_ssar__loc" multiple="multiple">'
                . $h_locs
                . '</select>'
        ;

        $h_filter_values = '<div style="text-align: center;"> -- all available region(s)/location(s) -- </div>';
        $fmpfilter_locs = '';
        $preference_fmpfilter_locs = '';
        if($settings_expired_time >= $current_date) $preference_fmpfilter_locs = $current_user->getPreference($report_id.'fmpfilter_locs');
        if ( $preference_fmpfilter_locs != '') {
            $fmpfilter_locs = $preference_fmpfilter_locs;
        }
        if (isset($_REQUEST['run']) && $_REQUEST['run'] == 'true' && isset($_REQUEST['fmpfilter_locs'])) {
            $fmpfilter_locs = $_REQUEST['fmpfilter_locs'];
            $current_user->setPreference($report_id.'fmpfilter_locs', $fmpfilter_locs);
        }



        return <<<EOJS
<tr>
 <td class="tabDetailViewDL">$desc</td>
 <td class="tabDetailViewDF" colspan="3">
<script>
var a_regions = new Array();
                $h_js

var h_onchange_reg = function() {
    var o = document.getElementById("fmprep_ssar__reg");
    var r_reg_id = o.options[o.options.selectedIndex].value;
    
    var sbox = document.getElementById("fmprep_ssar__loc");
    sbox.options.length = 0;
    for (var i = sbox.childNodes.length-1; i >= 0 ; i--) {
        sbox.removeChild(sbox.childNodes[i]);
    }


    for (var ii in a_regions) {
        if (parseInt(r_reg_id) != -1) {
            if (parseInt(r_reg_id) != parseInt(a_regions[ii].id)) {
                continue;
            }
        }        

        var oGrp = document.createElement('optgroup');
        oGrp.label = a_regions[ii].id + ' ' + a_regions[ii].name;

        for (i in a_regions[ii].locs) {
            var oOpt = new Option();
            oOpt.label = a_regions[ii].locs[i].id + " " + a_regions[ii].locs[i].name;
            oOpt.text = oOpt.label;
            oOpt.value = a_regions[ii].locs[i].id;
            oGrp.appendChild(oOpt);
        }

        sbox.appendChild(oGrp);
    }
    return true;
}

var h_click_location_add = function() {
    var sbox = document.getElementById("fmprep_ssar__loc");
    for (var i=0; i < sbox.options.length; i++) {
        if (!sbox.options[i].selected) {
            continue;
        }

        fmp_regloc__add(parseInt(sbox.options[i].value));
    }    
}

var h_click_region_add = function() {
    var sbox = document.getElementById("fmprep_ssar__reg");
    var reg_id  = parseInt(sbox.options[sbox.options.selectedIndex].value);

    for(var ii=0; ii < a_regions.length; ii++ ) {
        if (reg_id != -1) {
            if ( reg_id != parseInt(a_regions[ii].id) ) {
                continue;
            }
        }

        for (var i = 0; i<a_regions[ii].locs.length; i++) {
            fmp_regloc__add(a_regions[ii].locs[i].id);
        }
    }

    fmp_regloc_list__refresh();
    return true;
}

var fmp_regloc__add = function(loc_id) {
    var oInp = document.getElementById("fmpfilter_locs");
    var aIDs = new Array();
    if (oInp.value) {
        var oStr = new String(oInp.value);
        aIDs = oStr.split(",");
    }

    for (var i in aIDs) {
        if (loc_id == parseInt(aIDs[i])) {
            return true;
        }
    }
   
    aIDs[aIDs.length] = loc_id;
    oInp.value = aIDs.join(',');

    fmp_regloc_list__refresh();
    return true;
}

var fmp_regloc_list__refresh = function() {
    var oInp = document.getElementById("fmpfilter_locs");
    var aIDs = new Array();
    if (!oInp.value) {
        var oDiv = document.getElementById("fmpfilter_locs_preview");
        oDiv.innerHTML = '<div style="text-align: center;">-- all available region(s)/location(s) --</div>';
        return ;
    }
    var oStr = new String(oInp.value);
    aIDs = oStr.split(",");
    
      

    var b_regions = new Array();

    var oView = document.getElementById("fmpfilter_locs_preview");
    oView.innerHTML = "";
    for (var ii=0; ii < a_regions.length; ii++) {
        var is_locs = false;
        for (var i=0; i < a_regions[ii].locs.length; i++) {

            for (var j=0; j<aIDs.length; j++) {
                if ( parseInt(aIDs[j]) != parseInt(a_regions[ii].locs[i].id) ) {
                    continue;
                }
                is_locs = true;
                break;
            }
        }

        if (!is_locs) {
            continue;
        }

        var hLocs = document.createElement('ul');
        for (var i=0; i < a_regions[ii].locs.length; i++) {
            for (var j=0; j<aIDs.length; j++) {
                if ( parseInt(aIDs[j]) != parseInt(a_regions[ii].locs[i].id) ) {
                    continue;
                }
                
                var hItem = document.createElement('li');
                hItem.innerHTML = "" 
                    + a_regions[ii].locs[i].id + " " + a_regions[ii].locs[i].name
                    + '<a href="javascript: void(0);" onclick="return fmp_regloc_list__exclude(\'' + a_regions[ii].locs[i].id + '\');" style="float: right;">[del]</a>'
                    ;
                hLocs.appendChild(hItem);
                break; 
            }
        }
        
        var hGrp = document.createElement('div');
        hGrp.innerHTML = "" 
            + "<strong>" + a_regions[ii].id + " " + a_regions[ii].name + "</strong>"
            + '<a href="javascript: void(0);" onclick="return fmp_regloc_list__exclude_reg(\'' + a_regions[ii].id + '\');" style="float: right; margin-right: 30px;">[del]</a>'
            + "<br>"
            + hLocs.innerHTML
            ;
        oView.appendChild(hGrp);
    }
}

var fmp_regloc_list__exclude = function(loc_id) {
    var oInp = document.getElementById("fmpfilter_locs");
    var aIDs = new Array();
    if (oInp.value) {
        var oStr = new String(oInp.value);
        aIDs = oStr.split(",");
    }

    var aIDs_new = new Array();
    for (var i=0; i<aIDs.length; i++) {
        if (parseInt(loc_id) == parseInt(aIDs[i])) {
            continue;
        }
        aIDs_new[aIDs_new.length] = aIDs[i];
    }
    
    if (aIDs.length == aIDs_new.length) {
        return false;
    }
    oInp.value = aIDs_new.join(',');
    fmp_regloc_list__refresh();
    return false;
}

var fmp_regloc_list__exclude_reg = function(reg_id) {
    var oInp = document.getElementById("fmpfilter_locs");
    var aIDs = new Array();
    if (oInp.value) {
        var oStr = new String(oInp.value);
        aIDs = oStr.split(",");
    }
    
    var locs = new Array();
    for (var i=0; i<a_regions.length; i++) {
        if (a_regions[i].id != reg_id) {
            continue;
        } 
        locs = a_regions[i].locs;
        break;
    }

    if (!locs) {
        return ;
    }
    

    
    
    var aIDs_new = new Array();
    for (var i=0; i<aIDs.length; i++) {

        var is_here = false; 
        for (var j=0; j<locs.length; j++) {
            if (parseInt(locs[j].id) == aIDs[i]) {
                is_here = true;
                break;
            } 
        }

        if (is_here) {
            continue;
        }

        aIDs_new[aIDs_new.length] = aIDs[i];
    }
    
    if (aIDs.length == aIDs_new.length) {
        return false;
    }

    oInp.value = aIDs_new.join(',');
    fmp_regloc_list__refresh();
    return false;
}

YAHOO.util.Event.addListener(window, "load", function() { fmp_regloc_list__refresh(); });
</script>
    <table cellspacing="2" cellpadding="0" border="0" style="float: left;">
        <tr>
          <td align="center" valign="top">$h_btn_reg_add</td>
          <td align="center" valign="top">$h_btn_loc_add</td>
          <td align="center" valign="top"><div style="font-size: 12px; color: #000000;">Search by:</div></td>
        </tr>
        <tr>
          <td align="left" valign="top">$h_regs</td>
          <td align="left" valign="top">$h_locs</td>
          <td align="left" valign="top">
            <div style="font-size: 12px; width: 350px; float: left; border: 1px dashed #94C1E8;">
                <div id="fmpfilter_locs_preview">$h_filter_values</div>
                <input type="hidden" name="fmpfilter_locs" value="$fmpfilter_locs" id="fmpfilter_locs" />
            </div>
          </td>
        </tr>
    </table>

    <div style="clear: both;"></div>
 </td>
</tr>
EOJS;
    }
}
