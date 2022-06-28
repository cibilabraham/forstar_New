<?php
require("include/include.php");
$datasList = array();
$id = (int) $_REQUEST['id'];
if($id != '' && $id != 0)
{
	$datasList = $objWeighmentDataSheet->getWeighmentDataSheetForView($id);
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
<table width='80%' border="1" cellspacing='1' cellpadding='1' class="boarder" align='center'>
	<tr>
		<td width="30%"> RM Lot ID</td>
		<td> <?php echo $datasList[1];?></td>
	</tr>
	<tr>
		<td> Sl No </td>
		<td> <?php echo $datasList[2];?></td>
	</tr>
	<tr>
		<td> Date </td>
		<td> <?php echo $datasList[3];?></td>
	</tr>
	<tr>
		<td> Gate Pass </td>
		<td> <?php echo $datasList[4];?></td>
	</tr>
	<tr>
		<td> Farm Name </td>
		<td> <?php echo $datasList[5];?></td>
	</tr>
	<tr>
		<td> Farm Details </td>
		<td> <?php echo $datasList[6];?></td>
	</tr>
	<tr>
		<td> Farmer at Harvest </td>
		<td> <?php echo $datasList[7];?></td>
	</tr>
	<tr>
		<td> Product species </td>
		<td> <?php echo $datasList[8];?></td>
	</tr>
	<tr>
		<td> Purchase supervisor </td>
		<td> <?php echo $datasList[9];?></td>
	</tr>
	<tr>
		<td> Process code </td>
		<td> <?php echo $datasList[10];?></td>
	</tr>
	<tr>
		<td> Grade count </td>
		<td> <?php echo $datasList[11];?></td>
	</tr>
	<tr>
		<td> Count code </td>
		<td> <?php echo $datasList[12];?></td>
	</tr>
	<tr>
		<td> Weight </td>
		<td> <?php echo $datasList[13];?></td>
	</tr>
	<tr>
		<td> Soft % </td>
		<td> <?php echo $datasList[14];?></td>
	</tr>
	<tr>
		<td> Soft weight </td>
		<td> <?php echo $datasList[15];?></td>
	</tr>
	<tr>
		<td> Package type </td>
		<td> <?php echo $datasList[16];?></td>
	</tr>
	<tr>
		<td> Package nos </td>
		<td> <?php echo $datasList[17];?></td>
	</tr>
	<tr>
		<td> Total quantity </td>
		<td> <?php echo $datasList[18];?></td>
	</tr>
	<tr>
		<td> Received at unit </td>
		<td> <?php echo $datasList[19];?></td>
	</tr>
	<tr>
		<td> Receiving supervisor </td>
		<td> <?php echo $datasList[20];?></td>
	</tr>
	<tr>
		<td> Harvesting equipment  </td>
		<td> <?php echo $datasList[21];?></td>
	</tr>
	<tr>
		<td> Issued </td>
		<td> <?php echo $datasList[22];?></td>
	</tr>
	<tr>
		<td> Used </td>
		<td> <?php echo $datasList[23];?></td>
	</tr>
	<tr>
		<td> Returned </td>
		<td> <?php echo $datasList[24];?></td>
	</tr>
	<tr>
		<td> Different </td>
		<td> <?php echo $datasList[25];?></td>
	</tr>
</table>
</form>
<?php
}
?>
</body>
</html>