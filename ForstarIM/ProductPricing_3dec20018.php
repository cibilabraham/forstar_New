<?php
	$insideIFrame = "Y";
	require("include/include.php");

	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$userId		= $sessObj->getValue("userId");
	$avgMargin	= "";

	$selection 	= "?pageNo=".$p["pageNo"]."&selRateList=".$p["selRateList"];

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
		header("Location: ErrorPageIFrame.php");
		//header("Location: ErrorPage.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/

	# Add New Start 
	if ($p["cmdAddNew"]!="") $addMode = true;
	
	if ($p["cmdCancel"]!="") $addMode = false;	

	#Variable resetting
	if ($p["selProduct"]!="") $selProduct = $p["selProduct"];

	
	#Add a Record
	if ($p["cmdAdd"]!="") {

		$selProduct 	= $p["selProduct"];
		$basicManufCost = $p["basicManufCost"];
		$selBuffer 	= $p["selBuffer"];
		$inclBuffer 	= $p["inclBuffer"];
		$profitMargin 	= $p["profitMargin"];
		$factoryCost 	= $p["factoryCost"];
		$avgDistMgn 	= $p["avgDistMgn"];
		$mgnForScheme 	= $p["mgnForScheme"];
		$noOfPacksFree 	= $p["noOfPacksFree"];
		$mrp 		= $p["mrp"];
		$actualProfitMargin = $p["actualProfitMargin"];
		$onMRP 		= $p["onMRP"];
		$onFactoryCost 	= $p["onFactoryCost"];
		$productPriceRateList = $p["productPriceRateList"];

		#Checking same entry exist in the table
		$sameEntryExist = $productPricingObj->checkEntryExist($selProduct, $productPriceRateList); 

		if ($selProduct!="" && !$sameEntryExist) {
			$productPriceRecIns = $productPricingObj->addProductPrice($selProduct, $basicManufCost, $selBuffer, $inclBuffer, $profitMargin, $factoryCost, $avgDistMgn, $mgnForScheme, $noOfPacksFree, $mrp, $actualProfitMargin, $onMRP, $onFactoryCost, $productPriceRateList, $userId);
			
			if ($productPriceRecIns) $productPriceInsLastId = $databaseConnect->getLastInsertedId();
			
			if ($productPriceInsLastId!="" || $productPriceInsLastId!=0) {
				$hidDistributorRowCount = $p["hidDistributorRowCount"];
				$distStateProdPriceInsLastId = "";
				$prevSelDistributor = "";
				for ($m=1; $m<=$hidDistributorRowCount; $m++) {					

					$selDistributor		= $p["distributorId_".$m];	
					$selProduct		= $p["selProduct"];
					$mrp			= $p["mrp"];
					$productPriceRateListId	= $p["productPriceRateList"];	
					if ($prevSelDistributor!=$selDistributor) {
						$distProductPriceMainRecIns = $productPricingObj->addDistProductPriceMainRec($selDistributor, $selProduct, $mrp, $userId, $productPriceRateListId, $productPriceInsLastId);
						# Last Id
						if ($distProductPriceMainRecIns) {
							$proPriceInsLastId = $databaseConnect->getLastInsertedId();
						}
					}
					$stateId		= $p["stateId_".$m];	
					$costToDistOrStkist	= $p["costToDistOrStkist_".$m];
					$actualDistnCost	= $p["actualDistnCost_".$m];
					$octroi			= $p["octroi_".$m];		
					$freight		= $p["freight_".$m];	
					$insurance		= $p["insurance_".$m];
					$vatOrCst		= $p["vatOrCst_".$m];
					$excise			= $p["excise_".$m];	
					$eduCess		= $p["eduCess_".$m];
					$basicCost		= $p["basicCost_".$m];		
					$costMargin		= $p["costMargin_".$m];	
					$actualProfitMgn 	= $p["actualProfitMgn_".$m];
					$onMrp			= $p["onMrp_".$m];
					$onFactoryCost		= $p["onFactoryCost_".$m];			
					
					if ($proPriceInsLastId!="") {
						$distStateWiseProPriceRecIns = $productPricingObj->addDistProductPriceStateWiseRec($proPriceInsLastId, $stateId, $costToDistOrStkist, $actualDistnCost, $octroi, $freight, $insurance, $vatOrCst, $excise, $eduCess, $basicCost, $costMargin, $actualProfitMgn, $onMrp, $onFactoryCost);
					
						if ($distStateWiseProPriceRecIns) $distStateProdPriceInsLastId = $databaseConnect->getLastInsertedId();
					}

					$hidFieldRowCount = $p["hidFieldRowCount_".$m];

					for ($k=1; $k<=$hidFieldRowCount; $k++) {
						$marginStructureId = $p["marginStructureId_".$m."_".$k];
						$distMarginEntryId = $p["distMarginEntryId_".$m."_".$k];
						$distProfitMargin  = $p["distProfitMargin_".$m."_".$k];
						if ($marginStructureId!="" && $distStateProdPriceInsLastId!="") {
							$distProductPriceEntryRecIns= $productPricingObj->addDistProductPriceEntry($distStateProdPriceInsLastId, $marginStructureId, $distMarginEntryId, $distProfitMargin);
						}
					}

					$prevSelDistributor = $selDistributor;
				}
			}

			if ($productPriceRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddProductPricing);
				$sessObj->createSession("nextPage",$url_afterAddProductPricing.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddProductPricing;
			}
			$productPriceRecIns = false;
		} else {
			$err	 = $msg_failAddProductPricing;
		}
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$productPriceMasterId =	$p["hidProductPriceId"];

		//$selProduct 	= $p["selProduct"];
		$basicManufCost = $p["basicManufCost"];
		$selBuffer 	= $p["selBuffer"];
		$inclBuffer 	= $p["inclBuffer"];
		$profitMargin 	= $p["profitMargin"];
		$factoryCost 	= $p["factoryCost"];
		$avgDistMgn 	= $p["avgDistMgn"];
		$mgnForScheme 	= $p["mgnForScheme"];
		$noOfPacksFree 	= $p["noOfPacksFree"];
		$mrp 		= $p["mrp"];
		$actualProfitMargin = trim($p["actualProfitMargin"]);
		$onMRP 		= $p["onMRP"];
		$onFactoryCost 	= $p["onFactoryCost"];
		$productPriceRateList = $p["productPriceRateList"];
		
		//$hidMrp		= number_format($p["hidMrp"],2,'.','');
		$hidActualProfitMargin	= trim($p["hidActualProfitMargin"]);
		
		# delete from m_dist_product_price tables
		if ($productPriceMasterId!="" && $actualProfitMargin!=$hidActualProfitMargin) {			
			$delDistWiseProductWiseRec = $productPricingObj->deleteDistProductPriceEntryRec($productPriceMasterId);		
		}		

		if ($productPriceMasterId!="" && $mrp!="") {
			$productPriceRecUptd = $productPricingObj->updateProductPrice($productPriceMasterId, $basicManufCost, $selBuffer, $inclBuffer, $profitMargin, $factoryCost, $avgDistMgn, $mgnForScheme, $noOfPacksFree, $mrp, $actualProfitMargin, $onMRP, $onFactoryCost, $productPriceRateList);

			if ($productPriceMasterId!="" && $actualProfitMargin!=$hidActualProfitMargin) {
				$hidDistributorRowCount = $p["hidDistributorRowCount"];
				$distStateProdPriceInsLastId = "";
				$prevSelDistributor = "";
				for ($m=1; $m<=$hidDistributorRowCount; $m++) {	
					$selDistributor		= $p["distributorId_".$m];	
					$selProduct		= $p["hidSelProduct"];		// Edit Mode
					$mrp			= $p["mrp"];
					$productPriceRateListId	= $p["productPriceRateList"];	
					if ($prevSelDistributor!=$selDistributor) {
						$distProductPriceMainRecIns = $productPricingObj->addDistProductPriceMainRec($selDistributor, $selProduct, $mrp, $userId, $productPriceRateListId, $productPriceMasterId);
						# Last Id
						if ($distProductPriceMainRecIns) {
							$proPriceInsLastId = $databaseConnect->getLastInsertedId();
						}
					}
					$stateId		= $p["stateId_".$m];	
					$costToDistOrStkist	= $p["costToDistOrStkist_".$m];
					$actualDistnCost	= $p["actualDistnCost_".$m];
					$octroi			= $p["octroi_".$m];		
					$freight		= $p["freight_".$m];	
					$insurance		= $p["insurance_".$m];
					$vatOrCst		= $p["vatOrCst_".$m];
					$excise			= $p["excise_".$m];	
					$eduCess		= $p["eduCess_".$m];
					$basicCost		= $p["basicCost_".$m];		
					$costMargin		= $p["costMargin_".$m];	
					$actualProfitMgn 	= $p["actualProfitMgn_".$m];
					$onMrp			= $p["onMrp_".$m];
					$onFactoryCost		= $p["onFactoryCost_".$m];			
					
					if ($proPriceInsLastId!="") {
						$distStateWiseProPriceRecIns = $productPricingObj->addDistProductPriceStateWiseRec($proPriceInsLastId, $stateId, $costToDistOrStkist, $actualDistnCost, $octroi, $freight, $insurance, $vatOrCst, $excise, $eduCess, $basicCost, $costMargin, $actualProfitMgn, $onMrp, $onFactoryCost);
					
						if ($distStateWiseProPriceRecIns) $distStateProdPriceInsLastId = $databaseConnect->getLastInsertedId();
					}

					$hidFieldRowCount = $p["hidFieldRowCount_".$m];

					for ($k=1; $k<=$hidFieldRowCount; $k++) {
						$marginStructureId = $p["marginStructureId_".$m."_".$k];
						$distMarginEntryId = $p["distMarginEntryId_".$m."_".$k];
						$distProfitMargin  = $p["distProfitMargin_".$m."_".$k];
						if ($marginStructureId!="" && $distStateProdPriceInsLastId!="") {
							$distProductPriceEntryRecIns= $productPricingObj->addDistProductPriceEntry($distStateProdPriceInsLastId, $marginStructureId, $distMarginEntryId, $distProfitMargin);
						}
					}

					$prevSelDistributor = $selDistributor;
				}
			}

		}
	
		if ($productPriceRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succProductPricingUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateProductPricing.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failProductPricingUpdate;
		}
		$productPriceRecUptd	=	false;
	}


	# Edit  a Record
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$productPriceRec	= $productPricingObj->find($editId);
		$editProductPriceId 	= $productPriceRec[0];
		$selProduct 		= $productPriceRec[1];
		$basicManufCost 	= $productPriceRec[2];
		$selBuffer 		= $productPriceRec[3];
		$inclBuffer 		= $productPriceRec[4];
		$profitMargin 		= $productPriceRec[5];
		$factoryCost 		= $productPriceRec[6];
		$avgDistMgn 		= $productPriceRec[7];
		$mgnForScheme 		= $productPriceRec[8];
		$noOfPacksFree 		= $productPriceRec[9];
		$mrp 			= $productPriceRec[10];
		$actualProfitMargin	= $productPriceRec[11];
		$onMRP 			= $productPriceRec[12];
		$onFactoryCost 		= $productPriceRec[13];
		$productPriceRateList 	= $productPriceRec[14];

		$selProductDisabled = "disabled";
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$productPriceMasterId	=	$p["delId_".$i];

			if ($productPriceMasterId!="") {
				# Delete Dist wise Product wise Recs
				$delDistWiseProductWiseRec = $productPricingObj->deleteDistProductPriceEntryRec($productPriceMasterId);
			
			/*
				# delete from m_dist_product_price tables
				$getDistProductPriceRecs = $productPricingObj->fetchAllDistProductPriceRec($productPriceMasterId);
				foreach ($getDistProductPriceRecs as $dpr) {
					$distProductRecId = $dpr[0];
					# Delete entry table
					$delDistProductPriceEntry = $productPricingObj->deleteDistProdPriceEntry($distProductRecId);
				}
			# Delete main table all rec 
			$distProdPriceRecDel = $productPricingObj->deleteDistProductPriceRec($productPriceMasterId);	
			*/
			// Need to check the selected id is link with any other process
			$productPriceRecDel = $productPricingObj->deleteProductPrice($productPriceMasterId);
			}
		}
		if ($productPriceRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelProductPricing);
			$sessObj->createSession("nextPage",$url_afterDelProductPricing.$selection);
		} else {
			$errDel	=	$msg_failDelProductPricing;
		}
		$productPriceRecDel	=	false;
	}


	#----------------Rate list--------------------	
		if ($g["selRateList"]!="") $selRateList	= $g["selRateList"];
		else if($p["selRateList"]!="") $selRateList	= $p["selRateList"];
		else $selRateList = $productPriceRateListObj->latestRateList();			
	#---------------------------------------------


	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;

	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all DistMarginStructure
	$productPriceResultSetObj = $productPricingObj->fetchAllPagingRecords($selRateList, $offset, $limit);
	$productPriceRecordSize   = $productPriceResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$allProductPriceResultSetObj = $productPricingObj->fetchAllRecords($selRateList);
	$numrows	=  $allProductPriceResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	

	#Product Price Rate List
	$productPriceRateListRecords = $productPriceRateListObj->fetchAllRecords();

	if ($addMode || $editMode) {
		# List all Product matrix
		$productMatrixResultSetObj = $comboMatrixObj->fetchAllRecords();

		# Find the average dist margin
		$avgTotalDistMargin = $productPricingObj->getAvgDistMargin(); 
	}

	if ($selProduct!="") {
		// (basic_manufact_cost, contingency, profit_margin, ideal_factory_cost)
		list($basicManufCost, $contigency, $profitMargin, $factoryCost) = $productPricingObj->getProductMatrixRec($selProduct);

		# for getting Distributor wise product wise records
		$distributorWiseRecords = $productPricingObj->fetchAllDistributorRecs($selProduct);	
	}	

	if ($addMode || $editMode) {
		$productionMatrixMaster = "PMM";	
		$pmmRateList = $manageRateListObj->latestRateList($productionMatrixMaster);
		#Get all Production Matrix master Value
		list($noOfHoursPerShift, $noOfShifts, $noOfRetorts, $noOfSealingMachines, $noOfPouchesSealed, $noOfMinutesForSealing, $noOfDaysInYear, $noOfWorkingDaysInMonth, $noOfHoursPerDay, $noOfMinutesPerHour, $dieselConsumptionOfBoiler, $dieselCostPerLitre, $electricConsumptionPerShift, $electricConsumptionPerDayUnit, $electricCostPerUnit, $waterConsumptionPerRetortBatchUnit, $generalWaterConsumptionPerDayUnit, $costPerLitreOfWater, $noOfCylindersPerShiftPerRetort, $gasPerCylinderPerDay, $costOfCylinder, $maintenanceCostPerRetortPerShift, $maintenanceCost, $consumableCostPerShiftPerMonth, $consumablesCost, $labCostPerRetort, $labCost, $pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost, $holdingCost, $holdingDuration, $adminOverheadChargesCode, $adminOverheadChargesCost, $mProfitMargin, $insuranceCost, $educationCess, $exciseRate, $pickle, $variableManPowerCostPerDay, $fixedManPowerCostPerDay, $totalMktgCostActual, $totalMktgCostIdeal, $totalMktgCostTCost, $totalMktgCostACost, $totalTravelCost, $totalTravelACost, $advtCostPerMonth) = $productionMatrixMasterObj->getProductionMasterValue($pmmRateList);
	} 

	# CST PERCENT From TAX MASTER
	$cstPercent = $taxMasterObj->getBaseCst();
	
	#heading Section
	if ($editMode) $heading	=	$label_editProductPricing;
	else	       $heading	=	$label_addProductPricing;

	$ON_LOAD_PRINT_JS = "libjs/ProductPricing.js"; // For Printing JS in Head section

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmProductPricing" action="ProductPricing.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<? if($err!="" ){?>
	<tr>
		<td height="10" align="center" class="err1"><?=$err;?></td>
	</tr>
	<?}?>
		<?
			if( ($editMode || $addMode) && $disabled) {
		?>
		<tr style="display:none;">
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>
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
												<input type="submit" name="cmdCancel2" class="button" value=" Cancel " onclick="return cancel('ProductPricing.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateProductPricing(document.frmProductPricing);"></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductPricing.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductPricing(document.frmProductPricing);">												</td>
					<?}?>
					</tr>
					<input type="hidden" name="hidProductPriceId" value="<?=$editProductPriceId;?>">
					<tr>
					<!-- First Column -->
						<TD>
						<table width="200">					
					<tr>
			<td nowrap class="fieldName">*Product</td>
			<td nowrap>
                        	<select name="selProduct" id="selProduct" onchange="this.form.submit();" <?=$selProductDisabled?>>
                                <option value="">-- Select --</option>
				<?
				while (($pmr=$productMatrixResultSetObj->getRow())) {
					$productMatrixRecId 	= $pmr[0];
					$productCode		= $pmr[1];
					$productName		= $pmr[2];
					$selected = "";
					if ($selProduct==$productMatrixRecId) $selected = "Selected";
				?>
                            	<option value="<?=$productMatrixRecId?>" <?=$selected?>><?=$productName?></option>
				<? }?>
				</select>
				</td></tr>
					
						<tr>
						  <td class="fieldName" nowrap>Basic Manuf Cost</td>
						  <td class="listing-item">
							<input type="text" name="basicManufCost" id="basicManufCost" size="6" value="<?=$basicManufCost;?>" style="text-align:right;" readonly></td>
						</tr>
						<tr>
						  <td class="fieldName" nowrap>*Buffer</td>
						  <td class="listing-item">
							<select name="selBuffer" id="selBuffer" onchange="includeBuffer();">
								<option value="">--Select--</option>
								<option value="Y" <? if ($selBuffer=='Y') echo "selected";?>>Yes</option>
								<option value="N" <? if ($selBuffer=='N') echo "selected";?>>No</option>
							</select>
						  </td>
						</tr>
						<tr>
						  <td class="fieldName" nowrap>Incl Buffer</td>
						  <td class="listing-item">
							<input type="text" name="inclBuffer" id="inclBuffer" size="6" value="<?=$inclBuffer;?>" style="text-align:right;" readonly>
						</td>
						</tr>
						<tr>
						  <td class="fieldName" nowrap>Profit Margin</td>
						  <td class="listing-item">
							<input type="text" name="profitMargin" id="profitMargin" size="6" value="<?=$profitMargin;?>" style="text-align:right;" readonly></td>
						</tr>
						<tr>
						  <td class="fieldName" nowrap>Factory Cost</td>
						  <td class="listing-item">
							<input type="text" name="factoryCost" id="factoryCost" size="6" value="<?=$factoryCost;?>" style="text-align:right;" readonly></td>
						</tr>
						<tr>
						  <td class="fieldName" nowrap>Av Dist Mgn</td>
						  <td class="listing-item">
							<input type="text" name="avgDistMgn" id="avgDistMgn" size="6" value="<?=$avgDistMgn;?>" style="text-align:right;" readonly></td>
						</tr>						
			</table>
						</TD>
					<!-- First Column End -->
					<!-- Second Column -->
						<TD>
						<table width="200">				
						<tr>
						  <td class="fieldName" nowrap>Mgn For Scheme</td>
						  <td class="listing-item">
							<input type="text" name="mgnForScheme" id="mgnForScheme" size="6" value="<?=$mgnForScheme;?>" style="text-align:right;" readonly></td>
						</tr>
						<tr>
						  <td class="fieldName" nowrap>No of Packs for One Free</td>
						  <td class="listing-item">
							<input type="text" name="noOfPacksFree" id="noOfPacksFree" size="6" value="<?=$noOfPacksFree;?>" style="text-align:right;" readonly></td>
						</tr>
						<tr>
						  <td class="fieldName" nowrap>*MRP</td>
						  <td class="listing-item">
							<input type="text" name="mrp" id="mrp" size="6" value="<?=$mrp;?>" style="text-align:right;" onkeyup="findMarginforScheme();calcDistributorProfitMargin();" autocomplete="off">
							<input type="hidden" name="hidMrp" id="hidMrp" size="6" value="<?=$mrp;?>" style="text-align:right;" autocomplete="off" readonly="true">
						  </td>
						</tr>			
			<tr>
				<td class="fieldName" nowrap>Actual Profit Margin</td>
				<td class="listing-item">
					<input type="text" name="actualProfitMargin" id="actualProfitMargin" size="6" value="<?=$actualProfitMargin;?>" style="text-align:right;" readonly>
					<input type="hidden" name="hidActualProfitMargin" id="hidActualProfitMargin" size="6" value="<?=$actualProfitMargin;?>" style="text-align:right;" readonly>
				</td>
			</tr>
			<tr>
				<td class="fieldName" nowrap>On MRP</td>
				<td class="listing-item" nowrap>
					<input type="text" name="onMRP" id="onMRP" size="6" value="<?=$onMRP;?>" style="text-align:right;" readonly>&nbsp;%
				</td>
			</tr>
			<tr>
				<td class="fieldName" nowrap>On Factory Cost</td>
				<td class="listing-item">
					<input type="text" name="onFactoryCost" id="onFactoryCost" size="6" value="<?=$onFactoryCost;?>" style="text-align:right;" readonly>&nbsp;%
				</td>
			</tr>
			<tr>
			<td class="fieldName" nowrap>*Rate list</td>
			<td>
			<select name="productPriceRateList">
                        <option value="">-- Select --</option>
			<?
			foreach ($productPriceRateListRecords as $prl) {
				$ingredientRateListId	=	$prl[0];
				$rateListName		=	stripSlash($prl[1]);
				$array			=	explode("-",$prl[2]);
				$startDate		=	$array[2]."/".$array[1]."/".$array[0];
				$displayRateList = $rateListName."&nbsp;(".$startDate.")";
				if ($addMode) $rateListId = $selRateList;
				else $rateListId = $productPriceRateList;
				$selected = "";
				if ($rateListId==$ingredientRateListId) $selected = "Selected";
			?>
                      <option value="<?=$ingredientRateListId?>" <?=$selected?>><?=$displayRateList?>
                      </option>
                      <? }?>
                      </select></td>
			</tr>
			</table>
						</TD>					
					<!-- Second Column End-->
					</tr>
					<tr>
						  <td colspan="2" nowrap>
					</td>
			 </td>
			<tr><TD colspan="4" align="center">
			<table cellpadding="1"  width="55%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if (sizeof($distributorWiseRecords)>0) {
		$m = 0;
	?>
	<tr  bgcolor="#f2f2f2" align="center">
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Distributor</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">State</td>
                <td class="listing-head" style="padding-left:10px; padding-right:10px;">Avg Margin</td>
                <td class="listing-head" style="padding-left:10px; padding-right:10px;">Actual Profit Margin</td>
	</tr>
	<?
	foreach ($distributorWiseRecords as $dwr) {
		$m++;
		$distributorId		= $dwr[1];
		$distributorName	= $dwr[2];

		$distMarginStateEntryId = $dwr[3];
		$selStateId		= $dwr[4];
		$stateRec	=	$stateMasterObj->find($selStateId);
		$stateName	=	stripSlash($stateRec[2]);
		
		$avgDistributorMargin	= $dwr[5];
		$distriTransportCost	= $dwr[6];
		$octroiPercent		= $dwr[7];
		$vatPercent		= $dwr[8];
		$freight		= $dwr[9];		

		# List Margin structure Records
		$marginStructureRecords = $productPricingObj->filterStructureEntryRecs($distMarginStateEntryId);	
		
		# (vat/CST, billing form F
		list($taxType, $billingFormF) = $productPricingObj->getDistributorRec($distributorId, $selStateId);
	
		#Find product rec
		list($productExciseRatePercent) = $productPricingObj->getProductExciseRate($selProduct);
		
	?>
	<tr  bgcolor="WHITE"  >
		<td height="25" class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap><?=$distributorName?>

		<input type="hidden" name="distributorId_<?=$m?>" id="distributorId_<?=$m?>" value="<?=$distributorId?>">
		<input type="hidden" name="stateId_<?=$m?>" id="stateId_<?=$m?>" value="<?=$selStateId?>">

		<input type="hidden" name="distriTransportCost_<?=$m?>" id="distriTransportCost_<?=$m?>" value="<?=$distriTransportCost?>">
		<input type="hidden" name="avgDistributorMargin_<?=$m?>" id="avgDistributorMargin_<?=$m?>" value="<?=$avgDistributorMargin?>">
		</td>		
		<td height="25" class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap>
			<?=$stateName?>
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$avgDistributorMargin?>&nbsp;%</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center">
		<input type="text" name="distriActualProfitMargin_<?=$m?>" id="distriActualProfitMargin_<?=$m?>" size="6" style="text-align:right; border:none;" readonly>
		<!-- 	Find the Actual Profit Margin	 -->
		<!--input type="hidden" name="costToDistOrStkist_<?=$m?>" id="costToDistOrStkist_<?=$m?>" size="6" style="text-align:right;"-->
		</td>				
	</tr>
	<tr style="display:none;" bgcolor="white">
	<TD>
<!-- Distributor Wise Product price Actual profit margin calculation -->
		<table>
			<TR>
				<TD>
		<table cellpadding="0" cellspacing="0">
			<?
			$k=0;
			$prevUseAvgDistMagn = 'Y';
			foreach ($marginStructureRecords as $msr) {				
				$k++;
				$distMarginEntryId 	= $msr[0];		
				$marginStructureId 	= $msr[1];
				$distMarginPercent	= $msr[2];
				$marginStructureName	= stripSlash($msr[3]);	
				$priceCalcType		= $msr[4];
				$useAvgDistMagn		= $msr[5];
				$schemeChk		= $msr[6];
				$selSchemeHeadId	= $msr[7];
			?>

		<? if ($prevUseAvgDistMagn!=$useAvgDistMagn) { ?>
		<tr>
<!-- Cost to Dist/Stockist -->
			<td class="fieldName" nowrap></td>
			<TD>
				<input type="hidden" name="costToDistOrStkist_<?=$m?>" id="costToDistOrStkist_<?=$m?>" value="<?=$costToDistOrStkist?>" size="5" style="text-align:right;font-weight:bold;" readonly>			
			</TD>
		</tr>
		<? }?>
		<tr>
			<td class="fieldName" nowrap ><?=$marginStructureName?></td>
			<td class="listing-item">			

				<INPUT TYPE="hidden" NAME="marginStructureId_<?=$m?>_<?=$k?>" id="marginStructureId_<?=$m?>_<?=$k?>" value="<?=$marginStructureId;?>">

				<input type="hidden" name="distMarginEntryId_<?=$m?>_<?=$k?>" value="<?=$distMarginEntryId?>">	

<!-- 	Dist Profit Magn -->
				<INPUT TYPE="hidden" NAME="distProfitMargin_<?=$m?>_<?=$k?>" id="distProfitMargin_<?=$m?>_<?=$k?>" size="5" value="<?=$distProfitMargin;?>" style="text-align:right;" readonly>

				<INPUT TYPE="hidden" NAME="distMarginPercent_<?=$m?>_<?=$k?>" id="distMarginPercent_<?=$m?>_<?=$k?>" size="5" value="<?=$distMarginPercent;?>" style="text-align:right;">

				<INPUT TYPE="hidden" NAME="priceCalcType_<?=$m?>_<?=$k?>" id="priceCalcType_<?=$m?>_<?=$k?>" size="5" value="<?=$priceCalcType;?>" style="text-align:right;">

				<INPUT TYPE="hidden" NAME="useAvgDistMagn_<?=$m?>_<?=$k?>" id="useAvgDistMagn_<?=$m?>_<?=$k?>" size="5" value="<?=$useAvgDistMagn;?>" style="text-align:right;">
			
				<INPUT TYPE="hidden" NAME="schemeChk_<?=$m?>_<?=$k?>" id="schemeChk_<?=$m?>_<?=$k?>" size="5" value="<?=$schemeChk;?>" style="text-align:right;">

				<INPUT TYPE="hidden" NAME="selSchemeHeadId_<?=$m?>_<?=$k?>" id="selSchemeHeadId_<?=$m?>_<?=$k?>" size="5" value="<?=$selSchemeHeadId;?>" style="text-align:right;">
				
			</td>
		</tr>
			
		<? 
			$prevUseAvgDistMagn = $useAvgDistMagn;
		}
		?>
		<input type="hidden" name="hidFieldRowCount_<?=$m?>" id="hidFieldRowCount_<?=$m?>" value="<?=$k?>">
		</table>
		</TD></TR>
		<tr>
			<td class="fieldName" nowrap><!--Act Distn Cost--></td>
			<td class="listing-item">
				<input type="hidden" name="actualDistnCost_<?=$m?>" id="actualDistnCost_<?=$m?>" size="5" value="<?=$actualDistnCost;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap><!--Octroi--></td>
			<td class="listing-item">
				<input type="hidden" name="octroi_<?=$m?>" id="octroi_<?=$m?>" size="5" value="<?=$octroi;?>" style="text-align:right;" readonly>
			</td>
		</tr>	
		<tr>
			<td class="fieldName" nowrap><!--Freight--></td>
			<td class="listing-item">
				<input type="hidden" name="freight_<?=$m?>" id="freight_<?=$m?>" size="5" value="<?=$freight;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap><!--Insurance--></td>
			<td class="listing-item">
				<input type="hidden" name="insurance_<?=$m?>" id="insurance_<?=$m?>" size="5" value="<?=$insurance;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap><!--VAT / CST--></td>
			<td class="listing-item">
				<input type="hidden" name="vatOrCst_<?=$m?>" id="vatOrCst_<?=$m?>" size="5" value="<?=$vatOrCst;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap><!--Excise--></td>
			<td class="listing-item">
				<input type="hidden" name="excise_<?=$m?>" id="excise_<?=$m?>" size="5" value="<?=$excise;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap><!--Educ. Cess--></td>
			<td class="listing-item">
				<input type="hidden" name="eduCess_<?=$m?>" id="eduCess_<?=$m?>" size="5" value="<?=$eduCess;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap><!--Basic Cost--></td>
			<td class="listing-item">
				<input type="hidden" name="basicCost_<?=$m?>" id="basicCost_<?=$m?>" size="5" value="<?=$basicCost;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap><!--Cost Margin--></td>
			<td class="listing-item">
				<input type="hidden" name="costMargin_<?=$m?>" id="costMargin_<?=$m?>" size="5" value="<?=$costMargin;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap><!--Actual Profit Margin--></td>
			<td class="listing-item">
				<input type="hidden" name="actualProfitMgn_<?=$m?>" id="actualProfitMgn_<?=$m?>" size="5" value="<?=$actualProfitMgn;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap>On MRP</td>
			<td class="listing-item">
				<input type="hidden" name="onMrp_<?=$m?>" id="onMrp_<?=$m?>" size="5" value="<?=$onMrp;?>" style="text-align:right;" readonly>&nbsp;%</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap>On Factory Cost</td>
			<td class="listing-item">
				  <input type="hidden" name="onFactoryCost_<?=$m?>" id="onFactoryCost_<?=$m?>" size="5" value="<?=$onFactoryCost;?>" style="text-align:right;" readonly>&nbsp;%
			</td>
		</tr>
<!--  Hidden values -->
		<input type="hidden" name="octroiPercent_<?=$m?>" id="octroiPercent_<?=$m?>" value="<?=$octroiPercent?>">
		<input type="hidden" name="insuranceCost_<?=$m?>" id="insuranceCost_<?=$m?>" value="<?=$insuranceCost?>">
		<input type="hidden" name="taxType_<?=$m?>" id="taxType_<?=$m?>" value="<?=$taxType?>">
		<input type="hidden" name="vatPercent_<?=$m?>" id="vatPercent_<?=$m?>" value="<?=$vatPercent?>">
		<input type="hidden" name="billingFormF_<?=$m?>" id="billingFormF_<?=$m?>" value="<?=$billingFormF?>">
		<input type="hidden" name="hidCstRate_<?=$m?>" id="hidCstRate_<?=$m?>" value="<?=$cstPercent?>">
		<input type="hidden" name="productExciseRatePercent_<?=$m?>" id="productExciseRatePercent_<?=$m?>" value="<?=$productExciseRatePercent?>">
		<input type="hidden" name="educationCess_<?=$m?>" id="educationCess_<?=$m?>" value="<?=$educationCess?>">
		</table>
	</TD>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidDistributorRowCount" id="hidDistributorRowCount" value="<?=$m?>">
	<?
	} else if ($selProduct!="") {
	?>
	<tr bgcolor="white">
		<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoDistMarginRecords;?></td>
	</tr>	
											<?
												}
											?>
										</table>
