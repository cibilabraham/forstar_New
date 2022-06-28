<?php	
	require("include/include.php");
	$dailyACStmntRecs = array();
		
	$dateFrom = $g["dateFrom"];
	$dateTill = $g["dateTill"];
	$fromDate	= mysqlDateFormat($dateFrom);
	$tillDate	= mysqlDateFormat($dateTill);

	$selDistributorId	= $g["selDistributor"];
	$pendingOrder		= $g["pendingOrder"];	
	$orderDispatched	= $g["orderDispatched"];

	$claimPending		= $g["claimPending"];
	$claimSettled		= $g["claimSettled"];
	$distributorAccount	= $g["distributorAccount"];
	$sampleInvoice		= $g["sampleInvoice"];
	$qryType		= $g["qryType"];
	$distACStmnt		= $g["distACStmnt"];
	
	if ($fromDate!="" && $tillDate!="") {
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

		# Daily Account statement
		if ($distACStmnt) $dailyACStmntRecs = $distributorReportObj->dailyACStatmentRecs($fromDate, $tillDate, $selDistributorId);
	}

	# Get Distributor Rec
	$distributorName = "";
	if ($selDistributorId) {
		$distributorRec		= $distributorMasterObj->find($selDistributorId);
		$distributorName	= stripSlash($distributorRec[2]);
	}	


	$userName	= $sessObj->getValue("userName");
	$date		= date("d/m/Y");
?>
<html>
<head>
<title>Distributor Report</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
	function displayBtn()
	{
		document.getElementById("printButton").style.display="block";
	}
	function printThisPage(printbtn)
	{
		document.getElementById("printButton").style.display="none";
		window.print();
		setTimeout("displayBtn()",2000);
		//document.getElementById("printButton").style.display="block";
	}
