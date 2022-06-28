<?php
	$insideIFrame = "Y";
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;
	
	$selection 	= "?pageNo=".$p["pageNo"]."&selFilter=".$p["selFilter"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPageIFrame.php");
		//header("Location: ErrorPage.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/

	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;		
	if ($p["cmdCancel"]!="") {
		$addMode  = false;
		$editMode = false;	
	}

	# Value Re-setting	
	if ($p["name"]!="") $name = $p["name"];
	if ($p["address"]!="") $address = $p["address"];
		
	# Add a Record
	if ($p["cmdAdd"]!="") {
		$code		= "TM_".autoGenNum();  // Transporter Master		
		$name		= addSlash(trim($p["name"]));		
		$address	= addSlash(trim($p["address"]));		
		$pinCode	= addSlash(trim($p["pinCode"]));
		$telNo		= addSlash(trim($p["telNo"]));
		$faxNo		= addSlash(trim($p["faxNo"]));
		$mobNo		= addSlash(trim($p["mobNo"]));
		$serviceTaxNo	= addSlash(trim($p["serviceTaxNo"]));
		$billNoRequired	= ($p["billNoRequired"]=="")?Y:$p["billNoRequired"]; // Default Y
				
		# Check Duplicate Entry
		$duplicateEntry = $transporterMasterObj->chkDuplicateEntry($name, $cRtCtId);
			
		if ($name!="" && !$duplicateEntry) {
			$transporterRecIns = $transporterMasterObj->addTransporter($code, $name, $address, $pinCode, $telNo, $faxNo, $mobNo, $serviceTaxNo, $userId, $billNoRequired);

			if ($transporterRecIns) {
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddTransporterMaster);
				$sessObj->createSession("nextPage",$url_afterAddTransporterMaster.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddTransporterMaster;
			}
			$transporterRecIns		=	false;
		} else {
			$addMode	=	true;
			$err		=	$msg_failAddTransporterMaster;
		}
	}

	# Edit a Record
	if ($p["editId"]!="") {
		$editId			= $p["editId"];
		$editMode		= true;
		$transporterRec		= $transporterMasterObj->find($editId);
		$editTransporterId	= $transporterRec[0];
		$code			= stripSlash($transporterRec[1]);
		$name			= stripSlash($transporterRec[2]);		
		$address		= stripSlash($transporterRec[3]);
		$pinCode		= stripSlash($transporterRec[4]);
		$telNo			= stripSlash($transporterRec[5]);
		$faxNo			= stripSlash($transporterRec[6]);
		$mobNo			= stripSlash($transporterRec[7]);
		$serviceTaxNo		= stripSlash($transporterRec[8]);
		$billNoRequiredChk	= ($transporterRec[9]=='N')?"checked":"";
	}


	# Update a record
	if ($p["cmdSaveChange"]!="") {
		$transporterId = $p["hidTransporterId"];		
		$name		= addSlash(trim($p["name"]));		
		$address	= addSlash(trim($p["address"]));		
		$pinCode	= addSlash(trim($p["pinCode"]));
		$telNo		= addSlash(trim($p["telNo"]));
		$faxNo		= addSlash(trim($p["faxNo"]));
		$mobNo		= addSlash(trim($p["mobNo"]));
		$serviceTaxNo	= addSlash(trim($p["serviceTaxNo"]));
		$billNoRequired	= ($p["billNoRequired"]=="")?Y:$p["billNoRequired"]; // Default Y
		
		# Check Duplicate Entry
		$duplicateEntry = $transporterMasterObj->chkDuplicateEntry($name, $transporterId);	

		if ($transporterId!="" && $name!="" && !$duplicateEntry) {
			$transporterRecUptd = $transporterMasterObj->updateTransporter($transporterId, $name, $address, $pinCode, $telNo, $faxNo, $mobNo, $serviceTaxNo, $billNoRequired);
		}
	
		if ($transporterRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succTransporterMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateTransporterMaster.$selection);
		} else {
			$editMode	=	true;
			$err		=	$msg_failTransporterMasterUpdate;
		}
		$transporterRecUptd	=	false;
	}

	# Delete a Record
	if ($p["cmdDelete"]!="") {

		$rowCount = $p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$transporterId = $p["delId_".$i];

			if ($transporterId!="") {
				# Check Retail Counter in use
				$transporterInUse = $transporterMasterObj->transporterInUse($transporterId);
				if (!$transporterInUse) {
					# Need to check the selected Category is link with any other process
					$transporterRecDel = $transporterMasterObj->deleteTransporter($transporterId);
				} 
			}
		}
		if ($transporterRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelTransporterMaster);
			$sessObj->createSession("nextPage",$url_afterDelTransporterMaster.$selection);
		} else {
			if ($transporterInUse) $errDel	= $msg_failDelTransporterMaster."<br>The selected transporter is in use. <br><span style='font-size:9px;'>Please make sure the Transporter does not exist in Transporter Rate List/ Other Charges/ Rate Master/ Management/ Sales Order section</span>";
			else $errDel	=	$msg_failDelTransporterMaster;
		}
		$transporterRecDel	=	false;
	}
