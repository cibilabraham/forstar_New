<?php
//
	require("include/include.php");
	require_once("lib/DistributorAccountReport_class.php");
	$distributorAccountReportObj = new DistributorAccountReport($databaseConnect);
	require_once("lib/DistributorAccountReport_ajax.php");


	$err			= "";
	$errDel			= "";
	$editMode		= false;
	$addMode		= true;
	$searchMode 		= false;
	$recEditable 		= false;
	$statusUpdated		= false;
	//$printMode		= true;
	$distPmtChqRtgsRecs	= array();

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
	// Cheking access control end ==================================================

	$advSearchEnabled = false;
	$searchEnabled		= false;
	
	#QS-> Quick Search / AS-> Advanced Search
	if ($g["searchMode"]!="") $searchMode = $g["searchMode"];
	else $searchMode = $p["searchMode"];

	if ($searchMode=='QS') $quickSearch = "Checked";
	else if ($searchMode=='AS') $advancedSearch = "Checked";
	else $searchMode = "";

	// ---------------------	
	$cbChqRt	= $p["cbChqRt"];
	$cbChqRtChk	= ($cbChqRt!="")?"checked":"";

	$ddChqRt	= $p["ddChqRt"];
	$chqRtNo	= trim($p["chqRtNo"]);

	$cbShowSimilar	= $p["cbShowSimilar"];
	$cbShowSimilarChk = ($cbShowSimilar!="")?"checked":"";
	
	$cbPmtDate	= $p["cbPmtDate"];
	$cbPmtDateChk	= ($cbPmtDate)?"checked":"";
	$ddPmtDate	= $p["ddPmtDate"];
	$txtPmtDate	= trim($p["txtPmtDate"]);
	$pmtDate	= ($txtPmtDate!="")?mysqlDateFormat($txtPmtDate):"";
	$txtPmtEndDate	= trim($p["txtPmtEndDate"]);
	$pmtEndDate	= ($txtPmtEndDate!="")?mysqlDateFormat($txtPmtEndDate):"";
	$ddPmtDateType	= $p["ddPmtDateType"]; // SD/DR

	$cbPmtAmt	= $p["cbPmtAmt"];
	$cbPmtAmtChk	= ($cbPmtAmt)?"checked":"";
	$txtPmtAmt	= trim($p["txtPmtAmt"]);
	

	
	# select records between selected date
	if ($g["selectFrom"]!="" && $g["selectTill"]!="") {
		$dateFrom = $g["selectFrom"];
		$dateTill = $g["selectTill"];
	} else if ($p["selectFrom"]!="" && $p["selectTill"]!="") {
		$dateFrom = $p["selectFrom"];
		$dateTill = $p["selectTill"];
	} else {
		# List default for the current financial year [1st April (Y-1) to March 31st Y]
		# but display up to current date
		//$dateFrom = date("d/m/Y", mktime(0, 0, 0, 04, 01, (date("Y")-1)));
		$dateFrom = financialYear();
		$dateTill = date("d/m/Y");
	}

	// Quick Search
	if ($p["cmdSearch"]!="" || $g["cmdSearch"]!="") {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);

		# Search payments based on cheque nos / rtgs no
		if ($cbChqRt || $cbPmtDate || $cbPmtAmt) {
			$selChqRtNo = ltrim($chqRtNo,'\0');
			$distPmtChqRtgsRecs = $distributorAccountReportObj->getDistPmtChqRtgsRecs($fromDate, $tillDate, $cbChqRt, $ddChqRt, $selChqRtNo, $cbShowSimilar, $cbPmtDate, $ddPmtDate, $pmtDate, $cbPmtAmt, $txtPmtAmt, $ddPmtDateType, $pmtEndDate);
			$searchEnabled = true;
		}
	}

	//echo 1111112%10000;
	//echo substr("99", 0, -1);

	// Advanced Search
	$advSearchArr  = array("CHQN"=>"Cheque No", "RTGSN"=>"RTGS No", "CHQD"=>"Cheque Date", "VALD"=>"Value Date");
	$tblHeaderArr = array();
	if ($p["cmdAdvSearch"]!="") {
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);
		/*
		$chqNo		= $p["CHQN"];
		$rtgsNo		= $p["RTGSN"];
		*/
		$tblHeaderArr = array();
		foreach ($advSearchArr as $key=>$value) {
			if ($p[$key]!="") $tblHeaderArr[$key] = $value;
		}
		
		if (sizeof($tblHeaderArr)>0) {
			$distAdvSearchRecs = $distributorAccountReportObj->getDistAdvSearchRecs($fromDate, $tillDate);
			$searchEnabled = true;
		}
		$advSearchEnabled = true;
		/*
		echo "<pre>";
		print_r($tblHeaderArr);
		echo "</pre>";
		*/
	}


	$chrtArr	= array("CHQN"=>"Cheque No", "RTGSN"=>"RTGS No");
	$pmtDateArr	= array("CHQD"=>"Cheque Date", "VALD"=>"Value Date");
	$pmtDateTypeArr = array("SD"=>"Single Date", "DR"=>"Date Range");

	$ON_LOAD_SAJAX 		= "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	# include JS in template
	$ON_LOAD_PRINT_JS = "libjs/DistributorAccountReport.js";	

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmDistributorAccountReport" action="DistributorAccountReport.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="85%" >
		<? if($err!="" ){?>
		<tr>
			<td height="20" align="center" class="err1"><?=$err;?></td>			
		</tr>
		<?}?>
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
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Distributor Account Report</td>
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
		<TD>
			<table>
				<TR>
					<TD valign="top" align="center">
					<table cellpadding="0" cellspacing="0">
					<tr>
                      <td>
				<table width="200" border="0">
				<tr>
					<td>
					<table width="100" border="0">					
					<tr>
					<td><input type="radio" class="chkBox" checked onclick="this.form.submit();" value="QS" name="searchMode" <?=$quickSearch?> /></td>
					<td nowrap="" class="listing-item">Quick Search </td>
					</tr>
					</table>
					</td>
					<td>
					<table width="100" border="0">					
					<tr>
					<td><input type="radio" class="chkBox" onclick="this.form.submit();" value="AS" name="searchMode" <?=$advancedSearch?>/></td>
					<td nowrap="" class="listing-item">Advanced Search </td>
					</tr>
					</table></td>
				</tr>
				<?php
					if ($searchMode=="QS" || $searchMode=="") {
				?>
				<tr>
					<td align="center" colspan="2">
							<fieldset>
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td class="listing-item" nowrap>From:</td>
										<td nowrap="nowrap">
											<input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>" />
										</td>
										<td class="listing-item">&nbsp;</td>
										<td class="listing-item" nowrap>Till:</td>
										<td nowrap>
											<input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>" />
										</td>
									</tr>	
									<tr><TD height="5"></TD></tr>		
								</table>
							</fieldset>
					</td>
				</tr>
				<?php
						}	
				?>
				</table></td>
                                  </tr>
					</table>
					</TD>
					<td>
					<table>
				<TR>				
					<td valign="top">
					<?php
					if ($searchMode=="QS" || $searchMode=="") {
					?>
						<fieldset>
						<legend class="fieldName" style="line-height:normal;">Search Options</legend>
						<table>
							<TR>
								<td>
									<INPUT type="checkbox" name="cbChqRt" id="cbChqRt" value="Y" class="chkBox" onclick="selectChk('cbChqRt');" <?=$cbChqRtChk?> />
								</td>
								<TD>
									<select name="ddChqRt" id="ddChqRt">
										<option value="">-- Select --</option>
										<?php 
											foreach ($chrtArr as $key=>$value) {
												$selected = ($ddChqRt==$key)?"selected":"";
										?>
										<option value="<?=$key?>" <?=$selected?>><?=$value?></option>
										<? }?>
									</select>
								</TD>
								<td class="listing-item" align="left">
									<input type="text" name="chqRtNo" id="chqRtNo" size="12" value="<?=$chqRtNo?>" />
								</td>
								<td class="listing-item" nowrap="true">
									<INPUT type="checkbox" name="cbShowSimilar" id="cbShowSimilar" value="Y" class="chkBox"  <?=$cbShowSimilarChk?> />&nbsp;Show similarities / nearest
								</td>
							</TR>
							<TR>
								<td>
									<INPUT type="checkbox" name="cbPmtDate" id="cbPmtDate" value="Y" class="chkBox" onclick="selectChk('cbPmtDate');" <?=$cbPmtDateChk?> />
								</td>
								<TD>
									<select name="ddPmtDate" id="ddPmtDate">
										<option value="">-- Select --</option>
										<? 
											foreach ($pmtDateArr as $key=>$value) {
												$selected = ($ddPmtDate==$key)?"selected":"";
										?>
										<option value="<?=$key?>" <?=$selected?>><?=$value?></option>
										<? }?>
									</select>
								</TD>
								<td>
									<select name="ddPmtDateType" id="ddPmtDateType" onchange="displayDateType();">
										<?php 
											foreach ($pmtDateTypeArr as $key=>$value) {
												$selected = ($ddPmtDateType==$key)?"selected":"";
										?>
										<option value="<?=$key?>" <?=$selected?>><?=$value?></option>
										<? }?>
									</select>	
								</td>
								<td class="listing-item" align="left" nowrap>
									<span id="startDateRow">
										<span id='startSpan' style="display:none;">Start</span> Date:&nbsp;
											<input type="text" name="txtPmtDate" id="txtPmtDate" size="9" value="<?=$txtPmtDate?>" />
										</span>
									<span id="endDateRow" style="display:none;">
										End Date:&nbsp;
										<input type="text" name="txtPmtEndDate" id="txtPmtEndDate" size="9" value="<?=$txtPmtEndDate?>" />
									</span>
								</td>
							</TR>
							<TR>
								<td>
									<INPUT type="checkbox" name="cbPmtAmt" id="cbPmtAmt" value="Y" class="chkBox" onclick="selectChk('cbPmtAmt');" <?=$cbPmtAmtChk?> />
								</td>
								<TD class="listing-item" title="Search payment based on amount">
									Amount
								</TD>
								<td class="listing-item" align="left">
									<input type="text" name="txtPmtAmt" id="txtPmtAmt" size="9" value="<?=$txtPmtAmt?>" />
								</td>
							</TR>							
							<TR>
								<TD>&nbsp;</TD>
								<td class="listing-item">
									<INPUT TYPE="submit" class="button" name="cmdSearch" value=" Search " onclick="return validateDistributorAccountReport();">
								</td>
							</TR>
						</table>
						</fieldset>
						<?php
							}
						?>
					</td>
				</TR>
			</table>
					</td>
				</TR>
	
