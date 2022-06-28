<?php
	require("include/include.php");
	require_once("lib/PreProcessingPaymentStatus_ajax.php");
	$err			=	"";
	$errDel			=	"";
	$checked		=	"";
	$searchMode 	= false;
	
	#-------------------Admin Checking--------------------------------------
	$isAdmin 			= false;
	$role		=	$manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

	//------------  Checking Access Control Level  ----------------
	$add	 = false;
	$edit	 = false;
	$del	 = false;
	$print	 = false;
	$confirm = false;
	$reEdit  = false;
	
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
	//----------------------------------------------------------
	
	if ($p["selSettlementDate"]!="") $selSettlementDate = $p["selSettlementDate"];
	if ($p["selProcessor"]!="") $selProcessor = $p["selProcessor"];

	# select record between selected date
	if ($p["supplyFrom"]=="" && $p["supplyTill"]=="") {
		$dateFrom = $g["supplyFrom"];
		$dateTill = $g["supplyTill"];
	} else {
		$dateFrom = $p["supplyFrom"];
		$dateTill = $p["supplyTill"];
	}

	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);


	$searchType  = $p["searchType"];
	if ($searchType=='RWS') $rateSearch = "Checked";
	if ($searchType=='QWS') $qtySearch = "Checked";
	$qtySearchType	= $p["qtySearchType"];

	#Change the Status
	if ($p["cmdUpdate"]) {
		$changeStatus = $p["changeStatus"];
		if ($changeStatus)
			$updateRecStatus = $preProcessPaymentStatusObj->updatePreProcessPaymentStatus($fromDate, $tillDate, $selProcessor, $changeStatus);
		if ($updateRecStatus) $statusUpdated = true;
	}


	#Select the records based on date
	if ($dateFrom!="" && $dateTill!="") {	
		#For Pre-Processor Lisitng
		$preProcessorRecords = 	$preProcessPaymentStatusObj->fetchDistinctPreProcessorRecords($fromDate, $tillDate);

		#for selecting settlement  Date
		$settlementDateRecords	= $preProcessPaymentStatusObj->fetchAllDateRecords($fromDate, $tillDate, $selProcessor);			
	}

	if ($p["cmdSearch"]!="" || $statusUpdated==true) {
		if ($searchType=='RWS') {
			$settlementRecords = $preProcessPaymentStatusObj->fetchProcessorSettlementRecords($fromDate, $tillDate, $selProcessor, $selSettlementDate);						
		}
		if ($searchType=='QWS') {			
			$dailyPreProcessEntryRecords =  $preProcessPaymentStatusObj->filterPreProcessingRecords($fromDate, $tillDate, $selProcessor, $qtySearchType);
		}
		$searchMode = true;
	}
	$ON_LOAD_SAJAX = "Y";

	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/PreProcessingPaymentStatus.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
	
?>
	<form name="frmPreProcessPaymentStatus" action="PreProcessingPaymentStatus.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">
		<tr>
			<td height="30" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>		
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="70%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp; Pre-Processing Payment Status</td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2"  align="center">
										<table cellpadding="0"  width="99%" cellspacing="0" border="0" align="center">
                      <tr> 
                        <td colspan="2" height="5"></td>
                      </tr>
                      <tr> 
                        <? if($editMode){?>
                        <?} else{?>
                        <td colspan="4" align="center"></td>
                        <?} ?>
                      </tr>
                      <input type="hidden" name="hidDailyRateId" value="<?=$dailyRateId;?>">
                      <tr>
                        <td colspan="3" nowrap height="5"></td>
                        </tr>
			<tr>
				<TD style="padding-left:10px;padding-right:10px;">
					<fieldset>
					<table>
						<TR>
							<TD>
								<table width="250">
								<tr> 
								<td class="fieldName">*From:</td>
								<td> 
								<input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" onchange="this.form.submit();" autocomplete="off" />
									</td>
									<td class="fieldName">*To:</td>
									<td>
									<input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" onChange="this.form.submit();" autocomplete="off">
									</td>
								</tr>
								</table>
							</TD>
						</TR>
						<tr>
							<TD>
								<table width="250" cellpadding="0" cellspacing="0">
								<tr>
								<td class="fieldName"></td>
								<td></td>
								</tr>
								<tr> 
								<td class="fieldName" nowrap>*Pre-Processor:</td>
								<td>
									<!--<select name="selProcessor" id="selProcessor" onchange="this.form.submit();">-->
									<select name="selProcessor" id="selProcessor" onchange="functionLoad(this)">
									<option value="">--- Select ---</option>
									<? 
									foreach ($preProcessorRecords as $pr) {
										$processorId	= $pr[0];
										$processorName	= stripSlash($pr[1]);
										$processorCode	= stripSlash($pr[2]);
										$selected	= "";
										if ($selProcessor == $processorId) {
											$selected	=	"selected";
										}
									?>
									<option value="<?=$processorId;?>" <?=$selected;?>><?=$processorName;?></option>
									<?
									}
									?>
								</select></td>
									</tr>
							<?php 
								if ($selProcessor!="" && sizeof($settlementDateRecords)>0) {
							?>
							<tr>
								<td class="fieldName" nowrap>Settlement Date&nbsp;</td>
								<td>
								<!-- 	 onchange="this.form.submit();" -->
								<select name="selSettlementDate">
								<option value="">-- Select All --</option>
								<? 
								foreach ($settlementDateRecords as $sdr) {
									$settledDate	=	$sdr[0];
									$Date		=	explode("-",$sdr[0]);
									$recordDate	=	$Date[2]."/".$Date[1]."/".$Date[0];
									$selected	=	"";
									if ($settledDate == $selSettlementDate) $selected = "selected";
									if ($settledDate!=0000-00-00) {
								?>
								<option value="<?=$settledDate;?>" <?=$selected;?> ><?=$recordDate;?> </option>
								<?
									}
								}
								?>
								</select></td></tr>
								<? }?>
							<tr><TD height="10"></TD></tr>
							<tr>
								<td colspan="2" align="center" >
									<fieldset style="padding-left:5px;pading-right:5px;padding-bottom:5px;padding-top:5px;">
									<legend class="listing-item">Search</legend>
									<table cellpadding="0" cellspacing="0">
										<TR valign="center">
											<TD>
