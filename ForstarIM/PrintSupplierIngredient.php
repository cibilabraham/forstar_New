<?php
	require("include/include.php");
	
	$supplierFilterId = $g["supplierFilter"];
#List all Supplier Stock
	$supplierIngredientRecords	=	$supplierIngredientObj->getAllRecords($supplierFilterId);
	$supplierIngredientSize		=	sizeof($supplierIngredientRecords);

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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Ingredient Suppliers</td>
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
								<td colspan="2" style="padding-left:5px; padding-right:5px;" >
<table cellpadding="1"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if (sizeof($supplierIngredientRecords)>0) {
		$i	=	0;
	?>
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:10px;">Supplier</td>
		<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:10px;">Ingredients</td>	
		<td class="listing-head" style="padding-left:5px; padding-right:5px;font-size:10px;">Start Date</td>
	</tr>
	<?
		$prevSupplierId		=	0;
		foreach($supplierIngredientRecords as $ssr)
		{
			$supplierIngredientId	= $ssr[0];
			$supplierId		= $ssr[1];
			$ingId			= $ssr[2];
			$ingName			= $ssr[4];
			$startDate		= dateFormat($ssr[6]);
			$supplierName		= "";
			if ($prevSupplierId!=$supplierId) 
			{
				$supplierName = stripSlash($ssr[3]);				
			}
			//$stockName		= $ssr[4];
			//$getSupplierWiseIngredients = $supplierIngredientObj->getIngreients($supplierId);
			$active=$ssr[5];
	?>
	<tr  bgcolor="WHITE">		
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierName;?></td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
			<?=$ingName?>
			</td>
			<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
			<? if($startDate!='00/00/0000') {?><?=$startDate?> <? } ?>
			</td>			
	</tr>
	<?
		$prevSupplierId=$supplierId;
	}
	?>
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