<!-- Advance Search Starts here -->
<?php
	if ($searchMode=="AS" ) {
?>
	<tr>
		<TD align="center" colspan="2">
			<table width="200" border="0">
                          <tr>
                            <td>
			<fieldset>
			<legend class="fieldName">Advanced Search</legend>
			<table border="0">
				<tr>
					<TD colspan="8">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td class="listing-item" nowrap>From:</td>
							<td nowrap="nowrap">
								<input type="text" id="selectFrom" name="selectFrom" size="8" value="<?=$dateFrom?>" />
							</td>
							<td class="listing-item">&nbsp;</td>
							<td class="listing-item" nowrap>Till:</td>
							<td nowrap>
								<input type="text" id="selectTill" name="selectTill" size="8"  value="<?=$dateTill?>" />
							</td>
						</tr>	
						<tr><TD height="5"></TD></tr>		
					</table>
					</TD>
				</tr>
				<tr>
				<?php
				$asNumCol = 8;
				$count = 0;
				foreach ($advSearchArr as $key=>$value) {
					$count++;
					$checked = (array_key_exists($key,$tblHeaderArr))?"checked":"";
				?>
				<td>
				<table width="50" border="0">
                                	<tr>
                                    		<td align="center">
							<input name="<?=$key?>" type="checkbox" id="<?=$key?>" value="<?=$key?>" class="chkBox" rel="cbAdvSearch" <?=$checked?> />
						</td>
                                    		<td class="listing-item" nowrap><?=$value?></td>
                                  	</tr>
                                </table>
				</td>
				<?php 
					if ($count%$asNumCol == 0) { 
				?>
				</tr>
				<tr>
				<?php
					}
				}
				?>
                              <tr>
                                <td colspan="8" align="center" height="5"></td>
                              </tr>
                              <tr>
                                <td colspan="8" align="center"><input name="cmdAdvSearch" type="submit" class="button" id="cmdAdvSearch" value="Search" onClick="return validateAdvanceSearch();"></td>
                                </tr>
                            </table>
		</fieldset>
		</td>
                          </tr>
                        </table>
		</TD>
	</tr>
<?php
	} // Adv sr ends
