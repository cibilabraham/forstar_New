<?php
	require_once("lib/databaseConnect.php");
	require_once("lib/StockReturn_class.php");
	require_once("libjs/xajax_core/xajax.inc.php");
	
	$xajax = new xajax(); // create xajax ref 

	function validateReturnNumber($retNumber,$extRetNum,$inputId,$mode) 
	{
		$objResponse = new xajaxResponse();
	    	$dbc = new DatabaseConnect();	
		$si = new StockReturn($dbc);
	    	$msg = $si->checkUnique($retNumber,$extRetNum);
		if ($msg !="" ) {
			$objResponse->assign("requestNumExist", "value", "1");
			$objResponse->script("chkStockQtyExist()");			
		} else {
			$objResponse->assign("requestNumExist", "value", "");
			$objResponse->script("chkStockQtyExist()");				
		}
		$objResponse->assign("$inputId", "innerHTML", "$msg");
	  	return $objResponse;
	}

	function checkStockIssued($departmentId, $stockId, $returnQty, $mode, $rowId)
	{
		$objResponse = new xajaxResponse();
	    	$dbc = new DatabaseConnect();	
		$stockReturnObj = new StockReturn($dbc);
		$issuedQty = $stockReturnObj->getIssuedQty($departmentId, $stockId);
		$returnedQty = $stockReturnObj->getStockReturnedQty($departmentId, $stockId);
		//$objResponse->alert($issuedQty.'---'.$returnedQty);
		$totalQty = $issuedQty-$returnedQty;
		if ($returnQty>$totalQty) {			
			$msg = "This stock returned qty is greater than the issued qty ";
			$objResponse->assign("returnQtyExist_".$rowId, "value", "N");
			$objResponse->script("chkStockQtyExist()");	
		} else {
			$msg = "";
			$objResponse->assign("returnQtyExist_".$rowId, "value", "Y");	
			$objResponse->script("chkStockQtyExist()");
		}

		//$objResponse->alert("de=$departmentId,stock=$stockId, reQty=$returnQty, iss=$issuedQty");
		$objResponse->assign("returnErrMsg_".$rowId, "innerHTML", "$msg");
		return $objResponse;		
	}

		
	$xajax->registerFunction("validateReturnNumber");
	$xajax->registerFunction("checkStockIssued");
	
	$xajax->processRequest(); // xajax end
?>