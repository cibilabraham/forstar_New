<?php
	error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
	require("ftemplate/include.php");

	require_once 'components/CommonReason/CommonReason_ajax.php';
	require_once("components/CommonReason/CommonReason_controller.php");
	$comReason_c = new CommonReason_controller(array_merge($p,$g), $xajax, $codArr);
	
	if ($p["cmdAddNew"]!="") 	$comReason_c->showAddView();
	else if ($p["cmdAdd"]!="" || $p["cmdSaveChange"]!="") 	$comReason_c->save();
	else if ($p["editId"]!="")	$comReason_c->edit();	
	else if ($p["cmdDelete"]!="")	$comReason_c->deleteRecs();
	else if ($g["print"]!="")	$comReason_c->printList();
	else if ($p["confirmId"]!="")	$comReason_c->confirm();
	else if ($p["rlconfirmId"]!="")	$comReason_c->Releaseconfirm();
	else if ($p["confirmId"]!="")	$comReason_c->confirm();
	else if ($p["rlconfirmId"]!="")	$comReason_c->Releaseconfirm();
	
	// Display page
	$comReason_c->index();

?>