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


	###display content in pop up for allocation of quantity
	function getSupplierItemQuantity($stockId,$totalQty,$hidRowCount,$qty,$requisitionId,$row,$company,$unit)
	{
		$objResponse = new NxajaxResponse();
	    $databaseConnect = new DatabaseConnect();	
		$stockissuanceObj = new StockIssuance($databaseConnect);	
		//$objResponse->alert($company.','.$unit);
		$result=$stockissuanceObj->getSupplierItemDetail($stockId,$totalQty,$hidRowCount,$qty,$requisitionId,$row,$company,$unit);
		$objResponse->assign("dialog", "innerHTML", $result);
		return $objResponse;
	}

	###insert dta to stock issuance table
	function saveData($supplierStockId,$item,$supplierId,$supplierStockQty,$allotQuantity,$requisitionId,$company,$unit)
	{
		
		$objResponse = new NxajaxResponse();
	    $databaseConnect = new DatabaseConnect();	
		$stockissuanceObj = new StockIssuance($databaseConnect);
		$sessObj =	new Session($databaseConnect);
		$userId		=	$sessObj->getValue("userId");
		//$objResponse->alert("hii");
		$result=$stockissuanceObj->saveData($supplierStockId,$item,$supplierId,$supplierStockQty,$allotQuantity,$requisitionId,$userId,$company,$unit);
		if($result)
		{
			$supplierDetail=$stockissuanceObj->getSupplierData($supplierStockId,$item,$supplierId,$company,$unit);
			foreach($supplierDetail as $sd)
			{
				if($balanceQty=='')
				{
					$stockQtyId=$sd[0];
					$stockQty=$sd[1];
					$balanceQty=$allotQuantity-$stockQty;
					if($balanceQty>=0)
					{
						$updateStock=$stockissuanceObj->updateSupplierStock($stockQtyId,$qty);	
						continue;
					}
					else
					{	$newQty=$stockQty-$allotQuantity;
						$updateStock=$stockissuanceObj->updateSupplierStock($stockQtyId,$newQty);	
						break;
					}
				}
				else
				{
					$stockQtyId=$sd[0];
					$stockQty=$sd[1];
					$oldbalanceQty=$balanceQty;
					$balanceQty=$balanceQty-$stockQty;
					if($balanceQty>=0)
					{
						$updateStock=$stockissuanceObj->updateSupplierStock($stockQtyId,$qty);	
						continue;
					}
					else
					{	$newQty=$stockQty-$oldbalanceQty;
						$updateStock=$stockissuanceObj->updateSupplierStock($stockQtyId,$newQty);	
						break;
					}
				}

			}
		}
		$objResponse->setReturnValue($result);
		//$objResponse->assign("dialog", "innerHTML", $result);
		return $objResponse;
	}


	###display content in pop up for view
	function getSupplierItemQuantityShow($stockId,$totalQty,$hidRowCount,$qty,$requisitionId,$row)
	{
		
		$objResponse = new NxajaxResponse();
	    $databaseConnect = new DatabaseConnect();	
		$stockissuanceObj = new StockIssuance($databaseConnect);
		//$objResponse->alert("hii");
		$result=$stockissuanceObj->getStockIssuance($requisitionId,$stockId);
		$objResponse->assign("dialog", "innerHTML", $result);
		return $objResponse;
	}	

	function getfromPlant($companyId,$row,$cel)
	{
		$objResponse 	= new NxajaxResponse();	
		$databaseConnect= new DatabaseConnect();
		$manageusersObj			=	new ManageUsers($databaseConnect);
		$sessObj				=	new Session($databaseConnect);
		$userId		=	$sessObj->getValue("userId");
		//$objResponse->alert("jih");
		list($companyRecords,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);
		$unit=$unitRecords[$companyId];
		$unit = array('0' => '--Select--') + $unit;
		//$objResponse->alert($unit);
		$objResponse->addDropDownOptions("fromPlant",$unit,$cel);
		return $objResponse;	
	}
	
	function gettoPlant($companyId,$row,$cel)
	{
		$objResponse 	= new NxajaxResponse();	
		$databaseConnect= new DatabaseConnect();
		$manageusersObj			=	new ManageUsers($databaseConnect);
		$sessObj				=	new Session($databaseConnect);
		$userId		=	$sessObj->getValue("userId");
		list($companyRecords,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);
		$unit=$unitRecords[$companyId];
		$unit = array('0' => '--Select--') + $unit;
		$objResponse->addDropDownOptions("toPlant",$unit,$cel);
		return $objResponse;	
	}


	function getSupplierDetail($company,$unit,$item)
	{
		$objResponse 	 = new NxajaxResponse();	
		$databaseConnect = new DatabaseConnect();
		$stockObj        = new Stock($databaseConnect);
		$supplier		 = $stockObj->getAllSupplierInPo($company,$unit,$item);
		$objResponse->addDropDownOptions("supplier",$supplier,$cel);
		return $objResponse;	
	}


	function getStockQuantity($item,$company,$unit,$supplier)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();	
		$stockObj = new Stock($databaseConnect);
		list($companyunitId,$stockQty) = $stockObj->getTotalUnitStockQty($item,$company,$unit,$supplier);
		$company = $stockObj->getCompanyInGRN($item,$supplier);
		$objResponse->addDropDownOptions("toCompany",$company,$cel);
		$objResponse->assign("fromqty", "value", "$stockQty");
		$objResponse->assign("companyUnitId", "value", "$companyunitId");
		return $objResponse;

	}

	function getUnitInGRN($item,$supplier,$company)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();	
		$stockObj = new Stock($databaseConnect);
		$unit = $stockObj->getUnitInGRN($item,$supplier,$company);
		$objResponse->addDropDownOptions("toPlant",$unit,$cel);
		return $objResponse;
	}
	
	function getItem($company,$unit)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();	
		$stockObj = new Stock($databaseConnect);
		$item = $stockObj->getItems($company,$unit);
		$objResponse->addDropDownOptions("item",$item,$cel);
		return $objResponse;
	}



/*	# Get balance Qty
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

	
		//Check Unique number

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
$xajax->register(XAJAX_FUNCTION,'getStockqty', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));*/





$xajax->register(XAJAX_FUNCTION,'getItem', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getUnitInGRN', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getStockQuantity', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getSupplierDetail', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'getfromPlant', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'gettoPlant', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));


$xajax->register(XAJAX_FUNCTION,'getSupplierItemQuantityShow', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'saveData',array('mode' => "'synchronous'"));
$xajax->register(XAJAX_FUNCTION,'getSupplierItemQuantity', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>