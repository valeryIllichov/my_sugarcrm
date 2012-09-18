<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

function getFleetTotOpp(&$focus, $field, $value, $view) 
{
require_once('modules/Currencies/Currency.php');
$val = round($focus->fleet_num_vehicles_c * $focus->percent_okv_c * 252);
$okvperc = $focus->percent_okv_c . "%";
    if ($view == 'DetailView') {
	$focus->percent_okv_c = $okvperc;
	return currency_format_number($val);
    }

    if ($view == 'EditView') {
	$js = <<<EOJS

<script>
var totOpp_init = function() {
   var eval_totOpp = function() {
        var Tech =  document.getElementById('fleet_num_vehicles_c').value;
        var OKVP =  document.getElementById('percent_okv_c').value;
	var totOpp = Tech * (OKVP/100) * 252;
	
	document.getElementById('fleet_tot_opp_c').value =  totOpp;
	return true;

    }
	document.getElementById('fleet_num_vehicles_c').onkeyup = eval_totOpp;
	document.getElementById('percent_okv_c').onkeyup = eval_totOpp;

 
}
YAHOO.util.Event.onContentReady('fleet_tot_opp_c', totOpp_init);

</script>


EOJS;
return '' 
            . $js 
            . '<input id="fleet_tot_opp_c" type="text" readonly="readonly" value="' . $val . '" />';
            }
}
