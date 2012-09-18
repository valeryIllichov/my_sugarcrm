<?php

if (!isset($_GET['fmpfix']) || !$_GET['fmpfix']) {
    require_once 'modules/ZuckerQueryTemplate/fmp.config.php';
    header('Location: index.php?module=ZuckerReports&action=ReportOnDemand&record=' . $FMPCO_REP_ID__SSAR );
    exit();
}


	require_once("modules/ZuckerReports/config.php");

	if (!empty($zuckerreports_config["index_include"])) {
		include($zuckerreports_config["index_include"]);
	} else {
		include("modules/ZuckerReportContainer/DetailView.php");
	}
?>
