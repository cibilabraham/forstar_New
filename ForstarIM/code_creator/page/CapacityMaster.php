<?php
	require("ftemplate/include.php");

	require_once 'components/MyCapacity/MyCapacity_ajax.php';
	require_once("components/MyCapacity/MyCapacity_controller.php");
	$mc_c = new MyCapacity_controller(array_merge($p,$g), $xajax);
	
	if ($p["cmdAddNew"]!="") 	$mc_c->showAddView();
	else if ($p["cmdAdd"]!="" || $p["cmdSaveChange"]!="") 	$mc_c->save();
	else if ($p["editId"]!="")	$mc_c->edit();	
	else if ($p["cmdDelete"]!="")	$mc_c->deleteRecs();
	else if ($g["print"]!="")	$mc_c->printList();

	// Display page
	$mc_c->index();

?>