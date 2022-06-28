<?php	
	require("include/include.php");
	ob_start();
	# Get Sales Order Id
	$selSOId = $g["selSOId"];
	# Find the Pkg Instruction Main Id	
	$editPkngInstId = $packingInstructionObj->pkgInstrMainId($selSOId);
	//$editPkngInstId = $g["PkgInsMainId"];
	$pkngInstRec		= $salesOrderObj->find($selSOId); //findSORec($orderId)
	$invType = $pkngInstRec[20];
	$soNo 	= $pkngInstRec[1];
	$pfNo 	= $pkngInstRec[39];
	$saNo	= $pkngInstRec[41];				
	$sInvoiceNo = "";
	if ($soNo!=0) $sInvoiceNo=$soNo;
	else if ($invType=='T') $sInvoiceNo = "P$pfNo";
	else if ($invType=='S') $sInvoiceNo = "S$saNo";	


	if ($selSOId) {
			# List all ordered items
			$salesOrderEntryRecs = $packingInstructionObj->salesOrderEntryRecs($selSOId);

			$sORec = $packingInstructionObj->getSORecord($selSOId);
			$distributorName = $sORec[6];	
			$distributorId	 = $sORec[2];	
			$createDate	 = $sORec[3];	
			$salesOrderNo	 = $sORec[1];	
			$selStatusId	 = $sORec[7];	
			$selPaymentStatus   = $sORec[8];
			$lastDate	 = $sORec[20]; //dateFormat($lastDate)
			$selDispatchDate = ($sORec[9]!="")?dateFormat($sORec[9]):dateFormat($lastDate);
			$grossWt	 = $sORec[10];
			$invoiceType	= $sORec[18]; // T ->Taxable: S->Sample
			if ($invoiceType=='S') {
				//$additionalItemTotalWt = $sORec[19];
				$grossWt += $additionalItemTotalWt;
			}
	
			$extended	= $sORec[21];
			if ($extended=='E') $extendedChk = "Checked";
	
			$selTransporter	 = $sORec[11];
			$docketNo	 = $sORec[12];
			$transporterRateListId	= $sORec[13];
			$completeStatus	= $sORec[14];
			$confirmChecked = ($completeStatus=='C')?"Checked":"";	
			$selTaxApplied	= $sORec[15];
			if ($selTaxApplied!="") $taxApplied	= explode(",",$sORec[15]);
			$roundVal      = $sORec[16];
			$salesOrderTotalAmt = ($invoiceType=='T')?round($sORec[17]+$roundVal):100;
			$grandTotalAmt = round($sORec[17]+$roundVal);	
			$transOtherChargeRateListId = $sORec[22];
			$discount	 = $sORec[23];
			$discountRemark  = $sORec[24];
			$discountPercent = $sORec[25];
			$discountAmt	 = $sORec[26];	
			$octroiExempted = $sORec[27];
			$oecNo		= $sORec[28];
			$oecValidDate	= dateFormat($sORec[29]);	
			$invoiceNo	= $sORec[1];
			$invoiceDate	= dateFormat($sORec[3]);	
			$sampleInvoiceNo = $sORec[33];	
			$soNetWt	= $sORec[35]; 
			$soGrossWt	= $sORec[10];
			$soTNumBox	= $sORec[36];
			$productMRPRateListId	= $sORec[37];
			$distMgnRateListId	= $sORec[38];	
			$stateId		= $sORec[39];

		} // Chk SO Selected Ends Here
	

	# Edit  a Record
	if ($editPkngInstId!="") {
		$pkngInstRec		= $packingInstructionObj->find($editPkngInstId);
		$editPkngInstId 	= $pkngInstRec[0];

		$mcTotalActualWt	= $pkngInstRec[7];
		$additionalItemTotalWt	= $pkngInstRec[8];
	
		$totalGrossWt		= $pkngInstRec[9];
		$mcDoneBy		= $pkngInstRec[10];
		$verifiedBy		= $pkngInstRec[11];
		$packingConfirmStatus	= $pkngInstRec[12];
		$prdMCTotalActualWt	= $pkngInstRec[13];

		# Get Additional Item Recs
		$adnlItemRecs	= $packingInstructionObj->getAdnlItemRecs($editPkngInstId, $selSOId);
		
	}

	$numCopy	= 1;
	$userName	= $sessObj->getValue("userName");
	$date		= date("d/m/Y");
