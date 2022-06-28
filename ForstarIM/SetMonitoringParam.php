<?php
	require("ftemplate/include.php");

	require_once 'components/SetMonitoringParam/SetMonitoringParam_ajax.php';
	require_once("components/SetMonitoringParam/SetMonitoringParam_controller.php");
	$SetMonitoringParam_c = new SetMonitoringParam_controller(array_merge($p,$g), $xajax);
	
	if ($p["cmdAddNew"]!="") 	$SetMonitoringParam_c->showAddView();
	else if ($p["cmdAdd"]!="" || $p["cmdSaveChange"]!="") 	$SetMonitoringParam_c->save();
	else if ($p["editId"]!="")	$SetMonitoringParam_c->edit();	
	else if ($p["cmdDelete"]!="")	$SetMonitoringParam_c->deleteRecs();
	else if ($g["print"]!="")	$SetMonitoringParam_c->printList();


	// Display page
	$SetMonitoringParam_c->index();

?>