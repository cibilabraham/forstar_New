<?php
require("include/include.php");
	
	$dateFrom = $g["selectFrom"];
	$dateTill = $g["selectTill"];
	$preProcessorFilterId = $g["preProcessorFilter"];
	
	$fromDate = mysqlDateFormat($dateFrom);
	$tillDate = mysqlDateFormat($dateTill);

	if ($dateFrom && $dateTill) {
		$processorsPaymentsRecords = $processorspaymentsObj->fetchAllRecords($fromDate, $tillDate, $preProcessorFilterId);
	}
	$displayHead = "";
	if ($preProcessorFilterId) {		
		$processorRec	=	$preprocessorObj->find($preProcessorFilterId);
		$ppName		=	stripSlash($processorRec[2]);		
		$displayHead = $ppName;
	} else $displayHead = "Processor's";

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
		<table cellpadding="1"  width="75%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
			if( sizeof($processorsPaymentsRecords) > 0 )
			{
				$i	=	0;
		?>
										<tr  bgcolor="#f2f2f2" align="center">			
											<td nowrap class="listing-head" style="padding-left:10px; padding-right:10px;">Date</td>
											<td nowrap class="listing-head" style="padding-left:10px; padding-right:10px;">Processor</td>
											<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Cheque No</td>
	<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Issuing Bank</td>
											<td align="right" class="listing-head" style="padding-left:10px; padding-right:10px;">Amount</td>
											<? if($edit==true){?>
											<td class="listing-head"></td>
											<? }?>
										</tr>
		<?php
		$totalAmtPaid = 0;
		foreach ($processorsPaymentsRecords as $ppr) {						
			$i++;
			$paymentId	= $ppr[0];			
			$selPaymentDate	= dateFormat($ppr[4]);
			$chequeNo	= $ppr[2];
			$amountPaid	= $ppr[3];
			$processorName	= $ppr[6];
			$issuingBankName = $ppr[7];
			$totalAmtPaid += $amountPaid;
		?>
										
										<tr  bgcolor="WHITE">	
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selPaymentDate?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$processorName;?></td>
											<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$chequeNo?></td>
	<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$issuingBankName?></td>	
											<td class="listing-item"  align="right" style="padding-left:10px; padding-right:10px;"><?=$amountPaid?></td>
											<? if($edit==true){?>
											<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;"><input type="submit" value=" Edit " name="cmdEdit" onClick="assignValue(this.form,<?=$paymentId;?>,'editId'); this.form.action='ProcessorsPayments.php';"></td>
											<? }?>
										</tr>			
										<?
												}
										?>
<tr bgcolor="WHITE">
		<TD colspan="4" class="listing-head" align="right">Total:&nbsp;</TD>
		<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><strong><?=number_format($totalAmtPaid,2,'.',',')?></strong></td>		
	</tr>
										
											
										<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
										<input type="hidden" name="editId" value="">
										
								<input type="hidden" name="editSelectionChange" value="0">								
								
										<?
											}
											else
											{
										?>
										
										<tr bgcolor="white">
											<td colspan="7"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
                    