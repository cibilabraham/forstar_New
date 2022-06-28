<?php
	require("include/include.php");

	$distributorFilterId = $g["distributorFilter"];
	$distributorRateListFilterId = $g["distributorRateListFilter"];
	//$selRateList	= $g["selRateList"];
	#List All Records
	$distWiseProuctWiseMarginResultSetObj = $distMarginStructureObj->fetchAllRecords($distributorFilterId, $distributorRateListFilterId);
	$distMarginRecordSize	= $distWiseProuctWiseMarginResultSetObj->getNumRows();
	
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Distributor Margin Structure</td>
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
								<td colspan="2" style="padding-left:3px; padding-right:3px;">
<table cellpadding="2"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if ($distMarginRecordSize) {
			$i	=	0;
		?>
	<tr  bgcolor="#f2f2f2" align="center">
	
	<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:9px;">Distributor</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:9px;">State</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:9px;">Location</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:9px;">Margin<br>(%)</td>	
	<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:9px;">Product Codes</td>	
	</td>
	<?
		$prevDistributorId 	= "";
		$prevProductId		= "";
		$prevStateId	   	= "";			
		while ($dmr=$distWiseProuctWiseMarginResultSetObj->getRow()) {		
			$i++;
			$distMarginId = $dmr[0];
			$distributorId = $dmr[1];

			$distributorName = "";
			if ($prevDistributorId!=$distributorId) {
				$distributorName = $dmr[4];
			}
			$productId	= $dmr[2];
			$productName	= "";
			if ($prevProductId!=$productId) {
				$productName	= $dmr[5];
			}
			$distMarginStateEntryId = $dmr[6];
			
			$stateId	= $dmr[11];
			$stateName = "";
			if ($prevStateId!=$stateId || $prevDistributorId!=$distributorId) {	
				$stateName	= $dmr[7];
			}
			$avgMargin	= number_format($dmr[8],4,'.','');
			$selDistStateEntryId = $dmr[9];
			
			$disabled = "";
			if ($distributorFilterId=="" || $distributorRateListFilterId=="") $disabled = "disabled"; 

			$cityId		= $dmr[12];
			$cityName     = $dmr[10];
			$distRateListId   = $dmr[3];
			$distFinalMargin	 = $dmr[13];			
			$getProductRecs = $distMarginStructureObj->getDistMarginProductRecs($distributorId, $stateId, $cityId, $distFinalMargin, $distRateListId);			
			
	?>
	<tr  bgcolor="WHITE">		
		<td class="listing-item" style="padding-left:5px; padding-right:5px;font-size:9px; line-height:normal;"><?=$distributorName;?></td>			
		<td class="listing-item" style="padding-left:5px; padding-right:5px;font-size:9px;"><?=$stateName;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;font-size:9px;"><?=$cityName;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;font-size:9px;" align="right"><?=$distFinalMargin;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;font-size:9px;" nowrap="true">
						<?
							$numLine = 7;
							if (sizeof($getProductRecs)>0) {
								$nextRec	=	0;
								$k=0;
								$cityName = "";
								foreach ($getProductRecs as $cR) {		
									$productCode = $cR[1];
									$nextRec++;
									if ($nextRec>1) echo ",&nbsp;";
									echo $productCode;
									if($nextRec%$numLine==0) { 
										echo "<br>";
									}
								}
							}
						?>		
		</td>	
	</tr>
	<?
		$prevDistributorId = $distributorId;
		$prevProductId	   = $productId;
		$prevStateId	   = $stateId;		
		}
	?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" value="<?=$editId?>">
		<!--input type="text" name="editSelItem" id="editSelItem" value="<?=$editSelItem?>"-->
		<input type="hidden" name="editSelectionChange" value="0">
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
