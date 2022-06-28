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
		
	function getRMLotIDResult($selDate,$rmLotID)
	{
		//$sel = ''; 
		//$date=mysqlDateFormat($selDate);
		$result='';
		$objResponse 			= new xajaxResponse();
		$databaseConnect	   = new DatabaseConnect();
		$objManageRMLOTID      = new ManageRMLOTID($databaseConnect);
		//$objResponse->alert($rmLotOD);
		$objResponse->script("xajax_getNewRMlotId()");
		$disMsgInactive="This record is inactive";
		$totalResultVal = $objManageRMLOTID->getLotIdTotalvalue($rmLotID);
		$processingStage= $objManageRMLOTID->getRMProgressStage($rmLotID);
		//print_r($totalResultVal);
		//$totalResult = $objManageRMLOTID->getLotIdTotalval($date,$rmLotOD);		
		//$totalval = $objManageRMLOTID->getLotIdTotalval($date,$rmLotOD);
		//$totalResult = $objManageRMLOTID->getLotIdTotalDetailsValue($totalval[2]);
			if(sizeof($totalResultVal) > 0)
			{
			$result= '<table width="88%" border="0" align="center" cellspacing="1" cellpadding="2">
					<tr>
						<td>
							<table width="94%" border="0" bgcolor="#999999" align="center" cellspacing="1" cellpadding="2">
																						<thead><tr bgcolor="#f2f2f2">																				
							<td style="padding-left:10px; padding-right:10px;" class="listing-head">RM Lot ID</td>
							<td style="padding-left:10px; padding-right:10px;" class="listing-head">Weightment Challan Number</td>
							<td style="padding-left:10px; padding-right:10px;" class="listing-head">Current Unit</td>
							<td style="padding-left:10px; padding-right:10px;" class="listing-head">Current Processing Stage</td>
							<td style="padding-left:10px; padding-right:10px;" class="listing-head">Status</td>
							<td class="listing-head"> Action </td>
							</tr></thead>
							<tbody >';
							
							$i = 0;
							$style = 'style="padding-left:10px; padding-right:10px;" ';
							foreach($totalResultVal as $res)
							{
								$status = '';$edit = '<a href="javascript:void(0);" onclick="xajax_chageStatusRmLotID('.$res[0][0].');"> Confirm </a>';
								if($res[0][7] == 1) { $status = 'Confirm'; $edit = '&nbsp;'; }
								
								if($i == 0)
								{
									
								 

									//$result = '<tr bgcolor="WHITE" >';
									$result.= '<tr bgcolor="WHITE"';
									if ($res[0][7] == 0) {
										$result.= 'bgcolor="WHITE"  onMouseOver="ShowTip('.$disMsgInactive.');" onMouseOut="UnTip();"';
									}
									$result.= '>';
									$result.= '<td class="listing-item" '.$style.'>'.$res[0][1].$res[0][2].'</td>';
									$result.= '<td class="listing-item" '.$style.'>'.$res[0][11].'</td>';
									/*$result.= '<td '.$style.'>'.$res[3].'</td>';
									$result.= '<td '.$style.'>'.$res[4].'</td>';*/
									$result.= '<td class="listing-item" '.$style.'>'.$res[0][6].'</td>';
									$result.= '<td class="listing-item"  '.$style.'>'.$processingStage.'</td>';
									$result.= '<td class="listing-item"  '.$style.'>'.$status.'</td>';
									$result.= '<td class="listing-item" '.$style.'>'.$edit.'</td>';
									$result.= '</tr>';
								}
								else
								{
									//$result.= '<tr bgcolor="WHITE">';
									$result.= '<tr bgcolor="WHITE"';
									if ($res[0][7] == 0) {
										$result.= 'bgcolor="WHITE"  onMouseOver="ShowTip('.$disMsgInactive.');" onMouseOut="UnTip();"';
									}
									$result.= '>';
									$result.= '<td class="listing-item" '.$style.'>'.$res[0][1].$res[0][2].'</td>';
									$result.= '<td class="listing-item" '.$style.'>'.$res[0][11].'</td>';
									/*$result.= '<td '.$style.'>'.$res[3].'</td>';
									$result.= '<td '.$style.'>'.$res[4].'</td>';*/
									$result.= '<td class="listing-item" '.$style.'>'.$res[0][6].'</td>';
									$result.= '<td class="listing-item" '.$style.'>'.$processingStage.'</td>';
									$result.= '<td class="listing-item" '.$style.'>'.$status.'</td>';
									$result.= '<td class="listing-item" '.$style.'>'.$edit.'</td>';
									$result.= '</tr>';
								}
								$i++;
							}
							
							$result.= '</tbody>
								</table>
				
				</td>
				<td bgcolor="WHITE"  width="10%"></td>
				<td bgcolor="WHITE" valign="top" >
					<table  width="100%" border="0" bgcolor="#999999" align="center" cellspacing="1" cellpadding="2">
						<tr bgcolor="#f2f2f2">
																					<td nowrap="" class="fieldName">*Company Name:&nbsp;</td>
																					<td height="5">
																						<select id="Company_Name" name="Company_Name" required>
																							<option value="">--select--</option>';																							
																							$companyNames   = $objManageRMLOTID->getAllCompany();
																								if(sizeof($companyNames) > 0)
																								{
																									foreach($companyNames as $companyName)
																									{
																										$sel = '';
																										if($Company_Name == $companyName['id'])
																										$sel = 'selected';
																										
																										$result.= '<option '.$sel.' value="'.$companyName['id'].'">'.$companyName['name'].'</option>';
																									}
																								}
																							
											  										    $result.= '</select>										      
																					</td>
																				</tr>
																				<tr bgcolor="#f2f2f2">
																					<td nowrap="" class="fieldName">*Unit:&nbsp;</td>
																					<td height="5">
																						<select id="unit" name="unit" required onchange="UnitAlreadyTransfer()">
																							<option value="">--select--</option>';
											  											    $units          = $objManageRMLOTID->getAllUnit();
																								if(sizeof($units) > 0)
																								{
																									foreach($units as $unitval)
																									{
																										$selt = '';
																										if($unit == $unitval['id'])
																										$selt = 'selected';
																										
																										$result.='<option '.$selt.' value="'.$unitval['id'].'">'.$unitval['name'].'</option>';
																									}
																								}
																							
											  										    $result.= '</select>										      
																					</td>
																				</tr>
																				<tr bgcolor="#f2f2f2">
																					<td nowrap="" class="fieldName">*Generate new lotId:&nbsp;</td>
																					<td nowrap=""><input type="text" name="alphavalue" id="alphavalue" readonly="readonly" size="2" value=""/>
																					<input type="text" name="generateNewLotId" id="generateNewLotId" size="8" readonly="readonly"/>
																					<input type="hidden" name="number_gen" id="number_gen" size="15" value=""/>
																					<input type="hidden" name="company_old_id" id="company_old_id" size="15" value="'.$res[8].'"/>
																					<input type="hidden" name="unit_old_id" id="unit_old_id" size="15" value="'.$res[9].'"/>
																					<input type="hidden" name="alreadyExist" id="alreadyExist" value=""/>
																					</td>
																				</tr>
																				<tr bgcolor="WHITE"  align="center">
																					<td colspan="2" >
																					<input type="submit" class="button" name="addUnit" id="addUnit" value="Add New Unit" onclick="return CheckUniqueUnit();"/></td>
																					
																				</tr>
					</table>
				
				</td>
			</tr>
			<tr><td >&nbsp;</td></tr>
			<tr><td colspan="3" align="center"><input class="button" type="submit" onclick=\'return cancel("ManageRMLOTID.php");\' value=" Cancel " name="cmdCancel"></td></tr>
			<tr><td >&nbsp;</td></tr>
			</table>';
			
			
			
			
			
		}



		
		$objResponse->assign("lotIdList", "innerHTML", $result);
		//$objResponse->alert($totalResult);
		return $objResponse;
	} 
	
	function chageStatusRmLotID($id)
	{
		$objResponse 			= new xajaxResponse();
		
		$databaseConnect	   = new DatabaseConnect();
		$objManageRMLOTID      = new ManageRMLOTID($databaseConnect);
		$objManageRMLOTID->changeStatus($id);
		
		$objResponse->script("xajax_getRMLotIDResult(document.getElementById('select_date').value,document.getElementById('rm_lot_id').value)");	
		//$objResponse->script("xajax_getRMLotIDS(document.getElementById('select_date').value)");	
		return $objResponse;
	}
	function getRMlotId($receiptID,$cnt,$procurementAvailable)
	{
	
		$selDate=Date('Y-m-d'); $supplyDetail=''; $supplyDetailId=''; $receiptIdVal='';  $companyIdVal='';  $unitIdVal=''; $farmIdVal='';
		$objResponse 			= new xajaxResponse();
		
		$databaseConnect	   = new DatabaseConnect();
		$objManageRMLOTID      = new ManageRMLOTID($databaseConnect);
		//$objResponse->alert("hii");
		//$objResponse->alert($receiptID);
		
		//
		//if(sizeof($Details)>0)
		if($procurementAvailable=="1")
		{
		$Details=$objManageRMLOTID->receiptGatePassDetail($receiptID);
		$receipt_gatepassVal=$objManageRMLOTID->receiptGatePassIDFind($receiptID);
		
			foreach($Details as $detVal)
			{
				if($supplyDetail=='')
					{
						$supplyDetail=$detVal[0];
						$supplyDetailId=$detVal[3];
						$receiptIdVal=$detVal[4];
						$companyIdVal=$detVal[5];
						$unitIdVal=$detVal[6];
						$farmIdVal=$detVal[7];
						$supplierChellanDt=$detVal[8];
						$supplierChellan=$detVal[9];
						$receiptGatePassID=$receipt_gatepassVal[0];
					}
					else
					{
						$supplyDetail.=','.$detVal[0];
						$supplyDetailId.=','.$detVal[3];
						$receiptIdVal.=','.$detVal[4];
						$companyIdVal=$detVal[5];
						$unitIdVal=$detVal[6];
						$farmIdVal.=','.$detVal[7];
						$supplierChellanDt.=','.$detVal[8];
						$supplierChellan.=','.$detVal[9];
						$receiptGatePassID.=','.$receipt_gatepassVal[0];
					}
					
					$supplyVal="Supplier Name:-".$supplyDetail.'<br/> '."Company Name:-".$detVal[1].'<br/> '."Unit:-".$detVal[2];
			}
		}
		else
		{
		$Details=$objManageRMLOTID->receiptGatePassDetailSingle($receiptID);
		
		foreach($Details as $detVal)
			{
		$supplyDetail=$detVal[0];
						$supplyDetailId=$detVal[3];
						$receiptIdVal=0;
						//$receiptIdVal=$detVal[4];
						$companyIdVal=$detVal[5];
						$unitIdVal=$detVal[6];
						$supplierChellanDt=$detVal[7];
						$supplierChellan=$detVal[8];
						$farmIdVal='';
						$receiptGatePassID=$receiptID;
						}
						$supplyVal="Supplier Name:-".$supplyDetail.'<br/> '."Company Name:-".$detVal[1].'<br/> '."Unit:-".$detVal[2];
		}
		//$objResponse->alert($receiptGatePassID);
		//$objResponse->alert($sz);
		$checkGateNumberSettingsExist=$objManageRMLOTID->chkValidGatePassId($selDate);
		 if (sizeof($checkGateNumberSettingsExist)>0){
		 $alphaCode=$objManageRMLOTID->getAlphaCode($selDate);
		 $alphaCodePrefix= $alphaCode[0];
		//$objResponse->alert("HII");
		//$objResponse->alert($alphaCodePrefix);
	//}	
		
		$checkExist=$objManageRMLOTID->getAvailableLotIdNos();
		 //$objResponse->alert($alphaCodePrefix);
		if ($checkExist>0){
		$nextGatePassId=$checkExist[0];
		//$objResponse->alert($nextGatePassId);
		/*$getFirstRecord=$objManageRMLOTID->getAvailableLotIdNos();
		$nextGatePassId=$getFirstRecord[0];
		$getFirstRecord=$objManageRMLOTID->getmaxGatePassId();
		$getFirstRec= $getFirstRecord[0];
		//$objResponse->alert($getFirstRec);
		$getFirstRecEx=explode($alphaCodePrefix,$getFirstRec);
		//$objResponse->alert($getFirstRecEx[1]);
		$nextGatePassId=$getFirstRecEx[1]+1;*/
		//$objResponse->alert($nextGatePassId);
		$validendno=$objManageRMLOTID->getValidendnoGatePassId($selDate);	
		if ($nextGatePassId>$validendno){
		$GatePassMsg="Please set the Gate Pass number in Settings,since it reached the end no";
		$objResponse->assign("message","innerHTML",$GatePassMsg);
		}
		else{
		$numbergen=$checkGateNumberSettingsExist[0][0];
		$disGateNo="$alphaCodePrefix$nextGatePassId";
		$tempStore=$objManageRMLOTID->addLotIdTemporary($nextGatePassId,$checkGateNumberSettingsExist[0][0]);
		$disGatePassIds='<br/>'."RMlotId:-"."$disGateNo";
		$validPassNoVals="$supplyVal.$disGatePassIds";
		$objResponse->assign("display_lotId_$cnt","innerHTML","$validPassNoVals");
		$objResponse->assign("alphaValue_$cnt","value","$alphaCodePrefix");
		$objResponse->assign("rmId_$cnt","value","$nextGatePassId");
		$objResponse->assign("supplyDetail_$cnt","value","$supplyDetailId");
		//$objResponse->alert($receiptIdVal);
		$objResponse->assign("receipt_idval_$cnt","value","$receiptIdVal");
		$objResponse->assign("company_idval_$cnt","value","$companyIdVal");
		$objResponse->assign("unit_idval_$cnt","value","$unitIdVal");
		$objResponse->assign("farmIdVal_$cnt","value","$farmIdVal");
		$objResponse->assign("number_genval_$cnt","value","$numbergen");
		$objResponse->assign("receiptGatePass_$cnt","value","$receiptGatePassID");
		$objResponse->assign("supplierChellanDate_$cnt","value","$supplierChellanDt");
		$objResponse->assign("supplierChellan_$cnt","value","$supplierChellan");
		}
		
		}
		else{
		$numbergen=$checkGateNumberSettingsExist[0][0];
		$validPassNo=$objManageRMLOTID->getValidGatePassId($selDate);	
		$lotVal="$alphaCodePrefix$validPassNo";
		$checkPassId=$objManageRMLOTID->chkValidGatePassId($selDate);
		$tempStore=$objManageRMLOTID->addLotIdTemporary($validPassNo,$checkGateNumberSettingsExist[0][0]);
		$disGatePassId='<br/>'."RMlotId:-"."$lotVal";
		$validPassNoVal="$supplyVal.$disGatePassId";
		$objResponse->assign("display_lotId_$cnt","innerHTML","$validPassNoVal");
		$objResponse->assign("alphaValue_$cnt","value","$alphaCodePrefix");
		$objResponse->assign("rmId_$cnt","value","$validPassNo");
		$objResponse->assign("supplyDetail_$cnt","value","$supplyDetailId");
		//$objResponse->alert($receiptIdVal);
		$objResponse->assign("receipt_idval_$cnt","value","$receiptIdVal");
		$objResponse->assign("company_idval_$cnt","value","$companyIdVal");
		$objResponse->assign("unit_idval_$cnt","value","$unitIdVal");
		$objResponse->assign("farmIdVal_$cnt","value","$farmIdVal");
		$objResponse->assign("number_genval_$cnt","value","$numbergen");
		$objResponse->assign("receiptGatePass_$cnt","value","$receiptGatePassID");
		$objResponse->assign("supplierChellanDate_$cnt","value","$supplierChellanDt");
		$objResponse->assign("supplierChellan_$cnt","value","$supplierChellan");
		
		}
		
		}
		else{
		//$objResponse->alert("hi");
		$GatePassMsg="Please set the gate pass in Settings";
		$objResponse->assign("message","innerHTML",$GatePassMsg);
		}
	
		return $objResponse;
	}
	
	
	
	function getNewRMlotId()
	{
	
		$selDate=Date('Y-m-d');
		$objResponse 			= new xajaxResponse();
		
		$databaseConnect	   = new DatabaseConnect();
		$objManageRMLOTID      = new ManageRMLOTID($databaseConnect);
		//$objResponse->alert('ff');
		//$objResponse->alert($receiptID);
		//$Details=$objManageRMLOTID->receiptGatePassDetail($receiptID);
		$checkGateNumberSettingsExist=$objManageRMLOTID->chkValidGatePassId($selDate);
		 if (sizeof($checkGateNumberSettingsExist)>0){
		 $alphaCode=$objManageRMLOTID->getAlphaCode($selDate);
		 $alphaCodePrefix= $alphaCode[0];
		$checkExist=$objManageRMLOTID->getAvailableLotIdNos();
		if ($checkExist>0){
		$nextGatePassId=$checkExist[0];
		$validendno=$objManageRMLOTID->getValidendnoGatePassId($selDate);	
		if ($nextGatePassId>$validendno){
		$GatePassMsg="Please set the Gate Pass number in Settings,since it reached the end no";
		$objResponse->assign("message","innerHTML",$GatePassMsg);
		}
		else{
		$numbergen=$checkGateNumberSettingsExist[0][0];
		$disGateNo="$alphaCodePrefix$nextGatePassId";
		$tempStore=$objManageRMLOTID->addLotIdTemporary($nextGatePassId,$checkGateNumberSettingsExist[0][0]);
	
		$objResponse->assign("alphavalue","value","$alphaCodePrefix");
		$objResponse->assign("generateNewLotId","value","$nextGatePassId");
		$objResponse->assign("number_gen","value","$numbergen");
		
		}
		
		}
		else{
		$numbergen=$checkGateNumberSettingsExist[0][0];
		$validPassNo=$objManageRMLOTID->getValidGatePassId($selDate);	
		$lotVal="$alphaCodePrefix$validPassNo";
		$checkPassId=$objManageRMLOTID->chkValidGatePassId($selDate);
		$tempStore=$objManageRMLOTID->addLotIdTemporary($validPassNo,$checkGateNumberSettingsExist[0][0]);
		$objResponse->assign("alphavalue","value","$alphaCodePrefix");
		$objResponse->assign("generateNewLotId","value","$validPassNo");
		$objResponse->assign("number_gen","value","$numbergen");
		}
		
		}
		else{
		//$objResponse->alert("hi");
		$GatePassMsg="Please set the gate pass in Settings";
		$objResponse->assign("message","innerHTML",$GatePassMsg);
		}
	
		return $objResponse;
	}
	
	
	
	
	
	function saveChange($Checkcount,$sizeSuplr)
	{
	$objResponse 			= new xajaxResponse();
		
		$databaseConnect	   = new DatabaseConnect();
		$objManageRMLOTID      = new ManageRMLOTID($databaseConnect);
		if($Checkcount == $sizeSuplr)
		{
		$button='<input type="submit" name="save_rmlotid" id="save_rmlotid" value="Save Changes"  class="button"/>';
		$objResponse->assign("save_button","innerHTML",$button);
		}
		if($Checkcount >0)
		{
		$reset_button='<input type="button" name="reset_rmlotid" id="reset_rmlotid" value="Reset" onclick="ReloadPage();" class="button"/>';
		$objResponse->assign("reset_button","innerHTML",$reset_button);
		}
		
		
		return $objResponse;
	}
	function unitexist($rm_lot_id)
	{
		$objResponse 			= new xajaxResponse();
		
		$databaseConnect	   = new DatabaseConnect();
		$objManageRMLOTID      = new ManageRMLOTID($databaseConnect);
		//$objResponse->alert("hii");
		$checkexist=$objManageRMLOTID->unitTransfered($rm_lot_id);
		if(sizeof($checkexist)>0)
		{
		//$mes="unit already transfered";
		$objResponse->assign("alreadyExist","value",1);
		}
		
		
		return $objResponse;
	}
	
	$xajax->register(XAJAX_FUNCTION, 'unitexist', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getNewRMlotId', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'saveChange', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getRMlotId', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getRMLotIDS', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'getRMLotIDResult', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION, 'chageStatusRmLotID', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->ProcessRequest();