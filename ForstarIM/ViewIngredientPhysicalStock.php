<?php
require("include/include.php");
$datasList = array();
$ingPhysicalStockId =$_REQUEST['ingPhysicalStockId'];
if($ingPhysicalStockId != '' && $ingPhysicalStockId != 0)
{
	$physicalIngredientRec	=	$ingredientPhysicalStockObj->find($ingPhysicalStockId);	
	$editSupplierIngredientId	= $physicalIngredientRec[0];				
	$editDate			=  $physicalIngredientRec[1];
	$physicalStock = $ingredientPhysicalStockObj->findPhysicalStock($ingPhysicalStockId);
}
?>
<html>
<head>
<title>Ingredient Physical Stock</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
 function printThisPage(printbtn){

	document.getElementById("printButton").style.display="none";
	window.print();
	document.getElementById("printButton").style.display="block";
}
</script>
</head>
<body>
<?php 
if(sizeof($physicalStock) > 0)
{
?>
<form name="frmPrintDailyCatchReportMemo">
	<table width='40%'  border="0" cellspacing='1' cellpadding='1'  align="center"> <!--style='border:1px ridge #ccc;'-->
	<tr>
		<td class="listing-head"  align="center">
			Ingredient Physical Stock
		</td>
	</tr>
	<tr>
		<td >
			&nbsp;
		</td>
	</tr>
		<tr>
			<td bgcolor="#fff" align="left">
				<table cellpadding="5" cellspacing="1" bgcolor="#ccc">
					<tr >
						<td class="listing-head" bgcolor="#e8edff"> Date:- </td>
					
						<td class="listing-item" bgcolor="#ffffff"><?=dateFormat($editDate)?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="center">
				<table cellpadding="5" cellspacing="1" bgcolor="#ccc">
					<tr >
						<td class="listing-head" bgcolor="#e8edff">
						Supplier
						</td>
						<td class="listing-head" bgcolor="#e8edff">
						Ingredient
						</td>
						<td class="listing-head" bgcolor="#e8edff">
						Expected Quantity
						</td>
						<td class="listing-head" bgcolor="#e8edff">
						 Quantity
						</td>
						<td class="listing-head" bgcolor="#e8edff">
						Difference in quantity
						</td>
					</tr>
					<?php 
					foreach($physicalStock  as $phyStck)
					{
						$suppId= $phyStck[1];
						$supplierName=$supplierMasterObj->getSupplierName($suppId);
						$ingredientId= $phyStck[2];
						$ingredientName=$ingredientMasterObj->getIngName($ingredientId);
						$expectedQuantity= $phyStck[3];
						$quantity= $phyStck[4];
						$differenceInQuantity= $phyStck[5];
						//echo $supplierId.','.$suppId.','.$ingId.','.$ingredientId.'<br/>';	
					?>
					<tr>
						<td class="listing-item" bgcolor="#ffffff">
							<?=$supplierName?>
						</td>
						<td  class="listing-item" bgcolor="#ffffff">
							<?=$ingredientName?>
						</td>
						<td  class="listing-item" bgcolor="#ffffff">
							<?=$expectedQuantity?>
						</td>
						<td  class="listing-item" bgcolor="#ffffff">
							<?=$quantity?>
						</td>
						<td  class="listing-item" bgcolor="#ffffff">
							<?=$differenceInQuantity?>
						</td>
					</tr>
					<?
					}
					?>
				</table>
			</td>
		</tr>
	</table>
</form>
<?php
}
?>
</body>
</html>