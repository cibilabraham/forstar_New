<?php
	$insideIFrame = "Y";
	require("include/include.php");		
	require_once("lib/SemiFinishProductMaster_ajax.php");
	
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$userId		= $sessObj->getValue("userId");

	$selection 	= "?pageNo=".$p["pageNo"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	= false;
	$edit	= false;
	$del	= false;
	$print	= false;
	$confirm= false;
	
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
	
	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;
	# Cancel
	if ($p["cmdCancel"]!="") $addMode = false;

	# Add a Product
	if ($p["cmdAdd"]!="") {
	
		$productCode		= $p["productCode"];
		$productName		= $p["productName"];
		$ingCategory		= $p["ingCategory"];
		$subCategory		= $p["subCategory"];
		$productRatePerKgPerBatch = $p["productRatePerKgPerBatch"];	
		$kgPerBatch		= $p["kgPerBatch"];
		$productRatePerBatch	= $p["productRatePerBatch"];
		$productKgRawPerBatch	= $p["productKgRawPerBatch"];
		$productYieldPercent	= $p["productYieldPercent"];
		$processHrs		= $p["processHrs"];
		$processMints		= $p["processMints"];
		$gasHrs			= $p["gasHrs"];
		$gasMints		= $p["gasMints"];
		$steamHrs		= $p["steamHrs"];
		$steamMints		= $p["steamMints"];
		$fixedStaffHrs		= $p["fixedStaffHrs"];
		$fixedStaffMints	= $p["fixedStaffMints"];
		$noOfFixedStaff		= $p["noOfFixedStaff"];
		$ingCostPerKg		= $p["ingCostPerKg"];
		$productionCostPerKg	= $p["productionCostPerKg"];
		$totProdCostPerKg	= $p["totProdCostPerKg"];
		$openingQty		= $p["openingQty"];

		$selIngRateList		= $p["hidSelIngRateList"];
		$selManPowerRateList	= $p["hidManPowerRateList"];

		$hidTableRowCount	= $p["hidTableRowCount"]; /* Ing Row Count*/
		$varStaffRowCount	= $p["varStaffRowCount"]; /* Variable Staff Row Count*/
		
		# Check for existing rec
		//$recExist	= $semiFinishProductObj->chkRecExist($semiFinishProduct, $cId); && !$recExist

		if ($productCode!="" && $productName!="" && $ingCategory && $subCategory!="" && $kgPerBatch!="") {
			$semiProductRecIns = $semiFinishProductObj->addSemiFinishProduct($productCode, $productName, $ingCategory, $subCategory, $productRatePerKgPerBatch, $kgPerBatch, $productRatePerBatch, $productKgRawPerBatch, $productYieldPercent, $processHrs, $processMints, $gasHrs, $gasMints, $steamHrs, $steamMints, $fixedStaffHrs, $fixedStaffMints, $noOfFixedStaff, $ingCostPerKg, $productionCostPerKg, $totProdCostPerKg, $openingQty, $userId, $selIngRateList, $selManPowerRateList);	

			#Find the Last inserted Id From m_productmaster Table
			$lastId = $databaseConnect->getLastInsertedId();
			
			/* Ingredient Items */
			for ($i=0; $i<$hidTableRowCount; $i++) {
			    $status = $p["status_".$i];
			    if ($status!='N') {
				# ING - Ingredient  SFP = Semi Finished Product
				$selIngRec	= explode("_",$p["selIngredient_".$i]);				
				$selIngType	= $selIngRec[0];	
				$ingredientId   = $selIngRec[1];
				$quantity	= trim($p["quantity_".$i]);				
				$percentagePerBatch 	= $p["percentagePerBatch_".$i];
				$ratePerBatch		= $p["ratePerBatch_".$i];

				if ($lastId!="" && $ingredientId!="" && $quantity!="") {
					$sfProductIngItemsIns = $semiFinishProductObj->addSfProductIngRecs($lastId, $ingredientId, $quantity, $percentagePerBatch, $ratePerBatch, $selIngType);
				}
			  }
		     	}	
			/* Variable staff Rec Ins*/
			for ($j=1; $j<=$varStaffRowCount; $j++) {
				$manPowerId   = $p["manPowerId_".$j];					
				$manPowerUnit = $p["manPowerUnit_".$j];
				if ($lastId!="" && $manPowerUnit!="") {
					$sfProductVarStaffRecIns = $semiFinishProductObj->addSfProductVarStaffRecs($lastId, $manPowerId, $manPowerUnit);
				}
			}
		
		}

		if ($semiProductRecIns) {
			$addMode	= false;
			$sessObj->createSession("displayMsg",$msg_succAddSemiFinishProductMaster);
			$sessObj->createSession("nextPage",$url_afterAddSemiFinishProductMaster.$selection);
		} else {
			$addMode	= true;
			if ($recExist) $err = $msg_failAddSemiFinishProductMaster."<br>".$msgProductMRPExistRec ;
			else $err	= $msg_failAddSemiFinishProductMaster;
		}
		$semiProductRecIns		=	false;
	}
	

	# Edit 
	if ($p["editId"]!="" ) {
		$editId			= $p["editId"];
		$editMode		= true;
		$productMRPMasterRec	= $semiFinishProductObj->find($editId);
		
		$editSemiProductMasterId = $productMRPMasterRec[0];
		$productCode		= $productMRPMasterRec[1];
		$productName		= $productMRPMasterRec[2];
		$selCategoryId		= $productMRPMasterRec[3];
		$selSubCategory		= $productMRPMasterRec[4];
		$productRatePerKgPerBatch = $productMRPMasterRec[5];
		$kgPerBatch		= $productMRPMasterRec[6];
		$productRatePerBatch	= $productMRPMasterRec[7];
		$productKgRawPerBatch	= $productMRPMasterRec[8];
		$productYieldPercent	= $productMRPMasterRec[9];
		$processHrs		= $productMRPMasterRec[10];
		$processMints		= $productMRPMasterRec[11];
		$gasHrs			= $productMRPMasterRec[12];
		$gasMints		= $productMRPMasterRec[13];
		$steamHrs		= $productMRPMasterRec[14];
		$steamMints		= $productMRPMasterRec[15];
		$fixedStaffHrs		= $productMRPMasterRec[16];
		$fixedStaffMints	= $productMRPMasterRec[17];
		$noOfFixedStaff		= $productMRPMasterRec[18];
		$ingCostPerKg		= $productMRPMasterRec[19];
		$productionCostPerKg	= $productMRPMasterRec[20];
		$totProdCostPerKg	= $productMRPMasterRec[21];
	
		$openingQty		= $productMRPMasterRec[22];

		$selIngRateListId	= $productMRPMasterRec[24];
		$mpcRateListId		= $productMRPMasterRec[25];
		
	}

	#Update Record
	if ($p["cmdSaveChange"]!="" ) {
		
		$semiProductMasterId = $p["hidSemiProductMasterId"];

		//$semiFinishProduct	= $p["semiFinishProduct"];

		$productCode		= $p["productCode"];
		$productName		= $p["productName"];
		$ingCategory		= $p["ingCategory"];
		$subCategory		= $p["subCategory"];
		$productRatePerKgPerBatch = $p["productRatePerKgPerBatch"];	
		$kgPerBatch		= $p["kgPerBatch"];
		$productRatePerBatch	= $p["productRatePerBatch"];
		$productKgRawPerBatch	= $p["productKgRawPerBatch"];
		$productYieldPercent	= $p["productYieldPercent"];
		$processHrs		= $p["processHrs"];
		$processMints		= $p["processMints"];
		$gasHrs			= $p["gasHrs"];
		$gasMints		= $p["gasMints"];
		$steamHrs		= $p["steamHrs"];
		$steamMints		= $p["steamMints"];
		$fixedStaffHrs		= $p["fixedStaffHrs"];
		$fixedStaffMints	= $p["fixedStaffMints"];
		$noOfFixedStaff		= $p["noOfFixedStaff"];
		$ingCostPerKg		= $p["ingCostPerKg"];
		$productionCostPerKg	= $p["productionCostPerKg"];
		$totProdCostPerKg	= $p["totProdCostPerKg"];
		//$openingQty		= $p["openingQty"];
		//$selIngRateList		= $p["hidSelIngRateList"];
		//$selManPowerRateList	= $p["hidManPowerRateList"];

		$hidTableRowCount	= $p["hidTableRowCount"]; /* Ing Row Count*/
		$varStaffRowCount	= $p["varStaffRowCount"]; /* Variable Staff Row Count*/

		$openingQty		= $p["openingQty"];		
		$hidExistingQty 	= trim($p["hidExistingQty"]);

		# Check for existing rec
		//$recExist	= $semiFinishProductObj->chkRecExist($semiFinishProduct, $semiProductMasterId);	&& !$recExist

		if ($semiProductMasterId!="" && $productCode!="" && $productName!="" && $ingCategory && $subCategory!="" && $kgPerBatch!="" ) {
			$semiFinishProductMasterRecUptd = $semiFinishProductObj->updateSemiProductMaster($semiProductMasterId, $productCode, $productName, $ingCategory, $subCategory, $productRatePerKgPerBatch, $kgPerBatch, $productRatePerBatch, $productKgRawPerBatch, $productYieldPercent, $processHrs, $processMints, $gasHrs, $gasMints, $steamHrs, $steamMints, $fixedStaffHrs, $fixedStaffMints, $noOfFixedStaff, $ingCostPerKg, $productionCostPerKg, $totProdCostPerKg, $openingQty, $hidExistingQty);
			if ($semiFinishProductMasterRecUptd) {
				
				if ($semiProductMasterId) {
					# Delete Ing Items
					$delSFIngRecs  = $semiFinishProductObj->delSFIngRecs($semiProductMasterId);
					# Delete Var Staff Recs
					$delSFVarStaffRecs = $semiFinishProductObj->delSFVarStaffRecs($semiProductMasterId);
				}

				/* Ingredient Items */
				for ($i=0; $i<$hidTableRowCount; $i++) {
					$status = $p["status_".$i];
					if ($status!='N') {
						# ING - Ingredient  SFP = Semi Finished Product
						$selIngRec	= explode("_",$p["selIngredient_".$i]);
						$selIngType	= $selIngRec[0];	
						$ingredientId   = $selIngRec[1];
						$quantity	= trim($p["quantity_".$i]);
						$percentagePerBatch 	= $p["percentagePerBatch_".$i];
						$ratePerBatch		= $p["ratePerBatch_".$i];
		
						if ($semiProductMasterId!="" && $ingredientId!="" && $quantity!="") {
							$sfProductIngItemsIns = $semiFinishProductObj->addSfProductIngRecs($semiProductMasterId, $ingredientId, $quantity, $percentagePerBatch, $ratePerBatch, $selIngType);
						}
					}
				}	
				/* Variable staff Rec Ins*/
				for ($j=1; $j<=$varStaffRowCount; $j++) {
					$manPowerId   = $p["manPowerId_".$j];					
					$manPowerUnit = $p["manPowerUnit_".$j];
					if ($semiProductMasterId!="" && $manPowerUnit!="") {
						$sfProductVarStaffRecIns = $semiFinishProductObj->addSfProductVarStaffRecs($semiProductMasterId,$manPowerId, $manPowerUnit);
					}
				}

			}
		}
	
		if ($semiFinishProductMasterRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succSemiFinishProductMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateSemiFinishProductMaster.$selection);
		} else {
			$editMode	=	true;
			if ($recExist) $err = $msg_failSemiFinishProductMasterUpdate."<br>".$msgProductMRPExistRec ;
			else $err = $msg_failSemiFinishProductMasterUpdate;
		}
		$semiFinishProductMasterRecUptd	=	false;
	}


	# Delete 
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$semiProductMasterId	= $p["delId_".$i];
			if ($semiProductMasterId!="" ) {	
				// Need to check		
				$delSFIngRecs  = $semiFinishProductObj->delSFIngRecs($semiProductMasterId);
				# Delete Var Staff Recs
				$delSFVarStaffRecs = $semiFinishProductObj->delSFVarStaffRecs($semiProductMasterId);

				$semiProductMasterRecDel = $semiFinishProductObj->deleteSemiFinishProductMaster($semiProductMasterId);
			}
		}
		if ($semiProductMasterRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelSemiFinishProductMaster);
			$sessObj->createSession("nextPage",$url_afterDelSemiFinishProductMaster.$selection);
		} else {
			$errDel	=	$msg_failDelSemiFinishProductMaster;
		}
		$semiProductMasterRecDel	=	false;
	}

