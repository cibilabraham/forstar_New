<?
	require("include/include.php");
	require("lib/goodsreceipt_ajax.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	$userId		=	$sessObj->getValue("userId");
	
	$dateSelection =  "?selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"]."&pageNo=".$p["pageNo"];

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

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
	
	#Add
	if ($p["cmdAdd"]!="" ) {
	
		$itemCount	=	$p["hidItemCount"];
		$selPoId	=	$p["selPoId"];
		$selDepartment	=	$p["selDepartment"];
		$challanNo	=	$p["challanNo"];
		$billNo		=	$p["billNo"];
		$gateEntryNo	=	$p["gateEntryNo"];
		$storeEntry	=	$p["storeEntry"];
		$rejectedEntry	=	$p["rejectedEntry"];	
		$totalPOQty 	= $p["totalPOQty"];
		$grnRemarks	= addSlash(trim($p["grnRemarks"]));
		$companyId	=	$p["companyId"];	
		$plantId 	= $p["plantId"];
		$supplierId 	= $p["supplierId"];

		
		if ($selPoId!="" && $storeEntry!="") {
			$goodsReceiptRecIns = $goodsreceiptObj->addGoodsReceipt($selPoId, $selDepartment, $challanNo, $billNo, $gateEntryNo, $storeEntry, $rejectedEntry, $userId, $grnRemarks,$companyId,$plantId);
									
			$lastId = $databaseConnect->getLastInsertedId();
				
			for ($i=1; $i<=$itemCount; $i++) {
				//$plantId=$p["plantId_".$i];
				$stockId	=	$p["stockId_".$i];
				$quantity	=	trim($p["quantity_".$i]);
				$qtyReceived	=	trim($p["qtyReceived_".$i]);
				$qtyRejected	=	trim($p["qtyRejected_".$i]);
				$remarks	=	addSlash(trim($p["remarks_".$i]));
				$chkPointRowCount = $p["chkPointRowCount_".$i];
				$checkPoint    = ($chkPointRowCount>0)?"Y":"N";
				$confirmation=$p["confirmextraQty_".$i];
				$notover=$p["notover_".$i];
				$extraquantity=$p["quantity_".$i];
				$quantityafterextra=$p["extraQty_".$i];

				$checkExist=$goodsreceiptObj->chkValidDateEntry($supplierId,$stockId);
				$supplierStockId=$checkExist[0];
				$totalQty=$physicalStockInventoryObj->getSupplierQty($supplierStockId,$supplierId,$stockId);
				//$totalQty = $stockissuanceObj->getTotalStockQty($stockId);

				$currentStock = $totalQty + $qtyReceived;
				
				if ($lastId!="" && $stockId!="" && $quantity!="") {

					$receivedItemsRecIns	=	$goodsreceiptObj->addReceivedEntries($lastId, $stockId, $quantity, $qtyReceived, $qtyRejected, $remarks, $currentStock, $checkPoint,$confirmation,$notover,$extraquantity,$quantityafterextra);
					
					#Update Stock Qty
					$companyUnitId=$goodsreceiptObj->getCompanyUnitId($supplierId,$stockId,$companyId,$plantId);
					//echo $companyUnitId;
					//die();
					if(sizeof($checkExist)>0)
					{
						$stockDate=date("Y-m-d");
						
						$supplierQty=$goodsreceiptObj->addSupplierStock($supplierStockId,$supplierId,$stockId,$qtyReceived,$stockDate,$lastId,$companyUnitId);
					
					}


					/*$checkExist=$stockObj->checkStockExistUnit($stockId, $qtyReceived,$plantId);
					if ($checkExist)
					{
					//$stockToUnit=$stockObj->updateStockToUnit($stockIdFrom,$qty,$unitTo);
					$updateStockQty = $goodsreceiptObj->updateStockQty($stockId, $qtyReceived,$plantId);
					}
					else {
					//$stockToUnit=$stockObj->addUnitStock($unitTo,$qty,$stockIdFrom);
					$addStockQty = $goodsreceiptObj->addStockQty($stockId, $qtyReceived,$plantId);

					}*/
					
					if ($receivedItemsRecIns) {
						$goodsReceiptEntryId = $databaseConnect->getLastInsertedId();
						if ($chkPointRowCount>0) {
							for ($j=1; $j<=$chkPointRowCount; $j++) {
								$chkPointId	= $p["chkPointId_".$i."_".$j];
								$chkPointAnswer = ($p["chkPointAnswer_".$i."_".$j]!="")?$p["chkPointAnswer_".$i."_".$j]:"N";
								$chkPointRemarks = $p["chkPointRemarks_".$i."_".$j];
								if ($goodsReceiptEntryId!="" && $chkPointId!="") {
									$checkPointRecIns = $goodsreceiptObj->addGRCheckPoint($goodsReceiptEntryId, $chkPointId, $chkPointAnswer, $chkPointRemarks);
								}
							}
						}
					}
				}
			}
			# Update GRN Status
			$updateGRNStatus = $goodsreceiptObj->updateGRNStatus($selPoId,$totalPOQty);		
		}

		if ($goodsReceiptRecIns) {
			$addMode	=	false;
			$sessObj->createSession("displayMsg",$msg_succAddGoodsReceipt);
			$sessObj->createSession("nextPage",$url_afterAddGoodsReceipt.$dateSelection);
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddGoodsReceipt;
		}
		$goodsReceiptRecIns		=	false;
	}	
		
	
	# Edit Goods Receipt Note	
	if ($p["editId"]!="" ) {
		$addMode		= 	false;
		$editId			=	$p["editId"];
		$editMode		=	true;
		$goodsReceiptRec	=	$goodsreceiptObj->find($editId);
		
		$editGoodsReceiptId	=	$goodsReceiptRec[0];		
		$editPONumber		=	$goodsReceiptRec[1];		
		$editDepartmentId	=	$goodsReceiptRec[2];
		$challanNo		=	$goodsReceiptRec[3];
		$billNo			=	$goodsReceiptRec[4];
		$gateEntry		=	$goodsReceiptRec[5];
		$storeEntry		=	$goodsReceiptRec[6];
		$rejectedEntry		=	$goodsReceiptRec[7];
		//$poNumber		=	$goodsReceiptRec[2];
		$grnRemarks		= 	stripSlash($goodsReceiptRec[10]);
		
		$Date			=	explode("-",$goodsReceiptRec[8]);
		$enteredDate		=	$Date[2]."/".$Date[1]."/".$Date[0];
			
		$goodsReceiptRecs = $goodsreceiptObj->fetchAllStockItem($editGoodsReceiptId,'');
		#List all Purchase Order Number
		$purchaseOrderRecords = $goodsreceiptObj->fetchAllPORecords($editGoodsReceiptId);
		$fieldDisabled = "disabled";
	}


	#Update A Record
	if ($p["cmdSaveChange"]!="" ) {
		
		$goodsReceiptId	=	$p["hidGoodsReceiptId"];
		$itemCount	=	$p["hidItemCount"];		
		//$selPoId	=	$p["selPoId"];
		$selDepartment	=	$p["selDepartment"];
		$challanNo	=	$p["challanNo"];
		$billNo		=	$p["billNo"];
		$gateEntryNo	=	$p["gateEntryNo"];
		$storeEntry	=	$p["storeEntry"];
		$rejectedEntry	=	$p["rejectedEntry"];
		$poId		=	$p["hidPO"];
		$totalPOQty 	= 	$p["totalPOQty"];
		$grnRemarks	= 	addSlash(trim($p["grnRemarks"]));
		$companyId	=	$p["companyId"];	
		$plantId 	= $p["plantId"];
		$supplierId 	= $p["supplierId"];

		//&& $selPoId!=""
		if ($goodsReceiptId!=""  && $storeEntry!="") {
			$goodsReceiptRecUptd	=	$goodsreceiptObj->updateGoodsReceipt($goodsReceiptId, $selDepartment, $challanNo, $billNo, $gateEntryNo, $storeEntry, $rejectedEntry, $grnRemarks);
			
			for ($i=1; $i<=$itemCount; $i++) {
				$stockId	=	$p["stockId_".$i];
				$quantity	=	trim($p["quantity_".$i]);
				$qtyReceived	=	trim($p["qtyReceived_".$i]);
				$qtyRejected	=	trim($p["qtyRejected_".$i]);
				$remarks	=	$p["remarks_".$i];
				$qtyAlreadyReceived = $p["qtyAlreadyReceived_".$i];
				$grnEntryId	=	$p["hidGrnEntryId_".$i];
				$plantId=$p["plantId_".$i];
				$chkPointRowCount = $p["chkPointRowCount_".$i];
				$checkPoint    = ($chkPointRowCount>0)?"Y":"N";
				$confirmation=$p["confirmextraQty_".$i];
				$notover=$p["notover_".$i];
				$extraquantity=$p["quantity_".$i];
				$quantityafterextra=$p["extraQty_".$i];
				#Update the current stock
				
				$checkExist=$goodsreceiptObj->chkValidDateEntry($supplierId,$stockId);
				$supplierStockId=$checkExist;
				$totalQty=$physicalStockInventoryObj->getSupplierQty($supplierStockId,$supplierId,$stockId);
				$stockQty = $qtyReceived-$qtyAlreadyReceived;
				$currentStock = $totalQty + $stockQty;

				
				if ($goodsReceiptId!="" && $stockId!="" && $quantity!="") {
					$goodsReceiptEntryId = "";
					if ($grnEntryId!="") {
						$updateReceivedItemsRecIns = $goodsreceiptObj->updateReceivedItemRec($goodsReceiptId, $stockId, $quantity, $qtyReceived, $qtyRejected, $remarks, $currentStock, $grnEntryId, $checkPoint,$confirmation,$notover,$extraquantity,$quantityafterextra);
						$goodsReceiptEntryId = $grnEntryId;
					} else if ($grnEntryId=="") {
						$receivedItemsRecIns	=	$goodsreceiptObj->addReceivedEntries($goodsReceiptId, $stockId, $quantity, $qtyReceived, $qtyRejected, $remarks, $currentStock, $checkPoint,$confirmation,$notover,$extraquantity,$quantityafterextra);
						$goodsReceiptEntryId = $databaseConnect->getLastInsertedId();
					}

				if(sizeof($checkExist)>0)
				{
					$stockDate=date("Y-m-d");
					$supplierStockRecDel =	$goodsreceiptObj->deleteSupplierStock($goodsReceiptId);		
					$supplierQty=$goodsreceiptObj->addSupplierStock($supplierStockId,$supplierId,$stockId,$qtyReceived,$stockDate,$goodsReceiptId);
				}


					if ($chkPointRowCount>0) {
						for ($j=1; $j<=$chkPointRowCount; $j++) {
			
							$grCPEntryId	= $p["grCPEntryId_".$i."_".$j];

							$chkPointId	= $p["chkPointId_".$i."_".$j];
							$chkPointAnswer = ($p["chkPointAnswer_".$i."_".$j]!="")?$p["chkPointAnswer_".$i."_".$j]:"N";
							$chkPointRemarks = $p["chkPointRemarks_".$i."_".$j];
							if ($goodsReceiptEntryId!="" && $chkPointId!="" && $grCPEntryId=="") {
								$checkPointRecIns = $goodsreceiptObj->addGRCheckPoint($goodsReceiptEntryId, $chkPointId, $chkPointAnswer, $chkPointRemarks);
							} else if($goodsReceiptEntryId!="" && $chkPointId!="" && $grCPEntryId!="") {
								$updateChkPointRecs = $goodsreceiptObj->updateGRCheckPoint($goodsReceiptEntryId, $chkPointId, $chkPointAnswer, $chkPointRemarks, $grCPEntryId);
							}
						}
					}

					#Update the Stock Qty [Qty>0 add stock else Less stock ]
					if ($qtyAlreadyReceived!=$qtyReceived) {
						$stockQty = $qtyReceived-$qtyAlreadyReceived;			
						$updateStockQty = $goodsreceiptObj->updateStockQty($stockId, $stockQty,$plantId);
					}
				}
			}
		}
		
		$updateGRNStatus = $goodsreceiptObj->updateGRNStatus($poId,$totalPOQty);
	
		if ($goodsReceiptRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succGoodsReceiptUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateGoodsReceipt.$dateSelection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failGoodsReceiptUpdate;
		}
		$goodsReceiptRecUptd	=	false;
	}
	

	# Delete Goods Receipt Note
	
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$goodsReceiptId	= $p["delId_".$i];
			$poId		= $p["poId_".$i];

			if ($goodsReceiptId!="" && $isAdmin!="") {				
				$deleteReceivedStockItem =	$goodsreceiptObj->deleteGoodsReceivedRecs($goodsReceiptId);
				$goodsReceiptRecDel =	$goodsreceiptObj->deleteGoodsReceipt($goodsReceiptId);	
				$supplierStockRecDel =	$goodsreceiptObj->deleteSupplierStock($goodsReceiptId);	
				# Update the PO Status
				$updatePOstatus = $goodsreceiptObj->updatePOStatus($poId);
			}

		}
		if ($goodsReceiptRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelGoodsReceipt);
			$sessObj->createSession("nextPage",$url_afterDelGoodsReceipt.$dateSelection);
		} else {
			$errDel	=	$msg_failDelGoodsReceipt;
		}
		$goodsReceiptRecDel	=	false;
	}
	
	
	#List all Ordered Items Details	
	if ($p["editSelectionChange"]=='1' || $p["selPoId"]=="") {
		$selPoId = $editPONumber;
	} else {
		$selPoId = $p["selPoId"];
	}
		
	$purchaseOrderRec	=	$goodsreceiptObj->fetchPOList($selPoId);
	$SupplierId		=	$purchaseOrderRec[3];
	$supplierRec		=	$supplierMasterObj->find($SupplierId);
	$supplierName		=	stripSlash($supplierRec[2]);
	$companyId=$purchaseOrderRec[10];
	$companyName=$purchaseOrderRec[11];
	$plantUnitId=$purchaseOrderRec[12];
	$plantName=$purchaseOrderRec[13];
	
	# Fetch all Stock Item	(From Purchase Order Section)
	$purchaseRecs = $purchaseOrderInventoryObj->fetchAllStockItem($selPoId,'');
	# List all Department 
	//$departmentRecords = $departmentObj->fetchAllRecords();
	$departmentRecords = $goodsreceiptObj->getAllDepartmentUser($userId);
	
	if ($addMode) {	
		#List all Purchase Order Number
		//$purchaseOrderRecords = $goodsreceiptObj->fetchAllNotReceivedPORecords();
		$purchaseOrderRecords = $goodsreceiptObj->fetchAllPORecords();
	}
	//edited$purchaseOrderRecords		=	$purchaseOrderInventoryObj->fetchAllRecords();
		
	#List all Stock Based on Supplier Id
	if ($addMode==true) {
		//$stockRecords				=	$supplierstockObj->fetchSupplierStocks($selSupplierId);
	}
	# List all Supplier
	//$supplierRecords = $supplierMasterObj->fetchAllRecords();

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") 		$pageNo = $p["pageNo"];
	else if ($g["pageNo"]!="") 	$pageNo = $g["pageNo"];
	else 				$pageNo = 1;
	
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

		#List all Goods Receipt Note	
		$goodsReceiptRecords = $goodsreceiptObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		$goodsReceiptSize = sizeof($goodsReceiptRecords);

		// For pagination
		$fetchAllGdsReceiptRecs = $goodsreceiptObj->fetchAllDateRangeRecords($fromDate, $tillDate);
	}
	//$gdsReceiptRecords = $goodsreceiptObj->fetchAllRecords();

	## -------------- Pagination Settings II -------------------
	$numrows	=	sizeof($fetchAllGdsReceiptRecs);
	$maxpage	=	ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	if ($editMode) $heading = $label_editGoodsReceipt;
	else $heading = $label_addGoodsReceipt;
	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/goodsreceipt.js";

	$modeVal = 1;
	if( $editMode ) $modeVal = 2;

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");	
?>

