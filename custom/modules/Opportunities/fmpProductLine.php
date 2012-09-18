<?php
class fmpProductLine {
    public function after_save(&$bean, $event, $arguments) 
    {
        global $db;
        
        if (!isset($_POST['product_line_hidden'])) {
            return ;
        }
        


        $q = 'DELETE FROM opportunities_product_line '
            . ' WHERE opportunity_id=\'' . $bean->id . '\'';
        $db->query($q);
        
        if (!$_POST['product_line_hidden']) {
            return ;
        }

        $data = explode('|', $_POST['product_line_hidden']);
        foreach($data as $v) {
            $v = explode(',', $v);

            $pline_id = null;
            if (isset($v[0])) { 
                $pline_id = htmlspecialchars($v[0]);
            }

            $pcat_id = null;
            if (isset($v[1])) {
                $pcat_id = htmlspecialchars($v[1]);
            }

            $pcode_id = null;
            if (isset($v[2])) {
                $pcode_id = htmlspecialchars($v[2]);
            }
            
            $q = '' 
                . 'INSERT INTO opportunities_product_line ('
                    . 'opportunity_id,' 
                    . 'pline_id,' 
                    . 'pcat_id,' 
                    . 'pcode_id' 
                . ') VALUES (' 
                    . '\'' . $bean->id . '\',' 
                    . '\'' . $pline_id . '\',' 
                    . '\'' . $pcat_id . '\',' 
                    . '\'' . $pcode_id . '\'' 
                . ')';
            $db->query($q);
        }

    }
    
    public function after_retrieve(&$bean, $event, $arguments) 
    {
        global $db;
        $q = 'SELECT * FROM opportunities_product_line'
            . ' WHERE opportunity_id=\'' . $bean->id . '\'';
        $rs = $db->query($q);

        $bean->product_line = array();
        while($data = $db->fetchByAssoc($rs)) {
            $bean->product_line[] = $data;
        }
    }
}