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
	//function generateGatePass($selDate)
	{
		$selDate=Date('Y-m-d');
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($selDate);	
		$databaseConnect 		= new DatabaseConnect();
		$rmProcurmentGatePassObj = new ProcurementGatePass($databaseConnect);
		
		//$objResponse->alert(mysqlDateFormat($selDate));
		
		$checkGateNumberSettingsExist=$rmProcurmentGatePassObj->chkValidGatePassId($selDate);
		if ($checkGateNumberSettingsExist){
		$alphaCode=$rmProcurmentGatePassObj->getAlphaCode();
		$alphaCodePrefix= $alphaCode[0];
		//$objResponse->alert("HII");
		//$objResponse->alert($alphaCodePrefix);
		$checkExist=$rmProcurmentGatePassObj->checkGatePassDisplayExist($processType);
		if ($checkExist>0){
		$getFirstRecord=$rmProcurmentGatePassObj->getmaxGatePassId();
		$getFirstRec= $getFirstRecord[0];
		//$objResponse->alert($getFirstRec);
		$getFirstRecEx=explode($alphaCodePrefix,$getFirstRec);
		//$objResponse->alert($getFirstRecEx[1]);
		$nextGatePassId=$getFirstRecEx[1]+1;
		//$objResponse->alert($nextGatePassId);
		$validendno=$rmProcurmentGatePassObj->getValidendnoGatePassId($selDate);	
		if ($nextGatePassId>$validendno){
		$GatePassMsg="Please set the Gate Pass number in Settings,since it reached the end no";
		$objResponse->assign("message","innerHTML",$GatePassMsg);
		}
		else{
		
		$disGateNo="$alphaCodePrefix$nextGatePassId";
		$objResponse->assign("procurmentGatePass","value","$disGateNo");	
		}
		
		}
		else{
		
		$validPassNo=$rmProcurmentGatePassObj->getValidGatePassId($selDate);	
		$checkPassId=$rmProcurmentGatePassObj->chkValidGatePassId($selDate);
		$disGatePassId="$alphaCodePrefix$validPassNo";
		$objResponse->assign("procurmentGatePass","value","$disGatePassId");	
		}
		
		}
		else{
		//$objResponse->alert("hi");
		$GatePassMsg="Please set the gate pass in Settings";
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
		$rmProcurmentGatePassObj = new ProcurementGatePass($databaseConnect);
		$chkUnique = $rmProcurmentGatePassObj->checkUnique($reqNum,$existNum);
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
		// $objResponse->alert($inputId);
		$databaseConnect 		= new DatabaseConnect();
		$rmProcurmentGatePassObj 	= new ProcurementGatePass($databaseConnect);
		$supplierRecs 			= $rmProcurmentGatePassObj->filterSupplierList($supplierGroupId);
		//$objResponse->alert(sizeof($supplierRecs));
		if (sizeof($supplierRecs)>0) addDropDownOptions("supplierName_$inputId", $supplierRecs, $selSupplierGroupId, $objResponse);
		
		return $objResponse;
	}
	
	function rmProcurmentSupplierAddress($supplierNameId,$inputId,$supplierId)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($supplierGroupId);
		$databaseConnect 		= new DatabaseConnect();
		$rmProcurmentGatePassObj 	= new ProcurementGatePass($databaseConnect);
		$supplierAddressRecs 			= $rmProcurmentGatePassObj->filterSupplierAddressList($supplierNameId);
		$pondsRecs 			= $rmProcurmentGatePassObj->filterPondList($supplierNameId);
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
		$rmProcurmentGatePassObj 	= new ProcurementGatePass($databaseConnect);
		$pondAddressRecs 			= $rmProcurmentGatePassObj->filterPondAddressList($pondNameId);
		$objResponse->assign("pondAddress_$inputId", "value", "$pondAddressRecs");
		//if (sizeof($pondAddressRecs)>0) addDropDownOptions("pondAddress", $pondAddressRecs, $selPondNameId, $objResponse);
		
		return $objResponse;
	}
	
	function getDetails($vehicleNumId,$sel,$inputId,$cel)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($vehicleNumId);
		$databaseConnect 		= new DatabaseConnect();
		$rmProcurmentGatePassObj 	= new ProcurementGatePass($databaseConnect);
		$equipmentRecs 			= $rmProcurmentGatePassObj->filterEquipmentList($vehicleNumId);
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
		$rmProcurmentGatePassObj 	= new ProcurementGatePass($databaseConnect);
		
		$chemicalRecs 			= $rmProcurmentGatePassObj->filterChemicalList($vehicleNumId);
		//$objResponse->alert($vehicleNumId);
		if (sizeof($chemicalRecs)>0) addDropDownOptions("chemicalName_$inputId", $chemicalRecs, $cel, $objResponse);
		return $objResponse;
	}
	function equipmentQuantity($equipmentNameId,$vehicleNumId,$inputId)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($vehicleNumId);
		$databaseConnect 		= new DatabaseConnect();
		$rmProcurmentGatePassObj 	= new ProcurementGatePass($databaseConnect);
		$equipmentQtyRecs 			= $rmProcurmentGatePassObj->filterEquipmentQty($equipmentNameId,$vehicleNumId);
		$objResponse->assign("equipmentQty_$inputId", "value", "$equipmentQtyRecs");
		//if (sizeof($pondAddressRecs)>0) addDropDownOptions("pondAddress", $pondAddressRecs, $selPondNameId, $objResponse);
		
		return $objResponse;
	}
	
	function chemicalQuantity($chemicalNameId,$vehicleNumId,$inputId)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($pondNameId);
		$databaseConnect 		= new DatabaseConnect();
		$rmProcurmentGatePassObj 	= new ProcurementGatePass($databaseConnect);
		$chemicalQtyRecs 			= $rmProcurmentGatePassObj->filterChemicalQty($chemicalNameId,$vehicleNumId);
		$objResponse->assign("chemicalQty_$inputId", "value", "$chemicalQtyRecs");
		//if (sizeof($pondAddressRecs)>0) addDropDownOptions("pondAddress", $pondAddressRecs, $selPondNameId, $objResponse);
		
		return $objResponse;
	}
	
	function getOutSeal($sealNo)
	{
		$objResponse 			    = new xajaxResponse();
		$databaseConnect 		    = new DatabaseConnect();
		$rmProcurmentGatePassObj 	= new ProcurementGatePass($databaseConnect);
		$checkSeal     			    = $rmProcurmentGatePassObj->checkSealNo($sealNo);
		if($checkSeal > 0)
		{
			$objResponse->assign("outSealAvailable", "value", "ALready Used or Blocked");
			$objResponse->alert("This seal no already used please enter different seal no");
		}
		else
		{
			$objResponse->assign("outSealAvailable", "value", "");
		}	
		return $objResponse;
	}
	
	function getAvailableSealNos($sealNoFrom)
	{
		$objResponse 			    = new xajaxResponse();
		$databaseConnect 		    = new DatabaseConnect();
		$rmProcurmentGatePassObj 	= new ProcurementGatePass($databaseConnect);
		$result     			    = $rmProcurmentGatePassObj->getAvailableSealNos($sealNoFrom);
		$returnVal = '';
		if(sizeof($result) > 0)
		{
			$i = 0;
			$returnVal.= '<table width="100%">';
			$returnVal.='<tr><td class="listing-head" colspan="4"> Available seal numbers </td></tr>';
			$returnVal.='<tr>';
			foreach($result as $res)
			{
				if($i%4 == 0)
				{
					$returnVal.= '</tr><tr>';
				}
				$returnVal.= '<td class="listing-head"><a href="javascript:void(0);" onclick="assignToOutSeal('.$res.');">'.$res.'</a></td>';
				$i++;
			}
			$returnVal.= '</tr></table>';
		}
		
		$objResponse->assign("popupcontent", "innerHTML", $returnVal);	
		return $objResponse;
	}
	function getAvailableSealNosForInseals($sealNoFrom,$sealNos,$insealId)
	{
		$objResponse 			    = new xajaxResponse();
		$databaseConnect 		    = new DatabaseConnect();
		$rmProcurmentGatePassObj 	= new ProcurementGatePass($databaseConnect);
		$result     			    = $rmProcurmentGatePassObj->getAvailableSealNos($sealNoFrom);
		$returnVal = '';
		if(sizeof($result) > 0)
		{
			$i = 0;
			$returnVal.= '<table width="100%">';
			$returnVal.='<tr><td class="listing-head" colspan="4"> Available seal numbers </td></tr>';
			$returnVal.='<tr>';
			foreach($result as $res)
			{
				if($i%4 == 0)
				{
					$returnVal.= '</tr><tr>';
				}
				$returnVal.= '<td class="listing-head"><a href="javascript:void(0);" onclick="assignToInSeal('.$res.','.$insealId.');">'.$res.'</a></td>';
				$i++;
			}
			$returnVal.= '</tr></table>';
		}
		
		$objResponse->assign("popupcontent", "innerHTML", $returnVal);	
		return $objResponse;
	}

	function addSealAssigned($number_gen_id,$seal_no,$user_id,$login_time)
	{
		$objResponse 			    = new xajaxResponse();
		$databaseConnect 		    = new DatabaseConnect();
		$rmProcurmentGatePassObj 	= new ProcurementGatePass($databaseConnect);
		
		$result = $rmProcurmentGatePassObj->addSealAssigned($number_gen_id,$seal_no,$user_id,$login_time);
		
		return $objResponse;
	}
	function assignSealsInsert($id,$seal,$userid,$logtime)
	{
		$objResponse 			    = new xajaxResponse();
		$databaseConnect 		    = new DatabaseConnect();
		$rmProcurmentGatePassObj 	= new ProcurementGatePass($databaseConnect);
		$result     			    = $rmProcurmentGatePassObj->insertSeal($id,$seal,$userid,$logtime);
		//$objResponse->alert("hii");
		return $objResponse;
	}

	/*function checkSealUsed($checkSeals)
	{
		$checkSeals = explode(',',$checkSeals);
		$objResponse 			    = new xajaxResponse();
		$databaseConnect 		    = new DatabaseConnect();
		$rmProcurmentGatePassObj 	= new ProcurementGatePass($databaseConnect);
		$result     			    = $rmProcurmentGatePassObj->checkSealUsed($checkSeals);
		// $objResponse->alert($result);
		$objResponse->assign("sealsAvailable", "value", $result);	
		return $objResponse;
	}*/

	

	function checkSealUsedIn($checkSeals)
	{
		$resultVal='';
		$objResponse 			    = new xajaxResponse();
		$databaseConnect 		    = new DatabaseConnect();
		$rmProcurmentGatePassObj 	= new ProcurementGatePass($databaseConnect);
		//$objResponse->alert($checkSeals);
		$arr=explode(",",$checkSeals);
		$arrSz=sizeof($arr);
		//$objResponse->alert($arrSz);
			for($i=0; $i<$arrSz; $i++)
			{
				$val=$arr[$i];
				//$objResponse->alert($val);
				$arrVal=explode("/",$val);
				$sealno=$arrVal[0];
				$alpha=$arrVal[1];
				$id=$arrVal[2];
				$results     			    = $rmProcurmentGatePassObj->checkSealUsedIns($sealno,$alpha,$id);
				if($results!="")
				{
				$resultVal.=$results ;
				
				}
			}
		
		//$objResponse->alert($results);
		//$objResponse->alert($resultVal);
		if($resultVal!="")
		{
			$objResponse->assign("err1", "innerHTML", $resultVal);
			$objResponse->script("sealStatusRen($resultVal);");
		}
		else
		{
			$res=0;
			$objResponse->script("sealStatusRen($res);");
		}
		
		//$message="Seal no already exist";
		//$objResponse->assign("err1","innerHTML",$message);
		return $objResponse;
	}
	
	###get seal nos in pop up
	function getAllAvailableAlphaPrefix($inputStatus,$row)
	{
		$objResponse 			    = new xajaxResponse();
		$databaseConnect 		    = new DatabaseConnect();
		$rmProcurmentGatePassObj 	= new ProcurementGatePass($databaseConnect);
		$result     			    = $rmProcurmentGatePassObj->getAvailableAlphaprefixSealNosAll($inputStatus,$row);
		if(sizeof($result)>0)
		{
			$objResponse->assign("dialog", "innerHTML", $result);
			$objResponse->script("tabactive();");
		}	
		
		
		return $objResponse;
	}


	###assigning seal no according to starting number in pagination
	function getAllSealNo($i,$id,$newStartNo,$endno,$alphacode,$inputStatus,$row,$startOriginal,$newPage)
	{
		$objResponse 			    = new xajaxResponse();
		$databaseConnect 		    = new DatabaseConnect();
		$rmProcurmentGatePassObj 	= new ProcurementGatePass($databaseConnect);
		//$objResponse->alert("56789");
		$result     			    = $rmProcurmentGatePassObj->getAllSealNo($i,$id,$newStartNo,$endno,$alphacode,$inputStatus,$row,$startOriginal,$newPage);
		if(sizeof($result)>0)
		{
			$objResponse->assign("tabs-$i", "innerHTML", $result);
			
		}	
		
		
		return $objResponse;
	}

	###assigning seal no according to starting number in pagination and also search no.
	function getSearchSealNo($i,$id,$newStartNo,$endno,$alphacode,$inputStatus,$row,$startOriginal,$newPage,$searchNo)
	{
		$objResponse 			    = new xajaxResponse();
		$databaseConnect 		    = new DatabaseConnect();
		$rmProcurmentGatePassObj 	= new ProcurementGatePass($databaseConnect);
		//$objResponse->alert($i.','.$id.','.$newStartNo.','.$endno.','.$alphacode.','.$inputStatus.','.$row.','.$startOriginal.','.$newPage.','.$searchNo);
		$result     			    = $rmProcurmentGatePassObj->getSearchSealNo($i,$id,$newStartNo,$endno,$alphacode,$inputStatus,$row,$startOriginal,$newPage,$searchNo);
		if(sizeof($result)>0)
		{
			$objResponse->assign("tabs-$i", "innerHTML", $result);
			
		}	
		
		
		return $objResponse;
	}

	$xajax->register(XAJAX_FUNCTION, 'getSearchSealNo', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'checkSealUsedIn', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'checkSealUsedOut', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'assignSealsInsert', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getAllSealNo', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getAllAvailableAlphaPrefix', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'checkProcurmentNumberExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'rmProcurmentSupplierName', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'rmProcurmentSupplierAddress', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'rmProcurmentPondAddress', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getDetails', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getDetailvalue', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'equipmentQuantity', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'chemicalQuantity', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'generateGatePass', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getOutSeal', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getAvailableSealNos', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getAvailableSealNosForInseals', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'addSealAssigned', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'checkSealUsed', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	
	$xajax->ProcessRequest();
?>