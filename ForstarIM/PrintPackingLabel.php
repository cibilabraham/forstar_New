<?php	
	require("include/include.php");

	# Get Sales Order Id
	$selSOId = $g["selSOId"];
	$confirmed = false;
	
	// ----------------------------------------------------------
	# Find PO Records
	$sORec	=	$salesOrderObj->find($selSOId);
	$pOGenerateId	= $sORec[1];
	$distributorId	= $sORec[2];
	$selStateId	= $sORec[9];	
	$invoiceDate	= dateFormat($sORec[3]);
	$selCityId	= $sORec[11];
	$taxType	= $sORec[12];
	$billingForm	= $sORec[13];
	$taxAmount	= $sORec[14];
	$soRemark	= stripSlash($sORec[15]);
	$selTaxApplied	= $sORec[16];
	if ($selTaxApplied!="") $taxApplied	= explode(",",$sORec[16]);
	$invoiceType	= $sORec[20]; // T ->Taxable: S->Sample
	$soNetWt	= $sORec[17]; 
	$soGrossWt	= $sORec[18];
	$soTNumBox	= $sORec[19];
	$transporterId  = $sORec[21];
	$transporterName = "";
	if ($transporterId!="") {
		$transporterRec		= $transporterMasterObj->find($transporterId);		
		$transporterName	= stripSlash($transporterRec[2]);
	}
	if ($invoiceType=='S') {
		$additionalItemTotalWt = $sORec[22];
		$soGrossWt += $additionalItemTotalWt;
	}
	$poNo		= $sORec[23];
	$selAreaId	= $sORec[24];

	$poDate		= ($sORec[25]!=0)?dateFormat($sORec[25]):"";
	$challanNo	= $sORec[26];
	$challanDate	= ($sORec[27]!=0)?dateFormat($sORec[27]):"";

	$poNoDisplay	= $poNo;	
	if ($sORec[25]!=0)	$poNoDisplay .= ",&nbsp;$poDate";

	$challanNoDisplay = $challanNo;
	if ($sORec[27]!=0)	$challanNoDisplay .= ",&nbsp;$challanDate";

	$discount  = $sORec[28];
	$discountRemark  = $sORec[29];
	$discountPercent = $sORec[30];
	$discountAmt	 = $sORec[31];
	$grandTotalAmt   = $sORec[32];
	$roundVal	 = $sORec[33];
	if ($invoiceType=="S") $grandTotalAmt = 100;
	else $grandTotalAmt = round($grandTotalAmt+$roundVal);
	$invoiceConfirmed	= $sORec[38];
	
	$proformaInvoiceNo	= $sORec[39];
	$proformaInvoiceDate	= ($sORec[40]!='0000-00-00')?dateFormat($sORec[40]):"";
	$sampleInvoiceNo	= $sORec[41];
	$sampleInvoiceDate	= ($sORec[42]!='0000-00-00')?dateFormat($sORec[42]):"";
	
	$productMRPRateListId	= $sORec[8];
	
	// ----------------------------------------------------------
	
	$cityRec	=	$cityMasterObj->find($selCityId);
	$cityName	=	stripSlash($cityRec[2]);

	$stateRec	= $stateMasterObj->find($selStateId);				
	$stateName	= stripSlash($stateRec[2]);
	
	$areaRec	=	$areaMasterObj->find($selAreaId);
	$areaName	=	stripSlash($areaRec[2]);
	# Supplier Rec
	$distributorRec		= $salesOrderObj->getDistributorRec($distributorId, $selStateId, $selCityId, $selAreaId);	
	$distributorName	= stripSlash($distributorRec[2]);
	$address		= $salesOrderObj->getAddressFormat(stripSlash($distributorRec[12]));	
	$telNo			= stripSlash($distributorRec[11]);
	$vatNo			= stripSlash($distributorRec[8]);
	$tinNo			= stripSlash($distributorRec[9]);	
	$cstNo			= stripSlash($distributorRec[10]);
	$deliveryAddress	= $salesOrderObj->getAddressFormat(stripSlash($distributorRec[13]));
	$sameBillingAddress	= $distributorRec[14];	
	$pinCode		= stripSlash($distributorRec[15]);

	# Get dist Wise Tax Calc
	list($taxType, $taxRate) = $salesOrderObj->distWiseTaxInvoiceCalc($distributorId, $selStateId);
	
	# Get all SO Items
	//if ($selSOId) $salesOrderItemRecs = $salesOrderObj->fetchAllSalesOrderItem($selSOId);

	/* Company Rec Starts Here */
	$companyRec	=	$companydetailsObj->find($editIt);
	$cName		=	stripSlash($companyRec[1]);
	$cAddress	=	stripSlash($companyRec[2]);
	$cPlace		=	stripSlash($companyRec[3]);
	$cPinCode	=	stripSlash($companyRec[4]);
	$cCountry	=	stripSlash($companyRec[5]);
	$cTelNo		=	stripSlash($companyRec[6]);
	$cFaxNo		=	stripSlash($companyRec[7]);
	$vatTin		=	stripSlash($companyRec[8]);
	$cstTin		= 	stripSlash($companyRec[9]);
	/* Company Rec Ends Here */

	#  Number of Copy
	/*
	if ($invoiceConfirmed=='C') {
		$numCopy	= 3;
		$confirmed = true;
	} else {
		$numCopy	= 1;
	}*/

	$numCopy	= 1;

	$userName	= $sessObj->getValue("userName");
	$date		= date("d/m/Y");
