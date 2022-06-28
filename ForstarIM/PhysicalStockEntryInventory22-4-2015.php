<?php
	require("include/include.php");
	require_once('lib/PhysicalStockEntryInventory_ajax.php');
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	false;
	
	$selection 	=	"?pageNo=".$p["pageNo"];

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


	# Add Employee Master Start 
	if ($p["cmdAddNew"]!="") $addMode = true;

	if ($p["cmdCancel"]!="") {
		$addMode  = false;
		$editMode = false;
	}
	

	#Add a Employee Master
	if ($p["cmdAdd"]!="") 
	{
		$supplierStockId		=	$p["supplierStockId"];
		$supplier				=	$p["supplier"];
		$item					=	$p["item"];
		$quantity				=	$p["quantity"];
		$stockDate				=	mysqlDateFormat($p["stockDate"]);
			
		if ($supplier!="") {

			$checkRecord=$physicalStockInventoryObj->checkRecordExist($supplierStockId,$supplier,$item);
			//echo sizeof($checkRecord);
			if(sizeof($checkRecord)>0)
			{
				//echo "hii";	
				$supplierStockQty=$physicalStockInventoryObj->getSupplierQty($supplierStockId,$supplier,$item);
				$diff=$quantity-$supplierStockQty;
				//echo "hii".$diff; die();
				if($diff!='' || $diff=='0')
				{
					$physicalStockRecIns	=	$physicalStockInventoryObj->addPhysicalStockQuantity($supplierStockId,$supplier,$item,$quantity,$stockDate,$userId);
					$lastId = $databaseConnect->getLastInsertedId();
					$supplierIns	=	$physicalStockInventoryObj->addSupplierStock($supplierStockId,$supplier,$item,$diff,$stockDate,$lastId);
					
				}
			}
			else
			{
				$physicalStockRecIns	=	$physicalStockInventoryObj->addPhysicalStockQuantity($supplierStockId,$supplier,$item,$quantity,$stockDate,$userId);
				$lastId = $databaseConnect->getLastInsertedId();
				$supplierIns	=	$physicalStockInventoryObj->addSupplierStock($supplierStockId,$supplier,$item,$quantity,$stockDate,$lastId);
			}
			if ($physicalStockRecIns) {
				$sessObj->createSession("displayMsg", $msg_succAddPhysicalStockInventory);
				$sessObj->createSession("nextPage", $url_afterAddPhysicalStockInventory.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddPhysicalStockInventory;
			}
			$physicalStockRecIns		=	false;
		}
	}
		
	# Edit Employee Master 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$physicalStockRec		=	$physicalStockInventoryObj->find($editId);
		$physicalStockId			=	$physicalStockRec[0];
		$supplierStockId			=	$physicalStockRec[1];
		$supplierId			=$physicalStockRec[2];
		$item			=$physicalStockRec[3];
		$quantity				=$physicalStockRec[4];
		$stockDate	=	dateFormat($physicalStockRec[5]);
		$itemRecs=$physicalStockInventoryObj->getSupplierStock($supplierId);
	}

	#Update
	if ($p["cmdSaveChange"]!="") {
		
		$physicalStockId		=	$p["hidPhysicalStockId"];
		$supplierStockId		=	$p["supplierStockId"];
		$supplier				=	$p["supplier"];
		$item					=	$p["item"];
		$quantity				=	$p["quantity"];
		$stockDate				=	mysqlDateFormat($p["stockDate"]);
		
		if ($physicalStockId!="" && $supplierStockId!="") 
		{
			$supplierStockQty=$physicalStockInventoryObj->getSupplierQty($supplierStockId,$supplier,$item);
			$diff=$quantity-$supplierStockQty;
			//echo "hii".$diff; die();
			if($diff!='' || $diff=='0')
			{
				$physicalStockRecUptd = $physicalStockInventoryObj->updatePhysicalStockQuantity($physicalStockId, $supplierStockId, $supplier,$item, $quantity,$stockDate);
				$supplierIns	=	$physicalStockInventoryObj->addSupplierStock($supplierStockId,$supplier,$item,$diff,$stockDate,$physicalStockId);
			}
		}
	
		if ($physicalStockRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succPhysicalStockInventoryUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdatePhysicalStockInventory.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failPhysicalStockInventoryUpdate;
		}
		$physicalStockRecUptd	=	false;
	}


	# Delete Employee Master
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$physicalStockInventoryId	=	$p["delId_".$i];
			//echo $physicalStockInventoryId; 
			if ($physicalStockInventoryId!="") {
				$physicalStockRec		=	$physicalStockInventoryObj->find($physicalStockInventoryId);
				$supplierStockId		=	$physicalStockRec[1];
				$stockLast=$physicalStockInventoryObj->getsupplierStockLst($supplierStockId);
				if($physicalStockInventoryId>=$stockLast)
				{
					$physicalStockInventoryIdRecDel =	$physicalStockInventoryObj->deletePhysicalStockInventory($physicalStockInventoryId);
					$physicalStockRecDel =	$physicalStockInventoryObj->deleteStockInventory($physicalStockInventoryId);
				}
			}
		}
		if ($physicalStockInventoryIdRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelPhysicalStockInventory);
			$sessObj->createSession("nextPage",$url_afterDelPhysicalStockInventory.$selection);
		} else {
			/*if ($recInUse) $errDel	=	$msg_failDelEmployeeInUse;
			else*/
			$errDel	=	$msg_failDelPhysicalStockInventoryInUse;
		}
		$physicalStockInventoryIdRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$physicalStockId	=	$p["confirmId"];
			if ($physicalStockId!="") {
				// Checking the selected fish is link with any other process
				$physicalStockRecConfirm = $physicalStockInventoryObj->updatePhysicalStockConfirm($physicalStockId);
			}

		}
		if ($physicalStockRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmPhysicalStockInventory);
			$sessObj->createSession("nextPage",$url_afterDelPhysicalStockInventory.$selection);
		} else {
			$errConfirm	=	$msg_failDelPhysicalStockInventory;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$physicalStockId	=	$p["confirmId"];
			if ($physicalStockId!="") {
				#Check any entries exist
				
					$physicalStockRecConfirm = $physicalStockInventoryObj->updatePhysicalStockReConfirm($physicalStockId);
				
			}
		}
		if ($physicalStockRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmPhysicalStockInventory);
			$sessObj->createSession("nextPage",$url_afterDelPhysicalStockInventory.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failDelPhysicalStockInventory;
		}
	}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Employee Master ;
	$physicalStockInventoryRecords	=	$physicalStockInventoryObj->fetchAllPagingRecords($offset, $limit);
	$physicalStockSize		=	sizeof($physicalStockInventoryRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($physicalStockInventoryObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode) 	$heading = $label_editPhysicalStockInventory;
	else 		$heading = $label_addPhysicalStockInventory;
	
	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS	= "libjs/PhysicalStockEntry.js";
	
	# Get all supplier in stock Recs
	$supplierRecs = $physicalStockInventoryObj->getSupplierInStock();
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmPhysicalStockEntryInventory" action="PhysicalStockEntryInventory.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
		<tr><TD height="10"></TD></tr>
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1" ><?=$err;?></td>
		</tr>
		<?}?>
		<tr>
			<td align="center">
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
						<?php	
							$bxHeader = "Physical Stock Entry Inventory";
							include "template/boxTL.php";
						?>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td colspan="3" align="center">
										<Table width="30%">
										<?
											if ( $editMode || $addMode) {
										?>
											<tr>
												<td>
													<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
														<tr>
															<td>
																<!-- Form fields start -->
																<?php							
																	$entryHead = $heading;
																	require("template/rbTop.php");
																?>
																<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
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
																					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('EmployeeMaster.php');">&nbsp;&nbsp;
																					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validatePhysicalStockEntry(document.frmPhysicalStockEntryInventory);">											</td>
																					
																					<?} else{?>

																					
																					<td  colspan="2" align="center">
																					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('EmployeeMaster.php');">&nbsp;&nbsp;
																					<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validatePhysicalStockEntry(document.frmPhysicalStockEntryInventory);">												</td>

																					<?}?>
																				</tr>
																				<input type="hidden" name="hidPhysicalStockId" value="<?=$physicalStockId;?>">
																				<tr>
																					<td colspan="2"  height="10" ></td>
																				</tr>
																				<tr>
																					<td class="fieldName" nowrap >*Supplier </td>
																					<td>
																						<select name="supplier" id="supplier" onchange="xajax_getSupplierStock(this.value);">
																							<option value="">-- Select --</option>
																							<?php
																							foreach ($supplierRecs as $sup) {
																								$suppliersId 	= $sup[0];	
																								$suppliersName	= $sup[1];
																								$selected = ($supplierId==$suppliersId)?"selected":""
																							?>
																							<option value="<?=$suppliersId?>" <?=$selected?>><?=$suppliersName?></option>
																							<?  }?>
																						</select>
																					</td>
																				</tr>
																				<tr>
																					<td class="fieldName" nowrap>*Item</td>
																					<td nowrap>
																						<select name="item" id="item" onchange="xajax_getSupplierStockId(document.getElementById('supplier').value,this.value);">
																						<option value="">-- Select --</option>
																						<?php
																						foreach ($itemRecs as $itm=>$itemValue) {
																							$itemsId 	= $itm;	
																							$itemsName	= $itemValue;
																							$selected = ($item==$itemsId)?"selected":""
																						?>
																						<option value="<?=$itemsId?>" <?=$selected?>><?=$itemsName?></option>
																						<?  }?>
																						</select>
																					</td>
																				</tr>
																				<tr>
																					<td class="fieldName" nowrap>*Quantity</td>
																					
																					<td ><INPUT TYPE="text" NAME="quantity" size="15" value="<?=$quantity;?>">
																						<input type="hidden" name="supplierStockId" id="supplierStockId" value="<?=$supplierStockId?>" /> 
																					</td>
																				</tr>
																				<tr>
																					<td class="fieldName" nowrap>*Date</td>
																					<td><INPUT TYPE="text" id="stockDate"  NAME="stockDate" size="15" value="<?=$stockDate;?>"></td>
																				</tr>
																				<tr>
																					<td colspan="2"  height="10" ></td>
																				</tr>
																				<tr>
																					<? if($editMode){?>

																					<td colspan="2" align="center">
																					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('EmployeeMaster.php');">&nbsp;&nbsp;
																					<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validatePhysicalStockEntry(document.frmPhysicalStockEntryInventory);">												</td>
																					
																					<?} else{?>

																					<td  colspan="2" align="center">
																					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('EmployeeMaster.php');">&nbsp;&nbsp;
																					<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validatePhysicalStockEntry(document.frmPhysicalStockEntryInventory);">												</td>

																					<?}?>
																				</tr>
																				<tr>
																					<td colspan="2"  height="10" ></td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																</table>	
																<?php
																	require("template/rbBottom.php");
																?>
															</td>
														</tr>
													</table>
				<!-- Form fields end   -->		</td>
											</tr>	
											<?
											}
												
												# Listing Employee master Starts
											?>
										</table>
									</td>
								</tr>
								<tr>
									<td height="10" align="center" ></td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td>
													<? if($del==true){?>
													<input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$physicalStockSize;?>);"><? }?>&nbsp;
													<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?>
													<input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPhysicalStockInventory.php',700,600);"><? }?></td>
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
									<td colspan="2" style="padding-left:10px; padding-right:10px;" >
										<table cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
										<?
										if ( sizeof($physicalStockInventoryRecords) > 0 ) {
											$i	=	0;
										?>
											<thead>
											<? if($maxpage>1){?>
												<tr>
													<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
														<div align="right">
														<?php
														$nav  = '';
														for ($page=1; $page<=$maxpage; $page++) {
															if ($page==$pageNo) {
																	$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
															} else {
																	$nav.= " <a href=\"PhysicalStockEntryInventory.php?pageNo=$page\" class=\"link1\">$page</a> ";
																//echo $nav;
															}
														}
														if ($pageNo > 1) {
															$page  = $pageNo - 1;
															$prev  = " <a href=\"PhysicalStockEntryInventory.php?pageNo=$page\"  class=\"link1\"><<</a> ";
														} else {
															$prev  = '&nbsp;'; // we're on page one, don't print previous link
															$first = '&nbsp;'; // nor the first page link
														}

														if ($pageNo < $maxpage) {
															$page = $pageNo + 1;
															$next = " <a href=\"PhysicalStockEntryInventory.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<tr align="center">
													<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></th>
													<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Date </th>
													<th class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</th>
													<th class="listing-head" style="padding-left:10px; padding-right:10px;">Items</th>
													<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Stock Quantity </th>
													
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
											foreach($physicalStockInventoryRecords as $cr) {
													$disabled=="";
												$i++;
												$physicalStockId		=	$cr[0];
												$supplierStockId= $cr[1];
												$supplier		=	stripSlash($cr[6]);
												$item	=	stripSlash($cr[7]);
												$stockDate		=dateFormat($cr[5]);
												$stockQuantity		=	stripSlash($cr[4]); 
												$active=$cr[8];
												$stockLast=$physicalStockInventoryObj->getsupplierStockLst();
												//echo $physicalStockId."----".$stockLast;
												if($physicalStockId == $stockLast)
												{
													$disabled="";
													
												}
												else
												{
													$disabled="disabled";
													//echo "huiii".$disabled;
												}
												
												/* $telephone		=	stripSlash($cr[5]);
												
												 $existingrecords=$cr[7];
													*/
												/* $designationRec=$physicalStockInventoryObj->fetchDesignation($designation);
												 $designationName=$designationRec[1];
												 $departmentRec=$physicalStockInventoryObj->fetchDepartment($department);
												 $departmentName=$departmentRec[1];
												*/
												?>
												<tr ><!-- <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php } ?>>-->
													<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$physicalStockId;?>" <?=$disabled?>></td>
													<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$stockDate;?></td>
													<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$supplier;?></td>
													<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$item;?></td>
													<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$stockQuantity;?></td>
													
												
													
													<? if($edit==true){?>
													<td class="listing-item" width="60" align="center">
													<?php if ($active!=1) {
													?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$physicalStockId;?>,'editId'); this.form.action='PhysicalStockEntryInventory.php';" <?=$disabled?> >
													<?php } ?>
													</td>
													<? }?>
													
													<? if ($confirm==true){?>
													<td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
													<?php 
													if ($confirm==true){	
													if ($active==0){ ?>
														<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$physicalStockId;?>,'confirmId');" >
													<?php } else if ($active==1){ if ($existingrecords==0) {?>
														<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$physicalStockId;?>,'confirmId');" >
													<?php } } }?>
													</td>
													<? }?>

													
												</tr>
												<?
													}
												?>
												<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
												<input type="hidden" name="editId" value="">
												<input type="hidden" name="confirmId" value="">
												<? if($maxpage>1){?>
												<tr>
													<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
														<div align="right">
														<?php
														$nav  = '';
														for ($page=1; $page<=$maxpage; $page++) {
															if ($page==$pageNo) {
																	$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
															} else {
																	$nav.= " <a href=\"PhysicalStockEntryInventory.php?pageNo=$page\" class=\"link1\">$page</a> ";
																//echo $nav;
															}
														}
														if ($pageNo > 1) {
															$page  = $pageNo - 1;
															$prev  = " <a href=\"PhysicalStockEntryInventory.php?pageNo=$page\"  class=\"link1\"><<</a> ";
														} else {
															$prev  = '&nbsp;'; // we're on page one, don't print previous link
															$first = '&nbsp;'; // nor the first page link
														}

														if ($pageNo < $maxpage) {
															$page = $pageNo + 1;
															$next = " <a href=\"PhysicalStockEntryInventory.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
													} else {
												?>
												<tr>
													<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
												</tr>	
												<?
													}
												?>
											</tbody>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$physicalStockSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPhysicalStockInventory.php',700,600);"><? }?></td>
											</tr>
										</table>									
									</td>
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
					<!-- Form fields end   -->			
			</td>
		</tr>	
			
		<tr>
			<td height="10"></td>
		</tr>
	</table>
</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
<script>
Calendar.setup 
(	
	{
		inputField  : "stockDate",         // ID of the input field
		eventName	  : "click",	    // name of event
		button : "stockDate", 
		ifFormat    : "%d/%m/%Y",    // the date format
		singleClick : true,
		step : 1
	}
);
</script>