<?php
	require("include/include.php");
	
	$dateFrom = $g["selectFrom"];
	$dateTill = $g["selectTill"];

	if ($dateFrom && $dateTill) {
		$distributorAccount = true;
		$fromDate = mysqlDateFormat($dateFrom);
		$tillDate = mysqlDateFormat($dateTill);
	
		$distributorFilterId = $g["distributorFilter"];
		$filterType = $g["filterType"];

		# List all Records
		
		$distributorAccountRecords = $distributorAccountObj->fetchDateRangeRecords($fromDate, $tillDate, $distributorFilterId,'','','',$filterType);
		if ($distributorFilterId) {
			list($openingBalanceAmt, $postType) = $distributorReportObj->getOpeningBalanceAmt($fromDate, $tillDate, $distributorFilterId);	
			//echo "$dateFrom, $dateTill, $distributorFilterId, $openingBalanceAmt, $postType";
		}	
	
		$displayHead = "";
		if ($distributorFilterId) {
			$distributorRec		= $distributorMasterObj->find($distributorFilterId);
			$distriName		= stripSlash($distributorRec[2]);
			$displayHead 		= $distriName;
		} else $displayHead = " Distributor's ";

		$pendingChqMsg = "";
		if ($filterType=="PE") $pendingChqMsg = "(Pending Cheques)" ;
		else if ($filterType=="CHQR") $pendingChqMsg = "(Received Cheques)" ;
	}

	$userName	= $sessObj->getValue("userName");
	$date		= date("d/m/Y");
	
?>
<html>
<head>
<title><?=strtoupper($displayHead)?> ACCOUNT</title>
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
		setTimeout("displayBtn()",2500);
	}
</script>
</head>
<body>
<form name="frmPrintDistributorAccount" id="frmPrintDistributorAccount">
<table width="85%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table>
<?php
	# Distributor Account Report
	if ($distributorAccount!="") {
?>
<table width='85%' cellspacing='1' cellpadding='1' class="boarder" align='center' border="0">
<tr>
	<td>
	<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 <tr bgcolor='white'>
	<td height="5"></td>
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
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF" style="padding-left:5px; padding-right:5px;">
	<table width='95%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='center' colspan='2'>
		<?=$displayHead?> Account From:<?=$dateFrom?> to:<?=$dateTill?> 
		<? if ($pendingChqMsg) {?>
		<br> <?=$pendingChqMsg?>
		<? }?>
	</td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		   </td>
		 </tr></table></td>
  </tr>  
  <tr bgcolor=white>
    <td colspan="17" align="center" height="5">&nbsp;</td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" style="padding-left:5px; padding-right:5px;">
<table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
	<?
	if ($distributorAccountRecords>0) {		
		if (!$distributorFilterId) {
			$particularsWidth = "120px";
			$rowStyle = "padding-left:2px; padding-right:2px; font-size:11px; line-height:normal;";
			$numRows = 20;
		} else {
			$particularsWidth = "100%";
			$rowStyle = "padding-left:5px; padding-right:5px; font-size:11px;";
			$numRows = 24;
		}
	?>
 	 <tr bgcolor="#f2f2f2" align="center">		
		<th class="printPageHead" style="<?=$rowStyle?>">Date</th>
		<? if (!$distributorFilterId) {?>		
		<th class="printPageHead" style="<?=$rowStyle?>">Distributor</th>
		<? }?>		
		<th class="printPageHead" style="<?=$rowStyle?>">Particulars</th>
		<th class="printPageHead" style="<?=$rowStyle?>">AMOUNT DUE<br>(Debit) (Rs.)</th>	
		<th class="printPageHead" style="<?=$rowStyle?>">AMOUNT RECEIVED<br>(Credit) (Rs.)</th>
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
		$distributorAccountRecSize = sizeof($distributorAccountRecords);
		$totalPage = ceil($distributorAccountRecSize/$numRows);
		foreach ($distributorAccountRecords as $dar) {	
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
			
	
		?>
      <tr bgcolor="#FFFFFF">
		<td class="listing-item" nowrap style="<?=$rowStyle?>"><?=$selectDate;?></td>
		<? if (!$distributorFilterId) {?>
		<td class="listing-item" nowrap style="<?=$rowStyle?>"><?=$distributorName;?></td>
		<?php }?>			
		<td class="listing-item" style="<?=$rowStyle?>" align="left" nowrap width="<?=$particularsWidth?>">
			<?=$particulars?>
		</td>
		<td class="listing-item" style="<?=$rowStyle?>" align="right">
			<?=($debitAmt!=0)?$debitAmt:""?>
		</td>	
		<td class="listing-item" style="<?=$rowStyle?>" align="right">
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
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="printPageHead" ></td>
  </tr>	</table></td>
	    </tr>
	  <tr bgcolor=white>
	    <td colspan="17" align="center" class="printPageHead">
	<table width='95%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='2'>
			ACCOUNT REPORT - Cont.
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
	  <tr bgcolor="White">
	<td colspan="17" align="center" style="padding-left:5px; padding-right:5px;">
	  	  <table width="85%" cellpadding="2" cellspacing="0" bgcolor="#CCCCCC"  class="print">
 	 <tr bgcolor="#f2f2f2" align="center">		
		<th class="printPageHead" style="<?=$rowStyle?>">Date</th>
		<? if (!$distributorFilterId) {?>		
		<th class="printPageHead" style="<?=$rowStyle?>">Distributor</th>
		<? }?>		
		<th class="printPageHead" style="<?=$rowStyle?>">Particulars</th>
		<th class="printPageHead" style="<?=$rowStyle?>">AMOUNT DUE<br>(Debit) (Rs.)</th>	
		<th class="printPageHead" style="<?=$rowStyle?>">AMOUNT RECEIVED<br>(Credit) (Rs.)</th>
        </tr>
   <?php
	#Main Loop ending section 			
	       }		
	}		
			# Find Closing Balance Amt
			$closingBalAmt = $totalDebitAmt-$totalCreditAmt;
			if ($closingBalAmt>0) $closingCreditAmt = $closingBalAmt;
			else $closingDebitAmt = $closingBalAmt;

			if (!$distributorFilterId) $colSpan = 3;
			else $colSpan = 2;
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
	# Distributor Account Report Ends Here
?>
</form>	
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body>
</html>