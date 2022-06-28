<?php
	$insideIFrame = "Y";
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	$selection 	=	"?pageNo=".$p["pageNo"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/

	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;
	
	if ($p["cmdCancel"]!="") {
		$addMode 	= false;
		$editMode 	= false;
	}
	
	#Add 
	if ($p["cmdAdd"]!="") {
		$prodName	= addSlash(trim($p["prodName"]));
		$processType=$p["processType"];
		$kettles=$p["kettles"];
		$hrsForCooking=$p["hrsForCooking"];
		$fillingWtPerPouch 	= $p["fillingWtPerPouch"];
		$prodQtyPerBtch		= $p["prodQtyPerBtch"];	
		$noOfPouch		= $p["noOfPouch"];
		$processedWtPerBtch 	= $p["processedWtPerBtch"];
		$noOfHrsPrep		= $p["noOfHrsPrep"];	
		$noOfHrsCook		= $p["noOfHrsCook"];
		$noOfHrsFilling		= $p["noOfHrsFilling"];
		$noOfHrsFill		= $p["noOfHrsFill"];	
		$noOfHrsRetort		= $p["noOfHrsRetort"];
		$noOfHrsFirstBtch	= $p["noOfHrsFirstBtch"];
		$noOfHrsOtherBtch	= $p["noOfHrsOtherBtch"];
		$gasRequired		=$p["gasRequired"];
		$boilerRequired		=$p["boilerRequired"]; 
		$electRequired		=$p["electRequired"];
		$boilerRequiredProcessing=$p["boilerRequiredProcessing"];
		$noOfBtchsPerDay	= $p["noOfBtchsPerDay"];
		$dieselCostPerBtch	= $p["dieselCostPerBtch"];
		$electricityCostPerBtch = $p["electricityCostPerBtch"];
		$waterCostPerBtch	= $p["waterCostPerBtch"];
		$gasCostPerBtch		= $p["gasCostPerBtch"];
		$totFuelCostPerBtch	= $p["totFuelCostPerBtch"];
		$maintCostPerBtch	= $p["maintCostPerBtch"];
		$variManPwerCostPerBtch = $p["variManPwerCostPerBtch"];
		$mktgTeamCostPerPouch	= $p["mktgTeamCostPerPouch"];
		$coordinationCostPerPouch	= $p["coordinationCostPerPouch"];
		$mktgTravelCost		= $p["mktgTravelCost"];
		$adCostPerPouch		= $p["adCostPerPouch"];
		$facilityCostPerDay=$p["facilityCostPerDay"];
		

		if ($prodName!="" && $processType!="") {
			$chkDuplicateRec=$productionMatrixObj->chckDuplicate($prodName,$processType);
			if(!$chkDuplicateRec)
			{
				$productionMatrixRecIns=$productionMatrixObj->addProductionMatrix($prodName,$processType,$kettles,$hrsForCooking,$fillingWtPerPouch, $prodQtyPerBtch, $noOfPouch, $processedWtPerBtch, $noOfHrsPrep, $noOfHrsCook,$noOfHrsFilling	,$noOfHrsFill, $noOfHrsRetort, $noOfHrsFirstBtch, $noOfHrsOtherBtch,$gasRequired,$boilerRequired, $electRequired,$boilerRequiredProcessing,$noOfBtchsPerDay,$dieselCostPerBtch, $electricityCostPerBtch, $waterCostPerBtch, $gasCostPerBtch, $totFuelCostPerBtch, $maintCostPerBtch, $variManPwerCostPerBtch, $mktgTeamCostPerPouch,$coordinationCostPerPouch,$mktgTravelCost, $adCostPerPouch,$facilityCostPerDay,$userId);
			}
			if ($productionMatrixRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddProductionMatrix);
				$sessObj->createSession("nextPage",$url_afterAddProductionMatrix.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddProductionMatrix;
			}
			$productionMatrixRecIns = false;
		}
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$productionMatrixRecId	= $p["hidProductionMatrixId"];
		$prodName	= addSlash(trim($p["prodName"]));
		$processType=$p["processType"];
		$kettles=$p["kettles"];
		$hrsForCooking=$p["hrsForCooking"];
		$fillingWtPerPouch 	= $p["fillingWtPerPouch"];
		$prodQtyPerBtch		= $p["prodQtyPerBtch"];	
		$noOfPouch		= $p["noOfPouch"];
		$processedWtPerBtch 	= $p["processedWtPerBtch"];
		$noOfHrsPrep		= $p["noOfHrsPrep"];	
		$noOfHrsCook		= $p["noOfHrsCook"];
		$noOfHrsFilling		= $p["noOfHrsFilling"];
		$noOfHrsFill		= $p["noOfHrsFill"];	
		$noOfHrsRetort		= $p["noOfHrsRetort"];
		$noOfHrsFirstBtch	= $p["noOfHrsFirstBtch"];
		$noOfHrsOtherBtch	= $p["noOfHrsOtherBtch"];
		$gasRequired		=$p["gasRequired"];
		$boilerRequired		=$p["boilerRequired"]; 
		$electRequired		=$p["electRequired"];
		$boilerRequiredProcessing=$p["boilerRequiredProcessing"];
		$noOfBtchsPerDay	= $p["noOfBtchsPerDay"];
		$dieselCostPerBtch	= $p["dieselCostPerBtch"];
		$electricityCostPerBtch = $p["electricityCostPerBtch"];
		$waterCostPerBtch	= $p["waterCostPerBtch"];
		$gasCostPerBtch		= $p["gasCostPerBtch"];
		$totFuelCostPerBtch	= $p["totFuelCostPerBtch"];
		$maintCostPerBtch	= $p["maintCostPerBtch"];
		$variManPwerCostPerBtch = $p["variManPwerCostPerBtch"];
		$mktgTeamCostPerPouch	= $p["mktgTeamCostPerPouch"];
		$coordinationCostPerPouch	= $p["coordinationCostPerPouch"];
		$mktgTravelCost		= $p["mktgTravelCost"];
		$adCostPerPouch		= $p["adCostPerPouch"];
		$facilityCostPerDay=$p["facilityCostPerDay"];
		
		
		if ($productionMatrixRecId!="" && $prodName!="") {
			$productionMatrixRecUptd = $productionMatrixObj->updateProductionMatrix($productionMatrixRecId, $prodName,$processType,$kettles,$hrsForCooking,$fillingWtPerPouch, $prodQtyPerBtch, $noOfPouch, $processedWtPerBtch, $noOfHrsPrep, $noOfHrsCook,$noOfHrsFilling	,$noOfHrsFill, $noOfHrsRetort, $noOfHrsFirstBtch, $noOfHrsOtherBtch,$gasRequired,$boilerRequired, $electRequired,$boilerRequiredProcessing,$noOfBtchsPerDay,$dieselCostPerBtch, $electricityCostPerBtch, $waterCostPerBtch, $gasCostPerBtch, $totFuelCostPerBtch, $maintCostPerBtch, $variManPwerCostPerBtch, $mktgTeamCostPerPouch,$coordinationCostPerPouch,$mktgTravelCost, $adCostPerPouch,$facilityCostPerDay);
		}
	
		if ($productionMatrixRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succProductionMatrixUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateProductionMatrix.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failProductionMatrixUpdate;
		}
		$productionMatrixRecUptd	=	false;
	}


	# Edit  
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$productionMatrixRec	=	$productionMatrixObj->find($editId);
		$editProductionMatrixId =	$productionMatrixRec[0];
		$prodName		= $productionMatrixRec[1];
		$prodTypeId		= $productionMatrixRec[2];
		$kettles		= $productionMatrixRec[3];
		$hrsForCooking		= $productionMatrixRec[4];
		$fillingWtPerPouch 	= $productionMatrixRec[5];
		$prodQtyPerBtch		= $productionMatrixRec[6];
		$noOfPouch		= $productionMatrixRec[7];
		$processedWtPerBtch 	= $productionMatrixRec[8];
		$noOfHrsPrep		= $productionMatrixRec[9];
		$noOfHrsCook		= $productionMatrixRec[10];
		$noOfHrsFilling		= $productionMatrixRec[11];
		$noOfHrsFill		= $productionMatrixRec[12];
		$noOfHrsRetort		= $productionMatrixRec[13];
		$noOfHrsFirstBtch	= $productionMatrixRec[14];
		$noOfHrsOtherBtch	= $productionMatrixRec[15];
		$noOfBtchsPerDay	= $productionMatrixRec[16];
		$gasRequired		= $productionMatrixRec[17];
		$boilerRequired		= $productionMatrixRec[18];
		$electRequired		= $productionMatrixRec[19];
		$boilerRequiredProcessing		= $productionMatrixRec[20];
		$dieselCostPerBtch	= $productionMatrixRec[21];
		$electricityCostPerBtch = $productionMatrixRec[22];
		$waterCostPerBtch	= $productionMatrixRec[23];
		$gasCostPerBtch		= $productionMatrixRec[24];
		$totFuelCostPerBtch	= $productionMatrixRec[25];
		$maintCostPerBtch	= $productionMatrixRec[26];
		$variManPwerCostPerBtch = $productionMatrixRec[27];
		$coordinationCostPerPouch = $productionMatrixRec[28];
		$mktgTeamCostPerPouch	= $productionMatrixRec[29];
		$mktgTravelCost		= $productionMatrixRec[30];
		$adCostPerPouch		= $productionMatrixRec[31];
		$facilityCostPerDay = $productionMatrixRec[32];
	}


	# Delete a Record
	if ( $p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$productionMatrixRecId	=	$p["delId_".$i];

			if ($productionMatrixRecId!="") {
				// Need to check the selected Category is link with any other process
				$productionMatrixRecDel = $productionMatrixObj->deleteProductionMatrixRec($productionMatrixRecId);
			}
		}
		if ($productionMatrixRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProductionMatrix);
			$sessObj->createSession("nextPage",$url_afterDelProductionMatrix.$selection);
		} else {
			$errDel	=	$msg_failDelProductionMatrix;
		}
		$productionMatrixRecDel	=	false;
	}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo = $p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo = $g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Man Power
	$productionMatrixResultSetObj = $productionMatrixObj->fetchAllPagingRecords($offset, $limit);
	$productionMatrixRecordSize	= $productionMatrixResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$allProductionMatrixResultSetObj = $productionMatrixObj->fetchAllRecords();
	$numrows	=  $allProductionMatrixResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	

	#Get all Production Matrix master Value
	$productionMatrixMaster = "PMM";	
	$selRateList = $manageRateListObj->latestRateList($productionMatrixMaster);

	if ($addMode || $editMode) 
	{
		//list($noOfHoursPerShift, $noOfShifts, $noOfRetorts, $noOfSealingMachines, $noOfPouchesSealed, $noOfMinutesForSealing, $noOfDaysInYear, $noOfWorkingDaysInMonth, $noOfHoursPerDay, $noOfMinutesPerHour, $dieselConsumptionOfBoiler, $dieselCostPerLitre, $electricConsumptionPerShift, $electricConsumptionPerDayUnit, $electricCostPerUnit, $waterConsumptionPerRetortBatchUnit, $generalWaterConsumptionPerDayUnit, $costPerLitreOfWater, $noOfCylindersPerShiftPerRetort, $gasPerCylinderPerDay, $costOfCylinder, $maintenanceCostPerRetortPerShift, $maintenanceCost, $consumableCostPerShiftPerMonth, $consumablesCost, $labCostPerRetort, $labCost, $pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost, $holdingCost, $holdingDuration, $adminOverheadChargesCode, $adminOverheadChargesCost, $profitMargin, $insuranceCost, $educationCess, $exciseRate, $pickle, $variableManPowerCostPerDay, $fixedManPowerCostPerDay, $totalMktgCostActual, $totalMktgCostIdeal, $totalMktgCostTCost, $totalMktgCostACost, $totalTravelCost, $totalTravelACost, $advtCostPerMonth) = $productionMatrixMasterObj->getProductionMasterValue($selRateList);
		list($noOfHoursPerShift,$noOfShifts,$noOfGravyCookers,$noOfRetorts,$noOfSealingMachines,$noOfPouchesSealed,$noOfMinutesForSealing, $noOfDaysInYear, $noOfWorkingDaysInMonth, $noOfHoursPerDay, $noOfMinutesPerHour)= $productionMatrixObj->getProductionWorkingHours();
		list($dieselCostPerLitre,$dieselConsumptionOfBoiler,$electricCostPerUnit,$electricConsumptionPerDayUnit,$waterConsumptionPerRateUnit,$waterConsumptionPerRetortBatchUnit,$costPerLitreOfWater, $generalWaterConsumptionPerDayUnit,$costOfCylinder,$gasPerCylinderPerDay)= $productionMatrixObj->getProductionFuelRate();
		list($maintenanceCost,$consumablesCost,$labCost, $pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost,$ingredientPowderingCosperkg,$holdingCost,$holdingDuration, $adminOverheadChargesCode, $adminOverheadChargesCost, $profitMargin, $insuranceCost)= $productionMatrixObj->getProductionOtherCost();
		list($variableManPowerCostPerDay, $fixedManPowerCostPerDay, $totalMktgCostTCost, $totalTravelCost,$advtCostPerMonth,$totalCoordinationCost) = $productionMatrixObj->getProductionPowerValue();
		//list($variableManPowerCostPerDay, $fixedManPowerCostPerDay, $totalMktgCostActual, $totalMktgCostIdeal,$totalMktgCostTCost, $totalMktgCostACost, $totalTravelCost, $totalTravelACost, $advtCostPerMonth) = $productionMatrixMasterObj->getProductionMasterValue($selRateList);
		$processRecords=$productionMatrixObj->getProcessRecords();
	}	

	#heading Section
	if ($editMode) $heading	= $label_editProductionMatrix;
	else	       $heading	= $label_addProductionMatrix;

	$ON_LOAD_PRINT_JS = "libjs/ProductionMatrix.js";

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
<form name="frmProductionMatrix" action="ProductionMatrix.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr><TD height="10"></TD></tr>	
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
		<tr>
			<td align="center">
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
						<?php	
							$bxHeader = "Production Matrix";
							include "template/boxTL.php";
						?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td colspan="3" align="center">
										<Table width="30%">
										<?php
											if ( $editMode || $addMode) {
										?>
											<tr>
												<td>
													<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%">
														<tr>
															<td>
																<!-- Form fields start -->
																<?php							
																	$entryHead = $heading;
																	require("template/rbTop.php");
																?>
																<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
																	<!--<tr>
																		<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
																		<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?//=$heading;?></td>
																	</tr>-->
																	<tr>
																		<td width="1" ></td>
																		<td colspan="2" >
																			<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
																				<tr>
																					<td colspan="2" height="10" ></td>
																				</tr>
																				<tr>
																					<? if($editMode){?>
																					<td colspan="2" align="center">
																						<input type="submit" name="cmdCancel2" class="button" value=" Cancel " onclick="return cancel('ProductionMatrix.php');" />&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateProductionMatrix(document.frmProductionMatrix);" />
																					</td>
																					<?} else{?>
																					<td  colspan="2" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductionMatrix.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductionMatrix(document.frmProductionMatrix);">
																					</td>
																					<?}?>
																				</tr>
																				<input type="hidden" name="hidProductionMatrixId" value="<?=$editProductionMatrixId;?>">
																				<tr><TD height="10"></TD></tr>
																				<tr>
																					<td colspan="2" nowrap style="padding-left:10px; padding-right:10px;">
																						<table width="200">
																							<tr>
																							<!-- 	Ist Column -->
																								<TD valign="top">
																									<table>
																										<!--
																										<tr>
																											<td class="fieldName" nowrap >*Product Code</td>
																											<td>
																												<input type="text" name="prodCode" size="20" value="<?=$prodCode?>" />
																											</td>
																										</tr>
																										-->
																										<tr>
																											<td class="fieldName" nowrap >*Product Name</td>
																											<td>
																												<input type="text" name="prodName" size="20" value="<?=$prodName?>" />
																											</td>
																										</tr>	
																										<tr>
																											<td class="fieldName" nowrap >*Process Type</td>
																											<td>
																												<select name="processType" id="processType">
																													<option value="">--Select--</option>
																													<?
																													foreach($processRecords as $pr)
																													{
																														$prdId=$pr[0];
																														$prdName=$pr[1];
																														($prodTypeId==$prdId)?$sel="selected":$sel="";
																													?>
																													<option value="<?=$prdId?>" <?=$sel?>><?=$prdName?></option>
																													<? 
																													} 
																													?>
																												</select>
																											</td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >Kettles used for cooking</td>
																											<td>
																												<select name="kettles" id="kettles" onchange="calcCookingHrs();">
																													<option value="">--Select--</option>
																													<option value="Y" <? if ($kettles=='Y') echo "Selected";?>>Yes</option>
																													<option value="N" <? if ($kettles=='N') echo "Selected";?>>No</option>
																												</select>
																											</td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >Hours for cooking</td>
																											<td><input type="text" name="hrsForCooking" size="5" id="hrsForCooking" value="<?=$hrsForCooking?>" style="text-align:right;" onkeyup="calcCookingHrs()"; ></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Filling Wt per pouch in Kg</td>
																											<td><input type="text" name="fillingWtPerPouch" size="5" id="fillingWtPerPouch" value="<?=$fillingWtPerPouch?>" style="text-align:right;" onkeyup="calcProcessedWtPerBatch();"></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Production Qty per Batch</td>
																											<td class="listing-item"><input type="text" name="prodQtyPerBtch" size="5" id="prodQtyPerBtch" value="<?=$prodQtyPerBtch?>" style="text-align:right;"></td>
																										</tr>					
																										<tr>
																											<td class="fieldName" nowrap >*No of pouches/Batch</td>
																											<td class="listing-item"><input type="text" name="noOfPouch" size="5" id="noOfPouch" value="<?=$noOfPouch?>" style="text-align:right;" onkeyup="calcProcessedWtPerBatch();"></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Processed Wt per Batch in Kg</td>
																											<td class="listing-item"><input type="text" name="processedWtPerBtch" size="5" id="processedWtPerBtch" value="<?=$processedWtPerBtch?>" style="text-align:right;" readonly></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*No of Hours for Prep</td>
																											<td class="listing-item"><input type="text" name="noOfHrsPrep" size="5" id="noOfHrsPrep" value="<?=$noOfHrsPrep?>" style="text-align:right;" onkeyup="calcCookingHrs();"></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*No of Hours for Cooking</td>
																											<td class="listing-item"><input type="text" name="noOfHrsCook" size="5" id="noOfHrsCook" value="<?=$noOfHrsCook?>" style="text-align:right;" readonly></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*No of Hours hours for prep for filling</td>
																											<td class="listing-item"><input type="text" name="noOfHrsFilling" size="5" id="noOfHrsFilling" value="<?=$noOfHrsFilling?>" style="text-align:right;" onkeyup="calcNumHrsForFirstBtch();"></td>
																										</tr>
																										
																										
																									</table>
																								</TD>
																								<!-- IInd Column -->
																								<td valign="top">
																									<table>	
																										<tr>
																											<td class="fieldName" nowrap >*No of Hours for Filling & Sealing</td>
																											<td class="listing-item"><input type="text" name="noOfHrsFill" size="5" id="noOfHrsFill" value="<?=$noOfHrsFill?>" style="text-align:right;" readonly ></td>
																										</tr>						
																										<tr>
																											<td class="fieldName" nowrap >*No of Hours for Retorting</td>
																											<td class="listing-item"><input type="text" name="noOfHrsRetort" size="5" id="noOfHrsRetort" value="<?=$noOfHrsRetort?>" style="text-align:right;" onkeyup="calcNumHrsForFirstBtch();"></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*No of Hours for First Batch</td>
																											<td class="listing-item"><input type="text" name="noOfHrsFirstBtch" size="5" id="noOfHrsFirstBtch" value="<?=$noOfHrsFirstBtch?>" style="text-align:right;" readonly ></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*No of Hours for other Batches</td>
																											<td class="listing-item"><input type="text" name="noOfHrsOtherBtch" size="5" id="noOfHrsOtherBtch" value="<?=$noOfHrsOtherBtch?>" style="text-align:right;" readonly></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Gas Required For Cooking</td>
																											<td class="listing-item">
																												<select name="gasRequired" id="gasRequired" onchange="calcDieselCostPerBtch();">
																													<option value="">--Select--</option>
																													<option value="Y" <? if ($gasRequired=='Y') echo "Selected";?>>Yes</option>
																													<option value="N" <? if ($gasRequired=='N') echo "Selected";?>>No</option>
																												</select>						
																											</td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Boiler Required For Cooking</td>
																											<td class="listing-item">
																												<select name="boilerRequired" id="boilerRequired" onchange="calcDieselCostPerBtch();">
																													<option value="">--Select--</option>
																													<option value="Y" <? if ($boilerRequired=='Y') echo "Selected";?>>Yes</option>
																													<option value="N" <? if ($boilerRequired=='N') echo "Selected";?>>No</option>
																												</select>						
																											</td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >Elect required for processing</td>
																											<td class="listing-item">
																												<select name="electRequired" id="electRequired" onchange="calcDieselCostPerBtch();">
																													<option value="">--Select--</option>
																													<option value="Y" <? if ($electRequired=='Y') echo "Selected";?>>Yes</option>
																													<option value="N" <? if ($electRequired=='N') echo "Selected";?>>No</option>
																												</select>						
																											</td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Boiler Required For Processing</td>
																											<td class="listing-item">
																												<select name="boilerRequiredProcessing" id="boilerRequiredProcessing" onchange="calcDieselCostPerBtch();">
																													<option value="">--Select--</option>
																													<option value="Y" <? if ($boilerRequiredProcessing=='Y') echo "Selected";?>>Yes</option>
																													<option value="N" <? if ($boilerRequiredProcessing=='N') echo "Selected";?>>No</option>
																												</select>						
																											</td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*No of Batches per Day</td>
																											<td class="listing-item"><input type="text" name="noOfBtchsPerDay" size="5" id="noOfBtchsPerDay" value="<?=$noOfBtchsPerDay?>" style="text-align:right;" readonly></td>
																										</tr>
																										
																										<tr>
																											<td class="fieldName" nowrap >*Diesel Cost per Batch</td>
																											<td class="listing-item"><input type="text" name="dieselCostPerBtch" size="5" id="dieselCostPerBtch" value="<?=$dieselCostPerBtch?>" style="text-align:right;" readonly></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Electricity Cost per Batch</td>
																											<td class="listing-item"><input type="text" name="electricityCostPerBtch" size="5" id="electricityCostPerBtch" value="<?=$electricityCostPerBtch?>" style="text-align:right;" readonly></td>
																										</tr>
																									</table>	
																								</td>
																								<td valign="top">
																									<table>					
																										<tr>
																											<td class="fieldName" nowrap >*Water Cost per Batch</td>
																											<td class="listing-item"><input type="text" name="waterCostPerBtch" size="5" id="waterCostPerBtch" value="<?=$waterCostPerBtch?>" style="text-align:right;" readonly></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Gas cost per Batch</td>
																											<td class="listing-item"><input type="text" name="gasCostPerBtch" size="5" id="gasCostPerBtch" value="<?=$gasCostPerBtch?>" style="text-align:right;" readonly></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Total Fuel cost per Batch</td>
																											<td class="listing-item"><input type="text" name="totFuelCostPerBtch" size="7" id="totFuelCostPerBtch" value="<?=$totFuelCostPerBtch?>" style="text-align:right;" readonly></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Maint/Cons. Cost per Batch</td>
																											<td class="listing-item"><input type="text" name="maintCostPerBtch" size="5" id="maintCostPerBtch" value="<?=$maintCostPerBtch?>" style="text-align:right;" readonly></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Variable Manpower Cost per Batch</td>
																											<td class="listing-item"><input type="text" name="variManPwerCostPerBtch" size="5" id="variManPwerCostPerBtch" value="<?=$variManPwerCostPerBtch?>" style="text-align:right;" readonly></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Mktg Team cost per pouch</td>
																											<td class="listing-item"><input type="text" name="mktgTeamCostPerPouch" size="5" id="mktgTeamCostPerPouch" value="<?=$mktgTeamCostPerPouch?>" style="text-align:right;" readonly></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Co-ordination cost per pouch</td>
																											<td class="listing-item"><input type="text" name="coordinationCostPerPouch" size="5" id="coordinationCostPerPouch" value="<?=$coordinationCostPerPouch?>" style="text-align:right;" readonly></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Mktg Travel Cost</td>
																											<td class="listing-item"><input type="text" name="mktgTravelCost" size="5" id="mktgTravelCost" value="<?=$mktgTravelCost?>" style="text-align:right;" readonly></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Advt Cost per pouch</td>
																											<td class="listing-item"><input type="text" name="adCostPerPouch" size="5" id="adCostPerPouch" value="<?=$adCostPerPouch?>" style="text-align:right;" readonly></td>
																										</tr>
																										<tr>
																											<td class="fieldName" nowrap >*Facility Cost/Day</td>
																											<td class="listing-item"><input type="text" name="facilityCostPerDay" size="5" id="facilityCostPerDay" value="<?=$facilityCostPerDay?>" style="text-align:right;" readonly></td>
																										</tr>
																									</table>
																								</td>
																		<!--  Third Column End Here-->
																							</tr>						
																						</table>
																					</td>
																				</tr>
																				<tr>
																					<td colspan="2"  height="10" ></td>
																				</tr>
																				<tr>
																					<? if($editMode){?>
																					<td colspan="2" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductionMatrix.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductionMatrix(document.frmProductionMatrix);">												</td>
																					<?} else{?>

																					<td  colspan="2" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductionMatrix.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductionMatrix(document.frmProductionMatrix);">												</td>
																						<input type="hidden" name="cmdAddNew" value="1">
																					<?}?>
																				</tr>
																				<tr>
																					<td colspan="2"  height="10" ></td>
																				</tr>
																			</table>									
																		</td>
																	</tr>
																</table>	
																<?php
																	require("template/rbBottom.php");
																?>
															</td>
														</tr>
													</table>
													<!-- Form fields end   -->			
												</td>
											</tr>	
											<?
												}
												
												# Listing Category Starts
											?>
										</table>
									</td>
								</tr>
								<tr>
									<td height="10" align="center" ></td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productionMatrixRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductionMatrix.php',700,600);"><? }?></td>
											</tr>
										</table>									
									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?
								if ($errDel!="") {
								?>
								<tr>
									<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?
								}
								?>
								<tr>
									<td width="1" ></td>
									<td colspan="2" style="padding-left:10px; padding-right:10px;">
										<table cellpadding="2"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
										<?
										if ($productionMatrixRecordSize) 
										{
											$i	=	0;
										?>
											<thead>
											<? if($maxpage>1){?>
												<tr>
													<td colspan="6" align="right" style="padding-right:10px;" class="navRow">
														<div align="right">
														<?php
														$nav  = '';
														for ($page=1; $page<=$maxpage; $page++) {
															if ($page==$pageNo) {
																	$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
															} else {
																	$nav.= " <a href=\"ProductionMatrix.php?pageNo=$page\" class=\"link1\">$page</a> ";
																//echo $nav;
															}
														}
														if ($pageNo > 1) {
															$page  = $pageNo - 1;
															$prev  = " <a href=\"ProductionMatrix.php?pageNo=$page\"  class=\"link1\"><<</a> ";
														} else {
															$prev  = '&nbsp;'; // we're on page one, don't print previous link
															$first = '&nbsp;'; // nor the first page link
														}

														if ($pageNo < $maxpage) {
															$page = $pageNo + 1;
															$next = " <a href=\"ProductionMatrix.php?pageNo=$page\"  class=\"link1\">>></a> ";
														} else {
															$next = '&nbsp;'; // we're on the last page, don't print next link
															$last = '&nbsp;'; // nor the last page link
														}
														// print the navigation link
														$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
														echo $first . $prev . $nav . $next . $last . $summary; 
														?>	
														<input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
														</div> 
													</td>
												</tr>
												<? }?>
												<tr  align="center">
													<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
													<th class="listing-head" style="padding-left:10px; padding-right:10px;">Name</th>
													<th class="listing-head" style="padding-left:10px; padding-right:10px;">Process Type</th>		
													<? if($edit==true){?>
													<th class="listing-head">&nbsp;</th>
													<? }?>
												</tr>
											</thead>
											<tbody>
											<?
											while(($pmr=$productionMatrixResultSetObj->getRow())) 
											{
												$i++;
												$productionMatrixRecId 	= $pmr[0];
												$pmCode			= $pmr[1];
												$pmName			= $pmr[2];					
											?>
												<tr>
													<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$productionMatrixRecId;?>" class="chkBox"></td>
													<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$pmCode?></td>
													<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$pmName?></td>
													<? if($edit==true){?>
													<td class="listing-item" width="60" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$productionMatrixRecId;?>,'editId'); this.form.action='ProductionMatrix.php';" ></td>
													<? }?>
												</tr>
											<?
											}
											?>
												<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
												<input type="hidden" name="editId" value="">
											<? if($maxpage>1){?>
												<tr>
													<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
														<div align="right">
															<?php
															$nav  = '';
															for ($page=1; $page<=$maxpage; $page++) {
																if ($page==$pageNo) {
																		$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
																} else {
																		$nav.= " <a href=\"ProductionMatrix.php?pageNo=$page\" class=\"link1\">$page</a> ";
																	//echo $nav;
																}
															}
															if ($pageNo > 1) {
																$page  = $pageNo - 1;
																$prev  = " <a href=\"ProductionMatrix.php?pageNo=$page\"  class=\"link1\"><<</a> ";
															} else {
																$prev  = '&nbsp;'; // we're on page one, don't print previous link
																$first = '&nbsp;'; // nor the first page link
															}

															if ($pageNo < $maxpage) {
																$page = $pageNo + 1;
																$next = " <a href=\"ProductionMatrix.php?pageNo=$page\"  class=\"link1\">>></a> ";
															} else {
																$next = '&nbsp;'; // we're on the last page, don't print next link
																$last = '&nbsp;'; // nor the last page link
															}
															// print the navigation link
															$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
															echo $first . $prev . $nav . $next . $last . $summary; 
															?>	
															<input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
														</div> 
													</td>
												</tr>
												<? }?>
												<?
													} else {
												?>
												<tr>
													<td colspan="8"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
												</tr>	
												<?
													}
												?>
											</tbody>
										</table>	
									</td>
								</tr>
								<tr>
									<td colspan="3" height="5">
										<input type="hidden" name="noOfGravyCookers" id="noOfGravyCookers" value="<?=$noOfGravyCookers?>">
										<input type="hidden" name="noOfMinutesForSealing" id="noOfMinutesForSealing" value="<?=$noOfMinutesForSealing?>">
										<input type="hidden" name="noOfMinutesPerHour" id="noOfMinutesPerHour" value="<?=$noOfMinutesPerHour?>">
										<input type="hidden" name="noOfPouchesSealed" id="noOfPouchesSealed" value="<?=$noOfPouchesSealed?>">
										<input type="hidden" name="noOfSealingMachines" id="noOfSealingMachines" value="<?=$noOfSealingMachines?>">
										<input type="hidden" name="noOfHoursPerShift" id="noOfHoursPerShift" value="<?=$noOfHoursPerShift?>">
										<input type="hidden" name="noOfRetorts" id="noOfRetorts" value="<?=$noOfRetorts?>">
										<input type="hidden" name="noOfShifts" id="noOfShifts" value="<?=$noOfShifts?>">
										<input type="hidden" name="dieselConsumptionOfBoiler" id="dieselConsumptionOfBoiler" value="<?=$dieselConsumptionOfBoiler?>">
										<input type="hidden" name="dieselCostPerLitre" id="dieselCostPerLitre" value="<?=$dieselCostPerLitre?>">
										<input type="hidden" name="electricConsumptionPerDayUnit" id="electricConsumptionPerDayUnit" value="<?=$electricConsumptionPerDayUnit?>">
										<input type="hidden" name="electricCostPerUnit" id="electricCostPerUnit" value="<?=$electricCostPerUnit?>">
										<input type="hidden" name="waterConsumptionPerRetortBatchUnit" id="waterConsumptionPerRetortBatchUnit" value="<?=$waterConsumptionPerRetortBatchUnit?>">
										<input type="hidden" name="generalWaterConsumptionPerDayUnit" id="generalWaterConsumptionPerDayUnit" value="<?=$generalWaterConsumptionPerDayUnit?>">
										<input type="hidden" name="noOfWorkingDaysInMonth" id="noOfWorkingDaysInMonth" value="<?=$noOfWorkingDaysInMonth?>">
										<input type="hidden" name="costPerLitreOfWater" id="costPerLitreOfWater" value="<?=$costPerLitreOfWater?>">
										<input type="hidden" name="costOfCylinder" id="costOfCylinder" value="<?=$costOfCylinder?>">
										<input type="hidden" name="gasPerCylinderPerDay" id="gasPerCylinderPerDay" value="<?=$gasPerCylinderPerDay?>">
										<input type="hidden" name="maintenanceCost" id="maintenanceCost" value="<?=$maintenanceCost?>">
										<input type="hidden" name="consumablesCost" id="consumablesCost" value="<?=$consumablesCost?>">
										<input type="hidden" name="labCost" id="labCost" value="<?=$labCost?>">
										<input type="hidden" name="variableManPowerCostPerDay" id="variableManPowerCostPerDay" value="<?=$variableManPowerCostPerDay?>">
										<input type="hidden" name="totalMktgCostTCost" id="totalMktgCostTCost" value="<?=$totalMktgCostTCost?>">
										<input type="hidden" name="totalTravelCost" id="totalTravelCost" value="<?=$totalTravelCost?>">
										<input type="hidden" name="totalCoordinationCost" id="totalCoordinationCost" value="<?=$totalCoordinationCost?>">
										<input type="hidden" name="advtCostPerMonth" id="advtCostPerMonth" value="<?=$advtCostPerMonth?>">
									</td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productionMatrixRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductionMatrix.php',700,600);"><? }?></td>
											</tr>
										</table>									
									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>		
							<?php
								include "template/boxBR.php"
							?>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			
			</td>
		</tr>	
		<tr>
			<td height="10"></td>
		</tr>
		<!--tr><td height="10" align="center"><a href="IngredientCategory.php" class="link1" title="Click to manage Category">Category</a></td></tr-->
		<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
	</table>
<? 
	if ($iFrameVal=="") { 
?>
	<script language="javascript">
	<!--
	function ensureInFrameset(form)
	{		
		var pLocation = window.parent.location ;	
		var cLocation = window.location.href;			
		if (pLocation==cLocation) {		// Same Location
			document.getElementById("inIFrame").value = 'N';
			form.submit();		
		} else if (pLocation!=cLocation) { // Not in IFrame
			document.getElementById("inIFrame").value = 'Y';
		}
	}
	ensureInFrameset(document.frmProductionMatrix);
	//-->
	</script>
<? 
	}
?>	
</form>
<?
	# Include Template [bottomRightNav.php]
	if ($iFrameVal=='N') require("template/bottomRightNav.php");
?>