<?php
	require("include/include.php");

	$selRateList = $g["selRateList"];
	#List All Records
	$packingMaterialCostResultSetObj = $packingMaterialCostObj->fetchAllRecords($selRateList);
	$packingMaterialCostRecordSize	= $packingMaterialCostResultSetObj->getNumRows();
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Packing Material Cost Master</td>
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
								<td colspan="2" >
<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if ($packingMaterialCostRecordSize) {
			$i	=	0;
		?>
		<tr  bgcolor="#f2f2f2" align="center">	
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">Name</td>
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">Code</td>
			<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Cost</td>	
		</tr>
			<?
			while(($pmcr=$packingMaterialCostResultSetObj->getRow())) {
				$i++;
				$packingMaterialCostRecId = $pmcr[0];				
				$materialName		  = $pmcr[4];
				$materialCode		  = $pmcr[5];
				$materialCost		  = $pmcr[3]; 
			?>
		<tr  bgcolor="WHITE">	
			<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$materialName?></td>
			<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$materialCode?></td>
			<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$materialCost?></td>
		</tr>
		<?
			}
		?>
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
