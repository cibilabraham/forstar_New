<?
	require("include/include.php");
	$err			=	"";
	$errDel			=	"";
	$editMode		=	false;
	$addMode		=	true;
	$searchMode		=	false;
	$confirmed		=	"";
	$statusUpdated		=	false;

	#-------------------Admin Checking--------------------------------------
	$isAdmin 			= false;
	$role		=	$manageroleObj->findRoleName($roleId);
	if (strtolower($role)=="admin" || strtolower($role)=="administrator") {
		$isAdmin = true;
	}
	#-----------------------------------------------------------------

	//------------  Checking Access Control Level  ----------------
	$add	=false;
	$edit	=false;
	$del	=false;
	$print	=false;
	$confirm=false;
	$reEdit = false;	

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
	if($accesscontrolObj->canReEdit()) $reEdit=true;		
	//----------------------------------------------------------
	//Show wastage check box
	$showWastage	=	$p["showWastage"];
	if ($showWastage=='SW') {
		$showWastageChk = "Checked";
	}

	#Change Status Declaration
	$changeStatus = $p["changeStatus"];
	if ($p["billingCompany"]!="") $billingCompany = $p["billingCompany"];

	/*if ($changeStatus=='CRMQ') {
		$changeRMQuantity = "Checked";
	} else if ($changeStatus=='CR') {
		$changeRate = "Checked";
	} else if ($changeStatus=='CPD') {
		$changePaymentDetails = "Checked";
	} else {
		$changeStatus = "";
	}*/


	#Change the Status
	if ($p["cmdUpdate"]) {

		$weighNumber	=	trim($p["weighNumber"]);
		$supplierPaymentWt	= $p["supplierPaymentWt"]; //Declared or effective wt
		$changeStatus = $p["changeStatus"];
		$billingCompany = $p["billingCompany"];

		if ($weighNumber && $changeStatus)
			$updateRecStatus = $purchasereportObj->updateRMStatus($weighNumber, $supplierPaymentWt, $changeStatus, $billingCompany);
		if ($updateRecStatus) $statusUpdated = true;
	}


	if ($p["cmdSearch"]!="" ||  $p["cmdConfirm"]!="" || $statusUpdated!="") {
		//echo "dfd";
		$weighNumber	=	trim($p["weighNumber"]);
		$billingCompany = $p["billingCompany"];
		#Find Supplier Effective Wt Records
		$dailyCatchReportRecords = $purchasereportObj->fetchAllCatchReportRecords($weighNumber, $billingCompany);

		$supplierPaymentWt = $dailyCatchReportRecords[0][44]; //Declared/ effective Wt

		#Find supplier Declared Wt Records(Suplier Memo)
		if ($dailyCatchReportRecords[0][44]=='D') {
			$declaredWtRecords  = $purchasereportObj->getSupplierDeclaredWtRecords($weighNumber, $billingCompany);
		}
		$searchMode	=	true;
		
		$supplyCostRec	=	$purchasereportObj->findSupplyCostRec($weighNumber, $billingCompany);		
		$challanMainId 	= $dailycatchreportObj->getChallanMainId($weighNumber, $billingCompany);
		$rmSettled 	= $purchasereportObj->checkAllRMSettled($weighNumber, $billingCompany);
	}


	

#confirm the dailycatch
if ($p["cmdConfirm"]!="") {

	$weighNumber	=	trim($p["weighNumber"]);
	$billingCompany = $p["billingCompany"];
	
	if ($weighNumber!="") {
		$confirmDailyCatchEntry	= $purchasereportObj->updateDailyCatchMainConfirmRecords($weighNumber, $billingCompany);
	}
}

	# Get Billing Comapany  Records
		//$billingCompanyRecords = $billingCompanyObj->fetchAllRecords();
		$billingCompanyRecords = $billingCompanyObj->fetchAllRecordsActivebillingCompany();

# Display heading
	if ($editMode) {
		$heading	=	$label_editDailyCatchReports;
	} else {
		$heading	=	$label_addDailyCatchReports;
	}

	$ON_LOAD_PRINT_JS	= "libjs/purchasereport.js";

	# Include Template [topLeftNav.php]
	require("template/topLeftNav.php");