?>
<html>
<head>
<title>PACKING DETAILS</title>
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
<? //if ($packingConfirmStatus=='C') {?>
<script language="javascript">
var key1="119";
var x='';
function handler(e)
{
    var code;
    if (!e) var e = window.event;
    if (e.keyCode) code = e.keyCode;
    else if (e.which) code = e.which;		
    //alert(code);
    if (code=="112") {
	alert("Access denied for printing!!!");
	return false;		
    }
}
if (!document.all){	
	window.captureEvents(Event.KEYPRESS);
	window.onkeypress=handler;
} else {
	document.onkeypress = handler;
}
 </script>
<?// }?>
<style type="text/css">
@page
{
	size: landscape;
	margin: 2cm;
}
body { size: landscape;}
</style>
<style type="text/css" media="print">
@page
{
	size: landscape;
	margin: 2cm;
}
</style>
</head>
<body topmargin="0" rightmargin="0" bottommargin="0" leftmargin="0">
<form name="frmViewPkgInstruction" id="frmViewPkgInstruction">
<?
	if ($packingConfirmStatus!='C') {
?>
<!--<table width="100%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right">
	<input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block">
</td>
</tr>
</table>-->
<? }?>
<?php
	# Number of Copy	
 //for ($print=0;$print<$numCopy;$print++) {
