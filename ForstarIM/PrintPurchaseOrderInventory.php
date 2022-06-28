<?
	require("include/include.php");

	#List all Purchase Order
	$fromDate	= $_GET["fd"];
	$tillDate	= $_GET["td"];
			
	$purchaseOrderRecords = $purchaseOrderInventoryObj->fetchAllRecords($fromDate, $tillDate);

	$purchaseOrderSize = sizeof($purchaseOrderRecords);
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="95%" align="center">
	<tr>
		<Td height="50" ></td>
	</tr>
	
	<tr>
		<td>
			<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="90%"  bgcolor="#D3D3D3">
					<tr>
					<td  background="images/heading_bg.gif" class="pageName" >&nbsp;Purchase Order </td>
				</tr>
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
	<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
						
							
	<?
	if ( $purchaseOrderSize > 0) {
		$i	=	0;
	?>
	
	<tr  bgcolor="#f2f2f2" align="center">
		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Number</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Total</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Status</td>
		<!--td class="listing-head"></td-->
		<? if($edit==true){?>
		<td class="listing-head"></td>
		<? }?>
	</tr>
	<?
	foreach ($purchaseOrderRecords as $por) {
		$i++;
		$purchaseOrderId	= $por[0];
		$poId			= $por[1];
		$poNumber		= $por[2];				
		$supplierName		= $por[7];		
		
		$total_amount = $purchaseOrderInventoryObj->fetchPurchaseOrderAmount($purchaseOrderId);
		
		$status		=	$por[6];
		if ($status=='C') {
			$displayStatus	=	"Cancelled";
		} else if ($status=='R') {
			$displayStatus	=	"Received";
		} else if ($status=='PC') {
			$displayStatus	=	"Partially<br>Completed";
		} else  { //($status=='P')
			$displayStatus	=	"Pending";
		}
		$disabled = "";
		if ($status=='R') $disabled = "disabled";
	?>
	<tr bgcolor="White">
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$poId;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$supplierName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="right"><?=$total_amount;?></td>
		<td class="listing-item" width="60" align="center" nowrap style="padding-left:10px; padding-right:10px;"><?=$displayStatus?></td>	</tr>
	<? 
		}
	?>
	<?
		} else {
	?>
	<tr bgcolor="white">
		<td colspan="6"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
	</tr>
	<?
		}
	?>
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
