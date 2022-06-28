<?php
require("include/include.php");
$datasList = array();
$id = (int) $_REQUEST['id'];
if($id != '' && $id != 0)
{
	$datasList = $objWeighmentDataSheet->getWeighmentDataSheetForView($id);
	$id=$datasList[0];
	$supplierData	=	$objWeighmentDataSheet->getSupplierDataView($id);
}
?>
<html>
<head>
<title>WEIGHMENT DATA SHEET DETAILS</title>
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
if(sizeof($datasList) > 0)
{
?>
<form name="frmPrintDailyCatchReportMemo">
<!--table width="65%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table-->
<table width='80%' border="0" cellspacing='2' cellpadding='3' class="boarder" align='center'>
	<tr>
		<td width="30%"> RM Lot ID</td>
		<td bgcolor="#fff"> <?php echo $datasList[17];?></td>
	</tr>
	<tr>
		<td> Data Sheet No  </td>
		<td bgcolor="#fff"> <?php echo $datasList[2];?></td>
	</tr>
	<tr>
		<td> Date </td>
		<td bgcolor="#fff"> <?php echo dateFormat($datasList[3]);?></td>
	</tr>
	<tr>
		<td> Receiving supervisor </td>
		<td bgcolor="#fff"> <?php echo $datasList[18];?></td>
	</tr>
	

</table>
	
	<br/>
	<?php if($datasList[12]=="1")
	{
	?>
		<table width='80%'  border="0" cellspacing='2' cellpadding='3' class="boarder" align='center'>
			<tr bgcolor="#f2f2f2" align="center">
			<td class="listing-head"> Supplier </td>
			<td class="listing-head"> Farm Name </td>
			<td class="listing-head"> Species </td>
			<td class="listing-head"> Process Code</td>
			<td class="listing-head"> Count Code </td>
			<td class="listing-head">  	Qty  </td>
			<td class="listing-head"> Soft % </td>
			<td nowrap="" class="listing-head"> Soft Qty </td>
			<!--<td nowrap="" class="listing-head"> Package Type</td>
			<td nowrap="" class="listing-head"> Package Nos </td>-->
			</tr>
			<?php if (sizeof($supplierData)>0) {
						foreach ($supplierData as $cR) {
						?>
			<tr align="center"  bgcolor="#fff">
			<td class="listing-item"><?php echo $cR[14];?></td>
			<td class="listing-item"><?php echo $cR[15];?></td>
			<td class="listing-item"><?php echo $cR[13];?></td>
			<td class="listing-item"><?php echo $cR[17];?></td>
			<td class="listing-item"><?php echo $cR[6];?></td>
			<td class="listing-item"><?php echo $cR[7];?></td>
			<td class="listing-item"><?php echo $cR[8];?></td>
			<td nowrap="" class="listing-item"><?php echo $cR[9];?></td>
			<!--<td class="listing-item"><?php echo $cR[16];?></td>
			<td nowrap="" class="listing-item"><?php echo $cR[11];?></td>-->
			</tr>
				<?php
				}
				}
				?>
								
		</table>
		<br/>
		<?php

		/*	$equipmentData	=	$objWeighmentDataSheet->getEquipmentDataView($id);
		?>
		<table width='80%' border="1" cellspacing='1' cellpadding='1' class="boarder" align='center'>											<tr bgcolor="#f2f2f2" align="center">
			<tr>													
				<td class="listing-head" nowrap>Equipment name </td>
				<td class="listing-head">Issued </td>
				<td class="listing-head">Returned</td>
				<td class="listing-head">Difference</td>
			</tr>
			<?php if (sizeof($equipmentData)>0) {
						foreach ($equipmentData as $ep) {
						?>
			<tr>													
				<td class="listing-head" nowrap><?php echo $ep[6];?></td>
				<td class="listing-head"><?php echo $ep[3];?></td>
				<td class="listing-head"><?php echo $ep[4];?></td>
				<td class="listing-head"><?php echo $ep[5];?></td>
			</tr>
			<?php
				}
				}
				?>
												
		</table>
		<br/>
		<?php $chemicalData	=	$objWeighmentDataSheet->getChemicalDataView($id);
		?>
		<table width='80%' border="1" cellspacing='1' cellpadding='1' class="boarder" align='center'>											<tr bgcolor="#f2f2f2" align="center">
			<tr>													
				<td class="listing-head" nowrap>Chemical name </td>
                     		<td class="listing-head">Issued </td>
							<td class="listing-head">Used</td>
							<td class="listing-head">Returned</td>
							<td class="listing-head">Difference</td>
			</tr>
			<?php if (sizeof($chemicalData)>0) {
						foreach ($chemicalData as $chm) {
						?>
			<tr>													
				<td class="listing-head" nowrap><?php echo $chm[7];?></td>
				<td class="listing-head"><?php echo $chm[3];?></td>
				<td class="listing-head"><?php echo $chm[4];?></td>
				<td class="listing-head"><?php echo $chm[5];?></td>
				<td class="listing-head" nowrap><?php echo $chm[6];?></td>
			</tr>
			<?php
				}
				}
				?>
												
		</table>
	<?php
	}
	else
	{
	?>
	<table width='80%' border="1" cellspacing='1' cellpadding='1' class="boarder" align='center'>
		<tr bgcolor="#f2f2f2" align="center">
		<td class="listing-head"> Supplier </td>
		<td class="listing-head"> Farm Name </td>
		<td class="listing-head"> Species </td>
		<td class="listing-head"> Process code </td>
		<td class="listing-head"> Count Code </td>
		<td class="listing-head">  Weight </td>
		<td class="listing-head"> Soft % </td>
		<td nowrap="" class="listing-head"> Soft Weight </td>
		</tr>
		<?php if (sizeof($supplierData)>0) {
					foreach ($supplierData as $cR) {
					?>
		<tr align="center">
		<td class="listing-head"><?php echo $cR[14];?></td>
		<td class="listing-head"><?php echo $cR[15];?></td>
		<td class="listing-head"><?php echo $cR[13];?></td>
		<td class="listing-head"><?php echo $cR[17];?></td>
		<td class="listing-head"><?php echo $cR[6];?></td>
		<td class="listing-head"><?php echo $cR[7];?></td>
		<td class="listing-head"><?php echo $cR[8];?></td>
		<td nowrap="" class="listing-head"><?php echo $cR[9];?></td>
		</tr>
		<?php
		}
		}*/
		?>
		
										
	</table>
	<?php
	}
	?>



</form>
<?php
}
?>
</body>
</html>