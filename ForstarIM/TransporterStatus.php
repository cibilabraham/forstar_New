<?php
	$insideIFrame = "Y";
	require("include/include.php");

	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;

	$selection 	= "?pageNo=".$p["pageNo"];
	
	/*-----------  Checking Access Control Level  ----------------*/
	$add	= false;
	$edit	= false;
	$del	= false;
	$print	= false;
	$confirm= false;
	
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
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/

	# Add New
	if ($p["cmdAddNew"]!="") $addMode = true;
		
	if ($p["cmdCancel"]!="") {
		$addMode = false;
		$editMode = false;
	}

	# Add a Record
	if ($p["cmdAdd"]!="") {	
		$selTransporter	= $p["selTransporter"];
		$selectFrom	= mysqlDateFormat($p["selectFrom"]);
		$selectTill	= mysqlDateFormat($p["selectTill"]);
		
		# Checking any entry exist for the selected period
		$chkRecExist   = $transporterStatusObj->chkEntryExist($selTransporter, $selectFrom, $selectTill, '');
		if ($selTransporter!="" && !$chkRecExist) {
			$transporterStatusRecIns = $transporterStatusObj->addTransporterStatus($selTransporter, $selectFrom, $selectTill, $userId);

			if ($transporterStatusRecIns) {
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddTransporterStatus);
				$sessObj->createSession("nextPage",$url_afterAddTransporterStatus.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddTransporterStatus;
			}
			$transporterStatusRecIns		=	false;
		} else {
			if ($chkRecExist) $err  = $msg_failTransporterStatusEntryExist;	
		}
	}

	# Edit a Record
	if ($p["editId"]!="" && $p["cmdCancel"]=="") {
		$editId			= $p["editId"];
		$editMode		= true;
		$transporterStatusRec	= $transporterStatusObj->find($editId);
		$editTransporterStatusId	= $transporterStatusRec[0];
		$selTransporter	= $transporterStatusRec[1];
		$selectFrom	= dateFormat($transporterStatusRec[2]);
		$selectTill	= dateFormat($transporterStatusRec[3]);
	}

	#Update 
	if ($p["cmdSaveChange"]!="") {
		$transporterStatusId	= $p["hidTransporterStatusId"];

		$selTransporter	= $p["selTransporter"];
		$selectFrom	= mysqlDateFormat($p["selectFrom"]);
		$selectTill	= mysqlDateFormat($p["selectTill"]);
		
		# Checking any entry  of scheme exist for the selected period
		$chkRecExist   = $transporterStatusObj->chkEntryExist($selTransporter, $selectFrom, $selectTill, $transporterStatusId);

		if ($selTransporter!="" && !$chkRecExist) {
			$transporterStatusRecUptd = $transporterStatusObj->updateTransporterStatus($transporterStatusId, $selTransporter, $selectFrom, $selectTill);
		}
	
		if ($transporterStatusRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succTransporterStatusUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateTransporterStatus.$selection);
		} else {
			$editMode	=	true;
			if ($chkRecExist) $err  = $msg_failTransporterStatusEntryExist;	
			else $err	= $msg_failTransporterStatusUpdate;
		}
		$transporterStatusRecUptd	=	false;
	}


	# Delete a Record
	if ($p["cmdDelete"]!="") {

		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$transporterStatusId	=	$p["delId_".$i];

			if ($transporterStatusId!="") {
				// Need to check the selected id is link with any other process		
				$transporterStatusRecDel = $transporterStatusObj->deleteTransporterStatus($transporterStatusId);
			}
		}
		if ($transporterStatusRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelTransporterStatus);
			$sessObj->createSession("nextPage",$url_afterDelTransporterStatus.$selection);
		} else {
			$errDel	=	$msg_failDelTransporterStatus;
		}
		$transporterStatusRecDel	=	false;
	}
	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$transporterStatusId	=	$p["confirmId"];
			if ($transporterStatusId!="") {
				// Checking the selected fish is link with any other process
				$transporterStatusRecConfirm = $transporterStatusObj->updatetransporterStatusconfirm($transporterStatusId);
			}

		}
		if ($transporterStatusRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmtransporterStatus);
			$sessObj->createSession("nextPage",$url_afterDelTransporterStatus.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$transporterStatusId = $p["confirmId"];
			if ($transporterStatusId!="") {
				#Check any entries exist
				
					$transporterStatusRecConfirm = $transporterStatusObj->updatetransporterStatusReleaseconfirm($transporterStatusId);
				
			}
		}
		if ($transporterStatusRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmtransporterStatus);
			$sessObj->createSession("nextPage",$url_afterDelTransporterStatus.$selection);
		} else {
			$errReleaseConfirm	=	$msg_failRlConfirm;
		}
		}

	## -------------- Pagination Settings I -------------------
	if 	($p["pageNo"] != "") $pageNo=$p["pageNo"];
	else if ($g["pageNo"] != "") $pageNo=$g["pageNo"];
	else 	$pageNo=1;
	$offset = ($pageNo - 1) * $limit; 
	## ----------------- Pagination Settings I End ------------	

	# List all Records
	$transporterStatusRecords 	= $transporterStatusObj->fetchAllPagingRecords($offset, $limit);
	$transporterStatusRecordSize	= sizeof($transporterStatusRecords);

	## -------------- Pagination Settings II -------------------
	$fetchAllTransporterStatusRecs = $transporterStatusObj->fetchAllRecords();
	$numrows	=  sizeof($fetchAllTransporterStatusRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
	
	if ($addMode || $editMode) {
		# List all Transporter		
		//$transporterRecords	= $transporterMasterObj->fetchAllRecords();
		$transporterRecords	= $transporterMasterObj->fetchAllRecordsActiveTransporter();
	}

	if ($editMode)	$heading = $label_editTransporterStatus;
	else 		$heading = $label_addTransporterStatus;
	
	$ON_LOAD_PRINT_JS	= "libjs/TransporterStatus.js";

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmTransporterStatus" action="TransporterStatus.php" method="post">
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
					$bxHeader = "Transporter Management";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="40%">
		<?php
			if ( $editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%">
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
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterStatus.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateTransporterStatus(document.frmTransporterStatus);">	
			</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterStatus.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateTransporterStatus(document.frmTransporterStatus);">				
		</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidTransporterStatusId" value="<?=$editTransporterStatusId;?>">
	<tr><TD height="10"></TD></tr>	
	<tr>
		<td class="fieldName" nowrap >*Transporter</td>
		<td>
			<select name="selTransporter" id="selTransporter">
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
		</td>
	</tr>		
	<tr>
		<td class="fieldName">*From</td>
		<td>
			<table cellpadding="0" cellspacing="0">
				<TR>
					<TD>
					<?
					if ($p["selectFrom"]!="") 	$selectFrom	= $p["selectFrom"];
					?>
						<input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$selectFrom?>">
					</TD>
					<td class="fieldName" nowrap="true">&nbsp;*To&nbsp;</td>
					<td> 
					<?
						if ($p["selectTill"]!="") 	$selectTill	= $p["selectTill"];
					?>
						<input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$selectTill?>">
					</td>
				</TR>
			</table>			
		</td>
	</tr>	
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
	<tr>
		<? if($editMode){?>
		<td colspan="2" align="center">
		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterStatus.php');">&nbsp;&nbsp;<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateTransporterStatus(document.frmTransporterStatus);">					
		</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterStatus.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateTransporterStatus(document.frmTransporterStatus);">	
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Transporter Management</td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$transporterStatusRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintTransporterStatus.php',700,600);"><? }?></td>
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
									<td colspan="2" style="padding-left:10px; padding-right:10px;">
	<table cellpadding="1"  width="40%" cellspacing="1" border="0" align="center" id="newspaper-b1">
	<?php
		if ($transporterStatusRecordSize) {
			$i = 0;
	?>
	<thead>
	<? if($maxpage>1){ ?>
		<tr>
		<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"TransporterStatus.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"TransporterStatus.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"TransporterStatus.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
		<th width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Transporter</th>	
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Valid From</th>	
		<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Valid To</th>			
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
	foreach ($transporterStatusRecords as $tsr) {
		$i++;
		$transporterStatusId		= $tsr[0];
		$transporterName		= $tsr[4];
		$fromDate	= dateFormat($tsr[2]);
		$tillDate	= dateFormat($tsr[3]);
		$active=$tsr[5];
	?>
	<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$transporterStatusId;?>" class="chkBox"></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$transporterName;?></td>		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center"><?=$fromDate;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="center"><?=$tillDate;?></td>		
		<? if($edit==true){?>
			<td class="listing-item" width="80" align="center">
				<?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$transporterStatusId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='TransporterStatus.php';"><? } ?>
			</td>
		<? }?>


		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$transporterStatusId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$transporterStatusId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>
	</tr>
	<?php	
		}
	?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="<?=$editId?>"><input type="hidden" name="confirmId" value="">
		<input type="hidden" name="editSelectionChange" value="0">
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
      				$nav.= " <a href=\"TransporterStatus.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"TransporterStatus.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"TransporterStatus.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$transporterStatusRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintTransporterStatus.php',700,600);"><? }?></td>
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
	//ensureInFrameset(document.frmTransporterStatus);
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