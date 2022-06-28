<?
require("include/include.php");

/*
	$numEntries	=	"";
	$recDel	=	false;
	
	if ($p["entryId"] =="") $lastId	=	$g["lastId"];
	else $lastId	=	$p["entryId"];

#paging variables 
if($p["curBasketWt"]!=""){
		$basketWt =	$p["curBasketWt"];
	}
	else if($decTotalWt!="" || $decTotalWt!=0 || $p["declNetWt"]!=""){
		$basketWt = $decTotalWt;
	}
	else {
		$basketWt		=	$g["basketWt"];
	}

#Add new entry 
	$count = $p["hidTotalCount"];
	
#Gross Weight Add and Save changes   -------------
if( $p["cmdSaveChange"]!=""){
		
		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++) {
			$grossId		=	$p["grossId_".$i];
			$grossWt		=	$p["grossWt_".$i];
			$basketWt		=	$p["grossBasketWt_".$i];
			$entryId		=	$p["entryId"];
			
			if($grossId	=="" && $entryId!="" && $grossWt!="")
			{
				$dailyGrossRecIns=$dailycatchentryObj->addGrossWt($grossWt,$basketWt,$entryId);
				$saveChanges	=	$p["countSaved"];
			}
			else if($grossId!="" && $entryId!="" && $grossWt!="") 
			{
				$grossUpdateRec		=	$dailycatchentryObj->updateGrossWt($grossId,$grossWt,$basketWt,$entryId);	
				$saveChanges	=	$p["countSaved"];
			}
			

		}
		if($grossUpdateRec)
		{
			//$sessObj->createSession("displayMsg",$msg_succUpdateQuality);
			//$sessObj->createSession("nextPage",$url_afterDelProcessor);
		}
		else
		{
			$err	=	$msg_failUpdateGross;
		}
		$grossUpdateRec	=	false;
	}


#Delete gross List

if( $p["cmdDelete"]!=""){

		$rowCount	=	$p["hidRowCount"];
		for($i=1; $i<=$rowCount; $i++)
		{
			$grossId	=	$p["delId_".$i];

			if( $grossId!="" )
			{
				$grossRecDel		=	$dailycatchentryObj->deleteGrossEntryWt($grossId);	
				$recDel	=	true;
			}
		}
}	
		
if( $g["newWt"]!="" ){	
		
	$resetBasketWt			=	$g["newWt"];
	$entryId				=	$p["entryId"];
	if($resetBasketWt!="" && $entryId!=""){
		$updateBasketWtRec	=	$dailycatchentryObj->updateBasketWt($resetBasketWt,$entryId);
	}
	
}		
		
		
if($p["entryId"]==""){
	$entryId=$g["lastId"];
}else {
	$entryId = $lastId;
}


#List All Gross Wt based on paging
$grossRecords	=	$dailycatchentryObj->fetchAllPagingRecords($entryId,$offset,$limit);
$grossRecSize	=	sizeof($grossRecords);


#count all Gross Records
$countGrossRecords	=	$dailycatchentryObj->fetchAllGrossRecords($entryId);
foreach ($countGrossRecords as $cgr){
			$countGrossWt			=	$cgr[1];
			$totalWt			=	$totalWt+$countGrossWt;
			$countGrossBasketWt		=	$cgr[2];
			$grandTotalBasketWt		=	$grandTotalBasketWt + $countGrossBasketWt;
			$netGrossWt			=	$totalWt - $grandTotalBasketWt;
}
*/

	#List all Product Records
	$productMasterRecords = $productMasterObj->fetchAllRecords();

	#List all Fish Cutting Records
	$fishCuttingRecords = $productionFishCuttingObj->fetchAllFishCuttingRecs();

	#List all Production Records
	$productionMatrixRecords = $productionMatrixObj->fetchAllProductionMatrixRecords();
?>

<!--script language="javascript">
	parent.document.frmDailyCatch.entryGrossNetWt.value='<?=$netGrossWt?>';
	parent.document.frmDailyCatch.entryTotalGrossWt.value='<?=$totalWt?>';
	parent.document.frmDailyCatch.entryTotalBasketWt.value='<?=$grandTotalBasketWt?>';
	parent.document.frmDailyCatch.totalGrossWt.value	='<?=$totalWt?>';
	parent.document.frmDailyCatch.totalBasketWt.value	='<?=$grandTotalBasketWt?>';	
</script-->

