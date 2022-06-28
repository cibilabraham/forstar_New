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
	$stockRecords	= $stockObj->fetchAllFilterRecords($categoryFilterId, $subCategoryFilterId);
	$stockSize		=	sizeof($stockRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Stocks <?=$displayTitle?></td>
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
										if( sizeof($stockRecords) > 0 )	{
										$i	=	0;
										?>
										<tr  bgcolor="#f2f2f2" align="center">
											<td class="listing-head" style="padding-left:5px; padding-right:5px;">Code</td>
											<td class="listing-head" style="padding-left:5px; padding-right:5px;">Name</td>
											<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Opening<br> Quantity </td>
											<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Current<br> Quantity</td>
											<td class="listing-head" style="padding-left:5px; padding-right:5px;">Reorder Point </td>
											<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;">Active</td>
										</tr>
										<?
										foreach($stockRecords as $sr) {
											$i++;
											$stockId			=	$sr[0];
											$stockCode			=	stripSlash($sr[1]);
											$stockName			=	stripSlash($sr[2]);
											//$quantity			=	$sr[3];
											$reOrderPoint		=	$sr[5];
											$activeStatus = $sr[6];
											$displayActiveStaus = "";
											if ($activeStatus=='Y') {
												$displayActiveStaus = "Yes";
											} else {
												$displayActiveStaus = "No";
											}
											$actualQuantity = $sr[9];
											/**************************/
											$displayActualQty = "";
											$displayTitle  = "";
											if ($actualQuantity<$reOrderPoint) {
												$displayActualQty = "<span style=\"color:#FF0000\">".$actualQuantity."</span>";
												$displayTitle = "This stock is below Re-order Point";
											} else {
												$displayActualQty  = $actualQuantity;
												$displayTitle = "";
											}
											$getPlantList= $stockObj->getPlantList($stockId);
										?>
										<tr  bgcolor="WHITE">
											<td class="listing-item" nowrap="nowrap" style="padding-left:5px;	padding-right:5px;"><?=$stockCode;?></td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><?=$stockName;?></td>
											<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;">
												<?php
												foreach($getPlantList as $gP){
												//list($id,$no,$plantName)=$plantandunitObj->find($gP[2]);
												$quantity=$gP[4];
												$qStO=$qStO.$quantity.",";
												?>
												<?//=$quantity?><?php }?><?=trim($qStO,',');?>
											</td>
											<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;">
												<?php
												foreach($getPlantList as $gP){
												//list($id,$no,$plantName)=$plantandunitObj->find($gP[2]);
												$displayActualQty=$gP[3];
												$qStA=$qStA.$displayActualQty.",";
												?>
												<?//=$displayActualQty?><?php }?><?=trim($qStA,',');?>
											</td>
											<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;"><?=$reOrderPoint;?></td>
											<td class="listing-item" nowarp align="right" style="padding-left:5px; padding-right:5px;"><?=$displayActiveStaus?></td>
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
