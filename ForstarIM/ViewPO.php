<?php	
	require("include/include.php");

	# Get PO Id
	$selPOId = $g["selPOId"];
	$purchaseOrderRec	= $purchaseorderObj->find($selPOId);
			
	//$purchaseOrderId	= $purchaseOrderRec[0];
	$mainId 		= $purchaseOrderRec[0];
	$selCustomerId		= $purchaseOrderRec[1];
	$dischargePort		= $purchaseOrderRec[2];
	$paymentTerms		= $purchaseOrderRec[3];
	$lastDate		= dateFormat($purchaseOrderRec[4]);
	$selectedDate	= $purchaseOrderRec[5];
	$selDate		= dateFormat($selectedDate);
	$totalNumMC		= $purchaseOrderRec[6];
	$totalValUSD		= $purchaseOrderRec[7];
	$totalValINR		= $purchaseOrderRec[8];
	$selCountry	= $purchaseOrderRec[9];
	$selPort	= $purchaseOrderRec[10];
	$selAgent	= $purchaseOrderRec[11];
	$poNo		= $purchaseOrderRec[12];
	$poDate		= ($purchaseOrderRec[13]!='0000-00-00')?dateFormat($purchaseOrderRec[13]):"";
	$shipmentInstrs	= $purchaseOrderRec[14];
	$documentInstrs = $purchaseOrderRec[15];
	$surveyInstrs	= $purchaseOrderRec[16];
	$commnPaymentInstrs = $purchaseOrderRec[17];
	$varients	= $purchaseOrderRec[18];
	$selCarriageMode	= $purchaseOrderRec[19];
	$otherBuyer			= stripSlash($purchaseOrderRec[20]);
	$selCurrencyId		= $purchaseOrderRec[21];	 
	$selUnit			= $purchaseOrderRec[23];

	list($cyRateListId, $cyCode, $cyValue) = $usdvalueObj->getCYRateList($selCurrencyId, $selectedDate);
	
	# Get Unit Recs 
	//$unitRecs = array("1"=>"Kgs","2"=>"Lbs");
	$unitTxt = ($selUnit>0)?$spoUnitRecs[$selUnit]:"";

	//echo $unitTxt;

	// ----------------------------------------------------------
	# Find PO Records
	if ($selPOId) $poItemRecs = $purchaseorderObj->getProductsInPO($selPOId);
	$exportAddrArr=$purchaseorderObj->getAllCompany();
	$exportAddrContact=$purchaseorderObj->getAllCompanyContact($exportAddrArr[0]);
	//printr($exportAddrArr);
	#  Number of Copy
	$numCopy	= 1; //3	
?>
<html>
<head>
<title>PURCHASE ORDER</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
div.watermark{
	display:none;
}
div.watermark{
	display:block;
	position:fixed;
	z-index:-1;
	width:100%;
	height:100%;
}
	div.content > *:first-child,x:-moz-any-link{margin-top:0;}/* ff only */
	div.watermark,x:-moz-any-link{z-index:auto;}/* ff only */
	div.watermark,x:-moz-any-link,x:default{z-index:-1;}/* ff3 only */

	div.watermark div{
		position:absolute;
		left:0;
		width:99%;
	}

/* watermark position */
/* horizontal */
div.left{text-align:left;}
div.center{text-align:center;}
div.right{text-align:right;}
	body:last-child:not(:root:root) div.right div{left:-160px;}/* safari only */

/* vertical */
div.top div{top:0;}
div.middle div{top:50%;margin-top:-80px;}
div.bottom div{bottom:2px;}

.wmark { display: block; }
@media print {
.wmark {
	display: block;
	position: absolute;
	z-index:-1;
	width:100%;
	height:100%;
	}
}
body {
	background-image: url('images/watermark.png');
	background-repeat: no-repeat;
	background-attachment:fixed;
}
</style> 
<script src="libjs/jquery/jquery-1.3.2.min.js" type="text/javascript"></script>
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
if (!document.all) {	
	window.captureEvents(Event.KEYPRESS);
	window.onkeypress=handler;
} else {
	document.onkeypress = handler;
}
 </script>

<script language="javascript" type="text/javascript">
/*
	function doSomething(e)
{
    var code;
    if (!e) var e = window.event;
    if (e.keyCode) code = e.keyCode;
    else if (e.which) code = e.which;
    var character = String.fromCharCode(code);
    alert('Character was ' + character);
}
*/
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
<body topmargin="0" rightmargin="0" bottommargin="0" leftmargin="25px;">
<form name="frmPrintPO" id="frmPrintPO">
<!--<table width="95%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right">
	<input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block">
