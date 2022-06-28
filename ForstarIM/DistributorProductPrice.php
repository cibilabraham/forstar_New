<?php
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

	# Add New Start 
	if ($p["cmdAddNew"]!="") $addMode = true;
	
	if ($p["cmdCancel"]!="") $addMode = false;

		
	#Add a Rec
	if ($p["cmdAdd"]!="") {

		$selDistributor		= $p["selDistributor"];	
		$selProduct		= $p["selProduct"];
		$mrp			= $p["mrp"];		
		$costToDistOrStkist	= $p["costToDistOrStkist"];
		$actualDistnCost	= $p["actualDistnCost"];
		$octroi			= $p["octroi"];		
		$freight		= $p["freight"];	
		$insurance		= $p["insurance"];
		$vatOrCst		= $p["vatOrCst"];
		$excise			= $p["excise"];	
		$eduCess		= $p["eduCess"];
		$basicCost		= $p["basicCost"];		
		$costMargin		= $p["costMargin"];	
		$actualProfitMgn 	= $p["actualProfitMgn"];
		$onMrp			= $p["onMrp"];
		$onFactoryCost		= $p["onFactoryCost"];
		$productPriceRateListId	= $p["productPriceRateList"];

		#Checking same entry exist in the table
		$sameEntryExist = $distProductPriceObj->checkEntryExist($selDistributor, $selProduct); 

		if ($selDistributor!="" &&  $selProduct!="" && !$sameEntryExist) {
			$distProductPriceRecIns = $distProductPriceObj->addDistProductPriceRec($selDistributor, $selProduct, $mrp, $costToDistOrStkist, $actualDistnCost, $octroi, $freight, $insurance, $vatOrCst, $excise, $eduCess, $basicCost, $costMargin, $actualProfitMgn, $onMrp, $onFactoryCost, $userId, $productPriceRateListId);

			#Find the Last inserted Id From m_distributor_margin
			$lastId = $databaseConnect->getLastInsertedId();

			$hidFieldRowCount	= $p["hidFieldRowCount"];
			for ($i=1; $i<=$hidFieldRowCount; $i++) {
				$marginStructureId = $p["marginStructureId_".$i];
				$distMarginEntryId = $p["distMarginEntryId_".$i];	
				$distProfitMargin = $p["distProfitMargin_".$i];
				if ($marginStructureId!="" && $lastId!="") {
					$distProductPriceEntryRecIns= $distProductPriceObj->addDistProductPriceEntry($lastId, $marginStructureId, $distMarginEntryId, $distProfitMargin);
				}
			}

			if ($distProductPriceRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddDistProductPrice);
				$sessObj->createSession("nextPage",$url_afterAddDistProductPrice.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddDistProductPrice;
			}
			$distProductPriceRecIns = false;
		} else {
			$err	 = $msg_failAddDistProductPrice;
		}
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$distProdPriceRecId =	$p["hidDistProductPriceId"];

		//$selDistributor	= $p["selDistributor"];	
		//$selProduct		= $p["selProduct"];
		$mrp			= $p["mrp"];		
		$costToDistOrStkist	= $p["costToDistOrStkist"];
		$actualDistnCost	= $p["actualDistnCost"];
		$octroi			= $p["octroi"];		
		$freight		= $p["freight"];	
		$insurance		= $p["insurance"];
		$vatOrCst		= $p["vatOrCst"];
		$excise			= $p["excise"];	
		$eduCess		= $p["eduCess"];
		$basicCost		= $p["basicCost"];		
		$costMargin		= $p["costMargin"];	
		$actualProfitMgn 	= $p["actualProfitMgn"];
		$onMrp			= $p["onMrp"];
		$onFactoryCost		= $p["onFactoryCost"];
		
		if ($distProdPriceRecId!="" && $costToDistOrStkist!="") {
			$distProdPriceRecUptd = $distProductPriceObj->updateDistProductPriceRec($distProdPriceRecId, $mrp, $costToDistOrStkist, $actualDistnCost, $octroi, $freight, $insurance, $vatOrCst, $excise, $eduCess, $basicCost, $costMargin, $actualProfitMgn, $onMrp, $onFactoryCost);

			$hidFieldRowCount = $p["hidFieldRowCount"];
			$distMarginEntryId = "";
			for ($i=1; $i<=$hidFieldRowCount; $i++) {

				$distProductPriceEntryId = $p["distProductPriceEntryId_".$i];

				$marginStructureId = $p["marginStructureId_".$i];
				$distMarginEntryId = $p["distMarginEntryId_".$i];	
				$distProfitMargin = $p["distProfitMargin_".$i];

				if ($marginStructureId!="" && $distProdPriceRecId!="" && $distProductPriceEntryId=="") {					
					$distProductPriceEntryRecIns= $distProductPriceObj->addDistProductPriceEntry($distProdPriceRecId, $marginStructureId, $distMarginEntryId, $distProfitMargin);
				} else if ($marginStructureId!="" && $distProdPriceRecId!="" && $distProductPriceEntryId!="") {
					$updateDistProdPriceEntryRec = $distProductPriceObj->updateDistProdPriceEntryRec($distProductPriceEntryId, $distProfitMargin);
				}
			}
		}
	
		if ($distProdPriceRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succDistProductPriceUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateDistProductPrice.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failDistProductPriceUpdate;
		}
		$distProdPriceRecUptd	=	false;
	}


	# Edit  a Record
	if ($p["editId"]!="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$distProdPriceRec	= $distProductPriceObj->find($editId);
		$editDistProdPriceRecId = $distProdPriceRec[0];
		$selDistributor		= $distProdPriceRec[1];
		$selProduct		= $distProdPriceRec[2];
		$mrp			= $distProdPriceRec[3];
		$costToDistOrStkist	= $distProdPriceRec[4];
		$actualDistnCost	= $distProdPriceRec[5];
		$octroi			= $distProdPriceRec[6];
		$freight		= $distProdPriceRec[7];
		$insurance		= $distProdPriceRec[8];
		$vatOrCst		= $distProdPriceRec[9];
		$excise			= $distProdPriceRec[10];
		$eduCess		= $distProdPriceRec[11];
		$basicCost		= $distProdPriceRec[12];
		$costMargin		= $distProdPriceRec[13];
		$actualProfitMgn 	= $distProdPriceRec[14];
		$onMrp			= $distProdPriceRec[15];
		$onFactoryCost		= $distProdPriceRec[16];

		$disabled 	= "disabled";
	}


	# Delete a Record
	if ( $p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$distProdPriceRecId	=	$p["delId_".$i];

			if ($distProdPriceRecId!="") {
				#Deleting from Entry table
				$distProdPriceEntryRecDel = $distProductPriceObj->delDistProdPriceEntryRec($distProdPriceRecId);
				#del main table
				// Need to check the selected id is link with any other process
				$distProdPriceRecDel = $distProductPriceObj->deleteDistProductPriceRec($distProdPriceRecId);
			}
		}
		if ($distProdPriceRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelDistProductPrice);
			$sessObj->createSession("nextPage",$url_afterDelDistProductPrice.$selection);
		} else {
			$errDel	=	$msg_failDelDistProductPrice;
		}
		$distProdPriceRecDel	=	false;
	}

