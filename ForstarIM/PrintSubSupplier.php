<?
	require("include/include.php");


	$supplierId=$g["supplierId"];

	#List All Sub Suppliers
	
	$subSupplierRecords		=	$subsupplierObj->filterRecords($supplierId);
	$subSupplierSize		=	sizeof($subSupplierRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >SHIP OWNERS/ SUB SUPPLIERS </td>
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
												if( sizeof($subSupplierRecords) > 0 )
												{
													$i	=	0;
											?>
											<tr  bgcolor="#f2f2f2"  >
												<td class="listing-head" nowrap >&nbsp; Code</td>
												<td width="120" class="listing-head" >&nbsp;&nbsp;Name</td>
												<td class="listing-head" align="center">Main Supplier</td>
											</tr>
											<?

													foreach($subSupplierRecords as $fr)
													{
														$i++;
														$subSupplierId	=	$fr[0];
														$subSupplierName	=	stripSlash($fr[1]);
														$subSupplierCode	=	stripSlash($fr[2]);
														//$mainSupplier	=	stripSlash($fr[3]);
													$supplierRec		=	$supplierMasterObj->find($fr[3]);

													$supplierName		=	stripSlash($supplierRec[2]);
											?>
											<tr  bgcolor="WHITE"  >
												<td class="listing-item" width="116" nowrap >&nbsp;&nbsp;<?=$subSupplierCode;?></td>
												<td class="listing-item" >&nbsp;&nbsp;<?=$subSupplierName;?></td>
										        <td class="listing-item" width="45">&nbsp;&nbsp;<?=$supplierName;?></td>
								            </tr>
											<?
													}
											?>
												
											<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
											<input type="hidden" name="editId" value="">
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
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	</SCRIPT>
</table>











			