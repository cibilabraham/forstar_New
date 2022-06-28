<?php
	require("include/include.php");
	require_once("lib/PaymentStatus_ajax.php");
	$err			=	"";
	$errDel			=	"";
	$checked		=	"";
	
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

	if ($p["supplier"]=="") {
		$selectSupplier		= $g["supplier"];
	} else {
		$selectSupplier		= $p["supplier"];
	}

	if ($p["billingCompany"]!="") $billingCompany = $p["billingCompany"];

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
	if ($searchType=='ACS') $assSearch = "Checked";

	$qtySearchType	= $p["qtySearchType"];
	$acFilterType	= $p["acFilterType"];
	
	if ($searchType=='ACS') {
		# Get Supplier Payment Recs
		$getSupplierPaymentRecs = $paymentstatusObj->getSupplierPaymentRecords($fromDate, $tillDate, $selectSupplier);	
	}

	# Select the records based on date
	if ($dateFrom!="" && $dateTill!="") {	
		# Supplier Records
		$supplierRecords	= $paymentstatusObj->fetchSupplierRecords($fromDate, $tillDate);
		
		# Get Billing Comapany  Records
		$billingCompanyRecords = $paymentstatusObj->fetchBillingCompanyRecords($fromDate, $tillDate, $selectSupplier);

		# For selecting settlement  Date
		$settlementDateRecords	= $paymentstatusObj->fetchAllDateRecords($fromDate, $tillDate, $selectSupplier, $billingCompany);
		
		if ($searchType=='RWS') {	
			$settlementRecords = $paymentstatusObj->filterPurchaseStatementRecords($selectSupplier, $fromDate, $tillDate, $selSettlementDate, $billingCompany);
		}	
		
		if ($searchType=='QWS') {
			$dailyCatchEntryRecords	= $paymentstatusObj->filterDailyCatchEntryRecords($selectSupplier, $fromDate, $tillDate, $selSettlementDate, $qtySearchType, $billingCompany);
		}	
	}

	# define the Report Type Array
	$rTypeArr = array("0"=>"Summarized","30"=>"30 Day","15"=>"15 Day");
	$ON_LOAD_SAJAX = "Y";
	# For Printing JS in Head section	
	$ON_LOAD_PRINT_JS = "libjs/PaymentStatus.js"; 

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");

