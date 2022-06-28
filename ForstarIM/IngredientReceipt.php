<?
	require("include/include.php");
	require_once("lib/IngredientPO_ajax.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;

	$userId		=	$sessObj->getValue("userId");
	
	$dateSelection =  "?selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"]."&pageNo=".$p["pageNo"];

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

	# Add Goods Receipt Note Start 
	if ($p["cmdAddNew"]!="") $addMode = true;
	
	if ($p["cmdCancel"]!="") $addMode = false;	

	# Ingredient Rate List
	$selIngRateListId = $ingredientRateListObj->latestRateList();

	#Add
	if ($p["cmdAdd"]!="" ) {
	
		$itemCount	=	$p["hidItemCount"];
		
		$selPoId	=	$p["selPoId"];
		$selDepartment	=	$p["selDepartment"];
		$billNo		=	$p["billNo"];
		$gateEntryNo	=	$p["gateEntryNo"];
		$storeEntry	=	$p["storeEntry"];
		$rejectedEntry	=	$p["rejectedEntry"];	
		$supplierId=$p["supplierId"];
		if ($selPoId!="" && $storeEntry!="") {

			$ingReceiptRecIns = $ingredientReceiptObj->addIngredientReceipt($selPoId, $selDepartment, $billNo, $gateEntryNo, $storeEntry, $rejectedEntry, $userId);
									
			$lastId = $databaseConnect->getLastInsertedId();
				
			for($i=1; $i<=$itemCount; $i++)
			{
				$rate="";
				$ingredientId	= $p["ingredientId_".$i];
				$quantity	= trim($p["quantity_".$i]);
				$qtyReceived	= trim($p["qtyReceived_".$i]);
				$qtyRejected	= trim($p["qtyRejected_".$i]);
				$remarks	= $p["remarks_".$i];
				$unitPrice	= $p["unitPrice_".$i];
				$newUnitPrice	= $p["newUnitPrice_".$i];
				$totalAmt	= $p["totalAmt_".$i];
				$totalQty = $ingredientMasterObj->getTotalStockQty($supplierId,$ingredientId);
				$currentStock = $totalQty + $qtyReceived;

				if ($lastId!="" && $ingredientId!="" && $quantity!="" && $qtyReceived) {
					$receivedItemsRecIns =	$ingredientReceiptObj->addReceivedEntries($lastId, $ingredientId, $quantity, $qtyReceived, $qtyRejected, $remarks, $currentStock, $unitPrice,$newUnitPrice, $totalAmt);
					$entryId = $databaseConnect->getLastInsertedId();
				
					#Update Ingredient Qty
					if($newUnitPrice!="")
					{	
						$rate=$newUnitPrice;
					}
					else
					{
						$rate=$unitPrice;
					}
					$supplierIngId =	$ingredientReceiptObj->getSupplierIngId($supplierId,$ingredientId);
					$addStockQty = $ingredientReceiptObj->addStockQty($supplierIngId,$lastId,$entryId, $qtyReceived);
					# update Price Variation
					//$updatePriceVariation = $ingredientReceiptObj->getIngPriceVariation($ingredientId, $selIngRateListId);
				}
			}
				
		}

		if ($receivedItemsRecIns) {
			$addMode	=	false;
			$sessObj->createSession("displayMsg",$msg_succAddIngredientReceipt);
			$sessObj->createSession("nextPage",$url_afterAddIngredientReceipt.$dateSelection);
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddIngredientReceipt;
		}
		$ingReceiptRecIns		=	false;
	}
	
	
	# Edit Goods Receipt Note
	
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$ingredientReceiptRec	=	$ingredientReceiptObj->find($editId);
		
		$editIngredientReceiptId	=	$ingredientReceiptRec[0];
		
		$editPONumber		=	$ingredientReceiptRec[1];
		$editDepartmentId	=	$ingredientReceiptRec[2];
		
		$billNo			=	$ingredientReceiptRec[3];
		$gateEntry		=	$ingredientReceiptRec[4];
		$storeEntry		=	$ingredientReceiptRec[5];
		$rejectedEntry		=	$ingredientReceiptRec[6];
		
		$Date			=	explode("-",$ingredientReceiptRec[8]);
		$enteredDate		=	$Date[2]."/".$Date[1]."/".$Date[0];				
			
		$ingredientReceiptRecs = $ingredientReceiptObj->fetchAllStockItem($editIngredientReceiptId);
	}


	#Update A Record
	if ($p["cmdSaveChange"]!="" ) {		
		$ingredientReceiptId	=	$p["hidIngredientReceiptId"];
		$itemCount	=	$p["hidItemCount"];		
		$selPoId	=	$p["selPoId"];
		$selDepartment	=	$p["selDepartment"];
		$supplierId=$p["supplierId"];
		$billNo		=	$p["billNo"];
		$gateEntryNo	=	$p["gateEntryNo"];
		$storeEntry	=	$p["storeEntry"];
		$rejectedEntry	=	$p["rejectedEntry"];
		$supplierId=$p["supplierId"];
		if ($ingredientReceiptId!="" && $selPoId!="" && $storeEntry!="")
		{
			$ingredientReceiptRecUptd = $ingredientReceiptObj->updateIngredientReceipt($ingredientReceiptId,$selPoId, $selDepartment, $billNo, $gateEntryNo, $storeEntry, $rejectedEntry);
		
			#Delete First all records from Goods Receipt Note entry table	
			//$deleteReceivedStockItem = $ingredientReceiptObj->deleteGoodsReceivedRecs($ingredientReceiptId);
			
			for ($i=1; $i<=$itemCount; $i++) {
				$ingredientId	=	$p["ingredientId_".$i];
				$quantity	=	trim($p["quantity_".$i]);
				$qtyReceived	=	trim($p["qtyReceived_".$i]);
				$qtyRejected	=	trim($p["qtyRejected_".$i]);
				$remarks	=	$p["remarks_".$i];
				$qtyAlreadyReceived = trim($p["qtyAlreadyReceived_".$i]);
				$unitPrice	= $p["unitPrice_".$i];
				$newUnitPrice	= $p["newUnitPrice_".$i];
				$totalAmt	= $p["totalAmt_".$i];
				$ingReceiptEntryId = $p["ingReceiptEntryId_".$i];
				#Update the current stock
				$totalQty = $ingredientMasterObj->getTotalStockQty($ingredientId);
				$stockQty = $qtyReceived-$qtyAlreadyReceived;
				$currentStock = $totalQty + $stockQty;

				if($newUnitPrice!="")
				{	
					$rate=$newUnitPrice;
				}
				else
				{
					$rate=$unitPrice;
				} 

				//echo "$qtyAlreadyReceived!=$qtyReceived";
				//echo "total=$totalQty,StockQty=$stockQty, custock=$currentStock";
				if ($ingredientReceiptId!="" && $ingredientId!="" && $quantity!="") {
					if ($ingReceiptEntryId!="") { 
						//echo $newUnitPrice.','.$totalAmt;
						//	die();
						$updateReceivedItemsRec = $ingredientReceiptObj->updateReceivedEntry($ingReceiptEntryId, $ingredientId, $quantity, $qtyReceived, $qtyRejected, $remarks, $currentStock, $unitPrice,$newUnitPrice, $totalAmt);
					}
					else {
						$receivedItemsRecIns	=	$ingredientReceiptObj->addReceivedEntries($ingredientReceiptId, $ingredientId, $quantity, $qtyReceived, $qtyRejected, $remarks, $currentStock, $unitPrice,$newUnitPrice, $totalAmt);
					}

					#Update the Stock Qty [Qty>0 add stock else Less stock ]
					if ($qtyAlreadyReceived!=$qtyReceived) {
						$supplierIngId =	$ingredientReceiptObj->getSupplierIngId($supplierId,$ingredientId);
						$updateStock	=	$ingredientReceiptObj->updateStock($supplierIngId,$ingReceiptEntryId,$ingredientReceiptId,$qtyReceived);
						//$stkQty = $qtyReceived-$qtyAlreadyReceived;
						//$updateStockQty = $ingredientReceiptObj->updateStockQty($ingredientId, $stkQty);
					}
					# update Price Variation
				//	$updatePriceVariation = $ingredientReceiptObj->getIngPriceVariation($ingredientId, $selIngRateListId);
				}
			}
		}
	
		if ($ingredientReceiptRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succIngredientReceiptUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateIngredientReceipt.$dateSelection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failIngredientReceiptUpdate;
		}
		$ingredientReceiptRecUptd	=	false;
	}
	

	# Delete Ingredient Receipt Note
	
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$ingredientReceiptId	=	$p["delId_".$i];
			$poId			=	$p["poId_".$i];			

			if ($ingredientReceiptId!="") {
				$ingredientReceiptRecDel =	$ingredientReceiptObj->deleteIngredientReceipt($ingredientReceiptId, $poId);
				$deleteReceivedStockItem	=	$ingredientReceiptObj->deleteGoodsReceivedRecs($ingredientReceiptId);
				$deleteSupplierStockItem	=	$ingredientReceiptObj->deleteSuplierStockRecs($ingredientReceiptId);
			}
		}
		if ($ingredientReceiptRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelIngredientReceipt);
			$sessObj->createSession("nextPage",$url_afterDelIngredientReceipt.$dateSelection);
		} else {
			$errDel	=	$msg_failDelIngredientReceipt;
		}
		$ingredientReceiptRecDel	=	false;
	}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!= "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!= "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------		

	# select records between selected date
	if ($g["selectFrom"]!="" && $g["selectTill"]!="") {
		$dateFrom = $g["selectFrom"];
		$dateTill = $g["selectTill"];
	} else if ($p["selectFrom"]!="" && $p["selectTill"]!="") {
		$dateFrom = $p["selectFrom"];
		$dateTill = $p["selectTill"];
	} else {
		$dateFrom = date("d/m/Y");
		$dateTill = date("d/m/Y");
	}

	#List all Purchase Order
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		#List all Ingredient Receipt Note
		$ingredientReceiptRecords = $ingredientReceiptObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);		
		$ingredientReceiptSize	= sizeof($ingredientReceiptRecords);
		#Pagination
		$fetchAllIngReceiptRecords = $ingredientReceiptObj->fetchAllRecords($fromDate, $tillDate);
	}	

	## -------------- Pagination Settings II -------------------
	$numrows	=	sizeof($fetchAllIngReceiptRecords);
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	#List all Ordered Items Details
	if ($p["editSelectionChange"]=='1' || $p["selPoId"]=="") {
		$selPoId = $editPONumber;
	} else {
		$selPoId		=	$p["selPoId"];
	}
	
	//$selPoId = $p["selPoId"];
	if ($addMode || $editMode) {
		$purchaseOrderRec	=	$ingredientPurchaseorderObj->find($selPoId);
		$SupplierId		=	$purchaseOrderRec[2];
		$pOnumber		=	$purchaseOrderRec[1];
		$supplierRec		=	$supplierMasterObj->find($SupplierId);
		$supplierName		=	stripSlash($supplierRec[2]);
	}
	
	#Fetch all Stock Item;
	if ($selPoId) $purchaseRecs = $ingredientPurchaseorderObj->fetchAllStockItem($selPoId);


	#List all Purchase Order Number
	$mode = "";
	if ($addMode) $mode = "A";	// Add Mode
	else if ($editMode) $mode = "E"; // Edit Mode
	if ($mode) $purchaseOrderRecords = $ingredientReceiptObj->fetchAllPORecords($mode);
	
	# Ingredient Rate List
	$selIngRateListId = $ingredientRateListObj->latestRateList();

	# List all Department 
	//$departmentRecords	=	$departmentObj->fetchAllRecords();
	$departmentRecords	=	$departmentObj->fetchAllRecordsActivedept();

	if ($editMode) $heading	= $label_editIngredientReceipt;
	else $heading = $label_addIngredientReceipt;
	$ON_LOAD_SAJAX = "Y"; // SAJAX, settings for TopLeftNav	

	$ON_LOAD_PRINT_JS	= "libjs/IngredientReceipt.js";	

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<!-- rekha added code -->
	<table width="100%" border="1" style= "border: 1px solid #ddd;background-color:#f5f5f5;">
	<tr>
	<td width="15%" valign="top">
	<?php 
		require("template/sidemenuleft.php");
	?>
	</td>
	<td width="85%" valign="top" align="left">
	<form name="frmIngredientReceipt" action="IngredientReceipt.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%" >
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
		</tr>
		<?
		if( $editMode || $addMode)
		{
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
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientReceipt.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateIngredientReceipt(document.frmIngredientReceipt);">												
												</td>
												<?} else{?>
												<td  colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientReceipt.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateIngredientReceipt(document.frmIngredientReceipt);"> &nbsp;&nbsp;												
												</td>
												<?}?>
											</tr>
											<input type="hidden" name="hidIngredientReceiptId" value="<?=$editIngredientReceiptId;?>">
											<tr>
												<td class="fieldName" nowrap >&nbsp;</td>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td colspan="2" nowrap class="fieldName" >
													<table width="200">
														<tr>
															<td class="fieldName">*PO Number:</td>
															<td class="listing-item">
															 <?
															 if ($addMode==true) {
															 ?>
															<!--<select name="selPoId" id="selPoId" onchange="this.form.submit();">	-->
																<select name="selPoId" id="selPoId" onchange="getPONumber(this);">	
																	<option value="">-- Select --</option>
																	<?
																	foreach ($purchaseOrderRecords as $por) {
																		$purchaseOrderId	=	$por[0];
																		$poGeneratedId 		=	$por[1];
																		$selected	=	"";
																		if($selPoId==$purchaseOrderId) $selected="Selected";
																	?>
																	<option value="<?=$purchaseOrderId?>" <?=$selected?>><?=$poGeneratedId?></option>
																	<? }?>
																</select>
															<?  } //AddMode end
															if ($editMode) {
															?>
															<input type="text" name="pONumber" value="<?=$pOnumber?>" style="border:none;">
															<input type="hidden" name="selPoId" value="<?=$selPoId?>">
															<? }?>
														</td>
													</tr>
													<tr>
														<td class="fieldName">*Department</td>
														<td class="listing-item">
															<select name="selDepartment" id="selDepartment">
																<option value="">-- Select --</option>
																<?
																foreach($departmentRecords as $cr)
																{
																	$departmentId		=	$cr[0];
																	$departmentName	=	stripSlash($cr[1]);
																	$selected="";
																	if($editDepartmentId==$departmentId) echo $selected="Selected";
																
																 ?>
																<option value="<?=$departmentId?>" <?=$selected?>><?=$departmentName?></option>
																<? }?>
															</select>                                                  
														</td>
													</tr>
													<tr>
														<td class="fieldName">Supplier</td>
														<td class="listing-item"><input name="supplier" type="text" id="supplier" value="<?=$supplierName?>" size="12" readonly />
														<input name="supplierId" id="SupplierId" value="<?=$SupplierId?>" type="hidden"/>
														</td>
													</tr>
													<tr>
														<td class="fieldName">Bill No </td>
														<td class="listing-item"><input name="billNo" type="text" id="billNo" value="<?=$billNo?>"></td>
													</tr>
													<tr>
														<td class="fieldName" nowrap="nowrap">*Gate Entry No</td>
														<td class="listing-item"><input name="gateEntryNo" type="text" id="gateEntryNo" value="<?=$gateEntry?>"></td>
													</tr>
													<tr>
														<td class="fieldName" nowrap="nowrap">*Store Entry No</td>
														<td class="listing-item"><input name="storeEntry" type="text" id="storeEntry" value="<?=$storeEntry?>"></td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td colspan="2" nowrap style="padding-left:5px;padding-right:5px;">
											  <?
											  if (sizeof($purchaseRecs) > 0) {
												$j=0;
											  ?>
												<table width="300" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddItem">
													<tr bgcolor="#f2f2f2" align="center">
														<td class="listing-head">Ingredient</td>
														<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Ordered <br>Qty</td>
														<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Received <br>Qty</td>
														<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Actual <br>Qty</td>
														<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Accepted <br>Qty</td>
														<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;"> Rejected <br>Qty</td>
														<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Rate/Kg</td>
														<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">New Rate/Kg</td>
														<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Total Amt</td>	
														<td class="listing-head" style="padding-left:5px; padding-right:5px;">Remarks</td>
													</tr>
													<?
													$grandTotalAmt = 0;
													foreach ($purchaseRecs as $pr)	{
														$j++;
														$ingredientId	= $pr[2];
														$ingredientRec	= $ingredientMasterObj->find($ingredientId);
														$ingredientName	= stripSlash($ingredientRec[2]);
														//$quantity	= $pr[4];
														$orderedQty	= $pr[4];
														$unitPrice	= $pr[3];

														$rec 		= $ingredientReceiptRecs[$j-1];	
														$totalAmt = 0;			
														if ($p["editSelectionChange"]=='1') {
															$ingReceiptEntryId = $rec[0];	
															$qtyReceived	= $rec[4];
															$qtyRejected	= $rec[5];
															$remarks	= $rec[6];
															$actualQty	= $rec[3];
															$unitPrice	= $rec[8];
															$totalAmt	= $rec[9];
															$newUnitPrice	= $rec[10];
														} else {
															$ingReceiptEntryId = "";	
															$qtyReceived	= "";
															$qtyRejected	= "";
															$remarks	= "";
															$actualQty	= "";	
															$totalAmt = 0;
														}
														$grandTotalAmt	+= $totalAmt;
														# Find the Total Received Qty
														$totalReceivedQty = $ingredientReceiptObj->getReceivedQtyOfIngredient($ingredientId, $selPoId);
														?>
														<tr bgcolor="#FFFFFF" align="center">
															<td class="fieldName" nowrap="nowrap"><?=$ingredientName?>
																<input type="hidden" value="<?=$ingredientId?>" name="ingredientId_<?=$j?>" id="ingredientId_<?=$j?>">
																<input type="hidden" name="ingReceiptEntryId_<?=$j?>" id="ingReceiptEntryId_<?=$j?>" value="<?=$ingReceiptEntryId?>">
															</td>
															<td class="fieldName">
																<input name="orderedQty_<?=$j?>" type="text" id="orderedQty_<?=$j?>" size="6" style="text-align:right; border:none;" value="<?=$orderedQty?>" readonly="true">
															</td>
															<td class="fieldName">
																<input name="receivedQty_<?=$j?>" type="text" id="receivedQty_<?=$j?>" size="6" style="text-align:right; border:none;" value="<?=$totalReceivedQty?>" readonly="true">
															</td>
															<td class="fieldName">
																<input name="quantity_<?=$j?>" type="text" id="quantity_<?=$j?>" size="6" style="text-align:right;" value="<?=$actualQty?>" onkeyup="return calcIngredientReject(document.frmIngredientReceipt);" autoComplete="off">
															</td>
														   <td class="fieldName">
																<input name="qtyReceived_<?=$j?>" type="text" id="qtyReceived_<?=$j?>" size="6" style="text-align:right" value="<?=$qtyReceived?>" onkeyup="return calcIngredientReject(document.frmIngredientReceipt);" autoComplete="off">
																<!--	While Updating -->
																<input name="qtyAlreadyReceived_<?=$j?>" type="hidden" id="qtyAlreadyReceived_<?=$j?>" size="4" value="<?=$qtyReceived?>">
															</td>
															<td class="fieldName">
																<input name="qtyRejected_<?=$j?>" type="text" id="qtyRejected_<?=$j?>" size="6" readonly style="text-align:right;border:none;" value="<?=$qtyRejected?>" />
															</td>
															
															<td class="fieldName">
																<input name="unitPrice_<?=$j?>" type="text" id="unitPrice_<?=$j?>" size="6" style="text-align:right;" value="<?=$unitPrice?>" onkeyup="calcIngReceivedAmt();" readonly autoComplete="off"/>
															</td>
															<td class="fieldName">
																<input name="newUnitPrice_<?=$j?>" type="text" id="newUnitPrice_<?=$j?>" size="6" style="text-align:right;" value="<?=$newUnitPrice?>"  onkeyup="calcIngReceivedAmt();"  autoComplete="off"/>
															</td>
															<td class="fieldName">
																<input name="totalAmt_<?=$j?>" type="text" id="totalAmt_<?=$j?>" size="6" readonly style="text-align:right;border:none;" value="<?=$totalAmt?>" />
															</td>
															<td class="fieldName">
																<textarea name="remarks_<?=$j?>" id="remarks_<?=$j?>"><?=$remarks?></textarea>
															</td>
														</tr>
														<?
														}										
														?>
														<tr bgcolor="#FFFFFF" align="center">						
															<td class="listing-head" align="right" colspan="8">Grand Total Amt:</td>
															<td class="fieldName">
																<input name="grandTotalAmt" type="text" id="grandTotalAmt" size="7" style="text-align:right; border:none;" readonly value="<?=$grandTotalAmt;?>">
															</td>				
															<td></td>
														</tr>
													</table>
												  <? 
													}
													?>										
												  </td>
												</tr>
												<input type="hidden" name="hidItemCount" id="hidItemCount" value="<?=$j;?>">
												<tr>
													<td colspan="2" nowrap class="fieldName">
														<table width="200">
															<tr>
																<td class="fieldName" nowrap="nowrap">Rejected Material Gate pass No.</td>
																<td class="listing-item">
																	<input name="rejectedEntry" type="text" id="rejectedEntry" value="<?=$rejectedEntry?>">
																</td>
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
														<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientReceipt.php');">&nbsp;&nbsp;
														<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateIngredientReceipt(document.frmIngredientReceipt);">												
													</td>
													<?} else{?>
													<td  colspan="2" align="center">
														<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('IngredientReceipt.php');">&nbsp;&nbsp;
														<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateIngredientReceipt(document.frmIngredientReceipt);">&nbsp;&nbsp;												
													</td>
													<input type="hidden" name="cmdAddNew" value="1">
													<?}?>
														<input type="hidden" name="stockType" value="<?=$stockType?>" />
												</tr>
												<tr>
													<td colspan="2"  height="10" ></td>
												</tr>
											</table>									
										</td>
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
					<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
						<tr>
							<td   bgcolor="white">
								<!-- Form fields start -->
								<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
									<tr>
										<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
										<td background="images/heading_bg.gif" class="pageName" nowrap>&nbsp;Ingredient Receipt Note</td>
										<td background="images/heading_bg.gif" align="right" nowrap>
											<table cellpadding="0" cellspacing="0">
												<tr>
													<td nowrap="nowrap">
														<table cellpadding="0" cellspacing="0">
															<tr>
																<td class="listing-item"> From:</td>
																<td nowrap="nowrap"> 
																<? 
																if ($dateFrom=="") $dateFrom=date("d/m/Y");
																?>
																	<input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>">
																</td>
																<td class="listing-item">&nbsp;</td>
																<td class="listing-item"> Till:</td>
																<td> 
																<? 
																if($dateTill=="") $dateTill=date("d/m/Y");
																?>
																<input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>">
															</td>
															<td class="listing-item">&nbsp;</td>
															<td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search"></td>
															<td class="listing-item" nowrap >&nbsp;</td>
														</tr>
													</table>
												</td>
											</tr>
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
													<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$ingredientReceiptSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintIngredientReceipt.php?fd=<?=$fromDate?>&td=<?=$tillDate?>',700,600);"><? }?>
												</td>
											</tr>
										</table>									
									</td>
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
											if (sizeof($ingredientReceiptRecords)>0) {
												$i = 0;
										?>
										<? if($maxpage>1){?>
											<tr  bgcolor="#f2f2f2" align="center">
												<td colspan="6" bgcolor="#FFFFFF" style="padding-right:10px;">
													<div align="right">
													<?php 				 			  
													$nav  = '';
													for($page=1; $page<=$maxpage; $page++)
													{
														if ($page==$pageNo)
														{
																$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
														}
														else
														{
																$nav.= " <a href=\"IngredientReceipt.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
															//echo $nav;
														}
													}
													if ($pageNo > 1)
													{
														$page  = $pageNo - 1;
														$prev  = " <a href=\"IngredientReceipt.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
													}
													else
													{
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage)
													{
														$page = $pageNo + 1;
														$next = " <a href=\"IngredientReceipt.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
													}
													else
													{
														$next = '&nbsp;'; // we're on the last page, don't print next link
														$last = '&nbsp;'; // nor the last page link
													}
													// print the navigation link
													$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
													echo $first . $prev . $nav . $next . $last . $summary; 
													?>
													</div>
												</td>
											</tr>
											<? }?>
											<tr  bgcolor="#f2f2f2" align="center">
												<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;">GRN No </td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;">PO ID</td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;">Date</td>
												<? if($edit==true){?>
												<td class="listing-head"></td>
												<? }?>
											</tr>
											<?
											foreach ($ingredientReceiptRecords as $grr) {
												$i++;
												$ingredientReceiptId	=	$grr[0];
												$poId			=	$grr[1];						
												$storeEntry		=	$grr[5];
												$Date			=	explode("-",$grr[7]);
												$createdDate		=	$Date[2]."/".$Date[1]."/".$Date[0];
												$supplierName		=	$grr[9];
												$generatedPOId		=	$grr[10];
											?>
											<tr  bgcolor="WHITE">
												<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$ingredientReceiptId;?>" class="chkBox">
												<input type="hidden" name="poId_<?=$i;?>" id="poId_<?=$i;?>" value="<?=$poId;?>"></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$storeEntry;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$generatedPOId;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierName;?></td>
												<td class="listing-item" width="60" style="padding-left:10px; padding-right:10px;"><?=$createdDate?></td>
												<? if($edit==true){?>
												<td class="listing-item" width="60" align="center">
												<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$ingredientReceiptId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='IngredientReceipt.php';">
												</td>
												<? }?>
											</tr>
											<?
												}
											?>
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="editId" value="">
											<input type="hidden" name="editSelectionChange" value="0">
											<? if($maxpage>1){?>
											<tr bgcolor="#FFFFFF">
												<td colspan="6" style="padding-right:10px;">
													<div align="right">
													<?php 				 			  
													$nav  = '';
													for ($page=1; $page<=$maxpage; $page++) {
															if ($page==$pageNo) {
																	$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
															} else {
																	$nav.= " <a href=\"IngredientReceipt.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
																//echo $nav;
															}
													}
													if ($pageNo > 1) {
														$page  = $pageNo - 1;
														$prev  = " <a href=\"IngredientReceipt.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
													} else {
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}
													if ($pageNo < $maxpage) {
														$page = $pageNo + 1;
														$next = " <a href=\"IngredientReceipt.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
													} else {
														$next = '&nbsp;'; // we're on the last page, don't print next link
														$last = '&nbsp;'; // nor the last page link
													}
													// print the navigation link
													$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
													echo $first . $prev . $nav . $next . $last . $summary; 
													 ?>
													</div>
													 <input type="hidden" name="pageNo" value="<?=$pageNo?>">
												</td>
											</tr>
											<? }?>
										<?
										}
										else
										{
										?>
											<tr bgcolor="white">
												<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
											<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$ingredientReceiptSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintIngredientReceipt.php?fd=<?=$fromDate?>&td=<?=$tillDate?>',700,600);"><? }?>
											</td>
										</tr>
									</table>									
								</td>
							</tr>
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
						</table>						
					</td>
				</tr>
			</table>
					<!-- Form fields end   -->			
		</td>
	</tr>	
	<tr>
		<td height="10"></td>
	</tr>
</table>
 	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selectFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "selectTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selectTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>	
	</form></td></tr></table>


<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>