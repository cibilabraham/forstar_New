<?php
require_once("lib/databaseConnect.php");
require_once("stockissuance_class.php");
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();	

	class NxajaxResponse extends xajaxResponse
	{
		function addCreateOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".$val."');");
	       			}
	     		}
  		}	
	}
	
	# Get balance Qty
	function getTotalQty($stockId,$inputId) 
	{
		$objResponse = new NxajaxResponse();
	    	$databaseConnect = new DatabaseConnect();	
		$stockissuanceObj = new StockIssuance($databaseConnect);
	    	$data = $stockissuanceObj->getTotalStockQty($stockId);
		$inputData = ( $data == "") ? 0 : number_format($data,0,"","");
		$objResponse->assign("exisitingQty_$inputId", "value", "$inputData");		
	  	return $objResponse;
	}

	/*
		Check Unique number
	*/
	function checkRequestNumberExist($reqNum, $existNum, $mode)
	{
		$objResponse = new NxajaxResponse();
	    	$databaseConnect = new DatabaseConnect();	
		$stockissuanceObj = new StockIssuance($databaseConnect);
		$chkUnique = $stockissuanceObj->checkUnique($reqNum,$existNum);
		if ($chkUnique) {
			$msg = "$reqNum is already in use. Please choose another one";
			$objResponse->assign("requestNumExistTxt", "innerHTML", "$msg");			
			$objResponse->script("disableStockIssuanceButtons($mode)");			
		} else {
			$objResponse->assign("requestNumExistTxt", "innerHTML", "");			
			$objResponse->script("enableStockIssuanceButtons($mode)");			
		}
		return $objResponse;
	}

	function getStockqty($item,$fromunit)
	{
$objResponse = new NxajaxResponse();
$databaseConnect = new DatabaseConnect();	
$stockissuanceObj = new StockIssuance($databaseConnect);
$data = $stockissuanceObj->getTotalUnitStockQty($item,$fromunit);
$objResponse->assign("fromqty", "value", "$data");
return $objResponse;

	}

//$xajax->registerFunction("getTotalQty");
$xajax->register(XAJAX_FUNCTION, 'getTotalQty', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->registerFunction("checkRequestNumberExist");
$xajax->register(XAJAX_FUNCTION,'getStockqty', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->ProcessRequest();
?>