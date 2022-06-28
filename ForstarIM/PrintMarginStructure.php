<?
	require("include/include.php");

	#List All Margin Structure (Head) Record
	$marginStructureRecords = $marginStructureObj->fetchAllRecords();
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
									<td  colspan="2" background="images/heading_bg.gif" class="pageName">&nbsp;Margin Structure </td>
								</tr>
								
								<tr>
									<td colspan="3" height="5" ></td>
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
										<table cellpadding="1"  width="75%" cellspacing="1" border="0" align="center" bgcolor="#999999">
		<?
		if (sizeof($marginStructureRecords)>0) {
			$i	=	0;
		?>	
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Code </td>
		<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Name </td>
		<td class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Markup/Markdown </td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Description</td>
	</tr>
	<?
	foreach ($marginStructureRecords as $msr) {
		$i++;
		$marginStructureId	= $msr[0];
		$marginStructureName	= stripSlash($msr[1]);
		$mgnStructureDescr	= stripSlash($msr[2]);
		$priceCalcType		= $msr[3];
		$displayPriceCalcType = "";
		if ($priceCalcType=='MU') $displayPriceCalcType = "Markup"; 
		else if ($priceCalcType=='MD') $displayPriceCalcType = "Markdown";
		
		$mgnStructureCode	= $msr[5];
	?>
	<tr  bgcolor="WHITE"  >		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$mgnStructureCode;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$marginStructureName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$displayPriceCalcType;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$mgnStructureDescr;?></td>		
	</tr>
	<?
		}
	?>
	<? } else { ?>
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
