<?php
	$insideIFrame = "Y";
	require("include/include.php");
	require_once("lib/TransporterRateMaster_ajax.php");
	ob_start();

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
	/*
	$zoneWiseAreaRecs = $transporterRateMasterObj->getZWAreaDemarcationRecs(12);
	echo "<pre>";
	print_r($zoneWiseAreaRecs);
        echo "</pre>";
	*/
	# Add New Start 
	if ($p["cmdAddNew"]!="" || $g["addMode"]!="") $addMode = true;
	
	if ($g["selProduct"]!="")$transporterId = $g["selProduct"];

	if ($p["cmdCancel"]!="") {
		$addMode = false;	
		$editMode = false;				
		$p["editId"] = "";
	}

	$transporterFunctionType = "TRM"; // Transporter Rate Master

	#Add a Rec
	if ($p["cmdAdd"]!="") {
		$selTransporter 	= $p["selTransporter"];
		$selZone 		= $p["selZone"];	
		$transporterRateListId	= $p["transporterRateList"];
		$rowCount		= $p["hidTableRowCount"]; // Row Count
		
				
		# Creating a New Rate List
		if ($transporterRateListId=="") {
			$transporterRec		= $transporterMasterObj->find($selTransporter);
			$transporterName = str_replace (" ",'',$transporterRec[2]);
			$selName 	 = substr($transporterName, 0,9);	
			$rateListName = $selName."-".date("dMy");
			$startDate    = date("Y-m-d");
			$transporterRateListRecIns = $transporterRateListObj->addtransporterRateList($rateListName, $startDate, $cRList, $userId, $selTransporter, $dCurrentRListId, $transporterFunctionType);
			if ($transporterRateListRecIns) $transporterRateListId =$transporterRateListObj->latestRateList($selTransporter, $transporterFunctionType);	
		}
				
		if ($selTransporter!="" && $selZone) {			
			$transporterRateRecIns = $transporterRateMasterObj->addTransporterRate($selTransporter, $selZone, $transporterRateListId, $userId);

			#Find the Last inserted Id From m_transporter_rate
			if ($transporterRateRecIns) $transporterRateLastId = $databaseConnect->getLastInsertedId();

			if ($transporterRateLastId!="" && $rowCount>0) {
				for ($i=1; $i<=$rowCount; $i++) {
					$weightSlabId 	= $p["weightSlabId_".$i];
					$rate		= $p["rate_".$i];
					$trptrWtSlabEntryId = $p["trptrWtSlabEntryId_".$i];
					$trptrRateType		= $p["trptrRateType_".$i];
					
					if ($weightSlabId!="" && $rate!=0) {
						$transporterRateEntryRecIns = $transporterRateMasterObj->addTransporterRateEntryRec($transporterRateLastId, $weightSlabId, $rate, $trptrWtSlabEntryId, $trptrRateType);
					}
				} # For Loop Ends Here
			}
		}

		if ($transporterRateRecIns) {
			$addMode = false;
			$sessObj->createSession("displayMsg",$msg_succAddTransporterRateMaster);
			$sessObj->createSession("nextPage",$url_afterAddTransporterRateMaster.$selection);
		} else {
			$addMode = true;
			$err	 = $msg_failAddTransporterRateMaster;
		}
		$transporterRateRecIns = false;
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {	
		$transporterRateId =	$p["hidTransporterRateId"];
		
		$selTransporter 	= $p["selTransporter"];
		$selZone 		= $p["selZone"];	
		$transporterRateListId	= $p["transporterRateList"];
		$rowCount		= $p["hidTableRowCount"]; // Row Count
				

		if ($transporterRateId!="") {
			# Update Rec 
			//$transporterRecUptd = $transporterRateMasterObj->updateTransporterRate($transporterRateId);

			if ($rowCount>0) {
				$transporterRateEntryId = "";		
				for ($i=1; $i<=$rowCount; $i++) {
					$weightSlabId 	= $p["weightSlabId_".$i];
					$rate		= $p["rate_".$i];
					$transporterRateEntryId	= $p["transporterRateEntryId_".$i];
					$trptrWtSlabEntryId = $p["trptrWtSlabEntryId_".$i];
					$trptrRateType		= $p["trptrRateType_".$i];

					if ($transporterRateId!="" && $weightSlabId!="" && $transporterRateEntryId=="") {
						$transporterRateEntryRecIns = $transporterRateMasterObj->addTransporterRateEntryRec($transporterRateId, $weightSlabId, $rate, $trptrWtSlabEntryId, $trptrRateType);
						$transporterRecUptd = true;
					} else if ($transporterRateId!="" && $weightSlabId!="" && $transporterRateEntryId!="") {
						$updateTransporterRateEntryRec = $transporterRateMasterObj->updateTransporterRateEntryRec($transporterRateEntryId, $weightSlabId, $rate, $trptrWtSlabEntryId, $trptrRateType);
						$transporterRecUptd = true;
					}
				} // Foe Loop Ends Here	
			} // Rate Per Kg ends (RPW)
		}
	
		if ($transporterRecUptd) {
			$editMode = false;
			$p["editId"] = "";
			$editId = "";
			$sessObj->createSession("displayMsg",$msg_succTransporterRateMasterUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateTransporterRateMaster.$selection);	
		} else {
			$editMode	=	true;
			$err		=	$msg_failTransporterRateMasterUpdate;
		}
		$transporterRecUptd	=	false;
	}


	# Edit  a Record		
	if ($p["editId"]) {
		$editId		= $p["editId"];
		$editMode = true;
		$transporterRateRec	= $transporterRateMasterObj->find($editId);	
		$editTransporterRateId  = $transporterRateRec[0];	
		$selTransporter 	= $transporterRateRec[1];
		$selZone 		= $transporterRateRec[2];
		$transporterRateListId	= $transporterRateRec[3];
		/*
		$trptrRateType		= $transporterRateRec[4];			
		if ($trptrRateType=='RPW') $ratePerKgchk = "checked";
		else if ($trptrRateType=='FRC') $fixedRatePerCngchk = "checked";
		*/
		$disableField		= "disabled";
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$transporterRateId	=	$p["delId_".$i];

			if ($transporterRateId!="") {	
					
				# Check OC in use in use
				$trptrRateRecInUse = $transporterRateMasterObj->trptrRateRecInUse($transporterRateId);
				if (!$trptrRateRecInUse) {
	
					// Need to check whether the function is using in any where	
					# Delete Entry Rec					
					$delTransporterRateEntryRec = $transporterRateMasterObj->deleteTransporterRateEntryRec($transporterRateId);	
					# Delete Main Rec
					if ($delTransporterRateEntryRec) { 
						$transporterRateRecDel = $transporterRateMasterObj->deleteTransporterRate($transporterRateId);
					}					
				}
				if ($trptrRateRecInUse) $existCount++;
			}
		}
		
		if ($transporterRateRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelTransporterRateMaster);
			$sessObj->createSession("nextPage",$url_afterDelTransporterRateMaster.$selection);
		} else {
			if ($existCount>0) $errDel = $msg_failDelTransporterRateMaster."<br>The selected Transporter Rate is linked with sales order.";
			else $errDel	=	$msg_failDelTransporterRateMaster;
		}
		$transporterRateRecDel	=	false;
	}
	
	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$transporterRateId	=	$p["confirmId"];
			if ($transporterRateId!="") {
				// Checking the selected fish is link with any other process
				$transporterRecConfirm = $transporterRateMasterObj->updateTransporterRateconfirm($transporterRateId);
			}

		}
		if ($transporterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmtransporter);
			$sessObj->createSession("nextPage",$url_afterDelTransporterRateMaster.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$transporterRateId = $p["confirmId"];
			if ($transporterRateId!="") {
				#Check any entries exist
				
					$transporterRecConfirm = $transporterRateMasterObj->updateTransporterRateReleaseconfirm($transporterRateId);
				
			}
		}
		if ($transporterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmtransporter);
			$sessObj->createSession("nextPage",$url_afterDelTransporterRateMaster.$selection);
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

	if ($g["transporterFilter"]!="") $transporterFilterId = $g["transporterFilter"];
	else $transporterFilterId = $p["transporterFilter"];	

	if ($g["transporterRateListFilter"]!="") $transporterRateListFilterId = $g["transporterRateListFilter"];
	else $transporterRateListFilterId = $p["transporterRateListFilter"];	

	# Resettting offset values
	if ($p["hidTransporterFilterId"]!=$p["transporterFilter"]) {		
		$offset = 0;
		$pageNo = 1;	
		$transporterRateListFilterId = "";	
	} else if ($p["hidtransporterRateListFilterId"]!=$p["transporterRateListFilter"]) {
		$offset = 0;
		$pageNo = 1;
	}

	# List all Records
	$transporterRateRecords = $transporterRateMasterObj->fetchAllPagingRecords($offset, $limit, $transporterFilterId, $transporterRateListFilterId);	
	$transporterRateRecordSize = sizeof($transporterRateRecords);	

	## -------------- Pagination Settings II -------------------
	$fetchAllTransporterRateRecs = $transporterRateMasterObj->fetchAllRecords($transporterFilterId, $transporterRateListFilterId);
	$numrows	=  sizeof($fetchAllTransporterRateRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
			
		
	# Fetch All Transporter Rate List Records
	if ($transporterFilterId) {
		$transporterRateListFilterRecords = $transporterRateListObj->filterTransporterWiseRecords($transporterFilterId,$transporterFunctionType);
	}
	
	# List all Transporter		
	//$transporterRecords	= $transporterMasterObj->fetchAllRecords();
	$transporterRecords	= $transporterMasterObj->fetchAllRecordsActiveTransporter();

	# Get All Zone Records
	//$zoneRecords = $zoneMasterObj->fetchAllRecords();

	$zoneRecords = $zoneMasterObj->fetchAllRecordsActiveZone();

	# --------------------------------------------------------------------------------------------

	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav		
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/TransporterRateMaster.js"; 

	#heading Section
	if ($editMode) $heading	=	$label_editTransporterRateMaster;
	else	       $heading	=	$label_addTransporterRateMaster;

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmTransporterRateMaster" action="TransporterRateMaster.php" method="post">
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
					$bxHeader = "Transporter Rate Master";
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
	<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('TransporterRateMaster.php');">&nbsp;&nbsp;
	<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateTransporterMaster(document.frmTransporterRateMaster);">
</td>
												
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterRateMaster.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateTransporterMaster(document.frmTransporterRateMaster);">												</td>
					<?}?>
					</tr>
	<input type="hidden" name="hidTransporterRateId" id="hidTransporterRateId" value="<?=$editTransporterRateId;?>">
	<tr><TD height="10"></TD></tr>
					<tr>
					  	<td colspan="2" class="err1" align="center" nowrap style="padding-left:5px;padding-right:5px;" id="divRecExistTxt">
						</td>
					</tr>
					<tr>
					  	<td colspan="2" nowrap style="padding-left:5px;padding-right:5px;">
					<table width="200">
					<tr>
					<td nowrap class="fieldName">*Transporter</td>
					<td nowrap>
                                        <select name="selTransporter" id="selTransporter" onchange="xajax_getTransporterRateRec(document.getElementById('selTransporter').value, document.getElementById('selZone').value, '<?=$mode?>', '','<?=$transporterFunctionType?>'); xajax_getWtSlabList(document.getElementById('selTransporter').value, '<?=$mode?>', '<?=$editTransporterRateId?>');" <?=$disableField?>>
                                        <option value="">-- Select --</option>
					<?php
						foreach ($transporterRecords as $tr) {
							$transporterId	 = $tr[0];
							$transporterName = stripSlash($tr[2]);	
							$selected = "";
							if ($selTransporter==$transporterId) $selected = "selected";	
					?>
                            		<option value="<?=$transporterId?>" <?=$selected?>><?=$transporterName?></option>
					<? }?>
					</select>
					<input type="hidden" name="hidDistributor" value="<?=$selTransporter?>">
					</td>
					<td>&nbsp;</td>
					<td class="fieldName" nowrap >*Zone </td>
		<td>
			<select name="selZone" id="selZone" onchange="xajax_getTransporterRateRec(document.getElementById('selTransporter').value, document.getElementById('selZone').value, '<?=$mode?>', '', '<?=$transporterFunctionType?>'); xajax_getAreaDemarcation(document.getElementById('selZone').value);" <?=$disableField?>>
				<option value="">-- Select --</option>
				<?php
					foreach ($zoneRecords as $zr) {
						$zoneId		= $zr[0];		
						$zoneName 	= stripSlash($zr[2]);
						$selected =  ($selZone==$zoneId)?"selected":"";
				?>
				<option value="<?=$zoneId?>" <?=$selected?>><?=$zoneName?></option>
				<?php
					}
				?>			
			</select>
		</td>
	</tr>		
	</table>
	</td></tr>
	<tr><td id="areaDemarcation" style="padding-left:5px; padding-right:5px;"></td></tr>
	<tr><TD height="5"></TD></tr>	
	<tr id="ratePerKgRow">
		<input type="hidden" name="WtSlabExist" id="WtSlabExist" >
	<!--  Weight Slab List section-->
		<td colspan="2" style="padding-left:5px; padding-right:5px;" align="center" id="wtSlabList"></td>
	</tr>
	<tr><TD height="10"></TD></tr>
			<tr>
				<? if($editMode){?>
				<td colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterRateMaster.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onclick="return validateTransporterMaster(document.frmTransporterRateMaster);">
				</td>
				<?} else{?>
				<td  colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterRateMaster.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateTransporterMaster(document.frmTransporterRateMaster);">							
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
<tr>
				<td colspan="3" align="center">
						<table width="30%">
						<TR><TD>
						<?php			
							$entryHead = "";
							require("template/rbTop.php");
						?>
						<table cellpadding="4" cellspacing="0">
					  <tr>
					<td nowrap="nowrap">
					<table cellpadding="2" cellspacing="0">
                	<tr>
		<td class="listing-item" nowrap="true">Transporter</td>
                <td nowrap="true">
		<select name="transporterFilter" id="transporterFilter" onchange="this.form.submit();">
		<option value="">-- Select All --</option>
		<?php	
			foreach ($transporterRecords as $tr) {
				$transId   = $tr[0];
				$transName = stripSlash($tr[2]);
				$selected =  ($transporterFilterId==$transId)?"selected":"";	
		?>
                <option value="<?=$transId?>" <?=$selected?>><?=$transName?></option>
		<?php 
			}
		?>		
                </select> 
                 </td>
	   <td class="listing-item">&nbsp;</td>
	   <td class="listing-item" nowrap="true">Rate List</td>
	<td nowrap="true">
		<select name="transporterRateListFilter" id="transporterRateListFilter" onchange="this.form.submit();">
                        <option value="">-- Select All --</option>
			<?php
			foreach ($transporterRateListFilterRecords as $srl) {
				$rateListRecId	=	$srl[0];
				$rateListName	=	stripSlash($srl[1]);				
				$startDate	=	dateFormat($srl[2]);
				$displayRateList = $rateListName."&nbsp;(".$startDate.")";
				$selected = ($transporterRateListFilterId==$rateListRecId)?"Selected":"";
			?>
                      <option value="<?=$rateListRecId?>" <?=$selected?>><?=$displayRateList?></option>
                      <? }?>
                      </select>
	</td>		
          <td>&nbsp;</td>
                          </tr>
                    </table>
		</td></tr>
	</table>
		<?php
			require("template/rbBottom.php");
		?>
						</td>
						</tr>
						</table>
								</td>
							</tr>
			<!--<tr>
				<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Transporter Rate Master  </td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap"></td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>

								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$transporterRateRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintTransporterRateMaster.php?transporterFilter=<?=$transporterFilterId?>&transporterRateListFilter=<?=$transporterRateListFilterId?>',700,600);"><? }?></td>
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
		<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
		if ($transporterRateRecordSize) {
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
      				$nav.= " <a href=\"TransporterRateMaster.php?pageNo=$page&transporterFilter=$transporterFilterId&transporterRateListFilter=$transporterRateListFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"TransporterRateMaster.php?pageNo=$page&transporterFilter=$transporterFilterId&transporterRateListFilter=$transporterRateListFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"TransporterRateMaster.php?pageNo=$page&transporterFilter=$transporterFilterId&transporterRateListFilter=$transporterRateListFilterId\"  class=\"link1\">>></a> ";
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
	<th class="listing-head" style="padding-left:3px; padding-right:3px;font-size:11px;">Transporter</th>
	<th class="listing-head" style="padding-left:3px; padding-right:3px;font-size:11px;">Zone</th>
	<th class="listing-head" style="padding-left:3px; padding-right:3px;font-size:11px;">Weight Slab&nbsp;/&nbsp;Rate</th>
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
		foreach ($transporterRateRecords as $trr) {
			$i++;
			$transporterRateId 	= $trr[0];
			$transporterId 		= $trr[1];

			$transporterName = "";
			if ($prevTransporterId!=$transporterId) {
				$transporterName = $trr[4];
			}
			$zoneId		= $trr[2];
			$zoneName	= $trr[5];
			$active=$trr[6];
		
			# Get Wt Slab Records
			//$getWtSlabRecs = $transporterRateMasterObj->getSelWtSlabRecs($transporterRateId);
			# Zone Wise Area Recs
			$displayAreaDemarcation = $transporterRateMasterObj->displayArea($zoneId);

			# Trptr Rate Slab
			 $disTrptrRateSlab	= $transporterRateMasterObj->displayTransporterRate($transporterRateId, $transporterId);

			//$rateType		= ($trr[6]=='RPW')?(($disTrptrRateSlab)?"<a href='###' class='link1' style='text-decoration:none;font-size:9pt;' onMouseover=\"ShowTip('$disTrptrRateSlab');\" onMouseout=\"UnTip();\">Rate Per Kg</a>":"Rate Per Kg"):(($disTrptrRateSlab)?"<a href='###' class='link1' style='text-decoration:none;font-size:9pt;' onMouseover=\"ShowTip('$disTrptrRateSlab');\" onMouseout=\"UnTip();\">Fixed Rate</a>":"Fixed Rate");
			
	?>
	<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$transporterRateId;?>" class="chkBox"></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" nowrap="true"><?=$transporterName;?></td>			
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" onMouseover="ShowTip('<?=$displayAreaDemarcation?>');" onMouseout="UnTip();">
			<a href="###" class="link1" title="click here to view Area" style="text-decoration:none;font-size:9pt;">
			<?=$zoneName;?>
			</a>
		</td>
		<td class="listing-item" style="padding-left:10px; padding-right:3px;" nowrap="true" align="left"><?=$disTrptrRateSlab;?></td>		
	<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
			<?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,'<?=$transporterRateId?>','editId');assignValue(this.form,'1','editSelectionChange');this.form.action='TransporterRateMaster.php';" ><? } ?>
		</td>

		 <? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$transporterRateId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$transporterRateId;?>,'confirmId');" >
			<?php }?>
			<? }?>
			
			
			
			</td>
	<? }?>
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
		<td colspan="5" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"TransporterRateMaster.php?pageNo=$page&transporterFilter=$transporterFilterId&transporterRateListFilter=$transporterRateListFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"TransporterRateMaster.php?pageNo=$page&transporterFilter=$transporterFilterId&transporterRateListFilter=$transporterRateListFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"TransporterRateMaster.php?pageNo=$page&transporterFilter=$transporterFilterId&transporterRateListFilter=$transporterRateListFilterId\"  class=\"link1\">>></a> ";
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
	<?php
		} else {
	?>
	<tr>
		<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?php
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
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$transporterRateRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintTransporterRateMaster.php?transporterFilter=<?=$transporterFilterId?>&transporterRateListFilter=<?=$transporterRateListFilterId?>',700,600);"><? }?></td>
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
				<!-- Form fields end   -->	
		</td>
	</tr>	
	<input type="hidden" name="hidTransporterFilterId" value="<?=$transporterFilterId?>">	
	<input type="hidden" name="hidtransporterRateListFilterId" value="<?=$transporterRateListFilterId?>">	
	<input type="hidden" name="transporterRateList" id="transporterRateList" value="<?=$transporterRateListId?>">
	<input type="hidden" name="hidMode" id="hidMode" value="<?=$mode?>">
		<tr>
			<td height="10"></td>
		</tr>
	<input type="hidden" name="inIFrame" id="inIFrame" value="<?=$iFrameVal?>">
	</table>
	<? if ($editMode) {?>
		<script language="JavaScript" type="text/javascript">
			xajax_getAreaDemarcation('<?=$selZone?>');
			xajax_getWtSlabList('<?=$selTransporter?>', '<?=$mode?>', '<?=$editTransporterRateId?>');
		</script>
	<? }?>
	<?php 
	if ($iFrameVal=="") { 
	?>
	<script language="javascript" type="text/javascript">
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
	//ensureInFrameset(document.frmTransporterRateMaster);
	//-->
	</script>
<?php 
	}
?>	
	</form>
<?php
	# Include Template [bottomRightNav.php]
	if ($iFrameVal=='N') require("template/bottomRightNav.php");
	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>