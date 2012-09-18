<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

function getFleetLeaseOKVPerc(&$focus, $field, $value, $view)
{
$numV = round($focus->fleet_num_vehicles_c);
$numO = round($focus->num_okv_leased_c);

$okvLeasePer = 0;
if ($numV > 0){
$okvLeasePer = round($numO / $numV *100);
}

    if ($view == 'DetailView') {
		return $okvLeasePer . "%";
    }
    if ($view == 'EditView') {
	    $js = <<<EOJS

<script>
var totOpp_init = function() {
   var eval_OKV = function() {
        var leaseOKV =  document.getElementById('num_okv_leased_c').value;
        var numFleet =  document.getElementById('fleet_num_vehicles_c').value;
		var leasePer = (leaseOKV/numFleet)*100;		
		leasePer = parseInt(leasePer);
		document.getElementById('per_okv_leased_c').value =  leasePer;
        return true;
    }
	document.getElementById('fleet_num_vehicles_c').onkeyup = eval_OKV;
	document.getElementById('num_okv_leased_c').onkeyup = eval_OKV;

}
YAHOO.util.Event.onContentReady('num_okv_leased_c', totOpp_init);
</script>
EOJS;

return ''
            . $js
            . '<input id="per_okv_leased_c" type="text" readonly="readonly" value="' . $okvLeasePer . '" />';
            }
}
