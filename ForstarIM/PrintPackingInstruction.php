<?php
	require("include/include.php");
	$selDistributorFilter = $g["selDistributorFilter"];
	#List All Records
	$packingInstructionRecords 	= $packingInstructionObj->fetchAllRecords();
	$packingInstructionRecordSize	= sizeof($packingInstructionRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Packing Details</td>
							</tr>
							<tr>
								<td colspan="3" height="15" ></td>
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
								<td colspan="2" style="padding:10 10 10 10px">
<table cellpadding="2"  width="70%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?php
		if ($packingInstructionRecordSize) {
			$i	=	0;
		?>
	<tr  bgcolor="#f2f2f2" align="center">
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Distributor</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Invoice No</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap="true">Total <br>Gross Wt <br>(Kg)</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Status</td>
	</tr>
			<?php
			foreach ($packingInstructionRecords as $pir) {
				$i++;
				$pkngInstructionId = $pir[0];
				$sDistributorName    = $pir[6];
				$invType = $pir[2];
				$soNo 	= $pir[3];
				$pfNo 	= $pir[4];
				$saNo	= $pir[5];				
				$invoiceNo = "";
				if ($soNo!=0) $invoiceNo=$soNo;
				else if ($invType=='T') $invoiceNo = "P$pfNo";
				else if ($invType=='S') $invoiceNo = "$saNo";		
			
				$tGrossWt	= $pir[7];
				$pkngInstStatus  = $pir[8];
				$disableRow = "";
				if ($pkngInstStatus=='C') {
					$disableRow = "disabled";
				} 
				$displayPkngInstStatus = ($pkngInstStatus=='C')?"COMPLETE":"PENDING";

				
				
				$soId	= $pir[1];
			?>
 <tr  bgcolor="WHITE">	
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$sDistributorName;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$invoiceNo;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="right"><?=$tGrossWt;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" bgcolor="<?=$displayColor?>"><?=$displayPkngInstStatus;?></td>
</tr>
		<?php
			}
		?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="">
											<?php
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
