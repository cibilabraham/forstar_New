<?
	require("include/include.php");

	$selBatch = $g["selBatch"];
	$selProduct = $g["selProduct"];
	$productMasterRec = $productMasterObj->find($selProduct);
	 $productName = $productMasterRec[2];

	$productBatchRec = $productBatchObj->find($selBatch);
	$batchNo 	 =  $productBatchRec[1];

	if ($selBatch) {
		$productIngredientRecs = $productDetailsObj->fetchAllIngredients($selBatch);
		list($productGmsPerPouch, $fishGmsPerPouch, $pouchPerBatch) = $productDetailsObj->getProductBatchSummaryRec($selBatch);

		#Gms per Pouch
		$gravyGmsPerPouch = $productGmsPerPouch-$fishGmsPerPouch;

		#% per Pouch
		$fishPercentagePerPouch = number_format(($fishGmsPerPouch/$productGmsPerPouch)*100,0);
		$gravyPercentagePerPouch = number_format(($gravyGmsPerPouch/$productGmsPerPouch)*100,0);
		$productPercentagePerPouch = $fishPercentagePerPouch+$gravyPercentagePerPouch;


		#Rs. Per Batch
		$fishRatePerBatch = $productDetailsObj->getFishRatePerBatch($selBatch);
		$gravyRatePerBatch = $productDetailsObj->getGravyRatePerBatch($selBatch);
		$productRatePerBatch = $fishRatePerBatch+$gravyRatePerBatch;

		#Kg (Raw) per Batch
		$fishKgPerbatch  = $productDetailsObj->getfishKgPerbatch($selBatch);
		$gravyKgPerbatch  = $productDetailsObj->getGravyKgPerbatch($selBatch);
		$productKgPerbatch = $fishKgPerbatch+$gravyKgPerbatch;

		#% (Raw) per Batch
		$fishRawPercentagePerPouch = number_format(($fishKgPerbatch/$productKgPerbatch)*100,0);
		$gravyRawPercentagePerPouch = number_format(($gravyKgPerbatch/$productKgPerbatch)*100,0);
		$productRawPercentagePerPouch = $fishRawPercentagePerPouch+$gravyRawPercentagePerPouch;

		#Kg (in Pouch) per Batch
		$fishKgInPouchPerBatch = ceil($fishGmsPerPouch * $pouchPerBatch);
		$gravyKgInPouchPerBatch = ceil($gravyGmsPerPouch * $pouchPerBatch);
		$productKgInPouchPerBatch = $fishKgInPouchPerBatch +$gravyKgInPouchPerBatch;

		#Rs. Per Kg per Batch
		$fishRatePerKgPerbatch = ceil($fishRatePerBatch/$fishKgInPouchPerBatch);
		$gravyRatePerKgPerbatch = ceil($gravyRatePerBatch/$gravyKgInPouchPerBatch);
		$productRatePerKgPerbatch = $fishRatePerKgPerbatch+$gravyRatePerKgPerbatch;

		
		#Rs. Per Pouch
		$fishRatePerPouch = number_format(($fishRatePerKgPerbatch*$fishGmsPerPouch),2);
		$gravyRatePerPouch = number_format(($gravyRatePerKgPerbatch*$gravyGmsPerPouch),2);
		$productRatePerPouch = number_format(($productRatePerBatch/$pouchPerBatch),2);

		#% Yield
		$fishPercentageYield = number_format(($fishKgInPouchPerBatch/$fishKgPerbatch),0);
		$gravyPercentageYield = number_format(($gravyKgInPouchPerBatch/$gravyKgPerbatch),0);
	}
?>
<html>
<head>
<title>PRODUCT DETAILS</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-head" ><font size="3"><?=$companyArr["Name"];?></font> </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item" height="5"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="listing-item"><font size="2">PRODUCT DETAILS </font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="left" height="5"></td>
  </tr>
