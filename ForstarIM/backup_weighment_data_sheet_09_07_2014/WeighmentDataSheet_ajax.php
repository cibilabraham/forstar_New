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
function weightmentSupplierName($supplierGroupId,$inputId,$selSupplierGroupId)
	{
		
		$objResponse 			= new xajaxResponse();
		// $objResponse->alert($inputId);
		$databaseConnect 		= new DatabaseConnect();
		$objWeighmentDataSheet 	= new WeighmentDataSheet($databaseConnect);
		$supplierRecs 			= $objWeighmentDataSheet->filterSupplierList($supplierGroupId);
		//$objResponse->alert(sizeof($supplierRecs));
		if (sizeof($supplierRecs)>0) addDropDownOptions("supplierName_$inputId", $supplierRecs, $selSupplierGroupId, $objResponse);
		
		return $objResponse;
	}
	
	function weightmentSupplierAddress($supplierNameId,$inputId,$supplierId)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($supplierGroupId);
		$databaseConnect 		= new DatabaseConnect();
		$objWeighmentDataSheet 	= new WeighmentDataSheet($databaseConnect);
		//$supplierAddressRecs 			= $rmProcurmentOrderObj->filterSupplierAddressList($supplierNameId);
		$pondsRecs 			= $objWeighmentDataSheet->filterPondList($supplierNameId);
		//$inputData = ( $data == "") ? 0 : number_format($data,0,"","");
		//$objResponse->assign("supplierAddress_$inputId", "value", "$supplierAddressRecs");
		//if (sizeof($supplierAddressRecs)>0) addDropDownOptions("supplierAddress", $supplierAddressRecs, $selSupplierNameId, $objResponse);
		if (sizeof($pondsRecs)>0) addDropDownOptions("pondName_$inputId", $pondsRecs, $supplierId, $objResponse);
		return $objResponse;
	}
	function weightmentSpecies($pondNameId,$inputId,$pondId)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($supplierGroupId);
		$databaseConnect 		= new DatabaseConnect();
		$objWeighmentDataSheet 	= new WeighmentDataSheet($databaseConnect);
		$speciesRecs 			= $objWeighmentDataSheet->filterSpecies($pondNameId);
		//$inputData = ( $data == "") ? 0 : number_format($data,0,"","");
		//$objResponse->assign("supplierAddress_$inputId", "value", "$supplierAddressRecs");
		//if (sizeof($supplierAddressRecs)>0) addDropDownOptions("supplierAddress", $supplierAddressRecs, $selSupplierNameId, $objResponse);
		if (sizeof($speciesRecs)>0) addDropDownOptions("product_species_$inputId", $speciesRecs, $pondId, $objResponse);
		return $objResponse;
	}
	
	function generateDatasheet()
	//function generateGatePass($selDate)
	{
		$selDate=Date('Y-m-d');
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($selDate);	
		$databaseConnect 		= new DatabaseConnect();
		$objWeighmentDataSheet = new WeighmentDataSheet($databaseConnect);
		
		//$objResponse->alert(mysqlDateFormat($selDate));
		
		$checkGateNumberSettingsExist=$objWeighmentDataSheet->chkValidDataSheetId($selDate);
		if ($checkGateNumberSettingsExist){
		$alphaCode=$objWeighmentDataSheet->getAlphaCode();
		$alphaCodePrefix= $alphaCode[0];
		//$objResponse->alert("HII");
		//$objResponse->alert($alphaCodePrefix);
		$checkExist=$objWeighmentDataSheet->checkDataSheetDisplayExist($processType);
		if ($checkExist>0){
		$getFirstRecord=$objWeighmentDataSheet->getmaxDataSheetId();
		$getFirstRec= $getFirstRecord[0];
		//$objResponse->alert($getFirstRec);
		$getFirstRecEx=explode($alphaCodePrefix,$getFirstRec);
		//$objResponse->alert($getFirstRecEx[1]);
		$nextDataSheetId=$getFirstRecEx[1]+1;
		//$objResponse->alert($nextDataSheetId);
		$validendno=$objWeighmentDataSheet->getValidendnoDataSheetId($selDate);	
		if ($nextDataSheetId>$validendno){
		$DataSheetMsg="Please set the Data sheet number in Settings,since it reached the end no";
		$objResponse->assign("message","innerHTML",$DataSheetMsg);
		}
		else{
		
		$disGateNo="$alphaCodePrefix$nextDataSheetId";
		$objResponse->assign("data_sheet_slno","value","$disGateNo");	
		}
		
		}
		else{
		
		$validPassNo=$objWeighmentDataSheet->getValidDataSheetId($selDate);	
		$checkPassId=$objWeighmentDataSheet->chkValidDataSheetId($selDate);
		$disDataSheetId="$alphaCodePrefix$validPassNo";
		$objResponse->assign("data_sheet_slno","value","$disDataSheetId");	
		}
		
		}
		else{
		//$objResponse->alert("hi");
		$DataSheetMsg="Please set the gate pass in Settings";
		$objResponse->assign("message","innerHTML",$DataSheetMsg);
		}
	
		return $objResponse;
	}
	
