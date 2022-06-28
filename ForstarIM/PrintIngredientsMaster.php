<?php
	require("include/include.php");

	$mainCategoryFilterId = $g["mainCategoryFilter"];
	$categoryFilterId = $g["categoryFilter"];
	
	#List All Records
	$ingredientResultSetObj	=$ingredientMasterObj->ingredientRecFilter($categoryFilterId, $mainCategoryFilterId);		
	$ingredientRecordSize	= $ingredientResultSetObj->getNumRows();
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="80%" align="center">
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Ingredients Master</td>
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
								<td colspan="2" style="padding-left:10px;padding-right:10px;">
<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if ($ingredientRecordSize) {
			$i	=	0;
		?>
	<tr  bgcolor="#f2f2f2" align="center">
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Name</td>
		<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Local Name</td>
		<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Category</td>
		<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Sub-Category</td>
		<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Stock</td>	
	</tr>
			<?
			while($ir=$ingredientResultSetObj->getRow()) {
				$i++;
				$ingredientId = $ir[0];
				$ingredientCode	= stripSlash($ir[1]);
				$ingredientName	= stripSlash($ir[2]);
				$surname	= stripSlash($ir[3]);
				$qtyInStock	= ($ir[4]==0)?"":$ir[4];
				$stockInHand	= ($ir[5]==0)?"":$ir[5];
				$categoryName	= $ir[6];
				$mainCategoryName = $ir[7];
			?>
	<tr  bgcolor="WHITE">		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$ingredientName;?></td>
		<td class="listing-item" nowarp style="padding-left:10px; padding-right:10px;" ><?=$surname?></td>
		<td class="listing-item" nowarp style="padding-left:10px; padding-right:10px;" align="left"><?=$mainCategoryName?></td>
		<td class="listing-item" nowarp style="padding-left:10px; padding-right:10px;" align="left"><?=$categoryName?></td>
		<td class="listing-item" nowarp style="padding-left:10px; padding-right:10px;" align="right"><?=$stockInHand?></td>
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
												<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
