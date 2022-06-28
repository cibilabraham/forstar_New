<?php
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
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".$val."');");
	       			}
	     		}
  		}
	}
		
	
	# Get Transporter Rate List  
	function getSupplierRec($supplierId, $stockId, $selRateListId, $mode, $currentId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$supplierstockObj		=	new SupplierStock($databaseConnect);
		$supplierRateListObj		= 	new SupplierRateList($databaseConnect);

		# Rate List Id
		$rateListId = $supplierRateListObj->latestRateList($supplierId,$stockId);
		//$objResponse->alert("$supplierId, $stockId, $selRateListId, $mode, $currentId");
		# Check Rec Exist
		$chkRecExist 	= $supplierstockObj->checkEntryExist($supplierId, $stockId, $rateListId, $currentId);

		/*if ($chkRecExist) {
			$objResponse->assign("divRecExistTxt", "innerHTML", "Please make sure the selected record is not existing.");
			$objResponse->script("disableSupplierStockButton($mode);");*/
		//} else  {
			$objResponse->assign("divRecExistTxt", "innerHTML", "");
			$objResponse->script("enableSupplierStockButton($mode);");
		//}

		$objResponse->assign("supplierRateList", "value", $rateListId);
		return $objResponse;
	}

	function getCompanyUnit($stockId)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$supplierstockObj		=	new SupplierStock($databaseConnect);
		$stockObj		=	new Stock($databaseConnect);
		$stockItemUnitObj		=	new StockItemUnit($databaseConnect);
		//$objResponse->alert("hiii");
		$companyRecs=$supplierstockObj->getcompany($stockId);
		$objResponse->addDropDownOptions("companyId_0",$companyRecs,$cel);
		$objResponse->assign("punitId_0", "value", "");
		//$objResponse->script("enableSubmit();");
		return $objResponse;

	}

	function getUnit($stockId,$company,$row)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$supplierstockObj		=	new SupplierStock($databaseConnect);
		//$objResponse->alert("hiii");
		$unitRecs=$supplierstockObj->getUnit($stockId,$company);
		$objResponse->addDropDownOptions("punitId_$row",$unitRecs,$cel);
		 return $objResponse;

	} 

	
$xajax->register(XAJAX_FUNCTION, 'getUnit', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));	
$xajax->register(XAJAX_FUNCTION, 'getCompanyUnit', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));	
$xajax->register(XAJAX_FUNCTION, 'getSupplierRec', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));	

$xajax->ProcessRequest();
?>