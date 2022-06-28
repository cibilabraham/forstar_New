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
	function generateGatePass()
	{
		$selDate=Date('Y-m-d');
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$rmProcurmentOrderObj = new ProcurementOrder($databaseConnect);
		//$objResponse->alert("ddd");
		//$objResponse->alert(mysqlDateFormat($selDate));
		$checkGateNumberSettingsExist=$rmProcurmentOrderObj->chkValidGatePassId($selDate);
		if (sizeof($checkGateNumberSettingsExist)>0)
		{
			$alphaCode=$rmProcurmentOrderObj->getAlphaCode();
			$alphaCodePrefix= $alphaCode[0];
			//$objResponse->alert($alphaCodePrefix);
			$numbergen=$checkGateNumberSettingsExist[0][0];
			//$objResponse->alert($numbergen);
			//}
			//$objResponse->alert($alphaCodePrefix);
			$checkExist=$rmProcurmentOrderObj->checkGatePassDisplayExist();
			//$checkExist=$rmProcurmentOrderObj->checkGatePassDisplayExist($processType);
			if ($checkExist>0)
			{
			$getFirstRecord=$rmProcurmentOrderObj->getmaxGatePassId();
			$getFirstRec= $getFirstRecord[0];
			//$objResponse->alert($getFirstRec);
			$getFirstRecEx=explode($alphaCodePrefix,$getFirstRec);
			//$objResponse->alert($getFirstRecEx[1]);
			$nextGatePassId=$getFirstRecEx[1]+1;
			$validendno=$rmProcurmentOrderObj->getValidendnoGatePassId($selDate);
			//$objResponse->alert($nextGatePassId);
				if ($nextGatePassId>$validendno)
				{
					$GatePassMsg="Please set the Gate Pass number in Settings,since it reached the end no";
					$objResponse->assign("message","innerHTML",$GatePassMsg);
				}
				else
				{
					$disGateNo="$alphaCodePrefix$nextGatePassId";
					//$objResponse->alert($disGateNo);
					$objResponse->assign("procurmentNo","value","$disGateNo");	
					$objResponse->assign("number_gen_id","value","$numbergen");	
				}
			}
			else
			{
				$validPassNo=$rmProcurmentOrderObj->getValidGatePassId($selDate);	
				$checkPassId=$rmProcurmentOrderObj->chkValidGatePassId($selDate);
				$disGatePassId="$alphaCodePrefix$validPassNo";
				$objResponse->assign("procurmentNo","value","$disGatePassId");	
				$objResponse->assign("number_gen_id","value","$numbergen");	
			}
		}
		else
		{
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
		if ($chkUnique) 
		{
			$msg = "$reqNum is already in use. Please choose another one";
			$objResponse->assign("requestNumExistTxt", "innerHTML", "$msg");			
			$objResponse->script("disableStockIssuanceButtons($mode)");			
		} 
		else 
		{
			$objResponse->assign("requestNumExistTxt", "innerHTML", "");			
			$objResponse->script("enableStockIssuanceButtons($mode)");			
		}
		return $objResponse;
	}
	
	function rmProcurmentScheduleDriverAndVehicleDetails($schedule_date,$inputId,$driverID,$vehicleID,$procurementId,$tableRowCount,$mode)
	{
		$objResponse 			= new NxajaxResponse();
		//$objResponse->alert($mode);
		$databaseConnect 		= new DatabaseConnect();
		$rmProcurmentOrderObj 	= new ProcurementOrder($databaseConnect);
		$schedule_day=mysqlDateFormat($schedule_date);
		//$objResponse->alert($schedule_day);
		$driverRecs 			= $rmProcurmentOrderObj->fetchAllDriverName($schedule_day,$procurementId);
		//$objResponse->alert(sizeof($supplierRecs));
		$vehicleRecs 			= $rmProcurmentOrderObj->fetchAllVehicleName($schedule_day,$procurementId);
		//$objResponse->alert(sizeof($supplierRecs));
		if($mode == "1")
		{
			if (sizeof($driverRecs)>0) $objResponse->addDropDownOptions("driverName_$inputId", $driverRecs, $driverID );
			if (sizeof($vehicleRecs)>0) $objResponse->addDropDownOptions("vehicleNumber_$inputId", $vehicleRecs, $vehicleID );
		}
		elseif($mode == "2")
		{
			$objResponse->script("getDriverArr('".json_encode($driverRecs)."','$tableRowCount')");
			$objResponse->script("getVehicleArr('".json_encode($vehicleRecs)."','$tableRowCount')");
		
		}
		return $objResponse;
	}
	
	function vehicleNumber($rowCnt)
	{
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($inputId);
		$databaseConnect 		= new DatabaseConnect();
		$rmProcurmentOrderObj 	= new ProcurementOrder($databaseConnect);
		$objResponse->alert($rowCnt);
		return $objResponse;
	}
	
	function rmProcurmentSupplierGroup($supplierNameId,$inputId,$supplierId)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($supplierGroupId);
		$databaseConnect 		= new DatabaseConnect();
		$rmProcurmentOrderObj 	= new ProcurementOrder($databaseConnect);
		//$supplierAddressRecs 			= $rmProcurmentOrderObj->filterSupplierAddressList($supplierNameId);
		//$objResponse->assign("supplierAddress_$inputId", "value", "$supplierAddressRecs");
		$supplierGroupRecs 			= $rmProcurmentOrderObj->filterSupplierGroupList($supplierNameId);
		$supplierGroupSize=sizeof($supplierGroupRecs);
		$objResponse->script("supplierGroupExist($supplierGroupSize);");
		$pondsRecs 			= $rmProcurmentOrderObj->filterPondList($supplierNameId);
		$objResponse->assign("supplierGroup_$inputId", "value", "$supplierGroupRecs[1]");
		$locationRecs 			= $rmProcurmentOrderObj->getLandingCenterSupplier($supplierNameId);
		if (sizeof($locationRecs)>0) addDropDownOptions("location_$inputId", $locationRecs, $supplierId, $objResponse);
		if (sizeof($pondsRecs)>0) addDropDownOptions("pondName_$inputId", $pondsRecs, $supplierId, $objResponse);
		//$objResponse->script("resetField($inputId);");
		return $objResponse;
	}
	
	function rmProcurmentPondDetails($pondNameId,$inputId)
	{
		$pondQnty="";
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($pondNameId);
		$databaseConnect 		= new DatabaseConnect();
		$rmProcurmentOrderObj 	= new ProcurementOrder($databaseConnect);
		$pondLocationRecs 			= $rmProcurmentOrderObj->filterPondLocationList($pondNameId);
		$pondQuantityRecs 			= $rmProcurmentOrderObj->filterPondQtyList($pondNameId);
		if(sizeof($pondQuantityRecs)>0)
		{
			foreach($pondQuantityRecs as $pondQuantity )
			{
				$pondQnty+=$pondQuantity[1];
			}
			$pondvalue="Yes".' ('.$pondQnty.')';
		}
		else
		{
			$pondvalue="No";
		}
		if (sizeof($pondLocationRecs)>0) addDropDownOptions("location_$inputId", $pondLocationRecs, $pondNameId, $objResponse);
		$objResponse->assign("pondQty_$inputId", "value", "$pondvalue");
		//$objResponse->script("setField($inputId);");
		
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
	
	function checkProcurementOrder($gatePass)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$rmProcurmentOrderObj 	= new ProcurementOrder($databaseConnect);
		//$str = 'GTP123';
		$numbers = preg_replace('/[^0-9]/', '', $gatePass);
		$letters = preg_replace('/[^a-zA-Z]/', '', $gatePass);
		if($numbers!="" && $letters!="")
		{
			$chkDuplicate=$rmProcurmentOrderObj->chkDuplicate($gatePass);
			if(!$chkDuplicate)
			{
				$checkStatus=$rmProcurmentOrderObj->checkProcurementStatus($numbers,$letters);
				if(sizeof($checkStatus)>0)
				{	
					$id=$checkStatus[0];
					$billingCompanyId=$checkStatus[1];
					$unitid=$checkStatus[2];
					$objResponse->assign("selCompanyName", "value",$billingCompanyId);
					$objResponse->assign("unitId", "value",$unitid);
					$objResponse->assign("number_gen_id", "value",$id);
					$objResponse->assign("message","innerHTML","");
					$objResponse->script("enableButton();");
				}
				else
				{
					$GatePassMsg="Please set the Lot Id in Settings";
					$objResponse->assign("message","innerHTML",$GatePassMsg);
					$objResponse->assign("selCompanyName", "value","");
					$objResponse->assign("unitId", "value","");
					$objResponse->assign("number_gen_id", "value","");
					$objResponse->script("disableButton();");
				}
			}
			else
			{
				$GatePassMsg="Duplicate entry of procurement number";
				$objResponse->assign("message","innerHTML",$GatePassMsg);
				$objResponse->assign("selCompanyName", "value","");
				$objResponse->assign("unitId", "value","");
				$objResponse->assign("number_gen_id", "value","");
				$objResponse->script("disableButton();");
			}
		}
		/*else
		{
			$GatePassMsg="In correct Procurement number format";
			$objResponse->assign("message","innerHTML",$GatePassMsg);
		}	
		*/		
		return $objResponse;
	}
	
	
	$xajax->register(XAJAX_FUNCTION, 'checkProcurementOrder', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'vehicleNumber', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'rmProcurmentScheduleDriverAndVehicleDetails', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'generateGatePass', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'checkProcurmentNumberExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'rmProcurmentSupplierGroup', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'rmProcurmentPondDetails', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getDetails', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getDetailvalue', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'equipmentQuantity', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'chemicalQuantity', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	
	$xajax->ProcessRequest();

?>