<?php
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$mode		= $g["mode"];
	
	$selection	= "?pageNo=".$p["pageNo"]."&supplierFilter=".$p["supplierFilter"];
	#------------  Checking Access Control Level  ----------------
	$add		= false;
	$edit		= false;
	$del		= false;
	$print		= false;
	$confirm	= false;
	
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
	#----------------------------------------------------------

	# Add New Rate List Start 
	if ($p["cmdAddNew"]!="" || $mode!="") {
		$addMode = true;
	}
	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;	
	}
	
	if ($p["selSupplier"]!="") $selSupplierId = $p["selSupplier"];
	if ($p["rateListName"]!="") $rateListName = $p["rateListName"];
	if ($p["startDate"]!="") $startDate = $p["startDate"];	

	#Insert a Record
	if ($p["cmdAdd"]!="") {	

		$rateListName	=	addSlash(trim($p["rateListName"]));
		$startDate	=	mysqlDateFormat($p["startDate"]);
		$copyRateList	=	$p["copyRateList"];
		$selSupplier	= 	$p["selSupplier"];
		$supplierCurrentRateListId = $p["hidCurrentRateListId"];

		if ($rateListName!="" && $p["startDate"]!="") {

			$vaildDateEntry	=$supplierRateListObj->chkValidDateEntry($startDate,"",$selSupplier); 
			//echo "hii";
			if($vaildDateEntry)
			{
				$supplierRateListRecIns = $supplierRateListObj->addSupplierRateList($rateListName, $startDate, $copyRateList, $selSupplier, $supplierCurrentRateListId);
				if($supplierRateListRecIns) 
				{
					# Update Prev Rate List Rec END DATE
					$sDate		= explode("-",$startDate);
					$endDate  	= date("Y-m-d",mktime(0, 0, 0,$sDate[1],$sDate[2]-1,$sDate[0])); //End Date
					$lastRateListId =$supplierRateListObj->getSupplierRateList($endDate,$selSupplier);
					//echo $lastRateListId;
					//die();
					if ($lastRateListId!="" && $lastRateListId!="0") 
					{
						$updateRateListEndDate = $supplierRateListObj->updateRateListRec($lastRateListId, $endDate);
					}	
				}
			}
			if ($supplierRateListRecIns) 
			{
				$sessObj->createSession("displayMsg",$msg_succAddSupplierRateList);
				$sessObj->createSession("nextPage",$url_afterAddSupplierRateList.$selection);
			} 
			else 
			{
				$addMode		=	true;
				$err			=	$msg_failAddSupplierRateList;
			}
			$supplierRateListRecIns	=	false;
		}
	}
	
	# Edit 
	if ($p["editId"]!="") {
		$editId			= $p["editId"];
		$editMode		= true;
		
		$rateListRec		= $supplierRateListObj->find($editId);
		
		$editRateListId		= $rateListRec[0];
		$rateListName		= stripSlash($rateListRec[1]);
		$startDate		= dateFormat($rateListRec[2]);
		$readOnly		= "readonly";		
	}
	
	
	
	#Update a Record
	if ($p["cmdSaveChange"]!="") {
		
		$supplierRateListId	=	$p["hidRateListId"];
		
		$rateListName		=	addSlash(trim($p["rateListName"]));
		$startDate		=	mysqlDateFormat($p["startDate"]);

		$hidStartDate		=	mysqlDateFormat($p["hidStartDate"]);

		#Latest rate List Id
		$latestRateListId = $supplierRateListObj->latestRateList(); 
		
		if ($supplierRateListId!="" && $rateListName!="") {
			$vaildDateEntry	=$supplierRateListObj->chkValidDateEntry($startDate,$supplierRateListId); 
			
			if($vaildDateEntry)
			{
				$supplierRateListRecUptd = $supplierRateListObj->updateSupplierRateList($rateListName, $startDate, $supplierRateListId, $hidStartDate, $latestRateListId);
			}
		}
	
		if ($supplierRateListRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succUpdateSupplierRateList);
			$sessObj->createSession("nextPage",$url_afterUpdateSupplierRateList.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failUpdateSupplierRateList;
		}
		$supplierRateListRecUptd	=	false;
	}
	

	# Delete a Rec
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$supplierRateListId	=	$p["delId_".$i];
			
			$isRateListUsed = $supplierRateListObj->checkRateListUse($supplierRateListId); 
			
			if ($supplierRateListId!="" && !$isRateListUsed) {
				$supplierRateListRecDel = $supplierRateListObj->deleteSupplierRateList($supplierRateListId);
			}
		}
		if ($supplierRateListRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelSupplierRateList);
			$sessObj->createSession("nextPage",$url_afterDelSupplierRateList.$selection);
		} else {
			$errDel	=	$msg_failDelSupplierRateList;
		}
		$supplierRateListRecDel	=	false;
	}


