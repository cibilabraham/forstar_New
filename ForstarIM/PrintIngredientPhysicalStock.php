<?php
	require("include/include.php");
	#List All Records
	$supplierIngredientRecords	= $ingredientPhysicalStockObj->getAllRecords();
	//printr($supplierIngredientRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Ingredient Physical Stock</td>
							</tr>
							<tr>
								<td colspan="3" height="15" ></td>
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
									<table cellpadding="2"  width="70%" cellspacing="1" border="0" align="center" bgcolor="#999999">
									<?
									if (sizeof($supplierIngredientRecords)>0)
									{
										$i	=	0;
									?>
										<tr align="center" bgcolor="#f2f2f2">
											<th class="listing-head" style="padding-left:10px; padding-right:10px;">Date</th>
											<th class="listing-head" style="padding-left:10px; padding-right:10px;">User</th>
										</tr>
									    <?
										//printr($supplierIngredientRecords);
										$prevSupplierId=	0; $prevIngId=0;
										foreach($supplierIngredientRecords as $ssr) 
										{
											$i++;
											$ingPhysicalStockId= $ssr[0];
											$startDate		= dateFormat($ssr[1]);
											//echo $startDate;
											$active=$ssr[4];
											$userId=$ssr[3];
											$userName= $userObj->getUserName($userId);
										?>
										<tr bgcolor="#ffffff">
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
											<?=$startDate;?>
											</td>
											<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;">
											<?=$userName?>
											</td>
										</tr>
											<?
												//$prevIngId=$ingId;
											}
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
