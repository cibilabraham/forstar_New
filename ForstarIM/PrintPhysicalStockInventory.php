<?
	require("include/include.php");

	# List all Departments 
	$physicalStockInventoryRecords	=	$physicalStockInventoryObj->fetchAllRecords();
	$physicalStockInventoryrSize		=	sizeof($physicalStockInventoryRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Physical Stock Entry(Inventory)</td>
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
									<table cellpadding="1"  width="90%" cellspacing="1" border="0" align="center" bgcolor="#999999">
									<?
									if (sizeof($physicalStockInventoryRecords) > 0) {
										$i	=	0;
									?>
										<tr  bgcolor="#f2f2f2" align="center">
											<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Date </th>
											<th class="listing-head" style="padding-left:10px; padding-right:10px;">Company</th>
											<th class="listing-head" style="padding-left:10px; padding-right:10px;">Unit</th>
											<th class="listing-head" style="padding-left:10px; padding-right:10px;" nowrap>Stock Detail </th>
										</tr>
										<?
										foreach($physicalStockInventoryRecords as $cr) {
											$i++;
											$physicalStockId		=	$cr[0];
											$stockDate		=dateFormat($cr[3]);
											$company= $cr[4];
											$unit= $cr[5];
											$active=$cr[6];
											$stockQty=$physicalStockInventoryObj->stockQtyDetail($physicalStockId);
		
										?>
										<tr  bgcolor="WHITE">
											<td class="listing-item" style="padding-left:10px; padding-right:10px;"><?=$stockDate;?></td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$company;?></td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;"><?=$unit;?></td>
											<td class="listing-item" nowrap="nowrap" style="padding-left:10px; padding-right:10px;">
												<table cellpadding='1' cellspacing='1' bgcolor="#cccccc">
													<tr bgcolor="#f2f2f2">
														<td class="listing-head">Stock</td>
														<td class="listing-head">Supplier</td>
														<td class="listing-head">Quantity</td>
													</tr>
													<?
													foreach($stockQty as $stk)
													{
														$physicalStckEntryId=$stk[0];
														$stock=$stk[5];
														$supplier=$stk[6];
														$quantity=$stk[4];
													?>
													<tr bgcolor="#ffffff">
														<td class="listing-item"><?=$stock?></td>
														<td class="listing-item"><?=$supplier?></td>
														<td class="listing-item"><?=$quantity?></td>
													</tr>
													<?
													}
													?>
												</table>
											</td>
										</tr>
										<?
											}
										} else {
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
