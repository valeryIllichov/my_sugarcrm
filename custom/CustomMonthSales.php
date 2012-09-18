<?php
function getMonthSales(&$focus, $field, $value, $view)
{
    $val = round( $focus->amount_usdollar / 12);
    if ($view == 'DetailView') {
        return $val;
    }

        if ($view == 'EditView') {
        
        $js = <<<EOJS
<script>

    var eval_month_sales = function() {
        var amount = document.getElementById('amount').value;
        amount = new String(amount).replace(',', '');
        amount = parseInt(amount);
        if (isNaN(amount)) {
            amount = 0;
        }

       

        var month_sales = amount/12;
        document.getElementById('month_sales').value = Math.round(month_sales);

        return true;
    }

     var eval_sales = function() {
        var gp =  document.getElementById('gp_perc').value;
        var month_amount = document.getElementById('month_sales').value;
        month_amount = new String(month_amount).replace(',', '');
        month_amount = parseInt(month_amount);
        if (isNaN(month_amount)) {
            month_amount = 0;
        }

        var gp_amount = parseInt(gp);
        if (isNaN(gp_amount)) {
            gp_amount = 0;
        }

        var sales = month_amount * 12;
        var gp_dollar = (sales * gp_amount)/100;
        document.getElementById('gp_dollar').value = Math.round(gp_dollar);
        document.getElementById('amount').value = sales;

        return true;
    }

    
   

YAHOO.util.Event.onContentReady("pline_c", eval_month_sales);
YAHOO.util.Event.onContentReady("pline_c", eval_sales);
</script>
EOJS;
           return ''
            . $js
            . '<input id="month_sales" size="8" onkeyup="eval_sales(); eval_month_gp_dollar();"  type="text"  value="' . round($val) . '" />'
            ;
    }
}