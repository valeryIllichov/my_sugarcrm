<?php
function copyBilling(&$focus, $field, $value, $view) 
{
    $val = $focus->copy_ship_c;
       echo "in script";
	if ($view == 'EditView') {
		$js = <<<EOJS
<script>
var updateShipStreet = function() {
   var copyShipStreet = function() {
   
   
   	var street =  document.getElementById('billing_address_street').value;
	document.getElementById('shipping_address_street').value = street;
	var city =  document.getElementById('billing_address_city').value;
	document.getElementById('shipping_address_city').value = city;
	var state =  document.getElementById('billing_address_state').value;
	document.getElementById('shipping_address_state').value = state;
	var zip =  document.getElementById('billing_address_postalcode').value;
	document.getElementById('shipping_address_postalcode').value = zip;
        return true;
	
    }
	    document.getElementById('copy_ship_c').onclick = copyShipStreet; 
	}
YAHOO.util.Event.onContentReady("copy_ship_c", updateShipStreet);
</script>
EOJS;
    
        return '' 
            . $js
            .'<input id="copy_ship_c" name = "copy_ship_c" type="checkbox"value="' . $val . '" />'
            ;
    }
}
