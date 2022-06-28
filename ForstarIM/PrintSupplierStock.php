<?
	require("include/include.php");
	
	$supplierFilter	= $g["supplierFilter"];
#List all Supplier Stock
	$supplierStockRecords	=	$supplierstockObj->fetchAllRecords($supplierFilter);
	$supplierStockSize	=	sizeof($supplierStockRecords);

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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Supplier Stock</td>
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
												if( sizeof($supplierStockRecords) > 0 )
												{
													$i	=	0;
											?>
		<tr  bgcolor="#f2f2f2" align="center">
			<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</td>
												<td class="listing-head" style="padding-left:10px; padding-right:10px;">Stock</td>

											</tr>
											<?
													$prevSupplierId		=	0;
																										
													foreach($supplierStockRecords as $ssr)
													{
														$i++;
														$supplierStockId	=	$ssr[0];
														$supplierId			=	$ssr[1];
														$supplierName	=	"";
														if($prevSupplierId!=$supplierId)
														{
											   				$supplierRec		=	$supplierMasterObj->find($ssr[1]);
											   				$supplierName		=	stripSlash($supplierRec[2]);
											   			}
													$stockRec			=	$stockObj->find($ssr[2]);
				                                	$stockName			=	stripSlash($stockRec[2]);
														
														/*$stockItemRecords	=	$supplierstockObj->fetchSupplierStocks($supplierId);
														$j=0;
														$displayStock = "";
												foreach($stockItemRecords as $sir)
													{
													$j++;
											   	  $stockId			=	$sir[2];
																																				
													$stockRec			=	$stockObj->find($sir[2]);
				                                	$stockName			=	stripSlash($stockRec[2]);
												
													if( $j>1) $displayStock.=", &nbsp;";
													
													$displayStock.="$stockName";
																									
												}	*/																													
			
											?>
											<tr  bgcolor="WHITE">
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierName;?></td>
												<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$stockName;?></td>
											</tr>
									<?
										$prevSupplierId=$supplierId;
												
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
