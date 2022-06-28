<?php
	require("include/include.php");
	$selRateList		= $g["selRateList"];
	$categoryFilterId 	= $g["categoryFilter"];
	#List All Records	
	$ingredientRateResultSetObj	= $ingredientRateMasterObj->ingredientRateRecFilter($selRateList, $categoryFilterId);	
	$ingredientRateRecordSize	= $ingredientRateResultSetObj->getNumRows();
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="85%" align="center">
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Ingredient Rate Master</td>
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
		if ($ingredientRateRecordSize) {
			$i	=	0;
		?>
	<tr  bgcolor="#f2f2f2" align="center">
	<td class="listing-head" style="padding-left:10px; padding-right:10px;">Ingredient</td>
	<td class="listing-head" style="padding-left:10px; padding-right:10px;">Rate/Kg</td>
	<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Yield<br>%</td>
	<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">High</td>
	<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Low</td>
	<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Clean<br> Rate/Kg</td>
	</tr>
	<?
		while(($irr=$ingredientRateResultSetObj->getRow())) {
			$i++;
			$ingredientRateId = $irr[0];
			$ingRatePerKg	=	$irr[2];
			$ingYield	=	$irr[3];
			$ingHighPrice	=	$irr[4];
			$ingLowPrice	=	$irr[5];
			$ingLastPrice	=	$irr[6];
			$ingredientName	=	$irr[8];

			?>
	<tr  bgcolor="WHITE">
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$ingredientName;?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="right"><?=$ingRatePerKg;?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$ingYield?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$ingHighPrice?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$ingLowPrice?></td>
		<td class="listing-item" nowarp align="right" style="padding-left:10px; padding-right:10px;"><?=$ingLastPrice?></td>
	<? }?>
	</tr>
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
