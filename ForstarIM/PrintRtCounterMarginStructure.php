<?php
	require("include/include.php");
	//$selRateList	= $g["selRateList"];
	$rtCounterFilterId = $g["rtCounterFilter"];
	$rtCounterRateListFilterId = $g["rtCounterRateListFilter"];
	#List All Records
	$rtCounterMarginResultSetObj = $rtCounterMarginStructureObj->fetchAllRecords($rtCounterFilterId, $rtCounterRateListFilterId);
	$rtCounterMarginRecordSize	= $rtCounterMarginResultSetObj->getNumRows();
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Retail Counter Margin Structure</td>
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
								<td colspan="2" style="padding-left:10px;padding-right:10px;" >
<table cellpadding="2"  width="75%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if ($rtCounterMarginRecordSize) {
			$i	=	0;
		?>

	<tr  bgcolor="#f2f2f2" align="center">	
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Retail Counter</td>	
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Margin<br>(%)</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Product</td>	
	</tr>
	<?
		$prevRetailCounterId = "";
		$selCriteria ="";
		while (($rcm=$rtCounterMarginResultSetObj->getRow())) {
			$i++;
			$rtCounterMarginId 	= $rcm[0];
			$retailCounterId	= $rcm[1];
			$retailCounterName	= "";
			if ($prevRetailCounterId!=$retailCounterId) {
				$retailCounterName = $rcm[4];
			}
			//$productName	= $rcm[5];
			$marginPercent	= $rcm[5];	
			$rtctRateListId = $rcm[3];
			$selCriteria = "";
			# Get Product Records	
			$getProductRecs = $rtCounterMarginStructureObj->getRtCounterMarginProductRecs($retailCounterId, $marginPercent, $rtctRateListId);
			# Format 
			$selCriteria = "$retailCounterId,$marginPercent,$rtctRateListId,$rtCounterMarginId";	
	?>
	<tr  bgcolor="WHITE">		
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$retailCounterName;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$marginPercent;?></td>	
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;">
						<?
							$numLine = 6;
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
		$prevRetailCounterId = $retailCounterId;
		}
	?>
		<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
		<input type="hidden" name="editId" id="editId" value="<?=$editId?>">
											<?
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
