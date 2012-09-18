<?php
function db_pline__pline_list() 
{
    global $db;
    $q = '' 
        . 'SELECT ' 
            . '* ' 
        . 'FROM dsls_pline_simple ORDER BY line'
        ;
    $rs = $db->query($q);

    $out = array();
    while($row = $db->fetchByAssoc($rs)) {
        $row['name'] = $row['line'] . ' - ' . $row['description'];
        $row['value'] = $row['line'];
        $out[] = $row;
    }
    return $out;
}

function db_pline__pcat_list() 
{
    global $db;
    $q = '' 
        . 'SELECT ' 
            . '* ' 
        . 'FROM dsls_cat_simple ORDER BY line, cat'
        ;
    $rs = $db->query($q);

    $out = array();
    while($row = $db->fetchByAssoc($rs)) {
        $row['name'] = $row['cat'] . ' - ' . $row['description'];
        $row['value'] = $row['id'];
        $out[] = $row;
    }
    return $out;
}

function db_pline__pcode_list() 
{
    global $db;
    $q = '' 
        . 'SELECT ' 
            . '* ' 
        . 'FROM dsls_pricecode ORDER BY line, cat, pc'
        ;
    $rs = $db->query($q);

    $out = array();
    while($row = $db->fetchByAssoc($rs)) {
        $row['name'] = $row['pc'] . ' - ' . $row['description'];
        $row['value'] = $row['id'];
        $out[] = $row;
    }
    return $out;
}

function db_pline__pline($pline_id) 
{
    global $db;
    $q = '' 
        . 'SELECT ' 
            . '* ' 
        . 'FROM dsls_pline_simple '
        . 'WHERE line=\'' . $pline_id . '\''
        ;
    $rs = $db->query($q);

    $row = $db->fetchByAssoc($rs);
    if (!$row) {
        return ; 
    }
    $row['name'] = $row['line'] . ' - ' . $row['description'];
    return $row;
}

function db_pline__pcat($pline_id, $pcat_id) 
{
    global $db;
    $q = '' 
        . 'SELECT ' 
            . '* ' 
        . 'FROM dsls_cat_simple '
        . 'WHERE line=\'' . $pline_id . '\''
            . ' AND cat=\'' . $pcat_id . '\''
        ;

    $rs = $db->query($q);

    $row = $db->fetchByAssoc($rs);
    if (!$row) {
        return ;
    }
    $row['name'] = $row['cat'] . ' - ' . $row['description'];
    return $row;
}

function db_pline__pcode($pline_id, $pcat_id, $pcode_id) 
{
    global $db;
    $q = '' 
        . 'SELECT ' 
            . '* ' 
        . 'FROM dsls_pricecode '
        . 'WHERE line=\'' . $pline_id . '\''
            . ' AND cat=\'' . $pcat_id . '\''
            . ' AND pc=\'' . $pcode_id . '\''
        ;
    $rs = $db->query($q);

    $row = $db->fetchByAssoc($rs);
    if (!$row) {
        return ;
    }
    $row['name'] = $row['pc'] . ' - ' . $row['description'];
    return $row;
}

function html_sbox($name, $rows) 
{
    $h = array();
    foreach($rows as $v) {
        $h[] = '<option value="' . $v['value'] . '" />' . $v['name'] . '</option>';
    }

    return ''
        . '<select name="' . $name . '" id="' . $name . '" size="5" tabindex="2" style="width: 150px; margin-right: 3px;">'
            . implode('', $h)
        . '</select>'
        ;
}

