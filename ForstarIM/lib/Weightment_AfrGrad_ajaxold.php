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
	
	function suplierDetail($rmLotId, $selWeightAfterGradeId,$gradeTypeLenghth)
	{
		
		$objResponse 			= new xajaxResponse();		
		$databaseConnect 		= new DatabaseConnect();
		//$objResponse->alert($rmLotId);
		//$objResponse->alert($gradeTypeLenghth);
		
		$weightmentAfterGradingObj 	= new WeightmentAfterGrading($databaseConnect);		
		$supplierRecs 			= $weightmentAfterGradingObj->getSupplierDetail($rmLotId);
		//$pondDetails = 'Farm at harvest : '.$supplierRecs[1].' Product Specious : '.$supplierRecs[2].' Total Quantity : '.$supplierRecs[3];
		$pondDetails = 'Farm at harvest : '.$supplierRecs[1].' Total Quantity : '.$supplierRecs[2];
		$LotId=$weightmentAfterGradingObj->getGradeId($rmLotId);
		//$objResponse->alert($LotId[0]);
		$gradeRecs	= $weightmentAfterGradingObj->getGrade($LotId[0]);
		//$objResponse->alert($gradeId);
		$weightRecs	= $weightmentAfterGradingObj->getWeight($rmLotId);
		$weig=$weightRecs[1];
		//$unitRecs	= $unitTransferObj->getUnitName($unit);
		//$processingRecs	= $unitTransferObj->getProcessingStage($rmLotId);
		//$processingRecs	= $unitTransferObj->getProcessingName($processingStage);
		
		//$objResponse->alert($rmLotId);
		
		if (sizeof($pondDetails)>0) {
		$objResponse->assign("supplyDetails", "value", $pondDetails);
		}
		
			if (sizeof($gradeRecs)>0) {
			for($i=1; $i<=$gradeTypeLenghth; $i++)
			{
			
			//$objResponse->alert("gradeType".$i);
			addDropDownOptions("gradeType".$i, $gradeRecs, $selWeightAfterGradeId, $objResponse);
			}
		}
		
		if (sizeof($weig)>0) {
		$objResponse->assign("totalwt", "value", $weig);
		}
		//if (sizeof($processingRecs)>0) {
		//$objResponse->assign("currentProcessingStage", "value", $processingRecs);
		//addDropDownOptions("currentProcessingStage", $processingRecs, $selunitTransferId, $objResponse);
		//}
		//addDropDownOptions("rmtestMethod", $methodRecs, $selrmTestNameId, $objResponse);
		
		return $objResponse;
	}
	
	$xajax->register(XAJAX_FUNCTION,'suplierDetail', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	//$xajax->register(XAJAX_FUNCTION,'getLotId', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	
	
	$xajax->ProcessRequest();
?>