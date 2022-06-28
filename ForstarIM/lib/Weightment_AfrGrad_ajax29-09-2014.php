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
	function supplierDetails_old($rmLotId,$vals)
	{
	
	   $objResponse 			= new xajaxResponse();		
		$databaseConnect 		= new DatabaseConnect();
		//$objResponse->alert("hii");
		//$objResponse->alert($gradeTypeLenghth);
		
		 $weightmentAfterGradingObj 	= new WeightmentAfterGrading($databaseConnect);
		 $supplierRecs 			= $weightmentAfterGradingObj->filterSupplierList($rmLotId);
		////$objResponse->alert(sizeof($supplierRecs));
		 if (sizeof($supplierRecs)>0) addDropDownOptions("supplier",$supplierRecs,$vals,$objResponse);
		
		return $objResponse;		
		
	}
	function supplierDetails($rmLotId)
	{
		$objResponse 			= new xajaxResponse();		
		$databaseConnect 		= new DatabaseConnect();		
		$weightmentAfterGradingObj 	= new WeightmentAfterGrading($databaseConnect);
		 
		$result 			= $weightmentAfterGradingObj->getCompayAndUnit($rmLotId);
		$companyName = ''; $unitName  = '';
		if(sizeof($result) > 0)
		{
			$companyName = $result[1];
			$unitName    = $result[3];
		}
		$returnVal = '<td nowrap="" class="fieldName"> Billing Company : </td> <td class="listing-item" nowrap> '.$companyName.' </td>  ';
		$returnVal.= '<td nowrap="" class="fieldName"> Unit : </td> <td class="listing-item" nowrap> '.$unitName.' </td>  ';
		$objResponse->assign("companyandunit", "innerHTML", $returnVal);
		
		//$effectiveWeight = $weightmentAfterGradingObj->getEffectiveWeight($rmLotId);
		$weightment=$weightmentAfterGradingObj->getWeightmentSizeofRmlotId($rmLotId);
		$daily=$weightmentAfterGradingObj->getFishesInDailycatchentry($rmLotId);
		$fishRecords  = $weightmentAfterGradingObj->getAllFishes($rmLotId);
		//$objResponse->alert(sizeof($fishRecords));
		if(sizeof($weightment)>0)
		{
			
			if((sizeof($weightment))== (sizeof($daily))) 
			{
			$tableRecords = '<tr bgcolor="#f2f2f2" align="center">
								<td class="listing-head" nowrap> Fish </td>
								<td class="listing-head"> Process Code </td>';
								//<td class="listing-head" nowrap>Count Code </td>
			$tableRecords.= '<td class="listing-head" nowrap>Grading </td>
								<td class="listing-head">Weight </td>
								<td>
									<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="1" />';
									//<input type="hidden" name="effectiveWeight" id="effectiveWeight" size="9" value="'.$effectiveWeight.'" />
			$tableRecords.= 	'</td>
							</tr>';
			$i = 0;
			$fishes = '';
			$process_code = '<select name="process_code_0" id="process_code_0" onchange="xajax_getGrading(this.value,0);"><option value=""> -- Select -- </option></select>';
			//$count_code   = '<input type="text" name="count_code_0" size="12" id="count_code_0" />';
			$grading	  = '<select name="grading_0" id="grading_0" 0><option value=""> -- Select -- </option></select>';
			$weight   = '<input type="text" name="weight_0" id="weight_0" size="4" onkeyup="checkValue(0);" />';
			$imageButton = "<a href='javascript:void(0);' onClick=\"setIssuanceItemStatus('0');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
			$hiddenFields = "<input name='status_0' type='hidden' id='status_0' value=''><input name='IsFromDB_0' type='hidden' id='IsFromDB_0' value='N'><input type='hidden' name='rmId_0' id='rmId_0' value=''><input name='lotidAvailable_0' type='hidden' id='lotidAvailable_0' value='1'>";
			if(sizeof($fishRecords) > 0)
			{
				$fishes.= '<select name="fish_id_0" id="fish_id_0" onchange="xajax_getProcessCode(this.value,'.$rmLotId.',0);"><option value=""> -- Select -- </option>';
				foreach($fishRecords as $fish)
				{	
					$fishes.= '<option value="'.$fish[0].'">'.$fish[1].'</option>';
				}
				$fishes.= '</select>';
			}
			// $objResponse->alert($fishes);
			if($fishes == '')
			{
				$tableRecords.= '<tr class="whiteRow"><td colspan="6"> No records found </td></tr>';
				
			}
			else
			{
				$tableRecords.= '<tr class="whiteRow" id="row_0">';
				$tableRecords.= '<td class="listing-item">'.$fishes.'</td>';
				$tableRecords.= '<td class="listing-item">'.$process_code.'</td>';
				//$tableRecords.= '<td class="listing-item">'.$count_code.'</td>';
				$tableRecords.= '<td class="listing-item">'.$grading.'</td>';
				$tableRecords.= '<td class="listing-item">'.$weight.'</td>';
				$tableRecords.= '<td class="listing-item">'.$imageButton.' '.$hiddenFields.'</td>';
				$tableRecords.= '</tr>';
			}
			$objResponse->assign("tblAddWeightmentAfterGrading", "innerHTML", $tableRecords);
			}
			else
			{
				$objResponse->script("pendingRecordsInDailyCatchEntry();");
			}
		}
		else
		{
			$tableRecords = '<tr bgcolor="#f2f2f2" align="center">
								<td class="listing-head" nowrap> Fish </td>
								<td class="listing-head"> Process Code </td>';
								//<td class="listing-head" nowrap>Count Code </td>
			$tableRecords.= 	'<td class="listing-head" nowrap>Grading </td>
								<td class="listing-head">Weight </td>
								<td>
									<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="1" />';
									//<input type="hidden" name="effectiveWeight" id="effectiveWeight" size="9" value="'.$effectiveWeight.'" />
			$tableRecords.= 	'</td>
							</tr>';
			$i = 0;
			$fishes = '';
			$process_code = '<select name="process_code_0" id="process_code_0" onchange="xajax_getGrading(this.value,0);"><option value=""> -- Select -- </option></select>';
			//$count_code   = '<input type="text" name="count_code_0" size="12" id="count_code_0" />';
			$grading	  = '<select name="grading_0" id="grading_0" 0><option value=""> -- Select -- </option></select>';
			$weight   = '<input type="text" name="weight_0" id="weight_0" size="4" onkeyup="checkValue(0);" />';
			$imageButton = "<a href='javascript:void(0);' onClick=\"setIssuanceItemStatus('0');\" ><img title=\"Click here to remove this item\" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>";
			$hiddenFields = "<input name='status_0' type='hidden' id='status_0' value=''><input name='IsFromDB_0' type='hidden' id='IsFromDB_0' value='N'><input type='hidden' name='rmId_0' id='rmId_0' value=''><input name='lotidAvailable_0' type='hidden' id='lotidAvailable_0' value='1'>";
			if(sizeof($fishRecords) > 0)
			{
				$fishes.= '<select name="fish_id_0" id="fish_id_0" onchange="xajax_getProcessCode(this.value,'.$rmLotId.',0);"><option value=""> -- Select -- </option>';
				foreach($fishRecords as $fish)
				{	
					$fishes.= '<option value="'.$fish[0].'">'.$fish[1].'</option>';
				}
				$fishes.= '</select>';
			}
			// $objResponse->alert($fishes);
			if($fishes == '')
			{
				$tableRecords.= '<tr class="whiteRow"><td colspan="6"> No records found </td></tr>';
				
			}
			else
			{
				$tableRecords.= '<tr class="whiteRow" id="row_0">';
				$tableRecords.= '<td class="listing-item">'.$fishes.'</td>';
				$tableRecords.= '<td class="listing-item">'.$process_code.'</td>';
				//$tableRecords.= '<td class="listing-item">'.$count_code.'</td>';
				$tableRecords.= '<td class="listing-item">'.$grading.'</td>';
				$tableRecords.= '<td class="listing-item">'.$weight.'</td>';
				$tableRecords.= '<td class="listing-item">'.$imageButton.' '.$hiddenFields.'</td>';
				$tableRecords.= '</tr>';
			}
			$objResponse->assign("tblAddWeightmentAfterGrading", "innerHTML", $tableRecords);
		
		}
		return $objResponse;
	}
	function getProcessCode($fishId,$rmlotid,$inputId)
	{
		$objResponse 			= new xajaxResponse();		
		$databaseConnect 		= new DatabaseConnect();		
		$weightmentAfterGradingObj 	= new WeightmentAfterGrading($databaseConnect);
		 
		$result 			= $weightmentAfterGradingObj->getProcessCode($fishId,$rmlotid);
		if (sizeof($result)>0) addDropDownOptions("process_code_$inputId",$result,'',$objResponse);
		return $objResponse;
	}
	function getAllProcessCode($fishId,$inputId,$vals)
	{
		$objResponse 			= new xajaxResponse();		
		$databaseConnect 		= new DatabaseConnect();		
		$weightmentAfterGradingObj 	= new WeightmentAfterGrading($databaseConnect);
		//$objResponse->alert($inputId);
		$result 			= $weightmentAfterGradingObj->getAllProcessCode($fishId);
		if (sizeof($result)>0) addDropDownOptions("process_code_$inputId",$result,'',$objResponse);
		return $objResponse;
	}
	function pondNames($supplier,$rmLotId,$vals)
	{
	
	   $objResponse 			= new xajaxResponse();		
		$databaseConnect 		= new DatabaseConnect();
		//$objResponse->alert("hii");
		//$objResponse->alert($gradeTypeLenghth);
		
		 $weightmentAfterGradingObj 	= new WeightmentAfterGrading($databaseConnect);
		 $pondRecs 			= $weightmentAfterGradingObj->filterPondList($supplier,$rmLotId);
		////$objResponse->alert(sizeof($supplierRecs));
		 if (sizeof($pondRecs)>0) addDropDownOptions("pondName",$pondRecs,$vals,$objResponse);
		
		return $objResponse;		
		
	}
	function getGrading($process_code_id,$inputId,$vals)
	{
	
	    $objResponse 			= new xajaxResponse();		
	    $databaseConnect 		= new DatabaseConnect();
		$weightmentAfterGradingObj 	= new WeightmentAfterGrading($databaseConnect);
		$GradeVal=$weightmentAfterGradingObj->filterGradeList($process_code_id);

		if (sizeof($GradeVal)>0) 
		addDropDownOptions("grading_$inputId",$GradeVal,$vals,$objResponse);
		
		return $objResponse;		
		
	}
	function getRmLotId($material_type,$vals)
	{
		$objResponse 			= new xajaxResponse();		
	    $databaseConnect 		= new DatabaseConnect();
		$weightmentAfterGradingObj 	= new WeightmentAfterGrading($databaseConnect);
		if($material_type=="Raw material")
		{
			$rmlot=$weightmentAfterGradingObj->getAllLotIds($material_type);
			
		}
		elseif($material_type=="Pre process")
		{
			$rmlot=$weightmentAfterGradingObj->getAllLotIdsPreprocess($material_type);
		}
		if (sizeof($rmlot)>0) 
		addDropDownOptions("rm_lot_id",$rmlot,$vals,$objResponse);
		
		
		return $objResponse;
	}
	// function suplierDetail($rmLotId, $selWeightAfterGradeId,$gradeTypeLenghth)
	// {
		
		// $objResponse 			= new xajaxResponse();		
		// $databaseConnect 		= new DatabaseConnect();
		////$objResponse->alert($rmLotId);
		////$objResponse->alert($gradeTypeLenghth);
		
		// $weightmentAfterGradingObj 	= new WeightmentAfterGrading($databaseConnect);		
		// $supplierRecs 			= $weightmentAfterGradingObj->getSupplierDetail($rmLotId);
		////$pondDetails = 'Farm at harvest : '.$supplierRecs[1].' Product Specious : '.$supplierRecs[2].' Total Quantity : '.$supplierRecs[3];
		// $pondDetails = 'Farm at harvest : '.$supplierRecs[1].' Total Quantity : '.$supplierRecs[2];
		// $LotId=$weightmentAfterGradingObj->getGradeId($rmLotId);
		////$objResponse->alert($LotId[0]);
		// $gradeRecs	= $weightmentAfterGradingObj->getGrade($LotId[0]);
		////$objResponse->alert($gradeId);
		// $weightRecs	= $weightmentAfterGradingObj->getWeight($rmLotId);
		// $weig=$weightRecs[1];
		////$unitRecs	= $unitTransferObj->getUnitName($unit);
		////$processingRecs	= $unitTransferObj->getProcessingStage($rmLotId);
		/////$processingRecs	= $unitTransferObj->getProcessingName($processingStage);
		
		////$objResponse->alert($rmLotId);
		
		// if (sizeof($pondDetails)>0) {
		// $objResponse->assign("supplyDetails", "value", $pondDetails);
		// }
		
			// if (sizeof($gradeRecs)>0) {
			// for($i=1; $i<=$gradeTypeLenghth; $i++)
			// {
			
			////$objResponse->alert("gradeType".$i);
			// addDropDownOptions("gradeType".$i, $gradeRecs, $selWeightAfterGradeId, $objResponse);
			// }
		// }
		
		// if (sizeof($weig)>0) {
		// $objResponse->assign("totalwt", "value", $weig);
		// }
		////if (sizeof($processingRecs)>0) {
		////$objResponse->assign("currentProcessingStage", "value", $processingRecs);
		////addDropDownOptions("currentProcessingStage", $processingRecs, $selunitTransferId, $objResponse);
		////}
		////addDropDownOptions("rmtestMethod", $methodRecs, $selrmTestNameId, $objResponse);
		
		// return $objResponse;
	// }
	
	$xajax->register(XAJAX_FUNCTION,'getRmLotId', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getAllProcessCode', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'supplierDetails', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'pondNames', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'pondNames', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getGrading', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	$xajax->register(XAJAX_FUNCTION,'getProcessCode', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	//$xajax->register(XAJAX_FUNCTION,'suplierDetail', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	//$xajax->register(XAJAX_FUNCTION,'getLotId', array('onResponseDelay'=>'showFnLoading','onComplete'=>'hideFnLoading'));
	
	
	$xajax->ProcessRequest();
?>