<?php
class fmp_Param_Warehouse {
    
    function __construct() {}
    
    public function build_query_addon() 
    {
        if (!isset($_REQUEST['product_line_hidden'])) {
            return '';
        }
        
        if (!$_REQUEST['product_line_hidden']) {
            return '';
        }

        $product_line_hidden = $_REQUEST['product_line_hidden'];
        
        $product_line_hidden = explode('|', $product_line_hidden);
        
        $out = array();
        foreach($product_line_hidden as $v) {
            $v = explode(',', $v);
            
            $r = array();
            $r[] = 'x_o_pline.pline_id=' . '\'' . $v[0] . '\'';
            
            if ($v[1]) {
                $r[] = 'x_o_pline.pcat_id=' . '\'' . $v[1] . '\'';
                if ($v[2]) {
                    $r[] = 'x_o_pline.pcode_id' . '\'' . $v[2] . '\'';
                }
            }

            $out[] = '(' . implode(' AND ', $r) . ')';
        }

        $out = ' AND (' . implode(' OR ', $out) . ')';

        return $out;
    }

    public function html($desc) 
    {
        require_once 'custom/CustomProductlineField.php';

        $lines = db_pline__pline_list();
        $cats = db_pline__pcat_list();
        $codes = db_pline__pcode_list();
        
        $js = pline_js($lines, $cats, $codes);
        $lines = $cats = $codes = array();

        $product_line_hidden = null;
        if (isset($_REQUEST['product_line_hidden'])) {
            $product_line_hidden = $_REQUEST['product_line_hidden'];
        }
        
        $style = 'margin-bottom: 1px;';
        $h = ''
            . $js
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

        return <<<EOJS
<tr>
 <td class="tabDetailViewDL">{$desc}</td>
 <td class="tabDetailViewDF" colspan="3">
    {$h}
 </td>
</tr>
EOJS;
    }

}