?>
<html>
<head>
<title>PACKING LABEL</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
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
			setTimeout("displayBtn()",7000); //3500			
		}		
	}
</script>
</head>
<body topmargin="0" rightmargin="0" bottommargin="0" leftmargin="0">
<form name="frmPrintSOTaxInvoice" id="frmPrintSOTaxInvoice">
<table width="95%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right">
	<input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block">
</td>
</tr>
</table>
<?php
	# Number of Copy	
 for ($print=0;$print<$numCopy;$print++) {
?>
<table width='95%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>
<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	<tr bgcolor="White"><TD height="5"></TD></tr>
	<tr bgcolor="White">
		<TD style="padding-left:5px; padding-right:5px;">
			<table cellpadding="0" cellspacing="0" width="100%">
				<TR>
					<TD align="left" valign="top"><img src="images/ForstarLogo.png" alt=""/></TD>
					<td class="pageName" valign="bottom" align="center">			
						<span style="font-size:16px;">
							PACKING LABEL
						</span>						
					</td>
					<td align="right">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<TD>
									<table cellpadding="0" cellspacing="0">
										<TR>
											<TD class="listing-head" style="line-height:normal;"><font size="2px"><?=$forstinsfoods["fifoods"];?></font></TD>
										</TR>
										<TR>
											<TD class="listing-head" style="font-size:9px;text-align:center;" valign="top"><?=$divfrfoods["frfoods"];?></TD>
										</TR>
									</table>
								</TD>
							</tr>
							<tr>
								<TD class="print-SOTHead-item"><?=$addr["ADR1"]?></TD>
							</tr>
							<tr>
								<TD class="print-SOTHead-item"><?=$addr["ADR2"]?></TD>
							</tr>
							<tr>
								<TD class="print-SOTHead-item"><?=$addr["ADR3"]?>&nbsp;<?=$addr["ADR4"]?></TD>
							</tr>
							<tr>
								<TD class="print-SOTHead-item"><?=$companyArr["Email"]?></TD>
							</tr>
						</table>
					</td>
				</TR>
			</table>
		</TD>
	</tr>
 <tr bgcolor='white'>
	<td height="2"></td>
 </tr>
  <tr bgcolor='white'>
    <td colspan="17" align="RIGHT"></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2' height="5"></td>
	 </tr>
	</table>
	</td>
  </tr>
  <tr bgcolor=white>
	<td align="LEFT" valign="top" width='100%'>
	<table width='99%' cellpadding='0' cellspacing='0'  align="center">
		<?
			$numLine = 2;
		?>
		<tr>
		<?
			for ($nextRec=1; $nextRec<=8; ++$nextRec) {
		?>
			<TD>
			<table cellpadding='0' cellspacing='0'  align="center" class="print" width="200" >
			<TR><TD>
				<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder" style="padding:5 5 5 5;">
					<tr><TD height="5"></TD></tr>
					<tr>
					<td nowrap="nowrap" class="listing-head" colspan="2" height="30" style="padding-left:1px;padding-right:5px;font-size:18pt;">
						To<!--Delivery Address-->
					</td>
					</tr>
					<tr><TD height="10"></TD></tr>
					<tr>
					<td class="listing-item" nowrap="nowrap" colspan="2" height="30" style="padding-left:10px;padding-right:10px;font-size:18pt;">
						<strong>M/S.&nbsp;<?=$distributorName?></strong>
					</td>
					</tr>
					<tr>
					<td class="listing-item" height="30" colspan="2" style="padding-left:10px;padding-right:10px;font-size:14pt;line-height:30px;" nowrap>
						<? 
							if ($sameBillingAddress=='N') {
								echo $deliveryAddress;
							} else echo $address;
						?>
					</td>
					</tr>
					<tr>
						<td class="listing-item" height="30" colspan="2" style="padding-left:10px;padding-right:10px;font-size:14pt;" nowrap>
							<?=$cityName.",&nbsp;".$stateName?>
						</td>
					</tr>
					<?php 
						if ($pinCode!="") {
					?>
					<tr>
						<td class="listing-item" width='200' height="25" colspan="2" style="padding-left:10px;padding-right:10px;font-size:14pt;">
							PIN - &nbsp;<?=$pinCode?>
						</td>
					</tr>
					<?	
						}
					?>
					<?php 
						if ($telNo!="") {
					?>
					<!--<tr>
						<td class="listing-item" width='200' height='20' colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
							TEL - &nbsp;<?=$telNo?>
						</td>
					</tr>-->
					<?php 
						}
					?>
					
				</table>
		</TD></TR>
		</table>
		</TD>	
		<!--<td>s</td>-->
		<?php 
			//echo "$nextRec%$numLine==".$nextRec%$numLine;
			if($nextRec%$numLine == 0) { 
		?>
			</tr>
			<tr><TD height="35"></TD></tr>
			<tr>
		<?php 
			} // Next Row	
		} // Loop Ends here
		?>
	
		</tr>

	</table>
	</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center">
<table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  >
	<?php
		if (sizeof($salesOrderItemRecs)) {
	?>
      
      <?php
		$numRows = 20; // Setting No.of rows 20
		$j = 0;
	
		$decreaseRow = 1;
		if ($sameBillingAddress=='N') $decreaseRow = 3;
		if ($soRemark!="")	      $decreaseRow += 2	;		
		if ($numRows==sizeof($salesOrderItemRecs)) $numRows = $numRows-$decreaseRow;

		$salesOrderRecSize = sizeof($salesOrderItemRecs);

		# Find Balance Rows		
		$balanceRows = ($salesOrderRecSize%$numRows);
		if ($balanceRows<=1 && $balanceRows!=0) $numRows = $numRows-1; 
		
		$totalPage = ceil($salesOrderRecSize/$numRows);
		$totalAmount 	= 0;
		$totalNumMCPack = 0;
		$totalNumLoosePack = 0;
		$totalQuantity	= 0;
		$totalFreePkts = 0;
		$i = 0;
		$resultNumMCArr = array();
		$resultNumLPArr = array();
		$resultQtyArr   = array();
		$resultTotalArr = array();
		$rTotalFreePkts = array();
		foreach ($salesOrderItemRecs as $por) {
			$i++;
			$selProductId	= $por[2];			
			$unitRate	= $por[3];
			$quantity	= $por[4];
			$totalQuantity  += $quantity;
			$total		= ($invoiceType=='T')?$por[5]:0;			
			$productName	= "";
			$productRec	= $manageProductObj->find($selProductId);
			$productName	= $productRec[2];
			$totalAmount 	= $totalAmount + $total;
			$numMCPack	= $por[7];
			$totalNumMCPack += $numMCPack;
			$numLoosePack	= $por[8];
			$totalNumLoosePack += $numLoosePack;
			$resultNumMCArr[$j] += $numMCPack;
			$resultNumLPArr[$j] += $numLoosePack;
			$resultQtyArr[$j]   += $quantity;
			$resultTotalArr[$j] += $total;

			$freePkts 	= $por[13];
			$totalFreePkts += $freePkts;
			$totalFreePktsArr[$j] += $freePkts;
			$productMRP = $orderDispatchedObj->getProductMRP($selProductId, $productMRPRateListId);
	?>
      
	  	<?php
		if ($i%$numRows==0 && $salesOrderRecSize!=$numRows) {
			$j++;
		?>
		
	    </table></td></tr>
<!--  Sign Starts-->

<!-- Ends Here -->	
	</table>
	</td></tr>
     </table>
	<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	  <table width='95%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
	  <tr>
	  	<td>
	  		<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	  		<tr bgcolor='white'>
				<td height="5"></td>
 	  		</tr>
	   <tr bgcolor="White">
		<TD colspan="17" align="center">
			<table cellpadding="0" cellspacing="0" width="100%">
				<TR>
					<TD align="left" valign="top"><img src="images/ForstarLogo.png" alt=""/></TD>
					<td class="pageName" valign="bottom" align="center">
						<?php if ($invoiceType=='S') {?>	
						<span style="font-size:16px;">
							SAMPLE INVOICE
						</span>
						<? } else {?>
						<span style="font-size:16px;">
							TAX INVOICE
						</span>
						<? }?>
						<?php //if ($invoiceType=='S') {?>
						<!--<br>
						<span style="font-size:9px;">
							(NOT FOR COMMERCIAL PURPOSE)
						</span>-->
						<?php //}?>
						<?php
							if($print==0){
						?>
						<div id="printMsg" class="printSOMsg">(ORIGINAL) - Cont.</div>
						<?php
							 } else if ($print==1) {
						?>
							<div id="printMsg" class="printSOMsg">(DUPLICATE) - Cont.</div>
						<?php 
							} else  {
						?>
							<div id="printMsg" class="printSOMsg">(TRIPLICATE) - Cont.</div>
						<?php
							}
						?> 
					</td>
					<td align="right" valign="top">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<TD>
									<table cellpadding="0" cellspacing="0">
										<TR>
											<TD class="listing-head" style="line-height:normal;"><font size="2px"><?=$forstinsfoods["fifoods"];?></font></TD>
										</TR>
										<TR>
											<TD class="listing-head" style="font-size:9px;text-align:center;" valign="top"><?=$divfrfoods["frfoods"];?></TD>
										</TR>
									</table>
								</TD>
							</tr>
						</table>
					</td>
				</TR>
			</table>
		</TD>
	  </tr>
	<tr bgcolor="White"><TD height="5"></TD></tr>
	<tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="listing-head" nowrap="nowrap" align='left' colspan='2' height="5"></td>
	 </tr>
	</table>
	</td>
  </tr>
	<tr bgcolor=white>
	<td align="LEFT" valign="top" width='100%'>
	<table width='99%' cellpadding='0' cellspacing='0'  align="center" >
		
	</table>
	</td>
  </tr>
  <tr>
	<td colspan="17" align="center">
  	  <table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC" >
	
   <?php
	#Main Loop ending section 
			
	       }
	}
			# height 
			//$hgt = ( 10 + 8 ) * 20 - ($numRows * 20 ); // Original
			if ($balanceRows>0) $salesOrderRecSize = $balanceRows; 
			$hgt = ($salesOrderRecSize + (-2)) * 20 - ($numRows * 20 );			
			$defaultHgt = 80;
   ?>
	
    </table></td>
  </tr>
  <? } else {?>
 <!-- <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName"><span class="err1">
      <?=$msgNoRecords;?>
    </span></td>
  </tr>-->
	<? }?>
  <tr bgcolor="White">
	<TD colspan="17" align="center" >
		<table width='99%' cellpadding='0' cellspacing='0' >
			<tr>	
				<td >
		<table cellspacing='0' cellpadding='0' width="100%" >
		<!--<tr><TD height="5"></TD></tr>-->		
		<tr>
			<td>
			<table width="100%" cellpadding="0" cellspacing="0">		
		
		<tr>
			<TD>
				<table width="100%">
					<tr>
				<td align="left" class="fieldName" style="line-height:normal;paing-left:5px;">	
					
				</td>
				<!--colspan="6"-->
				<TD  style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
				</table>
			</TD>
		</tr>
			</table>
		</td>
	</tr>
	<tr>
	<td valign="top">
	<table width="98%" cellpadding="3">
	<?php
		if ($sameBillingAddress=='Y' || $soRemark=="") {
	?>	
      
	<? }?>
	
    </table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</TD>
  </tr>	 
</TD>
	</tr>
  </table>
</td>
</tr>
</table>
</form>	
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body>
</html>
<?php
	} // Num copy Ends here
?>