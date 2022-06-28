<?php
	require("include/include.php");

	$categoryFilterId = $g["categoryFilter"];

	# List all Sub Category
	$subCategoryRecords	= $subcategoryObj->fetchAllRecords($categoryFilterId);
	$subCategorySize	= sizeof($subCategoryRecords);

	$displayCategoryName = "";
	if ($categoryFilterId) {
		$categoryRec		=	$categoryObj->find($categoryFilterId);
		$categoryName		=	stripSlash($categoryRec[1]);
		$displayCategoryName    = "($categoryName)";
	}
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="90%" align="center">
	<tr>
		<Td height="50" ></td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="100%"  bgcolor="#D3D3D3">
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
						<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
							<tr>
								<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Sub Category <?=$displayCategoryName?></td>
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
									<table cellpadding="1"  width="95%" cellspacing="1" border="0" align="center" bgcolor="#999999">
			<?
			if (sizeof($subCategoryRecords)>0) {
				$i	=	0;
			?>		
	<tr  bgcolor="#f2f2f2" align="center">
		<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px">Name</td>
		<td class="listing-head" nowrap style="padding-left:5px; padding-right:5px;font-size:11px">Description </td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px">Category</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px">Unit Group</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px">Check Point</td>	
		<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px">Carton</td>	
	</tr>
	<?php
	foreach ($subCategoryRecords as $scr) {
		$i++;
		$subCategoryId		= $scr[0];
		$subCategoryName	= stripSlash($scr[2]);
		$subCategoryDescr	= stripSlash($scr[3]);
		$category		= $scr[4];
		
		$unitGroupName 		= $unitGroupObj->getUnitGroupName($scr[5]); //Find the name
		$chkPoint		= ($scr[6]=='Y')?"YES":"NO";
		$chkCarton		= ($scr[7]=='Y')?"YES":"NO";
	?>
	<tr  bgcolor="WHITE">
		<td class="listing-item" nowrap="nowrap" style="padding-left:5px; padding-right:5px;font-size:11px"><?=$subCategoryName;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;font-size:11px;line-height:normal;"><?=$subCategoryDescr?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;font-size:11px;line-height:normal;"><?=$category;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;font-size:11px"><?=$unitGroupName;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;font-size:11px" align="center"><?=$chkPoint;?></td>
		<td class="listing-item" style="padding-left:5px; padding-right:5px;font-size:11px" align="center"><?=$chkCarton;?></td>		
	</tr>
		<?php
			}
		?>
		<?
			}
			else
			{
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
