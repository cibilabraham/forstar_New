<?php
	require("include/include.php");

	$selRateList	= $g["selRateList"];

	if ($g["selProductCategory"]!="") $selProductCategoryId = $g["selProductCategory"];	
	if ($g["selProductState"]!="") $selProductStateId = $g["selProductState"];	
	if ($g["selProductGroup"]!="") $selProductGroupId = $g["selProductGroup"];

	#List All Records
	$productMRPMasterRecords = $productMRPMasterObj->fetchAllRecords($selRateList, $selProductCategoryId, $selProductStateId, $selProductGroupId);
	
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Product MRP Master</td>
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
								<td colspan="2" style="padding-left:10px; padding-right:10px;">
<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if ( sizeof($productMRPMasterRecords) > 0) {
		$i	=	0;
	?>	
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Product</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Net Wt</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">MRP</td>		
	</tr>
	<?
	foreach ($productMRPMasterRecords as $pmr) {
		$i++;
		$productMRPId	= $pmr[0];		
		$productName	= $pmr[4];
		$netWt		= $pmr[5];
		$pMRP		= $pmr[2];
	?>
	<tr  bgcolor="WHITE">
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$productName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$netWt;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$pMRP;?></td>		
	</tr>
	<?
		}
	?>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
	<?
		} else {
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
