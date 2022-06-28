<?php
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$checked		=	"";
	$searchMode 	= false;
	$statusUpdated = false;
	
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
	if ($p["selTransporter"]!="") $selTransporter = $p["selTransporter"];

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

	# Change the Status
	if ($p["cmdUpdate"]) {
		$changeStatus = $p["changeStatus"];
		if ($changeStatus)
			$updateRecStatus = $transporterPaymentStatusObj->updateTransporterPaymentStatus($fromDate, $tillDate, $selTransporter, $changeStatus, $selSettlementDate);
		if ($updateRecStatus) $statusUpdated = true;
	}

	#Select the records based on date
	if ($dateFrom!="" && $dateTill!="") {	
		# Get all Transporter	
		$transporterRecords	= $transporterPaymentStatusObj->fetchTransporterRecords($fromDate, $tillDate);
		
		#for selecting settlement  Date
		if ($selTransporter) $settlementDateRecords = $transporterPaymentStatusObj->fetchAllSettledDateRecords($fromDate, $tillDate, $selTransporter);			
	}

	if ($p["cmdSearch"]!="" || $statusUpdated==true) {
		# Get Records
		$settlementRecords = $transporterPaymentStatusObj->fetchAllTransporterRecords($fromDate, $tillDate, $selTransporter, $selSettlementDate);		
		$searchMode = true;
	}

	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/TransporterPaymentStatus.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmTransporterPaymentStatus" action="TransporterPaymentStatus.php" method="Post">
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
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp; Transporter Payment Status</td>
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
								<input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" onchange="submitForm('supplyFrom','supplyTill',document.frmTransporterPaymentStatus);" autocomplete="off" />
									</td>
									<td class="fieldName">*To:</td>
									<td>
									<input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" onChange="submitForm('supplyFrom','supplyTill',document.frmTransporterPaymentStatus);" autocomplete="off">
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
								<td class="fieldName" nowrap>*Transporter:</td>
								<td>
									 <select name="selTransporter" id="selTransporter" onchange="this.form.submit();">
										<option value="">-- Select --</option>
										<?
										foreach ($transporterRecords as $tr) {
											$transporterId	=	$tr[0];
											$transporterName	=	stripSlash($tr[1]);
											$selected	=	"";
											if ($transporterId == $selTransporter) {
												$selected	=	"selected";
											}
										?>
										<option value="<?=$transporterId?>" <?=$selected?>> 
										<?=$transporterName?>
										</option>
										<? } ?>
									</select>
								</td>
									</tr>
							<?php 
								if ($selTransporter!="" && sizeof($settlementDateRecords)>0) {
							?>
							<tr>
								<td class="fieldName" nowrap>Settlement Date&nbsp;</td>
								<td>
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
							</table>
							</TD>
						</tr>
					<tr><TD height="5"></TD></tr>
					<tr>
						<TD align="center">
							<input name="cmdSearch" type="submit" class="button" id="cmdSearch" value="Search" onclick="return validateTransporterPaymentStatus(document.frmTransporterPaymentStatus);">
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
									<!--TD>
										<table>
										<TR>
										<TD><INPUT type="radio" name="changeStatus" value="CA" <?=$changeRMQuantity?> class="chkBox"></TD>
										<TD class="listing-item" nowrap="true">Bill</TD>
										</TR>
										</table>
									</TD-->
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
										<input type="submit" name="cmdUpdate" value=" Update " class="button" onclick="return validateTransporterPaymentUpdate(document.frmTransporterPaymentStatus);" <? if((sizeof($settlementRecords)==0) && $selTransporter!="") echo "disabled";?>>
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
			 if (sizeof($settlementRecords)>0) {
				 $i = 0;
		      ?>
                      <tr>
                        <td colspan="4" align="center" style="padding-left:10px;padding-right:10px;"> 
		<table width="80%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
              <tr bgcolor="#f2f2f2" align="center"> 		
		<th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;">Docket No</th>
                <th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;">Date</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Status</th>
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
		$challanConfirmed = "";
		$chkAllSettled		= "";
		$chkAllPaid		= "";
		$chkAllConfirmed	= "";
		foreach ($settlementRecords as $tps) {
			$i++;							
			$despatchDate 	= dateFormat($tps[1]);
			$docketNo	= $tps[3];
			$transporterGrandTotalAmt	=	$tps[9];
			$settled	= $tps[4];
			$paid		= $tps[6];
			$billNo		= $tps[10];

			$displayPaymentStatus = "";
			$paidAmount = "";
			$settledAmount = "";			
			if($settled=='Y') {
				$displayPaymentStatus = "";
				if ($paid=='Y') {
					$displayPaymentStatus = "<span style=\"color:#003300\"><strong>Paid </strong></span> ";
					$paidAmount = $transporterGrandTotalAmt;
					$totalPaidAmount += $paidAmount;
				} else {
					$displayPaymentStatus = "<span style=\"color:#DF610D\"><strong>Not Paid</span></strong>";
					$settledAmount = $transporterGrandTotalAmt;
					$totalSettledAmount += $settledAmount;
				}
			} else {
				$displayPaymentStatus = "";
				if ($transporterGrandTotalAmt!=0) {
			  		$displayPaymentStatus = "<span style=\"color:#330099\"><strong>Not Settled</span></strong>";					
				} else {
					$displayPaymentStatus = "<span style=\"color:#FF0000\"><strong>Not Confirmed</span></strong>";
				}
				/*
				if ($transporterGrandTotalAmt!=0) {
			  		$displayPaymentStatus = "<span style=\"color:#330099\"><strong>Not Settled</span></strong>";					
				} else if ($billNo!="") {
					$displayPaymentStatus = "<span style=\"color:#DF610D\"><strong>Confirmed</span></strong>";
				} else {
					$displayPaymentStatus = "<span style=\"color:#FF0000\"><strong>Not Confirmed</span></strong>";
				}
				*/	
			}
			$challanPaidAmount = $paidAmount + $settledAmount;
			$grandTotalAmount += $challanPaidAmount;				
		?>
              <tr bgcolor="#FFFFFF">
		   <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left"><?=$docketNo?></td>
                <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$despatchDate?></td>
		 <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px" align="center"><?=$displayPaymentStatus?></td>
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
		<td></td>
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

                      <tr> 
                        <td colspan="4" align="center" class="err1">
				<? if (sizeof($settlementRecords)<=0 && $selTransporter!="" && $searchMode){ echo $msgNoSettlement;}?>
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
