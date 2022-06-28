<?php
	require("include/include.php");
	$printMode = false;

	$dateFrom 	= $g["supplyFrom"];
	$dateTill 	= $g["supplyTill"];
	$selectSupplier	 = $g["supplier"];
	$acFilterType	= $g["acFilterType"];

	

	# -------------------- Billing Company started -------
	list($companyName,$address,$place,$pinCode,$country,$telNo,$faxNo) = $companydetailsObj->getForstarCompanyDetails();
	$displayAddress		= "";
	$displayTelNo		= "";
	if ($companyName)	$displayAddress = $address."&nbsp;".$place."&nbsp;".$pinCode;
	if ($telNo)		$displayTelNo	= $telNo;
	if ($faxNo)		$displayTelNo	.= "&nbsp;/&nbsp;".$faxNo;
	//echo $companyName."<br>".$displayAddress."<br>".$displayTelNo;
	# -------------------- Billing Company Ends Here -------

	// Finding Supplier Record
	$supplierRec	=	$supplierMasterObj->find($selectSupplier);
	$supplierName	=	$supplierRec[2];
		
	$fromDate	=	mysqlDateFormat($dateFrom);
	$selFromDate	= 	date("j M Y", strtotime($fromDate));
		
	$tillDate	=	mysqlDateFormat($dateTill);
	$selTillDate	= 	date("j M Y", strtotime($tillDate));

	# Get Supplier Payment Recs
		$getSupplierPaymentRecs = $paymentstatusObj->getSupplierPaymentRecords($fromDate, $tillDate, $selectSupplier);
	#Select the records based on date
	//$purchaseStatementRecords = $purchasestatementObj->filterPurchaseStatementRecords($selectSupplier, $fromDate,  $tillDate, $acConfirmed, $billingCompanyId);

	#Checking Print Mode
	if ($p["printButton"]!="") $printMode = true;
?>
<html>
<head>
<title><?=strtoupper($supplierName)?> ACCOUNT STATEMENT</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
 function printThisPage(printbtn) {
	document.getElementById("printButton").style.display="none";
	window.print();
	document.getElementById("printButton").style.display="block";
}
</script>
</head>
<body>
<form name="frmPrintAccountStatement" method="POST">
<table width="90%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right" nowrap>
		<input name="printButton" type="submit" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block">
</td>
</tr>
<!--<tr>
  <td align="right">&nbsp;</td>
</tr>-->
</table>
<table width='90%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
  <tr bgcolor=white> 
    <td colspan="2" align="center" class="listing-head" ><font size="4"><?=$companyName?></font> </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="2" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="2" align="center" class="listing-item" height="3"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="2" align="center" class="listing-item"><?=$displayAddress?><!--M53, MIDC, Taloja, New Bombay 412208--></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="2" align="center" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="2" align="center" class="listing-item">Tel: <!--022 2741 0807 / 2741 2376--><?=$displayTelNo?></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="2" align="center" class="listing-item"><font size="3"><strong>Account Statement of</strong></font> </td>
  </tr> 
  <tr bgcolor=white>
    <td colspan="2" align="center" class="listing-item" height="25"><font size="3"><strong>M/s.<?=strtoupper($supplierName)?></strong></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="2" align="center" class="listing-item"><font size="2"> For the period from <strong><?=$selFromDate?></strong> to <strong><?=$selTillDate?></strong></font></td>
  </tr>	
  <tr bgcolor=white> 
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
 <tr bgcolor=white> 
    <td colspan="4" align="center" style="padding-left:10px;padding-right:10px;">
		<table cellpadding="0" cellspacing="0" width="100%">
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
		<tr>
        <td colspan="2">
<table width="100%" cellpadding="3">
      <tr bgcolor="White">
        <td colspan="6" height="10"></td>
       </tr>
      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;">
		<? //echo date("d/m/Y");?>
		<?=date("j M Y", strtotime(date("Y-m-d")));?>
	</td>
        </tr>
      <!--<tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
        </tr>-->
    </table></td>
        </tr>
	</table>
	</td>
 </tr>	
