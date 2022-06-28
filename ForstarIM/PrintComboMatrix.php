<?
	require("include/include.php");

	#List All Records
	$comboMatrixResultSetObj = $comboMatrixObj->fetchAllRecords();
	$comboMatrixRecordSize   = $comboMatrixResultSetObj->getNumRows();
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Combo Matrix</td>
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
		if ($comboMatrixRecordSize) {
			$i	=	0;
		?>	
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Code</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Name</td>		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Basic Man Fact Cost</td>	
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Profit Margin</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Ideal Fact Cost</td>
	</tr>
	<?
		$basicManufactCost ="";
		while (($pmr=$comboMatrixResultSetObj->getRow())) {
			$i++;
			$comboMatrixRecId 	= $pmr[0];
			$productCode		= $pmr[1];
			$productName		= $pmr[2];
			$basicManufactCost	= $pmr[17];
			$comboProdProfitMargin  = $pmr[11];
			$comboIdealFactCost	= $pmr[8];
	?>
	<tr  bgcolor="WHITE">		
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$productCode?></td>
		<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$productName?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><?=$basicManufactCost?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><?=$comboProdProfitMargin?></td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><?=$comboIdealFactCost?></td>
	</tr>
		<?
			}
		?>	
	<?
	} else {
	?>
	<tr bgcolor="white">
		<td colspan="5"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
