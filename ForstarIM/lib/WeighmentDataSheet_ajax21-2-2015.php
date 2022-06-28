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
		// $objResponse->alert(sizeof($pondsRecs));
		//if (sizeof($supplierAddressRecs)>0) addDropDownOptions("supplierAddress", $supplierAddressRecs, $selSupplierNameId, $objResponse);
		if (sizeof($pondsRecs)>0) addDropDownOptions("pondName_$inputId", $pondsRecs, $supplierId, $objResponse);
		
		if(sizeof($pondsRecs) == 1)
		{
			$speciesRecs 			= $objWeighmentDataSheet->filterSpecies(0);
			if (sizeof($speciesRecs)>0) addDropDownOptions("product_species_$inputId", $speciesRecs, $pondId, $objResponse);
		}
		return $objResponse;
	}
	function weightmentSpecies($pondNameId,$inputId,$pondId)
	{
		
		$objResponse 			= new xajaxResponse();
		$databaseConnect 		= new DatabaseConnect();
		$objWeighmentDataSheet 	= new WeighmentDataSheet($databaseConnect);
		$speciesRecs 			= $objWeighmentDataSheet->filterSpecies($pondNameId);
		//$inputData = ( $data == "") ? 0 : number_format($data,0,"","");
		//$objResponse->assign("supplierAddress_$inputId", "value", "$supplierAddressRecs");
		//if (sizeof($supplierAddressRecs)>0) addDropDownOptions("supplierAddress", $supplierAddressRecs, $selSupplierNameId, $objResponse);
		// $objResponse->alert(sizeof($speciesRecs));
		if (sizeof($speciesRecs) == 1) { $speciesRecs = $objWeighmentDataSheet->filterSpecies(0);}
		if (sizeof($speciesRecs)>0) addDropDownOptions("product_species_$inputId", $speciesRecs, $pondId, $objResponse);
		return $objResponse;
	}
	
	function processCode($fishId,$inputId,$celId)
	{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert('55');
		$databaseConnect 		= new DatabaseConnect();
		$objWeighmentDataSheet 	= new WeighmentDataSheet($databaseConnect);
		$processCodeRecs 			= $objWeighmentDataSheet->filterProcessCode($fishId);
		
		if (sizeof($processCodeRecs)>0) addDropDownOptions("process_code_$inputId", $processCodeRecs, $celId, $objResponse);
		return $objResponse;
	}
	
	function generateDatasheet()
	{
		$selDate=Date('Y-m-d');
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($selDate);	
		$databaseConnect 		= new DatabaseConnect();
		$objWeighmentDataSheet = new WeighmentDataSheet($databaseConnect);
		//$objResponse->alert(mysqlDateFormat($selDate));
		$checkGateNumberSettingsExist=$objWeighmentDataSheet->chkValidDataSheetId($selDate);
		if(sizeof($checkGateNumberSettingsExist)>0)
		{
			$alphaCode=$objWeighmentDataSheet->getAlphaCode();
			$alphaCodePrefix= $alphaCode[0];
			$numbergen=$checkGateNumberSettingsExist[0][0];
			//$objResponse->alert("HII");
			//$objResponse->alert($alphaCodePrefix);
			$checkExist=$objWeighmentDataSheet->checkDataSheetDisplayExist();
			if ($checkExist>0)
			{
				$getFirstRecord=$objWeighmentDataSheet->getmaxDataSheetId();
				$getFirstRec= $getFirstRecord[0];
				//$objResponse->alert($getFirstRec);
				$getFirstRecEx=explode($alphaCodePrefix,$getFirstRec);
				//$objResponse->alert($getFirstRecEx[1]);
				$nextDataSheetId=$getFirstRecEx[1]+1;
				//$objResponse->alert($nextDataSheetId);
				$validendno=$objWeighmentDataSheet->getValidendnoDataSheetId($selDate);	
					if ($nextDataSheetId>$validendno)
					{
						$DataSheetMsg="Please set the Data sheet number in Settings,since it reached the end no";
						$objResponse->assign("message","innerHTML",$DataSheetMsg);
					}
					else
					{
						$disGateNo="$alphaCodePrefix$nextDataSheetId";
						$objResponse->assign("data_sheet_slno","value","$disGateNo");
						$objResponse->assign("number_gen_id","value","$numbergen");	
					}
			}
			else
			{
				
				$validPassNo=$objWeighmentDataSheet->getValidDataSheetId($selDate);	
				//$checkPassId=$objWeighmentDataSheet->chkValidDataSheetId($selDate);
				$disDataSheetId="$alphaCodePrefix$validPassNo";
				$objResponse->assign("data_sheet_slno","value","$disDataSheetId");
				$objResponse->assign("number_gen_id","value","$numbergen");	
			}
		}
		else
		{
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

function productCodeDetails($fishId,$inputId,$celId)
{
		
		$objResponse 			= new xajaxResponse();
		//$objResponse->alert($fishId);
		$databaseConnect 		= new DatabaseConnect();
		$objWeighmentDataSheet 	= new WeighmentDataSheet($databaseConnect);
		$processCodeRecs 			= $objWeighmentDataSheet->filterProcessCode($fishId);
		//$objResponse->alert(sizeof($processCodeRecs));
		if (sizeof($processCodeRecs)>0) addDropDownOptions("processCodeValue_$inputId", $processCodeRecs, $celId, $objResponse);
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

function getTableRowBasedRmLotId($rm_lot_id,$edit = '')
{
	$objResponse 			= new xajaxResponse();
	// $objResponse->alert($rm_lot_id);
	$databaseConnect 		= new DatabaseConnect();
	$objWeighmentDataSheet 	= new WeighmentDataSheet($databaseConnect);
	if($edit == '')
	{
		$result = $objWeighmentDataSheet->getTableRowBasedRmLotId($rm_lot_id);
		//$objResponse->alert(print_r($result));
		//print_r($result );
		$hiddenFields = '';
		$returnVal = '<tr bgcolor="#f2f2f2" align="center">
						<td class="listing-head" nowrap> Supplier </td>
						<td class="listing-head" nowrap> Farm Name </td>
						<td class="listing-head" nowrap> Species </td>
						<td class="listing-head" nowrap> Process Code </td>
						<td class="listing-head" nowrap> Quality </td>
						<td class="listing-head" nowrap> Count Code </td>
						<td class="listing-head" nowrap>  Weight </td>
						<td class="listing-head" nowrap> Soft % </td>
						<td class="listing-head" nowrap> Soft Weight </td>
						<td class="listing-head" nowrap> Pht tag </td>
						<td>&nbsp; <input type="hidden" name="rowcount" id="rowcount" value="'.sizeof($result).'" />
						<input type="hidden" name="hidTableRowCounts" id="hidTableRowCounts" value="'.sizeof($result).'" /></td>
					</tr>';
			$qualityDropDown = '';
			$qualityList = $objWeighmentDataSheet->getAllQuality();
			//$objResponse->alert(sizeof($result));
			if(sizeof($result) > 0)
			{
				$i=0;
				foreach($result as $res)
				{
					$hiddenFields = "<input name='mstatus[]' type='hidden' id='mstatus_".$i."'>
									<input name='IsFromDB[]' type='hidden' id='IsFromDB_".$i."' value='N'>
									<input type='hidden' name='mrmId_".$i."' id='mrmId_".$i."'>
									<input type='hidden' name='newData[]' id='newData_".$i."' value='1'>
									<input type='hidden' name='phtTagData[]' id='phtTagData_".$i."' value=''>
									<input type='hidden' name='weightmentId[]' id='weightmentId_".$i."' value=''>";
					if(sizeof($qualityList) > 0)
					{
						$qualityDropDown = '<select name="quality[]" id="quality_'.$i.'">';
						$qualityDropDown.= '<option value=""> -- Select --</option>';
						foreach($qualityList as $quality)
						{
							$qualityDropDown.= '<option value="'.$quality[0].'"> '.$quality[1].' </option>';
						}
						$qualityDropDown.= '</select>';
					}
					$farmName = '<select name="pondName[]" id="pondName_'.$i.'" />
									<option value=""> Select </option>
								</select>';
					$processCode = '<select name="process_code[]" id="process_code_'.$i.'" />
									<option value=""> Select </option>
								</select>';
					$supplierDropDown = '<select name="supplierName[]" id="supplierName_'.$i.'" />
										<option value="'.$res[0].'">'.$res[1].'</option>
										</select>';
					$speciesdropDown = '';
					if(isset($res[2]))
					{
						$farmName = '<select name="pondName[]" id="pondName_'.$i.'" />
										<option value="'.$res[2].'">'.$res[3].'</option>
										</select>';
						$speciesArray = explode(',',$res[4]);
						// $objResponse->alert(sizeof($speciesArray).'---'.$res[4]);
					/*	if($res[4] != '' && sizeof($speciesArray) > 0)
						{
							$speciesdropDown = '<select name="product_species[]" id="product_species_'.$i.'" onchange="xajax_processCode(this.value,'.$i.',0);">';
							$speciesdropDown.= '<option value=""> --Select-- </option>';
							foreach($speciesArray as $species)
							{	
								$speciesPrint = explode('$$',$species);
								$speciesdropDown.= '<option value="'.$speciesPrint[0].'">'.$speciesPrint[1].'</option>';
							}
							$speciesdropDown.= '</select>';
						}
						else
						{*/
							$speciesArray = $objWeighmentDataSheet->getAllSpecies();
							if(sizeof($speciesArray) > 0)
							{
								$speciesdropDown = '<select name="product_species[]" id="product_species_'.$i.'" onchange="xajax_processCode(this.value,'.$i.',0);">';
								$speciesdropDown.= '<option value=""> --Select-- </option>';
								foreach($speciesArray as $species)
								{	
									$speciesdropDown.= '<option value="'.$species[0].'">'.$species[1].'</option>';
								}
								$speciesdropDown.= '</select>';
							}
						/*}*/
						$speciesdropDown.= '<input type="hidden" name="procurementAvailable" id="procurementAvailable" value="1">';
					}
					else
					{
						$speciesArray = $objWeighmentDataSheet->getAllSpecies();
						if(sizeof($speciesArray) > 0)
						{
							$speciesdropDown = '<select name="product_species[]" id="product_species_'.$i.'" onchange="xajax_processCode(this.value,'.$i.',0);">';
							$speciesdropDown.= '<option value=""> --Select-- </option>';
							foreach($speciesArray as $species)
							{	
								$speciesdropDown.= '<option value="'.$species[0].'">'.$species[1].'</option>';
							}
							$speciesdropDown.= '</select>';
						}
						$speciesdropDown.= '<input type="hidden" name="procurementAvailable" id="procurementAvailable" value="0">';
					}
					
					$returnVal.='<tr bgcolor="#f2f2f2" align="center">
							<td class="listing-head"> '.$supplierDropDown.' </td>
							<td class="listing-head"> '.$farmName.' </td>
							<td class="listing-head"> '.$speciesdropDown.' </td>
							<td class="listing-head"> '.$processCode.' </td>
							<td class="listing-head"> '.$qualityDropDown.' </td>
							<td class="listing-head"> <input type="text" name="count_code[]" id="count_code_'.$i.'" size="10" /> </td>
							<td class="listing-head"> <input type="text" name="weight[]" id="weight_'.$i.'" size="10" onkeyup="calculateWeightAndPercent();"  onblur="changePhtTag('.$i.');" / autofill=off> </td>
							<td class="listing-head"> <input type="text" name="soft_precent[]" id="soft_precent_'.$i.'" size="10" onkeyup="calculateWeightAndPercent();" /> </td>
							
							<td nowrap="" class="listing-head"> <input type="text" readonly name="soft_weight[]" id="soft_weight_'.$i.'" size="10" /> </td>
							<td class="listing-item"><a href="#" class="link1" onclick="getPhtCertificate('.$i.');">link tag</a></td>
							<td>'.$hiddenFields.'</td>
							
						</tr>';
						$i++;
				}
			}
			else
			{
				$returnVal.= '<tr>
								<td  class="err1"  nowrap align="center" colspan="10"> No records found 
								<input type="hidden" name="procurementAvailable" id="procurementAvailable" value="">
								</td>
							</tr>';
			}
			$objResponse->assign("tblWeighmentMultiple", "innerHTML", "$returnVal");
		}
	return $objResponse;
}
	
function getSupplierBasedOnRmLotId($rm_lot_id,$inputId,$sel)
{
		$objResponse 			= new xajaxResponse();
		// $objResponse->alert($rm_lot_id);
		$databaseConnect 		= new DatabaseConnect();
		$objWeighmentDataSheet 	= new WeighmentDataSheet($databaseConnect);
		
		$suppliers = $objWeighmentDataSheet->getSupplierBasedOnRmLotId($rm_lot_id);
		$ponds = $objWeighmentDataSheet->getPondBasedOnRmLotId($rm_lot_id);
		if (sizeof($suppliers)>0) addDropDownOptions("supplierName_$inputId", $suppliers, $sel, $objResponse);
		if (sizeof($ponds)>0) addDropDownOptions("pondName_$inputId", $ponds, $sel, $objResponse);
		return $objResponse;
}	

function getPondBasedOnRmLotIdAndSupplier($rm_lot_id,$supplier_id,$inputId,$sel)
{
		$objResponse 			= new xajaxResponse();
		// $objResponse->alert($rm_lot_id);
		$databaseConnect 		= new DatabaseConnect();
		$objWeighmentDataSheet 	= new WeighmentDataSheet($databaseConnect);
		
		
		$ponds = $objWeighmentDataSheet->getPondBasedOnRmLotIdAndSupplier($rm_lot_id,$supplier_id);
		if (sizeof($ponds)>0) addDropDownOptions("pondName_$inputId", $ponds, $sel, $objResponse);
		return $objResponse;
}

function getPhtCertificate($i,$supplierId,$pondId,$product_species,$weight,$phtTagData)
{	$result="";
	$objResponse 			= new xajaxResponse();
		// $objResponse->alert($rm_lot_id);
	$databaseConnect 		= new DatabaseConnect();
	$objWeighmentDataSheet 	= new WeighmentDataSheet($databaseConnect);
	if($phtTagData=="")
	{
		$certificate = $objWeighmentDataSheet->getPhtCerificateDetail($i,$supplierId,$pondId,$product_species,$weight);
	}
	else
	{
		$certificate = $objWeighmentDataSheet->getPhtCerificateDetailTag($i,$supplierId,$pondId,$product_species,$weight,$phtTagData);
	}
	if(sizeof($certificate)>0)
	{
		$objResponse->assign("dialog", "innerHTML", $certificate);
	}
	
	return $objResponse;
}


/*function certificateNo($certificateId,$rowcount)
{
	$objResponse 			= new xajaxResponse();
	//$objResponse->alert($certificateId);
	$databaseConnect 		= new DatabaseConnect();
	$objWeighmentDataSheet 	= new WeighmentDataSheet($databaseConnect);
	$certificateRecs 			= $objWeighmentDataSheet->getCertificate($certificateId);
	//$objResponse->alert($certificateRecs);
	$objResponse->assign("availableQnty_$rowcount", "value", "$certificateRecs");
	$objResponse->script("balanceQnty($rowcount,$certificateRecs);");
	return $objResponse;
}*/

function certificateNo($certificateId,$rowcount,$supplyRow)
{
	$objResponse 			= new xajaxResponse();
	//$objResponse->alert($certificateId);
	$databaseConnect 		= new DatabaseConnect();
	$objWeighmentDataSheet 	= new WeighmentDataSheet($databaseConnect);
	$certificateQnty 		= $objWeighmentDataSheet->getCertificate($certificateId);
	//$objResponse->alert($certificateRecs);
	
	$objResponse->script("availableQnty($rowcount,$certificateId,$certificateQnty,$supplyRow);");
	$objResponse->script("requiredRow($rowcount,$supplyRow);");
	
	//$objResponse->assign("availableQnty_$rowcount", "value", "$certificateRecs");
	//$objResponse->script("balanceQnty($rowcount,$certificateRecs);");
	return $objResponse;
}

function certificateNoEdit($certificateId,$rowcount,$supplyRow)
{
	$objResponse 			= new xajaxResponse();
	//$objResponse->alert($certificateId);
	$databaseConnect 		= new DatabaseConnect();
	$objWeighmentDataSheet 	= new WeighmentDataSheet($databaseConnect);
	$certificateQnty 		= $objWeighmentDataSheet->getCertificate($certificateId);
	//$objResponse->alert($certificateRecs);
	
	$objResponse->script("availableQntyEdit($rowcount,$certificateId,$certificateQnty,$supplyRow);");
	//$objResponse->script("requiredRow($rowcount,$supplyRow);");
	
	return $objResponse;
}




function getPhtCertificateEdit($i,$supplierId,$pondId,$product_species,$weight,$phtTagData)
{	$result="";
	$objResponse 			= new xajaxResponse();
	// $objResponse->alert($rm_lot_id);
	$databaseConnect 		= new DatabaseConnect();
	$objWeighmentDataSheet 	= new WeighmentDataSheet($databaseConnect);
	//$objResponse->alert($phtTagData);
	if($phtTagData=="")
	{
		$certificate = $objWeighmentDataSheet->getPhtCerificateDetailEdit($i,$supplierId,$pondId,$product_species,$weight);
	}
	else
	{
		$certificate = $objWeighmentDataSheet->getPhtCerificateDetailTagEdit($i,$supplierId,$pondId,$product_species,$weight,$phtTagData);
	}
	if(sizeof($certificate)>0)
	{
		$objResponse->assign("dialog", "innerHTML", $certificate);
	}
	
	return $objResponse;
}


$xajax->register(XAJAX_FUNCTION, 'certificateNoEdit', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getPhtCertificateEdit', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getPhtCertificate', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'certificateNo', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getPhtCertificate', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getPondBasedOnRmLotIdAndSupplier', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));		
$xajax->register(XAJAX_FUNCTION, 'getSupplierBasedOnRmLotId', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));	
$xajax->register(XAJAX_FUNCTION, 'generateDatasheet', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'ProcurmentDetailEquipment', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'ProcurmentDetailChemical', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'chemicalIssued', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'equipmentIssued', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'rmProcurmentPondName', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'productCodeDetails', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'weightmentSpeciespro', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'ProcurmentDetail', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));

$xajax->register(XAJAX_FUNCTION, 'weightmentSpecies', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'processCode', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'weightmentSupplierName', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'weightmentSupplierAddress', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'rmprocurementdet', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'rmDataSheetDetails', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getProcessCode', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->register(XAJAX_FUNCTION, 'getTableRowBasedRmLotId', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
$xajax->ProcessRequest();



?>