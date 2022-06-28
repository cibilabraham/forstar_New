<?php
	require("include/include.php");
# select record between selected date

	$dateFrom = $g["selectFrom"];
	$dateTill = $g["selectTill"];

	if ($dateFrom!="" && $dateTill!="") {	
		$fromDate = mysqlDateFormat($dateFrom);	
		$tillDate = mysqlDateFormat($dateTill);
		$fetchAllPORecords		= $purchaseorderObj->fetchAllRecords($fromDate, $tillDate);
		$PORecordsize	=	sizeof($fetchAllPORecords);	
	}
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="90%" align="center">
	<tr>
		<Td height="10" ></td>
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Purchase Order </td>
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
								<td colspan="2" style="padding-left:5px; padding-right:5px;" >
									<table cellpadding="1"  width="95%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?php
		if( sizeof($PORecordsize) > 0 ) {
			$i	=	0;
	?>
	
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" align="center" style="padding-left:5px; padding-right:5px;" nowrap>PO No.</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;" nowrap>PO Date</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Customer</td>		
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Total Num MC</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Value in USD</td>				
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Value in INR</td>	
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Entry<br/>Date</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Despatch<br/>Date</td>	
		
	</tr>
	<?php
		foreach($fetchAllPORecords as $por) {
			$i++;
			$poMainId = $por[0];
		$custName 	= $por[6];
		$poNumMC	= $por[1];
		$totalNumPC	+= $poNumMC;

		$poValueUSD	= $por[2];
		$totalValueInUSD += $poValueUSD;

		$poValueINR	= $por[3];
		$totalValueInINR += $poValueINR;

		$poLastDate	= $por[4];
		$soEntryDate  	= $por[5];

		# ----- QEL Gen Starts ---------
		$qelGen		= $por[7];
		$qelConfirmed 	= $por[8];
		$notConfirmedCount = $por[12];
		$invoiceCount = $por[13];
		$poCompletedStatus = $por[9];
		$disableEdit = ($poCompletedStatus=='C' && ($invoiceCount>0 && $notConfirmedCount<=0))?"disabled":"";
		$purchaseOrderNo	= $por[10];
		$purchaseOrderDate	= dateFormat($por[11]);
	?>
	<tr  bgcolor="WHITE"  >		
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left"><?=($purchaseOrderNo!="")?$purchaseOrderNo:"";?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=($purchaseOrderDate!='00/00/0000')?$purchaseOrderDate:"";?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left"><?=$custName?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$poNumMC?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$poValueUSD?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$poValueINR?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=dateFormat($soEntryDate)?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="center"><?=dateFormat($poLastDate)?></td>
		
				<?php
				/*
					$numLine = 3;
					if (sizeof($selInvoiceNos)>0) {
						$nextRec = 0;						
						foreach ($selInvoiceNos as $cR) {
							$j++;
							$invNo = $cR[2];
							$nextRec++;
				?>
				<td class="listing-item" nowrap="true">
					<? if($nextRec>1) echo ",";?><?=$invNo?></td>
					<? if($nextRec%$numLine == 0) { ?>
				</tr>
				<tr>
				<?php 
						}	
					 }
					}
					*/
				?>
				</tr>
				<?php
		}
	?>
			</table>
		</td>		
	</tr>
	

											<?
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="10"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
											</tr>	
											<?
												}
											?>
											</table>
								</td>
							</tr>
							<!--<tr>
								<td colspan="3" height="5" ></td>
							</tr>-->
						
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
