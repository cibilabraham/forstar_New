<?php
	require("include/include.php");
	$err		=	"";
	$errDel		=	"";
	$editMode	=	false;
	$addMode	=	true;

	//$redirectLocation = "?selDate=".$p["selDate"]."&pageNo=".$p["pageNo"];

	/*-----------  Checking Access Control Level  ----------------*/
	$add	= false;
	$edit	= false;
	$del	= false;
	$print	= false;
	$confirm= false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId, $functionId);
	if (!$accesscontrolObj->canAccess()) {
		//echo "ACCESS DENIED";
		header("Location: ErrorPage.php");
		die();
	}
	
	if ($accesscontrolObj->canAdd()) $add=true;
	if ($accesscontrolObj->canEdit()) $edit=true;
	if ($accesscontrolObj->canDel()) $del=true;
	if ($accesscontrolObj->canPrint()) $print=true;
	if ($accesscontrolObj->canConfirm()) $confirm=true;
	if ($accesscontrolObj->canReEdit()) $reEdit=true;	
	/*-----------------------------------------------------------*/

	$selSalesStaffId = $p["selSalesStaff"];	
	
	$dateFrom = $p["salesFrom"];
	$dateTill = $p["salesTo"];
	
	$fromDate	= mysqlDateFormat($dateFrom);
	$tillDate	= mysqlDateFormat($dateTill);
	
	if ($p["cmdSearch"]!="") {
		# List all Daily Sales
		$dailySalesEntryRecords = $dailySalesReportObj->fetchDailySalesEntryRecords($fromDate, $tillDate, $selSalesStaffId);

		$dailySalesEntryRecSize = sizeof($dailySalesEntryRecords);		
	}

	# List all Sales Staff
	//$salesStaffResultSetObj = $salesStaffMasterObj->fetchAllRecords();
	$salesStaffResultSetObj = $salesStaffMasterObj->fetchAllRecordsActiveStaff();

	# List all Combo Matrix Product
	$productPriceRateListId = $productPriceRateListObj->latestRateList();
	$getMrpProductRecs = $dailySalesEntryObj->fetchMrpProductRecs($productPriceRateListId);

	# Include JS
	$ON_LOAD_PRINT_JS	= "libjs/DailySalesReport.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>

	<form name="frmDailySalesReport" action="DailySalesReport.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="70%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?
			if( $editMode || $addMode)
			{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="80%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Daily Sales Report</td>
									<td background="images/heading_bg.gif"  >
									<table cellpadding="0" cellspacing="0" align="right">	
									<tr>
									</tr>
									</table></td>
								</tr>
								<tr>
									<td width="1" ></td>
								  <td colspan="2" >
<table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
                                    <tr>
                                      <td height="10" ></td>
                                    </tr>
                                    <tr>
                                      <? if($print==true){?>
                                      <td colspan="3" align="center">&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="button" name="cmdPrint" class="button" value=" Print " onClick="return printWindow('PrintDailySalesReport.php?salesFrom=<?=$dateFrom?> &salesTo=<?=$dateTill?>&selSalesStaff=<?=$selSalesStaffId?>',700,600);" <? if (!$dailySalesEntryRecSize) echo "disabled";?>>&nbsp;&nbsp; </td>
                                      <?}?>
				<tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
                                    <tr>
                                      <td  height="10" colspan="4"  align="center">
						<table><tr> 
                                    <td class="fieldName"> From:</td>
                                    <td> 
                                       <input type="text" id="salesFrom" name="salesFrom" size="8" value="<?=$dateFrom?>"></td>
					<td class="fieldName">To:</td>
					<td>
                                        <input type="text" id="salesTo" name="salesTo" size="8"  value="<?=$dateTill?>"></td>
					<td class="fieldName" nowrap>*Sales Staff&nbsp;</td>
                                                  <td class="listing-item">
					<select name="selSalesStaff" id="selSalesStaff">			
                                        <option value="">-- Select --</option>
					<?	
					while ($ssr=$salesStaffResultSetObj->getRow()) {
						$salesStaffId	 = $ssr[0];
						$salesStaffCode = stripSlash($ssr[1]);
						$salesStaffName = stripSlash($ssr[2]);
						$selected = "";
						if ($selSalesStaffId==$salesStaffId) $selected = "selected";	
					?>
                            		<option value="<?=$salesStaffId?>" <?=$selected?>><?=$salesStaffName?></option>
					<? }?>
					</select>
					</td>
					<td nowrap>&nbsp;<input type="submit" name="cmdSearch" value=" Search " class="button" onClick="return validateDailySalesReportSearch(document.frmDailySalesReport)"></td>
                                  </tr>

					</table>
					</td>
                                    </tr>
                                    </tr>
                                    <input type="hidden" name="hidSupplierStockId" value="<?=$editSupplierStockId;?>" >
                                    <tr>
                                      <td colspan="3" nowrap>&nbsp;</td>
                                    </tr>
	<?
	if ($dailySalesEntryRecSize) {
	$i=0;
	?>
        <tr>
        <td colspan="4">
	<table width="200" align="center" bgcolor="#999999" cellspacing="1" cellpadding="2">
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
				<table cellspacing="0" cellpadding="0" bgcolor="White" width="100%">
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
				<table align="center"  cellspacing="0" cellpadding="0" width="100%">
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
				<table cellspacing="1" bgcolor="#999999" cellpadding="3">
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
	
	<input type="hidden" name="hidRowCount" id="hidRowCount" value="<?=$i?>">
        </table></td>
	</tr>
	<tr><TD height="10"></TD></tr>
	<tr>
		<TD>
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
	<? } else if ($dateFrom!="" && $dateTill!="") {?>
	<tr>
		<td colspan="3" height="5" class="err1" align="center"><?=$msgNoRecords;?></td>
	</tr>
	<? }?>
				    <tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
                                    <tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
                                    <tr>
                                      <? if($print==true){?>
                                      <td colspan="3" align="center">&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="button" name="cmdPrint" class="button" value=" Print " onClick="return printWindow('PrintDailySalesReport.php?salesFrom=<?=$dateFrom?> &salesTo=<?=$dateTill?>&selSalesStaff=<?=$selSalesStaffId?>',700,600);" <? if (!$dailySalesEntryRecSize) echo "disabled";?>>&nbsp;&nbsp; </td>
                                      <?} ?>
                                    </tr>
                                    <tr>
                                      <td  height="10" ></td>
                                    </tr>
                                  </table></td>
								</td>
							</table>						</td>
					</tr>
				</table>
				<!-- Form fields end   -->			</td>
		</tr>	
		<?
			}
			
			# Listing Category Starts
		?>
		
			<tr>
				<td height="10" align="center" ></td>
			</tr>				
		
		<tr>
			<td height="10"></td>
		</tr>
	</table>
<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "salesFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "salesFrom", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	
	Calendar.setup 
	(	
		{
			inputField  : "salesTo",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "salesTo", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>