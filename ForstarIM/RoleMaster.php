<?php
	require("ftemplate/include.php");

	require_once 'components/role_master/role_master_ajax.php';
	require_once("components/role_master/role_master_controller.php");
	$rmc = new role_master_controller(array_merge($p,$g), $xajax);
	

	if ($p["cmdAddNew"]!="") 	$rmc->showAddView();
	else if ($p["cmdAdd"]!="" || $p["cmdSaveChange"]!="") 	$rmc->save();
	else if ($p["editId"]!="")	$rmc->edit();	
	else if ($p["cmdDelete"]!="")	$rmc->deleteRecs();
	else if ($g["print"]!="")	$rmc->printList();

	// Display page
	$rmc->index();
?>