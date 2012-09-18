<?php
function getTechTotOpp(&$focus, $field, $value, $view) 
{
    $val = round($focus->technicians_c *2500);
    if ($view == 'DetailView') {
        return $val;
    }

    if ($view == 'EditView') {
        $js = <<<EOJS
<script>
var totOpp_init = function() {
   var eval_totOpp = function() {
        var Tech =  document.getElementById('technicians_c').value;
        var totOpp = (Tech * 2500); 
	document.getElementById('tech_total_opp_c').value = totOpp;
        return true;
    }

    document.getElementById('technicians_c').onkeyup = eval_totOpp; 
}
YAHOO.util.Event.onContentReady('tech_total_opp_c', totOpp_init);
</script>
EOJS;
        return '' 
            . $js 
            . '<input id="tech_total_opp_c" type="text" readonly="readonly" value="' . $val . '" />'
            ;
    }
}
