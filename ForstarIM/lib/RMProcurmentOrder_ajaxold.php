<?php
//require_once("lib/databaseConnect.php");
//require_once("RMProcurmentOrder_class.php");
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
	
	#Generate GatePass
	function generateGatePass($selDate)
	{
		
		$objResponse 			= new xajaxResponse();
			
		$databaseConnect 		= new DatabaseConnect();
		$rmProcurmentOrderObj = new ProcurementOrder($databaseConnect);
		//$objResponse->alert(mysqlDateFormat($selDate));
		$checkGateNumberSettingsExist=$rmProcurmentOrderObj->chkValidGatePassId($selDate);
		if ($checkGateNumberSettingsExist){
		$alphaCode=$rmProcurmentOrderObj->getAlphaCode();
		$alphaCodePrefix= $alphaCode[0];
		//$objResponse->alert($alphaCodePrefix);
		$checkExist=$rmProcurmentOrderObj->checkGatePassDisplayExist($processType);
		if ($checkExist>0){
		$getFirstRecord=$rmProcurmentOrderObj->getmaxGatePassId();
		$getFirstRec= $getFirstRecord[0];
		//$objResponse->alert($getFirstRec);
		$getFirstRecEx=explode($alphaCodePrefix,$getFirstRec);
		//$objResponse->alert($getFirstRecEx[1]);
		$nextGatePassId=$getFirstRecEx[1]+1;
		$validendno=$rmProcurmentOrderObj->getValidendnoGatePassId($selDate);	
		if ($nextGatePassId>$validendno){
		$GatePassMsg="Please set the Gate Pass number in Settings,since it reached the end no";
		$objResponse->assign("message","innerHTML",$GatePassMsg);
		}
		else{
		
		$disGateNo="$alphaCodePrefix$nextGatePassId";
		$objResponse->assign("procurmentNo","value","$disGateNo");	
		}
		
		}
		else{
		
		$validPassNo=$rmProcurmentOrderObj->getValidGatePassId($selDate);	
		$checkPassId=$rmProcurmentOrderObj->chkValidGatePassId($selDate);
		$disGatePassId="$alphaCodePrefix$validPassNo";
		$objResponse->assign("procurmentNo","value","$disGatePassId");	
		}
		
		}
		else{
		//$objResponse->alert("hi");
		$GatePassMsg="Please set the Lot Id in Settings";
		$objResponse->assign("message","innerHTML",$GatePassMsg);
		}
	
		return $objResponse;
	}
	
	
	/*
		Check Unique number
	*/
	function checkProcurmentNumberExist($reqNum, $existNum, $mode)
	{
		$objResponse = new NxajaxResponse();
	    	$databaseConnect = new DatabaseConnect();	
		$rmProcurmentOrderObj = new ProcurementOrder($databaseConnect);
		$chkUnique = $rmProcurmentOrderObj->checkUnique($reqNum,$existNum);
		if ($chkUnique) {
			$msg = "$reqNum is already in use. Please choose another one";
			$objResponse->assign("requestNumExistTxt", "innerHTML", "$msg");			
			$objResponse->script("disableStockIssuanceButtons($mode)");			
		} else {
			
			$objResponse->assign("requestNumExistTxt", "innerHTML", "");			
			$objResponse->script("enableStockIssuanceButtons($mode)");			
		}
		return $objResponse;
	}
	
	function rmProcurmentSupplierName($supplierGroupId,$inputId,$selSupplierGroupId)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($supplierGroupId);
		$databaseConnect 		= new DatabaseConnect();
		$rmProcurmentOrderObj 	= new ProcurementOrder($databaseConnect);
		$supplierRecs 			= $rmProcurmentOrderObj->filterSupplierList($supplierGroupId);
		//$objResponse->alert(sizeof($supplierRecs));
		if (sizeof($supplierRecs)>0) addDropDownOptions("supplierName_$inputId", $supplierRecs, $selSupplierGroupId, $objResponse);
		
		return $objResponse;
	}
	
	function rmProcurmentSupplierAddress($supplierNameId,$inputId,$supplierId)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($supplierGroupId);
		$databaseConnect 		= new DatabaseConnect();
		$rmProcurmentOrderObj 	= new ProcurementOrder($databaseConnect);
		$supplierAddressRecs 			= $rmProcurmentOrderObj->filterSupplierAddressList($supplierNameId);
		$pondsRecs 			= $rmProcurmentOrderObj->filterPondList($supplierNameId);
		//$inputData = ( $data == "") ? 0 : number_format($data,0,"","");
		$objResponse->assign("supplierAddress_$inputId", "value", "$supplierAddressRecs");
		//if (sizeof($supplierAddressRecs)>0) addDropDownOptions("supplierAddress", $supplierAddressRecs, $selSupplierNameId, $objResponse);
		if (sizeof($pondsRecs)>0) addDropDownOptions("pondName_$inputId", $pondsRecs, $supplierId, $objResponse);
		return $objResponse;
	}
	
	function rmProcurmentPondAddress($pondNameId,$inputId)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($pondNameId);
		$databaseConnect 		= new DatabaseConnect();
		$rmProcurmentOrderObj 	= new ProcurementOrder($databaseConnect);
		$pondAddressRecs 			= $rmProcurmentOrderObj->filterPondAddressList($pondNameId);
		$objResponse->assign("pondAddress_$inputId", "value", "$pondAddressRecs");
		//if (sizeof($pondAddressRecs)>0) addDropDownOptions("pondAddress", $pondAddressRecs, $selPondNameId, $objResponse);
		
		return $objResponse;
	}
	
	function getDetails($vehicleNumId,$sel,$inputId,$cel)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($vehicleNumId);
		$databaseConnect 		= new DatabaseConnect();
		$rmProcurmentOrderObj 	= new ProcurementOrder($databaseConnect);
		$equipmentRecs 			= $rmProcurmentOrderObj->filterEquipmentList($vehicleNumId);
		$objResponse->assign("vehicle", "value", "$vehicleNumId");
		if (sizeof($equipmentRecs)>0) addDropDownOptions("equipmentName_$inputId", $equipmentRecs, $sel, $objResponse);
		//if (sizeof($equipmentRecs)>0) assign("tblAddProcurmentOrder", $equipmentRecs, $sel, $objResponse);
		
		$chemicalRecs 			= $rmProcurmentOrderObj->filterChemicalList($vehicleNumId);
		//$objResponse->alert($vehicleNumId);
		if (sizeof($chemicalRecs)>0) addDropDownOptions("chemicalName_$inputId", $chemicalRecs, $cel, $objResponse);
		return $objResponse;
	}
	
	function equipmentQuantity($equipmentNameId,$vehicleNumId,$inputId)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($vehicleNumId);
		$databaseConnect 		= new DatabaseConnect();
		$rmProcurmentOrderObj 	= new ProcurementOrder($databaseConnect);
		$equipmentQtyRecs 			= $rmProcurmentOrderObj->filterEquipmentQty($equipmentNameId,$vehicleNumId);
		$objResponse->assign("equipmentQty_$inputId", "value", "$equipmentQtyRecs");
		//if (sizeof($pondAddressRecs)>0) addDropDownOptions("pondAddress", $pondAddressRecs, $selPondNameId, $objResponse);
		
		return $objResponse;
	}
	
	function chemicalQuantity($chemicalNameId,$vehicleNumId,$inputId)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($pondNameId);
		$databaseConnect 		= new DatabaseConnect();
		$rmProcurmentOrderObj 	= new ProcurementOrder($databaseConnect);
		$chemicalQtyRecs 			= $rmProcurmentOrderObj->filterChemicalQty($chemicalNameId,$vehicleNumId);
		$objResponse->assign("chemicalQty_$inputId", "value", "$chemicalQtyRecs");
		//if (sizeof($pondAddressRecs)>0) addDropDownOptions("pondAddress", $pondAddressRecs, $selPondNameId, $objResponse);
		
		return $objResponse;
	}
	
	
	$xajax->register(XAJAX_FUNCTION, 'checkProcurmentNumberExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'rmProcurmentSupplierName', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'rmProcurmentSupplierAddress', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'rmProcurmentPondAddress', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getDetails', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'equipmentQuantity', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'chemicalQuantity', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'generateGatePass', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->ProcessRequest();

?>