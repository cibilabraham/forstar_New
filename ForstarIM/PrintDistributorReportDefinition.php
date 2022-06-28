<?php
	require("include/include.php");

	$selDistributorFilter = $g["selDistributorFilter"];

	#List All Records
	$distReportDefinitionRecords 	= $distReportDefinitionObj->fetchAllRecords($selDistributorFilter);
	$distReportDefinitionRecordSize	= sizeof($distReportDefinitionRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Distributor Wise Report Definition</td>
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
		if ($distReportDefinitionRecordSize) {
			$i	=	0;
		?>
   <tr  bgcolor="#f2f2f2" align="center">	
	<td class="listing-head" style="padding-left:10px; padding-right:10px;">Distributor</td>
	<td class="listing-head" style="padding-left:10px; padding-right:10px;">Product Margin Based on</td>
	<td class="listing-head" style="padding-left:10px; padding-right:10px;">Discount Splitup</td>	
   </tr>
			<?php
			foreach ($distReportDefinitionRecords as $drd) {
				$i++;
				$distReportDefinitionId = $drd[0];
				$sDistributorName    = $drd[3];
				$rateMarginHead	     = $drd[4];
				# Get Split up recs
				$getDiscountSplitupRecs = $distReportDefinitionObj->getDiscountSplitupRecs($distReportDefinitionId);
			?>
 <tr  bgcolor="WHITE">
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$sDistributorName;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$rateMarginHead;?></td>
	<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
		<table cellpadding="0" cellspacing="0">
				<tr>
				<?php
					$numLine = 3;
					if (sizeof($getDiscountSplitupRecs)>0) {
						$nextRec = 0;
						$selMgnHead = "";
						foreach ($getDiscountSplitupRecs as $dR) {
							$selMgnHead = $dR[0];
							$nextRec++;
				?>
				<td class="listing-item" style="line-height:normal; font-size:9px;">
					<? if($nextRec>1) echo ",";?><?=$selMgnHead?></td>
					<? if($nextRec%$numLine == 0) { ?>
					</tr>
					<tr>
				<?php 
						}	
					 }
				 }
				?>
				</tr>
			</table>
	</td>
	</tr>
		<?php
			}
		?>
											<?php
												}
												else
												{
											?>
											<tr bgcolor="white">
												<td colspan="3"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
