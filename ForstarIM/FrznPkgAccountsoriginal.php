<?php
	require("ftemplate/include.php");

	require_once 'components/FrznPkgAccounts/FrznPkgAccounts_ajax.php';
	require_once("components/FrznPkgAccounts/FrznPkgAccounts_controller.php");
	$FrznPkgAccounts_c = new FrznPkgAccounts_controller(array_merge($p,$g), $xajax);
	
	if ($p["cmdAddNew"]!="") 	$FrznPkgAccounts_c->showAddView();
	else if ($p["cmdAdd"]!="" || $p["cmdSaveChange"]!="" || $p["cmdSave"]) 	$FrznPkgAccounts_c->save();
	else if ($p["editId"]!="")	$FrznPkgAccounts_c->edit();	
	else if ($p["cmdDelete"]!="")	$FrznPkgAccounts_c->deleteRecs();
	else if ($g["print"]!="")	$FrznPkgAccounts_c->printList();

	// Display page
	$FrznPkgAccounts_c->index();

?>