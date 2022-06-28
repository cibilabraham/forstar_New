<?
	require_once("lib/databaseConnect.php");
	require_once("lib/goodsreceipt_class.php");
	require_once("libjs/xajax_core/xajax.inc.php");
	
	$xajax = new xajax(); // create xajax ref 

	function verifyChellanNumber($newChellanNumber,$extChellanNumber,$fieldId,$mode) 
	{
		$objResponse = new xajaxResponse();
	    	$dbc = new DatabaseConnect();	
		$gr = new GoodsReceipt($dbc);
	    	$msg = $gr->checkChallanNumberExist($newChellanNumber,$extChellanNumber);
		if( $msg!='' ) 
		{
			$objResponse->assign("ce","value", "1");
			$objResponse->script("disableButtons($mode)");
		}
		else 
		{
			$objResponse->assign("ce","value", "");
			$objResponse->script("enableButtons($mode)");
		}
		$objResponse->assign("$fieldId", "innerHTML", "$msg");	
		return $objResponse;
	}
	
	function verifyGateEntryNumber($newGENumber,$extGENumber,$fieldId,$mode) 
	{
		$objResponse = new xajaxResponse();
	    $dbc = new DatabaseConnect();	
		$gr = new GoodsReceipt($dbc);
	    $msg = $gr->checkGateEntryNumberExist($newGENumber,$extGENumber);
		if( $msg!='' ) 
		{
			$objResponse->assign("ge","value", "1");
			$objResponse->script("disableButtons($mode)");
		}
		else
		{
			$objResponse->assign("ge","value", "");
			$objResponse->script("enableButtons($mode)");
		}
		$objResponse->assign("$fieldId", "innerHTML", "$msg");	
		return $objResponse;
	}

	function verifyStoreEntryNumber($newSENumber,$extSENumber,$fieldId,$mode) 
	{
		$objResponse = new xajaxResponse();
	    $dbc = new DatabaseConnect();	
		$gr = new GoodsReceipt($dbc);
	    $msg = $gr->checkStoreEntryNumberExist($newSENumber,$extSENumber);
		if( $msg!='' ) 
		{
			$objResponse->assign("se","value", "1");
			$objResponse->script("disableButtons($mode)");
		}
		else 
		{
			$objResponse->assign("se","value", "");
			$objResponse->script("enableButtons($mode)");
		}
		$objResponse->assign("$fieldId", "innerHTML", "$msg");	
		return $objResponse;
	}

	function verifyRMGatePassNumber($newRMNumber,$extRMNumber,$fieldId,$mode) 
	{
		$objResponse = new xajaxResponse();
	    $dbc = new DatabaseConnect();	
		$gr = new GoodsReceipt($dbc);
	    $msg = $gr->checkRMGatePassNumberExist($newRMNumber,$extRMNumber);
		if( $msg!='' ) 
		{
			$objResponse->assign("rm","value", "1");
			$objResponse->script("disableButtons($mode)");
		}
		else 
		{
			$objResponse->assign("rm","value", "");
			$objResponse->script("enableButtons($mode)");
		}
		$objResponse->assign("$fieldId", "innerHTML", "$msg");	
		return $objResponse;
	}

	$xajax->registerFunction("verifyChellanNumber");
	$xajax->registerFunction("verifyGateEntryNumber");
	$xajax->registerFunction("verifyStoreEntryNumber");
	$xajax->registerFunction("verifyRMGatePassNumber");
	$xajax->processRequest(); // xajax end
?>