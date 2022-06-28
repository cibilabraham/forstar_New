<?
	require("include/include.php");

	$dateFrom = $g["selectFrom"];
	$dateTill = $g["selectTill"];
	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);;
	
	#List all Records
	$salesOrderRecords = $salesOrderObj->fetchAllDateRangeRecords($fromDate, $tillDate);
	$salesOrderSize	= sizeof($salesOrderRecords);
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
								<td  colspan="2" background="images/heading_bg.gif" class="pageName" >&nbsp;Sales Order</td>
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
								<td colspan="2" style="padding:10 10 10 10px;">
<table cellpadding="2"  width="80%" cellspacing="1" border="0" align="center" bgcolor="#999999">
	<?php
	if (sizeof($salesOrderRecords)>0) {
		$i = 0;
	?>	
	<tr  bgcolor="#f2f2f2" align="center">		
		<td class="listing-head" align="center" style="padding-left:10px; padding-right:10px;font-size:9px;" nowrap>SO ID</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;font-size:9px;">Distributor</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;font-size:9px;">City</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;font-size:9px;">Total</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;font-size:9px;" nowrap="true">Invoice Type</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;font-size:9px;" nowrap="true">Invoice Date</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;font-size:9px;">Last Date</td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;font-size:9px;">Status</td>		
	</tr>
	<?php
	$totalSalesOrderAmt = 0;
	foreach ($salesOrderRecords as $sor) {
		$i++;
		$salesOrderId	= $sor[0];
		$poId		= $sor[1];
		// Find the Total Amount of Each Sales Order
		//$salesOrderTotalAmt = $salesOrderObj->getSalesOrderAmount($salesOrderId);
		$salesOrderTotalAmt 	= $sor[20];	
		$totalSalesOrderAmt += $salesOrderTotalAmt;
		$distributorName 	= $sor[5];
		$selInvoiceType	= ($sor[15]=='S')?"Sample":"Taxable";


		/*******************************************************/
		$completeStatus	= 	$sor[13];
		$selStatusId	= 	$sor[12];
		$currentDate	=	date("Y-m-d");
		$cDate		=	explode("-",$currentDate);
		$d2 = mktime(22,0,0,$cDate[1],$cDate[2],$cDate[0]);

		$selLastDate	= 	$sor[6]; 	
		$eDate		=	explode("-", $selLastDate);
		$lastDate	=	$eDate[2]."/".$eDate[1]."/".$eDate[0];
		$d1=mktime(0,0,0,$eDate[1],$eDate[2],$eDate[0]);

		$dateDiff = floor(($d2-$d1)/86400);
		$status = "";
		$statusFlag	=	"";
		$extended	=	$sor[7];
		if ($extended=='E' && ($completeStatus=="" || $completeStatus=='P')) {
			//$status	= "<span class='err1'>PENDING (Extended)</span>";
			$status	= "PENDING (Extended)";
			$statusFlag =	'E';
		} else {
			/*
			if ($statusObj->findStatus($selStatusId)) {
				$status	=	$statusObj->findStatus($selStatusId);
			}
			*/
			if ($completeStatus=='C') {
				$status	= " COMPLETED ";
				$statusFlag = 'C';
			} else if ($dateDiff>0) {
				//$status = "<span class='err1'>DELAYED</span>";
				$status = "DELAYED";
				$statusFlag = 'D';
			} else {
				$status = "PENDING";
				$statusFlag = 'P';
			}
		}		
		$currentLogStatus	=	$sor[8];
		$currentLogDate		=	$sor[9];
		$dispatchLastDate	=	$sor[6];
		if ((($statusFlag=='E') || ($statusFlag=='D')) && strlen($currentLogStatus)<=1 ) {
			if ($currentLogStatus=='D' && $statusFlag=='E') {
				$statusFlag = $currentLogStatus.",".$statusFlag;
				$dispatchLastDate = $currentLogDate.",".$dispatchLastDate;	
			}
			// Log Status Update
			$logStatusUpted = $salesOrderObj->updateSalesOrderLogStatus($salesOrderId, $statusFlag, $dispatchLastDate);
		}
		/*******************************************************/
		$disabledField 	= "";
		$settledStatus	= $sor[16];
		$paidStatus	= $sor[17];
		//echo "$paidStatus,$settledStatus,$completeStatus,$reEdit<br/>";
		//if ( ($completeStatus=='C' && !$reEdit) || (($paidStatus=='Y' || $settledStatus=='Y') && $completeStatus=='C' && $reEdit)) {
		if ($completeStatus=='C') {
			$disabledField = "disabled";
		}

		//$areaRec	=	$areaMasterObj->find($sor[18]);
		//$areaName	=	stripSlash($areaRec[2]);

		$soCityId	= $sor[19];
		$cityRec	= $cityMasterObj->find($soCityId);
		$cityName	= stripSlash($cityRec[2]);
		
		$displayColor = "";
		if ($statusFlag=='C') $displayColor = "#90EE90"; // LightGreen
		else if ($statusFlag=='D') $displayColor = "#DD7500"; // LightOrange
		else if ($statusFlag=='E') $displayColor = "Grey";
		else $displayColor = "White";

		$soInvoiceDate  = $sor[3];
	?>
	<tr  bgcolor="WHITE">		
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;font-size:9px;"><?=$poId;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;font-size:9px;"><?=$distributorName;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;font-size:9px;">
			<?=$cityName;?>
		</td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;font-size:9px;" align="right"><?=$salesOrderTotalAmt;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;font-size:9px;" align="center"><?=$selInvoiceType;?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;font-size:9px;" align="right"><?=dateFormat($soInvoiceDate);?></td>
		<td class="listing-item" nowrap style="padding-left:10px; padding-right:10px;font-size:9px;" align="right"><?=$lastDate;?></td>
		<td class="listing-item" align="center" nowrap style="padding-left:10px; padding-right:10px;font-size:9px;" bgcolor="<?=$displayColor?>">
			<?=$status?>
		</td>
	</tr>
	<?php
		} // Loop ends here
	?>
	<tr  bgcolor="WHITE">
		
		<td></td>
		<td></td>
		<td class="listing-head" style="padding-left:10px; padding-right:10px;font-size:9px;" align="right">Total:</td>
		<td class="listing-item" style="padding-left:10px; padding-right:10px;font-size:9px;" align="right"><strong><?=number_format($totalSalesOrderAmt,2,'.',',');?></strong></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<input type="hidden" name="hidRowCount"	id="hidRowCount" value="<?=$i?>" >
	<input type="hidden" name="editId" value="">
	<input type="hidden" name="editSelectionChange" value="0">
	<?
		} else {
	?>
	<tr bgcolor="white">
		<td colspan="7"  class="err1" height="10" align="center"><?=$msgNoRecords;?></td>
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
