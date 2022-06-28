<?
	require("include/include.php");

	$dateFrom = $g["selectFrom"];
	$dateTill = $g["selectTill"];
	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);;
	//echo "$fromDate-$tillDate";
	$purchaseOrderRecords	=	$ingredientPurchaseorderObj->fetchAllRecords($fromDate, $tillDate);
	$purchaseOrderSize	=	sizeof($purchaseOrderRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Ingredient Purchase Order</td>
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
<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?
	if ( sizeof($purchaseOrderRecords) > 0) {
		$i	=	0;
	?>
	<tr  bgcolor="#f2f2f2" align="center">
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;">Number</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Total</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Status</td>
	</tr>
	<?
	foreach ($purchaseOrderRecords as $por) {
		$i++;
		$purchaseOrderId	=	$por[0];
		$poId			=	$por[1];

		$total_amount = $ingredientPurchaseorderObj->getPurchaseOrderAmount($purchaseOrderId);

		$status		=	$por[5];
		$displayStatus = "";
		if 	($status=='C') $displayStatus = "Cancelled";
		else if ($status=='R') $displayStatus = "Received";
		else if ($status=='P') $displayStatus = "Pending";

		$supplierName	=	$por[6];
	?>
	<tr  bgcolor="WHITE">
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$poId;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$total_amount;?></td>
		<td class="listing-item" align="center" nowrap style="padding-left:10px; padding-right:10px;"><?=$displayStatus?></td>
	</tr>
	<?
		}
	?>

	<?
		} else {
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
