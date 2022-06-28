<?
	require("include/include.php");
	require_once('lib/stockallocation_ajax.php');
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


	if($p["btnCancelAllocation"] !="")
	{
		//echo "hii";
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$requisitionId	=	$p["delId_".$i];
			$stockIssuanceDet = $stockAllocationObj->getstockIssuanceIdRequisition($requisitionId);
			if(sizeof($stockIssuanceDet)>0)
			{
				foreach($stockIssuanceDet as $issuance)
				{
					$issuanceId=$issuance[0];	
					if($issuanceId!="")
					{
						$stockDetail = $stockAllocationObj->getstockIssuanceDetail($issuanceId);
						$supplierStockId=$stockDetail[1];
						$stockId=$stockDetail[2];
						$supplierId=$stockDetail[3];
						$allotQty=$stockDetail[5];
						$companyId=$stockDetail[6];
						$unitId=$stockDetail[7];
						$delStock = $stockAllocationObj->deleteIssuanceItem($issuanceId);
						if($delStock)
						{	//echo "hii".$supplierStockId.','.$supplierId.','.$stockId.','.$allotQty;
							$companyUnitId=$stockAllocationObj->getCompanyUnitId($supplierStockId,$supplierId,$stockId,$companyId,$unitId);
							$insStock = $stockAllocationObj->insertStockQty($supplierStockId,$supplierId,$stockId,$allotQty,$companyUnitId);
						}
					}
				}
			}
			//die();
			//echo $allocationId;
		}
		if ($insStock) {
			$sessObj->createSession("displayMsg",$msg_succAddStockAllocationRemoval);
			$sessObj->createSession("nextPage",$url_afterDelStockAllocation.$selection);
		} else {
			$errDel = $msg_succFailStockAllocationRemoval;
		}
		$delStock	=	false;
		
	}



	
	# Checking auto generate Exist
	$genReqNumber = $idManagerObj->check("SI");

		

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
		
		$stockIssuanceRecords	= $stockAllocationObj->fetchAllPagingRecords($fromDate, $tillDate,$offset, $limit);
		$stockIssuanceSize	= sizeof($stockIssuanceRecords);
		$fetchAllStockIssuanceRecs = $stockAllocationObj->fetchAllDateRangeRecords($fromDate, $tillDate);
	}
	//$stockAllocationObj->fetchAllRecords()
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
		
	if ($editMode) $heading	=	$label_editStockAllocation;
	else $heading	=	$label_addStockAllocation;
		
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/stockissuance.js"; // For Printing JS in Head section

	if ($addMode) $mode = 1;
	else if ($editMode) $mode = 2;
	else $mode = "";




	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>

<form name="frmStockAllocation" id="frmStockAllocation" action="StockAllocation.php" method="post">
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
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="82%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;Stock Allocation  </td>
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
									<td colspan="3" height="10"></td>
								</tr>
								<tr align="center">
									<td  align="center" nowrap="nowrap" colspan="3"  >
										<?php
										/*
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
											*/
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
												<? if(($confirm==true) || ($reEdit==true)){?><input type="submit" value=" Cancel Allocation " name="btnCancelAllocation" class="button" ><? }?>
												
												&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStockAllocation.php',700,600);"><? }?></td>
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
												<td width="20">
													<!--<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox fsaChkbx">-->
												</td>
												<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Date</td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;">Department</td>
												<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Item</td>
												<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Company</td>
												<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Unit</td>
												<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Quantity</td>
												<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Supplier</td>
												<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Allot Quantity</td>
											<!--	<? if($confirm==true){?>	
												<td class="listing-item">&nbsp;</td><? }?>
												-->
											</tr>
											<? $totalQty=0;
											foreach ($stockIssuanceRecords as $sir) {
												$i++;
												$stockIssuanceId	=$sir[0];
												$stockRequisitionId	=	$sir[1];
												$department		=	$sir[3];
												$item		=	$sir[5];
												$company		=	$sir[7];
												$unit		=	$sir[9];
												$qty		=	$sir[11];
												$createdDate		= dateFormat($sir[12]);
												$itemId		=	$sir[4];
												$stockAllot=$stockAllocationObj->getStockIssuanceDetailRequisition($stockRequisitionId);
											?>
											<tr  bgcolor="WHITE">
												<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$stockRequisitionId;?>" class="chkBox fsaChkbx"></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$createdDate;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$department;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$item;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$company;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$unit;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$qty;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
												<? foreach($stockAllot as $stkAllot)
												{
													echo $stkAllot[2].'<br/>';
												}
												?>
												</td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
												<? foreach($stockAllot as $stkAllot)
												{
													echo $stkAllot[3].'<br/>';
												}
												?>
												</td>
											<!--	<? if ($confirm==true){?>
												<td <?php if ($active==1) {?> class="listing-item" <?php }else {?> <?php }?> width="45" align="center" >
													<?php 
													if ($confirm==true){	
													if ($active==0){ ?>
													<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$stockRequisitionId;?>,'confirmId');" >
													<?php } else if ($active==1){ if ($existingrecords==0) { ?>
													<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$stockRequisitionId;?>,'confirmId');" >
													<?php } } }?>
												</td>
												<? }?>-->
											</tr>
											<?
												}
											?>
											
											<input type="hidden" name="totalQty"	id="totalQty" value="<?=$totalQty?>" >
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
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><!--<? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stockIssuanceSize;?>);"><? }?>&nbsp;
												<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>-->
												
												<? if(($confirm==true) || ($reEdit==true)){?><input type="submit" value=" Cancel Allocation " name="btnCancelAllocation" class="button" ><? }?>
												&nbsp;
												<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStockAllocation.php',700,600);"><? }?></td>
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

	$(document).ready(function(){
 var $unique = $('input.fsaChkbx');
$unique.click(function() {
    $unique.filter(':checked').not(this).removeAttr('checked');
});
});
	</SCRIPT>
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
	