function pline_js($lines, $cats, $codes) 
{
    $js1 = array('var pline_a = new Array();');
    foreach($lines as $k=>$v) {
        $js1[] = 'pline_a[' . ((int) $k) . '] = {id: "' . $v['value'] . '", value: "' . $v['name'] . '"};';
    }

    $js2 = array('var pcat_a = new Array();');
    foreach($cats as $k=>$v) {
        $js2[] = 'pcat_a[' . ((int) $k) . '] = {id: "' . $v['value'] . '", value: "' . $v['name'] . '", ' 
            . 'line_id: "' . $v['line'] . '", cat_id: "' . $v['cat'] . '"};';
    }
    
    $js3 = array('var pcode_a = new Array();');
    foreach($codes as $k=>$v) {
        $js3[] = 'pcode_a[' . ((int) $k) . '] = {id: "' . $v['value'] . '", value: "' . $v['name'] . '", '
            . 'line_id: "' . $v['line'] . '", cat_id: "' . $v['cat'] . '", code_id: "' . $v['pc'] . '"};';
    }
    

    $js1 = implode("\n", $js1) . "\n";
    $js2 = implode("\n", $js2) . "\n\n";
    $js3 = implode("\n", $js3);
   
    return "\n" .  <<<PLINE_JS
<script>
{$js1}
{$js2}
{$js3}
var pline_init = function() {

    var sb_clear = function(sboxName) {
        document.getElementById(sboxName).length = 0;
    } 
    
    var sb_pline = function() {
        sb_clear('pline_id');

        var sb = document.getElementById('pline_id');
        sb.options[sb.length] = new Option(' -- Select Line --', -1);

        for(var i=0; i<pline_a.length; i++) {
            sb.options[sb.length] = new Option(pline_a[i].value, pline_a[i].id);
        }
        sb_pcat();
    }

    var sb_pcat = function() {
        sb_clear('pcat_id');
        
        var pline_id = document.getElementById('pline_id').value; 

        var sb = document.getElementById('pcat_id');
        sb.options[sb.length] = new Option(' -- Select Category --', -1);

        for(var i=0; i<pcat_a.length; i++) {
            if (pcat_a[i].line_id != pline_id) {
                continue;
            }
            sb.options[sb.length] = new Option(pcat_a[i].value, pcat_a[i].cat_id);
        }
        
        sb_pcode();
    }

    var sb_pcode = function() {
        sb_clear('pcode_id');
        
        var pline_id = document.getElementById('pline_id').value;
        var pcat_id = document.getElementById('pcat_id').value;

        var sb = document.getElementById('pcode_id');
        sb.options[sb.length] = new Option(' -- Select Price Code --', -1);
//        sb.options[sb.length] = new Option(' -- All --', -2);
        
        for(var i=0; i<pcode_a.length; i++) {

            if (pcode_a[i].line_id != pline_id) {
                continue;
            }

            if (pcode_a[i].cat_id != pcat_id) {
                continue;
            }

            sb.options[sb.length] = new Option(pcode_a[i].value, pcode_a[i].code_id);
        }
    }

    var h_pline_add = function() {
        var pline_id = document.getElementById('pline_id').value;
        var pcat_id = document.getElementById('pcat_id').value;
        var pcode_id = document.getElementById('pcode_id').value;

        if ((pline_id == '-1')  || (pline_id == '-2') ) {
            pline_id = '';
        }
        
        if ((pcat_id == '-1')  || (pcat_id == '-2') ) {
            pcat_id = '';
        }

        if ((pcode_id == '-1')  || (pcode_id == '-2') ) {
            pcode_id = '';
        }
        
        if (!pline_id && !pcat_id && !pcode_id) {
            alert('Please select Line/Category/Price Code');
            return ;
        }
        
        var hidd_c = document.getElementById('product_line_hidden');
        var s = new String( hidd_c.value );
        var arr = s.split('|');

        
        var save_me = pline_id + ',' + pcat_id + ',' + pcode_id;

        var i_empty = -1;
        var i_match = -1;
        for (var i = 0; i<arr.length; i++) {

            if (!arr[i]) {
                i_empty = i;
                continue;
            }
            
            if (arr[i] == save_me) {
                i_empty = -1;
                i_match = i;
                break;
            }
        }

        if (i_match > -1) {
            alert('Such Line/Category/Price Code already is selected');
            arr[i_match] = save_me;
        } else {
            if (i_empty > -1) {
                arr[i_empty] = save_me;
            } else {
                arr[arr.length] = save_me;
            }
        }

        hidd_c.value = arr.join('|');

        refresh();
        return false;
    }
    
    var sb = document.getElementById("pline_id");
    sb.onchange = function() { sb_pcat(); }

    sb = document.getElementById("pcat_id");
    sb.onchange = function() { sb_pcode(); }
    
    document.getElementById("plinebtn_add").onclick = h_pline_add;

    sb_pline();
    refresh();
}

var h_pline_del = function(product_s) {
    var hidd_c = document.getElementById('product_line_hidden');
    var s = new String( hidd_c.value );
    var arr = s.split('|');
    
    var arr2 = new Array();
    var j=0;
    for (var i=0; i<arr.length; i++) {

        if (arr[i] == product_s) {
            continue;
        }
        arr2[j] = arr[i];
        j++;
    }

    if (arr2.length != arr.lenght) {
        hidd_c.value = arr2.join('|');
    }
    
    refresh();
    return ;
}

var refresh = function() {
    var title = function(product_s) {
        var title = '';
        var o = (new String(product_s)).split(',');
        var pline_id = o[0];
        if (!pline_id) {
            return title;
        }

        for (var j=0; j<pline_a.length; j++) {
            if (pline_a[j].id != pline_id) {
                continue;
            } 
            title = pline_a[j].value;
            break;
        }

        var pcat_id = o[1];
        if (!pcat_id) {
            return title;
        }

        for (var j=0; j<pcat_a.length; j++) {
            if (pcat_a[j].cat_id != pcat_id) {
                continue;
            }
            
            if (pcat_a[j].line_id != pline_id) {
                continue;
            }
            title += ', ' + pcat_a[j].value;
            break;
        }

        var pcode_id = o[2];

        if (!pcode_id) {
            return title;
        }

        for (var j=0; j<pcode_a.length; j++) {
            if (pcode_a[j].code_id != pcode_id) {
                continue;
            }

            if (pcode_a[j].line_id != pline_id) {
                continue;
            }
            
            if (pcode_a[j].cat_id != pcat_id) {
                continue;
            }
            
            title += ', ' + pcode_a[j].value;
        }

        return title;
    }

    var b_canvas = document.getElementById('pline_list_display');
    b_canvas.innerHTML = '';
    
    var hidd_c = document.getElementById('product_line_hidden');
    if (!hidd_c.value) {
        return ;
    }
    var s = new String( hidd_c.value );
    var arr = s.split('|');

    for (var i=0; i<arr.length; i++) {
        var p = document.createElement('div');

        p.innerHTML = ''
            + '<div>'
                + '<div style="float: left; width: 400px; overflow: hidden;">'
                    + title(arr[i])
                + '</div>'
                + '<div style="float: left: width: 50px; padding-left: 3px;">'
                    + '<a href="javascript: void(0);" onclick="return h_pline_del(\'' + arr[i] + '\')">del</a>'
                + '</div>'
                + '<div style="clear: both;"></div>'
            + '</div>'
            ;
        b_canvas.appendChild(p);
    }

}

YAHOO.util.Event.onContentReady("pline_c", pline_init);
</script>
PLINE_JS;
}

