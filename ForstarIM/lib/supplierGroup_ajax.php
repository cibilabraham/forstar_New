<?php

require_once("libjs/xajax_core/xajax.inc.php");


$xajax = new xajax();	


$xajax->configure('statusMessages', true); // For display status
class NxajaxResponse extends xajaxResponse
	{
		function addCreateOptions($sSelectId, $options, $cId)
		{
			$this->script("document.getElementById('".$sSelectId."').length=0");
   			if (sizeof($options) >0) {
				foreach ($options as $option=>$val) {
					$this->script("addDropDownList('".$cId."','".$sSelectId."','".$option."','".addSlash($val)."');");
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

	function locationName($supplierId, $selSupplierId,$field)
	{
		
		//$objResponse->alert($supplierId);
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($supplierId);
		$databaseConnect 		= new DatabaseConnect();
		$supplierGroupObj 	= new SupplierGroup($databaseConnect);
		$locationRecs 			= $supplierGroupObj->filterLocationList($supplierId);
		//filterPondList($locationId)
		//$location=$supplierGroupObj->filterLocationName($locationRecs);
		if (sizeof($locationRecs)>0) addDropDownOptions("suplocField_$field", $locationRecs, $selSupplierId, $objResponse);
		
		return $objResponse;
	}
	
	function pondName($locationId, $selLocationId,$field)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($locationId);
		$databaseConnect 		= new DatabaseConnect();
		$supplierGroupObj 	= new SupplierGroup($databaseConnect);
		$pondRecs 			= $supplierGroupObj->filterPondList($locationId);
		
		if (sizeof($pondRecs)>0) addDropDownOptions("pondField_$field", $pondRecs, $selLocationId, $objResponse);
		
		// Add by Shobu
		$objResponse->script("checkPondUnique(".$field.");");

		return $objResponse;
	}


	function checkPondUnique($supplierId, $locationId,$pondId, $supplierGroupId)
	{
		
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$supplierGroupObj 		= new SupplierGroup($databaseConnect);

		//$objResponse->alert($supplierId.",". $locationId.",".$pondId.",". $supplierGroupId);
		$chkEntryExist			= $supplierGroupObj->chkPondEntryExist($supplierId, $locationId,$pondId, $supplierGroupId);
		if ($chkEntryExist) {			
			$objResponse->assign("divEntryExistTxt", "innerHTML", "The selected farm is already assigned to another group. Please choose another one.");
			$objResponse->assign("entryExist", "value", 1);
		} else  {
			$objResponse->assign("divEntryExistTxt", "innerHTML", "");
			$objResponse->assign("entryExist", "value", "");
		}
		

		return $objResponse;
	}

$xajax->register(XAJAX_FUNCTION,'locationName', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'pondName', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION,'checkPondUnique', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->ProcessRequest();
?>