<?
			  if (sizeof($productIngredientRecs) > 0) {
				$j=0;
?>
<tr><TD>
	<table>
		<TR><TD class="listing-head">Product Name:</TD>
		<td class="listing-item" nowrap><?=$productName?></td>
		<td width="70"></td>
		<TD class="listing-head">Batch No:</TD>
		<td class="listing-item" nowrap><?=$batchNo?></td>
	</TR></table>
</TD></tr>
<tr bgcolor="white"><TD height="10" colspan="17"></TD></tr>
  <tr bgcolor=white>
    <td colspan="17" align="left">
<table bgcolor="#999999" cellspacing="1" border="0">
					<TR bgcolor="#f2f2f2">
						<TD class="listing-head"></TD>
						<TD class="listing-head" style="padding-left:10px; padding-right:10px;">Product</TD>
						<TD class="listing-head" style="padding-left:10px; padding-right:10px;">Fish</TD>
						<TD class="listing-head" style="padding-left:10px; padding-right:10px;">Gravy</TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:5px; padding-right:5px;" bgcolor="lightYellow">Rs. Per Pouch</TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$productRatePerPouch?></TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$fishRatePerPouch?></TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$gravyRatePerPouch?></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:5px; padding-right:5px;" bgcolor="lightYellow">Gms per Pouch</TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right" ><?=$productGmsPerPouch?></TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$fishGmsPerPouch?></TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$gravyGmsPerPouch?></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:5px; padding-right:5px;" bgcolor="lightYellow">% per Pouch</TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$productPercentagePerPouch?>%</TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$fishPercentagePerPouch?>%</TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$gravyPercentagePerPouch?>%</TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:5px; padding-right:5px;" bgcolor="lightYellow">Rs. Per Kg per Batch</TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$productRatePerKgPerbatch?></TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$fishRatePerKgPerbatch?></TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$gravyRatePerKgPerbatch?></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:5px; padding-right:5px;" bgcolor="lightYellow">Pouches per Batch</TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$pouchPerBatch?></TD>
						<TD class="listing-item" colspan="2"></TD>
						<!--<TD class="listing-item"></TD>-->
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:5px; padding-right:5px;" bgcolor="lightYellow">Rs. Per Batch</TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$productRatePerBatch?></TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$fishRatePerBatch?></TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$gravyRatePerBatch?></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:5px; padding-right:5px;" bgcolor="lightYellow">Kg (Raw) per Batch</TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$productKgPerbatch?></TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$fishKgPerbatch?></TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$gravyKgPerbatch?></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:5px; padding-right:5px;" bgcolor="lightYellow">% (Raw) per Batch</TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$productRawPercentagePerPouch?>%</TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$fishRawPercentagePerPouch?>%</TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$gravyRawPercentagePerPouch?>%</TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:5px; padding-right:5px;" bgcolor="lightYellow">Kg (in Pouch) per Batch</TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$productKgInPouchPerBatch?></TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$fishKgInPouchPerBatch?></TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$gravyKgInPouchPerBatch?></TD>
					</TR>
					<TR bgcolor="#FFFFFF">
						<TD class="fieldName" style="line-height:normal; padding-left:5px; padding-right:5px;" bgcolor="lightYellow">% Yield</TD>
						<TD class="listing-item"></TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$fishPercentageYield?>%</TD>
						<TD class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$gravyPercentageYield?>%</TD>
					</TR>
				</table></td>
  </tr>
<tr><TD height="10" colspan="17" bgcolor="white"></TD></tr>
  <tr bgcolor=white>
    <td colspan="17" align="left" height="5">
	<table width="300" cellspacing="1" bgcolor="#999999" cellpadding="3" id="tblAddItem">
			     <tr bgcolor="#f2f2f2" align="center">
                                <td class="listing-head" style="padding-left:5px; padding-right:5px;">Ingredient</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">Type</td>	
                                <td class="listing-head" style="padding-left:5px; padding-right:5px;">Kg(Raw)</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">%/Batch</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">Rs/<br>Batch</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">Gms/<br>Pouch</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">%Wt/<br>Pouch</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">Rs/<br>Pouch</td>
				<td class="listing-head" style="padding-left:5px; padding-right:5px;">%Cost/<br>Pouch</td>
				
                              </tr>
				<?
			 	foreach ($productIngredientRecs as $pir)	{
					$j++;
					$ingredientId	= $pir[2];
					$ingredientName = $pir[5];
					$quantity	= $pir[3];
					$ingType	= $pir[6];
					$displayIngType = "";
					if ($ingType=='S') $displayIngType = "F";
					else $displayIngType = "I";
					
					$ratePerBatch = $pir[7];
					#%/Batch
					$percentagePerBatch = number_format(($quantity/$productKgPerbatch)*100,0);
					$gmsPerPouch = number_format(($quantity/$pouchPerBatch),3);
					$percentageWtPerPouch = number_format(($gmsPerPouch/$productGmsPerPouch)*100,2);
					$ratePerPouch = number_format(($ratePerBatch/$pouchPerBatch),2);
					$percentageCostPerPouch = number_format(($ratePerPouch/$productRatePerPouch)*100,0);
				?>
                                <tr bgcolor="#FFFFFF">
                                	<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="left"><?=$ingredientName?></td>
					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;"><?=$displayIngType?></td>
                                        <td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$quantity?></td>
					<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$percentagePerBatch?>%</td>
					<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$ratePerBatch?></td>
					<td class="listing-item" nowrap style="padding-left:5px; padding-right:5px;" align="right"><?=$gmsPerPouch?></td>
                                        <td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$percentageWtPerPouch?>%</td>
					<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$ratePerPouch?></td>
					<td class="listing-item" style="padding-left:5px; padding-right:5px;" align="right"><?=$percentageCostPerPouch?>%</td>
                                 </tr>
				<?
					 }
				?>
                                </table></td>
  </tr>

<? }?>
</table>
<SCRIPT LANGUAGE="JavaScript">
	<!--
	window.print();
	//-->
	</SCRIPT>
</body></html>