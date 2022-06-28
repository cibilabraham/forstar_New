<?php	
	require("include/include.php");

	$dateFrom 		= $g["salesFrom"];
	$dateTill 		= $g["salesTo"];
	$selSalesStaffId	= $g["selSalesStaff"];

	$salesStaffRec		= $salesStaffMasterObj->find($selSalesStaffId);
	$salesStaffName		= stripSlash($salesStaffRec[2]);	

	# List all Daily Sales
	if ($dateFrom!="" && $dateTill!="" && $selSalesStaffId!="") {
		$fromDate	= mysqlDateFormat($dateFrom);
		$tillDate	= mysqlDateFormat($dateTill);
		$dailySalesEntryRecords = $dailySalesReportObj->fetchDailySalesEntryRecords($fromDate, $tillDate, $selSalesStaffId);
		$dailySalesEntryRecSize = sizeof($dailySalesEntryRecords);

		# List all Combo Matrix Product
		$productPriceRateListId = $productPriceRateListObj->latestRateList();
		$getMrpProductRecs = $dailySalesEntryObj->fetchMrpProductRecs($productPriceRateListId);
	}

	$userName	= $sessObj->getValue("userName");
	$date		= date("d/m/Y");
?>
<html>
<head>
<title>Stocks Report</title>
<link href="libjs/style.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript">
function printThisPage(printbtn)
{
	document.getElementById("printButton").style.display="none";
	window.print();
	document.getElementById("printButton").style.display="block";
}
</script>
</head>
<body>
<form name="frmPrintDailySalesReport">
<table width="100%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="right"><input name="printButton" type="button" id="printButton" value="Print" class="button" onClick="printThisPage(this);" style="display:block"></td>
</tr>
</table>
<table width='100%' cellspacing='1' cellpadding='1' class="boarder" align='center'>
<tr>
	<th>

<table width="100%" align='center' border="0" cellpadding="0" cellspacing="0" bgcolor="#f2f2f2">
 <tr bgcolor='white'>
	<td height="10"></td>
 </tr>
  <tr bgcolor=white>
    <td colspan="17" class="printPageHead" align="center" ><font size="4"><?=COMPANY_NAME?></font></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="printPageHead" height="5" ></td>
  </tr>
 <!-- <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" ><?=REG_NO?></td>
  </tr>	-->
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" ><?=COMPANY_ADDRESS?></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" class="listing-item" align="center" ><?=COMPANY_PHONE?></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="RIGHT" class="printPageHead" ></td>
  </tr>
  <tr>
	<td align="center" valign="top" width='100%' bgcolor="#FFFFFF">
	<table width='98%' bgcolor="#f2f2f2">
         <tr>
           <td class="printPageHead" nowrap="nowrap" align='left' colspan='3'>DAILY SALES REPORT OF <?=$salesStaffName?> FROM <?=$dateFrom;?> TO <?=$dateTill;?></td>
		   <td class="printPageHead" nowrap="nowrap" align='right'>
		   </td>
		 </tr></table></td>
  </tr>
  
   <tr bgcolor=white> 
    <td colspan="17" align="LEFT" class="printPageHead" > </td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="LEFT" class="listing-item"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" class="printPageHead"></td>
  </tr>
  <tr bgcolor=white>
    <td colspan="17" align="center" height="5"></td>
  </tr>
  <!--tr bgcolor=white> 
    <td colspan="17" align="center" class="printPageHead">SUMMARY OF ITEMS</td>
  </tr-->
  <tr bgcolor=white>
    <td colspan="17" align="center" class="fieldName" style="line-height:10px;">&nbsp;</td>
  </tr>
