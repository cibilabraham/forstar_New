<?
	require("include/include.php");
	require_once('lib/stockissuance_ajax.php');
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$selStockId		=	"";
	$userId		=	$sessObj->getValue("userId");
	$genReqNumber	= "";

	$selection = "?pageNo=".$p["pageNo"]."&selectFrom=".$p["selectFrom"]."&selectTill=".$p["selectTill"];

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

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
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;
	if($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/
	
	$requestNo		= $p["requestNo"];
	$selDepartment		= $p["selDepartment"];

	# For resetting the values from edit mode to add mode
	$hidEditId = "";
	if ($p["editId"]!="") {
		$hidEditId = $p["editId"];
	} else {
		$hidEditId = $p["hidEditId"];
	}

	if ($p["cmdAddNew"]!="" && $p["hidEditId"]!="") {
		$requestNo 	= "";
		$selDepartment  = "";
		//$hidEditId 	= "";
	}
	// end

	# Add Stock Issuance Start 
	if ($p["cmdAddNew"]!="") {
		$addMode	=	true;
	}
	
	if ($p["cmdCancel"]!="") {
		$addMode	=	false;	
	}	
	
	# Checking auto generate Exist
	$genReqNumber = $idManagerObj->check("SI");

/*
	#Add
	if ($p["cmdAdd"]!="" ) {
		$itemCount		=	$p["hidTableRowCount"];		
		//$requestNo		=	$p["requestNo"];
		if( $genReqNumber==1 ) 
		{
			list($isMaxId,$requestNo)	= $idManagerObj->generateNumberByType("SI"); 
			$err  = ( $isMaxId=="Y") ? "The generated request number is greater than the ending number of stock issuance." : "";
		} else $requestNo		=	$p["requestNo"];
				
		if ($requestNo!="") $chkUnique = $stockissuanceObj->checkUnique($requestNo, "");

		$selDepartment		=	$p["selDepartment"];
		//$hidStockItemStatus	= 	$p["hidStockItemStatus"];
		
		if ($requestNo!="" && $selDepartment!="" && !$chkUnique) {	
			$stockIssuanceRecIns	=	$stockissuanceObj->addStockIssuance($requestNo, $selDepartment, $userId);
									
				$lastId = $databaseConnect->getLastInsertedId();
				
				for ($i=0; $i<$itemCount; $i++) {
					$status = $p["status_".$i];
			    	  if ($status!='N') {
					$stockId		=	$p["selStock_".$i];
					$exisitingQty		=	trim($p["exisitingQty_".$i]);
					$quantity		=	trim($p["quantity_".$i]);
					$balanceQty		=	$p["balanceQty_".$i];

					#Update the Current Stock Qty
					$totalQty = $stockissuanceObj->getTotalStockQty($stockId);
					$currentStock = $totalQty - $quantity;
						
					if ($lastId!="" && $stockId!="" && $exisitingQty!="" && $quantity!="") {
						$stockIssuanceRecIns	=	$stockissuanceObj->addIssuanceEntries($lastId, $stockId, $exisitingQty, $quantity, $balanceQty, $currentStock);
						#Update the Stock
						$updateStockQty = $stockissuanceObj->updateBalanceStockQty($stockId, $balanceQty);
					}
				}
			  }
			} else if ($chkUnique) $err = " Failed to add Stock Issuance. Please make sure the request number you have entered is not duplicate. ";

			if ($stockIssuanceRecIns) {
				if( $err!="" ) printJSAlert($err);
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddStockIssuance);
				$sessObj->createSession("nextPage",$url_afterAddStockIssuance.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddStockIssuance;
			}
			$stockIssuanceRecIns		=	false;
			$hidEditId 	=  "";
	}
	*/


/*
	# Edit Stock Issuance
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$stockIssuanceRec	=	$stockissuanceObj->find($editId);		
		$editStockIssuanceId	=	$stockIssuanceRec[0];		
		$requestNo		=	$stockIssuanceRec[1];		
		$editDepartmentId	=	$stockIssuanceRec[2];
		// Get Issuance Records
		$issuanceRecs = $stockissuanceObj->fetchAllStockItem($editStockIssuanceId);
	}

	*/

/*
	#Update 
	if ($p["cmdSaveChange"]!="") {		
		$stockIssuanceId	=	$p["hidStockIssuanceId"];
		$itemCount		=	$p["hidTableRowCount"];		
		$requestNo		=	$p["requestNo"];
		$selDepartment		=	$p["selDepartment"];
		
		$hidRequestNo = $p["hidReqNumber"];
				
		if ($genReqNumber==0 && ($requestNo!=$hidRequestNo ) ) 
		{
			$chkUnique = $stockissuanceObj->checkUnique($requestNo, $hidRequestNo);
		}

		if ($stockIssuanceId!="" && $requestNo!="" && $selDepartment!="" && !$chkUnique) {
			$stockIssuanceRecUptd	=	$stockissuanceObj->updateStockIssuance($stockIssuanceId, $requestNo, $selDepartment);
		
			#Delete First all records from Stock Issuance entry table	
			$deleteStockIssuanceItemRecs	=	$stockissuanceObj->deleteIssuanceItemRecs($stockIssuanceId);
			
			for ($i=0; $i<$itemCount; $i++) {
			   $status = $p["status_".$i];
			   if ($status!='N') {
				$stockId	=	$p["selStock_".$i];
				$exisitingQty	=	trim($p["exisitingQty_".$i]);
				$quantity	=	trim($p["quantity_".$i]);
				$balanceQty	=	$p["balanceQty_".$i];
				$quantityAlreadyIssued	=	$p["quantityAlreadyIssued_".$i];

				#Update the Current Stock Qty
					$totalQty = $stockissuanceObj->getTotalStockQty($stockId);
					$stockQty = $quantityAlreadyIssued-$quantity;
					$currentStock = $totalQty + $stockQty;
						
				if ( $stockIssuanceId!="" && $stockId!="" && $exisitingQty!="" && $quantity!="") {
					$stockIssuanceRecUptd=	$stockissuanceObj->addIssuanceEntries($stockIssuanceId, $stockId, $exisitingQty, $quantity, $balanceQty, $currentStock);
					if ($quantity!=$quantityAlreadyIssued) {
					#Update the Stock
						$stockQty = $quantityAlreadyIssued-$quantity;
						$updateStockQty = $goodsreceiptObj->updateStockQty($stockId, $stockQty);
					}
				}
                           }
			}		
		}
	
		if ($stockIssuanceRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succStockIssuanceUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateStockIssuance.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failStockIssuanceUpdate;
		}
		$stockIssuanceRecUptd	=	false;
		$hidEditId 	= "";
	}
	*/
	
/*	# Delete Stock Issuance
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$stockIssuanceId	=	$p["delId_".$i];

			if ($stockIssuanceId!="" && $isAdmin!="") {
				$deleteStockIssuanceItemRecs	=	$stockissuanceObj->deleteIssuanceItemRecs($stockIssuanceId);
				$stockIssuanceRecDel =	$stockissuanceObj->deleteStockIssuance($stockIssuanceId);	
			}
		}
		if ($stockIssuanceRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelStockIssuance);
			$sessObj->createSession("nextPage",$url_afterDelStockIssuance.$selection);
		} else {
			$errDel	=	$msg_failDelStockIssuance;
		}
		$stockIssuanceRecDel	=	false;
		$hidEditId 	= "";
	}
	*/

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$stockRequisitionId	=	$p["confirmId"];
			if ($stockRequisitionId!="") {
				$stockRequisitionRecConfirm = $stockissuanceObj->updateStockIssuanceConfirm($stockRequisitionId);
			}
		}
		if ($stockRequisitionRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmStockIssuance);
			$sessObj->createSession("nextPage",$url_afterDelStockIssuance.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$stockRequisitionId = $p["confirmId"];
			if ($stockRequisitionId!="") {
				#Check any entries exist
				$stockRequisitionRecConfirm = $stockissuanceObj->updateStockIssuanceReConfirm($stockRequisitionId);
			}
		}
		if ($stockRequisitionRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmStockIssuance);
			$sessObj->createSession("nextPage",$url_afterDelStockIssuance.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
	}



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
	
	#List all Stock Issuance
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);
		
		###filter by department,company,unit
		$selDepartment=$p["selDepartment"];
		$selCompany=$p["selCompany"];
		$selUnit=$p["selUnit"];
		$selItem=$p["selItem"];
		$departmentsRec=$stockissuanceObj->getDepartmentsInRequisition($fromDate, $tillDate);
		$companyRec=$stockissuanceObj->getCompanyInRequisition($fromDate, $tillDate,$selDepartment);
		$unitRec=$stockissuanceObj->getUnitInRequisition($fromDate, $tillDate,$selDepartment,$selCompany);
		$itemRec=$stockissuanceObj->getItemInRequisition($fromDate, $tillDate,$selDepartment,$selCompany,$selUnit);

		$stockIssuanceRecords	= $stockissuanceObj->fetchAllPagingRecords($fromDate, $tillDate,$selDepartment,$selCompany,$selUnit,$selItem,$offset, $limit);
		$stockIssuanceSize	= sizeof($stockIssuanceRecords);
		$fetchAllStockIssuanceRecs = $stockissuanceObj->fetchAllDateRangeRecords($fromDate, $tillDate,$selDepartment,$selCompany,$selUnit,$selItem);
	}
	//$stockissuanceObj->fetchAllRecords()
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($fetchAllStockIssuanceRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# List all Stocks
	//$stockRecords		= $stockObj->fetchAllActiveRecords();
	//$stockRecords		= $stockObj->fetchAllActiveRecordsConfirm();
	
	# List all Supplier
	//$supplierRecords	= $supplierMasterObj->fetchAllRecords();
	
	# List all Departments
	//$departmentRecords	= $departmentObj->fetchAllRecordsActivedept();
		
	if ($editMode) $heading	=	$label_editStockIssuance;
	else $heading	=	$label_addStockIssuance;
		
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/stockissuance.js"; // For Printing JS in Head section

	if ($addMode) $mode = 1;
	else if ($editMode) $mode = 2;
	else $mode = "";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<!-- 
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="/resources/demos/style.css">-->
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="libjs/jquery/jquery-1.10.2.js"></script>
<script src="libjs/jquery/jquery-ui.js"></script>



<form name="frmStockIssuance" id="frmStockIssuance" action="StockIssuance.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%" >
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
		</tr>
		<?
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="70%"  bgcolor="#D3D3D3">
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
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockIssuance.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateStockIssuance(document.frmStockIssuance);">												</td>
												<?} else{?>
												<td  colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockIssuance.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateStockIssuance(document.frmStockIssuance);"> &nbsp;&nbsp;												</td>
												<?}?>
											</tr>
											<input type="hidden" name="hidStockIssuanceId" value="<?=$editStockIssuanceId;?>">
											<tr>
												<td class="fieldName" nowrap >&nbsp;</td>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<TD nowrap><span class="fieldName" style="color:red; line-height:normal" id="requestNumExistTxt"></span></TD>
											</tr>
											<!--
											<tr>
												<td colspan="2" nowrap class="fieldName" >
													<table width="200">
													<? 
														if( $genReqNumber==1 ) {
															if( $editMode ) {
													?>
														<tr>
															<td class="fieldName" nowrap>*Request No:</td>
															<td class="listing-item"><input name="requestNo" tabindex=1  Style="border:none;" readonly  type="text" id="requestNo" size="10" value="<?=$requestNo?>"></td>
														</tr>
													<?
															} else {
													?>
														<input name="requestNo" Style="border:none;" readonly  type="hidden" id="requestNo" value="Y">
													<?
															}							
														} else  {
													?>
														<tr>
															<td class="fieldName" nowrap>*Request No:</td>
															<td class="listing-item"><input name="requestNo" type="text" id="requestNo" size="10" value="<?=$requestNo?>" tabindex="1" onchange="xajax_checkRequestNumberExist(document.getElementById('requestNo').value, '<?=$requestNo?>', <?=$mode?>);"></td>
														
														</tr>
													<?
														}
													?>
													<input type="hidden" name="hidReqNumber" value="<?=$requestNo?>">
														<tr>
                                							<td class="fieldName" align='right'>*Department:&nbsp;</td>
															<td class="listing-item">
																<select name="selDepartment" id="selDepartment">
																	<option value="">--select--</option>
																	<?
																	foreach($departmentRecords as $cr)
																	{
																		$departmentId		=	$cr[0];
																		$departmentName	=	stripSlash($cr[1]);
																		$selected="";
																		if($selDepartment==$departmentId || $editDepartmentId==$departmentId) echo $selected="Selected";
																	  ?>
																	<option value="<?=$departmentId?>" <?=$selected?>><?=$departmentName?></option>
																	<? }?>
																</select>
															</td>
														 </tr>
													</table>
												</td>
											</tr>
											-->
											<tr>
												<td colspan="2">&nbsp;</td>
											</tr>					
											<tr>
												<TD>
													<table width="300" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddStkIssuanceItem">
														<tr bgcolor="#f2f2f2" align="center">
															<td class="listing-head">Item</td>
															<td class="listing-head" nowrap>Exisiting Qty </td>
															<td class="listing-head">Quantity</td>
															<td class="listing-head">Balance Qty </td>
															<td></td>
                										</tr>
													</table>
												</TD>
											</tr>
												<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=$rowSize?>">
											<tr><TD height="10"></TD></tr>
											<tr><TD nowrap style="padding-left:5px; padding-right:5px;">
												<a href="###" id='addRow' onclick="javascript:addNewStockIssuanceItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
												</TD>
											</tr>
											<tr>
												<td colspan="2" nowrap class="fieldName" >&nbsp;</td>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
											<? if($editMode){?>
												<td colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockIssuance.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" id="cmdSaveChange2" class="button" value=" Save Changes " onClick="return validateStockIssuance(document.frmStockIssuance);">
												</td>
											<?} else{?>
												<td  colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockIssuance.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAdd" id="cmdAdd2" class="button" value=" Add " onClick="return validateStockIssuance(document.frmStockIssuance);">&nbsp;&nbsp;												</td>
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
		}	# Listing Category Starts
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
									<td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;Stock Issuance  </td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap" >
									&nbsp;
									</td>
								</tr>
								<tr>
									<td colspan="3" height="10"></td>
								</tr>
								<tr align="center">
									<td  align="center" nowrap="nowrap" colspan="3"  >
										<?php
										$entryHead = "Data Search";
										$rbTopWidth = "95%";
										require("template/rbTop.php");
										?>
										<table cellpadding="0" cellspacing="0" >
											<tr>
												<td nowrap="nowrap">
													<table cellpadding="0" cellspacing="0" >
														<tr>
															<td class="fieldName1"> From:</td>
															<td nowrap="nowrap"> 
																<? 
																if ($dateFrom=="") $dateFrom=date("d/m/Y");
																?>
																<input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>">
															</td>
															<td class="listing-item">&nbsp;</td>
															<td class="fieldName1"> Department:</td>
															<td nowrap="nowrap"> 
																<select id="selDepartment" name="selDepartment" onchange="functionLoad(this);"/>
																	<option value="">--select--</option>
																	<?php
																	if(sizeof($departmentsRec) > 0)
																	{
																		foreach($departmentsRec as $dr)
																		{
																			$departmentsId=$dr[0];
																			$departmentName=$dr[1];
																			$sel = '';
																			if($selDepartment == $departmentsId) $sel = 'selected';
																										
																			echo '<option '.$sel.' value="'.$departmentsId.'">'.$departmentName.'</option>';
																		}
																	}
																	?>
																</select>		
															</td>
															<td class="listing-item">&nbsp;</td>
															<td class="fieldName1">Company:</td>
															<td>
																<select id="selCompany" name="selCompany" onchange="functionLoad(this);"/>
																	<option value="">--select--</option>
																	<?php
																	if(sizeof($companyRec) > 0)
																	{
																		foreach($companyRec as $cr)
																		{
																			$companyId=$cr[0];
																			$companyName=$cr[1];
																			$sel = '';
																			if($selCompany == $companyId) $sel = 'selected';
																										
																			echo '<option '.$sel.' value="'.$companyId.'">'.$companyName.'</option>';
																		}
																	}
																	?>
																</select>		
															</td>
														</tr>
														<!--<tr>
															<td class="listing-item" colspan='2' height='5'>&nbsp;</td>
														</tr>-->
														<tr>
															<td class="fieldName1"> Till:</td>
															<td>
															<? 
																if($dateTill=="") $dateTill=date("d/m/Y");
															?>
																<input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>">
															</td>
															<td class="listing-item">&nbsp;</td>
															<td class="fieldName1"> Unit</td>
															<td nowrap="nowrap">
																<select id="selUnit" name="selUnit" onchange="functionLoad(this);">
																	<option value="">--select--</option>
																	<?php
																	if(sizeof($unitRec) > 0)
																	{
																		foreach($unitRec as $ur)
																		{
																			$unitId=$ur[0];
																			$unitName=$ur[1];
																			$sel = '';
																			if($selUnit == $unitId) $sel = 'selected';
																										
																			echo '<option '.$sel.' value="'.$unitId.'">'.$unitName.'</option>';
																		}
																	}
																	?>			
																</select>	
															</td>
															<td class="listing-item">&nbsp;</td>
															<td class="fieldName1"> Item</td>
															<td nowrap="nowrap">
																<select id="selItem" name="selItem" onchange="functionLoad(this);">
																	<option value="">--select--</option>
																	<?php
																	if(sizeof($itemRec) > 0)
																	{
																		foreach($itemRec as $ir)
																		{
																			$itemId=$ir[0];
																			$itemName=$ir[1];
																			$sel = '';
																			if($selItem == $itemId) $sel = 'selected';
																										
																			echo '<option '.$sel.' value="'.$itemId.'">'.$itemName.'</option>';
																		}
																	}
																	?>			
																</select>	
															</td>
														</tr>
													</table>
												</td>
												<td width="3%">&nbsp;</td>
												<td><input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search"></td>
											</tr>
										</table>
										<?php
											require("template/rbBottom.php");
										?>
									</td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><!--<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stockIssuanceSize;?>);"><? }?>&nbsp;
												<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>-->
												&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStockIssuance.php',700,600);"><? }?></td>
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
											if( sizeof($stockIssuanceRecords) > 0 )
											{
													$i	=	0;
											?>
											<? if($maxpage>1){?>
											<tr bgcolor="#FFFFFF">
												<td colspan="6" align="right" style="padding-right:10px;">
													<div align="right">
													<?php
													 $nav  = '';
													for ($page=1; $page<=$maxpage; $page++) {
														if ($page==$pageNo) {
																$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
														} else {
																$nav.= " <a href=\"StockIssuance.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
															//echo $nav;
														}
													}
													if ($pageNo > 1) {
														$page  = $pageNo - 1;
														$prev  = " <a href=\"StockIssuance.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
													} else {
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage) {
														$page = $pageNo + 1;
														$next = " <a href=\"StockIssuance.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
											<tr  bgcolor="#f2f2f2" >
												<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox"></td>
												<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Date</td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;">Department</td>
												<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Item</td>
												<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Company</td>
												<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Unit</td>
												<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Quantity</td>
												<? if($selDepartment!="" && $selCompany!="" && $selUnit!="" && $selItem!="") { ?>
												<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Stock Available</td>
												<? } ?>
												<? if($confirm==true){?>	
												<td class="listing-item">&nbsp;</td><? }?>
											</tr>
											<? $totalQty=0;
											foreach ($stockIssuanceRecords as $sir) {
												$i++;
												$stockRequisitionId	=	$sir[0];
												$department		=	$sir[2];
												$item		=	$sir[4];
												$companyId		=	$sir[5];
												$company		=	$sir[6];
												$unitId		=	$sir[7];
												$unit		=	$sir[8];
												$qty		=	$sir[10];
												$createdDate		= dateFormat($sir[11]);
												$itemId		=	$sir[3];
												if($selDepartment!="" && $selCompany!="" && $selUnit!="" && $selItem!="") 
												{
													$totalQty+=$qty;
												}

												$exist=$stockissuanceObj->checkExistInIssuance($stockRequisitionId);
												if($exist)
												{
												$active=$stockissuanceObj->getIssuanceStatus($stockRequisitionId);
												}
												

											?>
											<tr  bgcolor="WHITE">
												<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$stockRequisitionId;?>" class="chkBox"></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$createdDate;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$department;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$item;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$company;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$unit;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$qty;?></td>
												<? if($selDepartment!="" && $selCompany!="" && $selUnit!="" && $selItem!="") { ?>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
													<? if(!$exist){ ?>
													<input type="button" value="Stock Available " name="stockAvail" onclick="getSupplierDetail(<?=$itemId?>,<?=$qty?>,'<?=$stockRequisitionId?>','<?=$i;?>','<?=$companyId?>','<?=$unitId?>');" />
													<input type="hidden" name="supplierStockAllot_<?=$i;?>" id="supplierStockAllot_<?=$i;?>"/>
													<? } 
													else
													{
													?>
													<input type="button" value="ViewDetails " name="ViewDetails" onclick="getSupplierDetailShow(<?=$itemId?>,<?=$qty?>,'<?=$stockRequisitionId?>','<?=$i;?>');" />
													<? 
													} 
													?>
												</td> 
												<? } ?>

												<? if ($confirm==true){?>
												<td <?php if ($active==1) {?> class="listing-item" <?php }else {?> <?php }?> width="45" align="center" >
													<?php 
													if ($confirm==true){	
													if ($active==0 && $exist){ ?>
													<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$stockRequisitionId;?>,'confirmId');" >
													<?php } else if ($active==1 && $exist){ if ($existingrecords==0) { ?>
													<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$stockRequisitionId;?>,'confirmId');" >
													<?php } } }?>
												</td>
												<? }?>
											</tr>
											<?
												}
											?>
											
											<input type="hidden" name="totalQty"	id="totalQty" value="<?=$totalQty?>" >
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="confirmId" value="">
											<input type="hidden" name="editId" value="">
											<input type="hidden" name="editSelectionChange" value="0">
											<? if($maxpage>1){?>
											<tr bgcolor="#FFFFFF">
												<td colspan="6" align="right" style="padding-right:10px;">
													<div align="right">
													<?php
													 $nav  = '';
													for ($page=1; $page<=$maxpage; $page++) {
														if ($page==$pageNo) {
																$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
														} else {
																$nav.= " <a href=\"StockIssuance.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
															//echo $nav;
														}
													}
													if ($pageNo > 1) {
														$page  = $pageNo - 1;
														$prev  = " <a href=\"StockIssuance.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
													} else {
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage) {
														$page = $pageNo + 1;
														$next = " <a href=\"StockIssuance.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><!--<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stockIssuanceSize;?>);"><? }?>&nbsp;
												<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>-->
												&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStockIssuance.php',700,600);"><? }?></td>
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
		<input type="hidden" name="hidStockItemStatus" id="hidStockItemStatus">
		<input type="hidden" name="hidEditId" value="<?=$hidEditId?>">
		<tr>
			<td height="10"></td>
		</tr>
	</table>

	<div id="dialog" title="Supplier Stock Details" style="display:none">
	<!--<p>This is the default dialog which is useful for displaying information. The dialog window can be moved, resized and closed with the 'x' icon.</p>-->
	</div>

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