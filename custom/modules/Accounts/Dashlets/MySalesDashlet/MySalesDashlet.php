<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('include/Dashlets/Dashlet.php');
require_once('modules/Accounts/Account.php');
require_once('custom/modules/Accounts/Dashlets/MySalesDashlet/MySalesDashlet.php');
			  

class MySalesDashlet extends Dashlet { 
	var $mtdSales = 0;
	var $mtdProjectedSales = 0;
	var $mtdBudget = 0;
 	var $workdays;	
	var $remainingWorkdays;
	var $dailyTarget;
	var $dailyAvgTarget;
	var $currentMonthBudget;
	var $yesterdaySales = 0;
	var $dayRemainBudget;

function MySalesDashlet($id, $def = null) {
        global $current_user, $app_strings;
        parent::Dashlet($id);
        $this->isConfigurable = false; 
        $this->isRefreshable = true;        
        if(empty($def['title'])) $this->title = 'Sales Goals';
        
        $this->seedBean = new Account();      

      	$qry = "select count(*) as workdays from dsls_calendar where yr = 2011 and mo = month(current_date) and workday = -1";
            $result = $this->seedBean->db->query($qry);
            $row = $this->seedBean->db->fetchByAssoc($result);
            $this->workdays = $row['workdays'];

	    $qry = "select count(*) as remainingWorkdays from dsls_calendar where yr = 2011 and mo = month(current_date) and da >= day(current_date) and workday = -1";
            $result = $this->seedBean->db->query($qry);
            $row = $this->seedBean->db->fetchByAssoc($result);
            $this->remainingWorkdays = $row['remainingWorkdays'];
	
	     
		$qry = "select empid from users where id = '" . $current_user->id . "' AND deleted=0"; 
			$result = $this->seedBean->db->query($qry);
			$row = $this->seedBean->db->fetchByAssoc($result);
            $this->empid = $row['empid'];
		
		$qry = "select slsm, lastname, firstname, class, mgr_group from dsls_slsm where empid = '" . $this->empid . "' AND deleted=0 order by class asc"; 
			
			$result = $this->seedBean->db->query($qry);
			$this->rollup = array();
			while($row =$this->seedBean->db->fetchByAssoc($result)){
				$this->rollup[] = $row['slsm'];
			}
			$this->group = array();
			foreach($this->rollup as $s){		
				$this->group = $this->getSlsmInfo($s);
					foreach($this->group as $t){
						if(!in_array($t, $this->rollup)){
							$this->rollup[] = $t;
						}
						
					}
			}	
			    foreach($this->rollup as $r){
						
						$qry1 = "select round(sum(sales)) as Sales from dsls_previous_day where slsm = '". $r . "'";
							$rss = $this->seedBean->db->query($qry1);
							$ro = $this->seedBean->db->fetchByAssoc($rss);
							$this->yesterdaySales = $this->yesterdaySales + $ro['Sales'];
						$qry2 = "SELECT round(sum(mtd_sales_c)) as mtd_sales, round(sum(mtd_budget_sales_c)) as mtd_budget, round(sum(mtd_projected_c)) as mtd_projected from accounts  where slsm_c = '". $r .  "' AND deleted=0";
							$rs1 = $this->seedBean->db->query($qry2);
							$r1 = $this->seedBean->db->fetchByAssoc($rs1);
							$this->mtdSales = $this->mtdSales + $r1['mtd_sales'];
								
							$this->mtdBudget = $this->mtdBudget + $r1['mtd_budget'];
							$this->mtdProjectedSales = $this->mtdProjectedSales + $r1['mtd_projected'];
				}
				
	$this->currentMonthBudget = $this->mtdBudget;
	$this->dailyAvgTarget = $this->mtdBudget/$this->workdays;

	$this->dayRemainBudget = (($this->mtdBudget-$this->mtdSales)/$this->remainingWorkdays);
	$this->projected_sales = ($this->projected_sales/$this->workdays);
	
		 
	}
	 function getSlsmInfo(&$slsm){
						
						$rtrnRollup = array();
						
						$qry3 = "select slsm from dsls_slsm where mgr_group = '" . $slsm . "' AND deleted=0";
						$rs1 = $this->seedBean->db->query($qry3);
						while($r1 = $this->seedBean->db->fetchByAssoc($rs1)){	
							if(!in_array($r1['slsm'], $rtrnRollup)){
								$rtrnRollup[] = $r1['slsm'];
							}
						}
						if(!empty($rtrnRollup)){
							foreach($rtrnRollup	as $r){								
									$this->getSlsmInfo($r);
									}
							}
						if(!in_array($slsm, $this->rollup)){
							$this->rollup[] = $slsm;
						} 
						return $rtrnRollup;
					}	
  
	function display(){
	require_once('include/Sugar_Smarty.php');
	$ss = new Sugar_Smarty();
    $ss->assign('lblMonthlySales', 'Current Monthly Sales');
    $ss->assign('lblMonthProjSales','Current Month Projection');   	
	$ss->assign('lblDailyBudgetSales', 'Daily Target');
  	$ss->assign('lblyesterdaySales', 'Previous Day\'s Invoiced Sales' );
	$ss->assign('lblAvgDayTarget', 'Daily Average Target');
	$ss->assign('lblBudgetSales', 'Current Month Budget');
	
	  setlocale(LC_MONETARY, 'en_US.UTF-8');
  	$ss->assign('mtdSales',money_format('%.0n',$this->mtdSales));
   	$ss->assign('yesterdaySales', money_format('%.0n',$this->yesterdaySales));
	$ss->assign('mtdProjectedSales',money_format('%.0n',$this->mtdProjectedSales));
	//never want the daily target to be negative
	if($this->dayRemainBudget > $this->dailyAvgTarget) $ss->assign('dayRemainBudget', money_format('%.0n',$this->dayRemainBudget));
	  else $ss->assign('dayRemainBudget', money_format('%.0n', $this->dailyAvgTarget));    	
    
	$ss->assign('mtdBudget',money_format('%.0n',$this->mtdBudget));	
    $ss->assign('dailyAvgTarget',money_format('%.0n',$this->dailyAvgTarget));	
		
		return parent::display() . $ss->fetch('custom/modules/Accounts/Dashlets/MySalesDashlet/MySalesDashlet.tpl');

    }
}
?>
