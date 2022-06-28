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

function rmprocurementdet($rmLotID)
{

	$objResponse 			= new xajaxResponse();
	$databaseConnect 		= new DatabaseConnect();
	$objWeighmentDataSheet =   new WeighmentDataSheet($databaseConnect);
	$result = $objWeighmentDataSheet->getProcurementGatePassDetails($rmLotID);
	// $objResponse->alert($result[0].'---'.$result[1]);
	$objResponse->assign("harvesting_equipment", "value", $result[1]);
	$objResponse->assign("pondName", "value", $result[2]);
	$pondDetails = 'Supplier : '.$result[3].' Pond Quantity : '.$result[5].' Pond Size : '.$result[6].' Address : '.$result[7].' , '.$result[11].' , '.$result[10].' , '.$result[9].' '.$result[8];
	$pondDetails.= 'Expiry Date :'.$result[12];
	$objResponse->assign("pond_details", "value", $pondDetails);
	$objResponse->assign("issued", "value", $result[13]);
	// $objResponse->assign("different", "value", $result[14]);
	$objResponse->assign("gate_pass", "value", $result[15]);
	$objResponse->assign("pond_id", "value", $result[16]);
	$gateDetails = 'Supplier Challan No : '.$result[17].' Date Of Entry : '.$result[18].' In seal no : '.$result[19];
	$objResponse->assign("gate_pass_details", "value", $gateDetails);
	return $objResponse;
}

function getProcessCode($fishId,$selected,$id)
{
	$objResponse 			= new xajaxResponse();
	$databaseConnect 		= new DatabaseConnect();
	$objWeighmentDataSheet =   new WeighmentDataSheet($databaseConnect);
	$result = $objWeighmentDataSheet->getAllProcessCodeDetails($fishId);
	
	if($id == '')
	{
		$selectLoad = 'process_code';
	}
	else
	{
		$selectLoad = 'process_code'.$id;
	}

	if (sizeof($result)>0) addDropDownOptions($selectLoad, $result, $selected, $objResponse);
		
	return $objResponse;
}

$xajax->register(XAJAX_FUNCTION, 'rmprocurementdet', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'rmGatePassDetails', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getProcessCode', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->ProcessRequest();



?>