<?
	if (sizeof($dailySalesEntryRecords)>0) {
?>
  <tr bgcolor=white>
    <td colspan="17" align="center" Style="padding-left:5px;padding-right:5px;" >
	<table width="200" align="center" bgcolor="#999999" cellspacing="1" cellpadding="2" class="print">
	 	<tr align="left" bgcolor="White">
			<td class="listing-head" style="padding-left:2px;padding-right:2px;" bgcolor="#f2f2f2">NO OF VISITS</td>
			<?
				foreach ($dailySalesEntryRecords as $dse) {
					$i++;
					$rtctCounterId = $dse[3];
					$retailCounterRec	= $retailCounterMasterObj->find($rtctCounterId);
					$retailCounterName	= stripSlash($retailCounterRec[2]);
					$visitDate		= dateFormat($dse[4]);
					$selectTime	=	explode("-",$dse[5]);
					$visitTime	= "$selectTime[0]:$selectTime[1]&nbsp;$selectTime[2]";
					$poNumber	= $dse[6];
					$orderValue	= $dse[7];
			?>
			<td class="listing-item" style="padding-left:5px;padding-right:5px;" align="center"><strong><?=$i?></strong></td>
			<?
				}
			?>
	 	</tr>
	 <tr bgcolor="white" align="left">
	  	<td class="listing-head" style="padding-left:2px;padding-right:2px;" bgcolor="#f2f2f2">Date of Visit</td>
		<?
				foreach ($dailySalesEntryRecords as $dse) {
					$i++;
					$rtctCounterId = $dse[3];
					$retailCounterRec	= $retailCounterMasterObj->find($rtctCounterId);
					$retailCounterName	= stripSlash($retailCounterRec[2]);
					$visitDate		= dateFormat($dse[4]);
					$selectTime	=	explode("-",$dse[5]);
					$visitTime	= "$selectTime[0]:$selectTime[1]&nbsp;$selectTime[2]";
					$poNumber	= $dse[6];
					$orderValue	= $dse[7];
			?>
			<td class="listing-item" style="padding-left:5px;padding-right:5px;"><?=$visitDate?></td>
			<?
				}
			?>
	 </tr>
	 <tr bgcolor="white" align="left">
                <td class="listing-head" style="padding-left:2px;padding-right:2px;" bgcolor="#f2f2f2">Time of Visit</td>
		<?
				foreach ($dailySalesEntryRecords as $dse) {
					$i++;
					$rtctCounterId = $dse[3];
					$retailCounterRec	= $retailCounterMasterObj->find($rtctCounterId);
					$retailCounterName	= stripSlash($retailCounterRec[2]);
					$visitDate		= dateFormat($dse[4]);
					$selectTime	=	explode("-",$dse[5]);
					$visitTime	= "$selectTime[0]:$selectTime[1]&nbsp;$selectTime[2]";
					$poNumber	= $dse[6];
					$orderValue	= $dse[7];
			?>
			<td class="listing-item" style="padding-left:5px;padding-right:5px;"><?=$visitTime?></td>
			<?
				}
			?>
	 </tr>
	 <tr bgcolor="white" align="left">
		<td class="listing-head" style="padding-left:2px;padding-right:2px;" bgcolor="#f2f2f2">Outlet Name</td>
		<?
				foreach ($dailySalesEntryRecords as $dse) {
					$i++;
					$rtctCounterId = $dse[3];
					$retailCounterRec	= $retailCounterMasterObj->find($rtctCounterId);
					$retailCounterName	= stripSlash($retailCounterRec[2]);
					$visitDate		= dateFormat($dse[4]);
					$selectTime	=	explode("-",$dse[5]);
					$visitTime	= "$selectTime[0]:$selectTime[1]&nbsp;$selectTime[2]";
					$poNumber	= $dse[6];
					$orderValue	= $dse[7];
			?>
			<td class="listing-item" style="padding-left:5px;padding-right:5px;">
				<strong><?=$retailCounterName?></strong>
			</td>
			<?
				}
			?>
	 </tr>
	 <tr bgcolor="white" align="left">
		<td class="listing-head" style="padding-left:2px;padding-right:2px;" bgcolor="#f2f2f2">Area/Location</td>
		<?
				foreach ($dailySalesEntryRecords as $dse) {
					$i++;
					$rtctCounterId = $dse[3];
					$retailCounterRec	= $retailCounterMasterObj->find($rtctCounterId);
					$retailCounterName	= stripSlash($retailCounterRec[2]);
					$visitDate		= dateFormat($dse[4]);
					$selectTime	=	explode("-",$dse[5]);
					$visitTime	= "$selectTime[0]:$selectTime[1]&nbsp;$selectTime[2]";
					$poNumber	= $dse[6];
					$orderValue	= $dse[7];
					# Get City Name
					$cityName 	= $dailySalesReportObj->getCity($rtctCounterId);
			?>
			<td class="listing-item" style="padding-left:5px;padding-right:5px;"><?=$cityName?></td>
			<?
				}
			?>
	 </tr>
	<tr bgcolor="white" align="left">	
		<td class="listing-head" style="padding-left:2px;padding-right:2px;" bgcolor="#f2f2f2">Tele.No</td>
		<?
				foreach ($dailySalesEntryRecords as $dse) {
					$i++;
					$rtctCounterId = $dse[3];
					$retailCounterRec	= $retailCounterMasterObj->find($rtctCounterId);
					$retailCounterName	= stripSlash($retailCounterRec[2]);
					$telNo			= stripSlash($retailCounterRec[8]);
					$visitDate		= dateFormat($dse[4]);
					$selectTime	=	explode("-",$dse[5]);
					$visitTime	= "$selectTime[0]:$selectTime[1]&nbsp;$selectTime[2]";
					$poNumber	= $dse[6];
					$orderValue	= $dse[7];
			?>
			<td class="listing-item" style="padding-left:5px;padding-right:5px;"><?=$telNo?></td>
			<?
				}
			?>
	</tr>
	<tr bgcolor="white" align="left">
		<td class="listing-head" style="padding-left:2px;padding-right:2px;" bgcolor="#f2f2f2">Category</td>
		<?
				foreach ($dailySalesEntryRecords as $dse) {
					$i++;
					$rtctCounterId = $dse[3];
					$retailCounterRec	= $retailCounterMasterObj->find($rtctCounterId);
					$retailCounterName	= stripSlash($retailCounterRec[2]);
					$selRtCtCateogry	= $retailCounterRec[16];
					$categoryRec	=	$retailCounterCategoryObj->find($selRtCtCateogry);
					$categoryName	=	stripSlash($categoryRec[1]);			
			?>
			<td class="listing-item" style="padding-left:5px;padding-right:5px;">
				<?=$categoryName?>
			</td>
			<?
				}
			?>
	</tr>
	<tr bgcolor="white" align="left">
		<td class="listing-head" style="padding-left:2px;padding-right:2px;" bgcolor="#f2f2f2">Display Charge</td>
		<?
				foreach ($dailySalesEntryRecords as $dse) {
					$i++;
					$rtctCounterId = $dse[3];
					$retailCounterRec	= $retailCounterMasterObj->find($rtctCounterId);
					$retailCounterName	= stripSlash($retailCounterRec[2]);
					$visitDate		= dateFormat($dse[4]);
					$selectTime	=	explode("-",$dse[5]);
					$visitTime	= "$selectTime[0]:$selectTime[1]&nbsp;$selectTime[2]";
					$poNumber	= $dse[6];
					$orderValue	= $dse[7];
					$disCharge = $dailySalesEntryObj->getEligibleDisplayCharge($rtctCounterId);
					$displayCharge = "";
					if ($disCharge!="") $displayCharge = "Rs.$disCharge";
					else $displayCharge = "No";
			?>
			<td class="listing-item" style="padding-left:5px;padding-right:5px;"><?=$displayCharge?></td>
			<?
				}
			?>
	</tr>
	<tr bgcolor="white" align="left">
                <td class="listing-head" style="padding-left:2px;padding-right:2px;" bgcolor="#f2f2f2">Product</td>
		<?
				foreach ($dailySalesEntryRecords as $dse) {
					$i++;
					$rtctCounterId = $dse[3];
					$retailCounterRec	= $retailCounterMasterObj->find($rtctCounterId);
					$retailCounterName	= stripSlash($retailCounterRec[2]);
					$visitDate		= dateFormat($dse[4]);
					$selectTime	=	explode("-",$dse[5]);
					$visitTime	= "$selectTime[0]:$selectTime[1]&nbsp;$selectTime[2]";
					$poNumber	= $dse[6];
					$orderValue	= $dse[7];
			?>
			<td class="listing-item" style="padding-left:5px;padding-right:5px;line-height:normal;">
				<table cellspacing="0" cellpadding="0" bgcolor="White" width="100%" class="noBoarder">
	 				<tr align="center">
					    <td class="listing-head" style="padding-left:2px;padding-right:2px;" >
						Stock
					    </td>
					    <td width="1">|</td>	
					    <td class="listing-head" style="padding-left:2px;padding-right:2px;" align="center">
						Order
					    </td>	
					</tr>
				</table>
			</td>
			<?
				}
			?>		
	</tr>
	<?
		$soldPack 	 = 0;
		$totalOrderValue = 0;
		foreach ($getMrpProductRecs as $pmr) {	
			$comboMatrixRecId 	= $pmr[0];
			$productCode		= $pmr[1];
			$productName		= $pmr[2];
	?>
	<tr bgcolor="white" align="left">
                <td class="listing-head" style="padding-left:2px;padding-right:2px;font-size:11px;" nowrap="true">
			<?=$productName?>
		</td>
		<?
				foreach ($dailySalesEntryRecords as $dse) {
					$i++;
					$rtCtEntryId	= $dse[2];
					$rtctCounterId 	= $dse[3];
					$retailCounterRec	= $retailCounterMasterObj->find($rtctCounterId);
					$retailCounterName	= stripSlash($retailCounterRec[2]);
					$visitDate		= dateFormat($dse[4]);
					$selectTime	=	explode("-",$dse[5]);
					$visitTime	= "$selectTime[0]:$selectTime[1]&nbsp;$selectTime[2]";
					$poNumber	= $dse[6];
					$orderValue	= $dse[7];

					list($numStock, $numOrder) = $dailySalesReportObj->getStockPosition($rtCtEntryId,$comboMatrixRecId);

					$soldPack	+= $numOrder;					
			?>
			<td class="listing-item" style="padding-left:5px;padding-right:5px;line-height:normal;">
				<table align="center"  cellspacing="0" cellpadding="0" width="100%" class="nullBorder">
	 				<tr align="center" bgcolor="white">
					    <td class="listing-item" style="padding-left:2px;padding-right:2px;">
						<?=$numStock?>
					    </td>
					    <td width="1">|</td>	
					    <td class="listing-item" style="padding-left:2px;padding-right:2px;" align="center"><?=$numOrder?></td>	
					</tr>
				</table>
			</td>
			<?
				}
			?>
	</tr>
	<?
		}	
	?>
	<tr bgcolor="white" align="left">
		<td class="listing-head" style="padding-left:2px;padding-right:2px;" bgcolor="#f2f2f2">Schemes</td>
		<?
				foreach ($dailySalesEntryRecords as $dse) {
					$i++;
					$rtctCounterId = $dse[3];
					$retailCounterRec	= $retailCounterMasterObj->find($rtctCounterId);
					$retailCounterName	= stripSlash($retailCounterRec[2]);
					$visitDate		= dateFormat($dse[4]);
					$selectTime	=	explode("-",$dse[5]);
					$visitTime	= "$selectTime[0]:$selectTime[1]&nbsp;$selectTime[2]";
					$poNumber	= $dse[6];
					$orderValue	= $dse[7];
					# Get Scheme Records
					$schemeRecords	= $dailySalesEntryObj->getEligibleSchemes($rtctCounterId); 
			?>
			<td class="listing-item" style="padding-left:5px;padding-right:5px;">
				<?
				if (sizeof($schemeRecords)>0) {
				?>
				<table cellspacing="1" bgcolor="#999999" cellpadding="3" class="fBoarder">
					<tr bgcolor="#f2f2f2" align='center'>
						<td class="listing-head" style='line-height:normal;font-size:11px;' nowrap="true">Scheme</td>
						<td class="listing-head" style='line-height:normal;font-size:11px;' nowrap="true">Valid Till</td>
					</tr>
				<?
				foreach ($schemeRecords as $sr) {
					$schemeId	= $sr[0];
					$schemeName	= $sr[1];
					$tillDate	= $sr[3];
					$sDate		= explode("-", $tillDate);
					$validTill = date("jS M y", mktime(0, 0, 0, $sDate[1], $sDate[2], $sDate[0]));
				?>
				<tr bgcolor="white">
					<td class="listing-item" style='line-height:normal;font-size:11px;'><?=$schemeName?></td>
					<td class="listing-item" noWrap style='line-height:normal;font-size:11px;'><?=$validTill?></td>
				</tr>
				<?
				}
				?>
			</table>
			<?
				} else {
			?>
			<span class='err1'>No Scheme available</span>
			<?
				}
			?>
			</td>
			<?
				}
			?>
	</tr>
	<tr bgcolor="white" align="left">
		<td class="listing-head" style="padding-left:2px;padding-right:2px;" bgcolor="#f2f2f2">PO No.</td>
		<?
				foreach ($dailySalesEntryRecords as $dse) {
					$i++;
					$rtctCounterId = $dse[3];
					$retailCounterRec	= $retailCounterMasterObj->find($rtctCounterId);
					$retailCounterName	= stripSlash($retailCounterRec[2]);
					$visitDate		= dateFormat($dse[4]);
					$selectTime	=	explode("-",$dse[5]);
					$visitTime	= "$selectTime[0]:$selectTime[1]&nbsp;$selectTime[2]";
					$poNumber	= $dse[6];
					$orderValue	= $dse[7];
			?>
			<td class="listing-item" style="padding-left:5px;padding-right:5px;"><?=$poNumber?></td>
			<?
				}
			?>
	</tr>
	<tr bgcolor="white" align="left">
		<td class="listing-head" style="padding-left:2px;padding-right:2px;" bgcolor="#f2f2f2">Order Value</td>
		<?
				$totalOrderValue	= 0;
				foreach ($dailySalesEntryRecords as $dse) {
					$i++;
					$rtctCounterId = $dse[3];
					$retailCounterRec	= $retailCounterMasterObj->find($rtctCounterId);
					$retailCounterName	= stripSlash($retailCounterRec[2]);
					$visitDate		= dateFormat($dse[4]);
					$selectTime	=	explode("-",$dse[5]);
					$visitTime	= "$selectTime[0]:$selectTime[1]&nbsp;$selectTime[2]";
					$poNumber	= $dse[6];
					$orderValue	= $dse[7];
					$totalOrderValue += $orderValue;
			?>
			<td class="listing-item" style="padding-left:5px;padding-right:5px;"><?=$orderValue?></td>
			<?
				}
			?>
         </tr>		
        </table>
</td>
</tr>
<tr bgcolor="White"><TD height="10"></TD></tr>
<tr bgcolor="White">
	<TD colspan="17" align="center">
		<table cellpadding="0" cellspacing="0">
				<TR>
					<TD class="listing-head" align="right" style="padding-left:5px;padding-right:5px;">TOTAL PACKS SOLD:</TD>
					<td class="listing-item"><strong><?=$soldPack?></strong></td>
				</TR>
				<TR>
					<TD class="listing-head" align="right" style="padding-left:5px;padding-right:5px;">TOTAL VALUE OF ORDER COLLECTED:</TD>
					<td class="listing-item"><strong><?=number_format($totalOrderValue,2,'.','');?></strong></td>
				</TR>
			</table>
	</TD>
</tr>
	<?
	} else {
	?>
		<tr bgcolor="white" >
		<td class="err1" colspan="9" nowrap align='center'>No daily sales records found.</td>
	</tr>
	<?
		}
	?>
<tr bgcolor=white>
    <td colspan="17" align="center"><table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="6" height="20"></td>
        </tr>		  
		<tr><TD colspan="6" height="5"></TD></tr>
		<tr><TD colspan="6" style="padding-left:5px; padding-right:5px;" align="right"><? require("template/PrintFooter.php");?></TD></tr>
    </table></td>
  </tr>		

	</table>
	</td></tr></table> 

</form>	
<div style="page-break-after: always">&nbsp;</div><!-- Works in IE & Firefox -->
</body></html>
