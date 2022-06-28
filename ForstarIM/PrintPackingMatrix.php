<?
	require("include/include.php");

	#List All Records
	$pkgMatrixResultSetObj = $packingMatrixObj->fetchAllRecords();
	$pkgMatrixRecordSize   = sizeof($pkgMatrixResultSetObj);
	/*echo "<pre>";
	print_r($pkgMatrixResultSetObj);
	echo "</prev>"; */
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="100%" align="center">
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Packing Matrix</td>
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
									if ($pkgMatrixRecordSize > 0) 
									{
										$i	=	0;
									?>	
										<tr  bgcolor="#f2f2f2" align="center">
											<td class="listing-head" style="padding-left:10px; padding-right:10px;">Packing Type</td>
											<td class="listing-head" style="padding-left:10px; padding-right:10px;">Inner Packing Cost</td>
											<td class="listing-head" style="padding-left:10px; padding-right:10px;">Outer Packing Cost</td>	
											<td class="listing-head" style="padding-left:10px; padding-right:10px;">Labour Cost Only</td>	
										</tr>
										<?
										foreach ($pkgMatrixResultSetObj as $pmr)
										{
											$i++;
											$pkgMatrixRecId 	= $pmr[0];
											$pmType			= $pmr[1];
											$innerPackingCost 	= $pmr[21];					
											$outerPackingCost 	= $pmr[27];
											$pmLabourCost       = $pmr[28];
										?>
										<tr  bgcolor="WHITE">
											<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$pmType;?></td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;" align="right"><?=$innerPackingCost;?></td>
											<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><?=$outerPackingCost;?></td>
											<td class="listing-item" style="padding-left:10px; padding-right:10px;" align="right"><?=$pmLabourCost;?></td>
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
											<td colspan="3"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
