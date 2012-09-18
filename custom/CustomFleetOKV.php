<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

function getFleetOwnOKVPerc(&$focus, $field, $value, $view)
{
$numV = round($focus->fleet_num_vehicles_c);
$numO = round($focus->num_okv_owned_c);
$okvOwnPer = 0;
if($numV>0){
	$okvOwnPer = round($numO / $numV *100);
}
    if ($view == 'DetailView') {
		return $okvOwnPer . "%";
    }
    if ($view == 'EditView') {
	    $js = <<<EOJS

<script>
var totOpp_init = function() {
   var eval_OKV = function() {
        var ownOKV =  document.getElementById('num_okv_owned_c').value;
        var numFleet =  document.getElementById('fleet_num_vehicles_c').value;
		var ownPer = (ownOKV/numFleet)*100;		
		ownPer = parseInt(ownPer);
		document.getElementById('per_okv_owned_c').value =  ownPer;
        return true;
    }
	document.getElementById('fleet_num_vehicles_c').onkeyup = eval_OKV;
	document.getElementById('num_okv_owned_c').onkeyup = eval_OKV;

}
YAHOO.util.Event.onContentReady('num_okv_owned_c', totOpp_init);
</script>
EOJS;

return ''
            . $js
            . '<input id="per_okv_owned_c" type="text" readonly="readonly" value="' . $okvOwnPer . '" />';
            }
}