</script>
</head>
<body>
<form name="frmPrintDistributorReport">
<table width="85%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table>
<?php
	# Sales Order Report
	if ($pendingOrder!="" || $orderDispatched!="") {
?>
<table width='85%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>
	<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 <tr bgcolor='white'>
	<td height="10"></td>
 </tr>
<tr bgcolor="white">
    <td colspan="17" align="center">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<TD align="center">
				<table cellpadding="0" cellspacing="0">
					<TR>
						<TD class="printPageHead" style="line-height:normal;"><font size="4px"><?=FIF_COMPANY_NAME?></font></TD>
					</TR>
					<TR>
						<TD class="printPageHead" style="font-size:11px;text-align:center;" valign="top"><?=FIF_SUB_HEAD?></TD>
					</TR>
				</table>
			</TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_ADDRESS1?></TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_ADDRESS2?></TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_PHONE?></TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_EMAIL?></TD>
		</tr>
	</table>
</TD>
</tr>
  <tr bgcolor=white>
    <td colspan="17" align="RIGHT" height="5"></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='85%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>SALES ORDER REPORT
		OF <?=$distributorName?> FROM <?=$dateFrom?> TO <?=$dateTill?> 
	</td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		   </td>
		 </tr></table></td>
  </tr>
<tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
   <tr bgcolor=white> 
    <td colspan="17" align="LEFT" class="printPageHead" > </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="printPageHead"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center">
<table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?
	if ($salesOrderRecords>0) {
	?>
 	 <tr bgcolor="#f2f2f2" align="center">		
		<th class="printPageHead" align="center" style="padding-left:10px; padding-right:10px;" nowrap>SO ID</th>		
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Total Amt</th>
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Last Date</th>
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Status</th>	
        </tr>	
      <?
		$numRows = 14; // Setting No.of rows 14
		$j = 0;
		$salesOrderRecSize = sizeof($salesOrderRecords);
		$totalPage = ceil($salesOrderRecSize/$numRows);

		foreach ($salesOrderRecords as $sor) {
			$i++;
			$salesOrderId	= $sor[0];
			$soNo		= $sor[1];
			// Find the Total Amount of Each Sales Order		
			$salesOrderTotalAmt = $sor[4];
			$selStatusId	= 	$sor[11];
			$completeStatus	= 	$sor[12];

			$soInvoiceType		= $sor[13];
			$proformaNo	= $sor[14];
			$sampleNo	= $sor[15];
			$invoiceNo = "";
			if ($soNo!=0) $invoiceNo=$soNo;
			else if ($soInvoiceType=='T') $invoiceNo = "P$proformaNo";
			else if ($soInvoiceType=='S') $invoiceNo = "S$sampleNo";
	
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
				
		?>
      <tr bgcolor="#FFFFFF">
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="center"><?=$invoiceNo;?></td>	
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$salesOrderTotalAmt;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$lastDate;?></td>
		<td class="listing-item" align="center" nowrap style="padding-left:10px; padding-right:10px;"><?=$status?></td>					
      </tr>
	  	<?
		if ($i%$numRows==0 && $salesOrderRecSize!=$numRows) {
			$j++;
		?>
	    </table></td></tr>
		<tr bgcolor="#FFFFFF">
		<td colspan="17" align="center">
		<table width="85%" cellpadding="0" cellspacing="0">
        <tr>
        <td colspan="6" height="20"></td>
        </tr>	
	  <tr>
	    <td colspan="6" valign="bottom" nowrap="nowrap" class="listing-item" style="line-height:8px;" align="right">(Page <?=$j?> of <?=$totalPage?>)</td>
	    </tr>
		<tr><TD colspan="6" height="10"></TD></tr>
		<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
    	</table></td></tr>
	</table>
	</td></tr></table>
	<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	  <table width='85%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
	  <tr>
	  	<td>
	  		<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	  		<tr bgcolor='white'>
			<td height="10" colspan="17"></td>
 	  	</tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
		<table width="100%">
		<tr bgcolor=white>
    <td colspan="17" class="printPageHead" align="center" ><font size="3"><?=FIF_COMPANY_NAME?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="printPageHead" ></td>
  </tr>	</table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
	<table width='85%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>
			SALES ORDER REPORT - Cont.</td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		</td>		 
		 </tr>
	</table></td>
	    </tr>
	
	  <tr bgcolor=white>
	    <td colspan="17" align="center">
		</td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" height="5"></td>
	    </tr>	  
		<tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
	  <tr bgcolor="White"><td colspan="17" align="center">
	<table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<tr bgcolor="#f2f2f2">
		<th class="printPageHead" align="center" style="padding-left:10px; padding-right:10px;" nowrap>SO ID</th>		
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Total Amt</th>
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Last Date</th>
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Status</th>
	</tr>
   <?
	#Main Loop ending section 
			
	       }		
	}
   ?>
      <!--tr bgcolor="#FFFFFF">
        <td height='30' colspan="3" nowrap="nowrap" class="printPageHead" align="right">Total:</td>
        <td height='30' class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><strong><? echo number_format($totalAmount,2);?></strong></td>
      </tr-->
    </table></td>
  </tr>
  <? } else {?>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName"><span class="err1">
      <?=$msgNoRecords;?>
    </span></td>
  </tr><? }?>
  
  <tr bgcolor=white>
    <td colspan="17" align="center">
<table width="85%" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="6" height="20"></td>
        </tr>	
	  <tr>
	    <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" align="right">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
	    </tr>
		<tr><TD colspan="6" height="5"></TD></tr>
		<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
    </table></td>
  </tr>
</table>
</td>
</tr>
</table>
<?
	}
	# Sales Order Report Ends Here
?>

