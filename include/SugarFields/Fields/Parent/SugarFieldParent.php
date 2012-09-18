<?php
/*********************************************************************************
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004 - 2009 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 * *******************************************************************************/
require_once('include/SugarFields/Fields/Base/SugarFieldBase.php');

class SugarFieldParent extends SugarFieldBase {
   
	function getDetailViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex) {
		$nolink = array('Users', 'Teams');
		if(in_array($vardef['module'], $nolink)){
			$this->ss->assign('nolink', true);
		}else{
			$this->ss->assign('nolink', false);
		}
        $this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);
        return $this->fetch('include/SugarFields/Fields/Parent/DetailView.tpl');
    }
    
    function getEditViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex) {
        require_once 'include/QuickSearchDefaults.php';
        $qsd = new QuickSearchDefaults();
        $o = $qsd->getQSParent();
        $o['field_list'][] = 'custno_c';
        $o['populate_list'] = array("parent_name", 'parent_name_custno', "parent_id");
        $sqs_objects['parent_name'] = $o;

        $o = $qsd->getQSParent();
        $o['field_list'] = array('custno_c', 'name', 'id');
        $o['populate_list'] = array('parent_name_custno', "parent_name", "parent_id");
        $o['conditions'][0]['name'] = 'custno_c';
        $o['order'] = 'custno_c';
        $sqs_objects['parent_name_custno'] = $o;

        $json = getJSONobj();
        $quicksearch_js = array();
        foreach($sqs_objects as $sqsfield=>$sqsfieldArray){
           $quicksearch_js[] = "sqs_objects['$sqsfield']={$json->encode($sqsfieldArray)};";
        }
        
        $quicksearch_js = ''
            . '{literal}'
            . '<script language="javascript">'
                . "if(typeof sqs_objects == 'undefined'){var sqs_objects = new Array;}"
                . implode("\n", $quicksearch_js)
            . '</script>'
            . '{/literal}'
            ;

    	$form_name = 'EditView';
    	if(isset($displayParams['formName'])) {
    		$form_name = $displayParams['formName'];
    	}

    	$popup_request_data = array(
			'call_back_function' => 'set_return',
			'form_name' => $form_name,
			'field_to_name_array' => array(
											'id' => $vardef['id_name'],
											'name' => $vardef['name'],
									 ),
		);


		global $app_list_strings;
		$parent_types = $app_list_strings['record_type_display'];
		$disabled_parent_types = ACLController::disabledModuleList($parent_types,false, 'list');
		foreach($disabled_parent_types as $disabled_parent_type){
			if($disabled_parent_type != $focus->parent_type){
				unset($parent_types[$disabled_parent_type]);
			}
		}

		$json = getJSONobj();
		$displayParams['popupData'] = '{literal}'.$json->encode($popup_request_data).'{/literal}';
    	$displayParams['disabled_parent_types'] = '<script>var disabledModules='. $json->encode($disabled_parent_types).';</script>';
    	$this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);

    	$js = <<<EOS
{literal}
<script type="text/javascript">
var disabledModules=[];
var changeQS = function() {
    new_module = document.getElementById('parent_type').value;

    if(typeof(disabledModules[new_module]) != 'undefined') {
        sqs_objects['parent_name']['disable'] = true;
        document.getElementById('parent_name').readOnly = true;
    }
    else {
        sqs_objects['parent_name']['disable'] = false;
        document.getElementById('parent_name').readOnly = false;
    }   
    sqs_objects['parent_name']['modules'] = new Array(new_module);

    var type = (new String(new_module)).toLowerCase();
    sqs_objects["parent_name"]["field_list"] = new Array("name", "id");
    sqs_objects["parent_name"]["populate_list"] = new Array("parent_name", "parent_id");
    document.getElementById('parent_name_custno').style.display = 'none';

    if ( type == "accounts") {
        sqs_objects["parent_name"]["field_list"] = new Array("name", "id", "custno_c");
        sqs_objects["parent_name"]["populate_list"] = new Array("parent_name", "parent_id", "parent_name_custno");
        document.getElementById('parent_name_custno').style.display = '';
    } else {
        document.getElementById('parent_name_custno').value = '';    
    }

    enableQS(false);
}
YAHOO.util.Event.onContentReady('EditView', function() {changeQS();});
</script>
{/literal}
EOS;

        return '' 
            . $quicksearch_js 
            . $this->fetch('include/SugarFields/Fields/Parentfmp/EditView.tpl') 
            . $js
            ;
    }
 	
    function getSearchViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex) {
		$form_name = 'search_form';
				
    	if(isset($displayParams['formName'])) {
    		$form_name = $displayParams['formName'];
    	}
    	
    	$this->ss->assign('form_name', $form_name);

    	$popup_request_data = array(
			'call_back_function' => 'set_return',
			'form_name' => $form_name,
			'field_to_name_array' => array(
											'id' => $vardef['id_name'],
											'name' => $vardef['name'],
									 ),
		);


		global $app_list_strings;
		$parent_types = $app_list_strings['record_type_display'];
		$disabled_parent_types = ACLController::disabledModuleList($parent_types,false, 'list');
		foreach($disabled_parent_types as $disabled_parent_type){
			if($disabled_parent_type != $focus->parent_type){
				unset($parent_types[$disabled_parent_type]);
			}
		}

		$json = getJSONobj();
		$displayParams['popupData'] = '{literal}'.$json->encode($popup_request_data).'{/literal}';
    	$displayParams['disabled_parent_types'] = '<script>var disabledModules='. $json->encode($disabled_parent_types).';</script>';
    	$this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);        
        return $this->fetch('include/SugarFields/Fields/Parent/SearchView.tpl');
    }
}
?>
