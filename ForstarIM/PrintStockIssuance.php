<?
	require("include/include.php");
	#List all Stock Issuance
	$stockIssuanceRecords		=	$stockissuanceObj->fetchAllRecords();
	$stockIssuanceSize		=	sizeof($stockIssuanceRecords);
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="70%" align="center">
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Stock Issuance</td>
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
									<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
									<?
									if( sizeof($stockIssuanceRecords) > 0 )
									{
										$i	=	0;
									?>
										<tr  bgcolor="#f2f2f2" >
											<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Date</td>
											<td class="listing-head" style="padding-left:10px; padding-right:10px;">Department</td>
											<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Item</td>
											<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Company</td>
											<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Unit</td>
											<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Quantity</td>
											<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Allocated Details</td>
										</tr>
										<?
										foreach($stockIssuanceRecords as $sir)
										{
											$i++;
											$stockRequisitionId	=	$sir[0];
											$department		=	$sir[2];
											$item		=	$sir[4];
											$company		=	$sir[6];
											$unit		=	$sir[8];
											$qty		=	$sir[10];
											$createdDate		= dateFormat($sir[11]);
											$itemId		=	$sir[3];
											$allocateDetail=$stockissuanceObj->getAllStockIssuance($stockRequisitionId,$itemId);
											//echo sizeof($allocateDetail);
										?>
										<tr  bgcolor="WHITE">
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$createdDate;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$department;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$item;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$company;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$unit;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$qty;?></td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
											<?if(sizeof($allocateDetail)>0)
											{
											?>
											<table cellspacing="1" cellpadding="2" border="0" bgcolor="#999999" align="center">
												<tr  bgcolor="#f2f2f2">
													<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Supplier</th>
													<th class="listing-head" nowrap style="padding-left:10px; padding-right:10px;">Quantity</th>
												</tr>
												<? 
												foreach($allocateDetail as $allotDetail)
												{
												?>
												<tr bgcolor="WHITE">
													<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$allotDetail[3]?></td>
													<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$allotDetail[5]?></td>
												</tr>
												<?
												} 
												?>
											</table>

											<? 
											} 
											else
											{
											?>
												No allocation
											<?
											}
											?>
											</td>
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
											<td colspan="2"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