if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$supplierRateListId	=	$p["confirmId"];
			if ($supplierRateListId!="") {
				// Checking the selected fish is link with any other process
				$supplierRateRecConfirm = $supplierRateListObj->updateSupplierRateListconfirm($supplierRateListId);
			}

		}
		if ($supplierRateRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmsupplierRate);
			$sessObj->createSession("nextPage",$url_afterDelSupplierRateList.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$supplierRateListId = $p["confirmId"];
			if ($supplierRateListId!="") {
				#Check any entries exist
				
					$supplierRateRecConfirm = $supplierRateListObj->updateSupplierRateListReleaseconfirm($supplierRateListId);
				
			}
		}
		if ($supplierRateRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmsupplierRate);
			$sessObj->createSession("nextPage",$url_afterDelSupplierRateList.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	if ($g["supplierFilter"]!="") $supplierFilterId = $g["supplierFilter"];
	else $supplierFilterId = $p["supplierFilter"];	

	
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"]!="") $pageNo=$g["pageNo"];
	else $pageNo=1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------

	# Resettting offset values
	if ($p["hidSupplierFilterId"]!=$p["supplierFilter"]) {		
		$offset = 0;
		$pageNo = 1;		
	}

	#List All Rate List
	$supplierStockRateListRecords		= $supplierRateListObj->fetchAllPagingRecords($offset, $limit, $supplierFilterId);
	$supplierStockRateListRecordsSize	= sizeof($supplierStockRateListRecords);

	## -------------- Pagination Settings II -------------------	
	//$supplierRateListObj->fetchAllRecords();
	$numrows	=  sizeof($supplierRateListObj->fetchAllRecords($supplierFilterId));
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	# List all Supplier
	//$supplierRecords	=	$supplierMasterObj->fetchAllRecordsActivesupplier("INV");
	$supplierRecords	=	$supplierMasterObj->fetchAllRecords("INV");


	if ($selSupplierId!="") {
		$filterSupplierRateListRecords = $supplierRateListObj->fetchAllSupplierRateListRecords($selSupplierId);
		# get Current Rate List of the supplier
		$currentRateListId = $supplierRateListObj->latestRateList($selSupplierId);
	}

	if ($editMode)	$heading	= $label_editSupplierRateList;
	else 		$heading	= $label_addSupplierRateList;
		
	$ON_LOAD_PRINT_JS	= "libjs/SupplierRateList.js";
		
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
<form name="frmSupplierRateList" action="SupplierRateList.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%">	
		<tr>
			<td align="center"><a href="SupplierStock.php" class="link1">Supplier Stock </a></td>
		</tr>
		<? if($err!="" ){?>
			<tr>
				<td height="10" align="center" class="err1" ><?=$err;?></td>
			</tr>
		<?}?>
		<tr> 
			<td align="center" class="listing-item" style="color:Maroon;" height="<?=(!$supplierFilterId)?20:10?>">
				<?php
				if (!$supplierFilterId) {
				?>
				<strong>Latest Supplier Rate List.</strong>
				<?php
				}
				?>
			</td>
		</tr>
		<tr>
			<td align="center">
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
						<?php	
							$bxHeader = "Supplier Rate List Master";
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
																<!--<tr>
																	<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
																	<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;<?//=$heading;?></td>
																</tr>-->
																	<tr>
																		<td width="1" ></td>
																		<td colspan="2" >
																			<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
																				<tr>
																					<td colspan="2" height="10" ></td>
																				</tr>
																				<tr>
																					<? if($editMode){?>

																					<td colspan="2" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierRateList.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSupplierRateList(document.frmSupplierRateList);">	</td>
																					<?} else{?>
																					<td  colspan="2" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierRateList.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSupplierRateList(document.frmSupplierRateList);">
																						<input type="hidden" name="cmdAddNew" value="1">	
																					</td>
																					<?}?>
																				</tr>
																				<input type="hidden" name="hidRateListId" value="<?=$editRateListId;?>">
																				<tr>
																					<td colspan="2"  height="10" ></td>
																				</tr>
																				<tr>
																					<td colspan="2" align="center">
																						<table>
																							<tr>
																								<td class="fieldName" nowrap align='right'>*Name</td>
																								<td><INPUT NAME="rateListName" TYPE="text" id="rateListName" value="<?=$rateListName;?>" size="20"></td>
																							</tr>
																							<tr>
																								<td class="fieldName" nowrap align='right'>*Start Date</td>
																								<td>
																									<INPUT NAME="startDate" TYPE="text" id="startDate" value="<?=$startDate;?>" size="8" <?=$readOnly?>>
																									<input type="hidden" name="hidStartDate" id="hidStartDate" value="<?=$startDate?>">
																								</td>
																							</tr>
																							<? if($addMode==true) { ?>
																							<tr>
																								<TD class="fieldName" nowrap align='right'>*Supplier</TD>
																								<td>
																									<select name="selSupplier" id="selSupplier" onchange="this.form.submit();">
																										<option value="">--Select--</option>
																										<?php
																										foreach($supplierRecords as $sr) {
																											$supplierId	=	$sr[0];				
																											$supplierName	=	stripSlash($sr[2]);
																											$selected = ($selSupplierId==$supplierId)?"selected":"";
																										?>
																										<option value="<?=$supplierId?>" <?=$selected;?>><?=$supplierName?></option>
																										<? }?>
																									</select>
																								</td>
																							</tr>
																							<? 
																							}
																							?>
																							<? if ($addMode==true){ ?>
																							<tr>
																								<td class="fieldName" nowrap align='right'>*Copy From</td>
																								<td>
																									<select name="copyRateList" id="copyRateList">
																										<option value="">-- Select --</option>
																										<?
																										foreach($filterSupplierRateListRecords as $prl) {
																										$supplierRateListId	=	$prl[0];
																										$rateListName		=	stripSlash($prl[1]);
																										$startDate		=	dateFormat($prl[2]);
																										$displayRateList = $rateListName."&nbsp;(".$startDate.")";
																										$selected = ($currentRateListId==$supplierRateListId)?"Selected":"";
																										?>
																										<option value="<?=$supplierRateListId?>" <?=$selected?>><?=$displayRateList?>
																										</option>
																										<? }?>
																									</select>
																								</td>
																							</tr>
																							<? }?>		
																						</table>
																					</td>
																				</tr>
																				<tr>
																					<td colspan="2"  height="10" ></td>
																				</tr>
																				<tr>
																					<? if($editMode){?>
																					<td colspan="2" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierRateList.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateSupplierRateList(document.frmSupplierRateList);">												
																					</td>
																					<?} else{?>
																					<td  colspan="2" align="center">
																						<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierRateList.php');">&nbsp;&nbsp;
																						<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateSupplierRateList(document.frmSupplierRateList);">												
																					</td>
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
													<!-- Form fields end   -->	
												</td>
											</tr>	
											<?
												}
												
												# Listing Grade Starts
											?>
										</table>
									</td>
								</tr>
								<tr>
									<td height="10" align="center" ></td>
								</tr>
								<tr>
									<td colspan="3" align="center">
										<table width="20%">
											<TR>
												<TD>
												<?php			
													$entryHead = "";
													require("template/rbTop.php");
												?>
													<table cellpadding="4" cellspacing="4">
														<tr>
															<td nowrap="nowrap" style="padding:5px;">
																<table cellpadding="0" cellspacing="0">
                													<tr>
																		<td class="listing-item">Supplier&nbsp;</td>
																		<td>
																			<select name="supplierFilter" onchange="this.form.submit();">
																				<option value="">--Select All--</option>
																				<?php
																				foreach($supplierRecords as $sr) {
																					$supplierId	= $sr[0];
																					$supplierName	= stripSlash($sr[2]);
																					$selected = ($supplierFilterId==$supplierId)?"selected":"";
																				?>
																				<option value="<?=$supplierId?>" <?=$selected?>><?=$supplierName?></option>
																				<? }?>
																			</select> 
																		</td>
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
									</td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplierStockRateListRecordsSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintSupplierRateList.php?supplierFilter=<?=$supplierFilterId?>',700,600);"><? }?></td>
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
										<table cellpadding="1"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
											 <?php
											if ( sizeof($supplierStockRateListRecords) > 0 ) {
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
																		$nav.= " <a href=\"SupplierRateList.php?pageNo=$page&supplierFilter=$supplierFilterId\" class=\"link1\">$page</a> ";
																	//echo $nav;
																}
															}
															if ($pageNo > 1) {
																$page  = $pageNo - 1;
																$prev  = " <a href=\"SupplierRateList.php?pageNo=$page&supplierFilter=$supplierFilterId\"  class=\"link1\"><<</a> ";
															} else {
																$prev  = '&nbsp;'; // we're on page one, don't print previous link
																$first = '&nbsp;'; // nor the first page link
															}

															if ($pageNo < $maxpage) {
																$page = $pageNo + 1;
																$next = " <a href=\"SupplierRateList.php?pageNo=$page&supplierFilter=$supplierFilterId\"  class=\"link1\">>></a> ";
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
													<th width="20"><INPUT type='checkbox'  class="chkBox" name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></th>
													<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Name</th>
													<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Start Date </th>
														<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Supplier</th>
													<? if($edit==true){?>
													<th class="listing-head" width="45">&nbsp;</th>
													<? }?>
													<? if($confirm==true){?>	<th class="listing-head"></th><? }?>
												</tr>
											</thead>
											<tbody>
											<?php
											foreach ($supplierStockRateListRecords as $prl) {
											$i++;
											$supplierRateListId	=	$prl[0];
											$rateListName		=	stripSlash($prl[1]);
											$startDate		=	dateFormat($prl[2]);
											$supplierName		= 	$prl[3];
											$active=$prl[4];
											$existingrecords=$prl[5];
											?>
												<tr <?php if ($active==0){?> bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>> 
													<td width="20">
													<?php 
													
													if($existingrecords==0){
													?>
													<input type="checkbox"  class="chkBox"  name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$supplierRateListId;?>" >
													<?php
													}?></td>
													<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$rateListName;?></td>
													<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="center"><?=$startDate?></td>
													<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$supplierName?></td>
													<? if($edit==true){?>
													 <td class="listing-item" align="center"><?php if ($active==0) {?>
													 <input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$supplierRateListId;?>,'editId'); this.form.action='SupplierRateList.php';">
													 <? } ?>
													 </td>
													<?  } ?>
													<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
													
													<?php 
													 if ($confirm==true){	
													if ($active==0){ ?>
													<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$supplierRateListId;?>,'confirmId');" >
													<?php } else if ($active==1){ 
													//if ($existingrecords==0) {?>
													<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$supplierRateListId;?>,'confirmId');" >
													<?php 
													//} 
													}
													}?>
													</td>
												<? }?>
											</tr>
											<?
											}
											?>
											  <input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											  <input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
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
																	$nav.= " <a href=\"SupplierRateList.php?pageNo=$page&supplierFilter=$supplierFilterId\" class=\"link1\">$page</a> ";
																//echo $nav;
															}
														}
														if ($pageNo > 1) {
															$page  = $pageNo - 1;
															$prev  = " <a href=\"SupplierRateList.php?pageNo=$page&supplierFilter=$supplierFilterId\"  class=\"link1\"><<</a> ";
														} else {
															$prev  = '&nbsp;'; // we're on page one, don't print previous link
															$first = '&nbsp;'; // nor the first page link
														}

														if ($pageNo < $maxpage) {
															$page = $pageNo + 1;
															$next = " <a href=\"SupplierRateList.php?pageNo=$page&supplierFilter=$supplierFilterId\"  class=\"link1\">>></a> ";
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
											<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplierStockRateListRecordsSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="return printWindow('PrintSupplierRateList.php?supplierFilter=<?=$supplierFilterId?>',700,600);"><? }?>
											</td>
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
	<tr>
		<td height="10" align="center"><a href="SupplierStock.php" class="link1"> Supplier Stock </a>
		</td>
	</tr>
	<input type="hidden" name="hidSupplierFilterId" value="<?=$supplierFilterId?>">	
	<input type="hidden" name="hidCurrentRateListId" value="<?=$currentRateListId?>">	
	<input type="hidden" name="hidAddMode" id="hidAddMode" value="<?=$addMode?>">	
</table>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "startDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "startDate", 
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