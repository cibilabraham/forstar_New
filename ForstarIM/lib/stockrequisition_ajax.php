<?php
require_once("lib/databaseConnect.php");
require_once("stockrequisition_class.php");
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
		
		function addDropDownOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}
  		}
	}
	
	# Get balance Qty
	function getTotalQty($stockId,$inputId) 
	{
		$objResponse = new NxajaxResponse();
	    	$databaseConnect = new DatabaseConnect();	
		$stockRequisitionObj=new StockRequisition($databaseConnect);
	    	$data = $stockRequisitionObj->getTotalStockQty($stockId);
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
		$stockRequisitionObj=new StockRequisition($databaseConnect);
		$chkUnique = $stockRequisitionObj->checkUnique($reqNum,$existNum);
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

	function getCompany($item,$company,$cid )
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();	
		$stockRequisitionObj=new StockRequisition($databaseConnect);
		$data = $stockRequisitionObj->getCompanyUser($item);
		if (sizeof($data)>0) $objResponse->addDropDownOptions("company", $data, $cid );
		return $objResponse;
	}

	function getUnit($item,$company,$cid )
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();	
		$stockRequisitionObj=new StockRequisition($databaseConnect);
		$data = $stockRequisitionObj->getUnitUser($item,$company);
		if (sizeof($data)>0) $objResponse->addDropDownOptions("unit", $data, $cid );
		return $objResponse;
	}

	function getStockQty($item,$company,$unit)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();	
		$stockRequisitionObj=new StockRequisition($databaseConnect);
		$stockQty = $stockRequisitionObj->getTotalUnitStockQty($item,$company,$unit);
		$objResponse->assign("stockQty", "value", "$stockQty");
		return $objResponse;
	}

//$xajax->registerFunction("getTotalQty");

$xajax->register(XAJAX_FUNCTION, 'getStockQty', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getTotalQty', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getCompany', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->registerFunction("checkRequestNumberExist");
$xajax->register(XAJAX_FUNCTION,'getUnit', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->ProcessRequest();
?>