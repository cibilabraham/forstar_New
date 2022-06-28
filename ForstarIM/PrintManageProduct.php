<?php
	require("include/include.php");

	if ($g["selProductCategory"]!="") $selProductCategoryId = $g["selProductCategory"];
	
	if ($g["selProductState"]!="") $selProductStateId = $g["selProductState"];
	
	if ($g["selProductGroup"]!="") $selProductGroupId = $g["selProductGroup"];
	
	#List All Records
	$productRecords = $manageProductObj->fetchAllRecords($selProductCategoryId, $selProductStateId, $selProductGroupId);

	// Ex Duty Rate List Id
	$exDutyRateListId = $exciseDutyMasterObj->latestRateList();	
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Manage Product</td>
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
<table cellpadding="2"  width="60%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if ( sizeof($productRecords) > 0) {
		$i	=	0;
	?>
	
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" align="center" style="padding-left:5px; padding-right:5px;">Code</td>
		<td class="listing-head" align="center" style="padding-left:5px; padding-right:5px;">Identified No</td>
		<td class="listing-head" align="center" style="padding-left:5px; padding-right:5px;">Chapter/<br>Subheading</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Name</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Net Wt</td>
		<!--<td class="listing-head" style="padding-left:5px; padding-right:5px;">Opening Qty</td>-->
		<td class="listing-head" style="padding-left:5px; padding-right:5px;">Actual Qty</td>		
	</tr>
	<?php
	foreach ($productRecords as $pr) {
		$i++;
		$productId	=	$pr[0];
		$productCode	=	$pr[1];
		$productName	=	$pr[2];
		$pNetWt		= $pr[3];	
		$openingStock	= $pr[4];
		$actualStk	= $pr[5];
		$pIdentifiedNo	= $pr[8];


		$pCategoryId	= $pr[9];
		$pStateId	= $pr[10];
		$pGroupId	= $pr[11];

		$pExciseCode = "";
		$exDtyPrdRec = $manageProductObj->getExDutyByProductId($productId, $exDutyRateListId);	 
		$pExmptExciseCode = $exDtyPrdRec[1];
		$pExciseCode = $pExmptExciseCode;
		if ($pExmptExciseCode=="") {
			$exDutyRec = $manageProductObj->getExciseDutyMasterRec($pCategoryId, $pStateId, $pGroupId, $exDutyRateListId);
			$pExciseCode = $exDutyRec[7];
		}	
	?>
	<tr  bgcolor="WHITE">
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$productCode;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$pIdentifiedNo;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$pExciseCode;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$productName;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$pNetWt;?></td>
		<!--<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$openingStock;?></td>-->
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=($actualStk!=0)?$actualStk:"";?></td>		
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
		<td colspan="4"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
