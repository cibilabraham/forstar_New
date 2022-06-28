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
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
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
	
	if ($p["cmdCancel"]!="") {		
		$addMode = false;	
		$editMode = false;
		$editId   = "";
	}
	
	#Add a Rec
	if ($p["cmdAdd"]!="") {
		$schemeName 	= addSlash(trim($p["schemeName"]));	
		$buyNum		= $p["buyNum"];		
		$buyBasedOn	= $p["buyBasedOn"]; //P-> Product, M->MRP
		$selProduct	= $p["selProduct"]; // Multiple selection
		$selMrp		= $p["selMrp"];	
		
		$getNum		= $p["getNum"];
		$getProductType = $p["getProductType"]; // MP->MRP PRODUCT, SP-> Sample Product
		$getMrpProductType = $p["getMrpProductType"]; // G-Group, I- Individual
		// if G
		$getMrpGroupType = $p["getMrpGroupType"]; // SM -> same MRP, LM- Less MRP
		$selGroupMrp	 = $p["selGroupMrp"];	
		// if I
		$selIndProduct	= $p["selIndProduct"]; // Multiple selection
		
		// If SP
		$selSampleProduct = $p["selSampleProduct"];



		#Checking same entry exist in the table
		//$sameEntryExist = $schemeMasterObj->checkEntryExist($selRetailCounter, $retCtMarginRateListId, $selProduct, '');  && !$sameEntryExist

		if ($schemeName!="" && $buyNum!="" && $getNum!="") {
			$schmeMasterRecIns = $schemeMasterObj->addSchemeMaster($schemeName, $buyNum, $buyBasedOn, $selProduct, $selMrp, $getNum, $getProductType, $getMrpProductType, $getMrpGroupType, $selGroupMrp, $selIndProduct, $selSampleProduct, $userId);

			if ($schmeMasterRecIns) {
				$addMode = false;
				$sessObj->createSession("displayMsg",$msg_succAddSchemeMaster);
				$sessObj->createSession("nextPage",$url_afterAddSchemeMaster.$selection);
			} else {
				$addMode = true;
				$err	 = $msg_failAddSchemeMaster;
			}
			$schmeMasterRecIns = false;
		} else {
			if ($sameEntryExist)	$err = $msg_failRtCounterMarginDuplication; // Duplication err
			else 			$err = $msg_failAddSchemeMaster;
		}
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$schemeMasterId =	$p["hidSchemeMasterId"];
		$schemeName 	= addSlash(trim($p["schemeName"]));	
		$buyNum		= $p["buyNum"];		
		$buyBasedOn	= $p["buyBasedOn"]; //P-> Product, M->MRP
		$selProduct	= $p["selProduct"]; // Multiple selection
		$selMrp		= $p["selMrp"];	
		
		$getNum		= $p["getNum"];
		$getProductType = $p["getProductType"]; // MP->MRP PRODUCT, SP-> Sample Product
		$getMrpProductType = $p["getMrpProductType"]; // G-Group, I- Individual
		// if G
		$getMrpGroupType = $p["getMrpGroupType"]; // SM -> same MRP, LM- Less MRP
		$selGroupMrp	 = $p["selGroupMrp"];	
		// if I
		$selIndProduct	= $p["selIndProduct"]; // Multiple selection
		
		// If SP
		$selSampleProduct = $p["selSampleProduct"];
		
		#Checking same entry exist in the table
		//$sameEntryExist = $schemeMasterObj->checkEntryExist($selRetailCounter, $retCtMarginRateListId, $selProduct, $schemeMasterId);&& !$sameEntryExist
	
		if ($schemeMasterId!="" && $schemeName!="" && $buyNum!="" && $getNum!="" ) {
			# Delete Product Entry Rec
			$deleteSchemeProductEntryRec = $schemeMasterObj->delSchemeMasterProductRec($schemeMasterId);
			# Update Rec
			$schemeMasterRecUptd = $schemeMasterObj->updateSchemeMasterRec($schemeMasterId,$schemeName, $buyNum, $buyBasedOn, $selProduct, $selMrp, $getNum, $getProductType, $getMrpProductType, $getMrpGroupType, $selGroupMrp, $selIndProduct, $selSampleProduct);
		}
	
		if ($schemeMasterRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succSchemeMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateSchemeMaster.$selection);
		} else {
			$editMode	=	true;
			if ($sameEntryExist)	$err = $msg_failSchemeMasterDuplication; // Duplication err
			else $err		=	$msg_failSchemeMasterUpdate;
		}
		$schemeMasterRecUptd	=	false;
	}


	# Edit  a Record
	if ($p["editId"]!="" && $p["cmdCancel"]=="") {
		$editId		=	$p["editId"];
		$editMode	=	true;
		$schemeMasterRec	= $schemeMasterObj->find($editId);
		$editSchemeMasterId  = $schemeMasterRec[0];
		$schemeName 	= stripSlash($schemeMasterRec[1]);	
		$buyNum		= $schemeMasterRec[2];
		$buyBasedOn	= $schemeMasterRec[3]; //P-> Product, M->MRP
		if ($buyBasedOn=='P') {
			$chkAllProduct = $schemeMasterObj->chkAllProductSelectedRec($editSchemeMasterId, $buyBasedOn);
			$selAllProduct = "";
			if ($chkAllProduct) $selAllProduct = "Selected";
		}
		//$selProduct	= $p["selProduct"]; // Multiple selection
		$selMrp		= $schemeMasterRec[4];
		
		$getNum		= $schemeMasterRec[5];
		$getProductType = $schemeMasterRec[6]; // MP->MRP PRODUCT, SP-> Sample Product
		$getMrpProductType = $schemeMasterRec[7]; // G-Group, I- Individual
		// if G
		$getMrpGroupType = $schemeMasterRec[8]; // SM -> same MRP, LM- Less MRP
		$selGroupMrp	 = $schemeMasterRec[9];	
		if ($getMrpProductType=='I') {
			$chkAllIndProduct = $schemeMasterObj->chkAllProductSelectedRec($editSchemeMasterId, $getMrpProductType);
			$selAllIndProduct = "";
			if ($chkAllIndProduct) $selAllIndProduct = "Selected";
		}
		// if I
		//$selIndProduct	= $p["selIndProduct"]; // Multiple selection
		
		// If SP
		$selSampleProduct = $schemeMasterRec[10];			
	}


	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$schemeMasterId	=	$p["delId_".$i];
			if ($schemeMasterId!="") {		
				// Need to check the selected id is link with any other process
				# Delete Product Entry Rec
				$deleteSchemeProductEntryRec = $schemeMasterObj->delSchemeMasterProductRec($schemeMasterId);		
				#del main table
				$schemeMasterRecDel = $schemeMasterObj->deleteSchemeMasterRec($schemeMasterId);
			}
		}
		if ($schemeMasterRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelSchemeMaster);
			$sessObj->createSession("nextPage",$url_afterDelSchemeMaster.$selection);
		} else {
			$errDel	=	$msg_failDelSchemeMaster;
		}
		$schemeMasterRecDel	=	false;
	}



