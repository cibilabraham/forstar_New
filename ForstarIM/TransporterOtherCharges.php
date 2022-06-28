<?php
	$insideIFrame = "Y";
	require("include/include.php");
	require_once("lib/TransporterOtherCharges_ajax.php");

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
	
	if ($g["selProduct"]!="") $transporterId = $g["selProduct"];

	if ($p["cmdCancel"]!="") {
		$addMode = false;	
		$editMode = false;				
		$p["editId"] = "";
	}
	
	$transporterFunctionType = "TOC"; // Transporter Other Charges

	#Add a Rec
	if ($p["cmdAdd"]!="") {
		$selTransporter 	= $p["selTransporter"];		
		$transporterRateListId	= $p["transporterRateList"];

		$fovCharge	= $p["fovCharge"];
		$docketCharge	= $p["docketCharge"];
		$serviceTax	= $p["serviceTax"];
		$octroiServiceCharge = $p["octroiServiceCharge"];
		$odaCharge	= $p["odaCharge"];
		$surcharge	= $p["surcharge"];
		
		# Creating a New Rate List
		if ($transporterRateListId=="") {
			$transporterRec		= $transporterMasterObj->find($selTransporter);
			$transporterName = str_replace (" ",'',$transporterRec[2]);
			$selName 	 = substr($transporterName, 0,9);	
			$rateListName = $selName."-".date("dMy");
			$startDate    = date("Y-m-d");
			$transporterRateListRecIns = $transporterRateListObj->addTransporterRateList($rateListName, $startDate, $cRList, $userId, $selTransporter, $dCurrentRListId, $transporterFunctionType);
			if ($transporterRateListRecIns) $transporterRateListId =$transporterRateListObj->latestRateList($selTransporter, $transporterFunctionType);	
		}
				
		if ($selTransporter!="" && $transporterRateListId) {
			$transporterRateRecIns = $transporterOtherChargesObj->addTransporterOtherCharge($selTransporter, $transporterRateListId, $fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge, $userId, $odaCharge, $surcharge);
		}

		if ($transporterRateRecIns) {
			$addMode = false;
			$sessObj->createSession("displayMsg",$msg_succAddTransporterOtherCharges);
			$sessObj->createSession("nextPage",$url_afterAddTransporterOtherCharges.$selection);
		} else {
			$addMode = true;
			$err	 = $msg_failAddTransporterOtherCharges;
		}
		$transporterRateRecIns = false;
	}


	#Update a Record
	if ($p["cmdSaveChange"]!="") {	
		$transporterOtherChargeId =	$p["hidTransporterRateId"];
		
		$selTransporter 	= $p["selTransporter"];		
		$transporterRateListId	= $p["transporterRateList"];
		$rowCount		= $p["hidTableRowCount"]; // Row Count

		$fovCharge	= $p["fovCharge"];
		$docketCharge	= $p["docketCharge"];
		$serviceTax	= $p["serviceTax"];
		$octroiServiceCharge = $p["octroiServiceCharge"];
		$odaCharge	= $p["odaCharge"];
		$surcharge	= $p["surcharge"];

		if ($transporterOtherChargeId!="") {
			# Update Rec
			$transporterRecUptd = $transporterOtherChargesObj->updateTransporterRate($transporterOtherChargeId,$fovCharge, $docketCharge, $serviceTax, $octroiServiceCharge, $odaCharge, $surcharge);
		}
	
		if ($transporterRecUptd) {
			$editMode = false;
			$p["editId"] = "";
			$editId = "";
			$sessObj->createSession("displayMsg",$msg_succTransporterOtherChargesUpdate);
			$sessObj->createSession("nextPage",$url_afterUpdateTransporterOtherCharges.$selection);	
		} else {
			$editMode	=	true;
			$err		=	$msg_failTransporterOtherChargesUpdate;
		}
		$transporterRecUptd	=	false;
	}


	# Edit  a Record		
	if ($p["editId"]) {
		$editId		= $p["editId"];
		$editMode = true;
		$transporterRateRec	= $transporterOtherChargesObj->find($editId);	
		$editTransporterRateId  = $transporterRateRec[0];	
		$selTransporter 	= $transporterRateRec[1];		
		$transporterRateListId	= $transporterRateRec[2];

		$fovCharge		= $transporterRateRec[3];
		$docketCharge		= $transporterRateRec[4];
		$serviceTax		= $transporterRateRec[5];
		$octroiServiceCharge 	= $transporterRateRec[6];
		$odaCharge		= $transporterRateRec[7];
		$surcharge		= ($transporterRateRec[8]!=0)?$transporterRateRec[8]:"";
		
		$disableField		= "disabled";
	}

	# Delete a Record
	if ( $p["cmdDelete"]!="") {
		$rowCount	=	$p["hidRowCount"];
		$existCount = 0;
		for ($i=1; $i<=$rowCount; $i++) {
			$transporterOtherChargeId	=	$p["delId_".$i];

			if ($transporterOtherChargeId!="") {	
				# Check OC in use in use
				$trptrOCRecInUse = $transporterOtherChargesObj->trptrOCRecInUse($transporterOtherChargeId);
				if (!$trptrOCRecInUse) {
					// Need to check whether the function is using in any where	
					# Delete Main Rec
					$transporterRateRecDel = $transporterOtherChargesObj->deleteTransporterRate($transporterOtherChargeId);	
				}
				if ($trptrOCRecInUse) $existCount++;
			}
		}
		
		if ($transporterRateRecDel) {
			$sessObj->createSession("displayMsg",$msg_succDelTransporterOtherCharges);
			$sessObj->createSession("nextPage",$url_afterDelTransporterOtherCharges.$selection);
		} else {
			if ($existCount>0) $errDel	= $msg_failDelTransporterOtherCharges."<br>The selected Transporter Other charge is in use. <br><span style='font-size:9px;'>The selected Transporter other charge is linked with Sales Order. </span>";
			else $errDel	=	$msg_failDelTransporterOtherCharges;
		}
		$transporterRateRecDel	=	false;
	}
	
	if ($p["btnConfirm"]!="")
	{
		$rowCount	=	$p["hidRowCount"];
		for ($i=1; $i<=$rowCount; $i++) {
			$transporterOtherChargeId	=	$p["confirmId"];
			if ($transporterOtherChargeId!="") {
				// Checking the selected fish is link with any other process
				$transporterRecConfirm = $transporterOtherChargesObj->updatetransporterOtherChargesconfirm($transporterOtherChargeId);
			}

		}
		if ($transporterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succConfirmtransporter);
			$sessObj->createSession("nextPage",$url_afterDelTransporterOtherCharges.$selection);
		} else {
			$errConfirm	=	$msg_failConfirm;
		}
		}


	if ($p["btnRlConfirm"]!="")
	{
	
$rowCount	=	$p["hidRowCount"];
for ($i=1; $i<=$rowCount; $i++) {

			$transporterOtherChargeId = $p["confirmId"];
			if ($transporterOtherChargeId!="") {
				#Check any entries exist
				
					$transporterRecConfirm = $transporterOtherChargesObj->updatetransporterOtherChargesReleaseconfirm($transporterOtherChargeId);
				
			}
		}
		if ($transporterRecConfirm) {
			$sessObj->createSession("displayMsg",$msg_succRelConfirmtransporter);
			$sessObj->createSession("nextPage",$url_afterDelTransporterOtherCharges.$selection);
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
	$transporterRateRecords = $transporterOtherChargesObj->fetchAllPagingRecords($offset, $limit, $transporterFilterId, $transporterRateListFilterId);	
	$transporterRateRecordSize = sizeof($transporterRateRecords);	

	## -------------- Pagination Settings II -------------------
	$fetchAllTransporterRateRecs = $transporterOtherChargesObj->fetchAllRecords($transporterFilterId, $transporterRateListFilterId);
	$numrows	=  sizeof($fetchAllTransporterRateRecs);
	$maxpage	=  ceil($numrows/$limit);
	## ----------------- Pagination Settings II End ------------
			
		
	# Fetch All Transporter Rate List Records
	if ($transporterFilterId) {
		$transporterRateListFilterRecords = $transporterRateListObj->filterTransporterWiseRecords($transporterFilterId,$transporterFunctionType);
	}
	
	# List all Transporter		
	$transporterRecords	= $transporterMasterObj->fetchAllRecordsActiveTransporter();	
	# --------------------------------------------------------------------------------------------

	if ($addMode) 		$mode = 1;
	else if ($editMode) 	$mode = 2;
	else 			$mode = "";

	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav		
	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/TransporterOtherCharges.js"; 

	#heading Section
	if ($editMode) $heading	=	$label_editTransporterOtherCharges;
	else	       $heading	=	$label_addTransporterOtherCharges;

	# Include Template [topLeftNav.php]
	$iFrameVal	= $p["inIFrame"]; // N - Not in Iframe
	if ($iFrameVal=='N') require("template/topLeftNav.php");
	else require("template/btopLeftNav.php");
?>
	<form name="frmTransporterOtherCharges" action="TransporterOtherCharges.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="96%" >
	<tr><td height="10"></td></tr>	
	<? if($err!="" ){?>
	<tr>
		<td height="10" align="center" class="err1" > <?=$err;?></td>
	</tr>
	<?}?>
	<tr>
	<td align="center">
		<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
			<tr>
				<td>
				<?php	
					$bxHeader = "Transporter Other Charges";
					include "template/boxTL.php";
				?>
				<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
				<tr>
					<td colspan="3" align="center">
		<Table width="45%">
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
										<table cellpadding="0"  width="85%" cellspacing="0" border="0" align="center">
											<tr>
												<td colspan="2" height="10" ></td>
											</tr>
											<tr>
												<? if($editMode){?>

											  <td colspan="2" align="center">
	<input type="submit" name="cmdCancel" class="button" value=" Cancel " onclick="return cancel('TransporterOtherCharges.php');">&nbsp;&nbsp;
	<input type="submit" name="cmdSaveChange" id="cmdSaveChange" class="button" value=" Save Changes " onclick="return validateTransporterOtherCharge(document.frmTransporterOtherCharges);">
</td>
											
												<?} else{?>

												
												<td  colspan="2" align="center">
												<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterOtherCharges.php');">&nbsp;&nbsp;
												<input type="submit" name="cmdAdd" id="cmdAdd" class="button" value=" Add " onClick="return validateTransporterOtherCharge(document.frmTransporterOtherCharges);">												</td>
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
                                        <select name="selTransporter" id="selTransporter" onchange="xajax_getTransporterRateRec(document.getElementById('selTransporter').value, '<?=$mode?>', '<?=$transporterFunctionType?>','');" <?=$disableField?>>
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
		
	</tr>		
	</table>
	</td></tr>
	<!--  Dynamic Row adding starts here-->
	<tr>
		<td colspan="2" style="padding-left:5px; padding-right:5px;" align="center">
			<table>
				<TR>
					<TD>
						<!--<fieldset>
						<legend class="listing-item">Others</legend>-->
						<?php
							$entryHead = "Others";
							$rbTopWidth = "";
							require("template/rbTop.php");
						?>
						<table width="200" align="center">
						<tr>
							<TD>
								<table>
									<tr>
									<td class="fieldName" nowrap>*FOV Charge: </td>
										<td class="listing-item" nowrap="true">
											<INPUT NAME="fovCharge" TYPE="text" id="fovCharge" value="<?=$fovCharge;?>" size="4" style="text-align:right;">&nbsp;%
										</td>
									</tr>
									<tr>
									<td class="fieldName" nowrap>*Docket Charge (Rs.): </td>
										<td class="listing-item" nowrap="true">
											<INPUT NAME="docketCharge" TYPE="text" id="docketCharge" value="<?=$docketCharge;?>" size="4" style="text-align:right;">
										</td>
									</tr>
									<tr>
									<td class="fieldName" nowrap onMouseover="ShowTip('Out of Delivery Charges');" onMouseout="UnTip();">ODA Charge (Rs.): </td>
										<td class="listing-item" nowrap="true">
											<INPUT NAME="odaCharge" TYPE="text" id="odaCharge" value="<?=$odaCharge;?>" size="4" style="text-align:right;">
										</td>
									</tr>
								</table>
							</TD>
							<td>&nbsp;</td>
							<td valign="top">
								<table>
									<tr>
										<td class="fieldName" nowrap>*Service Tax Rate: </td>
											<td class="listing-item" nowrap="true">
												<INPUT NAME="serviceTax" TYPE="text" id="serviceTax" value="<?=$serviceTax;?>" size="4" style="text-align:right;">&nbsp;%
											</td>
										</tr>
										<tr>
										<td class="fieldName" nowrap>*Octroi Service charge: </td>
											<td class="listing-item" nowrap="true">
												<INPUT NAME="octroiServiceCharge" TYPE="text" id="octroiServiceCharge" value="<?=$octroiServiceCharge;?>" size="4" style="text-align:right;">&nbsp;%
											</td>
										</tr>
										<tr>
										<td class="fieldName" nowrap>Surcharge: </td>
											<td class="listing-item" nowrap="true">
												<INPUT NAME="surcharge" TYPE="text" id="surcharge" value="<?=$surcharge;?>" size="4" style="text-align:right;">&nbsp;%
											</td>
										</tr>
								</table>
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
		</td>
	</tr>	
	<tr>
		<td colspan="2"  height="10" ></td>
	</tr>
			<tr>
				<? if($editMode){?>
				<td colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterOtherCharges.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdSaveChange" id="cmdSaveChange1" class="button" value=" Save Changes " onclick="return validateTransporterOtherCharge(document.frmTransporterOtherCharges);">
				</td>
				<?} else{?>
				<td  colspan="2" align="center">
					<input type="submit" name="cmdCancel" class="button" value=" Cancel " onClick="return cancel('TransporterOtherCharges.php');">&nbsp;&nbsp;
					<input type="submit" name="cmdAdd" id="cmdAdd1" class="button" value=" Add " onClick="return validateTransporterOtherCharge(document.frmTransporterOtherCharges);">							
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
				<!-- Form fields end   -->			</td>
		</tr>	
		<?php
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
		<table width="35%">
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
						<td class="listing-item" nowrap="true">Transporter:&nbsp;</td>
						<td nowrap="true">
						<select name="transporterFilter" id="transporterFilter" onchange="this.form.submit();">
						<option value="">-- Select All --</option>
						<?php	
							foreach ($transporterRecords as $tr) {
								$transId   = $tr[0];
								$transName = stripSlash($tr[2]);
								$selected  = ($transporterFilterId==$transId)?"selected":"";	
						?>
						<option value="<?=$transId?>" <?=$selected?>><?=$transName?></option>
						<? 
							}
						?>		
						</select> 
						</td>
					<td class="listing-item">&nbsp;</td>
					<td class="listing-item" nowrap="true">Rate List:</td>
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
			<!--<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%">
					<tr>
						<td>
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;Transporter Other Charges  </td>
									<td background="images/heading_bg.gif" align="right" nowrap="nowrap"></td>
								</tr>-->
								<tr>
									<td colspan="3" height="10" ></td>
								</tr>
								<tr>	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$transporterRateRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintTransporterOtherChargesMaster.php?transporterFilter=<?=$transporterFilterId?>&transporterRateListFilter=<?=$transporterRateListFilterId?>',700,600);"><? }?></td>
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
		<table cellpadding="2"  width="65%" cellspacing="1" border="0" align="center" id="newspaper-b1">
		<?
		if ($transporterRateRecordSize) {
			$i	=	0;
		?>
		<thead>
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
      				$nav.= " <a href=\"TransporterOtherCharges.php?pageNo=$page&transporterFilter=$transporterFilterId&transporterRateListFilter=$transporterRateListFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"TransporterOtherCharges.php?pageNo=$page&transporterFilter=$transporterFilterId&transporterRateListFilter=$transporterRateListFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"TransporterOtherCharges.php?pageNo=$page&transporterFilter=$transporterFilterId&transporterRateListFilter=$transporterRateListFilterId\"  class=\"link1\">>></a> ";
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
		<th width="20" rowspan="2">
			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'delId_');" class="chkBox">
		</th>
		<th class="listing-head" style="padding-left:3px; padding-right:3px;font-size:11px;" rowspan="2">Transporter</th>	
		<th class="listing-head" style="padding-left:3px; padding-right:3px;font-size:11px;" colspan="6">Other Charges</th>
		<? if($edit==true){?>
			<th class="listing-head" rowspan="2">&nbsp;</th>
		<? }?>	
		<? if($confirm==true){?>
		<th class="listing-head" rowspan="2">&nbsp;</th>
		<? }?>
	</tr>
	<tr align="center">		
		<th class="secondRowHead" style="padding-left:3px; padding-right:3px;font-size:11px;">FOV<br> (%)</th>
		<th class="secondRowHead" style="padding-left:3px; padding-right:3px;font-size:11px;">Docket<br> (Rs.)</th>
		<th class="secondRowHead" style="padding-left:3px; padding-right:3px;font-size:11px;">Service Tax<br> (%)</th>
		<th class="secondRowHead" style="padding-left:3px; padding-right:3px;font-size:11px;">Octroi Service<br> (%)</th>
		<th class="secondRowHead" style="padding-left:3px; padding-right:3px;font-size:11px;">ODA Charge<br> (Rs.)</th>
		<th class="secondRowHead" style="padding-left:3px; padding-right:3px;font-size:11px;">Surcharge<br> (%)</th>
	</tr>
	</thead>
	<tbody>
	<?php
		$odaCharge = "";
		foreach ($transporterRateRecords as $trr) {
			$i++;
			$transporterOtherChargeId 	= $trr[0];
			$transporterId 		= $trr[1];

			$transporterName = "";
			if ($prevTransporterId!=$transporterId) {
				$transporterName = $trr[3];
			}
			
			$fovCharge		= $trr[4];
			$docketCharge		= $trr[5];
			$serviceTax		= $trr[6];
			$octroiServiceCharge 	= $trr[7];
			$odaCharge 		= $trr[8];
			$trSurcharge		= $trr[9];
			$active=$trr[10];
	?>
	<tr <?php if ($active==0) { ?> id="inactive" bgcolor="#afddf8" onMouseOver="ShowTip('<?=$disMsgInactive?>');" onMouseOut="UnTip();" <?php }?>>
		<td width="20"><input type="checkbox" name="delId_<?=$i;?>" id="delId_<?=$i;?>" value="<?=$transporterOtherChargeId;?>" class="chkBox"></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" nowrap="true"><?=$transporterName;?></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" align="right"><?=$fovCharge;?></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" align="right"><?=$docketCharge;?></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" align="right"><?=$serviceTax;?></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" align="right"><?=$octroiServiceCharge;?></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" align="right"><?=($odaCharge!=0)?$odaCharge:"";?></td>	
		<td class="listing-item" style="padding-left:3px; padding-right:3px;" align="right"><?=($trSurcharge!=0)?$trSurcharge:"";?></td>	
	<? if($edit==true){?>
		<td class="listing-item" width="60" align="center">
			<?php if ($active==0){ ?><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,'<?=$transporterOtherChargeId?>','editId');assignValue(this.form,'1','editSelectionChange');this.form.action='TransporterOtherCharges.php';" ><? } ?>
			<!--<input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?//$transporterOtherChargeId;?>,'editId');assignValue(this.form,'1','editSelectionChange');this.form.action='TransporterOtherCharges.php';" >-->
		</td>
	<? }?>

	<? if ($confirm==true){?><td <?php if ($active==1) {?> class="listing-item" <?php }else {?> bgcolor="#afddf8" <?php }?> width="45" align="center" >
			
			<?php if ($active==0){ ?>
			<input type="submit" value=" <?=$pending;?> " name="btnConfirm" onClick="assignValue(this.form,<?=$transporterOtherChargeId;?>,'confirmId');" >
			<?php } else if ($active==1){?>
			<input type="submit" value="<?=$ReleaseConfirm;?> " name="btnRlConfirm" onClick="assignValue(this.form,<?=$transporterOtherChargeId;?>,'confirmId');" >
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
		<td colspan="9" align="right" style="padding-right:10px;" class="navRow">
		<div align="right">
		<?php
		 $nav  = '';
		for ($page=1; $page<=$maxpage; $page++) {
			if ($page==$pageNo) {
      				$nav.= "<span class='paging'>$page</span>"; // no need to create a link to current page				
   			} else {
      				$nav.= " <a href=\"TransporterOtherCharges.php?pageNo=$page&transporterFilter=$transporterFilterId&transporterRateListFilter=$transporterRateListFilterId\" class=\"link1\">$page</a> ";
				//echo $nav;
   			}
		}
		if ($pageNo > 1) {
   			$page  = $pageNo - 1;
   			$prev  = " <a href=\"TransporterOtherCharges.php?pageNo=$page&transporterFilter=$transporterFilterId&transporterRateListFilter=$transporterRateListFilterId\"  class=\"link1\"><<</a> ";
	 	} else {
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNo < $maxpage) {
   			$page = $pageNo + 1;
   			$next = " <a href=\"TransporterOtherCharges.php?pageNo=$page&transporterFilter=$transporterFilterId&transporterRateListFilter=$transporterRateListFilterId\"  class=\"link1\">>></a> ";
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
		<td colspan="3" height="5"></td>
	</tr>
								<tr >	
									<td colspan="3">
										<table cellpadding="0" cellspacing="0" align="center">
											<tr>
												<td><? if($del==true){?><input type="submit" value=" Delete " class="button"  name="cmdDelete" onClick="return confirmDelete(this.form,'delId_',<?=$transporterRateRecordSize;?>);" ><? }?>&nbsp;<? if($add==true){?><input type="submit" value=" Add New " name="cmdAddNew" class="button"><? }?>&nbsp;<? if($print==true){?><input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:printWindow('PrintTransporterOtherChargesMaster.php?transporterFilter=<?=$transporterFilterId?>&transporterRateListFilter=<?=$transporterRateListFilterId?>',700,600);"><? }?></td>
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
		<!--tr>
			<td height="10" align="center">
				<a href="TransporterRateList.php" class="link1" title="Click to Manage Transporter Rate List"> Transporter Rate List</a>
			</td>	
		</tr-->
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
	//ensureInFrameset(document.frmTransporterOtherCharges);
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