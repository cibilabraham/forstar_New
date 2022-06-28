<?php
	require("include/include.php");
	$err		= 	"";
	$errDel		= 	"";	
	$fishId		=	"";
	$searchEnabled  = false;
	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	
	list($moduleId,$functionId) = $modulemanagerObj->resolveIds($currentUrl);

	$accesscontrolObj->getAccessControl($moduleId,$functionId);
	if (!$accesscontrolObj->canAccess()) { 
		//echo "ACCESS DENIED";
		header ("Location: ErrorPage.php");
		die();	
	}	
	
	if($accesscontrolObj->canAdd()) $add=true;
	if($accesscontrolObj->canEdit()) $edit=true;
	if($accesscontrolObj->canDel()) $del=true;
	if($accesscontrolObj->canPrint()) $print=true;
	if($accesscontrolObj->canConfirm()) $confirm=true;	
	//----------------------------------------------------------

	if ($g["filterType"]!="") $filterType = $g["filterType"];
	else $filterType = $p["filterType"];

	$searchMode = $p["searchMode"];
	if ($searchMode=='CNW') $challanSearch = "Checked";
	else if($searchMode=='DWS') $dateWiseSearch = "Checked";
	else $searchMode = "";
	
	$weighNumber	= $p["weighNumber"];
	if ($p["billingCompany"]!="") $billingCompany = $p["billingCompany"];

	# select record between selected date
	$dateFrom = $p["supplyFrom"];
	$dateTill = $p["supplyTill"];

	$fromDate		= mysqlDateFormat($dateFrom);
	$tillDate		= mysqlDateFormat($dateTill);

	$selectUnit		=	$p["selUnit"];
	$landingCenterId	=	$p["landingCenter"];
	$selectSupplier		=	$p["supplier"];
	$fishId			=	$p["fish"];
	$processId		=	$p["processCode"];
	$dateSelectFrom		=	'WCD'; /* Weighment Challan date*/

	if ($dateFrom!="" && $dateTill!="") {
	
		#List all units
		$plantRecords = $dailycatchsummaryObj->fetchPlantWiseRecords($fromDate, $tillDate, $selectADate, $dateSelectFrom);
	
		#List all Landing Center
		$landingCenterRecords	= $dailycatchsummaryObj->fetchLandingCenterRecords($fromDate, $tillDate, $selectUnit, $selectADate, $dateSelectFrom);
		
		if ($filterType=='SW') $selBillingCompany = "";
		else $selBillingCompany = $billingCompany;	

		#List all supplier
		$supplierRecords = $dailycatchsummaryObj->fetchSupplierRecords($fromDate, $tillDate, $landingCenterId, $selectUnit, $selectADate, $dateSelectFrom, $selBillingCompany);

		if ($filterType=='SW') $selSupplier = $selectSupplier;
		else $selSupplier = "";

		# Get Billing Comapany  Records
		$billingCompanyRecords = $dailycatchsummaryObj->fetchBillingCompanyRecords($fromDate, $tillDate, $selSupplier, $landingCenterId, $selectUnit, $selectADate, $dateSelectFrom);	

		#List All Fishes	
		$fishMasterRecords = $dailycatchsummaryObj->fetchFishRecords($fromDate, $tillDate, $selectSupplier, $landingCenterId, $selectUnit, $selectADate, $dateSelectFrom, $billingCompany);
		
		if ($fishId!="") {
			$processCodeRecords = $dailycatchsummaryObj->getProcessCodeRecords($fromDate, $tillDate, $fishId, $selectSupplier, $landingCenterId, $selectUnit, $selectADate, $dateSelectFrom, $billingCompany);	
		}	
	}

	# Search
	if ($p["cmdSearch"]!="" || ($dateFrom!="" && $dateTill!="")) {

		$weighNumber	= trim($p["weighNumber"]);

		#Filter daily catch records
		if ($weighNumber || ($dateFrom!="" && $dateTill!="")) {
		 
			$dailyCatchReportResultSetObj = $localquantityreportObj->filterDailyCatchEntryRecords($selectUnit, $landingCenterId, $selectSupplier, $fromDate, $tillDate, $fishId, $processId, $weighNumber, $billingCompany);
		
			$dailyCatchReportRecords = $dailyCatchReportResultSetObj->getNumRows();	
			$searchEnabled  = true;	
		}
	}

	if ($searchMode=='CNW') {
		# Get Billing Comapany  Records
		//$billingCompanyRecords = $billingCompanyObj->fetchAllRecords();
		$billingCompanyRecords = $billingCompanyObj->fetchAllRecordsActivebillingCompany();
	}
	

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmLocalQtyReport" id="frmLocalQtyReport" action="LocalQuantityReport.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">
		<tr>
			<td height="30" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>		
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="581" colspan="2" background="images/heading_bg.gif" class="pageName">&nbsp;Local Quantity Report</td>
								</tr>
								<tr>
									<td width="1" ></td>
								  <td colspan="2"  align="center">
									<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
									<tr> 
										<td colspan="2" height="10" ></td>
			                      </tr>
						          <tr> 
                                  <td colspan="4" align="center">
				  <? if($print==true){?>
				  <input type="button" name="Submit" value=" View / Print" class="button" onClick="return printWindow('PrintLocalQuantityReport.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&selUnit=<?=$selectUnit?>&landingCenter=<?=$landingCenterId?>&supplier=<?=$selectSupplier?>&fish=<?=$fishId?>&processCode=<?=$processId?>&weighNumber=<?=$weighNumber?>&billingCompany=<?=$billingCompany?>',700,600);" <? if(sizeof($dailyCatchReportRecords)==0) echo $disabled="disabled";?>>&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; </td>
                        <? } ?>
                      </tr>
                      <input type="hidden" name="hidDailyRateId" value="<?=$dailyRateId;?>">
                      <tr> 
                        <td colspan="3" nowrap>&nbsp;</td>
                        </tr>
                      <tr> 
                        <td colspan="3" nowrap>
			<table width="85%" border="0" align="center" cellpadding="0" cellspacing="0">
				<!-- nee Search type Here-->
				<tr>
				<TD align="center">
				<table><TR><TD colspan="2">
				<fieldset><legend class="fieldName">Search </legend>
				<table width="200" border="0">
                                      <tr>
                                        <td><table width="100" border="0">
                                          <tr>
                                            <td><input name="searchMode" type="radio" value="CNW" onclick="this.form.submit();" <?=$challanSearch?> class="chkBox"></td>
                                            <td nowrap class="listing-item">Challan No </td>
                                          </tr>
                                        </table></td>
                                        <td><table width="100" border="0">
                                          <tr>
                                            <td><input name="searchMode" type="radio" value="DWS" onClick="this.form.submit();" <?=$dateWiseSearch?> class="chkBox"></td>
                                            <td nowrap class="listing-item">Detailed </td>
                                          </tr>
                                        </table></td>
                                      </tr>
                                    </table></fieldset></TD></TR></table>
					</TD>
				</tr>
				<tr><TD height="10"></TD></tr>
				<!-- nee Search type End Here-->
				<!-- Challan Number Wise Search  Start-->
			<? if ($searchMode=='CNW') {?>
			<tr><TD align="center">
			<table cellpadding="2" cellspacing="0">
			    <tr>
				<TD class="fieldName">*Billing Company</TD>
				<td>					
					<select name="billingCompany" id="billingCompany">
					<?php
					foreach ($billingCompanyRecords as $bcr) {
						$billingCompanyId	= $bcr[0];
						$cName			= $bcr[1];
						$defaultChk		= $bcr[10];
						$displayCName		= $bcr[9];
						$selected = "";
						if ($billingCompanyId==$billingCompany || ($billingCompany=="" && $defaultChk=='Y') ) $selected = "selected";
					?>
					<option value="<?=$billingCompanyId?>" <?=$selected?>><?=$displayCName?></option>
					<?	
					}	
					?>
					</select>
				</td>
			</tr>
                            <tr>
                          <td class="fieldName" nowrap="nowrap">Weighment Challan:</td>
                          <td nowrap="nowrap">
			  <? $weighNumber	=	$p["weighNumber"];?>
			  <input name="weighNumber" type="text" id="weighNumber" size="8" value="<?=$weighNumber?>">&nbsp;&nbsp;</td>
			  <td><input type="submit" name="cmdSearch" value=" Search" class="button" onclick="if(document.getElementById('weighNumber').value=='') { alert('Please enter a Weigment Challan Number.'); document.getElementById('weighNumber').focus(); return false; }" /></td>
                            </tr></table></TD></tr>
			   <? }?>
			<!-- Challan Number Wise Search  End-->
			<!-- Detailed Search Start here -->
			<? if($searchMode=='DWS') { ?>			
			<tr><TD align="center">
			<table>
                            <tr> 
                              <td valign="top">
				<table>
				<tr>
				<td colspan="2">
				<fieldset>
				 <legend class="listing-item">Date Selection</legend>
					<table width="100" border="0">
                                      <tr>
                                        <td>
					
					<table width="60" border="0">
					<tr> 
                                    <td class="fieldName"> From:</td>
                                    <td> 
                                      <? $dateFrom = $p["supplyFrom"];?>
                                      <input type="text" id="supplyFrom" name="supplyFrom" size="8" value="<?=$dateFrom?>" onchange="submitForm('supplyFrom','supplyTill',document.frmLocalQtyReport);"></td>
                                  </tr>
                                  <tr> 
                                    <td class="fieldName"> Till:</td>
                                    <td> 
                                      <? $dateTill = $p["supplyTill"];?>
                                      <input type="text" id="supplyTill" name="supplyTill" size="8"  value="<?=$dateTill?>" onchange="submitForm('supplyFrom','supplyTill',document.frmLocalQtyReport);"/></td>
                                  </tr> 
                                        </table></td> 
                                      </tr>
                                    </table>
					</fieldset>
				  </td>
				</tr>
                                </table></td>
				<td width="10"></td>
                              <td valign="top"><table width="200" cellpadding="0" cellspacing="0">
                                  <tr> 
                                    <td class="fieldName" align="left">Plant:</td>
                                    <td> 
                                      <? $selectUnit			=	$p["selUnit"]; ?>
                                      <select name="selUnit" onchange="this.form.submit();">
                                        <option value="0">--Select All--</option>
                                        <?
					foreach($plantRecords as $pr) {
						$plantId	= $pr[0];
						$plantName	= stripSlash($pr[2]);
						$selected = ($plantId==$selectUnit)?"selected":"";
					?>
                                        <option value="<?=$plantId?>" <?=$selected?>><?=$plantName?></option>
                                        <? }?>
                                      </select> </td>
                                  </tr>
                                  <tr> 
                                    <td class="fieldName" nowrap>Landing Center:</td>
                                    <td> 
                                      <? $landingCenterId	=	$p["landingCenter"];?>
                                <select name="landingCenter" id="landingCenter" onchange="this.form.submit();">
				<option value="0">--Select All--</option>
                                <?
				foreach($landingCenterRecords as $fr) {
					$centerId	=	$fr[0];
					$centerName	=	stripSlash($fr[2]);
					$selected = ($centerId==$landingCenterId)?"selected":"";
				?>
                                <option value="<?=$centerId?>" <?=$selected?>><?=$centerName?></option>
                                <? } ?>
                                </select></td>
                                  </tr>
				<TR>
		<TD class="fieldName">
			Filter
		</TD>
		<td nowrap="true" style="padding-left:2px; padding-right:2px;">
			<select name="filterType" id="filterType" style="width:70px;" onchange="this.form.submit();">
				<option value="BW" <? if ($filterType=='BW') echo "selected";?>>Billing Company Wise</option>
				<option value="SW" <? if ($filterType=='SW') echo "selected";?>>Supplier Wise</option>
			</select>
		</td>
	</TR>	
	<?php
		if ($filterType=='SW') {
	?>
	 			<tr> 
                                    <td class="fieldName">Supplier:</td>
                                    <td> 
                                      <? $selectSupplier = $p["supplier"];?>
                                      <select name="supplier" onchange="this.form.submit();">
					<option value="0">--Select All--</option>
                                        <?php
						foreach($supplierRecords as $fr) {
							$supplierId	= $fr[0];
							$supplierName	= stripSlash($fr[2]);
							$selected	= ($supplierId == $selectSupplier)?"selected":"";
					?>
                                        <option value="<?=$supplierId?>" <?=$selected?>><?=$supplierName?></option>
                                        <? } ?>
                                      </select></td>
                                  </tr>
				<tr>
				<TD class="fieldName" nowrap="true">Billing Company:</TD>
				<td>
					<select name="billingCompany" id="billingCompany" onchange="this.form.submit();">		
					<option value="">--Select All--</option>
					<?php
					foreach ($billingCompanyRecords as $bcr) {
						$billingCompanyId	= $bcr[0];
						$displayCName		= $bcr[1];
						$selected = ($billingCompanyId==$billingCompany)?"selected":"";
					?>
					<option value="<?=$billingCompanyId?>" <?=$selected?>><?=$displayCName?></option>
					<?php	
					}	
					?>
					</select>
				</td>
			</tr>
				
	<?php
		} else {
	?>
			<tr>
				<TD class="fieldName" nowrap="true">Billing Company:</TD>
				<td>
					<select name="billingCompany" id="billingCompany" onchange="this.form.submit();">		
					<option value="">--Select All--</option>
					<?
					foreach ($billingCompanyRecords as $bcr) {
						$billingCompanyId	= $bcr[0];
						$displayCName		= $bcr[1];
						$selected = "";
						if ($billingCompanyId==$billingCompany) $selected = "selected";
					?>
					<option value="<?=$billingCompanyId?>" <?=$selected?>><?=$displayCName?></option>
					<?	
					}	
					?>
					</select>
				</td>
			</tr>
			 <tr> 
                                    <td class="fieldName">Supplier:</td>
                                    <td> 
                                      <? $selectSupplier = $p["supplier"];?>
                                      <select name="supplier" onchange="this.form.submit();">
					<option value="0">-- Select All --</option>
                                        <?
						foreach($supplierRecords as $fr) {
							$supplierId	=	$fr[0];
							$supplierName	=	stripSlash($fr[2]);
							$selected	=	"";
							if ($supplierId == $selectSupplier) $selected = "selected";
					?>
                                        <option value="<?=$supplierId?>" <?=$selected?>> 
                                        <?=$supplierName?>
                                        </option>
                                        <? } ?>
                                      </select></td>
                                  </tr>
			<?php
				}
			?>
                                  <tr> 
                                    <td class="fieldName">Fish:</td>
                                    <td>				
				<select name="fish" onchange="this.form.submit();">
                                <option value="">--Select All--</option>
                                <?
				foreach($fishMasterRecords as $fr) {
					$Id		=	$fr[0];
					$fishName	=	stripSlash($fr[1]);
					$selected	=	"";
					if( $fishId==$Id) $selected	="selected";
				?>
                                <option value="<?=$Id?>" <?=$selected?>><?=$fishName?></option>
                                <? }?>
                                      </select></td>
                                  </tr>
                                  <tr>
                                    <td class="fieldName">Process Code: </td>
                                    <td>
				  <? $processId	=	$p["processCode"];?>
				 <select name="processCode" id="processCode" onchange="this.form.submit();">
                                 <option value="">-- Select All--</option>
                                  <?
				foreach ($processCodeRecords as $fl) {
					$processCodeId		=	$fl[0];
					$processCode		=	$fl[1];
					$selected	=	"";
					if ($processId==$processCodeId) $selected = "selected";
				?>
				<option value="<?=$processCodeId;?>" <?=$selected;?> ><?=$processCode;?>             </option>
                                <?
				}
				?>
                                  </select></td>
                                  </tr>
                                </table></td>
				  <td valign="top">&nbsp;</td>
                            </tr>
				</TD></tr></table>
				<!-- End Here-->
                          </table></td>
                      </tr>
			<? }?>
			<!-- Detailed Search End here -->
                      <tr> 
                        <td colspan="3" nowrap> </td>
                      </tr>
                      <tr> 
                        <td class="fieldName" nowrap > </td>
                        <td></td>
                        <td class="fieldName"></td>
                      </tr>
			<? if($dailyCatchReportRecords>0) {?>
			<tr><TD>
	<table width="99%" cellpadding="2" cellspacing="1" bgcolor="#999999" align="center">			
      <tr bgcolor="#f2f2f2" align="center">
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt" width="100">Wt Challan No</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt" width="100">SUPPLIER</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt" width="100">FISH</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">PROCESS</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">COUNT</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">GRADE</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">REMARKS</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">QUANTITY</th>	
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">RATE</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">AMOUNT</th>	
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Adjust. Qty</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Adjust. Rate</th>  
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Local Qty</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Local Rate</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Wastage Qty</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Wastage Rate</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Soft Qty</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Soft Rate</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Total Rate</th>	
      </tr>
      <?php
	  	$grandTotalEffectiveWt	=	"";			
		$grandTotalActualAmount = "";	
		$prevFishId	= "";	
		$prevPurchaseSettledDate = "";
		$grandTotalLocalQtyRate = "";
		$grandTotalWastageQtyRate	= "";
		$grandTotalAdjstWtRate = "";
		$totalLocalRate = "";
		$grandTotalLocalRate = "";
		$totalAdjustWt  = "";
		$totalLocalQty = "";
		$totalWastageQty = "";
		$totalSoftQty = "";
		$prevMainSuppId = "";
		$i=0;
		while ($dcr=$dailyCatchReportResultSetObj->getRow()) {
			$i++;	
			$catchEntryId		= $dcr[0];
			$enteredDate		= dateFormat($dcr[3]);
			$mainSupplierId		= $dcr[8];
			$mainSupplierName	= "";
			if ($prevMainSuppId!=$mainSupplierId) $mainSupplierName = $dcr[56];
			/*
			$challanNo		=	$dcr[6];
			$WtChallanNumber = $dcr[6];
			*/
			$challanNo		=	$dcr[52];
			$WtChallanNumber 	=	"";
			if ($prevChallanNo != $challanNo) {
				$WtChallanNumber = $dcr[52];
			}
			$selFishId		=	$dcr[11];
			$fishName		=	"";
			if ($prevFishId	!= $selFishId) {
				$fishName	=	$dcr[53];
			}	
			
			$processCode		=	$dcr[54];
			$selectRate		=	$dcr[33];
										
			$actualRate		=	$dcr[34];
		
			$paymentBy	=	$dcr[44];
			$receivedBy	=	$dcr[48];
			
			$count		=	$dcr[13];
			$countAverage	=	$dcr[14];
			$gradeCode = "";
			if ($count == "" || $receivedBy=='B') {				
				$gradeCode = stripSlash($dcr[55]);
			}
	
		
			$localQty	= $dcr[16];
			$totalLocalQty += $localQty;

			$wastageQty	= $dcr[17];
			$totalWastageQty += $wastageQty;

			$softQty	= $dcr[18];
			$totalSoftQty   += $softQty;

			#Find the Wastage Rate Percentage
			list($localRatePercent, $wastageRatePercent, $softRatePercent) = $wastageratepercentageObj->getWastageRatePercentage();
			
			$localQtyRate 	= (($selectRate*$localRatePercent/100));
			$wastageQtyRate = (($selectRate*$wastageRatePercent/100));
			$softQtyRate	= (($selectRate*$softRatePercent/100));	

			$totalLocalQtyRate 	= $localQty * $localQtyRate;
			$totalWastageQtyRate 	= $wastageQty * $wastageQtyRate;
			$totalSoftQtyRate	= $softQty * $softQtyRate;

			$grandTotalLocalQtyRate += $totalLocalQtyRate;		
			$grandTotalWastageQtyRate += $totalWastageQtyRate;
			$grandTotalSoftQtyRate	+= $totalSoftQtyRate;

			$gradeCountAdj	=	$dcr[46]; // Don't add $gradeCountAdj in Lcal Qty report (said on 18-01-07)
	
			$adjustWt	=	$dcr[20];
			$totalAdjustWt += $adjustWt;

			$adjustWtRate  	=	$adjustWt * $selectRate;
			$grandTotalAdjstWtRate += $adjustWtRate;

			//Find the Total Wastage Rate
			$totalLocalRate = $adjustWtRate + $totalLocalQtyRate + $totalWastageQtyRate + $totalSoftQtyRate;

			$grandTotalLocalRate += $totalLocalRate;
			
			$actualWt = $effectiveWt	=	$dcr[28];	
			$remarks	=	$dcr[23];
			$selectWeight	=	$dcr[32];
			$effectiveWt = ($selectWeight!="" && $selectWeight!=0.00 && $selectWeight>0)?$selectWeight:$effectiveWt;
		
			$grandTotalEffectiveWt	+=	$effectiveWt;
			$grandTotalActualAmount += 	$actualRate;	
			
			$showWtDiff = "";
			$rowColor = "#FFFFFF";
			if ($actualWt!=$selectWeight) {		
				$rowColor = "#CCCCFF";
				$showWtDiff = "onMouseover=\"ShowTip('Mismatch in Effective Wt and Final Wt.');\" onMouseout=\"UnTip();\" ";
			}
		?>
      <tr bgcolor="<?=$rowColor?>" <?=$showWtDiff?>>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$WtChallanNumber?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$mainSupplierName?></td>	
        <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$fishName?></td>
        <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$processCode?></td>
        <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" nowrap="nowrap"><?=$count?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$gradeCode?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$remarks?></td>	
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=number_format($effectiveWt,2);?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$selectRate?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$actualRate?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$adjustWt?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=number_format($adjustWtRate,2,'.','');?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$localQty?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$totalLocalQtyRate?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$wastageQty?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$totalWastageQtyRate?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$softQty?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$totalSoftQtyRate?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><?=number_format($totalLocalRate,2,'.','');?></strong></td>		
      </tr>
	  <?php
	  	$prevChallanNo = $challanNo;
	  	$prevFishId	= $selFishId;		  
		$prevMainSuppId = $mainSupplierId;
	  } // Loop Ends here	
	  ?>
 <tr bgcolor="#FFFFFF">
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-head" align="right" nowrap style="padding-left:2px; padding-right:2px; font-size:7pt;">Total:</td>	
	<td height='20' class="listing-item">&nbsp;</td>
	 <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><?=number_format($grandTotalEffectiveWt,2);?></strong></td>
        <td height='20' class="listing-item">&nbsp;</td>
        <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><? //echo number_format($grandTotalActualAmount,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><?=number_format($totalAdjustWt,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><?=number_format($grandTotalAdjstWtRate,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><?=number_format($totalLocalQty,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><?=number_format($grandTotalLocalQtyRate,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><?=number_format($totalWastageQty,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><?=number_format($grandTotalWastageQtyRate,2);?></strong></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><?=number_format($totalSoftQty,2);?></strong></td>
	<td height='20' nowrap="nowrap" class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><strong><?=number_format($grandTotalSoftQtyRate,2);?></strong></td> 
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:8pt;" align="right"><strong><?=number_format($grandTotalLocalRate,2);?></strong></td>	
      </tr>
	    </table></TD></tr>
		<? } else if ($searchEnabled) { // size check ends here ?>
			<tr><TD class="err1" align="center"><?=$msgNoRecords?></TD></tr>
	<?php
		}
	?>
		<!-- Report end Here-->
                      <tr> 
                        <td class="fieldName" nowrap ></td>
                        <td></td>
                        <td class="fieldName"></td>
                      </tr> 
                        <tr> 
                        <td colspan="3" align="center" height="10"></td>
                        </tr>
                      <tr>                        
                        <td colspan="4" align="center"><? if($print==true){?>
                            <input type="button" name="Submit" value=" View / Print" class="button" onClick="return printWindow('PrintLocalQuantityReport.php?supplyFrom=<?=$dateFrom?>&supplyTill=<?=$dateTill?>&selUnit=<?=$selectUnit?>&landingCenter=<?=$landingCenterId?>&supplier=<?=$selectSupplier?>&fish=<?=$fishId?>&processCode=<?=$processId?>&weighNumber=<?=$weighNumber?>&billingCompany=<?=$billingCompany?>',700,600);" <? if(sizeof($dailyCatchReportRecords)==0) echo $disabled="disabled";?>>
                          <? }?></td>
                      </tr>
			<? if(!sizeof($dailyCatchReportRecords)) {?>
		<tr bgcolor="White"><TD height="160">&nbsp;</TD></tr>
		<? }?>
                      <tr> 
                        <td colspan="2"  height="10" ></td>
                      </tr>
                    </table>
		 </td>
		</tr>
		</table>
		</td>
		</tr>
	</table>
	</td></tr>
	</table>	
	<SCRIPT LANGUAGE="JavaScript">
	<!--
	Calendar.setup 
	(	
		{
			inputField  : "supplyFrom",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "supplyFrom", 
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
			inputField  : "supplyTill",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "supplyTill", 
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
			inputField  : "selDate",         // ID of the input field
			eventName	  : "click",	    // name of event
			button : "selDate", 
			ifFormat    : "%d/%m/%Y",    // the date format
			singleClick : true,
			step : 1
		}
	);
	//-->
	</SCRIPT>
	</form>
<?php
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>