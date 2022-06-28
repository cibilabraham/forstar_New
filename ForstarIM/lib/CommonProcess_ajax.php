<?php
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();	


	function chkLoginStatus()
	{
		$objResponse 	 = new xajaxResponse();		
		$databaseConnect = new DatabaseConnect();
		if ($_SESSION["userId"]=="") $objResponse->script("doLogout();");
		return $objResponse;
	}

//$xajax->registerFunction("chkLoginStatus");

//$xajax->register(XAJAX_FUNCTION, 'updatePkgInsEditingTime', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>