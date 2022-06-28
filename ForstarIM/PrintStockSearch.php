<?
	require("include/include.php");

	$selSupplierId		=	$g["selSupplier"];
	
	if($g["selSupplier"]!=""){
		$SupplierStockRecs		=	$stocksearchObj->fetchSupplierStockRecords($selSupplierId);
	}
	
	$supplierRec		=	$supplierMasterObj->find($selSupplierId);
	$supplierName		=	stripSlash($supplierRec[2]);
		
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Stocks of M/s <?=$supplierName?></td>
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
							<?
	
								if( sizeof($SupplierStockRecs)){
								
						?>
							<tr>
								<td width="1" ></td>
								<td colspan="2" >
									<table width="200" align="center" bgcolor="#999999" cellspacing="1" cellpadding="2">
									  
                                        <tr bgcolor="#f2f2f2">
                                          <td class="listing-head" nowrap="nowrap">&nbsp;&nbsp;Stock Item </td>
                                          <td class="listing-head" nowrap="nowrap">&nbsp;&nbsp;Quoted Price </td>
                                          <td class="listing-head" nowrap="nowrap">&nbsp;&nbsp;Negotiated Price </td>
                                        </tr>
										<?
										foreach($SupplierStockRecs as $ssr){
										
										$quotedPrice	=	$ssr[3];
										$negoPrice		=	$ssr[4];
										$stock			=	$ssr[5];
										
										
										?>
                                        <tr bgcolor="#FFFFFF">
                                          <td class="listing-item">&nbsp;&nbsp;<?=$stock?></td>
                                          <td class="listing-item" align="right"><?=$quotedPrice?>&nbsp;&nbsp;</td>
                                          <td class="listing-item" align="right"><?=$negoPrice?>&nbsp;&nbsp;</td>
                                        </tr>
										<?
										 } 
										
										?>
                                        
                                        
                                </table>								</td>
							</tr>
							<? } else {?>
							<tr>
								<td colspan="3" height="5" class="err1" align="center"><?=$msgNoRecords;?></td>
							</tr>
							<? }?>
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