?>
<table width='95%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>
<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	<tr bgcolor="White"><TD height="2"></TD></tr>
	<tr bgcolor="White">
		<TD style="padding-left:3px; padding-right:3px; font-size:8pt;">
			<table cellpadding="0" cellspacing="0" width="100%">
				<TR>
					<TD align="left" valign="top">
						<table>
							<TR><TD><img src="images/ForstarLogo.png" alt="" width="183" height="60"/></TD></TR>
							<TR><TD>
							<table cellpadding="0" cellspacing="0">
							<tr>
								<TD>
									<table cellpadding="0" cellspacing="0">
										<TR>
											<TD class="p-listing-head" style="line-height:normal;"><?=$forstinsfoods["fifoods"];?></TD>
										</TR>			
									</table>
								</TD>
							</tr>							
						</table>
							</TD></TR>
						</table>
						
					</TD>
					<td class="pageName" valign="middle" align="center" style="line-height:normal;">			
						<span style="font-size:16px;">
							PACKING INSTRUCTIONS
						</span>						
					</td>	
					<td align="right" style="padding-left:10px; padding-right:10px;">
						<table>
					<tr>
					<td class="fieldName" nowrap style="line-height:normal;">Invoice No:</td>
					<TD class="listing-item" style="line-height:normal;"><?=$sInvoiceNo?></TD>		
				</tr>
					<tr>
						<td class="fieldName" nowrap="nowrap" style="line-height:normal;">Distributor:</td>
							<td class="listing-item" nowrap="true" align="left" style="line-height:normal;"><?=$distributorName?></td>
					</tr>
					<tr>
						<td class="fieldName" nowrap="nowrap" style="line-height:normal;">Despatch Date:</td>
							<td class="listing-item" nowrap="true" align="left" style="line-height:normal;"><?=$selDispatchDate?></td>
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
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="p-listing-head" nowrap="nowrap" align='left' colspan='2' height="2"></td>
	 </tr>
	</table>
	</td>
  </tr>
  <tr bgcolor=white>
	<td align="LEFT" valign="top" width='100%'>
	<table width='99%' cellpadding='0' cellspacing='0'  align="center">		
	<?php
		if ($selSOId) {
	?>	
	<tr>
		<td colspan="2" style="padding-left:5px;padding-right:5px;">
			<table cellpadding="1"  width="98%" cellspacing="1" border="0" align="center" bgcolor="#999999" >
	<?php
	$m = 0;
	if (sizeof($salesOrderEntryRecs)>0) {		
	?>
	<tr  bgcolor="#f2f2f2" align="center">
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" rowspan="2">Sr.<br>No</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" rowspan="2">DESCRIPTION OF GOODS</th>
		<!--<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" rowspan="2">MRP</th>	
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" rowspan="2">TOTAL<br/>PKTS</th>		
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" rowspan="2">M/C Pkg</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" rowspan="2">M/C</th>-->
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" rowspan="2">MC Actual Wt</th>	
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" rowspan="2">BATCH NO<br>(PRODUCT)</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" colspan="3">PACKING MATERIAL USED</th>	
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" rowspan="2">Remarks</th>
	</tr>
	<tr bgcolor="#f2f2f2" align="center">
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" nowrap="true">BATCH NO</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" nowrap="true">Material Name</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" nowrap="true">Qty</th>
	</tr>
	<?
	$totalAmount = 0;
	$totalNumMCPack = 0;
	$totalNumLoosePack = 0;
	$totalQuantity = 0;
	$totalFreePkts = 0;
	$mcArray = array();
	$mc=0;
	$salesOrderEntryRecSize = sizeof($salesOrderEntryRecs);
	$j = 0;
	$numRows = 9; // Setting No.of rows 9
	$totalPage = ceil($salesOrderEntryRecSize/$numRows);
	
	$smc = 0;
	$sSubTableArr = array();
	$subTableRows = $numRows+10; //10
	foreach ($salesOrderEntryRecs as $sor) {		
		$snMCPack	= $sor[7];	
		//$sPrdMCPack = ($snMCPack!=0)?$snMCPack:1;	
		$sSubTableArr[$smc] += $snMCPack;
		$sArrSum = array_sum($sSubTableArr);	
		$sNArr =  $salesOrderEntryRecs[$smc+1];
		$sNRecNumPac = $sNArr[7];
		$sArrSum +=$sNRecNumPac;
		if ($sArrSum>=$subTableRows) {			
			$totalPage += 1;
			$sSubTableArr = array();
		}
		$smc++;
	}
	

	$i = 0;
	$subTableArr = array();
	$pr = 0;
	foreach ($salesOrderEntryRecs as $sor) {
		$i++;
		$prodRate	= $sor[3];
		$prodQty	= $sor[4];
		$totalQuantity  += $prodQty;
		$prodTotalAmt 	= $sor[5];
		$totalAmount 	+= $prodTotalAmt;
		$selProductId	= $sor[2];
		$productName	= "";
		$productRec	= $manageProductObj->find($selProductId);
		$productName	= $productRec[2];				
		$numMCPack	= $sor[7];
		$totalNumMCPack += $numMCPack;
		$numLoosePack	= $sor[8];
		$totalNumLoosePack += $numLoosePack;
		$freePkts = $sor[13];
		$totalFreePkts += $freePkts;
		$mcPakingId	= $sor[6];

		# Find MC Packs Details	---------------------------
			$mcpackingRec	= $mcpackingObj->find($mcPakingId);
			$casePack	= $mcpackingRec[2];
		# Product MRP$pkngInstRecUptd
		$productMRP = $orderDispatchedObj->getProductMRP($selProductId, $productMRPRateListId, $distributorId, $stateId);
		
		$productRec	= $manageProductObj->find($selProductId);
		$productNetWt	= $productRec[6];
		# Find Mc Wt
		$mcPkgWtId = $sor[17];
		$mcPackageWt 	= $mcPkgWtMasterObj->getPackageWt($mcPakingId, $productNetWt, $mcPkgWtId);

		//$sCasePack = ($numMCPack!=0)?$casePack:1;
		$nMCPack = ($numMCPack!=0)?$numMCPack:1;
		if ($numLoosePack!=0) {
			for ($mj=0;$mj<$nMCPack;$mj++) {
				//$mcArray[$mc] =  array($mcPakingId, $casePack, $selProductId, $mcPackageWt);
				$mcArray[$mcPakingId] =  array($mcPakingId, $casePack, $selProductId, $mcPackageWt);
				$mc++;
			}
		}

		$pkgInstPrdEId  = $sor[15];
		$remarks	= $sor[16];
		if ($pkgInstPrdEId) {
			$getPrdBatchNoRecs 	= $packingInstructionObj->getProductBtchRecs($pkgInstPrdEId);
			$getPrdPkngDtlsRecs 	= $packingInstructionObj->getProductPkngDtlsRecs($pkgInstPrdEId);
		}
		
	?>	
	<tr bgcolor="WHITE">
		<td height="50" align="center" class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" nowrap="true" valign="top">
			<?=$m+1?>			
		</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" nowrap="true" valign="top">			
			<table class="tdboarder">
				<TR><TD class="listing-item" nowrap><?=$productName?></TD></TR>
				<tr>
					<TD>
						<table>
						<tr>
							<TD valign="top">
							<table>
							<TR>
								<TD class="p-listing-head" nowrap>MRP :</TD>
								<TD class="listing-item" nowrap><strong><?=$productMRP?></strong></TD>
							</TR>
							</table>
							</TD>
							<TD valign="top">
							<table><TR>
								<TD class="p-listing-head" nowrap>NO. OF PKTS :</TD>
								<TD class="listing-item" nowrap><strong><?=$prodQty?></strong></TD>
							</TR></table>
							</TD>
						</tr>
						<tr>
							<TD valign="top">
							<table>
							<TR>
								<TD class="p-listing-head" nowrap>PKTS/MC :</TD>
								<TD class="listing-item" nowrap><strong><?=$casePack?></strong></TD>
							</TR>
							</table>
							</TD>
							<TD valign="top">
							<table>
							<TR>
								<TD class="p-listing-head" nowrap>NO. OF MC :</TD>
								<TD class="listing-item" nowrap><strong><?=$numMCPack?></strong></TD>
							</TR>
							</table>
							</TD>
						</tr>
						<TR>							
							<TD colspan="2">
							<table><TR><TD class="p-listing-head" nowrap>Gross Wt per MC =</TD>
							<td class="listing-item" nowrap><strong><?=$mcPackageWt?>&nbsp;Kg</strong> </td>
							</TR></table>
							</TD>
						</TR>
						</table>
					</TD>
				</tr>			
			</table>		
		</td>		
		<td class="listing-item" nowrap style="padding-left:3px; padding-right:3px; font-size:8pt;" align="left" valign="top" width="100">
			<table width="100%" cellspacing="1" bgcolor="#999999" cellpadding="3">
				<?php
					$mcPrdActualGrossWt = "";
					for ($b=0; $b<$numMCPack; $b++) {
						if ($pkgInstPrdEId) {
							$mcPrdActualGrossWt = $packingInstructionObj->prdWiseActualGrossWt($pkgInstPrdEId, $b);

						}					
				?>
					<TR bgcolor="White" align="center">
						<TD class="listing-item" width="10"><?=$b+1?>.</TD>
						<TD class="listing-item" ><?=$mcPrdActualGrossWt?></TD>
					</TR>
				<? }?>				
			</table>
		</td>		
		<td class="listing-item" nowrap align="center" valign="top" width="150">	
			<table width="100%" cellspacing="1" bgcolor="#999999" cellpadding="3">
				<?
				foreach ($getPrdBatchNoRecs as $gpb) {
					$pBtchNo = $gpb[1];
				?>
				<tr bgcolor="White"><td class="listing-item" nowrap><?=$pBtchNo?></td></tr>
				<? }?>
			</table>		
		</td>
		<td nowrap align="center" colspan="3" valign="top" width="300">
			<table width="100%" cellspacing="1" bgcolor="#999999" cellpadding="3">
			<?
				foreach ($getPrdPkngDtlsRecs as $gpbd) {
					$pkngBtchNo 	= $gpbd[1];
					$pkngMatName	= $gpbd[2];
					$pkngQty	= $gpbd[3];
			?>
			<tr bgcolor="White" align="center">
				<TD class="listing-item" nowrap width="50" ><?=$pkngBtchNo?></TD>
				<TD class="listing-item" nowrap width="200" ><?=$pkngMatName?></TD>
				<TD class="listing-item" nowrap width="50"><?=$pkngQty?></TD>
			</tr>
			<? }?>
			</table>		
		</td>		
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="center" valign="top">			
			<?=$remarks?>
		</td>
	</tr>
	<?php
		
		$prdMCPack = ($numMCPack!=0)?$numMCPack:1;
		$subTableArr[$i] += $prdMCPack; //$m 
		
		$arrSum = array_sum($subTableArr);
		$nArr =  $salesOrderEntryRecs[$i];
		$nRecNumPac = $nArr[7];
		$arrSum +=$nRecNumPac; 
		// (i+1)
		if ((($i+1)%$numRows==0 && $salesOrderEntryRecSize!=$numRows) || $arrSum>=$subTableRows) {
			$j++;
			$subTableArr = array();
			
	?>	
	</table>
	</TD>
	</tr>
	<tr bgcolor="White" >
		<td height="10"></td>
	</tr>
	<tr bgcolor="White" >
			<TD valign="bottom">
				<table width="98%" cellpadding="0" cellspacing="0">
					<TR>
						<TD valign="bottom" class="listing-item" style="padding-left:5px;padding-right:5px;" align="left">
						<?php
							if ($totalPage>1) echo "(Page $j of $totalPage)";
						?>
						</TD>
					</TR>
				</table>
			</TD>
		</tr>
	</table>
	</TD>
	</tr>
	
	</table>
	</td>
	</tr>	
	</table>	
<!--sdsads-->
	 <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	<table width='95%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
	<tr>
	<td>	
	<!--<table><TR><TD>-->
		<tr width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
			<tr bgcolor="White"><TD height="5"></TD></tr>
			<tr bgcolor="White">
		<TD style="padding-left:3px; padding-right:3px; font-size:8pt;">
			<table cellpadding="0" cellspacing="0" width="100%">
				<TR>
					<TD align="left" valign="top">						
						
					</TD>
					<td class="pageName" valign="middle" align="center">			
						<span style="font-size:14px;">
							PACKING INSTRUCTIONS (Contd.)
						</span>						
					</td>					
					<td align="right" style="padding-left:10px; padding-right:10px;">
						<table>
					<tr>
					<td class="fieldName" nowrap style="line-height:normal;">Invoice No:</td>
					<TD class="listing-item" style="line-height:normal;"><?=$sInvoiceNo?></TD>		
				</tr>
					<tr>
						<td class="fieldName" nowrap="nowrap" style="line-height:normal;">Distributor:</td>
							<td class="listing-item" nowrap="true" align="left" style="line-height:normal;"><?=$distributorName?></td>
					</tr>
					<tr>
						<td class="fieldName" nowrap="nowrap" style="line-height:normal;">Despatch Date:</td>
							<td class="listing-item" nowrap="true" align="left" style="line-height:normal;"><?=$selDispatchDate?></td>
					</tr>
				</table>
					</td>
				</TR>
			</table>
		</TD>
	</tr>
	 <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='99%' bgcolor="#f2f2f2">
         <tr>
           <td class="p-listing-head" nowrap="nowrap" align='left' colspan='2' height="5"></td>
	 </tr>
	</table>
	</td>
  </tr>
			<tr bgcolor="White">
			<TD style="padding-left:3px; padding-right:3px; font-size:8pt;">
			<table cellpadding="1"  width="98%" cellspacing="1" border="0" align="center" bgcolor="#999999" >
				<tr  bgcolor="#f2f2f2" align="center">
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" rowspan="2">Sr.<br>No</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" rowspan="2">DESCRIPTION OF GOODS</th>
		<!--<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" rowspan="2">MRP</th>	
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" rowspan="2">TOTAL<br/>PKTS</th>		
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" rowspan="2">M/C Pkg</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" rowspan="2">M/C</th>-->
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" rowspan="2">MC Actual Wt</th>	
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" rowspan="2">BATCH NO<br>(PRODUCT)</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" colspan="3">Package Details</th>	
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" rowspan="2">Remarks</th>
	</tr>
	<tr bgcolor="#f2f2f2" align="center">
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" nowrap="true">BATCH NO</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" nowrap="true">Material Name</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" nowrap="true">Qty</th>
	</tr>
	<?php			
		}
		$m++;
		}  // Main Product loop Ends here		
	?>					
	<tr bgcolor="white">		
		<td class="p-listing-head" align="right" colspan="2">
			<table class="tdBoarder">				
		<tr>
			<TD>
			<table>
				<TR>
					<TD class="p-listing-head">TOTAL:</TD>
					<TD>
					<table>
					<TR>
					<TD>
					<table>
									<TR><TD class="p-listing-head">PKTS</TD>
									<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;">
										<strong><?=$totalQuantity?></strong>
									</td>
									</TR>
									</table>			  
									</TD>
									<td>&nbsp;</td>
									<TD>
										<table>
										<TR><TD class="p-listing-head">M/C</TD>
										<TD class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;">
										<strong>
										<?=$totalNumMCPack?>
										</strong>
										</TD>				
										</TR>
										</table>		 
									</TD>
								</TR>
								</table>
							</TD>
						</TR>
						</table>
					</TD>
				</tr>
			</table>
		</td>		
		<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px; font-size:8pt;">
			<?=$prdMCTotalActualWt?>
		</td>
		<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px; font-size:8pt;"></td>
		<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px; font-size:8pt;" colspan="3"></td>		
		<td class="listing-item" align="right" style="padding-left:3px; padding-right:3px; font-size:8pt;"></td>
	</tr>	
	<input type="hidden" name="hidProductRowCount" id="hidProductRowCount" value="<?=$m?>" >
	<?php 
		} else { 
	?>
	<tr bgcolor="white">
		<td colspan="9"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>	
	<?php
		}
	?>
	<input type="hidden" name="hidProductRowCount" id="hidProductRowCount" value="<?=$m?>" >
	</table>
	</TD>
	</tr>
	<tr bgcolor="White">
		<TD colspan="2" style="padding-left:5px;padding-right:5px;" valign="top">
		<table>
			<TR>
			<TD valign="top">
				<fieldset style="padding: 5 5 5 5px;">
				<legend class="listing-item" nowrap>MC Gr. Wt - Loose Packs<!--MC Actual Gross Wt(LP)--></legend>
				<table cellpadding="1"  width="65%" cellspacing="1" border="0" align="center" bgcolor="#999999" >
				<?php
					if (sizeof($mcArray)>0) {
				?>
					<TR bgcolor="#f2f2f2" align="center">
						<Th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Sl.No</Th>
						<Th class="p-listing-head" style="padding-left:3px; padding-right:3px;">MC Pkg</Th>	
						<Th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Wt (Kg)</Th>
					</TR>
					<?php 
						$kId = 0;
						$mcPackageWt = "";
						foreach ($mcArray as $mca) {
							$mcPackId 	= $mca[0];
							$numCasePack	= $mca[1];
							$mcProductId	= $mca[2];
							$mcPackageWt	= $mca[3];

							if ($editPkngInstId) {
								$mcActualGrossWt = $packingInstructionObj->getMcActualGrossWt($editPkngInstId, $mcPackId, $mcProductId, $kId);
							}
					?>
					<TR bgcolor="WHITE">
						<TD class="listing-item" align="center" style="padding-left:3px; padding-right:3px; font-size:8pt;" height="30" width="120"><?=$kId+1?></TD>
						<TD class="listing-item" align="center" style="padding-left:3px; padding-right:3px; font-size:8pt;" height="20">X&nbsp;<?=$numCasePack?></TD>	
						<TD class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><?=$mcActualGrossWt?></TD>
					</TR>
					<?php 
						$kId++;
						} // MC Actual Gross Wt
					} // MC LP Ends here
					?>				
	<input type="hidden" name="hidMCActRowCount" id="hidMCActRowCount" value="<?=$kId?>">
					<TR bgcolor="white" align="center">
						<TD class="p-listing-head" style="padding-left:3px; padding-right:3px; font-size:8pt;" colspan="2">Total</TD>	
						<TD class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;"><strong><?=(sizeof($mcArray)>0)?$mcTotalActualWt:0;?></strong>			
						</TD>
					</TR>
				</table>
				</fieldset>
			</TD>
	<TD style="padding-left:5px;padding-right:5px;" valign="top">
		<table>
			<TR>
			<TD>
				<fieldset>
				<legend class="listing-item">Additional Item</legend>
				<table><TR><TD>
				<table  cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblSOAdditionalItem" >
		                <tr bgcolor="#f2f2f2" align="center">
					<th class="p-listing-head">Item</th>
					<th class="p-listing-head">Wt (Kg)</th>					
                        	</tr>
				<? foreach ($adnlItemRecs as $itemName=>$itemWt) {?>
				<tr bgcolor="white">
					<td class="listing-item" style="padding-left:5px;padding-right:5px;" height="30" width="180"><?=$itemName?></td>
					<td class="listing-item" align="right"><?=$itemWt?></td>					
                        	</tr>
				<? }?>
				<?
				if ($packingConfirmStatus!='C') {
					for ($c=0; $c<(5-sizeof($adnlItemRecs));$c++) {
				?>
				<tr bgcolor="white">
					<td class="listing-item" style="padding-left:5px;padding-right:5px;" height="30" width="180"><?//$itemName?></td>
					<td class="listing-item" align="right"><?//$itemWt?></td>					
                        	</tr>
				<?php
					}
				  }
				?>
				<tr bgcolor="white">
					<td class="p-listing-head" style="padding-left:5px;padding-right:5px;" nowrap>Total Wt:</td>
					<td class="listing-item" align="right"><?=$additionalItemTotalWt?>	</td>
					
                        	</tr>
				</table>
	<input type='hidden' name="hidItemTbleRowCount" id="hidItemTbleRowCount" value="">	
				</TD>
				</TR>
				<tr><TD height="5"></TD></tr>

				</table>	
				</fieldset>	
			</TD>
			</TR>
		</table>
	</TD>
	<td valign="top">
		<table>
		<TR>
		<TD valign="top">
		<fieldset>
		<!--<legend class="listing-item"></legend>-->
			<table>
			<TR>
				<TD class="fieldName" nowrap="true">Total Gross Wt:</TD>
				<td width="100px">
					<input type="text" name="totalGrossWt" id="totalGrossWt" value="<?=$totalGrossWt?>" size="8" style="border:none;text-align:right;font-weight:bold;">
				</td>
			</TR>
			<!--<TR>
				<TD class="fieldName">MC Done By:</TD>
				<td class="listing-item">
					<?=$mcDoneBy?>	
				</td>
			</TR>
			<TR>
				<TD class="fieldName">Verified By:</TD>
				<td class="listing-item"><?=$verifiedBy?>
				</td>
			</TR>-->
			</table>
		</fieldset>
		</TD>
		</TR>
		</table>
	</td>
			<TD><!--Another column--></TD>
			</TR>
		</table>
		</TD>
	</tr>
	<?php
		}
	?>
  <tr bgcolor="white">
    <td colspan="17" align="center">
