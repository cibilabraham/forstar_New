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
	
	function getlots($date,$seldateId)
	{
		
		$objResponse 			= new xajaxResponse();		
		$databaseConnect 		= new DatabaseConnect();

		$dt=mysqlDateFormat($date);
		//$objResponse->alert($dt);
		 $phtMonitorngObj 	= new PHTMonitoring($databaseConnect);		
		 $lotRecs 			= $phtMonitorngObj->getLots($dt,'');
		 
		
		if (sizeof($lotRecs)>0) {
		//$objResponse->assign("currentUnitName", "value", $unitRecs);
		addDropDownOptions("rmLotId", $lotRecs, $seldateId, $objResponse);
		}
		
		
		return $objResponse;
	}
	
	function suppliergroup($supplierId,$selSupplierId)
	{
		
		$objResponse 			= new xajaxResponse();		
		$databaseConnect 		= new DatabaseConnect();

		//$dt=mysqlDateFormat($date);
		//$objResponse->alert($supplierId);
		 $phtMonitorngObj 	= new PHTMonitoring($databaseConnect);		
		 $supplierGroupRecs 	= $phtMonitorngObj->getSupplierGroup($supplierId);
		
		
		
		if (sizeof($supplierGroupRecs)>0) {
		//$objResponse->assign("currentUnitName", "value", $unitRecs);
		addDropDownOptions("supplierGroupName", $supplierGroupRecs, $selSupplierId, $objResponse);
		}
		
		
		
		
		return $objResponse;
	}
	
	function specious($rmLotId,$input,$selSupplierGroupId)
	{
		
		$objResponse 			= new xajaxResponse();		
		$databaseConnect 		= new DatabaseConnect();

		//$dt=mysqlDateFormat($date);
		//$objResponse->alert($supplierId);
		 $phtMonitorngObj 	= new PHTMonitoring($databaseConnect);		
		 $speciousRecs 	= $phtMonitorngObj->getSpecious($rmLotId);
		 
		 $a=sizeof($speciousRecs);
		 $specious="";
		 for($i=0;$i<$a;$i++)
		 {
		 $specious.=$speciousRecs[$i][0].",";
		 }
		 $supplyRecs 	= $phtMonitorngObj->getSupplyQty($rmLotId);
		
		$supplierRecs 	= $phtMonitorngObj->getSupplier($rmLotId);
		 
		 $b=sizeof($supplierRecs);
		 $supplier="";
		 for($j=0;$j<$b;$j++)
		 {
		 $supplier.=$supplierRecs[$j][1].",";
		 }
		//$objResponse->alert($supplierRecs[0]);
		foreach($supplierRecs as $sup)
		{
		$supplierval=$sup[0];
		}
		//$objResponse->alert($supplierval);
		//$objResponse->alert($rmLotId);
		
		$supplierGroupRecs 	= $phtMonitorngObj->getSupplierGroupNm($supplierval);
		//$supplierGroupRecs 	= $phtMonitorngObj->getSupplierGroupNm($rmLotId);
		if (sizeof($supplierGroupRecs)>0) addDropDownOptions("supplierGroupName",  $supplierGroupRecs,$selSupplierGroupId, $objResponse);
		
		
		$phtCertificate=$phtMonitorngObj->getPhtCertificate($rmLotId);
		
			if (sizeof($phtCertificate)>0) addDropDownOptions("phtCertificateNo_$input",  $phtCertificate,$selSupplierGroupId, $objResponse);
		if (sizeof($supplierRecs)>0) {
		$objResponse->assign("supplier", "value", $supplier);
		//addDropDownOptions("supplierGroupName", $speciousRecs, $selSupplierId, $objResponse);
		}
		
		//$objResponse->alert($supplierRecs[0]);
		
		if (sizeof($speciousRecs)>0) {
		$objResponse->assign("specious", "value", $specious);
		//addDropDownOptions("supplierGroupName", $speciousRecs, $selSupplierId, $objResponse);
		}
		
		if (sizeof($supplyRecs)>0) {
		$objResponse->assign("supplyQty", "value", $supplyRecs);
		//addDropDownOptions("supplierGroupName", $speciousRecs, $selSupplierId, $objResponse);
		}
	
		return $objResponse;
	}
	
	function Quantity($phtCertificateId,$supplyQtyId,$inputId)
	{
		
		$objResponse 			= new xajaxResponse();		
		$databaseConnect 		= new DatabaseConnect();

		//$dt=mysqlDateFormat($date);
		//$objResponse->alert($supplierId);
		 $phtMonitorngObj 	= new PHTMonitoring($databaseConnect);		
		 $qtyIdRecs 	= $phtMonitorngObj->getQuantity($phtCertificateId);
		 //$qtyRecs 	= $phtMonitorngObj->getPhtQty($qtyIdRecs);
		
		if (sizeof($qtyIdRecs)>0) {
		$objResponse->assign("phtQuantity_$inputId", "value", $qtyIdRecs);
		//addDropDownOptions("supplierGroupName", $speciousRecs, $selSupplierId, $objResponse);
		}
		if($supplyQtyId>=$qtyIdRecs)
	{
		$objResponse->assign("setoffQuantity_$inputId", "value", $qtyIdRecs);
		$objResponse->assign("balanceQuantity_$inputId", "value", '0');
		//document.getElementById('setOfQty').value = phtQty;
		//document.getElementById('balance').value = '0';
	}
	else if($supplyQtyId<$qtyIdRecs)
	{
		$diff=$qtyIdRecs-$supplyQtyId;
		$objResponse->assign("setOfQty", "value", $supplyQtyId);
		$objResponse->assign("balance", "value", $diff);
		// document.getElementById('setOfQty').value = supplyQty;
		// document.getElementById('balance').value = phtQty-supplyQty;
	}
		//$objResponse->alert($qtyRecs);
		
		
		
		
	
		return $objResponse;
	}
	function getRMLotIDS($selDate)
	{
		$date=mysqlDateFormat($selDate);
		$sel = '';
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($date);
		$databaseConnect	   = new DatabaseConnect();
		$objManageRMLOTID      = new ManageRMLOTID($databaseConnect);
		$result      		   = $objManageRMLOTID->getLotIdDetails($date);
		
		if (sizeof($result)>0) addDropDownOptions("rm_lot_id", $result, $sel, $objResponse);
			return $objResponse;
	}
	function getRMLotIdDate($cerificateNo)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect	   = new DatabaseConnect();
		$phtMonitorngObj 		= new PHTMonitoring($databaseConnect);
		$sel = '';
		$result      		   = $phtMonitorngObj->getCerificateSupplierData($cerificateNo);
		if (sizeof($result)>0) addDropDownOptions("rmlotidCertify", $result, $sel, $objResponse);
		return $objResponse;
	}

	function getWeightmentData($rmlotid,$phtCerificate)
	{
		$objResponse 		= new xajaxResponse();
		$databaseConnect	= new DatabaseConnect();
		$phtMonitorngObj 	= new PHTMonitoring($databaseConnect);
		$result      		= $phtMonitorngObj->getWeightmentData($rmlotid,$phtCerificate);
		$objResponse->assign("lotIdCertificate", "innerHTML", $result);
		//$objResponse->script("tableStructure();");
		return $objResponse;
	}

	function displayTable($rowcnt)
	{	
		$objResponse 		= new xajaxResponse();
		$databaseConnect	= new DatabaseConnect();
		$phtMonitorngObj 	= new PHTMonitoring($databaseConnect);
		//$objResponse->alert("hii");
		$result ="";
		$result.="<table cellspacing='1' cellpadding='4' bgcolor='#999999' align='center'>";
		$result.="<tr bgcolor='#f2f2f2'><td class='listing-head' nowrap>Supplier Name</td><td class='listing-head' nowrap>Pond Name</td><td class='listing-head' nowrap>Species</td><td class='listing-head' nowrap>available Qty</td><td class='listing-head' nowrap>balance Qty</td></tr>";
		for($i=0; $i<$rowcnt; $i++)
		{
		$result.="<tr id='selectedlotId_".$i."' bgcolor='#e8edff'></tr>"; 
			
		}
		$result.="</table>";
		$objResponse->assign("selectedlotId", "innerHTML", $result);
		return $objResponse;
	}

	function displaySelected($row,$checkedSize,$weightmentId,$certifyQnty)
	{
		$objResponse 		= new xajaxResponse();
		$databaseConnect	= new DatabaseConnect();
		$phtMonitorngObj 	= new PHTMonitoring($databaseConnect);
		$weighment      		= $phtMonitorngObj->getWeightmentDataSingle($weightmentId);
		$result="";
		$weight=$weighment[4];
		if($certifyQnty>$weight)
		{
			$diff=$certifyQnty-$weight;
			$qntyStatus=1;
		}
		else
		{
			$diff=$weight-$certifyQnty;
			$qntyStatus=0;
		}
		//$qntyStatus=1 means +ve;
		if($checkedSize=='0')
		{ 	
			
			$rowSz=0;
			//$result.="<tr bgcolor='#e8edff'>";
			$result.="<td class='listing-item' nowrap> <input type='hidden'  value='".$weighment[0]."' id='weightmentEntryId_".$rowSz."' name='weightmentEntryId_".$rowSz."' >$weighment[5]</td>";
			$result.="<td class='listing-item' nowrap>$weighment[6]</td>";
			$result.="<td class='listing-item' nowrap>$weighment[7]</td>";
			$result.="<td class='listing-item' nowrap>$certifyQnty</td>";
			$result.="<td class='listing-item' nowrap>$diff<input type='hidden' name='balance_".$rowSz."' id='balance_".$rowSz."' value='".$diff."' readonly /></td>";
			//$result.="</tr>";	

		}
		else
		{
			$rowSz=$checkedSize;
			//$result.="hii hello";
			$result.="<tr bgcolor='#e8edff'>
				<td class='listing-item' nowrap> <input type='hidden'  value='".$weighment[0]."' id='weightmentEntryId_".$rowSz."' name='weightmentEntryId_".$rowSz."' >$weighment[5]</td>
				<td class='listing-item' nowrap>$weighment[6]</td>
				<td class='listing-item' nowrap>$weighment[7]</td>
				<td class='listing-item' nowrap>$certifyQnty</td>
				<td class='listing-item' nowrap>$diff<input type='hidden' name='balance_".$rowSz."' id='balance_".$rowSz."' value='".$diff."'  readonly/></td>
				<input id='qntyStatus_".$rowSz."' type='hidden' name='qntyStatus_".$rowSz."' size='15' ' value='".$qntyStatus."'></tr>";
		}
		$objResponse->assign("selectedlotId_$rowSz", "innerHTML", $result);
		return $objResponse;
	}

	function getRMLotIDResult($date,$rmLotId)
	{
		$objResponse 		= new xajaxResponse();
		$databaseConnect	= new DatabaseConnect();
		$phtMonitorngObj 	= new PHTMonitoring($databaseConnect);
		$result=$phtMonitorngObj->getWeightmentDataRMLotID($rmLotId);
		$objResponse->assign("lotIdDetail", "innerHTML", $result);
		//$objResponse->script("tableStructure();");
		return $objResponse;
	}

	function certificateAvailableQty($cerificateID,$row,$weightmentId)
	{
		$objResponse 		= new xajaxResponse();
		$databaseConnect	= new DatabaseConnect();
		$phtMonitorngObj 	= new PHTMonitoring($databaseConnect);
		$result=$phtMonitorngObj->getcerificateAvailableQnty($cerificateID,$weightmentId);
		$objResponse->assign("availableQtySupplier_$row", "value", $result);
		$objResponse->script("adjustedQnty($result,$row);");	
		//$objResponse->script("tableStructure();");
		return $objResponse;
	}
	function saveTemporary($weightmentId,$certificateId,$availQty,$supplyQty,$balanceQty)
	{
		$objResponse 		= new xajaxResponse();
		$databaseConnect	= new DatabaseConnect();
		$sessObj 	= new Session($databaseConnect);
		$userId		=	$sessObj->getValue("userId");
		$phtMonitorngObj 	= new PHTMonitoring($databaseConnect);
		$result=$phtMonitorngObj->insertTemporary($weightmentId,$certificateId,$availQty,$supplyQty,$balanceQty,$userId);
		//$objResponse->script("tableStructure();");
		return $objResponse;
	}
	function deleteTemporaryData($userId)
	{
		$objResponse 		= new xajaxResponse();
		$databaseConnect	= new DatabaseConnect();
		$sessObj 	= new Session($databaseConnect);
		$userId		=	$sessObj->getValue("userId");
		$phtMonitorngObj 	= new PHTMonitoring($databaseConnect);
		$result=$phtMonitorngObj->deleteTemporaryData($userId);
		$objResponse->script("formSubmit();");
		//$objResponse->alert("hii");
		return $objResponse;
	}


	/*function getCertificateLot($cerificateNo)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect	   = new DatabaseConnect();
		$phtMonitorngObj 		= new PHTMonitoring($databaseConnect);
		
		$result      		   = $phtMonitorngObj->getCerificateLotId($date);
		//$msg="hello how are you";
		$objResponse->assign("lotIdCertificate", "innerHTML", $result);
		return $objResponse;
	}*/

	/*function getRMLotIDResult($date,$rmLotId)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect	   = new DatabaseConnect();
		$phtMonitorngObj 		= new PHTMonitoring($databaseConnect);
		
		$result      		   = $phtMonitorngObj->getLotIdValues($date);
		//$msg="hello how are you";
		$objResponse->assign("lotIdDetail", "innerHTML", $result);
		return $objResponse;
	}*/
	
	$xajax->register(XAJAX_FUNCTION,'deleteTemporaryData', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'saveTemporary', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'certificateAvailableQty', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'displayTable', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'displaySelected', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getWeightmentData', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getRMLotIdDate', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getCertificateLot', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getRMLotIDResult', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getCertificateLot', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getRMLotIDS', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getlots', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'suppliergroup', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'specious', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'Quantity', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->ProcessRequest();
?>
	