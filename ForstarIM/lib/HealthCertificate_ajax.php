<?php
require_once("lib/databaseConnect.php");
require_once("HealthCertificate_class.php");
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();	

	class NxajaxResponse extends xajaxResponse
	{
		function addCreateOptions($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addDropDownList('".$cId."','".$sSelectId."','".$option."','".$val."');");
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
	
	# Stock Unit Rate
	function getStockUnitRate($distributorId, $stockId, $rowId, $stateId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		//$productPriceRateListObj	= new ProductPriceRateList($databaseConnect);
		$manageRateListObj	= new ManageRateList($databaseConnect);
		$salesOrderObj		= new SalesOrder($databaseConnect);
		$distMarginRateListObj	= new DistributorMarginRateList($databaseConnect);
		
		/* Product Price Rate List */
		$productPriceRateListId = $manageRateListObj->latestRateList("PMRP");
		$mrp = $salesOrderObj->findProductPrice($distributorId, $stockId, $productPriceRateListId, $stateId);
		$distMarginRateListId	= $distMarginRateListObj->latestRateList($distributorId);
		$distAvgMargin		= $salesOrderObj->getDistAverageMargin($distributorId, $stockId, $stateId, $distMarginRateListId);
		 //Cost_To_dist= MRP - (MRP  *AVG MARGIN)/100;
				
		$calcCostToDist	=  $mrp - (($mrp*$distAvgMargin)/100);	
		if ($distAvgMargin>0) $costToDist = number_format($calcCostToDist,2,'.','');
		else 	$costToDist = 0;
		//$objResponse->alert("Supp=$distributorId;Stock=$stockId;row=$rowId;state=$stateId,unit=$unitRate");
		$objResponse->assign("unitPrice_".$rowId, "value", $costToDist);
		$objResponse->assign("hidSelStock_".$rowId, "value", $stockId);	 // Assign hid stock value
		//$objResponse->script("getProductPrice();");	
		$objResponse->script("multiplySalesOrderItem();");	
		sleep(1);		
            	return $objResponse;
	}
	

//$xajax->registerFunction("getDistributorStockRecords");
$xajax->registerFunction("getStockUnitRate");

//$xajax->register(XAJAX_FUNCTION, 'getStockUnitRate', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));


$xajax->ProcessRequest();
?>