<?php
	# Claim Order Report
	if ($claimPending!="" || $claimSettled!="") {
?>
<table width='85%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>
	<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 <tr bgcolor='white'>
	<td height="10"></td>
 </tr>
	<tr bgcolor="white">
    <td colspan="17" align="center">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<TD align="center">
				<table cellpadding="0" cellspacing="0">
					<TR>
						<TD class="printPageHead" style="line-height:normal;"><font size="4px"><?=FIF_COMPANY_NAME?></font></TD>
					</TR>
					<TR>
						<TD class="printPageHead" style="font-size:11px;text-align:center;" valign="top"><?=FIF_SUB_HEAD?></TD>
					</TR>
				</table>
			</TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_ADDRESS1?></TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_ADDRESS2?></TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_PHONE?></TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_EMAIL?></TD>
		</tr>
	</table>
</TD>
</tr>
  <tr bgcolor=white>
    <td colspan="17" align="RIGHT" height="5"></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='85%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>CLAIM REPORT
		OF <?=$distributorName?> FROM <?=$dateFrom?> TO <?=$dateTill?> 
	</td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		   </td>
		 </tr></table></td>
  </tr>
<tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
   <tr bgcolor=white> 
    <td colspan="17" align="LEFT" class="printPageHead" > </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="printPageHead"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center">
<table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?
	if ($claimOrderRecords>0) {
	?>
 	 <tr bgcolor="#f2f2f2" align="center">		
		<th class="printPageHead" align="center" style="padding-left:10px; padding-right:10px;">Claim <br>Number</th>		
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Sales Order Number</th>	
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Claim Type</th>	
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Amount</th>	
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Last Date</th>
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Status</th>
        </tr>	
      <?
		$numRows	=	14; // Setting No.of rows 14
		$j = 0;
		$claimOrderRecSize = sizeof($claimOrderRecords);
		$totalPage = ceil($claimOrderRecSize/$numRows);

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
      <tr bgcolor="#FFFFFF">
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
		if ($i%$numRows==0 && $claimOrderRecSize!=$numRows) {
			$j++;
		?>
	    </table></td></tr>
		<tr bgcolor="#FFFFFF">
		<td colspan="17" align="center">
		<table width="85%" cellpadding="0" cellspacing="0">
        <tr>
        <td colspan="6" height="20"></td>
        </tr>	
	  <tr>
	    <td colspan="6" valign="bottom" nowrap="nowrap" class="listing-item" style="line-height:8px;" align="right">(Page <?=$j?> of <?=$totalPage?>)</td>
	    </tr>
		<tr><TD colspan="6" height="10"></TD></tr>
		<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
    	</table></td></tr>
	</table>
	</td></tr></table>
	<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	  <table width='85%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
	  <tr>
	  	<td>
	  		<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	  		<tr bgcolor='white'>
			<td height="10" colspan="17"></td>
 	  	</tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
		<table width="100%">
		<tr bgcolor=white>
    <td colspan="17" class="printPageHead" align="center" ><font size="3"><?=FIF_COMPANY_NAME?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="printPageHead" ></td>
  </tr>	</table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
	<table width='85%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>
			CLAIM REPORT - Cont.</td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		</td>		 
		 </tr>
	</table></td>
	    </tr>
	
	  <tr bgcolor=white>
	    <td colspan="17" align="center">
		</td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" height="5"></td>
	    </tr>	  
		<tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
	  <tr bgcolor="White"><td colspan="17" align="center">
	  	  <table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
 	 <tr bgcolor="#f2f2f2" align="center">		
		<th class="printPageHead" align="center" style="padding-left:10px; padding-right:10px;">Claim <br>Number</th>		
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Sales Order Number</th>	
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Claim Type</th>	
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Amount</th>	
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Last Date</th>
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Status</th>		
        </tr>	
   <?
	#Main Loop ending section 
			
	       }
		$prevStockId = $stockId;
	}
   ?>
      <!--tr bgcolor="#FFFFFF">
        <td height='30' colspan="3" nowrap="nowrap" class="printPageHead" align="right">Total:</td>
        <td height='30' class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><strong><? echo number_format($totalAmount,2);?></strong></td>
      </tr-->
    </table></td>
  </tr>
  <? } else {?>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName"><span class="err1">
      <?=$msgNoRecords;?>
    </span></td>
  </tr><? }?>
  
  <tr bgcolor=white>
    <td colspan="17" align="center">
<table width="85%" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="6" height="20"></td>
        </tr>	
	  <tr>
	    <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" align="right">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
	    </tr>
		<tr><TD colspan="6" height="5"></TD></tr>
		<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
    </table></td>
  </tr>
</table>
</td>
</tr>
</table>
<?
	}
	# Claim Order Report Ends Here
?>

