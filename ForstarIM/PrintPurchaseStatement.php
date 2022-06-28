<?php
	
	require("include/include.php");
	$printMode = false;

	$dateFrom = $g["supplyFrom"];
	$dateTill = $g["supplyTill"];
	$selectSupplier	 =	$g["supplier"];
	
	# -------------------- Billing Company started -------
	$billingCompanyId = $g["billingCompany"];
	
	$unit = $g["unit"];

	if ($billingCompanyId>0) {	// Getting Rec from other billing company
		list($companyName,$address,$place,$pinCode,$country,$telNo,$faxNo) = $billingCompanyObj->getBillingCompanyRec($billingCompanyId);
	} else {	// Getting Rec from Company Details Rec
		list($companyName,$address,$place,$pinCode,$country,$telNo,$faxNo) = $companydetailsObj->getForstarCompanyDetails();
	}	
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
	$supplierPAN  = $supplierRec[13];
  // print_r($supplierPAN);

	$Date1		=	explode("/",$dateFrom);
	$fromDate	=	$Date1[2]."-".$Date1[1]."-".$Date1[0];
	
	$selFromDate	= 	date("j M Y", mktime(0, 0, 0, $Date1[1], $Date1[0], $Date1[2]));
	
	$Date2		=	explode("/",$dateTill);
	$tillDate	=	$Date2[2]."-".$Date2[1]."-".$Date2[0];
	$selTillDate	= 	date("j M Y", mktime(0, 0, 0, $Date2[1], $Date2[0], $Date2[2]));

	#Checking Confirm enabled or Disabled
	$acConfirmed = $manageconfirmObj->isACConfirmEnabled();
	#Select the records based on date
	$purchaseStatementRecords = $purchasestatementObj->filterPurchaseStatementRecords($selectSupplier, $fromDate,  $tillDate, $acConfirmed, $billingCompanyId, $unit);

	#Checking Print Mode
	if ($p["printButton"]!="") $printMode = true;

	
	if($unit!=0){ 
		$rec_unit = $plantandunitObj->find($unit);
		$companyName = $companyName." (Unit: $rec_unit[3])" ; 
	}
	?>
<html>
<head>
<title>STATEMENT OF RAW MATERIAL PURCHASE ACCOUNT</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
/*
 function printThisPage(printbtn) {
	document.getElementById("printButton").style.display="none";
	window.print();
	document.getElementById("printButton").style.display="block";
}*/
	function printDoc()
	{	
		window.print();	
		return false;
	}
	
	function displayBtn()
	{
		document.getElementById("printButton").style.display="block";			
	}
	
	function printThisPage(printbtn)
	{	
		document.getElementById("printButton").style.display="none";	
		if (!printDoc()) {
			setTimeout("displayBtn()",5000); //3500			
		}		
	}
