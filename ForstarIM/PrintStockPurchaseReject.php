<?
	require("include/include.php");

	$dateFrom = $g["stockFrom"];
	$dateTill = $g["stockTo"];
	$selSupplierId = $g["selSupplier"];

	
	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);

	if ($fromDate!="" && $tillDate!="") {
		# List all Stocks
		$stkPurchaseRejectResultSetObj = $stockPurchaseRejectObj->fetchStkPurchaseRejectRecords($fromDate, $tillDate, $selSupplierId);

		$stockPurchaseRejectRecords = $stkPurchaseRejectResultSetObj->getNumRows();
	}
		
?>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<table width="80%" align="center">
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Stock Purchases & Rejection</td>
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
					<?
	if ($stockPurchaseRejectRecords) {
	$i=0;
	?>	<tr>
								<td width="1" ></td>
								<td colspan="2" >
<table width="200" align="center" bgcolor="#999999" cellspacing="1" cellpadding="2">
	
        <tr bgcolor="#f2f2f2" align="center">
                <td class="listing-head" style="padding-left:10px; padding-right:10px;">Stock Item </td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Purchased Qty</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Rejected Qty</td>
        </tr>
	<?
	//foreach ($stockPurchaseRejectRecords as $sr) {
	$prevStockId = "";
	$prevDepartmentId = "";
	while ($sr=$stkPurchaseRejectResultSetObj->getRow()) {
		$i++;
		$stockId	=	$sr[1];
		$stockName = "";
		if ($prevStockId!=$stockId) {
			$stockName	=	stripSlash($sr[2]);
		}
		$purchasedQty	= $sr[3];
		$rejectedQty    = $sr[4];
	?>
        <tr bgcolor="#FFFFFF">
		
               <td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap><?=$stockName?></td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$purchasedQty?></td>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$rejectedQty?></td>
         </tr>
         <?
		$prevStockId = $stockId;
		$prevDepartmentId = $departmentId;
		$prevSelectDate = $selectedDate;
		}
	 
	?>
	<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>">
        </table>								</td>
							</tr>
							<? } else {?>
							<tr>
								<td colspan="3" height="5" class="err1" align="center"><?=$msgNoRecords;?></td>
							</tr>
							<? }?>
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
