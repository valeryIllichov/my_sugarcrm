<?php 
include("modules/ZuckerReports/MenuExt.php");


$import_box = Array(Array("javascript:OpportunityQIPopup();", "MultiCreate Opportunities", "CreateOpportunities"));
array_splice($module_menu, 1, 0, $import_box);
include_once 'custom/modules/Opportunities/javascript/shortcutjs.php';


?>