</script>
</head>
<body>
<form name="frmPrintPurchaseStatement" method="POST">
<table width="90%" align="center" cellpadding="0" cellspacing="0">
<?if($printMode==true) {?>
<tr>
<td align="right" nowrap valign="top"><input name="printButton" type="button" id="printButton" value="Re-Print" class="button" onClick="printThisPage(this);" style="display:block" title="Click to Print"></td>
</tr>
<? }?>
<? if ($printMode==false) {?>
<tr>
<td align="right" nowrap valign="top"><input name="printButton" type="submit" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
<? }?>
<!--<tr>
<td align="right" nowrap valign="top"><?if($printMode==true) {?><input name="printButton" type="button" id="printButton" value="Re-Print" class="button" onClick="printThisPage(this);" style="display:block" title="Click to Print"><? }?>&nbsp;<? if ($printMode==false) {?><input name="printButton" type="submit" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"><? }?></td>
</tr>-->
</table>
<table width='90%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
<td>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
  <tr bgcolor="#FFFFFF"> 
	<td colspan="2" align="center" class="listing-head" ><font size="4"><?=$companyName?></font> </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan="2" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan="2" align="center" class="listing-item" height="3"></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan="2" align="center" class="listing-item"><?=$displayAddress?></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan="2" align="center" class="listing-item"></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan="2" align="center" class="listing-item">Tel: <?=$displayTelNo?></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan="2" align="center" class="listing-item"><font size="3"><strong>STATEMENT OF RAW MATERIAL PURCHASE ACCOUNT</strong></font> </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan="2" align="center" class="listing-item"><font size="2">Of M/s <strong><?=$supplierName?></strong> From <strong><?=$selFromDate?></strong> Till <strong><?=$selTillDate?></strong></font></td>
  </tr>

  <tr bgcolor="#FFFFFF">
    <td colspan="2" align="center" class="listing-item"><font size="2">PAN No :   <strong><?=$supplierPAN?> </strong></td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td colspan="2">
	<table width="99%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
              <tr bgcolor="#f2f2f2" align="center"> 
                <th nowrap="nowrap" class="fieldName" style="padding-left:5px; padding-right:5px;" width="16%">Challan No</th>
                <th align="center" class="fieldName" style="padding-left:5px; padding-right:5px;" width="16%">Date</th>
				<th align="center" class="fieldName" style="padding-left:5px; padding-right:5px;" width="16%">Quantity (in Kg.)</th>
                <th class="fieldName" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" width="16%">Cost of Raw Material </th>
                <th class="fieldName" align="center" style="line-height:normal" width="16%">Transportation/<br />Ice/<br /> Commission if any </th>
                <th class="fieldName" style="padding-left:5px; padding-right:5px;" width="16%">Total</th>
              </tr>
              <?php						
		$purchaseStatementRecordSize = sizeof($purchaseStatementRecords);
		#Setting No.of Rows De=16/24		
		$numRows 	= 24;
		$diffRows = ($numRows-$purchaseStatementRecordSize);	
		if ($diffRows<=0) $numRows = 21;
		$totalPage = ceil($purchaseStatementRecordSize/$numRows);

		$totalRMCost		= 	"";
		$totalRMQty		= 	"";
		$totalRmSupplyCost	=	"";
		$grandTotalRMCost	= "";
		$i = 0;
		$j = 0;
		foreach ($purchaseStatementRecords as $psr) {
			$i++;
			$challanId = $psr[0];

			#If Printed update the status Y in t_dailycatch_main
			if ($printMode)	$challanRecUptd = $purchasestatementObj->updateRMChallanPrintStatus($challanId);
			//print_r($psr);
			//echo("<br>");
			
			$challanNo		=	$psr[1];
			$displayChallanNum	= 	$psr[6];
			
			// Find Supply Cost
			$rmSupplyCost		= $purchasestatementObj->getSupplyCost($challanNo, $billingCompanyId);
			
			$array			=	explode("-",$psr[2]);
			$enteredDate		=	$array[2]."/".$array[1]."/".$array[0];
			$qty		=	$psr[3];
		/* rekha added code */
			//$display_unit	= 	$psr[7];
		
		/* end code */
		
			//$iceWt		=	$psr[3];
			$totalRmSupplyCost	+=	$rmSupplyCost;
		
			$costRawMaterial	=	$psr[5];
			$totalCostOfRawMaterial = 	$costRawMaterial + $rmSupplyCost;
			$totalRMCost		+=	$costRawMaterial;
			$totalRMQty = $totalRMQty + $qty ; 

			$grandTotalRMCost 	=  $totalRMCost + $totalRmSupplyCost;
	?>
              <tr bgcolor="#FFFFFF"> 
                <td class="listing-item" nowrap height='25' style="padding-left:5px; padding-right:5px;"><?=$displayChallanNum;?></td>
                <td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" align="center"><?=$enteredDate?></td>
				<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" align="center"><?=$qty?></td>
                <td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"> 
                  <?=$costRawMaterial?></td>
                <td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"> 
                  <? echo number_format($rmSupplyCost,2,'.','');?></td>
                <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"> 
                  <? echo number_format($totalCostOfRawMaterial,2,'.','');?></td>
              </tr>
	  <?php
		if ($i%$numRows==0 && $purchaseStatementRecordSize!=$numRows) {
			$j++;
	  ?>
  </table>
	</td>
	</tr>
  <tr bgcolor="#FFFFFF">
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
			<tr bgcolor="#FFFFFF"> 
    <td colspan="2" align="center" class="listing-head" ><font size="3"><?=$companyName?></font> </td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan="2" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan="2" align="center" class="listing-item" height="3"></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan="2" align="center" class="listing-item"><font size="3"><b>STATEMENT OF RAW MATERIAL PURCHASE ACCOUNT</b></font> - Cont.</td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan="2" align="center" class="listing-item">
	<font size="2">Of M/s <strong><?=$supplierName?></strong> From <strong><?=$selFromDate?></strong> Till <strong><?=$selTillDate?></strong></font>
</td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan="2" align="center" class="listing-item">&nbsp;</td>
  </tr>
	<tr bgcolor="#FFFFFF">
			<td colspan="2">
			<table width="99%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" class="print" align="center">
              <tr bgcolor="#f2f2f2" align="center"> 
                <th nowrap="nowrap" class="fieldName" style="padding-left:5px; padding-right:5px;" width="16%">Challan No </th>
                <th align="center" class="fieldName" style="padding-left:5px; padding-right:5px;" width="16%">Date</th>
				<th align="center" class="fieldName" style="padding-left:5px; padding-right:5px;" width="16%">Quantity (in Kg.)</th>
                <th class="fieldName" nowrap="nowrap" style="padding-left:5px; padding-right:5px;" width="16%">Cost of Raw Material </th>
                <th class="fieldName" align="center" style="line-height:normal" width="16%">Transportation/<br />Ice/<br /> Commission if any </th>
                <th class="fieldName" style="padding-left:5px; padding-right:5px;" width="16%">Total</th>
              </tr>
              <?php 
		  	}
	  	}
	     ?>
              <tr bgcolor="#FFFFFF"> 
                <td class="listing-item" nowrap>&nbsp;</td>
                <td class="listing-head" align="center">TOTAL</td>
				<td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalRMQty,2);?></strong></td>
                <td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalRMCost,2);?></strong></td>
                <td class="listing-item" nowrap align="right" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalRmSupplyCost,2);?></strong></td>
                <td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong> 
                  <? echo number_format($grandTotalRMCost,2);?></strong></td>
              </tr>
      </table></td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td colspan="2" align="center">&nbsp;</td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td colspan="2">
	<!-- New table -->
	<table width="200" align="center" cellpadding="0" cellspacing="0">
	<tr>
	<td align="center">
	<table width="90" cellpadding="0" cellspacing="0">
      <tr>
        <td><table width="100%" cellpadding="2" cellspacing="0">
      <tr>
        <td colspan="3" class="fieldName" nowrap="nowrap">Paid by Cheque No. : __________________</td>
        </tr>
      <tr>
        <td colspan="3" class="fieldName">Dated ______________________________</td>
        </tr>
      <tr>
        <td colspan="3" class="fieldName">Drawn on ___________________________</td>
        </tr>
      <tr>
        <td colspan="3" class="fieldName" nowrap="nowrap">(Rs. _______________________________)</td>
        </tr>
      <tr>
        <td colspan="3" class="fieldName">&nbsp;</td>
        </tr>
      <tr>
        <td colspan="3" class="fieldName">&nbsp;</td>
        </tr>
    </table></td>
        <td valign="top"><table width="100%" cellpadding="2" cellspacing="0" align="right">
      <tr>
        <td class="fieldName" align="right" style="line-height:normal; padding-right:2px;">Total Cost of Raw<br> Material  : </td>
        <td class="listing-item" style="line-height:normal">________________________</td>
      </tr>
      <tr>
        <td class="fieldName" nowrap="nowrap" align="right">Cost of Ice: </td>
        <td class="listing-item">________________________</td>
      </tr>
      <tr>
        <td class="fieldName" align="right" style="line-height:normal; padding-right:2px;">Cost of<br> Transportation : </td>
        <td class="listing-item">________________________</td>
      </tr>
      <tr>
        <td class="fieldName" nowrap="nowrap" align="right">Others : </td>
        <td class="listing-item">________________________</td>
      </tr>
      <tr>
        <td class="fieldName" nowrap="nowrap" align="right">Grand Total :  Rs.</td>
        <td class="listing-item" nowrap="nowrap">________________________</td>
      </tr>
      <tr>
        <td class="fieldName" nowrap="nowrap" align="right">Less Advance if any </td>
        <td class="listing-item">________________________</td>
      </tr>
      <tr>
        <td class="fieldName" nowrap="nowrap" align="right">Net payable Rs: </td>
        <td class="listing-item">________________________</td>
      </tr>
    </table></td>
      </tr>
      <tr>
        <td colspan="2"><table width="98%" cellpadding="3">
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
        <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" style="line-height:8px;">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
        </tr>
    </table></td>
        </tr>
    </table>
	</td></tr></table>
	</td>
    </tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</form>
</body>
</html>