</td>
</tr>
</table>-->
<?php
	# Number of Copy	
 for ($print=0;$print<$numCopy;$print++) {
?>

<!--<div class="wmark"><img alt="forstarfoods" src="images/watermark.png" /></div>-->
<!--<div class="watermark middle center"><div><img alt="forstarfoods" src="images/watermark.png" /></div></div>-->
<!--  style="background-image:url('images/watermark.png');"-->
<table width='95%' cellspacing='1' cellpadding='1' class="boarder" align='center' border="0">
<tr>
	<td>	
<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	<tr bgcolor="White"><TD height="5"></TD></tr>
	<tr bgcolor="White">
		<TD style="padding-left:5px; padding-right:5px;">
			<table cellpadding="0" cellspacing="0" width="100%">
				<TR>
					<TD align="left" valign="top"><img src="images/ForstarLogo.png" alt=""></TD>
					<td class="pageName" valign="bottom" align="center">						
						PURCHASE ORDER
					</td>
					<td align="right">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<TD>
									<table cellpadding="0" cellspacing="0">
										<TR>
											<TD class="listing-head" style="line-height:normal;"><font size="2px"><?=$exportAddrArr[1]?></font></TD>
										</TR>										
									</table>
								</TD>
							</tr>
							<tr>
								<TD class="print-SOTHead-item"><?=$exportAddrArr[2]?></TD>
							</tr>
							<tr>
								<TD class="print-SOTHead-item"><?=$exportAddrArr[3]?>, <?=$exportAddrArr[4]?>,<?=$exportAddrArr[5]?></TD>
							</tr>							
							<tr>
								<TD class="print-SOTHead-item"><?php 
								 
								 foreach($exportAddrContact as $expt1)
								 {
									 if($expt1[1]!='') echo $expt1[1].',';
									
								 }
								 ?>
								</TD>
							</tr>
							<tr>
								<TD class="print-SOTHead-item"><?php 
								 foreach($exportAddrContact as $expt2)
								 {
									
									 if($expt2[2]!='')  echo $expt2[2].',';
								 }
								 ?></TD>
							</tr>
							<tr>
								<TD class="print-SOTHead-item">
								<?php 
								foreach($exportAddrContact as $expt3)
								 {
									 if($expt3[3]!='') echo  $expt3[3].',';
								 }
								 ?>
								 </td>
								 </tr>
							<tr>
								<TD class="print-SOTHead-item"><?php 
								 foreach($exportAddrContact as $expt4)
								 {
									 if($expt4[4]!='') echo $expt4[4].',';
								 }
								 ?></TD>
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
  <!--<tr bgcolor=white>
	<td align="LEFT" valign="top" width='100%'>
	<table width='99%' cellpadding='0' cellspacing='0' class="print" align="center" style="border-bottom-width:0px">
		<tr>
			<TD width="45%" style="border-bottom-width:0px">
				<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
						To
					</td>
					</tr>
					<tr>
					<td class="listing-item" nowrap="nowrap" colspan="2" height="20" style="padding-left:10px;padding-right:10px;font-size:11px;">
						<strong>M/S.&nbsp;<?=$distributorName?></strong>
					</td>
					</tr>
					<tr>
					<td class="listing-item" width='350' height="20" colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
						<?=$address?>
					</td>
					</tr>
					<tr>
						<td class="listing-item" width='200' height="15" colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
							<?=$cityName.",&nbsp;".$stateName?>
						</td>
					</tr>
					<?php 
						if ($pinCode!="") {
					?>
					<tr>
						<td class="listing-item" width='200' height="15" colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
							PIN - &nbsp;<?=$pinCode?>
						</td>
					</tr>
					<?	
						}
					?>
					<?php 
						if ($telNo!="") {
					?>
					<tr>
						<td class="listing-item" width='200' height='20' colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
							TEL - &nbsp;<?=$telNo?>
						</td>
					</tr>
					<?php 
						}
					?>
					<?php
						if ($taxType=='CST') {
					?>
					<tr><TD height="2"></TD></tr>					
					<tr><TD height="2"></TD></tr>
					<?php
						} else {
					?>
					<tr><TD height="2"></TD></tr>					
					<tr><TD height="2"></TD></tr>
					<?php
						}
					?>
				</table>
			</TD>
			<TD width="45%" style="border-bottom-width:0px" valign="top">
				<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
				<tr>
				<td >
					<table cellspacing='0' cellpadding='0' width="100%" class="print" style="border-top-width:0px;border-left-width:0px;border-right-width:0px;">
						<tr>	
							<td height="22" valign="middle" style="padding-left:3px;padding-right:3px;">
								<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder" width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="print-listing-head"  valign="middle">
										INVOICE NO. :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										<?=$pOGenerateId?>
									</td>
								</TR>
							</table>
						</TD>						
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="print-listing-head"  valign="middle" nowrap="true" align="right" width="25%">
										DTD :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left" width="75%">
										<?=$createdDate?>
									</td>
								</TR>
							</table>
						</td>
					</tr>
				</table>	
							</td>
						</tr>
					</table>
				</td>
				</tr>
				<tr>
				<td >
					<table cellspacing='0' cellpadding='0' width="100%" class="print" style="border-top-width:0px;border-left-width:0px;border-right-width:0px;">
						<tr>	
							<td height="22" valign="middle" style="padding-left:3px;padding-right:3px;">
								<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder" width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="print-listing-head"  valign="middle">
										PO NO. :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										<?=$poNo?>
									</td>
								</TR>
							</table>
						</TD>
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="print-listing-head"  valign="middle" nowrap="true" width="25%" align="right" nowrap="true">
										DTD :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left" width="75%">
										<?=$poDate?>
									</td>
								</TR>
							</table>
						</td>
					</tr>
				</table>	
							</td>
						</tr>
					</table>
				</td>
				</tr>
				<tr>
				<td >
					<table cellspacing='0' cellpadding='0' width="100%" class="print" style="border-top-width:0px;border-left-width:0px;border-right-width:0px;">
						<tr>	
							<td height="22" valign="middle" style="padding-left:3px;padding-right:3px;">
								<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder" width="100%">
					<tr>
						<TD width="50%">
							<table cellpadding="0" cellspacing="0" border="0">
								<TR>
									<TD class="print-listing-head"  valign="middle" nowrap="true">
										D.CHALLAN NO. :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle" align="left">
										<?=$gatePassNo?>
									</td>
								</TR>
							</table>
						</TD>
						<td width="40%" align="right">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<TR>
									<TD class="print-listing-head"  valign="middle" nowrap="true" width="25%" align="right" nowrap="true">
										DTD :
									</TD>
									<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true" valign="middle" align="left" width="75%">
										<?=($gatePassNo)?dateFormat($gpassDate):"";?>
									</td>
								</TR>
							</table>
						</td>
					</tr>
				</table>	
							</td>
						</tr>
					</table>
				</td>
				</tr>
				<tr>
				<td >
					<table cellspacing='0' cellpadding='0' width="100%" class="print" style="border-top-width:0px;border-left-width:0px;border-right-width:0px;">
						<tr>	
							<td height="22" valign="middle" style="padding-left:3px;padding-right:3px;">
								<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder">
					<TR>
						<TD class="print-listing-head">
							DESTINATION :
						</TD>
						<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true">
							<?php echo $cityName.",&nbsp;".$stateName;?>
						</td>
					</TR>
				</table>	
							</td>
						</tr>
					</table>
				</td>
				</tr>
				<tr>
				<td >
					<table cellspacing='0' cellpadding='0' width="100%" class="print" style="border-top-width:0px;border-left-width:0px;border-right-width:0px;border-bottom-width:0px;">
						<tr>	
							<td height="22" valign="middle" style="padding-left:3px;padding-right:3px;">
								<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder">
					<TR>
						<TD class="print-listing-head">
							DESPATCH THROUGH :
						</TD>
						<td class="listing-item-print" style="padding-left:3px;padding-right:3px;" nowrap="true">
							<?=$transporterName?>
						</td>
					</TR>
				</table>	
							</td>
						</tr>
					</table>
				</td>
				</tr>
				</table>
			</TD>
		</tr>
	</table>
	</td>
  </tr>-->
  <tr bgcolor=white>
    <th colspan="17" align="center">
<table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?php
		if (sizeof($poItemRecs)) {
	?>
      <tr bgcolor="#f2f2f2" align="center">
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Sr.<br>No</th> 
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Fish</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Process Code</th>				
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;">EU Code</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Brand</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Grade</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Freezing Stage</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Frozen Code</th>	
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;">MC Pkg</th>
                <th class="p-listing-head" style="padding-left:3px; padding-right:3px;">No of MC</th>
				   <th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Allocated MC</th>
				     <th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Balance MC</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Price per <span class="replaceUnitTxt">Kg</span> in <span class="replaceCY">USD</span></th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Value in <span class="replaceCY">USD</span></th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;">Value in INR</td>
      </tr>
      <?php
		$numRows = 20; // Setting No.of rows 18/20
		$j = 0;
	
		$decreaseRow = 1;
		if ($sameBillingAddress=='N') $decreaseRow = 3;
		if ($soRemark!="")	      $decreaseRow += 2	;		
		if ($numRows==sizeof($poItemRecs)) $numRows = $numRows-$decreaseRow;

		$salesOrderRecSize = sizeof($poItemRecs);

		# Find Balance Rows		
		$balanceRows = ($salesOrderRecSize%$numRows);
		if ($balanceRows<=1 && $balanceRows!=0) $numRows = $numRows-1; 
		
		$totalPage = ceil($salesOrderRecSize/$numRows);
		$i = 0;

		$totNumMC	= 0;
		$totValInUSD	= 0;
		$totValInINR	= 0;
		foreach ($poItemRecs as $poi) {
			$i++;
			$poEntryId 	= $poi[0];
			$selFish 	= $poi[1];
			$selProcessCode = $poi[2];
			$selEuCode	= $poi[3];
			$selBrand	= $poi[4];
			$selBrdFrom	= $poi[13];
			//$selBrandId	  = $selBrd."_".$selBrdFrom;
			$selGrade	  = $poi[5];
			$selFreezingStage = $poi[6];
			$selFrozenCode    = $poi[7];
			$selMCPacking	  = $poi[8];
			$numMC		= $poi[9];
			$totNumMC += $numMC;
			$pricePerKg	= $poi[10];
			$valueInUSD	= $poi[11];
			$totValInUSD += $valueInUSD;
			$valueInINR	= $poi[12];
			$totValInINR += $valueInINR;
			$gradeId=$poi[19];
			//list($allocatedCount,$balCount) = $purchaseorderObj->getAllocatedMcno($selPOId,$poEntryId,$gradeId);
			$allocatedCount = $purchaseorderObj->checkFrozenPackingReady($poEntryId);
			$updatedeliveredStatus=$purchaseorderObj->updateDeliveredStatus($balCount,$poEntryId);
			$balCount=$numMC-$allocatedCount ;
	?>
      <tr bgcolor="#FFFFFF">
		<td height='20' class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;line-height:normal;" align="center">
			<?=$i?>
		</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" nowrap="true">
			<?=$selFish?>
		</td>	
		<td class="listing-item" align="left" style="padding-left:3px; padding-right:3px; font-size:8pt;"><?=$selProcessCode?></td>
		<td class="listing-item" align="left" style="padding-left:3px; padding-right:3px; font-size:8pt;"><?=$selEuCode?></td>
		<td class="listing-item" align="left" style="padding-left:3px; padding-right:3px; font-size:8pt;"><?=$selBrand?></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><?=$selGrade?></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" nowrap="true">
			<?=$selFreezingStage?>
		</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;">
			<?=$selFrozenCode?>
		</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;">
			<?=$selMCPacking?>
		</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">
			<?=$numMC?>
		</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">
			<?=$allocatedCount?>
		</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">
			<?=$balCount?>
		</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">
			<?=$pricePerKg?>
		</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">
			<?=$valueInUSD?>
		</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">
			<?=$valueInINR?>
		</td>
      </tr>
	  	<?php
		if ($i%$numRows==0 && $salesOrderRecSize!=$numRows) {
			$j++;
		?>
		<tr bgcolor="#FFFFFF">
			<td height="20" colspan="2" nowrap="nowrap" class="listing-head" align="right">Totalgggg:</td>
			<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$resultNumMCArr[$j-1];?></strong></td>


			<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$resultNumMCArr[$j-1];?></strong></td>
			
			<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=number_format($resultQtyArr[$j-1],0,'','');?></strong></td>
			<? if ($invoiceType=='T') {?>
			<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$totalFreePktsArr[$j-1]?></strong></td>
			<? }?>
			<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8px;" align="right"></td>
			<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><? echo number_format($resultTotalArr[$j-1],2);?></strong></td>
			
		</tr>
	    </table></td></tr>
<!--  Sign Starts-->
<tr bgcolor="White">
	<TD colspan="17" align="center" >
		<table width='99%' cellpadding='0' cellspacing='0' class="print" align="center" style="border-top-width:0px">
			<tr>	
				<td style="border-bottom-width:0px;border-top-width:0px">
					<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
						<tr><TD height="5"></TD></tr>
						<tr>
							<td align="left">
		<table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print" style="border-left-width:0px;">
		</table>
			</td>
		</tr>
		<tr><TD height="5"></TD></tr>		
		<tr>
			<td>
			<table width="100%" cellpadding="0" cellspacing="0">
				<TR>
					<TD valign="bottom" height="5" width="400px">			
					</td>
					<td rowspan="5" valign="bottom" style="line-height:100px;">
						<table width="100%" align="right" cellpadding="0" cellspacing="0" class="print" style="border-right-width:0px; border-top-width:0px; border-left-width:0px;border-bottom-width:0px;" >
							<tr>
								<td class="listing-item" nowrap align="left" style="padding-left:5px;">
								For <strong><?=$forstinsfoods["fifoods"];?> <br>
								<span style="font-size:7pt;"><?=$divfrfoods["frfoods"];?></span>
								</strong>
								<br><br><br><br><div align="center">Authorised Signatory</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
		<tr>
			<TD valign="bottom">
				<table width="98%" cellpadding="0" cellspacing="0">
					<TR>
						<TD valign="bottom" class="listing-item" style="padding-left:5px;">
						<?php
							if ($totalPage>1) echo "(Page $j of $totalPage)";
						?>
						</TD>
					</TR>
				</table>
			</TD>
		</tr>
		</table>
		</td>
	</tr>
	<!--<tr>
	<td valign="top">
	<table width="98%" cellpadding="3">
	  <tr>
	    <td colspan="6" valign="top" nowrap="nowrap" style="line-height:11px;" align="center">
		<table cellpadding="0" cellspacing="0" width="100%">
			<TR>
				<TD width="35%" class="fieldName" nowrap="true" align="center" style="line-height:normal;">
				</TD>
				<td width="35%" align="center" class="listing-item">
					<?php
						//if ($totalPage>1) echo "(Page $j of $totalPage)";
					?>
				</td>
				<td width="35%" align="right" class="listing-item" style="font-size:8px;" nowrap="true">&nbsp;</td>
			</TR>
		</table>
		
		</td>
	    </tr>
    </table>
	</td>
	</tr>-->
	</table>
	</td>
	</tr>
	</table>
	</TD>
  </tr>
<!-- Ends Here -->	
	</table>
	</td></tr>
     </table>
	<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	  <table width='95%' cellspacing='1' cellpadding='1' class="boarder" align='center' style="background-image:url('images/watermark.png');">
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
	<table width='99%' cellpadding='0' cellspacing='0' class="print" align="center" style="border-bottom-width:0px">
		<tr>
			<TD rowspan="6" width="400px;" style="border-bottom-width:0px"> 
				<table cellspacing='0' cellpadding='0' width="100%" class="tdBoarder">
					<tr>
					<td nowrap="nowrap" class="print-listing-head" colspan="2" height="15" style="padding-left:5px;padding-right:5px;">
						To
					</td>
					</tr>
					<tr>
					<td class="listing-item" nowrap="nowrap" colspan="2" height="20" style="padding-left:10px;padding-right:10px;font-size:11px;">
						<strong>M/S.&nbsp;<?=$distributorName?></strong>
					</td>
					</tr>
					<tr>
						<td class="listing-item" width='200' height="15" colspan="2" style="padding-left:10px;padding-right:10px;font-size:11px;">
							<?=$cityName.",&nbsp;".$stateName?>
						</td>
					</tr>
				</table>
			</TD>
			<TD style="padding-left:3px;padding-right:3px;border-bottom-width:0px;" nowrap="true" colspan="4" width="282px;" valign="middle">
				<table cellpadding="0" cellspacing="0" border="0" class="tdBoarder">
					<TR>
						<TD class="print-listing-head"  valign="middle">
							INVOICE NO. & DATE :
						</TD>
						<td class="listing-item-print" style="padding-left:3px;" nowrap="true" valign="middle">
							<?=$pOGenerateId.",&nbsp;".$createdDate?>
						</td>
					</TR>
				</table>
			</TD>		
		</tr>
	</table>
	</td>
  </tr>
  <tr>
	<td colspan="17" align="center">
  	  <table width="99%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<tr bgcolor="#f2f2f2" align="center">
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" width="5%" nowrap="true">SR.<br/>NO</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" width="50%" nowrap="true">DESCRIPTION OF GOODS</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" width="6%" nowrap="true">M/C</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" width="6%" nowrap="true">IND<br/> PKTS</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" nowrap="true" width="7%">TOTAL<br/> PKTS</th>
		<? if ($invoiceType=='T') {?>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" width="6%" nowrap="true">FREE<br/>PKTS</th>
		<? }?>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" width="8%" nowrap="true">RATE PER <br/>UNIT (RS.)</th>
		<th class="p-listing-head" style="padding-left:3px; padding-right:3px;" nowrap="true" width="12%">TOTAL<br/>(RS.)</th>
      </tr>
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
	<?php
		if ($salesOrderRecSize<$numRows && abs($hgt)>=$defaultHgt) {			
	?>
	<tr rowspan='8' height='<?=abs($hgt)?>' >
		<td nowrap="nowrap" class="listing-head" align="right">&nbsp;</td>	
		<td class="listing-item" align="right">&nbsp;</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>		
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>		
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>		
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>		
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>		
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>
	</tr>
	<?php
		} // height Ceck Ends ere
	?>	
	<?php	
		if ($totalPage>1) {
	?>
	<tr bgcolor="#FFFFFF">
        <td height="20" colspan="2" nowrap="nowrap" class="listing-head" align="right">Total&nbsp;(<?=$totalPage?>):</td>
        <td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$resultNumMCArr[$j]?></strong></td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$resultNumLPArr[$j]?></strong></td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=number_format($resultQtyArr[$j],0,'','');?></strong></td>
	<? if ($invoiceType=='T') {?>	
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">
		<strong><?=$totalFreePktsArr[$j]?></strong>
	</td>
	<? }?>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8px;" align="right"></td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><? echo number_format($resultTotalArr[$j],2);?></strong></td>
	
      </tr>	
	<?
		for ($p=1;$p<=$totalPage-1;$p++) {
	?>
		<tr bgcolor="#FFFFFF">
		<td height="20" colspan="2" nowrap="nowrap" class="listing-head" align="right">Totaljj&nbsp;(<?=$p?>):</td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$resultNumMCArr[$p-1]?></strong></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$resultNumLPArr[$p-1]?></strong></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=number_format($resultQtyArr[$p-1],0,'','');?></strong></td>
		<? if ($invoiceType=='T') {?>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">
			<strong><?=$totalFreePktsArr[$p-1]?></strong>
		</td>
		<? }?>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8px;" align="right"></td>
		<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><? echo number_format($resultTotalArr[$p-1],2);?></strong></td>
	</tr>	
	<?php
		}  // Total Loop
	?>
	<?php
		} // Balance Row check
	?>
<!-- Last Row -->
      <tr bgcolor="#FFFFFF">
        <td height="20" colspan="9" nowrap="nowrap" class="listing-head" align="right" style="padding-left:3px; padding-right:10px;">
		Total:
	</td>
        <!--<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>	
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>	
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8px;" align="right">&nbsp;</td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>
	 <td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>-->
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=$totNumMC?></strong></td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>	
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right">&nbsp;</td>	
	 <td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=number_format($totValInUSD,2,'.',',')?></strong></td>
	<td class="listing-item" style="padding-left:3px; padding-right:3px; font-size:8pt;" align="right"><strong><?=number_format($totValInINR,2,'.',',')?></strong></td>	
      </tr>	
	<?php
	if (sizeof($taxApplied)>0 && $invoiceType=='T') {	
		for ($j=0;$j<sizeof($taxApplied);$j++) {
			$selTax = explode(":",$taxApplied[$j]); // Tax Percent:Amt
	?>	
	<?php
		}	// For Loop Ends Here
	} // Tax Size Check Ends Here
	?>	
    </table></td>
  </tr>
  <? } else {?>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName"><span class="err1">
      <?=$msgNoRecords;?>
    </span></td>
  </tr>
	<? }?>
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
<script language="JavaScript" type="text/javascript">
		$(document).ready(function () {
			<?php
			if ($cyCode!="")
			{	
			?>
			$(".replaceCY").html('<?=$cyCode?>');
			<?php
			}	
			?>
			
			<?php
			if ($unitTxt!="")
			{	
			?>
			$(".replaceUnitTxt").html('<?=$unitTxt?>');
			<?php
			}	
			?>			
		});
	</script>