function rmprocurementdet($gatePass,$inputId,$value)
{

	$objResponse 			= new xajaxResponse();
	$databaseConnect 		= new DatabaseConnect();
	$objWeighmentDataSheet =   new WeighmentDataSheet($databaseConnect);
	//$objResponse->alert($gatePass);
	$result = $objWeighmentDataSheet->getProcurementOrderID($gatePass);
	$proID=$result[0];
	
	$results 			= $objWeighmentDataSheet->getProcurementGatePassDetails($proID);
	$gateDetails = 'Supplier Challan No : '.$results[0].' Date Of Entry : '.$results[1].' In seal no : '.$results[2];
	$objResponse->assign("gate_pass_details", "value", $gateDetails);
	
	$purchaseRecs 			= $objWeighmentDataSheet->filterPurchaseProList($proID);
	//$objResponse->alert(sizeof($procurmentSupplierRecs));
	if (sizeof($purchaseRecs)>0) addDropDownOptions("purchase_supervisor", $purchaseRecs, $proID, $objResponse);
	
	
	return $objResponse;
}

	
	
function ProcurmentDetail($gatePass,$inputId,$value)
	{

	$objResponse 			= new xajaxResponse();
	$databaseConnect 		= new DatabaseConnect();
	$objWeighmentDataSheet =   new WeighmentDataSheet($databaseConnect);
	
	$result = $objWeighmentDataSheet->getProcurementOrderID($gatePass);
	$proID=$result[0];
	
	//$objResponse->alert($proID);
	$procurmentSupplierRecs 			= $objWeighmentDataSheet->filterSupplierProList($proID);
	//$objResponse->alert(sizeof($procurmentSupplierRecs));
	if (sizeof($procurmentSupplierRecs)>0) addDropDownOptions("supplierNamepro_$inputId", $procurmentSupplierRecs, $proID, $objResponse);
	
	$procurmentPondRecs 			= $objWeighmentDataSheet->filterPondProList($proID);
	//$objResponse->alert(sizeof($supplierRecs));
	if (sizeof($procurmentPondRecs)>0) addDropDownOptions("pondNamepro_$inputId", $procurmentPondRecs, $proID, $objResponse);
	
	$objResponse->assign("hidTableRowCountsValhid", "value", sizeof($procurmentSupplierRecs));
	
	$packageTypeRecs 			= $objWeighmentDataSheet->filterEquipmentProList($proID);
	//$objResponse->alert(sizeof($supplierRecs));
	if (sizeof($packageTypeRecs)>0) addDropDownOptions("packageTypepro_$inputId", $packageTypeRecs, $proID, $objResponse);
	
		
	//$objResponse->assign("harvesting_equipment", "value", $result[1]);
	//$objResponse->assign("pondName", "value", $result[2]);
	
	//$gateDetails = 'Supplier Challan No : '.$result[17].' Date Of Entry : '.$result[18].' In seal no : '.$result[19];
	//$objResponse->assign("gate_pass_details", "value", $gateDetails);
	return $objResponse;
	}
	function ProcurmentDetailEquipment($gatePass,$inputId,$value)
	{

	$objResponse 			= new xajaxResponse();
	$databaseConnect 		= new DatabaseConnect();
	$objWeighmentDataSheet =   new WeighmentDataSheet($databaseConnect);
	$result = $objWeighmentDataSheet->getProcurementOrderID($gatePass);
	$proID=$result[0];
	//$objResponse->alert($proID);
	$procurmentEquipmentRecs 			= $objWeighmentDataSheet->filterEquipmentProList($proID);
	//$objResponse->alert(sizeof($supplierRecs));
	if (sizeof($procurmentEquipmentRecs)>0) addDropDownOptions("equipmentName_$inputId", $procurmentEquipmentRecs, $proID, $objResponse);
	$objResponse->assign("hidTableRowCounthid", "value", sizeof($procurmentEquipmentRecs));
	
	//$objResponse->assign("harvesting_equipment", "value", $result[1]);
	//$objResponse->assign("pondName", "value", $result[2]);
	
	//$gateDetails = 'Supplier Challan No : '.$result[17].' Date Of Entry : '.$result[18].' In seal no : '.$result[19];
	//$objResponse->assign("gate_pass_details", "value", $gateDetails);
	return $objResponse;
	}
	function ProcurmentDetailChemical($gatePass,$inputId,$value)
	{

	$objResponse 			= new xajaxResponse();
	$databaseConnect 		= new DatabaseConnect();
	$objWeighmentDataSheet =   new WeighmentDataSheet($databaseConnect);
	$result = $objWeighmentDataSheet->getProcurementOrderID($gatePass);
	$proID=$result[0];
	//$objResponse->alert($proID);
	
	$procurmentChemicalRecs 			= $objWeighmentDataSheet->filterChemicalProList($proID);
	//$objResponse->alert(sizeof($supplierRecs));
	if (sizeof($procurmentChemicalRecs)>0) addDropDownOptions("chemicalName_$inputId", $procurmentChemicalRecs, $proID, $objResponse);
	$objResponse->assign("hidChemicalRowCounthid", "value", sizeof($procurmentChemicalRecs));
	
	
		
	//$objResponse->assign("harvesting_equipment", "value", $result[1]);
	//$objResponse->assign("pondName", "value", $result[2]);
	
	//$gateDetails = 'Supplier Challan No : '.$result[17].' Date Of Entry : '.$result[18].' In seal no : '.$result[19];
	//$objResponse->assign("gate_pass_details", "value", $gateDetails);
	return $objResponse;
	}
	function weightmentSpeciespro($pondNameId,$inputId,$pondId)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($supplierGroupId);
		$databaseConnect 		= new DatabaseConnect();
		$objWeighmentDataSheet 	= new WeighmentDataSheet($databaseConnect);
		$speciesRecs 			= $objWeighmentDataSheet->filterSpecies($pondNameId);
		//$inputData = ( $data == "") ? 0 : number_format($data,0,"","");
		//$objResponse->assign("supplierAddress_$inputId", "value", "$supplierAddressRecs");
		//if (sizeof($supplierAddressRecs)>0) addDropDownOptions("supplierAddress", $supplierAddressRecs, $selSupplierNameId, $objResponse);
		if (sizeof($speciesRecs)>0) addDropDownOptions("product_speciespro_$inputId", $speciesRecs, $pondId, $objResponse);
		return $objResponse;
	}
	
	function rmProcurmentPondName($supplierNameId,$inputId,$supplierId,$gatepass)
	{
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($supplierGroupId);
		$databaseConnect 		= new DatabaseConnect();
		$objWeighmentDataSheet 	= new WeighmentDataSheet($databaseConnect);
		//$objResponse->alert($gatepass);
		$result = $objWeighmentDataSheet->getProcurementOrderID($gatepass);
		$proID=$result[0];
		//$objResponse->alert($proID);
		//$supplierAddressRecs 			= $rmProcurmentOrderObj->filterSupplierAddressList($supplierNameId);
		$pondsRecs 			= $objWeighmentDataSheet->filterPondProValue($supplierNameId,$proID);
		//$inputData = ( $data == "") ? 0 : number_format($data,0,"","");
		//$objResponse->assign("supplierAddress_$inputId", "value", "$supplierAddressRecs");
		//if (sizeof($supplierAddressRecs)>0) addDropDownOptions("supplierAddress", $supplierAddressRecs, $selSupplierNameId, $objResponse);
		if (sizeof($pondsRecs)>0) addDropDownOptions("pondNamepro_$inputId", $pondsRecs, $supplierId, $objResponse);
		return $objResponse;
	}
	function equipmentIssued($equipmentNameId,$procurementGatePass,$inputId)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($vehicleNumId);
		$databaseConnect 		= new DatabaseConnect();
		$objWeighmentDataSheet 	= new WeighmentDataSheet($databaseConnect);
		$result = $objWeighmentDataSheet->getProcurementOrderID($procurementGatePass);
		$proID=$result[0];
		$equipmentIssueRecs 			= $objWeighmentDataSheet->filterEquipmentIssue($equipmentNameId,$proID);
		$objResponse->assign("equipmentIssued_$inputId", "value", "$equipmentIssueRecs");
		//if (sizeof($pondAddressRecs)>0) addDropDownOptions("pondAddress", $pondAddressRecs, $selPondNameId, $objResponse);
		
		return $objResponse;
	}
	function chemicalIssued($chemicalNameId,$procurementGatePass,$inputId)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($vehicleNumId);
		$databaseConnect 		= new DatabaseConnect();
		$objWeighmentDataSheet 	= new WeighmentDataSheet($databaseConnect);
		$result = $objWeighmentDataSheet->getProcurementOrderID($procurementGatePass);
		$proID=$result[0];
		$chemicalIssueRecs 			= $objWeighmentDataSheet->filterChemicalIssue($chemicalNameId,$proID);
		$objResponse->assign("chemicalIssued_$inputId", "value", "$chemicalIssueRecs");
		//if (sizeof($pondAddressRecs)>0) addDropDownOptions("pondAddress", $pondAddressRecs, $selPondNameId, $objResponse);
		
		return $objResponse;
	}

$xajax->register(XAJAX_FUNCTION, 'ProcurmentDetailEquipment', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'ProcurmentDetailChemical', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chemicalIssued', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'equipmentIssued', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'rmProcurmentPondName', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'weightmentSpeciespro', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'ProcurmentDetail', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'generateDatasheet', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'weightmentSpecies', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'weightmentSupplierName', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'weightmentSupplierAddress', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'rmprocurementdet', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'rmDataSheetDetails', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getProcessCode', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->ProcessRequest();



?>