/*
	#----------------Rate list--------------------	
		if ($g["selRateList"]!="") {
			$selRateList	= $g["selRateList"];
		} else if($p["selRateList"]!="") 	{
			$selRateList	= $p["selRateList"];
		} else {
			$selRateList = $distMarginRateListObj->latestRateList();	
		}
	#------------------------------------
*/

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") {
		$pageNo=$p["pageNo"];
	} else if ($g["pageNo"] != "") {
		$pageNo=$g["pageNo"];
	} else {
		$pageNo=1;
	}
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all DistMarginStructure
	$distProductPriceResultSetObj = $distProductPriceObj->fetchAllPagingRecords($offset, $limit);
	$distProPriceRecordSize   = $distProductPriceResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$allDistProdPriceResultSetObj = $distProductPriceObj->fetchAllRecords();
	$numrows	=  $allDistProdPriceResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	
/*
	#Distributor Margin Rate List
	$distMarginRateListRecords = $distMarginRateListObj->fetchAllRecords();
	
	#Get all Production Matrix master Value
	if ($addMode || $editMode) {	

		#List All Margin Structure (Head) Record
		//$marginStructureRecords = $marginStructureObj->fetchAllRecords();
	}

	if ($p["selDistributor"]!="") $selDistributor = $p["selDistributor"];
	if ($selDistributor) {
		$distributorRec = $distributorMasterObj->find($selDistributor);
		$billingFormF	= $distributorRec[16];
	}
	
// 	if ($editMode) {
// 		#List All Margin Structure (Head) Record (Left join from Structure table)
// 		$marginStructureRecords = $distProductPriceObj->filterStructureEntryRecs($editDistProdPriceRecId);
// 	} else {
// 		#List All Margin Structure (Head) Record
// 		$marginStructureRecords = $marginStructureObj->fetchAllRecords();
// 	}

*/

	if ($addMode || $editMode) {
		# List all Distributor
		$distributorResultSetObj = $distributorMasterObj->fetchAllRecords();
		
		if ($p["selDistributor"]!="") $selDistributor = $p["selDistributor"];
		
		# find the Latestes rate list of Dist Margin 
		$selDistMarginRateList = $distMarginRateListObj->latestRateList(); 

		# List all Product based on Dist $productRecords
		$productResultSetObj = $distProductPriceObj->filterProductRecords($selDistributor, $selDistMarginRateList);
		
		if ($selDistributor!="") {
			// (vat/CST, billing form F
			list($taxType, $billingFormF) = $distProductPriceObj->getDistributorRec($selDistributor);
		}


		if ($p["selProduct"]!="") $selProduct = $p["selProduct"];

		if ($selDistributor!="" && $selProduct!="") {
			# find the Latestes rate list of Dist Margin 
			//$selDistMarginRateList = $distMarginRateListObj->latestRateList(); 
			# Filter Margin Struct Records
			if ($editMode!="") {
				$marginStructureRecords = $distProductPriceObj->getDistMagnStructEntryRecs($selDistributor, $selProduct, $selDistMarginRateList);
			} else {
				$marginStructureRecords = $distProductPriceObj->filterStructureEntryRecs($selDistributor, $selProduct, $selDistMarginRateList);
			}
			#Find dist magn Struct Main rec
			list($octroiPercent, $vatPercent, $freight) = $distProductPriceObj->getDistMgnStructRec($selDistributor, $selProduct, $selDistMarginRateList);
		}
		
		if ($selProduct!="") {		
			#Product Price Rate List
			$productPriceRateList = $productPriceRateListObj->latestRateList();
			
			# Find the Product Price
			list($mrp, $factoryCost, $factoryProfitMargin) = $distProductPriceObj->getProductPriceRec($selProduct, $productPriceRateList);
			#Find product rec
			list($productExciseRatePercent) = $distProductPriceObj->getProductMatrixRec($selProduct);
		}


		#Get all Production Matrix master Value
		list($noOfHoursPerShift, $noOfShifts, $noOfRetorts, $noOfSealingMachines, $noOfPouchesSealed, $noOfMinutesForSealing, $noOfDaysInYear, $noOfWorkingDaysInMonth, $noOfHoursPerDay, $noOfMinutesPerHour, $dieselConsumptionOfBoiler, $dieselCostPerLitre, $electricConsumptionPerShift, $electricConsumptionPerDayUnit, $electricCostPerUnit, $waterConsumptionPerRetortBatchUnit, $generalWaterConsumptionPerDayUnit, $costPerLitreOfWater, $noOfCylindersPerShiftPerRetort, $gasPerCylinderPerDay, $costOfCylinder, $maintenanceCostPerRetortPerShift, $maintenanceCost, $consumableCostPerShiftPerMonth, $consumablesCost, $labCostPerRetort, $labCost, $pouchesTestPerBatchUnit, $pouchesTestPerBatchTCost, $holdingCost, $holdingDuration, $adminOverheadChargesCode, $adminOverheadChargesCost, $profitMargin, $insuranceCost, $educationCess, $exciseRate, $pickle, $variableManPowerCostPerDay, $fixedManPowerCostPerDay, $totalMktgCostActual, $totalMktgCostIdeal, $totalMktgCostTCost, $totalMktgCostACost, $totalTravelCost, $totalTravelACost, $advtCostPerMonth) = $productionMatrixMasterObj->getProductionMasterValue();			
	}

	# CST PERCENT From TAX MASTER
	$cstPercent = $taxMasterObj->getBaseCst();

	#heading Section
	if ($editMode) $heading	= $label_editDistProductPrice;
	else	       $heading	= $label_addDistProductPrice;

	$ON_LOAD_PRINT_JS	= "libjs/DistributorProductPrice.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmDistributorProductPrice" action="DistributorProductPrice.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="85%" >
	<tr>
		<td height="10" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
		<td></tr>
		<?
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%"  bgcolor="#D3D3D3">
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
	<input type="submit" name="cmdCancel2" class="button" value=" Cancel " onclick="return cancel('DistributorProductPrice.php');">&nbsp;&nbsp;
	<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateDistributorProductPrice(document.frmDistributorProductPrice);"></td>
	<?} else{?>
	<td  colspan="2" align="center">
	<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistributorProductPrice.php');">&nbsp;&nbsp;						<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateDistributorProductPrice(document.frmDistributorProductPrice);">
	</td>
	<?}?>
	</tr>
	<input type="hidden" name="hidDistProductPriceId" value="<?=$editDistProdPriceRecId;?>">
	<tr>
	<td colspan="2" nowrap class="fieldName" >
		<table width="200">
		<tr>
		<td nowrap class="fieldName">*Distributor</td>
		<td nowrap>
                <select name="selDistributor" id="selDistributor" onchange="this.form.submit();" <?=$disabled?>>
                <option value="">-- Select --</option>
		<?	
		while ($dr=$distributorResultSetObj->getRow()) {
			$distributorId	 = $dr[0];
			$distributorCode = stripSlash($dr[1]);
			$distributorName = stripSlash($dr[2]);	
			$selected = "";
			if ($selDistributor==$distributorId) $selected = "selected";	
		?>
                <option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>
		<? }?>
		</select>
		</td></tr>
		<tr>
		<td nowrap class="fieldName">*Product</td>
		<td nowrap>
                <select name="selProduct" id="selProduct" onchange="this.form.submit();" <?=$disabled?>>
                <option value="">-- Select --</option>
		<?
		 while ($pmr=$productResultSetObj->getRow()) {
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
			<td class="fieldName" nowrap>MRP</td>
			<td class="listing-item">
			<input type="text" name="mrp" id="mrp" size="5" value="<?=$mrp;?>" style="text-align:right;border:none;" readonly></td>
			</tr>
		<tr>
		<TD colspan="2">
		<table>
			<?
			$m=0;
			$prevUseAvgDistMagn = 'Y';
			foreach ($marginStructureRecords as $msr) {
				$m++;
				$distMarginEntryId 	= $msr[0];		
				$marginStructureId 	= $msr[1];
				$distMarginPercent	= $msr[2];
				$marginStructureName	= stripSlash($msr[3]);	
				$priceCalcType		= $msr[4];
				$useAvgDistMagn		= $msr[5];
				$schemeChk		= $msr[6];
				$selSchemeHeadId	= $msr[7];
	
				// Edit Mode			
				$distProductPriceEntryId = $msr[8];
				$distProfitMargin	 = $msr[9];

				//Rec
				/*
				$rec = $marginStructureRecords[$m-2];
				//echo "Percent=".$distMarginPercent."Rec=".$rec[2]."<br>";
				if ($m==1 && $priceCalcType=='MD') {
					$calcFirstRow = $mrp*(1-($distMarginPercent/100));
				}
				*/
				
			?>

		<? if ($prevUseAvgDistMagn!=$useAvgDistMagn) { ?>
		<tr>
			<td class="fieldName" nowrap>Cost to Dist/Stockist</td>
			<TD>
				<input type="text" name="costToDistOrStkist" id="costToDistOrStkist" value="<?=$costToDistOrStkist?>" size="5" style="text-align:right;font-weight:bold;" readonly>			
			</TD>
		</tr>
		<? }?>
		<tr>
			<td class="fieldName" nowrap ><?=$marginStructureName?></td>
			<td class="listing-item">
			
				<INPUT TYPE="hidden" NAME="distProductPriceEntryId_<?=$m?>" id="distProductPriceEntryId_<?=$m?>" value="<?=$distProductPriceEntryId;?>">

				<INPUT TYPE="hidden" NAME="marginStructureId_<?=$m?>" id="marginStructureId_<?=$m?>" value="<?=$marginStructureId;?>">

				<input type="hidden" name="distMarginEntryId_<?=$m?>" value="<?=$distMarginEntryId?>">	

				<INPUT TYPE="text" NAME="distProfitMargin_<?=$m?>" id="distProfitMargin_<?=$m?>" size="5" value="<?=$distProfitMargin;?>" style="text-align:right;" readonly>

				<INPUT TYPE="hidden" NAME="distMarginPercent_<?=$m?>" id="distMarginPercent_<?=$m?>" size="5" value="<?=$distMarginPercent;?>" style="text-align:right;">

				<INPUT TYPE="hidden" NAME="priceCalcType_<?=$m?>" id="priceCalcType_<?=$m?>" size="5" value="<?=$priceCalcType;?>" style="text-align:right;">

				<INPUT TYPE="hidden" NAME="useAvgDistMagn_<?=$m?>" id="useAvgDistMagn_<?=$m?>" size="5" value="<?=$useAvgDistMagn;?>" style="text-align:right;">
			
				<INPUT TYPE="hidden" NAME="schemeChk_<?=$m?>" id="schemeChk_<?=$m?>" size="5" value="<?=$schemeChk;?>" style="text-align:right;">

				<INPUT TYPE="hidden" NAME="selSchemeHeadId_<?=$m?>" id="selSchemeHeadId_<?=$m?>" size="5" value="<?=$selSchemeHeadId;?>" style="text-align:right;">
				
			</td>
		</tr>
			
		<? 
			$prevUseAvgDistMagn = $useAvgDistMagn;
		}
		?>
		<input type="hidden" name="hidFieldRowCount" id="hidFieldRowCount" value="<?=$m?>">
		</table>
		</TD>
		</tr>
		<tr>
			<td class="fieldName" nowrap>Act Distn Cost</td>
			<td class="listing-item">
				<input type="text" name="actualDistnCost" id="actualDistnCost" size="5" value="<?=$actualDistnCost;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap>Octroi</td>
			<td class="listing-item">
				<input type="text" name="octroi" id="octroi" size="5" value="<?=$octroi;?>" style="text-align:right;" readonly>
			</td>
		</tr>	
		<tr>
			<td class="fieldName" nowrap>Freight</td>
			<td class="listing-item">
				<input type="text" name="freight" id="freight" size="5" value="<?=$freight;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap>Insurance</td>
			<td class="listing-item">
				<input type="text" name="insurance" id="insurance" size="5" value="<?=$insurance;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap>VAT / CST</td>
			<td class="listing-item">
				<input type="text" name="vatOrCst" id="vatOrCst" size="5" value="<?=$vatOrCst;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap>Excise</td>
			<td class="listing-item">
				<input type="text" name="excise" id="excise" size="5" value="<?=$excise;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap>Educ. Cess</td>
			<td class="listing-item">
				<input type="text" name="eduCess" id="eduCess" size="5" value="<?=$eduCess;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap>Basic Cost</td>
			<td class="listing-item">
				<input type="text" name="basicCost" id="basicCost" size="5" value="<?=$basicCost;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap>Cost Margin</td>
			<td class="listing-item">
				<input type="text" name="costMargin" id="costMargin" size="5" value="<?=$costMargin;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap>Actual Profit Margin</td>
			<td class="listing-item">
				<input type="text" name="actualProfitMgn" id="actualProfitMgn" size="5" value="<?=$actualProfitMgn;?>" style="text-align:right;" readonly>
			</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap>On MRP</td>
			<td class="listing-item">
				<input type="text" name="onMrp" id="onMrp" size="5" value="<?=$onMrp;?>" style="text-align:right;" readonly>&nbsp;%</td>
		</tr>
		<tr>
			<td class="fieldName" nowrap>On Factory Cost</td>
			<td class="listing-item">
				  <input type="text" name="onFactoryCost" id="onFactoryCost" size="5" value="<?=$onFactoryCost;?>" style="text-align:right;" readonly>&nbsp;%
			</td>
		</tr>
		<!--tr>
			<td class="fieldName" nowrap>*Rate list</td>
			<td>
			<select name="distMarginRateList">
                        <option value="">-- Select --</option>
			<?
			foreach ($distMarginRateListRecords as $prl) {
				$ingredientRateListId	=	$prl[0];
				$rateListName		=	stripSlash($prl[1]);
				$array			=	explode("-",$prl[2]);
				$startDate		=	$array[2]."/".$array[1]."/".$array[0];
				$displayRateList = $rateListName."&nbsp;(".$startDate.")";
				if ($addMode) $rateListId = $selRateList;
				else $rateListId = $distMarginRateListId;
				$selected = "";
				if ($rateListId==$ingredientRateListId) $selected = "Selected";
			?>
                      <option value="<?=$ingredientRateListId?>" <?=$selected?>><?=$displayRateList?>
                      </option>
                      <? }?>
                                            </select></td>
						</tr-->
			</table></td>
			</td>
			<tr>
				<td colspan="2"  height="10" ></td>
			</tr>
			<tr>
				<? if($editMode){?>
				<td colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistributorProductPrice.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateDistributorProductPrice(document.frmDistributorProductPrice);">							
				</td>
				<?} else{?>
				<td  colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('DistributorProductPrice.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateDistributorProductPrice(document.frmDistributorProductPrice);">							
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
		<!--table width="200" border="0">
                  <tr>
                    <td class="fieldName" nowrap>Rate List </td>
                    <td>
		<select name="selRateList" id="selRateList" onchange="this.form.submit();">
                <option value="">-- Select --</option>
                <?
		foreach ($distMarginRateListRecords as $prl) {
			$ingredientRateListId	=	$prl[0];
			$rateListName		=	stripSlash($prl[1]);
			$array			=	explode("-",$prl[2]);
			$startDate		=	$array[2]."/".$array[1]."/".$array[0];
			$displayRateList = $rateListName."&nbsp;(".$startDate.")";
			$selected = "";
			if($selRateList==$ingredientRateListId) $selected = "Selected";
		?>
                <option value="<?=$ingredientRateListId?>" <?=$selected?>><?=$displayRateList?></option>
                 <? }?>
                </select></td>
		   <? if($add==true){?>
		  	<td><input name="cmdAddNewRateList" type="submit" class="button" id="cmdAddNewRateList" value=" Add New Rate List" onclick="this.form.action='DistMarginRateList.php?mode=AddNew'"></td>
		<? }?>
                  </tr>
                </table--></td>
	</tr>
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%"  bgcolor="#D3D3D3">
				
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Distributor Wise Product Pricing  </td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>

								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$distProPriceRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintDistributorProductPrice.php',700,600);"><? }?></td>
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
		<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if ($distProPriceRecordSize) {
			$i	=	0;
		?>
<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="9" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"DistributorProductPrice.php?pageNo=$page&selRateList=$selRateList\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DistributorProductPrice.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"DistributorProductPrice.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\">>></a> ";
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
	<tr  bgcolor="#f2f2f2" align="center">
	<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); "></td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Distributor</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Product</td>	
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">MRP</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Cost to Dist/Stockist</td>	
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Actual Profit Margin</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">On MRP (%)</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">On Factory Cost(%)</td>
	<? if($edit==true){?>
		<td class="listing-head"></td>
	<? }?>
	</tr>
	<?
		while ($dpr=$distProductPriceResultSetObj->getRow()) {
			$i++;
			$distProdPriceRecId	= $dpr[0];
			$distributorName	= $dpr[8];
			$productName		= $dpr[9];
			$productMrp		= $dpr[3];
			$costToDistOrStkist 	= $dpr[4];
			$actualProfitMargin	= $dpr[5];
			$onMrpPercent		= $dpr[6];
			$onFactoryCostPercent	= $dpr[7];
	?>
	<tr  bgcolor="WHITE">
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$distProdPriceRecId;?>" ></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$distributorName;?></td>	
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$productName;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$productMrp;?></td>	
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$costToDistOrStkist;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$actualProfitMargin;?></td>	
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$onMrpPercent;?></td>		
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$onFactoryCostPercent;?></td>
	<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$distProdPriceRecId;?>,'editId');this.form.action='DistributorProductPrice.php';" ></td>
	<? }?>
	</tr>
	<?
		}
	?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="9" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"DistributorProductPrice.php?pageNo=$page&selRateList=$selRateList\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"DistributorProductPrice.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"DistributorProductPrice.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\">>></a> ";
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
											<?
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="9"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
										</table>
	</td>
	</tr>
	<tr>
		<td colspan="3" height="5">
	
	
	<!--input type="hidden" name="sizeOfMarginStructureRecs" value="<?=sizeof($marginStructureRecords)?>"-->
	
		</td>
	</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$distProPriceRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintDistributorProductPrice.php',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		
		<tr>
			<td height="10">
		<input type="hidden" name="factoryCost" id="factoryCost" value="<?=$factoryCost?>">
		<input type="hidden" name="octroiPercent" id="octroiPercent" value="<?=$octroiPercent?>">
		<input type="hidden" name="insuranceCost" id="insuranceCost" value="<?=$insuranceCost?>">
		<input type="hidden" name="taxType" id="taxType" value="<?=$taxType?>">
		<input type="hidden" name="vatPercent" id="vatPercent" value="<?=$vatPercent?>">
		<input type="hidden" name="billingFormF" id="billingFormF" value="<?=$billingFormF?>">
		<input type="hidden" name="hidCstRate" id="hidCstRate" value="<?=$cstPercent?>">
		<input type="hidden" name="productExciseRatePercent" id="productExciseRatePercent" value="<?=$productExciseRatePercent?>">
		<input type="hidden" name="educationCess" id="educationCess" value="<?=$educationCess?>">
		<input type="hidden" name="factoryProfitMargin" id="factoryProfitMargin" value="<?=$factoryProfitMargin?>">
		<input type="hidden" name="productPriceRateList" id="productPriceRateList" value="<?=$productPriceRateList?>">
			</td>
		</tr>
	</table>
<? if ($addMode || $editMode) {?>
	<script>
		// Find Each Structure Value
		calcDistStructProfitMgn();	
	</script>
<? }?>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