if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$semiProductMasterId	=	$p["confirmId"];
			if ($semiProductMasterId!="") {
				// Checking the selected fish is link with any other process
				$semiProductRecConfirm = $semiFinishProductObj->updatesemiProductMasterconfirm($semiProductMasterId);
			}

		}
		if ($semiProductRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmSemiFinishProductMaster);
			$sessObj->createSession("nextPage",$url_afterDelSemiFinishProductMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$semiProductMasterId = $p["confirmId"];
			if ($semiProductMasterId!="") {
				#Check any entries exist
				
					$semiProductRecConfirm = $semiFinishProductObj->updatesemiProductMasterReleaseconfirm($semiProductMasterId);
				
			}
		}
		if ($semiProductRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmSemiFinishProductMaster);
			$sessObj->createSession("nextPage",$url_afterDelSemiFinishProductMaster.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo-1)*$limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Records
	$semiFinishProductMasterRecords = $semiFinishProductObj->fetchAllPagingRecords($offset, $limit);
	$semiFinishProductMasterRecordSize    = sizeof($semiFinishProductMasterRecords);

	## -------------- Pagination Settings II -------------------
	$fetchSemiProductMasterRecords = $semiFinishProductObj->fetchAllRecords();	// fetch All Records
	$numrows	=  sizeof($fetchSemiProductMasterRecords);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($addMode || $editMode) {
		# Get All Semi-Finish Product Records
		//$semiFinishProductRecords = $semiFinishProductObj->getSemiFinishProductRecs();		
	}

	
	# Get Main Category Records
	//$ingCategoryRecords = $ingMainCategoryObj->fetchAllRecords();
	$ingCategoryRecords = $ingMainCategoryObj->fetchAllRecordsActiveCategory();
	if ($addMode || $editMode) {
		if ($p["ingCategory"]!="") $selCategoryId = $p["ingCategory"];
		//else $selCategoryId = $selMainCategoyId; // Edit Mode
		
		# List all Ingredient Sub-Category	
		//$ingSubCategoryRecords = $ingredientCategoryObj->fetchAllRecords($selCategoryId);
		$ingSubCategoryRecords = $ingredientCategoryObj->fetchAllRecordsActiveSubcategory($selCategoryId);
	}

	#List all Ingredient
	if ($addMode || $editMode) {
		if ($selIngRateListId!="") $selRateList = $selIngRateListId; 
		else $selRateList = $ingredientRateListObj->latestRateList();		
		$ingredientRecords = $semiFinishProductObj->fetchAllIngredientRecords($selRateList, $editSemiProductMasterId);
		/*
	 	echo "<pre>";
	 	print_r($ingredientRecords);
        	 echo "</pre>";
		*/
	}

	#List all Man Power
	$productionManPower = "MPC";	
	if ($mpcRateListId=="") $mpcRateList = $manageRateListObj->latestRateList($productionManPower);
	else $mpcRateList = $mpcRateListId;
	if ($addMode) $variableManPowerRecords = $productionManPowerObj->getVariableManPowerRecords($mpcRateList);
	else if ($editMode) $variableManPowerRecords = $semiFinishProductObj->getSelVarManPowerRecords($mpcRateList, $editSemiProductMasterId);

	
	if ($editMode) $heading	= $label_editSemiFinishProductMaster;
	else	       $heading	= $label_addSemiFinishProductMaster;

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with XAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/SemiFinishProductMaster.js"; // For Printing JS in Head section

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmSemiFinishedProductMaster" action="SemiFinishProductMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr><TD height="10"></TD></tr>
	<tr><td height="10" align="center"><a href="###" class="link1" title="Click to Manage Product" onclick="parent.openTab('ProductMaster.php');">Manage Product</a></td>	
	</tr>
	<tr>
		<? if($err!="" ){?>
		<tr>
			<td height="20" align="center" class="err1" > <?=$err;?></td>
			
		</tr>
		<?}?>
		<?
			if( ($editMode || $addMode) && $disabled) {
		?>
		<tr style="display:none;">
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
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
			<td colspan="4" height="10" ></td>
		</tr>
		<tr>
		<? if($editMode){?>
		<td colspan="4" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SemiFinishProductMaster.php');">&nbsp;&nbsp;			<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSemiFinishProduct(document.frmSemiFinishedProductMaster);">	
		</td>
		<?} else{?>
		<td  colspan="4" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SemiFinishProductMaster.php');">&nbsp;&nbsp;			<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSemiFinishProduct(document.frmSemiFinishedProductMaster);">&nbsp;&nbsp;		
		</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidSemiProductMasterId" value="<?=$editSemiProductMasterId;?>">
		<tr>
			<td nowrap height="5"></td>			
		  </tr>
		<tr>
						  <td colspan="2" nowrap style="padding-left:5px; padding-right:5px;" valign="top">
					<table width="200">
                                                <tr>
                                                  <td class="fieldName">*Code : </td>
                                                  <td class="listing-item">
							<? if ($p["productCode"]!="") $productCode=$p["productCode"];?>	
							<input type="text" name="productCode" value="<?=$productCode?>" size="10">
						</td>
                                                </tr>
                                                <tr>
                                                  <td class="fieldName">*Name:</td>
                                                  <td class="listing-item">
							<? if ($p["productName"]!="") $productName=$p["productName"];?>
							<input type="text" name="productName" value="<?=$productName?>" size="30">
							</td>
                                                </tr>
						<tr>
						<td class="fieldName" nowrap >*Category</td>
						<td>
							<select name="ingCategory" id="ingCategory" onchange="<?if ($addMode) {?> this.form.submit();<? } else {?> this.form.editId.value=<?=$editId?>;this.form.submit(); <? }?>">
								<option value="">-- Select --</option>
								<?
								foreach ($ingCategoryRecords as $cr) {
									$ingCategoryId	= $cr[0];
									$ingCategoryName	= stripSlash($cr[1]);
									$selected = "";
									if ($selCategoryId==$ingCategoryId) $selected = "selected";
								?>	
								<option value="<?=$ingCategoryId?>" <?=$selected?>><?=$ingCategoryName?></option>
								<?
								}
								?>
							</select>
						</td>
					</tr>
						<tr>
					<td nowrap class="fieldName" >*Sub-Category</td>
					<td nowrap>
                                        <select name="subCategory" id="subCategory">
                                        <option value="">-- Select --</option>
					<?
					foreach ($ingSubCategoryRecords as $scr) {
						$subCategoryId	= $scr[0];
						$subCategoryName = stripSlash($scr[1]);
						$selected = "";
						if ($selSubCategory==$subCategoryId) $selected = "Selected";
					?>
                                        <option value="<?=$subCategoryId?>" <?=$selected?>><?=$subCategoryName?></option>
					<? }?>
                                        </select></td></tr>
					<tr>
				<td class="fieldName" nowrap>*Opening Qty:</td>
				<td>
					<input type="text" size="6" name="openingQty" value="<?=$openingQty?>" style="text-align:right;" autoComplete="off">
					<input type="hidden" name="hidExistingQty" size="5" value="<?=$openingQty;?>" style="text-align:right;">
				</td>
			</tr>
                                              </table>
					</td>
					<td></td>
					<td style="padding-left:5px; padding-right:5px;" valign="top">
					<table bgcolor="#999999" cellspacing="1" border="0">
					<TR bgcolor="#f2f2f2" align="center">
						<TD class="listing-head"></TD>
						<TD class="listing-head" style="padding-left:5px; padding-right:5px;">Product</TD>				
					</TR>					
					
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" nowrap bgcolor="lightYellow">Rs. Per Kg per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productRatePerKgPerBatch" id="productRatePerKgPerBatch" style="text-align:right;border:none" readonly value="<?=$productRatePerKgPerBatch?>" size="5"></TD>	
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Kg Per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
						<? if ($p["kgPerBatch"]!="") $kgPerBatch=$p["kgPerBatch"];?>
						<input type="text" size="5" style="text-align:right;" name="kgPerBatch" id="kgPerBatch" value="<?=$kgPerBatch?>" onkeyup="calcProductRatePerBatch();" autoComplete="off">
						</TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Rs. Per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productRatePerBatch" id="productRatePerBatch" style="text-align:right;border:none" readonly value="<?=$productRatePerBatch?>" size="5"></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Kg (Raw) per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productKgRawPerBatch" id="productKgRawPerBatch" style="text-align:right;border:none" readonly value="<?=$productKgRawPerBatch?>" size="5" onchange="calcProductRatePerBatch();"></TD>			
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">% Yield</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
							<input type="text" name="productYieldPercent" id="productYieldPercent" style="text-align:right;border:none" readonly value="<?=$productYieldPercent?>" size="5">%
						</TD>
					</TR>
				</table>
					</td>
					</tr>
		<tr>
			  <td colspan="2" height="5"></td>
		</tr>
	<!-- Dynamic row starts here -->
	<tr><TD colspan="4" nowrap style="padding-left:5px; padding-right:5px;">
	<table cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddIng">
		<TR bgcolor="#f2f2f2" align="center">
			<td class="listing-head" style="padding-left:5px; padding-right:5px;">Ingredient</td>
			<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Raw<br> Kg</td>
			<td class="listing-head" style="padding-left:5px; padding-right:5px;">%/<br>Batch</td>
			<td class="listing-head" style="padding-left:5px; padding-right:5px;">Rs/<br>Batch</td>
			<td></td>
		</TR>	
		<?
		$rowSize = 0;	
		if ($editSemiProductMasterId)  {
			$productIngRecs = $semiFinishProductObj->fetchAllSelIngRecs($editSemiProductMasterId);
			$rowSize = sizeof($productIngRecs);		
		$lastPrice = 0;
		$m = 0;
		$displayIngredient = "";	
		foreach ($productIngRecs as $rec) {
				$editIngredientId = $rec[2];
				$selIngType	  = $rec[6]; 
				$displayIngredient    = trim($selIngType.'_'.$editIngredientId);	
				if ($selIngType=='ING') {
					list($lastPrice,$declYield) = $semiFinishProductObj->getIngredientRate($editIngredientId, $selRateList);
				} else if ($selIngType=='SFP') {
					list($lastPrice,$declYield) = $semiFinishProductObj->getSemiFinishRate($editIngredientId);
				} else {
					$lastPrice	= 0;
					$declYield	= 0;
				}
				$editQuantity		= $rec[3];
				
				// Refer values
				$percentagePerBatch	= $rec[4];
				$ratePerBatch		= $rec[5];
	
				
		?>
				<tr bgcolor="#FFFFFF" align="center" id="row_<?=$m?>">
				<td style="padding-left:5px; padding-right:5px;">
						<select name="selIngredient_<?=$m?>" id="selIngredient_<?=$m?>" onchange="xajax_getIngRate(document.getElementById('selIngredient_<?=$m?>').value,<?=$m?>, <?=$selRateList?>);calcProductRatePerBatch();">			
					<option value="">-- Select --</option>
					<?
					$ingredientId = "";					
					foreach ($ingredientRecords as $kVal=>$ir) {
						$ingredientId	= $ir[0]; 
						$ingredientName = $ir[1];
						$selected	=	"";
						if ($displayIngredient==$ingredientId) $selected = "selected";
					?>
					<option value="<?=$ingredientId?>" <?=$selected?>><?=$ingredientName?></option>
					<? }?>
					</select></td>
					<td style="padding-left:5px; padding-right:5px;">
						<input name="quantity_<?=$m?>" type="text" id="quantity_<?=$m?>" value="<?=$editQuantity;?>" size='6' style="text-align:right" onkeyup="calcProductRatePerBatch();" autoComplete="off">
					</td>
					<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" nowrap>
						<input type="text" name="percentagePerBatch_<?=$m?>" id="percentagePerBatch_<?=$m?>" style="text-align:right;border:none" readonly value="<?=$percentagePerBatch?>" size="5">%
					</td>
					<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right">
						<input type="hidden" name="lastPrice_<?=$m?>" id="lastPrice_<?=$m?>" value="<?=$lastPrice?>">
						<input type="text" name="ratePerBatch_<?=$m?>" id="ratePerBatch_<?=$m?>" style="text-align:right;border:none" readonly value="<?=$ratePerBatch?>" size="5">
					</td>					
					<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="center" nowrap>
						<a href='###' onClick="setIngItemStatus(<?=$m?>);"><img title="Click here to remove this item" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>
						<input name="status_<?=$m?>" type="hidden" id="status_<?=$m?>" value="">
						<input name="IsFromDB_<?=$m?>" type="hidden" id="IsFromDB_<?=$m?>" value="N">
						<input name='ingType_<?=$m?>' type='hidden' id='ingType_<?=$m?>' value="<?=$selIngType?>">
					</td>
					</tr>
					<?
						$m++;
						}
			}
					?>	
	</table>
	</TD>
	</tr>
	<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=$rowSize?>">
	<tr><TD height="10"></TD></tr>
	<tr>
		<TD nowrap style="padding-left:5px; padding-right:5px;">
			<a href="###" id='addRow' onclick="javascript:addNewIngItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
		</TD>
	</tr>
	<!-- Dynamic row ends here -->
		<SCRIPT LANGUAGE="JavaScript">
		<!--
			setfieldId(<?=$rowSize;?>)
		//-->
		</SCRIPT>

		<tr>
		  <td colspan="2" nowrap style="padding-left:5px; padding-right:5px;" valign="top">
					<!--<table width="200">
						<tr>
                                                 <td class="fieldName" nowrap="true">*Semi-Finished Product</td>
                                                 <td class="listing-item">
						 <select name="semiFinishProduct">
						 <option value="">-- Select --</option>
						 <?php
						/*
						 foreach ($semiFinishProductRecords as $pr) {
							$semiFinishProductId	= $pr[0];
							$productName	= $pr[2];
							$selected = "";
							if ($semiFinishProduct==$semiFinishProductId) $selected = "Selected";
						*/
						 ?>
						 <option value="<?=$semiFinishProductId?>" <?=$selected?>><?=$productName?></option>
						 <? 
							//}
						?>
						 </select>
						 </td>
                                                </tr>
			<tr>
				<td class="fieldName" nowrap>*Opening Qty:</td>
				<td>
					<input type="text" size="6" name="openingQty" value="<?=$openingQty?>" style="text-align:right;" autoComplete="off">
					<input type="hidden" name="hidExistingQty" size="5" value="<?=$openingQty;?>" style="text-align:right;">
				</td>
			</tr>
				
                                              </table>-->
					</td>					
					</tr>
				<tr><TD height="10"></TD></tr>
				<tr>
					<TD style="padding-left:5px; padding-right:5px;">
						<fieldset>
						<legend class="listing-item">Production Cost</legend>
						<table>
							<tr>
								<TD class="fieldName" nowrap="true" style="line-height:normal;">*Total Duration of the process
								</TD>
								<td class="listing-item" nowrap="true">
									<input type="text" name="processHrs" id="processHrs" value="<?=$processHrs?>" size="2" style="text-align:right;" onchange="xajax_productionCost(document.getElementById('processHrs').value,document.getElementById('processMints').value,document.getElementById('gasHrs').value,document.getElementById('gasMints').value,document.getElementById('steamHrs').value,document.getElementById('steamMints').value);" autoComplete="off">&nbsp;Hrs
									&nbsp;&nbsp;<input type="text" name="processMints" id="processMints" value="<?=$processMints?>" size="2" style="text-align:right;" onchange="xajax_productionCost(document.getElementById('processHrs').value,document.getElementById('processMints').value,document.getElementById('gasHrs').value,document.getElementById('gasMints').value,document.getElementById('steamHrs').value,document.getElementById('steamMints').value);" autoComplete="off">&nbsp;Mints
									<input type="hidden" name="electricityConsumptionCost" id="electricityConsumptionCost">
								</td>
							</tr>
							<tr>
								<TD class="fieldName" nowrap="true">Duration of using Gas</TD>
								<td class="listing-item" nowrap="true">
									<input type="text" name="gasHrs" id="gasHrs" value="<?=$gasHrs?>" size="2" style="text-align:right;" onchange="xajax_productionCost(document.getElementById('processHrs').value,document.getElementById('processMints').value,document.getElementById('gasHrs').value,document.getElementById('gasMints').value,document.getElementById('steamHrs').value,document.getElementById('steamMints').value);" autoComplete="off">&nbsp;Hrs
									&nbsp;&nbsp;<input type="text" name="gasMints" id="gasMints" value="<?=$gasMints?>" size="2" style="text-align:right;" onchange="xajax_productionCost(document.getElementById('processHrs').value,document.getElementById('processMints').value,document.getElementById('gasHrs').value,document.getElementById('gasMints').value,document.getElementById('steamHrs').value,document.getElementById('steamMints').value);" autoComplete="off">&nbsp;Mints
									<input type="hidden" name="gasConsumptionCost" id="gasConsumptionCost">
								</td>
							</tr>
							<tr>
								<TD class="fieldName" nowrap="true" style="line-height:normal;">Duration of using Steam<br>
								<span class="fieldName" style="line-height:normal;font-size:10px;">(Diesel generated)</span>
								</TD>
								<td class="listing-item" nowrap="true">
									<input type="text" name="steamHrs" id="steamHrs" value="<?=$steamHrs?>" size="2" style="text-align:right;" onchange="xajax_productionCost(document.getElementById('processHrs').value,document.getElementById('processMints').value,document.getElementById('gasHrs').value,document.getElementById('gasMints').value,document.getElementById('steamHrs').value,document.getElementById('steamMints').value);" autoComplete="off">&nbsp;Hrs
									&nbsp;&nbsp;<input type="text" name="steamMints" id="steamMints" value="<?=$steamMints?>" size="2" style="text-align:right;" onchange="xajax_productionCost(document.getElementById('processHrs').value,document.getElementById('processMints').value,document.getElementById('gasHrs').value,document.getElementById('gasMints').value,document.getElementById('steamHrs').value,document.getElementById('steamMints').value);" autoComplete="off">&nbsp;Mints
									<input type="hidden" name="steamConsumptionCost" id="steamConsumptionCost">
								</td>
							</tr>
							<tr>
								<TD class="fieldName" nowrap="true" style="line-height:normal;" valign="top">Labour Cost
								</TD>
								<td class="listing-item" nowrap="true">
									<table>
										<TR>
											<TD class="fieldName" nowrap="true">No.of Fixed Staff</TD>
											<td>
												<input type="text" name="noOfFixedStaff" id="noOfFixedStaff" value="<?=$noOfFixedStaff?>" size="2" style="text-align:right;" onchange="xajax_getFixedStaffCost(document.getElementById('fixedStaffHrs').value, document.getElementById('fixedStaffMints').value, document.getElementById('noOfFixedStaff').value);" autoComplete="off">
											</td>
											<TD class="fieldName" nowrap="true" align="right">Hrs</TD>
											<td>
												<input type="text" name="fixedStaffHrs" id="fixedStaffHrs" value="<?=$fixedStaffHrs?>" size="2" style="text-align:right;" onchange="xajax_getFixedStaffCost(document.getElementById('fixedStaffHrs').value, document.getElementById('fixedStaffMints').value, document.getElementById('noOfFixedStaff').value);" autoComplete="off">
											</td>
											<TD class="fieldName" nowrap="true" align="right">Mints</TD>
											<td>
												<input type="text" name="fixedStaffMints" id="fixedStaffMints" value="<?=$fixedStaffMints?>" size="2" style="text-align:right;" onchange="xajax_getFixedStaffCost(document.getElementById('fixedStaffHrs').value, document.getElementById('fixedStaffMints').value, document.getElementById('noOfFixedStaff').value);" autoComplete="off">

<input type="hidden" name="fixedStaffCostPerHr" id="fixedStaffCostPerHr">
											</td>
										</TR>
	<tr>
		<!--<TD class="fieldName">Variable Staff</TD>-->
		<!--<TD class="fieldName" nowrap="true" align="right">Hrs</TD>	
		<td>
			<input type="text" name="varStaffHrs" id="varStaffHrs" value="<?=$varStaffHrs?>" size="2" style="text-align:right;">
		</td>
		<TD class="fieldName" nowrap="true" align="right">Mints</TD>
		<td>
			<input type="text" name="varStaffMints" id="varStaffMints" value="<?=$varStaffMints?>" size="2" style="text-align:right;">
		</td>		-->
	</tr>
	<tr>
		<TD colspan="6">
			<table>
				<TR bgcolor="#f2f2f2">
				<TD class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Variable Staff</TD>
				<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">No.of Staff</td>
			</TR>			
			<?
			$vm=0;			
			foreach ($variableManPowerRecords as $mpr) {
				$vm++;
				$manPowerId 	= $mpr[0];
				$mPName		= stripSlash($mpr[1]);
				$mPPuCost	= $mpr[4];
				$mPUnit		= $mpr[6];
				/*
					$mPType		= $mpr[2];	
					$mPUnit		= $mpr[3];
					$mPPuCost	= $mpr[4];
					$mpTotCost	= $mpr[5];
				*/
			?>
			<TR bgcolor="WHITE">
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"><?=$mPName;?>
					<input type="hidden" name="manPowerId_<?=$vm?>" id="manPowerId_<?=$vm?>" value="<?=$manPowerId?>">
					<input type="hidden" name="manPowerName_<?=$vm?>" id="manPowerName_<?=$vm?>" size="15"></TD>				
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
					<input type="text" name="manPowerUnit_<?=$vm?>" id="manPowerUnit_<?=$vm?>" size="2" style="text-align:right" value="<?=$mPUnit?>" onkeyup="calcTotalVariableStaffAmt(document.getElementById('fixedStaffHrs').value, document.getElementById('fixedStaffMints').value);" autoComplete="off">
					<input type="hidden" name="unitCost_<?=$vm?>" id="unitCost_<?=$vm?>" size="5" style="text-align:right" value="<?=$mPPuCost?>">
				</td>
			</TR>
		<?php 
			}
		?>	
	<input type="hidden" name="varStaffRowCount" id="varStaffRowCount" value="<?=$vm?>">	
	<input type="hidden" name="varStaffTotalCost" id="varStaffTotalCost">	
	<input type="hidden" name="varStaffPerHrCost" id="varStaffPerHrCost">	
			</table>
		</TD>
	</tr>
		</table>
			</td>
				</tr>
							
						</table>
					</fieldset>
					</TD>
				</tr>
	<tr><TD height="10"></TD></tr>
		<tr>
			<TD style="padding-left:5px; padding-right:5px;">
			<fieldset>
				<legend class="listing-item">Total Cost</legend>
				<table>
					<tr>
						<td class="fieldName" nowrap="true">Ingredient Cost:</td>
						<td class="listing-item">
							<input type="text" name="ingCostPerKg" id="ingCostPerKg" size="6" value="<?=$ingCostPerKg?>" style="text-align:right;" readonly>
						</td>
						<td class="fieldName" nowrap="true">Production Cost:</td>
						<td class="listing-item">
							<input type="text" name="productionCostPerKg" id="productionCostPerKg" size="6" value="<?=$productionCostPerKg?>" style="text-align:right;" readonly>
						</td>
						<td class="fieldName" nowrap="true">Total Cost/Kg:</td>
						<td class="listing-item">
							<input type="text" name="totProdCostPerKg" id="totProdCostPerKg" size="6" value="<?=$totProdCostPerKg?>" style="text-align:right;" readonly>
						</td>
					</tr>					
				</table>
			</fieldset>
			</TD>
		</tr>
				<tr>
					<td colspan="2"  height="10" ></td>
				</tr>
				<tr>
				<? if($editMode){?>
				<td colspan="4" align="center">
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SemiFinishProductMaster.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSemiFinishProduct(document.frmSemiFinishedProductMaster);">	
				</td>
				<?} else{?>
				<td  colspan="4" align="center">
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SemiFinishProductMaster.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSemiFinishProduct(document.frmSemiFinishedProductMaster);">&nbsp;&nbsp;			</td>
				<input type="hidden" name="cmdAddNew" value="1">
				<?}?>
				<!--input type="hidden" name="stockType" value="<?=$stockType?>"-->
				</tr>
		<tr>
			<td colspan="2"  height="10" >
				<input type="hidden" name="fixedQtyCheked" value="<?=$fixedQtyCheked?>">
			</td>
		</tr>
		</table></td>
		</tr>
		</table>
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
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>			
			<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
							<!-- Form fields start -->
							<?php	
								$bxHeader="Semi-Finished Product Master";
								include "template/boxTL.php";
							?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<!--<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName">&nbsp;Semi-Finished Product Master</td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
</td>
								</tr>-->
								<tr>
									<td colspan="3" align="center">
	<table width="100%">
	<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
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
		<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
		<tr>
			<td colspan="4" height="10" ></td>
		</tr>
		<tr>
		<? if($editMode){?>
		<td colspan="4" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SemiFinishProductMaster.php');">&nbsp;&nbsp;			<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSemiFinishProduct(document.frmSemiFinishedProductMaster);">	
		</td>
		<?} else{?>
		<td  colspan="4" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SemiFinishProductMaster.php');">&nbsp;&nbsp;			<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSemiFinishProduct(document.frmSemiFinishedProductMaster);">&nbsp;&nbsp;		
		</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidSemiProductMasterId" value="<?=$editSemiProductMasterId;?>">
		<tr>
			<td nowrap height="5"></td>			
		  </tr>
	<tr>
	<td colspan="4" align="center">
	<table width="70%"><TR><TD>
		<table border="0" width="100%">
		<tr>
		  <td nowrap valign="top" align="center">
			<table border="0" width="80%">
			<tr>
				<TD valign="top">
				<table cellpadding="0" cellspacing="0" width="50%">
					<TR><TD valign="top">
					<?php			
					$entryHead = "Product";
					require("template/rbTop.php");
					?>
				<table cellpadding="0" cellspacing="0" border="0" width="80%" align="center">
					<TR>
						<TD valign="top">
						<table width="200" border="0">
                                                <tr>
                                                  <td class="fieldName">*Code : </td>
                                                  <td class="listing-item">
							<? if ($p["productCode"]!="") $productCode=$p["productCode"];?>	
							<input type="text" name="productCode" value="<?=$productCode?>" size="10">
						</td>
                                                </tr>
                                                <tr>
                                                  <td class="fieldName">*Name:</td>
                                                  <td class="listing-item">
							<? if ($p["productName"]!="") $productName=$p["productName"];?>
							<input type="text" name="productName" value="<?=$productName?>" size="30">
							</td>
                                                </tr>
						<tr>
						<td class="fieldName" nowrap >*Category</td>
						<td>
							<select name="ingCategory" id="ingCategory" onchange="<?if ($addMode) {?> this.form.submit();<? } else {?> this.form.editId.value=<?=$editId?>;this.form.submit(); <? }?>">
								<option value="">-- Select --</option>
								<?
								foreach ($ingCategoryRecords as $cr) {
									$ingCategoryId	= $cr[0];
									$ingCategoryName	= stripSlash($cr[1]);
									$selected = "";
									if ($selCategoryId==$ingCategoryId) $selected = "selected";
								?>	
								<option value="<?=$ingCategoryId?>" <?=$selected?>><?=$ingCategoryName?></option>
								<?
								}
								?>
							</select>
						</td>
					</tr>
						<tr>
					<td nowrap class="fieldName" >*Sub-Category</td>
					<td nowrap>
                                        <select name="subCategory" id="subCategory">
                                        <option value="">-- Select --</option>
					<?
					foreach ($ingSubCategoryRecords as $scr) {
						$subCategoryId	= $scr[0];
						$subCategoryName = stripSlash($scr[1]);
						$selected = "";
						if ($selSubCategory==$subCategoryId) $selected = "Selected";
					?>
                                        <option value="<?=$subCategoryId?>" <?=$selected?>><?=$subCategoryName?></option>
					<? }?>
                                        </select></td></tr>
					<tr>
				<td class="fieldName" nowrap>*Opening Qty:</td>
				<td>
					<input type="text" size="6" name="openingQty" value="<?=$openingQty?>" style="text-align:right;" autoComplete="off">
					<input type="hidden" name="hidExistingQty" size="5" value="<?=$openingQty;?>" style="text-align:right;">
				</td>
			</tr>
                         </table>
			</TD>
			<td>&nbsp;</td>
						<TD valign="top">
						<table cellspacing="1" border="0" width="200">					
					<TR>
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" nowrap bgcolor="lightYellow">Rs. Per Kg per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productRatePerKgPerBatch" id="productRatePerKgPerBatch" style="text-align:right;border:none" readonly value="<?=$productRatePerKgPerBatch?>" size="5"></TD>	
					</TR>
					<TR>
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Kg Per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" bgcolor="lightblue">
						<? if ($p["kgPerBatch"]!="") $kgPerBatch=$p["kgPerBatch"];?>
						<input type="text" size="5" style="text-align:right;" name="kgPerBatch" id="kgPerBatch" value="<?=$kgPerBatch?>" onkeyup="calcProductRatePerBatch();" autoComplete="off">
						</TD>
					</TR>
					<TR>
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Rs. Per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productRatePerBatch" id="productRatePerBatch" style="text-align:right;border:none" readonly value="<?=$productRatePerBatch?>" size="5"></TD>
					</TR>
					<TR>
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">Kg (Raw) per Batch</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right">
						<input type="text" name="productKgRawPerBatch" id="productKgRawPerBatch" style="text-align:right;border:none" readonly value="<?=$productKgRawPerBatch?>" size="5" onchange="calcProductRatePerBatch();"></TD>			
					</TR>
					<TR>
						<TD class="fieldName" style="line-height:normal; padding-left:2px; padding-right:2px;" bgcolor="lightYellow">% Yield</TD>
						<TD class="listing-item" style="padding-left:2px; padding-right:2px;" align="right" nowrap>
							<input type="text" name="productYieldPercent" id="productYieldPercent" style="text-align:right;border:none" readonly value="<?=$productYieldPercent?>" size="5">
						</TD>
						<td class="listing-item">%</td>
					</TR>					
				</table>
						</TD>
					</TR>
				</table>
				
			<?php
				require("template/rbBottom.php");
			?>
					</TD></TR>
					<TR><TD>
					<table>
						<TR>
							<TD valign="top">
						<?php			
							$entryHead = "Production Cost";
							require("template/rbTop.php");
						?>
	<table>
		<TR>
			<TD valign="top">
			<table>
			<tr>
				<TD>&nbsp;</TD>
				<td class="listing-item">Hrs&nbsp;&nbsp;&nbsp;Mints</td>
			</tr>
							<tr>
								<TD class="fieldName" nowrap="true" style="line-height:normal;">*Total Dur<!--ation--> of the process
								</TD>
								<td class="listing-item" nowrap="true">
									<input type="text" name="processHrs" id="processHrs" value="<?=$processHrs?>" size="2" style="text-align:right;" onchange="xajax_productionCost(document.getElementById('processHrs').value,document.getElementById('processMints').value,document.getElementById('gasHrs').value,document.getElementById('gasMints').value,document.getElementById('steamHrs').value,document.getElementById('steamMints').value);" autoComplete="off"><!--&nbsp;Hrs-->
									&nbsp;&nbsp;<input type="text" name="processMints" id="processMints" value="<?=$processMints?>" size="2" style="text-align:right;" onchange="xajax_productionCost(document.getElementById('processHrs').value,document.getElementById('processMints').value,document.getElementById('gasHrs').value,document.getElementById('gasMints').value,document.getElementById('steamHrs').value,document.getElementById('steamMints').value);" autoComplete="off"><!--&nbsp;Mints-->
									<input type="hidden" name="electricityConsumptionCost" id="electricityConsumptionCost">
								</td>
							</tr>
							<tr>
								<TD class="fieldName" nowrap="true">Duration of using Gas</TD>
								<td class="listing-item" nowrap="true">
									<input type="text" name="gasHrs" id="gasHrs" value="<?=$gasHrs?>" size="2" style="text-align:right;" onchange="xajax_productionCost(document.getElementById('processHrs').value,document.getElementById('processMints').value,document.getElementById('gasHrs').value,document.getElementById('gasMints').value,document.getElementById('steamHrs').value,document.getElementById('steamMints').value);" autoComplete="off"><!--&nbsp;Hrs-->
									&nbsp;&nbsp;<input type="text" name="gasMints" id="gasMints" value="<?=$gasMints?>" size="2" style="text-align:right;" onchange="xajax_productionCost(document.getElementById('processHrs').value,document.getElementById('processMints').value,document.getElementById('gasHrs').value,document.getElementById('gasMints').value,document.getElementById('steamHrs').value,document.getElementById('steamMints').value);" autoComplete="off"><!--&nbsp;Mints-->
									<input type="hidden" name="gasConsumptionCost" id="gasConsumptionCost">
								</td>
							</tr>
							<tr>
								<TD class="fieldName" nowrap="true" style="line-height:normal;">Duration of using Steam<br>
								<span class="fieldName" style="line-height:normal;font-size:10px;">(Diesel generated)</span>
								</TD>
								<td class="listing-item" nowrap="true">
									<input type="text" name="steamHrs" id="steamHrs" value="<?=$steamHrs?>" size="2" style="text-align:right;" onchange="xajax_productionCost(document.getElementById('processHrs').value,document.getElementById('processMints').value,document.getElementById('gasHrs').value,document.getElementById('gasMints').value,document.getElementById('steamHrs').value,document.getElementById('steamMints').value);" autoComplete="off"><!--&nbsp;Hrs-->
									&nbsp;&nbsp;<input type="text" name="steamMints" id="steamMints" value="<?=$steamMints?>" size="2" style="text-align:right;" onchange="xajax_productionCost(document.getElementById('processHrs').value,document.getElementById('processMints').value,document.getElementById('gasHrs').value,document.getElementById('gasMints').value,document.getElementById('steamHrs').value,document.getElementById('steamMints').value);" autoComplete="off"><!--&nbsp;Mints-->
									<input type="hidden" name="steamConsumptionCost" id="steamConsumptionCost">
								</td>
							</tr>
							
		</table>
			</td>
		<td valign="top">
			<table>
					<tr>
								<TD class="fieldName" nowrap="true" style="line-height:normal; text-align:left;" valign="top" colspan="2" ><strong>Labour Cost</strong>
								</TD>
							</tr>
							<tr>
								<!--<TD class="fieldName" nowrap="true" style="line-height:normal;" valign="top">Labour Cost
								</TD>-->
								<td class="listing-item" nowrap="true" colspan="2">
									<table>
										<TR>
											<TD class="fieldName" nowrap="true">No.of Fixed Staff</TD>
											<td>
												<input type="text" name="noOfFixedStaff" id="noOfFixedStaff" value="<?=$noOfFixedStaff?>" size="2" style="text-align:right;" onchange="xajax_getFixedStaffCost(document.getElementById('fixedStaffHrs').value, document.getElementById('fixedStaffMints').value, document.getElementById('noOfFixedStaff').value);" autoComplete="off">
											</td>
											<TD class="fieldName" nowrap="true" align="right">Hrs</TD>
											<td>
												<input type="text" name="fixedStaffHrs" id="fixedStaffHrs" value="<?=$fixedStaffHrs?>" size="2" style="text-align:right;" onchange="xajax_getFixedStaffCost(document.getElementById('fixedStaffHrs').value, document.getElementById('fixedStaffMints').value, document.getElementById('noOfFixedStaff').value);" autoComplete="off">
											</td>
											<TD class="fieldName" nowrap="true" align="right">Mints</TD>
											<td>
												<input type="text" name="fixedStaffMints" id="fixedStaffMints" value="<?=$fixedStaffMints?>" size="2" style="text-align:right;" onchange="xajax_getFixedStaffCost(document.getElementById('fixedStaffHrs').value, document.getElementById('fixedStaffMints').value, document.getElementById('noOfFixedStaff').value);" autoComplete="off">
												<input type="hidden" name="fixedStaffCostPerHr" id="fixedStaffCostPerHr">
											</td>
										</TR>	
	<tr>
		<TD colspan="6">
			<table class="newspaperType">
				<thead>
				<TR>
					<Th nowrap style="padding-left:5px; padding-right:5px;">Variable Staff</Th>
					<th nowrap style="padding-left:5px; padding-right:5px;">No.of Staff</th>
				</TR>	
				</thead>		
				<tbody>
			<?php
			$vm=0;			
			foreach ($variableManPowerRecords as $mpr) {
				$vm++;
				$manPowerId 	= $mpr[0];
				$mPName		= stripSlash($mpr[1]);
				$mPPuCost	= $mpr[4];
				$mPUnit		= $mpr[6];				
			?>
			<TR>
				<TD class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"><?=$mPName;?>
					<input type="hidden" name="manPowerId_<?=$vm?>" id="manPowerId_<?=$vm?>" value="<?=$manPowerId?>">
					<input type="hidden" name="manPowerName_<?=$vm?>" id="manPowerName_<?=$vm?>" size="15"></TD>				
				<td nowrap style="padding-left:5px; padding-right:5px;" align="center">
					<input type="text" name="manPowerUnit_<?=$vm?>" id="manPowerUnit_<?=$vm?>" size="2" style="text-align:right" value="<?=$mPUnit?>" onkeyup="calcTotalVariableStaffAmt(document.getElementById('fixedStaffHrs').value, document.getElementById('fixedStaffMints').value);" autoComplete="off">
					<input type="hidden" name="unitCost_<?=$vm?>" id="unitCost_<?=$vm?>" size="5" style="text-align:right" value="<?=$mPPuCost?>">
				</td>
			</TR>
		<?php 
			}
		?>	
		</tbody>
	<input type="hidden" name="varStaffRowCount" id="varStaffRowCount" value="<?=$vm?>">	
	<input type="hidden" name="varStaffTotalCost" id="varStaffTotalCost">	
	<input type="hidden" name="varStaffPerHrCost" id="varStaffPerHrCost">	
			</table>
		</TD>
	</tr>
				</table>
			</TD>
		</TR>
	</table>
		</td>
				</tr>
	</table>			
						
		<?php
			require("template/rbBottom.php");
		?>
	</TD>
	<TD valign="top">
	<?php			
					$entryHead = "Total Cost";
					require("template/rbTop.php");
				?>
				<table>
					<tr>
						<td class="fieldName" nowrap="true">Ingredient Cost:</td>
						<td class="listing-item">
							<input type="text" name="ingCostPerKg" id="ingCostPerKg" size="6" value="<?=$ingCostPerKg?>" style="text-align:right;" readonly>
						</td>
					</tr>
					<tr>
						<td class="fieldName" nowrap="true">Production Cost:</td>
						<td class="listing-item">
							<input type="text" name="productionCostPerKg" id="productionCostPerKg" size="6" value="<?=$productionCostPerKg?>" style="text-align:right;" readonly>
						</td>
					</tr>
					<tr>
						<td class="fieldName" nowrap="true">Total Cost/Kg:</td>
						<td class="listing-item">
							<input type="text" name="totProdCostPerKg" id="totProdCostPerKg" size="6" value="<?=$totProdCostPerKg?>" style="text-align:right;" readonly>
						</td>
					</tr>					
				</table>
			<?php
				require("template/rbBottom.php");
			?>
	</TD>
						</TR>
					</table>
					</TD></TR>
				</table>
				</TD>
				<td valign="top">
				<table cellpadding="0" cellspacing="0">
					<!-- Dynamic row starts here -->
	<tr><TD nowrap>
	<table cellspacing="1" cellpadding="3" id="tblAddIng" class="newspaperType">
		<TR align="center">
			<th>Ingredient</th>
			<th nowrap>Raw Kg</th>
			<th>%/Batch</th>
			<th>Rs/Batch</th>
			<th>&nbsp;</th>
		</TR>	
		<?
		$rowSize = 0;	
		if ($editSemiProductMasterId)  {
			$productIngRecs = $semiFinishProductObj->fetchAllSelIngRecs($editSemiProductMasterId);
			$rowSize = sizeof($productIngRecs);		
		$lastPrice = 0;
		$m = 0;
		$displayIngredient = "";	
		foreach ($productIngRecs as $rec) {
				$editIngredientId = $rec[2];
				$selIngType	  = $rec[6]; 
				$displayIngredient    = trim($selIngType.'_'.$editIngredientId);	
				if ($selIngType=='ING') {
					list($lastPrice,$declYield) = $semiFinishProductObj->getIngredientRate($editIngredientId, $selRateList);
				} else if ($selIngType=='SFP') {
					list($lastPrice,$declYield) = $semiFinishProductObj->getSemiFinishRate($editIngredientId);
				} else {
					$lastPrice	= 0;
					$declYield	= 0;
				}
				$editQuantity		= $rec[3];
				
				// Refer values
				$percentagePerBatch	= $rec[4];
				$ratePerBatch		= $rec[5];
	
				
		?>
				<tr bgcolor="#FFFFFF" align="center" id="row_<?=$m?>">
				<td style="padding-left:5px; padding-right:5px;">
						<select name="selIngredient_<?=$m?>" id="selIngredient_<?=$m?>" onchange="xajax_getIngRate(document.getElementById('selIngredient_<?=$m?>').value,<?=$m?>, <?=$selRateList?>);calcProductRatePerBatch();">			
					<option value="">-- Select --</option>
					<?
					$ingredientId = "";					
					foreach ($ingredientRecords as $kVal=>$ir) {
						$ingredientId	= $ir[0]; 
						$ingredientName = $ir[1];
						$selected	=	"";
						if ($displayIngredient==$ingredientId) $selected = "selected";
					?>
					<option value="<?=$ingredientId?>" <?=$selected?>><?=$ingredientName?></option>
					<? }?>
					</select></td>
					<td style="padding-left:5px; padding-right:5px;">
						<input name="quantity_<?=$m?>" type="text" id="quantity_<?=$m?>" value="<?=$editQuantity;?>" size='6' style="text-align:right" onkeyup="calcProductRatePerBatch();" autoComplete="off">
					</td>
					<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" nowrap>
						<input type="text" name="percentagePerBatch_<?=$m?>" id="percentagePerBatch_<?=$m?>" style="text-align:right;border:none" readonly value="<?=$percentagePerBatch?>" size="5">%
					</td>
					<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right">
						<input type="hidden" name="lastPrice_<?=$m?>" id="lastPrice_<?=$m?>" value="<?=$lastPrice?>">
						<input type="text" name="ratePerBatch_<?=$m?>" id="ratePerBatch_<?=$m?>" style="text-align:right;border:none" readonly value="<?=$ratePerBatch?>" size="5">
					</td>					
					<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="center" nowrap>
						<a href='###' onClick="setIngItemStatus(<?=$m?>);"><img title="Click here to remove this item" SRC='images/delIcon.gif' BORDER='0' style='border:none;'></a>
						<input name="status_<?=$m?>" type="hidden" id="status_<?=$m?>" value="">
						<input name="IsFromDB_<?=$m?>" type="hidden" id="IsFromDB_<?=$m?>" value="N">
						<input name='ingType_<?=$m?>' type='hidden' id='ingType_<?=$m?>' value="<?=$selIngType?>">
					</td>
					</tr>
					<?php
						$m++;
						}
					}
					?>	
	</table>
	</TD>
	</tr>
	<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=$rowSize?>">
	<tr><TD height="10"></TD></tr>
	<tr>
		<TD nowrap style="padding-left:5px; padding-right:5px;">
			<a href="###" id='addRow' onclick="javascript:addNewIngItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
		</TD>
	</tr>
	<!-- Dynamic row ends here -->
		<SCRIPT LANGUAGE="JavaScript">
		<!--
			setfieldId(<?=$rowSize;?>)
		//-->
		</SCRIPT>
				</table>
				</td>
			</tr>
			</table>
		</td>
		</tr>		
	</table>
	</TD></TR></table>
	</td>
	</tr>
		
				<tr>
					<td colspan="2"  height="10" ></td>
				</tr>
				<tr>
				<? if($editMode){?>
				<td colspan="4" align="center">
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SemiFinishProductMaster.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSemiFinishProduct(document.frmSemiFinishedProductMaster);">	
				</td>
				<?} else{?>
				<td  colspan="4" align="center">
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SemiFinishProductMaster.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSemiFinishProduct(document.frmSemiFinishedProductMaster);">&nbsp;&nbsp;			</td>
				<input type="hidden" name="cmdAddNew" value="1">
				<?}?>
				<!--input type="hidden" name="stockType" value="<?=$stockType?>"-->
				</tr>
		<tr>
			<td colspan="2"  height="10" >
				<input type="hidden" name="fixedQtyCheked" value="<?=$fixedQtyCheked?>">
			</td>
		</tr>
		</table></td>
		</tr>
		</table>
		<?php
			require("template/rbBottom.php");
		?>		
		</td>
		</tr>
		</table>
	<!-- Form fields end   --></td>
		</tr>
		<?
			}			
			# Listing Category Starts
		?>
	</table>
									</td>
								</tr>						
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$semiFinishProductMasterRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSemiFinishProductMaster.php?selRateList=<?=$selRateList?>',700,600);"><?}?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<?php
									if($errDel!="") {
								?>
								<tr>
									<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
								</tr>
								<?php
									}
								?>
								<tr>
									<td width="1" ></td>
	<td colspan="2" style="padding-left:10px; padding-right:10px;">
	<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?
	if ( sizeof($semiFinishProductMasterRecords) > 0) {
		$i	=	0;
	?>
	<thead>
	<? if($maxpage>1){ ?>
		<tr>
		<td colspan="8" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"SemiFinishProductMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SemiFinishProductMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SemiFinishProductMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th width="20">
		<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></th>		
		<th style="padding-left:10px; padding-right:10px;">Product</th>
		<th style="padding-left:10px; padding-right:10px;">Category</th>
		<th style="padding-left:10px; padding-right:10px;">Sub-Category</th>
		<th style="padding-left:10px; padding-right:10px;">Opening Qty</th>
		<th style="padding-left:10px; padding-right:10px;">Actual Qty</th>
		<? if($edit==true){?>
		<th class="listing-head">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
		<th class="listing-head">&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
	<?
	foreach ($semiFinishProductMasterRecords as $sfpm) {
		$i++;
		$semiProductMasterId	= $sfpm[0];		
		$productName	= $sfpm[2];
		$openingQty	= $sfpm[3];	
		$actualQty	= $sfpm[4];
		$categoryName   = $sfpm[6];		
		$sCategoryName  = $sfpm[5];	
		$active=$sfpm[7];
	?>
	<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$semiProductMasterId;?>" class="chkBox"></td>		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$productName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$categoryName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$sCategoryName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$openingQty;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$actualQty;?></td>
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
		<?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$semiProductMasterId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='SemiFinishProductMaster.php';"><? } ?>
		</td>



		 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$semiProductMasterId;?>,'confirmId');"  >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$semiProductMasterId;?>,'confirmId');"  >
			<?php }?>
			<? }?>
			
			
			
			</td>
		<? }?>
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr>
		<td colspan="8" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"SemiFinishProductMaster.php?pageNo=$page\" class=\"link1\">$page</a> ";				
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SemiFinishProductMaster.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SemiFinishProductMaster.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		} else {
	?>
	<tr bgcolor="white">
		<td colspan="7"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>
	<?
		}
	?>
	</table>
</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$semiFinishProductMasterRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSemiFinishProductMaster.php?selRateList=<?=$selRateList?>',700,600);"><?}?></td>
											</tr>
										</table>			</td>
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
<input type="hidden" name="hidSelIngRateList" value="<?=$selRateList?>">	
<input type="hidden" name="hidManPowerRateList" value="<?=$mpcRateList?>">	
	
		<tr>
			<td height="10"></td>
		</tr>
		<tr><td height="10" align="center"><a href="###" class="link1" title="Click to Manage Product" onclick="parent.openTab('ProductMaster.php');">Manage Product</a></td>	
	</tr>
<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
	</table> 
	<? if ($addMode || $editMode ) {?>
	<script language="JavaScript">
		function addNewIngItem()
		{
			addNewIngItemRow('tblAddIng');
		}		
	</script>
	<? }?>
	<? if ($addMode) {?>
	<script language="JavaScript">
		window.onLoad = addNewIngItem();
	</script>
	<? }?>
	<? 
		if ($editMode) {
	?>
	<script language="JavaScript">
		xajax_productionCost(document.getElementById('processHrs').value,document.getElementById('processMints').value,document.getElementById('gasHrs').value,document.getElementById('gasMints').value,document.getElementById('steamHrs').value,document.getElementById('steamMints').value);
		calcTotalVariableStaffAmt(document.getElementById('fixedStaffHrs').value,document.getElementById('fixedStaffMints').value);
		xajax_getFixedStaffCost(document.getElementById('fixedStaffHrs').value, document.getElementById('fixedStaffMints').value, document.getElementById('noOfFixedStaff').value);
	</script>

	<?
		}
	?>
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
	ensureInFrameset(document.frmSemiFinishedProductMaster);
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