<table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  >
 <tr bgcolor=white>
    <td align="right"><table width="200" cellpadding="3">
      <tr>
        <td colspan="6" height="20"></td>
        </tr>
      <tr valign="top">
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;MC Done By </td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;</td>
        <td class="fieldName" nowrap="nowrap" valign="top">&nbsp;Verified By </td>
      </tr>	
    </table></td>
  </tr>	
  <tr bgcolor="White">
	<TD colspan="17" align="center" >
		<table width='99%' cellpadding='0' cellspacing='0' >
			<tr>	
				<td >
		<table cellspacing='0' cellpadding='0' width="100%" >		
		<tr>
			<td>
			<table width="100%" cellpadding="0" cellspacing="0">		
		
		<tr>
			<TD>
				<table width="100%">
					<tr>
				<td align="left" class="fieldName" style="line-height:normal;paing-left:5px;">	
							<table cellpadding="0" cellspacing="0" width="100%">
					<TR>
						<TD width="50%" class="listing-item" nowrap="true" align="left" style="line-height:normal;padding-left:5px;">
							<?php
								if ($totalPage>1) echo "(Page $totalPage of $totalPage)";
							?>
						</TD>						
					</TR>
				</table>
				</td>
				<!--colspan="6"-->
				<TD  style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
				</table>
			</TD>
		</tr>
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
  </table>
</td>
</tr>
</table>
</form>	
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body>
</html>
<?php
//	} // Num copy Ends here
	$outputContents = ob_get_contents(); 
	ob_end_clean();
	echo $outputContents;
?>