<?php
//require_once("lib/databaseConnect.php");
//require_once('ProductMaster_class.php');
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

		function addOptions($sSelectId, $options, $cId)
		{
   			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$optionId	= $val[0];
					$optValue	= $val[1];	
					$this->script("addOption('".$cId."','".$sSelectId."','".$optionId."','".$optValue."');");
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

	function getSupplier($supplierId)
	{	$cel ="";
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$ingredientPhysicalStockObj	=new IngredientPhysicalStock($databaseConnect);
		$ingredientRec = $ingredientPhysicalStockObj->fetchSelectedSupplierIngRecords($supplierId);
		 $objResponse->addOptions("selIngredient", $ingredientRec, $cel );
		return $objResponse;
	}
	# get ing List
	function  getIngredientSupplierId($ingId,$supplierId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$ingredientPhysicalStockObj	=new IngredientPhysicalStock($databaseConnect);
	    $ingRateId = $ingredientPhysicalStockObj->getSupplierIngId($ingId,$supplierId);
		$ingQty= $ingredientPhysicalStockObj->getSupplierQty("",$supplierId,$ingId);
		$objResponse->assign("supplierIngId", "value", $ingRateId);	
		$objResponse->assign("expectedQuantity", "value", $ingQty);	
		return $objResponse;
	}
	
	function getIngredientPhysicalStock($bulkDate,$supplierId,$ingId)
	{
		$objResponse = new NxajaxResponse();
		$databaseConnect = new DatabaseConnect();
		$ingredientPhysicalStockObj	=new IngredientPhysicalStock($databaseConnect);
		$ingRecord=$ingredientPhysicalStockObj->getIngredientPhysicalStock($bulkDate,$supplierId,$ingId);
		$objResponse->setReturnValue($ingRecord);
		//$objResponse->alert("hii");
		return $objResponse;
	}

$xajax->register(XAJAX_FUNCTION, 'getIngredientPhysicalStock', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading','mode' => "'synchronous'"));
$xajax->register(XAJAX_FUNCTION, 'getSupplier', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));
$xajax->register(XAJAX_FUNCTION, 'getIngredientSupplierId', array('onResponseDelay'=>'showLoading','onComplete'=>'hideLoading'));

$xajax->ProcessRequest();
?>