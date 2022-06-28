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
	
	function vehicleNumber($procurmentGatePassId,$selProcurmentGatePassId)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($procurmentGatePassId);
		$databaseConnect 		= new DatabaseConnect();
		$rmReceiptGatePassObj 	= new RMReceiptGatePass($databaseConnect);
		$rmReceiptVehicleRecs 			= $rmReceiptGatePassObj->filterVehicleNumber($procurmentGatePassId);
		$rmReceiptDriverRecs 			= $rmReceiptGatePassObj->filterDriver($procurmentGatePassId);
		
		if (sizeof($rmReceiptVehicleRecs)>0) addDropDownOptions("vehicleNumbers", $rmReceiptVehicleRecs, $selProcurmentGatePassId, $objResponse);
		if (sizeof($rmReceiptDriverRecs)>0) addDropDownOptions("driver", $rmReceiptDriverRecs, $selProcurmentGatePassId, $objResponse);
	
		return $objResponse;
	}
	
	function getLotId($selDate,$processType)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($processType);
		$databaseConnect 		= new DatabaseConnect();
		$rmReceiptGatePassObj 	= new RMReceiptGatePass($databaseConnect);
		
		$checkLotSettingsExist=$rmReceiptGatePassObj->chkValidLotId($selDate,$processType);
		if ($checkLotSettingsExist){
		$alphaCode=$rmReceiptGatePassObj->getAlphaCode($processType);
		$alphaCodePrefix= $alphaCode[0];
		$checkExist=$rmReceiptGatePassObj->checkLotIdDisplayExist($processType);
		if ($checkExist>0){
		$getFirstRecord=$rmReceiptGatePassObj->getmaxLotId($processType);
		$getFirstRec= $getFirstRecord[0];
		//$objResponse->alert($getFirstRec);
		$getFirstRecEx=explode($alphaCodePrefix,$getFirstRec);
		//$objResponse->alert($getFirstRecEx[1]);
		$nextLotId=$getFirstRecEx[1]+1;
		$validendno=$rmReceiptGatePassObj->getValidendnoLotId($selDate,$processType);	
		if ($nextLotId>$validendno){
		$LotIdMsg="Please set the Lot Id in Settings,since it reached the end no";
		$objResponse->assign("divlotIdExistTxt","innerHTML",$LotIdMsg);
		}
		else{
		
		$disLotIdNo="$alphaCodePrefix$nextLotId";
		$objResponse->assign("lotId","value","$disLotIdNo");	
		}
		
		}
		else{
		
		$validLotIdNo=$rmReceiptGatePassObj->getValidLotId($selDate,$processType);	
		$checkLotId=$rmReceiptGatePassObj->chkValidLotId($selDate,$processType);
		$dislotId="$alphaCodePrefix$validLotIdNo";
		$objResponse->assign("lotId","value","$dislotId");	
		}
		
		}
		else{
		//$objResponse->alert("hi");
		$LotIdMsg="Please set the Lot Id in Settings";
		$objResponse->assign("divlotIdExistTxt","innerHTML",$LotIdMsg);
		}
	
		return $objResponse;
	}
	
	function labours($superVisorNameId)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($supplierGroupId);
		$databaseConnect 		= new DatabaseConnect();
		$rmReceiptGatePassObj 	= new RMReceiptGatePass($databaseConnect);
		$labourRecs 			= $rmReceiptGatePassObj->filterLaboursList($superVisorNameId);
		//$inputData = ( $data == "") ? 0 : number_format($data,0,"","");
		$objResponse->assign("labours", "value", "$labourRecs");
		//if (sizeof($supplierAddressRecs)>0) addDropDownOptions("supplierAddress", $supplierAddressRecs, $selSupplierNameId, $objResponse);
		
		return $objResponse;
	}
	
	# Seal Number Exist
	function chksealNumberExist($sealNo, $mode, $cSOId)
	{
		$objResponse 		= new NxajaxResponse();		
		$databaseConnect 	= new DatabaseConnect();
		$sealNumberObj = new SealNumber($databaseConnect);
		//$objResponse->alert($sealNo);
		$chkSealNumExistSeal = $sealNumberObj->checkSealNumberExistSeal($sealNo, $cSOId);
		if ($chkSealNumExistSeal && $sealNo!="") {
			$objResponse->assign("status", "value", "Used");
			
		}
		else
		{
		$chkSealNumExist = $sealNumberObj->checkSealNumberExist($sealNo, $cSOId);
		
		if ($chkSealNumExist && $sealNo!="") {
			$objResponse->assign("status", "value", "Blocked");
			
		} else  {
			$objResponse->assign("status", "value", "Free");
			
		}
		}
		return $objResponse;
	}
	
	# Get all gate pass details based on procurement id
	function getReceiptDetails($procurement_id,$in_Seal = '')
	{
		$insealOptions = array( '' => 'Select');$selectedId = '';
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($procurement_id);
		$databaseConnect 		= new DatabaseConnect();
		$rmReceiptGatePassObj 	= new RMReceiptGatePass($databaseConnect);
		$records 			    = $rmReceiptGatePassObj->getFormFillData($procurement_id);
		$recordInseals  	    = $rmReceiptGatePassObj->getInsealData($procurement_id);
		$blockSeals = '';
		foreach($recordInseals as $res)
		{
			$insealOptions[$res['id']] = $res['seal_number'];
			$blockSeals.= '<tr id="block_seal_'.$res['id'].'">
					<td nowrap="" class="fieldName">
					<input type="checkbox" value="'.$res['id'].'" id="block_seal_val_'.$res['id'].'" name="block_seal[]">
					</td>
					<td height="10">
					'.$res['seal_number'].'
					</td>
				</tr>';
		}
		// $objResponse->alert($records[0]['vehicle_number']);
		$objResponse->assign("vehicle_Number", "value", $records[0]['vehicle_number']);
		$objResponse->assign("driver", "value", $records[0]['name_of_person']);
		$objResponse->assign("date_Of_Entry", "value", $records[0]['date_of_entry']);
		$objResponse->assign("labours", "innerHTML", $records[0]['labours']);
		$objResponse->assign("out_Seal", "value", $records[0]['seal_number']);
		$objResponse->assign("vehicle_id", "value", $records[0]['vehicle_id']);
		$objResponse->assign("driver_id", "value", $records[0]['driver_id']);
		$objResponse->assign("out_seal_id", "value", $records[0]['seal_out']);
		$objResponse->assign("procurment_Gate_PassId", "value", $procurement_id);
		
		addDropDownOptions("in_Seal", $insealOptions,$in_Seal,$objResponse);
		
		$contentBlockedSeal = '<table width="50%" cellspacing="0" cellpadding="0" border="0" align="center">
															<tbody>
																<tr>
																	<td height="10" colspan="2"></td>
																</tr>
																<input type="hidden" value="" name="hidunitTransferDataId">
																<tr>
																	<td nowrap="" class="fieldName" colspan="1">
																		<table width="200" align="center">
																			<tbody>
																				'.$blockSeals.'																				
																			</tbody>
																		</table>
																	</td>
																</tr>
																<tr>
																  <td colspan="2">&nbsp;</td>
																</tr>	
																<tr>
																	<td height="10" colspan="2"></td>
																</tr>
																<tr>
																	<td align="center" colspan="2">																	
																	<input onclick="unblockSeals();" type="button" value="Unblock" class="button" name="Unblock">												</td>
																</tr>
																<tr>
																	<td height="10" colspan="2"></td>
																</tr>
															</tbody>
														</table>';
		$objResponse->assign("blocked_seal_details", "innerHTML", $contentBlockedSeal);
		return $objResponse;
	}
	
	#get load content based on procurement available or not 
	function getLoadContent($avlStatus)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$rmReceiptGatePassObj 	= new RMReceiptGatePass($databaseConnect);
		$content = '';
		if($avlStatus == 1)
		{	
			$content = '<table width="200" align="center" id="display2">
						<tbody>
							<tr>
								<td nowrap="" class="fieldName">* Procurement Order:</td>
								<td height="10">
									<select onchange="xajax_getReceiptDetails(this.value);" name="procurment_Gate_PassId" id="procurment_Gate_PassId" required>
										<option value="">--Select--</option>';
			$procurementIDs = $rmReceiptGatePassObj->getAllProcurement();
										if(sizeof($procurementIDs) > 0)
										{
											foreach($procurementIDs as $proIDs)
											{
												$content.= '<option value="'.$proIDs['procurment_id'].'">'.$proIDs['gate_pass_id'].'</option>';
											}
										}

			$content.= '</select>
							</td>
								</tr>
																				<tr>
																					<td nowrap="" class="fieldName">* Vehicle No :</td>
																					<td height="10">
																						<input type="text" size="15" name="vehicle_Number" id="vehicle_Number" required />
																					</td>
																				</tr>
																				<tr>
																				   <td nowrap="" class="fieldName">* Driver Name:</td>
																				   <td><input type="text" value="" size="15" id="driver" name="driver" required /></td>
																				</tr>
																				<tr>
																					<td nowrap="" class="fieldName">*Date of Entry:&nbsp;</td>
																					<td><input type="text" value="" size="15" id="date_Of_Entry" name="date_Of_Entry" required /></td>
																				</tr>	  
																				<tr>
																					<td nowrap="" class="fieldName">* Labours :</td>
																					<td height="10">
																						<textarea size="15" name="labours" id="labours" required></textarea>
																					</td>
																				</tr>
																			</tbody>
																		</table>';
		}
		else if($avlStatus == 2)
		{

			$content = '<table width="200" align="center">
																			<tbody>
																				<tr>
																					<td nowrap="" class="fieldName">* Vehicle No :</td>
																					<td height="10">
																						<input type="text" size="15" name="vehicle_Number" id="vehicle_Number" required />
																					</td>
																				</tr>
																				<tr>
																				   <td nowrap="" class="fieldName">* Driver Name:</td>
																				   <td><input type="text" value="" size="15" id="driver" name="driver" required /></td>
																				</tr>
																				<tr>
																					<td nowrap="" class="fieldName">*Date of Entry:&nbsp;</td>
																					<td><input type="text" value="" size="15" id="date_Of_Entry" name="date_Of_Entry" required /></td>
																				</tr>	  
																				
																			</tbody>
																		</table>';
																		
		}
		$objResponse->assign("procurement_aval", "innerHTML", $content);
		return $objResponse;
	}
	
	function unblockseals($blockIds)
	{
		$objResponse 			= new xajaxResponse();
		// $objResponse->alert($blockIds);
		$databaseConnect 		= new DatabaseConnect();
		$rmReceiptGatePassObj 	= new RMReceiptGatePass($databaseConnect);
		$rmReceiptGatePassObj->freeSeals($blockIds);
		return $objResponse;
	}
	$xajax->register(XAJAX_FUNCTION,'vehicleNumber', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'labours', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'chksealNumberExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getLotId', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getReceiptDetails', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getLoadContent', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'unblockseals', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->ProcessRequest();
?>