<tr><TD></TD></tr>
  <!--<tr bgcolor=white> 
    <td colspan="2">
	<table width="99%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
              <tr bgcolor="#f2f2f2" align="center"> 
                <th nowrap="nowrap" class="fieldName" style="padding-left:5px; padding-right:5px;" width="20%">Challan No </th>
                <th align="center" class="fieldName" style="padding-left:5px; padding-right:5px;" width="20%">Date</th>
                <th class="fieldName" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" width="20%">Cost of Raw Material </th>
                <th class="fieldName" align="center" style="line-height:normal" width="20%">Transportation/<br />Ice/<br /> Commission if any </th>
                <th class="fieldName" style="padding-left:5px; padding-right:5px;" width="20%">Total</th>
              </tr>
              <?
		#Setting No.of Rows De=16
		$numRows 	=	16;				
		$purchaseStatementRecordSize = sizeof($purchaseStatementRecords);
		$totalPage = ceil($purchaseStatementRecordSize/$numRows);

		$i = 0;
		$j = 0;
		foreach($purchaseStatementRecords as $psr)
		{
			$i++;
			$challanId = $psr[0];
			$challanNo		=	$psr[1];			
			
	?>
              <tr bgcolor="#FFFFFF"> 
                <td class="listing-item" nowrap height='25' style="padding-left:5px; padding-right:5px;"><?=$challanNo;?></td>
                <td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" align="center"><?=$enteredDate?></td>
                <td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"> 
                  <?=$costRawMaterial?></td>
                <td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"> 
                  <? echo number_format($rmSupplyCost,2,'.','');?></td>
                <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"> 
                  <? echo number_format($totalCostOfRawMaterial,2,'.','');?></td>
              </tr>
			  <?
	  					
			if($i%$numRows==0 && $purchaseStatementRecordSize!=$numRows)
			{
				$j++;
			  ?>
			  </table></td></tr>-->
  <tr bgcolor="white">
    <td colspan="2">
	<table width="100%" cellpadding="3">
      <tr>
        <td colspan="6" height="10"></td>
        </tr>
      
      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;"><? echo date("d/m/Y");?></td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;">(Page <?=$j?> of <?=$totalPage?>)</td>
        </tr>
    </table></td>
  </tr> </table>
    </td>
  </tr>
</table>
			  
	<!-- Setting Page Break start Here-->
	<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
			<table width='90%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
			<tr>
			<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
			<tr bgcolor=white> 
    <td colspan="2" align="center" class="listing-head" ><font size="3"><?=$companyName?></font> </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="2" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="2" align="center" class="listing-item" height="3"></td>
  </tr>
  
  <tr bgcolor=white>
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="2" align="center" class="listing-item"><font size="3"><b>Account Statement of M/s <strong><?=strtoupper($supplierName)?></strong></b></font> - Cont.</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="2" align="center" class="listing-item">
		<font size="2"> For the period From <strong><?=$selFromDate?></strong> Till <strong><?=$selTillDate?></strong></font>
	</td>
  </tr>  
  <tr bgcolor=white>
    <td colspan="2" align="center" class="listing-item">&nbsp;</td>
  </tr>
			<tr bgcolor="#FFFFFF">
			<td colspan="2">
			<table width="99%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
              
              <tr bgcolor="#f2f2f2" align="center"> 
                <th nowrap="nowrap" class="fieldName" style="padding-left:5px; padding-right:5px;" width="20%">Challan No </th>
                <th align="center" class="fieldName" style="padding-left:5px; padding-right:5px;" width="20%">Date</th>
                <th class="fieldName" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" width="20%">Cost of Raw Material </th>
                <th class="fieldName" align="center" style="line-height:normal" width="20%">Transportation/<br />Ice/<br /> Commission if any </th>
                <th class="fieldName" style="padding-left:5px; padding-right:5px;" width="20%">Total</th>
              </tr>
              <? 
		  	}
	  	}
	     ?>
              <tr bgcolor="#FFFFFF"> 
                <td class="listing-item" nowrap>&nbsp;</td>
                <td class="listing-head" align="center">TOTAL</td>
                <td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalRMCost,2);?></strong></td>
                <td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalRmSupplyCost,2);?></strong></td>
                <td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
                  <? echo number_format($grandTotalRMCost,2);?></strong></td>
              </tr>
      </table></td>
  </tr>
  <tr bgcolor=white> 
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <!--<tr bgcolor="white">
    <td colspan="2">	
	<table width="100%" align="center" cellpadding="0" cellspacing="0">
	<tr bgcolor="White">
	<td align="center">
	<table width="98%" cellpadding="0" cellspacing="0">      
      <tr>
        <td colspan="2">
<table width="100%" cellpadding="3">
      <tr bgcolor="White">
        <td colspan="6" height="10"></td>
       </tr>
      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared On </td>
        <td class="fieldName" nowrap="nowrap" valign="top">Prepared By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Checked by </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Approved by </td>
      </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;">
		<? //echo date("d/m/Y");?>
		<?=date("j M Y", strtotime(date("Y-m-d")));?>
	</td>
        </tr>
      <tr valign="top">
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
        </tr>
    </table></td>
        </tr>
    </table>	</td></tr></table>	</td>
    </tr>-->
</table>
</td></tr>
</table>
</td></tr>
</table>
</form>
</body>
</html>

