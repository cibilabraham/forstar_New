<?php
	require("include/include.php");
	
	$dateFrom = $g["selectFrom"];
	$dateTill = $g["selectTill"];

	$fromDate = mysqlDateFormat($dateFrom);
	$tillDate = mysqlDateFormat($dateTill);

	$distributorFilterId = $g["distributorFilter"];
	# List all Records
	
	$distributorAccountRecords = $distributorAccountObj->fetchDateRangeRecords($fromDate, $tillDate, $distributorFilterId);
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

	
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="85%" align="center">
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" nowrap="true">&nbsp;<?=$displayHead?> Account From:<?=$dateFrom?> to:<?=$dateTill?></td>
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
								<td colspan="2" style="padding-left:5px;padding-right:5px;">
	<table cellpadding="2"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if (sizeof($distributorAccountRecords)>0) {
		$i	=	0;
	?>	
	<tr  bgcolor="#f2f2f2" align="center">	
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Date</td>
		<? if (!$distributorFilterId) {?>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Distributor</td>
		<? }?>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Particulars</td>		
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">Debit<br>(In Rs.)</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap="true">Credit<br>(In Rs.)</td>		
	</tr>
		<?php
		$totalCreditAmt = 0;
		$totalDebitAmt = 0;
		if ($distributorFilterId && $openingBalanceAmt!=0) {
			if ($postType=="C")  {								
				$totalCreditAmt += abs($openingBalanceAmt);
			} else if ($postType=="D") {		 		
				$totalDebitAmt += abs($openingBalanceAmt);
			}
		?>
		<tr  bgcolor="WHITE">			
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$dateFrom;?></td>			
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" width="170" nowrap="true">
				Opening Balance
			</td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right">
				<?=($postType=='D')?number_format($openingBalanceAmt,2,'.',''):""?>
			</td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right">
				<?=($postType=='C')?number_format($openingBalanceAmt,2,'.',''):""?>
			</td>			
		</tr>
		<?		
			}
		?>
		<?php		
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
		<tr  bgcolor="WHITE">			
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$selectDate;?></td>
			<? if (!$distributorFilterId) {?>
			<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$distributorName;?></td>
			<?php }?>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="left" width="170" nowrap="true">
				<?=$particulars?>
			</td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right">
				<?=($debitAmt!=0)?$debitAmt:""?>
			</td>
			<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right">
				<?=($creditAmt!=0)?$creditAmt:""?>
			</td>			
		</tr>
		<?
			}

			# Find Closing Balance Amt
			$closingBalAmt = $totalDebitAmt-$totalCreditAmt;
			if ($closingBalAmt>0) $closingCreditAmt = $closingBalAmt;
			else $closingDebitAmt = $closingBalAmt;

			if (!$distributorFilterId) $colSpan = 3;
			else $colSpan = 2;
		?>
		<tr bgcolor="White">
			<TD colspan="<?=$colSpan?>" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">Total:</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($totalDebitAmt>0)?number_format($totalDebitAmt,2,'.',','):"";?></strong></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($totalCreditAmt>0)?number_format($totalCreditAmt,2,'.',','):"";?></strong></td>
		</tr>
		<tr bgcolor="White">			
			<TD  colspan="<?=$colSpan?>" class="listing-item" style="padding-left:10px; padding-right:10px;" align="right" nowrap="true">Closing Balance:</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($closingDebitAmt!="")?number_format(abs($closingDebitAmt),2,'.',','):"";?></strong></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=($closingCreditAmt!="")?number_format(abs($closingCreditAmt),2,'.',','):"";?></strong></td>
		</tr>	
		<tr bgcolor="White">
			<TD colspan="<?=$colSpan?>" class="listing-head" style="padding-left:10px; padding-right:10px;" align="right">Total:</TD>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=number_format(($totalDebitAmt+abs($closingDebitAmt)),2,'.',',')?></strong></td>
			<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><strong><?=number_format(($totalCreditAmt+abs($closingCreditAmt)),2,'.',',')?></strong></td>
		</tr>	
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
							<tr>
								<td colspan="3" height="5" ></td>
							</tr>
						
						</table>
					</td>
				</tr>
			</table>
			<!-- Form fields end   -->
		</td>
	</tr>	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	</SCRIPT>
</table>
