<?php
	require("include/include.php");
	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= false;

	$selection 	= "?pageNo=".$p["pageNo"];
	$cUserId	= $sessObj->getValue("userId");

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
		$editId	  = "";
	}

	// Value setting
	if ($p["selRetailCounter"]!="") $selRetailCounterId = $p["selRetailCounter"];

	# Add a Record
	if ($p["cmdAdd"]!="") {	
		$selRetailCounter = $p["selRetailCounter"];	
		$disCharge	= $p["disCharge"];
		$disType	= $p["disType"]; // M-month or D-Date		
		if ($disType=='D') {
			$selectFrom	= mysqlDateFormat($p["selectFrom"]);
			$selectTill	= mysqlDateFormat($p["selectTill"]);			
		} else {
			$selectFrom	= "";
			$selectTill	= "";			
		}
		
		# Checking any entry  of assignment exist for the selected period
		$chkRecExist   = $assignRtCtDisChargeObj->chkEntryExist($selRetailCounter, $disType, $selectFrom, $selectTill, '');

		if ($selRetailCounter!="" && $disCharge!="" && !$chkRecExist) {
			$assignRtCtDisChargeRecIns = $assignRtCtDisChargeObj->addRtCtDisChargeAssign($selRetailCounter, $disCharge, $disType, $selectFrom, $selectTill, $cUserId);

			if ($assignRtCtDisChargeRecIns) {
				$addMode	=	false;
				$sessObj->createSession("displayMsg",$msg_succAddAssignRtCtDisCharge);
				$sessObj->createSession("nextPage",$url_afterAddAssignRtCtDisCharge.$selection);
			} else {
				$addMode	=	true;
				$err		=	$msg_failAddAssignRtCtDisCharge;
			}
			$assignRtCtDisChargeRecIns		=	false;
		} else {
			if ($chkRecExist) $err  = $msg_failAssignRtCtDisChargeEntryExist;	
		}
	}

	# Edit a Record
	if ($p["editId"]!="" && $p["cmdCancel"]=="") {
		$editId			= $p["editId"];
		$editMode		= true;
		$assignRtCtDisChargeRec	= $assignRtCtDisChargeObj->find($editId);
		$editAssignRtCtDisChargeId	= $assignRtCtDisChargeRec[0];
		$selRetailCounterId = $assignRtCtDisChargeRec[1];	
		$disCharge	= $assignRtCtDisChargeRec[2];
		$disType	= $assignRtCtDisChargeRec[3]; // M-month or D-Date
		if ($disType=='D') {
			$selectFrom = dateFormat($assignRtCtDisChargeRec[4]);
			$selectTill = dateFormat($assignRtCtDisChargeRec[5]);
		}	
	}

	#Update 
	if ($p["cmdSaveChange"]!="") {
		$assignRtCtDisChargeId	= $p["hidAssignRtCtDisChargeId"];
		
		$selRetailCounter = $p["selRetailCounter"];	
		$disCharge	= $p["disCharge"];
		$disType	= $p["disType"]; // M-month or D-Date		
		if ($disType=='D') {
			$selectFrom	= mysqlDateFormat($p["selectFrom"]);
			$selectTill	= mysqlDateFormat($p["selectTill"]);			
		} else {
			$selectFrom	= "";
			$selectTill	= "";			
		}
		
		# Checking any entry  of assignment exist for the selected period
		$chkRecExist   = $assignRtCtDisChargeObj->chkEntryExist($selRetailCounter, $disType, $selectFrom, $selectTill, $assignRtCtDisChargeId);
		if ($assignRtCtDisChargeId!="" && $selRetailCounter!="" && $disCharge!="" && !$chkRecExist) {
			$assignRtCtDisChargeRecUptd = $assignRtCtDisChargeObj->updateAssignRtCtDisCharge($assignRtCtDisChargeId, $selRetailCounter, $disCharge, $disType, $selectFrom, $selectTill);
		}
	
		if ($assignRtCtDisChargeRecUptd) {
			$sessObj->createSession("displayMsg",$msg_succAssignRtCtDisChargeUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateAssignRtCtDisCharge.$selection);
		} else {
			$editMode	=	true;
			if ($chkRecExist) $err  = $msg_failAssignRtCtDisChargeEntryExist;
			else $err	=	$msg_failAssignRtCtDisChargeUpdate;
		}
		$assignRtCtDisChargeRecUptd	=	false;
	}


	# Delete a Record
	if ($p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$assignRtCtDisChargeId	=	$p["delId_".$i];

			if ($assignRtCtDisChargeId!="") {
				// Need to check the selected Category is link with any other process		
				$assignRtCtDisChargeRecDel = $assignRtCtDisChargeObj->deleteAssignRtCtDisplayCharge($assignRtCtDisChargeId);
			}
		}
		if ($assignRtCtDisChargeRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelAssignRtCtDisCharge);
			$sessObj->createSession("nextPage",$url_afterDelAssignRtCtDisCharge.$selection);
		} else {
			$errDel	=	$msg_failDelAssignRtCtDisCharge;
		}
		$assignRtCtDisChargeRecDel	=	false;
	}
	

	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$assignRtCtDisChargeId	=	$p["confirmId"];


			if ($assignRtCtDisChargeId!="") {
				// Checking the selected fish is link with any other process
				$assignRtCtDisChargeRecConfirm = $assignRtCtDisChargeObj->updateAssignRtCtconfirm($assignRtCtDisChargeId);
			}

		}
		if ($assignRtCtDisChargeRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmassignRtCtDisCharge);
			$sessObj->createSession("nextPage",$url_afterDelAssignRtCtDisCharge.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
	}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$assignRtCtDisChargeId = $p["confirmId"];

			if ($assignRtCtDisChargeId!="") {
				#Check any entries exist
				
					$assignRtCtDisChargeRecConfirm = $assignRtCtDisChargeObj->updateAssignRtCtReleaseconfirm($assignRtCtDisChargeId);
				
			}
		}
		if ($assignRtCtDisChargeRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmassignRtCtDisCharge);
			$sessObj->createSession("nextPage",$url_afterDelAssignRtCtDisCharge.$selection);
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

	# List all Assign Rt Ct Dis Charge
	$assignRtCtDisChargeResultSetObj = $assignRtCtDisChargeObj->fetchAllPagingRecords($offset, $limit);
	$assignRtCtDisChargeRecordSize   = $assignRtCtDisChargeResultSetObj->getNumRows();

	## -------------- Pagination Settings II -------------------
	$fthAllAssignRtCtDisChargeResultSetObj = $assignRtCtDisChargeObj->fetchAllRecords();
	$numrows	=  $fthAllAssignRtCtDisChargeResultSetObj->getNumRows();
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------


	if ($addMode || $editMode ) {		
		#List all Retail Counter
		$retailCounterResultSetObj = $retailCounterMasterObj->fetchAllRecordsActiveRetailCounter('');
	}

	if ($editMode)	$heading = $label_editAssignRtCtDisCharge;
	else 		$heading = $label_addAssignRtCtDisCharge;
	
	$ON_LOAD_PRINT_JS	= "libjs/AssignRtCtDisplayCharge.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmAssignRtCtDisplayCharge" action="AssignRtCtDisplayCharge.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%" >
	
		<tr>
			<td height="10" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		<td>		</tr>
		<?
			if( $editMode || $addMode)
			{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="65%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" nowrap>&nbsp;<?=$heading;?></td>
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
				<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('AssignRtCtDisplayCharge.php');">&nbsp;&nbsp;
				<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAssignRtCtDisChargeMaster(document.frmAssignRtCtDisplayCharge);">	
			</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('AssignRtCtDisplayCharge.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAssignRtCtDisChargeMaster(document.frmAssignRtCtDisplayCharge);">				
		</td>
		<?}?>
		</tr>
		<input type="hidden" name="hidAssignRtCtDisChargeId" value="<?=$editAssignRtCtDisChargeId;?>">
	
	<tr>
					<td nowrap class="fieldName">*Retail Counter</td>
					<td nowrap>
                                        <select name="selRetailCounter">
                                        <option value="">-- Select All --</option>
					<?	
					while ($rc=$retailCounterResultSetObj->getRow()) {
						$retailCounterId	= $rc[0];
						$retailCounterCode 	= stripSlash($rc[1]);
						$retailCounterName 	= stripSlash($rc[2]);	
						$selected = "";
						if ($selRetailCounterId==$retailCounterId) $selected = "selected";	
					?>
                            		<option value="<?=$retailCounterId?>" <?=$selected?>><?=$retailCounterName?></option>
					<? }?>
					</select>
					</td></tr>
	<tr>
		<td class="fieldName" nowrap valign="top">*Display Charge</td>
		<td>
			<table>
				<TR>
					<TD>
						<input type="text" name="disCharge" id="disCharge" value="<?=$disCharge;?>" size="3" style="text-align:right;" autoComplete="off">
					</TD>
					<td class="listing-item">/Per</td>
					<td class="listing-item" nowrap="true"><INPUT type="radio" name="disType" id="disTypeM" class="chkBox" value="M" <? if ($disType=='M') echo "checked";?>>Month</td>				
				</TR>
				<tr>
				
					<td class="listing-item">OR</td>
					<td class="listing-item" nowrap="true"><INPUT type="radio" name="disType" id="disTypeD" class="chkBox" value="D" <? if ($disType=='D') echo "checked";?>>Date</td>
					<td class="listing-item">
					<table cellpadding="0" cellspacing="0">
				<TR>
					<td class="fieldName" nowrap="true">&nbsp;From&nbsp;</td>
					<TD>
					<?
					if ($p["selectFrom"]!="") 	$selectFrom	= $p["selectFrom"];
					?>
						<input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$selectFrom?>" autoComplete="off">
					</TD>
					<td class="fieldName" nowrap="true">&nbsp;To&nbsp;</td>
					<td> 
					<?
						if ($p["selectTill"]!="") 	$selectTill	= $p["selectTill"];
					?>
						<input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$selectTill?>" autoComplete="off">
					</td>
				</TR>
			</table>
					</td>
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
		<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('AssignRtCtDisplayCharge.php');">&nbsp;&nbsp;<input type="submit" name="cmdSaveChange" class="button" value=" Save Changes " onClick="return validateAssignRtCtDisChargeMaster(document.frmAssignRtCtDisplayCharge);">					
		</td>
		<?} else{?>
		<td  colspan="2" align="center">
			<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('AssignRtCtDisplayCharge.php');">&nbsp;&nbsp;<input type="submit" name="cmdAdd" class="button" value=" Add " onClick="return validateAssignRtCtDisChargeMaster(document.frmAssignRtCtDisplayCharge);">	
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
			</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Assign Display Charge Master</td>
								</tr>
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$assignRtCtDisChargeRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintAssignRtCtDisplayCharge.php',700,600);"><? }?></td>
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
	<table cellpadding="1"  width="75%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
		if ($assignRtCtDisChargeRecordSize) {
			$i = 0;
	?>

	<? if($maxpage>1){ ?>
		<tr bgcolor="#FFFFFF">
		<td colspan="5" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"AssignRtCtDisplayCharge.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"AssignRtCtDisplayCharge.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"AssignRtCtDisplayCharge.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	<tr  bgcolor="#f2f2f2" align="center">
		<td width="20"><INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox"></td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Retail Counter</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Dis.Charge<br>(Rs.)</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Period</td>		
		<? if($edit==true){?>
			<td class="listing-head"></td>
		<? }?>
		<? if($confirm==true){?>
			<td class="listing-head"></td>
		<? }?>
	</tr>
	<?	
	while ($adc=$assignRtCtDisChargeResultSetObj->getRow()) {
		$i++;
		$assignRtCtDisChargeId		= $adc[0];
		$retailCounterName		= $adc[6];
		$rtCtDisplayCharge		= $adc[2];
		$disChargeType			= $adc[3];
		$fromDate			= dateFormat($adc[4]);
		$tillDate			= dateFormat($adc[5]);

		$disPeriod	= "";
		if ($disChargeType=='M') {
			$disPeriod = "Per Month";
		}
		else if ($disChargeType=='D') {
			$disPeriod = "From:$fromDate To:$tillDate";
		}
		$active=$adc[7];
		
	?>
	<tr  <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php } else {?> bgcolor="white" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$assignRtCtDisChargeId;?>" class="chkBox"></td>		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$retailCounterName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="right"><?=$rtCtDisplayCharge;?></td>		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$disPeriod;?></td>
		<? if($edit==true){?>
			<td class="listing-item" width="80" align="center">
				<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$assignRtCtDisChargeId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='AssignRtCtDisplayCharge.php';">
			</td>
		<? }?>


		<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php 
			 if ($confirm==true){	
			if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$assignRtCtDisChargeId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?>" name="btnRlConfirm" onClick="assignValue(this.form,<?=$assignRtCtDisChargeId;?>,'confirmId');" >
			<?php } }?>
			
			
			
			
			</td>
												
												
												
												
												<? }?>
	</tr>
	<?
	
		}
	?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="<?=$editId?>"><input type="hidden" name="confirmId" value="">
		<input type="hidden" name="editSelectionChange" value="0">
	<? if($maxpage>1){?>
		<tr bgcolor="#FFFFFF">
		<td colspan="5" align="right" style="padding-right:10px;">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"AssignRtCtDisplayCharge.php?pageNo=$page\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"AssignRtCtDisplayCharge.php?pageNo=$page\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"AssignRtCtDisplayCharge.php?pageNo=$page\"  class=\"link1\">>></a> ";
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
	<tr bgcolor="white">
		<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?
		}
	?>
										</table>									</td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td nowrap><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$assignRtCtDisChargeRecordSize;?>);"><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintAssignRtCtDisplayCharge.php',700,600);"><? }?></td>
											</tr>
										</table></td>
								</tr>
								<tr>
									<td colspan="3" height="5" ></td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		
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