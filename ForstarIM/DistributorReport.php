<?php
	require("include/include.php");
	require_once("lib/DistributorReport_ajax.php");

	$err			= "";
	$errDel			= "";
	$editMode		= false;
	$addMode		= true;
	$searchMode 		= false;
	$recEditable 		= false;
	$statusUpdated		= false;
	//$printMode		= true;
	$dailyACStmntRecs	= array();

	#-------------------Admin Checking--------------------------------------
	$isAdmin 	= false;
	$role		= $manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

	// Cheking access control
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
	if ($isAdmin==true || $reEdit==true) $recEditable = true;	
	// Cheking access control end 

	# Inv List
	//$distributorReportObj->getInvList();

	# Get selected date 
	if ($g["dateFrom"]!="" && $g["dateTill"]!="") {
		$dateFrom = $g["dateFrom"];
		$dateTill = $g["dateTill"];
	} else if ($p["dateFrom"]!="" && $p["dateTill"]!="") {
		$dateFrom = $p["dateFrom"];
		$dateTill = $p["dateTill"];
	} else {
		$dateFrom = date("d/m/Y");
		$dateTill = date("d/m/Y");
	}

	$fromDate	= mysqlDateFormat($dateFrom);
	$tillDate	= mysqlDateFormat($dateTill);

	if ($p["selDistributor"]!="") $selDistributorId = $p["selDistributor"];
	else if ($g["selDistributor"]!="") $selDistributorId = $g["selDistributor"];

	$pendingOrder		= $p["pendingOrder"];
	if ($pendingOrder!="")	$pendingOrderChk = "checked";

	$orderDispatched	= $p["orderDispatched"];
	if ($orderDispatched)	$orderDispatchedChk = "checked";
	
	$claimPending		= $p["claimPending"];
	if ($claimPending)	$claimPendingChk = "checked";

	$claimSettled		= $p["claimSettled"];
	if ($claimSettled)	$claimSettledChk = "checked";

	$distributorAccount		= $p["distributorAccount"];
	if ($distributorAccount)	$distributorAccountChk = "checked";

	$sampleInvoice			= $p["sampleInvoice"];
	if ($sampleInvoice)	$sampleInvoiceChk = "checked";
	
	if ($p["qryType"]!="") $qryType	= $p["qryType"];
	
	$distOverdueChk = "";
	if ($p["distOverdue"]!="" || $g["distOverdue"]!="") $distOverdueChk = "checked";

	$distACStmntChk = "";
	$distACStmnt	= 	$p["distACStmnt"];
	if ($distACStmnt!="") $distACStmntChk = "checked";
		
	
	# Generate Report (Search From Dashboard)
	if ($p["cmdSearch"]!="" || $g["cmdSearch"]!="") {
		if ($fromDate!="" && $fromDate!="") {
			# Sales Order
			if ($pendingOrder!="" || $orderDispatched!="") {
				$salesOrderRecords = $distributorReportObj->getSalesOrderRecords($fromDate, $tillDate, $selDistributorId, $pendingOrder, $orderDispatched);
			}
			# Claim
			if ($claimPending!="" || $claimSettled!="") {
				$claimOrderRecords = $distributorReportObj->getClaimOrderRecords($fromDate, $tillDate, $selDistributorId, $claimPending, $claimSettled);
			}
			# Distributor Account
			if ($distributorAccount!="") {
				$distributorAccountRecords = $distributorReportObj->getDistributorAccountRecords($fromDate, $tillDate, $selDistributorId);
			}

			# Sample Invoices
			if ($sampleInvoice) {
				$sampleInvoiceRecords = $distributorReportObj->getSOSampleInvoiceRecords($fromDate, $tillDate, $selDistributorId, $qryType);
			}

			# Overdue report
			if ($distOverdueChk) {
				# Get All records
				//$distOverdueRecs = $distributorReportObj->distOverdueRecs($fromDate, $tillDate, $selDistributorId);

				# Get Over due records only
				$distOverdueRecs = $distributorReportObj->getOverDueRecs($fromDate, $tillDate, $selDistributorId);
				$print=false;
				//printr($distOverdueRecs);
			}

			# DAILY ACCOUNT STATEMENT
			if ($distACStmntChk) {
				$dailyACStmntRecs = $distributorReportObj->dailyACStatmentRecs($fromDate, $tillDate, $selDistributorId);		
			}
		

			# inv recs	
			$invRecs = $distributorReportObj->distACInvoiceRecs($fromDate, $tillDate, $selDistributorId);
			$searchMode = true;
		}
	}

	
	# change status update
	if ($p["cmdConfirmRelease"]!="") {
		$distACRowCount	= $p["distACRowCount"];
		$statusChanged = false;
		for ($i=1; $i<=$distACRowCount; $i++) {
			$distACId 	= $p["distACId_".$i];

			if ($distACId!="") {
				$updateDistACRec = $distributorReportObj->updateDistACStatusRec($distACId);
				if ($updateDistACRec) {
					$statusChanged = true;
				}
			}
		}  // Loop Ends here
		if ($statusChanged) $succMsg = "Distributor account status changed successfully.";	
		$statusUpdated = true;
	} // Bulk Status change ends here

	# Change status
	$chkChangeStatus = false;
	if ($p["cmdChangeStatus"]!="" || $statusUpdated) {
		$invoiceFilterId	= $p["invoiceFilter"];
		
		# inv recs	
		$invRecs = $distributorReportObj->distACInvoiceRecs($fromDate, $tillDate, $selDistributorId);

		if ($selDistributorId && $invoiceFilterId) {
			$distAccountRecs = $distributorReportObj->getDistACRecs($fromDate, $tillDate, $selDistributorId, $invoiceFilterId);
		}
		
		$chkChangeStatus = true;
	}

	# List all Distributor
	//$distributorResultSetObj = $distributorMasterObj->fetchAllRecords();
	$distributorResultSetObj = $distributorMasterObj->fetchAllRecordsActiveDistributor();


	$ON_LOAD_SAJAX 		= "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	

	# include JS in template
	$ON_LOAD_PRINT_JS = "libjs/DistributorReport.js";	

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmDistributorReport" action="DistributorReport.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="85%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?php
			if ($editMode || $addMode) {
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Distributor Wise Report</td>
									<td background="images/heading_bg.gif"  >
									<table cellpadding="0" cellspacing="0" align="right">	
									<tr>
									</tr>
									</table></td>
								</tr>
								<tr>
									<td width="1" ></td>
								  <td colspan="2" ><table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
                                    <tr>
                                      <td height="10" ></td>
                                    </tr>
	<tr>
		<TD colspan="3" align="center">
			<table>
				<TR>
					<TD valign="top" style="padding-left:10px;padding-right:10px;padding-bottom:10px;paing-top:10px;">
					<table><TR><TD>
						<fieldset>
						<table>
							<TR>
								<td class="fieldName" nowrap>*From:&nbsp; </td>
								<td>
							<?php 
								if ($dateFrom=="") $dateFrom=date("d/m/Y");
							?>
								<input type="text" id="dateFrom" name="dateFrom" size="8" value="<?=$dateFrom?>" onchange="xajax_getDistACInvoice(document.getElementById('dateFrom').value, document.getElementById('dateTill').value, document.getElementById('selDistributor').value);">
								</td>
							</TR>
							<tr>
								<td class="fieldName"  nowrap >*To:&nbsp;</td>
								<td>
								<?php
									if ($dateTill=="") $dateTill=date("d/m/Y");
								?>
								<input type="text" id="dateTill" name="dateTill" size="8"  value="<?=$dateTill?>" onchange="xajax_getDistACInvoice(document.getElementById('dateFrom').value, document.getElementById('dateTill').value, document.getElementById('selDistributor').value);">
								</td>
							</tr>
							<tr>
                                                  <td class="fieldName">*Distributor</td>
                                                  <td class="listing-item">
							<select name="selDistributor" id="selDistributor" onchange="xajax_getDistACInvoice(document.getElementById('dateFrom').value, document.getElementById('dateTill').value, document.getElementById('selDistributor').value);">			
							<option value="">-- Select --</option>
							<?	
							while ($dr=$distributorResultSetObj->getRow()) {
								$distributorId	 = $dr[0];			
								$distributorName = stripSlash($dr[2]);	
								$selected = "";
								if ($selDistributorId==$distributorId) $selected = "selected";	
							?>
							<option value="<?=$distributorId?>" <?=$selected?>><?=$distributorName?></option>
							<? }?>
							</select>
					</td>
                                                </tr>
						</table>
					</fieldset>
			</TD></TR>
			<!-- Change Status -->
	<tr>
	<TD valign="top">
		<table>
			<TR>
			<!-- 	Change Status Start Here	 -->
			<? if ($isAdmin==true || $reEdit==true) { ?>
			<td valign="top" nowrap>
			<table>
				<TR>
					<TD>
					<fieldset>
					<legend class="listing-item">Change Status</legend>
						<table>
							<TR>
							<TD>
							<table>
							<TR>
								<td>
								<table>
									<TR>
									<TD class="fieldName" nowrap="nowrap">*Ref.Invoice</TD>
						<td nowrap>
							<select name="invoiceFilter" id="invoiceFilter">
								<?php
								if (sizeof($invRecs)<=0) {
								?>
								<option value="">--Select--</option>
								<?php 
									}
								?>
								<?php
								foreach ($invRecs as $invId=>$invNum) {
									$selected = ($invId==$invoiceFilterId)?"selected":"";
								?>
								<option value="<?=$invId?>" <?=$selected?>><?=$invNum?></option>
								<?php
									}
								?>
							</select>
						</td>
					</TR>
					</table>
				</td>										
				<td>
					<input type="submit" name="cmdChangeStatus" value=" OK " class="button" onclick="return validateDistACStatus();">
				</td>
			</TR>
			</table>
		</TD>
		</TR>
		</table>
		</fieldset>
		</TD>
		</TR>
		</table>
		</td>
	<? }?>
<!--  Change Status End here   -->
		</TR>
		</table>
	</TD>
	</tr>
<!-- Change Status -->
			</table>
					</TD>
					<td width="10">&nbsp;</td>
					<td valign="top">
						<fieldset>
						<legend class="fieldName" style="line-height:normal;">Search Options</legend>
						<table>
							<TR>
								<TD>
									<INPUT type="checkbox" name="pendingOrder" id="pendingOrder" value="Y" class="chkBox" onclick="selectChk('pendingOrder');" <?=$pendingOrderChk?>>
								</TD>
								<td class="listing-item" align="left">Pending Orders</td>
							</TR>
							<TR>
								<TD>
									<INPUT type="checkbox" name="orderDispatched" id="orderDispatched" value="Y" class="chkBox" onclick="selectChk('orderDispatched');" <?=$orderDispatchedChk?>>
								</TD>
								<td class="listing-item" align="left">Orders Dispatched</td>
							</TR>
							<TR>
								<TD>
									<INPUT type="checkbox" name="claimPending" id="claimPending" value="Y" class="chkBox" onclick="selectChk('claimPending');" <?=$claimPendingChk?>>
								</TD>
								<td class="listing-item" align="left">Claims Pending</td>
							</TR>
							<TR>
								<TD>
									<INPUT type="checkbox" name="claimSettled" id="claimSettled" value="Y" class="chkBox" onclick="selectChk('claimSettled');" <?=$claimSettledChk?>>
								</TD>
								<td class="listing-item" align="left">Claims Settled</td>
							</TR>
							<TR>
								<TD>
									<INPUT type="checkbox" name="distributorAccount" id="distributorAccount" value="Y" class="chkBox" onclick="selectChk('distributorAccount');" <?=$distributorAccountChk?>>
								</TD>
								<td class="listing-item" align="left">Account Report</td>
							</TR>
							<TR>
								<TD>
									<INPUT type="checkbox" name="sampleInvoice" id="sampleInvoice" value="Y" class="chkBox" onclick="selectChk('sampleInvoice');displayQryType();" <?=$sampleInvoiceChk?>>
								</TD>
								<td class="listing-item" align="left">Sample Invoices</td>
								<td id="qryTypeRow">
									<select name="qryType">
										<option value="S" <? if ($qryType=='S') echo "selected";?>>Summary</option>
										<option value="D" <? if ($qryType=='D') echo "selected";?>>Detailed</option>
									</select>
								</td>
							</TR>
							<TR>
								<TD>
									<INPUT type="checkbox" name="distOverdue" id="distOverdue" value="Y" class="chkBox" onclick="selectChk('distOverdue');" <?=$distOverdueChk?>>
								</TD>
								<td class="listing-item" align="left">Overdue Report</td>
							</TR>
							<TR>
								<TD>
									<INPUT type="checkbox" name="distACStmnt" id="distACStmnt" value="Y" class="chkBox" onclick="selectChk('distACStmnt');" <?=$distACStmntChk?>>
								</TD>
								<td class="listing-item" align="left">Daily Account Statement</td>
							</TR>
							<TR>
								<TD>&nbsp;</TD>
								<td class="listing-item">
									<INPUT TYPE="submit" class="button" name="cmdSearch" value="Generate Report" onclick="return validateDistributorReport();">
								</td>
							</TR>
						</table>
						</fieldset>
					</td>
				</TR>
			</table>
		</TD>
	</tr>
	<? if ($succMsg) {?>
		<tr>
			<td colspan="3" nowrap class="successMsg" align="center" height="10"><strong><?=$succMsg?></strong></td>
		</tr>
	<? }?>	
	<tr>
        <td colspan="4" style="padding-left:10px; padding-right:10px;">
	<table cellpadding="2"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?php
	if (sizeof($distAccountRecs)>0) {
		$i	=	0;
	?>
	<tr bgcolor="White">
	<TD colspan="9" align="right" style="padding-left:10px; padding-right:10px;">
		<?php
			if ($recEditable) {
		?>
			<input type="submit" name="cmdConfirmRelease" class="button" value=" Confirm Release " onClick="return confirmChangeStatus('distACId_', '<?=sizeof($distAccountRecs)?>');">
		<?php
			}
		?>
	</TD></tr>
	<tr  bgcolor="#f2f2f2" align="center">
		<td width="20" nowrap>
			<INPUT type='checkbox' name='CheckAll' id='CheckAll' onClick="checkAll(this.form,'distACId_'); " class="chkBox">
		</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Date</td>
		<? if (!$distributorFilterId) {?>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Distributor</td>
		<? }?>
		<? if (!$cityFilterId) {?>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">City</td>
		<? }?>
		<? if (!$invoiceFilterId) {?>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">REF INVOICE</td>
		<? }?>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Reason</td>		
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">AMOUNT DUE<br>(Debit) (Rs.)</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">AMOUNT RECEIVED<br>(Credit) (Rs.)</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">CONFIRMED</td>
	</tr>
		<?php
		$totalCreditAmt = 0;
		$totalDebitAmt = 0;		
		foreach ($distAccountRecs as $dar) {
			$i++;			
			$distributorAccountId	= $dar[0];
			$selectDate		= dateFormat($dar[1]);
			$distributorName	= $dar[6];
			$particulars		= $dar[5];
			$amount			= $dar[3];
			$cod			= $dar[4];
			
			$creditAmt = 0;
			$debitAmt  = 0;	
			if ($cod=="C")  {				
				$creditAmt = number_format(abs($amount),2,'.','');
				$totalCreditAmt += abs($creditAmt);
			} else if ($cod=="D") {
		 		$debitAmt = number_format(abs($amount),2,'.','');
				$totalDebitAmt += abs($debitAmt);
			}

			$entryConfirmed = $dar[7];
			$rowDisabled = "";
			if ($entryConfirmed=="Y") {
				$rowDisabled = "disabled";
				$displayCellColor = "#90EE90"; // LightGreen
				$disctAcStatus = "YES";
			} else {
				$displayCellColor = "WHITE";
				$disctAcStatus = "NO";
			}

			$parentACId	= $dar[8];
			$acEntryType	= $dar[9];

			$pmtMode	= $paymentModeArr[$dar[10]];
			
			$chqRTGSNo	= $dar[11];
			$chqRTGSDate	= ($dar[12]!="0000-00-00")?dateFormat($dar[12]):"";
			/*
			$bankName	= $dar[13];
			$acNo		= $dar[14];
			$branchLocation 	= $dar[15];
			$depositedBankACNo	= $dar[16];
			*/

			$bankName	= $dar[32];
			$acNo		= $dar[33];
			$branchLocation 	= $dar[34];
			$depositedBankACNo	= $dar[31];
		
			$trValueDate	= ($dar[17]!="0000-00-00")?dateFormat($dar[17]):"";
			$selCityName	= $dar[20];

			$chequeReturnStatus 	= $dar[25];
			$dacChargeType  = $dar[27];
			$deReasonType    = $dar[28];
			if ($dacChargeType=="PRBC" || $dacChargeType=="CRBC") $selReasonName = "BANK CHARGES"; 
			else if ($dacChargeType=="CRPC") $selReasonName = "PENALTY CHARGES"; 
			else if ($trValueDate!="" && $chequeReturnStatus=='N' && $deReasonType=='PR') $selReasonName = "PAYMENT RECEIVED"; 
			else $selReasonName	= $dar[21];

			#Ref Invoice
			$referenceInvoiceRecs = array();
			if (!$invoiceFilterId) {
				//$referenceInvoiceRecs = $distributorAccountObj->getRefInvoices($distributorAccountId);	
			}
			
			$selCommonReasonId 	= $dar[22];
			$otherReasonDetails 	= $dar[23];
			if ($selCommonReasonId==0 && $otherReasonDetails!="") $selReasonName = $otherReasonDetails;
			$salesInvoiceId		= $dar[24];

			# PR Entry	
			$displayChkList = "";
			if ($selCommonReasonId!=0) {
				$acEntryType = $distributorAccountObj->DefaultReasonEntry($selCommonReasonId);
				list($selChkListRecs, $showChkList) = $distributorAccountObj->distChkList($distributorAccountId);
				if ($showChkList!="") $displayChkList = "onMouseover=\"ShowTip('$showChkList');\" onMouseout=\"UnTip();\" ";
			}

			$displayDetails	= "";
			$showPmnt  = "<table cellspacing=1 bgcolor=#999999 cellpadding=2>";
			if ($acEntryType=="PR" && !$parentACId) {
				// Main Row
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Payment mode</td><td class=listing-item>$pmtMode</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Cheque/RTGS No.</td><td class=listing-item>$chqRTGSNo</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Date</td><td class=listing-item>$chqRTGSDate</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Bank</td><td class=listing-item>$bankName</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Account no</td><td class=listing-item>$acNo</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Branch Location</td><td class=listing-item>$branchLocation</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Deposited Account</td><td class=listing-item>$depositedBankACNo</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Value date</td><td class=listing-item>$trValueDate</td></tr>";
			} 
			$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Particulars</td><td class=listing-item>$particulars</td></tr>";
			// Main Row Ends Here
			$showPmnt  .= "</table>";

			$showDebitEntry = "";
			if ($debitAmt!=0) $showDebitEntry = "onMouseover=\"ShowTip('$showPmnt');\" onMouseout=\"UnTip();\" ";

			$showCreditEntry = "";
			if ($creditAmt!=0) $showCreditEntry = "onMouseover=\"ShowTip('$showPmnt');\" onMouseout=\"UnTip();\" ";
			
			if ($entryConfirmed=="Y") {
				$displayDetails = "style=\"background-color: #ffffff;\" onMouseover=\"this.style.backgroundColor='#fde89f'\" onMouseout=\"this.style.backgroundColor='#ffffff'\" ";
			} else {
				$displayDetails = "style=\"background-color: #FFFFCC;\" onMouseover=\"this.style.backgroundColor='#fde89f'\" onMouseout=\"this.style.backgroundColor='#FFFFCC'\" ";
			}
		?>
		<tr <?=$listRowMouseOverStyle?> <?//=$displayDetails?>>
			<td width="20">
				<input type="checkbox" name="distACId_<?=$i;?>" id="distACId_<?=$i;?>" value="<?=$distributorAccountId;?>" class="chkBox">
				<input type="hidden" name="verified_<?=$i;?>" id="verified_<?=$i;?>" value="<?=$entryConfirmed;?>">
				<input type="hidden" name="salesInvoiceId_<?=$i;?>" id="salesInvoiceId_<?=$i;?>" value="<?=$salesInvoiceId;?>">		
			</td>
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$selectDate;?></td>
			<? if (!$distributorFilterId) {?>
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$distributorName;?></td>
			<?php }?>
			<? if (!$cityFilterId) {?>
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$selCityName;?></td>
			<?php }?>
			<? if (!$invoiceFilterId) {?>
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">
				<?php
					$numCol = 3;
					if (sizeof($referenceInvoiceRecs)>0) {
						$nextRec=	0;						
						$selName = "";
						foreach ($referenceInvoiceRecs as $r) {							
							$selName = $r[1];
							$nextRec++;
							if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $selName;
							if($nextRec%$numCol == 0) echo "<br/>";
						}
					}
				?>
			</td>
			<?php }?>			
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" <?=$displayChkList?>>
				<?=$selReasonName;?>
			</td>
			<!--<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" width="170" nowrap="true">
				<?//=$particulars?>
			</td>-->
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" <?=$showDebitEntry?>>
				<?=($debitAmt!=0)?"<a href='###' class='link5'>$debitAmt</a>":""?>
			</td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" <?=$showCreditEntry?>>
				<?=($creditAmt!=0)?"<a href='###' class='link5'>$creditAmt</a>":""?>
			</td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="center" bgcolor="<?=$displayCellColor?>">
				<?=$disctAcStatus?>
			</td>
			
		</tr>
		<?php
			}

			# Find Closing Balance Amt
			$closingBalAmt = $totalDebitAmt-$totalCreditAmt;
			if ($closingBalAmt>0) $closingCreditAmt = $closingBalAmt;
			else $closingDebitAmt = $closingBalAmt;

			if (!$distributorFilterId && !$cityFilterId && !$invoiceFilterId) $colSpan = 6;
			else if ($distributorFilterId && !$cityFilterId && !$invoiceFilterId) $colSpan = 5;
			else if (!$distributorFilterId && $cityFilterId && !$invoiceFilterId) $colSpan = 5;
			else if ($distributorFilterId && $cityFilterId && !$invoiceFilterId) $colSpan = 4;
			else if ($distributorFilterId && $cityFilterId && $invoiceFilterId) $colSpan = 3;
			else $colSpan = 4;			
		?>
		<!--<tr bgcolor="White">
			<TD colspan="<?=$colSpan?>" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">Total:</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($totalDebitAmt>0)?number_format($totalDebitAmt,2,'.',','):"";?></strong></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($totalCreditAmt>0)?number_format($totalCreditAmt,2,'.',','):"";?></strong></td>	
			<td></td>
		</tr>-->
		<!--<tr bgcolor="White">			
			<TD  colspan="<?=$colSpan?>" class="listing-item" style="padding-left:10px; padding-right:10px;" align="right" nowrap="true">Closing Balance:</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($closingDebitAmt!="")?number_format(abs($closingDebitAmt),2,'.',','):"";?></strong></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($closingCreditAmt!="")?number_format(abs($closingCreditAmt),2,'.',','):"";?></strong></td>	
			<td></td>
		</tr>-->	
		<!--<tr bgcolor="White">
			<TD colspan="<?=$colSpan?>" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">Total:</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=number_format(($totalDebitAmt+abs($closingDebitAmt)),2,'.',',')?></strong></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=number_format(($totalCreditAmt+abs($closingCreditAmt)),2,'.',',')?></strong></td>
			<td></td>	
		</tr>-->
			<input type="hidden" name="distACRowCount" id="distACRowCount" value="<?=$i?>" readonly />			
			<?php
				} else if ($chkChangeStatus) {
			?>
			<tr bgcolor="white">
				<td colspan="10"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
			</tr>	
			<?php
				}
			?>
	</table>
	</td>
	</tr>
	<?php
	if (sizeof($salesOrderRecords)>0 || sizeof($claimOrderRecords)>0 || sizeof($distributorAccountRecords)>0 || sizeof($sampleInvoiceRecords)>0 || sizeof($distOverdueRecs)>0 || sizeof($dailyACStmntRecs)>0) {
		$i=0;
	?>
	<tr>
               <td  height="5" colspan="4" style="padding-left:10px; padding-right:10px;" align="center">
		<? if ($print==true) {?>
		<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintDistributorReport.php?dateFrom=<?=$dateFrom?>&dateTill=<?=$dateTill?>&selDistributor=<?=$selDistributorId?>&pendingOrder=<?=$pendingOrder?>&orderDispatched=<?=$orderDispatched?>&claimPending=<?=$claimPending?>&claimSettled=<?=$claimSettled?>&distributorAccount=<?=$distributorAccount?>&sampleInvoice=<?=$sampleInvoice?>&qryType=<?=$qryType?>&distACStmnt=<?=$distACStmnt?>',700,600);">
		<? }?>
		</td>
		</tr>
	<tr>
               <td  height="10" colspan="4" style="padding-left:10px; padding-right:10px;">
		</td>
	</tr>
        <tr>
               <td colspan="4" style="padding-left:10px; padding-right:10px;">
		<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if (sizeof($salesOrderRecords)>0) {
		$i = 0;
	?>
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;" nowrap>Invoice No.</td>		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Total Amt</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Last Date</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Status</td>		
	</tr>
	<?
	foreach ($salesOrderRecords as $sor) {
		$i++;
		$salesOrderId	= $sor[0];
		$soNo		= $sor[1];
		// Find the Total Amount of Each Sales Order		
		$salesOrderTotalAmt = $sor[4];
		//$distributorName = $sor[5];

		$soInvoiceType		= $sor[13];
		$proformaNo	= $sor[14];
		$sampleNo	= $sor[15];
		$invoiceNo = "";
		if ($soNo!=0) $invoiceNo=$soNo;
		else if ($soInvoiceType=='T') $invoiceNo = "P$proformaNo";
		else if ($soInvoiceType=='S') $invoiceNo = "S$sampleNo";
			
		$selStatusId	= 	$sor[11];
		$completeStatus	= 	$sor[12];

		$currentDate	=	 date("Y-m-d");
		$cDate		=	explode("-",$currentDate);
		$d2 = mktime(22,0,0,$cDate[1],$cDate[2],$cDate[0]);

		$selLastDate	= 	$sor[5]; 	
		$eDate		=	explode("-", $selLastDate);
		$lastDate	=	$eDate[2]."/".$eDate[1]."/".$eDate[0];
		$d1=mktime(0,0,0,$eDate[1],$eDate[2],$eDate[0]);

		$dateDiff = floor(($d2-$d1)/86400);
		$status = "";
		$statusFlag	=	"";
		$extended	=	$sor[6];
		if ($extended=='E' && ($completeStatus=="" || $completeStatus=='P')) {			
			$status	= "PENDING (Extended)";
			$statusFlag =	'E';
		} else {			
			if ($completeStatus=='C') {
				$status	= " COMPLETED ";
				$statusFlag = 'C';
			} else if ($dateDiff>0) {				
				$status = "DELAYED";
				$statusFlag = 'D';
			} else {
				$status = "PENDING";
				$statusFlag = 'P';
			}
		}			
		/*******************************************************/
		$displayColor = "";
		if ($statusFlag=='C') $displayColor = "#90EE90"; // LightGreen
		else if ($statusFlag=='D') $displayColor = "#DD7500"; // LightOrange
		else if ($statusFlag=='E') $displayColor = "Grey";
		else $displayColor = "White";
	?>
	<tr  bgcolor="WHITE">		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center"><?=$invoiceNo;?></td>	
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$salesOrderTotalAmt;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$lastDate;?></td>
		<td class="listing-item" align="center" nowrap style="padding-left:10px; padding-right:10px;" bgcolor="<?=$displayColor?>"><?=$status?></td>				
	</tr>
	<?
		}
	?>
		
	<?
		} else if ($pendingOrder || $orderDispatched) {
	?>
	<tr bgcolor="white">
		<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>
	<?
		}
	?>
	</table>
	</td>
	</tr>
	<tr>
               <td colspan="4" style="padding-left:10px; padding-right:10px;">
	<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if (sizeof($claimOrderRecords)>0) {
		$i = 0;
	?>

	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Claim <br>Number</td>		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Sales Order Number</td>	
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Claim Type</td>	
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Amount</td>	
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Last Date</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Status</td>		
	</tr>
	<?
	foreach ($claimOrderRecords as $cor) {
		$i++;
		$claimOrderId	= $cor[0];
		$claimNumber	= $cor[1];
		
		/********************************************************/		
		$selStatusId	= 	$cor[8];

		$currentDate	=	 date("Y-m-d");
		$cDate		=	explode("-",$currentDate);
		$d2 = mktime(22,0,0,$cDate[1],$cDate[2],$cDate[0]);

		$selLastDate	= 	$cor[3]; 	
		$eDate		=	explode("-", $selLastDate);
		$lastDate	=	$eDate[2]."/".$eDate[1]."/".$eDate[0];
		$d1=mktime(0,0,0,$eDate[1],$eDate[2],$eDate[0]);

		$dateDiff = floor(($d2-$d1)/86400);
		$status = "";
		$statusFlag	=	"";
		$extended	=	$cor[4];
		if ($extended=='E' && ($selStatusId=="" || $selStatusId==0)) {
			$status	=	"<span class='err1'>Extended & Pending </span>";
			$statusFlag =	'E';
		} else {
			if ($statusObj->findStatus($selStatusId)) {
				$status	=	$statusObj->findStatus($selStatusId);
			} else if ($dateDiff>0) {
				$status 	= "<span class='err1'>Delayed</span>";
				$statusFlag =	'D';
			} else {
				$status = "Pending";
			}
		}				
		/*******************************************************/
		# Get Sales Order numbers
		$getSORecords = $claimObj->getClaimSORecords($claimOrderId);	

		$cType		= $cor[10];
		$claimType	= ($cType=='MR')?"Material Return":"Fixed Amount";	
		
		$fixedAmt	= $cor[12];
		$mrAmt		= $cor[13];
		$displayAmt = ($cType=='MR')?$mrAmt:$fixedAmt;

	?>
	<tr  bgcolor="WHITE">		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$claimNumber;?></td>		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="left">
			<table>
			<tr>
				<?
					$numColumn	=	3;
					if (sizeof($getSORecords)>0) {
						$nextRec	=	0;
						$k=0;
						foreach($getSORecords as $soR) {
							$j++;
							$soNumber=	$soR[2];
							$nextRec++;
				?>
				<td class="listing-item">
					<? if($nextRec>1) echo ",";?><?=$soNumber?>
				</td>
				<? 
					if($nextRec%$numColumn == 0) {
				?>
			</tr>
			<tr>
				<? 
					}	
				}
				}
				?>
			</tr>
			</table>
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="left"><?=$claimType;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$displayAmt;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$lastDate;?></td>
		<td class="listing-item" align="center" nowrap style="padding-left:10px; padding-right:10px;"><?=$status?></td>				
	</tr>
	<?
		}
	?>	
	<?
		} else if ($claimPending || $claimSettled) {
			
	?>
	<tr bgcolor="white">
		<td colspan="6"  class="err1" height="10" align="center">No Claim Records Found<?//$msgNoRecords;?></td>
	</tr>
	<?
		}
	?>
	</table>
		</td>
	</tr>
	<tr>
               <td  colspan="4" style="padding-left:10px; padding-right:10px;">
			<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if (sizeof($distributorAccountRecords)>0) {
		$i	=	0;
	?>	
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Date</td>		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Particulars</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Debit<br>(In Rs.)</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Credit<br>(In Rs.)</td>	
	</tr>
		<?php
		$totalCreditAmt = 0;
		$totalDebitAmt = 0;
		//foreach ($distributorAccountRecords as $dar) {
		foreach ($distributorAccountRecords as $key=>$dar) {	
			$i++;
			//$distributorAccountId	= $dar[0];
			$selectDate		= dateFormat($dar[0]);			
			$particulars		= $dar[1];
			$amount			= $dar[2];
			$cod			= $dar[3];				
			$creditAmt = 0;
			$debitAmt  = 0;	
			if ($cod=="C")  {				
				$creditAmt = number_format(abs($amount),2,'.','');
				$totalCreditAmt += abs($creditAmt);
			} else if ($cod=="D") {
		 		$debitAmt = number_format(abs($amount),2,'.','');
				$totalDebitAmt += abs($debitAmt);
			}

			
		?>
		<tr  bgcolor="WHITE">
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selectDate;?></td>			
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="left" nowrap="true">
				<?=$particulars?>
			</td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
				<?=($debitAmt!=0)?$debitAmt:""?>
			</td>	
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right">
				<?=($creditAmt!=0)?$creditAmt:""?>
			</td>
		</tr>
		<?
			}
			# Find Closing Balance Amt
			$closingBalAmt = $totalDebitAmt-$totalCreditAmt;

			if ($closingBalAmt>0) $closingCreditAmt = $closingBalAmt;
			else $closingDebitAmt = $closingBalAmt;
		?>
		<tr bgcolor="White">
			<TD colspan="2" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">Total:</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($totalDebitAmt>0)?number_format($totalDebitAmt,2,'.',''):"";?></strong></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($totalCreditAmt>0)?number_format($totalCreditAmt,2,'.',''):"";?></strong></td>	
		</tr>
		<tr bgcolor="White">
			<!--<TD class="listing-head" style="padding-left:10px; padding-right:10px;" align="right"></TD>-->	
			<TD  colspan="2" class="listing-item" style="padding-left:10px; padding-right:10px;" align="right" nowrap="true">Closing Balance:</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($closingDebitAmt!="")?number_format(abs($closingDebitAmt),2,'.',''):"";?></strong></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($closingCreditAmt!="")?number_format(abs($closingCreditAmt),2,'.',''):"";?></strong></td>	
		</tr>	
		<tr bgcolor="White">
			<TD colspan="2" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">Total:</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=number_format(($totalDebitAmt+abs($closingDebitAmt)),2,'.','')?></strong></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=number_format(($totalCreditAmt+abs($closingCreditAmt)),2,'.','')?></strong></td>	
		</tr>
		<?
			} else if ($distributorAccount!="") {
		?>
		<tr bgcolor="white">
			<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
		</tr>	

											<?
												}
											?>
										</table>
		</td>
	</tr>
<!--  Sample Invoice Report Starts here-->
	<tr>
               <td colspan="4" style="padding-left:10px; padding-right:10px;">
		<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?php
	if (sizeof($sampleInvoiceRecords)>0) {
		$i = 0;
	?>
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;" nowrap>Date</td>
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;" nowrap>Invoice No.</td>		
		<?php
			if ($qryType=='D') {
		?>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Product</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Qty</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Amt</td>
		<?php
			} else {
		?>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Total Amt</td>		
		<?php
			}
		?>
	</tr>
	<?
	$grandTotalSOAmt = 0;
	foreach ($sampleInvoiceRecords as $sor) {
		$i++;
		$salesOrderId	= $sor[0];
		$poId		= $sor[1];
		$invoiceDate	= dateFormat($sor[3]);
		// Find the Total Amount of Each Sales Order		
		//$salesOrderTotalAmt = $sor[4];
			

		$selStatusId	= 	$sor[11];
		$completeStatus	= 	$sor[12];

		$currentDate	=	 date("Y-m-d");
		$cDate		=	explode("-",$currentDate);
		$d2 = mktime(22,0,0,$cDate[1],$cDate[2],$cDate[0]);

		$selLastDate	= 	$sor[5]; 	
		$eDate		=	explode("-", $selLastDate);
		$lastDate	=	$eDate[2]."/".$eDate[1]."/".$eDate[0];
		$d1=mktime(0,0,0,$eDate[1],$eDate[2],$eDate[0]);

		$dateDiff = floor(($d2-$d1)/86400);
		$status = "";
		$statusFlag	=	"";
		$extended	=	$sor[6];
		if ($extended=='E' && ($completeStatus=="" || $completeStatus=='P')) {			
			$status	= "PENDING (Extended)";
			$statusFlag =	'E';
		} else {			
			if ($completeStatus=='C') {
				$status	= " COMPLETED ";
				$statusFlag = 'C';
			} else if ($dateDiff>0) {				
				$status = "DELAYED";
				$statusFlag = 'D';
			} else {
				$status = "PENDING";
				$statusFlag = 'P';
			}
		}			
		/*******************************************************/
		$displayColor = "";
		if ($statusFlag=='C') $displayColor = "#90EE90"; // LightGreen
		else if ($statusFlag=='D') $displayColor = "#DD7500"; // LightOrange
		else if ($statusFlag=='E') $displayColor = "Grey";
		else $displayColor = "White";

		if ($qryType=='D') {
			$productId 	= $sor[14];
			$productRec	= $manageProductObj->find($productId);
			$productName	= $productRec[2];
			$orderedQty     = $sor[16];
			$totalAmt	= $sor[17];
			$grandTotalSOAmt += 	$totalAmt;
		} else {
			// Find the Total Amount of Each Sales Order
			$salesOrderTotalAmt = $salesOrderObj->getSalesOrderAmount($salesOrderId);
			$grandTotalSOAmt += 	$salesOrderTotalAmt;
		}
	?>
	<tr  bgcolor="WHITE">		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$invoiceDate;?></td>	
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$poId;?></td>	
		<?php
			if ($qryType=='D') {
		?>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="left"><?=$productName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$orderedQty;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$totalAmt;?></td>
		<?php
			} else {
		?>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$salesOrderTotalAmt;?></td>
		<?
			}
		?>
	</tr>
	<?
		}
	?>
	<?php
		if ($qryType=='D') $colspan = 4;
		else $colspan = 2;
	?>
	<tr  bgcolor="WHITE">		
		<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;" colspan="<?=$colspan?>" align="right">Total:</td>	
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><strong><?=$grandTotalSOAmt;?></strong></td>					
	</tr>	
	<?
		} else if ($sampleInvoice) {
	?>
	<tr bgcolor="white">
		<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>
	<?
		}
	?>
	</table>
	</td>
	</tr>
<!--  Sample Invoice Ends Here-->
<!-- Overdue Report starts here	 -->
<?php
	if ($distOverdueChk) {

		$cityFilterId = true;
		$distributorFilterId = true;
?>
	<tr>
        	<td  colspan="4" style="padding-left:10px; padding-right:10px;" align="center">
	<table cellpadding="2"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?php
	if (sizeof($distOverdueRecs)>0) {
		$i	=	0;
	?>
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Date</td>
		<? if (!$distributorFilterId) {?>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Distributor</td>
		<? }?>
		<? if (!$cityFilterId) {?>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">City</td>
		<? }?>
		<? if (!$invoiceFilterId) {?>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">REF INVOICE</td>
		<? }?>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Reason</td>
		<!--<td class="listing-head" style="padding-left:5px; padding-right:5px;">Particulars</td>-->		
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">AMOUNT DUE<br>(Debit) (Rs.)</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">AMOUNT RECEIVED<br>(Credit) (Rs.)</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">BAL DUE<br> (Rs.)</td>
		
	</tr>
		<?php
		$totalCreditAmt = 0;
		$totalDebitAmt = 0;		
		if ($distributorFilterId && !$invoiceFilterId && $openingBalanceAmt!=0 && !$reasonFilterIds && $filterType!="PE" ) {
			if ($postType=="C")  {								
				$totalCreditAmt += abs($openingBalanceAmt);
				$grandTotalCreditAmt += abs($openingBalanceAmt);
			} else if ($postType=="D") {		 		
				$totalDebitAmt += abs($openingBalanceAmt);
				$grandTotalDebitAmt += abs($openingBalanceAmt);
			}
		?>
		<tr  bgcolor="WHITE">			
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$dateFrom;?></td>	
			<? if (!$distributorFilterId) {?>
			<td>&nbsp;</td>
			<? }?>
			<? if (!$cityFilterId) {?>
			<td>&nbsp;</td>
			<? }?>
			<? if (!$invoiceFilterId) {?>	
			<td>&nbsp;</td>
			<? }?>
			<!--<td>&nbsp;</td>-->
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" width="170" nowrap="true">
				Opening Balance
			</td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right">
				<?=($postType=='D')?number_format($openingBalanceAmt,2,'.',''):"&nbsp;"?>
			</td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right">
				<?=($postType=='C')?number_format($openingBalanceAmt,2,'.',''):"&nbsp;"?>
			</td>			
		</tr>
		<?		
			}
		?>
		<?php		
		$amtArr	= array();
		$totalBalDueAmt = 0;
		foreach ($distOverdueRecs as $dar) {
			$i++;			
			$distributorAccountId	= $dar[0];
			$selectDate		= dateFormat($dar[1]);
			$distributorName	= $dar[6];
			$particulars		= $dar[5];
			$amount			= $dar[3];
			$cod			= $dar[4];
			
			$creditAmt = 0;
			$debitAmt  = 0;	
			if ($cod=="C")  {				
				$creditAmt = number_format(abs($amount),2,'.','');
				$totalCreditAmt += abs($creditAmt);
			} else if ($cod=="D") {
		 		$debitAmt = number_format(abs($amount),2,'.','');
				$totalDebitAmt += abs($debitAmt);
			}

			$entryConfirmed = $dar[7];
			$rowDisabled = "";
			if ($entryConfirmed=="Y") $rowDisabled = "disabled";
			$parentACId	= $dar[8];
			$acEntryType	= $dar[9];

			$pmtMode	= $paymentModeArr[$dar[10]];
			//$pmtDescription = $pmtMode;
			//if ($pmtMode!="CH") $pmtDescription .= " No: $chqRtgsNo";

			$chqRTGSNo	= $dar[11];
			$chqRTGSDate	= ($dar[12]!="0000-00-00")?dateFormat($dar[12]):"";
			/*
			$bankName	= $dar[13];
			$acNo		= $dar[14];
			$branchLocation 	= $dar[15];
			$depositedBankACNo	= $dar[16];
			*/
			$bankName	= $dar[33];
			$acNo		= $dar[34];
			$branchLocation 	= $dar[35];
			$depositedBankACNo	= $dar[32];

			$trValueDate	= ($dar[17]!="0000-00-00")?dateFormat($dar[17]):"";
			if ($trValueDate!="") $amtArr[$cod] +=  $amount;

			$dacBankCharge = $dar[18];
			$dacBankChargeDescription =  $dar[19];

			$selCityName	= $dar[20];
			$chequeReturnStatus 	= $dar[24];
			$chequeReturnEntryId 	= $dar[25];

			$dacChargeType  = $dar[26];
			$deReasonType    = $dar[28];
			if ($dacChargeType=="PRBC") 	$selReasonName = "BANK CHARGES"; 
			else if ($dacChargeType=="CRBC") $selReasonName = "BANK CHARGES"; 
			else if ($dacChargeType=="CRPC") $selReasonName = "PENALTY CHARGES"; 
			else if ($trValueDate!="" && $chequeReturnStatus=='N' && $deReasonType=='PR') $selReasonName = "PAYMENT RECEIVED"; 
			else $selReasonName	= $dar[21];

			#Ref Invoice
			$referenceInvoiceRecs = array();
			if (!$invoiceFilterId) {
				$referenceInvoiceRecs = $distributorAccountObj->getRefInvoices($distributorAccountId);	
			}
			
			$selCommonReasonId 	= $dar[22];
			$otherReasonDetails 	= $dar[23];
			if ($selCommonReasonId==0 && $otherReasonDetails!="") $selReasonName = $otherReasonDetails;
			# PR Entry	
			$displayChkList = "";
			if ($selCommonReasonId!=0) {
				$acEntryType = $distributorAccountObj->DefaultReasonEntry($selCommonReasonId);
				list($selChkListRecs, $showChkList) = $distributorAccountObj->distChkList($distributorAccountId);
				if ($showChkList!="") $displayChkList = "onMouseover=\"ShowTip('$showChkList');\" onMouseout=\"UnTip();\" ";
			}

			$displayDetails	= "";
			$displayPopup = false;
			$showPmnt  = "<table cellspacing=1 bgcolor=#999999 cellpadding=2>";
			if ($acEntryType=="PR" && !$parentACId) {
				// Main Row
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Payment mode</td><td class=listing-item>$pmtMode</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Cheque/RTGS No.</td><td class=listing-item>$chqRTGSNo</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Date</td><td class=listing-item>$chqRTGSDate</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Bank</td><td class=listing-item>$bankName</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Account no</td><td class=listing-item>$acNo</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Branch Location</td><td class=listing-item>$branchLocation</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Deposited Account</td><td class=listing-item>$depositedBankACNo</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Value date</td><td class=listing-item>$trValueDate</td></tr>";
				$displayPopup = true;
			} 
			if ($particulars!="") {
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Particulars</td><td class=listing-item>".trim($particulars)."</td></tr>";
				$displayPopup = true;
			}
			// Main Row Ends Here
			$showPmnt  .= "</table>";

			$showDebitEntry = "";
			if ($debitAmt!=0 && $displayPopup) $showDebitEntry = "onMouseover=\"ShowTip('$showPmnt');\" onMouseout=\"UnTip();\" ";

			$showCreditEntry = "";
			if ($creditAmt!=0 && $displayPopup) $showCreditEntry = "onMouseover=\"ShowTip('$showPmnt');\" onMouseout=\"UnTip();\" ";
			
			$displayRefInvMsg = "";
			if ($entryConfirmed=="Y") {
				$displayDetails = "style=\"background-color: #ffffff;\" onMouseover=\"this.style.backgroundColor='#fde89f'\" onMouseout=\"this.style.backgroundColor='#ffffff'\" ";
			} else if (sizeof($referenceInvoiceRecs)<=0) {
				$displayDetails = "style=\"background-color: #fbb79f;\" onMouseover=\"this.style.backgroundColor='#fde89f'\" onMouseout=\"this.style.backgroundColor='#fbb79f'\" ";
				$displayRefInvMsg = "onMouseover=\"ShowTip('Please assign a invoice.');\" onMouseout=\"UnTip();\" ";
			} else {
				$displayDetails = "style=\"background-color: #FFFFCC;\" onMouseover=\"this.style.backgroundColor='#fde89f'\" onMouseout=\"this.style.backgroundColor='#FFFFCC'\" ";
			}

			$balDueAmt = $dar[29];
			$totalBalDueAmt += $balDueAmt;
		?>
		<tr <?//=$listRowMouseOverStyle?> <?=$displayDetails?>>			
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$selectDate;?></td>
			<? if (!$distributorFilterId) {?>
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$distributorName;?></td>
			<?php }?>
			<? if (!$cityFilterId) {?>
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$selCityName;?></td>
			<?php }?>
			<? if (!$invoiceFilterId) {?>
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" <?=$displayRefInvMsg?>>
				<?php
					$numCol = 3;
					if (sizeof($referenceInvoiceRecs)>0) {
						$nextRec=	0;						
						$selName = "";
						foreach ($referenceInvoiceRecs as $r) {							
							$selName = $r[1];
							$nextRec++;
							if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $selName;
							if($nextRec%$numCol == 0) echo "<br/>";
						}
					}
				?>
			</td>
			<?php }?>			
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" <?=$displayChkList?>>
				<?=$selReasonName;?>
			</td>
			<!--<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" width="170" nowrap="true">
				<?//=$particulars?>
			</td>-->
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" <?=$showDebitEntry?>>
				<?=($debitAmt!=0)?(($displayPopup)?"<a href='###' class='link5'>$debitAmt</a>":$debitAmt):""?>
			</td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" <?=$showCreditEntry?>>
				<?=($creditAmt!=0 )?(($displayPopup)?"<a href='###' class='link5'>$creditAmt</a>":$creditAmt):""?>
			</td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=($balDueAmt!=0)?$balDueAmt:""?></td>				
		</tr>
		<?php
			}

			# Find Closing Balance Amt
			//$closingBalAmt = $totalDebitAmt-$totalCreditAmt;
			$closingBalAmt = $grandTotalDebitAmt-$grandTotalCreditAmt;			
			if ($closingBalAmt>0) $closingCreditAmt = $closingBalAmt;
			else $closingDebitAmt = $closingBalAmt;
			

			if (!$distributorFilterId && !$cityFilterId && !$invoiceFilterId) $colSpan = 5;
			else if ($distributorFilterId && !$cityFilterId && !$invoiceFilterId) $colSpan = 4;
			else if (!$distributorFilterId && $cityFilterId && !$invoiceFilterId) $colSpan = 4;
			else if ($distributorFilterId && $cityFilterId && !$invoiceFilterId) $colSpan = 3;
			else if ($distributorFilterId && $cityFilterId && $invoiceFilterId) $colSpan = 2;
			else $colSpan = 3;
			
			$overdueAmt = $amtArr["D"]-$amtArr["C"];
		?>
		<tr bgcolor="White">
			<TD colspan="<?=$colSpan?>" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">Total:</TD>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><strong><?=($totalDebitAmt>0)?number_format($totalDebitAmt,2,'.',','):"";?></strong></td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right">
				<strong><?=($totalCreditAmt>0)?number_format($totalCreditAmt,2,'.',','):"";?></strong>
			</td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right">
				<strong><?=($totalBalDueAmt>0)?number_format($totalBalDueAmt,2,'.',','):"";?></strong>
			</td>			
		</tr>
		<tr bgcolor="White">
			<TD colspan="<?=$colSpan?>" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">
				OVERDUE AMT:
			</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="left" colspan="3"><strong><?=($overdueAmt>0)?number_format($overdueAmt,2,'.',','):"";?></strong></td>				
		</tr>
		
		<?php
		if ($maxpage==$pageNo && $filterType!="PE") {			
			$grandTotalDebitAmt 	+= abs($closingDebitAmt);
			$grandTotalCreditAmt 	+= abs($closingCreditAmt);
		?>
		<tr bgcolor="White" style="display:none;">			
			<TD  colspan="<?=$colSpan?>" class="listing-item" style="padding-left:10px; padding-right:10px;" align="right" nowrap="true">Closing Balance:</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($closingDebitAmt!="")?number_format(abs($closingDebitAmt),2,'.',','):"";?></strong></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($closingCreditAmt!="")?number_format(abs($closingCreditAmt),2,'.',','):"";?></strong></td>
			<td>&nbsp;</td>	
		</tr>
		<?php
			} 
		?>

		<?php
		if ($maxpage==1 && $filterType!="PE") {
		?>	
		<tr bgcolor="White">
			<TD colspan="<?=$colSpan?>" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">Total:</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=number_format(($totalDebitAmt+abs($closingDebitAmt)),2,'.',',')?></strong></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=number_format(($totalCreditAmt+abs($closingCreditAmt)),2,'.',',')?></strong></td>
			<td></td>	
		</tr>
		<?php
			} 
		?>
		
		<?php
		if ($maxpage>1) {
		?>
		<tr bgcolor="White">
			<TD colspan="<?=$colSpan?>" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">Grand Total:</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=number_format($grandTotalDebitAmt,2,'.',',')?></strong></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=number_format($grandTotalCreditAmt,2,'.',',')?></strong></td>
			<td></td>	
		</tr>
		<?php
		}
		?>
		<? 
			} else {
		?>
		<tr bgcolor="white">
			<td colspan="10"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
		</tr>	
		<?
			}
		?>
		</table>	
	</td>
	</tr>
<?php
	} // Overdue chk condition ends here
?>
<!-- Overdue Report Ends here -->

<!-- DAILY ACCOUNT STATEMENT Starts here -->
<?php	
	if ($distACStmntChk) {	
?>
	<tr>
        	<td  colspan="4" style="padding-left:10px; padding-right:10px;" align="center">
<table cellpadding="2"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?php
	if (sizeof($dailyACStmntRecs)>0) {
		$i	=	0;
	?>	
	<tr  bgcolor="#f2f2f2" align="center">	
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Date</td>
		<? if (!$distributorFilterId) {?>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Distributor</td>
		<? }?>
		<? if (!$cityFilterId) {?>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">City</td>
		<? }?>
		<? if (!$invoiceFilterId) {?>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">REF INVOICE</td>
		<? }?>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Reason</td>		
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">Debit<br>(Rs.)</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">Credit<br/>(Rs.)</td>		
	</tr>
		<?php
		$totalCreditAmt = 0;
		$totalDebitAmt = 0;
		//&& ($cityFilterId || $distCityFilterRecSize==1)
		if ($distributorFilterId && !$invoiceFilterId && $openingBalanceAmt!=0 && !$reasonFilterIds && $filterType=="VE" && $pageNo==1) {
			if ($postType=="C")  {								
				$totalCreditAmt += abs($openingBalanceAmt);
				//$grandTotalCreditAmt += abs($openingBalanceAmt);
			} else if ($postType=="D") {		 		
				$totalDebitAmt += abs($openingBalanceAmt);
				//$grandTotalDebitAmt += abs($openingBalanceAmt);
			}
		?>
		<tr  bgcolor="WHITE">
			<td width="20">&nbsp;</td>
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$dateFrom;?></td>	
			<? if (!$distributorFilterId) {?>
			<td>&nbsp;</td>
			<? }?>
			<? if (!$cityFilterId) {?>
			<td>&nbsp;</td>
			<? }?>
			<? if (!$invoiceFilterId) {?>	
			<td>&nbsp;</td>
			<? }?>
			<!--<td>&nbsp;</td>-->
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" width="170" nowrap="true">
				Opening Balance
			</td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right">
				<?=($postType=='D')?number_format($openingBalanceAmt,2,'.',''):"&nbsp;"?>
			</td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right">
				<?=($postType=='C')?number_format($openingBalanceAmt,2,'.',''):"&nbsp;"?>
			</td>
		</tr>
		<?		
			}
		?>
		<?php		
		$distributorAccountId = "";
		foreach ($dailyACStmntRecs as $dar) {
			$i++;			
			$distributorAccountId	= $dar[0];
			$selectDate		= dateFormat($dar[1]);
			$distributorName	= $dar[6];
			$particulars		= $dar[5];
			$amount			= $dar[3];
			$cod			= $dar[4];
			
			$creditAmt = 0;
			$debitAmt  = 0;	
			if ($cod=="C")  {				
				$creditAmt = number_format(abs($amount),2,'.','');
				$totalCreditAmt += abs($creditAmt);
			} else if ($cod=="D") {
		 		$debitAmt = number_format(abs($amount),2,'.','');
				$totalDebitAmt += abs($debitAmt);
			}

			$entryConfirmed = $dar[7];
			$rowDisabled = "";
			if ($entryConfirmed=="Y") $rowDisabled = "disabled";
			$parentACId	= $dar[8];
			$acEntryType	= $dar[9];

			$pmtMode	= $paymentModeArr[$dar[10]];			

			$chqRTGSNo	= $dar[11];
			$chqRTGSDate	= ($dar[12]!="0000-00-00")?dateFormat($dar[12]):"";
			
			/*
			$bankName	= $dar[13];
			$acNo		= $dar[14];
			$branchLocation 	= $dar[15];
			$depositedBankACNo	= $dar[16];
			*/
			$bankName	= $dar[31];
			$acNo		= $dar[32];
			$branchLocation 	= $dar[33];
			$depositedBankACNo	= $dar[30];

			$trValueDate	= ($dar[17]!="0000-00-00")?dateFormat($dar[17]):"";

			$dacBankCharge = $dar[18];
			$dacBankChargeDescription =  $dar[19];

			$selCityName	= $dar[20];
			$chequeReturnStatus 	= $dar[24];
			$chequeReturnEntryId 	= $dar[25];

			 $dacChargeType  = $dar[26];
			 $deReasonType    = $dar[27];
			if ($dacChargeType=="PRBC" || $dacChargeType=="CRBC") 	$selReasonName = "BANK CHARGES";
			else if ($dacChargeType=="CRPC") $selReasonName = "PENALTY CHARGES"; 
			else if ($trValueDate!="" && $chequeReturnStatus=='N' && $deReasonType=='PR') $selReasonName = "PAYMENT RECEIVED"; 
			else $selReasonName	= $dar[21];

			# Check Advance entry exist
			$refInvAdvEntryExist = $distributorAccountObj->chkBalAdvPmtEntryExist($distributorAccountId);
			if ($refInvAdvEntryExist) $selReasonName .= "<br>(Adv amt adjust is pending)";
			
			#Ref Invoice
			$referenceInvoiceRecs = array();
			if (!$invoiceFilterId) {
				$referenceInvoiceRecs = $distributorAccountObj->getRefInvoices($distributorAccountId);	
			}
			
			$selCommonReasonId 	= $dar[22];
			$otherReasonDetails 	= $dar[23];
			if ($selCommonReasonId==0 && $otherReasonDetails!="") $selReasonName = $otherReasonDetails;
			# PR Entry	
			$displayChkList = "";
			if ($selCommonReasonId!=0) {
				$acEntryType = $distributorAccountObj->DefaultReasonEntry($selCommonReasonId);
				list($selChkListRecs, $showChkList) = $distributorAccountObj->distChkList($distributorAccountId);
				if ($showChkList!="") $displayChkList = "onMouseover=\"ShowTip('$showChkList');\" onMouseout=\"UnTip();\" ";
			}

			$displayDetails	= "";
			$displayPopup = false;
			$showPmnt  = "<table cellspacing=1 bgcolor=#999999 cellpadding=2>";
			if (($acEntryType=="PR" || $deReasonType=='AP') && !$parentACId ) {
				// Main Row
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Payment mode</td><td class=listing-item>$pmtMode</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Cheque/RTGS No.</td><td class=listing-item>$chqRTGSNo</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Date</td><td class=listing-item>$chqRTGSDate</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Bank</td><td class=listing-item>$bankName</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Account no</td><td class=listing-item>$acNo</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Branch Location</td><td class=listing-item>$branchLocation</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Deposited Account</td><td class=listing-item>$depositedBankACNo</td></tr>";
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Value date</td><td class=listing-item>$trValueDate</td></tr>";
				$displayPopup = true;
			} 
			if ($particulars!="") {
				$showPmnt .= "<tr bgcolor=#fffbcc><td class=listing-head>Particulars</td><td class=listing-item>".trim($particulars)."</td></tr>";
				$displayPopup = true;
			}
			// Main Row Ends Here
			$showPmnt  .= "</table>";

			$showDebitEntry = "";
			if ($debitAmt!=0 && $displayPopup) $showDebitEntry = "onMouseover=\"ShowTip('$showPmnt');\" onMouseout=\"UnTip();\" ";

			$showCreditEntry = "";
			if ($creditAmt!=0 && $displayPopup) $showCreditEntry = "onMouseover=\"ShowTip('$showPmnt');\" onMouseout=\"UnTip();\" ";
			
			$displayRefInvMsg = "";
			if ($entryConfirmed=="Y") {
				$displayDetails = "style=\"background-color: #ffffff;\" onMouseover=\"this.style.backgroundColor='#fde89f'\" onMouseout=\"this.style.backgroundColor='#ffffff'\" ";
			} else if (sizeof($referenceInvoiceRecs)<=0 && !$invoiceFilterId) {
				$displayDetails = "style=\"background-color: #fbb79f;\" onMouseover=\"this.style.backgroundColor='#fde89f'\" onMouseout=\"this.style.backgroundColor='#fbb79f'\" ";
				$displayRefInvMsg = "onMouseover=\"ShowTip('Please assign a invoice.');\" onMouseout=\"UnTip();\" ";
			} else {
				$displayDetails = "style=\"background-color: #FFFFCC;\" onMouseover=\"this.style.backgroundColor='#fde89f'\" onMouseout=\"this.style.backgroundColor='#FFFFCC'\" ";
			}

			
		?>
		<tr <?=$displayDetails?>>
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$selectDate;?></td>
			<? if (!$distributorFilterId) {?>
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$distributorName;?></td>
			<?php }?>
			<? if (!$cityFilterId) {?>
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$selCityName;?></td>
			<?php }?>
			<? if (!$invoiceFilterId) {?>
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" <?=$displayRefInvMsg?>>
				<?php
					$numCol = 3;
					if (sizeof($referenceInvoiceRecs)>0) {
						$nextRec=	0;						
						$selName = "";
						foreach ($referenceInvoiceRecs as $r) {							
							$selName = $r[1];
							$nextRec++;
							if($nextRec>1) echo "&nbsp;,&nbsp;"; echo $selName;
							if($nextRec%$numCol == 0) echo "<br/>";
						}
					}
				?>
			</td>
			<?php }?>			
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" <?=$displayChkList?>>
				<?=$selReasonName;?>
			</td>
			<!--<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" width="170" nowrap="true">
				<?//=$particulars?>
			</td>-->
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" <?=$showDebitEntry?>>
				<?=($debitAmt!=0)?(($displayPopup)?"<a href='###' class='link5'>$debitAmt</a>":$debitAmt):""?>
			</td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" <?=$showCreditEntry?>>
				<?=($creditAmt!=0 )?(($displayPopup)?"<a href='###' class='link5'>$creditAmt</a>":$creditAmt):""?>
			</td>			
		</tr>
		<?php
			}
			# Find Closing Balance Amt
			
			$closingBalAmt = $grandTotalDebitAmt-$grandTotalCreditAmt;			
			if ($closingBalAmt>0) $closingCreditAmt = $closingBalAmt;
			else $closingDebitAmt = $closingBalAmt;
			
			if (!$distributorFilterId && !$cityFilterId && !$invoiceFilterId) $colSpan = 5;
			else if ($distributorFilterId && !$cityFilterId && !$invoiceFilterId) $colSpan = 4;
			else if (!$distributorFilterId && $cityFilterId && !$invoiceFilterId) $colSpan = 4;
			else if ($distributorFilterId && $cityFilterId && !$invoiceFilterId) $colSpan = 3;
			else if ($distributorFilterId && $cityFilterId && $invoiceFilterId) $colSpan = 2;
			else $colSpan = 3;
			
		?>
		<tr bgcolor="White">
			<TD colspan="<?=$colSpan?>" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">Total:</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($totalDebitAmt>0)?number_format($totalDebitAmt,2,'.',','):"";?></strong></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($totalCreditAmt>0)?number_format($totalCreditAmt,2,'.',','):"";?></strong></td>
		</tr>
		<?php
		if ($maxpage==$pageNo && $filterType=="VE") {
			$grandTotalDebitAmt 	+= abs($closingDebitAmt);
			$grandTotalCreditAmt 	+= abs($closingCreditAmt);
		?>
		<tr bgcolor="White">			
			<TD  colspan="<?=$colSpan?>" class="listing-item" style="padding-left:10px; padding-right:10px;" align="right" nowrap="true">Closing Balance:</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($closingDebitAmt!="")?number_format(abs($closingDebitAmt),2,'.',','):"";?></strong></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($closingCreditAmt!="")?number_format(abs($closingCreditAmt),2,'.',','):"";?></strong></td>
		</tr>
		<?php
			} 
		?>

		<?php
		if ($maxpage==1 && $filterType=="VE") {
		?>	
		<tr bgcolor="White">
			<TD colspan="<?=$colSpan?>" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">Total:</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=number_format(($totalDebitAmt+abs($closingDebitAmt)),2,'.',',')?></strong></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=number_format(($totalCreditAmt+abs($closingCreditAmt)),2,'.',',')?></strong></td>
		</tr>
		<?php
			} 
		?>
		
		<?php
		if ($maxpage>1) {
		?>
		<tr bgcolor="White">
			<TD colspan="<?=$colSpan?>" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">Grand Total:</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=number_format($grandTotalDebitAmt,2,'.',',')?></strong></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=number_format($grandTotalCreditAmt,2,'.',',')?></strong></td>				
		</tr>
		<?php
		}
		?>
	<?
	} else {
	?>
	<tr bgcolor="white">
		<td colspan="10"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?
		}
	?>
	</table>
	</td>
	</tr>
<?php
	}
?>
<!-- DAILY ACCOUNT STATEMENT Ends here -->

	<tr>
               <td  height="10" colspan="4" style="padding-left:10px; padding-right:10px;" align="center">
		</td>
	</tr>
	<tr>
               <td  height="5" colspan="4" style="padding-left:10px; padding-right:10px;" align="center">
<? if ($print==true) {?>
	<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintDistributorReport.php?dateFrom=<?=$dateFrom?>&dateTill=<?=$dateTill?>&selDistributor=<?=$selDistributorId?>&pendingOrder=<?=$pendingOrder?>&orderDispatched=<?=$orderDispatched?>&claimPending=<?=$claimPending?>&claimSettled=<?=$claimSettled?>&distributorAccount=<?=$distributorAccount?>&sampleInvoice=<?=$sampleInvoice?>&qryType=<?=$qryType?>&distACStmnt=<?=$distACStmnt?>',700,600);">
<? }?>
		</td>
		</tr>
	<?php 
		} else if ($dateFrom!="" && $dateTill!="" && $searchMode) {			
	?>
	<tr>
		<td colspan="3" height="5" class="err1" align="center"><?=$msgNoRecords;?></td>
	</tr>
	<? }?>
				    <tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
                                    <tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
                                    <tr>
                                      <td  height="10" ></td>
                                    </tr>
                                  </table></td>
								</tr>
							</table>						</td>
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
			<td height="10"></td>
		</tr>
	</table>
<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "dateFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateFrom", 
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
			inputField  : "dateTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	<script language="JavaScript" type="text/javascript">
		displayQryType();
	</script>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>