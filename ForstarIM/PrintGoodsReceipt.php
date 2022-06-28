<?
	require("include/include.php");

	#List all Purchase Order
	$fromDate	= $_GET["fd"];
	$tillDate	= $_GET["td"];
			
	$goodsReceiptRecords = $goodsreceiptObj->fetchAllDateRangeRecords($fromDate, $tillDate);

	$purchaseOrderSize = sizeof($goodsReceiptRecords);
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
					<td  background="images/heading_bg.gif" class="pageName" >&nbsp;GOODS RECEIPT NOTE</td>
				</tr>			
				<tr>
					<td   bgcolor="white">
						<!-- Form fields start -->
	<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
						
							
	<?
	if ($purchaseOrderSize>0) {
		$i	=	0;
	?>
	
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">GRN No</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">PO ID</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Supplier</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Date</td>
		<? if($edit==true){?>
		<td class="listing-head"></td>
		<? }?>
	</tr>
	<?
	foreach ($goodsReceiptRecords as $grr) {
		$i++;
		$goodsReceiptId		=	$grr[0];
			$poId			= 	$grr[1];
			$purchaseOrderRec	= $purchaseOrderInventoryObj->find($poId);	
			$pOGenerateId		=	$purchaseOrderRec[1];
			$SupplierId		=	$purchaseOrderRec[3];
			$supplierRec		=	$supplierMasterObj->find($SupplierId);
			$supplierName		=	stripSlash($supplierRec[2]);
			$storeEntry		=	$grr[6];	
			$Date			=	explode("-",$grr[8]);
			$createdDate		=	$Date[2]."/".$Date[1]."/".$Date[0];
	?>
	<tr bgcolor="White">
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$storeEntry;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;"><?=$pOGenerateId;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;" align="left"><?=$supplierName;?></td>
		<td class="listing-item" width="60" align="center" nowrap style="padding-left:10px; padding-right:10px;"><?=$createdDate?></td>	</tr>
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