<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript" src="libjs/ComboMatrix.js"></script>
<script type="text/javascript" src="libjs/generalFunctions.js"></script>

<form name="frmComboMatrixProduct" action="ComboMatrix_Product.php" method="post">
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
	<? 
		$col=3;
		
		for ($i=1;$i<=$col;$i++) {
	?>
			
	<td width="9%">
    	<table cellpadding="0" cellspacing="0">
	<tr bgcolor="#f2f2f2" class="listing-head">
		<td colspan="2" align="center">Product <?=$i?></td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >*Net Wt</td>
		<td>
		<? 
			$netWt = "";	
			if ($p["netWt_".$i]!="") $netWt = $p["netWt_".$i]; 
		?>
		<input type="text" name="netWt_<?=$i?>" id="netWt_<?=$i?>" size="6" value="<?=$netWt?>" style="text-align:right" onkeyup="getComboMatrixMixProduct();">
		</td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >*Fish Wt</td>
		<td class="listing-item">
		<? 
			$fishWt = "";
			if ($p["fishWt_".$i]!="") $fishWt = $p["fishWt_".$i]; 
		?>
		<input type="text" name="fishWt_<?=$i?>" id="fishWt_<?=$i?>" size="6" value="<?=$fishWt?>" style="text-align:right" onkeyup="getComboMatrixMixProduct();">
		</td>
	</tr>					
	<tr>
		<td class="fieldName" nowrap >Gravy Wt</td>
		<td class="listing-item">
		<? 
			$gravyWt = "";
			if ($p["gravyWt_".$i]!="") $gravyWt = $p["gravyWt_".$i]; 
		?>
		<input type="text" name="gravyWt_<?=$i?>" id="gravyWt_<?=$i?>" size="6" value="<?=$gravyWt?>" style="text-align:right" readonly>
		</td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >% of Seafood</td>
		<td class="listing-item" nowrap>
		<? 
			$percentSeafood = "";
			if ($p["percentSeafood_".$i]!="") $percentSeafood = $p["percentSeafood_".$i]; 
		?>
		<input type="text" name="percentSeafood_<?=$i?>" id="percentSeafood_<?=$i?>" size="6" value="<?=$percentSeafood?>" style="text-align:right" readonly>&nbsp;%
		</td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >*RM Code</td>
		<td class="listing-item">
		<? 
		$rMCodeId = "";
		$fishRatePerKgPerBatch = "";
		$gravyRatePerKgPerBatch = "";
		if ($p["rMCodeId_".$i]!="") $rMCodeId = $p["rMCodeId_".$i]; 

		if ($rMCodeId!="") {
			list($productRatePerKgPerBatch, $fishRatePerKgPerBatch, $gravyRatePerKgPerBatch) =$productMasterObj->getProductMasterRec($rMCodeId);		
		}	
		?>
		<select name="rMCodeId_<?=$i?>" id="rMCodeId_<?=$i?>" onchange="this.form.submit();">		
		<option value="">--Select--</option>
		<?
			foreach ($productMasterRecords as $pmr) {
				$productId	=	$pmr[0];
				$productCode	=	$pmr[1];
				$productName	=	$pmr[2];
				$selected = "";
				if ($rMCodeId==$productId) $selected = "Selected";
		?>	
		<option value="<?=$productId?>" <?=$selected?>><?=$productCode?></option>
		<? }?>
		</select>						
		</td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >*No of Batches</td>
		<td class="listing-item">
		<? 
			$noOfBatches = "";
			if ($p["noOfBatches_".$i]!="") $noOfBatches = $p["noOfBatches_".$i]; 
		?>
		<input type="text" name="noOfBatches_<?=$i?>" id="noOfBatches_<?=$i?>" size="6" value="<?=$noOfBatches?>" onkeyup="getComboMatrixMixProduct();">
		</td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >*Batch Size</td>
		<td class="listing-item">
		<? 
			$batchSize = "";
			if ($p["batchSize_".$i]!="") $batchSize = $p["batchSize_".$i]; 
		?>
		<input type="text" name="batchSize_<?=$i?>" size="5" id="batchSize_<?=$i?>" value="<?=$batchSize?>" onkeyup="getComboMatrixMixProduct();"></td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >*Fish</td>
		<td class="listing-item">
		<? 
			$selFishId = "";
			$selFishCost = "";
			if ($p["selFish_".$i]!="") $selFishId = $p["selFish_".$i]; 
		if ($selFishId!="") {
			$selFishCost = $productionFishCuttingObj->getFishCuttingCost($selFishId);
		}
		?>
		<select name="selFish_<?=$i?>" id="selFish_<?=$i?>" onchange="this.form.submit();">
		<option value="">-- Select --</option>	
		<?
		foreach ($fishCuttingRecords as $fcr) {
			$fishCuttingRecId 	= $fcr[0];
			$fName			= stripSlash($fcr[1]);
			$fCode			= $fcr[2];	
			$fishCuttingCost	= $fcr[3];
			$selected = "";
			if ($selFishId==$fishCuttingRecId) $selected = "Selected";
		?>		
		<option value="<?=$fishCuttingRecId?>" <?=$selected?>> <?=$fCode?></option>
		<? }?>	
		</select>
		</td>
	</tr>
	<tr>
		<td class="fieldName" nowrap >*Production Code</td>
		<td class="listing-item">
		<? 
			$selProductionCodeId	= "";
			$waterCostPerBtch	= "";
			$dieselCostPerBtch	= "";
			$electricityCostPerBtch = "";
			$gasCostPerBtch 	= "";
			$maintCostPerBtch	= "";
			$variManPwerCostPerBtch	= "";
			$fixedManPowerCostPerDay = "";
			$mktgTeamCostPerPouch	= "";
			$adCostPerPouch		= "";
			$holdingCost		= "";
			$holdingDuration	= "";
			$noOfDaysInYear		= "";
			$adminOverheadChargesCost = "";
			if ($p["productionCode_".$i]!="") $selProductionCodeId = $p["productionCode_".$i]; 

	if ($selProductionCodeId!="") {		
		
		list($prodCode, $prodName, $fillingWtPerPouch, $prodQtyPerBtch, $noOfPouch, $processedWtPerBtch, $noOfHrsPrep, $noOfHrsCook, $noOfHrsFill, $noOfHrsRetort, $noOfHrsFirstBtch, $noOfHrsOtherBtch, $noOfBtchsPerDay, $boilerRequired, $dieselCostPerBtch, $electricityCostPerBtch, $waterCostPerBtch, $gasCostPerBtch, $totFuelCostPerBtch, $maintCostPerBtch, $variManPwerCostPerBtch, $mktgTeamCostPerPouch, $mktgTravelCost, $adCostPerPouch) = $productionMatrixObj->getProductionMatrixRec($selProductionCodeId);	

		#Producion matrix Master
		list($noOfHoursPerShift, $noOfShifts, $noOfRetorts, $noOfSealingMachines, $noOfPouchesSealed, $noOfMinutesForSealing, $noOfDaysInYear, $noOfWorkingDaysInMonth, $noOfHoursPerDay, $noOfMinutesPerHour, $dieselConsumptionOfBoiler, $dieselCostPerLitre, $electricConsumptionPerShift, $electricConsumptionPerDayUnit, $electricCostPerUnit, $waterConsumptionPerRetortBatchUnit, $generalWaterConsumptionPerDayUnit, $costPerLitreOfWater, $noOfCylindersPerShiftPerRetort, $gasPerCylinderPerDay, $costOfCylinder, $maintenanceCostPerRetortPerShift, $maintenanceCost, $consumableCostPerShiftPerMonth, $consumablesCost, $labCostPerRetort, $labCost, $pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost, $holdingCost, $holdingDuration, $adminOverheadChargesCode, $adminOverheadChargesCost, $profitMargin, $insuranceCost, $educationCess, $exciseRate, $pickle, $variableManPowerCostPerDay, $fixedManPowerCostPerDay, $totalMktgCostActual, $totalMktgCostIdeal, $totalMktgCostTCost, $totalMktgCostACost, $totalTravelCost, $totalTravelACost, $advtCostPerMonth) = $productionMatrixMasterObj->getProductionMasterValue();
	}
		?>
		<select name="productionCode_<?=$i?>" id="productionCode_<?=$i?>" onchange="this.form.submit();">
		<option value="">-- Select --</option>
		<?
		foreach ($productionMatrixRecords as $pmr) {
			$productionMatrixRecId 	= $pmr[0];
			$pmCode			= $pmr[1];
			$pmName			= $pmr[2];
			$selected = "";
			if ($selProductionCodeId==$productionMatrixRecId) $selected = "Selected";	
		?>
		<option value="<?=$productionMatrixRecId?>" <?=$selected?>><?=$pmCode?></option>
		<? }?>
		</select>
		</td>
<!-- Setting hidden value	 -->
<input type="hidden" name="waterCostPerBtch_<?=$i?>" id="waterCostPerBtch_<?=$i?>" value="<?=$waterCostPerBtch?>">
<input type="hidden" name="dieselCostPerBtch_<?=$i?>" id="dieselCostPerBtch_<?=$i?>" value="<?=$dieselCostPerBtch?>">
<input type="hidden" name="electricityCostPerBtch_<?=$i?>" id="electricityCostPerBtch_<?=$i?>" value="<?=$electricityCostPerBtch?>">
<input type="hidden" name="gasCostPerBtch_<?=$i?>" id="gasCostPerBtch_<?=$i?>" value="<?=$gasCostPerBtch?>">
<input type="hidden" name="maintCostPerBtch_<?=$i?>" id="maintCostPerBtch_<?=$i?>" value="<?=$maintCostPerBtch?>">
<input type="hidden" name="variManPwerCostPerBtch_<?=$i?>" id="variManPwerCostPerBtch_<?=$i?>" value="<?=$variManPwerCostPerBtch?>">
<input type="hidden" name="fixedManPowerCostPerDay_<?=$i?>" id="fixedManPowerCostPerDay_<?=$i?>" value="<?=$fixedManPowerCostPerDay?>">
<input type="hidden" name="selFishCost_<?=$i?>" id="selFishCost_<?=$i?>" value="<?=$selFishCost?>"> 
<input type="hidden" name="fishRatePerKgPerBatch_<?=$i?>" id="fishRatePerKgPerBatch_<?=$i?>" value="<?=$fishRatePerKgPerBatch?>"> 
<input type="hidden" name="gravyRatePerKgPerBatch_<?=$i?>" id="gravyRatePerKgPerBatch_<?=$i?>" value="<?=$gravyRatePerKgPerBatch?>">
<input type="hidden" name="pouchesTestPerBatchUnit" id="pouchesTestPerBatchUnit" value="<?=$pouchesTestPerBatchUnit?>">
<input type="hidden" name="mktgTeamCostPerPouch_<?=$i?>" id="mktgTeamCostPerPouch_<?=$i?>" value="<?=$mktgTeamCostPerPouch?>"> 
<input type="hidden" name="mktgTravelCost_<?=$i?>" id="mktgTravelCost_<?=$i?>" value="<?=$mktgTravelCost?>"> 
<input type="hidden" name="adCostPerPouch_<?=$i?>" id="adCostPerPouch_<?=$i?>" value="<?=$adCostPerPouch?>">
<input type="hidden" name="holdingCost" id="holdingCost" value="<?=$holdingCost?>">
<input type="hidden" name="holdingDuration" id="holdingDuration" value="<?=$holdingDuration?>">
<input type="hidden" name="noOfDaysInYear" id="noOfDaysInYear" value="<?=$noOfDaysInYear?>">
<input type="hidden" name="adminOverheadChargesCost" id="adminOverheadChargesCost" value="<?=$adminOverheadChargesCost?>">
<input type="hidden" name="profitMargin" id="profitMargin" value="<?=$profitMargin?>">
	</tr>					
	</table>
	</td>
	<td width="1">&nbsp;</td>
	<td width="1" bgcolor="#CCCCCC">&nbsp;</td>
	<? }?>	
	<input type="hidden" name="hidColumnCount" id="hidColumnCount" value="<?=$col?>">	
	</tr>
<tr><td  colspan="14" align="center">
	<input type="hidden" name="entryId" value="<?=$lastId?>">				
		<table><tr>
		  <td>
		  </td>
		  <td>			
	<table><tr>
	  <td nowrap>&nbsp;
	    <input type="submit" value=" Delete " name="cmdDelete" class="button" onClick="return confirmDelete(this.form,'delId_',<?=$grossRecSize;?>);">&nbsp;<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="assignValue(this.form,'1','countSaved'); return validateGrossEntry(document.frmComboMatrixProduct); "></td></tr></table>
		</td></tr></table>
		</td></tr>
</table>
<? if($saveChanges!="" || $recDel==true){?>
<script language="javascript">
	parent.document.frmDailyCatch.saveChangesOk.value='<?=$saveChanges?>';	
</script>
<? }?>
<script>
	// Combo Matrix Mix product calculation
	getComboMatrixMixProduct();
</script>
</form>