<input type="hidden" name="hidNumOfDistRecords" id="hidNumOfDistRecords" value="<?=sizeof($distributorWiseRecords);?>">
			</TD></tr>
						<tr>
							<td colspan="2"  height="10" ></td>
											</tr>
			<tr>
				<? if($editMode){?>
				<td colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductPricing.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductPricing(document.frmProductPricing);">							
				</td>
				<?} else{?>
				<td  colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductPricing.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductPricing(document.frmProductPricing);">							
				</td>
				<input type="hidden" name="cmdAddNew" value="1">
				<?}?>
			</tr>
			<tr>
				<td colspan="2"  height="10" ></td>
			</tr>
			</table>								
			</td>
			</tr>
			</table></td>
			</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		<?
			}			
			# Listing Category Starts
		?>
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<tr>
				<td height="10" align="center">
		<table width="200" border="0">
                  <tr>
                    <td class="fieldName" nowrap>Rate List </td>
                    <td>
		<select name="selRateList" id="selRateList" onchange="this.form.submit();">
                <option value="">--Select--</option>
                <?
		foreach ($productPriceRateListRecords as $prl) {
			$ingredientRateListId	=	$prl[0];
			$rateListName		=	stripSlash($prl[1]);			
			$startDate		=	dateFormat($prl[2]);
			$displayRateList = $rateListName."&nbsp;(".$startDate.")";
			$selected = "";
			if($selRateList==$ingredientRateListId) $selected = "Selected";
		?>
                <option value="<?=$ingredientRateListId?>" <?=$selected?>><?=$displayRateList?></option>
                 <? }?>
                </select></td>
		   <? if($add==true){?>
		  	<td><input name="cmdAddNewRateList" type="button" class="button" id="cmdAddNewRateList" value=" Add New Rate List" onclick="parent.moveTab('ProductPriceRateList.php');"></td>
			<!--<td><input name="cmdAddNewRateList" type="submit" class="button" id="cmdAddNewRateList" value=" Add New Rate List" onclick="this.form.action='ProductPriceRateList.php?mode=AddNew'"></td>-->
		<? }?>
                  </tr>
                </table></td>
	</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
				
					<tr>
						<td>
							<!-- Form fields start -->
							<?php	
								$bxHeader="Product Pricing";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Product Pricing  </td>
								</tr>-->
								<tr>
									<td colspan="3" align="center">
	<table width="80%">
		<?
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<?php			
								$entryHead = $heading;
								require("template/rbTop.php");
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?=$heading;?></td>
								</tr>-->
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
										<table cellpadding="0"  width="85%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

											  <td colspan="2" align="center">
												<input type="submit" name="cmdCancel2" class="button" value=" Cancel " onclick="return cancel('ProductPricing.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateProductPricing(document.frmProductPricing);"></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductPricing.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductPricing(document.frmProductPricing);">												</td>
					<?}?>
					</tr>
					<input type="hidden" name="hidProductPriceId" value="<?=$editProductPriceId;?>">
					<tr>
							<td colspan="2"  height="10" ></td>
					</tr>
	<tr><TD colspan="2" align="center">
		<table width="100%">
		<TR>
		<TD valign="top">
			<table align="center" width="100%" cellpadding="0" cellspacing="0">
				<tr>
			<td nowrap class="fieldName">*Product</td>
			<td nowrap>
                        	<select name="selProduct" id="selProduct" onchange="this.form.submit();" <?=$selProductDisabled?>>
                                <option value="">--Select--</option>
				<?
				while ($pmr=$productMatrixResultSetObj->getRow()) {
					$productMatrixRecId 	= $pmr[0];
					$productCode		= $pmr[1];
					$productName		= $pmr[2];
					$selected = "";
					if ($selProduct==$productMatrixRecId) $selected = "Selected";
				?>
                            	<option value="<?=$productMatrixRecId?>" <?=$selected?>><?=$productName?></option>
				<? }?>
				</select>
				</td></tr>
					
						<tr>
						  <td class="fieldName" nowrap>Basic Manuf Cost</td>
						  <td class="listing-item">
							<input type="text" name="basicManufCost" id="basicManufCost" size="6" value="<?=$basicManufCost;?>" style="text-align:right;" readonly></td>
						</tr>
						<tr>
						  <td class="fieldName" nowrap>*Buffer</td>
						  <td class="listing-item">
							<select name="selBuffer" id="selBuffer" onchange="includeBuffer();">
								<option value="">--Select--</option>
								<option value="Y" <? if ($selBuffer=='Y') echo "selected";?>>Yes</option>
								<option value="N" <? if ($selBuffer=='N') echo "selected";?>>No</option>
							</select>
						  </td>
						</tr>
						<tr>
						  <td class="fieldName" nowrap>Incl Buffer</td>
						  <td class="listing-item">
							<input type="text" name="inclBuffer" id="inclBuffer" size="6" value="<?=$inclBuffer;?>" style="text-align:right;" readonly>
						</td>
						</tr>
						<tr>
						  <td class="fieldName" nowrap>Profit Margin</td>
						  <td class="listing-item">
							<input type="text" name="profitMargin" id="profitMargin" size="6" value="<?=$profitMargin;?>" style="text-align:right;" readonly></td>
						</tr>
						<tr>
						  <td class="fieldName" nowrap>Factory Cost</td>
						  <td class="listing-item">
							<input type="text" name="factoryCost" id="factoryCost" size="6" value="<?=$factoryCost;?>" style="text-align:right;" readonly></td>
						</tr>
						<tr>
						  <td class="fieldName" nowrap>Av Dist Mgn</td>
						  <td class="listing-item">
							<input type="text" name="avgDistMgn" id="avgDistMgn" size="6" value="<?=$avgDistMgn;?>" style="text-align:right;" readonly></td>
						</tr>
			<tr>
						  <td class="fieldName" nowrap>Mgn For Scheme</td>
						  <td class="listing-item">
							<input type="text" name="mgnForScheme" id="mgnForScheme" size="6" value="<?=$mgnForScheme;?>" style="text-align:right;" readonly></td>
						</tr>
						<tr>
						  <td class="fieldName" nowrap>No of Packs for One Free</td>
						  <td class="listing-item">
							<input type="text" name="noOfPacksFree" id="noOfPacksFree" size="6" value="<?=$noOfPacksFree;?>" style="text-align:right;" readonly></td>
						</tr>
						<tr>
						  <td class="fieldName" nowrap>*MRP</td>
						  <td class="listing-item">
							<input type="text" name="mrp" id="mrp" size="6" value="<?=$mrp;?>" style="text-align:right;" onkeyup="findMarginforScheme();calcDistributorProfitMargin();" autocomplete="off">
							<input type="hidden" name="hidMrp" id="hidMrp" size="6" value="<?=$mrp;?>" style="text-align:right;" autocomplete="off" readonly="true">
						  </td>
						</tr>			
			<tr>
				<td class="fieldName" nowrap>Actual Profit Margin</td>
				<td class="listing-item">
					<input type="text" name="actualProfitMargin" id="actualProfitMargin" size="6" value="<?=$actualProfitMargin;?>" style="text-align:right;" readonly>
					<input type="hidden" name="hidActualProfitMargin" id="hidActualProfitMargin" size="6" value="<?=$actualProfitMargin;?>" style="text-align:right;" readonly>
				</td>
			</tr>
			<tr>
				<td class="fieldName" nowrap>On MRP</td>
				<td class="listing-item" nowrap>
					<input type="text" name="onMRP" id="onMRP" size="6" value="<?=$onMRP;?>" style="text-align:right;" readonly>&nbsp;%
				</td>
			</tr>
			<tr>
				<td class="fieldName" nowrap>On Factory Cost</td>
				<td class="listing-item">
					<input type="text" name="onFactoryCost" id="onFactoryCost" size="6" value="<?=$onFactoryCost;?>" style="text-align:right;" readonly>&nbsp;%
				</td>
			</tr>
			<tr>
			<td class="fieldName" nowrap>*Rate list</td>
			<td>
			<select name="productPriceRateList">
                        <option value="">-- Select --</option>
			<?
			foreach ($productPriceRateListRecords as $prl) {
				$ingredientRateListId	=	$prl[0];
				$rateListName		=	stripSlash($prl[1]);
				$startDate		=	dateFormat($prl[2]);
				$displayRateList = $rateListName."&nbsp;(".$startDate.")";
				if ($addMode) $rateListId = $selRateList;
				else $rateListId = $productPriceRateList;
				$selected = "";
				if ($rateListId==$ingredientRateListId) $selected = "Selected";
			?>
                      <option value="<?=$ingredientRateListId?>" <?=$selected?>><?=$displayRateList?>
                      </option>
                      <? }?>
                      </select></td>
			</tr>
			</table>
		</TD>
		<td>&nbsp;</td>
		<td valign="top">
		<table cellpadding="1"  width="85%" cellspacing="1" border="0" align="center" class="newspaperType">
	<?
	if (sizeof($distributorWiseRecords)>0) {
		$m = 0;
	?>
	<thead>
	<tr align="center">
		<th style="padding-left:10px; padding-right:10px;">Distributor</th>
		<th style="padding-left:10px; padding-right:10px;">State</th>
                <th style="padding-left:10px; padding-right:10px;">Avg Margin</th>
                <th style="padding-left:10px; padding-right:10px;">Actual Profit Margin</th>
	</tr>
	</thead>
	<tbody>
	<?
	foreach ($distributorWiseRecords as $dwr) {
		$m++;
		$distributorId		= $dwr[1];
		$distributorName	= $dwr[2];

		$distMarginStateEntryId = $dwr[3];
		$selStateId		= $dwr[4];
		$stateRec	=	$stateMasterObj->find($selStateId);
		$stateName	=	stripSlash($stateRec[2]);
		
		$avgDistributorMargin	= $dwr[5];
		$distriTransportCost	= $dwr[6];
		$octroiPercent		= $dwr[7];
		$vatPercent		= $dwr[8];
		$freight		= $dwr[9];		

		# List Margin structure Records
		$marginStructureRecords = $productPricingObj->filterStructureEntryRecs($distMarginStateEntryId);	
		
		# (vat/CST, billing form F
		list($taxType, $billingFormF) = $productPricingObj->getDistributorRec($distributorId, $selStateId);
	
		#Find product rec
		list($productExciseRatePercent) = $productPricingObj->getProductExciseRate($selProduct);
		
	?>
	<tr>
		<td height="25" class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap><?=$distributorName?>

		<input type="hidden" name="distributorId_<?=$m?>" id="distributorId_<?=$m?>" value="<?=$distributorId?>">
		<input type="hidden" name="stateId_<?=$m?>" id="stateId_<?=$m?>" value="<?=$selStateId?>">

		<input type="hidden" name="distriTransportCost_<?=$m?>" id="distriTransportCost_<?=$m?>" value="<?=$distriTransportCost?>">
		<input type="hidden" name="avgDistributorMargin_<?=$m?>" id="avgDistributorMargin_<?=$m?>" value="<?=$avgDistributorMargin?>">
		</td>		
		<td height="25" class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap>
			<?=$stateName?>
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$avgDistributorMargin?>&nbsp;%</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center">
		<input type="text" name="distriActualProfitMargin_<?=$m?>" id="distriActualProfitMargin_<?=$m?>" size="6" style="text-align:right; border:none;" readonly>
		<!-- 	Find the Actual Profit Margin	 -->
		<!--input type="hidden" name="costToDistOrStkist_<?=$m?>" id="costToDistOrStkist_<?=$m?>" size="6" style="text-align:right;"-->
		</td>				
	</tr>
	<tr style="display:none;" bgcolor="white">
	<TD>
<!-- Distributor Wise Product price Actual profit margin calculation -->
		<table>
			<TR>
				<TD>
		<table cellpadding="0" cellspacing="0">
			<?
			$k=0;
			$prevUseAvgDistMagn = 'Y';
			foreach ($marginStructureRecords as $msr) {				
				$k++;
				$distMarginEntryId 	= $msr[0];		
				$marginStructureId 	= $msr[1];
				$distMarginPercent	= $msr[2];
				$marginStructureName	= stripSlash($msr[3]);	
				$priceCalcType		= $msr[4];
				$useAvgDistMagn		= $msr[5];
				$schemeChk		= $msr[6];
				$selSchemeHeadId	= $msr[7];
			?>

		<? if ($prevUseAvgDistMagn!=$useAvgDistMagn) { ?>
		<tr>
<!-- Cost to Dist/Stockist -->
			<td class="fieldName" nowrap></td>
			<TD>
				<input type="hidden" name="costToDistOrStkist_<?=$m?>" id="costToDistOrStkist_<?=$m?>" value="<?=$costToDistOrStkist?>" size="5" style="text-align:right;font-weight:bold;" readonly>			
			</TD>
		</tr>
		<? }?>
		<tr>
			<td class="fieldName" nowrap ><?=$marginStructureName?></td>
			<td class="listing-item">			

				<INPUT TYPE="hidden" NAME="marginStructureId_<?=$m?>_<?=$k?>" id="marginStructureId_<?=$m?>_<?=$k?>" value="<?=$marginStructureId;?>">

				<input type="hidden" name="distMarginEntryId_<?=$m?>_<?=$k?>" value="<?=$distMarginEntryId?>">	

<!-- 	Dist Profit Magn -->
				<INPUT TYPE="hidden" NAME="distProfitMargin_<?=$m?>_<?=$k?>" id="distProfitMargin_<?=$m?>_<?=$k?>" size="5" value="<?=$distProfitMargin;?>" style="text-align:right;" readonly>

				<INPUT TYPE="hidden" NAME="distMarginPercent_<?=$m?>_<?=$k?>" id="distMarginPercent_<?=$m?>_<?=$k?>" size="5" value="<?=$distMarginPercent;?>" style="text-align:right;">

				<INPUT TYPE="hidden" NAME="priceCalcType_<?=$m?>_<?=$k?>" id="priceCalcType_<?=$m?>_<?=$k?>" size="5" value="<?=$priceCalcType;?>" style="text-align:right;">

				<INPUT TYPE="hidden" NAME="useAvgDistMagn_<?=$m?>_<?=$k?>" id="useAvgDistMagn_<?=$m?>_<?=$k?>" size="5" value="<?=$useAvgDistMagn;?>" style="text-align:right;">
			
				<INPUT TYPE="hidden" NAME="schemeChk_<?=$m?>_<?=$k?>" id="schemeChk_<?=$m?>_<?=$k?>" size="5" value="<?=$schemeChk;?>" style="text-align:right;">

				<INPUT TYPE="hidden" NAME="selSchemeHeadId_<?=$m?>_<?=$k?>" id="selSchemeHeadId_<?=$m?>_<?=$k?>" size="5" value="<?=$selSchemeHeadId;?>" style="text-align:right;">
				
			</td>
		</tr>
			
		<? 
			$prevUseAvgDistMagn = $useAvgDistMagn;
		}
		?>
		<input type="hidden" name="hidFieldRowCount_<?=$m?>" id="hidFieldRowCount_<?=$m?>" value="<?=$k?>">
		</table>
		</TD></TR>
		<tr>
			<td class="fieldName" nowrap><!--Act Distn Cost--></td>
			<td class="listing-item">
				<input type="hidden" name="actualDistnCost_<?=$m?>" id="actualDistnCost_<?=$m?>" size="5" value="<?=$actualDistnCost;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap><!--Octroi--></td>
			<td class="listing-item">
				<input type="hidden" name="octroi_<?=$m?>" id="octroi_<?=$m?>" size="5" value="<?=$octroi;?>" style="text-align:right;" readonly>
			</td>
		</tr>	
		<tr>
			<td class="fieldName" nowrap><!--Freight--></td>
			<td class="listing-item">
				<input type="hidden" name="freight_<?=$m?>" id="freight_<?=$m?>" size="5" value="<?=$freight;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap><!--Insurance--></td>
			<td class="listing-item">
				<input type="hidden" name="insurance_<?=$m?>" id="insurance_<?=$m?>" size="5" value="<?=$insurance;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap><!--VAT / CST--></td>
			<td class="listing-item">
				<input type="hidden" name="vatOrCst_<?=$m?>" id="vatOrCst_<?=$m?>" size="5" value="<?=$vatOrCst;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap><!--Excise--></td>
			<td class="listing-item">
				<input type="hidden" name="excise_<?=$m?>" id="excise_<?=$m?>" size="5" value="<?=$excise;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap><!--Educ. Cess--></td>
			<td class="listing-item">
				<input type="hidden" name="eduCess_<?=$m?>" id="eduCess_<?=$m?>" size="5" value="<?=$eduCess;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap><!--Basic Cost--></td>
			<td class="listing-item">
				<input type="hidden" name="basicCost_<?=$m?>" id="basicCost_<?=$m?>" size="5" value="<?=$basicCost;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap><!--Cost Margin--></td>
			<td class="listing-item">
				<input type="hidden" name="costMargin_<?=$m?>" id="costMargin_<?=$m?>" size="5" value="<?=$costMargin;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap><!--Actual Profit Margin--></td>
			<td class="listing-item">
				<input type="hidden" name="actualProfitMgn_<?=$m?>" id="actualProfitMgn_<?=$m?>" size="5" value="<?=$actualProfitMgn;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap>On MRP</td>
			<td class="listing-item">
				<input type="hidden" name="onMrp_<?=$m?>" id="onMrp_<?=$m?>" size="5" value="<?=$onMrp;?>" style="text-align:right;" readonly>&nbsp;%</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap>On Factory Cost</td>
			<td class="listing-item">
				  <input type="hidden" name="onFactoryCost_<?=$m?>" id="onFactoryCost_<?=$m?>" size="5" value="<?=$onFactoryCost;?>" style="text-align:right;" readonly>&nbsp;%
			</td>
		</tr>
<!--  Hidden values -->
		<input type="hidden" name="octroiPercent_<?=$m?>" id="octroiPercent_<?=$m?>" value="<?=$octroiPercent?>">
		<input type="hidden" name="insuranceCost_<?=$m?>" id="insuranceCost_<?=$m?>" value="<?=$insuranceCost?>">
		<input type="hidden" name="taxType_<?=$m?>" id="taxType_<?=$m?>" value="<?=$taxType?>">
		<input type="hidden" name="vatPercent_<?=$m?>" id="vatPercent_<?=$m?>" value="<?=$vatPercent?>">
		<input type="hidden" name="billingFormF_<?=$m?>" id="billingFormF_<?=$m?>" value="<?=$billingFormF?>">
		<input type="hidden" name="hidCstRate_<?=$m?>" id="hidCstRate_<?=$m?>" value="<?=$cstPercent?>">
		<input type="hidden" name="productExciseRatePercent_<?=$m?>" id="productExciseRatePercent_<?=$m?>" value="<?=$productExciseRatePercent?>">
		<input type="hidden" name="educationCess_<?=$m?>" id="educationCess_<?=$m?>" value="<?=$educationCess?>">
		</table>
	</TD>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidDistributorRowCount" id="hidDistributorRowCount" value="<?=$m?>">
	<?
	} else if ($selProduct!="") {
	?>
	<tr bgcolor="white">
		<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoDistMarginRecords;?></td>
	</tr>	
											<?
												}
											?>
	</tbody>
	<input type="hidden" name="hidNumOfDistRecords" id="hidNumOfDistRecords" value="<?=sizeof($distributorWiseRecords);?>">
	</table>
		</td>
		</TR>
		</table>
	</TD></tr>	
		<tr>
				<td colspan="2"  height="10" ></td>
		</tr>
			<tr>
				<? if($editMode){?>
				<td colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductPricing.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateProductPricing(document.frmProductPricing);">							
				</td>
				<?} else{?>
				<td  colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('ProductPricing.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateProductPricing(document.frmProductPricing);">							
				</td>
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
				<!-- Form fields end   -->			</td>
		</tr>	
		<?
			}			
			# Listing Category Starts
		?>
	</table>
									</td>
								</tr>

								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productPriceRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductPricing.php?selRateList=<?=$selRateList?>',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?
									if($errDel!="")
									{
								?>
								<tr>
									<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?
									}
								?>
								<tr>
									<td width="1" ></td>
									<td colspan="2" >
		<table cellpadding="2"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
		if ($productPriceRecordSize) {
			$i	=	0;
		?>
		<thead>
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
      				$nav.= " <a href=\"ProductPricing.php?pageNo=$page&selRateList=$selRateList\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductPricing.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductPricing.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
	<? }?>
	<tr align="center">
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>
		<th style="padding-left:10px; padding-right:10px;">Product</th>
		<th style="padding-left:10px; padding-right:10px;">MRP</th>
		<th style="padding-left:10px; padding-right:10px;">Actual <br>Profit Margin</th>		
		<? if($edit==true){?>
			<th class="listing-head">&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
	<?php
		while ($ppr=$productPriceResultSetObj->getRow()) {
			$i++;
			$productPriceMasterId = $ppr[0];			
			$productName	= $ppr[14];	
			$productMRP	= $ppr[10];	
			$productActualProfitMgn = $ppr[11];
	?>
	<tr>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$productPriceMasterId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$productName;?></td>	
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$productMRP;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$productActualProfitMgn;?></td>		
	<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$productPriceMasterId;?>,'editId');this.form.action='ProductPricing.php';" ></td>
	<? }?>
	</tr>
	<?
		}
	?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
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
      				$nav.= " <a href=\"ProductPricing.php?pageNo=$page&selRateList=$selRateList\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"ProductPricing.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"ProductPricing.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
	<? }?>
	</tbody>
											<?
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
										</table>
	</td>
	</tr>
	<tr>
		<td colspan="3" height="5">
	<input type="hidden" name="avgTotalDistMargin" id="avgTotalDistMargin" value="<?=$avgTotalDistMargin?>">
	<input type="hidden" name="hidContigency" id="hidContigency" value="<?=$contigency?>">
	<input type="hidden" name="hidProfitMargin" id="hidProfitMargin" value="<?=$profitMargin?>">
	<input type="hidden" name="hidSelProduct" id="hidSelProduct" value="<?=$selProduct?>">	
	</td>
	</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$productPriceRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintProductPricing.php?selRateList=<?=$selRateList?>',700,600);"><? }?></td>
											</tr>
										</table>									</td>
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
				<!-- Form fields end   -->			</td>
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>
<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
	</table>
<? if ($addMode || $editMode) {?>
	<script>
		// Include Buffer
		includeBuffer()
		// calculate average dist margin for Each product
		calcAvgProductMargin();
		// Calculate Distributor wise Profit Margin
		calcDistributorProfitMargin();
	</script>
<? }?>
<?php 
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
	ensureInFrameset(document.frmProductPricing);
	//-->
	</script>
<?php 
	}
?>
	</form>
<?
	# Include Template [bottomRightNav.php]
	if ($iFrameVal=='N') require("template/bottomRightNav.php");
?>
