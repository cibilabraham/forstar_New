<?php
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

	function addNewRow($company,$unit,$department)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$manageusersObj			=	new ManageUsers($databaseConnect);
		$objResponse->script("displayRow('$company','$unit','$department');");
		return $objResponse;
	}

	function getAllUnits($company,$row,$cel)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$manageusersObj			=	new ManageUsers($databaseConnect);
		if($company!='0')
		{
			$unitRecs 			= $manageusersObj->getUnit($company);
			//$objResponse->alert($vehicleNumId);
			if (sizeof($unitRecs)>0) addDropDownOptions("unit_$row", $unitRecs,$cel,$objResponse);	
		}
		return $objResponse;
	}
	
	
	function getDetailvalue($vehicleNumId,$sel,$inputId,$cel)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($vehicleNumId);
		$databaseConnect 		= new DatabaseConnect();
		$rmProcurmentOrderObj 	= new ProcurementOrder($databaseConnect);
		
		$chemicalRecs 			= $rmProcurmentOrderObj->filterChemicalList($vehicleNumId);
		//$objResponse->alert($vehicleNumId);
		if (sizeof($chemicalRecs)>0) addDropDownOptions("chemicalName_$inputId", $chemicalRecs, $cel, $objResponse);
		return $objResponse;
	}


	$xajax->register(XAJAX_FUNCTION, 'getAllUnits', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getDetailvalue', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'addNewRow', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->ProcessRequest();

?>