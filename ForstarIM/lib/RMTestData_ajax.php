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
	
	function lotId($unitId,$companyId,$selUnitId)
	{
		
		$objResponse 			= new xajaxResponse();
		
		$databaseConnect 		= new DatabaseConnect();
		
		$rmTestDataObj 	= new RMTestData($databaseConnect);
		//$objResponse->alert($unitId);
		$lotRecs 			= $rmTestDataObj->getLotName($unitId,$companyId);
		
		if (sizeof($lotRecs)>0) {
		//$objResponse->alert($methodRecs);
		addDropDownOptions("rmLotId", $lotRecs, $selUnitId, $objResponse);
		}
		//addDropDownOptions("rmtestMethod", $methodRecs, $selrmTestNameId, $objResponse);
		
		return $objResponse;
	}
	
	function testMethod($rmTestNameId,$selrmTestNameId)
	{
		
		$objResponse 			= new xajaxResponse();
		
		$databaseConnect 		= new DatabaseConnect();
		
		$rmTestDataObj 	= new RMTestData($databaseConnect);
		
		$methodRecs 			= $rmTestDataObj->getTestMethod($rmTestNameId);
		
		if (sizeof($methodRecs)>0) {
		//$objResponse->alert($methodRecs);
		$objResponse->assign("rmtestMethod", "value", $methodRecs);
		}
		//addDropDownOptions("rmtestMethod", $methodRecs, $selrmTestNameId, $objResponse);
		
		return $objResponse;
	}
	
	
	$xajax->register(XAJAX_FUNCTION,'lotId', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'testMethod', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	
	
	$xajax->ProcessRequest();
?>