?>
	<form name="frmPaymentStatus" action="PaymentStatus.php" method="Post">
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
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp; Payment Status</td>
								</tr>
			<tr><TD height="10"></TD></tr>
			<?php
				if ($searchType=='ACS') {
			?>			
			<tr>
                        <td colspan="4" align="center">
				<? if($print==true){?>
					<input type="button" name="View" value=" View / Print" class="button" onClick="return validateAccountStatementSearch(document.frmPaymentStatus, 'PrintAccountStatement.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&supplier=<?=$selectSupplier?>&acFilterType=<?=$acFilterType?>');" <? //if( sizeof($purchaseStatementRecords)==0) echo $disabled="disabled";?>>	
				<? }?>
			</td>
                      </tr>
			<?php
				}
			?>
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
		<TD align="center" colspan="2">
			<table>
				<TR>
				<TD valign="top" style="padding-left:5px;padding-right:5px;">
					<fieldset>
						<table>
							<TR>
								<td class="fieldName" style="padding-left:5px;padding-right:5px;"> From:</td>
								<td> 
								<input type="text" id="supplyFrom" name="supplyFrom" size="9" value="<?=$dateFrom?>" onchange="submitForm('supplyFrom','supplyTill',document.frmPaymentStatus);">
								</td>
							</TR>
							<tr>
								<td class="fieldName" style="padding-left:5px;padding-right:5px;">To:</td>
								<td>
								<input type="text" id="supplyTill" name="supplyTill" size="9"  value="<?=$dateTill?>" onChange="submitForm('supplyFrom','supplyTill',document.frmPaymentStatus);">
								</td>
							</tr>
						</table>
					</fieldset>		
				</TD>
				<td>&nbsp;</td>
				<TD valign="top" style="padding-left:5px;padding-right:5px;">
					<fieldset>
					<table>
						<TR>						
							<td class="fieldName" style="padding-left:5px;padding-right:5px;">Supplier:</td>
							<td> 
								<!--<select name="supplier" id="supplier" onchange="this.form.submit();">-->
								<select name="supplier" id="supplier" onchange="functionLoad(this)">
								<option value="">-- Select --</option>
								<?
								foreach ($supplierRecords as $fr) {
									$supplierId	=	$fr[0];
									$supplierName	=	stripSlash($fr[2]);
									$selected	=	"";
									if ($supplierId == $selectSupplier) $selected = "selected";
								?>
								<option value="<?=$supplierId?>" <?=$selected?>><?=$supplierName?></option>
								<? } ?>
							</select></td>
						</td>
						</tr>
				<tr>
				<TD class="fieldName" nowrap="true">Billing Company:</TD>
				<td>
					<!--<select name="billingCompany" id="billingCompany" onchange="this.form.submit();">-->
					<select name="billingCompany" id="billingCompany" onchange="functionLoad(this);">
					<option value="">--Select All--</option>
					<?
					foreach ($billingCompanyRecords as $bcr) {
						$billingCompanyId	= $bcr[0];
						$displayCName		= $bcr[1];
						$selected = "";
						if ($billingCompanyId==$billingCompany) $selected = "selected";
					?>
					<option value="<?=$billingCompanyId?>" <?=$selected?>><?=$displayCName?></option>
					<?	
					}	
					?>
					</select>
				</td>
			</tr>

						<? if ($selectSupplier && sizeof($settlementDateRecords)>0) {?>
			<tr>
				<td class="fieldName" nowrap style="padding-left:5px;padding-right:5px;">Settlement Date:</td>
			        <td>
				 <!-- <select name="selSettlementDate" onchange="this.form.submit();">-->
				 <select name="selSettlementDate" onchange="functionLoad(this);">
				  <option value="">-- Select All --</option>
				  <? 
				  foreach ($settlementDateRecords as $sdr) {
					$settledDate	=	$sdr[0];
					$recordDate	= dateFormat($sdr[0]);
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
				<?php
					 }
				?>
					</table>
					</fieldset>
				</TD>
				</TR>
			</table>
		</TD>
	</tr>
	<tr><TD height="5"></TD></tr>
                      <tr> 
                        <td colspan="2" align="center">
			<table width="250" cellpadding="0" cellspacing="0">			
			<tr>
				<td colspan="2" align="center" >
					<fieldset style="padding-left:5px;padding-right:5px;padding-bottom:5px;padding-top:5px;">
					<legend class="listing-item">Search</legend>
					<table cellpadding="0" cellspacing="0">
						<TR valign="center">
							<TD>
								<INPUT type="radio" name="searchType" value="RWS" class="chkBox" <?=$rateSearch?> onclick="this.form.submit();">
							</TD>
							<TD class="listing-item" nowrap="true">Rate Wise</TD>
							<TD>
								<INPUT type="radio" name="searchType" value="QWS" class="chkBox" <?=$qtySearch?> onclick="this.form.submit();">
							</TD>
							<TD class="listing-item" nowrap="true">Qty Wise</TD>
							<?php
								if ($searchType=='QWS') {
							?>
							<td style="padding-left:10px;padding-right:10px;">
								<select name="qtySearchType" onchange="this.form.submit();">
								<option>-- Select--</option>
								<option value="SU" <? if ($qtySearchType=='SU') echo "selected"; ?>>Summary</option>
								<option value="DT" <? if ($qtySearchType=='DT') echo "selected"; ?>>Detailed</option>
								</select>
							</td>
							<?
								}
							?>
							<TD >
								<INPUT type="radio" name="searchType" id="searchType" value="ACS" class="chkBox" <?=$assSearch?> onclick="this.form.submit();">
							</TD>							
							<td class="listing-item" nowrap="true" style="padding-left:5px;padding-right:5px;">Account Statement Summary</td>
							<?php
								if ($searchType=='ACS') {
							?>							
							<td style="padding-left:5px;padding-right:5px;">
								<select name="acFilterType" id="acFilterType" onchange="this.form.submit();">
								<?php
									foreach ($rTypeArr as $rtype=>$rVal) {
										$selected = "";
										if ($acFilterType==$rtype) $selected = "selected";
								?>
									<option value="<?=$rtype?>" <?=$selected?>><?=$rVal?></option>
								<?php
									}
								?>
								</select>
							</td>
							<?php
								}
							?>
						</TR>	
					</table>
					</fieldset>
				</td>
			</tr>		
			</table>
			</td>
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
                <th nowrap="nowrap" class="listing-head" style="padding-left:5px; padding-right:5px;">Challan No </th>
                <th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;">Date</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Status</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Rate Entered (Amt)</th>
                <th class="listing-head" style="padding-left:5px; padding-right:5px;">Settled Not Paid (Amt)</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Settled and Paid (Amt)</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Total (Amt)</th>
		  </tr>
              <?php
		$paidAmount		= 	"";
		$totalPaidAmount = "";
		$totalSettledAmount = "";
		$challanPaidAmount = "";
		$grandTotalAmount = "";
		$totalRateEntered = "";
		$prevSelBillCompanyId = "";
		$prevBillCompanyId 	= 0;
		$challanConfirmed = "";

		$rateEnterdArr = array();
		$settledAmtArr = array();
		$paidAmtArr = array();
		$challanPaidAmtArr = array();
		$prevBillingArr = array();
		$p=0;
		foreach ($settlementRecords as $psr) {
			$i++;
			$challanEntryId		=	$psr[0];
		
			$challanNo		=	$psr[1];
		
			$enteredDate		=	dateFormat($psr[2]);
			$challanPaid 		= 	$psr[5];

			// Find Supply Cost
			$rmSupplyCost		= $purchasestatementObj->getSupplyCost($challanNo, $billingCompany);
			$rawMaterialCost	= $psr[4];

			$challanConfirmed	= $psr[8];
		
			$checkAllRMSettled = $paymentstatusObj->challanRecords($fromDate, $tillDate, $challanEntryId, $selectSupplier);
			$displayPaymentStatus = "";
			$paidAmount = "";
			$settledAmount = "";
			$rateEnteredAmt = "";
			if(!$checkAllRMSettled) {
				$displayPaymentStatus = "";

				if ($challanPaid=='Y') {
					$displayPaymentStatus = "<span style=\"color:#003300\"><strong>Paid </strong></span> ";
					$paidAmount	=	$rawMaterialCost + $rmSupplyCost;
					$totalPaidAmount += $paidAmount;
				} else {
					$displayPaymentStatus = "<span style=\"color:#DF610D\"><strong>Not Paid</span></strong>";
					$settledAmount = $psr[4];
					$totalSettledAmount += $settledAmount;
				}
			} else {
				$displayPaymentStatus = "";
				if ($psr[4]!=0) {
			  		$displayPaymentStatus = "<span style=\"color:#330099\"><strong>Not Settled</span></strong>";
					$rateEnteredAmt = $psr[4];
					$totalRateEntered += $rateEnteredAmt;
				} else if ($challanConfirmed==1) {
					$displayPaymentStatus = "<span style=\"color:#DF610D\"><strong>Confirmed</span></strong>";
				} else {
					$displayPaymentStatus = "<span style=\"color:#FF0000\"><strong>Not Confirmed</span></strong>";
				}	
			}
			$challanPaidAmount = $paidAmount + $settledAmount + $rateEnteredAmt;
			$grandTotalAmount += $challanPaidAmount;

			$selBillCompanyId 	=	$psr[7];
			if ($i==1) $prevBillCompanyId = $selBillCompanyId;

			$rateEnterdArr[$selBillCompanyId] += $rateEnteredAmt;
		 	$settledAmtArr[$selBillCompanyId] += $settledAmount; 
			$paidAmtArr[$selBillCompanyId] += $paidAmount;
			$challanPaidAmtArr[$selBillCompanyId] += $challanPaidAmount;

			if ($prevBillCompanyId!=$selBillCompanyId ) {
				$prevBillingArr[$p] = $prevSelBillCompanyId;
				echo '<tr bgcolor="#FFFFFF"> 
						<td class="listing-item" nowrap>&nbsp;</td>
						<td class="listing-item" nowrap>&nbsp;</td>
						<td class="listing-head" align="right" style="padding-left:5px; padding-right:5px;">TOTAL:</td>
						<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong>'.number_format($rateEnterdArr[$prevBillCompanyId],2).'</strong></td>
						<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong>'.number_format($settledAmtArr[$prevBillCompanyId],2).'</strong></td>
						<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong>'.number_format($paidAmtArr[$prevBillCompanyId],2).'</strong></td>
						<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong>'.number_format($challanPaidAmtArr[$prevBillCompanyId],2).'</strong></td>
						</tr>';
						$p++;
			}

			if ($prevSelBillCompanyId!=$selBillCompanyId) {
				if ($selBillCompanyId>0) {	// Getting Rec from other billing company
					list($companyName,$address,$place,$pinCode,$country,$telNo,$faxNo) = $billingCompanyObj->getBillingCompanyRec($selBillCompanyId);
				} else {	// Getting Rec from Company Details Rec
					list($companyName,$address,$place,$pinCode,$country,$telNo,$faxNo) = $companydetailsObj->getForstarCompanyDetails();
				}
				echo '<tr bgcolor="white"><td class="fieldname" colspan="10" style="padding-left:10px; padding-right:10px;" nowrap><b>'.$companyName.'</b></td></tr>';
			}

			$displayChallanNum	= $psr[9];
				
		?>
              <tr bgcolor="#FFFFFF"> 
                <td class="listing-item" nowrap height='25' style="padding-left:5px; padding-right:5px;"><?=$displayChallanNum?><input type="hidden" name="challanEntryId_<?=$i?>" value="<?=$challanEntryId?>"></td>
                <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$enteredDate?></td>
		 <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px" align="center"><?=$displayPaymentStatus?></td>
 		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$rateEnteredAmt?></td>
                <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$settledAmount?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><? if ($paidAmount) echo number_format($paidAmount,2);?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=number_format($challanPaidAmount,2);?></td>
		     </tr>
		<?php				
			if (sizeof($settlementRecords)==$i && sizeof($challanPaidAmtArr)>1) {
			?>
		<tr bgcolor="#FFFFFF"> 
			<td class="listing-item" nowrap>&nbsp;</td>
			<td class="listing-item" nowrap>&nbsp;</td>
			<td class="listing-head" align="right" style="padding-left:5px; padding-right:5px;">TOTAL:</td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($rateEnterdArr[$selBillCompanyId],2);?></strong></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($settledAmtArr[$selBillCompanyId],2);?></strong></td>
			<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
			<?=number_format($paidAmtArr[$selBillCompanyId],2);?></strong></td>
			<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
			<?=number_format($challanPaidAmtArr[$selBillCompanyId],2);?></strong></td>
		</tr>
		<?php
			} // Sub Total
		?>
		<?	
			$prevSelBillCompanyId	= $selBillCompanyId;	
			$prevBillCompanyId 	= $selBillCompanyId;		
			}
		?>
              <tr bgcolor="#FFFFFF"> 
                <td class="listing-item" nowrap>&nbsp;</td>
		<td class="listing-item" nowrap>&nbsp;</td>
		<td class="listing-head" nowrap align="right" style="padding-left:5px; padding-right:5px;">
			<? if (sizeof($prevBillingArr)>0) {?>GR.<? }?> TOTAL:
		</td>
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
			 if (sizeof($dailyCatchEntryRecords)>0 && $searchType=='QWS' && $qtySearchType!="") {
				 $i = 0;
		      	?>
                      <tr>
                        <td colspan="4" align="center" style="padding-left:10px;padding-right:10px;">
		<table width="80%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
        <tr bgcolor="#f2f2f2" align="center"> 
                <th nowrap="nowrap" class="listing-head" style="padding-left:5px; padding-right:5px;">Challan No </th>
                <th align="center" class="listing-head" style="padding-left:5px; padding-right:5px;">Date</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Status</th>
		<?
			if ($qtySearchType=='DT') {
		?>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Process Code</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Count</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Grade</th>
		<?
			}
		?>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Qty not confirmed</th>
                <th class="listing-head" style="padding-left:5px; padding-right:5px;">Qty Confirmed</th>
		<th class="listing-head" style="padding-left:5px; padding-right:5px;">Qty Paid</th>		
	  </tr>
              <?php
		$totalPaidQty = 0;
		$totalConfirmedQty = 0;
		$totalNotConfirmedQty = 0;
		$prevSelBillCompanyId = "";
		$prevBillCompanyId = 0;
		$notConfirmedQtyArr = array();
		$confirmedQtyArr = array();
		$paidQtyArr = array();
		$prevBillingArr = array();
		$p=0;
		
		$colSpan = "";
		if ($qtySearchType=='DT') $colSpan = 6;
		else $colSpan = 3;

		foreach ($dailyCatchEntryRecords as $psr) {
			$i++;
			$challanEntryId		=	$psr[0];		
			$challanNo		=	$psr[1];		
			$enteredDate		=	dateFormat($psr[2]);
			$challanPaid 		= 	$psr[3];
			// Find Supply Cost
			$rmSupplyCost		=	$purchasestatementObj->getSupplyCost($challanNo, $billingCompany);
			$rawMaterialCost	=	$psr[6];
			# Checking All RM Settled
			$checkAllRMSettled = $paymentstatusObj->challanRecords($fromDate, $tillDate, $challanEntryId, $selectSupplier);
			$displayPaymentStatus = "";
			$paidAmount = "";
			$settledAmount = "";
			$rateEnteredAmt = "";
			$effectiveQty	= $psr[5];
			$confirm	= $psr[4];
			$paidQty = "";
			$confirmedQty = "";
			$notConfirmedQty = "";
			if ($challanPaid=='Y') {
				$displayPaymentStatus = "<span style=\"color:#003300\"><strong>Paid </strong></span> ";
				$paidQty = $effectiveQty;
				$totalPaidQty += $paidQty;				
			} else {
				$displayPaymentStatus = "";
				if ($confirm==1) {
					$displayPaymentStatus = "<span style=\"color:#DF610D\"><strong>Confirmed</span></strong>";
					$confirmedQty = $effectiveQty;
					$totalConfirmedQty += $confirmedQty;
				} else {
					$displayPaymentStatus = "<span style=\"color:#FF0000\"><strong>Pending</span></strong>";
					$notConfirmedQty = $effectiveQty;
					$totalNotConfirmedQty += $notConfirmedQty;
				}
			}	
			$processCodeId	= $psr[8];
			$processCodeRec		= $processcodeObj->find($processCodeId);
			$processCode		= stripSlash($processCodeRec[2]);	

			$catchEntryCount	= stripSlash($psr[9]);
			$gradeId		= $psr[10];
			$gradeCode = "";
			$raWReceivedBy		=	$psr[11];
			if ($catchEntryCount==""|| $catchEntryCount==0 || $raWReceivedBy=='B' ) {
				$gradeRec	=	$grademasterObj->find($gradeId);
				$gradeCode	=	stripSlash($gradeRec[1]);
			}	

			$selBillCompanyId 	=	$psr[12];			
			if ($i==1) $prevBillCompanyId = $selBillCompanyId;

			$notConfirmedQtyArr[$selBillCompanyId] += $notConfirmedQty;
		 	$confirmedQtyArr[$selBillCompanyId] += $confirmedQty; 
			$paidQtyArr[$selBillCompanyId] += $paidQty;
			
			if ($prevBillCompanyId!=$selBillCompanyId ) {
				$prevBillingArr[$p] = $prevSelBillCompanyId;
				echo '<tr bgcolor="#FFFFFF">
						<td class="listing-head" align="right" colspan="'.$colSpan.'" style="padding-left:5px; padding-right:5px;">TOTAL:</td>
						<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong>'.number_format($notConfirmedQtyArr[$prevBillCompanyId],2).'</strong></td>
						<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong>'.number_format($confirmedQtyArr[$prevBillCompanyId],2).'</strong></td>
						<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong>'.number_format($paidQtyArr[$prevBillCompanyId],2).'</strong></td>
					</tr>';
					$p++;
			}

			if ($prevSelBillCompanyId!=$selBillCompanyId) {
				if ($selBillCompanyId>0) {	// Getting Rec from other billing company
					list($companyName,$address,$place,$pinCode,$country,$telNo,$faxNo) = $billingCompanyObj->getBillingCompanyRec($selBillCompanyId);
				} else {	// Getting Rec from Company Details Rec
					list($companyName,$address,$place,$pinCode,$country,$telNo,$faxNo) = $companydetailsObj->getForstarCompanyDetails();
				}
				echo '<tr bgcolor="white"><td class="fieldname" colspan="10" style="padding-left:10px; padding-right:10px;" nowrap><b>'.$companyName.'</b></td></tr>';
			}
			$displayChallanNum = 	$psr[13];
		?>
              <tr bgcolor="#FFFFFF"> 
                <td class="listing-item" nowrap height='25' style="padding-left:5px; padding-right:5px;"><?=$displayChallanNum?><input type="hidden" name="challanEntryId_<?=$i?>" value="<?=$challanEntryId?>"></td>
                <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$enteredDate?></td>
		 <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px" align="center"><?=$displayPaymentStatus?></td>
		<?
			if ($qtySearchType=='DT') {
		?>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px" align="left"><?=$processCode?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px" align="left"><?=$catchEntryCount?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; line-height:normal; font-size:11px" align="left"><?=$gradeCode?></td>
		<?
			}
		?>
 		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?if($notConfirmedQty) echo number_format($notConfirmedQty,2);?></td>
                <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?if($confirmedQty) echo number_format($confirmedQty,2);?></td>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><? if ($paidQty) echo number_format($paidQty,2);?></td>		
	    </tr>
		<?php				
			if (sizeof($dailyCatchEntryRecords)==$i && sizeof($paidQtyArr)>1) {
		?>
		<tr bgcolor="#FFFFFF"> 
			<td class="listing-head" align="right" colspan="<?=$colSpan?>" style="padding-left:5px; padding-right:5px;">TOTAL:</td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong><?=number_format($notConfirmedQtyArr[$selBillCompanyId],2);?></strong></td>
			<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong><?=number_format($confirmedQtyArr[$selBillCompanyId],2);?></strong></td>
			<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
			<?=number_format($paidQtyArr[$selBillCompanyId],2);?></strong></td>			
		</tr>
		<?php
			} // Sub Total
		?>
		<?php	
			$prevSelBillCompanyId=$selBillCompanyId;
			$prevBillCompanyId 	= $selBillCompanyId;	
			}
		?>
              <tr bgcolor="#FFFFFF">
		<td class="listing-head" nowrap align="right" colspan="<?=$colSpan?>" style="padding-left:5px; padding-right:5px;">
			<? if (sizeof($prevBillingArr)>0) {?>GR.<? }?> TOTAL:
		</td>
                <td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong>
                  <? echo number_format($totalNotConfirmedQty,2);?></strong></td>
                <td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
                  <? echo number_format($totalConfirmedQty,2);?></strong></td>
		<td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
                  <? echo number_format($totalPaidQty,2);?></strong></td>		
              </tr>
			  
      </table></td>
	<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>" >
	</tr>
	<?php
		}
	?>
	<?php
	# Account Settlement Summary
	if ($searchType=='ACS' && $selectSupplier!="") {

	?>
	<tr>
        	<td colspan="4" align="center" style="padding-left:10px;padding-right:10px;">
		<table cellpadding="0" cellspacing="0">
			<tr>
			<TD>
			<table>
			<TR>
				<TD class="listing-item">
					<u>Raw Material Purchased</u>
				</TD>
				<td>&nbsp;</td>
				<td class="listing-item">
					<u>On A/c Paid</u>
				</td>
			</TR>
			<!--tr><TD height="5"></TD></tr-->
			<TR>
			<TD valign="top">
				<table width="80%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
					<tr bgcolor="#f2f2f2" align="center"> 
						<th nowrap="nowrap" class="listing-head" style="padding-left:10px; padding-right:10px;">Period </th>
						<th align="center" class="listing-head" style="padding-left:10px; padding-right:10px;">Amount</th>
					</tr>
	<?php
		$totalPurchasedAmt = 0;
		$dateStart = $fromDate;
		$dateEnd = $tillDate;
		$datePrev = $dateStart;		
		$acType = $acFilterType; // Days 0 = summarized, 15 days, 30 days
		$calcNumDays = 0;
		$sDayOfMonth = "";
		$numDaysInMonth = "";
		$displayPeriod = "";
		
		while ($dateStart < $dateEnd ) {
			$numDaysInMonth = date('t',ctDateAdd('d', 0, $dateStart));			
			$sDayOfMonth = date('d',ctDateAdd('d', 0, $dateStart));
			if ($acType==0) {
				$calcNumDays = $paymentstatusObj->getDateDiff($fromDate, $tillDate);
			} else {
				$calcNumDays = $numDaysInMonth-$sDayOfMonth;				
				$diffDays    = $acType-$sDayOfMonth; 
			}
			$dateTo = "";
			if ($sDayOfMonth>=15 || $acType==30 || $acType==0) {
				$dateTo = date('Y-m-d',ctDateAdd('d', $calcNumDays, $dateStart));
			} else if ($sDayOfMonth<15) {
				$dateTo = date('Y-m-d',ctDateAdd('d', $diffDays, $dateStart));
			}

			# Purchased Amt
			$purchasedAmt = $paymentstatusObj->getPurchasedAmount($dateStart, $dateTo, $selectSupplier, $selSettlementDate);

			$displayPeriod = dateFormat($dateStart)."&nbsp;to&nbsp;".dateFormat($dateTo);
		
			$totalPurchasedAmt += $purchasedAmt;
	?>
		<tr bgcolor="#FFFFFF"> 
			<td class="listing-item" nowrap height='25' style="padding-left:10px; padding-right:10px;">
				<?=$displayPeriod?>
			</td>
			<td class="listing-item" nowrap height='25' style="padding-left:10px; padding-right:10px;" align="right">
				<?=$purchasedAmt?>
			</td>
		</tr>
	<?php

		$dateStart = date('Y-m-d',ctDateAdd('d', 1, $dateTo));
		}
	?>
	<tr bgcolor="#FFFFFF"> 
		<td class="listing-head" nowrap height='25' style="padding-left:10px; padding-right:10px;" align="right">
			Total:
		</td>
		<td class="listing-item" nowrap height='25' style="padding-left:10px; padding-right:10px;" align="right">
			<strong><?=number_format($totalPurchasedAmt,2,'.',',');?></strong>
		</td>
	</tr>	
	</table>
	</TD>
	<td>&nbsp;</td>
	<td valign="top">
		<table width="80%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
		<tr bgcolor="#f2f2f2" align="center"> 
			<th nowrap="nowrap" class="listing-head" style="padding-left:10px; padding-right:10px;">Date </th>
			<th align="center" class="listing-head" style="padding-left:10px; padding-right:10px;">Amount</th>
		</tr>
		<?php
			$totalSupplierPaidAmt  = 0;
			foreach ($getSupplierPaymentRecs as $gspr) {
				$paymentDate 	 = dateFormat($gspr[0]);
				$supplierPaidAmt = $gspr[1];
				$totalSupplierPaidAmt += $supplierPaidAmt;

		?>
		<tr bgcolor="#FFFFFF"> 
			<td class="listing-item" nowrap height='25' style="padding-left:10px; padding-right:10px;"><?=$paymentDate?></td>
			<td class="listing-item" nowrap height='25' style="padding-left:10px; padding-right:10px;" align="right"><?=$supplierPaidAmt?></td>
		</tr>
		<?php
			}
		?>
		<tr bgcolor="#FFFFFF"> 
			<td class="listing-head" nowrap height='25' style="padding-left:10px; padding-right:10px;" align="right">
				Total:
			</td>
			<td class="listing-item" nowrap height='25' style="padding-left:10px; padding-right:10px;" align="right">
				<strong><?=number_format($totalSupplierPaidAmt,2,'.',',');?></strong>
			</td>
		</tr>
		</table>
	</td>
	</TR>
	</table>
	</TD></tr>
		<tr>
			<TD>
				<table cellpadding="0" cellspacing="0">
					<!--TR>
						<TD class="listing-head" align="center">
							<u>Summary</u>
						</TD>
					</TR-->
					<tr>
						<TD>
							<fieldset>
								<legend class="listing-item">Summary</legend>
							<?php
								# Net Payable Amt
								$netPayableAmt = $totalPurchasedAmt-$totalSupplierPaidAmt;
							?>
							<table>
								<TR>
									<TD class="listing-head" style="padding-left:10px;padding-right:10px;" align="right">Total Amount</TD>
									<td class="listing-item" align="right" style="padding-left:10px;padding-right:10px;">
										<strong><?=number_format($totalPurchasedAmt,2,'.',',');?></strong>
									</td>
								</TR>
								<TR>
									<TD class="listing-head" style="padding-left:10px;padding-right:10px;" align="right">On A/c Paid</TD>
									<td class="listing-item" style="padding-left:10px;padding-right:10px;" align="right">
										<strong><?=number_format($totalSupplierPaidAmt,2,'.',',');?></strong>
									</td>
								</TR>
								<TR>
									<TD class="listing-head" style="padding-left:10px;padding-right:10px;" align="right">
										Net Amount Payable
									</TD>
								<td class="listing-item" style="padding-left:10px;padding-right:10px;" align="right">	
									<strong><?=number_format($netPayableAmt,2,'.',',');?></strong>
								</td>
								</TR>
							</table>
							</fieldset>
						</TD>
					</tr>
				</table>
			</TD>
		</tr>
	</table>
	</td>
	</tr>
	<?php
		} // AC Settled Summary condition Ends
	?>

                      <tr> 
                        <td colspan="4" align="center" class="err1">
				<? if( (sizeof($settlementRecords)<=0 && sizeof($dailyCatchEntryRecords)==0) && $selectSupplier!="" && $searchType!='ACS'){ echo $msgNoSettlement;}?>
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
			<?php
				if ($searchType=='ACS') {
			?>			
			<tr>
                        <td colspan="4" align="center">
				<? if($print==true){?>
					<input type="button" name="View" value=" View / Print" class="button" onClick="return validateAccountStatementSearch(document.frmPaymentStatus, 'PrintAccountStatement.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&supplier=<?=$selectSupplier?>&acFilterType=<?=$acFilterType?>');" <? //if( sizeof($purchaseStatementRecords)==0) echo $disabled="disabled";?>>	
				<? }?>
			</td>
                      </tr>
			<?php
				}
			?>
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
	</table>	
	<SCRIPT LANGUAGE="JavaScript">
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
	</SCRIPT>	
	<SCRIPT LANGUAGE="JavaScript">
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