<form name="frmGoodsReceipt" action="GoodsReceipt.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="90%" >
	<input type='hidden' name='hidPO' value='<?=$selPoId;?>'>
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
									<td colspan="2" align='center'>
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>
												<td colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('GoodsReceipt.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateGoodsReceipt(document.frmGoodsReceipt);">			
												</td>
												<?} else{?>
												<td  colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('GoodsReceipt.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAdd"  id="cmdAdd1" class="button" value=" Add " onClick="return validateGoodsReceipt(document.frmGoodsReceipt);"> &nbsp;&nbsp;		
												</td>
												<?}?>
											</tr>
												<input type="hidden" name="hidGoodsReceiptId" value="<?=$editGoodsReceiptId;?>">
											<tr>
												<td class="fieldName" colspan='2' align='center' style="color:red;" id="ErrMessage">
												</td>
											</tr>
											<tr>
												<td colspan="2" nowrap height="5">
												</td>
											</tr>
											<tr>
  												<td colspan="2" nowrap>
													<table>
														<TR>
															<TD valign="top">
																<fieldset>
																	<table align='left'>
																		<tr>
																			<td class="fieldName" align='right' >*PO ID:&nbsp;</td>
																			<td class="listing-item">
																			 <? 
																			 if ($addMode==true) {		
																			 ?>
																			<!--<select name="selPoId" id="selPoId" onchange="this.form.submit();">-->
																			<select name="selPoId" id="selPoId" onchange="getPOId(this);">
																			<? } else {?>
																			<!--<select name="selPoId" id="selPoId" onchange="this.form.editId.value=<?=$editId?>;this.form.submit();" <?=$fieldDisabled?>>-->
																			<select name="selPoId" id="selPoId" onchange="this.form.editId.value=<?=$editId?>;getPOId(this);" <?=$fieldDisabled?>>
																				<? }?>
																				<option value="">-- Select --</option>
																				<?
																				foreach ($purchaseOrderRecords as $por) {
																					$purchaseOrderId	=	$por[0];
																					$pogeneratedId 		=	$por[1];
																					$PONumber		=	$por[2];
																					$displayPO		= "$pogeneratedId";
																					$selected	=	"";
																					if($selPoId==$purchaseOrderId) $selected="Selected";
																				?>
																				<option value="<?=$purchaseOrderId?>" <?=$selected?>><?=$displayPO?></option>
																				<? }?>
																			</select>
																		</td>
																	</tr>
																	<tr>
																		<td class="fieldName"  align='right' >*Department:&nbsp;</td>
																		<td class="listing-item">
																			<select name="selDepartment" id="selDepartment">
																				<option value="">-- Select --</option>
																				<?
																				foreach($departmentRecords as $dr=>$drName) 
																				{
																					$departmentId		=	$dr;
																					$departmentName	=	stripSlash($drName);
																					$selected="";
																					if($editDepartmentId==$departmentId || $p["selDepartment"] == $departmentId) 
																						echo $selected="Selected";
																				 ?>
																				 <option value="<?=$departmentId?>" <?=$selected?>><?=$departmentName?></option>
																				<? }?>
																			</select>
																		</td>
																		<td></td>
																	</tr>
																		<input type='hidden' name='ce' id='ce' value='<?=$p["ce"];?>' >
																		<input type='hidden' name='ge' id='ge' value='<?=$p["ge"];?>' >
																		<input type='hidden' name='se' id='se' value='<?=$p["se"];?>' >
																		<input type='hidden' name='rm' id='rm' value='<?=$p["rm"];?>' >
																	<tr>
																		<td class="fieldName"  align='right'>Supplier:&nbsp;</td>
																		<td class="listing-item"><?=$supplierName?><input type="hidden" value="<?=$SupplierId?>" name="supplierId" id="supplierId"></td>
																		<td></td>
																	</tr>
																	<tr>
																		<td class="fieldName"  align='right'>Company:&nbsp;</td>
																		<td class="listing-item"><?=$companyName?>
																		<input type="hidden" value="<?=$companyId?>" name="companyId" id="companyId"></td>
																		<td></td>
																	</tr>
																	<tr>
																		<td class="fieldName"  align='right'>Unit:&nbsp;</td>
																		<td class="listing-item"><?=$plantName?>
																		<input type="hidden" value="<?=$plantUnitId?>" name="plantId" id="plantId"></td>
																		<td></td>
																	</tr>
																</table>
															</fieldset>
														</TD>
														<TD valign="top">&nbsp;</TD>
														<TD valign="top">
															<fieldset>
																<table align='left'>
																	<tr>
																		<td class="fieldName"  align='right'>*Challan No:&nbsp; </td>
																		<td class="listing-item">
																			<input name="challanNo" type="text" id="challanNo" value="<?=selectAvailableVal($p["challanNo"],$challanNo);?>"  onChange="xajax_verifyChellanNumber(document.getElementById('challanNo').value,'<?=$challanNo?>','ErrMessage_CN', <?=$modeVal;?>);" autocomplete="off" >
																		</td>
																		<td class="fieldName" nowrap align='left' style="color:red;" id="ErrMessage_CN"></td>
																	</tr>
																	<tr>
																		<td class="fieldName"  align='right'>Bill No:&nbsp; </td>
																		<td class="listing-item">
																			<input name="billNo" type="text" id="billNo"  value="<?=selectAvailableVal($p["billNo"],$billNo);?>" autocomplete="off">
																		</td>
																		<td></td>
																	</tr>
																	<tr>
																		<td class="fieldName" nowrap="nowrap"  align='right'>*Gate Entry No:&nbsp;</td>
																		<td class="listing-item">
																			<input name="gateEntryNo" type="text" id="gateEntryNo"  value="<?=selectAvailableVal($p["gateEntryNo"],$gateEntry);?>" onChange="xajax_verifyGateEntryNumber(document.getElementById('gateEntryNo').value,'<?=$gateEntry?>','ErrMessage_GE',<?=$modeVal;?>);" autocomplete="off">
																		</td>
																		<td class="fieldName" nowrap align='left' style="color:red;" id="ErrMessage_GE"></td>
																	</tr>
																	<tr>
																		<td class="fieldName" nowrap="nowrap"  align='right' >*Store Entry No:&nbsp;</td>
																		<td class="listing-item">
																			<input name="storeEntry" type="text" id="storeEntry"  value="<?=selectAvailableVal($p["storeEntry"],$storeEntry);?>" onChange="xajax_verifyStoreEntryNumber(document.getElementById('storeEntry').value,'<?=$storeEntry?>','ErrMessage_SE',<?=$modeVal;?>);" autocomplete="off">
																		</td>
																		<td class="fieldName" nowrap align='left' style="color:red;" id="ErrMessage_SE"></td>
																	</tr>
																</table>
															</fieldset>
														</TD>
													</TR>
												</table>
											</td>
										</tr>
										<tr>
  											<td colspan="2" nowrap align='left'>
											<!--<table align='left'>
																							<tr>
																							  <td class="fieldName" align='right' >*PO ID:&nbsp;</td>
																							  <td class="listing-item">
												 <? 
												 if ($addMode==true) {		
												 ?>
												<select name="selPoId" id="selPoId" onchange="this.form.submit();">
												<? } else {?>
												<select name="selPoId" id="selPoId" onchange="this.form.editId.value=<?=$editId?>;this.form.submit();" <?=$fieldDisabled?>>
												<? }?>
												<option value="">-- Select --</option>
												<?
												foreach ($purchaseOrderRecords as $por) {
													$purchaseOrderId	=	$por[0];
													$pogeneratedId 		=	$por[1];
													$PONumber		=	$por[2];
													$displayPO		= "$pogeneratedId";
													$selected	=	"";
													if($selPoId==$purchaseOrderId) $selected="Selected";
												?>
												<option value="<?=$purchaseOrderId?>" <?=$selected?>><?=$displayPO?></option>
												<? }?></select></td></tr>
													<tr>
														   <td class="fieldName"  align='right' >*Department:&nbsp;</td>
														   <td class="listing-item">
													<select name="selDepartment" id="selDepartment">
													 <option value="">-- Select --</option>
													 <?
													  foreach($departmentRecords as $cr) {
														$departmentId		=	$cr[0];
														$departmentName	=	stripSlash($cr[1]);
														$selected="";
														if($editDepartmentId==$departmentId || $p["selDepartment"] == $departmentId) 
															echo $selected="Selected";
													  ?>
													  <option value="<?=$departmentId?>" <?=$selected?>><?=$departmentName?></option>
													  <? }?>
														   </select>
														   </td>
													  <td></td>
													 </tr>
												<input type='hidden' name='ce' id='ce' value='<?=$p["ce"];?>' >
												<input type='hidden' name='ge' id='ge' value='<?=$p["ge"];?>' >
												<input type='hidden' name='se' id='se' value='<?=$p["se"];?>' >
												<input type='hidden' name='rm' id='rm' value='<?=$p["rm"];?>' >
													  <tr>
																				<td class="fieldName"  align='right'>Supplier:&nbsp;</td>
																							  <td class="listing-item"><?=$supplierName?></td>
																	  <td></td>
																							</tr>
																							<tr>
																							  <td class="fieldName"  align='right'>*Challan No:&nbsp; </td>
																							  <td class="listing-item">
																<input name="challanNo" type="text" id="challanNo" value="<?=selectAvailableVal($p["challanNo"],$challanNo);?>"  onChange="xajax_verifyChellanNumber(document.getElementById('challanNo').value,'<?=$challanNo?>','ErrMessage_CN', <?=$modeVal;?>);" autocomplete="off" >
																</td>
																							 <td class="fieldName" nowrap align='left' style="color:red;" id="ErrMessage_CN"></td>
																							</tr>
																							<tr>
																							  <td class="fieldName"  align='right'>Bill No:&nbsp; </td>
																							  <td class="listing-item">
																		<input name="billNo" type="text" id="billNo"  value="<?=selectAvailableVal($p["billNo"],$billNo);?>" autocomplete="off">
																	</td>
																							  <td></td>
																							</tr>
																							<tr>
																							  <td class="fieldName" nowrap="nowrap"  align='right'>*Gate Entry No:&nbsp;</td>
																							  <td class="listing-item">
																		<input name="gateEntryNo" type="text" id="gateEntryNo"  value="<?=selectAvailableVal($p["gateEntryNo"],$gateEntry);?>" onChange="xajax_verifyGateEntryNumber(document.getElementById('gateEntryNo').value,'<?=$gateEntry?>','ErrMessage_GE',<?=$modeVal;?>);" autocomplete="off">
																	</td>
																							   <td class="fieldName" nowrap align='left' style="color:red;" id="ErrMessage_GE"></td>
																							</tr>
																							<tr>
																							  <td class="fieldName" nowrap="nowrap"  align='right' >*Store Entry No:&nbsp;</td>
																							  <td class="listing-item">
																		<input name="storeEntry" type="text" id="storeEntry"  value="<?=selectAvailableVal($p["storeEntry"],$storeEntry);?>" onChange="xajax_verifyStoreEntryNumber(document.getElementById('storeEntry').value,'<?=$storeEntry?>','ErrMessage_SE',<?=$modeVal;?>);" autocomplete="off">
																	</td>
												   <td class="fieldName" nowrap align='left' style="color:red;" id="ErrMessage_SE"></td>
														</tr>
													  </table>-->
													</td>
												</tr>
												<tr>
													<td colspan="3" nowrap style="padding-left:5px;padding-right:5px;">
													<? 
													if ( sizeof($purchaseRecs) > 0) {
													$j=0;
													?>
														<table width="300" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddItem">
															<tr bgcolor="#f2f2f2" align="center">
																<td class="listing-head"  style="padding-left:5px; padding-right:5px;">Item</td>
																<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Ordered <br>Qty</td>
																<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Received <br>Qty</td>
																<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;"> NotOverQty</td>
																<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;"> Qty</td>
																<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Extra <br>Qty</td>
																<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;" colspan="2">Confirm</td>
																<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Actual <br>Qty</td>
																<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Accepted <br>Qty</td>
																<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;"> Rejected <br>Qty</td>
																<td class="listing-head" style="padding-left:5px; padding-right:5px;">Remarks</td>
																<td class="listing-head" style="padding-left:5px; padding-right:5px;"></td>	
															</tr>
															<?php
															$totalPoQty= 0;
															foreach($purchaseRecs as $pr)	{
																
																$j++;
																$stockId	= $pr[2];
																$stockRec	= $stockObj->find($pr[2]);
																$stockName	= stripSlash($stockRec[2]);
																$quantity	= $pr[4];
																$notover=$pr[8];
																
																//$plantUnitId=$pr[9];
																list($id,$no,$plantName)=$plantandunitObj->find($plantUnitId);

																



																$rec 		= $goodsReceiptRecs[$j-1];
																
																if ($p["editSelectionChange"]=='1') {
																	$goodsReceiptEntryId = $rec[0];
																	$qtyReceived	= $rec[4];

																	
																
																	$qtyRejected	= $rec[5];
																	$remarks	= $rec[6];
																	$actQty 	= $rec[3];
																	$confirmationtype=$rec[8];
																	$notover=$rec[9];
																	/*$extraQty=$rec[10];
																	$extraBal=$rec[11];*/
																	$extraBal=$rec[10];
																	$extraQty=$rec[11];
																	$orginalQty=$qtyReceived+$qtyRejected;



																	# Get Selected Chk Point
																	$chkPointRec = $goodsreceiptObj->getSelCheckPointRecs($goodsReceiptEntryId);
																} else {
																	$goodsReceiptEntryId = "";
																	$qtyReceived	= 	"";
																	$qtyRejected	=	"";
																	$remarks		=	"";
																	$actQty			=	"";
																}
																
																$totalReceivedAty = $goodsreceiptObj->getReceivedQtyOfStock($stockId,$selPoId);
																$expQty = $quantity;
																$totalPoQty = $totalPoQty + $expQty;
																$subCategoryId ="";
																# Check Subcategory has Check Point
																$subCategoryId	= $goodsreceiptObj->checkPointExist($stockId);
																
																if ($subCategoryId)
																{
																	$getCheckPointRecs = $goodsreceiptObj->getCheckPointRecs($subCategoryId);
																} else $getCheckPointRecs = array();
											
													
																?>
																<tr bgcolor="#FFFFFF">
																	
																	<td class="fieldName" nowrap style="padding-left:5px; padding-right:5px;"><?=$stockName?>
																		<input type="hidden" value="<?=$stockId?>" name="stockId_<?=$j?>" id="stockId_<?=$j?>">
																		<input type="hidden" value="<?=$goodsReceiptEntryId?>" name="hidGrnEntryId_<?=$j?>" id="hidGrnEntryId_<?=$j?>">
																	</td>
																	<td class="fieldName" align='center' >
																		<input name="expQuantity_<?=$j?>" type="text" id="expQuantity_<?=$j?>" readonly size="4" style="text-align:center; border:none;" value="<?=$expQty?>"></td>	
																	<td class="fieldName" align='center' >
																		<input name="dd<?=$j?>" type="text" id="dd<?=$j?>" readonly size="4" style="text-align:center; border:none;" value="<?=$totalReceivedAty?>">
																	</td>
																	<td><input type="text" name="notover_<?=$j?>" value="<?=$notover;?>" id="notover_<?=$j?>" readonly size="4" style="text-align:center; border:none;" />
																	</td>
																	<td>
																		<!--<input name="quantity_<?=$j?>" type="text" id="quantity_<?=$j?>" size="4" style="text-align:center; border:none;" value="<?=selectAvailableVal($p["quantity_".$j],$actQty);?>" onkeyup="return calcReject(document.frmGoodsReceipt);" autocomplete="off"  readonly size="4" >-->

																		<input name="quantity_<?=$j?>" type="text" id="quantity_<?=$j?>" size="4" style="text-align:center; border:none;" value="<?=$extraBal;?>" onkeyup="return calcReject(document.frmGoodsReceipt);" autocomplete="off"  readonly size="4" >
																	</td>
																	<td class="fieldName" align='center'>
																		<input name="extraQty_<?=$j?>" id="extraQty_<?=$j?>" type="text" size="4" value="<?=$extraQty;?>"   readonly size="4" style="text-align:center; border:none;" />
																	</td>
																	<td width=50>Yes<input name="confirmextraQty_<?=$j?>" id="confirmextraQty1_<?=$j?>"  type="radio" <?php if ($confirmationtype=='Y'){?> checked="true" <?php }?> value="Y" size="4" onClick="confirmQty(<?=$j?>);" /></td><td>No<input name="confirmextraQty_<?=$j?>" id="confirmextraQty2_<?=$j?>" type="radio" <?php if ($confirmationtype=='N'){?> checked="true" <?php }?> value="N" size="4"   onClick="rejectQty(<?=$j?>);"/></td>
																	<td>  <input type="text" name="orginalqty_<?=$j?>" id="orginalqty_<?=$j?>" value="<?=$orginalQty;?>" onBlur="CalculateExtraqty(<?=$j?>,frmGoodsReceipt)"; onKeyUp="CalculateExtraqty(<?=$j?>,frmGoodsReceipt)"; /> </td>
																	<td style="padding-left:5px; padding-right:5px;" align="center">
																		<!--<input name="qtyReceived_<?=$j?>" type="text" id="qtyReceived_<?=$j?>" size="4" style="text-align:right" value="<?=selectAvailableVal($p["qtyReceived_".$j],$qtyReceived);?>" onkeyup="return calcReject(document.frmGoodsReceipt);" autocomplete="off">-->
																		<input name="qtyReceived_<?=$j?>" type="text" id="qtyReceived_<?=$j?>" size="4" value="<?=$qtyReceived?>"  onkeyup="return calcReject(document.frmGoodsReceipt);" autocomplete="off" style="text-align:center; border:none;" readonly >
																		<!--	While Updating -->
																		<input name="qtyAlreadyReceived_<?=$j?>" type="hidden" id="qtyAlreadyReceived_<?=$j?>" size="4" value="<?=$qtyReceived?>" style="text-align:center; border:none;">
																	</td>
																	<td  align="center">
																		<input name="qtyRejected_<?=$j?>" type="text" id="qtyRejected_<?=$j?>" size="4" readonly value="<?=selectAvailableVal($p["qtyRejected_".$j],$qtyRejected);?>" style="text-align:center; border:none;" /></td>
																	<td style="padding-left:5px; padding-right:5px;" align="center">
																		<textarea name="remarks_<?=$j?>" id="remarks_<?=$j?>"><?=$remarks?></textarea>
																	</td>
																	<td style="padding-left:5px; padding-right:5px;" align="center">
																		<?php
																			$k = 0;	
																			if (sizeof($getCheckPointRecs)>0) {
																				
																		?>	
																		<table cellspacing="1" bgcolor="#999999" cellpadding="2">
																			<tr bgcolor="#f2f2f2" align="center">
																				<td class="listing-head"  style="padding-left:5px; padding-right:5px;font-size:11px;line-height:normal;">Check Point</td>
																				<td class="listing-head"  style="padding-left:5px; padding-right:5px;font-size:11px;line-height:normal;"></td>
																				<td class="listing-head"  style="padding-left:5px; padding-right:5px;font-size:11px;line-height:normal;">Remark</td>
																			</tr>
																			<?php
																				foreach ($getCheckPointRecs as $cpr) {
																					$k++;	
																					$checkPointId 	= $cpr[1];
																					$checkPointName = $cpr[2];
																					
																					if ($p["editSelectionChange"]=='1') {
																						$cpRec =  $chkPointRec[$k-1];
																						$grCPEntryId = $cpRec[0];
																						$chkAnsChecked = ($cpRec[2]=='Y')?"Checked":"";
																						$chkPointRemarks = $cpRec[3];
																					} else {
																						$grCPEntryId = "";
																						$chkAnsChecked = "";
																						$chkPointRemarks = "";
																					}
																			?>
																			<TR bgcolor="White">
																				<TD class="fieldName" nowrap="true">
																					<?=$checkPointName?>
																					<input type="hidden" name="chkPointId_<?=$j?>_<?=$k?>" value="<?=$checkPointId?>">
																					<input type="hidden" name="grCPEntryId_<?=$j?>_<?=$k?>" value="<?=$grCPEntryId?>">
																				</TD>
																				<td >
																					<input type="checkbox" class="chkBox" name="chkPointAnswer_<?=$j?>_<?=$k?>" id="chkPointAnswer_<?=$j?>_<?=$k?>" value="Y" <?=$chkAnsChecked?> >
																				</td>
																				<td>
																					<textarea name="chkPointRemarks_<?=$j?>_<?=$k?>" id="chkPointRemarks_<?=$j?>_<?=$k?>"><?=$chkPointRemarks?></textarea>
																				</td>
																			</TR>
																			<?php
																				}
																			?>
																		</table>
																	<?php
																		}
																	?>
																	<input type="hidden" name="chkPointRowCount_<?=$j?>" id="chkPointRowCount_<?=$j?>" value="<?=$k?>">
																</td>
															</tr>
															<?
															 }												
															?>
														</table>
														<input type='hidden' name='totalPOQty' value='<?=$totalPoQty;?>'>
													<? }?>
												</td>
    										</tr>
												<input type='hidden' name='hidItemCount' id='hidItemCount' value="<?=$j;?>">
											<tr>
												<td colspan="2" nowrap class="fieldName">
													<table width="200">
													   <tr>
															<td class="fieldName" nowrap="nowrap">Rejected Material Gate pass No:</td>
															<td class="listing-item"><input name="rejectedEntry" type="text" id="rejectedEntry" value="<?=selectAvailableVal($p["rejectedEntry"],$rejectedEntry);?>" onChange="xajax_verifyRMGatePassNumber(document.getElementById('rejectedEntry').value,'<?=$rejectedEntry?>','ErrMessage_RM',<?=$modeVal;?>);" ></td>
															 <input type='hidden' name="hidRejectedEntry" id="hidRejectedEntry" value="<?=selectAvailableVal($p["hidRejectedEntry"],$rejectedEntry);?>" >
															<td class="fieldName" nowrap align='left' style="color:red;" id="ErrMessage_RM"></td>
															<td class="fieldName" nowrap="nowrap">Remarks</td>
															<TD>
																<textarea name="grnRemarks" id="grnRemarks"><?=$grnRemarks?></textarea>	
															</TD>
														</tr>
														<!--<tr>
															<td class="fieldName" nowrap="nowrap">Remarks</td>
															<TD>
																<textarea name="grnRemarks" id="grnRemarks"><?=$grnRemarks?></textarea>	
															</TD>
														</tr>-->
													</table>
												</td>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>
												<td colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('GoodsReceipt.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" id="cmdSaveChange2" class="button" value=" Save Changes " onClick="return validateGoodsReceipt(document.frmGoodsReceipt);">												</td>
												<?} else{?>
												<td  colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('GoodsReceipt.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAdd"  id="cmdAdd2"  class="button" value=" Add " onClick="return validateGoodsReceipt(document.frmGoodsReceipt);">&nbsp;&nbsp;												</td>
													<input type="hidden" name="cmdAddNew" value="1">
												<?}?>
												<input type="hidden" name="stockType" value="<?=$stockType?>" />
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<SCRIPT LANGUAGE="JavaScript">
												<!--
													enableButtons("<?=$modeVal;?>");
												//-->
												</SCRIPT>
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
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap>&nbsp;Goods Receipt Note   </td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
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
														<input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>"></td>
														<td class="listing-item">&nbsp;</td>
														<td class="listing-item"> Till:</td>
														<td> 
														  <? 
														   if($dateTill=="") $dateTill=date("d/m/Y");
														  ?>
														  <input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>"></td>
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
												<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$goodsReceiptSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintGoodsReceipt.php?fd=<?=$fromDate;?>&td=<?=$tillDate;?>',700,600);"><?}?>
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
									if (sizeof($goodsReceiptRecords)>0) {
										$i	=	0;
									?>
									<? if($maxpage>1){?>
									<tr  bgcolor="#f2f2f2" align="center">
										<td colspan="6" bgcolor="#FFFFFF" style="padding-right:10px;">
											<div align="right">
											<?php 				 			  
											$nav  = '';
											for ($page=1; $page<=$maxpage; $page++) {
												if ($page==$pageNo) {
														$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
												} else {
														$nav.= " <a href=\"GoodsReceipt.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
													//echo $nav;
												}
											}
											if ($pageNo > 1) {
												$page  = $pageNo - 1;
												$prev  = " <a href=\"GoodsReceipt.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
											} else {
												$prev  = '&nbsp;'; // we're on page one, don't print previous link
												$first = '&nbsp;'; // nor the first page link
											}

											if ($pageNo < $maxpage) {
												$page = $pageNo + 1;
												$next = " <a href=\"GoodsReceipt.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
											} else {
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
									<tr bgcolor="#f2f2f2" align="center">
										<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></td>
										<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">GRN No </td>
										<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">PO ID</td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;">Date</td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;"></td>
										<td class="listing-head" style="padding-left:10px; padding-right:10px;"></td>
										<? if($edit==true){?>
										<td class="listing-head"></td>
										<? }?>
									</tr>
									<?
									foreach ($goodsReceiptRecords as $grr) {
										$i++;
										$goodsReceiptId		=	$grr[0];
										$poId			= 	$grr[1];
										$purchaseOrderRec	=	$purchaseOrderInventoryObj->find($poId);	
										$pOGenerateId		=	$purchaseOrderRec[1];
										$SupplierId		=	$purchaseOrderRec[3];
										$supplierRec		=	$supplierMasterObj->find($SupplierId);
										$supplierName		=	stripSlash($supplierRec[2]);
										$storeEntry		=	$grr[6];	
										$Date			=	explode("-",$grr[8]);
										$createdDate		=	$Date[2]."/".$Date[1]."/".$Date[0];	

										$chkGRNChkPointExist = $goodsreceiptObj->checkGRNCheckPointExist($goodsReceiptId)		
									?>
									<tr bgcolor="White">
										<td width="20">
											<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$goodsReceiptId;?>" class="chkBox">
											<input type="hidden" name="poId_<?=$i?>" value="<?=$poId?>">
										</td>
										<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$storeEntry;?></td>
										<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$pOGenerateId;?></td>
										<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierName;?></td>
										<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$createdDate?></td>
										<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap>
											<a href="javascript:printWindow('ViewGRNDetails.php?grnId=<?=$goodsReceiptId?>',700,600)" class="link1" title="Click here to view details">View Details</a>
										</td>
										<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap>
											<?
												if ($chkGRNChkPointExist) {
											?>
											<a href="javascript:printWindow('ViewGRNCheckPoint.php?grnId=<?=$goodsReceiptId?>',700,600)" class="link1" title="Click here to Print Check Point Remarks">Print Check Point</a>
											<?
												}
											?>
										</td>
										<? if($edit==true){?>
											<td class="listing-item" width="60" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$goodsReceiptId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='GoodsReceipt.php';"  ></td>
										<? }?>
									</tr>
									<?}?>
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
														$nav.= " <a href=\"GoodsReceipt.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";				
												}
											}
											if ($pageNo > 1) {
												$page  = $pageNo - 1;
												$prev  = " <a href=\"GoodsReceipt.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
											} else {
												$prev  = '&nbsp;'; // we're on page one, don't print previous link
												$first = '&nbsp;'; // nor the first page link
											}

											if ($pageNo < $maxpage) {
												$page = $pageNo + 1;
												$next = " <a href=\"GoodsReceipt.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
											<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$goodsReceiptSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintGoodsReceipt.php?fd=<?=$fromDate;?>&td=<?=$tillDate;?>',700,600);"><?}?>
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
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>