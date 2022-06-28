<?php
//require_once("lib/databaseConnect.php");
//require_once("PHTCertificate_class.php");
require_once("libjs/xajax_core/xajax.inc.php");

$xajax = new xajax();	
$xajax->configure('statusMessages', true);
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
	
	function lotDetails($rmLotId,$selunitTransferId)
	{
		
		$objResponse 			= new xajaxResponse();		
		$databaseConnect 		= new DatabaseConnect();		
		$unitTransferObj 	= new UnitTransfer($databaseConnect);
		$soakingObj	= new Soaking($databaseConnect);
		$supplierRecs 			= $unitTransferObj->getSupplierDetail($rmLotId);
		
		$processingRecs	= $unitTransferObj->getProcessingStage($rmLotId);
		//$processingRecs	= $unitTransferObj->getProcessingName($processingStage);
		$availableRecs	= $soakingObj->getAvailableQuantity($rmLotId);
		//$objResponse->alert($rmLotId);
		
		if (sizeof($supplierRecs)>0) {
		$objResponse->assign("supplierDetails", "value", $supplierRecs);
		}
		
		
		if (sizeof($processingRecs)>0) {
		//$objResponse->assign("currentProcessingStage", "value", $processingRecs);
		addDropDownOptions("currentProcessingStage", $processingRecs, $selunitTransferId, $objResponse);
		}
		//addDropDownOptions("rmtestMethod", $methodRecs, $selrmTestNameId, $objResponse);
		if (sizeof($availableRecs)>0) {
		$objResponse->assign("availableQuantity", "value", $availableRecs);
		}
		
		return $objResponse;
	}
	
	
	
	
	
	$xajax->register(XAJAX_FUNCTION,'lotDetails', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	
	
	
	$xajax->ProcessRequest();
?>
	