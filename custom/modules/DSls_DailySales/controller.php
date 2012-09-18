<?php
/* 
 * custom controller for post request
 * Memet
 * ItCrimea 2011
 * memet@itcrimea.com
 */

class DSls_DailySalesController extends SugarController {
    public function action_ProcessTable()
    {
        $this->view = 'tableCreate';
    }
}
?>