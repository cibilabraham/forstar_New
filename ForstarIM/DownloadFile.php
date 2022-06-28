<?php
	require("include/include.php");
	require("lib/MySQLDump_class.php");
	
	$path		= base64_decode($_GET["file"]);	
	$extension  = (isset($_GET["ext"]))?".".trim($_GET["ext"]):".sql";	

	$fileManageObj->downloadFile("",$extension,$path);
?>