function pline_desc($pline_id, $pcat_id, $pcode_id) 
{
    $desc = '';

    $x = db_pline__pline($pline_id);
    $desc = $x['name'];

    if (!$pcat_id) {
        return $desc;
    }
    
    $x = db_pline__pcat($pline_id, $pcat_id);
    if (!$x) {
        return $desc;
    }
    $desc .= ', ' . $x['name'];

    if (!$pcode_id) {
        return $desc;
    }
    $x = db_pline__pcode($pline_id, $pcat_id, $pcode_id);
    if (!$x) {
        return $desc;
    }
    
    $desc .= ', ' . $x['name'];
    return $desc;
}

function getProductLine(&$focus, $field, $value, $view) 
{
    if ($view == 'DetailView') {
        $h = array();
        foreach($focus->product_line as $v) {
            $h[] = pline_desc($v['pline_id'], $v['pcat_id'], $v['pcode_id']);
        }

        return implode('<br>', $h);
    }

    if ($view == 'EditView') {
        $lines = db_pline__pline_list();
        $cats = db_pline__pcat_list();
        $codes = db_pline__pcode_list();

        $product_line_hidden = array();
        if ($focus->product_line) {
            foreach($focus->product_line as $v) {
                $product_line_hidden[] = $v['pline_id'] . ',' . $v['pcat_id'] . ',' . $v['pcode_id'];
            }
        }
        $product_line_hidden = implode('|', $product_line_hidden);
        
        $js = pline_js($lines, $cats, $codes);

        $lines = $cats = $codes = array();
        
        $style = 'margin-bottom: 1px;';
        $h = ''
            . $js
            . '<input type="hidden" name="product_line" value="1" />'
            . '<div id="pline_c">'
                . '<div style="' . $style . '">'
                    . html_sbox('pline_id', $lines)
                    . html_sbox('pcat_id', $cats)
                    . html_sbox('pcode_id', $codes)
                . '</div>'
                . '<div style="' . $style . '">'
                    . '<a href="javascript: void(0);" id="plinebtn_add">Add Selected Product</a>'
                . '</div>'
                . '<div id="pline_list_display" style="' . $style . '"></div>'
                . '<input type="hidden" name="product_line_hidden" id="product_line_hidden" value="' . $product_line_hidden . '">'
            . '</div>'
            ;
        return $h;
    }

    return null;
}
