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
	
	$fromPlant=$p["fromPlant"];
	# Checking auto generate Exist
	$genReqNumber = $idManagerObj->check("SI");
	$plantUnitRecords=$plantandunitObj->fetchAllRecordsPlantsActive();
	$plantunit=$p["fromPlant"];
	//if ($fromPlant) $getPlantUnitRecs = $stockObj->filterRecords($fromPlant);
	//if ($fromPlant) $getPlantUnitRecs = $stockObj->filterRecords($fromPlant);
	$getPlantUnitRecs = $stockObj->filterRecords($fromPlant);

	
	#Add
	if ($p["cmdAdd"]!="" ) 
		{

		$fromCompany=$p["fromCompany"];
		$toCompany=$p["toCompany"];
		$unitFrom=$p["fromPlant"];
		$unitTo=$p["toPlant"];
		$stockIdFrom=$p["item"];
		$supplier=$p["supplier"];
		$qty=$p["quantity"];
		$date=Date("Y-m-d");
		$companyUnitId=$p["companyUnitId"];
		$stockTransferInsertion=$stockObj->addStockTransfer($fromCompany,$unitFrom,$toCompany,$unitTo,$stockIdFrom,$supplier,$qty,$date);
		$companyUnitArray=explode(",",$companyUnitId);
		$compUntSz=sizeof($companyUnitArray);
		for($i=0; $i<$compUntSz; $i++)
		{
			$compUntID=$companyUnitArray[$i];
			$stockQtyArr=$stockObj->getAllStockQtyCmpUtId($compUntID);
			foreach($stockQtyArr as $stck)
			{
				$stkID=$stck[0];
				$stkQty=$stck[1];
				if($stckNewQty=="")
				{
					$stckNewQty=$qty-$stkQty;
					if($stckNewQty>=0)
					{	
						$stckNewQtyVal='0';
						$stockTransferQty=$stockObj->updateStockQuantity($stckNewQtyVal,$stkID);
						continue;
					}
					else
					{
						$stckNewQty=$stkQty-$qty;
						$stockTransferQty=$stockObj->updateStockQuantity($stckNewQty,$stkID);
						break;
					}
				}
				else
				{
					$oldstckQty=$stckNewQty;
					$stckNewQty=$stckNewQty-$stkQty;
					if($stckNewQty>=0)
					{	
						$stckNewQtyVal='0';
						$stockTransferQty=$stockObj->updateStockQuantity($stckNewQtyVal,$stkID);
						continue;
					}
					else
					{
						$stckNewQty=$stkQty-$oldstckQty;
						$stockTransferQty=$stockObj->updateStockQuantity($stckNewQty,$stkID);
						break;
					}
				}
			}
		}
		
		list($supplierstockid,$comUnitId)=$stockObj->getCompanyUnitId($toCompany,$unitTo,$stockIdFrom);
		if($comUnitId!="")
		{
			$stockTransferRecIns=$stockObj->addStockTransferQuantity($supplierstockid,$supplier,$stockIdFrom,$qty,$comUnitId);
		}

		if($stockTransferRecIns) 
		{
				if( $err!="" ) printJSAlert($err);
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddStockTransfer);
				$sessObj->createSession("nextPage",$url_afterAddStockTransfer.$selection);
		} 
		else 
		{
				$addMode	=	true;
				$err		=	$msg_failAddStockTransfer;
		}
		$stockTransferRecIns		=	false;
		$hidEditId 	="";
	}
	

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
	
	# Delete Stock Issuance
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
	//if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		$stockTransferRecords	= $stockObj->fetchAllStockTransfer();

		//$stockIssuanceRecords	= $stockissuanceObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		//$stockIssuanceSize	= sizeof($stockIssuanceRecords);
		//$fetchAllStockIssuanceRecs = $stockissuanceObj->fetchAllDateRangeRecords($fromDate, $tillDate);
	//}
	//$stockissuanceObj->fetchAllRecords()
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($fetchAllStockIssuanceRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# List all Stocks
	//$stockRecords		= $stockObj->fetchAllActiveRecords();
	$stockRecords		= $stockObj->fetchAllActiveRecordsConfirm();
	
	# List all Supplier
	//$supplierRecords	= $supplierMasterObj->fetchAllRecords();
	
	# List all Departments
	$departmentRecords	= $departmentObj->fetchAllRecordsActivedept();
	
	list($companyNames,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId);	


	/*if ($editMode) $heading	=	$label_editStockIssuance;
	else $heading	=	$label_addStockIssuance;*/
	$heading="Stock Transfer";
	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/stocktransfer.js"; // For Printing JS in Head section

	if ($addMode) $mode = 1;
	else if ($editMode) $mode = 2;
	else $mode = "";

//	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");



?>
<form name="frmStockTransfer" action="StockTransfer.php" method="post">
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
													<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateStockIssuance(document.frmStockIssuance);">										
												</td>
												<?} else{?>
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockIssuance.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateStockTransfer(document.frmStockTransfer);"> &nbsp;&nbsp;												</td>
												<?}?>
										</tr>
										<input type="hidden" name="hidStockIssuanceId" value="<?=$editStockIssuanceId;?>">
										<tr>
											<td class="fieldName" nowrap >&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<TD nowrap>
												<span class="fieldName" style="color:red; line-height:normal" id="requestNumExistTxt"></span>
											</TD>
										</tr>
										<tr>
											<td colspan="2" nowrap class="fieldName" >
												<table width="200">
												<? 
													if( $genReqNumber==1 ) {
														if( $editMode ) {
												?>
													<tr>
													   <td class="fieldName" nowrap>*Select item:</td>
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
														<td class="fieldName" align='right'>*From&nbsp;Company&nbsp;</td>
														<td class="listing-item">
															<select name="fromCompany" id="fromCompany" onchange="xajax_getfromPlant(this.value,'','');">
																<option value="">--select--</option>			
																<?php
																if(sizeof($companyNames) > 0)
																{
																	foreach($companyNames as $compId=>$compName)
																	{
																		$companyId=$compId;
																		$companyName=$compName;
																		$sel = '';
																		if(($Company_Name == $companyId) || ($Company_Name=="" && $companyId==$defaultCompany))
																		$sel = 'selected';
																			echo '<option '.$sel.' value="'.$companyId.'">'.$companyName.'</option>';
																	}
																}
																?>
															</select>
														</td>
													</tr>
													<tr>
														<td class="fieldName" align='right'>*From&nbsp;Unit:&nbsp;</td>
														<td class="listing-item">
															<select name="fromPlant" id="fromPlant"  onchange="xajax_getItem(document.getElementById('fromCompany').value,this.value);">
																<option value="">--select--</option>
																<?php
																	($Company_Name!="")?$units=$unitRecords[$Company_Name]:$units=$unitRecords[$defaultCompany];
																	if(sizeof($units) > 0)
																	{
																		foreach($units as $untId=>$untName)
																		{
																			$unitId=$untId;
																			$unitName=$untName;
																			$sel = '';
																			if($unit == $unitId) $sel = 'selected';
																			echo '<option '.$sel.' value="'.$unitId.'">'.$unitName.'</option>';
																		}
																	}
																?>
															</select>
														</td>
													</tr>
													<tr>
														<td class="fieldName" nowrap>*Item:</td>
														<td class="listing-item">
														<!-- <input name="requestNo" type="text" id="requestNo" size="10" value="<?=$requestNo?>" tabindex="1" onchange="xajax_checkRequestNumberExist(document.getElementById('requestNo').value, '<?=$requestNo?>', <?=$mode?>);">-->
															<select name="item" id="item" onChange="xajax_getSupplierDetail(document.getElementById('fromCompany').value,document.getElementById('fromPlant').value,document.getElementById('item').value);">
																<option value="">--select--</option>
																<?php
															/*	foreach ($getPlantUnitRecs as $gpu) {				
																	$itemId		=	$gpu[0];
																	$itemName	=	stripSlash($gpu[1]);
																	//$selected = ($itemId==$plantunit)?"Selected":"";	
																?>
																<option value="<?=$itemId?>" <?=$selected;?>><?=$itemName;?></option>
																<? }*/
																?>
															</select>
														</td>
														<!--<td nowrap><span class="fieldName" style="color:red" id="requestNumExistTxt"></span></td>-->
													</tr>
													<?
														}
													?>
													<tr>
														<td class="fieldName" nowrap>*Supplier</td>
														<td class="listing-item">
														<!-- <input name="requestNo" type="text" id="requestNo" size="10" value="<?=$requestNo?>" tabindex="1" onchange="xajax_checkRequestNumberExist(document.getElementById('requestNo').value, '<?=$requestNo?>', <?=$mode?>);">-->
															<select name="supplier" id="supplier" onChange="xajax_getStockQuantity(document.getElementById('item').value,document.getElementById('fromCompany').value,document.getElementById('fromPlant').value,this.value);">
																<option value="">--select--</option>
																<?php
																foreach ($supplierRecs as $spr) {				
																	$supplierId		=	$spr[0];
																	$supplierName	=	stripSlash($spr[1]);
																	//$selected = ($itemId==$plantunit)?"Selected":"";	
																?>
																<option value="<?=$supplierId?>" <?=$selected;?>><?=$supplierName;?></option>
																<? }?>
															</select>
														</td>
														<!--<td nowrap><span class="fieldName" style="color:red" id="requestNumExistTxt"></span></td>-->
													</tr>
												
													<tr>
													   <td class="fieldName" nowrap>*Existing Quantity:</td>
													   <td class="listing-item">
															<input type="text" name="fromqty" id="fromqty"   />
															<input type="hidden" name="companyUnitId" id="companyUnitId"   />
															
														</td>
														<!--<td nowrap><span class="fieldName" style="color:red" id="requestNumExistTxt"></span></td>-->
													</tr>
													 <tr>
														<td class="fieldName" align='right'>*To Company:&nbsp;</td>
														<td class="listing-item">
															<select name="toCompany" id="toCompany" onchange="xajax_getUnitInGRN(document.getElementById('item').value,document.getElementById('supplier').value,this.value);">
																<option value="">--select--</option>
																<?php
															/*	if(sizeof($companyNames) > 0)
																{
																	foreach($companyNames as $compId=>$compName)
																	{
																		$companyId=$compId;
																		$companyName=$compName;
																		$sel = '';
																		if(($Company_Name == $companyId) || ($Company_Name=="" && $companyId==$defaultCompany))
																		$sel = 'selected';
																			echo '<option '.$sel.' value="'.$companyId.'">'.$companyName.'</option>';
																	}
																}*/
																?>
															</select>
														</td>
												   </tr>
												   <tr>
														<td class="fieldName" align='right'>*To Unit:&nbsp;</td>
														<td class="listing-item">
															<select name="toPlant" id="toPlant">
																<option value="">--select--</option>
																<?php
																/*	($Company_Name!="")?$units=$unitRecords[$Company_Name]:$units=$unitRecords[$defaultCompany];
																	if(sizeof($units) > 0)
																	{
																		foreach($units as $untId=>$untName)
																		{
																			$unitId=$untId;
																			$unitName=$untName;
																			$sel = '';
																			if($unit == $unitId) $sel = 'selected';
																			echo '<option '.$sel.' value="'.$unitId.'">'.$unitName.'</option>';
																		}
																	}*/
																?>
															</select>
														</td>
												   </tr>
												   <tr>
														<td class="fieldName" align='right'>Enter&nbsp;Quantity</td><td><input type="text" name="quantity" id="quantity">
														</td>
													</tr>
												 </table>
											</td>
										</tr>
										<tr>
											<td colspan="2">&nbsp;</td>
										</tr>					
											<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="<?=$rowSize?>">
										<tr>
											<? if($editMode){?>
											<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockIssuance.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange2" class="button" value=" Save Changes " onClick="return validateStockIssuance(document.frmStockIssuance);">
											</td>
											<?} else{?>
											<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockIssuance.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd2" class="button" value=" Add "  onClick="return validateStockTransfer(document.frmStockTransfer);">&nbsp;&nbsp;												
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
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="75%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;Stock Transfer  </td>
								<td background="images/heading_bg.gif" align="right" nowrap="nowrap">
									<table cellpadding="0" cellspacing="0">
										<tr>
											<td nowrap="nowrap">
												<table cellpadding="0" cellspacing="0">
                      			
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
											<td nowrap><? if($del==true){?><!--<input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stockIssuanceSize;?>);">--><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Stock Transfer " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><!--<input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStockIssuance.php',700,600);"><? }?>--></td>
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
									if( sizeof($stockTransferRecords) > 0 )
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
											<td width="20" class="listing-head" align="center" style="padding-left:10px; padding-right:10px;"><!--<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox">-->Date</td>
											<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Item</td>
											<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">From Company</td>
											<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">From Unit</td>
											<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">To Company</td>
											<td class="listing-head" style="padding-left:10px; padding-right:10px;">To Unit</td>
											<td class="listing-head">Quantity</td>
											
										</tr>
										<?
										foreach ($stockTransferRecords as $str) {
											$i++;
											$stockIssuanceId	=	$str[0];
											$date=dateFormat($str[1]);
											$item=$str[2];
											$namefrom		=	stripSlash($str[3]);
											$fromunit		=	$str[4];
											$nameto		=	stripSlash($str[5]);
											$tounit		=	$str[6];
											$quantity=$str[7];
											//$itemNameRec=$stockObj->find($item);
										
											
											
											//$department		=	$sir[2];
											/*$departmentRec		=	$departmentObj->find($sir[2]);
											$departmentId		=	$departmentRec[0];
											$departmentName		=	stripSlash($departmentRec[1]);
											$createdDate		= dateFormat($sir[3]);*/
										?>
										<tr  bgcolor="WHITE">
											<td width="20" class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$date;?><!--<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$stockIssuanceId;?>" class="chkBox">--></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$item;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?//=$fromunit;?><?=$namefrom?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?//=$fromunit;?><?=$fromunit?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?//=$tounit;?><?=$nameto?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?//=$tounit;?><?=$tounit?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
												<?=$quantity;?>
											</td>
										</tr>
										<?
											}
										?>
										<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
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
						<tr >	
							<td colspan="3">
								<table cellpadding="0" cellspacing="0" align="center">
									<tr>
										<td nowrap><? if($del==true){?><!--<input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stockIssuanceSize;?>);">--><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Stock Transfer " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><!--<input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStockIssuance.php',700,600);">--><? }?></td>
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
<!--
<? 
if ($addMode || $editMode) {?>
<SCRIPT LANGUAGE="JavaScript">
	 function addNewStockIssuanceItem() 
	{
		addNewStockIssuanceItemRow('tblAddStkIssuanceItem', '', '', '', '');
	}
		
	balanceQty();
</SCRIPT>	
<? }?> 
<? if ($addMode) {?>
<SCRIPT LANGUAGE="JavaScript">
window.onLoad = addNewStockIssuanceItem();
</SCRIPT>
<? }?>
<? 
	if ($editMode!="") {
		if (sizeof($issuanceRecs)>0) {
			foreach ($issuanceRecs as $isr) {				
				$selStockId = $isr[2];
				$exisitingQty = $isr[3];				
				$qty 	= $isr[4];				
				$bqty 	= $isr[5];	
?>
	<SCRIPT LANGUAGE="JavaScript">
	 	addNewStockIssuanceItemRow('tblAddStkIssuanceItem', '<?=$selStockId?>', '<?=$qty?>', '<?=$bqty?>', '<?=$exisitingQty?>');
		balanceQty();
	</SCRIPT>	
<? 
			}
		}
	}
?>
-->
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