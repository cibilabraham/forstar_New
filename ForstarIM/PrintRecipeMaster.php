<?php
	require("include/include.php");

	if ($g["selProductCategory"]!="") $selProductCategoryId = $g["selProductCategory"];
	
	if ($g["selCategoryId"]!="") $selCategoryId = $g["selCategoryId"];
	
	if ($g["selCusineId"]!="") $selCusineId = $g["selCusineId"];

	#List All Records
	$recipeMasterRecords = $recipeMasterObj->fetchAllRecords($selProductCategoryId, $selCategoryId, $selCusineId);
	$recipeMasterRecordSize    = sizeof($recipeMasterRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Recipe Master</td>
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
	<td colspan="2" style="padding-left:10px;padding-right:10px;padding-bottom:10px;">
<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if ( sizeof($recipeMasterRecords) > 0) {
		$i	=	0;
	?>
	<tr bgcolor="#f2f2f2" align="center">
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Code</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Name</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Net Wt</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Fixed Wt</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Gravy Wt</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Fixed Cost/Kg</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Gravy Cost/Kg</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Product Cost/Kg</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Cost/Unit</td>
	</tr>
	<?php
	foreach ($recipeMasterRecords as $pmr) {
		$i++;
		$productId		= $pmr[0];
		$productCode		= $pmr[1];
		$productName		= $pmr[2];
		$netWt			= $pmr[3];
		$fixedWt 		= $pmr[4]; 
		$gravyWt		= $pmr[5]; 
		$fixedCostPerKg		= $pmr[6]; 
		$gravyCostPerKg		= $pmr[7];
		$rsPerPouch		= $pmr[8];
		$qtyPerPouch		= $pmr[9];
		$costPerProduct		= number_format(($rsPerPouch/$qtyPerPouch),2,'.','');
	?>
	<tr  bgcolor="WHITE">		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$productCode;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$productName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$netWt;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$fixedWt;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$gravyWt;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$fixedCostPerKg;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$gravyCostPerKg;?></td>	
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$costPerProduct;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$rsPerPouch;?></td>	
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
