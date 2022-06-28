<?php
	require("include/include.php");
# select record between selected date

	$dateFrom = $g["selectFrom"];
	$dateTill = $g["selectTill"];

	if ($dateFrom!="" && $dateTill!="") {	
		$fromDate = mysqlDateFormat($dateFrom);	
		$tillDate = mysqlDateFormat($dateTill);
		$containerRecords = $containerObj->fetchAllRecords($fromDate, $tillDate);
		$containerRecordsize	=	sizeof($containerRecords);	
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Container </td>
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
		if( sizeof($containerRecords) > 0 ) {
			$i	=	0;
	?>
	
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Container Id</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Shipping Line </td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Container No </td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Seal No </td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Vessal Details </td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Sailing On </td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Expected Date<br> of Arrival</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Invoice Nos</td>		
	</tr>
	<?php
		foreach($containerRecords as $cr) {
			$i++;
			$containerMainId	=	$cr[0];
			$containerId		=	$cr[1];
			$shippingLine		=	$shippingCompanyMasterObj->getShippingCompanyName($cr[2]);
			$containerNo		=	$cr[3];
			$sealNo			=	$cr[4];
			$vessalDetails		=	$cr[5];			
			$sailingOn	= ($cr[6]!="0000-00-00")?dateFormat($cr[6]):"";
			$expectedDate	= ($cr[7]!="0000-00-00")?dateFormat($cr[7]):"";
			$selInvoiceNos = $containerObj->getSelPORecsEdit($containerMainId);	
			# Invoice Nos
			//$selInvoiceNos = $containerObj->getSelPORecs($containerMainId);

			$containerConfirmed = $cr[9];
			$disabledField = ($containerConfirmed=='Y')?"disabled":"";
	?>
	<tr  bgcolor="WHITE"  >		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$containerId;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$shippingLine?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$containerNo?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$sealNo?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$vessalDetails;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$sailingOn?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$expectedDate;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
		<table>
				<tr>
				<td class="listing-item" ><?=$selInvoiceNos?></td>
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
			</table>
		</td>		
	</tr>
	<?php
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="editContainerEntryId" value="<?=$containerEntryId;?>">
	<input type="hidden" name="editSelectionChange" value="0">
	<input type="hidden" name="editMode" value="<?=$editMode?>">
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
