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
	
	function checkValidReceiptNumber($gatepassnumber)
	{
		$currentDate=Date('Y-m-d');
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$rmReceiptGatePassObj 	= new RMReceiptGatePass($databaseConnect);
		//$objResponse->alert($gatepassnumber);
		$checkGateNumberSettingsExist=$rmReceiptGatePassObj->chkValidGatePassId($currentDate);
		if($checkGateNumberSettingsExist)
		{
		$alphaCode=$rmReceiptGatePassObj->getAlphaCodeGatePass();
		$alphaCodePrefix= $alphaCode[0];
		$getRecordval=explode($alphaCodePrefix,$gatepassnumber);
		$getRecord=$getRecordval[1];
			if($getRecord!=0 || $getRecord!='' )
			{
			$validendno=$rmReceiptGatePassObj->getValidendnoGatePassId($currentDate);
			$validstartno=$rmReceiptGatePassObj->getValidGatePassId($currentDate);
			//$objResponse->alert($nextGatePassId);
				if ($getRecord>$validendno){
				$GatePassMsg="Please set the Gate Pass number in Settings,since it reached the end no";
				$objResponse->assign("message","innerHTML",$GatePassMsg);
				}
				elseif ($getRecord<$validstartno)
				{
				$GatePassMsg="Gate Pass number is not in the range of settings";
				$objResponse->assign("message","innerHTML",$GatePassMsg);
				}
				else
				{
				$GatePassMsg="";
				$objResponse->assign("message","innerHTML",$GatePassMsg);
				}
			}
			else
			{
			$GatePassMsg="Alpha code doesnot match";
			$objResponse->assign("message","innerHTML",$GatePassMsg);
			}
		}
		return $objResponse;
	}
	
	function checkValidSerialNumber($sealnumber)
	{
		$currentDate=Date('Y-m-d');
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$rmReceiptGatePassObj 	= new RMReceiptGatePass($databaseConnect);
		//$objResponse->alert($sealnumber);
		$checkGateNumberSettingsExist=$rmReceiptGatePassObj->chkValidGatePassIdSeal($currentDate);
		if ($checkGateNumberSettingsExist)
		{
		$getValue=$rmReceiptGatePassObj->getValidGatePassIdSeal($currentDate);
		$startno=$getValue[0];
		//$objResponse->alert($sealnumber);
		//$getRecord=$sealnumber;
			if($sealnumber<$startno)
			{
			//$objResponse->alert("fii");
			$GatePassMsg="Seal no exceeds the limit ";
			$objResponse->assign("message","innerHTML",$GatePassMsg);
				
			}
			else
			{	$validendno=$rmReceiptGatePassObj->getValidendnoGatePassIdSeal($currentDate);
				if ($sealnumber>$validendno){
				$objResponse->alert("fii");
				$GatePassMsg="Please set the Gate Pass number in Settings,since it reached the end no";
				$objResponse->assign("message","innerHTML",$GatePassMsg);
				}
				else
				{
				
				$GatePassMsg="";
				$objResponse->assign("message","innerHTML",$GatePassMsg);
				}
			}
		}
		return $objResponse;
	}
	
	function generateReceiptGatePass()
	//function generateReceiptGatePass($selDate)
	{
		$selDate=Date('Y-m-d');
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$rmReceiptGatePassObj = new RMReceiptGatePass($databaseConnect);
		//$objResponse->alert("ddd");
		//$objResponse->alert(mysqlDateFormat($selDate));
		$checkGateNumberSettingsExist=$rmReceiptGatePassObj->chkValidGatePassId($selDate);
		if (sizeof($checkGateNumberSettingsExist)>0)
		{
			$alphaCode=$rmReceiptGatePassObj->getAlphaCodeGatePass();
			$alphaCodePrefix= $alphaCode[0];
			//$objResponse->alert($alphaCodePrefix);
			$numbergen=$checkGateNumberSettingsExist[0][0];
			//$objResponse->alert($alphaCodePrefix);
			$checkExist=$rmReceiptGatePassObj->checkGatePassDisplayExist();
			//$checkExist=$rmProcurmentOrderObj->checkGatePassDisplayExist($processType);
			if ($checkExist>0)
			{
				$getFirstRecord=$rmReceiptGatePassObj->getmaxGatePassId();
				$getFirstRec= $getFirstRecord[0];
				//$objResponse->alert($getFirstRec);
				if($getFirstRec=="0")
				{
					$validStartNo=$rmReceiptGatePassObj->getValidGatePassId($selDate);	
					$nextGatePassId=$validStartNo[0];
					//get first number
				}
				else
				{
					$getFirstRecEx=explode($alphaCodePrefix,$getFirstRec);
					//$objResponse->alert($getFirstRecEx[1]);
					$nextGatePassId=$getFirstRecEx[1]+1;
				}
				$validendno=$rmReceiptGatePassObj->getValidendnoGatePassId($selDate);
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
					$objResponse->assign("receiptGatePass","value","$disGateNo");	
					$objResponse->assign("number_gen_id","value","$numbergen");	
					$getSealAlpha=$rmReceiptGatePassObj->getSealNo();
				}
			}
			else
			{
				$validPassNo=$rmReceiptGatePassObj->getValidGatePassId($selDate);	
				$checkPassId=$rmReceiptGatePassObj->chkValidGatePassId($selDate);
				$disGatePassId="$alphaCodePrefix$validPassNo";
				$objResponse->assign("receiptGatePass","value","$disGatePassId");
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
		$veh=$records[0]['vehicle_number'];
		$vehicle = explode(",", $veh);
		$vehicleNumber=$vehicle[0];
		$veh_ID=$records[0]['vehicle_id'];
		$vehicleID = explode(",", $veh_ID);
		//$vehicleNumber=
		$vehicle_id=$vehicleID[0];
		// if($records[0]['name_of_person']>0)
		// {
			// if($driver=='')
			// {
				// $driver=$records[0]['name_of_person']
			// }
		// }
		//$objResponse->alert($records[0]['id']);
		$recordInseals  	    = $rmReceiptGatePassObj->getInsealData($records[0]['gatepass_id']);
		$alphachar=$recordInseals[0]['number_gen_id'];  
		//$objResponse->alert($alphachar);
		$alpha  	    = $rmReceiptGatePassObj->getalphachar($alphachar);
		foreach($recordInseals as $res) 
		{
			$insealOptions[$res['id']] = $res['alpha_code'].$res['in_seal'];
			if($res['accepted_status']!="1")
			{
				$blockSeals.= '<tr id="block_seal_'.$res['id'].'" class="allInSeal">
					<td nowrap="" class="listing-item"  bgcolor="White">
					<input type="checkbox" class="chkBox"  value="'.$res['id'].'" id="block_seal_val_'.$res['id'].'" name="block_seal[]">
					</td>
					<td height="10" class="listing-item"  bgcolor="White">
					'.$res['alpha_code'].$res['in_seal'].'
					</td>
				</tr>';
			}
		}
		
		// $objResponse->alert($records[0]['vehicle_number']);
		
		$contentBlockedSeal = '<fieldset>';
		$contentBlockedSeal.= '<table width="50%" cellspacing="1" cellpadding="2" border="0" bgcolor="#999999" align="center">
															<tbody>
																<tr bgcolor="#f2f2f2" class="listing-head">
																	
																	<td height="10" align="center">Seal #</td>
																</tr>
																
																<input type="hidden" value="" name="hidunitTransferDataId">
																<tr bgcolor="White">
																	<td nowrap="" class="fieldName" colspan="1">
																		<table  align="center" cellspacing="2">
																			<tbody>
																				'.$blockSeals.'																				
																			</tbody>
																		</table>
																	</td>
																</tr>
																
																
																<tr rowspan="2" height="34">
																	<td align="center" colspan="2" bgcolor="White">																	
																	<input onclick="unblockSeals();" type="button" value="Unblock" class="button" name="Unblock">												</td>
																</tr>
																
															</tbody>
														</table>';
			$contentBlockedSeal.= '</fieldset>';
		//$objResponse->alert($contentBlockedSeal);
		$objResponse->assign("blocked_seal_details", "innerHTML", $contentBlockedSeal);
		$objResponse->assign("vehicle_Number", "value", $vehicleNumber);
		$objResponse->assign("driver", "value",$records[0]['driver_name'] );
		$objResponse->assign("date_Of_Entry", "value", dateFormat($records[0]['date_of_entry']));
		$objResponse->assign("labours", "innerHTML", $records[0]['labours']);
		$objResponse->assign("out_Seal", "value", $records[0]['seal_number']);
		$objResponse->assign("vehicle_id", "value", $vehicle_id);
		$objResponse->assign("driver_id", "value", $records[0]['driver_id']);
		$objResponse->assign("out_seal_id", "value", $records[0]['seal_out']);
		$objResponse->assign("procurment_Gate_PassId", "value", $procurement_id);
		//$objResponse->assign("alphaCodeIn", "value",$alpha[0]['alpha_code']);
		//$objResponse->assign("alphaCodeOut", "value",$records[0]['alpha_code']);
		//$objResponse->assign("alphaCodeInDisp", "innerHTML",$alpha[0]['alpha_code']);
		$objResponse->assign("alphaCodeOutDisp", "innerHTML",$records[0]['alpha_code']);
		addDropDownOptions("in_Seal", $insealOptions,$in_Seal,$objResponse);
		$objResponse->script("displayDiv();");
		return $objResponse;
	}
	
		# Get all gate pass details based on procurement id
	function getSupplierDetails($procurement_id,$in_Seal = '')
	{
		$insealOptions = array( '' => 'Select');$selectedId = '';
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($procurement_id);
		$databaseConnect 		= new DatabaseConnect();
		$rmReceiptGatePassObj 	= new RMReceiptGatePass($databaseConnect);
		$manageusersObj			=	new ManageUsers($databaseConnect);
		$sessObj				=	new Session($databaseConnect);
		//$objResponse->alert($procurement_id);
		
		$editVal=$rmReceiptGatePassObj->getReceiptValid($procurement_id);
		if(sizeof($editVal)>0)
		{
			$supplierDetail 			    = $rmReceiptGatePassObj->getReceiptSupplierDetails($editVal[0]);
		}
		else
		{
			$supplierDetail 			    = $rmReceiptGatePassObj->getProcurementSupplierDetails($procurement_id);
			
		}
		
		$contentSupplier           ='<table id="newspaper-dce-rbt"  width="98%" border="0" cellpadding="4" cellspacing="2"><thead>
		<TR>
			<Th valign="center">
				<div style="height:100%; float: left; vertical-align:middle;"><img src="images/topLink.jpg" border="0" width="11" height="15" /></div>
				<div style="float: left; vertical-align:middle;">Supplier Detail</div>
			</Th>
		</TR>
		</thead>
		<tbody>
		<tr><TD align="center"  bgcolor="#ffffff" style="padding:8px 0px 18px 0px" >
		<table width="10%" cellspacing="1" bgcolor="#999999" cellpadding="6" id="tblAddProcurmentOrderSupplier" name="tblAddProcurmentOrderSupplier">
			<tr bgcolor="#ffffff" align="center" >
				<td class="listing-head" nowrap >Supplier name </td>
				<td class="listing-head" nowrap >Farm Name </td>
				<td class="listing-head" nowrap >Procurement center </td>
				<td class="listing-head" nowrap >Challan no </td> 
				<td class="listing-head" nowrap >Date</td>
				<td class="listing-head" nowrap >Alloted To Company  </td>
				<td class="listing-head" nowrap >Unit  </td>
			</tr>';
			if(sizeof($supplierDetail)>0)
			{
			$n=0;
				 foreach($supplierDetail as $supplierVal)
				{
					 
					 //$objResponse->alert($n);
						$contentSupplier.='<tr  align="center"  background-color="#ede6e6">';
							$supplierNm			    = $rmReceiptGatePassObj->getSupplierName($supplierVal[1]);
							$supplierName=$supplierNm[0];
							$pondNm			    = $rmReceiptGatePassObj->getPondName($supplierVal[2]);
							$pondName=$pondNm[0];
							$landNm		    = $rmReceiptGatePassObj->fetchLocationType($supplierVal[3]);
							$landingCenter=$landNm[0];
							
									$contentSupplier.='<td class="listing-item" nowrap>'.$supplierName.'
														<input type="hidden" value="'.$supplierVal[1].'" size="15" id="supplier_id_'.$n.'" name="supplier_id_'.$n.'" required />
														</td>
														<td class="listing-item" nowrap>'.$pondName.'
														<input type="hidden" value="'.$supplierVal[2].'" size="15" id="pond_id_'.$n.'" name="pond_id_'.$n.'" required /></td>';
									$contentSupplier.='<td class="listing-item" nowrap>'.$landingCenter.'<input type="hidden" value="'.$supplierVal[3].'" size="15" id="landing_center_'.$n.'" name="landing_center_'.$n.'" /></td>';
									$contentSupplier.='<td class="listing-item" nowrap><input type="text"  size="15" id="challan_no_'.$n.'" name="challan_no_'.$n.'" required  value="'.$supplierVal[4].'" onkeyup="chkChallanStatMul('.$n.');"/><br/><b style="color:red;" id="challan_stat_'.$n.'"></b></td>';
														if($supplierVal[5]!="")
														{
														$challan_date = dateFormat($supplierVal[5]);
														$Company_Name=$supplierVal[6];
														$unit=$supplierVal[7];
														
														}
														$contentSupplier.='<td class="listing-head" nowrap><input type="text" size="15" name="challan_date_'.$n.'" id="challan_date_'.$n.'" value="'.$challan_date.'" required /></td>
														
														<td class="listing-item" nowrap>
														<select id="Company_Name_'.$n.'" name="Company_Name_'.$n.'" required onchange=\'xajax_getUnitMultipleRow(this.value,"'.$n.'","");\'>
															<option value="">--select--</option>';	
															$userId		=	$sessObj->getValue("userId");
															list($companyNames,$unitRecords,$departmentRecords,$defaultCompany)=$manageusersObj->getUserReferenceSet($userId);
																if(sizeof($companyNames) > 0)
																{
																	foreach($companyNames as $cmpId=>$cmpNm)
																	{
																		$companyId=$cmpId;
																		$companyName=$cmpNm;
																		$sel = '';
																		if(($Company_Name == $companyId) ||($Company_Name =="" && $defaultCompany==$companyId))
																		$sel = 'selected';
																										
																		$contentSupplier.= '<option '.$sel.' value="'.$companyId.'">'.$companyName.'</option>';
																	}
																}
														$contentSupplier.='</select>';		
														$contentSupplier.='</td>
														<td class="listing-item" nowrap>
														<select id="unit_'.$n.'" name="unit_'.$n.'" required>
															<option value="">--select--</option>';
															($Company_Name!="")?$units=$unitRecords[$Company_Name]:$units=$unitRecords[$defaultCompany];
															//$units          = $unitRecords[$Company_Name];
											  				if(sizeof($units) > 0)
															{
																foreach($units as $untId=>$untNm)
																{
																	$unitId=$untId;
																	$unitName=$untNm;
																	$sel = '';
																	if($unit == $unitId) $sel = 'selected';
																	$contentSupplier.='<option '.$sel.' value="'.$unitId.'">'.$unitName.'</option>';
																}
															}
														$contentSupplier.='</select>	
														</td>
														<input type="hidden" name="receipt_'.$n.'" value="'.$supplierVal[0].'"  id="receipt_'.$n.'"/>
													</tr>';
												 $n++;
												 }
												
											}
										$cntval=sizeof($supplierDetail);
										$contentSupplier.='<input type="hidden" size="15" name="supplierSize" id="supplierSize" value="'.$cntval.'" required />';
										$contentSupplier.='</table></TD></tr></tbody></table>';
										
				
		/*$recordInseals  	    = $rmReceiptGatePassObj->getInsealData($procurement_id);
		$blockSeals = '';
		foreach($recordInseals as $res)
		{
			$insealOptions[$res['id']] = $res['seal_number'];
			$blockSeals.= '<tr id="block_seal_'.$res['id'].'" class="allInSeal">
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
		$objResponse->assign("date_Of_Entry", "value", dateFormat($records[0]['date_of_entry']));
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
														</table>';*/
		$objResponse->assign("supplier_display", "innerHTML", $contentSupplier);
		if(sizeof($supplierDetail)>0)
		{
		$cnt=sizeof($supplierDetail);
		for($i=0; $i<$cnt; $i++)
		{		
		$objResponse->script("displayCal('$i');");
		}
		}
		return $objResponse;
	}
	
	
	function getEquipmentDetails($procurement_id,$in_Seal = '')
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($procurement_id);
		$databaseConnect 		= new DatabaseConnect();
		$rmReceiptGatePassObj 	= new RMReceiptGatePass($databaseConnect);
		//$objResponse->alert($procurement_id);
		
		$equipmentDetail=$rmReceiptGatePassObj->getEquipmentValid($procurement_id);
		if(sizeof($equipmentDetail)>0)
		{
		$contentEquipment           ='<table id="newspaper-dce-rbt"  width="90%" border="0" cellpadding="4" cellspacing="2"><thead>
		<TR>
			<Th valign="center">
				<div style="height:100%; float: left; vertical-align:middle;"><img src="images/topLink.jpg" border="0" width="11" height="15" /></div>
				<div style="float: left; vertical-align:middle;">Equipment Detail</div>
			</Th>
		</TR>
		</thead>
		<tbody>
		<tr><TD align="center"  bgcolor="#ffffff" style="padding:8px 0px 20px 0px" >
			<table  cellspacing="1" bgcolor="#999999" cellpadding="6" id="tblAddProcurmentEquipment" 			name="tblAddProcurmentEquipment">
			<tr bgcolor="#ffffff" align="center">
				<td class="listing-head" nowrap>Equipment name </td>
				<td class="listing-head" nowrap>Issued Quantity </td>
				<td class="listing-head" nowrap>Returned Quantity </td> 
				<td class="listing-head" nowrap>Difference</td>
				<td class="listing-head" nowrap>Remarks </td>
			</tr>';
			if(sizeof($equipmentDetail)>0)
			{
				$n=0;
				foreach($equipmentDetail as $equipmentVal)
				{	if($equipmentVal[4]=='0')
					{ 
						$returnedQnty=""; 
						$differenceQnty="";
					}
					else 
					{ 
						$returnedQnty=$equipmentVal[4];
						$differenceQnty=$equipmentVal[5];
					}
					$contentEquipment.='<tr  align="center"  background-color="#ede6e6">';
					$contentEquipment.='<td class="listing-item" nowrap>'.$equipmentVal[3].'<input type="hidden" value="'.$equipmentVal[1].'" size="15" id="equipmentId_'.$n.'" name="equipmentId_'.$n.'" required />
					</td>
					<td class="listing-item" nowrap><input type="text" value="'.$equipmentVal[2].'" size="15" id="equipmentIssuedQuantity_'.$n.'" name="equipmentIssuedQuantity_'.$n.'" readonly  /></td>
					<td class="listing-item" nowrap><input type="text" size="15" name="equipmentReturnedQuantity_'.$n.'" id="equipmentReturnedQuantity_'.$n.'" value="'.$returnedQnty.'" onkeyup="calculateEquipDiff(this.value,'.$n.')" required /></td>';
					$contentEquipment.='<td class="listing-item" nowrap><input type="text" size="15" name="equipmentDifferenceQuantity_'.$n.'" id="equipmentDifferenceQuantity_'.$n.'" value="'.$differenceQnty.'" readonly /></td>';
					$contentEquipment.='<td class="listing-item" nowrap><textarea name="equipmentRemarks_'.$n.'" id="equipmentRemarks_'.$n.'" >'.$equipmentVal[6].'</textarea></td>
					<input type="hidden" name="procurementEquipmentId_'.$n.'" value="'.$equipmentVal[0].'"  id="procurementEquipmentId_'.$n.'"/>
				</tr>';
					 $n++;
					 }
				}
				$cntval=sizeof($equipmentDetail);
				$contentEquipment.='<input type="hidden" size="15" name="equipmentSize" id="equipmentSize" value="'.$cntval.'" required /> ';
				$contentEquipment.='</table></TD></tr></tbody></table>';
				$objResponse->assign("equipment_display", "innerHTML", $contentEquipment);
		}
		return $objResponse;
	}
	

	function getChemicalDetails($procurement_id,$in_Seal = '')
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($procurement_id);
		$databaseConnect 		= new DatabaseConnect();
		$rmReceiptGatePassObj 	= new RMReceiptGatePass($databaseConnect);
		//$objResponse->alert($procurement_id);
		
		$chemicalDetail=$rmReceiptGatePassObj->getChemicalValid($procurement_id);
		if(sizeof($chemicalDetail)>0)
		{
		$contentChemical           ='<table id="newspaper-dce-rbt" width="90%" border="0" cellpadding="4" cellspacing="2"><thead>
		<TR >
			<Th valign="center">
				<div style="height:100%; float: left; vertical-align:middle;"><img src="images/topLink.jpg" border="0" width="11" height="15" /></div>
				<div style="float: left; vertical-align:middle;">Chemical Detail</div>
			</Th>
		</TR>
		</thead>
		<tbody>
		<tr><TD align="center" bgcolor="#ffffff" style="padding:8px 0px 16px 0px" >
		<table width="10%" cellspacing="1" bgcolor="#99999" cellpadding="5" id="tblAddProcurmentChemical" name="tblAddProcurmentChemical">
			<tr bgcolor="#ffffff" align="center">
				<td class="listing-head" nowrap>Chemical name </td>
				<td class="listing-head" nowrap>Issued Quantity </td>
				<td class="listing-head" nowrap>Returned Quantity </td> 
				<td class="listing-head" nowrap>Used</td>
				<td class="listing-head" nowrap>Remarks </td>
			</tr>';
			if(sizeof($chemicalDetail)>0)
			{
				$n=0;
				foreach($chemicalDetail as $chemicalVal)
				{
					if($chemicalVal[4]=='0')
					{ 
						$returnedQnty=""; 
						$chemicalUsed="";
					}
					else 
					{ 
						$returnedQnty=$chemicalVal[4];
						$chemicalUsed=$chemicalVal[5];
					}
					
					$contentChemical.='<tr  align="center" background-color="#ede6e6">';
					$contentChemical.='<td class="listing-item" nowrap>'.$chemicalVal[3].'<input type="hidden" value="'.$chemicalVal[1].'" size="15" id="chemicalId_'.$n.'" name="chemicalId_'.$n.'" required />
					</td>
					<td class="listing-item" nowrap><input type="text" value="'.$chemicalVal[2].'" size="15" id="chemicalIssuedQuantity_'.$n.'" name="chemicalIssuedQuantity_'.$n.'" readonly /></td>
					<td class="listing-item" nowrap><input type="text" size="15" name="chemicalReturnedQuantity_'.$n.'" id="chemicalReturnedQuantity_'.$n.'" value="'.$returnedQnty.'" onkeyup="calculateChemicalDiff(this.value,'.$n.')" required /></td>';
					$contentChemical.='<td class="listing-item" nowrap><input type="text" size="15" name="chemicalDifferenceQuantity_'.$n.'" id="chemicalDifferenceQuantity_'.$n.'" value="'.$chemicalUsed.'" readonly /></td>';
					$contentChemical.='<td class="listing-item" nowrap><textarea name="chemicalRemarks_'.$n.'" id="chemicalRemarks_'.$n.'" >'.$chemicalVal[6].'</textarea></td>
					<input type="hidden" name="procurementChemicalId_'.$n.'" value="'.$chemicalVal[0].'"  id="procurementChemicalId_'.$n.'"/>
				</tr>';
					 $n++;
					 }
					
				}
				$cntval=sizeof($chemicalDetail);
				$contentChemical.='<input type="hidden" size="15" name="chemicalSize" id="chemicalSize" value="'.$cntval.'" required />';
				$contentChemical.='</table></TD></tr></tbody></table>';
				$objResponse->assign("chemical_display", "innerHTML", $contentChemical);
		}
		return $objResponse;
	}

	#get load content based on procurement available or not 
	function getLoadContent($avlStatus,$gate_pass_id)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$rmReceiptGatePassObj 	= new RMReceiptGatePass($databaseConnect);
		$manageusersObj			=	new ManageUsers($databaseConnect);
		$sessObj				=	new Session($databaseConnect);
		$content = '';
		if($avlStatus == 1)
		{	
			$content = '<table width="200" align="center" id="display2">
						<tbody>
							<tr>
								<td nowrap="" class="fieldName">* Procurement Order:</td>
								<td height="10">
									<select onchange="xajax_getReceiptDetails(this.value); xajax_getSupplierDetails(this.value); xajax_getEquipmentDetails(this.value); xajax_getChemicalDetails(this.value);" name="procurment_Gate_PassId" id="procurment_Gate_PassId" required>
										<option value="">--Select--</option>';
			$procurementIDs = $rmReceiptGatePassObj->getAllProcurement($gate_pass_id);
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
																						<input type="text" size="15" name="vehicle_Number" id="vehicle_Number" required readonly="readonly" style="border:none;"/>
																					</td>
																				</tr>
																				<tr>
																				   <td nowrap="" class="fieldName">* Driver Name:</td>
																				   <td>
																				   <textarea  size="15" name="driver" id="driver" required style="border:none;"> </textarea>
																				  
																				  </td>
																				</tr>
																					  
																				<tr>
																					<td nowrap="" class="fieldName">* Labours :</td>
																					<td height="10">
																						<textarea size="15" name="labours" id="labours" required style="border:none;"></textarea>
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
																				   <td><textarea id="driver" name="driver" required></textarea></td>
																				</tr>
																				<tr>
																					<td nowrap="" class="fieldName">*Supplier Chalan no:</td>
																					<td height="10">
																						<input type="text" size="15" value="" id="supplier_Challan_No" name="supplier_Challan_No" onkeyup="chkChallanStat();" required>
																					</td>
																				</tr>
																				<tr>
																					<td colspan="2"><b id="challan_stat" style="color:red; font-size:11px;"></b></td>
																				</tr>
																				<tr>
																				   <td nowrap="" class="fieldName">*Chalan Date:</td>
																					<td height="10">
																						<input type="text" size="15" value="" id="supplier_Challan_Date" name="supplier_Challan_Date" required>
																					</td>
																				</tr>
																				<tr>				
																					<td nowrap="" class="fieldName">*Raw material type</td>
																					<td height="5">
																						<select id="material" name="material" required onchange="getField();">
																							<option value="">--select--</option>';
											  											    $materialType   = $rmReceiptGatePassObj->getAllMaterialType();
				
																								if(sizeof($materialType) > 0)
																								{
																									foreach($materialType as $materialTypes)
																									{
																										$selv = '';
																										if($material == $materialTypes[0]) $selv = 'selected';
																										
																										$content.= '<option '.$selv.' value="'.$materialTypes[0].'">'.$materialTypes[1].'</option>';
																									}
																								}
																							
											  										     $content.= '</select>										      
																					</td>
																				</tr>
																				<tr>				
																					<td nowrap="" class="fieldName">*Supplier</td>
																					<td height="5">
																						<select id="supplier" name="supplier" required onchange=\'xajax_getCenter(document.getElementById("supplier").value,document.getElementById("material").value); chkChallanStat();\'>
																							<option value="">--select--</option>';
																							$suppliers= $rmReceiptGatePassObj->getAllSupplier();
											  											    if(sizeof($suppliers) > 0)
																								{
																									foreach($suppliers as $supplier)
																									{
																										$sels = '';
																										if($suplier == $supplier[0]) $sels = 'selected';
																										
																										$content.= '<option '.$sels.' value="'.$supplier[0].'">'.$supplier[1].'</option>';
																									}
																								}
																							
											  										     $content.= '</select>										      
																					</td>
																				</tr>
																				<tr id="1" class="rawtype" style="display:none">				
																					<td nowrap="" class="fieldName">*Farm Name</td>
																					<td height="5">
																						<select id="pond" name="pond" >
																							<option value="">--select--</option>';
																							foreach($pondRecs as $pnd)
																							{
																									
																									//alert($sr[0]);
																								$pondNameId		=	$pnd[1];
																								$pondNameValue	=	stripSlash($pnd[2]);
																								$sel  = ($selPondId==$pondNameId)?"Selected":"";
																							
																							$content.='<option value="'.$pondNameId.'" '.$sel.'>'.$pondNameValue.'</option>';
																							
																							}
																							
																						$content.= '</select>										      
																					</td>
																				</tr>
																				<tr id="2" class="rawtype" style="display:none">				
																					<td nowrap="" class="fieldName">*Landing Center</td>
																					<td height="5">
																						<select id="landingCenter" name="landingCenter" >
																							<option value="">--select--</option>
																							
																						</select>										      
																					</td>
																				</tr>
																				<tr>
																					<td nowrap="" class="fieldName">*Company Name:&nbsp;</td>
																					<td height="5">
																						<select id="Company_Name" name="Company_Name" required onchange=\'xajax_getUnit(this.value,"","");\'>
																							<option value="">--select--</option>';	
																							$userId		=	$sessObj->getValue("userId");
																							list($companyNames,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);
																							if(sizeof($companyNames) > 0)
																								{
																									foreach($companyNames as $cmpId=>$cmpNm)
																									{
																										$companyId=$cmpId;
																										$companyName=$cmpNm;

																										$sel = '';
																										if($Company_Name == $companyName)
																										$sel = 'selected';
																										
																										$content.= '<option '.$sel.' value="'.$companyId.'">'.$companyName.'</option>';
																									}
																								}
																							/*$companyNames   = $rmReceiptGatePassObj->getAllCompany();
																								if(sizeof($companyNames) > 0)
																								{
																									foreach($companyNames as $companyName)
																									{
																										$sel = '';
																										if($Company_Name == $companyName['id'])
																										$sel = 'selected';
																										
																										$content.= '<option '.$sel.' value="'.$companyName['id'].'">'.$companyName['name'].'</option>';
																									}
																								}*/
																							
											  										    $content.= '</select>										      
																					</td>
																				</tr>
																				<tr>
																					<td nowrap="" class="fieldName">*Unit:&nbsp;</td>
																					<td height="5">
																						<select id="unit" name="unit" required>
																							<option value="">--select--</option>';
											  											   /* $units          = $rmReceiptGatePassObj->getAllUnit();
																								if(sizeof($units) > 0)
																								{
																									foreach($units as $unitval)
																									{
																										$sel = '';
																										if($unit == $unitval['id'])
																										$sel = 'selected';
																										
																										$content.='<option '.$sel.' value="'.$unitval['id'].'">'.$unitval['name'].'</option>';
																									}
																								}
																							*/
											  										    $content.= '</select>										      
																					</td>
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
		//$objResponse->alert($blockIds);
		$objResponse->script("xajax_sealStatus('$blockIds');");
	
		//$objResponse->alert($sizeBlock);
		return $objResponse;
	}
	function sealStatus($blockIds)
	{
		$receiptSeal='';
		$objResponse 			= new xajaxResponse();
		 //$objResponse->alert($blockIds);
		$databaseConnect 		= new DatabaseConnect();
		$manageSealObj 	= new ManageSeal($databaseConnect);
		$sessObj= new Session($databaseConnect);
		$blockVal= explode( ',', $blockIds ) ;
		$sizeBlock=sizeof($blockVal);
			for($i=0;$i<$sizeBlock;$i++)
			{
			$userId		=	$sessObj->getValue("userId");
			$seal_id=$blockVal[$i];
			$sealDet=$manageSealObj->getSealDetail($seal_id);
			$alpha	=$manageSealObj->getAlphaPrefix($sealDet[3]);
			$alphacode=$alpha[0];
		 	$sealnumber=$sealDet[2];
		
			$rm_gate_pass_id=$sealDet[1];
			$seal_status='';
			$status="Free";
			$sealRecConfirm = $manageSealObj->insertReleaseSeal($alphacode,$sealnumber,$seal_id,$rm_gate_pass_id,$seal_status,$userId,$status);
				//$objResponse->alert($seal_id);
			}
		
		//$objResponse->alert($sizeBlock);
		return $objResponse;
	}


	function getCenter($supplierId,$material,$sel)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($material);
		$databaseConnect 		= new DatabaseConnect();
		$rmReceiptGatePassObj 	= new RMReceiptGatePass($databaseConnect);
		if($material=='1')
		{
			$rmReceiptPondRecs 			= $rmReceiptGatePassObj->getfilterPondList($supplierId);
			if (sizeof($rmReceiptPondRecs)>0) addDropDownOptions("pond", $rmReceiptPondRecs, $sel, $objResponse);
		}
		else if($material=='2')
		{
			$rmReceiptLandRecs 			= $rmReceiptGatePassObj->getLandingCenterSupplier($supplierId);
			if (sizeof($rmReceiptLandRecs)>0) addDropDownOptions("landingCenter", $rmReceiptLandRecs, $sel, $objResponse);
		}
		return $objResponse;
	}

	function getUnit($companyId,$row,$cel)
	{
		$objResponse 	= new NxajaxResponse();	
		$databaseConnect= new DatabaseConnect();
		$manageusersObj			=	new ManageUsers($databaseConnect);
		$sessObj				=	new Session($databaseConnect);
		$userId		=	$sessObj->getValue("userId");
		list($companyRecords,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);
		$unit=$unitRecords[$companyId];
		$unit = array('0' => '--Select--') + $unit;
		$objResponse->addDropDownOptions("unit",$unit,$cel);
		return $objResponse;	
	}

	function getUnitMultipleRow($companyId,$row,$cel)
	{
		$objResponse 	= new NxajaxResponse();	
		$databaseConnect= new DatabaseConnect();
		$manageusersObj			=	new ManageUsers($databaseConnect);
		$sessObj				=	new Session($databaseConnect);
		$userId		=	$sessObj->getValue("userId");
		list($companyRecords,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);
		$unit=$unitRecords[$companyId];
		$unit = array('0' => '--Select--') + $unit;
		$objResponse->addDropDownOptions("unit_$row",$unit,$cel);
		return $objResponse;	
	}
	
	function checkReceiptGatePass($gatePass)
	{
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$rmReceiptGatePassObj 	= new RMReceiptGatePass($databaseConnect);
		//$str = 'GTP123';
		$numbers = preg_replace('/[^0-9]/', '', $gatePass);
		$letters = preg_replace('/[^a-zA-Z]/', '', $gatePass);
		if($numbers!="" && $letters!="")
		{
			$chkDuplicate=$rmReceiptGatePassObj->chkDuplicate($gatePass);
			if(!$chkDuplicate)
			{
				$checkStatus=$rmReceiptGatePassObj->checkProcurementStatus($numbers,$letters);
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
				$GatePassMsg="Duplicate entry of procurement order";
				$objResponse->assign("message","innerHTML",$GatePassMsg);
				$objResponse->assign("selCompanyName", "value","");
				$objResponse->assign("unitId", "value","");
				$objResponse->assign("number_gen_id", "value","");
				$objResponse->script("disableButton();");
			}
		}		
		return $objResponse;
	}
	
	
	$xajax->register(XAJAX_FUNCTION, 'checkReceiptGatePass', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getUnitMultipleRow', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getUnit', array('onResponseDelay' => 'showFnLoading','onComplete' => 'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getCenter', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getChemicalDetails', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getEquipmentDetails', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'sealStatus', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'checkValidSerialNumber', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'checkValidReceiptNumber', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getSupplierDetails', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'generateReceiptGatePass', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'vehicleNumber', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'labours', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'chksealNumberExist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getLotId', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getReceiptDetails', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getLoadContent', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'unblockseals', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->ProcessRequest();
?>