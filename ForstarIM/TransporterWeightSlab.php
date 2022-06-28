<?php
	$insideIFrame = "Y";
	require("include/include.php");
	require_once("lib/TransporterWeightSlab_ajax.php");

	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	$editSelected 	= false;	
	$avgMargin	= "";

	$selection = "?pageNo=".$p["pageNo"]."&transporterFilter=".$p["transporterFilter"]."&transporterRateListFilter=".$p["transporterRateListFilter"];

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
		//header("Location: ErrorPage.php");
		header("Location: ErrorPageIFrame.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/
	
	# Add New Start 
	if ($p["cmdAddNew"]!="" || $g["addMode"]!="") $addMode = true;
	
	if ($p["cmdCancel"]!="") {
		$addMode = false;	
		$editMode = false;				
		$p["editId"] = "";
	}

	#Add a Rec
	if ($p["cmdAdd"]!="") {
		$selTransporter 	= $p["selTransporter"];		
		$rowCount		= $p["hidTableRowCount"]; // Row Count	
				
		if ($selTransporter!="") {			
			$transporterWtSlabRecIns = $transporterWeightSlabObj->addTransporterWtSlab($selTransporter, $userId);

			#Find the Last inserted Id 
			if ($transporterWtSlabRecIns) $trptrWtSlabLastId = $databaseConnect->getLastInsertedId();

			if ($trptrWtSlabLastId!="" && $rowCount>0) {
				for ($i=0; $i<$rowCount; $i++) {
					$status = $p["status_".$i];
					if ($status!='N') {
						$wtSlabId	= $p["weightSlab_".$i];
						if ($trptrWtSlabLastId!="" && $wtSlabId!="") {
							$trptrWiseWtSlabRecIns = $transporterWeightSlabObj->addTrptrWiseWtSlab($trptrWtSlabLastId, $wtSlabId);
						}
					}
				}
			}
		}

		if ($transporterWtSlabRecIns) {
			$addMode = false;
			$sessObj->createSession("displayMsg",$msg_succAddTransporterWeightSlab);
			$sessObj->createSession("nextPage",$url_afterAddTransporterWeightSlab.$selection);
		} else {
			$addMode = true;
			$err	 = $msg_failAddTransporterWeightSlab;
		}
		$transporterWtSlabRecIns = false;
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {	
		$transporterWeightSlabId =	$p["hidTransporterWeightSlabId"];
		
		$selTransporter 	= $p["selTransporter"];		
		$rowCount		= $p["hidTableRowCount"]; // Row Count		

		if ($transporterWeightSlabId!="") {
			# Update Rec 
			//$transporterRecUptd = $transporterWeightSlabObj->updateTransporterWeightSlab($transporterWeightSlabId);
			$trptrWtSlabEntryId = "";
			for ($i=0; $i<$rowCount; $i++) {
				$wtSlabId 		= $p["weightSlab_".$i];
				$trptrWtSlabEntryId	= $p["hidTrptrWtSlabEntryId_".$i];
				$status 		= $p["status_".$i];			    
			    	if ($status!='N') {
					if ($transporterWeightSlabId!="" && $wtSlabId!="" && $trptrWtSlabEntryId=="") {
						$trptrWiseWtSlabRecIns = $transporterWeightSlabObj->addTrptrWiseWtSlab($transporterWeightSlabId, $wtSlabId);
						$transporterRecUptd = true;
					} else if ($transporterWeightSlabId!="" && $wtSlabId!="" && $trptrWtSlabEntryId!="") {
						$updateTrptrWtSlabEntryRec = $transporterWeightSlabObj->updateTransporterWtSlabEntryRec($trptrWtSlabEntryId, $wtSlabId);
						$transporterRecUptd = true;
					}
				} //Status N check ends
				# Delete the state IF Status=N	
			        if ($status=='N' && $trptrWtSlabEntryId!="") {
					$wtSlabEntryInUse = $transporterWeightSlabObj->chkWtSlabExistInTrptrRate($trptrWtSlabEntryId);
					if (!$wtSlabEntryInUse) {
						# Delete Removed Rec
						$delRemovedRec = $transporterWeightSlabObj->delRemovedWtSlabRec($trptrWtSlabEntryId);
						$transporterRecUptd = true;
					}
				}
			} // Foe Loop Ends Here	
		}
	
		if ($transporterRecUptd) {
			$editMode = false;
			$p["editId"] = "";
			$editId = "";
			$sessObj->createSession("displayMsg",$msg_succTransporterWeightSlabUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateTransporterWeightSlab.$selection);	
		} else {
			$editMode	=	true;
			$err		=	$msg_failTransporterWeightSlabUpdate;
		}
		$transporterRecUptd	=	false;
	}


	# Edit  a Record		
	if ($p["editId"]) {
		$editId		= $p["editId"];
		$editMode = true;
		$transporterRateRec	= $transporterWeightSlabObj->find($editId);	
		$editTransporterWeightSlabId  = $transporterRateRec[0];	
		$selTransporter 	= $transporterRateRec[1];

		# Get Wt Slab Records
		$WeightSlabRecs = $transporterWeightSlabObj->getSelWtSlabRecs($editTransporterWeightSlabId);
	
		$disableField		= "disabled";
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		$existCount = 0;
		for ($i=1; $i<=$rowCount; $i++) {

			$transporterWeightSlabId	= $p["delId_".$i];
			if ($transporterWeightSlabId!="") {

				# Check Wt Slab in use in use
				$trptrWtSlabRecInUse = $transporterWeightSlabObj->trptrWtSlabRecInUse($transporterWeightSlabId);
				if (!$trptrWtSlabRecInUse) {	
					// Need to check whether the function is using in any where	
					# Delete Entry Rec					
					$delTrptrWtSlabEntryRec = $transporterWeightSlabObj->deleteTransporterWtSlabEntryRec($transporterWeightSlabId);
					# Delete Main Rec
					if ($delTrptrWtSlabEntryRec) { 
						$transporterWtSlabRecDel = $transporterWeightSlabObj->deleteTransporterWtSlab($transporterWeightSlabId);
					}
					
				}
				if ($trptrWtSlabRecInUse) $existCount++;
			}
		}
		
		if ($transporterWtSlabRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelTransporterWeightSlab);
			$sessObj->createSession("nextPage",$url_afterDelTransporterWeightSlab.$selection);
		} else {
			if ($existCount>0) $errDel	= $msg_failDelTransporterWeightSlab."<br>The selected Transporter Wt slab is linked with Transporter Rate master.";
			else $errDel	=	$msg_failDelTransporterWeightSlab;
		}
		$transporterWtSlabRecDel	=	false;
	}
	

if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$transporterWeightSlabId	=	$p["confirmId"];
			if ($transporterWeightSlabId!="") {
				// Checking the selected fish is link with any other process
				$transporterRecConfirm = $transporterWeightSlabObj->updateTransporterWtSlabconfirm($transporterWeightSlabId);
			}

		}
		if ($transporterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmtransporterWtSlab);
			$sessObj->createSession("nextPage",$url_afterDelTransporterWeightSlab.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$transporterWeightSlabId = $p["confirmId"];
			if ($transporterWeightSlabId!="") {
				#Check any entries exist
				
					$transporterRecConfirm = $transporterWeightSlabObj->updateTransporterWtSlabReleaseconfirm($transporterWeightSlabId);
				
			}
		}
		if ($transporterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmtransporterWtSlab);
			$sessObj->createSession("nextPage",$url_afterDelTransporterWeightSlab.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}
	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else $pageNo=1;
	$offset = ($pageNo-1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Records
	$transporterWeightSlabRecords = $transporterWeightSlabObj->fetchAllPagingRecords($offset, $limit);	
	$transporterWeightSlabRecordSize = sizeof($transporterWeightSlabRecords);	

	## -------------- Pagination Settings II -------------------
	$fetchAllTransporterWeightSlabRecs = $transporterWeightSlabObj->fetchAllRecords();
	$numrows	=  sizeof($fetchAllTransporterWeightSlabRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
						

	if ($addMode || $editMode) {
		# List all Transporter		
		//$transporterRecords	= $transporterMasterObj->fetchAllRecords();
		$transporterRecords	= $transporterMasterObj->fetchAllRecordsActiveTransporter();

		# Get All Wt Slab Records
		$weightSlabRecords = $weightSlabMasterObj->fetchAllRecords();
	}
	# --------------------------------------------------------------------------------------------

	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav		
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/TransporterWeightSlab.js"; 

	#heading Section
	if ($editMode) $heading	=	$label_editTransporterWeightSlab;
	else	       $heading	=	$label_addTransporterWeightSlab;

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmTransporterWeightSlab" action="TransporterWeightSlab.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr><td height="10"></td></tr>	
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
					$bxHeader = "Transporter Wise Weight Slab";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="30%">
		<?php
			if ($editMode || $addMode) {
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
										<table cellpadding="0"  width="85%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

											  <td colspan="2" align="center">
	<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('TransporterWeightSlab.php');">&nbsp;&nbsp;
	<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateTransporterWeightSlab(document.frmTransporterWeightSlab);">
</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterWeightSlab.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateTransporterWeightSlab(document.frmTransporterWeightSlab);">												</td>
					<?}?>
					</tr>
	<input type="hidden" name="hidTransporterWeightSlabId" id="hidTransporterWeightSlabId" value="<?=$editTransporterWeightSlabId;?>">
	<tr><TD height="10"></TD></tr>
					<tr>
					  	<td colspan="2" class="err1" align="center" nowrap style="padding-left:5px;padding-right:5px;" id="divRecExistTxt">
						</td>
					</tr>
					<tr>
					  	<td colspan="2" nowrap style="padding-left:10px;padding-right:10px;" align="center">
					<table width="200">
					<tr>
					<td nowrap class="fieldName">*Transporter</td>
					<td nowrap>
                                        <select name="selTransporter" id="selTransporter" onchange="xajax_trptrWtSlabExist(document.getElementById('selTransporter').value, '<?=$mode?>', '');" <?=$disableField?>>
                                        <option value="">-- Select --</option>
					<?php
						foreach ($transporterRecords as $tr) {
							$transporterId	 = $tr[0];
							$transporterName = stripSlash($tr[2]);	
							$selected =  ($selTransporter==$transporterId)?"selected":"";	
					?>
                            		<option value="<?=$transporterId?>" <?=$selected?>><?=$transporterName?></option>
					<? }?>
					</select>
					<input type="hidden" name="hidDistributor" value="<?=$selTransporter?>">
					</td>	
				
	</tr>		
	</table>
	</td></tr>
	<tr><td id="areaDemarcation" style="padding-left:5px; padding-right:5px;"></td></tr>
	<tr><TD height="5"></TD></tr>
	<!--  Dynamic Row adding starts here-->
	<tr>
		<TD colspan="2" style="padding-left:10px; padding-right:10px;" align="center">
			<table>
				<tr>
					<td style="padding-left:5px; padding-right:5px;">
						<table cellspacing="1" cellpadding="2" id="tblTrptrWeightSlab" class="newspaperType">
						<TR align="center">
							<th class="listing-head" style="padding-left:5px; padding-right:5px; text-align:center;">Weight Slab</th>
							<th>&nbsp;</th>
						</TR>	
						</table>
						<input type="hidden" name="hidTableRowCount" id="hidTableRowCount" value="">
						</td>
					</tr>
					<tr><TD height="5"></TD></tr>
				<tr>
					<TD nowrap style="padding-left:5px; padding-right:5px;">
						<a href="###" id='addRow' onclick="javascript:addNewTrptrWeightSlabRow();"  class="link1" title="Click here to add new item."><img  SRC='images/addIcon.gif' BORDER='0' style='border:none;padding-right:4px;vertical-align:middle;' >Add New Item</a>
					</TD>
				</tr>
			</table>
		</TD>
	</tr>	
	<!--  Dynamic Row Ends starts here-->
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
			<tr>
				<? if($editMode){?>
				<td colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterWeightSlab.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onclick="return validateTransporterWeightSlab(document.frmTransporterWeightSlab);">
				</td>
				<?} else{?>
				<td  colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterWeightSlab.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateTransporterWeightSlab(document.frmTransporterWeightSlab);">							
				</td>
				<input type="hidden" name="cmdAddNew" value="1">
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
			# Listing Category Starts
		?>
	</table>
	</td>
	</tr>
			<tr>
				<td height="10" align="center" ></td>
			</tr>			
			<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
				
					<tr>
						<td>							
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Transporter Wise Weight Slab</td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>

								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$transporterWeightSlabRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintTransporterWeightSlab.php',700,600);"><? }?></td>
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
							<td colspan="2" style="padding-left:5px;padding-right:5px;">
		<table cellpadding="2"  width="55%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
		if ($transporterWeightSlabRecordSize) {
			$i	=	0;
		?>
		<thead>
<? if($maxpage>1){?>
		<tr>
		<td colspan="4" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"TransporterWeightSlab.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"TransporterWeightSlab.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"TransporterWeightSlab.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th width="20">
			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox">
		</th>
		<th class="listing-head" style="padding-left:3px; padding-right:3px;font-size:11px;" >Transporter</th>	
		<th class="listing-head" style="padding-left:3px; padding-right:3px;font-size:11px;" >Weight Slab</th>
		<? if($edit==true){?>
			<th class="listing-head">&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
			<th class="listing-head">&nbsp;</th>
		<? }?>
	</tr>	
	</thead>
	<tbody>
	<?php
		foreach ($transporterWeightSlabRecords as $twsr) {
			$i++;
			$transporterWeightSlabId = $twsr[0];
			$transporterId 		= $twsr[1];

			$transporterName = "";
			if ($prevTransporterId!=$transporterId) {
				$transporterName = $twsr[2];
			}
			
			# Get Wt Slab Records
			$getWtSlabRecs = $transporterWeightSlabObj->getSelWtSlabRecs($transporterWeightSlabId);
			$active=$twsr[3];
			
	?>
	<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$transporterWeightSlabId;?>" class="chkBox"></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" nowrap="true"><?=$transporterName;?></td>			
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" nowrap="true">
			<table id="newspaper-b1-no-style">
				<tr>
				<?
					$numLine = 3;
					if (sizeof($getWtSlabRecs)>0) {
						$nextRec	=	0;
						$k=0;
						$cityName = "";
						foreach ($getWtSlabRecs as $cR) {
							$j++;
							$selName = $cR[2];
							$nextRec++;
				?>
				<td class="listing-item" nowrap="true" style="line-height:normal;">
					<? if($nextRec>1) echo ",";?><?=$selName?></td>
						<? if($nextRec%$numLine == 0) { ?>
				</tr>
				<tr>
				<?php
							}	
					 	}
					}
				?>
				</tr>
		</table>
		</td>				
	<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
			<?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,'<?=$transporterWeightSlabId?>','editId');assignValue(this.form,'1','editSelectionChange');this.form.action='TransporterWeightSlab.php';" ><? } ?>		
		</td>
	<? }?>

		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$transporterWeightSlabId;?>,'confirmId');" >
			<?php } else if ($active==1){ if ($existingcount==0) {?>
			
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$transporterWeightSlabId;?>,'confirmId');" >
			<?php } ?>
			<?php }?>
			<? }?>
			
			
			
			</td>
	</tr>
	<?php
		$prevTransporterId = $transporterId;		
		}
	?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="<?=$editId?>"><input type="hidden" name="confirmId" value="">
		<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr>
		<td colspan="4" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"TransporterWeightSlab.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"TransporterWeightSlab.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"TransporterWeightSlab.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?
		}
	?>
	</tbody>
	</table>
	</td>
	</tr>
	<tr>
		<td colspan="3" height="5"></td>
	</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$transporterWeightSlabRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintTransporterWeightSlab.php',700,600);"><? }?></td>
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
				<!-- Form fields end -->	
		</td>
	</tr>
	<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>">
		<tr>
			<td height="10"></td>
		</tr>
	<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
	</table>
	<?php 
		if ($addMode || $editMode ) {
	?>
		<script language="JavaScript">
			function addNewTrptrWeightSlabRow()
			{
				addNewTransporterWeightSlabRow('tblTrptrWeightSlab', '', '', '');
			}		
		</script>
	<?php 
		}
	?>
	<?php
		if ($addMode) {
	?>
	<script language="JavaScript">
		window.onLoad = addNewTrptrWeightSlabRow();
	</script>
	<?php
		 }
	?>

	<script language="JavaScript" type="text/javascript">	
		<?php	
			if (sizeof($WeightSlabRecs)>0) {		
				$j=0;
				foreach ($WeightSlabRecs as $dsr) {
					$trptrWtSlabEntryId	= $dsr[0];
					$selTransporterId	= $dsr[1];
					$wtSlabEntryInUse = $transporterWeightSlabObj->chkWtSlabExistInTrptrRate($trptrWtSlabEntryId);
		?>	
			addNewTransporterWeightSlabRow('tblTrptrWeightSlab','<?=$selTransporterId?>','<?=$trptrWtSlabEntryId?>', '<?=$wtSlabEntryInUse?>');
		<?php
				$j++;
				}
			}
		?>
	</script>
	<?php 
	if ($iFrameVal=="") { 
	?>
	<script language="javascript">
	<!--
	function ensureInFrameset(form)
	{		
		var pLocation = window.parent.location ;	
		var cLocation = window.location.href;			
		if (pLocation==cLocation) {		// Same Location
			document.getElementById("inIFrame").value = 'N';
			form.submit();		
		} else if (pLocation!=cLocation) { // Not in IFrame
			document.getElementById("inIFrame").value = 'Y';
		}
	}
	//ensureInFrameset(document.frmTransporterWeightSlab);
	//-->
	</script>
<?php 
	}
?>	
	</form>
<?php
	# Include Template [bottomRightNav.php]
	if ($iFrameVal=='N') require("template/bottomRightNav.php");
?>