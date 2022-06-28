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
		//$supplierStockId		=	$p["supplierStockId"];
		//$supplier				=	$p["supplier"];
		//$item					=	$p["item"];
		//$quantity				=	$p["quantity"];

		$companyName		=	$p["CompanyName"];
		$unit				=	$p["unit"];
		$stockDate				=	mysqlDateFormat($p["stockDate"]);
		$hidStockQuantityRowCount=$p["hidStockQuantityRowCount"];
			
		if ($companyName!="") 
		{
			$chkDuplicate=$physicalStockInventoryObj->checkDuplicate($companyName,$unit,$stockDate);
			if($chkDuplicate)
			{
				$physicalStockRecIns=$physicalStockInventoryObj->addPhysicalStock($companyName,$unit,$stockDate,$userId);
				$lastId = $databaseConnect->getLastInsertedId();
				
				for($i=0; $i<$hidStockQuantityRowCount; $i++)
				{
					$statusUnit=	$p["statusUnit_".$i];
					if($statusUnit!='N')
					{
						$itemId		=	$p["itemId_".$i];
						$supplierId	=	$p["supplierId_".$i];
						$stockQty	=	$p["stockQty_".$i];
						$supplierStockId=$p["supplierStockId_".$i];
						$companyUnitId=$p["companyUnitId_".$i];
						$physicalQuantityIns	=	$physicalStockInventoryObj->addPhysicalStockQty($lastId,$supplierStockId,$itemId,$supplierId,$stockQty);
						//die();
						$supplierStockQty=$physicalStockInventoryObj->getSupplierQty($supplierStockId,$supplierId,$itemId,$companyUnitId);
						//die();
						$diff=$stockQty-$supplierStockQty;
						if($diff!='' || $diff!='0')
						{
							$supplierIns	=	$physicalStockInventoryObj->addSupplierStock($supplierStockId,$supplierId,$itemId,$diff,$stockDate,$lastId,$companyUnitId);
						}
					}
				}
				
				if ($physicalStockRecIns) {
					$sessObj->createSession("displayMsg", $msg_succAddPhysicalStockInventory);
					$sessObj->createSession("nextPage", $url_afterAddPhysicalStockInventory.$selection);
				} 
			}
			else {
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
		$Company_Name			=	$physicalStockRec[1];
		$unit			=$physicalStockRec[2];
		$stockDate	=	dateFormat($physicalStockRec[3]);
		$itemRecs=$physicalStockInventoryObj->getSupplierStockDetail($editId);
		$itemSize=sizeof($itemRecs);
		if($itemSize>1)
		{
			$fieldDisabled="disabled";
		}
		else
		{
			$fieldDisabled="";
		}
	}

	#Update
	if ($p["cmdSaveChange"]!="") {
		$physicalStockId		=	$p["hidPhysicalStockId"];
		$companyName		=	$p["CompanyName"];
		$unit				=	$p["unit"];
		$stockDate				=	mysqlDateFormat($p["stockDate"]);
		$hidStockQuantityRowCount=$p["hidStockQuantityRowCount"];
		
		if($physicalStockId!="")
		{
			$physicalStockRecUptd=$physicalStockInventoryObj->updatePhysicalStock($physicalStockId,$companyName,$unit,$stockDate);

			for($i=0; $i<$hidStockQuantityRowCount; $i++)
			{
				$physicalStockEntry=$p["physicalStockEntry_".$i];
				$statusUnit=	$p["statusUnit_".$i];
				$itemId			 =	$p["itemId_".$i];
				$supplierId		 =	$p["supplierId_".$i];
				$stockQty		 =	$p["stockQty_".$i];
				$supplierStockId =  $p["supplierStockId_".$i];
				$companyUnitId	 =  $p["companyUnitId_".$i];
				//echo $companyName.'--'.$unit.'--'.$itemId;
				//die();
				if($statusUnit!='N' && $physicalStockEntry=="")
				{
					$physicalQuantityIns	=	$physicalStockInventoryObj->addPhysicalStockQty($physicalStockId,$supplierStockId,$itemId,$supplierId,$stockQty);
					//die();
					$supplierStockQty=$physicalStockInventoryObj->getSupplierQty($supplierStockId,$supplierId,$itemId,$companyUnitId);
					//die();
					$diff=$stockQty-$supplierStockQty;
					if($diff!='' || $diff!='0')
					{
						$supplierIns	=	$physicalStockInventoryObj->addSupplierStock($supplierStockId,$supplierId,$itemId,$diff,$stockDate,$physicalStockId,$companyUnitId);
					}
				}
				else if($statusUnit!='N' && $physicalStockEntry!="")
				{
					$physicalQuantityIns	=	$physicalStockInventoryObj->updatePhysicalStockQty($physicalStockEntry,$supplierStockId,$itemId,$supplierId,$stockQty);
					//die();
					$supplierStockQty=$physicalStockInventoryObj->getSupplierQty($supplierStockId,$supplierId,$itemId,$companyUnitId);
					//die();
					$diff=$stockQty-$supplierStockQty;
					if($diff!='' || $diff!='0')
					{
						$supplierIns	=	$physicalStockInventoryObj->addSupplierStock($supplierStockId,$supplierId,$itemId,$diff,$stockDate,$physicalStockId,$companyUnitId);
					}
				}
				if($statusUnit=='N' && $physicalStockEntry!="")
				{
					$physicalStock=$physicalStockInventoryObj->deletePhysicalStockEntryId($physicalStockEntry);
					$diff=-$stockQty;
					$supplierIns	=	$physicalStockInventoryObj->addSupplierStock($supplierStockId,$supplierId,$itemId,$diff,$stockDate,$physicalStockId,$companyUnitId);
				}

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
					$physicalStockIdRecDel =	$physicalStockInventoryObj->deletePhysicalStockInventoryEntry($physicalStockInventoryId);
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

	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	if ($editMode) 	$heading = $label_editPhysicalStockInventory;
	else 		$heading = $label_addPhysicalStockInventory;
	
	list($companyNames,$unitRecords,$departmentRecords,$defaultCompany)= $manageusersObj->getUserReferenceSet($userId); 

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	$ON_LOAD_PRINT_JS	= "libjs/PhysicalStockEntryInventory.js";
	
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
																					<td nowrap="" class="fieldName">*Company Name:&nbsp;</td>
																					<td height="5">
																						<select id="CompanyName" name="CompanyName"  onchange="xajax_getUnit(this.value,'','');" <?=$fieldDisabled?>>
																							<option value="">--select--</option>			<?php
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
																					<td nowrap="" class="fieldName">*Unit:&nbsp;</td>
																					<td height="5">
																						<select id="unit" name="unit" onchange="xajax_getStock(document.getElementById('CompanyName').value,this.value);" <?=$fieldDisabled?>>
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
																					<td class="fieldName" nowrap>*Date</td>
																					<td><INPUT TYPE="text" id="stockDate"  NAME="stockDate" size="15" value="<?=$stockDate;?>"></td>
																				</tr>
																				<tr>
																					<td colspan="4" align="left">
																						<table>
																							<tr>
																								<td>
																									<table width="20%" cellspacing="1" bgcolor="#999999" cellpadding="4" id="tblStockQuantity" name="tblStockQuantity">
																										<tr bgcolor="#f2f2f2" align="center">
																											<td class="listing-head" nowrap>Item</td>
																											<td class="listing-head" nowrap>Supplier</td>
																											<td class="listing-head" nowrap>Quantity</td>
																											<td></td>
																										</tr>

																										<?php 
																							if(sizeof($itemRecs)>0)
																							{	$p=0;
																								foreach($itemRecs as $iR)
																								{
																									$physicalStockEntryId=$iR[0];
																									$itemId=$iR[2];
																									$supplierId=$iR[3];
																									$stockQty=$iR[4];

																									$stockRec = $physicalStockInventoryObj->getStock($Company_Name,$unit);
																									$supplierStockRec = $physicalStockInventoryObj->getSupplier($Company_Name,$unit,$itemId);	
																									$supplierStockId = $physicalStockInventoryObj->getSupplierStockId($supplierId,$itemId);
																									$companyUnitId = $physicalStockInventoryObj->getCompanyUnitId($supplierId,$itemId,$supplierStockId,$Company_Name,$unit);
																							?>

																							<tr id="row_<?=$p?>" class="whiteRow" align="center">
																								<td class="listing-item" align="center">
																									<select id="itemId_<?=$p?>" onchange="xajax_getSupplier(document.getElementById('CompanyName').value,document.getElementById('unit').value,document.getElementById('itemId_<?=$p?>').value,'<?=$p?>0','');" name="itemId_<?=$p?>" disabled>
																									<?
																									foreach($stockRec as $sR=>$srName)
																									{	
																										$sel  = ($itemId==$sR)?"Selected":"";
																									?>	
																										<option value="<?=$sR?>" <?=$sel?>><?=$srName?></option>
																									<?
																									}
																									?>
																									</select>
																								</td>
																								<td class="listing-item" align="center">
																									<select id="supplierId_<?=$p?>" onchange="xajax_getSupplierStockId(this.value,document.getElementById('itemId_<?=$p?>').value,'<?=$p?>',document.getElementById('CompanyName').value,document.getElementById('unit').value);" name="supplierId_<?=$p?>" disabled>
																									<?
																									foreach($supplierStockRec as $sSR=>$ssrName)
																									{
																										$selt  = ($supplierId==$sSR)?"Selected":"";
																									?>
																										<option value="<?=$sSR?>" <?=$selt?>><?=$ssrName?></option>
																									<?
																									}
																									?>
																									</select>
																								</td>
																								<td class="listing-item" align="center">
																									<input id="stockQty_<?=$p?>" type="text" size="5" value="<?=$stockQty?>" name="stockQty_<?=$p?>">
																								</td>
																								<td class="listing-item" align="center">
																									<a onclick="setPOItemStatusUnit('<?=$p?>');" href="###">
																									<img border="0" style="border:none;" src="images/delIcon.gif" title="Click here to remove this item">
																									</a>
																									<input id="statusUnit_<?=$p?>" type="hidden" value="" name="statusUnit_<?=$p?>">
																									<input id="IsFromDB_<?=$p?>" type="hidden" value="N" name="IsFromDB_<?=$p?>">
																									<input id="poEntryId_<?=$p?>" type="hidden" value="" name="poEntryId_<?=$p?>">
																									<input id="supplierStockId_<?=$p?>" type="hidden" value="<?=$supplierStockId?>" name="supplierStockId_<?=$p?>">
																									<input id="companyUnitId_<?=$p?>" type="hidden" value="<?=$companyUnitId?>" name="companyUnitId_<?=$p?>">
																									<input id="physicalStockEntry_<?=$p?>" type="hidden" value="<?=$physicalStockEntryId?>" name="physicalStockEntry_<?=$p?>">
																								</td>
																							</tr>
																							<?
																								$p++;
																								}
																							}
																							?>
																									</table>
																								</td>
																							</tr>
																							


																								<input type='hidden' name="hidStockQuantityRowCount" id="hidStockQuantityRowCount" value="<?=$p?>">
																							<tr>
																								<TD style="padding-left:5px;padding-right:5px;">
																									<a href="###" id='addRow' onclick="javascript:addNewStockQuantity();"  class="link1" title="Click here to duplicate value."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New<!--(Copy)--></a>
																								</TD>
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
													<th class="listing-head" style="padding-left:10px; padding-right:10px;">Company</th>
													<th class="listing-head" style="padding-left:10px; padding-right:10px;">Unit</th>
													<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Stock Detail </th>
													
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
												$stockDate		=dateFormat($cr[3]);
												$company= $cr[4];
												$unit= $cr[5];
												$active=$cr[6];
												$stockQty=$physicalStockInventoryObj->stockQtyDetail($physicalStockId);

											/*	$stockLast=$physicalStockInventoryObj->getsupplierStockLst();
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
												*/
												?>
												<tr ><!-- <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php } ?>>-->
													<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$physicalStockId;?>" <?=$disabled?>></td>
													<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$stockDate;?></td>
													<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$company;?></td>
													<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$unit;?></td>
													<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
														<table cellpadding='1' cellspacing='1' bgcolor="#cccccc">
															<tr bgcolor="#f2f2f2">
																<td class="listing-head">Stock</td>
																<td class="listing-head">Supplier</td>
																<td class="listing-head">Quantity</td>
															</tr>
															<?
															foreach($stockQty as $stk)
															{
																$physicalStckEntryId=$stk[0];
																$stock=$stk[5];
																$supplier=$stk[6];
																$quantity=$stk[4];
															?>
															<tr >
																<td class="listing-item"><?=$stock?></td>
																<td class="listing-item"><?=$supplier?></td>
																<td class="listing-item"><?=$quantity?></td>
															</tr>
															<?
															}
															?>
														</table>
													</td>
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

<?php
	if ($addMode!="" || $editMode!="") 
	{
	?>
	<SCRIPT language="JavaScript">
	function addNewStockQuantity()
	{		
		//alert("entered");
		addNewStock('tblStockQuantity', '', '','','<?=$mode?>');
	}
	</SCRIPT>
	<?
	}
	
	if($selStockId!="" && $addMode)
	{
	?>
	<script>
	//	var com=document.getElementById("companyId_0").value;
			xajax_getCompanyUnit('<?=$selStockId?>');
	</script>
	<?
	}
	 if($addMode)
	{
	?>
		<script>
		window.load = addNewStockQuantity();
		</script>
	<? 
	} 
	if(sizeof($itemRecs)>0)
	{
	?>
		<script>
			fieldIdStock='<? echo sizeof($itemRecs)?>';
		</script>
	<?
	}
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