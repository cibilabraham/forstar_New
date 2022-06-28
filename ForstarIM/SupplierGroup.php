<?php
	require("include/include.php");
	require_once("lib/supplierGroup_ajax.php");
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


	# Add Registration Type Start 
	if ($p["cmdAddNew"]!="") $addMode = true;

	if ($p["cmdCancel"]!="") {
		$addMode  = false;
		$editMode = false;
	}
	

	#Add a supplier Group
	if ($p["cmdAdd"]!="") {

		$supplierGroupName		=	addSlash(trim($p["supplierGroupName"]));
		$groupCount				=	$p["hidTableRowCount"];
		
		
		if ($supplierGroupName!="") {
			$supplierGroupRecIns	=	$supplierGroupObj->addSupplierGroup($supplierGroupName,$userId);
			
		if ($supplierGroupRecIns) {  // CHECK HERE
				#Find the Last inserted Id From stock_group Table
				$supplierGroupId = $databaseConnect->getLastInsertedId();
				for ($i=0; $i<$groupCount; $i++) {
					$status = $p["status_".$i];
					if ($status!='N') {
						$supplierName 		= $p["supField_".$i];
						$supplierLocation	= $p["suplocField_".$i];
						$pondName			= $p["pondField_".$i];
						
						
						# Insert New labels
						if ($supplierGroupId && $supplierName!="" && $supplierLocation!="" && $pondName!="") {
							$supplierGroupEntryRecIns = $supplierGroupObj->addSupplierGroupDetails($supplierGroupId, $supplierName, $supplierLocation,$pondName );
						}
					} // Status Conditin Ends here 
				} // Item Row Count Ends Here 
			} // Group Rec Ins Condition Ends Here

			if ($supplierGroupRecIns) {
				$sessObj->createSession("displayMsg", $msg_succAddSupplierGroup);
				$sessObj->createSession("nextPage", $url_afterAddSupplierGroup.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddSupplierGroup;
			}
			$supplierGroupRecIns		=	false;
		}
	}
		
	# EditSupplier group 
	if ($p["editId"]!="" ) {
		$editId			=	$p["editId"];
		$editMode		=	true;
		$supplierGroupRec		=	$supplierGroupObj->find($editId);
		$supplierGroupId			=	$supplierGroupRec[0];
		$supplierGroupName			=	stripSlash($supplierGroupRec[1]);
		$supplierGroupRecs		= $supplierGroupObj->getSupplierData($supplierGroupId);
	}

	#Update
	if ($p["cmdSaveChange"]!="") {
		
		$supplierGroupId		=	$p["hidSupplierGroupId"];
		$supplierGroupName		=	addSlash(trim($p["supplierGroupName"]));
		$supplierGroupTableRowCount	= $p["hidTableRowCount"];
		
		
		if ($supplierGroupId!="" && $supplierGroupName!="") {
			$supplierGroupRecUptd = $supplierGroupObj->updateSupplierGroup($supplierGroupId, $supplierGroupName);
			
			# ----------------------------Test master
			for ($i=0; $i<$supplierGroupTableRowCount; $i++) {
				$status 	 	 = $p["status_".$i];
				$supplierGrpId  		= $p["supplierGroupId_".$i];
				if ($status!='N') {
					$name	= addSlash(trim($p["supField_".$i]));
					$suppLocation = addSlash(trim($p["suplocField_".$i]));
					$suppPond = addSlash(trim($p["pondField_".$i]));
					
					if ($supplierGroupId!="" && $name!=""  && $suppLocation!=""  && $suppPond!="" && $supplierGrpId!="") {
					
						$updateSupplierDataRec = $supplierGroupObj->updateSupplierGroupDetails($supplierGrpId, $name,$suppLocation,$suppPond);
						
					} else if  ($supplierGroupId!="" && $name!=""  && $suppLocation!=""  && $suppPond!="" && $supplierGrpId=="") {	
						
						$detailsIns = $supplierGroupObj->addSupplierGroupDetails($supplierGroupId, $name,$suppLocation,$suppPond);
					}
					//die;
				} // Status Checking End

				if ($status=='N' && $supplierGrpId!="") {
					# Check Test master In use
					/*$testMethodInUse = $rmTestMasterObj->testMethodRecInUse($testMethodId);
					if (!$testMethodInUse)*/ $delSupplierGrpRec = $supplierGroupObj->delSupGroupRec($supplierGrpId);
						
				}
			} // Test Master Loop ends here
		}
	
		if ($supplierGroupRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succSupplierGroupUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateSupplierGroup.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failSupplierGroupUpdate;
		}
		$supplierGroupRecUptd	=	false;
	}


	# Delete Supplier Group
	if ($p["cmdDelete"]!="") {

		$recInUse = false;
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$supplierGroupId	=	$p["delId_".$i];

			if ($supplierGroupId!="") {
				// Need to check the selected supplier group is link with any other section
				$supplierGroupRecInUse = $supplierGroupObj->supplierGroupRecInUse($supplierGroupId);
				if (!$supplierGroupRecInUse) {
					$supplierGroupRecDel	=	$supplierGroupObj->deleteSupplierGroup($supplierGroupId);
					$supplierGroupDetalRecDel	=	$supplierGroupObj->deleteSupplierGroupDetail($supplierGroupId);
				} else {
					$recInUse = true;
				}
			}
		}
		if ($supplierGroupRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelSupplierGroup);
			$sessObj->createSession("nextPage",$url_afterDelSupplierGroup.$selection);
		} else {
			if ($recInUse) $errDel	=	$msg_failDelSupplierGroupInUse;
			else $errDel	=	$msg_failDelSupplierGroup;
		}
		$supplierGroupRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$supplierGroupId	=	$p["confirmId"];
			if ($supplierGroupId!="") {
				// Checking the selected fish is link with any other process
				$supplierGroupRecConfirm = $supplierGroupObj->updateSupplierGroupConfirm($supplierGroupId);
			}

		}
		if ($supplierGroupRecConfirm) {
			
			$sessObj->createSession("displayMsg",$msg_succConfirmSupplierGroup);
			$sessObj->createSession("nextPage",$url_afterDelSupplierGroup.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$supplierGroupId = $p["confirmId"];
			if ($supplierGroupId!="") {
				#Check any entries exist
				
					$rmSupplierGroupRecConfirm = $supplierGroupObj->updateSupplierGroupReleaseconfirm($supplierGroupId);
				
			}
		}
		if ($rmSupplierGroupRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmSupplierGroup);
			$sessObj->createSession("nextPage",$url_afterDelSupplierGroup.$selection);
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

	# List all supplier group ;
	$supplierGroupRecords	=	$supplierGroupObj->fetchAllPagingRecords($offset, $limit);
	$supplierGroupSize		=	sizeof($supplierGroupRecords);

	## -------------- Pagination Settings II -------------------
	$numrows	=  sizeof($supplierGroupObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------

	if ($editMode) 	$heading = $label_editSupplierGroup;
	else 		$heading = $label_addSupplierGroup;
	
	$ON_LOAD_SAJAX 		= "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav
	$ON_LOAD_PRINT_JS	= "libjs/SupplierGroup.js";
	
	# Get all supplier Recs
		$supplierRecs = $supplierMasterObj->fetchAllRMSupplierActive();
	
	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmSupplierGroup" action="SupplierGroup.php" method="post">
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
					$bxHeader = "Manage Supplier Group";
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
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierGroup.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddSupplierGroup(document.frmSupplierGroup);">											</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierGroup.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddSupplierGroup(document.frmSupplierGroup);">												</td>

												<?}?>
											</tr>
											<input type="hidden" name="hidSupplierGroupId" id="hidSupplierGroupId" value="<?=$supplierGroupId;?>">
											<tr>
												<td colspan="2" align="center" style="padding-left:10px; padding-right:10px;" id="divEntryExistTxt" class="err1"></td>
											</tr>
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<td class="fieldName" nowrap >*Supplier Group Name</td>
												<td><INPUT TYPE="text" NAME="supplierGroupName" size="15" value="<?=$supplierGroupName;?>"></td>
											</tr>
											
											
											
											<tr>
											  <td colspan="2" nowrap>
											  <table align="center">
												<tr>
													<TD colspan="2" style="padding-left:5px; padding-right:5px;">
													<table>
														<TR>
															<TD valign="top">
																
															</TD>	
																	<td valign="top">
																		&nbsp;
																	</td>
																	<td valign="top">
																	<!--<fieldset>-->
																	<?php
																		$entryHead = "";
																		$rbTopWidth = "";
																		require("template/rbTop.php");
																	?>
																	<table>
																	<TR>
																		<TD style="padding-left:10px;padding-right:10px;">
																			<table  cellspacing="1" cellpadding="3" id="tblAddSupplierData" class="newspaperType">
																		<tr align="center">
																					<th class="listing-head" nowrap="true" style="text-align:center;">Supplier</th>
																	 <th class="listing-head" nowrap="true" style="text-align:center;">Supplier Location</th> 
																	 <th class="listing-head" nowrap="true" style="text-align:center;">Farm</th>
																	
																	<th>&nbsp;</th>
																			</tr>				
															</table>
																			</TD></TR>
																			<tr><TD height="5"><input type='hidden' name="hidTableRowCount" id="hidTableRowCount" value="<?=$rowSize;?>"></TD></tr>
																			<TR><TD></TD></TR>
																			<tr>
																				<TD style="padding-left:10px;padding-right:10px;" align="left">
																					<a href="###" id='addRow' onclick="javascript:addNewItem();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
																				</TD>
																			</tr>
																	</table>
																	<?php
																		require("template/rbBottom.php");
																	?>
																	<!--</fieldset>-->
																	</td>		
																	
														</TR>
													</table>
													</TD>
												</tr>
												</table></td>
	  </tr>	
											

						
											
		<tr>
			<td colspan="2"  height="10" ></td>
		</tr>
											<tr>
												<? if($editMode){?>

												<td colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierGroup.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAddSupplierGroup(document.frmSupplierGroup);">												</td>
												
												<?} else{?>

												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('SupplierGroup.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAddSupplierGroup(document.frmSupplierGroup);">												</td>

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
			
			# Listing Supplier group Starts
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
	<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplierGroupSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSupplierGroup.php',700,600);"><? }?></td>
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
			if ( sizeof($supplierGroupRecords) > 0 ) {
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
      				$nav.= " <a href=\"SupplierGroup.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SupplierGroup.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SupplierGroup.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier Group Name</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Supplier Location </th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Farm</th>
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
	foreach($supplierGroupRecords as $cr) {
		$i++;
		 $supplierGroupNameId		=	$cr[0];
		 $supplierGroupName		=	stripSlash($cr[1]);
		 $supplierData	=	$supplierGroupObj->getSupplierData($supplierGroupNameId);
		
		 $active=$cr[2];
		$existingrecords=$cr[3];
	?>
	<tr <?php if ($active==0){?> bgcolor="#afddf8"  onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$supplierGroupNameId;?>" ></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$supplierGroupName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
		<?php
			$numLine = 3;
			if (sizeof($supplierData)>0) {
				$nextRec = 0;						
				foreach ($supplierData as $cR) {					
					$supplier = $cR[1];
					$supName=$supplierGroupObj->getSupplierName($supplier);
						$name=$supName[0];					
					$nextRec++;
					if($nextRec>1) echo "<br>"; echo $name;
					if($nextRec%$numLine == 0) echo "<br/>";	
				}
			}
			?>
		
		
		</td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
		<?php
			$numLine = 3;
			if (sizeof($supplierData)>0) {
				$nextRec = 0;						
				foreach ($supplierData as $cR) {					
					$loc= $cR[2];
						$supLocation=$supplierGroupObj->getSupplierLocation($loc);
						$location=$supLocation[0];			
					$nextRec++;
					if($nextRec>1) echo "<br>"; echo $location;
					if($nextRec%$numLine == 0) echo "<br/>";	
				}
			}
			?>
		
		</td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
		<?php
			$numLine = 3;
			if (sizeof($supplierData)>0) {
				$nextRec = 0;						
				foreach ($supplierData as $cR) {					
					$pond = $cR[3];	
						$supPond=$supplierGroupObj->getSupplierPond($pond);
						$supplierPond=$supPond[0];
						$alloteName=$supPond[1];
						$regNumber=$supPond[2];
						$regDate=dateformat($supPond[3]);
						$expDate=dateformat($supPond[4]);
						
						$details="Allotee Name:$alloteName<br>"; 
						$details.="Registration Number:$regNumber<br>";
						$details.="Registration Date:$regDate<br>";
						$details.="Expiry Date:$expDate<br>";
						
						
					$nextRec++;
					if($nextRec>1) echo "<br>";?><a onMouseOver="ShowTip('<?=$details;?>');" onMouseOut="UnTip();"><? echo $supplierPond;
					if($nextRec%$numLine == 0) echo "<br/>";	
				}
			}
			?>
		
		</td>
		
		<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
			<?php if ($active!=1) { ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$supplierGroupNameId;?>,'editId'); this.form.action='SupplierGroup.php';"  ><?php } ?>
		</td>
		<? }?>

		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$supplierGroupNameId;?>,'confirmId');" >
			<?php } else if ($active==1){ if ($existingrecords==0) {?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$supplierGroupNameId;?>,'confirmId');" >
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
      				$nav.= " <a href=\"SupplierGroup.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"SupplierGroup.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"SupplierGroup.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$supplierGroupSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintSupplierGroup.php',700,600);"><? }?></td>
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
		<input type="hidden" name="entryExist" id="entryExist" value="" readonly />
		<tr>
			<td height="10"></td>
		</tr>
	</table>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">	
		function addNewItem()
		{
			addNewItemRow('tblAddSupplierData', '', '', '', '');
		}
	</script>
	<?php
		 if ($addMode) {
	?>
	<SCRIPT LANGUAGE="JavaScript">
		window.load = addNewItem();	
	</SCRIPT>
	<?php
		 }
	?>
	<?php
		if (sizeof($supplierGroupRecs)) {
			$j=0;
			foreach ($supplierGroupRecs as $sge) {
				$suplierDataId = $sge[0];
				$suplierName	 	= $sge[1];
				$suplierLocation		= $sge[2];
				$location1=$supplierGroupObj->filterLocationName($suplierLocation);
				foreach($location1 as $loc)
				//echo $loc[1];
				$suplierPond		= $sge[3];
	?>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">	
		//addNewItemRow('tblAddFieldGroupItem', '<?=$stkGroupEntryId?>', '<?=$stockFieldId?>', '', '<?=$stkFValidation?>', '<?=$selSubCategoryId?>');	
			addNewItemRow('tblAddSupplierData', '<?=$suplierDataId?>', '<?=$suplierName?>', '<?=$suplierLocation?>', '<?=$suplierPond?>');	

			//xajax function
			//xajax_getCompanyUnit('<?=$selbillCompId?>','<?=$j?>','<?=$unitId?>');
			xajax_locationName('<?=$suplierName?>','<?=$suplierLocation?>','<?=$j?>');
			xajax_pondName('<?=$suplierLocation?>','<?=$suplierPond?>','<?=$j?>');
			//xajax_pondName($locationId, $selLocationId,$field)
			//xajax_locationName('532','2','<?=$j?>')

	</script>
	<?php
			$j++;
			}
		}
	?>
	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>