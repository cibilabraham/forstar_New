<?
	require("include/include.php");
	require_once("lib/StockReturn_ajax.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	false;
	$selStockId		=	"";
	$userId		=	$sessObj->getValue("userId");

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
	}
	// end

	# Add Stock Issuance Start 
	if ($p["cmdAddNew"]!="") {
		$addMode	=	true;
	}
	
	if ($p["cmdCancel"]!="") {
		$addMode	=	false;	
		$editMode	=	false;
		$stockIssuanceRecIns		=	false;
	}	
	
	$genReqNumber = $idManagerObj->check("SR");


	// --------------------------------------
	// Add New Stock Return to database start
	// --------------------------------------
	if ($p["cmdAdd"]!="" ) 
	{
		$itemCount		=	$p["rowCount"];	
		if( $genReqNumber==1 ) 
		{
			list($isMaxId,$requestNo)	= $idManagerObj->generateNumberByType("SR"); 
			$err  = ( $isMaxId=="Y") ? "The generated return number is greater than the ending number of stock return." : "";
		}
		else $requestNo		=	$p["requestNo"];
		$selDepartment		=	$p["selDepartment"];

		
		if ($requestNo!="") $chkUnique = $stockReturnObj->checkUnique($requestNo, "");
		
		if ( ($requestNo!="" && $selDepartment!="" )  && $chkUnique==0 ) 
		{	
			$stockIssuanceRecIns	=	$stockReturnObj->addStockReturn($requestNo, $selDepartment, $userId);
			$lastId = $databaseConnect->getLastInsertedId();
			
			for ($i=0; $i<$itemCount; $i++) 
			{
				$status = $p["Status_".$i];
				if( $status !="N" )
				{
					$stockId = $p["selStock_".$i];
					$resonSel = trim($p["selReason_".$i]);
					$quantity = trim($p["quantity_".$i]);
					$scrapValue = trim($p["scrapValue_".$i]);
					$totalAmount = trim($p["totalAmt_".$i]);
					$remark = $p["remark_".$i];
					$incCosting = ($p["incCosting_".$i]=="")?N:Y; // Include in Costing

					if ($lastId!="" && $stockId!="" && $quantity!="" ) 
					{
						$stockIssuanceRecIns = $stockReturnObj->addReturnEntries($lastId, $stockId, $quantity, $resonSel, $remark, $scrapValue, $totalAmount, $incCosting);
					}
				} 
				//end status 
			}
		}
		else if ($chkUnique==1) $err = " Failed to add Stock Return. Please make sure the return number you have entered is not duplicate. ";
		
		if($stockIssuanceRecIns) 
		{
			if( $err!="" ) printJSAlert($err);
			$addMode = false;
			$sessObj->createSession("displayMsg",$msg_succAddStockReturn);
			$sessObj->createSession("nextPage",$url_afterAddStockReturn.$selection);
		} 
		else {
			$addMode	=	true;
			$err		=	$msg_failAddStockReturn;
		}
		$stockIssuanceRecIns		=	false;
		$hidEditId 	=  "";
	}
	// ------------------------------------
	// Add New Stock Return to database end
	// ------------------------------------



	
	// ----------------------------------------
	// Get Edit records from the database start 
	// ----------------------------------------
	if ($p["editId"]!="") {
		$editId	= $p["editId"];
		$editMode = true;
		$stockRetRec = $stockReturnObj->find($editId);		
		$editStockReturnId = $stockRetRec[0];		
		$requestNo = $stockRetRec[1];		
		$editDepartmentId = $stockRetRec[2];
		$retRecs = $stockReturnObj->fetchAllStockItem($editStockReturnId);
	}
	// --------------------------------------
	// Get Edit records from the database end 
	// --------------------------------------

	
	///////////////////// Save Edit records to database start/////////////////////
	
	if ($p["cmdSaveChange"]!="") 
	{		
		$stockRetId = $p["hidStockReturnId"];
		$itemCount = $p["rowCount"];		
		$requestNo = $p["requestNo"];
		$selDepartment = $p["selDepartment"];
		
		if ($stockRetId!="" && $requestNo!="" && $selDepartment!="" ) 
		{
			$stockIssuanceRecUptd	=	$stockReturnObj->updateStockReturn($stockRetId, $requestNo, $selDepartment);
			$deleteStockIssuanceItemRecs	=	$stockReturnObj->deleteReturnItemRecs($stockRetId);
			
			for ($i=0; $i<$itemCount; $i++) 
			{
				$status = $p["Status_".$i];
				if( $status !="N" )
				{
					$stockId = $p["selStock_".$i];
					$resonSel = trim($p["selReason_".$i]);
					$quantity = trim($p["quantity_".$i]);
					$scrapValue = trim($p["scrapValue_".$i]);
					$totalAmount = trim($p["totalAmt_".$i]);
					$remark = $p["remark_".$i];
					$incCosting = ($p["incCosting_".$i]=="")?N:Y; // Include in Costing
			

					if ($stockRetId!="" && $stockId!="" && $quantity!="" ) 
					{
						$stockIssuanceRecIns = $stockReturnObj->addReturnEntries($stockRetId, $stockId, $quantity, $resonSel, $remark, $scrapValue, $totalAmount, $incCosting);
					}
				} 
				//end status 
			}		
		}
	
		if ($stockIssuanceRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succStockReturnUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateStockReturn.$selection);
		} else {			
			$editId	= $stockRetId;
			$editMode	=	true;
			$err		=	$msg_failStockReturnUpdate;
		}
		$stockIssuanceRecUptd	=	false;
		$hidEditId 	= "";
	}
	///////////////////// Save Edit records to database end/////////////////////

	
	///////////////////////Delete records starts///////////////////
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$stockRetId	=	$p["delId_".$i];
			if ($stockRetId!="" && $isAdmin!="") {
				$deleteStockRetItemRecs	=	$stockReturnObj->deleteReturnItemRecs($stockRetId);
				$stockRetRecDel =	$stockReturnObj->deleteStockReturn($stockRetId);	
			}
		}
		if ($stockRetRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelStockReturn);
			$sessObj->createSession("nextPage",$url_afterDelStockReturn.$selection);
		} else {
			$errDel	=	$msg_failDelStockReturn;
		}
		$stockIssuanceRecDel	=	false;
		$hidEditId 	= "";
	}
	///////////////////////Delete records end///////////////////  



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

		$stockRetRecords	= $stockReturnObj->fetchAllPagingRecords($fromDate, $tillDate, $offset, $limit);
		$stockRetSize	= sizeof($stockRetRecords);
		$fetchAllStockRetRecs = $stockReturnObj->fetchAllDateRangeRecords($fromDate, $tillDate);
	}
	
	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($fetchAllStockRetRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# List all Stocks
	$stockRecords		= $stockObj->fetchAllRecords();
	
	# List all Supplier
	//$supplierRecords	= $supplierMasterObj->fetchAllRecords();
		
	# List all Departments
	//$departmentRecords	= $departmentObj->fetchAllRecords();

	$departmentRecords	= $departmentObj->fetchAllRecordsActivedept();
		
	if ($editMode) $heading	=	$label_editStockReturn;
	else $heading		=	$label_addStockReturn;
		
	# Setting the mode
	if ($addMode) 		$mode = 1;
        else if ($editMode) 	$mode = 2;

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS = "libjs/StockReturn.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmStockReturn" action="StockReturn.php" method="post" id="frmStockReturn">
	<table cellspacing="0"  align="center" cellpadding="0" width="90%" >
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
		</tr>
		<?
		if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%"  bgcolor="#D3D3D3">
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
										<table cellpadding="0"  width="80%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>
												<td colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="document.frmStockReturn.editId.value=''; return cancel('StockReturn.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdSaveChange" id="cmdSaveChange2" class="button" value=" Save Changes " onClick="return validateStockReturn(document.frmStockReturn);">					</td>
												<?} else{?>
												<td  colspan="2" align="center">
													<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockReturn.php');">&nbsp;&nbsp;
													<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateStockReturn(document.frmStockReturn);"> &nbsp;&nbsp;												</td>
												<?}?>
											</tr>
												<input type="hidden" name="hidStockReturnId" value="<?=$editStockReturnId;?>">
											<tr>
												<td class="fieldName" nowrap >&nbsp;</td>
												<td>&nbsp;</td>
											</tr>
											<tr>
												<td colspan="2" nowrap class="fieldName" >
													<table width="200">
													<? 
													if( $genReqNumber==1 )
													{
														if( $editMode )  
														{
													?>
														<tr>
															<td class="fieldName" nowrap>*Return No:</td>
															<td class="listing-item" colspan='2' nowrap ><input name="requestNo" tabindex=1  Style="border:none;" readonly  type="text" id="requestNo" size="10" value="<?=$requestNo?>" onChange="xajax_validateReturnNumber(document.getElementById('requestNo').value,'<?=$requestNo?>','erMsg',2);" >&nbsp;&nbsp;<span id="erMsg" class="fieldName" nowrap style="color:red;"></span></td> 
														</tr>
														<?
														}
														else {
														?>
															<input name="requestNo" Style="border:none;" readonly  type="hidden" id="requestNo" value="Y">
														<?
														}
													}
													else 
													{
														$ids= 1;
														if( $editMode ) $ids = 2;
													?>
														<tr>
														   <td class="fieldName" nowrap>*Return No:</td>
														   <td class="listing-item" colspan="2" nowrap ><input name="requestNo" type="text" id="requestNo" size="10" value="<?=$requestNo?>" tabindex="1" onChange="xajax_validateReturnNumber(document.getElementById('requestNo').value,'<?=$requestNo?>','erMsg',<?=$ids;?>);" >&nbsp;&nbsp;<span id="erMsg" class="fieldName" nowrap style="color:red;"></span></td>
														</tr>
														<?
														}
														?>
															<input type="hidden" name="hidReqNumber" value="<?=$requestNo?>">
														<tr>
                                							<td class="fieldName" align='right' >*Department:&nbsp;</td>
															<td class="listing-item">
																<select name="selDepartment" id="selDepartment" tabindex="1" onchange="getDepartmentWiseStock(document.getElementById('selDepartment').value,document.getElementById('hidItemCount').value, <?=$mode?>)">
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
															<td></td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
				  								<td colspan="3" nowrap>
													<table width="500" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddItem">
														<tr bgcolor="#f2f2f2" align="center">
															<td class="listing-head" style="padding-left:5px;padding-right:5px;">Item</td>
															<td class="listing-head" nowrap="nowrap" style="padding-left:5px;padding-right:5px;">Reason</td>
															<td class="listing-head" style="padding-left:5px;padding-right:5px;">Quantity</td>
															<td class="listing-head" nowrap align='center' style="padding-left:5px;padding-right:5px;">Include<br> in Costing </td>
															<td class="listing-head" nowrap align='center' style="padding-left:5px;padding-right:5px;">Total<br>Scrap Value </td>					
															<td class="listing-head" style="padding-left:5px;padding-right:5px;">Remarks</td>
															<td class="listing-head" width="20" style="padding-left:5px;padding-right:5px;">Remove</td>
														</tr>
														<tr bgcolor="#ffffff" align="center">
															<td class="listing-head" colspan='2' align='right' style="padding-left:5px;padding-right:5px;">Total</td>
															<td class="listing-head" nowrap align='right' id="subTotalQuantity" style="padding-left:5px;padding-right:5px;"></td>
															<td></td>
															<td class="listing-head"  align='right' id="subTotalScrapVal" style="padding-left:5px;padding-right:5px;"></td>
															<td class="listing-head" nowrap align='right' id="subTotalTotalAmt" style="padding-left:5px;padding-right:5px;"></td>
															<td class="listing-head" colspan='2' style="padding-left:5px;padding-right:5px;"></td>
														</tr>
													</table>
												</td>
											</tr>
													<input type='hidden' name='rowCount' id='rowCount' value="<?=$m;?>" >
													<input type='hidden' name='hidItemCount' id='hidItemCount' value="<?=$m;?>">
													<input type="hidden" name="newline" value="">
													<input type="hidden" name="new" value="<?=$m?>" />
											<tr>
												<td colspan="2" nowrap class="fieldName"  Style='padding-top:5px;'>
													<? 
														if($addMode==true) {
													?>
													<a href="javascript:addNewStockSelection('tblAddItem',document.frmStockReturn,<?=$mode?>,'','','','','','','');"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
													<?	} else { ?>
													<a href="javascript:addNewStockSelection('tblAddItem',document.frmStockReturn,<?=$mode?>,'','','','','','','');"  class="link1" title="Click here to add new item." onclick="document.frmStockReturn.editId.value=<?=$editId?>;" ><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
													<? 
														}
													?>
												</td>
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="document.frmStockReturn.editId.value=''; return cancel('StockReturn.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onClick="return validateStockReturn(document.frmStockReturn);">						</td>
												
												<?} else{?> 

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('StockReturn.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd2" class="button" value=" Add " onClick="return validateStockReturn(document.frmStockReturn);">&nbsp;&nbsp;												</td>
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
									<td background="images/heading_bg.gif" class="pageName" nowrap >&nbsp;Stock Return  </td>
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stockRetSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStockReturn.php?fd=<?=$fromDate;?>&td=<?=$tillDate;?>',700,600);"><? }?></td>
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
										<table cellpadding="2"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
											<?
											if( sizeof($stockRetRecords) > 0 )
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
																$nav.= " <a href=\"StockReturn.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
															//echo $nav;
														}
													}
													if ($pageNo > 1) {
														$page  = $pageNo - 1;
														$prev  = " <a href=\"StockReturn.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
													} else {
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage) {
														$page = $pageNo + 1;
														$next = " <a href=\"StockReturn.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' class="chkBox" onClick="checkAll(this.form,'delId_'); " ></td>
												<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;" align='center'>Date</td>
												<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;" align='center'>Return No</td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;" align='center' >Department</td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;" align='center' nowrap >Total<br>Scrap Value</td>
												<td class="listing-head"></td>
												<? if($edit==true){?>
												<td class="listing-head"></td>
												<? }?>
											</tr>
											<?
											$subTotalScrapVal  = 0 ;
											foreach ($stockRetRecords as $sir) {
												$i++;
												$stockRetId	=	$sir[0];
												$requestNo	=	$sir[1];
												$departmentRec	=	$departmentObj->find($sir[2]);
												$departmentId	=	$departmentRec[0];
												$departmentName	=	stripSlash($departmentRec[1]);
												$createdDate	= dateFormat($sir[3]);
												$totScrapVal = $stockReturnObj->getTotalVal($stockRetId,'scrap_value');
												$subTotalScrapVal =  $subTotalScrapVal+$totScrapVal;
											?>
											<tr bgcolor="white">
												<td width="20"><input type="checkbox" class="chkBox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$stockRetId;?>" ></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align='center' ><?=$createdDate;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align='center' ><?=$requestNo;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" ><?=$departmentName;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align='right' ><?=$totScrapVal;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
													<a href="javascript:printWindow('ViewStockReturnDetails.php?retId=<?=$stockRetId?>',700,600)" class="link1" title="Click here to view details.">View Details</a>
												</td>
												
											<? if($edit==true){?>
												<td class="listing-item" width="60" align="center"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$stockRetId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='StockReturn.php';"></td>
											<? }?>
											</tr>
											<?
												}
											?>
											<tr bgcolor='white'>
												<td colspan='4' class="listing-head"  align='right'>Total</td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align='right'><?=number_format($subTotalScrapVal,2,".","");?></td>
												<td colspan='<?=($edit==true) ? 2 : 1 ; ?>'></td>
											</tr>
												<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
												<input type="hidden" name="editId" value="<?=$editId;?>">
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
																$nav.= " <a href=\"StockReturn.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\" class=\"link1\">$page</a> ";
															//echo $nav;
														}
													}
													if ($pageNo > 1) {
														$page  = $pageNo - 1;
														$prev  = " <a href=\"StockReturn.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\"><<</a> ";
													} else {
														$prev  = '&nbsp;'; // we're on page one, don't print previous link
														$first = '&nbsp;'; // nor the first page link
													}

													if ($pageNo < $maxpage) {
														$page = $pageNo + 1;
														$next = " <a href=\"StockReturn.php?selectFrom=$dateFrom&selectTill=$dateTill&pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$stockRetSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintStockReturn.php?fd=<?=$fromDate;?>&td=<?=$tillDate;?>',700,600);"><? }?></td>
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
		<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>">		
		<input type="hidden" name="requestNumExist" id="requestNumExist">
		<tr>
			<td height="10"></td>
		</tr>
	</table>
	<? 
	if ($addMode || $editMode) 
	{
		if( $editMode )
		{
			for($d=0; $d<sizeof($retRecs); $d++ )
			{
				$lrec = $retRecs[$d];
				$eid = $lrec[2];
				$sval = $lrec[3];
				$qty = $lrec[4];
				$rid = $lrec[5];
				$rmk = $lrec[6];
				$totQty = ($rid=='L' || $rid=='S') ? "" : $lrec[7];
				$includeCosting = $lrec[8];
	?>
		<SCRIPT LANGUAGE="JavaScript">
		<!--
			addNewStockSelection('tblAddItem',document.frmStockReturn,'<?=$mode?>','<?=$eid;?>','<?=$qty;?>','<?=$totQty;?>','<?=$sval;?>','<?=$rid;?>','<?=$rmk;?>','<?=$includeCosting?>');
			displayAmtInput("<?=$d;?>");
			calculateTotalAmount("<?=$d;?>");
		//-->
		</SCRIPT>
	<?
			}
	?>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
			calcSubTotalValues();
	//-->
	</SCRIPT>
	<?
	}
	else 
	{
	?>
	<SCRIPT LANGUAGE="JavaScript">
	//
	addNewStockSelection('tblAddItem',document.frmStockReturn,'<?=$mode?>', '','','','','','','');
	//alert("hii");
	</SCRIPT>	
	<?
		}
	}
	?>
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "schedule",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "schedule", 
			ifFormat    : "%m/%d/%Y",    // the date format
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