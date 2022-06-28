<?
	require("include/include.php");

	$dateFrom = $g["stockFrom"];
	$dateTill = $g["stockTo"];
	$selStockId = $g["selStock"];

	$details  =$g["details"];
	$summary  = $g["summary"];

	
	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);

	if ($fromDate!="" && $tillDate!="" && ($details!="" || $summary!="")) {
		# List all Stocks
		$stkConsumptionResultSetObj = $stockConsumptionObj->fetchStockConsumptionRecords($fromDate, $tillDate, $selStockId, $details, $summary);

		$stockConsumptionRecords = $stkConsumptionResultSetObj->getNumRows();
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Stock Consumption</td>
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
	if ($stockConsumptionRecords) {
	$i=0;
	?>	<tr>
								<td width="1" ></td>
								<td colspan="2" >
<table width="200" align="center" bgcolor="#999999" cellspacing="1" cellpadding="2">
	
        <tr bgcolor="#f2f2f2" align="center">
		<? if ($details) {?>
		 <td class="listing-head" style="padding-left:10px; padding-right:10px;">Date </td>
		<? }?>
                <td class="listing-head" style="padding-left:10px; padding-right:10px;">Stock Item </td>
		<? if ($selStockId) {?>
		 <td class="listing-head" style="padding-left:10px; padding-right:10px;">Department </td>
		<? }?>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;">Used Qty</td>
        </tr>
	<?
	//foreach ($stockConsumptionRecords as $sr) {
	$prevStockId = "";
	$prevDepartmentId = "";
	while ($sr=$stkConsumptionResultSetObj->getRow()) {
		$i++;
		$stockId	=	$sr[0];
		$stockName = "";
		if ($prevStockId!=$stockId) {
			$stockName	=	stripSlash($sr[1]);
		}

		$departmentId 	= 	$sr[5];
		$departmentName = 	$sr[2];
// 		$departmentName = "";
// 		if ($prevDepartmentId!=$departmentId) {
// 			$departmentName = 	$sr[2];
// 		}

		$displaySelectedDate = "";
		$selectedDate = dateFormat($sr[4]);
		if ($prevSelectDate!=$selectedDate) {
			$displaySelectedDate 	= 	dateFormat($sr[4]);
		}
		
		$usedQty	=	$sr[3];
		$netsum=$netsum+$usedQty;
		
	?>
        <tr bgcolor="#FFFFFF" title="<?=$displayTitle?>">
		<? if ($details) {?>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap><?=$displaySelectedDate?></td>
		<? }?>
               <td class="listing-item" style="padding-left:10px; padding-right:10px;" nowrap><?=$stockName?></td>
		<? if ($selStockId) {?>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$departmentName?></td>
		<? }?>
		<td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$usedQty?></td>
         </tr>

         <?
		$prevStockId = $stockId;
		$prevDepartmentId = $departmentId;
		$prevSelectDate = $selectedDate;
		}
	 
	?>
	<tr bgcolor="#FFFFFF"><td <? if ($details) {
	if ($selStockId) {
		?> colspan="3" <?php } else  {?> colspan="2" <?php } } if (!$details){ if ($selStockId) {
		?> colspan="2" <?php } else  {?> colspan="1" <?php } }?> align="right">Total</td><td class="listing-item" align="center" style="padding-left:10px; padding-right:10px;"><?=$netsum;?></td></tr>
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
