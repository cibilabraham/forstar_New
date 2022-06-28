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
					$this->script("addOption('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
	       			}
	     		}
  		}
	}

	###get all item for company and unit
	function getStock($company,$unit,$cid)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$physicalStockInventoryObj =new PhysicalStockInventory($databaseConnect);
		$stockRec = $physicalStockInventoryObj->getStock($company,$unit);		
		if (sizeof($stockRec)>0) $objResponse->addDropDownOptions("itemId_0",$stockRec,$cid);	
		return $objResponse;
	}

	###get supplier for company,unit and item
	function getSupplier($company,$unit,$item,$row)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$physicalStockInventoryObj =new PhysicalStockInventory($databaseConnect);
		$supplierStockRec = $physicalStockInventoryObj->getSupplier($company,$unit,$item);		
		if (sizeof($supplierStockRec)>0) $objResponse->addDropDownOptions("supplierId_$row", $supplierStockRec, $cid );	
		return $objResponse;
	}


	###get all stock for a supplier
	function getSupplierStock($supplier,$cid)
	{
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$physicalStockInventoryObj =new PhysicalStockInventory($databaseConnect);
		$supplierStockRec = $physicalStockInventoryObj->getSupplierStock($supplier);		
		if (sizeof($supplierStockRec)>0) $objResponse->addDropDownOptions("item", $supplierStockRec, $cid );	
		return $objResponse;
	}

	###get supplier stockId and company for supplier stock
	function getSupplierStockId($supplierId,$itemId,$row,$companyId,$unitId)
	{
		
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$physicalStockInventoryObj =new PhysicalStockInventory($databaseConnect);
		$supplierStockRec = $physicalStockInventoryObj->getSupplierStockId($supplierId,$itemId);
		$companyUnitId = $physicalStockInventoryObj->getCompanyUnitId($supplierId,$itemId,$supplierStockRec,$companyId,$unitId);
		$objResponse->assign("supplierStockId_$row","value","$supplierStockRec");	
		$objResponse->assign("companyUnitId_$row","value","$companyUnitId");	
		return $objResponse;
	}

	function getUnit($companyId,$row,$cel)
	{
		$objResponse 	= new NxajaxResponse();	
		$databaseConnect= new DatabaseConnect();
		$manageusersObj			=	new ManageUsers($databaseConnect);
		$sessObj				=	new Session($databaseConnect);
		$userId		=	$sessObj->getValue("userId");
		list($companyRecords,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);
		$unit=$unitRecords[$companyId];
		$unit = array('0' => '--Select--') + $unit;
		$objResponse->addDropDownOptions("unit",$unit,$cel);
		return $objResponse;	
	}


	

	/*function getUnit($supplier,$stockId,$supplierStockId,$company,$row)
	{	
		$cel="";
		$objResponse 		= new NxajaxResponse();
		$databaseConnect 	= new DatabaseConnect();
		$physicalStockInventoryObj =new PhysicalStockInventory($databaseConnect);
		//$objResponse->alert("hiii");
		$unitRecs=$physicalStockInventoryObj->getUnit($supplier,$stockId,$supplierStockId,$company);
		if (sizeof($unitRecs)>0) $objResponse->addDropDownOptions("punitId_$row", $unitRecs, $cel );	
		return $objResponse;

	} 
*/

$xajax->register(XAJAX_FUNCTION, 'getStock', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));	
$xajax->register(XAJAX_FUNCTION, 'getSupplier', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));	
$xajax->register(XAJAX_FUNCTION, 'getSupplierStockId', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getUnit', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));



//$xajax->register(XAJAX_FUNCTION, 'getUnit', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));	
$xajax->register(XAJAX_FUNCTION, 'getSupplierStock', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));



$xajax->ProcessRequest();
?>