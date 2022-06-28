<?
	require("include/include.php");

	$categoryFilterId = $g["categoryFilter"];
	$subCategoryFilterId = $g["subCategoryFilter"];
	$displayHead = "";
	$displayTitle = "";
	if ($categoryFilterId) {
		$categoryRec		=	$categoryObj->find($categoryFilterId);		
		$categoryName		=	stripSlash($categoryRec[1]);
		$displayHead		= 	"CATEGORY: $categoryName";
		
		if ($subCategoryFilterId) {
			$subcategoryRec		=	$subcategoryObj->find($subCategoryFilterId);		
			$subcategoryName	=	stripSlash($subcategoryRec[2]);	
			$displayHead .= "&nbsp;,SUB-CATEGORY: $subcategoryName";
		}

		$displayTitle = "(".$displayHead.")";
	}

	# List all Stocks
	$stockGroupRecords	= $stockGroupObj->fetchAllRecords($categoryFilterId, $subCategoryFilterId);
	//$stockGroupRecords	= $stockGroupObj->fetchAllPagingRecords($offset, $limit, $categoryFilterId, $subCategoryFilterId);
	$stockSize		=	sizeof($stockGroupRecords);
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="90%" align="center">
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Stock Group <?=$displayTitle?></td>
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
	<table cellpadding="1"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
		if( sizeof($stockGroupRecords) > 0 )	{
			$i	=	0;
	?>
	<tr  bgcolor="#f2f2f2" align="center">
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Category</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Sub-Category</th>
		<th class="listing-head" style="padding-left:10px; padding-right:10px;">Basic Unit</th>
	</tr>
	
	<?php
	foreach ($stockGroupRecords as $sgr) {
		$i++;
		$stockGroupId 	= $sgr[0];
		$catName	= $sgr[4];
		$subCatName	= $sgr[5];
		$basicUnitName	= $sgr[6];
		$active=$sgr[7];
		$existingrecords=$sgr[8];
	?>
	<tr  bgcolor="WHITE">
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$catName;?></td>
		<td class="listing-item" nowarp align="left" style="padding-left:10px; padding-right:10px;"><?=($subCatName!="")?$subCatName:"ALL"?></td>
		<td class="listing-item" nowarp align="center" style="padding-left:10px; padding-right:10px;"><?=$basicUnitName?></td>	
	</tr>
											<?
											}
											?>
												
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="editId" value="">
										<input type="hidden" name="editSelectionChange" value="0">
											<?
												}
												else
												{
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