if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$transporterId	=	$p["confirmId"];
			if ($transporterId!="") {
				// Checking the selected fish is link with any other process
				$transporterRecConfirm = $transporterMasterObj->updateTransporterconfirm($transporterId);
			}

		}
		if ($transporterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmtransporter);
			$sessObj->createSession("nextPage",$url_afterDelTransporterMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$transporterId = $p["confirmId"];
			if ($transporterId!="") {
				#Check any entries exist
				
				$transporterRecConfirm = $transporterMasterObj->updateTransporterReleaseconfirm($transporterId);
				
			}
		}
		if ($transporterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmtransporter);
			$sessObj->createSession("nextPage",$url_afterDelTransporterMaster.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	## -------------- Pagination Settings I -------------------
	if ($p["pageNo"]!="") 		$pageNo = $p["pageNo"];
	else if ($g["pageNo"]!="") 	$pageNo = $g["pageNo"];
	else 				$pageNo = 1;	
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Records
	$transporterRecords 	= $transporterMasterObj->fetchAllPagingRecords($offset, $limit);
	$transporterRecordSize  = sizeof($transporterRecords);

	## -------------- Pagination Settings II -------------------	
	$numrows	=  sizeof($transporterMasterObj->fetchAllRecords());
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	if ($editMode)	$heading =	$label_editTransporterMaster;
	else 		$heading =	$label_addTransporterMaster;
	
	$ON_LOAD_PRINT_JS	= "libjs/TransporterMaster.js";  // Topleft Nav Settings

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmTransporterMaster" action="TransporterMaster.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr><TD height="10"></TD></tr>
		<? if($err!="" ){?>
		<tr>
			<td height="10" align="center" class="err1"><?=$err;?></td>
		</tr>
		<?}?>
	<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?	
					$bxHeader = "Transporter Data";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="65%">
		<?
			if( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="96%">
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
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterMaster.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateTransporterMaster(document.frmTransporterMaster);">	
			</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateTransporterMaster(document.frmTransporterMaster);">				
		</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidTransporterId" value="<?=$editTransporterId;?>">	
	<tr><TD height="10"></TD></tr>
	<tr>
		<TD colspan="2" style="padding-left:5px; padding-right:5px;">
		<table cellspacing="4">
			<TR>
				<TD valign="top">
					<!--<fieldset>-->
					<?php
						$entryHead = "";
						$rbTopWidth = "";
						require("template/rbTop.php");
					?>
					<table>
						<tr>
							<td class="fieldName" nowrap >*Name </td>
							<td><input type="text" name="name" size="20" value="<?=$name;?>"></td>
						</tr>	
						<tr>
							<td class="fieldName" nowrap >Address</td>
							<td><textarea name="address" rows="5" cols="35"><?=$address;?></textarea></td>
						</tr>
						<tr>
							<td class="fieldName" nowrap >Pin Code</td>
							<td><input type="text" name="pinCode" value="<?=$pinCode;?>"></td>
						</tr>						
					</table>
					<?php
						require("template/rbBottom.php");
					?>
					<!--</fieldset>-->
				</TD>
				<td>&nbsp;</td>
				<TD valign="top">
					<!--<fieldset>-->
					<?php
						$entryHead = "";
						$rbTopWidth = "";
						require("template/rbTop.php");
					?>
					<table>
						<tr>
							<td class="fieldName" nowrap >Tel No</td>
							<td><input type="text" name="telNo" value="<?=$telNo;?>"></td>
						</tr>
						<tr>
							<td class="fieldName" nowrap >Mob No</td>
							<td><input type="text" name="mobNo" value="<?=$mobNo;?>"></td>
						</tr>
						<tr>
							<td class="fieldName" nowrap >Fax No</td>
							<td><input type="text" name="faxNo" value="<?=$faxNo;?>"></td>
						</tr>	
						<tr>
							<td class="fieldName" nowrap >Service Tax No.</td>
							<td><input type="text" name="serviceTaxNo" value="<?=$serviceTaxNo;?>"></td>
						</tr>
						<tr>
							<td class="fieldName" nowrap valign="middle">Bill No Not Required</td>
							<td onMouseover="ShowTip('If bill number is not required for settling. Please give tick mark.');" onMouseout="UnTip();">
								<INPUT type="checkbox" name="billNoRequired" id="billNoRequired" class="chkBox" value="N" <?=$billNoRequiredChk?> />
							</td>
						</tr>
					</table>
					<?php
						require("template/rbBottom.php");
					?>
					<!--</fieldset>-->
				</TD>
			</TR>
		</table>
		</TD>
	</tr>
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
		<? if($editMode){?>
		<td colspan="2" align="center">
		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateTransporterMaster(document.frmTransporterMaster);">					
		</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterMaster.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateTransporterMaster(document.frmTransporterMaster);">	
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
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="85%">
					<tr>
						<td>
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
						<tr>
							<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
			<td background="images/heading_bg.gif" class="pageName" nowrap="true" style="background-repeat:repeat-x">&nbsp;Transporter Data</td>
			<td background="images/heading_bg.gif" class="pageName" align="right" nowrap="true" style="background-repeat:repeat-x">
			</td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$transporterRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintTransporterMaster.php',700,600);"><? }?></td>
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
	<td colspan="2" style="padding-left:10px;padding-right:10px;">
	<table cellpadding="1"  width="45%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?
		if ($transporterRecordSize) {
			$i = 0;
	?>
	<thead>
	<? if($maxpage>1){ ?>
		<tr>
		<td colspan="9" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"TransporterMaster.php?pageNo=$page&selFilter=$distFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"TransporterMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"TransporterMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\">>></a> ";
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
			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_'); " class="chkBox">
		</th>		
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Name</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Tel No</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Bill No<br>Required</th>
		<? if($edit==true){?>
		<th class="listing-head" >&nbsp;</th>
		<? }?>
		<? if($confirm==true){?>
		<th class="listing-head" >&nbsp;</th>
		<? }?>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach ($transporterRecords as $tr) {
		$i++;
		$transporterId	= $tr[0];		
		$name 	= stripSlash($tr[2]);	
		$telNo  = stripSlash($tr[5]);
		$billRequired = ($tr[9]=='Y')?"YES":"NO";
		$active=$tr[10];
	?>
<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
	<td width="20">
		<input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$transporterId;?>" class="chkBox">
	</td>	
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$name;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$telNo;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center"><?=$billRequired;?></td>	
	<? if($edit==true){?>
		<td class="listing-item" width="60" align="center"><?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$transporterId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='TransporterMaster.php';"><? } ?></td>
	<? }?>
	 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$transporterId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$transporterId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>
	</tr>
		<?php
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value=""><input type="hidden" name="confirmId" value="">
		<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr>
		<td colspan="9" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"TransporterMaster.php?pageNo=$page&selFilter=$distFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"TransporterMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"TransporterMaster.php?pageNo=$page&selFilter=$distFilterId\"  class=\"link1\">>></a> ";
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
		<td colspan="9"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete **" style="background-color:#ff0000;color: white;"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$transporterRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintTransporterMaster.php',700,600);"><? }?></td>
											</tr>
										</table></td>
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
	<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
	</table>	
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
	//ensureInFrameset(document.frmTransporterMaster);
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