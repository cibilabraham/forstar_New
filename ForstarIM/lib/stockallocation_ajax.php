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



/*	function getSupplierItemQuantity($stockId,$totalQty,$hidRowCount,$qty,$requisitionId,$row)
	{
		$objResponse = new NxajaxResponse();
	    $databaseConnect = new DatabaseConnect();	
		 $stockAllocationObj	=new StockAllocation($databaseConnect);
		//$objResponse->alert("hii");
		$result=$stockAllocationObj->getSupplierItemDetail($stockId,$totalQty,$hidRowCount,$qty,$requisitionId,$row);
		$objResponse->assign("dialog", "innerHTML", $result);
		return $objResponse;
	}

	function saveData($supplierStockId,$item,$supplierId,$supplierStockQty,$allotQuantity,$requisitionId)
	{
		
		$objResponse = new NxajaxResponse();
	    $databaseConnect = new DatabaseConnect();	
		 $stockAllocationObj	=new StockAllocation($databaseConnect);
		$sessObj =	new Session($databaseConnect);
		$userId		=	$sessObj->getValue("userId");
		//$objResponse->alert("hii");
		$result=$stockAllocationObj->saveData($supplierStockId,$item,$supplierId,$supplierStockQty,$allotQuantity,$requisitionId,$userId);
		if($result)
		{
			$supplierDetail=$stockAllocationObj->getSupplierData($supplierStockId,$item,$supplierId);
			foreach($supplierDetail as $sd)
			{
				if($balanceQty=='')
				{
					$stockQtyId=$sd[0];
					$stockQty=$sd[1];
					$balanceQty=$allotQuantity-$stockQty;
					if($balanceQty>=0)
					{
						$updateStock=$stockAllocationObj->updateSupplierStock($stockQtyId,$qty);	
						continue;
					}
					else
					{	$newQty=$stockQty-$allotQuantity;
						$updateStock=$stockAllocationObj->updateSupplierStock($stockQtyId,$newQty);	
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
						$updateStock=$stockAllocationObj->updateSupplierStock($stockQtyId,$qty);	
						continue;
					}
					else
					{	$newQty=$stockQty-$oldbalanceQty;
						$updateStock=$stockAllocationObj->updateSupplierStock($stockQtyId,$newQty);	
						break;
					}
				}

			}
		}
		$objResponse->setReturnValue($result);
		//$objResponse->assign("dialog", "innerHTML", $result);
		return $objResponse;
	}


	function getSupplierItemQuantityShow($stockId,$totalQty,$hidRowCount,$qty,$requisitionId,$row)
	{
		
		$objResponse = new NxajaxResponse();
	    $databaseConnect = new DatabaseConnect();	
		$stockAllocationObj = new StockAllocation($databaseConnect);
		//$objResponse->alert("hii");
		$result=$stockAllocationObj->getStockIssuance($requisitionId,$stockId);
		$objResponse->assign("dialog", "innerHTML", $result);
		return $objResponse;
	}	

	


$xajax->register(XAJAX_FUNCTION,'getSupplierItemQuantityShow', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'saveData',array('mode' => "'synchronous'"));
$xajax->register(XAJAX_FUNCTION,'getSupplierItemQuantity', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
*/
$xajax->ProcessRequest();
?>