if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$schemeMasterId	=	$p["delId_".$i];
			if ($schemeMasterId!="") {
				// Checking the selected fish is link with any other process
				$schemeMasterRecConfirm = $schemeMasterObj->updateSchemeMasterconfirm($schemeMasterId);
			}

		}
		if ($schemeMasterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmschemeMaster);
			$sessObj->createSession("nextPage",$url_afterDelSchemeMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirmFishCategory;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$schemeMasterId = $p["delId_".$i];
			if ($schemeMasterId!="") {
				#Check any entries exist
				
					$schemeMasterRecConfirm = $schemeMasterObj->updateSchemeMasterReleaseconfirm($schemeMasterId);
				
			}
		}
		if ($schemeMasterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmschemeMaster);
			$sessObj->createSession("nextPage",$url_afterDelSchemeMaster.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirmFishCategory;
		}
		}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo-1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Scheme Master
	$schemeMasterResultSetObj = $schemeMasterObj->fetchAllPagingRecords($offset, $limit);
	$schemeMasterRecordSize   = $schemeMasterResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$fetchAllSchemeMasterResultSetObj = $schemeMasterObj->fetchAllRecords();
	$numrows	=  $fetchAllSchemeMasterResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	

	# List all Combo matrix
	if ($addMode) {
		$productMatrixResultSetObj = $comboMatrixObj->fetchAllRecords();
		$indProductMatrixResultSetObj = $comboMatrixObj->fetchAllRecords();
	} else { // Edit Mode
		$productMatrixResultSetObj= $schemeMasterObj->fetchBuyProductRecs($editSchemeMasterId);
		$indProductMatrixResultSetObj = $schemeMasterObj->fetchGetIndProductRecs($editSchemeMasterId);
		
	}

	# Product Pricing Rate List
	$selRateList = $productPriceRateListObj->latestRateList();
	$productPriceResultSetObj = $productPricingObj->fetchAllRecords($selRateList);
	$productMrpGroupPriceResultSetObj = $productPricingObj->fetchAllRecords($selRateList);

	# List all Sample Product
	$sampleProductResultSetObj = $sampleProductMasterObj->fetchAllRecordsActiveProducts();

	#heading Section
	if ($editMode) $heading	=	$label_editSchemeMaster;
	else	       $heading	=	$label_addSchemeMaster;

	$ON_LOAD_PRINT_JS	= "libjs/SchemeMaster.js";

	if ($addMode||$editMode) $ON_LOAD_FN = "hideSchemeMasterHead();";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmSchemeMaster" action="SchemeMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="80%" >
	<tr><td height="10"></td></tr>
	<!--<tr>
		<td height="10" align="center"><a href="MarginStructure.php" class="link1" title="Click to Manage Margin Structure">Margin Structure</a>
		</td>
	</tr>-->
	<tr>
		<td height="10" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
		<td></tr>
		<?
			if ( $editMode || $addMode) {
		?>
		<tr>
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
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

											  <td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('SchemeMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateSchemeMaster(document.frmSchemeMaster);"></td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SchemeMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSchemeMaster(document.frmSchemeMaster);">												</td>
					<?}?>
					</tr>
	<input type="hidden" name="hidSchemeMasterId" value="<?=$editSchemeMasterId;?>">
	<tr>
		<td colspan="2" nowrap style="padding-left:5px;padding-right:5px;">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<TD>
				<table cellpadding="1" cellspacing="3">
					<TR>
						<TD class="fieldName">*Name</TD>
						<TD>
							<input type="text" name="schemeName" id="schemeName" value="<?=$schemeName?>" size="25">
						</TD>
					</TR>
				</table>
			</TD>
		</tr>
		<tr>
			<TD>
				<table cellpadding="0" cellspacing="0">
				<tr>
					<td nowrap class="fieldName">*Buy&nbsp;</td>
					<td nowrap>
						<input type="text" name="buyNum" id="buyNum" value="<?=$buyNum?>" size="3" autoComplete="off">&nbsp;
					</td>
					<td nowrap class="fieldName">*Based on&nbsp;</td>
					<td nowrap>
						<select name="buyBasedOn" id="buyBasedOn" onchange="displayBuyRow();">
						<option value="">-- Select --</option>
						<option value="P" <? if ($buyBasedOn=='P') echo "selected";?>>PRODUCT</option>
						<option value="M" <? if ($buyBasedOn=='M') echo "selected";?>>MRP</option>
						</select>
					</td>
					<td nowrap id="buyBasedOnProduct">
						<table cellpadding="1" cellspacing="3">
							<tr>
								<td nowrap class="fieldName">*Product</td>
								<td nowrap>
                        			<select name="selProduct[]" id="selProduct" multiple="true" size="10">
                                		<option value="0" <?=$selAllProduct?>>-- Select All--</option>
						<?
						while ($pmr=$productMatrixResultSetObj->getRow()) {
							$productMatrixRecId 	= $pmr[0];
							$productCode		= $pmr[1];
							$productName		= $pmr[2];
							$selectedProductId	= $pmr[3]; // When Edit Mode
							$selected = "";
							if ($selectedProductId==$productMatrixRecId) $selected = "Selected";
						?>
                            			<option value="<?=$productMatrixRecId?>" <?=$selected?>><?=$productName?></option>
						<? 
						}
						?>
						</select>
						</td></tr>
						</table>
					</td>
					<TD nowrap="true" id="buyBasedOnMrp">
						<table cellpadding="1" cellspacing="3">
							<tr>
								<td nowrap class="fieldName">*MRP</td>
								<td nowrap>
                        			<select name="selMrp" id="selMrp">
                                		<option value="">-- Select --</option>
						<?
						while ($ppr=$productPriceResultSetObj->getRow()) {
							$productPriceMasterId = $ppr[0];			
							$productName	= $ppr[14];	
							$productMRP	= $ppr[10];
							$displayMrp 	= "MRP&nbsp;".$productMRP;
							$selected = "";
							if ($selMrp==$productMRP) $selected = "Selected";
						?>
                            			<option value="<?=$productMRP?>" <?=$selected?>><?=$displayMrp?></option>
						<? 
						}
						?>
						</select>
						</td></tr>
						</table>
					</TD>
				</tr>
				<!--<tr id="buyBasedOnProduct">
					<TD colspan="4">
						<table cellpadding="1" cellspacing="3">
							<tr>
								<td nowrap class="fieldName">*Product</td>
								<td nowrap>
                        			<select name="selProduct[]" id="selProduct" multiple="true" size="10">
                                		<option value="0" <?=$selAllProduct?>>-- Select All--</option>
						<?
						while ($pmr=$productMatrixResultSetObj->getRow()) {
							$productMatrixRecId 	= $pmr[0];
							$productCode		= $pmr[1];
							$productName		= $pmr[2];
							$selectedProductId	= $pmr[3]; // When Edit Mode
							$selected = "";
							if ($selectedProductId==$productMatrixRecId) $selected = "Selected";
						?>
                            			<option value="<?=$productMatrixRecId?>" <?=$selected?>><?=$productName?></option>
						<? 
						}
						?>
						</select>
				</td></tr>
						</table>
					</TD>
				</tr>-->
				<!--<tr id="buyBasedOnMrp">
					<TD colspan="4">
						<table cellpadding="1" cellspacing="3">
							<tr>
								<td nowrap class="fieldName">*MRP</td>
								<td nowrap>
                        			<select name="selMrp" id="selMrp">
                                		<option value="">-- Select --</option>
						<?
						while ($ppr=$productPriceResultSetObj->getRow()) {
							$productPriceMasterId = $ppr[0];			
							$productName	= $ppr[14];	
							$productMRP	= $ppr[10];
							$displayMrp 	= "MRP&nbsp;".$productMRP;
							$selected = "";
							if ($selMrp==$productMRP) $selected = "Selected";
						?>
                            			<option value="<?=$productMRP?>" <?=$selected?>><?=$displayMrp?></option>
						<? 
						}
						?>
						</select>
				</td></tr>
						</table>
					</TD>
				</tr>-->
				</table>
			</TD>
		</tr>								
	<tr>
			<TD>
				<table cellpadding="0" cellspacing="0">
				<tr>
					<td nowrap class="fieldName">*Get&nbsp;</td>
					<td nowrap>
						<input type="text" name="getNum" id="getNum" value="<?=$getNum?>" size="3" autoComplete="off">&nbsp;
					</td>
					<td nowrap class="fieldName">*Product Based on&nbsp;</td>
					<td nowrap>
						<select name="getProductType" id="getProductType" onchange="displayGetFnRow();">
						<option value="">-- Select --</option>
						<option value="MP" <? if ($getProductType=='MP') echo "Selected"; ?>>MRP</option>
						<option value="SP" <? if ($getProductType=='SP') echo "Selected"; ?>>SAMPLE</option>
						</select>
					</td>
					<TD nowrap id="getMrpProductBaseOnR">
					<table cellpadding="1" cellspacing="3">
						<TR>
						<td nowrap class="fieldName">*MRP Product Based On</td>
						<td nowrap>
							<select name="getMrpProductType" id="getMrpProductType" onchange="disGetMrpProdBased();">
							<option value="">-- Select --</option>
							<option value="G" <? if ($getMrpProductType=='G') echo "Selected"; ?>>GROUP</option>
							<option value="I" <? if ($getMrpProductType=='I') echo "Selected"; ?>>INDIVIDUAL</option>
							</select>
						</td>
						</TR>
					</table>
					</TD>
					<TD id="getMrpGroupBasedOnR" nowrap="true">
						<table cellpadding="1" cellspacing="3">
							<TR>
								<td nowrap class="fieldName">*MRP Group Based On</td>
					<td nowrap>
						<select name="getMrpGroupType" id="getMrpGroupType">
						<option value="">-- Select --</option>
						<option value="SM" <? if ($getMrpGroupType=='SM') echo "Selected"; ?>>SAME MRP</option>
						<option value="LM" <? if ($getMrpGroupType=='LM') echo "Selected"; ?>>LESS MRP</option>
						</select>
					</td>
					<td class="fieldName" nowrap="true">AND&nbsp;
						<select name="selGroupMrp" id="selGroupMrp">
                                		<option value="">-- Select --</option>
						<?
						while ($ppr=$productMrpGroupPriceResultSetObj->getRow()) {
							$productPriceMasterId = $ppr[0];			
							$productName	= $ppr[14];	
							$productMRP	= $ppr[10];
							$displayMrp 	= "MRP&nbsp;".$productMRP;
							$selected = "";
							if ($selGroupMrp==$productMRP) $selected = "Selected";
						?>
                            			<option value="<?=$productMRP?>" <?=$selected?>><?=$displayMrp?></option>
						<? 
						}
						?>
						</select>
					</td>
							</TR>
						</table>
					</TD>
					<TD nowrap="true" id="getMrpIndProductBasedOnR">
						<table cellpadding="1" cellspacing="3">
							<tr>
								<td nowrap class="fieldName">*MRP Individual Product Based On</td>
								<td nowrap>
                        			<select name="selIndProduct[]" id="selIndProduct" multiple="true" size="10">
                                		<option value="0" <?=$selAllIndProduct?>>-- Select All--</option>
						<?
						while ($pmr=$indProductMatrixResultSetObj->getRow()) {
							$productMatrixRecId 	= $pmr[0];
							$productCode		= $pmr[1];
							$productName		= $pmr[2];
							$selectedProductId	= $pmr[3]; // When Edit Mode
							$selected = "";
							if ($selectedProductId==$productMatrixRecId) $selected = "Selected";
						?>
                            			<option value="<?=$productMatrixRecId?>" <?=$selected?>><?=$productName?></option>
						<? 
						}
						?>
						</select>
				</td></tr>
						</table>
					</TD>
					<TD nowrap id="sampleProductR">
						<table cellpadding="1" cellspacing="3">
							<tr>
								<td nowrap class="fieldName">*Sample Product</td>
								<td nowrap>
                        			<select name="selSampleProduct" id="selSampleProduct">
                                		<option value="">-- Select --</option>
						<?
						while ($spr=$sampleProductResultSetObj->getRow()) {
							$sampleProductId   = $spr[0];
							$sampleProductCode = stripSlash($spr[1]);
							$sampleProductName = stripSlash($spr[2]);	
							$selected = "";
							if ($selSampleProduct==$sampleProductId) $selected = "Selected";
						?>
                            			<option value="<?=$sampleProductId?>" <?=$selected?>><?=$sampleProductName?></option>
						<? 
						}
						?>
						</select>
						</td></tr>
						</table>
					</TD>
				</tr>
				
				<!--<tr id="getMrpProductBaseOnR">
				<TD colspan="4">
					<table cellpadding="1" cellspacing="3">
						<TR>
						<td nowrap class="fieldName">*MRP Product Based On</td>
						<td nowrap>
							<select name="getMrpProductType" id="getMrpProductType" onchange="disGetMrpProdBased();">
							<option value="">-- Select --</option>
							<option value="G" <? if ($getMrpProductType=='G') echo "Selected"; ?>>GROUP</option>
							<option value="I" <? if ($getMrpProductType=='I') echo "Selected"; ?>>INDIVIDUAL</option>
							</select>
						</td>
						</TR>
					</table>
				</TD>
				</tr>-->
				<!--<tr id="getMrpGroupBasedOnR">
					<TD colspan="4">
						<table cellpadding="1" cellspacing="3">
							<TR>
								<td nowrap class="fieldName">*MRP Group Based On</td>
					<td nowrap>
						<select name="getMrpGroupType" id="getMrpGroupType">
						<option value="">-- Select --</option>
						<option value="SM" <? if ($getMrpGroupType=='SM') echo "Selected"; ?>>SAME MRP</option>
						<option value="LM" <? if ($getMrpGroupType=='LM') echo "Selected"; ?>>LESS MRP</option>
						</select>
					</td>
					<td class="fieldName" nowrap="true">AND&nbsp;
						<select name="selGroupMrp" id="selGroupMrp">
                                		<option value="">-- Select --</option>
						<?
						while ($ppr=$productMrpGroupPriceResultSetObj->getRow()) {
							$productPriceMasterId = $ppr[0];			
							$productName	= $ppr[14];	
							$productMRP	= $ppr[10];
							$displayMrp 	= "MRP&nbsp;".$productMRP;
							$selected = "";
							if ($selGroupMrp==$productMRP) $selected = "Selected";
						?>
                            			<option value="<?=$productMRP?>" <?=$selected?>><?=$displayMrp?></option>
						<? 
						}
						?>
						</select>
					</td>
							</TR>
						</table>
					</TD>
				</tr>-->
				<!--<tr id="getMrpIndProductBasedOnR">
					<TD colspan="4">
						<table cellpadding="1" cellspacing="3">
							<tr>
								<td nowrap class="fieldName">*MRP Individual Product Based On</td>
								<td nowrap>
                        			<select name="selIndProduct[]" id="selIndProduct" multiple="true" size="10">
                                		<option value="0" <?=$selAllIndProduct?>>-- Select All--</option>
						<?
						while ($pmr=$indProductMatrixResultSetObj->getRow()) {
							$productMatrixRecId 	= $pmr[0];
							$productCode		= $pmr[1];
							$productName		= $pmr[2];
							$selectedProductId	= $pmr[3]; // When Edit Mode
							$selected = "";
							if ($selectedProductId==$productMatrixRecId) $selected = "Selected";
						?>
                            			<option value="<?=$productMatrixRecId?>" <?=$selected?>><?=$productName?></option>
						<? 
						}
						?>
						</select>
				</td></tr>
						</table>
					</TD>
				</tr>-->
				<!--<tr id="sampleProductR">
					<TD colspan="4">
						<table cellpadding="1" cellspacing="3">
							<tr>
								<td nowrap class="fieldName">*Sample Product</td>
								<td nowrap>
                        			<select name="selSampleProduct" id="selSampleProduct">
                                		<option value="">-- Select --</option>
						<?
						while ($spr=$sampleProductResultSetObj->getRow()) {
							$sampleProductId   = $spr[0];
							$sampleProductCode = stripSlash($spr[1]);
							$sampleProductName = stripSlash($spr[2]);	
							$selected = "";
							if ($selSampleProduct==$sampleProductId) $selected = "Selected";
						?>
                            			<option value="<?=$sampleProductId?>" <?=$selected?>><?=$sampleProductName?></option>
						<? 
						}
						?>
						</select>
				</td></tr>
						</table>
					</TD>
				</tr>-->
				</table>
			</TD>
		</tr>	
	
						</table></td>
										  </td>
						<tr>
							<td colspan="2"  height="10" ></td>
											</tr>
			<tr>
				<? if($editMode){?>
				<td colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SchemeMaster.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSchemeMaster(document.frmSchemeMaster);">							
				</td>
				<?} else{?>
				<td  colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SchemeMaster.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSchemeMaster(document.frmSchemeMaster);">							
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
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%"  bgcolor="#D3D3D3">
				
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Scheme Master  </td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>

								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$schemeMasterRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSchemeMaster.php?selRateList=<?=$selRateList?>',700,600);"><? }?></td>
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
									<td colspan="2" style="padding-left:10px;padding-right:10px;">
		<table cellpadding="2"  width="75%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if ($schemeMasterRecordSize) {
			$i	=	0;
		?>
<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="7" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"SchemeMaster.php?pageNo=$page&selRateList=$selRateList\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SchemeMaster.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SchemeMaster.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\">>></a> ";
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
	<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Name</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Buy</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Based On</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Get</td>	
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Based On</td>
	<? if($edit==true){?>
		<td class="listing-head"></td>
	<? }?>
	<? if($confirm==true){?>
		<td class="listing-head"></td>
	<? }?>
	</tr>
	<?	
/*
BUY <SELECT/ENTER HOW MANY> BASED ON <PRODUCT / MRP> 
IF PRODUCT BASED <ALL PRODUCTS / SELECT ONE OR MORE PRODUCTS>
IF MRP BASED <SELECT MRP 89 / MRP 75 / ETC. > based on the product mrp list
GET <SELECT/ENTER HOW MANY> BASED ON <PRODUCT / MRP> 
IF PRODUCT GET WHAT <SELECT MRP PRODUCT / SAMPLE PRODUCT>
IF MRP PRODUCT BASED THEN <GROUP / IND PRODUCTS>
IF MRP GROUP BASED THEN <SAME MRP / LESS MRP>
AND <SELECT MRP 89 / MRP 75 / ETC. > based on the product mrp list
IF MRP IND PRODUCT BASED <ALL PRODUCTS / SELECT ONE OR MORE PRODUCTS>
IF SAMPLE PRODUCT <SELECT SAMPLE PRODUCT FROM LIST>	
*/
		while ($smr=$schemeMasterResultSetObj->getRow()) {
			$i++;
			$schemeMasterId = $smr[0];
			$schemeName	= $smr[1];
			$buyNum		= $smr[2];
			$buyBasedOn	= $smr[3];
			$selMrp		= $smr[4];
			$chkAllProduct  = "";
			if ($buyBasedOn=='P') {
				$chkAllProduct = $schemeMasterObj->chkAllProductSelectedRec($schemeMasterId, $buyBasedOn);
				$selAllProduct = "";
				if ($chkAllProduct) $selAllProduct = "- All Products";				
			}
			$displayBuyBasedOn = "";
			if ($buyBasedOn=='P')  $displayBuyBasedOn = "PRODUCT&nbsp;$selAllProduct";
			else if ($buyBasedOn=='M') $displayBuyBasedOn = "MRP&nbsp;$selMrp";
			
			$getNum		= $smr[5];
			$getProductType = $smr[6];
			$sampleProductId = $smr[10];
			$displayGetProductType = "";
			if ($getProductType=='MP') {
				$getMrpProductType = $smr[7];
				$disMrpProductType = "";
				if ($getMrpProductType=='G') {
					$getMrpGroupType = $smr[8];
					$selGroupMrp = $smr[9];
					$disMrpGroupType = "";
					if ($getMrpGroupType=='SM') $disMrpGroupType = "SAME MRP"; 
					else if ($getMrpGroupType=='LM') $disMrpGroupType = "LESS MRP"; 
					$disMrpProductType = "GROUP&nbsp;-&nbsp;$disMrpGroupType&nbsp;-&nbsp;$selGroupMrp";
				}
				else if ($getMrpProductType=='I') {
					$chkIndProduct = $schemeMasterObj->chkAllProductSelectedRec($schemeMasterId, $getMrpProductType);
					$selIndProduct = "";
					if ($chkIndProduct) $selIndProduct = "&nbsp;- All Products";			
				
					$disMrpProductType = "INDIVIDUAL $selIndProduct";
				}
				$displayGetProductType = "MRP PRODUCT - $disMrpProductType";
			}else if ($getProductType=='SP') {
				$sampleProductRec	= $sampleProductMasterObj->find($sampleProductId);
				$sampleProductName	= stripSlash($sampleProductRec[2]);
				$displayGetProductType = "SAMPLE PRODUCT - $sampleProductName";
			}
			$active=$smr[11];
			$existingrecords=$smr[12];
						
	?>
	<tr  <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php } else {?>bgcolor="WHITE" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$schemeMasterId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$schemeName;?></td>	
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$buyNum;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">		
			<table cellpadding="0" cellspacing="0">
				<TR>
					<TD class="listing-item"><?=$displayBuyBasedOn;?></TD>
					<TD class="listing-item"><? if (!$chkAllProduct) $buyAllProduct = $schemeMasterObj->listSelProduct($schemeMasterId, 'P');?></TD>
				</TR>
			</table>
		</td>		
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$getNum;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left">
			<table cellpadding="0" cellspacing="0" align="left">
				<TR>
					<TD class="listing-item" nowrap><?=$displayGetProductType;?></TD>
					<TD class="listing-item"><? if (!$chkIndProduct) $getIndProduct = $schemeMasterObj->listSelProduct($schemeMasterId, 'I');?></TD>
				</TR>
			</table>				
		</td>	
	<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$schemeMasterId;?>,'editId');this.form.action='SchemeMaster.php';" ></td>
	<? }?>


<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" Confirm " name="btnConfirm"  >
			<?php } else if ($active==1){ 
			//if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm"  >
			<?php
//			} 
}?>
			<? }?>




	</tr>
	<?
		
		}
	?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="<?=$editId?>">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="7" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"SchemeMaster.php?pageNo=$page&selRateList=$selRateList\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SchemeMaster.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SchemeMaster.php?pageNo=$page&selRateList=$selRateList\"  class=\"link1\">>></a> ";
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
												<td colspan="7"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
										</table>
	</td>
	</tr>
	<tr>
		<td colspan="3" height="5"></td>
	</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$schemeMasterRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSchemeMaster.php?selRateList=<?=$selRateList?>',700,600);"><? }?></td>
											</tr>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
		<!-- Form fields end   -->	
		</td>
		</tr>		
		<tr>
			<td height="10"></td>
		</tr>
		<!--<tr>
			<td height="10" align="center"><a href="MarginStructure.php" class="link1" title="Click to Manage Margin Structure">Margin Structure</a>
			</td>	
		</tr>-->
	</table>	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
