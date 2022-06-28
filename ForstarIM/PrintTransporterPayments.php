<?php
require("include/include.php");

	$dateFrom = $g["selectFrom"];
	$dateTill = $g["selectTill"];
	$transporterFilterId = $g["transporterFilter"];
	
	$fromDate = mysqlDateFormat($dateFrom);
	$tillDate = mysqlDateFormat($dateTill);

	if ($dateFrom && $dateTill) {
		$transporterPaymentsRecords = $transporterPaymentsObj->fetchAllRecords($fromDate, $tillDate, $transporterFilterId);
	}
	$displayHead = "";
	if ($transporterFilterId) {		
		$transporterRec	= $transporterMasterObj->find($transporterFilterId);		
		$trptrName	= stripSlash($transporterRec[2]);	
		$displayHead = $trptrName;
	} else $displayHead = "Transporter";
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
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
								<td colspan="2" style="padding-left:5px; padding-right:5px;">
								<table cellpadding="1"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
									<?
									if( sizeof($transporterPaymentsRecords) > 0 )
											{
												$i	=	0;
											?>										
										<tr  bgcolor="#f2f2f2"  align="center">		
											<td nowrap class="listing-head" style="padding-left:10px; padding-right:10px;">Date</td>
											<td nowrap class="listing-head" style="padding-left:10px; padding-right:10px;">Transporter</td>
											<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Cheque No</td>
	<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Issuing Bank</td>	
											<td align="right" class="listing-head" style="padding-left:10px; padding-right:10px;">Amount</td>					
										</tr>
	<?
		$totalAmtPaid = 0;
		foreach($transporterPaymentsRecords as $spr)	{					
			$i++;
			$paymentId	=	$spr[0];
			$selPaymentDate	= dateFormat($spr[4]);
			$chequeNo		=	$spr[2];
			$amountPaid		=	$spr[3];
			$transporterName	=	$spr[6];
			$issuingBankName 	= $spr[7];
			$totalAmtPaid += $amountPaid;
	?>
										
										<tr  bgcolor="WHITE"  >						
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$selPaymentDate?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$transporterName;?></td>
											<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$chequeNo?></td>
	<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$issuingBankName?></td>
											<td class="listing-item"  align="right" style="padding-left:10px; padding-right:10px;"><?=$amountPaid?></td>							
										</tr>
										<?
												}
										?>
	<tr bgcolor="WHITE">
		<TD colspan="4" class="listing-head" align="right">Total:&nbsp;</TD>
		<td class="listing-item" align="right" style="padding-left:10px; padding-right:10px;"><strong><?=number_format($totalAmtPaid,2,'.',',')?></strong></td>		
	</tr>
		
								
										<?
											}
											else
											{
										?>
										
										<tr bgcolor="white">
											<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
										</tr>	
										<?
											}
										?>
									</table>
							  </td>
						  </tr>	
	<tr><TD height="25"></TD></tr>	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	</SCRIPT>
</table>