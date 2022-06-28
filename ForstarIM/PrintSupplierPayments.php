<?php
require("include/include.php");
	
	$dateFrom = $g["selectFrom"];
	$dateTill = $g["selectTill"];
	$supplierFilterId = $g["supplierFilter"];
	
	$fromDate = mysqlDateFormat($dateFrom);
	$tillDate = mysqlDateFormat($dateTill);

	$supplierPaymentsRecords = $supplierpaymentsObj->supplierPaymentsRecFilter($fromDate, $tillDate, $supplierFilterId);
	$displayHead = "";
	if ($supplierFilterId) {
		$supplierRec	=	$supplierMasterObj->find($supplierFilterId);
		$supplierName	=	stripSlash($supplierRec[2]);
		$displayHead = $supplierName;
	} else $displayHead = "Supplier";

?>
<html>
<head>
	<TITLE><?=$displayHead?> Payments</TITLE>
	<link href="libjs/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="70%" align="center">
	<tr>
		<Td height="50" ></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" nowrap="true" style="font-size:11px;">&nbsp;<?=$displayHead?> Payments For the period from <?=$dateFrom?> to <?=$dateTill?>
								</td>
							</tr>
							<tr>
								<td colspan="3" height="10" ></td>
							</tr>
							
							<?
								if($errDel!="")
								{
							?>
							<tr>
								<td colspan="3" height="15" align="center" class="err1"><?=$errDel;?></td>
							</tr>
							<?
								}
							?>
							<tr>
								<td width="1" ></td>
								<td colspan="2" style="padding:5 5 5 5;" >
		<table cellpadding="1"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999" class="print">
		<?php
			if (sizeof($supplierPaymentsRecords) > 0) {
				$i	=	0;
		?>		 
	<tr  bgcolor="#f2f2f2"  align="center">		
		<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Date</th>
		<? if (!$supplierFilterId) {?>
		<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Supplier</th>
		<? }?>
		<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Payment<br>Method</th>
		<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Cheque/DD<br> No</th>
		<th nowrap class="listing-head" style="padding-left:5px; padding-right:5px;">Issuing Bank</th>	
		<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Payable At</th>
		<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Payment<br>Type</th>
		<th class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Accounts<br>Entry No</th>	
		<th align="right" class="listing-head" style="padding-left:5px; padding-right:5px;">Amount</th>	
	</tr>
	<?php
		$totalAmtPaid = 0;
		foreach ($supplierPaymentsRecords as $spr) {						
			$i++;
			$paymentId	= $spr[0];			
			$enteredDate	= dateFormat($spr[4]);
			$chequeNo	= $spr[2];
			$amountPaid	= $spr[3];
			$totalAmtPaid	+= $amountPaid;
			$supplierName	= $spr[5];
			$selBankName	= $spr[6];
			$sPayableAt	= $spr[7];
			$spPmtMethod	= ($spr[8]=='CH')?"CHEQUE":"DD";
			$spPmtType	= ($spr[9]=='A')?"ADVANCE":"SETTLEMENT";
			$spACEntryNo	= $spr[10];
	?>
	<tr  bgcolor="WHITE"  >		
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$enteredDate?></td>
		<? if (!$supplierFilterId) {?>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$supplierName;?></td>
		<? }?>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$spPmtMethod;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=$chequeNo;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$selBankName;?></td>				
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$sPayableAt?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="center"><?=$spPmtType?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$spACEntryNo?></td>
		<td class="listing-item"  align="right" style="padding-left:5px; padding-right:5px;"><?=$amountPaid?></td>		
	</tr>
	<?php
		}
		$colSpan = (!$supplierFilterId)?8:7;
	?>
	<tr bgcolor="WHITE">
		<TD colspan="<?=$colSpan?>" class="listing-head" align="right">Total:&nbsp;</TD>
		<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><strong><?=number_format($totalAmtPaid,2,'.',',')?></strong></td>
	</tr>	
										<?
											}
											else
											{
										?>
										
										<tr bgcolor="white">
											<td colspan="9"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
										</tr>	
										<?
											}
										?>
									</table>
							  </td>
						  </tr>	
						
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	</SCRIPT>
</table>
</body>
</html>
                    