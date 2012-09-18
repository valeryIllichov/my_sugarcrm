<?php
function getGPDollar(&$focus, $field, $value, $view) 
{
    $val = round($focus->gp_perc * $focus->amount_usdollar / 100);
    if ($view == 'DetailView') {
        return $val;
    }

    if ($view == 'EditView') {
        $js = <<<EOJS
<script>
   

    var eval_gp_dollar = function() {
        var gp =  document.getElementById('gp_perc').value;
        var amount = document.getElementById('amount').value;
        amount = new String(amount).replace(',', '');
        amount = parseInt(amount);
        if (isNaN(amount)) {
            amount = 0;
        }

        var gp_amount = parseInt(gp);
        if (isNaN(gp_amount)) {
            gp_amount = 0;
        }

        var gp_dollar = (amount * gp_amount)/100;
        var month_gp_dollar = (amount * gp_amount)/1200;
        document.getElementById('gp_dollar').value = Math.round(gp_dollar);
        document.getElementById('month_gp_dollar').value = Math.round(month_gp_dollar);
        document.getElementById('month_gp_perc').value = gp_amount;

        return true;
    }

YAHOO.util.Event.onContentReady("pline_c", eval_gp_dollar);
</script>
EOJS;
        return '' 
            . $js 
            . '<input id="gp_dollar" size="8" type="text" readonly="readonly" value="' . $val . '" />'
            ;
    }
}