?>
<!-- Advance Search Ends here -->
		</table>
		</TD>
	</tr>
	<?php
	if (sizeof($distPmtChqRtgsRecs)>0 || sizeof($distAdvSearchRecs)>0) {
		$i=0;
	?>
	<tr>
               <td  height="5" colspan="4" style="padding-left:10px; padding-right:10px;" align="center">
		<? if ($print==true) {?>
		<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintDistributorAccountReport.php?dateFrom=<?=$dateFrom?>&dateTill=<?=$dateTill?>&selDistributor=<?=$selDistributorId?>&pendingOrder=<?=$pendingOrder?>&orderDispatched=<?=$orderDispatched?>&claimPending=<?=$claimPending?>&claimSettled=<?=$claimSettled?>&distributorAccount=<?=$distributorAccount?>&sampleInvoice=<?=$sampleInvoice?>&qryType=<?=$qryType?>&distACStmnt=<?=$distACStmnt?>',700,600);">
		<? }?>
		</td>
		</tr>
	<tr>
               <td  height="10" colspan="4" style="padding-left:10px; padding-right:10px;">
		</td>
	</tr>
<!-- Search based on cheque Starts here -->
<?php	
	if ($cbChqRtChk || $cbPmtDateChk || $cbPmtAmtChk) {	
?>
	<tr>
        	<td  colspan="4" style="padding-left:10px; padding-right:10px;" align="center">
<table cellpadding="2"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?php
	if (sizeof($distPmtChqRtgsRecs)>0) {
		$i	=	0;
	?>	
	<tr  bgcolor="#f2f2f2" align="center">	
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Date</td>		
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Distributor</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">City</td>		
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">REF INVOICE</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Reason</td>		
		<!--<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">Debit<br>(Rs.)</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">Credit<br/>(Rs.)</td>-->
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">Amount<br/>(Rs.)</td>		
	</tr>
		
		<?php		
		$distributorAccountId = "";
		foreach ($distPmtChqRtgsRecs as $dar) {
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
				$displayRefInvMsg = "onMouseover=\"ShowTip('Invoice not assigned.');\" onMouseout=\"UnTip();\" ";
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
			<!--<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" <?=$showDebitEntry?>>
				<?//=($debitAmt!=0)?(($displayPopup)?"<a href='###' class='link5'>$debitAmt</a>":$debitAmt):""?>
			</td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" <?=$showCreditEntry?>>
				<?//=($creditAmt!=0 )?(($displayPopup)?"<a href='###' class='link5'>$creditAmt</a>":$creditAmt):""?>
			</td>-->
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
			<!--<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($totalDebitAmt>0)?number_format($totalDebitAmt,2,'.',','):"";?></strong></td>-->
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
	} else if ($searchMode=="QS") {
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
<!--  Adv search listing starts here -->
<?php
	if ($searchMode=='AS') {
?>
<tr>
        	<td  colspan="4" style="padding:0 10px;" align="center">
<table cellpadding="2"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?php
	if (sizeof($distAdvSearchRecs)>0) {
		$i	=	0;
	?>	
	<tr  bgcolor="#f2f2f2" align="center">	
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Date</td>		
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Distributor</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">City</td>		
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">REF INVOICE</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Reason</td>		
		<!--<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">Debit<br>(Rs.)</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">Credit<br/>(Rs.)</td>-->
		<? foreach ($tblHeaderArr as $key=>$value) {?>
		<td class="listing-head" style="padding:0 5px;" nowrap="true"><?=$value?></td>
		<? }?>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">Amount<br/>(Rs.)</td>
	</tr>
		
		<?php		
		$distributorAccountId = "";
		foreach ($distAdvSearchRecs as $dar) {
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
			$selPmtMode	= $dar[10];
			$pmtMode	= $paymentModeArr[$dar[10]];			

			$chqRTGSNo	= $dar[11];
			$chqRTGSDate	= ($dar[12]!="0000-00-00")?dateFormat($dar[12]):"";
			
			
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
				$displayRefInvMsg = "onMouseover=\"ShowTip('Invoice not assigned.');\" onMouseout=\"UnTip();\" ";
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
			<?php 
				//$advSearchArr  = array("CHQN"=>"Cheque No", "RTGSN"=>"RTGS No", "CHQD"=>"Cheque Date", "VALD"=>"Value Date");
				foreach ($tblHeaderArr as $key=>$value) {
			?>
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">
				<?php
					if ($key=='CHQN' && $selPmtMode=='CHQ') echo $chqRTGSNo;
					if ($key=='RTGSN' && $selPmtMode=='RT') echo $chqRTGSNo;
					if ($key=='CHQD' && $selPmtMode=='CHQ') echo $chqRTGSDate;
					if ($key=='VALD') echo $trValueDate;
				?>
			</td>			
			<? }?>
			<!--<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" <?=$showDebitEntry?>>
				<?//=($debitAmt!=0)?(($displayPopup)?"<a href='###' class='link5'>$debitAmt</a>":$debitAmt):""?>
			</td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" <?=$showCreditEntry?>>
				<?//=($creditAmt!=0 )?(($displayPopup)?"<a href='###' class='link5'>$creditAmt</a>":$creditAmt):""?>
			</td>-->
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
			
			$colSpan = 5+sizeof($tblHeaderArr);			
			
		?>
		<tr bgcolor="White">
			<TD colspan="<?=$colSpan?>" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">Total:</TD>
			<!--<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($totalDebitAmt>0)?number_format($totalDebitAmt,2,'.',','):"";?></strong></td>-->
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($totalCreditAmt>0)?number_format($totalCreditAmt,2,'.',','):"";?></strong></td>
		</tr>
	<?
	} else if ($advSearchEnabled) {
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
<!-- Adv Search listing ends here -->
	<tr>
               <td  height="10" colspan="4" style="padding-left:10px; padding-right:10px;" align="center">
		</td>
	</tr>
	<tr>
               <td  height="5" colspan="4" style="padding-left:10px; padding-right:10px;" align="center">
<? if ($print==true) {?>
	<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintDistributorAccountReport.php?dateFrom=<?=$dateFrom?>&dateTill=<?=$dateTill?>&selDistributor=<?=$selDistributorId?>&pendingOrder=<?=$pendingOrder?>&orderDispatched=<?=$orderDispatched?>&claimPending=<?=$claimPending?>&claimSettled=<?=$claimSettled?>&distributorAccount=<?=$distributorAccount?>&sampleInvoice=<?=$sampleInvoice?>&qryType=<?=$qryType?>&distACStmnt=<?=$distACStmnt?>',700,600);">
<? }?>
		</td>
		</tr>
	<?php 
		} else if ($searchEnabled) {
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
	<SCRIPT LANGUAGE="JavaScript">
	<!--	
	Calendar.setup 
	(	
		{
			inputField  : "txtPmtDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "txtPmtDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	<!--	
	Calendar.setup 
	(	
		{
			inputField  : "txtPmtEndDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "txtPmtEndDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>

	<script language="JavaScript" type="text/javascript">
		//displayQryType();
		displayDateType();
	</script>
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>