<?
	require("include/include.php");
	require_once('lib/stockrequisition_ajax.php');
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

	# Add Stock Requisition Start 
	if ($p["cmdAddNew"]!="") {
		$addMode	=	true;
	}
	
	if ($p["cmdCancel"]!="") {
		$addMode	=	false;	
	}	
	
	# Checking auto generate Exist
	$genReqNumber = $idManagerObj->check("SI");

	#Add
	if ($p["cmdAdd"]!="" ) {

		$department	=	$p["department"];
		$item		=	$p["item"];
		$company		=	$p["company"];
		$unit		=	$p["unit"];
		$stockQty	=	$p["stockQty"];
		$qty		=	$p["qty"];
	

		if ($department!="" && $item!="" &&  $company!="" && $unit!="") 
		{
			//echo "hii"; 
			$stockRequisitionRecIns	=	$stockRequisitionObj->addStockRequisition($department, $item,$company,$unit,$stockQty,$qty,$userId);
			//die();
			if ($stockRequisitionRecIns) {
				if( $err!="" ) printJSAlert($err);
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddStockRequisition);
				$sessObj->createSession("nextPage",$url_afterAddStockRequisition.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddStockRequisition;
			}
			$stockRequisitionRecIns		=	false;
		}
		$hidEditId 	=  "";
	}
	

	# Edit Stock Requisition
	if ($p["editId"]!="") {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$stockRequisitionRec	=	$stockRequisitionObj->find($editId);		
		$editStockRequisitionId	=	$stockRequisitionRec[0];		
		$department		=	$stockRequisitionRec[1];		
		$item	=	$stockRequisitionRec[2];
		$company	=	$stockRequisitionRec[3];
		$unit	=	$stockRequisitionRec[4];
		$stockQty	=	$stockRequisitionRec[5];
		$qty	=	$stockRequisitionRec[6];
		// Get Requisition Records
		//$RequisitionRecs = $stockRequisitionObj->fetchAllStockItem($editStockRequisitionId);
		//$plantUnitRecords=$stockRequisitionObj->getUnitUser($item);
		$companyRecs=$stockRequisitionObj->getCompanyUser($item);
		$plantUnitRecords=$stockRequisitionObj->getUnitUser($item,$company);
	
	}

	#Update 
	if ($p["cmdSaveChange"]!="") 
	{		
		$stockRequisitionId	=	$p["hidStockRequisitionId"];
		$department	=	$p["department"];
		$item		=	$p["item"];
		$company		=	$p["company"];
		$unit		=	$p["unit"];
		$stockQty	=	$p["stockQty"];
		$qty		=	$p["qty"];
				
		if ($stockRequisitionId!="" && $department!="" && $item!="") 
		{
			$stockRequisitionRecUptd	=	$stockRequisitionObj->updateStockRequisition($stockRequisitionId,$department, $item,$company,$unit,$stockQty,$qty);
		}
	
		if ($stockRequisitionRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succStockRequisitionUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateStockRequisition.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failStockRequisitionUpdate;
		}
		$stockRequisitionRecUptd	=	false;
		$hidEditId 	= "";
	}
	
	# Delete Stock Requisition
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$stockRequisitionId	=	$p["delId_".$i];

			if ($stockRequisitionId!="" && $isAdmin!="") {
				$stockRequisitionRecDel	=	$stockRequisitionObj->deleteStockRequisition($stockRequisitionId);
			}
		}
		if ($stockRequisitionRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelStockRequisition);
			$sessObj->createSession("nextPage",$url_afterDelStockRequisition.$selection);
		} else {
			$errDel	=	$msg_failDelStockRequisition;
		}
		$stockRequisitionRecDel	=	false;
		$hidEditId 	= "";
	}

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) 
		{
			$stockRequisitionId	=	$p["confirmId"];
			if ($stockRequisitionId!="") {
				$stockRequisitionRecConfirm = $stockRequisitionObj->updateStockRequisitionConfirm($stockRequisitionId);
			}
		}
		if ($stockRequisitionRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmStockRequisition);
			$sessObj->createSession("nextPage",$url_afterDelStockRequisition.$selection);
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
				$stockRequisitionRecConfirm = $stockRequisitionObj->updateStockRequisitionReConfirm($stockRequisitionId);
			}
		}
		if ($stockRequisitionRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmStockRequisition);
			$sessObj->createSession("nextPage",$url_afterDelStockRequisition.$selection);
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
	
	#List all Stock Requisition
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);
		
		$stockRequisitionRecords	= $stockRequisitionObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		$stockRequisitionSize	= sizeof($stockRequisitionRecords);
		$fetchAllStockRequisitionRecs = $stockRequisitionObj->fetchAllDateRangeRecords($fromDate, $tillDate);

	}
	//$stockRequisitionObj->fetchAllRecords()
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($fetchAllStockRequisitionRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# List all Stocks
	//$stockRecords		= $stockObj->fetchAllActiveRecords();
	$stockRecords		= $stockObj->fetchAllActiveRecordsConfirm();
	
	# List all Supplier
	//$supplierRecords	= $supplierMasterObj->fetchAllRecords();
	
	# List all Departments
	//$departmentRecords	= $departmentObj->fetchAllRecordsActivedept();
	$departmentRecords	= $stockRequisitionObj->getAllDepartmentUser($userId);
		
	$getPlantUnitRecs = $stockObj->filterRecords($fromPlant);
	//$plantUnitRecords=$plantandunitObj->fetchAllRecordsPlantsActive();
	
	if ($editMode) $heading	=	$label_editStockRequisition;
	else $heading	=	$label_addStockRequisition;
		
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/stockrequisition.js"; // For Printing JS in Head section

	if ($addMode) $mode = 1;
	else if ($editMode) $mode = 2;
	else $mode = "";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmStockRequisition" action="StockRequisition.php" method="post">
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
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockRequisition.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateStockRequisition(document.frmStockRequisition);">	
												</td>
												<?} else{?>
												<td  colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockRequisition.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateStockRequisition(document.frmStockRequisition);"> &nbsp;&nbsp;	
												</td>
												<?}?>
											</tr>
												<input type="hidden" name="hidStockRequisitionId" value="<?=$editStockRequisitionId;?>">
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
													<? /*
													if( $genReqNumber==1 ) 
													{
														if( $editMode ) 
														{
													?>
														<tr>
														   <td class="fieldName" nowrap>*Request No:</td>
														   <td class="listing-item"><input name="requestNo" tabindex=1  Style="border:none;" readonly  type="text" id="requestNo" size="10" value="<?=$requestNo?>"></td>
														</tr>
														<?
														} 
														else 
														{
														?>
															<input name="requestNo" Style="border:none;" readonly  type="hidden" id="requestNo" value="Y">
														<?
														}							
													} 
													else  
													{
													?>
														<tr>
															<td class="fieldName" nowrap>*Request No:</td>
															<td class="listing-item">
																<input name="requestNo" type="text" id="requestNo" size="10" value="<?=$requestNo?>" tabindex="1" onchange="xajax_checkRequestNumberExist(document.getElementById('requestNo').value, '<?=$requestNo?>', <?=$mode?>);"></td>
																<!--<td nowrap><span class="fieldName" style="color:red" id="requestNumExistTxt"></span></td>-->
														</tr>
													<?
													}*/
													?>
														<input type="hidden" name="hidReqNumber" value="<?=$requestNo?>">
														<tr>
															<td class="fieldName" align='right'>*Department:&nbsp;</td>
															<td class="listing-item">
																<select name="department" id="department">
																	<option value="">--select--</option>
																	<?
																	foreach($departmentRecords as $dp=>$dpval)
																	{
																		$departmentId		=	$dp;
																		$departmentName	=	stripSlash($dpval);
																		$selected="";
																		if($department==$departmentId || $editDepartmentId==$departmentId) echo $selected="Selected";
																	?>
																	<option value="<?=$departmentId?>" <?=$selected?>><?=$departmentName?></option>
																	<? }?>
																</select>
															</td>
															<td class="fieldName" nowrap>*Item:</td>
															<td class="listing-item">
																<select name="item" id="item" onChange="xajax_getCompany(document.getElementById('item').value,'','');">
																	<option value="">--select--</option>
																	<?php
                                        							foreach ($getPlantUnitRecs as $gpu) {				
																	$itemId		=	$gpu[0];
																	$itemName	=	stripSlash($gpu[1]);
																	$selected ="";
																	($itemId==$item)?$selected="Selected":$selected="";	
																	?>
																	<option value="<?=$itemId?>" <?=$selected;?>><?=$itemName;?></option>
																	<? }?>
																</select>
															</td>
														</tr>

														<tr>
															<td class="fieldName" align='right'>*Company:&nbsp;</td>
															<td class="listing-item">
																<select name="company" id="company" onchange="xajax_getUnit(document.getElementById('item').value,this.value);">
																	<? if(sizeof($companyRecs)>0){
																		}else
																		{ ?>
																	<option value="">--select--</option>
																	<? } ?>
																	<?php
                                        							foreach ($companyRecs as $comId=>$comName) {				
																	$companyId		=	$comId;
																	$companyName	=	stripSlash($comName);
																	$selected = "";
																	($company==$companyId)?$selected="Selected":$selected="";		
																	?>
																	<option value="<?=$companyId?>" <?=$selected;?>><?=$companyName;?></option>
																	<? }?>
																</select>
															</td>

                                							<td class="fieldName" align='right'>*Unit:&nbsp;</td>
															<td class="listing-item">
																<select name="unit" id="unit" onchange="xajax_getStockQty(document.getElementById('item').value,document.getElementById('company').value,this.value);">
																	<? if(sizeof($plantUnitRecords)>0){
																		}else
																		{ ?>
																	<option value="">--select--</option>
																	<? } ?>
																	<?php
                                        							foreach ($plantUnitRecords as $pur=>$purVal) {				
																	$plantId		=	$pur;
																	$plantName	=	stripSlash($purVal);
																	$selected = "";
																	($plantId==$unit)?$selected="Selected":$selected="";		
																	?>
																	<option value="<?=$plantId?>" <?=$selected;?>><?=$plantName;?></option>
																	<? }?>
																</select>
															</td>
															
														</tr>
														<tr>
															<td class="fieldName" nowrap>Stock Quantity:</td>
															<td class="listing-item">
																<input type="text" name="stockQty" id="stockQty" value="<?=$stockQty?>" readonly  />
															</td>
															<td class="fieldName" nowrap>*Quantity:</td>
															<td class="listing-item">
																<input type="text" name="qty" id="qty" value="<?=$qty?>"  />
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td colspan="2">&nbsp;</td>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
											<tr>
											<? if($editMode){?>
												<td colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockRequisition.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" id="cmdSaveChange2" class="button" value=" Save Changes " onClick="return validateStockRequisition(document.frmStockRequisition);">
												</td>
											<?} else{?>
												<td  colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockRequisition.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAdd" id="cmdAdd2" class="button" value=" Add " onClick="return validateStockRequisition(document.frmStockRequisition);">&nbsp;&nbsp;												
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
		}# Listing Category Starts
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
									<td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;Stock Requisition  </td>
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
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stockRequisitionSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStockRequisition.php',700,600);"><? }?></td>
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
										if( sizeof($stockRequisitionRecords) > 0 )
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
															$nav.= " <a href=\"StockRequisition.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
														//echo $nav;
													}
													}
													if ($pageNo > 1) {
													$page  = $pageNo - 1;
													$prev  = " <a href=\"StockRequisition.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
													} else {
													$prev  = '&nbsp;'; // we're on page one, don't print previous link
													$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage) {
													$page = $pageNo + 1;
													$next = " <a href=\"StockRequisition.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td class="listing-head"></td>
												<? if($edit==true){?>
												<td class="listing-head"></td>
												<? }?>
												<? if($confirm==true){?>	
												<td class="listing-item">&nbsp;</td><? }?>
											</tr>
											<?
											$i=0;
											foreach ($stockRequisitionRecords as $sir) {
												$i++;
												$stockRequisitionId	=	$sir[0];
												$department		=	$sir[2];
												$item		=	$sir[4];
												$company		=	$sir[6];
												$unit		=	$sir[8];
												$qty		=	$sir[10];
												$createdDate		= dateFormat($sir[11]);
												$active=$sir[13];
											?>
											<tr  bgcolor="WHITE">
												<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$stockRequisitionId;?>" class="chkBox"></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$createdDate;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$department;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$item;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$company;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$unit;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$qty;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
													<a href="javascript:printWindow('ViewStockRequisitionDetails.php?RequisitionId=<?=$stockRequisitionId?>',700,600)" class="link1" title="Click here to view details.">View Details</a>
												</td>
											<? if($edit==true){?>
												<td class="listing-item" width="60" align="center">
												<? if ($active==0){?>	<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$stockRequisitionId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='StockRequisition.php';"> <? } ?></td>
											<? }?>
											<? if ($confirm==true){?>
												<td <?php if ($active==1) {?> class="listing-item" <?php }else {?> <?php }?> width="45" align="center" >
													<?php 
													if ($confirm==true){	
													if ($active==0){ ?>
													<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$stockRequisitionId;?>,'confirmId');" >
													<?php } else if ($active==1){ if ($existingrecords==0) { ?>
													<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$stockRequisitionId;?>,'confirmId');" >
													<?php } } }?>
												</td>
												<? }?>
											</tr>
											<?
												}
											?>
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="editId" value="<?=$editId?>">
											<input type="hidden" name="confirmId" value="">
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
																$nav.= " <a href=\"StockRequisition.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
															//echo $nav;
														}
													}
													if ($pageNo > 1) {
														$page  = $pageNo - 1;
														$prev  = " <a href=\"StockRequisition.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
													} else {
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage) {
														$page = $pageNo + 1;
														$next = " <a href=\"StockRequisition.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stockRequisitionSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStockRequisition.php',700,600);"><? }?></td>
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