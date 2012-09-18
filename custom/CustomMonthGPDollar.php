<?php
function getMonthGPDollar(&$focus, $field, $value, $view)
{
    

    $val = round($focus->gp_perc * $focus->amount_usdollar / 1200);
//     echo '<pre>';
//    print_r($val);
//    echo '</pre>';
    if ($view == 'DetailView') {
        return $val;
    }

    if ($view == 'EditView') {
        $js = <<<EOJS
<script>


    var eval_month_gp_dollar = function() {
        var gp =  document.getElementById('gp_perc').value;
        var amount = document.getElementById('month_sales').value;
        amount = new String(amount).replace(',', '');
        amount = parseInt(amount);
        if (isNaN(amount)) {
            amount = 0;
        }

        var gp_amount = parseInt(gp);
        if (isNaN(gp_amount)) {
            gp_amount = 0;
        }

        var month_gp_dollar = (amount * gp_amount)/100;
        document.getElementById('month_gp_dollar').value = Math.round(month_gp_dollar);

        return true;
    }

    var month_gp_perc_changed = function() {
        var gp =  document.getElementById('month_gp_perc').value;
        var month_amount = document.getElementById('month_sales').value;
        month_amount = new String(month_amount).replace(',', '');
        month_amount = parseInt(month_amount);
        if (isNaN(month_amount)) {
            month_amount = 0;
        }

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
        document.getElementById('gp_perc').value = gp_amount;

        return true;
    }

YAHOO.util.Event.onContentReady("pline_c", eval_month_gp_dollar);
YAHOO.util.Event.onContentReady("pline_c", month_gp_perc_changed);
</script>
EOJS;
        return ''
            . $js
            . '<input id="month_gp_dollar" size="8" type="text"  readonly="readonly" value="' . $val . '" />'
            ;
    }
}