<?php
	# Distributor Account Report
	if ($distributorAccount!="") {
?>
<table width='85%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>
	<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 <tr bgcolor='white'>
	<td height="10"></td>
 </tr>
<tr bgcolor="white">
    <td colspan="17" align="center">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<TD align="center">
				<table cellpadding="0" cellspacing="0">
					<TR>
						<TD class="printPageHead" style="line-height:normal;"><font size="4px"><?=FIF_COMPANY_NAME?></font></TD>
					</TR>
					<TR>
						<TD class="printPageHead" style="font-size:11px;text-align:center;" valign="top"><?=FIF_SUB_HEAD?></TD>
					</TR>
				</table>
			</TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_ADDRESS1?></TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_ADDRESS2?></TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_PHONE?></TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_EMAIL?></TD>
		</tr>
	</table>
</TD>
</tr>
  <tr bgcolor=white>
    <td colspan="17" align="RIGHT" height="5"></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='85%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>ACCOUNT REPORT
		OF <?=$distributorName?> FROM <?=$dateFrom?> TO <?=$dateTill?> 
	</td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		   </td>
		 </tr></table></td>
  </tr>
<tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
   <tr bgcolor=white> 
    <td colspan="17" align="LEFT" class="printPageHead" > </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="printPageHead"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center">
<table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?
	if ($distributorAccountRecords>0) {
	?>
 	 <tr bgcolor="#f2f2f2" align="center">		
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Date</th>		
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Particulars</th>
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Debit<br>(In Rs.)</th>	
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Credit<br>(In Rs.)</th>
        </tr>	
      <?
		$numRows	=	14; // Setting No.of rows 14
		$j = 0;
		$distributorAccountRecSize = sizeof($distributorAccountRecords);
		$totalPage = ceil($distributorAccountRecSize/$numRows);
		$totalCreditAmt = 0;
		$totalDebitAmt = 0;
		foreach ($distributorAccountRecords as $key=>$dar) {	
			$i++;
			//$distributorAccountId	= $dar[0];
			$selectDate		= dateFormat($dar[0]);			
			$particulars		= $dar[1];
			$amount			= $dar[2];
			$cod			= $dar[3];	
			//echo "$tDate-$cod";			
			$creditAmt = 0;
			$debitAmt  = 0;	
			if ($cod=="C")  {				
				$creditAmt = number_format($amount,2,'.','');
				$totalCreditAmt += $creditAmt;
			} else if ($cod=="D") {
		 		$debitAmt = number_format($amount,2,'.','');
				$totalDebitAmt += $debitAmt;
			}
			
	
		?>
      <tr bgcolor="#FFFFFF">
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selectDate;?></td>			
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="left">
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
		if ($i%$numRows==0 && $distributorAccountRecSize!=$numRows) {
			$j++;
		?>
	    </table></td></tr>
		<tr bgcolor="#FFFFFF">
		<td colspan="17" align="center">
		<table width="85%" cellpadding="0" cellspacing="0">
        <tr>
        <td colspan="6" height="20"></td>
        </tr>	
	  <tr>
	    <td colspan="6" valign="bottom" nowrap="nowrap" class="listing-item" style="line-height:8px;" align="right">(Page <?=$j?> of <?=$totalPage?>)</td>
	    </tr>
		<tr><TD colspan="6" height="10"></TD></tr>
		<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
    	</table></td></tr>
	</table>
	</td></tr></table>
	<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	  <table width='85%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
	  <tr>
	  	<td>
	  		<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	  		<tr bgcolor='white'>
			<td height="10" colspan="17"></td>
 	  	</tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
		<table width="100%">
		<tr bgcolor=white>
    <td colspan="17" class="printPageHead" align="center" ><font size="3"><?=FIF_COMPANY_NAME?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="printPageHead" ></td>
  </tr>	</table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
	<table width='85%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>
			ACCOUNT REPORT - Cont.</td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		</td>		 
		 </tr>
	</table></td>
	    </tr>
	
	  <tr bgcolor=white>
	    <td colspan="17" align="center">
		</td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" height="5"></td>
	    </tr>	  
		<tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
	  <tr bgcolor="White"><td colspan="17" align="center">
	  	  <table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
 	 <tr bgcolor="#f2f2f2" align="center">		
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Date</th>		
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Particulars</th>
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Credit<br>(In Rs.)</th>
		<th class="printPageHead" style="padding-left:10px; padding-right:10px;">Debit<br>(In Rs.)</th>	
        </tr>
   <?php
	#Main Loop ending section 			
	       }		
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
      <!--<tr bgcolor="#FFFFFF">
        <TD colspan="2" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">Total:</TD>
	<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=number_format($totalDebitAmt,2,'.',',')?></strong></td>
	<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=number_format($totalCreditAmt,2,'.',',')?></strong></td>	
      </tr>-->
    </table></td>
  </tr>
  <? } else {?>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName"><span class="err1">
      <?=$msgNoRecords;?>
    </span></td>
  </tr><? }?>
  
  <tr bgcolor=white>
    <td colspan="17" align="center">
<table width="85%" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="6" height="20"></td>
        </tr>	
	  <tr>
	    <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" align="right">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
	    </tr>
		<tr><TD colspan="6" height="5"></TD></tr>
		<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
    </table></td>
  </tr>
</table>
</td>
</tr>
</table>
<?php
	}
	# Distributor Account Report Ends Here
?>
<?php
	# Sample Invoice Report
	if ($sampleInvoice!="") {
?>
<table width='85%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<td>
	<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 <tr bgcolor='white'>
	<td height="10"></td>
 </tr>
<tr bgcolor="white">
    <td colspan="17" align="center">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<TD align="center">
				<table cellpadding="0" cellspacing="0">
					<TR>
						<TD class="printPageHead" style="line-height:normal;"><font size="4px"><?=FIF_COMPANY_NAME?></font></TD>
					</TR>
					<TR>
						<TD class="printPageHead" style="font-size:11px;text-align:center;" valign="top"><?=FIF_SUB_HEAD?></TD>
					</TR>
				</table>
			</TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_ADDRESS1?></TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_ADDRESS2?></TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_PHONE?></TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_EMAIL?></TD>
		</tr>
	</table>
</TD>
</tr>
  <tr bgcolor=white>
    <td colspan="17" align="RIGHT"  height="5" ></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='85%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>SAMPLE INVOICE REPORT
		OF <?=$distributorName?> FROM <?=$dateFrom?> TO <?=$dateTill?> 
	</td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		   </td>
		 </tr></table></td>
  </tr>
<tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
   <tr bgcolor=white> 
    <td colspan="17" align="LEFT" class="printPageHead" > </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="printPageHead"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center">
<table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?php
	if ($sampleInvoiceRecords>0) {
	?> 	
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="printPageHead" align="center" style="padding-left:10px; padding-right:10px;" nowrap>Date</td>
		<td class="printPageHead" align="center" style="padding-left:10px; padding-right:10px;" nowrap>Invoice No.</td>		
		<?php
			if ($qryType=='D') {
		?>
		<td class="printPageHead" style="padding-left:10px; padding-right:10px;">Product</td>
		<td class="printPageHead" style="padding-left:10px; padding-right:10px;">Qty</td>
		<td class="printPageHead" style="padding-left:10px; padding-right:10px;">Amt</td>
		<?php
			} else {
		?>
		<td class="printPageHead" style="padding-left:10px; padding-right:10px;">Total Amt</td>		
		<?php
			}
		?>
	</tr>
      <?php
		$numRows	= 14; // Setting No.of rows 14
		$j = 0;
		$sampleInvoiceRecSize = sizeof($sampleInvoiceRecords);
		$totalPage = ceil($sampleInvoiceRecSize/$numRows);
		$grandTotalSOAmt = 0;
		foreach ($sampleInvoiceRecords as $sor) {	
			$i++;
			$salesOrderId	= $sor[0];
			$poId		= $sor[1];
			$invoiceDate	= dateFormat($sor[3]);
				
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
		if ($i%$numRows==0 && $sampleInvoiceRecSize!=$numRows) {
			$j++;
		?>
	    </table></td></tr>
		<tr bgcolor="#FFFFFF">
		<td colspan="17" align="center">
		<table width="85%" cellpadding="0" cellspacing="0">
        <tr>
        <td colspan="6" height="20"></td>
        </tr>	
	  <tr>
	    <td colspan="6" valign="bottom" nowrap="nowrap" class="listing-item" style="line-height:8px;" align="right">(Page <?=$j?> of <?=$totalPage?>)</td>
	    </tr>
		<tr><TD colspan="6" height="10"></TD></tr>
		<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
    	</table></td></tr>
	</table>
	</td></tr></table>
	<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	  <table width='85%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
	  <tr>
	  	<td>
	  		<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	  		<tr bgcolor='white'>
			<td height="10" colspan="17"></td>
 	  	</tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
		<table width="100%">
		<tr bgcolor=white>
    <td colspan="17" class="printPageHead" align="center" ><font size="3"><?=FIF_COMPANY_NAME?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="printPageHead" ></td>
  </tr>	</table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
	<table width='85%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>
			SAMPLE INVOICE REPORT - Cont.</td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		</td>		 
		 </tr>
	</table></td>
	    </tr>
	
	  <tr bgcolor=white>
	    <td colspan="17" align="center">
		</td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" height="5"></td>
	    </tr>	  
		<tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
	  <tr bgcolor="White"><td colspan="17" align="center">
	  	  <table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
 	 <tr  bgcolor="#f2f2f2" align="center">		
		<td class="printPageHead" align="center" style="padding-left:10px; padding-right:10px;" nowrap>Date</td>
		<td class="printPageHead" align="center" style="padding-left:10px; padding-right:10px;" nowrap>Invoice No.</td>		
		<?php
			if ($qryType=='D') {
		?>
		<td class="printPageHead" style="padding-left:10px; padding-right:10px;">Product</td>
		<td class="printPageHead" style="padding-left:10px; padding-right:10px;">Qty</td>
		<td class="printPageHead" style="padding-left:10px; padding-right:10px;">Amt</td>
		<?php
			} else {
		?>
		<td class="printPageHead" style="padding-left:10px; padding-right:10px;">Total Amt</td>		
		<?php
			}
		?>
	</tr>
   <?php
	#Main Loop ending section 			
	       }		
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
    </table></td>
  </tr>
  <? } else {?>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName"><span class="err1">
      <?=$msgNoRecords;?>
    </span></td>
  </tr><? }?>
  
  <tr bgcolor=white>
    <td colspan="17" align="center">
<table width="85%" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="6" height="20"></td>
        </tr>	
	  <tr>
	    <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" align="right">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
	    </tr>
		<tr><TD colspan="6" height="5"></TD></tr>
		<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
    </table></td>
  </tr>
</table>
</td>
</tr>
</table>
<?
	}
	# Sample Invoice Report Ends Here
?>
<?php
	# DAILY ACCOUNT STATEMENT STARTS HERE --------------------------------------
	if ($distACStmnt!="") {
?>
<table width='85%' cellspacing='1' cellpadding='1' class="boarder" align='center' border="0">
<tr>
	<td>
	<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
  <tr bgcolor="white">
    <td colspan="17" align="center">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<TD align="center">
				<table cellpadding="0" cellspacing="0">
					<TR>
						<TD class="printPageHead" style="line-height:normal;"><font size="4px"><?=FIF_COMPANY_NAME?></font></TD>
					</TR>
					<TR>
						<TD class="printPageHead" style="font-size:11px;text-align:center;" valign="top"><?=FIF_SUB_HEAD?></TD>
					</TR>
				</table>
			</TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_ADDRESS1?></TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_ADDRESS2?></TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_PHONE?></TD>
		</tr>
		<tr>
			<TD class="listing-item" align="center"><?=FIF_EMAIL?></TD>
		</tr>
	</table>
</TD>
</tr>
  <tr bgcolor="white">
    <td colspan="17" align="RIGHT" height="5"></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF" style="padding-left:5px; padding-right:5px;">
	<table width='95%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='center' colspan='2'>
		<?=$distributorName?> Daily Account Statement From:<?=$dateFrom?> to:<?=$dateTill?> 
		<? if ($pendingChqMsg) {?>
		<br> <?=$pendingChqMsg?>
		<? }?>
	</td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		   </td>
		 </tr></table></td>
  </tr>  
  <tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" style="padding-left:5px; padding-right:5px;">
<table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?
	if ($dailyACStmntRecs>0) {		
		if (!$distributorFilterId) {
			$particularsWidth = "120px";
			$rowStyle = "padding-left:2px; padding-right:2px; font-size:11px; line-height:normal;";
			$numRows = 42;
		} else {
			$particularsWidth = "100%";
			$rowStyle = "padding-left:5px; padding-right:5px; font-size:11px;";
			$numRows = 42;
		}
		//echo $numRows;
	?>
 	 <tr bgcolor="#f2f2f2" align="center">
		<th class="printPageHead" style="<?=$rowStyle?>">Date</th>
		<? if (!$distributorFilterId) {?>
		<th class="printPageHead" style="<?=$rowStyle?>">Distributor</th>
		<? }?>
		<? if (!$cityFilterId) {?>
		<th class="printPageHead" style="<?=$rowStyle?>">City</th>
		<? }?>
		<? if (!$invoiceFilterId) {?>
		<th class="printPageHead" style="<?=$rowStyle?>">REF INVOICE</th>
		<? }?>
		<th class="printPageHead" style="<?=$rowStyle?>">Reason</th>		
		<th class="printPageHead" style="<?=$rowStyle?>" nowrap="true">Debit<br>(Rs.)</th>
		<th class="printPageHead" style="<?=$rowStyle?>" nowrap="true">Credit<br/>(Rs.)</th>
        </tr>	
	<?php
		$totalCreditAmt = 0;
		$totalDebitAmt = 0;
		if ($distributorFilterId && $openingBalanceAmt!=0 && $filterType=="VE") {
			if ($postType=="C")  {								
				$totalCreditAmt += abs($openingBalanceAmt);
			} else if ($postType=="D") {		 		
				$totalDebitAmt += abs($openingBalanceAmt);
			}
		?>
		<tr  bgcolor="WHITE">			
			<td class="listing-item" nowrap style="<?=$rowStyle?> "><?=$dateFrom;?></td>			
			<td class="listing-item" style="<?=$rowStyle?>" align="left" width="170" nowrap="true">
				Opening Balance
			</td>
			<td class="listing-item" style="<?=$rowStyle?>" align="right">
				<?=($postType=='D')?number_format($openingBalanceAmt,2,'.',''):""?>
			</td>
			<td class="listing-item" style="<?=$rowStyle?>" align="right">
				<?=($postType=='C')?number_format($openingBalanceAmt,2,'.',''):""?>
			</td>			
		</tr>
		<?		
			}
		?>
      	<?php
		//$numRows = 14; // Setting No.of rows 14
		$j = 0;
		$distributorAccountRecSize = sizeof($dailyACStmntRecs);
		$totalPage = ceil($distributorAccountRecSize/$numRows);
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
			$bankName	= $dar[13];
			$acNo		= $dar[14];
			$branchLocation 	= $dar[15];
			$depositedBankACNo	= $dar[16];
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
			
			#Ref Invoice
			$referenceInvoiceRecs = array();
			if (!$invoiceFilterId) {
				$referenceInvoiceRecs = $distributorAccountObj->getRefInvoices($distributorAccountId);	
			}
			
			$selCommonReasonId 	= $dar[22];
			$otherReasonDetails 	= $dar[23];
			if ($selCommonReasonId==0 && $otherReasonDetails!="") $selReasonName = $otherReasonDetails;
			
		?>
      <tr bgcolor="#FFFFFF">
		<td class="listing-item" nowrap style="<?=$rowStyle?>"><?=$selectDate;?></td>
		<? if (!$distributorFilterId) {?>
			<td class="listing-item" nowrap style="<?=$rowStyle?>"><?=$distributorName;?></td>
		<?php }?>
		<? if (!$cityFilterId) {?>
			<td class="listing-item" nowrap style="<?=$rowStyle?>"><?=$selCityName;?></td>
		<?php }?>
		<? if (!$invoiceFilterId) {?>
			<td class="listing-item" nowrap style="<?=$rowStyle?>">
				<?php
					$numCol = 3;
					if (sizeof($referenceInvoiceRecs)>0) {
						$nextRec=	0;						
						$selName = "";
						foreach ($referenceInvoiceRecs as $r) {							
							$selName = $r[1];
							$nextRec++;
							if($nextRec>1) echo ",&nbsp;"; echo $selName;
							if($nextRec%$numCol == 0) echo "<br/>";
						}
					}
				?>
			</td>
			<?php }?>			
			<td class="listing-item" nowrap style="<?=$rowStyle?>">
				<?=$selReasonName;?>
			</td>			
			<td class="listing-item" style="<?=$rowStyle?>" align="right">
				<?=($debitAmt!=0)?$debitAmt:""?>
			</td>
			<td class="listing-item" style="<?=$rowStyle?>" align="right">
				<?=($creditAmt!=0 )?$creditAmt:""?>
			</td>
	</tr>
	  	<?
		if ($i%$numRows==0 && $distributorAccountRecSize!=$numRows) {
			$j++;
		?>
	    </table></td></tr>
		<tr bgcolor="#FFFFFF">
		<td colspan="17" align="center">
		<table width="95%" cellpadding="0" cellspacing="0">
        <tr>
        <td colspan="6" height="20"></td>
        </tr>	
	  <tr>
	    <td colspan="6" valign="bottom" nowrap="nowrap" class="listing-item" style="line-height:8px;" align="right">(Page <?=$j?> of <?=$totalPage?>)</td>
	    </tr>
		<tr><TD colspan="6" height="10"></TD></tr>
		<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
    	</table></td></tr>
	</table>
	</td></tr></table>
	<!-- Setting Page Break start Here-->
	  <div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
	  <table width='85%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
	  <tr>
  	<td>
	<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
		<table width="100%">
		<tr bgcolor=white>
    <td colspan="17" class="printPageHead" align="center" ><font size="3"><?=FIF_COMPANY_NAME?></font></td>
  </tr>  
	</table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
	<table width='95%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>
			Daily Account statement - Cont.
		</td>
		 </tr>
	</table></td>
	    </tr>  
	  <!--<tr bgcolor=white>
	    <td colspan="17" align="center" height="5"></td>
	    </tr>-->	  
		<tr bgcolor="white">
    <td colspan="17" align="center" height="5"></td>
  </tr>
	  <tr bgcolor="White">
	<td colspan="17" align="center" style="padding-left:5px; padding-right:5px;">
	  	  <table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
 	 <tr bgcolor="#f2f2f2" align="center">		
		<th class="printPageHead" style="<?=$rowStyle?>">Date</th>
		<? if (!$distributorFilterId) {?>
		<th class="printPageHead" style="<?=$rowStyle?>">Distributor</th>
		<? }?>
		<? if (!$cityFilterId) {?>
		<th class="printPageHead" style="<?=$rowStyle?>">City</th>
		<? }?>
		<? if (!$invoiceFilterId) {?>
		<th class="printPageHead" style="<?=$rowStyle?>">REF INVOICE</th>
		<? }?>
		<th class="printPageHead" style="<?=$rowStyle?>">Reason</th>		
		<th class="printPageHead" style="<?=$rowStyle?>" nowrap="true">Debit<br>(Rs.)</th>
		<th class="printPageHead" style="<?=$rowStyle?>" nowrap="true">Credit<br/>(Rs.)</th>
        </tr>
   <?php
	#Main Loop ending section 			
	       }		
	}		
			# Find Closing Balance Amt
			$closingBalAmt = $totalDebitAmt-$totalCreditAmt;
			if ($closingBalAmt>0) $closingCreditAmt = $closingBalAmt;
			else $closingDebitAmt = $closingBalAmt;

			if (!$distributorFilterId) $colSpan = 5;
			else $colSpan = 4;
   ?>
	<tr bgcolor="White">
			<TD colspan="<?=$colSpan?>" class="listing-head" style="<?=$rowStyle?>" align="right">Total:</TD>
			<td class="listing-item" style="<?=$rowStyle?>" align="right"><strong><?=($totalDebitAmt>0)?number_format($totalDebitAmt,2,'.',','):"";?></strong></td>
			<td class="listing-item" style="<?=$rowStyle?>" align="right"><strong><?=($totalCreditAmt>0)?number_format($totalCreditAmt,2,'.',','):"";?></strong></td>
		</tr>
	<?php
		if ($filterType=="VE") {
	?>
		<tr bgcolor="White">			
			<TD  colspan="<?=$colSpan?>" class="listing-item" style="<?=$rowStyle?>" align="right" nowrap="true">Closing Balance:</TD>
			<td class="listing-item" style="<?=$rowStyle?>" align="right"><strong><?=($closingDebitAmt!="")?number_format(abs($closingDebitAmt),2,'.',','):"";?></strong></td>
			<td class="listing-item" style="<?=$rowStyle?>" align="right"><strong><?=($closingCreditAmt!="")?number_format(abs($closingCreditAmt),2,'.',','):"";?></strong></td>
		</tr>	
		<tr bgcolor="White">
			<TD colspan="<?=$colSpan?>" class="listing-head" style="<?=$rowStyle?>" align="right">Total:</TD>
			<td class="listing-item" style="<?=$rowStyle?>" align="right"><strong><?=number_format(($totalDebitAmt+abs($closingDebitAmt)),2,'.',',')?></strong></td>
			<td class="listing-item" style="<?=$rowStyle?>" align="right"><strong><?=number_format(($totalCreditAmt+abs($closingCreditAmt)),2,'.',',')?></strong></td>
		</tr>	
	<?php
		}
	?>
    </table></td>
  </tr>
  <? } else {?>
  <tr bgcolor=white> 
    <td colspan="17" align="center" class="fieldName"><span class="err1">
      <?=$msgNoRecords;?>
    </span></td>
  </tr><? }?>  
  <tr bgcolor=white>
    <td colspan="17" align="center">
<table width="95%" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="6" height="20"></td>
        </tr>	
	  <tr>
	    <td colspan="6" valign="top" nowrap="nowrap" class="listing-item" align="right">(Page <?=$totalPage?> of <?=$totalPage?>)</td>
	    </tr>
		<tr><TD colspan="6" height="5"></TD></tr>
		<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
    </table></td>
  </tr>
</table>
</td>
</tr>
</table>
<?php
	} 
	# DAILY ACCOUNT STATEMENT ENDS HERE -----------------------------
?>
</form>	
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body></html>
