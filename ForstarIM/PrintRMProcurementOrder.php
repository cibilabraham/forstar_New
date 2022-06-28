<?php
require("include/include.php");
$datasList = array();
$id = (int) $_REQUEST['id'];
if($id != '' && $id != 0)
{
	$datasList = $rmProcurmentOrderObj->find($id);
	$procurmentSupplierRecs = $rmProcurmentOrderObj->fetchAllProcurmentSupplier($id);
	$equipmentData	=	$rmProcurmentOrderObj->fetchAllProcurmentEquipment($id);
	$chemicalData	=	$rmProcurmentOrderObj->fetchAllProcurmentChemical($id);
	$procurmentVehicleAndDriverRecs = $rmProcurmentOrderObj->fetchAllProcurmentVehicleAndDriverName($id);
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
<table width='80%' border="0" cellspacing='2' cellpadding='3'  align='center'>
<tr><td colspan="4"></td></tr>
	<tr>
		<td height="10" colspan="3" align="center" bgcolor="#fff">
			<input type="button" value=" Print " name="btnPrint" class="button" onClick="javascript:window.print();"></td>
	</tr>
</table>
<br/>
<table width='80%' border="0" cellspacing='2' cellpadding='3' class="boarder" align='center'>
	
	<tr>
		<td class="listing-head" width="30%">Date of Entry</td>
		<td  class="listing-item" bgcolor="#fff"> <?php echo dateformat($datasList[3]);?></td>
	</tr>
	<tr>
		<td class="listing-head">Procurment Number</td>
		<td  class="listing-item" bgcolor="#fff"> <?php echo $datasList[2];?></td>
	</tr>
	<tr>
		<td class="listing-head"> Company Name </td>
		<td   class="listing-item" bgcolor="#fff"> <?php echo $datasList[5];?></td>
	</tr>
	<tr>
		<td class="listing-head"> Schedule date</td>
		<td   class="listing-item" bgcolor="#fff"> <?php  echo dateFormat($datasList[4]);?></td>
	</tr>
	

</table>
<br/>
	<?php if(sizeof($procurmentVehicleAndDriverRecs)>0)
	{
	?>
		<table width='80%' border="0" cellspacing='2' cellpadding='3' class="boarder" align='center'>
			<tr bgcolor="#f2f2f2" align="center">
				<td class="listing-head" nowrap>Vehicle Number</td>
				<td class="listing-head">Driver Name</td>
			</tr>	
			<?php
				foreach ($procurmentVehicleAndDriverRecs as $procurementDetail) {
				?>
		
			<tr bgcolor="#fff">													
				<td class="listing-item" nowrap><?php echo $procurementDetail[3];?></td>
				<td class="listing-item"><?php echo $procurementDetail[4];?></td>
			</tr>
			
												
		
			<?php
				}
			?>
		</table>
	
	<?php
	}
	?>
		
	<br/>
	
	<?php if(sizeof($procurmentSupplierRecs)>0)
	{
	?>
		<table width='80%'  border="0" cellspacing='2' cellpadding='3' class="boarder" align='center'>
			<tr bgcolor="#f2f2f2" align="center">
			<td class="listing-head"> Supplier name  </td>
			<td class="listing-head"> Supplier Group </td>
			<td class="listing-head"> Farm Name </td>
			<td class="listing-head"> Farm Location</td>
			<td class="listing-head"> PHT Certificate Available and balance Qty  </td>
			
			<!--<td nowrap="" class="listing-head"> Package Type</td>
			<td nowrap="" class="listing-head"> Package Nos </td>-->
			</tr>
			<?php if (sizeof($procurmentSupplierRecs)>0) {
						foreach ($procurmentSupplierRecs as $sp) {
						$selSupplierId=$sp[1];
							$selPondId=	$sp[2];
							 //$editProcurmentId	=	$sp[1];
							 $supplierName=$rmProcurmentOrderObj->supplierName($selSupplierId);
							 $supplierGroup=$rmProcurmentOrderObj->getSupplierGroupDetails($selSupplierId);
							 //$supplierGroup=$rmProcurmentOrderObj->getSupplierGroupId($editProcurmentId);
							 $supplierGroupNm=$supplierGroup[0][1];
							$pondLocationRecs 			= $rmProcurmentOrderObj->filterPondLocationList($selPondId);
							$pondlocation=$pondLocationRecs[1]; 	
							$pondQuantityRecs 			= $rmProcurmentOrderObj->filterPondQtyList($selPondId);
							if(sizeof($pondQuantityRecs)>0)
							{
								foreach($pondQuantityRecs as $pondQuantity )
								{
									$pondQnty+=$pondQuantity[1];
								}
								$pondvalue="Yes".' ('.$pondQnty.')';
							}
							else
							{
								$pondvalue="No";
							}
						?>
			<tr align="center"  bgcolor="#fff">
			<td class="listing-item"><?php echo $supplierName[0];?></td>
			<td class="listing-item"><?php echo $supplierGroupNm;?></td>
			<td class="listing-item"><?php echo $pondLocationRecs[5];?></td>
			<td class="listing-item"><?php echo $pondlocation;?></td>
			<td class="listing-item"><?php echo $pondvalue;?></td>
			
			</tr>
				<?php
				}
				}
				?>
		
		</table>
		<?php
		}
		?>		
		<br/>
		
		<?php if (sizeof($equipmentData)>0) {
		?>
			<table width='80%' border="0" cellspacing='2' cellpadding='3' class="boarder" align='center'>
				<tr bgcolor="#f2f2f2" align="center">
					<td class="listing-head" nowrap>Equipment name </td>
					<td class="listing-head">Required Quantity</td>
				
				</tr>
				<?php
					foreach ($equipmentData as $ep) {
				?>
		
				<tr bgcolor="#fff">													
					<td class="listing-item" nowrap><?php echo $ep[3];?></td>
					<td class="listing-item"><?php echo $ep[2];?></td>
				</tr>
				<?php
				}
				?>
			</table>
		<?php
		}
		?>
		<br/>
		<?php if (sizeof($chemicalData)>0) 
		{
		?>
			<table width='80%' border="0" cellspacing='2' cellpadding='3' class="boarder" align='center'>
				<tr bgcolor="#f2f2f2" align="center">
					<td class="listing-head" nowrap>Chemical name </td>
					<td class="listing-head">Required Quantity</td>
				</tr>
			<?php
				foreach ($chemicalData as $chm) {
				?>
				<tr bgcolor="#fff">													
					<td class="listing-item" nowrap><?php echo $chm[3];?></td>
					<td class="listing-item"><?php echo $chm[2];?></td>
				</tr>
				<?php
				}
				?>
			</table>
		<?php
		}
		?>
	
		
										
	</table>
	



</form>
<?php
	}
	?>
</body>
</html>