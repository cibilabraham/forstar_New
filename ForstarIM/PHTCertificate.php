<?php
	require("include/include.php");
	require_once("lib/PHTCertificate_ajax.php");
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


	# Add Designation Start 
	if ($p["cmdAddNew"]!="") $addMode = true;

	if ($p["cmdCancel"]!="") {
		$addMode  = false;
		$editMode = false;
	}
	

	#Add a PHT Certificate
	if ($p["cmdAdd"]!="") {

		$PHTCertificateNo		=	$p["PHTCertificateNo"];
		$species		=	addSlash(trim($p["species"]));
		$supplierGroup		=	addSlash(trim($p["supplierGroup"]));
		$supplier		=	addSlash(trim($p["supplier"]));
		$pondName		=	addSlash(trim($p["pondName"]));
		$phtQuantity		=	addSlash(trim($p["phtQuantity"]));
		$dateOfIssue		=	mysqlDateFormat($p["dateOfIssue"]);
		$dateOfExpiry		=	mysqlDateFormat($p["dateOfExpiry"]);
		$receivedDate		=	mysqlDateFormat($p["receivedDate"]);
	
		if ($PHTCertificateNo!="") {
			$phtCertificateRecIns	=	$phtCertificateObj->addPHTCertificate($PHTCertificateNo,$species,$supplierGroup,$supplier,$pondName,$phtQuantity,$dateOfIssue,$dateOfExpiry,$receivedDate, $userId);

			if ($phtCertificateRecIns) {
				$sessObj->createSession("displayMsg", $msg_succAddPHTCertificate);
				$sessObj->createSession("nextPage", $url_afterAddPHTCertificate.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddPHTCertificate;
			}
			$phtCertificateRecIns		=	false;
		}
	}
		
	# Edit pht certificate 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$phtCertificateRec		=	$phtCertificateObj->find($editId);
		$phtCertificateId			=	$phtCertificateRec[0];
		$PHTCertificateNo			=	stripSlash($phtCertificateRec[1]);
		$species			=	stripSlash($phtCertificateRec[2]);
		$supplierGroup			=	stripSlash($phtCertificateRec[3]);
		$supplierRecords=$phtCertificateObj->fetchAllSupplieRecords($supplierGroup);
		$supplier			=	stripSlash($phtCertificateRec[4]);
		$pondRecords=$phtCertificateObj->fetchAllPondRecords($supplier,$supplierGroup);
		$pondName			=	stripSlash($phtCertificateRec[5]);
		$qtyRecords=$phtCertificateObj->fetchQty($pondName);
		$phtQuantity =	stripSlash($phtCertificateRec[6]);
		$dateOfIssue			=	dateFormat($phtCertificateRec[7]);
		$dateOfExpiry			=	dateFormat($phtCertificateRec[8]);
		$receivedDate			=	dateFormat($phtCertificateRec[9]);
		
		
	}

	#Update
	if ($p["cmdSaveChange"]!="") {
		
		$phtCertificateId		=	$p["hidPHTCertificateId"];
		$PHTCertificateNo		=	addSlash(trim($p["PHTCertificateNo"]));
		$species		=	addSlash(trim($p["species"]));
		$supplierGroup		=	addSlash(trim($p["supplierGroup"]));
		$supplier		=	addSlash(trim($p["supplier"]));
		$pondName		=	addSlash(trim($p["pondName"]));
		$phtQuantity		=	addSlash(trim($p["phtQuantity"]));
		$dateOfIssue		=	mysqlDateFormat($p["dateOfIssue"]);
		$dateOfExpiry		=	mysqlDateFormat($p["dateOfExpiry"]);
		$receivedDate		=	mysqlDateFormat($p["receivedDate"]);
		
		if ($phtCertificateId!="" && $PHTCertificateNo!="" && $species!="" && $supplierGroup!="" && $supplier!="" && $pondName!="" && $phtQuantity!="" && $dateOfIssue!="" && $dateOfExpiry!="" && $receivedDate!="") {
			$phtCertificateRecUptd = $phtCertificateObj->updatePHTCertificate($phtCertificateId, $PHTCertificateNo, $species, $supplierGroup, $supplier, $pondName,$phtQuantity, $dateOfIssue, $dateOfExpiry, $receivedDate);
		}
	
		if ($phtCertificateRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succPHTCertificateUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdatePHTCertificate.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failPHTCertificateUpdate;
		}
		$phtCertificateRecUptd	=	false;
	}


	# Delete PHT Certificate
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$phtCertificateId	=	$p["delId_".$i];

			if ($phtCertificateId!="") {
				// Need to check the selected Department is link with any other process
				$phtCertificateDel	=	$phtCertificateObj->deletePhtCertificate($phtCertificateId);
				$phtCertificateObj->deletePhtMonitoring($phtCertificateId);
			}
		}
		if ($phtCertificateDel) {
			$sessObj->createSession("displayMsg",$msg_succDelPHTCertificate);
			$sessObj->createSession("nextPage",$url_afterDelPHTCertificate.$selection);
		} else {
			$errDel	=	$msg_failDelPHTCertificate;
		}
		$phtCertificateDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		//for ($i=1; $i<=$rowCount; $i++) {
			$phtCertificateId	=	$p["confirmId"];
			if($phtCertificateId!="") 
			{ 
				$certificateDetail=$phtCertificateObj->find($phtCertificateId);
				$dateOfIssue=$certificateDetail[7];
				$supplier=$certificateDetail[4];
				$supplierGroupName=$certificateDetail[3];
				$specious=$certificateDetail[2];
				$supplyQty=$certificateDetail[6];
				$phtMonitoringIns	=	$phtMonitorngObj->addPHTMonitoringData($phtCertificateId,$dateOfIssue,$supplier, $supplierGroupName, $specious,$supplyQty,$userId);
				//die();
				// Checking the selected fish is link with any other process
				$phtCerticicateRecConfirm = $phtCertificateObj->updatePHTCertificateconfirm($phtCertificateId);
				
			}

		//}
		if ($phtCerticicateRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmPHTCertificte);
			$sessObj->createSession("nextPage",$url_afterDelPHTCertificate.$selection);
		} 
		else
		{
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {

			$phtCertificateId = $p["confirmId"];
			if ($phtCertificateId!="") {
				#Check any entries exist
				
					$phtCerticicateRecConfirm = $phtCertificateObj->updatePhtCerticicateReleaseconfirm($phtCertificateId);
					$phtCertificateObj->deletePhtMonitoring($phtCertificateId);
			}
		}
		if ($phtCerticicateRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmPHTCertificte);
			$sessObj->createSession("nextPage",$url_afterDelPHTCertificate.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
	}
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Registration type ;
	$phtCertificateRecords	=	$phtCertificateObj->fetchAllPagingRecords($offset, $limit);
	$phtCertificateSize		=	sizeof($phtCertificateRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($phtCertificateObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	$speciesRecords	=$fishmasterObj->fetchAllRecordsFishactive();
	$supplierGroupRecords=$rmProcurmentOrderObj->fetchAllSupplierGroupName();
	
	
	
	if ($editMode) 	$heading = $label_editPHTCertificate;
	else 		$heading = $label_addPHTCertificate;
	$ON_LOAD_SAJAX 		= "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav
	$ON_LOAD_PRINT_JS	= "libjs/PHTCertificate.js";
	
	if ($addMode) $mode = 1;
	else if ($editMode) $mode = 2;
	else $mode = "";
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmPHTCertificate" action="PHTCertificate.php" method="post">
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
					$bxHeader = "PHT Certificate";
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
										<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PHTCertificate.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddPHTCertificate(document.frmPHTCertificate);">											</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PHTCertificate.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddPHTCertificate(document.frmPHTCertificate);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidPHTCertificateId" value="<?=$phtCertificateId;?>">
											<tr><TD nowrap><span class="fieldName" style="color:red; line-height:normal" id="requestNumExistTxt"></span></TD></tr>
		<tr>
			<td colspan="2" nowrap class="fieldName" >
		<table width="200" align="center">
			<tr>
				<td colspan="2"  align="center">
				<?php							
								$entryHead = "";
								require("template/rbTop.php");
							?>
					<table cellpadding="0"  cellspacing="1" border="0"   width="100%"  align="center">
						<tr><td class="fieldName" nowrap >*PHT Certificate No</td>
							<td class="listing-item"><INPUT TYPE="text" NAME="PHTCertificateNo" id="PHTCertificateNo" size="10" value="<?=$PHTCertificateNo;?>" tabindex="1" onchange="xajax_checkPHTCertificateExist(document.getElementById('PHTCertificateNo').value, '<?=$PHTCertificateNo?>', <?=$mode?>);"></td>			
						</tr>
						<tr><td class="fieldName" nowrap >*Species</td>
												<td  height="10" ><select name="species">
											  <option value="">--select--</option>
											  <?
												foreach($speciesRecords as $speciousValue)
													{
														$speciousId		=	$speciousValue[0];
														$speciousName	=	stripSlash($speciousValue[1]);
															$selected = ($species==$speciousId)?"selected":""
														/*$selected	=	"";
														if( $supplierId == $editCategoryId){
																$selected	=	"selected";
														}*/
											?>
											  <option value="<?=$speciousId?>" <?=$selected?>><?=$speciousName?></option>
											  <? }?>
											  
											 
										        </select>										      </td>
						</tr>
						<tr>
						<td class="fieldName" nowrap >*PHT Quantity</td>
												<td class="listing-item">
													<INPUT TYPE="text" NAME="phtQuantity" id="phtQuantity" size="10" value="<?=$phtQuantity;?>" tabindex="1">
																
													
															  </td>
						 </tr>
					</table>
					<?php
								require("template/rbBottom.php");
							?>
				</td>
				<td colspan="2"  align="center">
				<?php							
								$entryHead = "";
								require("template/rbTop.php");
				?>
					<table cellpadding="0"  cellspacing="1" border="0"  width="100%"  align="center">
						<tr>
							<td class="fieldName" nowrap >*Supplier Group</td>
												<td  height="10" ><select name="supplierGroup" id="supplierGroup"  onchange="xajax_supplierName(document.getElementById('supplierGroup').value,'');">
											  <option value="">--select--</option>
											  <?php 
													foreach($supplierGroupRecords as $sp)
													{
														$supplierGroupId		=	$sp[0];
														$supplierGroupName	=	stripSlash($sp[1]);
														//$selected="";
														
															$selected = ($supplierGroup==$supplierGroupId)?"selected":""
														
								  ?>
													<option value="<?=$supplierGroupId?>" <?=$selected?>><?=$supplierGroupName?></option>
																<? }
													
													
													?>
										        </select>										      </td>
						</tr>
						<tr>
												
												<td class="fieldName" nowrap >*Supplier</td>
												<td class="listing-item"><select name="supplier" id="supplier" onchange="xajax_pondName(document.getElementById('supplier').value,document.getElementById('supplierGroup').value,'');">
													<option value="">--select--</option>
													 <?php 
													foreach($supplierRecords as $spl)
													{
														echo $supplierId		=	$spl[1];
														echo $supplierName	=	$spl[0];
														$selected = ($supplier==$supplierId)?"selected":""
								  ?>
													<option value="<?=$supplierId?>" <?=$selected?>><?=$supplierName?></option>
																<? }
													
													
													?>
																
													
															  </select></td>
						</tr>
						<tr>
												<td class="fieldName" nowrap >*Farm Name</td>
												<td class="listing-item"><select name="pondName" id="pondName">
													<option value="">--select--</option>
													 <?php 
													foreach($pondRecords as $sp)
													{
														$pondNameId		=	$sp[0];
														$pond	=	stripSlash($sp[1]);
															$selected = ($pondName==$pondNameId)?"selected":""
								  ?>
													<option value="<?=$pondNameId?>" <?=$selected?>><?=$pond?></option>
																<? }
													
													
													?>
																
													
															  </select></td>
															  
															  
							</tr>
					</table>
					<?php
								require("template/rbBottom.php");
					?>
				</td>
			</tr>
<tr><td colspan="2" height="10"></td></tr>			
			<tr align="center" style="width:100px; float:right">
			<td colspan="2" >
			<?php							
								$entryHead = "";
								require("template/rbTop.php");
				?>
					<table cellpadding="0"  cellspacing="1" border="0"  width="65%" align="center">
					<tr>
						<td class="fieldName" nowrap >*Date of Issue</td>
						<td><input type="text" name="dateOfIssue" id="dateOfIssue" size="9" value="<?=$dateOfIssue;?>" autocomplete="off" /></td>
					</tr>						
					<tr>	
						<td class="fieldName" nowrap >*Date of Expiry</td>
						<td><input type="text" name="dateOfExpiry" id="dateOfExpiry" size="9" value="<?=$dateOfExpiry;?>" autocomplete="off" /></td>
					</tr>
					<tr>
						<td class="fieldName" nowrap >*Received Date</td>
						<td><input type="text" name="receivedDate" id="receivedDate" size="9" value="<?=$receivedDate;?>" autocomplete="off" /></td>
					</tr>
					</table>
					<?php
								require("template/rbBottom.php");
					?>
			</td>
			</tr>			
											
											
											<!--<tr>
												<td class="fieldName" nowrap >*Date of Expiry</td>
												<td><input type="text" name="dateOfExpiry" id="dateOfExpiry" size="9" value="<?=$dateOfExpiry;?>" autocomplete="off" /></td>
											</tr>-->
											
	 </table></td>
					  </tr>
											
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PHTCertificate.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddPHTCertificate(document.frmPHTCertificate);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('PHTCertificate.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddPHTCertificate(document.frmPHTCertificate);">												</td>

												<?}?>
											</tr>
											<tr>
												<td colspan="2"  height="10" ></td>
											</tr>
										</table>									</td>
								</tr>
							</table>	
							<?php
								require("template/rbBottom.php");
							?>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		<?
			}
			
			# Listing Registration Type Starts
		?>
	</table>
	</td>
	</tr>
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>
			<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Manage Department </td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
	<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$phtCertificateSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPHTCertificate.php',700,600);"><? }?></td>
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
									<td colspan="2" style="padding-left:10px; padding-right:10px;" >
		<table cellpadding="1"  width="50%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
			if ( sizeof($phtCertificateRecords) > 0 ) {
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
      				$nav.= " <a href=\"PHTCertificate.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PHTCertificate.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PHTCertificate.php?pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
	</tr>
	<? }?>
	<tr align="center">
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " ></th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">PHT Certificate No</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Species</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier Group</th>
		<!--<th class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Farm Name</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">PHT Quantity</th>-->
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Date Of Issue</th>
		
		<!--<th class="listing-head" style="padding-left:10px; padding-right:10px;">date of Expiry</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Received Date</th>-->
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Available</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Used</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Balance</th>
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
	foreach($phtCertificateRecords as $cr) {
		$i++;
		 $phtCertificateId		=	$cr[0];
		 $phtCertificateNo		=	stripSlash($cr[1]);
		 $speciesId		=	stripSlash($cr[2]);
		 $speciesName=$phtCertificateObj->getSpeciousName($speciesId);
		 $species=$speciesName[0];
		 
		 $supplierGroupId		=	stripSlash($cr[3]);
		 $supplierGroupName=$phtCertificateObj->getSupplierGroupName($supplierGroupId);
		 $supplierGroup=$supplierGroupName[0];
		 
		 $supplierId		=	stripSlash($cr[4]);
		 $supplierName=$phtCertificateObj->getSupplierName($supplierId);
		 $supplier=$supplierName[0];
		 
		 $pondNameId		=	stripSlash($cr[5]);
		 $pond=$phtCertificateObj->getPondName($pondNameId);
		 $pondName=$pond[0];
		 
		 $phtQuantity		=	stripSlash($cr[6]);
		 //$pondQty=$phtCertificateObj->getPondQty($pondQtyId);
		// $phtQuantity=$pondQty[0];
		 
		 
		 $dateOfIssue		=	dateFormat($cr[7]);
		 $dateOfExpiry		=	dateFormat($cr[8]);
		 $receivedDate		=	dateFormat($cr[9]);
		 
		 $active=$cr[12];
		//$existingrecords=$cr[13];
		
		$todays_date = date("Y-m-d");
		$currentDate = strtotime($todays_date);
		$expiryDate=strtotime($dateOfExpiry);
		$datedetails="Date of Expire:$dateOfExpiry<br>";
		$datedetails.="Received Date:$receivedDate<br>";
		$supplierdetails="Supplier Name:$supplier<br>";
		$supplierdetails.="Farm Name:$pondName<br>";
		$monitoringBalance=$phtCertificateObj->getMonitoringBalance($phtCertificateId);
		
		if($monitoringBalance[0]!='')
		{
			$used=$monitoringBalance[0];
			$disabled="disabled";
		}
		else
		{
			$used="0";
			$disabled="";
		}
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$phtCertificateId;?>" <?=$disabled?>></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$phtCertificateNo;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$species;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><a onMouseOver="ShowTip('<?=$supplierdetails;?>');" onMouseOut="UnTip();"><?=$supplierGroup;?></td>
		<!--<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$supplier?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$pondName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$phtQuantity;?></td> -->
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
		<a onMouseOver="ShowTip('<?=$datedetails;?>');" onMouseOut="UnTip();"><?=$dateOfIssue;?></td>
		<?php /*
		<?php if($expiryDate > $currentDate)
			{  ?>
			<td class="listing-item" style="padding-left:10px; padding-right:10px; background-color:red;"><?=$dateOfExpiry?></td>	
			<?php 		
				} else {?>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$dateOfExpiry;?></td>
		<?php } ?>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$receivedDate;?></td>
		*/?>
		
		
		
		
		<!--<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$phtQuantity;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$used;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$balance=$phtQuantity-$used;?></td>-->
		
		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$phtQuantity?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$used;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$phtQuantity-$used;?></td>
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
			<?php if ($active!=1) {?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$phtCertificateId;?>,'editId'); this.form.action='PHTCertificate.php';"  ><? } ?>
		</td>
		<? }?>

		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$phtCertificateId;?>,'confirmId');" >
			<?php } else if ($active==1){ if ($existingrecords==0) { if($used==0){  ?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$phtCertificateId;?>,'confirmId');" >
			<?php }  } } }?>
			
			
			
			
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
      				$nav.= " <a href=\"PHTCertificate.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"PHTCertificate.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"PHTCertificate.php?pageNo=$page\"  class=\"link1\">>></a> ";
	 	} else {
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}
		// print the navigation link
		$summary = " <span class='listing-item'>(page $pageNo of $maxpage)</span>";
		echo $first . $prev . $nav . $next . $last . $summary; 
	  ?>	
	  <input type="hidden" name="pageNo" value="<?=$pageNo?>"> 
	  </div> </td>
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$phtCertificateSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintPHTCertificate.php',700,600);"><? }?></td>
											</tr>
										</table>									</td>
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
				<!-- Form fields end   -->			</td>
		</tr>	
		
		<tr>
			<td height="10"></td>
		</tr>
	</table>
	
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "dateOfIssue",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateOfIssue", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "dateOfExpiry",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateOfExpiry", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "receivedDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "receivedDate", 
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