<!--  onclick="this.form.submit();" -->
												<INPUT type="radio" name="searchType" value="RWS" class="chkBox" <?=$rateSearch?> >
											</TD>
											<TD class="listing-item" nowrap="true">Rate Wise</TD>
											<TD>
												<INPUT type="radio" name="searchType" value="QWS" class="chkBox" <?=$qtySearch?> onclick="this.form.submit();">
											</TD>
											<TD class="listing-item" nowrap="true">
                                                Qty Wise
                                              </TD>
											<?
												if ($searchType=='QWS') {
											?>
											<td style="padding-left:10px;pading-right:10px;">
<!-- onchange="this.form.submit();" -->
							<select name="qtySearchType" >
							<option value="">-- Select--</option>
												<option value="SU" <? if ($qtySearchType=='SU') echo "selected"; ?>>Summary</option>
												<option value="DT" <? if ($qtySearchType=='DT') echo "selected"; ?>>Detailed</option>
												</select>
											</td>
											<?
												}
											?>
										</TR>
									</table>
									</fieldset>
								</td>
							</tr>
							</table>
							</TD>
						</tr>
					<tr><TD height="5"></TD></tr>
					<tr>
						<TD align="center">
							<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search" onclick="return validatePreProcessPaymentSearch(document.frmPreProcessPaymentStatus);">
						</TD>
					</tr>
					</table>
					</fieldset>	
				</TD>
				<!-- 	Change Status Start Here	 -->
				<?php
					 if ($isAdmin==true || $reEdit==true) { 
				?>
				<td valign="top" nowrap style="padding-right:10px;">
				<table>
					<TR>
						<TD>
						<fieldset>
						<legend class="listing-item">Change Payment Status</legend>
							<table>
								<TR>
									<TD>
									<table>
									<TR>
									<TD class="fieldName">Change:</TD>
									<TD>
										<table>
										<TR>
										<TD><INPUT type="radio" name="changeStatus" value="CRMQ" <?=$changeRMQuantity?> class="chkBox"></TD>
										<TD class="listing-item" nowrap="true">PreProcessed Qty</TD>
										</TR>
										</table>
									</TD>
									<TD>
										<table>
										<TR>
										<TD><INPUT type="radio" name="changeStatus" value="CR" <?=$changeRate?> class="chkBox"></TD>
										<TD class="listing-item">Rate</TD>
										</TR>
										</table>
									</TD>
									<TD>
										<table>
										<TR>
										<TD><INPUT type="radio" name="changeStatus" value="CPD" <?=$changePaymentDetails?> class="chkBox"></TD>
										<TD class="listing-item" nowrap="true">Payment Details</TD>
										</TR>
										</table>
									</TD>
									<td>
										<input type="submit" name="cmdUpdate" value=" Update " class="button" onclick="return validatePreProcessPaymentUpdate(document.frmPreProcessPaymentStatus);" <? if((sizeof($dailyCatchReportRecords)==0) && $weighNumber!="") echo "disabled";?>>
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
			</tr>
			<tr><TD height="10"></TD></tr>
                      <? 
			 if (sizeof($settlementRecords)>0 && $searchType=='RWS') {
				 $i = 0;
		      ?>
                      <tr>
                        <td colspan="4" align="center" style="padding-left:10px;padding-right:10px;"> 
		<table width="80%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
              <tr bgcolor="#f2f2f2" align="center"> 
                <th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;">Date</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Status</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Rate Entered (Amt)</th>
                <th class="listing-head" style="padding-left:5px; padding-right:5px;">Settled Not Paid (Amt)</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Settled and Paid (Amt)</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Total (Amt)</th>
	    </tr>
              <?
		$paidAmount		= 	"";
		$totalPaidAmount = "";
		$totalSettledAmount = "";
		$challanPaidAmount = "";
		$grandTotalAmount = "";
		$totalRateEntered = "";
		$prevSelBillCompanyId = "";
		$challanConfirmed = "";
		$chkAllSettled		= "";
		$chkAllPaid		= "";
		$chkAllConfirmed	= "";
		foreach ($settlementRecords as $psr) {
			$i++;							
			
			$entryDate		=	$psr[2];		
			$array			=	explode("-",$psr[2]);
			$enteredDate		=	$array[2]."/".$array[1]."/".$array[0];
		
			list($chkAllSettled, $chkAllPaid, $chkAllConfirmed) = $preProcessPaymentStatusObj->checkAllRecordsSettled($fromDate, $tillDate, $selProcessor, $entryDate);
			//echo "<br>$chkAllSettled,$chkAllPaid,$chkAllConfirmed<br>";
			$processorAmount	=	$psr[16];
			$displayPaymentStatus = "";
			$paidAmount = "";
			$settledAmount = "";
			$rateEnteredAmt = "";
			if(!$chkAllSettled) {
				$displayPaymentStatus = "";

				if (!$chkAllPaid) {
					$displayPaymentStatus = "<span style=\"color:#003300\"><strong>Paid </strong></span> ";
					$paidAmount = $processorAmount;
					$totalPaidAmount += $paidAmount;
				} else {
					$displayPaymentStatus = "<span style=\"color:#DF610D\"><strong>Not Paid</span></strong>";
					$settledAmount = $processorAmount;
					$totalSettledAmount += $settledAmount;
				}
			} else {
				$displayPaymentStatus = "";
				if ($processorAmount!=0) {
			  		$displayPaymentStatus = "<span style=\"color:#330099\"><strong>Not Settled</span></strong>";
					$rateEnteredAmt = $processorAmount;
					$totalRateEntered += $rateEnteredAmt;
				} else if (!$chkAllConfirmed) {
					$displayPaymentStatus = "<span style=\"color:#DF610D\"><strong>Confirmed</span></strong>";
				} else {
					$displayPaymentStatus = "<span style=\"color:#FF0000\"><strong>Not Confirmed</span></strong>";
				}	
			}
			$challanPaidAmount = $paidAmount + $settledAmount + $rateEnteredAmt;
			$grandTotalAmount += $challanPaidAmount;				
		?>
              <tr bgcolor="#FFFFFF">
                <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$enteredDate?></td>
		 <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px" align="center"><?=$displayPaymentStatus?></td>
 		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$rateEnteredAmt?></td>
                <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$settledAmount?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><? if ($paidAmount) echo number_format($paidAmount,2);?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><? echo number_format($challanPaidAmount,2);?></td>
		     </tr>
		<?php				
			}
		?>
              <tr bgcolor="#FFFFFF">
		<td class="listing-item" nowrap>&nbsp;</td>
		<td class="listing-head" nowrap align="center">TOTAL</td>
                <td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong>
                  <? echo number_format($totalRateEntered,2);?></strong></td>
                <td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
                  <? echo number_format($totalSettledAmount,2);?></strong></td>
		<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
                  <? echo number_format($totalPaidAmount,2);?></strong></td>
		<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
                  <? echo number_format($grandTotalAmount,2);?></strong></td>
		
              </tr>
			  
      </table></td><input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>" >
                        </tr>
						<? }?>
			<? 
			 if (sizeof($dailyPreProcessEntryRecords)>0 && $searchType=='QWS' && $qtySearchType!="") {
				 $i = 0;
		      	?>
                      <tr>
                        <td colspan="4" align="center" style="padding-left:10px;padding-right:10px;">
		<table width="80%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
        <tr bgcolor="#f2f2f2" align="center"> 
                <th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;">Date</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Status</th>
		<?
			if ($qtySearchType=='DT') {
		?>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Pre-Process code</th>	
		<?
			}
		?>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Qty not Settled</th>
                <th class="listing-head" style="padding-left:5px; padding-right:5px;">Qty Settled</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Qty Paid</th>		
	  </tr>
              <?
		$totalPaidQty = 0;
		$totalConfirmedQty = 0;
		$totalNotConfirmedQty = 0;
		$prevSelBillCompanyId = "";
		foreach ($dailyPreProcessEntryRecords as $psr) {
			$i++;
			$entryDate		=	$psr[2];		
			$array			=	explode("-",$psr[2]);
			$enteredDate		=	$array[2]."/".$array[1]."/".$array[0];

			# Check All Settled/Paid/Confirmed		
			list($chkAllSettled, $chkAllPaid, $chkAllConfirmed) = $preProcessPaymentStatusObj->checkAllRecordsSettled($fromDate, $tillDate, $selProcessor, $entryDate);
			//echo "<br>$chkAllSettled,$chkAllPaid,$chkAllConfirmed<br>";

			$preProcessCode		= $psr[16];
			$displayPaymentStatus = "";			
			$preProcessorQty	= $psr[18];	

			$cSettled		= $psr[19];	
			$cPaid			= $psr[20];
			$cConfirmed		= $psr[21];

			$paidQty = "";
			$confirmedQty = "";
			$notConfirmedQty = "";
			if (!$chkAllPaid || ($cPaid=='Y' && $qtySearchType=='DT')) {
				$displayPaymentStatus = "<span style=\"color:#003300\"><strong>Paid </strong></span> ";
				$paidQty = $preProcessorQty;
				$totalPaidQty += $paidQty;				
			} else {
				$displayPaymentStatus = "";
				if (!$chkAllSettled || ($cSettled=='Y' && $qtySearchType=='DT')) {
					$displayPaymentStatus = "<span style=\"color:#DF610D\"><strong>Settled</span></strong>";
					$confirmedQty = $preProcessorQty;
					$totalConfirmedQty += $confirmedQty;
				} else {
					$displayPaymentStatus = "<span style=\"color:#FF0000\"><strong>Pending</span></strong>";
					$notConfirmedQty = $preProcessorQty;
					$totalNotConfirmedQty += $notConfirmedQty;
				}
			}
		?>
              <tr bgcolor="#FFFFFF">
                <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$enteredDate?></td>
		 <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px" align="center"><?=$displayPaymentStatus?></td>
		<?
			if ($qtySearchType=='DT') {
		?>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px" align="left"><?=$preProcessCode?></td>		
		<?
			}
		?>
 		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?if($notConfirmedQty) echo number_format($notConfirmedQty,2);?></td>
                <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?if($confirmedQty) echo number_format($confirmedQty,2);?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><? if ($paidQty) echo number_format($paidQty,2);?></td>		
	    </tr>
		<?	
			$prevSelBillCompanyId=$selBillCompanyId;	
			}
		?>
              <tr bgcolor="#FFFFFF">
		<? 
			$colSpan = "";
			if ($qtySearchType=='DT') $colSpan = 3;
			else $colSpan = 2;
		?>
		<td class="listing-head" nowrap align="right" colspan="<?=$colSpan?>">TOTAL:&nbsp;</td>
                <td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong>
                  <? echo number_format($totalNotConfirmedQty,2);?></strong></td>
                <td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
                  <? echo number_format($totalConfirmedQty,2);?></strong></td>
		<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
                  <? echo number_format($totalPaidQty,2);?></strong></td>		
              </tr>
			  
      </table></td><input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>" >
                        </tr>
	<?
		}
	?>
                      <tr> 
                        <td colspan="4" align="center" class="err1">
				<? if( (sizeof($settlementRecords)<=0 && sizeof($dailyPreProcessEntryRecords)==0) && $selProcessor!="" && $searchMode){ echo $msgNoSettlement;}?>
			</td>
                        </tr>
		
                      <tr> 
                        <? if($editMode){?>
                        <?} else{?>
                        <td colspan="4" align="center"></td>
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
				<!-- Form fields end   -->
			</td>
		</tr>	
		<tr>
			<td height="10" ></td>
		</tr>
	
			
	</table><SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "supplyFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "supplyFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT><SCRIPT LANGUAGE="JavaScript">
	<!--
	
	Calendar.setup 
	(	
		{
			inputField  : "supplyTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "supplyTill", 
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
