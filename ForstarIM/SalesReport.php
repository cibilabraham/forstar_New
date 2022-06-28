<?php
	require("include/include.php");
	require_once("lib/SalesReport_ajax.php");

	$err		= "";
	$errDel		= "";
	$editMode	= false;
	$addMode	= true;
	$searchMode 	= false;
	$statusUpdated	= false;
	$getResult	= false;

	#-------------------Admin Checking--------------------------------------
	$isAdmin 			= false;
	$role		=	$manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

	// Cheking access control
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
	// Cheking access control end 

	if ($g["redirect"]!="") $getResult = true;		

	// get selected date 
	if ($g["dateFrom"]!="" && $g["dateTill"]!="") {
		$dateFrom = $g["dateFrom"];
		$dateTill = $g["dateTill"];
	} else if ($p["dateFrom"]!="" && $p["dateTill"]!="") {
		$dateFrom = $p["dateFrom"];
		$dateTill = $p["dateTill"];
	} else {
		$dateFrom = date("d/m/Y");
		$dateTill = date("d/m/Y");
	}

	$fromDate	=	mysqlDateFormat($dateFrom);
	$tillDate	=	mysqlDateFormat($dateTill);

	if ($p["invoiceType"]!="") 	$invoiceType 	= $p["invoiceType"];

	if ($g["reportType"]!="") 	$reportType 	= $g["reportType"];
	else $reportType 	= $p["reportType"];

	if ($g["selStatus"]!="")	$selStatus	= $g["selStatus"];
	else $selStatus	= $p["selStatus"];

	if ($p["selTransporter"]!="")	$selTransporter 	= $p["selTransporter"];
	if ($p["selDistributor"]!="") 	$selDistributorId 	= $p["selDistributor"];

	if ($p["selState"]!="")		$selState		= $p["selState"];
	if ($p["selCity"]!="")		$selCity		= $p["selCity"];	
	if (sizeof($selCity)>0) {		
		$selCityArr = implode(",",$selCity);
	}

	if ($p["periodType"]!="")	$periodType	= $p["periodType"]; // Yearly/ Month wise
	if ($p["selZone"]!="")		$selZoneId	= $p["selZone"];
	if ($p["selSOCity"]!="")	$selSOCityId	= $p["selSOCity"];
	
	$totNetWt = $p["totNetWt"];
	$totNetWtChk = "";
	if ($totNetWt)	$totNetWtChk = "Checked";

	$totNumPack = $p["totNumPack"];
	$totNumPackChk = "";	
	if ($totNumPack) $totNumPackChk = "Checked";
	
	# Generate Report
	if ($p["cmdSearch"]!="" || $statusUpdated || $getResult) {
		if ($fromDate!="" && $fromDate!="") {
			$salesOrderRecords = $salesReportObj->fetchSalesOrderRecords($fromDate, $tillDate, $invoiceType, $reportType, $selTransporter, $selDistributorId, $selState, $selCityArr, $selStatus, $selZoneId);
			$searchMode = true;
		}
	}

	# New
	if ($p["cmdSearch"]!="" && $periodType!="") {
		if ($fromDate!="" && $fromDate!="") {
			
			if (!$totNetWtChk && !$totNumPackChk) {
				$periodWiseInvRecords = $salesReportObj->getPeriodTypeRecords($fromDate, $tillDate, $invoiceType, $reportType, $selTransporter, $selDistributorId, $selState, $selCityArr, $selStatus, $periodType, $selZoneId, $selSOCityId);

			} 	

			if ($totNetWtChk || $totNumPackChk) {
				$fetchAllPeriod = $salesReportObj->getPeriod($fromDate, $tillDate, $invoiceType, $selStatus, $periodType);
				$productWiseRecs = $salesReportObj->getSelProducts($fromDate, $tillDate, $invoiceType, $selStatus);
			}
			$searchMode = true;
			
		}
	}
	


	$ON_LOAD_SAJAX = "Y"; // This screen is integrated with SAJAX, settings for TopLeftNav	
	# include JS in template
	$ON_LOAD_PRINT_JS = "libjs/SalesReport.js";	

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmSalesReport" action="SalesReport.php" method="post">
	<table cellspacing="0"  align="center" cellpadding="0" width="85%" >
	
		<tr>
			<td height="20" align="center" class="err1" ><? if($err!="" ){?> <?=$err;?><?}?> </td>
			
		</tr>
		<?php
			if ($editMode || $addMode) {
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
									<td background="images/heading_bg.gif" class="pageName" >&nbsp;Sales Report</td>
									<td background="images/heading_bg.gif"  >
									<table cellpadding="0" cellspacing="0" align="right">	
									<tr>
									</tr>
									</table></td>
								</tr>
								<tr>
									<td width="1" ></td>
								  <td colspan="2" ><table cellpadding="0"  width="65%" cellspacing="0" border="0" align="center">
                                    <tr>
                                      <td height="10" ></td>
                                    </tr>
	<tr>
		<TD colspan="3" align="center">
			<table>
				<TR>
					<TD valign="top" style="padding-left:10px;padding-right:10px;padding-bottom:10px;paing-top:10px;">
						<fieldset>
						<table>
							<TR>
								<td class="fieldName" nowrap align="left">*From&nbsp; </td>
								<td>
							<? 
								if ($dateFrom=="") $dateFrom=date("d/m/Y");
							?>
								<input type="text" id="dateFrom" name="dateFrom" size="8" value="<?=$dateFrom?>" onchange="xajax_getStateList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_distributorList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_transporterList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_zoneList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value, ''); xajax_soCityList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value, '');">&nbsp;&nbsp;
								</td>
							</TR>
							<tr>
								<td class="fieldName"  nowrap align="left">*To&nbsp;</td>
								<td>
								<? 
									if ($dateTill=="") $dateTill=date("d/m/Y");
								?>
								<input type="text" id="dateTill" name="dateTill" size="8"  value="<?=$dateTill?>" onchange="xajax_getStateList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_distributorList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_transporterList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_zoneList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value, ''); xajax_soCityList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value, '');">
								</td>
							</tr>
							<tr>
								<td class="fieldName"  nowrap align="left">Report Type&nbsp;</td>
								<td>
								<select name="periodType" id="periodType">
								  <!--<option value="">--Detailed--</option>-->
								  <option value="M" <? if ($periodType=='M') echo "selected";?>>Monthly</option>
								  <option value="Y" <? if ($periodType=='Y') echo "selected";?>>Yearly</option>
								</select>
								</td>
							</tr>
							<tr>
								<td class="fieldName"  nowrap align="left">Invoice Type&nbsp;</td>
								<td>
									<select name="invoiceType" id="invoiceType" onchange="xajax_getStateList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_distributorList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_transporterList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_zoneList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value, ''); xajax_soCityList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value, '');">
										<option value="">--Select All--</option>
										<option value="T" <? if ($invoiceType=='T') echo "selected";?>>Taxable</option>
										<option value="S" <? if ($invoiceType=='S') echo "selected";?>>Sample</option>
									</select>
								</td>
							</tr>	
						<tr>
								<td class="fieldName"  nowrap align="left">Status&nbsp;</td>
								<td>
									<select name="selStatus" id="selStatus" onchange="xajax_getStateList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_distributorList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_transporterList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_zoneList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value, ''); xajax_soCityList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value, '');">
										<option value="">-- Select All --</option>
										<option value="C" <? if ($selStatus=='C') echo "selected";?>>Complete</option>
										<option value="P" <? if ($selStatus=='P') echo "selected";?>>Pending</option>
									</select>
								</td>
						</tr>	
						<tr>
                                                  <td class="fieldName" nowrap="true" align="left">*Type</td>
                                                  <td class="listing-item">
							<select name="reportType" id="reportType" onchange="showSearchOption(); xajax_getStateList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_distributorList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_transporterList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value,''); xajax_zoneList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value, ''); xajax_soCityList(document.getElementById('dateFrom').value,document.getElementById('dateTill').value,document.getElementById('invoiceType').value,document.getElementById('selStatus').value, '');">
								<option value="">-- Select --</option>
								<option value="DIST" <? if ($reportType=='DIST') echo "selected";?>>Distributor</option>
								<option value="TRAN" <? if ($reportType=='TRAN') echo "selected";?>>Transporter</option>
								<option value="STAT" <? if ($reportType=='STAT') echo "selected";?>>State</option>
								<option value="ZONE" <? if ($reportType=='ZONE') echo "selected";?>>Zone</option>
								<option value="CITW" <? if ($reportType=='CITW') echo "selected";?>>City</option>
								<option value="OTHR" <? if ($reportType=='OTHR') echo "selected";?>>Other</option>
							</select>
					</td>
                                                </tr>
						</table>
					</fieldset>
					</TD>
	<td width="10">&nbsp;</td>
	<td valign="top">
		<fieldset>
			<legend class="fieldName" style="line-height:normal;">Search Options</legend>
			<table>
				<TR id="transporterRow">
					<TD colspan="2" align="left">
					<table>
					<tr>
						<td nowrap class="fieldName">*Transporter</td>
						<td nowrap>
							<select name="selTransporter" id="selTransporter">
							<option value="">-- Select All --</option>
							<?php
								foreach ($transporterRecords as $tr) {
									$transporterId	 = $tr[0];
									$transporterName = stripSlash($tr[2]);	
									$selected = "";
									if ($selTransporter==$transporterId) $selected = "selected";	
							?>
							<option value="<?=$transporterId?>" <?=$selected?>><?=$transporterName?></option>
							<?php 
								}
							?>
							</select>
						</td>
					</tr>
					</table>	
				</TD>
				</TR>
				<TR id="distributorRow">
					<TD colspan="2" align="left">
					<table>
					<tr>
						<td nowrap class="fieldName">Distributor</td>
						<td nowrap>
							<select name="selDistributor" id="selDistributor" onchange="xajax_getCityList(document.getElementById('selDistributor').value,'');">
							<option value="">-- Select --</option>			
							</select>
						</td>
						<td nowrap class="fieldName">City</td>
						<td>
							<select name="selCity[]" id="selCity" multiple='true' size='5'>
								<option value="">-- Select All --</option>
							</select>
						</td>
					</tr>
					</table>	
				</TD>
				</TR>
				<TR id="stateRow">
					<TD colspan="2" align="left">
					<table>
					<tr>
						<td nowrap class="fieldName">State</td>
						<td nowrap>
							<select name="selState" id="selState">
							<option value="">-- Select All--</option>		
							</select>
						</td>
					</tr>
					</table>	
				</TD>
				</TR>
				<TR id="zoneRow">
					<TD colspan="2" align="left">
					<table>
					<tr>
						<td nowrap class="fieldName">Zone</td>
						<td nowrap>
							<select name="selZone" id="selZone">
							<option value="">-- Select All--</option>		
							</select>
						</td>
					</tr>
					</table>	
				</TD>
				</TR>
				<TR id="soCityRow">
					<TD colspan="2" align="left">
					<table>
					<tr>
						<td nowrap class="fieldName">City</td>
						<td nowrap>
							<select name="selSOCity" id="selSOCity">
							<option value="">-- Select All--</option>		
							</select>
						</td>
					</tr>
					</table>	
				</TD>
				</TR>
				<TR id="otherRow">
					<TD colspan="2" align="left">
					<table>
					<tr>
						<td nowrap>
							<table>
								<TR>
									<TD>
										<input type="checkbox" name="totNetWt" id="totNetWt" value="Y" <?=$totNetWtChk?> class="chkBox" onclick="removeAllChk('totNetWt');">
									</TD>
									<td class="listing-item" onMouseover="ShowTip('Total Net Wt of each product.');" onMouseout="UnTip();">Total Net Wt</td>
								</TR>
							</table>
						</td>
					</tr>
					<tr>
						<td nowrap>
							<table>
								<TR>
									<TD>
										<input type="checkbox" name="totNumPack" id="totNumPack" value="Y" <?=$totNumPackChk?> class="chkBox" onclick="removeAllChk('totNumPack');">
									</TD>
									<td class="listing-item" onMouseover="ShowTip('Total no packs of each product.');" onMouseout="UnTip();">Total No of Packs</td>
								</TR>
							</table>
						</td>
					</tr>
					</table>	
				</TD>
				</TR>
	<TR>
		<TD colspan="3" align="center">
			<INPUT TYPE="submit" class="button" name="cmdSearch" value="Generate Report" onclick="return validateSalesReport();">
		</TD>
	</TR>
	</table>
	</fieldset>
	</td>
	</TR>
	</table>
		</TD>
	</tr>
        <tr>
               <td colspan="3" nowrap>&nbsp;</td>
       </tr>
	<? if ($succMsg) {?>
		<tr>
			<td colspan="3" nowrap class="successMsg" align="center"><strong><?=$succMsg?></strong></td>
		</tr>
	<? }?>	
	<?php
	if (sizeof($salesOrderRecords)>0 && !$periodType) {
		$i=0;
	?>
	<tr>
               <td  height="5" colspan="4" style="padding:left:10px; padding-right:10px;" align="center">
		<? if ($print==true) {?>
		<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintSalesReport.php?dateFrom=<?=$dateFrom?>&dateTill=<?=$dateTill?>&invoiceType=<?=$invoiceType?>&reportType=<?=$reportType?>&selTransporter=<?=$selTransporter?>&selDistributor=<?=$selDistributorId?>&selState=<?=$selState?>&selCity=<?=$selCity?>&selCityArr=<?=$selCityArr?>&selStatus=<?=$selStatus?>',700,600);">
		<? }?>
		</td>
		</tr>
	<tr>
               <td  height="10" colspan="4" style="padding:left:10px; padding-right:10px;">
		</td>
	</tr>
        <tr>
               <td colspan="5" style="padding-left:10px; padding-right:10px;">
		<table width="99%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" align="center">
	      	<tr bgcolor="#f2f2f2" align="center"> 
			<th nowrap="nowrap" class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Sr.No</th>
			<th nowrap="nowrap" class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Date</th>			
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Name of the Party</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Location</th>
			<th align="center" class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Invoice No</th>
                	<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Inv Value</th>
			<? if ($selDistributorId || $selState) {?>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Transporter</th>			
			<? }?>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Docket No.</th>
			<th class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">Status</th>
			<td class="listing-head"></td>
              </tr>
              <?php
		$totalInvoiceValue = 0;
		$invType = "";
		foreach($salesOrderRecords as $sor){
			$i++;
			$salesOrderId		= $sor[0];
			//$salesOrderNo		= $sor[1];
			$soNo 	= $sor[1];		
			$invType = $sor[20];			
			$pfNo 	= $sor[21];
			$saNo	= $sor[22];
			$salesOrderNo = "";
			if ($soNo!=0) $salesOrderNo=$soNo;
			else if ($invType=='T') $salesOrderNo = "P$pfNo";
			else if ($invType=='S') $salesOrderNo = "S$saNo";

			$distributorId		= $sor[2];
			$soDate			= dateFormat($sor[3]);
			$despatchDate		= dateFormat($sor[4]);			
	
			$invoiceValue		= $sor[17];	// Grand Total Invoice Amt $sor[6];
			$cityId			= $sor[7];
			$grossWt		= $sor[9];
			$numBox			= $sor[10];			
			$docketNum		= $sor[12];

			$cityName		= $sor[15];
			$distributorName	= $sor[13];
			$transporterId		= $sor[11];
			$transporterName = "";		
			if ($transporterId) {
				$transporterRec		= $transporterMasterObj->find($transporterId);
				$transporterName	= stripSlash($transporterRec[2]);
			}
			
			//$status = ($sor[16]=='C')?"<span style=\"color:#003300\">Complete</span>":"<span class='err1'>Pending</span>";

			$totalInvoiceValue  += $invoiceValue;
			# ----------------------- Status --------
			$completeStatus = $sor[16];
			$selLastDate	= $sor[18];
			$extended	= $sor[19];
			$currentDate	= date("Y-m-d");
			$dateDiff = dateDiff(strtotime($currentDate), strtotime($selLastDate), 'D');
			$displayColor = ""; 
			if ($extended=='E' && ($completeStatus=="" || $completeStatus=='P')) {
				$status	= "PENDING (Extended)";
				$displayColor = "Grey";
			} else {
				if ($completeStatus=='C') {
					$status	= " COMPLETED ";					
					$displayColor = "#90EE90"; // LightGreen	
				} else if ($dateDiff<0) {
					$status = "DELAYED";
					$displayColor = "#DD7500"; // LightOrange
				} else {
					$status = "PENDING";			
					$displayColor = "White";
				}
			}			
			# ----------------------- Status Ends--------
		?>
              <tr bgcolor="#FFFFFF"> 
		<td class="listing-item" align="center" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$i?>
		</td>		
		<td class="listing-item" nowrap height='25' style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$soDate?>			
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$distributorName?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$cityName?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$salesOrderNo?>
		</td>
                <td class="listing-item" align="right" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$invoiceValue?>
		</td>	
		<? if ($selDistributorId || $selState) {?>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$transporterName?>
		</td>		
		<?
			}
		?>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true">
			<?=$docketNum?>
		</td>
		<td class="listing-item" align="left" style="padding-left:2px; padding-right:2px;font-size:11px;" nowrap="true" bgcolor="<?=$displayColor?>">
			<?=$status?>			
		</td>
		<td class="listing-item" align="center" nowrap style="padding-left:5px; padding-right:5px;">
			<a href="javascript:printWindow('ViewSOTaxInvoice.php?selSOId=<?=$salesOrderId?>',700,600)" class="link1" title="Click here to View the Tax Invoice">
				View
			</a>
		</td>			
              </tr>
		<?php
			}
		?>
	<tr bgcolor="#FFFFFF">
		<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" colspan="5" align="right">Total:</td>
		<td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:11px;" align="right" nowrap="true"><strong><?=number_format($totalInvoiceValue,2,'.',',')?></strong></td>
		<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" colspan="4" align="right"></td>		
	</tr>	
      </table>
	</td>
	</tr>	
	<tr>
               <td  height="10" colspan="4" style="padding:left:10px; padding-right:10px;" align="center">
		</td>
	</tr>
	<tr>
               <td  height="5" colspan="4" style="padding:left:10px; padding-right:10px;" align="center">
<? if ($print==true) {?>
<input type="button" name="cmdAdd" class="button" value=" Print " onClick="return printWindow('PrintSalesReport.php?dateFrom=<?=$dateFrom?>&dateTill=<?=$dateTill?>&invoiceType=<?=$invoiceType?>&reportType=<?=$reportType?>&selTransporter=<?=$selTransporter?>&selDistributor=<?=$selDistributorId?>&selState=<?=$selState?>&selCity=<?=$selCity?>&selCityArr=<?=$selCityArr?>&selStatus=<?=$selStatus?>',700,600);">
<? }?>
		</td>
		</tr>
	<?php 
		} else if ($dateFrom!="" && $dateTill!="" && $searchMode && $periodType=="") {			
	?>
	<tr>
		<td colspan="3" height="5" class="err1" align="center"><?=$msgNoRecords;?></td>
	</tr>
	<? }?>
<!-- Monthly/ Year Wise	 -->
	<?php
		if ($periodType!="" && sizeof($periodWiseInvRecords)>0) {

			$displayTbleHead = ($periodType=='M')?"Month":"Year";
	?>
	<tr>
               <td colspan="5" style="padding-left:10px; padding-right:10px;">
		<table width="59%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" align="center">
	      	<tr bgcolor="#f2f2f2" align="center"> 
			<th nowrap="nowrap" class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;" nowrap="true">Sr.No</th>
			<th nowrap="nowrap" class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;" nowrap="true"><?=$displayTbleHead?></th>
                	<th class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;" nowrap="true">Total Amount</th>
              </tr>
              <?php
		$totalAmt = 0;	
		$i = 0;	
		$displayMonth = "";
		$grandTotalAmt = 0;
		foreach($periodWiseInvRecords as $pwir){
			$i++;			
			$month	= $pwir[0];
			$year   = $pwir[1];
			$displayMonthYear = $month."&nbsp;".$year;
			$displayListingHead = ($periodType=='M')?$displayMonthYear:$year;
			$totalAmt = $pwir[2];
			$grandTotalAmt += $totalAmt;
		?>
              <tr bgcolor="#FFFFFF"> 
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;font-size:11px;" nowrap="true">
			<?=$i?>
		</td>		
		<td class="listing-item" nowrap height='25' style="padding-left:5px; padding-right:5px;font-size:11px;" nowrap="true">
			<?=$displayListingHead?>			
		</td>	
                <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;font-size:11px;" nowrap="true">
			<?=$totalAmt?>
		</td>				
              </tr>
		<?php
			}
		?>
	<tr bgcolor="#FFFFFF">
		<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" colspan="2" align="right">Total:</td>
		<td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:11px;" align="right" nowrap="true"><strong><?=number_format($grandTotalAmt,2,'.',',')?></strong></td>		
	</tr>	
      </table>
	</td>
	</tr>
	<?php
		}
	?>
<!-- Monthly/ Year Wise	 Ends here -->
<!-- 	Product Wise Details -->
<?php
		if ($periodType!="" && sizeof($productWiseRecs)>0) {

			$displayTbleHead = ($totNetWtChk)?"Total Net Wt(Kg)":"Total No of Packs";
	?>
	<tr>
               <td colspan="5" style="padding-left:10px; padding-right:10px;">
		<table width="59%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999" align="center">
	      	<tr bgcolor="#f2f2f2" align="center"> 
			<th nowrap="nowrap" class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;" nowrap="true" rowspan="2">Sr.No</th>
			<th nowrap="nowrap" class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;" nowrap="true" rowspan="2">Product</th>
			<th class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;" nowrap="true" colspan="<?=sizeof($fetchAllPeriod)?>"><?=$displayTbleHead?></th>		
              </tr>
		<tr bgcolor="#f2f2f2" align="center">
			<?php 
				foreach ($fetchAllPeriod as $fap) {
					$displayH = $fap[0];
			?>
                	<th class="listing-head" style="padding-left:5px; padding-right:5px;font-size:11px;" nowrap="true"><?=$displayH?></th>
			<? }?>
		</tr>
              <?php		
		$i = 0;
		$totArr = array();
		foreach($productWiseRecs as $pwr){
			$i++;			
			$productId 	= $pwr[0];
			$productName	= $pwr[1];
		?>
              <tr bgcolor="#FFFFFF"> 
		<td class="listing-item" align="center" style="padding-left:5px; padding-right:5px;font-size:11px;" nowrap="true">
			<?=$i?>
		</td>		
		<td class="listing-item" nowrap height='25' style="padding-left:5px; padding-right:5px;font-size:11px;" nowrap="true">
			<?=$productName?>			
		</td>	
		<?php 
			foreach ($fetchAllPeriod as $fap) {
				$displayH 	=  $fap[0];
				$extractMonth	= $fap[1];
				# Product Wise output
				$totalAmt = $salesReportObj->getPeriodWiseProductRecs($fromDate, $tillDate, $invoiceType, $selStatus, $periodType, $productId, $extractMonth, $totNetWt, $totNumPack);
				$totArr[$displayH] += $totalAmt;
		?>
                <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;font-size:11px;" nowrap="true">
			<?=$totalAmt?>
		</td>				
		<?php
			}
		?>
              </tr>			
		<?php
			}
		?>
	<tr bgcolor="#FFFFFF">
		<td class="listing-head" style="padding-left:2px; padding-right:2px;font-size:11px;" colspan="2" align="right">Grand Total:</td>
		<?php 
			$displayH = "";
			foreach ($fetchAllPeriod as $fap) {
				$displayH 	=  $fap[0];
				$displayTot = ($totNetWtChk)?number_format($totArr[$displayH],2,'.',','):$totArr[$displayH];
		?>
		<td class="listing-item" style="padding-left:2px; padding-right:2px;font-size:11px;" align="right" nowrap="true"><strong><?=$displayTot?></strong></td>		
		<?php } ?>
	</tr>
      </table>
	</td>
	</tr>
	<?php
		}
	?>
<!--  Product wise Details Ends here-->
				    <tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
                                    <tr>
                                      <td  height="10" colspan="4" ></td>
                                    </tr>
                                    <tr>
                                      <td  height="10" ></td>
                                    </tr>
                                  </table></td>
								</tr>
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
			inputField  : "dateFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateFrom", 
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
			inputField  : "dateTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "dateTill", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">		
		disableSerachOptions();
		showSearchOption();
		xajax_getCityList('<?=$selDistributorId?>','<?=$selCityArr?>');	
	</SCRIPT>
	<?php
		if ($dateFrom!="" && $dateTill!="") {
		//$invoiceType, $reportType, $selTransporter, $selDistributorId, $selState, $selCityArr, $selStatus
	?>	
	<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
		xajax_getStateList('<?=$dateFrom?>', '<?=$dateTill?>', '<?=$invoiceType?>', '<?=$selStatus?>', '<?=$selState?>');
		xajax_distributorList('<?=$dateFrom?>', '<?=$dateTill?>', '<?=$invoiceType?>', '<?=$selStatus?>', '<?=$selDistributorId?>');
		xajax_transporterList('<?=$dateFrom?>', '<?=$dateTill?>', '<?=$invoiceType?>', '<?=$selStatus?>', '<?=$selTransporter?>');
		xajax_zoneList('<?=$dateFrom?>', '<?=$dateTill?>', '<?=$invoiceType?>', '<?=$selStatus?>', '<?=$selZoneId?>');
		xajax_soCityList('<?=$dateFrom?>', '<?=$dateTill?>', '<?=$invoiceType?>', '<?=$selStatus?>', '<?=$selSOCityId?>');
		// Required Function
		/*
			getRequiredFunction('<?=$dateFrom?>', '<?=$dateTill?>', '<?=$invoiceType?>', '<?=$selStatus?>', '<?=$selState?>', '<?=$selDistributorId?>', '<?=$selTransporter?>', '<?=$selZoneId?>', '<?=$selSOCityId?>', '<?=$selCityArr?>', '<?=$reportType?>'); 
		*/
	</SCRIPT>
	<?php
		}
	?>	
	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>