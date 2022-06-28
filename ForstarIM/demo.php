<?php
	require("include/include.php");
	require_once("lib/ChangesUpdateMaster_ajax.php");
	ob_start();

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------	

	require("template/topLeftNav.php");

	//$dashBoardStock = $dashboardManagerObj->getReorderLevelStock();
?>

content here 


<?php

	require("template/bottomRightNav.php");
	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>