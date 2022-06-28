<?
	require("include/include.php");

	#List All Records
	$distProductPriceResultSetObj = $distProductPriceObj->fetchAllRecords();
	$distProPriceRecordSize   = $distProductPriceResultSetObj->getNumRows();
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Distributor Wise Product Pricing</td>
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
<table cellpadding="2"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if ($distProPriceRecordSize) {
			$i	=	0;
		?>
	<tr  bgcolor="#f2f2f2" align="center">
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Distributor</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Product</td>	
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">MRP</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Cost to Dist/Stockist</td>	
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">Actual Profit Margin</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">On MRP (%)</td>
	<td class="listing-head" style="padding-left:5px; padding-right:5px;">On Factory Cost (%)</td>
	</tr>
	<?
		while ($dpr=$distProductPriceResultSetObj->getRow()) {
			$i++;
			$distProdPriceRecId	= $dpr[0];
			$distributorName	= $dpr[8];
			$productName		= $dpr[9];
			$productMrp		= $dpr[3];
			$costToDistOrStkist 	= $dpr[4];
			$actualProfitMargin	= $dpr[5];
			$onMrpPercent		= $dpr[6];
			$onFactoryCostPercent	= $dpr[7];
	?>
	<tr  bgcolor="WHITE">
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$distributorName;?></td>	
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$productName;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$productMrp;?></td>	
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$costToDistOrStkist;?></td>
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$actualProfitMargin;?></td>	
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$onMrpPercent;?></td>		
		<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$onFactoryCostPercent;?></td>	
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