?>
	<form name="frmPurchaseReport" action="PurchaseReport.php" method="Post">
	<table cellspacing="0"  align="center" cellpadding="0" width="100%">
		<tr>
			<td height="40" align="center" class="err1" ><? if($err!="" ){?><?=$err;?><?}?></td>
		</tr>
		<?
			if( $editMode || $addMode )
			{
		?>
		<tr>
			<td>
				<table cellpadding="0"  cellspacing="1" border="0" align="center"  width="95%"  bgcolor="#D3D3D3">
					<tr>
						<td   bgcolor="white">
							<!-- Form fields start -->
							<table cellpadding="0"  width="100%" cellspacing="0" border="0" align="center">
								<tr>
									<td width="1" background="images/heading_bg.gif" class="page_hint"></td>
									<td width="291" background="images/heading_bg.gif" class="pageName" >&nbsp;Purchase Report</td>
								    <td width="290" background="images/heading_bg.gif" class="pageName" align="right" ><table width="100%" border="0" cellpadding="0" cellspacing="0">
                                      <tr>
                                        <td background="images/heading_bg.gif">&nbsp;</td>
                                        <td background="images/heading_bg.gif" class="listing-item" align="right"><? echo $today=date("j F Y");?>&nbsp;&nbsp;</td>
                                      </tr>
                                    </table></td>
								</tr>
								<tr>
									<td width="1" ></td>
									<td colspan="2"  align="center">
										<table cellpadding="0"  width="90%" cellspacing="0" border="0" align="center">
											<tr>
												
                        <td height="5" ></td>
											</tr>
											<tr>
												<? if($editMode){?>
												<?} else{?>
											  <td colspan="2" align="center" nowrap="nowrap">&nbsp;</td>
												

												<?} ?>
											</tr>
											<input type="hidden" name="hidDailyRateId" value="<?=$dailyRateId;?>">
											<tr>
											  <td nowrap height="5"></td>
										  </tr>
											<tr>
											  <td class="fieldName" nowrap valign="top" >
											  <table width="100%">
											  <tr>
											  <td>
											  <table><TR><TD>
											  <fieldset><legend class="listing-item">Weighment Challan Wise Search</legend>
	<table cellpadding="2" cellspacing="0">
				<tr>
				<TD class="fieldName">*Billing Company</TD>
				<td>
					
					<select name="billingCompany" id="billingCompany">
					<!--<option value="">-- Select --</option>-->
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
							  <td><input type="submit" name="cmdSearch" value=" Search" class="button" onclick="return validatePurchaseReportSearch(document.frmPurchaseReport);" /></td>
                            </tr>
				<? if ($isAdmin==true || $reEdit==true) { ?>
                                    <TR>
                                      <TD colspan="3">
					<table>
					<TR>
					<TD><INPUT type="checkbox" name="showWastage" value="SW" <?=$showWastageChk?> class="chkBox"></TD>
					<TD class="listing-item">Show Wastage</TD>
					</TR></table>
					</TD> 
                                    </tr>
				<? }?>
                                  </table></fieldset>
				</TD></TR>
				</table>
				</td>
<!-- 	Change Status Start Here	 -->
				<? if ($isAdmin==true || $reEdit==true) { ?>
				<td valign="top" nowrap>
				<table>
					<TR>
						<TD>
						<fieldset>
						<legend class="listing-item">Change Payment Status</legend>
							<table>
								<TR>
									<TD>
									<table>
									<TR>
									<TD class="fieldName">Change:</TD>
									<TD>
										<table>
										<TR>
										<TD><INPUT type="radio" name="changeStatus" value="CRMQ" <?=$changeRMQuantity?> class="chkBox"></TD>
										<TD class="listing-item">RM Quantity</TD>
										</TR>
										</table>
									</TD>
									<TD>
										<table>
										<TR>
										<TD><INPUT type="radio" name="changeStatus" value="CR" <?=$changeRate?> class="chkBox"></TD>
										<TD class="listing-item">Rate</TD>
										</TR>
										</table>
									</TD>
									<TD>
										<table>
										<TR>
										<TD><INPUT type="radio" name="changeStatus" value="CPD" <?=$changePaymentDetails?> class="chkBox"></TD>
										<TD class="listing-item">Payment Details</TD>
										</TR>
										</table>
									</TD>
									<td>
										<input type="submit" name="cmdUpdate" value=" Update " class="button" onclick="return validatePurchaseReportUpdate(document.frmPurchaseReport);" <? if((sizeof($dailyCatchReportRecords)==0) && $weighNumber!="") echo "disabled";?>>
									</td>
									</TR>
									</table>

									</TD>
								</TR>
							</table>
						</fieldset>
						</TD>
					</TR>
				</table>
				</td>
				<? }?>
<!--  Change Status End here   -->
				
				</tr>
				</table>
				</td>
						                      <td valign="top">&nbsp;</td>
											</tr>
											
											<tr>
											  <td nowrap class="fieldName" > </td>
										  </tr>
											<tr>
											  <td class="fieldName" nowrap > </td>
										  </tr>
											<tr>
											  <td class="fieldName" nowrap ></td>
										  </tr>											
										    <tr>
										      <td height="5">&nbsp;</td>
									      </tr>
										  <? if($weighNumber!="" && (sizeof($dailyCatchReportRecords)>0 || sizeof($declaredWtRecords)>0)){?>
									      <tr>
										  <td height="5">
										  <table width="100%" cellpadding="0" cellspacing="0">
	<?
	#Finding Supplier Record
	$supplierRec	=	$supplierMasterObj->find($dailyCatchReportRecords[0][8]);
	$supplierName	=	$supplierRec[2];
	
	#Finding Landing Center Record
	$centerRec			=	$landingcenterObj->find($dailyCatchReportRecords[0][7]);
	$landingCenterName		=	stripSlash($centerRec[1]);
	
	#Finding Plant Record
	$plantRec			=	$plantandunitObj->find($dailyCatchReportRecords[0][1]);
	$plantName			=	stripSlash($plantRec[2]);
		
	$Date1			=	explode("-",$dailyCatchReportRecords[0][3]); //2007-06-27
	$date		= 	date("j M Y", mktime(0, 0, 0, $Date1[1], $Date1[2], $Date1[0]));
	
	$selectTime		=	explode("-",$dailyCatchReportRecords[0][43]);
	$time			=	$selectTime[0].":".$selectTime[1]."&nbsp;".$selectTime[2];
	
	$vechNo				=		$dailyCatchReportRecords[0][4];	
	//$selectedWeighmentNo	=		$dailyCatchReportRecords[0][6];
	$selectedWeighmentNo	=		$dailyCatchReportRecords[0][52];

	# Get Account Entry No
	$challanAccountEntryNo	= $purchasereportObj->getAccountEntryNo($dailyCatchReportRecords[0][0]);	
	?>
		    <tr><td colspan="3">
		<fieldset>
		  <table width="100%" cellpadding="0" cellspacing="0">
        	<tr>
          <td valign="top">		  
		  <table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" nowrap>Supplier:</td>
                <td class="listing-item" style="padding-left:3px; padding-right:3px;"><?=$supplierName?></td>
              </tr>
            </table></td>
          <td valign="top">
		  <table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">Landing Center:</td>
                <td class="listing-item"><?=$landingCenterName?></td>
              </tr>
            </table></td>
          <td valign="top">
		  <table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" nowrap style="padding-right:5px;">Wt Challan No:</td>
                <td class="listing-item" nowrap><?=$selectedWeighmentNo?></td>
              </tr>
            </table></td>
          </tr>
        <tr> 
          <td class="fieldName">
	     <table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" nowrap>Supplied At:</td>
                <td class="listing-item" nowrap="nowrap"><?=$plantName?></td>
              </tr>
            </table>
	  </td>
          <td class="listing-item">
		<table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" style="padding-left:5px; padding-right:5px;">Date/Time:</td>
                <td class="listing-item" nowrap><?=$date?>-<?=$time?>&nbsp;&nbsp;</td>
              </tr>
            </table>
	 </td>
          <td class="fieldName">
	    <table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" nowrap>Vehicle No:</td>
                <td class="listing-item" nowrap>&nbsp;<?=$vechNo?></td>
              </tr>
            </table></td>
          </tr>
	<tr> 
          <td class="fieldName">
	     <table cellpadding="0" cellspacing="0">
              <tr> 
                <td class="fieldName" nowrap>ACCOUNTS REF NO:</td>
                <td class="listing-item" nowrap="nowrap">&nbsp;<strong><?=$challanAccountEntryNo?></strong></td>
              </tr>
            </table>
	  </td>
          <td class="listing-item">&nbsp;		
	 </td>
          <td class="fieldName">&nbsp;
	    </td>
          </tr>
      </table></fieldset></td></tr></table></td></tr>	
	  <? }?>
	  <tr>
	  <td>
	  <? if($weighNumber!="" && (sizeof($dailyCatchReportRecords)>0 || sizeof($declaredWtRecords)>0)){?>
	  <table width="200" border="0" align="center">
                                           <tr>
                                              <td class="fieldName" nowrap style="line-height:normal; font-size:7pt;"><img src="images/y.png">- These are Settled</td>
                                              <td class="fieldName" nowrap style="line-height:normal; font-size:7pt;"><img src="images/x.png"> - These are Not Settled</td>
                                            </tr>
                                          </table>
					  <? }?>								  </td>
					  </tr>
					 <tr>
					 <?
					if( sizeof($dailyCatchReportRecords)){
						$i	=	0;
						$paymentBy	=	$dailyCatchReportRecords[0][44];
					?>
					<td  height="10" class="listing-item">
					<? if($paymentBy=='E') { ?>
					<table width="99%" cellpadding="2" cellspacing="1" bgcolor="#999999">
					<tr bgcolor="#FFFFFF">
        				<th colspan="21" class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">		
		<table width="200" border="0">
		<?
		//echo $dailyCatchReportRecords[0][45];
		#Checking All RM Settled
		if (!$purchasereportObj->checkAllRMSettled($weighNumber, $billingCompany)) {

			$paymentStatus = $dailyCatchReportRecords[0][49];
			//$paymentDate   = $dailyCatchReportRecords[0][50];	
			$pDate		=	explode("-",$dailyCatchReportRecords[0][50]);
			$paymentDate	=	$pDate[2]."/".$pDate[1]."/".$pDate[0];
			if ($paymentStatus=='Y') {
				$displayPaymentStatus = "<span style=\"color:#003300\"><strong>Paid </strong></span> on $paymentDate"; 
			} else {
				$displayPaymentStatus = "<span style=\"color:#FF0000\"><strong>not Paid</span></strong>"; 
			}
		}
		else if ($dailyCatchReportRecords[0][45]==1) //Qty Confirmed if yes=1, no=0;
		{
		  	$displayPaymentStatus = "<span style=\"color:#FF0000\"><strong>Weight Confirmed but Not Settled</span></strong>";
		} else {
			$displayPaymentStatus = "<span style=\"color:#FF0000\"><strong>Weight Not Confirmed</span></strong>";
		}
		?>
          <tr>
            <td nowrap class="fieldName">Weighment Challan No: <?=$dailyCatchReportRecords[0][52]//$weighNumber?> is <?=$displayPaymentStatus?></td>
            </tr>
        </table></th>
        </tr>
      <tr bgcolor="#f2f2f2" align="center">
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt" width="100">FISH</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">PROCESS</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">COUNT</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">GRADE</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">REMARKS</th> 
	<?php if ($showWastage) { ?>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Adjust. Qty</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Local Qty</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Local Rate</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Wastage Qty</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Wastage Rate</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Soft Qty</th> 
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Soft Rate</th> 
	<? }?>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">QUANTITY</th>	
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">RATE</th>
        <th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">AMOUNT</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Setld</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Setl Date</th>
	<th class="listing-head" style="padding-left:2px; padding-right:2px; font-size:7pt">Description</th>
      </tr>
	  
      <?
	  	$grandTotalEffectiveWt	=	"";
			
		$grandTotalActualAmount = "";	
		 $prevFishId	= "";	
		  $prevPurchaseSettledDate = "";
		$grandTotalLocalQtyRate = "";
		$grandTotalWastageQtyRate	= "";
		foreach ($dailyCatchReportRecords as $dcr) {
			$i++;
	
			$catchEntryId	=	$dcr[0];
			$array			=	explode("-",$dcr[3]);
			$enteredDate		=	$array[2]."/".$array[1]."/".$array[0];
			
			$fishId			=	$dcr[11];
			$fishName		=	"";
			if( $prevFishId	!= $fishId)
			{
				$fishRec		=	$fishmasterObj->find($fishId);
				$fishName		=	$fishRec[1];
			}
		
			$processCodeRec		=	$processcodeObj->find($dcr[12]);
			$processCode		=	$processCodeRec[2];
	
			$netWt			=	$dcr[27];
	
			$declWt			=	$dcr[29];
			$declCount		=	$dcr[30];
	
			$dailyRateRec	=	$supplieraccountObj->findDailyRate($fishId);
			$declRate		=	$dailyRateRec[7];
	
			$paidStatus			=	$dcr[35];
			
			$checked		=	"";
			$settledDate	=	"";
			if($paidStatus=='Y')
			{
				$checked	=	"Checked";
			}
			#Settled Date
			$purchaseSettledDate = $dcr[36];
			if($purchaseSettledDate!=0 &&  $prevPurchaseSettledDate!=$purchaseSettledDate){
				$sDate		=	explode("-",$purchaseSettledDate);
				$settledDate	=	$sDate[2]."/".$sDate[1]."/".$sDate[0];
			}
			
			$selectWeight		=	$dcr[32];
			$selectRate		=	$dcr[33];
			
			$displayDescription = "";
			if($selectRate=="" || $selectRate==0)
				{
					$displayDescription	= "Rate not defined";
				}
			
			$actualRate			=	$dcr[34];
	
			$dailyCatchEntryId	=	$dcr[42];
	
			$paymentBy	=	$dcr[44];
			$receivedBy	=	$dcr[48];
	
			if($paymentBy=='E')
			{	
					$count			=	$dcr[13];
					$countAverage	=	$dcr[14];
					$gradeCode = "";
					if($count == "" || $receivedBy=='B'){
						$gradeRec		=	$grademasterObj->find($dcr[37]);
						$gradeCode		=	stripSlash($gradeRec[1]);
					}
# -- count all Gross Records -------------------------------------------------------
		$countGrossRecords	=	$dailycatchentryObj->fetchAllGrossRecords($dailyCatchEntryId);

			$totalWt	=	"";
			$grandTotalBasketWt = "";
			$netGrossWt	=	"";
			foreach ($countGrossRecords as $cgr) {
				$countGrossWt		=	$cgr[1];
				$totalWt		=	$totalWt+$countGrossWt;
				$countGrossBasketWt	=	$cgr[2];
				$grandTotalBasketWt	=	$grandTotalBasketWt + $countGrossBasketWt;
				$netGrossWt		=	$totalWt - $grandTotalBasketWt;
			}
		
			$localQty	=	$dcr[16];
			$wastageQty	=	$dcr[17];
			$softQty	=	$dcr[18];

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

			$gradeCountAdj	=	$dcr[46];		
			$totalAdjustWt	=	$dcr[20] + $gradeCountAdj;	
			$adjustWt	=	$dcr[20] + $localQty + $wastageQty + $softQty + $gradeCountAdj;
		
			if ($dcr[41]=='N') {
				$netGrossWt	=	$dcr[26];
			}
			
			$actualWt = $effectiveWt	=	$netGrossWt - $adjustWt;			
		
		} else { 
		
			$count		=	$declCount;
			$actualWt = $effectiveWt	=	$declWt	;
		}
		$remarks		=	$dcr[23];

		$effectiveWt = ($selectWeight!="" && $selectWeight!=0.00 && $selectWeight>0)?$selectWeight:$effectiveWt;

		
		$grandTotalEffectiveWt	+=	$effectiveWt;
		$grandTotalActualAmount += $actualRate;

		$showWtDiff = "";
		$rowColor = "#FFFFFF";
		if ($actualWt!=$selectWeight) {		
			$rowColor = "#CCCCFF";
			$showWtDiff = "onMouseover=\"ShowTip('Mismatch in Effective Wt and Final Wt.');\" onMouseout=\"UnTip();\" ";
		}

	?>
      <tr bgcolor="<?=$rowColor?>" <?=$showWtDiff?>>
        <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$fishName?></td>
        <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$processCode?></td>
        <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" nowrap="nowrap"><?=$count?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$gradeCode?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$remarks?></td>
	<?php if ($showWastage) { ?>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$totalAdjustWt?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$localQty?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$totalLocalQtyRate?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$wastageQty?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$totalWastageQtyRate?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$softQty?></td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$totalSoftQtyRate?></td>
	<? }?>
        <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=number_format($effectiveWt,2);?></td>
		<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$selectRate?></td>
		<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$actualRate?></td>
		<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><? if($checked){?><img src="images/y.png" /><? } else {?><img src="images/x.png" /><? }?></td>
		<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;" align="right"><?=$settledDate?></td>
		<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px; font-size:7pt;"><?=$displayDescription?></td>
		
      </tr>
	  <?
	  $prevFishId	= $fishId;
	  $prevPurchaseSettledDate = $purchaseSettledDate;
	  }
	  
	  ?>
	 <tr bgcolor="#FFFFFF">
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-head" align="right" nowrap>Total:</td>
	<?php if ($showWastage) { ?>
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><strong><? echo number_format($grandTotalLocalQtyRate,2);?></strong></td>
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><strong><? echo number_format($grandTotalWastageQtyRate,2);?></strong></td>
	<td height='20' class="listing-item">&nbsp;</td>
	<td height='20' nowrap="nowrap" class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><strong><? echo number_format($grandTotalSoftQtyRate,2);?></strong></td>
	<? } else {?>
	<td height='20' class="listing-item">&nbsp;</td>
	<? }?>
        
        <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><strong><? echo number_format($grandTotalEffectiveWt,2);?></strong></td>
        <td height='20' class="listing-item">&nbsp;</td>
        <td height='20' class="listing-item" style="padding-left:2px; padding-right:2px;" align="right"><strong><? echo number_format($grandTotalActualAmount,2);?></strong></td>
		<td height='20' class="listing-item">&nbsp;</td>
		<td height='20' class="listing-item">&nbsp;</td>
		<td height='20' class="listing-item">&nbsp;</td>
      </tr>
	    </table>
		<!-- Declared Section -->
		<?
		} else { 
		
			if(sizeof($declaredWtRecords)>0)
			{
		?>
			<table width="80%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999">
							  	

                                  <tr bgcolor="#FFFFFF">
                                    <th colspan="9" class="listing-head" style="padding-left:5px; padding-right:5px;"><table width="200" border="0">
		<? 
		#Checking All RM Settled
		$displayPaymentStatus = "";
		if(!$purchasereportObj->checkAllRMSettled($weighNumber, $billingCompany))
		{
			$paymentStatus = $declaredWtRecords[0][17];
			//$paymentDate   = $dailyCatchReportRecords[0][50];	
			$pDate		=	explode("-",$declaredWtRecords[0][18]);
			$paymentDate	=	$pDate[2]."/".$pDate[1]."/".$pDate[0];
			if($paymentStatus=='Y')
			{
				$displayPaymentStatus = "<span style=\"color:#003300\"><strong>Paid </strong></span> on $paymentDate"; 
			}
			else
			{
				$displayPaymentStatus = "<span style=\"color:#FF0000\"><strong>not Paid</span></strong>"; 
			}
		}
		else
		{
				$displayPaymentStatus = "<span style=\"color:#FF0000\"><strong>not Settled</span></strong>"; 
		}
		?>
          <tr>
            <td nowrap class="fieldName">Weighment Challan No: <?=$declaredWtRecords[0][20]//$weighNumber?> is <?=$displayPaymentStatus?></td>
            </tr>
        </table></th>
                                  </tr>
                                  <tr bgcolor="#f2f2f2" align="center"> 
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:7pt" width="80">Fish</th>
                                    <th class="listing-head"  style="padding-left:5px; padding-right:5px; font-size:7pt">Process Code</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:7pt">Grade/Count</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:7pt">Decl.Qty</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:7pt">Rate</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:7pt">Amount</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:7pt" width="100">Setld</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:7pt" width="100">Setl Date</th>
                                    <th class="listing-head" style="padding-left:5px; padding-right:5px; font-size:7pt" width="100">Description</th>
                                  </tr>
                                  <?
								  $j=0;
								  $gradeCode="";
								  $totalWt	=	"";
								  $prevFishId = 0;
								  $prevProcessCodeId = 0;
								  $grandTotalDeclAmount = "";
								  $prevPurchaseSettledDate="";
								  
								foreach($declaredWtRecords as $sdr){
								$j++;
								
								$catchEntryId	=	$sdr[0];
								
								$fishId			=	$sdr[1];
								$fishName = "";
								if($prevFishId!=$fishId){
									$fishName		=	$sdr[11];
								}
								
								$processCodeId	=	$sdr[2];	
								$processCode	= "";
								if($prevProcessCodeId!=$processCodeId){
									$processCode	=	$sdr[12];
								}
									
								$declCount		=	$sdr[10];
												
								$declWt	=	$sdr[13];
								$totalWt	+=	$declWt;
								
								$declRate	=	$sdr[14];
								$settledStatus = $sdr[15];
								
								$declAmount  = $declWt * $declRate;
								$grandTotalDeclAmount += $declAmount;
								
								if($settledStatus=='Y')
								{
									$checked	=	"Checked";
									//$sDate		=	explode("-",$sdr[16]);
									//$settledDate	=	$sDate[2]."/".$sDate[1]."/".$sDate[0];
								}
								
								#Settled Date
								$purchaseSettledDate = $sdr[16];
								$settledDate = "";
								if($purchaseSettledDate!=0 &&  $prevPurchaseSettledDate!=$purchaseSettledDate){
									$sDate		=	explode("-",$purchaseSettledDate);
									$settledDate	=	$sDate[2]."/".$sDate[1]."/".$sDate[0];
								}
								
								$displayDescription = "";
								if($declRate=="")
								{
									$displayDescription	= "Rate not defined";
								}
										
								?>
                               <tr bgcolor="#FFFFFF"> 
                                  <td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:7pt" height="30"><?=$fishName?></td>
                                    <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; font-size:7pt"><?=$processCode?></td>
                                    <td class="listing-item" nowrap style="padding-left:5px; padding-right:5px; font-size:7pt"><?=$declCount?></td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:7pt"><?=$declWt?></td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:7pt"><?=$declRate?></td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:7pt"><? echo number_format($declAmount,2,'.','');?></td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:7pt"><? if($checked){?><img src="images/y.png" /><? } else {?><img src="images/x.png" /><? }?></td>
                                    <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px; font-size:7pt"><?=$settledDate?></td>
                                    <td class="listing-item" style="padding-left:5px; padding-right:5px; font-size:7pt"><?=$displayDescription?></td>
                            </tr>
                                  <? 
								  $prevFishId = $fishId;
								  $prevProcessCodeId = $processCodeId;
								  $prevPurchaseSettledDate=$purchaseSettledDate;
								  } 
								  ?>
								   <tr bgcolor="#FFFFFF">
                                    <td colspan="3" nowrap class="listing-head" align="right" style="padding-left:5px; padding-right:5px;" height="30">TOTAL:</td>
                                    <td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalWt,2);?></strong></td>
                                    <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">&nbsp;</td>
                                    <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($grandTotalDeclAmount,2);?></strong></td>
                                    <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">&nbsp;</td>
                                    <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">&nbsp;</td>
                                    <td class="listing-item" align="right" nowrap="nowrap" style="padding-left:5px; padding-right:5px;">&nbsp;</td>
							      </tr>								  
            </table>
		<? 
			}
			#Declared Ends Here
		}
		
		?>
		<!-- Here -->		</td></tr>
		<tr><TD height="5"></TD></tr>
		<!-- RM Supply Cost Start Here -->
		<?
		if (sizeof($supplyCostRec)>0) {
			
		$selWtChallan		=	$supplyCostRec[1];		
		$numIceBlocks		=	$supplyCostRec[2];
		$costPerBlock		=	$supplyCostRec[3];
		$totalIceCost		=	$supplyCostRec[4];
		$fixedIceCost		=	$supplyCostRec[5];
		
		$landingCenterDistance	=	$supplyCostRec[6];
		$costPerKm		=	$supplyCostRec[7];
		$totalTransAmt		=	$supplyCostRec[8];
		$fixedTransCost		=	$supplyCostRec[9];

		
		$effectiveQty		=	$supplyCostRec[10];
		$commissionPerKg	=	$supplyCostRec[11];
		$totalCommiRate		=	$supplyCostRec[12];
		$fixedCommiRate		=	$supplyCostRec[13];

		$totalRMQuanty		=	$supplyCostRec[15];
		$handlingRatePerKg	=	$supplyCostRec[16];
		$totalHandlingAmt	=	$supplyCostRec[17];
		$fixedHandlingAmt	=	$supplyCostRec[18];
				
		$selOption 		= $supplyCostRec[19];
		//if ($selOption=='I') $selIndividual = "Checked";
		//if ($selOption=='G') $selGroup = "Checked";

		$selCommission 		= $supplyCostRec[20];	
		$grandTotalCommiAmt 	= $supplyCostRec[21];

		$selHandling 		= $supplyCostRec[22];	
		$grandTotalHadlngAmt 	= $supplyCostRec[23];
		
		#For Selecting Process - Summary
		$processSummaryRecords	= $rmsupplycostObj -> filterFishProcessSummaryRecords($selWtChallan);	

		$displyIceCost ="";
		if ($fixedIceCost!=0) $displyIceCost  = $fixedIceCost;
		else $displyIceCost  = $totalIceCost;
		
		$displyTransCost = "";
		if ($fixedTransCost!=0) $displyTransCost  = $fixedTransCost;
		else $displyTransCost  = $totalTransAmt;

		$displyCommiCost = "";
		if ($fixedCommiRate!=0) $displyCommiCost  = $fixedCommiRate;
		else if ($totalCommiRate!=0) $displyCommiCost  = $totalCommiRate;
		else if ($grandTotalCommiAmt!=0) $displyCommiCost = $grandTotalCommiAmt;

		$displayHandlingCost = "";
		if ($fixedHandlingAmt!=0) $displayHandlingCost = $fixedHandlingAmt;
		else if ($totalHandlingAmt!=0) $displayHandlingCost = $totalHandlingAmt;
		else if ($grandTotalHadlngAmt!=0) $displayHandlingCost = $grandTotalHadlngAmt;

		$totalSupplyCostAmt = $displyIceCost + $displyTransCost + $displyCommiCost + $displayHandlingCost;	
		//echo "$displyIceCost + $displyTransCost + $displyCommiCost + $displayHandlingCost";


		/*
		$numIceBlocks		=	$supplyCostRec[2];
		$costPerBlock		=	$supplyCostRec[3];
		$totalIceCost		=	$supplyCostRec[4];
		$fixedIceCost		=	$supplyCostRec[5];

		$landingCenterDistance	=	$supplyCostRec[6];
		$costPerKm		=	$supplyCostRec[7];
		$totalTransAmt		=	$supplyCostRec[8];
		$fixedTransCost		=	$supplyCostRec[9];

		$effectiveQty		=	$supplyCostRec[10];
		$commissionPerKg	=	$supplyCostRec[11];
		$totalCommiRate		=	$supplyCostRec[12];
		$fixedCommiRate		=	$supplyCostRec[13];
		
		if ($fixedIceCost!=0) $displyIceCost  = $fixedIceCost;
		else $displyIceCost  = $totalIceCost;
		
		if ($fixedTransCost!=0) $displyTransCost  = $fixedTransCost;
		else $displyTransCost  = $totalTransAmt;
		
		if ($fixedCommiRate!=0) $displyCommiCost  = $fixedCommiRate;
		else $displyCommiCost  = $totalCommiRate;		
		$totalSupplyCostAmt = $displyIceCost + $displyTransCost + $displyCommiCost;
		*/
		?>
		<tr>
		<td>
		<table>
				<TR>
				<TD valign="top">
				<fieldset class="fieldName"><legend>Ice</legend> 
				<table>
				<? if($fixedIceCost==0) {?>
				<tr><TD valign="top">
				<div id="iceBlock" style="display:block">
				<table>
					<tr>
					<td nowrap class="fieldName">No.of blocks:</td>
					<td class="listing-item"><?=$numIceBlocks?></td>
					</tr>
                                          <tr>
                                            <TD class="fieldName">Cost/ Block:
                                            </TD>
                                            <TD class="listing-item"><?=$costPerBlock?></TD>
                                          </tr>
                                          <tr>
                                            <TD class="fieldName">Total Cost:</TD>
                                            <TD class="listing-item"><?=$totalIceCost?></TD>
                                          </tr>
					</table>
					</div>
				  	</TD></tr>
					<? } else {?>
					<tr>
						<td valign="top">
						<div id="fixedIceBlock" style="display:block">
						<table>
							<tr>
							<td class="fieldName">Fixed Cost:</td>
							<TD class="listing-item"><?=$fixedIceCost?></TD></tr>
						</table>
						</div>
						</td>
					</tr>
				 <? }?>
                                        </table>
				</fieldset>
				</TD>
				<td valign="top" style="padding-left:10px; padding-right:10px;">
				<table>
				<TR>
				<TD valign="top" nowrap>
				<fieldset class="fieldName"><legend>Transportation</legend> 
				<table>
				<? if($fixedTransCost==0) {?>
				<tr><TD valign="top">
				<div id="transportationBlock" style="display:block">
				<table>
					<tr>
					<td nowrap class="fieldName">Km :</td>
					<td class="listing-item"><?=$landingCenterDistance?></td>
					</tr>
                                          <tr>
                                            <TD class="fieldName">Cost/ Km:
                                            </TD>
                                            <TD class="listing-item"><?=$costPerKm?></TD>
                                          </tr>
                                          <tr>
                                            <TD class="fieldName" nowrap>Total Amt:</TD>
                                            <TD class="listing-item"><?=$totalTransAmt?></TD>
                                          </tr>
					</table>
					</div>
				  	</TD></tr>
					<? } else {?>
					<tr>
						<td>
						<div id="fixedTransBlock" style="display:block">
						<table>
							<tr>
							<td class="fieldName">Fixed Cost:</td>
							<TD class="listing-item"><?=$fixedTransCost?></TD></tr>
						</table>
						</div>
						</td>
					</tr>
					<? }?>
                                        </table>
				</fieldset>
				</TD></TR>
				</table></td>
			<? if ($selCommission!="") {?>
				<td valign="top">
				<table>
				<TR>
				<TD>
				<fieldset class="fieldName"><legend>Commission</legend> 
				<table>
			<TR>
				<TD>
				<table>
			<?if ($selCommission=='S') {?>
				<TR>
				<TD>
				<table>
				<TR>
				<TD>				
				<table>
				<? if ($fixedCommiRate==0) {?>
				<tr>
				<TD>
				<div id="commissionBlock" style="display:block">
				<table>
					<tr>
					<td nowrap class="fieldName">Total Qty :</td>
					<td class="listing-item"><?=$effectiveQty?></td>
					</tr>
                                          <tr>
                                            <TD class="fieldName" nowrap>Commi/ Kg:
                                            </TD>
                                            <TD class="listing-item"><?=$commissionPerKg?></TD>
                                          </tr>
                                          <tr>
                                            <TD class="fieldName">Total Rate:</TD>
                                            <TD class="listing-item"><?=$totalCommiRate?>
                                            </TD>
                                          </tr>
					</table>
					</div>
				  	</TD>
					</tr>
				<? } else {?>
					<tr>
						<td>
						<div id="fixedCommiBlock" style="display:block">
						<table>
							<tr>
							<td class="fieldName">Rate:</td>
							<TD class="listing-item"><?=$fixedCommiRate?>
							</TD></tr>
						</table>
						</div>
						</td>
					</tr>
				<? }?>
                                        </table>				
				</TD></TR>
				</table></TD>
					</TR>
					<? }?>
				<? if ($selCommission=='D') {?>
				<tr><TD>
				<table width="90%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">
				<?
				if (sizeof($processSummaryRecords)) {
				?>
                                  <tr bgcolor="#f2f2f2" align="center"> 
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Process Code</td>			
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Qty</td>
				     <td class="listing-head" style="padding-left:5px; padding-right:5px;">Rate</td>	
				      <td class="listing-head" style="padding-left:5px; padding-right:5px;">Amount</td>	
                                  </tr>
        <?
	$totalWt	=	"";
	foreach($processSummaryRecords as $psr){	
		$processCodeId	=	$psr[2];
		$processCode	=	$psr[5];
		$totalQty	=	$psr[4];
		$totalWt	+=	$totalQty;
		
		$commiRate	=	$psr[6];
		$commiAmt	=	$psr[7];
		
		
	?>
       <tr bgcolor="#FFFFFF"> 
	<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$processCode?></td> 
	<td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;">
		<input type="hidden" name="totalQty_<?=$k?>" id="totalQty_<?=$k?>" value="<?=$totalQty?>"><?=$totalQty?>
	</td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$commiRate?></td>	
	<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$commiAmt?>
	</td>	
       </tr>
       <? } ?>	
  	<tr bgcolor="#FFFFFF">
          <td  nowrap class="listing-head" align="right" style="padding-left:5px; padding-right:5px;">TOTAL</td>
          <td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($totalWt,2);?> </strong></td>
	  <td></td>
	 <td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;" nowrap><strong><?=$grandTotalCommiAmt?></strong></td>
        </tr>
	<? }?>
        </table>
	</TD></tr>
	<? }?>
	</table>		
	</TD>
	</TR>
	</table>
				</fieldset>
				</TD></TR>
				</table></td>
		<? }?>
		<?if ($selHandling!="") {?>
				<td valign="top">
				<table>
				<TR>
				<TD>
				<fieldset class="fieldName"><legend>Handling</legend> 
				<table>
				<tr>
				<TD>
				<table>
				<TR>
				<TD>
				<table>
					<?if ($selHandling=='S') {?>
					<TR>
						<TD><table>
				<TR>
				<TD>
				<table>
				<? if ($fixedHandlingAmt==0) {?>
				<tr>
				<TD>
				<div id="handlingBlock" style="display:block">
				<table>
					<tr>
					<td nowrap class="fieldName">RM Qty :</td>
					<td class="listing-item"><?=$totalRMQuanty?></td>
					</tr>
                                          <tr>
                                            <TD class="fieldName" nowrap>Rate:
                                            </TD>
                                            <TD class="listing-item"><?=$handlingRatePerKg?>  
                                            </TD>
                                          </tr>
                                          <tr>
                                            <TD class="fieldName" nowrap>Total Amt:</TD>
                                            <TD class="listing-item"><?=$totalHandlingAmt?>
                                            </TD>
                                          </tr>
					</table>
					</div>
				  	</TD></tr>
				<? } else {?>
					<tr>
						<td>
						<div id="fixedHandlingBlock" style="display:block">
						<table>
							<tr>
							<td class="fieldName">Amt:</td>
							<TD class="listing-item"><?=$fixedHandlingAmt?>
							</TD></tr>
				
						</table>
						</div>
						</td>
					</tr>
				<? }?>
                                        </table>				
				</TD></TR>
				</table></TD>
					</TR>
					<? }?>
				<? if ($selHandling=='D') {?>
				<tr><TD>
				<table width="90%" border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">
				<?
				if( sizeof($processSummaryRecords)){
				?>
                                  <tr bgcolor="#f2f2f2" align="center"> 
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Process Code</td>			
                                    <td class="listing-head" style="padding-left:5px; padding-right:5px;">Qty</td>
				     <td class="listing-head" style="padding-left:5px; padding-right:5px;">Rate</td>	
				      <td class="listing-head" style="padding-left:5px; padding-right:5px;">Amount</td>	
                                  </tr>
        <?
	$m=0;
	$totalWt	=	"";
	foreach($processSummaryRecords as $psr){
		$m++;
		$hProcessCodeId	=	$psr[2];
		$hProcessCode	=	$psr[5];
		$hTotalQty	=	$psr[4];
		$hTotalWt	+=	$hTotalQty;
		if ($editMode) {
			$hRate		=	$psr[8];	
			$hTotalAmt	=	$psr[9];
		}

	if ($p["hRate_".$m]!="") $hRate = $p["hRate_".$m];

	?>
       <tr bgcolor="#FFFFFF"> 
	<td class="listing-item" style="padding-left:5px; padding-right:5px;" height="20">
		<?=$hProcessCode?>
	</td> 
       <td class="listing-item" align="right" style="padding-left:5px; padding-right:5px;">
		<?=$hTotalQty?>
	</td>
	<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$hRate?>
	</td>	
	<td class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$hTotalAmt?>
	</td>	
       </tr>
       <? } ?>
  	<tr bgcolor="#FFFFFF">
          <td  nowrap class="listing-head" align="right" style="padding-left:5px; padding-right:5px;">TOTAL</td>
          <td class="listing-item" align="right" nowrap style="padding-left:5px; padding-right:5px;"><strong><? echo number_format($hTotalWt,2);?> </strong></td>
	  <td></td>
	 <td align="right" class="listing-item" style="padding-left:5px; padding-right:5px;"><?=$grandTotalHadlngAmt?></td>
        </tr>
	<? }?>
        </table>
	</TD></tr>
	<? }?>
	</table>	
	</TD>
	</TR>
	</table>
	</TD>
	</tr>
            </table>
	</fieldset>
	</TD></TR>
	</table></td>
	<? }?>
	</TR>
</table></td></tr>
		<? }
		  else
		  {
		?>
		<tr><TD height="10"></TD></tr>
		<tr bgcolor="white"> 
                        <td colspan="7"  class="err1" height="10" align="center"> 
                          <?=$msgNoRMSupplyCost;?></td>
                      </tr>
		<? }?>
		<!-- RM Supply Cost End Here -->
		<tr><TD height="10"></TD></tr>
		<tr><TD>
			<table align="center">
				<TR>
					<TD class="listing-head">Total Supply Cost:</TD>
					<td class="listing-item" align="right"><? echo number_format($totalSupplyCostAmt,2,'.','');?></td>
				</TR>
                            <tr>
                              <TD class="listing-head">Total RM Cost:</TD>
				<? 
				$rmCost = "";
				if($grandTotalDeclAmount=="")
				{
					$rmCost = $grandTotalActualAmount;	
				}
				else
				{
					$rmCost = $grandTotalDeclAmount;
				}
				$grandTotalCost = $rmCost + $totalSupplyCostAmt;
				?>
                              <td class="listing-item" align="right"><? echo number_format($rmCost,2,'.','');?></td>
                            </tr>
                            <tr>
                              <td class="listing-head">
                                Grand Total Cost:
                              </td>
                              <td class="listing-item" align="right"><strong><? echo  number_format($grandTotalCost,2,'.','');?></strong></td>
                            </tr>
                          </table></TD></tr>
		<? } else if($weighNumber!=""){?>
		<tr bgcolor="white"> 
		<td colspan="17"  class="err1" height="10" align="center"><?=$noPurchaseRecords;?></td>
		</tr>
		<? }?>
		<tr>
		<td  height="10" ><input type="hidden" name="supplierPaymentWt" value="<?=$supplierPaymentWt?>"></td>
	</tr>	
	<tr>
		<td align="center">
		<?
	if($confirm==true && (!$purchasereportObj->checkAllRMSettled($weighNumber, $billingCompany)) && ($manageconfirmObj->isACConfirmEnabled())){
	?>
		<? $confirmed = $dailyCatchReportRecords[0][51];?>
		<input name="cmdConfirm" type="submit" class="button" id="cmdConfirm" value=" Confirm this Weighment Challan No" style="width:250px;" <? if($confirmed=='Y') echo "disabled";?>>		
	<? }?>	

		<?php
			 if($print==true){
		?>
			&nbsp;&nbsp;
			<input type="button" name="cmdSetlmentMemo" class="button" value=" Purchase Settlement Memo " onClick="return printWindow('PrintPurchaseSettlementMemo.php?challanMainId=<?=$challanMainId?>',700,600);" style="width:200px" <? if( (($rmSettled) && $weighNumber!="") || $weighNumber=="") echo "disabled";?>>
		<?php }?>
		</td>
	</tr>
	
	<tr><TD height="10"></TD></tr>									</table></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<!-- Form fields end   -->
			</td>
		</tr>	
		<tr>
			<td height="10" ></td>
		</tr>
		<?
			}
		?>

			
	</table>	

	</form>
<?
	# Include Template [bottomRightNav.php]
	require("template/bottomRightNav.php");
?>
