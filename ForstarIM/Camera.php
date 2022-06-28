<?php
	require("include/include.php");

	$localIP = false;


	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}	
	//----------------------------------------------------------	


	#Getting Clent IP Address
	//$clientIP	=	$_SERVER['REMOTE_ADDR'];
	
	$clientIP	=	getenv("REMOTE_ADDR");	

	$private_ip	= "/^192\.168\..*/";
	if (preg_match($private_ip, $clientIP)) {
			$localIP = true;
	}
	
	if ($localIP) {		
		header("location:http://192.168.1.99:85/001x2234.html"); // changed 81-85 on 25-07-08
	} else {		
		header("location:http://59.181.113.157:85/001x2234.html");		
	}	
?>
<html>
<head><TITLE></TITLE></head>
</html>