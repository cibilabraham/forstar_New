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
		$supplierRecs 			= $unitTransferObj->getSupplierDetail($rmLotId);
		$unitRecs	= $unitTransferObj->getUnit($rmLotId);
		//$unitRecs	= $unitTransferObj->getUnitName($unit);
		$processingRecs	= $unitTransferObj->getProcessingStage($rmLotId);
		//$processingRecs	= $unitTransferObj->getProcessingName($processingStage);
		$firstLotIdRecs=$unitTransferObj->getFirstLot($rmLotId);
		//$objResponse->alert($firstLotIdRecs);
		
		if (sizeof($supplierRecs)>0) {
		$objResponse->assign("supplierDetails", "value", $supplierRecs);
		}
		
		if (sizeof($unitRecs)>0) {
		//$objResponse->assign("currentUnitName", "value", $unitRecs);
		addDropDownOptions("currentUnitName", $unitRecs, $selunitTransferId, $objResponse);
		}
		if (sizeof($processingRecs)>0) {
		//$objResponse->assign("currentProcessingStage", "value", $processingRecs);
		addDropDownOptions("currentProcessingStage", $processingRecs, $selunitTransferId, $objResponse);
		}
		//addDropDownOptions("rmtestMethod", $methodRecs, $selrmTestNameId, $objResponse);
		if (sizeof($firstLotIdRecs)>0) {
		$objResponse->assign("firstLotId", "value", $firstLotIdRecs);
		}
		return $objResponse;
	}
	
	/*function getLotId($selDate,$processType)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($selDate);
		$databaseConnect 		= new DatabaseConnect();
		$rmReceiptGatePassObj 	= new RMReceiptGatePass($databaseConnect);
		
		$checkLotSettingsExist=$rmReceiptGatePassObj->chkValidLotId($selDate,$processType);
		if ($checkLotSettingsExist){
		$alphaCode=$rmReceiptGatePassObj->getAlphaCode($processType);
		$alphaCodePrefix= $alphaCode[0];
		$checkExist=$rmReceiptGatePassObj->checkLotIdDisplayExist($processType);
		if ($checkExist>0){
		$getFirstRecord=$rmReceiptGatePassObj->getmaxLotId($processType);
		$getFirstRec= $getFirstRecord[0];
		//$objResponse->alert($getFirstRec);
		$getFirstRecEx=explode($alphaCodePrefix,$getFirstRec);
		//$objResponse->alert($getFirstRecEx[1]);
		$nextLotId=$getFirstRecEx[1]+1;
		$validendno=$rmReceiptGatePassObj->getValidendnoLotId($selDate,$processType);	
		if ($nextLotId>$validendno){
		$LotIdMsg="Please set the Lot Id in Settings,since it reached the end no";
		$objResponse->assign("divlotIdExistTxt","innerHTML",$LotIdMsg);
		}
		else{
		
		$disLotIdNo="$alphaCodePrefix$nextLotId";
		$objResponse->assign("lotId","value","$disLotIdNo");	
		}
		
		}
		else{
		
		$validLotIdNo=$rmReceiptGatePassObj->getValidLotId($selDate,$processType);	
		$checkLotId=$rmReceiptGatePassObj->chkValidLotId($selDate,$processType);
		$dislotId="$alphaCodePrefix$validLotIdNo";
		$objResponse->assign("lotId","value","$dislotId");	
		}
		
		}
		else{
		//$objResponse->alert("hi");
		$LotIdMsg="Please set the Lot Id in Settings";
		$objResponse->assign("divlotIdExistTxt","innerHTML",$LotIdMsg);
		}
	
		return $objResponse;
	}*/
	
	
	function getLotId($selDate,$process)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($selDate);
		$databaseConnect 		= new DatabaseConnect();
		$rmReceiptGatePassObj 	= new RMReceiptGatePass($databaseConnect);
		$unitTransferObj= new UnitTransfer($databaseConnect);
		//$objResponse->alert($processType);
		$getProcess=$unitTransferObj->getProcessingStages($process);
		//$objResponse->alert($getProcess);
		// if($getProcess[0]=='Fresh')
		// {
			// $processType='LF';
		// }
		if($getProcess[0]=='Unit')
		{
			$processType='LU';
		}
		if($getProcess[0]=='Cold Storage')
		{
			$processType='LC';
		}
		if($getProcess[0]=='Thawing')
		{
			$processType='LT';
		}
		//$objResponse->alert($processType);
		$checkLotSettingsExist=$unitTransferObj->chkValidLotId($selDate,$processType);
		if ($checkLotSettingsExist){
		$alphaCode=$unitTransferObj->getAlphaCode($processType);
		$alphaCodePrefix= $alphaCode[0];
		//$objResponse->alert($alphaCodePrefix);
		$checkExist=$unitTransferObj->checkLotIdDisplayExist($process);
		if ($checkExist>0){
		$getFirstRecord=$unitTransferObj->getmaxLotId($process);
		$getFirstRec= $getFirstRecord[0];
		//$objResponse->alert($getFirstRec);
		$getFirstRecEx=explode($alphaCodePrefix,$getFirstRec);
		//$objResponse->alert($getFirstRecEx[1]);
		$nextLotId=$getFirstRecEx[1]+1;
		$validendno=$unitTransferObj->getValidendnoLotId($selDate,$processType);	
		if ($nextLotId>$validendno){
		$LotIdMsg="Please set the Lot Id in Settings,since it reached the end no";
		$objResponse->assign("divlotIdExistTxt","innerHTML",$LotIdMsg);
		}
		else{
		
		$disLotIdNo="$alphaCodePrefix$nextLotId";
		$objResponse->assign("lotId","value","$disLotIdNo");	
		}
		
		}
		else{
		
		$validLotIdNo=$unitTransferObj->getValidLotId($selDate,$processType);	
		$checkLotId=$unitTransferObj->chkValidLotId($selDate,$processType);
		$dislotId="$alphaCodePrefix$validLotIdNo";
		$objResponse->assign("lotId","value","$dislotId");	
		}
		
		}
		else{
		//$objResponse->alert("hi");
		$LotIdMsg="Please set the Lot Id in Settings";
		$objResponse->assign("divlotIdExistTxt","innerHTML",$LotIdMsg);
		}
	
		return $objResponse;
	}
	
	
	$xajax->register(XAJAX_FUNCTION,'lotDetails', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getLotId', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	
	
	$xajax->ProcessRequest();
?>
	